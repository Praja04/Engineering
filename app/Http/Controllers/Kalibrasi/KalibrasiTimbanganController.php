<?php

namespace App\Http\Controllers\Kalibrasi;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\Timbangan\TareModel;
use App\Models\Kalibrasi\Timbangan\PingganModel;
use App\Models\Kalibrasi\Timbangan\SmryTareModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use App\Models\Kalibrasi\Timbangan\PembacaanModel;
use App\Models\Kalibrasi\Timbangan\HisterisisModel;
use App\Models\Kalibrasi\Timbangan\SmryPingganModel;
use App\Models\Kalibrasi\Master\MasterTimbanganModel;
use App\Models\Kalibrasi\Timbangan\SmryPembacaanModel;
use App\Models\Kalibrasi\Timbangan\SmryHisterisisModel;
use App\Models\Kalibrasi\Timbangan\KeseragamanSkalaModel;
use App\Models\Kalibrasi\Timbangan\SmryKeseragamanSkalaModel;

class KalibrasiTimbanganController extends Controller
{
    public function showForm()
    {
        $alat = AlatKalibrasiModel::select('id', 'kode_alat', 'nama_alat')
            ->where('jenis_kalibrasi', 'timbangan')
            ->get();

        return view('kalibrasi.timbangan.form', compact('alat'));
    }

    public function viewData()
    {
        return view('kalibrasi.timbangan.data');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Data utama kalibrasi
            'alat_id' => 'required|exists:alat_kalibrasi,id',
            'lokasi_kalibrasi' => 'required|string|max:255',
            'suhu_ruangan_final' => 'required|string|max:50',
            'kelembaban_final' => 'required|string|max:50',
            'tgl_kalibrasi' => 'required|date',

            'pembacaan' => 'required|array',
            'keseragaman_skala' => 'required|array',
            'pinggan' => 'required|array',
            'tare' => 'required|array',
            'histerisis' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $kalibrasi = KalibrasiModel::create([
                'alat_id' => $validated['alat_id'],
                'user_id' => Auth::id() ?? 1,
                'lokasi_kalibrasi' => $validated['lokasi_kalibrasi'],
                'suhu_ruangan' => $validated['suhu_ruangan_final'],
                'kelembaban' => $validated['kelembaban_final'],
                'tgl_kalibrasi' => $validated['tgl_kalibrasi'],
                'tgl_kalibrasi_ulang' => Carbon::parse($validated['tgl_kalibrasi'])->addYearNoOverflow(),
                'jenis_kalibrasi' => 'timbangan',
            ]);

            KalibrasiSertifikatModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            // Kemampuan Ulang Pembacaan
            foreach ($validated['pembacaan'] as $namaKemampuan => $titikList) {
                foreach ($titikList as $titikData) {
                    $titik = $titikData['titik'] ?? null;
                    $ulanganList = $titikData['percobaan'] ?? [];

                    // === Hitung selisih dan maks_perbedaan secara urut ===
                    $selisihList = [];
                    $maksList = [];

                    foreach ($ulanganList as $i => $percobaan) {
                        $pembacaanZ = $percobaan['pembacaan_z'] ?? null;
                        $pembacaanM = $percobaan['pembacaan_m'] ?? null;

                        $selisih = (isset($pembacaanM, $pembacaanZ))
                            ? $pembacaanM - $pembacaanZ
                            : null;

                        $selisihList[$i] = $selisih;

                        $maksList[$i] = isset($ulanganList[$i + 1]['pembacaan_z'], $ulanganList[$i + 1]['pembacaan_m'])
                            ? abs(($ulanganList[$i + 1]['pembacaan_m'] - $ulanganList[$i + 1]['pembacaan_z']) - $selisih)
                            : null;

                        PembacaanModel::create([
                            'kalibrasi_id'   => $kalibrasi->id,
                            'kemampuan'      => $namaKemampuan,
                            'titik'          => $titik,
                            'ulangan'        => $percobaan['ulangan_ke'] ?? null,
                            'pembacaan_z'    => $pembacaanZ,
                            'pembacaan_m'    => $pembacaanM,
                            'selisih'        => $selisih,
                            'maks_perbedaan' => $maksList[$i],
                        ]);
                    }

                    // === Simpan summary ===
                    $filteredSelisih = array_filter($selisihList, fn($v) => $v !== null);
                    $filteredMaks = array_filter($maksList, fn($v) => $v !== null);

                    if (count($filteredSelisih) > 0) {
                        $mean = array_sum($filteredSelisih) / count($filteredSelisih);
                        $stdDev = sqrt(array_sum(array_map(
                            fn($v) => pow($v - $mean, 2),
                            $filteredSelisih
                        )) / count($filteredSelisih));

                        SmryPembacaanModel::create([
                            'kalibrasi_id' => $kalibrasi->id,
                            'kemampuan' => $namaKemampuan,
                            'std_dev' => round($stdDev, 8),
                            'maks_perbedaan_akhir' => count($filteredMaks) ? max($filteredMaks) : null,
                        ]);
                    }
                }
            }

            // Data Pinggan
            $pingganData = $validated['pinggan'];
            if (!empty($pingganData)) {
                foreach ($pingganData['percobaan'] ?? [] as $percobaan) {
                    // Ambil data per percobaan
                    $tengah    = $percobaan['tengah'] ?? null;
                    $depan     = $percobaan['depan'] ?? null;
                    $belakang  = $percobaan['belakang'] ?? null;
                    $kiri      = $percobaan['kiri'] ?? null;
                    $kanan     = $percobaan['kanan'] ?? null;

                    // Simpan data mentah
                    PingganModel::create([
                        'kalibrasi_id' => $kalibrasi->id,
                        'diameter'     => $pingganData['diameter'] ?? null,
                        'massa'        => $pingganData['massa'] ?? null,
                        'percobaan'    => $percobaan['percobaan_ke'] ?? null,
                        'tengah'       => $tengah,
                        'depan'        => $depan,
                        'belakang'     => $belakang,
                        'kiri'         => $kiri,
                        'kanan'        => $kanan,
                    ]);

                    // === Hitung Summary Data
                    $smryTengah   = $tengah - $tengah;
                    $smryDepan    = isset($depan, $tengah) ? $depan - $tengah : null;
                    $smryBelakang = isset($belakang, $tengah) ? $belakang - $tengah : null;
                    $smryKiri     = isset($kiri, $tengah) ? $kiri - $tengah : null;
                    $smryKanan    = isset($kanan, $tengah) ? $kanan - $tengah : null;

                    $pembacaan = array_filter([$tengah, $depan, $belakang, $kiri, $kanan], fn($v) => $v !== null);

                    $minimum   = count($pembacaan) ? min($pembacaan) : null;
                    $maximum   = count($pembacaan) ? max($pembacaan) : null;
                    $selisihMax = (isset($minimum, $maximum)) ? abs($maximum - $minimum) : null;

                    SmryPingganModel::create([
                        'kalibrasi_id'   => $kalibrasi->id,
                        'percobaan'      => $percobaan['percobaan_ke'] ?? null,
                        'smry_tengah'    => $smryTengah,
                        'smry_depan'     => $smryDepan,
                        'smry_belakang'  => $smryBelakang,
                        'smry_kiri'      => $smryKiri,
                        'smry_kanan'     => $smryKanan,
                        'minimum'        => $minimum,
                        'maximum'        => $maximum,
                        'selisih_maks'   => $selisihMax,
                    ]);
                }
            }

            // Data Tare
            $tareData = $validated['tare'];
            if (!empty($tareData['percobaan'])) {

                foreach ($tareData['percobaan'] as $percobaan) {
                    // TANPA pengenolan
                    TareModel::create([
                        'kalibrasi_id' => $kalibrasi->id,
                        'massa'        => $tareData['massa'] ?? 0,
                        'tipe_tare'    => 'tanpa_pengenolan',
                        'beban'        => $percobaan['beban_tanpa'] ?? 0,
                        'pembacaan'    => $percobaan['pembacaan_tanpa'] ?? 0,
                    ]);

                    // DENGAN pengenolan
                    TareModel::create([
                        'kalibrasi_id' => $kalibrasi->id,
                        'massa'        => $tareData['massa'] ?? 0,
                        'tipe_tare'    => 'dengan_pengenolan',
                        'beban'        => $percobaan['beban_dengan'] ?? 0,
                        'pembacaan'    => $percobaan['pembacaan_dengan'] ?? 0,
                    ]);
                }

                // Setelah semua tersimpan, baru hitung summary ---
                $tanpaNol = TareModel::where('kalibrasi_id', $kalibrasi->id)
                    ->where('tipe_tare', 'tanpa_pengenolan')
                    ->get();

                $denganNol = TareModel::where('kalibrasi_id', $kalibrasi->id)
                    ->where('tipe_tare', 'dengan_pengenolan')
                    ->get();

                if ($tanpaNol->isNotEmpty() && $denganNol->isNotEmpty()) {
                    // pisahkan pembacaan M dan Z
                    $zTanpa = $tanpaNol->where('beban', 'like', 'Z%')->pluck('pembacaan')->toArray();
                    $mTanpa = $tanpaNol->where('beban', 'like', 'M%')->pluck('pembacaan')->toArray();
                    $zDengan = $denganNol->where('beban', 'like', 'Z%')->pluck('pembacaan')->toArray();
                    $mDengan = $denganNol->where('beban', 'like', 'M%')->pluck('pembacaan')->toArray();

                    // hitung rata-rata masing-masing
                    $rataZTanpa  = count($zTanpa)  ? array_sum($zTanpa) / count($zTanpa)  : 0;
                    $rataMTanpa  = count($mTanpa)  ? array_sum($mTanpa) / count($mTanpa)  : 0;
                    $rataZDengan = count($zDengan) ? array_sum($zDengan) / count($zDengan) : 0;
                    $rataMDengan = count($mDengan) ? array_sum($mDengan) / count($mDengan) : 0;

                    // rumus
                    $selisihTanpa  = ($rataMTanpa - $rataZTanpa);
                    $selisihDengan = ($rataMDengan - $rataZDengan);
                    $pengaruh       = abs($selisihTanpa - $selisihDengan);

                    SmryTareModel::create(
                        [
                            'kalibrasi_id' => $kalibrasi->id,
                            'massa'                 => $tareData['massa'] ?? 0,
                            'selisih_mz_tanpa_nol'  => $selisihTanpa,
                            'selisih_mz_dengan_nol' => $selisihDengan,
                            'pengaruh'              => $pengaruh,
                        ]
                    );
                }
            }

            // === Data Histerisis ===
            $histerisisData = $validated['histerisis'] ?? [];
            $grouped = [
                1 => ['z1' => null, 'm1' => null, 'm_m' => null, 'm2' => null, 'z2' => null],
                2 => ['z1' => null, 'm1' => null, 'm_m' => null, 'm2' => null, 'z2' => null],
                3 => ['z1' => null, 'm1' => null, 'm_m' => null, 'm2' => null, 'z2' => null],
            ];

            $massaTerkecil = null;
            $massaSetengah = null;

            // Loop semua beban
            foreach ($histerisisData as $item) {
                $massaTerkecil = $item['massa_terkecil'] ?? $massaTerkecil;
                $massaSetengah = $item['massa_setengah'] ?? $massaSetengah;
                $beban = strtoupper($item['beban']);
                $percobaanList = $item['percobaan'] ?? [];

                // Isi ke struktur grouped per nomor percobaan
                foreach ($percobaanList as $index => $nilai) {
                    $no = $index + 1;
                    if (str_contains($beban, 'Z_1')) {
                        $grouped[$no]['z1'] = $nilai;
                    } elseif (str_contains($beban, 'M_2')) {
                        $grouped[$no]['m1'] = $nilai;
                    } elseif (str_contains($beban, 'M+M_3')) {
                        $grouped[$no]['m_m'] = $nilai;
                    } elseif (str_contains($beban, 'M_4')) {
                        $grouped[$no]['m2'] = $nilai;
                    } elseif (str_contains($beban, 'Z_5')) {
                        $grouped[$no]['z2'] = $nilai;
                    }
                }
            }

            // Simpan ke database
            foreach ($grouped as $noPercobaan => $nilai) {
                $m1 = $nilai['m1'];
                $m2 = $nilai['m2'];
                $z1 = $nilai['z1'];
                $z2 = $nilai['z2'];

                $m1_m2 = (isset($m1, $m2)) ? $m1 - $m2 : null;
                $z1_z2 = (isset($z1, $z2)) ? $z1 - $z2 : null;

                HisterisisModel::create([
                    'kalibrasi_id'       => $kalibrasi->id,
                    'pembacaan_terkecil' => $massaTerkecil,
                    'setengah_kapasitas' => $massaSetengah,
                    'percobaan'          => $noPercobaan,
                    'z1'  => $z1,
                    'm1'  => $m1,
                    'm_m' => $nilai['m_m'],
                    'm2'  => $m2,
                    'z2'  => $z2,
                    'm1_m2' => $m1_m2,
                    'z1_z2' => $z1_z2,
                ]);
            }

            // === Hitung Summary Histerisis ===
            $histerisisRecords = HisterisisModel::where('kalibrasi_id', $kalibrasi->id)->get();

            if ($histerisisRecords->isNotEmpty()) {
                $pembacaanTerkecil = $histerisisRecords->first()->pembacaan_terkecil;
                $setengahKapasitas = $histerisisRecords->first()->setengah_kapasitas;

                $m1m2Values = $histerisisRecords->pluck('m1_m2')->filter(fn($v) => !is_null($v));
                $z1z2Values = $histerisisRecords->pluck('z1_z2')->filter(fn($v) => !is_null($v));

                $avg_m1m2 = $m1m2Values->avg() ?? 0;
                $avg_z1z2 = $z1z2Values->avg() ?? 0;

                $avg_mz = $avg_m1m2 - $avg_z1z2;

                $histerisisValue = ($avg_mz < 0.5 * $pembacaanTerkecil)
                    ? $pembacaanTerkecil
                    : $avg_mz;

                SmryHisterisisModel::create([
                    'kalibrasi_id'       => $kalibrasi->id,
                    'pembacaan_terkecil' => $pembacaanTerkecil,
                    'setengah_kapasitas' => $setengahKapasitas,
                    'avg_m1m2'           => $avg_m1m2,
                    'avg_z1z2'           => $avg_z1z2,
                    'avg_mz'             => $avg_mz,
                    'histerisis'         => $histerisisValue,
                ]);
            }

            // Data Keseragaman Skala
            foreach ($validated['keseragaman_skala'] as $row) {
                $massa_pengkalibrasi = $row['massa_pengkalibrasi'] ?? null;
                $massa = $row['massa'] ?? null;
                $bebanTimbanganList = $row['beban_timbangan'] ?? [];
                $pembacaanSkalaList = $row['pembacaan_skala'] ?? [];

                // pastikan jumlah beban_timbangan == pembacaan_skala
                foreach ($bebanTimbanganList as $i => $bebanTimbangan) {
                    KeseragamanSkalaModel::create([
                        'kalibrasi_id'     => $kalibrasi->id,
                        'massa'            => $massa_pengkalibrasi,
                        'beban'            => $massa,
                        'beban_timbangan'  => $bebanTimbangan,
                        'pembacaan_skala'  => $pembacaanSkalaList[$i] ?? null,
                    ]);
                }
            }

            // === Hitung Summary Keseragaman Skala ===
            $detailData = KeseragamanSkalaModel::where('kalibrasi_id', $kalibrasi->id)->get();

            $groupedByMassa = $detailData->groupBy('beban');

            foreach ($groupedByMassa as $massa => $items) {
                $zValue = $items->firstWhere('beban_timbangan', 'Z')['pembacaan_skala'] ?? null;
                $m1Value = $items->firstWhere('beban_timbangan', 'M1')['pembacaan_skala'] ?? null;
                $m2Value = $items->firstWhere('beban_timbangan', 'M2')['pembacaan_skala'] ?? null;

                $avg_z = $zValue;
                $avg_m = ($m1Value !== null && $m2Value !== null)
                    ? ($m1Value + $m2Value) / 2
                    : null;

                // Selisih dan koreksi
                $selisih_zm = ($avg_m !== null && $avg_z !== null) ? $avg_m - $avg_z : null;
                $koreksi_skala = ($selisih_zm !== null) ? (0 - $selisih_zm) : null;
                $absolut_koreksi = $koreksi_skala !== null ? abs($koreksi_skala) : null;

                $master = MasterTimbanganModel::where('beban', $items->first()->beban ?? null)->first();
                $standar_massa = $master ? $master->standar_massa : 0;

                // Simpan summary
                SmryKeseragamanSkalaModel::create([
                    'kalibrasi_id'   => $kalibrasi->id,
                    'beban'          => $items->first()->beban ?? null,
                    'avg_z'          => $avg_z,
                    'avg_m'          => $avg_m,
                    'selisih_zm'     => $selisih_zm,
                    'standar_massa'  => $standar_massa,
                    'koreksi_skala'  => $koreksi_skala,
                    'absolut_koreksi' => $absolut_koreksi,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data kalibrasi timbangan berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getData()
    {
        try {
            $data = KalibrasiModel::with([
                'alat',
                'pembacaan',
                'pembacaanSummary',
                'keseragamanSkala',
                'keseragamanSummary',
                'pinggan',
                'pingganSummary',
                'tare',
                'tareSummary',
                'histerisis',
                'histerisisSummary',
            ])
                ->where('jenis_kalibrasi', 'timbangan')
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

        // $kalibrasi->jangkaSorong()->delete();
        // $kalibrasi->jangkaSorongSummary()->delete();
        // $kalibrasi->jangkaSorongFinalSummary()->delete();

        // Hapus kalibrasi utama
        $kalibrasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kalibrasi berhasil dihapus!'
        ]);
    }
}
