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
            ->get(['id', 'mesin_id', 'bulan', 'minggu_ke', 'paket', 'tanggal']);

        // Group plans by machine_id -> bulan
        $groupedPlans = [];
        foreach ($plans as $p) {
            $groupedPlans[$p->mesin_id][$p->bulan][] = [
                'minggu_ke' => $p->minggu_ke,
                'paket' => $p->paket,
                'tanggal' => $p->tanggal ? $p->tanggal->format('Y-m-d') : null
            ];
        }

        return response()->json([
            'status' => true,
            'machines' => $machines,
            'plans' => $groupedPlans
        ]);
    }

    /**
     * Update or delete individual weekly/date packages for specific machine(s)-month-year
     */
    public function saveSingle(Request $request)
    {
        $request->validate([
            'mesin_id'      => 'nullable',
            'mesin_ids'     => 'nullable|array',
            'tahun'         => 'required|integer',
            'bulan'         => 'required|integer|between:1,12',
            'weeks'         => 'nullable|array',
            'dates'         => 'nullable|array',
            'date_packages' => 'nullable|array',
        ]);

        $tahun = (int) $request->tahun;
        $bulan = (int) $request->bulan;

        // Collect machine IDs (support both array mesin_ids and single mesin_id)
        $mesinIds = $request->input('mesin_ids', []);
        if (empty($mesinIds) && $request->filled('mesin_id')) {
            $mesinIds = [$request->input('mesin_id')];
        }

        if (empty($mesinIds)) {
            return response()->json([
                'status'  => false,
                'message' => 'Pilih minimal satu mesin.'
            ], 422);
        }

        $userId = Auth::id();
        $updatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($mesinIds as $mId) {
                $mesin = MtcMasterMesinModel::find($mId);
                if (!$mesin) continue;

                $isDateBased = in_array($mesin->jenis_mtc, ['Electric Engine', 'Diesel Engine']);

                if ($isDateBased) {
                    // Delete existing records for this machine, year, month
                    MtcAgendaModel::where([
                        'mesin_id' => $mId,
                        'tahun'    => $tahun,
                        'bulan'    => $bulan
                    ])->delete();

                    $dates = $request->get('dates', []);
                    $packages = $request->get('date_packages', []);

                    foreach ($dates as $idx => $dateStr) {
                        if (empty($dateStr)) continue;
                        $paket = isset($packages[$idx]) ? trim($packages[$idx]) : '';
                        if (empty($paket) || $paket === 'none') continue;

                        MtcAgendaModel::create([
                            'mesin_id'   => $mId,
                            'tahun'      => $tahun,
                            'bulan'      => $bulan,
                            'minggu_ke'  => null, // NULL for date-based
                            'tanggal'    => $dateStr,
                            'paket'      => strtoupper($paket),
                            'created_by' => $userId,
                            'updated_by' => $userId,
                        ]);
                    }
                } else {
                    $weeks = $request->get('weeks', []); // array of week_num => paket
                    for ($weekNum = 1; $weekNum <= 5; $weekNum++) {
                        $paket = isset($weeks[$weekNum]) ? trim($weeks[$weekNum]) : '';

                        if (empty($paket) || $paket === 'none') {
                            MtcAgendaModel::where([
                                'mesin_id'  => $mId,
                                'tahun'     => $tahun,
                                'bulan'     => $bulan,
                                'minggu_ke' => $weekNum
                            ])->delete();
                        } else {
                            MtcAgendaModel::updateOrCreate(
                                [
                                    'mesin_id'  => $mId,
                                    'tahun'     => $tahun,
                                    'bulan'     => $bulan,
                                    'minggu_ke' => $weekNum
                                ],
                                [
                                    'paket'      => strtoupper($paket),
                                    'tanggal'    => null, // NULL for week-based
                                    'updated_by' => $userId
                                ]
                            );
                        }
                    }
                }
                $updatedCount++;
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => $updatedCount > 1 
                    ? "Agenda berhasil disimpan untuk {$updatedCount} mesin."
                    : 'Agenda berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status'  => false,
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

        $agendaPlans = $agendaQuery->get(['mesin_id', 'bulan', 'minggu_ke', 'paket', 'tanggal']);

        // Group plans by mesin_id -> bulan -> minggu_ke (or computed week if date-based)
        $plansByMachine = [];
        foreach ($agendaPlans as $plan) {
            if ($plan->tanggal) {
                // Compute minggu_ke dynamically from date day
                $day = Carbon::parse($plan->tanggal)->day;
                $computedWk = ($day <= 7) ? 1 : (($day <= 14) ? 2 : (($day <= 21) ? 3 : (($day <= 28) ? 4 : 5)));
                $plansByMachine[$plan->mesin_id][$plan->bulan][$computedWk][] = [
                    'paket' => $plan->paket,
                    'tanggal' => $plan->tanggal->format('Y-m-d')
                ];
            } else {
                $plansByMachine[$plan->mesin_id][$plan->bulan][$plan->minggu_ke][] = [
                    'paket' => $plan->paket,
                    'tanggal' => null
                ];
            }
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
                $monthPlans = $mesinPlans[$bln] ?? [];

                // Filter actual inspections for this month
                $monthActuals = array_filter($actualInspections, function ($act) use ($bln) {
                    return $act['tanggal']->month === $bln;
                });

                // Pair month plans with month actual inspections
                $usedActualKeys = [];
                $planPairing = [];

                // First pass: exact packet match
                for ($wk = 1; $wk <= 5; $wk++) {
                    $weekPlansList = $monthPlans[$wk] ?? [];
                    foreach ($weekPlansList as $pIdx => $planData) {
                        $paket = $planData['paket'];

                        foreach ($monthActuals as $key => $act) {
                            if (in_array($key, $usedActualKeys)) continue;

                            if (strcasecmp(trim($act['paket']), trim($paket)) === 0) {
                                $planPairing["{$wk}_{$pIdx}"] = $act;
                                $usedActualKeys[] = $key;
                                break;
                            }
                        }
                    }
                }

                // Second pass: fallback to any unused actual in the same month
                for ($wk = 1; $wk <= 5; $wk++) {
                    $weekPlansList = $monthPlans[$wk] ?? [];
                    foreach ($weekPlansList as $pIdx => $planData) {
                        if (isset($planPairing["{$wk}_{$pIdx}"])) continue;

                        foreach ($monthActuals as $key => $act) {
                            if (in_array($key, $usedActualKeys)) continue;

                            $planPairing["{$wk}_{$pIdx}"] = $act;
                            $usedActualKeys[] = $key;
                            break;
                        }
                    }
                }

                for ($wk = 1; $wk <= 5; $wk++) {
                    $weekPlansList = $monthPlans[$wk] ?? [];
                    $planItemsList = [];

                    $wRange   = $weekRanges[$bln][$wk];
                    $wkStart  = Carbon::createFromDate($tahun, $bln, $wRange['start']);
                    $wkEnd    = Carbon::createFromDate($tahun, $bln, $wRange['end']);

                    foreach ($weekPlansList as $pIdx => $planData) {
                        $paket = $planData['paket'];
                        $planTanggal = $planData['tanggal'];
                        $totalPlanned++;
                        $summaryTotal++;

                        $pairedActual = $planPairing["{$wk}_{$pIdx}"] ?? null;
                        if ($pairedActual) {
                            $status = 'done';
                            $totalDone++;
                            $summaryDone++;
                            $doneDate = $pairedActual['tanggal']->format('Y-m-d');
                        } else {
                            $doneDate = null;
                            if ($planTanggal) {
                                $planDateObj = Carbon::parse($planTanggal);
                                if ($today->format('Y-m-d') === $planDateObj->format('Y-m-d')) {
                                    $status = 'today';
                                    $summaryToday++;
                                } elseif ($today->gt($planDateObj)) {
                                    $status = 'overdue';
                                    $summaryOverdue++;
                                } else {
                                    $status = 'pending';
                                    $summaryPending++;
                                }
                            } else {
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
                        }

                        $planItemsList[] = [
                            'paket'          => $paket,
                            'status'         => $status,
                            'tanggal_aktual' => $doneDate,
                            'tanggal'        => $planTanggal,
                        ];
                    }

                    // 2. Calculate Actual Items (all inspections in this week range, if any)
                    $actualItemsList = [];
                    foreach ($monthActuals as $act) {
                        if ($act['tanggal']->between($wkStart, $wkEnd)) {
                            $actualItemsList[] = [
                                'tanggal' => $act['tanggal']->format('Y-m-d'),
                                'paket'   => $act['paket'],
                            ];
                        }
                    }

                    $agendaItems[] = [
                        'bulan'     => $bln,
                        'minggu_ke' => $wk,
                        'plans'     => $planItemsList,
                        'plan'      => count($planItemsList) > 0 ? $planItemsList[0] : null,
                        'actuals'   => $actualItemsList,
                        'actual'    => count($actualItemsList) > 0 ? $actualItemsList[0] : null,
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
                if ($plan->tanggal) {
                    $dateObj = Carbon::parse($plan->tanggal);
                    $day = $dateObj->day;

                    $pairedActual = $planPairing[$idx] ?? null;
                    if ($pairedActual) {
                        $status = 'done';
                        $doneDate = $pairedActual['tanggal']->format('Y-m-d');
                    } else {
                        $doneDate = null;
                        if ($today->format('Y-m-d') === $dateObj->format('Y-m-d')) {
                            $status = 'today';
                        } elseif ($today->gt($dateObj)) {
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
                        'minggu_ke'      => $dateObj->weekOfMonth,
                        'day_start'      => $day,
                        'day_end'        => $day,
                        'status'         => $status,
                        'tanggal_aktual' => $doneDate,
                    ];

                    $events[$day][] = $event;
                    $listItems[] = $event;
                } else {
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
     * Download Excel template for Master Agenda
     * Supports:
     * - 'mhe' (Multi-sheet: Electric Engine, Diesel Engine; columns: No, Kode Mesin, Nama Mesin, Lokasi, Tanggal, Paket)
     * - 'non_mhe' (Multi-sheet: Electrical, Motor Pompa, Refrigerasi, Sipil, Utility, etc.; columns: No, Kode Mesin, Nama Mesin, Lokasi, 12 Bulan x (Minggu, Paket))
     */
    public function downloadTemplate(Request $request)
    {
        $type = $request->get('type', 'non_mhe'); // 'mhe' or 'non_mhe'
        $tahun = (int) $request->get('tahun', Carbon::today()->year);
        $bulan = (int) $request->get('bulan', Carbon::today()->month);

        $monthNamesId = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '94A3B8']
                ]
            ]
        ];

        $subHeaderStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A'], 'size' => 9],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1']
                ]
            ]
        ];

        $dataBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0']
                ]
            ]
        ];

        if ($type === 'mhe') {
            $mheTypes = ['Electric Engine', 'Diesel Engine'];
            $sheetIndex = 0;

            foreach ($mheTypes as $mheJenis) {
                if ($sheetIndex === 0) {
                    $sheet = $spreadsheet->getActiveSheet();
                } else {
                    $sheet = $spreadsheet->createSheet();
                }
                $sheet->setTitle($mheJenis);

                // Title Banner
                $sheet->setCellValue('A1', 'TEMPLATE MASTER AGENDA MHE - ' . strtoupper($mheJenis) . ' (HARIAN / PER TANGGAL)');
                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);

                $bulanNama = $monthNamesId[$bulan] ?? "Bulan {$bulan}";
                $sheet->setCellValue('A2', "Periode: {$bulanNama} {$tahun} | Petunjuk: Masukkan tanggal (1-31, pisah koma jika > 1, misal: 5, 12, 19, 26) dan kode paket (misal: A, B, A, B).");
                $sheet->mergeCells('A2:F2');
                $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(9);

                // Headers
                $sheet->setCellValue('A4', 'No');
                $sheet->setCellValue('B4', 'Kode Mesin');
                $sheet->setCellValue('C4', 'Nama Mesin');
                $sheet->setCellValue('D4', 'Lokasi');
                $sheet->setCellValue('E4', "Tanggal ({$bulanNama})");
                $sheet->setCellValue('F4', 'Paket');

                $sheet->getStyle('A4:F4')->applyFromArray($headerStyle);
                $sheet->getRowDimension(4)->setRowHeight(26);

                // Fetch active machines
                $machines = MtcMasterMesinModel::where('jenis_mtc', $mheJenis)
                    ->where('aktif', true)
                    ->orderBy('kode_mesin')
                    ->get();

                $rowIdx = 5;
                if ($machines->isNotEmpty()) {
                    foreach ($machines as $i => $m) {
                        $sheet->setCellValue("A{$rowIdx}", $i + 1);
                        $sheet->setCellValue("B{$rowIdx}", $m->kode_mesin);
                        $sheet->setCellValue("C{$rowIdx}", $m->nama_mesin);
                        $sheet->setCellValue("D{$rowIdx}", $m->lokasi);
                        if ($i === 0) {
                            $sheet->setCellValue("E{$rowIdx}", '5, 19');
                            $sheet->setCellValue("F{$rowIdx}", 'A, B');
                        }
                        $sheet->getStyle("A{$rowIdx}:F{$rowIdx}")->applyFromArray($dataBorder);
                        $sheet->getStyle("A{$rowIdx}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $rowIdx++;
                    }
                } else {
                    $sheet->setCellValue('A5', '1');
                    $sheet->setCellValue('B5', 'EE-01');
                    $sheet->setCellValue('C5', "Contoh {$mheJenis} 01");
                    $sheet->setCellValue('D5', 'Warehouse');
                    $sheet->setCellValue('E5', '5, 19');
                    $sheet->setCellValue('F5', 'A, B');
                    $sheet->getStyle('A5:F5')->applyFromArray($dataBorder);
                }

                foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheetIndex++;
            }

            $filename = "Template_Agenda_MHE_{$bulan}_{$tahun}.xlsx";
        } else {
            // Non-MHE Types
            $nonMheTypes = MtcMasterMesinModel::whereNotIn('jenis_mtc', ['Electric Engine', 'Diesel Engine'])
                ->where('aktif', true)
                ->distinct()
                ->orderBy('jenis_mtc')
                ->pluck('jenis_mtc')
                ->toArray();

            if (empty($nonMheTypes)) {
                $nonMheTypes = ['Electrical', 'Motor Pompa', 'Refrigerasi', 'Sipil', 'Utility'];
            }

            $sheetIndex = 0;
            foreach ($nonMheTypes as $jenis) {
                if ($sheetIndex === 0) {
                    $sheet = $spreadsheet->getActiveSheet();
                } else {
                    $sheet = $spreadsheet->createSheet();
                }
                $sheet->setTitle(substr($jenis, 0, 31));

                // Title
                $sheet->setCellValue('A1', 'TEMPLATE MASTER AGENDA - ' . strtoupper($jenis) . ' (MINGGUAN / TAHUNAN)');
                $sheet->mergeCells('A1:AB1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);

                $sheet->setCellValue('A2', "Tahun: {$tahun} | Petunjuk: Masukkan nomor minggu (1-5, pisah koma jika > 1, contoh: 1, 3) dan kode paket (contoh: A, B).");
                $sheet->mergeCells('A2:AB2');
                $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(9);

                // Row 4: Main headers
                $sheet->setCellValue('A4', 'No');
                $sheet->mergeCells('A4:A5');
                $sheet->setCellValue('B4', 'Kode Mesin');
                $sheet->mergeCells('B4:B5');
                $sheet->setCellValue('C4', 'Nama Mesin');
                $sheet->mergeCells('C4:C5');
                $sheet->setCellValue('D4', 'Lokasi');
                $sheet->mergeCells('D4:D5');

                $sheet->getStyle('A4:D5')->applyFromArray($headerStyle);

                // Months 1 to 12
                $colIdx = 5; // Column E
                for ($m = 1; $m <= 12; $m++) {
                    $col1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                    $col2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);

                    $monthName = strtoupper($monthNamesId[$m] ?? "BLN {$m}");
                    $sheet->setCellValue("{$col1}4", $monthName);
                    $sheet->mergeCells("{$col1}4:{$col2}4");
                    $sheet->getStyle("{$col1}4:{$col2}4")->applyFromArray($headerStyle);

                    $sheet->setCellValue("{$col1}5", 'Minggu');
                    $sheet->setCellValue("{$col2}5", 'Paket');
                    $sheet->getStyle("{$col1}5:{$col2}5")->applyFromArray($subHeaderStyle);

                    $colIdx += 2;
                }

                $sheet->getRowDimension(4)->setRowHeight(22);
                $sheet->getRowDimension(5)->setRowHeight(20);

                // Machines for this jenis
                $machines = MtcMasterMesinModel::where('jenis_mtc', $jenis)
                    ->where('aktif', true)
                    ->orderBy('kode_mesin')
                    ->get();

                $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx - 1);
                $rowIdx = 6;
                if ($machines->isNotEmpty()) {
                    foreach ($machines as $i => $m) {
                        $sheet->setCellValue("A{$rowIdx}", $i + 1);
                        $sheet->setCellValue("B{$rowIdx}", $m->kode_mesin);
                        $sheet->setCellValue("C{$rowIdx}", $m->nama_mesin);
                        $sheet->setCellValue("D{$rowIdx}", $m->lokasi);

                        if ($i === 0) {
                            $sheet->setCellValue("E{$rowIdx}", '1, 3');
                            $sheet->setCellValue("F{$rowIdx}", 'A, B');
                        }

                        $sheet->getStyle("A{$rowIdx}:{$lastColLetter}{$rowIdx}")->applyFromArray($dataBorder);
                        $sheet->getStyle("A{$rowIdx}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $rowIdx++;
                    }
                } else {
                    $sheet->setCellValue('A6', '1');
                    $sheet->setCellValue('B6', 'MC-01');
                    $sheet->setCellValue('C6', "Contoh {$jenis} 01");
                    $sheet->setCellValue('D6', 'Area Produksi');
                    $sheet->setCellValue('E6', '1');
                    $sheet->setCellValue('F6', 'A');
                    $sheet->getStyle("A6:{$lastColLetter}6")->applyFromArray($dataBorder);
                }

                for ($c = 1; $c < $colIdx; $c++) {
                    $cLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                    $sheet->getColumnDimension($cLetter)->setAutoSize(true);
                }

                $sheetIndex++;
            }

            $filename = "Template_Agenda_Non_MHE_{$tahun}.xlsx";
        }

        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Upload & import Excel Agenda with Multi-Sheet support for MHE and Non-MHE
     */
    public function upload(MtcAgendaUploadRequest $request)
    {
        $file = $request->file('file_excel');
        $tahun = (int) $request->tahun;
        $kategori = $request->input('kategori'); // 'mhe' or 'non_mhe'
        $bulan = $request->filled('bulan') ? (int) $request->bulan : null;
        $userId = Auth::id();

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $allSheets = $spreadsheet->getAllSheets();

        // Get all distinct jenis_mtc in system for sheet matching
        $registeredJenisList = MtcMasterMesinModel::distinct()->pluck('jenis_mtc')->toArray();

        $processedSheets = [];
        $totalMachinesUpdated = 0;
        $allRowErrors = [];
        $skippedSheets = [];

        DB::beginTransaction();
        try {
            foreach ($allSheets as $sheet) {
                $sheetTitle = trim($sheet->getTitle());

                // Find matching jenis_mtc from registered master mesin
                $matchedJenis = null;
                foreach ($registeredJenisList as $regJenis) {
                    if (strcasecmp(trim($regJenis), $sheetTitle) === 0) {
                        $matchedJenis = $regJenis;
                        break;
                    }
                }

                // If not exact match, try matching known aliases
                if (!$matchedJenis) {
                    $sheetTitleLower = strtolower($sheetTitle);
                    foreach ($registeredJenisList as $regJenis) {
                        $regLower = strtolower($regJenis);
                        if (str_contains($sheetTitleLower, $regLower) || str_contains($regLower, $sheetTitleLower)) {
                            $matchedJenis = $regJenis;
                            break;
                        }
                    }
                }

                // If still not matched, check if sheet is a special MHE sheet name
                if (!$matchedJenis && $kategori === 'mhe') {
                    if (str_contains(strtolower($sheetTitle), 'electric')) {
                        $matchedJenis = 'Electric Engine';
                    } elseif (str_contains(strtolower($sheetTitle), 'diesel')) {
                        $matchedJenis = 'Diesel Engine';
                    }
                }

                if (!$matchedJenis) {
                    $skippedSheets[] = "Sheet '{$sheetTitle}' dilewati (tidak dikenali sebagai Jenis MTC).";
                    continue;
                }

                $isMheJenis = in_array(strtolower($matchedJenis), ['electric engine', 'diesel engine']);

                // Filter based on selected kategori
                if ($kategori === 'mhe' && !$isMheJenis) {
                    $skippedSheets[] = "Sheet '{$sheetTitle}' dilewati karena bukan kategori MHE.";
                    continue;
                }
                if ($kategori === 'non_mhe' && $isMheJenis) {
                    $skippedSheets[] = "Sheet '{$sheetTitle}' dilewati karena berformat MHE (per tanggal).";
                    continue;
                }

                $rows = $sheet->toArray(null, true, true, true);
                if (empty($rows)) {
                    continue;
                }

                // Clear old data for machines of this jenis_mtc
                $mesinIds = MtcMasterMesinModel::where('jenis_mtc', $matchedJenis)->pluck('id')->toArray();
                if (!empty($mesinIds)) {
                    if ($isMheJenis && $bulan) {
                        MtcAgendaModel::whereIn('mesin_id', $mesinIds)
                            ->where('tahun', $tahun)
                            ->where('bulan', $bulan)
                            ->delete();
                    } else {
                        MtcAgendaModel::whereIn('mesin_id', $mesinIds)
                            ->where('tahun', $tahun)
                            ->delete();
                    }
                }

                $sheetInserted = 0;

                if ($isMheJenis) {
                    // ── MHE Date-Based Sheet Processing ──
                    $mheResult = $this->processMheSheet($rows, $matchedJenis, $tahun, $bulan ?? 1, $userId, $sheetTitle);
                    $sheetInserted = $mheResult['inserted'];
                    if (!empty($mheResult['errors'])) {
                        $allRowErrors = array_merge($allRowErrors, $mheResult['errors']);
                    }
                } else {
                    // ── Non-MHE Week-Based Sheet Processing ──
                    $nonMheResult = $this->processNonMheSheet($rows, $matchedJenis, $tahun, $userId, $sheetTitle);
                    $sheetInserted = $nonMheResult['inserted'];
                    if (!empty($nonMheResult['errors'])) {
                        $allRowErrors = array_merge($allRowErrors, $nonMheResult['errors']);
                    }
                }

                if ($sheetInserted > 0) {
                    $processedSheets[$matchedJenis] = $sheetInserted;
                    $totalMachinesUpdated += $sheetInserted;
                }
            }

            if (!empty($allRowErrors)) {
                throw new \Exception(implode("<br>", $allRowErrors));
            }

            if (empty($processedSheets)) {
                $skipMsg = !empty($skippedSheets) ? '<br>' . implode('<br>', $skippedSheets) : '';
                throw new \Exception("Tidak ada sheet yang cocok atau berisi data valid untuk diimport pada kategori " . strtoupper($kategori) . ". Pastikan nama sheet sesuai dengan Jenis MTC." . $skipMsg);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }

        $summaryText = implode(', ', array_map(function ($k, $v) {
            return "{$k}: {$v} mesin";
        }, array_keys($processedSheets), $processedSheets));

        return response()->json([
            'status' => true,
            'message' => "Berhasil mengimport Master Agenda untuk kategori " . strtoupper($kategori) . " ({$summaryText}).",
            'total_inserted' => $totalMachinesUpdated,
            'processed_sheets' => $processedSheets,
            'warnings' => $skippedSheets
        ]);
    }

    /**
     * Helper to process MHE date-based sheet (per tanggal & paket for a specific month)
     */
    private function processMheSheet(array $rows, string $jenisMtc, int $tahun, int $bulan, $userId, string $sheetTitle): array
    {
        $inserted = 0;
        $rowErrors = [];

        // Template MHE: Baris 1-4 adalah Header/Title, Data selalu mulai dari Baris 5
        // Kolom: A=No, B=Kode Mesin, C=Nama Mesin, D=Lokasi, E=Tanggal, F=Paket
        $dataStart = 5;
        $ignoreHeaders = ['kode mesin', 'kode', 'code', 'nama mesin', 'nama', 'mesin', 'tanggal', 'tgl', 'date', 'paket', 'package', 'lokasi', 'no', 'nomor'];

        foreach ($rows as $rowNum => $row) {
            if ($rowNum < $dataStart) continue;

            $namaMesinVal = trim((string)($row['C'] ?? ''));
            $kodeMesinVal = trim((string)($row['B'] ?? ''));

            if ($namaMesinVal === '' && $kodeMesinVal === '') {
                continue;
            }

            if (in_array(strtolower($namaMesinVal), $ignoreHeaders, true) || in_array(strtolower($kodeMesinVal), $ignoreHeaders, true)) {
                continue;
            }

            $mesin = null;
            if ($kodeMesinVal !== '') {
                $mesin = MtcMasterMesinModel::where('kode_mesin', $kodeMesinVal)
                    ->where('jenis_mtc', $jenisMtc)
                    ->first();
            }
            if (!$mesin && $namaMesinVal !== '') {
                $mesin = MtcMasterMesinModel::where('nama_mesin', $namaMesinVal)
                    ->where('jenis_mtc', $jenisMtc)
                    ->first();
            }

            if (!$mesin) {
                $rowErrors[] = "Sheet '{$sheetTitle}' Baris {$rowNum}: Mesin '{$namaMesinVal}' ('{$kodeMesinVal}') tidak ditemukan di Jenis MTC '{$jenisMtc}'.";
                continue;
            }

            $datesRaw = $row['E'] ?? '';
            $packagesRaw = $row['F'] ?? '';

            $parsedList = $this->parseDaysAndPackages($datesRaw, $packagesRaw, $tahun, $bulan);

            if (!empty($parsedList)) {
                $hasSchedule = false;
                foreach ($parsedList as $parsed) {
                    try {
                        MtcAgendaModel::create([
                            'mesin_id'   => $mesin->id,
                            'tahun'      => $tahun,
                            'bulan'      => $bulan,
                            'minggu_ke'  => null,
                            'tanggal'    => $parsed['tanggal'],
                            'paket'      => $parsed['paket'],
                            'created_by' => $userId,
                        ]);
                        $hasSchedule = true;
                    } catch (\Illuminate\Database\QueryException $ex) {
                        if ($ex->getCode() == 23000) {
                            $rowErrors[] = "Sheet '{$sheetTitle}' Baris {$rowNum}: Jadwal ganda terdeteksi untuk Mesin '{$mesin->nama_mesin}' pada Tanggal {$parsed['tanggal']}.";
                        } else {
                            $rowErrors[] = "Sheet '{$sheetTitle}' Baris {$rowNum}: Database error pada Tanggal {$parsed['tanggal']} - " . $ex->getMessage();
                        }
                    }
                }
                if ($hasSchedule) {
                    $inserted++;
                }
            }
        }

        return [
            'inserted' => $inserted,
            'errors'   => $rowErrors
        ];
    }

    /**
     * Helper to process Non-MHE week-based sheet (12 months x weeks & packages)
     */
    private function processNonMheSheet(array $rows, string $jenisMtc, int $tahun, $userId, string $sheetTitle): array
    {
        $inserted = 0;
        $rowErrors = [];

        $monthNames = [
            1  => ['jan', 'januari', 'january'],
            2  => ['feb', 'februari', 'february'],
            3  => ['mar', 'maret', 'march'],
            4  => ['apr', 'april'],
            5  => ['mei', 'may'],
            6  => ['jun', 'juni', 'june'],
            7  => ['jul', 'juli', 'july'],
            8  => ['agt', 'agustus', 'august', 'agu'],
            9  => ['sep', 'september'],
            10 => ['okt', 'oktober', 'october', 'okt'],
            11 => ['nov', 'november'],
            12 => ['des', 'desember', 'december', 'dec']
        ];

        // Scan rows 1-6 to detect month header row
        $headerRowIdx = null;
        for ($r = 1; $r <= 6; $r++) {
            if (!isset($rows[$r])) continue;
            $row = $rows[$r];

            $foundMonthsCount = 0;
            foreach ($row as $colLetter => $cellVal) {
                if (empty($cellVal)) continue;
                $valLower = strtolower(trim((string)$cellVal));

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
            $headerRowIdx = 4;
        }

        // Set column mapping
        $colMap = [
            'nama_mesin' => 'C',
            'kode_mesin' => 'B',
            'months'     => []
        ];

        // Detect nama & kode mesin from header rows
        for ($r = 1; $r <= $headerRowIdx + 1; $r++) {
            if (!isset($rows[$r])) continue;
            foreach ($rows[$r] as $col => $val) {
                $valLower = strtolower(trim((string)$val));
                if (str_contains($valLower, 'kode') || str_contains($valLower, 'code')) {
                    $colMap['kode_mesin'] = $col;
                } elseif (str_contains($valLower, 'nama') || str_contains($valLower, 'mesin')) {
                    $colMap['nama_mesin'] = $col;
                }
            }
        }

        // Search for Month columns dynamically
        $headerRow = $rows[$headerRowIdx] ?? [];
        $monthColIndices = [];
        foreach ($headerRow as $colLetter => $cellVal) {
            if (empty($cellVal)) continue;
            $valLower = strtolower(trim((string)$cellVal));
            foreach ($monthNames as $mNum => $mAliases) {
                if (in_array($valLower, $mAliases, true)) {
                    $colIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($colLetter);
                    $monthColIndices[$mNum] = $colIdx;
                    break;
                }
            }
        }

        // If month columns detected dynamically
        if (count($monthColIndices) >= 6) {
            for ($m = 1; $m <= 12; $m++) {
                if (isset($monthColIndices[$m])) {
                    $weekColIdx = $monthColIndices[$m];
                    $paketColIdx = $weekColIdx + 1;
                    $colMap['months'][$m] = [
                        'week_col'  => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($weekColIdx),
                        'paket_col' => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($paketColIdx),
                    ];
                }
            }
        } else {
            // Fallback to standard starting column E (5)
            $startColIdx = 5;
            for ($m = 1; $m <= 12; $m++) {
                $weekColIdx = $startColIdx + ($m - 1) * 2;
                $paketColIdx = $weekColIdx + 1;

                $colMap['months'][$m] = [
                    'week_col'  => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($weekColIdx),
                    'paket_col' => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($paketColIdx),
                ];
            }
        }

        $dataStart = $headerRowIdx + 2;
        $ignoreHeaders = ['kode mesin', 'kode', 'code', 'nama mesin', 'nama', 'mesin', 'lokasi', 'no', 'nomor'];

        foreach ($rows as $rowNum => $row) {
            if ($rowNum < $dataStart) continue;

            $namaMesinVal = trim((string)($row[$colMap['nama_mesin']] ?? ''));
            $kodeMesinVal = (!empty($colMap['kode_mesin']) && isset($row[$colMap['kode_mesin']])) ? trim((string)$row[$colMap['kode_mesin']]) : '';

            if ($namaMesinVal === '' && $kodeMesinVal === '') {
                continue;
            }

            if (in_array(strtolower($namaMesinVal), $ignoreHeaders, true) || in_array(strtolower($kodeMesinVal), $ignoreHeaders, true)) {
                continue;
            }

            $mesin = null;
            if ($kodeMesinVal !== '') {
                $mesin = MtcMasterMesinModel::where('kode_mesin', $kodeMesinVal)
                    ->where('jenis_mtc', $jenisMtc)
                    ->first();
            }
            if (!$mesin && $namaMesinVal !== '') {
                $mesin = MtcMasterMesinModel::where('nama_mesin', $namaMesinVal)
                    ->where('jenis_mtc', $jenisMtc)
                    ->first();
            }

            if (!$mesin) {
                $rowErrors[] = "Sheet '{$sheetTitle}' Baris {$rowNum}: Mesin '{$namaMesinVal}' ('{$kodeMesinVal}') tidak ditemukan di Jenis MTC '{$jenisMtc}'.";
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
                            'mesin_id'   => $mesin->id,
                            'tahun'      => $tahun,
                            'bulan'      => $mNum,
                            'minggu_ke'  => $parsed['minggu_ke'],
                            'tanggal'    => null,
                            'paket'      => $parsed['paket'],
                            'created_by' => $userId,
                        ]);
                        $hasSchedule = true;
                    } catch (\Illuminate\Database\QueryException $ex) {
                        if ($ex->getCode() == 23000) {
                            $rowErrors[] = "Sheet '{$sheetTitle}' Baris {$rowNum}: Jadwal ganda terdeteksi untuk Mesin '{$mesin->nama_mesin}' pada Bulan {$mNum} Minggu {$parsed['minggu_ke']}.";
                        } else {
                            $rowErrors[] = "Sheet '{$sheetTitle}' Baris {$rowNum}: Database error pada Bulan {$mNum} Minggu {$parsed['minggu_ke']} - " . $ex->getMessage();
                        }
                    }
                }
            }

            if ($hasSchedule) {
                $inserted++;
            }
        }

        return [
            'inserted' => $inserted,
            'errors'   => $rowErrors
        ];
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

    /**
     * Helper parser to parse days and packages from Excel cell values for date-based engines.
     */
    private function parseDaysAndPackages($daysRaw, $packagesRaw, $tahun, $bulan): array
    {
        if (empty($daysRaw) || trim((string)$daysRaw) === '') {
            return [];
        }

        $cleanDays = str_replace([';', '/', '|', ' '], ',', (string)$daysRaw);
        $days = array_filter(explode(',', $cleanDays), 'strlen');

        $cleanPackages = str_replace([';', '/', '|', ' '], ',', (string)$packagesRaw);
        $packages = array_filter(explode(',', $cleanPackages), 'strlen');

        $days = array_values($days);
        $packages = array_values($packages);

        $results = [];
        $numDays = count($days);
        $numPackages = count($packages);

        if ($numDays > 0) {
            for ($i = 0; $i < $numDays; $i++) {
                $dayNum = intval(trim($days[$i]));
                if ($dayNum < 1 || $dayNum > 31) continue;

                try {
                    $dateObj = Carbon::createFromDate($tahun, $bulan, $dayNum);
                    $dateStr = $dateObj->format('Y-m-d');
                } catch (\Exception $e) {
                    continue;
                }

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
                        'tanggal' => $dateStr,
                        'paket' => $pkg
                    ];
                }
            }
        }

        return $results;
    }
}
