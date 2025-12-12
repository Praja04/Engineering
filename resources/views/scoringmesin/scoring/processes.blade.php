@extends('layouts.app')

@section('title', 'Pilih Parameter Proses - ' . $machine->name)

@section('styles')
<style>
    .process-card {
        transition: all 0.3s ease;
        cursor: pointer;
        border-left: 4px solid #405189;
    }

    .process-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }



    .machine-info-header {
        background: linear-gradient(135deg, #89406cff 0%, #5664d2 100%);
        color: white;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .process-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.5rem;
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
                        Parameter Proses
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('scoring.index') }}">Scoring Mesin</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $machine->name }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Machine Info -->
        <div class="row mb-4">
            <div class="col-12">

                <div class="machine-info-header" data-aos="fade-up">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="text-white mb-2">
                                <i class="ri-settings-3-fill me-2"></i>
                                {{ $machine->name }}
                            </h4>
                            <h5 class="text-white opacity-90 mb-3">{{ $machine->code }}</h5>
                            <div class="d-flex gap-3">
                                <span class="badge ">
                                    <i class="ri-folder-line me-1"></i>
                                    {{ $machineProcesses->count() }} Parameter Proses
                                </span>
                                <span class="badge ">
                                    <i class="ri-checkbox-multiple-line me-1"></i>
                                    Status: {{ $machine->status_text }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('scoring.index') }}" class="btn btn-light">
                            <i class="ri-arrow-left-line me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>


        </div>

        <!-- Process Parameters -->
        <div class="row">
            <div class="col-12">
                <div class="card" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header border-0">
                        <h5 class="card-title mb-0">
                            <i class="ri-list-check align-middle me-1"></i>
                            Pilih Parameter Proses untuk Scoring
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($machineProcesses as $machineProcess)
                            <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ 100 * ($loop->index + 1) }}">
                                <a href="{{ route('scoring.form', $machineProcess->id) }}" class="text-decoration-none text-dark">
                                    <div class="card process-card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                <div class="process-icon bg-soft-primary text-primary me-3">
                                                    <i class="ri-file-list-3-line"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-2">{{ $machineProcess->processParameter->name }}</h5>

                                                    {{-- Status Scoring Mingguan --}}
                                                    @if(isset($machineProcess->last_scoring))
                                                    @if($machineProcess->scored_this_week)
                                                    <span class="badge-status bg-success-subtle text-success">
                                                        <i class="ri-checkbox-circle-line me-1"></i>
                                                        Sudah di-scoring minggu ini
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $machineProcess->last_scoring->format('d M Y') }}
                                                        oleh {{ $machineProcess->last_user }}
                                                    </small>
                                                    @else
                                                    <span class="badge-status bg-danger-subtle text-danger">
                                                        <i class="ri-close-circle-line me-1"></i>
                                                        Belum di-scoring minggu ini
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">
                                                        Terakhir: {{ $machineProcess->last_scoring->format('d M Y') }}
                                                        oleh {{ $machineProcess->last_user }}
                                                    </small>
                                                    @endif
                                                    @else
                                                    <span class="badge-status bg-secondary-subtle text-secondary">
                                                        <i class="ri-time-line me-1"></i>
                                                        Belum pernah di-scoring
                                                    </span>
                                                    @endif

                                                    @if($machineProcess->catatan)
                                                    <p class="text-muted mb-3 mt-2 small">
                                                        <i class="ri-information-line me-1"></i>
                                                        {{ Str::limit($machineProcess->catatan, 60) }}
                                                    </p>
                                                    @endif

                                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                        <div>
                                                            <small class="text-muted">
                                                                <i class="ri-folder-line me-1"></i>
                                                                {{ $machineProcess->processParameter->sections->count() }} Section
                                                            </small><br>
                                                            <small class="text-muted">
                                                                <i class="ri-checkbox-multiple-line me-1"></i>
                                                                {{ $machineProcess->processParameter->sections->sum(fn($s) => $s->parts->count()) }} Parts
                                                            </small>
                                                        </div>
                                                        @if($machineProcess->scored_this_week)
                                                        <button class="btn btn-sm btn-outline-success">
                                                            <i class="ri-checkbox-circle-line me-1"></i> Lihat / Update
                                                        </button>
                                                        @else
                                                        <button class="btn btn-sm btn-primary">
                                                            Mulai <i class="ri-arrow-right-line ms-1"></i>
                                                        </button>
                                                        @endif
                                                    </div>
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
                                    Tidak ada parameter proses yang tersedia untuk mesin ini.
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
    AOS.init({
        duration: 800,
        once: true
    });
</script>
@endsection