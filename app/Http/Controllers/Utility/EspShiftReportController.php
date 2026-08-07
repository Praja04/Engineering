<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\EspShiftReport;
use App\Models\User;
use App\Models\NotificationsModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EspShiftReportController extends Controller
{
    /**
     * 🧾 STORE (Operator Input)
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_laporan'   => 'required|date',
            'foreman_id'        => 'required|exists:users,id',
            'supervisor_id'     => 'required|exists:users,id',

            'pemakaian_air'         => 'nullable|numeric',
            'pemakaian_steam'       => 'nullable|numeric',
            'pemakaian_batubara'    => 'nullable|numeric',
            'efisiensi_batubara'    => 'nullable|numeric',

            'running_hour_awal'     => 'nullable|numeric',
            'running_hour_akhir'    => 'nullable|numeric',

            'feed_tank_awal'        => 'nullable|numeric',
            'feed_tank_akhir'       => 'nullable|numeric',

            'pengisian_batubara'    => 'nullable|numeric',
            'chemical_scf'          => 'nullable|numeric',
            'chemical_srtf'         => 'nullable|numeric',
            'dosis'                 => 'nullable|numeric',
        ]);

        // 🚨 Validasi waktu hanya untuk operator
        // if (auth()->user()->jabatan === 'operator' && Carbon::now()->format('H:i') >= '08:00') {
        //     return response()->json([
        //         'message' => 'Laporan hanya bisa diinput sebelum jam 08:00'
        //     ], 422);
        // }

        // 🔥 Hitung Kondensat
        $kondensat = null;
        if (!is_null($request->feed_tank_akhir) && 
            !is_null($request->feed_tank_awal) && 
            !is_null($request->pemakaian_air) && 
            $request->pemakaian_air != 0) {
            $kondensat = abs((($request->feed_tank_akhir - $request->feed_tank_awal) / $request->pemakaian_air) * 100 - 100);
        }

        // 🔥 Simpan (auto approve operator)
        $data = EspShiftReport::updateOrCreate(
            [
                'tanggal_laporan' => $request->tanggal_laporan
            ],
            [
                'pemakaian_air'         => $request->pemakaian_air,
                'pemakaian_steam'       => $request->pemakaian_steam,
                'pemakaian_batubara'    => $request->pemakaian_batubara,
                'efisiensi_batubara'    => $request->efisiensi_batubara,
                'running_hour_awal'     => $request->running_hour_awal,
                'running_hour_akhir'    => $request->running_hour_akhir,
                'feed_tank_awal'        => $request->feed_tank_awal,
                'feed_tank_akhir'       => $request->feed_tank_akhir,
                'pengisian_batubara'    => $request->pengisian_batubara,
                'chemical_scf'          => $request->chemical_scf,
                'chemical_srtf'         => $request->chemical_srtf,
                'dosis'                 => $request->dosis,
                'kondensat'             => $kondensat,

                'operator_id'    => auth()->id(),
                'foreman_id'     => $request->foreman_id,
                'supervisor_id'  => $request->supervisor_id,
                'status'         => 'approved_operator'
            ]
        );

        // 🔔 Kirim notifikasi hanya ke foreman & supervisor yang dipilih
        $this->sendNotification($data, $data->foreman_id);

        return response()->json([
            'message' => 'Laporan berhasil disubmit & menunggu approval',
            'data'    => $data
        ]);
    }

    /**
     * ✅ APPROVE FOREMAN
     */
    public function approveForeman($id)
    {
        $data = EspShiftReport::findOrFail($id);

        // Pastikan yang approve adalah foreman yang ditunjuk
        if ($data->foreman_id !== auth()->id()) {
            return response()->json(['message' => 'Anda tidak berwenang untuk menyetujui laporan ini'], 403);
        }

        if ($data->status !== 'approved_operator') {
            return response()->json(['message' => 'Belum bisa di-approve'], 422);
        }

        $data->update([
            'foreman_approved_at' => now(),
            'status'              => 'approved_foreman'
        ]);

        NotificationsModel::where('notifiable_type', EspShiftReport::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', auth()->id()) // opsional (biar spesifik)
            ->delete();

        try {
            $this->sendNotification($data, $data->supervisor_id);
        } catch (\Exception $e) {
            Log::error('Notif ESP Supervisor gagal: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Disetujui Foreman'
        ]);
    }

    /**
     * ✅ APPROVE SUPERVISOR (FINAL)
     */
    public function approveSupervisor($id)
    {
        $data = EspShiftReport::findOrFail($id);

        // Pastikan yang approve adalah supervisor yang ditunjuk
        if ($data->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Anda tidak berwenang untuk menyetujui laporan ini'], 403);
        }

        if ($data->status !== 'approved_foreman') {
            return response()->json(['message' => 'Belum bisa di-approve'], 422);
        }

        $data->update([
            'supervisor_approved_at' => now(),
            'status'                 => 'approved_supervisor'
        ]);

        NotificationsModel::where('notifiable_type', EspShiftReport::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', auth()->id()) // opsional (biar spesifik)
            ->delete();

        return response()->json([
            'message' => 'Disetujui Supervisor (Final)'
        ]);
    }

    /**
     * 👥 MASS APPROVE (Foreman or Supervisor)
     */
    public function massApprove(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|exists:esp_shift_reports,id',
        ]);

        $ids = $request->ids;
        $jabatan = auth()->user()->jabatan;
        $userId = auth()->id();

        if ($jabatan !== 'foreman' && $jabatan !== 'supervisor') {
            return response()->json(['message' => 'Anda tidak memiliki wewenang untuk menyetujui laporan ini'], 403);
        }

        $approvedCount = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($id, $jabatan, $userId, &$approvedCount, &$errors) {
                    $data = EspShiftReport::findOrFail($id);

                    if ($jabatan === 'foreman') {
                        if ($data->foreman_id !== $userId) {
                            $errors[] = "Laporan tanggal {$data->tanggal_laporan}: Anda bukan foreman yang ditunjuk.";
                            return;
                        }

                        if ($data->status !== 'approved_operator') {
                            $errors[] = "Laporan tanggal {$data->tanggal_laporan}: Status tidak valid untuk approval Foreman.";
                            return;
                        }

                        $data->update([
                            'foreman_approved_at' => now(),
                            'status'              => 'approved_foreman'
                        ]);

                        NotificationsModel::where('notifiable_type', EspShiftReport::class)
                            ->where('notifiable_id', $data->id)
                            ->where('user_id', $userId)
                            ->delete();

                        try {
                            $this->sendNotification($data, $data->supervisor_id);
                        } catch (\Exception $e) {
                            Log::error("Notif ESP Supervisor gagal untuk ID {$id}: " . $e->getMessage());
                        }

                        $approvedCount++;
                    } elseif ($jabatan === 'supervisor') {
                        if ($data->supervisor_id !== $userId) {
                            $errors[] = "Laporan tanggal {$data->tanggal_laporan}: Anda bukan supervisor yang ditunjuk.";
                            return;
                        }

                        if ($data->status !== 'approved_foreman') {
                            $errors[] = "Laporan tanggal {$data->tanggal_laporan}: Status tidak valid untuk approval Supervisor.";
                            return;
                        }

                        $data->update([
                            'supervisor_approved_at' => now(),
                            'status'                 => 'approved_supervisor'
                        ]);

                        NotificationsModel::where('notifiable_type', EspShiftReport::class)
                            ->where('notifiable_id', $data->id)
                            ->where('user_id', $userId)
                            ->delete();

                        $approvedCount++;
                    }
                });
            } catch (\Exception $e) {
                $errors[] = "Gagal memproses ID {$id}: " . $e->getMessage();
            }
        }

        if ($approvedCount === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada laporan yang berhasil disetujui.',
                'errors'  => $errors
            ], 422);
        }

        $msg = "Berhasil menyetujui {$approvedCount} laporan.";
        return response()->json([
            'success' => true,
            'message' => $msg,
            'approved_count' => $approvedCount,
            'errors'  => $errors
        ]);
    }

    /**
     * 🔔 NOTIFICATION HELPER
     * Kirim notifikasi hanya ke foreman & supervisor yang dipilih
     */
    private function sendNotification($data, $userId)
    {
        $approvalUrl = url(route('esp-shift-report.approval', [], false));

        NotificationsModel::create([
            'user_id'          => $userId,
            'title'            => 'Approval ESP Shift',
            'message'          => 'Laporan ESP Shift tanggal ' . $data->tanggal_laporan . ' menunggu persetujuan Anda',
            'url'              => $approvalUrl,
            'notifiable_type'  => EspShiftReport::class,
            'notifiable_id'    => $data->id,
            'is_read'          => 0,
        ]);
    }

    /**
     * 📋 INDEX — return view form
     */
    public function index()
    {
        return view('utility.esp-operational-report.form');
    }

    /**
     * 📊 GET DATA — JSON untuk data view & approval page
     * Foreman hanya melihat laporan yang ditunjuk padanya
     * Supervisor hanya melihat laporan yang ditunjuk padanya
     */
    public function getData(Request $request)
    {
        $query   = EspShiftReport::query();
        $jabatan = auth()->user()->jabatan;

        // Filter berdasarkan jabatan — hanya tampilkan laporan yang relevan
        if ($jabatan === 'foreman') {
            $query->where('foreman_id', auth()->id());
        } elseif ($jabatan === 'supervisor') {
            $query->where('supervisor_id', auth()->id());
        }
        // Operator & admin/lainnya bisa lihat semua

        if ($request->tanggal) {
            $query->where('tanggal_laporan', $request->tanggal);
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_laporan', [$request->start_date, $request->end_date]);
        } else {
            $query->whereBetween('tanggal_laporan', [
                Carbon::now()->subDays(30)->toDateString(),
                Carbon::now()->toDateString()
            ]);
        }

        if ($request->status === 'pending') {
            // pending = belum final (bukan approved_supervisor)
            $query->whereIn('status', ['approved_operator', 'approved_foreman']);
        } elseif ($request->status === 'approved') {
            $query->where('status', 'approved_supervisor');
        }

        return response()->json(
            $query->with(['operator', 'foreman', 'supervisor'])->latest()->get()
        );
    }

    /**
     * ✏️ UPDATE — edit oleh non-operator
     */
    public function update(Request $request, $id)
    {
        $data = EspShiftReport::findOrFail($id);

        $feedTankAkhir = $request->has('feed_tank_akhir') ? $request->feed_tank_akhir : $data->feed_tank_akhir;
        $feedTankAwal = $request->has('feed_tank_awal') ? $request->feed_tank_awal : $data->feed_tank_awal;
        $pemakaianAir = $request->has('pemakaian_air') ? $request->pemakaian_air : $data->pemakaian_air;

        $kondensat = null;
        if (!is_null($feedTankAkhir) && 
            !is_null($feedTankAwal) && 
            !is_null($pemakaianAir) && 
            $pemakaianAir != 0) {
            $kondensat = abs((($feedTankAkhir - $feedTankAwal) / $pemakaianAir) * 100 - 100);
        }

        $updateData = $request->only([
            'pemakaian_air',
            'pemakaian_steam',
            'pemakaian_batubara',
            'efisiensi_batubara',
            'pengisian_batubara',
            'running_hour_awal',
            'running_hour_akhir',
            'feed_tank_awal',
            'feed_tank_akhir',
            'chemical_scf',
            'chemical_srtf',
            'dosis',
        ]);
        $updateData['kondensat'] = $kondensat;

        $data->update($updateData);

        return response()->json(['message' => 'Data berhasil diperbarui', 'data' => $data]);
    }

    /**
     * 🔐 APPROVAL VIEW — halaman approval foreman/supervisor
     */
    public function approvalView()
    {
        return view('utility.esp-operational-report.approval');
    }
}
