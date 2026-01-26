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

   
}
