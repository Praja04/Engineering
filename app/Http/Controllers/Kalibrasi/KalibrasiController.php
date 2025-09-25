<?php

namespace App\Http\Controllers\Kalibrasi;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Exports\AlatKalibrasiTemplateExport;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KalibrasiController extends Controller
{
    public function viewMasterAlat()
    {
        return view('kalibrasi.master_alat_kalibrasi');
    }

    public function storeAlatKalibrasi(Request $request)
    {
        $request->validate([
            'kode_alat' => 'required|string|max:50|unique:alat_kalibrasi,kode_alat',
            'jenis_kalibrasi' => 'required|string|max:50',
            'jumlah' => 'required|integer',
            'nama_alat' => 'required|string|max:100',
            'departemen_pemilik' => 'required|string|max:50',
            'lokasi_alat' => 'required|string|max:50',
            'no_kalibrasi' => 'required|string|max:50',
            'merk' => 'nullable|string|max:50',
            'tipe' => 'nullable|string|max:50',
            'kapasitas' => 'nullable|integer',
            'resolusi' => 'nullable|numeric',
            'range_penggunaan' => 'nullable|integer',
            'limits_permissible_error' => 'nullable|integer',
        ]);

        try {
            $alat = AlatKalibrasiModel::create([
                'user_id' => Auth::id() ?? 1,
                'kode_alat' => $request->kode_alat,
                'jenis_kalibrasi' => $request->jenis_kalibrasi,
                'jumlah' => $request->jumlah,
                'nama_alat' => $request->nama_alat,
                'departemen_pemilik' => $request->departemen_pemilik,
                'lokasi_alat' => $request->lokasi_alat,
                'no_kalibrasi' => $request->no_kalibrasi,
                'merk' => $request->merk ?? '-',
                'tipe' => $request->tipe ?? '-',
                'kapasitas' => $request->kapasitas ?? 0,
                'resolusi' => $request->resolusi ?? 0,
                'range_penggunaan' => $request->range_penggunaan ?? 0,
                'limits_permissible_error' => $request->limits_permissible_error ?? 0,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Alat kalibrasi berhasil ditambahkan',
                'data' => $alat
            ], 201);
        } catch (Exception $e) {
            if ($e->getCode() == "23000") { // error kode duplikat (SQLSTATE 23000)
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode alat sudah digunakan, silakan gunakan kode lain'
                ], 409); // 409 Conflict
            }

            // fallback kalau error lain
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }
    }

    public function getDataAlatKalibrasi()
    {
        $data = AlatKalibrasiModel::select([
            'id', // jangan lupa id supaya relasi tetap bisa jalan
            'kode_alat',
            'jenis_kalibrasi',
            'nama_alat',
            'departemen_pemilik',
            'lokasi_alat'
        ])
            ->with('user:id,username')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function showAlatKalibrasi(String $id)
    {
        $data = AlatKalibrasiModel::with('user:id,username')->find($id);

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data alat kalibrasi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function updateAlatKalibrasi(Request $request, String $id)
    {
        $request->validate([
            'edit_kode_alat' => [
                'required',
                'string',
                'max:50',
                Rule::unique('alat_kalibrasi', 'kode_alat')->ignore($id),
            ],
            'edit_jenis_kalibrasi' => 'required|string|max:100',
            'edit_jumlah' => 'required|integer',
            'edit_nama_alat' => 'required|string|max:100',
            'edit_departemen_pemilik' => 'required|string|max:50',
            'edit_lokasi_alat' => 'required|string|max:50',
            'edit_no_kalibrasi' => 'required|string|max:50',
            'edit_merk' => 'nullable|string|max:50',
            'edit_tipe' => 'nullable|string|max:50',
            'edit_kapasitas' => 'nullable|integer',
            'edit_resolusi' => 'nullable|numeric',
            'edit_range_penggunaan' => 'nullable|integer',
            'edit_limits_permissible_error' => 'nullable|integer',
        ]);

        try {
            $alat = AlatKalibrasiModel::findOrFail($id);

            $alat->update([
                'user_id' => Auth::id() ?? $alat->user_id, // tetap simpan user lama kalau tidak ada auth
                'kode_alat' => $request->edit_kode_alat,
                'jenis_kalibrasi' => $request->edit_jenis_kalibrasi,
                'jumlah' => $request->edit_jumlah,
                'nama_alat' => $request->edit_nama_alat,
                'departemen_pemilik' => $request->edit_departemen_pemilik,
                'lokasi_alat' => $request->edit_lokasi_alat,
                'no_kalibrasi' => $request->edit_no_kalibrasi,
                'merk' => $request->edit_merk ?? '-',
                'tipe' => $request->edit_tipe ?? '-',
                'kapasitas' => $request->edit_kapasitas ?? 0,
                'resolusi' => $request->edit_resolusi ?? 0,
                'range_penggunaan' => $request->edit_range_penggunaan ?? 0,
                'limits_permissible_error' => $request->edit_limits_permissible_error ?? 0,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Alat kalibrasi berhasil diperbarui',
                'data' => $alat
            ], 200);
        } catch (Exception $e) {
            if ($e->getCode() == "23000") {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode alat sudah digunakan, silakan gunakan kode lain'
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data'
            ], 500);
        }
    }

    public function destroyAlatKalibrasi(String $id)
    {
        try {
            $alat = AlatKalibrasiModel::findOrFail($id);

            $alat->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Alat kalibrasi berhasil dihapus'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data alat kalibrasi dengan ID ' . $id . ' tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data'
            ], 500);
        }
    }

    public function getFilters()
    {
        $jenis = AlatKalibrasiModel::select('jenis_kalibrasi')
            ->distinct()
            ->pluck('jenis_kalibrasi');

        $departemen = AlatKalibrasiModel::select('departemen_pemilik')
            ->distinct()
            ->pluck('departemen_pemilik');

        return response()->json([
            'jenis' => $jenis,
            'departemen' => $departemen
        ]);
    }

    // download template excel
    public function downloadTemplateAlatKalibrasi()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Kode Alat');
        $sheet->setCellValue('B1', 'Nama Alat');
        $sheet->setCellValue('C1', 'Jenis Kalibrasi');
        $sheet->setCellValue('D1', 'Jumlah');
        $sheet->setCellValue('E1', 'Departemen Pemilik');
        $sheet->setCellValue('F1', 'Lokasi Alat');
        $sheet->setCellValue('G1', 'No Kalibrasi');
        $sheet->setCellValue('H1', 'Merk');
        $sheet->setCellValue('I1', 'Tipe');
        $sheet->setCellValue('J1', 'Kapasitas');
        $sheet->setCellValue('K1', 'Resolusi');
        $sheet->setCellValue('L1', 'Range Penggunaan Alat');
        $sheet->setCellValue('M1', 'Limits of Permissible Error');

        // Add example data
        $sheet->setCellValue('A2', 'EUT/COM/PRE/006');
        $sheet->setCellValue('B2', 'Pressure Gauge');
        $sheet->setCellValue('C2', 'Pressure');
        $sheet->setCellValue('D2', 1);
        $sheet->setCellValue('E2', 'EUT');
        $sheet->setCellValue('F2', 'Compressed Air Process');
        $sheet->setCellValue('G2', 'CAL/PRE/188');
        $sheet->setCellValue('H2', 'SCHUH');
        $sheet->setCellValue('I2', 'Analog');
        $sheet->setCellValue('J2', '16');
        $sheet->setCellValue('K2', '0.1');
        $sheet->setCellValue('L2', '5');
        $sheet->setCellValue('M2', '1');

        // Auto width columns
        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Style header
        $sheet->getStyle('A1:M1')->getFont()->setBold(true);
        $sheet->getStyle('A1:M1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCCCCC');

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_barang_' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    // import handler
    public function importAlatKalibrasi(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            $errors = [];
            $successCount = 0;

            foreach ($rows as $index => $row) {
                if ($index == 1) continue; // skip header

                // ambil kolom sesuai template
                $kode       = trim($row['A'] ?? '');
                $nama       = trim($row['B'] ?? '');
                $jenis      = trim($row['C'] ?? '');
                $departemen = trim($row['D'] ?? '');
                $lokasi     = trim($row['E'] ?? '');
                $noKal      = trim($row['F'] ?? '');

                // validasi wajib
                if (!$kode || !$nama || !$jenis || !$departemen || !$lokasi || !$noKal) {
                    $errors[] = "Baris {$index}: Data tidak lengkap";
                    continue;
                }

                // validasi kode unik
                if (AlatKalibrasiModel::where('kode_alat', $kode)->exists()) {
                    $errors[] = "Baris {$index}: Kode alat '{$kode}' sudah terdaftar";
                    continue;
                }

                // simpan jika lolos validasi
                AlatKalibrasiModel::create([
                    'user_id' => Auth::id() ?? 1,
                    'kode_alat' => $kode,
                    'nama_alat' => $nama,
                    'jenis_kalibrasi' => $jenis,
                    'departemen_pemilik' => $departemen,
                    'lokasi_alat' => $lokasi,
                    'no_kalibrasi' => $noKal,
                    'merk' => $row['G'] ?? '-',
                    'tipe' => $row['H'] ?? '-',
                    'kapasitas' => is_numeric($row['I']) ? (int)$row['I'] : 0,
                    'resolusi' => is_numeric($row['J']) ? (float)$row['J'] : 0,
                    'range_penggunaan' => is_numeric($row['K']) ? (int)$row['K'] : 0,
                    'limits_permissible_error' => is_numeric($row['L']) ? (int)$row['L'] : 0,
                    'jumlah' => 1,
                ]);

                $successCount++;
            }

            return response()->json([
                'status' => $errors ? 'partial' : 'success',
                'message' => "Berhasil import {$successCount} data",
                'errors' => $errors
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengimport data: ' . $e->getMessage()
            ], 500);
        }
    }
}
