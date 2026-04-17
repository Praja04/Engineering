<?php

namespace App\Http\Controllers\Kalibrasi;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Kalibrasi\TemperatureRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use App\Models\kalibrasi\Temperature\KalibrasiTemperatureDetailModel;
use App\Models\kalibrasi\Temperature\KalibrasiTemperatureModel;

class KalibrasiTemperatureController extends Controller
{
    public function showForm()
    {
        $alat = AlatKalibrasiModel::select('id', 'kode_alat', 'nama_alat')
            ->where('jenis_kalibrasi', 'temperature')
            ->get();

        return view('kalibrasi.temperature.form', compact('alat'));
    }

    public function viewData()
    {
        return view('kalibrasi.temperature.data');
    }

    public function store(TemperatureRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {

            $kalibrasi = KalibrasiModel::create([
                'alat_id' => $validated['alat_id'],
                'user_id' => Auth::id() ?? 1,
                'lokasi_kalibrasi' => $validated['lokasi_kalibrasi'] ?? '-',
                'suhu_ruangan' => $validated['suhu_ruangan'] . '°C ± 1°C' ?? '-',
                'kelembaban' => $validated['kelembaban'] . '% ± 3%' ?? '-',
                'tgl_kalibrasi' => $validated['tgl_kalibrasi'],
                'tgl_kalibrasi_ulang' => Carbon::parse($validated['tgl_kalibrasi'])->addYearNoOverflow(),
                'jenis_kalibrasi' => 'temperature',
            ]);

            KalibrasiSertifikatModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            foreach ($validated['data'] as $t) {

                $dataDetail = [];

                foreach ($t['penunjuk_standar'] as $index => $standar) {

                    $alat = $t['penunjuk_alat'][$index];

                    $koreksi_standar = $t['titik_kalibrasi'] >= 38 ? -0.10 : -0.30;

                    $suhu_standar = $standar + $koreksi_standar;
                    $koreksi_alat = $suhu_standar - $alat;

                    $dataDetail[] = [
                        'penunjuk_standar' => $standar,
                        'penunjuk_alat'    => $alat,
                        'koreksi_standar'  => $koreksi_standar,
                        'suhu_standar'     => $suhu_standar,
                        'koreksi_alat'     => $koreksi_alat,
                    ];
                }

                // ========================
                // HITUNG RATA-RATA
                // ========================
                $avg_penunjuk_alat = collect($dataDetail)->avg('penunjuk_alat');
                $avg_suhu_standar  = collect($dataDetail)->avg('suhu_standar');
                $avg_kor_alat      = $avg_suhu_standar - $avg_penunjuk_alat;

                $mean = $avg_suhu_standar;

                $stdev = sqrt(
                    collect($dataDetail)
                        ->map(fn($x) => pow($x['suhu_standar'] - $mean, 2))
                        ->sum() / (count($dataDetail) - 1)
                );

                $ketidakpastian = match ($t['titik_kalibrasi']) {
                    30 => 1.28740,
                    39.6, 39.8 => 1.18141,
                    default => 1.15778,
                };

                $temperature = KalibrasiTemperatureModel::create([
                    'kalibrasi_id'      => $kalibrasi->id,
                    'titik_kalibrasi'   => $t['titik_kalibrasi'],
                    'avg_penunjuk_alat' => $avg_penunjuk_alat,
                    'avg_suhu_standar'  => $avg_suhu_standar,
                    'avg_kor_alat'      => $avg_kor_alat,
                    'stdev'             => $stdev,
                    'ketidakpastian'    => $ketidakpastian,
                ]);

                foreach ($dataDetail as $detail) {
                    KalibrasiTemperatureDetailModel::create([
                        'temperature_id' => $temperature->id,
                        ...$detail
                    ]);
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data kalibrasi suhu berhasil disimpan.',
        ]);
    }

    public function getData()
    {
        try {
            $data = KalibrasiModel::with([
                'temperature.details',
                'alat'
            ])
                ->where('jenis_kalibrasi', 'temperature')
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

        $kalibrasi->temperature()->delete();
        $kalibrasi->temperatureGabungan()->delete();

        // Hapus kalibrasi utama
        $kalibrasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kalibrasi berhasil dihapus!'
        ]);
    }
}
