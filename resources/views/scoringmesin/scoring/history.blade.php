@extends('layouts.app')

@section('title', 'History Scoring')

@section('styles')
<style>
    .card-hover:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .status-badge {
        font-size: 0.875rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 500;
    }

    .percentage-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
    }

    .percentage-ok {
        background: linear-gradient(135deg, #198754 0%, #20c997 100%);
        color: white;
    }

    .percentage-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%);
        color: white;
    }

    .percentage-danger {
        background: linear-gradient(135deg, #dc3545 0%, #e35d6a 100%);
        color: white;
    }

    .history-card {
        border-left: 4px solid #405189;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .history-card:hover {
        border-left-color: #0d6efd;
    }

    .machine-card {
        border-left: 4px solid #405189;
        cursor: pointer;
        transition: all 0.3s ease;
       
    }

    .machine-card:hover {
        border-left-color: #0d6efd;
       
    }

    .process-list {
        display: none;
        padding-left: 20px;
        margin-top: 10px;
    }

    .process-list.show {
        display: block;
    }

    .machine-toggle {
        transition: transform 0.3s ease;
    }

    .machine-toggle.rotated {
        transform: rotate(90deg);
    }

    .process-item {
        border-left: 2px solid #dee2e6;
        padding-left: 15px;
        margin-bottom: 10px;
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
                        <i class="ri-history-line align-middle me-2"></i>
                        History Scoring
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('scoring.index') }}">Scoring Mesin</a>
                            </li>
                            <li class="breadcrumb-item active">History</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0" data-aos="fade-up">
                    <div class="card-body">
                        <div class="row align-items-end g-3">
                            <!-- Filter Bulan -->
                            <div class="col-md-6 col-sm-6">
                                <label for="filterMonth" class="form-label fw-semibold">Pilih Bulan</label>
                                <input type="month" id="filterMonth" class="form-control" value="{{ $month }}" onchange="changeMonth()">
                            </div>

                            <!-- Search -->
                            <div class="col-md-6 col-sm-6">
                                <label for="searchScoring" class="form-label fw-semibold">Pencarian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ri-search-line text-muted"></i>
                                    </span>
                                    <input type="text" id="searchScoring" class="form-control" placeholder="Cari mesin...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- History List -->
        <div class="row">
            <div class="col-12">
                <div class="card" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-list-check align-middle me-1"></i>
                                Riwayat Scoring
                            </h5>
                            <div class="flex-shrink-0">
                                <a href="{{ route('scoring.index') }}" class="btn btn-success">
                                    <i class="ri-add-line me-1"></i> Scoring Baru
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @forelse(array_reverse($weeks) as $week)
                        <div class="mb-4 week-section">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="ri-calendar-line me-2"></i>
                                {{ $week['label'] }} ({{ $week['start'] }} - {{ $week['end'] }})
                            </h5>

                            @foreach($week['machines'] as $machineData)
                            <div class="machine-group mb-3">
                                <!-- Machine Header Card -->
                                <div class="card machine-card mb-2" data-bs-toggle="collapse" data-bs-target="#machine-{{ $machineData['machine_id'] }}-week-{{ $loop->parent->index }}" aria-expanded="false" aria-controls="machine-{{ $machineData['machine_id'] }}-week-{{ $loop->parent->index }}">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-1 text-center">
                                                <i class="ri-arrow-right-s-line fs-4 machine-toggle" id="toggle-icon-{{ $machineData['machine_id'] }}-week-{{ $loop->parent->index }}"></i>
                                            </div>
                                            <div class="col-md-1 text-center">
                                                <div class="percentage-circle {{ $machineData['weekly_percentage'] >= 80 ? 'percentage-ok' : ($machineData['weekly_percentage'] >= 50 ? 'percentage-warning' : 'percentage-danger') }}">
                                                    {{ $machineData['weekly_percentage'] }}%
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="mb-1">{{ $machineData['machine_name'] }}</h6>
                                                <p class="text-muted mb-0 small">
                                                    <span class="badge bg-secondary">{{ $machineData['machine_code'] }}</span>
                                                    <span class="ms-2">{{ $machineData['process_count'] }} proses di-scoring</span>
                                                </p>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <span class="badge bg-info text-white">Skor Mingguan</span>
                                                <small class="text-muted d-block mt-1">Klik untuk lihat detail proses</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Process List (Collapse Bootstrap) -->
                                <div class="collapse process-list" id="machine-{{ $machineData['machine_id'] }}-week-{{ $loop->parent->index }}">
                                    @foreach($machineData['scorings'] as $scoring)
                                    <a href="{{ route('scoring.show', $scoring->id) }}" class="text-decoration-none text-dark">
                                        <div class="card history-card mb-2 process-item">
                                            <div class="card-body py-3">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <h6 class="mb-1">
                                                            <i class="ri-settings-3-line me-1 text-primary"></i>
                                                            {{ $scoring->machineProcess->processParameter->name }}
                                                        </h6>
                                                        <p class="text-muted mb-0 small">
                                                            <i class="ri-user-line me-1"></i>
                                                            {{ $scoring->user->username ?? '-' }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <p class="text-muted mb-0 small">
                                                            <i class="ri-calendar-line me-1"></i>
                                                            {{ $scoring->scoring_date->format('d M Y, H:i') }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-3 text-end">
                                                        <button class="btn btn-sm btn-primary">
                                                            Detail <i class="ri-arrow-right-line ms-1"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                                </div>
                            </div>

                            @endforeach
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <div class="avatar-lg mx-auto mb-4">
                                <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-2">
                                    <i class="ri-file-list-line"></i>
                                </div>
                            </div>
                            <h5 class="mb-2">Belum Ada History Scoring</h5>
                            <p class="text-muted mb-4">Mulai scoring mesin untuk melihat riwayat di sini.</p>
                            <a href="{{ route('scoring.index') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i> Mulai Scoring
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.machine-card').forEach(card => {
            const targetId = card.getAttribute('data-bs-target');
            const icon = card.querySelector('.machine-toggle');
            const collapse = document.querySelector(targetId);

            collapse.addEventListener('show.bs.collapse', () => {
                icon.classList.remove('ri-arrow-right-s-line');
                icon.classList.add('ri-arrow-down-s-line');
            });

            collapse.addEventListener('hide.bs.collapse', () => {
                icon.classList.remove('ri-arrow-down-s-line');
                icon.classList.add('ri-arrow-right-s-line');
            });
        });
    });

    function changeMonth() {
        const month = document.getElementById('filterMonth').value;
        window.location.href = '{{ route("scoring.history") }}?month=' + month;
    }

    // Search functionality
    document.getElementById('searchScoring').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const weekSections = document.querySelectorAll('.week-section');

        weekSections.forEach(section => {
            const text = section.textContent.toLowerCase();
            section.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });

    AOS.init({
        duration: 800,
        once: true
    });
</script>
@endsection