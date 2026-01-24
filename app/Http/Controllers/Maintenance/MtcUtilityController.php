<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Maintenance\MtcUtilityModel;
use App\Http\Requests\Maintenance\MtcUtilityRequest;

class MtcUtilityController extends Controller
{
    public function index()
    {
        return view('maintenance.form.utility');
    }

    public function viewData()
    {
        return view('maintenance.data.utility_data');
    }

    public function store(MtcUtilityRequest $request)
    {
        $data = $request->validated();

        $data['waktu'] = now()->format('H:i:s');
        $data['created_by'] = Auth::id() ?? 1;

        // Simpan record
        $inspection = MtcUtilityModel::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Data Mtc Utility berhasil disimpan',
            'data'    => $inspection,
        ], 201);
    }

    public function getData(Request $request)
    {
        $query = MtcUtilityModel::query()
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->with('user:id,username');

        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        if ($request->filled('paket')) {
            $query->where('paket', $request->paket);
        }

        if ($request->filled('nama_mesin')) {
            $query->where('nama_mesin', 'like', '%' . $request->nama_mesin . '%');
        }

        $data = $query->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data Mtc Utility berhasil diambil',
            'data'    => $data,
        ]);
    }

    public function update(MtcUtilityRequest $request, $id)
    {
        $row = MtcUtilityModel::findOrFail($id);
        $row->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diupdate',
            'data' => $row->fresh('user:id,username')
        ]);
    }

    public function destroy($id)
    {
        $inspection = MtcUtilityModel::find($id);

        if (!$inspection) {
            return response()->json([
                'status'  => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        $inspection->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data berhasil dihapus',
        ]);
    }
}
