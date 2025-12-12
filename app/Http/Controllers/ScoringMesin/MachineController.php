<?php

namespace App\Http\Controllers\ScoringMesin;

use App\Models\ScoringMesin\Machine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MachineController extends Controller
{
    public function index(Request $request)
    {
        // Check if request expects JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(Machine::all());
        }

        // For web view, return the view
        return view('scoringmesin.machines.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'code' => 'required|unique:machines,code',
        ]);

        $machine = Machine::create($validated);
        return response()->json(['message' => 'Mesin ditambahkan', 'data' => $machine], 201);
    }

    public function show(Machine $machine)
    {
        return response()->json($machine);
    }

    public function update(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'name' => 'required',
            'code' => 'required|unique:machines,code,' . $machine->id,
            'status' => 'in:active,maintenance,broken',
            'description' => 'nullable|string',
        ]);

        $machine->update($validated);
        return response()->json(['message' => 'Mesin diupdate', 'data' => $machine]);
    }

    public function destroy(Machine $machine)
    {
        $machine->delete();
        return response()->json(['message' => 'Mesin dihapus']);
    }

    public function statistics()
    {
        $stats = DB::table('machines')
        ->select('status', DB::raw('count(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();

        $statistics = [
            'total' => Machine::count(),
            'active' => $stats['active'] ?? 0,
            'maintenance' => $stats['maintenance'] ?? 0,
            'broken' => $stats['broken'] ?? 0,
        ];

        return response()->json($statistics);
    }
}