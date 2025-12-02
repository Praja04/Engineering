<?php

namespace App\Http\Controllers\Kalibrasi;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use App\Models\kalibrasi\Temperature\KalibrasiTemperatureModel;
use App\Models\kalibrasi\Temperature\KalibrasiTemperatureGabModel;

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

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'alat_id' => 'required|exists:alat_kalibrasi,id',
            'lokasi_kalibrasi' => 'required|string|max:255',
            'suhu_ruangan_final' => 'required|string|max:50',
            'kelembaban_final' => 'required|string|max:50',
            'tgl_kalibrasi' => 'required|date',

            'temperature' => 'required|array',
            'temperature.*.titik_kalibrasi' => 'required|numeric',
            'temperature.*.standar_1' => 'required|numeric',
            'temperature.*.standar_2' => 'required|numeric',
            'temperature.*.standar_3' => 'required|numeric',
            'temperature.*.alat_1' => 'required|numeric',
            'temperature.*.alat_2' => 'required|numeric',
            'temperature.*.alat_3' => 'required|numeric',
            'temperature.*.koreksi_standar' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($validated) {

            // Simpan data utama kalibrasi
            $kalibrasi = KalibrasiModel::create([
                'alat_id' => $validated['alat_id'],
                'user_id' => Auth::id() ?? 1,
                'lokasi_kalibrasi' => $validated['lokasi_kalibrasi'] ?? '-',
                'suhu_ruangan' => $validated['suhu_ruangan_final'] ?? '-',
                'kelembaban' => $validated['kelembaban_final'] ?? '-',
                'tgl_kalibrasi' => $validated['tgl_kalibrasi'],
                'tgl_kalibrasi_ulang' => Carbon::parse($validated['tgl_kalibrasi'])->addYearNoOverflow(),
                'jenis_kalibrasi' => 'temperature',
            ]);

            // Buat sertifikat kalibrasi
            KalibrasiSertifikatModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            $perTitik = [];

            // Loop tiap titik input
            foreach ($validated['temperature'] as $t) {
                // Hitung rata-rata 3 pengukuran standar & alat
                $avg_standar = ($t['standar_1'] + $t['standar_2'] + $t['standar_3']) / 3;
                $avg_alat = ($t['alat_1'] + $t['alat_2'] + $t['alat_3']) / 3;

                // Hitung suhu standar & koreksi alat
                $koreksi_standar = 0;
                if ($t['titik_kalibrasi'] >= 38) {
                    $koreksi_standar = -0.10;
                } else {
                    $koreksi_standar = -0.30;
                }

                $suhu_standar = $avg_standar + $koreksi_standar;
                $koreksi_alat = $suhu_standar - $avg_alat;


                // Simpan ke tabel detail
                KalibrasiTemperatureModel::create([
                    'kalibrasi_id' => $kalibrasi->id,
                    'titik_kalibrasi' => $t['titik_kalibrasi'],
                    'penunjuk_standar' => $avg_standar,
                    'penunjuk_alat' => $avg_alat,
                    'koreksi_standar' => $koreksi_standar,
                    'suhu_standar' => $suhu_standar,
                    'koreksi_alat' => $koreksi_alat,
                ]);

                $perTitik[$t['titik_kalibrasi']][] = [
                    'penunjuk_alat' => $avg_alat,
                    'suhu_standar' => $suhu_standar,
                ];
            }

            // Fungsi bantu rata-rata & standar deviasi
            $avg = fn($arr, $field) => count($arr)
                ? array_sum(array_column($arr, $field)) / count($arr)
                : null;

            $std = fn($arr, $field) => count($arr) > 1
                ? sqrt(array_sum(array_map(fn($x) => pow($x[$field] - $avg($arr, $field), 2), $arr)) / (count($arr) - 1))
                : 0;

            // Static uncertainty default
            $ketidakPastian = 0;
            $titik_kalibrasi = $t['titik_kalibrasi'];

            switch ($titik_kalibrasi) {
                case 30:
                    $ketidakPastian = 1.28740;
                    break;
                case 32:
                    $ketidakPastian = 1.15778;
                    break;
                case 34:
                    $ketidakPastian = 1.15778;
                    break;
                case 36:
                    $ketidakPastian = 1.15778;
                    break;
                case 38:
                    $ketidakPastian = 1.15778;
                    break;
                case 40:
                    $ketidakPastian = 1.15778;
                    break;
                case 60:
                    $ketidakPastian = 1.15778;
                    break;
                case 39.4:
                    $ketidakPastian = 1.15778;
                    break;
                case 39.6:
                    $ketidakPastian = 1.18141;
                    break;
                case 39.8:
                    $ketidakPastian = 1.18141;
                    break;
                default:
                    $ketidakPastian = 1.15778;
                    break;
            }

            // Loop tiap titik kalibrasi untuk simpan gabungan
            foreach ($perTitik as $titik => $dataTitik) {
                $avg_penunjuk_alat = $avg($dataTitik, 'penunjuk_alat');
                $avg_suhu_standar = $avg($dataTitik, 'suhu_standar');
                $avg_kor_alat = $avg_suhu_standar - $avg_penunjuk_alat;
                $stdev = $std($dataTitik, 'suhu_standar');

                KalibrasiTemperatureGabModel::create([
                    'kalibrasi_id' => $kalibrasi->id,
                    'titik_kalibrasi' => $titik,
                    'avg_penunjuk_alat' => $avg_penunjuk_alat,
                    'avg_suhu_standar' => $avg_suhu_standar,
                    'avg_kor_alat' => $avg_kor_alat,
                    'stdev' => $stdev,
                    'ketidakpastian' => $ketidakPastian,
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data kalibrasi suhu berhasil disimpan.',
        ], 200);
    }

    public function getData()
    {
        try {
            $data = KalibrasiModel::with([
                'temperature' => function ($q) {
                    $q->orderBy('titik_kalibrasi');
                },
                'temperatureGabungan',
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
