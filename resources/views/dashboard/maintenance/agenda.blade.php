@extends('layouts.app')

@section('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -800px 0;
            }

            100% {
                background-position: 800px 0;
            }
        }

        @keyframes pulse-ring {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.55;
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 37%, #f1f5f9 63%);
            background-size: 800px 100%;
            animation: shimmer 1.4s infinite;
            border-radius: 6px;
        }

        /* ── KPI Cards ── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 14px;
        }

        .kpi-card {
            border-radius: 16px;
            padding: 18px 20px;
            overflow: hidden;
        }

        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .07);
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        .kpi-card.kc-total::before {
            background: #6366f1;
        }

        .kpi-card.kc-done::before {
            background: #22c55e;
        }

        .kpi-card.kc-today::before {
            background: #3b82f6;
        }

        .kpi-card.kc-overdue::before {
            background: #ef4444;
        }

        .kpi-card.kc-pending::before {
            background: #f59e0b;
        }

        .kpi-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .kc-total .kpi-icon {
            background: rgba(99, 102, 241, .08);
            color: #6366f1;
        }

        .kc-done .kpi-icon {
            background: rgba(34, 197, 94, .08);
            color: #22c55e;
        }

        .kc-today .kpi-icon {
            background: rgba(59, 130, 246, .08);
            color: #3b82f6;
        }

        .kc-overdue .kpi-icon {
            background: rgba(239, 68, 68, .08);
            color: #ef4444;
        }

        .kc-pending .kpi-icon {
            background: rgba(245, 158, 11, .08);
            color: #f59e0b;
        }

        .kpi-value {
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1;
            margin-top: 10px;
            min-height: 30px;
        }

        .kc-total .kpi-value {
            color: #4f46e5;
        }

        .kc-done .kpi-value {
            color: #16a34a;
        }

        .kc-today .kpi-value {
            color: #2563eb;
        }

        .kc-overdue .kpi-value {
            color: #dc2626;
        }

        .kc-pending .kpi-value {
            color: #d97706;
        }

        .kpi-label {
            font-size: 11.5px;
            font-weight: 600;
            /* color: #64748b; */
        }

        .kpi-sub {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 5px;
        }

        .kpi-sub.green {
            background: #f0fdf4;
            color: #16a34a;
        }

        .kpi-sub.red {
            background: #fef2f2;
            color: #dc2626;
        }

        /* ── Chips ── */
        .chip-group {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .chip {
            font-size: 12.5px;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 30px;
            border: 1.5px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.18s ease;
            user-select: none;
        }

        /* ── Table ── */
        .dash-table-wrap {
            overflow-x: auto;
        }

        .dash-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            min-width: 900px;
        }

        .dash-table thead th {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--vz-body-color, #64748b);
            padding: 11px 8px;
            border-bottom: 1px solid var(--vz-border-color, #e2e8f0);
            white-space: nowrap;
            background: var(--vz-card-bg, #fff);
        }

        /* Sticky table header block (keeps the entire thead sticky to prevent rowspan/colspan horizontal scroll misalignment) */
        .dash-table thead {
            position: sticky;
            top: 0;
            z-index: 5;
        }

        .dash-table thead tr.week-hdr th {
            border-bottom: 1px solid var(--vz-border-color, #e2e8f0);
        }

        .dash-table tbody td {
            padding: 10px 8px;
            font-size: 13px;
            border-bottom: 1px solid var(--vz-border-color, #f1f5f9);
            vertical-align: middle;
            background: var(--vz-card-bg, #fff);
        }

        /* .dash-table tbody tr:hover td {
                                background: var(--vz-table-hover-bg, var(--vz-body-bg, #f8fafc));
                            } */

        /* Sticky columns */
        .dash-table td.sticky-col,
        .dash-table th.sticky-col,
        .dash-table td.sticky-selesai,
        .dash-table th.sticky-selesai,
        .dash-table td.sticky-tipe,
        .dash-table th.sticky-tipe {
            position: sticky;
            background: var(--vz-card-bg, #fff);
        }

        .dash-table td.sticky-col,
        .dash-table th.sticky-col {
            left: 0;
            z-index: 3;
        }

        .dash-table td.sticky-selesai,
        .dash-table th.sticky-selesai {
            left: 190px;
            z-index: 3;
        }

        .dash-table td.sticky-tipe,
        .dash-table th.sticky-tipe {
            left: 270px;
            z-index: 3;
            box-shadow: 2px 0 6px -2px rgba(0, 0, 0, .08);
        }

        .dash-table th.sticky-col,
        .dash-table th.sticky-selesai,
        .dash-table th.sticky-tipe {
            z-index: 6;
        }

        .dash-table tbody tr:hover td.sticky-col,
        .dash-table tbody tr:hover td.sticky-selesai,
        /* .dash-table tbody tr:hover td.sticky-tipe {
                            background: var(--vz-table-hover-bg, var(--vz-body-bg, #f8fafc));
                        } */

        .dash-table tbody tr:last-child td {
            border-bottom: none;
        }

        .mcode {
            font-size: 10.5px;
            font-weight: 700;
            color: #2563eb;
            background: rgba(37, 99, 235, .06);
            padding: 1px 6px;
            border-radius: 4px;
            border: 1px solid rgba(37, 99, 235, .12);
            display: inline-block;
        }

        .mname {
            font-size: 13px;
            font-weight: 600;
            color: var(--vz-body-color, #0f172a);
            margin-top: 3px;
        }

        .mloc {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 1px;
        }

        /* ── Week Chips ── */
        .wk-chip {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            font-size: 9.5px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            white-space: nowrap;
            border: 1px solid transparent;
        }

        .wk-chip[title] {
            cursor: help;
        }

        .wk-chip.done {
            background: #f0fdf4;
            color: #15803d;
            border-color: #bbf7d0;
        }

        .wk-chip.today {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
            animation: pulse-ring 2s ease-in-out infinite;
        }

        .wk-chip.overdue {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .wk-chip.pending {
            background: #fffbeb;
            color: #92400e;
            border-color: #fde68a;
        }

        .wk-chip.unplanned {
            background: var(--vz-table-hover-bg, var(--vz-body-bg, #f8fafc));
            color: #cbd5e1;
            border-color: var(--vz-border-color, #e2e8f0);
        }

        /* ── Completion Ring ── */
        .comp-ring-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ring-svg {
            transform: rotate(-90deg);
        }

        .ring-track {
            fill: none;
            stroke: var(--vz-border-color, #f1f5f9);
            stroke-width: 3.5;
        }

        .ring-fill {
            fill: none;
            stroke-width: 3.5;
            stroke-linecap: round;
            transition: stroke-dashoffset 0.5s ease;
        }

        /* ── Progress Big ── */
        .big-prog-wrap {
            height: 8px;
            background: var(--vz-border-color, #f1f5f9);
            border-radius: 99px;
            overflow: hidden;
        }

        .big-prog-fill {
            height: 100%;
            border-radius: 99px;
            transition: width .6s ease;
        }

        /* ── Legend ── */
        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 18px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--vz-body-color, #64748b);
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
        }

        .legend-dot.done {
            background: #22c55e;
        }

        .legend-dot.today {
            background: #3b82f6;
        }

        .legend-dot.overdue {
            background: #ef4444;
        }

        .legend-dot.pending {
            background: #f59e0b;
        }

        .legend-dot.unplanned {
            background: #cbd5e1;
        }

        /* ── Empty State ── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state .emoji {
            font-size: 48px;
            margin-bottom: 14px;
        }

        /* ── Month Header Row ── */
        .month-hdr th {
            font-size: 9.5px !important;
            text-align: center;
            padding: 6px 6px !important;
        }

        .month-hdr th.mlabel {
            font-size: 10px !important;
            font-weight: 800 !important;
            border-left: 1px solid var(--vz-border-color, #1e293b);
        }

        /* ── Search ── */
        .search-box {
            border: 1.5px solid var(--vz-border-color, #e2e8f0);
            border-radius: 10px;
            padding: 7px 12px 7px 34px;
            font-size: 13px;
            outline: none;
            transition: all .2s;
            width: 220px;
            background: var(--vz-input-bg, var(--vz-card-bg, #fff));
            color: var(--vz-body-color);
        }

        .search-box:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .1);
            background: var(--vz-input-bg, var(--vz-card-bg, #fff));
            color: var(--vz-body-color);
        }

        .search-wrap {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
        }

        /* ── Tab Bar ── */
        .tab-bar {
            display: inline-flex;
            gap: 2px;
            padding: 4px;
            border-radius: 12px;
        }

        /* ── Calendar Grid ── */
        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: var(--vz-border-color, #e2e8f0);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--vz-border-color, #e2e8f0);
        }

        .cal-day-hdr {
            background: var(--vz-table-hover-bg, var(--vz-body-bg, #f8fafc));
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--vz-body-color, #64748b);
            text-align: center;
            padding: 9px 4px;
        }

        .cal-cell {
            background: var(--vz-card-bg, #fff);
            min-height: 96px;
            padding: 7px 6px;
            transition: background 0.15s;
        }

        .cal-cell:not(.other-month) {
            cursor: pointer;
        }

        .cal-cell:not(.other-month):hover {
            background: var(--vz-table-hover-bg, var(--vz-body-bg, #f8fafc));
        }

        .cal-cell.today {
            box-shadow: inset 0 0 0 2px #3b82f6;
        }

        .cal-cell.active-cell {
            background: rgba(99, 102, 241, 0.08) !important;
            box-shadow: inset 0 0 0 2px #6366f1 !important;
        }

        .cal-day-num {
            font-size: 12.5px;
            font-weight: 700;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--vz-body-color, #64748b);
            margin-bottom: 4px;
        }

        .cal-cell.today .cal-day-num {
            background: #3b82f6;
            color: #fff;
            border-radius: 50%;
        }

        .cal-cell.other-month {
            background: var(--vz-table-hover-bg, var(--vz-body-bg, #f8fafc));
            opacity: .45;
        }

        .cal-event {
            font-size: 9.5px;
            font-weight: 700;
            padding: 2px 5px;
            border-radius: 4px;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            border: 1px solid transparent;
            cursor: default;
        }

        .cal-event[title] {
            cursor: help;
        }

        .cal-event.done {
            background: #f0fdf4;
            color: #15803d;
            border-color: #bbf7d0;
        }

        .cal-event.today {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .cal-event.overdue {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .cal-event.pending {
            background: #fffbeb;
            color: #92400e;
            border-color: #fde68a;
        }

        .more-badge {
            font-size: 9px;
            color: #94a3b8;
            font-weight: 600;
            margin-top: 1px;
            display: block;
            cursor: pointer;
        }

        /* ── Agenda List (calendar side) ── */
        .agenda-list-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 16px;
            border-bottom: 1px solid var(--vz-border-color, #f1f5f9);
            transition: background 0.15s;
        }

        /* .agenda-list-item:hover {
                                    background: var(--vz-table-hover-bg, var(--vz-body-bg, #f8fafc));
                                } */

        .agenda-list-item:last-child {
            border-bottom: none;
        }

        .ali-week {
            font-size: 10px;
            font-weight: 700;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: var(--vz-table-hover-bg, var(--vz-body-bg, #f1f5f9));
            color: var(--vz-body-color, #475569);
        }

        .ali-body {
            flex: 1;
            min-width: 0;
        }

        .ali-machine {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--vz-body-color, #0f172a);
        }

        .ali-meta {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 1px;
        }

        .ali-badge {
            font-size: 9.5px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
            border: 1px solid transparent;
            flex-shrink: 0;
        }

        .ali-badge.done {
            background: #f0fdf4;
            color: #15803d;
            border-color: #bbf7d0;
        }

        .ali-badge.today {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .ali-badge.overdue {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .ali-badge.pending {
            background: #fffbeb;
            color: #92400e;
            border-color: #fde68a;
        }

        /* ── Cal month nav ── */
        .cal-nav-btn {
            font-size: 12.5px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 8px;
            background: var(--vz-card-bg, #fff);
            border: 1.5px solid var(--vz-border-color, #e2e8f0);
            color: var(--vz-body-color, #475569);
            cursor: pointer;
            transition: all .18s;
        }

        .cal-nav-btn:hover {
            background: var(--vz-table-hover-bg, var(--vz-body-bg, #f8fafc));
            border-color: var(--vz-border-color, #94a3b8);
            color: var(--vz-body-color, #0f172a);
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- HEADER --}}
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4 animate-fade-in-up">
                <div>
                    <span
                        style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:4px 12px;border-radius:20px;background:rgba(99,102,241,.08);color:#6366f1;border:1px solid rgba(99,102,241,.15);display:inline-block;">
                        Maintenance System
                    </span>
                    <h1 class="fw-bold fs-3 mb-1 mt-2" style="letter-spacing:-0.5px;">Dashboard Agenda Perawatan</h1>
                    <p class="text-secondary small mb-0 fw-medium">
                        Monitoring realisasi vs rencana agenda per mesin &amp; per minggu &mdash;
                        <strong>{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</strong>
                    </p>
                </div>
                <div>
                    <a href="{{ route('master.mtc.agenda.master') }}"
                        class="btn btn-outline-dark shadow-sm d-flex align-items-center gap-2"
                        style="border-radius:9px;font-size:13px;font-weight:600;">
                        <i class="mdi mdi-table-edit"></i> Master Agenda Plan
                    </a>
                </div>
            </div>

            {{-- TAB BAR --}}
            <div class="d-flex align-items-center gap-3 mb-4 animate-fade-in-up" style="animation-delay:.03s;">
                <div class="tab-bar">
                    <button class="btn btn-outline-dark tab-btn active" id="tabBtnMatrix"
                        onclick="switchView('matrix', this)">
                        <i class="mdi mdi-table-large"></i> Matrix View
                    </button>
                    <button class="btn btn-outline-dark tab-btn" id="tabBtnCalendar" onclick="switchView('calendar', this)">
                        <i class="mdi mdi-calendar-month-outline"></i> Kalender
                    </button>
                </div>
            </div>

            {{-- MATRIX PANEL (semua komponen Matrix View dibungkus di sini) --}}
            <div id="panelMatrix">

                {{-- KPI CARDS --}}
                <div class="kpi-grid animate-fade-in-up" style="animation-delay:.05s;">
                    <div class="card kpi-card kc-total">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="kpi-label">Total Rencana</span>
                            <div class="kpi-icon"><i class="mdi mdi-calendar-check-outline" style="font-size:18px;"></i>
                            </div>
                        </div>
                        <div class="kpi-value" id="kpiTotal">
                            <div class="skeleton" style="width:50px;height:26px;"></div>
                        </div>
                        <div class="kpi-label" style="margin-top:5px;">agenda minggu terjadwal</div>
                    </div>
                    <div class="card kpi-card kc-done">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="kpi-label">Terlaksana</span>
                            <div class="kpi-icon"><i class="mdi mdi-check-circle-outline" style="font-size:18px;"></i></div>
                        </div>
                        <div class="kpi-value" id="kpiDone">
                            <div class="skeleton" style="width:50px;height:26px;"></div>
                        </div>
                        <span class="kpi-sub green" id="kpiDonePct">—</span>
                    </div>
                    <div class="card kpi-card kc-today">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="kpi-label">Minggu Ini</span>
                            <div class="kpi-icon"><i class="mdi mdi-calendar-today" style="font-size:18px;"></i></div>
                        </div>
                        <div class="kpi-value" id="kpiToday">
                            <div class="skeleton" style="width:50px;height:26px;"></div>
                        </div>
                        <div class="kpi-label" style="margin-top:5px;">jatuh di minggu berjalan</div>
                    </div>
                    <div class="card kpi-card kc-overdue">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="kpi-label">Terlewat</span>
                            <div class="kpi-icon"><i class="mdi mdi-alert-circle-outline" style="font-size:18px;"></i></div>
                        </div>
                        <div class="kpi-value" id="kpiOverdue">
                            <div class="skeleton" style="width:50px;height:26px;"></div>
                        </div>
                        <span class="kpi-sub red" id="kpiOverduePct">—</span>
                    </div>
                    <div class="card kpi-card kc-pending">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="kpi-label">Menunggu</span>
                            <div class="kpi-icon"><i class="mdi mdi-clock-outline" style="font-size:18px;"></i></div>
                        </div>
                        <div class="kpi-value" id="kpiPending">
                            <div class="skeleton" style="width:50px;height:26px;"></div>
                        </div>
                        <div class="kpi-label" style="margin-top:5px;">belum saatnya</div>
                    </div>
                </div>

                {{-- FILTER BAR (Matrix only) --}}
                <div id="filterBarWrap" class="card shadow-sm border-0 mb-4 animate-fade-in-up"
                    style="animation-delay:.1s;border-radius:16px;">
                    <div class="card-body py-3 px-4">
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-secondary fw-bold text-uppercase"
                                    style="font-size:10.5px;letter-spacing:.06em;white-space:nowrap;">Tahun:</span>
                                <select id="filterTahun" class="form-select form-select-sm"
                                    style="border-radius:8px;width:120px;font-weight:600;">
                                    @for ($y = date('Y') - 3; $y <= date('Y') + 3; $y++)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-secondary fw-bold text-uppercase"
                                    style="font-size:10.5px;letter-spacing:.06em;white-space:nowrap;">Jenis:</span>
                                <select id="filterJenis" class="form-select form-select-sm"
                                    style="border-radius:8px;width:180px;font-weight:600;">
                                    @foreach ($jenisMtcList as $jenis)
                                        <option value="{{ $jenis }}" {{ $loop->first ? 'selected' : '' }}>
                                            {{ $jenis }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-secondary fw-bold text-uppercase"
                                    style="font-size:10.5px;letter-spacing:.06em;white-space:nowrap;">Bulan:</span>
                                <select id="filterBulan" class="form-select form-select-sm"
                                    style="border-radius:8px;width:150px;font-weight:600;">
                                    <option value="all">Semua Bulan</option>
                                    @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bi => $bname)
                                        <option value="{{ $bi + 1 }}" {{ ($bi + 1) == date('n') ? 'selected' : '' }}>
                                            {{ $bname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MATRIX TABLE --}}
                <div class="card shadow-sm border-0 animate-fade-in-up"
                    style="animation-delay:.18s;border-radius:14px;overflow:hidden;">
                    <div class="card-body p-0">
                        <div
                            class="d-flex align-items-center justify-content-between flex-wrap gap-3 px-4 py-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark" style="font-size:13.5px;">Matrix Agenda Per Mesin</span>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border" id="tblMachineCount"
                                    style="font-size:11px;">—</span>
                            </div>
                            <div class="search-wrap">
                                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="14"
                                    height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <circle cx="11" cy="11" r="8" />
                                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                </svg>
                                <input type="text" class="search-box" id="tblSearch" placeholder="Cari mesin…">
                            </div>
                        </div>

                        <!-- Legend Bar -->
                        <div class="legend d-flex align-items-center flex-wrap gap-3 px-4 py-2 border-bottom"
                            style="background: var(--vz-table-hover-bg, var(--vz-body-bg, #f8fafc)); font-size: 11px;">
                            <div><span class="legend-dot pending"></span>Rencana (Plan)</div>
                            <div><span class="legend-dot done"></span>Terlaksana (Actual)</div>
                            <div><span class="legend-dot unplanned"></span>Tidak Terjadwal</div>
                        </div>

                        <div id="tableLoading" class="p-5 text-center text-secondary" style="display:none;">
                            <div class="spinner-border spinner-border-sm text-secondary me-2"></div>
                            Memuat data agenda…
                        </div>

                        <div class="dash-table-wrap" id="tableWrapper"
                            style="display:none;max-height:640px;overflow-y:auto;">
                            <table class="dash-table" id="agendaDashTable">
                                <thead id="tableHead"></thead>
                                <tbody id="tableBody"></tbody>
                            </table>
                        </div>

                        <div class="empty-state" id="tableEmpty" style="display:none;">
                            <div class="emoji">📅</div>
                            <h5 class="fw-bold text-secondary">Belum Ada Master Agenda</h5>
                            <p class="text-muted small mt-2">
                                Master Agenda Plan untuk jenis &amp; tahun yang dipilih belum tersedia.<br>
                                <a href="{{ route('master.mtc.agenda.master') }}"
                                    class="fw-bold text-dark mt-1 d-inline-block">Buat
                                    Master Agenda Plan sekarang &rarr;</a>
                            </p>
                        </div>
                    </div>
                </div>

            </div>{{-- /panelMatrix --}}

            {{-- CALENDAR PANEL --}}
            <div id="panelCalendar" style="display:none;">

                {{-- Calendar Nav Bar --}}
                <div class="card shadow-sm border-0 mb-3" style="border-radius:14px;">
                    <div class="card-body py-3 px-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <button class="cal-nav-btn" id="calPrevBtn">← Prev</button>
                                <h5 class="fw-bold mb-0" id="calMonthTitle"
                                    style="font-size:15px;min-width:160px;text-align:center;">—</h5>
                                <button class="cal-nav-btn" id="calNextBtn">Next →</button>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-secondary fw-bold text-uppercase"
                                    style="font-size:10.5px;letter-spacing:.06em;">Tahun:</span>
                                <select id="calFilterTahun" class="form-select form-select-sm"
                                    style="border-radius:8px;width:100px;font-weight:600;">
                                    @for ($y = date('Y') - 3; $y <= date('Y') + 3; $y++)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        {{-- Cal KPI mini --}}
                        <div class="d-flex flex-wrap gap-3 mt-3 pt-3 border-top">
                            <span class="fw-semibold text-secondary" style="font-size:12px;">Bulan ini:</span>
                            <span style="font-size:12px;"><span class="legend-dot done"></span><strong
                                    id="calKpiDone">—</strong> Terlaksana</span>
                            <span style="font-size:12px;"><span class="legend-dot today"></span><strong
                                    id="calKpiToday">—</strong> Minggu Ini</span>
                            <span style="font-size:12px;"><span class="legend-dot overdue"></span><strong
                                    id="calKpiOverdue">—</strong> Terlewat</span>
                            <span style="font-size:12px;"><span class="legend-dot pending"></span><strong
                                    id="calKpiPending">—</strong> Menunggu</span>
                        </div>
                    </div>
                </div>

                {{-- Grid + List --}}
                <div class="row g-3">
                    {{-- Calendar Grid --}}
                    <div class="col-xl-8">
                        <div class="card shadow-sm border-0" style="border-radius:14px;overflow:hidden;">
                            <div class="card-body p-3">
                                <div id="calLoading" class="p-5 text-center text-secondary" style="display:none;">
                                    <div class="spinner-border spinner-border-sm text-secondary me-2"></div> Memuat
                                    kalender…
                                </div>
                                <div id="calGrid"></div>
                            </div>
                        </div>
                    </div>
                    {{-- Agenda List --}}
                    <div class="col-xl-4">
                        <div class="card shadow-sm border-0" style="border-radius:14px;overflow:hidden;">
                            <div class="card-body p-0">
                                <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="fw-bold text-dark" style="font-size:13px;">Daftar Agenda</span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border ms-2"
                                            id="calListCount" style="font-size:11px;">—</span>
                                    </div>
                                    <button class="btn btn-link btn-sm p-0 d-none" id="btnResetCalFilter" onclick="resetCalendarFilter()" style="font-size:11px;text-decoration:none;font-weight:600;color:var(--vz-link-color, #3b82f6);">Lihat Semua</button>
                                </div>
                                <div style="max-height:600px;overflow-y:auto;" id="calList">
                                    <div class="p-4 text-center text-secondary" style="font-size:13px;">Belum ada data.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /panelCalendar --}}

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const MONTHS_SHORT = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        const MONTHS_FULL = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
            'Oktober', 'November', 'Desember'
        ];
        const DAYS_ID = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        let currentJenis = @json($jenisMtcList->first() ?? '');
        let currentTahun = {{ date('Y') }};
        let currentBulan = '{{ date("n") }}';

        // Calendar state
        let calTahun = {{ date('Y') }};
        let calBulan = {{ date('n') }};
        let allCalendarEvents = [];

        // ── Tab Switch ──
        function switchView(view, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            if (view === 'matrix') {
                $('#filterBarWrap').show();
                $('#panelMatrix').show();
                $('#panelCalendar').hide();
            } else {
                $('#filterBarWrap').hide();
                $('#panelMatrix').hide();
                $('#panelCalendar').show();
                loadCalendarData();
            }
        }

        // ── Status helpers ──
        function statusLabel(s) {
            return {
                done: 'Terlaksana',
                today: 'Minggu Ini',
                overdue: 'Terlewat',
                pending: 'Menunggu',
                unplanned: 'Tidak Terjadwal'
            } [s] || '—';
        }

        function statusIcon(s) {
            return {
                done: '✓',
                today: '◉',
                overdue: '✕',
                pending: '○'
            } [s] || '—';
        }

        // ── Calendar: Render ──
        function renderCalendar(data) {
            const {
                tahun,
                bulan,
                days,
                events
            } = data;
            const firstDay = new Date(tahun, bulan - 1, 1).getDay(); // 0=Sun
            const todayD = (new Date().getFullYear() === tahun && new Date().getMonth() + 1 === bulan) ? new Date()
                .getDate() : -1;

            let html = '<div class="cal-grid">';
            // Day headers (Mon first)
            const HDRS = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            HDRS.forEach(d => {
                html += `<div class="cal-day-hdr">${d}</div>`;
            });

            // First day offset (convert Sun=0 to Mon=0)
            let offset = (firstDay === 0) ? 6 : firstDay - 1;
            for (let i = 0; i < offset; i++) html += '<div class="cal-cell other-month"></div>';

            for (let d = 1; d <= days; d++) {
                const isToday = d === todayD;
                let startDay = 1;
                if (d >= 8 && d <= 14) startDay = 8;
                else if (d >= 15 && d <= 21) startDay = 15;
                else if (d >= 22 && d <= 28) startDay = 22;
                else if (d >= 29) startDay = 29;

                html += `<div class="cal-cell ${isToday ? 'today' : ''}" data-day="${d}" data-start-day="${startDay}" onclick="filterCalListByDay(${d}, ${startDay}, this)">`;
                html += `<div class="cal-day-num">${d}</div>`;

                // Events that start on this day
                const dayEvents = events[d] || [];
                const MAX_SHOW = 3;
                dayEvents.slice(0, MAX_SHOW).forEach(ev => {
                    const tip =
                        `${ev.nama_mesin} (${ev.jenis_mtc}) · Paket ${ev.paket} · ${statusLabel(ev.status)}`;
                    html +=
                        `<span class="cal-event ${ev.status}" title="${tip}">${statusIcon(ev.status)} ${ev.kode_mesin||ev.nama_mesin.substring(0,8)}</span>`;
                });
                if (dayEvents.length > MAX_SHOW) {
                    html += `<span class="more-badge">+${dayEvents.length - MAX_SHOW} lainnya</span>`;
                }
                html += '</div>';
            }

            // Trailing cells
            const total = offset + days;
            const trail = (7 - total % 7) % 7;
            for (let i = 0; i < trail; i++) html += '<div class="cal-cell other-month"></div>';

            html += '</div>';
            $('#calGrid').html(html);
        }

        // ── Calendar: Render List ──
        function renderCalList(list) {
            if (!list || list.length === 0) {
                $('#calList').html(
                    '<div class="p-4 text-center text-secondary" style="font-size:13px;">Tidak ada agenda bulan ini.</div>'
                );
                $('#calListCount').text('0');
                return;
            }
            let html = '';
            list.forEach(item => {
                const wkLabel = `W${item.minggu_ke} (${item.day_start}–${item.day_end})`;
                html += `<div class="agenda-list-item">
                    <div class="ali-week">W${item.minggu_ke}</div>
                    <div class="ali-body">
                        <div class="ali-machine">${item.nama_mesin} ${item.kode_mesin ? '<span style="font-size:10px;color:#94a3b8;">('+item.kode_mesin+')</span>' : ''}</div>
                        <div class="ali-meta">${item.jenis_mtc} · Paket ${item.paket} · ${wkLabel}</div>
                        ${item.tanggal_aktual ? `<div class="ali-meta" style="color:#16a34a;">✓ ${item.tanggal_aktual}</div>` : ''}
                    </div>
                    <span class="ali-badge ${item.status}">${statusLabel(item.status)}</span>
                </div>`;
            });
            $('#calList').html(html);
            $('#calListCount').text(list.length);
        }

        // ── Filter Calendar List by Clicked Day ──
        function filterCalListByDay(day, startDay, elem) {
            // 1. Highlight clicked cell
            $('.cal-cell').removeClass('active-cell');
            $(elem).addClass('active-cell');

            // 2. Filter events of that week
            const filtered = allCalendarEvents.filter(item => item.day_start === startDay);

            // 3. Render list
            renderCalList(filtered);

            // 4. Show "Lihat Semua" button
            $('#btnResetCalFilter').removeClass('d-none');
        }

        // ── Reset Calendar List Filter ──
        function resetCalendarFilter() {
            $('.cal-cell').removeClass('active-cell');
            renderCalList(allCalendarEvents);
            $('#btnResetCalFilter').addClass('d-none');
        }

        // ── Calendar AJAX ──
        function loadCalendarData() {
            $('#calLoading').show();
            $('#calGrid').html('');
            $('#calList').html('<div class="p-4 text-center text-secondary">Memuat…</div>');

            $('#calMonthTitle').text(MONTHS_FULL[calBulan] + ' ' + calTahun);
            ['calKpiDone', 'calKpiToday', 'calKpiOverdue', 'calKpiPending'].forEach(id => $(`#${id}`).text('…'));

            $.ajax({
                url: "{{ route('agenda.dashboard.calendar') }}",
                type: 'GET',
                data: {
                    tahun: calTahun,
                    bulan: calBulan
                },
                success: function(res) {
                    $('#calLoading').hide();
                    if (!res.status) return;
                    allCalendarEvents = res.list || [];
                    $('#btnResetCalFilter').addClass('d-none');
                    renderCalendar(res);
                    renderCalList(allCalendarEvents);
                    const s = res.summary || {};
                    $('#calKpiDone').text(s.done ?? 0);
                    $('#calKpiToday').text(s.today ?? 0);
                    $('#calKpiOverdue').text(s.overdue ?? 0);
                    $('#calKpiPending').text(s.pending ?? 0);
                },
                error: function() {
                    $('#calLoading').hide();
                    $('#calGrid').html('<p class="text-danger p-3">Gagal memuat data kalender.</p>');
                }
            });
        }

        $('#calPrevBtn').on('click', function() {
            calBulan--;
            if (calBulan < 1) {
                calBulan = 12;
                calTahun--;
            }
            $('#calFilterTahun').val(calTahun);
            loadCalendarData();
        });
        $('#calNextBtn').on('click', function() {
            calBulan++;
            if (calBulan > 12) {
                calBulan = 1;
                calTahun++;
            }
            $('#calFilterTahun').val(calTahun);
            loadCalendarData();
        });
        $('#calFilterTahun').on('change', function() {
            calTahun = parseInt($(this).val());
            loadCalendarData();
        });


        function renderKpi(summary) {
            const total = summary.total_planned || 0;
            const donePct = total > 0 ? Math.round(summary.done / total * 100) : 0;
            const ovdPct = total > 0 ? Math.round(summary.overdue / total * 100) : 0;

            $('#kpiTotal').text(total);
            $('#kpiDone').text(summary.done ?? 0);
            $('#kpiToday').text(summary.today ?? 0);
            $('#kpiOverdue').text(summary.overdue ?? 0);
            $('#kpiPending').text(summary.pending ?? 0);
            $('#kpiDonePct').text(donePct + '% dari rencana');
            $('#kpiOverduePct').text(ovdPct + '% dari rencana');

            var fc = donePct >= 80 ? '#22c55e' : (donePct >= 50 ? '#f59e0b' : '#ef4444');
            $('#progressPct').text(donePct + '%').css('color', fc);
            $('#progressFill').css({
                width: donePct + '%',
                background: fc
            });

            var bulanTxt = currentBulan === 'all' ? 'Seluruh Bulan' : MONTHS_FULL[parseInt(currentBulan)];
            $('#progressLabel').text(currentJenis + ' · ' + currentTahun + ' · ' + bulanTxt);
        }

        function renderTable(machines) {
            var months = currentBulan === 'all' ? [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] : [parseInt(currentBulan)];

            // thead
            var row1 = '<tr class="month-hdr">' +
                '<th rowspan="2" class="sticky-col" style="width:190px;min-width:190px;max-width:190px;text-align:left;color:#64748b!important;font-size:10.5px!important;">Mesin</th>' +
                '<th rowspan="2" class="sticky-selesai" style="width:80px;min-width:80px;max-width:80px;text-align:center;font-size:10.5px!important;">Selesai</th>' +
                '<th rowspan="2" class="sticky-tipe" style="width:70px;min-width:70px;max-width:70px;text-align:center;font-size:10.5px!important;">Tipe</th>';
            months.forEach(function(m) {
                row1 += '<th colspan="5" class="mlabel">' + MONTHS_SHORT[m] + '</th>';
            });
            row1 += '</tr>';

            var row2 = '<tr class="week-hdr">';
            months.forEach(function() {
                for (var w = 1; w <= 5; w++) {
                    row2 += '<th style="text-align:center;' + (w === 1 ?
                            'border-left:1px solid var(--vz-border-color, #e2e8f0);' : '') +
                        '">M' + w + '</th>';
                }
            });
            row2 += '</tr>';
            $('#tableHead').html(row1 + row2);

            // tbody
            var bodyHtml = '';
            var count = 0;

            machines.forEach(function(m) {
                var search = ((m.kode_mesin || '') + ' ' + (m.nama_mesin || '')).toLowerCase();
                var rate = m.completion_rate;
                var rc = rate !== null ? (rate >= 80 ? '#22c55e' : (rate >= 50 ? '#f59e0b' : '#ef4444')) :
                    '#cbd5e1';

                var agMap = {};
                (m.agenda || []).forEach(function(a) {
                    agMap[a.bulan + '_' + a.minggu_ke] = a;
                });

                var tdsPlan = '';
                var tdsActual = '';

                months.forEach(function(bln) {
                    for (var wk = 1; wk <= 5; wk++) {
                        var a = agMap[bln + '_' + wk];
                        var bl = wk === 1 ? 'border-left:1px solid var(--vz-border-color, #f1f5f9);' : '';

                        // Render Plan
                        if (!a || !a.plan) {
                            tdsPlan += '<td style="text-align:center;' + bl +
                                '"><span class="wk-chip unplanned">&mdash;</span></td>';
                        } else {
                            var p = a.plan;
                            var tip = 'Rencana: Paket ' + p.paket + (p.tanggal_aktual ? ' · Terlaksana: ' +
                                p.tanggal_aktual : '');
                            tdsPlan += '<td style="text-align:center;' + bl + '">' +
                                '<span class="wk-chip pending" title="' + tip + '">' + p.paket + '</span>' +
                                '</td>';
                        }

                        // Render Actual
                        if (!a || !a.actual) {
                            tdsActual += '<td style="text-align:center;' + bl +
                                '"><span class="wk-chip unplanned">&mdash;</span></td>';
                        } else {
                            var act = a.actual;
                            var tip = 'Realisasi: ' + act.tanggal + (act.paket ? ' · Paket ' + act.paket :
                                '');
                            tdsActual += '<td style="text-align:center;' + bl + '">' +
                                '<span class="wk-chip done" title="' + tip + '">✓ ' + (act.paket || 'Mtc') +
                                '</span>' +
                                '</td>';
                        }
                    }
                });

                var circum = (2 * Math.PI * 11).toFixed(2);
                var offset = rate !== null ? (circum - (rate / 100) * circum).toFixed(2) : circum;
                var ringHtml = rate !== null ?
                    '<div class="comp-ring-wrap">' +
                    '<svg class="ring-svg" width="28" height="28" viewBox="0 0 28 28">' +
                    '<circle class="ring-track" cx="14" cy="14" r="11"/>' +
                    '<circle class="ring-fill" cx="14" cy="14" r="11" stroke="' + rc + '" stroke-dasharray="' +
                    circum + '" stroke-dashoffset="' + offset + '"/>' +
                    '</svg>' +
                    '<span style="font-size:11px;font-weight:700;color:' + rc + ';">' + rate + '%</span>' +
                    '</div>' :
                    '<span class="text-secondary" style="font-size:12px;">&mdash;</span>';

                bodyHtml += '<tr data-search="' + search + '">' +
                    '<td class="sticky-col" rowspan="2" style="width:190px;min-width:190px;max-width:190px;">' +
                    (m.kode_mesin ? '<div><span class="mcode">' + m.kode_mesin + '</span></div>' : '') +
                    '<div class="mname">' + m.nama_mesin + '</div>' +
                    (m.lokasi ? '<div class="mloc">📍 ' + m.lokasi + '</div>' : '') +
                    '</td>' +
                    '<td class="sticky-selesai" style="text-align:center; vertical-align: middle; width:80px; min-width:80px; max-width:80px;" rowspan="2">' +
                    ringHtml + '</td>' +
                    '<td class="sticky-tipe" style="text-align:center; vertical-align: middle; width:70px; min-width:70px; max-width:70px;"><span class="badge badge-soft-warning fw-bold" style="font-size:10px; padding: 4px 6px;">PLAN</span></td>' +
                    tdsPlan +
                    '</tr>' +
                    '<tr data-search="' + search + '">' +
                    '<td class="sticky-tipe" style="text-align:center; vertical-align: middle; width:70px; min-width:70px; max-width:70px;"><span class="badge badge-soft-success text-success fw-bold" style="font-size:10px; padding: 4px 6px;">ACTUAL</span></td>' +
                    tdsActual +
                    '</tr>';
                count++;
            });

            if (count === 0) {
                bodyHtml =
                    '<tr><td colspan="100" class="text-center text-secondary py-5" style="font-size:13px;">Tidak ada data agenda untuk filter ini.</td></tr>';
            }

            $('#tableBody').html(bodyHtml);
            $('#tblMachineCount').text(count + ' mesin');
            $('#tableWrapper').show();
            $('#tableLoading').hide();
            $('#tableEmpty').hide();
        }

        $('#tblSearch').on('input', function() {
            var q = $(this).val().trim().toLowerCase();
            $('#tableBody tr[data-search]').each(function() {
                $(this).toggle(!q || ($(this).data('search') || '').includes(q));
            });
        });

        function loadDashboardData() {
            $('#tableWrapper').hide();
            $('#tableEmpty').hide();
            $('#tableLoading').show();

            ['kpiTotal', 'kpiDone', 'kpiToday', 'kpiOverdue', 'kpiPending'].forEach(function(id) {
                $('#' + id).html(
                    '<div class="skeleton" style="width:48px;height:26px;display:inline-block;"></div>');
            });
            $('#kpiDonePct, #kpiOverduePct').text('—');
            $('#progressPct').text('—');
            $('#progressFill').css('width', '0%');
            $('#progressLabel').text('Memuat…');

            $.ajax({
                url: "{{ route('agenda.dashboard.data') }}",
                type: 'GET',
                data: {
                    jenis_mtc: currentJenis,
                    tahun: currentTahun,
                    bulan: currentBulan
                },
                success: function(res) {
                    if (!res.status) {
                        $('#tableLoading').hide();
                        $('#tableEmpty').show();
                        return;
                    }
                    renderKpi(res.summary || {});
                    if (!res.machines || res.machines.length === 0 || (res.summary.total_planned || 0) === 0) {
                        $('#tableLoading').hide();
                        $('#tableEmpty').show();
                        return;
                    }
                    renderTable(res.machines);
                },
                error: function() {
                    $('#tableLoading').hide();
                    $('#tableEmpty').show();
                    ['kpiTotal', 'kpiDone', 'kpiToday', 'kpiOverdue', 'kpiPending'].forEach(function(id) {
                        $('#' + id).text('—');
                    });
                }
            });
        }

        $('#filterJenis').on('change', function() {
            currentJenis = $(this).val();
            loadDashboardData();
        });

        $('#filterBulan').on('change', function() {
            currentBulan = $(this).val().toString();
            loadDashboardData();
        });

        $('#filterTahun').on('change', function() {
            currentTahun = parseInt($(this).val());
            loadDashboardData();
        });

        $(document).ready(function() {
            loadDashboardData();
        });
    </script>
@endsection
