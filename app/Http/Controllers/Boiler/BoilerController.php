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
            'tanggal' => 'required|date',
            'batu_bara' => 'required|numeric|min:0',
            'steam' => 'required|numeric|min:0',
        ]);

        $jenis = $request->periode_tipe;
        // dd($jenis);
        $tanggal = $request->tanggal;

        $exists = BoilerModel::where('periode_tipe', $jenis)
            ->when($jenis === 'weekly', function ($query) use ($tanggal) {
                $query->whereRaw('YEAR(tanggal) = YEAR(?)', [$tanggal])
                    ->whereRaw('WEEK(tanggal, 1) = WEEK(?, 1)', [$tanggal]);
            })
            ->when($jenis === 'monthly', function ($query) use ($tanggal) {
                $query->whereRaw('YEAR(tanggal) = YEAR(?)', [$tanggal])
                    ->whereRaw('MONTH(tanggal) = MONTH(?)', [$tanggal]);
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

        $weekOfMonth = $jenis === 'weekly'
            ? ceil(date('d', strtotime($tanggal)) / 7)
            : null;

        BoilerModel::create([
            'periode_tipe' => $request->periode_tipe,
            'week' => $weekOfMonth,
            'tanggal' => $request->tanggal,
            'batu_bara' => $request->batu_bara,
            'steam' => $request->steam,
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
        if ($request->tanggal) {
            $query->whereDate('tanggal', $request->tanggal);
        }

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
            'tanggal' => 'required|date',
            'batu_bara' => 'required|numeric|min:0',
            'steam' => 'required|numeric|min:0',
        ]);

        $boiler = BoilerModel::find($id);

        if (!$boiler) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ], 404);
        }

        $boiler->update([
            'periode_tipe' => $request->periode_tipe,
            'tanggal' => $request->tanggal,
            'batu_bara' => $request->batu_bara,
            'steam' => $request->steam,
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
