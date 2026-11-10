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
        <div class="kpi-header mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
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
                {{-- Month Selector --}}
                <div class="d-flex align-items-center bg-dark bg-opacity-50 p-2 rounded border border-secondary border-opacity-50">
                    <label class="form-label text-slate-300 fs-11 mb-0 me-2 fw-semibold text-nowrap">BULAN:</label>
                    <select id="kpi-month" class="form-select form-select-sm bg-dark text-white border-secondary" style="width: 140px;" onchange="changeKpiFilter()">
                        @foreach($availableMonths as $m)
                            <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                                {{ date('F Y', strtotime($m . '-01')) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Specific Date Selector (Optional) --}}
                @if(count($availableDates) > 0)
                <div class="d-flex align-items-center bg-dark bg-opacity-50 p-2 rounded border border-secondary border-opacity-50">
                    <label class="form-label text-slate-300 fs-11 mb-0 me-2 fw-semibold text-nowrap">TANGGAL:</label>
                    <select id="kpi-date" class="form-select form-select-sm bg-dark text-white border-secondary" style="width: 130px;" onchange="changeKpiFilter()">
                        <option value="">Semua (1 Bulan)</option>
                        @foreach($availableDates as $d)
                            <option value="{{ $d }}" {{ $selectedDate === $d ? 'selected' : '' }}>
                                {{ date('d M Y', strtotime($d)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Quick Link to Corrective Maintenance History --}}
                <a href="{{ route('epr.cm.data') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
                    <i class="ri-file-list-3-line"></i> Data Log CM
                </a>
                <a href="{{ route('epr.cm.extra-data') }}" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1">
                    <i class="ri-settings-3-line"></i> Input Biaya & KPI
                </a>
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

        {{-- Row 2: 1. Performance Map, 2&3. Top 5 & Worst 5 Mesin, 4. Shift & Trend Breakdown --}}
        <div class="row g-3 mb-4">
            {{-- Module 1: Machine Performance Map --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header">
                        <span>1. MACHINE PERFORMANCE MAP (MTBF vs BREAKDOWN)</span>
                        <span class="badge bg-light text-dark fs-10">28 Mesin</span>
                    </div>
                    <div class="card-body p-3">
                        <div id="chartPerformanceMap" style="min-height: 280px;"></div>
                        <div class="d-flex justify-content-center gap-3 fs-11 mt-2">
                            <span><span class="status-dot dot-red me-1"></span> High Breakdown (&gt;3%)</span>
                            <span><span class="status-dot dot-yellow me-1"></span> Low MTBF (Freq Stop)</span>
                            <span><span class="status-dot dot-green me-1"></span> Healthy Zone (Ideal)</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Module 2 & 3: Top 5 & Worst 5 Mesin --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    {{-- Top 5 Mesin --}}
                    <div class="card-module-header header-emerald">
                        <span>2. TOP 5 MESIN TERBAIK (Availability Tertinggi)</span>
                        <i class="ri-trophy-line text-warning"></i>
                    </div>
                    <div class="card-body p-2 border-bottom">
                        <table class="table table-compact table-bordered mb-0 align-middle text-center">
                            <thead class="table-light fs-11">
                                <tr>
                                    <th>RANK</th>
                                    <th>MESIN</th>
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
                                    <td class="fw-bold">{{ $t['mesin'] }}</td>
                                    <td class="text-success fw-bold">{{ number_format($t['avail'], 1) }}%</td>
                                    <td class="font-monospace">{{ $t['total_dt'] }} m</td>
                                    <td class="font-monospace">{{ $t['mttr'] }}</td>
                                    <td class="font-monospace text-muted">{{ number_format($t['mtbf']) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-2">Belum ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Worst 5 Mesin --}}
                    <div class="card-module-header header-red">
                        <span>3. WORST 5 MESIN (Downtime Tertinggi)</span>
                        <i class="ri-alert-fill text-white"></i>
                    </div>
                    <div class="card-body p-2">
                        <table class="table table-compact table-bordered mb-0 align-middle text-center">
                            <thead class="table-light fs-11">
                                <tr>
                                    <th>RANK</th>
                                    <th>MESIN</th>
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
                                    <td class="text-danger fw-bold font-monospace">{{ $w['total_dt'] }} m</td>
                                    <td class="text-danger fw-semibold">{{ number_format($w['breakdown'], 2) }}%</td>
                                    <td class="font-monospace">{{ $w['mttr'] }}</td>
                                    <td><span class="badge bg-soft-danger text-danger">{{ $w['freq'] }}x</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-2">Belum ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Module 4: Shift Comparison & Worst Machine Trend --}}
            <div class="col-xl-4 col-lg-12">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header">
                        <span>4. KOMPARASI SHIFT & TREND DOWNTIME</span>
                        <span class="badge bg-light text-dark fs-10">Shift A, B, C</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2 mb-3 text-center">
                            <div class="col-4">
                                <div class="p-2 border rounded bg-light">
                                    <div class="fs-10 text-muted fw-bold">SHIFT A</div>
                                    <div class="fs-16 fw-bold text-primary font-monospace">{{ $shiftA }} m</div>
                                    <div class="fs-10 text-muted">{{ $totalDowntime > 0 ? round(($shiftA / $totalDowntime) * 100, 1) : 0 }}% DT</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded bg-light">
                                    <div class="fs-10 text-muted fw-bold">SHIFT B</div>
                                    <div class="fs-16 fw-bold text-warning font-monospace">{{ $shiftB }} m</div>
                                    <div class="fs-10 text-muted">{{ $totalDowntime > 0 ? round(($shiftB / $totalDowntime) * 100, 1) : 0 }}% DT</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded bg-light">
                                    <div class="fs-10 text-muted fw-bold">SHIFT C</div>
                                    <div class="fs-16 fw-bold text-success font-monospace">{{ $shiftC }} m</div>
                                    <div class="fs-10 text-muted">{{ $totalDowntime > 0 ? round(($shiftC / $totalDowntime) * 100, 1) : 0 }}% DT</div>
                                </div>
                            </div>
                        </div>
                        <div id="chartBreakdownTrend" style="min-height: 200px;"></div>
                        <div class="text-muted fs-11 mt-1 text-center">* Distribusi downtime per shift pada Top 4 Worst Mesin</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: 5. Pareto Kerusakan (Jenis DT), 6. Reliability Quadrant, 7. Klasifikasi E/M --}}
        <div class="row g-3 mb-4">
            {{-- Module 5: Pareto Analisis Jenis DT --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header header-blue">
                        <span>5. PARETO ANALISIS: TOP JENIS KERUSAKAN (DT)</span>
                        <i class="ri-bar-chart-grouped-line"></i>
                    </div>
                    <div class="card-body p-3">
                        <div id="chartParetoJenisDt" style="min-height: 280px;"></div>
                        <div class="text-muted fs-11 text-center mt-1">Mengidentifikasi 80% downtime disumbang oleh kategori kerusakan apa saja</div>
                    </div>
                </div>
            </div>

            {{-- Module 6: Reliability Quadrant --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header">
                        <span>6. RELIABILITY QUADRANT (MTTR vs MTBF)</span>
                        <i class="ri-bubble-chart-line text-warning"></i>
                    </div>
                    <div class="card-body p-3">
                        <div id="chartReliabilityQuadrant" style="min-height: 280px;"></div>
                        <div class="d-flex justify-content-center gap-3 fs-11 mt-2">
                            <span><span class="status-dot dot-red me-1"></span> Slow Repair (MTTR &gt; 30m)</span>
                            <span><span class="status-dot dot-yellow me-1"></span> Low MTBF (Fast Stop)</span>
                            <span><span class="status-dot dot-green me-1"></span> Ideal / Stable</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Module 7: Klasifikasi Electrical & Mechanical --}}
            <div class="col-xl-4 col-lg-12">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header">
                        <span>7. KLASIFIKASI ELECTRICAL VS MECHANICAL</span>
                        <i class="ri-flashlight-line text-info"></i>
                    </div>
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="row g-2 mb-3">
                            {{-- Electrical Card --}}
                            <div class="col-6">
                                <div class="p-3 rounded-3 border" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-color: #bfdbfe !important;">
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
                                <div class="p-3 rounded-3 border" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); border-color: #e9d5ff !important;">
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
document.addEventListener("DOMContentLoaded", function () {
    // 1. Machine Performance Map (Scatter)
    const perfMapOptions = {
        series: @json($chartPerfMapSeries),
        chart: {
            type: 'scatter',
            height: 280,
            toolbar: { show: false }
        },
        colors: ['#f59e0b', '#ef4444', '#22c55e'],
        xaxis: {
            title: { text: 'MTBF (MENIT OPERASI)', style: { fontSize: '11px' } },
            min: 0,
            labels: { formatter: val => Math.round(val) }
        },
        yaxis: {
            title: { text: 'BREAKDOWN RATE (%)', style: { fontSize: '11px' } },
            min: 0
        },
        grid: { borderColor: '#f1f5f9' },
        markers: { size: 8 }
    };
    new ApexCharts(document.querySelector("#chartPerformanceMap"), perfMapOptions).render();

    // 4. Breakdown Trend across Shifts on Worst Machines
    const trendOptions = {
        series: @json($chartTrendSeries),
        chart: {
            type: 'bar',
            height: 200,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 4
            }
        },
        colors: ['#ef4444', '#f97316', '#eab308', '#3b82f6'],
        xaxis: { categories: @json($trendCategories) },
        yaxis: { title: { text: 'Downtime (Menit)', style: { fontSize: '11px' } } },
        grid: { borderColor: '#f1f5f9' }
    };
    new ApexCharts(document.querySelector("#chartBreakdownTrend"), trendOptions).render();

    // 5. Pareto Analisis Jenis DT (Column + Line)
    const paretoOptions = {
        series: [{
            name: 'Durasi DT (Menit)',
            type: 'column',
            data: @json($paretoDurations)
        }, {
            name: 'Kumulatif %',
            type: 'line',
            data: @json($paretoCumulative)
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
            categories: @json($paretoCategories),
            labels: { rotate: -35, style: { fontSize: '10px' }, trim: true }
        },
        yaxis: [
            { title: { text: 'Downtime (Menit)', style: { fontSize: '11px' } } },
            { opposite: true, max: 100, title: { text: 'Kumulatif %', style: { fontSize: '11px' } } }
        ]
    };
    new ApexCharts(document.querySelector("#chartParetoJenisDt"), paretoOptions).render();

    // 6. Reliability Quadrant (MTTR vs MTBF)
    const relOptions = {
        series: @json($chartReliabilitySeries),
        chart: {
            type: 'scatter',
            height: 280,
            toolbar: { show: false }
        },
        colors: ['#ef4444', '#f59e0b', '#22c55e'],
        xaxis: {
            title: { text: 'MTBF (MENIT OPERASI)', style: { fontSize: '11px' } },
            min: 0,
            labels: { formatter: val => Math.round(val) }
        },
        yaxis: {
            title: { text: 'MTTR (MENIT PERBAIKAN)', style: { fontSize: '11px' } },
            min: 0
        },
        grid: { borderColor: '#f1f5f9' },
        markers: { size: 8 }
    };
    new ApexCharts(document.querySelector("#chartReliabilityQuadrant"), relOptions).render();
});

function changeKpiFilter() {
    const month = document.getElementById('kpi-month').value;
    const dateEl = document.getElementById('kpi-date');
    const date = dateEl ? dateEl.value : '';
    let url = `{{ route('epr.dashboard.cm') }}?month=${month}`;
    if (date) {
        url += `&date=${date}`;
    }
    window.location.href = url;
}
</script>
@endsection
