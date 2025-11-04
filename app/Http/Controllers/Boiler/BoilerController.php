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
            'jenis_input' => 'required|in:weekly,monthly',
            'tanggal' => 'required|date',
            'batu_bara' => 'required|numeric|min:0',
            'steam' => 'required|numeric|min:0',
        ]);

        BoilerModel::create([
            'jenis_input' => $request->jenis_input,
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
        if ($request->jenis_input) {
            $query->where('jenis_input', $request->jenis_input);
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
            'jenis_input' => 'required|in:weekly,monthly',
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
            'jenis_input' => $request->jenis_input,
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
