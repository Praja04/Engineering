<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\wwtp_analisa\WwtpAnalisa;
use App\Models\Utility\wwtp_analisa\WwtpAnalisaDetail;
use App\Models\Utility\wwtp_analisa\WwtpParameter;
use App\Models\Utility\wwtp_analisa\WwtpPoint;
use App\Models\Utility\wwtp_analisa\WwtpStandard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WWTPControllerAnalisa extends Controller
{
    public function form_analisa()
    {
        $parameters = WwtpParameter::all();
        $points = WwtpPoint::all();
        $standardsData = \App\Models\Utility\wwtp_analisa\WwtpStandard::all();

        $standards = [];
        foreach ($standardsData as $std) {
            $standards[$std->point_id . '_' . $std->parameter_id] = $std->standard_value;
        }

        return view('utility.wwtp.form_analisa', compact('parameters', 'points', 'standards'));
    }

    public function data_analisa()
    {
        $parameters = WwtpParameter::all();
        $standardsData = \App\Models\Utility\wwtp_analisa\WwtpStandard::all();

        $standards = [];
        foreach ($standardsData as $std) {
            $standards[$std->point_id . '_' . $std->parameter_id] = $std->standard_value;
        }

        return view('utility.wwtp.data_analisa', compact('parameters', 'standards'));
    }

    public function manage_standar()
    {
        $points = WwtpPoint::orderBy('point_name')->get();
        $parameters = WwtpParameter::orderBy('parameter_name')->get();

        return view('utility.wwtp.manage_standar', compact('points', 'parameters'));
    }

    public function indexParameter(Request $request)
    {
        $query = WwtpParameter::query()
            ->withCount('standards')
            ->orderBy('parameter_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('parameter_name', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%");
            });
        }

        return response()->json($query->get());
    }

    public function showParameter($id)
    {
        return response()->json(WwtpParameter::findOrFail($id));
    }

    public function storeParameter(Request $request)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        $validated = $request->validate([
            'parameter_name' => 'required|string|max:255|unique:wwtp_parameters,parameter_name',
            'unit' => 'nullable|string|max:50',
        ], [
            'parameter_name.unique' => 'Parameter ini sudah terdaftar.',
        ]);

        $parameter = WwtpParameter::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Parameter berhasil disimpan.',
            'data' => $parameter,
        ]);
    }

    public function updateParameter(Request $request, $id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        $parameter = WwtpParameter::findOrFail($id);

        $validated = $request->validate([
            'parameter_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('wwtp_parameters', 'parameter_name')->ignore($parameter->id),
            ],
            'unit' => 'nullable|string|max:50',
        ], [
            'parameter_name.unique' => 'Parameter ini sudah terdaftar.',
        ]);

        $parameter->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Parameter berhasil diperbarui.',
            'data' => $parameter,
        ]);
    }

    public function destroyParameter($id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        WwtpParameter::findOrFail($id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Parameter berhasil dihapus.',
        ]);
    }

    public function indexPoint(Request $request)
    {
        $query = WwtpPoint::query()
            ->withCount('standards')
            ->orderBy('point_name');

        if ($request->filled('search')) {
            $query->where('point_name', 'like', "%{$request->search}%");
        }

        return response()->json($query->get());
    }

    public function showPoint($id)
    {
        return response()->json(WwtpPoint::findOrFail($id));
    }

    public function storePoint(Request $request)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        $validated = $request->validate([
            'point_name' => 'required|string|max:255|unique:wwtp_point,point_name',
        ], [
            'point_name.unique' => 'Point pengukuran ini sudah terdaftar.',
        ]);

        $point = WwtpPoint::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Point pengukuran berhasil disimpan.',
            'data' => $point,
        ]);
    }

    public function updatePoint(Request $request, $id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        $point = WwtpPoint::findOrFail($id);

        $validated = $request->validate([
            'point_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('wwtp_point', 'point_name')->ignore($point->id),
            ],
        ], [
            'point_name.unique' => 'Point pengukuran ini sudah terdaftar.',
        ]);

        $point->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Point pengukuran berhasil diperbarui.',
            'data' => $point,
        ]);
    }

    public function destroyPoint($id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        WwtpPoint::findOrFail($id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Point pengukuran berhasil dihapus.',
        ]);
    }

    public function indexStandar(Request $request)
    {
        $query = WwtpStandard::with(['point', 'parameter'])
            ->join('wwtp_point', 'wwtp_standards.point_id', '=', 'wwtp_point.id')
            ->join('wwtp_parameters', 'wwtp_standards.parameter_id', '=', 'wwtp_parameters.id')
            ->select('wwtp_standards.*')
            ->orderBy('wwtp_parameters.parameter_name')
            ->orderBy('wwtp_point.point_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('wwtp_point.point_name', 'like', "%{$search}%")
                    ->orWhere('wwtp_parameters.parameter_name', 'like', "%{$search}%")
                    ->orWhere('wwtp_parameters.unit', 'like', "%{$search}%")
                    ->orWhere('wwtp_standards.standard_value', 'like', "%{$search}%");
            });
        }

        return response()->json($query->get());
    }

    public function showStandar($id)
    {
        return response()->json(WwtpStandard::with(['point', 'parameter'])->findOrFail($id));
    }

    public function storeStandar(Request $request)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        $validated = $request->validate([
            'point_id' => [
                'required',
                'exists:wwtp_point,id',
                Rule::unique('wwtp_standards')->where(fn ($query) => $query->where('parameter_id', $request->parameter_id)),
            ],
            'parameter_id' => 'required|exists:wwtp_parameters,id',
            'standard_value' => 'nullable|numeric|min:0',
        ], [
            'point_id.unique' => 'Standar untuk kombinasi point dan parameter ini sudah ada.',
        ]);

        $standard = WwtpStandard::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Standar analisa WWTP berhasil disimpan.',
            'data' => $standard->load(['point', 'parameter']),
        ]);
    }

    public function updateStandar(Request $request, $id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        $standard = WwtpStandard::findOrFail($id);

        $validated = $request->validate([
            'point_id' => [
                'required',
                'exists:wwtp_point,id',
                Rule::unique('wwtp_standards')->ignore($standard->id)->where(fn ($query) => $query->where('parameter_id', $request->parameter_id)),
            ],
            'parameter_id' => 'required|exists:wwtp_parameters,id',
            'standard_value' => 'nullable|numeric|min:0',
        ], [
            'point_id.unique' => 'Standar untuk kombinasi point dan parameter ini sudah ada.',
        ]);

        $standard->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Standar analisa WWTP berhasil diperbarui.',
            'data' => $standard->load(['point', 'parameter']),
        ]);
    }

    public function destroyStandar($id)
    {
        abort_if(Auth::user()?->jabatan === 'operator', 403);

        WwtpStandard::findOrFail($id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Standar analisa WWTP berhasil dihapus.',
        ]);
    }

    /**
     * Display a listing of the resource (JSON for DataTables/AJAX)
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page    = $request->input('page', 1);
        $bulan   = $request->input('bulan'); // Format YYYY-MM
        $search  = $request->input('search');

        $query = WwtpAnalisa::with(['creator', 'details.parameter', 'details.point'])->orderBy('analisa_date', 'desc')->orderBy('shift', 'asc');

        if ($bulan) {
            $query->whereRaw("DATE_FORMAT(analisa_date, '%Y-%m') = ?", [$bulan]);
        }

        if ($search) {
            $query->where('analisa_date', 'like', "%{$search}%");
        }

        return response()->json(
            $query->paginate($perPage, ['*'], 'page', $page)
        );
    }

    public function checkFilledParameters(Request $request)
    {
        $request->validate([
            'analisa_date' => 'required|date',
        ]);

        $analisa = WwtpAnalisa::where('analisa_date', $request->analisa_date)
            ->first();

        if (!$analisa) {
            return response()->json([]);
        }

        $filledParameterIds = WwtpAnalisaDetail::where('analisa_id', $analisa->id)
            ->distinct()
            ->pluck('parameter_id');

        return response()->json($filledParameterIds);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'analisa_date' => 'required|date',
            // 'shift'        => 'required|integer',
            'area'         => 'nullable|string',
            'hasil_analisa' => 'required|array',
            'hasil_analisa.*.*' => 'nullable|numeric' // array format: point_id => [ parameter_id => value ]
        ]);

        try {
            DB::beginTransaction();

            // Find or create the header record for this date and shift
            $analisa = WwtpAnalisa::firstOrCreate(
                [
                    'analisa_date' => $request->analisa_date,
                    // 'shift'        => $request->shift ?? null,
                ],
                [
                    // 'area'         => $request->area ?? null,
                    'created_by'   => Auth::id(), // Default to 1 if not logged in
                ]
            );

            // If it already exists, update area if provided
            if (!$analisa->wasRecentlyCreated && $request->filled('area')) {
                $analisa->update(['area' => $request->area]);
            }

            foreach ($request->hasil_analisa as $point_id => $parameters) {
                foreach ($parameters as $parameter_id => $hasil) {
                    if ($hasil !== null && $hasil !== '') {
                        WwtpAnalisaDetail::updateOrCreate(
                            [
                                'analisa_id'   => $analisa->id,
                                'point_id'     => $point_id,
                                'parameter_id' => $parameter_id,
                            ],
                            [
                                'hasil_analisa' => $hasil,
                                'keterangan'    => null
                            ]
                        );
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data analisa WWTP berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $analisa = WwtpAnalisa::with(['creator', 'details.point', 'details.parameter'])->findOrFail($id);
        return response()->json($analisa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $analisa = WwtpAnalisa::findOrFail($id);

        $request->validate([
            'analisa_date' => 'required|date',
            'shift'        => 'required|integer',
            'area'         => 'nullable|string',
            'hasil_analisa' => 'required|array',
            'hasil_analisa.*.*' => 'nullable|numeric'
        ]);

        try {
            DB::beginTransaction();

            $exist = WwtpAnalisa::where('analisa_date', $request->analisa_date)
                ->where('shift', $request->shift)
                ->where('id', '!=', $id)
                ->first();

            if ($exist) {
                return response()->json([
                    'message' => 'Data analisa WWTP untuk tanggal dan shift yang sama sudah ada.',
                ], 500);
            }

            $analisa->update([
                'analisa_date' => $request->analisa_date,
                'shift'        => $request->shift,
                'area'         => $request->area,
            ]);

            // Clear existing details and re-insert
            $analisa->details()->delete();

            foreach ($request->hasil_analisa as $point_id => $parameters) {
                foreach ($parameters as $parameter_id => $hasil) {
                    if ($hasil !== null && $hasil !== '') {
                        WwtpAnalisaDetail::create([
                            'analisa_id'    => $analisa->id,
                            'point_id'      => $point_id,
                            'parameter_id'  => $parameter_id,
                            'hasil_analisa' => $hasil,
                            'keterangan'    => null
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data analisa WWTP berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $analisa = WwtpAnalisa::findOrFail($id);
        $analisa->delete(); // Cascades to details

        return response()->json([
            'status'  => 'success',
            'message' => 'Data analisa WWTP berhasil dihapus.',
        ]);
    }
}
