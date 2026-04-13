<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use App\Models\NotificationsModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Maintenance\MtcMainModel;
use App\Models\Maintenance\MtcApprovalModel;
use App\Models\Maintenance\MtcMasterMesinModel;
use App\Models\Maintenance\MtcRefrigerasiModel;
use App\Http\Requests\Maintenance\MtcMainRequest;
use App\Models\Maintenance\MtcKebutuhanMaterialModel;
use App\Http\Requests\Maintenance\MtcRefrigerasiRequest;
use App\Http\Requests\Maintenance\MtcKebutuhanMaterialRequest;
use Illuminate\Support\Str;
class MtcRefrigerasiController extends Controller
{
    // ── Helper: simpan TTD base64 ke storage ────────────────────────────────────
    private function saveBase64Signature(
        string $base64,
        string $folder,
        string $username,
        ?string $departemen = null
     ): string {
        // Hapus prefix "data:image/png;base64," jika ada
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $decoded   = base64_decode($imageData);

        $dept      = $departemen ? Str::slug($departemen) : 'umum';
        $filename  = Str::slug($username) . '_' . now()->format('Ymd_His') . '.png';
        $path      = "{$folder}/{$dept}/{$filename}";

        Storage::disk('public')->put($path, $decoded);

        return $path;
    }

    public function index()
    {
        $mesin = MtcMasterMesinModel::where('jenis_mtc', 'Refrigerasi')
            ->orderBy('id')->get();

        return view('maintenance.form.refrigerasi', compact('mesin'));
    }

    public function viewData()
    {
        $mesin = MtcMasterMesinModel::where('jenis_mtc', 'Refrigerasi')
            ->orderBy('id')->get();

        return view('maintenance.data.refrigerasi_data', compact('mesin'));
    }

    public function store(
        MtcMainRequest $mainRequest,
        MtcRefrigerasiRequest $detailRequest,
        MtcKebutuhanMaterialRequest $materials
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest, $materials) {

            $userId = Auth::id();

            // Simpan Main
            $main = MtcMainModel::create([
                ...$mainRequest->validated(),
                'jenis_mtc'  => 'Refrigerasi',
                'status'     => 'pending',
                'created_by' => $userId,
            ]);

            MtcRefrigerasiModel::create([
                ...$detailRequest->validated(),
                'mtc_main_id' => $main->id,
            ]);

            foreach ($materials->materials ?? [] as $item) {
                MtcKebutuhanMaterialModel::create([
                    'mtc_main_id' => $main->id,
                    'mid'         => $item['mid'],
                    'deskripsi'   => $item['desc'] ?? null,
                    'qty'         => $item['qty'],
                    'created_by'  => $userId,
                ]);
            }

            // Simpan TTD teknisi (level 1 — auto approved)
            $ttdPaths = [
                'teknisi' => 'mtc/ttd/ttd_teknisi.jpeg',  // TTD operator/teknisi
                'staff'   => 'mtc/ttd/ttd_staff.jpeg',     // TTD supervisor
                'user'    => 'mtc/ttd/ttd_user.jpeg',      // TTD user MT/MTC
            ];


            // Approval flow
            $approvalFlows = [
                [
                    'level'       => 1,
                    'role'        => 'teknisi',
                    'approver_id' => $userId,
                    'auto'        => true,
                ],
                [
                    'level'       => 2,
                    'role'        => 'staff',
                    'approver_id' => $mainRequest->staff_id,
                    'auto'        => false,
                ],
                [
                    'level'       => 3,
                    'role'        => 'user',
                    'approver_id' => $mainRequest->user_id,
                    'auto'        => false,
                ],
            ];

            foreach ($approvalFlows as $flow) {

                $isAutoApproved = $flow['auto'];
                $ttdPath = $isAutoApproved ? ($ttdPaths[$flow['role']] ?? null) : null;

                MtcApprovalModel::create([
                    'mtc_main_id' => $main->id,
                    'level'       => $flow['level'],
                    'role'        => $flow['role'],
                    'approver_id' => $flow['approver_id'],
                    'status'      => $isAutoApproved ? 'approved' : 'pending',
                    'ttd'         => $isAutoApproved ? $ttdPath : null,
                    'action_at'   => $isAutoApproved ? now() : null,
                    'action_by'   => $isAutoApproved ? $userId : null,
                ]);

                if (!$isAutoApproved) {
                    NotificationsModel::create([
                        'user_id'         => $flow['approver_id'],
                        'notifiable_type' => MtcMainModel::class,
                        'notifiable_id'   => $main->id,
                        'title'           => 'Approval Maintenance',
                        'message'         => 'Maintenance Refrigerasi menunggu persetujuan Anda',
                        'url'             => route('mtc.approval.index'),
                        'is_read'         => false,
                    ]);
                }
            }
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data MTC Refrigerasi berhasil disimpan',
        ], 201);
    }

    public function getData(Request $request)
    {
        $query = MtcMainModel::query()
            ->where('jenis_mtc', 'Refrigerasi')
            ->orderBy('tanggal', 'desc')
            // ->orderBy('waktu', 'desc')
            ->with([
                'createdBy:id,username',
                'kebutuhanMaterial',
                'refrigerasi.mesin:id,nama_mesin,lokasi',
            ]);

        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        if ($request->filled('paket')) {
            $query->where('paket', $request->paket);
        }

        if ($request->filled('nama_mesin')) {
            $query->whereHas('refrigerasi.mesin', function ($q) use ($request) {
                $q->where('nama_mesin', 'like', '%' . $request->nama_mesin . '%');
            });
        }

        $data = $query->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data Mtc Refrigerasi berhasil diambil',
            'data'    => $data,
        ]);
    }

    public function update(
        MtcMainRequest $mainRequest,
        MtcRefrigerasiRequest $detailRequest,
        MtcKebutuhanMaterialRequest $materials,
        $id
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest, $materials, $id) {

            $userId = Auth::id();

            $main       = MtcMainModel::findOrFail($id);
            $inspection = MtcRefrigerasiModel::where('mtc_main_id', $main->id)->firstOrFail();

            $main->update([
                ...$mainRequest->validated(),
                'updated_by' => $userId,
            ]);

            $inspection->update($detailRequest->validated());

            $existingIds = $main->kebutuhanMaterial()->pluck('id')->toArray();
            $incomingIds = [];

            foreach ($materials['materials'] as $item) {

                if (!empty($item['id'])) {
                    $incomingIds[] = $item['id'];

                    MtcKebutuhanMaterialModel::where('id', $item['id'])->update([
                        'mid'        => $item['mid'],
                        'deskripsi'  => $item['deskripsi'] ?? null,
                        'qty'        => $item['qty'],
                        'updated_by' => $userId,
                    ]);
                } else {
                    $new = MtcKebutuhanMaterialModel::create([
                        'mtc_main_id' => $main->id,
                        'mid'         => $item['mid'],
                        'deskripsi'   => $item['deskripsi'] ?? null,
                        'qty'         => $item['qty'],
                        'created_by'  => $userId,
                    ]);

                    $incomingIds[] = $new->id;
                }
            }

            // Hapus material yang dihapus dari form
            $toDelete = array_diff($existingIds, $incomingIds);
            if ($toDelete) {
                MtcKebutuhanMaterialModel::whereIn('id', $toDelete)->delete();
            }
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data berhasil diperbarui',
        ]);
    }
}
