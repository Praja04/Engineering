<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\WaterSoftener;
use App\Models\Utility\WaterSoftenerApproval;
use App\Models\User;
use App\Models\NotificationsModel;
use Carbon\Carbon;

class WaterSoftenerController extends Controller
{
    /**
     * 📋 INDEX — view form input harian (Operator)
     */
    public function index()
    {
        return view('utility.water-softener.form');
    }

    /**
     * 📊 REKAP VIEW — halaman rekap + submit approval (Foreman)
     */
    public function rekapView()
    {
        return view('utility.water-softener.rekap');
    }

    /**
     * 🔐 APPROVAL VIEW — halaman approve laporan (Supervisor only)
     */
    public function approvalView()
    {
        return view('utility.water-softener.approval');
    }

    // =========================================================
    // OPERATOR — INPUT HARIAN
    // =========================================================

    /**
     * 🧾 STORE — Operator input data harian
     *
     * Tidak bisa input jika bulan tersebut sudah diajukan atau approved.
     * Satu tanggal hanya bisa diinput sekali.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'            => 'required|date',
            'ws1_jam'            => 'nullable|date_format:H:i',
            'ws1_hardness_in'    => 'nullable|numeric|min:0',
            'ws1_hardness_out'   => 'nullable|numeric|min:0',
            'ws1_flow'           => 'nullable|numeric|min:0',
            'ws2_jam'            => 'nullable|date_format:H:i',
            'ws2_hardness_in'    => 'nullable|numeric|min:0',
            'ws2_hardness_out'   => 'nullable|numeric|min:0',
            'ws2_flow'           => 'nullable|numeric|min:0',
            'regen1_jam'         => 'nullable|date_format:H:i',
            'regen1_air_pelarut' => 'nullable|numeric|min:0',
            'regen1_garam'       => 'nullable|numeric|min:0',
            'regen1_nomer_ws'    => 'nullable|integer|min:1',
            'regen2_jam'         => 'nullable|date_format:H:i',
            'regen2_air_pelarut' => 'nullable|numeric|min:0',
            'regen2_garam'       => 'nullable|numeric|min:0',
            'regen2_nomer_ws'    => 'nullable|integer|min:1',
        ]);

        $tanggal = Carbon::createFromFormat('Y-m-d', $request->tanggal);

        // Blokir jika bulan sudah diajukan atau final approved
        $approval = WaterSoftenerApproval::where('bulan', $tanggal->month)
            ->where('tahun', $tanggal->year)
            ->first();

        if ($approval && in_array($approval->status, ['waiting_supervisor', 'approved_supervisor'])) {
            return response()->json([
                'message' => 'Laporan bulan ini sudah diajukan/disetujui, tidak dapat diubah.'
            ], 422);
        }

        // Satu tanggal hanya bisa diinput sekali
        $existing = WaterSoftener::where('tanggal', $request->tanggal)->first();

        if ($existing) {
            return response()->json([
                'message' => 'Data tanggal ' . $tanggal->format('d/m/Y') . ' sudah ada dan tidak dapat diubah.'
            ], 422);
        }

        $data = WaterSoftener::create([
            'tanggal'            => $request->tanggal,
            'ws1_jam'            => $request->ws1_jam,
            'ws1_hardness_in'    => $request->ws1_hardness_in,
            'ws1_hardness_out'   => $request->ws1_hardness_out,
            'ws1_flow'           => $request->ws1_flow,
            'ws2_jam'            => $request->ws2_jam,
            'ws2_hardness_in'    => $request->ws2_hardness_in,
            'ws2_hardness_out'   => $request->ws2_hardness_out,
            'ws2_flow'           => $request->ws2_flow,
            'regen1_jam'         => $request->regen1_jam,
            'regen1_air_pelarut' => $request->regen1_air_pelarut,
            'regen1_garam'       => $request->regen1_garam,
            'regen1_nomer_ws'    => $request->regen1_nomer_ws,
            'regen2_jam'         => $request->regen2_jam,
            'regen2_air_pelarut' => $request->regen2_air_pelarut,
            'regen2_garam'       => $request->regen2_garam,
            'regen2_nomer_ws'    => $request->regen2_nomer_ws,
            'operator_id'        => auth()->id(),
        ]);

        // Buat record approval bulan ini jika belum ada
        WaterSoftenerApproval::firstOrCreate(
            ['bulan' => $tanggal->month, 'tahun' => $tanggal->year],
            ['status' => 'draft']
        );

        return response()->json([
            'message' => 'Data tanggal ' . $tanggal->format('d/m/Y') . ' berhasil disimpan.',
            'data'    => $data,
        ]);
    }

    /**
     * ✏️ UPDATE — Edit data harian yang sudah ada
     */
    public function update(Request $request, $tanggal)
    {
        $request->validate([
            'ws1_jam'            => 'nullable|date_format:H:i',
            'ws1_hardness_in'    => 'nullable|numeric|min:0',
            'ws1_hardness_out'   => 'nullable|numeric|min:0',
            'ws1_flow'           => 'nullable|numeric|min:0',
            'ws2_jam'            => 'nullable|date_format:H:i',
            'ws2_hardness_in'    => 'nullable|numeric|min:0',
            'ws2_hardness_out'   => 'nullable|numeric|min:0',
            'ws2_flow'           => 'nullable|numeric|min:0',
            'regen1_jam'         => 'nullable|date_format:H:i',
            'regen1_air_pelarut' => 'nullable|numeric|min:0',
            'regen1_garam'       => 'nullable|numeric|min:0',
            'regen1_nomer_ws'    => 'nullable|integer|min:1',
            'regen2_jam'         => 'nullable|date_format:H:i',
            'regen2_air_pelarut' => 'nullable|numeric|min:0',
            'regen2_garam'       => 'nullable|numeric|min:0',
            'regen2_nomer_ws'    => 'nullable|integer|min:1',
        ]);

        $carbon = Carbon::createFromFormat('Y-m-d', $tanggal);

        // Blokir jika bulan sudah diajukan atau final approved
        $approval = WaterSoftenerApproval::where('bulan', $carbon->month)
            ->where('tahun', $carbon->year)
            ->first();

        if ($approval && in_array($approval->status, ['waiting_supervisor', 'approved_supervisor'])) {
            return response()->json([
                'message' => 'Laporan bulan ini sudah diajukan/disetujui, tidak dapat diubah.'
            ], 422);
        }

        $data = WaterSoftener::where('tanggal', $tanggal)->first();

        if (!$data) {
            return response()->json([
                'message' => 'Data tanggal ' . $carbon->format('d/m/Y') . ' tidak ditemukan.'
            ], 404);
        }

        $data->update([
            'ws1_jam'            => $request->ws1_jam,
            'ws1_hardness_in'    => $request->ws1_hardness_in,
            'ws1_hardness_out'   => $request->ws1_hardness_out,
            'ws1_flow'           => $request->ws1_flow,
            'ws2_jam'            => $request->ws2_jam,
            'ws2_hardness_in'    => $request->ws2_hardness_in,
            'ws2_hardness_out'   => $request->ws2_hardness_out,
            'ws2_flow'           => $request->ws2_flow,
            'regen1_jam'         => $request->regen1_jam,
            'regen1_air_pelarut' => $request->regen1_air_pelarut,
            'regen1_garam'       => $request->regen1_garam,
            'regen1_nomer_ws'    => $request->regen1_nomer_ws,
            'regen2_jam'         => $request->regen2_jam,
            'regen2_air_pelarut' => $request->regen2_air_pelarut,
            'regen2_garam'       => $request->regen2_garam,
            'regen2_nomer_ws'    => $request->regen2_nomer_ws,
            'operator_id'        => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Data tanggal ' . $carbon->format('d/m/Y') . ' berhasil diperbarui.',
            'data'    => $data,
        ]);
    }

    // =========================================================
    // SUBMIT — FOREMAN AJUKAN KE SUPERVISOR (dari rekap.blade)
    // =========================================================

    /**
     * 📤 SUBMIT BULAN
     *
     * Foreman submit laporan bulan ke supervisor dari halaman rekap.
     * Syarat: minimal ada 1 data yang sudah diinput di bulan tersebut.
     */
    public function submitBulan(Request $request)
    {
        $request->validate([
            'bulan'         => 'required|integer|between:1,12',
            'tahun'         => 'required|integer|min:2000',
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $approval = WaterSoftenerApproval::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->firstOrFail();

        if ($approval->status !== 'draft') {
            return response()->json(['message' => 'Laporan sudah disubmit sebelumnya.'], 422);
        }

        // Validasi: minimal ada 1 data yang tidak null di bulan ini
        $jumlahTerisi = WaterSoftener::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->count();

        if ($jumlahTerisi === 0) {
            return response()->json([
                'message' => "Tidak ada data untuk bulan {$bulan}/{$tahun}. Minimal satu data harus diinput sebelum mengajukan."
            ], 422);
        }

        $approval->update([
            'foreman_id'    => auth()->id(),          // Catat siapa foreman yang mengajukan
            'supervisor_id' => $request->supervisor_id,
            'status'        => 'waiting_supervisor',
            'submitted_at'  => now(),
        ]);

        // Kirim notifikasi ke supervisor
        $this->kirimNotifikasi(
            userId: $request->supervisor_id,
            title: 'Approval Water Softener',
            message: "Laporan Water Softener bulan {$bulan}/{$tahun} menunggu persetujuan Anda.",
            approvalId: $approval->id,
        );

        return response()->json([
            'message' => "Laporan bulan {$bulan}/{$tahun} berhasil diajukan ke Supervisor."
        ]);
    }

    // =========================================================
    // APPROVAL — SUPERVISOR APPROVE (dari approval.blade)
    // =========================================================

    /**
     * ✅ APPROVE SUPERVISOR (FINAL)
     *
     * Supervisor menyetujui laporan yang diajukan foreman.
     * Setelah ini laporan terkunci permanen.
     */
    public function approveSupervisor($id)
    {
        $approval = WaterSoftenerApproval::findOrFail($id);

        if ((int) $approval->supervisor_id !== (int) auth()->id()) {
            return response()->json(['message' => 'Anda tidak berwenang menyetujui laporan ini.'], 403);
        }

        if ($approval->status !== 'waiting_supervisor') {
            return response()->json([
                'message' => 'Status laporan tidak valid untuk disetujui. Status saat ini: ' . $approval->status
            ], 422);
        }

        $approval->update([
            'status'                 => 'approved_supervisor',
            'supervisor_approved_at' => now(),
        ]);

        return response()->json([
            'message' => "Laporan bulan {$approval->bulan}/{$approval->tahun} telah disetujui (Final)."
        ]);
    }

    // =========================================================
    // DATA
    // =========================================================

    /**
     * 📊 GET DATA HARIAN — JSON untuk tabel rekap
     */
    public function getData(Request $request)
    {
        // Support format "2025-01" dari input type="month"
        if ($request->bulan && str_contains((string) $request->bulan, '-')) {
            [$tahun, $bulan] = explode('-', $request->bulan);
            $request->merge(['bulan' => (int) $bulan, 'tahun' => (int) $tahun]);
        }

        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2000',
        ]);

        $data = WaterSoftener::whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->orderBy('tanggal')
            ->get();

        $approval = WaterSoftenerApproval::where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->with(['foreman', 'supervisor'])
            ->first();

        return response()->json([
            'data'     => $data,
            'approval' => $approval,
        ]);
    }

    /**
     * 📋 GET LIST APPROVAL — untuk halaman approval.blade (Supervisor only)
     *
     * Tab "pending"  → laporan yang menunggu persetujuan supervisor ini
     * Tab "history"  → laporan yang sudah disetujui supervisor ini
     */
    public function getApprovalList(Request $request)
    {
        $tab = $request->tab ?? 'pending';

        $query = WaterSoftenerApproval::with(['foreman', 'supervisor'])
            ->where('supervisor_id', auth()->id());

        if ($tab === 'pending') {
            $query->where('status', 'waiting_supervisor');
        } else {
            $query->where('status', 'approved_supervisor');
        }

        return response()->json(
            $query->orderByDesc('tahun')->orderByDesc('bulan')->get()
        );
    }

    // =========================================================
    // HELPER
    // =========================================================

    private function kirimNotifikasi(int $userId, string $title, string $message, int $approvalId): void
    {
        NotificationsModel::create([
            'user_id'         => $userId,
            'title'           => $title,
            'message'         => $message,
            'url'             => url(route('water-softener.approval')),
            'notifiable_type' => WaterSoftenerApproval::class,
            'notifiable_id'   => $approvalId,
            'is_read'         => 0,
        ]);
    }
}
