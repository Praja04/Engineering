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
}
