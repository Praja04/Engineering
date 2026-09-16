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
     */
    public function uploadExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_excel' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ], [
            'file_excel.required' => 'File Excel wajib diunggah.',
            'file_excel.mimes'    => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'file_excel.max'      => 'Ukuran file maksimal 10MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $rows = IOFactory::load($request->file('file_excel')->getRealPath())
                ->getActiveSheet()
                ->toArray(null, true, true, true);

            $dataStart = 5;
            $inserted  = 0;
            $updated   = 0;
            $skipped   = 0;
            $errors    = [];

            // Pre-fetch warehouse catalog for fallback if needed
            $warehouseMap = [];
            try {
                $whRes = Http::timeout(4)->get('http://10.11.10.130:8087/api/wsp/barang');
                if ($whRes->successful()) {
                    $whJson = $whRes->json();
                    if (!empty($whJson['data'])) {
                        foreach ($whJson['data'] as $wb) {
                            if (!empty($wb['mid_barang'])) {
                                $warehouseMap[trim($wb['mid_barang'])] = [
                                    'nama' => $wb['nama_barang'] ?? '',
                                    'uom'  => $wb['uom'] ?? ''
                                ];
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // If warehouse API is temporarily unreachable, proceed with provided excel data
            }

            DB::transaction(function () use ($rows, $dataStart, $warehouseMap, &$inserted, &$updated, &$skipped, &$errors) {
                foreach ($rows as $rowNum => $row) {
                    if ($rowNum < $dataStart) continue;

                    $midRaw       = trim((string) ($row['B'] ?? ''));
                    $deskripsiRaw = trim((string) ($row['C'] ?? ''));
                    $uomRaw       = strtoupper(trim((string) ($row['D'] ?? '')));
                    $hargaRaw     = trim((string) ($row['E'] ?? '0'));
                    $kategoriRaw  = trim((string) ($row['F'] ?? ''));
                    $ketRaw       = trim((string) ($row['G'] ?? ''));

                    // Skip totally empty rows
                    if ($midRaw === '' && $deskripsiRaw === '' && $hargaRaw === '') {
                        continue;
                    }

                    // Auto-resolve desc & uom from warehouse API if missing
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

                    // Clean and parse numeric price
                    $cleanHarga = preg_replace('/[^0-9.]/', '', str_replace(',', '.', $hargaRaw));
                    $hargaVal = is_numeric($cleanHarga) ? floatval($cleanHarga) : 0;

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

            $totalProcessed = $inserted + $updated;
            $msg = "Import selesai! {$inserted} material baru ditambahkan, {$updated} material diperbarui.";
            if ($skipped > 0) {
                $msg .= " ({$skipped} baris dilewati)";
            }

            return response()->json([
                'status'   => true,
                'message'  => $msg,
                'inserted' => $inserted,
                'updated'  => $updated,
                'skipped'  => $skipped,
                'errors'   => $errors,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal memproses file Excel: ' . $e->getMessage()
            ], 500);
        }
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
