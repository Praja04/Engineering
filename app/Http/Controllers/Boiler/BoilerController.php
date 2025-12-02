<?php

namespace App\Http\Controllers\Boiler;

use App\Http\Controllers\Controller;
use App\Models\Boiler\BoilerModel;
use Illuminate\Http\Request;

class BoilerController extends Controller
{
    public function viewForm()
    {
        return view('boiler.form');
    }

    public function viewData()
    {
        return view('boiler.data');
    }

    public function store(Request $request)
    {

        $request->validate([
            'periode_tipe' => 'required|in:weekly,monthly',
            'batu_bara' => 'required|numeric|min:0',
            'steam' => 'required|numeric|min:0',
            'start_date'   => 'required_if:periode_tipe,weekly|date',
            'end_date'     => 'required_if:periode_tipe,weekly|date|after_or_equal:start_date',
            'month'        => 'required_if:periode_tipe,monthly|date_format:Y-m'
        ]);

        $jenis = $request->periode_tipe;

        // ===== CEK DUPLIKAT =====
        $exists = BoilerModel::where('periode_tipe', $jenis)
            ->when($jenis === 'weekly', function ($q) use ($request) {
                $q->where('start_date', $request->start_date);
            })
            ->when($jenis === 'monthly', function ($q) use ($request) {
                $q->where('month', $request->month);
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => $jenis === 'weekly'
                    ? 'Data KPI untuk minggu tersebut sudah ada.'
                    : 'Data KPI untuk bulan tersebut sudah ada.'
            ], 422);
        }

        BoilerModel::create([
            'periode_tipe' => $jenis,
            'start_date'   => $jenis === 'weekly' ? $request->start_date : null,
            'end_date'     => $jenis === 'weekly' ? $request->end_date : null,
            'month'        => $jenis === 'monthly' ? $request->month : null,
            'batu_bara'    => $request->batu_bara,
            'steam'        => $request->steam,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan!'
        ]);
    }

    public function getData(Request $request)
    {
        $query = BoilerModel::query();

        // filter dinamis
        if ($request->periode_tipe) {
            $query->where('periode_tipe', $request->periode_tipe);
        }

        // if ($request->tanggal) {
        //     $query->whereDate('tanggal', $request->tanggal);
        // }

        $data = $query->orderBy('created_at', 'desc')->get();
        return response()->json($data);
    }

    public function show($id)
    {
        $boiler = BoilerModel::find($id);

        if (!$boiler) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json(['success' => true, 'data' => $boiler]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'periode_tipe' => 'required|in:weekly,monthly',
            'batu_bara' => 'required|numeric|min:0',
            'steam' => 'required|numeric|min:0',
            'start_date'   => 'required_if:periode_tipe,weekly|date',
            'end_date'     => 'required_if:periode_tipe,weekly|date|after_or_equal:start_date',
            'month'        => 'required_if:periode_tipe,monthly|date_format:Y-m'
        ]);

        $boiler = BoilerModel::find($id);

        if (!$boiler) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ], 404);
        }
        $jenis = $request->periode_tipe;

        $boiler->update([
            'periode_tipe' => $jenis,
            'start_date'   => $jenis === 'weekly' ? $request->start_date : null,
            'end_date'     => $jenis === 'weekly' ? $request->end_date : null,
            'month'        => $jenis === 'monthly' ? $request->month : null,
            'batu_bara'    => $request->batu_bara,
            'steam'        => $request->steam,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $boiler = BoilerModel::find($id);

        if (!$boiler) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ], 404);
        }

        $boiler->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus!'
        ]);
    }
}
