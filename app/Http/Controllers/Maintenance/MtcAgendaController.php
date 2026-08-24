<?php

namespace App\Http\Controllers\Maintenance;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Maintenance\MtcMasterMesinModel;
use App\Models\Maintenance\MtcAgendaModel;
use App\Http\Requests\Maintenance\MtcAgendaUploadRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MtcAgendaController extends Controller
{
    /**
     * Map jenis_mtc → inspection table yang punya kolom mesin_id
     */
    private array $inspectionTables = [
        'Refrigerasi'     => 'mtc_refrigerasi_inspections',
        'Motor Pompa'      => 'mtc_motor_pump_inspections',
        'Utility'         => 'mtc_utility_inspections',
        'Electrical'      => 'mtc_electrical_inspections',
        'Electric Engine' => 'mtc_electric_engine_inspections',
        'Diesel Engine'   => 'mtc_diesel_engine_inspections',
        'Electric P2H'    => 'mtc_electric_p2h_inspections',
        'Diesel P2H'      => 'mtc_diesel_p2h_inspections',
    ];

    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        // Ambil semua jenis_mtc yang ada di master mesin
        $jenisMtcList = MtcMasterMesinModel::where('aktif', true)
            ->distinct()
            ->orderBy('jenis_mtc')
            ->pluck('jenis_mtc');

        $selectedJenis = $request->get('jenis_mtc', $jenisMtcList->first());

        // Mesin aktif untuk jenis terpilih, beserta frekuensi
        $mesinList = MtcMasterMesinModel::where('jenis_mtc', $selectedJenis)
            ->where('aktif', true)
            ->with(['frekuensi'])
            ->orderBy('kode_mesin')
            ->get();

        // Bangun data agenda
        $agendaData = $mesinList->map(function ($mesin) use ($selectedJenis) {
            $lastDate = $this->getLastMaintenanceDate($mesin->id, $selectedJenis);

            $schedules = $mesin->frekuensi->map(function ($frek) use ($lastDate) {
                $nextDue = $this->calculateNextDue($lastDate, $frek->interval, $frek->satuan);
                $daysLeft = (int) Carbon::today()->diffInDays($nextDue, false); // negatif = lewat
                $status   = $this->resolveStatus($daysLeft, $lastDate);

                return [
                    'label'     => $frek->label,
                    'interval'  => $frek->interval,
                    'satuan'    => $frek->satuan,
                    'next_due'  => $nextDue,
                    'days_left' => $daysLeft,
                    'status'    => $status,
                ];
            });

            // Jika tidak ada frekuensi, tetap tampilkan mesin dengan status no_schedule
            if ($schedules->isEmpty()) {
                $schedules = collect([[
                    'label'     => '-',
                    'interval'  => null,
                    'satuan'    => null,
                    'next_due'  => null,
                    'days_left' => null,
                    'status'    => 'no_schedule',
                ]]);
            }

            return [
                'mesin'     => $mesin,
                'last_date' => $lastDate,
                'schedules' => $schedules,
            ];
        });

        // Ringkasan status untuk semua schedule
        $summary = $this->buildSummary($agendaData);

        // Untuk filter bulan di kalender (opsional, dipakai di tab Kalender)
        $calendarMonth = $request->get('month', Carbon::today()->format('Y-m'));

        // Flatten agenda untuk tampilan kalender
        $calendarEvents = $this->buildCalendarEvents($agendaData, $calendarMonth);

        return view('dashboard.maintenance.agenda', compact(
            'jenisMtcList',
            'selectedJenis',
            'agendaData',
            'summary',
            'calendarMonth',
            'calendarEvents'
        ));
    }

    /**
     * Dedicated view to manage and upload Master Agenda Plan
     */
    public function master(Request $request)
    {
        $jenisMtcList = MtcMasterMesinModel::where('aktif', true)
            ->distinct()
            ->orderBy('jenis_mtc')
            ->pluck('jenis_mtc');

        $selectedJenis = $request->get('jenis_mtc', $jenisMtcList->first());
        $selectedYear = (int) $request->get('tahun', Carbon::today()->year);

        return view('maintenance.master.master_agenda', compact(
            'jenisMtcList',
            'selectedJenis',
            'selectedYear'
        ));
    }

    /**
     * Fetch planning matrix data for AJAX rendering
     */
    public function getMasterData(Request $request)
    {
        $jenisMtc = $request->get('jenis_mtc');
        $tahun = (int) $request->get('tahun', Carbon::today()->year);

        // Get active machines for the MTC type
        $machines = MtcMasterMesinModel::where('jenis_mtc', $jenisMtc)
            ->where('aktif', true)
            ->orderBy('kode_mesin')
            ->get(['id', 'nama_mesin', 'kode_mesin', 'lokasi']);

        // Get agenda plans for the type and year
        $plans = MtcAgendaModel::whereHas('mesin', function ($q) use ($jenisMtc) {
            $q->where('jenis_mtc', $jenisMtc);
        })
            ->where('tahun', $tahun)
            ->get(['id', 'mesin_id', 'bulan', 'minggu_ke', 'paket']);

        // Group plans by machine_id -> bulan
        $groupedPlans = [];
        foreach ($plans as $p) {
            $groupedPlans[$p->mesin_id][$p->bulan][] = [
                'minggu_ke' => $p->minggu_ke,
                'paket' => $p->paket
            ];
        }

        return response()->json([
            'status' => true,
            'machines' => $machines,
            'plans' => $groupedPlans
        ]);
    }

    /**
     * Update or delete individual weekly packages for a specific machine-month-year
     */
    public function saveSingle(Request $request)
    {
        $request->validate([
            'mesin_id' => 'required|exists:mtc_master_mesin,id',
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|between:1,12',
            'weeks' => 'array',
        ]);

        $mesinId = $request->mesin_id;
        $tahun = (int) $request->tahun;
        $bulan = (int) $request->bulan;
        $weeks = $request->get('weeks', []); // array of week_num => paket

        DB::beginTransaction();
        try {
            for ($weekNum = 1; $weekNum <= 5; $weekNum++) {
                $paket = isset($weeks[$weekNum]) ? trim($weeks[$weekNum]) : '';

                if (empty($paket) || $paket === 'none') {
                    // Delete if exists
                    MtcAgendaModel::where([
                        'mesin_id' => $mesinId,
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'minggu_ke' => $weekNum
                    ])->delete();
                } else {
                    // Update or create
                    MtcAgendaModel::updateOrCreate(
                        [
                            'mesin_id' => $mesinId,
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'minggu_ke' => $weekNum
                        ],
                        [
                            'paket' => strtoupper($paket),
                            'updated_by' => Auth::id()
                        ]
                    );
                }
            }
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Agenda berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all weekly packages for a specific machine and year
     */
    public function clearMachine(Request $request)
    {
        $request->validate([
            'mesin_id' => 'required|exists:mtc_master_mesin,id',
            'tahun' => 'required|integer',
        ]);

        try {
            MtcAgendaModel::where([
                'mesin_id' => $request->mesin_id,
                'tahun' => $request->tahun
            ])->delete();

            return response()->json([
                'status' => true,
                'message' => 'Seluruh agenda mesin untuk tahun ini berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX: Get agenda dashboard data combining master agenda plans with actual maintenance
     */
    public function getDashboardData(Request $request)
    {
        $jenisMtc = $request->get('jenis_mtc');
        $tahun    = (int) $request->get('tahun', Carbon::today()->year);
        $bulan    = $request->get('bulan', 'all'); // 'all' or 1-12

        // Get active machines for MTC type
        $machines = MtcMasterMesinModel::where('jenis_mtc', $jenisMtc)
            ->where('aktif', true)
            ->orderBy('kode_mesin')
            ->get(['id', 'nama_mesin', 'kode_mesin', 'lokasi']);

        if ($machines->isEmpty()) {
            return response()->json([
                'status'   => true,
                'machines' => [],
                'summary'  => ['total_planned' => 0, 'done' => 0, 'pending' => 0, 'overdue' => 0, 'today' => 0],
            ]);
        }

        // Get all agenda plans for these machines in the selected year
        $agendaQuery = MtcAgendaModel::whereIn('mesin_id', $machines->pluck('id'))
            ->where('tahun', $tahun);

        if ($bulan !== 'all' && is_numeric($bulan)) {
            $agendaQuery->where('bulan', (int)$bulan);
        }

        $agendaPlans = $agendaQuery->get(['mesin_id', 'bulan', 'minggu_ke', 'paket']);

        // Group plans by mesin_id -> bulan -> minggu_ke
        $plansByMachine = [];
        foreach ($agendaPlans as $plan) {
            $plansByMachine[$plan->mesin_id][$plan->bulan][$plan->minggu_ke] = $plan->paket;
        }

        // Get inspection table for this jenis_mtc
        $inspTable = $this->inspectionTables[$jenisMtc] ?? null;

        // Build week-range cache for year (week start/end by month+week_ke)
        // Week 1 = day 1-7, Week 2 = day 8-14, Week 3 = day 15-21, Week 4 = day 22-28, Week 5 = day 29-end
        $weekRanges = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthStart = Carbon::createFromDate($tahun, $m, 1);
            $daysInMonth = $monthStart->daysInMonth;
            $weekRanges[$m] = [
                1 => ['start' => 1,  'end' => 7],
                2 => ['start' => 8,  'end' => 14],
                3 => ['start' => 15, 'end' => 21],
                4 => ['start' => 22, 'end' => 28],
                5 => ['start' => 29, 'end' => $daysInMonth],
            ];
        }

        $today = Carbon::today();
        $summaryTotal   = 0;
        $summaryDone    = 0;
        $summaryPending = 0;
        $summaryOverdue = 0;
        $summaryToday   = 0;

        $result = [];

        foreach ($machines as $mesin) {
            $mesinPlans = $plansByMachine[$mesin->id] ?? [];
            $agendaItems = [];
            $totalPlanned = 0;
            $totalDone    = 0;

            // Get all actual inspections for this machine (from inspection table)
            $actualInspections = [];
            if ($inspTable) {
                $actualInspections = DB::table($inspTable)
                    ->join('mtc_main', 'mtc_main.id', '=', "{$inspTable}.mtc_main_id")
                    ->where("{$inspTable}.mesin_id", $mesin->id)
                    ->whereYear('mtc_main.tanggal', $tahun)
                    ->select('mtc_main.tanggal', 'mtc_main.paket')
                    ->get()
                    ->map(function ($row) {
                        return [
                            'tanggal' => Carbon::parse($row->tanggal),
                            'paket'   => $row->paket,
                        ];
                    })
                    ->toArray();
            }

            $bulanFilter = ($bulan !== 'all' && is_numeric($bulan)) ? [(int)$bulan] : range(1, 12);

            foreach ($bulanFilter as $bln) {
                $weekPlans = $mesinPlans[$bln] ?? [];

                // Filter actual inspections for this month
                $monthActuals = array_filter($actualInspections, function ($act) use ($bln) {
                    return $act['tanggal']->month === $bln;
                });

                // Pair month plans with month actual inspections
                $usedActualKeys = [];
                $planPairing = [];

                // First pass: exact packet match
                for ($wk = 1; $wk <= 5; $wk++) {
                    $paket = $weekPlans[$wk] ?? null;
                    if ($paket === null) continue;

                    foreach ($monthActuals as $key => $act) {
                        if (in_array($key, $usedActualKeys)) continue;

                        if (strcasecmp(trim($act['paket']), trim($paket)) === 0) {
                            $planPairing[$wk] = $act;
                            $usedActualKeys[] = $key;
                            break;
                        }
                    }
                }

                // Second pass: fallback to any unused actual in the same month
                for ($wk = 1; $wk <= 5; $wk++) {
                    $paket = $weekPlans[$wk] ?? null;
                    if ($paket === null) continue;
                    if (isset($planPairing[$wk])) continue;

                    foreach ($monthActuals as $key => $act) {
                        if (in_array($key, $usedActualKeys)) continue;

                        $planPairing[$wk] = $act;
                        $usedActualKeys[] = $key;
                        break;
                    }
                }

                for ($wk = 1; $wk <= 5; $wk++) {
                    $paket = $weekPlans[$wk] ?? null;

                    // 1. Calculate Plan Item
                    $planItem = null;
                    if ($paket !== null) {
                        $totalPlanned++;
                        $summaryTotal++;

                        $wRange   = $weekRanges[$bln][$wk];
                        $wkStart  = Carbon::createFromDate($tahun, $bln, $wRange['start']);
                        $wkEnd    = Carbon::createFromDate($tahun, $bln, $wRange['end']);

                        $pairedActual = $planPairing[$wk] ?? null;
                        if ($pairedActual) {
                            $status = 'done';
                            $totalDone++;
                            $summaryDone++;
                            $doneDate = $pairedActual['tanggal']->format('Y-m-d');
                        } else {
                            $doneDate = null;
                            if ($today->between($wkStart, $wkEnd)) {
                                $status = 'today';
                                $summaryToday++;
                            } elseif ($today->gt($wkEnd)) {
                                $status = 'overdue';
                                $summaryOverdue++;
                            } else {
                                $status = 'pending';
                                $summaryPending++;
                            }
                        }

                        $planItem = [
                            'paket'          => $paket,
                            'status'         => $status,
                            'tanggal_aktual' => $doneDate,
                        ];
                    }

                    // 2. Calculate Actual Item (first inspection in this week range, if any)
                    $actualItem = null;
                    $wRange = $weekRanges[$bln][$wk];
                    $wkStart = Carbon::createFromDate($tahun, $bln, $wRange['start']);
                    $wkEnd = Carbon::createFromDate($tahun, $bln, $wRange['end']);

                    foreach ($monthActuals as $act) {
                        if ($act['tanggal']->between($wkStart, $wkEnd)) {
                            $actualItem = [
                                'tanggal' => $act['tanggal']->format('Y-m-d'),
                                'paket'   => $act['paket'],
                            ];
                            break;
                        }
                    }

                    $agendaItems[] = [
                        'bulan'     => $bln,
                        'minggu_ke' => $wk,
                        'plan'      => $planItem,
                        'actual'    => $actualItem,
                    ];
                }
            }

            $completionRate = $totalPlanned > 0 ? round(($totalDone / $totalPlanned) * 100) : null;

            $result[] = [
                'id'              => $mesin->id,
                'kode_mesin'      => $mesin->kode_mesin,
                'nama_mesin'      => $mesin->nama_mesin,
                'lokasi'          => $mesin->lokasi,
                'agenda'          => $agendaItems,
                'completion_rate' => $completionRate,
                'total_planned'   => $totalPlanned,
                'total_done'      => $totalDone,
            ];
        }

        return response()->json([
            'status'   => true,
            'machines' => $result,
            'summary'  => [
                'total_planned' => $summaryTotal,
                'done'          => $summaryDone,
                'pending'       => $summaryPending,
                'overdue'       => $summaryOverdue,
                'today'         => $summaryToday,
            ],
        ]);
    }

    /**
     * AJAX: Get calendar events for all jenis_mtc combined (no jenis filter)
     * Groups agenda entries as week-range events on a monthly calendar
     */
    public function getDashboardCalendar(Request $request)
    {
        $tahun = (int) $request->get('tahun', Carbon::today()->year);
        $bulan = (int) $request->get('bulan', Carbon::today()->month);

        $today = Carbon::today();

        // Week date ranges for the selected month
        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        $weekRanges = [
            1 => ['start' => 1,  'end' => 7],
            2 => ['start' => 8,  'end' => 14],
            3 => ['start' => 15, 'end' => 21],
            4 => ['start' => 22, 'end' => 28],
            5 => ['start' => 29, 'end' => $daysInMonth],
        ];

        // Get all agenda for this month+year across all machines
        $plans = MtcAgendaModel::with('mesin')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->get();

        // Group plans by machine_id
        $plansByMachine = [];
        foreach ($plans as $plan) {
            if ($plan->mesin) {
                $plansByMachine[$plan->mesin_id][] = $plan;
            }
        }

        $events    = [];  // day => [event, ...]
        $listItems = [];  // flat list for the agenda list panel

        foreach ($plansByMachine as $mesinId => $machinePlans) {
            $firstPlan = $machinePlans[0];
            $mesin = $firstPlan->mesin;
            $jenisMtc = $mesin->jenis_mtc;
            $inspTable = $this->inspectionTables[$jenisMtc] ?? null;

            // Get actual inspections for this machine in this month
            $actualInspections = [];
            if ($inspTable) {
                $actualInspections = DB::table($inspTable)
                    ->join('mtc_main', 'mtc_main.id', '=', "{$inspTable}.mtc_main_id")
                    ->where("{$inspTable}.mesin_id", $mesinId)
                    ->whereYear('mtc_main.tanggal', $tahun)
                    ->whereMonth('mtc_main.tanggal', $bulan)
                    ->select('mtc_main.tanggal', 'mtc_main.paket')
                    ->get()
                    ->map(function ($row) {
                        return [
                            'tanggal' => Carbon::parse($row->tanggal),
                            'paket'   => $row->paket,
                        ];
                    })
                    ->toArray();
            }

            // Pair machine plans with actual inspections
            $usedActualKeys = [];
            $planPairing = [];

            // First pass: exact packet match
            foreach ($machinePlans as $idx => $plan) {
                foreach ($actualInspections as $key => $act) {
                    if (in_array($key, $usedActualKeys)) continue;
                    if (strcasecmp(trim($act['paket']), trim($plan->paket)) === 0) {
                        $planPairing[$idx] = $act;
                        $usedActualKeys[] = $key;
                        break;
                    }
                }
            }

            // Second pass: fallback to any unused actual in the same month
            foreach ($machinePlans as $idx => $plan) {
                if (isset($planPairing[$idx])) continue;
                foreach ($actualInspections as $key => $act) {
                    if (in_array($key, $usedActualKeys)) continue;
                    $planPairing[$idx] = $act;
                    $usedActualKeys[] = $key;
                    break;
                }
            }

            // Construct events & list items
            foreach ($machinePlans as $idx => $plan) {
                $wk      = $plan->minggu_ke;
                $wRange  = $weekRanges[$wk] ?? null;
                if (!$wRange) continue;

                $wkStart = Carbon::createFromDate($tahun, $bulan, $wRange['start']);
                $wkEnd   = Carbon::createFromDate($tahun, $bulan, $wRange['end']);

                $pairedActual = $planPairing[$idx] ?? null;
                if ($pairedActual) {
                    $status = 'done';
                    $doneDate = $pairedActual['tanggal']->format('Y-m-d');
                } else {
                    $doneDate = null;
                    if ($today->between($wkStart, $wkEnd)) {
                        $status = 'today';
                    } elseif ($today->gt($wkEnd)) {
                        $status = 'overdue';
                    } else {
                        $status = 'pending';
                    }
                }

                $event = [
                    'mesin_id'       => $mesin->id,
                    'kode_mesin'     => $mesin->kode_mesin,
                    'nama_mesin'     => $mesin->nama_mesin,
                    'jenis_mtc'      => $jenisMtc,
                    'paket'          => $plan->paket,
                    'minggu_ke'      => $wk,
                    'day_start'      => $wRange['start'],
                    'day_end'        => $wRange['end'],
                    'status'         => $status,
                    'tanggal_aktual' => $doneDate,
                ];

                $events[$wRange['start']][] = $event;
                $listItems[] = $event;
            }
        }

        // Sort list by minggu_ke then nama_mesin
        usort($listItems, fn($a, $b) => $a['minggu_ke'] <=> $b['minggu_ke'] ?: strcmp($a['nama_mesin'], $b['nama_mesin']));

        // Summary counts
        $summary = ['done' => 0, 'today' => 0, 'overdue' => 0, 'pending' => 0, 'total' => count($listItems)];
        foreach ($listItems as $li) {
            if (isset($summary[$li['status']])) $summary[$li['status']]++;
        }

        return response()->json([
            'status'     => true,
            'tahun'      => $tahun,
            'bulan'      => $bulan,
            'days'       => $daysInMonth,
            'events'     => $events,
            'list'       => $listItems,
            'summary'    => $summary,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────



    /**
     * Ambil tanggal maintenance terakhir dari inspection table terkait
     */
    private function getLastMaintenanceDate(int $mesinId, string $jenisMtc): ?Carbon
    {
        $table = $this->inspectionTables[$jenisMtc] ?? null;
        if (!$table) return null;

        // Subquery: mtc_main_id yang semua approval-nya sudah approved
        $approvedMainIds = DB::table('mtc_approval')
            ->select('mtc_main_id')
            ->groupBy('mtc_main_id')
            ->havingRaw('SUM(CASE WHEN status != ? THEN 1 ELSE 0 END) = 0', ['approved']);

        $row = DB::table($table)
            ->join('mtc_main', 'mtc_main.id', '=', "{$table}.mtc_main_id")
            ->joinSub($approvedMainIds, 'approved_main', function ($join) use ($table) {
                $join->on('approved_main.mtc_main_id', '=', "{$table}.mtc_main_id");
            })
            ->where("{$table}.mesin_id", $mesinId)
            ->orderByDesc('mtc_main.tanggal')
            ->select('mtc_main.tanggal')
            ->first();

        // Fallback: ambil semua tanpa filter approval
        if (!$row) {
            $row = DB::table($table)
                ->join('mtc_main', 'mtc_main.id', '=', "{$table}.mtc_main_id")
                ->where("{$table}.mesin_id", $mesinId)
                ->orderByDesc('mtc_main.tanggal')
                ->select('mtc_main.tanggal')
                ->first();
        }

        return $row ? Carbon::parse($row->tanggal) : null;
    }

    /**
     * Hitung jadwal berikutnya dari tanggal terakhir + interval frekuensi
     * Jika belum pernah maintenance, gunakan hari ini sebagai basis
     */
    private function calculateNextDue(?Carbon $lastDate, int $interval, string $satuan): Carbon
    {
        $base = $lastDate ?? Carbon::today();

        return match (strtolower($satuan)) {
            'hari'   => $base->copy()->addDays($interval),
            'minggu' => $base->copy()->addWeeks($interval),
            'bulan'  => $base->copy()->addMonths($interval),
            'tahun'  => $base->copy()->addYears($interval),
            default  => $base->copy()->addMonths($interval),
        };
    }

    /**
     * Tentukan status berdasarkan sisa hari
     * overdue   = sudah lewat
     * critical  = ≤ 3 hari
     * upcoming  = 4–14 hari
     * scheduled = > 14 hari
     * no_record = belum pernah maintenance
     */
    private function resolveStatus(int $daysLeft, ?Carbon $lastDate): string
    {
        if ($lastDate === null) return 'no_record';
        if ($daysLeft < 0)     return 'overdue';
        if ($daysLeft <= 3)    return 'critical';
        if ($daysLeft <= 14)   return 'upcoming';
        return 'scheduled';
    }

    /**
     * Hitung ringkasan jumlah per status
     */
    private function buildSummary($agendaData): array
    {
        $summary = [
            'overdue'    => 0,
            'critical'   => 0,
            'upcoming'   => 0,
            'scheduled'  => 0,
            'no_record'  => 0,
            'no_schedule' => 0,
        ];

        foreach ($agendaData as $item) {
            foreach ($item['schedules'] as $sch) {
                $key = $sch['status'];
                if (isset($summary[$key])) {
                    $summary[$key]++;
                }
            }
        }

        return $summary;
    }

    /**
     * Siapkan event kalender untuk bulan tertentu
     */
    private function buildCalendarEvents($agendaData, string $calendarMonth): array
    {
        $events = [];
        $monthStart = Carbon::parse($calendarMonth . '-01')->startOfMonth();
        $monthEnd   = $monthStart->copy()->endOfMonth();

        foreach ($agendaData as $item) {
            foreach ($item['schedules'] as $sch) {
                if (!$sch['next_due']) continue;

                $due = $sch['next_due'];
                if ($due->between($monthStart, $monthEnd)) {
                    $events[$due->day][] = [
                        'kode'   => $item['mesin']->kode_mesin,
                        'nama'   => $item['mesin']->nama_mesin,
                        'frek'   => $sch['label'],
                        'status' => $sch['status'],
                    ];
                }
            }
        }

        return $events;
    }

    /**
     * Upload & import Excel Agenda
     */
    public function upload(MtcAgendaUploadRequest $request)
    {
        $file = $request->file('file_excel');
        $tahun = (int) $request->tahun;
        $jenisMtc = $request->jenis_mtc;

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $headerRowIdx = null;
        // Indon/English month search names
        $monthNames = [
            1 => ['jan', 'januari', 'january'],
            2 => ['feb', 'februari', 'february'],
            3 => ['mar', 'maret', 'march'],
            4 => ['apr', 'april'],
            5 => ['mei', 'may'],
            6 => ['jun', 'juni', 'june'],
            7 => ['jul', 'juli', 'july'],
            8 => ['agt', 'agustus', 'august', 'agu'],
            9 => ['sep', 'september'],
            10 => ['okt', 'oktober', 'october', 'okt'],
            11 => ['nov', 'november'],
            12 => ['des', 'desember', 'december', 'dec']
        ];

        // Scan rows 1-5 to detect headers and column letters
        for ($r = 1; $r <= 5; $r++) {
            if (!isset($rows[$r])) continue;
            $row = $rows[$r];

            $foundMonthsCount = 0;
            foreach ($row as $colLetter => $cellVal) {
                if (empty($cellVal)) continue;
                $valLower = strtolower(trim((string)$cellVal));

                // Check if this cell matches any month
                foreach ($monthNames as $mNum => $mAliases) {
                    if (in_array($valLower, $mAliases, true)) {
                        $foundMonthsCount++;
                        break;
                    }
                }
            }

            if ($foundMonthsCount >= 3) {
                $headerRowIdx = $r;
                break;
            }
        }

        if ($headerRowIdx === null) {
            $headerRowIdx = 2;
        }

        // Set explicit mappings based on Jenis MTC
        $colMap = [
            'nama_mesin' => 'A',
            'kode_mesin' => 'B',
            'months' => []
        ];

        $jenisMtcLower = strtolower($jenisMtc);
        if (str_contains($jenisMtcLower, 'refrigerasi')) {
            $colMap['nama_mesin'] = 'A';
            $colMap['kode_mesin'] = 'B';
            $startColIdx = 6; // Column F
        } elseif (str_contains($jenisMtcLower, 'electrical') || str_contains($jenisMtcLower, 'listrik')) {
            $colMap['nama_mesin'] = 'A';
            $colMap['kode_mesin'] = null; // No Kode Mesin
            $startColIdx = 4; // Column D
        } else {
            // Default: Utility, Motor Pompa, dll.
            $colMap['nama_mesin'] = 'A';
            $colMap['kode_mesin'] = 'B';
            $startColIdx = 5; // Column E
        }

        for ($m = 1; $m <= 12; $m++) {
            $weekColIdx = $startColIdx + ($m - 1) * 2;
            $paketColIdx = $weekColIdx + 1;

            $colMap['months'][$m] = [
                'week_col'  => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($weekColIdx),
                'paket_col' => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($paketColIdx),
            ];
        }

        $dataStart = $headerRowIdx + 2;
        $inserted = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            $mesinIds = MtcMasterMesinModel::where('jenis_mtc', $jenisMtc)
                ->pluck('id')
                ->toArray();

            if (!empty($mesinIds)) {
                MtcAgendaModel::whereIn('mesin_id', $mesinIds)
                    ->where('tahun', $tahun)
                    ->delete();
            }

            $userId = Auth::id();
            $rowErrors = [];

            foreach ($rows as $rowNum => $row) {
                if ($rowNum < $dataStart) continue;

                $namaMesinVal = trim((string)($row[$colMap['nama_mesin']] ?? ''));
                $kodeMesinVal = (!empty($colMap['kode_mesin']) && isset($row[$colMap['kode_mesin']])) ? trim((string)$row[$colMap['kode_mesin']]) : '';

                if ($namaMesinVal === '' && $kodeMesinVal === '') {
                    continue;
                }

                $mesin = null;
                // Prioritas utama: kode mesin (nama tidak perlu cocok)
                // Jika kode mesin ada di Excel → cari berdasarkan kode saja
                if ($kodeMesinVal !== '') {
                    $mesin = MtcMasterMesinModel::where('kode_mesin', $kodeMesinVal)
                        ->where('jenis_mtc', $jenisMtc)
                        ->first();
                }

                // Fallback: jika kode mesin tidak ada di Excel → cari berdasarkan nama saja
                if (!$mesin && $kodeMesinVal === '' && $namaMesinVal !== '') {
                    $mesin = MtcMasterMesinModel::where('nama_mesin', $namaMesinVal)
                        ->where('jenis_mtc', $jenisMtc)
                        ->first();
                }

                if (!$mesin) {
                    $rowErrors[] = "Baris {$rowNum}: Mesin dengan Kode '{$kodeMesinVal}' atau Nama '{$namaMesinVal}' tidak ditemukan di Master Mesin dengan Jenis MTC '{$jenisMtc}'.";
                    continue;
                }

                $hasSchedule = false;
                foreach ($colMap['months'] as $mNum => $cfg) {
                    $weekCol = $cfg['week_col'];
                    $paketCol = $cfg['paket_col'];

                    $weeksRaw = $row[$weekCol] ?? '';
                    $packagesRaw = $row[$paketCol] ?? '';

                    $parsedList = $this->parseWeeksAndPackages($weeksRaw, $packagesRaw);

                    foreach ($parsedList as $parsed) {
                        try {
                            MtcAgendaModel::create([
                                'mesin_id' => $mesin->id,
                                'tahun' => $tahun,
                                'bulan' => $mNum,
                                'minggu_ke' => $parsed['minggu_ke'],
                                'paket' => $parsed['paket'],
                                'created_by' => $userId,
                            ]);
                            $hasSchedule = true;
                        } catch (\Illuminate\Database\QueryException $ex) {
                            if ($ex->getCode() == 23000) {
                                $rowErrors[] = "Baris {$rowNum}: Jadwal ganda terdeteksi untuk Mesin '{$mesin->nama_mesin}' pada Bulan {$mNum} Minggu {$parsed['minggu_ke']}.";
                            } else {
                                $rowErrors[] = "Baris {$rowNum}: Database error pada Bulan {$mNum} Minggu {$parsed['minggu_ke']} - " . $ex->getMessage();
                            }
                        }
                    }
                }

                if ($hasSchedule) {
                    $inserted++;
                } else {
                    $rowErrors[] = "Baris {$rowNum}: Mesin '{$mesin->nama_mesin}' tidak memiliki data jadwal yang valid.";
                }
            }

            if (!empty($rowErrors)) {
                throw new \Exception(implode("<br>", $rowErrors));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => "Berhasil mengimport agenda untuk {$inserted} mesin.",
            'inserted' => $inserted,
        ]);
    }

    /**
     * Helper parser to parse weeks and packages from Excel cell values.
     */
    private function parseWeeksAndPackages($weeksRaw, $packagesRaw): array
    {
        if (empty($weeksRaw) || trim((string)$weeksRaw) === '') {
            return [];
        }

        $cleanWeeks = str_replace([';', '/', '|', ' '], ',', (string)$weeksRaw);
        $weeks = array_filter(explode(',', $cleanWeeks), 'strlen');

        $cleanPackages = str_replace([';', '/', '|', ' '], ',', (string)$packagesRaw);
        $packages = array_filter(explode(',', $cleanPackages), 'strlen');

        $weeks = array_values($weeks);
        $packages = array_values($packages);

        $results = [];
        $numWeeks = count($weeks);
        $numPackages = count($packages);

        if ($numWeeks > 0) {
            for ($i = 0; $i < $numWeeks; $i++) {
                $weekNum = intval(trim($weeks[$i]));
                if ($weekNum < 1 || $weekNum > 5) continue;

                $pkg = '';
                if ($numPackages > 0) {
                    if (isset($packages[$i])) {
                        $pkg = strtoupper(trim($packages[$i]));
                    } else {
                        $pkg = strtoupper(trim($packages[$numPackages - 1]));
                    }
                }

                if ($pkg !== '') {
                    $results[] = [
                        'minggu_ke' => $weekNum,
                        'paket' => $pkg
                    ];
                }
            }
        }

        return $results;
    }
}
