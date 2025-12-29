<?php

namespace App\Http\Controllers\Utility;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Utility\PemakaianChemicalModel;
use App\Models\Utility\ChemicalType;
use App\Models\Utility\ChemicalArea;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\DB;

class ChemicalController extends Controller
{

    public function getTypesByArea($id)
    {
        $chemicals = ChemicalType::with('area')
            ->where('chemical_area_id', $id)
            ->get();

        if ($chemicals->isEmpty()) {
            return response()->json(['message' => 'No chemicals found for this area'], 404);
        }

        // Ubah ke format yang rapi
        $data = $chemicals->map(function ($chemical) {
            return [
                'id' => $chemical->id,
                'chemical_area_id' => $chemical->chemical_area_id,
                'nama_chemical' => trim($chemical->nama_chemical),
                'satuan' => $chemical->satuan,
                'nama_area' => $chemical->area->nama_area ?? '-',
                'created_at' => $chemical->created_at,
                'updated_at' => $chemical->updated_at,
            ];
        });

        return response()->json($data);
    }

    public function store_chemical(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required',
            'jenis_pemakaian' => 'required|array',
            'chemical_area' => 'required',
            'jumlah_pemakaian' => 'required|array',
            'running_hour' => 'nullable|array',
        ]);

        $tanggal = $request->input('tanggal');
        $shift = $request->input('shift');
        $chemical_area = $request->input('chemical_area');
        $keterangan = $request->input('keterangan');
        $jenisPemakaian = $request->input('jenis_pemakaian');
        $jumlahPemakaian = $request->input('jumlah_pemakaian');
        $running_hour = $request->input('running_hour');

        // $operator = Session::get('username');
        $operator = Auth::user()->username;
        if (count($jenisPemakaian) !== count($jumlahPemakaian)) {
            return response()->json(['message' => 'Data chemical tidak valid.'], 422);
        }

        foreach ($jenisPemakaian as $index => $jenis) {
            // Cek apakah data dengan kombinasi ini sudah ada
            $existing = PemakaianChemicalModel::where('tanggal', $tanggal)
                ->where('jenis_pemakaian', $jenis)
                ->where('shift', $shift)
                ->where('chemical_area', $chemical_area)
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Data untuk area "' . $chemical_area . '" pada tanggal dan shift ini sudah ada.'
                ], 422);
            }

            // Jika tidak ada, simpan data baru
            PemakaianChemicalModel::create([
                'tanggal' => $tanggal,
                'chemical_area' => $chemical_area,
                'jenis_pemakaian' => $jenis,
                'nilai_pemakaian' => $jumlahPemakaian[$index],
                'running_hour' => $running_hour[$index],
                'operator' => $operator,
                'shift' => $shift,
                'notes' => $keterangan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        return response()->json(['message' => 'Data pemakaian chemical berhasil disimpan.']);
    }



    // public function getPemakaianChemicalData(Request $request)
    // {
    //     $data = PemakaianChemicalModel::orderBy('tanggal', 'desc')->get();

    //     // Mapping satuan berdasarkan nama chemical yang dinormalisasi
    //     $satuanMap = ChemicalType::pluck('satuan', 'nama_chemical')->mapWithKeys(function ($satuan, $nama) {
    //         $key = strtolower(preg_replace('/[^a-z0-9]/', '', $nama));
    //         return [$key => $satuan];
    //     });

    //     $grouped = $data->groupBy(fn ($item) => date('Y-m-d', strtotime($item->tanggal)));
    //     $result = [];

    //     foreach ($grouped as $tanggal => $items) {
    //         $jenisGrouped = $items->groupBy('jenis_pemakaian');
    //         $jenisData = [];

    //         foreach ($jenisGrouped as $jenis => $entries) {
    //             $lookupKey = strtolower(preg_replace('/[^a-z0-9]/', '', $jenis));
    //             $satuanAsli = $satuanMap[$lookupKey] ?? null;

    //             // Detail per shift
    //             $shifts = $entries->map(function ($entry) use ($satuanAsli) {
    //                 $nilai = $entry->nilai_pemakaian;
    //                 $formatted = is_null($nilai) ? '-' : "{$nilai}" . ($satuanAsli ? " {$satuanAsli}" : '');
    //                 return [
    //                     'shift' => $entry->shift,
    //                     'nilai_pemakaian' => $formatted,
    //                     'area' => $entry->chemical_area,
    //                     'operator' => $entry->operator,
    //                     'notes' => $entry->notes,
    //                     'running_hour' => $entry->running_hour,
    //                     'created_at' => $entry->created_at,
    //                     'updated_at' => $entry->updated_at,
    //                 ];
    //             })->sortBy(fn ($s) => preg_replace('/\D/', '', strtolower($s['shift'])))->values();

    //             // Hitung total pemakaian dan tentukan satuannya
    //             $totalPemakaian = 0;
    //             $hasCustomRumus = false;

    //             foreach ($entries as $entry) {
    //                 $nilai = is_numeric($entry->nilai_pemakaian)
    //                     ? floatval($entry->nilai_pemakaian)
    //                     : floatval(preg_replace('/[^\d.]+/', '', $entry->nilai_pemakaian));
    //                 $rh = $entry->running_hour ?? 1;
    //                 $jenisAsli = trim($entry->jenis_pemakaian);

    //                 switch ($jenisAsli) {
    //                     case 'PAC powder 1':
    //                         $totalPemakaian += $rh * ($nilai * 60 * 7.6 / 100) / 1000;
    //                         $hasCustomRumus = true;
    //                         break;
    //                     case 'PAC powder 2':
    //                         $totalPemakaian += $rh * ($nilai * 60 * 12.5 / 100) / 1000;
    //                         $hasCustomRumus = true;
    //                         break;
    //                     case 'BE-100':
    //                         $totalPemakaian += $rh * ($nilai * 60 * 12.5 / 100) / 1000;
    //                         $hasCustomRumus = true;
    //                         break;
    //                     case 'C-204':
    //                         $totalPemakaian += $rh * ($nilai * 60 * 1 / 100) / 1000;
    //                         $hasCustomRumus = true;
    //                         break;
    //                     case 'C-9040 step 1':
    //                         $totalPemakaian += $rh * ($nilai * 60 * 0.11 / 100) / 1000;
    //                         $hasCustomRumus = true;
    //                         break;
    //                     case 'C-9040 step 2':
    //                         $totalPemakaian += $rh * ($nilai * 60 * 0.35 / 100) / 1000;
    //                         $hasCustomRumus = true;
    //                         break;
    //                     case 'Denfloc 260 PA':
    //                         $totalPemakaian += ($rh * ($nilai / 1000 * 60) * 480) / 1000 / 1000 / 1000;
    //                         $hasCustomRumus = true;
    //                         break;
    //                     case 'NaOH':
    //                         $totalPemakaian += $rh * ($nilai / 1000 * 60) * 1.5;
    //                         $hasCustomRumus = true;
    //                         break;
    //                     default:
    //                         $totalPemakaian += $nilai;
    //                         break;
    //                 }
    //             }

    //             $finalSatuan = $hasCustomRumus ? 'kg/hari' : ($satuanAsli ?? null);

    //             $jenisData[] = [
    //                 'jenis_pemakaian' => $jenis,
    //                 'total_pemakaian' => round($totalPemakaian, 3),
    //                 'satuan' => $finalSatuan,
    //                 'shifts' => $shifts
    //             ];
    //         }

    //         $result[] = [
    //             'tanggal' => $tanggal,
    //             'data' => $jenisData
    //         ];
    //     }

    //     return response()->json($result);
    // }

    public function getPemakaianChemicalData(Request $request)
    {
        // Ambil parameter bulan dari request, default ke bulan sekarang
        $bulan = $request->input('bulan', date('Y-m'));

        // Parse tahun dan bulan
        $year = date('Y', strtotime($bulan . '-01'));
        $month = date('m', strtotime($bulan . '-01'));

        // Query dengan filter bulan
        $data = PemakaianChemicalModel::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Mapping satuan berdasarkan nama chemical yang dinormalisasi
        $satuanMap = ChemicalType::pluck('satuan', 'nama_chemical')->mapWithKeys(function ($satuan, $nama) {
                $key = strtolower(preg_replace('/[^a-z0-9]/', '', $nama));
                return [$key => $satuan];
            });

        $grouped = $data->groupBy(fn ($item) => date('Y-m-d', strtotime($item->tanggal)));
        $result = [];

        foreach ($grouped as $tanggal => $items) {
            $jenisGrouped = $items->groupBy('jenis_pemakaian');
            $jenisData = [];

            foreach ($jenisGrouped as $jenis => $entries) {
                $lookupKey = strtolower(preg_replace('/[^a-z0-9]/', '', $jenis));
                $satuanAsli = $satuanMap[$lookupKey] ?? null;

                // Detail per shift
                $shifts = $entries->map(function ($entry) use ($satuanAsli) {
                    $nilai = $entry->nilai_pemakaian;
                    $formatted = is_null($nilai) ? '-' : "{$nilai}" . ($satuanAsli ? " {$satuanAsli}" : '');
                    return [
                        'shift' => $entry->shift,
                        'nilai_pemakaian' => $formatted,
                        'area' => $entry->chemical_area,
                        'operator' => $entry->operator,
                        'notes' => $entry->notes,
                        'running_hour' => $entry->running_hour,
                        'created_at' => $entry->created_at,
                        'updated_at' => $entry->updated_at,
                    ];
                })->sortBy(fn ($s) => preg_replace('/\D/', '', strtolower($s['shift'])))->values();

                // Hitung total pemakaian dan tentukan satuannya
                $totalPemakaian = 0;
                $hasCustomRumus = false;

                foreach ($entries as $entry) {
                    $nilai = is_numeric($entry->nilai_pemakaian)
                    ? floatval($entry->nilai_pemakaian)
                    : floatval(preg_replace('/[^\d.]+/', '', $entry->nilai_pemakaian));
                    $rh = $entry->running_hour ?? 1;
                    $jenisAsli = trim($entry->jenis_pemakaian);

                    switch ($jenisAsli) {
                        case 'PAC powder 1':
                            $totalPemakaian += $rh * ($nilai * 60 * 7.6 / 100) / 1000;
                            $hasCustomRumus = true;
                            break;
                        case 'PAC powder 2':
                            $totalPemakaian += $rh * ($nilai * 60 * 12.5 / 100) / 1000;
                            $hasCustomRumus = true;
                            break;
                        case 'BE-100':
                            $totalPemakaian += $rh * ($nilai * 60 * 12.5 / 100) / 1000;
                            $hasCustomRumus = true;
                            break;
                        case 'C-204':
                            $totalPemakaian += $rh * ($nilai * 60 * 1 / 100) / 1000;
                            $hasCustomRumus = true;
                            break;
                        case 'C-9040 step 1':
                            $totalPemakaian += $rh * ($nilai * 60 * 0.11 / 100) / 1000;
                            $hasCustomRumus = true;
                            break;
                        case 'C-9040 step 2':
                            $totalPemakaian += $rh * ($nilai * 60 * 0.35 / 100) / 1000;
                            $hasCustomRumus = true;
                            break;
                        case 'Denfloc 260 PA':
                            $totalPemakaian += ($rh * ($nilai / 1000 * 60) * 480) / 1000 / 1000 / 1000;
                            $hasCustomRumus = true;
                            break;
                        case 'NaOH':
                            $totalPemakaian += $rh * ($nilai / 1000 * 60) * 1.5;
                            $hasCustomRumus = true;
                            break;
                        default:
                            $totalPemakaian += $nilai;
                            break;
                    }
                }

                $finalSatuan = $hasCustomRumus ? 'kg/hari' : ($satuanAsli ?? null);

                $jenisData[] = [
                    'jenis_pemakaian' => $jenis,
                    'total_pemakaian' => round($totalPemakaian, 3),
                    'satuan' => $finalSatuan,
                    'shifts' => $shifts
                ];
            }

            $result[] = [
                'tanggal' => $tanggal,
                'data' => $jenisData
            ];
        }

        return response()->json($result);
    }

   

    public function getChemicalAreas()
    {
        $areas = ChemicalArea::orderBy('nama_area')->get();

        return response()->json($areas);
    }

   
    public function exportPemakaianChemicalSpreadsheet(Request $request)
    {
        $bulan = $request->input('bulan'); // contoh: '2025-06'
        if (!$bulan) {
            return response()->json(['message' => 'Parameter bulan diperlukan (format: YYYY-MM)'], 400);
        }

        $startDate = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Urutan chemical sesuai template (kolom B–R)
        $kategori = [
            'SCF', 'SRTF', 'PAC powder 1', 'PAC powder 2',
            'C-9040 step 1', 'C-9040 step 2', 'BE-100', 'C-204',
            'Denfloc 945', 'Defoamer', 'NaOH', 'NPK',
            'Chlorin', 'SMBS', 'PT100', 'B4', 'SRF'
        ];

        // Normalisasi satuan dari chemical_types
        $satuanMap = ChemicalType::all()
            ->mapWithKeys(function ($item) {
                $key = strtolower(preg_replace('/[^a-z0-9]/', '', trim($item->nama_chemical)));
                return [$key => trim($item->satuan)];
            });

        // Load template
        $templatePath = storage_path('app/templates/template_chemical.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Tulis bulan ke cell R1
        $sheet->setCellValue('R1', $bulan);

        foreach ($kategori as $index => $chemicalName) {
            // Ambil semua data per chemical untuk bulan tsb
            $entries = PemakaianChemicalModel::whereRaw('TRIM(LOWER(jenis_pemakaian)) = ?', [strtolower(trim($chemicalName))])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // Kelompokkan per hari
            $byDay = $entries->groupBy(function ($item) {
                return Carbon::parse($item->created_at)->day;
            });

            $colIndex = 2 + $index; // B=2, C=3, dst

            for ($day = 1; $day <= $endDate->day; $day++) {
                $rowIndex = 5 + $day;
                $dayEntries = $byDay->get($day, collect());

                $totalPemakaian = 0;
                foreach ($dayEntries as $entry) {
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
                            $totalPemakaian += $rh * ($nilai * 60 * 12.5 / 100) / 1000;
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

                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue("{$colLetter}{$rowIndex}", $dayEntries->isNotEmpty() ? round($totalPemakaian, 3) : '');
            }
        }

        // Simpan hasil export
        $filename = "Laporan_Chemical_Utility_{$bulan}.xlsx";
        $outputPath = storage_path("app/exports/{$filename}");
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputPath);

        return response()->download($outputPath)->deleteFileAfterSend(true);
    }

    // public function getTrendPemakaianChemical(Request $request)
    // {
    //     $tanggal = $request->query('tanggal'); // format: YYYY-MM-DD
    //     $bulan   = $request->query('bulan');   // format: YYYY-MM

    //     $query = PemakaianChemicalModel::query()
    //         ->join('chemical_types', 'pemakaian_chemical.jenis_pemakaian', '=', 'chemical_types.nama_chemical');

    //     if ($tanggal) {
    //         $query->whereDate('pemakaian_chemical.tanggal', $tanggal);
    //     } elseif ($bulan) {
    //         $query->whereYear('pemakaian_chemical.tanggal', substr($bulan, 0, 4))
    //             ->whereMonth('pemakaian_chemical.tanggal', substr($bulan, 5, 2));
    //     } else {
    //         $query->whereYear('pemakaian_chemical.tanggal', now()->format('Y'))
    //             ->whereMonth('pemakaian_chemical.tanggal', now()->format('m'));
    //     }

    //     $data = $query->select(
    //         'pemakaian_chemical.tanggal',
    //         'pemakaian_chemical.jenis_pemakaian',
    //         'chemical_types.satuan',
    //         DB::raw('SUM(nilai_pemakaian) as total_pemakaian')
    //     )
    //         ->groupBy('pemakaian_chemical.tanggal', 'pemakaian_chemical.jenis_pemakaian', 'chemical_types.satuan')
    //         ->orderBy('pemakaian_chemical.tanggal')
    //         ->get()
    //         ->groupBy('jenis_pemakaian');

    //     $result = [];

    //     foreach ($data as $jenis => $records) {
    //         $satuan = $records->first()->satuan ?? '-';
    //         $result[] = [
    //             'name' => "$jenis ($satuan)",
    //             'data' => $records->map(fn ($r) => [
    //                 'x' => $r->tanggal,
    //                 'y' => round($r->total_pemakaian, 2)
    //             ])->values()
    //         ];
    //     }

    //     return response()->json($result);
    // }


      // public function getTopJenisPemakaianChemical(Request $request)
    // {
    //     $start = $request->query('start_date');
    //     $end = $request->query('end_date');

    //     if (!$start || !$end) {
    //         $start = now()->startOfMonth()->format('Y-m-d');
    //         $end = now()->endOfMonth()->format('Y-m-d');
    //     }

    //     $data = PemakaianChemicalModel::whereBetween('tanggal', [$start, $end])->get();

    //     // Normalisasi satuan dari chemical type
    //     $satuanMap = ChemicalType::pluck('satuan', 'nama_chemical')->mapWithKeys(function ($satuan, $nama) {
    //         $key = strtolower(preg_replace('/[^a-z0-9]/', '', $nama));
    //         return [$key => $satuan];
    //     });

    //     $grouped = $data->groupBy('jenis_pemakaian');
    //     $result = [];

    //     foreach ($grouped as $jenis => $entries) {
    //         $totalPemakaian = 0;
    //         $hasCustomRumus = false;
    //         $lookupKey = strtolower(preg_replace('/[^a-z0-9]/', '', $jenis));
    //         $satuanAsli = $satuanMap[$lookupKey] ?? null;

    //         foreach ($entries as $entry) {
    //             $nilai = is_numeric($entry->nilai_pemakaian)
    //                 ? floatval($entry->nilai_pemakaian)
    //                 : floatval(preg_replace('/[^\d.]+/', '', $entry->nilai_pemakaian));
    //             $rh = $entry->running_hour ?? 1;
    //             $jenisAsli = trim($entry->jenis_pemakaian);

    //             switch ($jenisAsli) {
    //                 case 'PAC powder 1':
    //                     $totalPemakaian += $rh * ($nilai * 60 * 7.6 / 100) / 1000;
    //                     $hasCustomRumus = true;
    //                     break;
    //                 case 'PAC powder 2':
    //                     $totalPemakaian += $rh * ($nilai * 60 * 12.5 / 100) / 1000;
    //                     $hasCustomRumus = true;
    //                     break;
    //                 case 'BE-100':
    //                     $totalPemakaian += $rh * ($nilai * 60 * 12.5 / 100) / 1000;
    //                     $hasCustomRumus = true;
    //                     break;
    //                 case 'C-204':
    //                     $totalPemakaian += $rh * ($nilai * 60 * 1 / 100) / 1000;
    //                     $hasCustomRumus = true;
    //                     break;
    //                 case 'C-9040 step 1':
    //                     $totalPemakaian += $rh * ($nilai * 60 * 0.11 / 100) / 1000;
    //                     $hasCustomRumus = true;
    //                     break;
    //                 case 'C-9040 step 2':
    //                     $totalPemakaian += $rh * ($nilai * 60 * 0.35 / 100) / 1000;
    //                     $hasCustomRumus = true;
    //                     break;
    //                 case 'Denfloc 260 PA':
    //                     $totalPemakaian += ($rh * ($nilai / 1000 * 60) * 480) / 1000 / 1000 / 1000;
    //                     $hasCustomRumus = true;
    //                     break;
    //                 case 'NaOH':
    //                     $totalPemakaian += $rh * ($nilai / 1000 * 60) * 1.5;
    //                     $hasCustomRumus = true;
    //                     break;
    //                 default:
    //                     $totalPemakaian += $nilai;
    //                     break;
    //             }
    //         }


    //         $result[] = [
    //             'jenis_pemakaian' => $jenis,
    //             'total_pemakaian' => round($totalPemakaian, 3),
    //             'satuan'          => $hasCustomRumus ? 'kg/hari' : ($satuanAsli ?? '-'),
    //             'start_date'      => $start,
    //             'end_date'        => $end
    //         ];
    //     }

    //     return response()->json(collect($result)->sortByDesc('total_pemakaian')->values());
    // }


    public function getTrendPemakaianChemical(Request $request)
    {
        $tanggal = $request->query('tanggal'); // format: YYYY-MM-DD
        $bulan   = $request->query('bulan');   // format: YYYY-MM
        $area    = $request->query('area');    // 'utility' atau 'wwtp'

        // Definisi chemical berdasarkan area
        $chemicalUtility = ['SCF', 'SRTF', 'PT-100', 'PT100', 'SMBS', 'B4', 'SRF', 'Chlorin'];
        $chemicalWWTP = [
            'PAC powder 1', 'PAC powder 2', 'BE-100', 'C-204', 'C-9040 step 1',
            'C-9040 step 2', 'Denfloc 260 PA', 'Denfloc 945', 'NaOH',
            'Defoamer', 'NPK'
        ];

        $query = PemakaianChemicalModel::query()
        ->join('chemical_types', 'pemakaian_chemical.jenis_pemakaian', '=', 'chemical_types.nama_chemical');

        // Filter berdasarkan area
        if ($area === 'utility') {
            $query->whereIn('pemakaian_chemical.jenis_pemakaian', $chemicalUtility);
        } elseif ($area === 'wwtp') {
            $query->whereIn('pemakaian_chemical.jenis_pemakaian', $chemicalWWTP);
        }

        // Filter tanggal
        if ($tanggal) {
            $query->whereDate('pemakaian_chemical.tanggal', $tanggal);
        } elseif ($bulan) {
            $query->whereYear('pemakaian_chemical.tanggal', substr($bulan, 0, 4))
            ->whereMonth('pemakaian_chemical.tanggal', substr($bulan, 5, 2));
        } else {
            $query->whereYear('pemakaian_chemical.tanggal', now()->format('Y'))
                ->whereMonth('pemakaian_chemical.tanggal', now()->format('m'));
        }

        $data = $query->select(
            'pemakaian_chemical.tanggal',
            'pemakaian_chemical.jenis_pemakaian',
            'pemakaian_chemical.nilai_pemakaian',
            'pemakaian_chemical.running_hour',
            'chemical_types.satuan'
        )
        ->orderBy('pemakaian_chemical.tanggal')
        ->orderBy('pemakaian_chemical.jenis_pemakaian')
        ->get()
        ->groupBy('jenis_pemakaian');

        $result = [];

        foreach ($data as $jenis => $records) {
            $dataPoints = [];

            foreach ($records as $record) {
                $nilai = is_numeric($record->nilai_pemakaian)
                ? floatval($record->nilai_pemakaian)
                    : floatval(preg_replace('/[^\d.]+/', '', $record->nilai_pemakaian));
                $rh = $record->running_hour ?? 1;
                $totalPemakaian = 0;
                $satuan = $record->satuan ?? '-';
                $jenisAsli = trim($record->jenis_pemakaian);

                // Hitung berdasarkan rumus khusus
                switch ($jenisAsli) {
                    case 'PAC powder 1':
                        $totalPemakaian = $rh * ($nilai * 60 * 7.6 / 100) / 1000;
                        $satuan = 'kg/hari';
                        break;
                    case 'PAC powder 2':
                        $totalPemakaian = $rh * ($nilai * 60 * 12.5 / 100) / 1000;
                        $satuan = 'kg/hari';
                        break;
                    case 'BE-100':
                        $totalPemakaian = $rh * ($nilai * 60 * 12.5 / 100) / 1000;
                        $satuan = 'kg/hari';
                        break;
                    case 'C-204':
                        $totalPemakaian = $rh * ($nilai * 60 * 1 / 100) / 1000;
                        $satuan = 'kg/hari';
                        break;
                    case 'C-9040 step 1':
                        $totalPemakaian = $rh * ($nilai * 60 * 0.11 / 100) / 1000;
                        $satuan = 'kg/hari';
                        break;
                    case 'C-9040 step 2':
                        $totalPemakaian = $rh * ($nilai * 60 * 0.35 / 100) / 1000;
                        $satuan = 'kg/hari';
                        break;
                    case 'Denfloc 260 PA':
                        $totalPemakaian = ($rh * ($nilai / 1000 * 60) * 480) / 1000 / 1000 / 1000;
                        $satuan = 'kg/hari';
                        break;
                    case 'NaOH':
                        $totalPemakaian = $rh * ($nilai / 1000 * 60) * 1.5;
                        $satuan = 'kg/hari';
                        break;
                    default:
                        $totalPemakaian = $nilai;
                        break;
                }

                $dataPoints[] = [
                    'x' => $record->tanggal,
                    'y' => round($totalPemakaian, 3)
                ];
            }

            // Agregasi per tanggal (jika ada multiple entries per hari)
            $groupedByDate = collect($dataPoints)->groupBy('x')->map(function ($items) {
                return [
                    'x' => $items->first()['x'],
                    'y' => round($items->sum('y'), 3)
                ];
            })->values();

            $result[] = [
                'name' => "$jenis",
                'data' => $groupedByDate
            ];
        }

        return response()->json($result);
    }
  
    public function getTopJenisPemakaianChemical(Request $request)
    {
        $start = $request->query('start_date');
        $end = $request->query('end_date');
        $area = $request->query('area'); // 'utility' atau 'wwtp'

        if (!$start || !$end) {
            $start = now()->startOfMonth()->format('Y-m-d');
            $end = now()->endOfMonth()->format('Y-m-d');
        }

        // Definisi chemical berdasarkan area
        $chemicalUtility = ['SCF', 'SRTF', 'PT-100', 'PT100', 'SMBS', 'B4', 'SRF', 'Chlorin'];
        $chemicalWWTP = [
            'PAC powder 1', 'PAC powder 2', 'BE-100', 'C-204', 'C-9040 step 1',
            'C-9040 step 2', 'Denfloc 260 PA', 'Denfloc 945', 'NaOH',
            'Defoamer', 'NPK'
        ];

        $data = PemakaianChemicalModel::whereBetween('tanggal', [$start, $end])->get();

        // Filter berdasarkan area jika diminta
        if ($area === 'utility') {
            $data = $data->filter(function ($item) use ($chemicalUtility) {
                return in_array($item->jenis_pemakaian, $chemicalUtility);
            });
        } elseif ($area === 'wwtp') {
            $data = $data->filter(function ($item) use ($chemicalWWTP) {
                return in_array($item->jenis_pemakaian, $chemicalWWTP);
            });
        }

        // Normalisasi satuan dari chemical type
        $satuanMap = ChemicalType::pluck('satuan', 'nama_chemical')->mapWithKeys(function ($satuan, $nama) {
            $key = strtolower(preg_replace('/[^a-z0-9]/', '', $nama));
            return [$key => $satuan];
        });

        $grouped = $data->groupBy('jenis_pemakaian');
        $result = [];

        foreach ($grouped as $jenis => $entries) {
            $totalPemakaian = 0;
            $hasCustomRumus = false;
            $lookupKey = strtolower(preg_replace('/[^a-z0-9]/', '', $jenis));
            $satuanAsli = $satuanMap[$lookupKey] ?? null;

            foreach ($entries as $entry) {
                $nilai = is_numeric($entry->nilai_pemakaian)
                ? floatval($entry->nilai_pemakaian)
                : floatval(preg_replace('/[^\d.]+/', '', $entry->nilai_pemakaian));
                $rh = $entry->running_hour ?? 1;
                $jenisAsli = trim($entry->jenis_pemakaian);

                switch ($jenisAsli) {
                    case 'PAC powder 1':
                        $totalPemakaian += $rh * ($nilai * 60 * 7.6 / 100) / 1000;
                        $hasCustomRumus = true;
                        break;
                    case 'PAC powder 2':
                        $totalPemakaian += $rh * ($nilai * 60 * 12.5 / 100) / 1000;
                        $hasCustomRumus = true;
                        break;
                    case 'BE-100':
                        $totalPemakaian += $rh * ($nilai * 60 * 12.5 / 100) / 1000;
                        $hasCustomRumus = true;
                        break;
                    case 'C-204':
                        $totalPemakaian += $rh * ($nilai * 60 * 1 / 100) / 1000;
                        $hasCustomRumus = true;
                        break;
                    case 'C-9040 step 1':
                        $totalPemakaian += $rh * ($nilai * 60 * 0.11 / 100) / 1000;
                        $hasCustomRumus = true;
                        break;
                    case 'C-9040 step 2':
                        $totalPemakaian += $rh * ($nilai * 60 * 0.35 / 100) / 1000;
                        $hasCustomRumus = true;
                        break;
                    case 'Denfloc 260 PA':
                        $totalPemakaian += ($rh * ($nilai / 1000 * 60) * 480) / 1000 / 1000 / 1000;
                        $hasCustomRumus = true;
                        break;
                    case 'NaOH':
                        $totalPemakaian += $rh * ($nilai / 1000 * 60) * 1.5;
                        $hasCustomRumus = true;
                        break;
                    default:
                        $totalPemakaian += $nilai;
                        break;
                }
            }

            // Tentukan kategori area
            $areaCategory = in_array($jenis, $chemicalUtility) ? 'Utility' : 'WWTP';

            $result[] = [
                'jenis_pemakaian' => $jenis,
                'total_pemakaian' => round($totalPemakaian, 3),
                'satuan'          => $hasCustomRumus ? 'kg/hari' : ($satuanAsli ?? '-'),
                'area'            => $areaCategory,
                'start_date'      => $start,
                'end_date'        => $end
            ];
        }

        return response()->json(collect($result)->sortByDesc('total_pemakaian')->values());
    }

    public function getTopOperatorPemakaianChemical(Request $request)
    {
        $bulan = $request->query('bulan'); // contoh: 2025-06

        $tahun = $bulan ? substr($bulan, 0, 4) : now()->format('Y');
        $bulanAngka = $bulan ? substr($bulan, 5, 2) : now()->format('m');

        $data = PemakaianChemicalModel::query()
            ->select(
                'operator',
                DB::raw('COUNT(*) as jumlah_pengisian')
            )
            ->whereMonth('tanggal', $bulanAngka)
            ->whereYear('tanggal', $tahun)
            ->groupBy('operator')
            ->orderByDesc('jumlah_pengisian')
            ->limit(5)
            ->get();

        return response()->json($data);
    }

 
    public function updateChemical(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_pemakaian' => 'required|string',
            'shift' => 'required|string',
            'nilai_pemakaian' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $data = PemakaianChemicalModel::whereDate('tanggal', $request->tanggal)
            ->where('jenis_pemakaian', $request->jenis_pemakaian)
            ->where('shift', $request->shift)
            ->first();

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $data->update([
            'nilai_pemakaian' => $request->nilai_pemakaian,
            'jenis_pemakaian' => $request->jenis_pemakaian,
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'Data Chemical berhasil diperbarui.']);
    }


    //export data ke template excel


}
