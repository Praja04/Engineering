<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Utility\PemakaianListrikModel;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\DB;

class ListrikController extends Controller
{
    //

    public function formUtility()
    {
        return view('utility.form_utility');
    }

    public function DataUtility()
    {
        return view('utility.data_utility');
    }

    public function storeListrik(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'waktu' => 'required|date',
            // 'operator' => 'required|string|max:100',
            'panel_type' => 'required|in:MDP,SDP1,SDP2,SDP3,SDP4,SDP5,SDP6,SDP7,SDP8,SDP9,SDP10,SDP11,SDP12,SDP13,SDP14',
            'volt' => 'nullable|numeric',
            'a' => 'nullable|numeric',
            'kw' => 'nullable|numeric',
            'mwh' => 'nullable|numeric',
            'cos' => 'nullable|numeric',
        ]);
        $operator = auth()->user()->username;
        try {
            $exists = PemakaianListrikModel::whereDate('waktu', $validated['waktu'])
                ->where('panel_type', $validated['panel_type'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data untuk panel tersebut pada tanggal yang sama sudah ada.',
                ], 409); // 409 = Conflict
            }
            // Simpan ke database
            $data = PemakaianListrikModel::create([
                'waktu' => $validated['waktu'],
                'operator' => $operator,
                'panel_type' => $validated['panel_type'],
                'volt' => $validated['volt'] ?? null,
                'a' => $validated['a'] ?? null,
                'kw' => $validated['kw'] ?? null,
                'mwh' => $validated['mwh'] ?? null,
                'cos' => $validated['cos'] ?? null,
            ]);

            return response()->json(['success' => true, 'message' => 'Data listrik berhasil disimpan.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.', 'error' => $e->getMessage()], 500);
        }
    }
  

    public function data_listrik()
    {

        $data = PemakaianListrikModel::orderBy('waktu', 'desc')
        ->get();

        return response()->json($data);
    }


    public function getPemakaianListrikData(Request $request)
    {
        $defaultPanelOrder = ['MDP', 'SDP1', 'SDP2', 'SDP3', 'SDP4', 'SDP5', 'SDP6', 'SDP7', 'SDP8', 'SDP9', 'SDP10', 'SDP11', 'SDP12', 'SDP13', 'SDP14'];

        $data = PemakaianListrikModel::orderBy('waktu')->get();

        // Group by tanggal (YYYY-MM-DD)
        $grouped = $data->groupBy(function ($item) {
            return date('Y-m-d', strtotime($item->waktu));
        });

        $sortedDates = $grouped->keys()->sort()->values();
        $result = [];

        foreach ($sortedDates as $index => $tanggal) {
            $items = $grouped[$tanggal];
            $pivot = [];
            $usage = [];
            $operators = [];

            // Panel tersedia dan terurut
            $availablePanels = $items->pluck('panel_type')->unique()->values()->all();
            $panels = array_values(array_intersect($defaultPanelOrder, $availablePanels));

            // Ambil operator
            foreach ($panels as $panel) {
                $panelItem = $items->firstWhere('panel_type', $panel);
                $operators[$panel] = $panelItem?->operator ?? null;
            }

            // Ambil parameter-parameter
            $parameters = ['volt', 'a', 'kw', 'mwh', 'cos'];
            foreach ($parameters as $param) {
                $pivot[$param] = [];
                foreach ($panels as $panel) {
                    $panelItem = $items->firstWhere('panel_type', $panel);
                    $pivot[$param][$panel] = $panelItem?->$param ?? null;
                }
            }

            // Hitung usage berdasarkan mwh selisih antar hari
            if ($index < count($sortedDates) - 1) {
                $nextTanggal = $sortedDates[$index + 1];
                $nextItems = $grouped[$nextTanggal];

                foreach ($panels as $panel) {
                    $currentMwh = $items->firstWhere('panel_type', $panel)?->mwh;
                    $nextMwh = $nextItems->firstWhere('panel_type', $panel)?->mwh;

                    if (!is_null($currentMwh) && !is_null($nextMwh)) {
                        $usage[$panel] = $nextMwh - $currentMwh;
                    } else {
                        $usage[$panel] = null;
                    }
                }
            } else {
                // Tanggal terakhir: usage belum bisa dihitung
                foreach ($panels as $panel) {
                    $usage[$panel] = null;
                }
            }

            $result[] = [
                'tanggal' => $tanggal,
                'operator' => $operators,
                'panels' => $panels,
                'rows' => $pivot,
                'usage' => $usage,
            ];
        }

        return response()->json(array_reverse($result));
    }
    public function exportPemakaianListrikSpreadsheet(Request $request)
    {
        $month = $request->input('bulan');
        if (!$month) {
            return response()->json(['message' => 'Parameter bulan diperlukan (format: YYYY-MM)'], 400);
        }

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $panels = array_merge(['MDP'], array_map(fn ($i) => "SDP$i", range(1, 14)));

        $templatePath = storage_path('app/templates/template_listrik.xlsx');

        // --- Load template dengan proteksi XXE dimatikan (hanya untuk template internal) ---
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        // $reader->setSecurityScan(false);
        $spreadsheet = $reader->load($templatePath);

        $rowBase = 4;
        $rowStep = 4;

        $deskripsiMap = [
            'volt' => 0,
            'a'    => 1,
            'kw'   => 2,
            'mwh'  => 3,
        ];

        $sheetNames = ['minggu 1', 'minggu 2', 'minggu 3', 'minggu 4', 'minggu 5'];

        foreach ($sheetNames as $weekIndex => $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (!$sheet) continue;

            // Tambahkan info Bulan dan Tahun
            $sheet->setCellValue('S1', 'Bulan : ' . $startDate->translatedFormat('F'));
            $sheet->setCellValue('S2', 'Tahun : ' . $startDate->year);

            $weekStart = $startDate->copy()->addDays($weekIndex * 7);
            $weekEnd = $weekStart->copy()->addDays(6);
            if ($weekEnd->gt($endDate)) {
                $weekEnd = $endDate;
            }

            foreach ($panels as $panelIndex => $panel) {
                $data = PemakaianListrikModel::where('panel_type', $panel)
                ->whereBetween('waktu', [$weekStart, $weekEnd])
                    ->orderBy('waktu')
                    ->get()
                    ->groupBy(fn ($item) => Carbon::parse($item->waktu)->day);

                $col = 7 + $panelIndex;

                for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
                    $tanggal = $weekStart->copy()->addDays($dayOffset);
                    if ($tanggal->gt($weekEnd)) break;

                    $rowStart = $rowBase + ($dayOffset * $rowStep);

                    if ($panelIndex === 0) {
                        $sheet->setCellValue("B{$rowStart}", $tanggal->format('d-m-Y'));

                        $jamEntry = PemakaianListrikModel::where('panel_type', 'SDP14')
                        ->whereDate('waktu', $tanggal->toDateString())
                            ->orderBy('created_at')
                            ->first();

                        if ($jamEntry) {
                            $jam = Carbon::parse($jamEntry->created_at)->format('H:i:s');
                            $sheet->setCellValue("C{$rowStart}", $jam);
                        }
                    }

                    $entries = $data->get($tanggal->day);
                    if ($entries && $entries->isNotEmpty()) {
                        $entry = $entries->first();

                        foreach ($deskripsiMap as $field => $offset) {
                            $row = $rowStart + $offset;
                            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                            $sheet->setCellValue("{$colLetter}{$row}", $entry->$field);
                        }

                        if ($panel === 'MDP' && $entry->cos !== null) {
                            $sheet->setCellValue("F{$rowStart}", $entry->cos);
                        }
                    }
                }
            }
        }

        // --- Pastikan folder exports ada dan writable ---
        $exportDir = storage_path('app/exports');
        if (!file_exists($exportDir)) {
            mkdir($exportDir, 0775, true);
        }
        if (!is_writable($exportDir)) {
            chmod($exportDir, 0775);
        }

        $filename = "Laporan_Listrik_{$month}.xlsx";
        $outputPath = $exportDir . '/' . $filename;

        $writer = new Xlsx($spreadsheet);
        $writer->save($outputPath);

        return response()->download($outputPath)->deleteFileAfterSend(true);
    }


    public function getTrendPemakaianListrik(Request $request)
    {
        $tanggal = $request->query('tanggal'); // format: YYYY-MM-DD
        $bulan   = $request->query('bulan');   // format: YYYY-MM

        $query = PemakaianListrikModel::query();

        if ($tanggal) {
            $query->whereDate('waktu', $tanggal);
        } elseif ($bulan) {
            $query->whereYear('waktu', substr($bulan, 0, 4))
                ->whereMonth('waktu', substr($bulan, 5, 2));
        } else {
            $query->whereYear('waktu', now()->format('Y'))
                ->whereMonth('waktu', now()->format('m'));
        }

        // Ambil data per panel_type dan tanggal, lalu hitung delta mwh antar hari berikutnya
        $data = $query->select('panel_type', 'waktu', 'mwh')
        ->orderBy('panel_type')
        ->orderBy('waktu')
        ->get()
        ->groupBy('panel_type');

        $result = [];

        foreach ($data as $panel => $records) {
            $recordsByDate = $records->groupBy(fn ($r) => \Carbon\Carbon::parse($r->waktu)->format('Y-m-d'));
            $dates = $recordsByDate->keys();

            $series = [];
            for ($i = 0; $i < count($dates) - 1; $i++) {
                $d1 = $dates[$i];
                $d2 = $dates[$i + 1];

                $mwh1 = optional($recordsByDate[$d1]->first())->mwh;
                $mwh2 = optional($recordsByDate[$d2]->first())->mwh;

                if (!is_null($mwh1) && !is_null($mwh2) && $mwh2 >= $mwh1) {
                    $usage = round($mwh2 - $mwh1, 3);
                    $series[] = [
                        'x' => $d1,
                        'y' => $usage
                    ];
                }
            }

            if (!empty($series)) {
                $result[] = [
                    'name' => $panel,
                    'data' => $series
                ];
            }
        }

        return response()->json($result);
    }


    public function getTopJenisPemakaianListrik(Request $request)
    {
        $start = $request->query('start_date');
        $end = $request->query('end_date');

        if (!$start || !$end) {
            $start = now()->startOfMonth()->format('Y-m-d');
            $end = now()->endOfMonth()->format('Y-m-d');
        }

        $panelTypes = PemakaianListrikModel::whereBetween('waktu', [$start, $end])
        ->groupBy('panel_type')
        ->pluck('panel_type');

        $usages = [];

        foreach ($panelTypes as $panel) {
            $data = PemakaianListrikModel::where('panel_type', $panel)
            ->whereBetween('waktu', [$start, $end])
                ->orderBy('waktu')
                ->pluck('mwh')
                ->values();

            $totalUsage = 0;
            for ($i = 0; $i < $data->count() - 1; $i++) {
                $delta = $data[$i + 1] - $data[$i];
                if ($delta >= 0) $totalUsage += $delta;
            }

            $usages[] = [
                'panel_type'   => $panel,
                'total_usage'  => round($totalUsage, 2),
                'start_date'   => $start,
                'end_date'     => $end
            ];
        }

        return response()->json(collect($usages)->sortByDesc('total_usage')->values());
    }

    public function getTopOperatorPemakaianListrik(Request $request)
    {
        $bulan = $request->query('bulan'); // contoh: 2025-06

        $tahun = $bulan ? substr($bulan, 0, 4) : now()->format('Y');
        $bulanAngka = $bulan ? substr($bulan, 5, 2) : now()->format('m');

        $data = PemakaianListrikModel::query()
            ->select(
                'operator',
                DB::raw('COUNT(*) as jumlah_pengisian')
            )
            ->whereMonth('waktu', $bulanAngka)
            ->whereYear('waktu', $tahun)
            ->groupBy('operator')
            ->orderByDesc('jumlah_pengisian')
            // ->limit(5)
            ->get();

        return response()->json($data);
    }

    public function updateListrik(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'panel_type' => 'required|string',
            'volt' => 'nullable|numeric',
            'a' => 'nullable|numeric',
            'kw' => 'nullable|numeric',
            'mwh' => 'nullable|numeric',
            'cos' => 'nullable|numeric',
        ]);

        $data = PemakaianListrikModel::whereDate('waktu', $request->tanggal)
            ->where('panel_type', $request->panel_type)
            ->first();

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $data->update([
            'volt' => $request->volt,
            'a' => $request->a,
            'kw' => $request->kw,
            'mwh' => $request->mwh,
            'cos' => $request->cos,
        ]);

        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }


}
