<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\ProjectDokumentasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectDokumentasiController extends Controller
{
    /**
     * Hapus satu file dokumentasi.
     * Hanya foreman yang boleh menghapus.
     */
    public function destroy(ProjectDokumentasi $dokumentasi)
    {
        // Hapus file fisik dari storage
        Storage::disk('public')->delete($dokumentasi->path_file);

        $dokumentasi->delete();

        return back()->with('success', 'Dokumentasi berhasil dihapus.');
    }
}
