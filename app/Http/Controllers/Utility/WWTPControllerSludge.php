<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\WwtpSludge;

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



}
