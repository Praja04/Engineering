<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\WwtpRecord;
use App\Models\Utility\WwtpInfluent;
use App\Models\Utility\WwtpEffluent;
use App\Models\Utility\WwtpInfluentHarian;
use Carbon\Carbon;

class WWTPControllerProses extends Controller
{
    //
    public function proses()
    {
        return view('utility.wwtp.proses');
    }

    //form wwtp proses

    public function form_proses()
    {
        return view('utility.wwtp.form_proses');
    }

    //data wwtp proses
    public function data_proses()
    {
        return view('utility.wwtp.data_proses');
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
            'pit_produksi_step3' => 'nullable|numeric|min:0',
            'pit_storage' => 'nullable|numeric|min:0',
            'pit_proses_wwtp2' => 'nullable|numeric|min:0',
            'pit_outlet' => 'nullable|numeric|min:0',
            'pit_boiler' => 'nullable|numeric|min:0',
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
            'pit_produksi_step3' => $request->pit_produksi_step3,
            'pit_storage' => $request->pit_storage,
            'pit_proses_wwtp2' => $request->pit_proses_wwtp2,
            'pit_outlet' => $request->pit_outlet,
            'pit_boiler' => $request->pit_boiler,
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
     * Simpan data kategori Influent harian
     */


    public function storeinfluentHarian(Request $request)
    {
        $request->validate([
            'tanggal'  => 'required|date',
            'shift'    => 'required|in:shift1,shift2,shift3',
            'pit_sparta'   => 'required|numeric|min:0',
            'pit_garam'    => 'required|numeric|min:0',
            'pit_domestik' => 'required|numeric|min:0',
            'pit_produksi_step3' => 'nullable|numeric|min:0',
            'pit_storage' => 'nullable|numeric|min:0',
            'pit_proses_wwtp2' => 'nullable|numeric|min:0',
            'pit_outlet' => 'nullable|numeric|min:0',
            'pit_boiler' => 'nullable|numeric|min:0',
            'debit1' => 'nullable|numeric|min:0',
            'running_wwtp1' => 'nullable|string',
            'debit2' => 'nullable|numeric|min:0',
            'running_wwtp2' => 'nullable|string',

        ]);

        // Cek apakah shift pada tanggal tersebut sudah ada
        $existing = WwtpInfluentHarian::where('tanggal', $request->tanggal)
            ->where('shift', $request->shift)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Data untuk shift ini pada tanggal tersebut sudah ada. Setiap tanggal hanya boleh memiliki maksimal 3 shift (shift1, shift2, shift3).'
            ], 409);
        }

        // Cek jumlah shift pada tanggal tersebut (maksimal 3)
        $shiftCount = WwtpInfluentHarian::where('tanggal', $request->tanggal)->count();

        if ($shiftCount >= 3) {
            return response()->json([
                'message' => 'Tanggal ini sudah memiliki 3 shift. Tidak dapat menambah data lagi.'
            ], 409);
        }

        // Simpan data harian
        $harian = WwtpInfluentHarian::create([
            'tanggal' => $request->tanggal,
            'shift'   => $request->shift,
            'pit_sparta'     => $request->pit_sparta,
            'pit_garam'      => $request->pit_garam,
            'pit_domestik'   => $request->pit_domestik,
            'pit_produksi_step3' => $request->pit_produksi_step3,
            'pit_storage' => $request->pit_storage,
            'pit_proses_wwtp2' => $request->pit_proses_wwtp2,
            'pit_outlet' => $request->pit_outlet,
            'pit_boiler' => $request->pit_boiler,
            'debit1' => $request->debit1,
            'running_wwtp1' => $request->running_wwtp1,
            'debit2' => $request->debit2,
            'running_wwtp2' => $request->running_wwtp2,
        ]);

        return response()->json([
            'message' => 'Data influent harian berhasil disimpan.',
            'data'    => $harian
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



    //harian
    /**
     * Menampilkan semua data harian
     */
    public function indexHarian()
    {
        $data = WwtpInfluentHarian::orderBy('tanggal', 'desc')
            ->orderBy('shift', 'asc')
            ->get();

        return response()->json($data);
    }

    /**
     * Menampilkan detail data harian
     */
    public function showHarian($id)
    {
        $data = WwtpInfluentHarian::findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update data harian
     */
    public function updateHarian(Request $request, $id)
    {
        $harian = WwtpInfluentHarian::findOrFail($id);

        $request->validate([
            'tanggal'  => 'required|date',
            'shift'    => 'required|in:shift1,shift2,shift3',
            'pit_sparta'   => 'required|numeric|min:0',
            'pit_garam'    => 'required|numeric|min:0',
            'pit_domestik' => 'required|numeric|min:0',
            'pit_produksi_step3' => 'nullable|numeric|min:0',
            'pit_storage' => 'nullable|numeric|min:0',
            'pit_proses_wwtp2' => 'nullable|numeric|min:0',
            'pit_outlet' => 'nullable|numeric|min:0',
            'pit_boiler' => 'nullable|numeric|min:0',
            'debit1' => 'nullable|numeric|min:0',
            'running_wwtp1' => 'nullable|string',
            'debit2' => 'nullable|numeric|min:0',
            'running_wwtp2' => 'nullable|string',
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
    public function destroyHarian($id)
    {
        $harian = WwtpInfluentHarian::findOrFail($id);
        $harian->delete();

        return response()->json(['message' => 'Data harian berhasil dihapus.']);
    }
}
