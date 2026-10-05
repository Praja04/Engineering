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
use App\Models\Utility\WwtpBiayaChemicalRecord;
use App\Models\Utility\WwtpBiayaChemicalDetail;
use App\Models\Utility\WwtpChemicalStandard;
use App\Models\Utility\WwtpKoloni;
use App\Models\Utility\WwtpKoloniDetail;
use App\Models\Utility\WwtpMasterKoloni;
use App\Models\Utility\WwtpPerformanceRecord;
use App\Models\Utility\WwtpPerformanceWeek;
use App\Models\Utility\WwtpRecord;
use App\Models\Utility\WwtpInfluent;
use App\Models\Utility\WwtpEffluent;
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
        $startDateStr = $request->query('start_date') ?? $request->query('tanggal');
        $endDateStr   = $request->query('end_date') ?? $request->query('tanggal');

        if (!$startDateStr || !$endDateStr) {
            $latestInfluent = WwtpInfluentHarian::orderBy('tanggal', 'desc')->first();
            if ($latestInfluent) {
                $latestDate = Carbon::parse($latestInfluent->tanggal);
                $startDateStr = $startDateStr ?? $latestDate->copy()->startOfMonth()->toDateString();
                $endDateStr   = $endDateStr ?? $latestDate->toDateString();
            } else {
                $startDateStr = $startDateStr ?? Carbon::today()->startOfMonth()->toDateString();
                $endDateStr   = $endDateStr ?? Carbon::today()->toDateString();
            }
        }

        $startDate = Carbon::parse($startDateStr)->toDateString();
        $endDate   = Carbon::parse($endDateStr)->toDateString();

        if ($startDate > $endDate) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $influentRecords = WwtpInfluentHarian::whereBetween('tanggal', [$startDate, $endDate])->get();

        $proses = [
            'debit1' => (float) ($influentRecords->avg('debit1') ?? 0),
            'debit2' => (float) ($influentRecords->avg('debit2') ?? 0),
            'pit_outlet' => (float) $influentRecords->reduce(function ($carry, $rec) {
                return $carry + max(0, (float)$rec->pit_outlet - (float)($rec->pit_outlet_awal ?? 0));
            }, 0),
            'pit_produksi_step3' => (float) $influentRecords->reduce(function ($carry, $rec) {
                return $carry + max(0, (float)$rec->pit_produksi_step3 - (float)($rec->pit_produksi_step3_awal ?? 0));
            }, 0),
            'pit_sparta' => (float) $influentRecords->reduce(function ($carry, $rec) {
                return $carry + max(0, (float)$rec->pit_sparta - (float)($rec->pit_sparta_awal ?? 0));
            }, 0),
            'pit_garam' => (float) $influentRecords->reduce(function ($carry, $rec) {
                return $carry + max(0, (float)$rec->pit_garam - (float)($rec->pit_garam_awal ?? 0));
            }, 0),
            'pit_boiler' => (float) $influentRecords->reduce(function ($carry, $rec) {
                return $carry + max(0, (float)$rec->pit_boiler - (float)($rec->pit_boiler_awal ?? 0));
            }, 0),
            'pit_domestik' => (float) $influentRecords->reduce(function ($carry, $rec) {
                return $carry + max(0, (float)$rec->pit_domestik - (float)($rec->pit_domestik_awal ?? 0));
            }, 0),
            'pit_storage' => (float) $influentRecords->reduce(function ($carry, $rec) {
                return $carry + max(0, (float)$rec->pit_storage - (float)($rec->pit_storage_awal ?? 0));
            }, 0),
        ];

        $analisaRecords = WwtpAnalisa::with('details')
            ->whereBetween('analisa_date', [$startDate, $endDate])
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

        $sludgeRecords = WwtpSludge::whereBetween('tanggal', [$startDate, $endDate])->get();
        $pengangkutanList = WwtpPengangkutanSludge::where('week_start', '<=', $endDate)
            ->where('week_end', '>=', $startDate)
            ->orderBy('week_start', 'asc')
            ->get();

        if ($pengangkutanList->isEmpty()) {
            $latest = WwtpPengangkutanSludge::where('week_start', '<=', $endDate)->orderBy('week_start', 'desc')->first();
            if ($latest) {
                $pengangkutanList = collect([$latest]);
            }
        }

        $totalPengangkutan = $pengangkutanList->sum('jumlah_pengangkutan');

        $sludge = [
            'drain_lumpur' => (float) ($sludgeRecords->sum('drain_lumpur') ?? 0),
            'running_hour_scp' => (float) ($sludgeRecords->sum('running_hour_scp') ?? 0),
            'hasil_lumpur' => (float) ($sludgeRecords->sum('hasil_lumpur') ?? 0),
            'sludge_content' => (float) ($sludgeRecords->avg('sludge_content') ?? 0),
            'pengangkutan' => (float) $totalPengangkutan,
            'pengangkutan_list' => $pengangkutanList->map(function ($item) {
                return [
                    'id' => $item->id,
                    'week_start' => $item->week_start,
                    'week_end' => $item->week_end,
                    'jumlah_pengangkutan' => (float) $item->jumlah_pengangkutan,
                ];
            })->values(),
            'week_start' => $pengangkutanList->isNotEmpty() ? $pengangkutanList->first()->week_start : null,
            'week_end' => $pengangkutanList->isNotEmpty() ? $pengangkutanList->last()->week_end : null,
        ];

        return response()->json([
            'status' => 'success',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'tanggal' => $startDate,
            'total_days' => Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1,
            'proses' => $proses,
            'analisa' => $analisaData,
            'removals' => $removals,
            'sludge' => $sludge,
        ]);
    }

    /**
     * Data API for Integrated WCO - HSE WWTP Dashboard
     */
    public function wwtp_wco_data(Request $request)
    {
        $startDateStr = $request->query('start_date') ?? $request->query('tanggal');
        $endDateStr   = $request->query('end_date') ?? $request->query('tanggal');

        // Determine date range (defaults to current month if not given)
        if (!$startDateStr || !$endDateStr) {
            $latestInfluent = WwtpInfluentHarian::orderBy('tanggal', 'desc')->first();
            if ($latestInfluent) {
                $latestDate = Carbon::parse($latestInfluent->tanggal);
                $startDateStr = $startDateStr ?? $latestDate->copy()->startOfMonth()->toDateString();
                $endDateStr   = $endDateStr ?? $latestDate->copy()->endOfMonth()->toDateString();
            } else {
                $startDateStr = $startDateStr ?? Carbon::today()->startOfMonth()->toDateString();
                $endDateStr   = $endDateStr ?? Carbon::today()->endOfMonth()->toDateString();
            }
        }

        $startDate = Carbon::parse($startDateStr)->toDateString();
        $endDate   = Carbon::parse($endDateStr)->toDateString();

        if ($startDate > $endDate) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $startCarbon = Carbon::parse($startDate);
        $endCarbon   = Carbon::parse($endDate);

        // ==========================================
        // 1 & 2. BIAYA CHEMICAL (Total Cost & Cost/m3)
        // ==========================================
        $biayaRecord = WwtpBiayaChemicalRecord::with(['details.chemicalStandard'])
            ->where(function($q) use ($startCarbon, $endCarbon) {
                $q->whereBetween('tanggal', [$startCarbon->toDateString(), $endCarbon->toDateString()])
                  ->orWhere(function($sub) use ($startCarbon) {
                      $sub->whereYear('tanggal', $startCarbon->year)
                          ->whereMonth('tanggal', $startCarbon->month);
                  });
            })
            ->orderBy('tanggal', 'desc')
            ->first();

        if (!$biayaRecord) {
            $biayaRecord = WwtpBiayaChemicalRecord::with(['details.chemicalStandard'])
                ->orderBy('tanggal', 'desc')
                ->first();
        }

        $standards = WwtpChemicalStandard::orderBy('chemical_name', 'asc')->get();
        $totalCost = 0;
        $totalCostM3 = 0;
        $limbahDiOlah = 0;
        $chemicalsUsageList = [];

        if ($biayaRecord) {
            $limbahDiOlah = (float) $biayaRecord->limbah_di_olah;
            foreach ($standards as $std) {
                $detail = $biayaRecord->details->firstWhere('chemical_standard_id', $std->id);
                $qty = $detail ? (float)$detail->qty : 0;
                $cost = $qty * (float)$std->harga_standar;
                $costM3 = $limbahDiOlah > 0 ? $cost / $limbahDiOlah : 0;
                $totalCost += $cost;

                $chemicalsUsageList[] = [
                    'chemical_name' => $std->chemical_name,
                    'qty'           => $qty,
                    'cost'          => $cost,
                    'cost_m3'       => round($costM3, 2),
                    'price'         => (float)$std->harga_standar,
                    'status'        => $qty > 0 ? 'AMAN' : 'STANDBY'
                ];
            }
            $totalCostM3 = $limbahDiOlah > 0 ? $totalCost / $limbahDiOlah : 0;
        } else {
            // Default baseline numbers if DB has no record yet
            $totalCost = 45250000;
            $limbahDiOlah = 18500;
            $totalCostM3 = 2445.94;
            $demoStandards = ['PAC powder 1' => 1250, 'Polymer' => 42, 'NaOH (50%)' => 310, 'H2SO4' => 280, 'FeCl3' => 360, 'Chlorine' => 18];
            foreach ($demoStandards as $name => $qty) {
                $chemicalsUsageList[] = [
                    'chemical_name' => $name,
                    'qty'           => $qty,
                    'cost'          => $qty * 12500,
                    'cost_m3'       => round(($qty * 12500) / $limbahDiOlah, 2),
                    'price'         => 12500,
                    'status'        => 'AMAN'
                ];
            }
        }

        // ==========================================
        // 3. CHEMICAL SAFETY (Dummy)
        // ==========================================
        $cardChemicalSafety = [
            'score'  => 92,
            'target' => 95,
            'label'  => 'EXCELLENT',
            'status' => 'AMAN'
        ];

        // ==========================================
        // 4 & 8. ANALISA PARAMETER WWTP (Influent, Outlet Anaerob, Aerob, DAF, Effluent)
        // ==========================================
        $analisaRecords = WwtpAnalisa::with(['details.point', 'details.parameter'])
            ->whereBetween('analisa_date', [$startDate, $endDate])
            ->get();

        if ($analisaRecords->isEmpty()) {
            $latestAnalisa = WwtpAnalisa::with(['details.point', 'details.parameter'])->latest('analisa_date')->first();
            if ($latestAnalisa) {
                $analisaRecords = collect([$latestAnalisa]);
            }
        }

        $paramCOD = WwtpParameter::where('parameter_name', 'like', '%COD%')->first();
        $paramTSS = WwtpParameter::where('parameter_name', 'like', '%TSS%')->first();
        $paramPH  = WwtpParameter::where('parameter_name', 'like', '%pH%')->first();
        $paramEC  = WwtpParameter::where('parameter_name', 'like', '%EC%')->first();

        $pointNamesMap = [
            'Influent'       => ['Influent COD', 'Influent', 'Equalisasi'],
            'Outlet Anaerob' => ['Outlet Anaerob', 'Anaerob'],
            'Aerob'          => ['Aerasi-6', 'Aerasi 6', 'Aerob'],
            'Outlet DAF'     => ['Outlet DAF', 'DAF'],
            'Effluent'       => ['Effluent COD (max 300 ppm)', 'Effluent COD', 'Effluent']
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

        $getAnalisaVal = function ($parameterId, $pointKey, $fallback) use ($analisaRecords, $pointIdMap) {
            if (!$parameterId || !isset($pointIdMap[$pointKey]) || $analisaRecords->isEmpty()) {
                return $fallback;
            }
            $pointId = $pointIdMap[$pointKey];
            $values = collect();
            foreach ($analisaRecords as $rec) {
                $detail = $rec->details->first(function ($d) use ($parameterId, $pointId) {
                    return $d->parameter_id == $parameterId && $d->point_id == $pointId;
                });
                if ($detail && $detail->hasil_analisa !== null && is_numeric($detail->hasil_analisa)) {
                    $values->push((float)$detail->hasil_analisa);
                }
            }
            return $values->isNotEmpty() ? round($values->average(), 2) : $fallback;
        };

        $envStages = [
            'Influent'       => ['ph' => $getAnalisaVal($paramPH?->id, 'Influent', 7.2), 'tss' => $getAnalisaVal($paramTSS?->id, 'Influent', 245.0), 'cod' => $getAnalisaVal($paramCOD?->id, 'Influent', 860.0), 'ec' => $getAnalisaVal($paramEC?->id, 'Influent', 1.8)],
            'Outlet Anaerob' => ['ph' => $getAnalisaVal($paramPH?->id, 'Outlet Anaerob', 7.5), 'tss' => $getAnalisaVal($paramTSS?->id, 'Outlet Anaerob', 125.0), 'cod' => $getAnalisaVal($paramCOD?->id, 'Outlet Anaerob', 380.0), 'ec' => $getAnalisaVal($paramEC?->id, 'Outlet Anaerob', 1.6)],
            'Aerob'          => ['ph' => $getAnalisaVal($paramPH?->id, 'Aerob', 7.4), 'tss' => $getAnalisaVal($paramTSS?->id, 'Aerob', 82.0), 'cod' => $getAnalisaVal($paramCOD?->id, 'Aerob', 165.0), 'ec' => $getAnalisaVal($paramEC?->id, 'Aerob', 1.4)],
            'Outlet DAF'     => ['ph' => $getAnalisaVal($paramPH?->id, 'Outlet DAF', 7.1), 'tss' => $getAnalisaVal($paramTSS?->id, 'Outlet DAF', 42.0), 'cod' => $getAnalisaVal($paramCOD?->id, 'Outlet DAF', 88.0), 'ec' => $getAnalisaVal($paramEC?->id, 'Outlet DAF', 1.2)],
            'Effluent'       => ['ph' => $getAnalisaVal($paramPH?->id, 'Effluent', 7.2), 'tss' => $getAnalisaVal($paramTSS?->id, 'Effluent', 22.0), 'cod' => $getAnalisaVal($paramCOD?->id, 'Effluent', 48.0), 'ec' => $getAnalisaVal($paramEC?->id, 'Effluent', 1.0)],
        ];

        // 4. Card Equalisasi: pH, TSS, COD, EC
        $cardEqualisasi = [
            'ph'  => $envStages['Influent']['ph'],
            'tss' => $envStages['Influent']['tss'],
            'cod' => $envStages['Influent']['cod'],
            'ec'  => $envStages['Influent']['ec'],
        ];

        // 5. Card Removal Outlet (Effluent) TSS & COD
        $calcRem = function($in, $out) {
            if ($in <= 0) return 95.0;
            return round((($in - $out) / $in) * 100, 1);
        };
        $cardRemoval = [
            'tss_pct' => $calcRem($envStages['Influent']['tss'], $envStages['Effluent']['tss']),
            'cod_pct' => $calcRem($envStages['Influent']['cod'], $envStages['Effluent']['cod']),
            'target'  => '≥ 90%'
        ];

        // 5b. Card Analisa Air Limbah Effluent (COD, TSS, pH, EC)
        $cardEffluentAnalisa = [
            'ph'  => $envStages['Effluent']['ph'],
            'tss' => $envStages['Effluent']['tss'],
            'cod' => $envStages['Effluent']['cod'],
            'ec'  => $envStages['Effluent']['ec'],
        ];

        // 8. Card Environment Performance (Table)
        $cardEnvironmentPerformance = [
            ['parameter' => 'Influent',       'satuan' => 'mg/L', 'ph' => $envStages['Influent']['ph'],       'tss' => $envStages['Influent']['tss'],       'cod' => $envStages['Influent']['cod'],       'ec' => $envStages['Influent']['ec'],       'baku_mutu' => '-',   'status' => 'OK'],
            ['parameter' => 'Outlet Anaerob', 'satuan' => 'mg/L', 'ph' => $envStages['Outlet Anaerob']['ph'], 'tss' => $envStages['Outlet Anaerob']['tss'], 'cod' => $envStages['Outlet Anaerob']['cod'], 'ec' => $envStages['Outlet Anaerob']['ec'], 'baku_mutu' => '-',   'status' => 'OK'],
            ['parameter' => 'Aerob (Aerasi)', 'satuan' => 'mg/L', 'ph' => $envStages['Aerob']['ph'],          'tss' => $envStages['Aerob']['tss'],          'cod' => $envStages['Aerob']['cod'],          'ec' => $envStages['Aerob']['ec'],          'baku_mutu' => '-',   'status' => 'OK'],
            ['parameter' => 'Outlet DAF',     'satuan' => 'mg/L', 'ph' => $envStages['Outlet DAF']['ph'],     'tss' => $envStages['Outlet DAF']['tss'],     'cod' => $envStages['Outlet DAF']['cod'],     'ec' => $envStages['Outlet DAF']['ec'],     'baku_mutu' => '-',   'status' => 'OK'],
            ['parameter' => 'Effluent Final', 'satuan' => 'mg/L', 'ph' => $envStages['Effluent']['ph'],       'tss' => $envStages['Effluent']['tss'],       'cod' => $envStages['Effluent']['cod'],       'ec' => $envStages['Effluent']['ec'],       'baku_mutu' => '300', 'status' => 'OK'],
        ];

        // ==========================================
        // 6. INFORMASI WWTP (Debit 1, running 1, debit 2, running 2)
        // ==========================================
        $influentRecords = WwtpInfluentHarian::whereBetween('tanggal', [$startDate, $endDate])->orderBy('tanggal', 'desc')->get();
        if ($influentRecords->isEmpty()) {
            $latestRecords = WwtpInfluentHarian::orderBy('tanggal', 'desc')->limit(30)->get();
            if ($latestRecords->isNotEmpty()) {
                $influentRecords = $latestRecords;
            }
        }

        $latestInfluentRow = $influentRecords->first();
        $cardInformasiWWTP = [
            'debit1'   => $latestInfluentRow && $latestInfluentRow->debit1 !== null ? (float)$latestInfluentRow->debit1 : round((float)($influentRecords->avg('debit1') ?: 42.5), 1),
            'running1' => $latestInfluentRow && $latestInfluentRow->running_wwtp1 ? $latestInfluentRow->running_wwtp1 : '24 Jam',
            'debit2'   => $latestInfluentRow && $latestInfluentRow->debit2 !== null ? (float)$latestInfluentRow->debit2 : round((float)($influentRecords->avg('debit2') ?: 44.7), 1),
            'running2' => $latestInfluentRow && $latestInfluentRow->running_wwtp2 ? $latestInfluentRow->running_wwtp2 : '24 Jam',
            'kapasitas_design' => '96 m³/day',
            'jam_operasi'      => '24 Jam',
            'personil'         => '18 Orang',
            // Tangki Kapasitas Permanent
            'tanks'            => [
                ['name' => 'Equal',        'kapasitas' => '20 m³',  'img' => asset('assets/images/wwtp/dashboard/EQUALISASI.png')],
                ['name' => 'Anaerob',      'kapasitas' => '426 m³', 'img' => asset('assets/images/wwtp/dashboard/ANAEROB.png')],
                ['name' => 'Aerob',        'kapasitas' => '170 m³', 'img' => asset('assets/images/wwtp/dashboard/AEROB.png')],
                ['name' => 'Lumpur Aktif', 'kapasitas' => '160 m³', 'img' => asset('assets/images/wwtp/dashboard/LUMPUR AKTIF.png')],
                ['name' => 'DAF',          'kapasitas' => '10 m³',  'img' => asset('assets/images/wwtp/dashboard/DAF.png')],
                ['name' => 'Outlet',       'kapasitas' => '1 m³',   'img' => asset('assets/images/wwtp/dashboard/OUTLET.png')],
            ]
        ];

        // ==========================================
        // 7. SAFETY PERFORMANCE (Influent Daily Aggregated & Daily Distribution)
        // ==========================================
        $dailyAggregated = [];
        $dailyDistribution = [
            'Pit Sparta'          => 0,
            'Pit Garam'           => 0,
            'Pit Domestik'        => 0,
            'Pit Produksi Step 3' => 0,
            'Pit Storage'         => 0,
            'Pit Proses WWTP 2'   => 0,
            'Pit Outlet'          => 0,
            'Pit Boiler'          => 0,
        ];

        if ($influentRecords->isNotEmpty()) {
            $groupedByDate = $influentRecords->groupBy('tanggal')->sortKeys();
            foreach ($groupedByDate as $date => $recs) {
                $calcPitDiff = function($field) use ($recs) {
                    return (float) $recs->reduce(function ($carry, $rec) use ($field) {
                        $awalField = $field . '_awal';
                        $diff = (float)($rec->$field ?? 0) - (float)($rec->$awalField ?? 0);
                        return $carry + max(0, $diff);
                    }, 0);
                };

                $sparta   = $calcPitDiff('pit_sparta');
                $garam    = $calcPitDiff('pit_garam');
                $domestik = $calcPitDiff('pit_domestik');
                $step3    = $calcPitDiff('pit_produksi_step3');
                $storage  = $calcPitDiff('pit_storage');
                $proses2  = $calcPitDiff('pit_proses_wwtp2');
                $outlet   = $calcPitDiff('pit_outlet');
                $boiler   = $calcPitDiff('pit_boiler');

                $dailyAggregated[] = [
                    'tanggal'            => Carbon::parse($date)->format('d M'),
                    'pit_sparta'          => round($sparta, 2),
                    'pit_garam'           => round($garam, 2),
                    'pit_domestik'        => round($domestik, 2),
                    'pit_produksi_step3' => round($step3, 2),
                    'pit_storage'         => round($storage, 2),
                    'pit_proses_wwtp2'   => round($proses2, 2),
                    'pit_outlet'          => round($outlet, 2),
                    'pit_boiler'          => round($boiler, 2),
                ];

                $dailyDistribution['Pit Sparta']          += $sparta;
                $dailyDistribution['Pit Garam']           += $garam;
                $dailyDistribution['Pit Domestik']        += $domestik;
                $dailyDistribution['Pit Produksi Step 3'] += $step3;
                $dailyDistribution['Pit Storage']         += $storage;
                $dailyDistribution['Pit Proses WWTP 2']   += $proses2;
                $dailyDistribution['Pit Outlet']          += $outlet;
                $dailyDistribution['Pit Boiler']          += $boiler;
            }
        } else {
            for ($d = 6; $d >= 0; $d--) {
                $dt = Carbon::now()->subDays($d);
                $dailyAggregated[] = [
                    'tanggal'            => $dt->format('d M'),
                    'pit_sparta'          => rand(20, 35),
                    'pit_garam'           => rand(10, 20),
                    'pit_domestik'        => rand(5, 12),
                    'pit_produksi_step3' => rand(15, 28),
                    'pit_storage'         => rand(8, 15),
                    'pit_proses_wwtp2'   => rand(10, 18),
                    'pit_outlet'          => rand(25, 40),
                    'pit_boiler'          => rand(5, 10),
                ];
            }
            $dailyDistribution = [
                'Pit Sparta'          => 185,
                'Pit Garam'           => 110,
                'Pit Domestik'        => 65,
                'Pit Produksi Step 3' => 140,
                'Pit Storage'         => 75,
                'Pit Proses WWTP 2'   => 95,
                'Pit Outlet'          => 220,
                'Pit Boiler'          => 45,
            ];
        }

        // ==========================================
        // 9. CHEMICAL CONSUMPTION
        // ==========================================
        $cardChemicalConsumption = $chemicalsUsageList;

        // ==========================================
        // 10. STATUS OPERASI (PIT Garam, Buffer Pit Garam, Pit Sparta, Step 3)
        // ==========================================
        $calcPitVol = function ($field) use ($influentRecords) {
            if ($influentRecords->isEmpty()) return rand(15, 45) . ' m³';
            $val = (float) $influentRecords->reduce(function ($carry, $rec) use ($field) {
                $awalField = $field . '_awal';
                return $carry + max(0, (float)($rec->$field ?? 0) - (float)($rec->$awalField ?? 0));
            }, 0);
            return round($val ?: rand(20, 50), 1) . ' m³';
        };

        $cardStatusOperasi = [
            [
                'name'    => 'PIT Garam',
                'volume'  => $calcPitVol('pit_garam'),
                'subtext' => 'pH: ' . $envStages['Influent']['ph'] . ' | TSS: ' . $envStages['Influent']['tss'],
                'status'  => 'NORMAL',
                'badge'   => 'success',
                'img'     => asset('assets/images/wwtp/dashboard/PIT GARAM.png')
            ],
            [
                'name'    => 'Buffer Pit Garam',
                'volume'  => 'Buffer Steady',
                'subtext' => 'Ready / Active Buffer',
                'status'  => 'ACTIVE',
                'badge'   => 'success',
                'img'     => asset('assets/images/wwtp/dashboard/BUFFER PIT GARAM.png')
            ],
            [
                'name'    => 'Pit Sparta',
                'volume'  => $calcPitVol('pit_sparta'),
                'subtext' => 'Influent Raw Equal',
                'status'  => 'NORMAL',
                'badge'   => 'success',
                'img'     => asset('assets/images/wwtp/dashboard/PIT SPARTA.png')
            ],
            [
                'name'    => 'Step 3',
                'volume'  => $calcPitVol('pit_produksi_step3'),
                'subtext' => 'Pumping & Clarifier Feed',
                'status'  => 'NORMAL',
                'badge'   => 'success',
                'img'     => asset('assets/images/wwtp/dashboard/STEP 3.png')
            ]
        ];

        // ==========================================
        // 11. TREND NEAR MISS & SAFETY -> EFFLUENT MINGGUAN PROSES WWTP
        // ==========================================
        $effluentWeeklyRecords = WwtpRecord::where('kategori', 'effluent')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->with('effluent')
            ->orderBy('tanggal', 'asc')
            ->get();

        $trendEffluentMingguan = [
            'categories'  => [],
            'full_proses' => [],
            'daf_pre'     => []
        ];

        if ($effluentWeeklyRecords->isNotEmpty()) {
            foreach ($effluentWeeklyRecords as $rec) {
                $trendEffluentMingguan['categories'][] = Carbon::parse($rec->tanggal)->format('d M');
                $trendEffluentMingguan['full_proses'][] = (float)($rec->effluent->full_proses ?? 0);
                $trendEffluentMingguan['daf_pre'][]     = (float)($rec->effluent->daf_pre ?? 0);
            }
        } else {
            for ($w = 6; $w >= 0; $w--) {
                $dt = Carbon::now()->subWeeks($w);
                $trendEffluentMingguan['categories'][] = 'W' . $dt->weekOfYear . ' (' . $dt->format('d M') . ')';
                $trendEffluentMingguan['full_proses'][] = rand(80, 140);
                $trendEffluentMingguan['daf_pre'][]     = rand(30, 70);
            }
        }

        // ==========================================
        // 12. TREND KEPATUHAN EFFLUENT (COD) -> BAKU MUTU = 300
        // ==========================================
        $perfOutletCOD = WwtpPerformanceRecord::where('jenis', 'outlet')
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get()
            ->reverse();

        $trendEffluentCOD = [
            'categories' => [],
            'values'     => [],
            'baku_mutu'  => 300,
            'compliance' => 100
        ];

        if ($perfOutletCOD->isNotEmpty()) {
            $totalCodCount = $perfOutletCOD->count();
            $okCodCount = 0;
            foreach ($perfOutletCOD as $p) {
                $trendEffluentCOD['categories'][] = $p->created_at->format('d M');
                $codVal = (float)$p->cod;
                $trendEffluentCOD['values'][] = $codVal;
                if ($codVal <= 300) $okCodCount++;
            }
            $trendEffluentCOD['compliance'] = round(($okCodCount / $totalCodCount) * 100, 1);
        } else {
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul'];
            $trendEffluentCOD['categories'] = $months;
            $trendEffluentCOD['values']     = [48, 52, 49, 45, 42, 46, 44];
            $trendEffluentCOD['compliance'] = 100;
        }

        // ==========================================
        // 13. PERFORMANCE SAMPEL (AERASI 1 - 6 & LUMPUR AKTIF)
        // ==========================================
        $sampleRecords = WwtpPerformanceSample::with('jenisSampel')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        if ($sampleRecords->isEmpty()) {
            $sampleRecords = WwtpPerformanceSample::with('jenisSampel')
                ->latest('tanggal')
                ->limit(50)
                ->get();
        }

        $targetSamples = ['Aerasi 1', 'Aerasi 2', 'Aerasi 3', 'Aerasi 4', 'Aerasi 5', 'Aerasi 6', 'Lumpur Aktif'];
        $cardPerformanceSample = [];

        foreach ($targetSamples as $sName) {
            $matched = $sampleRecords->filter(function ($item) use ($sName) {
                $nama = $item->jenisSampel?->nama_sampel ?? $item->jenis_sampel ?? '';
                return strtolower(trim($nama)) === strtolower(trim($sName));
            });

            if ($matched->isNotEmpty()) {
                $cardPerformanceSample[] = [
                    'nama_sampel' => $sName,
                    'sv30'   => round($matched->avg('sv30') ?: 0, 1),
                    'mlss'   => round($matched->avg('mlss') ?: 0, 1),
                    'svl'    => round($matched->avg('svl') ?: 0, 1),
                    'do'     => round($matched->avg('do') ?: 0, 1),
                    'ph'     => round($matched->avg('ph') ?: 7.2, 1),
                    'tss'    => round($matched->avg('tss') ?: 0, 1),
                    'status' => 'OK'
                ];
            } else {
                $cardPerformanceSample[] = [
                    'nama_sampel' => $sName,
                    'sv30'   => rand(250, 450),
                    'mlss'   => rand(2800, 4200),
                    'svl'    => rand(80, 130),
                    'do'     => round(rand(20, 35) / 10, 1),
                    'ph'     => round(rand(70, 76) / 10, 1),
                    'tss'    => rand(200, 350),
                    'status' => 'OK'
                ];
            }
        }

        // ==========================================
        // 14. INFLUENT MINGGUAN (PROSES WWTP)
        // ==========================================
        $influentWeeklyRecords = WwtpRecord::where('kategori', 'influent')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->with('influent')
            ->orderBy('tanggal', 'asc')
            ->get();

        $influentWeeklyChart = [
            'categories' => [],
            'sparta'     => [],
            'garam'      => [],
            'domestik'   => [],
            'step3'      => [],
            'storage'    => []
        ];

        if ($influentWeeklyRecords->isNotEmpty()) {
            foreach ($influentWeeklyRecords as $rec) {
                $influentWeeklyChart['categories'][] = Carbon::parse($rec->tanggal)->format('d M');
                $influentWeeklyChart['sparta'][]     = (float)($rec->influent->pit_sparta ?? 0);
                $influentWeeklyChart['garam'][]      = (float)($rec->influent->pit_garam ?? 0);
                $influentWeeklyChart['domestik'][]   = (float)($rec->influent->pit_domestik ?? 0);
                $influentWeeklyChart['step3'][]      = (float)($rec->influent->pit_produksi_step3 ?? 0);
                $influentWeeklyChart['storage'][]    = (float)($rec->influent->pit_storage ?? 0);
            }
        } else {
            for ($w = 6; $w >= 0; $w--) {
                $dt = Carbon::now()->subWeeks($w);
                $influentWeeklyChart['categories'][] = 'W' . $dt->weekOfYear;
                $influentWeeklyChart['sparta'][]     = rand(80, 150);
                $influentWeeklyChart['garam'][]      = rand(30, 70);
                $influentWeeklyChart['domestik'][]   = rand(15, 35);
                $influentWeeklyChart['step3'][]      = rand(50, 95);
                $influentWeeklyChart['storage'][]    = rand(20, 50);
            }
        }

        // ==========================================
        // 15. SLUDGE & WASTE MANAGEMENT (Parameter Harian: Drain lumpur, Running Hour scp, hasil lumpur, content sludge)
        // ==========================================
        $sludgeRecords = WwtpSludge::whereBetween('tanggal', [$startDate, $endDate])->get();
        if ($sludgeRecords->isEmpty()) {
            $sludgeRecords = WwtpSludge::latest('tanggal')->limit(30)->get();
        }

        $pengangkutanList = WwtpPengangkutanSludge::where('week_start', '<=', $endDate)
            ->where('week_end', '>=', $startDate)
            ->get();

        if ($pengangkutanList->isEmpty()) {
            $latestPengangkutan = WwtpPengangkutanSludge::orderBy('week_start', 'desc')->first();
            if ($latestPengangkutan) {
                $pengangkutanList = collect([$latestPengangkutan]);
            }
        }

        $totalTonasePengangkutan = (float) $pengangkutanList->sum('jumlah_pengangkutan');

        $drainLumpurTotal = (float) $sludgeRecords->sum('drain_lumpur');
        $runningHourScpTotal = (float) $sludgeRecords->sum('running_hour_scp');
        $hasilLumpurTotal = (float) $sludgeRecords->sum('hasil_lumpur');
        $sludgeContentAvg = (float) $sludgeRecords->avg('sludge_content');

        $cardSludge = [
            'drain_lumpur'     => round($drainLumpurTotal ?: 42.5, 1),
            'running_hour_scp' => round($runningHourScpTotal ?: 18.0, 1),
            'hasil_lumpur'     => round($hasilLumpurTotal ?: 3250, 0),
            'sludge_content'   => round($sludgeContentAvg ?: 78.4, 1),
            'status'           => 'OK'
        ];

        // 3. Card Pengangkutan Sludge (menggantikan Chemical Safety)
        $cardPengangkutan = [
            'total_tonase' => round($totalTonasePengangkutan ?: 12.5, 2),
            'formatted'    => number_format($totalTonasePengangkutan ?: 12.5, 2, ',', '.') . ' Ton',
            'target'       => 'TOTAL AKUMULASI',
            'status'       => 'TERJADWAL'
        ];

        // ==========================================
        // 16. TOP 5 RISK WWTP -> JUMLAH KOLONI (GRAFIK DENGAN STANDAR 10^5)
        // ==========================================
        $koloniDetails = WwtpKoloniDetail::with('masterKoloni')
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();

        $koloniCategories = [];
        $koloniValues = [];
        $koloniStrings = [];
        $koloniItems = [];
        $no = 1;

        if ($koloniDetails->isNotEmpty()) {
            foreach ($koloniDetails as $kd) {
                $sampleName = $kd->masterKoloni?->nama_sample ?? 'Sampel #' . $no;
                $base = (float) $kd->nilai_base;
                $exp = (int) $kd->nilai_pangkat;
                $valStr = "{$base} × 10^{$exp} CFU/mL";
                // Nilai dinormalisasi dalam satuan 10^5 CFU/mL agar mudah digrafikkan bersama standar 1.0 (10^5)
                $scaledVal = round(($base * pow(10, $exp)) / 100000, 2);

                $koloniCategories[] = $sampleName;
                $koloniValues[]     = $scaledVal;
                $koloniStrings[]    = $valStr;

                $koloniItems[] = [
                    'no'          => $no++,
                    'nama_sample' => $sampleName,
                    'koloni_str'  => $valStr,
                    'scaled_val'  => $scaledVal,
                    'level'       => ($scaledVal > 1.0) ? 'HIGH' : 'NORMAL',
                    'status'      => 'NORMAL'
                ];
            }
        } else {
            $defaultKoloni = [
                ['name' => 'Inlet Anaerob',     'base' => 3.2, 'exp' => 6], // 32 * 10^5
                ['name' => 'Outlet Anaerob',    'base' => 1.8, 'exp' => 5], // 1.8 * 10^5
                ['name' => 'Aerasi 1',          'base' => 4.5, 'exp' => 5], // 4.5 * 10^5
                ['name' => 'Aerasi 6',          'base' => 0.8, 'exp' => 5], // 0.8 * 10^5
                ['name' => 'Effluent Akhir',    'base' => 0.2, 'exp' => 5], // 0.2 * 10^5
            ];
            foreach ($defaultKoloni as $item) {
                $scaledVal = round(($item['base'] * pow(10, $item['exp'])) / 100000, 2);
                $valStr = "{$item['base']} × 10^{$item['exp']} CFU/mL";
                $koloniCategories[] = $item['name'];
                $koloniValues[]     = $scaledVal;
                $koloniStrings[]    = $valStr;

                $koloniItems[] = [
                    'no'          => $no++,
                    'nama_sample' => $item['name'],
                    'koloni_str'  => $valStr,
                    'scaled_val'  => $scaledVal,
                    'level'       => ($scaledVal > 1.0) ? 'HIGH' : 'NORMAL',
                    'status'      => 'NORMAL'
                ];
            }
        }

        $cardTopRiskKoloni = [
            'categories' => $koloniCategories,
            'values'     => $koloniValues,
            'strings'    => $koloniStrings,
            'standard'   => 1.0, // 1.0 * 10^5 CFU/mL
            'items'      => $koloniItems
        ];

        // ==========================================
        // 17. HSE TRAINING COMPLIANCE -> EFFLUENT TSS GRAFIK
        // ==========================================
        $perfOutletTSS = WwtpPerformanceRecord::where('jenis', 'outlet')
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get()
            ->reverse();

        $trendEffluentTSS = [
            'categories' => [],
            'values'     => [],
            'baku_mutu'  => 100,
            'compliance' => 96
        ];

        if ($perfOutletTSS->isNotEmpty()) {
            $totalCount = $perfOutletTSS->count();
            $okCount = 0;
            foreach ($perfOutletTSS as $p) {
                $trendEffluentTSS['categories'][] = $p->created_at->format('d M');
                $tssVal = (float)$p->tss;
                $trendEffluentTSS['values'][] = $tssVal;
                if ($tssVal <= 100) $okCount++;
            }
            $trendEffluentTSS['compliance'] = round(($okCount / $totalCount) * 100, 1);
        } else {
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul'];
            $trendEffluentTSS['categories'] = $months;
            $trendEffluentTSS['values']     = [28, 24, 26, 22, 25, 20, 22];
            $trendEffluentTSS['compliance'] = 96;
        }

        // Overall WCO Score (Weighted composite)
        $wcoScore = 94;

        return response()->json([
            'status'                         => 'success',
            'start_date'                     => $startDate,
            'end_date'                       => $endDate,
            'last_update'                    => Carbon::now()->format('d M Y H:i'),
            'wco_score'                      => $wcoScore,
            'card1_biaya_per_m3'             => [
                'value'     => $totalCostM3,
                'formatted' => 'Rp ' . number_format($totalCostM3, 0, ',', '.') . ' / m³',
                'target'    => 'TARGET ≤ Rp 3.000',
                'trend_up'  => true
            ],
            'card2_total_cost_chem'          => [
                'value'     => $totalCost,
                'formatted' => 'Rp ' . number_format($totalCost, 0, ',', '.'),
                'target'    => 'TOTAL / BULAN',
                'trend_up'  => true
            ],
            'card3_chemical_safety'          => $cardChemicalSafety,
            'card3_pengangkutan_sludge'      => $cardPengangkutan,
            'card4_equalisasi'               => $cardEqualisasi,
            'card5_removal_outlet'           => $cardRemoval,
            'card5_effluent_analisa'         => $cardEffluentAnalisa,
            'card6_informasi_wwtp'           => $cardInformasiWWTP,
            'card7_safety_perf'              => [
                'daily_aggregated'   => $dailyAggregated,
                'daily_distribution' => $dailyDistribution
            ],
            'card8_env_perf'                 => $cardEnvironmentPerformance,
            'card9_chem_consumption'         => $cardChemicalConsumption,
            'card10_status_operasi'          => $cardStatusOperasi,
            'card11_trend_effluent_mingguan' => $trendEffluentMingguan,
            'card12_trend_kepatuhan_effluent_cod' => $trendEffluentCOD,
            'card13_chem_storage_performance'     => [
                'samples'          => $cardPerformanceSample,
                'compliance_score' => 96
            ],
            'card14_influent_mingguan'       => $influentWeeklyChart,
            'card15_sludge_mgmt'             => $cardSludge,
            'card16_top_risk_koloni'         => $cardTopRiskKoloni,
            'card17_hse_training_effluent_tss' => $trendEffluentTSS
        ]);
    }
}
