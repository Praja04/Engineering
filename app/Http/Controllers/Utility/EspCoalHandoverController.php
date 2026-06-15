<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\EspCoalHandover;
use Carbon\Carbon;

class EspCoalHandoverController extends Controller
{
    /**
     * 📊 GET DATA — JSON untuk tabel data
     */
    public function getData(Request $request)
    {
        $query = EspCoalHandover::query();

        if ($request->filled('tanggal')) {
            $query->where('tanggal_laporan', $request->tanggal);
        }

        return response()->json(
            $query->with('operator')->latest()->get()
        );
    }

    /**
     * 🧾 STORE (Save / Update)
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_laporan'    => 'required|date',
            'penyuplai_qty'      => 'required|numeric|min:0',
            'penyuplai_nik_nama' => 'required|string|max:255',
            'penerima_qty'       => 'required|numeric|min:0',
            'penerima_nik_nama'  => 'required|string|max:255',
        ]);

        $data = EspCoalHandover::updateOrCreate(
            [
                'tanggal_laporan' => $request->tanggal_laporan
            ],
            [
                'penyuplai_qty'      => $request->penyuplai_qty,
                'penyuplai_nik_nama' => $request->penyuplai_nik_nama,
                'penerima_qty'       => $request->penerima_qty,
                'penerima_nik_nama'  => $request->penerima_nik_nama,
                'operator_id'        => auth()->id(),
            ]
        );

        return response()->json([
            'message' => 'Data serah terima batu bara berhasil disimpan',
            'data'    => $data
        ]);
    }

    /**
     * ✏️ UPDATE — edit oleh non-operator
     */
    public function update(Request $request, $id)
    {
        $data = EspCoalHandover::findOrFail($id);

        $request->validate([
            'penyuplai_qty'      => 'required|numeric|min:0',
            'penyuplai_nik_nama' => 'required|string|max:255',
            'penerima_qty'       => 'required|numeric|min:0',
            'penerima_nik_nama'  => 'required|string|max:255',
        ]);

        $data->update([
            'penyuplai_qty'      => $request->penyuplai_qty,
            'penyuplai_nik_nama' => $request->penyuplai_nik_nama,
            'penerima_qty'       => $request->penerima_qty,
            'penerima_nik_nama'  => $request->penerima_nik_nama,
        ]);

        return response()->json([
            'message' => 'Data serah terima batu bara berhasil diperbarui',
            'data'    => $data
        ]);
    }
}
