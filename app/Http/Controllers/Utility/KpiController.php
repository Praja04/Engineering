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
        $request->validate([
            'periode_tipe' => 'required|in:weekly,monthly',
            'finish_goods' => 'required|numeric|min:0',
            'kecap_matang' => 'required|numeric|min:0',
            'start_date'   => 'nullable|required_if:periode_tipe,weekly|date',
            'end_date'     => 'nullable|required_if:periode_tipe,weekly|date|after_or_equal:start_date',
            'month'        => 'nullable|required_if:periode_tipe,monthly|date_format:Y-m',
            'invoice_listrik' => 'nullable|numeric|min:0',
            'steam' => 'nullable|numeric|min:0',
            'batubara' => 'nullable|numeric|min:0',

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
            'invoice_listrik' => $request->invoice_listrik,
            'steam' => $request->steam,
            'batubara' => $request->batubara,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan!'
        ]);
    }

    public function getData(Request $request)
    {
        $query = KpiModel::query();

        // Filter tipe periode (wajib)
        if ($request->periode_tipe) {
            $query->where('periode_tipe', $request->periode_tipe);
        } else {
            // Optional: kalau tidak ada periode_tipe, bisa return error atau default
            return response()->json([
                'status'  => 'error',
                'message' => 'Parameter periode_tipe wajib diisi (weekly atau monthly)',
                'data'    => []
            ], 400);
        }

        // Filter weekly: rentang tanggal
        if ($request->start_date) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('end_date', '<=', $request->end_date);
        }

        // Filter monthly: bulan spesifik
        if ($request->month) {
            $query->where('month', $request->month);
        }

        // Sorting terbaru dulu (DESC berdasarkan tanggal/bulan)
        $query->orderByRaw("
            CASE 
                WHEN periode_tipe = 'weekly' THEN start_date 
                WHEN periode_tipe = 'monthly' THEN month 
            END DESC
        ");

        // Pagination
        $perPage = $request->input('per_page', 10);
        $perPage = min(max($perPage, 5), 50);

        $data = $query->paginate($perPage);

        $data->appends($request->query());

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
            'month'        => 'nullable|required_if:periode_tipe,monthly|date_format:Y-m',
            'invoice_listrik' => 'nullable|numeric|min:0',
            'steam' => 'nullable|numeric|min:0',
            'batubara' => 'nullable|numeric|min:0',
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
            'invoice_listrik' => $request->invoice_listrik,
            'steam' => $request->steam,
            'batubara' => $request->batubara,
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


    public function getKpiListrik(Request $request)
    {
        $today = Carbon::now();
        $currentMonth = $today->format('Y-m');

        $filterType = $request->input('filter_type');
        $filterValue = $request->input('filter_value');

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
            } else {
                $sumberKpi = 'monthly-no-kpi';
            }

            $startDate = Carbon::parse($filterValue . '-01')->startOfMonth();
            $endDate = Carbon::parse($filterValue . '-01')->endOfMonth();
        }
        // Jika ada filter weekly
        elseif ($filterType === 'weekly' && $filterValue) {
            $kpiData = KpiModel::where('periode_tipe', 'weekly')
            ->where('id', $filterValue)
                ->first();

            if ($kpiData) {
                $sumberKpi = 'weekly';
                $startDate = Carbon::parse($kpiData->start_date);
                $endDate = Carbon::parse($kpiData->end_date);
            } else {
                return response()->json([
                    'message' => 'Data weekly dengan ID tersebut tidak ditemukan.'
                ], 404);
            }
        }
        // Default: gunakan bulan sekarang (dengan atau tanpa KPI)
        else {
            // Set range bulan sekarang
            $startDate = Carbon::parse($currentMonth . '-01')->startOfMonth();
            $endDate = Carbon::parse($currentMonth . '-01')->endOfMonth();

            // Cek apakah ada KPI untuk bulan sekarang
            $kpiData = KpiModel::where('periode_tipe', 'monthly')
            ->where('month', $currentMonth)
                ->first();

            if ($kpiData) {
                $sumberKpi = 'monthly-current';
            } else {
                // Tidak ada KPI bulanan, tetap tampilkan data pemakaian saja
                $sumberKpi = 'monthly-current-no-kpi';
            }
        }

        // Ambil finish goods dan kecap matang jika ada
        $finishGoods = $kpiData ? $kpiData->finish_goods : null;
        $kecapMatang = $kpiData ? $kpiData->kecap_matang : null;
        $invoiceListrik = $kpiData ? $kpiData->invoice_listrik : null;
        $hasKpiData = ($finishGoods && $kecapMatang && $finishGoods > 0 && $kecapMatang > 0);

        /**
         * Ambil data listrik berdasarkan range tanggal
         */
        $listrik = PemakaianListrikModel::whereBetween('waktu', [$startDate, $endDate])
            ->whereNotNull('usage')
            ->orderBy('panel_type')
            ->orderBy('waktu')
            ->get();

        if ($listrik->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data pemakaian listrik pada periode ini.',
                'periode' => [
                    'type' => $sumberKpi,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'display' => str_contains($sumberKpi, 'monthly')
                    ? Carbon::parse($startDate)->format('F Y')
                    : $startDate->format('d M') . ' - ' . $endDate->format('d M Y'),
                    'has_kpi_data' => false
                ]
            ], 200);
        }

        /**
         * Group data by panel_type
         */
        $groupedByPanel = $listrik->groupBy('panel_type');

        /**
         * Hitung total usage per panel
         */
        $panelUsages = [];
        $panelUsageDetails = [];

        foreach ($groupedByPanel as $panel => $panelData) {
            $sortedData = $panelData->sortBy('waktu')->values();
            $totalUsage = 0;
            $dailyUsage = [];

            foreach ($sortedData as $data) {
                $usage = $data->usage ?? 0;
                $currentDate = Carbon::parse($data->waktu)->format('Y-m-d');
                $currentMwh = $data->mwh ?? 0;

                $dailyUsage[] = [
                    'tanggal' => $currentDate,
                    'mwh' => round($currentMwh, 2),
                    'usage' => round($usage, 2),
                    'status' => $usage >= 0 ? 'valid' : 'negative'
                ];

                if ($usage >= 0) {
                    $totalUsage += $usage;
                }
            }

            $panelUsages[$panel] = $totalUsage;
            $panelUsageDetails[$panel] = $dailyUsage;
        }

        /**
         * Hitung Total Produksi
         */
        $totalProduksi = 0;
        $panelProduksi = ['SDP1', 'SDP2', 'SDP3', 'SDP5', 'SDP9', 'SDP10', 'SDP11'];
        $detailProduksi = [];

        foreach ($panelProduksi as $panel) {
            if (isset($panelUsages[$panel])) {
                if ($panel === 'SDP3') {
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
                        'total_usage' => round($panelUsages[$panel], 2),
                        'percentage' => 100,
                        'contribution' => round($panelUsages[$panel], 2)
                    ];
                }
            }
        }

        /**
         * Hitung Total BAS
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
         * Response data
         */
        $response = [
            'periode' => [
                'type' => $sumberKpi,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'display' => str_contains($sumberKpi, 'monthly')
                ? Carbon::parse($startDate)->format('F Y')
                : $startDate->format('d M') . ' - ' . $endDate->format('d M Y'),
                'has_kpi_data' => $hasKpiData
            ],

            'listrik' => [
                'total_produksi' => round($totalProduksi, 2),
                'total_bas' => round($totalBas, 2),
                'detail_per_panel' => array_map(function ($usage) {
                    return round($usage, 2);
                }, $panelUsages),
                'usage_harian_per_panel' => $panelUsageDetails,
                'detail_produksi' => $detailProduksi,
                'detail_bas' => $detailBas
            ]
        ];

        if ($hasKpiData) {
            $kpiProduksi = ($totalProduksi * 1000) / $finishGoods;
            $kpiBas = ($totalBas * 1000) / $kecapMatang;

            $response['kpi_data'] = [
                'finish_goods' => $finishGoods,
                'kecap_matang' => $kecapMatang,
            ];

            // Tambahkan invoice jika ada dan periode adalah monthly
            if ($invoiceListrik && str_contains($sumberKpi, 'monthly')) {
                $response['kpi_data']['invoice_listrik'] = $invoiceListrik;
            }

            $response['kpi'] = [
                'listrik_produksi' => round($kpiProduksi, 4),
                'listrik_bas' => round($kpiBas, 4)
            ];
        } else {
            $response['kpi_data'] = [
                'finish_goods' => null,
                'kecap_matang' => null,
                'message' => 'Data KPI (Finish Goods & Kecap Matang) tidak tersedia untuk periode ini. Hanya menampilkan data usage listrik.'
            ];

            $response['kpi'] = [
                'listrik_produksi' => null,
                'listrik_bas' => null
            ];
        }

        return response()->json($response);
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

    public function getMonthlyInvoiceListrik(Request $request)
    {
        // Default: bulan sekarang (format YYYY-MM)
        $month = $request->month ?? Carbon::now()->format('Y-m');

        $data = KpiModel::where('periode_tipe', 'monthly')
        ->where('month', $month)
            ->orderBy('month', 'desc')
            ->get([
                'id',
                'month',
                'invoice_listrik',
            ]);

        return response()->json([
            'status' => 'success',
            'month'  => $month,
            'data'   => $data
        ]);
    }
}
