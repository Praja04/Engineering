<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\ProjectMaster;
use App\Models\Project\ProjectFaseTiga;
use App\Models\Project\ProjectDokumentasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectFaseTigaController extends Controller
{
    /**
     * Form input Fase 3.
     * Hanya bisa diakses jika project sudah di fase_2 dan faseDua sudah ada.
     */
    public function create(ProjectMaster $project)
    {

        $users = \App\Models\User::orderBy('username')->get();

        return view('project.fase3.create', compact('project', 'users'));
    }

    /**
     * Simpan data Fase 3 dan update fase_aktif project.
     */
    public function store(Request $request, ProjectMaster $project)
    {

        $request->validate([
            'ejo'           => 'nullable|string|max:100',
            'deskripsi'     => 'required|string|max:255',
            'user_id'       => 'required|exists:users,id',
            'keterangan'    => 'nullable|string',
            'dokumentasi'   => 'nullable|array',
            'dokumentasi.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xlsx,xls',
        ]);

        DB::transaction(function () use ($request, $project) {
            ProjectFaseTiga::create([
                'project_id' => $project->id,
                'ejo'        => $request->ejo,
                'deskripsi'  => $request->deskripsi,
                'user_id'    => $request->user_id,
                'keterangan' => $request->keterangan,
            ]);

            // Naikkan fase_aktif project ke fase_3
            $project->update(['fase_aktif' => 'fase_3']);

            // Upload dokumentasi jika ada
            if ($request->hasFile('dokumentasi')) {
                foreach ($request->file('dokumentasi') as $file) {
                    $this->simpanDokumentasi($file, $project->id, 'fase_3');
                }
            }
        });

        return redirect()->route('project.show', $project)
                         ->with('success', 'Fase 3 berhasil disimpan. Project selesai.');
    }

    /**
     * Form edit Fase 3.
     */
    public function edit(ProjectMaster $project)
    {

        $users    = \App\Models\User::orderBy('username')->get();
        $faseTiga = $project->faseTiga;
        $dokumen  = $project->dokumentasiFase3;

        return view('project.fase3.edit', compact('project', 'faseTiga', 'users', 'dokumen'));
    }

    /**
     * Update data Fase 3.
     */
    public function update(Request $request, ProjectMaster $project)
    {

        $request->validate([
            'ejo'           => 'nullable|string|max:100',
            'deskripsi'     => 'required|string|max:255',
            'user_id'       => 'required|exists:users,id',
            'keterangan'    => 'nullable|string',
            'dokumentasi'   => 'nullable|array',
            'dokumentasi.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xlsx,xls',
        ]);

        DB::transaction(function () use ($request, $project) {
            $project->faseTiga()->update([
                'ejo'        => $request->ejo,
                'deskripsi'  => $request->deskripsi,
                'user_id'    => $request->user_id,
                'keterangan' => $request->keterangan,
            ]);

            // Tambah dokumentasi baru jika ada
            if ($request->hasFile('dokumentasi')) {
                foreach ($request->file('dokumentasi') as $file) {
                    $this->simpanDokumentasi($file, $project->id, 'fase_3');
                }
            }
        });

        return redirect()->route('project.show', $project)
                         ->with('success', 'Fase 3 berhasil diperbarui.');
    }

    /**
     * Helper: simpan file dokumentasi ke storage.
     */
    private function simpanDokumentasi($file, int $projectId, string $fase): void
    {
        $tipe = str_starts_with($file->getMimeType(), 'image/') ? 'foto' : 'dokumen';
        $path = $file->store("project/{$projectId}/{$fase}", 'public');

        ProjectDokumentasi::create([
            'project_id'  => $projectId,
            'fase'        => $fase,
            'tipe'        => $tipe,
            'nama_file'   => $file->getClientOriginalName(),
            'path_file'   => $path,
            'mime_type'   => $file->getMimeType(),
            'ukuran_file' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);
    }
}
