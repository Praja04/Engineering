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
use App\Models\Maintenance\MtcKebutuhanMaterialModel;
use App\Http\Requests\Maintenance\MtcMotorPumpRequest;
use App\Models\Maintenance\MtcMotorPumpModel;
use App\Http\Requests\Maintenance\MtcKebutuhanMaterialRequest;

class MtcMotorPumpController extends Controller
{
    public function index()
    {
        $mesin = MtcMasterMesinModel::where('jenis_mtc', 'Motor Pompa')
            ->orderBy('id')->get();

        return view('maintenance.form.motor_pump', compact('mesin'));
    }

    public function viewData()
    {
        $mesin = MtcMasterMesinModel::where('jenis_mtc', 'Motor Pompa')
            ->orderBy('id')->get();

        return view('maintenance.data.motor_pump_data', compact('mesin'));
    }

    public function store(
        MtcMainRequest $mainRequest,
        MtcMotorPumpRequest $detailRequest,
        MtcKebutuhanMaterialRequest $materials
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest, $materials) {

            $userId = Auth::id();

            // Simpan Main
            $main = MtcMainModel::create([
                ...$mainRequest->validated(),
                'jenis_mtc'     => 'Motor Pompa',
                'status'     => 'pending',
                'created_by' => $userId,
            ]);

            MtcMotorPumpModel::create([
                ...$detailRequest->validated(),
                'mtc_main_id' => $main->id,
            ]);

            foreach ($materials->materials ?? [] as $item) {
                MtcKebutuhanMaterialModel::create([
                    'mtc_main_id'   => $main->id,
                    'mid'           => $item['mid'] ?? null,
                    'deskripsi'     => $item['desc'] ?? null,
                    'qty'           => $item['qty'] ?? 0,
                    'created_by'    => $userId,
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

            $notificationSent = false;
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

                if (!$isAutoApproved && !$notificationSent) {
                    NotificationsModel::create([
                        'user_id'         => $flow['approver_id'],
                        'notifiable_type' => MtcMainModel::class,
                        'notifiable_id'   => $main->id,
                        'title'           => 'Approval Maintenance',
                        'message'         => 'Maintenance Motor Pump tanggal ' . date('d F Y', strtotime($main->tanggal)) . ' menunggu persetujuan Anda',
                        'url'             => route('mtc.approval.index'),
                        'is_read'         => false,
                    ]);

                    $notificationSent = true;
                }
            }
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data MTC Motor Pump berhasil disimpan',
        ], 201);
    }

    public function getData(Request $request)
    {
        $query = MtcMainModel::query()
            ->where('jenis_mtc', 'Motor Pompa')
            ->with([
                'createdBy:id,username',
                'kebutuhanMaterial',
                'motorPump.mesin:id,nama_mesin,lokasi'
            ]);

        // filter tanggal
        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        // filter paket
        if ($request->filled('paket')) {
            $query->where('paket', $request->paket);
        }

        // filter nama mesin
        if ($request->filled('nama_mesin')) {
            $query->whereHas('motorPump.mesin', function ($q) use ($request) {
                $q->where('nama_mesin', 'like', '%' . $request->nama_mesin . '%');
            });
        }

        // 🔥 total sebelum paging
        $total = $query->count();

        // 🔥 ambil data sesuai DataTables
        $data = $query
            ->orderBy('tanggal', 'desc')
            ->skip($request->start)
            ->take($request->length)
            ->get();

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $data
        ]);
    }

    public function update(
        MtcMainRequest $mainRequest,
        MtcMotorPumpRequest $detailRequest,
        MtcKebutuhanMaterialRequest $materials,
        $id
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest, $materials, $id) {

            $userId = Auth::id();

            $main = MtcMainModel::findOrFail($id);
            $inspection = MtcMotorPumpModel::where('mtc_main_id', $main->id)->firstOrFail();

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
                            'mid'        => $item['mid'] ?? null,
                            'deskripsi'  => $item['deskripsi'] ?? null,
                            'qty'        => $item['qty'] ?? 0,
                            'updated_by' => $userId,
                        ]);
                } else {

                    $new = MtcKebutuhanMaterialModel::create([
                        'mtc_main_id'       => $main->id,
                        'mid'               => $item['mid'] ?? null,
                        'deskripsi'         => $item['deskripsi'] ?? null,
                        'qty'               => $item['qty'] ?? 0,
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
