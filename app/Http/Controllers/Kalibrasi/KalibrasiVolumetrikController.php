<?php

namespace App\Http\Controllers\Kalibrasi;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Kalibrasi\VolumetrikRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use App\Models\Kalibrasi\Volumetrik\KalibrasiVolumetrikDetailModel;
use App\Models\Kalibrasi\Volumetrik\KalibrasiVolumetrikModel;

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

    public function store(VolumetrikRequest $request)
    {
        DB::beginTransaction();

        try {

            $validated = $request->validated();

            $kalibrasi = KalibrasiModel::create([
                'alat_id' => $validated['alat_id'],
                'user_id' => Auth::id() ?? 1,
                'lokasi_kalibrasi' => $validated['lokasi_kalibrasi'],
                'suhu_ruangan' => $validated['suhu_ruangan'] . '°C ± 1°C' ?? '-',
                'kelembaban' => $validated['kelembaban'] . '% ± 3%' ?? '-',
                'tgl_kalibrasi' => $validated['tgl_kalibrasi'],
                'tgl_kalibrasi_ulang' => Carbon::parse($validated['tgl_kalibrasi'])->addYearNoOverflow(),
                'jenis_kalibrasi' => 'volumetrik',
            ]);

            KalibrasiSertifikatModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            foreach ($validated['data'] as $titik) {

                $standarArray = $titik['penunjuk_standar'];
                $alatArray    = $titik['penunjuk_alat'];

                $koreksiValues = [];

                // hitung koreksi per repeat
                foreach ($standarArray as $index => $standar) {

                    $alat = $alatArray[$index] ?? 0;
                    $koreksi = $standar - $alat;

                    $koreksiValues[] = $koreksi;
                }

                $avgStandar = collect($standarArray)->avg();
                $avgKoreksi = collect($koreksiValues)->avg();
                $stdevStandar = $this->calculateStdev($standarArray);

                $akar10 = sqrt(count($standarArray));
                $uTotal = $akar10 > 0 ? $stdevStandar / $akar10 : 0;

                $volumetrik = KalibrasiVolumetrikModel::create([
                    'kalibrasi_id' => $kalibrasi->id,
                    'titik_kalibrasi' => $titik['titik_kalibrasi'],
                    'avg_penunjuk_standar' => $avgStandar,
                    'avg_koreksi' => $avgKoreksi,
                    'stdev_penunjuk_standar' => $stdevStandar,
                    'akar_10' => $akar10,
                    'u_timbangan' => null,
                    'u_total' => $uTotal,
                ]);

                foreach ($standarArray as $index => $standar) {

                    $alat = $alatArray[$index] ?? 0;
                    $koreksi = $standar - $alat;

                    KalibrasiVolumetrikDetailModel::create([
                        'volumetrik_id' => $volumetrik->id,
                        'penunjuk_standar' => $standar,
                        'penunjuk_alat' => $alat,
                        'koreksi' => $koreksi,
                    ]);
                }
            }

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
                'volumetrik.details',
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

        // Hapus kalibrasi utama
        $kalibrasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kalibrasi berhasil dihapus!'
        ]);
    }
}
