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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Kalibrasi\Pressure\KalibrasiPressureModel;
use App\Models\Kalibrasi\Volumetrik\KalibrasiVolumetrikModel;
use App\Models\Kalibrasi\Temperature\KalibrasiTemperatureModel;
use App\Models\Kalibrasi\Thermohygrometer\KalibrasiThermohygrometerModel;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongModel;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongSummaryModel;
use App\Models\Kalibrasi\Timbangan\KemampuanUlangSummariesModel;
use App\Models\Kalibrasi\Timbangan\KeseragamanSkalaSummariesModel;
use App\Models\Kalibrasi\Timbangan\PingganSummariesModel;
use App\Models\Kalibrasi\Timbangan\TareSummariesModel;
use App\Models\Kalibrasi\Timbangan\HisterisisSummariesModel;
use App\Models\Kalibrasi\Timbangan\KetidakpastianSummariesModel;
use App\Models\Kalibrasi\Instrumen\CalInstrumenModel;
use App\Models\Kalibrasi\Dimensi\CalDimensiModel;
use App\Models\Kalibrasi\Flowmeter\CalFlowmeterModel;

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
        $validated = $request->validate(
            [
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
            ],
            [
                'kode_alat.required' => 'Kode alat wajib diisi.',
                'kode_alat.unique' => 'Kode alat sudah terdaftar.',
                'kode_alat.max' => 'Kode alat maksimal 100 karakter.',

                'jenis_kalibrasi.required' => 'Jenis kalibrasi wajib diisi.',
                'jumlah.required' => 'Jumlah wajib diisi.',
                'jumlah.integer' => 'Jumlah harus berupa angka.',

                'nama_alat.required' => 'Nama alat wajib diisi.',
                'departemen_pemilik.required' => 'Departemen pemilik wajib diisi.',
                'lokasi_alat.required' => 'Lokasi alat wajib diisi.',
                'no_kalibrasi.required' => 'Nomor kalibrasi wajib diisi.',
                'merk.required' => 'Merk wajib diisi.',
                'tipe.required' => 'Tipe wajib diisi.',
                'kapasitas.required' => 'Kapasitas wajib diisi.',
                'resolusi.required' => 'Resolusi wajib diisi.',
                'range_min.required' => 'Range minimum wajib diisi.',
                'range_max.required' => 'Range maksimum wajib diisi.',
                'limits_permissible_error.required' => 'Limits Permissible Error wajib diisi.',
                'metode_kalibrasi.required' => 'Metode kalibrasi wajib diisi.',
                'metode_kalibrasi.max' => 'Metode kalibrasi maksimal 255 karakter.',
            ]
        );

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
            'edit_jenis_kalibrasi' => 'nullable|string|max:100',
            'edit_jumlah' => 'nullable|integer',
            'edit_nama_alat' => 'nullable|string|max:100',
            'edit_departemen_pemilik' => 'nullable|string|max:100',
            'edit_lokasi_alat' => 'nullable|string|max:100',
            'edit_no_kalibrasi' => 'nullable|string|max:100',
            'edit_merk' => 'nullable|string|max:100',
            'edit_tipe' => 'nullable|string|max:100',
            'edit_kapasitas' => 'nullable|string',
            'edit_resolusi' => 'nullable|string',
            'edit_range_penggunaan_alat' => 'nullable|string',
            'edit_limits_permissible_error' => 'nullable|string',
            'edit_metode_kalibrasi' => 'nullable|string'
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

    public function massDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:cal_sertifikat,id',
        ]);

        $ids = $request->ids;

        DB::beginTransaction();

        try {
            foreach ($ids as $id) {
                $certifikat = KalibrasiSertifikatModel::find($id);
                if (!$certifikat) continue;

                $kalibrasiId = $certifikat->kalibrasi_id;
                $kalibrasi = KalibrasiModel::find($kalibrasiId);
                if ($kalibrasi) {
                    $kalibrasi->delete();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data kalibrasi terpilih berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEditData(string $id)
    {
        if (Auth::user() && strtolower(Auth::user()->jabatan ?? '') === 'operator') {
            return response()->json([
                'status'  => 'error',
                'message' => 'User dengan jabatan operator tidak diperbolehkan mengakses edit data kalibrasi.'
            ], 403);
        }

        try {
            $kalibrasi = KalibrasiModel::with([
                'alat',
                'user',
                'certificate',
                'pressure',
                'volumetrik.details',
                'temperature',
                'thermohygrometer',
                'jangkaSorong.master',
                'jangkaSorongSummary',
                'kemampuanUlangSummary',
                'keseragamanSkalaSummary',
                'pingganSummary',
                'tareSummary',
                'histerisisSummary',
                'ketidakpastianSummary',
                'instrumen',
                'dimensi',
                'flowmeter'
            ])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data'   => $kalibrasi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengambil data kalibrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateDataKalibrasi(Request $request, string $id)
    {
        if (Auth::user() && strtolower(Auth::user()->jabatan ?? '') === 'operator') {
            return response()->json([
                'status'  => 'error',
                'message' => 'User dengan jabatan operator tidak diperbolehkan mengedit data kalibrasi.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $kalibrasi = KalibrasiModel::findOrFail($id);

            // 1. Update data umum / header kalibrasi (cal_main)
            $updateMain = [];
            if ($request->has('tgl_kalibrasi')) $updateMain['tgl_kalibrasi'] = $request->tgl_kalibrasi;
            if ($request->has('tgl_kalibrasi_ulang')) $updateMain['tgl_kalibrasi_ulang'] = $request->tgl_kalibrasi_ulang;
            if ($request->has('lokasi_kalibrasi')) $updateMain['lokasi_kalibrasi'] = $request->lokasi_kalibrasi;
            if ($request->has('suhu_ruangan')) $updateMain['suhu_ruangan'] = $request->suhu_ruangan;
            if ($request->has('kelembaban')) $updateMain['kelembaban'] = $request->kelembaban;
            if ($request->has('catatan')) $updateMain['catatan'] = $request->catatan;

            if (!empty($updateMain)) {
                $kalibrasi->update($updateMain);
            }

            $jenis = strtolower(str_replace(['-', ' '], '_', $kalibrasi->jenis_kalibrasi));

            // 2. Update data per jenis kalibrasi (bagian average / sertifikat)
            switch ($jenis) {
                case 'pressure':
                    if ($request->has('pressure') && is_array($request->pressure)) {
                        foreach ($request->pressure as $pData) {
                            if (!empty($pData['id'])) {
                                $p = KalibrasiPressureModel::where('kalibrasi_id', $kalibrasi->id)
                                    ->where('id', $pData['id'])
                                    ->first();
                                if ($p) {
                                    $p->update([
                                        'titik_kalibrasi'          => $pData['titik_kalibrasi'] ?? $p->titik_kalibrasi,
                                        'avg_penunjuk_alat_naik'   => $pData['avg_penunjuk_alat_naik'] ?? $p->avg_penunjuk_alat_naik,
                                        'avg_penunjuk_alat_turun'  => $pData['avg_penunjuk_alat_turun'] ?? $p->avg_penunjuk_alat_turun,
                                        'avg_tekanan_standar_naik' => $pData['avg_tekanan_standar_naik'] ?? $p->avg_tekanan_standar_naik,
                                        'avg_tekanan_standar_turun'=> $pData['avg_tekanan_standar_turun'] ?? $p->avg_tekanan_standar_turun,
                                        'avg_koreksi_alat_naik'    => $pData['avg_koreksi_alat_naik'] ?? $p->avg_koreksi_alat_naik,
                                        'avg_koreksi_alat_turun'   => $pData['avg_koreksi_alat_turun'] ?? $p->avg_koreksi_alat_turun,
                                        'std_deviasi_naik'         => $pData['std_deviasi_naik'] ?? $p->std_deviasi_naik,
                                        'std_deviasi_turun'        => $pData['std_deviasi_turun'] ?? $p->std_deviasi_turun,
                                        'ketidakpastian_naik'      => $pData['ketidakpastian_naik'] ?? $p->ketidakpastian_naik,
                                        'ketidakpastian_turun'     => $pData['ketidakpastian_turun'] ?? $p->ketidakpastian_turun,
                                        'u_naik'                   => $pData['u_naik'] ?? $p->u_naik,
                                        'u_turun'                  => $pData['u_turun'] ?? $p->u_turun,
                                        'u_gabungan'               => $pData['u_gabungan'] ?? $p->u_gabungan,
                                    ]);
                                }
                            }
                        }
                    }
                    break;

                case 'volumetrik':
                    if ($request->has('volumetrik') && is_array($request->volumetrik)) {
                        foreach ($request->volumetrik as $vData) {
                            if (!empty($vData['id'])) {
                                $v = KalibrasiVolumetrikModel::where('kalibrasi_id', $kalibrasi->id)
                                    ->where('id', $vData['id'])
                                    ->first();
                                if ($v) {
                                    $v->update([
                                        'titik_kalibrasi'        => $vData['titik_kalibrasi'] ?? $v->titik_kalibrasi,
                                        'avg_penunjuk_standar'   => $vData['avg_penunjuk_standar'] ?? $v->avg_penunjuk_standar,
                                        'avg_koreksi'            => $vData['avg_koreksi'] ?? $v->avg_koreksi,
                                        'stdev_penunjuk_standar' => $vData['stdev_penunjuk_standar'] ?? $v->stdev_penunjuk_standar,
                                        'u_total'                => $vData['u_total'] ?? $v->u_total,
                                    ]);

                                    if (isset($vData['penunjuk_alat'])) {
                                        $detail = $v->details()->first();
                                        if ($detail) {
                                            $detail->update(['penunjuk_alat' => $vData['penunjuk_alat']]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;

                case 'temperature':
                    if ($request->has('temperature') && is_array($request->temperature)) {
                        foreach ($request->temperature as $tData) {
                            if (!empty($tData['id'])) {
                                $t = KalibrasiTemperatureModel::where('kalibrasi_id', $kalibrasi->id)
                                    ->where('id', $tData['id'])
                                    ->first();
                                if ($t) {
                                    $t->update([
                                        'titik_kalibrasi'   => $tData['titik_kalibrasi'] ?? $t->titik_kalibrasi,
                                        'avg_penunjuk_alat' => $tData['avg_penunjuk_alat'] ?? $t->avg_penunjuk_alat,
                                        'avg_suhu_standar'  => $tData['avg_suhu_standar'] ?? $t->avg_suhu_standar,
                                        'avg_kor_alat'      => $tData['avg_kor_alat'] ?? $t->avg_kor_alat,
                                        'stdev'             => $tData['stdev'] ?? $t->stdev,
                                        'ketidakpastian'    => $tData['ketidakpastian'] ?? $t->ketidakpastian,
                                    ]);
                                }
                            }
                        }
                    }
                    break;

                case 'thermohygrometer':
                    if ($request->has('thermohygrometer') && is_array($request->thermohygrometer)) {
                        foreach ($request->thermohygrometer as $thData) {
                            if (!empty($thData['id'])) {
                                $th = KalibrasiThermohygrometerModel::where('kalibrasi_id', $kalibrasi->id)
                                    ->where('id', $thData['id'])
                                    ->first();
                                if ($th) {
                                    $th->update([
                                        'titik_kalibrasi'          => $thData['titik_kalibrasi'] ?? $th->titik_kalibrasi,
                                        'posisi'                   => $thData['posisi'] ?? $th->posisi,
                                        'avg_penunjuk_alat_suhu'   => $thData['avg_penunjuk_alat_suhu'] ?? $th->avg_penunjuk_alat_suhu,
                                        'avg_tekanan_standar_suhu' => $thData['avg_tekanan_standar_suhu'] ?? $th->avg_tekanan_standar_suhu,
                                        'avg_koreksi_suhu'         => $thData['avg_koreksi_suhu'] ?? $th->avg_koreksi_suhu,
                                        'std_deviasi_suhu'         => $thData['std_deviasi_suhu'] ?? $th->std_deviasi_suhu,
                                        'ketidak_pastian_suhu'     => $thData['ketidak_pastian_suhu'] ?? $th->ketidak_pastian_suhu,
                                        'avg_penunjuk_alat_rh'     => $thData['avg_penunjuk_alat_rh'] ?? $th->avg_penunjuk_alat_rh,
                                        'avg_tekanan_standar_rh'   => $thData['avg_tekanan_standar_rh'] ?? $th->avg_tekanan_standar_rh,
                                        'avg_koreksi_rh'           => $thData['avg_koreksi_rh'] ?? $th->avg_koreksi_rh,
                                        'std_deviasi_rh'           => $thData['std_deviasi_rh'] ?? $th->std_deviasi_rh,
                                        'ketidak_pastian_rh'       => $thData['ketidak_pastian_rh'] ?? $th->ketidak_pastian_rh,
                                    ]);
                                }
                            }
                        }
                    }
                    break;

                case 'jangka_sorong':
                    if ($request->has('jangka_sorong') && is_array($request->jangka_sorong)) {
                        foreach ($request->jangka_sorong as $jsData) {
                            if (!empty($jsData['id'])) {
                                $js = KalibrasiJangkaSorongModel::where('kalibrasi_id', $kalibrasi->id)
                                    ->where('id', $jsData['id'])
                                    ->first();
                                if ($js) {
                                    $js->update([
                                        'avg_pembacaan' => $jsData['avg_pembacaan'] ?? $js->avg_pembacaan,
                                        'std_dev'       => $jsData['std_dev'] ?? $js->std_dev,
                                        'koreksi'       => $jsData['koreksi'] ?? $js->koreksi,
                                    ]);
                                }
                            }
                        }
                    }
                    if ($request->has('jangka_sorong_summary')) {
                        $jsSummary = KalibrasiJangkaSorongSummaryModel::where('kalibrasi_id', $kalibrasi->id)->first();
                        if ($jsSummary) {
                            $jsSummary->update([
                                'std_dev_total'  => $request->jangka_sorong_summary['std_dev_total'] ?? $jsSummary->std_dev_total,
                                'ketidakpastian' => $request->jangka_sorong_summary['ketidakpastian'] ?? $jsSummary->ketidakpastian,
                            ]);
                        }
                    }
                    break;

                case 'timbangan':
                    if ($request->has('kemampuan_ulang_summary') && is_array($request->kemampuan_ulang_summary)) {
                        foreach ($request->kemampuan_ulang_summary as $kuData) {
                            if (!empty($kuData['id'])) {
                                $ku = KemampuanUlangSummariesModel::where('kalibrasi_id', $kalibrasi->id)
                                    ->where('id', $kuData['id'])
                                    ->first();
                                if ($ku) {
                                    $ku->update([
                                        'massa'                => $kuData['massa'] ?? $ku->massa,
                                        'std_dev'              => $kuData['std_dev'] ?? $ku->std_dev,
                                        'maks_perbedaan_akhir' => $kuData['maks_perbedaan_akhir'] ?? $ku->maks_perbedaan_akhir,
                                    ]);
                                }
                            }
                        }
                    }
                    if ($request->has('keseragaman_skala_summary') && is_array($request->keseragaman_skala_summary)) {
                        foreach ($request->keseragaman_skala_summary as $ksData) {
                            if (!empty($ksData['id'])) {
                                $ks = KeseragamanSkalaSummariesModel::where('kalibrasi_id', $kalibrasi->id)
                                    ->where('id', $ksData['id'])
                                    ->first();
                                if ($ks) {
                                    $ks->update([
                                        'beban'         => $ksData['beban'] ?? $ks->beban,
                                        'koreksi_skala' => $ksData['koreksi_skala'] ?? $ks->koreksi_skala,
                                    ]);
                                }
                            }
                        }
                    }
                    if ($request->has('pinggan_summary')) {
                        $pSummary = PingganSummariesModel::where('kalibrasi_id', $kalibrasi->id)->first();
                        if ($pSummary) {
                            $pSummary->update([
                                'summary_tengah'   => $request->pinggan_summary['summary_tengah'] ?? $pSummary->summary_tengah,
                                'summary_depan'    => $request->pinggan_summary['summary_depan'] ?? $pSummary->summary_depan,
                                'summary_belakang' => $request->pinggan_summary['summary_belakang'] ?? $pSummary->summary_belakang,
                                'summary_kiri'     => $request->pinggan_summary['summary_kiri'] ?? $pSummary->summary_kiri,
                                'summary_kanan'    => $request->pinggan_summary['summary_kanan'] ?? $pSummary->summary_kanan,
                                'selisih_maks'     => $request->pinggan_summary['selisih_maks'] ?? $pSummary->selisih_maks,
                            ]);
                        }
                    }
                    if ($request->has('tare_summary') && is_array($request->tare_summary)) {
                        foreach ($request->tare_summary as $tData) {
                            if (!empty($tData['id'])) {
                                $t = TareSummariesModel::where('kalibrasi_id', $kalibrasi->id)
                                    ->where('id', $tData['id'])
                                    ->first();
                                if ($t) {
                                    $t->update([
                                        'massa'      => $tData['massa'] ?? $t->massa,
                                        'selisih_mz' => $tData['selisih_mz'] ?? $t->selisih_mz,
                                    ]);
                                }
                            }
                        }
                    }
                    if ($request->has('histerisis_summary')) {
                        $hSummary = HisterisisSummariesModel::where('kalibrasi_id', $kalibrasi->id)->first();
                        if ($hSummary) {
                            $hSummary->update([
                                'setengah_kapasitas' => $request->histerisis_summary['setengah_kapasitas'] ?? $hSummary->setengah_kapasitas,
                                'histerisis'         => $request->histerisis_summary['histerisis'] ?? $hSummary->histerisis,
                            ]);
                        }
                    }
                    if ($request->has('ketidakpastian_summary')) {
                        $kpSummary = KetidakpastianSummariesModel::where('kalibrasi_id', $kalibrasi->id)->first();
                        if ($kpSummary) {
                            $kpSummary->update([
                                'ketidakpastian_gabungan' => $request->ketidakpastian_summary['ketidakpastian_gabungan'] ?? $kpSummary->ketidakpastian_gabungan,
                                'ketidakpastian_perluas'  => $request->ketidakpastian_summary['ketidakpastian_perluas'] ?? $kpSummary->ketidakpastian_perluas,
                            ]);
                        }
                    }
                    break;

                case 'instrumen':
                    if ($request->has('instrumen') && is_array($request->instrumen)) {
                        foreach ($request->instrumen as $inData) {
                            if (!empty($inData['id'])) {
                                $in = CalInstrumenModel::where('kalibrasi_id', $kalibrasi->id)
                                    ->where('id', $inData['id'])
                                    ->first();
                                if ($in) {
                                    $in->update([
                                        'titik_kalibrasi' => $inData['titik_kalibrasi'] ?? $in->titik_kalibrasi,
                                        'nilai_master'    => $inData['nilai_master'] ?? $in->nilai_master,
                                        'avg_pembacaan'   => $inData['avg_pembacaan'] ?? $in->avg_pembacaan,
                                        'std_dev'         => $inData['std_dev'] ?? $in->std_dev,
                                        'koreksi'         => $inData['koreksi'] ?? $in->koreksi,
                                    ]);
                                }
                            }
                        }
                    }
                    break;

                case 'dimensi':
                    if ($request->has('dimensi') && is_array($request->dimensi)) {
                        foreach ($request->dimensi as $dData) {
                            if (!empty($dData['id'])) {
                                $d = CalDimensiModel::where('kalibrasi_id', $kalibrasi->id)
                                    ->where('id', $dData['id'])
                                    ->first();
                                if ($d) {
                                    $d->update([
                                        'titik_kalibrasi' => $dData['titik_kalibrasi'] ?? $d->titik_kalibrasi,
                                        'nilai_master'    => $dData['nilai_master'] ?? $d->nilai_master,
                                        'avg_pembacaan'   => $dData['avg_pembacaan'] ?? $d->avg_pembacaan,
                                        'koreksi'         => $dData['koreksi'] ?? $d->koreksi,
                                        'std_dev'         => $dData['std_dev'] ?? $d->std_dev,
                                        'ketidakpastian'  => $dData['ketidakpastian'] ?? $d->ketidakpastian,
                                    ]);
                                }
                            }
                        }
                    }
                    break;

                case 'flowmeter':
                    if ($request->has('flowmeter') && is_array($request->flowmeter)) {
                        foreach ($request->flowmeter as $fData) {
                            if (!empty($fData['id'])) {
                                $f = CalFlowmeterModel::where('kalibrasi_id', $kalibrasi->id)
                                    ->where('id', $fData['id'])
                                    ->first();
                                if ($f) {
                                    $f->update([
                                        'titik_kalibrasi' => $fData['titik_kalibrasi'] ?? $f->titik_kalibrasi,
                                        'nilai_master'    => $fData['nilai_master'] ?? $f->nilai_master,
                                        'avg_pembacaan'   => $fData['avg_pembacaan'] ?? $f->avg_pembacaan,
                                        'koreksi'         => $fData['koreksi'] ?? $f->koreksi,
                                        'std_dev'         => $fData['std_dev'] ?? $f->std_dev,
                                        'ketidakpastian'  => $fData['ketidakpastian'] ?? $f->ketidakpastian,
                                    ]);
                                }
                            }
                        }
                    }
                    break;
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data kalibrasi dan sertifikat berhasil diperbarui!',
                'data'    => $kalibrasi
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
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
        $query = KalibrasiSertifikatModel::with([
            'kalibrasi.alat',
            'kalibrasi.user',
            'kalibrasi.pressure',
            'kalibrasi.temperature',
            'kalibrasi.volumetrik',
            'kalibrasi.thermohygrometer',
            'kalibrasi.jangkaSorong',
            'kalibrasi.instrumen',
            'kalibrasi.dimensi',
            'kalibrasi.keseragamanSkala'
        ])
            ->where('status', '!=', 'rejected');

        $user = Auth::user();
        $isEngineeringOrAdmin = ($user->departemen && strtolower($user->departemen) === 'engineering')
            || ($user->jabatan && in_array(strtolower($user->jabatan), ['admin', 'superadmin']));

        if (!$isEngineeringOrAdmin) {
            $query->whereHas('kalibrasi.alat', function ($q) use ($user) {
                $q->where('departemen_pemilik', $user->departemen);
            });
        }

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

        // Append max_koreksi to each item in collection
        $data->getCollection()->transform(function ($item) {
            $item->max_koreksi = $item->kalibrasi ? $item->kalibrasi->getMaxKoreksi() : 0;
            return $item;
        });

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
