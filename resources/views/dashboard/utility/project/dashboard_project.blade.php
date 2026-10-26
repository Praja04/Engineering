@extends('layouts.app')

@section('title', 'Dashboard Project Engineering Utility')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- ── Header Banner ── --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 12px;">
                        <div class="card-body p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <h4 class="text-white fw-bold mb-1">
                                    <i class="ri-dashboard-2-line text-warning me-2"></i>
                                    Dashboard Project Engineering Utility
                                </h4>
                                <p class="text-white-50 mb-0 fs-13">
                                    Monitoring visual performa dan status seluruh project utility (Boiler, Utility, WWTP,
                                    MTC)
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" id="btnRefreshDashboard"
                                    class="btn btn-outline-light btn-sm fw-semibold shadow-sm">
                                    <i class="ri-refresh-line me-1"></i> Refresh Data
                                </button>
                                <a href="{{ route('utility.project.data') }}"
                                    class="btn btn-light btn-sm fw-semibold shadow-sm">
                                    <i class="ri-table-line me-1 text-primary"></i> Data Project & Tracking
                                </a>
                                <a href="{{ route('utility.project.form') }}"
                                    class="btn btn-warning btn-sm fw-semibold shadow-sm">
                                    <i class="ri-add-line me-1"></i> Buat Project Baru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Alert Terlambat (jika ada) ── --}}
            <div id="alertTerlambatBanner"
                class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center mb-3"
                role="alert" style="display: {{ $kpis['terlambat'] > 0 ? 'flex' : 'none' }};">
                <i class="ri-error-warning-fill fs-20 me-2 text-danger"></i>
                <div class="flex-grow-1">
                    <strong>Perhatian:</strong> Terdapat <strong id="alertTerlambatCount">{{ $kpis['terlambat'] }}
                        project</strong> yang berstatus terlambat (belum selesai melewati batas deadline).
                </div>
                <a href="{{ route('utility.project.data', ['status' => 'Terlambat']) }}" class="btn btn-sm btn-danger ms-2">
                    Lihat Project Terlambat <i class="ri-arrow-right-s-line"></i>
                </a>
            </div>

            {{-- ── Filter Dashboard Panel ── --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3">
                    <form id="dashFilterForm" class="row g-2 align-items-center">
                        <div class="col-12 col-md-auto d-flex align-items-center text-dark fw-bold fs-13 me-1">
                            <i class="ri-filter-3-line text-primary me-1 fs-16"></i> Filter Dashboard:
                        </div>

                        {{-- Filter Tahun --}}
                        <div class="col-6 col-sm-4 col-md-2">
                            <select id="dashFilterYear" name="year" class="form-select form-select-sm">
                                <option value="all">Semua Tahun</option>
                                @foreach ($yearsList as $yr)
                                    <option value="{{ $yr }}"
                                        {{ isset($yearFilter) && (string) $yearFilter === (string) $yr ? 'selected' : '' }}>
                                        Tahun {{ $yr }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Area --}}
                        <div class="col-6 col-sm-4 col-md-2">
                            <select id="dashFilterArea" name="area" class="form-select form-select-sm">
                                <option value="all">Semua Area</option>
                                @foreach ($areasList as $ar)
                                    <option value="{{ $ar }}"
                                        {{ isset($areaFilter) && $areaFilter === $ar ? 'selected' : '' }}>
                                        {{ $ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Kategori --}}
                        <div class="col-6 col-sm-4 col-md-2">
                            <select id="dashFilterKategori" name="kategori" class="form-select form-select-sm">
                                <option value="all">Semua Kategori</option>
                                @foreach ($categoriesList as $cat)
                                    <option value="{{ $cat }}"
                                        {{ isset($kategoriFilter) && $kategoriFilter === $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Prioritas --}}
                        <div class="col-6 col-sm-4 col-md-2">
                            <select id="dashFilterPriority" name="priority" class="form-select form-select-sm">
                                <option value="all">Semua Prioritas</option>
                                <option value="Critical"
                                    {{ isset($priorityFilter) && $priorityFilter === 'Critical' ? 'selected' : '' }}>
                                    Critical</option>
                                <option value="High"
                                    {{ isset($priorityFilter) && $priorityFilter === 'High' ? 'selected' : '' }}>High
                                </option>
                                <option value="Medium"
                                    {{ isset($priorityFilter) && $priorityFilter === 'Medium' ? 'selected' : '' }}>Medium
                                </option>
                                <option value="Low"
                                    {{ isset($priorityFilter) && $priorityFilter === 'Low' ? 'selected' : '' }}>Low
                                </option>
                            </select>
                        </div>

                        {{-- Filter Status --}}
                        <div class="col-6 col-sm-4 col-md-2">
                            <select id="dashFilterStatus" name="status" class="form-select form-select-sm">
                                <option value="all">Semua Status</option>
                                <option value="On Progress"
                                    {{ isset($statusFilter) && $statusFilter === 'On Progress' ? 'selected' : '' }}>On
                                    Progress</option>
                                <option value="Terlambat"
                                    {{ isset($statusFilter) && $statusFilter === 'Terlambat' ? 'selected' : '' }}>
                                    Terlambat</option>
                                <option value="Selesai"
                                    {{ isset($statusFilter) && $statusFilter === 'Selesai' ? 'selected' : '' }}>Selesai
                                </option>
                            </select>
                        </div>

                        {{-- Reset Button --}}
                        <div class="col-auto">
                            <button type="button" id="btnDashResetFilter" class="btn btn-sm btn-outline-danger"
                                title="Reset Semua Filter">
                                <i class="ri-refresh-line me-1"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── 5 KPI Cards (Light Bootstrap) ── --}}
            <div class="row g-3 mb-4">
                {{-- Total Project --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card card-animate border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Total Project</p>
                                    <h3 class="mb-0 fw-bold text-dark" id="kpiTotal">{{ $kpis['total'] }}</h3>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                        <i class="ri-folders-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3 fs-12 text-muted">
                                <span class="badge bg-light text-secondary border me-1"
                                    id="kpiActiveCategories">{{ $kpis['active_categories'] }}</span>
                                <span>Kategori Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Selesai --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card card-animate border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Selesai</p>
                                    <h3 class="mb-0 fw-bold text-success" id="kpiSelesai">{{ $kpis['selesai'] }}</h3>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3 fs-12">
                                <span class="badge bg-success-subtle text-success me-1"
                                    id="kpiSelesaiPct">{{ $kpis['selesai_pct'] }}%</span>
                                <span class="text-muted">dari total project</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- On Progress --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card card-animate border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">On Progress</p>
                                    <h3 class="mb-0 fw-bold text-warning" id="kpiOnProgress">{{ $kpis['on_progress'] }}
                                    </h3>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                        <i class="ri-time-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3 fs-12">
                                <span class="badge bg-warning-subtle text-warning me-1"
                                    id="kpiOnProgressPct">{{ $kpis['on_progress_pct'] }}%</span>
                                <span class="text-muted">sedang berjalan</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Terlambat --}}
                <div class="col-xl-3 col-md-6 col-sm-6">
                    <div class="card card-animate border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Terlambat</p>
                                    <h3 class="mb-0 fw-bold text-danger" id="kpiTerlambat">{{ $kpis['terlambat'] }}</h3>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-3">
                                        <i class="ri-error-warning-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3 fs-12">
                                <span class="badge bg-danger-subtle text-danger me-1"
                                    id="kpiTerlambatPct">{{ $kpis['terlambat_pct'] }}%</span>
                                <span class="text-muted">butuh penanganan</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Rata-Rata Progress --}}
                <div class="col-xl-3 col-md-6 col-sm-12">
                    <div class="card card-animate border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Rata-Rata Progress</p>
                                    <h3 class="mb-0 fw-bold text-primary" id="kpiAvgProgress">
                                        {{ $kpis['avg_progress'] }}%
                                    </h3>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                        <i class="ri-pie-chart-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="progress progress-sm mt-3" style="height: 6px;">
                                <div class="progress-bar bg-primary" id="kpiAvgProgressBar" role="progressbar"
                                    style="width: {{ $kpis['avg_progress'] }}%;"
                                    aria-valuenow="{{ $kpis['avg_progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Charts Row 1: Summary Status & Status per Kategori ── --}}
            <div class="row g-3 mb-4">
                {{-- Donut Chart: Status Summary --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="card-title mb-0 fw-bold text-dark">
                                    <i class="ri-pie-chart-2-line text-primary me-2"></i>Status Summary
                                </h6>
                                <span class="badge bg-light text-muted border" id="summaryTotalBadge">Total:
                                    {{ $kpis['total'] }}</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div id="chartStatusSummary" style="min-height: 280px;"></div>
                            <div class="row text-center mt-2 g-1 border-top pt-2">
                                <div class="col-4">
                                    <span class="d-block text-muted fs-11">Selesai</span>
                                    <span class="fw-bold text-success fs-13"
                                        id="summaryPctSelesai">{{ $chartStatusSummary['percentages']['selesai'] }}%</span>
                                </div>
                                <div class="col-4 border-start border-end">
                                    <span class="d-block text-muted fs-11">On Progress</span>
                                    <span class="fw-bold text-warning fs-13"
                                        id="summaryPctOnProgress">{{ $chartStatusSummary['percentages']['on_progress'] }}%</span>
                                </div>
                                <div class="col-4">
                                    <span class="d-block text-muted fs-11">Terlambat</span>
                                    <span class="fw-bold text-danger fs-13"
                                        id="summaryPctTerlambat">{{ $chartStatusSummary['percentages']['terlambat'] }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Horizontal Stacked Bar: Status by Kategori --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="card-title mb-0 fw-bold text-dark">
                                    <i class="ri-bar-chart-horizontal-line text-primary me-2"></i>Status Project per
                                    Kategori
                                </h6>
                                <small class="text-muted">Akumulasi per kategori pekerjaan</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chartStatusByKategori" style="min-height: 330px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Charts Row 2: Status per Area Plant & Project Perlu Perhatian ── --}}
            <div class="row g-3 mb-4">
                {{-- Stacked Bar: Status per Area (Boiler, Utility, WWTP, MTC) --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="card-title mb-0 fw-bold text-dark">
                                    <i class="ri-community-line text-primary me-2"></i>Distribusi Status per Area
                                </h6>
                                <small class="text-muted">Boiler, Utility, WWTP, MTC</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chartStatusByArea" style="min-height: 280px;"></div>
                        </div>
                    </div>
                </div>

                {{-- Top 5 Attention Projects --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="card-title mb-0 fw-bold text-dark">
                                    <i class="ri-alarm-warning-line text-warning me-2"></i>Project Prioritas (Perlu
                                    Perhatian)
                                </h6>
                                <a href="{{ route('utility.project.data') }}" class="btn btn-sm btn-soft-primary">
                                    Lihat Semua <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 312px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0 text-nowrap" style="min-width: 500px;">
                                    <thead class="table-light fs-12 text-muted sticky-top" style="position: sticky; top: 0; z-index: 2; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        <tr>
                                            <th style="min-width: 170px;">Project</th>
                                            <th style="width: 90px;" class="text-center">Prioritas</th>
                                            <th style="width: 80px;" class="text-center">Area</th>
                                            <th style="min-width: 120px;">Progress</th>
                                            <th style="width: 100px;">Status</th>
                                            <th class="text-end" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attentionProjectsBody">
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <div class="spinner-border spinner-border-sm text-primary me-2"
                                                    role="status"></div>
                                                Memuat data project prioritas...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Modal Quick Update Progress (Light Bootstrap) ── --}}
    <div class="modal fade" id="modalQuickProgress" tabindex="-1" aria-labelledby="modalQuickProgressLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="modalQuickProgressLabel">
                        <i class="ri-edit-2-line text-primary me-2"></i>Update Progress Project
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formQuickProgress">
                    @csrf
                    <input type="hidden" id="quick_project_id" name="project_id">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-muted fs-12 mb-1">Nama Project</label>
                            <div id="quick_project_nama" class="fw-semibold text-dark fs-14 p-2 bg-light rounded border">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold text-dark mb-0">Progress Persentase (%)</label>
                                <span id="quick_slider_val" class="badge bg-primary fs-13">0%</span>
                            </div>
                            <input type="range" class="form-range" id="quick_progress_range" min="0"
                                max="100" step="1" value="0">
                            <div class="input-group input-group-sm mt-2">
                                <span class="input-group-text bg-light">Input Angka</span>
                                <input type="number" class="form-control" id="quick_progress_input" name="progress"
                                    min="0" max="100" value="0" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Tanggal Update</label>
                            <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Catatan Progress / Kendala <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" name="catatan" rows="3"
                                placeholder="Tuliskan perkembangan pekerjaan, kendala teknis, atau catatan lainnya..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitQuickProgress">
                            <i class="ri-save-line me-1"></i> Simpan Update Progress
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // ── Setup AJAX CSRF Token ──
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            let chartSummary = null;
            let chartKategori = null;
            let chartArea = null;

            function escapeHtml(str) {
                if (!str) return '';
                return $('<div>').text(str).html();
            }

            // ── 1. Chart Status Summary (Donut Chart) ──
            const summaryData = @json($chartStatusSummary);
            const summaryOptions = {
                series: (summaryData && summaryData.series) ? summaryData.series : [0, 0, 0],
                labels: (summaryData && summaryData.labels) ? summaryData.labels : ['Selesai', 'On Progress',
                    'Terlambat'
                ],
                chart: {
                    type: 'donut',
                    height: 280,
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#0ab39c', '#f7b84b', '#f06548'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '13px',
                                    fontWeight: 600,
                                    color: '#495057'
                                },
                                value: {
                                    show: true,
                                    fontSize: '20px',
                                    fontWeight: 700,
                                    color: '#212529',
                                    formatter: function(val) {
                                        return val + ' prj';
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total Project',
                                    fontSize: '12px',
                                    fontWeight: 600,
                                    color: '#74788d',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                stroke: {
                    width: 2,
                    colors: ['#fff']
                },
                legend: {
                    position: 'bottom',
                    fontSize: '12px',
                    markers: {
                        radius: 12
                    }
                },
                dataLabels: {
                    enabled: true,
                    dropShadow: {
                        enabled: false
                    },
                    formatter: function(val) {
                        return Math.round(val) + "%";
                    }
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function(val) {
                            return val + " Project";
                        }
                    }
                }
            };
            if (document.querySelector("#chartStatusSummary")) {
                chartSummary = new ApexCharts(document.querySelector("#chartStatusSummary"), summaryOptions);
                chartSummary.render();
            }

            // ── 2. Chart Status by Kategori (Horizontal Stacked Bar) ──
            const kategoriData = @json($chartStatusByKategori);
            const kategoriOptions = {
                series: (kategoriData && kategoriData.series) ? kategoriData.series : [],
                chart: {
                    type: 'bar',
                    height: 330,
                    stacked: true,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#0ab39c', '#f7b84b', '#f06548'],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '60%',
                        borderRadius: 2
                    }
                },
                stroke: {
                    width: 1,
                    colors: ['#fff']
                },
                xaxis: {
                    categories: (kategoriData && kategoriData.categories) ? kategoriData.categories : [],
                    labels: {
                        style: {
                            colors: '#74788d',
                            fontSize: '11px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#495057',
                            fontSize: '12px',
                            fontWeight: 500
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 3
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px'
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function(val) {
                            return val + " Project";
                        }
                    }
                }
            };
            if (document.querySelector("#chartStatusByKategori")) {
                chartKategori = new ApexCharts(document.querySelector("#chartStatusByKategori"), kategoriOptions);
                chartKategori.render();
            }

            // ── 3. Chart Status by Area (Vertical Stacked Bar) ──
            const areaData = @json($chartStatusByArea);
            const areaOptions = {
                series: (areaData && areaData.series) ? areaData.series : [],
                chart: {
                    type: 'bar',
                    height: 280,
                    stacked: true,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#0ab39c', '#f7b84b', '#f06548'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '45%',
                        borderRadius: 4
                    }
                },
                stroke: {
                    width: 1,
                    colors: ['#fff']
                },
                xaxis: {
                    categories: (areaData && areaData.categories) ? areaData.categories : [],
                    labels: {
                        style: {
                            colors: '#495057',
                            fontSize: '12px',
                            fontWeight: 600
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Project',
                        style: {
                            color: '#74788d',
                            fontSize: '11px'
                        }
                    },
                    labels: {
                        style: {
                            colors: '#74788d',
                            fontSize: '11px'
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 3
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px'
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function(val) {
                            return val + " Project";
                        }
                    }
                }
            };
            if (document.querySelector("#chartStatusByArea")) {
                chartArea = new ApexCharts(document.querySelector("#chartStatusByArea"), areaOptions);
                chartArea.render();
            }

            function getPriorityBadge(priority) {
                if (priority === 'Critical') {
                    return '<span class="badge bg-danger text-white px-2 py-1 fs-11"><i class="ri-alarm-warning-fill me-1"></i>Critical</span>';
                } else if (priority === 'High') {
                    return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-11 fw-semibold"><i class="ri-arrow-up-circle-fill me-1"></i>High</span>';
                } else if (priority === 'Low') {
                    return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 fs-11 fw-semibold"><i class="ri-arrow-down-circle-line me-1"></i>Low</span>';
                }
                return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-11 fw-semibold"><i class="ri-equal-circle-fill me-1"></i>Medium</span>';
            }

            // ── 4. Render Attention Projects Table Body ──
            function renderAttentionProjects(projects) {
                const $tbody = $('#attentionProjectsBody');
                $tbody.empty();

                if (!projects || projects.length === 0) {
                    $tbody.html(`
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="ri-checkbox-circle-line fs-24 text-success d-block mb-1"></i>
                        Semua project telah selesai atau tidak ada yang sesuai kriteria filter!
                    </td>
                </tr>
            `);
                    return;
                }

                projects.forEach(function(prj) {
                    let areaBadge = '';
                    if (prj.area === 'Boiler') {
                        areaBadge =
                            '<span class="badge bg-danger-subtle text-danger px-2 py-1 fs-12">Boiler</span>';
                    } else if (prj.area === 'Utility') {
                        areaBadge =
                            '<span class="badge bg-primary-subtle text-primary px-2 py-1 fs-12">Utility</span>';
                    } else if (prj.area === 'WWTP') {
                        areaBadge =
                            '<span class="badge bg-info-subtle text-info px-2 py-1 fs-12">WWTP</span>';
                    } else {
                        areaBadge =
                            `<span class="badge bg-secondary-subtle text-secondary px-2 py-1 fs-12">${escapeHtml(prj.area || 'MTC')}</span>`;
                    }

                    const isTerlambat = prj.status === 'Terlambat';
                    const barColor = isTerlambat ? 'bg-danger' : 'bg-primary';
                    const statusBadge = isTerlambat ?
                        '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-12"><i class="ri-alert-line me-1"></i>Terlambat</span>' :
                        '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-12"><i class="ri-time-line me-1"></i>On Progress</span>';

                    const row = `
                <tr>
                    <td>
                        <div class="fw-semibold text-dark fs-13 text-truncate" style="max-width: 200px;" title="${escapeHtml(prj.nama_project)}">${escapeHtml(prj.nama_project)}</div>
                        <div class="d-flex align-items-center gap-1 mt-1">
                            <span class="badge bg-light text-muted border fs-11">${escapeHtml(prj.nomor_project)}</span>
                            <span class="text-muted fs-11">&bull; Target: ${escapeHtml(prj.finish_date || '-')}</span>
                        </div>
                    </td>
                    <td class="text-center">${getPriorityBadge(prj.priority)}</td>
                    <td class="text-center">${areaBadge}</td>
                    <td style="min-width: 120px;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 6px;">
                                <div class="progress-bar ${barColor}" style="width: ${prj.progress}%;"></div>
                            </div>
                            <span class="fs-12 fw-semibold text-muted">${prj.progress}%</span>
                        </div>
                    </td>
                    <td>${statusBadge}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-update-progress"
                            data-id="${prj.id}"
                            data-nama="${escapeHtml(prj.nama_project)}"
                            data-progress="${prj.progress}">
                            <i class="ri-edit-line"></i> Update
                        </button>
                    </td>
                </tr>
            `;
                    $tbody.append(row);
                });
            }

            // Initial render of Attention Projects
            renderAttentionProjects(@json($attentionProjects));

            // ── 5. Load Dashboard Data via jQuery AJAX ──
            window.loadDashboardData = function() {
                const $btnRefresh = $('#btnRefreshDashboard');
                const origRefreshText = $btnRefresh.html();
                $btnRefresh.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Loading...');

                const filterParams = {
                    format: 'json',
                    year: $('#dashFilterYear').val(),
                    area: $('#dashFilterArea').val(),
                    kategori: $('#dashFilterKategori').val(),
                    priority: $('#dashFilterPriority').val(),
                    status: $('#dashFilterStatus').val()
                };

                $.ajax({
                    url: '{{ route('utility.project.dashboard') }}',
                    type: 'GET',
                    data: filterParams,
                    dataType: 'json',
                    success: function(res) {
                        if (!res.success) return;

                        // 1. Alert Banner
                        if (res.kpis && res.kpis.terlambat > 0) {
                            $('#alertTerlambatCount').text(res.kpis.terlambat + ' project');
                            $('#alertTerlambatBanner').fadeIn(200);
                        } else {
                            $('#alertTerlambatBanner').hide();
                        }

                        // 2. KPI Cards
                        if (res.kpis) {
                            $('#kpiTotal').text(res.kpis.total);
                            $('#kpiActiveCategories').text(res.kpis.active_categories);
                            $('#kpiSelesai').text(res.kpis.selesai);
                            $('#kpiSelesaiPct').text(res.kpis.selesai_pct + '%');
                            $('#kpiOnProgress').text(res.kpis.on_progress);
                            $('#kpiOnProgressPct').text(res.kpis.on_progress_pct + '%');
                            $('#kpiTerlambat').text(res.kpis.terlambat);
                            $('#kpiTerlambatPct').text(res.kpis.terlambat_pct + '%');
                            $('#kpiAvgProgress').text(res.kpis.avg_progress + '%');
                            $('#kpiAvgProgressBar').css('width', res.kpis.avg_progress + '%').attr(
                                'aria-valuenow', res.kpis.avg_progress);
                            $('#summaryTotalBadge').text('Total: ' + res.kpis.total);
                        }

                        // 3. Status Summary Percentages & Chart
                        if (res.chartStatusSummary) {
                            if (res.chartStatusSummary.percentages) {
                                $('#summaryPctSelesai').text((res.chartStatusSummary.percentages
                                    .selesai || 0) + '%');
                                $('#summaryPctOnProgress').text((res.chartStatusSummary.percentages
                                    .on_progress || 0) + '%');
                                $('#summaryPctTerlambat').text((res.chartStatusSummary.percentages
                                    .terlambat || 0) + '%');
                            }
                            if (chartSummary && res.chartStatusSummary.series) {
                                chartSummary.updateSeries(res.chartStatusSummary.series);
                            }
                        }

                        // 4. Chart Status by Kategori
                        if (res.chartStatusByKategori && chartKategori) {
                            chartKategori.updateOptions({
                                series: res.chartStatusByKategori.series,
                                xaxis: {
                                    categories: res.chartStatusByKategori.categories
                                }
                            });
                        }

                        // 5. Chart Status by Area
                        if (res.chartStatusByArea && chartArea) {
                            chartArea.updateOptions({
                                series: res.chartStatusByArea.series,
                                xaxis: {
                                    categories: res.chartStatusByArea.categories
                                }
                            });
                        }

                        // 6. Attention Projects Table
                        renderAttentionProjects(res.attentionProjects || []);
                    },
                    error: function(xhr) {
                        console.error('Gagal memuat data dashboard:', xhr);
                    },
                    complete: function() {
                        $btnRefresh.prop('disabled', false).html(origRefreshText);
                    }
                });
            };

            // Event listener Filter Dashboard
            $('#dashFilterYear, #dashFilterArea, #dashFilterKategori, #dashFilterPriority, #dashFilterStatus').on(
                'change',
                function() {
                    window.loadDashboardData();
                });

            $('#btnDashResetFilter').on('click', function() {
                $('#dashFilterYear').val('all');
                $('#dashFilterArea').val('all');
                $('#dashFilterKategori').val('all');
                $('#dashFilterPriority').val('all');
                $('#dashFilterStatus').val('all');
                window.loadDashboardData();
            });

            // Refresh Button Click
            $('#btnRefreshDashboard').on('click', function() {
                window.loadDashboardData();
            });

            // ── 6. Quick Progress Update Modal Interaction ──
            $('#quick_progress_range').on('input', function() {
                const val = $(this).val();
                $('#quick_progress_input').val(val);
                $('#quick_slider_val').text(val + '%');
            });

            $('#quick_progress_input').on('input', function() {
                let val = parseInt($(this).val()) || 0;
                if (val < 0) val = 0;
                if (val > 100) val = 100;
                $('#quick_progress_range').val(val);
                $('#quick_slider_val').text(val + '%');
            });

            $(document).on('click', '.btn-update-progress', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const prog = parseInt($(this).data('progress')) || 0;

                $('#quick_project_id').val(id);
                $('#quick_project_nama').text(nama);
                $('#quick_progress_range').val(prog);
                $('#quick_progress_input').val(prog);
                $('#quick_slider_val').text(prog + '%');

                $('#modalQuickProgress').modal('show');
            });

            $('#formQuickProgress').on('submit', function(e) {
                e.preventDefault();
                const id = $('#quick_project_id').val();
                const $btn = $('#btnSubmitQuickProgress');
                const origText = $btn.html();

                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: `{{ url('utility/project') }}/${id}/progress`,
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            $('#modalQuickProgress').modal('hide');
                            $('#formQuickProgress')[0].reset();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            // Refresh dashboard asynchronously via AJAX without page reload!
                            window.loadDashboardData();
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Gagal memproses request.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(origText);
                    }
                });
            });
        });
    </script>
@endsection
