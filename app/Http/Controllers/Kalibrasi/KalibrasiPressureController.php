<?php

namespace App\Http\Controllers\Kalibrasi;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\AlatKalibrasiModel;
use App\Models\Kalibrasi\KalibrasiSertifikatModel;
use App\Models\Kalibrasi\Pressure\KalibrasiPressureModel;
use App\Models\Kalibrasi\Pressure\KalibrasiPressureGabunganModel;

class KalibrasiPressureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alat = AlatKalibrasiModel::select('id', 'kode_alat', 'nama_alat')
            ->where('jenis_kalibrasi', 'Pressure')
            ->get();

        return view('kalibrasi.pressure.index', compact('alat'));
    }

    public function viewData()
    {
        return view('kalibrasi.pressure.data');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'alat_id' => 'required|exists:alat_kalibrasi,id',
            'lokasi_kalibrasi' => 'required|string|max:255',
            'suhu_ruangan' => 'required|numeric|max:50',
            'suhu_ruangan_tol' => 'required|numeric|max:50',
            'kelembaban' => 'required|numeric|max:50',
            'kelembaban_tol' => 'required|numeric|max:50',
            'tgl_kalibrasi' => 'required|date',
            'metode_kalibrasi' => 'required|string|max:255',

            'pressure' => 'required|array',
            'pressure.*.titik_kalibrasi' => 'required|numeric',
            'pressure.*.tekanan' => 'required|in:naik,turun',
            'pressure.*.penunjuk_standar' => 'required|numeric',
            'pressure.*.penunjuk_alat' => 'required|numeric',
            'pressure.*.koreksi_standar' => 'nullable|numeric',
        ]);

        // dd($validated['pressure']);

        // Simpan data utama kalibrasi
        $kalibrasi = KalibrasiModel::create([
            'alat_id' => $validated['alat_id'],
            'user_id' => Auth::id() ?? 1,
            'lokasi_kalibrasi' => $validated['lokasi_kalibrasi'],
            'suhu_ruangan' => $validated['suhu_ruangan'] . '±' . $validated['suhu_ruangan_tol'],
            'kelembaban' => $validated['kelembaban'] . '±' . $validated['kelembaban_tol'],
            'tgl_kalibrasi' => $validated['tgl_kalibrasi'],
            'tgl_kalibrasi_ulang' => Carbon::parse($validated['tgl_kalibrasi'])->addYearNoOverflow(),
            'metode_kalibrasi' => $validated['metode_kalibrasi'],
            'jenis_kalibrasi' => 'pressure',
        ]);

        KalibrasiSertifikatModel::create([
            'kalibrasi_id' => $kalibrasi->id,
            'user_id' => Auth::id(),
            'status' => 'draft'
        ]);

        // Pisahkan data titik
        $perTitik = [];

        // simpan ke pressure model
        foreach ($validated['pressure'] as $p) {
            $tekananStandar = $p['penunjuk_standar'] + ($p['koreksi_standar'] ?? 0);
            $koreksiAlat = $tekananStandar - $p['penunjuk_alat'];

            // Simpan detail
            KalibrasiPressureModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'titik_kalibrasi' => $p['titik_kalibrasi'],
                'tekanan' => $p['tekanan'],
                'penunjuk_standar' => $p['penunjuk_standar'],
                'penunjuk_alat' => $p['penunjuk_alat'],
                'koreksi_standar' => $p['koreksi_standar'] ?? null,
                'tekanan_standar' => $tekananStandar,
                'koreksi_alat' => $koreksiAlat,
            ]);

            $perTitik[$p['titik_kalibrasi']][$p['tekanan']][] = [
                'penunjuk_alat' => $p['penunjuk_alat'],
                'tekanan_standar' => $tekananStandar,
                'koreksi_alat' => $koreksiAlat,
            ];
        }

        $avg = fn($arr, $field) => count($arr) ? array_sum(array_column($arr, $field)) / count($arr) : null;
        $std = fn($arr, $field) => count($arr) > 1
            ? sqrt(array_sum(array_map(fn($x) => pow($x[$field] - $avg($arr, $field), 2), $arr)) / (count($arr) - 1))
            : 0;

        // Loop tiap titik kalibrasi untk simpak ke pressure u gabungan
        $staticU = [
            'naik' => [
                0 => 0.059872897,
                1 => 0.059872897,
                2 => 0.059872897,
                3 => 0.059872897,
                4 => 0.059872897,
                5 => 0.059872897,
                6 => 0.059872897,
                7 => 0.059872897,
                8 => 0.079157482,
            ],
            'turun' => [
                0 => 0.059872897,
                1 => 0.059872897,
                2 => 0.059872897,
                3 => 0.059872897,
                4 => 0.059872897,
                5 => 0.059872897,
                6 => 0.059872897,
                7 => 0.059872897,
                8 => 0.093090854,
            ],
        ];

        foreach ($perTitik as $titik => $arah) {
            $naik = $arah['naik'] ?? [];
            $turun = $arah['turun'] ?? [];

            $u_naik  = $staticU['naik'][$titik]  ?? 0.00;
            $u_turun = $staticU['turun'][$titik] ?? 0.00;

            $ketidakpastianNaik = $staticU['naik'][$titik]  ?? 0.00;
            $ketidakpastianTurun = $staticU['turun'][$titik] ?? 0.00;

            $u_naik_kuadrat  = pow($u_naik, 2);
            $u_turun_kuadrat = pow($u_turun, 2);
            $u_gabungan = sqrt($u_naik_kuadrat + $u_turun_kuadrat);

            KalibrasiPressureGabunganModel::create([
                'kalibrasi_id' => $kalibrasi->id,
                'titik_kalibrasi' => $titik,

                'avg_penunjuk_alat_naik' => $avg($naik, 'penunjuk_alat'),
                'avg_penunjuk_alat_turun' => $avg($turun, 'penunjuk_alat'),

                'avg_tekanan_standar_naik' => $avg($naik, 'tekanan_standar'),
                'avg_tekanan_standar_turun' => $avg($turun, 'tekanan_standar'),

                'avg_kor_alat_naik' => $avg($naik, 'koreksi_alat'),
                'avg_kor_alat_turun' => $avg($turun, 'koreksi_alat'),

                'std_deviasi_naik' => $std($naik, 'tekanan_standar'),
                'std_deviasi_turun' => $std($turun, 'tekanan_standar'),

                'ketidak_pastian_naik' => $ketidakpastianNaik,
                'ketidak_pastian_turun' => $ketidakpastianTurun,

                'u_naik' => $u_naik,
                'u_turun' => $u_turun,
                'u_naik_kuadrat' => $u_naik_kuadrat,
                'u_turun_kuadrat' => $u_turun_kuadrat,
                'u_gabungan' => $u_gabungan,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pressure calibration data successfully saved.',
            'data' => $kalibrasi->load('pressure')
        ], 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $alat = AlatKalibrasiModel::find($id);

        if (!$alat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Alat tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $alat
        ]);
    }

    public function getData()
    {
        try {
            // ambil data kalibrasi + relasi pressure & gabungan
            $data = KalibrasiModel::with([
                'pressure' => function ($q) {
                    $q->orderBy('titik_kalibrasi');
                },
                'pressureGabungan',
                'alat:id,kode_alat,nama_alat'
            ])
                ->where('jenis_kalibrasi', 'pressure')
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kalibrasi = KalibrasiModel::findOrFail($id);

        // Hapus relasi dulu
        $kalibrasi->pressure()->delete();
        $kalibrasi->pressureGabungan()->delete();

        // Hapus kalibrasi utama
        $kalibrasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Calibration data successfully deleted'
        ]);
    }
}
