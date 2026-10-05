@extends('layouts.app')

@section('title', 'WCO - HSE Dashboard WWTP')

@section('sidebar-size', 'sm')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
<style>
    :root {
        --wco-bg: #070c18;
        --wco-card-bg: #0d1527;
        --wco-card-header: #101c35;
        --wco-border: #1e293b;
        --wco-cyan: #06b6d4;
        --wco-green: #10b981;
        --wco-amber: #f59e0b;
        --wco-purple: #8b5cf6;
        --wco-blue: #3b82f6;
        --wco-red: #ef4444;
        --wco-text-muted: #94a3b8;
    }

    body[data-layout-mode="dark"], body {
        background-color: var(--wco-bg) !important;
    }

    .main-content {
        background-color: var(--wco-bg) !important;
    }

    /* Fix footer so it doesn't show as white bar or cut across content */
    footer.footer {
        position: static !important;
        background-color: #070c18 !important;
        color: #64748b !important;
        border-top: 1px solid #1e293b !important;
        width: 100% !important;
        margin-top: 20px !important;
        left: 0 !important;
        right: 0 !important;
        bottom: auto !important;
    }

    /* Custom Dark Scrollbar & Corner Fix to eliminate white scrollbar box */
    ::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }
    ::-webkit-scrollbar-track {
        background: #0d1527 !important;
    }
    ::-webkit-scrollbar-thumb {
        background: #1e293b !important;
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #06b6d4 !important;
    }
    ::-webkit-scrollbar-corner {
        background: #0d1527 !important;
    }

    .wco-dashboard-wrapper {
        background-color: var(--wco-bg);
        color: #f1f5f9;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        padding-bottom: 0.5rem;
    }

    /* Header styling */
    .wco-header {
        background: linear-gradient(135deg, #0b1329 0%, #0d1a38 50%, #0a1124 100%);
        border: 1px solid #1e2e4a;
        border-radius: 12px;
        padding: 10px 18px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        margin-bottom: 12px;
    }

    .wco-header-title {
        font-size: 20px;
        font-weight: 800;
        letter-spacing: 1.2px;
        color: #ffffff;
        text-shadow: 0 0 15px rgba(6, 182, 212, 0.35);
        margin-bottom: 0px;
        line-height: 1.2;
    }

    .wco-header-sub {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1px;
        color: var(--wco-cyan);
        text-transform: uppercase;
    }

    .wco-header-motto {
        font-size: 10px;
        font-style: italic;
        color: #94a3b8;
    }

    /* Common Card Styling - Compact & Fixed Overflow for Perfect Corners */
    .wco-card {
        background: var(--wco-card-bg);
        border: 1px solid var(--wco-border);
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        margin-bottom: 12px;
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden; /* Ensures rounded corners are never clipped */
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .wco-card:hover {
        border-color: rgba(56, 189, 248, 0.4);
        box-shadow: 0 6px 20px rgba(6, 182, 212, 0.12);
    }

    .wco-card-header {
        background-color: var(--wco-card-header);
        border-bottom: 1px solid var(--wco-border);
        padding: 7px 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top-left-radius: 9px;
        border-top-right-radius: 9px;
    }

    .wco-card-title {
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #ffffff;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .wco-card-body {
        padding: 8px 10px;
        flex-grow: 1;
        overflow: hidden;
    }

    /* Top KPI Cards */
    .kpi-score-card {
        background: linear-gradient(135deg, #091a2f 0%, #0a233f 100%);
        border: 1px solid #19436d;
        border-radius: 10px;
        padding: 8px 10px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .kpi-top-card {
        background: var(--wco-card-bg);
        border: 1px solid var(--wco-border);
        border-radius: 10px;
        padding: 8px 10px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .kpi-top-card:hover {
        border-color: var(--wco-cyan);
    }

    .kpi-top-header {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 2px;
    }

    .kpi-top-icon {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    .kpi-top-label {
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        line-height: 1.1;
    }

    .kpi-top-val {
        font-size: 16px;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.3px;
        line-height: 1.2;
    }

    .kpi-top-sub {
        font-size: 8.5px;
        font-weight: 600;
        color: var(--wco-cyan);
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 2px;
    }

    /* Equalisasi mini list */
    .eq-pill-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3px 5px;
        margin-top: 3px;
    }

    .eq-pill-item {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 4px;
        padding: 1px 5px;
        font-size: 8.5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .eq-pill-item span {
        color: #94a3b8;
    }

    .eq-pill-item strong {
        color: #38bdf8;
        font-family: monospace;
        font-size: 9px;
    }

    /* Info WWTP Tangki List */
    .info-tank-item {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 3px 6px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .info-tank-item:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(6, 182, 212, 0.3);
    }

    .info-tank-item img {
        width: 32px;
        height: 32px;
        object-fit: contain;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.5));
    }

    .info-tank-meta {
        overflow: hidden;
        line-height: 1.15;
    }

    .info-tank-name {
        font-size: 9px;
        font-weight: 700;
        color: #94a3b8;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .info-tank-cap {
        font-size: 11px;
        font-weight: 800;
        color: #38bdf8;
        font-family: monospace;
    }

    /* Removal Mini */
    .removal-metric-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1px 0;
        font-size: 9.5px;
    }

    .removal-metric-row span {
        color: #94a3b8;
    }

    .removal-metric-row strong {
        color: #10b981;
        font-size: 11px;
    }

    /* ApexCharts Tooltip Dark Styling for WCO Dashboard (Fix for light theme) */
    .apexcharts-tooltip,
    .apexcharts-tooltip.apexcharts-theme-light,
    .apexcharts-tooltip.apexcharts-theme-dark,
    div.apexcharts-tooltip {
        background: #0f172a !important;
        color: #f8fafc !important;
        border: 1px solid #334155 !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6) !important;
    }

    .apexcharts-tooltip-title {
        background: #1e293b !important;
        border-bottom: 1px solid #334155 !important;
        color: #38bdf8 !important;
        font-weight: 700 !important;
        padding: 4px 8px !important;
    }

    .apexcharts-tooltip-text,
    .apexcharts-tooltip-text-y-label,
    .apexcharts-tooltip-text-y-value,
    .apexcharts-tooltip-series-group {
        color: #f8fafc !important;
    }

    .apexcharts-tooltip-series-group.apexcharts-active,
    .apexcharts-tooltip-series-group:last-child {
        padding-bottom: 4px !important;
    }

    .apexcharts-xaxistooltip,
    .apexcharts-yaxistooltip {
        background: #0f172a !important;
        color: #38bdf8 !important;
        border: 1px solid #334155 !important;
    }

    .apexcharts-xaxistooltip:after,
    .apexcharts-xaxistooltip:before {
        border-bottom-color: #0f172a !important;
    }

    /* High Tech Tables */
    .wco-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 10.5px;
    }

    .wco-table th {
        background-color: rgba(255, 255, 255, 0.03);
        color: #94a3b8;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 4px 6px;
        border-bottom: 1px solid var(--wco-border);
        font-size: 9.5px;
    }

    .wco-table td {
        padding: 4px 6px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        color: #e2e8f0;
        vertical-align: middle;
    }

    .wco-table tr:hover td {
        background-color: rgba(255, 255, 255, 0.02);
    }

    .wco-table tr:last-child td {
        border-bottom: none;
    }

    /* Status indicators */
    .pulse-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        margin-right: 4px;
    }

    .pulse-green {
        background-color: #10b981;
        box-shadow: 0 0 6px #10b981;
    }

    .badge-ok {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.4);
        border-radius: 4px;
        padding: 1px 5px;
        font-size: 8.5px;
        font-weight: 700;
    }

    .badge-warn {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.4);
        border-radius: 4px;
        padding: 1px 5px;
        font-size: 8.5px;
        font-weight: 700;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.4);
        border-radius: 4px;
        padding: 1px 5px;
        font-size: 8.5px;
        font-weight: 700;
    }

    /* Operation Status List */
    .op-status-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 3px 6px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 5px;
        margin-bottom: 4px;
        transition: all 0.2s ease;
    }

    .op-status-item:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(6, 182, 212, 0.3);
    }

    .op-status-item:last-child {
        margin-bottom: 0;
    }

    /* Chart Containers - Controlled Height */
    .wco-chart-box {
        height: 135px;
        width: 100%;
        position: relative;
    }

    .wco-mini-stat {
        font-size: 9.5px;
        color: #94a3b8;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 2px 6px;
        background: rgba(255, 255, 255, 0.02);
        border-radius: 4px;
        margin-bottom: 2px;
    }

    /* Bottom Motto Banner */
    .wco-footer-banner {
        background: linear-gradient(90deg, #09162e 0%, #0d2247 50%, #09162e 100%);
        border: 1px solid #1e3a63;
        border-radius: 8px;
        padding: 6px 14px;
        text-align: center;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.8px;
        color: #38bdf8;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        margin-top: 4px;
    }

    /* Filter dropdown & dates */
    .filter-ctrl {
        background: rgba(15, 23, 42, 0.8) !important;
        border: 1px solid #334155 !important;
        color: #f8fafc !important;
        font-size: 11px !important;
    }

    .filter-ctrl:focus {
        border-color: var(--wco-cyan) !important;
        box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2) !important;
    }
</style>
@endsection

@section('content')
<div class="page-content wco-dashboard-wrapper">
    <div class="container-fluid">

        <!-- ======================================================= -->
        <!-- HEADER SECTION -->
        <!-- ======================================================= -->
        <div class="wco-header">
            <div class="row align-items-center g-2">
                <div class="col-xl-3 col-lg-4 col-md-12 d-flex align-items-center gap-2">
                    <div style="width: 36px; height: 36px; background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-water-pump text-cyan" style="font-size: 22px; color: var(--wco-cyan);"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-white fw-bold" style="letter-spacing: 1px; font-size: 15px;">WWTP</h4>
                        <div style="font-size: 9px; color: #38bdf8; font-weight: 600; letter-spacing: 0.5px;">WASTEWATER TREATMENT PLANT</div>
                    </div>
                </div>

                <div class="col-xl-5 col-lg-4 col-md-12 text-center">
                    <div class="wco-header-title">WCO – HSE DASHBOARD</div>
                    <div class="wco-header-sub">WORLD CLASS OPERATIONAL – PILLAR HSE</div>
                    <div class="wco-header-motto">"Safe People, Compliant Process, Clean Environment"</div>
                </div>

                <div class="col-xl-4 col-lg-4 col-md-12 d-flex align-items-center justify-content-end gap-2 flex-wrap">
                    <select id="wcoPeriodSelect" class="form-select form-select-sm filter-ctrl" style="width: 110px;">
                        <option value="this_month" selected>Bulan Ini</option>
                        <option value="last_7_days">7 Hari</option>
                        <option value="last_30_days">30 Hari</option>
                        <option value="custom">Custom</option>
                    </select>

                    <div id="wcoCustomDates" class="d-none d-flex align-items-center gap-1">
                        <input type="date" id="wcoStartDate" class="form-control form-control-sm filter-ctrl" style="width: 115px;">
                        <span class="text-muted small">s/d</span>
                        <input type="date" id="wcoEndDate" class="form-control form-control-sm filter-ctrl" style="width: 115px;">
                    </div>

                    <button id="btnRefreshWco" class="btn btn-sm btn-outline-info d-flex align-items-center gap-1" title="Muat ulang data">
                        <i class="mdi mdi-refresh"></i>
                    </button>

                    <div class="badge bg-dark border border-secondary px-2 py-1 text-muted" style="font-size: 9.5px;">
                        <i class="mdi mdi-clock-outline me-1 text-cyan"></i>
                        <span id="wcoLastUpdate">-</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- TOP ROW: 5 KPI CARDS + OVERALL SCORE GAUGE (1 TO 5) -->
        <!-- ======================================================= -->
        <div class="row g-2 mb-2">
            <!-- Card: REMOVAL EFFLUENT (Presentase Gede - Menggantikan Score) -->
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="kpi-top-card">
                    <div class="kpi-top-header">
                        <div class="kpi-top-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                            <i class="mdi mdi-percent-outline"></i>
                        </div>
                        <div>
                            <div class="kpi-top-label">REMOVAL EFFLUENT</div>
                            <div class="text-muted" style="font-size: 8.5px;">INFLUENT VS EFFLUENT</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-around my-auto py-1">
                        <div class="text-center">
                            <div class="text-muted" style="font-size: 8.5px; font-weight: 700; letter-spacing: 0.5px;">TSS REMOVAL</div>
                            <div class="fw-bold text-success font-monospace" id="remTssVal" style="font-size: 20px; line-height: 1.1;">0%</div>
                        </div>
                        <div style="width: 1px; height: 30px; background: rgba(255,255,255,0.1);"></div>
                        <div class="text-center">
                            <div class="text-muted" style="font-size: 8.5px; font-weight: 700; letter-spacing: 0.5px;">COD REMOVAL</div>
                            <div class="fw-bold text-cyan font-monospace" id="remCodVal" style="font-size: 20px; line-height: 1.1;">0%</div>
                        </div>
                    </div>
                    <div class="kpi-top-sub text-success">
                        <i class="mdi mdi-arrow-up-bold"></i> TARGET ≥ 90%
                    </div>
                </div>
            </div>

            <!-- Card 1: TOTAL COST / M3 (Biaya per Kubik di Biaya Chemical) -->
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="kpi-top-card">
                    <div class="kpi-top-header">
                        <div class="kpi-top-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                            <i class="mdi mdi-cash-multiple"></i>
                        </div>
                        <div>
                            <div class="kpi-top-label">BIAYA PER KUBIK</div>
                            <div class="text-muted" style="font-size: 8.5px;">BIAYA CHEMICAL</div>
                        </div>
                    </div>
                    <div class="my-auto">
                        <div class="kpi-top-val" id="topCostM3Val">Rp 0</div>
                    </div>
                    <div class="kpi-top-sub text-success" id="topCostM3Sub">
                        <i class="mdi mdi-arrow-down-bold"></i> TARGET ≤ Rp 3.000 / m³
                    </div>
                </div>
            </div>

            <!-- Card 2: TOTAL COST CHEMICAL (Total Cost Chemical / bulan) -->
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="kpi-top-card">
                    <div class="kpi-top-header">
                        <div class="kpi-top-icon" style="background: rgba(6, 182, 212, 0.15); color: #06b6d4;">
                            <i class="mdi mdi-flask-round-bottom-outline"></i>
                        </div>
                        <div>
                            <div class="kpi-top-label">TOTAL COST CHEMICAL</div>
                            <div class="text-muted" style="font-size: 8.5px;">PER BULAN</div>
                        </div>
                    </div>
                    <div class="my-auto">
                        <div class="kpi-top-val" id="topCostTotalVal">Rp 0</div>
                    </div>
                    <div class="kpi-top-sub text-cyan" id="topCostTotalSub">
                        <i class="mdi mdi-check-circle-outline"></i> BIAYA PEMAKAIAN
                    </div>
                </div>
            </div>

            <!-- Card 3: PENGANGKUTAN SLUDGE (Total Tonase Pengangkutan Sludge) -->
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="kpi-top-card">
                    <div class="kpi-top-header">
                        <div class="kpi-top-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                            <i class="mdi mdi-truck-delivery-outline"></i>
                        </div>
                        <div>
                            <div class="kpi-top-label">PENGANGKUTAN SLUDGE</div>
                            <div class="text-muted" style="font-size: 8.5px;">TOTAL TONASE</div>
                        </div>
                    </div>
                    <div class="my-auto">
                        <div class="kpi-top-val" id="topPengangkutanSludgeVal">0 Ton</div>
                    </div>
                    <div class="kpi-top-sub text-warning" id="topPengangkutanSludgeSub">
                        <i class="mdi mdi-scale"></i> TOTAL AKUMULASI
                    </div>
                </div>
            </div>

            <!-- Card 4: EQUALISASI (INFLUENT) (pH, TSS, COD, EC) -->
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="kpi-top-card">
                    <div class="kpi-top-header">
                        <div class="kpi-top-icon" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                            <i class="mdi mdi-tune-vertical"></i>
                        </div>
                        <div>
                            <div class="kpi-top-label">EQUALISASI (INFLUENT)</div>
                            <div class="text-muted" style="font-size: 8.5px;">PARAMETER AIR MASUK</div>
                        </div>
                    </div>
                    <div class="eq-pill-grid">
                        <div class="eq-pill-item"><span>pH:</span> <strong id="eqValPh">-</strong></div>
                        <div class="eq-pill-item"><span>TSS:</span> <strong id="eqValTss">-</strong></div>
                        <div class="eq-pill-item"><span>COD:</span> <strong id="eqValCod">-</strong></div>
                        <div class="eq-pill-item"><span>EC:</span> <strong id="eqValEc">-</strong></div>
                    </div>
                    <div class="kpi-top-sub text-info mt-1">
                        <i class="mdi mdi-information-outline"></i> Rata-rata Periode
                    </div>
                </div>
            </div>

            <!-- Card 5: ANALISA AIR LIMBAH EFFLUENT (COD, TSS, pH, EC) -->
            <div class="col-xl-2 col-lg-4 col-md-6">
                <div class="kpi-top-card">
                    <div class="kpi-top-header">
                        <div class="kpi-top-icon" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
                            <i class="mdi mdi-water-check-outline"></i>
                        </div>
                        <div>
                            <div class="kpi-top-label">ANALISA EFFLUENT</div>
                            <div class="text-muted" style="font-size: 8.5px;">PARAMETER AIR KELUAR</div>
                        </div>
                    </div>
                    <div class="eq-pill-grid">
                        <div class="eq-pill-item"><span>pH:</span> <strong id="effValPh">-</strong></div>
                        <div class="eq-pill-item"><span>TSS:</span> <strong id="effValTss">-</strong></div>
                        <div class="eq-pill-item"><span>COD:</span> <strong id="effValCod">-</strong></div>
                        <div class="eq-pill-item"><span>EC:</span> <strong id="effValEc">-</strong></div>
                    </div>
                    <div class="kpi-top-sub text-info mt-1">
                        <i class="mdi mdi-check-circle-outline"></i> Baku Mutu: COD ≤ 300
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- ROW 1: 4 CARDS (INFORMASI WWTP, STATUS OPERASI, ENVIRONMENT PERF, CHEMICAL CONSUMPTION) -->
        <!-- ======================================================= -->
        <div class="row g-2 mb-2">
            <!-- 6. INFORMASI WWTP (Kapasitas Tangki Permanent) -->
            <div class="col-xl-3 col-lg-6">
                <div class="wco-card">
                    <div class="wco-card-header">
                        <h5 class="wco-card-title">
                            <i class="mdi mdi-information-outline text-info"></i> INFORMASI WWTP
                        </h5>
                        <span class="badge bg-primary-subtle text-primary" style="font-size: 8.5px;">KAPASITAS TANGKI</span>
                    </div>
                    <div class="wco-card-body p-2 d-flex flex-column justify-content-between">
                        <div class="row g-1">
                            <div class="col-6">
                                <div class="info-tank-item">
                                    <img src="{{ asset('assets/images/wwtp/dashboard/EQUALISASI.png') }}" alt="Equal">
                                    <div class="info-tank-meta">
                                        <div class="info-tank-name">Equal</div>
                                        <div class="info-tank-cap">20 m³</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-tank-item">
                                    <img src="{{ asset('assets/images/wwtp/dashboard/ANAEROB.png') }}" alt="Anaerob">
                                    <div class="info-tank-meta">
                                        <div class="info-tank-name">Anaerob</div>
                                        <div class="info-tank-cap">426 m³</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-tank-item">
                                    <img src="{{ asset('assets/images/wwtp/dashboard/AEROB.png') }}" alt="Aerob">
                                    <div class="info-tank-meta">
                                        <div class="info-tank-name">Aerob</div>
                                        <div class="info-tank-cap">170 m³</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-tank-item">
                                    <img src="{{ asset('assets/images/wwtp/dashboard/LUMPUR AKTIF.png') }}" alt="Lumpur Aktif">
                                    <div class="info-tank-meta">
                                        <div class="info-tank-name">Lumpur Aktif</div>
                                        <div class="info-tank-cap">160 m³</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-tank-item">
                                    <img src="{{ asset('assets/images/wwtp/dashboard/DAF.png') }}" alt="DAF">
                                    <div class="info-tank-meta">
                                        <div class="info-tank-name">DAF</div>
                                        <div class="info-tank-cap">10 m³</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-tank-item">
                                    <img src="{{ asset('assets/images/wwtp/dashboard/OUTLET.png') }}" alt="Outlet">
                                    <div class="info-tank-meta">
                                        <div class="info-tank-name">Outlet</div>
                                        <div class="info-tank-cap">1 m³</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 10. STATUS OPERASI (User #10: PIT Garam, Buffer Pit Garam, Pit Sparta, Step 3) -->
            <div class="col-xl-3 col-lg-6">
                <div class="wco-card">
                    <div class="wco-card-header">
                        <h5 class="wco-card-title">
                            <i class="mdi mdi-cog-sync text-success"></i> STATUS OPERASI
                        </h5>
                        <span class="badge bg-success-subtle text-success" style="font-size: 8.5px;">REALTIME PITS</span>
                    </div>
                    <div class="wco-card-body p-2" id="wcoStatusOperasiContainer">
                        <div class="text-center p-2 text-muted" style="font-size: 10px;">Memuat status pit...</div>
                    </div>
                </div>
            </div>

            <!-- 8. ENVIRONMENT PERFORMANCE (User #8: Influent, Outlet Anaerob, Aerob, DAF, Effluent) -->
            <div class="col-xl-3 col-lg-6">
                <div class="wco-card">
                    <div class="wco-card-header">
                        <h5 class="wco-card-title">
                            <i class="mdi mdi-leaf text-success"></i> ENVIRONMENT PERF
                        </h5>
                        <span class="badge bg-success-subtle text-success" style="font-size: 8.5px;">5 TAHAPAN</span>
                    </div>
                    <div class="wco-card-body p-0">
                        <div class="table-responsive">
                            <table class="wco-table">
                                <thead>
                                    <tr>
                                        <th>Tahapan</th>
                                        <th class="text-center">pH</th>
                                        <th class="text-center">COD</th>
                                        <th class="text-center">TSS</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tableEnvPerfBody">
                                    <!-- Filled by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 9. CHEMICAL CONSUMPTION (User #9: Pemakaian Chemical WWTP) -->
            <div class="col-xl-3 col-lg-6">
                <div class="wco-card">
                    <div class="wco-card-header">
                        <h5 class="wco-card-title">
                            <i class="mdi mdi-flask text-warning"></i> CHEMICAL USAGE
                        </h5>
                        <span class="badge bg-warning-subtle text-warning" style="font-size: 8.5px;">THIS MONTH</span>
                    </div>
                    <div class="wco-card-body p-0">
                        <div class="table-responsive" style="max-height: 145px; overflow-y: auto;">
                            <table class="wco-table">
                                <thead>
                                    <tr>
                                        <th>Chemical</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Cost/m³</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tableChemConsBody">
                                    <!-- Filled by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- ROW 2: 4 TREND CARDS (SAFETY PERF, INFLUENT MINGGUAN, EFFLUENT MINGGUAN, KEPATUHAN COD) -->
        <!-- ======================================================= -->
        <div class="row g-2 mb-2">
            <!-- 7. SAFETY PERFORMANCE (User #7: Influent Daily Aggregated & Pit Distribution) -->
            <div class="col-xl-3 col-lg-6">
                <div class="wco-card">
                    <div class="wco-card-header">
                        <h5 class="wco-card-title">
                            <i class="mdi mdi-shield-check text-cyan"></i> SAFETY PERFORMANCE
                        </h5>
                        <span class="badge bg-info-subtle text-info" style="font-size: 8.5px;">INFLUENT DAILY</span>
                    </div>
                    <div class="wco-card-body p-2">
                        <div class="wco-mini-stat">
                            <span>Trend Influent Harian (Aggregated):</span>
                            <strong class="text-cyan" id="safetyDailyTotal">Active</strong>
                        </div>
                        <div class="wco-chart-box" id="chartSafetyPerf"></div>
                    </div>
                </div>
            </div>

            <!-- 14. INFLUENT MINGGUAN (User #14: Proses WWTP Influent Mingguan) -->
            <div class="col-xl-3 col-lg-6">
                <div class="wco-card">
                    <div class="wco-card-header">
                        <h5 class="wco-card-title">
                            <i class="mdi mdi-chart-areaspline text-success"></i> INFLUENT MINGGUAN
                        </h5>
                        <span class="badge bg-success-subtle text-success" style="font-size: 8.5px;">PIT BREAKDOWN</span>
                    </div>
                    <div class="wco-card-body p-2">
                        <div class="wco-mini-stat">
                            <span>Sparta, Garam, Step 3, Storage</span>
                            <span class="badge-ok">WEEKLY</span>
                        </div>
                        <div class="wco-chart-box" id="chartInfluentMingguan"></div>
                    </div>
                </div>
            </div>

            <!-- 11. TREND EFFLUENT MINGGUAN (User #11: Full Proses vs DAF Pre) -->
            <div class="col-xl-3 col-lg-6">
                <div class="wco-card">
                    <div class="wco-card-header">
                        <h5 class="wco-card-title">
                            <i class="mdi mdi-chart-bar text-primary"></i> EFFLUENT MINGGUAN
                        </h5>
                        <span class="badge bg-primary-subtle text-primary" style="font-size: 8.5px;">PROSES WWTP</span>
                    </div>
                    <div class="wco-card-body p-2">
                        <div class="wco-mini-stat">
                            <span>Full Proses vs DAF Pre (m³)</span>
                            <span class="badge bg-primary-subtle text-primary" style="font-size: 8.5px;">VOL EFFLUENT</span>
                        </div>
                        <div class="wco-chart-box" id="chartEffluentMingguan"></div>
                    </div>
                </div>
            </div>

            <!-- 12. TREND KEPATUHAN EFFLUENT (COD) (Baku Mutu 300 PPM) -->
            <div class="col-xl-3 col-lg-6">
                <div class="wco-card">
                    <div class="wco-card-header">
                        <h5 class="wco-card-title">
                            <i class="mdi mdi-check-decagram text-warning"></i> KEPATUHAN COD
                        </h5>
                        <span class="badge bg-warning-subtle text-warning" style="font-size: 8.5px;">≤ 300 PPM</span>
                    </div>
                    <div class="wco-card-body p-2">
                        <div class="wco-mini-stat">
                            <span>COD vs Baku Mutu (300 ppm):</span>
                            <strong class="text-success" id="codComplianceBadge">100% OK</strong>
                        </div>
                        <div class="wco-chart-box" id="chartKepatuhanCod"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- ROW 3: 4 CARDS (PERFORMANCE SAMPEL, SLUDGE, TOP 5 RISK, KEPATUHAN TSS) -->
        <!-- ======================================================= -->
        <div class="row g-2 mb-2">
            <!-- 13. PERFORMANCE SAMPEL (User #13: Aerasi 1-6 & Lumpur Aktif) -->
            <div class="col-xl-3 col-lg-6">
                <div class="wco-card">
                    <div class="wco-card-header">
                        <h5 class="wco-card-title">
                            <i class="mdi mdi-bacteria text-cyan"></i> PERFORMANCE SAMPEL
                        </h5>
                        <span class="badge bg-info-subtle text-info" style="font-size: 8.5px;">AERASI 1-6 & LA</span>
                    </div>
                    <div class="wco-card-body p-0">
                        <div class="table-responsive" style="max-height: 145px; overflow-y: auto;">
                            <table class="wco-table">
                                <thead>
                                    <tr>
                                        <th>Sampel</th>
                                        <th class="text-center">SV30</th>
                                        <th class="text-center">MLSS</th>
                                        <th class="text-center">DO</th>
                                        <th class="text-center">pH</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tableAerasiBody">
                                    <!-- Filled by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 15. SLUDGE MANAGEMENT (Parameter Harian: Drain lumpur, Running Hour scp, hasil lumpur, content sludge) -->
            <div class="col-xl-3 col-lg-6">
                <div class="wco-card">
                    <div class="wco-card-header">
                        <h5 class="wco-card-title">
                            <i class="mdi mdi-recycle text-purple"></i> SLUDGE MANAGEMENT
                        </h5>
                        <span class="badge bg-purple-subtle text-purple" style="font-size: 8.5px;">PARAMETER HARIAN</span>
                    </div>
                    <div class="wco-card-body p-1">
                        <table class="wco-table">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th class="text-end">Hasil</th>
                                    <th class="text-center">Sat</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-muted">Drain Lumpur</td>
                                    <td class="text-end fw-bold text-white"><span id="sludgeDrainVal">0</span></td>
                                    <td class="text-center text-muted">m³</td>
                                    <td class="text-center"><span class="badge-ok">OK</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Running Hour SCP</td>
                                    <td class="text-end fw-bold text-white"><span id="sludgeRunningVal">0</span></td>
                                    <td class="text-center text-muted">Jam</td>
                                    <td class="text-center"><span class="badge-ok">OK</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Hasil Lumpur</td>
                                    <td class="text-end fw-bold text-white"><span id="sludgeHasilVal">0</span></td>
                                    <td class="text-center text-muted">kg</td>
                                    <td class="text-center"><span class="badge-ok">OK</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Content Sludge</td>
                                    <td class="text-end fw-bold text-white"><span id="sludgeContentVal">0</span></td>
                                    <td class="text-center text-muted">%</td>
                                    <td class="text-center"><span class="badge-ok">OK</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 16. TOP 5 RISK WWTP -> JUMLAH KOLONI (Grafik Standar 10^5) -->
            <div class="col-xl-3 col-lg-6">
                <div class="wco-card">
                    <div class="wco-card-header">
                        <h5 class="wco-card-title">
                            <i class="mdi mdi-alert-circle-outline text-danger"></i> TOP 5 RISK (KOLONI)
                        </h5>
                        <span class="badge bg-danger-subtle text-danger" style="font-size: 8.5px;">STANDAR: 10⁵ CFU</span>
                    </div>
                    <div class="wco-card-body p-2">
                        <div class="wco-mini-stat">
                            <span>Batas Standar: 1.0 × 10⁵ CFU/mL</span>
                            <span class="badge bg-danger text-white" style="font-size: 8px;">10⁵</span>
                        </div>
                        <div class="wco-chart-box" id="chartTopRiskKoloni"></div>
                    </div>
                </div>
            </div>

            <!-- 17. KEPATUHAN EFFLUENT (TSS) / HSE COMPLIANCE (User #17: Kepatuhan TSS) -->
            <div class="col-xl-3 col-lg-6">
                <div class="wco-card">
                    <div class="wco-card-header">
                        <h5 class="wco-card-title">
                            <i class="mdi mdi-water-check text-cyan"></i> KEPATUHAN TSS
                        </h5>
                        <span class="badge bg-info-subtle text-info" style="font-size: 8.5px;">≤ 100 PPM</span>
                    </div>
                    <div class="wco-card-body p-2">
                        <div class="wco-mini-stat">
                            <span>TSS vs Baku Mutu (100 ppm):</span>
                            <strong class="text-success" id="tssComplianceBadge">96% OK</strong>
                        </div>
                        <div class="wco-chart-box" id="chartKepatuhanTss"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- BOTTOM BANNER -->
        <!-- ======================================================= -->
        <div class="wco-footer-banner">
            "DISIPLIN HSE ADALAH BUDAYA KITA, WWTP AMAN – LINGKUNGAN TERJAGA – PRODUKSI BERKELANJUTAN"
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    let wcoCharts = {};

    document.addEventListener('DOMContentLoaded', function () {
        initDefaultDates();
        loadWcoData();

        // Event Listeners
        document.getElementById('btnRefreshWco').addEventListener('click', function () {
            loadWcoData();
        });

        document.getElementById('wcoPeriodSelect').addEventListener('change', function () {
            const val = this.value;
            const customDatesDiv = document.getElementById('wcoCustomDates');
            if (val === 'custom') {
                customDatesDiv.classList.remove('d-none');
            } else {
                customDatesDiv.classList.add('d-none');
                setDatesByPeriod(val);
                loadWcoData();
            }
        });

        document.getElementById('wcoStartDate').addEventListener('change', function () {
            if (document.getElementById('wcoPeriodSelect').value === 'custom') loadWcoData();
        });
        document.getElementById('wcoEndDate').addEventListener('change', function () {
            if (document.getElementById('wcoPeriodSelect').value === 'custom') loadWcoData();
        });
    });

    function initDefaultDates() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        const fmt = d => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        document.getElementById('wcoStartDate').value = fmt(firstDay);
        document.getElementById('wcoEndDate').value = fmt(lastDay);
    }

    function setDatesByPeriod(period) {
        const today = new Date();
        const fmt = d => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

        if (period === 'this_month') {
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            document.getElementById('wcoStartDate').value = fmt(firstDay);
            document.getElementById('wcoEndDate').value = fmt(lastDay);
        } else if (period === 'last_7_days') {
            const past = new Date();
            past.setDate(today.getDate() - 7);
            document.getElementById('wcoStartDate').value = fmt(past);
            document.getElementById('wcoEndDate').value = fmt(today);
        } else if (period === 'last_30_days') {
            const past = new Date();
            past.setDate(today.getDate() - 30);
            document.getElementById('wcoStartDate').value = fmt(past);
            document.getElementById('wcoEndDate').value = fmt(today);
        }
    }

    function renderOrUpdateChart(domId, options) {
        const el = document.getElementById(domId);
        if (!el || typeof ApexCharts === 'undefined') return;
        if (wcoCharts[domId]) {
            wcoCharts[domId].updateOptions(options);
        } else {
            wcoCharts[domId] = new ApexCharts(el, options);
            wcoCharts[domId].render();
        }
    }

    // Fetch and bind all data
    async function loadWcoData() {
        const start = document.getElementById('wcoStartDate').value;
        const end   = document.getElementById('wcoEndDate').value;

        try {
            const res = await fetch(`{{ route('wwtp.dashboard_wco_data') }}?start_date=${start}&end_date=${end}`);
            if (!res.ok) throw new Error('Network error');
            const data = await res.json();

            // Last Update
            document.getElementById('wcoLastUpdate').textContent = data.last_update || '-';

            // Top Card: Removal Outlet (Effluent) - Presentase Gede
            if (data.card5_removal_outlet) {
                document.getElementById('remTssVal').textContent = `${data.card5_removal_outlet.tss_pct}%`;
                document.getElementById('remCodVal').textContent = `${data.card5_removal_outlet.cod_pct}%`;
            }

            // 1. Card Biaya per Kubik
            if (data.card1_biaya_per_m3) {
                document.getElementById('topCostM3Val').innerHTML = `${data.card1_biaya_per_m3.formatted}`;
                document.getElementById('topCostM3Sub').innerHTML = `<i class="mdi mdi-arrow-down-bold"></i> ${data.card1_biaya_per_m3.target}`;
            }

            // 2. Card Total Cost Chemical
            if (data.card2_total_cost_chem) {
                document.getElementById('topCostTotalVal').textContent = data.card2_total_cost_chem.formatted;
            }

            // 3. Card Pengangkutan Sludge (menggantikan Chemical Safety)
            if (data.card3_pengangkutan_sludge) {
                document.getElementById('topPengangkutanSludgeVal').textContent = data.card3_pengangkutan_sludge.formatted;
            }

            // 4. Card Equalisasi (Influent list)
            if (data.card4_equalisasi) {
                document.getElementById('eqValPh').textContent  = data.card4_equalisasi.ph || '-';
                document.getElementById('eqValTss').textContent = (data.card4_equalisasi.tss || '-') + ' ppm';
                document.getElementById('eqValCod').textContent = (data.card4_equalisasi.cod || '-') + ' ppm';
                document.getElementById('eqValEc').textContent  = (data.card4_equalisasi.ec || '-') + ' mS';
            }

            // 5. Card Analisa Air Limbah Effluent
            if (data.card5_effluent_analisa) {
                document.getElementById('effValPh').textContent  = data.card5_effluent_analisa.ph || '-';
                document.getElementById('effValTss').textContent = (data.card5_effluent_analisa.tss || '-') + ' ppm';
                document.getElementById('effValCod').textContent = (data.card5_effluent_analisa.cod || '-') + ' ppm';
                document.getElementById('effValEc').textContent  = (data.card5_effluent_analisa.ec || '-') + ' mS';
            }

            // 10. Card Status Operasi
            if (data.card10_status_operasi && Array.isArray(data.card10_status_operasi)) {
                let containerHtml = '';
                data.card10_status_operasi.forEach(item => {
                    containerHtml += `
                        <div class="op-status-item">
                            <div class="d-flex align-items-center gap-2">
                                <img src="${item.img}" alt="${item.name}" style="width: 22px; height: 22px; object-fit: contain;">
                                <div>
                                    <div class="fw-bold text-white" style="font-size: 10px;">${item.name}</div>
                                    <div class="text-muted" style="font-size: 8px;">${item.subtext}</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge-ok"><span class="pulse-dot pulse-green"></span>${item.status}</span>
                                <div class="text-info fw-bold font-monospace" style="font-size: 9px; margin-top: 1px;">${item.volume}</div>
                            </div>
                        </div>
                    `;
                });
                document.getElementById('wcoStatusOperasiContainer').innerHTML = containerHtml;
            }

            // 8. Card Environment Performance (Table)
            if (data.card8_env_perf && Array.isArray(data.card8_env_perf)) {
                let tbodyHtml = '';
                data.card8_env_perf.forEach(item => {
                    tbodyHtml += `
                        <tr>
                            <td class="fw-semibold text-white" style="font-size: 9.5px;">${item.parameter}</td>
                            <td class="text-center font-monospace">${item.ph}</td>
                            <td class="text-center font-monospace text-cyan">${item.cod}</td>
                            <td class="text-center font-monospace text-info">${item.tss}</td>
                            <td class="text-center"><span class="badge-ok">${item.status}</span></td>
                        </tr>
                    `;
                });
                document.getElementById('tableEnvPerfBody').innerHTML = tbodyHtml;
            }

            // 9. Card Chemical Consumption (Table)
            if (data.card9_chem_consumption && Array.isArray(data.card9_chem_consumption)) {
                let tbodyHtml = '';
                data.card9_chem_consumption.forEach(item => {
                    tbodyHtml += `
                        <tr>
                            <td class="fw-semibold text-white" style="font-size: 9.5px;">${item.chemical_name}</td>
                            <td class="text-end font-monospace">${Number(item.qty).toLocaleString('id-ID')}</td>
                            <td class="text-end font-monospace text-muted">Rp ${Number(item.cost_m3).toLocaleString('id-ID')}</td>
                            <td class="text-center"><span class="badge-ok">${item.status}</span></td>
                        </tr>
                    `;
                });
                document.getElementById('tableChemConsBody').innerHTML = tbodyHtml;
            }

            // 7. Card Safety Performance (Chart: Influent Daily Aggregated)
            if (data.card7_safety_perf && data.card7_safety_perf.daily_aggregated) {
                const arr = data.card7_safety_perf.daily_aggregated;
                const categories = arr.map(a => a.tanggal);
                const totals = arr.map(a => {
                    return Math.round((a.pit_sparta || 0) + (a.pit_garam || 0) + (a.pit_produksi_step3 || 0) + (a.pit_domestik || 0) + (a.pit_storage || 0));
                });

                const safetyOptions = {
                    chart: { type: 'bar', height: 135, toolbar: { show: false }, background: 'transparent' },
                    theme: { mode: 'dark' },
                    plotOptions: { bar: { columnWidth: '55%', borderRadius: 2 } },
                    dataLabels: { enabled: false },
                    colors: ['#06b6d4'],
                    series: [{ name: 'Total Influent (m³)', data: totals }],
                    xaxis: { categories: categories, labels: { style: { colors: '#64748b', fontSize: '9px' } } },
                    yaxis: { labels: { style: { colors: '#64748b', fontSize: '9px' } } },
                    grid: { borderColor: '#1e293b', strokeDashArray: 3, padding: { top: -10, bottom: -5, left: 5, right: 5 } },
                    tooltip: { theme: 'dark' }
                };
                renderOrUpdateChart('chartSafetyPerf', safetyOptions);
            }

            // 14. Card Influent Mingguan (Chart)
            if (data.card14_influent_mingguan) {
                const im = data.card14_influent_mingguan;
                const influentWeeklyOptions = {
                    chart: { type: 'area', height: 135, toolbar: { show: false }, background: 'transparent' },
                    theme: { mode: 'dark' },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2 },
                    colors: ['#06b6d4', '#10b981', '#f59e0b'],
                    series: [
                        { name: 'Pit Sparta', data: im.sparta || [] },
                        { name: 'Pit Garam', data: im.garam || [] },
                        { name: 'Step 3', data: im.step3 || [] }
                    ],
                    xaxis: { categories: im.categories || [], labels: { style: { colors: '#64748b', fontSize: '9px' } } },
                    yaxis: { labels: { style: { colors: '#64748b', fontSize: '9px' } } },
                    grid: { borderColor: '#1e293b', strokeDashArray: 3, padding: { top: -10, bottom: -5, left: 5, right: 5 } },
                    legend: { position: 'top', fontSize: '8.5px', labels: { colors: '#94a3b8' }, itemMargin: { horizontal: 3 } },
                    tooltip: { theme: 'dark' }
                };
                renderOrUpdateChart('chartInfluentMingguan', influentWeeklyOptions);
            }

            // 11. Card Trend Effluent Mingguan (Chart)
            if (data.card11_trend_effluent_mingguan) {
                const em = data.card11_trend_effluent_mingguan;
                const effluentWeeklyOptions = {
                    chart: { type: 'bar', height: 135, toolbar: { show: false }, background: 'transparent' },
                    theme: { mode: 'dark' },
                    plotOptions: { bar: { columnWidth: '50%', borderRadius: 2 } },
                    dataLabels: { enabled: false },
                    colors: ['#3b82f6', '#8b5cf6'],
                    series: [
                        { name: 'Full Proses', data: em.full_proses || [] },
                        { name: 'DAF Pre', data: em.daf_pre || [] }
                    ],
                    xaxis: { categories: em.categories || [], labels: { style: { colors: '#64748b', fontSize: '9px' } } },
                    yaxis: { labels: { style: { colors: '#64748b', fontSize: '9px' } } },
                    grid: { borderColor: '#1e293b', strokeDashArray: 3, padding: { top: -10, bottom: -5, left: 5, right: 5 } },
                    legend: { position: 'top', fontSize: '8.5px', labels: { colors: '#94a3b8' }, itemMargin: { horizontal: 3 } },
                    tooltip: { theme: 'dark' }
                };
                renderOrUpdateChart('chartEffluentMingguan', effluentWeeklyOptions);
            }

            // 12. Card Trend Kepatuhan Effluent COD (Chart: Baku Mutu 300)
            if (data.card12_trend_kepatuhan_effluent_cod) {
                const cod = data.card12_trend_kepatuhan_effluent_cod;
                const bakuMutuVal = cod.baku_mutu || 300;
                if (document.getElementById('codComplianceBadge')) {
                    document.getElementById('codComplianceBadge').textContent = (cod.compliance ?? 100) + '% OK';
                }
                const codOptions = {
                    chart: { type: 'line', height: 135, toolbar: { show: false }, background: 'transparent' },
                    theme: { mode: 'dark' },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2.5 },
                    colors: ['#10b981'],
                    series: [{ name: 'COD (ppm)', data: cod.values || [] }],
                    annotations: {
                        yaxis: [{
                            y: bakuMutuVal,
                            borderColor: '#ef4444',
                            strokeDashArray: 2,
                            label: { text: `Baku Mutu (${bakuMutuVal})`, style: { color: '#fff', background: '#ef4444', fontSize: '8px' } }
                        }]
                    },
                    xaxis: { categories: cod.categories || [], labels: { style: { colors: '#64748b', fontSize: '9px' } } },
                    yaxis: { labels: { style: { colors: '#64748b', fontSize: '9px' } } },
                    grid: { borderColor: '#1e293b', strokeDashArray: 3, padding: { top: -10, bottom: -5, left: 5, right: 5 } },
                    tooltip: { theme: 'dark' }
                };
                renderOrUpdateChart('chartKepatuhanCod', codOptions);
            }

            // 13. Card Performance Sampel (Aerasi 1-6 & Lumpur Aktif)
            if (data.card13_chem_storage_performance) {
                const samples = data.card13_chem_storage_performance.samples || [];
                let aerasiHtml = '';
                samples.forEach(s => {
                    aerasiHtml += `
                        <tr>
                            <td class="fw-semibold text-white" style="font-size: 9.5px;">${s.nama_sampel}</td>
                            <td class="text-center font-monospace">${s.sv30}</td>
                            <td class="text-center font-monospace text-cyan">${s.mlss}</td>
                            <td class="text-center font-monospace text-success">${s.do}</td>
                            <td class="text-center font-monospace text-warning">${s.ph}</td>
                            <td class="text-center"><span class="badge-ok">${s.status}</span></td>
                        </tr>
                    `;
                });
                document.getElementById('tableAerasiBody').innerHTML = aerasiHtml;
            }

            // 15. Card Sludge Management (Parameter Harian)
            if (data.card15_sludge_mgmt) {
                const sl = data.card15_sludge_mgmt;
                document.getElementById('sludgeDrainVal').textContent   = Number(sl.drain_lumpur || 0).toLocaleString('id-ID');
                document.getElementById('sludgeRunningVal').textContent = Number(sl.running_hour_scp || 0).toLocaleString('id-ID');
                document.getElementById('sludgeHasilVal').textContent   = Number(sl.hasil_lumpur || 0).toLocaleString('id-ID');
                document.getElementById('sludgeContentVal').textContent = Number(sl.sludge_content || 0).toLocaleString('id-ID');
            }

            // 16. Card Top 5 Risk WWTP -> Jumlah Koloni (Grafik Standar 10^5)
            if (data.card16_top_risk_koloni) {
                const kData = data.card16_top_risk_koloni;
                const categories = kData.categories || [];
                const values = kData.values || [];
                const strings = kData.strings || [];

                const koloniOptions = {
                    chart: { type: 'bar', height: 135, toolbar: { show: false }, background: 'transparent' },
                    theme: { mode: 'dark' },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '55%',
                            borderRadius: 2,
                            colors: {
                                ranges: [
                                    { from: 0, to: 1.0, color: '#10b981' },
                                    { from: 1.01, to: 999999, color: '#ef4444' }
                                ]
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        textAnchor: 'start',
                        style: { fontSize: '8.5px', colors: ['#ffffff'] },
                        formatter: function (val, opt) {
                            return (strings[opt.dataPointIndex] || (val + ' × 10⁵')).split(' ')[0] + ' × 10⁵';
                        },
                        offsetX: 5
                    },
                    series: [{ name: 'Koloni (× 10⁵ CFU)', data: values }],
                    xaxis: {
                        categories: categories,
                        labels: { style: { colors: '#64748b', fontSize: '8.5px' } }
                    },
                    yaxis: {
                        labels: { style: { colors: '#94a3b8', fontSize: '9px', fontWeight: 600 } }
                    },
                    annotations: {
                        xaxis: [{
                            x: 1.0,
                            borderColor: '#f59e0b',
                            strokeDashArray: 3,
                            label: {
                                text: 'Standar (10⁵)',
                                orientation: 'horizontal',
                                style: { color: '#ffffff', background: '#f59e0b', fontSize: '8px' }
                            }
                        }]
                    },
                    grid: { borderColor: '#1e293b', strokeDashArray: 3, padding: { top: -10, bottom: -5, left: 10, right: 10 } },
                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function (val, opt) {
                                const str = strings[opt.dataPointIndex] || (val + ' × 10⁵ CFU/mL');
                                const status = val > 1.0 ? ' (MELEBIHI STANDAR 10⁵)' : ' (AMAN ≤ 10⁵)';
                                return str + status;
                            }
                        }
                    }
                };
                renderOrUpdateChart('chartTopRiskKoloni', koloniOptions);
            }

            // 17. Card Kepatuhan Effluent TSS (Chart)
            if (data.card17_hse_training_effluent_tss) {
                const tss = data.card17_hse_training_effluent_tss;
                const tssOptions = {
                    chart: { type: 'line', height: 135, toolbar: { show: false }, background: 'transparent' },
                    theme: { mode: 'dark' },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2.5 },
                    colors: ['#06b6d4'],
                    series: [{ name: 'TSS (ppm)', data: tss.values || [] }],
                    annotations: {
                        yaxis: [{ y: 100, borderColor: '#ef4444', strokeDashArray: 2, label: { text: 'Baku Mutu (100)', style: { color: '#fff', background: '#ef4444', fontSize: '8px' } } }]
                    },
                    xaxis: { categories: tss.categories || [], labels: { style: { colors: '#64748b', fontSize: '9px' } } },
                    yaxis: { labels: { style: { colors: '#64748b', fontSize: '9px' } } },
                    grid: { borderColor: '#1e293b', strokeDashArray: 3, padding: { top: -10, bottom: -5, left: 5, right: 5 } },
                    tooltip: { theme: 'dark' }
                };
                renderOrUpdateChart('chartKepatuhanTss', tssOptions);
            }

        } catch (err) {
            console.error('Error loading WCO Dashboard data:', err);
        }
    }
</script>
@endsection
