<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\WwtpAnalisa;
use Carbon\Carbon;

class WWTPControllerAnalisa extends Controller
{
    public function form_analisa()
    {
        return view('utility.wwtp.form_analisa');
    }

    public function data_analisa()
    {
        return view('utility.wwtp.data_analisa');
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

        $query = WwtpAnalisa::orderBy('tanggal', 'desc');

        if ($bulan) {
            $query->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan]);
        }

        if ($search) {
            $query->where('tanggal', 'like', "%{$search}%");
        }

        return response()->json(
            $query->paginate($perPage, ['*'], 'page', $page)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'cod'     => 'nullable|numeric|min:0',
            'tss'     => 'nullable|numeric|min:0',
            'ph'      => 'nullable|numeric|min:0',
            'ec'      => 'nullable|numeric|min:0',
            'do'      => 'nullable|numeric|min:0',
        ]);

        $existing = WwtpAnalisa::where('tanggal', $request->tanggal)->first();

        if ($existing) {
            return response()->json([
                'message' => 'Data analisa WWTP untuk tanggal tersebut sudah ada.'
            ], 409);
        }

        $analisa = WwtpAnalisa::create([
            'tanggal' => $request->tanggal,
            'cod'     => $request->cod,
            'tss'     => $request->tss,
            'ph'      => $request->ph,
            'ec'      => $request->ec,
            'do'      => $request->do,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data analisa WWTP berhasil disimpan.',
            'data'    => $analisa,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $analisa = WwtpAnalisa::findOrFail($id);
        return response()->json($analisa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $analisa = WwtpAnalisa::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'cod'     => 'nullable|numeric|min:0',
            'tss'     => 'nullable|numeric|min:0',
            'ph'      => 'nullable|numeric|min:0',
            'ec'      => 'nullable|numeric|min:0',
            'do'      => 'nullable|numeric|min:0',
        ]);

        $existing = WwtpAnalisa::where('tanggal', $request->tanggal)
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Data analisa WWTP untuk tanggal tersebut sudah ada.'
            ], 409);
        }

        $analisa->update([
            'tanggal' => $request->tanggal,
            'cod'     => $request->cod,
            'tss'     => $request->tss,
            'ph'      => $request->ph,
            'ec'      => $request->ec,
            'do'      => $request->do,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data analisa WWTP berhasil diperbarui.',
            'data'    => $analisa,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $analisa = WwtpAnalisa::findOrFail($id);
        $analisa->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data analisa WWTP berhasil dihapus.',
        ]);
    }
}
