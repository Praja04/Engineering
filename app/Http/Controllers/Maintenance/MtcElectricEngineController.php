<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use App\Models\NotificationsModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Maintenance\MtcMainModel;
use App\Models\Maintenance\MtcApprovalModel;
use App\Models\Maintenance\MtcMasterMesinModel;
use App\Http\Requests\Maintenance\MtcMainRequest;
use App\Models\Maintenance\MtcElectricEngineModel;
use App\Models\Maintenance\MtcKebutuhanMaterialModel;
use App\Http\Requests\Maintenance\MtcElectricEngineRequest;
use App\Http\Requests\Maintenance\MtcKebutuhanMaterialRequest;

class MtcElectricEngineController extends Controller
{
    public function index()
    {
        $mesin = MtcMasterMesinModel::where('jenis_mtc', 'electric_engine')
            ->orderBy('id')->get();

        return view('maintenance.form.electric_engine', compact('mesin'));
    }

    public function viewData()
    {
        $mesin = MtcMasterMesinModel::where('jenis_mtc', 'electric_engine')
            ->orderBy('id')->get();

        return view('maintenance.data.electric_engine_data', compact('mesin'));
    }

    public function store(
        MtcMainRequest $mainRequest,
        MtcElectricEngineRequest $detailRequest,
        MtcKebutuhanMaterialRequest $materials
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest, $materials) {

            $userId = Auth::id();

            // Simpan Main
            $main = MtcMainModel::create([
                ...$mainRequest->validated(),
                'jenis_mtc'  => 'electric_engine',
                'status'     => 'pending',
                'created_by' => $userId,
            ]);

            MtcElectricEngineModel::create([
                ...$detailRequest->validated(),
                'mtc_main_id' => $main->id,
            ]);

            foreach ($materials->materials ?? [] as $item) {
                MtcKebutuhanMaterialModel::create([
                    'mtc_main_id'   => $main->id,
                    'mid'           => $item['mid'],
                    'deskripsi'     => $item['desc'] ?? null,
                    'qty'           => $item['qty'],
                    'created_by'    => $userId,
                ]);
            }

            $ttdPath = null;

            if ($detailRequest->filled('ttd_base64')) {
                $user = Auth::user();

                $ttdPath = saveBase64Signature(
                    $detailRequest->ttd_base64,
                    'mtc/electric-engine',
                    $user->username,
                    $user->departemen
                );
            }

            $approvalFlows = [
                [
                    'level' => 1,
                    'role'  => 'teknisi',
                    'approver_id' => $userId,
                    'auto'  => true,
                ],
                [
                    'level' => 2,
                    'role'  => 'staff',
                    'approver_id' => 3,
                    'auto'  => false,
                ],
                [
                    'level' => 3,
                    'role'  => 'user',
                    'approver_id' => 4,
                    'auto'  => false,
                ],
            ];

            foreach ($approvalFlows as $flow) {

                $isAutoApproved = $flow['auto'];

                MtcApprovalModel::create([
                    'mtc_main_id' => $main->id,
                    'level'       => $flow['level'],
                    'role'        => $flow['role'],
                    'approver_id' => $flow['approver_id'],
                    'status'      => $isAutoApproved ? 'approved' : 'pending',
                    'ttd'         => $isAutoApproved ? $ttdPath : null,
                    'action_at'   => $isAutoApproved ? now() : null,
                    'action_by'   => $isAutoApproved ? $userId : null,
                ]);

                if (!$isAutoApproved) {
                    NotificationsModel::create([
                        'user_id'         => $flow['approver_id'],
                        'notifiable_type' => MtcMainModel::class,
                        'notifiable_id'   => $main->id,
                        'title'           => 'Approval Maintenance',
                        'message'         => 'Maintenance Electric Engine menunggu persetujuan Anda',
                        'url'             => route('mtc.approval.index'),
                        'is_read'         => false,
                    ]);
                }
            }
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data Mtc Electric Engine berhasil disimpan',
        ], 201);
    }

    public function getData(Request $request)
    {
        $query = MtcMainModel::query()
            ->where('jenis_mtc', 'electric_engine')
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->with([
                'createdBy:id,username',
                'kebutuhanMaterial',
                'electricEngine.mesin:id,nama_mesin,lokasi'
            ]);

        // filter tanggal
        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        // filter paket
        if ($request->filled('paket')) {
            $query->where('paket', $request->paket);
        }

        // filter nama mesin (relasi)
        if ($request->filled('nama_mesin')) {
            $query->whereHas('electricEngine.mesin', function ($q) use ($request) {
                $q->where('nama_mesin', 'like', '%' . $request->nama_mesin . '%');
            });
        }

        $data = $query->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data Mtc Electric Engine berhasil diambil',
            'data'    => $data,
        ]);
    }

    public function update(
        MtcMainRequest $mainRequest,
        MtcElectricEngineRequest $detailRequest,
        MtcKebutuhanMaterialRequest $materials,
        $id
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest, $materials, $id) {

            $userId = Auth::id();

            $main = MtcMainModel::findOrFail($id);
            $inspection = MtcElectricEngineModel::where('mtc_main_id', $main->id)->firstOrFail();

            $main->update([
                ...$mainRequest->validated(),
                'updated_by' => $userId,
            ]);

            $inspection->update([
                ...$detailRequest->validated()
            ]);

            $existingIds = $main->kebutuhanMaterial()->pluck('id')->toArray();
            $incomingIds = [];

            foreach ($materials['materials'] as $item) {

                if (!empty($item['id'])) {

                    $incomingIds[] = $item['id'];

                    MtcKebutuhanMaterialModel::where('id', $item['id'])
                        ->update([
                            'mid'        => $item['mid'],
                            'deskripsi'  => $item['deskripsi'] ?? null,
                            'qty'        => $item['qty'],
                            'updated_by' => $userId,
                        ]);
                } else {

                    $new = MtcKebutuhanMaterialModel::create([
                        'mtc_main_id'       => $main->id,
                        'mid'               => $item['mid'],
                        'deskripsi'         => $item['deskripsi'] ?? null,
                        'qty'               => $item['qty'],
                        'created_by'        => $userId,
                    ]);

                    $incomingIds[] = $new->id;
                }
            }

            // DELETE material yg dihapus
            $toDelete = array_diff($existingIds, $incomingIds);
            if ($toDelete) {
                MtcKebutuhanMaterialModel::whereIn('id', $toDelete)->delete();
            }
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data berhasil diperbarui',
        ]);
    }
}
