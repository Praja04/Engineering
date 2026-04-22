<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\EspOperationalReport;
use App\Models\Utility\EspShiftReport;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
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

        // ── Peta jam => baris Excel (kolom B–F, data operasional) ─────
        $jamRow = [
            '06:00' => 6,  '07:00' => 7,  '08:00' => 8,  '09:00' => 9,
            '10:00' => 10, '11:00' => 11, '12:00' => 12, '13:00' => 13,
            '14:00' => 14, '15:00' => 15, '16:00' => 16, '17:00' => 17,
            '18:00' => 18, '19:00' => 19, '20:00' => 20, '21:00' => 21,
            '22:00' => 22, '23:00' => 23, '00:00' => 24, '01:00' => 25,
            '02:00' => 26, '03:00' => 27, '04:00' => 28, '05:00' => 29,
        ];

        // ── Ambil data operasional ─────────────────────────────────────
        $queryOp = EspOperationalReport::whereIn('tanggal_laporan', [$tanggal, $tanggalBerikut]);
        if ($grup) $queryOp->where('grup', $grup);
        $opRows = $queryOp->get()->keyBy(fn ($r) => Carbon::parse($r->jam_laporan)->format('H:i'));

        // ── Ambil data shift untuk tanggal yang sama ───────────────────
        // Shift report hanya per tanggal (1 record/hari)
        $shiftQuery = EspShiftReport::where('tanggal_laporan', $tanggal);
        if ($grup) {
            // Jika filter grup aktif, ambil shift berdasarkan grup operator jika ada field-nya
            // Kalau tidak ada, tetap ambil record hari itu
        }
        $shift = $shiftQuery->with(['operator', 'foreman', 'supervisor'])->latest()->first();

        // ── Load template ──────────────────────────────────────────────
        $templatePath = storage_path('app/templates/esp.xlsx');
        $spreadsheet  = IOFactory::load($templatePath);
        $sheet        = $spreadsheet->getActiveSheet();

        // ── Isi tanggal & grup ─────────────────────────────────────────
        $sheet->setCellValue('J1', $tanggal . ($grup ? ' | Grup ' . $grup : ''));

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

        // ── Isi data shift (kolom H–K) ─────────────────────────────────
        // Mapping sesuai template esp.xlsx:
        //   I5  = Total pemakaian air (HMI)
        //   I7  = Total pemakaian steam
        //   I8  = Total pemakaian batu bara
        //   I9  = Efisiensi batu bara
        //   I11 = Running Hour (Awal), J11 = Running Hour (Akhir)
        //   I12 = Pengisian Air Feedtank (Awal), J12 = (Akhir)
        //   I13 = Pengisian Batu bara
        //   I18 = BWT SCF (Volume Chemical)
        //   I19 = BWT SRTF (Volume Chemical)
        //   K18 = Dosis
        if ($shift) {
            $sheet->setCellValue('J5',  $shift->pemakaian_air);
            $sheet->setCellValue('J6',  $shift->pemakaian_air);
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
        }

        // ── Insert TTD (jika shift sudah diapprove) ────────────────────
        // Path gambar TTD:
        //   Operator   : storage/operasional/ttd/ttd_teknisi.jpeg
        //   Foreman    : storage/operasional/ttd/ttd_staff.jpeg
        //   Supervisor : storage/operasional/ttd/ttd_user_eng.jpeg
        //
        // Area TTD di template (merged cells):
        //   A32:C34  = Dibuat oleh (Operator)
        //   D32:G34  = Diperiksa oleh (Foreman/Staff)
        //   H32:K34  = Disetujui oleh (Supervisor)

        $ttdBasePath = public_path('storage/operasional/ttd');

        $ttdConfig = [
            // [ status_required, image_file, anchor_col, anchor_row, width_px, height_px ]
            'operator'   => [
                'file'       => $ttdBasePath . '/ttd_teknisi.jpeg',
                'anchor'     => 'A32',
                'col_offset' => 1,   // offset in EMU from anchor col (small margin)
                'row_offset' => 5,
                'width'      => 100,
                'height'     => 50,
            ],
            'foreman'    => [
                'file'       => $ttdBasePath . '/ttd_staff.jpeg',
                'anchor'     => 'D32',
                'col_offset' => 1,
                'row_offset' => 5,
                'width'      => 100,
                'height'     => 50,
            ],
            'supervisor' => [
                'file'       => $ttdBasePath . '/ttd_user_eng.jpeg',
                'anchor'     => 'H32',
                'col_offset' => 1,
                'row_offset' => 5,
                'width'      => 100,
                'height'     => 50,
            ],
        ];

        // Tentukan TTD mana yang perlu dimasukkan berdasarkan status approval
        $insertTtd = [];

        if ($shift) {
            // Operator selalu ada jika ada shift record (status minimal approved_operator)
            $insertTtd[] = 'operator';

            if (in_array($shift->status, ['approved_foreman', 'approved_supervisor'])) {
                $insertTtd[] = 'foreman';
            }

            if ($shift->status === 'approved_supervisor') {
                $insertTtd[] = 'supervisor';
            }
        }

        foreach ($insertTtd as $role) {
            $cfg  = $ttdConfig[$role];
            $file = $cfg['file'];

            if (!file_exists($file)) continue;

            $drawing = new Drawing();
            $drawing->setName('TTD ' . ucfirst($role));
            $drawing->setDescription('Tanda tangan ' . $role);
            $drawing->setPath($file);
            $drawing->setCoordinates($cfg['anchor']);
            $drawing->setOffsetX($cfg['col_offset']);
            $drawing->setOffsetY($cfg['row_offset']);
            $drawing->setWidth($cfg['width']);
            $drawing->setHeight($cfg['height']);
            $drawing->setWorksheet($sheet);
        }

        // ── Stream download ────────────────────────────────────────────
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
