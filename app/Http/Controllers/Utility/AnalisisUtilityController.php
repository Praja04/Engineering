<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\AnalisisUtility;
use App\Models\Utility\AnalisisUtilityDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AnalisisUtilityController extends Controller
{
    public function index()
    {
        return view('utility.analisis-utility.form');
    }

    public function dataView()
    {
        return view('utility.analisis-utility.data');
    }

    public function approvalView()
    {
        return view('utility.analisis-utility.approval');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $rules = [
                'tanggal' => 'required|date',
            ];

            $fields = [
                'ph_fw_storage',
                'ph_ws_storage',
                'ph_ro_storage',
                'ph_in_mmf',
                'ph_buffer_tank_ws',
                'ph_outlet_ws',
                'ph_menara_ws',
                'ph_depo_lt1',
                'ph_depo_lt2',
                'ph_cooling_tower',
                'ph_boiler',
                'ph_outlet_ws_2',
                'tds_fw_storage',
                'tds_ws_storage',
                'tds_ro_storage',
                'tds_in_mmf',
                'tds_out_ro',
                'tds_menara_ws',
                'tds_daily_tank_dissolver',
                'tds_depo_lt1',
                'tds_depo_lt2',
                'tds_cooling_tower',
                'tds_boiler',
                'turbidity_in_mmf',
                'turbidity_out_mmf',
                'turbidity_cooling_tower',
                'chlorine_mmf',
                'chlorine_menara',
                'chlorine_depo_lt1',
                'chlorine_depo_lt2',
                'chlorine_daily_tank_dissolver',
                'hardness_inlet_ws',
                'hardness_outlet_ws',
                'hardness_ws_storage',
                'hardness_ct',
                'hardness_ro',
                'hardness_boiler',
            ];

            foreach ($fields as $f) {
                $rules[$f] = 'nullable|numeric';
            }

            $validated = $request->validate($rules);

            // Cek Duplikat di Details
            if (AnalisisUtilityDetails::where('tanggal', $validated['tanggal'])->exists()) {
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
            $main = AnalisisUtility::firstOrCreate(
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

            $validated['analisis_utility_id'] = $main->id;
            $validated['created_by'] = Auth::id();
            $detail = AnalisisUtilityDetails::create($validated);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data analisis utility berhasil disimpan.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Analisis Utility Error: ' . $e->getMessage());
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
            $detail = AnalisisUtilityDetails::findOrFail($id);

            $rules = [
                'tanggal' => 'required|date',
            ];

            $fields = [
                'ph_fw_storage',
                'ph_ws_storage',
                'ph_ro_storage',
                'ph_in_mmf',
                'ph_buffer_tank_ws',
                'ph_outlet_ws',
                'ph_menara_ws',
                'ph_depo_lt1',
                'ph_depo_lt2',
                'ph_cooling_tower',
                'ph_boiler',
                'ph_outlet_ws_2',
                'tds_fw_storage',
                'tds_ws_storage',
                'tds_ro_storage',
                'tds_in_mmf',
                'tds_out_ro',
                'tds_menara_ws',
                'tds_daily_tank_dissolver',
                'tds_depo_lt1',
                'tds_depo_lt2',
                'tds_cooling_tower',
                'tds_boiler',
                'turbidity_in_mmf',
                'turbidity_out_mmf',
                'turbidity_cooling_tower',
                'chlorine_mmf',
                'chlorine_menara',
                'chlorine_depo_lt1',
                'chlorine_depo_lt2',
                'chlorine_daily_tank_dissolver',
                'hardness_inlet_ws',
                'hardness_outlet_ws',
                'hardness_ws_storage',
                'hardness_ct',
                'hardness_ro',
                'hardness_boiler',
            ];

            foreach ($fields as $f) {
                $rules[$f] = 'nullable|numeric';
            }

            $validated = $request->validate($rules);

            // Cek Duplikat di Details (jika tanggal berubah)
            if (
                $detail->tanggal->format('Y-m-d') !== $validated['tanggal'] &&
                AnalisisUtilityDetails::where('tanggal', $validated['tanggal'])->exists()
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
                'message' => 'Data analisis utility berhasil diperbarui.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Analisis Utility Error: ' . $e->getMessage());
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

        $main = AnalisisUtility::where([
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
            Log::error('Notif Analisis Utility gagal: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Laporan bulanan berhasil disubmit untuk approval']);
    }

    public function getData(Request $request)
    {
        $query = AnalisisUtilityDetails::with(['analisisUtility', 'createdBy:id,username'])
            ->orderBy('tanggal', 'desc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month);
        }

        $data = $query->paginate($request->get('per_page', 15));

        $items = collect($data->items())->map(function ($item) {
            $item->approval_status = $item->analisisUtility ? $item->analisisUtility->status : 'none';
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
        if (Auth::user()->jabatan !== 'foreman') {
            return response()->json(['status' => 200, 'results' => []]);
        }

        $mainDrafts = AnalisisUtility::whereIn('status', ['draft', 'rejected'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $result = [];
        foreach ($mainDrafts as $main) {
            $details = AnalisisUtilityDetails::where('analisis_utility_id', $main->id)->get();
            if ($details->count() > 0) {
                $result[] = ['approval' => $main, 'data' => $details];
            }
        }
        return response()->json(['status' => 200, 'results' => $result]);
    }

    public function getApprovalData(Request $request)
    {
        $query = AnalisisUtility::with(['operator', 'foreman', 'supervisor'])
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
        $data = AnalisisUtility::findOrFail($id);

        if ($data->foreman_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data->update([
            'approved_foreman_at' => now(),
            'status' => 'approved_foreman'
        ]);

        NotificationsModel::where('notifiable_type', AnalisisUtility::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        try {
            $this->sendNotification($data, $data->supervisor_id);
        } catch (\Exception $e) {
            Log::error('Notif Analisis Utility Supervisor gagal: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = AnalisisUtility::findOrFail($id);
        if ($data->supervisor_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data->update([
            'approved_supervisor_at' => now(),
            'status' => 'approved_supervisor'
        ]);

        NotificationsModel::where('notifiable_type', AnalisisUtility::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Disetujui Supervisor']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);

        $data = AnalisisUtility::findOrFail($id);

        $data->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        NotificationsModel::where('notifiable_type', AnalisisUtility::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Ditolak']);
    }

    public function show($id)
    {
        $data = AnalisisUtilityDetails::with('createdBy')->find($id);
        if ($data) {
            $data->creator_name = $data->createdBy ? $data->createdBy->username : '-';
        }
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function showMonthlyDetails($id)
    {
        $main = AnalisisUtility::with(['operator', 'foreman', 'supervisor'])->findOrFail($id);
        $details = AnalisisUtilityDetails::where('analisis_utility_id', $id)->orderBy('tanggal', 'asc')->get();
        return response()->json(['status' => 200, 'header' => $main, 'details' => $details]);
    }

    public function destroy($id)
    {
        $detail = AnalisisUtilityDetails::findOrFail($id);
        $parentId = $detail->analisis_utility_id;
        $detail->delete();

        // Check if there are any details left under this header
        $remainingDetails = AnalisisUtilityDetails::where('analisis_utility_id', $parentId)->count();
        if ($remainingDetails == 0) {
            $m = AnalisisUtility::find($parentId);
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
        $query = AnalisisUtilityDetails::with(['analisisUtility.operator', 'analisisUtility.foreman', 'analisisUtility.supervisor'])
            ->orderBy('tanggal', 'asc');

        if ($request->filled('bulan')) {
            $query->whereHas('analisisUtility', function ($q) use ($request) {
                $q->where('bulan', $request->bulan);
            });
        }
        if ($request->filled('tahun')) {
            $query->whereHas('analisisUtility', function ($q) use ($request) {
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

        $templatePath = public_path('assets/templates/operasional/analisis_utility.xlsx');
        if (!file_exists($templatePath)) {
            return response()->json([
                'status' => 404,
                'message' => 'File template excel tidak ditemukan.'
            ], 404);
        }

        $spreadsheet = IOFactory::load($templatePath);

        $tempFiles = [];
        $monthsData = $data->groupBy(function ($item) {
            return $item->analisisUtility->bulan;
        })->sortKeys();

        // List 37 fields in exact sequential order mapping to Columns C to AM
        $fields = [
            'ph_fw_storage',
            'ph_ws_storage',
            'ph_ro_storage',
            'ph_in_mmf',
            'ph_buffer_tank_ws',
            'ph_outlet_ws',
            'ph_menara_ws',
            'ph_depo_lt1',
            'ph_depo_lt2',
            'ph_cooling_tower',
            'ph_boiler',
            'ph_outlet_ws_2',
            'tds_fw_storage',
            'tds_ws_storage',
            'tds_ro_storage',
            'tds_in_mmf',
            'tds_out_ro',
            'tds_menara_ws',
            'tds_daily_tank_dissolver',
            'tds_depo_lt1',
            'tds_depo_lt2',
            'tds_cooling_tower',
            'tds_boiler',
            'turbidity_in_mmf',
            'turbidity_out_mmf',
            'turbidity_cooling_tower',
            'chlorine_mmf',
            'chlorine_menara',
            'chlorine_depo_lt1',
            'chlorine_depo_lt2',
            'chlorine_daily_tank_dissolver',
            'hardness_inlet_ws',
            'hardness_outlet_ws',
            'hardness_ws_storage',
            'hardness_ct',
            'hardness_ro',
            'hardness_boiler',
        ];

        $isFirst = true;
        foreach ($monthsData as $monthNum => $monthRecords) {
            $monthName = Carbon::create()->month($monthNum)->translatedFormat('F');

            if ($isFirst) {
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle($monthName);
                $isFirst = false;
            } else {
                $tempSpreadsheet = IOFactory::load($templatePath);
                $tempSheet = $tempSpreadsheet->getActiveSheet();
                $tempSheet->setTitle($monthName);
                $sheet = $spreadsheet->addExternalSheet($tempSheet);
            }

            // Write Month and Year to AH1 (User requirement: AH1 Bulan: Tahun:)
            $yearStr = $request->tahun ?? date('Y');
            $sheet->setCellValue('AH1', 'BULAN: ' . strtoupper($monthName) . ' - TAHUN: ' . $yearStr);

            // User requirement: Rows start at Row 6 for first date, Row 7 etc.
            // Column A represents day number format
            // Column C is Field 1, Column D is Field 2, ...
            $rowNum = 6;
            foreach ($monthRecords as $item) {
                $formattedDate = Carbon::parse($item->tanggal)->format('d');
                $sheet->setCellValue('A' . $rowNum, $formattedDate);

                $fieldIndex = 0;
                foreach ($fields as $fieldName) {
                    $val = $item->{$fieldName};

                    $colLetter = Coordinate::stringFromColumnIndex(3 + $fieldIndex); // C is 3
                    $cell = $colLetter . $rowNum;
                    if ($val !== null && $val !== '') {
                        $sheet->setCellValue($cell, (float)$val);
                    } else {
                        $sheet->setCellValue($cell, '');
                    }
                    $fieldIndex++;
                }
                $rowNum++;
            }

            // TTD / Approval Section
            // USER REQUEST: Sticker at G39, T39, AG39. Username at A42, N42, AB42. Time at A43, N43, AB43.
            $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');
            $mainRecord = $monthRecords->first()->analisisUtility;

            if ($mainRecord) {
                $hasSticker = file_exists($signaturePath);

                // Operator/Submitter (G39 = Sticker, A42 = Username, A43 = Created At)
                if (in_array($mainRecord->status, ['draft', 'submitted', 'approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathOp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_analisis_op_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathOp);
                        $tempFiles[] = $tempPathOp;

                        $drawingOperator = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingOperator->setName('Submitted Operator ' . $monthNum);
                        $drawingOperator->setPath($tempPathOp);
                        $drawingOperator->setHeight(80);
                        $drawingOperator->setCoordinates('G39');
                        $drawingOperator->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('A42', $mainRecord->operator ? $mainRecord->operator->username : '-');
                    $sheet->setCellValue('A43', $mainRecord->created_at ? Carbon::parse($mainRecord->created_at)->format('d/m/Y H:i') : '-');
                }

                // Foreman (T39 = Sticker, N42 = Username, N43 = Approved Foreman At)
                if (in_array($mainRecord->status, ['approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathFm = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_analisis_fm_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathFm);
                        $tempFiles[] = $tempPathFm;

                        $drawingForeman = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingForeman->setName('Approved Foreman ' . $monthNum);
                        $drawingForeman->setPath($tempPathFm);
                        $drawingForeman->setHeight(80);
                        $drawingForeman->setCoordinates('T39');
                        $drawingForeman->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('N42', $mainRecord->foreman ? $mainRecord->foreman->username : '-');
                    $sheet->setCellValue('N43', $mainRecord->approved_foreman_at ? Carbon::parse($mainRecord->approved_foreman_at)->format('d/m/Y H:i') : '-');
                }

                // Supervisor (AG39 = Sticker, AB42 = Username, AB43 = Approved Supervisor At)
                if ($mainRecord->status == 'approved_supervisor') {
                    if ($hasSticker) {
                        $tempPathSpv = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_analisis_spv_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathSpv);
                        $tempFiles[] = $tempPathSpv;

                        $drawingSupervisor = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingSupervisor->setName('Approved Supervisor ' . $monthNum);
                        $drawingSupervisor->setPath($tempPathSpv);
                        $drawingSupervisor->setHeight(80);
                        $drawingSupervisor->setCoordinates('AG39');
                        $drawingSupervisor->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('AB42', $mainRecord->supervisor ? $mainRecord->supervisor->username : '-');
                    $sheet->setCellValue('AB43', $mainRecord->approved_supervisor_at ? Carbon::parse($mainRecord->approved_supervisor_at)->format('d/m/Y H:i') : '-');
                }
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Analisis_Utility_Report_' . ($request->bulan ? Carbon::create()->month((int)$request->bulan)->translatedFormat('F') : 'All') . '_' . ($request->tahun ?? date('Y')) . '.xlsx';

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
        $approvalUrl = url(route('analisis-utility.approval', [], false));
        $targetUserId = $userId ?? $main->supervisor_id;

        if ($targetUserId) {
            NotificationsModel::updateOrCreate(
                [
                    'user_id' => $targetUserId,
                    'notifiable_type' => AnalisisUtility::class,
                    'notifiable_id' => $main->id,
                    'is_read' => 0
                ],
                [
                    'title' => 'Approval Bulanan Analisis Utility',
                    'message' => "Laporan Analisis Utility Bulan {$main->bulan} {$main->tahun} menunggu persetujuan",
                    'url' => $approvalUrl
                ]
            );
        }
    }
}
