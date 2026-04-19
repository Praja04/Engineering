<?php

namespace App\Http\Controllers\Ejo;

use App\Http\Controllers\Controller;
use App\Models\Ejo\EjoClassification;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EjoTemplateController extends Controller
{
    /**
     * Download template Excel dengan dropdown Klasifikasi dari DB.
     * GET /api/ejo/template
     */
    public function download()
    {
        $spreadsheet = new Spreadsheet();

        // ── Ambil klasifikasi dari DB dulu ────────────────────────────────
        $classifications = EjoClassification::orderBy('name')
            ->pluck('name')
            ->toArray();

        $classCount = count($classifications);

        // ── Sheet 2 (dibuat duluan): Hidden list source ───────────────────
        // Harus dibuat SEBELUM sheet utama agar index sheet tidak kacau
        $wsList = $spreadsheet->createSheet(1);
        $wsList->setTitle('_list');
        $wsList->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        foreach ($classifications as $i => $name) {
            $wsList->setCellValue('A' . ($i + 1), $name);
        }

        // ── Sheet 1: Template utama ───────────────────────────────────────
        $ws = $spreadsheet->getSheet(0);
        $ws->setTitle('EJO Import');

        $headers = [
            'No', 'OS/IN', 'Dept.', 'Tim', 'Ticket ID', 'Date',
            'Category', 'Module', 'Subject', 'Description', 'Requestor',
            'Status', 'Type', 'Schedule', 'Est.Time', 'Date Done',
            'PIC ACTION', 'Note', 'Klasifikasi',
        ];

        $ws->fromArray($headers, null, 'A1');

        // Style header
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $ws->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1E3A5F']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'B0BEC5']]],
        ]);
        $ws->getRowDimension(1)->setRowHeight(22);

        // Lebar kolom
        $colWidths = [5, 8, 8, 10, 16, 24, 20, 20, 40, 40, 18, 12, 8, 14, 10, 14, 14, 30, 20];
        foreach ($colWidths as $i => $width) {
            $ws->getColumnDimensionByColumn($i + 1)->setWidth($width);
        }

        $ws->freezePane('A2');

        // ── Dropdown validation kolom Klasifikasi (kolom S = ke-19) ───────
        if ($classCount > 0) {
            $klasifikasiCol = Coordinate::stringFromColumnIndex(19); // S
            $lastRow        = $classCount; // data di _list mulai baris 1

            // Formula referensi ke hidden sheet — cara paling reliable
            // WAJIB pakai tanda kutip tunggal di nama sheet yang diawali underscore
            $formula = "'_list'!\$A\$1:\$A\${$lastRow}";

            // Terapkan validation ke S2:S1000 sekaligus
            $dv = $ws->getDataValidation("{$klasifikasiCol}2:{$klasifikasiCol}1000");
            $dv->setType(DataValidation::TYPE_LIST);
            $dv->setErrorStyle(DataValidation::STYLE_STOP);
            $dv->setAllowBlank(true);
            $dv->setShowDropDown(false);        // false = TAMPIL panah dropdown
            $dv->setShowErrorMessage(true);
            $dv->setErrorTitle('Klasifikasi Tidak Valid');
            $dv->setError('Pilih dari daftar dropdown yang tersedia.');
            $dv->setFormula1($formula);
            $dv->setSqref("{$klasifikasiCol}2:{$klasifikasiCol}1000");
        }

        // Aktifkan sheet utama
        $spreadsheet->setActiveSheetIndex(0);

        // ── Stream download ───────────────────────────────────────────────
        $filename = 'EJO_Template_' . now()->format('Ymd') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
