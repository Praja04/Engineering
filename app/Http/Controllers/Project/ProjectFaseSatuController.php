<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\ProjectMaster;
use App\Models\Project\ProjectFaseSatu;
use App\Models\Project\ProjectDokumentasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectFaseSatuController extends Controller
{
    /**
     * Form buat project baru (Fase 1).
     */
    public function create()
    {
        $users = \App\Models\User::orderBy('username')->get();

        return view('project.fase1.create', compact('users'));
    }

    /**
     * Simpan project baru beserta data Fase 1.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_moc'   => 'required|string|unique:project_masters,nomor_moc',
            'ejo'         => 'nullable|string|max:100',
            'deskripsi'   => 'required|string|max:255',
            'user_id'     => 'required|exists:users,id',
            'keterangan'  => 'nullable|string',
            'dokumentasi' => 'nullable|array',
            'dokumentasi.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xlsx,xls',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Buat project master
            $project = ProjectMaster::create([
                'nomor_moc'  => $request->nomor_moc,
                'ejo'        => $request->ejo,
                'deskripsi'  => $request->deskripsi,
                'user_id'    => $request->user_id,
                'keterangan' => $request->keterangan,
                'fase_aktif' => 'fase_1',
            ]);

            // 2. Buat detail Fase 1
            ProjectFaseSatu::create([
                'project_id' => $project->id,
                'ejo'        => $request->ejo,
                'deskripsi'  => $request->deskripsi,
                'user_id'    => $request->user_id,
                'keterangan' => $request->keterangan,
            ]);

            // 3. Upload dokumentasi jika ada
            if ($request->hasFile('dokumentasi')) {
                foreach ($request->file('dokumentasi') as $file) {
                    $this->simpanDokumentasi($file, $project->id, 'fase_1');
                }
            }
        });

        return redirect()->route('project.index')
                         ->with('success', 'Project berhasil dibuat.');
    }

    /**
     * Form edit Fase 1.
     */
    public function edit(ProjectMaster $project)
    {
        $users    = \App\Models\User::orderBy('username')->get();
        $faseSatu = $project->faseSatu;
        $dokumen  = $project->dokumentasiFase1;

        return view('project.fase1.edit', compact('project', 'faseSatu', 'users', 'dokumen'));
    }

    /**
     * Update data Fase 1.
     */
    public function update(Request $request, ProjectMaster $project)
    {
        $request->validate([
            'ejo'           => 'nullable|string|max:100',
            'deskripsi'     => 'required|string|max:255',
            'user_id'       => 'required|exists:users,id',
            'keterangan'    => 'nullable|string',
            'nomor_moc'     => 'required|string|unique:project_masters,nomor_moc,' . $project->id,
            'dokumentasi'   => 'nullable|array',
            'dokumentasi.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xlsx,xls',
        ]);

        DB::transaction(function () use ($request, $project) {
            // Update project master
            $project->update([
                'nomor_moc'  => $request->nomor_moc,
                'ejo'        => $request->ejo,
                'deskripsi'  => $request->deskripsi,
                'user_id'    => $request->user_id,
                'keterangan' => $request->keterangan,
            ]);

            // Update atau buat fase 1
            $project->faseSatu()->updateOrCreate(
                ['project_id' => $project->id],
                [
                    'ejo'        => $request->ejo,
                    'deskripsi'  => $request->deskripsi,
                    'user_id'    => $request->user_id,
                    'keterangan' => $request->keterangan,
                ]
            );

            // Tambah dokumentasi baru jika ada
            if ($request->hasFile('dokumentasi')) {
                foreach ($request->file('dokumentasi') as $file) {
                    $this->simpanDokumentasi($file, $project->id, 'fase_1');
                }
            }
        });

        return redirect()->route('project.show', $project)
                         ->with('success', 'Fase 1 berhasil diperbarui.');
    }

    /**
     * Helper: simpan file dokumentasi ke storage.
     */
    private function simpanDokumentasi($file, int $projectId, string $fase): void
    {
        $tipe      = str_starts_with($file->getMimeType(), 'image/') ? 'foto' : 'dokumen';
        $path      = $file->store("project/{$projectId}/{$fase}", 'public');

        ProjectDokumentasi::create([
            'project_id'   => $projectId,
            'fase'         => $fase,
            'tipe'         => $tipe,
            'nama_file'    => $file->getClientOriginalName(),
            'path_file'    => $path,
            'mime_type'    => $file->getMimeType(),
            'ukuran_file'  => $file->getSize(),
            'uploaded_by'  => auth()->id(),
        ]);
    }
}
