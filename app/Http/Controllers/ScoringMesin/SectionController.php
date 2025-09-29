<?php

namespace App\Http\Controllers\ScoringMesin;

use App\Models\ScoringMesin\Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(Section::with('processParameter.machine')->get());
        }

        // For web view, return the view
        return view('scoringmesin.section.index');
        
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'process_parameter_id' => 'required|exists:process_parameters,id',
            'name' => 'required',
        ]);

        $section = Section::create($validated);
        return response()->json(['message' => 'Section ditambahkan', 'data' => $section], 201);
    }

    public function show(Section $section)
    {
        return response()->json($section->load('processParameter'));
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'process_parameter_id' => 'required|exists:process_parameters,id',
            'name' => 'required',
        ]);

        $section->update($validated);
        return response()->json(['message' => 'Section diupdate', 'data' => $section]);
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return response()->json(['message' => 'Section dihapus']);
    }
}