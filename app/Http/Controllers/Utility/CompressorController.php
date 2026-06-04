<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\Compressor;
use App\Models\Utility\CompressorDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CompressorController extends Controller
{
    public function index()
    {
        return view('utility.compressor.form');
    }

    public function dataView()
    {
        return view('utility.compressor.data');
    }

    public function approvalView()
    {
        return view('utility.compressor.approval');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'jam' => 'required|in:08:00,12:00,16:00,20:00,00:00,04:00',

                'pressure_outlet_1' => 'nullable|numeric',
                'pressure_outlet_2' => 'nullable|numeric',
                'pressure_outlet_3' => 'nullable|numeric',
                'pressure_outlet_4' => 'nullable|numeric',

                'element_outlet_1' => 'nullable|numeric',
                'element_outlet_2' => 'nullable|numeric',
                'element_outlet_4' => 'nullable|numeric',

                'load_percent' => 'nullable|numeric',

                'running_hour_1' => 'nullable|numeric',
                'running_hour_2' => 'nullable|numeric',
                'running_hour_3' => 'nullable|numeric',
                'running_hour_4' => 'nullable|numeric',

                'loaded_hour_1' => 'nullable|numeric',
                'loaded_hour_2' => 'nullable|numeric',
                'loaded_hour_3' => 'nullable|numeric',
                'loaded_hour_4' => 'nullable|numeric',

                'motor_start_1' => 'nullable|numeric',
                'motor_start_2' => 'nullable|numeric',
                'motor_start_3' => 'nullable|numeric',
                'motor_start_4' => 'nullable|numeric',

                'accumulated_volume' => 'nullable|numeric',
                'temperature_comp_ir' => 'nullable|numeric',
                'pressure_in' => 'nullable|numeric',
                'pressure_out' => 'nullable|numeric',

                'suhu_dryer_tr15' => 'nullable|numeric',
                'suhu_dryer_fx250' => 'nullable|numeric',
                'suhu_dryer_ir' => 'nullable|numeric',
            ]);

            // Cek Duplikat di Details
            if (CompressorDetails::where('tanggal', $validated['tanggal'])
                ->where('jam', $validated['jam'])
                ->exists()
            ) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Laporan untuk tanggal ' .  $validated['tanggal'] . ' dan jam ' . $validated['jam'] . ' sudah ada'
                ], 422);
            }

            // Hitung Minggu, Bulan, Tahun berdasarkan Senin sebagai awal minggu
            $date = Carbon::parse($validated['tanggal'])->startOfDay();
            $monday = $date->copy()->startOfWeek(Carbon::MONDAY);
            $sunday = $date->copy()->endOfWeek(Carbon::SUNDAY);

            // Kita gunakan data dari hari Senin tersebut sebagai identitas minggu
            $week = $monday->weekOfMonth;
            $month = $monday->month;
            $year = $monday->year;

            $tgl_awal = $monday->format('Y-m-d');
            $tgl_akhir = $sunday->format('Y-m-d');

            // Find or create main record
            $main = Compressor::firstOrCreate(
                [
                    'week' => $week,
                    'bulan' => $month,
                    'tahun' => $year,
                ],
                [
                    'tgl_awal' => $tgl_awal,
                    'tgl_akhir' => $tgl_akhir,
                    'operator_id' => auth()->id(),
                    'status' => 'draft',
                ]
            );

            // Create Detail record
            $validated['compressor_id'] = $main->id;
            $detail = CompressorDetails::create($validated);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data compressor berhasil disimpan sebagai Draft.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Compressor Error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $detail = CompressorDetails::findOrFail($id);

        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'jam' => 'required|in:08:00,12:00,16:00,00:00,04:00',
                'pressure_outlet_1' => 'nullable|numeric',
                'pressure_outlet_2' => 'nullable|numeric',
                'pressure_outlet_3' => 'nullable|numeric',
                'pressure_outlet_4' => 'nullable|numeric',
                'element_outlet_1' => 'nullable|numeric',
                'element_outlet_2' => 'nullable|numeric',
                'element_outlet_4' => 'nullable|numeric',
                'load_percent' => 'nullable|numeric',
                'running_hour_1' => 'nullable|numeric',
                'running_hour_2' => 'nullable|numeric',
                'running_hour_3' => 'nullable|numeric',
                'running_hour_4' => 'nullable|numeric',
                'motor_start_1' => 'nullable|numeric',
                'motor_start_2' => 'nullable|numeric',
                'motor_start_3' => 'nullable|numeric',
                'motor_start_4' => 'nullable|numeric',
                'accumulated_volume' => 'nullable|numeric',
                'temperature_comp_ir' => 'nullable|numeric',
                'pressure_in' => 'nullable|numeric',
                'pressure_out' => 'nullable|numeric',
                'suhu_dryer_tr15' => 'nullable|numeric',
                'suhu_dryer_fx250' => 'nullable|numeric',
                'suhu_dryer_ir' => 'nullable|numeric',
            ]);

            $detail->update($validated);

            return response()->json([
                'status' => 200,
                'message' => 'Data compressor berhasil diperbarui.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            Log::error('Update Compressor Error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat update data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function submitWeekly(Request $request)
    {
        $validated = $request->validate([
            'week' => 'required|integer',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
            'foreman_id' => 'required|exists:users,id',
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $main = Compressor::where([
            'week' => $validated['week'],
            'bulan' => $validated['bulan'],
            'tahun' => $validated['tahun'],
        ])->first();

        if (!$main) {
            return response()->json(['message' => 'Data log untuk minggu ini belum tersedia'], 404);
        }

        if ($main->status !== 'draft' && $main->status !== 'rejected') {
            return response()->json(['message' => 'Laporan sudah disubmit atau diproses'], 422);
        }

        $main->update([
            'foreman_id' => $validated['foreman_id'],
            'supervisor_id' => $validated['supervisor_id'],
            'status' => 'submitted',
            'submitted_at' => now(),
            'operator_id' => auth()->id(),
        ]);

        try {
            $this->sendNotification($main);
        } catch (\Exception $e) {
            Log::error('Notif gagal: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Laporan mingguan berhasil disubmit untuk approval']);
    }

    private function sendNotification($main)
    {
        $approvalUrl = url(route('compressor.approval', [], false));

        $recipients = User::whereIn('id', array_filter([
            $main->foreman_id,
            $main->supervisor_id,
        ]))->get();

        foreach ($recipients as $user) {
            NotificationsModel::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notifiable_type' => Compressor::class,
                    'notifiable_id' => $main->id,
                    'is_read' => 0,
                ],
                [
                    'title' => 'Approval Mingguan Compressor',
                    'message' => "Laporan compressor Minggu ke-{$main->week}, Bulan {$main->bulan} {$main->tahun} menunggu persetujuan",
                    'url' => $approvalUrl,
                ]
            );
        }
    }

    public function getData(Request $request)
    {
        $query = CompressorDetails::with('compressor')->orderBy('tanggal', 'desc')->orderBy('jam', 'desc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month);
        }

        $data = $query->paginate($request->get('per_page', 15));

        // Kita tambahkan status approval untuk tiap data
        $items = collect($data->items())->map(function ($item) {
            $item->approval_status = $item->compressor ? $item->compressor->status : 'none';
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
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ]
        ]);
    }

    public function getCollectedData()
    {
        // Ambil data dari tabel main (compressor) yang statusnya masih draft/rejected
        $mainDrafts = Compressor::whereIn('status', ['draft', 'rejected'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('week', 'desc')
            ->get();

        $result = [];
        foreach ($mainDrafts as $main) {
            // Cek apakah data sudah "terkumpul se week" (opsional, tapi user minta "data masuk approval sudah ke collect se week")
            // Kita ambil detail data untuk minggu ini
            $details = CompressorDetails::where('compressor_id', $main->id)->get();

            if ($details->count() > 0) {
                $result[] = [
                    'approval' => $main,
                    'data' => $details
                ];
            }
        }

        return response()->json([
            'status' => 200,
            'results' => $result
        ]);
    }

    public function getApprovalData(Request $request)
    {
        $query = Compressor::with(['operator', 'foreman', 'supervisor'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('week', 'desc');

        if ($request->mode === 'approval') {
            $query->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where('foreman_id', auth()->id())
                        ->where('status', 'submitted');
                })->orWhere(function ($sq) {
                    $sq->where('supervisor_id', auth()->id())
                        ->where('status', 'approved_foreman');
                });
            });
        }

        $data = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 200,
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
            ]
        ]);
    }

    public function approveForeman($id)
    {
        $data = Compressor::findOrFail($id);

        if ($data->foreman_id !== auth()->id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        $data->update([
            'approved_foreman_at' => now(),
            'status' => 'approved_foreman'
        ]);

        NotificationsModel::where('notifiable_type', Compressor::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', auth()->id()) // opsional (biar spesifik)
            ->delete();

        return response()->json(['message' => 'Laporan disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = Compressor::findOrFail($id);

        if ($data->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        $data->update([
            'approved_supervisor_at' => now(),
            'status' => 'approved_supervisor'
        ]);

        NotificationsModel::where('notifiable_type', Compressor::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', auth()->id()) // opsional (biar spesifik)
            ->delete();

        return response()->json(['message' => 'Laporan disetujui Supervisor']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $data = Compressor::findOrFail($id);

        $data->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        NotificationsModel::where('notifiable_type', Compressor::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', auth()->id()) // opsional (biar spesifik)
            ->delete();

        return response()->json(['message' => 'Laporan ditolak']);
    }

    public function show($id)
    {
        $data = CompressorDetails::find($id);
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function export(Request $request)
    {
        $query = CompressorDetails::with(['compressor.operator', 'compressor.foreman', 'compressor.supervisor'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam', 'asc');

        if ($request->filled('week') || $request->filled('bulan') || $request->filled('tahun')) {
            $query->whereHas('compressor', function ($q) use ($request) {
                if ($request->filled('week')) $q->where('week', $request->week);
                if ($request->filled('bulan')) $q->where('bulan', $request->bulan);
                if ($request->filled('tahun')) $q->where('tahun', $request->tahun);
            });
        }

        $data = $query->get();

        if ($data->isEmpty()) {
            return "<script>alert('Tidak ada data ditemukan untuk periode tersebut'); window.close();</script>";
        }

        $templatePath = public_path('assets/templates/operasional/compressor.xlsx');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Header Info
        if ($request->filled('bulan')) {
            $monthNum = (int)$request->bulan;
            if ($monthNum >= 1 && $monthNum <= 12) {
                $monthName = Carbon::create()->month($monthNum)->translatedFormat('F');
                $sheet->setCellValue('Z1', 'BULAN : ' . strtoupper($monthName) . ' ' . $request->tahun);
            }
        }

        // Mapping jam ke row offset (8, 12, 16, 20, 00, 04)
        $jamMap = [
            '08:00' => 0,
            '08:00:00' => 0,
            '12:00' => 1,
            '12:00:00' => 1,
            '16:00' => 2,
            '16:00:00' => 2,
            '20:00' => 3,
            '20:00:00' => 3,
            '00:00' => 4,
            '00:00:00' => 4,
            '04:00' => 5,
            '04:00:00' => 5,
        ];

        // Baris awal tiap tanggal sesuai instruksi user
        $dayStartRows = [7, 13, 19, 25, 31, 37, 43];

        // Group data berdasarkan tanggal
        $grouped = $data->groupBy('tanggal');
        $dayCounter = 0;

        foreach ($grouped as $tanggal => $details) {
            if ($dayCounter >= 7) break; // Template terbatas untuk 7 hari

            $startRow = $dayStartRows[$dayCounter];
            $carbonDate = Carbon::parse($tanggal);

            // Set Tanggal di kolom A (biasanya cell merge)
            $sheet->setCellValue('A' . $startRow, $carbonDate->format('d/m/Y'));

            foreach ($details as $item) {
                $jamKey = substr($item->jam, 0, 5);
                if (!isset($jamMap[$jamKey])) continue;

                $currentRow = $startRow + $jamMap[$jamKey];

                // Mapping Kolom (C ke AC)
                $sheet->setCellValue('C' . $currentRow, $item->pressure_outlet_1);
                $sheet->setCellValue('D' . $currentRow, $item->pressure_outlet_2);
                $sheet->setCellValue('E' . $currentRow, $item->pressure_outlet_3);
                $sheet->setCellValue('F' . $currentRow, $item->pressure_outlet_4);

                $sheet->setCellValue('G' . $currentRow, $item->element_outlet_1);
                $sheet->setCellValue('H' . $currentRow, $item->element_outlet_2);
                $sheet->setCellValue('I' . $currentRow, $item->element_outlet_4);

                $sheet->setCellValue('J' . $currentRow, $item->load_percent);

                $sheet->setCellValue('K' . $currentRow, $item->running_hour_1);
                $sheet->setCellValue('L' . $currentRow, $item->running_hour_2);
                $sheet->setCellValue('M' . $currentRow, $item->running_hour_3);
                $sheet->setCellValue('N' . $currentRow, $item->running_hour_4);

                $sheet->setCellValue('O' . $currentRow, $item->loaded_hour_1);
                $sheet->setCellValue('P' . $currentRow, $item->loaded_hour_2);
                $sheet->setCellValue('Q' . $currentRow, $item->loaded_hour_3);
                $sheet->setCellValue('R' . $currentRow, $item->loaded_hour_4);

                $sheet->setCellValue('S' . $currentRow, $item->motor_start_1);
                $sheet->setCellValue('T' . $currentRow, $item->motor_start_2);
                $sheet->setCellValue('U' . $currentRow, $item->motor_start_3);
                $sheet->setCellValue('V' . $currentRow, $item->motor_start_4);

                $sheet->setCellValue('W' . $currentRow, $item->accumulated_volume);
                $sheet->setCellValue('X' . $currentRow, $item->temperature_comp_ir);
                $sheet->setCellValue('Y' . $currentRow, $item->pressure_in);
                $sheet->setCellValue('Z' . $currentRow, $item->pressure_out);

                $sheet->setCellValue('AA' . $currentRow, $item->suhu_dryer_tr15);
                $sheet->setCellValue('AB' . $currentRow, $item->suhu_dryer_fx250);
                $sheet->setCellValue('AC' . $currentRow, $item->suhu_dryer_ir);
            }

            $dayCounter++;
        }

        // TTD Approval Section
        $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');
        $mainRecord = $data->first()->compressor;

        if ($mainRecord) {
            $hasSticker = file_exists($signaturePath);

            // Operator (A54 = Username, A55 = Submitted Time)
            if ($mainRecord->status != 'draft') {
                if ($hasSticker) {
                    $drawingOperator = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawingOperator->setName('Submitted Operator');
                    $drawingOperator->setPath($signaturePath);
                    $drawingOperator->setHeight(60);
                    $drawingOperator->setCoordinates('A51');
                    $drawingOperator->setWorksheet($sheet);
                }
                $sheet->setCellValue('A54', $mainRecord->operator ? $mainRecord->operator->username : '-');
                $sheet->setCellValue('A55', $mainRecord->submitted_at ? Carbon::parse($mainRecord->submitted_at)->format('d/m/Y H:i') : '-');
            }

            // Foreman (J54 = Username, J55 = Approved Time)
            if (in_array($mainRecord->status, ['approved_foreman', 'approved_supervisor'])) {
                if ($hasSticker) {
                    $drawingForeman = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawingForeman->setName('Approved Foreman');
                    $drawingForeman->setPath($signaturePath);
                    $drawingForeman->setHeight(60);
                    $drawingForeman->setCoordinates('J51');
                    $drawingForeman->setWorksheet($sheet);
                }
                $sheet->setCellValue('J54', $mainRecord->foreman ? $mainRecord->foreman->username : '-');
                $sheet->setCellValue('J55', $mainRecord->approved_foreman_at ? Carbon::parse($mainRecord->approved_foreman_at)->format('d/m/Y H:i') : '-');
            }

            // Supervisor (T54 = Username, T55 = Approved Time)
            if ($mainRecord->status == 'approved_supervisor') {
                if ($hasSticker) {
                    $drawingSupervisor = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawingSupervisor->setName('Approved Supervisor');
                    $drawingSupervisor->setPath($signaturePath);
                    $drawingSupervisor->setHeight(60);
                    $drawingSupervisor->setCoordinates('T51');
                    $drawingSupervisor->setWorksheet($sheet);
                }
                $sheet->setCellValue('T54', $mainRecord->supervisor ? $mainRecord->supervisor->username : '-');
                $sheet->setCellValue('T55', $mainRecord->approved_supervisor_at ? Carbon::parse($mainRecord->approved_supervisor_at)->format('d/m/Y H:i') : '-');
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Compressor_Report_' . now()->format('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function showWeeklyDetails($id)
    {
        $main = Compressor::with(['operator', 'foreman', 'supervisor'])->findOrFail($id);
        $details = CompressorDetails::where('compressor_id', $id)
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam', 'asc')
            ->get();

        return response()->json([
            'status' => 200,
            'header' => $main,
            'details' => $details
        ]);
    }

    public function destroy($id)
    {
        $data = CompressorDetails::findOrFail($id);

        if ($data->status == 'approved_foreman' || $data->status == 'approved_supervisor') {
            return response()->json([
                'status' => 422,
                'message' => 'Data sudah disetujui, tidak dapat dihapus'
            ]);
        }

        $compressorId = $data->compressor_id;

        // Hapus detail dulu
        $data->delete();

        // Cek apakah masih ada detail lain dengan compressor_id yang sama
        $remainingDetails = CompressorDetails::where('compressor_id', $compressorId)->count();

        if ($remainingDetails == 0) {
            $main = Compressor::find($compressorId);
            if ($main) {
                $main->delete();
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
