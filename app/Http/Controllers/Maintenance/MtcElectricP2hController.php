<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Maintenance\MtcElectricP2hRequest;
use App\Models\Maintenance\MtcElectricP2hInspectionModel;
use App\Models\Maintenance\MtcElectricP2hItemModel;

class MtcElectricP2hController extends Controller
{
    public function index()
    {
        $items = MtcElectricP2hItemModel::where('aktif', true)->orderBy('urutan')->get();

        return view('maintenance.form.electric_p2h', compact('items'));
    }

    public function viewData()
    {
        return view('maintenance.data.electric_p2h_data');
    }

    public function store(MtcElectricP2hRequest $request)
    {
        DB::transaction(function () use ($request) {

            $inspection = MtcElectricP2hInspectionModel::create([
                'tanggal'     => $request->tanggal,
                'no_unit'     => $request->no_unit,
                'departemen'  => $request->departemen,
                'shift'       => $request->shift,
                'created_by'  => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                $inspection->details()->create([
                    'item_id'    => $item['item_id'],
                    'kondisi'    => $item['kondisi'] ?? null,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data P2H Electric berhasil disimpan',
        ]);
    }

    public function getData(Request $request)
    {
        $query = MtcElectricP2hInspectionModel::query()
            ->with([
                'createdBy:id,username',
                'details.item:id,item_pengecekan,kondisi_normal'
            ])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('no_unit')) {
            $query->where('no_unit', 'like', '%' . $request->no_unit . '%');
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('departemen')) {
            $query->where('departemen', 'like', '%' . $request->departemen . '%');
        }

        // $perPage = (int) $request->get('per_page', 5);
        // $data = $query->paginate($perPage);

        $data = $query->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data P2H Electric berhasil diambil',
            'data'    => $data,
        ]);
    }

    public function update(MtcElectricP2hRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {

            $inspection = MtcElectricP2hInspectionModel::with('details')
                ->findOrFail($id);

            $inspection->update([
                'tanggal'     => $request->tanggal,
                'no_unit'     => $request->no_unit,
                'departemen'  => $request->departemen,
                'shift'       => $request->shift,
                'updated_by'  => Auth::id(),
            ]);

            // Ambil item_id yang dikirim
            $itemIds = collect($request->items)->pluck('item_id')->toArray();

            // Hapus detail lama yang tidak dikirim (optional tapi rapi)
            $inspection->details()
                ->whereNotIn('item_id', $itemIds)
                ->delete();

            foreach ($request->items as $item) {

                $inspection->details()->updateOrCreate(
                    [
                        'inspection_id' => $inspection->id,
                        'item_id'       => $item['item_id'],
                    ],
                    [
                        'kondisi'    => $item['kondisi'] ?? null,
                        'keterangan' => $item['keterangan'] ?? null,
                    ]
                );
            }
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Data P2H Electric berhasil diperbarui',
        ]);
    }


    public function destroy($id)
    {
        $inspection = MtcElectricP2hInspectionModel::find($id);

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
