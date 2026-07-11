<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\AgendaTankFarm;
use App\Models\Utility\AgendaTankFarmDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgendaTankFarmController extends Controller
{
    public function index()
    {
        return view('utility.agenda-tank-farm.form');
    }

    public function dataView()
    {
        return view('utility.agenda-tank-farm.data');
    }

    public function approvalView()
    {
        return view('utility.agenda-tank-farm.approval');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'kelistrikan_pompa_sumur_1' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_sumur_2' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_sumur_4' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_sumur_5' => 'nullable|in:OK,NOK',
                'pressure_pompa_sumur_1' => 'nullable|in:OK,NOK',
                'pressure_pompa_sumur_2' => 'nullable|in:OK,NOK',
                'pressure_pompa_sumur_4' => 'nullable|in:OK,NOK',
                'pressure_pompa_sumur_5' => 'nullable|in:OK,NOK',
                'flow_meter_pompa_sumur_1' => 'nullable|in:OK,NOK',
                'flow_meter_pompa_sumur_2' => 'nullable|in:OK,NOK',
                'flow_meter_pompa_sumur_4' => 'nullable|in:OK,NOK',
                'flow_meter_pompa_sumur_5' => 'nullable|in:OK,NOK',
                'drain_lumpur_settling_tank' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10p3' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10p3a' => 'nullable|in:OK,NOK',
                'pressure_gauge_intermediate' => 'nullable|in:OK,NOK',
                'level_bandul_tank_farm' => 'nullable|in:OK,NOK',
                'flow_meter_fresh_water_tank' => 'nullable|in:OK,NOK',
                'flow_meter_fwt_to_ro' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10p4' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10p4a' => 'nullable|in:OK,NOK',
                'pressure_gauge_pompa_10p4_p4a' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10p5' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10p5a' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10p5b' => 'nullable|in:OK,NOK',
                'flow_meter_ro_reject_tank' => 'nullable|in:OK,NOK',
                'pressure_gauge_pompa_10p5_10p5a' => 'nullable|in:OK,NOK',
                'drain_lumpur_tangki_intermediate' => 'nullable|in:OK,NOK',
                'inspeksi_all_pompa_tf_intermediate' => 'nullable|in:OK,NOK',
                'inspeksi_pompa_20p1' => 'nullable|in:OK,NOK',
                'inspeksi_pompa_20p1a' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_20p2' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_20p2a' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_60p1' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_60p2' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_60p3' => 'nullable|in:OK,NOK',
                'pressure_gauge_pompa_60p1' => 'nullable|in:OK,NOK',
                'pressure_gauge_pompa_60p2' => 'nullable|in:OK,NOK',
                'pressure_gauge_pompa_60p3' => 'nullable|in:OK,NOK',
                'baterai_pompa_60p3' => 'nullable|in:OK,NOK',
                'bahan_bakar_pompa_60p3' => 'nullable|in:OK,NOK',
                'pressure_gauge_water_tank_hydrant' => 'nullable|in:OK,NOK',
            ]);

            // Cek Duplikat di Details
            if (AgendaTankFarmDetails::where('tanggal', $validated['tanggal'])->exists()) {
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
            $main = AgendaTankFarm::firstOrCreate(
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

            $validated['agenda_tank_farm_id'] = $main->id;
            $validated['created_by'] = Auth::id();
            $detail = AgendaTankFarmDetails::create($validated);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data agenda checklist berhasil disimpan.',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Agenda Tank Farm Error: ' . $e->getMessage());
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
            $detail = AgendaTankFarmDetails::findOrFail($id);
            $main = $detail->agendaTankFarm;

            $validated = $request->validate([
                'tanggal' => 'required|date',
                'kelistrikan_pompa_sumur_1' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_sumur_2' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_sumur_4' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_sumur_5' => 'nullable|in:OK,NOK',
                'pressure_pompa_sumur_1' => 'nullable|in:OK,NOK',
                'pressure_pompa_sumur_2' => 'nullable|in:OK,NOK',
                'pressure_pompa_sumur_4' => 'nullable|in:OK,NOK',
                'pressure_pompa_sumur_5' => 'nullable|in:OK,NOK',
                'flow_meter_pompa_sumur_1' => 'nullable|in:OK,NOK',
                'flow_meter_pompa_sumur_2' => 'nullable|in:OK,NOK',
                'flow_meter_pompa_sumur_4' => 'nullable|in:OK,NOK',
                'flow_meter_pompa_sumur_5' => 'nullable|in:OK,NOK',
                'drain_lumpur_settling_tank' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10p3' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10p3a' => 'nullable|in:OK,NOK',
                'pressure_gauge_intermediate' => 'nullable|in:OK,NOK',
                'level_bandul_tank_farm' => 'nullable|in:OK,NOK',
                'flow_meter_fresh_water_tank' => 'nullable|in:OK,NOK',
                'flow_meter_fwt_to_ro' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10p4' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10p4a' => 'nullable|in:OK,NOK',
                'pressure_gauge_pompa_10p4_p4a' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10p5' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10p5a' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_10p5b' => 'nullable|in:OK,NOK',
                'flow_meter_ro_reject_tank' => 'nullable|in:OK,NOK',
                'pressure_gauge_pompa_10p5_10p5a' => 'nullable|in:OK,NOK',
                'drain_lumpur_tangki_intermediate' => 'nullable|in:OK,NOK',
                'inspeksi_all_pompa_tf_intermediate' => 'nullable|in:OK,NOK',
                'inspeksi_pompa_20p1' => 'nullable|in:OK,NOK',
                'inspeksi_pompa_20p1a' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_20p2' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_20p2a' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_60p1' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_60p2' => 'nullable|in:OK,NOK',
                'kelistrikan_pompa_60p3' => 'nullable|in:OK,NOK',
                'pressure_gauge_pompa_60p1' => 'nullable|in:OK,NOK',
                'pressure_gauge_pompa_60p2' => 'nullable|in:OK,NOK',
                'pressure_gauge_pompa_60p3' => 'nullable|in:OK,NOK',
                'baterai_pompa_60p3' => 'nullable|in:OK,NOK',
                'bahan_bakar_pompa_60p3' => 'nullable|in:OK,NOK',
                'pressure_gauge_water_tank_hydrant' => 'nullable|in:OK,NOK',
            ]);

            // Cek Duplikat di Details (jika tanggal berubah)
            if (
                $detail->tanggal->format('Y-m-d') !== $validated['tanggal'] &&
                AgendaTankFarmDetails::where('tanggal', $validated['tanggal'])->exists()
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
            Log::error('Update Agenda Tank Farm Error: ' . $e->getMessage());
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

        $main = AgendaTankFarm::where([
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
        $approvalUrl = url(route('agenda-tank-farm.approval', [], false));

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
                    'notifiable_type' => AgendaTankFarm::class,
                    'notifiable_id' => $main->id,
                    'is_read' => 0,
                ],
                [
                    'title' => 'Approval Bulanan Agenda TF-HY',
                    'message' => "Laporan agenda Tank Farm & Hydrant Bulan {$main->bulan} {$main->tahun} menunggu persetujuan",
                    'url' => $approvalUrl,
                ]
            );
        }
    }

    public function getData(Request $request)
    {
        $query = AgendaTankFarmDetails::with('agendaTankFarm')->orderBy('tanggal', 'desc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month);
        }

        $data = $query->paginate($request->get('per_page', 15));

        $items = collect($data->items())->map(function ($item) {
            $item->approval_status = $item->agendaTankFarm ? $item->agendaTankFarm->status : 'none';
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
        $mainDrafts = AgendaTankFarm::whereIn('status', ['draft', 'rejected'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $result = [];
        foreach ($mainDrafts as $main) {
            $details = AgendaTankFarmDetails::where('agenda_tank_farm_id', $main->id)->get();
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
        $query = AgendaTankFarm::with(['operator', 'foreman', 'supervisor'])
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
        $data = AgendaTankFarm::findOrFail($id);

        if ($data->foreman_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        $data->update([
            'approved_foreman_at' => now(),
            'status' => 'approved_foreman'
        ]);

        NotificationsModel::where('notifiable_type', AgendaTankFarm::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Laporan disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = AgendaTankFarm::findOrFail($id);

        if ($data->supervisor_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak berwenang'], 403);
        }

        $data->update([
            'approved_supervisor_at' => now(),
            'status' => 'approved_supervisor'
        ]);

        NotificationsModel::where('notifiable_type', AgendaTankFarm::class)
            ->where('notifiable_id', $data->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Laporan disetujui Supervisor']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $data = AgendaTankFarm::findOrFail($id);

        $data->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        NotificationsModel::where('notifiable_type', AgendaTankFarm::class)
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
            $data = AgendaTankFarm::find($id);
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
                NotificationsModel::where('notifiable_type', AgendaTankFarm::class)
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
            $data = AgendaTankFarm::find($id);
            if (!$data) continue;

            $isForeman = ($data->foreman_id === Auth::id() && $data->status === 'submitted');
            $isSupervisor = ($data->supervisor_id === Auth::id() && $data->status === 'approved_foreman');

            if ($isForeman || $isSupervisor) {
                $data->update([
                    'status' => 'rejected',
                    'reject_reason' => $request->reason
                ]);

                NotificationsModel::where('notifiable_type', AgendaTankFarm::class)
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
        $data = AgendaTankFarmDetails::with('createdBy')->find($id);
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function showMonthlyDetails($id)
    {
        $main = AgendaTankFarm::with(['operator', 'foreman', 'supervisor'])->findOrFail($id);
        $details = AgendaTankFarmDetails::where('agenda_tank_farm_id', $id)
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
        $data = AgendaTankFarmDetails::findOrFail($id);
        $main = $data->agendaTankFarm;

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

        $agendaTankFarmId = $data->agenda_tank_farm_id;
        $data->delete();

        // Cek apakah masih ada detail lain
        $remainingDetails = AgendaTankFarmDetails::where('agenda_tank_farm_id', $agendaTankFarmId)->count();
        if ($remainingDetails == 0) {
            $m = AgendaTankFarm::find($agendaTankFarmId);
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
        $query = AgendaTankFarmDetails::with(['agendaTankFarm.operator', 'agendaTankFarm.foreman', 'agendaTankFarm.supervisor'])
            ->orderBy('tanggal', 'asc');

        if ($request->filled('bulan')) {
            $query->whereHas('agendaTankFarm', function ($q) use ($request) {
                $q->where('bulan', $request->bulan);
            });
        }
        if ($request->filled('tahun')) {
            $query->whereHas('agendaTankFarm', function ($q) use ($request) {
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

        $templatePath = public_path('assets/templates/operasional/agenda_tank_farm_hydrant.xlsx');
        if (!file_exists($templatePath)) {
            return response()->json([
                'status' => 404,
                'message' => 'File template excel tidak ditemukan.'
            ], 404);
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);

        $tempFiles = [];
        $monthsData = $data->groupBy(function ($item) {
            return $item->agendaTankFarm->bulan;
        })->sortKeys();

        // 42 Fields sequential list (Rows 8 to 49)
        $fields = [
            'kelistrikan_pompa_sumur_1',
            'kelistrikan_pompa_sumur_2',
            'kelistrikan_pompa_sumur_4',
            'kelistrikan_pompa_sumur_5',
            'pressure_pompa_sumur_1',
            'pressure_pompa_sumur_2',
            'pressure_pompa_sumur_4',
            'pressure_pompa_sumur_5',
            'flow_meter_pompa_sumur_1',
            'flow_meter_pompa_sumur_2',
            'flow_meter_pompa_sumur_4',
            'flow_meter_pompa_sumur_5',
            'drain_lumpur_settling_tank',
            'kelistrikan_pompa_10p3',
            'kelistrikan_pompa_10p3a',
            'pressure_gauge_intermediate',
            'level_bandul_tank_farm',
            'flow_meter_fresh_water_tank',
            'flow_meter_fwt_to_ro',
            'kelistrikan_pompa_10p4',
            'kelistrikan_pompa_10p4a',
            'pressure_gauge_pompa_10p4_p4a',
            'kelistrikan_pompa_10p5',
            'kelistrikan_pompa_10p5a',
            'kelistrikan_pompa_10p5b',
            'flow_meter_ro_reject_tank',
            'pressure_gauge_pompa_10p5_10p5a',
            'drain_lumpur_tangki_intermediate',
            'inspeksi_all_pompa_tf_intermediate',
            'inspeksi_pompa_20p1',
            'inspeksi_pompa_20p1a',
            'kelistrikan_pompa_20p2',
            'kelistrikan_pompa_20p2a',
            'kelistrikan_pompa_60p1',
            'kelistrikan_pompa_60p2',
            'kelistrikan_pompa_60p3',
            'pressure_gauge_pompa_60p1',
            'pressure_gauge_pompa_60p2',
            'pressure_gauge_pompa_60p3',
            'baterai_pompa_60p3',
            'bahan_bakar_pompa_60p3',
            'pressure_gauge_water_tank_hydrant',
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

                foreach ($fields as $index => $field) {
                    $rowNum = 7 + $index;
                    $val = $item->{$field};
                    $symbol = '';
                    if ($val === 'OK') {
                        $symbol = '✓';
                    } elseif ($val === 'NOK') {
                        $symbol = '✗';
                    }
                    $sheet->setCellValue($colLetter . $rowNum, $symbol);
                }
            }

            // TTD / Approval Section
            // stiker approved ada di B51, N51 dan AA51
            // username ada A55, H55 dan U55
            // time approved di A56, H56 dan U56
            $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');
            $mainRecord = $monthRecords->first()->agendaTankFarm;

            if ($mainRecord) {
                $hasSticker = file_exists($signaturePath);

                // Operator (B51 = Sticker, A55 = Username, A56 = Created At)
                if (in_array($mainRecord->status, ['draft', 'submitted', 'approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathOp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tf_sig_op_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathOp);
                        $tempFiles[] = $tempPathOp;

                        $drawingOperator = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingOperator->setName('Submitted Operator ' . $monthNum);
                        $drawingOperator->setPath($tempPathOp);
                        $drawingOperator->setHeight(50);
                        $drawingOperator->setCoordinates('B51');
                        $drawingOperator->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('A55', $mainRecord->operator ? $mainRecord->operator->username : '-');
                    $sheet->setCellValue('A56', $mainRecord->created_at ? Carbon::parse($mainRecord->created_at)->format('d/m/Y H:i') : '-');
                }

                // Foreman (N51 = Sticker, H55 = Username, H56 = Approved Foreman At)
                if (in_array($mainRecord->status, ['approved_foreman', 'approved_supervisor'])) {
                    if ($hasSticker) {
                        $tempPathFm = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tf_sig_fm_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathFm);
                        $tempFiles[] = $tempPathFm;

                        $drawingForeman = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingForeman->setName('Approved Foreman ' . $monthNum);
                        $drawingForeman->setPath($tempPathFm);
                        $drawingForeman->setHeight(50);
                        $drawingForeman->setCoordinates('N51');
                        $drawingForeman->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('H55', $mainRecord->foreman ? $mainRecord->foreman->username : '-');
                    $sheet->setCellValue('H56', $mainRecord->approved_foreman_at ? Carbon::parse($mainRecord->approved_foreman_at)->format('d/m/Y H:i') : '-');
                }

                // Supervisor (AA51 = Sticker, U55 = Username, U56 = Approved Supervisor At)
                if ($mainRecord->status == 'approved_supervisor') {
                    if ($hasSticker) {
                        $tempPathSpv = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tf_sig_spv_' . $monthNum . '_' . uniqid() . '.png';
                        copy($signaturePath, $tempPathSpv);
                        $tempFiles[] = $tempPathSpv;

                        $drawingSupervisor = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawingSupervisor->setName('Approved Supervisor ' . $monthNum);
                        $drawingSupervisor->setPath($tempPathSpv);
                        $drawingSupervisor->setHeight(50);
                        $drawingSupervisor->setCoordinates('AA51');
                        $drawingSupervisor->setWorksheet($sheet);
                    }
                    $sheet->setCellValue('U55', $mainRecord->supervisor ? $mainRecord->supervisor->username : '-');
                    $sheet->setCellValue('U56', $mainRecord->approved_supervisor_at ? Carbon::parse($mainRecord->approved_supervisor_at)->format('d/m/Y H:i') : '-');
                }
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Agenda_Tank_Farm_Report_' . ($request->bulan ? Carbon::create()->month((int)$request->bulan)->translatedFormat('F') : 'All') . '_' . ($request->tahun ?? date('Y')) . '.xlsx';

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
