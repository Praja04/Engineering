<?php

namespace App\Http\Controllers\Kalibrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kalibrasi\PressureRequest;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use App\Models\Kalibrasi\Pressure\KalibrasiPressureDetailModel;
use App\Models\Kalibrasi\Pressure\KalibrasiPressureModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KalibrasiPressureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alat = AlatKalibrasiModel::select('id', 'kode_alat', 'nama_alat')
            ->where('jenis_kalibrasi', 'pressure')
            ->get();

        return view('kalibrasi.pressure.index', compact('alat'));
    }

    public function viewData()
    {
        return view('kalibrasi.pressure.data');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PressureRequest $request)
    {
        DB::beginTransaction();

        try {

            $staticU = [
                'naik' => [
                    0 => 0.059872897,
                    1 => 0.059872897,
                    2 => 0.059872897,
                    3 => 0.059872897,
                    4 => 0.059872897,
                    5 => 0.059872897,
                    6 => 0.059872897,
                    7 => 0.059872897,
                    8 => 0.079157482,
                ],
                'turun' => [
                    0 => 0.059872897,
                    1 => 0.059872897,
                    2 => 0.059872897,
                    3 => 0.059872897,
                    4 => 0.059872897,
                    5 => 0.059872897,
                    6 => 0.059872897,
                    7 => 0.059872897,
                    8 => 0.093090854,
                ],
            ];

            $kalibrasi = KalibrasiModel::create([
                'alat_id' => $request->alat_id,
                'user_id' => Auth::id(),
                'lokasi_kalibrasi' => $request->lokasi_kalibrasi ?? '-',
                'suhu_ruangan' => $request->suhu_ruangan . '°C ± 1°C',
                'kelembaban' => $request->kelembaban . '% ± 3%',
                'tgl_kalibrasi' => $request->tgl_kalibrasi,
                'tgl_kalibrasi_ulang' => Carbon::parse($request->tgl_kalibrasi)->addYear(),
                'jenis_kalibrasi' => 'pressure',
            ]);

            KalibrasiSertifikatModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            foreach ($request->pressure as $index => $p) {

                $titik = $p['titik_kalibrasi'];

                $alatNaik = collect($p['naik']['alat'])->map(fn($v) => (float)$v);
                $standarNaik = collect($p['naik']['standar'])->map(fn($v) => (float)$v < 1 ? (float)$v * 10 : (float)$v);

                $alatTurun = collect($p['turun']['alat'])->map(fn($v) => (float)$v);
                $standarTurun = collect($p['turun']['standar'])->map(fn($v) => (float)$v < 1 ? (float)$v * 10 : (float)$v);

                $avgAlatNaik = $alatNaik->avg();
                $avgStandarNaik = $standarNaik->avg();
                $stdNaik = $this->calculateStdDev($standarNaik);

                $avgAlatTurun = $alatTurun->avg();
                $avgStandarTurun = $standarTurun->avg();
                $stdTurun = $this->calculateStdDev($standarTurun);

                $koreksiStandarNaik = 0;
                $tekananStandarNaik = $avgStandarNaik;
                $koreksiAlatNaik = $avgStandarNaik - $avgAlatNaik;

                $koreksiStandarTurun = 0;
                $tekananStandarTurun = $avgStandarTurun;
                $koreksiAlatTurun = $avgStandarTurun - $avgAlatTurun;

                $ketidakpastianNaik = $staticU['naik'][$index] ?? 0.059872897;
                $ketidakpastianTurun = $staticU['turun'][$index] ?? 0.059872897;

                $uNaik = $ketidakpastianNaik;
                $uTurun = $ketidakpastianTurun;

                $uGabungan = sqrt(pow($uNaik, 2) + pow($uTurun, 2));

                $pressure = KalibrasiPressureModel::create([
                    'kalibrasi_id' => $kalibrasi->id,
                    'titik_kalibrasi' => $titik,

                    'avg_penunjuk_alat_naik' => $avgAlatNaik,
                    'avg_penunjuk_alat_turun' => $avgAlatTurun,

                    'avg_tekanan_standar_naik' => $tekananStandarNaik,
                    'avg_tekanan_standar_turun' => $tekananStandarTurun,

                    'avg_koreksi_alat_naik' => $koreksiAlatNaik,
                    'avg_koreksi_alat_turun' => $koreksiAlatTurun,

                    'std_deviasi_naik' => $stdNaik,
                    'std_deviasi_turun' => $stdTurun,

                    'ketidakpastian_naik' => $ketidakpastianNaik,
                    'ketidakpastian_turun' => $ketidakpastianTurun,

                    'u_naik' => $uNaik,
                    'u_turun' => $uTurun,
                    'u_naik_kuadrat' => pow($uNaik, 2),
                    'u_turun_kuadrat' => pow($uTurun, 2),
                    'u_gabungan' => $uGabungan,
                ]);

                // NAiK (3 percobaan)
                foreach ($alatNaik as $i => $alatValue) {

                    $standarValue = $standarNaik[$i];

                    KalibrasiPressureDetailModel::create([
                        'pressure_id' => $pressure->id,
                        'arah' => 'naik',
                        'penunjuk_standar' => $standarValue,
                        'penunjuk_alat' => $alatValue,
                        'koreksi_standar' => 0,
                        'tekanan_standar' => $standarValue,
                        'koreksi_alat' => $standarValue - $alatValue,
                    ]);
                }

                // TURUN (3 percobaan)
                foreach ($alatTurun as $i => $alatValue) {

                    $standarValue = $standarTurun[$i];

                    KalibrasiPressureDetailModel::create([
                        'pressure_id' => $pressure->id,
                        'arah' => 'turun',
                        'penunjuk_standar' => $standarValue,
                        'penunjuk_alat' => $alatValue,
                        'koreksi_standar' => 0,
                        'tekanan_standar' => $standarValue,
                        'koreksi_alat' => $standarValue - $alatValue,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data kalibrasi pressure berhasil disimpan.',
                'data' => $kalibrasi->load('pressure.details')
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function calculateStdDev($collection)
    {
        $values = $collection->filter(function ($v) {
            return is_numeric($v);
        });

        $count = $values->count();

        if ($count <= 1) {
            return 0;
        }

        $mean = $values->avg();

        $variance = $values->reduce(function ($carry, $item) use ($mean) {
            return $carry + pow($item - $mean, 2);
        }, 0) / ($count - 1);

        return sqrt($variance);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $alat = AlatKalibrasiModel::find($id);

        if (!$alat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Alat tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $alat
        ]);
    }

    public function getData()
    {
        try {
            // ambil data kalibrasi + relasi pressure & gabungan
            $data = KalibrasiModel::with([
                'pressure.details',
                'alat'
            ])
                ->where('jenis_kalibrasi', 'pressure')
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kalibrasi = KalibrasiModel::findOrFail($id);

        // Hapus kalibrasi utama
        $kalibrasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kalibrasi pressure berhasil dihapus.'
        ]);
    }
}
