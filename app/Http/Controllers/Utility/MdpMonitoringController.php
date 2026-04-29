<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\MdpMonitoring as MdpMonitoringModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MdpMonitoringController extends Controller
{
    public function index()
    {
        return view('utility.mdp.form');
    }

    public function approvalView()
    {
        return view('utility.mdp.approval');
    }

    public function dataView()
    {
        return view('utility.mdp.data');
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

                'e_del' => 'nullable|numeric',
                'arus_rata_rata' => 'nullable|numeric',
                'arus_i1' => 'nullable|numeric',
                'arus_i2' => 'nullable|numeric',
                'arus_i3' => 'nullable|numeric',
                'tegangan_rata_rata' => 'nullable|numeric',
                'tegangan_v1' => 'nullable|numeric',
                'tegangan_v2' => 'nullable|numeric',
                'tegangan_v3' => 'nullable|numeric',
                'daya_total' => 'nullable|numeric',
                'daya_p1' => 'nullable|numeric',
                'daya_p2' => 'nullable|numeric',
                'daya_p3' => 'nullable|numeric',
                'temperatur_transformator' => 'nullable|numeric',
                'level_oil' => 'nullable|string|in:ok,nok',
            ]);

            // Cek Duplikat
            if (MdpMonitoringModel::where('user_id', auth()->id())
                ->where('tanggal_laporan', $validated['tanggal_laporan'])
                ->where('jam_pencatatan', $validated['jam_pencatatan'])
                ->exists()
            ) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Laporan untuk tanggal dan jam ini sudah ada'
                ], 422);
            }

            $report = MdpMonitoringModel::create([
                ...$validated,
                'user_id' => auth()->id(),
                'status' => 'submitted',
            ]);

            try {
                $this->sendNotification($report);
            } catch (\Exception $e) {
                Log::error('Notif MDP gagal: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Laporan MDP berhasil disubmit & menunggu approval.',
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
            Log::error('Store MDP Error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function sendNotification($data)
    {
        $approvalUrl = url(route('mdp-monitoring.approval', [], false));

        $recipients = User::whereIn('id', array_filter([
            $data->foreman_id,
            $data->supervisor_id,
        ]))->get();

        foreach ($recipients as $user) {
            NotificationsModel::create([
                'user_id'          => $user->id,
                'title'            => 'Approval Pemantauan MDP',
                'message'          => 'Laporan pemantauan MDP tanggal ' . $data->tanggal_laporan . ' menunggu persetujuan Anda',
                'url'              => $approvalUrl,
                'notifiable_type'  => MdpMonitoringModel::class,
                'notifiable_id'    => $data->id,
                'is_read'          => 0,
            ]);
        }
    }

    public function approveForeman($id)
    {
        $data = MdpMonitoringModel::findOrFail($id);

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
        $data = MdpMonitoringModel::findOrFail($id);

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
        $data = MdpMonitoringModel::findOrFail($id);

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

    public function show($id)
    {
        $data = MdpMonitoringModel::with(['operator', 'foreman', 'supervisor'])->find($id);

        if (!$data) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function getData(Request $request)
    {
        $query = MdpMonitoringModel::with(['operator', 'foreman', 'supervisor'])
            ->orderBy('tanggal_laporan', 'desc')
            ->orderBy('jam_pencatatan', 'desc');

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

        if ($request->filled('bulan')) {
            $bulan = $request->bulan;
            $query->whereYear('tanggal_laporan', substr($bulan, 0, 4))
                ->whereMonth('tanggal_laporan', substr($bulan, 5, 2));
        }

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
            $data = MdpMonitoringModel::find($id);
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
            $data = MdpMonitoringModel::find($id);
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
        $query = MdpMonitoringModel::with(['operator', 'foreman', 'supervisor'])
            ->orderBy('tanggal_laporan', 'desc');

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
