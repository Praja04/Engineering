<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\Utility\WwtpDailyApproval;
use App\Models\Utility\WwtpPengangkutanSludge;
use App\Models\Utility\WwtpSludge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WWTPControllerSludge extends Controller
{
    public function form_sludge()
    {
        return view('utility.wwtp.form_sludge');
    }

    public function data_sludge()
    {
        return view('utility.wwtp.data_sludge');
    }

    /**
     * Menampilkan semua data sludge WWTP (JSON) — server-side pagination
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page    = $request->input('page', 1);
        $shift   = $request->input('shift');
        $bulan   = $request->input('bulan');
        $search  = $request->input('search');

        $query = WwtpSludge::orderBy('tanggal', 'desc')->orderBy('shift', 'asc');

        if ($shift) {
            $query->where('shift', $shift);
        }

        if ($bulan) {
            $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan]);
        }

        if ($search) {
            $query->where('tanggal', 'like', "%{$search}%");
        }

        return response()->json(
            $query->paginate($perPage, ['*'], 'page', $page)
        );
    }

    /**
     * Simpan data sludge WWTP
     */
    public function store(Request $request)
    {
        $approval = WwtpDailyApproval::where('tanggal', $request->tanggal)->first();

        if (!$approval) {
            $request->validate([
                'foreman_id' => 'required|exists:users,id',
                'supervisor_id' => 'required|exists:users,id',
            ]);
        }

        $request->validate([
            'tanggal'            => 'required|date',
            'shift'              => 'required|in:shift1,shift2,shift3',
            'drain_lumpur'       => 'required|numeric|min:0',
            'hasil_lumpur'       => 'required|numeric|min:0',
            'running_hour_scp'   => 'required|numeric|min:0',
            'sludge_content'     => 'nullable|numeric|min:0',
        ]);

        $existing = WwtpSludge::where('tanggal', $request->tanggal)
            ->where('shift', $request->shift)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Data untuk shift ini pada tanggal tersebut sudah ada. Setiap tanggal hanya boleh memiliki maksimal 3 shift (shift1, shift2, shift3).'
            ], 409);
        }

        $shiftCount = WwtpSludge::where('tanggal', $request->tanggal)->count();

        if ($shiftCount >= 3) {
            return response()->json([
                'message' => 'Tanggal ini sudah memiliki 3 shift. Tidak dapat menambah data lagi.'
            ], 409);
        }

        $sludge = WwtpSludge::create([
            'tanggal'          => $request->tanggal,
            'shift'            => $request->shift,
            'drain_lumpur'     => $request->drain_lumpur,
            'hasil_lumpur'     => $request->hasil_lumpur,
            'running_hour_scp' => $request->running_hour_scp,
            'sludge_content'   => $request->sludge_content,
        ]);

        // Create or update daily approval
        if (!$approval) {
            $approval = WwtpDailyApproval::create([
                'tanggal' => $request->tanggal,
                'operator_id' => Auth::id(),
                'foreman_id' => $request->foreman_id,
                'supervisor_id' => $request->supervisor_id,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            // Notify foreman
            NotificationsModel::create([
                'user_id' => $request->foreman_id,
                'title' => 'Approval Harian WWTP',
                'message' => 'Data harian WWTP tanggal ' . Carbon::parse($request->tanggal)->format('d/m/Y') . ' telah diajukan dan menunggu persetujuan Anda.',
                'url' => url('/wwtp/approval'),
                'notifiable_type' => WwtpDailyApproval::class,
                'notifiable_id' => $approval->id,
                'is_read' => 0,
            ]);
        } else {
            if ($approval->status === 'rejected') {
                $approval->update([
                    'status' => 'submitted',
                    'reject_reason' => null,
                    'operator_id' => Auth::id(),
                    'submitted_at' => now(),
                ]);

                // Re-notify foreman
                if ($approval->foreman_id) {
                    NotificationsModel::updateOrCreate(
                        [
                            'user_id' => $approval->foreman_id,
                            'notifiable_type' => WwtpDailyApproval::class,
                            'notifiable_id' => $approval->id,
                        ],
                        [
                            'title' => 'Approval Harian WWTP',
                            'message' => 'Data harian WWTP tanggal ' . Carbon::parse($request->tanggal)->format('d/m/Y') . ' telah diperbarui dan menunggu persetujuan Anda.',
                            'url' => url('/wwtp/approval'),
                            'is_read' => 0,
                        ]
                    );
                }
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data sludge WWTP berhasil disimpan.',
        ]);
    }

    public function show($id)
    {
        $data = WwtpSludge::findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update data sludge
     */
    public function update(Request $request, $id)
    {
        $harian = WwtpSludge::findOrFail($id);

        $request->validate([
            'tanggal'          => 'required|date',
            'shift'            => 'required',
            'drain_lumpur'     => 'nullable|numeric|min:0',
            'hasil_lumpur'     => 'nullable|numeric|min:0',
            'running_hour_scp' => 'nullable|numeric|min:0',
            'sludge_content'   => 'nullable|numeric|min:0',
        ]);

        // Check daily approval for both new and old date
        $approval = \App\Models\Utility\WwtpDailyApproval::where('tanggal', $request->tanggal)->first();
        if ($approval && in_array($approval->status, ['approved_foreman', 'approved_supervisor'])) {
            return response()->json([
                'message' => 'Laporan harian untuk tanggal ini sudah disetujui, tidak dapat mengubah data.'
            ], 422);
        }

        $oldApproval = \App\Models\Utility\WwtpDailyApproval::where('tanggal', $harian->tanggal)->first();
        if ($oldApproval && in_array($oldApproval->status, ['approved_foreman', 'approved_supervisor'])) {
            return response()->json([
                'message' => 'Laporan harian untuk tanggal awal sudah disetujui, tidak dapat mengubah data.'
            ], 422);
        }

        $harian->update($request->only([
            'tanggal',
            'shift',
            'drain_lumpur',
            'hasil_lumpur',
            'running_hour_scp',
            'sludge_content',
        ]));

        // If daily approval exists and is rejected, reset it to submitted
        if ($approval && $approval->status === 'rejected') {
            $approval->update([
                'status' => 'submitted',
                'reject_reason' => null,
                'operator_id' => \Illuminate\Support\Facades\Auth::id(),
                'submitted_at' => now(),
            ]);

            if ($approval->foreman_id) {
                \App\Models\NotificationsModel::updateOrCreate(
                    [
                        'user_id' => $approval->foreman_id,
                        'notifiable_type' => \App\Models\Utility\WwtpDailyApproval::class,
                        'notifiable_id' => $approval->id,
                    ],
                    [
                        'title' => 'Approval Harian WWTP',
                        'message' => 'Data harian WWTP tanggal ' . Carbon::parse($request->tanggal)->format('d/m/Y') . ' telah diperbarui dan menunggu persetujuan Anda.',
                        'url' => url('/wwtp/approval'),
                        'is_read' => 0,
                    ]
                );
            }
        }

        return response()->json([
            'message' => 'Data harian berhasil diperbarui.',
            'data'    => $harian,
        ]);
    }

    /**
     * Hapus data harian
     */
    public function destroy($id)
    {
        $harian = WwtpSludge::findOrFail($id);
        $approval = \App\Models\Utility\WwtpDailyApproval::where('tanggal', $harian->tanggal)->first();
        if ($approval && in_array($approval->status, ['approved_foreman', 'approved_supervisor'])) {
            return response()->json([
                'message' => 'Laporan harian untuk tanggal ini sudah disetujui, tidak dapat dihapus.'
            ], 422);
        }

        $harian->delete();

        return response()->json(['message' => 'Data harian berhasil dihapus.']);
    }

    /**
     * Get dashboard statistics
     */
    public function getStatistics()
    {
        try {
            $totalShifts = WwtpSludge::count();
            $totalDays   = WwtpSludge::distinct('tanggal')->count('tanggal');

            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek   = Carbon::now()->endOfWeek();
            $shiftsThisWeek = WwtpSludge::whereBetween('tanggal', [$startOfWeek, $endOfWeek])->count();

            $shiftsToday = WwtpSludge::whereDate('tanggal', Carbon::today())->count();

            $lastRecord = WwtpSludge::orderBy('tanggal', 'desc')
                ->orderBy('shift', 'desc')
                ->first();

            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth   = Carbon::now()->endOfMonth();

            $monthlyStats = WwtpSludge::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->selectRaw('AVG(drain_lumpur) as avg_drain, AVG(running_hour_scp) as avg_running_hour')
                ->first();

            return response()->json([
                'total_shifts'             => $totalShifts,
                'total_days'               => $totalDays,
                'shifts_this_week'         => $shiftsThisWeek,
                'shifts_today'             => $shiftsToday,
                'last_update'              => $lastRecord ? $lastRecord->tanggal : null,
                'last_shift'               => $lastRecord ? $lastRecord->shift : null,
                'monthly_drain_avg'        => $monthlyStats ? round($monthlyStats->avg_drain, 2) : 0,
                'monthly_running_hour_avg' => $monthlyStats ? round($monthlyStats->avg_running_hour, 2) : 0,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching statistics', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get drain lumpur chart data (aggregated by date)
     */
    public function getDrainChart(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
            $endDate   = $request->input('end_date',   Carbon::now()->endOfMonth());

            $data = WwtpSludge::whereBetween('tanggal', [$startDate, $endDate])
                ->select('tanggal')
                ->selectRaw('SUM(drain_lumpur) as total_drain')
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'asc')
                ->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching drain chart data', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get running hour SCP chart data (aggregated by date)
     */
    public function getRunningHourChart(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
            $endDate   = $request->input('end_date',   Carbon::now()->endOfMonth());

            $data = WwtpSludge::whereBetween('tanggal', [$startDate, $endDate])
                ->select('tanggal')
                ->selectRaw('SUM(running_hour_scp) as total_running_hour')
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'asc')
                ->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching running hour chart data', 'message' => $e->getMessage()], 500);
        }
    }

    public function getHasilLumpurChart(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
            $endDate   = $request->input('end_date',   Carbon::now()->endOfMonth());

            $data = WwtpSludge::whereBetween('tanggal', [$startDate, $endDate])
                ->select('tanggal')
                ->selectRaw('SUM(hasil_lumpur) as total_hasil_lumpur')
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'asc')
                ->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching hasil lumpur chart data', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get shift breakdown data
     */
    public function getShiftBreakdown(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
            $endDate   = $request->input('end_date',   Carbon::now()->endOfMonth());

            $data = WwtpSludge::whereBetween('tanggal', [$startDate, $endDate])
                ->selectRaw('
                    SUM(drain_lumpur) as total_drain_lumpur,
                    SUM(running_hour_scp) as total_running_hour_scp,
                    SUM(hasil_lumpur) as total_hasil_lumpur')
                ->first();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching shift breakdown data', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get 6-month comparison data
     */
    public function getMonthlyComparison()
    {
        try {
            $data = WwtpSludge::select(
                DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as month"),
                DB::raw('SUM(drain_lumpur) as drain_lumpur'),
                DB::raw('SUM(running_hour_scp) as running_hour_scp')
            )
                ->where('tanggal', '>=', Carbon::now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get()
                ->map(function ($item) {
                    $date        = Carbon::createFromFormat('Y-m', $item->month);
                    $item->month = $date->format('M Y');
                    return $item;
                });

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching monthly comparison data', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get recent records grouped by date
     */
    public function getRecentRecords($limit = 10)
    {
        try {
            $records = WwtpSludge::select('tanggal')
                ->selectRaw('COUNT(*) as shift_count')
                ->selectRaw('SUM(drain_lumpur) as total_drain')
                ->selectRaw('SUM(running_hour_scp) as total_running_hour')
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'desc')
                ->limit($limit)
                ->get();

            foreach ($records as $record) {
                $record->shifts = WwtpSludge::where('tanggal', $record->tanggal)
                    ->orderBy('shift', 'asc')
                    ->get(['shift', 'drain_lumpur', 'running_hour_scp']);
            }

            return response()->json($records);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching recent records', 'message' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────
    // Pengangkutan Sludge
    // ─────────────────────────────────────────────

    /**
     * Menampilkan semua data pengangkutan sludge (JSON) — server-side pagination
     */
    public function index_pengangkutan(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page    = $request->input('page', 1);
        $bulan   = $request->input('bulan');
        $search  = $request->input('search');

        $query = WwtpPengangkutanSludge::orderBy('week_start', 'desc');

        if ($bulan) {
            $query->whereRaw("DATE_FORMAT(week_start, '%Y-%m') = ?", [$bulan]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('week_start', 'like', "%{$search}%")
                    ->orWhere('week_end', 'like', "%{$search}%");
            });
        }

        return response()->json(
            $query->paginate($perPage, ['*'], 'page', $page)
        );
    }

    /**
     * Simpan data pengangkutan sludge
     */
    public function store_pengangkutan(Request $request)
    {
        $request->validate([
            'tanggal'             => 'required|date',
            'jumlah_pengangkutan' => 'required|numeric|min:0',
        ]);

        $tanggal   = Carbon::parse($request->tanggal);
        $startWeek = $tanggal->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endWeek   = $tanggal->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $existing = WwtpPengangkutanSludge::where('week_start', $startWeek)->first();
        if ($existing) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data untuk minggu ini sudah ada.',
            ], 409);
        }

        $data = WwtpPengangkutanSludge::create([
            'week_start'          => $startWeek,
            'week_end'            => $endWeek,
            'jumlah_pengangkutan' => $request->jumlah_pengangkutan,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data berhasil disimpan.',
            'data'    => $data,
        ]);
    }

    /**
     * Detail data pengangkutan
     */
    public function show_pengangkutan($id)
    {
        $data = WwtpPengangkutanSludge::findOrFail($id);
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * Update data pengangkutan
     */
    public function update_pengangkutan(Request $request, $id)
    {
        $data = WwtpPengangkutanSludge::findOrFail($id);

        $request->validate([
            'tanggal'             => 'required|date',
            'jumlah_pengangkutan' => 'required|numeric|min:0',
        ]);

        $tanggal   = Carbon::parse($request->tanggal);
        $startWeek = $tanggal->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endWeek   = $tanggal->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $data->update([
            'week_start'          => $startWeek,
            'week_end'            => $endWeek,
            'jumlah_pengangkutan' => $request->jumlah_pengangkutan,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data berhasil diperbarui.',
            'data'    => $data,
        ]);
    }

    /**
     * Hapus data pengangkutan
     */
    public function destroy_pengangkutan($id)
    {
        $data = WwtpPengangkutanSludge::findOrFail($id);
        $data->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    /**
     * Get sludge content chart data (aggregated by date)
     */
    public function getSludgeContentChart(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
            $endDate   = $request->input('end_date',   Carbon::now()->endOfMonth());

            $data = WwtpSludge::whereBetween('tanggal', [$startDate, $endDate])
                ->select('tanggal')
                ->selectRaw('AVG(sludge_content) as avg_sludge_content')
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'asc')
                ->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching sludge content chart data', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get pengangkutan sludge chart data
     */
    public function getPengangkutanChart(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate   = $request->input('end_date');

            $query = WwtpPengangkutanSludge::orderBy('week_start', 'asc');

            if ($startDate && $endDate) {
                $query->whereBetween('week_start', [$startDate, $endDate]);
            } else {
                // Default 2 bulan terakhir
                $twoMonthsAgo = Carbon::now()->subMonths(2)->startOfMonth()->toDateString();
                $query->where('week_start', '>=', $twoMonthsAgo);
            }

            $data = $query->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching pengangkutan chart data', 'message' => $e->getMessage()], 500);
        }
    }
}
