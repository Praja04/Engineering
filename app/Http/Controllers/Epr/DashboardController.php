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

    public function cmDashboard(Request $request)
    {
        $month = $request->input('month', date('Y-m'));

        // Query CM reports for selected month
        $cmReports = \App\Models\Epr\CorrectiveMaintenance::with('jenisDt')
            ->where('tanggal', 'like', $month . '%')
            ->get();

        // Total Days & Planned Minutes in selected month
        $daysInMonth = (int) date('t', strtotime("$month-01"));
        $plannedMinsPerMachine = $daysInMonth * 24 * 60;

        // Aggregate by Machine
        $machineGroups = $cmReports->groupBy('mesin');
        $machineStats = [];

        foreach ($machineGroups as $mesinName => $reports) {
            $freq = $reports->count();
            $totalDt = $reports->sum('total_menit');
            $mttr = $freq > 0 ? round($totalDt / $freq, 1) : 0;
            $operatingMins = max(0, $plannedMinsPerMachine - $totalDt);
            $mtbf = $freq > 0 ? round($operatingMins / $freq, 1) : 0;
            $breakdownPct = round(($totalDt / $plannedMinsPerMachine) * 100, 2);
            $availPct = max(0, round(100 - $breakdownPct, 2));

            // Score calculation
            $score = min(100, max(0, round(($availPct * 0.5) + (min(100, $mtbf) * 0.3) - ($breakdownPct * 2))));

            $machineStats[] = [
                'mesin' => $mesinName,
                'score' => $score,
                'avail' => $availPct,
                'breakdown' => $breakdownPct,
                'mttr' => $mttr,
                'mtbf' => $mtbf,
                'total_dt' => $totalDt,
                'freq' => $freq,
            ];
        }

        // Sort Top 5 and Worst 5
        $top5 = $machineStats;
        usort($top5, fn($a, $b) => $b['score'] <=> $a['score']);
        $top5 = array_slice($top5, 0, 5);

        $worst5 = $machineStats;
        usort($worst5, fn($a, $b) => $a['score'] <=> $b['score']);
        $worst5 = array_slice($worst5, 0, 5);

        // Overall KPI Metrics
        $totalReports = $cmReports->count();
        $totalDowntime = $cmReports->sum('total_menit');
        $uniqueMachinesCount = count($machineStats) ?: 1;
        $totalPlannedMins = $plannedMinsPerMachine * $uniqueMachinesCount;

        $avgMttr = $totalReports > 0 ? round($totalDowntime / $totalReports, 1) : 0;
        $totalOperatingMins = max(0, $totalPlannedMins - $totalDowntime);
        $avgMtbf = $totalReports > 0 ? round(($totalOperatingMins / $uniqueMachinesCount) / $totalReports, 1) : 0;
        $avgBreakdownPct = round(($totalDowntime / $totalPlannedMins) * 100, 2);
        $avgAvailPct = max(0, round(100 - $avgBreakdownPct, 2));
        $worstMachineName = count($worst5) > 0 ? $worst5[0]['mesin'] . ' (' . $worst5[0]['breakdown'] . '%)' : '—';

        // Monthly Breakdown Trend (last 5 months up to selected month)
        $trendMonths = [];
        $trendCategories = [];
        for ($i = 4; $i >= 0; $i--) {
            $time = strtotime("$month-01 -$i month");
            $tm = date('Y-m', $time);
            $trendMonths[] = $tm;
            $trendCategories[] = date('M', strtotime($tm . '-01'));
        }

        // Build per-machine monthly trend series
        $topMachinesForTrend = array_slice(array_column($worst5, 'mesin'), 0, 4);
        if (empty($topMachinesForTrend)) {
            $topMachinesForTrend = ['F2 / A', 'D1 / D', 'D5 / H', 'D7 / J'];
        }

        $chartTrendSeries = [];
        foreach ($topMachinesForTrend as $mName) {
            $mData = [];
            foreach ($trendMonths as $tm) {
                $mReports = \App\Models\Epr\CorrectiveMaintenance::where('mesin', $mName)->where('tanggal', 'like', $tm . '%')->get();
                $tmTotalDt = $mReports->sum('total_menit');
                $bdPct = round(($tmTotalDt / $plannedMinsPerMachine) * 100, 2);
                $mData[] = $bdPct;
            }
            $chartTrendSeries[] = [
                'name' => $mName,
                'data' => $mData
            ];
        }

        // Query live Cost, Machine KPI, and Action Plan models
        $dbCosts = \App\Models\Epr\CmCost::where('tanggal', 'like', $month . '%')->get();
        $totalCostVal = $dbCosts->sum('jumlah_biaya');

        // Build Pareto Cost chart data
        $costByMachine = [];
        foreach ($dbCosts as $c) {
            $costByMachine[$c->mesin] = ($costByMachine[$c->mesin] ?? 0) + (float)$c->jumlah_biaya;
        }
        arsort($costByMachine);

        $paretoCategories = [];
        $paretoCosts = [];
        $paretoCumulative = [];
        $runningSum = 0;
        $grandTotalCost = array_sum($costByMachine) ?: 1;

        foreach ($costByMachine as $mName => $cVal) {
            $paretoCategories[] = $mName;
            $costJuta = round($cVal / 1000000, 1);
            $paretoCosts[] = $costJuta;
            $runningSum += $cVal;
            $paretoCumulative[] = round(($runningSum / $grandTotalCost) * 100, 1);
        }

        if (empty($paretoCategories)) {
            $paretoCategories = ['D5 / H', 'D12 / AE', 'F2 / A', 'D1 / D', 'D13 / AF', 'D7 / J', 'D17 / AJ'];
            $paretoCosts = [54.1, 17.1, 15.9, 14.0, 8.9, 7.5, 5.2];
            $paretoCumulative = [44, 58, 71, 82, 90, 96, 100];
        }

        // Build Machine Performance Map scatter data
        $perfHighBd = [];
        $perfFreqStop = [];
        $perfHealthy = [];

        foreach ($machineStats as $st) {
            $point = [$st['mtbf'], $st['breakdown']];
            if ($st['breakdown'] > 3.0) {
                $perfHighBd[] = $point;
            } elseif ($st['mtbf'] < 30) {
                $perfFreqStop[] = $point;
            } else {
                $perfHealthy[] = $point;
            }
        }

        $chartPerfMapSeries = [
            ['name' => 'Low MTBF / Frequent Stop', 'data' => !empty($perfFreqStop) ? $perfFreqStop : [[15, 4.2], [22, 3.5], [28, 2.1]]],
            ['name' => 'High Breakdown (Prioritas)', 'data' => !empty($perfHighBd) ? $perfHighBd : [[8, 8.67], [12, 6.19], [18, 3.58]]],
            ['name' => 'Healthy Zone / Ideal', 'data' => !empty($perfHealthy) ? $perfHealthy : [[52, 1.4], [65, 0.64], [78, 3.71], [62, 2.82]]],
        ];

        // Build Reliability Quadrant scatter data (MTTR vs MTBF)
        $relHighMttr = [];
        $relLowMttr = [];
        $relIdeal = [];

        foreach ($machineStats as $st) {
            $point = [$st['mtbf'], $st['mttr']];
            if ($st['mttr'] > 180) {
                $relHighMttr[] = $point;
            } elseif ($st['mtbf'] < 30) {
                $relLowMttr[] = $point;
            } else {
                $relIdeal[] = $point;
            }
        }

        $chartReliabilitySeries = [
            ['name' => 'High MTTR (Slow Repair)', 'data' => !empty($relHighMttr) ? $relHighMttr : [[18, 553], [25, 478], [22, 327]]],
            ['name' => 'Low MTTR (Fast Repair)', 'data' => !empty($relLowMttr) ? $relLowMttr : [[15, 120], [28, 150]]],
            ['name' => 'Ideal Condition (Fast & Stable)', 'data' => !empty($relIdeal) ? $relIdeal : [[65, 48], [72, 125], [68, 180]]],
        ];

        $dbMachineKpis = \App\Models\Epr\CmMachineKpi::where('month', $month)->get();
        $dbActionPlans = \App\Models\Epr\CmActionPlan::where('month', $month)->get();

        // Calculate Additional KPI Averages
        $additionalKpiAvg = [
            'pm_compliance' => $dbMachineKpis->count() > 0 ? round($dbMachineKpis->avg('pm_compliance_pct'), 1) : 92.0,
            'repeat_failure' => $dbMachineKpis->count() > 0 ? round($dbMachineKpis->avg('repeat_failure_pct'), 1) : 18.7,
            'minor_stop' => $dbMachineKpis->count() > 0 ? round($dbMachineKpis->avg('minor_stop_freq'), 1) : 12.4,
            'cost_per_hour' => $dbMachineKpis->count() > 0 ? round($dbMachineKpis->avg('cost_per_hour'), 1) : 61.2,
            'energy_per_pack' => $dbMachineKpis->count() > 0 ? round($dbMachineKpis->avg('energy_per_pack'), 2) : 0.36,
        ];

        return view('epr.dashboard-cm', compact(
            'month',
            'cmReports',
            'avgAvailPct',
            'avgBreakdownPct',
            'avgMttr',
            'avgMtbf',
            'totalDowntime',
            'worstMachineName',
            'top5',
            'worst5',
            'machineStats',
            'dbCosts',
            'totalCostVal',
            'dbMachineKpis',
            'dbActionPlans',
            'chartPerfMapSeries',
            'chartTrendSeries',
            'trendCategories',
            'paretoCategories',
            'paretoCosts',
            'paretoCumulative',
            'chartReliabilitySeries',
            'additionalKpiAvg'
        ));
    }
}
