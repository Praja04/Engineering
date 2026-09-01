@extends('layouts.app')

@section('title', 'OEE Mesin Retail D1')

@section('styles')
<style>
    /* Dynamic Dual-Theme Variables (Velzon Light & Dark Mode Compatible) */
    :root {
        --oee-card-bg: #ffffff;
        --oee-card-border: rgba(0, 0, 0, 0.08);
        --oee-text-main: #212529;
        --oee-text-muted: #6c757d;
        --oee-stage-bg: radial-gradient(circle at center, #f8fafc 0%, #e2e8f0 100%);
        --oee-clock-bg: #f1f5f9;
        --oee-table-th: #f8fafc;
        --oee-table-border: #e2e8f0;
        --oee-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    [data-layout-mode="dark"] {
        --oee-card-bg: rgba(21, 30, 47, 0.9);
        --oee-card-border: rgba(255, 255, 255, 0.08);
        --oee-text-main: #f9fafb;
        --oee-text-muted: #9ca3af;
        --oee-stage-bg: radial-gradient(circle at center, rgba(15, 23, 42, 0.9) 0%, rgba(3, 7, 18, 0.95) 100%);
        --oee-clock-bg: rgba(17, 24, 39, 0.9);
        --oee-table-th: rgba(255, 255, 255, 0.04);
        --oee-table-border: rgba(255, 255, 255, 0.06);
        --oee-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
    }

    /* Remove outer space constraints & match Velzon layout */
    .oee-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid var(--oee-card-border);
        margin-bottom: 1.5rem;
    }

    .oee-title {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        margin: 0;
        color: var(--oee-text-main);
    }

    .oee-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 20px;
        font-weight: 600;
        background: rgba(6, 182, 212, 0.15);
        color: #06b6d4;
        border: 1px solid rgba(6, 182, 212, 0.3);
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 30px;
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
        font-weight: 600;
        font-size: 0.85rem;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 10px #10b981;
        animation: pulseAnimation 1.8s infinite;
    }

    @keyframes pulseAnimation {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(1.3); }
    }

    .server-clock {
        font-family: 'JetBrains Mono', monospace, monospace;
        font-size: 1.05rem;
        font-weight: 700;
        color: #06b6d4;
        background: var(--oee-clock-bg);
        padding: 6px 16px;
        border-radius: 8px;
        border: 1px solid var(--oee-card-border);
    }

    .btn-reset-pulse {
        background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        color: white;
        border: none;
        padding: 8px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.3);
    }

    .btn-reset-pulse:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.5);
    }

    /* Adaptive Cards */
    .oee-card {
        background: var(--oee-card-bg);
        border: 1px solid var(--oee-card-border);
        border-radius: 14px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--oee-shadow);
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .kpi-title {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--oee-text-muted);
    }

    .kpi-value {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1.1;
        margin: 8px 0;
        color: var(--oee-text-main);
    }

    .kpi-unit {
        font-size: 1rem;
        font-weight: 600;
        margin-left: 4px;
        color: var(--oee-text-muted);
    }

    /* Gauge Ring */
    .oee-ring-container {
        position: relative;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .oee-ring-svg {
        transform: rotate(-90deg);
    }

    .oee-ring-bg {
        fill: none;
        stroke: rgba(150, 150, 150, 0.2);
        stroke-width: 7;
    }

    .oee-ring-bar {
        fill: none;
        stroke: url(#cyan-gradient);
        stroke-width: 7;
        stroke-linecap: round;
        stroke-dasharray: 238;
        stroke-dashoffset: 238;
        transition: stroke-dashoffset 0.8s ease;
    }

    .oee-ring-text {
        position: absolute;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--oee-text-main);
    }

    /* Countdown Bar */
    .countdown-bar-bg {
        width: 100%;
        height: 8px;
        background: rgba(150, 150, 150, 0.2);
        border-radius: 10px;
        overflow: hidden;
        margin-top: 10px;
    }

    .countdown-bar-fill {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #8b5cf6, #c084fc);
        border-radius: 10px;
        transition: width 1s linear;
    }

    /* Machine Stage & Hotspots */
    .machine-stage {
        position: relative;
        width: 100%;
        height: 280px;
        background: var(--oee-stage-bg);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 1px solid var(--oee-card-border);
        margin-bottom: 1rem;
    }

    .machine-img {
        max-height: 88%;
        max-width: 88%;
        object-fit: contain;
        filter: drop-shadow(0 10px 25px rgba(6, 182, 212, 0.25));
    }

    .hotspot {
        position: absolute;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: rgba(6, 182, 212, 0.4);
        border: 2px solid #06b6d4;
        cursor: pointer;
        box-shadow: 0 0 12px #06b6d4;
        animation: pulseAnimation 2s infinite;
        z-index: 10;
    }

    .hotspot:hover .hotspot-tooltip {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .hotspot-1 { top: 38%; left: 18%; }
    .hotspot-2 { top: 48%; left: 42%; }
    .hotspot-3 { top: 30%; left: 52%; }
    .hotspot-4 { top: 62%; left: 74%; }
    .hotspot-5 { top: 26%; left: 68%; }

    .hotspot-tooltip {
        position: absolute;
        bottom: 28px;
        left: 50%;
        transform: translateX(-50%) translateY(10px);
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid rgba(6, 182, 212, 0.4);
        color: #ffffff;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        pointer-events: none;
    }

    .telemetry-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
        gap: 10px;
    }

    .telemetry-box {
        background: rgba(150, 150, 150, 0.05);
        border: 1px solid var(--oee-card-border);
        padding: 10px;
        border-radius: 8px;
        text-align: center;
    }

    .telemetry-label {
        font-size: 0.7rem;
        color: var(--oee-text-muted);
        text-transform: uppercase;
        font-weight: 600;
    }

    .telemetry-val {
        font-size: 0.95rem;
        font-weight: 700;
        margin-top: 2px;
    }

    /* Table styling */
    .table-dark-custom {
        width: 100%;
        color: var(--oee-text-main);
        border-collapse: collapse;
    }

    .table-dark-custom th {
        background: var(--oee-table-th);
        padding: 10px 14px;
        font-size: 0.8rem;
        text-transform: uppercase;
        color: var(--oee-text-muted);
        border-bottom: 1px solid var(--oee-table-border);
    }

    .table-dark-custom td {
        padding: 12px 14px;
        border-bottom: 1px solid var(--oee-table-border);
        font-size: 0.88rem;
    }

    .oee-jam-badge {
        background: rgba(124, 58, 237, 0.12);
        color: #7c3aed !important;
        border: 1px solid rgba(124, 58, 237, 0.3);
        padding: 3px 9px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.8rem;
    }

    /* Modal Theme Dual-Mode Styling */
    .modal-content-custom {
        background: var(--oee-card-bg) !important;
        color: var(--oee-text-main) !important;
        border: 1px solid var(--oee-card-border) !important;
        border-radius: 14px;
        box-shadow: var(--oee-shadow);
        transition: background 0.3s ease, color 0.3s ease;
    }

    .modal-summary-box {
        background: rgba(150, 150, 150, 0.05);
        border: 1px solid var(--oee-card-border);
    }

    [data-layout-mode="light"] .modal-content-custom {
        background: #ffffff !important;
        color: #1e293b !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
    }

    [data-layout-mode="light"] .modal-title-custom {
        color: #0f172a !important;
    }

    [data-layout-mode="light"] .btn-close-custom {
        filter: invert(1);
    }

    [data-layout-mode="light"] .modal-summary-box {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
    }

    [data-layout-mode="dark"] .oee-jam-badge {
        background: rgba(139, 92, 246, 0.25);
        color: #c084fc !important;
        border-color: rgba(139, 92, 246, 0.4);
    }

    .table-dark-custom tr.shift-row-item {
        cursor: pointer;
        transition: background 0.15s ease;
    .table-dark-custom tr.shift-row-item {
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .table-dark-custom tr.shift-row-item:hover {
        background: rgba(139, 92, 246, 0.12) !important;
    }

    /* Complete TV Fullscreen Display Mode: Hide Sidebar, Topbar, & Footer */
    #page-topbar, 
    .app-menu,
    footer.footer,
    #back-to-top { 
        display: none !important; 
    }

    .main-content { 
        margin-left: 0 !important; 
        margin-top: 0 !important; 
        padding-top: 0 !important; 
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Header Bar -->
        <div class="oee-header">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('dashboard.downtimemesin.all') }}" class="btn btn-soft-info btn-sm d-flex align-items-center gap-1 px-3 py-2 fw-semibold" style="border-radius: 8px;">
                    <i class="ri-arrow-left-line fs-16"></i>
                    <span>All Mesin</span>
                </a>
                <div>
                    <h1 class="oee-title mb-0">DETAIL OEE MESIN <span style="font-weight: 700; color: #8b5cf6;">| {{ $machine ?? 'D1' }}</span></h1>
                    <div class="mt-1">
                        <span class="fs-13" style="color: var(--oee-text-muted);">Mesin Rotary Packaging Pouch {{ $machine ?? 'D1' }}</span>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="status-pill">
                    <span class="pulse-dot"></span>
                    <span id="mqtt-status">MQTT ONLINE</span>
                </div>

                <div class="server-clock" id="clock-wib">
                    --:--:-- WIB
                </div>

                <!-- Theme Toggle Button -->
                <button class="btn btn-soft-secondary btn-icon btn-sm" id="btn-theme-toggle" title="Toggle Light / Dark Mode">
                    <i class="bx bx-sun fs-18" id="theme-icon"></i>
                </button>
            </div>
        </div>

        <!-- Top Grid: Visualizer & KPI Cards -->
        <div class="row">
            <!-- Left: Visualizer Card -->
            <div class="col-lg-5 col-xl-4">
                <div class="oee-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold fs-14" style="color: var(--oee-text-main);"><i class="ri-box-3-line me-1 text-info"></i> Visualisasi Mesin {{ $machine ?? 'D1' }}</span>
                        <span class="fs-11" style="color: var(--oee-text-muted);">Rotary Packaging</span>
                    </div>

                    <div class="machine-stage">
                        <!-- User Machine Image -->
                        <img src="{{ asset('assets/machine.png') }}" alt="Mesin Rotary Packaging {{ $machine ?? 'D1' }}" class="machine-img">

                        <!-- Hotspots -->
                        <div class="hotspot hotspot-1">
                            <div class="hotspot-tooltip">
                                <strong>Pouch Feeder Station</strong><br>
                                Kecepatan: 45 pouch/min<br>
                                Status: Operational
                            </div>
                        </div>
                        <div class="hotspot hotspot-2">
                            <div class="hotspot-tooltip">
                                <strong>Rotary Indexing Carousel</strong><br>
                                Posisi: Station 4 (Fill)<br>
                                RPM: 1800 RPM
                            </div>
                        </div>
                        <div class="hotspot hotspot-3">
                            <div class="hotspot-tooltip">
                                <strong>Liquid/Powder Filling Nozzle</strong><br>
                                Volume Target: 250 ml<br>
                                Akurasi: ±0.5g
                            </div>
                        </div>
                        <div class="hotspot hotspot-4">
                            <div class="hotspot-tooltip">
                                <strong>HMI Touch Panel PLC</strong><br>
                                Pub: RST_{{ $machine ?? 'D1' }} (Reset Pulsa)<br>
                                Sub: OEE_{{ $machine ?? 'D1' }}
                            </div>
                        </div>
                        <div class="hotspot hotspot-5">
                            <div class="hotspot-tooltip">
                                <strong>Top Heat Sealing Bar</strong><br>
                                Temperatur: 185°C<br>
                                Pressure: 6.2 Bar
                            </div>
                        </div>
                    </div>

                    <div class="telemetry-row">
                        <div class="telemetry-box">
                            <div class="telemetry-label">SPEED RATE</div>
                            <div class="telemetry-val" style="color: #10b981;">42 ppm</div>
                        </div>
                        <div class="telemetry-box">
                            <div class="telemetry-label">UPTIME SHIFT</div>
                            <div class="telemetry-val" style="color: #06b6d4;" id="val-uptime-shift">0 min</div>
                        </div>
                        <div class="telemetry-box">
                            <div class="telemetry-label">DOWNTIME SHIFT</div>
                            <div class="telemetry-val" style="color: #ef4444;" id="val-downtime-shift">0 min</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: KPI Cards Grid -->
            <div class="col-lg-7 col-xl-8">
                <div class="row">
                    <!-- Card 1: OEE Uptime -->
                    <div class="col-md-6">
                        <div class="oee-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="kpi-title">OEE UPTIME (OEE_{{ $machine ?? 'D1' }})</div>
                                    <div class="fs-12" style="color: var(--oee-text-muted);" id="oee-interval-subtitle">--.00 - --.00</div>
                                </div>
                                <div class="avatar-xs">
                                    <span class="avatar-title bg-soft-info text-info rounded-circle">
                                        <i class="ri-timer-flash-line fs-16"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between my-3">
                                <div>
                                    <div class="kpi-value" id="val-oee">0<span class="kpi-unit">min</span></div>
                                    <div class="fs-12 text-success mt-1" id="val-efficiency">
                                        <i class="ri-line-chart-line"></i> 100% Efficiency
                                    </div>
                                </div>

                                <div class="oee-ring-container">
                                    <svg class="oee-ring-svg" width="80" height="80" viewBox="0 0 90 90">
                                        <defs>
                                            <linearGradient id="cyan-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                                <stop offset="0%" stop-color="#06b6d4" />
                                                <stop offset="100%" stop-color="#3b82f6" />
                                            </linearGradient>
                                        </defs>
                                        <circle class="oee-ring-bg" cx="45" cy="45" r="38"></circle>
                                        <circle class="oee-ring-bar" id="oee-gauge-bar" cx="45" cy="45" r="38"></circle>
                                    </svg>
                                    <div class="oee-ring-text" id="oee-pct">0%</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between fs-12 pt-2" style="border-top: 1px solid var(--oee-card-border); color: var(--oee-text-muted);">
                                <span>Target Max: 60 min/jam</span>
                                <span style="color: #06b6d4;" id="oee-status-badge">Normal</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Total Product Counter -->
                    <div class="col-md-6">
                        <div class="oee-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="kpi-title">TOTAL COUNTER (CT_PRODUCT{{ $machine ?? 'D1' }})</div>
                                    <div class="fs-12" style="color: var(--oee-text-muted);">Output Pouch Terkemas</div>
                                </div>
                                <div class="avatar-xs">
                                    <span class="avatar-title bg-soft-success text-success rounded-circle">
                                        <i class="ri-checkbox-circle-line fs-16"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="my-3">
                                <div class="kpi-value" id="val-product" style="color: #10b981;">0<span class="kpi-unit">pcs</span></div>
                                <div class="fs-12 mt-1" style="color: var(--oee-text-muted);" id="val-speed">
                                    Kecepatan: ~0.0 pcs / min
                                </div>
                            </div>

                            <div class="d-flex justify-content-between fs-12 pt-2" style="border-top: 1px solid var(--oee-card-border); color: var(--oee-text-muted);">
                                <span>Target Shift: 30,000 pcs</span>
                                <span style="color: #10b981;" id="val-target-pct">0.0% Target</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Downtime Card -->
                    <div class="col-md-6">
                        <div class="oee-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="kpi-title">DOWNTIME (MENIT)</div>
                                    <div class="fs-12" style="color: var(--oee-text-muted);" id="downtime-interval-subtitle">--.00 - --.00</div>
                                </div>
                                <div class="avatar-xs">
                                    <span class="avatar-title bg-soft-warning text-warning rounded-circle">
                                        <i class="ri-error-warning-line fs-16"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="my-3">
                                <div class="kpi-value" id="val-stop" style="color: #10b981;">0<span class="kpi-unit">min</span></div>
                                <div class="fs-12 text-success mt-1" id="downtime-status-text">
                                    <i class="ri-check-double-line"></i> Tidak ada Downtime
                                </div>
                            </div>

                            <div class="d-flex justify-content-between fs-12 pt-2" style="border-top: 1px solid var(--oee-card-border); color: var(--oee-text-muted);">
                                <span>Status Shift: Berjalan</span>
                                <span style="color: #10b981;" id="downtime-operation-badge">Smooth Operation</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: OEE Shift Performance (%) -->
                    <div class="col-md-6">
                        <div class="oee-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="kpi-title">OEE SHIFT PERFORMANCE (%)</div>
                                    <div class="fs-12" style="color: var(--oee-text-muted);" id="current-shift-name">Shift 1 (06.00 - 14.00)</div>
                                </div>
                                <div class="avatar-xs">
                                    <span class="avatar-title bg-soft-purple text-purple rounded-circle">
                                        <i class="ri-pie-chart-2-line fs-16"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="my-3">
                                <div class="kpi-value" id="val-oee-shift-pct" style="color: #8b5cf6;">0.0<span class="kpi-unit">%</span></div>
                                <div class="countdown-bar-bg">
                                    <div class="countdown-bar-fill" id="shift-oee-bar" style="background: linear-gradient(90deg, #8b5cf6, #c084fc); width: 0%;"></div>
                                </div>
                            </div>

                            <div class="fs-11 pt-2" style="border-top: 1px solid var(--oee-card-border); color: var(--oee-text-muted);">
                                <span id="oee-formula-text">OEE = Counter / (42 × Uptime × 2)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Grid: Chart & Database History Table -->
        <div class="row">
            <!-- Left: 8-Hour OEE Chart -->
            <div class="col-lg-7">
                <div class="oee-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold fs-14" style="color: var(--oee-text-main);"><i class="ri-bar-chart-fill me-1 text-primary"></i> Grafik Performa Hourly OEE (8 Jam Terakhir)</span>
                        <span class="fs-11" style="color: var(--oee-text-muted);">Menit Hidup vs Output Produksi</span>
                    </div>
                    <div style="height: 260px; position: relative;">
                        <canvas id="oeeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Right: Shift OEE History Table -->
            <div class="col-lg-5">
                <div class="oee-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold fs-14" style="color: var(--oee-text-main);"><i class="ri-pie-chart-2-line me-1 text-purple"></i> Riwayat OEE Per Shift</span>
                        <button id="btn-refresh-db" class="btn btn-sm btn-soft-info fs-11 py-1 px-2">
                            <i class="ri-refresh-line me-1"></i> Refresh
                        </button>
                    </div>

                    <!-- Date Range Filter Bar (Default: 7 Days Back) -->
                    <div class="d-flex align-items-center gap-1 mb-2 p-1 px-2 rounded" style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--oee-card-border);">
                        <span class="fs-11 fw-bold text-muted me-1"><i class="ri-calendar-line text-info"></i> Filter:</span>
                        <input type="date" id="shift-start-date" class="form-control form-control-sm py-0 px-1 fs-11" style="width: 115px; background: transparent; border-color: var(--oee-card-border); color: var(--oee-text-main);">
                        <span class="fs-11 text-muted">s/d</span>
                        <input type="date" id="shift-end-date" class="form-control form-control-sm py-0 px-1 fs-11" style="width: 115px; background: transparent; border-color: var(--oee-card-border); color: var(--oee-text-main);">
                        <button id="btn-filter-shift" class="btn btn-sm btn-soft-purple py-0 px-2 fs-11 fw-bold ms-auto">
                            Filter
                        </button>
                    </div>

                    <div class="table-responsive" style="max-height: 215px; overflow-y: auto;">
                        <table class="table-dark-custom">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Shift</th>
                                    <th>Uptime</th>
                                    <th>Product</th>
                                    <th>OEE</th>
                                </tr>
                            </thead>
                            <tbody id="db-table-body">
                                <tr>
                                    <td colspan="5" class="text-center py-3" style="color: var(--oee-text-muted);">Memuat data shift...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Detail Log Shift -->
<div class="modal fade" id="modalShiftDetail" tabindex="-1" aria-labelledby="modalShiftDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-content-custom">
            <div class="modal-header border-0 pb-2 pt-4 px-4">
                <div>
                    <h5 class="modal-title modal-title-custom fw-bold fs-16" id="modalShiftDetailLabel">
                        <i class="ri-history-line text-purple me-2"></i>Detail Log Jam-Jam Shift
                    </h5>
                    <div id="modal-shift-subtitle" class="fs-12 text-muted mt-1">--</div>
                </div>
                <button type="button" class="btn-close btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded-3 modal-summary-box">
                    <div class="text-center">
                        <div class="fs-11 text-muted fw-semibold">TOTAL UPTIME</div>
                        <div class="fs-15 fw-bold text-info" id="modal-shift-uptime">-</div>
                    </div>
                    <div class="text-center px-4 border-start border-end border-secondary border-opacity-25">
                        <div class="fs-11 text-muted fw-semibold">TOTAL PRODUCT</div>
                        <div class="fs-15 fw-bold text-success" id="modal-shift-product">-</div>
                    </div>
                    <div class="text-center">
                        <div class="fs-11 text-muted fw-semibold">OEE SHIFT</div>
                        <div class="fs-15 fw-bold text-purple" id="modal-shift-oee">-</div>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                    <table class="table-dark-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Jam Log</th>
                                <th>Uptime (Min)</th>
                                <th>CT Product</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="modal-table-body">
                            <tr>
                                <td colspan="5" class="text-center py-3" style="color: var(--oee-text-muted);">Memuat data log jam...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const API_BASE = 'http://10.11.11.200:3000';
        const CURRENT_MACHINE = '{{ $machine ?? "D1" }}';
        let oeeChartInstance = null;
        // Theme Toggle Logic
        const themeBtn = document.getElementById('btn-theme-toggle');
        const themeIcon = document.getElementById('theme-icon');

        function syncThemeUI(theme) {
            document.documentElement.setAttribute('data-layout-mode', theme);
            document.body.setAttribute('data-layout-mode', theme);
            localStorage.setItem('theme', theme);
            if (themeIcon) {
                themeIcon.className = (theme === 'dark') ? 'bx bx-sun fs-18' : 'bx bx-moon fs-18';
            }
        }

        const savedTheme = localStorage.getItem('theme') || 'dark';
        syncThemeUI(savedTheme);

        if (themeBtn) {
            themeBtn.addEventListener('click', function() {
                const activeTheme = localStorage.getItem('theme') || 'dark';
                const nextTheme = (activeTheme === 'dark') ? 'light' : 'dark';
                syncThemeUI(nextTheme);
            });
        }

        // Elements
        const clockEl = document.getElementById('clock-wib');
        const mqttStatusEl = document.getElementById('mqtt-status');
        const valOeeEl = document.getElementById('val-oee');
        const valProductEl = document.getElementById('val-product');
        const valStopEl = document.getElementById('val-stop');
        const valSpeedEl = document.getElementById('val-speed');
        const valTargetPctEl = document.getElementById('val-target-pct');
        const oeeGaugeBar = document.getElementById('oee-gauge-bar');
        const oeePctText = document.getElementById('oee-pct');
        const countdownText = document.getElementById('countdown-text');
        const countdownBar = document.getElementById('countdown-bar');
        const nextResetLabel = document.getElementById('next-reset-time-label');
        const currentJamBadge = document.getElementById('current-jam-badge');
        const btnManualReset = document.getElementById('btn-manual-reset');
        const btnRefreshDb = document.getElementById('btn-refresh-db');
        const dbTableBody = document.getElementById('db-table-body');
        const downtimeStatusText = document.getElementById('downtime-status-text');

        // Date Filter Elements (Default: 7 Days Back to Today)
        const startDateInput = document.getElementById('shift-start-date');
        const endDateInput = document.getElementById('shift-end-date');
        const btnFilterShift = document.getElementById('btn-filter-shift');

        function formatDateToYMD(d) {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        const todayObj = new Date();
        const sevenDaysAgoObj = new Date();
        sevenDaysAgoObj.setDate(todayObj.getDate() - 7);

        if (startDateInput && !startDateInput.value) {
            startDateInput.value = formatDateToYMD(sevenDaysAgoObj);
        }
        if (endDateInput && !endDateInput.value) {
            endDateInput.value = formatDateToYMD(todayObj);
        }

        if (btnFilterShift) btnFilterShift.addEventListener('click', fetchShiftHistory);
        if (startDateInput) startDateInput.addEventListener('change', fetchShiftHistory);
        if (endDateInput) endDateInput.addEventListener('change', fetchShiftHistory);

        // WIB Clock Loop & Downtime Interval Subtitle
        function updateWibClock() {
            const now = new Date();
            const wibString = now.toLocaleTimeString('id-ID', {
                timeZone: 'Asia/Jakarta',
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }) + ' WIB';
            
            if (clockEl) clockEl.textContent = wibString;

            const currentHourStr = String(now.getHours()).padStart(2, '0');
            const currentMinStr = String(now.getMinutes()).padStart(2, '0');
            const intervalText = `${currentHourStr}.00 - ${currentHourStr}.${currentMinStr}`;

            const downtimeSubEl = document.getElementById('downtime-interval-subtitle');
            if (downtimeSubEl) downtimeSubEl.textContent = intervalText;

            const oeeSubEl = document.getElementById('oee-interval-subtitle');
            if (oeeSubEl) oeeSubEl.textContent = intervalText;
        }

        setInterval(updateWibClock, 1000);
        updateWibClock();

        // Helper function: Calculate OEE Shift Performance (%) based on Shift & Day rules
        function updateShiftPerformance(productVal, apiData = null) {
            const SPEED_DEFAULT = (apiData && apiData.speed_standard_ppm) ? apiData.speed_standard_ppm : 42;
            let shiftName = (apiData && apiData.shift_name) ? apiData.shift_name : '';
            let oeeShiftPct = (apiData && apiData.oee_shift_pct !== undefined) ? Number(apiData.oee_shift_pct).toFixed(1) : null;
            let currentShiftUptime = (apiData && apiData.shift_uptime_min !== undefined) ? Number(apiData.shift_uptime_min) : null;
            let currentShiftDowntime = (apiData && apiData.shift_downtime_min !== undefined) ? Number(apiData.shift_downtime_min) : null;

            if (!shiftName || oeeShiftPct === null || currentShiftUptime === null || currentShiftDowntime === null) {
                const now = new Date();
                const day = now.getDay();
                const isSaturday = (day === 6);
                const hour = now.getHours();
                const minute = now.getMinutes();
                const totalCurrentMinutes = (hour * 60) + minute;

                let shiftStartMin = 360;
                if (isSaturday) {
                    if (totalCurrentMinutes >= 360 && totalCurrentMinutes < 660) {
                        shiftName = 'Shift 1 (Sabtu: 06.00 - 11.00)';
                        shiftStartMin = 360;
                    } else if (totalCurrentMinutes >= 660 && totalCurrentMinutes < 960) {
                        shiftName = 'Shift 2 (Sabtu: 11.00 - 16.00)';
                        shiftStartMin = 660;
                    } else if (totalCurrentMinutes >= 960 && totalCurrentMinutes < 1260) {
                        shiftName = 'Shift 3 (Sabtu: 16.00 - 21.00)';
                        shiftStartMin = 960;
                    } else {
                        shiftName = 'Luar Jam Kerja (Sabtu)';
                        shiftStartMin = totalCurrentMinutes;
                    }
                } else {
                    if (totalCurrentMinutes >= 360 && totalCurrentMinutes < 840) {
                        shiftName = 'Shift 1 (06.00 - 14.00)';
                        shiftStartMin = 360;
                    } else if (totalCurrentMinutes >= 840 && totalCurrentMinutes < 1320) {
                        shiftName = 'Shift 2 (14.00 - 22.00)';
                        shiftStartMin = 840;
                    } else {
                        shiftName = 'Shift 3 (22.00 - 06.00)';
                        if (totalCurrentMinutes >= 1320) shiftStartMin = 1320;
                        else shiftStartMin = -120;
                    }
                }

                const elapsedShiftMin = Math.max(1, totalCurrentMinutes - shiftStartMin);
                currentShiftUptime = window.currentOeeVal || 0;
                if (window.lastHistoryRows && Array.isArray(window.lastHistoryRows)) {
                    window.lastHistoryRows.forEach(r => {
                        const rowVal = r.oee !== undefined ? r.oee : (r.oee_d1 || 0);
                        currentShiftUptime += Number(rowVal) || 0;
                    });
                }
                currentShiftDowntime = Math.max(0, elapsedShiftMin - currentShiftUptime);

                // OEE% = Total Counter / (Speed × Uptime × 2 jalur) × 100
                const LANE_MULTIPLIER = 2;
                const maxUptimeCapacity = currentShiftUptime * SPEED_DEFAULT * LANE_MULTIPLIER;
                oeeShiftPct = maxUptimeCapacity > 0
                    ? ((productVal / maxUptimeCapacity) * 100).toFixed(1)
                    : '0.0';
            }

            const currentShiftNameEl = document.getElementById('current-shift-name');
            const valOeeShiftPctEl = document.getElementById('val-oee-shift-pct');
            const shiftOeeBarEl = document.getElementById('shift-oee-bar');
            const valTargetPctEl = document.getElementById('val-target-pct');

            if (currentShiftNameEl) currentShiftNameEl.textContent = shiftName;
            if (valOeeShiftPctEl) valOeeShiftPctEl.innerHTML = `${oeeShiftPct}<span class="kpi-unit">%</span>`;
            if (shiftOeeBarEl) shiftOeeBarEl.style.width = `${Math.min(100, parseFloat(oeeShiftPct))}%`;
            if (valTargetPctEl) valTargetPctEl.textContent = `${oeeShiftPct}% Target Shift`;

            const valUptimeShiftEl = document.getElementById('val-uptime-shift');
            const valDowntimeShiftEl = document.getElementById('val-downtime-shift');
            const valSpeedEl = document.getElementById('val-speed');

            if (valUptimeShiftEl) valUptimeShiftEl.innerHTML = `${currentShiftUptime}<span class="kpi-unit">min</span>`;
            if (valDowntimeShiftEl) valDowntimeShiftEl.innerHTML = `${currentShiftDowntime}<span class="kpi-unit">min</span>`;

            // Calculate actual average machine speed (pcs / uptime minute)
            const speedPpm = (productVal > 0 && currentShiftUptime > 0) ? (productVal / currentShiftUptime).toFixed(1) : '0.0';
            if (valSpeedEl) valSpeedEl.textContent = `Kecepatan: ~${speedPpm} pcs / min`;

            // Update formula display with actual values
            const formulaEl = document.getElementById('oee-formula-text');
            if (formulaEl) {
                const capacity = currentShiftUptime * SPEED_DEFAULT * 2;
                formulaEl.innerHTML = `OEE = <b style="color:#10b981;">${Number(productVal).toLocaleString('id-ID')}</b> / (<b>42</b> × <b style="color:#06b6d4;">${currentShiftUptime}</b> × <b>2</b>) = <b style="color:#8b5cf6;">${oeeShiftPct}%</b>`;
            }
        }

        // Fetch Live Status from Node.js Daemon
        async function fetchStatus() {
            try {
                const res = await fetch(`${API_BASE}/api/${CURRENT_MACHINE}/status`);
                if (!res.ok) throw new Error('Status endpoint offline');
                const data = await res.json();

                if (mqttStatusEl) {
                    mqttStatusEl.textContent = 'MQTT ONLINE';
                    mqttStatusEl.parentElement.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                    mqttStatusEl.parentElement.style.color = '#10b981';
                }

                const oeeVal = (data.oee !== undefined ? data.oee : (data.oee_d1 !== undefined ? data.oee_d1 : 0));
                const productVal = (data.product !== undefined ? data.product : (data.ct_productd1 !== undefined ? data.ct_productd1 : 0));
                window.currentOeeVal = oeeVal;

                valOeeEl.innerHTML = `${oeeVal}<span class="kpi-unit">min</span>`;
                valProductEl.innerHTML = `${productVal.toLocaleString('id-ID')}<span class="kpi-unit">pcs</span>`;

                const maxHourMinutes = 60;
                const oeePct = Math.min(Math.round((oeeVal / maxHourMinutes) * 100), 100);
                if (oeePctText) oeePctText.textContent = `${oeePct}%`;
                
                const now = new Date();
                const elapsedMin = now.getMinutes() || 1;
                const calculatedDowntime = Math.max(0, elapsedMin - oeeVal);

                valStopEl.innerHTML = `${calculatedDowntime}<span class="kpi-unit">min</span>`;

                if (calculatedDowntime === 0) {
                    valStopEl.style.color = '#10b981';
                    downtimeStatusText.innerHTML = `<i class="ri-check-double-line"></i> Tidak ada Downtime`;
                    downtimeStatusText.className = 'fs-12 text-success mt-1';
                } else {
                    valStopEl.style.color = '#ef4444';
                    downtimeStatusText.innerHTML = `<i class="ri-alert-line"></i> Downtime ${calculatedDowntime} menit`;
                    downtimeStatusText.className = 'fs-12 text-danger mt-1';
                }

                // Calculate OEE Shift Performance % and actual speed
                updateShiftPerformance(productVal, data);

            } catch (err) {
                if (mqttStatusEl) {
                    mqttStatusEl.textContent = 'DAEMON CONNECTING...';
                    mqttStatusEl.parentElement.style.borderColor = 'rgba(245, 158, 11, 0.3)';
                    mqttStatusEl.parentElement.style.color = '#f59e0b';
                }
            }
        }

        setInterval(fetchStatus, 15000);
        fetchStatus();

        // Default 8-Hour Chart Data Fallback
        const defaultChartLabels = ["06.00", "07.00", "08.00", "09.00", "10.00", "11.00", "12.00", "13.00"];
        const defaultOeeValues = [54, 58, 60, 52, 59, 57, 60, 56];
        const defaultProductValues = [4536, 4872, 5040, 4368, 4956, 4788, 5040, 4704];

        // Initial Chart Render (Instant Display)
        renderChart(defaultChartLabels, defaultOeeValues, defaultProductValues);

        // Fetch Chart Data from /api/history
        async function fetchChartData() {
            try {
                const res = await fetch(`${API_BASE}/api/${CURRENT_MACHINE}/history`);
                if (!res.ok) throw new Error('Offline API');
                const data = await res.json();
                window.lastHistoryRows = data.history || [];

                if (data.chart && data.chart.labels && data.chart.labels.length > 0) {
                    renderChart(data.chart.labels, data.chart.oeeValues, data.chart.productValues);
                    return;
                }
            } catch (err) {
                console.log('Using default fallback chart data:', err.message);
            }
            renderChart(defaultChartLabels, defaultOeeValues, defaultProductValues);
        }

        // Render rows into Shift OEE History Table
        function renderShiftRows(shifts) {
            if (!dbTableBody) return;
            if (shifts && shifts.length > 0) {
                dbTableBody.innerHTML = '';
                shifts.forEach(shift => {
                    const tr = document.createElement('tr');
                    tr.className = 'shift-row-item';
                    tr.title = 'Klik untuk melihat detail log jam shift ini';
                    const oee = shift.oee_pct;

                    let oeeColor, oeeBg;
                    if (oee >= 85) {
                        oeeColor = '#10b981'; oeeBg = 'rgba(16, 185, 129, 0.15)';
                    } else if (oee >= 60) {
                        oeeColor = '#f59e0b'; oeeBg = 'rgba(245, 158, 11, 0.15)';
                    } else {
                        oeeColor = '#ef4444'; oeeBg = 'rgba(239, 68, 68, 0.15)';
                    }

                    const dateObj = new Date(shift.shift_date + 'T00:00:00');
                    const dateStr = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });

                    tr.innerHTML = `
                        <td>${dateStr}</td>
                        <td><span class="oee-jam-badge">${shift.shift_label}</span></td>
                        <td class="fw-bold text-info">${shift.total_uptime_min} min</td>
                        <td class="text-success">${Number(shift.total_product).toLocaleString('id-ID')}</td>
                        <td>
                            <span style="background: ${oeeBg}; color: ${oeeColor}; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 0.78rem;">
                                ${oee}%
                            </span>
                        </td>
                    `;

                    tr.addEventListener('click', function() {
                        openShiftDetailModal(shift);
                    });

                    dbTableBody.appendChild(tr);
                });
            } else {
                dbTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-3" style="color: var(--oee-text-muted);">Tidak ada data shift pada rentang tanggal ini</td></tr>`;
            }
        }

        // Generate 7 days fallback shift data
        function generateFallbackShifts(startVal, endVal) {
            const shifts = [];
            const today = new Date();
            for (let i = 0; i < 7; i++) {
                const d = new Date();
                d.setDate(today.getDate() - i);
                const ymd = formatDateToYMD(d);

                if (startVal && ymd < startVal) continue;
                if (endVal && ymd > endVal) continue;

                const uptime1 = Math.max(200, 420 - (i * 8));
                const prod1 = Math.round(uptime1 * 41);
                const oee1 = Math.min(100, Math.round((prod1 / (42 * uptime1 * 2)) * 100));

                shifts.push({
                    id: i * 2 + 1,
                    shift_date: ymd,
                    shift_label: 'Shift 1 (06.00-14.00)',
                    total_uptime_min: uptime1,
                    total_product: prod1,
                    oee_pct: oee1,
                    hourly_rows: [
                        { id: 1, jam: '06.00', oee: 55, ct_product: 4620, is_stop_shift: false, status: 'NORMAL' },
                        { id: 2, jam: '07.00', oee: 58, ct_product: 4872, is_stop_shift: false, status: 'NORMAL' },
                        { id: 3, jam: '08.00', oee: 60, ct_product: 5040, is_stop_shift: false, status: 'NORMAL' },
                        { id: 4, jam: '09.00', oee: 52, ct_product: 4368, is_stop_shift: false, status: 'NORMAL' }
                    ]
                });

                const uptime2 = Math.max(180, 400 - (i * 10));
                const prod2 = Math.round(uptime2 * 39.5);
                const oee2 = Math.min(100, Math.round((prod2 / (42 * uptime2 * 2)) * 100));

                shifts.push({
                    id: i * 2 + 2,
                    shift_date: ymd,
                    shift_label: 'Shift 2 (14.00-22.00)',
                    total_uptime_min: uptime2,
                    total_product: prod2,
                    oee_pct: oee2,
                    hourly_rows: [
                        { id: 5, jam: '14.00', oee: 57, ct_product: 4788, is_stop_shift: false, status: 'NORMAL' },
                        { id: 6, jam: '15.00', oee: 59, ct_product: 4956, is_stop_shift: false, status: 'NORMAL' }
                    ]
                });
            }
            return shifts;
        }

        // Fetch Shift OEE History from /api/shifts with Date Filtering
        async function fetchShiftHistory() {
            const startVal = startDateInput ? startDateInput.value : '';
            const endVal = endDateInput ? endDateInput.value : '';

            try {
                let url = `${API_BASE}/api/${CURRENT_MACHINE}/shifts`;
                if (startVal && endVal) {
                    url += `?start_date=${startVal}&end_date=${endVal}`;
                }
                const res = await fetch(url);
                if (!res.ok) throw new Error('Shift API Error');
                const data = await res.json();

                let rawShifts = data.shifts || [];
                if (startVal && endVal && rawShifts.length > 0) {
                    rawShifts = rawShifts.filter(s => {
                        const sDate = s.shift_date;
                        return (!startVal || sDate >= startVal) && (!endVal || sDate <= endVal);
                    });
                }

                if (rawShifts.length > 0) {
                    renderShiftRows(rawShifts);
                    return;
                }
            } catch (err) {
                console.log('Using default 7-day fallback shift data:', err.message);
            }

            renderShiftRows(generateFallbackShifts(startVal, endVal));
        }

        function formatJamRange(jamStr) {
            if (!jamStr) return '-';
            if (jamStr.includes('-')) return jamStr;
            const parts = jamStr.split('.');
            const hourInt = parseInt(parts[0], 10);
            if (isNaN(hourInt)) return jamStr;
            const prevHour = (hourInt + 23) % 24;
            const prevStr = String(prevHour).padStart(2, '0') + '.00';
            const currStr = String(hourInt).padStart(2, '0') + '.00';
            return `${prevStr}-${currStr}`;
        }

        // Open Shift Detail Modal
        function openShiftDetailModal(shift) {
            const subtitleEl = document.getElementById('modal-shift-subtitle');
            const uptimeEl = document.getElementById('modal-shift-uptime');
            const productEl = document.getElementById('modal-shift-product');
            const oeeEl = document.getElementById('modal-shift-oee');
            const modalTableBody = document.getElementById('modal-table-body');

            const dateObj = new Date(shift.shift_date + 'T00:00:00');
            const dateStr = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

            if (subtitleEl) subtitleEl.textContent = `${dateStr} • ${shift.shift_label}`;
            if (uptimeEl) uptimeEl.textContent = `${shift.total_uptime_min} min`;
            if (productEl) productEl.textContent = `${Number(shift.total_product).toLocaleString('id-ID')} pcs`;
            if (oeeEl) oeeEl.textContent = `${shift.oee_pct}%`;

            if (modalTableBody) {
                modalTableBody.innerHTML = '';
                const hourlyRows = shift.hourly_rows || [];
                if (hourlyRows.length > 0) {
                    // Sort rows chronologically for calculation
                    const sortedRows = [...hourlyRows].sort((a, b) => new Date(a.machine_ts || 0) - new Date(b.machine_ts || 0) || a.id - b.id);

                    let prevCumulative = 0;
                    sortedRows.forEach(row => {
                        const rawProd = Number(row.ct_product || 0);
                        let netProd = 0;

                        if (row.net_ct_product !== undefined) {
                            netProd = row.net_ct_product;
                        } else if (rawProd < prevCumulative) {
                            netProd = rawProd;
                        } else {
                            netProd = rawProd - prevCumulative;
                        }
                        prevCumulative = rawProd;

                        const displayJam = formatJamRange(row.jam);
                        const statusStr = row.status || 'NORMAL';
                        const upperStatus = statusStr.toUpperCase();

                        let statusBadgeHtml = '';
                        if (upperStatus.includes('NORMAL')) {
                            statusBadgeHtml = `<span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 0.78rem;">NORMAL</span>`;
                        } else if (upperStatus.includes('OFF') || upperStatus.includes('IDLE')) {
                            statusBadgeHtml = `<span style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 0.78rem;">${statusStr}</span>`;
                        } else if (upperStatus.includes('TIDAK ADA DATA') || upperStatus.includes('OFFLINE')) {
                            statusBadgeHtml = `<span style="background: rgba(107, 114, 128, 0.2); color: #9ca3af; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 0.78rem;">${statusStr}</span>`;
                        } else if (upperStatus.includes('STOP SHIFT')) {
                            statusBadgeHtml = `<span style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 0.78rem;">${statusStr}</span>`;
                        } else {
                            statusBadgeHtml = `<span style="background: rgba(239, 68, 68, 0.15); color: #ef4444; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 0.78rem;">${statusStr}</span>`;
                        }

                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.id}</td>
                            <td><span class="oee-jam-badge">${displayJam}</span></td>
                            <td class="fw-bold text-info">${row.oee} min</td>
                            <td class="text-success">${Number(netProd).toLocaleString('id-ID')} pcs</td>
                            <td>${statusBadgeHtml}</td>
                        `;
                        modalTableBody.appendChild(tr);
                    });
                } else {
                    modalTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-3" style="color: var(--oee-text-muted);">Tidak ada detail log jam untuk shift ini</td></tr>`;
                }
            }

            const modalEl = document.getElementById('modalShiftDetail');
            if (modalEl) {
                const bsModal = new bootstrap.Modal(modalEl);
                bsModal.show();
            }
        }

        // Combined fetch
        async function fetchHistoryAndRender() {
            await Promise.all([fetchChartData(), fetchShiftHistory()]);
        }

        function renderChart(labels, oeeData, productData) {
            const ctx = document.getElementById('oeeChart');
            if (!ctx) return;

            const isDark = document.documentElement.getAttribute('data-layout-mode') === 'dark';
            const textColor = isDark ? '#9ca3af' : '#6c757d';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

            if (oeeChartInstance) {
                oeeChartInstance.destroy();
            }

            oeeChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'OEE Uptime (Min)',
                            data: oeeData,
                            backgroundColor: 'rgba(6, 182, 212, 0.7)',
                            borderColor: '#06b6d4',
                            borderWidth: 1.5,
                            borderRadius: 4,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Product Output (Pcs)',
                            data: productData,
                            type: 'line',
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.15)',
                            borderWidth: 2.5,
                            pointRadius: 4,
                            pointBackgroundColor: '#10b981',
                            fill: true,
                            tension: 0.3,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: { color: gridColor },
                            ticks: { color: textColor }
                        },
                        y: {
                            type: 'linear',
                            position: 'left',
                            max: 60,
                            title: { display: true, text: 'Uptime (Menit)', color: '#06b6d4' },
                            grid: { color: gridColor },
                            ticks: { color: textColor }
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            title: { display: true, text: 'Product (Pcs)', color: '#10b981' },
                            grid: { drawOnChartArea: false },
                            ticks: { color: textColor }
                        }
                    },
                    plugins: {
                        legend: { labels: { color: textColor } }
                    }
                }
            });
        }

        fetchHistoryAndRender();
        if (btnRefreshDb) btnRefreshDb.addEventListener('click', fetchHistoryAndRender);

        // Listen to Theme Toggle (Dark/Light mode switch)
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'data-layout-mode') {
                    fetchHistoryAndRender();
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });

        // Manual Trigger Reset Button
        if (btnManualReset) {
            btnManualReset.addEventListener('click', async function() {
                if (!confirm(`Apakah Anda yakin ingin mengirim sinyal RESET PULSE (RST_${CURRENT_MACHINE}) ke mesin?`)) return;
                
                try {
                    btnManualReset.disabled = true;
                    btnManualReset.innerHTML = `<i class="ri-loader-4-line spin"></i> SENDING PULSE...`;

                    const res = await fetch(`${API_BASE}/api/reset`, { method: 'POST' });
                    const result = await res.json();

                    if (result.success) {
                        alert(`✅ Reset Pulse RST_${CURRENT_MACHINE} berhasil terkirim ke mesin!`);
                    } else {
                        alert('❌ Gagal mengirim reset pulse: ' + (result.reason || result.message));
                    }
                } catch (e) {
                    alert('❌ Gagal terhubung ke daemon server OEE');
                } finally {
                    btnManualReset.disabled = false;
                    btnManualReset.innerHTML = `<i class="ri-restart-line"></i> TRIGGER RESET`;
                    fetchStatus();
                }
            });
        }
    });
</script>
@endsection