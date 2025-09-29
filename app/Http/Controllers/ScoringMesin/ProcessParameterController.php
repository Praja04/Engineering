<?php

namespace App\Http\Controllers\ScoringMesin;

use App\Models\ScoringMesin\ProcessParameter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProcessParameterController extends Controller
{
    public function index(Request $request)
    {
        // Check if request expects JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(ProcessParameter::with('machine')->get());
        }

        // For web view, return the view
        return view('scoringmesin.process-parameters.index');
       
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'name' => 'required',
        ]);

        $param = ProcessParameter::create($validated);
        return response()->json(['message' => 'Parameter proses ditambahkan', 'data' => $param], 201);
    }

    public function show(ProcessParameter $processParameter)
    {
        return response()->json($processParameter->load('machine'));
    }

    public function update(Request $request, ProcessParameter $processParameter)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'name' => 'required',
        ]);

        $processParameter->update($validated);
        return response()->json(['message' => 'Parameter proses diupdate', 'data' => $processParameter]);
    }

    public function destroy(ProcessParameter $processParameter)
    {
        $processParameter->delete();
        return response()->json(['message' => 'Parameter proses dihapus']);
    }
}