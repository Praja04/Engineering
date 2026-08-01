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
                        $areaName = $request->input("data.$index.area") ?? "ke-$index";
                        if ($value < $awal) {
                            $fail("Pemakaian akhir harus lebih besar atau sama dengan awal untuk area $areaName.");
                        }
                        if (($value - $awal) > 999) {
                            $fail("Total pemakaian untuk area $areaName tidak masuk akal (lebih dari 3 digit / > 999). Silakan cek kembali input pemakaian awal dan akhir.");
                        }
                    }
                }
            ],
            'notes' => 'nullable|string|max:255',
            'foreman_id' => 'nullable|exists:users,id',
            'supervisor_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tanggal = $request->input('tanggal');
        $bulan = date('Y-m', strtotime($tanggal));
        $approval = \App\Models\Utility\UtilityMonthlyApproval::where('bulan', $bulan)->where('tipe', 'air')->first();
        // if ($approval && in_array($approval->status, ['approved_foreman', 'approved_supervisor'])) {
        //     return response()->json([
        //         'message' => 'Laporan Air untuk bulan ini (' . $bulan . ') sudah disetujui, sehingga data tidak dapat ditambahkan.'
        //     ], 422);
        // }

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

            if ($area === 'CT RO') {
                $this->syncCoolingTowerFlowrate($tanggal, $entry['pemakaian_liter_awal'], $entry['pemakaian_liter_akhir']);
            }

            $inserted[] = $area;
        }

        // Ensure monthly approval is created/submitted and foreman notified
        \App\Models\Utility\UtilityMonthlyApproval::checkAndNotify(
            $bulan,
            'air',
            auth()->id(),
            $request->input('foreman_id'),
            $request->input('supervisor_id')
        );

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
        $query = PemakaianAirModel::orderBy('tanggal', 'desc');

        if ($request->filled('bulan')) {
            $bulan = $request->input('bulan');
            $year = date('Y', strtotime($bulan . '-01'));
            $month = date('m', strtotime($bulan . '-01'));
            $query->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month);
        }

        $data = $query->get();

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

        if ($areas->isEmpty()) {
            $defaultAreas = PemakaianAirModel::distinct()->pluck('jenis_pemakaian')->filter()->toArray();
            if (empty($defaultAreas)) {
                $defaultAreas = [
                    'Sumur 1',
                    'Sumur 2',
                    'Sumur 4',
                    'Sumur 5',
                    'CT RO',
                    'CT WS',
                    'Green Belt',
                    'Outlet Fresh Water 1',
                    'Outlet Fresh Water 2',
                    'Outlet Storage RO Reject',
                    'Outlet Storage WS',
                    'PDAM'
                ];
            }
            foreach ($defaultAreas as $name) {
                AirArea::firstOrCreate(['nama_area' => $name]);
            }
            $areas = AirArea::orderBy('nama_area')->get();
        }

        foreach ($areas as $area) {
            $latest = PemakaianAirModel::where('jenis_pemakaian', $area->nama_area)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $area->pemakaian_awal = $latest ? $latest->pemakaian_akhir : 0;
        }

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
        $year = $startDate->year;
        $months = $startDate->month;

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
            'Air Proses',
        ];

        // Load template
        $templatePath = public_path('assets/templates/utility/laporan_air_utility.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Tanggal
        $sheet->setCellValue("AK1", $months);
        $sheet->setCellValue("AK2", $year);

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

        $approval = \App\Models\Utility\UtilityMonthlyApproval::where('bulan', $month)->where('tipe', 'air')->first();

        // Draw signatures if approved/submitted
        $signaturePath = public_path('storage/operasional/ttd/utility_approved_sticker.png');
        if (file_exists($signaturePath) && $approval) {
            if (in_array($approval->status, ['submitted', 'approved_foreman', 'approved_supervisor'])) {
                // Operator (AL6)
                $drawOp = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawOp->setName('Operator');
                $drawOp->setPath($signaturePath);
                $drawOp->setHeight(70);
                $drawOp->setCoordinates('AO9');
                $drawOp->setOffsetX('40');
                $drawOp->setWorksheet($sheet);
                $sheet->setCellValue('AO14', ($approval->operator ? $approval->operator->username : '-'));
                $sheet->setCellValue('AO15', ($approval->submitted_at));
            }
            if (in_array($approval->status, ['approved_foreman', 'approved_supervisor'])) {
                // Foreman (AL17)
                $drawFm = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawFm->setName('Foreman');
                $drawFm->setPath($signaturePath);
                $drawFm->setHeight(70);
                $drawFm->setCoordinates('AO20');
                $drawFm->setOffsetX('40');
                $drawFm->setWorksheet($sheet);
                $sheet->setCellValue('AO25', ($approval->foreman ? $approval->foreman->username : '-'));
                $sheet->setCellValue('AO26', ($approval->foreman_approved_at));
            }
            if ($approval->status === 'approved_supervisor') {
                // Supervisor (AL28)
                $drawSpv = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawSpv->setName('Supervisor');
                $drawSpv->setPath($signaturePath);
                $drawSpv->setHeight(70);
                $drawSpv->setCoordinates('AO31');
                $drawSpv->setOffsetX('40');
                $drawSpv->setWorksheet($sheet);
                $sheet->setCellValue('AO35', ($approval->supervisor ? $approval->supervisor->username : '-'));
                $sheet->setCellValue('AO36', ($approval->supervisor_approved_at));
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
                'data' => $records->map(fn($r) => [
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
            'pemakaian_akhir' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $awal = $request->input('pemakaian_awal');
                    $areaName = $request->input('jenis_pemakaian');
                    if ($value < $awal) {
                        $fail("Pemakaian akhir harus lebih besar atau sama dengan awal untuk area $areaName.");
                    }
                    if (($value - $awal) > 999) {
                        $fail("Total pemakaian untuk area $areaName tidak masuk akal (lebih dari 3 digit / > 999). Silakan cek kembali input pemakaian awal dan akhir.");
                    }
                }
            ],
            'created_by' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $bulan = date('Y-m', strtotime($request->tanggal));
        $approval = \App\Models\Utility\UtilityMonthlyApproval::where('bulan', $bulan)->where('tipe', 'air')->first();
        if ($approval && in_array($approval->status, ['approved_foreman', 'approved_supervisor'])) {
            return response()->json([
                'message' => 'Laporan Air untuk bulan ini (' . $bulan . ') sudah disetujui, sehingga data tidak dapat diubah.'
            ], 422);
        }

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

        if ($request->jenis_pemakaian === 'CT RO') {
            $this->syncCoolingTowerFlowrate($request->tanggal, $request->pemakaian_awal, $request->pemakaian_akhir);
        }

        // Check and transition status back to submitted if rejected
        \App\Models\Utility\UtilityMonthlyApproval::checkAndNotify(
            $bulan,
            'air',
            auth()->id(),
            $approval->foreman_id ?? null,
            $approval->supervisor_id ?? null
        );

        return response()->json(['message' => 'Data Air berhasil diperbarui.']);
    }

    private function syncCoolingTowerFlowrate($tanggal, $awal, $akhir)
    {
        $date = \Carbon\Carbon::parse($tanggal);
        $month = $date->month;
        $year = $date->year;

        $main = \App\Models\Utility\CoolingTower::firstOrCreate(
            [
                'bulan' => $month,
                'tahun' => $year,
            ],
            [
                'operator_id' => auth()->id() ?? 1,
                'status' => 'draft',
                'submitted_at' => now(),
            ]
        );

        $details = \App\Models\Utility\CoolingTowerDetails::where('tanggal', $tanggal)->get();

        if ($details->count() > 0) {
            foreach ($details as $detail) {
                $detail->update([
                    'flowrate_ro_awal' => $awal,
                    'flowrate_ro_akhir' => $akhir,
                ]);
            }
        } else {
            \App\Models\Utility\CoolingTowerDetails::create([
                'cooling_tower_id' => $main->id,
                'tanggal' => $tanggal,
                'jam' => '08:00',
                'flowrate_ro_awal' => $awal,
                'flowrate_ro_akhir' => $akhir,
                'created_by' => auth()->id() ?? 1,
            ]);
        }
    }
}
