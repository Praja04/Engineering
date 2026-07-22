<?php

namespace App\Http\Controllers\Epr;

use App\Http\Controllers\Controller;
use App\Models\Epr\WorkOrder;
use App\Models\Epr\PredictiveMaintenance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Stats Counter
        $woStats = [
            'total'    => WorkOrder::count(),
            'open'     => WorkOrder::where('status', 'open')->count(),
            'assigned' => WorkOrder::where('status', 'assigned')->count(),
            'progress' => WorkOrder::where('status', 'progress')->count(),
            'done'     => WorkOrder::where('status', 'done')->count(),
            'approved' => WorkOrder::where('status', 'approved')->count(),
            'rejected' => WorkOrder::where('status', 'rejected')->count(),
        ];

        $pmStats = [
            'total'    => PredictiveMaintenance::whereNull('parent_id')->count(),
            'open'     => PredictiveMaintenance::whereNull('parent_id')->where('status', 'open')->count(),
            'progress' => PredictiveMaintenance::whereNull('parent_id')->where('status', 'progress')->count(),
            'done'     => PredictiveMaintenance::whereNull('parent_id')->where('status', 'done')->count(),
            'onhold'   => PredictiveMaintenance::whereNull('parent_id')->where('status', 'onhold')->count(),
        ];

        // 2. Workload Manpower (Operators WO & Reports count)
        $operators = User::where('jabatan', 'operator')
            ->where('bagian', 'Engineering Produksi (EPR)')
            ->get();

        $manpowerWorkload = $operators->map(function ($op) {
            // Count assigned active WOs
            $activeWoCount = DB::table('epr_wo_assignees')
                ->join('epr_work_orders', 'epr_wo_assignees.work_order_id', '=', 'epr_work_orders.id')
                ->where('epr_wo_assignees.user_id', $op->id)
                ->whereIn('epr_work_orders.status', ['assigned', 'progress'])
                ->count();

            // Count completed WOs
            $completedWoCount = DB::table('epr_wo_assignees')
                ->join('epr_work_orders', 'epr_wo_assignees.work_order_id', '=', 'epr_work_orders.id')
                ->where('epr_wo_assignees.user_id', $op->id)
                ->where('epr_work_orders.status', 'approved')
                ->count();

            // Count total PM reports submitted
            $pmReportCount = PredictiveMaintenance::where('created_by', $op->id)
                ->whereNull('parent_id')
                ->count();

            return [
                'username' => $op->username,
                'bagian' => $op->bagian,
                'active_wo' => $activeWoCount,
                'completed_wo' => $completedWoCount,
                'pm_reports' => $pmReportCount,
            ];
        });

        // 3. WO per Area
        $areas = ['Filling Retail', 'Packing Retail', 'Gravity Roller', 'Workshop', 'Pasteur', 'Storage', 'Lainnya'];
        $woPerArea = [];
        foreach ($areas as $area) {
            $woPerArea[$area] = WorkOrder::where('area', $area)->count();
        }

        // 4. WO per Priority
        $woPerPriority = [
            'critical' => WorkOrder::where('priority', 'critical')->count(),
            'high'     => WorkOrder::where('priority', 'high')->count(),
            'medium'   => WorkOrder::where('priority', 'medium')->count(),
            'low'      => WorkOrder::where('priority', 'low')->count(),
        ];

        // 5. Recent Reports and WOs
        $recentReports = PredictiveMaintenance::with(['photos', 'workOrder'])
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentWos = WorkOrder::with(['assignees.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('epr.dashboard', compact(
            'woStats',
            'pmStats',
            'manpowerWorkload',
            'woPerArea',
            'woPerPriority',
            'recentReports',
            'recentWos'
        ));
    }
}
