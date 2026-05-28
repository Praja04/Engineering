<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\wwtp_analisa\WwtpAnalisa;
use App\Models\Utility\wwtp_analisa\WwtpAnalisaDetail;
use App\Models\Utility\wwtp_analisa\WwtpParameter;
use App\Models\Utility\wwtp_analisa\WwtpPoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
                    'shift'        => $request->shift ?? null,
                ],
                [
                    'area'         => $request->area ?? null,
                    'created_by'   => Auth::id() ?? 1, // Default to 1 if not logged in
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
