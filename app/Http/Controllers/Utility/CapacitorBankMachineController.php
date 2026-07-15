<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\CapacitorBankCapHistory;
use App\Models\Utility\CapacitorBankMachineData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapacitorBankMachineController extends Controller
{
    /**
     * POST /api/utility/capacitor-bank/machine-data
     *
     * Menerima data dari mesin (IoT / edge device).
     * - cap_type : string nama kapasitor aktif dari mesin, mis. "cap1", "cap11"
     *              → secara otomatis di-resolve ke baris history:
     *                cap yang sesuai = 1, sisanya = 0
     * - current  : satu nilai arus total
     * - Sisa     : power meter (voltage, power, pf, cosphi, freq, thd)
     *
     * Contoh payload:
     * {
     *   "cap_type": "cap11",
     *   "current": 204.863,
     *   "voltage_ll": {"Vab":411.72,"Vbc":412.38,"Vca":414.30},
     *   "voltage_ln": {"Van":237.96,"Vbn":237.95,"Vcn":239.10},
     *   "power":  {"Ptot":62.32,"Qtot":-27.52,"Stot":68.13},
     *   "pf":     {"PFa":-0.2662,"PFb":1.0878,"PFc":0.3068},
     *   "cosphi": {"dPFa":-0.2881,"dPFb":1.0517,"dPFc":0.3384},
     *   "freq":   49.94,
     *   "thd_i":  {"Ia":-0.00,"Ib":25855080.00,"Ic":2326512.00},
     *   "thd_v":  {"Van":-49.56,"Vbn":-0.00,"Vcn":-5128194.00}
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'nullable',
            // ── Cap type & status ──────────────────────────────
            'cap_type' => 'nullable|string|max:255',

            // ── Current (single value) ─────────────────────────
            'current' => 'nullable|numeric',

            // ── Voltage LL ────────────────────────────────────
            'voltage_ll'      => 'nullable|array',
            'voltage_ll.Vab'  => 'nullable|numeric',
            'voltage_ll.Vbc'  => 'nullable|numeric',
            'voltage_ll.Vca'  => 'nullable|numeric',

            // ── Voltage LN ────────────────────────────────────
            'voltage_ln'      => 'nullable|array',
            'voltage_ln.Van'  => 'nullable|numeric',
            'voltage_ln.Vbn'  => 'nullable|numeric',
            'voltage_ln.Vcn'  => 'nullable|numeric',

            // ── Power (total only) ────────────────────────────
            'power'      => 'nullable|array',
            'power.Ptot' => 'nullable|numeric',
            'power.Qtot' => 'nullable|numeric',
            'power.Stot' => 'nullable|numeric',

            // ── Power Factor ──────────────────────────────────
            'pf'      => 'nullable|array',
            'pf.PFa'  => 'nullable|numeric',
            'pf.PFb'  => 'nullable|numeric',
            'pf.PFc'  => 'nullable|numeric',

            // ── Cos Phi ───────────────────────────────────────
            'cosphi'        => 'nullable|array',
            'cosphi.dPFa'   => 'nullable|numeric',
            'cosphi.dPFb'   => 'nullable|numeric',
            'cosphi.dPFc'   => 'nullable|numeric',

            // ── Frequency ─────────────────────────────────────
            'freq' => 'nullable|numeric',

            // ── THD Current ───────────────────────────────────
            'thd_i'    => 'nullable|array',
            'thd_i.Ia' => 'nullable|numeric',
            'thd_i.Ib' => 'nullable|numeric',
            'thd_i.Ic' => 'nullable|numeric',

            // ── THD Voltage ───────────────────────────────────
            'thd_v'     => 'nullable|array',
            'thd_v.Van' => 'nullable|numeric',
            'thd_v.Vbn' => 'nullable|numeric',
            'thd_v.Vcn' => 'nullable|numeric',
        ]);

        $now = Carbon::now();

        DB::beginTransaction();
        try {
            // ── 1. Simpan power meter data ─────────────────────
            $machineData = CapacitorBankMachineData::create([
                'tanggal' => $request->tanggal,
                'cap_type' => $request->cap_type,
                'current'  => $request->current,

                'voltage_ll_Vab' => $request->input('voltage_ll.Vab'),
                'voltage_ll_Vbc' => $request->input('voltage_ll.Vbc'),
                'voltage_ll_Vca' => $request->input('voltage_ll.Vca'),

                'voltage_ln_Van' => $request->input('voltage_ln.Van'),
                'voltage_ln_Vbn' => $request->input('voltage_ln.Vbn'),
                'voltage_ln_Vcn' => $request->input('voltage_ln.Vcn'),

                'power_Ptot' => $request->input('power.Ptot'),
                'power_Qtot' => $request->input('power.Qtot'),
                'power_Stot' => $request->input('power.Stot'),

                'pf_PFa' => $request->input('pf.PFa'),
                'pf_PFb' => $request->input('pf.PFb'),
                'pf_PFc' => $request->input('pf.PFc'),

                'cosphi_dPFa' => $request->input('cosphi.dPFa'),
                'cosphi_dPFb' => $request->input('cosphi.dPFb'),
                'cosphi_dPFc' => $request->input('cosphi.dPFc'),

                'freq' => $request->freq,

                'thd_i_Ia' => $request->input('thd_i.Ia'),
                'thd_i_Ib' => $request->input('thd_i.Ib'),
                'thd_i_Ic' => $request->input('thd_i.Ic'),

                'thd_v_Van' => $request->input('thd_v.Van'),
                'thd_v_Vbn' => $request->input('thd_v.Vbn'),
                'thd_v_Vcn' => $request->input('thd_v.Vcn'),
            ]);

            // ── 2. Insert cap ON/OFF history ──────────────────────
            // Parse cap_type string dari mesin (mis. "cap1", "cap11")
            // → cap yang sesuai = 1, sisanya = 0
            $activeCapNum = null;
            if ($request->cap_type) {
                // Ambil angka dari string, mis. "cap11" → 11
                preg_match('/(\d+)$/', $request->cap_type, $matches);
                $activeCapNum = isset($matches[1]) ? (int) $matches[1] : null;
            }

            $capRow = ['tanggal' => $now->toDateString(), 'recorded_at' => $now];
            for ($i = 1; $i <= 12; $i++) {
                $capRow["cap{$i}"] = ($activeCapNum === $i) ? 1 : 0;
            }

            CapacitorBankCapHistory::create($capRow);

            DB::commit();

            return response()->json([
                'message'      => 'Data mesin berhasil disimpan.',
                'machine_data' => $machineData,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menyimpan data mesin.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================
    // POWER METER DATA
    // =========================================================

    /**
     * GET /api/utility/capacitor-bank/machine-data/latest
     * Ambil data power meter paling terakhir.
     */
    public function latest()
    {
        $data = CapacitorBankMachineData::latest()->first();

        if (!$data) {
            return response()->json(['message' => 'Belum ada data mesin.'], 404);
        }

        return response()->json($data);
    }

    /**
     * GET /api/utility/capacitor-bank/machine-data
     * List data power meter dengan paginasi (terbaru duluan).
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 50);

        return response()->json(
            CapacitorBankMachineData::latest()->paginate($perPage)
        );
    }

    // =========================================================
    // CAP HISTORY
    // =========================================================

    /**
     * GET /api/utility/capacitor-bank/cap-history
     *
     * Query params:
     *   - tanggal      : Y-m-d  → ambil history 1 hari
     *   - dari / sampai: Y-m-d  → ambil range tanggal
     *   - per_page     : int    (default 100)
     *
     * Kalau tidak ada param, ambil hari ini.
     */
    public function capHistory(Request $request)
    {
        $request->validate([
            'tanggal' => 'nullable|date',
            'dari'    => 'nullable|date',
            'sampai'  => 'nullable|date|after_or_equal:dari',
            'per_page' => 'nullable|integer|min:1|max:1000',
        ]);

        $perPage = $request->input('per_page', 100);
        $query   = CapacitorBankCapHistory::orderBy('recorded_at');

        if ($request->tanggal) {
            $query->byDate($request->tanggal);
        } elseif ($request->dari && $request->sampai) {
            $query->byDateRange($request->dari, $request->sampai);
        } else {
            // Default: hari ini
            $query->byDate(now()->toDateString());
        }

        return response()->json($query->paginate($perPage));
    }

    /**
     * GET /api/utility/capacitor-bank/cap-history/summary
     *
     * Ringkasan per hari: berapa kali tiap cap aktif (cap=1) dalam sehari.
     *
     * Query params:
     *   - tanggal: Y-m-d (default: hari ini)
     */
    public function capHistorySummary(Request $request)
    {
        $request->validate([
            'tanggal' => 'nullable|date',
        ]);

        $tanggal = $request->tanggal ?? now()->toDateString();

        $rows = CapacitorBankCapHistory::byDate($tanggal)->get();

        $summary = [];
        for ($i = 1; $i <= 12; $i++) {
            $key           = "cap{$i}";
            $summary[$key] = [
                'total_records' => $rows->count(),
                'on_count'      => $rows->where($key, 1)->count(),
                'off_count'     => $rows->where($key, 0)->count(),
            ];
        }

        return response()->json([
            'tanggal' => $tanggal,
            'summary' => $summary,
        ]);
    }

    /**
     * GET /utility/capacitor-bank/report
     * Render the report dashboard view.
     */
    public function reportView()
    {
        return view('utility.capacitor-bank.report');
    }

    /**
     * GET /utility/capacitor-bank/report/data
     * Get JSON data for the report dashboard based on a selected date.
     */
    public function reportData(Request $request)
    {
        $request->validate([
            'tanggal' => 'nullable|date',
        ]);

        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        // 1. Fetch data
        $machineData = CapacitorBankMachineData::where('tanggal', $tanggal)
            ->orderBy('created_at', 'asc')
            ->get();

        $capHistories = CapacitorBankCapHistory::where('tanggal', $tanggal)
            ->orderBy('recorded_at', 'asc')
            ->get();

        // 2. Compute averages for summary cards
        $count = $machineData->count();
        $avgCurrent = $count > 0 ? (float) $machineData->avg('current') : 0;
        
        $avgVllVab = $count > 0 ? (float) $machineData->avg('voltage_ll_Vab') : 0;
        $avgVllVbc = $count > 0 ? (float) $machineData->avg('voltage_ll_Vbc') : 0;
        $avgVllVca = $count > 0 ? (float) $machineData->avg('voltage_ll_Vca') : 0;
        $avgVll = $count > 0 ? ($avgVllVab + $avgVllVbc + $avgVllVca) / 3 : 0;

        $avgVlnVan = $count > 0 ? (float) $machineData->avg('voltage_ln_Van') : 0;
        $avgVlnVbn = $count > 0 ? (float) $machineData->avg('voltage_ln_Vbn') : 0;
        $avgVlnVcn = $count > 0 ? (float) $machineData->avg('voltage_ln_Vcn') : 0;
        $avgVln = $count > 0 ? ($avgVlnVan + $avgVlnVbn + $avgVlnVcn) / 3 : 0;

        $avgPtot = $count > 0 ? (float) $machineData->avg('power_Ptot') : 0;
        $avgQtot = $count > 0 ? (float) $machineData->avg('power_Qtot') : 0;
        $avgStot = $count > 0 ? (float) $machineData->avg('power_Stot') : 0;

        $avgPFa = $count > 0 ? (float) $machineData->avg('pf_PFa') : 0;
        $avgPFb = $count > 0 ? (float) $machineData->avg('pf_PFb') : 0;
        $avgPFc = $count > 0 ? (float) $machineData->avg('pf_PFc') : 0;
        $avgPF = $count > 0 ? ($avgPFa + $avgPFb + $avgPFc) / 3 : 0;

        $avgFreq = $count > 0 ? (float) $machineData->avg('freq') : 0;

        $latestCapType = 'Tidak ada';
        if ($machineData->isNotEmpty()) {
            $latestTs = $machineData->last()->created_at;
            $latestRecords = $machineData->filter(function ($item) use ($latestTs) {
                return $item->created_at == $latestTs;
            });
            $latestCaps = [];
            foreach ($latestRecords as $lr) {
                if ($lr->cap_type) {
                    $latestCaps[] = strtoupper($lr->cap_type);
                }
            }
            $latestCapType = count($latestCaps) > 0 ? implode(', ', array_unique($latestCaps)) : 'Tidak ada';
        }

        // Group machine data by created_at (actual timestamp) to combine multiple capacitor activations
        $groupedByTime = [];
        foreach ($machineData as $data) {
            $ts = Carbon::parse($data->created_at)->toDateTimeString();
            if (!isset($groupedByTime[$ts])) {
                $groupedByTime[$ts] = [
                    'timestamp' => $ts,
                    'created_at' => $data->created_at,
                    'active_caps' => [],
                ];
            }
            if ($data->cap_type) {
                $groupedByTime[$ts]['active_caps'][] = $data->cap_type;
            }
        }

        // Sort chronologically by timestamp key
        ksort($groupedByTime);

        // 3. Compute Capacitor Switch-On/Off frequency and status based on grouped intervals
        $capSummary = [];
        for ($i = 1; $i <= 12; $i++) {
            $key = "cap{$i}";
            $capSummary[$key] = [
                'on_count'  => 0,
                'off_count' => 0,
            ];
        }

        foreach ($groupedByTime as $ts => $group) {
            for ($i = 1; $i <= 12; $i++) {
                $key = "cap{$i}";
                if (in_array($key, $group['active_caps'])) {
                    $capSummary[$key]['on_count']++;
                } else {
                    $capSummary[$key]['off_count']++;
                }
            }
        }

        // 4. Compute ON/OFF state transitions throughout the day
        $transitions = [];
        $prevState = [];

        for ($i = 1; $i <= 12; $i++) {
            $prevState["cap{$i}"] = 0; // Default to OFF
        }

        // Get the last active state before this date
        $lastPriorMachineData = CapacitorBankMachineData::where('tanggal', '<', $tanggal)
            ->orderBy('created_at', 'desc')
            ->get(); // Get the latest ones to reconstruct the state

        if ($lastPriorMachineData->isNotEmpty()) {
            // Reconstruct the last state by looking at the last unique timestamp's records
            $lastPriorTs = $lastPriorMachineData->first()->created_at;
            $priorRecords = $lastPriorMachineData->filter(function ($item) use ($lastPriorTs) {
                return $item->created_at == $lastPriorTs;
            });
            foreach ($priorRecords as $prior) {
                if ($prior->cap_type) {
                    preg_match('/(\d+)$/', $prior->cap_type, $matches);
                    $priorActiveNum = isset($matches[1]) ? (int) $matches[1] : null;
                    if ($priorActiveNum !== null) {
                        $prevState["cap{$priorActiveNum}"] = 1;
                    }
                }
            }
        }

        foreach ($groupedByTime as $ts => $group) {
            $createdAt = Carbon::parse($group['created_at']);
            $timeFormatted = $createdAt->format('H:i:s');

            // Current state for this timestamp
            $currState = [];
            for ($i = 1; $i <= 12; $i++) {
                $capKey = "cap{$i}";
                $currState[$capKey] = in_array($capKey, $group['active_caps']) ? 1 : 0;
            }

            // Check for changes
            for ($i = 1; $i <= 12; $i++) {
                $capKey = "cap{$i}";
                $currVal = $currState[$capKey];
                $prevVal = $prevState[$capKey];

                if ($currVal !== $prevVal) {
                    $transitions[] = [
                        'timestamp' => $createdAt->toDateTimeString(),
                        'capacitor' => "Capacitor {$i}",
                        'event'     => $currVal === 1 ? 'ON' : 'OFF',
                        'time_formatted' => $timeFormatted,
                    ];
                    $prevState[$capKey] = $currVal;
                }
            }
        }

        // Sort transitions chronologically (descending: latest first)
        usort($transitions, function ($a, $b) {
            return strcmp($b['timestamp'], $a['timestamp']);
        });

        // 5. Map Trends data
        $groupedTrends = [];
        foreach ($machineData as $data) {
            $time = Carbon::parse($data->created_at)->format('H:i');

            $activeCapNum = null;
            if ($data->cap_type) {
                preg_match('/(\d+)$/', $data->cap_type, $matches);
                $activeCapNum = isset($matches[1]) ? (int) $matches[1] : null;
            }

            $currentA = null;
            $currentB = null;

            if ($activeCapNum !== null) {
                if (in_array($activeCapNum, [1, 3, 5, 7, 9, 11])) {
                    $currentA = (float) $data->current;
                } elseif (in_array($activeCapNum, [2, 4, 6, 8, 10, 12])) {
                    $currentB = (float) $data->current;
                }
            }

            if (!isset($groupedTrends[$time])) {
                $groupedTrends[$time] = [
                    'time' => $time,
                    'current' => (float) $data->current,
                    'current_a' => $currentA,
                    'current_b' => $currentB,
                    'v_ab' => (float) $data->voltage_ll_Vab,
                    'v_bc' => (float) $data->voltage_ll_Vbc,
                    'v_ca' => (float) $data->voltage_ll_Vca,
                    'v_an' => (float) $data->voltage_ln_Van,
                    'v_bn' => (float) $data->voltage_ln_Vbn,
                    'v_cn' => (float) $data->voltage_ln_Vcn,
                    'p_tot' => (float) $data->power_Ptot,
                    'q_tot' => (float) $data->power_Qtot,
                    's_tot' => (float) $data->power_Stot,
                    'cap_types' => $data->cap_type ? [$data->cap_type] : [],
                ];
            } else {
                if ($currentA !== null) {
                    $groupedTrends[$time]['current_a'] = $currentA;
                }
                if ($currentB !== null) {
                    $groupedTrends[$time]['current_b'] = $currentB;
                }
                if ($data->current !== null) {
                    $groupedTrends[$time]['current'] = ($groupedTrends[$time]['current'] + (float)$data->current) / 2;
                }
                if ($data->cap_type) {
                    $groupedTrends[$time]['cap_types'][] = $data->cap_type;
                }
                if ($data->voltage_ll_Vab !== null) {
                    $groupedTrends[$time]['v_ab'] = ($groupedTrends[$time]['v_ab'] + (float)$data->voltage_ll_Vab) / 2;
                }
                if ($data->voltage_ll_Vbc !== null) {
                    $groupedTrends[$time]['v_bc'] = ($groupedTrends[$time]['v_bc'] + (float)$data->voltage_ll_Vbc) / 2;
                }
                if ($data->voltage_ll_Vca !== null) {
                    $groupedTrends[$time]['v_ca'] = ($groupedTrends[$time]['v_ca'] + (float)$data->voltage_ll_Vca) / 2;
                }
                if ($data->voltage_ln_Van !== null) {
                    $groupedTrends[$time]['v_an'] = ($groupedTrends[$time]['v_an'] + (float)$data->voltage_ln_Van) / 2;
                }
                if ($data->voltage_ln_Vbn !== null) {
                    $groupedTrends[$time]['v_bn'] = ($groupedTrends[$time]['v_bn'] + (float)$data->voltage_ln_Vbn) / 2;
                }
                if ($data->voltage_ln_Vcn !== null) {
                    $groupedTrends[$time]['v_cn'] = ($groupedTrends[$time]['v_cn'] + (float)$data->voltage_ln_Vcn) / 2;
                }
                if ($data->power_Ptot !== null) {
                    $groupedTrends[$time]['p_tot'] = ($groupedTrends[$time]['p_tot'] + (float)$data->power_Ptot) / 2;
                }
                if ($data->power_Qtot !== null) {
                    $groupedTrends[$time]['q_tot'] = ($groupedTrends[$time]['q_tot'] + (float)$data->power_Qtot) / 2;
                }
                if ($data->power_Stot !== null) {
                    $groupedTrends[$time]['s_tot'] = ($groupedTrends[$time]['s_tot'] + (float)$data->power_Stot) / 2;
                }
            }
        }

        foreach ($groupedTrends as &$trend) {
            $trend['cap_type'] = count($trend['cap_types']) > 0 ? implode(', ', array_unique($trend['cap_types'])) : 'None';
            unset($trend['cap_types']);
        }
        $trends = array_values($groupedTrends);

        // 6. Map raw data table (all details)
        $rawTable = [];
        foreach ($machineData as $data) {
            $activeCapNum = null;
            if ($data->cap_type) {
                preg_match('/(\d+)$/', $data->cap_type, $matches);
                $activeCapNum = isset($matches[1]) ? (int) $matches[1] : null;
            }

            $currentA = null;
            $currentB = null;

            if ($activeCapNum !== null) {
                if (in_array($activeCapNum, [1, 3, 5, 7, 9, 11])) {
                    $currentA = $data->current;
                } elseif (in_array($activeCapNum, [2, 4, 6, 8, 10, 12])) {
                    $currentB = $data->current;
                }
            }

            $rawTable[] = [
                'time' => Carbon::parse($data->created_at)->format('H:i:s'),
                'cap_type' => $data->cap_type ?: 'None',
                'current' => $data->current,
                'current_a' => $currentA,
                'current_b' => $currentB,
                'v_ab' => $data->voltage_ll_Vab,
                'v_bc' => $data->voltage_ll_Vbc,
                'v_ca' => $data->voltage_ll_Vca,
                'v_an' => $data->voltage_ln_Van,
                'v_bn' => $data->voltage_ln_Vbn,
                'v_cn' => $data->voltage_ln_Vcn,
                'p_tot' => $data->power_Ptot,
                'q_tot' => $data->power_Qtot,
                's_tot' => $data->power_Stot,
                'pf' => $data->pf_PFa !== null ? ($data->pf_PFa + $data->pf_PFb + $data->pf_PFc)/3 : null,
                'cosphi' => $data->cosphi_dPFa !== null ? ($data->cosphi_dPFa + $data->cosphi_dPFb + $data->cosphi_dPFc)/3 : null,
                'freq' => $data->freq,
            ];
        }

        return response()->json([
            'tanggal' => $tanggal,
            'summary' => [
                'avg_current' => round($avgCurrent, 2),
                'avg_vll' => round($avgVll, 2),
                'avg_vll_vab' => round($avgVllVab, 2),
                'avg_vll_vbc' => round($avgVllVbc, 2),
                'avg_vll_vca' => round($avgVllVca, 2),
                'avg_vln' => round($avgVln, 2),
                'avg_vln_van' => round($avgVlnVan, 2),
                'avg_vln_vbn' => round($avgVlnVbn, 2),
                'avg_vln_vcn' => round($avgVlnVcn, 2),
                'avg_ptot' => round($avgPtot, 2),
                'avg_qtot' => round($avgQtot, 2),
                'avg_stot' => round($avgStot, 2),
                'avg_pf' => round($avgPF, 3),
                'avg_freq' => round($avgFreq, 2),
                'latest_cap_type' => $latestCapType,
                'total_records' => $count,
                'total_transitions' => count($transitions),
            ],
            'cap_summary' => $capSummary,
            'transitions' => $transitions,
            'trends' => $trends,
            'raw_table' => $rawTable,
        ]);
    }
}
