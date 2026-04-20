<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\EspOperationalReport;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
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

        $rows = $query->get()->keyBy(fn ($r) => Carbon::parse($r->jam_laporan)->format('H:i'));

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

        // Peta jam => baris Excel (sesuai template esp.xlsx)
        $jamRow = [
            '06:00' => 6,  '07:00' => 7,  '08:00' => 8,  '09:00' => 9,
            '10:00' => 10, '11:00' => 11, '12:00' => 12, '13:00' => 13,
            '14:00' => 14, '15:00' => 15, '16:00' => 16, '17:00' => 17,
            '18:00' => 18, '19:00' => 19, '20:00' => 20, '21:00' => 21,
            '22:00' => 22, '23:00' => 23, '00:00' => 24, '01:00' => 25,
            '02:00' => 26, '03:00' => 27, '04:00' => 28, '05:00' => 29,
        ];

        // Ambil data
        $query = EspOperationalReport::whereIn('tanggal_laporan', [$tanggal, $tanggalBerikut]);
        if ($grup) $query->where('grup', $grup);
        $rows = $query->get()->keyBy(fn ($r) => Carbon::parse($r->jam_laporan)->format('H:i'));

        // Load template
        $templatePath = storage_path('app/templates/esp.xlsx');
        $spreadsheet  = IOFactory::load($templatePath);
        $sheet        = $spreadsheet->getActiveSheet();

        // Isi tanggal
        $sheet->setCellValue('J1', $tanggal . ($grup ? ' | Grup ' . $grup : ''));

        // Isi data per jam
        foreach ($jamRow as $jam => $row) {
            $record = $rows->get($jam);
            if (!$record) continue;

            $sheet->setCellValue('B' . $row, $record->arus_primer);
            $sheet->setCellValue('C' . $row, $record->arus_sekunder);
            $sheet->setCellValue('D' . $row, $record->tegangan_primer);
            $sheet->setCellValue('E' . $row, $record->tegangan_sekunder);
            $sheet->setCellValue('F' . $row, $record->suhu_thermal);
        }

        // Stream download
        $filename = 'ESP_Operational_' . $tanggal . ($grup ? '_Grup' . $grup : '') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    
}
