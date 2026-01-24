<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Maintenance\MtcMotorPumpRequest;
use App\Models\Maintenance\MtcMotorPumpModel;

class MtcMotorPumpController extends Controller
{
    public function index()
    {
        return view('maintenance.form.motor_pump');
    }

    public function viewData()
    {
        return view('maintenance.data.motor_pump_data');
    }

    public function store(MtcMotorPumpRequest $request)
    {
        $data = $request->validated();

        $data['waktu'] = now()->format('H:i:s');
        $data['created_by'] = Auth::id() ?? 1;

        // Simpan record
        $inspection = MtcMotorPumpModel::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Data Mtc Motor Pump berhasil disimpan',
            'data'    => $inspection,
        ], 201);
    }

    public function getData(Request $request)
    {
        $query = MtcMotorPumpModel::query()
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
            'message' => 'Data Mtc Motor Pump berhasil diambil',
            'data'    => $data,
        ]);
    }

    public function update(MtcMotorPumpRequest $request, $id)
    {
        $row = MtcMotorPumpModel::findOrFail($id);
        $row->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diupdate',
            'data' => $row->fresh('user:id,username')
        ]);
    }

    public function destroy($id)
    {
        $inspection = MtcMotorPumpModel::find($id);

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
