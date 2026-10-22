<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MTC Maintenance Analytics Dashboard | PT BAS</title>
        <link rel="shortcut icon" href="{{ asset('assets/images/logo/kecap.png') }}">

        <!-- CSS Dependencies -->
        <link href="{{ asset('material/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

        <style>
            /* ──────────────────────────────────────────────────────────
           MTC MAINTENANCE DASHBOARD - COHESIVE TITANIUM PALETTE
           Minimal, Professional Color Palette:
           - Base: Slate Dark (#080c16 / #0f172a)
           - Primary Accent: Electric Sky (#38bdf8 / #0284c7)
           - Status Normal / Target Met: Emerald (#10b981)
           - Status Alert / Losses: Crimson (#f43f5e)
           - Neutrals: Slate Grayscale (#64748b / #94a3b8 / #f8fafc)
        ────────────────────────────────────────────────────────── */
            :root {
                --mtc-bg: #080c16;
                --mtc-card-bg: rgba(15, 23, 42, 0.82);
                --mtc-card-header: #0f172a;
                --mtc-border: rgba(255, 255, 255, 0.08);
                --mtc-border-hover: rgba(56, 189, 248, 0.35);
                --mtc-text-primary: #f8fafc;
                --mtc-text-secondary: #94a3b8;
                --mtc-text-muted: #64748b;

                /* Controlled Accent Colors */
                --mtc-primary: #38bdf8;
                --mtc-primary-dark: #0284c7;
                --mtc-primary-glow: rgba(56, 189, 248, 0.18);
                --mtc-success: #10b981;
                --mtc-success-glow: rgba(16, 185, 129, 0.18);
                --mtc-danger: #f43f5e;
                --mtc-danger-glow: rgba(244, 63, 94, 0.18);
            }

            html,
            body {
                margin: 0;
                padding: 0;
                width: 100%;
                min-height: 100vh;
                background-color: var(--mtc-bg) !important;
                color: var(--mtc-text-primary);
                font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                overflow-x: hidden;
            }

            /* Custom Dark Scrollbar */
            ::-webkit-scrollbar {
                width: 5px;
                height: 5px;
            }

            ::-webkit-scrollbar-track {
                background: #090e1a !important;
            }

            ::-webkit-scrollbar-thumb {
                background: #1e293b !important;
                border-radius: 4px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #38bdf8 !important;
            }

            .dashboard-wrapper {
                background: radial-gradient(circle at 10% 8%, rgba(30, 41, 59, 0.45) 0%, transparent 40%),
                    radial-gradient(circle at 90% 50%, rgba(15, 23, 42, 0.5) 0%, transparent 50%),
                    var(--mtc-bg);
                padding: 10px 14px 28px 14px;
                min-height: 100vh;
            }

            .font-mono {
                font-family: 'JetBrains Mono', monospace !important;
            }

            /* ── Header Styling ── */
            .mtc-header {
                background: linear-gradient(135deg, rgba(17, 24, 39, 0.94) 0%, rgba(13, 19, 33, 0.96) 100%);
                backdrop-filter: blur(14px);
                -webkit-backdrop-filter: blur(14px);
                border: 1px solid var(--mtc-border);
                border-radius: 12px;
                padding: 10px 16px;
                box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.08);
                margin-bottom: 12px;
                position: relative;
                overflow: hidden;
            }

            .mtc-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 2px;
                background: linear-gradient(90deg, #0284c7 0%, #38bdf8 50%, #0284c7 100%);
                opacity: 0.85;
            }

            .mtc-header-title {
                font-size: 16px;
                font-weight: 800;
                letter-spacing: 0.6px;
                color: #ffffff;
                margin-bottom: 0px;
                line-height: 1.2;
                text-transform: uppercase;
            }

            .mtc-header-sub {
                font-size: 9.5px;
                font-weight: 700;
                letter-spacing: 1px;
                color: #38bdf8;
                text-transform: uppercase;
            }

            .mtc-header-motto {
                font-size: 9px;
                font-style: italic;
                color: #94a3b8;
            }

            .badge-stream-pulse {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 3px 8px;
                border-radius: 14px;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                background: rgba(16, 185, 129, 0.12);
                color: #34d399;
                border: 1px solid rgba(16, 185, 129, 0.25);
            }

            .pulse-dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background-color: #10b981;
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
                animation: pulseRing 1.8s infinite cubic-bezier(0.66, 0, 0, 1);
            }

            @keyframes pulseRing {
                to {
                    box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
                }
            }

            /* ── Filter Segmented Switch ── */
            .filter-pill-container {
                display: inline-flex;
                background: rgba(15, 23, 42, 0.85);
                border: 1px solid var(--mtc-border);
                border-radius: 8px;
                padding: 3px;
                gap: 3px;
            }

            .filter-pill-btn {
                background: transparent;
                color: var(--mtc-text-secondary);
                border: none;
                border-radius: 6px;
                padding: 4px 11px;
                font-size: 11.5px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .filter-pill-btn:hover {
                color: #f8fafc;
                background: rgba(255, 255, 255, 0.05);
            }

            .filter-pill-btn.active {
                background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
                color: #38bdf8;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(56, 189, 248, 0.3);
            }

            .filter-ctrl {
                background: rgba(15, 23, 42, 0.85) !important;
                border: 1px solid var(--mtc-border) !important;
                color: #f8fafc !important;
                border-radius: 6px !important;
                font-size: 11px !important;
                font-weight: 600 !important;
                padding: 4px 8px !important;
                transition: all 0.2s ease;
            }

            .filter-ctrl:focus {
                border-color: #38bdf8 !important;
                box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2) !important;
                outline: none;
            }

            /* ── Unified KPI Glass Cards ── */
            .kpi-glass-card {
                background: var(--mtc-card-bg);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid var(--mtc-border);
                border-radius: 10px;
                padding: 12px 14px;
                position: relative;
                overflow: hidden;
                transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }

            .kpi-glass-card:hover {
                transform: translateY(-2px);
                border-color: rgba(56, 189, 248, 0.3);
                box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.5), 0 0 14px -2px rgba(56, 189, 248, 0.12);
            }

            .kpi-glass-card::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            }

            .kpi-icon-pill {
                width: 32px;
                height: 32px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 16px;
                background: rgba(56, 189, 248, 0.1);
                color: #38bdf8;
                border: 1px solid rgba(56, 189, 248, 0.2);
                flex-shrink: 0;
            }

            .kpi-icon-pill.alert {
                background: rgba(244, 63, 94, 0.1);
                color: #f43f5e;
                border: 1px solid rgba(244, 63, 94, 0.25);
            }

            .kpi-icon-pill.success {
                background: rgba(16, 185, 129, 0.1);
                color: #10b981;
                border: 1px solid rgba(16, 185, 129, 0.25);
            }

            .kpi-val {
                font-size: 1.6rem;
                font-weight: 800;
                line-height: 1.15;
                letter-spacing: -0.02em;
                margin: 6px 0 3px 0;
                color: #ffffff;
            }

            .kpi-val .unit {
                font-size: 0.85rem;
                font-weight: 600;
                color: var(--mtc-text-secondary);
                margin-left: 3px;
            }

            .kpi-badge {
                font-size: 10px;
                font-weight: 700;
                padding: 2px 7px;
                border-radius: 4px;
                display: inline-flex;
                align-items: center;
                gap: 3px;
            }

            .kpi-badge.optimal {
                background: rgba(16, 185, 129, 0.15);
                color: #34d399;
                border: 1px solid rgba(16, 185, 129, 0.25);
            }

            .kpi-badge.danger {
                background: rgba(244, 63, 94, 0.15);
                color: #fb7185;
                border: 1px solid rgba(244, 63, 94, 0.25);
            }

            .kpi-badge.neutral {
                background: rgba(56, 189, 248, 0.12);
                color: #38bdf8;
                border: 1px solid rgba(56, 189, 248, 0.25);
            }

            .kpi-progress-bar {
                height: 4px;
                background: rgba(255, 255, 255, 0.08);
                border-radius: 3px;
                overflow: hidden;
                margin-top: 6px;
            }

            .kpi-progress-fill {
                height: 100%;
                border-radius: 3px;
                background: #38bdf8;
                transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .kpi-progress-fill.success {
                background: #10b981;
            }

            .kpi-progress-fill.danger {
                background: #f43f5e;
            }

            /* ── Unified Chart Cards ── */
            .mtc-chart-card {
                background: var(--mtc-card-bg);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid var(--mtc-border);
                border-radius: 10px;
                padding: 12px 14px;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
                margin-bottom: 12px;
                position: relative;
                transition: border-color 0.2s ease;
            }

            .mtc-chart-card:hover {
                border-color: rgba(255, 255, 255, 0.12);
            }

            .mtc-card-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 10px;
                padding-bottom: 6px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }

            .mtc-card-title {
                font-size: 0.88rem;
                font-weight: 700;
                color: #ffffff;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 6px;
                letter-spacing: -0.01em;
            }

            .mtc-card-subtitle {
                font-size: 10px;
                color: var(--mtc-text-muted);
                margin-top: 2px;
            }

            /* ── Tables Styling ── */
            .mtc-table {
                width: 100%;
                margin-bottom: 0;
                color: #e2e8f0;
                font-size: 11.5px;
            }

            .mtc-table th {
                background: rgba(15, 23, 42, 0.85);
                color: var(--mtc-text-secondary);
                font-weight: 700;
                text-transform: uppercase;
                font-size: 10px;
                letter-spacing: 0.05em;
                padding: 7px 9px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
                white-space: nowrap;
            }

            .mtc-table td {
                padding: 8px 9px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.04);
                vertical-align: middle;
            }

            .mtc-table tbody tr:hover {
                background: rgba(255, 255, 255, 0.03);
            }

            .rank-badge {
                width: 20px;
                height: 20px;
                border-radius: 4px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 800;
                font-size: 10.5px;
                background: rgba(255, 255, 255, 0.06);
                color: #cbd5e1;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .rank-badge.top-1 {
                background: rgba(56, 189, 248, 0.18);
                color: #38bdf8;
                border: 1px solid rgba(56, 189, 248, 0.35);
            }

            .table-scrollable {
                max-height: 270px;
                overflow-y: auto;
            }

            /* ApexCharts Tooltip */
            .apexcharts-tooltip.apexcharts-theme-dark {
                background: rgba(15, 23, 42, 0.95) !important;
                backdrop-filter: blur(10px) !important;
                border: 1px solid rgba(255, 255, 255, 0.12) !important;
                box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.6) !important;
                border-radius: 6px !important;
            }

            .apexcharts-tooltip-title {
                background: rgba(30, 41, 59, 0.9) !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
                font-weight: 700 !important;
            }
        </style>
    </head>

    <body>
        <div class="dashboard-wrapper">
            <div class="container-fluid px-1">

                {{-- ═══════════════════════════════════════════════════════════════
                 1. TOP EXECUTIVE HEADER & CONTROLS
            ═══════════════════════════════════════════════════════════════ --}}
                <div class="mtc-header">
                    <div class="row align-items-center g-2">
                        <!-- Left: Brand -->
                        <div class="col-xl-3 col-lg-3 col-md-12 d-flex align-items-center gap-2">
                            <div
                                style="width: 36px; height: 36px; background: rgba(56, 189, 248, 0.12); border: 1px solid rgba(56, 189, 248, 0.35); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="mdi mdi-wrench-cog" style="font-size: 20px; color: #38bdf8;"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h4 class="mb-0 fw-bold" style="letter-spacing: 0.6px; font-size: 14px;">MAINTENANCE
                                        DASHBOARD</h4>
                                    <span class="badge-stream-pulse">
                                        <span class="pulse-dot"></span> LIVE
                                    </span>
                                </div>
                                <div style="font-size: 9px; color: #38bdf8; font-weight: 700; letter-spacing: 0.5px;">
                                    PT BANGUN ANUGERAH SEJAHTERA • ENG-MTC
                                </div>
                            </div>
                        </div>

                        <!-- Center: Title -->
                        <div class="col-xl-4 col-lg-4 col-md-12 text-center d-none d-lg-block">
                            <div class="mtc-header-title">MAINTENANCE PERFORMANCE COMMAND CENTER</div>
                            <div class="mtc-header-sub">MHE • MOTOR POMPA • SIPIL • UTILITY PLANT</div>
                            <div class="mtc-header-motto">"Reliability &amp; Maintainability (MTTR &bull; MTBF &bull;
                                Downtime Losses)"</div>
                        </div>

                        <!-- Right: Filters & Controls -->
                        <div
                            class="col-xl-5 col-lg-5 col-md-12 d-flex align-items-center justify-content-end gap-2 flex-wrap">
                            {{-- Segmented Mode Switcher --}}
                            <div class="filter-pill-container" id="filterTypeSwitch">
                                <button type="button" class="filter-pill-btn active" data-type="weekly">
                                    <i class="mdi mdi-calendar-week"></i> Weekly
                                </button>
                                <button type="button" class="filter-pill-btn" data-type="monthly">
                                    <i class="mdi mdi-calendar-month"></i> Monthly
                                </button>
                                <button type="button" class="filter-pill-btn" data-type="custom">
                                    <i class="mdi mdi-calendar-range"></i> Custom
                                </button>
                            </div>

                            {{-- Dynamic Sub-Filters --}}
                            <div id="weeklyFilterGroup" class="d-flex align-items-center gap-1">
                                <button type="button"
                                    class="btn btn-sm btn-dark border border-secondary border-opacity-25 px-1 py-1"
                                    id="btnPrevWeek" title="Minggu Sebelumnya">
                                    <i class="mdi mdi-chevron-left"></i>
                                </button>
                                <select id="selectWeek" class="filter-ctrl font-mono" style="min-width: 120px;">
                                    @for ($w = 1; $w <= 52; $w++)
                                        <option value="{{ $w }}" {{ $w == $currentWeek ? 'selected' : '' }}>
                                            Week {{ $w }} (2026)
                                        </option>
                                    @endfor
                                </select>
                                <button type="button"
                                    class="btn btn-sm btn-dark border border-secondary border-opacity-25 px-1 py-1"
                                    id="btnNextWeek" title="Minggu Berikutnya">
                                    <i class="mdi mdi-chevron-right"></i>
                                </button>
                            </div>

                            <div id="monthlyFilterGroup" class="d-flex align-items-center gap-1 d-none">
                                <select id="selectMonth" class="filter-ctrl">
                                    @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $num => $monthName)
                                        <option value="{{ $num }}"
                                            {{ $num == $currentMonth ? 'selected' : '' }}>
                                            {{ $monthName }}
                                        </option>
                                    @endforeach
                                </select>
                                <select id="selectYear" class="filter-ctrl font-mono">
                                    <option value="2026" selected>2026</option>
                                    <option value="2025">2025</option>
                                </select>
                            </div>

                            <div id="customFilterGroup" class="d-none">
                                <input type="text" id="customDateRange" class="filter-ctrl"
                                    placeholder="Pilih Rentang..." style="width: 180px;">
                            </div>

                            {{-- Action Controls --}}
                            <div class="d-flex align-items-center gap-1">
                                <button type="button"
                                    class="btn btn-sm btn-outline-info d-flex align-items-center gap-1" id="btnRefresh"
                                    title="Refresh Telemetri">
                                    <i class="mdi mdi-refresh" id="refreshIcon"></i>
                                </button>
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                    id="btnFullscreen" title="Layar Penuh (Wallboard)">
                                    <i class="mdi mdi-fullscreen" id="fsIcon"></i>
                                </button>
                                <a href="{{ url('home') }}"
                                    class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                    title="Kembali ke Portal">
                                    <i class="mdi mdi-home-outline"></i>
                                    <span class="d-none d-md-inline">Portal</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Status Strip --}}
                    <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top border-secondary border-opacity-10"
                        style="font-size: 11px;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-secondary fw-medium">Periode:</span>
                            <span
                                class="badge bg-primary bg-opacity-20 text-info border border-info border-opacity-25 font-mono px-2 py-1"
                                id="activeFilterLabel">
                                Memuat data...
                            </span>
                            <span class="text-muted">| Jam Standar: <strong class="text-light font-mono"
                                    id="operatingHoursLabel">168 Jam</strong></span>
                        </div>
                        <div class="text-muted">
                            <i class="mdi mdi-clock-outline me-1"></i> Terakhir Diperbarui: <span id="lastUpdatedTime"
                                class="text-light fw-semibold">Baru saja</span>
                        </div>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════════════════════════
                 2. KPI CARDS GRID (UNIFIED 6 METRICS)
            ═══════════════════════════════════════════════════════════════ --}}
                <div class="row g-2 mb-3">
                    {{-- Card 1: Schedule Completion --}}
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="kpi-glass-card">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="text-secondary fw-bold text-uppercase"
                                        style="font-size: 10px; letter-spacing: 0.04em;">Schedule Completion</div>
                                    <span class="kpi-badge optimal mt-1" id="kpiCompletionBadge">Target
                                        &gt;95.0%</span>
                                </div>
                                <div class="kpi-icon-pill success">
                                    <i class="mdi mdi-calendar-check"></i>
                                </div>
                            </div>
                            <div>
                                <div class="kpi-val font-mono" id="kpiCompletionVal" style="color: #34d399;">
                                    --<span class="unit">%</span>
                                </div>
                                <div class="kpi-progress-bar">
                                    <div class="kpi-progress-fill success" id="kpiCompletionBar" style="width: 0%;">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2"
                                    style="font-size: 10px;">
                                    <span class="text-muted">Realisasi:</span>
                                    <span class="text-light font-mono" id="kpiCompletionCount">-- / -- Unit</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Total Breakdown --}}
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="kpi-glass-card">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="text-secondary fw-bold text-uppercase"
                                        style="font-size: 10px; letter-spacing: 0.04em;">Total Breakdown</div>
                                    <span class="kpi-badge optimal mt-1" id="kpiBreakdownBadge">Target &lt;5x</span>
                                </div>
                                <div class="kpi-icon-pill alert">
                                    <i class="mdi mdi-alert-octagon-outline"></i>
                                </div>
                            </div>
                            <div>
                                <div class="kpi-val font-mono text-white" id="kpiBreakdownVal">
                                    --<span class="unit">x</span>
                                </div>
                                <div class="kpi-progress-bar">
                                    <div class="kpi-progress-fill danger" id="kpiBreakdownBar" style="width: 0%;">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2"
                                    style="font-size: 10px;">
                                    <span class="text-muted">Kategori:</span>
                                    <span class="text-secondary font-mono">Paket Korektif</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Downtime --}}
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="kpi-glass-card">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="text-secondary fw-bold text-uppercase"
                                        style="font-size: 10px; letter-spacing: 0.04em;">Downtime Lost</div>
                                    <span class="kpi-badge optimal mt-1" id="kpiDowntimeBadge">Target &lt;2.0%</span>
                                </div>
                                <div class="kpi-icon-pill">
                                    <i class="mdi mdi-timer-sand"></i>
                                </div>
                            </div>
                            <div>
                                <div class="kpi-val font-mono text-white" id="kpiDowntimeVal">
                                    --<span class="unit">%</span>
                                </div>
                                <div class="kpi-progress-bar">
                                    <div class="kpi-progress-fill" id="kpiDowntimeBar" style="width: 0%;"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2"
                                    style="font-size: 10px;">
                                    <span class="text-muted">Total Jam Lost:</span>
                                    <span class="text-light font-mono" id="kpiDowntimeHours">-- Jam</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 4: MTTR --}}
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="kpi-glass-card">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="text-secondary fw-bold text-uppercase"
                                        style="font-size: 10px; letter-spacing: 0.04em;">MTTR (Repair)</div>
                                    <span class="kpi-badge neutral mt-1" id="kpiMttrBadge">Target &lt;3.0 Jam</span>
                                </div>
                                <div class="kpi-icon-pill">
                                    <i class="mdi mdi-wrench-clock"></i>
                                </div>
                            </div>
                            <div>
                                <div class="kpi-val font-mono" id="kpiMttrVal" style="color: #38bdf8;">
                                    --<span class="unit">Jam</span>
                                </div>
                                <div class="kpi-progress-bar">
                                    <div class="kpi-progress-fill" id="kpiMttrBar" style="width: 0%;"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2"
                                    style="font-size: 10px;">
                                    <span class="text-muted">Formula:</span>
                                    <span class="text-light">Durasi / Korektif</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 5: MTBF --}}
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="kpi-glass-card">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="text-secondary fw-bold text-uppercase"
                                        style="font-size: 10px; letter-spacing: 0.04em;">MTBF (Reliability)</div>
                                    <span class="kpi-badge optimal mt-1" id="kpiMtbfBadge">Target &gt;250 Jam</span>
                                </div>
                                <div class="kpi-icon-pill">
                                    <i class="mdi mdi-heart-pulse"></i>
                                </div>
                            </div>
                            <div>
                                <div class="kpi-val font-mono text-white" id="kpiMtbfVal">
                                    --<span class="unit">Jam</span>
                                </div>
                                <div class="kpi-progress-bar">
                                    <div class="kpi-progress-fill" id="kpiMtbfBar" style="width: 0%;"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2"
                                    style="font-size: 10px;">
                                    <span class="text-muted">Mean Time Failure:</span>
                                    <span class="text-light">Operasi / Korektif</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 6: Cost Maintenance --}}
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="kpi-glass-card">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="text-secondary fw-bold text-uppercase"
                                        style="font-size: 10px; letter-spacing: 0.04em;">Maintenance Cost</div>
                                    <span class="kpi-badge neutral mt-1" id="kpiCostBadge">Budget 95M</span>
                                </div>
                                <div class="kpi-icon-pill">
                                    <i class="mdi mdi-cash-multiple"></i>
                                </div>
                            </div>
                            <div>
                                <div class="kpi-val font-mono text-white" id="kpiCostVal"
                                    style="font-size: 1.45rem;">
                                    --
                                </div>
                                <div class="kpi-progress-bar">
                                    <div class="kpi-progress-fill" id="kpiCostBar" style="width: 0%;"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2"
                                    style="font-size: 10px;">
                                    <span class="text-muted">Budget Terpakai:</span>
                                    <span class="text-light font-mono" id="kpiCostUtil">--%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════════════════════════
                 3. CHART 1: SCHEDULE MAINTENANCE (FULL WIDTH)
            ═══════════════════════════════════════════════════════════════ --}}
                <div class="row g-2 mb-3">
                    {{-- CHART 1: SCHEDULE MAINTENANCE --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-chart-box-outline text-info"></i> Schedule Maintenance
                                    </h4>
                                    {{-- <div class="mtc-card-subtitle">
                                        Sebaran rencana &amp; eksekusi: MHE (Electric &amp; Diesel), Motor Pompa, Sipil,
                                        dan Utility Plant
                                    </div> --}}
                                </div>
                                <span
                                    class="badge bg-dark text-secondary border border-secondary border-opacity-25 font-mono px-2 py-1"
                                    style="font-size: 10.5px;">
                                    Target Realisasi: &gt;95%
                                </span>
                            </div>
                            <div id="chartScheduleMaintenance" style="min-height: 290px;"></div>
                        </div>
                    </div>

                    {{-- Chart 2: Total Breakdown Frequency --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-chart-timeline-variant-shimmer text-danger"></i>
                                        Total Breakdown (Frequency Korektif)
                                    </h4>
                                    {{-- <div class="mtc-card-subtitle">
                                        Frekuensi insiden breakdown mesin per minggu (W1, W2, W3...) dengan threshold
                                        target &lt;5x
                                    </div> --}}
                                </div>
                                <span
                                    class="badge bg-dark text-secondary border border-secondary border-opacity-25 font-mono px-2 py-1"
                                    style="font-size: 10.5px;">
                                    Target: &lt;5x
                                </span>
                            </div>
                            <div id="chartTotalBreakdown" style="min-height: 260px;"></div>
                        </div>
                    </div>

                    {{-- Chart 3: Total Downtime Percentage & Loss Trend --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-chart-areaspline text-info"></i>
                                        Total Downtime
                                    </h4>
                                    {{-- <div class="mtc-card-subtitle">
                                        Persentase downtime dari 624 jam kerja standar per periode dengan garis Average
                                        &amp; Target
                                    </div> --}}
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span
                                        class="badge bg-dark text-info border border-info border-opacity-25 font-mono px-2 py-1"
                                        id="downtimeAvgBadge" style="font-size: 10.5px;">
                                        Avg: 1.5%
                                    </span>
                                    <span
                                        class="badge bg-dark text-danger border border-danger border-opacity-25 font-mono px-2 py-1"
                                        style="font-size: 10.5px;">
                                        Target: &lt;2%
                                    </span>
                                </div>
                            </div>
                            <div id="chartTotalDowntime" style="min-height: 260px;"></div>
                        </div>
                    </div>

                    {{-- Table 4: Top Mesin MTBF Terendah --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-alert-circle-outline"></i>
                                        Top Mesin MTBF Terendah
                                    </h4>
                                    <div class="mtc-card-subtitle">Mesin paling sering rusak (Reliability Risk)</div>
                                </div>
                                <span
                                    class="badge bg-danger bg-opacity-20 border border-danger border-opacity-25 font-mono"
                                    style="font-size: 10px;">
                                    Risk Watch
                                </span>
                            </div>
                            <div class="table-scrollable">
                                <table class="table mtc-table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 28px;">#</th>
                                            <th>Mesin / Aset</th>
                                            <th>Area</th>
                                            <th class="text-end">MTBF</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyTopMtbf">
                                        {{-- Dynamically injected --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Table 5: Top MTTR Terlama --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-clock-alert-outline"></i>
                                        Top MTTR Terlama
                                    </h4>
                                    <div class="mtc-card-subtitle">Perbaikan paling lama (Maintenance Bottleneck)</div>
                                </div>
                                <span class="badge bg-dark text-info border border-info border-opacity-25 font-mono"
                                    style="font-size: 10px;">
                                    Bottleneck
                                </span>
                            </div>
                            <div class="table-scrollable">
                                <table class="table mtc-table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 28px;">#</th>
                                            <th>Mesin / Aset</th>
                                            <th>Area</th>
                                            <th class="text-end">MTTR</th>
                                            <th>Kendala / Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyTopMttr">
                                        {{-- Dynamically injected --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Table 6: Top Cost Tertinggi --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-cash-fast text-info"></i>
                                        Top Cost Tertinggi
                                    </h4>
                                    <div class="mtc-card-subtitle">Pengeluaran terbesar (Budget Impact)</div>
                                </div>
                                <span class="badge bg-dark border border-secondary border-opacity-25 font-mono"
                                    style="font-size: 10px;">
                                    Top Spender
                                </span>
                            </div>
                            <div class="table-scrollable">
                                <table class="table mtc-table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 28px;">#</th>
                                            <th>Mesin / Aset</th>
                                            <th>Area</th>
                                            <th class="text-end">Total Cost</th>
                                            <th class="text-end">% Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyTopCost">
                                        {{-- Dynamically injected --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Chart 7: Unit Off --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-power-plug-off"></i>
                                        Unit Off (Area Breakdown)
                                    </h4>
                                    <div class="mtc-card-subtitle">
                                        Proporsi unit maintenance/breakdown yang sedang offline
                                    </div>
                                </div>
                                <span class="badge bg-danger bg-opacity-20 border border-danger border-opacity-25"
                                    id="unitOffTotalBadge" style="font-size: 10.5px;">
                                    4 Units Off
                                </span>
                            </div>
                            <div id="chartUnitOff" style="min-height: 280px;"></div>
                        </div>
                    </div>

                    {{-- Chart 8: MTTR vs MTBF Correlation --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-chart-line text-info"></i>
                                        Korelasi MTBF vs MTTR
                                    </h4>
                                    <div class="mtc-card-subtitle">
                                        Tren perbandingan MTBF vs MTTR dalam satuan Jam (Hours)
                                    </div>
                                </div>
                                <span class="badge bg-dark text-info border border-info border-opacity-25"
                                    style="font-size: 10px;">
                                    Dual Axis (Jam)
                                </span>
                            </div>
                            <div id="chartMttrMtbfCorrelation" style="min-height: 280px;"></div>
                        </div>
                    </div>

                    {{-- Chart 9: Cost Maintenance Details --}}
                    <div class="col-xl-4 col-lg-4">
                        <div class="mtc-chart-card h-100">
                            <div class="mtc-card-header">
                                <div>
                                    <h4 class="mtc-card-title">
                                        <i class="mdi mdi-currency-usd text-info"></i>
                                        Detail Maintenance Cost
                                    </h4>
                                    <div class="mtc-card-subtitle">
                                        Sparepart, Pelumas/Consumable, &amp; Jasa Vendor (W1, W2...)
                                    </div>
                                </div>
                                <span
                                    class="badge bg-dark text-secondary border border-secondary border-opacity-25 font-mono"
                                    id="budgetLimitBadge" style="font-size: 10.5px;">
                                    Budget: 95M
                                </span>
                            </div>
                            <div id="chartCostMaintenance" style="min-height: 280px;"></div>
                        </div>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════════════════════════
                 7. LIVE UNIT OFF DETAILED TABLE SECTION
            ═══════════════════════════════════════════════════════════════ --}}
                <div class="row g-2" id="tableOfflineUnitsSection">
                </div>

            </div>
        </div>

        <!-- JAVASCRIPT DEPENDENCIES -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="{{ asset('material/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <script>
            $(document).ready(function() {
                // ─────────────────────────────────────────────────────────────
                // GLOBAL STATE & APEXCHARTS INSTANCES
                // ─────────────────────────────────────────────────────────────
                let activeFilterType = 'weekly';
                let charts = {
                    scheduleMaintenance: null,
                    totalBreakdown: null,
                    totalDowntime: null,
                    unitOff: null,
                    mttrMtbfCorrelation: null,
                    costMaintenance: null
                };

                // Initialize Flatpickr for custom range
                const fpCustom = flatpickr("#customDateRange", {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    defaultDate: [
                        new Date(new Date().setDate(new Date().getDate() - 30)),
                        new Date()
                    ],
                    theme: "dark",
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length === 2) {
                            fetchDashboardData();
                        }
                    }
                });

                // ─────────────────────────────────────────────────────────────
                // FILTER CONTROLS & EVENT LISTENERS
                // ─────────────────────────────────────────────────────────────
                $('#filterTypeSwitch .filter-pill-btn').on('click', function() {
                    $('#filterTypeSwitch .filter-pill-btn').removeClass('active');
                    $(this).addClass('active');
                    activeFilterType = $(this).data('type');

                    $('#weeklyFilterGroup').toggleClass('d-none', activeFilterType !== 'weekly');
                    $('#monthlyFilterGroup').toggleClass('d-none', activeFilterType !== 'monthly');
                    $('#customFilterGroup').toggleClass('d-none', activeFilterType !== 'custom');

                    fetchDashboardData();
                });

                $('#selectWeek, #selectMonth, #selectYear').on('change', function() {
                    fetchDashboardData();
                });

                $('#btnPrevWeek').on('click', function() {
                    let cur = parseInt($('#selectWeek').val()) || 1;
                    if (cur > 1) {
                        $('#selectWeek').val(cur - 1).trigger('change');
                    }
                });

                $('#btnNextWeek').on('click', function() {
                    let cur = parseInt($('#selectWeek').val()) || 1;
                    if (cur < 52) {
                        $('#selectWeek').val(cur + 1).trigger('change');
                    }
                });

                $('#btnRefresh').on('click', function() {
                    const $icon = $('#refreshIcon');
                    $icon.addClass('mdi-spin');
                    fetchDashboardData().always(() => {
                        setTimeout(() => $icon.removeClass('mdi-spin'), 600);
                    });
                });

                // Fullscreen Toggle
                $('#btnFullscreen').on('click', function() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => {
                            console.log("Error attempting to enable fullscreen:", err);
                        });
                        $('#fsIcon').removeClass('mdi-fullscreen').addClass('mdi-fullscreen-exit');
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                            $('#fsIcon').removeClass('mdi-fullscreen-exit').addClass('mdi-fullscreen');
                        }
                    }
                });

                document.addEventListener('fullscreenchange', function() {
                    const isFs = !!document.fullscreenElement;
                    $('#fsIcon').toggleClass('mdi-fullscreen-exit', isFs).toggleClass('mdi-fullscreen', !isFs);
                    setTimeout(() => {
                        window.dispatchEvent(new Event('resize'));
                    }, 300);
                });

                // ─────────────────────────────────────────────────────────────
                // FETCH DATA VIA AJAX
                // ─────────────────────────────────────────────────────────────
                function fetchDashboardData() {
                    const params = {
                        filter_type: activeFilterType,
                        year: $('#selectYear').val() || 2026,
                        month: $('#selectMonth').val() || {{ $currentMonth }},
                        week: $('#selectWeek').val() || {{ $currentWeek }}
                    };

                    if (activeFilterType === 'custom') {
                        const rangeStr = $('#customDateRange').val();
                        if (rangeStr && rangeStr.includes(' to ')) {
                            const parts = rangeStr.split(' to ');
                            params.start_date = parts[0];
                            params.end_date = parts[1];
                        }
                    }

                    return $.ajax({
                        url: "{{ route('mtc.dashboard.data') }}",
                        type: "GET",
                        data: params,
                        beforeSend: function() {
                            $('#activeFilterLabel').text('Memperbarui data...');
                        },
                        success: function(res) {
                            if (!res || !res.status) {
                                console.error('Invalid response', res);
                                return;
                            }
                            updateDashboardUI(res);
                        },
                        error: function(err) {
                            console.error('Failed to load dashboard data', err);
                            $('#activeFilterLabel').text('Error memuat data');
                        }
                    });
                }

                // ─────────────────────────────────────────────────────────────
                // UPDATE DASHBOARD UI
                // ─────────────────────────────────────────────────────────────
                function updateDashboardUI(data) {
                    // Meta Labels
                    $('#activeFilterLabel').text(data.filter_meta.label);
                    $('#operatingHoursLabel').text(data.filter_meta.operating_hours + ' Jam');
                    $('#lastUpdatedTime').text(new Date().toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    }) + ' WIB');

                    // KPI 1: Schedule Completion
                    const sc = data.kpi.schedule_completion;
                    $('#kpiCompletionVal').html(`${sc.value}<span class="unit">%</span>`);
                    $('#kpiCompletionBar').css('width', `${Math.min(100, sc.value)}%`);
                    $('#kpiCompletionCount').text(`${sc.completed_count} / ${sc.planned_count} Unit`);
                    $('#kpiCompletionBadge')
                        .toggleClass('optimal', sc.status === 'optimal')
                        .toggleClass('danger', sc.status !== 'optimal')
                        .text(`Target >${sc.target}% (${sc.value >= sc.target ? 'Achieved' : 'Need Focus'})`);

                    // KPI 2: Total Breakdown
                    const bd = data.kpi.total_breakdown;
                    $('#kpiBreakdownVal').html(`${bd.value}<span class="unit">x</span>`);
                    const bdPct = Math.min(100, (bd.value / (bd.target * 2)) * 100);
                    $('#kpiBreakdownBar').css('width', `${bdPct}%`);
                    $('#kpiBreakdownBadge')
                        .toggleClass('optimal', bd.status === 'optimal')
                        .toggleClass('danger', bd.status !== 'optimal')
                        .text(bd.status === 'optimal' ? `Aman (<${bd.target}x)` : `Alarm (>${bd.target}x)`);

                    // KPI 3: Downtime
                    const dt = data.kpi.downtime;
                    $('#kpiDowntimeVal').html(`${dt.percentage}<span class="unit">%</span>`);
                    $('#kpiDowntimeHours').text(`${dt.hours} Jam`);
                    const dtPct = Math.min(100, (dt.percentage / (dt.target_percentage * 2)) * 100);
                    $('#kpiDowntimeBar').css('width', `${dtPct}%`);
                    $('#kpiDowntimeBadge')
                        .toggleClass('optimal', dt.status === 'optimal')
                        .toggleClass('danger', dt.status !== 'optimal')
                        .text(`Target <${dt.target_percentage}%`);

                    // KPI 4: MTTR
                    const mttr = data.kpi.mttr;
                    $('#kpiMttrVal').html(`${mttr.value}<span class="unit">${mttr.unit}</span>`);
                    const mttrPct = Math.min(100, (mttr.value / (mttr.target * 2)) * 100);
                    $('#kpiMttrBar').css('width', `${mttrPct}%`);
                    $('#kpiMttrBadge')
                        .toggleClass('neutral', mttr.status === 'optimal')
                        .toggleClass('danger', mttr.status !== 'optimal')
                        .text(`Target <${mttr.target} Jam`);

                    // KPI 5: MTBF
                    const mtbf = data.kpi.mtbf;
                    $('#kpiMtbfVal').html(`${mtbf.value}<span class="unit">${mtbf.unit}</span>`);
                    const mtbfPct = Math.min(100, (mtbf.value / 600) * 100);
                    $('#kpiMtbfBar').css('width', `${mtbfPct}%`);
                    $('#kpiMtbfBadge')
                        .toggleClass('optimal', mtbf.status === 'optimal')
                        .toggleClass('danger', mtbf.status !== 'optimal')
                        .text(`Target >${mtbf.target} Jam`);

                    // KPI 6: Cost Maintenance
                    const cost = data.kpi.cost;
                    $('#kpiCostVal').text(cost.value);
                    $('#kpiCostUtil').text(`${cost.budget_utilization}%`);
                    $('#kpiCostBar').css('width', `${Math.min(100, cost.budget_utilization)}%`);
                    $('#kpiCostBadge').text(`Budget ${cost.budget}`);

                    // Render Charts
                    renderScheduleMaintenanceChart(data.charts.schedule_maintenance);
                    renderTotalBreakdownChart(data.charts.total_breakdown);
                    renderTotalDowntimeChart(data.charts.total_downtime);
                    renderUnitOffChart(data.charts.unit_off);
                    renderMttrMtbfCorrelationChart(data.charts.mttr_mtbf_correlation);
                    renderCostMaintenanceChart(data.charts.cost_maintenance);

                    // Render Tables
                    renderTopMtbfTable(data.tables.top_mtbf);
                    renderTopMttrTable(data.tables.top_mttr);
                    renderTopCostTable(data.tables.top_cost);
                    renderUnitOffDetailsTable(data.tables.unit_off_list);
                }

                // ─────────────────────────────────────────────────────────────
                // RENDER CHARTS FUNCTIONS (COHESIVE PALETTE)
                // ─────────────────────────────────────────────────────────────

                // Chart 1: Schedule Maintenance
                function renderScheduleMaintenanceChart(chartData) {
                    const options = {
                        series: chartData.series,
                        chart: {
                            type: 'bar',
                            height: 290,
                            stacked: true,
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 600
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                borderRadius: 3,
                                columnWidth: '40%',
                                dataLabels: {
                                    total: {
                                        enabled: true,
                                        style: {
                                            color: '#f8fafc',
                                            fontSize: '10px',
                                            fontWeight: 700
                                        }
                                    }
                                }
                            }
                        },
                        colors: ['#38bdf8', '#10b981', '#f59e0b', '#a855f7'],
                        dataLabels: {
                            enabled: false
                        },
                        xaxis: {
                            categories: chartData.categories,
                            labels: {
                                style: {
                                    colors: '#94a3b8',
                                    fontSize: '10.5px',
                                    fontFamily: 'JetBrains Mono'
                                }
                            },
                            axisBorder: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Jumlah Unit Mesin',
                                style: {
                                    color: '#94a3b8',
                                    fontSize: '10.5px'
                                }
                            },
                            labels: {
                                style: {
                                    colors: '#94a3b8',
                                    fontSize: '10.5px'
                                }
                            }
                        },
                        grid: {
                            borderColor: 'rgba(255, 255, 255, 0.06)',
                            strokeDashArray: 3
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right',
                            labels: {
                                colors: '#cbd5e1'
                            },
                            fontSize: '10.5px',
                            markers: {
                                radius: 2
                            }
                        },
                        tooltip: {
                            theme: 'dark',
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: val => val + ' Unit Mesin'
                            }
                        }
                    };

                    if (charts.scheduleMaintenance) charts.scheduleMaintenance.destroy();
                    charts.scheduleMaintenance = new ApexCharts(document.querySelector("#chartScheduleMaintenance"),
                        options);
                    charts.scheduleMaintenance.render();
                }

                // Chart 2: Total Breakdown
                function renderTotalBreakdownChart(chartData) {
                    const options = {
                        series: chartData.series,
                        chart: {
                            type: 'bar',
                            height: 260,
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true
                            }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                columnWidth: '30%',
                                dataLabels: {
                                    position: 'top'
                                }
                            }
                        },
                        colors: ['#f43f5e'],
                        dataLabels: {
                            enabled: true,
                            formatter: val => val + 'x',
                            offsetY: -16,
                            style: {
                                fontSize: '10px',
                                fontWeight: 700,
                                colors: ['#ffffff']
                            }
                        },
                        annotations: {
                            yaxis: [{
                                y: chartData.target,
                                borderColor: '#64748b',
                                strokeDashArray: 4,
                                label: {
                                    borderColor: '#64748b',
                                    style: {
                                        color: '#ffffff',
                                        background: '#334155',
                                        fontSize: '9.5px',
                                        fontWeight: 700
                                    },
                                    text: 'Target Limit (' + chartData.target + 'x)'
                                }
                            }]
                        },
                        xaxis: {
                            categories: chartData.categories,
                            labels: {
                                style: {
                                    colors: '#94a3b8',
                                    fontSize: '10.5px',
                                    fontFamily: 'JetBrains Mono'
                                }
                            },
                            axisBorder: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Frekuensi Breakdown',
                                style: {
                                    color: '#94a3b8',
                                    fontSize: '10.5px'
                                }
                            },
                            labels: {
                                style: {
                                    colors: '#94a3b8',
                                    fontSize: '10.5px'
                                }
                            },
                            max: Math.max(6, Math.max(...chartData.series[0].data) + 2)
                        },
                        grid: {
                            borderColor: 'rgba(255, 255, 255, 0.06)',
                            strokeDashArray: 3
                        },
                        tooltip: {
                            theme: 'dark',
                            y: {
                                formatter: val => val + ' Kejadian Korektif'
                            }
                        }
                    };

                    if (charts.totalBreakdown) charts.totalBreakdown.destroy();
                    charts.totalBreakdown = new ApexCharts(document.querySelector("#chartTotalBreakdown"), options);
                    charts.totalBreakdown.render();
                }

                // Chart 3: Total Downtime
                function renderTotalDowntimeChart(chartData) {
                    const options = {
                        series: chartData.series,
                        chart: {
                            type: 'area',
                            height: 260,
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true
                            }
                        },
                        colors: ['#38bdf8'],
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.4,
                                opacityTo: 0.04,
                                stops: [0, 95, 100]
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2.5
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: val => val + '%',
                            style: {
                                fontSize: '9.5px',
                                fontWeight: 600,
                                colors: ['#38bdf8']
                            },
                            background: {
                                enabled: true,
                                foreColor: '#080c16',
                                padding: 2,
                                borderRadius: 2,
                                borderWidth: 0
                            }
                        },
                        annotations: {
                            yaxis: [{
                                    y: chartData.target,
                                    borderColor: '#f43f5e',
                                    strokeDashArray: 4,
                                    label: {
                                        borderColor: '#f43f5e',
                                        style: {
                                            color: '#ffffff',
                                            background: '#e11d48',
                                            fontSize: '9px',
                                            fontWeight: 700
                                        },
                                        text: 'Limit < ' + chartData.target + '%'
                                    }
                                },
                                {
                                    y: chartData.average,
                                    borderColor: '#64748b',
                                    strokeDashArray: 3,
                                    label: {
                                        borderColor: '#64748b',
                                        style: {
                                            color: '#ffffff',
                                            background: '#334155',
                                            fontSize: '9px',
                                            fontWeight: 700
                                        },
                                        text: 'Avg: ' + chartData.average + '%'
                                    }
                                }
                            ]
                        },
                        xaxis: {
                            categories: chartData.categories,
                            labels: {
                                style: {
                                    colors: '#94a3b8',
                                    fontSize: '10.5px',
                                    fontFamily: 'JetBrains Mono'
                                }
                            },
                            axisBorder: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Downtime (%)',
                                style: {
                                    color: '#94a3b8',
                                    fontSize: '10.5px'
                                }
                            },
                            labels: {
                                style: {
                                    colors: '#94a3b8',
                                    fontSize: '10.5px'
                                },
                                formatter: val => val + '%'
                            },
                            max: 3.0
                        },
                        grid: {
                            borderColor: 'rgba(255, 255, 255, 0.06)',
                            strokeDashArray: 3
                        },
                        tooltip: {
                            theme: 'dark',
                            y: {
                                formatter: val => val + '% dari Waktu Operasi'
                            }
                        }
                    };

                    if (charts.totalDowntime) charts.totalDowntime.destroy();
                    charts.totalDowntime = new ApexCharts(document.querySelector("#chartTotalDowntime"), options);
                    charts.totalDowntime.render();
                }

                // Chart 7: Unit Off (Donut)
                function renderUnitOffChart(chartData) {
                    const totalUnits = chartData.series.reduce((a, b) => a + b, 0);
                    $('#unitOffTotalBadge').text(totalUnits + ' Units Off');

                    const options = {
                        series: chartData.series,
                        labels: chartData.labels,
                        chart: {
                            type: 'donut',
                            height: 280,
                            animations: {
                                enabled: true
                            }
                        },
                        colors: ['#38bdf8', '#10b981', '#f59e0b', '#a855f7'],
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '72%',
                                    labels: {
                                        show: true,
                                        name: {
                                            show: true,
                                            fontSize: '11px',
                                            color: '#94a3b8'
                                        },
                                        value: {
                                            show: true,
                                            fontSize: '18px',
                                            fontFamily: 'JetBrains Mono',
                                            fontWeight: 800,
                                            color: '#ffffff',
                                            formatter: val => val + ' Unit'
                                        },
                                        total: {
                                            show: true,
                                            label: 'Total Off',
                                            color: '#94a3b8',
                                            fontSize: '10px',
                                            formatter: () => totalUnits + ' Unit'
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        legend: {
                            position: 'bottom',
                            fontSize: '10.5px',
                            labels: {
                                colors: '#cbd5e1'
                            },
                            markers: {
                                radius: 2
                            }
                        },
                        stroke: {
                            colors: ['#0f172a'],
                            width: 2
                        },
                        tooltip: {
                            theme: 'dark',
                            y: {
                                formatter: val => val + ' Unit Off'
                            }
                        }
                    };

                    if (charts.unitOff) charts.unitOff.destroy();
                    charts.unitOff = new ApexCharts(document.querySelector("#chartUnitOff"), options);
                    charts.unitOff.render();
                }

                // Chart 8: MTBF vs MTTR Correlation Line Chart
                function renderMttrMtbfCorrelationChart(chartData) {
                    const options = {
                        series: chartData.series,
                        chart: {
                            type: 'line',
                            height: 280,
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true
                            }
                        },
                        colors: ['#38bdf8', '#f59e0b'],
                        stroke: {
                            curve: 'smooth',
                            width: [3, 3]
                        },
                        markers: {
                            size: 5,
                            strokeWidth: 2,
                            strokeColors: '#080c16',
                            hover: {
                                size: 7
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(val, opt) {
                                return opt.seriesIndex === 0 ? val + 'h' : Number(val).toFixed(1) + 'h';
                            },
                            style: {
                                fontSize: '9.5px',
                                fontFamily: 'JetBrains Mono',
                                fontWeight: 700,
                                colors: ['#38bdf8', '#f59e0b']
                            },
                            background: {
                                enabled: true,
                                foreColor: '#080c16',
                                padding: 2,
                                borderRadius: 2,
                                borderWidth: 0
                            }
                        },
                        xaxis: {
                            categories: chartData.categories,
                            labels: {
                                style: {
                                    colors: '#94a3b8',
                                    fontSize: '10px',
                                    fontFamily: 'JetBrains Mono'
                                }
                            },
                            axisBorder: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        yaxis: [{
                                title: {
                                    text: 'MTBF (Jam)',
                                    style: {
                                        color: '#38bdf8',
                                        fontSize: '10px',
                                        fontWeight: 600
                                    }
                                },
                                labels: {
                                    formatter: val => Math.round(val) + 'h',
                                    style: {
                                        colors: '#38bdf8',
                                        fontSize: '10px',
                                        fontFamily: 'JetBrains Mono'
                                    }
                                }
                            },
                            {
                                opposite: true,
                                title: {
                                    text: 'MTTR (Jam)',
                                    style: {
                                        color: '#f59e0b',
                                        fontSize: '10px',
                                        fontWeight: 600
                                    }
                                },
                                labels: {
                                    formatter: val => Number(val).toFixed(1) + 'h',
                                    style: {
                                        colors: '#f59e0b',
                                        fontSize: '10px',
                                        fontFamily: 'JetBrains Mono'
                                    }
                                }
                            }
                        ],
                        grid: {
                            borderColor: 'rgba(255, 255, 255, 0.06)',
                            strokeDashArray: 3
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right',
                            labels: {
                                colors: '#cbd5e1'
                            },
                            fontSize: '10.5px',
                            markers: {
                                radius: 2
                            }
                        },
                        tooltip: {
                            theme: 'dark',
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function(val, {
                                    seriesIndex
                                }) {
                                    return seriesIndex === 0 ? val + ' Jam (Reliability)' : val +
                                        ' Jam (Recovery)';
                                }
                            }
                        }
                    };

                    if (charts.mttrMtbfCorrelation) charts.mttrMtbfCorrelation.destroy();
                    charts.mttrMtbfCorrelation = new ApexCharts(document.querySelector("#chartMttrMtbfCorrelation"),
                        options);
                    charts.mttrMtbfCorrelation.render();
                }

                // Chart 9: Cost Maintenance
                function renderCostMaintenanceChart(chartData) {
                    const options = {
                        series: chartData.series,
                        chart: {
                            type: 'line',
                            height: 290,
                            stacked: false,
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true
                            }
                        },
                        stroke: {
                            width: [0, 0, 0, 2.5],
                            curve: 'smooth'
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '40%',
                                borderRadius: 3
                            }
                        },
                        colors: ['#38bdf8', '#0284c7', '#64748b', '#f43f5e'],
                        xaxis: {
                            categories: chartData.categories,
                            labels: {
                                style: {
                                    colors: '#94a3b8',
                                    fontSize: '10.5px',
                                    fontFamily: 'JetBrains Mono'
                                }
                            },
                            axisBorder: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        yaxis: [{
                                title: {
                                    text: 'Biaya Mingguan (Juta IDR)',
                                    style: {
                                        color: '#94a3b8',
                                        fontSize: '10px'
                                    }
                                },
                                labels: {
                                    style: {
                                        colors: '#94a3b8',
                                        fontSize: '10px'
                                    },
                                    formatter: val => 'Rp ' + val + 'M'
                                }
                            },
                            {
                                opposite: true,
                                title: {
                                    text: 'Kumulatif (Juta IDR)',
                                    style: {
                                        color: '#f43f5e',
                                        fontSize: '10px'
                                    }
                                },
                                labels: {
                                    style: {
                                        colors: '#f43f5e',
                                        fontSize: '10px'
                                    },
                                    formatter: val => 'Rp ' + val + 'M'
                                }
                            }
                        ],
                        grid: {
                            borderColor: 'rgba(255, 255, 255, 0.06)',
                            strokeDashArray: 3
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right',
                            labels: {
                                colors: '#cbd5e1'
                            },
                            fontSize: '10.5px'
                        },
                        tooltip: {
                            theme: 'dark',
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: val => 'Rp ' + val + ' Juta'
                            }
                        }
                    };

                    if (charts.costMaintenance) charts.costMaintenance.destroy();
                    charts.costMaintenance = new ApexCharts(document.querySelector("#chartCostMaintenance"), options);
                    charts.costMaintenance.render();
                }

                // ─────────────────────────────────────────────────────────────
                // RENDER TABLES & LISTS
                // ─────────────────────────────────────────────────────────────

                function renderTopMtbfTable(rows) {
                    let html = '';
                    rows.forEach(item => {
                        const rankClass = item.rank === 1 ? 'top-1' : '';
                        const badgeClass = item.status === 'Critical' ? 'danger' : (item.status === 'Warning' ?
                            'danger' : 'optimal');
                        html += `
                    <tr>
                        <td><span class="rank-badge ${rankClass}">${item.rank}</span></td>
                        <td>
                            <div class="fw-bold text-white">${item.nama_mesin}</div>
                            <div class="text-secondary small font-mono" style="font-size: 9.5px;">${item.kode_mesin}</div>
                        </td>
                        <td><span class="badge bg-dark text-secondary border border-secondary border-opacity-25" style="font-size: 9.5px;">${item.area}</span></td>
                        <td class="text-end font-mono fw-bold" style="color: #f43f5e;">${item.nilai_mtbf} Jam</td>
                        <td class="text-center">
                            <span class="kpi-badge ${badgeClass}">${item.status}</span>
                        </td>
                    </tr>
                `;
                    });
                    $('#tbodyTopMtbf').html(html);
                }

                function renderTopMttrTable(rows) {
                    let html = '';
                    rows.forEach(item => {
                        const rankClass = item.rank === 1 ? 'top-1' : '';
                        html += `
                    <tr>
                        <td><span class="rank-badge ${rankClass}">${item.rank}</span></td>
                        <td>
                            <div class="fw-bold text-white">${item.nama_mesin}</div>
                            <div class="text-secondary small font-mono" style="font-size: 9.5px;">${item.kode_mesin}</div>
                        </td>
                        <td><span class="badge bg-dark text-secondary border border-secondary border-opacity-25" style="font-size: 9.5px;">${item.area}</span></td>
                        <td class="text-end font-mono fw-bold text-info">${item.rata_mttr} Jam</td>
                        <td>
                            <span class="text-secondary small" title="${item.keterangan}">
                                ${item.keterangan.length > 30 ? item.keterangan.substring(0, 30) + '...' : item.keterangan}
                            </span>
                        </td>
                    </tr>
                `;
                    });
                    $('#tbodyTopMttr').html(html);
                }

                function renderTopCostTable(rows) {
                    let html = '';
                    rows.forEach(item => {
                        const rankClass = item.rank === 1 ? 'top-1' : '';
                        html += `
                    <tr>
                        <td><span class="rank-badge ${rankClass}">${item.rank}</span></td>
                        <td>
                            <div class="fw-bold text-white">${item.nama_mesin}</div>
                            <div class="text-secondary small font-mono" style="font-size: 9.5px;">${item.kode_mesin}</div>
                        </td>
                        <td><span class="badge bg-dark text-secondary border border-secondary border-opacity-25" style="font-size: 9.5px;">${item.area}</span></td>
                        <td class="text-end font-mono fw-bold text-info">${item.total_cost_formatted}</td>
                        <td class="text-end font-mono text-light">${item.persentase}%</td>
                    </tr>
                `;
                    });
                    $('#tbodyTopCost').html(html);
                }

                function renderUnitOffDetailsTable(units) {
                    let tableHtml = '';

                    units.forEach(u => {
                        const isAlert = u.status_badge === 'danger' || u.status_badge === 'warning';
                        const statusBadgeClass = isAlert ?
                            'bg-danger bg-opacity-20 text-danger border border-danger border-opacity-25' :
                            'bg-secondary bg-opacity-20 text-secondary border border-secondary border-opacity-25';

                        tableHtml += `
                    <tr>
                        <td><span class="badge bg-dark text-info border border-info border-opacity-25 font-mono">${u.unit_id}</span></td>
                        <td class="fw-bold text-white">${u.name}</td>
                        <td><span class="badge bg-dark text-secondary border border-secondary border-opacity-25">${u.category}</span></td>
                        <td class="text-secondary">${u.lokasi}</td>
                        <td class="font-mono text-light">${u.off_since}</td>
                        <td class="font-mono fw-bold text-info">${u.downtime_str}</td>
                        <td class="text-secondary">${u.cause}</td>
                        <td class="text-center">
                            <span class="badge ${statusBadgeClass} px-2 py-1">
                                ${u.status}
                            </span>
                        </td>
                    </tr>
                `;
                    });

                    $('#tbodyUnitOffDetails').html(tableHtml);
                }

                // ─────────────────────────────────────────────────────────────
                // INITIAL LOAD
                // ─────────────────────────────────────────────────────────────
                fetchDashboardData();
            });
        </script>
    </body>

</html>
