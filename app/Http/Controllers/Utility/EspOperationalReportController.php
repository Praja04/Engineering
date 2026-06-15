<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\EspCoalHandover;
use App\Models\Utility\EspOperationalReport;
use App\Models\Utility\EspShiftReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EspOperationalReportController extends Controller
{
    // Jam shift 06:00 s/d 05:00
    private function shiftHours(): array
    {
        $hours = [];
        for ($h = 6; $h <= 23; $h++) $hours[] = sprintf('%02d:00', $h);
        for ($h = 0; $h <= 5; $h++) $hours[] = sprintf('%02d:00', $h);
        return $hours;
    }

    // Jam 00:00–05:59 → tanggal laporan = kemarin
    private function resolveDate(string $jam): string
    {
        $today = Carbon::today();
        return ($jam < '06:00')
            ? $today->subDay()->format('Y-m-d')
            : $today->format('Y-m-d');
    }

    // ── INDEX (form input) ───────────────────────────────────────────
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $grup    = $request->get('grup', '');

        return view('utility.esp-operational-report.form', compact('tanggal', 'grup'));
    }

    // ── DATA VIEW (rekap + export) ───────────────────────────────────
    public function dataView(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $grup    = $request->get('grup', '');

        return view('utility.esp-operational-report.data', compact('tanggal', 'grup'));
    }

    // ── STORE / UPDATE ───────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'jam_laporan'       => 'required|date_format:H:i',
            'grup'              => 'required|in:A,B,C,D',
            'arus_primer'       => 'nullable|numeric',
            'arus_sekunder'     => 'nullable|numeric',
            'tegangan_primer'   => 'nullable|numeric',
            'tegangan_sekunder' => 'nullable|numeric',
            'suhu_thermal'      => 'nullable|numeric',
        ]);

        $jam     = $request->jam_laporan;
        $tanggal = $this->resolveDate($jam);

        $data = EspOperationalReport::updateOrCreate(
            [
                'tanggal_laporan' => $tanggal,
                'jam_laporan'     => $jam,
                'grup'            => $request->grup,
            ],
            [
                'arus_primer'       => $request->arus_primer,
                'arus_sekunder'     => $request->arus_sekunder,
                'tegangan_primer'   => $request->tegangan_primer,
                'tegangan_sekunder' => $request->tegangan_sekunder,
                'suhu_thermal'      => $request->suhu_thermal,
            ]
        );

        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data'    => $data,
        ]);
    }

    // ── GET DATA (JSON untuk tabel & chart) ──────────────────────────
    public function getData(Request $request)
    {
        $tanggal        = $request->get('tanggal', Carbon::today()->format('Y-m-d'));

        $tanggalBerikut = Carbon::parse($tanggal)->addDay()->format('Y-m-d');

        $query = EspOperationalReport::whereIn('tanggal_laporan', [$tanggal, $tanggalBerikut]);

        if ($request->filled('grup')) {
            $query->where('grup', $request->grup);
        }

        $rows = $query->get()->keyBy(fn($r) => Carbon::parse($r->jam_laporan)->format('H:i'));

        $result = [];
        foreach ($this->shiftHours() as $jam) {
            $row      = $rows->get($jam);
            $result[] = [
                'jam'               => $jam,
                'grup'              => $row?->grup,
                'arus_primer'       => $row?->arus_primer,
                'arus_sekunder'     => $row?->arus_sekunder,
                'tegangan_primer'   => $row?->tegangan_primer,
                'tegangan_sekunder' => $row?->tegangan_sekunder,
                'suhu_thermal'      => $row?->suhu_thermal,
            ];
        }

        return response()->json($result);
    }

    // ── EXPORT — inject data ke template esp.xlsx ────────────────────
    public function export(Request $request)
    {
        $tanggal        = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $grup           = $request->get('grup');
        $tanggalBerikut = Carbon::parse($tanggal)->addDay()->format('Y-m-d');

        // ── Peta jam => baris Excel (kolom B–F, data operasional) ─────
        $jamRow = [
            '06:00' => 6,
            '07:00' => 7,
            '08:00' => 8,
            '09:00' => 9,
            '10:00' => 10,
            '11:00' => 11,
            '12:00' => 12,
            '13:00' => 13,
            '14:00' => 14,
            '15:00' => 15,
            '16:00' => 16,
            '17:00' => 17,
            '18:00' => 18,
            '19:00' => 19,
            '20:00' => 20,
            '21:00' => 21,
            '22:00' => 22,
            '23:00' => 23,
            '00:00' => 24,
            '01:00' => 25,
            '02:00' => 26,
            '03:00' => 27,
            '04:00' => 28,
            '05:00' => 29,
        ];

        // ── Ambil data operasional ─────────────────────────────────────
        $queryOp = EspOperationalReport::whereIn('tanggal_laporan', [$tanggal, $tanggalBerikut]);
        if ($grup) $queryOp->where('grup', $grup);
        $opRows = $queryOp->get()->keyBy(fn($r) => Carbon::parse($r->jam_laporan)->format('H:i'));

        // ── Ambil data shift untuk tanggal yang sama ───────────────────
        $shift = EspShiftReport::where('tanggal_laporan', $tanggal)
            ->with(['operator', 'foreman', 'supervisor'])
            ->latest()
            ->first();

        $coalHandover = EspCoalHandover::where('tanggal_laporan', $tanggal)
            ->with(['operator'])
            ->latest()
            ->first();

        // ── Load template ──────────────────────────────────────────────
        $templatePath = public_path('assets/templates/operasional/esp.xlsx');
        if (!file_exists($templatePath)) {
            return "<script>alert('Template ESP tidak ditemukan'); window.close();</script>";
        }

        $spreadsheet  = IOFactory::load($templatePath);
        $sheet        = $spreadsheet->getActiveSheet();

        // ── Restore stripped m³ equation symbols as plain text ────────────────
        $sheet->setCellValue('K5', 'm³');
        $sheet->setCellValue('K6', 'm³');
        $sheet->setCellValue('K12', 'm³');
        $sheet->getStyle('K5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // ── Isi tanggal & grup ─────────────────────────────────────────
        $sheet->setCellValue('J1', 'TANGGAL: ' . Carbon::parse($tanggal)->translatedFormat('d F Y'));
        if ($grup) {
            $sheet->setCellValue('J2', 'GRUP: ' . $grup);
        }

        // ── Isi data operasional per jam (kolom B–F) ───────────────────
        foreach ($jamRow as $jam => $row) {
            $record = $opRows->get($jam);
            if (!$record) continue;

            $sheet->setCellValue('B' . $row, $record->arus_primer);
            $sheet->setCellValue('C' . $row, $record->arus_sekunder);
            $sheet->setCellValue('D' . $row, $record->tegangan_primer);
            $sheet->setCellValue('E' . $row, $record->tegangan_sekunder);
            $sheet->setCellValue('F' . $row, $record->suhu_thermal);
        }

        // ── Isi data shift ───────────────────────────────────────────
        if ($shift || $coalHandover) {
            $sheet->setCellValue('J5',  $shift->pemakaian_air);
            $sheet->setCellValue('I7',  $shift->pemakaian_steam);
            $sheet->setCellValue('I8',  $shift->pemakaian_batubara);
            $sheet->setCellValue('I9',  $shift->efisiensi_batubara);
            $sheet->setCellValue('I11', $shift->running_hour_awal);
            $sheet->setCellValue('J11', $shift->running_hour_akhir);
            $sheet->setCellValue('I12', $shift->feed_tank_awal);
            $sheet->setCellValue('J12', $shift->feed_tank_akhir);
            $sheet->setCellValue('I13', $shift->pengisian_batubara);
            $sheet->setCellValue('I18', $shift->chemical_scf);
            $sheet->setCellValue('I19', $shift->chemical_srtf);
            $sheet->setCellValue('K18', $shift->dosis);
            $sheet->setCellValue('K19', $shift->dosis);

            $sheet->setCellValue('I22', $coalHandover->penyuplai_qty);
            $sheet->setCellValue('K22', $coalHandover->penyuplai_nik_nama);
            $sheet->setCellValue('I23', $coalHandover->penerima_qty);
            $sheet->setCellValue('K23', $coalHandover->penerima_nik_nama);
        }

        // ── TTD Approval Section ──────────────────────────────────────
        $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');
        if ($shift && file_exists($signaturePath)) {
            // Operator (A32)
            if ($shift->status != 'draft') {
                $drawOp = new Drawing();
                $drawOp->setName('Operator');
                $drawOp->setPath($signaturePath);
                $drawOp->setHeight(100);
                $drawOp->setCoordinates('B32');
                $drawOp->setWorksheet($sheet);
                $drawOp->setOffsetX(50);
                $drawOp->setOffsetY(20);
                $sheet->setCellValue('A35', ($shift->operator ? $shift->operator->username : '-'));
                $sheet->setCellValue('A36', ($shift->created_at ? $shift->created_at : '-'));
            }
            // Foreman (D32)
            if (in_array($shift->status, ['approved_foreman', 'approved_supervisor'])) {
                $drawFm = new Drawing();
                $drawFm->setName('Foreman');
                $drawFm->setPath($signaturePath);
                $drawFm->setHeight(100);
                $drawFm->setCoordinates('E32');
                $drawFm->setWorksheet($sheet);
                $drawFm->setOffsetY(20);
                $sheet->setCellValue('D35', ($shift->foreman ? $shift->foreman->username : '-'));
                $sheet->setCellValue('D36', ($shift->foreman_approved_at ? $shift->foreman_approved_at : '-'));
            }
            // Supervisor (H32)
            if ($shift->status == 'approved_supervisor') {
                $drawSpv = new Drawing();
                $drawSpv->setName('Supervisor');
                $drawSpv->setPath($signaturePath);
                $drawSpv->setHeight(100);
                $drawSpv->setCoordinates('I32');
                $drawSpv->setOffsetY(20);
                $drawSpv->setWorksheet($sheet);
                $sheet->setCellValue('H35', '(' . ($shift->supervisor ? $shift->supervisor->username : '-') . ')');
                $sheet->setCellValue('H36', ($shift->supervisor_approved_at ? $shift->supervisor_approved_at : '-'));
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'ESP_Report_' . $tanggal . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
