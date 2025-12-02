<?php

namespace App\Http\Controllers\Utility;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Utility\KpiModel;
use App\Http\Controllers\Controller;

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
        // dd($request);
        $request->validate([
            'periode_tipe' => 'required|in:weekly,monthly',
            'finish_goods' => 'required|numeric|min:0',
            'kecap_matang' => 'required|numeric|min:0',
            'start_date'   => 'nullable|required_if:periode_tipe,weekly|date',
            'end_date'     => 'nullable|required_if:periode_tipe,weekly|date|after_or_equal:start_date',
            'month'        => 'nullable|required_if:periode_tipe,monthly|date_format:Y-m'
        ]);

        $jenis = $request->periode_tipe;

        if ($jenis === 'weekly') {

            $start = Carbon::parse($request->start_date);
            $end   = Carbon::parse($request->end_date);

            if ($start->diffInDays($end) < 6) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode weekly harus minimal 7 hari.'
                ], 422);
            }

            $exists = KpiModel::where('periode_tipe', 'weekly')
                ->where('start_date', $request->start_date)
                ->where('end_date', $request->end_date)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data KPI untuk periode weekly tersebut sudah ada.'
                ], 422);
            }
        }

        if ($jenis === 'monthly') {

            $exists = KpiModel::where('periode_tipe', 'monthly')
                ->where('month', $request->month)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data KPI untuk bulan tersebut sudah ada.'
                ], 422);
            }
        }

        KpiModel::create([
            'periode_tipe' => $jenis,
            'start_date'   => $jenis === 'weekly' ? $request->start_date : null,
            'end_date'     => $jenis === 'weekly' ? $request->end_date : null,
            'month'        => $jenis === 'monthly' ? $request->month : null,
            'finish_goods' => $request->finish_goods,
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
        // if ($request->tanggal) {
        //     $query->whereDate('tanggal', $request->tanggal);
        // }

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
            'finish_goods' => 'required|numeric|min:0',
            'kecap_matang' => 'required|numeric|min:0',
            'start_date'   => 'nullable|required_if:periode_tipe,weekly|date',
            'end_date'     => 'nullable|required_if:periode_tipe,weekly|date|after_or_equal:start_date',
            'month'        => 'nullable|required_if:periode_tipe,monthly|date_format:Y-m'
        ]);

        $kpi = KpiModel::find($id);

        if (!$kpi) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ], 404);
        }

        $jenis = $request->periode_tipe;

        $kpi->update([
            'periode_tipe' => $jenis,
            'start_date'   => $jenis === 'weekly' ? $request->start_date : null,
            'end_date'     => $jenis === 'weekly' ? $request->end_date : null,
            'month'        => $jenis === 'monthly' ? $request->month : null,
            'finish_goods' => $request->finish_goods,
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
