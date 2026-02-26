<?php

namespace App\Http\Controllers\Kalibrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kalibrasi\TimbanganRequest;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use App\Models\Kalibrasi\Timbangan\HisterisisModel;
use App\Models\Kalibrasi\Timbangan\HisterisisSummariesModel;
use App\Models\Kalibrasi\Timbangan\KemampuanUlangModel;
use App\Models\Kalibrasi\Timbangan\KemampuanUlangSummariesModel;
use App\Models\Kalibrasi\Timbangan\KeseragamanSkalaModel;
use App\Models\Kalibrasi\Timbangan\KeseragamanSkalaSummariesModel;
use App\Models\Kalibrasi\Timbangan\KetidakpastianSummariesModel;
use App\Models\Kalibrasi\Timbangan\PingganDetailModel;
use App\Models\Kalibrasi\Timbangan\PingganModel;
use App\Models\Kalibrasi\Timbangan\PingganSummariesModel;
use App\Models\Kalibrasi\Timbangan\TareModel;
use App\Models\Kalibrasi\Timbangan\TareSummariesModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function store(TimbanganRequest $request)
    {

        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $kalibrasi = KalibrasiModel::create([
                'alat_id' => $request->alat_id,
                'user_id' => Auth::id(),
                'lokasi_kalibrasi' => $request->lokasi_kalibrasi,
                'suhu_ruangan' => $request->suhu_ruangan,
                'kelembaban' => $request->kelembaban,
                'tgl_kalibrasi' => $request->tgl_kalibrasi,
                'tgl_kalibrasi_ulang' => Carbon::parse($request->tgl_kalibrasi)->addYear(),
                'jenis_kalibrasi' => 'timbangan',
            ]);

            KalibrasiSertifikatModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            // Kemampuan Ulang
            $dataPembacaan = $request->data ?? [];

            $titikMassaMap = [
                'mendekati_nol'      => $request->titik_massa_mendekati_nol,
                'setengah_kapasitas' => $request->titik_massa_setengah_kapasitas,
                'full_kapasitas'     => $request->titik_massa_full_kapasitas,
            ];

            foreach ($dataPembacaan as $jenis => $ulanganList) {

                // $jenis sudah mendekati_nol, setengah_kapasitas, dll
                $selisihList = [];

                foreach ($ulanganList as $ulanganKe => $nilai) {

                    $z = $nilai['z'] ?? null;
                    $m = $nilai['m'] ?? null;

                    // Skip kalau kosong dua-duanya
                    if (is_null($z) && is_null($m)) {
                        continue;
                    }

                    $selisih = (isset($z, $m)) ? $m - $z : null;

                    KemampuanUlangModel::create([
                        'kalibrasi_id' => $kalibrasi->id,
                        'jenis'        => $jenis,
                        'ulangan'      => $ulanganKe,
                        'massa'        => $titikMassaMap[$jenis] ?? null,
                        'nilai_z'      => $z,
                        'nilai_m'      => $m,
                        'selisih'      => $selisih,
                    ]);

                    if (!is_null($selisih)) {
                        $selisihList[] = $selisih;
                    }
                }

                // =========================
                // Hitung Standard Deviasi
                // =========================

                if (count($selisihList) > 1) {

                    $mean = array_sum($selisihList) / count($selisihList);

                    $variance = array_sum(
                        array_map(fn($v) => pow($v - $mean, 2), $selisihList)
                    ) / (count($selisihList) - 1);

                    $stdDev = sqrt($variance);

                    $maksPerbedaanAkhir = null;

                    if (count($selisihList) > 1) {

                        $perbedaanList = [];

                        for ($i = 1; $i < count($selisihList); $i++) {
                            $perbedaan = abs($selisihList[$i] - $selisihList[$i - 1]);
                            $perbedaanList[] = $perbedaan;
                        }

                        $maksPerbedaanAkhir = max($perbedaanList);
                    }
                    KemampuanUlangSummariesModel::create([
                        'kalibrasi_id' => $kalibrasi->id,
                        'massa'        => $titikMassaMap[$jenis] ?? null,
                        'jenis'        => $jenis,
                        'std_dev'      => $stdDev,
                        'maks_perbedaan_akhir' => $maksPerbedaanAkhir,
                    ]);
                }
            }

            // Keseragaman Skala
            $keseragaman = $validated['keseragaman'] ?? [];

            $grouped = [];

            foreach ($keseragaman as $key => $row) {

                $beban      = $row['beban'] ?? null;
                $pembacaan  = $row['pembacaan'] ?? null;

                // Skip kalau kosong dua-duanya
                if (is_null($beban) && is_null($pembacaan)) {
                    continue;
                }

                $label = strtoupper($key); // contoh: 1M_1, 0_3

                $massaKe = 0;
                $jenis   = null;

                if (preg_match('/(\d+)M_(\d)/', $label, $match)) {

                    $massaKe = (int) $match[1];
                    $jenis   = $match[2] == '1' ? 'M1' : 'M2';
                } elseif (preg_match('/0_(\d+)/', $label, $match)) {

                    $massaKe = (int) $match[1];
                    $jenis   = 'Z';
                }

                // Simpan data mentah
                KeseragamanSkalaModel::create([
                    'kalibrasi_id' => $kalibrasi->id,
                    'massa_ke'     => $massaKe,
                    'jenis'        => $jenis,
                    'beban'        => $beban,
                    'pembacaan'    => $pembacaan,
                ]);

                // Kelompokkan untuk perhitungan
                $grouped[$massaKe][$jenis] = [
                    'pembacaan' => $pembacaan,
                    'beban'     => $beban,
                ];
            }

            foreach ($grouped as $massaKe => $nilai) {

                $z  = $nilai['Z']['pembacaan']  ?? null;
                $m1 = $nilai['M1']['pembacaan'] ?? null;
                $m2 = $nilai['M2']['pembacaan'] ?? null;

                // Ambil beban dari salah satu (biasanya sama semua dalam 1 massa_ke)
                $beban = $nilai['Z']['beban']
                    ?? $nilai['M1']['beban']
                    ?? $nilai['M2']['beban']
                    ?? null;

                $avg_z = $z;

                $avg_m = (isset($m1, $m2))
                    ? ($m1 + $m2) / 2
                    : null;

                $selisih_zm = (isset($avg_m, $avg_z))
                    ? $avg_m - $avg_z
                    : null;

                $koreksi = isset($selisih_zm)
                    ? (0 - $selisih_zm)
                    : null;

                $absolut = isset($koreksi)
                    ? abs($koreksi)
                    : null;

                KeseragamanSkalaSummariesModel::create([
                    'kalibrasi_id'    => $kalibrasi->id,
                    'massa_ke'        => $massaKe,
                    'beban'           => $titikMassaMap["setengah_kapasitas"] ?? null, // Bisa disesuaikan kalau nanti ada mapping massa tertentu
                    'avg_z'           => $avg_z,
                    'avg_m'           => $avg_m,
                    'selisih_zm'      => $selisih_zm,
                    'koreksi_skala'   => $koreksi,
                    'absolut_koreksi' => $absolut,
                ]);
            }

            // Data Pinggan
            $pingganData = $validated['pinggan'] ?? null;

            if (!empty($pingganData)) {

                $pinggan = PingganModel::create([
                    'kalibrasi_id' => $kalibrasi->id,
                    'diameter'     => $pingganData['diameter'] ?? null,
                    'massa'        => $pingganData['massa'] ?? null,
                ]);

                $posisiList = ['tengah', 'depan', 'belakang', 'kiri', 'kanan'];

                for ($p = 1; $p <= 3; $p++) {

                    $key = "percobaan_$p";

                    if (empty($pingganData[$key])) {
                        continue;
                    }

                    $percobaan = $pingganData[$key];

                    $nilaiPosisi = [];
                    $adaNilai = false;

                    foreach ($posisiList as $posisi) {

                        $nilai = $percobaan[$posisi] ?? null;

                        if (!is_null($nilai)) {
                            $adaNilai = true;
                        }

                        $nilaiPosisi[$posisi] = $nilai;

                        // Simpan detail hanya jika ada nilai
                        if (!is_null($nilai)) {
                            PingganDetailModel::create([
                                'pinggan_id' => $pinggan->id,
                                'percobaan'  => $p,
                                'posisi'     => $posisi,
                                'nilai'      => $nilai,
                            ]);
                        }
                    }

                    // Kalau semua kosong, skip summary
                    if (!$adaNilai) {
                        continue;
                    }

                    $tengah = $nilaiPosisi['tengah'] ?? null;

                    $summary = [
                        'tengah'   => 0,
                        'depan'    => isset($nilaiPosisi['depan'], $tengah) ? $nilaiPosisi['depan'] - $tengah : null,
                        'belakang' => isset($nilaiPosisi['belakang'], $tengah) ? $nilaiPosisi['belakang'] - $tengah : null,
                        'kiri'     => isset($nilaiPosisi['kiri'], $tengah) ? $nilaiPosisi['kiri'] - $tengah : null,
                        'kanan'    => isset($nilaiPosisi['kanan'], $tengah) ? $nilaiPosisi['kanan'] - $tengah : null,
                    ];

                    $values = array_filter($nilaiPosisi, fn($v) => $v !== null);

                    $minimum    = count($values) ? min($values) : null;
                    $maximum    = count($values) ? max($values) : null;
                    $selisihMax = (isset($minimum, $maximum))
                        ? abs($maximum - $minimum)
                        : null;

                    PingganSummariesModel::create([
                        'kalibrasi_id'     => $kalibrasi->id,
                        'percobaan'        => $p,
                        'summary_tengah'   => $summary['tengah'],
                        'summary_depan'    => $summary['depan'],
                        'summary_belakang' => $summary['belakang'],
                        'summary_kiri'     => $summary['kiri'],
                        'summary_kanan'    => $summary['kanan'],
                        'minimum'          => $minimum,
                        'maximum'          => $maximum,
                        'selisih_maks'     => $selisihMax,
                    ]);
                }
            }

            // Data Tare
            $tareData = $validated['tare'] ?? null;

            if (!empty($tareData)) {

                $massa = $tareData['massa'] ?? null;

                $kondisiList = ['tanpa', 'dengan'];
                $labelList   = ['zero_1', 'm_1', 'm_2', 'zero_2'];

                $hasilSelisih = [];

                foreach ($kondisiList as $kondisi) {

                    if (empty($tareData[$kondisi])) {
                        continue;
                    }

                    $data = $tareData[$kondisi];

                    $zero1 = $data['zero_1'] ?? null;
                    $m1    = $data['m_1'] ?? null;
                    $m2    = $data['m_2'] ?? null;
                    $zero2 = $data['zero_2'] ?? null;

                    $adaNilai = false;

                    // ===============================
                    // 1️⃣ SIMPAN DETAIL (Hanya jika ada nilai)
                    // ===============================
                    foreach ($labelList as $label) {

                        $nilai = $data[$label] ?? null;

                        if (!is_null($nilai)) {
                            $adaNilai = true;

                            TareModel::create([
                                'kalibrasi_id' => $kalibrasi->id,
                                'kondisi'      => $kondisi,
                                'label'        => $label,
                                'nilai'        => $nilai,
                            ]);
                        }
                    }

                    if (!$adaNilai) {
                        continue; // skip kalau semua kosong
                    }

                    // ===============================
                    // 2️⃣ HITUNG RATA-RATA
                    // ===============================

                    $rataZ = (isset($zero1, $zero2))
                        ? ($zero1 + $zero2) / 2
                        : null;

                    $rataM = (isset($m1, $m2))
                        ? ($m1 + $m2) / 2
                        : null;

                    $selisih = (isset($rataM, $rataZ))
                        ? $rataM - $rataZ
                        : null;

                    $hasilSelisih[$kondisi] = $selisih;

                    // ===============================
                    // 3️⃣ SIMPAN SUMMARY PER KONDISI
                    // ===============================

                    TareSummariesModel::create([
                        'kalibrasi_id' => $kalibrasi->id,
                        'kondisi'      => $kondisi,
                        'massa'        => $massa,
                        'rata_zero'    => $rataZ,
                        'rata_m'       => $rataM,
                        'selisih_mz'   => $selisih,
                        'pengaruh'     => null,
                    ]);
                }

                // ===============================
                // 4️⃣ HITUNG PENGARUH (SETELAH LOOP)
                // ===============================

                if (
                    isset($hasilSelisih['tanpa']) &&
                    isset($hasilSelisih['dengan']) &&
                    !is_null($hasilSelisih['tanpa']) &&
                    !is_null($hasilSelisih['dengan'])
                ) {

                    $pengaruh = abs(
                        $hasilSelisih['tanpa'] - $hasilSelisih['dengan']
                    );

                    // Update masing-masing kondisi saja
                    TareSummariesModel::where('kalibrasi_id', $kalibrasi->id)
                        ->whereIn('kondisi', ['tanpa', 'dengan'])
                        ->update(['pengaruh' => $pengaruh]);
                }
            }

            // E. HISTERISIS
            $histerisisData = $validated['histerisis'] ?? null;

            if ($histerisisData) {

                $pembacaanTerkecil = $histerisisData['pembacaan_terkecil'] ?? null;
                $setengahKapasitas = $histerisisData['m_setengah'] ?? null;

                $labelList = ['z1', 'm1', 'm_plus', 'm2', 'z2'];

                // Simpan nilai per pengulangan supaya aman
                $grouped = [];

                foreach ($labelList as $label) {

                    if (!isset($histerisisData[$label])) {
                        continue;
                    }

                    foreach ($histerisisData[$label] as $pengulangan => $nilai) {

                        // Simpan detail
                        HisterisisModel::create([
                            'kalibrasi_id' => $kalibrasi->id,
                            'label'        => $label,
                            'pengulangan'  => $pengulangan,
                            'nilai'        => $nilai,
                        ]);

                        // Kelompokkan berdasarkan pengulangan
                        $grouped[$pengulangan][$label] = $nilai;
                    }
                }

                $selisihM = [];
                $selisihZ = [];

                foreach ($grouped as $pengulangan => $data) {

                    // Hitung selisih M (m1 - m2)
                    if (isset($data['m1'], $data['m2'])) {
                        $selisihM[] = $data['m1'] - $data['m2'];
                    }

                    // Hitung selisih Z (z1 - z2)
                    if (isset($data['z1'], $data['z2'])) {
                        $selisihZ[] = $data['z1'] - $data['z2'];
                    }
                }

                $avg_m1m2 = count($selisihM)
                    ? array_sum($selisihM) / count($selisihM)
                    : null;

                $avg_z1z2 = count($selisihZ)
                    ? array_sum($selisihZ) / count($selisihZ)
                    : null;

                $nilai_mz = null;
                if (!is_null($avg_m1m2) && !is_null($avg_z1z2)) {
                    $nilai_mz = $avg_m1m2 - $avg_z1z2;
                }

                $histerisisValue = null;

                if (!is_null($nilai_mz)) {

                    $histerisisValue = abs($nilai_mz);

                    // Minimal histerisis = pembacaan terkecil
                    if (!is_null($pembacaanTerkecil) && $histerisisValue < $pembacaanTerkecil) {
                        $histerisisValue = $pembacaanTerkecil;
                    }
                }

                HisterisisSummariesModel::create([
                    'kalibrasi_id'       => $kalibrasi->id,
                    'pembacaan_terkecil' => $pembacaanTerkecil,
                    'setengah_kapasitas' => $setengahKapasitas,
                    'avg_m1m2'           => $avg_m1m2,
                    'avg_z1z2'           => $avg_z1z2,
                    'nilai_mz'           => $nilai_mz,
                    'histerisis'         => $histerisisValue,
                ]);
            }


            // HITUNG KETIDAKPASTIAN TIMBANGAN
            $kalibrasi->load('alat');

            $kapasitasRaw = trim($kalibrasi->alat->kapasitas ?? '');
            // dd($kalibrasi->alat->kapasitas);

            if ($kapasitasRaw === '') {
                $kapasitasAlatGram = 0;
            } else {
                $parts = preg_split('/\s+/', $kapasitasRaw);

                $angka = isset($parts[0])
                    ? (float) str_replace(',', '.', $parts[0])
                    : 0;

                $satuan = strtolower($parts[1] ?? 'g');

                switch ($satuan) {
                    case 'ton':
                        $kapasitasAlatGram = $angka * 1000000;
                        break;

                    case 'kg':
                        $kapasitasAlatGram = $angka * 1000;
                        break;

                    case 'g':
                    default:
                        $kapasitasAlatGram = $angka;
                        break;
                }
            }

            $pembacaanTerkecil = $request->pembacaan_terkecil ?? 0;

            $timbanganStandar = 0.9011271;
            $skalaTerkecil = $pembacaanTerkecil / 2;

            $stdList = KemampuanUlangSummariesModel::where('kalibrasi_id', $kalibrasi->id)
                ->pluck('std_dev')
                ->filter()
                ->toArray();

            $maxKemampuanUlang = count($stdList)
                ? max($stdList)
                : 0;

            $drift = 0.00001;
            $bouyancy = 0.000001 * $kapasitasAlatGram;

            $ustdTimbanganStandar = $timbanganStandar / 2;
            $ustdSkalaKecil       = $skalaTerkecil / sqrt(3);
            $ustdKemampuanUlang   = $maxKemampuanUlang / sqrt(10);
            $ustdDrift            = $drift / sqrt(3);
            $ustdBouyancy         = $bouyancy / sqrt(3);

            $u1 = pow($ustdTimbanganStandar, 2);
            $u2 = pow($ustdSkalaKecil, 2);
            $u3 = pow($ustdKemampuanUlang, 2);
            $u4 = pow($ustdDrift, 2);
            $u5 = pow($ustdBouyancy, 2);

            $varGabungan = $u1 + $u2 + $u3 + $u4 + $u5;

            $ketidakpastianGabungan = sqrt($varGabungan);
            $ketidakpastianPerluas  = 2 * $ketidakpastianGabungan;

            KetidakpastianSummariesModel::create([
                'kalibrasi_id'            => $kalibrasi->id,
                'kapasitas_alat'          => $kapasitasAlatGram,
                'pembacaan_terkecil'      => $pembacaanTerkecil,
                'timbangan_standar'       => $timbanganStandar,
                'skala_terkecil'          => $skalaTerkecil,
                'max_kemampuan_ulang'     => $maxKemampuanUlang,
                'drift'                   => $drift,
                'bouyancy'                => $bouyancy,
                'ketidakpastian_gabungan' => $ketidakpastianGabungan,
                'ketidakpastian_perluas'  => $ketidakpastianPerluas,
            ]);

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
                'kemampuanUlang',
                'keseragamanSkala',
                'pinggan.details',
                'tare',
                'histerisis',
                'kemampuanUlangSummary',
                'keseragamanSkalaSummary',
                'pingganSummary',
                'tareSummary',
                'histerisisSummary',
                'ketidakpastianSummary',
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

        // Hapus kalibrasi utama
        $kalibrasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kalibrasi berhasil dihapus!'
        ]);
    }
}
