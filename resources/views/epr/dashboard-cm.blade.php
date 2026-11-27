@extends('layouts.app')

@section('title', 'Dashboard KPI Engineering Produksi — CM')

@section('styles')
<style>
    .kpi-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: white;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .kpi-card {
        border-radius: 12px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .kpi-title {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #64748b;
    }
    .kpi-value {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .kpi-sub {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 500;
    }
    .card-module-header {
        background: #0f172a;
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 9px 14px;
        border-radius: 10px 10px 0 0;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-module-header.header-red {
        background: #dc2626;
    }
    .card-module-header.header-blue {
        background: #1e40af;
    }
    .card-module-header.header-emerald {
        background: #065f46;
    }
    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    .dot-green { background-color: #22c55e; box-shadow: 0 0 6px rgba(34, 197, 94, 0.6); }
    .dot-yellow { background-color: #eab308; box-shadow: 0 0 6px rgba(234, 179, 8, 0.6); }
    .dot-red { background-color: #ef4444; box-shadow: 0 0 6px rgba(239, 68, 68, 0.6); }

    .table-compact th, .table-compact td {
        padding: 6px 10px;
        font-size: 12px;
    }
    .badge-pouch { background-color: #e0f2fe; color: #0369a1; }
    .badge-sachet { background-color: #ffedd5; color: #c2410c; }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Top Banner Header --}}
        <div class="kpi-header mb-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-warning text-dark fw-bold px-2 py-1">LIVE CM DATA</span>
                    <h4 class="text-white mb-0 fw-bold">
                        <i class="ri-dashboard-2-line text-warning me-2"></i>
                        DASHBOARD KPI ENGINEERING PRODUKSI — CORRECTIVE MAINTENANCE
                    </h4>
                </div>
                <p class="text-slate-300 mb-0 fs-13">
                    Monitoring Realisasi Availability, Breakdown Rate, MTTR, MTBF, dan Distribusi Downtime 28 Mesin (Pouch & Sachet)
                </p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- Quick Link to Corrective Maintenance History --}}
                <a href="{{ route('epr.cm.data') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
                    <i class="ri-file-list-3-line"></i> Data Log CM
                </a>
                <a href="{{ route('epr.cm.extra-data') }}" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1">
                    <i class="ri-settings-3-line"></i> Input Biaya & KPI
                </a>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-body p-3">
                <form id="formKpiFilter" method="GET" action="{{ route('epr.dashboard.cm') }}">
                    <div class="row g-2 align-items-center">
                        {{-- Mode Filter Selector --}}
                        <div class="col-xl-2 col-md-3 col-sm-6">
                            <label class="form-label fs-11 fw-bold text-uppercase text-muted mb-1">
                                <i class="ri-filter-3-line text-primary me-1"></i> Tipe Periode
                            </label>
                            <select name="filter_type" id="filter_type" class="form-select form-select-sm fw-semibold" onchange="toggleFilterInputs()">
                                <option value="month" {{ $filterType === 'month' ? 'selected' : '' }}>Bulanan (Month)</option>
                                <option value="week" {{ $filterType === 'week' ? 'selected' : '' }}>Mingguan (Week)</option>
                                <option value="date" {{ $filterType === 'date' ? 'selected' : '' }}>Harian (1 Tanggal)</option>
                                <option value="custom" {{ $filterType === 'custom' ? 'selected' : '' }}>Custom (Rentang Tanggal)</option>
                            </select>
                        </div>

                        {{-- Month Selector (Shown on 'month' & 'week') --}}
                        <div class="col-xl-2 col-md-3 col-sm-6 filter-input-group" id="group_month">
                            <label class="form-label fs-11 fw-bold text-uppercase text-muted mb-1">
                                <i class="ri-calendar-line text-info me-1"></i> Pilih Bulan
                            </label>
                            <select name="month" id="filter_month" class="form-select form-select-sm" onchange="updateWeeksDropdown()">
                                @foreach($availableMonths as $m)
                                    <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                                        {{ date('F Y', strtotime($m . '-01')) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Week Selector (Shown only on 'week') --}}
                        <div class="col-xl-2 col-md-3 col-sm-6 filter-input-group" id="group_week" style="display: none;">
                            <label class="form-label fs-11 fw-bold text-uppercase text-muted mb-1">
                                <i class="ri-calendar-todo-line text-warning me-1"></i> Pilih Minggu (Senin-Minggu)
                            </label>
                            <select name="week" id="filter_week" class="form-select form-select-sm">
                                @foreach($weeksInMonth as $wk)
                                    <option value="{{ $wk['week_num'] }}" {{ $week == $wk['week_num'] ? 'selected' : '' }}>
                                        {{ $wk['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Single Date Selector (Shown only on 'date') --}}
                        <div class="col-xl-2 col-md-3 col-sm-6 filter-input-group" id="group_date" style="display: none;">
                            <label class="form-label fs-11 fw-bold text-uppercase text-muted mb-1">
                                <i class="ri-calendar-event-line text-success me-1"></i> Pilih Tanggal
                            </label>
                            <input type="date" name="date" id="filter_date" class="form-control form-control-sm" value="{{ $selectedDate }}">
                        </div>

                        {{-- Custom Start Date (Shown only on 'custom') --}}
                        <div class="col-xl-2 col-md-3 col-sm-6 filter-input-group" id="group_start_date" style="display: none;">
                            <label class="form-label fs-11 fw-bold text-uppercase text-muted mb-1">
                                <i class="ri-calendar-check-line text-primary me-1"></i> Dari Tanggal
                            </label>
                            <input type="date" name="start_date" id="filter_start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                        </div>

                        {{-- Custom End Date (Shown only on 'custom') --}}
                        <div class="col-xl-2 col-md-3 col-sm-6 filter-input-group" id="group_end_date" style="display: none;">
                            <label class="form-label fs-11 fw-bold text-uppercase text-muted mb-1">
                                <i class="ri-calendar-close-line text-danger me-1"></i> Sampai Tanggal
                            </label>
                            <input type="date" name="end_date" id="filter_end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                        </div>

                        {{-- Kategori Mesin Selector --}}
                        <div class="col-xl-2 col-md-3 col-sm-6">
                            <label class="form-label fs-11 fw-bold text-uppercase text-muted mb-1">
                                <i class="ri-macbook-line text-purple me-1"></i> Kategori Mesin
                            </label>
                            <select name="kategori" id="filter_kategori" class="form-select form-select-sm">
                                <option value="all" {{ $kategori === 'all' ? 'selected' : '' }}>Semua Mesin (28 Mesin)</option>
                                <option value="pouch" {{ $kategori === 'pouch' ? 'selected' : '' }}>Pouch Saja (21 Mesin)</option>
                                <option value="sachet" {{ $kategori === 'sachet' ? 'selected' : '' }}>Sachet Saja (7 Mesin)</option>
                            </select>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-xl-auto col-md-auto col-12 d-flex align-items-end gap-1 mt-auto pt-1">
                            <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center gap-1 shadow-sm">
                                <i class="ri-search-line"></i> Terapkan
                            </button>
                            <a href="{{ route('epr.dashboard.cm') }}" class="btn btn-sm btn-light border d-flex align-items-center gap-1" title="Reset ke Default">
                                <i class="ri-refresh-line"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                {{-- Active Period Info Badge --}}
                <div class="mt-2 pt-2 border-top d-flex flex-wrap align-items-center justify-content-between gap-2 fs-12">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-soft-primary text-primary px-2 py-1 fw-semibold">
                            <i class="ri-time-line me-1"></i> Periode Aktif: {{ $periodLabel }}
                        </span>
                        <span class="badge bg-soft-info text-info px-2 py-1">
                            {{ $daysInPeriod }} Hari Terpilih
                        </span>
                        @if($kategori !== 'all')
                        <span class="badge bg-soft-warning text-warning px-2 py-1 text-uppercase">
                            Kategori: {{ $kategori }}
                        </span>
                        @endif
                    </div>
                    <div class="text-muted fs-11">
                        Rentang Waktu: <strong class="text-dark">{{ date('d/m/Y', strtotime($startDate)) }}</strong> s/d <strong class="text-dark">{{ date('d/m/Y', strtotime($endDate)) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 1: 6 Top KPI Metric Cards (Calculated directly from CM History & Loading Time) --}}
        <div class="row g-3 mb-4">
            {{-- 1. Availability Avg --}}
            <div class="col-xl-2 col-md-4 col-6">
                <div class="kpi-card p-3 h-100 border-start border-success border-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kpi-title">AVAILABILITY PABRIK</span>
                        <i class="ri-percent-line text-success fs-18"></i>
                    </div>
                    <div class="kpi-value text-success" id="kpi-avail">{{ number_format($avgAvailPct, 1) }}%</div>
                    <div class="kpi-sub mt-1">Target ≥ 85% | Loading: {{ number_format($grandTotalLt) }} m</div>
                </div>
            </div>

            {{-- 2. Total Downtime --}}
            <div class="col-xl-2 col-md-4 col-6">
                <div class="kpi-card p-3 h-100 border-start border-danger border-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kpi-title">TOTAL DOWNTIME</span>
                        <i class="ri-time-line text-danger fs-18"></i>
                    </div>
                    <div class="kpi-value text-danger" id="kpi-downtime">{{ number_format($totalDowntime) }} <span class="fs-12 text-muted fw-normal">menit</span></div>
                    <div class="kpi-sub mt-1">Elec: {{ $elecDuration }}m | Mech: {{ $mechDuration }}m</div>
                </div>
            </div>

            {{-- 3. Plant Breakdown Rate --}}
            <div class="col-xl-2 col-md-4 col-6">
                <div class="kpi-card p-3 h-100 border-start border-warning border-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kpi-title">PLANT DT RATE</span>
                        <i class="ri-error-warning-line text-warning fs-18"></i>
                    </div>
                    <div class="kpi-value text-warning" id="kpi-breakdown">{{ number_format($avgBreakdownPct, 2) }}%</div>
                    <div class="kpi-sub mt-1">Rasio Total DT / Loading Time</div>
                </div>
            </div>

            {{-- 4. MTTR Avg --}}
            <div class="col-xl-2 col-md-4 col-6">
                <div class="kpi-card p-3 h-100 border-start border-info border-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kpi-title">MTTR (AVG)</span>
                        <i class="ri-tools-line text-info fs-18"></i>
                    </div>
                    <div class="kpi-value text-info" id="kpi-mttr">{{ $avgMttr }} <span class="fs-12 text-muted fw-normal">menit</span></div>
                    <div class="kpi-sub mt-1">Rata-rata durasi perbaikan</div>
                </div>
            </div>

            {{-- 5. Total Kejadian (MTBF) --}}
            <div class="col-xl-2 col-md-4 col-6">
                <div class="kpi-card p-3 h-100 border-start border-purple border-4" style="border-left-color: #8b5cf6 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kpi-title">TOTAL KEJADIAN (DT)</span>
                        <i class="ri-shield-flash-line text-violet fs-18" style="color: #8b5cf6;"></i>
                    </div>
                    <div class="kpi-value" style="color: #8b5cf6;" id="kpi-freq">{{ $totalReports }} <span class="fs-12 text-muted fw-normal">kejadian</span></div>
                    <div class="kpi-sub mt-1">MTBF Avg: {{ number_format($avgMtbf) }} m</div>
                </div>
            </div>

            {{-- 6. Worst Machine --}}
            <div class="col-xl-2 col-md-4 col-6">
                <div class="kpi-card p-3 h-100 border-start border-danger border-4" style="background: #fff5f5;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kpi-title text-danger">WORST MACHINE</span>
                        <i class="ri-alarm-warning-fill text-danger fs-18"></i>
                    </div>
                    <div class="kpi-value text-danger fs-16" id="kpi-worst">{{ $worstMachineName }}</div>
                    <div class="kpi-sub mt-1 text-danger font-semibold">Downtime tertinggi periode ini</div>
                </div>
            </div>
        </div>

        {{-- Row 2: Top 5 Mesin Terbaik & Worst 5 Mesin --}}
        <div class="row g-3 mb-4">
            {{-- Top 5 Mesin --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header header-emerald">
                        <span><i class="ri-trophy-line text-warning me-1"></i> TOP 5 MESIN TERBAIK (Availability Tertinggi)</span>
                        <span class="badge bg-white text-success fs-10">Efisiensi Tinggi</span>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table table-compact table-bordered mb-0 align-middle text-center">
                                <thead class="table-light fs-11">
                                    <tr>
                                        <th width="45">RANK</th>
                                        <th width="75">MESIN</th>
                                        <th>KATEGORI</th>
                                        <th>AVAILABILITY</th>
                                        <th>DOWNTIME</th>
                                        <th>MTTR</th>
                                        <th>MTBF</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($top5 as $idx => $t)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="fw-bold text-success">{{ $t['mesin'] }}</td>
                                        <td>
                                            <span class="badge {{ $t['kategori'] === 'Pouch' ? 'badge-pouch' : 'badge-sachet' }}">
                                                {{ $t['kategori'] }}
                                            </span>
                                        </td>
                                        <td class="text-success fw-bold">{{ number_format($t['avail'], 1) }}%</td>
                                        <td class="font-monospace">{{ $t['total_dt'] }} m</td>
                                        <td class="font-monospace">{{ $t['mttr'] }}</td>
                                        <td class="font-monospace text-muted">{{ number_format($t['mtbf']) }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center text-muted py-3">Belum ada data pada periode ini</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Worst 5 Mesin --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header header-red">
                        <span><i class="ri-alert-fill text-white me-1"></i> WORST 5 MESIN (Downtime Tertinggi)</span>
                        <span class="badge bg-white text-danger fs-10">Prioritas Perbaikan</span>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table table-compact table-bordered mb-0 align-middle text-center">
                                <thead class="table-light fs-11">
                                    <tr>
                                        <th width="45">RANK</th>
                                        <th width="75">MESIN</th>
                                        <th>KATEGORI</th>
                                        <th>DOWNTIME</th>
                                        <th>% DT</th>
                                        <th>MTTR</th>
                                        <th>KEJADIAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($worst5 as $idx => $w)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="fw-bold text-danger">{{ $w['mesin'] }}</td>
                                        <td>
                                            <span class="badge {{ $w['kategori'] === 'Pouch' ? 'badge-pouch' : 'badge-sachet' }}">
                                                {{ $w['kategori'] }}
                                            </span>
                                        </td>
                                        <td class="text-danger fw-bold font-monospace">{{ $w['total_dt'] }} m</td>
                                        <td class="text-danger fw-semibold">{{ number_format($w['breakdown'], 2) }}%</td>
                                        <td class="font-monospace">{{ $w['mttr'] }}</td>
                                        <td><span class="badge bg-soft-danger text-danger">{{ $w['freq'] }}x</span></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center text-muted py-3">Belum ada data pada periode ini</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: Pareto Kerusakan (Jenis DT) & Klasifikasi Electrical vs Mechanical --}}
        <div class="row g-3 mb-4">
            {{-- Module Pareto Analisis Jenis DT --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header header-blue">
                        <span><i class="ri-bar-chart-grouped-line me-1"></i> PARETO ANALISIS: TOP JENIS KERUSAKAN (DT)</span>
                        <span class="badge bg-light text-dark fs-10">80/20 Rule</span>
                    </div>
                    <div class="card-body p-3">
                        <div id="chartParetoJenisDt" style="min-height: 280px;"></div>
                        <div class="text-muted fs-11 text-center mt-1">Mengidentifikasi 80% downtime disumbang oleh kategori kerusakan apa saja</div>
                    </div>
                </div>
            </div>

            {{-- Module Klasifikasi Electrical & Mechanical --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header">
                        <span><i class="ri-flashlight-line text-info me-1"></i> KLASIFIKASI ELECTRICAL VS MECHANICAL</span>
                        <span class="badge bg-light text-dark fs-10">Distribusi Disiplin</span>
                    </div>
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="row g-2 mb-3">
                            {{-- Electrical Card --}}
                            <div class="col-6">
                                <div class="p-3 rounded-3 border h-100" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-color: #bfdbfe !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-1">
                                            <i class="ri-flashlight-fill text-primary fs-18"></i>
                                            <h6 class="fw-bold mb-0 text-primary fs-12">ELECTRICAL</h6>
                                        </div>
                                        <span class="badge bg-primary text-white">{{ $elecShare }}% DT</span>
                                    </div>
                                    <div class="fs-18 fw-bold text-dark font-monospace">{{ $elecDuration }} <small class="fs-11 text-muted fw-normal">menit</small></div>
                                    <div class="text-muted fs-11">{{ $elecCount }} kali kejadian</div>
                                </div>
                            </div>
                            {{-- Mechanical Card --}}
                            <div class="col-6">
                                <div class="p-3 rounded-3 border h-100" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); border-color: #e9d5ff !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-1">
                                            <i class="ri-settings-4-fill fs-18" style="color: #7c3aed;"></i>
                                            <h6 class="fw-bold mb-0 fs-12" style="color: #6b21a8;">MECHANICAL</h6>
                                        </div>
                                        <span class="badge text-white" style="background-color: #7c3aed;">{{ $mechShare }}% DT</span>
                                    </div>
                                    <div class="fs-18 fw-bold font-monospace" style="color: #6b21a8;">{{ $mechDuration }} <small class="fs-11 text-muted fw-normal">menit</small></div>
                                    <div class="text-muted fs-11">{{ $mechCount }} kali kejadian</div>
                                </div>
                            </div>
                        </div>

                        {{-- Progress bar --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between fs-11 text-muted mb-1">
                                <span>Distribusi Downtime</span>
                                <span>Total: {{ $totalDowntime }} menit</span>
                            </div>
                            <div class="progress" style="height: 14px; border-radius: 7px; overflow: hidden; background-color: #e2e8f0;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $elecShare }}%;" title="Electrical: {{ $elecShare }}%"></div>
                                <div class="progress-bar" role="progressbar" style="width: {{ $mechShare }}%; background-color: #7c3aed;" title="Mechanical: {{ $mechShare }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between fs-11 text-muted mt-1">
                                <span><i class="ri-checkbox-blank-circle-fill text-primary me-1"></i>Electrical ({{ $elecShare }}%)</span>
                                <span><i class="ri-checkbox-blank-circle-fill me-1" style="color: #7c3aed;"></i>Mechanical ({{ $mechShare }}%)</span>
                            </div>
                        </div>

                        <div class="p-2 border rounded bg-light text-center fs-12">
                            <span class="text-muted">Total Kejadian Kerusakan:</span>
                            <span class="fw-bold text-dark ms-1">{{ $totalReports }} Kali</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        {{-- Row 4 (BARU): Grafik Downtime Harian — Mechanical & Electrical (Divided into 2 Charts) --}}
        <div class="row g-3 mb-4">
            {{-- Grafik 1: Downtime Harian Mechanical --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden; border-top: 3px solid #7c3aed !important;">
                    <div class="card-module-header" style="background: #581c87;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-circle p-1 d-flex align-items-center justify-content-center text-white" style="width: 22px; height: 22px; background: rgba(255,255,255,0.2);">
                                <i class="ri-settings-4-line fs-13"></i>
                            </span>
                            <span>GRAFIK DOWNTIME HARIAN — MECHANICAL (%)</span>
                        </div>
                        <span class="badge bg-white text-purple fs-10" style="color: #7c3aed;">
                            Avg: {{ number_format($avgDailyMechPct, 2) }}%
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 px-1 gap-2">
                            <span class="text-muted fs-11">
                                Persentase (%) Downtime Mechanical per hari terhadap Loading Time (Sesuai Filter)
                            </span>
                            <span class="badge fs-11 font-monospace" style="background: #faf5ff; color: #7c3aed; border: 1px solid #e9d5ff;">
                                Total: {{ number_format($mechDuration) }} Menit ({{ $mechCount }} Kejadian)
                            </span>
                        </div>
                        <div id="chartDailyMechanical" style="min-height: 290px;"></div>
                        <div class="d-flex flex-wrap justify-content-between align-items-center fs-11 text-muted mt-2 pt-2 border-top gap-2">
                            <span style="background-color: #fef08a; padding: 2px 8px; border-radius: 4px; color: #854d0e; font-weight: 600;">
                                <i class="ri-line-chart-line me-1"></i> Rata-rata DT Mechanical: {{ number_format($avgDailyMechPct, 2) }}%
                            </span>
                            <span>
                                Puncak Tertinggi: <strong class="text-danger font-monospace">{{ number_format($maxDailyMechPct, 2) }}%</strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafik 2: Downtime Harian Electrical --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden; border-top: 3px solid #2563eb !important;">
                    <div class="card-module-header header-blue">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-circle p-1 d-flex align-items-center justify-content-center text-white" style="width: 22px; height: 22px; background: rgba(255,255,255,0.2);">
                                <i class="ri-flashlight-line fs-13"></i>
                            </span>
                            <span>GRAFIK DOWNTIME HARIAN — ELECTRICAL (%)</span>
                        </div>
                        <span class="badge bg-white text-primary fs-10">
                            Avg: {{ number_format($avgDailyElecPct, 2) }}%
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 px-1 gap-2">
                            <span class="text-muted fs-11">
                                Persentase (%) Downtime Electrical per hari terhadap Loading Time (Sesuai Filter)
                            </span>
                            <span class="badge bg-soft-primary text-primary fs-11 font-monospace" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                                Total: {{ number_format($elecDuration) }} Menit ({{ $elecCount }} Kejadian)
                            </span>
                        </div>
                        <div id="chartDailyElectrical" style="min-height: 290px;"></div>
                        <div class="d-flex flex-wrap justify-content-between align-items-center fs-11 text-muted mt-2 pt-2 border-top gap-2">
                            <span style="background-color: #dbeafe; padding: 2px 8px; border-radius: 4px; color: #1e40af; font-weight: 600;">
                                <i class="ri-line-chart-line me-1"></i> Rata-rata DT Electrical: {{ number_format($avgDailyElecPct, 2) }}%
                            </span>
                            <span>
                                Puncak Tertinggi: <strong class="text-danger font-monospace">{{ number_format($maxDailyElecPct, 2) }}%</strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 4: TOP 5 Electrical & TOP 5 Mechanical Breakdown --}}
        <div class="row g-3 mb-4">
            {{-- Top 5 Electrical --}}
            <div class="col-lg-6">
                <div class="card border border-primary-subtle shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-soft-primary border-bottom border-primary-subtle py-2 px-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary rounded-pill p-1 d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">
                                <i class="ri-flashlight-line fs-13"></i>
                            </span>
                            <h6 class="fw-bold text-primary mb-0 fs-12">TOP 5 KERUSAKAN ELECTRICAL (DURASI TERTINGGI)</h6>
                        </div>
                        <span class="badge bg-white text-primary border border-primary-subtle fs-10">Durasi Tertinggi</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-center" style="font-size: 11.5px;">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">#</th>
                                        <th width="85">TGL</th>
                                        <th width="55">SHIFT</th>
                                        <th width="60">MESIN</th>
                                        <th class="text-start">KETERANGAN KERUSAKAN</th>
                                        <th width="75">DURASI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($top5Elec as $idx => $r)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ date('d/m/Y', strtotime($r->tanggal)) }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $r->shift }}</span></td>
                                        <td class="fw-bold text-primary">{{ $r->mesin }}</td>
                                        <td class="text-start text-truncate" style="max-width: 220px;" title="{{ $r->keterangan }}">{{ $r->keterangan }}</td>
                                        <td class="fw-bold text-danger font-monospace">{{ $r->total_menit }} m</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data breakdown electrical</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top 5 Mechanical --}}
            <div class="col-lg-6">
                <div class="card border shadow-sm h-100" style="border-radius: 12px; overflow: hidden; border-color: #e9d5ff !important;">
                    <div class="card-header border-bottom py-2 px-3 d-flex justify-content-between align-items-center" style="background-color: #f5f3ff; border-color: #e9d5ff !important;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill p-1 d-flex align-items-center justify-content-center text-white" style="width: 22px; height: 22px; background-color: #7c3aed;">
                                <i class="ri-settings-4-line fs-13"></i>
                            </span>
                            <h6 class="fw-bold mb-0 fs-12" style="color: #6b21a8;">TOP 5 KERUSAKAN MECHANICAL (DURASI TERTINGGI)</h6>
                        </div>
                        <span class="badge bg-white border fs-10" style="color: #6b21a8; border-color: #e9d5ff !important;">Durasi Tertinggi</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-center" style="font-size: 11.5px;">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">#</th>
                                        <th width="85">TGL</th>
                                        <th width="55">SHIFT</th>
                                        <th width="60">MESIN</th>
                                        <th class="text-start">KETERANGAN KERUSAKAN</th>
                                        <th width="75">DURASI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($top5Mech as $idx => $r)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ date('d/m/Y', strtotime($r->tanggal)) }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $r->shift }}</span></td>
                                        <td class="fw-bold text-purple" style="color: #7c3aed;">{{ $r->mesin }}</td>
                                        <td class="text-start text-truncate" style="max-width: 220px;" title="{{ $r->keterangan }}">{{ $r->keterangan }}</td>
                                        <td class="fw-bold text-danger font-monospace">{{ $r->total_menit }} m</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data breakdown mechanical</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 5: Rekapitulasi Lengkap 28 Mesin Pouch & Sachet --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold text-uppercase mb-0 text-white">
                        <i class="ri-table-line text-warning me-2"></i>REKAPITULASI DOWNTIME & AVAILABILITY 28 MESIN (POUCH & SACHET)
                    </h6>
                    <small class="text-slate-300">Data terintegrasi penuh dari log corrective maintenance dan loading time mesin</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white">21 Pouch</span>
                    <span class="badge bg-warning text-dark">7 Sachet</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 480px;">
                    <table class="table table-bordered table-hover align-middle mb-0 text-center" style="font-size: 12px;">
                        <thead class="table-light sticky-top" style="z-index: 1;">
                            <tr>
                                <th width="50">NO</th>
                                <th width="90">MESIN</th>
                                <th width="90">KATEGORI</th>
                                <th width="85">SHIFT A</th>
                                <th width="85">SHIFT B</th>
                                <th width="85">SHIFT C</th>
                                <th width="100">TOTAL DT</th>
                                <th width="110">LOADING TIME</th>
                                <th width="90">MTTR</th>
                                <th width="90">MTBF (x)</th>
                                <th width="110">MTBF (MENIT)</th>
                                <th width="110">AVAILABILITY</th>
                                <th width="90">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($machineStats as $idx => $st)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td class="fw-bold">{{ $st['mesin'] }}</td>
                                <td>
                                    <span class="badge {{ $st['kategori'] === 'Pouch' ? 'badge-pouch' : 'badge-sachet' }}">
                                        {{ $st['kategori'] }}
                                    </span>
                                </td>
                                <td class="font-monospace">{{ $st['dt_a'] ?: '-' }}</td>
                                <td class="font-monospace">{{ $st['dt_b'] ?: '-' }}</td>
                                <td class="font-monospace">{{ $st['dt_c'] ?: '-' }}</td>
                                <td class="font-monospace fw-bold {{ $st['total_dt'] > 0 ? 'text-danger' : 'text-muted' }}">
                                    {{ $st['total_dt'] }} m
                                </td>
                                <td class="font-monospace text-muted">{{ number_format($st['loading_time']) }} m</td>
                                <td class="font-monospace">{{ $st['mttr'] }}</td>
                                <td class="font-monospace">{{ $st['freq'] }}</td>
                                <td class="font-monospace">{{ number_format($st['mtbf']) }}</td>
                                <td class="font-monospace fw-bold {{ $st['avail'] >= 90 ? 'text-success' : ($st['avail'] >= 80 ? 'text-warning' : 'text-danger') }}">
                                    {{ number_format($st['avail'], 2) }}%
                                </td>
                                <td>
                                    @if($st['avail'] >= 90)
                                        <span class="badge bg-soft-success text-success">Good</span>
                                    @elseif($st['avail'] >= 80)
                                        <span class="badge bg-soft-warning text-warning">Warning</span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger">Critical</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold sticky-bottom" style="z-index: 1;">
                            <tr>
                                <td colspan="3" class="text-center">TOTAL / RATA-RATA PABRIK</td>
                                <td class="font-monospace text-primary">{{ $shiftA }} m</td>
                                <td class="font-monospace text-warning">{{ $shiftB }} m</td>
                                <td class="font-monospace text-success">{{ $shiftC }} m</td>
                                <td class="font-monospace text-danger">{{ $totalDowntime }} m</td>
                                <td class="font-monospace">{{ number_format($grandTotalLt) }} m</td>
                                <td class="font-monospace">{{ $avgMttr }}</td>
                                <td class="font-monospace">{{ $totalReports }}</td>
                                <td class="font-monospace">{{ number_format($avgMtbf) }}</td>
                                <td class="font-monospace text-success fs-13">{{ number_format($avgAvailPct, 2) }}%</td>
                                <td>
                                    <span class="badge {{ $avgAvailPct >= 85 ? 'bg-success' : 'bg-warning' }}">
                                        {{ $avgAvailPct >= 85 ? 'NORMAL' : 'PERHATIAN' }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleFilterInputs() {
    const type = document.getElementById('filter_type').value;
    const groupMonth = document.getElementById('group_month');
    const groupWeek = document.getElementById('group_week');
    const groupDate = document.getElementById('group_date');
    const groupStart = document.getElementById('group_start_date');
    const groupEnd = document.getElementById('group_end_date');

    if (!groupMonth || !groupWeek || !groupDate || !groupStart || !groupEnd) return;

    // Reset visibility
    groupMonth.style.display = 'none';
    groupWeek.style.display = 'none';
    groupDate.style.display = 'none';
    groupStart.style.display = 'none';
    groupEnd.style.display = 'none';

    if (type === 'month') {
        groupMonth.style.display = 'block';
    } else if (type === 'week') {
        groupMonth.style.display = 'block';
        groupWeek.style.display = 'block';
    } else if (type === 'date') {
        groupDate.style.display = 'block';
    } else if (type === 'custom') {
        groupStart.style.display = 'block';
        groupEnd.style.display = 'block';
    }
}

function updateWeeksDropdown() {
    const monthEl = document.getElementById('filter_month');
    const weekEl = document.getElementById('filter_week');
    if (!monthEl || !weekEl) return;

    const yearMonth = monthEl.value;
    if (!yearMonth) return;

    const parts = yearMonth.split('-').map(Number);
    const year = parts[0];
    const month = parts[1];

    const firstDay = new Date(year, month - 1, 1);
    const lastDay = new Date(year, month, 0);

    const day = firstDay.getDay(); // 0 is Sun, 1 is Mon
    const diffToMonday = (day === 0 ? -6 : 1 - day);
    let curr = new Date(firstDay);
    curr.setDate(firstDay.getDate() + diffToMonday);

    const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];
    const currentSelected = weekEl.value;
    weekEl.innerHTML = '';

    let w = 1;
    while (curr <= lastDay) {
        let wStart = new Date(curr);
        let wEnd = new Date(curr);
        wEnd.setDate(curr.getDate() + 6);

        let sDay = String(wStart.getDate()).padStart(2, '0');
        let sMon = monthNames[wStart.getMonth()];
        let eDay = String(wEnd.getDate()).padStart(2, '0');
        let eMon = monthNames[wEnd.getMonth()];
        let eYear = wEnd.getFullYear();

        const opt = document.createElement('option');
        opt.value = w;
        opt.textContent = `Minggu ${w} (${sDay} ${sMon} - ${eDay} ${eMon} ${eYear})`;
        if (String(w) === String(currentSelected)) {
            opt.selected = true;
        }
        weekEl.appendChild(opt);

        curr.setDate(curr.getDate() + 7);
        w++;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    toggleFilterInputs();

    // Pareto Analisis Jenis DT (Column + Line)
    const paretoCategories = @json($paretoCategories);
    const paretoDurations = @json($paretoDurations);
    const paretoCumulative = @json($paretoCumulative);
    const paretoEl = document.querySelector("#chartParetoJenisDt");

    if (paretoEl) {
        if (paretoCategories && paretoCategories.length > 0) {
            const paretoOptions = {
                series: [{
                    name: 'Durasi DT (Menit)',
                    type: 'column',
                    data: paretoDurations
                }, {
                    name: 'Kumulatif %',
                    type: 'line',
                    data: paretoCumulative
                }],
                chart: {
                    type: 'line',
                    height: 280,
                    toolbar: { show: false }
                },
                stroke: { width: [0, 3], curve: 'smooth' },
                colors: ['#3b82f6', '#ef4444'],
                plotOptions: { bar: { columnWidth: '45%', borderRadius: 4 } },
                xaxis: {
                    categories: paretoCategories,
                    labels: { rotate: -35, style: { fontSize: '10px' }, trim: true }
                },
                yaxis: [
                    { title: { text: 'Downtime (Menit)', style: { fontSize: '11px' } } },
                    { opposite: true, max: 100, title: { text: 'Kumulatif %', style: { fontSize: '11px' } } }
                ],
                grid: { borderColor: '#f1f5f9' }
            };
            new ApexCharts(paretoEl, paretoOptions).render();
        } else {
            paretoEl.innerHTML = `
                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted py-5" style="min-height: 250px;">
                    <i class="ri-inbox-line fs-36 text-secondary mb-2" style="font-size: 36px;"></i>
                    <span class="fs-13">Tidak ada data breakdown jenis DT pada periode ini</span>
                </div>
            `;
        }
    }

    // -------------------------------------------------------------
    // Data Harian (Mechanical & Electrical) dari Controller
    // -------------------------------------------------------------
    const dailyLabels = @json($dailyLabels);
    const dailyFullDates = @json($dailyFullDates);
    const dailyMechPct = @json($dailyMechPct);
    const dailyElecPct = @json($dailyElecPct);
    const dailyMechMins = @json($dailyMechMins);
    const dailyElecMins = @json($dailyElecMins);
    const dailyMechCount = @json($dailyMechCount);
    const dailyElecCount = @json($dailyElecCount);

    // Hitung headroom sumbu Y agar data label di atas titik puncak tidak terpotong
    const maxMechVal = dailyMechPct.length > 0 ? Math.max(...dailyMechPct) : 0;
    const yAxisMaxMech = maxMechVal > 0 ? Number((maxMechVal * 1.35).toFixed(2)) : 1.0;

    const maxElecVal = dailyElecPct.length > 0 ? Math.max(...dailyElecPct) : 0;
    const yAxisMaxElec = maxElecVal > 0 ? Number((maxElecVal * 1.35).toFixed(2)) : 1.0;

    // 1. Grafik DT Harian: MECHANICAL
    const elDailyMech = document.querySelector("#chartDailyMechanical");
    if (elDailyMech && dailyLabels.length > 0) {
        const optionsDailyMech = {
            series: [{
                name: '% DT Mechanical',
                data: dailyMechPct
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            stroke: {
                curve: 'straight',
                width: 3
            },
            colors: ['#7c3aed'],
            markers: {
                size: 7,
                colors: ['#7c3aed'],
                strokeColors: '#ffffff',
                strokeWidth: 2.5,
                hover: { size: 9 }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val > 0 ? (Number(val).toFixed(2) + '%').replace('.', ',') : '';
                },
                offsetY: -10,
                style: {
                    fontSize: '12px',
                    fontWeight: 800,
                    colors: ['#581c87']
                },
                background: {
                    enabled: false
                }
            },
            xaxis: {
                categories: dailyLabels,
                labels: {
                    style: { fontSize: '11px', fontWeight: 600, colors: '#475569' },
                    rotate: dailyLabels.length > 10 ? -45 : 0,
                    trim: false,
                    hideOverlappingLabels: false
                },
                axisBorder: { show: true, color: '#cbd5e1' },
                axisTicks: { show: true }
            },
            yaxis: {
                title: {
                    text: '% Downtime Mechanical',
                    style: { fontSize: '11px', fontWeight: 600, color: '#64748b' }
                },
                labels: {
                    formatter: function (val) {
                        return val.toFixed(1) + '%';
                    }
                },
                min: 0,
                max: yAxisMaxMech
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 3,
                padding: {
                    top: 15,
                    right: 25,
                    left: 15,
                    bottom: 5
                }
            },
            tooltip: {
                shared: true,
                custom: function({series, seriesIndex, dataPointIndex, w}) {
                    const pct = series[seriesIndex][dataPointIndex];
                    const mins = dailyMechMins[dataPointIndex] || 0;
                    const count = dailyMechCount[dataPointIndex] || 0;
                    const fullDate = dailyFullDates[dataPointIndex] || dailyLabels[dataPointIndex] || '';
                    return `
                        <div class="p-2 shadow-sm border rounded bg-white" style="font-size: 12px; min-width: 170px;">
                            <div class="fw-bold text-dark border-bottom pb-1 mb-1">${fullDate}</div>
                            <div class="d-flex justify-content-between gap-3" style="color: #7c3aed;">
                                <span>Persentase DT:</span>
                                <strong>${Number(pct).toFixed(2)}%</strong>
                            </div>
                            <div class="d-flex justify-content-between gap-3 text-muted">
                                <span>Durasi DT:</span>
                                <strong class="text-dark">${mins} menit</strong>
                            </div>
                            <div class="d-flex justify-content-between gap-3 text-muted">
                                <span>Jumlah Kejadian:</span>
                                <strong class="text-dark">${count} kali</strong>
                            </div>
                        </div>
                    `;
                }
            }
        };
        new ApexCharts(elDailyMech, optionsDailyMech).render();
    }

    // 2. Grafik DT Harian: ELECTRICAL
    const elDailyElec = document.querySelector("#chartDailyElectrical");
    if (elDailyElec && dailyLabels.length > 0) {
        const optionsDailyElec = {
            series: [{
                name: '% DT Electrical',
                data: dailyElecPct
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            stroke: {
                curve: 'straight',
                width: 3
            },
            colors: ['#2563eb'],
            markers: {
                size: 7,
                colors: ['#2563eb'],
                strokeColors: '#ffffff',
                strokeWidth: 2.5,
                hover: { size: 9 }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val > 0 ? (Number(val).toFixed(2) + '%').replace('.', ',') : '';
                },
                offsetY: -10,
                style: {
                    fontSize: '12px',
                    fontWeight: 800,
                    colors: ['#1e40af']
                },
                background: {
                    enabled: false
                }
            },
            xaxis: {
                categories: dailyLabels,
                labels: {
                    style: { fontSize: '11px', fontWeight: 600, colors: '#475569' },
                    rotate: dailyLabels.length > 10 ? -45 : 0,
                    trim: false,
                    hideOverlappingLabels: false
                },
                axisBorder: { show: true, color: '#cbd5e1' },
                axisTicks: { show: true }
            },
            yaxis: {
                title: {
                    text: '% Downtime Electrical',
                    style: { fontSize: '11px', fontWeight: 600, color: '#64748b' }
                },
                labels: {
                    formatter: function (val) {
                        return val.toFixed(1) + '%';
                    }
                },
                min: 0,
                max: yAxisMaxElec
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 3,
                padding: {
                    top: 15,
                    right: 25,
                    left: 15,
                    bottom: 5
                }
            },
            tooltip: {
                shared: true,
                custom: function({series, seriesIndex, dataPointIndex, w}) {
                    const pct = series[seriesIndex][dataPointIndex];
                    const mins = dailyElecMins[dataPointIndex] || 0;
                    const count = dailyElecCount[dataPointIndex] || 0;
                    const fullDate = dailyFullDates[dataPointIndex] || dailyLabels[dataPointIndex] || '';
                    return `
                        <div class="p-2 shadow-sm border rounded bg-white" style="font-size: 12px; min-width: 170px;">
                            <div class="fw-bold text-dark border-bottom pb-1 mb-1">${fullDate}</div>
                            <div class="d-flex justify-content-between gap-3 text-primary">
                                <span>Persentase DT:</span>
                                <strong>${Number(pct).toFixed(2)}%</strong>
                            </div>
                            <div class="d-flex justify-content-between gap-3 text-muted">
                                <span>Durasi DT:</span>
                                <strong class="text-dark">${mins} menit</strong>
                            </div>
                            <div class="d-flex justify-content-between gap-3 text-muted">
                                <span>Jumlah Kejadian:</span>
                                <strong class="text-dark">${count} kali</strong>
                            </div>
                        </div>
                    `;
                }
            }
        };
        new ApexCharts(elDailyElec, optionsDailyElec).render();
    }
});
</script>
@endsection
