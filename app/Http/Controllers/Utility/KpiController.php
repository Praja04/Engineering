<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\KpiModel;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function viewForm()
    {
        return view('kpi.form_kpi');
    }

    public function viewData()
    {
        return view('kpi.data_kpi');
    }

    public function store(Request $request)
    {
        $request->validate([
            'periode_tipe' => 'required|in:weekly,monthly',
            'tanggal' => 'required|date',
            'fg' => 'required|numeric|min:0',
            'kecap_matang' => 'required|numeric|min:0',
        ]);

        $jenis = $request->periode_tipe;
        $tanggal = $request->tanggal;

        $weekOfMonth = $jenis === 'weekly'
            ? ceil(date('d', strtotime($tanggal)) / 7)
            : null;

        $exists = KpiModel::where('periode_tipe', $jenis)
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

        KpiModel::create([
            'periode_tipe' => $request->periode_tipe,
            'week' => $weekOfMonth,
            'tanggal' => $request->tanggal,
            'fg' => $request->fg,
            'kecap_matang' => $request->kecap_matang,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan!'
        ]);
    }

    public function getData(Request $request)
    {
        $query = KpiModel::query();

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
        $kpi = KpiModel::find($id);

        if (!$kpi) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json(['success' => true, 'data' => $kpi]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'periode_tipe' => 'required|in:weekly,monthly',
            'tanggal' => 'required|date',
            'fg' => 'required|numeric|min:0',
            'kecap_matang' => 'required|numeric|min:0',
        ]);

        $kpi = KpiModel::find($id);

        if (!$kpi) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ], 404);
        }

        $kpi->update([
            'periode_tipe' => $request->periode_tipe,
            'tanggal' => $request->tanggal,
            'fg' => $request->fg,
            'kecap_matang' => $request->kecap_matang,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $kpi = KpiModel::find($id);

        if (!$kpi) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ], 404);
        }

        $kpi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus!'
        ]);
    }
}
