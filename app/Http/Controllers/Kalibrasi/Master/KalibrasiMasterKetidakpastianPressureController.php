<?php

namespace App\Http\Controllers\Kalibrasi\Master;

use App\Http\Controllers\Controller;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\Master\MasterKetidakpastianPressureModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class KalibrasiMasterKetidakpastianPressureController extends Controller
{
    /**
     * Tampilkan halaman master ketidakpastian pressure.
     */
    public function viewKetidakpastian()
    {
        $alatList = AlatKalibrasiModel::where('jenis_kalibrasi', 'pressure')
            ->orderBy('kode_alat')
            ->get(['id', 'kode_alat', 'nama_alat', 'kapasitas', 'resolusi']);

        return view('kalibrasi.master.ketidakpastian_pressure', compact('alatList'));
    }

    /**
     * Ambil data JSON untuk tabel / datatable.
     */
    public function index(Request $request)
    {
        $query = MasterKetidakpastianPressureModel::with('alat')
            ->orderBy('kode_alat')
            ->orderBy('titik_kalibrasi');

        if ($request->filled('alat_id')) {
            $query->where('alat_id', $request->alat_id);
        }

        if ($request->filled('kode_alat')) {
            $query->where('kode_alat', $request->kode_alat);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_alat', 'like', "%{$search}%")
                  ->orWhere('nama_alat', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('titik_kalibrasi', 'like', "%{$search}%");
            });
        }

        $data = $query->get();

        if ($request->ajax()) {
            return response()->json($data);
        }

        return response()->json($data);
    }

    /**
     * Simpan data master baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'alat_id'              => 'nullable|exists:alat_kalibrasi,id',
            'kode_alat'            => 'required|string|max:100',
            'titik_kalibrasi'      => 'required|numeric',
            'nilai_ketidakpastian' => 'required|numeric|min:0',
            'keterangan'           => 'nullable|string|max:255',
        ], [
            'kode_alat.required'            => 'Kode alat wajib dipilih atau diisi.',
            'titik_kalibrasi.required'      => 'Titik kalibrasi wajib diisi.',
            'titik_kalibrasi.numeric'       => 'Titik kalibrasi harus berupa angka.',
            'nilai_ketidakpastian.required' => 'Nilai ketidakpastian wajib diisi.',
            'nilai_ketidakpastian.numeric'  => 'Nilai ketidakpastian harus berupa angka.',
        ]);

        // Cek duplikasi kode_alat + titik_kalibrasi
        $exists = MasterKetidakpastianPressureModel::where('kode_alat', $validated['kode_alat'])
            ->where('titik_kalibrasi', $validated['titik_kalibrasi'])
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => "Ketidakpastian untuk alat {$validated['kode_alat']} pada titik {$validated['titik_kalibrasi']} sudah ada.",
            ], 422);
        }

        // Auto-resolve nama_alat & alat_id jika salah satunya kosong
        if (!empty($validated['alat_id'])) {
            $alat = AlatKalibrasiModel::find($validated['alat_id']);
            if ($alat) {
                $validated['nama_alat'] = $alat->nama_alat;
                $validated['kode_alat'] = $alat->kode_alat;
            }
        } elseif (!empty($validated['kode_alat'])) {
            $alat = AlatKalibrasiModel::where('kode_alat', $validated['kode_alat'])->first();
            if ($alat) {
                $validated['alat_id'] = $alat->id;
                $validated['nama_alat'] = $alat->nama_alat;
            }
        }

        // Estimasi u_naik dan u_turun jika tidak disediakan
        $uGabungan = (float) $validated['nilai_ketidakpastian'];
        $validated['u_naik'] = $uGabungan / sqrt(2);
        $validated['u_turun'] = $uGabungan / sqrt(2);

        $record = MasterKetidakpastianPressureModel::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data ketidakpastian berhasil disimpan.',
            'data'    => $record,
        ]);
    }

    /**
     * Ambil data per id.
     */
    public function show($id)
    {
        $data = MasterKetidakpastianPressureModel::with('alat')->findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update data master.
     */
    public function update(Request $request, $id)
    {
        $data = MasterKetidakpastianPressureModel::findOrFail($id);

        $validated = $request->validate([
            'alat_id'              => 'nullable|exists:alat_kalibrasi,id',
            'kode_alat'            => 'required|string|max:100',
            'titik_kalibrasi'      => 'required|numeric',
            'nilai_ketidakpastian' => 'required|numeric|min:0',
            'keterangan'           => 'nullable|string|max:255',
        ], [
            'kode_alat.required'            => 'Kode alat wajib dipilih atau diisi.',
            'titik_kalibrasi.required'      => 'Titik kalibrasi wajib diisi.',
            'nilai_ketidakpastian.required' => 'Nilai ketidakpastian wajib diisi.',
        ]);

        // Cek duplikasi kecuali id ini
        $exists = MasterKetidakpastianPressureModel::where('kode_alat', $validated['kode_alat'])
            ->where('titik_kalibrasi', $validated['titik_kalibrasi'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => "Ketidakpastian untuk alat {$validated['kode_alat']} pada titik {$validated['titik_kalibrasi']} sudah terdaftar.",
            ], 422);
        }

        if (!empty($validated['alat_id'])) {
            $alat = AlatKalibrasiModel::find($validated['alat_id']);
            if ($alat) {
                $validated['nama_alat'] = $alat->nama_alat;
                $validated['kode_alat'] = $alat->kode_alat;
            }
        } elseif (!empty($validated['kode_alat'])) {
            $alat = AlatKalibrasiModel::where('kode_alat', $validated['kode_alat'])->first();
            if ($alat) {
                $validated['alat_id'] = $alat->id;
                $validated['nama_alat'] = $alat->nama_alat;
            }
        }

        $uGabungan = (float) $validated['nilai_ketidakpastian'];
        $validated['u_naik'] = $uGabungan / sqrt(2);
        $validated['u_turun'] = $uGabungan / sqrt(2);

        $data->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data ketidakpastian berhasil diperbarui.',
            'data'    => $data,
        ]);
    }

    /**
     * Hapus data master.
     */
    public function destroy($id)
    {
        $data = MasterKetidakpastianPressureModel::findOrFail($id);
        $data->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data ketidakpastian berhasil dihapus.',
        ]);
    }

    /**
     * Ambil list titik & ketidakpastian berdasarkan alat.
     */
    public function getByAlat($alatIdentifier)
    {
        $data = MasterKetidakpastianPressureModel::where(function ($q) use ($alatIdentifier) {
                $q->where('alat_id', $alatIdentifier)
                  ->orWhere('kode_alat', $alatIdentifier);
            })
            ->orderBy('titik_kalibrasi')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    /**
     * Download template Excel untuk import ketidakpastian pressure.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Ketidakpastian');

        // Header
        $headers = [
            'A1' => 'Kode Alat',
            'B1' => 'Titik Kalibrasi',
            'C1' => 'Nilai Ketidakpastian',
            'D1' => 'Keterangan',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Style Header
        $headerStyle = [
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'], // Primary blue
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'D1D5DB'],
                ],
            ],
        ];

        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Contoh Data
        $sampleData = [
            ['PRS/TEST/01', 0, 0.08, 'Contoh Titik 0'],
            ['PRS/TEST/01', 5, 0.08, 'Contoh Titik 5'],
            ['PRS/TEST/01', 10, 0.08, 'Contoh Titik 10'],
            ['PRS/TEST/01', 15, 0.08, 'Contoh Titik 15'],
            ['PRS/TEST/01', 20, 0.08, 'Contoh Titik 20'],
        ];

        $rowNum = 2;
        foreach ($sampleData as $row) {
            $sheet->setCellValueExplicit("A{$rowNum}", $row[0], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("B{$rowNum}", $row[1]);
            $sheet->setCellValue("C{$rowNum}", $row[2]);
            $sheet->setCellValueExplicit("D{$rowNum}", $row[3], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            $sheet->getStyle("A{$rowNum}:D{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$rowNum}:C{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getRowDimension($rowNum)->setRowHeight(22);

            $rowNum++;
        }

        // Auto-fit kolom
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'template_master_ketidakpastian_pressure.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import data ketidakpastian pressure via file Excel.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes'    => 'Format file harus berupa file Excel (.xlsx atau .xls).',
            'file.max'      => 'Ukuran file maksimal adalah 10MB.',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            $errors = [];
            $successCount = 0;
            $updatedCount = 0;

            foreach ($rows as $index => $row) {
                // Lewati header (baris 1)
                if ($index == 1) continue;

                $kodeAlat = trim((string)($row['A'] ?? ''));
                $titikRaw = trim((string)($row['B'] ?? ''));
                $nilaiRaw = trim((string)($row['C'] ?? ''));
                $keterangan = trim((string)($row['D'] ?? ''));

                // Jika seluruh baris kosong, lewati
                if ($kodeAlat === '' && $titikRaw === '' && $nilaiRaw === '') {
                    continue;
                }

                // Validasi Kode Alat
                if ($kodeAlat === '') {
                    $errors[] = "Baris {$index}: Kolom Kode Alat wajib diisi.";
                    continue;
                }

                // Konversi format desimal koma ke titik
                $titikClean = str_replace(',', '.', $titikRaw);
                if ($titikRaw === '' || !is_numeric($titikClean)) {
                    $errors[] = "Baris {$index}: Titik Kalibrasi '{$titikRaw}' harus berupa angka valid.";
                    continue;
                }
                $titik = (float) $titikClean;

                $nilaiClean = str_replace(',', '.', $nilaiRaw);
                if ($nilaiRaw === '' || !is_numeric($nilaiClean) || (float) $nilaiClean < 0) {
                    $errors[] = "Baris {$index}: Nilai Ketidakpastian '{$nilaiRaw}' harus berupa angka positif valid.";
                    continue;
                }
                $nilai = (float) $nilaiClean;

                // Cari alat di master alat kalibrasi jika terdaftar
                $alat = AlatKalibrasiModel::where('kode_alat', $kodeAlat)->first();

                // Simpan atau update
                $existing = MasterKetidakpastianPressureModel::where('kode_alat', $kodeAlat)
                    ->where('titik_kalibrasi', $titik)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'alat_id'              => $alat ? $alat->id : $existing->alat_id,
                        'nama_alat'            => $alat ? $alat->nama_alat : $existing->nama_alat,
                        'nilai_ketidakpastian' => $nilai,
                        'u_naik'               => $nilai / sqrt(2),
                        'u_turun'              => $nilai / sqrt(2),
                        'keterangan'           => $keterangan ?: $existing->keterangan,
                    ]);
                    $updatedCount++;
                } else {
                    MasterKetidakpastianPressureModel::create([
                        'alat_id'              => $alat ? $alat->id : null,
                        'kode_alat'            => $kodeAlat,
                        'nama_alat'            => $alat ? $alat->nama_alat : null,
                        'titik_kalibrasi'      => $titik,
                        'nilai_ketidakpastian' => $nilai,
                        'u_naik'               => $nilai / sqrt(2),
                        'u_turun'              => $nilai / sqrt(2),
                        'keterangan'           => $keterangan ?: null,
                    ]);
                    $successCount++;
                }
            }

            $totalProcessed = $successCount + $updatedCount;

            if ($totalProcessed === 0 && count($errors) > 0) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal mengimpor data. Semua baris tidak valid.',
                    'errors'  => $errors,
                ], 422);
            }

            if (count($errors) > 0) {
                return response()->json([
                    'status'  => 'partial',
                    'message' => "Berhasil memproses {$totalProcessed} data ({$successCount} baru, {$updatedCount} diperbarui), tetapi terdapat " . count($errors) . " baris gagal.",
                    'errors'  => $errors,
                ]);
            }

            return response()->json([
                'status'  => 'success',
                'message' => "Berhasil mengimpor {$totalProcessed} data ketidakpastian ({$successCount} baru, {$updatedCount} diperbarui).",
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage(),
            ], 500);
        }
    }
}
