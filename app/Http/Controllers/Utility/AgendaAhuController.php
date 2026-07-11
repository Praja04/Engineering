<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\AgendaAhu;
use App\Models\Utility\AgendaAhuDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgendaAhuController extends Controller
{
    public function index()
    {
        return view('utility.agenda-ahu.form');
    }

    public function dataView()
    {
        return view('utility.agenda-ahu.data');
    }

    public function approvalView()
    {
        return view('utility.agenda-ahu.approval');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'kelistrikan_ahu_1' => 'nullable|in:OK,NOK',
                'kelistrikan_ahu_2' => 'nullable|in:OK,NOK',
                'kelistrikan_ahu_3' => 'nullable|in:OK,NOK',
                'kelistrikan_ahu_4' => 'nullable|in:OK,NOK',
                'pressur_gauge_in_ahu_1' => 'nullable|in:OK,NOK',
                'pressur_gauge_in_ahu_2' => 'nullable|in:OK,NOK',
                'pressur_gauge_in_ahu_3' => 'nullable|in:OK,NOK',
                'pressur_gauge_in_ahu_4' => 'nullable|in:OK,NOK',
                'pressur_gauge_out_ahu_1' => 'nullable|in:OK,NOK',
                'pressur_gauge_out_ahu_2' => 'nullable|in:OK,NOK',
                'pressur_gauge_out_ahu_3' => 'nullable|in:OK,NOK',
                'pressur_gauge_out_ahu_4' => 'nullable|in:OK,NOK',
                'temp_gauge_in_ahu_1' => 'nullable|in:OK,NOK',
                'temp_gauge_in_ahu_2' => 'nullable|in:OK,NOK',
                'temp_gauge_in_ahu_3' => 'nullable|in:OK,NOK',
                'temp_gauge_in_ahu_4' => 'nullable|in:OK,NOK',
                'temp_gauge_out_ahu_1' => 'nullable|in:OK,NOK',
                'temp_gauge_out_ahu_2' => 'nullable|in:OK,NOK',
                'temp_gauge_out_ahu_3' => 'nullable|in:OK,NOK',
                'temp_gauge_out_ahu_4' => 'nullable|in:OK,NOK',
                'clean_filter_strainer_1' => 'nullable|in:OK,NOK',
                'clean_filter_strainer_2' => 'nullable|in:OK,NOK',
                'clean_filter_strainer_3' => 'nullable|in:OK,NOK',
                'clean_filter_strainer_4' => 'nullable|in:OK,NOK',
                'clean_filter_bebas_ahu' => 'nullable|in:OK,NOK',
                'inspeksi_h_ahu_1_4' => 'nullable|in:OK,NOK',
            ]);

            // Cek Duplikat di Details
            if (AgendaAhuDetails::where('tanggal', $validated['tanggal'])->exists()) {
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
            $main = AgendaAhu::firstOrCreate(
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

            $validated['agenda_ahu_id'] = $main->id;
            $validated['created_by'] = Auth::id();
            $detail = AgendaAhuDetails::create($validated);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data agenda checklist AHU berhasil disimpan.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Agenda AHU Error: ' . $e->getMessage());
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
            $detail = AgendaAhuDetails::findOrFail($id);

            $validated = $request->validate([
                'tanggal' => 'required|date',
                'kelistrikan_ahu_1' => 'nullable|in:OK,NOK',
                'kelistrikan_ahu_2' => 'nullable|in:OK,NOK',
                'kelistrikan_ahu_3' => 'nullable|in:OK,NOK',
                'kelistrikan_ahu_4' => 'nullable|in:OK,NOK',
                'pressur_gauge_in_ahu_1' => 'nullable|in:OK,NOK',
                'pressur_gauge_in_ahu_2' => 'nullable|in:OK,NOK',
                'pressur_gauge_in_ahu_3' => 'nullable|in:OK,NOK',
                'pressur_gauge_in_ahu_4' => 'nullable|in:OK,NOK',
                'pressur_gauge_out_ahu_1' => 'nullable|in:OK,NOK',
                'pressur_gauge_out_ahu_2' => 'nullable|in:OK,NOK',
                'pressur_gauge_out_ahu_3' => 'nullable|in:OK,NOK',
                'pressur_gauge_out_ahu_4' => 'nullable|in:OK,NOK',
                'temp_gauge_in_ahu_1' => 'nullable|in:OK,NOK',
                'temp_gauge_in_ahu_2' => 'nullable|in:OK,NOK',
                'temp_gauge_in_ahu_3' => 'nullable|in:OK,NOK',
                'temp_gauge_in_ahu_4' => 'nullable|in:OK,NOK',
                'temp_gauge_out_ahu_1' => 'nullable|in:OK,NOK',
                'temp_gauge_out_ahu_2' => 'nullable|in:OK,NOK',
                'temp_gauge_out_ahu_3' => 'nullable|in:OK,NOK',
                'temp_gauge_out_ahu_4' => 'nullable|in:OK,NOK',
                'clean_filter_strainer_1' => 'nullable|in:OK,NOK',
                'clean_filter_strainer_2' => 'nullable|in:OK,NOK',
                'clean_filter_strainer_3' => 'nullable|in:OK,NOK',
                'clean_filter_strainer_4' => 'nullable|in:OK,NOK',
                'clean_filter_bebas_ahu' => 'nullable|in:OK,NOK',
                'inspeksi_h_ahu_1_4' => 'nullable|in:OK,NOK',
            ]);

            // Cek Duplikat di Details (jika tanggal berubah)
            if (
                $detail->tanggal->format('Y-m-d') !== $validated['tanggal'] &&
                AgendaAhuDetails::where('tanggal', $validated['tanggal'])->exists()
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
                'message' => 'Data agenda checklist AHU berhasil diperbarui.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Agenda AHU Error: ' . $e->getMessage());
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

        $main = AgendaAhu::where([
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
            Log::error('Notif Agenda AHU gagal: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Laporan bulanan berhasil disubmit untuk approval']);
    }

    public function getData(Request $request)
    {
        $query = AgendaAhuDetails::with(['agendaAhu', 'createdBy:id,username'])
            ->orderBy('tanggal', 'desc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month);
        }

        $data = $query->paginate($request->get('per_page', 15));

        $items = collect($data->items())->map(function ($item) {
            $item->approval_status = $item->agendaAhu ? $item->agendaAhu->status : 'none';
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

        $mainDrafts = AgendaAhu::whereIn('status', ['draft', 'rejected'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $result = [];
        foreach ($mainDrafts as $main) {
            $details = AgendaAhuDetails::where('agenda_ahu_id', $main->id)->get();
            if ($details->count() > 0) {
                $result[] = ['approval' => $main, 'data' => $details];
            }
        }
        return response()->json(['status' => 200, 'results' => $result]);
    }

    public function getApprovalData(Request $request)
    {
        $query = AgendaAhu::with(['operator', 'foreman', 'supervisor'])
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
        $data = AgendaAhu::findOrFail($id);

        if ($data->foreman_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data->update([
            'approved_foreman_at' => now(),
            'status' => 'approved_foreman'
        ]);

        NotificationsModel::where('notifiable_type', AgendaAhu::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        try {
            $this->sendNotification($data, $data->supervisor_id);
        } catch (\Exception $e) {
            Log::error('Notif Agenda AHU Supervisor gagal: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = AgendaAhu::findOrFail($id);
        if ($data->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data->update([
            'approved_supervisor_at' => now(),
            'status' => 'approved_supervisor'
        ]);

        NotificationsModel::where('notifiable_type', AgendaAhu::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Disetujui Supervisor']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);

        $data = AgendaAhu::findOrFail($id);

        $data->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        NotificationsModel::where('notifiable_type', AgendaAhu::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Ditolak']);
    }

    public function show($id)
    {
        $data = AgendaAhuDetails::with('createdBy')->find($id);
        if ($data) {
            $data->creator_name = $data->createdBy ? $data->createdBy->username : '-';
        }
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function showMonthlyDetails($id)
    {
        $main = AgendaAhu::with(['operator', 'foreman', 'supervisor'])->findOrFail($id);
        $details = AgendaAhuDetails::where('agenda_ahu_id', $id)->orderBy('tanggal', 'asc')->get();
        return response()->json(['status' => 200, 'header' => $main, 'details' => $details]);
    }

    public function destroy($id)
    {
        $detail = AgendaAhuDetails::findOrFail($id);
        $agendaAhuId = $detail->agenda_ahu_id;
        $detail->delete();

        // Check if there are any details left under this header
        $remainingDetails = AgendaAhuDetails::where('agenda_ahu_id', $agendaAhuId)->count();
        if ($remainingDetails == 0) {
            $m = AgendaAhu::find($agendaAhuId);
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
        $query = AgendaAhuDetails::with(['agendaAhu.operator', 'agendaAhu.foreman', 'agendaAhu.supervisor'])
            ->orderBy('tanggal', 'asc');

        if ($request->filled('bulan')) {
            $query->whereHas('agendaAhu', function ($q) use ($request) {
                $q->where('bulan', $request->bulan);
            });
        }
        if ($request->filled('tahun')) {
            $query->whereHas('agendaAhu', function ($q) use ($request) {
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

        $templatePath = public_path('assets/templates/operasional/agenda_ahu.xlsx');
        if (!file_exists($templatePath)) {
            return response()->json([
                'status' => 404,
                'message' => 'File template excel tidak ditemukan.'
            ], 404);
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);

        $tempFiles = [];
        $monthsData = $data->groupBy(function ($item) {
            return $item->agendaAhu->bulan;
        })->sortKeys();

        // USER MAPPING: row 7 is for first field, and rows 8 onwards for subsequent fields
        $rowMap = [
            'kelistrikan_ahu_1' => 7,
            'kelistrikan_ahu_2' => 8,
            'kelistrikan_ahu_3' => 9,
            'kelistrikan_ahu_4' => 10,
            'pressur_gauge_in_ahu_1' => 11,
            'pressur_gauge_in_ahu_2' => 12,
            'pressur_gauge_in_ahu_3' => 13,
            'pressur_gauge_in_ahu_4' => 14,
            'pressur_gauge_out_ahu_1' => 15,
            'pressur_gauge_out_ahu_2' => 16,
            'pressur_gauge_out_ahu_3' => 17,
            'pressur_gauge_out_ahu_4' => 18,
            'temp_gauge_in_ahu_1' => 19,
            'temp_gauge_in_ahu_2' => 20,
            'temp_gauge_in_ahu_3' => 21,
            'temp_gauge_in_ahu_4' => 22,
            'temp_gauge_out_ahu_1' => 23,
            'temp_gauge_out_ahu_2' => 24,
            'temp_gauge_out_ahu_3' => 25,
            'temp_gauge_out_ahu_4' => 26,
            'clean_filter_strainer_1' => 27,
            'clean_filter_strainer_2' => 28,
            'clean_filter_strainer_3' => 29,
            'clean_filter_strainer_4' => 30,
            'clean_filter_bebas_ahu' => 31,
            'inspeksi_h_ahu_1_4' => 32,
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

            // Write Month and Year to C5
            $yearStr = $request->tahun ?? date('Y');
            $sheet->setCellValue('C5', 'BULAN: ' . strtoupper($monthName) . ' - TAHUN: ' . $yearStr);

            foreach ($monthRecords as $item) {
                $day = (int)Carbon::parse($item->tanggal)->day;
                // C -> Day 1 (index 3), D -> Day 2 (index 4)...
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($day + 2);

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
            // USER REQUEST: Sticker at C35, O35, AA35. Username at A40, I40, V40. Time approved at A41, I41, V41.
            $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');
            $mainRecord = $monthRecords->first()->agendaAhu;

            if ($mainRecord) {
                $hasSticker = file_exists($signaturePath);

                // Operator/Submitter (C35 = Sticker, A40 = Username, A41 = Created At)
                if (in_array($mainRecord->status, ['draft', 'submitted', 'approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathOp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_ahu_op_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathOp);
                        $tempFiles[] = $tempPathOp;

                        $drawingOperator = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingOperator->setName('Submitted Operator ' . $monthNum);
                        $drawingOperator->setPath($tempPathOp);
                        $drawingOperator->setHeight(80);
                        $drawingOperator->setCoordinates('C35');
                        $drawingOperator->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('A40', $mainRecord->operator ? $mainRecord->operator->username : '-');
                    $sheet->setCellValue('A41', $mainRecord->created_at ? Carbon::parse($mainRecord->created_at)->format('d/m/Y H:i') : '-');
                }

                // Foreman (O35 = Sticker, I40 = Username, I41 = Approved Foreman At)
                if (in_array($mainRecord->status, ['approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathFm = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_ahu_fm_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathFm);
                        $tempFiles[] = $tempPathFm;

                        $drawingForeman = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingForeman->setName('Approved Foreman ' . $monthNum);
                        $drawingForeman->setPath($tempPathFm);
                        $drawingForeman->setHeight(80);
                        $drawingForeman->setCoordinates('O35');
                        $drawingForeman->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('I40', $mainRecord->foreman ? $mainRecord->foreman->username : '-');
                    $sheet->setCellValue('I41', $mainRecord->approved_foreman_at ? Carbon::parse($mainRecord->approved_foreman_at)->format('d/m/Y H:i') : '-');
                }

                // Supervisor (AA35 = Sticker, V40 = Username, V41 = Approved Supervisor At)
                if ($mainRecord->status == 'approved_supervisor') {
                    if ($hasSticker) {
                        $tempPathSpv = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_ahu_spv_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathSpv);
                        $tempFiles[] = $tempPathSpv;

                        $drawingSupervisor = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingSupervisor->setName('Approved Supervisor ' . $monthNum);
                        $drawingSupervisor->setPath($tempPathSpv);
                        $drawingSupervisor->setHeight(80);
                        $drawingSupervisor->setCoordinates('AA35');
                        $drawingSupervisor->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('V40', $mainRecord->supervisor ? $mainRecord->supervisor->username : '-');
                    $sheet->setCellValue('V41', $mainRecord->approved_supervisor_at ? Carbon::parse($mainRecord->approved_supervisor_at)->format('d/m/Y H:i') : '-');
                }
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Agenda_AHU_Report_' . ($request->bulan ? Carbon::create()->month((int)$request->bulan)->translatedFormat('F') : 'All') . '_' . ($request->tahun ?? date('Y')) . '.xlsx';

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
        $approvalUrl = url(route('agenda-ahu.approval', [], false));
        $targetUserId = $userId ?? $main->supervisor_id;

        if ($targetUserId) {
            NotificationsModel::updateOrCreate(
                [
                    'user_id' => $targetUserId,
                    'notifiable_type' => AgendaAhu::class,
                    'notifiable_id' => $main->id,
                    'is_read' => 0
                ],
                [
                    'title' => 'Approval Bulanan Agenda AHU',
                    'message' => "Laporan Agenda AHU Bulan {$main->bulan} {$main->tahun} menunggu persetujuan",
                    'url' => $approvalUrl
                ]
            );
        }
    }
}
