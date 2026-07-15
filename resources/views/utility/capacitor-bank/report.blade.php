@extends('layouts.app')

@section('title', 'Capacitor Bank — Automated Report')

@section('styles')
    <style>
        /* ── Animations ── */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }

        /* ── Main Layout Styles ── */
        .report-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            border-radius: 16px;
            padding: 24px 32px;
            margin-bottom: 24px;
            color: #fff;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .report-header::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            right: -80px;
            top: -80px;
            pointer-events: none;
        }

        .report-header h4 {
            font-weight: 800;
            letter-spacing: -0.025em;
            margin: 0;
            font-size: 1.5rem;
        }

        .report-header p {
            opacity: 0.75;
            margin: 4px 0 0 0;
            font-size: 0.875rem;
        }

        /* ── Filters Card ── */
        .filter-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 14px;
            padding: 18px 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }

        [data-layout-mode="dark"] .filter-card {
            background: rgba(30, 41, 59, 0.8);
            border-color: rgba(51, 65, 85, 0.8);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* ── Glassmorphic Summary Cards ── */
        .stat-card {
            border-radius: 14px;
            padding: 20px;
            background: #fff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        [data-layout-mode="dark"] .stat-card {
            background: #1e293b;
            border-color: #334155;
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 12px;
            background: rgba(99, 102, 241, 0.08);
            color: #6366f1;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        [data-layout-mode="dark"] .stat-value {
            color: #f8fafc;
        }

        .stat-label {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.05em;
            margin-top: 4px;
        }

        [data-layout-mode="dark"] .stat-label {
            color: #94a3b8;
        }

        .stat-meta {
            font-size: 0.72rem;
            color: #94a3b8;
            margin-top: 8px;
            border-top: 1px dashed #e2e8f0;
            padding-top: 6px;
        }

        [data-layout-mode="dark"] .stat-meta {
            border-color: #334155;
            color: #64748b;
        }

        /* Card Accent Colors */
        .accent-blue .stat-icon {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .accent-cyan .stat-icon {
            background: rgba(6, 182, 212, 0.1);
            color: #06b6d4;
        }

        .accent-emerald .stat-icon {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .accent-amber .stat-icon {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .accent-rose .stat-icon {
            background: rgba(244, 63, 94, 0.1);
            color: #f43f5e;
        }

        .accent-indigo .stat-icon {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
        }

        /* ── Chart Card ── */
        .chart-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            height: calc(100% - 24px);
        }

        [data-layout-mode="dark"] .chart-card {
            background: #1e293b;
            border-color: #334155;
        }

        .chart-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .chart-card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        [data-layout-mode="dark"] .chart-card-title {
            color: #f8fafc;
        }

        /* ── Custom Scrollbar ── */
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        [data-layout-mode="dark"] .custom-scroll::-webkit-scrollbar-thumb {
            background: #475569;
        }

        /* ── Capacitor Grid ── */
        .cap-grid-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            transition: all 0.2s ease;
        }

        [data-layout-mode="dark"] .cap-grid-item {
            background: #1e293b;
            border-color: #334155;
        }

        .cap-grid-item.active-cap {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.05);
        }

        .cap-grid-item .cap-name {
            font-weight: 700;
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 4px;
        }

        [data-layout-mode="dark"] .cap-grid-item .cap-name {
            color: #94a3b8;
        }

        .cap-grid-item.active-cap .cap-name {
            color: #10b981;
        }

        .cap-grid-item .cap-count {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
        }

        [data-layout-mode="dark"] .cap-grid-item .cap-count {
            color: #f8fafc;
        }

        /* ── Timeline Section ── */
        .timeline-container {
            flex: 1;
            overflow-y: auto;
            padding-right: 8px;
        }

        @media (min-width: 992px) {
            .chart-card-equal {
                height: 540px !important;
            }
        }

        .timeline-event {
            position: relative;
            padding-left: 24px;
            margin-bottom: 16px;
            border-left: 2px solid #e2e8f0;
        }

        [data-layout-mode="dark"] .timeline-event {
            border-color: #334155;
        }

        .timeline-event::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 4px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #cbd5e1;
            border: 2px solid #fff;
        }

        [data-layout-mode="dark"] .timeline-event::before {
            border-color: #1e293b;
            background: #475569;
        }

        .timeline-event.event-on::before {
            background: #10b981;
        }

        .timeline-event.event-off::before {
            background: #ef4444;
        }

        .timeline-time {
            font-size: 0.72rem;
            font-weight: 700;
            color: #94a3b8;
        }

        .timeline-text {
            font-size: 0.8rem;
            color: #334155;
            margin-top: 2px;
        }

        [data-layout-mode="dark"] .timeline-text {
            color: #cbd5e1;
        }

        /* ── Table Card Design ── */
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        [data-layout-mode="dark"] .table-responsive {
            border-color: #334155;
        }

        .table thead th {
            background-color: #f1f5f9;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px;
        }

        [data-layout-mode="dark"] .table thead th {
            background-color: #1e293b;
            color: #cbd5e1;
            border-color: #334155;
        }

        .table tbody td {
            font-size: 0.82rem;
            padding: 10px 12px;
            color: #334155;
            vertical-align: middle;
        }

        [data-layout-mode="dark"] .table tbody td {
            color: #cbd5e1;
            border-color: #334155;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        [data-layout-mode="dark"] .table tbody tr:hover {
            background-color: #1e293b;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 14px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        [data-layout-mode="dark"] .loading-overlay {
            background: rgba(15, 23, 42, 0.7);
        }

        .loading-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- ── Header ── --}}
            <div class="report-header fade-in-up">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h4><i class="mdi mdi-chart-bell-curve-cumulative me-2"></i>Report Capacitor Bank - Auto IoT</h4>
                        <p>Dashboard telemetry capacitor bank yang diupdate otomatis setiap 30 menit</p>
                    </div>
                    {{-- <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('capacitor-bank.index') }}"
                            class="btn btn-sm btn-outline-light rounded-pill px-3">
                            <i class="bx bx-edit me-1"></i>Input Harian (Manual)
                        </a>
                        <a href="{{ route('capacitor-bank.rekap') }}"
                            class="btn btn-sm btn-outline-light rounded-pill px-3">
                            <i class="bx bx-history me-1"></i>Rekap Manual
                        </a>
                    </div> --}}
                </div>
            </div>

            {{-- ── Filter Card ── --}}
            <div class="filter-card fade-in-up" style="animation-delay: 0.1s;">
                <div class="row align-items-end g-3">
                    <div class="col-sm-4 col-md-3">
                        <label class="form-label fw-semibold" style="font-size: 0.8rem; color: #64748b;">Pilih Tanggal
                            Laporan</label>
                        <input type="date" id="inputTanggal" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary px-4" id="btnTampilkan">
                            <i class="bx bx-search me-1"></i>Tampilkan
                        </button>
                    </div>
                    <div class="col-auto ms-auto">
                        <button class="btn btn-outline-success" id="btnRefresh">
                            <i class="bx bx-refresh me-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── Dashboard Area with Overlay ── --}}
            <div class="position-relative">
                <div class="loading-overlay" id="reportLoading">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted fw-semibold">Memuat Data Telemetry...</p>
                    </div>
                </div>

                <div id="reportContent">
                    {{-- ── 1. Summary Cards ── --}}
                    <div class="row g-4 mb-4">
                        <!-- Avg Current -->
                        <div class="col-6 col-lg-2  ">
                            <div class="stat-card accent-blue">
                                <div class="stat-icon"><i class="mdi mdi-flash"></i></div>
                                <div class="stat-value" id="valCurrent">0 <span
                                        style="font-size: 0.9rem; font-weight: 500;">A</span></div>
                                <div class="stat-label">Arus Rata-rata</div>
                                <div class="stat-meta">Hari Ini</div>
                            </div>
                        </div>
                        <!-- Avg Voltage L-L -->
                        <div class="col-6 col-lg-2">
                            <div class="stat-card accent-cyan">
                                <div class="stat-icon"><i class="mdi mdi-lightning-bolt"></i></div>
                                <div class="stat-value" id="valVll">0 <span
                                        style="font-size: 0.9rem; font-weight: 500;">V</span></div>
                                <div class="stat-label">Voltase L-L</div>
                                <div class="stat-meta" id="subVll">Avg Vab / Vbc / Vca</div>
                            </div>
                        </div>
                        <!-- Avg Voltage L-N -->
                        <div class="col-6 col-lg-2">
                            <div class="stat-card accent-indigo">
                                <div class="stat-icon"><i class="mdi mdi-lightning-bolt-outline"></i></div>
                                <div class="stat-value" id="valVln">0 <span
                                        style="font-size: 0.9rem; font-weight: 500;">V</span></div>
                                <div class="stat-label">Voltase L-N</div>
                                <div class="stat-meta" id="subVln">Avg Van / Vbn / Vcn</div>
                            </div>
                        </div>
                        <!-- Avg Power -->
                        <div class="col-6 col-lg-2">
                            <div class="stat-card accent-emerald">
                                <div class="stat-icon"><i class="mdi mdi-gauge"></i></div>
                                <div class="stat-value" id="valPower">0 <span
                                        style="font-size: 0.9rem; font-weight: 500;">kW</span></div>
                                <div class="stat-label">Daya Aktif</div>
                                <div class="stat-meta" id="subPower">Ptot / Qtot / Stot</div>
                            </div>
                        </div>
                        <!-- Avg PF & Freq -->
                        <div class="col-6 col-lg-2">
                            <div class="stat-card accent-amber">
                                <div class="stat-icon"><i class="mdi mdi-speedometer"></i></div>
                                <div class="stat-value" id="valPF">0</div>
                                <div class="stat-label">Daya Factor</div>
                                <div class="stat-meta" id="subPF">Freq: 0 Hz</div>
                            </div>
                        </div>
                        <!-- Active Cap -->
                        <div class="col-6 col-lg-2">
                            <div class="stat-card accent-rose">
                                <div class="stat-icon"><i class="mdi mdi-radiobox-marked"></i></div>
                                <div class="stat-value" id="valLatestCap">-</div>
                                <div class="stat-label">Kapasitor Aktif</div>
                                <div class="stat-meta" id="subLatestCap">0 Kali Aktif</div>
                            </div>
                        </div>
                    </div>

                    {{-- ── 2. Trends Section (Current & LL Voltage) ── --}}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="chart-card">
                                <div class="chart-card-header">
                                    <h6 class="chart-card-title"><i class="bx bx-line-chart me-1 text-primary"></i> 2.
                                        Tren Arus (Current R-S-T)</h6>
                                    <span class="badge bg-primary-subtle text-primary">A</span>
                                </div>
                                <div id="chartCurrent"></div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="chart-card">
                                <div class="chart-card-header">
                                    <h6 class="chart-card-title"><i class="bx bx-line-chart me-1 text-info"></i> 3. Tren
                                        Tegangan Line-to-Line (L-L)</h6>
                                    <span class="badge bg-info-subtle text-info">V</span>
                                </div>
                                <div id="chartVll"></div>
                            </div>
                        </div>
                    </div>

                    {{-- ── 3. Trends Section (LN Voltage & Power) ── --}}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="chart-card">
                                <div class="chart-card-header">
                                    <h6 class="chart-card-title"><i class="bx bx-line-chart me-1 text-indigo"></i> 4. Tren
                                        Tegangan Line-to-Neutral (L-N)</h6>
                                    <span class="badge bg-indigo-subtle text-indigo">V</span>
                                </div>
                                <div id="chartVln"></div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="chart-card">
                                <div class="chart-card-header">
                                    <h6 class="chart-card-title"><i class="bx bx-area-chart me-1 text-emerald"></i> 5.
                                        Tren Power (P, Q, S)</h6>
                                    <span class="badge bg-emerald-subtle text-emerald">kW/kVAR/kVA</span>
                                </div>
                                <div id="chartPower"></div>
                            </div>
                        </div>
                    </div>

                    {{-- ── 4. Summary per Cap & Timeline transitions ── --}}
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="chart-card chart-card-equal">
                                <div class="chart-card-header">
                                    <h6 class="chart-card-title"><i class="bx bx-bar-chart-alt-2 me-1 text-warning"></i>
                                        6. Frekuensi Aktif per Kapasitor</h6>
                                    <span class="badge bg-warning-subtle text-warning">Frekuensi (Kali)</span>
                                </div>
                                <div class="mb-4">
                                    <div id="chartCapFreq"></div>
                                </div>
                                <h6 class="fw-bold mb-3" style="font-size: 0.85rem; color: #64748b;">Statistik Aktif per
                                    Kapasitor (Hari Ini)</h6>
                                <div class="row g-2" id="capGrid">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <div class="col-4 col-sm-3 col-md-2">
                                            <div class="cap-grid-item" id="capGridItem{{ $i }}">
                                                <div class="cap-name">CAP {{ $i }}</div>
                                                <div class="cap-count" id="capGridCount{{ $i }}">0</div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="chart-card chart-card-equal">
                                <div class="chart-card-header">
                                    <h6 class="chart-card-title"><i class="bx bx-calendar-event me-1 text-danger"></i> Log
                                        ON/OFF Transisi Kapasitor</h6>
                                    <span class="badge bg-danger-subtle text-danger" id="countTransitions">0 Event</span>
                                </div>
                                <div class="timeline-container custom-scroll" id="transitionsLog">
                                    <!-- Dynamic Timeline Items Go Here -->
                                    <div
                                        class="d-flex flex-column align-items-center justify-content-center h-100 text-center py-5 text-muted">
                                        <i class="bx bx-calendar-x fs-2"></i>
                                        <p class="mt-2 small mb-0">Tidak ada transisi hari ini</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── 5. Raw Data Table ── --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light border-0 d-flex align-items-center justify-content-between py-3">
                            <h6 class="fw-bold text-dark mb-0"><i class="bx bx-table me-1 text-secondary"></i> 7. Raw Data
                                Telemetry Tabel (Per 30 Menit)</h6>
                            <div class="d-flex align-items-center gap-2">
                                <input type="text" id="tableSearch" class="form-control form-control-sm"
                                    placeholder="Cari..." style="width: 180px;">
                                <button class="btn btn-sm btn-outline-secondary" id="btnExportCSV">
                                    <i class="bx bx-download me-1"></i>Export CSV
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive custom-scroll" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-hover table-striped mb-0" id="rawDataTable">
                                    <thead>
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Kapasitor Aktif</th>
                                            <th>Arus (A)</th>
                                            <th>Vab (V)</th>
                                            <th>Vbc (V)</th>
                                            <th>Vca (V)</th>
                                            <th>Van (V)</th>
                                            <th>Vbn (V)</th>
                                            <th>Vcn (V)</th>
                                            <th>Ptot (kW)</th>
                                            <th>Qtot (kVAR)</th>
                                            <th>Stot (kVA)</th>
                                            <th>PF (Avg)</th>
                                            <th>CosPhi (Avg)</th>
                                            <th>Freq (Hz)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rawTableBody">
                                        <tr>
                                            <td colspan="15" class="text-center py-4 text-muted">Memuat data...</td>
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
@endsection

@section('scripts')
    <!-- Highcharts JS CDN -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
        $(document).ready(function() {
            let chartInstances = {};
            let rawDataCache = [];

            // ── 1. Helper function for Highcharts baseline configurations ──
            function getHighchartsBaseOptions(type, categories) {
                const isDark = document.documentElement.getAttribute('data-layout-mode') === 'dark';
                const textColor = isDark ? '#cbd5e1' : '#64748b';
                const gridColor = isDark ? '#334155' : '#f1f5f9';
                const lineColor = isDark ? '#475569' : '#e2e8f0';

                return {
                    chart: {
                        type: type,
                        backgroundColor: 'transparent',
                        height: 320,
                        style: {
                            fontFamily: 'Inter, sans-serif'
                        }
                    },
                    title: {
                        text: null
                    },
                    credits: {
                        enabled: false
                    },
                    xAxis: {
                        categories: categories,
                        gridLineWidth: 0,
                        lineColor: lineColor,
                        tickColor: lineColor,
                        labels: {
                            style: {
                                color: textColor,
                                fontSize: '11px'
                            }
                        }
                    },
                    yAxis: {
                        title: {
                            text: null
                        },
                        gridLineColor: gridColor,
                        gridLineDashStyle: 'Dash',
                        labels: {
                            style: {
                                color: textColor,
                                fontSize: '11px'
                            }
                        }
                    },
                    legend: {
                        itemStyle: {
                            color: isDark ? '#cbd5e1' : '#475569',
                            fontSize: '11px'
                        }
                    },
                    tooltip: {
                        shared: true,
                        crosshairs: true,
                        backgroundColor: isDark ? '#1e293b' : '#ffffff',
                        style: {
                            color: isDark ? '#f8fafc' : '#0f172a'
                        }
                    },
                    plotOptions: {
                        series: {
                            marker: {
                                enabled: false,
                                symbol: 'circle',
                                radius: 3,
                                states: {
                                    hover: {
                                        enabled: true
                                    }
                                }
                            },
                            lineWidth: 3
                        },
                        column: {
                            borderRadius: 4,
                            borderWidth: 0
                        }
                    }
                };
            }

            // ── 2. AJAX Load Report Data ──
            function loadReportData() {
                const tanggal = $('#inputTanggal').val();
                if (!tanggal) {
                    toastr.warning('Silakan pilih tanggal terlebih dahulu.');
                    return;
                }

                $('#reportLoading').addClass('active');

                $.ajax({
                    url: "/api/utility/capbank/report/data",
                    method: "GET",
                    data: {
                        tanggal: tanggal
                    },
                    success: function(res) {
                        rawDataCache = res.raw_table;
                        updateDashboard(res);
                    },
                    error: function(xhr) {
                        toastr.error('Gagal mengambil data dari server.');
                        console.error(xhr);
                    },
                    complete: function() {
                        $('#reportLoading').removeClass('active');
                    }
                });
            }

            // ── 3. Update Dashboard view components ──
            function updateDashboard(res) {
                // Update Summary Cards
                $('#valCurrent').html(
                    `${res.summary.avg_current} <span style="font-size: 0.9rem; font-weight: 500;">A</span>`);
                $('#valVll').html(
                    `${res.summary.avg_vll} <span style="font-size: 0.9rem; font-weight: 500;">V</span>`);
                $('#subVll').text(
                    `Vab: ${res.summary.avg_vll_vab} | Vbc: ${res.summary.avg_vll_vbc} | Vca: ${res.summary.avg_vll_vca}`
                );

                $('#valVln').html(
                    `${res.summary.avg_vln} <span style="font-size: 0.9rem; font-weight: 500;">V</span>`);
                $('#subVln').text(
                    `Van: ${res.summary.avg_vln_van} | Vbn: ${res.summary.avg_vln_vbn} | Vcn: ${res.summary.avg_vln_vcn}`
                );

                $('#valPower').html(
                    `${res.summary.avg_ptot} <span style="font-size: 0.9rem; font-weight: 500;">kW</span>`);
                $('#subPower').text(
                    `P: ${res.summary.avg_ptot} | Q: ${res.summary.avg_qtot} | S: ${res.summary.avg_stot}`);

                $('#valPF').text(res.summary.avg_pf);
                $('#subPF').text(`Freq: ${res.summary.avg_freq} Hz`);

                $('#valLatestCap').text(res.summary.latest_cap_type);
                $('#subLatestCap').text(`${res.summary.total_transitions} Transisi ON/OFF`);

                // Update Capacitor Grid Status
                $('.cap-grid-item').removeClass('active-cap');
                const latestUpper = res.summary.latest_cap_type.toUpperCase();
                const activeCaps = latestUpper.split(',').map(function(item) {
                    return item.trim();
                });
                for (let i = 1; i <= 12; i++) {
                    const count = res.cap_summary[`cap${i}`].on_count;
                    $(`#capGridCount${i}`).text(count);

                    // If currently active in latest reading
                    if (activeCaps.includes(`CAP${i}`)) {
                        $(`#capGridItem${i}`).addClass('active-cap');
                    }
                }

                // Update Timeline Logs
                let logHtml = '';
                if (res.transitions.length > 0) {
                    res.transitions.forEach(function(item) {
                        const eventClass = item.event === 'ON' ? 'event-on' : 'event-off';
                        const badgeClass = item.event === 'ON' ? 'bg-success-subtle text-success' :
                            'bg-danger-subtle text-danger';
                        logHtml += `
                        <div class="timeline-event ${eventClass}">
                            <div class="timeline-time">${item.time_formatted}</div>
                            <div class="timeline-text">
                                <span class="badge ${badgeClass} small me-2">${item.event}</span>
                                <strong>${item.capacitor.toUpperCase()}</strong> telah beralih status
                            </div>
                        </div>
                    `;
                    });
                    $('#countTransitions').text(`${res.transitions.length} Event`);
                } else {
                    logHtml = `
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center py-5 text-muted">
                        <i class="bx bx-calendar-x fs-2"></i>
                        <p class="mt-2 small mb-0">Tidak ada transisi state kapasitor hari ini</p>
                    </div>
                `;
                    $('#countTransitions').text('0 Event');
                }
                $('#transitionsLog').html(logHtml);

                // Populate Raw Data Table
                renderTable(res.raw_table);

                // Render Charts
                renderCharts(res.trends, res.cap_summary);
            }

            // ── 4. Render Table helper ──
            function renderTable(tableData) {
                let tableHtml = '';
                if (tableData.length > 0) {
                    tableData.forEach(function(row) {
                        tableHtml += `
                        <tr>
                            <td class="fw-bold">${row.time}</td>
                            <td><span class="badge bg-info-subtle text-info">${row.cap_type}</span></td>
                            <td class="text-primary fw-semibold">${row.current ?? '-'}</td>
                            <td>${row.v_ab ?? '-'}</td>
                            <td>${row.v_bc ?? '-'}</td>
                            <td>${row.v_ca ?? '-'}</td>
                            <td>${row.v_an ?? '-'}</td>
                            <td>${row.v_bn ?? '-'}</td>
                            <td>${row.v_cn ?? '-'}</td>
                            <td class="text-emerald fw-semibold">${row.p_tot ?? '-'}</td>
                            <td class="text-warning">${row.q_tot ?? '-'}</td>
                            <td>${row.s_tot ?? '-'}</td>
                            <td>${row.pf !== null ? row.pf.toFixed(3) : '-'}</td>
                            <td>${row.cosphi !== null ? row.cosphi.toFixed(3) : '-'}</td>
                            <td>${row.freq ?? '-'}</td>
                        </tr>
                    `;
                    });
                } else {
                    tableHtml = `
                    <tr>
                        <td colspan="15" class="text-center py-4 text-muted">Tidak ada data untuk tanggal ini.</td>
                    </tr>
                `;
                }
                $('#rawTableBody').html(tableHtml);
            }

            // ── 5. Render/Update Highcharts ──
            function renderCharts(trends, capSummary) {
                const timeLabels = trends.map(d => d.time);

                // --- 2. Chart Current (Phase A vs Phase B) ---
                const currentOpts = getHighchartsBaseOptions('line', timeLabels);
                currentOpts.series = [{
                        name: 'Phase A Current (A)',
                        data: trends.map(d => d.current_a),
                        color: '#3b82f6',
                        connectNulls: true
                    },
                    {
                        name: 'Phase B Current (A)',
                        data: trends.map(d => d.current_b),
                        color: '#ef4444',
                        connectNulls: true
                    }
                ];
                Highcharts.chart('chartCurrent', currentOpts);

                // --- 3. Chart Voltage L-L ---
                const vllOpts = getHighchartsBaseOptions('line', timeLabels);
                vllOpts.series = [{
                        name: 'Vab (V)',
                        data: trends.map(d => d.v_ab),
                        color: '#2563eb'
                    },
                    {
                        name: 'Vbc (V)',
                        data: trends.map(d => d.v_bc),
                        color: '#06b6d4'
                    },
                    {
                        name: 'Vca (V)',
                        data: trends.map(d => d.v_ca),
                        color: '#6366f1'
                    }
                ];
                Highcharts.chart('chartVll', vllOpts);

                // --- 4. Chart Voltage L-N ---
                const vlnOpts = getHighchartsBaseOptions('line', timeLabels);
                vlnOpts.series = [{
                        name: 'Van (V)',
                        data: trends.map(d => d.v_an),
                        color: '#4f46e5'
                    },
                    {
                        name: 'Vbn (V)',
                        data: trends.map(d => d.v_bn),
                        color: '#a855f7'
                    },
                    {
                        name: 'Vcn (V)',
                        data: trends.map(d => d.v_cn),
                        color: '#ec4899'
                    }
                ];
                Highcharts.chart('chartVln', vlnOpts);

                // --- 5. Chart Power (P, Q, S) ---
                const powerOpts = getHighchartsBaseOptions('area', timeLabels);
                powerOpts.plotOptions.area = {
                    fillOpacity: 0.15,
                    lineWidth: 3
                };
                powerOpts.series = [{
                        name: 'Ptot (Active kW)',
                        data: trends.map(d => d.p_tot),
                        color: '#10b981'
                    },
                    {
                        name: 'Qtot (Reactive kVAR)',
                        data: trends.map(d => d.q_tot),
                        color: '#f59e0b'
                    },
                    {
                        name: 'Stot (Apparent kVA)',
                        data: trends.map(d => d.s_tot),
                        color: '#6b7280'
                    }
                ];
                Highcharts.chart('chartPower', powerOpts);

                // --- 6. Chart Capacitor Frequency (Bar/Column) ---
                const capNames = [];
                const capCounts = [];
                for (let i = 1; i <= 12; i++) {
                    capNames.push(`CAP ${i}`);
                    capCounts.push(capSummary[`cap${i}`].on_count);
                }

                const capFreqOpts = getHighchartsBaseOptions('column', capNames);
                capFreqOpts.chart.height = 240;
                capFreqOpts.plotOptions.column.dataLabels = {
                    enabled: true,
                    style: {
                        fontSize: '10px'
                    }
                };
                capFreqOpts.series = [{
                    name: 'Kali Aktif',
                    data: capCounts,
                    color: '#f59e0b'
                }];
                Highcharts.chart('chartCapFreq', capFreqOpts);
            }

            // ── 6. Filter Table Actions ──
            $('#tableSearch').on('keyup', function() {
                const query = $(this).val().toLowerCase();
                const filtered = rawDataCache.filter(function(row) {
                    return row.time.toLowerCase().includes(query) ||
                        row.cap_type.toLowerCase().includes(query) ||
                        (row.current && String(row.current).includes(query)) ||
                        (row.p_tot && String(row.p_tot).includes(query));
                });
                renderTable(filtered);
            });

            // ── 7. CSV Export ──
            $('#btnExportCSV').on('click', function() {
                if (rawDataCache.length === 0) {
                    toastr.warning('Tidak ada data untuk diexport.');
                    return;
                }

                const tanggal = $('#inputTanggal').val();
                let csv =
                    'Waktu,Kapasitor Aktif,Arus (A),Vab (V),Vbc (V),Vca (V),Van (V),Vbn (V),Vcn (V),Ptot (kW),Qtot (kVAR),Stot (kVA),PF (Avg),CosPhi (Avg),Freq (Hz)\n';

                rawDataCache.forEach(function(row) {
                    const pf = row.pf !== null ? row.pf.toFixed(3) : '';
                    const cosphi = row.cosphi !== null ? row.cosphi.toFixed(3) : '';
                    csv +=
                        `"${row.time}","${row.cap_type}","${row.current ?? ''}","${row.v_ab ?? ''}","${row.v_bc ?? ''}","${row.v_ca ?? ''}","${row.v_an ?? ''}","${row.v_bn ?? ''}","${row.v_cn ?? ''}","${row.p_tot ?? ''}","${row.q_tot ?? ''}","${row.s_tot ?? ''}","${pf}","${cosphi}","${row.freq ?? ''}"\n`;
                });

                const blob = new Blob([csv], {
                    type: 'text/csv;charset=utf-8;'
                });
                const link = document.createElement("a");
                const url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", `Capacitor_Bank_Report_${tanggal}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });

            // ── 8. Bind Buttons ──
            $('#btnTampilkan').on('click', function() {
                loadReportData();
            });

            $('#btnRefresh').on('click', function() {
                loadReportData();
            });

            // Auto load on init
            loadReportData();
        });
    </script>
@endsection
