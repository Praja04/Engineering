<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\EngUtilityProject;
use App\Models\Utility\EngUtilityProjectLog;
use App\Models\Utility\EngUtilityProjectArea;
use App\Models\Utility\EngUtilityProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class EngUtilityProjectController extends Controller
{
    /**
     * 1. Dashboard Project Utility (Separated as requested, Clean Light Bootstrap Theme)
     */
    public function dashboard(Request $request)
    {
        $areaFilter = $request->get('area');
        $kategoriFilter = $request->get('kategori');
        $priorityFilter = $request->get('priority');
        $statusFilter = $request->get('status');
        $yearFilter = $request->get('year');

        $query = EngUtilityProject::query();

        if (!empty($areaFilter) && $areaFilter !== 'all') {
            $query->where('area', $areaFilter);
        }
        if (!empty($kategoriFilter) && $kategoriFilter !== 'all') {
            $query->where('kategori', $kategoriFilter);
        }
        if (!empty($priorityFilter) && $priorityFilter !== 'all') {
            $query->where('priority', $priorityFilter);
        }
        if (!empty($statusFilter) && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }
        if (!empty($yearFilter) && $yearFilter !== 'all') {
            $query->where(function ($q) use ($yearFilter) {
                $q->whereYear('finish_date', $yearFilter)
                  ->orWhereYear('start_date', $yearFilter)
                  ->orWhereYear('created_at', $yearFilter);
            });
        }

        $allProjects = $query->get();
        $totalProjects = $allProjects->count();

        $selesaiCount = $allProjects->where('status', 'Selesai')->count();
        $onProgressCount = $allProjects->where('status', 'On Progress')->count();
        $terlambatCount = $allProjects->where('status', 'Terlambat')->count();

        $selesaiPct = $totalProjects > 0 ? round(($selesaiCount / $totalProjects) * 100, 1) : 0;
        $onProgressPct = $totalProjects > 0 ? round(($onProgressCount / $totalProjects) * 100, 1) : 0;
        $terlambatPct = $totalProjects > 0 ? round(($terlambatCount / $totalProjects) * 100, 1) : 0;
        $avgProgress = $totalProjects > 0 ? round($allProjects->avg('progress'), 0) : 0;

        $uniqueCategories = $allProjects->pluck('kategori')->filter()->unique()->values();
        $activeCategoryCount = $uniqueCategories->count();

        $kpis = [
            'total' => $totalProjects,
            'active_categories' => $activeCategoryCount,
            'selesai' => $selesaiCount,
            'selesai_pct' => $selesaiPct,
            'on_progress' => $onProgressCount,
            'on_progress_pct' => $onProgressPct,
            'terlambat' => $terlambatCount,
            'terlambat_pct' => $terlambatPct,
            'avg_progress' => $avgProgress,
        ];

        // Chart 1: Status Summary (Donut)
        $chartStatusSummary = [
            'series' => [$selesaiCount, $onProgressCount, $terlambatCount],
            'labels' => ['Selesai', 'On Progress', 'Terlambat'],
            'percentages' => [
                'selesai' => $selesaiPct,
                'on_progress' => $onProgressPct,
                'terlambat' => $terlambatPct,
            ]
        ];

        // Chart 2: Status by Kategori (Horizontal Stacked Bar)
        $kategoriGroup = $allProjects->groupBy('kategori')->sortByDesc(function ($group) {
            return $group->count();
        });

        $catLabels = [];
        $catSelesai = [];
        $catOnProgress = [];
        $catTerlambat = [];

        foreach ($kategoriGroup as $katName => $items) {
            $catLabels[] = "{$katName} (" . $items->count() . ")";
            $catSelesai[] = $items->where('status', 'Selesai')->count();
            $catOnProgress[] = $items->where('status', 'On Progress')->count();
            $catTerlambat[] = $items->where('status', 'Terlambat')->count();
        }

        $chartStatusByKategori = [
            'categories' => $catLabels,
            'series' => [
                ['name' => 'Selesai', 'data' => $catSelesai, 'color' => '#0ab39c'],
                ['name' => 'On Progress', 'data' => $catOnProgress, 'color' => '#f7b84b'],
                ['name' => 'Terlambat', 'data' => $catTerlambat, 'color' => '#f06548'],
            ]
        ];

        // Chart 3: Status by Area / PIC (Horizontal Stacked Bar)
        $definedAreas = EngUtilityProjectArea::where('is_active', true)->pluck('nama_area')->toArray();
        if (empty($definedAreas)) {
            $definedAreas = ['Boiler', 'Utility', 'WWTP', 'MTC'];
        }

        $areaLabels = [];
        $areaSelesai = [];
        $areaOnProgress = [];
        $areaTerlambat = [];

        foreach ($definedAreas as $aName) {
            $aItems = $allProjects->where('area', $aName);
            $areaLabels[] = "{$aName} (" . $aItems->count() . ")";
            $areaSelesai[] = $aItems->where('status', 'Selesai')->count();
            $areaOnProgress[] = $aItems->where('status', 'On Progress')->count();
            $areaTerlambat[] = $aItems->where('status', 'Terlambat')->count();
        }

        $chartStatusByArea = [
            'categories' => $areaLabels,
            'series' => [
                ['name' => 'Selesai', 'data' => $areaSelesai, 'color' => '#0ab39c'],
                ['name' => 'On Progress', 'data' => $areaOnProgress, 'color' => '#f7b84b'],
                ['name' => 'Terlambat', 'data' => $areaTerlambat, 'color' => '#f06548'],
            ]
        ];

        // Priority Projects: Diurutkan secara spesifik berdasarkan PRIORITY (Critical -> High -> Medium -> Low), lalu finish_date / progress
        $attentionQuery = (clone $query);
        if (empty($statusFilter) || $statusFilter === 'all') {
            $attentionQuery->where('status', '!=', 'Selesai');
        }

        $attentionProjects = $attentionQuery
            ->orderByRaw("CASE 
                WHEN priority = 'Critical' THEN 1 
                WHEN priority = 'High' THEN 2 
                WHEN priority = 'Medium' THEN 3 
                WHEN priority = 'Low' THEN 4 
                ELSE 5 END ASC")
            ->orderBy('progress', 'asc')
            ->take(8)
            ->get();

        // Support AJAX JSON response for dynamic jQuery dashboard loading
        if ($request->ajax() || $request->wantsJson() || $request->get('format') === 'json') {
            return response()->json([
                'success' => true,
                'kpis' => $kpis,
                'chartStatusSummary' => $chartStatusSummary,
                'chartStatusByKategori' => $chartStatusByKategori,
                'chartStatusByArea' => $chartStatusByArea,
                'attentionProjects' => $attentionProjects->map(function ($prj) {
                    return [
                        'id' => $prj->id,
                        'nama_project' => $prj->nama_project,
                        'nomor_project' => $prj->nomor_project,
                        'area' => $prj->area,
                        'kategori' => $prj->kategori,
                        'priority' => $prj->priority ?: 'Medium',
                        'progress' => (int) $prj->progress,
                        'status' => $prj->status,
                        'finish_date' => $prj->finish_date ? Carbon::parse($prj->finish_date)->format('d/m/Y') : ($prj->finish_week ?: '-'),
                        'latest_note' => $prj->latest_note,
                    ];
                })
            ]);
        }

        $categoriesList = EngUtilityProjectCategory::where('is_active', true)->orderBy('nama_kategori')->pluck('nama_kategori')->toArray();
        if (empty($categoriesList)) {
            $categoriesList = EngUtilityProject::pluck('kategori')->filter()->unique()->values()->toArray();
        }

        $areasList = EngUtilityProjectArea::where('is_active', true)->orderBy('nama_area')->pluck('nama_area')->toArray();
        if (empty($areasList)) {
            $areasList = ['Boiler', 'Utility', 'WWTP', 'MTC'];
        }

        // Available years for filter
        $yearsList = collect([
            Carbon::now()->year,
            Carbon::now()->subYear()->year,
            Carbon::now()->addYear()->year,
        ])->merge(
            EngUtilityProject::whereNotNull('finish_date')->pluck('finish_date')->map(fn($d) => Carbon::parse($d)->year)
        )->unique()->sortDesc()->values()->toArray();

        return view('dashboard.utility.project.dashboard_project', compact(
            'kpis',
            'chartStatusSummary',
            'chartStatusByKategori',
            'chartStatusByArea',
            'attentionProjects',
            'categoriesList',
            'areasList',
            'yearsList',
            'areaFilter',
            'kategoriFilter',
            'priorityFilter',
            'statusFilter',
            'yearFilter'
        ));
    }

    /**
     * 2. Data Project Utility (Clean Light Bootstrap Table & Tracking)
     */
    public function data(Request $request)
    {
        $statusFilter = $request->get('status');
        $kategoriFilter = $request->get('kategori');
        $areaFilter = $request->get('area');
        $priorityFilter = $request->get('priority');
        $search = $request->get('search');

        $query = EngUtilityProject::with(['logs.user', 'creator'])->orderBy('created_at', 'desc');

        if (!empty($statusFilter) && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if (!empty($kategoriFilter) && $kategoriFilter !== 'all') {
            $query->where('kategori', $kategoriFilter);
        }

        if (!empty($areaFilter) && $areaFilter !== 'all') {
            $query->where('area', $areaFilter);
        }

        if (!empty($priorityFilter) && $priorityFilter !== 'all') {
            $query->where('priority', $priorityFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_project', 'like', "%{$search}%")
                  ->orWhere('nomor_project', 'like', "%{$search}%")
                  ->orWhere('target_result', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhere('priority', 'like', "%{$search}%")
                  ->orWhere('latest_note', 'like', "%{$search}%");
            });
        }

        $projects = $query->get();

        $totalProjectsCount = EngUtilityProject::count();

        // Support AJAX JSON response for dynamic jQuery table loading
        if ($request->ajax() || $request->wantsJson() || $request->get('format') === 'json') {
            return response()->json([
                'success' => true,
                'data' => $projects->map(function ($item, $index) {
                    return [
                        'id' => $item->id,
                        'no' => $index + 1,
                        'nama_project' => $item->nama_project,
                        'nomor_project' => $item->nomor_project,
                        'target_result' => $item->target_result,
                        'area' => $item->area,
                        'kategori' => $item->kategori,
                        'priority' => $item->priority ?: 'Medium',
                        'start_date' => $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d/m/y') : null,
                        'start_week' => $item->start_week,
                        'finish_date' => $item->finish_date ? \Carbon\Carbon::parse($item->finish_date)->format('d/m/y') : null,
                        'finish_week' => $item->finish_week,
                        'progress' => (int) $item->progress,
                        'status' => $item->status,
                        'latest_note' => $item->latest_note,
                        'edit_url' => route('utility.project.edit', $item->id),
                    ];
                }),
                'stats' => [
                    'total' => $totalProjectsCount,
                    'filtered' => $projects->count(),
                    'selesai' => $projects->where('status', 'Selesai')->count(),
                    'on_progress' => $projects->where('status', 'On Progress')->count(),
                    'terlambat' => $projects->where('status', 'Terlambat')->count(),
                ]
            ]);
        }

        $categoriesList = EngUtilityProjectCategory::where('is_active', true)->orderBy('nama_kategori')->pluck('nama_kategori')->toArray();
        if (empty($categoriesList)) {
            $categoriesList = EngUtilityProject::pluck('kategori')->filter()->unique()->values()->toArray();
        }

        $areasList = EngUtilityProjectArea::where('is_active', true)->orderBy('nama_area')->pluck('nama_area')->toArray();
        if (empty($areasList)) {
            $areasList = ['Boiler', 'Utility', 'WWTP', 'MTC'];
        }

        return view('dashboard.utility.project.data_project', compact(
            'projects',
            'categoriesList',
            'areasList',
            'totalProjectsCount',
            'statusFilter',
            'kategoriFilter',
            'areaFilter',
            'priorityFilter',
            'search'
        ));
    }

    /**
     * 3. Form Input Project (Clean Light Bootstrap Form matching standard forms)
     */
    public function create()
    {
        $categories = EngUtilityProjectCategory::where('is_active', true)->orderBy('nama_kategori')->pluck('nama_kategori')->toArray();
        if (empty($categories)) {
            $categories = [
                'Maintenance', 'KPI BB dan Steam', 'Baku Mutu', 'Compliance', '5R',
                'KPI Steam', 'Improvement', 'Cost Saving', 'KPI Air', 'Digitalisasi', 'KPI Korektif'
            ];
        }

        $areas = EngUtilityProjectArea::where('is_active', true)->orderBy('nama_area')->pluck('nama_area')->toArray();
        if (empty($areas)) {
            $areas = ['Boiler', 'Utility', 'WWTP', 'MTC'];
        }

        return view('dashboard.utility.project.form_project', compact('categories', 'areas'));
    }

    /**
     * Store new project
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_project' => 'required|string|max:255',
            'area' => 'required|string|max:100',
            'kategori' => 'required|string|max:100',
            'priority' => 'nullable|string|in:Critical,High,Medium,Low',
            'target_result' => 'nullable|string',
            'start_date' => 'nullable|date',
            'start_week' => 'nullable|string|max:50',
            'finish_date' => 'nullable|date',
            'finish_week' => 'nullable|string|max:50',
            'progress' => 'nullable|integer|min:0|max:100',
            'status' => 'nullable|string|in:Selesai,On Progress,Terlambat',
            'latest_note' => 'nullable|string',
        ]);

        $userId = Auth::id() ?: 1;
        $progress = (int) $request->get('progress', 0);

        $status = $request->status;
        if (!$status) {
            if ($progress >= 100) {
                $status = 'Selesai';
            } elseif ($request->finish_date && Carbon::parse($request->finish_date)->isPast() && $progress < 100) {
                $status = 'Terlambat';
            } else {
                $status = 'On Progress';
            }
        }

        $nomor = $request->nomor_project;
        if (empty($nomor)) {
            $prefix = strtoupper(substr($request->area, 0, 3));
            $seq = EngUtilityProject::where('area', $request->area)->count() + 1;
            $nomor = 'PRJ-' . $prefix . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
        }

        $project = EngUtilityProject::create([
            'nomor_project' => $nomor,
            'nama_project' => $request->nama_project,
            'target_result' => $request->target_result,
            'area' => $request->area,
            'kategori' => $request->kategori,
            'priority' => $request->get('priority', 'Medium') ?: 'Medium',
            'start_date' => $request->start_date,
            'start_week' => $request->start_week,
            'finish_date' => $request->finish_date,
            'finish_week' => $request->finish_week,
            'progress' => $progress,
            'status' => $status,
            'latest_note' => $request->latest_note,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        if ($progress > 0 || !empty($request->latest_note)) {
            EngUtilityProjectLog::create([
                'project_id' => $project->id,
                'progress_sebelumnya' => 0,
                'progress_baru' => $progress,
                'catatan' => $request->latest_note ?: 'Inisiasi awal project',
                'tanggal' => $request->start_date ?: date('Y-m-d'),
                'created_by' => $userId,
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project berhasil ditambahkan!',
                'project' => $project
            ]);
        }

        return redirect()->route('utility.project.data')->with('success', 'Project baru berhasil disimpan!');
    }

    /**
     * Edit project form
     */
    public function edit($id)
    {
        $project = EngUtilityProject::findOrFail($id);

        $categories = EngUtilityProjectCategory::where('is_active', true)->orderBy('nama_kategori')->pluck('nama_kategori')->toArray();
        if (empty($categories)) {
            $categories = [
                'Maintenance', 'KPI BB dan Steam', 'Baku Mutu', 'Compliance', '5R',
                'KPI Steam', 'Improvement', 'Cost Saving', 'KPI Air', 'Digitalisasi', 'KPI Korektif'
            ];
        }

        $areas = EngUtilityProjectArea::where('is_active', true)->orderBy('nama_area')->pluck('nama_area')->toArray();
        if (empty($areas)) {
            $areas = ['Boiler', 'Utility', 'WWTP', 'MTC'];
        }

        return view('dashboard.utility.project.form_project', compact('project', 'categories', 'areas'));
    }

    /**
     * Update project
     */
    public function update(Request $request, $id)
    {
        $project = EngUtilityProject::findOrFail($id);

        $request->validate([
            'nama_project' => 'required|string|max:255',
            'area' => 'required|string|max:100',
            'kategori' => 'required|string|max:100',
            'priority' => 'nullable|string|in:Critical,High,Medium,Low',
            'target_result' => 'nullable|string',
            'start_date' => 'nullable|date',
            'start_week' => 'nullable|string|max:50',
            'finish_date' => 'nullable|date',
            'finish_week' => 'nullable|string|max:50',
            'progress' => 'nullable|integer|min:0|max:100',
            'status' => 'nullable|string|in:Selesai,On Progress,Terlambat',
            'latest_note' => 'nullable|string',
        ]);

        $userId = Auth::id() ?: 1;
        $oldProgress = (int) $project->progress;
        $newProgress = (int) $request->get('progress', $oldProgress);

        $status = $request->status;
        if (!$status) {
            if ($newProgress >= 100) {
                $status = 'Selesai';
            } elseif ($request->finish_date && Carbon::parse($request->finish_date)->isPast() && $newProgress < 100) {
                $status = 'Terlambat';
            } else {
                $status = 'On Progress';
            }
        }

        $project->update([
            'nomor_project' => $request->nomor_project ?: $project->nomor_project,
            'nama_project' => $request->nama_project,
            'target_result' => $request->target_result,
            'area' => $request->area,
            'kategori' => $request->kategori,
            'priority' => $request->get('priority', $project->priority ?? 'Medium') ?: 'Medium',
            'start_date' => $request->start_date,
            'start_week' => $request->start_week,
            'finish_date' => $request->finish_date,
            'finish_week' => $request->finish_week,
            'progress' => $newProgress,
            'status' => $status,
            'latest_note' => $request->latest_note,
            'updated_by' => $userId,
        ]);

        if ($oldProgress !== $newProgress || !empty($request->catatan_update)) {
            EngUtilityProjectLog::create([
                'project_id' => $project->id,
                'progress_sebelumnya' => $oldProgress,
                'progress_baru' => $newProgress,
                'catatan' => $request->catatan_update ?: $request->latest_note,
                'tanggal' => date('Y-m-d'),
                'created_by' => $userId,
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project berhasil diupdate!',
                'project' => $project
            ]);
        }

        return redirect()->route('utility.project.data')->with('success', 'Project berhasil diperbarui!');
    }

    /**
     * Delete project
     */
    public function destroy($id)
    {
        $project = EngUtilityProject::findOrFail($id);
        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project berhasil dihapus.'
        ]);
    }

    /**
     * 4. Update progress from user modal with history recording
     */
    public function updateProgress(Request $request, $id)
    {
        $project = EngUtilityProject::findOrFail($id);

        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'catatan' => 'required|string|max:1000',
            'tanggal' => 'nullable|date',
        ]);

        $userId = Auth::id() ?: 1;
        $oldProgress = (int) $project->progress;
        $newProgress = (int) $request->progress;
        $tanggal = $request->tanggal ? Carbon::parse($request->tanggal)->format('Y-m-d') : date('Y-m-d');

        $status = $project->status;
        if ($newProgress >= 100) {
            $status = 'Selesai';
        } elseif ($project->finish_date && Carbon::parse($project->finish_date)->isPast()) {
            $status = 'Terlambat';
        } else {
            $status = 'On Progress';
        }

        $project->update([
            'progress' => $newProgress,
            'status' => $status,
            'latest_note' => $request->catatan,
            'updated_by' => $userId,
        ]);

        $log = EngUtilityProjectLog::create([
            'project_id' => $project->id,
            'progress_sebelumnya' => $oldProgress,
            'progress_baru' => $newProgress,
            'catatan' => $request->catatan,
            'tanggal' => $tanggal,
            'created_by' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Progress project berhasil diperbarui!',
            'data' => [
                'project_id' => $project->id,
                'progress' => $newProgress,
                'status' => $status,
                'latest_note' => $request->catatan,
                'log' => $log,
            ]
        ]);
    }

    /**
     * Get history logs for a project (for modal display)
     */
    public function history($id)
    {
        $project = EngUtilityProject::findOrFail($id);
        $logs = EngUtilityProjectLog::with('user')
            ->where('project_id', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal)->format('d M Y') : $item->created_at->format('d M Y'),
                    'jam' => $item->created_at->format('H:i'),
                    'progress_sebelumnya' => $item->progress_sebelumnya,
                    'progress_baru' => $item->progress_baru,
                    'catatan' => $item->catatan,
                    'user_nama' => ($item->user && !empty($item->user->name)) ? $item->user->name : 'Engineering Team',
                ];
            });

        return response()->json([
            'success' => true,
            'project' => [
                'id' => $project->id,
                'nama_project' => $project->nama_project,
                'nomor_project' => $project->nomor_project,
                'area' => $project->area,
                'progress' => $project->progress,
                'status' => $project->status,
            ],
            'logs' => $logs
        ]);
    }

    /**
     * Master Data Management (Area & Kategori)
     */
    public function master(Request $request)
    {
        $areas = EngUtilityProjectArea::withCount('projects')->orderBy('nama_area')->get();
        $categories = EngUtilityProjectCategory::withCount('projects')->orderBy('nama_kategori')->get();

        if ($request->ajax() || $request->wantsJson() || $request->get('format') === 'json') {
            return response()->json([
                'success' => true,
                'areas' => $areas,
                'categories' => $categories
            ]);
        }

        return view('dashboard.utility.project.master_project', compact('areas', 'categories'));
    }

    public function storeArea(Request $request)
    {
        $request->validate([
            'nama_area' => 'required|string|max:100|unique:eng_utility_project_areas,nama_area',
            'kode_area' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
        ]);

        $area = EngUtilityProjectArea::create([
            'nama_area' => $request->nama_area,
            'kode_area' => $request->kode_area ?: strtoupper(substr($request->nama_area, 0, 4)),
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Area baru berhasil ditambahkan!', 'data' => $area]);
        }
        return redirect()->route('utility.project.master')->with('success', 'Area baru berhasil disimpan!');
    }

    public function updateArea(Request $request, $id)
    {
        $area = EngUtilityProjectArea::findOrFail($id);
        $request->validate([
            'nama_area' => 'required|string|max:100|unique:eng_utility_project_areas,nama_area,' . $id,
            'kode_area' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
        ]);

        $oldName = $area->nama_area;
        $area->update([
            'nama_area' => $request->nama_area,
            'kode_area' => $request->kode_area ?: $area->kode_area,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $area->is_active,
        ]);

        // Sync existing projects if area name changed
        if ($oldName !== $request->nama_area) {
            EngUtilityProject::where('area', $oldName)->update(['area' => $request->nama_area]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Area berhasil diperbarui!', 'data' => $area]);
        }
        return redirect()->route('utility.project.master')->with('success', 'Area berhasil diperbarui!');
    }

    public function destroyArea($id)
    {
        $area = EngUtilityProjectArea::findOrFail($id);
        $count = EngUtilityProject::where('area', $area->nama_area)->count();
        if ($count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Area '{$area->nama_area}' tidak dapat dihapus karena masih digunakan oleh {$count} project aktif."
            ], 422);
        }

        $area->delete();
        return response()->json(['success' => true, 'message' => 'Area berhasil dihapus.']);
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:150|unique:eng_utility_project_categories,nama_kategori',
            'deskripsi' => 'nullable|string',
        ]);

        $category = EngUtilityProjectCategory::create([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Kategori baru berhasil ditambahkan!', 'data' => $category]);
        }
        return redirect()->route('utility.project.master')->with('success', 'Kategori baru berhasil disimpan!');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = EngUtilityProjectCategory::findOrFail($id);
        $request->validate([
            'nama_kategori' => 'required|string|max:150|unique:eng_utility_project_categories,nama_kategori,' . $id,
            'deskripsi' => 'nullable|string',
        ]);

        $oldName = $category->nama_kategori;
        $category->update([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $category->is_active,
        ]);

        // Sync existing projects if category name changed
        if ($oldName !== $request->nama_kategori) {
            EngUtilityProject::where('kategori', $oldName)->update(['kategori' => $request->nama_kategori]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Kategori berhasil diperbarui!', 'data' => $category]);
        }
        return redirect()->route('utility.project.master')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroyCategory($id)
    {
        $category = EngUtilityProjectCategory::findOrFail($id);
        $count = EngUtilityProject::where('kategori', $category->nama_kategori)->count();
        if ($count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Kategori '{$category->nama_kategori}' tidak dapat dihapus karena masih digunakan oleh {$count} project aktif."
            ], 422);
        }

        $category->delete();
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus.']);
    }

    /**
     * Download Excel template for project utility migration
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();

        // ── Sheet 1: Template Data Project ──
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Project');

        $headers = [
            'A1' => 'Nomor Project (Opsional)',
            'B1' => 'Nama Project (Wajib)',
            'C1' => 'Target Result / Output',
            'D1' => 'Area (Wajib)',
            'E1' => 'Kategori (Wajib)',
            'F1' => 'Prioritas (Critical/High/Medium/Low)',
            'G1' => 'Tanggal Mulai (YYYY-MM-DD)',
            'H1' => 'Minggu Mulai (Contoh: W1 Okt 26)',
            'I1' => 'Target Selesai (YYYY-MM-DD)',
            'J1' => 'Minggu Selesai (Contoh: W3 Nov 26)',
            'K1' => 'Progress (%)',
            'L1' => 'Status (Selesai/On Progress/Terlambat)',
            'M1' => 'Catatan Terakhir',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        // Style Header
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1D4ED8'], // Deep Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Sample Data Rows (3 rows of realistic migration data)
        $sampleRows = [
            [
                'PRJ-BOI-001',
                'Overhaul Pompa Feed Water Boiler #1',
                'Peningkatan efisiensi suplai air boiler & zero breakdown',
                'Boiler',
                'Maintenance',
                'High',
                '2026-10-01',
                'W1 Okt 26',
                '2026-10-20',
                'W3 Okt 26',
                '100',
                'Selesai',
                'Penggantian bearing dan mechanical seal selesai diuji running',
            ],
            [
                '', // Nomor kosong -> akan digenerate otomatis
                'Pemasangan Flow Meter Air RO Line 2',
                'Monitoring debit pemakaian air RO secara realtime & akurat',
                'Utility',
                'Digitalisasi',
                'Medium',
                '2026-10-15',
                'W3 Okt 26',
                '2026-11-10',
                'W2 Nov 26',
                '45',
                'On Progress',
                'Pemasangan pipa selesai, modul sensor sedang proses kalibrasi',
            ],
            [
                '',
                'Optimasi Aerasi dan Dosing Bak WWTP',
                'Menjaga parameter COD & BOD limbah cair di bawah baku mutu',
                'WWTP',
                'Baku Mutu',
                'Critical',
                '2026-10-05',
                'W1 Okt 26',
                '2026-10-25',
                'W4 Okt 26',
                '70',
                'Terlambat',
                'Menunggu penggantian diffuser tube aerator yang aus',
            ],
        ];

        $rowIdx = 2;
        foreach ($sampleRows as $row) {
            $colLetter = 'A';
            foreach ($row as $val) {
                $sheet->setCellValueExplicit($colLetter . $rowIdx, (string)$val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $colLetter++;
            }
            $rowIdx++;
        }

        // Style Sample Rows
        $sheet->getStyle('A2:M4')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getStyle('A2:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:J4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K2:L4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        for ($r = 2; $r <= 4; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(22);
        }

        // Auto-fit column widths
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ── Sheet 2: Referensi Master (Area & Kategori) ──
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Referensi Master');

        $refSheet->setCellValue('A1', 'Daftar Area Terdaftar');
        $refSheet->setCellValue('C1', 'Daftar Kategori Terdaftar');
        $refSheet->setCellValue('E1', 'Daftar Prioritas');
        $refSheet->setCellValue('G1', 'Status Project');

        $refSheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '334155']],
        ]);

        $areas = EngUtilityProjectArea::where('is_active', true)->orderBy('nama_area')->pluck('nama_area')->toArray();
        if (empty($areas)) $areas = ['Boiler', 'Utility', 'WWTP', 'MTC'];

        $categories = EngUtilityProjectCategory::where('is_active', true)->orderBy('nama_kategori')->pluck('nama_kategori')->toArray();
        if (empty($categories)) {
            $categories = [
                'Maintenance', 'KPI BB dan Steam', 'Baku Mutu', 'Compliance', '5R',
                'KPI Steam', 'Improvement', 'Cost Saving', 'KPI Air', 'Digitalisasi', 'KPI Korektif'
            ];
        }

        $priorities = ['Critical', 'High', 'Medium', 'Low'];
        $statuses = ['Selesai', 'On Progress', 'Terlambat'];

        $maxRows = max(count($areas), count($categories), count($priorities), count($statuses));
        for ($i = 0; $i < $maxRows; $i++) {
            $currRow = $i + 2;
            if (isset($areas[$i])) $refSheet->setCellValue('A' . $currRow, $areas[$i]);
            if (isset($categories[$i])) $refSheet->setCellValue('C' . $currRow, $categories[$i]);
            if (isset($priorities[$i])) $refSheet->setCellValue('E' . $currRow, $priorities[$i]);
            if (isset($statuses[$i])) $refSheet->setCellValue('G' . $currRow, $statuses[$i]);
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $refSheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set active sheet back to Sheet 1
        $spreadsheet->setActiveSheetIndex(0);

        $fileName = 'Template_Import_Project_Utility.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import projects from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file.mimes' => 'Format file harus berupa Excel (.xlsx, .xls) atau .csv.',
            'file.max' => 'Ukuran file maksimal adalah 10MB.',
        ]);

        $file = $request->file('file');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membaca file Excel: ' . $e->getMessage()
                ], 422);
            }
            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        if (empty($rows)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File Excel kosong atau tidak memiliki data.'
                ], 422);
            }
            return redirect()->back()->with('error', 'File Excel kosong atau tidak memiliki data.');
        }

        // Detect Header Row
        $headerRowIdx = null;
        $headerMap = [];

        foreach ($rows as $rowIndex => $cols) {
            $hasProjectHeader = false;
            foreach ($cols as $colLetter => $val) {
                $cleaned = strtolower(trim((string)$val));
                if (str_contains($cleaned, 'nama project') || str_contains($cleaned, 'nama_project') || $cleaned === 'project' || $cleaned === 'nama') {
                    $hasProjectHeader = true;
                    break;
                }
            }

            if ($hasProjectHeader) {
                $headerRowIdx = $rowIndex;
                foreach ($cols as $colLetter => $val) {
                    $cleaned = strtolower(trim((string)$val));

                    // 1. Status HARUS dicek sebelum progress karena judul status memuat "On Progress"
                    if (str_contains($cleaned, 'status')) {
                        $headerMap['status'] = $colLetter;
                    }
                    // 2. Progress (%)
                    elseif (str_contains($cleaned, 'progress') || str_contains($cleaned, 'progres') || str_contains($cleaned, '%') || str_contains($cleaned, 'persen')) {
                        $headerMap['progress'] = $colLetter;
                    }
                    // 3. Tanggal & Minggu Mulai
                    elseif (str_contains($cleaned, 'mulai') && (str_contains($cleaned, 'minggu') || str_contains($cleaned, 'week'))) {
                        $headerMap['start_week'] = $colLetter;
                    } elseif (str_contains($cleaned, 'mulai') || str_contains($cleaned, 'start date') || str_contains($cleaned, 'start_date')) {
                        $headerMap['start_date'] = $colLetter;
                    }
                    // 4. Tanggal & Minggu Selesai / Target Selesai
                    elseif ((str_contains($cleaned, 'selesai') || str_contains($cleaned, 'finish')) && (str_contains($cleaned, 'minggu') || str_contains($cleaned, 'week'))) {
                        $headerMap['finish_week'] = $colLetter;
                    } elseif (str_contains($cleaned, 'selesai') || str_contains($cleaned, 'finish') || str_contains($cleaned, 'deadline') || str_contains($cleaned, 'due') || (str_contains($cleaned, 'target') && !str_contains($cleaned, 'result') && !str_contains($cleaned, 'hasil') && !str_contains($cleaned, 'output'))) {
                        $headerMap['finish_date'] = $colLetter;
                    }
                    // 5. Data Project Lainnya
                    elseif (str_contains($cleaned, 'nomor') || str_contains($cleaned, 'no project') || str_contains($cleaned, 'kode')) {
                        $headerMap['nomor_project'] = $colLetter;
                    } elseif (str_contains($cleaned, 'nama project') || str_contains($cleaned, 'nama_project') || $cleaned === 'project' || $cleaned === 'nama') {
                        $headerMap['nama_project'] = $colLetter;
                    } elseif (str_contains($cleaned, 'target result') || str_contains($cleaned, 'output') || str_contains($cleaned, 'target hasil') || str_contains($cleaned, 'tujuan')) {
                        $headerMap['target_result'] = $colLetter;
                    } elseif (str_contains($cleaned, 'area') || str_contains($cleaned, 'lokasi') || str_contains($cleaned, 'seksi')) {
                        $headerMap['area'] = $colLetter;
                    } elseif (str_contains($cleaned, 'kategori') || str_contains($cleaned, 'category')) {
                        $headerMap['kategori'] = $colLetter;
                    } elseif (str_contains($cleaned, 'prioritas') || str_contains($cleaned, 'priority')) {
                        $headerMap['priority'] = $colLetter;
                    } elseif (str_contains($cleaned, 'catatan') || str_contains($cleaned, 'note') || str_contains($cleaned, 'keterangan') || str_contains($cleaned, 'remark')) {
                        $headerMap['latest_note'] = $colLetter;
                    }
                }
                break;
            }
        }

        // If no header row detected, default to standard column order
        if ($headerRowIdx === null) {
            $headerRowIdx = 1;
            $headerMap = [
                'nomor_project' => 'A',
                'nama_project' => 'B',
                'target_result' => 'C',
                'area' => 'D',
                'kategori' => 'E',
                'priority' => 'F',
                'start_date' => 'G',
                'start_week' => 'H',
                'finish_date' => 'I',
                'finish_week' => 'J',
                'progress' => 'K',
                'status' => 'L',
                'latest_note' => 'M',
            ];
        }

        $userId = Auth::id() ?: 1;
        $importedCount = 0;
        $updatedCount = 0;

        // Date parser helper
        $parseDate = function ($rawVal) {
            if (empty($rawVal)) return null;
            $rawVal = trim((string)$rawVal);
            if (is_numeric($rawVal) && (float)$rawVal > 1000) {
                try {
                    return ExcelDate::excelToDateTimeObject((float)$rawVal)->format('Y-m-d');
                } catch (\Exception $e) {}
            }
            try {
                return Carbon::parse($rawVal)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        };

        // Helper to convert week string (e.g. 'W1 Okt 26', 'W3 Jun 26', 'W4 Agus 2026', 'Januari-26') to a real Date
        $parseWeekToDate = function ($weekStr, $isEndOfWeek = false) {
            if (empty($weekStr)) return null;
            $weekStr = trim((string)$weekStr);

            $monthsMap = [
                'jan' => 1, 'januari' => 1, 'january' => 1,
                'feb' => 2, 'februari' => 2, 'february' => 2,
                'mar' => 3, 'maret' => 3, 'march' => 3,
                'apr' => 4, 'april' => 4,
                'mei' => 5, 'may' => 5,
                'jun' => 6, 'juni' => 6, 'june' => 6,
                'jul' => 7, 'juli' => 7, 'july' => 7,
                'agu' => 8, 'ags' => 8, 'agt' => 8, 'agus' => 8, 'agustus' => 8, 'aug' => 8, 'august' => 8,
                'sep' => 9, 'sept' => 9, 'september' => 9,
                'okt' => 10, 'oct' => 10, 'oktober' => 10, 'october' => 10,
                'nov' => 11, 'nop' => 11, 'november' => 11,
                'des' => 12, 'dec' => 12, 'desember' => 12, 'december' => 12,
            ];

            $weekNum = 1;
            $month = null;
            $year = Carbon::now()->year;

            // Pattern 1: W1 Okt 26 or W3 Jun 2026 or W2 Juni
            if (preg_match('/W([1-5])\s*([a-zA-Z]+)(?:\s*[-_\/]?\s*(\d{2,4}))?/i', $weekStr, $matches)) {
                $weekNum = (int)$matches[1];
                $mStr = strtolower($matches[2]);
                if (isset($monthsMap[$mStr])) {
                    $month = $monthsMap[$mStr];
                }
                if (!empty($matches[3])) {
                    $yVal = (int)$matches[3];
                    $year = ($yVal < 100) ? 2000 + $yVal : $yVal;
                }
            }
            // Pattern 2: Januari-26 or Mei 2026 or Sep-26
            elseif (preg_match('/([a-zA-Z]+)(?:\s*[-_\/]?\s*(\d{2,4}))?/i', $weekStr, $matches)) {
                $mStr = strtolower($matches[1]);
                if (isset($monthsMap[$mStr])) {
                    $month = $monthsMap[$mStr];
                }
                if (!empty($matches[2])) {
                    $yVal = (int)$matches[2];
                    $year = ($yVal < 100) ? 2000 + $yVal : $yVal;
                }
                $weekNum = $isEndOfWeek ? 4 : 1;
            }

            if (!$month) {
                return null;
            }

            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

            if ($weekNum === 1) {
                $day = $isEndOfWeek ? min(7, $daysInMonth) : 1;
            } elseif ($weekNum === 2) {
                $day = $isEndOfWeek ? min(14, $daysInMonth) : 8;
            } elseif ($weekNum === 3) {
                $day = $isEndOfWeek ? min(21, $daysInMonth) : 15;
            } elseif ($weekNum === 4) {
                $day = $isEndOfWeek ? min(28, $daysInMonth) : 22;
            } else {
                $day = $isEndOfWeek ? $daysInMonth : 29;
                if ($day > $daysInMonth) $day = $daysInMonth;
            }

            return Carbon::create($year, $month, $day)->format('Y-m-d');
        };

        // Cache existing areas & categories
        $activeAreas = EngUtilityProjectArea::pluck('nama_area')->toArray();
        if (empty($activeAreas)) $activeAreas = ['Boiler', 'Utility', 'WWTP', 'MTC'];
        $activeCategories = EngUtilityProjectCategory::pluck('nama_kategori')->toArray();

        DB::beginTransaction();
        try {
            foreach ($rows as $rowIndex => $cols) {
                if ($rowIndex <= $headerRowIdx) continue;

                $namaProject = isset($headerMap['nama_project'], $cols[$headerMap['nama_project']])
                    ? trim((string)$cols[$headerMap['nama_project']])
                    : '';

                // Skip if no project name
                if (empty($namaProject)) {
                    continue;
                }

                $nomorProject = isset($headerMap['nomor_project'], $cols[$headerMap['nomor_project']])
                    ? trim((string)$cols[$headerMap['nomor_project']])
                    : null;

                $targetResult = isset($headerMap['target_result'], $cols[$headerMap['target_result']])
                    ? trim((string)$cols[$headerMap['target_result']])
                    : null;

                $areaRaw = isset($headerMap['area'], $cols[$headerMap['area']])
                    ? trim((string)$cols[$headerMap['area']])
                    : 'Utility';

                // Normalize area
                $area = 'Utility';
                foreach ($activeAreas as $a) {
                    if (strcasecmp($a, $areaRaw) === 0) {
                        $area = $a;
                        break;
                    }
                }
                if ($area === 'Utility' && !empty($areaRaw)) {
                    if (stripos($areaRaw, 'boiler') !== false) $area = 'Boiler';
                    elseif (stripos($areaRaw, 'wwtp') !== false || stripos($areaRaw, 'ipal') !== false) $area = 'WWTP';
                    elseif (stripos($areaRaw, 'mtc') !== false || stripos($areaRaw, 'maintenance') !== false) $area = 'MTC';
                }

                $kategoriRaw = isset($headerMap['kategori'], $cols[$headerMap['kategori']])
                    ? trim((string)$cols[$headerMap['kategori']])
                    : 'Maintenance';

                $kategori = !empty($kategoriRaw) ? $kategoriRaw : 'Maintenance';
                foreach ($activeCategories as $c) {
                    if (strcasecmp($c, $kategoriRaw) === 0) {
                        $kategori = $c;
                        break;
                    }
                }

                // Prioritas: default ke 'Medium' jika kosong atau tidak cocok
                $priorityRaw = isset($headerMap['priority'], $cols[$headerMap['priority']])
                    ? trim((string)$cols[$headerMap['priority']])
                    : '';
                $priority = 'Medium';
                if (!empty($priorityRaw)) {
                    foreach (['Critical', 'High', 'Medium', 'Low'] as $p) {
                        if (strcasecmp($p, $priorityRaw) === 0) {
                            $priority = $p;
                            break;
                        }
                    }
                }

                // Tanggal & Minggu Mulai
                $startDate = isset($headerMap['start_date'], $cols[$headerMap['start_date']])
                    ? $parseDate($cols[$headerMap['start_date']])
                    : null;

                $startWeek = isset($headerMap['start_week'], $cols[$headerMap['start_week']])
                    ? trim((string)$cols[$headerMap['start_week']])
                    : null;

                // Jika tanggal mulai kosong tapi ada minggu mulai -> ambil tanggal awal minggu
                if (empty($startDate) && !empty($startWeek)) {
                    $startDate = $parseWeekToDate($startWeek, false);
                } elseif (!empty($startDate) && empty($startWeek)) {
                    $cDate = Carbon::parse($startDate);
                    $startWeek = 'W' . (int)ceil($cDate->day / 7) . ' ' . $cDate->isoFormat('MMM YY');
                }

                // Tanggal & Minggu Selesai
                $finishDate = isset($headerMap['finish_date'], $cols[$headerMap['finish_date']])
                    ? $parseDate($cols[$headerMap['finish_date']])
                    : null;

                $finishWeek = isset($headerMap['finish_week'], $cols[$headerMap['finish_week']])
                    ? trim((string)$cols[$headerMap['finish_week']])
                    : null;

                // Jika target selesai kosong tapi ada minggu selesai -> ambil tanggal akhir minggu
                if (empty($finishDate) && !empty($finishWeek)) {
                    $finishDate = $parseWeekToDate($finishWeek, true);
                } elseif (!empty($finishDate) && empty($finishWeek)) {
                    $cDate = Carbon::parse($finishDate);
                    $finishWeek = 'W' . (int)ceil($cDate->day / 7) . ' ' . $cDate->isoFormat('MMM YY');
                }

                // Progress persentase (support format: "100%", "50 %", "0.45", "45", dsb)
                $progressRaw = isset($headerMap['progress'], $cols[$headerMap['progress']])
                    ? $cols[$headerMap['progress']]
                    : 0;
                $progress = 0;
                if ($progressRaw !== null && $progressRaw !== '') {
                    $cleanProg = trim((string)$progressRaw);
                    $hasPercentSign = str_contains($cleanProg, '%');

                    // Bersihkan karakter persen dan spasi, ganti koma dengan titik (misal "99,5%" -> "99.5")
                    $cleanProg = str_replace(['%', ' '], '', $cleanProg);
                    $cleanProg = str_replace(',', '.', $cleanProg);

                    if (is_numeric($cleanProg)) {
                        $pVal = (float)$cleanProg;
                        // Jika desimal murni dari Excel tanpa simbol % (misal 0.45 -> 45%)
                        if (!$hasPercentSign && $pVal > 0 && $pVal < 1) {
                            $pVal = $pVal * 100;
                        }
                        $progress = (int)round(max(0, min(100, $pVal)));
                    }
                }

                // Status project
                $statusRaw = isset($headerMap['status'], $cols[$headerMap['status']])
                    ? trim((string)$cols[$headerMap['status']])
                    : null;

                $status = null;
                if (!empty($statusRaw)) {
                    if (stripos($statusRaw, 'selesai') !== false || stripos($statusRaw, 'done') !== false || stripos($statusRaw, 'complete') !== false) {
                        $status = 'Selesai';
                    } elseif (stripos($statusRaw, 'lambat') !== false || stripos($statusRaw, 'delay') !== false || stripos($statusRaw, 'overdue') !== false) {
                        $status = 'Terlambat';
                    } elseif (stripos($statusRaw, 'progress') !== false || stripos($statusRaw, 'jalan') !== false || stripos($statusRaw, 'ongoing') !== false) {
                        $status = 'On Progress';
                    }
                }

                // Jika status kosong: tentukan otomatis dari persentase dan deadline minggu/tanggal target selesai
                if (!$status) {
                    if ($progress >= 100) {
                        $status = 'Selesai';
                    } else {
                        $deadline = $finishDate ?: ($finishWeek ? $parseWeekToDate($finishWeek, true) : null);
                        if ($deadline && Carbon::parse($deadline)->endOfDay()->isPast()) {
                            $status = 'Terlambat';
                        } else {
                            $status = 'On Progress';
                        }
                    }
                }

                $latestNote = isset($headerMap['latest_note'], $cols[$headerMap['latest_note']])
                    ? trim((string)$cols[$headerMap['latest_note']])
                    : null;

                // Check existing project by nomor_project if provided, or exact nama_project
                $existing = null;
                if (!empty($nomorProject)) {
                    $existing = EngUtilityProject::where('nomor_project', $nomorProject)->first();
                }
                if (!$existing) {
                    $existing = EngUtilityProject::where('nama_project', $namaProject)->first();
                }

                if ($existing) {
                    $oldProgress = $existing->progress;
                    $existing->update([
                        'nama_project' => $namaProject,
                        'target_result' => $targetResult ?: $existing->target_result,
                        'area' => $area,
                        'kategori' => $kategori,
                        'priority' => $priority,
                        'start_date' => $startDate ?: $existing->start_date,
                        'start_week' => $startWeek ?: $existing->start_week,
                        'finish_date' => $finishDate ?: $existing->finish_date,
                        'finish_week' => $finishWeek ?: $existing->finish_week,
                        'progress' => $progress,
                        'status' => $status,
                        'latest_note' => $latestNote ?: $existing->latest_note,
                        'updated_by' => $userId,
                    ]);

                    if (!empty($latestNote) || $progress != $oldProgress) {
                        EngUtilityProjectLog::create([
                            'project_id' => $existing->id,
                            'progress_sebelumnya' => $oldProgress,
                            'progress_baru' => $progress,
                            'catatan' => $latestNote ?: 'Update via Migrasi Excel Project',
                            'tanggal' => $finishDate ?: date('Y-m-d'),
                            'created_by' => $userId,
                        ]);
                    }

                    $updatedCount++;
                } else {
                    // Auto generate nomor if empty
                    if (empty($nomorProject)) {
                        $prefix = strtoupper(substr($area, 0, 3));
                        $seq = EngUtilityProject::where('area', $area)->count() + 1;
                        $nomorProject = 'PRJ-' . $prefix . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
                    }

                    $project = EngUtilityProject::create([
                        'nomor_project' => $nomorProject,
                        'nama_project' => $namaProject,
                        'target_result' => $targetResult,
                        'area' => $area,
                        'kategori' => $kategori,
                        'priority' => $priority,
                        'start_date' => $startDate,
                        'start_week' => $startWeek,
                        'finish_date' => $finishDate,
                        'finish_week' => $finishWeek,
                        'progress' => $progress,
                        'status' => $status,
                        'latest_note' => $latestNote,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);

                    EngUtilityProjectLog::create([
                        'project_id' => $project->id,
                        'progress_sebelumnya' => 0,
                        'progress_baru' => $progress,
                        'catatan' => $latestNote ?: 'Migrasi Project Berjalan via Excel',
                        'tanggal' => $startDate ?: date('Y-m-d'),
                        'created_by' => $userId,
                    ]);

                    $importedCount++;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses data: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses data: ' . $e->getMessage());
        }

        $msg = "Migrasi berhasil! {$importedCount} project baru ditambahkan";
        if ($updatedCount > 0) {
            $msg .= " dan {$updatedCount} project diperbarui.";
        } else {
            $msg .= ".";
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'imported' => $importedCount,
                'updated' => $updatedCount,
            ]);
        }

        return redirect()->route('utility.project.data')->with('success', $msg);
    }
}
