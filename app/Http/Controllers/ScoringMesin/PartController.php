<?php

namespace App\Http\Controllers\ScoringMesin;

use App\Models\ScoringMesin\Part;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PartController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(
                Part::with('section.processParameter.machine')->get()
            );
        }

        return view('scoringmesin.parts.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'name' => 'required',
        ]);

        $part = Part::create($validated);
        return response()->json(['message' => 'Part ditambahkan', 'data' => $part], 201);
    }

    public function show(Part $part)
    {
        return response()->json(
            $part->load('section.processParameter.machine')
        );
    }

    public function update(Request $request, Part $part)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'name' => 'required',
        ]);

        $part->update($validated);
        return response()->json(['message' => 'Part diupdate', 'data' => $part]);
    }

    public function destroy(Part $part)
    {
        $part->delete();
        return response()->json(['message' => 'Part dihapus']);
    }
}