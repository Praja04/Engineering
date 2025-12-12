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

        $steamQuery = BoilerModel::query();

        if ($start) {
            $steamQuery->whereDate('date', '>=', $start);
        }

        if ($end) {
            $steamQuery->whereDate('date', '<=', $end);
        }

        $steamDaily = $steamQuery->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'steam' => (float) $item->steam,
                    'month' => substr($item->date, 0, 7)
                ];
            });

        $fgMonthly = KpiModel::where('periode_tipe', 'monthly')
            ->get()
            ->keyBy('month')
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'fg' => (float) $item->finish_goods
                ];
            });

        $fgWeekly = KpiModel::where('periode_tipe', 'weekly')
            ->get()
            ->map(function ($item) {
                return [
                    'start_date' => $item->start_date,
                    'end_date' => $item->end_date,
                    'fg' => (float) $item->finish_goods
                ];
            });

        $final = collect([]);

        foreach ($steamDaily as $row) {

            $date = $row['date'];
            $month = $row['month'];

            if ($fgMonthly->has($month)) {
                $fgValue = $fgMonthly[$month]['fg'];
            } else {
                $fgValue = 0;
                foreach ($fgWeekly as $week) {
                    if ($date >= $week['start_date'] && $date <= $week['end_date']) {
                        $fgValue = $week['fg'];
                        break;
                    }
                }
            }

            $steam = $row['steam'];
            $rasio = $fgValue > 0 ? ($steam / $fgValue) * 1000 : 0;

            $final->push([
                'date' => $date,
                'steam' => $steam,
                'finish_goods' => $fgValue,
                'rasio' => $rasio,
            ]);
        }

        return response()->json([
            "status" => "success",
            "message" => "Data steam & FG berhasil diambil",
            "data" => $final
        ]);
    }

    public function getBatuBaraFg(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;

        $bbQuery = BoilerModel::query();

        if ($start) {
            $bbQuery->whereDate('date', '>=', $start);
        }

        if ($end) {
            $bbQuery->whereDate('date', '<=', $end);
        }

        $bbDaily = $bbQuery->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'batu_bara' => (float) $item->batu_bara,
                    'month' => substr($item->date, 0, 7)
                ];
            });

        $fgMonthly = KpiModel::where('periode_tipe', 'monthly')
            ->get()
            ->keyBy('month')
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'fg' => (float) $item->finish_goods
                ];
            });

        $fgWeekly = KpiModel::where('periode_tipe', 'weekly')
            ->get()
            ->map(function ($item) {
                return [
                    'start_date'   => $item->start_date,
                    'end_date'     => $item->end_date,
                    'fg'           => (float) $item->finish_goods
                ];
            });

        $final = collect([]);

        foreach ($bbDaily as $row) {

            $date = $row['date'];
            $month = $row['month'];

            if ($fgMonthly->has($month)) {
                $fgValue = $fgMonthly[$month]['fg'];
            } else {
                $fgValue = 0;
                foreach ($fgWeekly as $week) {
                    if ($date >= $week['start_date'] && $date <= $week['end_date']) {
                        $fgValue = $week['fg'];
                        break;
                    }
                }
            }

            $bbValue = $row['batu_bara'];
            $rasio = $fgValue > 0 ? ($bbValue / $fgValue) * 1000 : 0;

            $final->push([
                'date'          => $date,
                'batu_bara'     => $bbValue,
                'finish_goods'  => $fgValue,
                'rasio'         => $rasio
            ]);
        }

        return response()->json([
            "status"  => "success",
            "message" => "Data Batu Bara & FG berhasil diambil",
            "data"    => $final
        ]);
    }
}
