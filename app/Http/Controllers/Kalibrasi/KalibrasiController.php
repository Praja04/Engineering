<?php

namespace App\Http\Controllers\Kalibrasi;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Kalibrasi\KalibrasiModel;
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

    public function viewSchedule()
    {
        return view('kalibrasi.schedule');
    }

    public function viewCertificate()
    {
        return view('kalibrasi.certificate');
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
            'merk' => 'required|string|max:50',
            'tipe' => 'required|string|max:50',
            'kapasitas' => 'required|integer',
            'resolusi' => 'required|numeric',
            'min_range_use' => 'required|numeric',
            'max_range_use' => 'required|numeric',
            'limits_permissible_error' => 'required|integer',
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
                'range_use' => $request->min_range_use . 's/d' . $request->max_range_use ?? 0,
                'limits_permissible_error' => $request->limits_permissible_error ?? 0,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Calibration tool successfully added',
                'data' => $alat
            ], 201);
        } catch (Exception $e) {
            if ($e->getCode() == "23000") { // error kode duplikat (SQLSTATE 23000)
                return response()->json([
                    'status' => 'error',
                    'message' => 'The tool code has already been used. Please use another code.'
                ], 409); // 409 Conflict
            }

            // fallback kalau error lain
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving the data.'
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
            'edit_merk' => 'required|string|max:50',
            'edit_tipe' => 'required|string|max:50',
            'edit_kapasitas' => 'required|integer',
            'edit_resolusi' => 'required|numeric',
            'edit_min_range_use' => 'required|numeric',
            'edit_max_range_use' => 'required|numeric',
            'edit_limits_permissible_error' => 'required|integer',
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
                'range_use' => $request->edit_min_range_use . 's/d' . $request->edit_max_range_use ?? 0,
                'limits_permissible_error' => $request->edit_limits_permissible_error ?? 0,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'The calibration tool has been successfully updated.',
                'data' => $alat
            ], 200);
        } catch (Exception $e) {
            if ($e->getCode() == "23000") {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The tool code has already been used. Please use another code.'
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the data.'
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
                'message' => 'The calibration tool has been successfully deleted.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Calibration tool data with ID ' . $id . ' not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting data'
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
        $sheet->setCellValue('A1', 'Jenis Kalibrasi');
        $sheet->setCellValue('B1', 'Kode Alat');
        $sheet->setCellValue('C1', 'Nama Alat');
        $sheet->setCellValue('D1', 'Jumlah');
        $sheet->setCellValue('E1', 'Departemen Pemilik');
        $sheet->setCellValue('F1', 'Lokasi Alat');
        $sheet->setCellValue('G1', 'No Kalibrasi');
        $sheet->setCellValue('H1', 'Merk');
        $sheet->setCellValue('I1', 'Tipe');
        $sheet->setCellValue('J1', 'Kapasitas');
        $sheet->setCellValue('K1', 'Resolusi');
        $sheet->setCellValue('L1', 'Min Range Penggunaan');
        $sheet->setCellValue('M1', 'Max Range Penggunaan');
        $sheet->setCellValue('N1', 'Limits of Permissible Error');

        // Add example data
        $sheet->setCellValue('A2', 'Pressure');
        $sheet->setCellValue('B2', 'EUT/COM/PRE/006');
        $sheet->setCellValue('C2', 'Pressure Gauge');
        $sheet->setCellValue('D2', 1);
        $sheet->setCellValue('E2', 'EUT');
        $sheet->setCellValue('F2', 'Compressed Air Process');
        $sheet->setCellValue('G2', 'CAL/PRE/188');
        $sheet->setCellValue('H2', 'SCHUH');
        $sheet->setCellValue('I2', 'Analog');
        $sheet->setCellValue('J2', '16');
        $sheet->setCellValue('K2', '0.1');
        $sheet->setCellValue('L2', '1');
        $sheet->setCellValue('M2', '5');
        $sheet->setCellValue('N2', '1');

        // Auto width columns
        foreach (range('A', 'N') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Style header
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);
        $sheet->getStyle('A1:N1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCCCCC');

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_alat_kalibrasi_' . date('Y-m-d') . '.xlsx';

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
                $jenis        = trim($row['A'] ?? '');
                $kode         = trim($row['B'] ?? '');
                $nama         = trim($row['C'] ?? '');
                $jumlah       = trim($row['D'] ?? '');
                $departemen   = trim($row['E'] ?? '');
                $lokasi       = trim($row['F'] ?? '');
                $noKal        = trim($row['G'] ?? '');
                $merk         = trim($row['H'] ?? '');
                $tipe         = trim($row['I'] ?? '');
                $kapasitas    = trim($row['J'] ?? '');
                $resolusi     = trim($row['K'] ?? '');
                $min_range_use = trim($row['L'] ?? '');
                $max_range_use = trim($row['M'] ?? '');
                $limits_error = trim($row['N'] ?? '');

                // field yang wajib diisi
                $requiredFields = [
                    'Jenis Kalibrasi' => $jenis,
                    'Kode Alat'       => $kode,
                    'Nama Alat'       => $nama,
                    'Jumlah'          => $jumlah,
                    'Departemen'      => $departemen,
                    'Lokasi'          => $lokasi,
                    'Nomor Kalibrasi' => $noKal,
                    'Merk'            => $merk,
                    'Tipe'            => $tipe,
                    'Kapasitas'       => $kapasitas,
                    'Resolusi'        => $resolusi,
                    'Min Range'       => $min_range_use,
                    'Max Range'       => $max_range_use,
                    'Limits Error'    => $limits_error,
                ];

                foreach ($requiredFields as $field => $value) {
                    if ($value === '' || $value === null) {
                        $errors[] = "Row {$index}: Column {$field} Must be filled in";
                        continue; // skip baris ini, lanjut baris berikutnya
                    }
                }

                // validasi kode unik
                if (AlatKalibrasiModel::where('kode_alat', $kode)->exists()) {
                    $errors[] = "Row {$index}: Kode alat '{$kode}' already exists";
                    continue;
                }

                // simpan jika lolos validasi
                AlatKalibrasiModel::create([
                    'user_id' => Auth::id() ?? 1,
                    'kode_alat' => $kode,
                    'nama_alat' => $nama,
                    'jenis_kalibrasi' => $jenis,
                    'jumlah' => $jumlah,
                    'departemen_pemilik' => $departemen,
                    'lokasi_alat' => $lokasi,
                    'no_kalibrasi' => $noKal,
                    'merk' => $merk ?? '-',
                    'tipe' => $tipe ?? '-',
                    'kapasitas' => is_numeric($kapasitas) ? (int)$kapasitas : 0,
                    'resolusi' => is_numeric($resolusi) ? (float)$resolusi : 0,
                    'min_range_use' => is_numeric($min_range_use) ? (int)$min_range_use : 0,
                    'max_range_use' => is_numeric($max_range_use) ? (int)$max_range_use : 0,
                    'limits_permissible_error' => is_numeric($limits_error) ? (int)$limits_error : 0,
                ]);

                $successCount++;
            }

            return response()->json([
                'status' => $errors ? 'partial' : 'success',
                'message' => "Import successful {$successCount} data",
                'errors' => $errors
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to import data: ' . $e->getMessage()
            ], 500);
        }
    }

    // getData Schedule
    public function getSchedule()
    {
        try {
            $data = KalibrasiModel::selectRaw('id,alat_id,user_id,lokasi_kalibrasi,tgl_kalibrasi,tgl_kalibrasi_ulang,jenis_kalibrasi')
                ->with('alat:id,kode_alat')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data'   => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDataCertificate()
    {
        try {
            $data = KalibrasiModel::selectRaw('id,alat_id,lokasi_kalibrasi,tgl_kalibrasi,tgl_kalibrasi_ulang,jenis_kalibrasi')
                ->with(
                    'alat:id,kode_alat,nama_alat',
                    'certificate:id,kalibrasi_id,status'
                )
                ->get();

            return response()->json([
                'status' => 'success',
                'data'   => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserApprovals()
    {
        try {
            $data = User::selectRaw('id,username,email,jabatan,nik,bagian')
                ->get();

            return response()->json([
                'status' => 'success',
                'data'   => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function reqApprovalStore() {}
}
