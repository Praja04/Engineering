@extends('layouts.app')

@section('title', 'Edit Fase 2 – ' . $project->nomor_moc)

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
                    <h4 class="mb-sm-0">Edit Fase 2</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('project.index') }}">Project</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('project.show', $project) }}">{{ $project->nomor_moc }}</a></li>
                            <li class="breadcrumb-item active">Edit Fase 2</li>
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
                            <div class="step-badge">2</div>
                            <div>
                                <h5 class="mb-0">Fase 2 – Pengadaan</h5>
                                <small class="text-muted">Edit data pengadaan project <strong>{{ $project->nomor_moc }}</strong></small>
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

                        <form method="POST" action="{{ route('project.fase2.update', $project) }}" id="formFase2Edit">
                            @csrf @method('PUT')

                            <div class="row gy-3">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">EJO</label>
                                    <input type="text" name="ejo"
                                           class="form-control @error('ejo') is-invalid @enderror"
                                           value="{{ old('ejo', $faseDua?->ejo) }}" placeholder="Opsional">
                                    @error('ejo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Nomor IO <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nomor_io"
                                           class="form-control @error('nomor_io') is-invalid @enderror"
                                           value="{{ old('nomor_io', $faseDua?->nomor_io) }}">
                                    @error('nomor_io')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Deskripsi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="deskripsi"
                                           class="form-control @error('deskripsi') is-invalid @enderror"
                                           value="{{ old('deskripsi', $faseDua?->deskripsi) }}">
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
                                                {{ old('user_id', $faseDua?->user_id) == $user->id ? 'selected' : '' }}>
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
                                              placeholder="Catatan tambahan (opsional)">{{ old('keterangan', $faseDua?->keterangan) }}</textarea>
                                    @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Progress Pengadaan --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Progress Pengadaan</label>
                                    <div class="row gy-3 range-group">
                                        @foreach ([
                                            ['key' => 'persen_pr', 'id' => 'PR', 'label' => 'Persen PR'],
                                            ['key' => 'persen_po', 'id' => 'PO', 'label' => 'Persen PO'],
                                            ['key' => 'persen_gr', 'id' => 'GR', 'label' => 'Persen GR'],
                                        ] as $item)
                                            @php $val = old($item['key'], $faseDua?->{$item['key']} ?? 0); @endphp
                                            <div class="col-md-4">
                                                <label class="form-label d-flex justify-content-between">
                                                    <span>{{ $item['label'] }} <span class="text-danger">*</span></span>
                                                    <span class="persen-preview fw-semibold" id="label{{ $item['id'] }}">{{ $val }}%</span>
                                                </label>
                                                <input type="range" class="form-range" name="{{ $item['key'] }}"
                                                       id="slider{{ $item['id'] }}"
                                                       min="0" max="100" step="1" value="{{ $val }}"
                                                       oninput="document.getElementById('label{{ $item['id'] }}').textContent=this.value+'%';
                                                                document.getElementById('num{{ $item['id'] }}').value=this.value">
                                                <input type="number" id="num{{ $item['id'] }}"
                                                       class="form-control form-control-sm mt-1 @error($item['key']) is-invalid @enderror"
                                                       value="{{ $val }}" min="0" max="100"
                                                       oninput="document.getElementById('slider{{ $item['id'] }}').value=this.value;
                                                                document.getElementById('label{{ $item['id'] }}').textContent=this.value+'%'">
                                                @error($item['key'])<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>

                            <hr class="my-4">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('project.show', $project) }}" class="btn btn-light">
                                    <i class="ri-arrow-left-line me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="ri-save-line me-1"></i> Simpan Perubahan
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
</script>
@endsection
