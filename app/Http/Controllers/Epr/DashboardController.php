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
        $currentMonth = date('Y-m');
        if (!in_array($currentMonth, $availableMonths)) {
            $availableMonths[] = $currentMonth;
        }

        // Filter Mode: 'month', 'week', 'date', 'custom'
        $filterType = $request->input('filter_type');
        if (!$filterType) {
            if ($request->filled('start_date') || $request->filled('end_date')) {
                $filterType = 'custom';
            } elseif ($request->filled('week')) {
                $filterType = 'week';
            } elseif ($request->filled('date')) {
                $filterType = 'date';
            } else {
                $filterType = 'month';
            }
        }

        $month = $request->input('month', $defaultMonth);
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = $defaultMonth;
        }

        // Daftar minggu kalender (Senin - Minggu) pada bulan terpilih
        $weeksInMonth = self::getWeeksOfMonth($month);

        $week = (int) $request->input('week', 1);
        $selectedWeekObj = null;
        foreach ($weeksInMonth as $wk) {
            if ($wk['week_num'] === $week) {
                $selectedWeekObj = $wk;
                break;
            }
        }
        if (!$selectedWeekObj && count($weeksInMonth) > 0) {
            $selectedWeekObj = $weeksInMonth[0];
            $week = $selectedWeekObj['week_num'];
        }

        $selectedDate = $request->input('date', $defaultDate ?: date('Y-m-d'));
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');
        $kategori = strtolower(trim((string)$request->input('kategori', 'all')));

        // Calculate Start Date, End Date, and Period Label based on filterType
        $daysInSelectedMonth = (int) date('t', strtotime("$month-01"));
        $startDate = "$month-01";
        $endDate = "$month-$daysInSelectedMonth";
        $periodLabel = date('F Y', strtotime("$month-01"));

        if ($filterType === 'week' && $selectedWeekObj) {
            // Kalender Mingguan standar Senin s/d Minggu (misal 16 Nov - 22 Nov 2026)
            $startDate = $selectedWeekObj['start'];
            $endDate = $selectedWeekObj['end'];
            $periodLabel = $selectedWeekObj['label'];
        } elseif ($filterType === 'date') {
            if ($selectedDate) {
                $startDate = $selectedDate;
                $endDate = $selectedDate;
                $periodLabel = date('d F Y', strtotime($selectedDate));
            }
        } elseif ($filterType === 'custom') {
            $startDate = $customStart ?: "$month-01";
            $endDate = $customEnd ?: "$month-$daysInSelectedMonth";
            if ($startDate > $endDate) {
                [$startDate, $endDate] = [$endDate, $startDate];
            }
            $periodLabel = date('d M Y', strtotime($startDate)) . " s/d " . date('d M Y', strtotime($endDate));
        }

        // Available distinct dates in the selected period
        $availableDates = \App\Models\Epr\CorrectiveMaintenance::whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw("DISTINCT tanggal")
            ->orderBy('tanggal', 'asc')
            ->pluck('tanggal')
            ->toArray();

        // 2. Query CM Reports & Loading Times
        $queryCm = \App\Models\Epr\CorrectiveMaintenance::with('jenisDt')
            ->whereBetween('tanggal', [$startDate, $endDate]);
        $queryLt = \App\Models\Epr\CmLoadingTime::whereBetween('tanggal', [$startDate, $endDate]);

        // 3. Define Standard 28 Machines from Excel template sheet WEEKLY
        $pouchMachines = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'C', 'L', 'B', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'V', 'A', 'U'];
        $sachetMachines = ['O', 'P', 'W', 'X', 'R', 'Q', 'Y'];
        $defaultMachines = array_merge($pouchMachines, $sachetMachines);

        if ($kategori === 'pouch') {
            $queryCm->whereIn(\Illuminate\Support\Facades\DB::raw('UPPER(TRIM(mesin))'), $pouchMachines);
            $queryLt->whereIn(\Illuminate\Support\Facades\DB::raw('UPPER(TRIM(mesin))'), $pouchMachines);
            $activeMachines = $pouchMachines;
        } elseif ($kategori === 'sachet') {
            $queryCm->whereIn(\Illuminate\Support\Facades\DB::raw('UPPER(TRIM(mesin))'), $sachetMachines);
            $queryLt->whereIn(\Illuminate\Support\Facades\DB::raw('UPPER(TRIM(mesin))'), $sachetMachines);
            $activeMachines = $sachetMachines;
        } else {
            $activeMachines = $defaultMachines;
        }

        $cmReports = $queryCm->orderBy('tanggal', 'desc')->orderBy('jam_mulai', 'desc')->get();
        $loadingTimes = $queryLt->get();

        $extraMachines = $cmReports->pluck('mesin')
            ->filter(fn($m) => $m && !in_array(strtoupper(trim($m)), $activeMachines))
            ->unique()
            ->values()
            ->toArray();
        $allMachineNames = array_merge($activeMachines, $extraMachines);

        // Days in selected period
        $startCarbon = \Carbon\Carbon::parse($startDate)->startOfDay();
        $endCarbon = \Carbon\Carbon::parse($endDate)->endOfDay();
        $daysInPeriod = max(1, (int) $startCarbon->diffInDays($endCarbon) + 1);
        $plannedMinsPerMachine = $daysInPeriod * 24 * 60;

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

            if ($machineLt == 0) {
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

        // 10. Optional Cost, Machine KPI, Action Plan models
        $dbCosts = \App\Models\Epr\CmCost::whereBetween('tanggal', [$startDate, $endDate])->get();
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

        
        // ---------------------------------------------------------
        // Daily Downtime Trend for Mechanical & Electrical
        // ---------------------------------------------------------
        $dailyDates = [];
        $dailyLabels = [];
        $dailyFullDates = [];
        $dailyMechPct = [];
        $dailyElecPct = [];
        $dailyMechMins = [];
        $dailyElecMins = [];
        $dailyMechCount = [];
        $dailyElecCount = [];

        $dayNamesIndo = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $currDate = \Carbon\Carbon::parse($startDate)->startOfDay();
        $endDateCarbon = \Carbon\Carbon::parse($endDate)->startOfDay();

        while ($currDate->lte($endDateCarbon)) {
            $dStr = $currDate->format('Y-m-d');
            $dailyDates[] = $dStr;

            $dayEnglish = $currDate->format('l');
            $dayIndo = $dayNamesIndo[$dayEnglish] ?? $dayEnglish;

            if ($daysInPeriod <= 7) {
                // Sesuai contoh template Excel: "Senin", "Selasa", "Rabu", dst.
                $dailyLabels[] = $dayIndo;
                $dailyFullDates[] = $dayIndo . ', ' . $currDate->format('d/m/Y');
            } elseif ($daysInPeriod <= 31) {
                $dailyLabels[] = $currDate->format('d M') . ' (' . substr($dayIndo, 0, 3) . ')';
                $dailyFullDates[] = $dayIndo . ', ' . $currDate->format('d/m/Y');
            } else {
                $dailyLabels[] = $currDate->format('d/m/Y');
                $dailyFullDates[] = $dayIndo . ', ' . $currDate->format('d/m/Y');
            }

            // Reports for this day
            $dayReports = $cmReports->filter(fn($r) => $r->tanggal === $dStr);
            $dayMechReports = $dayReports->filter(fn($r) => strtolower($r->electrical_mechanical) === 'mechanical');
            $dayElecReports = $dayReports->filter(fn($r) => strtolower($r->electrical_mechanical) === 'electrical');

            $mMenit = $dayMechReports->sum('total_menit');
            $eMenit = $dayElecReports->sum('total_menit');
            $mCount = $dayMechReports->count();
            $eCount = $dayElecReports->count();

            // Daily loading time for this day from loadingTimes
            $dayLtRecs = $loadingTimes->filter(fn($lt) => $lt->tanggal === $dStr);
            $dayLt = $dayLtRecs->sum(function($lt) {
                $sum = ($lt->loading_time_a ?? 0) + ($lt->loading_time_b ?? 0) + ($lt->loading_time_c ?? 0);
                return $sum > 0 ? $sum : ($lt->loading_time_all ?? 0);
            });

            if ($dayLt <= 0) {
                $dayLt = count($activeMachines) * 24 * 60;
            }

            $mPct = $dayLt > 0 ? round(($mMenit / $dayLt) * 100, 2) : 0.0;
            $ePct = $dayLt > 0 ? round(($eMenit / $dayLt) * 100, 2) : 0.0;

            $dailyMechPct[] = $mPct;
            $dailyElecPct[] = $ePct;
            $dailyMechMins[] = $mMenit;
            $dailyElecMins[] = $eMenit;
            $dailyMechCount[] = $mCount;
            $dailyElecCount[] = $eCount;

            $currDate->addDay();
        }

        $avgDailyMechPct = count($dailyMechPct) > 0 ? round(array_sum($dailyMechPct) / count($dailyMechPct), 2) : 0.0;
        $avgDailyElecPct = count($dailyElecPct) > 0 ? round(array_sum($dailyElecPct) / count($dailyElecPct), 2) : 0.0;
        $maxDailyMechPct = count($dailyMechPct) > 0 ? max($dailyMechPct) : 0.0;
        $maxDailyElecPct = count($dailyElecPct) > 0 ? max($dailyElecPct) : 0.0;

        return view('epr.dashboard-cm', compact(
            'dailyDates',
            'dailyLabels',
            'dailyFullDates',
            'dailyMechPct',
            'dailyElecPct',
            'dailyMechMins',
            'dailyElecMins',
            'dailyMechCount',
            'dailyElecCount',
            'avgDailyMechPct',
            'avgDailyElecPct',
            'maxDailyMechPct',
            'maxDailyElecPct',
            'filterType',
            'month',
            'week',
            'selectedDate',
            'customStart',
            'customEnd',
            'startDate',
            'endDate',
            'periodLabel',
            'daysInPeriod',
            'kategori',
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
            'dbCosts',
            'totalCostVal',
            'dbMachineKpis',
            'dbActionPlans',
            'additionalKpiAvg',
            'weeksInMonth'
        ));
    }

    /**
     * Menghasilkan daftar minggu kalender (Senin - Minggu) untuk bulan yang dipilih.
     */
    public static function getWeeksOfMonth(string $yearMonth): array
    {
        $firstDay = \Carbon\Carbon::parse($yearMonth . '-01')->startOfDay();
        $lastDay = $firstDay->copy()->endOfMonth()->startOfDay();

        // Mulai dari hari Senin pada minggu yang memuat tanggal 1 bulan terpilih
        $curr = $firstDay->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $weeks = [];
        $w = 1;

        while ($curr->lte($lastDay)) {
            $start = $curr->copy();
            $end = $curr->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

            $weeks[] = [
                'week_num' => $w,
                'start'    => $start->format('Y-m-d'),
                'end'      => $end->format('Y-m-d'),
                'label'    => "Minggu {$w} (" . $start->format('d M') . " - " . $end->format('d M Y') . ")"
            ];

            $curr->addWeek();
            $w++;
        }

        return $weeks;
    }
}
