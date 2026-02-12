<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\WwtpSludge;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class WWTPControllerSludge extends Controller
{
    //
    public function form_sludge()
    {
        return view('utility.wwtp.form_sludge');
    }

    public function data_sludge()
    {
        return view('utility.wwtp.data_sludge');
    }

    /**
     * Menampilkan semua data sludge WWTP (JSON)
     */
    public function index()
    {
        $data = WwtpSludge::orderBy('tanggal', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    /**
     * Simpan data sludge WWTP
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'        => 'required|date',
            'shift'          => 'required|in:1,2,3',
            'drain_lumpur'   => 'required|numeric|min:0',
            'running_hour_scp' => 'required|numeric|min:0'
        ]);
        $existing = WwtpSludge::where('tanggal', $request->tanggal)
            ->where('shift', $request->shift)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Data untuk shift ini pada tanggal tersebut sudah ada. Setiap tanggal hanya boleh memiliki maksimal 3 shift (shift1, shift2, shift3).'
            ], 409);
        }

        // Cek jumlah shift pada tanggal tersebut (maksimal 3)
        $shiftCount = WwtpSludge::where('tanggal', $request->tanggal)->count();

        if ($shiftCount >= 3) {
            return response()->json([
                'message' => 'Tanggal ini sudah memiliki 3 shift. Tidak dapat menambah data lagi.'
            ], 409);
        }
        WwtpSludge::create([
            'tanggal'        => $request->tanggal,
            'shift'          => $request->shift,
            'drain_lumpur'   => $request->drain_lumpur,
            'running_hour_scp' => $request->running_hour_scp
        ]);
        return response()->json([
            'status'  => 'success',
            'message' => 'Data sludge WWTP berhasil disimpan.'
        ]);
    }

    public function show($id)
    {
        $data = WwtpSludge::findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update dat
     */
    public function update(Request $request, $id)
    {
        $harian = WwtpSludge::findOrFail($id);

        $request->validate([
            'tanggal'  => 'required|date',
            'shift'    => 'required|in:shift1,shift2,shift3',
            'drain_lumpur'   => 'nullable|numeric',
            'running_hour_scp' => 'nullable|numeric|min:0'
        ]);

        $harian->update($request->all());

        return response()->json([
            'message' => 'Data harian berhasil diperbarui.',
            'data' => $harian
        ]);
    }

    /**
     * Hapus data harian
     */
    public function destroy($id)
    {
        $harian = WwtpSludge::findOrFail($id);
        $harian->delete();

        return response()->json(['message' => 'Data harian berhasil dihapus.']);
    }

    // 
    /**
     * Get dashboard statistics
     */
    public function getStatistics()
    {
        try {
            // Total shifts
            $totalShifts = WwtpSludge::count();

            // Total unique days
            $totalDays = WwtpSludge::distinct('tanggal')->count('tanggal');

            // Shifts this week
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $shiftsThisWeek = WwtpSludge::whereBetween('tanggal', [$startOfWeek, $endOfWeek])->count();

            // Shifts today
            $shiftsToday = WwtpSludge::whereDate('tanggal', Carbon::today())->count();

            // Last update
            $lastRecord = WwtpSludge::orderBy('tanggal', 'desc')
            ->orderBy('shift', 'desc')
            ->first();

            // Monthly averages
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            $monthlyStats = WwtpSludge::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->selectRaw('AVG(drain_lumpur) as avg_drain, AVG(running_hour_scp) as avg_running_hour')
                ->first();

            return response()->json([
                'total_shifts' => $totalShifts,
                'total_days' => $totalDays,
                'shifts_this_week' => $shiftsThisWeek,
                'shifts_today' => $shiftsToday,
                'last_update' => $lastRecord ? $lastRecord->tanggal : null,
                'last_shift' => $lastRecord ? $lastRecord->shift : null,
                'monthly_drain_avg' => $monthlyStats ? round($monthlyStats->avg_drain, 2) : 0,
                'monthly_running_hour_avg' => $monthlyStats ? round($monthlyStats->avg_running_hour, 2) : 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get drain lumpur chart data (aggregated by date)
     */
    public function getDrainChart(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

            $data = WwtpSludge::whereBetween('tanggal', [$startDate, $endDate])
                ->select('tanggal')
                ->selectRaw('SUM(drain_lumpur) as total_drain')
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'asc')
                ->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching drain chart data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get running hour SCP chart data (aggregated by date)
     */
    public function getRunningHourChart(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

            $data = WwtpSludge::whereBetween('tanggal', [$startDate, $endDate])
                ->select('tanggal')
                ->selectRaw('SUM(running_hour_scp) as total_running_hour')
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'asc')
                ->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching running hour chart data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get shift breakdown data
     */
    public function getShiftBreakdown(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

            $data = WwtpSludge::whereBetween('tanggal', [$startDate, $endDate])
                ->select('shift')
                ->selectRaw('SUM(drain_lumpur) as total')
                ->groupBy('shift')
                ->orderBy('shift', 'asc')
                ->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching shift breakdown data',
                'message' => $e->getMessage()
            ], 500);
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
                    // Format month to be more readable
                    $date = Carbon::createFromFormat('Y-m', $item->month);
                    $item->month = $date->format('M Y');
                    return $item;
                });

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching monthly comparison data',
                'message' => $e->getMessage()
            ], 500);
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

            // Get detailed shifts for each date
            foreach ($records as $record) {
                $record->shifts = WwtpSludge::where('tanggal', $record->tanggal)
                    ->orderBy('shift', 'asc')
                    ->get(['shift', 'drain_lumpur', 'running_hour_scp']);
            }

            return response()->json($records);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching recent records',
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
