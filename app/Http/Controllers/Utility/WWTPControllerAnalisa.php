<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\wwtp_analisa\WwtpAnalisa;
use App\Models\Utility\wwtp_analisa\WwtpAnalisaDetail;
use App\Models\Utility\wwtp_analisa\WwtpParameter;
use App\Models\Utility\wwtp_analisa\WwtpPoint;
use App\Models\Utility\wwtp_analisa\WwtpStandard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use App\Services\GoogleSheetsService;
use App\Jobs\SyncGoogleSheetsJob;

class WWTPControllerAnalisa extends Controller
{
    public function form_analisa()
    {
        $parameters = WwtpParameter::all();
        $points = WwtpPoint::all();
        $standardsData = \App\Models\Utility\wwtp_analisa\WwtpStandard::all();

        $standards = [];
        foreach ($standardsData as $std) {
            $standards[$std->point_id . '_' . $std->parameter_id] = $std->standard_value;
        }

        return view('utility.wwtp.form_analisa', compact('parameters', 'points', 'standards'));
    }

    public function data_analisa()
    {
        $parameters = WwtpParameter::all();
        $standardsData = \App\Models\Utility\wwtp_analisa\WwtpStandard::all();

        $standards = [];
        foreach ($standardsData as $std) {
            $standards[$std->point_id . '_' . $std->parameter_id] = $std->standard_value;
        }

        return view('utility.wwtp.data_analisa', compact('parameters', 'standards'));
    }

    public function downloadPdf($id)
    {
        $analisa = WwtpAnalisa::with(['creator', 'pelaksana', 'foreman', 'supervisor', 'details.point', 'details.parameter'])->findOrFail($id);

        $parameters = WwtpParameter::all();
        $standardsData = WwtpStandard::all();

        $standards = [];
        foreach ($standardsData as $std) {
            $standards[$std->point_id . '_' . $std->parameter_id] = $std->standard_value;
        }

        // Reorganize details by parameter for easier looping in PDF
        $parameterData = [];
        foreach ($analisa->details as $detail) {
            $paramId = $detail->parameter_id;
            if (!isset($parameterData[$paramId])) {
                $parameterData[$paramId] = [
                    'id' => $paramId,
                    'name' => $detail->parameter->parameter_name ?? 'Unknown Parameter',
                    'unit' => $detail->parameter->unit ?? '',
                    'points' => []
                ];
            }
            $parameterData[$paramId]['points'][] = [
                'point_id' => $detail->point_id,
                'point_name' => $detail->point->point_name ?? 'Unknown Point',
                'value' => $detail->hasil_analisa
            ];
        }

        // Sort parameters by their order in parameter table
        $parameterOrder = $parameters->pluck('id')->toArray();
        uksort($parameterData, function ($a, $b) use ($parameterOrder) {
            return array_search($a, $parameterOrder) <=> array_search($b, $parameterOrder);
        });

        // Set A4 paper format
        $pdf = Pdf::loadView('utility.wwtp.pdf_analisa', compact('analisa', 'parameterData', 'standards'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Helvetica',
            ]);

        $filename = 'laporan-analisa-wwtp-' . $analisa->analisa_date . '.pdf';

        return $pdf->stream($filename);
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->input('bulan'); // Format YYYY-MM
        $search = $request->input('search');

        $query = WwtpAnalisa::with(['details.parameter', 'details.point'])
            ->orderBy('analisa_date', 'asc');

        if ($bulan) {
            $query->whereRaw("DATE_FORMAT(analisa_date, '%Y-%m') = ?", [$bulan]);
        }

        if ($search) {
            $query->where('analisa_date', 'like', "%{$search}%");
        }

        $analisaRecords = $query->get();

        if ($analisaRecords->isEmpty() && !$bulan) {
            return "<script>alert('Tidak ada data analisa ditemukan untuk periode tersebut'); window.close();</script>";
        }

        // Generate daily dates for the target range (including days with no data)
        $dates = [];
        if ($bulan) {
            $start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
            $end = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $dates[] = $d->format('Y-m-d');
            }
        } else {
            $minDate = WwtpAnalisa::min('analisa_date');
            $maxDate = WwtpAnalisa::max('analisa_date');
            if ($minDate && $maxDate) {
                $start = Carbon::parse($minDate);
                $end = Carbon::parse($maxDate);
                // limit to at most 366 days
                if ($start->diffInDays($end) > 366) {
                    $start = Carbon::now()->startOfMonth();
                    $end = Carbon::now()->endOfMonth();
                }
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $dates[] = $d->format('Y-m-d');
            }
        }

        // Build lookup: lookup[date][parameter_id][point_id] = hasil_analisa
        $lookup = [];
        foreach ($analisaRecords as $record) {
            $dateStr = $record->analisa_date->format('Y-m-d');
            foreach ($record->details as $detail) {
                $lookup[$dateStr][$detail->parameter_id][$detail->point_id] = $detail->hasil_analisa;
            }
        }

        // Get all parameters, ordered by name
        $parameters = WwtpParameter::orderBy('parameter_name')->get();

        // Get all standards with points, ordered by point_name
        $standards = WwtpStandard::with(['point'])
            ->join('wwtp_point', 'wwtp_standards.point_id', '=', 'wwtp_point.id')
            ->select('wwtp_standards.*')
            ->orderBy('wwtp_point.point_name')
            ->get()
            ->groupBy('parameter_id');

        $activeParameters = [];
        foreach ($parameters as $param) {
            if (isset($standards[$param->id]) && $standards[$param->id]->isNotEmpty()) {
                $activeParameters[] = [
                    'parameter' => $param,
                    'standards' => $standards[$param->id]
                ];
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Analisa WWTP');

        // Freeze columns A, B, C (first 3 columns) and rows 1-5 (header rows)
        $sheet->freezePane('D6');

        // Show grid lines explicitly
        $sheet->setShowGridlines(true);

        // Title and Subtitle block
        $sheet->setCellValue('A1', 'LAPORAN ANALISA WWTP');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        $periodeText = 'Periode: ';
        if ($bulan) {
            $periodeText .= Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y');
        } else {
            $periodeText .= 'Semua Periode';
        }
        $sheet->setCellValue('A2', $periodeText);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(11);

        // Set static headers with vertical merging across row 4 & 5
        $headerRow1 = 4;
        $headerRow2 = 5;

        $sheet->setCellValue('A' . $headerRow1, 'Parameter / Point Pengukuran');
        $sheet->mergeCells('A' . $headerRow1 . ':A' . $headerRow2);

        $sheet->setCellValue('B' . $headerRow1, 'Standar');
        $sheet->mergeCells('B' . $headerRow1 . ':B' . $headerRow2);

        $sheet->setCellValue('C' . $headerRow1, 'Satuan');
        $sheet->mergeCells('C' . $headerRow1 . ':C' . $headerRow2);

        // Date columns: Row 4 is day name, Row 5 is date value
        foreach ($dates as $colIdx => $date) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 4);

            // Row 4: Day name in Indonesian
            $dayName = Carbon::parse($date)->locale('id')->translatedFormat('l');
            $sheet->setCellValue($colLetter . $headerRow1, $dayName);

            // Row 5: Date string
            $sheet->setCellValue($colLetter . $headerRow2, Carbon::parse($date)->format('d/m/Y'));
        }

        // Style the double-row headers
        $lastColIdx = count($dates) + 3;
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIdx);
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '299CDB'], // Premium blue
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D0D0D0'],
                ],
            ],
        ];
        $sheet->getStyle('A' . $headerRow1 . ':' . $lastColLetter . $headerRow2)->applyFromArray($headerStyle);
        $sheet->getRowDimension($headerRow1)->setRowHeight(25);
        $sheet->getRowDimension($headerRow2)->setRowHeight(25);

        // Start writing data from row 6
        $currentRow = 6;

        $parameterRowStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '1F618D'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EBF5FB'], // Light premium blue background
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D0D0D0'],
                ],
            ],
        ];

        $pointRowStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'E0E0E0'],
                ],
            ],
        ];

        foreach ($activeParameters as $activeParam) {
            $param = $activeParam['parameter'];
            $paramStds = $activeParam['standards'];

            // Parameter Header Row
            $sheet->setCellValue('A' . $currentRow, $param->parameter_name);
            $sheet->setCellValue('B' . $currentRow, '-');
            $sheet->setCellValue('C' . $currentRow, $param->unit ?: '-');

            $sheet->getStyle('A' . $currentRow . ':' . $lastColLetter . $currentRow)->applyFromArray($parameterRowStyle);
            $sheet->getRowDimension($currentRow)->setRowHeight(22);
            $currentRow++;

            // Point rows
            foreach ($paramStds as $std) {
                $point = $std->point;
                if (!$point) continue;

                $sheet->setCellValue('A' . $currentRow, '   ' . $point->point_name); // Indented for visual hierarchy
                $sheet->setCellValue('B' . $currentRow, $std->standard_value !== null ? (float)$std->standard_value : '-');
                $sheet->setCellValue('C' . $currentRow, $param->unit ?: '-');

                // Align Col B & C to center
                $sheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Write date values
                foreach ($dates as $colIdx => $date) {
                    $valColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 4);
                    $val = $lookup[$date][$param->id][$point->id] ?? null;
                    if ($val !== null) {
                        $sheet->setCellValue($valColLetter . $currentRow, (float)$val);
                    } else {
                        $sheet->setCellValue($valColLetter . $currentRow, '-');
                    }
                    // Align values to center
                    $sheet->getStyle($valColLetter . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }

                $sheet->getStyle('A' . $currentRow . ':' . $lastColLetter . $currentRow)->applyFromArray($pointRowStyle);
                $sheet->getRowDimension($currentRow)->setRowHeight(20);
                $currentRow++;
            }
        }

        // Auto-fit column widths
        foreach (range(1, $lastColIdx) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Output Excel response
        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Analisa_WWTP_' . now()->format('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1'); // for compatibility with IE9/SSL

        $writer->save('php://output');
        exit;
    }

    public function manage_standar()
    {
        $points = WwtpPoint::orderBy('point_name')->get();
        $parameters = WwtpParameter::orderBy('parameter_name')->get();

        return view('utility.wwtp.manage_standar', compact('points', 'parameters'));
    }

    public function indexParameter(Request $request)
    {
        $query = WwtpParameter::query()
            ->withCount('standards')
            ->orderBy('parameter_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('parameter_name', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%");
            });
        }

        return response()->json($query->get());
    }

    public function showParameter($id)
    {
        return response()->json(WwtpParameter::findOrFail($id));
    }

    public function storeParameter(Request $request)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        $validated = $request->validate([
            'parameter_name' => 'required|string|max:255|unique:wwtp_parameters,parameter_name',
            'unit' => 'nullable|string|max:50',
        ], [
            'parameter_name.unique' => 'Parameter ini sudah terdaftar.',
        ]);

        $parameter = WwtpParameter::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Parameter berhasil disimpan.',
            'data' => $parameter,
        ]);
    }

    public function updateParameter(Request $request, $id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        $parameter = WwtpParameter::findOrFail($id);

        $validated = $request->validate([
            'parameter_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('wwtp_parameters', 'parameter_name')->ignore($parameter->id),
            ],
            'unit' => 'nullable|string|max:50',
        ], [
            'parameter_name.unique' => 'Parameter ini sudah terdaftar.',
        ]);

        $parameter->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Parameter berhasil diperbarui.',
            'data' => $parameter,
        ]);
    }

    public function destroyParameter($id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        WwtpParameter::findOrFail($id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Parameter berhasil dihapus.',
        ]);
    }

    public function indexPoint(Request $request)
    {
        $query = WwtpPoint::query()
            ->withCount('standards')
            ->orderBy('point_name');

        if ($request->filled('search')) {
            $query->where('point_name', 'like', "%{$request->search}%");
        }

        return response()->json($query->get());
    }

    public function showPoint($id)
    {
        return response()->json(WwtpPoint::findOrFail($id));
    }

    public function storePoint(Request $request)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        $validated = $request->validate([
            'point_name' => 'required|string|max:255|unique:wwtp_point,point_name',
        ], [
            'point_name.unique' => 'Point pengukuran ini sudah terdaftar.',
        ]);

        $point = WwtpPoint::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Point pengukuran berhasil disimpan.',
            'data' => $point,
        ]);
    }

    public function updatePoint(Request $request, $id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        $point = WwtpPoint::findOrFail($id);

        $validated = $request->validate([
            'point_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('wwtp_point', 'point_name')->ignore($point->id),
            ],
        ], [
            'point_name.unique' => 'Point pengukuran ini sudah terdaftar.',
        ]);

        $point->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Point pengukuran berhasil diperbarui.',
            'data' => $point,
        ]);
    }

    public function destroyPoint($id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        WwtpPoint::findOrFail($id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Point pengukuran berhasil dihapus.',
        ]);
    }

    public function indexStandar(Request $request)
    {
        $query = WwtpStandard::with(['point', 'parameter'])
            ->join('wwtp_point', 'wwtp_standards.point_id', '=', 'wwtp_point.id')
            ->join('wwtp_parameters', 'wwtp_standards.parameter_id', '=', 'wwtp_parameters.id')
            ->select('wwtp_standards.*')
            ->orderBy('wwtp_parameters.parameter_name')
            ->orderBy('wwtp_point.point_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('wwtp_point.point_name', 'like', "%{$search}%")
                    ->orWhere('wwtp_parameters.parameter_name', 'like', "%{$search}%")
                    ->orWhere('wwtp_parameters.unit', 'like', "%{$search}%")
                    ->orWhere('wwtp_standards.standard_value', 'like', "%{$search}%");
            });
        }

        return response()->json($query->get());
    }

    public function showStandar($id)
    {
        return response()->json(WwtpStandard::with(['point', 'parameter'])->findOrFail($id));
    }

    public function storeStandar(Request $request)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        $validated = $request->validate([
            'point_id' => [
                'required',
                'exists:wwtp_point,id',
                Rule::unique('wwtp_standards')->where(fn($query) => $query->where('parameter_id', $request->parameter_id)),
            ],
            'parameter_id' => 'required|exists:wwtp_parameters,id',
            'standard_value' => 'nullable|numeric|min:0',
        ], [
            'point_id.unique' => 'Standar untuk kombinasi point dan parameter ini sudah ada.',
        ]);

        $standard = WwtpStandard::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Standar analisa WWTP berhasil disimpan.',
            'data' => $standard->load(['point', 'parameter']),
        ]);
    }

    public function updateStandar(Request $request, $id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        $standard = WwtpStandard::findOrFail($id);

        $validated = $request->validate([
            'point_id' => [
                'required',
                'exists:wwtp_point,id',
                Rule::unique('wwtp_standards')->ignore($standard->id)->where(fn($query) => $query->where('parameter_id', $request->parameter_id)),
            ],
            'parameter_id' => 'required|exists:wwtp_parameters,id',
            'standard_value' => 'nullable|numeric|min:0',
        ], [
            'point_id.unique' => 'Standar untuk kombinasi point dan parameter ini sudah ada.',
        ]);

        $standard->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Standar analisa WWTP berhasil diperbarui.',
            'data' => $standard->load(['point', 'parameter']),
        ]);
    }

    public function destroyStandar($id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        WwtpStandard::findOrFail($id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Standar analisa WWTP berhasil dihapus.',
        ]);
    }

    /**
     * Display a listing of the resource (JSON for DataTables/AJAX)
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page    = $request->input('page', 1);
        $bulan   = $request->input('bulan'); // Format YYYY-MM
        $search  = $request->input('search');

        $query = WwtpAnalisa::with(['creator', 'pelaksana', 'foreman', 'supervisor', 'details.parameter', 'details.point'])->orderBy('analisa_date', 'desc')->orderBy('shift', 'asc');

        if ($bulan) {
            $query->whereRaw("DATE_FORMAT(analisa_date, '%Y-%m') = ?", [$bulan]);
        }

        if ($search) {
            $query->where('analisa_date', 'like', "%{$search}%");
        }

        return response()->json(
            $query->paginate($perPage, ['*'], 'page', $page)
        );
    }

    public function checkFilledParameters(Request $request)
    {
        $request->validate([
            'analisa_date' => 'required|date',
        ]);

        $analisa = WwtpAnalisa::where('analisa_date', $request->analisa_date)
            ->with(['pelaksana', 'foreman', 'supervisor'])
            ->first();

        if (!$analisa) {
            return response()->json([
                'filled_parameter_ids' => [],
                'has_header' => false,
            ]);
        }

        $filledParameterIds = WwtpAnalisaDetail::where('analisa_id', $analisa->id)
            ->distinct()
            ->pluck('parameter_id');

        return response()->json([
            'filled_parameter_ids' => $filledParameterIds,
            'has_header' => true,
            'header' => $analisa
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'analisa_date' => 'required|date',
            // 'shift'        => 'required|integer',
            'area'         => 'nullable|string',
            'hasil_analisa' => 'required|array',
            'hasil_analisa.*.*' => 'nullable|numeric' // array format: point_id => [ parameter_id => value ]
        ]);

        try {
            DB::beginTransaction();

            // Find existing header
            $analisa = WwtpAnalisa::where('analisa_date', $request->analisa_date)->first();

            if (!$analisa) {
                // First time input in that day, validate and require approvals
                $request->validate([
                    'foreman_id'   => 'required|exists:users,id',
                    'supervisor_id' => 'required|exists:users,id',
                ]);

                $analisa = WwtpAnalisa::create([
                    'analisa_date' => $request->analisa_date,
                    'pelaksana_id' => Auth::id(),
                    'foreman_id'   => $request->foreman_id,
                    'supervisor_id' => $request->supervisor_id,
                    'created_by'   => Auth::id(),
                    'status'       => 'submitted',
                ]);

                // Kirim notifikasi pertama kali ke Foreman
                \App\Models\NotificationsModel::create([
                    'user_id'         => $request->foreman_id,
                    'title'           => 'Approval Analisa WWTP',
                    'message'         => 'Data analisa WWTP tanggal ' . $request->analisa_date . ' menunggu persetujuan Anda.',
                    'url'             => url('/wwtp/analisa/approval'),
                    'notifiable_type' => WwtpAnalisa::class,
                    'notifiable_id'   => $analisa->id,
                    'is_read'         => 0,
                ]);
            } else {
                // Header exists. Let's check if Supervisor approved and they are trying to modify already filled parameters
                if ($analisa->status === 'approved_supervisor') {
                    $submittingParamIds = [];
                    foreach ($request->hasil_analisa as $point_id => $parameters) {
                        foreach ($parameters as $parameter_id => $hasil) {
                            if ($hasil !== null && $hasil !== '') {
                                $submittingParamIds[] = $parameter_id;
                            }
                        }
                    }
                    $submittingParamIds = array_unique($submittingParamIds);

                    $alreadyFilledIds = WwtpAnalisaDetail::where('analisa_id', $analisa->id)
                        ->distinct()
                        ->pluck('parameter_id')
                        ->toArray();

                    $intersect = array_intersect($submittingParamIds, $alreadyFilledIds);
                    if (!empty($intersect)) {
                        return response()->json([
                            'message' => 'Data analisa untuk parameter ini sudah disetujui oleh Supervisor dan tidak dapat diubah.'
                        ], 422);
                    }
                }

                // If status is rejected, reset it to submitted and re-notify foreman
                if ($analisa->status === 'rejected') {
                    $analisa->update([
                        'status' => 'submitted',
                        'reject_reason' => null
                    ]);

                    // Send notification to Foreman again
                    if ($analisa->foreman_id) {
                        \App\Models\NotificationsModel::updateOrCreate(
                            [
                                'user_id'         => $analisa->foreman_id,
                                'notifiable_type' => WwtpAnalisa::class,
                                'notifiable_id'   => $analisa->id,
                            ],
                            [
                                'title'           => 'Approval Analisa WWTP',
                                'message'         => 'Data analisa WWTP tanggal ' . $analisa->analisa_date . ' telah diperbarui dan menunggu persetujuan Anda.',
                                'url'             => url('/wwtp/analisa/approval'),
                                'is_read'         => 0,
                            ]
                        );
                    }
                }
            }

            // Update area if provided
            if ($request->filled('area')) {
                $analisa->update(['area' => $request->area]);
            }

            foreach ($request->hasil_analisa as $point_id => $parameters) {
                foreach ($parameters as $parameter_id => $hasil) {
                    if ($hasil !== null && $hasil !== '') {
                        WwtpAnalisaDetail::updateOrCreate(
                            [
                                'analisa_id'   => $analisa->id,
                                'point_id'     => $point_id,
                                'parameter_id' => $parameter_id,
                            ],
                            [
                                'hasil_analisa' => $hasil,
                                'keterangan'    => null
                            ]
                        );
                    }
                }
            }

            DB::commit();

            // Sync to Google Sheets in background after response is sent
            SyncGoogleSheetsJob::dispatch();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data analisa WWTP berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $analisa = WwtpAnalisa::with(['creator', 'pelaksana', 'foreman', 'supervisor', 'details.point', 'details.parameter'])->findOrFail($id);
        return response()->json($analisa);
    }

    public function updateParameterResults(Request $request, $id, $parameterId)
    {
        $analisa = WwtpAnalisa::findOrFail($id);
        WwtpParameter::findOrFail($parameterId);

        if ($analisa->status === 'approved_supervisor') {
            return response()->json([
                'message' => 'Laporan analisa ini sudah disetujui oleh Supervisor dan tidak dapat diubah.'
            ], 422);
        }

        $request->validate([
            'hasil_analisa' => 'required|array',
            'hasil_analisa.*' => 'nullable|numeric'
        ]);

        $hasValue = collect($request->hasil_analisa)->contains(fn($hasil) => $hasil !== null && $hasil !== '');
        if (!$hasValue) {
            return response()->json([
                'message' => 'Minimal satu hasil analisa harus diisi.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            WwtpAnalisaDetail::where('analisa_id', $analisa->id)
                ->where('parameter_id', $parameterId)
                ->delete();

            foreach ($request->hasil_analisa as $pointId => $hasil) {
                if ($hasil !== null && $hasil !== '') {
                    WwtpAnalisaDetail::create([
                        'analisa_id'    => $analisa->id,
                        'point_id'      => $pointId,
                        'parameter_id'  => $parameterId,
                        'hasil_analisa' => $hasil,
                        'keterangan'    => null
                    ]);
                }
            }

            DB::commit();

            // Sync to Google Sheets in background after response is sent
            SyncGoogleSheetsJob::dispatch();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data parameter analisa WWTP berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyParameterResults($id, $parameterId)
    {
        $analisa = WwtpAnalisa::findOrFail($id);
        WwtpParameter::findOrFail($parameterId);

        if ($analisa->status === 'approved_supervisor') {
            return response()->json([
                'message' => 'Laporan analisa ini sudah disetujui oleh Supervisor dan tidak dapat dihapus.'
            ], 422);
        }

        WwtpAnalisaDetail::where('analisa_id', $analisa->id)
            ->where('parameter_id', $parameterId)
            ->delete();

        if (!$analisa->details()->exists()) {
            $analisa->delete();
        }

        // Sync to Google Sheets in background after response is sent
        SyncGoogleSheetsJob::dispatch();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data parameter analisa WWTP berhasil dihapus.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $analisa = WwtpAnalisa::findOrFail($id);

        if ($analisa->status === 'approved_supervisor') {
            return response()->json([
                'message' => 'Laporan analisa ini sudah disetujui oleh Supervisor dan tidak dapat diubah.'
            ], 422);
        }

        $request->validate([
            'analisa_date' => 'required|date',
            'shift'        => 'required|integer',
            'area'         => 'nullable|string',
            'hasil_analisa' => 'required|array',
            'hasil_analisa.*.*' => 'nullable|numeric'
        ]);

        try {
            DB::beginTransaction();

            $exist = WwtpAnalisa::where('analisa_date', $request->analisa_date)
                ->where('shift', $request->shift)
                ->where('id', '!=', $id)
                ->first();

            if ($exist) {
                return response()->json([
                    'message' => 'Data analisa WWTP untuk tanggal dan shift yang sama sudah ada.',
                ], 500);
            }

            $analisa->update([
                'analisa_date' => $request->analisa_date,
                'shift'        => $request->shift,
                'area'         => $request->area,
            ]);

            // Clear existing details and re-insert
            $analisa->details()->delete();

            foreach ($request->hasil_analisa as $point_id => $parameters) {
                foreach ($parameters as $parameter_id => $hasil) {
                    if ($hasil !== null && $hasil !== '') {
                        WwtpAnalisaDetail::create([
                            'analisa_id'    => $analisa->id,
                            'point_id'      => $point_id,
                            'parameter_id'  => $parameter_id,
                            'hasil_analisa' => $hasil,
                            'keterangan'    => null
                        ]);
                    }
                }
            }

            DB::commit();

            // Sync to Google Sheets in background after response is sent
            SyncGoogleSheetsJob::dispatch();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data analisa WWTP berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $analisa = WwtpAnalisa::findOrFail($id);

        if ($analisa->status === 'approved_supervisor') {
            return response()->json([
                'message' => 'Laporan analisa ini sudah disetujui oleh Supervisor dan tidak dapat dihapus.'
            ], 422);
        }

        $analisa->delete(); // Cascades to details

        // Sync to Google Sheets in background after response is sent
        SyncGoogleSheetsJob::dispatch();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data analisa WWTP berhasil dihapus.',
        ]);
    }

    /**
     * Get users for WWTP Analisa approval dropdowns
     */
    public function getUsersForApproval()
    {
        $pelaksana = \App\Models\User::where('departemen', 'engineering')
            ->where('jabatan', 'operator')
            ->get(['id', 'username']);

        $foreman = \App\Models\User::where('departemen', 'engineering')
            ->where('jabatan', 'foreman')
            ->get(['id', 'username']);

        $supervisor = \App\Models\User::where('departemen', 'engineering')
            ->where('jabatan', 'supervisor')
            ->get(['id', 'username']);

        return response()->json([
            'pelaksana' => $pelaksana,
            'foreman'   => $foreman,
            'supervisor' => $supervisor
        ]);
    }

    /**
     * Render the approval view page
     */
    public function approvalView()
    {
        return view('utility.wwtp.approval');
    }

    /**
     * Get list of approvals (pending and history)
     */
    public function getApprovalList(Request $request)
    {
        $tab = $request->input('tab', 'pending');
        $user = Auth::user();
        $jabatan = $user->jabatan;
        $userId = $user->id;

        $query = WwtpAnalisa::with(['creator', 'pelaksana', 'foreman', 'supervisor', 'details.parameter', 'details.point']);

        if ($tab === 'pending') {
            if ($jabatan === 'foreman') {
                $query->where('foreman_id', $userId)
                    ->where('status', 'submitted');
            } elseif ($jabatan === 'supervisor') {
                $query->where('supervisor_id', $userId)
                    ->where('status', 'approved_foreman');
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            if ($jabatan === 'foreman') {
                $query->where('foreman_id', $userId)
                    ->whereIn('status', ['approved_foreman', 'approved_supervisor', 'rejected']);
            } elseif ($jabatan === 'supervisor') {
                $query->where('supervisor_id', $userId)
                    ->whereIn('status', ['approved_supervisor', 'rejected']);
            } else {
                $query->where('created_by', $userId);
            }
        }

        $data = $query->orderBy('analisa_date', 'desc')->get();
        return response()->json($data);
    }

    /**
     * Approve a daily WWTP analysis sheet
     */
    public function approve(Request $request, $id)
    {
        $analisa = WwtpAnalisa::findOrFail($id);
        $user = Auth::user();
        $jabatan = $user->jabatan;
        $userId = $user->id;

        if ($jabatan === 'foreman') {
            if ((int)$analisa->foreman_id !== $userId) {
                return response()->json(['message' => 'Anda tidak berwenang menyetujui laporan ini.'], 403);
            }
            if ($analisa->status !== 'submitted') {
                return response()->json(['message' => 'Status laporan tidak valid untuk disetujui Foreman.'], 422);
            }

            $analisa->update([
                'status' => 'approved_foreman',
                'approved_foreman_at' => now(),
                'reject_reason' => null
            ]);

            \App\Models\NotificationsModel::where('notifiable_type', WwtpAnalisa::class)
                ->where('notifiable_id', $analisa->id)
                ->where('user_id', $userId)
                ->delete();

            if ($analisa->supervisor_id) {
                \App\Models\NotificationsModel::create([
                    'user_id' => $analisa->supervisor_id,
                    'title' => 'Approval Analisa WWTP',
                    'message' => 'Data analisa WWTP tanggal ' . $analisa->analisa_date . ' telah disetujui Foreman dan menunggu persetujuan Anda.',
                    'url' => url('/wwtp/analisa/approval'),
                    'notifiable_type' => WwtpAnalisa::class,
                    'notifiable_id' => $analisa->id,
                    'is_read' => 0,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data analisa berhasil disetujui oleh Foreman.'
            ]);
        } elseif ($jabatan === 'supervisor') {
            if ((int)$analisa->supervisor_id !== $userId) {
                return response()->json(['message' => 'Anda tidak berwenang menyetujui laporan ini.'], 403);
            }
            if ($analisa->status !== 'approved_foreman') {
                return response()->json(['message' => 'Laporan harus disetujui oleh Foreman terlebih dahulu.'], 422);
            }

            $analisa->update([
                'status' => 'approved_supervisor',
                'approved_supervisor_at' => now(),
                'reject_reason' => null
            ]);

            \App\Models\NotificationsModel::where('notifiable_type', WwtpAnalisa::class)
                ->where('notifiable_id', $analisa->id)
                ->where('user_id', $userId)
                ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data analisa berhasil disetujui oleh Supervisor (Selesai).'
            ]);
        }

        return response()->json(['message' => 'Role Anda tidak memiliki otoritas approval.'], 403);
    }

    /**
     * Reject a daily WWTP analysis sheet
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $analisa = WwtpAnalisa::findOrFail($id);
        $user = Auth::user();
        $jabatan = $user->jabatan;
        $userId = $user->id;

        $isForeman = ($jabatan === 'foreman' && (int)$analisa->foreman_id === $userId && $analisa->status === 'submitted');
        $isSupervisor = ($jabatan === 'supervisor' && (int)$analisa->supervisor_id === $userId && $analisa->status === 'approved_foreman');

        if (!$isForeman && !$isSupervisor) {
            return response()->json(['message' => 'Anda tidak memiliki wewenang untuk menolak laporan ini pada tahap ini.'], 403);
        }

        $analisa->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason
        ]);

        \App\Models\NotificationsModel::where('notifiable_type', WwtpAnalisa::class)
            ->where('notifiable_id', $analisa->id)
            ->where('user_id', $userId)
            ->delete();

        if ($analisa->created_by) {
            \App\Models\NotificationsModel::create([
                'user_id' => $analisa->created_by,
                'title' => 'Laporan Analisa WWTP Ditolak',
                'message' => 'Data analisa WWTP tanggal ' . $analisa->analisa_date . ' ditolak. Alasan: ' . $request->reason,
                'url' => url('/wwtp/data_analisa'),
                'notifiable_type' => WwtpAnalisa::class,
                'notifiable_id' => $analisa->id,
                'is_read' => 0,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan analisa berhasil ditolak.'
        ]);
    }
}
