<?php

namespace App\Http\Controllers\Ejo;

use App\Http\Controllers\Controller;
use App\Models\Ejo\EjoTicket;
use App\Models\Ejo\EjoClassification;
use App\Models\Ejo\EjoType;
use Carbon\Carbon;

class EjoDashboardController extends Controller
{
    /**
     * GET /api/ejo/dashboard
     *
     * Satu endpoint yang mengembalikan semua data untuk dashboard:
     * - summary (total, open, done, overdue, avg_progress)
     * - per_classification (jumlah open per klasifikasi)
     * - per_department (jumlah open per departemen)
     * - per_status (breakdown status)
     * - active_tickets (tiket open beserta progress terbaru, max 20)
     * - monthly_trend (6 bulan terakhir: jumlah tiket masuk & selesai)
     */
    public function index()
    {
        $today = Carbon::today();

        // ── Ambil semua tiket sekali, eager load yang diperlukan ──────────
        $allTickets = EjoTicket::with([
            'classification.type',
            'progress' => fn ($q) => $q->latest()->limit(1),
        ])->get();

        $open   = $allTickets->filter(fn ($t) => $t->status !== 'Done');
        $done   = $allTickets->filter(fn ($t) => $t->status === 'Done');

        // Overdue: open + punya schedule + schedule sudah lewat
        $overdue = $open->filter(
            fn ($t) => $t->schedule && Carbon::parse($t->schedule)->lt($today)
        );

        // Avg progress tiket open
        $avgProgress = $open->count()
            ? round($open->avg(fn ($t) => $t->progress->first()?->progress_percent ?? 0))
            : 0;

        // ── Summary ───────────────────────────────────────────────────────
        $summary = [
            'total'        => $allTickets->count(),
            'open'         => $open->count(),
            'done'         => $done->count(),
            'overdue'      => $overdue->count(),
            'avg_progress' => $avgProgress,
            'completion_rate' => $allTickets->count()
                ? round($done->count() / $allTickets->count() * 100)
                : 0,
        ];

        // ── Per klasifikasi (open saja) ───────────────────────────────────
        $perClassification = $open
            ->groupBy('classification_id')
            ->map(function ($tickets, $classId) {
                $first = $tickets->first();
                return [
                    'classification_id'   => $classId,
                    'classification_name' => $first->classification?->name ?? 'Tanpa Klasifikasi',
                    'type_name'           => $first->classification?->type?->name ?? '',
                    'count'               => $tickets->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();

        // ── Per departemen (open saja) ────────────────────────────────────
        $perDepartment = $open
            ->groupBy(fn ($t) => $t->department ?: 'Tanpa Departemen')
            ->map(function ($tickets, $dept) use ($today) {
                $overdueCount = $tickets->filter(
                    fn ($t) => $t->schedule && Carbon::parse($t->schedule)->lt($today)
                )->count();

                return [
                    'department'   => $dept,
                    'count'        => $tickets->count(),
                    'overdue'      => $overdueCount,
                ];
            })
            ->sortByDesc('count')
            ->values();

        // ── Per status (semua tiket) ──────────────────────────────────────
        $perStatus = $allTickets
            ->groupBy(fn ($t) => $t->status ?: 'Tidak Diketahui')
            ->map(fn ($tickets, $status) => [
                'status' => $status,
                'count'  => $tickets->count(),
            ])
            ->sortByDesc('count')
            ->values();

        // ── Tiket aktif dengan progress (untuk list di dashboard) ─────────
        $activeTickets = $open
            ->map(fn ($t) => [
                'id'             => $t->id,
                'ticket_id'      => $t->ticket_id,
                'subject'        => $t->subject,
                'department'     => $t->department,
                'classification' => $t->classification?->name,
                'type'           => $t->classification?->type?->name,
                'schedule'       => $t->schedule?->format('Y-m-d'),
                'is_overdue'     => $t->schedule && Carbon::parse($t->schedule)->lt($today),
                'progress'       => $t->progress->first()?->progress_percent ?? 0,
                'status'         => $t->status,
            ])
            ->sortByDesc('progress')
            ->take(20)
            ->values();

        // ── Monthly trend (6 bulan terakhir) ──────────────────────────────
        $months = collect(range(5, 0))->map(fn ($i) => Carbon::now()->subMonths($i));

        $monthlyTrend = $months->map(function ($month) use ($allTickets) {
            $label = $month->translatedFormat('M Y');

            $created = $allTickets->filter(
                fn ($t) => $t->created_at
                    && Carbon::parse($t->created_at)->isSameMonth($month)
            )->count();

            $completed = $allTickets->filter(
                fn ($t) => $t->date_done
                    && Carbon::parse($t->date_done)->isSameMonth($month)
            )->count();

            return compact('label', 'created', 'completed');
        })->values();

        // ── Response ──────────────────────────────────────────────────────
        return response()->json([
            'summary'            => $summary,
            'per_classification' => $perClassification,
            'per_department'     => $perDepartment,
            'per_status'         => $perStatus,
            'active_tickets'     => $activeTickets,
            'monthly_trend'      => $monthlyTrend,
        ]);
    }
}
