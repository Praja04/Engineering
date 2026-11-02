<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\CoolingTower;
use App\Models\Utility\CoolingTowerDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoolingTowerController extends Controller
{
    public function index()
    {
        return view('utility.cooling-tower.form');
    }

    public function dataView()
    {
        return view('utility.cooling-tower.data');
    }

    public function approvalView()
    {
        return view('utility.cooling-tower.approval');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'jam' => 'required|in:08:00,12:00,16:00,20:00,00:00,04:00',
                'pressure_ct_in' => 'nullable|numeric',
                'pressure_ct_out' => 'nullable|numeric',
                'temp_ct_in' => 'nullable|numeric',
                'temp_ct_out' => 'nullable|numeric',
                'flowrate_ro_awal' => 'nullable|numeric',
                'flowrate_ro_akhir' => 'nullable|numeric',
            ]);

            // Cek Duplikat di Details
            if (CoolingTowerDetails::where('tanggal', $validated['tanggal'])
                ->where('jam', $validated['jam'])
                ->exists()
            ) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Laporan untuk tanggal ' . $validated['tanggal'] . ' dan jam ' . $validated['jam'] . ' sudah ada'
                ], 422);
            }

            // Hitung Bulan dan Tahun
            $date = Carbon::parse($validated['tanggal']);
            $month = $date->month;
            $year = $date->year;

            // Find or create main record
            $main = CoolingTower::firstOrCreate(
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

            // flowrate disimpan langsung di details (sekali per hari)
            $validated['cooling_tower_id'] = $main->id;
            $validated['created_by'] = Auth::id();

            $detail = CoolingTowerDetails::create($validated);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data cooling tower berhasil disimpan.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Cooling Tower Error: ' . $e->getMessage());
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
            $detail = CoolingTowerDetails::findOrFail($id);
            $main = $detail->coolingTower;

            $validated = $request->validate([
                'tanggal' => 'required|date',
                'jam' => 'required|in:08:00,12:00,16:00,20:00,00:00,04:00',
                'pressure_ct_in' => 'nullable|numeric',
                'pressure_ct_out' => 'nullable|numeric',
                'temp_ct_in' => 'nullable|numeric',
                'temp_ct_out' => 'nullable|numeric',
                'flowrate_ro_awal' => 'nullable|numeric',
                'flowrate_ro_akhir' => 'nullable|numeric',
            ]);

            // Cek Duplikat di Details (jika tanggal/jam berubah)
            $formattedJam = $detail->jam instanceof Carbon ? $detail->jam->format('H:i') : substr($detail->jam, 0, 5);
            if (($detail->tanggal->format('Y-m-d') !== $validated['tanggal'] || $formattedJam !== $validated['jam']) &&
                CoolingTowerDetails::where('tanggal', $validated['tanggal'])
                ->where('jam', $validated['jam'])
                ->exists()
            ) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Laporan untuk tanggal ' . $validated['tanggal'] . ' dan jam ' . $validated['jam'] . ' sudah ada'
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

            // flowrate langsung di-update di details row
            $detail->update([
                ...$validated,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data cooling tower berhasil diperbarui.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Cooling Tower Error: ' . $e->getMessage());
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

        $main = CoolingTower::where([
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
        $approvalUrl = url(route('cooling-tower.approval', [], false));

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
                    'notifiable_type' => CoolingTower::class,
                    'notifiable_id' => $main->id,
                    'is_read' => 0,
                ],
                [
                    'title' => 'Approval Bulanan Cooling Tower',
                    'message' => "Laporan cooling tower Bulan {$main->bulan} {$main->tahun} menunggu persetujuan",
                    'url' => $approvalUrl,
                ]
            );
        }
    }

    public function getData(Request $request)
    {
        $query = CoolingTowerDetails::with('coolingTower')->orderBy('tanggal', 'desc')->orderBy('jam', 'desc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month);
        }

        $data = $query->paginate($request->get('per_page', 15));

        $items = collect($data->items())->map(function ($item) {
            $item->approval_status = $item->coolingTower ? $item->coolingTower->status : 'none';
            // flowrate now comes directly from the details row
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

    /**
     * Check if flowrate fields have already been filled for the given date (once per day).
     * Looks in cooling_tower_details for any row on that date that has flowrate set.
     */
    public function checkFlowrate(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $tanggal = $request->tanggal;

        // Find any detail on this date that already has flowrate_ro_awal filled
        $awalRow = CoolingTowerDetails::where('tanggal', $tanggal)
            ->whereNotNull('flowrate_ro_awal')
            ->first();

        // Find any detail on this date that already has flowrate_ro_akhir filled
        $akhirRow = CoolingTowerDetails::where('tanggal', $tanggal)
            ->whereNotNull('flowrate_ro_akhir')
            ->first();

        return response()->json([
            'status'                  => 200,
            'flowrate_ro_awal_filled'  => $awalRow !== null,
            'flowrate_ro_awal'         => $awalRow ? $awalRow->flowrate_ro_awal : null,
            'flowrate_ro_akhir_filled' => $akhirRow !== null,
            'flowrate_ro_akhir'        => $akhirRow ? $akhirRow->flowrate_ro_akhir : null,
        ]);
    }

    public function getCollectedData()
    {
        $mainDrafts = CoolingTower::whereIn('status', ['draft', 'rejected'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $result = [];
        foreach ($mainDrafts as $main) {
            $details = CoolingTowerDetails::where('cooling_tower_id', $main->id)->get();
            if ($details->count() > 0) {
                // flowrate is already on each detail row — no extra mapping needed
                $result[] = [
                    'approval' => $main,
                    'data'     => $details
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
        $query = CoolingTower::with(['operator', 'foreman', 'supervisor'])
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
        $data = CoolingTower::findOrFail($id);

        if ($data->foreman_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        $data->update([
            'approved_foreman_at' => now(),
            'status' => 'approved_foreman'
        ]);

        NotificationsModel::where('notifiable_type', CoolingTower::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Laporan disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = CoolingTower::findOrFail($id);

        if ($data->supervisor_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        $data->update([
            'approved_supervisor_at' => now(),
            'status' => 'approved_supervisor'
        ]);

        NotificationsModel::where('notifiable_type', CoolingTower::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Laporan disetujui Supervisor']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $data = CoolingTower::findOrFail($id);

        $data->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        NotificationsModel::where('notifiable_type', CoolingTower::class)
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
            $data = CoolingTower::find($id);
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
                NotificationsModel::where('notifiable_type', CoolingTower::class)
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
            $data = CoolingTower::find($id);
            if (!$data) continue;

            $isForeman = ($data->foreman_id === Auth::id() && $data->status === 'submitted');
            $isSupervisor = ($data->supervisor_id === Auth::id() && $data->status === 'approved_foreman');

            if ($isForeman || $isSupervisor) {
                $data->update([
                    'status' => 'rejected',
                    'reject_reason' => $request->reason
                ]);

                NotificationsModel::where('notifiable_type', CoolingTower::class)
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
        // flowrate_ro_awal & flowrate_ro_akhir are columns on CoolingTowerDetails — no extra mapping needed
        $data = CoolingTowerDetails::with(['createdBy', 'coolingTower'])->find($id);
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function showMonthlyDetails($id)
    {
        $main = CoolingTower::with(['operator', 'foreman', 'supervisor'])->findOrFail($id);
        // flowrate is already on each detail row — no extra mapping needed
        $details = CoolingTowerDetails::where('cooling_tower_id', $id)
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam', 'asc')
            ->get();

        return response()->json([
            'status'  => 200,
            'header'  => $main,
            'details' => $details
        ]);
    }

    public function destroy($id)
    {
        $data = CoolingTowerDetails::findOrFail($id);
        $main = $data->coolingTower;

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

        $coolingTowerId = $data->cooling_tower_id;
        $data->delete();

        // Cek apakah masih ada detail lain
        $remainingDetails = CoolingTowerDetails::where('cooling_tower_id', $coolingTowerId)->count();
        if ($remainingDetails == 0) {
            $m = CoolingTower::find($coolingTowerId);
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
        $query = CoolingTowerDetails::with(['coolingTower.operator', 'coolingTower.foreman', 'coolingTower.supervisor'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam', 'asc');

        if ($request->filled('bulan')) {
            $query->whereHas('coolingTower', function ($q) use ($request) {
                $q->where('bulan', $request->bulan);
            });
        }
        if ($request->filled('tahun')) {
            $query->whereHas('coolingTower', function ($q) use ($request) {
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

        $templatePath = public_path('assets/templates/operasional/cooling_tower.xlsx');
        if (!file_exists($templatePath)) {
            return response()->json([
                'status' => 404,
                'message' => 'File template excel tidak ditemukan.'
            ], 404);
        }
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);

        $tempFiles = [];
        $monthsData = $data->groupBy(function ($item) {
            return $item->coolingTower->bulan;
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

            // Y1 -> BULAN - TAHUN
            $yearStr = $request->tahun ?? date('Y');
            $sheet->setCellValue('Y1', 'BULAN: ' . strtoupper($monthName) . ' - ' . $yearStr);

            // Group data by date
            $grouped = $monthRecords->groupBy(function ($item) {
                return $item->tanggal->format('Y-m-d');
            });

            foreach ($grouped as $tanggal => $dayRecords) {
                $day = (int)Carbon::parse($tanggal)->day;
                $rowNumber = 7 + ($day - 1); // Row 7 for day 1, Row 8 for day 2...

                // Write flowrate for this day (once per day — take any entry that has it)
                $awalDetail  = $dayRecords->firstWhere(fn($r) => $r->flowrate_ro_awal !== null);
                $akhirDetail = $dayRecords->firstWhere(fn($r) => $r->flowrate_ro_akhir !== null);
                if ($awalDetail)  $sheet->setCellValue('Z'  . $rowNumber, $awalDetail->flowrate_ro_awal);
                if ($akhirDetail) $sheet->setCellValue('AA' . $rowNumber, $akhirDetail->flowrate_ro_akhir);

                foreach ($dayRecords as $item) {
                    $jamKey = $item->jam instanceof Carbon ? $item->jam->format('H:i') : substr($item->jam, 0, 5); // "08:00"

                    // Map pressure CT in and out (B - M)
                    if ($jamKey === '08:00') {
                        $sheet->setCellValue('B' . $rowNumber, $item->pressure_ct_in);
                        $sheet->setCellValue('C' . $rowNumber, $item->pressure_ct_out);
                        $sheet->setCellValue('N' . $rowNumber, $item->temp_ct_in);
                        $sheet->setCellValue('O' . $rowNumber, $item->temp_ct_out);
                    } elseif ($jamKey === '12:00') {
                        $sheet->setCellValue('D' . $rowNumber, $item->pressure_ct_in);
                        $sheet->setCellValue('E' . $rowNumber, $item->pressure_ct_out);
                        $sheet->setCellValue('P' . $rowNumber, $item->temp_ct_in);
                        $sheet->setCellValue('Q' . $rowNumber, $item->temp_ct_out);
                    } elseif ($jamKey === '16:00') {
                        $sheet->setCellValue('F' . $rowNumber, $item->pressure_ct_in);
                        $sheet->setCellValue('G' . $rowNumber, $item->pressure_ct_out);
                        $sheet->setCellValue('R' . $rowNumber, $item->temp_ct_in);
                        $sheet->setCellValue('S' . $rowNumber, $item->temp_ct_out);
                    } elseif ($jamKey === '20:00') {
                        $sheet->setCellValue('H' . $rowNumber, $item->pressure_ct_in);
                        $sheet->setCellValue('I' . $rowNumber, $item->pressure_ct_out);
                        $sheet->setCellValue('T' . $rowNumber, $item->temp_ct_in);
                        $sheet->setCellValue('U' . $rowNumber, $item->temp_ct_out);
                    } elseif ($jamKey === '00:00') {
                        $sheet->setCellValue('J' . $rowNumber, $item->pressure_ct_in);
                        $sheet->setCellValue('K' . $rowNumber, $item->pressure_ct_out);
                        $sheet->setCellValue('V' . $rowNumber, $item->temp_ct_in);
                        $sheet->setCellValue('W' . $rowNumber, $item->temp_ct_out);
                    } elseif ($jamKey === '04:00') {
                        $sheet->setCellValue('L' . $rowNumber, $item->pressure_ct_in);
                        $sheet->setCellValue('M' . $rowNumber, $item->pressure_ct_out);
                        $sheet->setCellValue('X' . $rowNumber, $item->temp_ct_in);
                        $sheet->setCellValue('Y' . $rowNumber, $item->temp_ct_out);
                    }
                }
            }

            $mainRecord  = $monthRecords->first()->coolingTower;
            $totalDays   = Carbon::create($yearStr, $monthNum)->daysInMonth;

            // TTD / Approval Section
            $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');

            if ($mainRecord) {
                $hasSticker = file_exists($signaturePath);

                // Operator (E42 = Sticker, A44 = Username, A45 = Created At)
                if (in_array($mainRecord->status, ['draft', 'submitted', 'approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathOp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ct_sig_op_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathOp);
                        $tempFiles[] = $tempPathOp;

                        $drawingOperator = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingOperator->setName('Submitted Operator ' . $monthNum);
                        $drawingOperator->setPath($tempPathOp);
                        $drawingOperator->setHeight(60);
                        $drawingOperator->setCoordinates('E41');
                        $drawingOperator->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('A44', $mainRecord->operator ? $mainRecord->operator->username : '-');
                    $sheet->setCellValue('A45', $mainRecord->created_at ? Carbon::parse($mainRecord->created_at)->format('d/m/Y H:i') : '-');
                }

                // Foreman (N42 = Sticker, J44 = Username, J45 = Approved Foreman At)
                if (in_array($mainRecord->status, ['approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathFm = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ct_sig_fm_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathFm);
                        $tempFiles[] = $tempPathFm;

                        $drawingForeman = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingForeman->setName('Approved Foreman ' . $monthNum);
                        $drawingForeman->setPath($tempPathFm);
                        $drawingForeman->setHeight(60);
                        $drawingForeman->setCoordinates('N41');
                        $drawingForeman->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('J44', $mainRecord->foreman ? $mainRecord->foreman->username : '-');
                    $sheet->setCellValue('J45', $mainRecord->approved_foreman_at ? Carbon::parse($mainRecord->approved_foreman_at)->format('d/m/Y H:i') : '-');
                }

                // Supervisor (W42 = Sticker, S44 = Username, S45 = Approved Supervisor At)
                if ($mainRecord->status == 'approved_supervisor') {
                    if ($hasSticker) {
                        $tempPathSpv = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ct_sig_spv_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathSpv);
                        $tempFiles[] = $tempPathSpv;

                        $drawingSupervisor = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingSupervisor->setName('Approved Supervisor ' . $monthNum);
                        $drawingSupervisor->setPath($tempPathSpv);
                        $drawingSupervisor->setHeight(60);
                        $drawingSupervisor->setCoordinates('W41');
                        $drawingSupervisor->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('S44', $mainRecord->supervisor ? $mainRecord->supervisor->username : '-');
                    $sheet->setCellValue('S45', $mainRecord->approved_supervisor_at ? Carbon::parse($mainRecord->approved_supervisor_at)->format('d/m/Y H:i') : '-');
                }
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Cooling_Tower_Report_' . ($request->bulan ? Carbon::create()->month((int)$request->bulan)->translatedFormat('F') : 'All') . '_' . ($request->tahun ?? date('Y')) . '.xlsx';

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
