<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\ProjectMaster;
use App\Models\Project\ProjectFaseDua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectFaseDuaController extends Controller
{
    /**
     * Form input Fase 2.
     * Hanya bisa diakses jika project sudah di fase_1 dan faseSatu sudah ada.
     */
    public function create(ProjectMaster $project)
    {

        $users = \App\Models\User::orderBy('username')->get();

        return view('project.fase2.create', compact('project', 'users'));
    }

    /**
     * Simpan data Fase 2 dan update fase_aktif project.
     */
    public function store(Request $request, ProjectMaster $project)
    {

        $request->validate([
            'ejo'        => 'nullable|string|max:100',
            'deskripsi'  => 'required|string|max:255',
            'user_id'    => 'required|exists:users,id',
            'nomor_io'   => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'persen_pr'  => 'required|integer|min:0|max:100',
            'persen_po'  => 'required|integer|min:0|max:100',
            'persen_gr'  => 'required|integer|min:0|max:100',
        ]);

        DB::transaction(function () use ($request, $project) {
            ProjectFaseDua::create([
                'project_id' => $project->id,
                'ejo'        => $request->ejo,
                'deskripsi'  => $request->deskripsi,
                'user_id'    => $request->user_id,
                'nomor_io'   => $request->nomor_io,
                'keterangan' => $request->keterangan,
                'persen_pr'  => $request->persen_pr,
                'persen_po'  => $request->persen_po,
                'persen_gr'  => $request->persen_gr,
            ]);

            // Naikkan fase_aktif project ke fase_2
            $project->update(['fase_aktif' => 'fase_2']);
        });

        return redirect()->route('project.show', $project)
                         ->with('success', 'Fase 2 berhasil disimpan. Project naik ke Fase 2.');
    }

    /**
     * Form edit Fase 2.
     */
    public function edit(ProjectMaster $project)
    {

        $users   = \App\Models\User::orderBy('username')->get();
        $faseDua = $project->faseDua;

        return view('project.fase2.edit', compact('project', 'faseDua', 'users'));
    }

    /**
     * Update data Fase 2.
     */
    public function update(Request $request, ProjectMaster $project)
    {

        $request->validate([
            'ejo'        => 'nullable|string|max:100',
            'deskripsi'  => 'required|string|max:255',
            'user_id'    => 'required|exists:users,id',
            'nomor_io'   => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'persen_pr'  => 'required|integer|min:0|max:100',
            'persen_po'  => 'required|integer|min:0|max:100',
            'persen_gr'  => 'required|integer|min:0|max:100',
        ]);

        $project->faseDua()->update([
            'ejo'        => $request->ejo,
            'deskripsi'  => $request->deskripsi,
            'user_id'    => $request->user_id,
            'nomor_io'   => $request->nomor_io,
            'keterangan' => $request->keterangan,
            'persen_pr'  => $request->persen_pr,
            'persen_po'  => $request->persen_po,
            'persen_gr'  => $request->persen_gr,
        ]);

        return redirect()->route('project.show', $project)
                         ->with('success', 'Fase 2 berhasil diperbarui.');
    }
}
