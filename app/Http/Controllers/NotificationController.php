<?php

namespace App\Http\Controllers;

use App\Models\Kalibrasi\KalibrasiSertifikatApprovalModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function kalibrasiCertificate()
    {
        $userId = Auth::id();

        $approvals = KalibrasiSertifikatApprovalModel::with(['sertifikat'])
            ->where('approver_id', $userId)
            ->whereIn('status', ['pending', 'read'])
            ->orderByDesc('created_at')
            ->get();

        $notifications = $approvals->map(function ($a) {
            return [
                'id' => $a->id,
                'title' => 'Persetujuan Diperlukan',
                'message' => 'Sertifikat kalibrasi tanggal ' . $a->sertifikat->created_at . ' menunggu persetujuan Anda.',
                'url' => route('kalibrasi.certificate.approvals'), // arahkan ke halaman approval
                'created_at' => $a->created_at->diffForHumans(),
                'is_read' => $a->status === 'read',
            ];
        });

        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $notif = KalibrasiSertifikatApprovalModel::find($id);

        if (!$notif) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }

        $notif->update(['status' => 'read']);

        return response()->json(['status' => 'success']);
    }
}
