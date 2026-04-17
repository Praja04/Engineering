<?php

namespace App\Http\Controllers\Ejo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ejo\EjoTicket;
use App\Models\Ejo\EjoClassification;
use App\Models\Ejo\EjoProgress;
use App\Models\Ejo\EjoNote;
use App\Models\Ejo\EjoTeamAssign;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EjoImportController extends Controller
{
    /**
     * Upload & proses file Excel harian.
     * POST /api/ejo/import
     *
     * Kolom Excel:
     *   No | OS/IN | Dept. | Tim | Ticket ID | Date | Category | Module |
     *   Subject | Description | Requestor | Status | Type | Schedule |
     *   Est.Time | Date Done | PIC ACTION | Note | Klasifikasi
     *
     * Logika:
     * - ticket_id belum ada → INSERT
     * - ticket_id sudah ada → UPDATE field yang berubah
     * - Status Done → LOCKED, tidak bisa di-revert
     * - Note → INSERT ke ejo_notes (idempotent, skip jika isi sama sudah ada)
     * - Tim → cocokkan ke users.username → INSERT ke ejo_team_assign
     * - Klasifikasi → cocokkan ke ejo_classifications.name → set classification_id
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $path = $request->file('file')->getRealPath();

        $spreadsheet = IOFactory::load($path);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return response()->json(['message' => 'File kosong atau tidak ada data.'], 422);
        }

        $headers  = array_map('trim', $rows[0]);
        $dataRows = array_slice($rows, 1);
        $col      = array_flip($headers);

        $result = [
            'inserted' => 0,
            'updated'  => 0,
            'skipped'  => 0,
            'errors'   => [],
        ];

        // ── Cache lookup agar tidak query DB per-baris ───────────────────────
        // users.username → id  (contoh: 'AC' => 5, 'CIV' => 8)
        $userCache = User::pluck('id', 'username')->toArray();

        // ejo_classifications.name → id  (contoh: 'Sipil' => 2)
        $classificationCache = EjoClassification::pluck('id', 'name')->toArray();

        DB::beginTransaction();

        try {
            foreach ($dataRows as $lineNo => $row) {
                $ticketId = isset($col['Ticket ID'])
                    ? trim((string) ($row[$col['Ticket ID']] ?? ''))
                    : null;

                if (empty($ticketId)) {
                    $result['skipped']++;
                    continue;
                }

                // ── Map field utama ticket ────────────────────────────────────
                $incoming = $this->mapRow($row, $col);

                // ── Klasifikasi: cocokkan nama dari kolom "Klasifikasi" ────────
                $klasifikasiName = trim((string) ($row[$col['Klasifikasi'] ?? -1] ?? ''));
                $incoming['classification_id'] = $klasifikasiName
                    ? ($classificationCache[$klasifikasiName] ?? null)
                    : null;

                // ── Tim: cocokkan username dari kolom "Tim" ───────────────────
                $timValue  = trim((string) ($row[$col['Tim'] ?? -1] ?? ''));
                $timUserId = $timValue ? ($userCache[$timValue] ?? null) : null;

                // ── Note: ambil dari kolom "Note" ─────────────────────────────
                $noteText = trim((string) ($row[$col['Note'] ?? -1] ?? ''));

                // ── Upsert ticket ─────────────────────────────────────────────
                $existing = EjoTicket::where('ticket_id', $ticketId)->first();

                if (!$existing) {
                    $ticket = EjoTicket::create(array_merge(
                        ['ticket_id' => $ticketId],
                        $incoming
                    ));

                    $this->syncProgress($ticket, $incoming);
                    $this->syncTeam($ticket, $timUserId);
                    $this->syncNote($ticket, $noteText);

                    $result['inserted']++;
                } else {
                    $changed = $this->detectChanges($existing, $incoming);

                    if (!empty($changed)) {
                        $existing->update($changed);
                        $this->syncProgress($existing, $incoming, $changed);
                        $result['updated']++;
                    } else {
                        $result['skipped']++;
                    }

                    // Tim & Note selalu dicek ulang tiap upload (idempotent)
                    $this->syncTeam($existing, $timUserId);
                    $this->syncNote($existing, $noteText);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('EJO Import error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Import gagal: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Import selesai.',
            'result'  => $result,
        ]);
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function mapRow(array $row, array $col): array
    {
        $get = fn (string $key) => isset($col[$key]) ? ($row[$col[$key]] ?? null) : null;

        $requestDate = null;
        if ($raw = $get('Date')) {
            try {
                $requestDate = Carbon::parse(str_replace(' - ', ' ', $raw));
            } catch (\Throwable) {
            }
        }

        $schedule = null;
        if ($raw = $get('Schedule')) {
            try {
                $schedule = Carbon::parse($raw)->format('Y-m-d');
            } catch (\Throwable) {
            }
        }

        $dateDone = null;
        if ($raw = $get('Date Done')) {
            try {
                $dateDone = Carbon::parse($raw)->format('Y-m-d');
            } catch (\Throwable) {
            }
        }

        return [
            'os_in'        => $get('OS/IN'),
            'department'   => $get('Dept.'),
            'request_date' => $requestDate,
            'category'     => $get('Category'),
            'module'       => $get('Module'),
            'subject'      => $get('Subject'),
            'description'  => $get('Description'),
            'requestor'    => $get('Requestor'),
            'status'       => $this->normalizeStatus((string) ($get('Status') ?? '')),
            'type'         => $get('Type'),
            'schedule'     => $schedule,
            'est_time'     => $get('Est.Time'),
            'date_done'    => $dateDone,
        ];
    }

    private function normalizeStatus(string $raw): string
    {
        return match (strtolower(trim($raw))) {
            'done'                => 'Done',
            'progress'            => 'In Progress',
            'in progress'         => 'In Progress',
            'open'                => 'Open',
            'pending'             => 'Pending',
            'cancel', 'cancelled' => 'Cancel',
            default               => ucfirst(trim($raw)) ?: 'Open',
        };
    }

    /**
     * Hanya kembalikan field yang berubah.
     * LOCK: jika status DB sudah "Done", abaikan perubahan status dari Excel.
     */
    private function detectChanges(EjoTicket $existing, array $incoming): array
    {
        $changed      = [];
        $isLockedDone = $existing->status === 'Done';

        foreach ($incoming as $field => $newValue) {
            if ($field === 'status' && $isLockedDone) {
                continue; // ← Done tidak bisa di-revert
            }

            if ((string) $existing->$field !== (string) $newValue) {
                $changed[$field] = $newValue;
            }
        }

        return $changed;
    }

    /**
     * Insert progress 100 otomatis saat status berubah ke Done.
     */
    private function syncProgress(EjoTicket $ticket, array $incoming, array $changed = []): void
    {
        $isDone = !empty($changed)
            ? (isset($changed['status']) && $changed['status'] === 'Done')
            : ($incoming['status'] ?? null) === 'Done';

        if (!$isDone) return;

        $alreadyDone = EjoProgress::where('ejo_id', $ticket->id)
            ->where('progress_percent', 100)
            ->exists();

        if ($alreadyDone) return;

        EjoProgress::create([
            'ejo_id'           => $ticket->id,
            'progress_percent' => 100,
            'progress_note'    => 'Auto-updated via Excel import (Status: Done)',
            'updated_by'       => auth()->id(),
        ]);

        if (!$ticket->date_done) {
            $ticket->update(['date_done' => now()]);
        }
    }

    /**
     * Assign Tim ke ticket berdasarkan user_id.
     * Idempotent: skip jika user sudah di-assign.
     */
    private function syncTeam(EjoTicket $ticket, ?int $userId): void
    {
        if (!$userId) return;

        $exists = EjoTeamAssign::where('ejo_id', $ticket->id)
            ->where('user_id', $userId)
            ->exists();

        if (!$exists) {
            EjoTeamAssign::create([
                'ejo_id'  => $ticket->id,
                'user_id' => $userId,
            ]);
        }
    }

    /**
     * Simpan Note ke ejo_notes.
     * Idempotent: skip jika note dengan isi yang sama sudah ada pada ticket ini.
     */
    private function syncNote(EjoTicket $ticket, string $noteText): void
    {
        if ($noteText === '') return;

        $exists = EjoNote::where('ejo_id', $ticket->id)
            ->where('note', $noteText)
            ->exists();

        if (!$exists) {
            EjoNote::create([
                'ejo_id'  => $ticket->id,
                'note'    => $noteText,
                'user_id' => auth()->id(),
            ]);
        }
    }
}
