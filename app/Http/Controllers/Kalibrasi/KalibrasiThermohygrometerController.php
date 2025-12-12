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
use App\Models\Kalibrasi\Thermohygrometer\KalibrasiThermohygrometerModel;
use App\Models\Kalibrasi\Thermohygrometer\KalibrasiThermohygrometerGabModel;

class KalibrasiThermohygrometerController extends Controller
{
    public function showForm()
    {
        $alat = AlatKalibrasiModel::select('id', 'kode_alat', 'nama_alat')
            ->where('jenis_kalibrasi', 'thermohygrometer')
            ->get();

        return view('kalibrasi.thermohygrometer.form', compact('alat'));
    }

    public function viewData()
    {
        return view('kalibrasi.thermohygrometer.data');
    }

    public function store(Request $request)
    {
        $flattenedThermo = [];
        foreach ($request->input('thermo', []) as $tipe => $dataTipe) {
            foreach ($dataTipe as $titik => $rows) {
                foreach ($rows as $index => $row) {
                    $flattenedThermo[] = array_merge($row, [
                        'titik_kalibrasi' => $titik,
                        'tipe_hitung' => $tipe,
                    ]);
                }
            }
        }

        // Replace thermo input dengan versi flattened
        $request->merge(['thermo' => $flattenedThermo]);

        // --- Validasi input ---
        $validated = $request->validate([
            'alat_id' => 'required|exists:alat_kalibrasi,id',
            'lokasi_kalibrasi' => 'required|string|max:255',
            'suhu_ruangan_final' => 'required|string|max:50',
            'kelembaban_final' => 'required|string|max:50',
            'tgl_kalibrasi' => 'required|date',

            'thermo' => 'required|array|min:1',
            'thermo.*.tipe_hitung' => 'required|in:suhu,rh',
            'thermo.*.posisi' => 'required|string|max:100',
            'thermo.*.titik_kalibrasi' => 'required|numeric',
            'thermo.*.penunjuk_standar' => 'required|numeric',
            'thermo.*.penunjuk_alat' => 'required|numeric',
            'thermo.*.koreksi_standar' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            // Simpan data utama kalibrasi
            $kalibrasi = KalibrasiModel::create([
                'alat_id' => $validated['alat_id'],
                'user_id' => Auth::id() ?? 1,
                'lokasi_kalibrasi' => $validated['lokasi_kalibrasi'] ?? '-',
                'suhu_ruangan' => $validated['suhu_ruangan_final'] ?? '-',
                'kelembaban' => $validated['kelembaban_final'] ?? '-',
                'tgl_kalibrasi' => $validated['tgl_kalibrasi'],
                'tgl_kalibrasi_ulang' => Carbon::parse($validated['tgl_kalibrasi'])->addYearNoOverflow(),
                'jenis_kalibrasi' => 'thermohygrometer',
            ]);

            KalibrasiSertifikatModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            // Pisahkan data titik
            $perTitik = [];

            // simpan ke thermohygrometer model
            foreach ($validated['thermo'] as $row) {
                $titik = $row['titik_kalibrasi'];
                $tipe = $row['tipe_hitung'];
                $tekananStandar = $row['penunjuk_standar'] + ($row['koreksi_standar'] ?? 0);
                $koreksiAlat = $tekananStandar - $row['penunjuk_alat'];

                KalibrasiThermohygrometerModel::create([
                    'kalibrasi_id' => $kalibrasi->id,
                    'titik_kalibrasi' => $titik,
                    'tipe_hitung' => $tipe,
                    'posisi' => $row['posisi'],
                    'penunjuk_standar' => $row['penunjuk_standar'],
                    'penunjuk_alat' => $row['penunjuk_alat'],
                    'koreksi_standar' => $row['koreksi_standar'] ?? null,
                    'tekanan_standar' => $tekananStandar,
                    'koreksi_alat' => $koreksiAlat,
                ]);

                // Group berdasarkan titik dan tipe
                $perTitik[$titik][$tipe][] = [
                    'posisi' => $row['posisi'] ?? null,
                    'penunjuk_alat' => $row['penunjuk_alat'],
                    'tekanan_standar' => $tekananStandar,
                    'koreksi_alat' => $koreksiAlat,
                ];
            }

            $avg = fn($arr, $field) => count($arr) ? array_sum(array_column($arr, $field)) / count($arr) : null;
            $std = fn($arr, $field) => count($arr) > 1
                ? sqrt(array_sum(array_map(fn($x) => pow($x[$field] - $avg($arr, $field), 2), $arr)) / (count($arr) - 1))
                : 0;

            // Loop tiap titik kalibrasi untuk simpan ke gabungan
            $ketidakPastian = [
                'suhu' => 0.44461,
                'rh'   => 2.74458,
            ];

            foreach ($perTitik as $titik => $tipe) {
                $suhu = $tipe['suhu'] ?? [];
                $rh   = $tipe['rh'] ?? [];
                $posisiGab = $suhu[0]['posisi'] ?? $rh[0]['posisi'] ?? null;

                // ambil ketidakpastian tetap
                $ketidakpastianSuhu = $ketidakPastian['suhu'];
                $ketidakpastianRh   = $ketidakPastian['rh'];

                // --- Hitung rata-rata & stdev suhu
                $avgPenunjukAlatSuhu     = $avg($suhu, 'penunjuk_alat');
                $avgTekananStandarSuhu   = $avg($suhu, 'tekanan_standar');
                $avgKoreksiAlatSuhu      = $avgTekananStandarSuhu - $avgPenunjukAlatSuhu;
                $stdDeviasiSuhu          = $std($suhu, 'tekanan_standar');

                // --- Hitung rata-rata & stdev rh
                $avgPenunjukAlatRh       = $avg($rh, 'penunjuk_alat');
                $avgTekananStandarRh     = $avg($rh, 'tekanan_standar');
                $avgKoreksiAlatRh        = $avgTekananStandarRh - $avgPenunjukAlatRh;
                $stdDeviasiRh            = $std($rh, 'tekanan_standar');

                // --- Simpan ke tabel gabungan
                KalibrasiThermohygrometerGabModel::create([
                    'kalibrasi_id' => $kalibrasi->id,
                    'titik_kalibrasi' => $titik,
                    'posisi' => $posisiGab,

                    'avg_penunjuk_alat_suhu' => $avgPenunjukAlatSuhu,
                    'avg_tekanan_standar_suhu' => $avgTekananStandarSuhu,
                    'avg_kor_alat_suhu' => $avgKoreksiAlatSuhu,
                    'std_deviasi_suhu' => $stdDeviasiSuhu,
                    'ketidak_pastian_suhu' => $ketidakpastianSuhu,

                    'avg_penunjuk_alat_rh' => $avgPenunjukAlatRh,
                    'avg_tekanan_standar_rh' => $avgTekananStandarRh,
                    'avg_kor_alat_rh' => $avgKoreksiAlatRh,
                    'std_deviasi_rh' => $stdDeviasiRh,
                    'ketidak_pastian_rh' => $ketidakpastianRh,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data kalibrasi Thermohygrometer berhasil disimpan.',
                'data' => $kalibrasi->load('thermohygrometer')
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function getData()
    {
        try {
            // ambil data kalibrasi + relasi pressure & gabungan
            $data = KalibrasiModel::with([
                'thermohygrometer' => function ($q) {
                    $q->orderBy('titik_kalibrasi');
                },
                'thermohygrometerGabungan',
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

        $kalibrasi->thermohygrometer()->delete();
        $kalibrasi->thermohygrometerGabungan()->delete();

        // Hapus kalibrasi utama
        $kalibrasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kalibrasi berhasil dihapus!'
        ]);
    }
}
