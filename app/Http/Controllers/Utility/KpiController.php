<?php

namespace App\Http\Controllers\Utility;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Utility\KpiModel;
use App\Models\Utility\PemakaianListrikModel;
use App\Http\Controllers\Controller;

class KpiController extends Controller
{
    public function viewForm()
    {
        return view('kpi.form_kpi');
    }

    public function viewData()
    {
        return view('kpi.data_kpi');
    }

    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'periode_tipe' => 'required|in:weekly,monthly',
            'finish_goods' => 'required|numeric|min:0',
            'kecap_matang' => 'required|numeric|min:0',
            'start_date'   => 'nullable|required_if:periode_tipe,weekly|date',
            'end_date'     => 'nullable|required_if:periode_tipe,weekly|date|after_or_equal:start_date',
            'month'        => 'nullable|required_if:periode_tipe,monthly|date_format:Y-m'
        ]);

        $jenis = $request->periode_tipe;

        if ($jenis === 'weekly') {

            $start = Carbon::parse($request->start_date);
            $end   = Carbon::parse($request->end_date);

            if ($start->diffInDays($end) < 6) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode weekly harus minimal 7 hari.'
                ], 422);
            }

            $exists = KpiModel::where('periode_tipe', 'weekly')
                ->where('start_date', $request->start_date)
                ->where('end_date', $request->end_date)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data KPI untuk periode weekly tersebut sudah ada.'
                ], 422);
            }
        }

        if ($jenis === 'monthly') {

            $exists = KpiModel::where('periode_tipe', 'monthly')
                ->where('month', $request->month)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data KPI untuk bulan tersebut sudah ada.'
                ], 422);
            }
        }

        KpiModel::create([
            'periode_tipe' => $jenis,
            'start_date'   => $jenis === 'weekly' ? $request->start_date : null,
            'end_date'     => $jenis === 'weekly' ? $request->end_date : null,
            'month'        => $jenis === 'monthly' ? $request->month : null,
            'finish_goods' => $request->finish_goods,
            'kecap_matang' => $request->kecap_matang,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan!'
        ]);
    }

    public function getData(Request $request)
    {
        $query = KpiModel::query();

        // filter dinamis
        if ($request->periode_tipe) {
            $query->where('periode_tipe', $request->periode_tipe);
        }
        // if ($request->tanggal) {
        //     $query->whereDate('tanggal', $request->tanggal);
        // }

        $data = $query->orderBy('created_at', 'desc')->get();
        return response()->json($data);
    }

    public function show($id)
    {
        $kpi = KpiModel::find($id);

        if (!$kpi) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json(['success' => true, 'data' => $kpi]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'periode_tipe' => 'required|in:weekly,monthly',
            'finish_goods' => 'required|numeric|min:0',
            'kecap_matang' => 'required|numeric|min:0',
            'start_date'   => 'nullable|required_if:periode_tipe,weekly|date',
            'end_date'     => 'nullable|required_if:periode_tipe,weekly|date|after_or_equal:start_date',
            'month'        => 'nullable|required_if:periode_tipe,monthly|date_format:Y-m'
        ]);

        $kpi = KpiModel::find($id);

        if (!$kpi) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ], 404);
        }

        $jenis = $request->periode_tipe;

        $kpi->update([
            'periode_tipe' => $jenis,
            'start_date'   => $jenis === 'weekly' ? $request->start_date : null,
            'end_date'     => $jenis === 'weekly' ? $request->end_date : null,
            'month'        => $jenis === 'monthly' ? $request->month : null,
            'finish_goods' => $request->finish_goods,
            'kecap_matang' => $request->kecap_matang,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $kpi = KpiModel::find($id);

        if (!$kpi) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ], 404);
        }

        $kpi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus!'
        ]);
    }


    //// Api KPI utility Listrik ////
    // public function getKpiListrik(Request $request)
    // {
    //     $today = Carbon::now();
    //     $currentMonth = $today->format('Y-m');

    //     /**
    //      * 1. Ambil KPI:
    //      *    - Prioritas monthly
    //      *    - Jika tidak ada, ambil weekly terbaru
    //      */
    //     $kpiMonthly = KpiModel::where('periode_tipe', 'monthly')
    //     ->where('month', $currentMonth)
    //         ->first();

    //     if ($kpiMonthly) {
    //         $finishGoods   = $kpiMonthly->finish_goods;
    //         $kecapMatang   = $kpiMonthly->kecap_matang;
    //         $sumberKpi = 'monthly';
    //     } else {
    //         $kpiWeekly = KpiModel::where('periode_tipe', 'weekly')
    //         ->whereDate('start_date', '<=', $today)
    //             ->orderBy('end_date', 'desc')
    //             ->first();

    //         if (!$kpiWeekly) {
    //             return response()->json([
    //                 'message' => 'Tidak ada data KPI monthly maupun weekly.'
    //             ], 404);
    //         }

    //         $finishGoods   = $kpiWeekly->finish_goods;
    //         $kecapMatang   = $kpiWeekly->kecap_matang;
    //         $sumberKpi = 'weekly-terbaru';
    //     }

    //     /**
    //      * Validasi agar tidak membagi dengan nol
    //      */
    //     if ($finishGoods <= 0) {
    //         return response()->json([
    //             'message' => 'Finish Goods tidak valid (<=0).'
    //         ], 422);
    //     }

    //     if ($kecapMatang <= 0) {
    //         return response()->json([
    //             'message' => 'Kecap Matang tidak valid (<=0).'
    //         ], 422);
    //     }

    //     /**
    //      * 2. Ambil data listrik bulan berjalan
    //      */
    //     $listrik = PemakaianListrikModel::whereMonth('waktu', $today->month)
    //         ->whereYear('waktu', $today->year)
    //         ->get();

    //     if ($listrik->isEmpty()) {
    //         return response()->json([
    //             'message' => 'Tidak ada data listrik bulan ini.'
    //         ], 404);
    //     }

    //     /**
    //      * 3. Total Produksi SDP:
    //      * SDP: 1,2,3,5,9,10,11
    //      */
    //     $panelProduksi = ['SDP1', 'SDP2', 'SDP3', 'SDP5', 'SDP9', 'SDP10', 'SDP11'];

    //     $totalProduksi = $listrik
    //         ->whereIn('panel_type', $panelProduksi)
    //         ->sum('mwh');

    //     /**
    //      * 4. Total BAS = semua SDP dijumlah
    //      */
    //     $totalBas = $listrik->sum('mwh');

    //     /**
    //      * 5. KPI Perhitungan
    //      */
    //     $kpiProduksi = $totalProduksi / $finishGoods;
    //     $kpiBas = $totalBas / $kecapMatang;

    //     return response()->json([
    //         'periode' => $currentMonth,
    //         'kpi_sumber' => $sumberKpi,

    //         'finish_goods' => $finishGoods,
    //         'kecap_matang' => $kecapMatang,

    //         'total_listrik_produksi' => $totalProduksi,
    //         'total_listrik_bas' => $totalBas,

    //         'kpi_listrik_produksi' => round($kpiProduksi, 4),
    //         'kpi_listrik_bas' => round($kpiBas, 4)
    //     ]);
    // }

    public function getKpiListrik(Request $request)
    {
        $today = Carbon::now();
        $currentMonth = $today->format('Y-m');

        // Filter dari request: 'monthly', 'weekly', atau null (default)
        $filterType = $request->input('filter_type'); // 'monthly' atau 'weekly'
        $filterValue = $request->input('filter_value'); // 'YYYY-MM' untuk monthly, atau week ID untuk weekly

        /**
         * 1. Tentukan KPI yang akan digunakan
         */
        $kpiData = null;
        $sumberKpi = '';
        $startDate = null;
        $endDate = null;

        // Jika ada filter monthly
        if ($filterType === 'monthly' && $filterValue) {
            $kpiData = KpiModel::where('periode_tipe', 'monthly')
                ->where('month', $filterValue)
                ->first();

            if ($kpiData) {
                $sumberKpi = 'monthly';
                // Untuk monthly, ambil seluruh bulan
                $startDate = Carbon::parse($filterValue . '-01')->startOfMonth();
                $endDate = Carbon::parse($filterValue . '-01')->endOfMonth();
            }
        }
        // Jika ada filter weekly
        elseif ($filterType === 'weekly' && $filterValue
        ) {
            $kpiData = KpiModel::where('periode_tipe', 'weekly')
            ->where('id', $filterValue)
            ->first();

            if ($kpiData) {
                $sumberKpi = 'weekly';
                $startDate = Carbon::parse($kpiData->start_date);
                $endDate = Carbon::parse($kpiData->end_date);
            }
        }
        // Default: cari data bulanan current month
        else {
            $kpiData = KpiModel::where('periode_tipe', 'monthly')
            ->where('month', $currentMonth)
            ->first();

            if ($kpiData) {
                $sumberKpi = 'monthly';
                $startDate = Carbon::parse($currentMonth . '-01')->startOfMonth();
                $endDate = Carbon::parse($currentMonth . '-01')->endOfMonth();
            } else {
                // Jika tidak ada monthly, ambil weekly terbaru
                $kpiData = KpiModel::where('periode_tipe',
                    'weekly'
                )
                ->whereDate('end_date', '<=', $today)
                    ->orderBy('end_date', 'desc')
                    ->first();

                if ($kpiData) {
                    $sumberKpi = 'weekly-terbaru';
                    $startDate = Carbon::parse($kpiData->start_date);
                    $endDate = Carbon::parse($kpiData->end_date);
                }
            }
        }

        // Jika tidak ada data KPI sama sekali
        if (!$kpiData) {
            return response()->json([
                'message' => 'Tidak ada data KPI monthly maupun weekly.'
            ], 404);
        }

        $finishGoods = $kpiData->finish_goods;
        $kecapMatang = $kpiData->kecap_matang;

        /**
         * Validasi agar tidak membagi dengan nol
         */
        if ($finishGoods <= 0) {
            return response()->json([
                'message' => 'Finish Goods tidak valid (<=0).'
            ], 422);
        }

        if ($kecapMatang <= 0) {
            return response()->json([
                'message' => 'Kecap Matang tidak valid (<=0).'
            ], 422);
        }

        /**
         * 2. Ambil data listrik berdasarkan range tanggal
         */
        $listrik = PemakaianListrikModel::whereBetween('waktu', [$startDate, $endDate])
        ->orderBy('panel_type')
        ->orderBy('waktu')
        ->get();

        if ($listrik->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data listrik pada periode ini.'
            ], 404);
        }

        /**
         * 3. Group data by panel_type
         */
        $groupedByPanel = $listrik->groupBy('panel_type');

        /**
         * 4. Hitung usage per panel dengan detail per hari
         */
        $panelUsages = [];
        $panelUsageDetails = []; // Detail usage per hari

        foreach ($groupedByPanel as $panel => $panelData) {
            $sortedData = $panelData->sortBy('waktu')->values();
            $totalUsage = 0;
            $dailyUsage = []; // Array untuk menyimpan usage per hari

            // Hitung delta antara hari ini dan esok hari
            for ($i = 0; $i < $sortedData->count() - 1; $i++) {
                $currentDate = $sortedData[$i]->waktu;
                $nextDate = $sortedData[$i + 1]->waktu;
                $currentMwh = $sortedData[$i]->mwh;
                $nextMwh = $sortedData[$i + 1]->mwh;

                $delta = $nextMwh - $currentMwh;

                // Simpan detail per hari
                $dailyUsage[] = [
                    'tanggal' => $currentDate,
                    'mwh_sekarang' => round($currentMwh, 2),
                    'mwh_esok' => round($nextMwh, 2),
                    'tanggal_esok' => $nextDate,
                    'usage' => round($delta, 2),
                    'status' => $delta >= 0 ? 'valid' : 'negative'
                ];

                if ($delta >= 0) {
                    $totalUsage += $delta;
                }
            }

            $panelUsages[$panel] = $totalUsage;
            $panelUsageDetails[$panel] = $dailyUsage;
        }

        /**
         * 5. Hitung Total Produksi (SDP1 + SDP2 + 71.3% SDP3 + SDP5 + SDP9 + SDP10 + SDP11)
         */
        $totalProduksi = 0;
        $panelProduksi = ['SDP1', 'SDP2', 'SDP3', 'SDP5', 'SDP9', 'SDP10', 'SDP11'];
        $detailProduksi = [];

        foreach ($panelProduksi as $panel) {
            if (isset($panelUsages[$panel])) {
                if ($panel === 'SDP3') {
                    // Hanya ambil 71.3% dari SDP3
                    $contribution = $panelUsages[$panel] * 0.713;
                    $totalProduksi += $contribution;
                    $detailProduksi[$panel] = [
                        'total_usage' => round($panelUsages[$panel], 2),
                        'percentage' => 71.3,
                        'contribution' => round($contribution, 2)
                    ];
                } else {
                    $totalProduksi += $panelUsages[$panel];
                    $detailProduksi[$panel] = [
                        'total_usage' => round($panelUsages[$panel],
                            2
                        ),
                        'percentage' => 100,
                        'contribution' => round($panelUsages[$panel], 2)
                    ];
                }
            }
        }

        /**
         * 6. Hitung Total BAS (Semua SDP1 sampai SDP14)
         */
        $totalBas = 0;
        $detailBas = [];

        for ($i = 1; $i <= 14; $i++) {
            $panel = 'SDP' . $i;
            if (isset($panelUsages[$panel])) {
                $totalBas += $panelUsages[$panel];
                $detailBas[$panel] = round($panelUsages[$panel], 2);
            }
        }

        /**
         * 7. KPI Perhitungan
         */
        $kpiProduksi = $totalProduksi / $finishGoods;
        $kpiBas = $totalBas / $kecapMatang;

        return response()->json([
            'periode' => [
                'type' => $sumberKpi,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'display' => $sumberKpi === 'monthly'
                ? Carbon::parse($startDate)->format('F Y')
                : $startDate->format('d M') . ' - ' .
                    $endDate->format('d M Y')
            ],

            'kpi_data' => [
                'finish_goods' => $finishGoods,
                'kecap_matang' => $kecapMatang,
            ],

            'listrik' => [
                'total_produksi' => round($totalProduksi, 2),
                'total_bas' => round($totalBas, 2),
                'detail_per_panel' => array_map(function ($usage) {
                    return round($usage, 2);
                }, $panelUsages),

                // Detail usage per hari untuk setiap panel
                'usage_harian_per_panel' => $panelUsageDetails,

                // Detail kontribusi untuk produksi
                'detail_produksi' => $detailProduksi,

                // Detail untuk BAS
                'detail_bas' => $detailBas
            ],

            'kpi' => [
                'listrik_produksi' => round($kpiProduksi,
                    4
                ),
                'listrik_bas' => round($kpiBas, 4)
            ]
        ]);
    }

    public function getAvailableWeeks()
    {
        $weeks = KpiModel::where('periode_tipe', 'weekly')
        ->orderBy('start_date', 'desc')
        ->get(['id', 'start_date', 'end_date'])
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'start_date' => $item->start_date,
                'end_date' => $item->end_date
            ];
        });

        return response()->json($weeks);
    }
}
