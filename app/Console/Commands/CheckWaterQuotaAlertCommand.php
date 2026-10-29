<?php

namespace App\Console\Commands;

use App\Mail\WaterQuotaAlertMail;
use App\Models\User;
use App\Models\Utility\AirArea;
use App\Models\Utility\PemakaianAirModel;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckWaterQuotaAlertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utility:check-water-quota 
                            {--force : Force send email alert even if already alerted this month}
                            {--test : Run in simulation mode without sending actual emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check raw water sources (Sumur 1, 2, 4, 5, PDAM) and send email alert to supervisors if usage reaches 80% of monthly quota';

    /**
     * Raw water sources to monitor
     *
     * @var array
     */
    protected array $rawSources = ['Sumur 1', 'Sumur 2', 'Sumur 4', 'Sumur 5', 'PDAM'];

    /**
     * Default daily quotas if not set in DB
     *
     * @var array
     */
    protected array $defaultQuotas = [
        'Sumur 1' => 59.00,
        'Sumur 2' => 73.00,
        'Sumur 4' => 28.00,
        'Sumur 5' => 32.00,
        'PDAM'    => 14.00,
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isForce = (bool) $this->option('force');
        $isTest = (bool) $this->option('test');

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();
        $daysInMonth = $now->daysInMonth;
        $monthName = $now->translatedFormat('F');
        $year = $now->year;

        $this->info("=== Pengecekan Kuota Air Mentah (≥ 80%) ===");
        $this->line("Periode: {$monthName} {$year} ({$daysInMonth} hari | {$startOfMonth} s.d. {$endOfMonth})");
        $this->line("Tanggal Check: " . $now->toDateTimeString());

        // 1. Ambil target kuota harian dari AirArea
        $dbQuotas = AirArea::whereIn('nama_area', $this->rawSources)
            ->pluck('quota_target', 'nama_area')
            ->toArray();

        // 2. Ambil pemakaian aktual bulan berjalan
        $records = PemakaianAirModel::query()
            ->select('jenis_pemakaian', DB::raw('SUM(pemakaian_akhir - pemakaian_awal) AS total_pemakaian'))
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->whereIn('jenis_pemakaian', $this->rawSources)
            ->groupBy('jenis_pemakaian')
            ->get()
            ->keyBy('jenis_pemakaian');

        $tableData = [];
        $alertItems = [];

        foreach ($this->rawSources as $source) {
            $dailyQuota = isset($dbQuotas[$source]) && $dbQuotas[$source] !== null
                ? (float) $dbQuotas[$source]
                : ($this->defaultQuotas[$source] ?? 0.0);

            // Perhitungan: Kuota 1 bulan penuh = daily_quota * jumlah hari di bulan ini
            $monthlyQuota = round($dailyQuota * $daysInMonth, 2);
            $threshold80 = round($monthlyQuota * 0.8, 2);

            $actual = isset($records[$source])
                ? round((float) $records[$source]->total_pemakaian, 2)
                : 0.0;

            $percentage = $monthlyQuota > 0
                ? round(($actual / $monthlyQuota) * 100, 1)
                : 0.0;

            $isExceeded80 = $actual >= $threshold80;

            $cacheKey = "water_quota_alert_sent_{$source}_{$year}_{$now->month}";
            $alreadyAlerted = Cache::has($cacheKey);

            $statusText = 'Normal';
            if ($percentage >= 100) {
                $statusText = 'OVER KUOTA (≥100%)';
            } elseif ($isExceeded80) {
                $statusText = 'WASPADA (≥80%)';
            }

            $tableData[] = [
                'Sumber'             => $source,
                'Kuota Harian (m³)' => number_format($dailyQuota, 1, ',', '.'),
                'Kuota 1 Bulan (m³)'=> number_format($monthlyQuota, 1, ',', '.'),
                'Ambang 80% (m³)'   => number_format($threshold80, 1, ',', '.'),
                'Aktual (m³)'       => number_format($actual, 1, ',', '.'),
                'Pencapaian (%)'    => $percentage . '%',
                'Status'             => $statusText,
                'Sudah Kirim?'       => $alreadyAlerted ? 'Ya' : 'Belum',
            ];

            // Cek apakah perlu kirim notifikasi
            if ($isExceeded80) {
                if ($isForce || !$alreadyAlerted) {
                    $alertItems[] = [
                        'source'        => $source,
                        'daily_quota'   => $dailyQuota,
                        'monthly_quota' => $monthlyQuota,
                        'threshold_80'  => $threshold80,
                        'actual'        => $actual,
                        'percentage'    => $percentage,
                        'cache_key'     => $cacheKey,
                    ];
                } else {
                    $this->warn("Sumber [{$source}] sudah melampaui 80% ({$percentage}%), namun notifikasi bulan ini sudah pernah terkirim sebelumnya.");
                }
            }
        }

        $this->table(
            ['Sumber', 'Kuota Harian (m³)', 'Kuota 1 Bulan (m³)', 'Ambang 80% (m³)', 'Aktual (m³)', 'Pencapaian (%)', 'Status', 'Alert Terkirim?'],
            $tableData
        );

        if (empty($alertItems)) {
            $this->info("Tidak ada sumber air mentah baru yang mencapai threshold 80% kuota bulanan untuk dikirimi email.");
            return Command::SUCCESS;
        }

        $this->warn("Ditemukan " . count($alertItems) . " sumber air yang mencapai/melebihi 80% kuota bulanan!");

        // 3. Ambil user dengan jabatan supervisor
        $supervisors = User::where('is_active', true)
            ->where(function ($q) {
                $q->where('jabatan', 'LIKE', '%supervisor%')
                  ->orWhere('jabatan', 'LIKE', '%spv%');
            })
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        if ($supervisors->isEmpty()) {
            $this->error("Tidak ditemukan user supervisor aktif dengan alamat email valid.");
            return Command::FAILURE;
        }

        $this->info("Ditemukan {$supervisors->count()} user supervisor penerima notifikasi.");

        if ($isTest) {
            $this->warn("[MODE TEST] Email tidak dikirim ke supervisor secara riil. Data simulasi:");
            foreach ($supervisors as $spv) {
                $recipientName = $spv->fullname ?: ($spv->username ?: 'Supervisor');
                $this->line("- [TEST] Penerima: {$recipientName} ({$spv->email}) | Jabatan: {$spv->jabatan} | Dept: {$spv->departemen}");
            }
            return Command::SUCCESS;
        }

        // 4. Kirim email ke setiap supervisor
        $sentCount = 0;
        foreach ($supervisors as $supervisor) {
            $recipientName = $supervisor->fullname ?: ($supervisor->username ?: 'Supervisor');
            try {
                $this->line("Mengirim email ke: {$recipientName} <{$supervisor->email}>...");
                Mail::to($supervisor->email)->send(new WaterQuotaAlertMail(
                    $alertItems,
                    $recipientName,
                    $monthName,
                    $year,
                    $daysInMonth
                ));
                $sentCount++;
                $this->info("✓ Sukses terkirim ke {$supervisor->email}");
            } catch (\Throwable $e) {
                $this->error("✗ Gagal mengirim email ke {$supervisor->email}: " . $e->getMessage());
                Log::error("WaterQuotaAlertMail error for {$supervisor->email}: " . $e->getMessage());
            }
        }

        // 5. Tandai cache untuk sumber yang berhasil diproses agar tidak duplikat di run berikutnya (TTL s.d akhir bulan)
        if ($sentCount > 0) {
            $secondsUntilEndOfMonth = $now->diffInSeconds($now->copy()->endOfMonth());
            foreach ($alertItems as $item) {
                Cache::put($item['cache_key'], [
                    'sent_at'    => $now->toDateTimeString(),
                    'actual'     => $item['actual'],
                    'percentage' => $item['percentage'],
                ], $secondsUntilEndOfMonth);
            }
            $this->info("Berhasil mengirim notifikasi email kuota air ke {$sentCount} supervisor.");
        }

        return Command::SUCCESS;
    }
}
