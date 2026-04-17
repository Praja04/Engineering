<?php

namespace App\Http\Controllers\Kalibrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kalibrasi\ThermohygrometerRequest;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use App\Models\Kalibrasi\Thermohygrometer\KalibrasiThermohygrometerDetailModel;
use App\Models\Kalibrasi\Thermohygrometer\KalibrasiThermohygrometerModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KalibrasiThermohygrometerController extends Controller
{
    public function showForm()
    {
        $alat = AlatKalibrasiModel::select('id', 'kode_alat', 'nama_alat')
            ->whereIn('jenis_kalibrasi', ['temperature', 'thermohygrometer'])
            ->get();

        return view('kalibrasi.thermohygrometer.form', compact('alat'));
    }

    public function viewData()
    {
        return view('kalibrasi.thermohygrometer.data');
    }

    public function store(ThermohygrometerRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $kalibrasi = KalibrasiModel::create([
                'alat_id' => $validated['alat_id'],
                'user_id' => Auth::id() ?? 1,
                'lokasi_kalibrasi' => $validated['lokasi_kalibrasi'],
                'suhu_ruangan' => $validated['suhu_ruangan'] . '°C ± 1°C' ?? '-',
                'kelembaban' => $validated['kelembaban'] . '% ± 3%' ?? '-',
                'tgl_kalibrasi' => $validated['tgl_kalibrasi'],
                'tgl_kalibrasi_ulang' => Carbon::parse($validated['tgl_kalibrasi'])->addYearNoOverflow(),
                'jenis_kalibrasi' => 'thermohygrometer',
            ]);

            KalibrasiSertifikatModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            foreach ($validated['data'] as $row) {

                $thermo = KalibrasiThermohygrometerModel::create([
                    'kalibrasi_id' => $kalibrasi->id,
                    'titik_kalibrasi' => $row['titik_kalibrasi'] ?? null,
                    'posisi' => $row['posisi'] ?? null,
                ]);

                $suhuArr = [];
                $rhArr   = [];

                for ($i = 0; $i < 3; $i++) {

                    $standarSuhu = $row['standar'][$i]['suhu'] ?? null;
                    $standarRh   = $row['standar'][$i]['rh'] ?? null;

                    $alatSuhu    = $row['alat'][$i]['suhu'] ?? null;
                    $alatRh      = $row['alat'][$i]['rh'] ?? null;

                    $koreksiStandarSuhu = 0;
                    $koreksiStandarRh   = 0;

                    $tekananSuhu = $standarSuhu + $koreksiStandarSuhu;
                    $tekananRh   = $standarRh + $koreksiStandarRh;

                    $koreksiAlatSuhu = $tekananSuhu - $alatSuhu;
                    $koreksiAlatRh   = $tekananRh - $alatRh;

                    KalibrasiThermohygrometerDetailModel::create([
                        'thermohygro_id' => $thermo->id,
                        'urutan' => $i,

                        'penunjuk_standar_suhu' => $standarSuhu,
                        'penunjuk_alat_suhu' => $alatSuhu,
                        'koreksi_standar_suhu' => $koreksiStandarSuhu,
                        'tekanan_standar_suhu' => $tekananSuhu,
                        'koreksi_alat_suhu' => $koreksiAlatSuhu,

                        'penunjuk_standar_rh' => $standarRh,
                        'penunjuk_alat_rh' => $alatRh,
                        'koreksi_standar_rh' => $koreksiStandarRh,
                        'tekanan_standar_rh' => $tekananRh,
                        'koreksi_alat_rh' => $koreksiAlatRh,
                    ]);

                    $suhuArr[] = ['penunjuk_alat' => $alatSuhu, 'tekanan' => $tekananSuhu];
                    $rhArr[]   = ['penunjuk_alat' => $alatRh,   'tekanan' => $tekananRh];
                }

                // ===============================
                // HITUNG AVG & STD
                // ===============================

                $avg = fn($arr, $key) =>
                count($arr) ? array_sum(array_column($arr, $key)) / count($arr) : null;

                $std = fn($arr, $key) =>
                count($arr) > 1
                    ? sqrt(array_sum(array_map(
                        fn($x) => pow($x[$key] - $avg($arr, $key), 2),
                        $arr
                    )) / (count($arr) - 1))
                    : 0;

                $avgSuhuAlat = $avg($suhuArr, 'penunjuk_alat');
                $avgSuhuTekanan = $avg($suhuArr, 'tekanan');

                $avgRhAlat = $avg($rhArr, 'penunjuk_alat');
                $avgRhTekanan = $avg($rhArr, 'tekanan');

                $defaultKetidakpastianSuhu = [
                    8  => 0.45647,
                    20 => 0.44461,
                    30 => 0.46721,
                ];

                $defaultKetidakpastianRh = [
                    8  => 2.80105,
                    20 => 2.74458,
                    30 => 2.91234,
                ];

                $referensi = $row['titik_kalibrasi'] ?? null;

                if (!$referensi && $avgSuhuTekanan !== null) {
                    $referensi = round($avgSuhuTekanan); // atau bisa floor/ceil
                }

                $ketidakpastianSuhu = $defaultKetidakpastianSuhu[$referensi] ?? 0.44461;
                $ketidakpastianRh   = $defaultKetidakpastianRh[$referensi] ?? 2.74458;

                $thermo->update([
                    'avg_penunjuk_alat_suhu' => $avgSuhuAlat,
                    'avg_tekanan_standar_suhu' => $avgSuhuTekanan,
                    'avg_kor_alat_suhu' => $avgSuhuTekanan - $avgSuhuAlat,
                    'std_deviasi_suhu' => $std($suhuArr, 'tekanan'),
                    'ketidak_pastian_suhu' => $ketidakpastianSuhu,

                    'avg_penunjuk_alat_rh' => $avgRhAlat,
                    'avg_tekanan_standar_rh' => $avgRhTekanan,
                    'avg_kor_alat_rh' => $avgRhTekanan - $avgRhAlat,
                    'std_deviasi_rh' => $std($rhArr, 'tekanan'),
                    'ketidak_pastian_rh' => $ketidakpastianRh,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Thermohygrometer berhasil disimpan.',
            ]);
        } catch (\Throwable $th) {

            DB::rollBack();

            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getData()
    {
        try {
            // ambil data kalibrasi + relasi pressure & gabungan
            $data = KalibrasiModel::with([
                'thermohygrometer.details',
                'alat'
            ])
                ->where('jenis_kalibrasi', 'thermohygrometer')
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


    public function destroy(string $id)
    {
        $kalibrasi = KalibrasiModel::findOrFail($id);

        // Hapus kalibrasi utama
        $kalibrasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kalibrasi berhasil dihapus!'
        ]);
    }
}
