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
use App\Models\Maintenance\MtcDieselP2hItemModel;
use App\Http\Requests\Maintenance\MtcDieselP2hRequest;
use App\Models\Maintenance\MtcDieselP2hInspectionModel;

class MtcDieselP2hController extends Controller
{
    public function index()
    {
        $mesin = MtcMasterMesinModel::where('jenis_mtc', 'Diesel P2H')
            ->orderBy('id')->get();

        return view('maintenance.form.diesel_p2h', compact('mesin'));
    }

    public function viewData()
    {
        $mesin = MtcMasterMesinModel::where('jenis_mtc', 'Diesel P2H')
            ->orderBy('id')->get();

        return view('maintenance.data.diesel_p2h_data', compact('mesin'));
    }

    public function store(
        MtcMainRequest $mainRequest,
        MtcDieselP2hRequest $detailRequest
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest) {

            $userId = Auth::id();

            $tanggal = $mainRequest->validated()['tanggal'];
            $shift   = $detailRequest->validated()['shift'];

            // CEK DATA SUDAH ADA ATAU BELUM
            $exists = MtcDieselP2hInspectionModel::where('shift', $shift)
                ->whereHas('main', function ($q) use ($tanggal) {
                    $q->where('tanggal', $tanggal)
                        ->where('jenis_mtc', 'Diesel P2H');
                })
                ->exists();

            if ($exists) {
                abort(response()->json([
                    'status'  => false,
                    'message' => 'Data Diesel P2H untuk tanggal dan shift tersebut sudah ada.',
                ], 422));
            }

            // Simpan Main
            $main = MtcMainModel::create([
                ...$mainRequest->validated(),
                'jenis_mtc'  => 'Diesel P2H',
                'status'     => 'pending',
                'created_by' => $userId,
            ]);

            MtcDieselP2hInspectionModel::create([
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
                        'message'         => 'Maintenance Diesel P2H menunggu persetujuan Anda',
                        'url'             => route('mtc.approval.index'),
                        'is_read'         => false,
                    ]);
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data P2H Diesel berhasil disimpan',
        ]);
    }

    public function getData(Request $request)
    {
        $query = MtcMainModel::query()
            ->where('jenis_mtc', 'Diesel P2H')
            ->orderBy('tanggal', 'desc')
            // ->orderBy('waktu', 'desc')
            ->with([
                'createdBy:id,username',
                'dieselP2h.mesin'
            ]);

        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
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
            'message' => 'Data P2H Diesel berhasil diambil',
            'data'    => $data,
        ]);
    }

    public function update(
        MtcMainRequest $mainRequest,
        MtcDieselP2hRequest $detailRequest,
        $id
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest, $id) {

            $userId = Auth::id();

            $main = MtcMainModel::findOrFail($id);
            $inspection = MtcDieselP2hInspectionModel::where('mtc_main_id', $main->id)->firstOrFail();

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
            'message' => 'Data P2H Diesel berhasil diperbarui',
        ]);
    }
}
