<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\NotificationsModel;
use App\Models\User;
use App\Models\Utility\Ahu;
use App\Models\Utility\AhuDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

        $this->sendNotification($main);

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
        if ($data->foreman_id !== auth()->id()) return response()->json(['message' => 'Forbidden'], 403);
        $data->update(['approved_foreman_at' => now(), 'status' => 'approved_foreman']);
        return response()->json(['message' => 'Disetujui Foreman']);
    }

    public function approveSupervisor($id)
    {
        $data = Ahu::findOrFail($id);
        if ($data->supervisor_id !== auth()->id()) return response()->json(['message' => 'Forbidden'], 403);
        $data->update(['approved_supervisor_at' => now(), 'status' => 'approved_supervisor']);
        return response()->json(['message' => 'Disetujui Supervisor']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);
        $data = Ahu::findOrFail($id);
        $data->update(['status' => 'rejected', 'reject_reason' => $request->reason]);
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
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc');

        if ($request->filled('bulan')) {
            $date = Carbon::parse($request->bulan);
            $query->whereYear('tanggal', $date->year)->whereMonth('tanggal', $date->month);
        }

        return response()->json(['status' => 200, 'data' => $query->get()]);
    }

    public function destroy($id)
    {
        $data = AhuDetails::findOrFail($id);
        $data->delete();
        return response()->json(['status' => 200, 'message' => 'Data dihapus']);
    }

    private function sendNotification($main)
    {
        $approvalUrl = url(route('ahu.approval', [], false));
        $recipients = User::whereIn('id', array_filter([$main->foreman_id, $main->supervisor_id]))->get();

        foreach ($recipients as $user) {
            NotificationsModel::updateOrCreate(
                ['user_id' => $user->id, 'notifiable_type' => Ahu::class, 'notifiable_id' => $main->id, 'is_read' => 0],
                ['title' => 'Approval Bulanan AHU', 'message' => "Laporan AHU Bulan {$main->bulan} {$main->tahun} menunggu persetujuan", 'url' => $approvalUrl]
            );
        }
    }
}
