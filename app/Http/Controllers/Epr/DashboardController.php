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
        // 1. Auto-detect default month from latest Corrective Maintenance record
        $defaultDate = \App\Models\Epr\CorrectiveMaintenance::max('tanggal');
        $defaultMonth = $defaultDate ? substr($defaultDate, 0, 7) : date('Y-m');
        $month = $request->input('month', $defaultMonth);
        $selectedDate = $request->input('date');

        // Available distinct months from database
        $availableMonths = \App\Models\Epr\CorrectiveMaintenance::selectRaw("DISTINCT SUBSTRING(tanggal, 1, 7) as m")
            ->orderBy('m', 'desc')
            ->pluck('m')
            ->filter()
            ->values()
            ->toArray();
        if ($defaultMonth && !in_array($defaultMonth, $availableMonths)) {
            array_unshift($availableMonths, $defaultMonth);
        }

        // Available distinct dates in this month
        $availableDates = \App\Models\Epr\CorrectiveMaintenance::where('tanggal', 'like', $month . '%')
            ->selectRaw("DISTINCT tanggal")
            ->orderBy('tanggal', 'asc')
            ->pluck('tanggal')
            ->toArray();

        // 2. Query CM Reports & Loading Times
        $queryCm = \App\Models\Epr\CorrectiveMaintenance::with('jenisDt');
        $queryLt = \App\Models\Epr\CmLoadingTime::query();

        if ($selectedDate) {
            $queryCm->where('tanggal', $selectedDate);
            $queryLt->where('tanggal', $selectedDate);
        } else {
            $queryCm->where('tanggal', 'like', $month . '%');
            $queryLt->where('tanggal', 'like', $month . '%');
        }

        $cmReports = $queryCm->orderBy('tanggal', 'desc')->orderBy('jam_mulai', 'desc')->get();
        $loadingTimes = $queryLt->get();

        // 3. Define Standard 28 Machines from Excel template sheet WEEKLY
        $pouchMachines = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'C', 'L', 'B', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'V', 'A', 'U'];
        $sachetMachines = ['O', 'P', 'W', 'X', 'R', 'Q', 'Y'];
        $defaultMachines = array_merge($pouchMachines, $sachetMachines);
        $extraMachines = $cmReports->pluck('mesin')
            ->filter(fn($m) => $m && !in_array(strtoupper(trim($m)), $defaultMachines))
            ->unique()
            ->values()
            ->toArray();
        $allMachineNames = array_merge($defaultMachines, $extraMachines);

        // Days in selected period
        $daysInMonth = (int) date('t', strtotime("$month-01"));
        $plannedMinsPerMachine = $selectedDate ? (24 * 60) : ($daysInMonth * 24 * 60);

        // 4. Calculate Per-Machine Statistics
        $machineStats = [];
        $grandTotalDt = 0;
        $grandTotalLt = 0;
        $grandTotalFreq = 0;

        foreach ($allMachineNames as $mName) {
            $mReports = $cmReports->filter(fn($r) => strtoupper(trim($r->mesin)) === strtoupper(trim($mName)));
            $freq = $mReports->count();

            $dtA = $mReports->filter(fn($r) => strtoupper(trim($r->grup)) === 'A')->sum('total_menit');
            $dtB = $mReports->filter(fn($r) => strtoupper(trim($r->grup)) === 'B')->sum('total_menit');
            $dtC = $mReports->filter(fn($r) => strtoupper(trim($r->grup)) === 'C')->sum('total_menit');
            $totalDt = $dtA + $dtB + $dtC;

            // Loading Time from table or fallback
            $ltRecs = $loadingTimes->filter(fn($lt) => strtoupper(trim($lt->mesin)) === strtoupper(trim($mName)));
            $ltA = $ltRecs->sum('loading_time_a');
            $ltB = $ltRecs->sum('loading_time_b');
            $ltC = $ltRecs->sum('loading_time_c');
            $machineLt = ($ltA + $ltB + $ltC) > 0 ? ($ltA + $ltB + $ltC) : $ltRecs->sum('loading_time_all');

            if ($machineLt == 0 && $freq > 0) {
                $machineLt = $plannedMinsPerMachine;
            }

            // Formulas matching Corrective Maintenance History
            $mttr = $freq > 0 ? round($totalDt / $freq, 1) : 0;
            $opTime = max(0, $machineLt - $totalDt);
            $mtbfMenit = $freq > 0 ? round($opTime / $freq, 1) : ($machineLt > 0 ? $machineLt : 0);
            $availPct = $machineLt > 0 ? round(($opTime / $machineLt) * 100, 2) : ($totalDt == 0 ? 100.0 : 0.0);
            $breakdownPct = $machineLt > 0 ? round(($totalDt / $machineLt) * 100, 2) : 0.0;

            // Score: 100 if no downtime, otherwise weighted by availability & MTBF
            if ($totalDt == 0) {
                $score = 100;
            } else {
                $score = min(99, max(10, round(($availPct * 0.7) - ($breakdownPct * 3) + min(20, $mtbfMenit / 50))));
            }

            $category = in_array(strtoupper(trim($mName)), $sachetMachines) ? 'Sachet' : 'Pouch';

            $machineStats[] = [
                'mesin' => $mName,
                'kategori' => $category,
                'score' => $score,
                'dt_a' => $dtA,
                'dt_b' => $dtB,
                'dt_c' => $dtC,
                'total_dt' => $totalDt,
                'loading_time' => $machineLt,
                'freq' => $freq,
                'mttr' => $mttr,
                'mtbf' => $mtbfMenit,
                'avail' => $availPct,
                'breakdown' => $breakdownPct,
            ];

            $grandTotalDt += $totalDt;
            $grandTotalLt += $machineLt;
            $grandTotalFreq += $freq;
        }

        // 5. Overall KPI Metrics
        $totalReports = $cmReports->count();
        $totalDowntime = $cmReports->sum('total_menit');
        $avgMttr = $totalReports > 0 ? round($totalDowntime / $totalReports, 1) : 0;
        $avgBreakdownPct = $grandTotalLt > 0 ? round(($grandTotalDt / $grandTotalLt) * 100, 2) : 0.0;
        $avgAvailPct = max(0, round(100 - $avgBreakdownPct, 2));
        $avgMtbf = $totalReports > 0 ? round(max(0, $grandTotalLt - $grandTotalDt) / $totalReports, 1) : ($grandTotalLt > 0 ? $grandTotalLt : 0);

        // 6. Worst 5 and Top 5 Machines
        // Worst 5: machines with highest downtime
        $worst5 = $machineStats;
        usort($worst5, fn($a, $b) => $b['total_dt'] <=> $a['total_dt']);
        $worst5 = array_slice($worst5, 0, 5);
        $worstMachineName = (count($worst5) > 0 && $worst5[0]['total_dt'] > 0)
            ? "Mesin {$worst5[0]['mesin']} ({$worst5[0]['total_dt']} m / {$worst5[0]['breakdown']}%)"
            : '—';

        // Top 5: machines with best availability & least downtime
        $top5 = $machineStats;
        usort($top5, function($a, $b) {
            if ($b['avail'] == $a['avail']) {
                return $a['total_dt'] <=> $b['total_dt'];
            }
            return $b['avail'] <=> $a['avail'];
        });
        $top5 = array_slice($top5, 0, 5);

        // 7. Electrical vs Mechanical Analysis
        $elecReports = $cmReports->filter(fn($r) => strtolower($r->electrical_mechanical) === 'electrical');
        $mechReports = $cmReports->filter(fn($r) => strtolower($r->electrical_mechanical) === 'mechanical');

        $elecCount = $elecReports->count();
        $elecDuration = $elecReports->sum('total_menit');
        $mechCount = $mechReports->count();
        $mechDuration = $mechReports->sum('total_menit');

        $elecShare = $totalDowntime > 0 ? round(($elecDuration / $totalDowntime) * 100, 1) : 0;
        $mechShare = $totalDowntime > 0 ? round(($mechDuration / $totalDowntime) * 100, 1) : 0;

        $top5Elec = $elecReports->sortByDesc('total_menit')->take(5)->values();
        $top5Mech = $mechReports->sortByDesc('total_menit')->take(5)->values();

        // 8. Shift Distribution (A, B, C)
        $shiftA = $cmReports->filter(fn($r) => strtoupper(trim($r->grup)) === 'A')->sum('total_menit');
        $shiftB = $cmReports->filter(fn($r) => strtoupper(trim($r->grup)) === 'B')->sum('total_menit');
        $shiftC = $cmReports->filter(fn($r) => strtoupper(trim($r->grup)) === 'C')->sum('total_menit');

        // 9. Pareto Chart: Downtime by Jenis DT (Failure Categories)
        $jenisDtGroups = $cmReports->groupBy(function($r) {
            return $r->jenisDt->name ?? ($r->downtime ?: 'Lain-lain');
        });
        $jenisDtStats = [];
        foreach ($jenisDtGroups as $name => $items) {
            $jenisDtStats[] = [
                'name' => $name,
                'count' => $items->count(),
                'duration' => $items->sum('total_menit')
            ];
        }
        usort($jenisDtStats, fn($a, $b) => $b['duration'] <=> $a['duration']);

        $paretoCategories = [];
        $paretoDurations = [];
        $paretoCumulative = [];
        $runningSum = 0;
        foreach (array_slice($jenisDtStats, 0, 10) as $st) {
            $paretoCategories[] = $st['name'];
            $paretoDurations[] = $st['duration'];
            $runningSum += $st['duration'];
            $paretoCumulative[] = $totalDowntime > 0 ? round(($runningSum / $totalDowntime) * 100, 1) : 0;
        }

        // 10. Machine Performance Map & Reliability Quadrant (Scatter)
        $perfHighBd = [];
        $perfFreqStop = [];
        $perfHealthy = [];

        $relHighMttr = [];
        $relLowMttr = [];
        $relIdeal = [];

        foreach ($machineStats as $st) {
            $pointPerf = [$st['mtbf'], $st['breakdown']];
            if ($st['breakdown'] > 3.0 || $st['total_dt'] >= 40) {
                $perfHighBd[] = $pointPerf;
            } elseif ($st['mtbf'] < 300 && $st['freq'] >= 2) {
                $perfFreqStop[] = $pointPerf;
            } else {
                $perfHealthy[] = $pointPerf;
            }

            $pointRel = [$st['mtbf'], $st['mttr']];
            if ($st['mttr'] > 30) {
                $relHighMttr[] = $pointRel;
            } elseif ($st['mtbf'] < 300) {
                $relLowMttr[] = $pointRel;
            } else {
                $relIdeal[] = $pointRel;
            }
        }

        $chartPerfMapSeries = [
            ['name' => 'Low MTBF / Frequent Stop', 'data' => $perfFreqStop],
            ['name' => 'High Breakdown (Prioritas)', 'data' => $perfHighBd],
            ['name' => 'Healthy Zone / Ideal', 'data' => $perfHealthy],
        ];

        $chartReliabilitySeries = [
            ['name' => 'High MTTR (Slow Repair)', 'data' => $relHighMttr],
            ['name' => 'Low MTTR (Fast Repair)', 'data' => $relLowMttr],
            ['name' => 'Ideal Condition (Fast & Stable)', 'data' => $relIdeal],
        ];

        // 11. Breakdown Trend by 4 Top Worst Machines across shifts or months
        $trendCategories = ['Shift A', 'Shift B', 'Shift C'];
        $chartTrendSeries = [];
        $topWorstForTrend = array_slice($worst5, 0, 4);

        foreach ($topWorstForTrend as $tw) {
            if ($tw['total_dt'] > 0) {
                $chartTrendSeries[] = [
                    'name' => 'Mesin ' . $tw['mesin'],
                    'data' => [$tw['dt_a'], $tw['dt_b'], $tw['dt_c']]
                ];
            }
        }

        // 12. Optional Cost, Machine KPI, Action Plan models
        $dbCosts = \App\Models\Epr\CmCost::where('tanggal', 'like', $month . '%')->get();
        $totalCostVal = $dbCosts->sum('jumlah_biaya');
        $dbMachineKpis = \App\Models\Epr\CmMachineKpi::where('month', $month)->get();
        $dbActionPlans = \App\Models\Epr\CmActionPlan::where('month', $month)->get();

        $additionalKpiAvg = [
            'pm_compliance' => $dbMachineKpis->count() > 0 ? round($dbMachineKpis->avg('pm_compliance_pct'), 1) : 96.5,
            'repeat_failure' => $dbMachineKpis->count() > 0 ? round($dbMachineKpis->avg('repeat_failure_pct'), 1) : 4.2,
            'minor_stop' => $dbMachineKpis->count() > 0 ? round($dbMachineKpis->avg('minor_stop_freq'), 1) : 3,
            'cost_per_hour' => $dbMachineKpis->count() > 0 ? round($dbMachineKpis->avg('cost_per_hour'), 1) : 15.0,
            'energy_per_pack' => $dbMachineKpis->count() > 0 ? round($dbMachineKpis->avg('energy_per_pack'), 2) : 0.28,
        ];

        return view('epr.dashboard-cm', compact(
            'month',
            'selectedDate',
            'availableMonths',
            'availableDates',
            'cmReports',
            'loadingTimes',
            'totalReports',
            'totalDowntime',
            'avgAvailPct',
            'avgBreakdownPct',
            'avgMttr',
            'avgMtbf',
            'grandTotalLt',
            'worstMachineName',
            'top5',
            'worst5',
            'machineStats',
            'elecCount',
            'elecDuration',
            'elecShare',
            'mechCount',
            'mechDuration',
            'mechShare',
            'top5Elec',
            'top5Mech',
            'shiftA',
            'shiftB',
            'shiftC',
            'paretoCategories',
            'paretoDurations',
            'paretoCumulative',
            'chartPerfMapSeries',
            'chartTrendSeries',
            'trendCategories',
            'chartReliabilitySeries',
            'dbCosts',
            'totalCostVal',
            'dbMachineKpis',
            'dbActionPlans',
            'additionalKpiAvg'
        ));
    }

}
