<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Maintenance\MtcMainModel;
use App\Models\Maintenance\MtcKebutuhanMaterialModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MtcMaterialDashboardController extends Controller
{
    /**
     * Render the dashboard page.
     */
    public function index()
    {
        // Get unique maintenance types and packages for dropdown filters
        $jenisMtcList = MtcMainModel::select('jenis_mtc')
            ->whereNotNull('jenis_mtc')
            ->distinct()
            ->orderBy('jenis_mtc')
            ->pluck('jenis_mtc');

        $paketList = MtcMainModel::select('paket')
            ->whereNotNull('paket')
            ->distinct()
            ->orderBy('paket')
            ->pluck('paket');

        return view('dashboard.maintenance.material', compact('jenisMtcList', 'paketList'));
    }

    /**
     * Get chart and card data for the dashboard.
     */
    public function getDashboardCharts(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        // 1. Base Query for Material Requirements
        $query = MtcKebutuhanMaterialModel::query()
            ->join('mtc_main', 'mtc_kebutuhan_material.mtc_main_id', '=', 'mtc_main.id')
            ->whereBetween('mtc_main.tanggal', [$startDate, $endDate]);

        if ($request->filled('jenis_mtc')) {
            $query->where('mtc_main.jenis_mtc', $request->jenis_mtc);
        }

        if ($request->filled('paket')) {
            $query->where('mtc_main.paket', $request->paket);
        }

        // Clone base query for different aggregations
        $cardsQuery = clone $query;
        $topMaterialsQuery = clone $query;
        $trendQuery = clone $query;
        $typeDistQuery = clone $query;

        // 2. Summary Cards Data
        $totalQty = floatval($cardsQuery->sum('mtc_kebutuhan_material.qty'));
        $uniqueCount = intval($cardsQuery->distinct('mtc_kebutuhan_material.deskripsi')->count('mtc_kebutuhan_material.deskripsi'));
        $totalJobs = intval($cardsQuery->distinct('mtc_kebutuhan_material.mtc_main_id')->count('mtc_kebutuhan_material.mtc_main_id'));

        // 3. Top 10 Materials (by total quantity)
        $topMaterials = $topMaterialsQuery
            ->select('mtc_kebutuhan_material.deskripsi', 'mtc_kebutuhan_material.mid', DB::raw('SUM(mtc_kebutuhan_material.qty) as total_qty'))
            ->groupBy('mtc_kebutuhan_material.deskripsi', 'mtc_kebutuhan_material.mid')
            ->orderBy('total_qty', 'DESC')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->mid ? "{$item->mid} - {$item->deskripsi}" : $item->deskripsi,
                    'qty' => floatval($item->total_qty)
                ];
            });

        // 4. Monthly Trend of consumption (quantity sum grouped by month)
        $trendData = $trendQuery
            ->select(
                DB::raw("DATE_FORMAT(mtc_main.tanggal, '%Y-%m') as year_month"),
                DB::raw("DATE_FORMAT(mtc_main.tanggal, '%b %Y') as formatted_month"),
                DB::raw('SUM(mtc_kebutuhan_material.qty) as total_qty')
            )
            ->groupBy('year_month', 'formatted_month')
            ->orderBy('year_month', 'ASC')
            ->get();

        // 5. Maintenance Type distribution
        $typeDistribution = $typeDistQuery
            ->select('mtc_main.jenis_mtc', DB::raw('SUM(mtc_kebutuhan_material.qty) as total_qty'))
            ->groupBy('mtc_main.jenis_mtc')
            ->orderBy('total_qty', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'jenis_mtc' => $item->jenis_mtc ?? 'Unspecified',
                    'qty' => floatval($item->total_qty)
                ];
            });

        return response()->json([
            'status' => 200,
            'summary' => [
                'total_qty' => $totalQty,
                'unique_items' => $uniqueCount,
                'total_jobs' => $totalJobs,
            ],
            'charts' => [
                'top_materials' => $topMaterials,
                'trend' => $trendData,
                'distribution' => $typeDistribution
            ]
        ]);
    }

    /**
     * Get paginated materials list for Datatables server-side processing.
     */
    public function getMaterialList(Request $request)
    {
        $query = MtcKebutuhanMaterialModel::query()
            ->select('mtc_kebutuhan_material.*')
            ->join('mtc_main', 'mtc_kebutuhan_material.mtc_main_id', '=', 'mtc_main.id')
            ->with(['main.createdBy']);

        // Filter: Date range
        if ($request->filled('start_date')) {
            $query->whereDate('mtc_main.tanggal', '>=', Carbon::parse($request->start_date));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('mtc_main.tanggal', '<=', Carbon::parse($request->end_date));
        }

        // Filter: Maintenance Type
        if ($request->filled('jenis_mtc')) {
            $query->where('mtc_main.jenis_mtc', $request->jenis_mtc);
        }

        // Filter: Package (Preventif / Korektif)
        if ($request->filled('paket')) {
            $query->where('mtc_main.paket', $request->paket);
        }

        // Search: General keyword
        if ($request->filled('search_val')) {
            $search = $request->search_val;
            $query->where(function ($q) use ($search) {
                $q->where('mtc_kebutuhan_material.deskripsi', 'like', "%{$search}%")
                    ->orWhere('mtc_kebutuhan_material.mid', 'like', "%{$search}%")
                    ->orWhere('mtc_main.jenis_mtc', 'like', "%{$search}%")
                    ->orWhere('mtc_main.paket', 'like', "%{$search}%");
            });
        }

        // Get total count for pagination
        $total = $query->count();

        // Sort
        $query->orderBy('mtc_main.tanggal', 'desc')
            ->orderBy('mtc_kebutuhan_material.id', 'desc');

        // Paginate
        $data = $query
            ->skip($request->start ?? 0)
            ->take($request->length ?? 10)
            ->get();

        // Lazy eager load the specific inspection models and their mesin relations
        $data->load([
            'main.motorPump.mesin',
            'main.utility.mesin',
            'main.electrical.mesin',
            'main.refrigerasi.mesin',
            'main.electricEngine.mesin',
            'main.dieselEngine.mesin',
            'main.electricP2h.mesin',
            'main.dieselP2h.mesin',
            'main.battery',
        ]);

        foreach ($data as $item) {
            $main = $item->main;
            $machineName = '-';
            if ($main) {
                $rel = match ($main->jenis_mtc) {
                    'Motor Pompa' => 'motorPump',
                    'Utility' => 'utility',
                    'Electrical' => 'electrical',
                    'Refrigerasi' => 'refrigerasi',
                    'Electric Engine' => 'electricEngine',
                    'Diesel Engine' => 'dieselEngine',
                    'Electric P2h' => 'electricP2h',
                    'Diesel P2h' => 'dieselP2h',
                    default => null
                };
                if ($rel && $main->$rel && $main->$rel->mesin) {
                    $machineName = $main->$rel->mesin->nama_mesin;
                } elseif ($main->jenis_mtc === 'Battery' && $main->battery) {
                    $machineName = 'Unit: ' . ($main->battery->no_unit ?? '-') . ' (Seri: ' . ($main->battery->no_seri ?? '-') . ')';
                } elseif ($main->jenis_mtc === 'Sipil') {
                    $machineName = 'Sipil Area: ' . ($main->area ?? '-');
                }
            }
            $item->nama_mesin = $machineName;
        }

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $data
        ]);
    }
}
