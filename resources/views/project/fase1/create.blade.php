@extends('layouts.app')

@section('title', 'Buat Project Baru – Fase 1')

@section('styles')
<style>
    .upload-area {
        border: 2px dashed var(--vz-border-color);
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s, background .2s;
    }
    .upload-area:hover, .upload-area.dragover {
        border-color: #405189;
        background: rgba(64,81,137,.04);
    }
    .file-preview-list .file-item {
        display: flex; align-items: center; gap: .5rem;
        padding: .4rem .75rem;
        background: var(--vz-light);
        border-radius: 6px;
        font-size: .85rem;
    }
    .step-header {
        display: flex; align-items: center; gap: .75rem;
        padding-bottom: .75rem;
        border-bottom: 1px solid var(--vz-border-color);
        margin-bottom: 1.25rem;
    }
    .step-badge {
        width: 32px; height: 32px; border-radius: 50%;
        background: #405189; color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .9rem;
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
                    <h4 class="mb-sm-0">Buat Project Baru</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('project.index') }}">Project</a></li>
                            <li class="breadcrumb-item active">Fase 1 – Inisiasi</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center" data-aos="fade-up">
            <div class="col-xl-8 col-lg-10">

                {{-- Progress Steps --}}
                <div class="card mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary rounded-pill px-3 py-2">1</span>
                                <span class="fw-semibold">Inisiasi MOC</span>
                            </div>
                            <div class="flex-grow-1 mx-3">
                                <div class="progress" style="height:4px">
                                    <div class="progress-bar bg-primary" style="width:33%"></div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <span class="badge bg-light text-muted rounded-pill px-3 py-2">2</span>
                                <span>Pengadaan</span>
                            </div>
                            <div class="flex-grow-1 mx-3">
                                <div class="progress" style="height:4px">
                                    <div class="progress-bar bg-light" style="width:0%"></div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <span class="badge bg-light text-muted rounded-pill px-3 py-2">3</span>
                                <span>Pekerjaan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="step-header">
                            <div class="step-badge">1</div>
                            <div>
                                <h5 class="mb-0">Fase 1 – Inisiasi MOC</h5>
                                <small class="text-muted">Isi data dasar project dan MOC</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="ri-error-warning-line me-1"></i>
                                <strong>Terdapat kesalahan:</strong>
                                <ul class="mb-0 mt-1 ps-3">
                                    @foreach ($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('project.fase1.store') }}"
                              enctype="multipart/form-data" id="formFase1">
                            @csrf

                            <div class="row gy-3">

                                {{-- Nomor MOC --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Nomor MOC <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nomor_moc" id="nomor_moc"
                                           class="form-control @error('nomor_moc') is-invalid @enderror"
                                           value="{{ old('nomor_moc') }}"
                                           placeholder="Contoh: MOC-2024-001">
                                    @error('nomor_moc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- EJO --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">EJO</label>
                                    <input type="text" name="ejo"
                                           class="form-control @error('ejo') is-invalid @enderror"
                                           value="{{ old('ejo') }}"
                                           placeholder="Nomor EJO (opsional)">
                                    @error('ejo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Deskripsi --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Deskripsi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="deskripsi"
                                           class="form-control @error('deskripsi') is-invalid @enderror"
                                           value="{{ old('deskripsi') }}"
                                           placeholder="Deskripsi singkat project">
                                    @error('deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- User PIC --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        PIC / Penanggung Jawab <span class="text-danger">*</span>
                                    </label>
                                    <select name="user_id" id="user_id"
                                            class="form-select select2 @error('user_id') is-invalid @enderror">
                                        <option value="">-- Pilih User --</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->username }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Keterangan --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Keterangan</label>
                                    <textarea name="keterangan" rows="3"
                                              class="form-control @error('keterangan') is-invalid @enderror"
                                              placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Upload Dokumentasi --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Dokumentasi
                                        <small class="text-muted fw-normal">
                                            (foto / dokumen, maks. 10MB per file)
                                        </small>
                                    </label>
                                    <div class="upload-area" id="uploadArea" onclick="document.getElementById('dokumentasi').click()">
                                        <i class="bx bx-cloud-upload fs-2 text-muted"></i>
                                        <p class="mb-0 text-muted mt-1">
                                            Klik atau seret file ke sini<br>
                                            <small>JPG, PNG, WEBP, PDF, DOC, DOCX, XLSX</small>
                                        </p>
                                    </div>
                                    <input type="file" name="dokumentasi[]" id="dokumentasi"
                                           class="d-none @error('dokumentasi.*') is-invalid @enderror"
                                           multiple
                                           accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xlsx,.xls">
                                    @error('dokumentasi.*')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="file-preview-list mt-2 d-flex flex-column gap-1" id="filePreview"></div>
                                </div>

                            </div>{{-- /row --}}

                            <hr class="my-4">

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('project.index') }}" class="btn btn-light">
                                    <i class="ri-arrow-left-line me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnSubmit">
                                    <i class="ri-save-line me-1"></i> Simpan & Buat Project
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    // Select2
    $('#user_id').select2({ theme: 'bootstrap-5', placeholder: '-- Pilih User --' });

    // File preview
    const input    = document.getElementById('dokumentasi');
    const preview  = document.getElementById('filePreview');
    const area     = document.getElementById('uploadArea');

    input.addEventListener('change', renderPreview);

    function renderPreview() {
        preview.innerHTML = '';
        Array.from(input.files).forEach((f, i) => {
            const icon = f.type.startsWith('image/') ? 'ri-image-line' : 'ri-file-line';
            preview.innerHTML += `
                <div class="file-item">
                    <i class="${icon} text-primary"></i>
                    <span class="flex-grow-1 text-truncate">${f.name}</span>
                    <small class="text-muted">${(f.size/1024).toFixed(1)} KB</small>
                </div>`;
        });
    }

    // Drag & drop
    area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('dragover'); });
    area.addEventListener('dragleave', () => area.classList.remove('dragover'));
    area.addEventListener('drop', e => {
        e.preventDefault();
        area.classList.remove('dragover');
        input.files = e.dataTransfer.files;
        renderPreview();
    });

    // Konfirmasi submit
    document.getElementById('formFase1').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Buat project?',
            text: 'Data Fase 1 akan disimpan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan',
            cancelButtonText: 'Batal',
        }).then(r => { if (r.isConfirmed) this.submit(); });
    });
</script>
@endsection
