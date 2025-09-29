<?php

namespace App\Http\Controllers\ScoringMesin;

use App\Models\ScoringMesin\StandardState;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StandardStateController extends Controller
{
    public function index()
    {
        return response()->json(StandardState::with('part')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'part_id' => 'required|exists:parts,id',
            'value' => 'required',
            'unit' => 'nullable|string',
        ]);

        $state = StandardState::create($validated);
        return response()->json(['message' => 'Keadaan standar ditambahkan', 'data' => $state], 201);
    }

    public function show(StandardState $standardState)
    {
        return response()->json($standardState->load('part'));
    }

    public function update(Request $request, StandardState $standardState)
    {
        $validated = $request->validate([
            'part_id' => 'required|exists:parts,id',
            'value' => 'required',
            'unit' => 'nullable|string',
        ]);

        $standardState->update($validated);
        return response()->json(['message' => 'Keadaan standar diupdate', 'data' => $standardState]);
    }

    public function destroy(StandardState $standardState)
    {
        $standardState->delete();
        return response()->json(['message' => 'Keadaan standar dihapus']);
    }
}