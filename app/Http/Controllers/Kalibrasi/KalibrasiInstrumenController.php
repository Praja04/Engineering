<?php

namespace App\Http\Controllers\Kalibrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kalibrasi\InstrumenRequest;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\Instrumen\CalInstrumenDetailModel;
use App\Models\Kalibrasi\Instrumen\CalInstrumenKeypadModel;
use App\Models\Kalibrasi\Instrumen\CalInstrumenModel;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KalibrasiInstrumenController extends Controller
{
    public function showForm()
    {
        $alat = AlatKalibrasiModel::select('id', 'kode_alat', 'nama_alat')
            ->where('jenis_kalibrasi', 'instrumen')
            ->get();

        return view('kalibrasi.instrumen.form', compact('alat'));
    }

    public function viewData()
    {
        return view('kalibrasi.instrumen.data');
    }

    public function store(InstrumenRequest $request)
    {
        DB::beginTransaction();

        try {
            $kalibrasi = KalibrasiModel::create([
                'alat_id' => $request['alat_id'],
                'user_id' => Auth::id(),
                'lokasi_kalibrasi' => $request['lokasi_kalibrasi'],
                'suhu_ruangan' => $request['suhu_ruangan'] . '°C ± 1°C' ?? '-',
                'kelembaban' => $request['kelembaban'] . '% ± 3%' ?? '-',
                'tgl_kalibrasi' => $request['tgl_kalibrasi'],
                'tgl_kalibrasi_ulang' => Carbon::parse($request['tgl_kalibrasi'])->addYearNoOverflow(),
                'jenis_kalibrasi' => 'instrumen',
                'catatan' => $request['catatan'],
            ]);

            KalibrasiSertifikatModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'user_id' => Auth::id(),
                'status' => 'draft'
            ]);

            $kalibrasiId = $kalibrasi->id;

            foreach ($request->data as $titik) {

                $avg = collect($titik['pembacaan_alat'])->avg();
                $stdDev = collect($titik['pembacaan_alat'])->count() > 1
                    ? sqrt(
                        collect($titik['pembacaan_alat'])
                            ->map(fn($val) => pow($val - $avg, 2))
                            ->sum()
                            / (count($titik['pembacaan_alat']) - 1)
                    )
                    : 0;

                $nilaiMaster = collect($titik['pembacaan_standar'])->avg();
                $koreksi = $nilaiMaster - $avg;

                $instrumen = CalInstrumenModel::create([
                    'kalibrasi_id'   => $kalibrasiId,
                    'titik_kalibrasi' => $titik['titik_kalibrasi'],
                    'indikator'       => $titik['indikator'],
                    'jenis_alat_ukur' => $request->jenis_alat_ukur,
                    'jenis_standar'  => $request->jenis_standar,
                    'nilai_master'   => $nilaiMaster,
                    'avg_pembacaan'  => $avg,
                    'std_dev'        => $stdDev,
                    'koreksi'        => $koreksi,
                ]);

                foreach ($titik['alat'] as $index => $value) {

                    CalInstrumenDetailModel::create([
                        'instrumen_id'      => $instrumen->id,
                        'no_ulang'          => $index + 1,
                        'alat'              => $titik['alat'][$index] ?? null,
                        'standar'           => $titik['standar'][$index] ?? null,
                        'pembacaan_alat'    => $titik['pembacaan_alat'][$index] ?? null,
                        'pembacaan_standar' => $titik['pembacaan_standar'][$index] ?? null,
                    ]);
                }
            }

            CalInstrumenKeypadModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'tested'       => $request->tested ?? null,
                'measured'     => $request->measured ?? null,
                'criterion'    => $request->criterion ?? null,
                'passed'       => $request->passed ?? null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data kalibrasi instrumen berhasil disimpan.'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getData()
    {
        try {
            $data = KalibrasiModel::with([
                'instrumen.details',
                'keypad',
                'alat'
            ])
                ->where('jenis_kalibrasi', 'instrumen')
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
