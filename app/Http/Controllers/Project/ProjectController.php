<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\ProjectMaster;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Dashboard — tampilkan semua project beserta progress fase.
     */
    public function index()
    {
        $projects = ProjectMaster::with([
            'user',
            'faseSatu',
            'faseDua',
            'faseTiga',
        ])
        ->latest()
        ->paginate(10);

        return view('project.index', compact('projects'));
    }

    /**
     * Detail project — tampilkan semua fase yang sudah diisi.
     */
    public function show(ProjectMaster $project)
    {
        $project->load([
            'user',
            'faseSatu.user',
            'faseDua.user',
            'faseTiga.user',
            'dokumentasiFase1.uploadedBy',
            'dokumentasiFase3.uploadedBy',
        ]);

        return view('project.show', compact('project'));
    }
}
