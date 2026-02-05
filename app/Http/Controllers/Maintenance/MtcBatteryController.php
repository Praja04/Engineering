<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use App\Models\NotificationsModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Maintenance\MtcMainModel;
use App\Models\Maintenance\MtcBatteryModel;
use App\Models\Maintenance\MtcApprovalModel;
use App\Models\Maintenance\MtcBatteryMainModel;
use App\Http\Requests\Maintenance\MtcMainRequest;
use App\Http\Requests\Maintenance\MtcBatteryRequest;
use App\Http\Requests\Maintenance\MtcBatteryMainRequest;

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

    public function store(
        MtcMainRequest $mainRequest,
        MtcBatteryRequest $detailRequest,
        MtcBatteryMainRequest $batteryRequest
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest, $batteryRequest) {

            // dd($detailRequest);

            $data = $detailRequest->validated();

            $userId = Auth::id();

            // Simpan Main
            $main = MtcMainModel::create([
                ...$mainRequest->validated(),
                'jenis_mtc'  => 'battery',
                'status'     => 'pending',
                'created_by' => $userId,
            ]);

            $battery = MtcBatteryMainModel::create([
                ...$batteryRequest->validated(),
                'mtc_main_id' => $main->id,
            ]);

            // dd($detailRequest->validated()['details']);  // atau Log::info($detailRequest->all());

            $details = $detailRequest->validated()['details'] ?? [];

            // 4. Loop dan simpan setiap cell ke MtcBatteryModel
            foreach ($details as $detail) {
                MtcBatteryModel::create([
                    'mtc_battery_id'    => $battery->id,
                    'cell'              => $detail['cell'],
                    'voltase'           => $detail['voltase']         ?? null,
                    'grounding'         => $detail['grounding']       ?? null,
                    'level_air_aki'     => isset($detail['level_air_aki']) ? (bool) $detail['level_air_aki'] : false,
                    'intercell'         => isset($detail['intercell'])     ? (bool) $detail['intercell']     : false,
                    'kondisi_skun'      => isset($detail['kondisi_skun'])  ? (bool) $detail['kondisi_skun']  : false,
                    'kondisi_unit'      => isset($detail['kondisi_unit'])  ? (bool) $detail['kondisi_unit']  : false,
                ]);
            }

            $ttdPath = null;

            if ($detailRequest->filled('ttd_base64')) {
                $user = Auth::user();

                $ttdPath = saveBase64Signature(
                    $detailRequest->ttd_base64,
                    'mtc/battery',
                    $user->username,
                    $user->departemen
                );
            }

            $approvalFlows = [
                [
                    'level' => 1,
                    'role'  => 'teknisi',
                    'approver_id' => $userId,
                    'auto'  => true,
                ],
                [
                    'level' => 2,
                    'role'  => 'staff',
                    'approver_id' => 3,
                    'auto'  => false,
                ],
                [
                    'level' => 3,
                    'role'  => 'user',
                    'approver_id' => 4,
                    'auto'  => false,
                ],
            ];

            foreach ($approvalFlows as $flow) {

                $isAutoApproved = $flow['auto'];

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
                        'message'         => 'Maintenance Battery menunggu persetujuan Anda',
                        'url'             => route('mtc.approval.index'),
                        'is_read'         => false,
                    ]);
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data battery berhasil disimpan',
        ]);
    }

    public function getData(Request $request)
    {
        $query = MtcMainModel::query()
            ->where('jenis_mtc', 'battery')
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->with([
                'createdBy:id,username',
                'battery.details',
            ]);

        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        if ($request->filled('tipe_baterai')) {
            $query->whereHas('battery', function ($q) use ($request) {
                $q->where('battery_type', 'like', '%' . $request->tipe_baterai . '%');
            });
        }

        // Filter unit → cari di no_unit ATAU no_seri di relasi battery
        if ($request->filled('unit')) {
            $search = $request->unit;

            $query->whereHas('battery', function ($q) use ($search) {
                $q->where('no_unit', 'like', "%{$search}%")
                    ->orWhere('no_seri', 'like', "%{$search}%");
            });
        }

        $data = $query->get();

        return response()->json([
            'status'  => true,
            'message' => 'Data Mtc Battery berhasil diambil',
            'data'    => $data,
        ]);
    }

    public function update(
        MtcMainRequest $mainRequest,
        MtcBatteryRequest $detailRequest,
        MtcBatteryMainRequest $batteryRequest,
        $id
    ) {
        DB::transaction(function () use ($mainRequest, $detailRequest, $batteryRequest, $id) {

            $userId = Auth::id();

            $main = MtcMainModel::findOrFail($id);

            $main->update([
                ...$mainRequest->validated(),
                'updated_by' => $userId,
            ]);

            $battery = MtcBatteryMainModel::where('mtc_main_id', $main->id)
                ->firstOrFail();

            $battery->update([
                ...$batteryRequest->validated()
            ]);

            $details = $detailRequest->validated()['details'] ?? [];

            $existingCells = MtcBatteryModel::where('mtc_battery_id', $battery->id)
                ->get()
                ->keyBy('cell');

            foreach ($details as $detail) {

                $cell = $detail['cell'];

                $payload = [
                    'voltase'       => $detail['voltase']   ?? null,
                    'grounding'     => $detail['grounding'] ?? null,
                    'level_air_aki' => isset($detail['level_air_aki']) ? (bool)$detail['level_air_aki'] : false,
                    'intercell'     => isset($detail['intercell'])     ? (bool)$detail['intercell']     : false,
                    'kondisi_skun'  => isset($detail['kondisi_skun'])  ? (bool)$detail['kondisi_skun']  : false,
                    'kondisi_unit'  => isset($detail['kondisi_unit'])  ? (bool)$detail['kondisi_unit']  : false,
                ];

                if ($existingCells->has($cell)) {
                    // UPDATE
                    $existingCells[$cell]->update($payload);
                } else {
                    MtcBatteryModel::create([
                        'mtc_battery_id' => $battery->id,
                        'cell'           => $cell,
                        ...$payload,
                    ]);
                }
            }

            $incomingCells = collect($details)->pluck('cell')->toArray();

            MtcBatteryModel::where('mtc_battery_id', $battery->id)
                ->whereNotIn('cell', $incomingCells)
                ->delete();
        });



        return response()->json([
            'status' => 'success',
            'message' => 'Data battery berhasil diperbarui',
        ]);
    }
}
