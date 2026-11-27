<?php

namespace App\Http\Controllers\Maintenance;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Maintenance\MtcMasterMesinModel;
use App\Models\Maintenance\MtcMesinFrekuensiModel;
use App\Http\Requests\Maintenance\MtcMasterMesinRequest;
use App\Http\Requests\Maintenance\MtcMasterMesinUploadRequest;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MtcMasterMesinController extends Controller
{
    private const VALID_SATUAN = ['hari', 'minggu', 'bulan', 'tahun'];

    public function index()
    {
        return view('maintenance.master.master_mesin');
    }

    // ── CRUD ─────────────────────────────────────────────────────────────────

    public function store(MtcMasterMesinRequest $request)
    {
        DB::transaction(function () use ($request) {
            $hasBackup = $request->boolean('has_backup', false);
            $backupMesinIds = [];
            if ($hasBackup) {
                if ($request->has('backup_mesin_ids')) {
                    $backupMesinIds = array_values(array_filter(array_map('intval', (array) $request->input('backup_mesin_ids', []))));
                } elseif (!empty($request->backup_mesin_id)) {
                    $backupMesinIds = [(int) $request->backup_mesin_id];
                }
            }

            $primaryBackupId = count($backupMesinIds) > 0 ? $backupMesinIds[0] : null;

            $mesin = MtcMasterMesinModel::create([
                'jenis_mtc'       => $request->jenis_mtc,
                'nama_mesin'      => $request->nama_mesin,
                'lokasi'          => $request->lokasi,
                'dept'            => $request->dept ?? null,
                'kode_mesin'      => $request->kode_mesin ?? null,
                'has_backup'      => $hasBackup && count($backupMesinIds) > 0,
                'backup_mesin_id' => $primaryBackupId,
                'backup_notes'    => null,
                'aktif'           => 1,
                'created_by'      => Auth::id(),
            ]);

            // Sinkronisasi multi-backup (Many-to-Many 2 arah)
            $this->syncMutualMultiBackup($mesin, $hasBackup, $backupMesinIds, $request->backup_notes);

            // frekuensi dikirim sebagai array: [{ interval: 1, satuan: 'bulan' }, ...]
            $this->syncFrekuensi($mesin->id, $request->frekuensi ?? []);
        });

        return response()->json(['status' => true, 'message' => 'Data mesin berhasil disimpan']);
    }

    public function getData()
    {
        $data = MtcMasterMesinModel::with(['frekuensi', 'backupMesinList:id,kode_mesin,nama_mesin,lokasi'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $backupList = $item->backupMesinList->map(fn($b) => [
                    'id'         => $b->id,
                    'kode_mesin' => $b->kode_mesin,
                    'nama_mesin' => $b->nama_mesin,
                    'lokasi'     => $b->lokasi,
                ])->values()->toArray();

                $backupIds = array_column($backupList, 'id');

                return [
                    'id'                => $item->id,
                    'jenis_mtc'         => $item->jenis_mtc,
                    'nama_mesin'        => $item->nama_mesin,
                    'lokasi'            => $item->lokasi,
                    'dept'              => $item->dept,
                    'kode_mesin'        => $item->kode_mesin,
                    'has_backup'        => (bool) $item->has_backup,
                    'backup_mesin_id'   => $item->backup_mesin_id,
                    'backup_mesin_ids'  => $backupIds,
                    'backup_mesin_list' => $backupList,
                    'backup_notes'      => $item->backup_notes,
                    'aktif'             => $item->aktif,
                    'created_at'        => $item->created_at,
                    // [{ interval: 2, satuan: 'bulan', label: '2 Bulan' }, ...]
                    'frekuensi_list'    => $item->frekuensi->map(fn($f) => [
                        'interval' => $f->interval,
                        'satuan'   => $f->satuan,
                        'label'    => $f->label,
                    ])->toArray(),
                ];
            });

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function update(MtcMasterMesinRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $mesin = MtcMasterMesinModel::findOrFail($id);
            $hasBackup = $request->boolean('has_backup', false);
            $backupMesinIds = [];
            if ($hasBackup) {
                if ($request->has('backup_mesin_ids')) {
                    $backupMesinIds = array_values(array_filter(array_map('intval', (array) $request->input('backup_mesin_ids', []))));
                } elseif (!empty($request->backup_mesin_id)) {
                    $backupMesinIds = [(int) $request->backup_mesin_id];
                }
            }

            $primaryBackupId = count($backupMesinIds) > 0 ? $backupMesinIds[0] : null;

            $mesin->update([
                'jenis_mtc'       => $request->jenis_mtc,
                'nama_mesin'      => $request->nama_mesin,
                'lokasi'          => $request->lokasi,
                'dept'            => $request->dept ?? null,
                'kode_mesin'      => $request->kode_mesin ?? null,
                'has_backup'      => $hasBackup && count($backupMesinIds) > 0,
                'backup_mesin_id' => $primaryBackupId,
                'aktif'           => $request->aktif,
                'updated_by'      => Auth::id(),
            ]);

            // Sinkronisasi multi-backup (Many-to-Many 2 arah)
            $this->syncMutualMultiBackup($mesin, $hasBackup, $backupMesinIds, $request->backup_notes);

            $this->syncFrekuensi($mesin->id, $request->frekuensi ?? []);
        });

        return response()->json(['status' => true, 'message' => 'Data mesin berhasil diupdate']);
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $mesin = MtcMasterMesinModel::findOrFail($id);

            // Ambil daftar partner yang menjadikan mesin ini sebagai backup
            $partnerIds = DB::table('mtc_mesin_backup')
                ->where('backup_mesin_id', $mesin->id)
                ->pluck('mesin_id')
                ->toArray();

            // Hapus dari pivot
            DB::table('mtc_mesin_backup')
                ->where('mesin_id', $mesin->id)
                ->orWhere('backup_mesin_id', $mesin->id)
                ->delete();

            // Refresh partner machines
            foreach ($partnerIds as $pId) {
                $remBackups = DB::table('mtc_mesin_backup')
                    ->where('mesin_id', $pId)
                    ->pluck('backup_mesin_id')
                    ->toArray();
                $pMesin = MtcMasterMesinModel::find($pId);
                if ($pMesin) {
                    $pMesin->update([
                        'has_backup'      => count($remBackups) > 0,
                        'backup_mesin_id' => count($remBackups) > 0 ? $remBackups[0] : null,
                        'backup_notes'    => $this->buildBackupNotes($remBackups),
                    ]);
                }
            }

            $mesin->delete();
        });

        return response()->json(['status' => true, 'message' => 'Data mesin berhasil dihapus']);
    }

    // ── Download template Excel ───────────────────────────────────────────────

    public function downloadTemplate()
    {
        $path = storage_path('app/templates/template_master_mesin.xlsx');
        $dir = dirname($path);

        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }

        if (!file_exists($path) || true) { // Always ensure updated columns
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers/instructions
            $sheet->setCellValue('A1', 'TEMPLATE IMPORT MASTER MESIN');
            $sheet->mergeCells('A1:J1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            $sheet->setCellValue('A2', 'Petunjuk: Mulai isi data di Baris 5. Kolom B, C, dan D wajib diisi.');
            $sheet->mergeCells('A2:J2');

            $sheet->setCellValue('A3', 'Frekuensi: dipisah koma (contoh: 1 Hari, 1 Bulan). Aktif: Ya/Tidak. Backup Unit: Ya/Tidak (Jika Ya, korektif tidak dihitung downtime). Catatan Backup: nama mesin cadangan.');
            $sheet->mergeCells('A3:J3');

            // Table headers in row 4
            $sheet->setCellValue('A4', 'No');
            $sheet->setCellValue('B4', 'Jenis MTC *');
            $sheet->setCellValue('C4', 'Nama Mesin *');
            $sheet->setCellValue('D4', 'Lokasi *');
            $sheet->setCellValue('E4', 'Dept');
            $sheet->setCellValue('F4', 'Kode Mesin');
            $sheet->setCellValue('G4', 'Frekuensi Perawatan');
            $sheet->setCellValue('H4', 'Status Aktif');
            $sheet->setCellValue('I4', 'Backup Unit (Ya/Tidak)');
            $sheet->setCellValue('J4', 'Catatan Backup (Opsional)');

            // Apply bold to headers
            $sheet->getStyle('A4:J4')->getFont()->setBold(true);

            // Add sample data in Row 5
            $sheet->setCellValue('A5', '1');
            $sheet->setCellValue('B5', 'Utility');
            $sheet->setCellValue('C5', 'Kompresor A');
            $sheet->setCellValue('D5', 'Ruang Utility');
            $sheet->setCellValue('E5', 'Engineering');
            $sheet->setCellValue('F5', 'UTL-COMP-A');
            $sheet->setCellValue('G5', '1 Bulan, 6 Bulan');
            $sheet->setCellValue('H5', 'Aktif');
            $sheet->setCellValue('I5', 'Ya');
            $sheet->setCellValue('J5', 'Kompresor A.1 (Standby)');

            // Add sample data in Row 6
            $sheet->setCellValue('A6', '2');
            $sheet->setCellValue('B6', 'Electrical');
            $sheet->setCellValue('C6', 'Panel Listrik Utama');
            $sheet->setCellValue('D6', 'Gedung Produksi');
            $sheet->setCellValue('E6', 'Engineering');
            $sheet->setCellValue('F6', 'ELC-PAN-MAIN');
            $sheet->setCellValue('G6', '1 Minggu, 1 Tahun');
            $sheet->setCellValue('H6', 'Aktif');
            $sheet->setCellValue('I6', 'Tidak');
            $sheet->setCellValue('J6', '');

            // Auto fit column widths
            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($path);
        }

        return response()->download($path, 'template_master_mesin_maintenance.xlsx');
    }

    // ── Upload & import Excel ─────────────────────────────────────────────────

    public function uploadExcel(MtcMasterMesinUploadRequest $request)
    {
        $rows = IOFactory::load($request->file('file_excel')->getRealPath())
            ->getActiveSheet()
            ->toArray(null, true, true, true);

        // Kolom: A=No, B=jenis_mtc, C=nama_mesin, D=lokasi,
        //        E=dept, F=kode_mesin, G=frekuensi (pisah koma: "1 Bulan,6 Bulan"), H=aktif,
        //        I=backup (Ya/Tidak), J=backup_notes
        $dataStart = 5;
        $inserted  = 0;
        $skipped   = 0;
        $errors    = [];

        DB::transaction(function () use ($rows, $dataStart, &$inserted, &$skipped, &$errors) {
            foreach ($rows as $rowNum => $row) {
                if ($rowNum < $dataStart) continue;

                $jenisMtc       = trim((string) ($row['B'] ?? ''));
                $namaMesin      = trim((string) ($row['C'] ?? ''));
                $lokasi         = trim((string) ($row['D'] ?? ''));
                $dept           = trim((string) ($row['E'] ?? ''));
                $kodeMesin      = trim((string) ($row['F'] ?? ''));
                $frekRaw        = trim((string) ($row['G'] ?? ''));
                $aktifRaw       = trim((string) ($row['H'] ?? ''));
                $backupRaw      = trim((string) ($row['I'] ?? ''));
                $backupNotesRaw = trim((string) ($row['J'] ?? ''));

                if ($jenisMtc === '' && $namaMesin === '' && $lokasi === '') continue;

                $rowErrors = [];
                if ($jenisMtc === '')  $rowErrors[] = 'Jenis MTC wajib diisi';
                if ($namaMesin === '') $rowErrors[] = 'Nama Mesin wajib diisi';

                $jenisMtcLower = strtolower($jenisMtc);
                $isElectrical = str_contains($jenisMtcLower, 'Electrical') || str_contains($jenisMtcLower, 'electrical');

                if ($lokasi === '' && !$isElectrical) {
                    $rowErrors[] = 'Lokasi wajib diisi';
                }

                if ($lokasi === '') {
                    $lokasi = '-';
                }

                // Parse frekuensi: "1 Bulan, 6 Bulan" → [['interval'=>1,'satuan'=>'bulan'], ...]
                $frekuensiList = [];
                if ($frekRaw !== '') {
                    foreach (explode(',', $frekRaw) as $item) {
                        $parsed = $this->parseFrekuensiString(trim($item));
                        if ($parsed) {
                            $frekuensiList[] = $parsed;
                        } else {
                            $rowErrors[] = "Frekuensi '{$item}' tidak dikenali "
                                . "(contoh: 1 Hari, 2 Minggu, 1 Bulan, 6 Bulan, 1 Tahun)";
                        }
                    }
                }

                // Parse aktif
                $aktifNorm = strtolower($aktifRaw);
                if (in_array($aktifNorm, ['1', 'true', 'yes', 'aktif', 'ya'], true)) {
                    $aktif = 1;
                } elseif (in_array($aktifNorm, ['0', 'false', 'no', 'tidak', 'non aktif', 'nonaktif'], true)) {
                    $aktif = 0;
                } elseif ($aktifRaw === '') {
                    $aktif = 1;
                } else {
                    $rowErrors[] = "Aktif tidak valid ({$aktifRaw})";
                    $aktif = null;
                }

                // Parse has_backup
                $backupNorm = strtolower($backupRaw);
                $hasBackup = in_array($backupNorm, ['1', 'true', 'yes', 'ya', 'ada'], true);
                $backupNotes = $hasBackup && $backupNotesRaw !== '' ? $backupNotesRaw : null;

                if (!empty($rowErrors)) {
                    $errors[] = ['baris' => $rowNum, 'masalah' => implode(', ', $rowErrors)];
                    $skipped++;
                    continue;
                }

                $mesin = null;
                if ($kodeMesin !== '') {
                    $mesin = MtcMasterMesinModel::where('kode_mesin', $kodeMesin)->first();
                }

                if ($mesin) {
                    $mesin->update([
                        'jenis_mtc'  => $jenisMtc,
                        'nama_mesin' => $namaMesin,
                        'lokasi'     => $lokasi,
                        'dept'       => $dept !== '' ? $dept : null,
                        'aktif'      => $aktif,
                        'updated_by' => Auth::id(),
                    ]);
                } else {
                    $mesin = MtcMasterMesinModel::create([
                        'jenis_mtc'  => $jenisMtc,
                        'nama_mesin' => $namaMesin,
                        'lokasi'     => $lokasi,
                        'dept'       => $dept !== '' ? $dept : null,
                        'kode_mesin' => $kodeMesin !== '' ? $kodeMesin : null,
                        'aktif'      => $aktif,
                        'created_by' => Auth::id(),
                    ]);
                }

                // Sinkronisasi backup 2 arah
                $partnerId = null;
                if ($hasBackup && $backupNotesRaw !== '') {
                    $partner = MtcMasterMesinModel::where('kode_mesin', $backupNotesRaw)
                        ->orWhere('nama_mesin', $backupNotesRaw)
                        ->first();
                    if ($partner && $partner->id !== $mesin->id) {
                        $partnerId = $partner->id;
                    }
                }
                $this->syncTwoWayBackup($mesin, $hasBackup, $partnerId, $backupNotesRaw);

                $this->syncFrekuensi($mesin->id, $frekuensiList);
                $inserted++;
            }
        });

        $message = "{$inserted} data berhasil diimport.";
        if ($skipped > 0) $message .= " {$skipped} baris dilewati karena ada kesalahan.";

        return response()->json([
            'status'   => true,
            'message'  => $message,
            'inserted' => $inserted,
            'skipped'  => $skipped,
            'errors'   => $errors,
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Sync frekuensi: hapus lama, insert baru.
     * $list = [['interval' => 1, 'satuan' => 'bulan'], ...]
     */
    private function syncFrekuensi(int $mesinId, array $list): void
    {
        MtcMesinFrekuensiModel::where('mesin_id', $mesinId)->delete();

        $seen = [];
        foreach ($list as $item) {
            $interval = 1;
            $satuan   = '';

            if (is_string($item)) {
                $parsed = $this->parseFrekuensiString($item);
                if (!$parsed) continue;
                $interval = $parsed['interval'];
                $satuan   = $parsed['satuan'];
            } else {
                $interval = max(1, (int) ($item['interval'] ?? 1));
                $satuan   = strtolower(trim($item['satuan'] ?? ''));
            }

            if (!in_array($satuan, self::VALID_SATUAN, true)) continue;

            $key = "{$interval}-{$satuan}";
            if (isset($seen[$key])) continue; // skip duplikat

            $seen[$key] = true;

            MtcMesinFrekuensiModel::create([
                'mesin_id' => $mesinId,
                'interval' => $interval,
                'satuan'   => $satuan,
            ]);
        }
    }

    /**
     * Parse string bebas dari Excel → ['interval' => int, 'satuan' => string]
     *
     * Format yang didukung:
     *   "1 Bulan", "2 Bulan", "6 Bulan", "1 Minggu", "2 Minggu",
     *   "1 Hari", "1 Tahun", "bulanan", "mingguan", "harian", "tahunan"
     */
    private function parseFrekuensiString(string $input): ?array
    {
        $val = strtolower(trim(preg_replace('/\s+/', ' ', $input)));

        // Format: "<angka> <satuan>" — contoh: "2 bulan", "6 bulan", "1 minggu"
        if (preg_match('/^(\d+)\s*(hari|minggu|bulan|tahun)s?$/i', $val, $m)) {
            return ['interval' => (int) $m[1], 'satuan' => strtolower($m[2])];
        }

        // Kata tunggal — anggap interval = 1
        $wordMap = [
            'harian'   => 'hari',
            'hari'    => 'hari',
            'daily'  => 'hari',
            'mingguan' => 'minggu',
            'minggu'  => 'minggu',
            'weekly' => 'minggu',
            'bulanan'  => 'bulan',
            'bulan'   => 'bulan',
            'monthly' => 'bulan',
            'tahunan'  => 'tahun',
            'tahun'   => 'tahun',
            'yearly' => 'tahun',
            'annual' => 'tahun',
        ];

        if (isset($wordMap[$val])) {
            return ['interval' => 1, 'satuan' => $wordMap[$val]];
        }

        return null;
    }

    /**
     * Helper membuat string ringkasan kode & nama mesin backup
     */
    private function buildBackupNotes(array $backupIds): ?string
    {
        if (empty($backupIds)) {
            return null;
        }

        $mesins = MtcMasterMesinModel::whereIn('id', $backupIds)->get();
        if ($mesins->isEmpty()) {
            return null;
        }

        $notes = [];
        foreach ($mesins as $m) {
            $notes[] = $m->kode_mesin ? "[{$m->kode_mesin}] {$m->nama_mesin}" : $m->nama_mesin;
        }

        return implode(', ', $notes);
    }

    /**
     * Sinkronisasi relasi Many-to-Many 2 arah untuk multi-backup mesin
     */
    private function syncMutualMultiBackup(MtcMasterMesinModel $mesin, bool $hasBackup, array $backupMesinIds, ?string $manualNotes = null)
    {
        // Filter out self ID
        $cleanBackupIds = $hasBackup
            ? array_values(array_unique(array_filter(array_map('intval', $backupMesinIds), fn($id) => $id > 0 && $id !== $mesin->id)))
            : [];

        // Ambil daftar backup lama yang terhubung ke mesin ini
        $oldBackupIds = DB::table('mtc_mesin_backup')
            ->where('mesin_id', $mesin->id)
            ->pluck('backup_mesin_id')
            ->toArray();

        // 1. Sync relasi pivot untuk mesin ini
        $mesin->backupMesinList()->sync($cleanBackupIds);

        // 2. Mesin yang dilepas (ada di old tapi tidak ada di new) -> copot link 2 arah
        $removedIds = array_diff($oldBackupIds, $cleanBackupIds);
        foreach ($removedIds as $remId) {
            DB::table('mtc_mesin_backup')
                ->where('mesin_id', $remId)
                ->where('backup_mesin_id', $mesin->id)
                ->delete();

            // Refresh status partner
            $remRemaining = DB::table('mtc_mesin_backup')->where('mesin_id', $remId)->pluck('backup_mesin_id')->toArray();
            $partnerMesin = MtcMasterMesinModel::find($remId);
            if ($partnerMesin) {
                $partnerMesin->update([
                    'has_backup'      => count($remRemaining) > 0,
                    'backup_mesin_id' => count($remRemaining) > 0 ? $remRemaining[0] : null,
                    'backup_notes'    => $this->buildBackupNotes($remRemaining),
                ]);
            }
        }

        // 3. Mesin baru yang ditambahkan -> tambahkan link balik 2 arah
        foreach ($cleanBackupIds as $addId) {
            DB::table('mtc_mesin_backup')->updateOrInsert(
                ['mesin_id' => $addId, 'backup_mesin_id' => $mesin->id],
                ['created_at' => now(), 'updated_at' => now()]
            );

            // Refresh status partner
            $addRemaining = DB::table('mtc_mesin_backup')->where('mesin_id', $addId)->pluck('backup_mesin_id')->toArray();
            $partnerMesin = MtcMasterMesinModel::find($addId);
            if ($partnerMesin) {
                $partnerMesin->update([
                    'has_backup'      => true,
                    'backup_mesin_id' => $mesin->id,
                    'backup_notes'    => $this->buildBackupNotes($addRemaining),
                ]);
            }
        }

        // 4. Update status mesin ini
        $notesForThis = count($cleanBackupIds) > 0
            ? $this->buildBackupNotes($cleanBackupIds)
            : ($hasBackup && !empty($manualNotes) ? $manualNotes : null);

        $mesin->update([
            'has_backup'      => count($cleanBackupIds) > 0 || ($hasBackup && !empty($manualNotes)),
            'backup_mesin_id' => count($cleanBackupIds) > 0 ? $cleanBackupIds[0] : null,
            'backup_notes'    => $notesForThis,
        ]);
    }
}
