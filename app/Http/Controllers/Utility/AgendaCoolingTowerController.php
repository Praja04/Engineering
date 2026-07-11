<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\AgendaCoolingTower;
use App\Models\Utility\AgendaCoolingTowerDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgendaCoolingTowerController extends Controller
{
    public function index()
    {
        return view('utility.agenda-cooling-tower.form');
    }

    public function dataView()
    {
        return view('utility.agenda-cooling-tower.data');
    }

    public function approvalView()
    {
        return view('utility.agenda-cooling-tower.approval');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'kelistrikan_pompa_10000p2' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10000p2a' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10000p2b' => 'nullable|in:OK,NOK',
                'kelistrikan_fan_1' => 'nullable|in:OK,NOK',
                'kelistrikan_fan_2' => 'nullable|in:OK,NOK',
                'kelistrikan_fan_3' => 'nullable|in:OK,NOK',
                'kelistrikan_fan_4' => 'nullable|in:OK,NOK',
                'suhu_out_ct' => 'nullable|in:OK,NOK',
                'suhu_in_ct' => 'nullable|in:OK,NOK',
                'pressure_out_ct' => 'nullable|in:OK,NOK',
                'pressure_in_ct' => 'nullable|in:OK,NOK',
                'ph_air_ct' => 'nullable|in:OK,NOK',
                'stok_chemical' => 'nullable|in:OK,NOK',
                'cleaning_saringan_bak' => 'nullable|in:OK,NOK',
                'cleaning_strainer_10000p2' => 'nullable|in:OK,NOK',
                'cleaning_strainer_10000p2a' => 'nullable|in:OK,NOK',
                'cleaning_strainer_10000p2b' => 'nullable|in:OK,NOK',
                'greasing_pompa_10000p2' => 'nullable|in:OK,NOK',
                'greasing_pompa_10000p2a' => 'nullable|in:OK,NOK',
                'greasing_pompa_10000p2b' => 'nullable|in:OK,NOK',
                'rubber_coupling_10000p2' => 'nullable|in:OK,NOK',
                'rubber_coupling_10000p2a' => 'nullable|in:OK,NOK',
                'rubber_coupling_10000p2b' => 'nullable|in:OK,NOK',
                'cleaning_valve_10000p2' => 'nullable|in:OK,NOK',
                'cleaning_valve_10000p2a' => 'nullable|in:OK,NOK',
                'cleaning_valve_10000p2b' => 'nullable|in:OK,NOK',
                'kalibrasi_dosis_chemical' => 'nullable|in:OK,NOK',
                'greasing_cleaning_fan_1' => 'nullable|in:OK,NOK',
                'greasing_cleaning_fan_2' => 'nullable|in:OK,NOK',
                'greasing_cleaning_fan_3' => 'nullable|in:OK,NOK',
                'greasing_cleaning_fan_4' => 'nullable|in:OK,NOK',
                'sling_fan_ct_1' => 'nullable|in:OK,NOK',
                'sling_fan_ct_2' => 'nullable|in:OK,NOK',
                'sling_fan_ct_3' => 'nullable|in:OK,NOK',
                'sling_fan_ct_4' => 'nullable|in:OK,NOK',
                'inspeksi_baut_mur' => 'nullable|in:OK,NOK',
            ]);

            // Cek Duplikat di Details
            if (AgendaCoolingTowerDetails::where('tanggal', $validated['tanggal'])->exists()) {
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
            $main = AgendaCoolingTower::firstOrCreate(
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

            $validated['agenda_cooling_tower_id'] = $main->id;
            $validated['created_by'] = Auth::id();
            $detail = AgendaCoolingTowerDetails::create($validated);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data agenda checklist cooling tower berhasil disimpan.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Agenda Cooling Tower Error: ' . $e->getMessage());
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
            $detail = AgendaCoolingTowerDetails::findOrFail($id);

            $validated = $request->validate([
                'tanggal' => 'required|date',
                'kelistrikan_pompa_10000p2' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10000p2a' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10000p2b' => 'nullable|in:OK,NOK',
                'kelistrikan_fan_1' => 'nullable|in:OK,NOK',
                'kelistrikan_fan_2' => 'nullable|in:OK,NOK',
                'kelistrikan_fan_3' => 'nullable|in:OK,NOK',
                'kelistrikan_fan_4' => 'nullable|in:OK,NOK',
                'suhu_out_ct' => 'nullable|in:OK,NOK',
                'suhu_in_ct' => 'nullable|in:OK,NOK',
                'pressure_out_ct' => 'nullable|in:OK,NOK',
                'pressure_in_ct' => 'nullable|in:OK,NOK',
                'ph_air_ct' => 'nullable|in:OK,NOK',
                'stok_chemical' => 'nullable|in:OK,NOK',
                'cleaning_saringan_bak' => 'nullable|in:OK,NOK',
                'cleaning_strainer_10000p2' => 'nullable|in:OK,NOK',
                'cleaning_strainer_10000p2a' => 'nullable|in:OK,NOK',
                'cleaning_strainer_10000p2b' => 'nullable|in:OK,NOK',
                'greasing_pompa_10000p2' => 'nullable|in:OK,NOK',
                'greasing_pompa_10000p2a' => 'nullable|in:OK,NOK',
                'greasing_pompa_10000p2b' => 'nullable|in:OK,NOK',
                'rubber_coupling_10000p2' => 'nullable|in:OK,NOK',
                'rubber_coupling_10000p2a' => 'nullable|in:OK,NOK',
                'rubber_coupling_10000p2b' => 'nullable|in:OK,NOK',
                'cleaning_valve_10000p2' => 'nullable|in:OK,NOK',
                'cleaning_valve_10000p2a' => 'nullable|in:OK,NOK',
                'cleaning_valve_10000p2b' => 'nullable|in:OK,NOK',
                'kalibrasi_dosis_chemical' => 'nullable|in:OK,NOK',
                'greasing_cleaning_fan_1' => 'nullable|in:OK,NOK',
                'greasing_cleaning_fan_2' => 'nullable|in:OK,NOK',
                'greasing_cleaning_fan_3' => 'nullable|in:OK,NOK',
                'greasing_cleaning_fan_4' => 'nullable|in:OK,NOK',
                'sling_fan_ct_1' => 'nullable|in:OK,NOK',
                'sling_fan_ct_2' => 'nullable|in:OK,NOK',
                'sling_fan_ct_3' => 'nullable|in:OK,NOK',
                'sling_fan_ct_4' => 'nullable|in:OK,NOK',
                'inspeksi_baut_mur' => 'nullable|in:OK,NOK',
            ]);

            // Cek Duplikat di Details (jika tanggal berubah)
            if (
                $detail->tanggal->format('Y-m-d') !== $validated['tanggal'] &&
                AgendaCoolingTowerDetails::where('tanggal', $validated['tanggal'])->exists()
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
                'message' => 'Data agenda checklist cooling tower berhasil diperbarui.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Agenda Cooling Tower Error: ' . $e->getMessage());
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

        $main = AgendaCoolingTower::where([
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
            Log::error('Notif Agenda Cooling Tower gagal: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Laporan bulanan berhasil disubmit untuk approval']);
    }

    public function getData(Request $request)
    {
        $query = AgendaCoolingTowerDetails::with(['agendaCoolingTower', 'createdBy:id,username'])
            ->orderBy('tanggal', 'desc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month);
        }

        $data = $query->paginate($request->get('per_page', 15));

        $items = collect($data->items())->map(function ($item) {
            $item->approval_status = $item->agendaCoolingTower ? $item->agendaCoolingTower->status : 'none';
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

        $mainDrafts = AgendaCoolingTower::whereIn('status', ['draft', 'rejected'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $result = [];
        foreach ($mainDrafts as $main) {
            $details = AgendaCoolingTowerDetails::where('agenda_cooling_tower_id', $main->id)->get();
            if ($details->count() > 0) {
                $result[] = ['approval' => $main, 'data' => $details];
            }
        }
        return response()->json(['status' => 200, 'results' => $result]);
    }

    public function getApprovalData(Request $request)
    {
        $query = AgendaCoolingTower::with(['operator', 'foreman', 'supervisor'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc');

        if ($request->mode === 'approval') {
            $query->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where('foreman_id', auth()->id())->where('status', 'submitted');
                })->orWhere(function ($sq) {
                    $sq->where('supervisor_id', auth()->id())->where('status', 'approved_foreman');
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
        $data = AgendaCoolingTower::findOrFail($id);

        if ($data->foreman_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data->update([
            'approved_foreman_at' => now(),
            'status' => 'approved_foreman'
        ]);

        NotificationsModel::where('notifiable_type', AgendaCoolingTower::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        try {
            $this->sendNotification($data, $data->supervisor_id);
        } catch (\Exception $e) {
            Log::error('Notif Agenda Cooling Tower Supervisor gagal: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = AgendaCoolingTower::findOrFail($id);
        if ($data->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data->update([
            'approved_supervisor_at' => now(),
            'status' => 'approved_supervisor'
        ]);

        NotificationsModel::where('notifiable_type', AgendaCoolingTower::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Disetujui Supervisor']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);

        $data = AgendaCoolingTower::findOrFail($id);

        $data->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        NotificationsModel::where('notifiable_type', AgendaCoolingTower::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Ditolak']);
    }

    public function show($id)
    {
        $data = AgendaCoolingTowerDetails::with('createdBy')->find($id);
        if ($data) {
            $data->creator_name = $data->createdBy ? $data->createdBy->username : '-';
        }
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function showMonthlyDetails($id)
    {
        $main = AgendaCoolingTower::with(['operator', 'foreman', 'supervisor'])->findOrFail($id);
        $details = AgendaCoolingTowerDetails::where('agenda_cooling_tower_id', $id)->orderBy('tanggal', 'asc')->get();
        return response()->json(['status' => 200, 'header' => $main, 'details' => $details]);
    }

    public function destroy($id)
    {
        $detail = AgendaCoolingTowerDetails::findOrFail($id);
        $parentId = $detail->agenda_cooling_tower_id;
        $detail->delete();

        // Check if there are any details left under this header
        $remainingDetails = AgendaCoolingTowerDetails::where('agenda_cooling_tower_id', $parentId)->count();
        if ($remainingDetails == 0) {
            $m = AgendaCoolingTower::find($parentId);
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
        $query = AgendaCoolingTowerDetails::with(['agendaCoolingTower.operator', 'agendaCoolingTower.foreman', 'agendaCoolingTower.supervisor'])
            ->orderBy('tanggal', 'asc');

        if ($request->filled('bulan')) {
            $query->whereHas('agendaCoolingTower', function ($q) use ($request) {
                $q->where('bulan', $request->bulan);
            });
        }
        if ($request->filled('tahun')) {
            $query->whereHas('agendaCoolingTower', function ($q) use ($request) {
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

        $templatePath = public_path('assets/templates/operasional/agenda_cooling_tower.xlsx');
        if (!file_exists($templatePath)) {
            return response()->json([
                'status' => 404,
                'message' => 'File template excel tidak ditemukan.'
            ], 404);
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);

        $tempFiles = [];
        $monthsData = $data->groupBy(function ($item) {
            return $item->agendaCoolingTower->bulan;
        })->sortKeys();

        // Row map mapping 36 fields to row numbers 7 to 42
        $rowMap = [
            'kelistrikan_pompa_10000p2' => 7,
            'kelistrikan_pompa_10000p2a' => 8,
            'kelistrikan_pompa_10000p2b' => 9,
            'kelistrikan_fan_1' => 10,
            'kelistrikan_fan_2' => 11,
            'kelistrikan_fan_3' => 12,
            'kelistrikan_fan_4' => 13,
            'suhu_out_ct' => 14,
            'suhu_in_ct' => 15,
            'pressure_out_ct' => 16,
            'pressure_in_ct' => 17,
            'ph_air_ct' => 18,
            'stok_chemical' => 19,
            'cleaning_saringan_bak' => 20,
            'cleaning_strainer_10000p2' => 21,
            'cleaning_strainer_10000p2a' => 22,
            'cleaning_strainer_10000p2b' => 23,
            'greasing_pompa_10000p2' => 24,
            'greasing_pompa_10000p2a' => 25,
            'greasing_pompa_10000p2b' => 26,
            'rubber_coupling_10000p2' => 27,
            'rubber_coupling_10000p2a' => 28,
            'rubber_coupling_10000p2b' => 29,
            'cleaning_valve_10000p2' => 30,
            'cleaning_valve_10000p2a' => 31,
            'cleaning_valve_10000p2b' => 32,
            'kalibrasi_dosis_chemical' => 33,
            'greasing_cleaning_fan_1' => 34,
            'greasing_cleaning_fan_2' => 35,
            'greasing_cleaning_fan_3' => 36,
            'greasing_cleaning_fan_4' => 37,
            'sling_fan_ct_1' => 38,
            'sling_fan_ct_2' => 39,
            'sling_fan_ct_3' => 40,
            'sling_fan_ct_4' => 41,
            'inspeksi_baut_mur' => 42,
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

            // Write Month and Year to D5 (User requirement: D5 Bulan: Tahun:)
            $yearStr = $request->tahun ?? date('Y');
            $sheet->setCellValue('D5', 'BULAN: ' . strtoupper($monthName) . ' - TAHUN: ' . $yearStr);

            // User requirement: Day 1 maps to Column D (index 4)
            // Fields map to rows 7 to 42
            foreach ($monthRecords as $item) {
                $day = (int)Carbon::parse($item->tanggal)->day;
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3 + $day); // Day 1 = Column D (index 4)

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
            // USER REQUEST: Sticker at C45, Q45, AC45. Username at A49, K49, X49. Time approved at A50, K50, X50.
            $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');
            $mainRecord = $monthRecords->first()->agendaCoolingTower;

            if ($mainRecord) {
                $hasSticker = file_exists($signaturePath);

                // Operator/Submitter (C45 = Sticker, A49 = Username, A50 = Created At)
                if (in_array($mainRecord->status, ['draft', 'submitted', 'approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathOp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_ct_op_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathOp);
                        $tempFiles[] = $tempPathOp;

                        $drawingOperator = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingOperator->setName('Submitted Operator ' . $monthNum);
                        $drawingOperator->setPath($tempPathOp);
                        $drawingOperator->setHeight(80);
                        $drawingOperator->setCoordinates('C45');
                        $drawingOperator->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('A49', $mainRecord->operator ? $mainRecord->operator->username : '-');
                    $sheet->setCellValue('A50', $mainRecord->created_at ? Carbon::parse($mainRecord->created_at)->format('d/m/Y H:i') : '-');
                }

                // Foreman (Q45 = Sticker, K49 = Username, K50 = Approved Foreman At)
                if (in_array($mainRecord->status, ['approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathFm = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_ct_fm_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathFm);
                        $tempFiles[] = $tempPathFm;

                        $drawingForeman = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingForeman->setName('Approved Foreman ' . $monthNum);
                        $drawingForeman->setPath($tempPathFm);
                        $drawingForeman->setHeight(80);
                        $drawingForeman->setCoordinates('Q45');
                        $drawingForeman->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('K49', $mainRecord->foreman ? $mainRecord->foreman->username : '-');
                    $sheet->setCellValue('K50', $mainRecord->approved_foreman_at ? Carbon::parse($mainRecord->approved_foreman_at)->format('d/m/Y H:i') : '-');
                }

                // Supervisor (AC45 = Sticker, X49 = Username, X50 = Approved Supervisor At)
                if ($mainRecord->status == 'approved_supervisor') {
                    if ($hasSticker) {
                        $tempPathSpv = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_ct_spv_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathSpv);
                        $tempFiles[] = $tempPathSpv;

                        $drawingSupervisor = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingSupervisor->setName('Approved Supervisor ' . $monthNum);
                        $drawingSupervisor->setPath($tempPathSpv);
                        $drawingSupervisor->setHeight(80);
                        $drawingSupervisor->setCoordinates('AC45');
                        $drawingSupervisor->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('X49', $mainRecord->supervisor ? $mainRecord->supervisor->username : '-');
                    $sheet->setCellValue('X50', $mainRecord->approved_supervisor_at ? Carbon::parse($mainRecord->approved_supervisor_at)->format('d/m/Y H:i') : '-');
                }
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Agenda_Cooling_Tower_Report_' . ($request->bulan ? Carbon::create()->month((int)$request->bulan)->translatedFormat('F') : 'All') . '_' . ($request->tahun ?? date('Y')) . '.xlsx';

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
        $approvalUrl = url(route('agenda-cooling-tower.approval', [], false));
        $targetUserId = $userId ?? $main->supervisor_id;

        if ($targetUserId) {
            NotificationsModel::updateOrCreate(
                [
                    'user_id' => $targetUserId,
                    'notifiable_type' => AgendaCoolingTower::class,
                    'notifiable_id' => $main->id,
                    'is_read' => 0
                ],
                [
                    'title' => 'Approval Bulanan Agenda Cooling Tower',
                    'message' => "Laporan Agenda Cooling Tower Bulan {$main->bulan} {$main->tahun} menunggu persetujuan",
                    'url' => $approvalUrl
                ]
            );
        }
    }
}
