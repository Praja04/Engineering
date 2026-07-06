<?php

namespace App\Http\Controllers\Kalibrasi\Master;

use App\Http\Controllers\Controller;
use App\Models\Kalibrasi\JangkaSorong\CalJangkaSorongMasterModel;
use Illuminate\Http\Request;

class KalibrasiMasterJangkaSorongController extends Controller
{
    public function viewJangkaSorong()
    {
        return view('kalibrasi.master.jangka_sorong');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // 'no' => 'required|unique:cal_jangka_sorong_master,no',
            'nilai_master' => 'required|numeric',
        ]);

        CalJangkaSorongMasterModel::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan.'
        ]);
    }

    public function index()
    {
        if (request()->ajax()) {
            return CalJangkaSorongMasterModel::all();
        }
    }

    public function show($id)
    {
        return CalJangkaSorongMasterModel::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            // 'no' => 'required|unique:cal_jangka_sorong_master,no,' . $id,
            'nilai_master' => 'required|numeric',
        ]);

        $data = CalJangkaSorongMasterModel::findOrFail($id);
        $data->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
    }


    public function destroy($id)
    {
        CalJangkaSorongMasterModel::findOrFail($id)->delete();
        return response()->json(['status' => 'success']);
    }
}
