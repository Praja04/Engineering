<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Maintenance\MtcMasterMesinModel;
use App\Models\Maintenance\MtcMesinFrekuensiModel;
use App\Http\Requests\Maintenance\MtcMasterMesinRequest;
use App\Http\Requests\Maintenance\MtcMasterMesinUploadRequest;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MtcMasterMesinController extends Controller
{
    private const VALID_SATUAN = ['hari', 'minggu', 'bulan', 'tahun'];

    public function index()
    {
        return view('maintenance.master.master_mesin');
    }

    // ── CRUD ─────────────────────────────────────────────────────────────────

    public function store(MtcMasterMesinRequest $request)
    {
        DB::transaction(function () use ($request) {
            $mesin = MtcMasterMesinModel::create([
                'jenis_mtc'  => $request->jenis_mtc,
                'nama_mesin' => $request->nama_mesin,
                'lokasi'     => $request->lokasi,
                'dept'       => $request->dept ?? null,
                'kode_mesin' => $request->kode_mesin ?? null,
                'aktif'      => 1,
                'created_by' => Auth::id(),
            ]);

            // frekuensi dikirim sebagai array: [{ interval: 1, satuan: 'bulan' }, ...]
            $this->syncFrekuensi($mesin->id, $request->frekuensi ?? []);
        });

        return response()->json(['status' => true, 'message' => 'Data mesin berhasil disimpan']);
    }

    public function getData()
    {
        $data = MtcMasterMesinModel::with('frekuensi')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($item) => [
                'id'             => $item->id,
                'jenis_mtc'      => $item->jenis_mtc,
                'nama_mesin'     => $item->nama_mesin,
                'lokasi'         => $item->lokasi,
                'dept'           => $item->dept,
                'kode_mesin'     => $item->kode_mesin,
                'aktif'          => $item->aktif,
                'created_at'     => $item->created_at,
                // [{ interval: 2, satuan: 'bulan', label: '2 Bulan' }, ...]
                'frekuensi_list' => $item->frekuensi->map(fn($f) => [
                    'interval' => $f->interval,
                    'satuan'   => $f->satuan,
                    'label'    => $f->label,
                ])->toArray(),
            ]);

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function update(MtcMasterMesinRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $mesin = MtcMasterMesinModel::findOrFail($id);

            $mesin->update([
                'jenis_mtc'  => $request->jenis_mtc,
                'nama_mesin' => $request->nama_mesin,
                'lokasi'     => $request->lokasi,
                'dept'       => $request->dept ?? null,
                'kode_mesin' => $request->kode_mesin ?? null,
                'aktif'      => $request->aktif,
                'updated_by' => Auth::id(),
            ]);

            $this->syncFrekuensi($mesin->id, $request->frekuensi ?? []);
        });

        return response()->json(['status' => true, 'message' => 'Data mesin berhasil diupdate']);
    }

    public function destroy($id)
    {
        DB::transaction(fn() => MtcMasterMesinModel::findOrFail($id)->delete());

        return response()->json(['status' => true, 'message' => 'Data mesin berhasil dihapus']);
    }

    // ── Download template Excel ───────────────────────────────────────────────

    public function downloadTemplate()
    {
        $path = storage_path('app/templates/template_master_mesin.xlsx');
        $dir = dirname($path);

        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }

        if (!file_exists($path)) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers/instructions
            $sheet->setCellValue('A1', 'TEMPLATE IMPORT MASTER MESIN');
            $sheet->mergeCells('A1:H1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            $sheet->setCellValue('A2', 'Petunjuk: Mulai isi data di Baris 5. Kolom B, C, dan D wajib diisi.');
            $sheet->mergeCells('A2:H2');

            $sheet->setCellValue('A3', 'Frekuensi dipisah koma (contoh: 1 Hari, 2 Minggu, 1 Bulan, 6 Bulan). Aktif diisi: Ya/Tidak atau Aktif/Non Aktif.');
            $sheet->mergeCells('A3:H3');

            // Table headers in row 4
            $sheet->setCellValue('A4', 'No');
            $sheet->setCellValue('B4', 'Jenis MTC *');
            $sheet->setCellValue('C4', 'Nama Mesin *');
            $sheet->setCellValue('D4', 'Lokasi *');
            $sheet->setCellValue('E4', 'Dept');
            $sheet->setCellValue('F4', 'Kode Mesin');
            $sheet->setCellValue('G4', 'Frekuensi Perawatan');
            $sheet->setCellValue('H4', 'Status Aktif');

            // Apply bold to headers
            $sheet->getStyle('A4:H4')->getFont()->setBold(true);

            // Add sample data in Row 5
            $sheet->setCellValue('A5', '1');
            $sheet->setCellValue('B5', 'Utility');
            $sheet->setCellValue('C5', 'Kompresor A');
            $sheet->setCellValue('D5', 'Ruang Utility');
            $sheet->setCellValue('E5', 'Engineering');
            $sheet->setCellValue('F5', 'UTL-COMP-A');
            $sheet->setCellValue('G5', '1 Bulan, 6 Bulan');
            $sheet->setCellValue('H5', 'Aktif');

            // Add sample data in Row 6
            $sheet->setCellValue('A6', '2');
            $sheet->setCellValue('B6', 'Electrical');
            $sheet->setCellValue('C6', 'Panel Listrik Utama');
            $sheet->setCellValue('D6', 'Gedung Produksi');
            $sheet->setCellValue('E6', 'Engineering');
            $sheet->setCellValue('F6', 'ELC-PAN-MAIN');
            $sheet->setCellValue('G6', '1 Minggu, 1 Tahun');
            $sheet->setCellValue('H6', 'Aktif');

            // Auto fit column widths
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($path);
        }

        return response()->download($path, 'template_master_mesin_maintenance.xlsx');
    }

    // ── Upload & import Excel ─────────────────────────────────────────────────

    public function uploadExcel(MtcMasterMesinUploadRequest $request)
    {
        $rows = IOFactory::load($request->file('file_excel')->getRealPath())
            ->getActiveSheet()
            ->toArray(null, true, true, true);

        // Kolom: A=No, B=jenis_mtc, C=nama_mesin, D=lokasi,
        //        E=dept, F=kode_mesin, G=frekuensi (pisah koma: "1 Bulan,6 Bulan"), H=aktif
        $dataStart = 5;
        $inserted  = 0;
        $skipped   = 0;
        $errors    = [];

        DB::transaction(function () use ($rows, $dataStart, &$inserted, &$skipped, &$errors) {
            foreach ($rows as $rowNum => $row) {
                if ($rowNum < $dataStart) continue;

                $jenisMtc  = trim((string) ($row['B'] ?? ''));
                $namaMesin = trim((string) ($row['C'] ?? ''));
                $lokasi    = trim((string) ($row['D'] ?? ''));
                $dept      = trim((string) ($row['E'] ?? ''));
                $kodeMesin = trim((string) ($row['F'] ?? ''));
                $frekRaw   = trim((string) ($row['G'] ?? ''));
                $aktifRaw  = trim((string) ($row['H'] ?? ''));

                if ($jenisMtc === '' && $namaMesin === '' && $lokasi === '') continue;

                $rowErrors = [];
                if ($jenisMtc === '')  $rowErrors[] = 'Jenis MTC wajib diisi';
                if ($namaMesin === '') $rowErrors[] = 'Nama Mesin wajib diisi';

                $jenisMtcLower = strtolower($jenisMtc);
                $isElectrical = str_contains($jenisMtcLower, 'Electrical') || str_contains($jenisMtcLower, 'electrical');

                if ($lokasi === '' && !$isElectrical) {
                    $rowErrors[] = 'Lokasi wajib diisi';
                }

                if ($lokasi === '') {
                    $lokasi = '-';
                }

                // Parse frekuensi: "1 Bulan, 6 Bulan" → [['interval'=>1,'satuan'=>'bulan'], ...]
                $frekuensiList = [];
                if ($frekRaw !== '') {
                    foreach (explode(',', $frekRaw) as $item) {
                        $parsed = $this->parseFrekuensiString(trim($item));
                        if ($parsed) {
                            $frekuensiList[] = $parsed;
                        } else {
                            $rowErrors[] = "Frekuensi '{$item}' tidak dikenali "
                                . "(contoh: 1 Hari, 2 Minggu, 1 Bulan, 6 Bulan, 1 Tahun)";
                        }
                    }
                }

                // Parse aktif
                $aktifNorm = strtolower($aktifRaw);
                if (in_array($aktifNorm, ['1', 'true', 'yes', 'aktif', 'ya'], true)) {
                    $aktif = 1;
                } elseif (in_array($aktifNorm, ['0', 'false', 'no', 'tidak', 'non aktif', 'nonaktif'], true)) {
                    $aktif = 0;
                } elseif ($aktifRaw === '') {
                    $aktif = 1;
                } else {
                    $rowErrors[] = "Aktif tidak valid ({$aktifRaw})";
                    $aktif = null;
                }

                if (!empty($rowErrors)) {
                    $errors[] = ['baris' => $rowNum, 'masalah' => implode(', ', $rowErrors)];
                    $skipped++;
                    continue;
                }

                $mesin = null;
                if ($kodeMesin !== '') {
                    $mesin = MtcMasterMesinModel::where('kode_mesin', $kodeMesin)->first();
                }

                if ($mesin) {
                    $mesin->update([
                        'jenis_mtc'  => $jenisMtc,
                        'nama_mesin' => $namaMesin,
                        'lokasi'     => $lokasi,
                        'dept'       => $dept !== '' ? $dept : null,
                        'aktif'      => $aktif,
                        'updated_by' => Auth::id(),
                    ]);
                } else {
                    $mesin = MtcMasterMesinModel::create([
                        'jenis_mtc'  => $jenisMtc,
                        'nama_mesin' => $namaMesin,
                        'lokasi'     => $lokasi,
                        'dept'       => $dept !== '' ? $dept : null,
                        'kode_mesin' => $kodeMesin !== '' ? $kodeMesin : null,
                        'aktif'      => $aktif,
                        'created_by' => Auth::id(),
                    ]);
                }

                $this->syncFrekuensi($mesin->id, $frekuensiList);
                $inserted++;
            }
        });

        $message = "{$inserted} data berhasil diimport.";
        if ($skipped > 0) $message .= " {$skipped} baris dilewati karena ada kesalahan.";

        return response()->json([
            'status'   => true,
            'message'  => $message,
            'inserted' => $inserted,
            'skipped'  => $skipped,
            'errors'   => $errors,
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Sync frekuensi: hapus lama, insert baru.
     * $list = [['interval' => 1, 'satuan' => 'bulan'], ...]
     */
    private function syncFrekuensi(int $mesinId, array $list): void
    {
        MtcMesinFrekuensiModel::where('mesin_id', $mesinId)->delete();

        $seen = [];
        foreach ($list as $item) {
            $interval = 1;
            $satuan   = '';

            if (is_string($item)) {
                $parsed = $this->parseFrekuensiString($item);
                if (!$parsed) continue;
                $interval = $parsed['interval'];
                $satuan   = $parsed['satuan'];
            } else {
                $interval = max(1, (int) ($item['interval'] ?? 1));
                $satuan   = strtolower(trim($item['satuan'] ?? ''));
            }

            if (!in_array($satuan, self::VALID_SATUAN, true)) continue;

            $key = "{$interval}-{$satuan}";
            if (isset($seen[$key])) continue; // skip duplikat

            $seen[$key] = true;

            MtcMesinFrekuensiModel::create([
                'mesin_id' => $mesinId,
                'interval' => $interval,
                'satuan'   => $satuan,
            ]);
        }
    }

    /**
     * Parse string bebas dari Excel → ['interval' => int, 'satuan' => string]
     *
     * Format yang didukung:
     *   "1 Bulan", "2 Bulan", "6 Bulan", "1 Minggu", "2 Minggu",
     *   "1 Hari", "1 Tahun", "bulanan", "mingguan", "harian", "tahunan"
     */
    private function parseFrekuensiString(string $input): ?array
    {
        $val = strtolower(trim(preg_replace('/\s+/', ' ', $input)));

        // Format: "<angka> <satuan>" — contoh: "2 bulan", "6 bulan", "1 minggu"
        if (preg_match('/^(\d+)\s*(hari|minggu|bulan|tahun)s?$/i', $val, $m)) {
            return ['interval' => (int) $m[1], 'satuan' => strtolower($m[2])];
        }

        // Kata tunggal — anggap interval = 1
        $wordMap = [
            'harian'   => 'hari',
            'hari'    => 'hari',
            'daily'  => 'hari',
            'mingguan' => 'minggu',
            'minggu'  => 'minggu',
            'weekly' => 'minggu',
            'bulanan'  => 'bulan',
            'bulan'   => 'bulan',
            'monthly' => 'bulan',
            'tahunan'  => 'tahun',
            'tahun'   => 'tahun',
            'yearly' => 'tahun',
            'annual' => 'tahun',
        ];

        if (isset($wordMap[$val])) {
            return ['interval' => 1, 'satuan' => $wordMap[$val]];
        }

        return null;
    }
}
