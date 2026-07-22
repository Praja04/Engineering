@extends('layouts.app')

@section('title', 'EPR Command Center — Dashboard')

@section('styles')
<style>
    .card-header-custom {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        color: white;
        padding: 1.25rem;
        border-radius: 12px 12px 0 0;
    }

    .badge-status {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        letter-spacing: .4px;
        text-transform: uppercase;
    }
    .badge-open     { background: rgba(59,130,246,.15);  color: #3b82f6; }
    .badge-assigned { background: rgba(139,92,246,.15);  color: #8b5cf6; }
    .badge-progress { background: rgba(245,158,11,.15); color: #f59e0b; }
    .badge-done     { background: rgba(34,197,94,.15);  color: #22c55e; }
    .badge-approved { background: rgba(16,185,129,.15); color: #10b981; }
    .badge-rejected { background: rgba(239,68,68,.15);  color: #ef4444; }
    .badge-onhold   { background: rgba(239,68,68,.15);  color: #ef4444; }

    .badge-prio { font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: .5px; }
    .prio-critical { background: #ef4444; color: #fff; }
    .prio-high     { background: #f59e0b; color: #fff; }
    .prio-medium   { background: #3b82f6; color: #fff; }
    .prio-low      { background: #94a3b8; color: #fff; }

    .stat-card {
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,.06);
        transition: transform .2s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 20px;
    }

    .progress-bar-wrap {
        background: var(--vz-border-color);
        height: 6px;
        border-radius: 3px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 3px;
    }

    .recent-item {
        border-left: 3px solid var(--vz-border-color);
        background: var(--vz-card-bg);
        border: 1px solid var(--vz-border-color);
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 10px;
        transition: all .15s;
    }
    .recent-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
        border-left-color: #3b82f6;
    }
</style>
@endsection

@section('content')
<div class="page-content">
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="ri-dashboard-2-line me-2 text-primary"></i>
                EPR Command Center
            </h4>
            <p class="text-muted mb-0 fs-13">Dashboard statistik, pemantauan beban kerja manpower, dan analisis data Predictive Maintenance</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('epr.wo.index') }}" class="btn btn-soft-warning btn-sm d-flex align-items-center gap-1">
                <i class="ri-task-line"></i> Manage WO
            </a>
            <a href="{{ route('epr.pm.data') }}" class="btn btn-soft-primary btn-sm d-flex align-items-center gap-1">
                <i class="ri-database-2-line"></i> Lihat Data PM
            </a>
        </div>
    </div>

    {{-- Stats Row (WO & PM) --}}
    <div class="row g-3 mb-4">
        {{-- Total WO --}}
        <div class="col-md-3">
            <div class="card stat-card mb-0 h-100 border-start border-primary border-3">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="ri-task-line"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-20 lh-1 mb-1">{{ number_format($woStats['total']) }}</div>
                        <div class="text-muted fs-12">Total Work Order</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Active WO (Assigned + Progress) --}}
        <div class="col-md-3">
            <div class="card stat-card mb-0 h-100 border-start border-warning border-3">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="ri-loader-4-line"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-20 lh-1 mb-1">{{ number_format($woStats['assigned'] + $woStats['progress']) }}</div>
                        <div class="text-muted fs-12">WO Sedang Berjalan</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Approved WO --}}
        <div class="col-md-3">
            <div class="card stat-card mb-0 h-100 border-start border-success border-3">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-20 lh-1 mb-1">{{ number_format($woStats['approved']) }}</div>
                        <div class="text-muted fs-12">WO Selesai (Approved)</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Total PM Reports --}}
        <div class="col-md-3">
            <div class="card stat-card mb-0 h-100 border-start border-info border-3">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-20 lh-1 mb-1">{{ number_format($pmStats['total']) }}</div>
                        <div class="text-muted fs-12">Total Laporan PM</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Dashboard Layout --}}
    <div class="row g-4 mb-4">
        {{-- Left: Charts --}}
        <div class="col-lg-8">
            <div class="row g-3 mb-4">
                {{-- Chart 1: Progress WO --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold"><i class="ri-pie-chart-line text-primary me-2"></i>Status Progress WO</h6>
                        </div>
                        <div class="card-body d-flex flex-column align-items-center justify-content-center py-4" style="min-height: 250px;">
                            <div style="width: 160px; height: 160px;">
                                <canvas id="chartWoProgress"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Chart 2: WO per Area --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold"><i class="ri-bar-chart-line text-success me-2"></i>WO per Area / Unit</h6>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center py-4" style="min-height: 250px;">
                            <canvas id="chartWoArea" style="max-height: 180px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Work Orders --}}
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="ri-task-line text-warning me-2"></i>Work Order Terbaru</h6>
                    <a href="{{ route('epr.wo.index') }}" class="btn btn-ghost btn-xs text-primary">Lihat Semua WO →</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No WO</th>
                                    <th>Judul Pekerjaan</th>
                                    <th>Area</th>
                                    <th>Prioritas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentWos as $wo)
                                    <tr>
                                        <td><strong style="font-family:monospace;">{{ $wo->wo_number }}</strong></td>
                                        <td>{{ $wo->title }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $wo->area }}</span></td>
                                        <td>
                                            @php
                                                $prioLabels = ['critical'=>'Critical','high'=>'High','medium'=>'Medium','low'=>'Low'];
                                            @endphp
                                            <span class="badge-prio prio-{{ $wo->priority }}">{{ $prioLabels[$wo->priority] ?? $wo->priority }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusBadges = [
                                                    'open' => 'badge-open',
                                                    'assigned' => 'badge-assigned',
                                                    'progress' => 'badge-progress',
                                                    'done' => 'badge-done',
                                                    'approved' => 'badge-approved',
                                                    'rejected' => 'badge-rejected',
                                                ];
                                            @endphp
                                            <span class="badge-status {{ $statusBadges[$wo->status] ?? 'badge-open' }}">{{ $wo->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada Work Order.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Manpower & Workload --}}
        <div class="col-lg-4">
            {{-- Manpower Workload --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold"><i class="ri-user-star-line text-purple me-2"></i>Beban Kerja Manpower (Operator)</h6>
                </div>
                <div class="card-body py-3">
                    @forelse($manpowerWorkload as $op)
                        @php
                            // Workload score
                            $totalActive = $op['active_wo'];
                            $workloadClass = $totalActive > 3 ? 'bg-danger' : ($totalActive > 0 ? 'bg-warning' : 'bg-success');
                            $workloadStatus = $totalActive > 3 ? 'Overload' : ($totalActive > 0 ? 'Active' : 'Idle');
                            $workloadPct = min(100, max(5, $totalActive * 25)); // 4 active WOs = 100%
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <strong class="fs-13">{{ $op['username'] }}</strong>
                                    <br><small class="text-muted">{{ $op['bagian'] }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-opacity-10 text-dark fs-10" style="background:#f1f5f9;">{{ $totalActive }} WO Aktif</span>
                                    <br><span class="badge {{ $totalActive > 3 ? 'bg-danger-subtle text-danger' : ($totalActive > 0 ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success') }} fs-9">{{ $workloadStatus }}</span>
                                </div>
                            </div>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar-fill {{ $workloadClass }}" style="width: {{ $workloadPct }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small text-center my-3">Belum ada operator terdaftar.</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent Reports --}}
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="ri-history-line text-info me-2"></i>Laporan PM Terbaru</h6>
                    <a href="{{ route('epr.pm.data') }}" class="btn btn-ghost btn-xs text-primary">Lihat Semua →</a>
                </div>
                <div class="card-body">
                    @forelse($recentReports as $rep)
                        <div class="recent-item">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <strong class="fs-12">{{ $rep->tech_name }}</strong>
                                <span class="badge bg-light text-dark fs-9">{{ $rep->area }}</span>
                            </div>
                            <p class="text-muted small mb-2 text-truncate" title="{{ $rep->adhoc_title ?: $rep->work_description }}">
                                {{ $rep->is_adhoc ? '⚡ ' . ($rep->adhoc_title ?: $rep->work_description) : $rep->work_description }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="text-muted"><i class="ri-calendar-line me-1"></i>{{ $rep->date }}</span>
                                @php
                                    $pmStatusBadges = [
                                        'open' => 'badge-open',
                                        'progress' => 'badge-progress',
                                        'done' => 'badge-done',
                                        'onhold' => 'badge-onhold',
                                    ];
                                @endphp
                                <span class="badge-status {{ $pmStatusBadges[$rep->status] ?? 'badge-open' }} fs-9" style="padding: 2px 8px;">{{ $rep->status }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small text-center my-3">Belum ada laporan masuk.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@section('scripts')
{{-- Load Chart.js from CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(function() {
    // 1. WO Progress Pie Chart
    const ctxProgress = document.getElementById('chartWoProgress').getContext('2d');
    new Chart(ctxProgress, {
        type: 'doughnut',
        data: {
            labels: ['Open', 'Assigned', 'Progress', 'Done', 'Approved', 'Rejected'],
            datasets: [{
                data: [
                    {{ $woStats['open'] }},
                    {{ $woStats['assigned'] }},
                    {{ $woStats['progress'] }},
                    {{ $woStats['done'] }},
                    {{ $woStats['approved'] }},
                    {{ $woStats['rejected'] }}
                ],
                backgroundColor: [
                    '#3b82f6', // Open - Blue
                    '#8b5cf6', // Assigned - Purple
                    '#f59e0b', // Progress - Amber
                    '#22c55e', // Done - Green
                    '#10b981', // Approved - Teal
                    '#ef4444'  // Rejected - Red
                ],
                borderWidth: 2,
                borderColor: 'var(--vz-card-bg)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        font: { size: 10 }
                    }
                }
            },
            cutout: '60%'
        }
    });

    // 2. WO per Area Bar Chart
    const ctxArea = document.getElementById('chartWoArea').getContext('2d');
    new Chart(ctxArea, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($woPerArea)) !!},
            datasets: [{
                label: 'Jumlah WO',
                data: {!! json_encode(array_values($woPerArea)) !!},
                backgroundColor: '#3b82f6',
                borderRadius: 4,
                maxBarThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 9 }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    ticks: { font: { size: 9 } },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endsection
