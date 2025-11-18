<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationHelper
{
    public static function pushToPortalUser($approval)
    {
        try {
            switch ($approval->approver->departemen) {
                case 'warehouse':
                    // $portalUrl = 'https://portal-user-wh.company.com/api/notifications';
                    $portalUrl = 'http://localhost:8081/api/notifications/show/kalibrasi';
                    break;
                case 'quality_control':
                    $portalUrl = 'http://localhost:8081/api/notifications/show/kalibrasi';
                    break;
                case 'produksi':
                    $portalUrl = 'http://localhost:8081/api/notifications/show/kalibrasi';
                    break;
                default:
                    Log::warning("Departemen {$approval->approver->departemen} tidak dikenali, notifikasi tidak dikirim.");
                    return;
            }

            // Data yang dikirim ke portal user
            $data = [
                'approver_id' => $approval->approver_id,
                'title' => 'Persetujuan Diperlukan',
                'message' => 'Sertifikat kalibrasi tanggal ' .
                    $approval->sertifikat->created_at->format('d-m-Y') .
                    ' menunggu persetujuan Anda.',
                'url' => route('kalibrasi.certificate.approvals'),
            ];

            // Kirim request dengan header rahasia antar portal
            Http::withHeaders([
                'X-Internal-Key' => env('INTERNAL_PORTAL_KEY', 'DEFAULT_SECRET_2025'),
            ])->post($portalUrl, $data);

            Log::info("Notifikasi dikirim ke portal {$approval->approver->departemen} ({$approval->approver_id})");
        } catch (\Exception $e) {
            Log::error('Gagal kirim notifikasi portal user: ' . $e->getMessage());
        }
    }
}
