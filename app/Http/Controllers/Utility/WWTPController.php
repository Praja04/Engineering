<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\PemakaianChemicalModel;
use App\Models\Utility\wwtp_analisa\WwtpAnalisa;
use App\Models\Utility\wwtp_analisa\WwtpParameter;
use App\Models\Utility\wwtp_analisa\WwtpPoint;
use App\Models\Utility\WwtpDailyApproval;
use App\Models\Utility\WwtpInfluentHarian;
use App\Models\Utility\WwtpJenisSample;
use App\Models\Utility\WwtpPengangkutanSludge;
use App\Models\Utility\WwtpPerformancePHharian;
use App\Models\Utility\WwtpPerformanceSample;
use App\Models\Utility\WwtpSludge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class WWTPController extends Controller
{
    /**
     * GET /api/utility/wwtp/export?tanggal=Y-m-d
     */
    // public function export(Request $request)
    // {
    //     $tanggal = $request->tanggal;

    //     if (!$tanggal) {
    //         return response()->json(['status' => 'error', 'message' => 'Parameter tanggal wajib diisi.'], 422);
    //     }

    //     // ── Ambil data dari DB ────────────────────────────────────────────────
    //     // Nilai shift di DB disimpan sebagai string: 'shift1', 'shift2', 'shift3'

    //     $sludge   = WwtpSludge::whereDate('tanggal', $tanggal)->orderBy('shift')->get()->keyBy('shift');
    //     $influent = WwtpInfluentHarian::whereDate('tanggal', $tanggal)->orderBy('shift')->get()->keyBy('shift');
    //     $ph       = WwtpPerformancePHharian::whereDate('tanggal', $tanggal)->orderBy('shift')->get()->keyBy('shift');
    //     $sampel   = WwtpPerformanceSample::whereDate('tanggal', $tanggal)->get()->keyBy('id_sampel');
    //     $chemical = PemakaianChemicalModel::whereDate('tanggal', $tanggal)->get();
    //     // ── Return JSON preview ───────────────────────────────────────────────

    //     return response()->json([
    //         'status'  => 'success',
    //         'tanggal' => $tanggal,
    //         'data'    => [
    //             'ph'      => [
    //                 'shift_1' => $ph->get('shift1'),
    //                 'shift_2' => $ph->get('shift2'),
    //                 'shift_3' => $ph->get('shift3'),
    //             ],
    //             'influent' => [
    //                 'shift_1' => $influent->get('shift1'),
    //                 'shift_2' => $influent->get('shift2'),
    //                 'shift_3' => $influent->get('shift3'),
    //             ],
    //             'sludge'  => [
    //                 'shift_1' => $sludge->get('shift1'),
    //                 'shift_2' => $sludge->get('shift2'),
    //                 'shift_3' => $sludge->get('shift3'),
    //             ],
    //             'chemical' => $chemical->values(),
    //             'sampel'  => $sampel->values(),
    //         ],
    //     ]);
    //     // // ── Load template ─────────────────────────────────────────────────────
    //     // $templatePath = public_path('assets\templates\Template_wwtp.xlsx');

    //     // if (!file_exists($templatePath)) {
    //     //     return "<script>alert('Template WWTP tidak ditemukan'); window.close();</script>";
    //     // }

    //     // $spreadsheet = IOFactory::load($templatePath);
    //     // $sheet       = $spreadsheet->getActiveSheet();

    //     // // ── Tanggal ───────────────────────────────────────────────────────────

    //     // $sheet->setCellValue('M1','Tanggal = ' . $tanggal);

    //     // // ── pH per Shift ──────────────────────────────────────────────────────
    //     // // Shift 1 = col C | Shift 2 = col H | Shift 3 = col K

    //     // $s1ph = $ph->get(1);
    //     // $s2ph = $ph->get(2);
    //     // $s3ph = $ph->get(3);

    //     // // Equalisasi 1 (row 15)
    //     // $sheet->setCellValue('C15', $s1ph->equalisasi_1   ?? '');
    //     // $sheet->setCellValue('H15', $s2ph->equalisasi_1   ?? '');
    //     // $sheet->setCellValue('K15', $s3ph->equalisasi_1   ?? '');

    //     // // Equalisasi 2 (row 16)
    //     // $sheet->setCellValue('C16', $s1ph->equalisasi_2   ?? '');
    //     // $sheet->setCellValue('H16', $s2ph->equalisasi_2   ?? '');
    //     // $sheet->setCellValue('K16', $s3ph->equalisasi_2   ?? '');

    //     // // Netralisasi (row 17)
    //     // $sheet->setCellValue('C17', $s1ph->netralisasi    ?? '');
    //     // $sheet->setCellValue('H17', $s2ph->netralisasi    ?? '');
    //     // $sheet->setCellValue('K17', $s3ph->netralisasi    ?? '');

    //     // // Sedimentasi 2 (row 18)
    //     // $sheet->setCellValue('C18', $s1ph->sedimentasi_2  ?? '');
    //     // $sheet->setCellValue('H18', $s2ph->sedimentasi_2  ?? '');
    //     // $sheet->setCellValue('K18', $s3ph->sedimentasi_2  ?? '');

    //     // // Outlet Anaerob (row 19)
    //     // $sheet->setCellValue('C19', $s1ph->outlet_anaerob ?? '');
    //     // $sheet->setCellValue('H19', $s2ph->outlet_anaerob ?? '');
    //     // $sheet->setCellValue('K19', $s3ph->outlet_anaerob ?? '');

    //     // // Aerob (row 20)
    //     // $sheet->setCellValue('C20', $s1ph->aerob          ?? '');
    //     // $sheet->setCellValue('H20', $s2ph->aerob          ?? '');
    //     // $sheet->setCellValue('K20', $s3ph->aerob          ?? '');

    //     // // Lumpur Aktif (row 21)
    //     // $sheet->setCellValue('C21', $s1ph->lumpur_aktif   ?? '');
    //     // $sheet->setCellValue('H21', $s2ph->lumpur_aktif   ?? '');
    //     // $sheet->setCellValue('K21', $s3ph->lumpur_aktif   ?? '');

    //     // // Clarifier 2 (row 22)
    //     // $sheet->setCellValue('C22', $s1ph->clarifier_2    ?? '');
    //     // $sheet->setCellValue('H22', $s2ph->clarifier_2    ?? '');
    //     // $sheet->setCellValue('K22', $s3ph->clarifier_2    ?? '');

    //     // // Sedimentasi 1 (row 23)
    //     // $sheet->setCellValue('C23', $s1ph->sedimentasi_1  ?? '');
    //     // $sheet->setCellValue('H23', $s2ph->sedimentasi_1  ?? '');
    //     // $sheet->setCellValue('K23', $s3ph->sedimentasi_1  ?? '');

    //     // // Outlet (row 24)
    //     // $sheet->setCellValue('C24', $s1ph->outlet         ?? '');
    //     // $sheet->setCellValue('H24', $s2ph->outlet         ?? '');
    //     // $sheet->setCellValue('K24', $s3ph->outlet         ?? '');

    //     // // ── Flowmeter / Influent per Shift ────────────────────────────────────
    //     // // Shift 1 = col C | Shift 2 = col G | Shift 3 = col I

    //     // $s1in = $influent->get(1);
    //     // $s2in = $influent->get(2);
    //     // $s3in = $influent->get(3);

    //     // // Pit Garam (row 27)
    //     // $sheet->setCellValue('C27', $s1in->pit_garam          ?? '');
    //     // $sheet->setCellValue('G27', $s2in->pit_garam          ?? '');
    //     // $sheet->setCellValue('I27', $s3in->pit_garam          ?? '');

    //     // // Pit Produksi Step 3 ke Equal 1 (row 28)
    //     // $sheet->setCellValue('C28', $s1in->pit_produksi_step3 ?? '');
    //     // $sheet->setCellValue('G28', $s2in->pit_produksi_step3 ?? '');
    //     // $sheet->setCellValue('I28', $s3in->pit_produksi_step3 ?? '');

    //     // // Pit Produksi / Sparta (row 29)
    //     // $sheet->setCellValue('C29', $s1in->pit_sparta         ?? '');
    //     // $sheet->setCellValue('G29', $s2in->pit_sparta         ?? '');
    //     // $sheet->setCellValue('I29', $s3in->pit_sparta         ?? '');

    //     // // Pit Storage (row 30)
    //     // $sheet->setCellValue('C30', $s1in->pit_storage        ?? '');
    //     // $sheet->setCellValue('G30', $s2in->pit_storage        ?? '');
    //     // $sheet->setCellValue('I30', $s3in->pit_storage        ?? '');

    //     // // Proses WWTP 2 (row 31)
    //     // $sheet->setCellValue('C31', $s1in->pit_proses_wwtp2   ?? '');
    //     // $sheet->setCellValue('G31', $s2in->pit_proses_wwtp2   ?? '');
    //     // $sheet->setCellValue('I31', $s3in->pit_proses_wwtp2   ?? '');

    //     // // Outlet (row 32)
    //     // $sheet->setCellValue('C32', $s1in->pit_outlet         ?? '');
    //     // $sheet->setCellValue('G32', $s2in->pit_outlet         ?? '');
    //     // $sheet->setCellValue('I32', $s3in->pit_outlet         ?? '');

    //     // // Boiler (row 33)
    //     // $sheet->setCellValue('C33', $s1in->pit_boiler         ?? '');
    //     // $sheet->setCellValue('G33', $s2in->pit_boiler         ?? '');
    //     // $sheet->setCellValue('I33', $s3in->pit_boiler         ?? '');

    //     // // Domestik (row 34)
    //     // $sheet->setCellValue('C34', $s1in->pit_domestik       ?? '');
    //     // $sheet->setCellValue('G34', $s2in->pit_domestik       ?? '');
    //     // $sheet->setCellValue('I34', $s3in->pit_domestik       ?? '');

    //     // // ── Sludge per Shift ──────────────────────────────────────────────────
    //     // // Shift 1 = col C | Shift 2 = col G | Shift 3 = col I

    //     // $s1sl = $sludge->get('shift1');
    //     // $s2sl = $sludge->get('shift2');
    //     // $s3sl = $sludge->get('shift3');

    //     // // Drain Lumpur (row 36)
    //     // $sheet->setCellValue('C36', $s1sl->drain_lumpur     ?? '');
    //     // $sheet->setCellValue('G36', $s2sl->drain_lumpur     ?? '');
    //     // $sheet->setCellValue('I36', $s3sl->drain_lumpur     ?? '');

    //     // // Running Hour SCP (row 37)
    //     // $sheet->setCellValue('C37', $s1sl->running_hour_scp ?? '');
    //     // $sheet->setCellValue('G37', $s2sl->running_hour_scp ?? '');
    //     // $sheet->setCellValue('I37', $s3sl->running_hour_scp ?? '');

    //     // // ── Proses / Debit per Shift ──────────────────────────────────────────

    //     // // Debit 1 (row 39)
    //     // $sheet->setCellValue('C39', $s1in->debit1       ?? '');
    //     // $sheet->setCellValue('G39', $s2in->debit1       ?? '');
    //     // $sheet->setCellValue('I39', $s3in->debit1       ?? '');

    //     // // Running WWTP 1 (row 40)
    //     // $sheet->setCellValue('C40', $s1in->running_wwtp1 ?? '');
    //     // $sheet->setCellValue('G40', $s2in->running_wwtp1 ?? '');
    //     // $sheet->setCellValue('I40', $s3in->running_wwtp1 ?? '');

    //     // // Debit 2 (row 41)
    //     // $sheet->setCellValue('C41', $s1in->debit2       ?? '');
    //     // $sheet->setCellValue('G41', $s2in->debit2       ?? '');
    //     // $sheet->setCellValue('I41', $s3in->debit2       ?? '');

    //     // // Running WWTP 2 (row 42)
    //     // $sheet->setCellValue('C42', $s1in->running_wwtp2 ?? '');
    //     // $sheet->setCellValue('G42', $s2in->running_wwtp2 ?? '');
    //     // $sheet->setCellValue('I42', $s3in->running_wwtp2 ?? '');

    //     // // ── Sampel: TSS / SV30 / pH (col M / N / O) ──────────────────────────
    //     // // id_sampel → row (sesuaikan dengan master wwtp_jenis_sampel di DB)
    //     // //   1 = Aerasi 1           → 27
    //     // //   2 = Aerasi 2           → 28
    //     // //   3 = Aerasi 3           → 29
    //     // //   4 = Aerasi 4           → 30
    //     // //   5 = Aerasi 5           → 31
    //     // //   6 = Lumpur Aktif (LA)  → 32
    //     // //   7 = Sed-1              → 34
    //     // //   8 = Filtrat RAS Aerasi → 35
    //     // //   9 = Filtrat RAS LA     → 36

    //     // $sampelRowMap = [
    //     //     1 => 27,
    //     //     2 => 28,
    //     //     3 => 29,
    //     //     4 => 30,
    //     //     5 => 31,
    //     //     6 => 32,
    //     //     7 => 34,
    //     //     8 => 35,
    //     //     9 => 36,
    //     // ];

    //     // foreach ($sampelRowMap as $idSampel => $row) {
    //     //     $s = $sampel->get($idSampel);
    //     //     $sheet->setCellValue('M' . $row, $s->tss  ?? '');
    //     //     $sheet->setCellValue('N' . $row, $s->sv30 ?? '');
    //     //     $sheet->setCellValue('O' . $row, $s->ph   ?? '');
    //     // }

    //     // // ── Stream download ───────────────────────────────────────────────────

    //     // $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    //     // $filename = 'WWTP_' . str_replace('-', '', $tanggal) . '.xlsx';

    //     // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     // header('Content-Disposition: attachment;filename="' . $filename . '"');
    //     // header('Cache-Control: max-age=0');

    //     // $writer->save('php://output');
    //     // exit;
    // }

    public function export(Request $request)
    {
        $tanggal = $request->tanggal;

        if (!$tanggal) {
            return response()->json(['status' => 'error', 'message' => 'Parameter tanggal wajib diisi.'], 422);
        }

        // ── Ambil data dari DB ────────────────────────────────────────────────
        // Nilai shift di DB disimpan sebagai string: 'shift1', 'shift2', 'shift3'

        $sludge   = WwtpSludge::whereDate('tanggal', $tanggal)->orderBy('shift')->get()->keyBy('shift');
        $influent = WwtpInfluentHarian::whereDate('tanggal', $tanggal)->orderBy('shift')->get()->keyBy('shift');
        $ph       = WwtpPerformancePHharian::whereDate('tanggal', $tanggal)->orderBy('shift')->get()->keyBy('shift');
        $sampel   = WwtpPerformanceSample::whereDate('tanggal', $tanggal)->get()->keyBy('id_sampel');
        $chemical = PemakaianChemicalModel::whereDate('tanggal', $tanggal)
            ->where(
                'chemical_area',
                'WWTP'
            )
            ->get()
            ->groupBy(['jenis_pemakaian', 'shift']); // ['PAC powder 1']['shift 1'] => collection

        // ── Load template ─────────────────────────────────────────────────────
        $templatePath = public_path('assets/templates/Template_wwtp.xlsx');

        if (!file_exists($templatePath)) {
            return "<script>alert('Template WWTP tidak ditemukan'); window.close();</script>";
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet       = $spreadsheet->getActiveSheet();

        // ── Helper: setCellValue aman untuk merged cell ───────────────────────
        // PhpSpreadsheet menolak setCellValue pada non-master merged cell.
        // Fungsi ini selalu menulis ke cell paling kiri-atas dari merged range.
        $setCell = function (string $coord, $value) use ($sheet): void {
            $col = preg_replace(
                '/[0-9]/',
                '',
                $coord
            );
            $row = (int) preg_replace('/[A-Z]/', '', $coord);
            $colIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($col);

            foreach ($sheet->getMergeCells() as $mergeRange) {
                [$rangeStart] = explode(':', $mergeRange);
                [$startCol, $startRow] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($rangeStart);
                $startColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($startCol);
                $startRow    = (int) $startRow;

                [$rangeEnd] = array_reverse(explode(':', $mergeRange));
                [$endCol, $endRow] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($rangeEnd);
                $endColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($endCol);
                $endRow    = (int) $endRow;

                if (
                    $colIdx >= $startColIdx && $colIdx <= $endColIdx
                    && $row >= $startRow    && $row <= $endRow
                ) {
                    // Tulis ke master cell (kiri-atas)
                    $sheet->setCellValue($startCol . $startRow, $value);
                    return;
                }
            }

            $sheet->setCellValue($coord, $value);
        };

        // ── Tanggal ───────────────────────────────────────────────────────────
        // M1 adalah merged cell M1:O2, master-nya M1
        $setCell('M2', 'TANGGAL: ' . $tanggal);

        // ── pH per Shift ──────────────────────────────────────────────────────
        // Shift 1 = col C | Shift 2 = col H | Shift 3 = col K
        // Kunci DB: 'shift1', 'shift2', 'shift3'

        $s1ph = $ph->get('shift1');
        $s2ph = $ph->get('shift2');
        $s3ph = $ph->get('shift3');

        $phFields = [
            'equalisasi_1'  => 16,
            'sedimentasi_1' => 17,
            'equalisasi_2'  => 18,
            'netralisasi'   => 19,
            'sedimentasi_2' => 20,
            'outlet_anaerob' => 21,
            'aerob'         => 22,
            'lumpur_aktif'  => 23,
            'clarifier_2'   => 24,
            'outlet'        => 25,
        ];

        foreach ($phFields as $field => $row) {
            $setCell('D' . $row, $s1ph?->{$field} ?? '');
            $setCell(
                'F' . $row,
                $s2ph?->{$field} ?? ''
            );
            $setCell('H' . $row, $s3ph?->{$field} ?? '');
        }

        // ── Chemical Dose per Shift ───────────────────────────────────────────
        // Shift 1 = col C | Shift 2 = col H | Shift 3 = col K
        // Kunci shift di DB: 'shift 1', 'shift 2', 'shift 3' (ada spasi)
        // Row map sesuai template:
        //   PAC powder 1  → 7   (baris PAC)
        //   BE-100        → 8
        //   C-204         → 9
        //   C-9040 step 1 → 10  (baris C 9040)
        //   NaOH          → 11  (baris NaOH Step 2)
        //   Denfloc 945   → 12
        //   NPK           → 13

        $chemRowMap = [
            'PAC powder 1'  => 8,
            'BE-100'        => 9,
            'C-204'         => 10,
            'C-9040 step 1' => 11,
            'NaOH'          => 12,
            'Denfloc 945'   => 13,
            'NPK'           => 14,
        ];

        $chemShiftCol = [
            'shift 1' => 'D',
            'shift 2' => 'F',
            'shift 3' => 'H',
        ];

        foreach ($chemRowMap as $jenis => $row) {
            foreach ($chemShiftCol as $shiftKey => $col) {
                $item = $chemical->get($jenis)?->get($shiftKey)?->first();
                $setCell($col . $row, $item?->nilai_pemakaian ?? '');
            }
        }

        // ── Flowmeter / Influent per Shift ────────────────────────────────────
        // Shift 1 = col C | Shift 2 = col G | Shift 3 = col I

        $s1in = $influent->get('shift1');
        $s2in = $influent->get('shift2');
        $s3in = $influent->get('shift3');

        $influentFields = [
            'pit_garam'          => 28,
            'pit_produksi_step3' => 29,
            'pit_sparta'         => 30,
            'pit_storage'        => 31,
            'pit_proses_wwtp2'   => 32,
            'pit_outlet'         => 33,
            'pit_boiler'         => 34,
            'pit_domestik'       => 35,
        ];

        foreach ($influentFields as $field => $row) {
            $val1 = '';
            if ($s1in && $s1in->{$field} !== null) {
                $val1 = max(0, (float)$s1in->{$field} - (float)($s1in->{$field . '_awal'} ?? 0));
            }
            $val2 = '';
            if ($s2in && $s2in->{$field} !== null) {
                $val2 = max(0, (float)$s2in->{$field} - (float)($s2in->{$field . '_awal'} ?? 0));
            }
            $val3 = '';
            if ($s3in && $s3in->{$field} !== null) {
                $val3 = max(0, (float)$s3in->{$field} - (float)($s3in->{$field . '_awal'} ?? 0));
            }

            $setCell('D' . $row, $val1);
            $setCell('F' . $row, $val2);
            $setCell('H' . $row, $val3);
        }

        // ── Sludge per Shift ──────────────────────────────────────────────────
        // Shift 1 = col C | Shift 2 = col G | Shift 3 = col I

        $s1sl = $sludge->get('shift1');
        $s2sl = $sludge->get('shift2');
        $s3sl = $sludge->get('shift3');

        // Drain Lumpur (row 36)
        $setCell('D37', $s1sl?->drain_lumpur     ?? '');
        $setCell('F37', $s2sl?->drain_lumpur     ?? '');
        $setCell('H37', $s3sl?->drain_lumpur     ?? '');

        // Running Hour SCP (row 37)
        $setCell('D38', $s1sl?->running_hour_scp ?? '');
        $setCell('F38', $s2sl?->running_hour_scp ?? '');
        $setCell('H38', $s3sl?->running_hour_scp ?? '');

        // ── Proses / Debit per Shift ──────────────────────────────────────────
        // Shift 1 = col C | Shift 2 = col G | Shift 3 = col I

        // Debit 1 (row 39)
        $setCell('D40', $s1in?->debit1       ?? '');
        $setCell('F40', $s2in?->debit1       ?? '');
        $setCell('H40', $s3in?->debit1       ?? '');

        // Running WWTP 1 (row 40)
        $setCell('D41', $s1in?->running_wwtp1 ?? '');
        $setCell('F41', $s2in?->running_wwtp1 ?? '');
        $setCell('H41', $s3in?->running_wwtp1 ?? '');

        // Debit 2 (row 41)
        $setCell('D42', $s1in?->debit2       ?? '');
        $setCell('F42', $s2in?->debit2       ?? '');
        $setCell('H42', $s3in?->debit2       ?? '');

        // Running WWTP 2 (row 42)
        $setCell('D43', $s1in?->running_wwtp2 ?? '');
        $setCell('F43', $s2in?->running_wwtp2 ?? '');
        $setCell('H43', $s3in?->running_wwtp2 ?? '');

        // ── Sampel: TSS / SV30 / pH (col M / N / O) ──────────────────────────
        // id_sampel → row (sesuai template dan data JSON)
        //   1 = Aerasi 1           → 27
        //   2 = Aerasi 2           → 28
        //   3 = Aerasi 3           → 29
        //   4 = Aerasi 4           → 30
        //   5 = Aerasi 5           → 31
        //   6 = Lumpur Aktif (LA)  → 32
        //   7 = Sed-1              → 34
        //   8 = Filtrat RAS Aerasi → 35
        //   9 = Filtrat RAS LA     → 36

        $sampelRowMap = [
            1 => 8,
            2 => 9,
            3 => 10,
            4 => 11,
            5 => 12,
            6 => 13,
            7 => 14,
            8 => 15,
            9 => 16,
            10 => 17,
            11 => 19,
            12 => 20,
            13 => 21,
            14 => 22,
            15 => 23,
            16 => 24,
        ];

        foreach ($sampelRowMap as $idSampel => $row) {
            $s = $sampel->get($idSampel);
            $setCell('K' . $row, $s?->tss  ?? '');
            $setCell('L' . $row, $s?->sv30 ?? '');
            $setCell('M' . $row, $s?->ph   ?? '');
            $setCell('N' . $row, $s?->mlss   ?? '');
            $setCell('O' . $row, $s?->svl   ?? '');
            $setCell('P' . $row, $s?->do   ?? '');
        }

        // TTD
        $approval = WwtpDailyApproval::where('tanggal', $tanggal)
            ->with(['operator', 'foreman', 'supervisor'])
            ->first();

        if ($approval) {
            $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');
            $hasSticker = file_exists($signaturePath);

            // Operator (B)
            if (in_array($approval->status, ['submitted', 'approved_foreman', 'approved_supervisor'])) {
                if ($hasSticker) {
                    $drawOp = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawOp->setName('Operator');
                    $drawOp->setPath($signaturePath);
                    $drawOp->setHeight(50);
                    $drawOp->setCoordinates('B46');
                    $drawOp->setOffsetX(150);
                    $drawOp->setOffsetY(5);
                    $drawOp->setWorksheet($sheet);
                }
                $setCell('B49', $approval->operator ? $approval->operator->username : '-');
                $setCell('B50', $approval->submitted_at ? $approval->submitted_at->format('d/m/Y H:i') : '-');
            }

            // Foreman (E)
            if (in_array($approval->status, ['approved_foreman', 'approved_supervisor'])) {
                if ($hasSticker) {
                    $drawFm = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawFm->setName('Foreman');
                    $drawFm->setPath($signaturePath);
                    $drawFm->setHeight(50);
                    // $drawFm->setOffsetX(150);
                    $drawFm->setOffsetY(5);
                    $drawFm->setCoordinates('G46');
                    $drawFm->setWorksheet($sheet);
                }
                $setCell('E49', $approval->foreman ? $approval->foreman->username : '-');
                $setCell('E50', $approval->foreman_approved_at ? $approval->foreman_approved_at->format('d/m/Y H:i') : '-');
            }

            // Supervisor (J)
            if ($approval->status === 'approved_supervisor') {
                if ($hasSticker) {
                    $drawSpv = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawSpv->setName('Supervisor');
                    $drawSpv->setPath($signaturePath);
                    $drawSpv->setHeight(50);
                    // $drawSpv->setOffsetX(150);
                    $drawSpv->setOffsetY(5);
                    $drawSpv->setCoordinates('L46');
                    $drawSpv->setWorksheet($sheet);
                }
                $setCell('J49', $approval->supervisor ? $approval->supervisor->username : '-');
                $setCell('J50', $approval->supervisor_approved_at ? $approval->supervisor_approved_at->format('d/m/Y H:i') : '-');
            }
        }

        // ── Stream download ───────────────────────────────────────────────────

        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'WWTP_' . str_replace('-', '', $tanggal) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportMonthly(Request $request)
    {
        $month = $request->month;
        $year  = $request->year;

        if (!$month || !$year) {
            return response()->json(['status' => 'error', 'message' => 'Parameter bulan dan tahun wajib diisi.'], 422);
        }

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // ── Load template ─────────────────────────────────────────────────────
        $templatePath = public_path('assets/templates/Template_wwtp_bulanan.xlsx');

        if (!file_exists($templatePath)) {
            return "<script>alert('Template WWTP Bulanan tidak ditemukan'); window.close();</script>";
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet       = $spreadsheet->getActiveSheet();

        // ── Helper: setCellValue aman untuk merged cell ───────────────────────
        $setCell = function (string $coord, $value) use ($sheet): void {
            $col = preg_replace(
                '/[0-9]/',
                '',
                $coord
            );
            $row = (int) preg_replace('/[A-Z]/', '', $coord);
            $colIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($col);

            foreach ($sheet->getMergeCells() as $mergeRange) {
                [$rangeStart] = explode(':', $mergeRange);
                [$startCol, $startRow] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($rangeStart);
                $startColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($startCol);
                $startRow    = (int) $startRow;

                [$rangeEnd] = array_reverse(explode(':', $mergeRange));
                [$endCol, $endRow] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($rangeEnd);
                $endColIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($endCol);
                $endRow    = (int) $endRow;

                if (
                    $colIdx >= $startColIdx && $colIdx <= $endColIdx
                    && $row >= $startRow    && $row <= $endRow
                ) {
                    $sheet->setCellValue($startCol . $startRow, $value);
                    return;
                }
            }

            $sheet->setCellValue($coord, $value);
        };

        // ── Set header TAHUN & BULAN ──────────────────────────────────────────
        $indoMonths = [
            1  => 'JANUARI',
            2  => 'FEBRUARI',
            3  => 'MARET',
            4  => 'APRIL',
            5  => 'MEI',
            6  => 'JUNI',
            7  => 'JULI',
            8  => 'AGUSTUS',
            9  => 'SEPTEMBER',
            10 => 'OKTOBER',
            11 => 'NOVEMBER',
            12 => 'DESEMBER'
        ];
        $monthName = $indoMonths[(int)$month] ?? '';
        $setCell('AE2', "TAHUN : {$year}\nBULAN : {$monthName}");

        // ── Ambil data dari DB ────────────────────────────────────────────────
        $influentData = WwtpInfluentHarian::whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('j'); // 1 sampai 31
            });

        $chemicalData = PemakaianChemicalModel::whereBetween('tanggal', [$startDate, $endDate])
            ->where('chemical_area', 'WWTP')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('j'); // 1 sampai 31
            });

        $chemicalMapping = [
            15 => ['PAC powder 1'],
            16 => ['PAC powder 2'],
            17 => ['BE-100'],
            18 => ['C-204'],
            19 => ['C-9040 step 1'],
            20 => ['C-9040 step 2'],
            21 => ['Denfloc 260 PA'],
            22 => ['NaOH'],
            23 => ['NaOH step 2'],
        ];

        $chemicalKgHari = [
            24 => ['PAC powder 1'],
            25 => ['PAC powder 2'],
            27 => ['BE-100'],
            28 => ['C-204'],
            29 => ['C-9040 step 1'],
            30 => ['C-9040 step 2'],
            32 => ['Denfloc 260 PA'],
            33 => ['NaOH'],
            34 => ['NaOH step 2'],
            36 => ['Denfloc 945'],
            37 => ['Enzim'],
            38 => ['NPK'],
        ];

        // ── Ambil data analisa ────────────────────────────────────────────────
        $paramCOD = \App\Models\Utility\wwtp_analisa\WwtpParameter::where('parameter_name', 'like', '%COD%')->first();
        $paramTSS = \App\Models\Utility\wwtp_analisa\WwtpParameter::where('parameter_name', 'like', '%TSS%')->first();
        $paramPH  = \App\Models\Utility\wwtp_analisa\WwtpParameter::where('parameter_name', 'like', '%pH%')->first();
        $paramEC  = \App\Models\Utility\wwtp_analisa\WwtpParameter::where('parameter_name', 'like', '%EC%')->first();

        $analisaRecords = \App\Models\Utility\wwtp_analisa\WwtpAnalisa::with('details')
            ->whereBetween('analisa_date', [$startDate, $endDate])
            ->get();

        $analisaData = $analisaRecords->groupBy(function ($item) {
            return Carbon::parse($item->analisa_date)->format('j'); // 1 sampai 31
        });

        $pointNamesMap = [
            'Influent'           => ['Influent', 'Influent COD'],
            'Outlet DAF'         => ['Outlet DAF', 'DAF pre'],
            'Equalisasi 2'       => ['Equalisasi 2', 'New Anaerob', 'Sparta'],
            'Inlet Anaerob'      => ['Inlet Anaerob'],
            'Outlet Anaerob'     => ['Outlet Anaerob'],
            'Aerasi-1'           => ['Aerasi-1'],
            'Aerasi-2'           => ['Aerasi-2'],
            'Aerasi-3'           => ['Aerasi-3'],
            'Aerasi-4'           => ['Aerasi-4'],
            'Aerasi-5'           => ['Aerasi-5'],
            'Lumpur Aktif'       => ['Lumpur Aktif'],
            'Clarifier 1'        => ['Clarifier 1', 'Clarifier-1'],
            'Clarifier 2'        => ['Clarifier 2', 'Clarifier-2'],
            'SDM 1'              => ['SDM 1', 'Sedimen-1', 'Sedimen 1'],
            'Filtrat SCP'        => ['Filtrat SCP', 'Fitrat SCP'],
            'Outlet Sand Filter' => ['Outlet Sand Filter'],
            'Effluent'           => ['Effluent', 'Pit Outlet (Effluent)', 'Effluent COD (max 300 ppm)'],
        ];

        $dbPoints = \App\Models\Utility\wwtp_analisa\WwtpPoint::all();
        $pointIdMap = [];
        foreach ($pointNamesMap as $key => $names) {
            foreach ($names as $name) {
                $found = $dbPoints->first(function ($p) use ($name) {
                    return strtolower(trim($p->point_name)) === strtolower(trim($name));
                });
                if ($found) {
                    $pointIdMap[$key] = $found->id;
                    break;
                }
            }
        }

        $analisaPointOrder = [
            'Influent',
            'Outlet DAF',
            'Equalisasi 2',
            'Inlet Anaerob',
            'Outlet Anaerob',
            'Aerasi-1',
            'Aerasi-2',
            'Aerasi-3',
            'Aerasi-4',
            'Aerasi-5',
            'Lumpur Aktif',
            'Clarifier 1',
            'Clarifier 2',
            'SDM 1',
            'Filtrat SCP',
            'Outlet Sand Filter',
            'Effluent',
        ];

        $getAnalisaVal = function ($day, $parameterId, $pointKey) use ($analisaData, $pointIdMap) {
            if (!$parameterId || !isset($pointIdMap[$pointKey])) {
                return 0;
            }
            $pointId = $pointIdMap[$pointKey];
            $records = $analisaData->get($day) ?? collect();
            if ($records->isEmpty()) {
                return 0;
            }
            $values = collect();
            foreach ($records as $rec) {
                $detail = $rec->details->first(function ($d) use ($parameterId, $pointId) {
                    return $d->parameter_id == $parameterId && $d->point_id == $pointId;
                });
                if ($detail && $detail->hasil_analisa !== null) {
                    $values->push((float)$detail->hasil_analisa);
                }
            }
            return $values->isNotEmpty() ? $values->average() : 0;
        };

        // ── Ambil data performance sample ──────────────────────────────────────
        $performanceSamples = WwtpPerformanceSample::whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('j'); // 1 sampai 31
            });

        $sampleNamesMap = [
            'Aerasi 1'         => ['Aerasi 1', 'Aerasi-1'],
            'Aerasi 2'         => ['Aerasi 2', 'Aerasi-2'],
            'Aerasi 3'         => ['Aerasi 3', 'Aerasi-3'],
            'Aerasi 4'         => ['Aerasi 4', 'Aerasi-4'],
            'Aerasi 5'         => ['Aerasi 5', 'Aerasi-5'],
            'Lumpur Aktif'     => ['Lumpur Aktif'],
            'Netralisasi'      => ['Netralisasi'],
            'Sedimen 2'        => ['Sedimen 2', 'Sedimen-2'],
            'Anaerob'          => ['Anaerob'],
            'RAS Aerasi'       => ['RAS Aerasi', 'Ras Aerasi'],
            'RAS Lumpur Aktif' => ['RAS Lumpur Aktif', 'Ras Lumpur Aktif'],
            'Clarifier 1'      => ['Clarifier 1', 'Clarifier-1'],
            'Clarifier 2'      => ['Clarifier 2', 'Clarifier-2'],
            'Sedimen 1'        => ['Sedimen 1', 'Sedimen-1'],
        ];

        $dbSamples = \App\Models\Utility\WwtpJenisSample::all();
        $sampleIdMap = [];
        foreach ($sampleNamesMap as $key => $names) {
            foreach ($names as $name) {
                $found = $dbSamples->first(function ($s) use ($name) {
                    return strtolower(trim($s->nama_sampel)) === strtolower(trim($name));
                });
                if ($found) {
                    $sampleIdMap[$key] = $found->id;
                    break;
                }
            }
        }

        $getSampleVal = function ($day, $sampleKey, $field) use ($performanceSamples, $sampleIdMap) {
            if (!isset($sampleIdMap[$sampleKey])) {
                return 0;
            }
            $sampleId = $sampleIdMap[$sampleKey];
            $daySamples = $performanceSamples->get($day) ?? collect();
            if ($daySamples->isEmpty()) {
                return 0;
            }
            $matching = $daySamples->filter(fn($item) => $item->id_sampel == $sampleId);
            if ($matching->isEmpty()) {
                return 0;
            }
            $values = $matching->pluck($field)->filter(fn($v) => $v !== null);
            return $values->isNotEmpty() ? $values->average() : 0;
        };

        // ── Ambil data sludge ────────────────────────────────────────────────
        $sludgeData = WwtpSludge::whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('j'); // 1 sampai 31
            });

        // ── Isi data ke cell ─────────────────────────────────────────────────
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(4 + $day); // Day 1 = E (kolom ke-5)

            // 1. Proses Harian & Pit (influent)
            $dayRecords = $influentData->get($day) ?? collect();

            if ($dayRecords->isNotEmpty()) {
                // Row 6-7: debit1 & debit2 (Average across shifts)
                $debit1Vals = $dayRecords->pluck('debit1')->filter(fn($v) => $v !== null);
                $avgDebit1 = $debit1Vals->isNotEmpty() ? $debit1Vals->average() : 0;
                $setCell($colLetter . '6', $avgDebit1);

                $debit2Vals = $dayRecords->pluck('debit2')->filter(fn($v) => $v !== null);
                $avgDebit2 = $debit2Vals->isNotEmpty() ? $debit2Vals->average() : 0;
                $setCell($colLetter . '7', $avgDebit2);

                // Row 8-14: flowmeter difference sum across shifts
                $fields = [
                    8  => 'pit_outlet',
                    9  => 'pit_produksi_step3',
                    10 => 'pit_sparta',
                    11 => 'pit_garam',
                    12 => 'pit_boiler',
                    13 => 'pit_domestik',
                    14 => 'pit_storage',
                ];

                foreach ($fields as $row => $field) {
                    $totalDiff = 0;
                    foreach ($dayRecords as $rec) {
                        if ($rec->{$field} !== null) {
                            $diff = max(0, (float)$rec->{$field} - (float)($rec->{$field . '_awal'} ?? 0));
                            $totalDiff += $diff;
                        }
                    }
                    $setCell($colLetter . $row, $totalDiff);
                }
            } else {
                $setCell($colLetter . '6', 0);
                $setCell($colLetter . '7', 0);
                for ($row = 8; $row <= 14; $row++) {
                    $setCell($colLetter . $row, 0);
                }
            }

            // 2. Chemical
            $dayChems = $chemicalData->get($day) ?? collect();
            foreach ($chemicalMapping as $row => $possibleNames) {
                $matchingChems = $dayChems->filter(function ($item) use ($possibleNames) {
                    return in_array(strtolower(trim($item->jenis_pemakaian)), array_map('strtolower', $possibleNames));
                });

                if ($matchingChems->isNotEmpty()) {
                    $values = $matchingChems->map(function ($entry) {
                        return is_numeric($entry->nilai_pemakaian)
                            ? floatval($entry->nilai_pemakaian)
                            : floatval(preg_replace('/[^\d.]+/', '', $entry->nilai_pemakaian));
                    });
                    $avgPemakaian = $values->average();
                    $setCell($colLetter . $row, round($avgPemakaian, 3));
                } else {
                    $setCell($colLetter . $row, 0);
                }
            }

            // 2.1 Chemical (Kg/Hari)
            foreach ($chemicalKgHari as $row => $possibleNames) {
                $matchingChems = $dayChems->filter(function ($item) use ($possibleNames) {
                    return in_array(strtolower(trim($item->jenis_pemakaian)), array_map('strtolower', $possibleNames));
                });

                if ($matchingChems->isNotEmpty()) {
                    $totalPemakaian = 0;
                    foreach ($matchingChems as $entry) {
                        $nilai = is_numeric($entry->nilai_pemakaian)
                            ? floatval($entry->nilai_pemakaian)
                            : floatval(preg_replace('/[^\d.]+/', '', $entry->nilai_pemakaian));

                        $rh = $entry->running_hour ?? 1;
                        $jenisAsli = trim($entry->jenis_pemakaian);

                        switch ($jenisAsli) {
                            case 'PAC powder 1':
                                $totalPemakaian += $rh * ($nilai * 60 * 7.6 / 100) / 1000;
                                break;
                            case 'PAC powder 2':
                                $totalPemakaian += $rh * ($nilai * 60 * 12.5 / 100) / 1000;
                                break;
                            case 'BE-100':
                                $totalPemakaian += $rh * ($nilai * 60 * 2.5 / 100) / 1000;
                                break;
                            case 'C-204':
                                $totalPemakaian += $rh * ($nilai * 60 * 1 / 100) / 1000;
                                break;
                            case 'C-9040 step 1':
                                $totalPemakaian += $rh * ($nilai * 60 * 0.11 / 100) / 1000;
                                break;
                            case 'C-9040 step 2':
                                $totalPemakaian += $rh * ($nilai * 60 * 0.35 / 100) / 1000;
                                break;
                            case 'Denfloc 260 PA':
                                $totalPemakaian += ($rh * ($nilai / 1000 * 60) * 480) / 1000 / 1000 / 1000;
                                break;
                            case 'NaOH':
                                $totalPemakaian += $rh * ($nilai / 1000 * 60) * 1.5;
                                break;
                            default:
                                $totalPemakaian += $nilai;
                                break;
                        }
                    }
                    $setCell($colLetter . $row, round($totalPemakaian, 3));
                } else {
                    $setCell($colLetter . $row, 0);
                }
            }

            // 3. Analisa COD (Row 50-66)
            $paramId = $paramCOD?->id;
            foreach ($analisaPointOrder as $idx => $pointKey) {
                $row = 50 + $idx;
                $val = $getAnalisaVal($day, $paramId, $pointKey);
                $setCell($colLetter . $row, $val);
            }

            // 4. Analisa TSS (Row 67-83)
            $paramId = $paramTSS?->id;
            foreach ($analisaPointOrder as $idx => $pointKey) {
                $row = 67 + $idx;
                $val = $getAnalisaVal($day, $paramId, $pointKey);
                $setCell($colLetter . $row, $val);
            }

            // 5. Analisa pH (Row 84-100)
            $paramId = $paramPH?->id;
            foreach ($analisaPointOrder as $idx => $pointKey) {
                $row = 84 + $idx;
                $val = $getAnalisaVal($day, $paramId, $pointKey);
                $setCell($colLetter . $row, $val);
            }

            // 6. Analisa EC (Row 101-117)
            $paramId = $paramEC?->id;
            foreach ($analisaPointOrder as $idx => $pointKey) {
                $row = 101 + $idx;
                $val = $getAnalisaVal($day, $paramId, $pointKey);
                $setCell($colLetter . $row, $val);
            }

            // 7. SV30 (Row 118-123)
            $sampleKeys = ['Aerasi 1', 'Aerasi 2', 'Aerasi 3', 'Aerasi 4', 'Aerasi 5', 'Lumpur Aktif'];
            foreach ($sampleKeys as $idx => $key) {
                $row = 118 + $idx;
                $val = $getSampleVal($day, $key, 'sv30');
                $setCell($colLetter . $row, $val);
            }

            // 8. MLSS (Row 124-129)
            foreach ($sampleKeys as $idx => $key) {
                $row = 124 + $idx;
                $val = $getSampleVal($day, $key, 'mlss');
                $setCell($colLetter . $row, $val);
            }

            // 9. SVI (Row 130-135)
            foreach ($sampleKeys as $idx => $key) {
                $row = 130 + $idx;
                $val = $getSampleVal($day, $key, 'svl');
                $setCell($colLetter . $row, $val);
            }

            // 10. F/M Ratio (Row 136-141)
            for ($row = 136; $row <= 141; $row++) {
                $setCell($colLetter . $row, 0);
            }

            // 11. SV30 Slurry (Row 142-154)
            $slurrySampleOrder = [
                'Netralisasi',
                'Sedimen 2',
                'Anaerob',
                'Aerasi 1',
                'Aerasi 2',
                'Aerasi 3',
                'Aerasi 4',
                'Aerasi 5',
                'RAS Aerasi',
                'Lumpur Aktif',
                'Clarifier 1',
                'Clarifier 2',
                'Sedimen 1',
            ];
            foreach ($slurrySampleOrder as $idx => $key) {
                $row = 142 + $idx;
                $val = $getSampleVal($day, $key, 'sv30');
                $setCell($colLetter . $row, $val);
            }

            // 12. Sludge Screwpress (Row 155-157)
            $daySludge = $sludgeData->get($day) ?? collect();
            if ($daySludge->isNotEmpty()) {
                $totalDrain = $daySludge->sum('drain_lumpur');
                $setCell($colLetter . '155', $totalDrain);

                $totalRh = $daySludge->sum('running_hour_scp');
                $setCell($colLetter . '156', $totalRh);

                $sludgeContentVals = $daySludge->pluck('sludge_content')->filter(fn($v) => $v !== null);
                $avgSludgeContent = $sludgeContentVals->isNotEmpty() ? $sludgeContentVals->average() : 0;
                $setCell($colLetter . '157', $avgSludgeContent);
            } else {
                $setCell($colLetter . '155', 0);
                $setCell($colLetter . '156', 0);
                $setCell($colLetter . '157', 0);
            }
        }

        // ── Stream download ───────────────────────────────────────────────────
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'WWTP_Bulanan_' . $year . '_' . sprintf('%02d', $month) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }


    // Dashboard WWTP
    public function wwtp_visualisasi_data(Request $request)
    {
        $dateStr = $request->query('tanggal');
        if (!$dateStr) {
            $latestInfluent = WwtpInfluentHarian::orderBy('tanggal', 'desc')->first();
            $dateStr = $latestInfluent ? $latestInfluent->tanggal : Carbon::today()->toDateString();
        }

        $date = \Carbon\Carbon::parse($dateStr);
        $dateFormatted = $date->toDateString();

        $influentRecords = WwtpInfluentHarian::whereDate('tanggal', $dateFormatted)->get();

        $proses = [
            'debit1' => $influentRecords->avg('debit1') ?? 0,
            'debit2' => $influentRecords->avg('debit2') ?? 0,
            'pit_outlet' => $influentRecords->reduce(function ($carry, $rec) {
                return $carry + max(0, (float)$rec->pit_outlet - (float)($rec->pit_outlet_awal ?? 0));
            }, 0),
            'pit_produksi_step3' => $influentRecords->reduce(function ($carry, $rec) {
                return $carry + max(0, (float)$rec->pit_produksi_step3 - (float)($rec->pit_produksi_step3_awal ?? 0));
            }, 0),
            'pit_sparta' => $influentRecords->reduce(function ($carry, $rec) {
                return $carry + max(0, (float)$rec->pit_sparta - (float)($rec->pit_sparta_awal ?? 0));
            }, 0),
            'pit_garam' => $influentRecords->reduce(function ($carry, $rec) {
                return $carry + max(0, (float)$rec->pit_garam - (float)($rec->pit_garam_awal ?? 0));
            }, 0),
            'pit_boiler' => $influentRecords->reduce(function ($carry, $rec) {
                return $carry + max(0, (float)$rec->pit_boiler - (float)($rec->pit_boiler_awal ?? 0));
            }, 0),
            'pit_domestik' => $influentRecords->reduce(function ($carry, $rec) {
                return $carry + max(0, (float)$rec->pit_domestik - (float)($rec->pit_domestik_awal ?? 0));
            }, 0),
            'pit_storage' => $influentRecords->reduce(function ($carry, $rec) {
                return $carry + max(0, (float)$rec->pit_storage - (float)($rec->pit_storage_awal ?? 0));
            }, 0),
        ];

        $analisaRecords = WwtpAnalisa::with('details')
            ->whereDate('analisa_date', $dateFormatted)
            ->get();

        $paramCOD = WwtpParameter::where('parameter_name', 'like', '%COD%')->first();
        $paramTSS = WwtpParameter::where('parameter_name', 'like', '%TSS%')->first();
        $paramPH  = WwtpParameter::where('parameter_name', 'like', '%pH%')->first();
        $paramEC  = WwtpParameter::where('parameter_name', 'like', '%EC%')->first();

        $pointNamesMap = [
            'Influent'           => ['Influent'],
            'Outlet DAF'         => ['Outlet DAF'],
            'Equalisasi 2'       => ['Influent'],
            'Inlet Anaerob'      => ['Inlet Anaerob'],
            'Outlet Anaerob'     => ['Outlet Anaerob', 'Anaerob'],
            'Aerasi-1'           => ['Aerasi-1'],
            'Aerasi-2'           => ['Aerasi-2'],
            'Aerasi-3'           => ['Aerasi-3'],
            'Aerasi-4'           => ['Aerasi-4'],
            'Aerasi-5'           => ['Aerasi-5'],
            'Aerasi-6'           => ['Aerasi-6'],
            'Lumpur Aktif'       => ['Lumpur Aktif'],
            'Clarifier 1'        => ['Clarifier 1'],
            'Clarifier 2'        => ['Clarifier 2'],
            'SDM 1'              => ['SDM 1'],
            'Filtrat SCP'        => ['Filtrat SCP'],
            'Outlet Sand Filter' => ['Outlet Sand Filter', 'Sandfilter'],
            'Effluent'           => ['Effluent'],
            'Pit Garam'          => ['Pit Garam'],
        ];

        $dbPoints = WwtpPoint::all();
        $pointIdMap = [];
        foreach ($pointNamesMap as $key => $names) {
            foreach ($names as $name) {
                $found = $dbPoints->first(function ($p) use ($name) {
                    return strtolower(trim($p->point_name)) === strtolower(trim($name));
                });
                if ($found) {
                    $pointIdMap[$key] = $found->id;
                    break;
                }
            }
        }

        $getAnalisaVal = function ($parameterId, $pointKey) use ($analisaRecords, $pointIdMap) {
            if (!$parameterId || !isset($pointIdMap[$pointKey])) {
                return 0;
            }
            $pointId = $pointIdMap[$pointKey];
            if ($analisaRecords->isEmpty()) {
                return 0;
            }
            $values = collect();
            foreach ($analisaRecords as $rec) {
                $detail = $rec->details->first(function ($d) use ($parameterId, $pointId) {
                    return $d->parameter_id == $parameterId && $d->point_id == $pointId;
                });
                if ($detail && $detail->hasil_analisa !== null) {
                    $values->push((float)$detail->hasil_analisa);
                }
            }
            return $values->isNotEmpty() ? $values->average() : 0;
        };

        $analisaData = [];
        $points = array_keys($pointNamesMap);
        foreach ($points as $point) {
            $analisaData[$point] = [
                'ph'  => $getAnalisaVal($paramPH?->id, $point),
                'tss' => $getAnalisaVal($paramTSS?->id, $point),
                'cod' => $getAnalisaVal($paramCOD?->id, $point),
                'ec'  => $getAnalisaVal($paramEC?->id, $point),
            ];
        }

        $removals = [];
        $calcRemoval = function ($in, $out) {
            if ($in <= 0) return 0;
            return (($in - $out) / $in) * 100;
        };

        $removals['anaerob'] = [
            'tss' => $calcRemoval($analisaData['Influent']['tss'], $analisaData['Outlet Anaerob']['tss']),
            'cod' => $calcRemoval($analisaData['Influent']['cod'], $analisaData['Outlet Anaerob']['cod']),
        ];

        $aerasiTSS = $analisaData['Aerasi-6']['tss'];
        $aerasiCOD = $analisaData['Aerasi-6']['cod'];
        $removals['aerob'] = [
            'tss' => $calcRemoval($analisaData['Outlet Anaerob']['tss'], $aerasiTSS),
            'cod' => $calcRemoval($analisaData['Outlet Anaerob']['cod'], $aerasiCOD),
        ];

        $removals['lumpur_aktif'] = [
            'tss' => $calcRemoval($analisaData['Outlet Anaerob']['tss'], $analisaData['Lumpur Aktif']['tss']),
            'cod' => $calcRemoval($analisaData['Outlet Anaerob']['cod'], $analisaData['Lumpur Aktif']['cod']),
        ];

        $clarifierAvgTSS = ($analisaData['Clarifier 1']['tss'] + $analisaData['Clarifier 2']['tss']) / 2;
        $clarifierAvgCOD = ($analisaData['Clarifier 1']['cod'] + $analisaData['Clarifier 2']['cod']) / 2;
        $removals['daf'] = [
            'tss' => $calcRemoval($clarifierAvgTSS, $analisaData['Outlet DAF']['tss']),
            'cod' => $calcRemoval($clarifierAvgCOD, $analisaData['Outlet DAF']['cod']),
        ];

        $removals['sandfilter'] = [
            'tss' => $calcRemoval($analisaData['Outlet DAF']['tss'], $analisaData['Outlet Sand Filter']['tss']),
            'cod' => $calcRemoval($analisaData['Outlet DAF']['cod'], $analisaData['Outlet Sand Filter']['cod']),
        ];

        $removals['outlet'] = [
            'tss' => $calcRemoval($analisaData['Outlet Sand Filter']['tss'], $analisaData['Effluent']['tss']),
            'cod' => $calcRemoval($analisaData['Outlet Sand Filter']['cod'], $analisaData['Effluent']['cod']),
        ];

        $removals['total'] = [
            'tss' => $calcRemoval($analisaData['Influent']['tss'], $analisaData['Effluent']['tss']),
            'cod' => $calcRemoval($analisaData['Influent']['cod'], $analisaData['Effluent']['cod']),
        ];

        $sludgeRecords = WwtpSludge::whereDate('tanggal', $dateFormatted)->get();
        $pengangkutan = WwtpPengangkutanSludge::where('week_start', '<=', $dateFormatted)
            ->where('week_end', '>=', $dateFormatted)
            ->first();

        $sludge = [
            'drain_lumpur' => $sludgeRecords->sum('drain_lumpur') ?? 0,
            'running_hour_scp' => $sludgeRecords->sum('running_hour_scp') ?? 0,
            'hasil_lumpur' => $sludgeRecords->sum('hasil_lumpur') ?? 0,
            'sludge_content' => $sludgeRecords->avg('sludge_content') ?? 0,
            'pengangkutan' => $pengangkutan ? $pengangkutan->jumlah_pengangkutan : 0,
        ];

        return response()->json([
            'status' => 'success',
            'tanggal' => $dateFormatted,
            'proses' => $proses,
            'analisa' => $analisaData,
            'removals' => $removals,
            'sludge' => $sludge,
        ]);
    }
}
