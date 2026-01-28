<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Maintenance\MtcBatteryModel;
use App\Http\Requests\Maintenance\MtcBatteryRequest;

class MtcBatteryController extends Controller
{
    public function index()
    {
        return view('maintenance.form.battery');
    }

    public function viewData()
    {
        return view('maintenance.data.battery_data');
    }

    public function store(MtcBatteryRequest $request)
    {
        DB::transaction(function () use ($request) {
            $battery = MtcBatteryModel::create([
                ...$request->validated(),
                'created_by' => Auth::id(),
            ]);

            $battery->details()->createMany($request->details);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data battery berhasil disimpan',
        ]);
    }

    public function getData(Request $request)
    {
        $query = MtcBatteryModel::query()
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->with('user:id,username', 'details');

        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        if ($request->filled('tipe_baterai')) {
            $query->where('battery_type', 'like', '%' . $request->tipe_baterai . '%');
        }

        if ($request->filled('unit')) {
            $query->where(function ($q) use ($request) {
                $q->where('no_unit', 'like', '%' . $request->unit . '%')
                    ->orWhere('no_seri', 'like', '%' . $request->unit . '%');
            });
        }

        // $perPage = (int) $request->get('per_page', 5);
        // $data = $query->paginate($perPage);

        $data = $query->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data Mtc Battery berhasil diambil',
            'data'    => $data,
        ]);
    }

    public function update(MtcBatteryRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $battery = MtcBatteryModel::findOrFail($id);

            $battery->update([
                ...$request->validated(),
                'updated_by' => Auth::id(),
            ]);

            foreach ($request->details as $detail) {
                $battery->details()->updateOrCreate(
                    ['id' => $detail['id'] ?? null],
                    $detail
                );
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data battery berhasil diperbarui',
        ]);
    }

    public function destroy($id)
    {
        $inspection = MtcBatteryModel::find($id);

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
