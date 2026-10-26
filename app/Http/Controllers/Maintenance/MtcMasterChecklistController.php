<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Maintenance\MtcMasterChecklist;
use App\Models\Maintenance\MtcMasterChecklistGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MtcMasterChecklistController extends Controller
{
    protected $jenisMtcList = [
        'Motor Pompa',
        'Utility',
        'Electrical',
        'Refrigerasi',
        'Electric Engine',
        'Diesel Engine',
        'Battery',
        'Sipil',
    ];

    /**
     * Render the master checklist view.
     */
    public function index()
    {
        $jenisList = $this->jenisMtcList;
        $groups = MtcMasterChecklistGroup::orderBy('jenis_mtc')->orderBy('urutan')->get();

        return view('maintenance.master.master_checklist', compact('jenisList', 'groups'));
    }

    /**
     * Get list of checklist items & summary stats via AJAX.
     */
    public function getData(Request $request)
    {
        $query = MtcMasterChecklist::with('group');

        if ($request->filled('jenis_mtc')) {
            $query->where('jenis_mtc', $request->jenis_mtc);
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('label', 'like', "%{$search}%")
                  ->orWhere('standar', 'like', "%{$search}%")
                  ->orWhere('jenis_mtc', 'like', "%{$search}%")
                  ->orWhereHas('group', function ($g) use ($search) {
                      $g->where('nama_group', 'like', "%{$search}%");
                  });
            });
        }

        $totalFiltered = $query->count();

        $page = max(1, (int)$request->input('page', 1));
        $perPage = max(1, (int)$request->input('per_page', $request->input('length', 25)));
        $start = ($page - 1) * $perPage;

        $items = $query->orderBy('jenis_mtc')
            ->orderBy('group_id')
            ->orderBy('urutan')
            ->skip($start)
            ->take($perPage)
            ->get();

        // Summary stats
        $totalChecklists = MtcMasterChecklist::count();
        $totalActive = MtcMasterChecklist::where('is_active', true)->count();
        $totalInactive = $totalChecklists - $totalActive;
        $totalGroups = MtcMasterChecklistGroup::count();

        return response()->json([
            'status'          => true,
            'data'            => $items,
            'recordsTotal'    => $totalChecklists,
            'recordsFiltered' => $totalFiltered,
            'current_page'    => $page,
            'per_page'        => $perPage,
            'total_pages'     => (int)ceil($totalFiltered / $perPage),
            'summary' => [
                'total'    => $totalChecklists,
                'active'   => $totalActive,
                'inactive' => $totalInactive,
                'groups'   => $totalGroups,
            ]
        ]);
    }

    /**
     * Get groups by jenis_mtc.
     */
    public function getGroups(Request $request)
    {
        $query = MtcMasterChecklistGroup::query();

        if ($request->filled('jenis_mtc')) {
            $query->where('jenis_mtc', $request->jenis_mtc);
        }

        $groups = $query->where('is_active', true)->orderBy('urutan')->get();

        return response()->json([
            'status' => true,
            'groups' => $groups,
        ]);
    }

    /**
     * Store a new checklist group.
     */
    public function storeGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_mtc'  => 'required|string|max:100',
            'nama_group' => 'required|string|max:150',
            'urutan'     => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $maxUrutan = MtcMasterChecklistGroup::where('jenis_mtc', $request->jenis_mtc)->max('urutan') ?? 0;

        $group = MtcMasterChecklistGroup::create([
            'jenis_mtc'  => $request->jenis_mtc,
            'nama_group' => $request->nama_group,
            'urutan'     => $request->urutan ?? ($maxUrutan + 1),
            'is_active'  => true,
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Group checklist berhasil ditambahkan',
            'group'   => $group,
        ]);
    }

    /**
     * Store a new checklist item.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_mtc'  => 'required|string|max:100',
            'group_id'   => 'required|exists:mtc_master_checklist_groups,id',
            'label'      => 'required|string|max:255',
            'standar'    => 'nullable|string',
            'tipe_input' => 'required|in:boolean_ok_ng,text,number',
            'urutan'     => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $maxUrutan = MtcMasterChecklist::where('group_id', $request->group_id)->max('urutan') ?? 0;

        $checklist = MtcMasterChecklist::create([
            'jenis_mtc'  => $request->jenis_mtc,
            'group_id'   => $request->group_id,
            'label'      => $request->label,
            'standar'    => $request->standar,
            'tipe_input' => $request->tipe_input,
            'urutan'     => $request->urutan ?? ($maxUrutan + 1),
            'is_active'  => true,
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'status'    => true,
            'message'   => 'Item checklist berhasil ditambahkan',
            'checklist' => $checklist,
        ]);
    }

    /**
     * Update checklist item.
     */
    public function update(Request $request, $id)
    {
        $checklist = MtcMasterChecklist::find($id);

        if (!$checklist) {
            return response()->json([
                'status'  => false,
                'message' => 'Data checklist tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'group_id'   => 'required|exists:mtc_master_checklist_groups,id',
            'label'      => 'required|string|max:255',
            'standar'    => 'nullable|string',
            'tipe_input' => 'required|in:boolean_ok_ng,text,number',
            'urutan'     => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $group = MtcMasterChecklistGroup::find($request->group_id);

        $checklist->update([
            'group_id'   => $request->group_id,
            'jenis_mtc'  => $group->jenis_mtc,
            'label'      => $request->label,
            'standar'    => $request->standar,
            'tipe_input' => $request->tipe_input,
            'urutan'     => $request->urutan ?? $checklist->urutan,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Item checklist berhasil diperbarui',
        ]);
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus($id)
    {
        $checklist = MtcMasterChecklist::find($id);

        if (!$checklist) {
            return response()->json([
                'status'  => false,
                'message' => 'Data checklist tidak ditemukan',
            ], 404);
        }

        $checklist->is_active = !$checklist->is_active;
        $checklist->save();

        $statusStr = $checklist->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'status'    => true,
            'message'   => "Item checklist berhasil {$statusStr}",
            'is_active' => $checklist->is_active,
        ]);
    }

    /**
     * Soft/safe delete checklist item.
     */
    public function destroy($id)
    {
        $checklist = MtcMasterChecklist::find($id);

        if (!$checklist) {
            return response()->json([
                'status'  => false,
                'message' => 'Data checklist tidak ditemukan',
            ], 404);
        }

        // Safety check: if checklist has historical results, do not hard-delete to protect data integrity
        $hasResults = DB::table('mtc_inspection_checklist_results')
            ->where('checklist_id', $id)
            ->exists();

        if ($hasResults) {
            // Automatically switch to inactive so historical records remain intact!
            $checklist->update(['is_active' => false]);
            return response()->json([
                'status'  => true,
                'message' => 'Item checklist memiliki riwayat inspeksi sebelumnya, sehingga statusnya otomatis diubah menjadi NONAKTIF (Zero Data Loss)',
            ]);
        }

        $checklist->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Item checklist berhasil dihapus',
        ]);
    }
}
