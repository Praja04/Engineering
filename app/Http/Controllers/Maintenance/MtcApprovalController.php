<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use App\Models\NotificationsModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Maintenance\MtcMainModel;
use App\Models\Maintenance\MtcApprovalModel;
use App\Models\Maintenance\MtcBatteryMainModel;
use App\Models\Maintenance\MtcDieselEngineModel;
use App\Models\Maintenance\MtcDieselP2hInspectionModel;
use App\Models\Maintenance\MtcElectricalModel;
use App\Models\Maintenance\MtcElectricEngineModel;
use App\Models\Maintenance\MtcElectricP2hInspectionModel;
use App\Models\Maintenance\MtcMotorPumpModel;
use App\Models\Maintenance\MtcRefrigerasiModel;
use App\Models\Maintenance\MtcSipilInspectionModel;
use App\Models\Maintenance\MtcUtilityModel;

class MtcApprovalController extends Controller
{
    private function resolveTtdPath($user): ?string
    {
        $jabatan    = $user->jabatan;
        $departemen = strtolower($user->departemen ?? '');

        // Teknisi / Foreman Engineering → ttd_staff
        if (in_array($jabatan, ['operator', 'foreman']) && $departemen === 'engineering') {
            return 'mtc/ttd/ttd_staff.jpeg';
        }

        // Supervisor & Dept Head Engineering → ttd_user_eng
        if (in_array($jabatan, ['supervisor', 'dept_head']) && $departemen === 'engineering') {
            return 'mtc/ttd/ttd_user_eng.jpeg';
        }
        if ($jabatan === 'supervisor' && $departemen === 'qc') {
            return 'mtc/ttd/ttd_user_qc.jpeg';
        } else if ($jabatan === 'supervisor' && $departemen === 'produksi') {
            return 'mtc/ttd/ttd_user_prd.jpeg';
        } else if ($jabatan === 'supervisor' && $departemen === 'warehouse') {
            return 'mtc/ttd/ttd_user_warehouse.jpeg';
        }


        return null;
    }

    public function index()
    {
        $user = Auth::user();

        $approvals = MtcApprovalModel::with([
            'main',
            'main.createdBy'
        ])
            ->where('status', 'pending')
            ->where(function ($q) use ($user) {
                $q->where('approver_id', $user->id)
                    ->orWhere('role', $user->role);
            })
            ->orderBy('level')
            ->orderBy('created_at')
            ->get();

        $ttdPath = $this->resolveTtdPath($user);

        return view('maintenance.approval.approval_mtc', compact('approvals', 'ttdPath'));
    }

    public function detail(MtcMainModel $main)
    {
        // dd('MASUK CONTROLLER', $main->id, $main->jenis_mtc);
        $main->load(['kebutuhanMaterial']);

        switch ($main->jenis_mtc) {

            case 'Motor Pompa':
                $data = MtcMotorPumpModel::with('mesin')
                    ->where('mtc_main_id', $main->id)
                    ->firstOrFail();

                return view('maintenance.approval.partials.motor_pump_partials', compact('data', 'main'));

            case 'Utility':
                $data = MtcUtilityModel::with(['mesin'])
                    ->where('mtc_main_id', $main->id)
                    ->firstOrFail();

                // dd($main);
                return view('maintenance.approval.partials.utility_partials', compact('data', 'main'));

            case 'Electrical':
                $data = MtcElectricalModel::with(['mesin'])
                    ->where('mtc_main_id', $main->id)
                    ->firstOrFail();

                return view('maintenance.approval.partials.electrical_partials', compact('data', 'main'));

            case 'Refrigerasi':
                $data = MtcRefrigerasiModel::with(['mesin'])
                    ->where('mtc_main_id', $main->id)
                    ->firstOrFail();

                return view('maintenance.approval.partials.refrigerasi_partials', compact('data', 'main'));

            case 'Electric Engine':
                $data = MtcElectricEngineModel::with(['mesin'])
                    ->where('mtc_main_id', $main->id)
                    ->firstOrFail();

                return view('maintenance.approval.partials.electric_engine_partials', compact('data', 'main'));

            case 'Diesel Engine':
                $data = MtcDieselEngineModel::with(['mesin'])
                    ->where('mtc_main_id', $main->id)
                    ->firstOrFail();

                return view('maintenance.approval.partials.diesel_engine_partials', compact('data', 'main'));

            case 'Sipil':
                $data = MtcSipilInspectionModel::where('mtc_main_id', $main->id)
                    ->firstOrFail();

                return view('maintenance.approval.partials.sipil_partials', compact('data', 'main'));

            case 'Battery':
                $data = MtcBatteryMainModel::with(['details'])
                    ->where('mtc_main_id', $main->id)
                    ->firstOrFail();

                return view('maintenance.approval.partials.battery_partials', compact('data', 'main'));

            case 'Diesel P2H':
                $data = MtcDieselP2hInspectionModel::with(['mesin'])
                    ->where('mtc_main_id', $main->id)
                    ->firstOrFail();

                return view('maintenance.approval.partials.diesel_p2h_partials', compact('data', 'main'));

            case 'Electric P2H':
                $data = MtcElectricP2hInspectionModel::with(['mesin'])
                    ->where('mtc_main_id', $main->id)
                    ->firstOrFail();

                return view('maintenance.approval.partials.electric_p2h_partials', compact('data', 'main'));

            default:
                abort(404);
        }
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {

            $approval = MtcApprovalModel::where('id', $id)
                ->where('status', 'pending')
                ->where('approver_id', Auth::id())
                ->firstOrFail();

            // approve current level
            $approval->update([
                'status'    => 'approved',
                'action_at' => now(),
                'action_by' => Auth::id(),
            ]);

            // cari approval level berikutnya
            $nextApproval = MtcApprovalModel::where('mtc_main_id', $approval->mtc_main_id)
                ->where('level', $approval->level + 1)
                ->first();

            if ($nextApproval) {

                // kirim notif ke approver berikutnya
                NotificationsModel::create([
                    'user_id'         => $nextApproval->approver_id,
                    'notifiable_type' => MtcMainModel::class,
                    'notifiable_id'   => $approval->mtc_main_id,
                    'title'           => 'Approval Maintenance',
                    'message'         => 'Maintenance menunggu persetujuan Anda',
                    'url'             => route('mtc.approval.index'),
                    'is_read'         => false,
                ]);

                // main masih waiting
                MtcMainModel::where('id', $approval->mtc_main_id)
                    ->update(['status' => 'waiting']);
            } else {

                // approval terakhir
                MtcMainModel::where('id', $approval->mtc_main_id)
                    ->update(['status' => 'approved']);
            }
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data berhasil di-approve'
        ]);
    }

    public function reject(Request $request, $id)
    {
        DB::transaction(function () use ($id, $request) {

            $approval = MtcApprovalModel::findOrFail($id);

            $approval->update([
                'status'     => 'rejected',
                'action_at'  => now(),
                'action_by'  => Auth::id(),
                'catatan'       => $request->catatan ?? null,
            ]);

            MtcMainModel::where('id', $approval->mtc_main_id)
                ->update(['status' => 'rejected']);
        });

        return response()->json([
            'status' => true,
            'message' => 'Data Mtc berhasil direject'
        ]);
    }
}
