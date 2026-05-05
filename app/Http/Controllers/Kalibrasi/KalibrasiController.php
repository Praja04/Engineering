<?php

namespace App\Http\Controllers\Kalibrasi;

use App\Http\Controllers\Controller;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    public function destroy(string $id)
    {
        $certifikat = KalibrasiSertifikatModel::find($id);

        if (!$certifikat) {
            return response()->json([
                'success' => false,
                'message' => 'Data sertifikat tidak ditemukan'
            ], 404);
        }

        $kalibrasiId = $certifikat->kalibrasi_id;

        $kalibrasi = KalibrasiModel::find($kalibrasiId);

        if (!$kalibrasi) {
            return response()->json([
                'success' => false,
                'message' => 'Data kalibrasi tidak ditemukan'
            ], 404);
        }

        $kalibrasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kalibrasi berhasil dihapus!'
        ]);
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
                    // 'lokasi_alat' => $lokasi,
                    // 'no_kalibrasi' => $noKal,
                    // 'merk' => $merk,
                    // 'tipe' => $tipe,
                    // 'kapasitas' => $kapasitas,
                    // 'resolusi' => $resolusi,
                    // 'range_penggunaan_alat' => $range_penggunaan,
                    // 'limits_of_permissible_error' => $limits_error,
                    // 'metode_kalibrasi' => $metodeKal
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

    public function detail($id)
    {
        $main = KalibrasiModel::with(['alat', 'user',])->findOrFail($id);

        // Log::info('DATA MAIN:', $main->toArray());

        switch ($main->jenis_kalibrasi) {

            case 'pressure':
                $main->load('pressure');

                return view(
                    'kalibrasi.certificate.partials.pressure_details',
                    compact('main')
                );

            case 'volumetrik':
                $main->load('volumetrik');

                return view(
                    'kalibrasi.certificate.partials.volumetrik_details',
                    compact('main')
                );

            case 'temperature':
                $main->load('temperature');

                return view(
                    'kalibrasi.certificate.partials.temperature_details',
                    compact('main')
                );

            case 'thermohygrometer':
                $main->load('thermohygrometer');

                return view(
                    'kalibrasi.certificate.partials.thermohygrometer_details',
                    compact('main')
                );

            case 'jangka_sorong':
                $main->load('jangkaSorong', 'jangkaSorongSummary');
                $data = $main->jangkaSorong()->get();

                return view(
                    'kalibrasi.certificate.partials.jangka_sorong_details',
                    compact('data', 'main')
                );

            case 'timbangan':
                $main->load(
                    'kemampuanUlang',
                    'keseragamanSkala',
                    'pinggan',
                    'tare',
                    'histerisis',
                    'kemampuanUlangSummary',
                    'keseragamanSkalaSummary',
                    'pingganSummary',
                    'tareSummary',
                    'histerisisSummary'
                );

                return view(
                    'kalibrasi.certificate.partials.timbangan_details',
                    compact('main')
                );

            case 'instrumen':
                $main->load(
                    'instrumen',
                    'keypad',
                );

                return view(
                    'kalibrasi.certificate.partials.instrumen_details',
                    compact('main')
                );

            case 'dimensi':
                $main->load('dimensi',);

                return view(
                    'kalibrasi.certificate.partials.dimensi_details',
                    compact('main')
                );

            default:
                abort(404);
        }
    }

    // Sticker

    public function viewSticker()
    {
        return view('kalibrasi.sticker.sticker_kalibrasi');
    }

    public function getDataSticker(Request $request)
    {
        $query = KalibrasiSertifikatModel::with(['kalibrasi.alat', 'kalibrasi.user'])
            ->where('status', 'approved');

        // Filter kode alat
        if ($request->kode_alat) {
            $query->whereHas('kalibrasi.alat', function ($q) use ($request) {
                $q->where('kode_alat', 'like', '%' . $request->kode_alat . '%');
            });
        }

        // Filter tanggal
        if ($request->tanggal) {
            $query->whereHas('kalibrasi', function ($q) use ($request) {
                $q->whereDate('tgl_kalibrasi', $request->tanggal);
            });
        }

        $data = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($data);
    }

    public function downloadSticker($id)
    {
        $data = KalibrasiSertifikatModel::with(['kalibrasi.alat', 'kalibrasi.user'])
            ->findOrFail($id);

        $kalibrasi = $data->kalibrasi;

        // 🔥 ambil relasi berdasarkan jenis
        $relations = $kalibrasi->getRelasiByJenis();

        // 🔥 load relasi tambahan secara dynamic
        if (!empty($relations)) {
            $kalibrasi->load($relations);
        }

        // ukuran 10cm x 5cm
        $customPaper = [0, 0, 283.46, 113.38];

        $pdf = Pdf::loadView('kalibrasi.sticker.sticker_pdf', compact('kalibrasi'))
            ->setPaper($customPaper)
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultPaperSize' => 'custom',
            ]);

        return $pdf->stream('sticker-kalibrasi.pdf');
    }
}
