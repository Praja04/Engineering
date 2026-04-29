<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\Compressor;
use App\Models\Utility\CompressorDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CompressorController extends Controller
{
    public function index()
    {
        return view('utility.compressor.form');
    }

    public function dataView()
    {
        return view('utility.compressor.data');
    }

    public function approvalView()
    {
        return view('utility.compressor.approval');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'jam' => 'required|in:08:00,12:00,16:00,00:00,04:00',

                'pressure_outlet_1' => 'nullable|numeric',
                'pressure_outlet_2' => 'nullable|numeric',
                'pressure_outlet_3' => 'nullable|numeric',
                'pressure_outlet_4' => 'nullable|numeric',

                'element_outlet_1' => 'nullable|numeric',
                'element_outlet_2' => 'nullable|numeric',
                'element_outlet_4' => 'nullable|numeric',

                'load_percent' => 'nullable|numeric',

                'running_hour_1' => 'nullable|numeric',
                'running_hour_2' => 'nullable|numeric',
                'running_hour_3' => 'nullable|numeric',
                'running_hour_4' => 'nullable|numeric',

                'loaded_hour_1' => 'nullable|numeric',
                'loaded_hour_2' => 'nullable|numeric',
                'loaded_hour_3' => 'nullable|numeric',
                'loaded_hour_4' => 'nullable|numeric',

                'motor_start_1' => 'nullable|numeric',
                'motor_start_2' => 'nullable|numeric',
                'motor_start_3' => 'nullable|numeric',
                'motor_start_4' => 'nullable|numeric',

                'accumulated_volume' => 'nullable|numeric',
                'temperature_comp_ir' => 'nullable|numeric',
                'pressure_in' => 'nullable|numeric',
                'pressure_out' => 'nullable|numeric',

                'suhu_dryer_tr15' => 'nullable|numeric',
                'suhu_dryer_fx250' => 'nullable|numeric',
                'suhu_dryer_ir' => 'nullable|numeric',
            ]);

            // Cek Duplikat di Details
            if (CompressorDetails::where('tanggal', $validated['tanggal'])
                ->where('jam', $validated['jam'])
                ->exists()
            ) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Laporan untuk tanggal dan jam ini sudah ada'
                ], 422);
            }

            // Hitung Minggu, Bulan, Tahun berdasarkan Senin sebagai awal minggu
            $date = Carbon::parse($validated['tanggal'])->startOfDay();
            $monday = $date->copy()->startOfWeek(Carbon::MONDAY);
            $sunday = $date->copy()->endOfWeek(Carbon::SUNDAY);

            // Kita gunakan data dari hari Senin tersebut sebagai identitas minggu
            $week = $monday->weekOfMonth;
            $month = $monday->month;
            $year = $monday->year;

            $tgl_awal = $monday->format('Y-m-d');
            $tgl_akhir = $sunday->format('Y-m-d');

            // Find or create main record
            $main = Compressor::firstOrCreate(
                [
                    'week' => $week,
                    'bulan' => $month,
                    'tahun' => $year,
                ],
                [
                    'tgl_awal' => $tgl_awal,
                    'tgl_akhir' => $tgl_akhir,
                    'operator_id' => auth()->id(),
                    'status' => 'draft',
                ]
            );

            // Create Detail record
            $validated['compressor_id'] = $main->id;
            $detail = CompressorDetails::create($validated);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data compressor berhasil disimpan sebagai Draft.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Compressor Error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $detail = CompressorDetails::findOrFail($id);

        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'jam' => 'required|in:08:00,12:00,16:00,00:00,04:00',
                'pressure_outlet_1' => 'nullable|numeric',
                'pressure_outlet_2' => 'nullable|numeric',
                'pressure_outlet_3' => 'nullable|numeric',
                'pressure_outlet_4' => 'nullable|numeric',
                'element_outlet_1' => 'nullable|numeric',
                'element_outlet_2' => 'nullable|numeric',
                'element_outlet_4' => 'nullable|numeric',
                'load_percent' => 'nullable|numeric',
                'running_hour_1' => 'nullable|numeric',
                'running_hour_2' => 'nullable|numeric',
                'running_hour_3' => 'nullable|numeric',
                'running_hour_4' => 'nullable|numeric',
                'motor_start_1' => 'nullable|numeric',
                'motor_start_2' => 'nullable|numeric',
                'motor_start_3' => 'nullable|numeric',
                'motor_start_4' => 'nullable|numeric',
                'accumulated_volume' => 'nullable|numeric',
                'temperature_comp_ir' => 'nullable|numeric',
                'pressure_in' => 'nullable|numeric',
                'pressure_out' => 'nullable|numeric',
                'suhu_dryer_tr15' => 'nullable|numeric',
                'suhu_dryer_fx250' => 'nullable|numeric',
                'suhu_dryer_ir' => 'nullable|numeric',
            ]);

            $detail->update($validated);

            return response()->json([
                'status' => 200,
                'message' => 'Data compressor berhasil diperbarui.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            Log::error('Update Compressor Error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat update data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function submitWeekly(Request $request)
    {
        $validated = $request->validate([
            'week' => 'required|integer',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
            'foreman_id' => 'required|exists:users,id',
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $main = Compressor::where([
            'week' => $validated['week'],
            'bulan' => $validated['bulan'],
            'tahun' => $validated['tahun'],
        ])->first();

        if (!$main) {
            return response()->json(['message' => 'Data log untuk minggu ini belum tersedia'], 404);
        }

        if ($main->status !== 'draft' && $main->status !== 'rejected') {
            return response()->json(['message' => 'Laporan sudah disubmit atau diproses'], 422);
        }

        $main->update([
            'foreman_id' => $validated['foreman_id'],
            'supervisor_id' => $validated['supervisor_id'],
            'status' => 'submitted',
            'submitted_at' => now(),
            'operator_id' => auth()->id(),
        ]);

        try {
            $this->sendNotification($main);
        } catch (\Exception $e) {
            Log::error('Notif gagal: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Laporan mingguan berhasil disubmit untuk approval']);
    }

    private function sendNotification($main)
    {
        $approvalUrl = url(route('compressor.approval', [], false));

        $recipients = User::whereIn('id', array_filter([
            $main->foreman_id,
            $main->supervisor_id,
        ]))->get();

        foreach ($recipients as $user) {
            NotificationsModel::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notifiable_type' => Compressor::class,
                    'notifiable_id' => $main->id,
                    'is_read' => 0,
                ],
                [
                    'title' => 'Approval Mingguan Compressor',
                    'message' => "Laporan compressor Minggu ke-{$main->week}, Bulan {$main->bulan} {$main->tahun} menunggu persetujuan",
                    'url' => $approvalUrl,
                ]
            );
        }
    }

    public function getData(Request $request)
    {
        $query = CompressorDetails::with('compressor')->orderBy('tanggal', 'desc')->orderBy('jam', 'desc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month);
        }

        $data = $query->paginate($request->get('per_page', 15));

        // Kita tambahkan status approval untuk tiap data
        $items = collect($data->items())->map(function ($item) {
            $item->approval_status = $item->compressor ? $item->compressor->status : 'none';
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
        // Ambil data dari tabel main (compressor) yang statusnya masih draft/rejected
        $mainDrafts = Compressor::whereIn('status', ['draft', 'rejected'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('week', 'desc')
            ->get();

        $result = [];
        foreach ($mainDrafts as $main) {
            // Cek apakah data sudah "terkumpul se week" (opsional, tapi user minta "data masuk approval sudah ke collect se week")
            // Kita ambil detail data untuk minggu ini
            $details = CompressorDetails::where('compressor_id', $main->id)->get();

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
        $query = Compressor::with(['operator', 'foreman', 'supervisor'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('week', 'desc');

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
        $data = Compressor::findOrFail($id);

        if ($data->foreman_id !== auth()->id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        $data->update([
            'approved_foreman_at' => now(),
            'status' => 'approved_foreman'
        ]);

        return response()->json(['message' => 'Laporan disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = Compressor::findOrFail($id);

        if ($data->supervisor_id !== auth()->id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        $data->update([
            'approved_supervisor_at' => now(),
            'status' => 'approved_supervisor'
        ]);

        return response()->json(['message' => 'Laporan disetujui Supervisor']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $data = Compressor::findOrFail($id);

        $data->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        return response()->json(['message' => 'Laporan ditolak']);
    }

    public function show($id)
    {
        $data = CompressorDetails::find($id);
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function export(Request $request)
    {
        $query = CompressorDetails::with(['compressor.operator', 'compressor.foreman', 'compressor.supervisor'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month);
        }

        $data = $query->get();

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function showWeeklyDetails($id)
    {
        $main = Compressor::with(['operator', 'foreman', 'supervisor'])->findOrFail($id);
        $details = CompressorDetails::where('compressor_id', $id)
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam', 'asc')
            ->get();

        return response()->json([
            'status' => 200,
            'header' => $main,
            'details' => $details
        ]);
    }
}
