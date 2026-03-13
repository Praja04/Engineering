@extends('layouts.app')

@section('title', 'Dashboard EJO')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

<style>
    :root {
        --primary: #2563eb;
        --primary-light: #eff6ff;
        --primary-dark: #1d4ed8;
        --success: #059669;
        --success-light: #ecfdf5;
        --warning: #d97706;
        --warning-light: #fffbeb;
        --danger: #dc2626;
        --danger-light: #fef2f2;
        --surface: #ffffff;
        --surface-2: #f8fafc;
        --border: #e2e8f0;
        --text: #0f172a;
        --text-muted: #64748b;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04);
        --shadow-md: 0 4px 16px rgba(0, 0, 0, .08), 0 2px 6px rgba(0, 0, 0, .04);
        --radius: 12px;
        --radius-sm: 8px;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: #f1f5f9;
    }

    /* ── Page Header ── */
    .db-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        padding: 24px 28px;
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
    }

    .db-header h1 {
        font-size: 20px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 4px;
        letter-spacing: -.3px;
    }

    .db-header p {
        font-size: 13px;
        color: var(--text-muted);
        margin: 0;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-sm {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all .15s;
        border: none;
    }

    .btn-outline {
        background: var(--surface);
        color: var(--text-muted);
        border: 1.5px solid var(--border);
    }

    .btn-outline:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-light);
    }

    .btn-primary {
        background: var(--primary);
        color: #fff;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        box-shadow: 0 4px 12px rgba(37, 99, 235, .35);
        color: #fff;
    }

    #last-updated {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* ── Metric Cards ── */
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    @media(max-width:900px) {
        .metric-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .metric-card {
        background: var(--surface);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        padding: 20px 22px;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        transition: box-shadow .15s, transform .15s;
    }

    .metric-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .metric-card .accent {
        position: absolute;
        left: 0;
        top: 0;
        width: 4px;
        height: 100%;
    }

    .metric-card .mc-icon {
        width: 38px;
        height: 38px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
    }

    .metric-card .mc-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 6px;
    }

    .metric-card .mc-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--text);
        line-height: 1;
        margin-bottom: 5px;
    }

    .metric-card .mc-sub {
        font-size: 12px;
        color: var(--text-muted);
    }

    .acc-amber {
        background: #EF9F27;
    }

    .acc-red {
        background: #E24B4A;
    }

    .acc-blue {
        background: #378ADD;
    }

    .acc-teal {
        background: #1D9E75;
    }

    .icon-amber {
        background: #FAEEDA;
        color: #BA7517;
    }

    .icon-red {
        background: #FCEBEB;
        color: #A32D2D;
    }

    .icon-blue {
        background: #E6F1FB;
        color: #185FA5;
    }

    .icon-teal {
        background: #E1F5EE;
        color: #0F6E56;
    }

    /* ── Card base ── */
    .card {
        background: var(--surface);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
    }

    .card-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
    }

    .card-body {
        padding: 18px 20px;
    }

    /* ── Grid layouts ── */
    .row-2 {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
        gap: 16px;
        margin-bottom: 16px;
    }

    .row-3 {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }

    @media(max-width:900px) {

        .row-2,
        .row-3 {
            grid-template-columns: 1fr;
        }
    }

    /* ── Classification bar ── */
    .class-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .class-row:last-child {
        border-bottom: none;
    }

    .class-label {
        font-size: 13px;
        color: var(--text);
        min-width: 0;
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .class-type {
        font-size: 11px;
        color: var(--text-muted);
        margin-left: 4px;
    }

    .bar-wrap {
        width: 100px;
        height: 6px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .bar-fill {
        height: 100%;
        border-radius: 99px;
        transition: width .8s cubic-bezier(.22, 1, .36, 1);
    }

    .class-num {
        font-size: 12px;
        font-weight: 700;
        color: var(--text);
        min-width: 24px;
        text-align: right;
    }

    /* ── Active ticket list ── */
    .ticket-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 0;
        border-bottom: 1px solid #f1f5f9;
        transition: background .1s;
        cursor: pointer;
        text-decoration: none;
    }

    .ticket-row:last-child {
        border-bottom: none;
    }

    .ticket-row:hover {
        background: var(--surface-2);
        margin: 0 -20px;
        padding: 9px 20px;
        border-radius: var(--radius-sm);
    }

    .ticket-id {
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        font-weight: 600;
        color: var(--primary);
        background: var(--primary-light);
        padding: 2px 7px;
        border-radius: 5px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .ticket-subject {
        font-size: 12px;
        color: var(--text-muted);
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .overdue-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--danger);
        flex-shrink: 0;
    }

    .mini-bar-wrap {
        width: 52px;
        height: 4px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .mini-bar-fill {
        height: 100%;
        border-radius: 99px;
    }

    .pct-label {
        font-size: 11px;
        font-weight: 700;
        min-width: 30px;
        text-align: right;
    }

    /* ── Dept list ── */
    .dept-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
    }

    .dept-row:last-child {
        border-bottom: none;
    }

    .dept-name {
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dept-count {
        font-weight: 700;
        font-size: 12px;
        color: var(--text);
        background: var(--surface-2);
        border: 1px solid var(--border);
        padding: 2px 8px;
        border-radius: 99px;
    }

    .badge-overdue {
        font-size: 10px;
        font-weight: 600;
        color: #A32D2D;
        background: #FCEBEB;
        padding: 1px 6px;
        border-radius: 4px;
    }

    /* ── Chart container ── */
    .chart-wrap {
        position: relative;
        width: 100%;
    }

    /* ── Skeleton ── */
    .skel {
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% 100%;
        animation: shimmer 1.4s infinite;
        border-radius: 6px;
    }

    @keyframes shimmer {
        from {
            background-position: 200% 0;
        }

        to {
            background-position: -200% 0;
        }
    }

    /* ── Legend ── */
    .legend {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
        font-size: 12px;
        color: var(--text-muted);
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 2px;
        display: inline-block;
        margin-right: 4px;
    }

    /* ── Empty state ── */
    .empty {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-muted);
        font-size: 13px;
    }

    /* Completion bar */
    .completion-row {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
    }

    .comp-bar-wrap {
        flex: 1;
        height: 8px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
    }

    .comp-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #2563eb, #059669);
        border-radius: 99px;
        transition: width 1s cubic-bezier(.22, 1, .36, 1);
    }
</style>

@section('content')
<div class="page-content">
    <div class="container-fluid" style="padding: 24px;">

        <!-- Header -->
        <div class="db-header">
            <div>
                <h1>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline;vertical-align:-.15em;margin-right:8px;color:var(--primary)">
                        <rect x="3" y="3" width="7" height="7" />
                        <rect x="14" y="3" width="7" height="7" />
                        <rect x="14" y="14" width="7" height="7" />
                        <rect x="3" y="14" width="7" height="7" />
                    </svg>
                    Dashboard EJO
                </h1>
                <p>Ringkasan status dan progress seluruh tiket EJO</p>
            </div>
            <div class="header-actions">
                <span id="last-updated">Memuat...</span>
                <button class="btn-sm btn-outline" onclick="loadDashboard()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M23 4v6h-6" />
                        <path d="M1 20v-6h6" />
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15" />
                    </svg>
                    Refresh
                </button>
                <a href="{{ url('ejo') }}" class="btn-sm btn-primary">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
                        <rect x="9" y="3" width="6" height="4" rx="1" />
                    </svg>
                    Semua Tiket
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="metric-grid" id="metricGrid">
            @for($i=0;$i<4;$i++) <div class="metric-card">
                <div class="skel" style="height:80px;"></div>
        </div>
        @endfor
    </div>

    <!-- Row 1: Completion bar + Classification bars -->
    <div class="row-2" style="margin-bottom:16px;">
        <div class="card">
            <div class="card-head">
                <span class="card-title">EJO belum selesai per klasifikasi</span>
            </div>
            <div class="card-body" id="classificationList">
                <div class="skel" style="height:160px;"></div>
            </div>
        </div>
        <div class="card">
            <div class="card-head">
                <span class="card-title">Status keseluruhan</span>
            </div>
            <div class="card-body">
                <div style="display:flex;align-items:center;gap:16px;">
                    <div style="position:relative;width:120px;height:120px;flex-shrink:0;">
                        <canvas id="donutChart" width="120" height="120"></canvas>
                        <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;pointer-events:none;">
                            <div style="font-size:24px;font-weight:700;color:var(--text);" id="donutCenter">—</div>
                            <div style="font-size:10px;color:var(--text-muted);">total</div>
                        </div>
                    </div>
                    <div id="statusLegend" style="flex:1;">
                        <div class="skel" style="height:60px;"></div>
                    </div>
                </div>
                <!-- Completion rate bar -->
                <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
                    <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-muted);margin-bottom:6px;">
                        <span>Completion rate</span>
                        <span id="completionPct" style="font-weight:700;color:var(--text);">—</span>
                    </div>
                    <div class="comp-bar-wrap">
                        <div class="comp-bar-fill" id="completionBar" style="width:0%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Monthly trend + Active tickets -->
    <div class="row-2" style="margin-bottom:16px;">
        <div class="card">
            <div class="card-head">
                <span class="card-title">Tren 6 bulan terakhir</span>
            </div>
            <div class="card-body">
                <div class="legend">
                    <span><span class="legend-dot" style="background:#2563eb;"></span>Masuk</span>
                    <span><span class="legend-dot" style="background:#059669;"></span>Selesai</span>
                </div>
                <div class="chart-wrap" style="height:180px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-head">
                <span class="card-title">EJO per departemen</span>
                <span id="overdueBadge" style="display:none;font-size:11px;font-weight:600;color:#A32D2D;background:#FCEBEB;padding:2px 8px;border-radius:99px;">⚠ ada terlambat</span>
            </div>
            <div class="card-body" id="deptList">
                <div class="skel" style="height:160px;"></div>
            </div>
        </div>
    </div>

    <!-- Row 3: Active ticket progress list -->
    <div class="card" style="margin-bottom:16px;">
        <div class="card-head">
            <span class="card-title">Progress tiket aktif (open)</span>
            <a href="{{ url('ejo') }}?status=Open" style="font-size:12px;color:var(--primary);text-decoration:none;">Lihat semua →</a>
        </div>
        <div class="card-body" id="activeTickets">
            <div class="skel" style="height:120px;"></div>
        </div>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    let donutInst = null;
    let trendInst = null;

    const BAR_COLORS = ['#2563eb', '#059669', '#d97706', '#dc2626', '#7c3aed', '#db2777', '#0891b2', '#65a30d'];

    async function loadDashboard() {
        document.getElementById('last-updated').textContent = 'Memuat...';
        try {
            const res = await fetch('/api/ejo/dashboard');
            const data = await res.json();

            renderMetrics(data.summary);
            renderClassification(data.per_classification);
            renderDonut(data.per_status, data.summary);
            renderTrend(data.monthly_trend);
            renderDepartments(data.per_department);
            renderActiveTickets(data.active_tickets);

            const now = new Date();
            document.getElementById('last-updated').textContent =
                'Update ' + now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
        } catch (e) {
            document.getElementById('last-updated').textContent = 'Gagal memuat';
            console.error(e);
        }
    }

    /* ── Metrics ── */
    function renderMetrics(s) {
        document.getElementById('metricGrid').innerHTML = `
        <div class="metric-card" onclick="location.href='/ejo?status=Open'">
            <div class="accent acc-amber"></div>
            <div class="mc-icon icon-amber">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="mc-label">Belum Selesai</div>
            <div class="mc-value">${s.open}</div>
            <div class="mc-sub">dari ${s.total} total tiket</div>
        </div>
        <div class="metric-card" onclick="location.href='/ejo?status=Done'">
            <div class="accent acc-teal"></div>
            <div class="mc-icon icon-teal">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="mc-label">Selesai</div>
            <div class="mc-value">${s.done}</div>
            <div class="mc-sub">${s.completion_rate}% completion rate</div>
        </div>
        <div class="metric-card">
            <div class="accent acc-red"></div>
            <div class="mc-icon icon-red">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="mc-label">Terlambat</div>
            <div class="mc-value" style="color:var(--danger);">${s.overdue}</div>
            <div class="mc-sub">lewat jadwal schedule</div>
        </div>
        <div class="metric-card">
            <div class="accent acc-blue"></div>
            <div class="mc-icon icon-blue">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div class="mc-label">Rata-rata Progress</div>
            <div class="mc-value">${s.avg_progress}%</div>
            <div class="mc-sub">dari tiket yang masih open</div>
        </div>
    `;
    }

    /* ── Classification bars ── */
    function renderClassification(list) {
        if (!list.length) {
            document.getElementById('classificationList').innerHTML = '<div class="empty">Tidak ada data klasifikasi</div>';
            return;
        }
        const max = list[0].count;
        document.getElementById('classificationList').innerHTML = list.map((r, i) => `
        <div class="class-row">
            <span class="class-label">
                ${r.classification_name}
                <span class="class-type">${r.type_name ? '· ' + r.type_name : ''}</span>
            </span>
            <div class="bar-wrap">
                <div class="bar-fill" style="width:${Math.round(r.count/max*100)}%;background:${BAR_COLORS[i%BAR_COLORS.length]};"></div>
            </div>
            <span class="class-num">${r.count}</span>
        </div>
    `).join('');
    }

    /* ── Donut chart ── */
    function renderDonut(statusList, summary) {
        document.getElementById('donutCenter').textContent = summary.total;

        document.getElementById('completionPct').textContent = summary.completion_rate + '%';
        document.getElementById('completionBar').style.width = summary.completion_rate + '%';

        const colors = {
            'Done': '#059669',
            'Open': '#d97706'
        };
        const labels = statusList.map(s => s.status);
        const values = statusList.map(s => s.count);
        const bgs = statusList.map(s => colors[s.status] ?? '#94a3b8');

        if (donutInst) {
            donutInst.destroy();
            donutInst = null;
        }

        donutInst = new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: bgs,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: false,
                cutout: '74%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: c => ` ${c.label}: ${c.raw} tiket`
                        }
                    }
                },
                animation: {
                    duration: 800
                }
            }
        });

        document.getElementById('statusLegend').innerHTML = statusList.map(s => `
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
            <span style="display:flex;align-items:center;gap:7px;font-size:13px;color:var(--text);">
                <span style="width:10px;height:10px;border-radius:2px;background:${colors[s.status]??'#94a3b8'};display:inline-block;"></span>
                ${s.status}
            </span>
            <span style="font-size:13px;font-weight:700;color:var(--text);">
                ${s.count}
                <span style="font-size:11px;font-weight:400;color:var(--text-muted);">(${summary.total ? Math.round(s.count/summary.total*100) : 0}%)</span>
            </span>
        </div>
    `).join('');
    }

    /* ── Monthly trend chart ── */
    function renderTrend(data) {
        if (trendInst) {
            trendInst.destroy();
            trendInst = null;
        }
        trendInst = new Chart(document.getElementById('trendChart'), {
            type: 'bar',
            data: {
                labels: data.map(d => d.label),
                datasets: [{
                        label: 'Masuk',
                        data: data.map(d => d.created),
                        backgroundColor: 'rgba(37,99,235,.75)',
                        borderRadius: 4,
                        borderSkipped: false,
                    },
                    {
                        label: 'Selesai',
                        data: data.map(d => d.completed),
                        backgroundColor: 'rgba(5,150,105,.75)',
                        borderRadius: 4,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 11
                            }
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    }

    /* ── Department list ── */
    function renderDepartments(list) {
        if (!list.length) {
            document.getElementById('deptList').innerHTML = '<div class="empty">Tidak ada data</div>';
            return;
        }
        const hasOverdue = list.some(d => d.overdue > 0);
        if (hasOverdue) document.getElementById('overdueBadge').style.display = 'inline-flex';

        document.getElementById('deptList').innerHTML = list.map(d => `
        <div class="dept-row">
            <span class="dept-name">
                ${d.department}
                ${d.overdue ? `<span class="badge-overdue">${d.overdue} terlambat</span>` : ''}
            </span>
            <span class="dept-count">${d.count}</span>
        </div>
    `).join('');
    }

    /* ── Active tickets ── */
    function renderActiveTickets(list) {
        if (!list.length) {
            document.getElementById('activeTickets').innerHTML = '<div class="empty">🎉 Semua tiket sudah selesai!</div>';
            return;
        }
        document.getElementById('activeTickets').innerHTML = `
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:0 24px;">
            ${list.map(t => {
                const color = t.progress >= 75 ? '#059669' : t.progress >= 40 ? '#d97706' : '#dc2626';
                return `
                    <a class="ticket-row" href="/ejo/${t.id}" style="color:inherit;">
                        <span class="ticket-id">${t.ticket_id ?? '—'}</span>
                        ${t.is_overdue ? '<span class="overdue-dot" title="Terlambat"></span>' : ''}
                        <span class="ticket-subject" title="${t.subject ?? ''}">${t.subject ?? '—'}</span>
                        <div class="mini-bar-wrap"><div class="mini-bar-fill" style="width:${t.progress}%;background:${color};"></div></div>
                        <span class="pct-label" style="color:${color};">${t.progress}%</span>
                    </a>
                `;
            }).join('')}
        </div>
    `;
    }

    // Jalankan saat halaman load
    loadDashboard();
</script>
@endsection