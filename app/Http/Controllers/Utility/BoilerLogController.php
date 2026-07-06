<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\BoilerLog;
use App\Models\Utility\BoilerApproval;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class BoilerLogController extends Controller
{
    /**
     * Sinkronisasi data sensor boiler ke boiler_logs.
     *
     * Metode ini mendukung dua cara kerja:
     * 1. Pull Mode: Mengambil data dari API sensor eksternal (10.11.11.200) untuk tanggal tertentu.
     * 2. Push Mode: Menerima langsung data JSON di request body/data array.
     */
    public function syncFromSensor(Request $request)
    {
        $logs = [];
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        // Jika terdapat parameter 'data' yang berupa array di payload, gunakan langsung (Push Mode)
        if ($request->has('data') && is_array($request->input('data'))) {
            $logs = $request->input('data');
        } else {
            // Jika tidak, tarik data dari API sensor eksternal (Pull Mode)
            $apiUrl = "http://10.11.11.200/mybas/public/api/sensor/boiler/generate";

            try {
                $response = Http::timeout(15)->get($apiUrl, [
                    'tanggal' => $tanggal
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghubungi API sensor: ' . $e->getMessage()
                ], 500);
            }

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari API sensor.',
                    'error' => $response->body()
                ], 500);
            }

            $result = $response->json();

            if (empty($result['status']) || empty($result['data'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak ada data sensor ditemukan untuk tanggal {$tanggal}."
                ], 400);
            }

            $logs = $result['data'];
        }

        $insertedCount = 0;

        foreach ($logs as $item) {
            if (empty($item['waktu'])) {
                continue;
            }

            // Update atau buat log baru berdasarkan waktu unik per jam.
            // Nama properti disamakan persis dengan payload API sensor.
            BoilerLog::updateOrCreate(
                ['waktu' => $item['waktu']],
                [
                    'PVSteam'          => $item['PVSteam'] ?? null,
                    'FeedPressure'     => $item['FeedPressure'] ?? null,
                    'Press_Pasteur'     => $item['Press_Pasteur'] ?? null,
                    'LevelFeedWater'   => $item['LevelFeedWater'] ?? null,
                    'InletWaterFlow'  => $item['InletWaterFlow'] ?? null,
                    'OutletSteamFlow' => $item['OutletSteamFlow'] ?? null,
                    'SuhuFeedTank'    => $item['SuhuFeedTank'] ?? null,
                    'IDFan'            => $item['IDFan'] ?? null,
                    'LHFDFan'         => $item['LHFDFan'] ?? null,
                    'RHFDFan'         => $item['RHFDFan'] ?? null,
                    'LHStoker'         => $item['LHStoker'] ?? null,
                    'RHStoker'         => $item['RHStoker'] ?? null,
                    'LHTemp'           => $item['LHTemp'] ?? null,
                    'RHTemp'           => $item['RHTemp'] ?? null,
                    'O2'                => $item['O2'] ?? null,
                    'CO2'               => $item['CO2'] ?? null,
                    'LHGuiloutine'     => $item['LHGuiloutine'] ?? null,
                    'RHGuiloutine'     => $item['RHGuiloutine'] ?? null,
                    'WaterPump1'       => $item['WaterPump1'] ?? null,
                    'WaterPump2'       => $item['WaterPump2'] ?? null,
                    'Batubara_FK'      => $item['Batubara_FK'] ?? null,
                    'Steam_FK'         => $item['Steam_FK'] ?? null,
                ]
            );
            $insertedCount++;
        }

        // Inisialisasi draft approval harian jika belum ada
        if ($insertedCount > 0) {
            BoilerApproval::firstOrCreate(
                [
                    'tanggal' => $tanggal
                ],
                [
                    'status' => 'draft'
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil menyinkronkan {$insertedCount} data log boiler untuk tanggal {$tanggal}."
        ]);
    }
}
