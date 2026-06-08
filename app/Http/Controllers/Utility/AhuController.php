<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\Ahu;
use App\Models\Utility\AhuDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AhuController extends Controller
{
    public function index()
    {
        return view('utility.ahu.form');
    }

    public function dataView()
    {
        return view('utility.ahu.data');
    }

    public function approvalView()
    {
        return view('utility.ahu.approval');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'jam' => 'required',

                'ampere_1' => 'nullable|numeric',
                'set_temp_1' => 'nullable|numeric',
                'pressure_in_1' => 'nullable|numeric',
                'pressure_out_1' => 'nullable|numeric',
                'ct_in_1' => 'nullable|numeric',
                'ct_out_1' => 'nullable|numeric',

                'ampere_2' => 'nullable|numeric',
                'set_temp_2' => 'nullable|numeric',
                'pressure_in_2' => 'nullable|numeric',
                'pressure_out_2' => 'nullable|numeric',
                'ct_in_2' => 'nullable|numeric',
                'ct_out_2' => 'nullable|numeric',

                'ampere_3' => 'nullable|numeric',
                'set_temp_3' => 'nullable|numeric',
                'pressure_in_3' => 'nullable|numeric',
                'pressure_out_3' => 'nullable|numeric',
                'ct_in_3' => 'nullable|numeric',
                'ct_out_3' => 'nullable|numeric',

                'ampere_4' => 'nullable|numeric',
                'set_temp_4' => 'nullable|numeric',
                'pressure_in_4' => 'nullable|numeric',
                'pressure_out_4' => 'nullable|numeric',
                'ct_in_4' => 'nullable|numeric',
                'ct_out_4' => 'nullable|numeric',

                'temp_out_1' => 'nullable|numeric',
                'temp_out_2' => 'nullable|numeric',
                'temp_out_3' => 'nullable|numeric',
                'temp_out_4' => 'nullable|numeric',
            ]);

            // Dupe check
            if (AhuDetails::where('tanggal', $validated['tanggal'])->exists()) {
                return response()->json(['status' => 422, 'message' => 'Laporan untuk tanggal ini sudah ada'], 422);
            }

            $date = Carbon::parse($validated['tanggal']);
            $month = $date->month;
            $year = $date->year;

            // Find or create monthly header
            $main = Ahu::firstOrCreate(
                [
                    'bulan' => $month,
                    'tahun' => $year,
                ],
                [
                    'operator_id' => auth()->id(),
                    'status' => 'draft',
                ]
            );

            $validated['ahu_id'] = $main->id;
            $detail = AhuDetails::create($validated);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data AHU berhasil disimpan.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store AHU Error: ' . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Terjadi kesalahan pada server', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $detail = AhuDetails::findOrFail($id);
        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'jam' => 'required',
                'ampere_1' => 'nullable|numeric',
                'set_temp_1' => 'nullable|numeric',
                'pressure_in_1' => 'nullable|numeric',
                'pressure_out_1' => 'nullable|numeric',
                'ct_in_1' => 'nullable|numeric',
                'ct_out_1' => 'nullable|numeric',
                'ampere_2' => 'nullable|numeric',
                'set_temp_2' => 'nullable|numeric',
                'pressure_in_2' => 'nullable|numeric',
                'pressure_out_2' => 'nullable|numeric',
                'ct_in_2' => 'nullable|numeric',
                'ct_out_2' => 'nullable|numeric',
                'ampere_3' => 'nullable|numeric',
                'set_temp_3' => 'nullable|numeric',
                'pressure_in_3' => 'nullable|numeric',
                'pressure_out_3' => 'nullable|numeric',
                'ct_in_3' => 'nullable|numeric',
                'ct_out_3' => 'nullable|numeric',
                'ampere_4' => 'nullable|numeric',
                'set_temp_4' => 'nullable|numeric',
                'pressure_in_4' => 'nullable|numeric',
                'pressure_out_4' => 'nullable|numeric',
                'ct_in_4' => 'nullable|numeric',
                'ct_out_4' => 'nullable|numeric',
                'temp_out_1' => 'nullable|numeric',
                'temp_out_2' => 'nullable|numeric',
                'temp_out_3' => 'nullable|numeric',
                'temp_out_4' => 'nullable|numeric',
            ]);

            $detail->update($validated);
            return response()->json(['status' => 200, 'message' => 'Data AHU berhasil diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Gagal update data'], 500);
        }
    }

    public function submitMonthly(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
            'foreman_id' => 'required|exists:users,id',
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $main = Ahu::where([
            'bulan' => $validated['bulan'],
            'tahun' => $validated['tahun'],
        ])->first();

        if (!$main) return response()->json(['message' => 'Data untuk bulan ini belum tersedia'], 404);

        $main->update([
            'foreman_id' => $validated['foreman_id'],
            'supervisor_id' => $validated['supervisor_id'],
            'status' => 'submitted',
            'submitted_at' => now(),
            'operator_id' => auth()->id(),
        ]);

        $this->sendNotification($main, $main->foreman_id);

        return response()->json(['message' => 'Laporan bulanan berhasil disubmit untuk approval']);
    }

    public function getData(Request $request)
    {
        $query = AhuDetails::with('ahu')->orderBy('tanggal', 'desc')->orderBy('jam', 'desc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month);
        }

        $data = $query->paginate($request->get('per_page', 15));

        $items = collect($data->items())->map(function ($item) {
            $item->approval_status = $item->ahu ? $item->ahu->status : 'none';
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
        $mainDrafts = Ahu::whereIn('status', ['draft', 'rejected'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $result = [];
        foreach ($mainDrafts as $main) {
            $details = AhuDetails::where('ahu_id', $main->id)->get();
            if ($details->count() > 0) {
                $result[] = ['approval' => $main, 'data' => $details];
            }
        }
        return response()->json(['status' => 200, 'results' => $result]);
    }

    public function getApprovalData(Request $request)
    {
        $query = Ahu::with(['operator', 'foreman', 'supervisor'])
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
        return response()->json(['status' => 200, 'data' => $data->items(), 'pagination' => ['total' => $data->total(), 'last_page' => $data->lastPage()]]);
    }

    public function approveForeman($id)
    {
        $data = Ahu::findOrFail($id);

        if ($data->foreman_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data->update([
            'approved_foreman_at' => now(),
            'status' => 'approved_foreman'
        ]);

        NotificationsModel::where('notifiable_type', Ahu::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        try {
            $this->sendNotification($data, $data->supervisor_id);
        } catch (\Exception $e) {
            Log::error('Notif AHU Supervisor gagal: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = Ahu::findOrFail($id);
        if ($data->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data->update([
            'approved_supervisor_at' => now(),
            'status' => 'approved_supervisor'
        ]);

        NotificationsModel::where('notifiable_type', Ahu::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Disetujui Supervisor']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);

        $data = Ahu::findOrFail($id);

        $data->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        NotificationsModel::where('notifiable_type', Ahu::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Ditolak']);
    }

    public function show($id)
    {
        $data = AhuDetails::find($id);
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function showMonthlyDetails($id)
    {
        $main = Ahu::with(['operator', 'foreman', 'supervisor'])->findOrFail($id);
        $details = AhuDetails::where('ahu_id', $id)->orderBy('tanggal', 'asc')->orderBy('jam', 'asc')->get();
        return response()->json(['status' => 200, 'header' => $main, 'details' => $details]);
    }

    public function export(Request $request)
    {
        $query = AhuDetails::with(['ahu.operator', 'ahu.foreman', 'ahu.supervisor'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam', 'asc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)->whereMonth('tanggal', $date->month);
        }

        $data = $query->get();

        if ($data->isEmpty()) {
            return "<script>alert('Tidak ada data ditemukan untuk periode tersebut'); window.close();</script>";
        }

        $templatePath = public_path('assets/templates/operasional/ahu.xlsx');
        if (!file_exists($templatePath)) {
            return "<script>alert('Template AHU tidak ditemukan'); window.close();</script>";
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Header Info
        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $sheet->setCellValue('W1', strtoupper($date->translatedFormat('F')));
            $sheet->setCellValue('W3', $date->year);
        }

        foreach ($data as $item) {
            $day = Carbon::parse($item->tanggal)->day;
            $currentRow = 7 + ($day - 1); // Start Row 7

            // B: Jam
            $sheet->setCellValue('B' . $currentRow, Carbon::parse($item->jam)->format('H:i'));

            // AHU 1 (C-I)
            $sheet->setCellValue('C' . $currentRow, $item->ampere_1);
            $sheet->setCellValue('D' . $currentRow, $item->set_temp_1);
            $sheet->setCellValue('E' . $currentRow, $item->pressure_in_1);
            $sheet->setCellValue('F' . $currentRow, $item->pressure_out_1);
            $sheet->setCellValue('G' . $currentRow, $item->ct_in_1);
            $sheet->setCellValue('H' . $currentRow, $item->ct_out_1);



            // AHU 2 (J-P)
            $sheet->setCellValue('I' . $currentRow, $item->ampere_2);
            $sheet->setCellValue('J' . $currentRow, $item->set_temp_2);
            $sheet->setCellValue('K' . $currentRow, $item->pressure_in_2);
            $sheet->setCellValue('L' . $currentRow, $item->pressure_out_2);
            $sheet->setCellValue('M' . $currentRow, $item->ct_in_2);
            $sheet->setCellValue('N' . $currentRow, $item->ct_out_2);


            // AHU 3 (Q-W)
            $sheet->setCellValue('O' . $currentRow, $item->ampere_3);
            $sheet->setCellValue('P' . $currentRow, $item->set_temp_3);
            $sheet->setCellValue('Q' . $currentRow, $item->pressure_in_3);
            $sheet->setCellValue('R' . $currentRow, $item->pressure_out_3);
            $sheet->setCellValue('S' . $currentRow, $item->ct_in_3);
            $sheet->setCellValue('T' . $currentRow, $item->ct_out_3);


            // AHU 4 (X-AD)
            $sheet->setCellValue('U' . $currentRow, $item->ampere_4);
            $sheet->setCellValue('V' . $currentRow, $item->set_temp_4);
            $sheet->setCellValue('W' . $currentRow, $item->pressure_in_4);
            $sheet->setCellValue('X' . $currentRow, $item->pressure_out_4);
            $sheet->setCellValue('Y' . $currentRow, $item->ct_in_4);
            $sheet->setCellValue('Z' . $currentRow, $item->ct_out_4);

            $sheet->setCellValue('AA' . $currentRow, $item->temp_out_1);
            $sheet->setCellValue('AB' . $currentRow, $item->temp_out_2);
            $sheet->setCellValue('AC' . $currentRow, $item->temp_out_3);
            $sheet->setCellValue('AD' . $currentRow, $item->temp_out_4);
        }

        // Signature Section
        $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');
        $mainRecord = $data->first()->ahu;

        if (file_exists($signaturePath) && $mainRecord) {
            if ($mainRecord->status != 'draft') {
                $drawOp = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawOp->setName('Operator');
                $drawOp->setPath($signaturePath);
                $drawOp->setHeight(60);
                $drawOp->setCoordinates('C40');
                $drawOp->setWorksheet($sheet);
                $sheet->setCellValue('B44', $mainRecord->operator ? $mainRecord->operator->username : '-');
                $sheet->setCellValue('B45', $mainRecord->submitted_at ?? '-');
            }
            if ($mainRecord->status == 'approved_foreman') {
                $drawFm = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawFm->setName('Foreman');
                $drawFm->setPath($signaturePath);
                $drawFm->setHeight(60);
                $drawFm->setCoordinates('N40');
                $drawFm->setWorksheet($sheet);
                $sheet->setCellValue('M44', $mainRecord->foreman ? $mainRecord->foreman->username : '-');
                $sheet->setCellValue('M45', $mainRecord->approved_foreman_at ?? '-');
            }
            if ($mainRecord->status == 'approved_supervisor') {
                $drawSpv = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawSpv->setName('Supervisor');
                $drawSpv->setPath($signaturePath);
                $drawSpv->setHeight(60);
                $drawSpv->setCoordinates('AA40');
                $drawSpv->setWorksheet($sheet);
                $sheet->setCellValue('Z44', $mainRecord->supervisor ? $mainRecord->supervisor->username : '-');
                $sheet->setCellValue('Z45', $mainRecord->approved_supervisor_at ?? '-');
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'AHU_Monthly_Report_' . now()->format('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function destroy($id)
    {
        $data = AhuDetails::findOrFail($id);
        $data->delete();
        return response()->json(['status' => 200, 'message' => 'Data dihapus']);
    }

    private function sendNotification($main, $userId)
    {
        $approvalUrl = url(route('ahu.approval', [], false));
        NotificationsModel::updateOrCreate(
            ['user_id' => $userId, 'notifiable_type' => Ahu::class, 'notifiable_id' => $main->id, 'is_read' => 0],
            ['title' => 'Approval Bulanan AHU', 'message' => "Laporan AHU Bulan {$main->bulan} {$main->tahun} menunggu persetujuan", 'url' => $approvalUrl]
        );
    }
}
