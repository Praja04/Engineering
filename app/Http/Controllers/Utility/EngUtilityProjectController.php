<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\EngUtilityProject;
use App\Models\Utility\EngUtilityProjectLog;
use App\Models\Utility\EngUtilityProjectArea;
use App\Models\Utility\EngUtilityProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
}
