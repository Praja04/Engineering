<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\PemakaianChemicalModel;
use App\Models\Utility\WwtpDailyApproval;
use App\Models\Utility\WwtpInfluentHarian;
use App\Models\Utility\WwtpPerformancePHharian;
use App\Models\Utility\WwtpPerformanceSample;
use App\Models\Utility\WwtpSludge;
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
            $setCell('D' . $row, $s1in?->{$field} ?? '');
            $setCell('F' . $row, $s2in?->{$field} ?? '');
            $setCell('H' . $row, $s3in?->{$field} ?? '');
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
}
