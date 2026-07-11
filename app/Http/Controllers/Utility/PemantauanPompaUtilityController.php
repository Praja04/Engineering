<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\PemantauanPompaUtility;
use App\Models\Utility\PemantauanPompaUtilityDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PemantauanPompaUtilityController extends Controller
{
    public function index()
    {
        return view('utility.pemantauan-pompa-utility.form');
    }

    public function dataView()
    {
        return view('utility.pemantauan-pompa-utility.data');
    }

    public function approvalView()
    {
        return view('utility.pemantauan-pompa-utility.approval');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'ampere_pompa_10p3' => 'nullable|in:OK,NOK',
                'ampere_pompa_10p3a' => 'nullable|in:OK,NOK',
                'ampere_pompa_10p4' => 'nullable|in:OK,NOK',
                'ampere_pompa_10p4a' => 'nullable|in:OK,NOK',
                'ampere_pompa_10p5b' => 'nullable|in:OK,NOK',
                'ampere_pompa_20p1' => 'nullable|in:OK,NOK',
                'ampere_pompa_20p1a' => 'nullable|in:OK,NOK',
                'ampere_pompa_20p2' => 'nullable|in:OK,NOK',
                'ampere_pompa_20p2a' => 'nullable|in:OK,NOK',
                'ampere_pompa_60p1' => 'nullable|in:OK,NOK',
                'ampere_pompa_60p2' => 'nullable|in:OK,NOK',
                'ampere_pompa_60p3' => 'nullable|in:OK,NOK',
                'ampere_pompa_hp_pump' => 'nullable|in:OK,NOK',
                'ampere_pompa_cip_pump' => 'nullable|in:OK,NOK',
                'ampere_pompa_tf_ws' => 'nullable|in:OK,NOK',
                'ampere_fan_1' => 'nullable|in:OK,NOK',
                'ampere_fan_2' => 'nullable|in:OK,NOK',
                'ampere_fan_3' => 'nullable|in:OK,NOK',
                'ampere_fan_4' => 'nullable|in:OK,NOK',
                'ampere_pompa_ct_10000p1' => 'nullable|in:OK,NOK',
                'ampere_pompa_ct_10000p2' => 'nullable|in:OK,NOK',
                'ampere_pompa_ct_10000p3' => 'nullable|in:OK,NOK',
            ]);

            // Cek Duplikat di Details
            if (PemantauanPompaUtilityDetails::where('tanggal', $validated['tanggal'])->exists()) {
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
            $main = PemantauanPompaUtility::firstOrCreate(
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

            $validated['pemantauan_pompa_utility_id'] = $main->id;
            $validated['created_by'] = Auth::id();
            $detail = PemantauanPompaUtilityDetails::create($validated);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data agenda checklist pompa utility berhasil disimpan.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Pemantauan Pompa Utility Error: ' . $e->getMessage());
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
            $detail = PemantauanPompaUtilityDetails::findOrFail($id);

            $validated = $request->validate([
                'tanggal' => 'required|date',
                'ampere_pompa_10p3' => 'nullable|in:OK,NOK',
                'ampere_pompa_10p3a' => 'nullable|in:OK,NOK',
                'ampere_pompa_10p4' => 'nullable|in:OK,NOK',
                'ampere_pompa_10p4a' => 'nullable|in:OK,NOK',
                'ampere_pompa_10p5b' => 'nullable|in:OK,NOK',
                'ampere_pompa_20p1' => 'nullable|in:OK,NOK',
                'ampere_pompa_20p1a' => 'nullable|in:OK,NOK',
                'ampere_pompa_20p2' => 'nullable|in:OK,NOK',
                'ampere_pompa_20p2a' => 'nullable|in:OK,NOK',
                'ampere_pompa_60p1' => 'nullable|in:OK,NOK',
                'ampere_pompa_60p2' => 'nullable|in:OK,NOK',
                'ampere_pompa_60p3' => 'nullable|in:OK,NOK',
                'ampere_pompa_hp_pump' => 'nullable|in:OK,NOK',
                'ampere_pompa_cip_pump' => 'nullable|in:OK,NOK',
                'ampere_pompa_tf_ws' => 'nullable|in:OK,NOK',
                'ampere_fan_1' => 'nullable|in:OK,NOK',
                'ampere_fan_2' => 'nullable|in:OK,NOK',
                'ampere_fan_3' => 'nullable|in:OK,NOK',
                'ampere_fan_4' => 'nullable|in:OK,NOK',
                'ampere_pompa_ct_10000p1' => 'nullable|in:OK,NOK',
                'ampere_pompa_ct_10000p2' => 'nullable|in:OK,NOK',
                'ampere_pompa_ct_10000p3' => 'nullable|in:OK,NOK',
            ]);

            // Cek Duplikat di Details (jika tanggal berubah)
            if (
                $detail->tanggal->format('Y-m-d') !== $validated['tanggal'] &&
                PemantauanPompaUtilityDetails::where('tanggal', $validated['tanggal'])->exists()
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
                'message' => 'Data agenda checklist pompa utility berhasil diperbarui.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Pemantauan Pompa Utility Error: ' . $e->getMessage());
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

        $main = PemantauanPompaUtility::where([
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
            Log::error('Notif Pemantauan Pompa Utility gagal: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Laporan bulanan berhasil disubmit untuk approval']);
    }

    public function getData(Request $request)
    {
        $query = PemantauanPompaUtilityDetails::with(['pemantauanPompaUtility', 'createdBy:id,username'])
            ->orderBy('tanggal', 'desc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month);
        }

        $data = $query->paginate($request->get('per_page', 15));

        $items = collect($data->items())->map(function ($item) {
            $item->approval_status = $item->pemantauanPompaUtility ? $item->pemantauanPompaUtility->status : 'none';
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

        $mainDrafts = PemantauanPompaUtility::whereIn('status', ['draft', 'rejected'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $result = [];
        foreach ($mainDrafts as $main) {
            $details = PemantauanPompaUtilityDetails::where('pemantauan_pompa_utility_id', $main->id)->get();
            if ($details->count() > 0) {
                $result[] = ['approval' => $main, 'data' => $details];
            }
        }
        return response()->json(['status' => 200, 'results' => $result]);
    }

    public function getApprovalData(Request $request)
    {
        $query = PemantauanPompaUtility::with(['operator', 'foreman', 'supervisor'])
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
        $data = PemantauanPompaUtility::findOrFail($id);

        if ($data->foreman_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data->update([
            'approved_foreman_at' => now(),
            'status' => 'approved_foreman'
        ]);

        NotificationsModel::where('notifiable_type', PemantauanPompaUtility::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        try {
            $this->sendNotification($data, $data->supervisor_id);
        } catch (\Exception $e) {
            Log::error('Notif Pemantauan Pompa Utility Supervisor gagal: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = PemantauanPompaUtility::findOrFail($id);
        if ($data->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data->update([
            'approved_supervisor_at' => now(),
            'status' => 'approved_supervisor'
        ]);

        NotificationsModel::where('notifiable_type', PemantauanPompaUtility::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Disetujui Supervisor']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);

        $data = PemantauanPompaUtility::findOrFail($id);

        $data->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        NotificationsModel::where('notifiable_type', PemantauanPompaUtility::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Ditolak']);
    }

    public function show($id)
    {
        $data = PemantauanPompaUtilityDetails::with('createdBy')->find($id);
        if ($data) {
            $data->creator_name = $data->createdBy ? $data->createdBy->username : '-';
        }
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function showMonthlyDetails($id)
    {
        $main = PemantauanPompaUtility::with(['operator', 'foreman', 'supervisor'])->findOrFail($id);
        $details = PemantauanPompaUtilityDetails::where('pemantauan_pompa_utility_id', $id)->orderBy('tanggal', 'asc')->get();
        return response()->json(['status' => 200, 'header' => $main, 'details' => $details]);
    }

    public function destroy($id)
    {
        $detail = PemantauanPompaUtilityDetails::findOrFail($id);
        $parentId = $detail->pemantauan_pompa_utility_id;
        $detail->delete();

        // Check if there are any details left under this header
        $remainingDetails = PemantauanPompaUtilityDetails::where('pemantauan_pompa_utility_id', $parentId)->count();
        if ($remainingDetails == 0) {
            $m = PemantauanPompaUtility::find($parentId);
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
        $query = PemantauanPompaUtilityDetails::with(['pemantauanPompaUtility.operator', 'pemantauanPompaUtility.foreman', 'pemantauanPompaUtility.supervisor'])
            ->orderBy('tanggal', 'asc');

        if ($request->filled('bulan')) {
            $query->whereHas('pemantauanPompaUtility', function ($q) use ($request) {
                $q->where('bulan', $request->bulan);
            });
        }
        if ($request->filled('tahun')) {
            $query->whereHas('pemantauanPompaUtility', function ($q) use ($request) {
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

        $templatePath = public_path('assets/templates/operasional/pemantauan_pompa_utility.xlsx');
        if (!file_exists($templatePath)) {
            return response()->json([
                'status' => 404,
                'message' => 'File template excel tidak ditemukan.'
            ], 404);
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);

        $tempFiles = [];
        $monthsData = $data->groupBy(function ($item) {
            return $item->pemantauanPompaUtility->bulan;
        })->sortKeys();

        // 22 Fields in sequential order (columns B to W)
        $fields = [
            'ampere_pompa_10p3',
            'ampere_pompa_10p3a',
            'ampere_pompa_10p4',
            'ampere_pompa_10p4a',
            'ampere_pompa_10p5b',
            'ampere_pompa_20p1',
            'ampere_pompa_20p1a',
            'ampere_pompa_20p2',
            'ampere_pompa_20p2a',
            'ampere_pompa_60p1',
            'ampere_pompa_60p2',
            'ampere_pompa_60p3',
            'ampere_pompa_hp_pump',
            'ampere_pompa_cip_pump',
            'ampere_pompa_tf_ws',
            'ampere_fan_1',
            'ampere_fan_2',
            'ampere_fan_3',
            'ampere_fan_4',
            'ampere_pompa_ct_10000p1',
            'ampere_pompa_ct_10000p2',
            'ampere_pompa_ct_10000p3',
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

            // Write Month and Year to U1 (User requirement: U1 Bulan: Tahun:)
            $yearStr = $request->tahun ?? date('Y');
            $sheet->setCellValue('U1', 'BULAN: ' . strtoupper($monthName) . ' TAHUN: ' . $yearStr);

            // User requirement: Row 8 is for Day 1, Row 9 for Day 2, etc.
            // Columns B, C, D, ... representing checklist fields.
            foreach ($monthRecords as $item) {
                $day = (int)Carbon::parse($item->tanggal)->day;
                $rowNum = 7 + $day; // Day 1 = Row 8

                foreach ($fields as $index => $field) {
                    // Column B starts at index 2
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(2 + $index);
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
            // USER REQUEST: Sticker at D40, I40, T40. Username at A45, G45, O45. Time approved at A46, G46, O46.
            $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');
            $mainRecord = $monthRecords->first()->pemantauanPompaUtility;

            if ($mainRecord) {
                $hasSticker = file_exists($signaturePath);

                // Operator/Submitter (D40 = Sticker, A45 = Username, A46 = Created At)
                if (in_array($mainRecord->status, ['draft', 'submitted', 'approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathOp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_pompa_op_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathOp);
                        $tempFiles[] = $tempPathOp;

                        $drawingOperator = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingOperator->setName('Submitted Operator ' . $monthNum);
                        $drawingOperator->setPath($tempPathOp);
                        $drawingOperator->setHeight(120);
                        $drawingOperator->setCoordinates('D40');
                        $drawingOperator->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('A45', $mainRecord->operator ? $mainRecord->operator->username : '-');
                    $sheet->setCellValue('A46', $mainRecord->created_at ? Carbon::parse($mainRecord->created_at)->format('d/m/Y H:i') : '-');
                }

                // Foreman (I40 = Sticker, G45 = Username, G46 = Approved Foreman At)
                if (in_array($mainRecord->status, ['approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathFm = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_pompa_fm_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathFm);
                        $tempFiles[] = $tempPathFm;

                        $drawingForeman = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingForeman->setName('Approved Foreman ' . $monthNum);
                        $drawingForeman->setPath($tempPathFm);
                        $drawingForeman->setHeight(120);
                        $drawingForeman->setCoordinates('I40');
                        $drawingForeman->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('G45', $mainRecord->foreman ? $mainRecord->foreman->username : '-');
                    $sheet->setCellValue('G46', $mainRecord->approved_foreman_at ? Carbon::parse($mainRecord->approved_foreman_at)->format('d/m/Y H:i') : '-');
                }

                // Supervisor (T40 = Sticker, O45 = Username, O46 = Approved Supervisor At)
                if ($mainRecord->status == 'approved_supervisor') {
                    if ($hasSticker) {
                        $tempPathSpv = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_pompa_spv_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathSpv);
                        $tempFiles[] = $tempPathSpv;

                        $drawingSupervisor = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingSupervisor->setName('Approved Supervisor ' . $monthNum);
                        $drawingSupervisor->setPath($tempPathSpv);
                        $drawingSupervisor->setHeight(120);
                        $drawingSupervisor->setCoordinates('T40');
                        $drawingSupervisor->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('O45', $mainRecord->supervisor ? $mainRecord->supervisor->username : '-');
                    $sheet->setCellValue('O46', $mainRecord->approved_supervisor_at ? Carbon::parse($mainRecord->approved_supervisor_at)->format('d/m/Y H:i') : '-');
                }
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Pemantauan_Pompa_Utility_Report_' . ($request->bulan ? Carbon::create()->month((int)$request->bulan)->translatedFormat('F') : 'All') . '_' . ($request->tahun ?? date('Y')) . '.xlsx';

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
        $approvalUrl = url(route('pemantauan-pompa-utility.approval', [], false));
        $targetUserId = $userId ?? $main->supervisor_id;

        if ($targetUserId) {
            NotificationsModel::updateOrCreate(
                [
                    'user_id' => $targetUserId,
                    'notifiable_type' => PemantauanPompaUtility::class,
                    'notifiable_id' => $main->id,
                    'is_read' => 0
                ],
                [
                    'title' => 'Approval Bulanan Pemantauan Pompa Utility',
                    'message' => "Laporan Pemantauan Pompa Utility Bulan {$main->bulan} {$main->tahun} menunggu persetujuan",
                    'url' => $approvalUrl
                ]
            );
        }
    }
}
