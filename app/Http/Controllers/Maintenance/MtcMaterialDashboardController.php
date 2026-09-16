<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Maintenance\MtcMainModel;
use App\Models\Maintenance\MtcKebutuhanMaterialModel;
use App\Models\Maintenance\MtcPenggantianMaterialModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MtcMaterialDashboardController extends Controller
{
    /**
     * Render the dashboard page.
     */
    public function index()
    {
        // Get unique maintenance types that have material requirements or replacements
        $jenisMtcList = MtcMainModel::where(function ($query) {
            $query->whereHas('kebutuhanMaterial', function ($q) {
                $q->where('qty', '>', 0);
            })->orWhereHas('penggantianMaterial', function ($q) {
                $q->where('qty', '>', 0);
            });
        })
            ->select('jenis_mtc')
            ->whereNotNull('jenis_mtc')
            ->distinct()
            ->orderBy('jenis_mtc')
            ->pluck('jenis_mtc');

        // Simplified 2 options for Paket Filter: Maintenance & Korektif
        $paketList = ['Maintenance', 'Korektif'];

        return view('dashboard.maintenance.material', compact('jenisMtcList', 'paketList'));
    }

    /**
     * Helper to apply common filters (date range, jenis_mtc, paket) on a query joined with mtc_main.
     */
    private function applyFilters($query, Request $request, Carbon $startDate, Carbon $endDate)
    {
        $query->whereBetween('mtc_main.tanggal', [$startDate, $endDate]);

        if ($request->filled('jenis_mtc')) {
            $query->where('mtc_main.jenis_mtc', $request->jenis_mtc);
        }

        if ($request->filled('paket')) {
            $paket = trim($request->paket);
            if (strcasecmp($paket, 'Korektif') === 0) {
                $query->where(function ($q) {
                    $q->whereRaw("LOWER(COALESCE(mtc_main.paket, '')) LIKE '%Korektif%'")
                        ->orWhere(function ($sub) {
                            $sub->whereNotNull('mtc_main.korektif')
                                ->where('mtc_main.korektif', '!=', '');
                        });
                });
            } elseif (strcasecmp($paket, 'Maintenance') === 0) {
                $query->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereRaw("LOWER(COALESCE(mtc_main.paket, '')) NOT LIKE '%Korektif%'")
                            ->orWhereNull('mtc_main.paket');
                    })->where(function ($sub) {
                        $sub->whereNull('mtc_main.korektif')
                            ->orWhere('mtc_main.korektif', '=', '');
                    });
                });
            }
        }

        return $query;
    }

    /**
     * Get chart and summary card data for the dashboard.
     */
    public function getDashboardCharts(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        // 1. Base Query for Kebutuhan Material
        $kebQuery = MtcKebutuhanMaterialModel::query()
            ->join('mtc_main', 'mtc_kebutuhan_material.mtc_main_id', '=', 'mtc_main.id')
            ->where('mtc_kebutuhan_material.qty', '>', 0);
        $kebQuery = $this->applyFilters($kebQuery, $request, $startDate, $endDate);

        // 2. Base Query for Penggantian Material
        $pengQuery = MtcPenggantianMaterialModel::query()
            ->join('mtc_main', 'mtc_penggantian_material.mtc_main_id', '=', 'mtc_main.id')
            ->where('mtc_penggantian_material.qty', '>', 0);
        $pengQuery = $this->applyFilters($pengQuery, $request, $startDate, $endDate);

        // 3. Summary Cards Calculations
        $totalQtyKebutuhan   = floatval((clone $kebQuery)->sum('mtc_kebutuhan_material.qty'));
        $totalQtyPenggantian = floatval((clone $pengQuery)->sum('mtc_penggantian_material.qty'));

        $uniqueKebDesc  = (clone $kebQuery)->whereNotNull('mtc_kebutuhan_material.deskripsi')->pluck('mtc_kebutuhan_material.deskripsi')->toArray();
        $uniquePengDesc = (clone $pengQuery)->whereNotNull('mtc_penggantian_material.deskripsi')->pluck('mtc_penggantian_material.deskripsi')->toArray();
        $uniqueItemsCount = count(array_unique(array_filter(array_merge($uniqueKebDesc, $uniquePengDesc))));

        $jobsKeb  = (clone $kebQuery)->pluck('mtc_kebutuhan_material.mtc_main_id')->toArray();
        $jobsPeng = (clone $pengQuery)->pluck('mtc_penggantian_material.mtc_main_id')->toArray();
        $totalJobsCount = count(array_unique(array_merge($jobsKeb, $jobsPeng)));

        // 4. Top 10 Kebutuhan Material (by total quantity)
        $topKebutuhan = (clone $kebQuery)
            ->whereNotNull('mtc_kebutuhan_material.deskripsi')
            ->where('mtc_kebutuhan_material.deskripsi', '!=', '')
            ->select('mtc_kebutuhan_material.deskripsi', 'mtc_kebutuhan_material.mid', DB::raw('SUM(mtc_kebutuhan_material.qty) as total_qty'))
            ->groupBy('mtc_kebutuhan_material.deskripsi', 'mtc_kebutuhan_material.mid')
            ->orderBy('total_qty', 'DESC')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->mid ? "{$item->mid} - {$item->deskripsi}" : ($item->deskripsi ?? 'Tanpa Deskripsi'),
                    'qty'   => floatval($item->total_qty)
                ];
            });

        // 5. Top 10 Penggantian Material (by total quantity)
        $topPenggantian = (clone $pengQuery)
            ->whereNotNull('mtc_penggantian_material.deskripsi')
            ->where('mtc_penggantian_material.deskripsi', '!=', '')
            ->select('mtc_penggantian_material.deskripsi', 'mtc_penggantian_material.mid', DB::raw('SUM(mtc_penggantian_material.qty) as total_qty'))
            ->groupBy('mtc_penggantian_material.deskripsi', 'mtc_penggantian_material.mid')
            ->orderBy('total_qty', 'DESC')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->mid ? "{$item->mid} - {$item->deskripsi}" : ($item->deskripsi ?? 'Tanpa Deskripsi'),
                    'qty'   => floatval($item->total_qty)
                ];
            });

        return response()->json([
            'status' => 200,
            'summary' => [
                'total_qty_kebutuhan'   => $totalQtyKebutuhan,
                'total_qty_penggantian' => $totalQtyPenggantian,
                'unique_items'          => $uniqueItemsCount,
                'total_jobs'            => $totalJobsCount,
            ],
            'charts' => [
                'top_kebutuhan'   => $topKebutuhan,
                'top_penggantian' => $topPenggantian,
            ]
        ]);
    }

    /**
     * Get unique Machine Ledger summary data grouped by unique machine/unit/area.
     */
    public function getMachineLedger(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        $mainQuery = MtcMainModel::query()
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->where(function ($q) {
                $q->whereHas('kebutuhanMaterial', function ($k) {
                    $k->where('qty', '>', 0);
                })->orWhereHas('penggantianMaterial', function ($p) {
                    $p->where('qty', '>', 0);
                });
            })
            ->with([
                'createdBy',
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
                'kebutuhanMaterial',
                'penggantianMaterial',
            ]);

        if ($request->filled('jenis_mtc')) {
            $mainQuery->where('jenis_mtc', $request->jenis_mtc);
        }

        if ($request->filled('paket')) {
            $paket = trim($request->paket);
            if (strcasecmp($paket, 'Korektif') === 0) {
                $mainQuery->where(function ($q) {
                    $q->whereRaw("LOWER(COALESCE(paket, '')) LIKE '%korektif%'")
                        ->orWhere(function ($sub) {
                            $sub->whereNotNull('korektif')
                                ->where('korektif', '!=', '');
                        });
                });
            } elseif (strcasecmp($paket, 'Maintenance') === 0) {
                $mainQuery->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereRaw("LOWER(COALESCE(paket, '')) NOT LIKE '%korektif%'")
                            ->orWhereNull('paket');
                    })->where(function ($sub) {
                        $sub->whereNull('korektif')
                            ->orWhere('korektif', '=', '');
                    });
                });
            }
        }

        $allMains = $mainQuery->get();

        $grouped = [];

        foreach ($allMains as $main) {
            $machineKey = 'unknown';
            $machineName = '-';
            $machineCode = '-';
            $location = $main->lokasi ?? $main->area ?? '-';

            $rel = match ($main->jenis_mtc) {
                'Motor Pompa'     => 'motorPump',
                'Utility'         => 'utility',
                'Electrical'      => 'electrical',
                'Refrigerasi'     => 'refrigerasi',
                'Electric Engine' => 'electricEngine',
                'Diesel Engine'   => 'dieselEngine',
                'Electric P2h', 'Electric P2H' => 'electricP2h',
                'Diesel P2h', 'Diesel P2H'     => 'dieselP2h',
                'Genset P2h', 'Genset P2H'     => 'gensetP2h',
                default => null
            };

            if ($rel && $main->$rel && $main->$rel->mesin) {
                $mObj = $main->$rel->mesin;
                $machineKey = "mesin_{$mObj->id}";
                $machineName = $mObj->nama_mesin;
                $machineCode = $mObj->kode_mesin ?? '-';
                if ($mObj->lokasi) $location = $mObj->lokasi;
            } elseif ($main->jenis_mtc === 'Battery' && $main->battery) {
                $machineKey = "battery_" . ($main->battery->id ?? $main->id);
                $machineName = 'Unit: ' . ($main->battery->no_unit ?? '-') . ' (Seri: ' . ($main->battery->no_seri ?? '-') . ')';
                $machineCode = 'BATTERY';
            } elseif ($main->jenis_mtc === 'Sipil') {
                $areaVal = $main->area ?? 'Umum';
                $machineKey = "sipil_" . strtolower(trim($areaVal));
                $machineName = 'Sipil: ' . $areaVal;
                $machineCode = 'SIPIL';
            } else {
                $machineKey = "main_" . $main->id;
                $machineName = $main->area ? "Area: {$main->area}" : ($main->jenis_mtc . ' #' . $main->id);
                $machineCode = '-';
            }

            $kebQty  = floatval($main->kebutuhanMaterial->where('qty', '>', 0)->sum('qty'));
            $pengQty = floatval($main->penggantianMaterial->where('qty', '>', 0)->sum('qty'));

            if (!isset($grouped[$machineKey])) {
                $grouped[$machineKey] = [
                    'key'                  => $machineKey,
                    'nama_mesin'           => $machineName,
                    'kode_mesin'           => $machineCode,
                    'jenis_mtc'            => $main->jenis_mtc ?? '-',
                    'lokasi'               => $location,
                    'total_kebutuhan_qty'  => 0,
                    'total_penggantian_qty' => 0,
                    'main_ids'             => [],
                    'total_pekerjaan'      => 0,
                ];
            }

            $grouped[$machineKey]['total_kebutuhan_qty']   += $kebQty;
            $grouped[$machineKey]['total_penggantian_qty'] += $pengQty;
            if (!in_array($main->id, $grouped[$machineKey]['main_ids'])) {
                $grouped[$machineKey]['main_ids'][] = $main->id;
            }
        }

        // Compute total_pekerjaan
        $result = array_values(array_map(function ($item) {
            $item['total_pekerjaan'] = count($item['main_ids']);
            return $item;
        }, $grouped));

        // Sort descending by total material activity
        usort($result, function ($a, $b) {
            $sumA = $a['total_kebutuhan_qty'] + $a['total_penggantian_qty'];
            $sumB = $b['total_kebutuhan_qty'] + $b['total_penggantian_qty'];
            return $sumB <=> $sumA ?: strcmp($a['nama_mesin'], $b['nama_mesin']);
        });

        return response()->json([
            'status' => 200,
            'data'   => $result
        ]);
    }

    /**
     * Get detailed transaction rows for a specific machine / list of mtc_main IDs.
     */
    public function getMachineDetails(Request $request)
    {
        $mainIds = $request->get('main_ids', []);
        if (is_string($mainIds)) {
            $mainIds = explode(',', $mainIds);
        }
        $mainIds = array_filter(array_map('intval', (array)$mainIds));

        if (empty($mainIds)) {
            return response()->json([
                'status' => 200,
                'data'   => []
            ]);
        }

        $mains = MtcMainModel::whereIn('id', $mainIds)
            ->with(['createdBy', 'kebutuhanMaterial', 'penggantianMaterial'])
            ->orderBy('tanggal', 'desc')
            ->get();

        $details = [];

        foreach ($mains as $main) {
            $tglStr  = $main->tanggal ? $main->tanggal->format('d M Y') : '-';
            $tglRaw  = $main->tanggal ? $main->tanggal->format('Y-m-d') : '';
            $teknisi = $main->createdBy?->name ?? 'Teknisi';
            $paket   = $main->paket ? $main->paket : ($main->korektif ? 'Korektif' : 'Maintenance');

            // 1. Kebutuhan Material
            foreach ($main->kebutuhanMaterial as $item) {
                if ($item->qty <= 0) continue;
                $details[] = [
                    'id'          => 'keb_' . $item->id,
                    'tanggal'     => $tglStr,
                    'tanggal_raw' => $tglRaw,
                    'jenis_mtc'   => $main->jenis_mtc ?? '-',
                    'paket'       => $paket,
                    'kategori'    => 'Kebutuhan',
                    'mid'         => $item->mid ?? '-',
                    'deskripsi'   => $item->deskripsi ?? '-',
                    'qty'         => floatval($item->qty),
                    'satuan'      => $item->uom ?? '-',
                    'harga'       => '-',
                    'teknisi'     => $teknisi,
                ];
            }

            // 2. Penggantian Material
            foreach ($main->penggantianMaterial as $item) {
                if ($item->qty <= 0) continue;
                $details[] = [
                    'id'          => 'peng_' . $item->id,
                    'tanggal'     => $tglStr,
                    'tanggal_raw' => $tglRaw,
                    'jenis_mtc'   => $main->jenis_mtc ?? '-',
                    'paket'       => $paket,
                    'kategori'    => 'Penggantian',
                    'mid'         => $item->mid ?? '-',
                    'deskripsi'   => $item->deskripsi ?? '-',
                    'qty'         => floatval($item->qty),
                    'satuan'      => $item->uom ?? '-',
                    'harga'       => '-',
                    'teknisi'     => $teknisi,
                ];
            }
        }

        // Sort by tanggal_raw desc
        usort($details, fn($a, $b) => strcmp($b['tanggal_raw'], $a['tanggal_raw']));

        return response()->json([
            'status' => 200,
            'data'   => $details
        ]);
    }
}
