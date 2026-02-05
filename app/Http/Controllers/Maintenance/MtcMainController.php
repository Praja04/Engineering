<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Http\Request;
use App\Models\NotificationsModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Maintenance\MtcMainModel;
use App\Models\Maintenance\MtcApprovalModel;

class MtcMainController extends Controller
{
    public function tracking($id)
    {
        $approvals = MtcApprovalModel::where('mtc_main_id', $id)
            ->with('approver:id,username')
            ->orderBy('level')
            ->get()
            ->map(function ($a) {
                return [
                    'level'     => $a->level,
                    'role'      => ucfirst($a->role),
                    'status'    => $a->status,
                    'approver'  => $a->approver->username ?? '-',
                    'catatan'   => $a->catatan,
                    'action_at' => $a->action_at,
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => $approvals
        ]);
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $main = MtcMainModel::findOrFail($id);

            // hapus notification
            NotificationsModel::where([
                'notifiable_type' => MtcMainModel::class,
                'notifiable_id'   => $id
            ])->delete();

            // terakhir hapus main
            $main->delete();
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data maintenance berhasil dihapus',
        ]);
    }
}
