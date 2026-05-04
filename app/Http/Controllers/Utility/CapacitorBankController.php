<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\CapacitorBank;
use App\Models\Utility\CapacitorBankApproval;
use App\Models\NotificationsModel;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CapacitorBankController extends Controller
{
    public function index()
    {
        return view('utility.capacitor-bank.form');
    }

    public function approvalView()
    {
        return view('utility.capacitor-bank.approval');
    }

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
            'jam' => 'required',
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

        // 🚫 blokir jika sudah diajukan atau final disetujui
        $approval = CapacitorBankApproval::where('bulan', $tanggal->month)
            ->where('tahun', $tanggal->year)
            ->first();

        if ($approval && in_array($approval->status, ['waiting_supervisor', 'approved_supervisor'])) {
            return response()->json([
                'message' => 'Laporan bulan ini sudah diajukan/disetujui, tidak dapat diubah.'
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

        // Pastikan record approval ada (foreman_id diisi saat foreman submit)
        CapacitorBankApproval::firstOrCreate(
            ['bulan' => $tanggal->month, 'tahun' => $tanggal->year],
            [
                'status'      => 'draft',
                'operator_id' => auth()->id(),
                'submitted_at' => now(),
            ]
        );
        return response()->json([
            'message' => 'Data berhasil disimpan.',
            'data'    => $data
        ]);
    }

    /**
     * ✏️ UPDATE — Edit data harian
     */
    public function update(Request $request, $tanggal)
    {
        $request->validate([
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

        $carbon = Carbon::createFromFormat('Y-m-d', $tanggal);

        $approval = CapacitorBankApproval::where('bulan', $carbon->month)
            ->where('tahun', $carbon->year)
            ->first();

        if ($approval && in_array($approval->status, ['waiting_supervisor', 'approved_supervisor'])) {
            return response()->json([
                'message' => 'Laporan bulan ini sudah diajukan/disetujui, tidak dapat diubah.'
            ], 422);
        }

        $data = CapacitorBank::where('tanggal', $tanggal)->first();
        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $data->update($request->all());

        return response()->json([
            'message' => 'Data berhasil diupdate.',
            'data'    => $data
        ]);
    }

    // =========================================================
    // SUBMIT — FOREMAN AJUKAN KE SUPERVISOR
    // =========================================================

    /**
     * 📤 Foreman submit laporan bulan penuh ke supervisor.
     */
    public function submitBulan(Request $request)
    {
        $request->validate([
            'bulan'         => 'required|integer|between:1,12',
            'tahun'         => 'required|integer',
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $approval = CapacitorBankApproval::where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->firstOrFail();

        if ($approval->status !== 'draft') {
            return response()->json(['message' => 'Laporan sudah diajukan sebelumnya.'], 422);
        }
        $jumlahTerisi = CapacitorBank::whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->count();



        if ($jumlahTerisi === 0) {
            return response()->json([
                'message' => "Tidak ada data untuk bulan {$request->bulan}/{$request->tahun}. Minimal satu data harus diinput sebelum mengajukan."
            ], 422);
        }

        $approval->update([
            'foreman_id'    => auth()->id(),
            'supervisor_id' => $request->supervisor_id,
            'status'        => 'waiting_supervisor',
            'foreman_approved_at'  => now(),
        ]);

        $this->kirimNotifikasi(
            $request->supervisor_id,
            'Approval Capacitor Bank',
            "Laporan Capacitor Bank bulan {$request->bulan}/{$request->tahun} menunggu persetujuan Anda.",
            $approval->id
        );

        return response()->json([
            'message' => 'Laporan berhasil diajukan ke Supervisor.'
        ]);
    }

    // =========================================================
    // APPROVAL — SUPERVISOR FINAL
    // =========================================================

    /**
     * ✅ Supervisor setujui laporan (final).
     */
    public function approveSupervisor($id)
    {
        $approval = CapacitorBankApproval::findOrFail($id);

        if ((int) $approval->supervisor_id !== (int) auth()->id()) {
            return response()->json(['message' => 'Anda tidak berwenang menyetujui laporan ini.'], 403);
        }

        if ($approval->status !== 'waiting_supervisor') {
            return response()->json([
                'message' => 'Status laporan tidak valid. Status saat ini: ' . $approval->status
            ], 422);
        }

        $approval->update([
            'status'                 => 'approved_supervisor',
            'supervisor_approved_at' => now(),
        ]);

        NotificationsModel::where('notifiable_type', CapacitorBankApproval::class)
            ->where('notifiable_id', $approval->id)
            ->where('user_id', auth()->id()) // opsional (biar spesifik)
            ->delete();

        return response()->json([
            'message' => "Laporan bulan {$approval->bulan}/{$approval->tahun} telah disetujui (Final)."
        ]);
    }

    // =========================================================
    // DATA
    // =========================================================

    public function getData(Request $request)
    {
        if ($request->bulan && str_contains($request->bulan, '-')) {
            [$tahun, $bulan] = explode('-', $request->bulan);
            $request->merge(['bulan' => (int) $bulan, 'tahun' => (int) $tahun]);
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
            ->with(['foreman', 'supervisor'])
            ->first();

        return response()->json([
            'data'     => $data,
            'approval' => $approval
        ]);
    }

    public function getApprovalList(Request $request)
    {
        $query   = CapacitorBankApproval::with(['foreman', 'supervisor']);
        $jabatan = auth()->user()->jabatan;
        $tab     = $request->tab ?? 'pending';

        if ($jabatan === 'supervisor') {
            $query->where('supervisor_id', auth()->id());

            if ($tab === 'pending') {
                $query->where('status', 'waiting_supervisor');
            } else {
                $query->where('status', 'approved_supervisor');
            }
        } else {
            // Foreman: lihat laporan yang dia buat/ajukan
            $query->where('foreman_id', auth()->id());

            if ($tab === 'pending') {
                $query->whereIn('status', ['draft', 'waiting_supervisor']);
            } else {
                $query->where('status', 'approved_supervisor');
            }
        }

        return response()->json(
            $query->orderByDesc('tahun')->orderByDesc('bulan')->get()
        );
    }

    // =========================================================
    // EXPORT EXCEL
    // =========================================================

    /**
     * 📥 Export laporan bulan ke Excel (.xlsx).
     *
     * Route contoh:
     *   GET /utility/capacitor-bank/export?bulan=2025-04
     *
     * Jika laporan sudah approved_supervisor, baris tanda-tangan
     * (gambar TTD + nama + timestamp) akan otomatis disisipkan.
     */
    public function exportExcel(Request $request)
    {
        if ($request->bulan && str_contains($request->bulan, '-')) {
            [$tahun, $bulan] = explode('-', $request->bulan);
            $request->merge(['bulan' => (int) $bulan, 'tahun' => (int) $tahun]);
        }

        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer',
        ]);

        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;

        // ── Ambil data dari DB ────────────────────────────────
        $rows = CapacitorBank::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->get()
            ->keyBy(fn($r) => (int) Carbon::parse($r->tanggal)->format('j'));

        $approval = CapacitorBankApproval::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->with(['operator', 'foreman', 'supervisor'])
            ->first();

        // ── Load template ─────────────────────────────────────
        $templatePath = public_path('assets/templates/operasional/capacitor_bank.xlsx');
        if (!file_exists($templatePath)) {
            return "<script>alert('Template tidak ditemukan di: {$templatePath}'); window.history.back();</script>";
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet       = $spreadsheet->getActiveSheet();

        // ── Isi header bulan & tahun ──────────────────────────
        $bulanNames = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $sheet->setCellValue('P1', 'Bulan : ' . $bulanNames[$bulan]);
        $sheet->setCellValue('P3', 'Tahun : ' . $tahun);

        // ── Style helpers ─────────────────────────────────────
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $centerAlign = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ];

        // ── Isi baris data (hari 1–31, mulai row 7) ───────────
        // Kolom: A=Tanggal, B=Jam, C=Arus Total
        //        D=cap_a_nomor, E=cap_a_i1, F=cap_a_i2, G=cap_a_i3
        //        H=cap_b_nomor, I=cap_b_i1, J=cap_b_i2, K=cap_b_i3
        //        L=cap_c_nomor, M=cap_c_i1, N=cap_c_i2, O=cap_c_i3
        //        P=Suhu Ruang,  Q=Pelaksana (operator), R=Staff (foreman)

        for ($day = 1; $day <= 31; $day++) {
            $excelRow = $day + 6;   // hari 1 → row 7, dst.
            $range    = "A{$excelRow}:R{$excelRow}";

            $sheet->getStyle($range)->applyFromArray($borderStyle);
            $sheet->getStyle($range)->applyFromArray($centerAlign);
            $sheet->setCellValue("A{$excelRow}", $day);

            if (isset($rows[$day])) {
                $r = $rows[$day];

                $sheet->setCellValue("B{$excelRow}", $r->jam);
                $sheet->setCellValue("C{$excelRow}", $r->arus_total);

                $sheet->setCellValue("D{$excelRow}", $r->cap_a_nomor);
                $sheet->setCellValue("E{$excelRow}", $r->cap_a_i1);
                $sheet->setCellValue("F{$excelRow}", $r->cap_a_i2);
                $sheet->setCellValue("G{$excelRow}", $r->cap_a_i3);

                $sheet->setCellValue("H{$excelRow}", $r->cap_b_nomor);
                $sheet->setCellValue("I{$excelRow}", $r->cap_b_i1);
                $sheet->setCellValue("J{$excelRow}", $r->cap_b_i2);
                $sheet->setCellValue("K{$excelRow}", $r->cap_b_i3);

                $sheet->setCellValue("L{$excelRow}", $r->cap_c_nomor);
                $sheet->setCellValue("M{$excelRow}", $r->cap_c_i1);
                $sheet->setCellValue("N{$excelRow}", $r->cap_c_i2);
                $sheet->setCellValue("O{$excelRow}", $r->cap_c_i3);

                $sheet->setCellValue("P{$excelRow}", $r->suhu_ruang);

                if ($approval) {
                    $sheet->setCellValue("Q{$excelRow}", $approval->operator?->username ?? '-');
                    $sheet->setCellValue("R{$excelRow}", $approval->foreman?->username ?? '-');
                }
            }
        }

        // ── Tanda tangan (hanya jika approved_supervisor) ─────
        if ($approval && $approval->status === 'approved_supervisor') {
            $this->insertSignatureSection($sheet, $approval);
        }

        // ── Stream ke browser ─────────────────────────────────
        $filename = "CapacitorBank_{$bulanNames[$bulan]}_{$tahun}.xlsx";
        $writer   = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // =========================================================
    // HELPER: sisipkan baris tanda tangan
    // =========================================================

    private function insertSignatureSection($sheet, CapacitorBankApproval $approval): void
    {
        $labelRow = 39;
        $imgStart = 40;
        $imgEnd   = 43;
        $nameRow  = 44;
        $tsRow    = 45;

        $basePath = public_path('storage/operasional/ttd');

        // Helper ambil username dari relasi User
        $getName = fn($user) => $user?->username ?? '-';

        $signatories = [
            [
                'colStart'  => 'A',
                'colEnd'    => 'F',
                'label'     => 'Operator / Pelaksana',
                'ttdFile'   => $basePath . '/ttd_teknisi.jpeg',
                'name'      => $getName($approval->operator),
                'timestamp' => $approval->submitted_at?->format('d/m/Y H:i') ?? '-',
            ],
            [
                'colStart'  => 'G',
                'colEnd'    => 'L',
                'label'     => 'Foreman',
                'ttdFile'   => $basePath . '/ttd_staff.jpeg',
                'name'      => $getName($approval->foreman),
                'timestamp' => $approval->foreman_approved_at?->format('d/m/Y H:i') ?? '-',
            ],
            [
                'colStart'  => 'M',
                'colEnd'    => 'R',
                'label'     => 'Supervisor',
                'ttdFile'   => $basePath . '/ttd_user_eng.jpeg',
                'name'      => $getName($approval->supervisor),
                'timestamp' => $approval->supervisor_approved_at?->format('d/m/Y H:i') ?? '-',
            ],
        ];

        $borderThin = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $centerStyle = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ];

        // Set tinggi baris
        for ($r = $imgStart; $r <= $imgEnd; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(35);
        }
        $sheet->getRowDimension($labelRow)->setRowHeight(20);
        $sheet->getRowDimension($nameRow)->setRowHeight(20);
        $sheet->getRowDimension($tsRow)->setRowHeight(18);

        foreach ($signatories as $sig) {
            $cs = $sig['colStart'];
            $ce = $sig['colEnd'];

            // ── Label role ─────────────────────────────────────
            $labelRange = "{$cs}{$labelRow}:{$ce}{$labelRow}";
            $sheet->mergeCells($labelRange);
            $sheet->setCellValue("{$cs}{$labelRow}", $sig['label']);
            $sheet->getStyle($labelRange)->applyFromArray($borderThin);
            $sheet->getStyle($labelRange)->applyFromArray($centerStyle);
            $sheet->getStyle("{$cs}{$labelRow}")->getFont()->setBold(true);

            // ── Area gambar TTD ────────────────────────────────
            $imgRange = "{$cs}{$imgStart}:{$ce}{$imgEnd}";
            $sheet->mergeCells($imgRange);
            $sheet->getStyle($imgRange)->applyFromArray($borderThin);
            $sheet->getStyle($imgRange)->applyFromArray($centerStyle);

            if (file_exists($sig['ttdFile'])) {
                $drawing = new Drawing();
                $drawing->setName('TTD_' . $sig['label']);
                $drawing->setDescription('Tanda Tangan ' . $sig['label']);
                $drawing->setPath($sig['ttdFile']);
                $drawing->setCoordinates("{$cs}{$imgStart}");
                $drawing->setWidth(120);
                $drawing->setHeight(100);
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);
            }

            // ── Nama (username) ────────────────────────────────
            $nameRange = "{$cs}{$nameRow}:{$ce}{$nameRow}";
            $sheet->mergeCells($nameRange);
            $sheet->setCellValue("{$cs}{$nameRow}", $sig['name']);
            $sheet->getStyle($nameRange)->applyFromArray($borderThin);
            $sheet->getStyle($nameRange)->applyFromArray($centerStyle);
            $sheet->getStyle("{$cs}{$nameRow}")->getFont()->setBold(true);

            // ── Timestamp ──────────────────────────────────────
            $tsRange = "{$cs}{$tsRow}:{$ce}{$tsRow}";
            $sheet->mergeCells($tsRange);
            $sheet->setCellValue("{$cs}{$tsRow}", $sig['timestamp']);
            $sheet->getStyle($tsRange)->applyFromArray($borderThin);
            $sheet->getStyle($tsRange)->applyFromArray($centerStyle);
            $sheet->getStyle("{$cs}{$tsRow}")->getFont()
                ->setItalic(true)
                ->setSize(9)
                ->getColor()->setARGB('FF666666');
        }
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
