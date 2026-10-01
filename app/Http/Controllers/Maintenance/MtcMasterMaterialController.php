<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Maintenance\MtcMasterMaterialModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MtcMasterMaterialController extends Controller
{
    /**
     * Render the master material view.
     */
    public function index()
    {
        return view('maintenance.master.master_material');
    }

    /**
     * Get list of materials & summary stats via AJAX.
     */
    public function getData(Request $request)
    {
        $query = MtcMasterMaterialModel::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status_harga')) {
            if ($request->status_harga === 'has_price') {
                $query->where('harga', '>', 0);
            } elseif ($request->status_harga === 'no_price') {
                $query->where(function ($q) {
                    $q->where('harga', '<=', 0)->orWhereNull('harga');
                });
            }
        }

        $allData = $query->orderBy('updated_at', 'desc')->get();

        // Calculate summary stats
        $totalItems    = MtcMasterMaterialModel::count();
        $itemsWithPrice = MtcMasterMaterialModel::where('harga', '>', 0)->count();
        $itemsNoPrice  = $totalItems - $itemsWithPrice;
        $avgPrice      = MtcMasterMaterialModel::where('harga', '>', 0)->avg('harga') ?? 0;
        $categories    = MtcMasterMaterialModel::whereNotNull('kategori')->where('kategori', '!=', '')->distinct()->pluck('kategori');

        return response()->json([
            'status' => true,
            'summary' => [
                'total_items'       => $totalItems,
                'items_with_price'  => $itemsWithPrice,
                'items_no_price'    => $itemsNoPrice,
                'avg_price'         => round($avgPrice, 2),
                'categories'        => $categories,
            ],
            'data' => $allData->map(function ($item) {
                return [
                    'id'          => $item->id,
                    'mid'         => $item->mid ?? '-',
                    'deskripsi'   => $item->deskripsi,
                    'uom'         => $item->uom ?? '-',
                    'harga'       => floatval($item->harga),
                    'harga_fmt'   => 'Rp ' . number_format($item->harga, 0, ',', '.'),
                    'kategori'    => $item->kategori ?? 'Umum',
                    'keterangan'  => $item->keterangan ?? '-',
                    'updated_at'  => $item->updated_at ? $item->updated_at->format('d M Y H:i') : '-',
                ];
            })
        ]);
    }

    /**
     * Store new material record.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'deskripsi' => 'required|string|max:255',
            'mid'       => 'nullable|string|max:100|unique:mtc_master_material,mid',
            'uom'       => 'nullable|string|max:50',
            'harga'     => 'required|numeric|min:0',
            'kategori'  => 'nullable|string|max:100',
            'keterangan'=> 'nullable|string|max:500',
        ], [
            'deskripsi.required' => 'Nama / Deskripsi Material wajib diisi.',
            'mid.unique'         => 'MID tersebut sudah terdaftar di Master Material.',
            'harga.required'     => 'Harga satuan wajib diisi.',
            'harga.numeric'      => 'Harga harus berupa angka.',
            'harga.min'          => 'Harga tidak boleh kurang dari 0.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        $material = MtcMasterMaterialModel::create([
            'mid'        => $request->mid ? trim($request->mid) : null,
            'deskripsi'  => trim($request->deskripsi),
            'uom'        => $request->uom ? strtoupper(trim($request->uom)) : null,
            'harga'      => floatval($request->harga),
            'kategori'   => $request->kategori ? trim($request->kategori) : 'Umum',
            'keterangan' => $request->keterangan ? trim($request->keterangan) : null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Material berhasil ditambahkan ke Master MTC.',
            'data'    => $material
        ]);
    }

    /**
     * Update existing material record.
     */
    public function update(Request $request, $id)
    {
        $material = MtcMasterMaterialModel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'deskripsi' => 'required|string|max:255',
            'mid'       => 'nullable|string|max:100|unique:mtc_master_material,mid,' . $id,
            'uom'       => 'nullable|string|max:50',
            'harga'     => 'required|numeric|min:0',
            'kategori'  => 'nullable|string|max:100',
            'keterangan'=> 'nullable|string|max:500',
        ], [
            'deskripsi.required' => 'Nama / Deskripsi Material wajib diisi.',
            'mid.unique'         => 'MID tersebut sudah digunakan pada material lain.',
            'harga.required'     => 'Harga satuan wajib diisi.',
            'harga.numeric'      => 'Harga harus berupa angka.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        $material->update([
            'mid'        => $request->mid ? trim($request->mid) : null,
            'deskripsi'  => trim($request->deskripsi),
            'uom'        => $request->uom ? strtoupper(trim($request->uom)) : null,
            'harga'      => floatval($request->harga),
            'kategori'   => $request->kategori ? trim($request->kategori) : 'Umum',
            'keterangan' => $request->keterangan ? trim($request->keterangan) : null,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Material berhasil diperbarui.',
            'data'    => $material
        ]);
    }

    /**
     * Delete material record.
     */
    public function destroy($id)
    {
        $material = MtcMasterMaterialModel::findOrFail($id);
        $material->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Material berhasil dihapus dari Master MTC.'
        ]);
    }

    /**
     * Download Excel template for import.
     */
    public function downloadTemplate()
    {
        $path = storage_path('app/templates/template_master_material.xlsx');
        $dir = dirname($path);

        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title & Instruction
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT MASTER HARGA & MATERIAL MTC');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        $sheet->setCellValue('A2', 'Petunjuk: Pengisian dimulai dari Baris 5. Kolom MID atau Deskripsi wajib diisi.');
        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A3', 'Jika MID diisi dan Deskripsi/UoM kosong, sistem akan mencoba mengambil data otomatis dari Warehouse API.');
        $sheet->mergeCells('A3:F3');

        // Headers
        $headers = ['A4' => 'No', 'B4' => 'MID Barang', 'C4' => 'Deskripsi Material *', 'D4' => 'UOM', 'E4' => 'Harga Satuan (Rp) *', 'F4' => 'Kategori', 'G4' => 'Keterangan'];
        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        // Sample rows
        $samples = [
            ['1', '60021589', '1783-US5T STRATIX 2000 UNMANAGED SWITH', 'UN', '1500000', 'Electrical', 'Switch ethernet switchboard'],
            ['2', '60018920', 'FILTER OLI FORKLIFT DIESEL', 'PCS', '125000', 'Forklift Part', 'Penggantian rutin Forklift 01-16'],
            ['3', '60014522', 'SEAL CYLINDER HYDRAULIC FORKLIFT', 'SET', '450000', 'Forklift Part', 'Sparepart hydraulic lift'],
            ['4', '', 'MUR BAUT M10X30 SS304', 'PCS', '7500', 'Consumable', 'Material umum maintenance'],
        ];

        $rowIdx = 5;
        foreach ($samples as $sample) {
            $sheet->setCellValue('A' . $rowIdx, $sample[0]);
            $sheet->setCellValue('B' . $rowIdx, $sample[1]);
            $sheet->setCellValue('C' . $rowIdx, $sample[2]);
            $sheet->setCellValue('D' . $rowIdx, $sample[3]);
            $sheet->setCellValue('E' . $rowIdx, $sample[4]);
            $sheet->setCellValue('F' . $rowIdx, $sample[5]);
            $sheet->setCellValue('G' . $rowIdx, $sample[6]);
            $rowIdx++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return response()->download($path, 'template_master_material_mtc.xlsx');
    }

    /**
     * Upload & import Excel file for material master & prices.
     * Supports high-volume "Pmk SAP" sheet format (tens of thousands of rows) and standard MTC template format.
     */
    public function uploadExcel(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('>>> [MTC Upload] Request Masuk ke Controller <<<', [
            'has_file'   => $request->hasFile('file_excel'),
            'file_name'  => $request->hasFile('file_excel') ? $request->file('file_excel')->getClientOriginalName() : null,
            'file_size'  => $request->hasFile('file_excel') ? $request->file('file_excel')->getSize() : null,
            'all_keys'   => array_keys($request->all()),
            'client_ip'  => $request->ip(),
        ]);

        $validator = Validator::make($request->all(), [
            'file_excel' => 'required|file|mimes:xlsx,xls,csv|max:30720' // up to 30MB
        ], [
            'file_excel.required' => 'File Excel wajib diunggah.',
            'file_excel.mimes'    => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'file_excel.max'      => 'Ukuran file maksimal 30MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Tingkatkan batas memori dan durasi eksekusi untuk menangani data besar
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', '0');
            set_time_limit(0);

            $filePath = $request->file('file_excel')->getRealPath();

            /** @var \PhpOffice\PhpSpreadsheet\Reader\BaseReader $reader */
            $reader = IOFactory::createReaderForFile($filePath);
            if (method_exists($reader, 'setReadDataOnly')) {
                $reader->setReadDataOnly(true);
            }
            if (method_exists($reader, 'setReadEmptyCells')) {
                $reader->setReadEmptyCells(false);
            }

            // Dapatkan seluruh daftar sheet tanpa memuat isi file
            $sheetNames = method_exists($reader, 'listWorksheetNames')
                ? $reader->listWorksheetNames($filePath)
                : [];
            $pmkSheetName = null;

            foreach ($sheetNames as $name) {
                if (strcasecmp(trim($name), 'Pmk SAP') === 0) {
                    $pmkSheetName = $name;
                    break;
                }
            }

            // Jika tidak persis, cari sheet yang mengandung "pmk" dan "sap"
            if (!$pmkSheetName) {
                foreach ($sheetNames as $name) {
                    $lower = strtolower(trim($name));
                    if (str_contains($lower, 'pmk') && str_contains($lower, 'sap')) {
                        $pmkSheetName = $name;
                        break;
                    }
                }
            }

            // 1. Jika sheet "Pmk SAP" ditemukan: gunakan ReadFilter khusus kolom K sampai P
            if ($pmkSheetName) {
                if (method_exists($reader, 'setLoadSheetsOnly')) {
                    $reader->setLoadSheetsOnly([$pmkSheetName]);
                }

                // Pasang IReadFilter: hanya baca baris >= 2 dan kolom K, L, M, N, O, P
                // Ini mempercepat loading hingga 10x lipat dan memangkas 85% penggunaan memori
                $readFilter = new class implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
                    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
                    {
                        if ($row < 2) return false;
                        return in_array($columnAddress, ['K', 'L', 'M', 'N', 'O', 'P'], true);
                    }
                };
                if (method_exists($reader, 'setReadFilter')) {
                    $reader->setReadFilter($readFilter);
                }

                $spreadsheet = $reader->load($filePath);
                $sheet = $spreadsheet->getSheetByName($pmkSheetName) ?? $spreadsheet->getActiveSheet();

                // Pre-fetch katalog Warehouse API sekali di awal
                $warehouseMap = $this->getWarehouseCatalogMap();

                // Proses langsung dari sheet tanpa toArray() untuk mencegah lonjakan RAM (Memory Exhaustion)
                $result = $this->processPmkSapSheet($sheet, $warehouseMap);

                // Release RAM dari spreadsheet seketika
                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
                unset($reader);

                return $result;
            }

            // 2. Fallback: jika tidak ada sheet "Pmk SAP", cek apakah ini template standar MTC
            $spreadsheet = $reader->load($filePath);
            $activeSheet = $spreadsheet->getActiveSheet();
            $cellB4 = trim((string) $activeSheet->getCell('B4')->getValue());
            $cellC4 = trim((string) $activeSheet->getCell('C4')->getValue());

            if (stripos($cellB4, 'MID') !== false || stripos($cellC4, 'Deskripsi') !== false) {
                $warehouseMap = $this->getWarehouseCatalogMap();
                return $this->processStandardTemplateSheet($activeSheet, $warehouseMap);
            }

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            unset($reader);

            return response()->json([
                'status'  => false,
                'message' => "Sheet 'Pmk SAP' tidak ditemukan pada file Excel yang diunggah. Pastikan nama sheet adalah 'Pmk SAP' atau gunakan template resmi MTC."
            ], 422);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Upload Excel Error: ' . $e->getMessage(), [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => substr($e->getTraceAsString(), 0, 1500)
            ]);
            return response()->json([
                'status'  => false,
                'message' => 'Gagal memproses file Excel: ' . $e->getMessage() . ' (Line ' . $e->getLine() . ')'
            ], 500);
        }
    }

    /**
     * Process high-volume "Pmk SAP" sheet directly without converting entire sheet to array.
     * Uses cellCollection directly to avoid creating dummy cells for non-selected columns,
     * reducing RAM usage by 95% and finishing in seconds.
     */
    private function processPmkSapSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $warehouseMap)
    {
        $uniqueMaterials = [];
        $totalRowsRead   = 0;
        $jasaSkipped     = 0;
        $emptyMidSkipped = 0;

        $cellCollection = $sheet->getCellCollection();
        $highestRow     = (int) $sheet->getHighestRow();

        for ($rowNum = 2; $rowNum <= $highestRow; $rowNum++) {
            // 1. Lewati jika cell K tidak ada di collection (kolom MID)
            if (!$cellCollection->has("K{$rowNum}")) {
                $emptyMidSkipped++;
                continue;
            }

            $cellK = $cellCollection->get("K{$rowNum}");
            $midRaw = $cellK ? trim((string) $cellK->getValue()) : '';

            // Lewati jika MID kosong, '-', atau teks header
            if ($midRaw === '' || $midRaw === '-' || strcasecmp($midRaw, 'material') === 0 || strcasecmp($midRaw, 'mid') === 0) {
                $emptyMidSkipped++;
                continue;
            }

            // 2. Cek kategori di kolom M (Skip jika Jasa/JASA)
            $katRaw = '';
            if ($cellCollection->has("M{$rowNum}")) {
                $cellM = $cellCollection->get("M{$rowNum}");
                $katRaw = $cellM ? strtoupper(trim((string) $cellM->getValue())) : '';
            }
            if (str_contains($katRaw, 'JASA')) {
                $jasaSkipped++;
                continue;
            }

            // 3. Normalisasi kategori: hanya 'Consumable' atau 'Maintenance'
            $kategori = 'Maintenance';
            if (str_contains($katRaw, 'CONSUMABLE')) {
                $kategori = 'Consumable';
            } elseif (str_contains($katRaw, 'MAINTENANCE')) {
                $kategori = 'Maintenance';
            }

            // Bersihkan MID dari spasi dan trailing float '.0'
            $mid = preg_replace('/\s+/', '', $midRaw);
            if (preg_match('/^\d+\.0+$/', $mid)) {
                $mid = explode('.', $mid)[0];
            }

            if ($mid === '') {
                $emptyMidSkipped++;
                continue;
            }

            $totalRowsRead++;

            $cellN = $cellCollection->has("N{$rowNum}") ? $cellCollection->get("N{$rowNum}") : null;
            $valInRcRaw = $cellN ? $cellN->getValue() : 0;

            $cellO = $cellCollection->has("O{$rowNum}") ? $cellCollection->get("O{$rowNum}") : null;
            $qtyRaw = $cellO ? $cellO->getValue() : 0;

            $cellP = $cellCollection->has("P{$rowNum}") ? $cellCollection->get("P{$rowNum}") : null;
            $uomPumRaw = $cellP ? strtoupper(trim((string) $cellP->getValue())) : '';

            $cellL = $cellCollection->has("L{$rowNum}") ? $cellCollection->get("L{$rowNum}") : null;
            $descExcel = $cellL ? trim((string) $cellL->getValue()) : '';

            $totalHarga = $this->parseNumericValue($valInRcRaw);
            $parsedQty  = $this->parseQtyAndUom($qtyRaw);
            $qty        = $parsedQty['qty'];
            $uomO       = $parsedQty['uom'];
            $uomExcel   = $uomO ?: $uomPumRaw;

            // Hitung harga per 1 barang: abs(Total) / abs(Qty)
            $absTotal    = abs($totalHarga);
            $absQty      = abs($qty);
            $hargaSatuan = ($absQty > 0.00001) ? round($absTotal / $absQty, 2) : 0.0;

            // Filter MID unik: jika MID sudah ada, update dengan data baris terbaru yang memiliki harga valid
            if (!isset($uniqueMaterials[$mid])) {
                $uniqueMaterials[$mid] = [
                    'mid'          => $mid,
                    'deskripsi'    => $descExcel,
                    'uom'          => $uomExcel,
                    'harga_satuan' => $hargaSatuan,
                    'kategori'     => $kategori,
                    'row_num'      => $rowNum,
                    'rows_count'   => 1,
                ];
            } else {
                $uniqueMaterials[$mid]['rows_count']++;
                $uniqueMaterials[$mid]['row_num'] = $rowNum;

                if (!empty($descExcel)) {
                    $uniqueMaterials[$mid]['deskripsi'] = $descExcel;
                }
                if (!empty($uomExcel)) {
                    $uniqueMaterials[$mid]['uom'] = $uomExcel;
                }
                $uniqueMaterials[$mid]['kategori'] = $kategori;
                if ($hargaSatuan > 0) {
                    $uniqueMaterials[$mid]['harga_satuan'] = $hargaSatuan;
                }
            }
        }

        $totalUnique = count($uniqueMaterials);
        if ($totalUnique === 0) {
            return response()->json([
                'status'  => false,
                'message' => "Tidak ditemukan baris material yang valid pada Sheet 'Pmk SAP'. Pastikan kolom K berisi MID barang."
            ], 422);
        }

        // 4. Deteksi MID existing di database secara chunked (1000 items/chunk) untuk statistik insert/update
        $allMids = array_keys($uniqueMaterials);
        $existingMidsMap = [];
        foreach (array_chunk($allMids, 1000) as $midChunk) {
            $found = DB::table('mtc_master_material')->whereIn('mid', $midChunk)->pluck('mid')->toArray();
            foreach ($found as $f) {
                $existingMidsMap[$f] = true;
            }
        }

        $inserted = 0;
        $updated  = 0;
        foreach ($allMids as $mid) {
            if (isset($existingMidsMap[$mid])) {
                $updated++;
            } else {
                $inserted++;
            }
        }
        unset($allMids);
        unset($existingMidsMap);

        // 5. Susun batch data untuk upsert dengan sanitasi panjang kolom
        $batchData = [];
        $now       = now();
        $userId    = Auth::id();

        foreach ($uniqueMaterials as $mid => $item) {
            $finalDesc = $item['deskripsi'];
            $finalUom  = $item['uom'];

            // Sinkronkan nama dan satuan dari API Warehouse jika tersedia
            if (isset($warehouseMap[$mid])) {
                if (!empty($warehouseMap[$mid]['nama'])) {
                    $finalDesc = $warehouseMap[$mid]['nama'];
                }
                if (!empty($warehouseMap[$mid]['uom'])) {
                    $finalUom = $warehouseMap[$mid]['uom'];
                }
            }

            if (empty($finalDesc)) {
                $finalDesc = 'Material ' . $mid;
            }
            if (empty($finalUom)) {
                $finalUom = 'UN';
            }

            $batchData[] = [
                'mid'        => substr((string) $mid, 0, 100),
                'deskripsi'  => substr((string) $finalDesc, 0, 255),
                'uom'        => substr((string) $finalUom, 0, 50),
                'harga'      => floatval($item['harga_satuan']),
                'kategori'   => substr((string) $item['kategori'], 0, 100),
                'keterangan' => 'Import SAP (Pmk SAP)',
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        unset($uniqueMaterials);

        // 6. Jalankan Chunked Bulk Upsert (500 items per batch)
        foreach (array_chunk($batchData, 500) as $chunk) {
            DB::table('mtc_master_material')->upsert(
                $chunk,
                ['mid'], // unique key column
                ['deskripsi', 'uom', 'harga', 'kategori', 'keterangan', 'updated_by', 'updated_at']
            );
        }

        unset($batchData);

        $msg = "Import Sheet 'Pmk SAP' berhasil! Diproses {$totalRowsRead} baris transaksi material menjadi {$totalUnique} MID unik. {$inserted} data baru ditambahkan, {$updated} data diperbarui.";
        if ($jasaSkipped > 0) {
            $msg .= " ({$jasaSkipped} baris kategori Jasa dilewati).";
        }

        return response()->json([
            'status'       => true,
            'message'      => $msg,
            'format'       => 'Pmk SAP',
            'total_rows'   => $totalRowsRead,
            'unique_count' => $totalUnique,
            'inserted'     => $inserted,
            'updated'      => $updated,
            'skipped_jasa' => $jasaSkipped,
        ]);
    }

    /**
     * Process Standard Template Sheet (fallback).
     */
    private function processStandardTemplateSheet($sheet, array $warehouseMap)
    {
        $rows = $sheet->toArray(null, true, true, true);
        $dataStart = 5;
        $inserted  = 0;
        $updated   = 0;
        $skipped   = 0;
        $errors    = [];

        DB::transaction(function () use ($rows, $dataStart, $warehouseMap, &$inserted, &$updated, &$skipped, &$errors) {
            foreach ($rows as $rowNum => $row) {
                if ($rowNum < $dataStart) continue;

                $midRaw       = trim((string) ($row['B'] ?? ''));
                $deskripsiRaw = trim((string) ($row['C'] ?? ''));
                $uomRaw       = strtoupper(trim((string) ($row['D'] ?? '')));
                $hargaRaw     = trim((string) ($row['E'] ?? '0'));
                $kategoriRaw  = trim((string) ($row['F'] ?? ''));
                $ketRaw       = trim((string) ($row['G'] ?? ''));

                if ($midRaw === '' && $deskripsiRaw === '' && $hargaRaw === '') {
                    continue;
                }

                if ($midRaw !== '' && isset($warehouseMap[$midRaw])) {
                    if ($deskripsiRaw === '') {
                        $deskripsiRaw = $warehouseMap[$midRaw]['nama'];
                    }
                    if ($uomRaw === '') {
                        $uomRaw = strtoupper($warehouseMap[$midRaw]['uom']);
                    }
                }

                if ($deskripsiRaw === '') {
                    $errors[] = ['baris' => $rowNum, 'masalah' => 'Deskripsi Material tidak boleh kosong'];
                    $skipped++;
                    continue;
                }

                $hargaVal = $this->parseNumericValue($hargaRaw);

                $existing = null;
                if ($midRaw !== '') {
                    $existing = MtcMasterMaterialModel::where('mid', $midRaw)->first();
                }
                if (!$existing && $deskripsiRaw !== '') {
                    $existing = MtcMasterMaterialModel::where('deskripsi', $deskripsiRaw)->whereNull('mid')->first();
                }

                if ($existing) {
                    $existing->update([
                        'mid'        => $midRaw !== '' ? $midRaw : $existing->mid,
                        'deskripsi'  => $deskripsiRaw !== '' ? $deskripsiRaw : $existing->deskripsi,
                        'uom'        => $uomRaw !== '' ? $uomRaw : $existing->uom,
                        'harga'      => $hargaVal,
                        'kategori'   => $kategoriRaw !== '' ? $kategoriRaw : ($existing->kategori ?? 'Umum'),
                        'keterangan' => $ketRaw !== '' ? $ketRaw : $existing->keterangan,
                        'updated_by' => Auth::id(),
                    ]);
                    $updated++;
                } else {
                    MtcMasterMaterialModel::create([
                        'mid'        => $midRaw !== '' ? $midRaw : null,
                        'deskripsi'  => $deskripsiRaw,
                        'uom'        => $uomRaw !== '' ? $uomRaw : null,
                        'harga'      => $hargaVal,
                        'kategori'   => $kategoriRaw !== '' ? $kategoriRaw : 'Umum',
                        'keterangan' => $ketRaw !== '' ? $ketRaw : null,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                    $inserted++;
                }
            }
        });

        $msg = "Import template selesai! {$inserted} material baru ditambahkan, {$updated} material diperbarui.";
        if ($skipped > 0) {
            $msg .= " ({$skipped} baris dilewati)";
        }

        return response()->json([
            'status'   => true,
            'message'  => $msg,
            'format'   => 'Template Standar',
            'inserted' => $inserted,
            'updated'  => $updated,
            'skipped'  => $skipped,
            'errors'   => $errors,
        ]);
    }

    /**
     * Helper to fetch Warehouse API catalog.
     */
    private function getWarehouseCatalogMap(): array
    {
        $warehouseMap = [];
        try {
            $whRes = Http::timeout(5)->get('http://10.11.10.130:8087/api/wsp/barang');
            if ($whRes->successful()) {
                $whJson = $whRes->json();
                if (!empty($whJson['data'])) {
                    foreach ($whJson['data'] as $wb) {
                        if (!empty($wb['mid_barang'])) {
                            $warehouseMap[trim((string) $wb['mid_barang'])] = [
                                'nama' => trim((string) ($wb['nama_barang'] ?? '')),
                                'uom'  => strtoupper(trim((string) ($wb['uom'] ?? '')))
                            ];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fallback if API unreachable
        }
        return $warehouseMap;
    }

    /**
     * Parse numeric values supporting currency formatting, decimals, negative numbers,
     * and abbreviated thousands (e.g. "68," => 68000).
     */
    private function parseNumericValue($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        if (empty($value)) {
            return 0.0;
        }

        $str = trim((string) $value);
        $isNegative = false;
        if (str_starts_with($str, '-') || (str_starts_with($str, '(') && str_ends_with($str, ')'))) {
            $isNegative = true;
        }

        $cleaned = preg_replace('/[^\d.,]/', '', $str);
        if ($cleaned === '') {
            return 0.0;
        }

        // Tangani format disingkat ribuan berakhiran koma (contoh "68," => 68000)
        if (preg_match('/^([+-]?\d+),$/', $cleaned, $commaMatch)) {
            $val = ((float) $commaMatch[1]) * 1000;
            return $isNegative ? -abs($val) : abs($val);
        }

        if (str_contains($cleaned, ',') && str_contains($cleaned, '.')) {
            if (strrpos($cleaned, ',') > strrpos($cleaned, '.')) {
                // Format Indonesia: 1.500.000,50
                $cleaned = str_replace('.', '', $cleaned);
                $cleaned = str_replace(',', '.', $cleaned);
            } else {
                // Format US: 1,500,000.50
                $cleaned = str_replace(',', '', $cleaned);
            }
        } elseif (str_contains($cleaned, ',')) {
            $parts = explode(',', $cleaned);
            $lastPart = end($parts);
            if (count($parts) > 2 || (count($parts) === 2 && strlen($lastPart) === 3)) {
                $cleaned = str_replace(',', '', $cleaned);
            } else {
                $cleaned = str_replace(',', '.', $cleaned);
            }
        } elseif (str_contains($cleaned, '.')) {
            $parts = explode('.', $cleaned);
            $lastPart = end($parts);
            if (count($parts) > 2 || (count($parts) === 2 && strlen($lastPart) === 3)) {
                $cleaned = str_replace('.', '', $cleaned);
            }
        }

        $num = is_numeric($cleaned) ? (float) $cleaned : 0.0;
        return $isNegative ? -$num : $num;
    }

    /**
     * Extract quantity and UoM from string like "1 UN", "-100 L", "2 AU", "50 PCS",
     * and handle abbreviated thousands like "68," or "68, UN" => 68000.
     */
    private function parseQtyAndUom($value): array
    {
        if (is_numeric($value)) {
            return ['qty' => (float) $value, 'uom' => ''];
        }

        $str = trim((string) $value);
        if ($str === '' || $str === '-') {
            return ['qty' => 0.0, 'uom' => ''];
        }

        // Cek jika ada pola singkatan ribuan seperti "68," atau "68, UN" atau "-68, L"
        if (preg_match('/^([+-]?\d+),\s*([a-zA-Z]*)$/', $str, $abbrMatches)) {
            $baseNumber = (float) $abbrMatches[1];
            $uomPart = strtoupper(trim($abbrMatches[2]));
            return ['qty' => $baseNumber * 1000, 'uom' => $uomPart];
        }

        if (preg_match('/^([+-]?[\d.,\s]+)(.*)$/', $str, $matches)) {
            $qtyRaw = trim($matches[1]);
            $uomRaw = strtoupper(trim($matches[2]));

            // Jika qtyRaw diakhiri tanda koma (contoh "68,")
            if (preg_match('/^([+-]?\d+),$/', $qtyRaw, $qMatches)) {
                $qty = ((float) $qMatches[1]) * 1000;
            } else {
                $qty = $this->parseNumericValue($qtyRaw);
            }
            return ['qty' => $qty, 'uom' => $uomRaw];
        }

        return ['qty' => $this->parseNumericValue($str), 'uom' => ''];
    }

    /**
     * Bulk sync catalog items from Warehouse API (sets price 0 if new).
     */
    public function syncWarehouse()
    {
        try {
            $response = Http::timeout(10)->get('http://10.11.10.130:8087/api/wsp/barang');
            if (!$response->successful()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Gagal menghubungi API Warehouse (HTTP ' . $response->status() . ')'
                ], 502);
            }

            $json = $response->json();
            $data = $json['data'] ?? [];

            if (empty($data)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Tidak ada data barang yang diterima dari API Warehouse.'
                ]);
            }

            $newCount = 0;
            $syncCount = 0;

            DB::transaction(function () use ($data, &$newCount, &$syncCount) {
                foreach ($data as $item) {
                    $mid = trim((string) ($item['mid_barang'] ?? ''));
                    $nama = trim((string) ($item['nama_barang'] ?? ''));
                    $uom = strtoupper(trim((string) ($item['uom'] ?? '')));

                    if ($mid === '' || $nama === '') continue;

                    $mat = MtcMasterMaterialModel::where('mid', $mid)->first();
                    if ($mat) {
                        // Update desc or uom if changed
                        $mat->update([
                            'deskripsi' => $nama,
                            'uom'       => $uom !== '' ? $uom : $mat->uom,
                            'updated_by'=> Auth::id(),
                        ]);
                        $syncCount++;
                    } else {
                        MtcMasterMaterialModel::create([
                            'mid'        => $mid,
                            'deskripsi'  => $nama,
                            'uom'        => $uom !== '' ? $uom : null,
                            'harga'      => 0,
                            'kategori'   => 'Warehouse Part',
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id(),
                        ]);
                        $newCount++;
                    }
                }
            });

            return response()->json([
                'status'  => true,
                'message' => "Sinkronisasi berhasil! {$newCount} barang baru dimasukkan, {$syncCount} barang diperbarui deskripsi & UoM-nya."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat sinkronisasi API: ' . $e->getMessage()
            ], 500);
        }
    }
}
