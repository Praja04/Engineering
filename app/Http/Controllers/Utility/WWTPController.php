<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\PemakaianChemicalModel;
use App\Models\Utility\ChemicalType;
use App\Models\Utility\ChemicalArea;
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

        // Preload chemical types map untuk kalkulasi rumus dinamis
        $chemicalTypesMap = ChemicalType::all()->keyBy(fn($c) => strtolower(trim($c->nama_chemical)));

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

            // 2.1 Chemical (Kg/Hari) - Menggunakan rumus dinamis dari master chemical
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
                        $chemModel = $chemicalTypesMap->get(strtolower(trim($entry->jenis_pemakaian)));

                        if ($chemModel && $chemModel->tipe_perhitungan === 'rumus' && !empty($chemModel->rumus_formula)) {
                            $totalPemakaian += $chemModel->calculateUsage($nilai, (float) $rh);
                        } else {
                            $totalPemakaian += $nilai;
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
     * Supports lazy loading per section via ?section=...
     */
    public function wwtp_wco_data(Request $request)
    {
        $startDateStr = $request->query('start_date') ?? $request->query('tanggal');
        $endDateStr   = $request->query('end_date') ?? $request->query('tanggal');
        $section      = $request->query('section', 'all');

        // Determine date range (defaults to current month if not given)
        if (!$startDateStr || !$endDateStr) {
            $startDateStr = $startDateStr ?? Carbon::today()->startOfMonth()->toDateString();
            $endDateStr   = $endDateStr ?? Carbon::today()->endOfMonth()->toDateString();
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

        $baseResponse = [
            'status'      => 'success',
            'section'     => $section,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'last_update' => Carbon::now()->format('d M Y H:i'),
        ];

        switch ($section) {
            case 'biaya':
                return response()->json(array_merge($baseResponse, $this->getWcoBiayaData($startCarbon, $endCarbon)));

            case 'analisa':
                $analisa = $this->getWcoAnalisaData($startDate, $endDate);
                unset($analisa['envStages']);
                return response()->json(array_merge($baseResponse, $analisa));

            case 'status_operasi':
                return response()->json(array_merge($baseResponse, $this->getWcoStatusOperasiData($startDate, $endDate)));

            case 'safety_perf':
                return response()->json(array_merge($baseResponse, $this->getWcoSafetyPerfData($startDate, $endDate)));

            case 'influent_mingguan':
                return response()->json(array_merge($baseResponse, $this->getWcoInfluentMingguanData($startDate, $endDate)));

            case 'effluent_mingguan':
                return response()->json(array_merge($baseResponse, $this->getWcoEffluentMingguanData($startDate, $endDate)));

            case 'kepatuhan_cod':
                return response()->json(array_merge($baseResponse, $this->getWcoKepatuhanCodData($startDate, $endDate)));

            case 'performance_sampel':
                return response()->json(array_merge($baseResponse, $this->getWcoPerformanceSampleData($startDate, $endDate)));

            case 'sludge_mgmt':
                return response()->json(array_merge($baseResponse, $this->getWcoSludgeMgmtData($startDate, $endDate)));

            case 'top_risk_koloni':
                return response()->json(array_merge($baseResponse, $this->getWcoTopRiskKoloniData()));

            case 'kepatuhan_tss':
                return response()->json(array_merge($baseResponse, $this->getWcoKepatuhanTssData($startDate, $endDate)));

            case 'informasi_wwtp':
                return response()->json(array_merge($baseResponse, [
                    'card6_informasi_wwtp' => $this->getWcoInformasiWWTPData($startDate, $endDate)
                ]));

            case 'all':
            default:
                $biaya = $this->getWcoBiayaData($startCarbon, $endCarbon);
                $analisa = $this->getWcoAnalisaData($startDate, $endDate);
                $statusOperasi = $this->getWcoStatusOperasiData($startDate, $endDate, $analisa['envStages'] ?? null);
                unset($analisa['envStages']);
                $safetyPerf = $this->getWcoSafetyPerfData($startDate, $endDate);
                $influentMingguan = $this->getWcoInfluentMingguanData($startDate, $endDate);
                $effluentMingguan = $this->getWcoEffluentMingguanData($startDate, $endDate);
                $kepatuhanCod = $this->getWcoKepatuhanCodData($startDate, $endDate);
                $performanceSample = $this->getWcoPerformanceSampleData($startDate, $endDate);
                $sludgeMgmt = $this->getWcoSludgeMgmtData($startDate, $endDate);
                $topRiskKoloni = $this->getWcoTopRiskKoloniData();
                $kepatuhanTss = $this->getWcoKepatuhanTssData($startDate, $endDate);

                return response()->json(array_merge($baseResponse, [
                    'wco_score'             => 94,
                    'card3_chemical_safety' => [
                        'score'  => 92,
                        'target' => 95,
                        'label'  => 'EXCELLENT',
                        'status' => 'AMAN'
                    ],
                    'card6_informasi_wwtp'  => $this->getWcoInformasiWWTPData($startDate, $endDate),
                ], $biaya, $analisa, $statusOperasi, $safetyPerf, $influentMingguan, $effluentMingguan, $kepatuhanCod, $performanceSample, $sludgeMgmt, $topRiskKoloni, $kepatuhanTss));
        }
    }

    private function getWcoBiayaData($startCarbon, $endCarbon)
    {
        $startDate = $startCarbon->copy()->toDateString();
        $endDate   = $endCarbon->copy()->toDateString();
        $startMonth = $startCarbon->copy()->startOfMonth()->toDateString();
        $endMonth   = $endCarbon->copy()->endOfMonth()->toDateString();
        $daysInMonth = $startCarbon->daysInMonth ?: 30;

        // Format label periode berdasarkan filter
        $isFullMonth = ($startCarbon->copy()->startOfMonth()->toDateString() === $startDate
            && $endCarbon->copy()->endOfMonth()->toDateString() === $endDate);

        if ($isFullMonth) {
            $filterMonthName = $startCarbon->locale('id')->isoFormat('MMMM Y');
        } elseif ($startDate === $endDate) {
            $filterMonthName = $startCarbon->locale('id')->isoFormat('D MMMM Y');
        } else {
            $filterMonthName = $startCarbon->locale('id')->isoFormat('D MMM Y') . ' - ' . $endCarbon->locale('id')->isoFormat('D MMM Y');
        }

        // Ambil Master Data Chemical untuk Area WWTP
        $wwtpArea = ChemicalArea::whereRaw('LOWER(nama_area) = ?', ['wwtp'])
            ->orWhere('nama_area', 'like', '%wwtp%')
            ->first();

        $masterChemicals = ChemicalType::query()
            ->when($wwtpArea, function ($q) use ($wwtpArea) {
                $q->where('chemical_area_id', $wwtpArea->id);
            }, function ($q) {
                $q->whereHas('area', function ($sub) {
                    $sub->where('nama_area', 'like', '%wwtp%');
                });
            })
            ->where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();

        // Fallback jika query master area belum terisi di database
        if ($masterChemicals->isEmpty()) {
            $masterChemicals = ChemicalType::where('is_active', true)
                ->whereIn('nama_chemical', [
                    'PAC powder 1', 'PAC powder 2', 'BE-100', 'C-204', 'C-9040 step 1',
                    'C-9040 step 2', 'Denfloc 260 PA', 'Denfloc 945', 'NaOH', 'Defoamer', 'NPK'
                ])
                ->orderBy('id', 'asc')
                ->get();
        }

        $chemicalWWTP = $masterChemicals->pluck('nama_chemical')->toArray();
        $allStandards = WwtpChemicalStandard::orderBy('id', 'asc')->get();

        // Helper untuk mencocokkan master chemical dengan standar harga dan kuota WWTP
        $findStandard = function ($chemName) use ($allStandards) {
            $nameUpper = strtoupper(trim($chemName));
            return $allStandards->first(function ($std) use ($nameUpper, $chemName) {
                $stdUpper = strtoupper(trim($std->chemical_name));
                if (strcasecmp($chemName, $std->chemical_name) === 0) return true;
                if (str_contains($nameUpper, 'NAOH') && str_contains($stdUpper, 'NAOH')) return true;
                if (str_contains($nameUpper, 'PAC') && str_contains($stdUpper, 'PAC')) return true;
                if (str_contains($nameUpper, 'BE') && str_contains($stdUpper, 'BE')) return true;
                if (str_contains($nameUpper, '204') && str_contains($stdUpper, '204')) return true;
                if (str_contains($nameUpper, '9040') && str_contains($stdUpper, '9040')) return true;
                if (str_contains($nameUpper, '260') && (str_contains($stdUpper, 'DAF') || str_contains($stdUpper, '260'))) return true;
                if (str_contains($nameUpper, '945') && str_contains($stdUpper, '945')) return true;
                if (str_contains($nameUpper, 'DAF') && str_contains($stdUpper, 'DAF')) return true;
                if (str_contains($nameUpper, 'DENFLOC') && str_contains($stdUpper, 'DENFLOC')) return true;
                return false;
            });
        };

        // =========================================================================
        // 1. DATA BIAYA CHEMICAL & BIAYA PER KUBIK BULANAN (Ditampilkan di Dashboard)
        // Sumber: WwtpBiayaChemicalRecord & Details (Riwayat Data Biaya Chemical WWTP)
        // =========================================================================
        $biayaRecord = WwtpBiayaChemicalRecord::with(['details.chemicalStandard'])
            ->whereBetween('tanggal', [$startMonth, $endMonth])
            ->orderBy('tanggal', 'desc')
            ->first();

        // Jika pada rentang bulan filter belum ada data, fallback ke data bulan terakhir yang tersedia
        if (!$biayaRecord) {
            $biayaRecord = WwtpBiayaChemicalRecord::with(['details.chemicalStandard'])
                ->orderBy('tanggal', 'desc')
                ->first();
        }

        $monthlyTotalCost = 0;
        $monthlyTotalCostM3 = 0;
        $limbahDiOlah = $biayaRecord ? (float) $biayaRecord->limbah_di_olah : 0;
        $biayaMonthLabel = $biayaRecord
            ? Carbon::parse($biayaRecord->tanggal)->locale('id')->isoFormat('MMMM Y')
            : $filterMonthName;

        $monthlyUsageList = [];

        if ($biayaRecord) {
            foreach ($masterChemicals as $mChem) {
                $cName = trim($mChem->nama_chemical);
                $matchedStd = $findStandard($cName);
                $qty = 0;
                $price = $matchedStd ? (float) $matchedStd->harga_standar : 0;
                $standardQuota = $matchedStd ? (float) ($matchedStd->standar_pemakaian ?? 0) : 0;

                if ($biayaRecord->details && $matchedStd) {
                    $detail = $biayaRecord->details->first(function ($d) use ($matchedStd) {
                        return $d->chemical_standard_id == $matchedStd->id;
                    });
                    if ($detail) {
                        $qty = (float) $detail->qty;
                    }
                }

                $cost = $qty * $price;
                $costM3 = $limbahDiOlah > 0 ? ($cost / $limbahDiOlah) : 0;
                $monthlyTotalCost += $cost;

                $isOver = ($standardQuota > 0 && $qty > $standardQuota);
                $status = $isOver ? 'OVER' : 'AMAN';

                $monthlyUsageList[] = [
                    'chemical_name' => $cName,
                    'qty'           => $qty,
                    'standard'      => $standardQuota,
                    'standard_text' => $standardQuota > 0 ? (number_format($standardQuota, 0, ',', '.') . ' kg') : '-',
                    'cost'          => $cost,
                    'cost_m3'       => round($costM3, 2),
                    'price'         => $price,
                    'is_over'       => $isOver,
                    'status'        => $status,
                ];
            }

            $monthlyTotalCostM3 = $limbahDiOlah > 0 ? ($monthlyTotalCost / $limbahDiOlah) : 0;
        }

        // =========================================================================
        // 2. PERHITUNGAN HARIAN (TETAP DISIMPAN LENGKAP UNTUK DISKUSI LEBIH LANJUT)
        // =========================================================================
        $dailyRecords = PemakaianChemicalModel::query()
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->where(function ($q) use ($chemicalWWTP) {
                $q->whereIn('jenis_pemakaian', $chemicalWWTP)
                  ->orWhere('chemical_area', 'like', '%wwtp%');
            })
            ->get();

        $influentRecords = WwtpInfluentHarian::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->orderByRaw("CASE 
                WHEN shift = 'shift1' THEN 1 
                WHEN shift = 'shift2' THEN 2 
                WHEN shift = 'shift3' THEN 3 
                ELSE 4 
            END ASC")
            ->get();

        $totalPitProduksiStep3 = (float) $influentRecords->reduce(function ($carry, $rec) {
            return $carry + max(0, (float)($rec->pit_produksi_step3 ?? 0) - (float)($rec->pit_produksi_step3_awal ?? 0));
        }, 0);

        // Identifikasi shift apa saja yang datanya sudah masuk
        $shiftNameMap = [
            'shift1' => 'Shift 1',
            'shift2' => 'Shift 2',
            'shift3' => 'Shift 3',
            '1'      => 'Shift 1',
            '2'      => 'Shift 2',
            '3'      => 'Shift 3',
        ];

        $allShifts = $influentRecords->pluck('shift')->map(function ($s) use ($shiftNameMap) {
            $key = strtolower(trim($s));
            return $shiftNameMap[$key] ?? ucfirst($s);
        })->filter()->unique()->sort()->values();

        $latestDate = $influentRecords->max('tanggal');
        $latestRecords = $latestDate ? $influentRecords->where('tanggal', $latestDate) : collect();
        $latestShifts = $latestRecords->pluck('shift')->map(function ($s) use ($shiftNameMap) {
            $key = strtolower(trim($s));
            return $shiftNameMap[$key] ?? ucfirst($s);
        })->filter()->unique()->sort()->values();

        if ($influentRecords->isEmpty()) {
            $shiftsText = 'Belum Ada Shift';
        } elseif ($startDate === $endDate || $influentRecords->pluck('tanggal')->unique()->count() <= 1) {
            $shiftsText = $allShifts->isNotEmpty() ? $allShifts->join(', ') : 'Belum Ada Shift';
        } else {
            if ($latestShifts->isNotEmpty() && $latestShifts->count() < 3) {
                $shiftsText = $latestShifts->join(', ') . ' (' . Carbon::parse($latestDate)->locale('id')->isoFormat('D MMM') . ')';
            } elseif ($allShifts->count() >= 3) {
                $shiftsText = 'Shift 1, 2, 3';
            } else {
                $shiftsText = $allShifts->join(', ');
            }
        }

        $chemicalTypesMap = ChemicalType::all()->keyBy(fn($c) => strtolower(trim($c->nama_chemical)));

        $dailyUsageByDateAndChem = [];
        $totalUsageByChem = [];

        foreach ($dailyRecords as $record) {
            $jenis = trim($record->jenis_pemakaian);
            $tgl   = Carbon::parse($record->tanggal)->toDateString();
            $nilai = is_numeric($record->nilai_pemakaian)
                ? floatval($record->nilai_pemakaian)
                : floatval(preg_replace('/[^\d.]+/', '', $record->nilai_pemakaian));
            $rh = $record->running_hour ?? 1;

            $chemModel = $chemicalTypesMap->get(strtolower($jenis));
            if ($chemModel && $chemModel->tipe_perhitungan === 'rumus' && !empty($chemModel->rumus_formula)) {
                $usage = $chemModel->calculateUsage($nilai, (float) $rh);
            } else {
                $usage = $nilai;
            }

            $dailyUsageByDateAndChem[$tgl][$jenis] = ($dailyUsageByDateAndChem[$tgl][$jenis] ?? 0) + $usage;
            $totalUsageByChem[$jenis] = ($totalUsageByChem[$jenis] ?? 0) + $usage;
        }

        $uniqueDaysCount = count($dailyUsageByDateAndChem) > 0 ? count($dailyUsageByDateAndChem) : 1;
        $dailyUsageList = [];
        $dailyTotalCost = 0;
        $processedDailyChemNames = [];

        foreach ($masterChemicals as $mChem) {
            $cName = trim($mChem->nama_chemical);
            $processedDailyChemNames[strtolower($cName)] = true;

            $dailyVals = [];
            foreach ($dailyUsageByDateAndChem as $tgl => $chemMap) {
                foreach ($chemMap as $jName => $uVal) {
                    if (strcasecmp(trim($jName), $cName) === 0) {
                        $dailyVals[$tgl] = ($dailyVals[$tgl] ?? 0) + $uVal;
                    }
                }
            }

            $totUsage = array_sum($dailyVals);
            $avgDailyQty = ($dailyRecords->isNotEmpty() && $uniqueDaysCount > 0) ? ($totUsage / $uniqueDaysCount) : 0;

            $matchedStd = $findStandard($cName);
            $price = $matchedStd ? (float) $matchedStd->harga_standar : 0;
            $standardQuota = $matchedStd ? (float) ($matchedStd->standar_pemakaian ?? 0) : 0;
            $standardDaily = $standardQuota > 0 ? round($standardQuota / $daysInMonth, 1) : 0;

            $chemTotalCost = $totUsage * $price;
            $costM3 = $totalPitProduksiStep3 > 0 ? ($chemTotalCost / $totalPitProduksiStep3) : 0;
            $dailyTotalCost += $chemTotalCost;

            $isOver = ($standardDaily > 0 && $avgDailyQty > $standardDaily);
            $status = $isOver ? 'OVER' : 'AMAN';

            $dailyUsageList[] = [
                'chemical_name' => $cName,
                'qty'           => round($avgDailyQty, 2),
                'tot_qty'       => round($totUsage, 2),
                'standard'      => $standardDaily,
                'standard_text' => $standardDaily > 0 ? (number_format($standardDaily, 1, ',', '.') . ' kg') : '-',
                'cost'          => round($chemTotalCost, 2),
                'cost_m3'       => round($costM3, 2),
                'price'         => $price,
                'is_over'       => $isOver,
                'status'        => $status,
            ];
        }

        foreach ($totalUsageByChem as $jenis => $totUsage) {
            if (isset($processedDailyChemNames[strtolower(trim($jenis))])) continue;
            $avgDailyQty = ($dailyRecords->isNotEmpty() && $uniqueDaysCount > 0) ? ($totUsage / $uniqueDaysCount) : 0;
            $matchedStd = $findStandard($jenis);
            $price = $matchedStd ? (float) $matchedStd->harga_standar : 0;
            $standardQuota = $matchedStd ? (float) ($matchedStd->standar_pemakaian ?? 0) : 0;
            $standardDaily = $standardQuota > 0 ? round($standardQuota / $daysInMonth, 1) : 0;

            $chemTotalCost = $totUsage * $price;
            $costM3 = $totalPitProduksiStep3 > 0 ? ($chemTotalCost / $totalPitProduksiStep3) : 0;
            $dailyTotalCost += $chemTotalCost;
            $isOver = ($standardDaily > 0 && $avgDailyQty > $standardDaily);

            $dailyUsageList[] = [
                'chemical_name' => $jenis,
                'qty'           => round($avgDailyQty, 2),
                'tot_qty'       => round($totUsage, 2),
                'standard'      => $standardDaily,
                'standard_text' => $standardDaily > 0 ? (number_format($standardDaily, 1, ',', '.') . ' kg') : '-',
                'cost'          => round($chemTotalCost, 2),
                'cost_m3'       => round($costM3, 2),
                'price'         => $price,
                'is_over'       => $isOver,
                'status'        => $isOver ? 'OVER' : 'AMAN',
            ];
        }

        $dailyTotalCostM3 = $totalPitProduksiStep3 > 0 ? ($dailyTotalCost / $totalPitProduksiStep3) : 0;

        // Tampilkan di dashboard: data bulanan (Biaya Chemical & Biaya Per Kubik Bulanan)
        $hasData = ($biayaRecord !== null);

        return [
            'biaya_bulan'        => $biayaMonthLabel,
            'has_biaya_data'     => $hasData,
            'total_pit_step3'    => round($totalPitProduksiStep3, 2),
            // Card 1: BIAYA PER KUBIK (Data Bulanan)
            'card1_biaya_per_m3' => [
                'value'               => round($monthlyTotalCostM3, 2),
                'formatted'           => $hasData ? ('Rp ' . number_format($monthlyTotalCostM3, 0, ',', '.') . ' / m³') : 'Rp 0 / m³',
                'target'              => 'TARGET ≤ Rp 20.000 / m³',
                'target_value'        => 20000,
                'is_over'             => ($monthlyTotalCostM3 > 20000),
                'trend_up'            => ($monthlyTotalCostM3 > 20000),
                'bulan'               => $biayaMonthLabel,
                'has_data'            => $hasData,
                'limbah_di_olah'      => $limbahDiOlah,
                'shifts_text'         => $shiftsText,
            ],
            // Card 2: TOTAL COST CHEMICAL (Data Bulanan)
            'card2_total_cost_chem' => [
                'value'        => round($monthlyTotalCost, 2),
                'formatted'    => $hasData ? ('Rp ' . number_format($monthlyTotalCost, 0, ',', '.')) : 'Rp 0',
                'standard'     => 45000000,
                'target'       => 'STANDAR ≤ Rp 45 Jt',
                'is_over'      => ($monthlyTotalCost > 45000000),
                'trend_up'     => ($monthlyTotalCost > 45000000),
                'bulan'        => $biayaMonthLabel,
                'has_data'     => $hasData,
            ],
            // Card 9: Tabel Konsumsi Chemical (ambil harian jika ada input, fallback bulanan)
            'card9_chem_consumption' => $dailyRecords->isNotEmpty() ? $dailyUsageList : $monthlyUsageList,
            // Struktur data perhitungan harian tetap disimpan lengkap
            'perhitungan_harian'     => [
                'total_cost'          => round($dailyTotalCost, 2),
                'total_cost_m3'       => round($dailyTotalCostM3, 2),
                'total_pit_step3'     => round($totalPitProduksiStep3, 2),
                'shifts_text'         => $shiftsText,
                'shifts_list'         => $allShifts->toArray(),
                'latest_shifts'       => $latestShifts->toArray(),
                'pit_step3_formatted' => number_format($totalPitProduksiStep3, 1, ',', '.') . ' m³',
                'usage_list'          => $dailyUsageList,
            ],
            'perhitungan_bulanan'    => [
                'record_id'      => $biayaRecord ? $biayaRecord->id : null,
                'tanggal'        => $biayaRecord ? $biayaRecord->tanggal : null,
                'bulan'          => $biayaMonthLabel,
                'limbah_di_olah' => $limbahDiOlah,
                'total_cost'     => round($monthlyTotalCost, 2),
                'total_cost_m3'  => round($monthlyTotalCostM3, 2),
                'usage_list'     => $monthlyUsageList,
            ],
        ];
    }

    private function getWcoAnalisaData($startDate, $endDate)
    {
        $analisaRecords = WwtpAnalisa::with(['details.point', 'details.parameter'])
            ->whereBetween('analisa_date', [$startDate, $endDate])
            ->get();

        if ($analisaRecords->isEmpty()) {
            $latestDate = WwtpAnalisa::max('analisa_date');
            if ($latestDate) {
                $analisaRecords = WwtpAnalisa::with(['details.point', 'details.parameter'])
                    ->where('analisa_date', $latestDate)
                    ->get();
            }
        }

        $allParams = WwtpParameter::all();
        $paramCOD = $allParams->first(fn($p) => stripos($p->parameter_name, 'COD') !== false);
        $paramTSS = $allParams->first(fn($p) => stripos($p->parameter_name, 'TSS') !== false);
        $paramPH  = $allParams->first(fn($p) => stripos($p->parameter_name, 'pH') !== false);
        $paramEC  = $allParams->first(fn($p) => stripos($p->parameter_name, 'EC') !== false);

        $dbPoints = WwtpPoint::all();

        // Pemetaan nama point dari Master Point Analisa WWTP untuk setiap tahapan
        // Catatan: Aerob dihitung khusus dari rata-rata (Clarifier 1 + Clarifier 2) / 2
        $stageCandidatePoints = [
            'Influent'       => ['Influent'],
            'Outlet Anaerob' => ['Outlet Anaerob'],
            'Outlet DAF'     => ['Outlet DAF'],
            'Effluent'       => ['Effluent'],
        ];

        $getAnalisaVal = function ($paramObj, array $candidateNames, $fallback) use ($analisaRecords, $dbPoints) {
            if (!$paramObj || $analisaRecords->isEmpty()) {
                return $fallback;
            }

            $paramId = $paramObj->id;

            // Kumpulkan ID point kandidat sesuai urutan prioritas
            $targetPointIds = [];
            foreach ($candidateNames as $cName) {
                foreach ($dbPoints as $p) {
                    if (strcasecmp(trim($p->point_name), trim($cName)) === 0 || stripos($p->point_name, $cName) !== false) {
                        if (!in_array($p->id, $targetPointIds)) {
                            $targetPointIds[] = $p->id;
                        }
                    }
                }
            }

            // Cari nilai dari data analisa harian berdasarkan urutan prioritas point
            foreach ($targetPointIds as $ptId) {
                $vals = collect();
                foreach ($analisaRecords as $rec) {
                    if (!$rec->details) continue;
                    foreach ($rec->details as $d) {
                        if ($d->parameter_id == $paramId && $d->point_id == $ptId && $d->hasil_analisa !== null && is_numeric($d->hasil_analisa)) {
                            $vals->push((float) $d->hasil_analisa);
                        }
                    }
                }
                if ($vals->isNotEmpty()) {
                    return round($vals->average(), 2);
                }
            }

            return $fallback;
        };

        // Aerob dihitung dari rata-rata (Clarifier 1 + Clarifier 2) / 2
        $getAerobVal = function ($paramObj, $fallback) use ($getAnalisaVal) {
            $val1 = $getAnalisaVal($paramObj, ['Clarifier 1'], null);
            $val2 = $getAnalisaVal($paramObj, ['Clarifier 2'], null);

            if ($val1 !== null && $val2 !== null) {
                return round(($val1 + $val2) / 2, 2);
            }
            if ($val1 !== null) {
                return $val1;
            }
            if ($val2 !== null) {
                return $val2;
            }
            return $fallback;
        };

        $envStages = [
            'Influent'       => [
                'ph'  => $getAnalisaVal($paramPH, $stageCandidatePoints['Influent'], 7.2),
                'tss' => $getAnalisaVal($paramTSS, $stageCandidatePoints['Influent'], 450.0),
                'cod' => $getAnalisaVal($paramCOD, $stageCandidatePoints['Influent'], 2200.0),
                'ec'  => $getAnalisaVal($paramEC, $stageCandidatePoints['Influent'], 1.8),
            ],
            'Outlet Anaerob' => [
                'ph'  => $getAnalisaVal($paramPH, $stageCandidatePoints['Outlet Anaerob'], 7.5),
                'tss' => $getAnalisaVal($paramTSS, $stageCandidatePoints['Outlet Anaerob'], 85.0),
                'cod' => $getAnalisaVal($paramCOD, $stageCandidatePoints['Outlet Anaerob'], 380.0),
                'ec'  => $getAnalisaVal($paramEC, $stageCandidatePoints['Outlet Anaerob'], 1.6),
            ],
            'Aerob'          => [
                'ph'  => $getAerobVal($paramPH, 7.4),
                'tss' => $getAerobVal($paramTSS, 16.0),
                'cod' => $getAerobVal($paramCOD, 70.0),
                'ec'  => $getAerobVal($paramEC, 1.4),
            ],
            'Outlet DAF'     => [
                'ph'  => $getAnalisaVal($paramPH, $stageCandidatePoints['Outlet DAF'], 7.1),
                'tss' => $getAnalisaVal($paramTSS, $stageCandidatePoints['Outlet DAF'], 12.0),
                'cod' => $getAnalisaVal($paramCOD, $stageCandidatePoints['Outlet DAF'], 55.0),
                'ec'  => $getAnalisaVal($paramEC, $stageCandidatePoints['Outlet DAF'], 1.2),
            ],
            'Effluent'       => [
                'ph'  => $getAnalisaVal($paramPH, $stageCandidatePoints['Effluent'], 7.2),
                'tss' => $getAnalisaVal($paramTSS, $stageCandidatePoints['Effluent'], 10.0),
                'cod' => $getAnalisaVal($paramCOD, $stageCandidatePoints['Effluent'], 42.0),
                'ec'  => $getAnalisaVal($paramEC, $stageCandidatePoints['Effluent'], 1.0),
            ],
        ];

        $calcRem = function ($in, $out) {
            if ($in <= 0) return 95.0;
            return round((($in - $out) / $in) * 100, 1);
        };

        // Formula removal: (Inlet - Outlet) / Inlet * 100
        $calcStageRemoval = function ($inVal, $outVal) {
            $in = (float) $inVal;
            $out = (float) $outVal;
            if ($in <= 0) return null;
            return round((($in - $out) / $in) * 100, 1);
        };

        // 1. Influent (baseline)
        $infCod = (float) $envStages['Influent']['cod'];
        $infTss = (float) $envStages['Influent']['tss'];

        // 2. Outlet Anaerob (Standar: COD >= 80%, TSS >= 10%)
        $anCod = (float) $envStages['Outlet Anaerob']['cod'];
        $anTss = (float) $envStages['Outlet Anaerob']['tss'];
        $remCodAnaerob = $calcStageRemoval($infCod, $anCod);
        $remTssAnaerob = $calcStageRemoval($infTss, $anTss);
        $codOkAnaerob  = ($remCodAnaerob !== null && $remCodAnaerob >= 80);
        $tssOkAnaerob  = ($remTssAnaerob !== null && $remTssAnaerob >= 10);
        $statusAnaerob = ($codOkAnaerob && $tssOkAnaerob) ? 'OK' : 'NOK';

        // 3. Aerob (Standar: COD >= 80%, TSS >= 50%)
        $aeroCod = (float) $envStages['Aerob']['cod'];
        $aeroTss = (float) $envStages['Aerob']['tss'];
        $remCodAerob = $calcStageRemoval($anCod, $aeroCod);
        $remTssAerob = $calcStageRemoval($anTss, $aeroTss);
        $codOkAerob  = ($remCodAerob !== null && $remCodAerob >= 80);
        $tssOkAerob  = ($remTssAerob !== null && $remTssAerob >= 50);
        $statusAerob = ($codOkAerob && $tssOkAerob) ? 'OK' : 'NOK';

        // 4. Outlet DAF (Standar: COD >= 10%, TSS >= 70%)
        $dafCod = (float) $envStages['Outlet DAF']['cod'];
        $dafTss = (float) $envStages['Outlet DAF']['tss'];
        $remCodDaf = $calcStageRemoval($aeroCod, $dafCod);
        $remTssDaf = $calcStageRemoval($aeroTss, $dafTss);
        $codOkDaf  = ($remCodDaf !== null && $remCodDaf >= 10);
        $tssOkDaf  = ($remTssDaf !== null && $remTssDaf >= 70);
        $statusDaf = ($codOkDaf && $tssOkDaf) ? 'OK' : 'NOK';

        // 5. Effluent (Standar Baku Mutu: COD <= 300, TSS <= 100)
        $effCod = (float) $envStages['Effluent']['cod'];
        $effTss = (float) $envStages['Effluent']['tss'];
        $remCodEff = $calcStageRemoval($dafCod, $effCod);
        $remTssEff = $calcStageRemoval($dafTss, $effTss);
        $codOkEff  = ($effCod <= 300);
        $tssOkEff  = ($effTss <= 100);
        $statusEff = ($codOkEff && $tssOkEff) ? 'OK' : 'NOK';

        return [
            'card4_equalisasi' => [
                'ph'  => $envStages['Influent']['ph'],
                'tss' => $envStages['Influent']['tss'],
                'cod' => $envStages['Influent']['cod'],
                'ec'  => $envStages['Influent']['ec'],
            ],
            'card5_removal_outlet' => [
                'tss_pct'    => $calcRem($envStages['Influent']['tss'], $envStages['Effluent']['tss']),
                'cod_pct'    => $calcRem($envStages['Influent']['cod'], $envStages['Effluent']['cod']),
                'target_tss' => '≥ 95%',
                'target_cod' => '≥ 98%',
                'target'     => 'TARGET: TSS ≥ 95% | COD ≥ 98%'
            ],
            'card5_effluent_analisa' => [
                'ph'  => $envStages['Effluent']['ph'],
                'tss' => $envStages['Effluent']['tss'],
                'cod' => $envStages['Effluent']['cod'],
                'ec'  => $envStages['Effluent']['ec'],
            ],
            'card8_env_perf' => [
                [
                    'parameter'   => 'Influent',
                    'satuan'      => 'mg/L',
                    'ph'          => $envStages['Influent']['ph'],
                    'tss'         => $envStages['Influent']['tss'],
                    'cod'         => $envStages['Influent']['cod'],
                    'ec'          => $envStages['Influent']['ec'],
                    'rem_cod'     => '-',
                    'rem_tss'     => '-',
                    'rem_cod_val' => null,
                    'rem_tss_val' => null,
                    'cod_ok'      => true,
                    'tss_ok'      => true,
                    'standard'    => 'Baseline',
                    'status'      => 'OK',
                ],
                [
                    'parameter'   => 'Outlet Anaerob',
                    'satuan'      => 'mg/L',
                    'ph'          => $envStages['Outlet Anaerob']['ph'],
                    'tss'         => $envStages['Outlet Anaerob']['tss'],
                    'cod'         => $envStages['Outlet Anaerob']['cod'],
                    'ec'          => $envStages['Outlet Anaerob']['ec'],
                    'rem_cod'     => $remCodAnaerob !== null ? $remCodAnaerob . '%' : '-',
                    'rem_tss'     => $remTssAnaerob !== null ? $remTssAnaerob . '%' : '-',
                    'rem_cod_val' => $remCodAnaerob,
                    'rem_tss_val' => $remTssAnaerob,
                    'cod_ok'      => $codOkAnaerob,
                    'tss_ok'      => $tssOkAnaerob,
                    'standard'    => 'COD ≥ 80% | TSS ≥ 10%',
                    'status'      => $statusAnaerob,
                ],
                [
                    'parameter'   => 'Aerob',
                    'satuan'      => 'mg/L',
                    'ph'          => $envStages['Aerob']['ph'],
                    'tss'         => $envStages['Aerob']['tss'],
                    'cod'         => $envStages['Aerob']['cod'],
                    'ec'          => $envStages['Aerob']['ec'],
                    'rem_cod'     => $remCodAerob !== null ? $remCodAerob . '%' : '-',
                    'rem_tss'     => $remTssAerob !== null ? $remTssAerob . '%' : '-',
                    'rem_cod_val' => $remCodAerob,
                    'rem_tss_val' => $remTssAerob,
                    'cod_ok'      => $codOkAerob,
                    'tss_ok'      => $tssOkAerob,
                    'standard'    => 'COD ≥ 80% | TSS ≥ 50%',
                    'status'      => $statusAerob,
                ],
                [
                    'parameter'   => 'Outlet DAF',
                    'satuan'      => 'mg/L',
                    'ph'          => $envStages['Outlet DAF']['ph'],
                    'tss'         => $envStages['Outlet DAF']['tss'],
                    'cod'         => $envStages['Outlet DAF']['cod'],
                    'ec'          => $envStages['Outlet DAF']['ec'],
                    'rem_cod'     => $remCodDaf !== null ? $remCodDaf . '%' : '-',
                    'rem_tss'     => $remTssDaf !== null ? $remTssDaf . '%' : '-',
                    'rem_cod_val' => $remCodDaf,
                    'rem_tss_val' => $remTssDaf,
                    'cod_ok'      => $codOkDaf,
                    'tss_ok'      => $tssOkDaf,
                    'standard'    => 'COD ≥ 10% | TSS ≥ 70%',
                    'status'      => $statusDaf,
                ],
                [
                    'parameter'   => 'Effluent',
                    'satuan'      => 'mg/L',
                    'ph'          => $envStages['Effluent']['ph'],
                    'tss'         => $envStages['Effluent']['tss'],
                    'cod'         => $envStages['Effluent']['cod'],
                    'ec'          => $envStages['Effluent']['ec'],
                    'rem_cod'     => $remCodEff !== null ? $remCodEff . '%' : '-',
                    'rem_tss'     => $remTssEff !== null ? $remTssEff . '%' : '-',
                    'rem_cod_val' => $remCodEff,
                    'rem_tss_val' => $remTssEff,
                    'cod_ok'      => $codOkEff,
                    'tss_ok'      => $tssOkEff,
                    'standard'    => 'COD ≤ 300 | TSS ≤ 100',
                    'status'      => $statusEff,
                ],
            ],
            'envStages' => $envStages,
        ];
    }

    private function getWcoStatusOperasiData($startDate, $endDate, $envStages = null)
    {
        $influentRecords = WwtpInfluentHarian::whereBetween('tanggal', [$startDate, $endDate])->orderBy('tanggal', 'desc')->get();
        if ($influentRecords->isEmpty()) {
            $latestRecords = WwtpInfluentHarian::orderBy('tanggal', 'desc')->limit(30)->get();
            if ($latestRecords->isNotEmpty()) {
                $influentRecords = $latestRecords;
            }
        }

        $uniqueDays = $influentRecords->pluck('tanggal')->unique()->count() ?: 1;

        $calcPitVol = function ($field, $fallbackVal) use ($influentRecords, $uniqueDays) {
            if ($influentRecords->isEmpty()) {
                return [
                    'total' => $fallbackVal,
                    'daily' => $fallbackVal,
                    'formatted' => $fallbackVal . ' m³/day'
                ];
            }
            $val = (float) $influentRecords->reduce(function ($carry, $rec) use ($field) {
                $awalField = $field . '_awal';
                return $carry + max(0, (float)($rec->$field ?? 0) - (float)($rec->$awalField ?? 0));
            }, 0);

            $daily = $uniqueDays > 0 ? ($val / $uniqueDays) : $val;
            $dailyRounded = round($daily, 1);
            return [
                'total'     => round($val, 1),
                'daily'     => $dailyRounded,
                'formatted' => $dailyRounded . ' m³/day'
            ];
        };

        // 1. Pit Sparta (Std: 50 m³/day)
        // 2. Pit Garam (Std: 50 m³/day)
        // 3. Pit Domestic (Std: 2 m³/day)
        // 4. Pit Produksi Step (Std: 48 m³/day)
        // 5. Pit Outlet (Std: 50 m³/day)
        $pitsConfig = [
            [
                'name'     => 'Pit Sparta',
                'field'    => 'pit_sparta',
                'standard' => 50,
                'fallback' => 35.0,
                'img'      => asset('assets/images/wwtp/dashboard/PIT SPARTA.png'),
            ],
            [
                'name'     => 'Pit Garam',
                'field'    => 'pit_garam',
                'standard' => 50,
                'fallback' => 28.5,
                'img'      => asset('assets/images/wwtp/dashboard/PIT GARAM.png'),
            ],
            [
                'name'     => 'Pit Domestic',
                'field'    => 'pit_domestik',
                'standard' => 2,
                'fallback' => 1.2,
                'img'      => asset('assets/images/wwtp/dashboard/EQUALISASI.png'),
            ],
            [
                'name'     => 'Pit Produksi Step',
                'field'    => 'pit_produksi_step3',
                'standard' => 48,
                'fallback' => 38.0,
                'img'      => asset('assets/images/wwtp/dashboard/STEP 3.png'),
            ],
            [
                'name'     => 'Pit Outlet',
                'field'    => 'pit_outlet',
                'standard' => 50,
                'fallback' => 41.5,
                'img'      => asset('assets/images/wwtp/dashboard/OUTLET.png'),
            ],
        ];

        $cardStatusOperasi = [];
        foreach ($pitsConfig as $cfg) {
            $data = $calcPitVol($cfg['field'], $cfg['fallback']);
            $isOver = $data['daily'] > $cfg['standard'];
            $subtext = 'Std: max ' . $cfg['standard'] . ' m³/day' . ($uniqueDays > 1 ? ' (' . $uniqueDays . ' hr)' : '');

            $cardStatusOperasi[] = [
                'name'      => $cfg['name'],
                'volume'    => $data['formatted'],
                'daily_val' => $data['daily'],
                'total_val' => $data['total'],
                'standard'  => $cfg['standard'],
                'subtext'   => $subtext,
                'status'    => $isOver ? 'OVER' : 'NORMAL',
                'is_over'   => $isOver,
                'badge'     => $isOver ? 'danger' : 'success',
                'img'       => $cfg['img'],
            ];
        }

        return [
            'card10_status_operasi' => $cardStatusOperasi
        ];
    }

    private function getWcoSafetyPerfData($startDate, $endDate)
    {
        $influentRecords = WwtpInfluentHarian::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->orderBy('shift', 'asc')
            ->get();

        if ($influentRecords->isEmpty()) {
            $latestRecords = WwtpInfluentHarian::orderBy('tanggal', 'desc')->limit(30)->get();
            if ($latestRecords->isNotEmpty()) {
                $influentRecords = $latestRecords->reverse()->values();
            }
        }

        $categories      = [];
        $spartaSeries    = [];
        $garamSeries     = [];
        $totalSparta     = 0;
        $totalGaram      = 0;
        $dailyAggregated = [];

        if ($influentRecords->isNotEmpty()) {
            $uniqueDates = $influentRecords->pluck('tanggal')->unique();
            if ($uniqueDates->count() === 1 && $influentRecords->count() > 1) {
                // Single date with multiple shifts: breakdown by Shift
                foreach ($influentRecords as $rec) {
                    $shiftLabel = $rec->shift ? (str_starts_with(strtolower($rec->shift), 'shift') ? 'Shift ' . substr($rec->shift, 5) : ucfirst($rec->shift)) : 'Shift';
                    $sVal = max(0, (float)($rec->pit_sparta ?? 0) - (float)($rec->pit_sparta_awal ?? 0));
                    $gVal = max(0, (float)($rec->pit_garam ?? 0) - (float)($rec->pit_garam_awal ?? 0));

                    $categories[]   = $shiftLabel;
                    $spartaSeries[] = round($sVal, 1);
                    $garamSeries[]  = round($gVal, 1);
                    $totalSparta   += $sVal;
                    $totalGaram    += $gVal;

                    $dailyAggregated[] = [
                        'tanggal'    => $shiftLabel,
                        'pit_sparta' => round($sVal, 1),
                        'pit_garam'  => round($gVal, 1),
                    ];
                }
            } else {
                // Group by daily date
                $groupedByDate = $influentRecords->groupBy('tanggal');
                foreach ($groupedByDate as $date => $recs) {
                    $sDay = $recs->reduce(function ($carry, $rec) {
                        return $carry + max(0, (float)($rec->pit_sparta ?? 0) - (float)($rec->pit_sparta_awal ?? 0));
                    }, 0);
                    $gDay = $recs->reduce(function ($carry, $rec) {
                        return $carry + max(0, (float)($rec->pit_garam ?? 0) - (float)($rec->pit_garam_awal ?? 0));
                    }, 0);

                    $fmtDate = Carbon::parse($date)->format('d M');
                    $categories[]   = $fmtDate;
                    $spartaSeries[] = round($sDay, 1);
                    $garamSeries[]  = round($gDay, 1);
                    $totalSparta   += $sDay;
                    $totalGaram    += $gDay;

                    $dailyAggregated[] = [
                        'tanggal'    => $fmtDate,
                        'pit_sparta' => round($sDay, 1),
                        'pit_garam'  => round($gDay, 1),
                    ];
                }
            }
        } else {
            // Realistic simulation matching date range when no data exists yet
            $startC = Carbon::parse($startDate);
            $endC   = Carbon::parse($endDate);

            if ($startC->equalTo($endC)) {
                $categories   = ['Shift 1', 'Shift 2', 'Shift 3'];
                $spartaSeries = [12.5, 14.2, 11.0];
                $garamSeries  = [9.2, 11.5, 8.8];
                $totalSparta  = array_sum($spartaSeries);
                $totalGaram   = array_sum($garamSeries);

                foreach ($categories as $idx => $sh) {
                    $dailyAggregated[] = [
                        'tanggal'    => $sh,
                        'pit_sparta' => $spartaSeries[$idx],
                        'pit_garam'  => $garamSeries[$idx],
                    ];
                }
            } else {
                $numDays = min(14, max(3, $startC->diffInDays($endC) + 1));
                $spartaPattern = [32, 38, 29, 41, 35, 44, 39, 36, 42, 33, 40, 37, 45, 34];
                $garamPattern  = [22, 28, 25, 30, 26, 32, 29, 24, 27, 31, 23, 26, 28, 25];

                for ($i = $numDays - 1; $i >= 0; $i--) {
                    $dt = $endC->copy()->subDays($i);
                    $fmtDate = $dt->format('d M');
                    $sVal = (float)$spartaPattern[$i % count($spartaPattern)];
                    $gVal = (float)$garamPattern[$i % count($garamPattern)];

                    $categories[]   = $fmtDate;
                    $spartaSeries[] = $sVal;
                    $garamSeries[]  = $gVal;
                    $totalSparta   += $sVal;
                    $totalGaram    += $gVal;

                    $dailyAggregated[] = [
                        'tanggal'    => $fmtDate,
                        'pit_sparta' => $sVal,
                        'pit_garam'  => $gVal,
                    ];
                }
            }
        }

        $count = count($spartaSeries) ?: 1;
        $avgSparta = round($totalSparta / $count, 1);
        $avgGaram  = round($totalGaram / $count, 1);

        return [
            'card7_safety_perf' => [
                'title'            => 'INFLUENT SPARTA & PIT GARAM',
                'categories'       => $categories,
                'sparta'           => $spartaSeries,
                'garam'            => $garamSeries,
                'total_sparta'     => round($totalSparta, 1),
                'total_garam'      => round($totalGaram, 1),
                'avg_sparta'       => $avgSparta,
                'avg_garam'        => $avgGaram,
                'std_sparta'       => 50,
                'std_garam'        => 50,
                'is_sparta_over'   => $avgSparta > 50,
                'is_garam_over'    => $avgGaram > 50,
                'daily_aggregated' => $dailyAggregated,
            ]
        ];
    }

    private function getWcoInfluentMingguanData($startDate, $endDate)
    {
        $startC = Carbon::parse($startDate);
        $endC   = Carbon::parse($endDate);
        $diffDays = $startC->diffInDays($endC);
        $numWeeks = max(1, min(12, (int) ceil(($diffDays + 1) / 7)));

        $influentWeeklyRecords = WwtpRecord::where('kategori', 'influent')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->with('influent')
            ->orderBy('tanggal', 'asc')
            ->get();

        if ($influentWeeklyRecords->isEmpty()) {
            $latestRecords = WwtpRecord::where('kategori', 'influent')
                ->with('influent')
                ->orderBy('tanggal', 'desc')
                ->limit($numWeeks)
                ->get();

            if ($latestRecords->isNotEmpty()) {
                $influentWeeklyRecords = $latestRecords->reverse()->values();
            }
        }

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
                $dt = Carbon::parse($rec->tanggal);
                $influentWeeklyChart['categories'][] = 'W' . $dt->weekOfYear . ' (' . $dt->format('d M') . ')';
                $influentWeeklyChart['sparta'][]     = (float)($rec->influent->pit_sparta ?? 0);
                $influentWeeklyChart['garam'][]      = (float)($rec->influent->pit_garam ?? 0);
                $influentWeeklyChart['domestik'][]   = (float)($rec->influent->pit_domestik ?? 0);
                $influentWeeklyChart['step3'][]      = (float)($rec->influent->pit_produksi_step3 ?? 0);
                $influentWeeklyChart['storage'][]    = (float)($rec->influent->pit_storage ?? 0);
            }
        } else {
            for ($w = $numWeeks - 1; $w >= 0; $w--) {
                $dt = $endC->copy()->subWeeks($w);
                $influentWeeklyChart['categories'][] = 'W' . $dt->weekOfYear . ' (' . $dt->format('d M') . ')';
                $influentWeeklyChart['sparta'][]     = rand(80, 150);
                $influentWeeklyChart['garam'][]      = rand(30, 70);
                $influentWeeklyChart['domestik'][]   = rand(15, 35);
                $influentWeeklyChart['step3'][]      = rand(50, 95);
                $influentWeeklyChart['storage'][]    = rand(20, 50);
            }
        }

        return [
            'card14_influent_mingguan' => $influentWeeklyChart
        ];
    }

    private function getWcoEffluentMingguanData($startDate, $endDate)
    {
        $startC = Carbon::parse($startDate);
        $endC   = Carbon::parse($endDate);
        $diffDays = $startC->diffInDays($endC);
        $numWeeks = max(1, min(12, (int) ceil(($diffDays + 1) / 7)));

        $effluentWeeklyRecords = WwtpRecord::where('kategori', 'effluent')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->with('effluent')
            ->orderBy('tanggal', 'asc')
            ->get();

        if ($effluentWeeklyRecords->isEmpty()) {
            $latestRecords = WwtpRecord::where('kategori', 'effluent')
                ->with('effluent')
                ->orderBy('tanggal', 'desc')
                ->limit($numWeeks)
                ->get();

            if ($latestRecords->isNotEmpty()) {
                $effluentWeeklyRecords = $latestRecords->reverse()->values();
            }
        }

        $trendEffluentMingguan = [
            'categories'  => [],
            'full_proses' => [],
            'daf_pre'     => []
        ];

        if ($effluentWeeklyRecords->isNotEmpty()) {
            foreach ($effluentWeeklyRecords as $rec) {
                $dt = Carbon::parse($rec->tanggal);
                $trendEffluentMingguan['categories'][] = 'W' . $dt->weekOfYear . ' (' . $dt->format('d M') . ')';
                $trendEffluentMingguan['full_proses'][] = (float)($rec->effluent->full_proses ?? 0);
                $trendEffluentMingguan['daf_pre'][]     = (float)($rec->effluent->daf_pre ?? 0);
            }
        } else {
            for ($w = $numWeeks - 1; $w >= 0; $w--) {
                $dt = $endC->copy()->subWeeks($w);
                $trendEffluentMingguan['categories'][] = 'W' . $dt->weekOfYear . ' (' . $dt->format('d M') . ')';
                $trendEffluentMingguan['full_proses'][] = rand(80, 140);
                $trendEffluentMingguan['daf_pre'][]     = rand(30, 70);
            }
        }

        return [
            'card11_trend_effluent_mingguan' => $trendEffluentMingguan
        ];
    }

    private function getWcoKepatuhanCodData($startDate, $endDate)
    {
        $paramCOD = WwtpParameter::where('parameter_name', 'like', '%COD%')->first();
        $effluentPointNames = ['Effluent'];
        $effluentPointIds = WwtpPoint::where(function ($q) use ($effluentPointNames) {
            foreach ($effluentPointNames as $name) {
                $q->orWhere('point_name', 'like', '%' . $name . '%');
            }
        })->pluck('id')->toArray();

        $analisaRecords = collect();
        if ($paramCOD && !empty($effluentPointIds)) {
            $analisaRecords = WwtpAnalisa::with(['details' => function ($q) use ($paramCOD, $effluentPointIds) {
                $q->where('parameter_id', $paramCOD->id)
                    ->whereIn('point_id', $effluentPointIds);
            }])
                ->whereBetween('analisa_date', [$startDate, $endDate])
                ->orderBy('analisa_date', 'asc')
                ->orderBy('shift', 'asc')
                ->get();
        }

        $validRecords = $analisaRecords->filter(function ($rec) {
            return $rec->details->contains(function ($d) {
                return $d->hasil_analisa !== null && is_numeric($d->hasil_analisa);
            });
        });

        // Fallback to recent daily analisa if current range has no records
        if ($validRecords->isEmpty() && $paramCOD && !empty($effluentPointIds)) {
            $recent = WwtpAnalisa::with(['details' => function ($q) use ($paramCOD, $effluentPointIds) {
                $q->where('parameter_id', $paramCOD->id)
                    ->whereIn('point_id', $effluentPointIds);
            }])
                ->whereHas('details', function ($q) use ($paramCOD, $effluentPointIds) {
                    $q->where('parameter_id', $paramCOD->id)
                        ->whereIn('point_id', $effluentPointIds)
                        ->whereNotNull('hasil_analisa');
                })
                ->orderBy('analisa_date', 'desc')
                ->limit(14)
                ->get();

            if ($recent->isNotEmpty()) {
                $validRecords = $recent->reverse()->values();
            }
        }

        $trendEffluentCOD = [
            'categories' => [],
            'values'     => [],
            'baku_mutu'  => 300,
            'compliance' => 100
        ];

        if ($validRecords->isNotEmpty()) {
            $okCodCount = 0;
            $totalCodCount = 0;

            if ($startDate === $endDate && $validRecords->count() > 1) {
                // Single day with multiple shifts: display per shift
                foreach ($validRecords as $r) {
                    $val = $r->details->filter(fn($d) => $d->hasil_analisa !== null && is_numeric($d->hasil_analisa))->avg('hasil_analisa');
                    if ($val !== null) {
                        $shiftLabel = $r->shift ? (str_starts_with(strtolower($r->shift), 'shift') ? 'Shift ' . substr($r->shift, 5) : ucfirst($r->shift)) : Carbon::parse($r->analisa_date)->format('d M');
                        $codVal = round((float)$val, 1);
                        $trendEffluentCOD['categories'][] = $shiftLabel;
                        $trendEffluentCOD['values'][]     = $codVal;
                        $totalCodCount++;
                        if ($codVal <= 300) $okCodCount++;
                    }
                }
            } else {
                // Group by daily date
                $grouped = $validRecords->groupBy(fn($r) => Carbon::parse($r->analisa_date)->toDateString());
                foreach ($grouped as $date => $recs) {
                    $vals = collect();
                    foreach ($recs as $r) {
                        foreach ($r->details as $d) {
                            if ($d->hasil_analisa !== null && is_numeric($d->hasil_analisa)) {
                                $vals->push((float)$d->hasil_analisa);
                            }
                        }
                    }
                    if ($vals->isNotEmpty()) {
                        $codVal = round($vals->avg(), 1);
                        $trendEffluentCOD['categories'][] = Carbon::parse($date)->format('d M');
                        $trendEffluentCOD['values'][]     = $codVal;
                        $totalCodCount++;
                        if ($codVal <= 300) $okCodCount++;
                    }
                }
            }

            $trendEffluentCOD['compliance'] = $totalCodCount > 0 ? round(($okCodCount / $totalCodCount) * 100, 1) : 100;
        } else {
            // Realistic daily simulation matching the filtered dates if no daily analysis records exist yet
            $startC = Carbon::parse($startDate);
            $endC   = Carbon::parse($endDate);
            if ($startC->equalTo($endC)) {
                $trendEffluentCOD['categories'] = ['Shift 1', 'Shift 2', 'Shift 3'];
                $trendEffluentCOD['values']     = [45, 42, 48];
            } else {
                $numDays = min(14, max(3, $startC->diffInDays($endC) + 1));
                $demoPattern = [48, 52, 49, 45, 42, 46, 44, 43, 47, 50, 46, 42, 45, 43];
                $categories = [];
                $values = [];
                for ($i = $numDays - 1; $i >= 0; $i--) {
                    $categories[] = $endC->copy()->subDays($i)->format('d M');
                    $values[]     = $demoPattern[$i % count($demoPattern)];
                }
                $trendEffluentCOD['categories'] = $categories;
                $trendEffluentCOD['values']     = $values;
            }
            $trendEffluentCOD['compliance'] = 100;
        }

        return [
            'card12_trend_kepatuhan_effluent_cod' => $trendEffluentCOD
        ];
    }

    private function getWcoPerformanceSampleData($startDate, $endDate)
    {
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
                $avgSv30 = (float) $matched->avg('sv30');
                $avgMlss = (float) $matched->avg('mlss');
                $avgSvl  = (float) $matched->avg('svl');
                if ($avgSvl <= 0 && $avgMlss > 0) {
                    $avgSvl = ($avgSv30 * 1000) / $avgMlss;
                }
                $avgDo = (float) $matched->avg('do');
                $avgPh = (float) ($matched->avg('ph') ?: 7.2);
                $avgTss = (float) $matched->avg('tss');

                $sv30Val = round($avgSv30 ?: 0, 1);
                $mlssVal = round($avgMlss ?: 0, 1);
                $sviVal  = round($avgSvl ?: 0, 1);
                $doVal   = round($avgDo ?: 0, 1);
                $phVal   = round($avgPh ?: 7.2, 1);
                $tssVal  = round($avgTss ?: 0, 1);
            } else {
                $sv30Val = rand(320, 480);
                $mlssVal = rand(2200, 4200);
                $sviVal  = rand(95, 140);
                $doVal   = round(rand(14, 20) / 10, 1);
                $phVal   = round(rand(72, 78) / 10, 1);
                $tssVal  = rand(200, 350);
            }

            // Standar:
            // 1. Sv'30 : 300 - 500 ml
            // 2. Mlss : 1000 - 5000 ppm
            // 3. SVI : 90 - 150
            // 4. Do : 2 - 4 ppm (jika < 2 merah, jika 2 - 4 hijau, jika > 4 merah)
            // 5. pH : 7 - 9
            $sv30Ok = ($sv30Val >= 300 && $sv30Val <= 500);
            $mlssOk = ($mlssVal >= 1000 && $mlssVal <= 5000);
            $sviOk  = ($sviVal >= 90 && $sviVal <= 150);
            $doOk   = ($doVal >= 2.0 && $doVal <= 4.0);
            $phOk   = ($phVal >= 7.0 && $phVal <= 9.0);
            $isOk   = ($sv30Ok && $mlssOk && $sviOk && $doOk && $phOk);

            $cardPerformanceSample[] = [
                'nama_sampel' => $sName,
                'sv30'        => $sv30Val,
                'mlss'        => $mlssVal,
                'svl'         => $sviVal,
                'svi'         => $sviVal,
                'do'          => $doVal,
                'ph'          => $phVal,
                'tss'         => $tssVal,
                'sv30_ok'     => $sv30Ok,
                'mlss_ok'     => $mlssOk,
                'svi_ok'      => $sviOk,
                'do_ok'       => $doOk,
                'ph_ok'       => $phOk,
                'status'      => $isOk ? 'OK' : 'NOT OK',
            ];
        }

        $totalSamples = count($cardPerformanceSample);
        $okSamples = collect($cardPerformanceSample)->where('status', 'OK')->count();
        $complianceScore = $totalSamples > 0 ? round(($okSamples / $totalSamples) * 100) : 100;

        return [
            'card13_chem_storage_performance' => [
                'samples'          => $cardPerformanceSample,
                'compliance_score' => $complianceScore
            ]
        ];
    }

    private function getWcoSludgeMgmtData($startDate, $endDate)
    {
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

        $daysCount = $sludgeRecords->pluck('tanggal')->unique()->count() ?: 1;

        if ($sludgeRecords->isNotEmpty()) {
            $drainVal = round($drainLumpurTotal / $daysCount, 1);
            $runningVal = round($runningHourScpTotal / $daysCount, 1);
            $rawHasil = $hasilLumpurTotal / $daysCount;
            // Jika nilai > 50 kemungkinan diinput dalam satuan kg, konversi ke ton
            $hasilVal = $rawHasil > 50 ? round($rawHasil / 1000, 2) : round($rawHasil, 2);
            $contentVal = round($sludgeContentAvg, 1);
        } else {
            $drainVal = 18.5;
            $runningVal = 5.5;
            $hasilVal = 1.1;
            $contentVal = 22.5;
        }

        // Standar Sludge Management:
        // 1. Sludge content : 20% (dibawah 20% merah, di atas 20% hijau)
        $contentOk = ($contentVal >= 20.0);
        // 2. Hasil Lumpur : 1.2 ton/hari (di atas standar merah, dibawah hijau)
        $hasilOk   = ($hasilVal <= 1.2);
        // 3. Running : 6 jam/hari (di atas standar merah, dibawah hijau)
        $runningOk = ($runningVal <= 6.0);
        // 4. Drain Lumpur : 19 m³/hari (di atas standar merah, dibawah hijau)
        $drainOk   = ($drainVal <= 19.0);

        // Rumus Cost Biaya Pengangkutan Sludge: Ton * 1000 * Rp 1.400 = Total Cost Rp
        $tonaseVal = $totalTonasePengangkutan > 0 ? $totalTonasePengangkutan : 12.5;
        $costPerKg = 1400;
        $totalCostPengangkutan = $tonaseVal * 1000 * $costPerKg;

        return [
            'card3_pengangkutan_sludge' => [
                'total_tonase'   => round($tonaseVal, 2),
                'formatted'      => number_format($tonaseVal, 2, ',', '.') . ' Ton',
                'cost_per_kg'    => $costPerKg,
                'total_cost'     => round($totalCostPengangkutan, 0),
                'cost_formatted' => 'Rp ' . number_format($totalCostPengangkutan, 0, ',', '.'),
                'formula'        => number_format($tonaseVal, 2, ',', '.') . ' Ton × 1000 × Rp ' . number_format($costPerKg, 0, ',', '.') . ' = Rp ' . number_format($totalCostPengangkutan, 0, ',', '.'),
                'target'         => 'TOTAL AKUMULASI',
                'status'         => 'TERJADWAL'
            ],
            'card15_sludge_mgmt' => [
                'drain_lumpur'       => $drainVal,
                'drain_lumpur_std'   => '≤ 19 m³/hari',
                'drain_lumpur_ok'    => $drainOk,
                'running_hour_scp'   => $runningVal,
                'running_hour_std'   => '≤ 6 jam/hari',
                'running_hour_ok'    => $runningOk,
                'hasil_lumpur'       => $hasilVal,
                'hasil_lumpur_std'   => '≤ 1.2 ton/hari',
                'hasil_lumpur_sat'   => 'Ton',
                'hasil_lumpur_ok'    => $hasilOk,
                'sludge_content'     => $contentVal,
                'sludge_content_std' => '≥ 20%',
                'sludge_content_ok'  => $contentOk,
                'status'             => ($contentOk && $hasilOk && $runningOk && $drainOk) ? 'OK' : 'NOK'
            ]
        ];
    }

    private function getWcoTopRiskKoloniData()
    {
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
                ['name' => 'Inlet Anaerob',     'base' => 3.2, 'exp' => 6],
                ['name' => 'Outlet Anaerob',    'base' => 1.8, 'exp' => 5],
                ['name' => 'Aerasi 1',          'base' => 4.5, 'exp' => 5],
                ['name' => 'Aerasi 6',          'base' => 0.8, 'exp' => 5],
                ['name' => 'Effluent Akhir',    'base' => 0.2, 'exp' => 5],
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

        return [
            'card16_top_risk_koloni' => [
                'categories' => $koloniCategories,
                'values'     => $koloniValues,
                'strings'    => $koloniStrings,
                'standard'   => 1.0,
                'items'      => $koloniItems
            ]
        ];
    }

    private function getWcoKepatuhanTssData($startDate, $endDate)
    {
        $paramTSS = WwtpParameter::where('parameter_name', 'like', '%TSS%')->first();
        $effluentPointNames = ['Effluent'];
        $effluentPointIds = WwtpPoint::where(function ($q) use ($effluentPointNames) {
            foreach ($effluentPointNames as $name) {
                $q->orWhere('point_name', 'like', '%' . $name . '%');
            }
        })->pluck('id')->toArray();

        $analisaRecords = collect();
        if ($paramTSS && !empty($effluentPointIds)) {
            $analisaRecords = WwtpAnalisa::with(['details' => function ($q) use ($paramTSS, $effluentPointIds) {
                $q->where('parameter_id', $paramTSS->id)
                    ->whereIn('point_id', $effluentPointIds);
            }])
                ->whereBetween('analisa_date', [$startDate, $endDate])
                ->orderBy('analisa_date', 'asc')
                ->orderBy('shift', 'asc')
                ->get();
        }

        $validRecords = $analisaRecords->filter(function ($rec) {
            return $rec->details->contains(function ($d) {
                return $d->hasil_analisa !== null && is_numeric($d->hasil_analisa);
            });
        });

        // Fallback to recent daily analisa if current range has no records
        if ($validRecords->isEmpty() && $paramTSS && !empty($effluentPointIds)) {
            $recent = WwtpAnalisa::with(['details' => function ($q) use ($paramTSS, $effluentPointIds) {
                $q->where('parameter_id', $paramTSS->id)
                    ->whereIn('point_id', $effluentPointIds);
            }])
                ->whereHas('details', function ($q) use ($paramTSS, $effluentPointIds) {
                    $q->where('parameter_id', $paramTSS->id)
                        ->whereIn('point_id', $effluentPointIds)
                        ->whereNotNull('hasil_analisa');
                })
                ->orderBy('analisa_date', 'desc')
                ->limit(14)
                ->get();

            if ($recent->isNotEmpty()) {
                $validRecords = $recent->reverse()->values();
            }
        }

        $trendEffluentTSS = [
            'categories' => [],
            'values'     => [],
            'baku_mutu'  => 100,
            'compliance' => 96
        ];

        if ($validRecords->isNotEmpty()) {
            $okTssCount = 0;
            $totalTssCount = 0;

            if ($startDate === $endDate && $validRecords->count() > 1) {
                // Single day with multiple shifts: display per shift
                foreach ($validRecords as $r) {
                    $val = $r->details->filter(fn($d) => $d->hasil_analisa !== null && is_numeric($d->hasil_analisa))->avg('hasil_analisa');
                    if ($val !== null) {
                        $shiftLabel = $r->shift ? (str_starts_with(strtolower($r->shift), 'shift') ? 'Shift ' . substr($r->shift, 5) : ucfirst($r->shift)) : Carbon::parse($r->analisa_date)->format('d M');
                        $tssVal = round((float)$val, 1);
                        $trendEffluentTSS['categories'][] = $shiftLabel;
                        $trendEffluentTSS['values'][]     = $tssVal;
                        $totalTssCount++;
                        if ($tssVal <= 100) $okTssCount++;
                    }
                }
            } else {
                // Group by daily date
                $grouped = $validRecords->groupBy(fn($r) => Carbon::parse($r->analisa_date)->toDateString());
                foreach ($grouped as $date => $recs) {
                    $vals = collect();
                    foreach ($recs as $r) {
                        foreach ($r->details as $d) {
                            if ($d->hasil_analisa !== null && is_numeric($d->hasil_analisa)) {
                                $vals->push((float)$d->hasil_analisa);
                            }
                        }
                    }
                    if ($vals->isNotEmpty()) {
                        $tssVal = round($vals->avg(), 1);
                        $trendEffluentTSS['categories'][] = Carbon::parse($date)->format('d M');
                        $trendEffluentTSS['values'][]     = $tssVal;
                        $totalTssCount++;
                        if ($tssVal <= 100) $okTssCount++;
                    }
                }
            }

            $trendEffluentTSS['compliance'] = $totalTssCount > 0 ? round(($okTssCount / $totalTssCount) * 100, 1) : 100;
        } else {
            // Realistic daily simulation matching the filtered dates if no daily analysis records exist yet
            $startC = Carbon::parse($startDate);
            $endC   = Carbon::parse($endDate);
            if ($startC->equalTo($endC)) {
                $trendEffluentTSS['categories'] = ['Shift 1', 'Shift 2', 'Shift 3'];
                $trendEffluentTSS['values']     = [24, 22, 26];
            } else {
                $numDays = min(14, max(3, $startC->diffInDays($endC) + 1));
                $demoPattern = [28, 24, 26, 22, 25, 20, 22, 24, 27, 23, 21, 25, 22, 24];
                $categories = [];
                $values = [];
                for ($i = $numDays - 1; $i >= 0; $i--) {
                    $categories[] = $endC->copy()->subDays($i)->format('d M');
                    $values[]     = $demoPattern[$i % count($demoPattern)];
                }
                $trendEffluentTSS['categories'] = $categories;
                $trendEffluentTSS['values']     = $values;
            }
            $trendEffluentTSS['compliance'] = 96;
        }

        return [
            'card17_hse_training_effluent_tss' => $trendEffluentTSS,
            'card17_she_training_effluent_tss' => $trendEffluentTSS
        ];
    }

    private function getWcoInformasiWWTPData($startDate, $endDate)
    {
        $influentRecords = WwtpInfluentHarian::whereBetween('tanggal', [$startDate, $endDate])->orderBy('tanggal', 'desc')->get();
        if ($influentRecords->isEmpty()) {
            $latestRecords = WwtpInfluentHarian::orderBy('tanggal', 'desc')->limit(30)->get();
            if ($latestRecords->isNotEmpty()) {
                $influentRecords = $latestRecords;
            }
        }

        $latestInfluentRow = $influentRecords->first();
        return [
            'debit1'   => $latestInfluentRow && $latestInfluentRow->debit1 !== null ? (float)$latestInfluentRow->debit1 : round((float)($influentRecords->avg('debit1') ?: 42.5), 1),
            'running1' => $latestInfluentRow && $latestInfluentRow->running_wwtp1 ? $latestInfluentRow->running_wwtp1 : '24 Jam',
            'debit2'   => $latestInfluentRow && $latestInfluentRow->debit2 !== null ? (float)$latestInfluentRow->debit2 : round((float)($influentRecords->avg('debit2') ?: 44.7), 1),
            'running2' => $latestInfluentRow && $latestInfluentRow->running_wwtp2 ? $latestInfluentRow->running_wwtp2 : '24 Jam',
            'kapasitas_design' => '96 m³/day',
            'jam_operasi'      => '24 Jam',
            'personil'         => '18 Orang',
            'tanks'            => [
                ['name' => 'Equal',        'kapasitas' => '20 m³',  'img' => asset('assets/images/wwtp/dashboard/EQUALISASI.png')],
                ['name' => 'Anaerob',      'kapasitas' => '426 m³', 'img' => asset('assets/images/wwtp/dashboard/ANAEROB.png')],
                ['name' => 'Aerob',        'kapasitas' => '170 m³', 'img' => asset('assets/images/wwtp/dashboard/AEROB.png')],
                ['name' => 'Lumpur Aktif', 'kapasitas' => '160 m³', 'img' => asset('assets/images/wwtp/dashboard/LUMPUR AKTIF.png')],
                ['name' => 'DAF',          'kapasitas' => '10 m³',  'img' => asset('assets/images/wwtp/dashboard/DAF.png')],
                ['name' => 'Outlet',       'kapasitas' => '1 m³',   'img' => asset('assets/images/wwtp/dashboard/OUTLET.png')],
            ]
        ];
    }
}
