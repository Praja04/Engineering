<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\WwtpDailyApproval;
use App\Models\Utility\WwtpInfluentHarian;
use App\Models\Utility\WwtpPerformancePHharian;
use App\Models\Utility\WwtpSludge;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WWTPControllerApproval extends Controller
{
    public function approvalView()
    {
        return view('utility.wwtp.approval_harian');
    }

    public function checkApproval(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date'
        ]);

        $tanggal = $request->tanggal;

        $approval = WwtpDailyApproval::where('tanggal', $tanggal)
            ->with(['operator', 'foreman', 'supervisor'])
            ->first();

        $foremen = \App\Models\User::where('departemen', 'engineering')
            ->where('jabatan', 'foreman')
            ->get(['id', 'username']);

        $supervisors = \App\Models\User::where('departemen', 'engineering')
            ->where('jabatan', 'supervisor')
            ->get(['id', 'username']);

        return response()->json([
            'approval_exists' => !is_null($approval),
            'approval' => $approval,
            'foremen' => $foremen,
            'supervisors' => $supervisors
        ]);
    }

    public function getApprovalList(Request $request)
    {
        $tab = $request->input('tab', 'pending');
        $user = Auth::user();
        $jabatan = $user->jabatan;
        $userId = $user->id;

        $query = WwtpDailyApproval::with(['operator', 'foreman', 'supervisor']);

        if ($tab === 'pending') {
            if ($jabatan === 'foreman') {
                $query->where('foreman_id', $userId)
                    ->where('status', 'submitted');
            } elseif ($jabatan === 'supervisor') {
                $query->where('supervisor_id', $userId)
                    ->where('status', 'approved_foreman');
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            if ($jabatan === 'foreman') {
                $query->where('foreman_id', $userId)
                    ->whereIn('status', ['approved_foreman', 'approved_supervisor', 'rejected']);
            } elseif ($jabatan === 'supervisor') {
                $query->where('supervisor_id', $userId)
                    ->whereIn('status', ['approved_supervisor', 'rejected']);
            } else {
                $query->where('operator_id', $userId);
            }
        }

        $data = $query->orderBy('tanggal', 'desc')->get();
        return response()->json($data);
    }

    public function show($id)
    {
        $approval = WwtpDailyApproval::with(['operator', 'foreman', 'supervisor'])->findOrFail($id);
        $tanggal = $approval->tanggal->format('Y-m-d');

        $influent = WwtpInfluentHarian::whereDate('tanggal', $tanggal)->orderBy('shift')->get();
        $ph = WwtpPerformancePHharian::whereDate('tanggal', $tanggal)->orderBy('shift')->get();
        $sludge = WwtpSludge::whereDate('tanggal', $tanggal)->orderBy('shift')->get();

        return response()->json([
            'approval' => $approval,
            'influent' => $influent,
            'ph' => $ph,
            'sludge' => $sludge
        ]);
    }

    public function approve(Request $request, $id)
    {
        $approval = WwtpDailyApproval::findOrFail($id);
        $user = Auth::user();
        $jabatan = $user->jabatan;
        $userId = $user->id;

        if ($jabatan === 'foreman') {
            if ((int)$approval->foreman_id !== $userId) {
                return response()->json(['message' => 'Anda tidak berwenang menyetujui laporan ini.'], 403);
            }
            if ($approval->status !== 'submitted') {
                return response()->json(['message' => 'Status laporan tidak valid untuk disetujui Foreman.'], 422);
            }

            $approval->update([
                'status' => 'approved_foreman',
                'foreman_approved_at' => now(),
                'reject_reason' => null
            ]);

            \App\Models\NotificationsModel::where('notifiable_type', WwtpDailyApproval::class)
                ->where('notifiable_id', $approval->id)
                ->where('user_id', $userId)
                ->delete();

            if ($approval->supervisor_id) {
                \App\Models\NotificationsModel::create([
                    'user_id' => $approval->supervisor_id,
                    'title' => 'Approval Harian WWTP',
                    'message' => 'Data harian WWTP tanggal ' . $approval->tanggal->format('d/m/Y') . ' telah disetujui Foreman dan menunggu persetujuan Anda.',
                    'url' => url('/wwtp/approval'),
                    'notifiable_type' => WwtpDailyApproval::class,
                    'notifiable_id' => $approval->id,
                    'is_read' => 0,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data harian berhasil disetujui oleh Foreman.'
            ]);
        } elseif ($jabatan === 'supervisor') {
            if ((int)$approval->supervisor_id !== $userId) {
                return response()->json(['message' => 'Anda tidak berwenang menyetujui laporan ini.'], 403);
            }
            if ($approval->status !== 'approved_foreman') {
                return response()->json(['message' => 'Laporan harus disetujui oleh Foreman terlebih dahulu.'], 422);
            }

            $approval->update([
                'status' => 'approved_supervisor',
                'supervisor_approved_at' => now(),
                'reject_reason' => null
            ]);

            \App\Models\NotificationsModel::where('notifiable_type', WwtpDailyApproval::class)
                ->where('notifiable_id', $approval->id)
                ->where('user_id', $userId)
                ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data harian berhasil disetujui oleh Supervisor (Selesai).'
            ]);
        }

        return response()->json(['message' => 'Role Anda tidak memiliki otoritas approval.'], 403);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $approval = WwtpDailyApproval::findOrFail($id);
        $user = Auth::user();
        $jabatan = $user->jabatan;
        $userId = $user->id;

        $isForeman = ($jabatan === 'foreman' && (int)$approval->foreman_id === $userId && $approval->status === 'submitted');
        $isSupervisor = ($jabatan === 'supervisor' && (int)$approval->supervisor_id === $userId && $approval->status === 'approved_foreman');

        if (!$isForeman && !$isSupervisor) {
            return response()->json(['message' => 'Anda tidak memiliki wewenang untuk menolak laporan ini pada tahap ini.'], 403);
        }

        $approval->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        \App\Models\NotificationsModel::where('notifiable_type', WwtpDailyApproval::class)
            ->where('notifiable_id', $approval->id)
            ->where('user_id', $userId)
            ->delete();

        if ($approval->operator_id) {
            \App\Models\NotificationsModel::create([
                'user_id' => $approval->operator_id,
                'title' => 'Laporan Harian WWTP Ditolak',
                'message' => 'Data harian WWTP tanggal ' . $approval->tanggal->format('d/m/Y') . ' ditolak. Alasan: ' . $request->reason,
                'url' => url('/wwtp/data_proses'),
                'notifiable_type' => WwtpDailyApproval::class,
                'notifiable_id' => $approval->id,
                'is_read' => 0,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan harian berhasil ditolak.'
        ]);
    }
}
