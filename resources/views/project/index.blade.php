@extends('layouts.app')

@section('title', 'Dashboard Project')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Page Title --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dashboard Project</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Engineering</a></li>
                            <li class="breadcrumb-item active">Project</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-checkbox-circle-line me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="row" data-aos="fade-up">
            @php
                $total   = $projects->total();
                $fase1   = $projects->getCollection()->where('fase_aktif','fase_1')->count();
                $fase2   = $projects->getCollection()->where('fase_aktif','fase_2')->count();
                $fase3   = $projects->getCollection()->where('fase_aktif','fase_3')->count();
            @endphp
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="fw-medium text-muted mb-0">Total Project</p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <span class="counter-value" data-target="{{ $projects->total() }}">0</span>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-info rounded-circle fs-2">
                                        <i class="bx bx-briefcase text-info"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="fw-medium text-muted mb-0">Fase 1 – Inisiasi</p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <span class="counter-value" data-target="{{ $fase1 }}">0</span>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-primary rounded-circle fs-2">
                                        <i class="bx bx-flag text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="fw-medium text-muted mb-0">Fase 2 – Pengadaan</p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <span class="counter-value" data-target="{{ $fase2 }}">0</span>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-warning rounded-circle fs-2">
                                        <i class="bx bx-cart text-warning"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="fw-medium text-muted mb-0">Fase 3 – Selesai</p>
                                <h2 class="mt-4 ff-secondary fw-semibold">
                                    <span class="counter-value" data-target="{{ $fase3 }}">0</span>
                                </h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-success rounded-circle fs-2">
                                        <i class="bx bx-check-double text-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- /Stats --}}

        {{-- Table Card --}}
        <div class="row" data-aos="fade-up" data-aos-delay="100">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Daftar Project</h5>
                        <a href="{{ route('project.fase1.create') }}" class="btn btn-primary btn-sm">
                            <i class="ri-add-line me-1"></i> Buat Project Baru
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap align-middle mb-0" id="projectTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Nomor MOC</th>
                                        <th>Deskripsi</th>
                                        <th>PIC</th>
                                        <th>Fase Aktif</th>
                                        <th>Progres</th>
                                        <th>Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($projects as $i => $project)
                                        <tr>
                                            <td>{{ $projects->firstItem() + $i }}</td>
                                            <td>
                                                <span class="fw-semibold">{{ $project->nomor_moc }}</span>
                                            </td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width:220px"
                                                      title="{{ $project->deskripsi }}">
                                                    {{ $project->deskripsi }}
                                                </span>
                                            </td>
                                            <td>{{ $project->user?->username ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $badgeMap = [
                                                        'fase_1' => ['class' => 'badge-soft-primary',  'label' => 'Fase 1 – Inisiasi'],
                                                        'fase_2' => ['class' => 'badge-soft-warning',  'label' => 'Fase 2 – Pengadaan'],
                                                        'fase_3' => ['class' => 'badge-soft-success',  'label' => 'Fase 3 – Selesai'],
                                                    ];
                                                    $badge = $badgeMap[$project->fase_aktif] ?? ['class' => 'badge-soft-secondary', 'label' => $project->fase_aktif];
                                                @endphp
                                                <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                            </td>
                                            <td style="min-width:140px">
                                                @php
                                                    $pct = match($project->fase_aktif) {
                                                        'fase_1' => 33,
                                                        'fase_2' => 66,
                                                        'fase_3' => 100,
                                                        default  => 0,
                                                    };
                                                    $bar = match($project->fase_aktif) {
                                                        'fase_1' => 'bg-primary',
                                                        'fase_2' => 'bg-warning',
                                                        'fase_3' => 'bg-success',
                                                        default  => 'bg-secondary',
                                                    };
                                                @endphp
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height:6px">
                                                        <div class="progress-bar {{ $bar }}" style="width:{{ $pct }}%"></div>
                                                    </div>
                                                    <small class="text-muted">{{ $pct }}%</small>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $project->created_at?->format('d M Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('project.show', $project) }}"
                                                       class="btn btn-soft-info btn-sm" title="Detail">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                    <a href="{{ route('project.fase1.edit', $project) }}"
                                                       class="btn btn-soft-warning btn-sm" title="Edit Fase 1">
                                                        <i class="ri-edit-line"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="bx bx-folder-open fs-1 d-block mb-2"></i>
                                                Belum ada project.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-end mt-3">
                            {{ $projects->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    // Counter animation
    document.querySelectorAll('.counter-value').forEach(el => {
        const target = +el.dataset.target;
        const step   = Math.max(1, Math.floor(target / 30));
        let current  = 0;
        const timer  = setInterval(() => {
            current += step;
            if (current >= target) { el.textContent = target; clearInterval(timer); }
            else el.textContent = current;
        }, 40);
    });
</script>
@endsection
