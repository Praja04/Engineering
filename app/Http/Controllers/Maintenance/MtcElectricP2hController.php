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
        $mesin = MtcMasterMesinModel::where('jenis_mtc', 'Electrical P2H')
            ->orderBy('id')->get();

        return view('maintenance.form.electric_p2h', compact('mesin'));
    }

    public function viewData()
    {
        $mesin = MtcMasterMesinModel::where('jenis_mtc', 'Electrical P2H')
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
                        ->where('jenis_mtc', 'Electrical P2H');
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
                'jenis_mtc'  => 'Electrical P2H',
                'status'     => 'pending',
                'created_by' => $userId,
            ]);

            MtcElectricP2hInspectionModel::create([
                ...$detailRequest->validated(),
                'mtc_main_id' => $main->id,
            ]);

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
            ->where('jenis_mtc', 'Electrical P2H')
            ->with([
                'createdBy:id,username',
                'electricP2h'
            ]);

        // 🔍 filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // 🔍 filter no unit
        if ($request->filled('no_unit')) {
            $query->whereHas('electricP2h', function ($q) use ($request) {
                $q->where('no_unit', 'like', '%' . $request->no_unit . '%');
            });
        }

        // 🔍 filter shift
        if ($request->filled('shift')) {
            $query->whereHas('electricP2h', function ($q) use ($request) {
                $q->where('shift', 'like', '%' . $request->shift . '%');
            });
        }

        // 🔍 filter departemen
        if ($request->filled('departemen')) {
            $query->where('departemen', 'like', '%' . $request->departemen . '%');
        }

        // 🔥 total setelah filter
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
                        ->where('jenis_mtc', 'Electrical P2H');
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
