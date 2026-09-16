<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Maintenance\MtcMainModel;
use App\Models\Maintenance\MtcKebutuhanMaterialModel;
use App\Models\Maintenance\MtcPenggantianMaterialModel;
use App\Models\Maintenance\MtcMasterMaterialModel;
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
                $query->whereRaw("LOWER(COALESCE(mtc_main.paket, '')) LIKE '%korektif%'");
            } elseif (strcasecmp($paket, 'Maintenance') === 0) {
                $query->where(function ($q) {
                    $q->whereRaw("LOWER(COALESCE(mtc_main.paket, '')) NOT LIKE '%korektif%'")
                        ->orWhereNull('mtc_main.paket');
                });
            } else {
                $query->where('mtc_main.paket', $paket);
            }
        }

        return $query;
    }

    /**
     * Helper to extract machine and forklift details from a MtcMainModel.
     */
    private function resolveMachineInfo($main)
    {
        $rel = match ($main->jenis_mtc) {
            'Motor Pompa'                  => 'motorPump',
            'Utility'                      => 'utility',
            'Electrical'                   => 'electrical',
            'Refrigerasi'                  => 'refrigerasi',
            'Electric Engine'              => 'electricEngine',
            'Diesel Engine'                => 'dieselEngine',
            'Electric P2h', 'Electric P2H' => 'electricP2h',
            'Diesel P2h', 'Diesel P2H'     => 'dieselP2h',
            'Genset P2h', 'Genset P2H'     => 'gensetP2h',
            default                        => null
        };

        $machineKey  = 'unknown';
        $machineName = '-';
        $machineCode = '-';
        $location    = $main->lokasi ?? $main->area ?? '-';
        $isForklift  = false;

        if ($rel && $main->$rel && $main->$rel->mesin) {
            $mObj        = $main->$rel->mesin;
            $machineKey  = "mesin_{$mObj->id}";
            $machineName = $mObj->nama_mesin;
            $machineCode = $mObj->kode_mesin ?? '-';
            if ($mObj->lokasi) $location = $mObj->lokasi;

            // Electric Engine is the primary module for Forklifts
            if (
                $main->jenis_mtc === 'Electric Engine' ||
                stripos($mObj->nama_mesin, 'forklift') !== false ||
                stripos($mObj->kode_mesin ?? '', 'F') === 0 ||
                stripos($mObj->nama_mesin, 'pallet mover') !== false
            ) {
                $isForklift = true;
            }
        } elseif ($main->jenis_mtc === 'Electric Engine') {
            $machineKey  = "main_" . $main->id;
            $machineName = $main->area ? "Unit: {$main->area}" : "Forklift #" . $main->id;
            $machineCode = '-';
            $isForklift  = true;
        } elseif ($main->jenis_mtc === 'Battery' && $main->battery) {
            $machineKey  = "battery_" . ($main->battery->id ?? $main->id);
            $machineName = 'Unit: ' . ($main->battery->no_unit ?? '-') . ' (Seri: ' . ($main->battery->no_seri ?? '-') . ')';
            $machineCode = 'BATTERY';
            if (stripos($main->battery->no_unit ?? '', 'forklift') !== false || stripos($main->battery->tipe_unit ?? '', 'forklift') !== false) {
                $isForklift = true;
            }
        } elseif ($main->jenis_mtc === 'Sipil') {
            $areaVal     = $main->area ?? 'Umum';
            $machineKey  = "sipil_" . strtolower(trim($areaVal));
            $machineName = 'Sipil: ' . $areaVal;
            $machineCode = 'SIPIL';
        } else {
            $machineKey  = "main_" . $main->id;
            $machineName = $main->area ? "Area: {$main->area}" : ($main->jenis_mtc . ' #' . $main->id);
            $machineCode = '-';
            if (stripos($main->area ?? '', 'forklift') !== false || stripos($main->keterangan ?? '', 'forklift') !== false) {
                $isForklift = true;
            }
        }

        return [
            'key'         => $machineKey,
            'nama_mesin'  => $machineName,
            'kode_mesin'  => $machineCode,
            'lokasi'      => $location,
            'is_forklift' => $isForklift,
        ];
    }

    /**
     * Get price maps from MtcMasterMaterialModel.
     */
    private function getPriceMaps()
    {
        $materials = MtcMasterMaterialModel::select('mid', 'deskripsi', 'harga')->get();
        $midMap = [];
        $descMap = [];

        foreach ($materials as $m) {
            $price = floatval($m->harga);
            if (!empty($m->mid)) {
                $midMap[trim($m->mid)] = $price;
            }
            if (!empty($m->deskripsi)) {
                $descMap[strtolower(trim($m->deskripsi))] = $price;
            }
        }

        return [$midMap, $descMap];
    }

    /**
     * Get chart and summary card data for the dashboard (including Forklift analytics).
     */
    public function getDashboardCharts(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        list($midMap, $descMap) = $this->getPriceMaps();

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

        // ═════════════════════════════════════════════════════════════════
        // 6. FORKLIFT ANALYTICS (PENGGANTIAN PART & COST - KHUSUS MTC ELECTRIC ENGINE)
        // ═════════════════════════════════════════════════════════════════
        $forkliftMainsQuery = MtcMainModel::whereBetween('tanggal', [$startDate, $endDate])
            ->where(function ($q) {
                $q->where('jenis_mtc', 'Electric Engine')
                  ->orWhereHas('electricEngine');
            })
            ->where(function ($q) {
                $q->whereHas('penggantianMaterial', function ($p) {
                    $p->where('qty', '>', 0);
                })->orWhereHas('kebutuhanMaterial', function ($k) {
                    $k->where('qty', '>', 0);
                });
            })
            ->with([
                'electricEngine.mesin',
                'penggantianMaterial',
                'kebutuhanMaterial',
            ]);

        if ($request->filled('paket')) {
            $paket = trim($request->paket);
            if (strcasecmp($paket, 'Korektif') === 0) {
                $forkliftMainsQuery->whereRaw("LOWER(COALESCE(paket, '')) LIKE '%korektif%'");
            } elseif (strcasecmp($paket, 'Maintenance') === 0) {
                $forkliftMainsQuery->where(function ($q) {
                    $q->whereRaw("LOWER(COALESCE(paket, '')) NOT LIKE '%korektif%'")->orWhereNull('paket');
                });
            } else {
                $forkliftMainsQuery->where('paket', $paket);
            }
        }

        $allForkliftMains = $forkliftMainsQuery->get();

        $forkliftPartsPerUnit = [];
        $forkliftCostPerUnit  = [];
        $forkliftTopPartsMap  = [];
        $forkliftTopCostMap   = [];

        $totalForkliftCost     = 0;
        $totalForkliftCostWrh  = 0;
        $totalForkliftCostPrd  = 0;
        $totalForkliftPartsQty = 0;
        $forkliftJobIds        = [];
        $activeForklifts       = [];

        foreach ($allForkliftMains as $main) {
            $mesinObj = $main->electricEngine?->mesin;
            $unitName = $mesinObj?->nama_mesin ?? ($main->area ? "Unit: {$main->area}" : "Forklift #{$main->id}");
            $unitCode = $mesinObj?->kode_mesin ?? '-';

            // Determine if WRH (Warehouse) or PRD (Produksi)
            $deptStr = strtoupper(trim($mesinObj?->dept ?? $main->departemen ?? ''));
            $kodeStr = strtoupper(trim($unitCode));
            $nameStr = strtoupper(trim($unitName));
            $locStr  = strtoupper(trim($main->lokasi ?? $mesinObj?->lokasi ?? ''));

            $isPrd = (str_contains($deptStr, 'PRD') || str_contains($deptStr, 'PRODUKSI') || str_ends_with($kodeStr, '-PRD') || str_contains($nameStr, 'PRD') || str_contains($locStr, 'PRD'));
            $isWrh = !$isPrd; // If not PRD, it belongs to Warehouse (WRH) by default

            if (!isset($forkliftPartsPerUnit[$unitName])) {
                $forkliftPartsPerUnit[$unitName] = [
                    'unit'       => $unitName,
                    'kode'       => $unitCode,
                    'dept'       => $isPrd ? 'PRD' : 'WRH',
                    'total_qty'  => 0,
                    'total_cost' => 0,
                    'jobs'       => [],
                ];
            }

            if (!isset($forkliftCostPerUnit[$unitName])) {
                $forkliftCostPerUnit[$unitName] = [
                    'unit'       => $unitName,
                    'kode'       => $unitCode,
                    'dept'       => $isPrd ? 'PRD' : 'WRH',
                    'total_cost' => 0,
                    'total_qty'  => 0,
                    'total_jobs' => 0,
                ];
            }

            if (!in_array($main->id, $forkliftJobIds)) {
                $forkliftJobIds[] = $main->id;
            }
            if (!in_array($unitName, $activeForklifts)) {
                $activeForklifts[] = $unitName;
            }
            if (!in_array($main->id, $forkliftPartsPerUnit[$unitName]['jobs'])) {
                $forkliftPartsPerUnit[$unitName]['jobs'][] = $main->id;
            }

            foreach ($main->penggantianMaterial as $pm) {
                if ($pm->qty <= 0) continue;

                $midKey  = !empty($pm->mid) ? trim($pm->mid) : null;
                $descKey = !empty($pm->deskripsi) ? strtolower(trim($pm->deskripsi)) : null;

                $unitPrice = 0;
                if ($midKey && isset($midMap[$midKey])) {
                    $unitPrice = $midMap[$midKey];
                } elseif ($descKey && isset($descMap[$descKey])) {
                    $unitPrice = $descMap[$descKey];
                }

                $qty       = floatval($pm->qty);
                $cost      = $qty * $unitPrice;
                $partLabel = $pm->mid ? "{$pm->mid} - {$pm->deskripsi}" : ($pm->deskripsi ?? 'Tanpa Deskripsi');

                $totalForkliftCost     += $cost;
                $totalForkliftPartsQty += $qty;

                if ($isPrd) {
                    $totalForkliftCostPrd += $cost;
                } else {
                    $totalForkliftCostWrh += $cost;
                }

                $forkliftPartsPerUnit[$unitName]['total_qty']  += $qty;
                $forkliftPartsPerUnit[$unitName]['total_cost'] += $cost;

                $forkliftCostPerUnit[$unitName]['total_cost'] += $cost;
                $forkliftCostPerUnit[$unitName]['total_qty']  += $qty;

                // Part replacement frequency
                if (!isset($forkliftTopPartsMap[$partLabel])) {
                    $forkliftTopPartsMap[$partLabel] = [
                        'label'      => $partLabel,
                        'qty'        => 0,
                        'cost'       => 0,
                        'unit_price' => $unitPrice,
                    ];
                }
                $forkliftTopPartsMap[$partLabel]['qty']  += $qty;
                $forkliftTopPartsMap[$partLabel]['cost'] += $cost;

                // Part cost ranking
                if (!isset($forkliftTopCostMap[$partLabel])) {
                    $forkliftTopCostMap[$partLabel] = [
                        'label'      => $partLabel,
                        'qty'        => 0,
                        'total_cost' => 0,
                        'unit_price' => $unitPrice,
                    ];
                }
                $forkliftTopCostMap[$partLabel]['qty']        += $qty;
                $forkliftTopCostMap[$partLabel]['total_cost'] += $cost;
            }
        }

        // Sort Top Replaced Parts on Forklift (by Qty DESC)
        $forkliftTopParts = array_values($forkliftTopPartsMap);
        usort($forkliftTopParts, fn($a, $b) => $b['qty'] <=> $a['qty']);
        $forkliftTopParts = array_slice($forkliftTopParts, 0, 10);

        // Sort Top Cost Parts on Forklift (by Total Cost DESC)
        $forkliftTopCostParts = array_values($forkliftTopCostMap);
        usort($forkliftTopCostParts, fn($a, $b) => $b['total_cost'] <=> $a['total_cost']);
        $forkliftTopCostParts = array_slice($forkliftTopCostParts, 0, 10);
        foreach ($forkliftTopCostParts as &$fcp) {
            $fcp['total_cost_fmt'] = 'Rp ' . number_format($fcp['total_cost'], 0, ',', '.');
        }

        // Sort Cost per Unit (by Cost DESC)
        $forkliftCostPerUnitList = array_values($forkliftCostPerUnit);
        foreach ($forkliftCostPerUnitList as &$item) {
            $item['total_jobs']     = count($forkliftPartsPerUnit[$item['unit']]['jobs'] ?? []);
            $item['total_cost_fmt'] = 'Rp ' . number_format($item['total_cost'], 0, ',', '.');
        }
        usort($forkliftCostPerUnitList, fn($a, $b) => $b['total_cost'] <=> $a['total_cost'] ?: strcmp($a['unit'], $b['unit']));

        // Sort Parts per Unit (by Qty DESC)
        $forkliftPartsPerUnitList = array_values($forkliftPartsPerUnit);
        foreach ($forkliftPartsPerUnitList as &$item) {
            $item['total_jobs']     = count($item['jobs'] ?? []);
            $item['total_cost_fmt'] = 'Rp ' . number_format($item['total_cost'], 0, ',', '.');
            unset($item['jobs']);
        }
        usort($forkliftPartsPerUnitList, fn($a, $b) => $b['total_qty'] <=> $a['total_qty'] ?: strcmp($a['unit'], $b['unit']));

        return response()->json([
            'status' => 200,
            'summary' => [
                'total_qty_kebutuhan'   => $totalQtyKebutuhan,
                'total_qty_penggantian' => $totalQtyPenggantian,
                'unique_items'          => $uniqueItemsCount,
                'total_jobs'            => $totalJobsCount,
            ],
            'forklift_summary' => [
                'total_cost'            => $totalForkliftCost,
                'total_cost_fmt'        => 'Rp ' . number_format($totalForkliftCost, 0, ',', '.'),
                'total_cost_wrh'        => $totalForkliftCostWrh,
                'total_cost_wrh_fmt'    => 'Rp ' . number_format($totalForkliftCostWrh, 0, ',', '.'),
                'total_cost_prd'        => $totalForkliftCostPrd,
                'total_cost_prd_fmt'    => 'Rp ' . number_format($totalForkliftCostPrd, 0, ',', '.'),
                'total_parts_qty'       => $totalForkliftPartsQty,
                'total_jobs'            => count($forkliftJobIds),
                'active_units_count'    => count($activeForklifts),
            ],
            'charts' => [
                'top_kebutuhan'           => $topKebutuhan,
                'top_penggantian'         => $topPenggantian,
                'forklift_top_parts'      => $forkliftTopParts,
                'forklift_parts_per_unit' => $forkliftPartsPerUnitList,
                'forklift_cost_per_unit'  => $forkliftCostPerUnitList,
                'forklift_top_cost_parts' => $forkliftTopCostParts,
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

        list($midMap, $descMap) = $this->getPriceMaps();

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
                $mainQuery->whereRaw("LOWER(COALESCE(paket, '')) LIKE '%korektif%'");
            } elseif (strcasecmp($paket, 'Maintenance') === 0) {
                $mainQuery->where(function ($q) {
                    $q->whereRaw("LOWER(COALESCE(paket, '')) NOT LIKE '%korektif%'")
                        ->orWhereNull('paket');
                });
            } else {
                $mainQuery->where('paket', $paket);
            }
        }

        $allMains = $mainQuery->get();

        $grouped = [];

        foreach ($allMains as $main) {
            $mInfo = $this->resolveMachineInfo($main);
            $machineKey  = $mInfo['key'];
            $machineName = $mInfo['nama_mesin'];
            $machineCode = $mInfo['kode_mesin'];
            $location    = $mInfo['lokasi'];

            $kebQty  = floatval($main->kebutuhanMaterial->where('qty', '>', 0)->sum('qty'));
            $pengQty = floatval($main->penggantianMaterial->where('qty', '>', 0)->sum('qty'));

            // Calculate cost for this main record
            $mainCost = 0;
            foreach ($main->penggantianMaterial as $pm) {
                if ($pm->qty <= 0) continue;
                $midKey  = !empty($pm->mid) ? trim($pm->mid) : null;
                $descKey = !empty($pm->deskripsi) ? strtolower(trim($pm->deskripsi)) : null;

                $unitPrice = 0;
                if ($midKey && isset($midMap[$midKey])) {
                    $unitPrice = $midMap[$midKey];
                } elseif ($descKey && isset($descMap[$descKey])) {
                    $unitPrice = $descMap[$descKey];
                }

                $mainCost += ($pm->qty * $unitPrice);
            }

            if (!isset($grouped[$machineKey])) {
                $grouped[$machineKey] = [
                    'key'                   => $machineKey,
                    'nama_mesin'            => $machineName,
                    'kode_mesin'            => $machineCode,
                    'jenis_mtc'             => $main->jenis_mtc ?? '-',
                    'lokasi'                => $location,
                    'is_forklift'           => $mInfo['is_forklift'],
                    'total_kebutuhan_qty'   => 0,
                    'total_penggantian_qty' => 0,
                    'total_cost'            => 0,
                    'main_ids'              => [],
                    'total_pekerjaan'       => 0,
                ];
            }

            $grouped[$machineKey]['total_kebutuhan_qty']   += $kebQty;
            $grouped[$machineKey]['total_penggantian_qty'] += $pengQty;
            $grouped[$machineKey]['total_cost']            += $mainCost;

            if (!in_array($main->id, $grouped[$machineKey]['main_ids'])) {
                $grouped[$machineKey]['main_ids'][] = $main->id;
            }
        }

        // Compute total_pekerjaan & formatting
        $result = array_values(array_map(function ($item) {
            $item['total_pekerjaan'] = count($item['main_ids']);
            $item['total_cost_fmt']  = $item['total_cost'] > 0 ? ('Rp ' . number_format($item['total_cost'], 0, ',', '.')) : 'Rp 0';
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

        list($midMap, $descMap) = $this->getPriceMaps();

        $mains = MtcMainModel::whereIn('id', $mainIds)
            ->with(['createdBy', 'kebutuhanMaterial', 'penggantianMaterial'])
            ->orderBy('tanggal', 'desc')
            ->get();

        $details = [];

        foreach ($mains as $main) {
            $tglStr  = $main->tanggal ? $main->tanggal->format('d M Y') : '-';
            $tglRaw  = $main->tanggal ? $main->tanggal->format('Y-m-d') : '';
            $teknisi = $main->createdBy?->name ?? 'Teknisi';
            $paket   = $main->paket ?: '-';

            // 1. Kebutuhan Material
            foreach ($main->kebutuhanMaterial as $item) {
                if ($item->qty <= 0) continue;

                $midKey  = !empty($item->mid) ? trim($item->mid) : null;
                $descKey = !empty($item->deskripsi) ? strtolower(trim($item->deskripsi)) : null;

                $unitPrice = 0;
                if ($midKey && isset($midMap[$midKey])) {
                    $unitPrice = $midMap[$midKey];
                } elseif ($descKey && isset($descMap[$descKey])) {
                    $unitPrice = $descMap[$descKey];
                }

                $totalPrice = $item->qty * $unitPrice;

                $details[] = [
                    'id'              => 'keb_' . $item->id,
                    'tanggal'         => $tglStr,
                    'tanggal_raw'     => $tglRaw,
                    'jenis_mtc'       => $main->jenis_mtc ?? '-',
                    'paket'           => $paket,
                    'kategori'        => 'Kebutuhan',
                    'mid'             => $item->mid ?? '-',
                    'deskripsi'       => $item->deskripsi ?? '-',
                    'qty'             => floatval($item->qty),
                    'satuan'          => $item->uom ?? '-',
                    'harga'           => $unitPrice > 0 ? ('Rp ' . number_format($unitPrice, 0, ',', '.')) : '-',
                    'harga_val'       => $unitPrice,
                    'total_harga'     => $totalPrice > 0 ? ('Rp ' . number_format($totalPrice, 0, ',', '.')) : '-',
                    'total_harga_val' => $totalPrice,
                    'teknisi'         => $teknisi,
                ];
            }

            // 2. Penggantian Material
            foreach ($main->penggantianMaterial as $item) {
                if ($item->qty <= 0) continue;

                $midKey  = !empty($item->mid) ? trim($item->mid) : null;
                $descKey = !empty($item->deskripsi) ? strtolower(trim($item->deskripsi)) : null;

                $unitPrice = 0;
                if ($midKey && isset($midMap[$midKey])) {
                    $unitPrice = $midMap[$midKey];
                } elseif ($descKey && isset($descMap[$descKey])) {
                    $unitPrice = $descMap[$descKey];
                }

                $totalPrice = $item->qty * $unitPrice;

                $details[] = [
                    'id'              => 'peng_' . $item->id,
                    'tanggal'         => $tglStr,
                    'tanggal_raw'     => $tglRaw,
                    'jenis_mtc'       => $main->jenis_mtc ?? '-',
                    'paket'           => $paket,
                    'kategori'        => 'Penggantian',
                    'mid'             => $item->mid ?? '-',
                    'deskripsi'       => $item->deskripsi ?? '-',
                    'qty'             => floatval($item->qty),
                    'satuan'          => $item->uom ?? '-',
                    'harga'           => $unitPrice > 0 ? ('Rp ' . number_format($unitPrice, 0, ',', '.')) : '-',
                    'harga_val'       => $unitPrice,
                    'total_harga'     => $totalPrice > 0 ? ('Rp ' . number_format($totalPrice, 0, ',', '.')) : '-',
                    'total_harga_val' => $totalPrice,
                    'teknisi'         => $teknisi,
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
