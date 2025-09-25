<?php

namespace App\Http\Controllers\Kalibrasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Kalibrasi\KalibrasiModel;
use App\Models\Kalibrasi\AlatKalibrasiModel;
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
            'suhu_ruangan' => 'required|string|max:50',
            'kelembaban' => 'required|string|max:50',
            'tgl_kalibrasi' => 'required|date',
            'tgl_kalibrasi_ulang' => 'required|date',
            'metode_kalibrasi' => 'required|string|max:255',

            'pressure' => 'required|array',
            'pressure.*.titik_kalibrasi' => 'required|numeric',
            'pressure.*.tekanan' => 'required|in:naik,turun',
            'pressure.*.penunjuk_standar' => 'required|numeric',
            'pressure.*.penunjuk_alat' => 'required|numeric',
            'pressure.*.koreksi_standar' => 'nullable|numeric',
        ]);

        // Simpan data utama kalibrasi
        $kalibrasi = KalibrasiModel::create([
            'alat_id' => $validated['alat_id'],
            'user_id' => Auth::id() ?? 1,
            'lokasi_kalibrasi' => $validated['lokasi_kalibrasi'],
            'suhu_ruangan' => $validated['suhu_ruangan'],
            'kelembaban' => $validated['kelembaban'],
            'tgl_kalibrasi' => $validated['tgl_kalibrasi'],
            'tgl_kalibrasi_ulang' => $validated['tgl_kalibrasi_ulang'],
            'metode_kalibrasi' => $validated['metode_kalibrasi'],
            'jenis_kalibrasi' => 'Pressure',
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
            ? sqrt(array_sum(array_map(fn($x) => pow($x[$field] - ($avg($arr, $field)), 2), $arr)) / (count($arr) - 1))
            : null;

        // Loop tiap titik kalibrasi untk simpak ke pressure u gabungan
        foreach ($perTitik as $titik => $arah) {
            $naik = $arah['naik'] ?? [];
            $turun = $arah['turun'] ?? [];

            $u_naik = $avg($naik, 'penunjuk_alat');
            $u_turun = $avg($turun, 'penunjuk_alat');

            $u_naik_kuadrat = count($naik) ? array_sum(array_map(fn($x) => pow($x['penunjuk_alat'] - $u_naik, 2), $naik)) : null;
            $u_turun_kuadrat = count($turun) ? array_sum(array_map(fn($x) => pow($x['penunjuk_alat'] - $u_turun, 2), $turun)) : null;

            $u_gabungan = ($u_naik_kuadrat !== null && $u_turun_kuadrat !== null)
                ? sqrt($u_naik_kuadrat + $u_turun_kuadrat)
                : null;

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

                'ketidak_pastian_naik' => null,
                'ketidak_pastian_turun' => null,

                'u_naik' => $u_naik,
                'u_turun' => $u_turun,
                'u_naik_kuadrat' => $u_naik_kuadrat,
                'u_turun_kuadrat' => $u_turun_kuadrat,
                'u_gabungan' => $u_gabungan,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data kalibrasi berhasil disimpan',
            'data' => $kalibrasi->load('pressureDetails')
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
        //
    }
}
