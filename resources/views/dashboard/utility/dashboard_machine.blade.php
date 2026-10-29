<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Engineering Utility | PT BAS</title>
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

            /* Hide ApexCharts Legend globally on this dashboard */
            .apexcharts-legend {
                display: none !important;
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

            body.wco-light-mode .wco-card-title {
                color: #0f172a !important;
            }

            body.wco-light-mode .kpi-top-val:not([class*="text-"]):not([style*="color"]) {
                color: #0f172a !important;
            }

            body.wco-light-mode .stat-tile-val:not([class*="text-"]):not([class*="val-"]):not([style*="color"]) {
                color: #0f172a !important;
            }

            /* Dedicated metric classes matching chart series */
            .val-ahu-1 {
                color: #0284c7 !important;
            }

            .val-ahu-2 {
                color: #06b6d4 !important;
            }

            .val-ahu-3 {
                color: #3b82f6 !important;
            }

            .val-ahu-4 {
                color: #8b5cf6 !important;
            }

            .val-ahu-suhu {
                color: #ef4444 !important;
            }

            .val-comp-ir55 {
                color: #3b82f6 !important;
            }

            /* Light mode high-contrast adaptive colors for metrics */
            body.wco-light-mode .val-ahu-1 {
                color: #0369a1 !important;
            }

            body.wco-light-mode .val-ahu-2 {
                color: #0891b2 !important;
            }

            body.wco-light-mode .val-ahu-3 {
                color: #2563eb !important;
            }

            body.wco-light-mode .val-ahu-4 {
                color: #7c3aed !important;
            }

            body.wco-light-mode .val-ahu-suhu {
                color: #dc2626 !important;
            }

            body.wco-light-mode .val-comp-ir55 {
                color: #2563eb !important;
            }

            body.wco-light-mode .text-cyan {
                color: #0891b2 !important;
            }

            body.wco-light-mode .text-warning {
                color: #d97706 !important;
            }

            body.wco-light-mode .text-info {
                color: #0284c7 !important;
            }

            body.wco-light-mode .text-success {
                color: #059669 !important;
            }

            body.wco-light-mode .text-danger {
                color: #dc2626 !important;
            }

            body.wco-light-mode .text-pink {
                color: #db2777 !important;
            }

            body.wco-light-mode .text-purple {
                color: #7c3aed !important;
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

            .text-pink {
                color: #ec4899 !important;
            }

            .text-purple {
                color: #a855f7 !important;
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
                                <h4 class="mb-0 fw-bold" style="letter-spacing: 0.8px; font-size: 15px;">ENGINEERING
                                    UTILITY
                                </h4>
                                <div
                                    style="font-size: 9px; color: var(--wco-cyan); font-weight: 700; letter-spacing: 0.5px;">
                                    OPERASIONAL ENGINEERING UTILITY
                                </div>
                            </div>
                        </div>

                        <!-- Center: Title -->
                        <div class="col-xl-4 col-lg-4 col-md-12 text-center d-none d-lg-block">
                            <div class="wco-header-title">ENGINEERING UTILITY DASHBOARD</div>
                            <div class="wco-header-sub"> COMPRESSOR • AHU • MDP • GENSET • COOLING TOWER • AIR</div>
                            <div class="wco-header-motto">"Reliability & Efficiency Of Engineering Utility Operation"
                            </div>
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
                                    <div class="text-muted" style="font-size: 8px;">TOTAL RUNNING HOUR</div>
                                </div>
                            </div>
                            <div class="kpi-top-val" id="kpiCompVal">0 <span
                                    style="font-size: 11px; font-weight: normal; color: #94a3b8;">h</span></div>
                            <div class="kpi-top-sub" id="kpiCompSub"
                                style="font-size: 8px; gap: 3px; flex-wrap: wrap;">
                                <i class="mdi mdi-check-circle-outline"></i> RH1: <span id="kpiCompRh1">-</span> |
                                RH2: <span id="kpiCompGa57">-</span> | RH3: <span id="kpiCompIr55">-</span> | RH4:
                                <span id="kpiCompGa55">-</span>
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

                    <!-- Card 3: Power House (formerly MDP Power) -->
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="kpi-top-card">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon"
                                    style="background: rgba(245, 158, 11, 0.15); color: var(--wco-amber);">
                                    <i class="mdi mdi-home-lightning-bolt"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">POWER HOUSE</div>
                                    <div class="text-muted" style="font-size: 8px;">PLN • PLTS • INVOICE</div>
                                </div>
                            </div>

                            <!-- 3 Metrics: PLN | PLTS | Invoice -->
                            <div class="row g-0 text-center my-auto py-1 align-items-center"
                                style="background: rgba(255, 255, 255, 0.02); border-radius: 6px; border: 1px solid rgba(255, 255, 255, 0.05);">
                                <!-- PLN -->
                                <div class="col-4 border-end border-secondary border-opacity-25 px-1">
                                    <div class="text-muted"
                                        style="font-size: 7.5px; font-weight: 700; text-transform: uppercase;">PLN
                                    </div>
                                    <div class="text-warning fw-bold text-truncate" id="kpiPlnVal"
                                        style="font-family: 'JetBrains Mono', monospace; font-size: 11px; line-height: 1.2;"
                                        title="E-Del Terbaru - Awal">0</div>
                                    <div class="text-muted" style="font-size: 7px;">kWh</div>
                                </div>
                                <!-- PLTS -->
                                <div class="col-4 border-end border-secondary border-opacity-25 px-1">
                                    <div class="text-muted"
                                        style="font-size: 7.5px; font-weight: 700; text-transform: uppercase;">PLTS
                                    </div>
                                    <div class="text-success fw-bold text-truncate" id="kpiPltsVal"
                                        style="font-family: 'JetBrains Mono', monospace; font-size: 11px; line-height: 1.2;"
                                        title="iSolarCloud API (Dummy)">0</div>
                                    <div class="text-muted" style="font-size: 7px;">kWh</div>
                                </div>
                                <!-- Invoice -->
                                <div class="col-4 px-1">
                                    <div class="text-muted"
                                        style="font-size: 7.5px; font-weight: 700; text-transform: uppercase;">INVOICE
                                    </div>
                                    <div class="text-cyan fw-bold text-truncate" id="kpiInvoiceVal"
                                        style="font-family: 'JetBrains Mono', monospace; font-size: 10px; line-height: 1.2;"
                                        title="Estimasi Biaya">Rp 0</div>
                                    <div class="text-muted text-truncate" id="kpiInvoiceSub" style="font-size: 7px;"
                                        title="Tarif Listrik">kWh/Rp</div>
                                </div>
                            </div>

                            <div class="kpi-top-sub d-flex justify-content-between align-items-center mt-1"
                                id="kpiPowerHouseSub" style="font-size: 7.5px;">
                                <span class="text-truncate"><i class="mdi mdi-flash text-warning"></i> Tot: <strong
                                        id="kpiTotalEnergy">0</strong> kWh</span>
                                <span class="text-muted text-truncate" id="kpiEdelMeta" title="E-Del Akhir - Awal">Δ
                                    E-Del</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Total Cost Utility (formerly Cooling Tower) -->
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="kpi-top-card">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon"
                                    style="background: rgba(16, 185, 129, 0.15); color: var(--wco-green);">
                                    <i class="mdi mdi-cash-multiple"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">TOTAL COST UTILITY</div>
                                    <div class="text-muted" style="font-size: 8px;">COST SMKP & UTILITY</div>
                                </div>
                            </div>
                            <div class="kpi-top-val text-success" id="kpiTotalCostUtilityVal">Rp 0 Jt</div>
                            <div class="kpi-top-sub d-flex justify-content-between align-items-center" id="kpiTotalCostUtilitySub"
                                style="font-size: 7.5px;">
                                <span class="text-truncate"><i class="mdi mdi-calendar-text text-success"></i> <span
                                        id="kpiCostSmkpYearMeta">Tahun -</span></span>
                                <span class="text-muted text-truncate" id="kpiAvgCostUtilityMeta">Avg: 0 Jt/bln</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 5: Genset Warming Up / RH Genset -->
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="kpi-top-card">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon"
                                    style="background: rgba(236, 72, 153, 0.15); color: var(--wco-pink);">
                                    <i class="mdi mdi-generator-mobile"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">RH GENSET</div>
                                    <div class="text-muted" style="font-size: 8px;">RUNNING HOUR TAHUNAN</div>
                                </div>
                            </div>
                            <div class="kpi-top-val" id="kpiGensetVal">0 <span
                                    style="font-size: 11px; font-weight: normal; color: #94a3b8;">h</span></div>
                            <div class="kpi-top-sub" id="kpiGensetSub"
                                style="font-size: 8px; gap: 3px; flex-wrap: wrap;">
                                <i class="mdi mdi-clock-outline"></i> Awal: <span id="kpiGensetRhStart">-</span> |
                                Akhir: <span id="kpiGensetRhLatest">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 6: Raw Water Usage vs Quota -->
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="kpi-top-card">
                            <div class="kpi-top-header">
                                <div class="kpi-top-icon"
                                    style="background: rgba(6, 182, 212, 0.15); color: var(--wco-cyan);">
                                    <i class="mdi mdi-water-pump"></i>
                                </div>
                                <div>
                                    <div class="kpi-top-label">AIR MENTAH</div>
                                    <div class="text-muted" style="font-size: 8px;">TOTAL / QUOTA RAW WATER</div>
                                </div>
                            </div>
                            <div class="kpi-top-val text-cyan" id="kpiWaterVal">0 / 0 <span
                                    style="font-size: 10px; font-weight: normal; color: #94a3b8;">m³</span></div>
                            <div class="kpi-top-sub" id="kpiWaterSub">
                                <i class="mdi mdi-water-check"></i> Aktual: <span id="kpiRawAktual">0</span> | Quota:
                                <span id="kpiRawQuota">0</span> m³
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
                                    <!-- RH 1 (rh1) - #f59e0b -->
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="text-center">
                                                <span class="stat-tile-label text-warning"
                                                    style="font-size: 8px;">AQ55 (rh1)</span>
                                            </div>
                                            <div class="stat-tile-val mt-1 text-center text-warning" id="valCompRh1"
                                                style="font-size: 13px;">0.00</div>
                                            <div class="text-center text-muted" id="compMetaRh1"
                                                style="font-size: 7.5px; line-height: 1.1;">Tgl Terbaru</div>
                                        </div>
                                    </div>
                                    <!-- GA57 (rh2) - #06b6d4 -->
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="text-center">
                                                <span class="stat-tile-label text-cyan" style="font-size: 8px;">GA37
                                                    (rh2)</span>
                                            </div>
                                            <div class="stat-tile-val mt-1 text-center text-cyan" id="valCompGa57"
                                                style="font-size: 13px;">0.00</div>
                                            <div class="text-center text-muted" id="compMetaGa57"
                                                style="font-size: 7.5px; line-height: 1.1;">Tgl Terbaru</div>
                                        </div>
                                    </div>
                                    <!-- IR55 (rh3) - #3b82f6 (Dark) / #2563eb (Light) -->
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="text-center">
                                                <span class="stat-tile-label val-comp-ir55"
                                                    style="font-size: 8px;">IR55
                                                    (rh3)</span>
                                            </div>
                                            <div class="stat-tile-val mt-1 text-center val-comp-ir55" id="valCompIr55"
                                                style="font-size: 13px;">0.00</div>
                                            <div class="text-center text-muted" id="compMetaIr55"
                                                style="font-size: 7.5px; line-height: 1.1;">Tgl Terbaru</div>
                                        </div>
                                    </div>
                                    <!-- GA55 (rh4) - #10b981 -->
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="text-center">
                                                <span class="stat-tile-label text-success"
                                                    style="font-size: 8px;">GA55 (rh4)</span>
                                            </div>
                                            <div class="stat-tile-val mt-1 text-center text-success" id="valCompGa55"
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
                                <!-- Top: Ampere AHU 1 - 4 & Suhu combined in 1 row (matching chart colors) -->
                                <div class="row g-1 mb-2 align-items-stretch">
                                    <div class="col-8">
                                        <div class="row g-1">
                                            <div class="col-3">
                                                <div class="stat-tile text-center py-1 px-0">
                                                    <div class="stat-tile-label val-ahu-1"
                                                        style="font-size: 7.5px; font-weight: 600;">AVG. AHU 1
                                                    </div>
                                                    <div class="stat-tile-val mt-1 val-ahu-1" id="valAhuAmp1"
                                                        style="font-size: 11px;">0</div>
                                                    <div class="stat-tile-meta val-ahu-1" style="font-size: 7px;">Amp
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="stat-tile text-center py-1 px-0">
                                                    <div class="stat-tile-label val-ahu-2"
                                                        style="font-size: 7.5px; font-weight: 600;">AVG. AHU 2
                                                    </div>
                                                    <div class="stat-tile-val mt-1 val-ahu-2" id="valAhuAmp2"
                                                        style="font-size: 11px;">0</div>
                                                    <div class="stat-tile-meta val-ahu-2" style="font-size: 7px;">Amp
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="stat-tile text-center py-1 px-0">
                                                    <div class="stat-tile-label val-ahu-3"
                                                        style="font-size: 7.5px; font-weight: 600;">AVG. AHU 3
                                                    </div>
                                                    <div class="stat-tile-val mt-1 val-ahu-3" id="valAhuAmp3"
                                                        style="font-size: 11px;">0</div>
                                                    <div class="stat-tile-meta val-ahu-3" style="font-size: 7px;">Amp
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="stat-tile text-center py-1 px-0">
                                                    <div class="stat-tile-label val-ahu-4"
                                                        style="font-size: 7.5px; font-weight: 600;">AVG. AHU 4
                                                    </div>
                                                    <div class="stat-tile-val mt-1 val-ahu-4" id="valAhuAmp4"
                                                        style="font-size: 11px;">0</div>
                                                    <div class="stat-tile-meta val-ahu-4" style="font-size: 7px;">Amp
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div
                                            class="stat-tile text-center py-1 px-1 h-100 d-flex flex-column justify-content-center">
                                            <div class="stat-tile-label val-ahu-suhu"
                                                style="font-size: 7.5px; font-weight: 600;">AVG. SUHU OUT</div>
                                            <div class="stat-tile-val mt-1 val-ahu-suhu" id="valAhuTempOut"
                                                style="font-size: 11px;">0.0 °C</div>
                                            <div class="stat-tile-meta val-ahu-suhu" style="font-size: 7px;"
                                                id="ahuTempOutMeta">
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
                                                <span>Avg Total: <strong id="avgCtTemp">0</strong>°C</span>
                                                <span id="rangeCtTemp" class="text-muted">Tgl Terbaru</span>
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
                                                <span>Avg Total: <strong id="avgCtPress">0</strong> Bar</span>
                                                <span id="rangeCtPress" class="text-muted">Tgl Terbaru</span>
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
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="stat-tile-label text-warning" style="font-size: 7.5px;">AVG.
                                                ARUS</div>
                                            <div class="stat-tile-val mt-1 text-warning" id="valMdpArus"
                                                style="font-size: 11px;">
                                                0.0 A</div>
                                            <div class="stat-tile-meta" id="valMdpArusPhases"
                                                style="font-size: 6.5px;">I1: - | I2: -</div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="stat-tile-label text-info" style="font-size: 7.5px;">AVG.
                                                TEGANGAN
                                            </div>
                                            <div class="stat-tile-val mt-1 text-info" id="valMdpTegangan"
                                                style="font-size: 11px;">0.0 V</div>
                                            <div class="stat-tile-meta" id="valMdpVoltPhases"
                                                style="font-size: 6.5px;">V1: - | V2: -</div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="stat-tile-label text-success" style="font-size: 7.5px;">AVG.
                                                DAYA</div>
                                            <div class="stat-tile-val mt-1 text-success" id="valMdpDaya"
                                                style="font-size: 11px;">
                                                0.0 kW</div>
                                            <div class="stat-tile-meta" id="valMdpDayaPhases"
                                                style="font-size: 6.5px;">P1: - | P2: -</div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1">
                                            <div class="stat-tile-label text-cyan" id="labelMdpCosPhi"
                                                style="font-size: 7.5px;">AVG.
                                                COS φ</div>
                                            <div class="stat-tile-val mt-1 text-cyan" id="valMdpCosPhi"
                                                style="font-size: 11px;">
                                                0.00</div>
                                            <div class="stat-tile-meta text-muted" id="valMdpCosPhiMeta"
                                                style="font-size: 6.5px;">STD: 0.85 - 1.00</div>
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
                                            <div class="stat-tile-val mt-1 text-danger" id="valMdpTempTrafo"
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
                                            <div class="stat-tile-val mt-1 text-warning" id="valMdpSuhuTrafo"
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
                                            <div class="stat-tile-val mt-1 text-pink" id="valMdpSuhuGenset"
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

                    <!-- 7. PEMAKAIAN AIR MENTAH / RAW WATER (col-xl-6) -->
                    <div class="col-xl-4 col-lg-6 col-12">
                        <div class="wco-card h-100">
                            <div class="wco-card-header">
                                <div class="wco-card-title">
                                    <i class="mdi mdi-water-pump text-cyan"></i>
                                    <span>AIR MENTAH (AKTUAL VS QUOTA)</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-cyan"
                                    id="airRawPeriodBadge">Sumur 1,2,4,5 & PDAM</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <!-- 4 Stat Tiles -->
                                <div class="row g-1 mb-2">
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1 text-center">
                                            <span class="stat-tile-label text-cyan" style="font-size: 8px;">AKTUAL
                                                RAW</span>
                                            <div class="stat-tile-val mt-1 text-cyan" id="valAirRawTotal"
                                                style="font-size: 12px;">0 m³</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Total
                                                Aktual</div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1 text-center">
                                            <span class="stat-tile-label text-muted" style="font-size: 8px;">TARGET
                                                QUOTA</span>
                                            <div class="stat-tile-val mt-1 text-secondary" id="valAirRawQuota"
                                                style="font-size: 12px;">0 m³</div>
                                            <div class="stat-tile-meta text-muted" id="valAirRawDaysMeta"
                                                style="font-size: 7px;">1 Hari</div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1 text-center">
                                            <span class="stat-tile-label text-info"
                                                style="font-size: 8px;">VARIANSI</span>
                                            <div class="stat-tile-val mt-1" id="valAirRawVariance"
                                                style="font-size: 12px;">0 m³</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Aktual -
                                                Quota</div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1 text-center">
                                            <span class="stat-tile-label text-warning" style="font-size: 8px;">%
                                                PENCAPAIAN</span>
                                            <div class="stat-tile-val mt-1" id="valAirRawPercent"
                                                style="font-size: 12px;">0%</div>
                                            <div class="stat-tile-meta" id="valAirRawStatusMeta"
                                                style="font-size: 7px;">Status: Normal</div>
                                        </div>
                                    </div>
                                </div>
                                <div id="chartAirRaw" style="min-height: 200px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 8. PEMAKAIAN AIR PROSES & SUPPORT (col-xl-6) -->
                    <div class="col-xl-4 col-lg-6 col-12">
                        <div class="wco-card h-100">
                            <div class="wco-card-header">
                                <div class="wco-card-title">
                                    <i class="mdi mdi-water text-primary"></i>
                                    <span>AIR PROSES & SUPPORT</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-primary"
                                    id="airProsesPeriodBadge">Pemakaian Area</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <!-- 4 Stat Tiles -->
                                <div class="row g-1 mb-2">
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1 text-center">
                                            <span class="stat-tile-label text-primary" style="font-size: 8px;">TOTAL
                                                PEMAKAIAN</span>
                                            <div class="stat-tile-val mt-1 text-primary" id="valAirProsesTotal"
                                                style="font-size: 12px;">0 m³</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Semua Area
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile py-1 px-1 text-center">
                                            <span class="stat-tile-label text-info" style="font-size: 8px;">AREA
                                                TERBESAR</span>
                                            <div class="stat-tile-val mt-1 text-info text-truncate"
                                                id="valAirProsesTopArea" style="font-size: 11px;" title="-">-
                                            </div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Top
                                                Konsumsi</div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="stat-tile py-1 px-1 text-center">
                                            <span class="stat-tile-label text-warning" style="font-size: 8px;">TOP
                                                KONSUMSI</span>
                                            <div class="stat-tile-val mt-1 text-warning" id="valAirProsesTopVal"
                                                style="font-size: 12px;">0 m³</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Volume
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="stat-tile py-1 px-1 text-center">
                                            <span class="stat-tile-label text-success" style="font-size: 8px;">TITIK
                                                AREA</span>
                                            <div class="stat-tile-val mt-1 text-success" id="valAirProsesCount"
                                                style="font-size: 12px;">0</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Titik Aktif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="chartAirProses" style="min-height: 200px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 9. KUALITAS AIR COOLING TOWER (col-xl-4) -->
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
                                            <span class="stat-tile-label text-cyan" style="font-size: 8px;"> AVG.
                                                pH</span>
                                            <div class="stat-tile-val mt-1 text-cyan" style="font-size: 12px;"
                                                id="wCtPh">
                                                0.0</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: 7.5 -
                                                8.5</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-info" style="font-size: 8px;">AVG.
                                                TDS</span>
                                            <div class="stat-tile-val mt-1 text-info" style="font-size: 11px;"
                                                id="wCtTds">0 ppm</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;">Std: ≤ 2500
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-warning" style="font-size: 8px;">AVG.
                                                HARDNESS</span>
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
                                    <i class="mdi mdi-water-plus text-cyan"></i>
                                    <span>KUALITAS AIR REVERSE OSMOSIS</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-cyan">RO System</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <div class="row g-1 mb-2">
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-cyan" style="font-size: 8px;">AVG.
                                                pH</span>
                                            <div class="stat-tile-val mt-1 text-cyan" style="font-size: 12px;"
                                                id="wRoPh">
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
                                            <div class="stat-tile-val mt-1 text-success" style="font-size: 12px;"
                                                id="wWsPh">
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
                    <div class="col-xl-4 col-lg-6 col-12">
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
                                            <div class="stat-tile-val mt-1 text-warning" style="font-size: 12px;"
                                                id="wBoilerPh">
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
                                    <i class="mdi mdi-water text-cyan"></i>
                                    <span>KUALITAS AIR FRESH WATER</span>
                                </div>
                                <span class="badge bg-dark border border-secondary text-cyan">Fresh Water</span>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <div class="row g-1 mb-2">
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-cyan" style="font-size: 8px;">AVG.
                                                pH</span>
                                            <div class="stat-tile-val mt-1 text-cyan" style="font-size: 12px;"
                                                id="wFwPh">
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

                    <!-- 12. COST SMKP & UTILITY (col-xl-4) -->
                    <div class="col-xl-6 col-lg-6 col-12">
                        <div class="wco-card h-100">
                            <div class="wco-card-header">
                                <div class="wco-card-title">
                                    <i class="mdi mdi-cash-multiple text-warning"></i>
                                    <span>COST SMKP & UTILITY</span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <select id="costSmkpYearSelect" class="form-select form-select-sm filter-ctrl"
                                        style="width: 75px; padding: 1px 6px; font-size: 10px;"
                                        title="Filter Tahun Cost SMKP">
                                        <option value="2026" selected>2026</option>
                                    </select>
                                    <span class="badge bg-dark border border-secondary text-warning"
                                        id="costSmkpBadge">1 Tahun</span>
                                </div>
                            </div>
                            <div class="wco-card-body p-2 d-flex flex-column">
                                <!-- 3 Stat Highlight Tiles -->
                                <div class="row g-1 mb-2">
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-warning" style="font-size: 8px;">AVG. BB
                                                / FG</span>
                                            <div class="stat-tile-val mt-1 text-warning" style="font-size: 12px;"
                                                id="valCostSmkpBb">0.00</div>
                                            <div class="stat-tile-meta" style="font-size: 7px; color: #dc2626;">
                                                Target: 66
                                                Kg/Ton
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-cyan" style="font-size: 8px;">AVG.
                                                LISTRIK/KM</span>
                                            <div class="stat-tile-val mt-1 text-cyan" style="font-size: 12px;"
                                                id="valCostSmkpListrik">0.00</div>
                                            <div class="stat-tile-meta" style="font-size: 7px; color: #2563eb;">
                                                Target: 75
                                                kWh/Ton
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-tile text-center py-1 px-1">
                                            <span class="stat-tile-label text-success" style="font-size: 8px;">TOTAL
                                                COST</span>
                                            <div class="stat-tile-val mt-1 text-success" style="font-size: 11px;"
                                                id="valCostSmkpCost">Rp 0 Jt</div>
                                            <div class="stat-tile-meta text-muted" style="font-size: 7px;"
                                                id="metaCostSmkpMonth">Per Bulan</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ApexChart Combined 1-Year Trend (BB/FG, Listrik BAS/KM, Cost Utility) -->
                                <div id="chartCostSmkp" style="min-height: 190px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- JS Dependencies -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <!-- PPTX Presentation Dependencies -->
        <script src="{{ asset('material/assets/libs/pptxgenjs/jszip.min.js') }}"></script>
        <script src="{{ asset('material/assets/libs/pptxgenjs/pptxgen.bundle.js') }}"></script>
        <script>
            if (typeof PptxGenJS === 'undefined') {
                document.write('<script src="https://cdn.jsdelivr.net/npm/pptxgenjs@3.12.0/dist/pptxgen.bundle.js"><\/script>');
            }
        </script>
        <script src="{{ asset('material/assets/js/pptx-common-theme.js') }}"></script>
        <script src="{{ asset('material/assets/js/dashboard-utility-pptx.js') }}"></script>

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
                    loadDashboardData(); // Redraw charts with theme color adjustments
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
                    const curDateVal = $('#selectedDate').val() || $('#customStartDate').val();
                    if (curDateVal) {
                        const yr = new Date(curDateVal).getFullYear();
                        if (yr && $('#costSmkpYearSelect').val() != yr) {
                            if ($(`#costSmkpYearSelect option[value="${yr}"]`).length > 0) {
                                $('#costSmkpYearSelect').val(yr);
                            }
                        }
                    }
                    loadDashboardData();
                });
                $('#gensetYearSelect').on('change', function() {
                    loadGensetSection();
                });
                $('#costSmkpYearSelect').on('change', function() {
                    loadCostSmkpSection();
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
                    waterFw: null,
                    airProses: null,
                    airRaw: null,
                    costSmkp: null
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
                    const costYear = $('#costSmkpYearSelect').val();

                    let params = {
                        mode: mode,
                        tanggal: tanggal,
                        genset_year: gensetYear,
                        cost_year: costYear
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
                    const totalDelta = (comp.total_rh_delta !== undefined) ? comp.total_rh_delta : 0;
                    $('#kpiCompVal').html(
                        `${Number(totalDelta).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 1 })} <span style="font-size: 11px; font-weight: normal; color: #94a3b8;">h</span>`
                    );
                    $('#kpiCompRh1').text(comp.rh1_delta !== undefined ?
                        `${Number(comp.rh1_delta).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 1 })}h` :
                        '0h');
                    $('#kpiCompGa57').text(comp.ga57_delta !== undefined ?
                        `${Number(comp.ga57_delta).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 1 })}h` :
                        '0h');
                    $('#kpiCompIr55').text(comp.ir55_delta !== undefined ?
                        `${Number(comp.ir55_delta).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 1 })}h` :
                        '0h');
                    $('#kpiCompGa55').text(comp.ga55_delta !== undefined ?
                        `${Number(comp.ga55_delta).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 1 })}h` :
                        '0h');

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
                    // Top KPI Card: Power House (PLN | PLTS | Invoice)
                    if (mdp.power_house) {
                        const ph = mdp.power_house;
                        const plnVal = Number(ph.pln_kwh || 0);
                        const pltsVal = Number(ph.plts_kwh || 0);
                        const totalEnergy = Number(ph.total_energy_kwh || (plnVal + pltsVal));
                        const invVal = Number(ph.invoice_total || 0);

                        // 1. PLN (E-Del Terbaru - Awal)
                        $('#kpiPlnVal').text(plnVal.toLocaleString('id-ID', {
                            maximumFractionDigits: 1
                        }));
                        $('#kpiPlnVal').attr('title',
                            `PLN E-Del: Akhir (${Number(ph.edel_latest || 0).toLocaleString('id-ID')}) - Awal (${Number(ph.edel_start || 0).toLocaleString('id-ID')}) kWh`
                        );

                        // 2. PLTS (iSolarCloud Dummy API)
                        $('#kpiPltsVal').text(pltsVal.toLocaleString('id-ID', {
                            maximumFractionDigits: 1
                        }));
                        $('#kpiPltsVal').attr('title',
                            `PLTS Produksi: ${pltsVal.toLocaleString('id-ID', { maximumFractionDigits: 1 })} kWh (${ph.plts_source || 'iSolarCloud API'})`
                        );

                        // 3. Invoice (kWh / Rp)
                        let invFormatted = 'Rp 0';
                        if (invVal >= 1000000000) {
                            invFormatted = 'Rp ' + (invVal / 1000000000).toLocaleString('id-ID', {
                                minimumFractionDigits: 1,
                                maximumFractionDigits: 2
                            }) + ' M';
                        } else if (invVal >= 1000000) {
                            invFormatted = 'Rp ' + (invVal / 1000000).toLocaleString('id-ID', {
                                minimumFractionDigits: 1,
                                maximumFractionDigits: 2
                            }) + ' Jt';
                        } else if (invVal > 0) {
                            invFormatted = 'Rp ' + (invVal / 1000).toLocaleString('id-ID', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 1
                            }) + ' Rb';
                        } else {
                            invFormatted = 'Rp 0';
                        }
                        const tarifFormatted = Number(ph.tarif_per_kwh || 1303).toLocaleString('id-ID');
                        $('#kpiInvoiceVal').text(invFormatted);
                        $('#kpiInvoiceVal').attr('title',
                            `Estimasi Biaya: Rp ${invVal.toLocaleString('id-ID')} (@Rp ${tarifFormatted} / kWh)`);
                        $('#kpiInvoiceSub').text(`@${tarifFormatted}/kWh`);

                        // Subtitle Meta
                        $('#kpiTotalEnergy').text(totalEnergy.toLocaleString('id-ID', {
                            maximumFractionDigits: 1
                        }));
                        const edelDiffText =
                            `Δ E-Del: ${(plnVal > 0 ? '+' : '') + plnVal.toLocaleString('id-ID', { maximumFractionDigits: 0 })}`;
                        $('#kpiEdelMeta').text(edelDiffText).attr('title',
                            `E-Del Akhir (${ph.edel_latest_date || ''}): ${Number(ph.edel_latest || 0).toLocaleString('id-ID')} vs Awal (${ph.edel_start_date || ''}): ${Number(ph.edel_start || 0).toLocaleString('id-ID')}`
                        );
                    }

                    $('#valMdpArus').text(`${mdp.arus_rata_rata} A`);
                    $('#valMdpTegangan').text(`${mdp.tegangan_rata_rata} V`);
                    $('#valMdpDaya').text(`${mdp.daya_total} kW`);
                    $('#valMdpTempTrafo').text(`${mdp.temperatur_transformator} °C`);
                    $('#valMdpSuhuTrafo').text(`${mdp.suhu_ruang_trafo} °C`);
                    $('#valMdpSuhuGenset').text(`${mdp.suhu_ruang_genset} °C`);

                    if (mdp.has_cos_phi_data) {
                        const cosVal = Number(mdp.cos_phi);
                        $('#valMdpCosPhi').text(cosVal.toFixed(2));
                        if (cosVal < 0.85 || cosVal > 1.00) {
                            $('#valMdpCosPhi').removeClass('text-cyan text-success').addClass('text-danger');
                            $('#labelMdpCosPhi').removeClass('text-cyan text-success').addClass('text-danger');
                            $('#valMdpCosPhiMeta').html(
                                '<span class="text-danger fw-bold"><i class="mdi mdi-alert-circle"></i> Di Luar STD</span>'
                            );
                        } else {
                            $('#valMdpCosPhi').removeClass('text-danger text-success').addClass('text-cyan');
                            $('#labelMdpCosPhi').removeClass('text-danger text-success').addClass('text-cyan');
                            $('#valMdpCosPhiMeta').html(
                                '<span class="text-success"><i class="mdi mdi-check-circle"></i> Normal (0.85-1)</span>'
                            );
                        }
                    } else {
                        $('#valMdpCosPhi').removeClass('text-danger text-success').addClass('text-cyan').text('0.00');
                        $('#labelMdpCosPhi').removeClass('text-danger text-success').addClass('text-cyan');
                        $('#valMdpCosPhiMeta').html('<span class="text-muted">STD: 0.85 - 1.00</span>');
                    }

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
                    // Card metric tiles: tampilkan AVG data tanggal terbaru (sama seperti Compressor)
                    const tempLatestVal = (ct.temp_out_latest_avg !== undefined) ? ct.temp_out_latest_avg : ct
                        .temp_out_avg;
                    const pressLatestVal = (ct.pressure_out_latest_avg !== undefined) ? ct.pressure_out_latest_avg : ct
                        .pressure_out_avg;

                    $('#valCtTempOut').text(
                        `${Number(tempLatestVal).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 })} °C`
                    );
                    $('#valCtPressOut').text(
                        `${Number(pressLatestVal).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 })} Bar`
                    );

                    const dateMeta = ct.latest_date_formatted ? `Tgl: ${ct.latest_date_formatted}` : (ct.latest_date ?
                        `Tgl: ${ct.latest_date}` : 'Tgl Terbaru');

                    $('#avgCtTemp').text(ct.temp_out_avg);
                    $('#rangeCtTemp').text(dateMeta);

                    $('#avgCtPress').text(ct.pressure_out_avg);
                    $('#rangeCtPress').text(dateMeta);
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

                    const rhDelta = (genset.running_hour_delta !== undefined) ? genset.running_hour_delta : 0;
                    const rhStart = (genset.running_hour_start !== undefined) ? genset.running_hour_start : 0;
                    const rhLatest = (genset.running_hour_latest !== undefined) ? genset.running_hour_latest : 0;

                    const rhDeltaFormatted = Number(rhDelta).toLocaleString('id-ID', {
                        minimumFractionDigits: (rhDelta % 1 === 0 ? 0 : 1),
                        maximumFractionDigits: 2
                    });

                    $('#kpiGensetVal').html(
                        `${rhDeltaFormatted} <span style="font-size: 11px; font-weight: normal; color: #94a3b8;">h</span>`
                    );
                    $('#kpiGensetRhStart').text(`${rhStart}h`);
                    $('#kpiGensetRhLatest').text(`${rhLatest}h`);
                    if ($('#kpiGensetRuns').length) $('#kpiGensetRuns').text(genset.total_runs);
                    if ($('#kpiGensetOil').length) $('#kpiGensetOil').text(genset.avg_oil_pressure);
                    $('#kpiGensetSub').attr('title',
                        `RH Awal: ${rhStart}h (${genset.rh_start_date || '-'}) | RH Akhir: ${rhLatest}h (${genset.rh_latest_date || '-'}) | Runs: ${genset.total_runs}`
                    );

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
                }

                async function loadWaterSection() {
                    const data = await fetchMachineSection('water');
                    if (!data || !data.analisis_utility) return;
                    updateWaterUI(data.analisis_utility.water_systems);
                    renderWaterCharts(data.analisis_utility, data.selected_date);
                }

                // 7. WATER USAGE UI & LOADER (PROSES & AIR MENTAH)
                function updateWaterUsageUI(wu) {
                    if (!wu) return;
                    const raw = wu.raw || {};
                    const proses = wu.proses || {};

                    // Top KPI Card Air Mentah (Card 6)
                    const totalAktual = Number(raw.total_aktual || 0);
                    const totalQuota = Number(raw.total_quota || 0);
                    const aktualFormatted = totalAktual.toLocaleString('id-ID', {
                        maximumFractionDigits: 1
                    });
                    const quotaFormatted = totalQuota.toLocaleString('id-ID', {
                        maximumFractionDigits: 0
                    });

                    $('#kpiWaterVal').html(
                        `${aktualFormatted} / ${quotaFormatted} <span style="font-size: 10px; font-weight: normal; color: #94a3b8;">m³</span>`
                    );
                    $('#kpiRawAktual').text(aktualFormatted);
                    $('#kpiRawQuota').text(quotaFormatted);

                    if (raw.status === 'exceeded') {
                        $('#kpiWaterVal').removeClass('text-cyan text-info').addClass('text-danger');
                        $('#kpiWaterSub').html(
                            `<i class="mdi mdi-alert-circle text-danger"></i> <span class="text-danger fw-bold">Over Quota: +${Number(raw.total_variance).toLocaleString('id-ID')} m³</span> (${raw.total_percentage}%)`
                        );
                    } else {
                        $('#kpiWaterVal').removeClass('text-danger').addClass('text-cyan');
                        $('#kpiWaterSub').html(
                            `<i class="mdi mdi-water-check text-cyan"></i> Quota: ${quotaFormatted} m³ | Pemakaian: ${raw.total_percentage}%`
                        );
                    }
                    $('#kpiWaterSub').attr('title',
                        `Aktual: ${aktualFormatted} m³ | Quota: ${quotaFormatted} m³ (${raw.days_count || 1} hari)`);

                    // Process & Support Card
                    $('#valAirProsesTotal').text(`${Number(proses.total_pemakaian || 0).toLocaleString('id-ID')} m³`);
                    $('#valAirProsesTopArea').text(proses.top_area || '-').attr('title', proses.top_area || '-');
                    $('#valAirProsesTopVal').text(`${Number(proses.top_pemakaian || 0).toLocaleString('id-ID')} m³`);
                    $('#valAirProsesCount').text(proses.count || 0);

                    // Raw Water Card
                    $('#valAirRawTotal').text(`${aktualFormatted} m³`);
                    $('#valAirRawQuota').text(`${quotaFormatted} m³`);
                    $('#valAirRawDaysMeta').text(
                        `${raw.days_count || 1} Hari (${(totalQuota / (raw.days_count || 1)).toFixed(0)} m³/hari)`);

                    const varFormatted = (raw.total_variance > 0 ? '+' : '') + Number(raw.total_variance || 0)
                        .toLocaleString('id-ID', {
                            maximumFractionDigits: 1
                        }) + ' m³';
                    $('#valAirRawVariance').text(varFormatted);
                    if (raw.total_variance > 0) {
                        $('#valAirRawVariance').removeClass('text-info text-success').addClass('text-danger');
                    } else {
                        $('#valAirRawVariance').removeClass('text-danger').addClass('text-info');
                    }

                    $('#valAirRawPercent').text(`${raw.total_percentage || 0}%`);
                    if (raw.status === 'exceeded') {
                        $('#valAirRawPercent').removeClass('text-warning text-cyan text-success').addClass(
                            'text-danger');
                        $('#valAirRawStatusMeta').html(
                            '<span class="text-danger fw-bold"><i class="mdi mdi-alert"></i> Over Quota</span>');
                    } else {
                        $('#valAirRawPercent').removeClass('text-danger').addClass('text-cyan');
                        $('#valAirRawStatusMeta').html(
                            '<span class="text-success"><i class="mdi mdi-check-circle"></i> Normal (≤ Quota)</span>'
                        );
                    }
                }

                async function loadWaterUsageSection() {
                    const data = await fetchMachineSection('water_usage');
                    if (!data || !data.water_usage) return;
                    updateWaterUsageUI(data.water_usage);
                    renderAirProsesChart(data.water_usage.proses);
                    renderAirRawChart(data.water_usage.raw);
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
                            loadWaterUsageSection(),
                            loadWaterSection(),
                            loadCostSmkpSection()
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
                                name: 'AQ55 (rh1)',
                                data: compData.hourly.map(h => h.rh1)
                            },
                            {
                                name: 'GA37 (rh2)',
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
                                name: 'AQ55 Avg (rh1)',
                                data: compData.daily_trends.map(t => t.rh1_avg)
                            },
                            {
                                name: 'GA37 Avg (rh2)',
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
                        colors: isThemeLight() ? ['#d97706', '#0891b2', '#2563eb', '#059669'] : ['#f59e0b',
                            '#06b6d4', '#3b82f6', '#10b981'
                        ],
                        stroke: {
                            curve: 'smooth',
                            width: 2.5
                        },
                        markers: {
                            size: 4,
                            hover: {
                                size: 6
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
                                name: 'Suhu (°C)',
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
                                name: 'Suhu (°C)',
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
                        colors: isThemeLight() ? ['#0369a1', '#0891b2', '#2563eb', '#7c3aed', '#dc2626'] : [
                            '#0284c7', '#06b6d4', '#3b82f6', '#8b5cf6', '#ef4444'
                        ],
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
                                    text: 'Suhu (°C)'
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
                        colors: isThemeLight() ? ['#059669', '#d97706', '#0891b2'] : ['#10b981', '#f59e0b',
                            '#06b6d4'
                        ],
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
                                colors: isThemeLight() ? ['#059669', '#d97706', '#0891b2'] : ['#34d399', '#fbbf24',
                                    '#22d3ee'
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
                        colors: isThemeLight() ? ['#dc2626', '#d97706', '#db2777'] : ['#ef4444', '#f59e0b',
                            '#ec4899'
                        ],
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
                        colors: isThemeLight() ? ['#0891b2', '#059669'] : ['#06b6d4', '#10b981'],
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
                        colors: ['#06b6d4', '#f59e0b', '#10b981'],
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
                                colors: isThemeLight() ? ['#0891b2', '#d97706', '#059669'] : ['#22d3ee', '#fbbf24',
                                    '#34d399'
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
                            show: true,
                            position: 'top',
                            horizontalAlign: 'right',
                            fontSize: '9px',
                            fontFamily: 'Inter, sans-serif',
                            labels: {
                                colors: isThemeLight() ? '#475569' : '#94a3b8'
                            },
                            markers: {
                                radius: 3,
                                width: 8,
                                height: 8
                            }
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

                    function renderWaterSystemChart(chartElemId, chartKey, phData, tdsData, hardnessData, phColor,
                        tdsColor = '#38bdf8', hardnessColor = '#f59e0b', phColorLight = null) {
                        const lightPh = phColorLight || phColor;
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

                        const finalPh = isThemeLight() ? lightPh : phColor;
                        const finalTds = isThemeLight() ? '#0284c7' : tdsColor;
                        const finalHardness = isThemeLight() ? (hardnessColor === '#ef4444' ? '#dc2626' : '#d97706') :
                            hardnessColor;

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
                            colors: [finalPh, finalTds, finalHardness],
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
                                    colors: isThemeLight() ? [
                                        lightPh,
                                        '#0284c7',
                                        hardnessColor === '#ef4444' ? '#dc2626' : '#d97706'
                                    ] : [
                                        phColor,
                                        tdsColor,
                                        hardnessColor === '#ef4444' ? '#f87171' : '#fbbf24'
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
                                            color: tdsColor
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

                    // 1. Cooling Tower Water (pH Cyan, TDS Info Blue, Hardness Warning Amber)
                    const ctPh = hasWaterTrends ? waterData.daily_trends.map(t => t.ct_ph) : [ws.cooling_tower.ph];
                    const ctTds = hasWaterTrends ? waterData.daily_trends.map(t => t.ct_tds) : [ws.cooling_tower.tds];
                    const ctHardness = hasWaterTrends ? waterData.daily_trends.map(t => t.ct_hardness) : [ws
                        .cooling_tower.hardness
                    ];
                    renderWaterSystemChart('#chartWaterCt', 'waterCt', ctPh, ctTds, ctHardness, '#06b6d4', '#38bdf8',
                        '#f59e0b', '#0891b2');

                    // 2. Reverse Osmosis Water (pH Cyan, TDS Info Blue, Hardness Warning Amber)
                    const roPh = hasWaterTrends ? waterData.daily_trends.map(t => t.ro_ph) : [ws.reverse_osmosis.ph];
                    const roTds = hasWaterTrends ? waterData.daily_trends.map(t => t.ro_tds) : [ws.reverse_osmosis.tds];
                    const roHardness = hasWaterTrends ? waterData.daily_trends.map(t => t.ro_hardness) : [ws
                        .reverse_osmosis.hardness
                    ];
                    renderWaterSystemChart('#chartWaterRo', 'waterRo', roPh, roTds, roHardness, '#06b6d4', '#38bdf8',
                        '#f59e0b', '#0891b2');

                    // 3. Water Softener Water (pH Success Green, TDS Info Blue, Hardness Warning Amber)
                    const wsPh = hasWaterTrends ? waterData.daily_trends.map(t => t.ws_ph) : [ws.water_softener.ph];
                    const wsTds = hasWaterTrends ? waterData.daily_trends.map(t => t.ws_tds) : [ws.water_softener.tds];
                    const wsHardness = hasWaterTrends ? waterData.daily_trends.map(t => t.ws_hardness) : [ws
                        .water_softener.hardness
                    ];
                    renderWaterSystemChart('#chartWaterWs', 'waterWs', wsPh, wsTds, wsHardness, '#10b981', '#38bdf8',
                        '#f59e0b', '#059669');

                    // 4. Boiler Water (pH Warning Amber, TDS Info Blue, Hardness Danger Red)
                    const boilerPh = hasWaterTrends ? waterData.daily_trends.map(t => t.boiler_ph) : [ws.boiler.ph];
                    const boilerTds = hasWaterTrends ? waterData.daily_trends.map(t => t.boiler_tds) : [ws.boiler.tds];
                    const boilerHardness = hasWaterTrends ? waterData.daily_trends.map(t => t.boiler_hardness) : [ws
                        .boiler.hardness
                    ];
                    renderWaterSystemChart('#chartWaterBoiler', 'waterBoiler', boilerPh, boilerTds, boilerHardness,
                        '#f59e0b', '#38bdf8', '#ef4444', '#d97706');

                    // 5. Fresh Water (pH Cyan, TDS Info Blue, Hardness Warning Amber)
                    const fwPh = hasWaterTrends ? waterData.daily_trends.map(t => t.fw_ph) : [ws.fresh_water.ph];
                    const fwTds = hasWaterTrends ? waterData.daily_trends.map(t => t.fw_tds) : [ws.fresh_water.tds];
                    const fwHardness = hasWaterTrends ? waterData.daily_trends.map(t => t.fw_hardness) : [ws.fresh_water
                        .hardness
                    ];
                    renderWaterSystemChart('#chartWaterFw', 'waterFw', fwPh, fwTds, fwHardness, '#06b6d4', '#38bdf8',
                        '#f59e0b', '#0891b2');
                }

                // 7. AIR PROSES & SUPPORT CHART RENDERER
                function renderAirProsesChart(prosesData) {
                    if (!prosesData) return;
                    const chartTheme = getChartTheme();
                    const items = prosesData.items || [];
                    const categories = items.length > 0 ? items.map(d => d.jenis_pemakaian) : ['Tidak Ada Data'];
                    const seriesData = items.length > 0 ? items.map(d => Number(d.total_pemakaian)) : [0];

                    const options = {
                        series: [{
                            name: 'Pemakaian (m³)',
                            data: seriesData
                        }],
                        chart: {
                            type: 'bar',
                            height: 200,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: chartTheme,
                        colors: ['#3b82f6'],
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                columnWidth: '50%',
                                dataLabels: {
                                    position: 'top'
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: val => (val > 0 ? Number(val).toLocaleString('id-ID') : '0'),
                            offsetY: -16,
                            style: {
                                fontSize: '8.5px',
                                fontFamily: 'JetBrains Mono, monospace',
                                fontWeight: 600,
                                colors: isThemeLight() ? ['#1e40af'] : ['#93c5fd']
                            }
                        },
                        xaxis: {
                            categories: categories,
                            labels: {
                                rotate: -30,
                                rotateAlways: false,
                                hideOverlappingLabels: true,
                                style: {
                                    fontSize: '8px',
                                    fontFamily: 'Inter, sans-serif'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    fontSize: '8px'
                                },
                                formatter: val => Math.round(val)
                            },
                            title: {
                                text: 'm³',
                                style: {
                                    fontSize: '8px',
                                    color: '#64748b'
                                }
                            }
                        },
                        grid: {
                            borderColor: isThemeLight() ? '#e2e8f0' : '#1e293b',
                            padding: {
                                top: 14,
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
                                formatter: val => Number(val).toLocaleString('id-ID', {
                                    maximumFractionDigits: 2
                                }) + ' m³'
                            }
                        }
                    };

                    if (charts.airProses) charts.airProses.destroy();
                    charts.airProses = new ApexCharts($('#chartAirProses')[0], options);
                    charts.airProses.render();
                }

                // 8. AIR MENTAH / RAW WATER CHART RENDERER (AKTUAL VS QUOTA)
                function renderAirRawChart(rawData) {
                    if (!rawData) return;
                    const chartTheme = getChartTheme();
                    const items = rawData.items || [];
                    const categories = items.length > 0 ? items.map(d => d.jenis_pemakaian) : ['Tidak Ada Data'];
                    const actualData = items.length > 0 ? items.map(d => Number(d.total_pemakaian)) : [0];
                    const quotaData = items.length > 0 ? items.map(d => Number(d.expected_quota)) : [0];

                    const options = {
                        series: [{
                                name: 'Aktual (m³)',
                                data: actualData
                            },
                            {
                                name: 'Target Quota (m³)',
                                data: quotaData
                            }
                        ],
                        chart: {
                            type: 'bar',
                            height: 200,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: chartTheme,
                        colors: ['#06b6d4', isThemeLight() ? '#94a3b8' : '#475569'],
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                columnWidth: '55%',
                                dataLabels: {
                                    position: 'top'
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: val => (val > 0 ? Number(val).toLocaleString('id-ID') : '0'),
                            offsetY: -16,
                            style: {
                                fontSize: '8px',
                                fontFamily: 'JetBrains Mono, monospace',
                                fontWeight: 600,
                                colors: isThemeLight() ? ['#0891b2', '#475569'] : ['#22d3ee', '#cbd5e1']
                            }
                        },
                        xaxis: {
                            categories: categories,
                            labels: {
                                style: {
                                    fontSize: '8.5px',
                                    fontFamily: 'Inter, sans-serif'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    fontSize: '8px'
                                },
                                formatter: val => Math.round(val)
                            },
                            title: {
                                text: 'm³',
                                style: {
                                    fontSize: '8px',
                                    color: '#64748b'
                                }
                            }
                        },
                        grid: {
                            borderColor: isThemeLight() ? '#e2e8f0' : '#1e293b',
                            padding: {
                                top: 14,
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
                                formatter: function(val, {
                                    seriesIndex,
                                    dataPointIndex
                                }) {
                                    const item = items[dataPointIndex];
                                    if (!item) return val + ' m³';
                                    if (seriesIndex === 0) {
                                        const statusBadge = item.status === 'exceeded' ?
                                            `<span style="color: #ef4444; font-weight: 600;">⚠️ Over +${Math.abs(item.variance).toFixed(1)} m³</span>` :
                                            `<span style="color: #10b981; font-weight: 600;">✓ Normal</span>`;
                                        return `${Number(val).toLocaleString('id-ID')} m³ (${item.percentage}% dari kuota)<br/>${statusBadge}`;
                                    } else {
                                        return `${Number(val).toLocaleString('id-ID')} m³ (Target ${item.daily_quota} m³/hari)`;
                                    }
                                }
                            }
                        }
                    };

                    if (charts.airRaw) charts.airRaw.destroy();
                    charts.airRaw = new ApexCharts($('#chartAirRaw')[0], options);
                    charts.airRaw.render();
                }

                // 9. COST SMKP & UTILITY SECTION (TREN 1 TAHUN)
                function updateCostSmkpUI(costSmkp) {
                    if (!costSmkp || !costSmkp.summary) return;
                    const s = costSmkp.summary;

                    if (costSmkp.available_years && costSmkp.available_years.length > 0) {
                        const $yearSelect = $('#costSmkpYearSelect');
                        if ($yearSelect.children().length <= 1) {
                            $yearSelect.empty();
                            $.each(costSmkp.available_years, function(_, yr) {
                                $yearSelect.append($('<option>', {
                                    value: yr,
                                    text: yr,
                                    selected: (yr == costSmkp.year)
                                }));
                            });
                        }
                    }

                    $('#costSmkpBadge').text(`Tahun ${costSmkp.year}`);
                    $('#valCostSmkpBb').text(Number(s.avg_bb_per_fg || 0).toLocaleString('id-ID', {
                        minimumFractionDigits: 1,
                        maximumFractionDigits: 2
                    }));
                    $('#valCostSmkpListrik').text(Number(s.avg_listrik_per_km || 0).toLocaleString('id-ID', {
                        minimumFractionDigits: 1,
                        maximumFractionDigits: 2
                    }));
                    $('#valCostSmkpCost').text(
                        `Rp ${Number(s.total_utility_cost_jt || 0).toLocaleString('id-ID', { maximumFractionDigits: 1 })} Jt`
                    );
                    $('#metaCostSmkpMonth').text(
                        `Avg: Rp ${Number(s.avg_utility_cost_jt || 0).toLocaleString('id-ID', { maximumFractionDigits: 1 })} Jt/bln`
                    );

                    // Top KPI Card 4: TOTAL COST UTILITY (dari Cost SMKP & Utility)
                    const totalCostJt = Number(s.total_utility_cost_jt || 0);
                    let formattedKpiCost = 'Rp 0 Jt';
                    if (totalCostJt >= 1000) {
                        formattedKpiCost = `Rp ${(totalCostJt / 1000).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 })} M`;
                    } else if (totalCostJt > 0) {
                        formattedKpiCost = `Rp ${totalCostJt.toLocaleString('id-ID', { maximumFractionDigits: 1 })} Jt`;
                    }
                    $('#kpiTotalCostUtilityVal').text(formattedKpiCost);
                    $('#kpiTotalCostUtilityVal').attr('title',
                        `Total Cost Utility: Rp ${Number(s.total_utility_cost || 0).toLocaleString('id-ID')}`
                    );
                    $('#kpiCostSmkpYearMeta').text(`Tahun ${costSmkp.year}`);
                    $('#kpiAvgCostUtilityMeta').text(
                        `Avg: ${Number(s.avg_utility_cost_jt || 0).toLocaleString('id-ID', { maximumFractionDigits: 1 })} Jt/bln`
                    );
                }

                function renderCostSmkpChart(costSmkp) {
                    if (!costSmkp || !costSmkp.months) return;
                    const chartTheme = getChartTheme();
                    const months = costSmkp.months;

                    const categories = months.map(m => m.month_name);
                    const bbData = months.map(m => m.bb_per_fg);
                    const listrikData = months.map(m => m.listrik_per_km);
                    const costData = months.map(m => m.utility_cost_jt);
                    const targetBbData = months.map(() => 66);
                    const targetListrikData = months.map(() => 75);

                    const options = {
                        series: [{
                                name: 'BB / FG',
                                type: 'column',
                                data: bbData
                            },
                            {
                                name: 'Target BB (66)',
                                type: 'line',
                                data: targetBbData
                            },
                            {
                                name: 'Listrik / KM',
                                type: 'column',
                                data: listrikData
                            },
                            {
                                name: 'Target Listrik (75)',
                                type: 'line',
                                data: targetListrikData
                            },
                            {
                                name: 'Cost Utility',
                                type: 'line',
                                data: costData
                            }
                        ],
                        chart: {
                            height: 200,
                            type: 'line',
                            stacked: false,
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        theme: chartTheme,
                        stroke: {
                            width: [0, 2, 0, 2, 3],
                            dashArray: [0, 4, 0, 4, 0],
                            curve: ['smooth', 'straight', 'smooth', 'straight', 'smooth']
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '46%',
                                borderRadius: 3
                            }
                        },
                        colors: isThemeLight() ? ['#d97706', '#dc2626', '#0891b2', '#2563eb', '#059669'] : [
                            '#f59e0b', '#dc2626', '#06b6d4', '#2563eb', '#10b981'
                        ],
                        fill: {
                            opacity: [0.85, 1, 0.85, 1, 1],
                            gradient: {
                                inverseColors: false,
                                shade: 'light',
                                type: 'vertical',
                                opacityFrom: 0.85,
                                opacityTo: 0.55,
                                stops: [0, 100]
                            }
                        },
                        labels: categories,
                        markers: {
                            size: [0, 0, 0, 0, 4],
                            strokeWidth: 2,
                            hover: {
                                size: 5
                            }
                        },
                        xaxis: {
                            categories: categories,
                            labels: {
                                style: {
                                    fontSize: '8px',
                                    fontFamily: 'Inter, sans-serif'
                                }
                            }
                        },
                        yaxis: [{
                                seriesName: 'BB / FG (Kg/Ton)',
                                title: {
                                    text: 'Ratio (Kg/Ton & kWh/Ton)',
                                    style: {
                                        fontSize: '8px',
                                        color: '#64748b'
                                    }
                                },
                                labels: {
                                    style: {
                                        fontSize: '8px'
                                    },
                                    formatter: val => (val !== null && val !== undefined) ? Math.round(val) : ''
                                }
                            },
                            {
                                seriesName: 'BB / FG (Kg/Ton)',
                                show: false
                            },
                            {
                                seriesName: 'BB / FG (Kg/Ton)',
                                show: false
                            },
                            {
                                seriesName: 'BB / FG (Kg/Ton)',
                                show: false
                            },
                            {
                                opposite: true,
                                seriesName: 'Cost Utility (Jt Rp)',
                                title: {
                                    text: 'Cost (Jt Rp)',
                                    style: {
                                        fontSize: '8px',
                                        color: '#10b981'
                                    }
                                },
                                labels: {
                                    style: {
                                        fontSize: '8px'
                                    },
                                    formatter: val => (val !== null && val !== undefined) ? Math.round(val) +
                                        ' Jt' : ''
                                }
                            }
                        ],
                        annotations: {
                            yaxis: [{
                                    y: 66,
                                    yAxisIndex: 0,
                                    borderColor: '#dc2626',
                                    strokeDashArray: 0,
                                    borderWidth: 0,
                                    label: {
                                        borderColor: '#dc2626',
                                        style: {
                                            color: '#fff',
                                            background: '#dc2626',
                                            fontSize: '7.5px',
                                            fontWeight: '600'
                                        },
                                        text: 'Target BB: 66'
                                    }
                                },
                                {
                                    y: 75,
                                    yAxisIndex: 0,
                                    borderColor: '#2563eb',
                                    strokeDashArray: 0,
                                    borderWidth: 0,
                                    label: {
                                        borderColor: '#2563eb',
                                        style: {
                                            color: '#fff',
                                            background: '#2563eb',
                                            fontSize: '7.5px',
                                            fontWeight: '600'
                                        },
                                        text: 'Target Listrik: 75'
                                    }
                                }
                            ]
                        },
                        grid: {
                            borderColor: isThemeLight() ? '#e2e8f0' : '#1e293b',
                            padding: {
                                top: 10,
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
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function(val, {
                                    seriesIndex
                                }) {
                                    if (val === null || val === undefined) return 'Tidak ada data';
                                    if (seriesIndex === 0) return Number(val).toLocaleString('id-ID', {
                                        maximumFractionDigits: 2
                                    }) + ' Kg/Ton FG';
                                    if (seriesIndex === 1) return Number(val).toLocaleString('id-ID', {
                                        maximumFractionDigits: 0
                                    }) + ' Kg/Ton (Target)';
                                    if (seriesIndex === 2) return Number(val).toLocaleString('id-ID', {
                                        maximumFractionDigits: 2
                                    }) + ' kWh/Ton Kecap Matang';
                                    if (seriesIndex === 3) return Number(val).toLocaleString('id-ID', {
                                        maximumFractionDigits: 0
                                    }) + ' kWh/Ton (Target)';
                                    return 'Rp ' + Number(val).toLocaleString('id-ID', {
                                        maximumFractionDigits: 1
                                    }) + ' Juta';
                                }
                            }
                        }
                    };

                    if (charts.costSmkp) charts.costSmkp.destroy();
                    charts.costSmkp = new ApexCharts($('#chartCostSmkp')[0], options);
                    charts.costSmkp.render();
                }

                async function loadCostSmkpSection() {
                    const data = await fetchMachineSection('cost_smkp');
                    if (!data || !data.cost_smkp) return;
                    updateCostSmkpUI(data.cost_smkp);
                    renderCostSmkpChart(data.cost_smkp);
                }

                // Initial trigger
                loadDashboardData();
            });
        </script>
    </body>

</html>
