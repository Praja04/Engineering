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
use App\Models\Maintenance\MtcElectricP2hItemModel;
use App\Http\Requests\Maintenance\MtcElectricP2hRequest;
use App\Models\Maintenance\MtcElectricP2hInspectionModel;

class MtcElectricP2hController extends Controller
{
    public function index()
    {
        $mesin = MtcMasterMesinModel::where('jenis_mtc', 'electric_p2h')
            ->orderBy('id')->get();

        return view('maintenance.form.electric_p2h', compact('mesin'));
    }

    public function viewData()
    {
        $mesin = MtcMasterMesinModel::where('jenis_mtc', 'electric_p2h')
            ->orderBy('id')->get();

        return view('maintenance.data.electric_p2h_data', compact('mesin'));
    }

    public function store(
        MtcMainRequest $mainRequest,
        MtcElectricP2hRequest $detailRequest
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest) {

            $userId = Auth::id();

            $tanggal = $mainRequest->validated()['tanggal'];
            $shift   = $detailRequest->validated()['shift'];

            // CEK DATA SUDAH ADA ATAU BELUM
            $exists = MtcElectricP2hInspectionModel::where('shift', $shift)
                ->whereHas('main', function ($q) use ($tanggal) {
                    $q->where('tanggal', $tanggal)
                        ->where('jenis_mtc', 'electric_p2h');
                })
                ->exists();

            if ($exists) {
                abort(response()->json([
                    'status'  => false,
                    'message' => 'Data Electric P2H untuk tanggal dan shift tersebut sudah ada.',
                ], 422));
            }

            // Simpan Main
            $main = MtcMainModel::create([
                ...$mainRequest->validated(),
                'jenis_mtc'  => 'electric_p2h',
                'status'     => 'pending',
                'created_by' => $userId,
            ]);

            MtcElectricP2hInspectionModel::create([
                ...$detailRequest->validated(),
                'mtc_main_id' => $main->id,
            ]);

            $ttdPath = null;

            if ($detailRequest->filled('ttd_base64')) {
                $user = Auth::user();

                $ttdPath = saveBase64Signature(
                    $detailRequest->ttd_base64,
                    'mtc/electric-p2h',
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
                        'message'         => 'Maintenance Electric P2H menunggu persetujuan Anda',
                        'url'             => route('mtc.approval.index'),
                        'is_read'         => false,
                    ]);
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data P2H Electric berhasil disimpan',
        ]);
    }

    public function getData(Request $request)
    {
        $query = MtcMainModel::query()
            ->where('jenis_mtc', 'electric_p2h')
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->with([
                'createdBy:id,username',
                'electricP2h'
            ]);

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('no_unit')) {
            $query->whereHas('electricP2h', function ($q) use ($request) {
                $q->where('no_unit', 'like', '%' . $request->no_unit . '%');
            });
        }

        if ($request->filled('shift')) {
            $query->whereHas('electricP2h', function ($q) use ($request) {
                $q->where('shift', 'like', '%' . $request->shift . '%');
            });
        }

        if ($request->filled('departemen')) {
            $query->where('departemen', 'like', '%' . $request->departemen . '%');
        }

        $data = $query->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data P2H Electric berhasil diambil',
            'data'    => $data,
        ]);
    }

    public function update(
        MtcMainRequest $mainRequest,
        MtcElectricP2hRequest $detailRequest,
        $id
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest, $id) {

            $userId = Auth::id();

            $tanggal = $mainRequest->validated()['tanggal'];
            $shift   = $detailRequest->validated()['shift'];

            // CEK DATA SUDAH ADA ATAU BELUM
            $exists = MtcElectricP2hInspectionModel::where('shift', $shift)
                ->whereHas('main', function ($q) use ($tanggal) {
                    $q->where('tanggal', $tanggal)
                        ->where('jenis_mtc', 'electric_p2h');
                })
                ->exists();

            if ($exists) {
                abort(response()->json([
                    'status'  => false,
                    'message' => 'Data Electric P2H untuk tanggal dan shift tersebut sudah ada.',
                ], 422));
            }

            $main = MtcMainModel::findOrFail($id);
            $inspection = MtcElectricP2hInspectionModel::where('mtc_main_id', $main->id)->firstOrFail();

            $main->update([
                ...$mainRequest->validated(),
                'updated_by' => $userId,
            ]);

            $inspection->update([
                ...$detailRequest->validated()
            ]);
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Data P2H Electric berhasil diperbarui',
        ]);
    }
}
