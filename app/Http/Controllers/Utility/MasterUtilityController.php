<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\AirArea;
use App\Models\Utility\ChemicalArea;
use App\Models\Utility\ChemicalType;
use App\Models\Utility\MasterListrikPanel;
use App\Models\Utility\PemakaianAirModel;
use App\Models\Utility\PemakaianChemicalModel;
use App\Models\Utility\PemakaianListrikModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MasterUtilityController extends Controller
{
    /**
     * Check if user is allowed to manage master data
     */
    private function authorizeMaster()
    {
        $jabatan = Auth::user()->jabatan ?? '';
        if ($jabatan === 'operator') {
            abort(403, 'Operator tidak memiliki izin untuk mengubah Master Data.');
        }
    }

    /**
     * Index page for Master Utility
     */
    public function index()
    {
        $totalPanels = MasterListrikPanel::count();
        $totalAirAreas = AirArea::count();
        $totalChemAreas = ChemicalArea::count();
        $totalChemTypes = ChemicalType::count();

        $chemAreas = ChemicalArea::orderBy('nama_area')->get();

        return view('utility.master_utility', compact(
            'totalPanels',
            'totalAirAreas',
            'totalChemAreas',
            'totalChemTypes',
            'chemAreas'
        ));
    }

    // ==========================================
    // 1. MASTER LISTRIK (PANEL TYPE)
    // ==========================================

    public function getListrikPanels()
    {
        $panels = MasterListrikPanel::orderBy('urutan')
            ->orderBy('nama_panel')
            ->get();

        // Count usages in pemakaian_listrik_eng
        $usageCounts = PemakaianListrikModel::select('panel_type', DB::raw('count(*) as total'))
            ->groupBy('panel_type')
            ->pluck('total', 'panel_type')
            ->toArray();

        $data = $panels->map(function ($p) use ($usageCounts) {
            return [
                'id' => $p->id,
                'nama_panel' => $p->nama_panel,
                'deskripsi' => $p->deskripsi ?? '-',
                'urutan' => $p->urutan,
                'is_active' => (bool) $p->is_active,
                'usage_count' => $usageCounts[$p->nama_panel] ?? 0,
                'created_at' => $p->created_at ? $p->created_at->format('d M Y H:i') : '-',
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function storeListrikPanel(Request $request)
    {
        $this->authorizeMaster();

        $validator = Validator::make($request->all(), [
            'nama_panel' => 'required|string|max:100|unique:master_listrik_panels,nama_panel',
            'deskripsi' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer',
        ], [
            'nama_panel.required' => 'Nama panel wajib diisi.',
            'nama_panel.unique' => 'Nama panel sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $urutan = $request->input('urutan');
        if ($urutan === null || $urutan === '') {
            $urutan = (MasterListrikPanel::max('urutan') ?? 0) + 1;
        }

        $panel = MasterListrikPanel::create([
            'nama_panel' => trim($request->input('nama_panel')),
            'deskripsi' => $request->input('deskripsi'),
            'urutan' => $urutan,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Panel '{$panel->nama_panel}' berhasil ditambahkan.",
            'data' => $panel
        ]);
    }

    public function updateListrikPanel(Request $request, $id)
    {
        $this->authorizeMaster();

        $panel = MasterListrikPanel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_panel' => 'required|string|max:100|unique:master_listrik_panels,nama_panel,' . $id,
            'deskripsi' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'nama_panel.required' => 'Nama panel wajib diisi.',
            'nama_panel.unique' => 'Nama panel sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $oldName = $panel->nama_panel;
        $newName = trim($request->input('nama_panel'));

        $panel->update([
            'nama_panel' => $newName,
            'deskripsi' => $request->input('deskripsi'),
            'urutan' => $request->has('urutan') ? $request->input('urutan') : $panel->urutan,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $panel->is_active,
        ]);

        // Sync old transactions if name changed
        if ($oldName !== $newName) {
            PemakaianListrikModel::where('panel_type', $oldName)->update(['panel_type' => $newName]);
        }

        return response()->json([
            'success' => true,
            'message' => "Panel '{$newName}' berhasil diperbarui.",
            'data' => $panel
        ]);
    }

    public function toggleListrikPanel($id)
    {
        $this->authorizeMaster();

        $panel = MasterListrikPanel::findOrFail($id);
        $panel->is_active = !$panel->is_active;
        $panel->save();

        $statusStr = $panel->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => "Panel '{$panel->nama_panel}' berhasil {$statusStr}.",
            'is_active' => $panel->is_active
        ]);
    }

    public function destroyListrikPanel($id)
    {
        $this->authorizeMaster();

        $panel = MasterListrikPanel::findOrFail($id);
        $usageCount = PemakaianListrikModel::where('panel_type', $panel->nama_panel)->count();

        if ($usageCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Panel '{$panel->nama_panel}' tidak dapat dihapus karena sudah memiliki {$usageCount} data riwayat pemakaian. Anda dapat menonaktifkannya agar tidak muncul di form baru."
            ], 422);
        }

        $nama = $panel->nama_panel;
        $panel->delete();

        return response()->json([
            'success' => true,
            'message' => "Panel '{$nama}' berhasil dihapus."
        ]);
    }

    // ==========================================
    // 2. MASTER AIR (JENIS PEMAKAIAN)
    // ==========================================

    public function getAirAreas()
    {
        $areas = AirArea::orderBy('nama_area')->get();

        $usageCounts = PemakaianAirModel::select('jenis_pemakaian', DB::raw('count(*) as total'))
            ->groupBy('jenis_pemakaian')
            ->pluck('total', 'jenis_pemakaian')
            ->toArray();

        $data = $areas->map(function ($a) use ($usageCounts) {
            return [
                'id' => $a->id,
                'nama_area' => $a->nama_area,
                'deskripsi' => $a->deskripsi ?? '-',
                'is_active' => (bool) ($a->is_active ?? true),
                'usage_count' => $usageCounts[$a->nama_area] ?? 0,
                'created_at' => $a->created_at ? $a->created_at->format('d M Y H:i') : '-',
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function storeAirArea(Request $request)
    {
        $this->authorizeMaster();

        $validator = Validator::make($request->all(), [
            'nama_area' => 'required|string|max:100|unique:air_area_utility,nama_area',
            'deskripsi' => 'nullable|string|max:255',
        ], [
            'nama_area.required' => 'Nama jenis pemakaian air wajib diisi.',
            'nama_area.unique' => 'Jenis pemakaian air sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $area = AirArea::create([
            'nama_area' => trim($request->input('nama_area')),
            'deskripsi' => $request->input('deskripsi'),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Jenis pemakaian air '{$area->nama_area}' berhasil ditambahkan.",
            'data' => $area
        ]);
    }

    public function updateAirArea(Request $request, $id)
    {
        $this->authorizeMaster();

        $area = AirArea::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_area' => 'required|string|max:100|unique:air_area_utility,nama_area,' . $id,
            'deskripsi' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ], [
            'nama_area.required' => 'Nama jenis pemakaian air wajib diisi.',
            'nama_area.unique' => 'Jenis pemakaian air sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $oldName = $area->nama_area;
        $newName = trim($request->input('nama_area'));

        $area->update([
            'nama_area' => $newName,
            'deskripsi' => $request->input('deskripsi'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : ($area->is_active ?? true),
        ]);

        if ($oldName !== $newName) {
            PemakaianAirModel::where('jenis_pemakaian', $oldName)->update(['jenis_pemakaian' => $newName]);
        }

        return response()->json([
            'success' => true,
            'message' => "Jenis pemakaian air '{$newName}' berhasil diperbarui.",
            'data' => $area
        ]);
    }

    public function toggleAirArea($id)
    {
        $this->authorizeMaster();

        $area = AirArea::findOrFail($id);
        $area->is_active = !($area->is_active ?? true);
        $area->save();

        $statusStr = $area->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => "Jenis pemakaian air '{$area->nama_area}' berhasil {$statusStr}.",
            'is_active' => $area->is_active
        ]);
    }

    public function destroyAirArea($id)
    {
        $this->authorizeMaster();

        $area = AirArea::findOrFail($id);
        $usageCount = PemakaianAirModel::where('jenis_pemakaian', $area->nama_area)->count();

        if ($usageCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Jenis pemakaian air '{$area->nama_area}' tidak dapat dihapus karena sudah memiliki {$usageCount} data riwayat. Silakan nonaktifkan agar tidak muncul di form baru."
            ], 422);
        }

        $nama = $area->nama_area;
        $area->delete();

        return response()->json([
            'success' => true,
            'message' => "Jenis pemakaian air '{$nama}' berhasil dihapus."
        ]);
    }

    // ==========================================
    // 3. MASTER CHEMICAL: AREA & JENIS PEMAKAIAN
    // ==========================================

    public function getChemicalAreas()
    {
        $areas = ChemicalArea::withCount('types')
            ->orderBy('nama_area')
            ->get();

        $usageCounts = PemakaianChemicalModel::select('chemical_area', DB::raw('count(*) as total'))
            ->groupBy('chemical_area')
            ->pluck('total', 'chemical_area')
            ->toArray();

        $data = $areas->map(function ($a) use ($usageCounts) {
            return [
                'id' => $a->id,
                'nama_area' => $a->nama_area,
                'types_count' => $a->types_count,
                'is_active' => (bool) ($a->is_active ?? true),
                'usage_count' => $usageCounts[$a->nama_area] ?? 0,
                'created_at' => $a->created_at ? $a->created_at->format('d M Y H:i') : '-',
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function storeChemicalArea(Request $request)
    {
        $this->authorizeMaster();

        $validator = Validator::make($request->all(), [
            'nama_area' => 'required|string|max:50|unique:chemical_areas,nama_area',
        ], [
            'nama_area.required' => 'Nama area chemical wajib diisi.',
            'nama_area.unique' => 'Nama area chemical sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $area = ChemicalArea::create([
            'nama_area' => trim($request->input('nama_area')),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Area chemical '{$area->nama_area}' berhasil ditambahkan.",
            'data' => $area
        ]);
    }

    public function updateChemicalArea(Request $request, $id)
    {
        $this->authorizeMaster();

        $area = ChemicalArea::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_area' => 'required|string|max:50|unique:chemical_areas,nama_area,' . $id,
            'is_active' => 'nullable|boolean',
        ], [
            'nama_area.required' => 'Nama area chemical wajib diisi.',
            'nama_area.unique' => 'Nama area chemical sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $oldName = $area->nama_area;
        $newName = trim($request->input('nama_area'));

        $area->update([
            'nama_area' => $newName,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : ($area->is_active ?? true),
        ]);

        if ($oldName !== $newName) {
            PemakaianChemicalModel::where('chemical_area', $oldName)->update(['chemical_area' => $newName]);
        }

        return response()->json([
            'success' => true,
            'message' => "Area chemical '{$newName}' berhasil diperbarui.",
            'data' => $area
        ]);
    }

    public function toggleChemicalArea($id)
    {
        $this->authorizeMaster();

        $area = ChemicalArea::findOrFail($id);
        $area->is_active = !($area->is_active ?? true);
        $area->save();

        $statusStr = $area->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => "Area chemical '{$area->nama_area}' berhasil {$statusStr}.",
            'is_active' => $area->is_active
        ]);
    }

    public function destroyChemicalArea($id)
    {
        $this->authorizeMaster();

        $area = ChemicalArea::findOrFail($id);

        if ($area->types()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => "Area chemical '{$area->nama_area}' memiliki {$area->types()->count()} jenis chemical di dalamnya. Hapus atau pindahkan jenis chemical terlebih dahulu."
            ], 422);
        }

        $usageCount = PemakaianChemicalModel::where('chemical_area', $area->nama_area)->count();
        if ($usageCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Area chemical '{$area->nama_area}' tidak dapat dihapus karena sudah memiliki {$usageCount} riwayat pemakaian."
            ], 422);
        }

        $nama = $area->nama_area;
        $area->delete();

        return response()->json([
            'success' => true,
            'message' => "Area chemical '{$nama}' berhasil dihapus."
        ]);
    }

    // ------------------------------------------
    // CHEMICAL TYPES (JENIS PEMAKAIAN CHEMICAL)
    // ------------------------------------------

    public function getChemicalTypes(Request $request)
    {
        $query = ChemicalType::with('area');

        if ($request->filled('area_id')) {
            $query->where('chemical_area_id', $request->input('area_id'));
        }

        $types = $query->orderBy('nama_chemical')->get();

        $usageCounts = PemakaianChemicalModel::select('jenis_pemakaian', DB::raw('count(*) as total'))
            ->groupBy('jenis_pemakaian')
            ->pluck('total', 'jenis_pemakaian')
            ->toArray();

        $data = $types->map(function ($t) use ($usageCounts) {
            return [
                'id' => $t->id,
                'chemical_area_id' => $t->chemical_area_id,
                'nama_area' => $t->area->nama_area ?? '-',
                'nama_chemical' => $t->nama_chemical,
                'satuan' => $t->satuan ?? 'Kg',
                'tipe_perhitungan' => $t->tipe_perhitungan ?? 'langsung',
                'rumus_formula' => $t->rumus_formula ?? '',
                'is_active' => (bool) ($t->is_active ?? true),
                'usage_count' => $usageCounts[$t->nama_chemical] ?? 0,
                'created_at' => $t->created_at ? $t->created_at->format('d M Y H:i') : '-',
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function storeChemicalType(Request $request)
    {
        $this->authorizeMaster();

        $validator = Validator::make($request->all(), [
            'chemical_area_id' => 'required|exists:chemical_areas,id',
            'nama_chemical' => 'required|string|max:50',
            'satuan' => 'required|string|max:50',
            'tipe_perhitungan' => 'nullable|in:langsung,rumus',
            'rumus_formula' => 'nullable|string|max:255',
        ], [
            'chemical_area_id.required' => 'Pilih area chemical terlebih dahulu.',
            'chemical_area_id.exists' => 'Area chemical tidak valid.',
            'nama_chemical.required' => 'Nama jenis chemical wajib diisi.',
            'satuan.required' => 'Satuan wajib diisi (misal: Liter, Kg).',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // Cek duplikasi di area yang sama
        $exists = ChemicalType::where('chemical_area_id', $request->input('chemical_area_id'))
            ->where('nama_chemical', trim($request->input('nama_chemical')))
            ->exists();

        if ($exists) {
            return response()->json(['message' => "Chemical '{$request->input('nama_chemical')}' sudah terdaftar pada area ini."], 422);
        }

        $tipePerhitungan = $request->input('tipe_perhitungan', 'langsung');
        $rumusFormula = $tipePerhitungan === 'rumus' ? trim($request->input('rumus_formula')) : null;

        $type = ChemicalType::create([
            'chemical_area_id' => $request->input('chemical_area_id'),
            'nama_chemical' => trim($request->input('nama_chemical')),
            'satuan' => trim($request->input('satuan')),
            'tipe_perhitungan' => $tipePerhitungan,
            'rumus_formula' => $rumusFormula,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Jenis chemical '{$type->nama_chemical}' berhasil ditambahkan.",
            'data' => $type->load('area')
        ]);
    }

    public function updateChemicalType(Request $request, $id)
    {
        $this->authorizeMaster();

        $type = ChemicalType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'chemical_area_id' => 'required|exists:chemical_areas,id',
            'nama_chemical' => 'required|string|max:50',
            'satuan' => 'required|string|max:50',
            'tipe_perhitungan' => 'nullable|in:langsung,rumus',
            'rumus_formula' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ], [
            'chemical_area_id.required' => 'Pilih area chemical terlebih dahulu.',
            'chemical_area_id.exists' => 'Area chemical tidak valid.',
            'nama_chemical.required' => 'Nama jenis chemical wajib diisi.',
            'satuan.required' => 'Satuan wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // Cek duplikasi di area yang sama kecuali diri sendiri
        $exists = ChemicalType::where('chemical_area_id', $request->input('chemical_area_id'))
            ->where('nama_chemical', trim($request->input('nama_chemical')))
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => "Chemical '{$request->input('nama_chemical')}' sudah terdaftar pada area ini."], 422);
        }

        $oldName = $type->nama_chemical;
        $newName = trim($request->input('nama_chemical'));
        $tipePerhitungan = $request->input('tipe_perhitungan', 'langsung');
        $rumusFormula = $tipePerhitungan === 'rumus' ? trim($request->input('rumus_formula')) : null;

        $type->update([
            'chemical_area_id' => $request->input('chemical_area_id'),
            'nama_chemical' => $newName,
            'satuan' => trim($request->input('satuan')),
            'tipe_perhitungan' => $tipePerhitungan,
            'rumus_formula' => $rumusFormula,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : ($type->is_active ?? true),
        ]);

        if ($oldName !== $newName) {
            PemakaianChemicalModel::where('jenis_pemakaian', $oldName)->update(['jenis_pemakaian' => $newName]);
        }

        return response()->json([
            'success' => true,
            'message' => "Jenis chemical '{$newName}' berhasil diperbarui.",
            'data' => $type->load('area')
        ]);
    }

    public function testFormula(Request $request)
    {
        $formula = $request->input('formula');
        $nilai = (float) $request->input('nilai', 10);
        $rh = (float) $request->input('rh', 24);

        if (empty($formula)) {
            return response()->json([
                'success' => true,
                'result' => $nilai,
                'preview' => "Tanpa rumus -> Hasil langsung = {$nilai}"
            ]);
        }

        $result = ChemicalType::evaluateFormula($formula, $nilai, $rh);

        return response()->json([
            'success' => true,
            'result' => round($result, 4),
            'preview' => "Simulasi: nilai={$nilai}, rh={$rh} jam -> Hasil: " . round($result, 4)
        ]);
    }

    public function toggleChemicalType($id)
    {
        $this->authorizeMaster();

        $type = ChemicalType::findOrFail($id);
        $type->is_active = !($type->is_active ?? true);
        $type->save();

        $statusStr = $type->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => "Jenis chemical '{$type->nama_chemical}' berhasil {$statusStr}.",
            'is_active' => $type->is_active
        ]);
    }

    public function destroyChemicalType($id)
    {
        $this->authorizeMaster();

        $type = ChemicalType::findOrFail($id);
        $usageCount = PemakaianChemicalModel::where('jenis_pemakaian', $type->nama_chemical)->count();

        if ($usageCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Jenis chemical '{$type->nama_chemical}' tidak dapat dihapus karena sudah memiliki {$usageCount} data riwayat. Silakan nonaktifkan agar tidak muncul di form baru."
            ], 422);
        }

        $nama = $type->nama_chemical;
        $type->delete();

        return response()->json([
            'success' => true,
            'message' => "Jenis chemical '{$nama}' berhasil dihapus."
        ]);
    }
}
