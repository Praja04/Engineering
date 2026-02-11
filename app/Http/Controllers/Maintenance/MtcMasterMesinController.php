<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Maintenance\MtcMasterMesinModel;
use App\Http\Requests\Maintenance\MtcMasterMesinRequest;

class MtcMasterMesinController extends Controller
{
    public function index()
    {
        return view('maintenance.master.master_mesin');
    }

    public function store(MtcMasterMesinRequest $request)
    {
        DB::transaction(function () use ($request) {
            MtcMasterMesinModel::create([
                'jenis_mtc'  => $request->jenis_mtc,
                'nama_mesin' => $request->nama_mesin,
                'lokasi'     => $request->lokasi,
                'frekuensi'  => $request->frekuensi ?? null,
                'aktif'      => 1,
                'created_by' => Auth::id(),
            ]);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data mesin berhasil disimpan'
        ]);
    }

    public function getData()
    {
        $data = MtcMasterMesinModel::orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function update(MtcMasterMesinRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $mesin = MtcMasterMesinModel::findOrFail($id);

            $mesin->update([
                'jenis_mtc'  => $request->jenis_mtc,
                'nama_mesin' => $request->nama_mesin,
                'lokasi'     => $request->lokasi,
                'frekuensi'  => $request->frekuensi,
                'aktif'      => $request->aktif,
                'updated_by' => Auth::id(),
            ]);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data mesin berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $mesin = MtcMasterMesinModel::findOrFail($id);
            $mesin->delete();
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data mesin berhasil dihapus'
        ]);
    }
}
