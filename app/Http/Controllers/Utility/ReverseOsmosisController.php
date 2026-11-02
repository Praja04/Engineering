<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\ReverseOsmosis;
use App\Models\Utility\ReverseOsmosisDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReverseOsmosisController extends Controller
{
    public function index()
    {
        return view('utility.reverse-osmosis.form');
    }

    public function dataView()
    {
        return view('utility.reverse-osmosis.data');
    }

    public function approvalView()
    {
        return view('utility.reverse-osmosis.approval');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'mmf_pressure_feed_1' => 'nullable|numeric',
                'mmf_pressure_feed_2' => 'nullable|numeric',
                'mmf_pressure_produk_1' => 'nullable|numeric',
                'mmf_pressure_produk_2' => 'nullable|numeric',
                'mmf_output_flow_1' => 'nullable|numeric',
                'mmf_output_flow_2' => 'nullable|numeric',
                'mmf_status_backwash_1' => 'nullable|boolean',
                'mmf_status_backwash_2' => 'nullable|boolean',
                'micron_filter_pressure_inlet' => 'nullable|numeric',
                'micron_filter_pressure_outlet' => 'nullable|numeric',
                'ro_permeate_flowrate' => 'nullable|numeric',
                'ro_reject_flowrate' => 'nullable|numeric',
                'ro_flowmeter_accumulation' => 'nullable|numeric',
                'ro_pressure_inlet_1st_stage' => 'nullable|numeric',
                'ro_pressure_inlet_2nd_stage' => 'nullable|numeric',
                'ro_pressure_concentrate' => 'nullable|numeric',
                'ro_pressure_produk' => 'nullable|numeric',
                'cip_keterangan' => 'nullable|string',
                'cip_jenis_chemical' => 'nullable|string',
                'cip_qty_chemical' => 'nullable|string',
                'cip_hasil' => 'nullable|string',
            ]);

            // Default booleans
            $validated['mmf_status_backwash_1'] = $request->has('mmf_status_backwash_1') ? 1 : 0;
            $validated['mmf_status_backwash_2'] = $request->has('mmf_status_backwash_2') ? 1 : 0;

            // Cek Duplikat di Details
            if (ReverseOsmosisDetails::where('tanggal', $validated['tanggal'])->exists()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Laporan untuk tanggal ' . $validated['tanggal'] . ' sudah ada'
                ], 422);
            }

            // Hitung Bulan dan Tahun
            $date = Carbon::parse($validated['tanggal']);
            $month = $date->month;
            $year = $date->year;

            // Find or create main record
            $main = ReverseOsmosis::firstOrCreate(
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

            // Validasi status approval dan kunci bulan
            // if (in_array($main->status, ['approved_foreman', 'approved_supervisor'])) {
            //     $currentMonth = now()->month;
            //     $currentYear = now()->year;
            //     if ($month !== $currentMonth || $year !== $currentYear) {
            //         return response()->json([
            //             'status' => 422,
            //             'message' => 'Laporan untuk bulan lalu sudah disetujui, data tidak dapat ditambah.'
            //         ], 422);
            //     }
            // }

            if (empty($main->operator_id)) {
                $main->update(['operator_id' => Auth::id()]);
            }

            $validated['reverse_osmosis_id'] = $main->id;
            $validated['created_by'] = Auth::id();
            $detail = ReverseOsmosisDetails::create($validated);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data reverse osmosis berhasil disimpan.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Reverse Osmosis Error: ' . $e->getMessage());
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
            $detail = ReverseOsmosisDetails::findOrFail($id);
            $main = $detail->reverseOsmosis;

            $validated = $request->validate([
                'tanggal' => 'required|date',
                'mmf_pressure_feed_1' => 'nullable|numeric',
                'mmf_pressure_feed_2' => 'nullable|numeric',
                'mmf_pressure_produk_1' => 'nullable|numeric',
                'mmf_pressure_produk_2' => 'nullable|numeric',
                'mmf_output_flow_1' => 'nullable|numeric',
                'mmf_output_flow_2' => 'nullable|numeric',
                'mmf_status_backwash_1' => 'nullable|boolean',
                'mmf_status_backwash_2' => 'nullable|boolean',
                'micron_filter_pressure_inlet' => 'nullable|numeric',
                'micron_filter_pressure_outlet' => 'nullable|numeric',
                'ro_permeate_flowrate' => 'nullable|numeric',
                'ro_reject_flowrate' => 'nullable|numeric',
                'ro_flowmeter_accumulation' => 'nullable|numeric',
                'ro_pressure_inlet_1st_stage' => 'nullable|numeric',
                'ro_pressure_inlet_2nd_stage' => 'nullable|numeric',
                'ro_pressure_concentrate' => 'nullable|numeric',
                'ro_pressure_produk' => 'nullable|numeric',
                'cip_keterangan' => 'nullable|string',
                'cip_jenis_chemical' => 'nullable|string',
                'cip_qty_chemical' => 'nullable|string',
                'cip_hasil' => 'nullable|string',
            ]);

            $validated['mmf_status_backwash_1'] = $request->has('mmf_status_backwash_1') ? 1 : 0;
            $validated['mmf_status_backwash_2'] = $request->has('mmf_status_backwash_2') ? 1 : 0;

            // Cek Duplikat di Details (jika tanggal berubah)
            if (
                $detail->tanggal->format('Y-m-d') !== $validated['tanggal'] &&
                ReverseOsmosisDetails::where('tanggal', $validated['tanggal'])->exists()
            ) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Laporan untuk tanggal ' . $validated['tanggal'] . ' sudah ada'
                ], 422);
            }

            // Validasi status approval dan kunci bulan
            // if ($main && in_array($main->status, ['approved_foreman', 'approved_supervisor'])) {
            //     $inputMonth = Carbon::parse($validated['tanggal'])->month;
            //     $inputYear = Carbon::parse($validated['tanggal'])->year;
            //     $currentMonth = now()->month;
            //     $currentYear = now()->year;
            //     if ($inputMonth !== $currentMonth || $inputYear !== $currentYear) {
            //         return response()->json([
            //             'status' => 422,
            //             'message' => 'Laporan untuk bulan lalu sudah disetujui, data tidak dapat diubah.'
            //         ], 422);
            //     }
            // }

            $detail->update([
                ...$validated,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data reverse osmosis berhasil diperbarui.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Reverse Osmosis Error: ' . $e->getMessage());
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

        $main = ReverseOsmosis::where([
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
            Log::error('Notif gagal: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Laporan bulanan berhasil disubmit untuk approval']);
    }

    private function sendNotification($main)
    {
        $approvalUrl = url(route('reverse-osmosis.approval', [], false));

        $recipients = [];
        if ($main->status === 'submitted' && $main->foreman_id) {
            $recipients[] = $main->foreman_id;
        }
        if ($main->status === 'approved_foreman' && $main->supervisor_id) {
            $recipients[] = $main->supervisor_id;
        }

        $users = User::whereIn('id', array_filter($recipients))->get();

        foreach ($users as $user) {
            NotificationsModel::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notifiable_type' => ReverseOsmosis::class,
                    'notifiable_id' => $main->id,
                    'is_read' => 0,
                ],
                [
                    'title' => 'Approval Bulanan Reverse Osmosis',
                    'message' => "Laporan reverse osmosis Bulan {$main->bulan} {$main->tahun} menunggu persetujuan",
                    'url' => $approvalUrl,
                ]
            );
        }
    }

    public function getData(Request $request)
    {
        $query = ReverseOsmosisDetails::with('reverseOsmosis')->orderBy('tanggal', 'desc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month);
        }

        $data = $query->paginate($request->get('per_page', 15));

        $items = collect($data->items())->map(function ($item) {
            $item->approval_status = $item->reverseOsmosis ? $item->reverseOsmosis->status : 'none';
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
        $mainDrafts = ReverseOsmosis::whereIn('status', ['draft', 'rejected'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $result = [];
        foreach ($mainDrafts as $main) {
            $details = ReverseOsmosisDetails::where('reverse_osmosis_id', $main->id)->get();
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
        $query = ReverseOsmosis::with(['operator', 'foreman', 'supervisor'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc');

        if ($request->mode === 'approval') {
            $query->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where('foreman_id', Auth::id())
                        ->where('status', 'submitted');
                })->orWhere(function ($sq) {
                    $sq->where('supervisor_id', Auth::id())
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
        $data = ReverseOsmosis::findOrFail($id);

        if ($data->foreman_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        $data->update([
            'approved_foreman_at' => now(),
            'status' => 'approved_foreman'
        ]);

        NotificationsModel::where('notifiable_type', ReverseOsmosis::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Laporan disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = ReverseOsmosis::findOrFail($id);

        if ($data->supervisor_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        $data->update([
            'approved_supervisor_at' => now(),
            'status' => 'approved_supervisor'
        ]);

        NotificationsModel::where('notifiable_type', ReverseOsmosis::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Laporan disetujui Supervisor']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $data = ReverseOsmosis::findOrFail($id);

        $data->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        NotificationsModel::where('notifiable_type', ReverseOsmosis::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Laporan ditolak']);
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['message' => 'Tidak ada data yang dipilih'], 422);
        }

        $successCount = 0;
        foreach ($ids as $id) {
            $data = ReverseOsmosis::find($id);
            if (!$data) continue;

            $updated = false;
            if ($data->foreman_id === Auth::id() && $data->status === 'submitted') {
                $data->update([
                    'approved_foreman_at' => now(),
                    'status' => 'approved_foreman'
                ]);
                $updated = true;
            } elseif ($data->supervisor_id === Auth::id() && $data->status === 'approved_foreman') {
                $data->update([
                    'approved_supervisor_at' => now(),
                    'status' => 'approved_supervisor'
                ]);
                $updated = true;
            }

            if ($updated) {
                NotificationsModel::where('notifiable_type', ReverseOsmosis::class)
                    ->where('notifiable_id', $data->id)
                    ->where('user_id', Auth::id())
                    ->delete();
                $successCount++;
            }
        }

        return response()->json([
            'status' => 200,
            'message' => $successCount . ' laporan berhasil disetujui secara massal.'
        ]);
    }

    public function bulkReject(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'reason' => 'required|string|max:255'
        ]);

        $successCount = 0;
        foreach ($request->ids as $id) {
            $data = ReverseOsmosis::find($id);
            if (!$data) continue;

            $isForeman = ($data->foreman_id === Auth::id() && $data->status === 'submitted');
            $isSupervisor = ($data->supervisor_id === Auth::id() && $data->status === 'approved_foreman');

            if ($isForeman || $isSupervisor) {
                $data->update([
                    'status' => 'rejected',
                    'reject_reason' => $request->reason
                ]);

                NotificationsModel::where('notifiable_type', ReverseOsmosis::class)
                    ->where('notifiable_id', $data->id)
                    ->where('user_id', Auth::id())
                    ->delete();

                $successCount++;
            }
        }

        return response()->json([
            'status' => 200,
            'message' => $successCount . ' laporan berhasil ditolak secara massal.'
        ]);
    }

    public function show($id)
    {
        $data = ReverseOsmosisDetails::with('createdBy')->find($id);
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function showMonthlyDetails($id)
    {
        $main = ReverseOsmosis::with(['operator', 'foreman', 'supervisor'])->findOrFail($id);
        $details = ReverseOsmosisDetails::where('reverse_osmosis_id', $id)
            ->orderBy('tanggal', 'asc')
            ->get();

        return response()->json([
            'status' => 200,
            'header' => $main,
            'details' => $details
        ]);
    }

    public function destroy($id)
    {
        $data = ReverseOsmosisDetails::findOrFail($id);
        $main = $data->reverseOsmosis;

        // Validasi status approval dan kunci bulan
        // if ($main && in_array($main->status, ['approved_foreman', 'approved_supervisor'])) {
        //     $inputMonth = Carbon::parse($data->tanggal)->month;
        //     $inputYear = Carbon::parse($data->tanggal)->year;
        //     $currentMonth = now()->month;
        //     $currentYear = now()->year;
        //     if ($inputMonth !== $currentMonth || $inputYear !== $currentYear) {
        //         return response()->json([
        //             'status' => 422,
        //             'message' => 'Laporan untuk bulan lalu sudah disetujui, data tidak dapat dihapus.'
        //         ], 422);
        //     }
        // }

        $reverseOsmosisId = $data->reverse_osmosis_id;
        $data->delete();

        // Cek apakah masih ada detail lain
        $remainingDetails = ReverseOsmosisDetails::where('reverse_osmosis_id', $reverseOsmosisId)->count();
        if ($remainingDetails == 0) {
            $m = ReverseOsmosis::find($reverseOsmosisId);
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
        $query = ReverseOsmosisDetails::with(['reverseOsmosis.operator', 'reverseOsmosis.foreman', 'reverseOsmosis.supervisor'])
            ->orderBy('tanggal', 'asc');

        if ($request->filled('bulan')) {
            $query->whereHas('reverseOsmosis', function ($q) use ($request) {
                $q->where('bulan', $request->bulan);
            });
        }
        if ($request->filled('tahun')) {
            $query->whereHas('reverseOsmosis', function ($q) use ($request) {
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

        $templatePath = public_path('assets/templates/operasional/reverse_osmosis.xlsx');
        if (!file_exists($templatePath)) {
            return response()->json([
                'status' => 404,
                'message' => 'File template excel tidak ditemukan.'
            ], 404);
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);

        $tempFiles = [];
        $monthsData = $data->groupBy(function ($item) {
            return $item->reverseOsmosis->bulan;
        })->sortKeys();

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

            // U1 -> BULAN - TAHUN (User: "U untuk bulan dan tahun")
            $yearStr = $request->tahun ?? date('Y');
            $sheet->setCellValue('U1', 'BULAN: ' . strtoupper($monthName) . ' - ' . $yearStr);

            foreach ($monthRecords as $item) {
                $day = (int)Carbon::parse($item->tanggal)->day;
                $rowNumber = 6 + ($day - 1); // Row 6 for day 1, Row 7 for day 2...

                // MMF (B - I)
                $sheet->setCellValue('B' . $rowNumber, $item->mmf_pressure_feed_1);
                $sheet->setCellValue('C' . $rowNumber, $item->mmf_pressure_feed_2);
                $sheet->setCellValue('D' . $rowNumber, $item->mmf_pressure_produk_1);
                $sheet->setCellValue('E' . $rowNumber, $item->mmf_pressure_produk_2);
                $sheet->setCellValue('F' . $rowNumber, $item->mmf_output_flow_1);
                $sheet->setCellValue('G' . $rowNumber, $item->mmf_output_flow_2);
                $sheet->setCellValue('H' . $rowNumber, $item->mmf_status_backwash_1 ? '✓' : '');
                $sheet->setCellValue('I' . $rowNumber, $item->mmf_status_backwash_2 ? '✓' : '');

                // Micron Filter (J - K)
                $sheet->setCellValue('J' . $rowNumber, $item->micron_filter_pressure_inlet);
                $sheet->setCellValue('K' . $rowNumber, $item->micron_filter_pressure_outlet);

                // RO (L - S)
                $sheet->setCellValue('L' . $rowNumber, $item->ro_permeate_flowrate);
                $sheet->setCellValue('M' . $rowNumber, $item->ro_reject_flowrate);
                $sheet->setCellValue('N' . $rowNumber, $item->ro_flowmeter_accumulation);
                $sheet->setCellValue('O' . $rowNumber, $item->ro_pressure_inlet_1st_stage);
                $sheet->setCellValue('P' . $rowNumber, $item->ro_pressure_inlet_2nd_stage);
                $sheet->setCellValue('Q' . $rowNumber, $item->ro_pressure_concentrate);
                $sheet->setCellValue('R' . $rowNumber, $item->ro_pressure_produk);

                // CIP (T - W)
                $sheet->setCellValue('T' . $rowNumber, $item->cip_keterangan);
                $sheet->setCellValue('U' . $rowNumber, $item->cip_jenis_chemical);
                $sheet->setCellValue('V' . $rowNumber, $item->cip_qty_chemical);
                $sheet->setCellValue('W' . $rowNumber, $item->cip_hasil);
            }

            // TTD / Approval Section
            // approval stiker di E34, L34, T34
            // username di A37, J37, Q37
            // timestamp approved di A38, J38 dan Q38
            $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');
            $mainRecord = $monthRecords->first()->reverseOsmosis;

            if ($mainRecord) {
                $hasSticker = file_exists($signaturePath);

                // Operator (E34 = Sticker, A37 = Username, A38 = Created At)
                if (in_array($mainRecord->status, ['draft', 'submitted', 'approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathOp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ro_sig_op_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathOp);
                        $tempFiles[] = $tempPathOp;

                        $drawingOperator = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingOperator->setName('Submitted Operator ' . $monthNum);
                        $drawingOperator->setPath($tempPathOp);
                        $drawingOperator->setHeight(50);
                        $drawingOperator->setCoordinates('E34');
                        $drawingOperator->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('A37', $mainRecord->operator ? $mainRecord->operator->username : '-');
                    $sheet->setCellValue('A38', $mainRecord->created_at ? Carbon::parse($mainRecord->created_at)->format('d/m/Y H:i') : '-');
                }

                // Foreman (L34 = Sticker, J37 = Username, J38 = Approved Foreman At)
                if (in_array($mainRecord->status, ['approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathFm = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ro_sig_fm_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathFm);
                        $tempFiles[] = $tempPathFm;

                        $drawingForeman = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingForeman->setName('Approved Foreman ' . $monthNum);
                        $drawingForeman->setPath($tempPathFm);
                        $drawingForeman->setHeight(50);
                        $drawingForeman->setCoordinates('L34');
                        $drawingForeman->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('J37', $mainRecord->foreman ? $mainRecord->foreman->username : '-');
                    $sheet->setCellValue('J38', $mainRecord->approved_foreman_at ? Carbon::parse($mainRecord->approved_foreman_at)->format('d/m/Y H:i') : '-');
                }

                // Supervisor (T34 = Sticker, Q37 = Username, Q38 = Approved Supervisor At)
                if ($mainRecord->status == 'approved_supervisor') {
                    if ($hasSticker) {
                        $tempPathSpv = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ro_sig_spv_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathSpv);
                        $tempFiles[] = $tempPathSpv;

                        $drawingSupervisor = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingSupervisor->setName('Approved Supervisor ' . $monthNum);
                        $drawingSupervisor->setPath($tempPathSpv);
                        $drawingSupervisor->setHeight(50);
                        $drawingSupervisor->setCoordinates('T34');
                        $drawingSupervisor->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('Q37', $mainRecord->supervisor ? $mainRecord->supervisor->username : '-');
                    $sheet->setCellValue('Q38', $mainRecord->approved_supervisor_at ? Carbon::parse($mainRecord->approved_supervisor_at)->format('d/m/Y H:i') : '-');
                }
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Reverse_Osmosis_Report_' . ($request->bulan ? Carbon::create()->month((int)$request->bulan)->translatedFormat('F') : 'All') . '_' . ($request->tahun ?? date('Y')) . '.xlsx';

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
}
