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

    public function getSteamFg(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;

        // Ambil data daily steam
        $dailyQuery = BoilerModel::query();

        if ($start) {
            $dailyQuery->whereDate('date', '>=', $start);
        }
        if ($end) {
            $dailyQuery->whereDate('date', '<=', $end);
        }

        $dailyData = $dailyQuery->orderBy('date', 'asc')->get();

        if ($dailyData->isEmpty()) {
            return response()->json([
                "status"  => "success",
                "message" => "Tidak ada data steam",
                "data"    => []
            ]);
        }

        // Ambil FG weekly sebagai acuan utama
        $fgWeeklyQuery = KpiModel::where('periode_tipe', 'weekly');

        if ($start) {
            $fgWeeklyQuery->where('end_date', '>=', $start);
        }
        if ($end) {
            $fgWeeklyQuery->where('start_date', '<=', $end);
        }

        $fgWeekly = $fgWeeklyQuery->orderBy('start_date')->get();

        $result = [];

        foreach ($fgWeekly as $week) {
            $weekStart = $week->start_date;
            $weekEnd   = $week->end_date;
            $fgValue   = (float) $week->finish_goods;

            // Ambil semua data daily yang masuk dalam rentang minggu ini
            $steamInWeek = $dailyData->filter(function ($item) use ($weekStart, $weekEnd) {
                return $item->date >= $weekStart && $item->date <= $weekEnd;
            })->sum('steam');

            $totalSteam = (float) $steamInWeek;
            $rasio = $fgValue > 0 ? ($totalSteam / $fgValue) * 1000 : 0;

            $result[] = [
                'week_start'    => $weekStart,
                'week_end'      => $weekEnd,
                'steam'         => round($totalSteam, 2),
                'finish_goods'  => $fgValue,
                'rasio'         => round($rasio, 4),
            ];
        }

        return response()->json([
            "status"  => "success",
            "message" => "Data steam kumulatif per minggu & FG berhasil diambil",
            "data"    => $result
        ]);
    }

    public function getBatuBaraFg(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;

        // Ambil data daily batu bara
        $dailyQuery = BoilerModel::query();

        if ($start) {
            $dailyQuery->whereDate('date', '>=', $start);
        }
        if ($end) {
            $dailyQuery->whereDate('date', '<=', $end);
        }

        $dailyData = $dailyQuery->orderBy('date', 'asc')->get();

        if ($dailyData->isEmpty()) {
            return response()->json([
                "status"  => "success",
                "message" => "Tidak ada data batu bara",
                "data"    => []
            ]);
        }

        // Ambil FG weekly sebagai acuan
        $fgWeeklyQuery = KpiModel::where('periode_tipe', 'weekly');

        if ($start) {
            $fgWeeklyQuery->where('end_date', '>=', $start);
        }
        if ($end) {
            $fgWeeklyQuery->where('start_date', '<=', $end);
        }

        $fgWeekly = $fgWeeklyQuery->orderBy('start_date')->get();

        $result = [];

        foreach ($fgWeekly as $week) {
            $weekStart = $week->start_date;
            $weekEnd   = $week->end_date;
            $fgValue   = (float) $week->finish_goods;

            // Hitung total batu bara di minggu ini
            $bbInWeek = $dailyData->filter(function ($item) use ($weekStart, $weekEnd) {
                return $item->date >= $weekStart && $item->date <= $weekEnd;
            })->sum('batu_bara');

            $totalBb = (float) $bbInWeek;
            $rasio = $fgValue > 0 ? ($totalBb / $fgValue) * 1000 : 0;

            $result[] = [
                'week_start'    => $weekStart,
                'week_end'      => $weekEnd,
                'batu_bara'     => round($totalBb, 2),
                'finish_goods'  => $fgValue,
                'rasio'         => round($rasio, 4),
            ];
        }

        return response()->json([
            "status"  => "success",
            "message" => "Data batu bara kumulatif per minggu & FG berhasil diambil",
            "data"    => $result
        ]);
    }

    public function getSteamFgMonthly(Request $request)
    {
        $start = $request->start_date; // format YYYY-MM-DD
        $end = $request->end_date;

        // Tentukan rentang bulan berdasarkan start & end
        $startMonth = $start ? substr($start, 0, 7) : null; // YYYY-MM
        $endMonth = $end ? substr($end, 0, 7) : null;

        // Ambil data daily steam dalam rentang
        $dailyQuery = BoilerModel::query();

        if ($start) {
            $dailyQuery->whereDate('date', '>=', $start);
        }
        if ($end) {
            $dailyQuery->whereDate('date', '<=', $end);
        }

        $dailyData = $dailyQuery->orderBy('date', 'asc')->get();

        if ($dailyData->isEmpty()) {
            return response()->json([
                "status"  => "success",
                "message" => "Tidak ada data steam",
                "data"    => []
            ]);
        }

        // Group daily data per bulan (YYYY-MM)
        $steamByMonth = $dailyData->groupBy(function ($item) {
            return substr($item->date, 0, 7); // YYYY-MM
        })->map(function ($items) {
            return $items->sum('steam');
        });

        // Ambil FG Monthly
        $fgMonthlyQuery = KpiModel::where('periode_tipe', 'monthly');

        if ($startMonth) {
            $fgMonthlyQuery->where('month', '>=', $startMonth);
        }
        if ($endMonth) {
            $fgMonthlyQuery->where('month', '<=', $endMonth);
        }

        $fgMonthly = $fgMonthlyQuery->pluck('finish_goods', 'month')->map(fn($val) => (float) $val);

        // Ambil semua FG Weekly untuk fallback (jika monthly kosong)
        $fgWeeklyAll = KpiModel::where('periode_tipe', 'weekly')
            ->when($start, fn($q) => $q->where('end_date', '>=', $start))
            ->when($end, fn($q) => $q->where('start_date', '<=', $end))
            ->get();

        // Hitung total FG weekly per bulan sebagai fallback
        $fgWeeklyByMonth = $fgWeeklyAll->groupBy(function ($item) {
            // Ambil bulan dari start_date atau end_date (biasanya sama)
            return substr($item->start_date, 0, 7);
        })->map(function ($weeks) {
            return $weeks->sum('finish_goods');
        });

        $result = [];

        // Loop semua bulan yang ada data steam
        foreach ($steamByMonth as $month => $totalSteam) {
            $fgValue = $fgMonthly->get($month); // Prioritas: monthly

            // Jika tidak ada monthly, pakai total weekly di bulan itu
            if (is_null($fgValue) || $fgValue == 0) {
                $fgValue = $fgWeeklyByMonth->get($month, 0);
            }

            $totalSteam = (float) $totalSteam;
            $rasio = $fgValue > 0 ? ($totalSteam / $fgValue) * 1000 : 0;

            $result[] = [
                'month'         => $month, // YYYY-MM
                'steam'         => round($totalSteam, 2),
                'finish_goods'  => (float) $fgValue,
                'rasio'         => round($rasio, 4),
            ];
        }

        // Urutkan berdasarkan bulan
        usort($result, function ($a, $b) {
            return $a['month'] <=> $b['month'];
        });

        return response()->json([
            "status"  => "success",
            "message" => "Data steam kumulatif bulanan & FG berhasil diambil",
            "data"    => $result
        ]);
    }

    public function getBatuBaraFgMonthly(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;

        $startMonth = $start ? substr($start, 0, 7) : null;
        $endMonth = $end ? substr($end, 0, 7) : null;

        // Ambil data daily batu bara
        $dailyQuery = BoilerModel::query();

        if ($start) {
            $dailyQuery->whereDate('date', '>=', $start);
        }
        if ($end) {
            $dailyQuery->whereDate('date', '<=', $end);
        }

        $dailyData = $dailyQuery->orderBy('date', 'asc')->get();

        if ($dailyData->isEmpty()) {
            return response()->json([
                "status"  => "success",
                "message" => "Tidak ada data batu bara",
                "data"    => []
            ]);
        }

        // Group per bulan
        $bbByMonth = $dailyData->groupBy(function ($item) {
            return substr($item->date, 0, 7);
        })->map(function ($items) {
            return $items->sum('batu_bara');
        });

        // FG Monthly
        $fgMonthlyQuery = KpiModel::where('periode_tipe', 'monthly');

        if ($startMonth) {
            $fgMonthlyQuery->where('month', '>=', $startMonth);
        }
        if ($endMonth) {
            $fgMonthlyQuery->where('month', '<=', $endMonth);
        }

        $fgMonthly = $fgMonthlyQuery->pluck('finish_goods', 'month')->map(fn($val) => (float) $val);

        // FG Weekly untuk fallback
        $fgWeeklyAll = KpiModel::where('periode_tipe', 'weekly')
            ->when($start, fn($q) => $q->where('end_date', '>=', $start))
            ->when($end, fn($q) => $q->where('start_date', '<=', $end))
            ->get();

        $fgWeeklyByMonth = $fgWeeklyAll->groupBy(function ($item) {
            return substr($item->start_date, 0, 7);
        })->map(function ($weeks) {
            return $weeks->sum('finish_goods');
        });

        $result = [];

        foreach ($bbByMonth as $month => $totalBb) {
            $fgValue = $fgMonthly->get($month);

            if (is_null($fgValue) || $fgValue == 0) {
                $fgValue = $fgWeeklyByMonth->get($month, 0);
            }

            $totalBb = (float) $totalBb;
            $rasio = $fgValue > 0 ? ($totalBb / $fgValue) * 1000 : 0;

            $result[] = [
                'month'         => $month,
                'batu_bara'     => round($totalBb, 2),
                'finish_goods'  => (float) $fgValue,
                'rasio'         => round($rasio, 4),
            ];
        }

        // Sort by month
        usort($result, function ($a, $b) {
            return $a['month'] <=> $b['month'];
        });

        return response()->json([
            "status"  => "success",
            "message" => "Data batu bara kumulatif bulanan & FG berhasil diambil",
            "data"    => $result
        ]);
    }
}
