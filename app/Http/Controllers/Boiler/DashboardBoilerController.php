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

    public function getBatuBaraSteam()
    {
        $weeklyGrouped = BoilerModel::where('periode_tipe', 'weekly')
            ->get()
            ->map(function ($item) {

                $month = substr($item->start_date, 0, 7);

                return [
                    'month' => $month,
                    'batu_bara' => (float) $item->batu_bara,
                    'steam' => (float) $item->steam,
                ];
            })
            ->groupBy('month')
            ->map(function ($group) {
                return [
                    'month' => $group->first()['month'],
                    'total_batubara' => $group->sum('batu_bara'),
                    'total_steam' => $group->sum('steam'),
                ];
            });

        // Ambil monthly
        $monthly = BoilerModel::where('periode_tipe', 'monthly')
            ->selectRaw('month, SUM(batu_bara) as total_batubara, SUM(steam) as total_steam')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Gabungkan
        $finalData = collect([]);

        // Semua bulan yang ada di weekly maupun monthly
        $allMonths = $weeklyGrouped->keys()
            ->merge($monthly->keys())
            ->unique()
            ->sort();

        foreach ($allMonths as $month) {

            if ($monthly->has($month)) {
                // Monthly punya prioritas
                $row = $monthly[$month];
                $total_bb = (float) $row->total_batubara;
                $total_steam = (float) $row->total_steam;
            } else {
                // Jika monthly tidak ada → ambil weekly
                $row = $weeklyGrouped[$month];
                $total_bb = (float) $row['total_batubara'];
                $total_steam = (float) $row['total_steam'];
            }

            // Hitung rasio
            $rasio = $total_steam > 0 ? ($total_bb / $total_steam) * 1000 : 0;

            $finalData->push([
                'month' => $month,
                'total_batubara' => $total_bb,
                'total_steam' => $total_steam,
                'rasio' => $rasio,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data Batubara & Steam berhasil diambil',
            'data' => $finalData
        ]);
    }

    public function getSteamFg()
    {
        $steamWeekly = BoilerModel::where('periode_tipe', 'weekly')
            ->get()
            ->map(function ($item) {

                $month = substr($item->start_date, 0, 7);

                return [
                    'month' => $month,
                    'steam' => (float) $item->steam
                ];
            })
            ->groupBy('month')
            ->map(function ($group) {
                return [
                    'month' => $group->first()['month'],
                    'steam' => $group->sum('steam')
                ];
            });

        $steamMonthly = BoilerModel::where('periode_tipe', 'monthly')
            ->get()
            ->keyBy('month')
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'steam' => (float) $item->steam
                ];
            });

        $fgWeekly = KpiModel::where('periode_tipe', 'weekly')
            ->get()
            ->map(function ($item) {

                $month = substr($item->start_date, 0, 7);

                return [
                    'month' => $month,
                    'finish_goods' => (float) $item->finish_goods
                ];
            })
            ->groupBy('month')
            ->map(function ($group) {
                return [
                    'month' => $group->first()['month'],
                    'finish_goods' => $group->sum('finish_goods')
                ];
            });

        $fgMonthly = KpiModel::where('periode_tipe', 'monthly')
            ->get()
            ->keyBy('month')
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'finish_goods' => (float) $item->finish_goods
                ];
            });

        $allMonths = $steamWeekly->keys()
            ->merge($steamMonthly->keys())
            ->merge($fgWeekly->keys())
            ->merge($fgMonthly->keys())
            ->unique()
            ->sort();

        $final = collect([]);

        foreach ($allMonths as $month) {

            if ($steamMonthly->has($month)) {
                $steam = $steamMonthly[$month]['steam'];
            } else {
                $steam = $steamWeekly->has($month) ? $steamWeekly[$month]['steam'] : 0;
            }

            if ($fgMonthly->has($month)) {
                $fg = $fgMonthly[$month]['finish_goods'];
            } else {
                $fg = $fgWeekly->has($month) ? $fgWeekly[$month]['finish_goods'] : 0;
            }

            // Rasio
            $rasio = $fg > 0 ? ($steam / $fg) : 0;

            $final->push([
                'month' => $month,
                'steam' => $steam,
                'finish_goods' => $fg,
                'rasio' => $rasio,
            ]);
        }

        return response()->json([
            "status" => "success",
            "message" => "Data steam & FG berhasil diambil",
            "data" => $final
        ]);
    }

    public function getBatuBaraFg()
    {
        $bbWeekly = BoilerModel::where('periode_tipe', 'weekly')
            ->get()
            ->map(function ($item) {

                // Ambil bulan dari start_date: "2025-11-17" → "2025-11"
                $month = substr($item->start_date, 0, 7);

                return [
                    'month' => $month,
                    'batu_bara' => (float) $item->batu_bara
                ];
            })
            ->groupBy('month')
            ->map(function ($group) {
                return [
                    'month' => $group->first()['month'],
                    'batu_bara' => $group->sum('batu_bara')
                ];
            });

        $bbMonthly = BoilerModel::where('periode_tipe', 'monthly')
            ->get()
            ->keyBy('month')
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'batu_bara' => (float) $item->batu_bara
                ];
            });

        $fgWeekly = KpiModel::where('periode_tipe', 'weekly')
            ->get()
            ->map(function ($item) {

                $month = substr($item->start_date, 0, 7);

                return [
                    'month' => $month,
                    'finish_goods' => (float) $item->finish_goods
                ];
            })
            ->groupBy('month')
            ->map(function ($group) {
                return [
                    'month' => $group->first()['month'],
                    'finish_goods' => $group->sum('finish_goods')
                ];
            });

        $fgMonthly = KpiModel::where('periode_tipe', 'monthly')
            ->get()
            ->keyBy('month')
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'finish_goods' => (float) $item->finish_goods
                ];
            });

        $allMonths = $bbWeekly->keys()
            ->merge($bbMonthly->keys())
            ->merge($fgWeekly->keys())
            ->merge($fgMonthly->keys())
            ->unique()
            ->sort();

        $final = collect([]);

        foreach ($allMonths as $month) {

            // Batu bara → monthly > weekly > 0
            if ($bbMonthly->has($month)) {
                $batuBara = $bbMonthly[$month]['batu_bara'];
            } else {
                $batuBara = $bbWeekly->has($month) ? $bbWeekly[$month]['batu_bara'] : 0;
            }

            // Finish goods → monthly > weekly > 0
            if ($fgMonthly->has($month)) {
                $finishGoods = $fgMonthly[$month]['finish_goods'];
            } else {
                $finishGoods = $fgWeekly->has($month) ? $fgWeekly[$month]['finish_goods'] : 0;
            }

            // Rasio
            $rasio = $finishGoods > 0 ? ($batuBara / $finishGoods) : 0;

            $final->push([
                'month'        => $month,
                'batu_bara'    => $batuBara,
                'finish_goods' => $finishGoods,
                'rasio'        => $rasio
            ]);
        }

        return response()->json([
            "status"  => "success",
            "message" => "Data Batu Bara & FG berhasil diambil",
            "data"    => $final
        ]);
    }
}
