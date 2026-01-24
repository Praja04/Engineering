<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Maintenance\MtcSipilRequest;
use App\Models\Maintenance\MtcSipilInspectionDetailModel;
use App\Models\Maintenance\MtcSipilInspectionModel;
use App\Models\Maintenance\MtcSipilItemModel;

class MtcSipilController extends Controller
{
    public function index()
    {
        $items = MtcSipilItemModel::where('aktif', true)->orderBy('urutan')->get();

        return view('maintenance.form.sipil', compact('items'));
    }

    public function viewData()
    {
        return view('maintenance.data.sipil_data');
    }

    public function store(MtcSipilRequest $request)
    {
        $payload = $request->validated();

        return DB::transaction(function () use ($payload, $request) {

            // HEADER
            $inspection = MtcSipilInspectionModel::create([
                'tanggal'     => $payload['tanggal'],
                'waktu'       => $payload['waktu'] ?? now()->format('H:i:s'),
                'area'        => $payload['area'] ?? null,
                'rekomendasi' => $payload['rekomendasi'] ?? null,
                'korektif' => $payload['korektif'] ?? null,
                'created_by'  => Auth::id() ?? 1,
            ]);

            $rows = [];
            foreach ($payload['details'] as $d) {
                if (!array_key_exists('kondisi', $d) || $d['kondisi'] === null || $d['kondisi'] === '') {
                    continue;
                }

                $rows[] = [
                    'inspection_id' => $inspection->id,
                    'item_id'       => (int) $d['item_id'],
                    'kondisi'       => filter_var($d['kondisi'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                    'keterangan'    => $d['keterangan'] ?? null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }

            if (empty($rows)) {
                throw new \Exception('Tidak ada data kondisi yang valid untuk disimpan');
            }

            MtcSipilInspectionDetailModel::insert($rows);

            // return JSON
            return response()->json([
                'status'  => true,
                'message' => 'Data inspeksi sipil berhasil disimpan',
                'data'    => $inspection->load(['details.item']),
            ], 201);
        });
    }

    public function getData(Request $request)
    {
        $query = MtcSipilInspectionModel::query()
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->with([
                'user:id,username',
                'details.item'
            ]);

        // Filter tanggal (jika ada parameter date)
        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        // Filter area (partial match)
        if ($request->filled('area')) {
            $query->where('area', 'like', '%' . $request->area . '%');
        }

        // Filter rekomendasi (partial match)
        if ($request->filled('rekomendasi')) {
            $query->where('rekomendasi', 'like', '%' . $request->rekomendasi . '%');
        }

        // Filter korektif (partial match)
        if ($request->filled('korektif')) {
            $query->where('korektif', 'like', '%' . $request->korektif . '%');
        }

        $data = $query->get();

        // Optional: transform data supaya lebih rapi di frontend (jika perlu)
        $formatted = $data->map(function ($inspection) {
            return [
                'id'           => $inspection->id,
                'tanggal'      => $inspection->tanggal,
                'waktu'        => $inspection->waktu ? $inspection->waktu->format('H:i') : null,
                'area'         => $inspection->area,
                'rekomendasi'  => $inspection->rekomendasi,
                'korektif'     => $inspection->korektif,
                'created_by'   => $inspection->user?->username ?? 'Unknown',
                'details'      => $inspection->details->map(function ($detail) {
                    return [
                        'item_id'              => $detail->item_id,
                        'jenis_perawatan'      => $detail->item->jenis_perawatan ?? null,
                        'standar_pemeliharaan' => $detail->item->standar_pemeliharaan ?? null,
                        'kondisi'              => $detail->kondisi,           // true = YA, false = TIDAK
                        'keterangan'           => $detail->keterangan,
                    ];
                }),
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data Mtc Sipil berhasil diambil',
            'data'    => $formatted,
            // 'raw'     => $data, // optional: kalau frontend butuh raw data
        ]);
    }

    public function update(MtcSipilRequest $request, $id)
    {
        $payload = $request->validated();

        return DB::transaction(function () use ($payload, $id, $request) {

            $inspection = MtcSipilInspectionModel::findOrFail($id);

            // Update header
            $inspection->update([
                'tanggal'     => $payload['tanggal'],
                'waktu'       => $payload['waktu'] ?? $inspection->waktu ?? now()->format('H:i:s'),
                'area'        => $payload['area'] ?? $inspection->area,
                'rekomendasi' => $payload['rekomendasi'] ?? $inspection->rekomendasi,
                'korektif'    => $payload['korektif'] ?? $inspection->korektif,
                'updated_by'  => Auth::id() ?? 1,
            ]);

            // Hapus detail lama (soft-delete atau hard-delete)
            // Kalau pakai soft-delete → $inspection->details()->delete();
            // Kalau hard-delete (seperti store-mu) → hapus permanen
            $inspection->details()->delete();

            $rows = [];
            foreach ($payload['details'] ?? [] as $key => $d) {
                if (!isset($d['item_id']) || !is_numeric($d['item_id'])) {
                    continue; // skip kalau item_id hilang
                }

                if (!array_key_exists('kondisi', $d) || $d['kondisi'] === null || $d['kondisi'] === '') {
                    continue;
                }

                $rows[] = [
                    'inspection_id' => $inspection->id,
                    'item_id'       => (int) $d['item_id'],
                    'kondisi'       => filter_var($d['kondisi'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                    'keterangan'    => $d['keterangan'] ?? null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }

            if (!empty($rows)) {
                MtcSipilInspectionDetailModel::insert($rows);
            }

            // Reload dengan relasi
            $inspection->refresh();
            $inspection->load(['details.item']);

            return response()->json([
                'status'  => true,
                'message' => 'Data inspeksi sipil berhasil diupdate',
                'data'    => $inspection,
            ]);
        });
    }

    public function destroy($id)
    {
        $inspection = MtcSipilInspectionModel::findOrFail($id);

        return DB::transaction(function () use ($inspection) {

            // Hapus detail dulu
            $inspection->details()->delete();

            // Hapus header
            $inspection->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Data inspeksi sipil berhasil dihapus',
            ]);
        });
    }
}
