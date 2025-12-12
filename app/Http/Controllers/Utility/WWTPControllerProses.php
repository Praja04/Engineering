<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\WwtpRecord;
use App\Models\Utility\WwtpInfluent;
use App\Models\Utility\WwtpEffluent;
use Carbon\Carbon;

class WWTPControllerProses extends Controller
{
    //
    public function proses()
    {
        return view('utility.wwtp.proses');
    }

   
    /**
     * Menampilkan semua record WWTP
     */

    public function index()
    {
        $data = WwtpRecord::with(['influent', 'effluent'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json($data);
    }

    /**
     * Menyimpan data WWTP (influent / effluent)
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'  => 'required|date',
            'kategori' => 'required|in:influent,effluent',
        ]);

        if ($request->kategori === 'influent') {
            return $this->storeInfluent($request);
        } else {
            return $this->storeEffluent($request);
        }
    }

    /**
     * Simpan data kategori INFLUENT
     */
    private function storeInfluent(Request $request)
    {
        $request->validate([
            'pit_sparta'   => 'required|numeric|min:0',
            'pit_garam'    => 'required|numeric|min:0',
            'pit_domestik' => 'required|numeric|min:0',
        ]);

        // Cek apakah minggu ini sudah ada data influent
        $tanggal = Carbon::parse($request->tanggal);
        $startWeek = $tanggal->copy()->startOfWeek();
        $endWeek   = $tanggal->copy()->endOfWeek();

        $existing = WwtpRecord::where('kategori', 'influent')
            ->whereBetween('tanggal', [$startWeek, $endWeek])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Input influent minggu ini sudah ada.'
            ], 409);
        }

        // Simpan header
        $record = WwtpRecord::create([
            'tanggal'  => $request->tanggal,
            'kategori' => 'influent',
        ]);

        // Simpan detail
        WwtpInfluent::create([
            'wwtp_record_id' => $record->id,
            'pit_sparta'     => $request->pit_sparta,
            'pit_garam'      => $request->pit_garam,
            'pit_domestik'   => $request->pit_domestik,
        ]);

        return response()->json([
            'message' => 'Data influent berhasil disimpan.',
            'data'    => $record->load('influent')
        ]);
    }

    /**
     * Simpan data kategori EFFLUENT
     */
    private function storeEffluent(Request $request)
    {
        $request->validate([
            'full_proses' => 'required|numeric|min:0',
            'daf_pre'     => 'required|numeric|min:0',
        ]);

        // Buat header
        $record = WwtpRecord::create([
            'tanggal'  => $request->tanggal,
            'kategori' => 'effluent',
        ]);

        // Simpan detail
        WwtpEffluent::create([
            'wwtp_record_id' => $record->id,
            'full_proses'    => $request->full_proses,
            'daf_pre'        => $request->daf_pre,
        ]);

        return response()->json([
            'message' => 'Data effluent berhasil disimpan.',
            'data'    => $record->load('effluent')
        ]);
    }

    /**
     * Menampilkan detail record
     */
    public function show($id)
    {
        $data = WwtpRecord::with(['influent', 'effluent'])->findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update record WWTP
     */
    public function update(Request $request, $id)
    {
        $record = WwtpRecord::with(['influent', 'effluent'])->findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
        ]);

        // Jika kategori influent → tetap jaga rule perminggu
        if ($record->kategori === 'influent') {  

            $request->validate([
                'pit_sparta'   => 'required|numeric|min:0',
                'pit_garam'    => 'required|numeric|min:0',
                'pit_domestik' => 'required|numeric|min:0',
            ]);

            // Cek apakah tanggal barunya masuk minggu yang sudah ada data lain
            $tanggal = Carbon::parse($request->tanggal);
            $startWeek = $tanggal->copy()->startOfWeek();
            $endWeek   = $tanggal->copy()->endOfWeek();

            $existing = WwtpRecord::where('kategori', 'influent')
                ->whereBetween('tanggal', [$startWeek, $endWeek])
                ->where('id', '!=', $id)  // abaikan data dirinya sendiri
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Input influent minggu ini sudah ada.'
                ], 409);
            }

            // Update header
            $record->update([
                'tanggal' => $request->tanggal,
            ]);

            // Update detail
            $record->influent->update([
                'pit_sparta'   => $request->pit_sparta,
                'pit_garam'    => $request->pit_garam,
                'pit_domestik' => $request->pit_domestik,
            ]);
        }

        // Jika kategori effluent
        elseif ($record->kategori === 'effluent') {

            $request->validate([
                'full_proses' => 'required|numeric|min:0',
                'daf_pre'     => 'required|numeric|min:0',
            ]);

            // Update header
            $record->update([
                'tanggal' => $request->tanggal,
            ]);

            // Update detail
            $record->effluent->update([
                'full_proses' => $request->full_proses,
                'daf_pre'     => $request->daf_pre,
            ]);
        }

        return response()->json([
            'message' => 'Data berhasil diperbarui.',
            'data' => $record->fresh(['influent', 'effluent'])
        ]);
    }

    /**
     * Hapus record
     */
    public function destroy($id)
    {
        $record = WwtpRecord::findOrFail($id);
        $record->delete();

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }

    /**
     * API: Get dashboard statistics
     */
    public function getStatistics()
    {
        $totalRecords = WwtpRecord::count();
        $totalInfluent = WwtpRecord::where('kategori', 'influent')->count();
        $totalEffluent = WwtpRecord::where('kategori', 'effluent')->count();

        $lastUpdate = WwtpRecord::orderBy('tanggal', 'desc')->first();

        // Current week data
        $startWeek = Carbon::now()->startOfWeek();
        $endWeek = Carbon::now()->endOfWeek();

        $weeklyInfluent = WwtpRecord::where('kategori', 'influent')
            ->whereBetween('tanggal', [$startWeek, $endWeek])
            ->with('influent')
            ->first();

        $weeklyEffluentCount = WwtpRecord::where('kategori', 'effluent')
            ->whereBetween('tanggal', [$startWeek, $endWeek])
            ->count();

        // Calculate averages for current month
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();

        $monthlyInfluentAvg = WwtpRecord::where('kategori', 'influent')
            ->whereBetween('tanggal', [$startMonth, $endMonth])
            ->with('influent')
            ->get()
            ->avg(function ($record) {
                if ($record->influent) {
                    return $record->influent->pit_sparta +
                        $record->influent->pit_garam +
                        $record->influent->pit_domestik;
                }
                return 0;
            });

        $monthlyEffluentAvg = WwtpRecord::where('kategori', 'effluent')
            ->whereBetween('tanggal', [$startMonth, $endMonth])
            ->with('effluent')
            ->get()
            ->avg(function ($record) {
                if ($record->effluent) {
                    return $record->effluent->full_proses +
                        $record->effluent->daf_pre;
                }
                return 0;
            });

        return response()->json([
            'total_records' => $totalRecords,
            'total_influent' => $totalInfluent,
            'total_effluent' => $totalEffluent,
            'last_update' => $lastUpdate ? $lastUpdate->tanggal : null,
            'weekly_influent' => $weeklyInfluent,
            'weekly_effluent_count' => $weeklyEffluentCount,
            'monthly_influent_avg' => round($monthlyInfluentAvg, 2),
            'monthly_effluent_avg' => round($monthlyEffluentAvg, 2),
        ]);
    }

    /**
     * API: Get chart data for influent
     */
    public function getInfluentChartData($period = 30)
    {
        $startDate = Carbon::now()->subDays($period);

        $data = WwtpRecord::where('kategori', 'influent')
            ->where('tanggal', '>=', $startDate)
            ->with('influent')
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($record) {
                return [
                    'tanggal' => $record->tanggal,
                    'pit_sparta' => $record->influent->pit_sparta ?? 0,
                    'pit_garam' => $record->influent->pit_garam ?? 0,
                    'pit_domestik' => $record->influent->pit_domestik ?? 0,
                    'total' => ($record->influent->pit_sparta ?? 0) +
                        ($record->influent->pit_garam ?? 0) +
                        ($record->influent->pit_domestik ?? 0),
                ];
            });

        return response()->json($data);
    }

    /**
     * API: Get chart data for effluent
     */
    public function getEffluentChartData($period = 30)
    {
        $startDate = Carbon::now()->subDays($period);

        $data = WwtpRecord::where('kategori', 'effluent')
            ->where('tanggal', '>=', $startDate)
            ->with('effluent')
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($record) {
                return [
                    'tanggal' => $record->tanggal,
                    'full_proses' => $record->effluent->full_proses ?? 0,
                    'daf_pre' => $record->effluent->daf_pre ?? 0,
                    'total' => ($record->effluent->full_proses ?? 0) +
                        ($record->effluent->daf_pre ?? 0),
                ];
            });

        return response()->json($data);
    }

    /**
     * API: Get monthly comparison
     */
    public function getMonthlyComparison()
    {
        $months = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startMonth = $date->copy()->startOfMonth();
            $endMonth = $date->copy()->endOfMonth();

            // Influent total
            $influentTotal = WwtpRecord::where('kategori', 'influent')
                ->whereBetween('tanggal', [$startMonth, $endMonth])
                ->with('influent')
                ->get()
                ->sum(function ($record) {
                    if ($record->influent) {
                        return $record->influent->pit_sparta +
                            $record->influent->pit_garam +
                            $record->influent->pit_domestik;
                    }
                    return 0;
                });

            // Effluent total
            $effluentTotal = WwtpRecord::where('kategori', 'effluent')
                ->whereBetween('tanggal', [$startMonth, $endMonth])
                ->with('effluent')
                ->get()
                ->sum(function ($record) {
                    if ($record->effluent) {
                        return $record->effluent->full_proses +
                            $record->effluent->daf_pre;
                    }
                    return 0;
                });

            $months[] = [
                'month' => $date->format('M Y'),
                'influent' => round($influentTotal, 2),
                'effluent' => round($effluentTotal, 2),
            ];
        }

        return response()->json($months);
    }

    /**
     * API: Get recent records
     */
    public function getRecentRecords($limit = 10)
    {
        $data = WwtpRecord::with(['influent', 'effluent'])
            ->orderBy('tanggal', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($data);
    }
}
