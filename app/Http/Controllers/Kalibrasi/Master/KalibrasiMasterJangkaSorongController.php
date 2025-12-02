<?php

namespace App\Http\Controllers\Kalibrasi\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kalibrasi\Master\MasterJangkaSorongModel;

class KalibrasiMasterJangkaSorongController extends Controller
{
    public function viewJangkaSorong()
    {
        return view('kalibrasi.master.jangka_sorong');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no' => 'required|unique:kalibrasi_jangka_sorong_master,no',
            'nilai_master' => 'required|numeric',
        ]);

        MasterJangkaSorongModel::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan.'
        ]);
    }

    public function index()
    {
        if (request()->ajax()) {
            return MasterJangkaSorongModel::all();
        }
    }

    public function show($id)
    {
        return MasterJangkaSorongModel::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'no' => 'required|unique:kalibrasi_jangka_sorong_master,no,' . $id,
            'nilai_master' => 'required|numeric',
        ]);

        $data = MasterJangkaSorongModel::findOrFail($id);
        $data->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
    }


    public function destroy($id)
    {
        MasterJangkaSorongModel::findOrFail($id)->delete();
        return response()->json(['status' => 'success']);
    }
}
