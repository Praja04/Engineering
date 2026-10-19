<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Mesin Utility | PT BAS</title>
        <link rel="shortcut icon" href="{{ asset('assets/images/logo/kecap.png') }}">

        <!-- CSS Dependencies -->
        <link href="{{ asset('material/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap"
            rel="stylesheet">

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
                --wco-pink: #ec4899;
                --wco-text-muted: #94a3b8;
            }

            html,
            body {
                margin: 0;
                padding: 0;
                width: 100%;
                min-height: 100vh;
                background-color: var(--wco-bg) !important;
                color: #f1f5f9;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                overflow-x: hidden;
            }

            /* Custom Dark Scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
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

            .dashboard-wrapper {
                background-color: var(--wco-bg);
                color: #f1f5f9;
                padding-bottom: 2rem;
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

            /* Card Styling */
            .wco-card {
                background: var(--wco-card-bg);
                border: 1px solid var(--wco-border);
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
                margin-bottom: 12px;
                display: flex;
                flex-direction: column;
                height: 100%;
                position: relative;
                overflow: visible;
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
            }

            .wco-card:hover,
            .wco-card:focus-within {
                border-color: rgba(56, 189, 248, 0.4);
                box-shadow: 0 6px 20px rgba(6, 182, 212, 0.12);
                z-index: 1050;
            }

            [class*="col-"]:has(.wco-card:hover),
            [class*="col-"]:has(.wco-card:focus-within) {
                z-index: 1050;
                position: relative;
            }

            /* ApexCharts Tooltip Stacking & Visibility */
            .apexcharts-canvas {
                overflow: visible !important;
            }

            .apexcharts-tooltip {
                z-index: 999999 !important;
                pointer-events: none !important;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.6) !important;
            }

            .wco-card-header {
                background-color: var(--wco-card-header);
                border-bottom: 1px solid var(--wco-border);
                padding: 8px 12px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                border-top-left-radius: 9px;
                border-top-right-radius: 9px;
            }

            .wco-card-title {
                font-size: 11px;
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
                padding: 10px 12px;
                flex-grow: 1;
            }

            /* KPI Top Cards */
            .kpi-top-card {
                background: var(--wco-card-bg);
                border: 1px solid var(--wco-border);
                border-radius: 10px;
                padding: 9px 11px;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                overflow: hidden;
                transition: all 0.2s ease;
            }

            .kpi-top-card:hover {
                border-color: var(--wco-cyan);
                box-shadow: 0 4px 12px rgba(6, 182, 212, 0.15);
            }

            .kpi-top-header {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 4px;
            }

            .kpi-top-icon {
                width: 28px;
                height: 28px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 16px;
                flex-shrink: 0;
            }

            .kpi-top-label {
                font-size: 10px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #94a3b8;
                line-height: 1.1;
            }

            .kpi-top-val {
                font-size: 17px;
                font-weight: 800;
                color: #ffffff;
                font-family: 'JetBrains Mono', monospace;
                line-height: 1.2;
            }

            .kpi-top-sub {
                font-size: 9px;
                font-weight: 600;
                color: var(--wco-cyan);
                display: flex;
                align-items: center;
                gap: 4px;
                margin-top: 4px;
            }

            /* Inner Machine Stat Tiles */
            .stat-tile {
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(255, 255, 255, 0.06);
                border-radius: 8px;
                padding: 8px 10px;
                transition: all 0.2s ease;
            }

            .stat-tile:hover {
                background: rgba(255, 255, 255, 0.06);
                border-color: rgba(6, 182, 212, 0.3);
            }

            .stat-tile-label {
                font-size: 9.5px;
                font-weight: 600;
                text-transform: uppercase;
                color: var(--wco-text-muted);
                letter-spacing: 0.4px;
            }

            .stat-tile-val {
                font-family: 'JetBrains Mono', monospace;
                font-size: 16px;
                font-weight: 700;
                color: #ffffff;
            }

            .stat-tile-meta {
                font-size: 8.5px;
                color: #64748b;
            }

            /* Pills and badges */
            .pill-badge {
                background: rgba(255, 255, 255, 0.04);
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 4px;
                padding: 3px 6px;
                font-size: 9.5px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            .badge-status-ok {
                background: rgba(16, 185, 129, 0.15) !important;
                color: #10b981 !important;
                border: 1px solid rgba(16, 185, 129, 0.3) !important;
            }

            .badge-status-warn {
                background: rgba(245, 158, 11, 0.15) !important;
                color: #f59e0b !important;
                border: 1px solid rgba(245, 158, 11, 0.3) !important;
            }

            .badge-status-danger {
                background: rgba(239, 68, 68, 0.15) !important;
                color: #ef4444 !important;
                border: 1px solid rgba(239, 68, 68, 0.3) !important;
            }

            /* Filter Controls */
            .filter-ctrl {
                background: #0f172a !important;
                border: 1px solid #1e293b !important;
                color: #f1f5f9 !important;
                font-size: 11px;
                border-radius: 6px;
            }

            .filter-ctrl:focus {
                border-color: #06b6d4 !important;
                box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.25) !important;
                outline: none;
            }

            .btn-theme-toggle {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid #334155;
                color: #f8fafc;
                font-size: 11px;
                padding: 4px 9px;
                border-radius: 6px;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 5px;
                font-weight: 600;
                transition: all 0.2s ease;
            }

            .btn-theme-toggle:hover {
                background: rgba(255, 255, 255, 0.1);
                border-color: #38bdf8;
                color: #38bdf8;
            }

            /* Compact Data Tables */
            .wco-table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                font-size: 10px;
            }

            .wco-table th {
                background-color: rgba(255, 255, 255, 0.03);
                color: #94a3b8;
                font-weight: 700;
                text-transform: uppercase;
                padding: 5px 8px;
                border-bottom: 1px solid var(--wco-border);
                letter-spacing: 0.5px;
            }

            .wco-table td {
                padding: 5px 8px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                color: #e2e8f0;
                font-family: 'JetBrains Mono', monospace;
            }

            .wco-table tr:hover td {
                background-color: rgba(6, 182, 212, 0.05);
            }

            /* Light Mode Styles */
            body.wco-light-mode {
                background-color: #f8fafc !important;
                color: #0f172a !important;
            }

            body.wco-light-mode .dashboard-wrapper {
                background-color: #f8fafc;
                color: #0f172a;
            }

            body.wco-light-mode .wco-header {
                background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 50%, #e2e8f0 100%) !important;
                border: 1px solid #cbd5e1 !important;
                box-shadow: 0 4px 15px rgba(15, 23, 42, 0.05) !important;
            }

            body.wco-light-mode .wco-header-title {
                color: #0f172a !important;
                text-shadow: none !important;
            }

            body.wco-light-mode .wco-header-sub {
                color: #0284c7 !important;
            }

            body.wco-light-mode .wco-header-motto {
                color: #64748b !important;
            }

            body.wco-light-mode .wco-card,
            body.wco-light-mode .kpi-top-card {
                background: #ffffff !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
            }

            body.wco-light-mode .wco-card-header {
                background-color: #f8fafc !important;
                border-bottom: 1px solid #e2e8f0 !important;
            }

            body.wco-light-mode .wco-card-title,
            body.wco-light-mode .kpi-top-val,
            body.wco-light-mode .stat-tile-val {
                color: #0f172a !important;
            }

            body.wco-light-mode .stat-tile {
                background: #f8fafc !important;
                border: 1px solid #e2e8f0 !important;
            }

            body.wco-light-mode .filter-ctrl {
                background: #ffffff !important;
                border: 1px solid #cbd5e1 !important;
                color: #0f172a !important;
            }

            body.wco-light-mode .btn-theme-toggle {
                background: #ffffff !important;
                border-color: #cbd5e1 !important;
                color: #334155 !important;
            }

            body.wco-light-mode .wco-table th {
                background-color: #f8fafc !important;
                color: #475569 !important;
                border-bottom: 1px solid #cbd5e1 !important;
            }

            body.wco-light-mode .wco-table td {
                border-bottom: 1px solid #f1f5f9 !important;
                color: #334155 !important;
            }

            .text-cyan {
                color: #06b6d4 !important;
            }
        </style>
    </head>

    <body class="wco-body">
        <script>
            (function() {
                try {
                    if (localStorage.getItem('utility_machine_theme') === 'light') {
                        document.body.classList.add('wco-light-mode');
                    }
                } catch (e) {}
            })();
        </script>

        <div class="dashboard-wrapper p-2 p-md-3">
            <div class="container-fluid px-1">

                <!-- ======================================================= -->
                <!-- HEADER BAR & FILTERS -->
                <!-- ======================================================= -->
                <div class="wco-header">
                    <div class="row align-items-center g-2">
                        <!-- Left: Branding -->
                        <div class="col-xl-3 col-lg-4 col-md-12 d-flex align-items-center gap-2">
                            <div
                                style="width: 38px; height: 38px; background: rgba(6, 182, 212, 0.12); border: 1px solid rgba(6, 182, 212, 0.35); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="mdi mdi-cog-sync" style="font-size: 22px; color: var(--wco-cyan);"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold" style="letter-spacing: 0.8px; font-size: 15px;">MESIN
                                    UTILITY</h4>
                                <div
                                    style="font-size: 9px; color: var(--wco-cyan); font-weight: 700; letter-spacing: 0.5px;">
                                    OPERASIONAL MESIN UTILITY
                                </div>
                            </div>
                        </div>

                        <!-- Center: Title -->
                        <div class="col-xl-4 col-lg-4 col-md-12 text-center d-none d-lg-block">
                            <div class="wco-header-title">UTILITY MACHINE DASHBOARD</div>
                            <div class="wco-header-sub"> COMPRESSOR • AHU • MDP • GENSET • COOLING TOWER • AIR</div>
                            <div class="wco-header-motto">"Reliability & Efficiency Of Utility Operation"</div>
                        </div>

                        <!-- Right: Filters & Controls -->
                        <div
                            class="col-xl-5 col-lg-4 col-md-12 d-flex align-items-center justify-content-end gap-2 flex-wrap">
                            <!-- Mode Filter -->
                            <select id="filterMode" class="form-select form-select-sm filter-ctrl"
                                style="width: 105px;">
                                <option value="today" selected>Hari ini</option>
                                <option value="mingguan">7 Hari</option>
                                <option value="bulanan">30 Hari</option>
                                <option value="custom">Custom</option>
                            </select>

                            <!-- Single Date Input (Hidden when Today) -->
                            <div id="singleDateBox" class="d-none d-flex align-items-center">
                                <input type="date" id="selectedDate" class="form-control form-control-sm filter-ctrl"
                                    style="width: 125px;">
                            </div>

                            <!-- Custom Date Range Box (Hidden by default) -->
                            <div id="customDateBox" class="d-none d-flex align-items-center gap-1">
                                <input type="date" id="customStartDate"
                                    class="form-control form-control-sm filter-ctrl" style="width: 115px;">
                                <span class="text-muted small">s/d</span>
                                <input type="date" id="customEndDate"
                                    class="form-control form-control-sm filter-ctrl" style="width: 115px;">
                            </div>

                            <!-- Genset Year Dropdown -->
                            <div class="d-flex align-items-center gap-1" title="Filter Tahun Genset">
                                <i class="mdi mdi-generator-mobile text-warning" style="font-size: 14px;"></i>
                                <select id="gensetYearSelect" class="form-select form-select-sm filter-ctrl"
                                    style="width: 85px;">
                                    <option value="2026" selected>2026</option>
                                </select>
                            </div>

                            <!-- Refresh Button -->
                            <button id="btnRefresh" class="btn btn-sm btn-outline-info d-flex align-items-center gap-1"
                                title="Muat Ulang Data">
                                <i class="mdi mdi-refresh" id="refreshIcon"></i>
                            </button>

                            <!-- Theme Toggle Button -->
                            <button id="btnThemeToggle" class="btn-theme-toggle" type="button"
                                title="Ganti Mode Tampilan (Dark / Light)">
                                <i class="mdi mdi-weather-sunny text-warning" id="themeIcon"></i>
                                <span id="themeText">Light</span>
                            </button>

                            <!-- Fullscreen Toggle -->
                            <button id="btnFullscreen"
                                class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                title="Layar Penuh" type="button">
                                <i class="mdi mdi-fullscreen" id="fsIcon"></i>
                            </button>

                            <!-- Portal Button -->
                            <a href="{{ url('home') }}"
                                class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                title="Kembali ke Portal Utility">
                                <i class="mdi mdi-home-outline"></i>
                                <span class="d-none d-md-inline">Portal</span>
                            </a>

                            <!-- Last Updated Badge -->
                            <div class="badge bg-dark border border-secondary px-2 py-1 text-muted"
                                style="font-size: 9px;">
                                <i class="mdi mdi-clock-outline me-1 text-cyan"></i>
                                <span id="lastUpdateBadge">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- TOP ROW: 6 KPI SUMMARY CARDS (1 ROW OF 6 WIDGETS) -->
                <!-- ======================================================= -->
                <div class="row g-2 mb-2">
                    <!-- Card 1: Compressor Fleet -->
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="kpi-top-card">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon"
                                    style="background: rgba(6, 182, 212, 0.15); color: var(--wco-cyan);">
                                    <i class="mdi mdi-storage-tank"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">COMPRESSOR</div>
                                    <div class="text-muted" style="font-size: 8px;">TOTAL AVG RUNNING HOUR</div>
                                </div>
                            </div>
                            <div class="kpi-top-val" id="kpiCompVal">0 <span
                                    style="font-size: 11px; font-weight: normal; color: #94a3b8;">h</span></div>
                            <div class="kpi-top-sub" id="kpiCompSub"
                                style="font-size: 8px; gap: 3px; flex-wrap: wrap;">
                                <i class="mdi mdi-check-circle-outline"></i> RH1: <span id="kpiCompRh1">-</span> |
                                RH3: <span id="kpiCompIr55">-</span> | RH4: <span id="kpiCompGa55">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: AHU Fleet -->
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="kpi-top-card">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon"
                                    style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                                    <i class="mdi mdi-fan"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">SUHU AHU</div>
                                    <div class="text-muted" style="font-size: 8px;">SUHU & AMPERE</div>
                                </div>
                            </div>
                            <div class="kpi-top-val" id="kpiAhuVal">0 <span
                                    style="font-size: 11px; font-weight: normal; color: #94a3b8;">°C</span></div>
                            <div class="kpi-top-sub" id="kpiAhuSub">
                                <i class="mdi mdi-current-ac"></i> Avg: <span id="kpiAhuAmp">-</span> A (AHU 1-4)
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: MDP Power -->
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="kpi-top-card">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon"
                                    style="background: rgba(245, 158, 11, 0.15); color: var(--wco-amber);">
                                    <i class="mdi mdi-lightning-bolt"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">MDP POWER</div>
                                    <div class="text-muted" style="font-size: 8px;">DAYA & ARUS MDP</div>
                                </div>
                            </div>
                            <div class="kpi-top-val text-warning" id="kpiMdpVal">0 <span
                                    style="font-size: 11px; font-weight: normal; color: #94a3b8;">kW</span></div>
                            <div class="kpi-top-sub" id="kpiMdpSub">
                                <i class="mdi mdi-thermometer"></i> Trafo: <span id="kpiMdpTrafo">-</span> °C | <span
                                    id="kpiMdpArus">-</span> A
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Cooling Tower -->
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="kpi-top-card">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon"
                                    style="background: rgba(16, 185, 129, 0.15); color: var(--wco-green);">
                                    <i class="mdi mdi-coolant-temperature"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">COOLING TOWER</div>
                                    <div class="text-muted" style="font-size: 8px;">TEMP & PRESSURE OUT</div>
                                </div>
                            </div>
                            <div class="kpi-top-val text-success" id="kpiCtVal">0 <span
                                    style="font-size: 11px; font-weight: normal; color: #94a3b8;">°C</span></div>
                            <div class="kpi-top-sub" id="kpiCtSub">
                                <i class="mdi mdi-gauge"></i> Press: <span id="kpiCtPress">-</span> Bar
                            </div>
                        </div>
                    </div>

                    <!-- Card 5: Genset Warming Up -->
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="kpi-top-card">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon"
                                    style="background: rgba(236, 72, 153, 0.15); color: var(--wco-pink);">
                                    <i class="mdi mdi-generator-mobile"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">GENSET STATUS</div>
                                    <div class="text-muted" style="font-size: 8px;">WARMING UP TAHUNAN</div>
                                </div>
                            </div>
                            <div class="kpi-top-val" id="kpiGensetVal">0 <span
                                    style="font-size: 11px; font-weight: normal; color: #94a3b8;">RPM</span></div>
                            <div class="kpi-top-sub" id="kpiGensetSub">
                                <i class="mdi mdi-history"></i> <span id="kpiGensetRuns">0</span> Runs | Oil: <span
                                    id="kpiGensetOil">-</span> Bar
                            </div>
                        </div>
                    </div>

                    <!-- Card 6: Water Quality Chemistry -->
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="kpi-top-card">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon"
                                    style="background: rgba(139, 92, 246, 0.15); color: var(--wco-purple);">
                                    <i class="mdi mdi-water-check"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">KUALITAS AIR</div>
                                    <div class="text-muted" style="font-size: 8px;">5 SISTEM AIR UTILITY</div>
                                </div>
                            </div>
                            <div class="kpi-top-val text-info" id="kpiWaterVal">5 / 5 <span
                                    style="font-size: 10px; font-weight: normal; color: #94a3b8;">SYS</span></div>
                            <div class="kpi-top-sub" id="kpiWaterSub">
                                <i class="mdi mdi-flask-outline"></i> Boiler pH: <span id="kpiBoilerPh">-</span> |
                                TDS: <span id="kpiBoilerTds">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- ROW 1: 4 CARDS PER ROW (col-xl-3) -->
                <!-- ======================================================= -->
                <div class="row g-2 mb-2">
                    <!-- 1. COMPRESSOR SECTION (col-xl-3) -->
                    <div class="col-xl-4 col-lg-6 col-12">
                        <div class="wco-card h-100">
                            <div class="wco-card-header">
                                <div class="wco-card-title">
                                    <i class="mdi mdi-gas-cylinder text-cyan"></i>
                                    <span>COMPRESSOR RUNNING HOURS</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-cyan"
                                    id="compPeriodBadge">Hourly Readings</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <!-- 4 Machine Metric Tiles -->
                                <div class="row g-1 mb-2">
                                    <!-- RH 1 (rh1) -->
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="text-center">
                                                <span class="stat-tile-label text-warning"
                                                    style="font-size: 8px;">AVG. RH 1 (rh1)</span>
                                            </div>
                                            <div class="stat-tile-val mt-1 text-center text-warning" id="valCompRh1"
                                                style="font-size: 13px;">0.00</div>
                                            <div class="text-center text-muted" id="compMetaRh1"
                                                style="font-size: 7.5px; line-height: 1.1;">Tgl Terbaru</div>
                                        </div>
                                    </div>
                                    <!-- GA57 (rh2) -->
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="text-center">
                                                <span class="stat-tile-label text-cyan" style="font-size: 8px;">AVG.
                                                    GA57
                                                    (rh2)</span>
                                            </div>
                                            <div class="stat-tile-val mt-1 text-center" id="valCompGa57"
                                                style="font-size: 13px;">0.00</div>
                                            <div class="text-center text-muted" id="compMetaGa57"
                                                style="font-size: 7.5px; line-height: 1.1;">Tgl Terbaru</div>
                                        </div>
                                    </div>
                                    <!-- IR55 (rh3) -->
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="text-center">
                                                <span class="stat-tile-label text-info" style="font-size: 8px;">AVG.
                                                    IR55
                                                    (rh3)</span>
                                            </div>
                                            <div class="stat-tile-val mt-1 text-center" id="valCompIr55"
                                                style="font-size: 13px;">0.00</div>
                                            <div class="text-center text-muted" id="compMetaIr55"
                                                style="font-size: 7.5px; line-height: 1.1;">Tgl Terbaru</div>
                                        </div>
                                    </div>
                                    <!-- GA55 (rh4) -->
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="text-center">
                                                <span class="stat-tile-label text-success"
                                                    style="font-size: 8px;">AVG. GA55 (rh4)</span>
                                            </div>
                                            <div class="stat-tile-val mt-1 text-center" id="valCompGa55"
                                                style="font-size: 13px;">0.00</div>
                                            <div class="text-center text-muted" id="compMetaGa55"
                                                style="font-size: 7.5px; line-height: 1.1;">Tgl Terbaru</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Compressor Chart with Data Labels (No Legend) -->
                                <div id="chartCompressor" style="min-height: 190px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. AHU SECTION (col-xl-3) -->
                    <div class="col-xl-4 col-lg-6 col-12">
                        <div class="wco-card h-100">
                            <div class="wco-card-header">
                                <div class="wco-card-title">
                                    <i class="mdi mdi-fan text-primary"></i>
                                    <span>AHU AMPERE & SUHU TEMP OUT</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-primary"
                                    id="ahuPeriodBadge">AHU 1 - 4</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <!-- Top: Ampere AHU 1 - 4 & Suhu combined in 1 row -->
                                <div class="row g-1 mb-2 align-items-stretch">
                                    <div class="col-8">
                                        <div class="row g-1">
                                            <div class="col-3">
                                                <div class="stat-tile text-center py-1 px-0">
                                                    <div class="stat-tile-label" style="font-size: 7.5px;">AVG. AHU 1
                                                    </div>
                                                    <div class="stat-tile-val mt-1 text-info" id="valAhuAmp1"
                                                        style="font-size: 11px;">0</div>
                                                    <div class="stat-tile-meta" style="font-size: 7px;">Amp</div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="stat-tile text-center py-1 px-0">
                                                    <div class="stat-tile-label" style="font-size: 7.5px;">AVG. AHU 2
                                                    </div>
                                                    <div class="stat-tile-val mt-1 text-info" id="valAhuAmp2"
                                                        style="font-size: 11px;">0</div>
                                                    <div class="stat-tile-meta" style="font-size: 7px;">Amp</div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="stat-tile text-center py-1 px-0">
                                                    <div class="stat-tile-label" style="font-size: 7.5px;">AVG. AHU 3
                                                    </div>
                                                    <div class="stat-tile-val mt-1 text-info" id="valAhuAmp3"
                                                        style="font-size: 11px;">0</div>
                                                    <div class="stat-tile-meta" style="font-size: 7px;">Amp</div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="stat-tile text-center py-1 px-0">
                                                    <div class="stat-tile-label" style="font-size: 7.5px;">AVG. AHU 4
                                                    </div>
                                                    <div class="stat-tile-val mt-1 text-info" id="valAhuAmp4"
                                                        style="font-size: 11px;">0</div>
                                                    <div class="stat-tile-meta" style="font-size: 7px;">Amp</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div
                                            class="stat-tile text-center py-1 px-1 h-100 d-flex flex-column justify-content-center">
                                            <div class="stat-tile-label" style="font-size: 7.5px;">AVG. SUHU OUT</div>
                                            <div class="stat-tile-val mt-1 text-cyan" id="valAhuTempOut"
                                                style="font-size: 11px;">0.0 °C</div>
                                            <div class="stat-tile-meta" style="font-size: 7px;" id="ahuTempOutMeta">
                                                Min: 0° | Max: 0°</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- AHU Chart (No Legend) -->
                                <div id="chartAhu" style="min-height: 190px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. COOLING TOWER SECTION (col-xl-3) -->
                    <div class="col-xl-4 col-lg-6 col-12">
                        <div class="wco-card h-100">
                            <div class="wco-card-header">
                                <div class="wco-card-title">
                                    <i class="mdi mdi-coolant-temperature text-success"></i>
                                    <span>COOLING TOWER (SUHU & PRESS)</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-success"
                                    id="ctPeriodBadge">Tiap Jam & Avg</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <!-- 2 Highlight Tiles -->
                                <div class="row g-1 mb-2">
                                    <div class="col-6">
                                        <div class="stat-tile py-1 px-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="stat-tile-label text-cyan" style="font-size: 8px;">AVG.
                                                    TEMP
                                                    OUT</span>
                                                <i class="mdi mdi-thermometer text-cyan"></i>
                                            </div>
                                            <div class="stat-tile-val mt-1 text-cyan" id="valCtTempOut"
                                                style="font-size: 13px;">0.00 °C</div>
                                            <div class="stat-tile-meta d-flex justify-content-between"
                                                style="font-size: 7.5px;">
                                                <span>Avg: <strong id="avgCtTemp">0</strong>°C</span>
                                                <span id="rangeCtTemp">Max: 0</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-tile py-1 px-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="stat-tile-label text-success"
                                                    style="font-size: 8px;">AVG. PRESSURE OUT</span>
                                                <i class="mdi mdi-gauge text-success"></i>
                                            </div>
                                            <div class="stat-tile-val mt-1 text-success" id="valCtPressOut"
                                                style="font-size: 13px;">0.00 Bar</div>
                                            <div class="stat-tile-meta d-flex justify-content-between"
                                                style="font-size: 7.5px;">
                                                <span>Avg: <strong id="avgCtPress">0</strong>Bar</span>
                                                <span id="rangeCtPress">Max: 0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cooling Tower Dual Axis Chart (No Legend) -->
                                <div id="chartCoolingTower" style="min-height: 190px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. MDP ELECTRICAL SECTION (col-xl-4) -->
                    <div class="col-xl-4 col-lg-6 col-12">
                        <div class="wco-card h-100">
                            <div class="wco-card-header">
                                <div class="wco-card-title">
                                    <i class="mdi mdi-transmission-tower text-warning"></i>
                                    <span>MDP POWER & KELISTRIKAN</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-warning"
                                    id="mdpStatusBadge">Status: Normal</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <!-- MDP Electrical Metrics -->
                                <div class="row g-1 mb-2">
                                    <div class="col-4">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="stat-tile-label text-warning" style="font-size: 8px;">AVG.
                                                ARUS
                                                RATA2</div>
                                            <div class="stat-tile-val mt-1" id="valMdpArus" style="font-size: 12px;">
                                                0.0 A</div>
                                            <div class="stat-tile-meta" id="valMdpArusPhases"
                                                style="font-size: 7px;">I1: - | I2: -</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="stat-tile-label text-info" style="font-size: 8px;">AVG.
                                                TEGANGAN
                                            </div>
                                            <div class="stat-tile-val mt-1" id="valMdpTegangan"
                                                style="font-size: 12px;">0.0 V</div>
                                            <div class="stat-tile-meta" id="valMdpVoltPhases"
                                                style="font-size: 7px;">V1: - | V2: -</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="stat-tile-label text-success" style="font-size: 8px;">AVG.
                                                DAYA
                                                TOTAL</div>
                                            <div class="stat-tile-val mt-1" id="valMdpDaya" style="font-size: 12px;">
                                                0.0 kW</div>
                                            <div class="stat-tile-meta" id="valMdpDayaPhases"
                                                style="font-size: 7px;">P1: - | P2: -</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- MDP Chart (No Legend) -->
                                <div id="chartMdp" style="min-height: 190px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 5. MDP & TRAFO THERMAL (col-xl-4) -->
                    <div class="col-xl-4 col-lg-6 col-12">
                        <div class="wco-card h-100">
                            <div class="wco-card-header">
                                <div class="wco-card-title">
                                    <i class="mdi mdi-thermometer-lines text-danger"></i>
                                    <span>MDP SUHU MESIN DAN SUHU RUANG</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-danger"
                                    id="mdpThermalBadge">Suhu & Trafo</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <!-- MDP Thermal Metrics -->
                                <div class="row g-1 mb-2">
                                    <div class="col-4">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="stat-tile-label text-danger" style="font-size: 7.5px;">AVG.
                                                TEMP
                                                TRAFO</div>
                                            <div class="stat-tile-val mt-1" id="valMdpTempTrafo"
                                                style="font-size: 12px;">0.0 °C</div>
                                            <div class="stat-tile-meta" style="font-size: 7px;">Oil: <strong
                                                    id="valMdpLevelOil" class="text-success">OK</strong></div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="stat-tile-label text-warning" style="font-size: 7.5px;">AVG.
                                                RUANG
                                                TRAFO</div>
                                            <div class="stat-tile-val mt-1" id="valMdpSuhuTrafo"
                                                style="font-size: 12px;">0.0 °C</div>
                                            <div class="stat-tile-meta" style="font-size: 7px;">Cond: <strong
                                                    id="valMdpTrafoCond" class="text-warning">Normal</strong></div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="stat-tile-label text-pink" style="font-size: 7.5px;">AVG.
                                                RUANG
                                                GENSET</div>
                                            <div class="stat-tile-val mt-1" id="valMdpSuhuGenset"
                                                style="font-size: 12px;">0.0 °C</div>
                                            <div class="stat-tile-meta" style="font-size: 7px;">Cond: <strong
                                                    id="valMdpGensetCond" class="text-pink">Normal</strong></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Thermal Trend Chart (No Legend) -->
                                <div id="chartMdpThermal" style="min-height: 190px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 6. GENSET WARMING UP (col-xl-4) -->
                    <div class="col-xl-4 col-lg-6 col-12">
                        <div class="wco-card h-100">
                            <div class="wco-card-header">
                                <div class="wco-card-title">
                                    <i class="mdi mdi-generator-mobile text-pink"></i>
                                    <span>GENSET WARMING UP</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-pink"
                                    id="gensetHeaderYearBadge">Tahun 2026</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <!-- 4 Genset Stat Tiles -->
                                <div class="row g-1 mb-2">
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <div class="stat-tile-label" style="font-size: 8px;">AVG. SPEED</div>
                                            <div class="stat-tile-val mt-1 text-info" id="gensetSpeedVal"
                                                style="font-size: 12px;">0</div>
                                            <div class="stat-tile-meta" style="font-size: 7px;">RPM</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <div class="stat-tile-label" style="font-size: 8px;">AVG. TEMP</div>
                                            <div class="stat-tile-val mt-1 text-warning" id="gensetTempVal"
                                                style="font-size: 12px;">0</div>
                                            <div class="stat-tile-meta" style="font-size: 7px;">°C</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <div class="stat-tile-label" style="font-size: 8px;">AVG. OIL PRESS</div>
                                            <div class="stat-tile-val mt-1 text-success" id="gensetOilVal"
                                                style="font-size: 12px;">0</div>
                                            <div class="stat-tile-meta" style="font-size: 7px;">Bar</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Genset Chart (No Legend) -->
                                <div id="chartGenset" style="min-height: 190px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 7. KUALITAS AIR COOLING TOWER (col-xl-4) -->
                    <div class="col-xl-4 col-lg-6 col-12">
                        <div class="wco-card h-100">
                            <div class="wco-card-header">
                                <div class="wco-card-title">
                                    <i class="mdi mdi-coolant-temperature text-cyan"></i>
                                    <span>KUALITAS AIR COOLING TOWER</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-cyan">Cooling Tower</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <div class="row g-1 mb-2">
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-cyan" style="font-size: 8px;">pH</span>
                                            <div class="stat-tile-val mt-1" style="font-size: 12px;" id="wCtPh">
                                                0.0</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: 7.5 -
                                                8.5</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-info" style="font-size: 8px;">TDS</span>
                                            <div class="stat-tile-val mt-1 text-info" style="font-size: 11px;"
                                                id="wCtTds">0 ppm</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: ≤ 2500
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-warning"
                                                style="font-size: 8px;">HARDNESS</span>
                                            <div class="stat-tile-val mt-1 text-warning" style="font-size: 11px;"
                                                id="wCtHardness">0 ppm</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: ≤ 800
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="chartWaterCt" style="min-height: 190px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 8. KUALITAS AIR REVERSE OSMOSIS (col-xl-4) -->
                    <div class="col-xl-4 col-lg-6 col-12">
                        <div class="wco-card h-100">
                            <div class="wco-card-header">
                                <div class="wco-card-title">
                                    <i class="mdi mdi-water-plus text-primary"></i>
                                    <span>KUALITAS AIR REVERSE OSMOSIS</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-primary">RO System</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <div class="row g-1 mb-2">
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-primary" style="font-size: 8px;">AVG.
                                                pH</span>
                                            <div class="stat-tile-val mt-1" style="font-size: 12px;" id="wRoPh">
                                                0.0</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: 6.5 -
                                                7.5</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-info" style="font-size: 8px;">AVG.
                                                TDS</span>
                                            <div class="stat-tile-val mt-1 text-info" style="font-size: 11px;"
                                                id="wRoTds">0 ppm</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: ≤ 50
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-warning" style="font-size: 8px;">AVG.
                                                HARDNESS</span>
                                            <div class="stat-tile-val mt-1 text-warning" style="font-size: 11px;"
                                                id="wRoHardness">0 ppm</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: 0 - 10
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="chartWaterRo" style="min-height: 190px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 9. KUALITAS AIR WATER SOFTENER (col-xl-4) -->
                    <div class="col-xl-4 col-lg-6 col-12">
                        <div class="wco-card h-100">
                            <div class="wco-card-header">
                                <div class="wco-card-title">
                                    <i class="mdi mdi-water-sync text-success"></i>
                                    <span>KUALITAS AIR WATER SOFTENER</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-success">Softener</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <div class="row g-1 mb-2">
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-success" style="font-size: 8px;">AVG.
                                                pH</span>
                                            <div class="stat-tile-val mt-1" style="font-size: 12px;" id="wWsPh">
                                                0.0</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: 6.8 -
                                                7.8</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-info" style="font-size: 8px;">AVG.
                                                TDS</span>
                                            <div class="stat-tile-val mt-1 text-info" style="font-size: 11px;"
                                                id="wWsTds">0 ppm</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: ≤ 300
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-warning" style="font-size: 8px;">AVG.
                                                HARDNESS</span>
                                            <div class="stat-tile-val mt-1 text-warning" style="font-size: 11px;"
                                                id="wWsHardness">0 ppm</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: 0 - 10
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="chartWaterWs" style="min-height: 190px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 10. KUALITAS AIR BOILER (col-xl-6) -->
                    <div class="col-xl-6 col-lg-6 col-12">
                        <div class="wco-card h-100">
                            <div class="wco-card-header">
                                <div class="wco-card-title">
                                    <i class="mdi mdi-fire-circle text-warning"></i>
                                    <span>KUALITAS AIR BOILER</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-warning">Boiler System</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <div class="row g-1 mb-2">
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-warning" style="font-size: 8px;">AVG.
                                                pH</span>
                                            <div class="stat-tile-val mt-1" style="font-size: 12px;" id="wBoilerPh">
                                                0.0</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: 10.5 -
                                                11.5</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-info" style="font-size: 8px;">AVG.
                                                TDS</span>
                                            <div class="stat-tile-val mt-1 text-info" style="font-size: 11px;"
                                                id="wBoilerTds">0 ppm</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: ≤ 3500
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-danger" style="font-size: 8px;">AVG.
                                                HARDNESS</span>
                                            <div class="stat-tile-val mt-1 text-danger" style="font-size: 11px;"
                                                id="wBoilerHardness">0 ppm</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: 0 ppm
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="chartWaterBoiler" style="min-height: 190px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 11. KUALITAS AIR FRESH WATER (col-xl-6) -->
                    <div class="col-xl-6 col-lg-6 col-12">
                        <div class="wco-card h-100">
                            <div class="wco-card-header">
                                <div class="wco-card-title">
                                    <i class="mdi mdi-water text-purple"></i>
                                    <span>KUALITAS AIR FRESH WATER</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-purple">Fresh Water</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <div class="row g-1 mb-2">
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-purple" style="font-size: 8px;">AVG.
                                                pH</span>
                                            <div class="stat-tile-val mt-1" style="font-size: 12px;" id="wFwPh">
                                                0.0</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: 6.5 -
                                                8.5</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-info" style="font-size: 8px;">AVG.
                                                TDS</span>
                                            <div class="stat-tile-val mt-1 text-info" style="font-size: 11px;"
                                                id="wFwTds">0 ppm</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: ≤ 500
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-warning" style="font-size: 8px;">AVG.
                                                HARDNESS</span>
                                            <div class="stat-tile-val mt-1 text-warning" style="font-size: 11px;"
                                                id="wFwHardness">0 ppm</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: ≤ 150
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="chartWaterFw" style="min-height: 190px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- JS Dependencies -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <!-- Dashboard Core Script (jQuery) -->
        <script>
            $(document).ready(function() {
                // Theme setup
                function updateThemeUI() {
                    const isLight = $('body').hasClass('wco-light-mode');
                    if (isLight) {
                        $('#themeIcon').attr('class', 'mdi mdi-weather-night text-info');
                        $('#themeText').text('Dark');
                    } else {
                        $('#themeIcon').attr('class', 'mdi mdi-weather-sunny text-warning');
                        $('#themeText').text('Light');
                    }
                }
                updateThemeUI();

                $('#btnThemeToggle').on('click', function() {
                    $('body').toggleClass('wco-light-mode');
                    const isLight = $('body').hasClass('wco-light-mode');
                    localStorage.setItem('utility_machine_theme', isLight ? 'light' : 'dark');
                    updateThemeUI();
                    renderAllCharts(); // Redraw charts with theme color adjustments
                });

                // Fullscreen setup
                $('#btnFullscreen').on('click', function() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(function() {});
                        $('#fsIcon').attr('class', 'mdi mdi-fullscreen-exit');
                    } else {
                        if (document.exitFullscreen) document.exitFullscreen();
                        $('#fsIcon').attr('class', 'mdi mdi-fullscreen');
                    }
                });

                // Set default date to today
                const todayStr = new Date().toISOString().split('T')[0];
                $('#selectedDate').val(todayStr);
                $('#customStartDate').val(todayStr);
                $('#customEndDate').val(todayStr);

                $('#filterMode').on('change', function() {
                    const mode = $(this).val();
                    if (mode === 'today') {
                        $('#singleDateBox').addClass('d-none');
                        $('#customDateBox').addClass('d-none');
                    } else if (mode === 'custom') {
                        $('#singleDateBox').addClass('d-none');
                        $('#customDateBox').removeClass('d-none');
                    } else {
                        $('#singleDateBox').removeClass('d-none');
                        $('#customDateBox').addClass('d-none');
                    }
                    loadDashboardData();
                });

                $('#selectedDate, #customStartDate, #customEndDate').on('change', function() {
                    loadDashboardData();
                });
                $('#gensetYearSelect').on('change', function() {
                    loadGensetSection();
                });
                $('#btnRefresh').on('click', function() {
                    loadDashboardData();
                });

                // Chart instances holder
                let charts = {
                    comp: null,
                    ahu: null,
                    mdp: null,
                    mdpThermal: null,
                    ct: null,
                    genset: null,
                    waterCt: null,
                    waterRo: null,
                    waterWs: null,
                    waterBoiler: null,
                    waterFw: null
                };


                function isThemeLight() {
                    return $('body').hasClass('wco-light-mode');
                }

                function getChartTheme() {
                    const light = isThemeLight();
                    return {
                        mode: light ? 'light' : 'dark',
                        foreColor: light ? '#475569' : '#94a3b8',
                        fontFamily: 'Inter, sans-serif'
                    };
                }

                // Filter Parameters Helper
                function getFilterParams() {
                    const mode = $('#filterMode').val();
                    const tanggal = $('#selectedDate').val();
                    const startDate = $('#customStartDate').val();
                    const endDate = $('#customEndDate').val();
                    const gensetYear = $('#gensetYearSelect').val();

                    let params = {
                        mode: mode,
                        tanggal: tanggal,
                        genset_year: gensetYear
                    };
                    if (mode === 'custom') {
                        params.start_date = startDate;
                        params.end_date = endDate;
                    }
                    return params;
                }

                // Generic Section Fetcher via fetch API
                async function fetchMachineSection(section, extraParams = {}) {
                    try {
                        const baseParams = getFilterParams();
                        const queryObj = Object.assign({}, baseParams, extraParams, {
                            section: section
                        });
                        const queryStr = new URLSearchParams(queryObj).toString();
                        const url = `{{ route('utility.dashboard_machine_data') }}?${queryStr}`;

                        const res = await fetch(url);
                        if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                        const data = await res.json();

                        if (data.last_update) {
                            $('#lastUpdateBadge').text(data.last_update);
                        }
                        if (data.selected_date && $('#selectedDate').val() !== data.selected_date && $(
                                '#filterMode').val() !== 'custom') {
                            $('#selectedDate').val(data.selected_date);
                        }
                        return data;
                    } catch (err) {
                        console.error(`Error loading section ${section}:`, err);
                        return null;
                    }
                }

                // 1. COMPRESSOR UI & LOADER
                function updateCompressorUI(comp) {
                    $('#kpiCompVal').html(
                        `${comp.ga57_avg} <span style="font-size: 11px; font-weight: normal; color: #94a3b8;">h</span>`
                    );
                    $('#kpiCompRh1').text(comp.rh1_avg ? `${comp.rh1_avg}h` : '-');
                    $('#kpiCompIr55').text(`${comp.ir55_avg}h`);
                    $('#kpiCompGa55').text(`${comp.ga55_avg}h`);

                    // Card metric tiles: tampilkan AVG data tanggal terbaru (bukan total periode)
                    const rh1Val = (comp.rh1_latest_avg !== undefined) ? comp.rh1_latest_avg : (comp.rh1_avg || 0);
                    const ga57Val = (comp.ga57_latest_avg !== undefined) ? comp.ga57_latest_avg : comp.ga57_avg;
                    const ir55Val = (comp.ir55_latest_avg !== undefined) ? comp.ir55_latest_avg : comp.ir55_avg;
                    const ga55Val = (comp.ga55_latest_avg !== undefined) ? comp.ga55_latest_avg : comp.ga55_avg;

                    $('#valCompRh1').text(Number(rh1Val).toLocaleString('id-ID', {
                        minimumFractionDigits: 1,
                        maximumFractionDigits: 2
                    }));
                    $('#valCompGa57').text(Number(ga57Val).toLocaleString('id-ID', {
                        minimumFractionDigits: 1,
                        maximumFractionDigits: 2
                    }));
                    $('#valCompIr55').text(Number(ir55Val).toLocaleString('id-ID', {
                        minimumFractionDigits: 1,
                        maximumFractionDigits: 2
                    }));
                    $('#valCompGa55').text(Number(ga55Val).toLocaleString('id-ID', {
                        minimumFractionDigits: 1,
                        maximumFractionDigits: 2
                    }));

                    const dateMeta = comp.latest_date_formatted ? `Tgl: ${comp.latest_date_formatted}` : (comp
                        .latest_date ? `Tgl: ${comp.latest_date}` : 'Tgl Terbaru');
                    $('#compMetaRh1').text(dateMeta);
                    $('#compMetaGa57').text(dateMeta);
                    $('#compMetaIr55').text(dateMeta);
                    $('#compMetaGa55').text(dateMeta);
                }

                async function loadCompressorSection() {
                    const data = await fetchMachineSection('compressor');
                    if (!data || !data.compressor) return;
                    updateCompressorUI(data.compressor.summary);
                    renderCompressorChart(data.compressor);
                }

                // 2. AHU UI & LOADER
                function updateAhuUI(ahu) {
                    $('#kpiAhuVal').html(
                        `${ahu.temp_out_avg} <span style="font-size: 11px; font-weight: normal; color: #94a3b8;">°C</span>`
                    );
                    const avgTotalAmp = ((ahu.ampere_1_avg + ahu.ampere_2_avg + ahu.ampere_3_avg + ahu.ampere_4_avg) /
                        4).toFixed(1);
                    $('#kpiAhuAmp').text(avgTotalAmp);

                    $('#valAhuAmp1').text(ahu.ampere_1_avg);
                    $('#valAhuAmp2').text(ahu.ampere_2_avg);
                    $('#valAhuAmp3').text(ahu.ampere_3_avg);
                    $('#valAhuAmp4').text(ahu.ampere_4_avg);
                    $('#valAhuTempOut').text(`${ahu.temp_out_avg} °C`);
                    $('#ahuTempOutMeta').text(`Min: ${ahu.temp_out_min}°C | Max: ${ahu.temp_out_max}°C`);
                }

                async function loadAhuSection() {
                    const data = await fetchMachineSection('ahu');
                    if (!data || !data.ahu) return;
                    updateAhuUI(data.ahu.summary);
                    renderAhuChart(data.ahu);
                }

                // 3. MDP UI & LOADER
                function updateMdpUI(mdp) {
                    $('#kpiMdpVal').html(
                        `${mdp.daya_total} <span style="font-size: 11px; font-weight: normal; color: #94a3b8;">kW</span>`
                    );
                    $('#kpiMdpTrafo').text(mdp.temperatur_transformator);
                    $('#kpiMdpArus').text(mdp.arus_rata_rata);

                    $('#valMdpArus').text(`${mdp.arus_rata_rata} A`);
                    $('#valMdpTegangan').text(`${mdp.tegangan_rata_rata} V`);
                    $('#valMdpDaya').text(`${mdp.daya_total} kW`);
                    $('#valMdpTempTrafo').text(`${mdp.temperatur_transformator} °C`);
                    $('#valMdpSuhuTrafo').text(`${mdp.suhu_ruang_trafo} °C`);
                    $('#valMdpSuhuGenset').text(`${mdp.suhu_ruang_genset} °C`);

                    if (mdp.latest) {
                        $('#valMdpArusPhases').text(
                            `I1: ${mdp.latest.arus_i1} | I2: ${mdp.latest.arus_i2} | I3: ${mdp.latest.arus_i3}`);
                        $('#valMdpVoltPhases').text(
                            `V1: ${mdp.latest.tegangan_v1} | V2: ${mdp.latest.tegangan_v2} | V3: ${mdp.latest.tegangan_v3}`
                        );
                        $('#valMdpDayaPhases').text(
                            `P1: ${mdp.latest.daya_p1} | P2: ${mdp.latest.daya_p2} | P3: ${mdp.latest.daya_p3}`);
                        $('#valMdpLevelOil').text(mdp.latest.level_oil ? mdp.latest.level_oil.toUpperCase() : 'OK');
                    }

                    const trafoTemp = mdp.temperatur_transformator;
                    if (trafoTemp > 75) {
                        $('#mdpStatusBadge').text('Warning: Suhu Trafo Tinggi').attr('class', 'badge bg-danger');
                    } else {
                        $('#mdpStatusBadge').text('Status: Normal').attr('class',
                            'badge bg-dark border border-secondary text-warning');
                    }
                    $('#valMdpTrafoCond').text(mdp.suhu_ruang_trafo > 45 ? 'Panas' : 'Normal');
                    $('#valMdpGensetCond').text(mdp.suhu_ruang_genset > 45 ? 'Panas' : 'Normal');
                }

                async function loadMdpSection() {
                    const data = await fetchMachineSection('mdp');
                    if (!data || !data.mdp) return;
                    updateMdpUI(data.mdp.summary);
                    renderMdpCharts(data.mdp, data.selected_date);
                }

                // 4. COOLING TOWER UI & LOADER
                function updateCtUI(ct) {
                    $('#kpiCtVal').html(
                        `${ct.temp_out_avg} <span style="font-size: 11px; font-weight: normal; color: #94a3b8;">°C</span>`
                    );
                    $('#kpiCtPress').text(ct.pressure_out_avg);

                    $('#valCtTempOut').text(`${ct.temp_out_avg} °C`);
                    $('#avgCtTemp').text(ct.temp_out_avg);
                    $('#rangeCtTemp').text(`Min: ${ct.temp_out_min} | Max: ${ct.temp_out_max}`);

                    $('#valCtPressOut').text(`${ct.pressure_out_avg} Bar`);
                    $('#avgCtPress').text(ct.pressure_out_avg);
                    $('#rangeCtPress').text(`Min: ${ct.pressure_out_min} | Max: ${ct.pressure_out_max}`);
                }

                async function loadCoolingTowerSection() {
                    const data = await fetchMachineSection('cooling_tower');
                    if (!data || !data.cooling_tower) return;
                    updateCtUI(data.cooling_tower.summary);
                    renderCtChart(data.cooling_tower);
                }

                // 5. GENSET UI & LOADER
                function updateGensetUI(genset) {
                    if (genset.available_years && genset.available_years.length > 0) {
                        const $gensetSelect = $('#gensetYearSelect');
                        if ($gensetSelect.children().length === 0) {
                            $.each(genset.available_years, function(_, yr) {
                                $gensetSelect.append($('<option>', {
                                    value: yr,
                                    text: yr,
                                    selected: (yr == genset.year)
                                }));
                            });
                        }
                    }

                    $('#kpiGensetVal').html(
                        `${genset.avg_speed} <span style="font-size: 11px; font-weight: normal; color: #94a3b8;">RPM</span>`
                    );
                    $('#kpiGensetRuns').text(genset.total_runs);
                    $('#kpiGensetOil').text(genset.avg_oil_pressure);

                    $('#gensetHeaderYearBadge').text(`Tahun ${genset.year}`);
                    $('#gensetSpeedVal').text(genset.avg_speed);
                    $('#gensetTempVal').text(`${genset.avg_temperature} °C`);
                    $('#gensetOilVal').text(`${genset.avg_oil_pressure} Bar`);

                    const $tbody = $('#gensetTableBody');
                    $tbody.empty();
                    if (genset.runs && genset.runs.length > 0) {
                        $('#gensetTableCount').text(`${genset.runs.length} Uji Tercatat`);
                        $.each(genset.runs, function(_, r) {
                            $tbody.append(`
                                <tr>
                                    <td><strong class="text-cyan">${r.tanggal}</strong></td>
                                    <td>${r.jam}</td>
                                    <td class="text-end fw-bold text-info">${r.engine_speed}</td>
                                    <td class="text-end text-warning">${r.engine_temperature}</td>
                                    <td class="text-end text-success">${r.engine_oil_pressure}</td>
                                    <td class="text-center"><span class="pill-badge badge-status-ok">${r.status || 'OK'}</span></td>
                                </tr>
                            `);
                        });
                    } else {
                        $('#gensetTableCount').text('0 Record');
                        $tbody.html(
                            `<tr><td colspan="6" class="text-center text-muted py-3">Belum ada uji warming up genset pada tahun ${genset.year}.</td></tr>`
                        );
                    }
                }

                async function loadGensetSection() {
                    const data = await fetchMachineSection('genset');
                    if (!data || !data.genset) return;
                    updateGensetUI(data.genset);
                    renderGensetChart(data.genset);
                }

                // 6. WATER SYSTEMS UI & LOADER
                function updateWaterUI(ws) {
                    // Cooling Tower
                    $('#wCtPh').text(ws.cooling_tower.ph);
                    $('#wCtTds').html(`${ws.cooling_tower.tds} <span style="font-size: 9px;">ppm</span>`);
                    $('#wCtHardness').html(`${ws.cooling_tower.hardness} <span style="font-size: 9px;">ppm</span>`);
                    // Reverse Osmosis
                    $('#wRoPh').text(ws.reverse_osmosis.ph);
                    $('#wRoTds').html(`${ws.reverse_osmosis.tds} <span style="font-size: 9px;">ppm</span>`);
                    $('#wRoHardness').html(`${ws.reverse_osmosis.hardness} <span style="font-size: 9px;">ppm</span>`);
                    // Water Softener
                    $('#wWsPh').text(ws.water_softener.ph);
                    $('#wWsTds').html(`${ws.water_softener.tds} <span style="font-size: 9px;">ppm</span>`);
                    $('#wWsHardness').html(`${ws.water_softener.hardness} <span style="font-size: 9px;">ppm</span>`);
                    // Boiler
                    $('#wBoilerPh').text(ws.boiler.ph);
                    $('#wBoilerTds').html(`${ws.boiler.tds} <span style="font-size: 9px;">ppm</span>`);
                    $('#wBoilerHardness').html(`${ws.boiler.hardness} <span style="font-size: 9px;">ppm</span>`);
                    // Fresh Water
                    $('#wFwPh').text(ws.fresh_water.ph);
                    $('#wFwTds').html(`${ws.fresh_water.tds} <span style="font-size: 9px;">ppm</span>`);
                    $('#wFwHardness').html(`${ws.fresh_water.hardness} <span style="font-size: 9px;">ppm</span>`);

                    // Top KPI strip water values
                    $('#kpiBoilerPh').text(ws.boiler.ph);
                    $('#kpiBoilerTds').text(`${ws.boiler.tds} ppm`);
                }

                async function loadWaterSection() {
                    const data = await fetchMachineSection('water');
                    if (!data || !data.analisis_utility) return;
                    updateWaterUI(data.analisis_utility.water_systems);
                    renderWaterCharts(data.analisis_utility, data.selected_date);
                }

                // Master Loader: Parallel Asynchronous Execution
                async function loadDashboardData() {
                    $('#refreshIcon').addClass('mdi-spin');
                    try {
                        await Promise.allSettled([
                            loadCompressorSection(),
                            loadAhuSection(),
                            loadMdpSection(),
                            loadCoolingTowerSection(),
                            loadGensetSection(),
                            loadWaterSection()
                        ]);
                    } catch (e) {
                        console.error('Error running asynchronous telemetry loaders:', e);
                    } finally {
                        $('#refreshIcon').removeClass('mdi-spin');
                    }
                }

                // 1. COMPRESSOR CHART RENDERER
                function renderCompressorChart(compData) {
                    if (!compData) return;
                    const chartTheme = getChartTheme();
                    const isHourly = ($('#filterMode').val() === 'today' || $('#filterMode').val() === 'harian');
                    $('#compPeriodBadge').text(isHourly ? 'Hourly Readings' : 'Daily Trends');

                    let compCategories = [];
                    let compSeries = [];

                    if (isHourly) {
                        compCategories = compData.hourly.map(h => h.jam);
                        compSeries = [{
                                name: 'RH 1 (rh1)',
                                data: compData.hourly.map(h => h.rh1)
                            },
                            {
                                name: 'GA57 (rh2)',
                                data: compData.hourly.map(h => h.ga57)
                            },
                            {
                                name: 'IR55 (rh3)',
                                data: compData.hourly.map(h => h.ir55)
                            },
                            {
                                name: 'GA55 (rh4)',
                                data: compData.hourly.map(h => h.ga55)
                            }
                        ];
                    } else {
                        compCategories = compData.daily_trends.map(t => t.tanggal);
                        compSeries = [{
                                name: 'RH 1 Avg (rh1)',
                                data: compData.daily_trends.map(t => t.rh1_avg)
                            },
                            {
                                name: 'GA57 Avg (rh2)',
                                data: compData.daily_trends.map(t => t.ga57_avg)
                            },
                            {
                                name: 'IR55 Avg (rh3)',
                                data: compData.daily_trends.map(t => t.ir55_avg)
                            },
                            {
                                name: 'GA55 Avg (rh4)',
                                data: compData.daily_trends.map(t => t.ga55_avg)
                            }
                        ];
                    }

                    const compOptions = {
                        series: compSeries,
                        chart: {
                            type: 'line',
                            height: 190,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: chartTheme,
                        colors: ['#f59e0b', '#06b6d4', '#3b82f6', '#10b981'],
                        stroke: {
                            curve: 'smooth',
                            width: 2.5
                        },
                        markers: {
                            size: 4,
                            hover: {
                                size: 6
                            },
                            showNullDataPoints: false
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -6,
                            background: {
                                enabled: false
                            },
                            style: {
                                fontSize: '8px',
                                fontFamily: 'JetBrains Mono, monospace',
                                fontWeight: '600',
                                colors: [isThemeLight() ? '#334155' : '#cbd5e1']
                            },
                            formatter: function(val) {
                                if (val === null || val === undefined || val === '') return '';
                                if (val === 0) return '';
                                return Number.isInteger(val) ? val : Number(val).toFixed(1);
                            }
                        },
                        legend: {
                            show: false
                        },
                        xaxis: {
                            categories: compCategories,
                            labels: {
                                rotate: -45,
                                rotateAlways: false,
                                hideOverlappingLabels: true,
                                maxHeight: 38,
                                style: {
                                    fontSize: '8.5px',
                                    fontFamily: 'Inter, sans-serif'
                                },
                                formatter: function(val) {
                                    if (!val) return '';
                                    if (typeof val === 'string' && val.length === 10 && val.charAt(4) === '-') {
                                        return val.substring(8, 10) + '/' + val.substring(5, 7) + '/' + val
                                            .substring(2, 4);
                                    }
                                    return val;
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: val => Math.round(val)
                            }
                        },
                        grid: {
                            borderColor: isThemeLight() ? '#e2e8f0' : '#1e293b',
                            padding: {
                                top: 5,
                                bottom: 0,
                                left: 6,
                                right: 6
                            }
                        },
                        tooltip: {
                            theme: chartTheme.mode
                        }
                    };

                    if (charts.comp) charts.comp.destroy();
                    charts.comp = new ApexCharts($('#chartCompressor')[0], compOptions);
                    charts.comp.render();
                }

                // 2. AHU CHART (Ampere 1-4 & Suhu Temp Out)
                // 2. AHU CHART RENDERER
                function renderAhuChart(ahuData) {
                    if (!ahuData) return;
                    const chartTheme = getChartTheme();
                    const isHourly = ($('#filterMode').val() === 'today' || $('#filterMode').val() === 'harian');
                    $('#ahuPeriodBadge').text(isHourly ? 'AHU 1 - 4 (Hourly)' : 'AHU 1 - 4 (Daily)');

                    let ahuCategories = [];
                    let ahuSeries = [];

                    if (isHourly) {
                        ahuCategories = ahuData.hourly.map(h => h.jam);
                        ahuSeries = [{
                                name: 'Ampere 1',
                                type: 'column',
                                data: ahuData.hourly.map(h => h.ampere_1)
                            },
                            {
                                name: 'Ampere 2',
                                type: 'column',
                                data: ahuData.hourly.map(h => h.ampere_2)
                            },
                            {
                                name: 'Ampere 3',
                                type: 'column',
                                data: ahuData.hourly.map(h => h.ampere_3)
                            },
                            {
                                name: 'Ampere 4',
                                type: 'column',
                                data: ahuData.hourly.map(h => h.ampere_4)
                            },
                            {
                                name: 'Suhu Temp Out (°C)',
                                type: 'line',
                                data: ahuData.hourly.map(h => h.temp_out)
                            }
                        ];
                    } else {
                        ahuCategories = ahuData.daily_trends.map(t => t.tanggal);
                        ahuSeries = [{
                                name: 'Ampere 1 Avg',
                                type: 'column',
                                data: ahuData.daily_trends.map(t => t.ampere_1)
                            },
                            {
                                name: 'Ampere 2 Avg',
                                type: 'column',
                                data: ahuData.daily_trends.map(t => t.ampere_2)
                            },
                            {
                                name: 'Ampere 3 Avg',
                                type: 'column',
                                data: ahuData.daily_trends.map(t => t.ampere_3)
                            },
                            {
                                name: 'Ampere 4 Avg',
                                type: 'column',
                                data: ahuData.daily_trends.map(t => t.ampere_4)
                            },
                            {
                                name: 'Suhu Temp Out Avg (°C)',
                                type: 'line',
                                data: ahuData.daily_trends.map(t => t.temp_out_avg)
                            }
                        ];
                    }

                    const ahuOptions = {
                        series: ahuSeries,
                        chart: {
                            height: 190,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: chartTheme,
                        colors: ['#0284c7', '#06b6d4', '#3b82f6', '#8b5cf6', '#ef4444'],
                        stroke: {
                            width: [0, 0, 0, 0, 3],
                            curve: 'smooth'
                        },
                        legend: {
                            show: false
                        },
                        xaxis: {
                            categories: ahuCategories,
                            labels: {
                                rotate: -45,
                                rotateAlways: false,
                                hideOverlappingLabels: true,
                                maxHeight: 38,
                                style: {
                                    fontSize: '8.5px',
                                    fontFamily: 'Inter, sans-serif'
                                },
                                formatter: function(val) {
                                    if (!val) return '';
                                    if (typeof val === 'string' && val.length === 10 && val.charAt(4) === '-') {
                                        return val.substring(8, 10) + '/' + val.substring(5, 7) + '/' + val
                                            .substring(2, 4);
                                    }
                                    return val;
                                }
                            }
                        },
                        yaxis: [{
                                title: {
                                    text: 'Ampere (A)'
                                },
                                labels: {
                                    formatter: val => Math.round(val)
                                }
                            },
                            {
                                opposite: true,
                                title: {
                                    text: 'Temp Out (°C)'
                                },
                                labels: {
                                    formatter: val => val ? val.toFixed(1) : 0
                                }
                            }
                        ],
                        grid: {
                            borderColor: isThemeLight() ? '#e2e8f0' : '#1e293b',
                            padding: {
                                top: 5,
                                bottom: 0,
                                left: 6,
                                right: 6
                            }
                        },
                        tooltip: {
                            theme: chartTheme.mode,
                            shared: true,
                            intersect: false,
                            fixed: {
                                enabled: false
                            }
                        }
                    };

                    if (charts.ahu) charts.ahu.destroy();
                    charts.ahu = new ApexCharts($('#chartAhu')[0], ahuOptions);
                    charts.ahu.render();
                }

                // 3. MDP CHARTS RENDERER (Electrical & Thermal)
                function renderMdpCharts(mdpData, selectedDate) {
                    if (!mdpData) return;
                    const chartTheme = getChartTheme();

                    // 1st MDP Chart: Electrical
                    let mdpCategories = [];
                    let mdpSeries = [];

                    if (mdpData.daily_trends && mdpData.daily_trends.length > 0) {
                        mdpCategories = mdpData.daily_trends.map(t => t.tanggal);
                        mdpSeries = [{
                                name: 'Daya Total (kW)',
                                type: 'area',
                                data: mdpData.daily_trends.map(t => t.daya_total_avg)
                            },
                            {
                                name: 'Arus Rata-rata (A)',
                                type: 'line',
                                data: mdpData.daily_trends.map(t => t.arus_avg)
                            },
                            {
                                name: 'Tegangan (V)',
                                type: 'line',
                                data: mdpData.daily_trends.map(t => t.tegangan_avg)
                            }
                        ];
                    } else {
                        mdpCategories = [selectedDate || 'Today'];
                        mdpSeries = [{
                                name: 'Daya Total (kW)',
                                type: 'area',
                                data: [mdpData.summary.daya_total]
                            },
                            {
                                name: 'Arus Rata-rata (A)',
                                type: 'line',
                                data: [mdpData.summary.arus_rata_rata]
                            },
                            {
                                name: 'Tegangan (V)',
                                type: 'line',
                                data: [mdpData.summary.tegangan_rata_rata]
                            }
                        ];
                    }

                    const mdpOptions = {
                        series: mdpSeries,
                        chart: {
                            height: 190,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: chartTheme,
                        colors: ['#f59e0b', '#06b6d4', '#3b82f6'],
                        stroke: {
                            width: [2, 2.5, 2.5],
                            curve: 'smooth'
                        },
                        markers: {
                            size: 3.5,
                            hover: {
                                size: 5.5
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -6,
                            background: {
                                enabled: false
                            },
                            style: {
                                fontSize: '8px',
                                fontFamily: 'JetBrains Mono, monospace',
                                fontWeight: '600',
                                colors: isThemeLight() ? ['#d97706', '#0891b2', '#2563eb'] : ['#fbbf24', '#22d3ee',
                                    '#60a5fa'
                                ]
                            },
                            formatter: function(val, opt) {
                                if (val === null || val === undefined || isNaN(val) || val === 0) return '';
                                return Number.isInteger(val) ? val : Number(val).toFixed(1);
                            }
                        },
                        fill: {
                            type: ['gradient', 'solid', 'solid'],
                            gradient: {
                                opacityFrom: 0.35,
                                opacityTo: 0.05
                            }
                        },
                        legend: {
                            show: false
                        },
                        xaxis: {
                            categories: mdpCategories,
                            labels: {
                                rotate: -45,
                                rotateAlways: false,
                                hideOverlappingLabels: true,
                                maxHeight: 38,
                                style: {
                                    fontSize: '8.5px',
                                    fontFamily: 'Inter, sans-serif'
                                },
                                formatter: function(val) {
                                    if (!val) return '';
                                    if (typeof val === 'string' && val.length === 10 && val.charAt(4) === '-') {
                                        return val.substring(8, 10) + '/' + val.substring(5, 7) + '/' + val
                                            .substring(2, 4);
                                    }
                                    return val;
                                }
                            }
                        },
                        grid: {
                            borderColor: isThemeLight() ? '#e2e8f0' : '#1e293b',
                            padding: {
                                top: 8,
                                bottom: 0,
                                left: 6,
                                right: 6
                            }
                        },
                        tooltip: {
                            theme: chartTheme.mode
                        }
                    };

                    if (charts.mdp) charts.mdp.destroy();
                    charts.mdp = new ApexCharts($('#chartMdp')[0], mdpOptions);
                    charts.mdp.render();

                    // 2nd MDP Chart: Thermal
                    let mdpThermalSeries = [];
                    if (mdpData.daily_trends && mdpData.daily_trends.length > 0) {
                        mdpThermalSeries = [{
                                name: 'Temp Trafo (°C)',
                                type: 'area',
                                data: mdpData.daily_trends.map(t => t.temp_trafo_avg)
                            },
                            {
                                name: 'Ruang Trafo (°C)',
                                type: 'line',
                                data: mdpData.daily_trends.map(t => t.suhu_trafo_avg)
                            },
                            {
                                name: 'Ruang Genset (°C)',
                                type: 'line',
                                data: mdpData.daily_trends.map(t => t.suhu_genset_avg)
                            }
                        ];
                    } else {
                        mdpThermalSeries = [{
                                name: 'Temp Trafo (°C)',
                                type: 'area',
                                data: [mdpData.summary.temperatur_transformator]
                            },
                            {
                                name: 'Ruang Trafo (°C)',
                                type: 'line',
                                data: [mdpData.summary.suhu_ruang_trafo]
                            },
                            {
                                name: 'Ruang Genset (°C)',
                                type: 'line',
                                data: [mdpData.summary.suhu_ruang_genset]
                            }
                        ];
                    }

                    const mdpThermalOptions = {
                        series: mdpThermalSeries,
                        chart: {
                            height: 190,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: chartTheme,
                        colors: ['#ef4444', '#f59e0b', '#ec4899'],
                        stroke: {
                            width: [2, 2.5, 2.5],
                            curve: 'smooth'
                        },
                        markers: {
                            size: 3.5,
                            hover: {
                                size: 5.5
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -6,
                            background: {
                                enabled: false
                            },
                            style: {
                                fontSize: '8px',
                                fontFamily: 'JetBrains Mono, monospace',
                                fontWeight: '600',
                                colors: isThemeLight() ? ['#dc2626', '#d97706', '#db2777'] : ['#f87171', '#fbbf24',
                                    '#f472b6'
                                ]
                            },
                            formatter: function(val, opt) {
                                if (val === null || val === undefined || isNaN(val) || val === 0) return '';
                                return Number(val).toFixed(1) + '°';
                            }
                        },
                        fill: {
                            type: ['gradient', 'solid', 'solid'],
                            gradient: {
                                opacityFrom: 0.35,
                                opacityTo: 0.05
                            }
                        },
                        legend: {
                            show: false
                        },
                        xaxis: {
                            categories: mdpCategories,
                            labels: {
                                rotate: -45,
                                rotateAlways: false,
                                hideOverlappingLabels: true,
                                maxHeight: 38,
                                style: {
                                    fontSize: '8.5px',
                                    fontFamily: 'Inter, sans-serif'
                                },
                                formatter: function(val) {
                                    if (!val) return '';
                                    if (typeof val === 'string' && val.length === 10 && val.charAt(4) === '-') {
                                        return val.substring(8, 10) + '/' + val.substring(5, 7) + '/' + val
                                            .substring(2, 4);
                                    }
                                    return val;
                                }
                            }
                        },
                        grid: {
                            borderColor: isThemeLight() ? '#e2e8f0' : '#1e293b',
                            padding: {
                                top: 8,
                                bottom: 0,
                                left: 6,
                                right: 6
                            }
                        },
                        tooltip: {
                            theme: chartTheme.mode
                        }
                    };

                    if (charts.mdpThermal) charts.mdpThermal.destroy();
                    charts.mdpThermal = new ApexCharts($('#chartMdpThermal')[0], mdpThermalOptions);
                    charts.mdpThermal.render();
                }

                // 4. COOLING TOWER CHART RENDERER
                function renderCtChart(ctData) {
                    if (!ctData) return;
                    const chartTheme = getChartTheme();
                    const isHourly = ($('#filterMode').val() === 'today' || $('#filterMode').val() === 'harian');
                    $('#ctPeriodBadge').text(isHourly ? 'Tiap Jam & Avg' : 'Daily Trends');

                    let ctCategories = [];
                    let ctSeries = [];

                    if (isHourly) {
                        ctCategories = ctData.hourly.map(h => h.jam);
                        ctSeries = [{
                                name: 'Temp Out (°C)',
                                type: 'area',
                                data: ctData.hourly.map(h => h.temp_out)
                            },
                            {
                                name: 'Pressure Out (Bar)',
                                type: 'line',
                                data: ctData.hourly.map(h => h.pressure_out)
                            }
                        ];
                    } else {
                        ctCategories = ctData.daily_trends.map(t => t.tanggal);
                        ctSeries = [{
                                name: 'Temp Out Avg (°C)',
                                type: 'area',
                                data: ctData.daily_trends.map(t => t.temp_out_avg)
                            },
                            {
                                name: 'Pressure Out Avg (Bar)',
                                type: 'line',
                                data: ctData.daily_trends.map(t => t.pressure_out_avg)
                            }
                        ];
                    }

                    const ctOptions = {
                        series: ctSeries,
                        chart: {
                            height: 190,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: chartTheme,
                        colors: ['#06b6d4', '#10b981'],
                        stroke: {
                            width: [2, 3],
                            curve: 'smooth'
                        },
                        markers: {
                            size: 3.5,
                            hover: {
                                size: 5.5
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -6,
                            background: {
                                enabled: false
                            },
                            style: {
                                fontSize: '8px',
                                fontFamily: 'JetBrains Mono, monospace',
                                fontWeight: '600',
                                colors: isThemeLight() ? ['#0891b2', '#059669'] : ['#22d3ee', '#34d399']
                            },
                            formatter: function(val, opt) {
                                if (val === null || val === undefined || isNaN(val) || val === 0) return '';
                                if (opt && opt.seriesIndex === 0) {
                                    return Number(val).toFixed(1) + '°';
                                }
                                return Number(val).toFixed(1);
                            }
                        },
                        fill: {
                            type: ['gradient', 'solid'],
                            gradient: {
                                opacityFrom: 0.35,
                                opacityTo: 0.05
                            }
                        },
                        legend: {
                            show: false
                        },
                        xaxis: {
                            categories: ctCategories,
                            labels: {
                                rotate: -45,
                                rotateAlways: false,
                                hideOverlappingLabels: true,
                                maxHeight: 38,
                                style: {
                                    fontSize: '8.5px',
                                    fontFamily: 'Inter, sans-serif'
                                },
                                formatter: function(val) {
                                    if (!val) return '';
                                    if (typeof val === 'string' && val.length === 10 && val.charAt(4) === '-') {
                                        return val.substring(8, 10) + '/' + val.substring(5, 7) + '/' + val
                                            .substring(2, 4);
                                    }
                                    return val;
                                }
                            }
                        },
                        yaxis: [{
                                title: {
                                    text: 'Temp Out (°C)'
                                },
                                labels: {
                                    formatter: val => val ? val.toFixed(1) : 0
                                }
                            },
                            {
                                opposite: true,
                                title: {
                                    text: 'Pressure Out (Bar)'
                                },
                                labels: {
                                    formatter: val => val ? val.toFixed(2) : 0
                                }
                            }
                        ],
                        grid: {
                            borderColor: isThemeLight() ? '#e2e8f0' : '#1e293b',
                            padding: {
                                top: 8,
                                bottom: 0,
                                left: 6,
                                right: 6
                            }
                        },
                        tooltip: {
                            theme: chartTheme.mode
                        }
                    };

                    if (charts.ct) charts.ct.destroy();
                    charts.ct = new ApexCharts($('#chartCoolingTower')[0], ctOptions);
                    charts.ct.render();
                }

                // 5. GENSET ANNUAL TIMELINE CHART RENDERER
                function renderGensetChart(gensetData) {
                    if (!gensetData) return;
                    const chartTheme = getChartTheme();
                    const gensetRuns = gensetData.runs || [];
                    const gensetCategories = gensetRuns.map(r => r.tanggal);
                    const gensetOptions = {
                        series: [{
                                name: 'Engine Speed (RPM)',
                                data: gensetRuns.map(r => r.engine_speed)
                            },
                            {
                                name: 'Engine Temp (°C)',
                                data: gensetRuns.map(r => r.engine_temperature)
                            },
                            {
                                name: 'Oil Press (x100)',
                                data: gensetRuns.map(r => Math.round(r.engine_oil_pressure * 100))
                            }
                        ],
                        chart: {
                            type: 'line',
                            height: 190,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: chartTheme,
                        colors: ['#06b6d4', '#f59e0b', '#ec4899'],
                        stroke: {
                            curve: 'smooth',
                            width: 2.5
                        },
                        markers: {
                            size: 3.5,
                            hover: {
                                size: 5.5
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -6,
                            background: {
                                enabled: false
                            },
                            style: {
                                fontSize: '8px',
                                fontFamily: 'JetBrains Mono, monospace',
                                fontWeight: '600',
                                colors: isThemeLight() ? ['#0891b2', '#d97706', '#db2777'] : ['#22d3ee', '#fbbf24',
                                    '#f472b6'
                                ]
                            },
                            formatter: function(val, opt) {
                                if (val === null || val === undefined || isNaN(val) || val === 0) return '';
                                if (opt && opt.seriesIndex === 1) {
                                    return Number(val).toFixed(0) + '°';
                                }
                                if (opt && opt.seriesIndex === 2) {
                                    return (val / 100).toFixed(1);
                                }
                                return Number(val).toFixed(0);
                            }
                        },
                        legend: {
                            show: false
                        },
                        xaxis: {
                            categories: gensetCategories,
                            labels: {
                                rotate: -45,
                                rotateAlways: false,
                                hideOverlappingLabels: true,
                                maxHeight: 38,
                                style: {
                                    fontSize: '8.5px',
                                    fontFamily: 'Inter, sans-serif'
                                },
                                formatter: function(val) {
                                    if (!val) return '';
                                    if (typeof val === 'string' && val.length === 10 && val.charAt(4) === '-') {
                                        return val.substring(8, 10) + '/' + val.substring(5, 7) + '/' + val
                                            .substring(2, 4);
                                    }
                                    return val;
                                }
                            }
                        },
                        grid: {
                            borderColor: isThemeLight() ? '#e2e8f0' : '#1e293b',
                            padding: {
                                top: 8,
                                bottom: 0,
                                left: 6,
                                right: 6
                            }
                        },
                        tooltip: {
                            theme: chartTheme.mode
                        }
                    };

                    if (charts.genset) charts.genset.destroy();
                    charts.genset = new ApexCharts($('#chartGenset')[0], gensetOptions);
                    charts.genset.render();
                }

                // 6. WATER QUALITY CHARTS RENDERER (5 SEPARATE SYSTEMS)
                function renderWaterCharts(waterData, selectedDate) {
                    if (!waterData) return;
                    const chartTheme = getChartTheme();
                    const ws = waterData.water_systems;
                    const hasWaterTrends = (waterData.daily_trends && waterData.daily_trends.length > 0);
                    const waterCategories = hasWaterTrends ?
                        waterData.daily_trends.map(t => t.tanggal) : [selectedDate || 'Today'];

                    function renderWaterSystemChart(chartElemId, chartKey, phData, tdsData, hardnessData, phColor) {
                        const series = [{
                                name: 'pH',
                                type: 'line',
                                data: phData
                            },
                            {
                                name: 'TDS (ppm)',
                                type: 'area',
                                data: tdsData
                            },
                            {
                                name: 'Hardness (ppm)',
                                type: 'line',
                                data: hardnessData
                            }
                        ];

                        const options = {
                            series: series,
                            chart: {
                                height: 190,
                                toolbar: {
                                    show: false
                                },
                                background: 'transparent'
                            },
                            theme: chartTheme,
                            colors: [phColor, '#38bdf8', '#fb923c'],
                            stroke: {
                                width: [2.5, 1.8, 2],
                                curve: 'smooth'
                            },
                            fill: {
                                type: ['solid', 'gradient', 'solid'],
                                gradient: {
                                    opacityFrom: 0.25,
                                    opacityTo: 0.03
                                }
                            },
                            markers: {
                                size: 3.5,
                                hover: {
                                    size: 5.5
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                offsetY: -6,
                                background: {
                                    enabled: false
                                },
                                style: {
                                    fontSize: '8px',
                                    fontFamily: 'JetBrains Mono, monospace',
                                    fontWeight: '600',
                                    colors: isThemeLight() ? [phColor, '#0284c7', '#d97706'] : [phColor, '#38bdf8',
                                        '#fbbf24'
                                    ]
                                },
                                formatter: function(val, opt) {
                                    if (val === null || val === undefined || isNaN(val) || val === 0) return '';
                                    if (opt && opt.seriesIndex === 0) {
                                        return Number(val).toFixed(1);
                                    }
                                    return Math.round(val);
                                }
                            },
                            yaxis: [{
                                    seriesName: 'pH',
                                    title: {
                                        text: 'pH',
                                        style: {
                                            fontSize: '8px',
                                            color: phColor
                                        }
                                    },
                                    min: 0,
                                    max: 14,
                                    tickAmount: 7,
                                    labels: {
                                        style: {
                                            fontSize: '8px'
                                        },
                                        formatter: val => Math.round(val)
                                    }
                                },
                                {
                                    seriesName: 'TDS (ppm)',
                                    opposite: true,
                                    title: {
                                        text: 'ppm',
                                        style: {
                                            fontSize: '8px',
                                            color: '#38bdf8'
                                        }
                                    },
                                    labels: {
                                        style: {
                                            fontSize: '8px'
                                        },
                                        formatter: val => Math.round(val)
                                    }
                                },
                                {
                                    seriesName: 'TDS (ppm)',
                                    opposite: true,
                                    show: false
                                }
                            ],
                            xaxis: {
                                categories: waterCategories,
                                labels: {
                                    rotate: -45,
                                    rotateAlways: false,
                                    hideOverlappingLabels: true,
                                    maxHeight: 38,
                                    style: {
                                        fontSize: '8.5px',
                                        fontFamily: 'Inter, sans-serif'
                                    },
                                    formatter: function(val) {
                                        if (!val) return '';
                                        if (typeof val === 'string' && val.length === 10 && val.charAt(4) ===
                                            '-') {
                                            return val.substring(8, 10) + '/' + val.substring(5, 7) + '/' + val
                                                .substring(2, 4);
                                        }
                                        return val;
                                    }
                                }
                            },
                            grid: {
                                borderColor: isThemeLight() ? '#e2e8f0' : '#1e293b',
                                padding: {
                                    top: 8,
                                    bottom: 0,
                                    left: 6,
                                    right: 6
                                }
                            },
                            legend: {
                                show: false
                            },
                            tooltip: {
                                theme: chartTheme.mode,
                                y: {
                                    formatter: function(val, opt) {
                                        if (val === null || val === undefined || isNaN(val)) return '-';
                                        if (opt && opt.seriesIndex === 0) {
                                            return Number(val).toFixed(2);
                                        }
                                        return Number(val).toLocaleString() + ' ppm';
                                    }
                                }
                            }
                        };

                        if (charts[chartKey]) charts[chartKey].destroy();
                        charts[chartKey] = new ApexCharts($(chartElemId)[0], options);
                        charts[chartKey].render();
                    }

                    // 1. Cooling Tower Water
                    const ctPh = hasWaterTrends ? waterData.daily_trends.map(t => t.ct_ph) : [ws.cooling_tower.ph];
                    const ctTds = hasWaterTrends ? waterData.daily_trends.map(t => t.ct_tds) : [ws.cooling_tower.tds];
                    const ctHardness = hasWaterTrends ? waterData.daily_trends.map(t => t.ct_hardness) : [ws
                        .cooling_tower.hardness
                    ];
                    renderWaterSystemChart('#chartWaterCt', 'waterCt', ctPh, ctTds, ctHardness, '#06b6d4');

                    // 2. Reverse Osmosis Water
                    const roPh = hasWaterTrends ? waterData.daily_trends.map(t => t.ro_ph) : [ws.reverse_osmosis.ph];
                    const roTds = hasWaterTrends ? waterData.daily_trends.map(t => t.ro_tds) : [ws.reverse_osmosis.tds];
                    const roHardness = hasWaterTrends ? waterData.daily_trends.map(t => t.ro_hardness) : [ws
                        .reverse_osmosis.hardness
                    ];
                    renderWaterSystemChart('#chartWaterRo', 'waterRo', roPh, roTds, roHardness, '#3b82f6');

                    // 3. Water Softener Water
                    const wsPh = hasWaterTrends ? waterData.daily_trends.map(t => t.ws_ph) : [ws.water_softener.ph];
                    const wsTds = hasWaterTrends ? waterData.daily_trends.map(t => t.ws_tds) : [ws.water_softener.tds];
                    const wsHardness = hasWaterTrends ? waterData.daily_trends.map(t => t.ws_hardness) : [ws
                        .water_softener.hardness
                    ];
                    renderWaterSystemChart('#chartWaterWs', 'waterWs', wsPh, wsTds, wsHardness, '#10b981');

                    // 4. Boiler Water
                    const boilerPh = hasWaterTrends ? waterData.daily_trends.map(t => t.boiler_ph) : [ws.boiler.ph];
                    const boilerTds = hasWaterTrends ? waterData.daily_trends.map(t => t.boiler_tds) : [ws.boiler.tds];
                    const boilerHardness = hasWaterTrends ? waterData.daily_trends.map(t => t.boiler_hardness) : [ws
                        .boiler.hardness
                    ];
                    renderWaterSystemChart('#chartWaterBoiler', 'waterBoiler', boilerPh, boilerTds, boilerHardness,
                        '#f59e0b');

                    // 5. Fresh Water
                    const fwPh = hasWaterTrends ? waterData.daily_trends.map(t => t.fw_ph) : [ws.fresh_water.ph];
                    const fwTds = hasWaterTrends ? waterData.daily_trends.map(t => t.fw_tds) : [ws.fresh_water.tds];
                    const fwHardness = hasWaterTrends ? waterData.daily_trends.map(t => t.fw_hardness) : [ws.fresh_water
                        .hardness
                    ];
                    renderWaterSystemChart('#chartWaterFw', 'waterFw', fwPh, fwTds, fwHardness, '#a855f7');
                }

                // Initial trigger
                loadDashboardData();
            });
        </script>
    </body>

</html>
