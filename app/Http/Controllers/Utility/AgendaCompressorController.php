<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\AgendaCompressor;
use App\Models\Utility\AgendaCompressorDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AgendaCompressorController extends Controller
{
    public function index()
    {
        return view('utility.agenda-compressor.form');
    }

    public function dataView()
    {
        return view('utility.agenda-compressor.data');
    }

    public function approvalView()
    {
        return view('utility.agenda-compressor.approval');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'pressure_aq55vsd' => 'nullable|in:OK,NOK',
                'running_hour_aq55vsd' => 'nullable|in:OK,NOK',
                'element_outlet_aq55vsd' => 'nullable|in:OK,NOK',
                'kelistrikan_aq55vsd' => 'nullable|in:OK,NOK',
                'rpm_aq55vsd' => 'nullable|in:OK,NOK',
                'pressure_ga37' => 'nullable|in:OK,NOK',
                'running_hour_ga37' => 'nullable|in:OK,NOK',
                'kelistrikan_ga37' => 'nullable|in:OK,NOK',
                'element_outlet_ga37' => 'nullable|in:OK,NOK',
                'pressure_ir55' => 'nullable|in:OK,NOK',
                'running_hour_ir55' => 'nullable|in:OK,NOK',
                'kelistrikan_ir55' => 'nullable|in:OK,NOK',
                'temperature_ir55' => 'nullable|in:OK,NOK',
                'cleaning_strainer_aq55vsd' => 'nullable|in:OK,NOK',
                'cleaning_valve_ga37' => 'nullable|in:OK,NOK',
                'replace_filter_ir55' => 'nullable|in:OK,NOK',
                'inspeksi_motor_aq55vsd' => 'nullable|in:OK,NOK',
                'inspeksi_motor_ga37' => 'nullable|in:OK,NOK',
                'inspeksi_motor_ir55' => 'nullable|in:OK,NOK',
                'inspeksi_dryer_120' => 'nullable|in:OK,NOK',
                'inspeksi_dryer_tr15' => 'nullable|in:OK,NOK',
                'inspeksi_dryer_ir' => 'nullable|in:OK,NOK',
                'pressure_in_out_ct' => 'nullable|in:OK,NOK',
                'pressure_bejana_receiver' => 'nullable|in:OK,NOK',
                'pressure_in_out_dryer' => 'nullable|in:OK,NOK',
            ]);

            // Cek Duplikat di Details
            if (AgendaCompressorDetails::where('tanggal', $validated['tanggal'])->exists()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Laporan checklist untuk tanggal ' . $validated['tanggal'] . ' sudah ada'
                ], 422);
            }

            // Hitung Bulan dan Tahun
            $date = Carbon::parse($validated['tanggal']);
            $month = $date->month;
            $year = $date->year;

            // Find or create main record
            $main = AgendaCompressor::firstOrCreate(
                [
                    'bulan' => $month,
                    'tahun' => $year,
                ],
                [
                    'operator_id' => Auth::id(),
                    'status' => 'draft',
                    'submitted_at' => now(),
                ]
            );

            // USER REQUIREMENT: User can still input even if approved foreman/supervisor.
            // Bypassing any approval checks/locks here.

            if (empty($main->operator_id)) {
                $main->update(['operator_id' => Auth::id()]);
            }

            $validated['agenda_compressor_id'] = $main->id;
            $validated['created_by'] = Auth::id();
            $detail = AgendaCompressorDetails::create($validated);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data agenda checklist compressor berhasil disimpan.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Agenda Compressor Error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $detail = AgendaCompressorDetails::findOrFail($id);

            $validated = $request->validate([
                'tanggal' => 'required|date',
                'pressure_aq55vsd' => 'nullable|in:OK,NOK',
                'running_hour_aq55vsd' => 'nullable|in:OK,NOK',
                'element_outlet_aq55vsd' => 'nullable|in:OK,NOK',
                'kelistrikan_aq55vsd' => 'nullable|in:OK,NOK',
                'rpm_aq55vsd' => 'nullable|in:OK,NOK',
                'pressure_ga37' => 'nullable|in:OK,NOK',
                'running_hour_ga37' => 'nullable|in:OK,NOK',
                'kelistrikan_ga37' => 'nullable|in:OK,NOK',
                'element_outlet_ga37' => 'nullable|in:OK,NOK',
                'pressure_ir55' => 'nullable|in:OK,NOK',
                'running_hour_ir55' => 'nullable|in:OK,NOK',
                'kelistrikan_ir55' => 'nullable|in:OK,NOK',
                'temperature_ir55' => 'nullable|in:OK,NOK',
                'cleaning_strainer_aq55vsd' => 'nullable|in:OK,NOK',
                'cleaning_valve_ga37' => 'nullable|in:OK,NOK',
                'replace_filter_ir55' => 'nullable|in:OK,NOK',
                'inspeksi_motor_aq55vsd' => 'nullable|in:OK,NOK',
                'inspeksi_motor_ga37' => 'nullable|in:OK,NOK',
                'inspeksi_motor_ir55' => 'nullable|in:OK,NOK',
                'inspeksi_dryer_120' => 'nullable|in:OK,NOK',
                'inspeksi_dryer_tr15' => 'nullable|in:OK,NOK',
                'inspeksi_dryer_ir' => 'nullable|in:OK,NOK',
                'pressure_in_out_ct' => 'nullable|in:OK,NOK',
                'pressure_bejana_receiver' => 'nullable|in:OK,NOK',
                'pressure_in_out_dryer' => 'nullable|in:OK,NOK',
            ]);

            // Cek Duplikat di Details (jika tanggal berubah)
            if (
                $detail->tanggal->format('Y-m-d') !== $validated['tanggal'] &&
                AgendaCompressorDetails::where('tanggal', $validated['tanggal'])->exists()
            ) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Laporan untuk tanggal ' . $validated['tanggal'] . ' sudah ada'
                ], 422);
            }

            // USER REQUIREMENT: User can still update even if approved foreman/supervisor.
            // Bypassing any approval checks/locks here.

            $detail->update([
                ...$validated,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data agenda checklist compressor berhasil diperbarui.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Agenda Compressor Error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat update data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function submitMonthly(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $main = AgendaCompressor::where([
            'bulan' => $validated['bulan'],
            'tahun' => $validated['tahun'],
        ])->first();

        if (!$main) {
            return response()->json(['message' => 'Data log untuk bulan ini belum tersedia'], 404);
        }

        if ($main->status !== 'draft' && $main->status !== 'rejected') {
            return response()->json(['message' => 'Laporan sudah disubmit atau diproses'], 422);
        }

        $main->update([
            'foreman_id' => Auth::id(),
            'supervisor_id' => $validated['supervisor_id'],
            'status' => 'approved_foreman',
            'submitted_at' => now(),
            'approved_foreman_at' => now(),
        ]);

        try {
            $this->sendNotification($main);
        } catch (\Exception $e) {
            Log::error('Notif Agenda Compressor gagal: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Laporan bulanan berhasil disubmit untuk approval']);
    }

    public function getData(Request $request)
    {
        $query = AgendaCompressorDetails::with(['agendaCompressor', 'createdBy:id,username'])
            ->orderBy('tanggal', 'desc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month);
        }

        $data = $query->paginate($request->get('per_page', 15));

        $items = collect($data->items())->map(function ($item) {
            $item->approval_status = $item->agendaCompressor ? $item->agendaCompressor->status : 'none';
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

    public function getCollectedData()
    {
        if (auth()->user()->jabatan !== 'foreman') {
            return response()->json(['status' => 200, 'results' => []]);
        }

        $mainDrafts = AgendaCompressor::whereIn('status', ['draft', 'rejected'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $result = [];
        foreach ($mainDrafts as $main) {
            $details = AgendaCompressorDetails::where('agenda_compressor_id', $main->id)->get();
            if ($details->count() > 0) {
                $result[] = ['approval' => $main, 'data' => $details];
            }
        }
        return response()->json(['status' => 200, 'results' => $result]);
    }

    public function getApprovalData(Request $request)
    {
        $query = AgendaCompressor::with(['operator', 'foreman', 'supervisor'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc');

        if ($request->mode === 'approval') {
            $query->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where('foreman_id', Auth::id())->where('status', 'submitted');
                })->orWhere(function ($sq) {
                    $sq->where('supervisor_id', Auth::id())->where('status', 'approved_foreman');
                });
            });
        }

        $data = $query->paginate($request->get('per_page', 15));
        return response()->json([
            'status' => 200,
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'last_page' => $data->lastPage()
            ]
        ]);
    }

    public function approveForeman($id)
    {
        $data = AgendaCompressor::findOrFail($id);

        if ($data->foreman_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data->update([
            'approved_foreman_at' => now(),
            'status' => 'approved_foreman'
        ]);

        NotificationsModel::where('notifiable_type', AgendaCompressor::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        try {
            $this->sendNotification($data, $data->supervisor_id);
        } catch (\Exception $e) {
            Log::error('Notif Agenda Compressor Supervisor gagal: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = AgendaCompressor::findOrFail($id);
        if ($data->supervisor_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data->update([
            'approved_supervisor_at' => now(),
            'status' => 'approved_supervisor'
        ]);

        NotificationsModel::where('notifiable_type', AgendaCompressor::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Disetujui Supervisor']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);

        $data = AgendaCompressor::findOrFail($id);

        $data->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        NotificationsModel::where('notifiable_type', AgendaCompressor::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Ditolak']);
    }

    public function show($id)
    {
        $data = AgendaCompressorDetails::with('createdBy')->find($id);
        if ($data) {
            $data->creator_name = $data->createdBy ? $data->createdBy->username : '-';
        }
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function showMonthlyDetails($id)
    {
        $main = AgendaCompressor::with(['operator', 'foreman', 'supervisor'])->findOrFail($id);
        $details = AgendaCompressorDetails::where('agenda_compressor_id', $id)->orderBy('tanggal', 'asc')->get();
        return response()->json(['status' => 200, 'header' => $main, 'details' => $details]);
    }

    public function destroy($id)
    {
        $detail = AgendaCompressorDetails::findOrFail($id);
        $parentId = $detail->agenda_compressor_id;
        $detail->delete();

        // Check if there are any details left under this header
        $remainingDetails = AgendaCompressorDetails::where('agenda_compressor_id', $parentId)->count();
        if ($remainingDetails == 0) {
            $m = AgendaCompressor::find($parentId);
            if ($m) {
                $m->delete();
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Data berhasil dihapus'
        ]);
    }

    public function export(Request $request)
    {
        $query = AgendaCompressorDetails::with(['agendaCompressor.operator', 'agendaCompressor.foreman', 'agendaCompressor.supervisor'])
            ->orderBy('tanggal', 'asc');

        if ($request->filled('bulan')) {
            $query->whereHas('agendaCompressor', function ($q) use ($request) {
                $q->where('bulan', $request->bulan);
            });
        }
        if ($request->filled('tahun')) {
            $query->whereHas('agendaCompressor', function ($q) use ($request) {
                $q->where('tahun', $request->tahun);
            });
        }

        $data = $query->get();

        if ($data->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'Tidak ada data ditemukan untuk periode tersebut.'
            ], 404);
        }

        $templatePath = public_path('assets/templates/operasional/agenda_compressor.xlsx');
        if (!file_exists($templatePath)) {
            return response()->json([
                'status' => 404,
                'message' => 'File template excel tidak ditemukan.'
            ], 404);
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);

        $tempFiles = [];
        $monthsData = $data->groupBy(function ($item) {
            return $item->agendaCompressor->bulan;
        })->sortKeys();

        // Row map mapping 25 fields to row numbers 7 to 31
        $rowMap = [
            'pressure_aq55vsd' => 7,
            'running_hour_aq55vsd' => 8,
            'element_outlet_aq55vsd' => 9,
            'kelistrikan_aq55vsd' => 10,
            'rpm_aq55vsd' => 11,
            'pressure_ga37' => 12,
            'running_hour_ga37' => 13,
            'kelistrikan_ga37' => 14,
            'element_outlet_ga37' => 15,
            'pressure_ir55' => 16,
            'running_hour_ir55' => 17,
            'kelistrikan_ir55' => 18,
            'temperature_ir55' => 19,
            'cleaning_strainer_aq55vsd' => 20,
            'cleaning_valve_ga37' => 21,
            'replace_filter_ir55' => 22,
            'inspeksi_motor_aq55vsd' => 23,
            'inspeksi_motor_ga37' => 24,
            'inspeksi_motor_ir55' => 25,
            'inspeksi_dryer_120' => 26,
            'inspeksi_dryer_tr15' => 27,
            'inspeksi_dryer_ir' => 28,
            'pressure_in_out_ct' => 29,
            'pressure_bejana_receiver' => 30,
            'pressure_in_out_dryer' => 31,
        ];

        $isFirst = true;
        foreach ($monthsData as $monthNum => $monthRecords) {
            $monthName = Carbon::create()->month($monthNum)->translatedFormat('F');

            if ($isFirst) {
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle($monthName);
                $isFirst = false;
            } else {
                $tempSpreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
                $tempSheet = $tempSpreadsheet->getActiveSheet();
                $tempSheet->setTitle($monthName);
                $sheet = $spreadsheet->addExternalSheet($tempSheet);
            }

            // Write Month and Year to C5 (User requirement: C5 Bulan: Tahun:)
            $yearStr = $request->tahun ?? date('Y');
            $sheet->setCellValue('C5', 'BULAN: ' . strtoupper($monthName) . ' - TAHUN: ' . $yearStr);

            // User requirement: Day 1 maps to Column C (index 3)
            // Fields map to rows 7 to 31
            foreach ($monthRecords as $item) {
                $day = (int)Carbon::parse($item->tanggal)->day;
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(2 + $day); // Day 1 = Column C (index 3)

                foreach ($rowMap as $field => $rowNum) {
                    $val = $item->{$field};
                    $symbol = '';
                    $color = null;
                    if ($val === 'OK') {
                        $symbol = '✓';
                        $color = 'FF28A745'; // Green
                    } elseif ($val === 'NOK') {
                        $symbol = '✗';
                        $color = 'FFDC3545'; // Red
                    }

                    $cell = $colLetter . $rowNum;
                    $sheet->setCellValue($cell, $symbol);
                    if ($color) {
                        $sheet->getStyle($cell)->getFont()->getColor()->setARGB($color);
                    }
                }
            }

            // TTD / Approval Section
            // USER REQUEST: Sticker at C34, O34, AA34. Username at A38, J38, V38. Time approved at A39, J39, V39.
            $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');
            $mainRecord = $monthRecords->first()->agendaCompressor;

            if ($mainRecord) {
                $hasSticker = file_exists($signaturePath);

                // Operator/Submitter (C34 = Sticker, A38 = Username, A39 = Created At)
                if (in_array($mainRecord->status, ['draft', 'submitted', 'approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathOp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_comp_op_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathOp);
                        $tempFiles[] = $tempPathOp;

                        $drawingOperator = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingOperator->setName('Submitted Operator ' . $monthNum);
                        $drawingOperator->setPath($tempPathOp);
                        $drawingOperator->setHeight(80);
                        $drawingOperator->setCoordinates('C34');
                        $drawingOperator->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('A38', $mainRecord->operator ? $mainRecord->operator->username : '-');
                    $sheet->setCellValue('A39', $mainRecord->created_at ? Carbon::parse($mainRecord->created_at)->format('d/m/Y H:i') : '-');
                }

                // Foreman (O34 = Sticker, J38 = Username, J39 = Approved Foreman At)
                if (in_array($mainRecord->status, ['approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathFm = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_comp_fm_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathFm);
                        $tempFiles[] = $tempPathFm;

                        $drawingForeman = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingForeman->setName('Approved Foreman ' . $monthNum);
                        $drawingForeman->setPath($tempPathFm);
                        $drawingForeman->setHeight(80);
                        $drawingForeman->setCoordinates('O34');
                        $drawingForeman->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('J38', $mainRecord->foreman ? $mainRecord->foreman->username : '-');
                    $sheet->setCellValue('J39', $mainRecord->approved_foreman_at ? Carbon::parse($mainRecord->approved_foreman_at)->format('d/m/Y H:i') : '-');
                }

                // Supervisor (AA34 = Sticker, V38 = Username, V39 = Approved Supervisor At)
                if ($mainRecord->status == 'approved_supervisor') {
                    if ($hasSticker) {
                        $tempPathSpv = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_comp_spv_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathSpv);
                        $tempFiles[] = $tempPathSpv;

                        $drawingSupervisor = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingSupervisor->setName('Approved Supervisor ' . $monthNum);
                        $drawingSupervisor->setPath($tempPathSpv);
                        $drawingSupervisor->setHeight(80);
                        $drawingSupervisor->setCoordinates('AA34');
                        $drawingSupervisor->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('V38', $mainRecord->supervisor ? $mainRecord->supervisor->username : '-');
                    $sheet->setCellValue('V39', $mainRecord->approved_supervisor_at ? Carbon::parse($mainRecord->approved_supervisor_at)->format('d/m/Y H:i') : '-');
                }
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Agenda_Compressor_Report_' . ($request->bulan ? Carbon::create()->month((int)$request->bulan)->translatedFormat('F') : 'All') . '_' . ($request->tahun ?? date('Y')) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');

        foreach ($tempFiles as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }

        exit;
    }

    private function sendNotification($main, $userId = null)
    {
        $approvalUrl = url(route('agenda-compressor.approval', [], false));
        $targetUserId = $userId ?? $main->supervisor_id;

        if ($targetUserId) {
            NotificationsModel::updateOrCreate(
                [
                    'user_id' => $targetUserId,
                    'notifiable_type' => AgendaCompressor::class,
                    'notifiable_id' => $main->id,
                    'is_read' => 0
                ],
                [
                    'title' => 'Approval Bulanan Agenda Compressor',
                    'message' => "Laporan Agenda Compressor Bulan {$main->bulan} {$main->tahun} menunggu persetujuan",
                    'url' => $approvalUrl
                ]
            );
        }
    }
}
