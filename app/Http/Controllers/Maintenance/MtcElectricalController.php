<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Maintenance\MtcElectricalModel;
use App\Http\Requests\Maintenance\MtcElectricalRequest;

class MtcElectricalController extends Controller
{
    public function index()
    {
        return view('maintenance.form.electrical');
    }

    public function viewData()
    {
        return view('maintenance.data.electrical_data');
    }

    public function store(MtcElectricalRequest $request)
    {
        $data = $request->validated();

        $data['waktu'] = now()->format('H:i:s');
        $data['created_by'] = Auth::id() ?? 1;

        // Simpan record
        $inspection = MtcElectricalModel::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Data Mtc Electrical berhasil disimpan',
            'data'    => $inspection,
        ], 201);
    }

    public function getData(Request $request)
    {
        $query = MtcElectricalModel::query()
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->with('user:id,username');

        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }
        // if ($request->filled('start_date') && $request->filled('end_date')) {
        //     $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        // }

        if ($request->filled('paket')) {
            $query->where('paket', $request->paket);
        }

        if ($request->filled('nama_mesin')) {
            $query->where('nama_mesin', 'like', '%' . $request->nama_mesin . '%');
        }

        // $perPage = (int) $request->get('per_page', 5);
        // $data = $query->paginate($perPage);

        $data = $query->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data Mtc Electrical berhasil diambil',
            'data'    => $data,
        ]);
    }

    public function update(MtcElectricalRequest $request, $id)
    {
        $row = MtcElectricalModel::findOrFail($id);
        $row->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diupdate',
            'data' => $row->fresh('user:id,username')
        ]);
    }

    public function destroy($id)
    {
        $inspection = MtcElectricalModel::find($id);

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
