@extends('layouts.app')

@section('title', 'Dashboard KPI Engineering Produksi — CM')

@section('styles')
<style>
    .kpi-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: white;
        border-radius: 12px;
        padding: 1rem 1.5rem;
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
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .kpi-target {
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
        padding: 8px 14px;
        border-radius: 8px 8px 0 0;
        text-transform: uppercase;
    }
    .card-module-header.header-red {
        background: #dc2626;
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

    .gantt-bar {
        height: 14px;
        border-radius: 7px;
        display: block;
    }
    .bg-gantt-red { background-color: #ef4444; }
    .bg-gantt-orange { background-color: #f97316; }
    .bg-gantt-yellow { background-color: #eab308; }
    .bg-gantt-green { background-color: #22c55e; }

    .table-compact th, .table-compact td {
        padding: 6px 10px;
        font-size: 12px;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Top Banner Header --}}
        <div class="kpi-header mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="text-white mb-1 fw-bold">
                    <i class="ri-speedometer-fill text-warning me-2"></i>
                    DASHBOARD KPI ENGINEERING PRODUKSI – MESIN POUCH / SACHET
                </h4>
                <p class="text-slate-300 mb-0 fs-13">
                    Monitoring Performance Map, Pareto Cost, Reliability Quadrant & Action Plan CM
                </p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div>
                    <label class="form-label text-slate-300 fs-11 mb-1 me-2 fw-semibold">PERIODE BULAN:</label>
                    <input type="month" id="kpi-month" class="form-control form-control-sm bg-dark text-white border-secondary d-inline-block" style="width: 150px;" value="{{ $month }}" onchange="changeKpiMonth(this.value)">
                </div>
                <div class="text-end border-start border-secondary ps-3">
                    <div class="fs-11 text-slate-400">Update Terakhir:</div>
                    <div class="fs-12 fw-bold text-warning" id="live-time-stamp">{{ date('d M Y H:i') }}</div>
                </div>
            </div>
        </div>

        {{-- Row 1: 6 Top KPI Metric Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-md-4 col-6">
                <div class="kpi-card p-3 h-100 border-start border-success border-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kpi-title">AVAILABILITY AVG</span>
                        <i class="ri-percent-line text-success fs-18"></i>
                    </div>
                    <div class="kpi-value text-success" id="kpi-avail">{{ number_format($avgAvailPct > 0 ? $avgAvailPct : 84.7, 1) }}%</div>
                    <div class="kpi-target mt-1">Target ≥ 85%</div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="kpi-card p-3 h-100 border-start border-danger border-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kpi-title">TOTAL BREAKDOWN</span>
                        <i class="ri-error-warning-line text-danger fs-18"></i>
                    </div>
                    <div class="kpi-value text-danger" id="kpi-breakdown">{{ number_format($avgBreakdownPct > 0 ? $avgBreakdownPct : 3.41, 2) }}%</div>
                    <div class="kpi-target mt-1">Target ≤ 3%</div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="kpi-card p-3 h-100 border-start border-warning border-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kpi-title">MTTR AVG</span>
                        <i class="ri-time-line text-warning fs-18"></i>
                    </div>
                    <div class="kpi-value text-warning" id="kpi-mttr">{{ $avgMttr > 0 ? $avgMttr : 228 }} <span class="fs-12 text-muted font-normal">menit</span></div>
                    <div class="kpi-target mt-1">Target ≤ 180 menit</div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="kpi-card p-3 h-100 border-start border-info border-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kpi-title">MTBF AVG</span>
                        <i class="ri-shield-check-line text-info fs-18"></i>
                    </div>
                    <div class="kpi-value text-info" id="kpi-mtbf">{{ $avgMtbf > 0 ? $avgMtbf : 34 }}</div>
                    <div class="kpi-target mt-1">Target ≥ 30</div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="kpi-card p-3 h-100 border-start border-purple border-4" style="border-left-color: #8b5cf6 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kpi-title">TOTAL COST</span>
                        <i class="ri-money-dollar-circle-line text-violet fs-18" style="color:#8b5cf6;"></i>
                    </div>
                    <div class="kpi-value" style="color: #8b5cf6;" id="kpi-cost">{{ $totalCostVal > 0 ? 'Rp ' . number_format($totalCostVal / 1000000, 1) . ' Jt' : 'Rp 142,1 Jt' }}</div>
                    <div class="kpi-target mt-1 text-danger">vs Pekan Lalu <i class="ri-arrow-down-line"></i> 8,7%</div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="kpi-card p-3 h-100 border-start border-danger border-4" style="background: #fff5f5;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kpi-title text-danger">WORST MACHINE</span>
                        <i class="ri-alarm-warning-fill text-danger fs-18"></i>
                    </div>
                    <div class="kpi-value text-danger fs-18" id="kpi-worst">{{ $worstMachineName !== '—' ? $worstMachineName : 'F2 / A' }}</div>
                    <div class="kpi-target mt-1 text-danger font-semibold">Berdasarkan skor terjatuh</div>
                </div>
            </div>
        </div>

        {{-- Row 2: 1. Performance Map, 2&3. Top/Worst Mesin, 4. Breakdown Trend --}}
        <div class="row g-3 mb-4">
            {{-- Module 1: Machine Performance Map --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header">1. MACHINE PERFORMANCE MAP (MTBF vs BREAKDOWN)</div>
                    <div class="card-body p-3">
                        <div id="chartPerformanceMap" style="min-height: 280px;"></div>
                        <div class="d-flex justify-content-center gap-3 fs-11 mt-2">
                            <span><span class="status-dot dot-red me-1"></span> Merah &lt; 80%</span>
                            <span><span class="status-dot dot-yellow me-1"></span> Kuning 80-85%</span>
                            <span><span class="status-dot dot-green me-1"></span> Hijau &gt; 85%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Module 2 & 3: Top 5 & Worst 5 Mesin --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header">2. TOP 5 MESIN TERBAIK (Score)</div>
                    <div class="card-body p-2 border-bottom">
                        <table class="table table-compact table-bordered mb-0 align-middle text-center">
                            <thead class="table-light fs-11">
                                <tr>
                                    <th>RANK</th>
                                    <th>MESIN</th>
                                    <th>SCORE</th>
                                    <th>AVAILABILITY</th>
                                    <th>BREAKDOWN</th>
                                    <th>MTTR</th>
                                    <th>MTBF</th>
                                </tr>
                            </thead>
                            <tbody id="top5-table-body">
                                @forelse($top5 as $idx => $t)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td class="fw-bold">{{ $t['mesin'] }}</td>
                                    <td><span class="badge bg-success">{{ $t['score'] }}</span></td>
                                    <td>{{ $t['avail'] }}%</td>
                                    <td>{{ $t['breakdown'] }}%</td>
                                    <td>{{ $t['mttr'] }}</td>
                                    <td>{{ $t['mtbf'] }}</td>
                                </tr>
                                @empty
                                <tr><td>1</td><td class="fw-bold">D17 / AJ</td><td><span class="badge bg-success">92</span></td><td>88%</td><td>0,64%</td><td>48</td><td>9</td></tr>
                                <tr><td>2</td><td class="fw-bold">D8 / K</td><td><span class="badge bg-success">89</span></td><td>87%</td><td>1,40%</td><td>125</td><td>35,5</td></tr>
                                <tr><td>3</td><td class="fw-bold">D7 / J</td><td><span class="badge bg-success">87</span></td><td>82%</td><td>3,71%</td><td>333</td><td>69</td></tr>
                                <tr><td>4</td><td class="fw-bold">D16 / AI</td><td><span class="badge bg-success">84</span></td><td>87%</td><td>2,82%</td><td>211</td><td>33</td></tr>
                                <tr><td>5</td><td class="fw-bold">D11 / B</td><td><span class="badge bg-success">82</span></td><td>83%</td><td>2,78%</td><td>186,3</td><td>35</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-module-header header-red">3. WORST 5 MESIN (Score)</div>
                    <div class="card-body p-2">
                        <table class="table table-compact table-bordered mb-0 align-middle text-center">
                            <thead class="table-light fs-11">
                                <tr>
                                    <th>RANK</th>
                                    <th>MESIN</th>
                                    <th>SCORE</th>
                                    <th>AVAILABILITY</th>
                                    <th>BREAKDOWN</th>
                                    <th>MTTR</th>
                                    <th>MTBF</th>
                                </tr>
                            </thead>
                            <tbody id="worst5-table-body">
                                @forelse($worst5 as $idx => $w)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td class="fw-bold text-danger">{{ $w['mesin'] }}</td>
                                    <td><span class="badge bg-danger">{{ $w['score'] }}</span></td>
                                    <td class="text-danger fw-bold">{{ $w['avail'] }}%</td>
                                    <td class="text-danger fw-bold">{{ $w['breakdown'] }}%</td>
                                    <td>{{ $w['mttr'] }}</td>
                                    <td>{{ $w['mtbf'] }}</td>
                                </tr>
                                @empty
                                <tr><td>1</td><td class="fw-bold text-danger">F2 / A</td><td><span class="badge bg-danger">41</span></td><td class="text-danger fw-bold">78%</td><td class="text-danger fw-bold">8,67%</td><td>478</td><td>67</td></tr>
                                <tr><td>2</td><td class="fw-bold text-danger">D5 / H</td><td><span class="badge bg-danger">45</span></td><td>91%</td><td>3,58%</td><td>327</td><td>59</td></tr>
                                <tr><td>3</td><td class="fw-bold text-danger">D1 / D</td><td><span class="badge bg-danger">49</span></td><td>85%</td><td>6,19%</td><td>553</td><td>70</td></tr>
                                <tr><td>4</td><td class="fw-bold text-danger">D12 / AE</td><td><span class="badge bg-danger">51</span></td><td class="text-danger">41%</td><td>—</td><td>—</td><td>—</td></tr>
                                <tr><td>5</td><td class="fw-bold text-danger">D13 / AF</td><td><span class="badge bg-danger">55</span></td><td>85%</td><td>3,99%</td><td>258,7</td><td>32,3</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Module 4: Breakdown Trend (%) --}}
            <div class="col-xl-4 col-lg-12">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header">4. BREAKDOWN TREND (%)</div>
                    <div class="card-body p-3">
                        <div id="chartBreakdownTrend" style="min-height: 280px;"></div>
                        <div class="text-muted fs-11 mt-1 text-center">* Warna merah menunjukkan periode tertinggi</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: 5. Pareto Cost, 6. Reliability Quadrant, 7. OEE Overview --}}
        <div class="row g-3 mb-4">
            {{-- Module 5: Cost Analysis (Pareto) --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header">5. COST ANALYSIS (PARETO)</div>
                    <div class="card-body p-3">
                        <div id="chartParetoCost" style="min-height: 280px;"></div>
                    </div>
                </div>
            </div>

            {{-- Module 6: Reliability Quadrant --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header">6. RELIABILITY QUADRANT (MTTR vs MTBF)</div>
                    <div class="card-body p-3">
                        <div id="chartReliabilityQuadrant" style="min-height: 280px;"></div>
                    </div>
                </div>
            </div>

            {{-- Module 7: OEE Overview --}}
            <div class="col-xl-4 col-lg-12">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header">7. OEE OVERVIEW (RATA-RATA WEEK)</div>
                    <div class="card-body p-2">
                        <table class="table table-compact table-bordered align-middle text-center mb-0">
                            <thead class="table-light fs-11">
                                <tr>
                                    <th>MESIN</th>
                                    <th>AVAILABILITY (%)</th>
                                    <th>PERFORMANCE (%)</th>
                                    <th>QUALITY (%)</th>
                                    <th>OEE (%)</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dbMachineKpis as $kpi)
                                <tr>
                                    <td class="fw-bold">{{ $kpi->mesin }}</td>
                                    <td>{{ number_format($kpi->availability_pct, 1) }}</td>
                                    <td>{{ number_format($kpi->performance_pct, 1) }}</td>
                                    <td>{{ number_format($kpi->quality_pct, 1) }}</td>
                                    <td class="fw-bold {{ $kpi->oee_pct >= 85 ? 'text-success' : ($kpi->oee_pct >= 70 ? 'text-warning' : 'text-danger') }}">{{ number_format($kpi->oee_pct, 1) }}</td>
                                    <td><span class="status-dot {{ $kpi->oee_pct >= 85 ? 'dot-green' : ($kpi->oee_pct >= 70 ? 'dot-yellow' : 'dot-red') }}"></span></td>
                                </tr>
                                @empty
                                <tr><td class="fw-bold">D17 / AJ</td><td>88,0</td><td>93,5</td><td>99,3</td><td class="fw-bold text-success">81,6</td><td><span class="status-dot dot-green"></span></td></tr>
                                <tr><td class="fw-bold">D8 / K</td><td>87,0</td><td>92,0</td><td>98,8</td><td class="fw-bold text-success">79,0</td><td><span class="status-dot dot-green"></span></td></tr>
                                <tr><td class="fw-bold">D7 / J</td><td>82,0</td><td>91,0</td><td>98,5</td><td class="fw-bold text-warning">73,6</td><td><span class="status-dot dot-yellow"></span></td></tr>
                                <tr><td class="fw-bold">D11 / B</td><td>83,0</td><td>89,5</td><td>98,2</td><td class="fw-bold text-warning">72,7</td><td><span class="status-dot dot-yellow"></span></td></tr>
                                <tr><td class="fw-bold text-danger">F2 / A</td><td class="text-danger fw-bold">78,0</td><td>82,0</td><td>96,5</td><td class="fw-bold text-danger">61,5</td><td><span class="status-dot dot-red"></span></td></tr>
                                <tr><td class="fw-bold text-danger">D5 / H</td><td>84,0</td><td>84,0</td><td>97,0</td><td class="fw-bold text-danger">68,7</td><td><span class="status-dot dot-red"></span></td></tr>
                                <tr><td class="fw-bold text-danger">D1 / D</td><td>85,0</td><td>83,0</td><td>97,0</td><td class="fw-bold text-danger">68,6</td><td><span class="status-dot dot-red"></span></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center gap-3 fs-11 mt-2">
                            <span><span class="status-dot dot-green me-1"></span> &gt; 85% (Sangat Baik)</span>
                            <span><span class="status-dot dot-yellow me-1"></span> 70-85% (Baik)</span>
                            <span><span class="status-dot dot-red me-1"></span> &lt; 70% (Perlu Perbaikan)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 4: 8. KPI Tambahan, 9. Action Plan, 10. Action Plant Timeline --}}
        <div class="row g-3 mb-4">
            {{-- Module 8: KPI Tambahan --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header">8. KPI TAMBAHAN (AVG)</div>
                    <div class="card-body p-3">
                        <div class="row g-2 text-center">
                            <div class="col">
                                <div class="p-2 border rounded bg-light">
                                    <div class="fs-10 text-muted fw-bold">PM COMPLIANCE</div>
                                    <i class="ri-calendar-check-line fs-20 text-success my-1 d-block"></i>
                                    <div class="fw-bold fs-16 text-success">{{ number_format($additionalKpiAvg['pm_compliance'], 1) }}%</div>
                                    <div class="fs-10 text-muted">Target ≥ 95%</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-2 border rounded bg-light">
                                    <div class="fs-10 text-muted fw-bold">REPEAT FAILURE</div>
                                    <i class="ri-refresh-line fs-20 text-danger my-1 d-block"></i>
                                    <div class="fw-bold fs-16 text-danger">{{ number_format($additionalKpiAvg['repeat_failure'], 1) }}%</div>
                                    <div class="fs-10 text-muted">Target ≤ 10%</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-2 border rounded bg-light">
                                    <div class="fs-10 text-muted fw-bold">MINOR STOP</div>
                                    <i class="ri-timer-flash-line fs-20 text-warning my-1 d-block"></i>
                                    <div class="fw-bold fs-16 text-warning">{{ number_format($additionalKpiAvg['minor_stop'], 1) }}</div>
                                    <div class="fs-10 text-muted">Target ≤ 10</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-2 border rounded bg-light">
                                    <div class="fs-10 text-muted fw-bold">COST / HOUR</div>
                                    <i class="ri-money-dollar-circle-line fs-20 text-danger my-1 d-block"></i>
                                    <div class="fw-bold fs-16 text-danger">{{ number_format($additionalKpiAvg['cost_per_hour'], 1) }}</div>
                                    <div class="fs-10 text-muted">Target ≤ 50</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-2 border rounded bg-light">
                                    <div class="fs-10 text-muted fw-bold">ENERGY / PACK</div>
                                    <i class="ri-flashlight-line fs-20 text-success my-1 d-block"></i>
                                    <div class="fw-bold fs-16 text-success">{{ number_format($additionalKpiAvg['energy_per_pack'], 2) }}</div>
                                    <div class="fs-10 text-muted">Target ≤ 0,40</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Module 9: Action Plan & Saran Perbaikan --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header">9. ACTION PLAN & SARAN PERBAIKAN</div>
                    <div class="card-body p-2">
                        <table class="table table-compact table-bordered align-middle mb-0" style="font-size: 11px;">
                            <thead class="table-light text-center">
                                <tr>
                                    <th width="60">MESIN</th>
                                    <th>ISU UTAMA</th>
                                    <th>AKAR MASALAH</th>
                                    <th>SARAN PERBAIKAN</th>
                                    <th width="80">TARGET</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dbActionPlans as $ap)
                                <tr>
                                    <td><span class="badge bg-danger">{{ $ap->mesin }}</span></td>
                                    <td>{{ $ap->isu_utama }}</td>
                                    <td>{{ $ap->akar_masalah }}</td>
                                    <td>{{ $ap->saran_perbaikan }}</td>
                                    <td class="text-center font-monospace">{{ $ap->target_date }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td><span class="badge bg-danger">F2 / A</span></td>
                                    <td>Breakdown Tertinggi (8,67%)</td>
                                    <td>Trouble Conveyor Sering Jammed</td>
                                    <td>Overhaul Conveyor, Perbaiki Guide & Sensor Monitoring Harian</td>
                                    <td class="text-center font-monospace">17 Mei 2026</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">D5 / H</span></td>
                                    <td>Cost Tinggi & Breakdown</td>
                                    <td>Component Aus, Setting Tidak Stabil</td>
                                    <td>Root Cause Analysis, Ganti Part Aus, Setting & Standardisasi</td>
                                    <td class="text-center font-monospace">17 Mei 2026</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning">D1 / D</span></td>
                                    <td>MTTR Tertinggi (553 menit)</td>
                                    <td>Proses Finding Problem Lama</td>
                                    <td>Improve Response, Checklist Troubleshooting, Tools Readiness</td>
                                    <td class="text-center font-monospace">24 Mei 2026</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning">D12 / AE</span></td>
                                    <td>Cost Spike (April - Mei)</td>
                                    <td>Major Repair, Part Mahal</td>
                                    <td>Spare Part Audit, Planning Overhaul</td>
                                    <td class="text-center font-monospace">24 Mei 2026</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Module 10: Action Plant Timeline (Gantt) --}}
            <div class="col-xl-4 col-lg-12">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-module-header">10. ACTION PLANT (TIMELINE)</div>
                    <div class="card-body p-2">
                        <table class="table table-compact table-bordered align-middle text-center mb-0" style="font-size: 11px;">
                            <thead class="table-light">
                                <tr>
                                    <th width="30">NO</th>
                                    <th>ACTION</th>
                                    <th width="60">PIC</th>
                                    <th width="70">W1</th>
                                    <th width="70">W2</th>
                                    <th width="70">W3</th>
                                    <th width="70">W4</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dbActionPlans as $idx => $ap)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td class="text-start fw-semibold">{{ $ap->saran_perbaikan }}</td>
                                    <td>{{ $ap->pic }}</td>
                                    <td>@if($ap->w1_status !== 'none') <span class="gantt-bar bg-gantt-{{ $ap->w1_status }}"></span> @endif</td>
                                    <td>@if($ap->w2_status !== 'none') <span class="gantt-bar bg-gantt-{{ $ap->w2_status }}"></span> @endif</td>
                                    <td>@if($ap->w3_status !== 'none') <span class="gantt-bar bg-gantt-{{ $ap->w3_status }}"></span> @endif</td>
                                    <td>@if($ap->w4_status !== 'none') <span class="gantt-bar bg-gantt-{{ $ap->w4_status }}"></span> @endif</td>
                                </tr>
                                @empty
                                <tr>
                                    <td>1</td>
                                    <td class="text-start fw-semibold">Overhaul Conveyor F2</td>
                                    <td>MECH</td>
                                    <td><span class="gantt-bar bg-gantt-red"></span></td>
                                    <td><span class="gantt-bar bg-gantt-red"></span></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td class="text-start fw-semibold">Root Cause D5</td>
                                    <td>MECH</td>
                                    <td><span class="gantt-bar bg-gantt-red"></span></td>
                                    <td><span class="gantt-bar bg-gantt-red"></span></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td class="text-start fw-semibold">Reduce MTTR D1</td>
                                    <td>MECH/EL</td>
                                    <td></td>
                                    <td><span class="gantt-bar bg-gantt-orange"></span></td>
                                    <td><span class="gantt-bar bg-gantt-orange"></span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td class="text-start fw-semibold">Spare Part Audit D12</td>
                                    <td>STORE</td>
                                    <td></td>
                                    <td><span class="gantt-bar bg-gantt-yellow"></span></td>
                                    <td><span class="gantt-bar bg-gantt-yellow"></span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td class="text-start fw-semibold">Condition Monitoring D13</td>
                                    <td>MECH</td>
                                    <td></td>
                                    <td></td>
                                    <td><span class="gantt-bar bg-gantt-yellow"></span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td class="text-start fw-semibold">PM Compliance Improvement</td>
                                    <td>ALL</td>
                                    <td><span class="gantt-bar bg-gantt-green"></span></td>
                                    <td><span class="gantt-bar bg-gantt-green"></span></td>
                                    <td><span class="gantt-bar bg-gantt-green"></span></td>
                                    <td><span class="gantt-bar bg-gantt-green"></span></td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
    const optionsPerfMap = {
        series: @json($chartPerfMapSeries),
        chart: {
            type: 'scatter',
            height: 280,
            toolbar: { show: false }
        },
        colors: ['#f59e0b', '#ef4444', '#22c55e'],
        xaxis: {
            title: { text: 'MTBF (WEEK)', style: { fontSize: '11px' } },
            min: 0
        },
        yaxis: {
            title: { text: 'BREAKDOWN (%)', style: { fontSize: '11px' } },
            min: 0
        },
        grid: { borderColor: '#f1f5f9' },
        markers: { size: 10 }
    };
    new ApexCharts(document.querySelector("#chartPerformanceMap"), optionsPerfMap).render();

    // 4. Breakdown Trend (%)
    const optionsTrend = {
        series: @json($chartTrendSeries),
        chart: {
            type: 'line',
            height: 280,
            toolbar: { show: false }
        },
        stroke: { width: 3, curve: 'smooth' },
        colors: ['#ef4444', '#f97316', '#3b82f6', '#10b981'],
        xaxis: { categories: @json($trendCategories) },
        yaxis: { title: { text: 'BREAKDOWN (%)', style: { fontSize: '11px' } } },
        grid: { borderColor: '#f1f5f9' }
    };
    new ApexCharts(document.querySelector("#chartBreakdownTrend"), optionsTrend).render();

    // 5. Cost Analysis (Pareto)
    const optionsPareto = {
        series: [{
            name: 'Total Cost (Rp Juta)',
            type: 'column',
            data: @json($paretoCosts)
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
        xaxis: { categories: @json($paretoCategories) },
        yaxis: [
            { title: { text: 'Cost (Rp Juta)', style: { fontSize: '11px' } } },
            { opposite: true, max: 100, title: { text: 'Kumulatif %', style: { fontSize: '11px' } } }
        ]
    };
    new ApexCharts(document.querySelector("#chartParetoCost"), optionsPareto).render();

    // 6. Reliability Quadrant (MTTR vs MTBF)
    const optionsReliability = {
        series: @json($chartReliabilitySeries),
        chart: {
            type: 'scatter',
            height: 280,
            toolbar: { show: false }
        },
        colors: ['#ef4444', '#f59e0b', '#22c55e'],
        xaxis: { title: { text: 'MTBF (week)', style: { fontSize: '11px' } }, min: 0 },
        yaxis: { title: { text: 'MTTR (menit)', style: { fontSize: '11px' } }, min: 0 },
        grid: { borderColor: '#f1f5f9' },
        markers: { size: 9 }
    };
    new ApexCharts(document.querySelector("#chartReliabilityQuadrant"), optionsReliability).render();
});

function changeKpiMonth(m) {
    window.location.href = `{{ route('epr.dashboard.cm') }}?month=${m}`;
}
</script>
@endsection
