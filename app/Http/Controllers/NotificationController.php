<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationsModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Kalibrasi\KalibrasiSertifikatApprovalModel;

class NotificationController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        // $approvalotification = $this->getSopApprovalNotification($userId);
        // $barangBaruNotifications = $this->getBarangBaruNotifications($user);
        $kalibrasiCertificate = $this->kalibrasiCertificate($userId);

        $notifications = NotificationsModel::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'url' => $n->url,
                    'type' => $n->type,
                    'created_at' => $n->created_at->format('d F Y, H:i'),
                    'is_read' => $n->is_read
                ];
            });

        return response()->json($notifications);
    }

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

        // return response()->json($notifications);
        return $notifications;
    }

    public function markAsRead($id)
    {
        $notif = NotificationsModel::where('id', $id)
            // ->where('user_id', Auth::id())
            ->first();

        // Log::info("MarkAsRead dipanggil untuk ID: {$id}");

        if (!$notif) {
            // Log::warning("Notifikasi ID {$id} TIDAK ditemukan!");

            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }

        $notif->update([
            'is_read' => true
        ]);

        // Log::info("Notifikasi ID {$id} berhasil ditandai read.");

        return response()->json([
            'success' => true,
            'message' => 'Update is read'
        ]);
    }
}
