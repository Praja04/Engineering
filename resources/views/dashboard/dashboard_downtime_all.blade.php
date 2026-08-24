@extends('layouts.app')

@section('title', 'Overview OEE Multi-Mesin Production Line')

@section('styles')
<style>
    /* Dynamic Dual-Theme Variables (Velzon Light & Dark Mode Compatible) */
    :root {
        --oee-card-bg: #ffffff;
        --oee-card-border: #e2e8f0;
        --oee-text-main: #0f172a;
        --oee-text-muted: #475569;
        --oee-clock-bg: #f1f5f9;
        --oee-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        --oee-tag-bg: rgba(124, 58, 237, 0.12);
        --oee-tag-color: #6d28d9;
        --oee-stat-uptime: #0284c7;
        --oee-stat-counter: #059669;
        --oee-stat-downtime: #dc2626;
    }

    [data-layout-mode="dark"] {
        --oee-card-bg: #151e2f;
        --oee-card-border: rgba(255, 255, 255, 0.12);
        --oee-text-main: #f8fafc;
        --oee-text-muted: #94a3b8;
        --oee-clock-bg: #1e293b;
        --oee-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        --oee-tag-bg: rgba(139, 92, 246, 0.3);
        --oee-tag-color: #c084fc;
        --oee-stat-uptime: #38bdf8;
        --oee-stat-counter: #34d399;
        --oee-stat-downtime: #f87171;
    }

    /* Complete TV Fullscreen Display Mode: Hide Sidebar, Topbar, & Footer */
    #page-topbar, 
    .app-menu,
    footer.footer,
    #back-to-top { 
        display: none !important; 
    }

    /* Lock Page to 100vh Viewport (Strictly NO Scrollbar for TV Screen) */
    html, body {
        height: 100vh !important;
        max-height: 100vh !important;
        overflow: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .layout-wrapper {
        height: 100vh !important;
        overflow: hidden !important;
    }

    .main-content {
        margin-left: 0 !important;
        margin-top: 0 !important;
        padding-top: 0 !important;
        height: 100vh !important;
        overflow: hidden !important;
        display: flex;
        flex-direction: column;
    }

    .page-content {
        padding: 0.6rem 1rem !important;
        height: 100vh !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden !important;
    }

    .oee-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--oee-card-border);
        margin-bottom: 0.5rem;
        flex-shrink: 0;
    }

    .oee-title {
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--oee-text-main);
        margin-bottom: 0;
    }

    .server-clock {
        background: var(--oee-clock-bg);
        border: 1px solid var(--oee-card-border);
        padding: 4px 12px;
        border-radius: 20px;
        font-family: 'Monaco', 'Consolas', monospace;
        font-size: 0.85rem;
        font-weight: 800;
        color: #06b6d4;
    }

    .stat-summary-card {
        background: var(--oee-card-bg);
        border: 1px solid var(--oee-card-border);
        border-radius: 10px;
        padding: 10px 14px;
        box-shadow: var(--oee-shadow);
    }

    .stat-icon-wrapper {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    /* Grid Layout: 5 columns x 4 rows tailored for 43" TV */
    .tv-grid-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        grid-template-rows: repeat(4, 1fr);
        gap: 0.55rem;
        flex: 1;
        overflow: hidden;
    }

    /* Machine Card Layout (Prominent, High-Contrast Typography) */
    .machine-card {
        background: var(--oee-card-bg);
        border: 1px solid var(--oee-card-border);
        border-radius: 12px;
        padding: 12px 14px;
        box-shadow: var(--oee-shadow);
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        text-decoration: none !important;
        overflow: hidden;
    }

    .machine-card:hover {
        transform: translateY(-2px);
        border-color: rgba(139, 92, 246, 0.6);
        box-shadow: 0 8px 24px rgba(139, 92, 246, 0.25);
    }

    .machine-tag {
        background: var(--oee-tag-bg);
        color: var(--oee-tag-color);
        border: 1px solid rgba(139, 92, 246, 0.4);
        padding: 2px 10px;
        border-radius: 6px;
        font-weight: 800;
        font-size: 0.85rem;
        letter-spacing: 0.02em;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    .status-dot.running {
        background: #10b981;
        box-shadow: 0 0 10px #10b981;
        animation: pulseGreen 2s infinite;
    }
    .status-dot.stop {
        background: #ef4444;
        box-shadow: 0 0 10px #ef4444;
    }

    @keyframes pulseGreen {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .progress-bar-custom {
        height: 8px;
        border-radius: 8px;
        background: rgba(150, 150, 150, 0.2);
        overflow: hidden;
    }

    .filter-tab-btn {
        background: transparent;
        border: 1px solid var(--oee-card-border);
        color: var(--oee-text-muted);
        padding: 4px 14px;
        border-radius: 16px;
        font-size: 0.78rem;
        font-weight: 700;
        transition: all 0.2s ease;
    }
    .filter-tab-btn.active, .filter-tab-btn:hover {
        background: #8b5cf6;
        color: #ffffff;
        border-color: #8b5cf6;
    }

    /* Clarified high-contrast footer stats styling (ENLARGED FOR 43" TV) */
    .stat-number-uptime {
        color: var(--oee-stat-uptime);
        font-weight: 800;
        font-size: 1.05rem;
        line-height: 1.1;
    }
    .stat-number-counter {
        color: var(--oee-stat-counter);
        font-weight: 800;
        font-size: 1.05rem;
        line-height: 1.1;
    }
    .stat-number-downtime {
        color: var(--oee-stat-downtime);
        font-weight: 800;
        font-size: 1.05rem;
        line-height: 1.1;
    }
    .stat-label-title {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        color: var(--oee-text-muted);
        margin-top: 3px;
    }
</style>
@endsection

@section('content')
<div class="page-content">

    <!-- Header Bar -->
    <div class="oee-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ url('/home') }}" class="btn btn-soft-purple btn-sm d-flex align-items-center gap-1 px-3 py-1 fw-bold" style="border-radius: 8px;">
                <i class="ri-arrow-left-line fs-14"></i>
                <span>Kembali</span>
            </a>
            <div>
                <h1 class="oee-title">OVERVIEW OEE MULTI-MESIN <span style="font-weight: 400; opacity: 0.7; font-size: 1.05rem;">| 20 Mesin Line</span></h1>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="server-clock me-2" id="clock-wib">
                --:--:-- WIB
            </div>

            <!-- Theme Toggle Button -->
            <button class="btn btn-soft-secondary btn-icon btn-sm" id="btn-theme-toggle" title="Toggle Light / Dark Mode">
                <i class="bx bx-sun fs-18" id="theme-icon"></i>
            </button>

            <div class="input-group input-group-sm" style="width: 170px;">
                <span class="input-group-text bg-transparent border-end-0 py-1" style="border-color: var(--oee-card-border);"><i class="ri-search-line text-muted fs-12"></i></span>
                <input type="text" id="search-machine" class="form-control border-start-0 py-1 fs-12" placeholder="Cari..." style="background: transparent; border-color: var(--oee-card-border); color: var(--oee-text-main);">
            </div>
        </div>
    </div>

    <!-- KPI Summary Bar (Top 4 Cards) -->
    <div class="row g-2 mb-2 flex-shrink-0">
        <div class="col-3">
            <div class="stat-summary-card d-flex align-items-center justify-content-between">
                <div>
                    <span class="fs-11 text-muted fw-bold text-uppercase">Mesin Operasional</span>
                    <h4 class="fw-extrabold mb-0 mt-0" style="color: #10b981;" id="summary-active-count">2 / 20</h4>
                    <span class="fs-10 text-muted fw-bold" id="summary-active-subtext">2 Active • 18 Coming Soon</span>
                </div>
                <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                    <i class="ri-cpu-line"></i>
                </div>
            </div>
        </div>

        <div class="col-3">
            <div class="stat-summary-card d-flex align-items-center justify-content-between">
                <div>
                    <span class="fs-11 text-muted fw-bold text-uppercase">Rata-Rata OEE Shift</span>
                    <h4 class="fw-extrabold mb-0 mt-0" style="color: #8b5cf6;" id="summary-avg-oee">0.0%</h4>
                    <span class="fs-10 text-muted fw-bold" id="summary-avg-subtext">Rata-rata OEE Mesin Aktif</span>
                </div>
                <div class="stat-icon-wrapper" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
                    <i class="ri-pie-chart-2-line"></i>
                </div>
            </div>
        </div>

        <div class="col-3">
            <div class="stat-summary-card d-flex align-items-center justify-content-between">
                <div>
                    <span class="fs-11 text-muted fw-bold text-uppercase">Total Output Shift</span>
                    <h4 class="fw-extrabold mb-0 mt-0" style="color: #06b6d4;" id="summary-total-output">0 <span class="fs-11 fw-normal">pcs</span></h4>
                    <span class="fs-10 text-muted fw-bold">Akumulasi 20 Mesin</span>
                </div>
                <div class="stat-icon-wrapper" style="background: rgba(6, 182, 212, 0.15); color: #06b6d4;">
                    <i class="ri-shopping-bag-3-line"></i>
                </div>
            </div>
        </div>

        <div class="col-3">
            <div class="stat-summary-card d-flex align-items-center justify-content-between">
                <div>
                    <span class="fs-11 text-muted fw-bold text-uppercase">Total Downtime Shift</span>
                    <h4 class="fw-extrabold mb-0 mt-0" style="color: #ef4444;" id="summary-total-downtime">0 <span class="fs-11 fw-normal">min</span></h4>
                    <span class="fs-10 text-muted fw-bold" id="summary-downtime-subtext"><i class="ri-check-double-line text-success"></i> Realtime Telemetry</span>
                </div>
                <div class="stat-icon-wrapper" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                    <i class="ri-timer-flash-line"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Category Tabs -->
    <div class="d-flex align-items-center justify-content-between mb-2 flex-shrink-0">
        <div class="d-flex align-items-center gap-1">
            <button class="filter-tab-btn active" data-filter="all">Semua (20)</button>
            <button class="filter-tab-btn" data-filter="d1-d10">Mesin D1 - D10</button>
            <button class="filter-tab-btn" data-filter="d11-d20">Mesin D11 - D20</button>
        </div>
        <div class="fs-11 text-muted fw-bold">
            <i class="ri-tv-2-line me-1"></i> Mode TV 43" Auto Refresh
        </div>
    </div>

    <!-- 20 Machine Cards TV Grid Container (No Scroll) -->
    <div class="tv-grid-container" id="machine-grid">
        @php
            $machines = [];
            for ($i = 1; $i <= 20; $i++) {
                $code = 'D' . $i;
                $hasData = in_array($code, ['D1', 'D10']); // D1 & D10 active cards, others coming soon
                $group = ($i <= 10) ? 'd1-d10' : 'd11-d20';
                $machines[] = [
                    'code' => $code,
                    'name' => 'Mesin ' . $code,
                    'group' => $group,
                    'has_data' => $hasData,
                    'default_oee' => $hasData ? 0 : 0,
                    'default_status' => $hasData ? 'running' : 'coming_soon',
                    'uptime' => 0,
                    'counter' => 0,
                    'downtime' => 0
                ];
            }
        @endphp

        @foreach($machines as $m)
            @php
                $hasData = $m['has_data'];
                $oee = $m['default_oee'];
                $isRun = ($m['default_status'] === 'running');
                $oeeColor = ($oee >= 85) ? '#10b981' : (($oee >= 60) ? '#f59e0b' : '#ef4444');
            @endphp
            <div class="machine-card-col" data-group="{{ $m['group'] }}" data-name="{{ strtolower($m['name'] . ' ' . $m['code']) }}">
                <a href="{{ route('dashboard.downtimemesin.detail', ['machine' => $m['code']]) }}" class="machine-card">
                    <div>
                        <!-- Header status & tag -->
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="machine-tag">{{ $m['code'] }}</span>
                            @if($hasData)
                                <div class="d-flex align-items-center gap-1">
                                    <span class="status-dot {{ $isRun ? 'running' : 'stop' }}" id="status-dot-{{ $m['code'] }}"></span>
                                    <span class="fs-12 fw-extrabold {{ $isRun ? 'text-success' : 'text-danger' }}" id="status-label-{{ $m['code'] }}">
                                        {{ $isRun ? 'RUNNING' : 'STOP' }}
                                    </span>
                                </div>
                            @else
                                <span class="badge bg-soft-warning text-warning fw-extrabold px-2 py-1 fs-10" style="letter-spacing: 0.05em; border: 1px solid rgba(245, 158, 11, 0.3);">
                                    <i class="ri-time-line me-1"></i>COMING SOON
                                </span>
                            @endif
                        </div>

                        <!-- Machine title -->
                        <h6 class="fw-extrabold mb-1" style="color: var(--oee-text-main); font-size: 1.05rem;">{{ $m['name'] }}</h6>

                        @if($hasData)
                            <!-- OEE gauge performance -->
                            <div class="mb-1">
                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <span class="fs-11 text-muted fw-bold">OEE SHIFT</span>
                                    <span class="fs-16 fw-extrabold" style="color: {{ $oeeColor }};" id="oee-val-{{ $m['code'] }}">{{ $oee }}%</span>
                                </div>
                                <div class="progress-bar-custom">
                                    <div id="oee-bar-{{ $m['code'] }}" style="width: {{ $oee }}%; height: 100%; background: {{ $oeeColor }}; transition: width 0.5s ease;"></div>
                                </div>
                            </div>
                        @else
                            <!-- Coming Soon Full Card Placeholder Box -->
                            <div class="d-flex flex-column align-items-center justify-content-center my-2 py-3" style="background: rgba(245, 158, 11, 0.05); border-radius: 10px; border: 1px dashed rgba(245, 158, 11, 0.3); min-height: 80px;">
                                <i class="ri-time-line mb-1" style="font-size: 1.3rem; color: #f59e0b;"></i>
                                <span class="fs-12 fw-extrabold" style="color: #f59e0b; letter-spacing: 0.08em;">COMING SOON</span>
                            </div>
                        @endif
                    </div>

                    @if($hasData)
                        <!-- Footer stats (Larger font, high contrast) -->
                        <div class="pt-1 border-top border-secondary border-opacity-10">
                            <div class="row text-center g-0">
                                <div class="col-4">
                                    <div class="stat-number-uptime" id="uptime-val-{{ $m['code'] }}">{{ $m['uptime'] }}m</div>
                                    <div class="stat-label-title">UPTIME</div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-number-counter" id="counter-val-{{ $m['code'] }}">{{ number_format($m['counter'], 0, ',', '.') }}</div>
                                    <div class="stat-label-title">COUNTER</div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-number-downtime" id="downtime-val-{{ $m['code'] }}">{{ $m['downtime'] }}m</div>
                                    <div class="stat-label-title">DOWNTIME</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </a>
            </div>
        @endforeach
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const API_BASE = 'http://10.11.11.200:3000';
        const clockEl = document.getElementById('clock-wib');
        const themeBtn = document.getElementById('btn-theme-toggle');
        const themeIcon = document.getElementById('theme-icon');

        // Theme Toggle Logic
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

        // Clock WIB
        function updateWibClock() {
            const now = new Date();
            if (clockEl) {
                clockEl.textContent = now.toLocaleTimeString('id-ID', {
                    timeZone: 'Asia/Jakarta',
                    hour12: false,
                    hour: '2-digit', minute: '2-digit', second: '2-digit'
                }) + ' WIB';
            }
        }
        setInterval(updateWibClock, 1000);
        updateWibClock();

        // Search & Filter Logic
        const filterBtns = document.querySelectorAll('.filter-tab-btn');
        const searchInput = document.getElementById('search-machine');
        const machineCols = document.querySelectorAll('.machine-card-col');

        let activeFilter = 'all';

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                activeFilter = this.getAttribute('data-filter');
                applyFilterAndSearch();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', applyFilterAndSearch);
        }

        function applyFilterAndSearch() {
            const query = (searchInput ? searchInput.value : '').toLowerCase().trim();

            machineCols.forEach(col => {
                const group = col.getAttribute('data-group');
                const name = col.getAttribute('data-name');

                const matchGroup = (activeFilter === 'all' || group === activeFilter);
                const matchSearch = (!query || name.includes(query));

                if (matchGroup && matchSearch) {
                    col.style.display = 'block';
                } else {
                    col.style.display = 'none';
                }
            });
        }

        function updateMachineCardUI(code, m) {
            const oeeValEl = document.getElementById(`oee-val-${code}`);
            const oeeBarEl = document.getElementById(`oee-bar-${code}`);
            const uptimeValEl = document.getElementById(`uptime-val-${code}`);
            const counterValEl = document.getElementById(`counter-val-${code}`);
            const downtimeValEl = document.getElementById(`downtime-val-${code}`);

            const oeeShift = m.oee_shift_pct !== undefined ? m.oee_shift_pct : (m.oee !== undefined ? m.oee : 0);
            const uptime = m.shift_uptime_min !== undefined ? m.shift_uptime_min : 0;
            const product = m.product !== undefined ? m.product : 0;
            const downtime = m.shift_downtime_min !== undefined ? m.shift_downtime_min : 0;

            if (oeeValEl) oeeValEl.textContent = `${oeeShift}%`;
            if (oeeBarEl) oeeBarEl.style.width = `${Math.min(100, oeeShift)}%`;
            if (uptimeValEl) uptimeValEl.textContent = `${uptime}m`;
            if (counterValEl) counterValEl.textContent = Number(product).toLocaleString('id-ID');
            if (downtimeValEl) downtimeValEl.textContent = `${downtime}m`;
        }

        // Calculate and update top 4 summary KPI cards dynamically
        function updateSummaryKPIs(machines) {
            let activeCount = 0;
            let totalOutput = 0;
            let totalDowntime = 0;
            let sumOee = 0;
            let stoppedCount = 0;

            Object.keys(machines).forEach(code => {
                const m = machines[code];
                if (!m) return;
                const oeeShift = m.oee_shift_pct !== undefined ? Number(m.oee_shift_pct) : (m.oee !== undefined ? Number(m.oee) : 0);
                const uptime = m.shift_uptime_min !== undefined ? Number(m.shift_uptime_min) : 0;
                const product = m.product !== undefined ? Number(m.product) : 0;
                const downtime = m.shift_downtime_min !== undefined ? Number(m.shift_downtime_min) : 0;

                const isActive = m.has_data || (product > 0) || (uptime > 0) || (oeeShift > 0);
                if (isActive) {
                    activeCount++;
                    totalOutput += product;
                    totalDowntime += downtime;
                    sumOee += oeeShift;

                    if (downtime > 0) stoppedCount++;
                }
            });

            const summaryActiveEl = document.getElementById('summary-active-count');
            const summaryActiveSub = document.getElementById('summary-active-subtext');
            const summaryAvgOeeEl = document.getElementById('summary-avg-oee');
            const summaryOutputEl = document.getElementById('summary-total-output');
            const summaryDowntimeEl = document.getElementById('summary-total-downtime');
            const summaryDowntimeSub = document.getElementById('summary-downtime-subtext');

            if (summaryActiveEl) summaryActiveEl.textContent = `${activeCount} / 20`;
            if (summaryActiveSub) summaryActiveSub.textContent = `${activeCount} Active • ${20 - activeCount} Coming Soon`;

            const avgOee = activeCount > 0 ? (sumOee / activeCount).toFixed(1) : '0.0';
            if (summaryAvgOeeEl) summaryAvgOeeEl.textContent = `${avgOee}%`;

            if (summaryOutputEl) summaryOutputEl.innerHTML = `${totalOutput.toLocaleString('id-ID')} <span class="fs-11 fw-normal">pcs</span>`;
            if (summaryDowntimeEl) summaryDowntimeEl.innerHTML = `${totalDowntime} <span class="fs-11 fw-normal">min</span>`;
            if (summaryDowntimeSub) {
                if (stoppedCount > 0) {
                    summaryDowntimeSub.innerHTML = `<i class="ri-error-warning-line text-danger"></i> ${stoppedCount} Mesin Stop`;
                    summaryDowntimeSub.className = 'fs-10 text-danger fw-bold';
                } else {
                    summaryDowntimeSub.innerHTML = `<i class="ri-check-double-line text-success"></i> Semua Mesin Normal`;
                    summaryDowntimeSub.className = 'fs-10 text-success fw-bold';
                }
            }
        }

        // Modular live status fetch for all machines from /api/all-status (with D1 & D10 fallback)
        async function fetchAllMachinesStatus() {
            try {
                const res = await fetch(`${API_BASE}/api/all-status`);
                if (res.ok) {
                    const data = await res.json();
                    if (data.machines) {
                        Object.keys(data.machines).forEach(code => {
                            updateMachineCardUI(code, data.machines[code]);
                        });
                        updateSummaryKPIs(data.machines);
                        return;
                    }
                }
            } catch (err) {}

            // Fallback: fetch individual machine status endpoints for D1 and D10 if /api/all-status returns 404
            const fallbackStore = {};
            const activeList = ['D1', 'D10'];
            for (const code of activeList) {
                try {
                    const res = await fetch(`${API_BASE}/api/${code}/status`);
                    if (res.ok) {
                        const data = await res.json();
                        data.has_data = true;
                        fallbackStore[code] = data;
                        updateMachineCardUI(code, data);
                    }
                } catch (e) {}
            }
            updateSummaryKPIs(fallbackStore);
        }

        setInterval(fetchAllMachinesStatus, 15000);
        fetchAllMachinesStatus();
    });
</script>
@endsection
