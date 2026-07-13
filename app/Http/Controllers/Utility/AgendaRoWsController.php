<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\AgendaRoWs;
use App\Models\Utility\AgendaRoWsDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgendaRoWsController extends Controller
{
    public function index()
    {
        return view('utility.agenda-ro-ws.form');
    }

    public function dataView()
    {
        return view('utility.agenda-ro-ws.data');
    }

    public function approvalView()
    {
        return view('utility.agenda-ro-ws.approval');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'inspeksi_hpt_pump' => 'nullable|in:OK,NOK',
                'inspeksi_cip_pump' => 'nullable|in:OK,NOK',
                'inspeksi_blower_ro' => 'nullable|in:OK,NOK',
                'cek_chemical' => 'nullable|in:OK,NOK',
                'pencatatan_flow_meter_produksi' => 'nullable|in:OK,NOK',
                'cek_nilai_conductivity' => 'nullable|in:OK,NOK',
                'cek_dp_1st_2st' => 'nullable|in:OK,NOK',
                'cek_dp_mmf_1_2' => 'nullable|in:OK,NOK',
                'pencatatan_flow_meter_konsumsi' => 'nullable|in:OK,NOK',
                'backwash_mmf_1' => 'nullable|in:OK,NOK',
                'backwash_mmf_2' => 'nullable|in:OK,NOK',
                'cek_kondisi_rotameter_mmf_1' => 'nullable|in:OK,NOK',
                'cek_kondisi_rotameter_mmf_2' => 'nullable|in:OK,NOK',
                'cek_kondisi_rotameter_ro_product' => 'nullable|in:OK,NOK',
                'cek_kondisi_rotameter_ro_reject' => 'nullable|in:OK,NOK',
                'kalibrasi_dosis_kimia' => 'nullable|in:OK,NOK',
                'cleaning_unit_ro' => 'nullable|in:OK,NOK',
                'cleaning_unit_mmf_1' => 'nullable|in:OK,NOK',
                'cleaning_unit_mmf_2' => 'nullable|in:OK,NOK',
                'cek_output_hardness' => 'nullable|in:OK,NOK',
                'cek_flow_produk' => 'nullable|in:OK,NOK',
                'regenerasi_mesin_ws' => 'nullable|in:OK,NOK',
                'cek_pompa_transfer' => 'nullable|in:OK,NOK',
                'cek_pompa_suplai' => 'nullable|in:OK,NOK',
                'cleaning_tanki_buffer_ws' => 'nullable|in:OK,NOK',
            ]);

            // Cek Duplikat di Details
            if (AgendaRoWsDetails::where('tanggal', $validated['tanggal'])->exists()) {
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
            $main = AgendaRoWs::firstOrCreate(
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
            if (in_array($main->status, ['approved_foreman', 'approved_supervisor'])) {
                $currentMonth = now()->month;
                $currentYear = now()->year;
                if ($month !== $currentMonth || $year !== $currentYear) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Laporan untuk bulan lalu sudah disetujui, data tidak dapat ditambah.'
                    ], 422);
                }
            }

            if (empty($main->operator_id)) {
                $main->update(['operator_id' => Auth::id()]);
            }

            // Extract keterangan
            $keterangan = [];
            foreach ($request->all() as $key => $val) {
                if (str_starts_with($key, 'keterangan_') && !empty($val)) {
                    $fieldName = substr($key, 11);
                    if ($request->input($fieldName) === 'NOK') {
                        $keterangan[$fieldName] = $val;
                    }
                }
            }
            $validated['keterangan'] = !empty($keterangan) ? $keterangan : null;

            $validated['agenda_ro_ws_id'] = $main->id;
            $validated['created_by'] = Auth::id();
            $detail = AgendaRoWsDetails::create($validated);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data agenda checklist berhasil disimpan.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Agenda RO-WS Error: ' . $e->getMessage());
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
            $detail = AgendaRoWsDetails::findOrFail($id);
            $main = $detail->agendaRoWs;

            $validated = $request->validate([
                'tanggal' => 'required|date',
                'inspeksi_hpt_pump' => 'nullable|in:OK,NOK',
                'inspeksi_cip_pump' => 'nullable|in:OK,NOK',
                'inspeksi_blower_ro' => 'nullable|in:OK,NOK',
                'cek_chemical' => 'nullable|in:OK,NOK',
                'pencatatan_flow_meter_produksi' => 'nullable|in:OK,NOK',
                'cek_nilai_conductivity' => 'nullable|in:OK,NOK',
                'cek_dp_1st_2st' => 'nullable|in:OK,NOK',
                'cek_dp_mmf_1_2' => 'nullable|in:OK,NOK',
                'pencatatan_flow_meter_konsumsi' => 'nullable|in:OK,NOK',
                'backwash_mmf_1' => 'nullable|in:OK,NOK',
                'backwash_mmf_2' => 'nullable|in:OK,NOK',
                'cek_kondisi_rotameter_mmf_1' => 'nullable|in:OK,NOK',
                'cek_kondisi_rotameter_mmf_2' => 'nullable|in:OK,NOK',
                'cek_kondisi_rotameter_ro_product' => 'nullable|in:OK,NOK',
                'cek_kondisi_rotameter_ro_reject' => 'nullable|in:OK,NOK',
                'kalibrasi_dosis_kimia' => 'nullable|in:OK,NOK',
                'cleaning_unit_ro' => 'nullable|in:OK,NOK',
                'cleaning_unit_mmf_1' => 'nullable|in:OK,NOK',
                'cleaning_unit_mmf_2' => 'nullable|in:OK,NOK',
                'cek_output_hardness' => 'nullable|in:OK,NOK',
                'cek_flow_produk' => 'nullable|in:OK,NOK',
                'regenerasi_mesin_ws' => 'nullable|in:OK,NOK',
                'cek_pompa_transfer' => 'nullable|in:OK,NOK',
                'cek_pompa_suplai' => 'nullable|in:OK,NOK',
                'cleaning_tanki_buffer_ws' => 'nullable|in:OK,NOK',
            ]);

            // Cek Duplikat di Details (jika tanggal berubah)
            if (
                $detail->tanggal->format('Y-m-d') !== $validated['tanggal'] &&
                AgendaRoWsDetails::where('tanggal', $validated['tanggal'])->exists()
            ) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Laporan untuk tanggal ' . $validated['tanggal'] . ' sudah ada'
                ], 422);
            }

            // Validasi status approval dan kunci bulan
            if ($main && in_array($main->status, ['approved_foreman', 'approved_supervisor'])) {
                $inputMonth = Carbon::parse($validated['tanggal'])->month;
                $inputYear = Carbon::parse($validated['tanggal'])->year;
                $currentMonth = now()->month;
                $currentYear = now()->year;
                if ($inputMonth !== $currentMonth || $inputYear !== $currentYear) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Laporan untuk bulan lalu sudah disetujui, data tidak dapat diubah.'
                    ], 422);
                }
            }

            // Extract keterangan
            $keterangan = [];
            foreach ($request->all() as $key => $val) {
                if (str_starts_with($key, 'keterangan_') && !empty($val)) {
                    $fieldName = substr($key, 11);
                    if ($request->input($fieldName) === 'NOK') {
                        $keterangan[$fieldName] = $val;
                    }
                }
            }
            $validated['keterangan'] = !empty($keterangan) ? $keterangan : null;

            $detail->update([
                ...$validated,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data agenda checklist berhasil diperbarui.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Agenda RO-WS Error: ' . $e->getMessage());
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

        $main = AgendaRoWs::where([
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
        $approvalUrl = url(route('agenda-ro-ws.approval', [], false));

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
                    'notifiable_type' => AgendaRoWs::class,
                    'notifiable_id' => $main->id,
                    'is_read' => 0,
                ],
                [
                    'title' => 'Approval Bulanan Agenda RO-WS',
                    'message' => "Laporan agenda RO-WS Bulan {$main->bulan} {$main->tahun} menunggu persetujuan",
                    'url' => $approvalUrl,
                ]
            );
        }
    }

    public function getData(Request $request)
    {
        $query = AgendaRoWsDetails::with('agendaRoWs')->orderBy('tanggal', 'desc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month);
        }

        $data = $query->paginate($request->get('per_page', 15));

        $items = collect($data->items())->map(function ($item) {
            $item->approval_status = $item->agendaRoWs ? $item->agendaRoWs->status : 'none';
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
        $mainDrafts = AgendaRoWs::whereIn('status', ['draft', 'rejected'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $result = [];
        foreach ($mainDrafts as $main) {
            $details = AgendaRoWsDetails::where('agenda_ro_ws_id', $main->id)->get();
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
        $query = AgendaRoWs::with(['operator', 'foreman', 'supervisor'])
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
        $data = AgendaRoWs::findOrFail($id);

        if ($data->foreman_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        $data->update([
            'approved_foreman_at' => now(),
            'status' => 'approved_foreman'
        ]);

        NotificationsModel::where('notifiable_type', AgendaRoWs::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Laporan disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = AgendaRoWs::findOrFail($id);

        if ($data->supervisor_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        $data->update([
            'approved_supervisor_at' => now(),
            'status' => 'approved_supervisor'
        ]);

        NotificationsModel::where('notifiable_type', AgendaRoWs::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Laporan disetujui Supervisor']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $data = AgendaRoWs::findOrFail($id);

        $data->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        NotificationsModel::where('notifiable_type', AgendaRoWs::class)
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
            $data = AgendaRoWs::find($id);
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
                NotificationsModel::where('notifiable_type', AgendaRoWs::class)
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
            $data = AgendaRoWs::find($id);
            if (!$data) continue;

            $isForeman = ($data->foreman_id === Auth::id() && $data->status === 'submitted');
            $isSupervisor = ($data->supervisor_id === Auth::id() && $data->status === 'approved_foreman');

            if ($isForeman || $isSupervisor) {
                $data->update([
                    'status' => 'rejected',
                    'reject_reason' => $request->reason
                ]);

                NotificationsModel::where('notifiable_type', AgendaRoWs::class)
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
        $data = AgendaRoWsDetails::with('createdBy')->find($id);
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function showMonthlyDetails($id)
    {
        $main = AgendaRoWs::with(['operator', 'foreman', 'supervisor'])->findOrFail($id);
        $details = AgendaRoWsDetails::where('agenda_ro_ws_id', $id)
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
        $data = AgendaRoWsDetails::findOrFail($id);
        $main = $data->agendaRoWs;

        // Validasi status approval dan kunci bulan
        if ($main && in_array($main->status, ['approved_foreman', 'approved_supervisor'])) {
            $inputMonth = Carbon::parse($data->tanggal)->month;
            $inputYear = Carbon::parse($data->tanggal)->year;
            $currentMonth = now()->month;
            $currentYear = now()->year;
            if ($inputMonth !== $currentMonth || $inputYear !== $currentYear) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Laporan untuk bulan lalu sudah disetujui, data tidak dapat dihapus.'
                ], 422);
            }
        }

        $agendaRoWsId = $data->agenda_ro_ws_id;
        $data->delete();

        // Cek apakah masih ada detail lain
        $remainingDetails = AgendaRoWsDetails::where('agenda_ro_ws_id', $agendaRoWsId)->count();
        if ($remainingDetails == 0) {
            $m = AgendaRoWs::find($agendaRoWsId);
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
        $query = AgendaRoWsDetails::with(['agendaRoWs.operator', 'agendaRoWs.foreman', 'agendaRoWs.supervisor'])
            ->orderBy('tanggal', 'asc');

        if ($request->filled('bulan')) {
            $query->whereHas('agendaRoWs', function ($q) use ($request) {
                $q->where('bulan', $request->bulan);
            });
        }
        if ($request->filled('tahun')) {
            $query->whereHas('agendaRoWs', function ($q) use ($request) {
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

        $templatePath = public_path('assets/templates/operasional/agenda_ro_ws.xlsx');
        if (!file_exists($templatePath)) {
            return response()->json([
                'status' => 404,
                'message' => 'File template excel tidak ditemukan.'
            ], 404);
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);

        $tempFiles = [];
        $monthsData = $data->groupBy(function ($item) {
            return $item->agendaRoWs->bulan;
        })->sortKeys();

        // Row Map for 25 fields
        $rowMap = [
            'inspeksi_hpt_pump' => 7,
            'inspeksi_cip_pump' => 8,
            'inspeksi_blower_ro' => 9,
            'cek_chemical' => 10,
            'pencatatan_flow_meter_produksi' => 11,
            'cek_nilai_conductivity' => 12,
            'cek_dp_1st_2st' => 13,
            'cek_dp_mmf_1_2' => 14,
            'pencatatan_flow_meter_konsumsi' => 15,
            'backwash_mmf_1' => 16,
            'backwash_mmf_2' => 17,
            'cek_kondisi_rotameter_mmf_1' => 18,
            'cek_kondisi_rotameter_mmf_2' => 19,
            'cek_kondisi_rotameter_ro_product' => 20,
            'cek_kondisi_rotameter_ro_reject' => 21,
            'kalibrasi_dosis_kimia' => 22,
            'cleaning_unit_ro' => 23,
            'cleaning_unit_mmf_1' => 24,
            'cleaning_unit_mmf_2' => 25,
            // Spacer row 26
            'cek_output_hardness' => 27,
            'cek_flow_produk' => 28,
            'regenerasi_mesin_ws' => 29,
            'cek_pompa_transfer' => 30,
            'cek_pompa_suplai' => 31,
            'cleaning_tanki_buffer_ws' => 32,
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

            // Write Month and Year to C2
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
            // stiker approved ada di C35, O35 dan AA35
            // username di A39, J39 dan U39
            // time approved ada di A40, J40 dan U40
            $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');
            $mainRecord = $monthRecords->first()->agendaRoWs;

            if ($mainRecord) {
                $hasSticker = file_exists($signaturePath);

                // Operator (C35 = Sticker, A39 = Username, A40 = Created At)
                if (in_array($mainRecord->status, ['draft', 'submitted', 'approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathOp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_op_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathOp);
                        $tempFiles[] = $tempPathOp;

                        $drawingOperator = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingOperator->setName('Submitted Operator ' . $monthNum);
                        $drawingOperator->setPath($tempPathOp);
                        $drawingOperator->setHeight(50);
                        $drawingOperator->setCoordinates('C35');
                        $drawingOperator->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('A39', $mainRecord->operator ? $mainRecord->operator->username : '-');
                    $sheet->setCellValue('A40', $mainRecord->created_at ? Carbon::parse($mainRecord->created_at)->format('d/m/Y H:i') : '-');
                }

                // Foreman (O35 = Sticker, J39 = Username, J40 = Approved Foreman At)
                if (in_array($mainRecord->status, ['approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathFm = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_fm_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathFm);
                        $tempFiles[] = $tempPathFm;

                        $drawingForeman = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingForeman->setName('Approved Foreman ' . $monthNum);
                        $drawingForeman->setPath($tempPathFm);
                        $drawingForeman->setHeight(50);
                        $drawingForeman->setCoordinates('O35');
                        $drawingForeman->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('J39', $mainRecord->foreman ? $mainRecord->foreman->username : '-');
                    $sheet->setCellValue('J40', $mainRecord->approved_foreman_at ? Carbon::parse($mainRecord->approved_foreman_at)->format('d/m/Y H:i') : '-');
                }

                // Supervisor (AA35 = Sticker, U39 = Username, U40 = Approved Supervisor At)
                if ($mainRecord->status == 'approved_supervisor') {
                    if ($hasSticker) {
                        $tempPathSpv = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ag_sig_spv_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathSpv);
                        $tempFiles[] = $tempPathSpv;

                        $drawingSupervisor = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingSupervisor->setName('Approved Supervisor ' . $monthNum);
                        $drawingSupervisor->setPath($tempPathSpv);
                        $drawingSupervisor->setHeight(50);
                        $drawingSupervisor->setCoordinates('AA35');
                        $drawingSupervisor->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('U39', $mainRecord->supervisor ? $mainRecord->supervisor->username : '-');
                    $sheet->setCellValue('U40', $mainRecord->approved_supervisor_at ? Carbon::parse($mainRecord->approved_supervisor_at)->format('d/m/Y H:i') : '-');
                }
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Agenda_RO_WS_Report_' . ($request->bulan ? Carbon::create()->month((int)$request->bulan)->translatedFormat('F') : 'All') . '_' . ($request->tahun ?? date('Y')) . '.xlsx';

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
