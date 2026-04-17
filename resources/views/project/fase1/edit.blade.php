@extends('layouts.app')

@section('title', 'Edit Fase 1 – ' . $project->nomor_moc)

@section('styles')
<style>
    .upload-area {
        border: 2px dashed var(--vz-border-color);
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s, background .2s;
    }

    .upload-area:hover,
    .upload-area.dragover {
        border-color: #405189;
        background: rgba(64, 81, 137, .04);
    }

    .file-preview-list .file-item {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .4rem .75rem;
        background: var(--vz-light);
        border-radius: 6px;
        font-size: .85rem;
    }

    .doc-thumb {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 6px;
    }

    .step-header {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding-bottom: .75rem;
        border-bottom: 1px solid var(--vz-border-color);
        margin-bottom: 1.25rem;
    }

    .step-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f7b84b;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: .9rem;
        flex-shrink: 0;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit Fase 1</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('project.index') }}">Project</a></li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('project.show', $project) }}">{{ $project->nomor_moc }}</a>
                            </li>
                            <li class="breadcrumb-item active">Edit Fase 1</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center" data-aos="fade-up">
            <div class="col-xl-8 col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <div class="step-header">
                            <div class="step-badge">1</div>
                            <div>
                                <h5 class="mb-0">Fase 1 – Inisiasi MOC</h5>
                                <small class="text-muted">Edit data inisiasi project <strong>{{ $project->nomor_moc }}</strong></small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="ri-error-warning-line me-1"></i> <strong>Terdapat kesalahan:</strong>
                            <ul class="mb-0 mt-1 ps-3">
                                @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        {{-- ═══════════════════════════════════════════════════
                             FORM UTAMA — tidak boleh ada <form> lain di dalamnya
                        ════════════════════════════════════════════════════ --}}
                        <form method="POST" action="{{ route('project.fase1.update', $project) }}" enctype="multipart/form-data" id="formFase1Edit">
                            @csrf
                            @method('PUT')

                            <div class="row gy-3">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Nomor MOC <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nomor_moc" class="form-control @error('nomor_moc') is-invalid @enderror" value="{{ old('nomor_moc', $project->nomor_moc) }}">
                                    @error('nomor_moc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">EJO</label>
                                    <input type="text" name="ejo" class="form-control @error('ejo') is-invalid @enderror" value="{{ old('ejo', $faseSatu?->ejo) }}" placeholder="Opsional">
                                    @error('ejo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Deskripsi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" value="{{ old('deskripsi', $faseSatu?->deskripsi ?? $project->deskripsi) }}">
                                    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        PIC / Penanggung Jawab <span class="text-danger">*</span>
                                    </label>
                                    <select name="user_id" id="user_id" class="form-select select2 @error('user_id') is-invalid @enderror">
                                        <option value="">-- Pilih User --</option>
                                        @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $faseSatu?->user_id ?? $project->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->username }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Keterangan</label>
                                    <textarea name="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Catatan tambahan (opsional)">{{ old('keterangan', $faseSatu?->keterangan ?? $project->keterangan) }}</textarea>
                                    @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Dokumentasi Tersimpan
                                     Tombol hapus BUKAN <form>, melainkan <button> biasa
                                     dengan data-url yang akan dipakai oleh form tersembunyi di luar --}}
                                @if ($dokumen?->count())
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Dokumentasi Tersimpan
                                        <span class="badge badge-soft-info ms-1">{{ $dokumen->count() }} file</span>
                                    </label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($dokumen as $dok)
                                        <div class="position-relative border rounded p-1" style="min-width:80px">
                                            @if ($dok->tipe === 'foto')
                                            <a href="{{ Storage::url($dok->path_file) }}" target="_blank">
                                                <img src="{{ Storage::url($dok->path_file) }}" class="doc-thumb d-block">
                                            </a>
                                            @else
                                            <a href="{{ Storage::url($dok->path_file) }}" target="_blank" class="d-flex flex-column align-items-center text-muted text-decoration-none p-2">
                                                <i class="ri-file-line fs-2"></i>
                                                <small class="text-truncate" style="max-width:70px">{{ $dok->nama_file }}</small>
                                            </a>
                                            @endif

                                            {{-- Gunakan button biasa, bukan nested <form> --}}
                                            <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 btn-hapus-dok" data-url="{{ route('project.dokumentasi.destroy', $dok) }}" style="width:18px;height:18px;padding:0;font-size:.6rem" title="Hapus file">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                {{-- Upload Baru --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Tambah Dokumentasi Baru
                                        <small class="text-muted fw-normal">(opsional)</small>
                                    </label>
                                    <div class="upload-area" id="uploadArea">
                                        <i class="bx bx-cloud-upload fs-2 text-muted"></i>
                                        <p class="mb-0 text-muted mt-1">
                                            Klik atau seret file ke sini<br>
                                            <small>JPG, PNG, WEBP, PDF, DOC, DOCX, XLSX • Maks 10MB</small>
                                        </p>
                                    </div>
                                    <input type="file" name="dokumentasi[]" id="dokumentasi" class="d-none" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xlsx,.xls">
                                    <div class="file-preview-list mt-2 d-flex flex-column gap-1" id="filePreview"></div>
                                </div>

                            </div>{{-- /row --}}

                            <hr class="my-4">

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('project.show', $project) }}" class="btn btn-light">
                                    <i class="ri-arrow-left-line me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-warning" id="btnUpdate">
                                    <i class="ri-save-line me-1"></i> Simpan Perubahan
                                </button>
                            </div>

                        </form>
                        {{-- akhir #formFase1Edit --}}

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     Form hapus dokumentasi — di LUAR form utama agar tidak nested
     Action diisi secara dinamis via JavaScript
════════════════════════════════════════════════════════════ --}}
<form id="formHapusDok" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ── Select2 ─────────────────────────────────────────────────
        if (typeof $.fn.select2 !== 'undefined') {
            $('#user_id').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih User --'
            });
        }

        // ── File preview & drag-drop ─────────────────────────────────
        const input = document.getElementById('dokumentasi');
        const preview = document.getElementById('filePreview');
        const area = document.getElementById('uploadArea');

        // Klik area upload → trigger input file
        area.addEventListener('click', () => input.click());

        input.addEventListener('change', renderPreview);

        function renderPreview() {
            preview.innerHTML = '';
            Array.from(input.files).forEach(f => {
                const icon = f.type.startsWith('image/') ? 'ri-image-line' : 'ri-file-line';
                preview.innerHTML += `
                <div class="file-item">
                    <i class="${icon} text-primary"></i>
                    <span class="flex-grow-1 text-truncate">${f.name}</span>
                    <small class="text-muted">${(f.size / 1024).toFixed(1)} KB</small>
                </div>`;
            });
        }

        area.addEventListener('dragover', e => {
            e.preventDefault();
            area.classList.add('dragover');
        });
        area.addEventListener('dragleave', () => area.classList.remove('dragover'));
        area.addEventListener('drop', e => {
            e.preventDefault();
            area.classList.remove('dragover');
            input.files = e.dataTransfer.files;
            renderPreview();
        });

        // ── Hapus dokumentasi ────────────────────────────────────────
        // Gunakan form tersembunyi #formHapusDok yang ada DI LUAR form utama
        const formHapus = document.getElementById('formHapusDok');

        document.querySelectorAll('.btn-hapus-dok').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.dataset.url;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hapus file ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#f06548',
                    }).then(r => {
                        if (r.isConfirmed) {
                            formHapus.action = url;
                            formHapus.submit();
                        }
                    });
                } else {
                    if (confirm('Hapus file ini?')) {
                        formHapus.action = url;
                        formHapus.submit();
                    }
                }
            });
        });

        // ── Konfirmasi simpan ────────────────────────────────────────
        document.getElementById('formFase1Edit').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Simpan perubahan?',
                    text: 'Data Fase 1 akan diperbarui.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, simpan',
                    cancelButtonText: 'Batal',
                }).then(r => {
                    if (r.isConfirmed) form.submit();
                });
            } else {
                form.submit();
            }
        });

    });
</script>
@endsection