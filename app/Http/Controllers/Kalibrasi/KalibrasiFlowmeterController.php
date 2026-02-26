<?php

namespace App\Http\Controllers\Kalibrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kalibrasi\FlowmeterRequest;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\Flowmeter\CalFlowmeterDetailModel;
use App\Models\Kalibrasi\Flowmeter\CalFlowmeterModel;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KalibrasiFlowmeterController extends Controller
{
    public function showForm()
    {
        $alat = AlatKalibrasiModel::select('id', 'kode_alat', 'nama_alat')
            ->where('jenis_kalibrasi', 'flowmeter')
            ->get();

        return view('kalibrasi.flowmeter.form', compact('alat'));
    }

    public function viewData()
    {
        return view('kalibrasi.flowmeter.data');
    }

    public function store(FlowmeterRequest $request)
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
                'jenis_kalibrasi' => 'flowmeter',
                'catatan' => $validated['catatan'],
            ]);

            KalibrasiSertifikatModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            foreach ($validated['data'] as $titik) {
                $standarArray = collect($titik['penunjuk_standar'])->map(fn($v) => (float)$v);
                $alatArray    = collect($titik['penunjuk_alat'])->map(fn($v) => (float)$v);
                $keteranganArray = collect($titik['keterangan'] ?? []);

                $avgStandar      = $standarArray->avg(); // nilai master
                $avgPembacaan    = $alatArray->avg();    // avg pembacaan
                $koreksi         = $avgStandar - $avgPembacaan;

                $mean = $avgPembacaan;
                $n    = $alatArray->count();

                $variance = $alatArray->sum(function ($value) use ($mean) {
                    return pow($value - $mean, 2);
                }) / ($n - 1);

                $stdDev = sqrt($variance);

                $stddevFinal     = sqrt($stdDev);
                $uGab            = $stddevFinal / sqrt(50);
                $ketidakpastian  = $uGab * 2;

                $flowMeter = CalFlowmeterModel::create([
                    'kalibrasi_id'   => $kalibrasi->id,
                    'titik_kalibrasi' => $titik['titik_kalibrasi'],
                    'nilai_master'   => $avgStandar,
                    'avg_pembacaan'  => $avgPembacaan,
                    'koreksi'        => $koreksi,
                    'std_dev'        => $stdDev,
                    'ketidakpastian' => $ketidakpastian,
                ]);

                foreach ($standarArray as $index => $standar) {

                    $alat = $alatArray[$index] ?? 0;
                    $keterangan = $keteranganArray[$index] ?? null;

                    CalFlowmeterDetailModel::create([
                        'flowmeter_id'        => $flowMeter->id,
                        'penunjuk_standar'  => $standar,
                        'penunjuk_alat'     => $alat,
                        'keterangan'     => $keterangan,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data kalibrasi flow meter berhasil disimpan.',
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
                'flowmeter.details',
                'alat'
            ])
                ->where('jenis_kalibrasi', 'flowmeter')
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
