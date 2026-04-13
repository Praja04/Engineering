@extends('layouts.app')

@section('title', 'Detail Project – ' . $project->nomor_moc)

@section('styles')
<style>
    .fase-timeline { position: relative; }
    .fase-timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0; bottom: 0;
        width: 2px;
        background: var(--vz-border-color);
    }
    .fase-item { position: relative; padding-left: 56px; margin-bottom: 1.5rem; }
    .fase-icon {
        position: absolute; left: 0; top: 0;
        width: 40px; height: 40px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; font-weight: 700;
        border: 3px solid var(--vz-card-bg);
        z-index: 1;
    }
    .fase-icon.done   { background: #0ab39c; color: #fff; }
    .fase-icon.active { background: #405189; color: #fff; }
    .fase-icon.pending{ background: var(--vz-border-color); color: var(--vz-secondary-color); }
    .persen-bar { height: 8px; border-radius: 4px; }
    .doc-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; }
    .doc-badge { font-size: .7rem; }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Breadcrumb --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Detail Project</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('project.index') }}">Project</a></li>
                            <li class="breadcrumb-item active">{{ $project->nomor_moc }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-checkbox-circle-line me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row" data-aos="fade-up">

            {{-- Sidebar Info --}}
            <div class="col-xl-4 col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-3 flex-shrink-0">
                                <span class="avatar-title bg-soft-primary rounded fs-3">
                                    <i class="bx bx-briefcase text-primary"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="fs-15 mb-1 fw-semibold text-truncate">{{ $project->nomor_moc }}</h5>
                                <p class="text-muted mb-0 text-truncate">{{ $project->deskripsi }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            @php
                                $badgeMap = [
                                    'fase_1' => ['class' => 'badge-soft-primary',  'label' => 'Fase 1 – Inisiasi'],
                                    'fase_2' => ['class' => 'badge-soft-warning',  'label' => 'Fase 2 – Pengadaan'],
                                    'fase_3' => ['class' => 'badge-soft-success',  'label' => 'Fase 3 – Selesai'],
                                ];
                                $badge = $badgeMap[$project->fase_aktif] ?? ['class' => 'badge-soft-secondary', 'label' => $project->fase_aktif];
                                $pct   = match($project->fase_aktif){ 'fase_1'=>33,'fase_2'=>66,'fase_3'=>100,default=>0 };
                                $bar   = match($project->fase_aktif){ 'fase_1'=>'bg-primary','fase_2'=>'bg-warning','fase_3'=>'bg-success',default=>'bg-secondary' };
                            @endphp
                            <span class="badge {{ $badge['class'] }} mb-2">{{ $badge['label'] }}</span>
                            <div class="progress persen-bar">
                                <div class="progress-bar {{ $bar }}" style="width:{{ $pct }}%"></div>
                            </div>
                            <small class="text-muted">Progress keseluruhan: {{ $pct }}%</small>
                        </div>

                        <hr>

                        <div class="row gy-2">
                            <div class="col-6">
                                <p class="text-muted mb-0 fs-12">PIC Project</p>
                                <p class="fw-semibold mb-0">{{ $project->user?->username ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <p class="text-muted mb-0 fs-12">EJO</p>
                                <p class="fw-semibold mb-0">{{ $project->ejo ?? '-' }}</p>
                            </div>
                            <div class="col-12">
                                <p class="text-muted mb-0 fs-12">Keterangan</p>
                                <p class="mb-0">{{ $project->keterangan ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <p class="text-muted mb-0 fs-12">Dibuat</p>
                                <p class="fw-semibold mb-0">{{ $project->created_at?->format('d M Y') }}</p>
                            </div>
                        </div>

                        <hr>

                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2">
                            <a href="{{ route('project.fase1.edit', $project) }}"
                               class="btn btn-soft-warning btn-sm">
                                <i class="ri-edit-line me-1"></i> Edit Fase 1
                            </a>

                            @if ($project->faseSatu && $project->fase_aktif === 'fase_1')
                                <a href="{{ route('project.fase2.create', $project) }}"
                                   class="btn btn-soft-primary btn-sm">
                                    <i class="ri-arrow-right-line me-1"></i> Lanjut ke Fase 2
                                </a>
                            @endif

                            @if ($project->faseDua && $project->fase_aktif === 'fase_2')
                                <a href="{{ route('project.fase2.edit', $project) }}"
                                   class="btn btn-soft-warning btn-sm">
                                    <i class="ri-edit-line me-1"></i> Edit Fase 2
                                </a>
                                <a href="{{ route('project.fase3.create', $project) }}"
                                   class="btn btn-soft-primary btn-sm">
                                    <i class="ri-arrow-right-line me-1"></i> Lanjut ke Fase 3
                                </a>
                            @endif

                            @if ($project->faseTiga && $project->fase_aktif === 'fase_3')
                                <a href="{{ route('project.fase3.edit', $project) }}"
                                   class="btn btn-soft-warning btn-sm">
                                    <i class="ri-edit-line me-1"></i> Edit Fase 3
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timeline Fase --}}
            <div class="col-xl-8 col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Progress Fase</h5>
                    </div>
                    <div class="card-body">
                        <div class="fase-timeline">

                            {{-- ===== FASE 1 ===== --}}
                            @php $f1Done = !is_null($project->faseSatu); @endphp
                            <div class="fase-item">
                                <div class="fase-icon {{ $f1Done ? 'done' : 'pending' }}">1</div>
                                <div class="card mb-0 border">
                                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-semibold">Fase 1 – Inisiasi MOC</h6>
                                        @if ($f1Done)
                                            <span class="badge badge-soft-success">Selesai</span>
                                        @else
                                            <span class="badge badge-soft-secondary">Belum diisi</span>
                                        @endif
                                    </div>
                                    @if ($f1Done)
                                        <div class="card-body py-3">
                                            <div class="row gy-2">
                                                <div class="col-md-4">
                                                    <p class="text-muted mb-0 fs-12">EJO</p>
                                                    <p class="mb-0 fw-semibold">{{ $project->faseSatu->ejo ?? '-' }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="text-muted mb-0 fs-12">PIC</p>
                                                    <p class="mb-0 fw-semibold">{{ $project->faseSatu->user?->username ?? '-' }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="text-muted mb-0 fs-12">Tanggal</p>
                                                    <p class="mb-0 fw-semibold">{{ $project->faseSatu->created_at?->format('d M Y') }}</p>
                                                </div>
                                                @if ($project->faseSatu->keterangan)
                                                    <div class="col-12">
                                                        <p class="text-muted mb-0 fs-12">Keterangan</p>
                                                        <p class="mb-0">{{ $project->faseSatu->keterangan }}</p>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Dokumentasi Fase 1 --}}
                                            @if ($project->dokumentasiFase1?->count())
                                                <hr class="my-2">
                                                <p class="text-muted fs-12 mb-2">
                                                    <i class="ri-attachment-2 me-1"></i>
                                                    Dokumentasi ({{ $project->dokumentasiFase1->count() }} file)
                                                </p>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach ($project->dokumentasiFase1 as $dok)
                                                        <div class="position-relative">
                                                            @if ($dok->tipe === 'foto')
                                                                <a href="{{ Storage::url($dok->path_file) }}" target="_blank">
                                                                    <img src="{{ Storage::url($dok->path_file) }}"
                                                                         alt="{{ $dok->nama_file }}"
                                                                         class="doc-thumb border">
                                                                </a>
                                                            @else
                                                                <a href="{{ Storage::url($dok->path_file) }}" target="_blank"
                                                                   class="btn btn-soft-secondary btn-sm">
                                                                    <i class="ri-file-line me-1"></i>
                                                                    {{ Str::limit($dok->nama_file, 20) }}
                                                                </a>
                                                            @endif
                                                            <form method="POST"
                                                                  action="{{ route('project.dokumentasi.destroy', $dok) }}"
                                                                  class="d-inline form-hapus-dok">
                                                                @csrf @method('DELETE')
                                                                <button type="submit"
                                                                        class="btn btn-danger btn-xs position-absolute top-0 end-0"
                                                                        title="Hapus">
                                                                    <i class="ri-close-line"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- ===== FASE 2 ===== --}}
                            @php $f2Done = !is_null($project->faseDua); @endphp
                            <div class="fase-item">
                                <div class="fase-icon {{ $f2Done ? 'done' : ($f1Done ? 'active' : 'pending') }}">2</div>
                                <div class="card mb-0 border">
                                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-semibold">Fase 2 – Pengadaan</h6>
                                        @if ($f2Done)
                                            <span class="badge badge-soft-success">Selesai</span>
                                        @elseif ($f1Done)
                                            <span class="badge badge-soft-warning">Menunggu input</span>
                                        @else
                                            <span class="badge badge-soft-secondary">Terkunci</span>
                                        @endif
                                    </div>
                                    @if ($f2Done)
                                        <div class="card-body py-3">
                                            <div class="row gy-2">
                                                <div class="col-md-4">
                                                    <p class="text-muted mb-0 fs-12">EJO</p>
                                                    <p class="mb-0 fw-semibold">{{ $project->faseDua->ejo ?? '-' }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="text-muted mb-0 fs-12">Nomor IO</p>
                                                    <p class="mb-0 fw-semibold">{{ $project->faseDua->nomor_io ?? '-' }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="text-muted mb-0 fs-12">PIC</p>
                                                    <p class="mb-0 fw-semibold">{{ $project->faseDua->user?->username ?? '-' }}</p>
                                                </div>
                                                @if ($project->faseDua->keterangan)
                                                    <div class="col-12">
                                                        <p class="text-muted mb-0 fs-12">Keterangan</p>
                                                        <p class="mb-0">{{ $project->faseDua->keterangan }}</p>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Progress Pengadaan --}}
                                            <hr class="my-2">
                                            <p class="text-muted fs-12 mb-2">Progress Pengadaan</p>
                                            <div class="row gy-2">
                                                @foreach (['PR' => $project->faseDua->persen_pr, 'PO' => $project->faseDua->persen_po, 'GR' => $project->faseDua->persen_gr] as $label => $val)
                                                    <div class="col-md-4">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <small class="fw-semibold">{{ $label }}</small>
                                                            <small>{{ $val }}%</small>
                                                        </div>
                                                        <div class="progress persen-bar">
                                                            <div class="progress-bar bg-info" style="width:{{ $val }}%"></div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- ===== FASE 3 ===== --}}
                            @php $f3Done = !is_null($project->faseTiga); @endphp
                            <div class="fase-item" style="margin-bottom:0">
                                <div class="fase-icon {{ $f3Done ? 'done' : ($f2Done ? 'active' : 'pending') }}">3</div>
                                <div class="card mb-0 border">
                                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-semibold">Fase 3 – Pelaksanaan Pekerjaan</h6>
                                        @if ($f3Done)
                                            <span class="badge badge-soft-success">Selesai</span>
                                        @elseif ($f2Done)
                                            <span class="badge badge-soft-warning">Menunggu input</span>
                                        @else
                                            <span class="badge badge-soft-secondary">Terkunci</span>
                                        @endif
                                    </div>
                                    @if ($f3Done)
                                        <div class="card-body py-3">
                                            <div class="row gy-2">
                                                <div class="col-md-4">
                                                    <p class="text-muted mb-0 fs-12">EJO</p>
                                                    <p class="mb-0 fw-semibold">{{ $project->faseTiga->ejo ?? '-' }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="text-muted mb-0 fs-12">PIC</p>
                                                    <p class="mb-0 fw-semibold">{{ $project->faseTiga->user?->username ?? '-' }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="text-muted mb-0 fs-12">Tanggal</p>
                                                    <p class="mb-0 fw-semibold">{{ $project->faseTiga->created_at?->format('d M Y') }}</p>
                                                </div>
                                                @if ($project->faseTiga->keterangan)
                                                    <div class="col-12">
                                                        <p class="text-muted mb-0 fs-12">Keterangan</p>
                                                        <p class="mb-0">{{ $project->faseTiga->keterangan }}</p>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Dokumentasi Fase 3 --}}
                                            @if ($project->dokumentasiFase3?->count())
                                                <hr class="my-2">
                                                <p class="text-muted fs-12 mb-2">
                                                    <i class="ri-attachment-2 me-1"></i>
                                                    Dokumentasi ({{ $project->dokumentasiFase3->count() }} file)
                                                </p>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach ($project->dokumentasiFase3 as $dok)
                                                        <div class="position-relative">
                                                            @if ($dok->tipe === 'foto')
                                                                <a href="{{ Storage::url($dok->path_file) }}" target="_blank">
                                                                    <img src="{{ Storage::url($dok->path_file) }}"
                                                                         alt="{{ $dok->nama_file }}"
                                                                         class="doc-thumb border">
                                                                </a>
                                                            @else
                                                                <a href="{{ Storage::url($dok->path_file) }}" target="_blank"
                                                                   class="btn btn-soft-secondary btn-sm">
                                                                    <i class="ri-file-line me-1"></i>
                                                                    {{ Str::limit($dok->nama_file, 20) }}
                                                                </a>
                                                            @endif
                                                            <form method="POST"
                                                                  action="{{ route('project.dokumentasi.destroy', $dok) }}"
                                                                  class="d-inline form-hapus-dok">
                                                                @csrf @method('DELETE')
                                                                <button type="submit"
                                                                        class="btn btn-danger btn-xs position-absolute top-0 end-0"
                                                                        title="Hapus">
                                                                    <i class="ri-close-line"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>{{-- /fase-timeline --}}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Konfirmasi hapus dokumentasi
    document.querySelectorAll('.form-hapus-dok').forEach(form => {
        form.querySelector('button').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus dokumentasi?',
                text: 'File ini akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#f06548',
            }).then(res => {
                if (res.isConfirmed) form.submit();
            });
        });
    });
</script>
@endsection
