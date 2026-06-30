<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\UtilityMonthlyApproval;
use App\Models\Utility\PemakaianListrikModel;
use App\Models\Utility\PemakaianAirModel;
use App\Models\Utility\PemakaianChemicalModel;
use App\Models\NotificationsModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class UtilityApprovalController extends Controller
{
    public function approvalView()
    {
        return view('utility.approval_monthly');
    }

    public function checkApproval(Request $request)
    {
        $request->validate([
            'bulan' => 'required|string',
            'tipe' => 'required|string|in:listrik,air,chemical'
        ]);
        $approval = UtilityMonthlyApproval::where('bulan', $request->bulan)
            ->where('tipe', $request->tipe)
            ->first();
        return response()->json([
            'status' => $approval ? $approval->status : 'none'
        ]);
    }

    public function getCollectedData()
    {
        // 1. Scan distinct months from Listrik table
        $listrikMonths = PemakaianListrikModel::selectRaw("DATE_FORMAT(waktu, '%Y-%m') as bulan")
            ->distinct()
            ->pluck('bulan')
            ->toArray();

        // 2. Scan distinct months from Air table
        $airMonths = PemakaianAirModel::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan")
            ->distinct()
            ->pluck('bulan')
            ->toArray();

        // 3. Scan distinct months from Chemical table
        $chemicalMonths = PemakaianChemicalModel::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan")
            ->distinct()
            ->pluck('bulan')
            ->toArray();

        // Ensure monthly approval records exist in database for each type
        foreach ($listrikMonths as $bulan) {
            if ($bulan) {
                UtilityMonthlyApproval::firstOrCreate(
                    ['bulan' => $bulan, 'tipe' => 'listrik'],
                    ['status' => 'draft']
                );
            }
        }

        foreach ($airMonths as $bulan) {
            if ($bulan) {
                UtilityMonthlyApproval::firstOrCreate(
                    ['bulan' => $bulan, 'tipe' => 'air'],
                    ['status' => 'draft']
                );
            }
        }

        foreach ($chemicalMonths as $bulan) {
            if ($bulan) {
                UtilityMonthlyApproval::firstOrCreate(
                    ['bulan' => $bulan, 'tipe' => 'chemical'],
                    ['status' => 'draft']
                );
            }
        }

        // Fetch draft or rejected approvals
        $results = UtilityMonthlyApproval::whereIn('status', ['draft', 'rejected'])
            ->with(['operator', 'foreman', 'supervisor'])
            ->orderBy('bulan', 'desc')
            ->orderBy('tipe', 'asc')
            ->get();

        return response()->json([
            'status' => 200,
            'results' => $results
        ]);
    }

    public function submitMonthly(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required|string', // Format: YYYY-MM
            'tipe' => 'required|string|in:listrik,air,chemical',
            'foreman_id' => 'required|exists:users,id',
            'supervisor_id' => 'nullable|exists:users,id',
        ]);

        $main = UtilityMonthlyApproval::where('bulan', $validated['bulan'])
            ->where('tipe', $validated['tipe'])
            ->first();

        if (!$main) {
            return response()->json(['message' => 'Laporan untuk bulan dan tipe ini tidak ditemukan.'], 404);
        }

        $main->update([
            'foreman_id' => $validated['foreman_id'],
            'supervisor_id' => $validated['supervisor_id'] ?? null,
            'status' => 'submitted',
            'submitted_at' => now(),
            'operator_id' => Auth::id(),
            'reject_reason' => null
        ]);

        // Send notification to Foreman
        $bulanFormatted = Carbon::parse($main->bulan . '-01')->translatedFormat('F Y');
        $tipeFormatted = ucfirst($main->tipe);
        NotificationsModel::create([
            'user_id' => $main->foreman_id,
            'title' => "Approval Bulanan Utility ({$tipeFormatted})",
            'message' => "Laporan Pemakaian {$tipeFormatted} Bulan {$bulanFormatted} menunggu persetujuan Anda.",
            'url' => url('/utility/approval'),
            'notifiable_type' => UtilityMonthlyApproval::class,
            'notifiable_id' => $main->id,
            'is_read' => 0,
        ]);

        return response()->json([
            'message' => "Laporan bulanan {$main->tipe} berhasil disubmit untuk approval."
        ]);
    }

    public function getApprovalList(Request $request)
    {
        $tab = $request->input('tab', 'pending');
        $user = Auth::user();
        $jabatan = $user->jabatan;
        $userId = $user->id;

        $query = UtilityMonthlyApproval::with(['operator', 'foreman', 'supervisor']);

        if ($tab === 'pending') {
            if ($jabatan === 'foreman') {
                $query->where('foreman_id', $userId)
                    ->where('status', 'submitted');
            } elseif ($jabatan === 'supervisor') {
                $query->where('supervisor_id', $userId)
                    ->where('status', 'approved_foreman');
            } elseif (in_array($jabatan, ['admin', 'dept_head'])) {
                $query->whereIn('status', ['submitted', 'approved_foreman']);
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            // History tab
            if ($jabatan === 'foreman') {
                $query->where('foreman_id', $userId)
                    ->whereIn('status', ['approved_foreman', 'approved_supervisor', 'rejected']);
            } elseif ($jabatan === 'supervisor') {
                $query->where('supervisor_id', $userId)
                    ->whereIn('status', ['approved_supervisor', 'rejected']);
            } else {
                // Admin, Dept Head, and Operators see history of submissions
                if ($jabatan === 'operator') {
                    $query->where('operator_id', $userId);
                }
            }
        }

        $data = $query->orderBy('bulan', 'desc')->orderBy('tipe', 'asc')->get();
        return response()->json($data);
    }

    public function showMonthlyDetails($id)
    {
        $approval = UtilityMonthlyApproval::with(['operator', 'foreman', 'supervisor'])->findOrFail($id);
        $bulan = $approval->bulan; // e.g. YYYY-MM
        $year = date('Y', strtotime($bulan . '-01'));
        $month = date('m', strtotime($bulan . '-01'));

        $listrikRaw = [];
        $airRaw = [];
        $chemicalRaw = [];

        if ($approval->tipe === 'listrik') {
            $listrikRaw = PemakaianListrikModel::whereYear('waktu', $year)
                ->whereMonth('waktu', $month)
                ->orderBy('waktu', 'asc')
                ->get();
        } elseif ($approval->tipe === 'air') {
            $airRaw = PemakaianAirModel::whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->orderBy('tanggal', 'asc')
                ->get();
        } elseif ($approval->tipe === 'chemical') {
            $chemicalRaw = PemakaianChemicalModel::whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->orderBy('tanggal', 'asc')
                ->get();
        }

        return response()->json([
            'approval' => $approval,
            'listrik' => $listrikRaw,
            'air' => $airRaw,
            'chemical' => $chemicalRaw
        ]);
    }

    public function approve(Request $request, $id)
    {
        $approval = UtilityMonthlyApproval::findOrFail($id);
        $user = Auth::user();
        $jabatan = $user->jabatan;
        $userId = $user->id;
        $bulanFormatted = Carbon::parse($approval->bulan . '-01')->translatedFormat('F Y');
        $tipeFormatted = ucfirst($approval->tipe);

        if ($jabatan === 'foreman') {
            if ((int)$approval->foreman_id !== $userId) {
                return response()->json(['message' => 'Anda tidak berwenang menyetujui laporan ini.'], 403);
            }
            if ($approval->status !== 'submitted') {
                return response()->json(['message' => 'Status laporan tidak valid untuk disetujui Foreman.'], 422);
            }

            $validated = $request->validate([
                'supervisor_id' => 'required|exists:users,id'
            ]);

            $approval->update([
                'status' => 'approved_foreman',
                'foreman_approved_at' => now(),
                'supervisor_id' => $validated['supervisor_id'],
                'reject_reason' => null
            ]);

            // Clear foreman notification
            NotificationsModel::where('notifiable_type', UtilityMonthlyApproval::class)
                ->where('notifiable_id', $approval->id)
                ->where('user_id', $userId)
                ->delete();

            // Notify Supervisor
            if ($approval->supervisor_id) {
                NotificationsModel::create([
                    'user_id' => $approval->supervisor_id,
                    'title' => "Approval Bulanan Utility ({$tipeFormatted})",
                    'message' => "Laporan Pemakaian {$tipeFormatted} Bulan {$bulanFormatted} telah disetujui Foreman dan menunggu persetujuan Anda.",
                    'url' => url('/utility/approval'),
                    'notifiable_type' => UtilityMonthlyApproval::class,
                    'notifiable_id' => $approval->id,
                    'is_read' => 0,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => "Laporan bulanan {$approval->tipe} berhasil disetujui oleh Foreman."
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

            // Clear supervisor notification
            NotificationsModel::where('notifiable_type', UtilityMonthlyApproval::class)
                ->where('notifiable_id', $approval->id)
                ->where('user_id', $userId)
                ->delete();

            return response()->json([
                'status' => 'success',
                'message' => "Laporan bulanan {$approval->tipe} berhasil disetujui oleh Supervisor (Selesai)."
            ]);
        }

        return response()->json(['message' => 'Role Anda tidak memiliki otoritas approval.'], 403);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $approval = UtilityMonthlyApproval::findOrFail($id);
        $user = Auth::user();
        $jabatan = $user->jabatan;
        $userId = $user->id;
        $bulanFormatted = Carbon::parse($approval->bulan . '-01')->translatedFormat('F Y');
        $tipeFormatted = ucfirst($approval->tipe);

        $isForeman = ($jabatan === 'foreman' && (int)$approval->foreman_id === $userId && $approval->status === 'submitted');
        $isSupervisor = ($jabatan === 'supervisor' && (int)$approval->supervisor_id === $userId && $approval->status === 'approved_foreman');

        if (!$isForeman && !$isSupervisor) {
            return response()->json(['message' => 'Anda tidak memiliki wewenang untuk menolak laporan ini pada tahap ini.'], 403);
        }

        $approval->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        // Delete notifications for this approval for current user
        NotificationsModel::where('notifiable_type', UtilityMonthlyApproval::class)
            ->where('notifiable_id', $approval->id)
            ->where('user_id', $userId)
            ->delete();

        // Notify Operator
        if ($approval->operator_id) {
            NotificationsModel::create([
                'user_id' => $approval->operator_id,
                'title' => "Laporan Bulanan Utility ({$tipeFormatted}) Ditolak",
                'message' => "Laporan Pemakaian {$tipeFormatted} Bulan {$bulanFormatted} ditolak. Alasan: {$request->reason}",
                'url' => url('/utility/data'),
                'notifiable_type' => UtilityMonthlyApproval::class,
                'notifiable_id' => $approval->id,
                'is_read' => 0,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Laporan bulanan {$approval->tipe} berhasil ditolak."
        ]);
    }
}
