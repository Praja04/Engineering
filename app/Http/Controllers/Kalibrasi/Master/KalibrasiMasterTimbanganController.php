<?php

namespace App\Http\Controllers\Kalibrasi\Master;

use App\Http\Controllers\Controller;
use App\Models\Kalibrasi\Master\MasterTimbanganModel;
use Illuminate\Http\Request;

class KalibrasiMasterTimbanganController extends Controller
{
    public function viewTimbangan()
    {
        return view('kalibrasi.master.timbangan');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'beban' => 'required|unique:kalibrasi_timbangan_master,beban',
            'standar_massa' => 'required|numeric',
        ]);

        MasterTimbanganModel::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan.'
        ]);
    }

    public function index()
    {
        if (request()->ajax()) {
            return MasterTimbanganModel::all();
        }
    }

    public function show($id)
    {
        return MasterTimbanganModel::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'beban' => 'required|unique:kalibrasi_timbangan_master,beban,' . $id,
            'standar_massa' => 'required|numeric',
        ]);

        $data = MasterTimbanganModel::findOrFail($id);
        $data->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
    }


    public function destroy($id)
    {
        MasterTimbanganModel::findOrFail($id)->delete();
        return response()->json(['status' => 'success']);
    }
}
