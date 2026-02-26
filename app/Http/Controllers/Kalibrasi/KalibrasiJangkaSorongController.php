<?php

namespace App\Http\Controllers\Kalibrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kalibrasi\JangkaSorongRequest;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\JangkaSorong\CalJangkaSorongMasterModel;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongDetailModel;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongModel;
use App\Models\Kalibrasi\JangkaSorong\KalibrasiJangkaSorongSummaryModel;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KalibrasiJangkaSorongController extends Controller
{
    public function showForm()
    {
        $alat = AlatKalibrasiModel::select('id', 'kode_alat', 'nama_alat')
            ->where('jenis_kalibrasi', 'jangka_sorong')
            ->get();

        $masters = CalJangkaSorongMasterModel::select('id', 'no', 'nilai_master')->get();

        return view('kalibrasi.jangka_sorong.form', compact('alat', 'masters'));
    }

    public function viewData()
    {
        return view('kalibrasi.jangka_sorong.data');
    }

    public function store(JangkaSorongRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {

            // ================= HEADER =================
            $kalibrasi = KalibrasiModel::create([
                'alat_id' => $validated['alat_id'],
                'user_id' => Auth::id(),
                'lokasi_kalibrasi' => $validated['lokasi_kalibrasi'],
                'suhu_ruangan' => $validated['suhu_ruangan'] . '°C ± 1°C' ?? '-',
                'kelembaban' => $validated['kelembaban'] . '% ± 3%' ?? '-',
                'tgl_kalibrasi' => $validated['tgl_kalibrasi'],
                'tgl_kalibrasi_ulang' => Carbon::parse($validated['tgl_kalibrasi'])->addYearNoOverflow(),
                'jenis_kalibrasi' => 'jangka_sorong',
            ]);

            KalibrasiSertifikatModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            $kalibrasiId = $kalibrasi->id;

            $nilaiMaster      = $validated['nilai_master'];
            $nilaiPembacaan   = $validated['nilai_pembacaan'];
            $no               = $validated['no'];
            $masterIdTitik    = $validated['master_id_titik'];

            $stdDevPerTitik = [];

            // ================= LOOP PER TITIK =================
            foreach ($nilaiPembacaan as $titik => $barisPembacaan) {

                $values = array_map('floatval', $barisPembacaan);
                $n = count($values);

                if ($n === 0) continue;

                $avg = array_sum($values) / $n;

                // Sample standard deviation
                $variance = 0;
                if ($n > 1) {
                    foreach ($values as $v) {
                        $variance += pow($v - $avg, 2);
                    }
                    $variance /= ($n - 1);
                }

                $stdDev = sqrt($variance);

                $nilaiMasterTitik = floatval($nilaiMaster[$titik][0] ?? 0);
                $koreksi = $nilaiMasterTitik - $avg;

                $stdDevPerTitik[] = $stdDev;

                // ======= SIMPAN SUMMARY PER TITIK =======
                $jangkaSorong = KalibrasiJangkaSorongModel::create([
                    'kalibrasi_id'  => $kalibrasiId,
                    'master_id'     => $masterIdTitik[$titik],
                    'avg_pembacaan' => round($avg, 4),
                    'std_dev'       => round($stdDev, 4),
                    'koreksi'       => round($koreksi, 4),
                ]);

                // ======= SIMPAN 10x DETAIL =======
                foreach ($barisPembacaan as $i => $nilaiBaca) {

                    KalibrasiJangkaSorongDetailModel::create([
                        'jangka_sorong_id' => $jangkaSorong->id,
                        'no_pengulangan'   => $no[$titik][$i],
                        'nilai_master'     => $nilaiMasterTitik,
                        'nilai_pembacaan'  => $nilaiBaca,
                    ]);
                }
            }

            // ================= FINAL GLOBAL SUMMARY =================
            $jumlahTitik = count($stdDevPerTitik);
            $jumlahDataPerTitik = 10;

            if ($jumlahTitik > 0) {

                $totalVar = 0;

                foreach ($stdDevPerTitik as $s) {
                    $totalVar += 9 * pow($s, 2);
                }

                $stdDevTotal = sqrt($totalVar / (9 * $jumlahTitik));

                $totalPengukuran = $jumlahTitik * $jumlahDataPerTitik;
                $ketidakpastian  = $stdDevTotal / sqrt($totalPengukuran);
                $k2              = 2 * $ketidakpastian;

                KalibrasiJangkaSorongSummaryModel::create([
                    'kalibrasi_id'   => $kalibrasiId,
                    'std_dev_total'  => round($stdDevTotal, 4),
                    'ketidakpastian' => round($ketidakpastian, 4),
                    'k_2'            => round($k2, 4),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Data kalibrasi berhasil disimpan.'
            ]);
        } catch (\Throwable $th) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menyimpan data.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function getData()
    {
        try {
            $data = KalibrasiModel::with([
                'jangkaSorong.details',
                'jangkaSorong.master',
                'jangkaSorongSummary',
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

        // Hapus kalibrasi utama
        $kalibrasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kalibrasi berhasil dihapus!'
        ]);
    }
}
