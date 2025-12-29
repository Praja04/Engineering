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

    /**
     * 1. Ambil KPI:
     *    - Prioritas monthly
     *    - Jika tidak ada, ambil weekly terbaru
     */
    $kpiMonthly = KpiModel::where('periode_tipe', 'monthly')
        ->where('month', $currentMonth)
        ->first();

    if ($kpiMonthly) {
        $finishGoods   = $kpiMonthly->finish_goods;
        $kecapMatang   = $kpiMonthly->kecap_matang;
        $sumberKpi = 'monthly';
    } else {
        $kpiWeekly = KpiModel::where('periode_tipe', 'weekly')
            ->whereDate('start_date', '<=', $today)
            ->orderBy('end_date', 'desc')
            ->first();

        if (!$kpiWeekly) {
            return response()->json([
                'message' => 'Tidak ada data KPI monthly maupun weekly.'
            ], 404);
        }

        $finishGoods   = $kpiWeekly->finish_goods;
        $kecapMatang   = $kpiWeekly->kecap_matang;
        $sumberKpi = 'weekly-terbaru';
    }

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
     * 2. Ambil data listrik bulan berjalan
     */
    $listrik = PemakaianListrikModel::whereMonth('waktu', $today->month)
        ->whereYear('waktu', $today->year)
        ->orderBy('waktu')
        ->get();

    if ($listrik->isEmpty()) {
        return response()->json([
            'message' => 'Tidak ada data listrik bulan ini.'
        ], 404);
    }

    /**
     * 3. Panel Produksi & Semua Panel
     */
    $panelProduksi = ['SDP1', 'SDP2', 'SDP3', 'SDP5', 'SDP9', 'SDP10', 'SDP11'];
    
    // Group data by panel_type
    $groupedByPanel = $listrik->groupBy('panel_type');

    /**
     * 4. Hitung Total Produksi (SDP Produksi) dengan Delta
     */
    $totalProduksi = 0;
    
    foreach ($panelProduksi as $panel) {
        if (!$groupedByPanel->has($panel)) continue;
        
        $panelData = $groupedByPanel[$panel]->sortBy('waktu')->pluck('mwh')->values();
        
        // Hitung delta untuk panel ini
        for ($i = 0; $i < $panelData->count() - 1; $i++) {
            $delta = $panelData[$i + 1] - $panelData[$i];
            if ($delta >= 0) {
                $totalProduksi += $delta;
            }
        }
    }

    /**
     * 5. Hitung Total BAS (Semua Panel) dengan Delta
     */
    $totalBas = 0;
    
    foreach ($groupedByPanel as $panel => $panelData) {
        $panelValues = $panelData->sortBy('waktu')->pluck('mwh')->values();
        
        // Hitung delta untuk panel ini
        for ($i = 0; $i < $panelValues->count() - 1; $i++) {
            $delta = $panelValues[$i + 1] - $panelValues[$i];
            if ($delta >= 0) {
                $totalBas += $delta;
            }
        }
    }

    /**
     * 6. KPI Perhitungan
     */
    $kpiProduksi = $totalProduksi / $finishGoods;
    $kpiBas = $totalBas / $kecapMatang;

    return response()->json([
        'periode' => $currentMonth,
        'kpi_sumber' => $sumberKpi,

        'finish_goods' => $finishGoods,
        'kecap_matang' => $kecapMatang,

        'total_listrik_produksi' => round($totalProduksi, 2),
        'total_listrik_bas' => round($totalBas, 2),

        'kpi_listrik_produksi' => round($kpiProduksi, 4),
        'kpi_listrik_bas' => round($kpiBas, 4)
    ]);
}
}
