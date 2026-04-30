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

    public function export(Request $request)
    {
        if ($request->bulan && str_contains((string) $request->bulan, '-')) {
            [$tahun, $bulan] = explode('-', $request->bulan);
            $request->merge(['bulan' => (int) $bulan, 'tahun' => (int) $tahun]);
        }

        $query = WaterSoftener::whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->orderBy('tanggal', 'asc');

        $data = $query->get();

        if ($data->isEmpty()) {
            return "<script>alert('Tidak ada data ditemukan untuk periode tersebut'); window.close();</script>";
        }

        $templatePath = public_path('assets/templates/operasional/water_softener.xlsx');
        if (!file_exists($templatePath)) {
            return "<script>alert('Template Water Softener tidak ditemukan'); window.close();</script>";
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Header Info (Misal di K1 & K2)
        $monthName = Carbon::create()->month($request->bulan)->translatedFormat('F');
        $sheet->setCellValue('N1', 'BULAN: ' . strtoupper($monthName) . ' ' . $request->tahun);

        foreach ($data as $item) {
            $day = Carbon::parse($item->tanggal)->day;
            $currentRow = 7 + ($day - 1);

            // WS 1 (B-E)
            $sheet->setCellValue('B' . $currentRow, $item->ws1_jam ? Carbon::parse($item->ws1_jam)->format('H:i') : '');
            $sheet->setCellValue('C' . $currentRow, $item->ws1_hardness_in);
            $sheet->setCellValue('D' . $currentRow, $item->ws1_hardness_out);
            $sheet->setCellValue('E' . $currentRow, $item->ws1_flow);

            // WS 2 (F-I)
            $sheet->setCellValue('F' . $currentRow, $item->ws2_jam ? Carbon::parse($item->ws2_jam)->format('H:i') : '');
            $sheet->setCellValue('G' . $currentRow, $item->ws2_hardness_in);
            $sheet->setCellValue('H' . $currentRow, $item->ws2_hardness_out);
            $sheet->setCellValue('I' . $currentRow, $item->ws2_flow);

            // Regen 1 (J-M)
            $sheet->setCellValue('J' . $currentRow, $item->regen1_jam ? Carbon::parse($item->regen1_jam)->format('H:i') : '');
            $sheet->setCellValue('K' . $currentRow, $item->regen1_air_pelarut);
            $sheet->setCellValue('L' . $currentRow, $item->regen1_garam);
            $sheet->setCellValue('M' . $currentRow, $item->regen1_nomer_ws);

            // Regen 2 (N-Q)
            $sheet->setCellValue('N' . $currentRow, $item->regen2_jam ? Carbon::parse($item->regen2_jam)->format('H:i') : '');
            $sheet->setCellValue('O' . $currentRow, $item->regen2_air_pelarut);
            $sheet->setCellValue('P' . $currentRow, $item->regen2_garam);
            $sheet->setCellValue('Q' . $currentRow, $item->regen2_nomer_ws);
        }

        // Approval Section
        $approval = WaterSoftenerApproval::where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->with(['operator', 'foreman', 'supervisor'])
            ->first();

        $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');
        if ($approval && file_exists($signaturePath)) {
            // Operator (A29)
            if ($approval->status != 'draft') {
                $drawOp = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawOp->setName('Operator');
                $drawOp->setPath($signaturePath);
                $drawOp->setHeight(60);
                $drawOp->setCoordinates('A29');
                $drawOp->setWorksheet($sheet);
                $sheet->setCellValue('A33', '(' . ($approval->operator ? $approval->operator->username : '-') . ')');
            }
            // Foreman (G29)
            if (in_array($approval->status, ['waiting_supervisor', 'approved_supervisor'])) {
                $drawFm = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawFm->setName('Foreman');
                $drawFm->setPath($signaturePath);
                $drawFm->setHeight(60);
                $drawFm->setCoordinates('G29');
                $drawFm->setWorksheet($sheet);
                $sheet->setCellValue('G33', '(' . ($approval->foreman ? $approval->foreman->username : '-') . ')');
            }
            // Supervisor (L29)
            if ($approval->status == 'approved_supervisor') {
                $drawSpv = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawSpv->setName('Supervisor');
                $drawSpv->setPath($signaturePath);
                $drawSpv->setHeight(60);
                $drawSpv->setCoordinates('L29');
                $drawSpv->setWorksheet($sheet);
                $sheet->setCellValue('L33', '(' . ($approval->supervisor ? $approval->supervisor->username : '-') . ')');
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'WaterSoftener_Report_' . now()->format('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
