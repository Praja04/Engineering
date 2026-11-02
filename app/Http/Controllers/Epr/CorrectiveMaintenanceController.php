<?php

namespace App\Http\Controllers\Epr;

use App\Http\Controllers\Controller;
use App\Models\Epr\CorrectiveMaintenance;
use App\Models\Epr\JenisDt;
use App\Models\Epr\CmLoadingTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CorrectiveMaintenanceController extends Controller
{
    public function form()
    {
        $jenisDts = JenisDt::where('aktif', true)->orderBy('name', 'asc')->get();
        $machines = \App\Models\ScoringMesin\Machine::orderBy('name', 'asc')->get();
        return view('epr.corrective-maintenance.form', compact('jenisDts', 'machines'));
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
            'file' => 'required|file|max:51200' // allow up to 50MB
        ]);

        try {
            $file = $request->file('file');
            $filePath = $file->getRealPath();

            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);

            // Check sheet names to only load 'HARIAN' if present (avoids loading massive weekly/pivot sheets)
            $sheetNames = $reader->listWorksheetNames($filePath);
            $targetSheet = null;
            foreach ($sheetNames as $name) {
                if (strcasecmp(trim($name), 'HARIAN') === 0) {
                    $targetSheet = $name;
                    break;
                }
            }

            if (!$targetSheet && !empty($sheetNames)) {
                $targetSheet = $sheetNames[0];
            }

            if ($targetSheet) {
                $reader->setLoadSheetsOnly([$targetSheet]);
            }

            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Set calculateFormulas = false to avoid hanging on external/pivot formulas
            $rows = $sheet->toArray(null, false, false, true);

            // 1. Dynamic Header Mapping
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
                foreach ($row as $colLetter => $val) {
                    $cleanVal = preg_replace('/[^a-z0-9]/', '', strtolower(trim((string)$val)));
                    if (strpos($cleanVal, 'tanggal') !== false || $cleanVal === 'tgl' || $cleanVal === 'date') {
                        $headerRowIndex = $index;
                        break;
                    }
                }
                if ($headerRowIndex !== null) {
                    foreach ($row as $colLetter => $val) {
                        $cleanVal = preg_replace('/[^a-z0-9]/', '', strtolower(trim((string)$val)));
                        if (empty($cleanVal)) continue;

                        if (strpos($cleanVal, 'tanggal') !== false || $cleanVal === 'tgl' || $cleanVal === 'date') {
                            $colMap['tanggal'] = $colLetter;
                        } elseif (strpos($cleanVal, 'shift') !== false) {
                            $colMap['shift'] = $colLetter;
                        } elseif (strpos($cleanVal, 'grup') !== false || strpos($cleanVal, 'group') !== false) {
                            $colMap['grup'] = $colLetter;
                        } elseif (strpos($cleanVal, 'mesin') !== false || strpos($cleanVal, 'machine') !== false) {
                            $colMap['mesin'] = $colLetter;
                        } elseif (strpos($cleanVal, 'pouch') !== false || strpos($cleanVal, 'sachet') !== false) {
                            $colMap['pouch_sachet'] = $colLetter;
                        } elseif (strpos($cleanVal, 'jammulai') !== false || $cleanVal === 'mulai' || $cleanVal === 'start' || $cleanVal === 'waktumulai') {
                            $colMap['jam_mulai'] = $colLetter;
                        } elseif (strpos($cleanVal, 'jamselesai') !== false || $cleanVal === 'selesai' || $cleanVal === 'end' || $cleanVal === 'waktuselesai') {
                            $colMap['jam_selesai'] = $colLetter;
                        } elseif (strpos($cleanVal, 'totalmenit') !== false || $cleanVal === 'menit' || strpos($cleanVal, 'durasi') !== false) {
                            $colMap['total_menit'] = $colLetter;
                        } elseif (strpos($cleanVal, 'keterangandowntime') !== false || strpos($cleanVal, 'keterangandt') !== false || $cleanVal === 'downtime' || strpos($cleanVal, 'problem') !== false) {
                            $colMap['downtime'] = $colLetter;
                        } elseif (strpos($cleanVal, 'jenisdt') !== false || $cleanVal === 'jenis' || $cleanVal === 'kategoridt') {
                            $colMap['jenis_dt'] = $colLetter;
                        } elseif ($cleanVal === 'ampm' || (strpos($cleanVal, 'am') !== false && strpos($cleanVal, 'pm') !== false)) {
                            $colMap['am_pm'] = $colLetter;
                        } elseif (strpos($cleanVal, 'electrical') !== false || strpos($cleanVal, 'mechanical') !== false || strpos($cleanVal, 'klasifikasi') !== false) {
                            $colMap['electrical_mechanical'] = $colLetter;
                        } elseif (strpos($cleanVal, 'catatan') !== false || strpos($cleanVal, 'tambahan') !== false || ($cleanVal === 'keterangan' && !$colMap['downtime'])) {
                            $colMap['keterangan'] = $colLetter;
                        }
                    }
                    break;
                }
            }

            if ($headerRowIndex === null || !$colMap['tanggal'] || !$colMap['shift'] || !$colMap['mesin']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format file Excel tidak sesuai template. Pastikan baris header memuat kolom TANGGAL, SHIFT, dan MESIN.'
                ], 400);
            }

            $parseTime = function($val) {
                if ($val === null || $val === '') return '00:00:00';
                if (is_numeric($val)) {
                    return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('H:i:s');
                }
                $t = strtotime(trim((string)$val));
                if ($t !== false) {
                    return date('H:i:s', $t);
                }
                return trim((string)$val);
            };

            $successCount = 0;
            $createdById = Auth::id();

            // Preload existing JenisDt to minimize database queries
            $existingJenisDts = JenisDt::all()->keyBy(function ($item) {
                return strtolower(trim($item->name));
            });

            $recordsToInsert = [];

            foreach ($rows as $index => $row) {
                if ($index <= $headerRowIndex) continue;

                $tanggalRaw = $colMap['tanggal'] ? ($row[$colMap['tanggal']] ?? null) : null;
                $shiftRaw = $colMap['shift'] ? ($row[$colMap['shift']] ?? null) : null;
                $mesinRaw = $colMap['mesin'] ? ($row[$colMap['mesin']] ?? null) : null;

                // Skip empty rows (common in Excel templates)
                if (empty($tanggalRaw) && empty($shiftRaw) && empty($mesinRaw)) {
                    continue;
                }

                if (strtolower(trim((string)$tanggalRaw)) === 'tanggal' || strtolower(trim((string)$shiftRaw)) === 'shift') {
                    continue;
                }

                // Parse Date
                if (is_numeric($tanggalRaw)) {
                    $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalRaw)->format('Y-m-d');
                } else {
                    $cleanDate = str_replace('/', '-', trim((string)$tanggalRaw));
                    $parsedTime = strtotime($cleanDate);
                    $tanggal = $parsedTime ? date('Y-m-d', $parsedTime) : date('Y-m-d');
                }

                $shift = trim((string)($row[$colMap['shift']] ?? ''));
                $grup = $colMap['grup'] ? trim((string)($row[$colMap['grup']] ?? '')) : '';
                $mesin = trim((string)($row[$colMap['mesin']] ?? ''));
                $pouchSachet = $colMap['pouch_sachet'] ? trim((string)($row[$colMap['pouch_sachet']] ?? '')) : '';
                if (!empty($pouchSachet)) {
                    $pouchSachet = ucfirst(strtolower($pouchSachet));
                }

                $jamMulai = $parseTime($colMap['jam_mulai'] ? ($row[$colMap['jam_mulai']] ?? null) : null);
                $jamSelesai = $parseTime($colMap['jam_selesai'] ? ($row[$colMap['jam_selesai']] ?? null) : null);

                // Parse Total Menit
                $totalMenitRaw = $colMap['total_menit'] ? ($row[$colMap['total_menit']] ?? null) : null;
                if (is_numeric($totalMenitRaw)) {
                    $totalMenit = (int) round((float) $totalMenitRaw);
                } else {
                    $sh = strtotime($jamMulai);
                    $eh = strtotime($jamSelesai);
                    if ($sh !== false && $eh !== false) {
                        $diff = ($eh - $sh) / 60;
                        if ($diff < 0) $diff += 24 * 60;
                        $totalMenit = (int) round($diff);
                    } else {
                        $totalMenit = 0;
                    }
                }

                $downtime = $colMap['downtime'] ? trim((string)($row[$colMap['downtime']] ?? '')) : null;
                $keterangan = $colMap['keterangan'] ? trim((string)($row[$colMap['keterangan']] ?? '')) : null;
                if (empty($keterangan)) {
                    $keterangan = null;
                }

                // Resolve Jenis DT
                $jenisDtName = $colMap['jenis_dt'] ? trim((string)($row[$colMap['jenis_dt']] ?? '')) : '';
                $jenisDtId = null;
                if (!empty($jenisDtName) && $jenisDtName !== '—' && $jenisDtName !== '-') {
                    $lookupKey = strtolower($jenisDtName);
                    if ($existingJenisDts->has($lookupKey)) {
                        $jenisDtId = $existingJenisDts->get($lookupKey)->id;
                    } else {
                        $newJenis = JenisDt::create([
                            'name' => $jenisDtName,
                            'aktif' => true
                        ]);
                        $existingJenisDts->put($lookupKey, $newJenis);
                        $jenisDtId = $newJenis->id;
                    }
                }

                // Parse AM/PM
                $amPmRaw = $colMap['am_pm'] ? strtoupper(trim((string)($row[$colMap['am_pm']] ?? ''))) : '';
                if ($amPmRaw === 'AM' || $amPmRaw === 'PM') {
                    $amPm = $amPmRaw;
                } else {
                    $h = (int) date('H', strtotime($jamMulai));
                    $amPm = ($h >= 12) ? 'PM' : 'AM';
                }

                // Parse Electrical / Mechanical
                $elecMechRaw = $colMap['electrical_mechanical'] ? trim((string)($row[$colMap['electrical_mechanical']] ?? '')) : '';
                if (stripos($elecMechRaw, 'elec') !== false) {
                    $elecMech = 'Electrical';
                } elseif (stripos($elecMechRaw, 'mech') !== false) {
                    $elecMech = 'Mechanical';
                } else {
                    $elecMech = !empty($elecMechRaw) ? ucfirst(strtolower($elecMechRaw)) : 'Mechanical';
                }

                $now = now();
                $recordsToInsert[] = [
                    'tanggal' => $tanggal,
                    'shift' => $shift,
                    'grup' => $grup,
                    'mesin' => $mesin,
                    'pouch_sachet' => $pouchSachet,
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'total_menit' => $totalMenit,
                    'downtime' => $downtime,
                    'jenis_dt_id' => $jenisDtId,
                    'am_pm' => $amPm,
                    'electrical_mechanical' => $elecMech,
                    'keterangan' => $keterangan,
                    'created_by' => $createdById,
                    'created_at' => $now,
                    'updated_at' => $now
                ];

                $successCount++;
            }

            // Batch insert in chunks
            foreach (array_chunk($recordsToInsert, 200) as $chunk) {
                CorrectiveMaintenance::insert($chunk);
            }

            // 2. Extract Loading Times from matrix (Columns N-Y, Rows 4 to 34)
            $importDate = !empty($recordsToInsert) ? $recordsToInsert[0]['tanggal'] : now()->format('Y-m-d');
            $loadingTimesCount = 0;
            $currentCategory = 'Pouch';

            for ($r = 4; $r <= 35; $r++) {
                if (!isset($rows[$r])) continue;
                $row = $rows[$r];

                $colN = trim((string)($row['N'] ?? ''));
                $colO = trim((string)($row['O'] ?? ''));

                if (stripos($colN, 'sachet') !== false) {
                    $currentCategory = 'Sachet';
                } elseif (stripos($colN, 'pouch') !== false && stripos($colN, 'sachet') === false && stripos($colN, 'rata') === false) {
                    $currentCategory = 'Pouch';
                }

                if (empty($colO) || stripos($colN, 'rata') !== false || stripos($colN, 'total') !== false) {
                    continue;
                }

                $mesin = $colO;
                $ltA = is_numeric($row['P'] ?? null) ? (int)$row['P'] : 0;
                $ltB = is_numeric($row['S'] ?? null) ? (int)$row['S'] : 0;
                $ltC = is_numeric($row['V'] ?? null) ? (int)$row['V'] : 0;
                $ltAll = is_numeric($row['Y'] ?? null) ? (int)$row['Y'] : ($ltA + $ltB + $ltC);

                CmLoadingTime::updateOrCreate(
                    [
                        'tanggal' => $importDate,
                        'mesin' => $mesin
                    ],
                    [
                        'kategori' => $currentCategory,
                        'loading_time_a' => $ltA,
                        'loading_time_b' => $ltB,
                        'loading_time_c' => $ltC,
                        'loading_time_all' => $ltAll,
                        'created_by' => $createdById
                    ]
                );
                $loadingTimesCount++;
            }

            $message = "Berhasil mengimpor {$successCount} data corrective maintenance";
            if ($loadingTimesCount > 0) {
                $message .= " dan {$loadingTimesCount} data loading time mesin";
            }
            $message .= " dari template Excel.";

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLoadingTimes(Request $request)
    {
        $query = CmLoadingTime::orderBy('id', 'asc');

        if ($request->filled('month')) {
            $query->where('tanggal', 'like', $request->input('month') . '%');
        }

        if ($request->filled('date')) {
            $query->where('tanggal', $request->input('date'));
        }

        return response()->json($query->get());
    }



    // ── Extra Data Management (Cost, OEE KPI, Action Plan) ──

    public function extraData(Request $request)
    {
        $month = $request->input('month', date('Y-m'));

        $costs = \App\Models\Epr\CmCost::where('tanggal', 'like', $month . '%')->orderBy('tanggal', 'desc')->get();
        $kpis = \App\Models\Epr\CmMachineKpi::where('month', $month)->orderBy('mesin', 'asc')->get();
        $actionPlans = \App\Models\Epr\CmActionPlan::where('month', $month)->orderBy('mesin', 'asc')->get();

        $machines = \App\Models\ScoringMesin\Machine::orderBy('name', 'asc')->get();

        return view('epr.corrective-maintenance.extra-data', compact('month', 'costs', 'kpis', 'actionPlans', 'machines'));
    }

    public function storeCost(Request $request)
    {
        $request->validate([
            'mesin' => 'required|string',
            'tanggal' => 'required|date',
            'kategori_biaya' => 'required|string',
            'jumlah_biaya' => 'required|numeric|min:0',
        ]);

        \App\Models\Epr\CmCost::create([
            'mesin' => $request->input('mesin'),
            'tanggal' => $request->input('tanggal'),
            'kategori_biaya' => $request->input('kategori_biaya'),
            'deskripsi' => $request->input('deskripsi'),
            'jumlah_biaya' => $request->input('jumlah_biaya'),
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Data biaya perbaikan berhasil disimpan.');
    }

    public function deleteCost($id)
    {
        $cost = \App\Models\Epr\CmCost::findOrFail($id);
        $cost->delete();
        return redirect()->back()->with('success', 'Data biaya berhasil dihapus.');
    }

    public function storeKpi(Request $request)
    {
        $request->validate([
            'month' => 'required|string',
            'mesin' => 'required|string',
            'availability_pct' => 'required|numeric|min:0|max:100',
            'performance_pct' => 'required|numeric|min:0|max:100',
            'quality_pct' => 'required|numeric|min:0|max:100',
        ]);

        $avail = (float)$request->input('availability_pct');
        $perf = (float)$request->input('performance_pct');
        $qual = (float)$request->input('quality_pct');
        $oee = round(($avail * $perf * $qual) / 10000, 1);

        \App\Models\Epr\CmMachineKpi::updateOrCreate(
            ['month' => $request->input('month'), 'mesin' => $request->input('mesin')],
            [
                'availability_pct' => $avail,
                'performance_pct' => $perf,
                'quality_pct' => $qual,
                'oee_pct' => $oee,
                'pm_compliance_pct' => $request->input('pm_compliance_pct', 90),
                'repeat_failure_pct' => $request->input('repeat_failure_pct', 10),
                'minor_stop_freq' => $request->input('minor_stop_freq', 10),
                'cost_per_hour' => $request->input('cost_per_hour', 50),
                'energy_per_pack' => $request->input('energy_per_pack', 0.35),
                'created_by' => Auth::id(),
            ]
        );

        return redirect()->back()->with('success', 'Data OEE KPI Mesin berhasil disimpan.');
    }

    public function deleteKpi($id)
    {
        $kpi = \App\Models\Epr\CmMachineKpi::findOrFail($id);
        $kpi->delete();
        return redirect()->back()->with('success', 'Data KPI berhasil dihapus.');
    }

    public function storeActionPlan(Request $request)
    {
        $request->validate([
            'month' => 'required|string',
            'mesin' => 'required|string',
            'isu_utama' => 'required|string',
        ]);

        \App\Models\Epr\CmActionPlan::create([
            'month' => $request->input('month'),
            'mesin' => $request->input('mesin'),
            'isu_utama' => $request->input('isu_utama'),
            'akar_masalah' => $request->input('akar_masalah'),
            'saran_perbaikan' => $request->input('saran_perbaikan'),
            'pic' => $request->input('pic'),
            'target_date' => $request->input('target_date'),
            'w1_status' => $request->input('w1_status', 'none'),
            'w2_status' => $request->input('w2_status', 'none'),
            'w3_status' => $request->input('w3_status', 'none'),
            'w4_status' => $request->input('w4_status', 'none'),
            'status' => $request->input('status', 'Open'),
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Action plan berhasil disimpan.');
    }

    public function deleteActionPlan($id)
    {
        $ap = \App\Models\Epr\CmActionPlan::findOrFail($id);
        $ap->delete();
        return redirect()->back()->with('success', 'Action plan berhasil dihapus.');
    }
}
