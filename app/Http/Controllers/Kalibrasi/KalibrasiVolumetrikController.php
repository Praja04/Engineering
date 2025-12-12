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
use App\Models\Kalibrasi\Volumetrik\KalibrasiVolumetrikModel;
use App\Models\Kalibrasi\Volumetrik\KalibrasiVolumetrikGabunganModel;

class KalibrasiVolumetrikController extends Controller
{
    public function showForm()
    {
        $alat = AlatKalibrasiModel::select('id', 'kode_alat', 'nama_alat')
            ->where('jenis_kalibrasi', 'volumetrik')
            ->get();

        return view('kalibrasi.volumetrik.form', compact('alat'));
    }

    public function viewData()
    {
        return view('kalibrasi.volumetrik.data');
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

            // Data pengukuran volumetrik
            'data' => 'required|array|min:1',
            'data.*.titik_kalibrasi' => 'required|numeric',
            'data.*.penunjuk_standar' => 'required|numeric',
            'data.*.penunjuk_alat' => 'required|numeric',
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
                'jenis_kalibrasi' => 'volumetrik',
            ]);

            KalibrasiSertifikatModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            $dataInput = $validated['data'];
            $standarValues = [];
            $koreksiValues = [];

            foreach ($dataInput as $item) {
                $koreksi = $item['penunjuk_standar'] - $item['penunjuk_alat'];

                KalibrasiVolumetrikModel::create([
                    'kalibrasi_id' => $kalibrasi->id,
                    'titik_kalibrasi' => $item['titik_kalibrasi'],
                    'penunjuk_standar' => $item['penunjuk_standar'],
                    'penunjuk_alat' => $item['penunjuk_alat'],
                    'koreksi' => $koreksi,
                ]);

                $standarValues[] = $item['penunjuk_standar'];
                $koreksiValues[] = $koreksi;
            }

            // Hitung nilai gabungan
            $avgStandar = collect($standarValues)->avg();
            $avgKoreksi = collect($koreksiValues)->avg();
            $stdevStandar = $this->calculateStdev($standarValues);
            $akar10 = sqrt(10);
            $uTimbangan = null;
            $uTotal = $stdevStandar / $akar10;

            // Simpan hasil gabungan ke tabel volumetrik_gabungan
            KalibrasiVolumetrikGabunganModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'avg_penunjuk_standar' => $avgStandar,
                'avg_koreksi' => $avgKoreksi,
                'stdev_penunjuk_standar' => $stdevStandar,
                'akar_10' => $akar10,
                'u_timbangan' => $uTimbangan,
                'u_total' => $uTotal,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data kalibrasi volumetrik berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function calculateStdev($values)
    {
        $n = count($values);
        if ($n <= 1) return 0;

        $mean = array_sum($values) / $n;
        $sumSq = array_sum(array_map(fn($v) => pow($v - $mean, 2), $values));

        return sqrt($sumSq / ($n - 1)); // sample stdev
    }

    public function getData()
    {
        try {
            $data = KalibrasiModel::with([
                'volumetrik' => function ($q) {
                    $q->orderBy('titik_kalibrasi');
                },
                'volumetrikGabungan',
                'alat'
            ])
                ->where('jenis_kalibrasi', 'volumetrik')
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

        $kalibrasi->volumetrik()->delete();
        $kalibrasi->volumetrikGabungan()->delete();

        // Hapus kalibrasi utama
        $kalibrasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kalibrasi berhasil dihapus!'
        ]);
    }
}
