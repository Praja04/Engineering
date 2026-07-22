<?php

namespace App\Http\Controllers\Epr;

use App\Http\Controllers\Controller;
use App\Models\Epr\WorkOrder;
use App\Models\Epr\WoAssignee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkOrderController extends Controller
{
    // ── Management WO (Foreman) ──

    public function index()
    {
        return view('epr.work-orders.index');
    }

    public function getWorkOrders(Request $request)
    {
        $query = WorkOrder::with(['creator', 'approver', 'assignees.user', 'reports'])
            ->orderBy('created_at', 'desc');

        // Optional filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('area')) {
            $query->where('area', $request->area);
        }
        if ($request->filled('month')) {
            $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$request->month]);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('wo_number', 'like', "%$s%")
                  ->orWhere('title', 'like', "%$s%")
                  ->orWhere('machine', 'like', "%$s%")
                  ->orWhere('area', 'like', "%$s%");
            });
        }

        $workOrders = $query->limit(500)->get();

        $mapped = $workOrders->map(function ($wo) {
            return [
                'id'           => $wo->id,
                'wo_number'    => $wo->wo_number,
                'title'        => $wo->title,
                'description'  => $wo->description,
                'area'         => $wo->area,
                'machine'      => $wo->machine,
                'priority'     => $wo->priority,
                'status'       => $wo->status,
                'target_date'  => $wo->target_date ? $wo->target_date->format('Y-m-d') : null,
                'created_by'   => $wo->creator->username ?? '-',
                'approved_by'  => $wo->approver->username ?? null,
                'approved_at'  => $wo->approved_at?->format('d M Y H:i'),
                'reject_reason' => $wo->reject_reason,
                'created_at'   => $wo->created_at->format('Y-m-d'),
                'assignees'    => $wo->assignees->map(fn($a) => [
                    'id'       => $a->id,
                    'user_id'  => $a->user_id,
                    'username' => $a->user->username ?? '-',
                    'duration' => $a->duration_minutes,
                    'note'     => $a->note,
                ]),
                'report_count' => $wo->reports->count(),
            ];
        });

        return response()->json($mapped);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'area'        => 'required|string',
            'priority'    => 'required|in:critical,high,medium,low',
            'assignees'   => 'required|array|min:1',
            'assignees.*.user_id' => 'required|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $id = $request->input('id');

            if ($id) {
                // Update existing WO
                $wo = WorkOrder::findOrFail($id);
                $wo->update([
                    'title'       => $request->title,
                    'description' => $request->description,
                    'area'        => $request->area,
                    'machine'     => $request->machine,
                    'priority'    => $request->priority,
                    'target_date' => $request->target_date,
                ]);

                // Replace assignees
                $wo->assignees()->delete();
            } else {
                // Create new WO
                $wo = WorkOrder::create([
                    'wo_number'   => WorkOrder::generateWoNumber(),
                    'title'       => $request->title,
                    'description' => $request->description,
                    'area'        => $request->area,
                    'machine'     => $request->machine,
                    'priority'    => $request->priority,
                    'status'      => 'assigned',
                    'target_date' => $request->target_date,
                    'created_by'  => Auth::id(),
                ]);
            }

            // Insert assignees
            foreach ($request->assignees as $assignee) {
                WoAssignee::create([
                    'work_order_id'    => $wo->id,
                    'user_id'          => $assignee['user_id'],
                    'duration_minutes' => $assignee['duration'] ?? null,
                    'note'             => $assignee['note'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'wo' => $wo->fresh()->load('assignees.user')]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $wo = WorkOrder::findOrFail($id);
        if (!in_array($wo->status, ['open', 'assigned'])) {
            return response()->json(['success' => false, 'message' => 'Hanya WO berstatus Open/Assigned yang bisa dihapus'], 422);
        }
        $wo->delete();
        return response()->json(['success' => true]);
    }

    // ── Assignable Users ──

    public function getAssignableUsers()
    {
        $operators = User::where('jabatan', 'operator')
            ->where('bagian', 'Engineering Produksi (EPR)')
            ->select('id', 'username', 'jabatan', 'bagian')
            ->orderBy('username')
            ->get();

        // Also include foreman (they might do field work too)
        $foremen = User::where('jabatan', 'foreman')
            ->where('bagian', 'Engineering Produksi (EPR)')
            ->select('id', 'username', 'jabatan', 'bagian')
            ->orderBy('username')
            ->get();

        return response()->json([
            'operators' => $operators,
            'foremen'   => $foremen,
        ]);
    }

    // ── WO assigned to current user (for Operator form dropdown) ──

    public function getMyWorkOrders()
    {
        $userId = Auth::id();
        $assignedWoIds = WoAssignee::where('user_id', $userId)->pluck('work_order_id');

        $workOrders = WorkOrder::with('assignees.user')
            ->whereIn('id', $assignedWoIds)
            ->whereIn('status', ['assigned', 'progress'])
            ->orderBy('priority')
            ->orderBy('target_date')
            ->get();

        $mapped = $workOrders->map(fn($wo) => [
            'id'          => $wo->id,
            'wo_number'   => $wo->wo_number,
            'title'       => $wo->title,
            'area'        => $wo->area,
            'machine'     => $wo->machine,
            'priority'    => $wo->priority,
            'target_date' => $wo->target_date?->format('Y-m-d'),
            'my_duration' => $wo->assignees->firstWhere('user_id', $userId)?->duration_minutes,
            'my_note'     => $wo->assignees->firstWhere('user_id', $userId)?->note,
        ]);

        return response()->json($mapped);
    }

    // ── Approval (Supervisor) ──

    public function approvalIndex()
    {
        return view('epr.work-orders.approval');
    }

    public function approve(Request $request, $id)
    {
        $wo = WorkOrder::findOrFail($id);
        if ($wo->status !== 'done') {
            return response()->json(['success' => false, 'message' => 'WO belum berstatus Done'], 422);
        }

        $wo->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'WO berhasil diapprove']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);

        $wo = WorkOrder::findOrFail($id);
        if ($wo->status !== 'done') {
            return response()->json(['success' => false, 'message' => 'WO belum berstatus Done'], 422);
        }

        $wo->update([
            'status'        => 'rejected',
            'reject_reason' => $request->reason,
            'approved_by'   => Auth::id(),
            'approved_at'   => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'WO ditolak']);
    }
}
