<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\PemakaianAirModel;
use App\Models\Utility\AirArea;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AirController extends Controller
{
 
  

    // Api crud operator

    public function storeAir(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'data' => 'required|array|min:1',
            'data.*.area' => 'required|string',
            'data.*.pemakaian_liter_awal' => 'required|numeric|min:0',
            'data.*.pemakaian_liter_akhir' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    // Ambil index dari data.*.pemakaian_liter_akhir
                    preg_match('/data\.(\d+)\.pemakaian_liter_akhir/', $attribute, $matches);
                    $index = $matches[1] ?? null;

                    if ($index !== null) {
                        $awal = $request->input("data.$index.pemakaian_liter_awal");
                        if ($value < $awal) {
                            $fail("Pemakaian akhir harus lebih besar atau sama dengan awal untuk area ke-$index.");
                        }
                    }
                }
            ],
            'notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tanggal = $request->input('tanggal');
        // $createdBy = Session::get('username') ?? 'system';
        $createdBy = Auth::check() ? Auth::user()->username : 'system';

        $notes = $request->input('notes');
        $data = $request->input('data');

        $conflicts = [];
        $inserted = [];

        foreach ($data as $entry) {
            $area = $entry['area'];

            $exists = PemakaianAirModel::whereDate('tanggal', $tanggal)
                ->where('jenis_pemakaian', $area)
                ->exists();

            if ($exists) {
                $conflicts[] = $area;
                continue;
            }

            $air = new PemakaianAirModel();
            $air->tanggal = $tanggal;
            $air->pemakaian_awal = $entry['pemakaian_liter_awal'];
            $air->pemakaian_akhir = $entry['pemakaian_liter_akhir'];
            $air->jenis_pemakaian = $area;
            $air->created_by = $createdBy;
            $air->notes = $notes;
            $air->save();

            $inserted[] = $area;
        }

        return response()->json([
            'message' => 'Data pemakaian air berhasil diproses.',
            'inserted' => $inserted,
            'conflict' => $conflicts,
        ], count($conflicts) ? 207 : 201);
    }

    public function getPemakaianAir($mode)
    {
        $query = PemakaianAirModel::query();

        if ($mode == 'harian') {
            $query->whereDate('tanggal', Carbon::today());
        } elseif ($mode == 'mingguan') {
            $query->whereBetween('tanggal', [
                Carbon::now()->subDays(7)->startOfDay(),
                Carbon::now()->endOfDay()
            ]);
        } elseif ($mode == 'bulanan') {
            $query->whereMonth('tanggal', Carbon::now()->month);
        } elseif ($mode === 'terakhir') {
            $data = PemakaianAirModel::orderBy('tanggal', 'desc')->limit(7)->get();
            return response()->json($data);
        }


        $data = $query->orderBy('tanggal', 'desc')->limit(7)->get();

        return response()->json($data);
    }






    // public function getPemakaianAirData(Request $request)
    // {

    //     $data = PemakaianAirModel::orderBy('tanggal', 'desc')->get();

    //     // Kelompokkan berdasarkan tanggal
    //     $grouped = $data->groupBy(function ($item) {
    //         return date('Y-m-d', strtotime($item->tanggal));
    //     });

    //     $result = [];

    //     foreach ($grouped as $tanggal => $items) {
    //         $result[] = [
    //             'tanggal' => $tanggal,
    //             'data' => $items->map(function ($item) {
    //                 return [
    //                     'id' => $item->id,
    //                     'pemakaian_awal' => $item->pemakaian_awal,
    //                     'pemakaian_akhir' => $item->pemakaian_akhir,
    //                     'jenis_pemakaian' => $item->jenis_pemakaian,
    //                     'created_by' => $item->created_by,
    //                     'notes' => $item->notes,
    //                     'created_at' => $item->created_at,
    //                     'updated_at' => $item->updated_at,
    //                 ];
    //             })->values(),
    //         ];
    //     }

    //     return response()->json($result);
    // }

    public function getPemakaianAirData(Request $request)
    {

        $data = PemakaianAirModel::orderBy('tanggal', 'desc')->get();

        // Kelompokkan berdasarkan tanggal
        $grouped = $data->groupBy(function ($item) {
            return date('Y-m-d', strtotime($item->tanggal));
        });

        $result = [];

        foreach ($grouped as $tanggal => $items) {
            $result[] = [
                'tanggal' => $tanggal,
                'data' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'pemakaian_awal' => $item->pemakaian_awal,
                        'pemakaian_akhir' => $item->pemakaian_akhir,
                        'jenis_pemakaian' => $item->jenis_pemakaian,
                        'created_by' => $item->created_by,
                        'notes' => $item->notes,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ];
                })->values(),
            ];
        }

        return response()->json($result);
    }

    public function getAirAreas()
    {
        $areas = AirArea::orderBy('nama_area')->get();

        return response()->json($areas);
    }


    public function exportPemakaianAirSpreadsheet(Request $request)
    {
        $month = $request->input('bulan');
        if (!$month) {
            return response()->json(['message' => 'Parameter bulan diperlukan (format: YYYY-MM)'], 400);
        }

        $startDate = Carbon::create($month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Kategori sesuai template Excel
        $kategori = [
            'Outlet Storage WS',
            'Outlet Storage RO Reject',
            'Outlet Fresh Water 1',
            'Outlet Fresh Water 2',
            'Sumur 1',
            'Sumur 2',
            'Sumur 4',
            'Sumur 5',
            'PDAM',
            'CT RO',
            'CT WS',
            'Green Belt',
        ];

        // Load template
        $templatePath = storage_path('app/templates/laporan_air_utility.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($kategori as $index => $jenis) {
            $data = PemakaianAirModel::where('jenis_pemakaian', $jenis)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->orderBy('tanggal')
                ->get()
                ->keyBy(function ($item) {
                    return Carbon::parse($item->tanggal)->day;
                });

            $startColumn = 2 + ($index * 3); // C = 2, F = 5, dst...
            $row = 5;

            for ($day = 1; $day <= $endDate->day; $day++) {
                $entry = $data->get($day);
                if ($entry) {
                    $colAwal = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startColumn);
                    $colAkhir = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startColumn + 1);
                    $colSelisih = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startColumn + 2);

                    $sheet->setCellValue("{$colAwal}{$row}", $entry->pemakaian_awal);
                    $sheet->setCellValue("{$colAkhir}{$row}", $entry->pemakaian_akhir);
                    $sheet->setCellValue("{$colSelisih}{$row}", $entry->pemakaian_akhir - $entry->pemakaian_awal);
                }
                $row++;
            }
        }

        // Simpan hasil export
        $filename = "Laporan_Air_Utility_{$month}.xlsx";
        $outputPath = storage_path("app/exports/{$filename}");
        $writer = new Xlsx($spreadsheet);
        $writer->save($outputPath);

        return response()->download($outputPath)->deleteFileAfterSend(true);
    }

    public function getTrendPemakaianAir(Request $request)
    {
        $tanggal = $request->query('tanggal'); // format: YYYY-MM-DD
        $bulan   = $request->query('bulan');   // format: YYYY-MM

        $query = PemakaianAirModel::query();

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        } elseif ($bulan) {
            $query->whereMonth('tanggal', substr($bulan, 5, 2))
                ->whereYear('tanggal', substr($bulan, 0, 4));
        } else {
            // 🔁 Default: seluruh data untuk bulan ini
            $query->whereMonth('tanggal', now()->format('m'))
                ->whereYear('tanggal', now()->format('Y'));
        }

        $data = $query->select(
            'tanggal',
            'jenis_pemakaian',
            DB::raw('SUM(pemakaian_akhir - pemakaian_awal) as total_pemakaian')
        )
            ->groupBy('tanggal', 'jenis_pemakaian')
            ->orderBy('tanggal')
            ->get()
            ->groupBy('jenis_pemakaian');

        $result = [];
        foreach ($data as $jenis => $records) {
            $result[] = [
                'name' => $jenis,
                'data' => $records->map(fn ($r) => [
                    'x' => $r->tanggal,
                    'y' => round($r->total_pemakaian, 2)
                ])->values()
            ];
        }

        return response()->json($result);
    }
 
    public function getTopJenisPemakaianAir(Request $request)
    {
        $start = $request->query('start_date');
        $end = $request->query('end_date');

        // Default ke bulan sekarang
        if (!$start || !$end) {
            $start = now()->startOfMonth()->format('Y-m-d');
            $end = now()->endOfMonth()->format('Y-m-d');
        }

        $excludedSources = ['PDAM', 'Sumur 1', 'Sumur 2', 'Sumur 4', 'Sumur 5'];
        $data = PemakaianAirModel::query()
            ->select('jenis_pemakaian', DB::raw('SUM(pemakaian_akhir - pemakaian_awal) AS total_pemakaian'))
            ->whereBetween('tanggal', [$start, $end])
            ->whereNotIn('jenis_pemakaian', $excludedSources)
            ->groupBy('jenis_pemakaian')
            ->orderByDesc('total_pemakaian')
            ->get()
            ->map(function ($item) use ($start, $end) {
                return [
                    'jenis_pemakaian' => $item->jenis_pemakaian,
                    'total_pemakaian' => $item->total_pemakaian,
                    'start_date'      => $start,
                    'end_date'        => $end,
                ];
            });

        return response()->json($data);
    }
    public function getTopJenisPemakaianAirRaw(Request $request)
    {
        $start = $request->query('start_date');
        $end = $request->query('end_date');

        // Default ke bulan sekarang
        if (!$start || !$end) {
            $start = now()->startOfMonth()->format('Y-m-d');
            $end = now()->endOfMonth()->format('Y-m-d');
        }
        $excludedSources = ['PDAM', 'Sumur 1', 'Sumur 2', 'Sumur 4', 'Sumur 5'];
        $data = PemakaianAirModel::query()
            ->select('jenis_pemakaian', DB::raw('SUM(pemakaian_akhir - pemakaian_awal) AS total_pemakaian'))
            ->whereBetween('tanggal', [$start, $end])
            ->whereIn('jenis_pemakaian', $excludedSources)
            ->groupBy('jenis_pemakaian')
            ->orderByDesc('total_pemakaian')
            ->get()
            ->map(function ($item) use ($start, $end) {
                return [
                    'jenis_pemakaian' => $item->jenis_pemakaian,
                    'total_pemakaian' => $item->total_pemakaian,
                    'start_date'      => $start,
                    'end_date'        => $end,
                ];
            });

        return response()->json($data);
    }


    public function getTopOperatorPemakaianAir(Request $request)
    {
        $bulan = $request->query('bulan'); // contoh: 2025-06

        $tahun = $bulan ? substr($bulan, 0, 4) : now()->format('Y');
        $bulanAngka = $bulan ? substr($bulan, 5, 2) : now()->format('m');

        $data = PemakaianAirModel::query()
            ->select(
                'created_by',
                DB::raw('COUNT(*) as jumlah_pengisian')
            )
            ->whereMonth('tanggal', $bulanAngka)
            ->whereYear('tanggal', $tahun)
            ->groupBy('created_by')
            ->orderByDesc('jumlah_pengisian')
            // ->limit(5)
            ->get();

        return response()->json($data);
    }


    public function updateAir(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_pemakaian' => 'required|string',
            'pemakaian_awal' => 'required|numeric',
            'pemakaian_akhir' => 'required|numeric',
            'created_by' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $air = PemakaianAirModel::where('id', $request->id)
            ->first();

        if (!$air) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $air->update([
            'tanggal' => $request->tanggal,
            'jenis_pemakaian' => $request->jenis_pemakaian,
            'pemakaian_awal' => $request->pemakaian_awal,
            'pemakaian_akhir' => $request->pemakaian_akhir,
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'Data Air berhasil diperbarui.']);
    }


}