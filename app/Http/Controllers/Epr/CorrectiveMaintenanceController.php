<?php

namespace App\Http\Controllers\Epr;

use App\Http\Controllers\Controller;
use App\Models\Epr\CorrectiveMaintenance;
use App\Models\Epr\JenisDt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CorrectiveMaintenanceController extends Controller
{
    public function form()
    {
        $jenisDts = JenisDt::where('aktif', true)->orderBy('name', 'asc')->get();
        return view('epr.corrective-maintenance.form', compact('jenisDts'));
    }

    public function data()
    {
        $jenisDts = JenisDt::orderBy('name', 'asc')->get();
        return view('epr.corrective-maintenance.data', compact('jenisDts'));
    }

    public function getReports(Request $request)
    {
        $query = CorrectiveMaintenance::with(['jenisDt', 'creator'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'desc');

        if (Auth::user()->jabatan === 'operator') {
            $query->where('created_by', Auth::id());
        }

        if ($request->filled('month')) {
            $query->where('tanggal', 'like', $request->input('month') . '%');
        }

        $reports = $query->limit(1500)->get();

        $mapped = $reports->map(function ($r) {
            return [
                'id' => (string) $r->id,
                'tanggal' => $r->tanggal,
                'shift' => $r->shift,
                'grup' => $r->grup,
                'mesin' => $r->mesin,
                'pouch_sachet' => $r->pouch_sachet,
                'jam_mulai' => substr($r->jam_mulai, 0, 5),
                'jam_selesai' => substr($r->jam_selesai, 0, 5),
                'total_menit' => (int) $r->total_menit,
                'keterangan' => $r->keterangan,
                'downtime' => $r->downtime,
                'jenis_dt_id' => $r->jenis_dt_id ? (string) $r->jenis_dt_id : null,
                'jenis_dt_name' => $r->jenisDt->name ?? '—',
                'am_pm' => $r->am_pm,
                'electrical_mechanical' => $r->electrical_mechanical,
                'created_by' => $r->creator->username ?? '—',
                'createdAt' => $r->created_at->toIso8601String()
            ];
        });

        return response()->json($mapped);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required|string',
            'grup' => 'required|string',
            'mesin' => 'required|string',
            'pouch_sachet' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'total_menit' => 'required|integer|min:0',
            'am_pm' => 'required|string',
            'electrical_mechanical' => 'required|string',
        ]);

        $id = $request->input('id');
        $jenisDtId = $request->input('jenis_dt_id') ?: null;

        if ($id) {
            $report = CorrectiveMaintenance::findOrFail($id);
            $report->update([
                'tanggal' => $request->input('tanggal'),
                'shift' => $request->input('shift'),
                'grup' => $request->input('grup'),
                'mesin' => $request->input('mesin'),
                'pouch_sachet' => $request->input('pouch_sachet'),
                'jam_mulai' => $request->input('jam_mulai'),
                'jam_selesai' => $request->input('jam_selesai'),
                'total_menit' => $request->input('total_menit'),
                'keterangan' => $request->input('keterangan'),
                'downtime' => $request->input('downtime'),
                'jenis_dt_id' => $jenisDtId,
                'am_pm' => $request->input('am_pm'),
                'electrical_mechanical' => $request->input('electrical_mechanical')
            ]);
        } else {
            CorrectiveMaintenance::create([
                'tanggal' => $request->input('tanggal'),
                'shift' => $request->input('shift'),
                'grup' => $request->input('grup'),
                'mesin' => $request->input('mesin'),
                'pouch_sachet' => $request->input('pouch_sachet'),
                'jam_mulai' => $request->input('jam_mulai'),
                'jam_selesai' => $request->input('jam_selesai'),
                'total_menit' => $request->input('total_menit'),
                'keterangan' => $request->input('keterangan'),
                'downtime' => $request->input('downtime'),
                'jenis_dt_id' => $jenisDtId,
                'am_pm' => $request->input('am_pm'),
                'electrical_mechanical' => $request->input('electrical_mechanical'),
                'created_by' => Auth::id()
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $report = CorrectiveMaintenance::findOrFail($id);
        $report->delete();
        return response()->json(['success' => true]);
    }

    public function export(Request $request)
    {
        $query = CorrectiveMaintenance::with(['jenisDt', 'creator'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc');

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('mesin', 'like', $search)
                  ->orWhere('downtime', 'like', $search)
                  ->orWhere('keterangan', 'like', $search)
                  ->orWhereHas('jenisDt', function ($q2) use ($search) {
                      $q2->where('name', 'like', $search);
                  });
            });
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->input('shift'));
        }

        if ($request->filled('pouch_sachet')) {
            $query->where('pouch_sachet', $request->input('pouch_sachet'));
        }

        if ($request->filled('electrical_mechanical')) {
            $query->where('electrical_mechanical', $request->input('electrical_mechanical'));
        }

        if ($request->filled('month')) {
            $query->where('tanggal', 'like', $request->input('month') . '%');
        }

        $reports = $query->get();

        if ($reports->isEmpty()) {
            return "<script>alert('Tidak ada data ditemukan untuk periode tersebut'); window.close();</script>";
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Corrective Maintenance');
        $sheet->setShowGridlines(true);

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ];

        $headers = [
            'A1' => 'NO',
            'B1' => 'TANGGAL',
            'C1' => 'SHIFT',
            'D1' => 'GRUP',
            'E1' => 'MESIN',
            'F1' => 'POUCH/SACHET',
            'G1' => 'JAM MULAI',
            'H1' => 'JAM SELESAI',
            'I1' => 'TOTAL MENIT',
            'J1' => 'KETERANGAN DOWNTIME',
            'K1' => 'JENIS DT',
            'L1' => 'AM/PM',
            'M1' => 'ELECTRICAL/MECHANICAL',
            'N1' => 'CATATAN TAMBAHAN',
            'O1' => 'DIBUAT OLEH'
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $sheet->getStyle('A1:O1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $currentRow = 2;
        foreach ($reports as $index => $r) {
            $sheet->setCellValue('A' . $currentRow, $index + 1);
            $sheet->setCellValue('B' . $currentRow, $r->tanggal);
            $sheet->setCellValue('C' . $currentRow, $r->shift);
            $sheet->setCellValue('D' . $currentRow, $r->grup);
            $sheet->setCellValue('E' . $currentRow, $r->mesin);
            $sheet->setCellValue('F' . $currentRow, $r->pouch_sachet);
            $sheet->setCellValue('G' . $currentRow, substr($r->jam_mulai, 0, 5));
            $sheet->setCellValue('H' . $currentRow, substr($r->jam_selesai, 0, 5));
            $sheet->setCellValue('I' . $currentRow, (int) $r->total_menit);
            $sheet->setCellValue('J' . $currentRow, $r->downtime ?: '—');
            $sheet->setCellValue('K' . $currentRow, $r->jenisDt->name ?? '—');
            $sheet->setCellValue('L' . $currentRow, $r->am_pm);
            $sheet->setCellValue('M' . $currentRow, $r->electrical_mechanical);
            $sheet->setCellValue('N' . $currentRow, $r->keterangan ?: '—');
            $sheet->setCellValue('O' . $currentRow, $r->creator->username ?? '—');

            $sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $currentRow . ':I' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L' . $currentRow . ':M' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle('A' . $currentRow . ':O' . $currentRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->getColor()->setRGB('E0E0E0');

            $sheet->getRowDimension($currentRow)->setRowHeight(20);
            $currentRow++;
        }

        foreach (range(1, 15) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Corrective_Maintenance_' . now()->format('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->file('file')->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            // 1. Find the header row and map columns dynamically
            $headerRowIndex = null;
            $colMap = [
                'tanggal' => null,
                'shift' => null,
                'grup' => null,
                'mesin' => null,
                'pouch_sachet' => null,
                'jam_mulai' => null,
                'jam_selesai' => null,
                'total_menit' => null,
                'downtime' => null,
                'jenis_dt' => null,
                'am_pm' => null,
                'electrical_mechanical' => null,
                'keterangan' => null
            ];

            foreach ($rows as $index => $row) {
                // Check if this row contains 'TANGGAL'
                foreach ($row as $colLetter => $val) {
                    $cleanVal = strtolower(str_replace(' ', '', trim($val)));
                    if (strpos($cleanVal, 'tanggal') !== false) {
                        $headerRowIndex = $index;
                        break;
                    }
                }
                if ($headerRowIndex !== null) {
                    // Map this row's columns
                    foreach ($row as $colLetter => $val) {
                        $cleanVal = strtolower(str_replace([' ', '/', '_', '-'], '', trim($val)));
                        if (empty($cleanVal)) continue;

                        if (strpos($cleanVal, 'tanggal') !== false) {
                            $colMap['tanggal'] = $colLetter;
                        } elseif (strpos($cleanVal, 'shift') !== false) {
                            $colMap['shift'] = $colLetter;
                        } elseif (strpos($cleanVal, 'grup') !== false) {
                            $colMap['grup'] = $colLetter;
                        } elseif (strpos($cleanVal, 'mesin') !== false) {
                            $colMap['mesin'] = $colLetter;
                        } elseif (strpos($cleanVal, 'pouch') !== false || strpos($cleanVal, 'sachet') !== false || strpos($cleanVal, 'uchsach') !== false) {
                            $colMap['pouch_sachet'] = $colLetter;
                        } elseif (strpos($cleanVal, 'jammulai') !== false || strpos($cleanVal, 'ammula') !== false || $cleanVal === 'mulai') {
                            $colMap['jam_mulai'] = $colLetter;
                        } elseif (strpos($cleanVal, 'jamselesai') !== false || strpos($cleanVal, 'amseles') !== false || $cleanVal === 'selesai') {
                            $colMap['jam_selesai'] = $colLetter;
                        } elseif (strpos($cleanVal, 'totalmenit') !== false || $cleanVal === 'menit') {
                            $colMap['total_menit'] = $colLetter;
                        } elseif (strpos($cleanVal, 'keterangandowntime') !== false || strpos($cleanVal, 'ngandov') !== false || $cleanVal === 'downtime') {
                            $colMap['downtime'] = $colLetter;
                        } elseif (strpos($cleanVal, 'jenisdt') !== false || $cleanVal === 'jenis') {
                            $colMap['jenis_dt'] = $colLetter;
                        } elseif ($cleanVal === 'ampm') {
                            $colMap['am_pm'] = $colLetter;
                        } elseif (strpos($cleanVal, 'electrical') !== false || strpos($cleanVal, 'mechanical') !== false || strpos($cleanVal, 'calmech') !== false || strpos($cleanVal, 'klasifikasi') !== false) {
                            $colMap['electrical_mechanical'] = $colLetter;
                        } elseif (strpos($cleanVal, 'catatan') !== false || strpos($cleanVal, 'tambahan') !== false || $cleanVal === 'keterangan') {
                            $colMap['keterangan'] = $colLetter;
                        }
                    }
                    break;
                }
            }

            if ($headerRowIndex === null || !$colMap['tanggal'] || !$colMap['shift'] || !$colMap['mesin']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format file Excel tidak dikenali. Pastikan memiliki baris judul kolom dengan minimal kolom TANGGAL, SHIFT, dan MESIN.'
                ], 400);
            }

            $successCount = 0;
            $createdById = Auth::id();

            foreach ($rows as $index => $row) {
                if ($index <= $headerRowIndex) continue; // Skip header row and anything above

                // Get fields
                $tanggal = $colMap['tanggal'] ? trim($row[$colMap['tanggal']] ?? '') : '';
                $shift = $colMap['shift'] ? trim($row[$colMap['shift']] ?? '') : '';
                $grup = $colMap['grup'] ? trim($row[$colMap['grup']] ?? '') : '';
                $mesin = $colMap['mesin'] ? trim($row[$colMap['mesin']] ?? '') : '';
                $pouchSachet = $colMap['pouch_sachet'] ? trim($row[$colMap['pouch_sachet']] ?? '') : '';
                $jamMulai = $colMap['jam_mulai'] ? trim($row[$colMap['jam_mulai']] ?? '') : '';
                $jamSelesai = $colMap['jam_selesai'] ? trim($row[$colMap['jam_selesai']] ?? '') : '';
                $totalMenit = $colMap['total_menit'] ? trim($row[$colMap['total_menit']] ?? '') : '';
                $downtime = $colMap['downtime'] ? trim($row[$colMap['downtime']] ?? '') : '';
                $jenisDtName = $colMap['jenis_dt'] ? trim($row[$colMap['jenis_dt']] ?? '') : '';
                $amPm = $colMap['am_pm'] ? trim($row[$colMap['am_pm']] ?? '') : '';
                $elecMech = $colMap['electrical_mechanical'] ? trim($row[$colMap['electrical_mechanical']] ?? '') : '';
                $keterangan = $colMap['keterangan'] ? trim($row[$colMap['keterangan']] ?? '') : '';

                // Validation of mandatory fields
                if (empty($tanggal) || empty($shift) || empty($mesin) || empty($jamMulai) || empty($jamSelesai)) {
                    continue; // Skip incomplete rows
                }

                // Skip header rows if repeated (safety)
                if (strtolower($tanggal) === 'tanggal' || strtolower($shift) === 'shift') {
                    continue;
                }

                // Parse Date (handles Excel serial dates as well)
                if (is_numeric($tanggal)) {
                    $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal)->format('Y-m-d');
                } else {
                    $tanggal = date('Y-m-d', strtotime(str_replace('/', '-', $tanggal)));
                }

                // Resolve or create Jenis DT
                $jenisDtId = null;
                if (!empty($jenisDtName) && $jenisDtName !== '—') {
                    $jenisDt = JenisDt::firstOrCreate(
                        ['name' => $jenisDtName],
                        ['aktif' => true]
                    );
                    $jenisDtId = $jenisDt->id;
                }

                // If total_menit is not provided or placeholder
                if (empty($totalMenit) || $totalMenit === '—') {
                    $sh = strtotime($jamMulai);
                    $eh = strtotime($jamSelesai);
                    if ($sh !== false && $eh !== false) {
                        $diff = ($eh - $sh) / 60;
                        if ($diff < 0) {
                            $diff += 24 * 60; // Overnight
                        }
                        $totalMenit = $diff;
                    } else {
                        $totalMenit = 0;
                    }
                }

                // Insert into database
                CorrectiveMaintenance::create([
                    'tanggal' => $tanggal,
                    'shift' => $shift,
                    'grup' => $grup,
                    'mesin' => $mesin,
                    'pouch_sachet' => $pouchSachet,
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'total_menit' => (int) $totalMenit,
                    'downtime' => $downtime,
                    'jenis_dt_id' => $jenisDtId,
                    'am_pm' => $amPm,
                    'electrical_mechanical' => $elecMech,
                    'keterangan' => $keterangan,
                    'created_by' => $createdById
                ]);

                $successCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimpor {$successCount} data corrective maintenance."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage()
            ], 500);
        }
    }
}
