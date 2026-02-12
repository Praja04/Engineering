<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\WwtpPerformanceWeek;
use App\Models\Utility\WwtpPerformanceRecord;
use App\Models\Utility\WwtpPerformancePHharian;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class WWTPControllerPerformance extends Controller
{
    public function performance()
    {
        return view('utility.wwtp.performance');
    }

    public function form_performance()
    {
        return view('utility.wwtp.form_performance');
    }

    public function data_performance()
    {
        return view('utility.wwtp.data_performance');
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


    //////////////////PH harian/////////////////////
    public function indexPHHarian()
    {
        $data = WwtpPerformancePHharian::orderBy('tanggal', 'desc')
            ->orderBy('shift', 'asc')
            ->get();

        return response()->json($data);
    }

    /**
     * Simpan data PH harian
     */
    public function storePHHarian(Request $request)
    {
        $request->validate([
            'tanggal'  => 'required|date',
            'shift'    => 'required|in:shift1,shift2,shift3',
            'equalisasi_1'   => 'nullable|numeric',
            'equalisasi_2'   => 'nullable|numeric',
            'netralisasi'    => 'nullable|numeric',
            'sedimentasi_1'  => 'nullable|numeric',
            'sedimentasi_2'  => 'nullable|numeric',
            'outlet_anaerob' => 'nullable|numeric',
            'aerob'          => 'nullable|numeric',
            'lumpur_aktif'   => 'nullable|numeric',
            'clarifier_2'    => 'nullable|numeric',
            'outlet'         => 'nullable|numeric',
        ]);

        // Cek apakah shift pada tanggal tersebut sudah ada
        $existing = WwtpPerformancePHharian::where('tanggal', $request->tanggal)
            ->where('shift', $request->shift)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Data PH untuk shift ini pada tanggal tersebut sudah ada. Setiap tanggal hanya boleh memiliki maksimal 3 shift (shift1, shift2, shift3).'
            ], 409);
        }

        // Cek jumlah shift pada tanggal tersebut (maksimal 3)
        $shiftCount = WwtpPerformancePHharian::where('tanggal', $request->tanggal)->count();

        if ($shiftCount >= 3) {
            return response()->json([
                'message' => 'Tanggal ini sudah memiliki 3 shift. Tidak dapat menambah data lagi.'
            ], 409);
        }

        // Simpan data PH harian
        $phHarian = WwtpPerformancePHharian::create([
            'tanggal'        => $request->tanggal,
            'shift'          => $request->shift,
            'equalisasi_1'   => $request->equalisasi_1,
            'equalisasi_2'   => $request->equalisasi_2,
            'netralisasi'    => $request->netralisasi,
            'sedimentasi_1'  => $request->sedimentasi_1,
            'sedimentasi_2'  => $request->sedimentasi_2,
            'outlet_anaerob' => $request->outlet_anaerob,
            'aerob'          => $request->aerob,
            'lumpur_aktif'   => $request->lumpur_aktif,
            'clarifier_2'    => $request->clarifier_2,
            'outlet'         => $request->outlet,
        ]);

        return response()->json([
            'message' => 'Data PH harian berhasil disimpan.',
            'data'    => $phHarian
        ]);
    }

    /**
     * Menampilkan detail data PH harian
     */
    public function showPHHarian($id)
    {
        $data = WwtpPerformancePHharian::findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update data PH harian
     */
    public function updatePHHarian(Request $request, $id)
    {
        $phHarian = WwtpPerformancePHharian::findOrFail($id);

        $request->validate([
            'tanggal'  => 'required|date',
            'shift'    => 'required|in:shift1,shift2,shift3',
            'equalisasi_1'   => 'nullable|numeric',
            'equalisasi_2'   => 'nullable|numeric',
            'netralisasi'    => 'nullable|numeric',
            'sedimentasi_1'  => 'nullable|numeric',
            'sedimentasi_2'  => 'nullable|numeric',
            'outlet_anaerob' => 'nullable|numeric',
            'aerob'          => 'nullable|numeric',
            'lumpur_aktif'   => 'nullable|numeric',
            'clarifier_2'    => 'nullable|numeric',
            'outlet'         => 'nullable|numeric',
        ]);

        // Cek apakah shift pada tanggal tersebut sudah ada (kecuali data yang sedang diupdate)
        $existing = WwtpPerformancePHharian::where('tanggal', $request->tanggal)
            ->where('shift', $request->shift)
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Data PH untuk shift ini pada tanggal tersebut sudah ada. Setiap tanggal hanya boleh memiliki maksimal 3 shift (shift1, shift2, shift3).'
            ], 409);
        }

        $phHarian->update($request->all());

        return response()->json([
            'message' => 'Data PH harian berhasil diperbarui.',
            'data' => $phHarian
        ]);
    }

    /**
     * Hapus data PH harian
     */
    public function destroyPHHarian($id)
    {
        $phHarian = WwtpPerformancePHharian::findOrFail($id);
        $phHarian->delete();

        return response()->json(['message' => 'Data PH harian berhasil dihapus.']);
    }

    //////////////API STATISTIK WWTP/////////////////////
    public function getStatistics()
    {
        $totalRecords = WwtpPerformanceRecord::count();

        // Last update berdasarkan created_at
        $lastUpdate = WwtpPerformanceRecord::orderBy('created_at', 'desc')->first();

        // Current Week - ambil data dari week yang aktif
        $today = Carbon::now();

        $currentWeek = WwtpPerformanceWeek::where('week_start', '<=', $today)
            ->where('week_end', '>=', $today)
            ->first();

        $weeklyData = [];
        $totalRecordsThisWeek = 0;

        if ($currentWeek) {
            $weekRecords = WwtpPerformanceRecord::where('performance_week_id', $currentWeek->id)->get();

            $totalRecordsThisWeek = $weekRecords->count();

            $weeklyData = $weekRecords
                ->groupBy('jenis')
                ->map(function ($group) {
                    return [
                        'avg_tss' => round($group->avg('tss'), 2),
                        'avg_cod' => round($group->avg('cod'), 2),
                        'count'   => $group->count(),
                    ];
                })
                ->toArray(); // 🔧 Convert to array untuk consistent JSON
        }

        // Current Month - berdasarkan created_at
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth   = Carbon::now()->endOfMonth();

        $monthlyRecords = WwtpPerformanceRecord::whereBetween('created_at', [$startMonth, $endMonth])->get();

        $monthlyData = $monthlyRecords
            ->groupBy('jenis')
            ->map(function ($group) {
                return [
                    'avg_tss' => round($group->avg('tss'), 2),
                    'avg_cod' => round($group->avg('cod'), 2),
                    'count'   => $group->count(),
                ];
            })
            ->toArray(); // 🔧 Convert to array

        return response()->json([
            'total_records'            => $totalRecords,
            'total_records_this_week'  => $totalRecordsThisWeek,
            'total_records_this_month' => $monthlyRecords->count(), // 🆕 Bonus: total month
            'last_update'              => $lastUpdate ? $lastUpdate->created_at : null,
            'weekly_summary'           => (object) $weeklyData, // 🔧 Force as object
            'monthly_summary'          => (object) $monthlyData, // 🔧 Force as object
        ]);
    }

    /**
     * Get chart data for specific jenis and period with date range
     * Data diambil berdasarkan created_at
     */
    public function getChartData(Request $request, $jenis)
    {
        // Default: start of current month to end of current month
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Ambil records berdasarkan jenis dan date range
        $records = WwtpPerformanceRecord::with('week')
            ->where('jenis', $jenis)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Format data untuk chart
        $data = $records->map(function ($record) {
            return [
                'tanggal' => $record->created_at->format('Y-m-d'),
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

    /**
     * Get PH harian chart data with date range filter
     */
    public function getChartDataHarian(Request $request)
    {
        // Default: start of current month to end of current month
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Aggregate by date (average of all shifts per day)
        $records = WwtpPerformancePHharian::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get()
            ->groupBy('tanggal')
            ->map(function ($dayRecords) {
                return [
                    'tanggal' => $dayRecords->first()->tanggal,
                    'equalisasi_1' => round($dayRecords->avg('equalisasi_1'), 2) ?? 0,
                    'equalisasi_2' => round($dayRecords->avg('equalisasi_2'), 2) ?? 0,
                    'netralisasi' => round($dayRecords->avg('netralisasi'), 2) ?? 0,
                    'sedimentasi_1' => round($dayRecords->avg('sedimentasi_1'), 2) ?? 0,
                    'sedimentasi_2' => round($dayRecords->avg('sedimentasi_2'), 2) ?? 0,
                    'outlet_anaerob' => round($dayRecords->avg('outlet_anaerob'), 2) ?? 0,
                    'aerob' => round($dayRecords->avg('aerob'), 2) ?? 0,
                    'lumpur_aktif' => round($dayRecords->avg('lumpur_aktif'), 2) ?? 0,
                    'clarifier_2' => round($dayRecords->avg('clarifier_2'), 2) ?? 0,
                    'outlet' => round($dayRecords->avg('outlet'), 2) ?? 0,
                    'shift_count' => $dayRecords->count(),
                ];
            })
            ->values();

        return response()->json($records);
    }

    /**
     * Get shift breakdown data for pie chart
     */
    public function getShiftBreakdownData(Request $request)
    {
        // Default: start of current month to end of current month
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $records = WwtpPerformancePHharian::whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy('shift')
            ->map(function ($group, $shift) {
                return [
                    'shift' => $shift,
                    'count' => $group->count(),
                ];
            })
            ->values();

        return response()->json($records);
    }

    /**
     * Get monthly comparison for PH harian (last 6 months)
     */
    public function getMonthlyComparisonHarian()
    {
        $months = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startMonth = $date->copy()->startOfMonth();
            $endMonth   = $date->copy()->endOfMonth();

            $monthlyRecords = WwtpPerformancePHharian::whereBetween('tanggal', [$startMonth, $endMonth])->get();

            $months[] = [
                'month' => $date->format('M Y'),
                'data' => [
                    'equalisasi_1' => round($monthlyRecords->avg('equalisasi_1'), 2) ?? 0,
                    'equalisasi_2' => round($monthlyRecords->avg('equalisasi_2'), 2) ?? 0,
                    'netralisasi' => round($monthlyRecords->avg('netralisasi'), 2) ?? 0,
                    'sedimentasi_1' => round($monthlyRecords->avg('sedimentasi_1'), 2) ?? 0,
                    'sedimentasi_2' => round($monthlyRecords->avg('sedimentasi_2'), 2) ?? 0,
                    'outlet_anaerob' => round($monthlyRecords->avg('outlet_anaerob'), 2) ?? 0,
                    'aerob' => round($monthlyRecords->avg('aerob'), 2) ?? 0,
                    'lumpur_aktif' => round($monthlyRecords->avg('lumpur_aktif'), 2) ?? 0,
                    'clarifier_2' => round($monthlyRecords->avg('clarifier_2'), 2) ?? 0,
                    'outlet' => round($monthlyRecords->avg('outlet'), 2) ?? 0,
                    'shift_count' => $monthlyRecords->count(),
                ],
            ];
        }

        return response()->json($months);
    }

    /**
     * Get recent PH harian records grouped by date
     */
    public function getRecentRecordsHarian($limit = 10)
    {
        // Get distinct dates
        $dates = WwtpPerformancePHharian::selectRaw('DISTINCT tanggal')
            ->orderBy('tanggal', 'desc')
            ->limit($limit)
            ->pluck('tanggal');

        $data = [];
        foreach ($dates as $date) {
            $shifts = WwtpPerformancePHharian::where('tanggal', $date)
                ->orderBy('shift', 'asc')
                ->get();

            $data[] = [
                'tanggal' => $date,
                'shift_count' => $shifts->count(),
                'shifts' => $shifts->map(function ($shift) {
                    return [
                        'shift' => $shift->shift,
                        'equalisasi_1' => $shift->equalisasi_1,
                        'equalisasi_2' => $shift->equalisasi_2,
                        'netralisasi' => $shift->netralisasi,
                        'sedimentasi_1' => $shift->sedimentasi_1,
                        'sedimentasi_2' => $shift->sedimentasi_2,
                        'outlet_anaerob' => $shift->outlet_anaerob,
                        'aerob' => $shift->aerob,
                        'lumpur_aktif' => $shift->lumpur_aktif,
                        'clarifier_2' => $shift->clarifier_2,
                        'outlet' => $shift->outlet,
                    ];
                }),
            ];
        }

        return response()->json($data);
    }
}
