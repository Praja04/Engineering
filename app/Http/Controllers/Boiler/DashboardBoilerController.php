<?php

namespace App\Http\Controllers\Boiler;

use Illuminate\Http\Request;
use App\Models\Utility\KpiModel;
use App\Models\Boiler\BoilerModel;
use App\Http\Controllers\Controller;

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

    public function getBatuBaraSteam(Request $request)
    {
        $query = BoilerModel::orderBy('date', 'asc');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $data = $query->get()->map(function ($item) {
            $steam = (float) $item->steam;
            $bb = (float) $item->batu_bara;

            return [
                'date' => $item->date,
                'steam' => $steam,
                'batu_bara' => $bb,
                'rasio' => $steam > 0 ? ($bb / $steam) * 1000 : 0
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getKondensat(Request $request)
    {
        $query = BoilerModel::orderBy('date', 'asc');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $data = $query->get()->map(function ($item) {
            return [
                'date' => $item->date,
                'kondensat' => (float) $item->kondensat,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }


    public function getSteamFg(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        // 1. Ambil semua data harian steam (selalu siapkan sebagai fallback)
        $dailyQuery = BoilerModel::query();
        if ($start) $dailyQuery->whereDate('date', '>=', $start);
        if ($end)   $dailyQuery->whereDate('date', '<=', $end);
        $dailySteamData = $dailyQuery->orderBy('date', 'asc')->get(['date', 'steam']);

        if ($dailySteamData->isEmpty() && !$request->has('start_date')) {
            return response()->json([
                "status"  => "success",
                "message" => "Tidak ada data steam",
                "data"    => []
            ]);
        }

        // 2. Ambil semua KPI weekly yang overlap dengan range
        $fgWeeklyQuery = KpiModel::where('periode_tipe', 'weekly');
        if ($start) $fgWeeklyQuery->where('end_date', '>=', $start);
        if ($end)   $fgWeeklyQuery->where('start_date', '<=', $end);
        $fgWeekly = $fgWeeklyQuery->orderBy('start_date')->get();

        $result = [];

        foreach ($fgWeekly as $week) {
            $weekStart = $week->start_date;
            $weekEnd   = $week->end_date;
            $fgValue   = (float) $week->finish_goods;

            // Prioritas: pakai nilai manual dari KPI jika ada
            $steamFromKpi = $week->steam;

            if ($steamFromKpi !== null && $steamFromKpi > 0) {
                $totalSteam = (float) $steamFromKpi;
                $source     = 'kpi';
            } else {
                // Fallback ke akumulasi harian
                $steamInWeek = $dailySteamData
                    ->filter(fn($item) => $item->date >= $weekStart && $item->date <= $weekEnd)
                    ->sum('steam');
                $totalSteam = (float) $steamInWeek;
                $source     = 'daily';
            }

            $rasio = $fgValue > 0 ? ($totalSteam / $fgValue) * 10 : 0;

            $result[] = [
                'week_start'    => $weekStart,
                'week_end'      => $weekEnd,
                'steam'         => round($totalSteam, 2),
                'finish_goods'  => $fgValue,
                'rasio'         => round($rasio, 2),
                'source'        => $source,   // opsional: untuk debug atau tampil di frontend
            ];
        }

        return response()->json([
            "status"  => "success",
            "message" => "Data steam per minggu & FG berhasil diambil",
            "data"    => $result
        ]);
    }

    public function getBatuBaraFg(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        // 1. Ambil data harian batu bara (fallback)
        $dailyQuery = BoilerModel::query();
        if ($start) $dailyQuery->whereDate('date', '>=', $start);
        if ($end)   $dailyQuery->whereDate('date', '<=', $end);
        $dailyBbData = $dailyQuery->orderBy('date', 'asc')->get(['date', 'batu_bara']);

        if ($dailyBbData->isEmpty() && !$request->has('start_date')) {
            return response()->json([
                "status"  => "success",
                "message" => "Tidak ada data batu bara",
                "data"    => []
            ]);
        }

        // 2. Ambil KPI weekly
        $fgWeeklyQuery = KpiModel::where('periode_tipe', 'weekly');
        if ($start) $fgWeeklyQuery->where('end_date', '>=', $start);
        if ($end)   $fgWeeklyQuery->where('start_date', '<=', $end);
        $fgWeekly = $fgWeeklyQuery->orderBy('start_date')->get();

        $result = [];

        foreach ($fgWeekly as $week) {
            $weekStart = $week->start_date;
            $weekEnd   = $week->end_date;
            $fgValue   = (float) $week->finish_goods;

            // Prioritas: pakai nilai manual dari KPI jika ada
            $bbFromKpi = $week->batubara;

            if ($bbFromKpi !== null && $bbFromKpi > 0) {
                $totalBb = (float) $bbFromKpi;
                $source  = 'kpi';
            } else {
                // Fallback ke harian
                $bbInWeek = $dailyBbData
                    ->filter(fn($item) => $item->date >= $weekStart && $item->date <= $weekEnd)
                    ->sum('batu_bara');
                $totalBb = (float) $bbInWeek;
                $source  = 'daily';
            }

            $rasio = $fgValue > 0 ? ($totalBb / $fgValue) * 1000 : 0;

            $result[] = [
                'week_start'    => $weekStart,
                'week_end'      => $weekEnd,
                'batu_bara'     => round($totalBb, 2),
                'finish_goods'  => $fgValue,
                'rasio'         => round($rasio, 2),
                'source'        => $source,   // opsional
            ];
        }

        return response()->json([
            "status"  => "success",
            "message" => "Data batu bara per minggu & FG berhasil diambil",
            "data"    => $result
        ]);
    }

    public function getSteamFgMonthly(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        $startMonth = $start ? substr($start, 0, 7) : null;
        $endMonth   = $end   ? substr($end,   0, 7) : null;

        // 1. Ambil data harian steam (selalu siapkan sebagai fallback)
        $dailyQuery = BoilerModel::query();
        if ($start) $dailyQuery->whereDate('date', '>=', $start);
        if ($end)   $dailyQuery->whereDate('date', '<=', $end);
        $dailyData = $dailyQuery->orderBy('date', 'asc')->get(['date', 'steam']);

        if ($dailyData->isEmpty() && !$request->has('start_date')) {
            return response()->json([
                "status"  => "success",
                "message" => "Tidak ada data steam",
                "data"    => []
            ]);
        }

        // Group steam per bulan (fallback)
        $steamByMonth = $dailyData->groupBy(fn($item) => substr($item->date, 0, 7))
            ->map(fn($items) => $items->sum('steam'));

        // 2. Ambil KPI Monthly (prioritas utama untuk steam & FG)
        $monthlyQuery = KpiModel::where('periode_tipe', 'monthly');
        if ($startMonth) $monthlyQuery->where('month', '>=', $startMonth);
        if ($endMonth)   $monthlyQuery->where('month', '<=', $endMonth);
        $kpiMonthly = $monthlyQuery->get(['month', 'finish_goods', 'steam']);

        // 3. Ambil FG Weekly sebagai fallback FG (jika monthly FG kosong)
        $weeklyQuery = KpiModel::where('periode_tipe', 'weekly')
            ->when($start, fn($q) => $q->where('end_date', '>=', $start))
            ->when($end,   fn($q) => $q->where('start_date', '<=', $end));
        $fgWeeklyByMonth = $weeklyQuery->get()
            ->groupBy(fn($item) => substr($item->start_date, 0, 7))
            ->map(fn($weeks) => $weeks->sum('finish_goods'));

        $result = [];

        // Ambil semua bulan unik dari steam harian (atau dari KPI monthly kalau lebih lengkap)
        $allMonths = $steamByMonth->keys()->merge($kpiMonthly->pluck('month'))->unique()->sort();

        foreach ($allMonths as $month) {
            // Prioritas steam: dari KPI monthly jika ada
            $monthlyRecord = $kpiMonthly->firstWhere('month', $month);
            $steamFromKpi  = $monthlyRecord ? $monthlyRecord->steam : null;

            if ($steamFromKpi !== null && $steamFromKpi > 0) {
                $totalSteam = (float) $steamFromKpi;
                $sourceSteam = 'kpi_monthly';
            } else {
                // Fallback ke akumulasi harian
                $totalSteam = (float) ($steamByMonth->get($month, 0));
                $sourceSteam = 'daily';
            }

            // FG: prioritas monthly
            $fgValue = $monthlyRecord ? (float) $monthlyRecord->finish_goods : null;

            if (is_null($fgValue) || $fgValue == 0) {
                $fgValue = $fgWeeklyByMonth->get($month, 0);
                $sourceFg = 'weekly_fallback';
            } else {
                $sourceFg = 'monthly';
            }

            $rasio = $fgValue > 0 ? ($totalSteam / $fgValue) * 1000 : 0;

            $result[] = [
                'month'         => $month,
                'steam'         => round($totalSteam, 2),
                'finish_goods'  => $fgValue,
                'rasio'         => round($rasio, 2),
                'source_steam'  => $sourceSteam,   // opsional, untuk debug/frontend
                'source_fg'     => $sourceFg,
            ];
        }

        return response()->json([
            "status"  => "success",
            "message" => "Data steam kumulatif bulanan & FG berhasil diambil",
            "data"    => $result
        ]);
    }

    public function getBatuBaraFgMonthly(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        $startMonth = $start ? substr($start, 0, 7) : null;
        $endMonth   = $end   ? substr($end,   0, 7) : null;

        // Data harian batu bara (fallback)
        $dailyQuery = BoilerModel::query();
        if ($start) $dailyQuery->whereDate('date', '>=', $start);
        if ($end)   $dailyQuery->whereDate('date', '<=', $end);
        $dailyData = $dailyQuery->orderBy('date', 'asc')->get(['date', 'batu_bara']);

        if ($dailyData->isEmpty() && !$request->has('start_date')) {
            return response()->json([
                "status"  => "success",
                "message" => "Tidak ada data batu bara",
                "data"    => []
            ]);
        }

        $bbByMonth = $dailyData->groupBy(fn($item) => substr($item->date, 0, 7))
            ->map(fn($items) => $items->sum('batu_bara'));

        // KPI Monthly (prioritas batubara & FG)
        $monthlyQuery = KpiModel::where('periode_tipe', 'monthly');
        if ($startMonth) $monthlyQuery->where('month', '>=', $startMonth);
        if ($endMonth)   $monthlyQuery->where('month', '<=', $endMonth);
        $kpiMonthly = $monthlyQuery->get(['month', 'finish_goods', 'batubara']);

        // FG Weekly fallback
        $weeklyQuery = KpiModel::where('periode_tipe', 'weekly')
            ->when($start, fn($q) => $q->where('end_date', '>=', $start))
            ->when($end,   fn($q) => $q->where('start_date', '<=', $end));
        $fgWeeklyByMonth = $weeklyQuery->get()
            ->groupBy(fn($item) => substr($item->start_date, 0, 7))
            ->map(fn($weeks) => $weeks->sum('finish_goods'));

        $result = [];

        $allMonths = $bbByMonth->keys()->merge($kpiMonthly->pluck('month'))->unique()->sort();

        foreach ($allMonths as $month) {
            $monthlyRecord = $kpiMonthly->firstWhere('month', $month);
            $bbFromKpi     = $monthlyRecord ? $monthlyRecord->batubara : null;

            if ($bbFromKpi !== null && $bbFromKpi > 0) {
                $totalBb = (float) $bbFromKpi;
                $sourceBb = 'kpi_monthly';
            } else {
                $totalBb = (float) ($bbByMonth->get($month, 0));
                $sourceBb = 'daily';
            }

            $fgValue = $monthlyRecord ? (float) $monthlyRecord->finish_goods : null;

            if (is_null($fgValue) || $fgValue == 0) {
                $fgValue = $fgWeeklyByMonth->get($month, 0);
                $sourceFg = 'weekly_fallback';
            } else {
                $sourceFg = 'monthly';
            }

            $rasio = $fgValue > 0 ? ($totalBb / $fgValue) * 1000 : 0;

            $result[] = [
                'month'         => $month,
                'batu_bara'     => round($totalBb, 2),
                'finish_goods'  => $fgValue,
                'rasio'         => round($rasio, 2),
                'source_batubara' => $sourceBb,
                'source_fg'       => $sourceFg,
            ];
        }

        return response()->json([
            "status"  => "success",
            "message" => "Data batu bara kumulatif bulanan & FG berhasil diambil",
            "data"    => $result
        ]);
    }
}
