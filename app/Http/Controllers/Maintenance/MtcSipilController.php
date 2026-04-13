<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use App\Models\NotificationsModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Maintenance\MtcMainModel;
use App\Models\Maintenance\MtcApprovalModel;
use App\Http\Requests\Maintenance\MtcMainRequest;
use App\Http\Requests\Maintenance\MtcSipilRequest;
use App\Models\Maintenance\MtcSipilInspectionModel;
use App\Models\Maintenance\MtcKebutuhanMaterialModel;
use App\Http\Requests\Maintenance\MtcKebutuhanMaterialRequest;
use App\Models\Maintenance\MtcMasterMesinModel;

class MtcSipilController extends Controller
{
    public function index()
    {
        $area = MtcMasterMesinModel::where('jenis_mtc', 'Sipil')
        ->orderBy('id')->get();
        return view('maintenance.form.sipil')->with(compact('area'));
    }

    public function viewData()
    {
        return view('maintenance.data.sipil_data');
    }

    public function store(
        MtcMainRequest $mainRequest,
        MtcSipilRequest $detailRequest,
        MtcKebutuhanMaterialRequest $materials
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest, $materials) {

            $userId = Auth::id();

            // Simpan Main
            $main = MtcMainModel::create([
                ...$mainRequest->validated(),
                'jenis_mtc'  => 'Sipil',
                'status'     => 'pending',
                'created_by' => $userId,
            ]);

            MtcSipilInspectionModel::create([
                ...$detailRequest->validated(),
                'mtc_main_id' => $main->id,
            ]);

            foreach ($materials->materials ?? [] as $item) {
                MtcKebutuhanMaterialModel::create([
                    'mtc_main_id' => $main->id,
                    'mid'        => $item['mid'],
                    'deskripsi'  => $item['desc'] ?? null,
                    'qty'        => $item['qty'],
                    'created_by' => $userId,
                ]);
            }

            $ttdPaths = [
                'teknisi' => 'mtc/ttd/ttd_teknisi.jpeg',  // TTD operator/teknisi
                'staff'   => 'mtc/ttd/ttd_staff.jpeg',     // TTD supervisor
                'user'    => 'mtc/ttd/ttd_user.jpeg',      // TTD user MT/MTC
            ];


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
                    'approver_id' => $mainRequest->staff_id,
                    'auto'  => false,
                ],
                [
                    'level' => 3,
                    'role'  => 'user',
                    'approver_id' => $mainRequest->user_id,
                    'auto'  => false,
                ],
            ];

            foreach ($approvalFlows as $flow) {

                $isAutoApproved = $flow['auto'];
                $ttdPath = $isAutoApproved ? ($ttdPaths[$flow['role']] ?? null) : null;

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
                        'message'         => 'Maintenance Sipil menunggu persetujuan Anda',
                        'url'             => route('mtc.approval.index'),
                        'is_read'         => false,
                    ]);
                }
            }
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data inspeksi sipil berhasil disimpan',
        ], 201);
    }

    public function getData(Request $request)
    {
        $query = MtcMainModel::query()
            ->where('jenis_mtc', 'Sipil')
            ->orderBy('tanggal', 'desc')
            // ->orderBy('waktu', 'desc')
            ->with([
                'createdBy:id,username',
                'Sipil',
                'kebutuhanMaterial'
            ]);

        // Filter tanggal (jika ada parameter date)
        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        // Filter area (partial match)
        if ($request->filled('area')) {
            $query->where('area', 'like', '%' . $request->area . '%');
        }

        // Filter rekomendasi (partial match)
        if ($request->filled('departemen')) {
            $query->where('departemen', 'like', '%' . $request->departemen . '%');
        }

        $data = $query->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data Mtc Sipil berhasil diambil',
            'data'    => $data,
        ]);
    }

    public function update(
        MtcMainRequest $mainRequest,
        MtcSipilRequest $detailRequest,
        MtcKebutuhanMaterialRequest $materials,
        $id
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest, $materials, $id) {

            $userId = Auth::id();

            $main = MtcMainModel::findOrFail($id);
            $inspection = MtcSipilInspectionModel::where('mtc_main_id', $main->id)->firstOrFail();

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
            'message' => 'Data inspeksi sipil berhasil diupdate',
        ]);
    }
}
