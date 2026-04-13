@extends('layouts.app')

@section('title', 'Input Fase 2 – ' . $project->nomor_moc)

@section('styles')
<style>
    .step-header { display:flex; align-items:center; gap:.75rem; padding-bottom:.75rem; border-bottom:1px solid var(--vz-border-color); margin-bottom:1.25rem; }
    .step-badge  { width:32px; height:32px; border-radius:50%; background:#f7b84b; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.9rem; flex-shrink:0; }
    .persen-preview { font-size:.8rem; color:var(--vz-secondary-color); }
    .range-group label { font-size:.85rem; }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Input Fase 2 – Pengadaan</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('project.index') }}">Project</a></li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('project.show', $project) }}">{{ $project->nomor_moc }}</a>
                            </li>
                            <li class="breadcrumb-item active">Fase 2</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step indicator --}}
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <div class="card mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <span class="badge bg-success rounded-pill px-3 py-2"><i class="ri-check-line"></i></span>
                                <span>Inisiasi</span>
                            </div>
                            <div class="flex-grow-1 mx-3">
                                <div class="progress" style="height:4px">
                                    <div class="progress-bar bg-success" style="width:100%"></div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning rounded-pill px-3 py-2">2</span>
                                <span class="fw-semibold">Pengadaan</span>
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
            </div>
        </div>

        <div class="row justify-content-center" data-aos="fade-up">
            <div class="col-xl-8 col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <div class="step-header">
                            <div class="step-badge">2</div>
                            <div>
                                <h5 class="mb-0">Fase 2 – Pengadaan</h5>
                                <small class="text-muted">Data pengadaan untuk project <strong>{{ $project->nomor_moc }}</strong></small>
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

                        <form method="POST" action="{{ route('project.fase2.store', $project) }}" id="formFase2">
                            @csrf

                            <div class="row gy-3">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">EJO</label>
                                    <input type="text" name="ejo"
                                           class="form-control @error('ejo') is-invalid @enderror"
                                           value="{{ old('ejo') }}" placeholder="Nomor EJO (opsional)">
                                    @error('ejo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Nomor IO <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nomor_io"
                                           class="form-control @error('nomor_io') is-invalid @enderror"
                                           value="{{ old('nomor_io') }}" placeholder="Nomor IO">
                                    @error('nomor_io')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Deskripsi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="deskripsi"
                                           class="form-control @error('deskripsi') is-invalid @enderror"
                                           value="{{ old('deskripsi') }}" placeholder="Deskripsi pengadaan">
                                    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

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
                                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Keterangan</label>
                                    <textarea name="keterangan" rows="3"
                                              class="form-control @error('keterangan') is-invalid @enderror"
                                              placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                                    @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Progress Pengadaan --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Progress Pengadaan</label>
                                    <div class="row gy-3 range-group">

                                        {{-- PR --}}
                                        <div class="col-md-4">
                                            <label class="form-label d-flex justify-content-between">
                                                <span>Persen PR <span class="text-danger">*</span></span>
                                                <span class="persen-preview fw-semibold" id="labelPR">{{ old('persen_pr', 0) }}%</span>
                                            </label>
                                            <input type="range" class="form-range" name="persen_pr" id="sliderPR"
                                                   min="0" max="100" step="1"
                                                   value="{{ old('persen_pr', 0) }}"
                                                   oninput="document.getElementById('labelPR').textContent=this.value+'%';
                                                            document.getElementById('numPR').value=this.value">
                                            <input type="number" id="numPR"
                                                   class="form-control form-control-sm mt-1 @error('persen_pr') is-invalid @enderror"
                                                   value="{{ old('persen_pr', 0) }}" min="0" max="100"
                                                   oninput="document.getElementById('sliderPR').value=this.value;
                                                            document.getElementById('labelPR').textContent=this.value+'%'"
                                                   readonly>
                                            @error('persen_pr')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        {{-- PO --}}
                                        <div class="col-md-4">
                                            <label class="form-label d-flex justify-content-between">
                                                <span>Persen PO <span class="text-danger">*</span></span>
                                                <span class="persen-preview fw-semibold" id="labelPO">{{ old('persen_po', 0) }}%</span>
                                            </label>
                                            <input type="range" class="form-range" name="persen_po" id="sliderPO"
                                                   min="0" max="100" step="1"
                                                   value="{{ old('persen_po', 0) }}"
                                                   oninput="document.getElementById('labelPO').textContent=this.value+'%';
                                                            document.getElementById('numPO').value=this.value">
                                            <input type="number" id="numPO"
                                                   class="form-control form-control-sm mt-1 @error('persen_po') is-invalid @enderror"
                                                   value="{{ old('persen_po', 0) }}" min="0" max="100"
                                                   oninput="document.getElementById('sliderPO').value=this.value;
                                                            document.getElementById('labelPO').textContent=this.value+'%'"
                                                   readonly>
                                            @error('persen_po')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        {{-- GR --}}
                                        <div class="col-md-4">
                                            <label class="form-label d-flex justify-content-between">
                                                <span>Persen GR <span class="text-danger">*</span></span>
                                                <span class="persen-preview fw-semibold" id="labelGR">{{ old('persen_gr', 0) }}%</span>
                                            </label>
                                            <input type="range" class="form-range" name="persen_gr" id="sliderGR"
                                                   min="0" max="100" step="1"
                                                   value="{{ old('persen_gr', 0) }}"
                                                   oninput="document.getElementById('labelGR').textContent=this.value+'%';
                                                            document.getElementById('numGR').value=this.value">
                                            <input type="number" id="numGR"
                                                   class="form-control form-control-sm mt-1 @error('persen_gr') is-invalid @enderror"
                                                   value="{{ old('persen_gr', 0) }}" min="0" max="100"
                                                   oninput="document.getElementById('sliderGR').value=this.value;
                                                            document.getElementById('labelGR').textContent=this.value+'%'"
                                                   readonly>
                                            @error('persen_gr')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <hr class="my-4">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('project.show', $project) }}" class="btn btn-light">
                                    <i class="ri-arrow-left-line me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="ri-save-line me-1"></i> Simpan Fase 2
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
    $('#user_id').select2({ theme: 'bootstrap-5', placeholder: '-- Pilih User --' });

    // Make number inputs editable but sync with range
    ['PR','PO','GR'].forEach(x => {
        document.getElementById('num'+x).removeAttribute('readonly');
    });

    document.getElementById('formFase2').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ title: 'Simpan Fase 2?', icon: 'question',
            showCancelButton: true, confirmButtonText: 'Ya, simpan', cancelButtonText: 'Batal' })
            .then(r => { if (r.isConfirmed) this.submit(); });
    });
</script>
@endsection
