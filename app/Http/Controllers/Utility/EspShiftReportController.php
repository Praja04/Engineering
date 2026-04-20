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
            'tanggal_laporan' => 'required|date',

            'pemakaian_air' => 'nullable|numeric',
            'pemakaian_steam' => 'nullable|numeric',
            'pemakaian_batubara' => 'nullable|numeric',
            'efisiensi_batubara' => 'nullable|numeric',

            'running_hour_awal' => 'nullable|numeric',
            'running_hour_akhir' => 'nullable|numeric',

            'feed_tank_awal' => 'nullable|numeric',
            'feed_tank_akhir' => 'nullable|numeric',

            'pengisian_batubara' => 'nullable|numeric',
            'chemical_scf' => 'nullable|numeric',
            'chemical_srtf' => 'nullable|numeric',
            'dosis' => 'nullable|numeric',
        ]);

        // 🚨 Validasi waktu (hanya boleh sebelum jam 06:00)
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
                'pemakaian_air' => $request->pemakaian_air,
                'pemakaian_steam' => $request->pemakaian_steam,
                'pemakaian_batubara' => $request->pemakaian_batubara,
                'efisiensi_batubara' => $request->efisiensi_batubara,
                'running_hour_awal' => $request->running_hour_awal,
                'running_hour_akhir' => $request->running_hour_akhir,
                'feed_tank_awal' => $request->feed_tank_awal,
                'feed_tank_akhir' => $request->feed_tank_akhir,
                'pengisian_batubara' => $request->pengisian_batubara,
                'chemical_scf' => $request->chemical_scf,
                'chemical_srtf' => $request->chemical_srtf,
                'dosis' => $request->dosis,

                'operator_id' => auth()->id(),
                'status' => 'approved_operator'
            ]
        );

        // 🔔 Kirim notifikasi ke foreman & supervisor
        $this->sendNotification($data);

        return response()->json([
            'message' => 'Laporan berhasil disubmit & menunggu approval',
            'data' => $data
        ]);
    }

    /**
     * ✅ APPROVE FOREMAN
     */
    public function approveForeman($id)
    {
        $data = EspShiftReport::findOrFail($id);

        if ($data->status !== 'approved_operator') {
            return response()->json(['message' => 'Belum bisa di-approve'], 422);
        }

        $data->update([
            'foreman_id' => auth()->id(),
            'foreman_approved_at' => now(),
            'status' => 'approved_foreman'
        ]);

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

        if ($data->status !== 'approved_foreman') {
            return response()->json(['message' => 'Belum bisa di-approve'], 422);
        }

        $data->update([
            'supervisor_id' => auth()->id(),
            'supervisor_approved_at' => now(),
            'status' => 'approved_supervisor'
        ]);

        return response()->json([
            'message' => 'Disetujui Supervisor (Final)'
        ]);
    }

    /**
     * 🔔 NOTIFICATION HELPER
     */
    private function sendNotification($data)
    {
        $users = \App\Models\User::whereIn('jabatan', ['foreman', 'supervisor'])
        ->where('departemen', 'engineering')
        ->get();

        foreach ($users as $user) {
            NotificationsModel::create([
                'user_id' => $user->id,
                'title' => 'Approval ESP Shift',
                'message' => 'Laporan ESP menunggu persetujuan Anda',
                'url' => '/esp/shift/approval',
                'notifiable_type' => EspShiftReport::class,
                'notifiable_id' => $data->id,
                'is_read' => 0
            ]);
        }
    }
    // index — return view form
    public function index()
    {
        return view('utility.esp-operational-report.form');
    }

    // getData — JSON untuk data view & approval page
    public function getData(Request $request)
    {
        $query = EspShiftReport::query();

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

    // update — edit oleh non-operator
    public function update(Request $request, $id)
    {
        $data = EspShiftReport::findOrFail($id);

        $data->update($request->only([
            'pemakaian_air', 'pemakaian_steam', 'pemakaian_batubara',
            'efisiensi_batubara', 'pengisian_batubara',
            'running_hour_awal', 'running_hour_akhir',
            'feed_tank_awal', 'feed_tank_akhir',
            'chemical_scf', 'chemical_srtf', 'dosis',
        ]));

        return response()->json(['message' => 'Data berhasil diperbarui', 'data' => $data]);
    }

    // approvalView — halaman approval foreman/supervisor
    public function approvalView()
    {
        return view('utility.esp-operational-report.approval');
    }
}
