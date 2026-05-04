<?php

namespace App\Http\Controllers;

use App\Models\Maintenance\MtcMainModel;
use App\Models\NotificationsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

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

    public function refresh()
    {
        $userId = Auth::id();

        $notifications = NotificationsModel::where('user_id', $userId)->get();

        foreach ($notifications as $notif) {

            $modelClass = $notif->notifiable_type;

            // pastikan class exists (biar aman)
            if (!class_exists($modelClass)) {
                continue;
            }

            $data = $modelClass::find($notif->notifiable_id);

            // kalau data tidak ada ATAU sudah selesai → hapus
            if (!$data || (isset($data->status) && in_array($data->status, ['approved', 'rejected']))) {
                $notif->delete();
            }
        }

        $total = NotificationsModel::where('user_id', $userId)->count();

        return response()->json([
            'status' => true,
            'total'  => $total
        ]);
    }

    public function destroy($id)
    {
        $notif = NotificationsModel::find($id);

        if (!$notif) {
            return response()->json(['status' => 'error', 'message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $notif->delete();

        return response()->json(['status' => 'success', 'message' => 'Notifikasi berhasil dihapus']);
    }

    public function destroyAll()
    {
        NotificationsModel::truncate();

        return response()->json(['status' => 'success', 'message' => 'Semua notifikasi berhasil dihapus']);
    }
}
