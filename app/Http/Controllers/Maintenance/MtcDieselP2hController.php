<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Maintenance\MtcDieselP2hItemModel;
use App\Http\Requests\Maintenance\MtcDieselP2hRequest;
use App\Models\Maintenance\MtcDieselP2hInspectionModel;

class MtcDieselP2hController extends Controller
{
    public function index()
    {
        $items = MtcDieselP2hItemModel::where('aktif', true)->orderBy('urutan')->get();

        return view('maintenance.form.diesel_p2h', compact('items'));
    }

    public function viewData()
    {
        return view('maintenance.data.diesel_p2h_data');
    }

    public function store(MtcDieselP2hRequest $request)
    {
        DB::transaction(function () use ($request) {

            $inspection = MtcDieselP2hInspectionModel::create([
                'nama_mesin'  => $request->nama_mesin,
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
            'message' => 'Data P2H Diesel berhasil disimpan',
        ]);
    }

    public function getData(Request $request)
    {
        $query = MtcDieselP2hInspectionModel::query()
            ->with([
                'createdBy:id,username',
                'details.item:id,item_pengecekan,kondisi_normal'
            ])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
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
            'message' => 'Data P2H Diesel berhasil diambil',
            'data'    => $data,
        ]);
    }

    public function update(MtcDieselP2hRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {

            $inspection = MtcDieselP2hInspectionModel::with('details')
                ->findOrFail($id);

            $inspection->update([
                'nama_mesin'  => $request->nama_mesin,
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
        $inspection = MtcDieselP2hInspectionModel::find($id);

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
