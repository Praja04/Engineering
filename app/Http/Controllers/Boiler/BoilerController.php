<?php

namespace App\Http\Controllers\Boiler;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Boiler\BoilerModel;
use App\Http\Controllers\Controller;

class BoilerController extends Controller
{
    public function viewForm()
    {
        return view('boiler.form');
    }

    // public function viewData()
    // {
    //     return view('boiler.data');
    // }

    public function store(Request $request)
    {

        $request->validate([
            // 'periode_tipe' => 'required|in:weekly,monthly',
            'batu_bara' => 'required|numeric|min:0',
            'steam' => 'required|numeric|min:0',
            'date'   => 'required|date',
            // 'end_date'     => 'required_if:periode_tipe,weekly|date|after_or_equal:start_date',
            // 'month'        => 'required_if:periode_tipe,monthly|date_format:Y-m'
        ]);

        $date = $request->date;

        $exists = BoilerModel::where('date', $date)->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Data Boiler untuk hari ini sudah ada.'
            ], 422);
        }

        BoilerModel::create([
            'date'        => $request->date,
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

        // Filter dinamis tanggal (tetap sama seperti sebelumnya)
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        } else {
            $start = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end   = Carbon::now()->endOfMonth()->format('Y-m-d');

            $query->whereBetween('date', [$start, $end]);
        }

        $query->orderBy('date', 'desc');

        // Pagination
        $perPage = $request->input('per_page', 10);
        $perPage = min(max($perPage, 5), 100);

        $data = $query->paginate($perPage);

        $data->appends($request->query());

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
            // 'periode_tipe' => 'required|in:weekly,monthly',
            'batu_bara' => 'required|numeric|min:0',
            'steam' => 'required|numeric|min:0',
            'date'   => 'required|date',
            // 'end_date'     => 'required_if:periode_tipe,weekly|date|after_or_equal:start_date',
            // 'month'        => 'required_if:periode_tipe,monthly|date_format:Y-m'
        ]);

        $boiler = BoilerModel::find($id);

        if (!$boiler) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ], 404);
        }
        // $jenis = $request->periode_tipe;

        $boiler->update([
            // 'periode_tipe' => $jenis,
            // 'start_date'   => $jenis === 'weekly' ? $request->start_date : null,
            // 'end_date'     => $jenis === 'weekly' ? $request->end_date : null,
            'date'         => $request->date,
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
