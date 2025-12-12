<?php

namespace App\Http\Controllers\ScoringMesin;

use App\Http\Controllers\Controller;
use App\Models\ScoringMesin\Machine;
use App\Models\ScoringMesin\MachineProcess;
use App\Models\ScoringMesin\MachineScoring;
use App\Models\ScoringMesin\ScoringDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

class MachineScoringController extends Controller
{
    /**
     * Display a listing of machines for scoring
     */
    public function index()
    {
        $machines = Machine::with(['machineProcesses.processParameter'])
            // ->active()
            ->get();

        return view('scoringmesin.scoring.index', compact('machines'));
    }

    /**
     * Display machine processes for selected machine
     */
    public function showMachineProcesses(Machine $machine)
    {
        $machineProcesses = $machine->machineProcesses()
            ->with([
                'processParameter.sections.parts',
                'machineScorings' => function ($query) {
                    $query->completed()->latest('scoring_date');
                }
            ])
            ->get();

        // Tambahkan flag apakah sudah di scoring minggu ini
        $machineProcesses->each(function ($process) {
            $latest = $process->machineScorings->first();
            $process->scored_this_week = false;

            if ($latest) {
                $process->last_scoring = $latest->scoring_date;
                $process->last_user = $latest->user->username ?? 'Tidak diketahui';

                // Cek apakah scoring terakhir ada di minggu ini
                if ($latest->scoring_date->isCurrentWeek()) {
                    $process->scored_this_week = true;
                }
            }
        });

        return view('scoringmesin.scoring.processes', compact('machine', 'machineProcesses'));
    }


    /**
     * Display sections and parts for scoring
     */
    public function showScoringForm($machineProcessId)
    {
        $machineProcess = MachineProcess::with([
            'machine',
            'processParameter.sections.parts'
        ])->findOrFail($machineProcessId);

        // Cek draft scoring aktif
        $existingScoring = MachineScoring::where('machine_process_id', $machineProcessId)
            ->where('user_id', Auth::id())
            ->where('status', 'draft')
            ->first();

        // Ambil detail lama (jika ada)
        $existingDetails = [];
        if ($existingScoring) {
            $existingDetails = $existingScoring->scoringDetails()
                ->get()
                ->keyBy('part_id')
                ->toArray();
        }

        $sections = $machineProcess->processParameter->sections()
            ->with('parts')
            ->get();

        return view('scoringmesin.scoring.form', compact(
            'machineProcess',
            'sections',
            'existingScoring',
            'existingDetails'
        ));
    }

    /**
     * Store or update scoring
     */
    public function store(Request $request, $machineProcessId)
    {
        // Validasi data input
        $request->validate([
            'parts' => 'required|array|min:1',
            'parts.*.part_id' => 'required|exists:parts,id',
            'parts.*.result' => 'required|in:OK,NOT OK',
            'parts.*.notes' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,completed',
        ]);

        DB::beginTransaction();
        try {
            // Cari draft aktif (tidak berdasarkan status)
            $machineScoring = MachineScoring::firstOrNew([
                'machine_process_id' => $machineProcessId,
                'user_id' => Auth::id(),
            ]);

            $machineScoring->scoring_date = now();
            $machineScoring->notes = $request->notes;
            $machineScoring->status = $request->status;
            $machineScoring->save();

            // Hapus detail lama
            $machineScoring->scoringDetails()->delete();

            // Simpan ulang detail scoring
            foreach ($request->parts as $partData) {
                ScoringDetail::create([
                    'machine_scoring_id' => $machineScoring->id,
                    'part_id' => $partData['part_id'],
                    'result' => $partData['result'],
                    'notes' => $partData['notes'] ?? null,
                ]);
            }
            
            DB::commit();

            // Pesan sukses dan redirect sesuai status
            if ($request->status === 'completed') {
                return redirect()
                    ->route('scoring.history')
                    ->with('success', '✅ Scoring berhasil diselesaikan!');
            } else {
                return redirect()
                    ->route('scoring.form', $machineProcessId)
                    ->with('success', '💾 Draft scoring berhasil disimpan!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display scoring history
     */

    // public function history(Request $request)
    // {
    //     $month = $request->input('month', now()->format('Y-m'));
    //     $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    //     $endOfMonth = $startOfMonth->copy()->endOfMonth();

    //     $scorings = MachineScoring::with([
    //         'machineProcess.machine',
    //         'machineProcess.processParameter',
    //         'user'
    //     ])
    //     ->where('status', 'completed')
    //     ->whereBetween('scoring_date', [$startOfMonth, $endOfMonth])
    //     ->orderBy('scoring_date', 'asc')
    //     ->get();

    //     $weeks = [];
    //     $period = CarbonPeriod::create($startOfMonth, '7 days', $endOfMonth);

    //     foreach ($period as $weekStart) {
    //         $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

    //         $weekScorings = $scorings->filter(function ($scoring) use ($weekStart, $weekEnd) {
    //             return $scoring->scoring_date->between($weekStart, $weekEnd);
    //         });

    //         if ($weekScorings->isNotEmpty()) {
    //             $weeks[] = [
    //                 'label' => 'Minggu ' . (count($weeks) + 1),
    //                 'start' => $weekStart->format('d M'),
    //                 'end' => $weekEnd->format('d M'),
    //                 'scorings' => $weekScorings,
    //             ];
    //         }
    //     }
    //     //return response json for debugging
    //     return response()->json(['weeks' => $weeks, 'month' => $month]);

    //    // return view('scoringmesin.scoring.history', compact('weeks', 'month'));
    // }

    public function history(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $scorings = MachineScoring::with([
            'machineProcess.machine',
            'machineProcess.processParameter',
            // 'scoringDetails.part',
            'user'
        ])
            ->where('status', 'completed')
            ->whereBetween('scoring_date', [
                $startOfMonth, $endOfMonth
            ])
            ->orderBy('scoring_date', 'asc')
            ->get();

        $weeks = [];
        $period = CarbonPeriod::create($startOfMonth, '7 days', $endOfMonth);

        foreach ($period as $weekStart) {
            $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

            $weekScorings = $scorings->filter(function ($scoring) use ($weekStart, $weekEnd) {
                return $scoring->scoring_date->between($weekStart, $weekEnd);
            });

            if ($weekScorings->isNotEmpty()) {
                // Group by machine
                $machineGroups = $weekScorings->groupBy('machineProcess.machine.id');

                $machinesData = [];

                foreach ($machineGroups as $machineId => $machineScoringGroup) {
                    $firstScoring = $machineScoringGroup->first();
                    $machine = $firstScoring->machineProcess->machine;

                    // Calculate weekly machine score
                    $weeklyScore = MachineScoring::getWeeklyMachineScore(
                        $machineId,
                        $weekStart,
                        $weekEnd
                    );

                    $machinesData[] = [
                        'machine_id' => $machineId,
                        'machine_name' => $machine->name,
                        'machine_code' => $machine->code,
                        'weekly_percentage' => $weeklyScore,
                        'process_count' => $machineScoringGroup->count(),
                        'scorings' => $machineScoringGroup->values()->all(),
                    ];
                }

                $weeks[] = [
                    'label' => 'Minggu ' . (count($weeks) + 1),
                    'start' => $weekStart->format('d M'),
                    'end' => $weekEnd->format('d M'),
                    'machines' => $machinesData,
                ];
            }
        }
        // return response()->json(['weeks' => $weeks, 'month' => $month]);
        return view('scoringmesin.scoring.history', compact('weeks', 'month'));
    }
    /**
     * Display scoring detail
     */
    public function show($scoringId)
    {
        $scoring = MachineScoring::with([
            'machineProcess.machine',
            'machineProcess.processParameter.sections.parts',
            'scoringDetails.part.section',
            'user'
        ])->findOrFail($scoringId);

        $summary = $scoring->getSummary();

        return view('scoringmesin.scoring.show', compact('scoring', 'summary'));
    }

    /**
     * Delete draft scoring
     */
    public function destroy($scoringId)
    {
        try {
            $scoring = MachineScoring::findOrFail($scoringId);

            if ($scoring->user_id !== Auth::id() || $scoring->status === 'completed') {
                return back()->with('error', 'Anda tidak dapat menghapus scoring ini!');
            }

            $scoring->delete();

            return back()->with('success', '🗑️ Draft scoring berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get scoring statistics
     */
    public function statistics(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth();
        $endDate = $request->end_date ?? now()->endOfMonth();

        $scorings = MachineScoring::with([
            'machineProcess.machine',
            'machineProcess.processParameter'
        ])
            ->where('status', 'completed')
            ->whereBetween('scoring_date', [$startDate, $endDate])
            ->get();

        $statistics = [
            'total_scorings' => $scorings->count(),
            'total_ok' => $scorings->sum(fn ($s) => $s->scoringDetails()->where('result', 'OK')->count()),
            'total_not_ok' => $scorings->sum(fn ($s) => $s->scoringDetails()->where('result', 'NOT OK')->count()),
            'by_machine' => $scorings->groupBy('machineProcess.machine.name')->map(fn ($items) => [
                'count' => $items->count(),
                'avg_ok_percentage' => round($items->avg('ok_percentage'), 2),
            ]),
        ];

        return view('scoringmesin.scoring.statistics', compact('statistics', 'startDate', 'endDate'));
    }


    ///

    public function  api_scoring_mesin(Request $request)
    {
        // Ambil minggu yang dipilih, default ke minggu ini (format: 2025-W40)
        $selectedWeek = $request->input('week');

        // Jika tidak ada parameter week, ambil minggu saat ini
        if (!$selectedWeek) {
            $year = now()->format('o'); // ISO Year
            $week = now()->format('W'); // Week number (01–53)
            $selectedWeek = "{$year}-W{$week}";
        }

        // Pastikan format benar (2025-W40)
        if (!preg_match('/^\d{4}-W\d{2}$/', $selectedWeek)) {
            return response()->json([
                'success' => false,
                'message' => 'Format minggu tidak valid. Gunakan format seperti 2025-W40.',
                'error' => 'Invalid week format',
            ], 400);
        }

        try {
            // Parse ke Carbon
            $weekDate = \Carbon\Carbon::now();
            $weekDate->setISODate(
                substr($selectedWeek, 0, 4),
                intval(substr($selectedWeek, 6, 2))
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses minggu.',
                'error' => $e->getMessage(),
            ], 400);
        }

        // Ambil tanggal awal & akhir minggu
        $weekStart = $weekDate->copy()->startOfWeek();
        $weekEnd = $weekDate->copy()->endOfWeek();

        // Ambil semua mesin yang memiliki scoring pada minggu ini
        $machines = Machine::whereHas('machineProcesses.machineScorings', function ($query) use ($weekStart, $weekEnd) {
            $query->where('status', 'completed')
                ->whereBetween('scoring_date', [$weekStart, $weekEnd]);
        })
            ->with(['machineProcesses.machineScorings' => function ($query) use ($weekStart, $weekEnd) {
                $query->where('status', 'completed')
                    ->whereBetween('scoring_date', [$weekStart, $weekEnd])
                    ->with('scoringDetails.part');
            }])
            ->get();

        $machineScores = [];

        foreach ($machines as $machine) {
            // Hitung skor mingguan per mesin
            $weeklyScore = MachineScoring::getWeeklyMachineScore(
                $machine->id,
                $weekStart,
                $weekEnd
            );

            // Jumlah process yang sudah di-scoring
            $completedProcesses = $machine->machineProcesses()
                ->whereHas('machineScorings', function ($query) use ($weekStart, $weekEnd) {
                    $query->where('status', 'completed')
                        ->whereBetween('scoring_date', [$weekStart, $weekEnd]);
                })
                ->count();

            $totalProcesses = $machine->machineProcesses()->count();

            // Detail deduction
            $scorings = MachineScoring::with('scoringDetails.part')
                ->whereHas('machineProcess', function ($query) use ($machine) {
                    $query->where('machine_id', $machine->id);
                })
                ->where('status', 'completed')
                ->whereBetween('scoring_date', [$weekStart, $weekEnd])
                ->get();

            $totalDeduction = 0;
            $criticalNotOk = 0;
            $nonCriticalNotOk = 0;

            foreach ($scorings as $scoring) {
                foreach ($scoring->scoringDetails as $detail) {
                    if ($detail->result === 'NOT OK') {
                        if ($detail->part->critical === 'Y') {
                            $criticalNotOk++;
                            $totalDeduction += 4;
                        } else {
                            $nonCriticalNotOk++;
                            $totalDeduction += 1;
                        }
                    }
                }
            }

            $machineScores[] = [
                'machine_id' => $machine->id,
                'machine_name' => $machine->name,
                'machine_code' => $machine->code,
                'machine_status' => $machine->status,
                'weekly_percentage' => $weeklyScore,
                'completed_processes' => $completedProcesses,
                'total_processes' => $totalProcesses,
                'completion_rate' => $totalProcesses > 0 ? round(($completedProcesses / $totalProcesses) * 100, 2) : 0,
                'total_deduction_points' => $totalDeduction,
                'critical_not_ok_count' => $criticalNotOk,
                'non_critical_not_ok_count' => $nonCriticalNotOk,
                'total_scorings' => $scorings->count(),
            ];
        }

        // Urutkan dari persentase terendah ke tertinggi
        usort($machineScores, fn ($a, $b) => $a['weekly_percentage'] <=> $b['weekly_percentage']);

        // Statistik keseluruhan
        $overallAverage = count($machineScores) > 0
            ? round(array_sum(array_column($machineScores, 'weekly_percentage')) / count($machineScores), 2)
            : 0;

        $totalMachines = count($machineScores);
        $excellentMachines = count(array_filter($machineScores, fn ($m) => $m['weekly_percentage'] >= 95));
        $goodMachines = count(array_filter($machineScores, fn ($m) => $m['weekly_percentage'] >= 85 && $m['weekly_percentage'] < 95));
        $fairMachines = count(array_filter($machineScores, fn ($m) => $m['weekly_percentage'] >= 75 && $m['weekly_percentage'] < 85));
        $poorMachines = count(array_filter($machineScores, fn ($m) => $m['weekly_percentage'] < 75));

        return response()->json([
            'success' => true,
            'week_info' => [
                'selected_week' => $selectedWeek,
                'week_start' => $weekStart->format('Y-m-d'),
                'week_end' => $weekEnd->format('Y-m-d'),
                'week_label' => $weekStart->format('d M Y') . ' - ' . $weekEnd->format('d M Y'),
            ],
            'summary' => [
                'total_machines' => $totalMachines,
                'overall_average' => $overallAverage,
                'excellent_machines' => $excellentMachines,
                'good_machines' => $goodMachines,
                'fair_machines' => $fairMachines,
                'poor_machines' => $poorMachines,
            ],
            'machines' => $machineScores,
        ]);
    }

}
