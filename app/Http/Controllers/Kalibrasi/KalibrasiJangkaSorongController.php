<?php

namespace App\Http\Controllers\Kalibrasi;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongFinalSummaryModel;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongModel;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongSummaryModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use App\Models\Kalibrasi\Master\MasterJangkaSorongModel;

class KalibrasiJangkaSorongController extends Controller
{
    public function showForm()
    {
        $alat = AlatKalibrasiModel::select('id', 'kode_alat', 'nama_alat')
            ->where('jenis_kalibrasi', 'jangka_sorong')
            ->get();

        $masters = MasterJangkaSorongModel::select('id', 'no', 'nilai_master')->get();

        return view('kalibrasi.jangka_sorong.form', compact('alat', 'masters'));
    }

    public function viewData()
    {
        return view('kalibrasi.jangka_sorong.data');
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
            'nilai_master' => 'required|array',
            'nilai_master.*' => 'required|array',
            'nilai_master.*.*' => 'required|numeric',
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
                'jenis_kalibrasi' => 'jangka_sorong',
            ]);

            KalibrasiSertifikatModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            // === Ambil ID kalibrasi yang baru dibuat ===
            $kalibrasiId = $kalibrasi->id;

            // === Ambil semua input dari form ===
            $nilaiMaster = $request->input('nilai_master', []);
            $nilaiPembacaan = $request->input('nilai_pembacaan', []);
            $no = $request->input('no', []);
            $masterIdTitik = $request->input('master_id_titik', []);

            // === Simpan detail tiap titik ke kalibrasi_jangka_sorong ===
            foreach ($nilaiMaster as $titik => $baris) {
                foreach ($baris as $i => $nilai) {
                    KalibrasiJangkaSorongModel::create([
                        'kalibrasi_id'    => $kalibrasiId,
                        'master_id'       => $masterIdTitik[$titik] ?? null,
                        'no'              => $no[$titik][$i],
                        'nilai_master'    => $nilai,
                        'nilai_pembacaan' => $nilaiPembacaan[$titik][$i] ?? null,
                    ]);
                }
            }

            // === Hitung dan simpan summary tiap titik ===
            $stdDevPerTitik = []; // simpan semua std_dev untuk perhitungan total nanti

            foreach ($nilaiPembacaan as $titik => $baris) {
                $values = array_map('floatval', $baris);
                $n = count($values);

                if ($n === 0) continue;

                $avg = array_sum($values) / $n;

                // Hitung standar deviasi (sample)
                $variance = 0;
                if ($n > 1) {
                    foreach ($values as $v) {
                        $variance += pow($v - $avg, 2);
                    }
                    $variance /= ($n - 1);
                }
                $stdDev = sqrt($variance);

                $nilaiMasterTitik = floatval($nilaiMaster[$titik][0] ?? 0);
                $koreksi = $nilaiMasterTitik - $avg; // dibalik dari versi lama

                $stdDevPerTitik[] = $stdDev; // simpan std_dev per titik

                KalibrasiJangkaSorongSummaryModel::create([
                    'kalibrasi_id'  => $kalibrasiId,
                    'master_id'     => $masterIdTitik[$titik] ?? null,
                    'avg_pembacaan' => round($avg, 4),
                    'std_dev'       => round($stdDev, 4),
                    'koreksi'       => round($koreksi, 4),
                ]);
            }

            // === Hitung Final Summary ===
            $jumlahTitik = count($stdDevPerTitik);
            $jumlahDataPerTitik = 10; // tetap 10 pengukuran per titik (dari konfirmasi kamu)

            if ($jumlahTitik > 0) {
                // std_dev_total = sqrt( (Σ (9 * (std_dev_titik^2))) / (9 * jumlahTitik) )
                $totalVar = 0;
                foreach ($stdDevPerTitik as $s) {
                    $totalVar += 9 * pow($s, 2);
                }
                $stdDevTotal = sqrt($totalVar / (9 * $jumlahTitik));

                // ketidakpastian = std_dev_total / sqrt(total pengukuran)
                $totalPengukuran = $jumlahTitik * $jumlahDataPerTitik;
                $ketidakpastian = $stdDevTotal / sqrt($totalPengukuran);

                // k_2 = ketidakpastian * 2
                $k_2 = 2 * $ketidakpastian;

                KalibrasiJangkaSorongFinalSummaryModel::create([
                    'kalibrasi_id'   => $kalibrasiId,
                    'std_dev_total'  => round($stdDevTotal, 4),
                    'ketidakpastian' => round($ketidakpastian, 4),
                    'k_2'            => round($k_2, 4),
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Data kalibrasi berhasil disimpan.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan data.', 'error' => $th->getMessage()], 500);
        }
    }

    public function getData()
    {
        try {
            $data = KalibrasiModel::with([
                'jangkaSorong.master', // ← tambahkan relasi master di sini
                'jangkaSorongSummary.master',
                'jangkaSorongFinalSummary',
                'alat'
            ])
                ->where('jenis_kalibrasi', 'jangka_sorong')
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

        $kalibrasi->jangkaSorong()->delete();
        $kalibrasi->jangkaSorongSummary()->delete();
        $kalibrasi->jangkaSorongFinalSummary()->delete();

        // Hapus kalibrasi utama
        $kalibrasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kalibrasi berhasil dihapus!'
        ]);
    }
}
