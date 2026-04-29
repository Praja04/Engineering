<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\WarmingUpGenset as WarmingUpGensetModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WarmingUpGenset extends Controller
{
    public function index()
    {
        return view('utility.warming-up-genset.form');
    }

    public function approvalView()
    {
        return view('utility.warming-up-genset.approval');
    }

    public function dataView()
    {
        return view('utility.warming-up-genset.data');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'tanggal_laporan' => 'required|date',
                'jam_pencatatan' => 'required',
                'foreman_id' => 'required|exists:users,id',
                'supervisor_id' => 'required|exists:users,id',

                'engine_speed' => 'nullable|numeric',
                'engine_temperature' => 'nullable|numeric',
                'engine_oil_pressure' => 'nullable|numeric',
                'battery_voltage' => 'nullable|numeric',
                'charge_alt_voltage' => 'nullable|numeric',
                'running_hour' => 'nullable|numeric',
                'frequency' => 'nullable|numeric',
                'status_oil_1' => 'nullable|numeric',
                'status_oil_2' => 'nullable|numeric',
            ]);

            // 2. CEK DUPLIKAT
            if (WarmingUpGensetModel::where('user_id', auth()->id())
                ->where('tanggal_laporan', $validated['tanggal_laporan'])
                ->exists()
            ) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Laporan untuk tanggal ini sudah ada'
                ], 422);
            }

            $foreman = User::where('id', $validated['foreman_id'])
                ->where('jabatan', 'foreman')
                ->exists();

            $supervisor = User::where('id', $validated['supervisor_id'])
                ->where('jabatan', 'supervisor')
                ->exists();

            if (!$foreman || !$supervisor) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Foreman / Supervisor tidak valid'
                ], 422);
            }

            $report = WarmingUpGensetModel::create([
                ...$validated,
                'user_id' => auth()->id() ?? 1,
                'status' => 'submitted',
            ]);

            try {
                $this->sendNotification($report);
            } catch (\Exception $e) {
                Log::error('Notif gagal: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Laporan berhasil disubmit & menunggu approval.',
                'data' => $report
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Genset Error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage() // boleh dihapus kalau production
            ], 500);
        }
    }

    private function sendNotification($data)
    {
        $approvalUrl = url(route('warming-up-genset.approval', [], false));

        $recipients = User::whereIn('id', array_filter([
            $data->foreman_id,
            $data->supervisor_id,
        ]))->get();

        foreach ($recipients as $user) {
            NotificationsModel::create([
                'user_id'          => $user->id,
                'title'            => 'Approval Warming Up Genset',
                'message'          => 'Laporan warming up genset tanggal ' . $data->tanggal_laporan . ' menunggu persetujuan Anda',
                'url'              => $approvalUrl,
                'notifiable_type'  => WarmingUpGensetModel::class,
                'notifiable_id'    => $data->id,
                'is_read'          => 0,
            ]);
        }
    }

    public function approveForeman($id)
    {
        $data = WarmingUpGensetModel::findOrFail($id);

        if ($data->foreman_id !== auth()->id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        if ($data->status !== 'submitted') {
            return response()->json(['message' => 'Laporan ini tidak dalam status menunggu approval Foreman'], 422);
        }

        $data->update([
            'approved_foreman_at' => now(),
            'approved_foreman_by' => auth()->id(),
            'status' => 'approved_foreman'
        ]);

        return response()->json(['message' => 'Laporan disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = WarmingUpGensetModel::findOrFail($id);

        if ($data->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        if ($data->status !== 'approved_foreman') {
            return response()->json(['message' => 'Laporan harus disetujui Foreman terlebih dahulu'], 422);
        }

        $data->update([
            'approved_supervisor_at' => now(),
            'approved_supervisor_by' => auth()->id(),
            'status' => 'approved_supervisor'
        ]);

        return response()->json(['message' => 'Laporan disetujui Supervisor (Selesai)']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $data = WarmingUpGensetModel::findOrFail($id);

        // Cek apakah user adalah foreman atau supervisor yang sah
        $isForeman = ($data->foreman_id === auth()->id() && $data->status === 'submitted');
        $isSupervisor = ($data->supervisor_id === auth()->id() && $data->status === 'approved_foreman');

        if (!$isForeman && !$isSupervisor) {
            return response()->json(['message' => 'Anda tidak berwenang menolak laporan ini pada tahap ini'], 403);
        }

        $data->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        return response()->json(['message' => 'Laporan berhasil ditolak']);
    }

    public function update(Request $request, $id)
    {
        $report = WarmingUpGensetModel::findOrFail($id);

        // Check ownership
        if ($report->user_id !== auth()->id()) {
            return response()->json([
                'status' => 403,
                'message' => 'Anda tidak berwenang mengedit laporan ini'
            ], 403);
        }

        // Check status
        if (!in_array($report->status, ['submitted', 'rejected'])) {
            return response()->json([
                'status' => 422,
                'message' => 'Laporan yang sudah diproses tidak dapat diedit'
            ], 422);
        }

        try {
            $validated = $request->validate([
                'tanggal_laporan' => 'required|date',
                'jam_pencatatan' => 'required',
                'foreman_id' => 'required|exists:users,id',
                'supervisor_id' => 'required|exists:users,id',

                'engine_speed' => 'nullable|numeric',
                'engine_temperature' => 'nullable|numeric',
                'engine_oil_pressure' => 'nullable|numeric',
                'battery_voltage' => 'nullable|numeric',
                'charge_alt_voltage' => 'nullable|numeric',
                'running_hour' => 'nullable|numeric',
                'frequency' => 'nullable|numeric',
                'status_oil_1' => 'nullable|numeric',
                'status_oil_2' => 'nullable|numeric',
            ]);

            $report->update($validated);

            return response()->json([
                'status' => 200,
                'message' => 'Laporan berhasil diperbarui',
                'data' => $report
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Update Genset Error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    public function show($id)
    {
        $data = WarmingUpGensetModel::with(['operator', 'foreman', 'supervisor'])->find($id);

        if (!$data) {
            return response()->json([
                'status' => 404,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function getData(Request $request)
    {
        $query = WarmingUpGensetModel::with(['operator', 'foreman', 'supervisor'])
            ->orderBy('tanggal_laporan', 'desc');

        // Mode Approval: Hanya data yang perlu di-approve oleh user saat ini
        if ($request->mode === 'approval') {
            $query->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where('foreman_id', auth()->id())
                        ->where('status', 'submitted');
                })->orWhere(function ($sq) {
                    $sq->where('supervisor_id', auth()->id())
                        ->where('status', 'approved_foreman');
                });
            });
        }

        // Filter berdasarkan bulan tahun (format: YYYY-MM)
        if ($request->filled('bulan')) {
            $bulan = $request->bulan; // format: 2025-04
            $query->whereYear('tanggal_laporan', substr($bulan, 0, 4))
                ->whereMonth('tanggal_laporan', substr($bulan, 5, 2));
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 200,
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ]
        ]);
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['message' => 'Tidak ada data yang dipilih'], 422);
        }

        $successCount = 0;
        foreach ($ids as $id) {
            $data = WarmingUpGensetModel::find($id);
            if (!$data) continue;

            if ($data->foreman_id === auth()->id() && $data->status === 'submitted') {
                $data->update([
                    'approved_foreman_at' => now(),
                    'approved_foreman_by' => auth()->id(),
                    'status' => 'approved_foreman'
                ]);
                $successCount++;
            } elseif ($data->supervisor_id === auth()->id() && $data->status === 'approved_foreman') {
                $data->update([
                    'approved_supervisor_at' => now(),
                    'approved_supervisor_by' => auth()->id(),
                    'status' => 'approved_supervisor'
                ]);
                $successCount++;
            }
        }

        return response()->json([
            'status' => 200,
            'message' => $successCount . ' laporan berhasil disetujui secara massal.'
        ]);
    }

    public function bulkReject(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'reason' => 'required|string|max:255'
        ]);

        $successCount = 0;
        foreach ($request->ids as $id) {
            $data = WarmingUpGensetModel::find($id);
            if (!$data) continue;

            $isForeman = ($data->foreman_id === auth()->id() && $data->status === 'submitted');
            $isSupervisor = ($data->supervisor_id === auth()->id() && $data->status === 'approved_foreman');

            if ($isForeman || $isSupervisor) {
                $data->update([
                    'status' => 'rejected',
                    'reject_reason' => $request->reason
                ]);
                $successCount++;
            }
        }

        return response()->json([
            'status' => 200,
            'message' => $successCount . ' laporan berhasil ditolak secara massal.'
        ]);
    }

    public function export(Request $request)
    {
        $query = WarmingUpGensetModel::with(['operator', 'foreman', 'supervisor'])
            ->orderBy('tanggal_laporan', 'desc');

        // Filter berdasarkan bulan tahun (format: YYYY-MM)
        if ($request->filled('bulan')) {
            $bulan = $request->bulan;
            $query->whereYear('tanggal_laporan', substr($bulan, 0, 4))
                ->whereMonth('tanggal_laporan', substr($bulan, 5, 2));
        }

        $data = $query->get();

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }
}
