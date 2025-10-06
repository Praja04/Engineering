@extends('layouts.app')

@section('title', 'Pilih Mesin - Machine Scoring')

@section('styles')
<style>
    .machine-card {
        transition: all 0.3s ease;
        cursor: pointer;
        border-left: 4px solid #405189;
    }

    .machine-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .status-badge {
        font-size: 0.875rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 500;
    }

    .process-count-badge {

        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
    }

    .machine-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 2rem;
    }

    /* Membuat efek hover "nimbul" */
    .klik-link {
        transition: all 0.2s ease;
        transform: translateY(0);
    }

    .klik-link:hover {
        transform: translateY(-3px);
        text-shadow: 0 2px 6px rgba(255, 255, 255, 0.4);
    }

    .klik-link span {
        font-size: 0.8rem;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="ri-settings-3-line align-middle me-2"></i>
                        Machine Scoring
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Scoring Mesin</a></li>
                            <li class="breadcrumb-item active">Pilih Mesin</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Total Mesin</p>
                                <h4 class="mb-0">{{ $machines->count() }}</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-primary rounded fs-3">
                                        <i class="bx bx-cog text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Mesin Aktif</p>
                                <h4 class="mb-0 text-success">{{ $machines->where('status', 'active')->count() }}</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-success rounded fs-3">
                                        <i class="bx bx-check-circle text-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Maintenance</p>
                                <h4 class="mb-0 text-warning">{{ $machines->where('status', 'maintenance')->count() }}</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-warning rounded fs-3">
                                        <i class="bx bx-wrench text-warning"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate" data-aos="fade-up" data-aos-delay="400" style="border-radius: 20px;">
                    <div class="card-body bg-primary text-white">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold fs-12 mb-1">
                                    <a href="{{ route('scoring.history') }}" class="text-white text-decoration-none">
                                        History Scoring
                                    </a>
                                </p>
                                <a href="{{ route('scoring.history') }}" class="text-white text-decoration-none d-inline-flex align-items-center klik-link">
                                    <i class="ri-history-line fs-4 me-2"></i>
                                    <span class="fs-12">Klik here!</span>
                                </a>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-light rounded fs-3">
                                        <i class="bx bx-history text-white"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Machines Grid -->
        <div class="row">
            <div class="col-12">
                <div class="card" data-aos="fade-up" data-aos-delay="500">
                    <div class="card-header border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-settings-2-line align-middle me-1"></i>
                                Pilih Mesin untuk Scoring
                            </h5>
                            <div class="flex-shrink-0">
                                <div class="search-box">
                                    <input type="text" id="searchMachine" class="form-control search" placeholder="Cari mesin...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="machineGrid">
                            @forelse($machines as $machine)
                            <div class="col-xl-4 col-md-6 machine-item" data-aos="fade-up" data-aos-delay="{{ 100 * ($loop->index + 1) }}">
                                <a href="{{ route('scoring.machine.processes', $machine->id) }}" class="text-decoration-none text-dark">
                                    <div class="card machine-card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                <div class="machine-icon bg-soft-primary text-primary me-3">
                                                    <i class="ri-settings-3-fill"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1">{{ $machine->name }}</h5>
                                                    <p class="text-muted mb-2">
                                                        <small><i class="ri-barcode-line me-1"></i>{{ $machine->code }}</small>
                                                    </p>
                                                    <span class="badge {{ $machine->status_badge_class }} status-badge">
                                                        {{ $machine->status_text }}
                                                    </span>
                                                </div>
                                            </div>

                                            @if($machine->description)
                                            <div class="mt-3">
                                                <p class="text-muted mb-0 small">{{ Str::limit($machine->description, 80) }}</p>
                                            </div>
                                            @endif

                                            <div class="mt-3 pt-3 border-top">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="process-count-badge">
                                                        <i class="ri-list-check me-1"></i>
                                                        {{ $machine->machineProcesses->count() }} Process
                                                    </div>
                                                    <button class="btn btn-sm btn-primary">
                                                        Pilih <i class="ri-arrow-right-line ms-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                            </div>
                            @empty
                            <div class="col-12">
                                <div class="alert alert-warning text-center">
                                    <i class="ri-alert-line me-2"></i>
                                    Tidak ada mesin yang tersedia untuk scoring.
                                </div>
                            </div>
                            @endforelse
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
    // Search functionality
    document.getElementById('searchMachine').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const machineItems = document.querySelectorAll('.machine-item');

        machineItems.forEach(item => {
            const machineName = item.querySelector('h5').textContent.toLowerCase();
            const machineCode = item.querySelector('.text-muted small').textContent.toLowerCase();

            if (machineName.includes(searchValue) || machineCode.includes(searchValue)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // AOS Animation
    AOS.init({
        duration: 800,
        once: true
    });
</script>
@endsection