<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\CapacitorBank;
use App\Models\Utility\CapacitorBankApproval;
use App\Models\NotificationsModel;
use Carbon\Carbon;

class CapacitorBankController extends Controller
{
    /**
     * 📋 INDEX — form input harian
     */
    public function index()
    {
        return view('utility.capacitor-bank.form');
    }

    /**
     * 🔐 APPROVAL VIEW
     */
    public function approvalView()
    {
        return view('utility.capacitor-bank.approval');
    }

    /**
     * 📊 REKAP VIEW
     */
    public function rekapView()
    {
        return view('utility.capacitor-bank.rekap');
    }

    // =========================================================
    // OPERATOR — INPUT HARIAN
    // =========================================================

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'     => 'required|date',
            'jam'         => 'nullable',
            'arus_total'  => 'nullable|numeric|min:0',

            'cap_a_nomor' => 'nullable|integer',
            'cap_a_i1'    => 'nullable|numeric|min:0',
            'cap_a_i2'    => 'nullable|numeric|min:0',
            'cap_a_i3'    => 'nullable|numeric|min:0',

            'cap_b_nomor' => 'nullable|integer',
            'cap_b_i1'    => 'nullable|numeric|min:0',
            'cap_b_i2'    => 'nullable|numeric|min:0',
            'cap_b_i3'    => 'nullable|numeric|min:0',

            'cap_c_nomor' => 'nullable|integer',
            'cap_c_i1'    => 'nullable|numeric|min:0',
            'cap_c_i2'    => 'nullable|numeric|min:0',
            'cap_c_i3'    => 'nullable|numeric|min:0',

            'suhu_ruang'  => 'nullable|numeric',
        ]);

        $tanggal = Carbon::createFromFormat('Y-m-d', $request->tanggal);

        // 🚫 blokir jika sudah final
        $approval = CapacitorBankApproval::where('bulan', $tanggal->month)
            ->where('tahun', $tanggal->year)
            ->first();

        if ($approval && $approval->status === 'approved_supervisor') {
            return response()->json([
                'message' => 'Laporan bulan ini sudah disetujui, tidak dapat diubah.'
            ], 422);
        }

        // 🚫 tidak boleh double tanggal
        $existing = CapacitorBank::where('tanggal', $request->tanggal)->first();

        if ($existing) {
            return response()->json([
                'message' => 'Data tanggal ' . $tanggal->format('d/m/Y') . ' sudah ada.'
            ], 422);
        }

        $data = CapacitorBank::create($request->all());

        // pastikan approval ada
        CapacitorBankApproval::firstOrCreate(
            ['bulan' => $tanggal->month, 'tahun' => $tanggal->year],
            ['status' => 'draft', 'operator_id' => auth()->id()]
        );

        return response()->json([
            'message' => 'Data berhasil disimpan.',
            'data'    => $data
        ]);
    }

    /**
     * ✏️ UPDATE
     */
    public function update(Request $request, $tanggal)
    {
        $request->validate([
            'jam'        => 'nullable',
            'arus_total' => 'nullable|numeric|min:0',

            'cap_a_nomor' => 'nullable|integer',
            'cap_a_i1'    => 'nullable|numeric|min:0',
            'cap_a_i2'    => 'nullable|numeric|min:0',
            'cap_a_i3'    => 'nullable|numeric|min:0',

            'cap_b_nomor' => 'nullable|integer',
            'cap_b_i1'    => 'nullable|numeric|min:0',
            'cap_b_i2'    => 'nullable|numeric|min:0',
            'cap_b_i3'    => 'nullable|numeric|min:0',

            'cap_c_nomor' => 'nullable|integer',
            'cap_c_i1'    => 'nullable|numeric|min:0',
            'cap_c_i2'    => 'nullable|numeric|min:0',
            'cap_c_i3'    => 'nullable|numeric|min:0',

            'suhu_ruang'  => 'nullable|numeric',
        ]);

        $carbon = Carbon::createFromFormat('Y-m-d', $tanggal);

        $approval = CapacitorBankApproval::where('bulan', $carbon->month)
            ->where('tahun', $carbon->year)
            ->first();

        if ($approval && $approval->status === 'approved_supervisor') {
            return response()->json([
                'message' => 'Laporan bulan ini sudah disetujui.'
            ], 422);
        }

        $data = CapacitorBank::where('tanggal', $tanggal)->first();

        if (!$data) {
            return response()->json([
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        $data->update($request->all());

        return response()->json([
            'message' => 'Data berhasil diupdate.',
            'data'    => $data
        ]);
    }

    // =========================================================
    // SUBMIT
    // =========================================================

    public function submitBulan(Request $request)
    {
        $request->validate([
            'bulan'      => 'required|integer|between:1,12',
            'tahun'      => 'required|integer',
            'foreman_id' => 'required|exists:users,id',
        ]);

        $approval = CapacitorBankApproval::where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->firstOrFail();

        if ($approval->status !== 'draft') {
            return response()->json(['message' => 'Sudah disubmit.'], 422);
        }

        $jumlahHari = Carbon::create($request->tahun, $request->bulan)->daysInMonth;

        $jumlahData = CapacitorBank::whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->count();

        if ($jumlahData < $jumlahHari) {
            return response()->json([
                'message' => "Data belum lengkap ({$jumlahData}/{$jumlahHari})"
            ], 422);
        }

        $approval->update([
            'foreman_id'   => $request->foreman_id,
            'status'       => 'waiting_foreman',
            'submitted_at' => now(),
        ]);

        $this->kirimNotifikasi(
            $request->foreman_id,
            'Approval Capacitor Bank',
            "Laporan bulan {$request->bulan}/{$request->tahun} menunggu approval.",
            $approval->id
        );

        return response()->json([
            'message' => 'Berhasil submit ke Foreman'
        ]);
    }

    // =========================================================
    // APPROVAL
    // =========================================================

    public function approveForeman(Request $request, $id)
    {
        $request->validate([
            'supervisor_id' => 'required|exists:users,id'
        ]);

        $approval = CapacitorBankApproval::findOrFail($id);

        if ($approval->foreman_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($approval->status !== 'waiting_foreman') {
            return response()->json(['message' => 'Status tidak valid'], 422);
        }

        $approval->update([
            'supervisor_id'       => $request->supervisor_id,
            'status'              => 'approved_foreman',
            'foreman_approved_at' => now(),
        ]);

        $this->kirimNotifikasi(
            $request->supervisor_id,
            'Approval Capacitor Bank',
            "Laporan menunggu approval Supervisor.",
            $approval->id
        );

        return response()->json([
            'message' => 'Disetujui Foreman'
        ]);
    }

    public function approveSupervisor($id)
    {
        $approval = CapacitorBankApproval::findOrFail($id);

        if ($approval->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($approval->status !== 'approved_foreman') {
            return response()->json(['message' => 'Belum disetujui foreman'], 422);
        }

        $approval->update([
            'status'                 => 'approved_supervisor',
            'supervisor_approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Final Approved'
        ]);
    }

    // =========================================================
    // DATA
    // =========================================================

    public function getData(Request $request)
    {
        if ($request->bulan && str_contains($request->bulan, '-')) {
            [$tahun, $bulan] = explode('-', $request->bulan);
            $request->merge([
                'bulan' => (int)$bulan,
                'tahun' => (int)$tahun
            ]);
        }

        $request->validate([
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
        ]);

        $data = CapacitorBank::whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->orderBy('tanggal')
            ->get();

        $approval = CapacitorBankApproval::where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->with(['operator', 'foreman', 'supervisor'])
            ->first();

        return response()->json([
            'data'     => $data,
            'approval' => $approval
        ]);
    }

    public function getApprovalList(Request $request)
    {
        $query = CapacitorBankApproval::with(['operator', 'foreman', 'supervisor']);
        $jabatan = auth()->user()->jabatan;
        $tab = $request->tab ?? 'pending';

        if ($jabatan === 'foreman') {
            $query->where('foreman_id', auth()->id());

            if ($tab === 'pending') {
                $query->where('status', 'waiting_foreman');
            }
        }

        if ($jabatan === 'supervisor') {
            $query->where('supervisor_id', auth()->id());

            if ($tab === 'pending') {
                $query->where('status', 'approved_foreman');
            }
        }

        return response()->json(
            $query->orderByDesc('tahun')->orderByDesc('bulan')->get()
        );
    }

    // =========================================================
    // HELPER
    // =========================================================

    private function kirimNotifikasi($userId, $title, $message, $approvalId)
    {
        NotificationsModel::create([
            'user_id'         => $userId,
            'title'           => $title,
            'message'         => $message,
            'url'             => url(route('capacitor-bank.approval')),
            'notifiable_type' => CapacitorBankApproval::class,
            'notifiable_id'   => $approvalId,
            'is_read'         => 0,
        ]);
    }
}
