<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Maintenance\MtcAgendaModel;
use App\Models\Maintenance\MtcMainModel;
use App\Models\Maintenance\MtcMasterMaterialModel;
use App\Models\Maintenance\MtcMasterMesinModel;
use App\Models\Maintenance\MtcPenggantianMaterialModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MtcDashboardController extends Controller
{
    /**
     * Map jenis_mtc to inspection table and foreign key
     */
    private array $inspectionTables = [
        'Refrigerasi'     => ['table' => 'mtc_refrigerasi_inspections', 'foreign_key' => 'mesin_id'],
        'Motor Pompa'     => ['table' => 'mtc_motor_pump_inspections', 'foreign_key' => 'mesin_id'],
        'Utility'         => ['table' => 'mtc_utility_inspections', 'foreign_key' => 'mesin_id'],
        'Electrical'      => ['table' => 'mtc_electrical_inspections', 'foreign_key' => 'mesin_id'],
        'Electric Engine' => ['table' => 'mtc_electric_engine_inspections', 'foreign_key' => 'mesin_id'],
        'Diesel Engine'   => ['table' => 'mtc_diesel_engine_inspections', 'foreign_key' => 'mesin_id'],
        'Electric P2H'    => ['table' => 'mtc_electric_p2h_inspections', 'foreign_key' => 'no_unit'],
        'Diesel P2H'      => ['table' => 'mtc_diesel_p2h_inspections', 'foreign_key' => 'mesin_id'],
    ];

    /**
     * Render the Maintenance Dashboard view
     */
    public function index(Request $request)
    {
        $currentYear = Carbon::today()->year;
        $currentMonth = Carbon::today()->month;
        $currentWeek = Carbon::today()->weekOfYear;

        $weeksOptions = [];
        for ($w = 1; $w <= 52; $w++) {
            $wCarbon = Carbon::now()->setISODate($currentYear, $w);
            $wStart = (clone $wCarbon)->startOfWeek(Carbon::MONDAY);
            $wEnd = (clone $wCarbon)->endOfWeek(Carbon::SUNDAY);
            $weeksOptions[$w] = "W{$w} (" . $wStart->format('d M') . " - " . $wEnd->format('d M Y') . ")";
        }

        return view('dashboard.maintenance.dashboard_maintenance', compact(
            'currentYear',
            'currentMonth',
            'currentWeek',
            'weeksOptions'
        ));
    }

    /**
     * API: Get aggregated dashboard data based on filters
     * Supports modular async loading via ?section=schedule|reliability|cost|top_machines|unit_off|all
     * Filter types: 'weekly', 'monthly', 'custom'
     */
    public function getData(Request $request)
    {
        $filterType = $request->get('filter_type', 'weekly'); // weekly | monthly | custom
        $year = (int) $request->get('year', Carbon::today()->year);
        $month = (int) $request->get('month', Carbon::today()->month);
        $week = (int) $request->get('week', Carbon::today()->weekOfYear);
        $startDateStr = $request->get('start_date');
        $endDateStr = $request->get('end_date');
        $section = $request->get('section', 'all');

        // Resolve Date Range based on Filter
        $dateRange = $this->resolveDateRange($filterType, $year, $month, $week, $startDateStr, $endDateStr);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        $weeksList = $dateRange['weeks']; // Array of week objects with label, start, end

        $filterMeta = [
            'type' => $filterType,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'label' => $dateRange['label'],
            'operating_hours' => $dateRange['operating_hours'],
        ];

        // 1. Asynchronous Section: Schedule Maintenance
        if ($section === 'schedule') {
            $scheduleData = $this->getScheduleMaintenanceData($startDate, $endDate, $weeksList, $year, $month, $filterType);
            return response()->json([
                'status' => true,
                'section' => 'schedule',
                'filter_meta' => $filterMeta,
                'kpi' => [
                    'schedule_completion' => [
                        'value' => $scheduleData['kpi_completion_rate'],
                        'target' => 95.0,
                        'status' => $scheduleData['kpi_completion_rate'] >= 95.0 ? 'optimal' : 'warning',
                        'planned_count' => $scheduleData['kpi_total_planned'],
                        'completed_count' => $scheduleData['kpi_total_completed'],
                    ],
                ],
                'charts' => [
                    'schedule_maintenance' => $scheduleData['chart'],
                ],
            ]);
        }

        // 2. Asynchronous Section: Reliability, Breakdown, Downtime, MTTR, MTBF & Correlation
        if ($section === 'reliability' || $section === 'breakdown') {
            $breakdownData = $this->getBreakdownData($startDate, $endDate, $weeksList, $filterType);
            $downtimeData = $this->getDowntimeData($breakdownData, $weeksList, $dateRange['operating_hours'], $filterType);
            $mttrData = $this->getMttrData($breakdownData, $downtimeData['kpi_downtime_hours']);
            $mtbfData = $this->getMtbfData($breakdownData['kpi_breakdowns'], $breakdownData['kpi_records'], $dateRange['operating_hours']);
            $correlationData = $this->getMttrMtbfCorrelation($weeksList, $breakdownData, $downtimeData);

            return response()->json([
                'status' => true,
                'section' => 'reliability',
                'filter_meta' => $filterMeta,
                'kpi' => [
                    'total_breakdown' => [
                        'value' => $breakdownData['kpi_breakdowns'],
                        'target' => 5,
                        'status' => $breakdownData['kpi_breakdowns'] <= 5 ? 'optimal' : 'danger',
                        'subtext' => 'Paket Korektif (Target <5x)',
                    ],
                    'downtime' => [
                        'percentage' => $downtimeData['kpi_downtime_percentage'],
                        'hours' => $downtimeData['kpi_downtime_hours'],
                        'target_percentage' => 2.0,
                        'target_hours' => 12.5,
                        'status' => $downtimeData['kpi_downtime_percentage'] <= 2.0 ? 'optimal' : 'danger',
                        'subtext' => 'Target <2% dari 624 Jam (Max 12.5 Jam)',
                    ],
                    'mttr' => [
                        'value' => $mttrData['value'],
                        'unit' => 'Jam',
                        'target' => 3.0,
                        'status' => $mttrData['value'] <= 3.0 ? 'optimal' : 'warning',
                        'subtext' => 'Mean Time To Repair',
                    ],
                    'mtbf' => [
                        'value' => $mtbfData['value'],
                        'unit' => 'Jam',
                        'target' => 250,
                        'status' => $mtbfData['value'] >= 250 ? 'optimal' : 'danger',
                        'subtext' => 'Avg ((624 - MTTR) / Korektif)',
                    ],
                ],
                'charts' => [
                    'total_breakdown' => $breakdownData['chart'],
                    'total_downtime' => $downtimeData['chart'],
                    'mttr_mtbf_correlation' => $correlationData,
                ],
            ]);
        }

        // 3. Asynchronous Section: Maintenance Cost
        if ($section === 'cost') {
            $costData = $this->getCostMaintenanceData($startDate, $endDate, $weeksList, $filterType);
            return response()->json([
                'status' => true,
                'section' => 'cost',
                'filter_meta' => $filterMeta,
                'kpi' => [
                    'cost' => [
                        'value' => $costData['total_cost_formatted'],
                        'raw_value' => $costData['total_cost'],
                        'budget' => $costData['budget_formatted'],
                        'budget_utilization' => $costData['budget_utilization'],
                        'subtext' => 'Total Maintenance Cost',
                    ],
                ],
                'charts' => [
                    'cost_maintenance' => $costData['chart'],
                ],
            ]);
        }

        // 4. Asynchronous Section: Top Machines (MTBF, MTTR, Cost)
        if ($section === 'top_machines') {
            $topMachinesData = $this->getTopMachinesAnalytics($startDate, $endDate, $dateRange['operating_hours']);
            return response()->json([
                'status' => true,
                'section' => 'top_machines',
                'filter_meta' => $filterMeta,
                'tables' => [
                    'top_mtbf' => $topMachinesData['top_mtbf'],
                    'top_mttr' => $topMachinesData['top_mttr'],
                    'top_cost' => $topMachinesData['top_cost'],
                ],
            ]);
        }

        // 5. Asynchronous Section: Unit Off (Donut & Live Offline Units)
        if ($section === 'unit_off') {
            $unitOffData = $this->getUnitOffData($startDate, $endDate);
            return response()->json([
                'status' => true,
                'section' => 'unit_off',
                'filter_meta' => $filterMeta,
                'charts' => [
                    'unit_off' => $unitOffData['chart'],
                ],
                'tables' => [
                    'unit_off_list' => $unitOffData['offline_units'],
                ],
            ]);
        }

        // Fallback: Compute all if section is 'all' or not specified
        $scheduleData = $this->getScheduleMaintenanceData($startDate, $endDate, $weeksList, $year, $month, $filterType);
        $breakdownData = $this->getBreakdownData($startDate, $endDate, $weeksList, $filterType);
        $downtimeData = $this->getDowntimeData($breakdownData, $weeksList, $dateRange['operating_hours'], $filterType);
        $mttrData = $this->getMttrData($breakdownData, $downtimeData['kpi_downtime_hours']);
        $mtbfData = $this->getMtbfData($breakdownData['kpi_breakdowns'], $breakdownData['kpi_records'], $dateRange['operating_hours']);
        $costData = $this->getCostMaintenanceData($startDate, $endDate, $weeksList, $filterType);
        $topMachinesData = $this->getTopMachinesAnalytics($startDate, $endDate, $dateRange['operating_hours']);
        $unitOffData = $this->getUnitOffData($startDate, $endDate);
        $correlationData = $this->getMttrMtbfCorrelation($weeksList, $breakdownData, $downtimeData);

        return response()->json([
            'status' => true,
            'filter_meta' => $filterMeta,
            'kpi' => [
                'schedule_completion' => [
                    'value' => $scheduleData['kpi_completion_rate'],
                    'target' => 95.0,
                    'status' => $scheduleData['kpi_completion_rate'] >= 95.0 ? 'optimal' : 'warning',
                    'planned_count' => $scheduleData['kpi_total_planned'],
                    'completed_count' => $scheduleData['kpi_total_completed'],
                ],
                'total_breakdown' => [
                    'value' => $breakdownData['kpi_breakdowns'],
                    'target' => 5,
                    'status' => $breakdownData['kpi_breakdowns'] <= 5 ? 'optimal' : 'danger',
                    'subtext' => 'Paket Korektif (Target <5x)',
                ],
                'downtime' => [
                    'percentage' => $downtimeData['kpi_downtime_percentage'],
                    'hours' => $downtimeData['kpi_downtime_hours'],
                    'target_percentage' => 2.0,
                    'target_hours' => 12.5,
                    'status' => $downtimeData['kpi_downtime_percentage'] <= 2.0 ? 'optimal' : 'danger',
                    'subtext' => 'Target <2% dari 624 Jam (Max 12.5 Jam)',
                ],
                'mttr' => [
                    'value' => $mttrData['value'],
                    'unit' => 'Jam',
                    'target' => 3.0,
                    'status' => $mttrData['value'] <= 3.0 ? 'optimal' : 'warning',
                    'subtext' => 'Mean Time To Repair',
                ],
                'mtbf' => [
                    'value' => $mtbfData['value'],
                    'unit' => 'Jam',
                    'target' => 250,
                    'status' => $mtbfData['value'] >= 250 ? 'optimal' : 'danger',
                    'subtext' => 'Avg ((624 - MTTR) / Korektif)',
                ],
                'cost' => [
                    'value' => $costData['total_cost_formatted'],
                    'raw_value' => $costData['total_cost'],
                    'budget' => $costData['budget_formatted'],
                    'budget_utilization' => $costData['budget_utilization'],
                    'subtext' => 'Total Maintenance Cost',
                ],
            ],
            'charts' => [
                'schedule_maintenance' => $scheduleData['chart'],
                'total_breakdown' => $breakdownData['chart'],
                'total_downtime' => $downtimeData['chart'],
                'unit_off' => $unitOffData['chart'],
                'mttr_mtbf_correlation' => $correlationData,
                'cost_maintenance' => $costData['chart'],
            ],
            'tables' => [
                'top_mtbf' => $topMachinesData['top_mtbf'],
                'top_mttr' => $topMachinesData['top_mttr'],
                'top_cost' => $topMachinesData['top_cost'],
                'unit_off_list' => $unitOffData['offline_units'],
            ],
        ]);
    }

    /**
     * Resolve date range and list of weeks based on filter selection
     */
    private function resolveDateRange($filterType, $year, $month, $week, $startDateStr, $endDateStr): array
    {
        $now = Carbon::today();

        if ($filterType === 'weekly') {
            // Selected week: Senin to Minggu
            $carbon = Carbon::now()->setISODate($year, $week);
            $start = (clone $carbon)->startOfWeek(Carbon::MONDAY);
            $end = (clone $carbon)->endOfWeek(Carbon::SUNDAY);

            // Display a surrounding 5-week context (4 preceding weeks + selected week) labeled consistently as W1 - W5
            $weeks = [];
            for ($i = 4; $i >= 0; $i--) {
                $wStart = (clone $start)->subWeeks($i);
                $wEnd = (clone $wStart)->endOfWeek(Carbon::SUNDAY);
                $stepIdx = 5 - $i; // Always 1, 2, 3, 4, 5
                $wNum = (int) $wStart->format('W');
                $dateLabel = $wStart->format('d M') . " - " . $wEnd->format('d M Y');
                $weeks[] = [
                    'key' => "W{$stepIdx}",
                    'label' => $dateLabel,
                    'short_label' => "W{$stepIdx}",
                    'start' => $wStart->copy(),
                    'end' => $wEnd->copy(),
                    'display' => "W{$stepIdx} ({$dateLabel})",
                    'week_iso' => "W{$wNum}",
                    'is_selected' => ($i === 0),
                ];
            }

            return [
                'start' => $start,
                'end' => $end,
                'weeks' => $weeks,
                'operating_hours' => 624, // 624 hours company standard reference
                'label' => "Minggu ke-{$week} ({$start->format('d M Y')} - {$end->format('d M Y')})",
            ];
        }

        if ($filterType === 'monthly') {
            $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $end = (clone $start)->endOfMonth()->endOfDay();
            $daysInMonth = $start->daysInMonth;

            // Divide month into standard operational weeks with date range labels
            $w1Start = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $w1End = Carbon::createFromDate($year, $month, 7)->endOfDay();
            $w2Start = Carbon::createFromDate($year, $month, 8)->startOfDay();
            $w2End = Carbon::createFromDate($year, $month, 14)->endOfDay();
            $w3Start = Carbon::createFromDate($year, $month, 15)->startOfDay();
            $w3End = Carbon::createFromDate($year, $month, 21)->endOfDay();
            $w4Start = Carbon::createFromDate($year, $month, 22)->startOfDay();
            $w4End = Carbon::createFromDate($year, $month, 28)->endOfDay();

            $weeks = [
                [
                    'key' => 'W1',
                    'label' => $w1Start->format('d M') . ' - ' . $w1End->format('d M Y'),
                    'short_label' => 'W1',
                    'start' => $w1Start,
                    'end' => $w1End,
                    'display' => "W1 (" . $w1Start->format('d M') . " - " . $w1End->format('d M Y') . ")",
                ],
                [
                    'key' => 'W2',
                    'label' => $w2Start->format('d M') . ' - ' . $w2End->format('d M Y'),
                    'short_label' => 'W2',
                    'start' => $w2Start,
                    'end' => $w2End,
                    'display' => "W2 (" . $w2Start->format('d M') . " - " . $w2End->format('d M Y') . ")",
                ],
                [
                    'key' => 'W3',
                    'label' => $w3Start->format('d M') . ' - ' . $w3End->format('d M Y'),
                    'short_label' => 'W3',
                    'start' => $w3Start,
                    'end' => $w3End,
                    'display' => "W3 (" . $w3Start->format('d M') . " - " . $w3End->format('d M Y') . ")",
                ],
                [
                    'key' => 'W4',
                    'label' => $w4Start->format('d M') . ' - ' . $w4End->format('d M Y'),
                    'short_label' => 'W4',
                    'start' => $w4Start,
                    'end' => $w4End,
                    'display' => "W4 (" . $w4Start->format('d M') . " - " . $w4End->format('d M Y') . ")",
                ],
            ];

            if ($daysInMonth > 28) {
                $w5Start = Carbon::createFromDate($year, $month, 29)->startOfDay();
                $w5End = Carbon::createFromDate($year, $month, $daysInMonth)->endOfDay();
                $weeks[] = [
                    'key' => 'W5',
                    'label' => $w5Start->format('d M') . ' - ' . $w5End->format('d M Y'),
                    'short_label' => 'W5',
                    'start' => $w5Start,
                    'end' => $w5End,
                    'display' => "W5 (" . $w5Start->format('d M') . " - " . $w5End->format('d M Y') . ")",
                ];
            }

            return [
                'start' => $start,
                'end' => $end,
                'weeks' => $weeks,
                'operating_hours' => 624, // Standard 26 days * 24h = 624 hours as specified
                'label' => $start->translatedFormat('F Y'),
            ];
        }

        // Custom Date Range
        $start = !empty($startDateStr) ? Carbon::parse($startDateStr)->startOfDay() : Carbon::today()->subDays(30)->startOfDay();
        $end = !empty($endDateStr) ? Carbon::parse($endDateStr)->endOfDay() : Carbon::today()->endOfDay();

        // Slice custom date range into weekly chunks (up to 12 weeks)
        $weeks = [];
        $tempStart = $start->copy();
        $idx = 1;
        while ($tempStart->lte($end)) {
            $tempEnd = (clone $tempStart)->addDays(6)->endOfDay();
            if ($tempEnd->gt($end)) {
                $tempEnd = $end->copy();
            }
            $dateLabel = $tempStart->format('d M') . " - " . $tempEnd->format('d M Y');
            $weeks[] = [
                'key' => "W{$idx}",
                'label' => $dateLabel,
                'short_label' => "W{$idx}",
                'start' => $tempStart->copy(),
                'end' => $tempEnd->copy(),
                'display' => "W{$idx} ({$dateLabel})",
            ];
            $tempStart = (clone $tempEnd)->addSecond()->startOfDay();
            $idx++;
            if ($idx > 12) break; // cap at 12 slices
        }

        return [
            'start' => $start,
            'end' => $end,
            'weeks' => $weeks,
            'operating_hours' => 624,
            'label' => "{$start->format('d M Y')} - {$end->format('d M Y')}",
        ];
    }

    /**
     * Compute Schedule Maintenance Completion data
     * Xaxis: per Unit (MHE, Motor Pompa, Utility, Sipil)
     * Series: per Week (W1, W2, W3... sesuai filter)
     */
    private function getScheduleMaintenanceData($startDate, $endDate, array $weeksList, int $year, int $month, string $filterType = 'weekly'): array
    {
        // Spanning seluruh weeksList agar filter weekly menampilkan data minggu-minggu sebelumnya
        $queryStart = count($weeksList) > 0 ? $weeksList[0]['start']->copy()->startOfDay() : $startDate->copy()->startOfDay();
        $queryEnd = count($weeksList) > 0 ? end($weeksList)['end']->copy()->endOfDay() : $endDate->copy()->endOfDay();

        $startYear = (int) $queryStart->year;
        $startMonth = (int) $queryStart->month;
        $endYear = (int) $queryEnd->year;
        $endMonth = (int) $queryEnd->month;

        // 1. Query agenda records from mtc_agenda
        $agendaRecords = DB::table('mtc_agenda')
            ->join('mtc_master_mesin', 'mtc_master_mesin.id', '=', 'mtc_agenda.mesin_id')
            ->where('mtc_master_mesin.aktif', true)
            ->where(function ($q) use ($queryStart, $queryEnd, $startYear, $startMonth, $endYear, $endMonth) {
                $q->whereBetween('mtc_agenda.tanggal', [$queryStart->format('Y-m-d'), $queryEnd->format('Y-m-d')])
                    ->orWhere(function ($sub) use ($startYear, $startMonth, $endYear, $endMonth) {
                        $sub->whereNull('mtc_agenda.tanggal')
                            ->whereRaw('(mtc_agenda.tahun * 100 + mtc_agenda.bulan) BETWEEN ? AND ?', [
                                $startYear * 100 + $startMonth,
                                $endYear * 100 + $endMonth
                            ]);
                    });
            })
            ->select('mtc_agenda.*', 'mtc_master_mesin.jenis_mtc', 'mtc_master_mesin.nama_mesin', 'mtc_master_mesin.kode_mesin', 'mtc_master_mesin.lokasi as mesin_lokasi')
            ->get();

        // 2. Query completed maintenance form records in date range (exclude Korektif)
        $mtcStart = $queryStart->copy()->startOfMonth()->format('Y-m-d');
        $mtcEnd = $queryEnd->copy()->endOfMonth()->format('Y-m-d');

        $mainRecords = MtcMainModel::with([
            'motorPump.mesin',
            'utility.mesin',
            'electrical.mesin',
            'refrigerasi.mesin',
            'electricEngine.mesin',
            'dieselEngine.mesin',
            'electricP2h.mesin',
            'dieselP2h.mesin',
            'gensetP2h.mesin',
            'battery'
        ])
            ->whereBetween('tanggal', [$mtcStart, $mtcEnd])
            ->where(function ($q) {
                $q->whereNull('paket')->orWhereRaw("LOWER(TRIM(paket)) NOT LIKE '%korektif%'");
            })
            ->get();

        // Master mesin map untuk fallback matching by name/code
        $allMesin = DB::table('mtc_master_mesin')->where('aktif', true)->get();
        $mesinByName = [];
        $mesinByCode = [];
        foreach ($allMesin as $m) {
            if ($m->nama_mesin) $mesinByName[trim(strtolower($m->nama_mesin))] = $m;
            if ($m->kode_mesin) $mesinByCode[trim(strtolower($m->kode_mesin))] = $m;
        }

        // Pre-parse mtc_main records
        $parsedMains = [];
        foreach ($mainRecords as $main) {
            $mObj = null;
            foreach (['motorPump', 'utility', 'electrical', 'refrigerasi', 'electricEngine', 'dieselEngine', 'electricP2h', 'dieselP2h', 'gensetP2h'] as $rel) {
                if ($main->$rel && $main->$rel->mesin) {
                    $mObj = $main->$rel->mesin;
                    break;
                }
            }
            if (!$mObj && !empty($main->area)) {
                $k = trim(strtolower($main->area));
                $mObj = $mesinByName[$k] ?? ($mesinByCode[$k] ?? null);
            }

            $tglObj = $main->tanggal ? Carbon::parse($main->tanggal) : null;

            $parsedMains[] = [
                'id' => $main->id,
                'mesin_id' => $mObj ? $mObj->id : null,
                'nama_mesin' => $mObj ? $mObj->nama_mesin : '',
                'kode_mesin' => $mObj ? $mObj->kode_mesin : '',
                'area' => trim($main->area ?? ''),
                'jenis_mtc' => $main->jenis_mtc,
                'paket' => trim($main->paket ?? ''),
                'tanggal' => $tglObj ? $tglObj->format('Y-m-d') : null,
                'year' => $tglObj ? (int)$tglObj->year : null,
                'month' => $tglObj ? (int)$tglObj->month : null,
            ];
        }

        $units = ['MHE', 'Motor Pompa', 'Utility', 'Sipil'];
        $series = [];
        $weekColors = ['#38bdf8', '#10b981', '#f59e0b', '#a855f7', '#06b6d4', '#ec4899', '#6366f1'];

        // Cek apakah mode monthly filter (W1: 1-7, W2: 8-14, dst)
        $isMonthlyFilter = count($weeksList) === 5 && $weeksList[0]['start']->day === 1 && $weeksList[0]['end']->day === 7;

        $weekPlannedData = [];
        $weekCompletedData = [];
        $totalPlannedPerUnit = array_fill_keys($units, 0);
        $totalCompletedPerUnit = array_fill_keys($units, 0);
        $totalPlannedOverall = 0;
        $totalCompletedOverall = 0;
        $usedMainIds = [];

        foreach ($weeksList as $idx => $wk) {
            $wStart = $wk['start']->format('Y-m-d');
            $wEnd = $wk['end']->format('Y-m-d');
            $wMingguKe = $idx + 1;

            // Filter agenda untuk minggu ini
            $wAgendas = $agendaRecords->filter(function ($item) use ($wStart, $wEnd, $wMingguKe, $isMonthlyFilter, $wk) {
                if ($item->tanggal !== null) {
                    $tglObj = Carbon::parse($item->tanggal);
                    if ($isMonthlyFilter) {
                        $day = $tglObj->day;
                        $computedWk = ($day <= 7) ? 1 : (($day <= 14) ? 2 : (($day <= 21) ? 3 : (($day <= 28) ? 4 : 5)));
                        return $computedWk === $wMingguKe
                            && (int)$tglObj->month === (int)$wk['start']->month
                            && (int)$tglObj->year === (int)$wk['start']->year;
                    } else {
                        $tgl = $tglObj->format('Y-m-d');
                        return $tgl >= $wStart && $tgl <= $wEnd;
                    }
                } else {
                    if ($isMonthlyFilter) {
                        return (int)$item->minggu_ke === $wMingguKe
                            && (int)$item->bulan === (int)$wk['start']->month
                            && (int)$item->tahun === (int)$wk['start']->year;
                    } else {
                        // Date range of (tahun, bulan, minggu_ke) as standard 7-day buckets
                        $daysInMonth = Carbon::createFromDate($item->tahun, $item->bulan, 1)->daysInMonth;
                        $dStart = ($item->minggu_ke - 1) * 7 + 1;
                        $dEnd = $item->minggu_ke == 5 ? $daysInMonth : min($daysInMonth, $item->minggu_ke * 7);
                        $midDay = (int) floor(($dStart + $dEnd) / 2);
                        $midDate = Carbon::createFromDate($item->tahun, $item->bulan, min($daysInMonth, $midDay))->startOfDay();
                        return $midDate->betweenIncluded($wk['start'], $wk['end']);
                    }
                }
            });

            // Group planned machines strictly per unique machine to avoid duplicate count on multi-package
            $plannedMachines = [
                'MHE'         => [],
                'Motor Pompa' => [],
                'Utility'     => [],
                'Sipil'       => []
            ];

            foreach ($wAgendas as $ag) {
                $cat = $this->normalizeJenisCategory($ag->jenis_mtc);
                if (!isset($plannedMachines[$cat])) {
                    $cat = 'Utility';
                }

                $agYear = !empty($ag->tahun) ? (int)$ag->tahun : ($ag->tanggal ? (int)Carbon::parse($ag->tanggal)->year : (int)$wk['start']->year);
                $agMonth = !empty($ag->bulan) ? (int)$ag->bulan : ($ag->tanggal ? (int)Carbon::parse($ag->tanggal)->month : (int)$wk['start']->month);

                // Use mesin_id as primary key to group plans per machine
                if (!isset($plannedMachines[$cat][$ag->mesin_id])) {
                    $plannedMachines[$cat][$ag->mesin_id] = [
                        'mesin_id'   => $ag->mesin_id,
                        'nama_mesin' => trim(strtolower($ag->nama_mesin ?? '')),
                        'kode_mesin' => trim(strtolower($ag->kode_mesin ?? '')),
                        'paket'      => trim(strtolower($ag->paket ?? '')),
                        'tahun'      => $agYear,
                        'bulan'      => $agMonth,
                    ];
                }
            }

            $agendaCounts = array_fill_keys($units, 0);
            $completedCounts = array_fill_keys($units, 0);

            foreach ($units as $u) {
                $agendaCounts[$u] = count($plannedMachines[$u]);

                foreach ($plannedMachines[$u] as $agMesinId => $agInfo) {
                    $agYear  = $agInfo['tahun'];
                    $agMonth = $agInfo['bulan'];
                    $agNama  = $agInfo['nama_mesin'];
                    $agKode  = $agInfo['kode_mesin'];
                    $agPaket = $agInfo['paket'];

                    // Pass 1: Strict Machine Match + Paket Match
                    $isDone = false;
                    foreach ($parsedMains as $pm) {
                        if (in_array($pm['id'], $usedMainIds)) continue;

                        // Harus di bulan & tahun yang sama dengan jadwal agenda
                        if ($pm['year'] !== $agYear || $pm['month'] !== $agMonth) continue;

                        // Strict Machine Match:
                        $machineMatches = false;
                        if ($pm['mesin_id'] !== null && $pm['mesin_id'] === $agMesinId) {
                            $machineMatches = true;
                        } elseif ($agKode !== '' && strcasecmp($agKode, $pm['kode_mesin']) === 0) {
                            $machineMatches = true;
                        } elseif ($agNama !== '' && strcasecmp($agNama, $pm['nama_mesin']) === 0) {
                            $machineMatches = true;
                        } elseif ($u === 'Sipil') {
                            if ($agNama !== '' && strcasecmp($agNama, $pm['area']) === 0) {
                                $machineMatches = true;
                            } elseif ($agKode !== '' && strcasecmp($agKode, $pm['area']) === 0) {
                                $machineMatches = true;
                            }
                        }

                        if (!$machineMatches) continue;

                        // Match paket:
                        $pmPaket = trim(strtolower($pm['paket']));
                        $paketMatches = false;
                        if ($agPaket === '' || $pmPaket === '') {
                            $paketMatches = true;
                        } elseif ($agPaket === $pmPaket) {
                            $paketMatches = true;
                        } elseif ($pmPaket === 'maintenance' || $agPaket === 'maintenance') {
                            $paketMatches = true;
                        }

                        if ($paketMatches) {
                            $usedMainIds[] = $pm['id'];
                            $isDone = true;
                            break;
                        }
                    }

                    // Pass 2: Fallback ke realisasi mesin yang sama di bulan & tahun tersebut (sesuai Dashboard Agenda)
                    if (!$isDone) {
                        foreach ($parsedMains as $pm) {
                            if (in_array($pm['id'], $usedMainIds)) continue;
                            if ($pm['year'] !== $agYear || $pm['month'] !== $agMonth) continue;

                            $machineMatches = false;
                            if ($pm['mesin_id'] !== null && $pm['mesin_id'] === $agMesinId) {
                                $machineMatches = true;
                            } elseif ($agKode !== '' && strcasecmp($agKode, $pm['kode_mesin']) === 0) {
                                $machineMatches = true;
                            } elseif ($agNama !== '' && strcasecmp($agNama, $pm['nama_mesin']) === 0) {
                                $machineMatches = true;
                            } elseif ($u === 'Sipil') {
                                if ($agNama !== '' && strcasecmp($agNama, $pm['area']) === 0) {
                                    $machineMatches = true;
                                } elseif ($agKode !== '' && strcasecmp($agKode, $pm['area']) === 0) {
                                    $machineMatches = true;
                                }
                            }

                            if ($machineMatches) {
                                $usedMainIds[] = $pm['id'];
                                $isDone = true;
                                break;
                            }
                        }
                    }

                    if ($isDone) {
                        $completedCounts[$u]++;
                    }
                }
            }

            $weekPlannedData[$idx] = [];
            $weekCompletedData[$idx] = [];

            foreach ($units as $u) {
                $pVal = $agendaCounts[$u];
                $cVal = $completedCounts[$u];

                $weekPlannedData[$idx][$u] = $pVal;
                $weekCompletedData[$idx][$u] = $cVal;
                $totalPlannedPerUnit[$u] += $pVal;
                $totalCompletedPerUnit[$u] += $cVal;
                $totalPlannedOverall += $pVal;
                $totalCompletedOverall += $cVal;
            }
        }

        // Pass 2: Distribute percentage using Largest Remainder Method per Unit
        // This guarantees that the sum of stacked weeks matches targetTotalPct exactly and NEVER exceeds 100%
        $weeklyPercentages = []; // [$idx][$u] => float

        foreach ($units as $u) {
            $unitTotalPlanned = $totalPlannedPerUnit[$u];
            $unitTotalCompleted = $totalCompletedPerUnit[$u];

            if ($unitTotalPlanned <= 0) {
                foreach ($weeksList as $idx => $wk) {
                    $weeklyPercentages[$idx][$u] = 0.0;
                }
                continue;
            }

            $targetTotalPct = min(100.0, round(($unitTotalCompleted / $unitTotalPlanned) * 100, 1));

            $floors = [];
            $remainders = [];
            $sumFloors = 0.0;

            foreach ($weeksList as $idx => $wk) {
                $cVal = $weekCompletedData[$idx][$u];
                $exactPct = ($cVal / $unitTotalPlanned) * 100;
                $floorVal = floor($exactPct * 10) / 10;
                $floors[$idx] = $floorVal;
                $remainders[$idx] = $exactPct - $floorVal;
                $sumFloors += $floorVal;
            }

            $diffTenths = (int) round(($targetTotalPct - $sumFloors) * 10);
            if ($diffTenths > 0) {
                arsort($remainders);
                foreach (array_keys($remainders) as $idx) {
                    if ($diffTenths <= 0) break;
                    $floors[$idx] = round($floors[$idx] + 0.1, 1);
                    $diffTenths--;
                }
            } elseif ($diffTenths < 0) {
                asort($remainders);
                foreach (array_keys($remainders) as $idx) {
                    if ($diffTenths >= 0) break;
                    $floors[$idx] = max(0.0, round($floors[$idx] - 0.1, 1));
                    $diffTenths++;
                }
            }

            foreach ($weeksList as $idx => $wk) {
                $weeklyPercentages[$idx][$u] = $floors[$idx];
            }
        }

        // Pass 3: Assemble series for ApexCharts
        foreach ($weeksList as $idx => $wk) {
            $wShort = !empty($wk['short_label']) ? $wk['short_label'] : "W" . ($idx + 1);
            $wFull = (!empty($wk['short_label']) ? $wk['short_label'] . ' (' . $wk['label'] . ')' : $wk['label']);

            $pctData = [];
            $completedUnits = [];
            $plannedUnits = [];

            foreach ($units as $u) {
                $pctData[] = $weeklyPercentages[$idx][$u];
                $completedUnits[] = $weekCompletedData[$idx][$u];
                $plannedUnits[] = $weekPlannedData[$idx][$u];
            }

            $series[] = [
                'name'          => $wShort,
                'short_name'    => $wShort,
                'date_range'    => $wk['label'],
                'full_name'     => $wFull,
                'data'          => $pctData,
                'real_units'    => $completedUnits,
                'planned_units' => $plannedUnits,
                'color'         => $weekColors[$idx % count($weekColors)],
            ];
        }

        $overallRate = $totalPlannedOverall > 0
            ? min(100.0, round(($totalCompletedOverall / $totalPlannedOverall) * 100, 1))
            : 0.0;

        return [
            'kpi_completion_rate'   => $overallRate,
            'kpi_total_planned'     => $totalPlannedOverall,
            'kpi_total_completed'   => $totalCompletedOverall,
            'total_planned_by_unit' => $totalPlannedPerUnit,
            'total_completed_by_unit' => $totalCompletedPerUnit,
            'chart' => [
                'categories' => $units, // ['MHE', 'Motor Pompa', 'Utility', 'Sipil']
                'series'     => $series,
            ],
        ];
    }

    /**
     * Normalize category to MHE, Motor Pompa, Sipil, Utility
     */
    private function normalizeJenisCategory($jenis): string
    {
        $jenis = trim($jenis ?? '');
        if (
            in_array($jenis, ['Diesel Engine', 'Electric Engine'])
        ) {
            return 'MHE';
        }
        if (stripos($jenis, 'Pompa') !== false || stripos($jenis, 'Pump') !== false) {
            return 'Motor Pompa';
        }
        if (stripos($jenis, 'Sipil') !== false) {
            return 'Sipil';
        }
        return 'Utility';
    }

    /**
     * Compute Breakdown data (Total maintenance with paket = 'Korektif')
     */
    private function getBreakdownData($startDate, $endDate, array $weeksList, string $filterType): array
    {
        $chartStart = count($weeksList) > 0 ? $weeksList[0]['start']->format('Y-m-d') : $startDate->format('Y-m-d');
        $chartEnd = count($weeksList) > 0 ? end($weeksList)['end']->format('Y-m-d') : $endDate->format('Y-m-d');

        // Query strictly records where paket is 'Korektif' with machine relationships
        $korektifRecords = MtcMainModel::with([
            'motorPump.mesin',
            'utility.mesin',
            'electrical.mesin',
            'refrigerasi.mesin',
            'electricEngine.mesin',
            'dieselEngine.mesin',
            'electricP2h.mesin',
            'dieselP2h.mesin',
            'gensetP2h.mesin',
            'battery',
        ])
            ->whereRaw("LOWER(TRIM(COALESCE(paket, ''))) LIKE '%korektif%'")
            ->whereBetween('tanggal', [$chartStart, $chartEnd])
            ->get();

        $chartCategories = [];
        $dateRanges = [];
        $categoryDetails = [];
        $seriesBreakdown = [];
        $totalPeriodBreakdowns = 0;
        $selectedWeekBreakdowns = 0;
        $selectedWeekRecords = collect([]);

        foreach ($weeksList as $idx => $wk) {
            $wStart = $wk['start']->format('Y-m-d');
            $wEnd = $wk['end']->format('Y-m-d');
            $wShort = !empty($wk['short_label']) ? $wk['short_label'] : $wk['key'];
            $chartCategories[] = $wShort;
            $dateRanges[] = $wk['label'];
            $categoryDetails[] = !empty($wk['display']) ? $wk['display'] : "{$wShort} ({$wk['label']})";

            $wRecords = $korektifRecords->filter(function ($item) use ($wStart, $wEnd) {
                return $item->tanggal >= $wStart && $item->tanggal <= $wEnd;
            });

            $realCount = $wRecords->count();
            $seriesBreakdown[] = $realCount;
            $totalPeriodBreakdowns += $realCount;

            if (!empty($wk['is_selected'])) {
                $selectedWeekBreakdowns = $realCount;
                $selectedWeekRecords = $wRecords;
            }
        }

        // For weekly filter, KPI displays the breakdown of the selected week; for monthly/custom, total of period
        $kpiBreakdowns = ($filterType === 'weekly') ? $selectedWeekBreakdowns : $totalPeriodBreakdowns;
        $kpiRecords = ($filterType === 'weekly') ? $selectedWeekRecords : $korektifRecords->filter(function ($item) use ($startDate, $endDate) {
            return $item->tanggal >= $startDate->format('Y-m-d') && $item->tanggal <= $endDate->format('Y-m-d');
        });

        return [
            'kpi_breakdowns' => $kpiBreakdowns,
            'kpi_records' => $kpiRecords,
            'total_breakdowns' => $totalPeriodBreakdowns,
            'records' => $korektifRecords,
            'chart' => [
                'categories' => $chartCategories,
                'date_ranges' => $dateRanges,
                'category_details' => $categoryDetails,
                'series' => [
                    [
                        'name' => 'Breakdown Frequency',
                        'data' => $seriesBreakdown,
                        'color' => '#f43f5e', // Crimson Rose
                    ],
                ],
                'target' => 5,
            ],
        ];
    }

    /**
     * Compute Downtime data: jam mulai sampai jam selesai dari paket korektif
     * Rumus Downtime persentase: Total jam korektif / 624 * 100
     * Target < 2% dari 624 jam
     */
    private function getDowntimeData(array $breakdownData, array $weeksList, float $operatingHours, string $filterType): array
    {
        $chartCategories = [];
        $dateRanges = [];
        $categoryDetails = [];
        $seriesDowntimePct = [];
        $seriesDowntimeHours = [];
        $totalDowntimeHours = 0;
        $selectedWeekHours = 0;
        $selectedWeekPct = 0;

        $targetPct = 2.0;
        $baseHours = 624.0; // Rumus resmi perusahaan: Total jam korektif / 624 * 100

        foreach ($weeksList as $idx => $wk) {
            $wShort = !empty($wk['short_label']) ? $wk['short_label'] : $wk['key'];
            $chartCategories[] = $wShort;
            $dateRanges[] = $wk['label'];
            $categoryDetails[] = !empty($wk['display']) ? $wk['display'] : "{$wShort} ({$wk['label']})";

            $wRecords = $breakdownData['records']->filter(function ($item) use ($wk) {
                $wStart = $wk['start']->format('Y-m-d');
                $wEnd = $wk['end']->format('Y-m-d');
                return $item->tanggal >= $wStart && $item->tanggal <= $wEnd;
            });

            $weekHours = 0;
            foreach ($wRecords as $rec) {
                if (!empty($rec->waktu_mulai) && !empty($rec->waktu_selesai)) {
                    $startT = Carbon::parse($rec->waktu_mulai);
                    $endT = Carbon::parse($rec->waktu_selesai);
                    $durHours = max(0.1, $startT->diffInMinutes($endT) / 60);
                    $weekHours += $durHours;
                }
            }

            // If there were breakdown records but waktu_mulai/waktu_selesai was not filled, estimate 1.5h per event
            if ($wRecords->count() > 0 && $weekHours === 0) {
                $weekHours = round($wRecords->count() * 1.5, 1);
            }

            // Rumus Downtime persentase: Total jam korektif / 624 * 100
            $pct = round(($weekHours / $baseHours) * 100, 2);

            $seriesDowntimeHours[] = round($weekHours, 1);
            $seriesDowntimePct[] = $pct;
            $totalDowntimeHours += $weekHours;

            if (!empty($wk['is_selected'])) {
                $selectedWeekHours = round($weekHours, 1);
                $selectedWeekPct = $pct;
            }
        }

        // Overall Downtime persentase: Total jam korektif / 624 * 100
        $kpiHours = ($filterType === 'weekly') ? $selectedWeekHours : round($totalDowntimeHours, 1);
        $kpiPct   = round(($kpiHours / $baseHours) * 100, 2);
        $overallPct = round(($totalDowntimeHours / $baseHours) * 100, 2);

        return [
            'kpi_downtime_hours' => $kpiHours,
            'kpi_downtime_percentage' => $kpiPct,
            'total_downtime_hours' => round($totalDowntimeHours, 1),
            'total_downtime_percentage' => $overallPct,
            'avg_percentage' => $overallPct,
            'chart' => [
                'categories' => $chartCategories,
                'date_ranges' => $dateRanges,
                'category_details' => $categoryDetails,
                'series' => [
                    [
                        'name' => 'Downtime (%)',
                        'data' => $seriesDowntimePct,
                        'color' => '#10b981', // Mint Emerald
                    ],
                ],
                'hours_series' => $seriesDowntimeHours,
                'target' => $targetPct,
                'average' => $overallPct,
            ],
        ];
    }

    /**
     * Compute MTTR: waktu mulai dan waktu selesai lalu di-average
     */
    private function getMttrData(array $breakdownData, float $downtimeHours): array
    {
        $breakdowns = $breakdownData['kpi_breakdowns'];
        if ($breakdowns <= 0) {
            return [
                'value' => 0.0,
            ];
        }

        $mttr = round($downtimeHours / $breakdowns, 1);
        return [
            'value' => (float) max(0.1, $mttr),
        ];
    }

    /**
     * Compute MTBF for KPI Card:
     * Average of unit MTBFs: (624 - total MTTR/downtime unit) / berapa kali korektif unit itu
     */
    private function getMtbfData(int $kpiBreakdowns, $kpiRecords = null, float $baseHours = 624.0): array
    {
        if ($kpiBreakdowns <= 0 || !$kpiRecords || $kpiRecords->count() === 0) {
            return [
                'value' => (int) $baseHours,
            ];
        }

        $avgMtbf = $this->computeAverageMtbf($kpiRecords, $baseHours);

        return [
            'value' => (int) round($avgMtbf, 0),
        ];
    }

    /**
     * Helper to compute average MTBF across all broken-down units:
     * Unit MTBF = (624 - total downtime / repair duration) / berapa kali korektif unit itu
     * Overall = Average of unit MTBFs
     */
    private function computeAverageMtbf($records, float $baseHours = 624.0): float
    {
        if (!$records || $records->count() === 0) {
            return $baseHours;
        }

        $machinesGrouped = [];

        foreach ($records as $rec) {
            $mInfo = $this->resolveMachineInfo($rec);
            $mKey = $mInfo['key'];

            $durationHours = 0;
            if (!empty($rec->waktu_mulai) && !empty($rec->waktu_selesai)) {
                $tDate = $rec->tanggal ? Carbon::parse($rec->tanggal)->format('Y-m-d') : date('Y-m-d');
                $tDateEnd = !empty($rec->tanggal_selesai) ? Carbon::parse($rec->tanggal_selesai)->format('Y-m-d') : $tDate;
                $startT = Carbon::parse($tDate . ' ' . $rec->waktu_mulai);
                $endT = Carbon::parse($tDateEnd . ' ' . $rec->waktu_selesai);
                if ($endT->lessThan($startT)) {
                    $endT->addDay();
                }
                $durationHours = max(0.1, $startT->diffInMinutes($endT) / 60);
            } else {
                $durationHours = 1.5;
            }

            if (!isset($machinesGrouped[$mKey])) {
                $machinesGrouped[$mKey] = [
                    'freq' => 0,
                    'downtime' => 0.0,
                ];
            }

            $machinesGrouped[$mKey]['freq'] += 1;
            $machinesGrouped[$mKey]['downtime'] += $durationHours;
        }

        if (empty($machinesGrouped)) {
            return $baseHours;
        }

        $unitMtbfs = [];
        foreach ($machinesGrouped as $m) {
            $unitMtbf = max(0, $baseHours - $m['downtime']) / max(1, $m['freq']);
            $unitMtbfs[] = $unitMtbf;
        }

        return count($unitMtbfs) > 0 ? round(array_sum($unitMtbfs) / count($unitMtbfs), 1) : $baseHours;
    }

    /**
     * Maintenance Cost Data - Real calculation based on mtc_penggantian_material
     * and pricing from mtc_master_material
     */
    private function getCostMaintenanceData($startDate, $endDate, array $weeksList, string $filterType): array
    {
        $chartStart = count($weeksList) > 0 ? $weeksList[0]['start']->format('Y-m-d') : $startDate->format('Y-m-d');
        $chartEnd = count($weeksList) > 0 ? end($weeksList)['end']->format('Y-m-d') : $endDate->format('Y-m-d');

        // Fetch replacement materials in chart range joined with main and master material
        $replacementItems = MtcPenggantianMaterialModel::with(['main', 'masterMaterial'])
            ->whereHas('main', function ($q) use ($chartStart, $chartEnd) {
                $q->whereBetween('tanggal', [$chartStart, $chartEnd]);
            })
            ->where('qty', '>', 0)
            ->get();

        $chartCategories = [];
        $dateRanges = [];
        $categoryDetails = [];

        $categoriesList = ['MHE', 'Motor Pompa', 'Utility', 'Sipil'];
        $categoryColors = [
            'MHE'         => '#38bdf8', // Cyan
            'Motor Pompa' => '#10b981', // Emerald
            'Utility'     => '#f59e0b', // Amber
            'Sipil'       => '#a855f7', // Purple
        ];

        $costDataByCat = [
            'MHE'         => [],
            'Motor Pompa' => [],
            'Utility'     => [],
            'Sipil'       => [],
        ];

        $totalSpareparts = 0;
        $selectedWeekTotal = 0;

        foreach ($weeksList as $idx => $wk) {
            $wShort = !empty($wk['short_label']) ? $wk['short_label'] : $wk['key'];
            $chartCategories[] = $wShort;
            $dateRanges[] = $wk['label'];
            $categoryDetails[] = !empty($wk['display']) ? $wk['display'] : "{$wShort} ({$wk['label']})";
            $wStart = $wk['start']->format('Y-m-d');
            $wEnd = $wk['end']->format('Y-m-d');

            $wItems = $replacementItems->filter(function ($item) use ($wStart, $wEnd) {
                if (!$item->main || empty($item->main->tanggal)) return false;
                $t = Carbon::parse($item->main->tanggal)->format('Y-m-d');
                return $t >= $wStart && $t <= $wEnd;
            });

            $weekCostPerCat = [
                'MHE'         => 0.0,
                'Motor Pompa' => 0.0,
                'Utility'     => 0.0,
                'Sipil'       => 0.0,
            ];

            $weekTotal = 0.0;

            foreach ($wItems as $item) {
                $cost = floatval($item->total_harga);
                if ($cost <= 0) {
                    $unitPrice = floatval($item->harga_satuan);
                    if ($unitPrice <= 0 && $item->masterMaterial) {
                        $unitPrice = floatval($item->masterMaterial->harga);
                    }
                    $cost = floatval($item->qty) * $unitPrice;
                }

                $cat = $this->normalizeJenisCategory($item->main->jenis_mtc ?? '');
                if (!isset($weekCostPerCat[$cat])) {
                    $cat = 'Utility';
                }
                $weekCostPerCat[$cat] += $cost;
                $weekTotal += $cost;
            }

            foreach ($categoriesList as $cat) {
                $costDataByCat[$cat][] = round($weekCostPerCat[$cat] / 1000000, 2);
            }

            $totalSpareparts += $weekTotal;

            if (!empty($wk['is_selected'])) {
                $selectedWeekTotal = $weekTotal;
            }
        }

        $series = [];
        foreach ($categoriesList as $cat) {
            $series[] = [
                'name'  => $cat,
                'data'  => $costDataByCat[$cat],
                'color' => $categoryColors[$cat],
            ];
        }

        $totalPeriodCost = $totalSpareparts;
        // In weekly filter, KPI displays selected week cost; in monthly/custom, total of period
        $kpiCost = ($filterType === 'weekly' && $selectedWeekTotal > 0) ? $selectedWeekTotal : $totalPeriodCost;

        $monthlyBudget = 95000000.0; // 95 Juta IDR budget limit
        $budgetLimit = ($filterType === 'weekly') ? ($monthlyBudget / 4) : $monthlyBudget;
        $budgetUtil = $budgetLimit > 0 ? round(($kpiCost / $budgetLimit) * 100, 1) : 0;

        $kpiCostM = round($kpiCost / 1000000, 2);
        $totalCostFormatted = ($kpiCostM >= 1.0)
            ? 'IDR ' . number_format($kpiCostM, 1, ',', '.') . ' Jt'
            : ($kpiCost > 0 ? 'IDR ' . number_format($kpiCostM, 2, ',', '.') . ' Jt' : 'IDR 0,0 Jt');

        $budgetFormatted = ($filterType === 'weekly') ? '23.8 Jt (W)' : '95.0 Jt';

        return [
            'total_cost' => $kpiCost,
            'total_cost_formatted' => $totalCostFormatted,
            'budget_formatted' => $budgetFormatted,
            'budget_utilization' => $budgetUtil,
            'chart' => [
                'categories' => $chartCategories,
                'date_ranges' => $dateRanges,
                'category_details' => $categoryDetails,
                'series' => $series,
            ],
        ];
    }

    /**
     * Helper to resolve machine info from a MtcMainModel instance
     */
    private function resolveMachineInfo($main): array
    {
        $rel = match ($main->jenis_mtc) {
            'Motor Pompa'                  => 'motorPump',
            'Utility'                      => 'utility',
            'Electrical'                   => 'electrical',
            'Refrigerasi'                  => 'refrigerasi',
            'Electric Engine'              => 'electricEngine',
            'Diesel Engine'                => 'dieselEngine',
            'Electric P2h', 'Electric P2H' => 'electricP2h',
            'Diesel P2h', 'Diesel P2H'     => 'dieselP2h',
            'Genset P2h', 'Genset P2H'     => 'gensetP2h',
            default                        => null
        };

        $machineKey  = 'unknown';
        $machineName = '-';
        $machineCode = '-';
        $location    = $main->lokasi ?? $main->area ?? '-';

        if ($rel && $main->$rel && $main->$rel->mesin) {
            $mObj        = $main->$rel->mesin;
            $machineKey  = "mesin_{$mObj->id}";
            $machineName = $mObj->nama_mesin;
            $machineCode = $mObj->kode_mesin ?? '-';
            $location    = $mObj->lokasi ?? $mObj->dept ?? $location;
        } elseif (in_array($main->jenis_mtc, ['Electric Engine', 'Diesel Engine'])) {
            $machineKey  = "main_" . $main->id;
            $machineName = $main->area ? "Unit: {$main->area}" : ($main->jenis_mtc . ' #' . $main->id);
            $machineCode = '-';
        } elseif ($main->jenis_mtc === 'Battery' && $main->battery) {
            $machineKey  = "battery_" . ($main->battery->id ?? $main->id);
            $machineName = 'Unit: ' . ($main->battery->no_unit ?? '-') . ' (Seri: ' . ($main->battery->no_seri ?? '-') . ')';
            $machineCode = 'BATTERY';
        } elseif ($main->jenis_mtc === 'Sipil') {
            $areaVal     = $main->area ?? 'Umum';
            $machineKey  = "sipil_" . strtolower(trim($areaVal));
            $machineName = 'Sipil: ' . $areaVal;
            $machineCode = 'SIPIL';
        } else {
            $machineKey  = "main_" . $main->id;
            $machineName = $main->area ? "Area: {$main->area}" : ($main->jenis_mtc ? $main->jenis_mtc . ' #' . $main->id : 'Mesin #' . $main->id);
            $machineCode = '-';
        }

        return [
            'key'        => $machineKey,
            'nama_mesin' => $machineName,
            'kode_mesin' => $machineCode,
            'area'       => $location ?: '-',
        ];
    }

    /**
     * Top Machines Analytics: Top Mesin MTBF Terendah, Top MTTR Terlama, Top Cost Tertinggi
     * Real calculation based on paket 'Korektif' in mtc_main (waktu_mulai s.d. waktu_selesai)
     */
    private function getTopMachinesAnalytics($startDate, $endDate, float $operatingHours = 624): array
    {
        // 1. Ambil data korektif dalam rentang waktu filter
        $korektifRecords = MtcMainModel::with([
            'motorPump.mesin',
            'utility.mesin',
            'electrical.mesin',
            'refrigerasi.mesin',
            'electricEngine.mesin',
            'dieselEngine.mesin',
            'electricP2h.mesin',
            'dieselP2h.mesin',
            'gensetP2h.mesin',
            'battery',
        ])
            ->whereRaw("LOWER(TRIM(COALESCE(paket, ''))) LIKE '%korektif%'")
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $machinesGrouped = [];

        foreach ($korektifRecords as $rec) {
            $mInfo = $this->resolveMachineInfo($rec);
            $mKey = $mInfo['key'];

            // Hitung durasi perbaikan dari waktu_mulai s.d. waktu_selesai
            $durationHours = 0;
            if (!empty($rec->waktu_mulai) && !empty($rec->waktu_selesai)) {
                $tDate = $rec->tanggal ? Carbon::parse($rec->tanggal)->format('Y-m-d') : date('Y-m-d');
                $tDateEnd = !empty($rec->tanggal_selesai) ? Carbon::parse($rec->tanggal_selesai)->format('Y-m-d') : $tDate;
                $startT = Carbon::parse($tDate . ' ' . $rec->waktu_mulai);
                $endT = Carbon::parse($tDateEnd . ' ' . $rec->waktu_selesai);
                if ($endT->lessThan($startT)) {
                    $endT->addDay();
                }
                $durationHours = max(0.1, $startT->diffInMinutes($endT) / 60);
            } else {
                $durationHours = 1.5; // fallback estimasi jika waktu tidak diisi
            }

            $kendala = trim($rec->keterangan ?: ($rec->korektif ?: ''));

            if (!isset($machinesGrouped[$mKey])) {
                $machinesGrouped[$mKey] = [
                    'nama_mesin'     => $mInfo['nama_mesin'],
                    'kode_mesin'     => $mInfo['kode_mesin'],
                    'area'           => $mInfo['area'],
                    'breakdown_freq' => 0,
                    'total_downtime' => 0.0,
                    'keterangan'     => [],
                ];
            }

            $machinesGrouped[$mKey]['breakdown_freq'] += 1;
            $machinesGrouped[$mKey]['total_downtime'] += $durationHours;
            if ($kendala && !in_array($kendala, $machinesGrouped[$mKey]['keterangan'])) {
                $machinesGrouped[$mKey]['keterangan'][] = $kendala;
            }
        }

        // Kalkulasi MTBF & MTTR per mesin
        $evaluatedMachines = [];
        $baseHours = max(1, $operatingHours);

        foreach ($machinesGrouped as $key => $m) {
            $freq = $m['breakdown_freq'];
            $downtime = $m['total_downtime'];

            $mttr = round($downtime / max(1, $freq), 1);
            // Rumus MTBF unit: (624 - total MTTR / total downtime unit) / berapa kali korektif dari unit itu
            $mtbf = round(max(0, $baseHours - $downtime) / max(1, $freq), 0);

            $ket = !empty($m['keterangan']) ? implode(', ', $m['keterangan']) : 'Perbaikan Kerusakan Korektif';

            $evaluatedMachines[] = [
                'nama_mesin'     => $m['nama_mesin'],
                'kode_mesin'     => $m['kode_mesin'],
                'area'           => $m['area'],
                'breakdown_freq' => $freq,
                'total_downtime' => round($downtime, 1),
                'nilai_mtbf'     => (int) $mtbf,
                'rata_mttr'      => (float) $mttr,
                'keterangan'     => $ket,
            ];
        }

        // 1. Top Mesin MTBF (Urut dari nilai terbesar / all data tanpa limit)
        $sortedByMtbf = collect($evaluatedMachines)->sortByDesc('nilai_mtbf')->values();

        $topMtbf = [];
        foreach ($sortedByMtbf as $idx => $item) {
            $mtbfVal = $item['nilai_mtbf'];
            $status = $mtbfVal < 100 ? 'Critical' : ($mtbfVal < 250 ? 'Warning' : 'Optimal');
            $statusColor = $mtbfVal < 100 ? 'danger' : ($mtbfVal < 250 ? 'warning' : 'success');

            $topMtbf[] = [
                'rank'           => $idx + 1,
                'nama_mesin'     => $item['nama_mesin'],
                'kode_mesin'     => $item['kode_mesin'],
                'area'           => $item['area'],
                'nilai_mtbf'     => $mtbfVal,
                'breakdown_freq' => $item['breakdown_freq'],
                'status'         => $status,
                'status_color'   => $statusColor,
            ];
        }

        // 2. Top MTTR Terlama (Urut dari nilai terbesar / all data tanpa limit)
        $sortedByMttr = collect($evaluatedMachines)->sortByDesc('rata_mttr')->values();

        $topMttr = [];
        foreach ($sortedByMttr as $idx => $item) {
            $mttrVal = $item['rata_mttr'];
            $status = $mttrVal > 5.0 ? 'Critical' : ($mttrVal > 3.0 ? 'Warning' : 'Optimal');
            $statusColor = $mttrVal > 5.0 ? 'danger' : ($mttrVal > 3.0 ? 'warning' : 'success');

            $topMttr[] = [
                'rank'         => $idx + 1,
                'nama_mesin'   => $item['nama_mesin'],
                'kode_mesin'   => $item['kode_mesin'],
                'area'         => $item['area'],
                'rata_mttr'    => $mttrVal,
                'keterangan'   => $item['keterangan'],
                'status'       => $status,
                'status_color' => $statusColor,
            ];
        }

        // 3. Top Cost Tertinggi (Budget Impact - Real calculation from mtc_penggantian_material)
        $costRecords = MtcPenggantianMaterialModel::with([
            'main.motorPump.mesin',
            'main.utility.mesin',
            'main.electrical.mesin',
            'main.refrigerasi.mesin',
            'main.electricEngine.mesin',
            'main.dieselEngine.mesin',
            'main.electricP2h.mesin',
            'main.dieselP2h.mesin',
            'main.gensetP2h.mesin',
            'main.battery',
        ])
            ->whereHas('main', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })
            ->where('qty', '>', 0)
            ->get();

        $masterMids = $costRecords->pluck('mid')->filter()->unique();
        $masterMaterials = MtcMasterMaterialModel::whereIn('mid', $masterMids)->get()->keyBy('mid');

        $machineCosts = [];
        $grandTotalCost = 0;

        foreach ($costRecords as $rec) {
            if (!$rec->main) continue;
            $mInfo = $this->resolveMachineInfo($rec->main);
            $mKey = $mInfo['key'];

            $cost = floatval($rec->total_harga);
            if ($cost <= 0) {
                $unitPrice = floatval($rec->harga_satuan);
                if ($unitPrice <= 0 && !empty($rec->mid) && isset($masterMaterials[$rec->mid])) {
                    $unitPrice = floatval($masterMaterials[$rec->mid]->harga);
                }
                $cost = floatval($rec->qty) * $unitPrice;
            }

            if (!isset($machineCosts[$mKey])) {
                $machineCosts[$mKey] = [
                    'nama_mesin' => $mInfo['nama_mesin'],
                    'kode_mesin' => $mInfo['kode_mesin'],
                    'area'       => $mInfo['area'],
                    'total_cost' => 0.0,
                    'spareparts' => [],
                ];
            }

            $machineCosts[$mKey]['total_cost'] += $cost;
            $grandTotalCost += $cost;
            if (!empty($rec->deskripsi) && !in_array($rec->deskripsi, $machineCosts[$mKey]['spareparts'])) {
                $machineCosts[$mKey]['spareparts'][] = $rec->deskripsi;
            }
        }

        $sortedByCost = collect($machineCosts)->sortByDesc('total_cost')->values()->take(5);

        $topCost = [];
        foreach ($sortedByCost as $idx => $item) {
            $mCost = $item['total_cost'];
            $pct = $grandTotalCost > 0 ? round(($mCost / $grandTotalCost) * 100, 1) : 0;
            $fmtCost = $mCost >= 1000000
                ? 'IDR ' . number_format($mCost / 1000000, 1, ',', '.') . ' Jt'
                : 'IDR ' . number_format($mCost / 1000000, 2, ',', '.') . ' Jt';

            $topCost[] = [
                'rank'                 => $idx + 1,
                'nama_mesin'           => $item['nama_mesin'],
                'kode_mesin'           => $item['kode_mesin'],
                'area'                 => $item['area'],
                'total_cost'           => $mCost,
                'total_cost_formatted' => $fmtCost,
                'persentase'           => $pct,
                'sparepart'            => !empty($item['spareparts']) ? implode(', ', array_slice($item['spareparts'], 0, 2)) : '-',
            ];
        }

        // All active machines representation for correlation
        $allActiveMachines = [
            ['name' => 'Conveyor Line 3', 'area' => 'MHE', 'mtbf' => 45, 'mttr' => 4.2],
            ['name' => 'Pompa Utama Air Bersih', 'area' => 'Motor Pompa', 'mtbf' => 72, 'mttr' => 3.1],
            ['name' => 'Chiller Unit 02', 'area' => 'Utility', 'mtbf' => 95, 'mttr' => 4.5],
            ['name' => 'Robot Welding R-4', 'area' => 'MHE', 'mtbf' => 140, 'mttr' => 8.5],
            ['name' => 'Compressor Utama 01', 'area' => 'Utility', 'mtbf' => 180, 'mttr' => 6.2],
            ['name' => 'AHU Produksi B', 'area' => 'Utility', 'mtbf' => 210, 'mttr' => 5.0],
            ['name' => 'Forklift Electric 04', 'area' => 'MHE', 'mtbf' => 260, 'mttr' => 2.3],
            ['name' => 'Motor Pompa Booster 01', 'area' => 'Motor Pompa', 'mtbf' => 310, 'mttr' => 1.8],
            ['name' => 'Genset Caterpillar 500kVA', 'area' => 'Utility', 'mtbf' => 380, 'mttr' => 3.5],
            ['name' => 'Compressor GA37', 'area' => 'Utility', 'mtbf' => 420, 'mttr' => 2.1],
            ['name' => 'Pintu Rolling Door Timur', 'area' => 'Sipil', 'mtbf' => 480, 'mttr' => 1.5],
            ['name' => 'Hydraulic Press 1000T', 'area' => 'MHE', 'mtbf' => 520, 'mttr' => 3.8],
            ['name' => 'Cooling Tower Unit 03', 'area' => 'Utility', 'mtbf' => 560, 'mttr' => 1.9],
            ['name' => 'Motor Pompa Transfer 02', 'area' => 'Motor Pompa', 'mtbf' => 610, 'mttr' => 1.4],
            ['name' => 'Linde Reach Truck RT-02', 'area' => 'MHE', 'mtbf' => 650, 'mttr' => 2.0],
            ['name' => 'Plafon & Partisi Produksi', 'area' => 'Sipil', 'mtbf' => 700, 'mttr' => 1.2],
        ];

        return [
            'top_mtbf' => $topMtbf,
            'top_mttr' => $topMttr,
            'top_cost' => $topCost,
            'all_active_machines' => $allActiveMachines,
        ];
    }

    /**
     * Unit Off (Area Breakdown & Live Offline Units)
     * Breakdown units where paket = 'Off'
     */
    private function getUnitOffData($startDate, $endDate): array
    {
        $offRecords = MtcMainModel::with([
            'motorPump.mesin',
            'utility.mesin',
            'electrical.mesin',
            'refrigerasi.mesin',
            'electricEngine.mesin',
            'dieselEngine.mesin',
            'electricP2h.mesin',
            'dieselP2h.mesin',
            'gensetP2h.mesin',
            'battery',
        ])
            ->whereRaw("LOWER(TRIM(COALESCE(paket, ''))) = 'off'")
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderByDesc('tanggal')
            ->orderByDesc('waktu_mulai')
            ->get();

        $areaCounts = [
            'MHE (Diesel & Electric)' => 0,
            'Motor Pompa'             => 0,
            'Utility Plant'           => 0,
            'Sipil & Building'        => 0,
        ];

        $palette = [
            'MHE (Diesel & Electric)' => '#38bdf8',
            'Motor Pompa'             => '#10b981',
            'Utility Plant'           => '#f59e0b',
            'Sipil & Building'        => '#a855f7',
        ];

        $offlineUnits = [];

        foreach ($offRecords as $rec) {
            $cat = $this->normalizeJenisCategory($rec->jenis_mtc);
            $areaLabel = match ($cat) {
                'MHE'         => 'MHE (Diesel & Electric)',
                'Motor Pompa' => 'Motor Pompa',
                'Sipil'       => 'Sipil & Building',
                default       => 'Utility Plant',
            };

            $areaCounts[$areaLabel]++;

            $mInfo = $this->resolveMachineInfo($rec);

            $catBadge = match ($cat) {
                'MHE'         => 'primary',
                'Motor Pompa' => 'success',
                'Utility'     => 'purple',
                'Sipil'       => 'warning',
                default       => 'secondary',
            };

            // Calculate duration or time
            $downtimeStr = '-';
            if (!empty($rec->waktu_mulai) && !empty($rec->waktu_selesai)) {
                $tDate = $rec->tanggal ? Carbon::parse($rec->tanggal)->format('Y-m-d') : date('Y-m-d');
                $tDateEnd = !empty($rec->tanggal_selesai) ? Carbon::parse($rec->tanggal_selesai)->format('Y-m-d') : $tDate;
                $startT = Carbon::parse($tDate . ' ' . $rec->waktu_mulai);
                $endT = Carbon::parse($tDateEnd . ' ' . $rec->waktu_selesai);
                if ($endT->lessThan($startT)) {
                    $endT->addDay();
                }
                $diffMin = $startT->diffInMinutes($endT);
                $hours = floor($diffMin / 60);
                $mins = $diffMin % 60;
                $downtimeStr = "{$hours} Jam {$mins} Menit";
            } elseif (!empty($rec->waktu_mulai)) {
                $downtimeStr = Carbon::parse($rec->waktu_mulai)->format('H:i') . ' WIB';
            }

            $offSince = $rec->tanggal ? Carbon::parse($rec->tanggal)->format('d M Y') : '-';
            if (!empty($rec->waktu_mulai)) {
                $offSince .= ' ' . Carbon::parse($rec->waktu_mulai)->format('H:i');
            }

            $offlineUnits[] = [
                'unit_id'        => ($mInfo['kode_mesin'] && $mInfo['kode_mesin'] !== '-') ? $mInfo['kode_mesin'] : 'MTC-' . $rec->id,
                'name'           => $mInfo['nama_mesin'],
                'category'       => $cat,
                'category_badge' => $catBadge,
                'lokasi'         => $mInfo['area'],
                'off_since'      => $offSince,
                'downtime_str'   => $downtimeStr,
                'cause'          => $rec->keterangan ?: ($rec->korektif ?: 'Paket Off'),
                'status'         => $rec->status ? ucfirst($rec->status) : 'Off',
                'status_badge'   => 'danger',
            ];
        }

        $totalOff = $offRecords->count();

        $chartData = [
            'labels' => array_keys($areaCounts),
            'series' => array_values($areaCounts),
            'colors' => array_values($palette),
            'total'  => $totalOff,
        ];

        return [
            'chart'         => $chartData,
            'offline_units' => $offlineUnits,
        ];
    }

    /**
     * MTBF vs MTTR Correlation Line Chart (Trend per week)
     * Comparing: MTBF (Jam) and MTTR (Jam)
     */
    private function getMttrMtbfCorrelation(array $weeksList, array $breakdownData, array $downtimeData): array
    {
        $categories = [];
        $dateRanges = [];
        $categoryDetails = [];
        $seriesMtbf = [];
        $seriesMttr = [];

        $baseHours = 624.0; // Rumus standar: 624 jam perusahaan

        foreach ($weeksList as $idx => $wk) {
            $wShort = !empty($wk['short_label']) ? $wk['short_label'] : $wk['key'];
            $categories[] = $wShort;
            $dateRanges[] = $wk['label'];
            $categoryDetails[] = !empty($wk['display']) ? $wk['display'] : "{$wShort} ({$wk['label']})";

            $wStart = $wk['start']->format('Y-m-d');
            $wEnd = $wk['end']->format('Y-m-d');

            $wRecords = $breakdownData['records']->filter(function ($item) use ($wStart, $wEnd) {
                return $item->tanggal >= $wStart && $item->tanggal <= $wEnd;
            });

            $breakdownCount = $wRecords->count();
            $downtimeHours = 0;

            foreach ($wRecords as $rec) {
                if (!empty($rec->waktu_mulai) && !empty($rec->waktu_selesai)) {
                    $startT = Carbon::parse($rec->waktu_mulai);
                    $endT = Carbon::parse($rec->waktu_selesai);
                    $downtimeHours += max(0.1, $startT->diffInMinutes($endT) / 60);
                }
            }

            if ($breakdownCount > 0) {
                if ($downtimeHours <= 0) {
                    $downtimeHours = round($breakdownCount * 1.5, 1);
                }
                $weekMttr = round($downtimeHours / $breakdownCount, 1);
                $weekMtbf = round($this->computeAverageMtbf($wRecords, $baseHours), 0);
            } else {
                $weekMttr = 0.0;
                $weekMtbf = (int) $baseHours;
            }

            $seriesMtbf[] = (int) $weekMtbf;
            $seriesMttr[] = (float) $weekMttr;
        }

        return [
            'categories' => $categories,
            'date_ranges' => $dateRanges,
            'category_details' => $categoryDetails,
            'series' => [
                [
                    'name' => 'MTBF (Jam)',
                    'data' => $seriesMtbf,
                ],
                [
                    'name' => 'MTTR (Jam)',
                    'data' => $seriesMttr,
                ],
            ],
            'benchmark' => [
                'target_mttr' => 3.0,
                'target_mtbf' => 250,
            ],
        ];
    }
}
