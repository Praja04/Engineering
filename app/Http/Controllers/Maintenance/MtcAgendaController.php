<?php

namespace App\Http\Controllers\Maintenance;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Maintenance\MtcMasterMesinModel;
use Illuminate\Support\Facades\DB;

class MtcAgendaController extends Controller
{
    /**
     * Map jenis_mtc → inspection table yang punya kolom mesin_id
     */
    private array $inspectionTables = [
        'Refrigerasi'     => 'mtc_refrigerasi_inspections',
        'Motor Pompa'      => 'mtc_motor_pump_inspections',
        'Utility'         => 'mtc_utility_inspections',
        'Electrical'      => 'mtc_electrical_inspections',
        'Electric Engine' => 'mtc_electric_engine_inspections',
        'Diesel Engine'   => 'mtc_diesel_engine_inspections',
        'Electric P2H'    => 'mtc_electric_p2h_inspections',
        'Diesel P2H'      => 'mtc_diesel_p2h_inspections',
    ];

    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        // Ambil semua jenis_mtc yang ada di master mesin
        $jenisMtcList = MtcMasterMesinModel::where('aktif', true)
            ->distinct()
            ->orderBy('jenis_mtc')
            ->pluck('jenis_mtc');

        $selectedJenis = $request->get('jenis_mtc', $jenisMtcList->first());

        // Mesin aktif untuk jenis terpilih, beserta frekuensi
        $mesinList = MtcMasterMesinModel::where('jenis_mtc', $selectedJenis)
            ->where('aktif', true)
            ->with(['frekuensi'])
            ->orderBy('kode_mesin')
            ->get();

        // Bangun data agenda
        $agendaData = $mesinList->map(function ($mesin) use ($selectedJenis) {
            $lastDate = $this->getLastMaintenanceDate($mesin->id, $selectedJenis);

            $schedules = $mesin->frekuensi->map(function ($frek) use ($lastDate) {
                $nextDue = $this->calculateNextDue($lastDate, $frek->interval, $frek->satuan);
                $daysLeft = (int) Carbon::today()->diffInDays($nextDue, false); // negatif = lewat
                $status   = $this->resolveStatus($daysLeft, $lastDate);

                return [
                    'label'     => $frek->label,
                    'interval'  => $frek->interval,
                    'satuan'    => $frek->satuan,
                    'next_due'  => $nextDue,
                    'days_left' => $daysLeft,
                    'status'    => $status,
                ];
            });

            // Jika tidak ada frekuensi, tetap tampilkan mesin dengan status no_schedule
            if ($schedules->isEmpty()) {
                $schedules = collect([[
                    'label'     => '-',
                    'interval'  => null,
                    'satuan'    => null,
                    'next_due'  => null,
                    'days_left' => null,
                    'status'    => 'no_schedule',
                ]]);
            }

            return [
                'mesin'     => $mesin,
                'last_date' => $lastDate,
                'schedules' => $schedules,
            ];
        });

        // Ringkasan status untuk semua schedule
        $summary = $this->buildSummary($agendaData);

        // Untuk filter bulan di kalender (opsional, dipakai di tab Kalender)
        $calendarMonth = $request->get('month', Carbon::today()->format('Y-m'));

        // Flatten agenda untuk tampilan kalender
        $calendarEvents = $this->buildCalendarEvents($agendaData, $calendarMonth);

        return view('maintenance.agenda.index', compact(
            'jenisMtcList',
            'selectedJenis',
            'agendaData',
            'summary',
            'calendarMonth',
            'calendarEvents'
        ));

        // // ── DEBUG: return JSON, jangan lupa hapus setelah selesai ──
        // return response()->json([
        //     'selected_jenis'    => $selectedJenis,
        //     'inspection_tables' => $this->inspectionTables,  // ← lihat semua key mapping
        //     'agenda'            => $agendaData,
        // ], 200, [], JSON_PRETTY_PRINT);

    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Ambil tanggal maintenance terakhir dari inspection table terkait
     */
    private function getLastMaintenanceDate(int $mesinId, string $jenisMtc): ?Carbon
    {
        $table = $this->inspectionTables[$jenisMtc] ?? null;
        if (!$table) return null;

        // Subquery: mtc_main_id yang semua approval-nya sudah approved
        $approvedMainIds = DB::table('mtc_approval')
        ->select('mtc_main_id')
        ->groupBy('mtc_main_id')
        ->havingRaw('SUM(CASE WHEN status != ? THEN 1 ELSE 0 END) = 0', ['approved']);

        $row = DB::table($table)
            ->join('mtc_main', 'mtc_main.id', '=', "{$table}.mtc_main_id")
            ->joinSub($approvedMainIds, 'approved_main', function ($join) use ($table) {
                $join->on('approved_main.mtc_main_id', '=', "{$table}.mtc_main_id");
            })
            ->where("{$table}.mesin_id", $mesinId)
            ->orderByDesc('mtc_main.tanggal')
            ->select('mtc_main.tanggal')
            ->first();

        // Fallback: ambil semua tanpa filter approval
        if (!$row) {
            $row = DB::table($table)
                ->join('mtc_main', 'mtc_main.id', '=', "{$table}.mtc_main_id")
                ->where("{$table}.mesin_id", $mesinId)
                ->orderByDesc('mtc_main.tanggal')
                ->select('mtc_main.tanggal')
                ->first();
        }

        return $row ? Carbon::parse($row->tanggal) : null;
    }

    /**
     * Hitung jadwal berikutnya dari tanggal terakhir + interval frekuensi
     * Jika belum pernah maintenance, gunakan hari ini sebagai basis
     */
    private function calculateNextDue(?Carbon $lastDate, int $interval, string $satuan): Carbon
    {
        $base = $lastDate ?? Carbon::today();

        return match (strtolower($satuan)) {
            'hari'   => $base->copy()->addDays($interval),
            'minggu' => $base->copy()->addWeeks($interval),
            'bulan'  => $base->copy()->addMonths($interval),
            'tahun'  => $base->copy()->addYears($interval),
            default  => $base->copy()->addMonths($interval),
        };
    }

    /**
     * Tentukan status berdasarkan sisa hari
     * overdue   = sudah lewat
     * critical  = ≤ 3 hari
     * upcoming  = 4–14 hari
     * scheduled = > 14 hari
     * no_record = belum pernah maintenance
     */
    private function resolveStatus(int $daysLeft, ?Carbon $lastDate): string
    {
        if ($lastDate === null) return 'no_record';
        if ($daysLeft < 0)     return 'overdue';
        if ($daysLeft <= 3)    return 'critical';
        if ($daysLeft <= 14)   return 'upcoming';
        return 'scheduled';
    }

    /**
     * Hitung ringkasan jumlah per status
     */
    private function buildSummary($agendaData): array
    {
        $summary = [
            'overdue'    => 0,
            'critical'   => 0,
            'upcoming'   => 0,
            'scheduled'  => 0,
            'no_record'  => 0,
            'no_schedule' => 0,
        ];

        foreach ($agendaData as $item) {
            foreach ($item['schedules'] as $sch) {
                $key = $sch['status'];
                if (isset($summary[$key])) {
                    $summary[$key]++;
                }
            }
        }

        return $summary;
    }

    /**
     * Siapkan event kalender untuk bulan tertentu
     */
    private function buildCalendarEvents($agendaData, string $calendarMonth): array
    {
        $events = [];
        $monthStart = Carbon::parse($calendarMonth . '-01')->startOfMonth();
        $monthEnd   = $monthStart->copy()->endOfMonth();

        foreach ($agendaData as $item) {
            foreach ($item['schedules'] as $sch) {
                if (!$sch['next_due']) continue;

                $due = $sch['next_due'];
                if ($due->between($monthStart, $monthEnd)) {
                    $events[$due->day][] = [
                        'kode'   => $item['mesin']->kode_mesin,
                        'nama'   => $item['mesin']->nama_mesin,
                        'frek'   => $sch['label'],
                        'status' => $sch['status'],
                    ];
                }
            }
        }

        return $events;
    }
}
