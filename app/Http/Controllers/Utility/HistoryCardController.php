<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\UtilityHistoryCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HistoryCardController extends Controller
{
    /**
     * Display the History Card input form.
     */
    public function index(Request $request)
    {
        $areas = UtilityHistoryCard::AREAS;
        $defaultArea = $request->query('area', $areas[0]);

        return view('utility.history-card.form', compact('areas', 'defaultArea'));
    }

    /**
     * Display the History Card data / card sheet view.
     */
    public function dataView(Request $request)
    {
        $areas = UtilityHistoryCard::AREAS;
        $selectedArea = $request->query('area', '');
        $selectedMonth = $request->query('bulan', date('m'));
        $selectedYear = $request->query('tahun', date('Y'));
        $startDate = $request->query('start_date', '');
        $endDate = $request->query('end_date', '');

        return view('utility.history-card.data', compact(
            'areas',
            'selectedArea',
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get JSON data for AJAX requests / Datatable / live preview.
     */
    public function getData(Request $request)
    {
        $query = UtilityHistoryCard::with('creator')->orderBy('tanggal', 'desc')->orderBy('jam', 'desc');

        if ($request->filled('area')) {
            $query->where('area', $request->area);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun)
                ->whereMonth('tanggal', $request->bulan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                    ->orWhere('area', 'like', "%{$search}%")
                    ->orWhereHas('creator', function ($qc) use ($search) {
                        $qc->where('username', 'like', "%{$search}%")
                            ->orWhere('fullname', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = (int) $request->input('per_page', 25);
        $paginator = $query->paginate($perPage);

        $data = collect($paginator->items())->map(function ($item) {
            $teknisiName = $item->creator
                ? ($item->creator->fullname ?: ($item->creator->nama ?? $item->creator->username))
                : '-';

            return [
                'id'                => $item->id,
                'tanggal'           => $item->tanggal ? Carbon::parse($item->tanggal)->format('Y-m-d') : '',
                'tanggal_formatted' => $item->tanggal ? Carbon::parse($item->tanggal)->format('d/m/Y') : '',
                'jam'               => substr($item->jam, 0, 5),
                'jam_formatted'     => substr($item->jam, 0, 5) . ' WIB',
                'area'              => $item->area,
                'deskripsi'         => $item->deskripsi,
                'teknisi'           => $teknisiName,
            ];
        });

        return response()->json([
            'status'     => true,
            'data'       => $data,
            'pagination' => [
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'from'         => $paginator->firstItem() ?? 0,
                'to'           => $paginator->lastItem() ?? 0,
            ],
        ]);
    }

    /**
     * Store a newly created History Card record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'   => 'required|date',
            'jam'       => 'required|string',
            'area'      => 'required|string|max:150',
            'deskripsi' => 'required|string',
        ]);

        // Format custom time to H:i (WIB)
        $jam = $this->parseWibTime($validated['jam']);

        $record = UtilityHistoryCard::create([
            'tanggal'    => $validated['tanggal'],
            'jam'        => $jam,
            'area'       => $validated['area'],
            'deskripsi'  => $validated['deskripsi'],
            'created_by' => Auth::id(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => true,
                'message' => 'Data History Card berhasil disimpan!',
                'data'    => $record,
            ]);
        }

        return redirect()->back()->with('success', 'Data History Card berhasil disimpan!');
    }

    /**
     * Show a specific History Card item.
     */
    public function show($id)
    {
        $record = UtilityHistoryCard::with('creator')->findOrFail($id);
        $teknisi = $record->creator
            ? ($record->creator->fullname ?: ($record->creator->nama ?? $record->creator->username))
            : '-';

        $data = $record->toArray();
        $data['teknisi'] = $teknisi;

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    /**
     * Update a specific History Card record.
     */
    public function update(Request $request, $id)
    {
        $record = UtilityHistoryCard::findOrFail($id);

        $validated = $request->validate([
            'tanggal'   => 'required|date',
            'jam'       => 'required|string',
            'area'      => 'required|string|max:150',
            'deskripsi' => 'required|string',
        ]);

        $jam = $this->parseWibTime($validated['jam']);

        $record->update([
            'tanggal'    => $validated['tanggal'],
            'jam'        => $jam,
            'area'       => $validated['area'],
            'deskripsi'  => $validated['deskripsi'],
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Data History Card berhasil diperbarui!',
            'data'    => $record,
        ]);
    }

    /**
     * Parse and normalize custom time string to H:i format (WIB).
     */
    private function parseWibTime($timeInput)
    {
        $rawJam = trim($timeInput);
        // Remove WIB/wib/Wita/Wit
        $cleanJam = trim(preg_replace('/wib|wita|wit/i', '', $rawJam));
        // Replace dot with colon (e.g., 08.30 -> 08:30)
        $cleanJam = str_replace('.', ':', $cleanJam);

        if (preg_match('/^(\d{1,2}):(\d{2})/', $cleanJam, $matches)) {
            return sprintf('%02d:%02d', (int)$matches[1], (int)$matches[2]);
        }

        try {
            return Carbon::parse($cleanJam)->format('H:i');
        } catch (\Throwable $e) {
            return now()->timezone('Asia/Jakarta')->format('H:i');
        }
    }

    /**
     * Remove the specified History Card record.
     */
    public function destroy($id)
    {
        $record = UtilityHistoryCard::findOrFail($id);
        $record->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data History Card berhasil dihapus!',
        ]);
    }

    /**
     * Printable sheet format of the History Card (matching FRM/EUT/01/009/001-00).
     */
    public function printCard(Request $request)
    {
        $areas = UtilityHistoryCard::AREAS;
        $area = $request->query('area', $areas[0]);
        $month = $request->query('bulan', '');
        $year = $request->query('tahun', '');
        $startDate = $request->query('start_date', '');
        $endDate = $request->query('end_date', '');

        $query = UtilityHistoryCard::with('creator')->orderBy('tanggal', 'asc')->orderBy('jam', 'asc');

        if (!empty($area)) {
            $query->where('area', $area);
        }

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif (!empty($month) && !empty($year)) {
            $query->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
        }

        $records = $query->get();

        return view('utility.history-card.print', compact('areas', 'area', 'records', 'month', 'year', 'startDate', 'endDate'));
    }

    /**
     * Export History Card data to CSV.
     */
    public function export(Request $request)
    {
        $area = $request->query('area', '');
        $startDate = $request->query('start_date', '');
        $endDate = $request->query('end_date', '');
        $month = $request->query('bulan', '');
        $year = $request->query('tahun', '');

        $query = UtilityHistoryCard::with('creator')->orderBy('tanggal', 'asc')->orderBy('jam', 'asc');

        if (!empty($area)) {
            $query->where('area', $area);
        }
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif (!empty($month) && !empty($year)) {
            $query->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
        }

        $records = $query->get();

        $filename = 'History_Card_' . ($area ? str_replace([' ', ',', '-'], '_', $area) : 'All_Area') . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($records, $area) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['PT BUMI ALAM SEGAR']);
            fputcsv($file, ['HISTORY CARD']);
            fputcsv($file, ['Area: ' . ($area ?: 'Semua Area')]);
            fputcsv($file, ['Form No: FRM/EUT/01/009/001-00']);
            fputcsv($file, []);
            fputcsv($file, ['No', 'Tanggal', 'Jam', 'Area', 'Deskripsi', 'Teknisi']);

            foreach ($records as $idx => $r) {
                $teknisiName = $r->creator
                    ? ($r->creator->fullname ?: ($r->creator->nama ?? $r->creator->username))
                    : '-';

                fputcsv($file, [
                    $idx + 1,
                    Carbon::parse($r->tanggal)->format('d/m/Y'),
                    Carbon::parse($r->jam)->format('H:i'),
                    $r->area,
                    $r->deskripsi,
                    $teknisiName,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
