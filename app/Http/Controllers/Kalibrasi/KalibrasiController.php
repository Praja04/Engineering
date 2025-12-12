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

    public function viewDevPage()
    {
        return view('kalibrasi.maintenance_page');
    }

    public function dashboardForm()
    {
        return view('kalibrasi.dashboard_form');
    }

    public function dashboardData()
    {
        return view('kalibrasi.dashboard_data');
    }

    public function viewMasterAlat()
    {
        return view('kalibrasi.master.master_alat_kalibrasi');
    }

    public function viewSchedule()
    {
        return view('kalibrasi.schedule');
    }

    public function viewCertificate()
    {
        return view('kalibrasi.certificate.certificate');
    }

    private function normalizePlusMinus($input)
    {
        if (is_array($input)) {
            foreach ($input as $key => $val) {
                $input[$key] = $this->normalizePlusMinus($val);
            }
            return $input;
        }

        if (is_string($input)) {
            return str_replace(['+-', '-+'], '±', $input);
        }

        return $input;
    }

    public function storeAlatKalibrasi(Request $request)
    {
        $validated = $request->validate([
            'kode_alat' => 'required|string|max:100|unique:alat_kalibrasi,kode_alat',
            'jenis_kalibrasi' => 'required|string|max:100',
            'jumlah' => 'required|integer',
            'nama_alat' => 'required|string|max:100',
            'departemen_pemilik' => 'required|string|max:100',
            'lokasi_alat' => 'required|string|max:100',
            'no_kalibrasi' => 'required|string|max:100',
            'merk' => 'required|string|max:100',
            'tipe' => 'required|string|max:100',
            'kapasitas' => 'required|string',
            'resolusi' => 'required|string',
            'range_min' => 'required|string',
            'range_max' => 'required|string',
            'limits_permissible_error' => 'required|string',
            'metode_kalibrasi' => 'required|string|max:255'
        ]);

        try {
            $satuan = match (strtolower($validated['jenis_kalibrasi'])) {
                'pressure' => 'bar',
                'timbangan' => 'kg',
                'temperature' => '°C',
                'volumetrik' => 'ml',
                'jangka_sorong' => 'mm',
                'thermohygrometer' => '°C',
                default => ''
            };

            // format nilai-nilai numerik
            $kapasitas = "{$validated['kapasitas']} {$satuan}";
            $resolusi = "{$validated['resolusi']} {$satuan}";
            $range_penggunaan_alat = "{$validated['range_min']} {$satuan} - {$validated['range_max']} {$satuan}";
            $limits = "± {$validated['limits_permissible_error']} {$satuan}";

            $alat = AlatKalibrasiModel::create([
                'user_id' => Auth::id() ?? 1,
                'kode_alat' => $validated['kode_alat'],
                'jenis_kalibrasi' => $validated['jenis_kalibrasi'],
                'jumlah' => $validated['jumlah'],
                'nama_alat' => $validated['nama_alat'],
                'departemen_pemilik' => $validated['departemen_pemilik'],
                'lokasi_alat' => $validated['lokasi_alat'],
                'no_kalibrasi' => $validated['no_kalibrasi'],
                'merk' => $validated['merk'],
                'tipe' => $validated['tipe'],
                'kapasitas' => $kapasitas,
                'resolusi' => $resolusi,
                'range_penggunaan_alat' => $range_penggunaan_alat,
                'limits_of_permissible_error' => $limits,
                'metode_kalibrasi' => $validated['metode_kalibrasi'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Alat kalibrasi berhasil ditambahkan.',
                'data' => $alat
            ], 201);
        } catch (Exception $e) {
            if ($e->getCode() == "23000") { // error kode duplikat (SQLSTATE 23000)
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode alat tersebut sudah digunakan. Silakan gunakan kode lain.'
                ], 409); // 409 Conflict
            }

            // fallback kalau error lain
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data.' . $e
            ], 500);
        }
    }

    public function getDataAlatKalibrasi()
    {
        $data = AlatKalibrasiModel::select([
            'id',
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
        $validated = $request->validate([
            'edit_kode_alat' => [
                'required',
                'string',
                'max:50',
                Rule::unique('alat_kalibrasi', 'kode_alat')->ignore($id),
            ],
            'edit_jenis_kalibrasi' => 'required|string|max:100',
            'edit_jumlah' => 'required|integer',
            'edit_nama_alat' => 'required|string|max:100',
            'edit_departemen_pemilik' => 'required|string|max:100',
            'edit_lokasi_alat' => 'required|string|max:100',
            'edit_no_kalibrasi' => 'required|string|max:100',
            'edit_merk' => 'required|string|max:100',
            'edit_tipe' => 'required|string|max:100',
            'edit_kapasitas' => 'required|string',
            'edit_resolusi' => 'required|string',
            'edit_range_penggunaan_alat' => 'required|string',
            'edit_limits_permissible_error' => 'required|string',
            'edit_metode_kalibrasi' => 'required|string'
        ]);

        $data = $this->normalizePlusMinus($validated);

        try {
            $alat = AlatKalibrasiModel::findOrFail($id);

            $alat->update([
                'user_id' => Auth::id() ?? $alat->user_id, // tetap simpan user lama kalau tidak ada auth
                'kode_alat' => $data['edit_kode_alat'],
                'jenis_kalibrasi' => $data['edit_jenis_kalibrasi'] ?? '-',
                'jumlah' => $data['edit_jumlah'] ?? 0,
                'nama_alat' => $data['edit_nama_alat'] ?? '-',
                'departemen_pemilik' => $data['edit_departemen_pemilik'] ?? '-',
                'lokasi_alat' => $data['edit_lokasi_alat'] ?? '-',
                'no_kalibrasi' => $data['edit_no_kalibrasi'] ?? '-',
                'merk' => $data['edit_merk'] ?? '-',
                'tipe' => $data['edit_tipe'] ?? '-',
                'kapasitas' => $data['edit_kapasitas'] ?? '-',
                'resolusi' => $data['edit_resolusi'] ?? '-',
                'range_penggunaan_alat' => $data['edit_range_penggunaan_alat'] ?? '-',
                'limits_of_permissible_error' => $data['edit_limits_permissible_error'] ?? '-',
                'metode_kalibrasi' => $data['edit_metode_kalibrasi'] ?? '-',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Alat kalibrasi telah berhasil diperbarui.',
                'data' => $alat
            ], 200);
        } catch (Exception $e) {
            if ($e->getCode() == "23000") {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode alat tersebut sudah digunakan. Silakan gunakan kode lain.'
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data.' . $e
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
                'message' => 'Alat kalibrasi telah berhasil dihapus.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data alat kalibrasi dengan ID ' . $id . ' tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data.' . $e
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

    public function downloadTemplateAlatKalibrasi()
    {
        // Path ke file Excel template
        $path = public_path('assets/templates/template_alat_kalibrasi.xlsx');

        // Cek apakah file-nya ada
        if (!file_exists($path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Template file tidak ditemukan.'
            ], 404);
        }

        // Load file dari public
        $spreadsheet = IOFactory::load($path);
        $writer = new Xlsx($spreadsheet);

        $filename = 'template_alat_kalibrasi.xlsx';

        // Kirim ke browser untuk didownload
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
                if ($index == 1) continue;

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
                $range_penggunaan = trim($row['L'] ?? '');
                $limits_error = trim($row['M'] ?? '');
                $metodeKal    = trim($row['N'] ?? '');

                $jenisFormatted = strtolower(str_replace(' ', '_', $jenis));

                // field yang wajib diisi
                $data = [
                    'jenis_kalibrasi' => $jenisFormatted,
                    'kode_alat' => $kode,
                    'nama_alat' => $nama,
                    'jumlah' => $jumlah,
                    'departemen_pemilik' => $departemen,
                    'lokasi_alat' => $lokasi,
                    'no_kalibrasi' => $noKal,
                    'merk' => $merk,
                    'tipe' => $tipe,
                    'kapasitas' => $kapasitas,
                    'resolusi' => $resolusi,
                    'range_penggunaan_alat' => $range_penggunaan,
                    'limits_of_permissible_error' => $limits_error,
                    'metode_kalibrasi' => $metodeKal
                ];

                $data = $this->normalizePlusMinus($data);

                foreach ($data as $field => $value) {
                    if ($value === '' || $value === null) {
                        $errors[] = "Baris {$index}: Kolom {$field} harus terisi.";
                        continue 2; // skip baris ini, lanjut berikutnya
                    }
                }

                // validasi kode unik
                if (AlatKalibrasiModel::where('kode_alat', $data['kode_alat'])->exists()) {
                    $errors[] = "Baris {$index}: Kode alat '{$data['kode_alat']}' sudah terdaftar";
                    continue;
                }

                // simpan jika lolos validasi
                AlatKalibrasiModel::create(array_merge($data, [
                    'user_id' => Auth::id() ?? 1
                ]));

                $successCount++;
            }

            return response()->json([
                'status' => $errors ? 'partial' : 'success',
                'message' => "Import berhasil {$successCount} data",
                'errors' => $errors
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal untuk mengimport: ' . $e->getMessage()
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

    // Get Data Kalibrasi in Approval
    public function getData($id)
    {
        try {
            // cari data kalibrasi berdasarkan ID
            $kalibrasi = KalibrasiModel::with('alat:id,kode_alat,nama_alat')->findOrFail($id);

            // cek jenis kalibrasi
            $jenis = strtolower($kalibrasi->jenis_kalibrasi);
            $relasi = null;

            switch ($jenis) {
                case 'pressure':
                    $relasi = [
                        'pressure' => function ($q) {
                            $q->orderBy('titik_kalibrasi');
                        },
                        'pressureGabungan',
                    ];
                    break;

                case 'temperature':
                    $relasi = [
                        'temperature',
                        'temperatureGabungan',
                    ];
                    break;

                case 'mass':
                    $relasi = [
                        'mass',
                        'massGabungan',
                    ];
                    break;

                default:
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Jenis kalibrasi tidak dikenali: ' . $jenis
                    ], 400);
            }

            // ambil data lengkap sesuai jenisnya
            $data = KalibrasiModel::with(array_merge($relasi, ['alat:id,kode_alat,nama_alat']))
                ->where('id', $id)
                ->first();

            return response()->json([
                'status' => 'success',
                'jenis_kalibrasi' => $jenis,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
