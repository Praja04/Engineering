<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\MdpMonitoring as MdpMonitoringModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MdpMonitoringController extends Controller
{
    public function index()
    {
        return view('utility.mdp.form');
    }

    public function approvalView()
    {
        return view('utility.mdp.approval');
    }

    public function dataView()
    {
        return view('utility.mdp.data');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'tanggal_laporan' => 'required|date',
                'jam_pencatatan' => 'required',
                'foreman_id' => 'required|exists:users,id',
                'supervisor_id' => 'required|exists:users,id',

                'e_del' => 'nullable|numeric',
                'arus_rata_rata' => 'nullable|numeric',
                'arus_i1' => 'nullable|numeric',
                'arus_i2' => 'nullable|numeric',
                'arus_i3' => 'nullable|numeric',
                'tegangan_rata_rata' => 'nullable|numeric',
                'tegangan_v1' => 'nullable|numeric',
                'tegangan_v2' => 'nullable|numeric',
                'tegangan_v3' => 'nullable|numeric',
                'daya_total' => 'nullable|numeric',
                'daya_p1' => 'nullable|numeric',
                'daya_p2' => 'nullable|numeric',
                'daya_p3' => 'nullable|numeric',
                'temperatur_transformator' => 'nullable|numeric',
                'level_oil' => 'nullable|string|in:ok,nok',
            ]);

            // Cek Duplikat
            if (MdpMonitoringModel::where('user_id', auth()->id())
                ->where('tanggal_laporan', $validated['tanggal_laporan'])
                ->exists()
            ) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Laporan untuk tanggal ini sudah ada'
                ], 422);
            }

            $report = MdpMonitoringModel::create([
                ...$validated,
                'user_id' => auth()->id(),
                'status' => 'submitted',
            ]);

            try {
                $this->sendNotification($report);
            } catch (\Exception $e) {
                Log::error('Notif MDP gagal: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Laporan MDP berhasil disubmit & menunggu approval.',
                'data' => $report
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store MDP Error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function sendNotification($data)
    {
        $approvalUrl = url(route('mdp-monitoring.approval', [], false));

        $recipients = User::whereIn('id', array_filter([
            $data->foreman_id,
            $data->supervisor_id,
        ]))->get();

        foreach ($recipients as $user) {
            NotificationsModel::create([
                'user_id'          => $user->id,
                'title'            => 'Approval Pemantauan MDP',
                'message'          => 'Laporan pemantauan MDP tanggal ' . $data->tanggal_laporan . ' menunggu persetujuan Anda',
                'url'              => $approvalUrl,
                'notifiable_type'  => MdpMonitoringModel::class,
                'notifiable_id'    => $data->id,
                'is_read'          => 0,
            ]);
        }
    }

    public function approveForeman($id)
    {
        $data = MdpMonitoringModel::findOrFail($id);

        if ($data->foreman_id !== auth()->id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        if ($data->status !== 'submitted') {
            return response()->json(['message' => 'Laporan ini tidak dalam status menunggu approval Foreman'], 422);
        }

        $data->update([
            'approved_foreman_at' => now(),
            'approved_foreman_by' => auth()->id(),
            'status' => 'approved_foreman'
        ]);

        NotificationsModel::where('notifiable_type', MdpMonitoringModel::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', auth()->id()) // opsional (biar spesifik)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'Laporan disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = MdpMonitoringModel::findOrFail($id);

        if ($data->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        if ($data->status !== 'approved_foreman') {
            return response()->json(['message' => 'Laporan harus disetujui Foreman terlebih dahulu'], 422);
        }

        $data->update([
            'approved_supervisor_at' => now(),
            'approved_supervisor_by' => auth()->id(),
            'status' => 'approved_supervisor'
        ]);

        NotificationsModel::where('notifiable_type', MdpMonitoringModel::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', auth()->id()) // opsional (biar spesifik)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'Laporan disetujui Supervisor (Selesai)']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $data = MdpMonitoringModel::findOrFail($id);

        $isForeman = ($data->foreman_id === auth()->id() && $data->status === 'submitted');
        $isSupervisor = ($data->supervisor_id === auth()->id() && $data->status === 'approved_foreman');

        if (!$isForeman && !$isSupervisor) {
            return response()->json(['message' => 'Anda tidak berwenang menolak laporan ini pada tahap ini'], 403);
        }

        $data->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        return response()->json(['message' => 'Laporan berhasil ditolak']);
    }

    public function show($id)
    {
        $data = MdpMonitoringModel::with(['operator', 'foreman', 'supervisor'])->find($id);

        if (!$data) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $data = MdpMonitoringModel::findOrFail($id);

        // Hanya bisa edit jika status masih submitted (atau admin)
        if ($data->status !== 'submitted' && !auth()->user()->hasRole(['superadmin', 'admin'])) {
            return response()->json(['message' => 'Hanya laporan dengan status submitted yang bisa diubah'], 422);
        }

        $validated = $request->validate([
            'e_del' => 'nullable|numeric',
            'arus_rata_rata' => 'nullable|numeric',
            'arus_i1' => 'nullable|numeric',
            'arus_i2' => 'nullable|numeric',
            'arus_i3' => 'nullable|numeric',
            'tegangan_rata_rata' => 'nullable|numeric',
            'tegangan_v1' => 'nullable|numeric',
            'tegangan_v2' => 'nullable|numeric',
            'tegangan_v3' => 'nullable|numeric',
            'daya_total' => 'nullable|numeric',
            'daya_p1' => 'nullable|numeric',
            'daya_p2' => 'nullable|numeric',
            'daya_p3' => 'nullable|numeric',
            'temperatur_transformator' => 'nullable|numeric',
            'level_oil' => 'nullable|string|in:ok,nok',
        ]);

        $data->update($validated);

        return response()->json(['message' => 'Data MDP berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $data = MdpMonitoringModel::findOrFail($id);

        if ($data->status !== 'submitted' && !auth()->user()->hasRole(['superadmin', 'admin'])) {
            return response()->json(['message' => 'Data yang sudah diapprove tidak dapat dihapus'], 422);
        }

        $data->delete();

        return response()->json(['message' => 'Data MDP berhasil dihapus']);
    }

    public function getData(Request $request)
    {
        $query = MdpMonitoringModel::with(['operator', 'foreman', 'supervisor'])
            ->orderBy('tanggal_laporan', 'desc')
            ->orderBy('jam_pencatatan', 'desc');

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

        if ($request->filled('bulan')) {
            $bulan = $request->bulan;
            $query->whereYear('tanggal_laporan', substr($bulan, 0, 4))
                ->whereMonth('tanggal_laporan', substr($bulan, 5, 2));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

    public function bulkApprove(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['message' => 'Tidak ada data yang dipilih'], 422);
        }

        $successCount = 0;
        foreach ($ids as $id) {
            $data = MdpMonitoringModel::find($id);
            if (!$data) continue;

            if ($data->foreman_id === auth()->id() && $data->status === 'submitted') {
                $data->update([
                    'approved_foreman_at' => now(),
                    'approved_foreman_by' => auth()->id(),
                    'status' => 'approved_foreman'
                ]);
                $successCount++;
            } elseif ($data->supervisor_id === auth()->id() && $data->status === 'approved_foreman') {
                $data->update([
                    'approved_supervisor_at' => now(),
                    'approved_supervisor_by' => auth()->id(),
                    'status' => 'approved_supervisor'
                ]);
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
            $data = MdpMonitoringModel::find($id);
            if (!$data) continue;

            $isForeman = ($data->foreman_id === auth()->id() && $data->status === 'submitted');
            $isSupervisor = ($data->supervisor_id === auth()->id() && $data->status === 'approved_foreman');

            if ($isForeman || $isSupervisor) {
                $data->update([
                    'status' => 'rejected',
                    'reject_reason' => $request->reason
                ]);
                $successCount++;
            }
        }

        return response()->json([
            'status' => 200,
            'message' => $successCount . ' laporan berhasil ditolak secara massal.'
        ]);
    }

    public function export(Request $request)
    {
        $query = MdpMonitoringModel::with(['operator', 'foreman', 'supervisor'])
            ->orderBy('tanggal_laporan', 'desc')
            ->orderBy('jam_pencatatan', 'desc');

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereYear('tanggal_laporan', $request->tahun)
                ->whereMonth('tanggal_laporan', $request->bulan);
        }

        $data = $query->get();

        if ($data->isEmpty()) {
            return "<script>alert('Tidak ada data ditemukan untuk periode tersebut'); window.close();</script>";
        }

        $templatePath = public_path('assets/templates/operasional/mdp.xlsx');
        if (!file_exists($templatePath)) {
            return "<script>alert('Template MDP tidak ditemukan di: " . $templatePath . "'); window.close();</script>";
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Header Info
        if ($request->filled('bulan')) {
            $monthNum = (int) $request->bulan;
            $monthName = Carbon::create()->month($monthNum)->translatedFormat('F');
            $sheet->setCellValue('P1', strtoupper($monthName));
            $sheet->setCellValue('P2', $request->tahun);
        }

        // Path Stiker TTD
        $signaturePath = asset('storage/operasional/ttd/utility_approved_sticker.png');
        $hasSignature = file_exists($signaturePath);

        // Isi Data Teknis (Berdasarkan Tanggal: Baris 5-35)
        foreach ($data as $item) {
            $day = Carbon::parse($item->tanggal_laporan)->day;
            $currentRow = 5 + ($day - 1); // Tanggal 1 di baris 5, dst.

            $sheet->setCellValue(
                'B' . $currentRow,
                Carbon::parse($item->jam_pencatatan)->format('H:i')
            );
            $sheet->setCellValue('C' . $currentRow, $item->e_del);
            $sheet->setCellValue('D' . $currentRow, $item->arus_rata_rata);
            $sheet->setCellValue('E' . $currentRow, $item->arus_i1);
            $sheet->setCellValue('F' . $currentRow, $item->arus_i2);
            $sheet->setCellValue('G' . $currentRow, $item->arus_i3);
            $sheet->setCellValue('H' . $currentRow, $item->tegangan_rata_rata);
            $sheet->setCellValue('I' . $currentRow, $item->tegangan_v1);
            $sheet->setCellValue('J' . $currentRow, $item->tegangan_v2);
            $sheet->setCellValue('K' . $currentRow, $item->tegangan_v3);
            $sheet->setCellValue('L' . $currentRow, $item->daya_total);
            $sheet->setCellValue('M' . $currentRow, $item->daya_p1);
            $sheet->setCellValue('N' . $currentRow, $item->daya_p2);
            $sheet->setCellValue('O' . $currentRow, $item->daya_p3);
            $sheet->setCellValue('P' . $currentRow, $item->temperatur_transformator);
            $sheet->setCellValue('Q' . $currentRow, $item->level_oil);

            // Masukkan TTD ke dalam Loop (Kolom R & S)
            if ($hasSignature) {
                // TTD Operator (R)
                if ($item->status != 'draft') {
                    $drawOp = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawOp->setName('Op');
                    $drawOp->setPath($signaturePath);
                    $drawOp->setHeight(20); // Ukuran lebih kecil
                    $drawOp->setCoordinates('R' . $currentRow);
                    $drawOp->setWorksheet($sheet);
                }

                // TTD Approval (S)
                if (in_array($item->status, ['approved_foreman', 'approved_supervisor'])) {
                    $drawApp = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawApp->setName('App');
                    $drawApp->setPath($signaturePath);
                    $drawApp->setHeight(20); // Ukuran lebih kecil
                    $drawApp->setCoordinates('S' . $currentRow);
                    $drawApp->setWorksheet($sheet);
                }
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'MDP_Monitoring_Report_' . now()->format('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
