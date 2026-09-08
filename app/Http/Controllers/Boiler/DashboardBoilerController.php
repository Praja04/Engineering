<?php

namespace App\Http\Controllers\Boiler;

use Illuminate\Http\Request;
use App\Models\Utility\KpiModel;
use App\Models\Utility\EspShiftReport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class DashboardBoilerController extends Controller
{
    public function index()
    {
        return view('boiler.dashboard');
    }

    public function dashboard_realtime()
    {
        return view('dashboard.boiler.dashboard_boiler');
    }

    public function viewDashboardKpi()
    {
        return view('dashboard.boiler.kpi_dashboard');
    }

    // ==========================================
    // JSON API Handlers
    // ==========================================

    public function getBatuBaraSteam(Request $request)
    {
        $data = $this->queryBatuBaraSteam($request->start_date, $request->end_date);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getKondensat(Request $request)
    {
        $data = $this->queryKondensat($request->start_date, $request->end_date);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getSteamFg(Request $request)
    {
        $data = $this->querySteamFgWeekly($request->start_date, $request->end_date, true);

        return response()->json([
            "status"  => "success",
            "message" => "Data steam per minggu & FG berhasil diambil",
            "data"    => $data
        ]);
    }

    public function getBatuBaraFg(Request $request)
    {
        $data = $this->queryBatuBaraFgWeekly($request->start_date, $request->end_date, true);

        return response()->json([
            "status"  => "success",
            "message" => "Data batu bara per minggu & FG berhasil diambil",
            "data"    => $data
        ]);
    }

    public function getSteamFgMonthly(Request $request)
    {
        $data = $this->querySteamFgMonthly($request->start_date, $request->end_date, true);

        return response()->json([
            "status"  => "success",
            "message" => "Data steam kumulatif bulanan & FG berhasil diambil",
            "data"    => $data
        ]);
    }

    public function getBatuBaraFgMonthly(Request $request)
    {
        $data = $this->queryBatuBaraFgMonthly($request->start_date, $request->end_date, true);

        return response()->json([
            "status"  => "success",
            "message" => "Data batu bara kumulatif bulanan & FG berhasil diambil",
            "data"    => $data
        ]);
    }

    // ==========================================
    // Data Query Helpers
    // ==========================================

    public function queryBatuBaraSteam($startDate = null, $endDate = null)
    {
        $query = EspShiftReport::orderBy('tanggal_laporan', 'asc');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_laporan', [$startDate, $endDate]);
        }

        return $query->get()->map(function ($item) {
            $steam = (float) $item->pemakaian_steam;
            $bb = (float) $item->pemakaian_batubara;

            return [
                'date' => $item->tanggal_laporan,
                'steam' => $steam,
                'batu_bara' => $bb,
                'rasio' => $steam > 0 ? ($bb / $steam) * 1000 : 0
            ];
        });
    }

    public function queryKondensat($startDate = null, $endDate = null)
    {
        $query = EspShiftReport::orderBy('tanggal_laporan', 'asc');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_laporan', [$startDate, $endDate]);
        }

        return $query->get()->map(function ($item) {
            return [
                'date' => $item->tanggal_laporan,
                'pemakaian_air' => (float) $item->pemakaian_air,
                'feed_tank_awal' => (float) $item->feed_tank_awal,
                'feed_tank_akhir' => (float) $item->feed_tank_akhir,
                'kondensat' => (float) $item->kondensat,
            ];
        });
    }

    public function querySteamFgWeekly($start = null, $end = null, $limit = false)
    {
        $dailyQuery = EspShiftReport::query();

        if ($start) {
            $dailyQuery->whereDate('tanggal_laporan', '>=', $start);
        }

        if ($end) {
            $dailyQuery->whereDate('tanggal_laporan', '<=', $end);
        }

        if ($limit && !$start && !$end) {
            $dailyQuery->orderBy('tanggal_laporan', 'asc')->limit(20);
        } else {
            $dailyQuery->orderBy('tanggal_laporan', 'asc');
        }

        $dailySteamData = $dailyQuery->get(['tanggal_laporan as date', 'pemakaian_steam as steam']);

        if ($dailySteamData->isEmpty() && $start) {
            return collect();
        }

        $fgWeeklyQuery = KpiModel::where('periode_tipe', 'weekly');

        if ($start) {
            $fgWeeklyQuery->where('end_date', '>=', $start);
        }

        if ($end) {
            $fgWeeklyQuery->where('start_date', '<=', $end);
        }

        if ($limit && !$start && !$end) {
            $fgWeeklyQuery->orderBy('start_date', 'asc')->limit(20);
        } else {
            $fgWeeklyQuery->orderBy('start_date', 'asc');
        }

        $fgWeekly = $fgWeeklyQuery->get();

        $result = [];

        foreach ($fgWeekly as $week) {
            $weekStart = $week->start_date;
            $weekEnd   = $week->end_date;
            $fgValue   = (float) $week->finish_goods;

            $steamFromKpi = $week->steam;

            if ($steamFromKpi !== null && $steamFromKpi > 0) {
                $totalSteam = (float) $steamFromKpi;
                $source     = 'KPI';
            } else {
                $steamInWeek = $dailySteamData
                    ->filter(fn($item) => $item->date >= $weekStart && $item->date <= $weekEnd)
                    ->sum('steam');

                $totalSteam = (float) $steamInWeek;
                $source     = 'Daily ESP';
            }

            $rasio = $fgValue > 0 ? ($totalSteam / $fgValue) * 10 : 0;

            $result[] = [
                'week_start'   => $weekStart,
                'week_end'     => $weekEnd,
                'steam'        => round($totalSteam, 2),
                'finish_goods' => $fgValue,
                'rasio'        => round($rasio, 2),
                'source'       => $source,
            ];
        }

        return collect($result);
    }

    public function queryBatuBaraFgWeekly($start = null, $end = null, $limit = false)
    {
        $dailyQuery = EspShiftReport::query();
        if ($start) {
            $dailyQuery->whereDate('tanggal_laporan', '>=', $start);
        }

        if ($end) {
            $dailyQuery->whereDate('tanggal_laporan', '<=', $end);
        }

        if ($limit && !$start && !$end) {
            $dailyQuery->orderBy('tanggal_laporan', 'asc')->limit(20);
        } else {
            $dailyQuery->orderBy('tanggal_laporan', 'asc');
        }

        $dailyBbData = $dailyQuery->get(['tanggal_laporan as date', 'pemakaian_batubara as batu_bara']);

        if ($dailyBbData->isEmpty() && $start) {
            return collect();
        }

        $fgWeeklyQuery = KpiModel::where('periode_tipe', 'weekly');
        if ($start) {
            $fgWeeklyQuery->where('end_date', '>=', $start);
        }

        if ($end) {
            $fgWeeklyQuery->where('start_date', '<=', $end);
        }

        if ($limit && !$start && !$end) {
            $fgWeeklyQuery->orderBy('start_date', 'asc')->limit(20);
        } else {
            $fgWeeklyQuery->orderBy('start_date', 'asc');
        }

        $fgWeekly = $fgWeeklyQuery->get();

        $result = [];

        foreach ($fgWeekly as $week) {
            $weekStart = $week->start_date;
            $weekEnd   = $week->end_date;
            $fgValue   = (float) $week->finish_goods;

            $bbFromKpi = $week->batubara;

            if ($bbFromKpi !== null && $bbFromKpi > 0) {
                $totalBb = (float) $bbFromKpi;
                $source  = 'KPI';
            } else {
                $bbInWeek = $dailyBbData
                    ->filter(fn($item) => $item->date >= $weekStart && $item->date <= $weekEnd)
                    ->sum('batu_bara');
                $totalBb = (float) $bbInWeek;
                $source  = 'Daily ESP';
            }

            $rasio = $fgValue > 0 ? ($totalBb / $fgValue) * 1000 : 0;

            $result[] = [
                'week_start'    => $weekStart,
                'week_end'      => $weekEnd,
                'batu_bara'     => round($totalBb, 2),
                'finish_goods'  => $fgValue,
                'rasio'         => round($rasio, 2),
                'source'        => $source,
            ];
        }

        return collect($result);
    }

    public function querySteamFgMonthly($start = null, $end = null, $limit = false)
    {
        $startMonth = $start ? substr($start, 0, 7) : null;
        $endMonth   = $end   ? substr($end,   0, 7) : null;

        $dailyQuery = EspShiftReport::query();
        if ($start) {
            $dailyQuery->whereDate('tanggal_laporan', '>=', $start);
        }

        if ($end) {
            $dailyQuery->whereDate('tanggal_laporan', '<=', $end);
        }

        if ($limit && !$start && !$end) {
            $dailyQuery->orderBy('tanggal_laporan', 'asc')->limit(365);
        } else {
            $dailyQuery->orderBy('tanggal_laporan', 'asc');
        }

        $dailyData = $dailyQuery->get(['tanggal_laporan as date', 'pemakaian_steam as steam']);

        $steamByMonth = $dailyData->groupBy(fn($item) => substr($item->date, 0, 7))
            ->map(fn($items) => $items->sum('steam'));

        $monthlyQuery = KpiModel::where('periode_tipe', 'monthly');
        if ($startMonth) {
            $monthlyQuery->where('month', '>=', $startMonth);
        }

        if ($endMonth) {
            $monthlyQuery->where('month', '<=', $endMonth);
        }

        if ($limit && !$startMonth && !$endMonth) {
            $monthlyQuery->orderBy('month', 'asc')->limit(30);
        } else {
            $monthlyQuery->orderBy('month', 'asc');
        }

        $kpiMonthly = $monthlyQuery->get(['month', 'finish_goods', 'steam']);

        $weeklyQuery = KpiModel::where('periode_tipe', 'weekly')
            ->when($start, fn($q) => $q->where('end_date', '>=', $start))
            ->when($end,   fn($q) => $q->where('start_date', '<=', $end));
        $fgWeeklyByMonth = $weeklyQuery->get()
            ->groupBy(fn($item) => substr($item->start_date, 0, 7))
            ->map(fn($weeks) => $weeks->sum('finish_goods'));

        $allMonths = $steamByMonth->keys()
            ->merge($kpiMonthly->pluck('month'))
            ->unique()
            ->sort();

        if ($limit && !$start && !$end) {
            $allMonths = $allMonths->take(30);
        }

        $result = [];

        foreach ($allMonths as $month) {
            $monthlyRecord = $kpiMonthly->firstWhere('month', $month);
            $steamFromKpi  = $monthlyRecord ? $monthlyRecord->steam : null;

            if ($steamFromKpi !== null && $steamFromKpi > 0) {
                $totalSteam = (float) $steamFromKpi;
                $sourceSteam = 'KPI Monthly';
            } else {
                $totalSteam = (float) ($steamByMonth->get($month, 0));
                $sourceSteam = 'Daily ESP';
            }

            $fgValue = $monthlyRecord ? (float) $monthlyRecord->finish_goods : null;

            if (is_null($fgValue) || $fgValue == 0) {
                $fgValue = $fgWeeklyByMonth->get($month, 0);
                $sourceFg = 'Weekly Fallback';
            } else {
                $sourceFg = 'KPI Monthly';
            }

            $rasio = $fgValue > 0 ? ($totalSteam / $fgValue) * 1000 : 0;

            $result[] = [
                'month'         => $month,
                'steam'         => round($totalSteam, 2),
                'finish_goods'  => $fgValue,
                'rasio'         => round($rasio, 2),
                'source_steam'  => $sourceSteam,
                'source_fg'     => $sourceFg,
            ];
        }

        return collect($result);
    }

    public function queryBatuBaraFgMonthly($start = null, $end = null, $limit = false)
    {
        $startMonth = $start ? substr($start, 0, 7) : null;
        $endMonth   = $end   ? substr($end,   0, 7) : null;

        $dailyQuery = EspShiftReport::query();
        if ($start) {
            $dailyQuery->whereDate('tanggal_laporan', '>=', $start);
        }

        if ($end) {
            $dailyQuery->whereDate('tanggal_laporan', '<=', $end);
        }

        if ($limit && !$start && !$end) {
            $dailyQuery->orderBy('tanggal_laporan', 'asc')->limit(365);
        } else {
            $dailyQuery->orderBy('tanggal_laporan', 'asc');
        }

        $dailyData = $dailyQuery->get(['tanggal_laporan as date', 'pemakaian_batubara as batu_bara']);

        $bbByMonth = $dailyData->groupBy(fn($item) => substr($item->date, 0, 7))
            ->map(fn($items) => $items->sum('batu_bara'));

        $monthlyQuery = KpiModel::where('periode_tipe', 'monthly');
        if ($startMonth) {
            $monthlyQuery->where('month', '>=', $startMonth);
        }

        if ($endMonth) {
            $monthlyQuery->where('month', '<=', $endMonth);
        }

        if ($limit && !$startMonth && !$endMonth) {
            $monthlyQuery->orderBy('month', 'asc')->limit(30);
        } else {
            $monthlyQuery->orderBy('month', 'asc');
        }

        $kpiMonthly = $monthlyQuery->get(['month', 'finish_goods', 'batubara']);

        $weeklyQuery = KpiModel::where('periode_tipe', 'weekly')
            ->when($start, fn($q) => $q->where('end_date', '>=', $start))
            ->when($end,   fn($q) => $q->where('start_date', '<=', $end));
        $fgWeeklyByMonth = $weeklyQuery->get()
            ->groupBy(fn($item) => substr($item->start_date, 0, 7))
            ->map(fn($weeks) => $weeks->sum('finish_goods'));

        $allMonths = $bbByMonth->keys()->merge($kpiMonthly->pluck('month'))->unique()->sort();
        if ($limit && !$start && !$end) {
            $allMonths = $allMonths->take(30);
        }

        $result = [];

        foreach ($allMonths as $month) {
            $monthlyRecord = $kpiMonthly->firstWhere('month', $month);
            $bbFromKpi     = $monthlyRecord ? $monthlyRecord->batubara : null;

            if ($bbFromKpi !== null && $bbFromKpi > 0) {
                $totalBb = (float) $bbFromKpi;
                $sourceBb = 'KPI Monthly';
            } else {
                $totalBb = (float) ($bbByMonth->get($month, 0));
                $sourceBb = 'Daily ESP';
            }

            $fgValue = $monthlyRecord ? (float) $monthlyRecord->finish_goods : null;

            if (is_null($fgValue) || $fgValue == 0) {
                $fgValue = $fgWeeklyByMonth->get($month, 0);
                $sourceFg = 'Weekly Fallback';
            } else {
                $sourceFg = 'KPI Monthly';
            }

            $rasio = $fgValue > 0 ? ($totalBb / $fgValue) * 1000 : 0;

            $result[] = [
                'month'           => $month,
                'batu_bara'       => round($totalBb, 2),
                'finish_goods'    => $fgValue,
                'rasio'           => round($rasio, 2),
                'source_batubara' => $sourceBb,
                'source_fg'       => $sourceFg,
            ];
        }

        return collect($result);
    }

    // ==========================================
    // Export Excel Method
    // ==========================================

    public function exportKpiExcel(Request $request)
    {
        $scope = $request->get('scope', 'all');
        $filtered = $request->get('filtered', '0') === '1';

        $startBBSteam = $filtered ? $request->get('start_bb_steam') : null;
        $endBBSteam   = $filtered ? $request->get('end_bb_steam') : null;

        $startKondensat = $filtered ? $request->get('start_kondensat') : null;
        $endKondensat   = $filtered ? $request->get('end_kondensat') : null;

        $startSteamWeekly = $filtered ? $request->get('start_steam_weekly') : null;
        $endSteamWeekly   = $filtered ? $request->get('end_steam_weekly') : null;

        // Monthly Steam Date Range
        $yearSteamMonthly  = $filtered ? $request->get('year_steam_monthly') : null;
        $monthSteamMonthly = $filtered ? $request->get('month_steam_monthly') : null;
        $startSteamMonthly = null;
        $endSteamMonthly   = null;
        if ($yearSteamMonthly) {
            $startSteamMonthly = $monthSteamMonthly ? "{$yearSteamMonthly}-{$monthSteamMonthly}-01" : "{$yearSteamMonthly}-01-01";
            $endSteamMonthly   = $monthSteamMonthly
                ? Carbon::createFromDate($yearSteamMonthly, (int)$monthSteamMonthly, 1)->endOfMonth()->toDateString()
                : "{$yearSteamMonthly}-12-31";
        }

        $startBbWeekly = $filtered ? $request->get('start_bb_weekly') : null;
        $endBbWeekly   = $filtered ? $request->get('end_bb_weekly') : null;

        // Monthly BB Date Range
        $yearBbMonthly  = $filtered ? $request->get('year_bb_monthly') : null;
        $monthBbMonthly = $filtered ? $request->get('month_bb_monthly') : null;
        $startBbMonthly = null;
        $endBbMonthly   = null;
        if ($yearBbMonthly) {
            $startBbMonthly = $monthBbMonthly ? "{$yearBbMonthly}-{$monthBbMonthly}-01" : "{$yearBbMonthly}-01-01";
            $endBbMonthly   = $monthBbMonthly
                ? Carbon::createFromDate($yearBbMonthly, (int)$monthBbMonthly, 1)->endOfMonth()->toDateString()
                : "{$yearBbMonthly}-12-31";
        }

        $spreadsheet = new Spreadsheet();
        $isFirstSheet = true;

        // Helper to format subtitle
        $makeSubtitle = function ($start, $end) {
            if ($start && $end) {
                return 'Periode: ' . Carbon::parse($start)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($end)->translatedFormat('d M Y');
            } elseif ($start) {
                return 'Periode Mulai: ' . Carbon::parse($start)->translatedFormat('d M Y');
            } elseif ($end) {
                return 'Periode Sampai: ' . Carbon::parse($end)->translatedFormat('d M Y');
            }
            return 'Periode: Semua Data Historis';
        };

        // ----------------------------------------------------
        // Sheet 1: Batu Bara / Steam
        // ----------------------------------------------------
        if ($scope === 'all' || $scope === 'bb_steam') {
            $sheet = $isFirstSheet ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $isFirstSheet = false;

            $bbSteamData = $this->queryBatuBaraSteam($startBBSteam, $endBBSteam);
            $rows = [];
            $no = 1;
            foreach ($bbSteamData as $item) {
                $rasio = (float) $item['rasio'];
                $status = $rasio > 175 ? 'Melebihi Ambang' : 'Normal';
                $rows[] = [
                    $no++,
                    $item['date'],
                    (float) $item['batu_bara'],
                    (float) $item['steam'],
                    round($rasio, 2),
                    175,
                    $status
                ];
            }

            $this->applySheetStyles(
                $sheet,
                'Batu Bara - Steam',
                'DASHBOARD KPI BOILER - BATU BARA / STEAM',
                $makeSubtitle($startBBSteam, $endBBSteam),
                ['No', 'Tanggal', 'Batu Bara (Ton)', 'Steam (m³)', 'Rasio (BB/Steam x 1000)', 'Batas Ambang', 'Status Ambang'],
                $rows,
                [
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_CENTER
                ],
                [
                    '#,##0',
                    null,
                    '#,##0.00',
                    '#,##0.00',
                    '#,##0.00',
                    '#,##0',
                    null
                ],
                '006D77' // teal color
            );
        }

        // ----------------------------------------------------
        // Sheet 2: Kondensat
        // ----------------------------------------------------
        if ($scope === 'all' || $scope === 'kondensat') {
            $sheet = $isFirstSheet ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $isFirstSheet = false;

            $kondensatData = $this->queryKondensat($startKondensat, $endKondensat);
            $rows = [];
            $no = 1;
            foreach ($kondensatData as $item) {
                $rows[] = [
                    $no++,
                    $item['date'],
                    (float) $item['pemakaian_air'],
                    (float) $item['feed_tank_awal'],
                    (float) $item['feed_tank_akhir'],
                    round((float) $item['kondensat'], 2)
                ];
            }

            $this->applySheetStyles(
                $sheet,
                'Kondensat',
                'DASHBOARD KPI BOILER - KONDENSAT',
                $makeSubtitle($startKondensat, $endKondensat),
                ['No', 'Tanggal', 'Pemakaian Air (m³)', 'Feed Tank Awal (cm)', 'Feed Tank Akhir (cm)', 'Kondensat (%)'],
                $rows,
                [
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_RIGHT
                ],
                [
                    '#,##0',
                    null,
                    '#,##0.00',
                    '#,##0.00',
                    '#,##0.00',
                    '0.00"%"'
                ],
                '8338EC' // purple color
            );
        }

        // ----------------------------------------------------
        // Sheet 3: Steam / Finish Goods (Weekly)
        // ----------------------------------------------------
        if ($scope === 'all' || $scope === 'steam_fg') {
            $sheet = $isFirstSheet ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $isFirstSheet = false;

            $steamWeeklyData = $this->querySteamFgWeekly($startSteamWeekly, $endSteamWeekly, false);
            $rows = [];
            $no = 1;
            foreach ($steamWeeklyData as $item) {
                $rows[] = [
                    $no++,
                    $item['week_start'],
                    $item['week_end'],
                    (float) $item['steam'],
                    (float) $item['finish_goods'],
                    round((float) $item['rasio'], 2),
                    $item['source']
                ];
            }

            $this->applySheetStyles(
                $sheet,
                'Steam - FG (Weekly)',
                'DASHBOARD KPI BOILER - STEAM / FINISH GOODS (WEEKLY)',
                $makeSubtitle($startSteamWeekly, $endSteamWeekly),
                ['No', 'Periode Awal', 'Periode Akhir', 'Steam (m³)', 'Finish Goods (Ton)', 'Rasio (Steam/FG x 10)', 'Sumber Data'],
                $rows,
                [
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_CENTER
                ],
                [
                    '#,##0',
                    null,
                    null,
                    '#,##0.00',
                    '#,##0.00',
                    '#,##0.00',
                    null
                ],
                '1D3557' // dark navy
            );
        }

        // ----------------------------------------------------
        // Sheet 4: Steam / Finish Goods (Monthly)
        // ----------------------------------------------------
        if ($scope === 'all' || $scope === 'steam_fg') {
            $sheet = $isFirstSheet ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $isFirstSheet = false;

            $steamMonthlyData = $this->querySteamFgMonthly($startSteamMonthly, $endSteamMonthly, false);
            $rows = [];
            $no = 1;
            foreach ($steamMonthlyData as $item) {
                $rows[] = [
                    $no++,
                    $item['month'],
                    (float) $item['steam'],
                    (float) $item['finish_goods'],
                    round((float) $item['rasio'], 2),
                    $item['source_steam'],
                    $item['source_fg']
                ];
            }

            $this->applySheetStyles(
                $sheet,
                'Steam - FG (Monthly)',
                'DASHBOARD KPI BOILER - STEAM / FINISH GOODS (MONTHLY)',
                $makeSubtitle($startSteamMonthly, $endSteamMonthly),
                ['No', 'Bulan', 'Steam (m³)', 'Finish Goods (Ton)', 'Rasio (Steam/FG x 10)', 'Sumber Steam', 'Sumber FG'],
                $rows,
                [
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_CENTER
                ],
                [
                    '#,##0',
                    null,
                    '#,##0.00',
                    '#,##0.00',
                    '#,##0.00',
                    null,
                    null
                ],
                '2A6F97' // medium navy/blue
            );
        }

        // ----------------------------------------------------
        // Sheet 5: Batu Bara / Finish Goods (Weekly)
        // ----------------------------------------------------
        if ($scope === 'all' || $scope === 'bb_fg') {
            $sheet = $isFirstSheet ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $isFirstSheet = false;

            $bbWeeklyData = $this->queryBatuBaraFgWeekly($startBbWeekly, $endBbWeekly, false);
            $rows = [];
            $no = 1;
            foreach ($bbWeeklyData as $item) {
                $rows[] = [
                    $no++,
                    $item['week_start'],
                    $item['week_end'],
                    (float) $item['batu_bara'],
                    (float) $item['finish_goods'],
                    round((float) $item['rasio'], 2),
                    $item['source']
                ];
            }

            $this->applySheetStyles(
                $sheet,
                'BB - FG (Weekly)',
                'DASHBOARD KPI BOILER - BATU BARA / FINISH GOODS (WEEKLY)',
                $makeSubtitle($startBbWeekly, $endBbWeekly),
                ['No', 'Periode Awal', 'Periode Akhir', 'Batu Bara (Ton)', 'Finish Goods (Ton)', 'Rasio (BB/FG x 1000)', 'Sumber Data'],
                $rows,
                [
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_CENTER
                ],
                [
                    '#,##0',
                    null,
                    null,
                    '#,##0.00',
                    '#,##0.00',
                    '#,##0.00',
                    null
                ],
                'D97706' // amber/orange
            );
        }

        // ----------------------------------------------------
        // Sheet 6: Batu Bara / Finish Goods (Monthly)
        // ----------------------------------------------------
        if ($scope === 'all' || $scope === 'bb_fg') {
            $sheet = $isFirstSheet ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $isFirstSheet = false;

            $bbMonthlyData = $this->queryBatuBaraFgMonthly($startBbMonthly, $endBbMonthly, false);
            $rows = [];
            $no = 1;
            foreach ($bbMonthlyData as $item) {
                $rows[] = [
                    $no++,
                    $item['month'],
                    (float) $item['batu_bara'],
                    (float) $item['finish_goods'],
                    round((float) $item['rasio'], 2),
                    $item['source_batubara'],
                    $item['source_fg']
                ];
            }

            $this->applySheetStyles(
                $sheet,
                'BB - FG (Monthly)',
                'DASHBOARD KPI BOILER - BATU BARA / FINISH GOODS (MONTHLY)',
                $makeSubtitle($startBbMonthly, $endBbMonthly),
                ['No', 'Bulan', 'Batu Bara (Ton)', 'Finish Goods (Ton)', 'Rasio (BB/FG x 1000)', 'Sumber Batu Bara', 'Sumber FG'],
                $rows,
                [
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_RIGHT,
                    Alignment::HORIZONTAL_CENTER,
                    Alignment::HORIZONTAL_CENTER
                ],
                [
                    '#,##0',
                    null,
                    '#,##0.00',
                    '#,##0.00',
                    '#,##0.00',
                    null,
                    null
                ],
                'C2410C' // darker amber/rust
            );
        }

        // Set active sheet to the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Download Response
        $scopeName = ($scope === 'all') ? 'Semua_Chart' : str_replace('_', '-', strtoupper($scope));
        $filename = 'KPI_Boiler_' . $scopeName . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $writer->save('php://output');
        exit;
    }

    /**
     * Helper to apply styling to an individual worksheet
     */
    private function applySheetStyles($sheet, $sheetTitle, $reportTitle, $subtitle, $headers, $dataRows, $colAlignments = [], $colFormats = [], $headerColor = '1D3557')
    {
        $sheet->setTitle($sheetTitle);
        $sheet->setShowGridlines(true);
        $sheet->freezePane('A5');

        $totalCols = count($headers);
        $lastColLetter = Coordinate::stringFromColumnIndex($totalCols);

        // Title Block (Row 1)
        $sheet->setCellValue('A1', $reportTitle);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF1D3557'));

        // Subtitle Block (Row 2)
        $sheet->setCellValue('A2', $subtitle . ' | Tanggal Unduh: ' . Carbon::now()->translatedFormat('d F Y H:i') . ' WIB');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF555555'));

        // Header Row (Row 4)
        $headerRow = 4;
        foreach ($headers as $idx => $headerText) {
            $colLetter = Coordinate::stringFromColumnIndex($idx + 1);
            $sheet->setCellValue($colLetter . $headerRow, $headerText);
        }

        // Header Styling
        $headerRange = 'A' . $headerRow . ':' . $lastColLetter . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $headerColor],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        // Data Rows (Row 5+)
        $currentRow = 5;
        foreach ($dataRows as $rowIdx => $row) {
            $isEven = ($rowIdx % 2 === 0);
            foreach ($row as $colIdx => $val) {
                $colLetter = Coordinate::stringFromColumnIndex($colIdx + 1);
                $cellCoord = $colLetter . $currentRow;

                if (is_numeric($val)) {
                    $sheet->setCellValueExplicit($cellCoord, $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                } else {
                    $sheet->setCellValue($cellCoord, $val);
                }

                $align = $colAlignments[$colIdx] ?? (is_numeric($val) ? Alignment::HORIZONTAL_RIGHT : Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle($cellCoord)->getAlignment()->setHorizontal($align)->setVertical(Alignment::VERTICAL_CENTER);

                if (isset($colFormats[$colIdx]) && $colFormats[$colIdx] !== null) {
                    $sheet->getStyle($cellCoord)->getNumberFormat()->setFormatCode($colFormats[$colIdx]);
                }

                $sheet->getStyle($cellCoord)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D1D5DB');

                if (!$isEven) {
                    $sheet->getStyle($cellCoord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
                }
            }
            $sheet->getRowDimension($currentRow)->setRowHeight(20);
            $currentRow++;
        }

        // Auto-fit column widths
        for ($i = 1; $i <= $totalCols; $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
    }
}
