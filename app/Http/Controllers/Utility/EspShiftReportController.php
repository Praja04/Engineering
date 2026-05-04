<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\EspShiftReport;
use App\Models\User;
use App\Models\NotificationsModel;
use Carbon\Carbon;

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
        if (auth()->user()->jabatan === 'operator' && Carbon::now()->format('H:i') >= '06:00') {
            return response()->json([
                'message' => 'Laporan hanya bisa diinput sebelum jam 06:00'
            ], 422);
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

                'operator_id'    => auth()->id(),
                'foreman_id'     => $request->foreman_id,
                'supervisor_id'  => $request->supervisor_id,
                'status'         => 'approved_operator'
            ]
        );

        // 🔔 Kirim notifikasi hanya ke foreman & supervisor yang dipilih
        $this->sendNotification($data);

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
     * 🔔 NOTIFICATION HELPER
     * Kirim notifikasi hanya ke foreman & supervisor yang dipilih
     */
    private function sendNotification($data)
    {
        $approvalUrl = url(route('esp-shift-report.approval', [], false));

        $recipients = User::whereIn('id', array_filter([
            $data->foreman_id,
            $data->supervisor_id,
        ]))->get();

        foreach ($recipients as $user) {
            NotificationsModel::create([
                'user_id'          => $user->id,
                'title'            => 'Approval ESP Shift',
                'message'          => 'Laporan ESP Shift tanggal ' . $data->tanggal_laporan . ' menunggu persetujuan Anda',
                'url'              => $approvalUrl,
                'notifiable_type'  => EspShiftReport::class,
                'notifiable_id'    => $data->id,
                'is_read'          => 0,
            ]);
        }
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

        $data->update($request->only([
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
        ]));

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
