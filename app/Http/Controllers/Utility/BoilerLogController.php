<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\BoilerLog;
use App\Models\Utility\BoilerApproval;
use App\Models\User;
use App\Models\NotificationsModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
        $tanggalInput = $request->input('tanggal', Carbon::today()->toDateString());

        // Normalisasi tanggal untuk DB (Y-m-d) dan API (j-m-Y atau format input asalnya)
        try {
            $carbonDate = Carbon::parse($tanggalInput);
            $tanggalDb = $carbonDate->toDateString(); // Y-m-d (misal: 2026-06-06)
            $tanggalApi = $carbonDate->format('j-m-Y'); // e.g. 6-06-2026
        } catch (\Exception $e) {
            $tanggalDb = Carbon::today()->toDateString();
            $tanggalApi = Carbon::today()->format('j-m-Y');
        }

        // Jika terdapat parameter 'data' yang berupa array di payload, gunakan langsung (Push Mode)
        if ($request->has('data') && is_array($request->input('data'))) {
            $logs = $request->input('data');
        } else {
            // Jika tidak, tarik data dari API sensor eksternal (Pull Mode)
            $apiUrl = "http://10.11.11.200/mybas/public/api/sensor/boiler/generate";

            try {
                $response = Http::timeout(15)->get($apiUrl, [
                    'tanggal' => $tanggalApi
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
                    'message' => "Tidak ada data sensor ditemukan untuk tanggal {$tanggalApi}."
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
                    'tanggal' => $tanggalDb
                ],
                [
                    'status' => 'draft'
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil menyinkronkan {$insertedCount} data log boiler untuk tanggal {$tanggalDb}."
        ]);
    }

    public function dataView()
    {
        return view('utility.boiler.data');
    }

    public function formView()
    {
        return view('utility.boiler.form');
    }

    public function approvalView()
    {
        return view('utility.boiler.approval');
    }

    public function getData(Request $request)
    {
        $query = BoilerApproval::with(['foreman', 'supervisor'])
            ->orderBy('tanggal', 'desc');

        $user = Auth::user();
        $jabatan = $user->jabatan;

        if ($request->mode === 'approval') {
            $query->where(function ($q) use ($user, $jabatan) {
                if ($jabatan === 'foreman') {
                    $q->where('status', 'draft');
                } elseif ($jabatan === 'supervisor') {
                    $q->where('status', 'waiting_supervisor');
                } else {
                    $q->where('status', '!=', 'approved_supervisor');
                }
            });
        }

        if ($request->filled('bulan')) {
            $bulan = $request->bulan; // Format: YYYY-MM
            $query->whereYear('tanggal', substr($bulan, 0, 4))
                  ->whereMonth('tanggal', substr($bulan, 5, 2));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate($request->get('per_page', 15));

        // Hitung total log count dan rata-rata indikator
        $items = collect($data->items())->map(function ($item) {
            // Siklus Harian 06:00 s.d 06:00 hari berikutnya
            $start = Carbon::parse($item->tanggal)->setTime(6, 0, 0);
            $end = Carbon::parse($item->tanggal)->addDay()->setTime(6, 0, 0);

            $logs = BoilerLog::whereBetween('waktu', [$start, $end])->get();
            
            $item->total_logs = $logs->count();
            $item->avg_steam = $logs->avg('PVSteam');
            $item->avg_temp = $logs->avg('SuhuFeedTank');
            return $item;
        });

        return response()->json([
            'status' => 200,
            'data' => $items,
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
            ]
        ]);
    }

    public function show($id)
    {
        $approval = BoilerApproval::with(['foreman', 'supervisor'])->find($id);

        if (!$approval) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan'], 404);
        }

        // Siklus Harian 06:00 s.d 06:00 hari berikutnya
        $start = Carbon::parse($approval->tanggal)->setTime(6, 0, 0);
        $end = Carbon::parse($approval->tanggal)->addDay()->setTime(6, 0, 0);

        $logs = BoilerLog::whereBetween('waktu', [$start, $end])
            ->orderBy('waktu', 'asc')
            ->get();

        return response()->json([
            'status' => 200,
            'approval' => $approval,
            'logs' => $logs
        ]);
    }

    public function getUsersForApproval()
    {
        $foremen = User::where('departemen', 'engineering')
            ->where('jabatan', 'foreman')
            ->get(['id', 'username']);

        $supervisors = User::where('departemen', 'engineering')
            ->where('jabatan', 'supervisor')
            ->get(['id', 'username']);

        return response()->json([
            'status' => 200,
            'foremen' => $foremen,
            'supervisors' => $supervisors
        ]);
    }

    public function submitDaily(Request $request, $id)
    {
        $request->validate([
            'foreman_id' => 'required|exists:users,id',
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $approval = BoilerApproval::findOrFail($id);

        if ($approval->status !== 'draft') {
            return response()->json(['message' => 'Hanya laporan draft yang dapat di-submit'], 422);
        }

        $approval->update([
            'foreman_id' => $request->foreman_id,
            'supervisor_id' => $request->supervisor_id,
            'status' => 'waiting_supervisor',
            'submitted_at' => now(),
        ]);

        // Kirim notifikasi ke Supervisor
        try {
            $dateFormatted = Carbon::parse($approval->tanggal)->translatedFormat('d F Y');
            NotificationsModel::create([
                'user_id' => $approval->supervisor_id,
                'title' => 'Approval Boiler Log Harian',
                'message' => "Laporan Boiler Log tanggal {$dateFormatted} menunggu persetujuan Anda.",
                'url' => url('/utility/boiler-logs/approval'),
                'notifiable_type' => BoilerApproval::class,
                'notifiable_id' => $approval->id,
                'is_read' => 0,
            ]);
        } catch (\Exception $e) {
            Log::error('Notification Boiler Log failed: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 200,
            'message' => 'Laporan berhasil di-submit untuk approval Supervisor.'
        ]);
    }

    public function approveDaily(Request $request, $id)
    {
        $approval = BoilerApproval::findOrFail($id);

        if ($approval->status !== 'waiting_supervisor') {
            return response()->json(['message' => 'Laporan harus berada dalam status menunggu supervisor.'], 422);
        }

        if (Auth::user()->jabatan !== 'supervisor' && Auth::user()->jabatan !== 'admin') {
            return response()->json(['message' => 'Anda tidak berwenang melakukan tindakan ini.'], 403);
        }

        $approval->update([
            'status' => 'approved_supervisor',
            'approved_at' => now(),
            'supervisor_id' => Auth::id(),
        ]);

        // Hapus notifikasi untuk user ini
        NotificationsModel::where('notifiable_type', BoilerApproval::class)
            ->where('notifiable_id', $approval->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Laporan berhasil disetujui oleh Supervisor.'
        ]);
    }

    public function rejectDaily(Request $request, $id)
    {
        $approval = BoilerApproval::findOrFail($id);

        if ($approval->status !== 'waiting_supervisor') {
            return response()->json(['message' => 'Hanya laporan yang menunggu supervisor yang dapat ditolak.'], 422);
        }

        $approval->update([
            'status' => 'draft',
            'foreman_id' => null,
            'submitted_at' => null,
            'approved_at' => null,
        ]);

        // Hapus notifikasi terkait
        NotificationsModel::where('notifiable_type', BoilerApproval::class)
            ->where('notifiable_id', $approval->id)
            ->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Laporan berhasil ditolak dan dikembalikan ke status draft.'
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $tanggal = $request->tanggal;
        $approval = BoilerApproval::with(['foreman', 'supervisor'])->where('tanggal', $tanggal)->first();

        // Siklus Harian 06:00 s.d 06:00 hari berikutnya
        $start = Carbon::parse($tanggal)->setTime(6, 0, 0);
        $end = Carbon::parse($tanggal)->addDay()->setTime(6, 0, 0);

        $logs = BoilerLog::whereBetween('waktu', [$start, $end])
            ->orderBy('waktu', 'asc')
            ->get();

        if ($logs->isEmpty()) {
            return "<script>alert('Tidak ada data log ditemukan untuk tanggal tersebut.'); window.close();</script>";
        }

        $templatePath = public_path('assets/templates/template_excel_boiler.xlsx');
        if (!file_exists($templatePath)) {
            return "<script>alert('Template Excel Boiler tidak ditemukan'); window.close();</script>";
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Header Info (Date)
        $dateFormatted = Carbon::parse($tanggal)->translatedFormat('d F Y');
        $sheet->setCellValue('P1', 'Date: ' . $dateFormatted);

        // Fill hourly logs from row 11 to 35
        foreach ($logs as $item) {
            $logTime = Carbon::parse($item->waktu);
            $hour = $logTime->hour;
            
            // Tentukan baris data berdasarkan jam log
            if ($logTime->isSameDay($start)) {
                if ($hour >= 6) {
                    $currentRow = 11 + ($hour - 6);
                } else {
                    continue;
                }
            } else {
                if ($hour <= 6) {
                    $currentRow = 29 + $hour;
                } else {
                    continue;
                }
            }

            if ($currentRow >= 11 && $currentRow <= 35) {
                $sheet->setCellValue('B' . $currentRow, $item->PVSteam);
                $sheet->setCellValue('C' . $currentRow, $item->IDFan);
                $sheet->setCellValue('D' . $currentRow, $item->LHFDFan);
                $sheet->setCellValue('E' . $currentRow, $item->RHFDFan);
                $sheet->setCellValue('F' . $currentRow, $item->LHStoker);
                $sheet->setCellValue('G' . $currentRow, $item->RHStoker);
                $sheet->setCellValue('H' . $currentRow, $item->water_flow_total);
                $sheet->setCellValue('I' . $currentRow, $item->LevelFeedWater);
                $sheet->setCellValue('J' . $currentRow, $item->water_hmi_flow_rate);
                $sheet->setCellValue('K' . $currentRow, $item->water_hmi_total);
                $sheet->setCellValue('L' . $currentRow, $item->O2);
                $sheet->setCellValue('M' . $currentRow, $item->CO2);
                $sheet->setCellValue('N' . $currentRow, $item->flue_gass_temp);
                $sheet->setCellValue('O' . $currentRow, $item->LHGuiloutine);
                $sheet->setCellValue('P' . $currentRow, $item->RHGuiloutine);
                $sheet->setCellValue('Q' . $currentRow, $item->LHTemp);
                $sheet->setCellValue('R' . $currentRow, $item->RHTemp);
                $sheet->setCellValue('S' . $currentRow, $item->SuhuFeedTank);
            }
        }

        // Tanda tangan & Stempel (TTD)
        $signaturePath = public_path('assets/images/ttd/utility_approved_sticker.png');
        $hasSticker = file_exists($signaturePath);

        // Operator (Dibuat oleh) - Sistem pengumpul data otomatis (selalu digambar stempel)
        if ($hasSticker) {
            $drawOp = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawOp->setName('Operator');
            $drawOp->setPath($signaturePath);
            $drawOp->setHeight(60);
            $drawOp->setOffsetX(35); // Pas di tengah kolom A-G
            $drawOp->setCoordinates('A38');
            $drawOp->setWorksheet($sheet);
        }
        $sheet->setCellValue('A42', Carbon::parse($tanggal)->format('d/m/Y'));

        if ($approval) {
            // Foreman (Diperiksa oleh)
            if (in_array($approval->status, ['waiting_supervisor', 'approved_supervisor'])) {
                if ($hasSticker) {
                    $drawFm = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawFm->setName('Foreman');
                    $drawFm->setPath($signaturePath);
                    $drawFm->setHeight(60);
                    $drawFm->setOffsetX(35); // Geser sedikit ke kanan agar pas di tengah kolom H-M
                    $drawFm->setCoordinates('H38');
                    $drawFm->setWorksheet($sheet);
                }
                $sheet->setCellValue('H42', $approval->submitted_at ? Carbon::parse($approval->submitted_at)->format('d/m/Y H:i') : '-');
            }

            // Supervisor (Disetujui oleh)
            if ($approval->status === 'approved_supervisor') {
                if ($hasSticker) {
                    $drawSpv = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawSpv->setName('Supervisor');
                    $drawSpv->setPath($signaturePath);
                    $drawSpv->setHeight(60);
                    $drawSpv->setOffsetX(35); // Geser sedikit ke kanan agar pas di tengah kolom N-S
                    $drawSpv->setCoordinates('N38');
                    $drawSpv->setWorksheet($sheet);
                }
                $sheet->setCellValue('N42', $approval->approved_at ? Carbon::parse($approval->approved_at)->format('d/m/Y H:i') : '-');
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Boiler_Log_Report_' . Carbon::parse($tanggal)->format('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'             => 'required|date',
            'jam'                 => 'required|date_format:H:i',
            'PVSteam'             => 'nullable|numeric',
            'FeedPressure'        => 'nullable|numeric',
            'Press_Pasteur'       => 'nullable|numeric',
            'LevelFeedWater'      => 'nullable|numeric',
            'InletWaterFlow'      => 'nullable|numeric',
            'OutletSteamFlow'     => 'nullable|numeric',
            'SuhuFeedTank'        => 'nullable|numeric',
            'IDFan'               => 'nullable|numeric',
            'LHFDFan'             => 'nullable|numeric',
            'RHFDFan'             => 'nullable|numeric',
            'LHStoker'            => 'nullable|numeric',
            'RHStoker'            => 'nullable|numeric',
            'LHTemp'              => 'nullable|numeric',
            'RHTemp'              => 'nullable|numeric',
            'O2'                  => 'nullable|numeric',
            'CO2'                 => 'nullable|numeric',
            'LHGuiloutine'        => 'nullable|numeric',
            'RHGuiloutine'        => 'nullable|numeric',
            'WaterPump1'          => 'nullable|numeric',
            'WaterPump2'          => 'nullable|numeric',
            'Batubara_FK'         => 'nullable|numeric',
            'Steam_FK'            => 'nullable|numeric',
            'water_flow_total'    => 'nullable|numeric',
            'water_hmi_flow_rate' => 'nullable|numeric',
            'water_hmi_total'     => 'nullable|numeric',
            'flue_gass_temp'      => 'nullable|numeric',
        ]);

        $tanggal = $request->tanggal;
        $waktu = Carbon::parse($tanggal . ' ' . $request->jam)->format('Y-m-d H:i:s');

        // Tentukan operational date dari log waktu ini untuk mengecek status approval harian
        $logTime = Carbon::parse($waktu);
        $operationalDate = $logTime->hour < 6 ? $logTime->copy()->subDay()->toDateString() : $logTime->toDateString();

        $approval = BoilerApproval::where('tanggal', $operationalDate)->first();
        if ($approval) {
            if ($approval->status === 'approved_supervisor') {
                return response()->json(['message' => 'Laporan harian untuk tanggal operasional ini sudah disetujui, tidak dapat diisi.'], 422);
            }
            if ($approval->status === 'waiting_supervisor') {
                // Hanya foreman, supervisor, dan admin yang boleh mengisi/mengedit jika status waiting_supervisor
                $user = Auth::user();
                if ($user && !in_array($user->jabatan, ['foreman', 'supervisor', 'admin'])) {
                    return response()->json(['message' => 'Laporan harian ini sedang menunggu persetujuan supervisor. Hanya foreman atau supervisor yang dapat mengisi/mengubah.'], 422);
                }
            }
        }

        // Cek apakah data waktu tersebut sudah ada di database (misal dari sync sensor)
        $existingLog = BoilerLog::where('waktu', $waktu)->first();
        if (!$existingLog) {
            return response()->json([
                'message' => 'Data sensor untuk tanggal dan jam tersebut belum tersinkronisasi. Silakan pastikan data sudah ter-sync terlebih dahulu sebelum mengisi parameter manual.'
            ], 422);
        }

        // Update data manual saja pada log yang sudah ada
        $existingLog->update([
            'water_flow_total'    => $request->water_flow_total,
            'water_hmi_flow_rate' => $request->water_hmi_flow_rate,
            'water_hmi_total'     => $request->water_hmi_total,
            'flue_gass_temp'      => $request->flue_gass_temp,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Data manual log boiler berhasil disimpan.',
            'log' => $existingLog
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->jabatan, ['foreman', 'supervisor', 'admin'])) {
            return response()->json(['message' => 'Anda tidak berwenang melakukan pengeditan.'], 403);
        }

        $request->validate([
            'PVSteam'             => 'nullable|numeric',
            'FeedPressure'        => 'nullable|numeric',
            'Press_Pasteur'       => 'nullable|numeric',
            'LevelFeedWater'      => 'nullable|numeric',
            'InletWaterFlow'      => 'nullable|numeric',
            'OutletSteamFlow'     => 'nullable|numeric',
            'SuhuFeedTank'        => 'nullable|numeric',
            'IDFan'               => 'nullable|numeric',
            'LHFDFan'             => 'nullable|numeric',
            'RHFDFan'             => 'nullable|numeric',
            'LHStoker'            => 'nullable|numeric',
            'RHStoker'            => 'nullable|numeric',
            'LHTemp'              => 'nullable|numeric',
            'RHTemp'              => 'nullable|numeric',
            'O2'                  => 'nullable|numeric',
            'CO2'                 => 'nullable|numeric',
            'LHGuiloutine'        => 'nullable|numeric',
            'RHGuiloutine'        => 'nullable|numeric',
            'WaterPump1'          => 'nullable|numeric',
            'WaterPump2'          => 'nullable|numeric',
            'Batubara_FK'         => 'nullable|numeric',
            'Steam_FK'            => 'nullable|numeric',
            'water_flow_total'    => 'nullable|numeric',
            'water_hmi_flow_rate' => 'nullable|numeric',
            'water_hmi_total'     => 'nullable|numeric',
            'flue_gass_temp'      => 'nullable|numeric',
        ]);

        $log = BoilerLog::findOrFail($id);

        // Tentukan operational date dari log waktu ini untuk mengecek status approval harian
        $logTime = Carbon::parse($log->waktu);
        $operationalDate = $logTime->hour < 6 ? $logTime->copy()->subDay()->toDateString() : $logTime->toDateString();

        $approval = BoilerApproval::where('tanggal', $operationalDate)->first();
        if ($approval && $approval->status !== 'draft') {
            // Jika sudah di-approve atau diajukan (selain draft), hanya foreman dan admin yang boleh mengedit
            if (!in_array($user->jabatan, ['foreman', 'admin'])) {
                return response()->json(['message' => 'Laporan sudah diajukan/disetujui. Hanya foreman yang dapat mengedit data manual.'], 422);
            }
        }

        $log->update([
            'PVSteam'             => $request->PVSteam,
            'FeedPressure'        => $request->FeedPressure,
            'Press_Pasteur'       => $request->Press_Pasteur,
            'LevelFeedWater'      => $request->LevelFeedWater,
            'InletWaterFlow'      => $request->InletWaterFlow,
            'OutletSteamFlow'     => $request->OutletSteamFlow,
            'SuhuFeedTank'        => $request->SuhuFeedTank,
            'IDFan'               => $request->IDFan,
            'LHFDFan'             => $request->LHFDFan,
            'RHFDFan'             => $request->RHFDFan,
            'LHStoker'            => $request->LHStoker,
            'RHStoker'            => $request->RHStoker,
            'LHTemp'              => $request->LHTemp,
            'RHTemp'              => $request->RHTemp,
            'O2'                  => $request->O2,
            'CO2'                 => $request->CO2,
            'LHGuiloutine'        => $request->LHGuiloutine,
            'RHGuiloutine'        => $request->RHGuiloutine,
            'WaterPump1'          => $request->WaterPump1,
            'WaterPump2'          => $request->WaterPump2,
            'Batubara_FK'         => $request->Batubara_FK,
            'Steam_FK'            => $request->Steam_FK,
            'water_flow_total'    => $request->water_flow_total,
            'water_hmi_flow_rate' => $request->water_hmi_flow_rate,
            'water_hmi_total'     => $request->water_hmi_total,
            'flue_gass_temp'      => $request->flue_gass_temp,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Data log boiler berhasil diperbarui.'
        ]);
    }
}
