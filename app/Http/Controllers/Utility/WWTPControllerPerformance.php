<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\WwtpPerformanceWeek;
use App\Models\Utility\WwtpPerformanceRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class WWTPControllerPerformance extends Controller
{
    public function performance()
    {
        return view('utility.wwtp.performance');
    }

    /**
     * Menampilkan semua data performance WWTP (JSON)
     */
    public function index()
    {
        $data = WwtpPerformanceWeek::with('records')
            ->orderBy('week_start', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    /**
     * Simpan data performance WWTP
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis'   => 'required|in:equal,outlet_anaerob,aerob,daf,outlet',
            'tss'     => 'required|numeric|min:0',
            'cod'     => 'required|numeric|min:0',
            'foto'    => 'nullable|image|max:2048'
        ]);

        // Tentukan minggu otomatis
        $tanggal = Carbon::parse($request->tanggal);
        $startWeek = $tanggal->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endWeek   = $tanggal->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

        // Cari atau buat minggu baru
        $week = WwtpPerformanceWeek::firstOrCreate([
            'week_start' => $startWeek,
            'week_end'   => $endWeek,
        ]);

        // Cek apakah jenis untuk minggu ini sudah diinput
        $existing = WwtpPerformanceRecord::where('performance_week_id', $week->id)
            ->where('jenis', $request->jenis)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jenis ini sudah diinput untuk minggu tersebut.'
            ], 409);
        }

        // Upload foto (jika ada)
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('wwtp_performance', 'public');
        }

        // Simpan record
        $record = WwtpPerformanceRecord::create([
            'performance_week_id' => $week->id,
            'jenis' => $request->jenis,
            'tss'   => $request->tss,
            'cod'   => $request->cod,
            'foto'  => $fotoPath,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan.',
            'data' => $record->load('week')
        ]);
    }

    /**
     * Detail record (JSON)
     */
    public function show($id)
    {
        $data = WwtpPerformanceRecord::with('week')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Update data
     * Accept POST with _method=PUT for multipart/form-data
     */
    public function update(Request $request, $id)
    {
        $record = WwtpPerformanceRecord::findOrFail($id);

        $request->validate([
            'tss'  => 'required|numeric|min:0',
            'cod'  => 'required|numeric|min:0',
            'foto' => 'nullable|image|max:2048',
        ]);

        // Update data
        $record->tss = $request->tss;
        $record->cod = $request->cod;

        // Upload foto baru (hapus lama jika ada foto baru)
        if ($request->hasFile('foto')) {
            if ($record->foto && Storage::disk('public')->exists($record->foto)) {
                Storage::disk('public')->delete($record->foto);
            }
            $record->foto = $request->file('foto')->store('wwtp_performance', 'public');
        }

        $record->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbarui.',
            'data' => $record->load('week')
        ]);
    }

    /**
     * Hapus data
     */
    public function destroy($id)
    {
        $record = WwtpPerformanceRecord::findOrFail($id);

        // Hapus foto jika ada
        if ($record->foto && Storage::disk('public')->exists($record->foto)) {
            Storage::disk('public')->delete($record->foto);
        }

        $record->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus.'
        ]);
    }


    //////////////API STATISTIK WWTP/////////////////////
    public function getStatistics()
    {
        $totalRecords = WwtpPerformanceRecord::count();

        // Last update berdasarkan created_at atau updated_at
        $lastUpdate = WwtpPerformanceRecord::orderBy('created_at', 'desc')->first();

        // Current Week - ambil data dari week yang aktif
        $today = Carbon::now();
        $startWeek = $today->copy()->startOfWeek(Carbon::MONDAY);
        $endWeek   = $today->copy()->endOfWeek(Carbon::SUNDAY);

        $currentWeek = WwtpPerformanceWeek::where('week_start', '<=', $today)
            ->where('week_end', '>=', $today)
            ->first();

        $weeklyData = [];
        if ($currentWeek) {
            $weeklyData = WwtpPerformanceRecord::where('performance_week_id', $currentWeek->id)
                ->get()
                ->groupBy('jenis')
                ->map(function ($group) {
                    return [
                        'avg_tss' => round($group->avg('tss'), 2),
                        'avg_cod' => round($group->avg('cod'), 2),
                        'count'   => $group->count(),
                    ];
                });
        }

        // Current Month - berdasarkan created_at
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth   = Carbon::now()->endOfMonth();

        $monthlyData = WwtpPerformanceRecord::whereBetween('created_at', [$startMonth, $endMonth])
            ->get()
            ->groupBy('jenis')
            ->map(function ($group) {
                return [
                    'avg_tss' => round($group->avg('tss'), 2),
                    'avg_cod' => round($group->avg('cod'), 2),
                ];
            });

        return response()->json([
            'total_records'      => $totalRecords,
            'last_update'        => $lastUpdate ? $lastUpdate->created_at : null,
            'weekly_summary'     => $weeklyData,
            'monthly_summary'    => $monthlyData,
        ]);
    }

    /**
     * Get chart data for specific jenis and period
     * Data diambil berdasarkan created_at dan dikelompokkan per week
     */
    public function getChartData($jenis, $period = 30)
    {
        $startDate = Carbon::now()->subDays($period);

        // Ambil records berdasarkan jenis dan periode
        $records = WwtpPerformanceRecord::with('week')
        ->where('jenis', $jenis)
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'asc')
            ->get();

        // Format data untuk chart
        $data = $records->map(function ($record) {
            return [
                'tanggal' => $record->created_at->format('Y-m-d'), // gunakan created_at
                'week_start' => $record->week ? $record->week->week_start : null,
                'week_end' => $record->week ? $record->week->week_end : null,
                'tss'     => (float) $record->tss,
                'cod'     => (float) $record->cod,
            ];
        });

        return response()->json($data);
    }


    /**
     * Get monthly comparison for last 6 months
     */
    public function getMonthlyComparison()
    {
        $months = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startMonth = $date->copy()->startOfMonth();
            $endMonth   = $date->copy()->endOfMonth();

            // Ambil semua records di bulan tersebut berdasarkan created_at
            $monthlyData = WwtpPerformanceRecord::whereBetween('created_at', [$startMonth, $endMonth])
                ->get()
                ->groupBy('jenis')
                ->map(function ($group) {
                    return [
                        'avg_tss' => round($group->avg('tss'), 2),
                        'avg_cod' => round($group->avg('cod'), 2),
                    ];
                });

            $months[] = [
                'month' => $date->format('M Y'),
                'data'  => $monthlyData,
            ];
        }

        return response()->json($months);
    }

    /**
     * Get recent records
     */
    public function getRecentRecords($limit = 10)
    {
        $data = WwtpPerformanceRecord::with('week')
        ->orderBy('created_at', 'desc')
        ->limit($limit)
            ->get();

        return response()->json($data);
    }

    /**
     * Get weekly performance
     */
    public function getWeeklyPerformance()
    {
        $weeks = WwtpPerformanceWeek::with('records')
        ->orderBy('week_start', 'desc')
        ->limit(10)
            ->get();

        return response()->json($weeks);
    }
}
