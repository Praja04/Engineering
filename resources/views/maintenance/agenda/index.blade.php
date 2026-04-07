@extends('layouts.app')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

   

    /* ── Summary Cards ── */
    .sum-card {
        border-radius: 16px;
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.04) !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        transition: transform .2s, box-shadow .2s;
    }

    .sum-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.06);
    }

    .sum-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 5px;
    }

    .sum-card.overdue::before { background: #ef4444; }
    .sum-card.critical::before { background: #f97316; }
    .sum-card.upcoming::before { background: #eab308; }
    .sum-card.scheduled::before { background: #22c55e; }
    .sum-card.no-record::before { background: #9ca3af; }
    .sum-card.no-sched::before { background: #4b5563; }

    .sum-card__count {
        font-size: 36px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -1px;
    }

    .sum-card__label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .1em;
        font-weight: 600;
        color: #64748b;
    }

    /* ── Tab bar ── */
    .tab-bar {
        display: inline-flex;
        gap: 4px;
        padding: 6px;
        margin-bottom: 24px;
        border-radius: 12px;
        background: #f1f5f9;
        border: 1px solid rgba(0,0,0,0.03) !important;
    }

    .tab-btn {
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .02em;
        padding: 8px 24px;
        border-radius: 8px;
        border: none;
        background: transparent;
        color: #64748b;
        cursor: pointer;
        transition: all .2s ease;
    }

    .tab-btn.active {
        background: #ffffff;
        color: #0f172a !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .tab-btn:hover:not(.active) {
        color: #334155;
    }

    /* ── Filter chips ── */
    .filter-chip {
        font-size: 13px;
        font-weight: 500;
        padding: 8px 18px;
        border-radius: 20px;
        text-decoration: none;
        display: inline-block;
        transition: all .2s;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff;
        color: #475569 !important;
    }

    .filter-chip:hover {
        background: #f8fafc;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .filter-chip.active-chip {
        background: #2563eb !important;
        border-color: #2563eb !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    /* ── Agenda Table ── */
    .agenda-table thead th {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #475569;
        padding: 16px 24px;
        background: #f1f5f9;
        border-bottom: 2px solid #e2e8f0;
        text-align: left;
        white-space: nowrap;
    }

    .agenda-table tbody td {
        padding: 20px 24px;
        font-size: 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    .agenda-table tbody tr:last-child td {
        border-bottom: none;
    }

    .agenda-row {
        transition: background .15s;
    }

    .agenda-row:hover {
        background: #f8fafc;
    }

    /* ── Machine info ── */
    .machine-code {
        font-size: 12px;
        font-weight: 600;
        color: #2563eb;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }

    .machine-name {
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
    }

    .machine-meta {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
    }

    /* ── Last date ── */
    .last-date {
        font-size: 14px;
        font-weight: 500;
        color: #1e293b;
    }

    .last-date.none {
        font-style: italic;
        color: #94a3b8;
    }

    /* ── Schedule item ── */
    .sched-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sched-freq {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        min-width: 72px;
    }

    .sched-due {
        font-size: 13px;
        color: #64748b;
    }

    /* ── Status badge ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        padding: 6px 12px;
        border-radius: 24px;
        white-space: nowrap;
    }

    .status-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-badge.overdue { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }
    .status-badge.overdue::before { background: #ef4444; box-shadow: 0 0 6px #ef4444; }

    .status-badge.critical { background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; }
    .status-badge.critical::before { background: #f97316; box-shadow: 0 0 6px #f97316; }

    .status-badge.upcoming { background: #fefce8; color: #ca8a04; border: 1px solid #fef9c3; }
    .status-badge.upcoming::before { background: #eab308; }

    .status-badge.scheduled { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
    .status-badge.scheduled::before { background: #22c55e; }

    .status-badge.no_record { background: #f8fafc; color: #64748b; border: 1px solid #f1f5f9; }
    .status-badge.no_record::before { background: #94a3b8; }

    .status-badge.no_schedule { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .status-badge.no_schedule::before { background: #64748b; }

    /* ── Days chip ── */
    .days-chip {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .days-chip.neg { background: #fef2f2; color: #ef4444; }
    .days-chip.warn { background: #fff7ed; color: #f97316; }
    .days-chip.mid { background: #fefce8; color: #eab308; }
    .days-chip.ok { background: #f0fdf4; color: #22c55e; }

    /* ── Calendar ── */
    .cal-nav a {
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 8px;
        color: #475569;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        transition: all .15s;
    }
    
    .cal-nav a:hover { background: #f8fafc; border-color: #cbd5e1; color:#0f172a;}

    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #e2e8f0;
    }

    .cal-day-header {
        padding: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .08em;
        text-align: center;
        background: #f8fafc;
        color: #64748b;
    }

    .cal-cell {
        min-height: 110px;
        padding: 12px;
        background: #ffffff;
        transition: background .12s;
    }

    .cal-cell:hover { background: #f8fafc; }

    .cal-day-num {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
    }

    .cal-cell.today .cal-day-num {
        background: #2563eb;
        color: #ffffff;
        border-radius: 50%;
        box-shadow: 0 4px 10px rgba(37,99,235,0.3);
    }

    .cal-event {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 4px;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        border: 1px solid transparent;
    }

    .cal-event.overdue { background: #fef2f2; color: #dc2626; border-color: #fee2e2; }
    .cal-event.critical { background: #fff7ed; color: #ea580c; border-color: #ffedd5; }
    .cal-event.upcoming { background: #fefce8; color: #ca8a04; border-color: #fef9c3; }
    .cal-event.scheduled { background: #f0fdf4; color: #16a34a; border-color: #dcfce7; }

    /* ── Pagination ── */
    .page-btn {
        font-size: 13px;
        font-weight: 500;
        padding: 8px 14px;
        border-radius: 8px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        cursor: pointer;
        transition: all .15s;
    }
    
    .page-btn:hover:not(.disabled) { background: #f8fafc; color: #0f172a; }
    .page-btn.btn-primary { background: #2563eb; border-color: #2563eb; color: #ffffff; box-shadow: 0 4px 10px rgba(37,99,235,0.2); }

    /* ── Search ── */
    .search-wrap { position: relative; }
    .search-wrap svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; }
    .search-input {
        font-size: 14px;
        padding: 10px 14px 10px 40px;
        border-radius: 10px;
        width: 250px;
        border: 1px solid #e2e8f0;
        outline: none;
        transition: all .2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .search-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }

    /* ── Misc ── */
    .agenda-header__badge {
        font-size: 12px;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 24px;
        letter-spacing: .08em;
        text-transform: uppercase;
        display: inline-block;
        margin-bottom: 12px;
        background: #eff6ff;
        color: #2563eb;
    }

    .section-divider { margin: 32px 0; }
    @keyframes rowIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .agenda-table tbody tr { animation: rowIn .3s ease both; }

    @media (max-width: 900px) {
        .agenda-table thead th:nth-child(4),
        .agenda-table tbody td:nth-child(4) { display: none; }
    }
    @media (max-width: 640px) {
        .cal-cell { min-height: 70px; padding: 8px 6px; }
        .cal-event { display: none; }
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="px-3 px-md-4 py-4">

            {{-- ══════════════════════════════════════════ HEADER ══ --}}
            <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4">
                <div>
                    <span class="agenda-header__badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                        Maintenance System
                    </span>
                    <h1 class="fw-bold fs-4 mb-0">Agenda Perawatan</h1>
                    <p class="text-secondary small mt-1 fw-light">
                        Jadwal &amp; histori inspeksi seluruh mesin — {{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}
                    </p>
                </div>
                <form method="GET" action="{{ route('agenda.index') }}" id="jenisForm" style="display:none"></form>
            </div>

            {{-- ════════════════════════════════════════ SUMMARY ══ --}}
            <div class="row g-2 mb-4">
                @php
                $sumDefs = [
                'overdue' => ['label' => 'Overdue', 'cls' => 'overdue', 'text' => 'text-danger', 'card' => 'border-danger-subtle'],
                'critical' => ['label' => 'Kritis (≤3 hr)', 'cls' => 'critical', 'text' => 'text-warning', 'card' => 'border-warning-subtle'],
                'upcoming' => ['label' => 'Mendekati', 'cls' => 'upcoming', 'text' => 'text-warning', 'card' => 'border-warning-subtle'],
                'scheduled' => ['label' => 'Terjadwal', 'cls' => 'scheduled', 'text' => 'text-success', 'card' => 'border-success-subtle'],
                'no_record' => ['label' => 'Belum Ada Data', 'cls' => 'no-record', 'text' => 'text-secondary','card' => 'border-secondary-subtle'],
                'no_schedule' => ['label' => 'Tanpa Jadwal', 'cls' => 'no-sched', 'text' => 'text-secondary','card' => 'border-secondary-subtle'],
                ];
                @endphp
                @foreach ($sumDefs as $key => $def)
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="sum-card {{ $def['cls'] }} card border {{ $def['card'] }} h-100">
                        <div class="sum-card__count {{ $def['text'] }}">{{ $summary[$key] ?? 0 }}</div>
                        <div class="sum-card__label text-secondary">{{ $def['label'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ═══════════════════════════════════ JENIS FILTER ══ --}}
            <div class="d-flex align-items-center flex-wrap gap-2 mb-4">
                <span class="text-secondary text-uppercase" style="font-size:11px;letter-spacing:.07em">Jenis Mesin</span>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($jenisMtcList as $jenis)
                    <a href="{{ route('agenda.index', ['jenis_mtc' => $jenis, 'month' => $calendarMonth]) }}" class="filter-chip {{ $selectedJenis === $jenis ? 'active-chip' : '' }}">
                        {{ $jenis }}
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- ═══════════════════════════════════════════ TABS ══ --}}
            <div class="tab-bar bg-light border mb-4" role="tablist">
                <button class="tab-btn active btn" role="tab" onclick="switchTab('list', this)">&#9776; List View</button>
                <button class="tab-btn btn" role="tab" onclick="switchTab('calendar', this)">&#9783; Kalender</button>
            </div>

            {{-- ══════════════════════════════════════ LIST PANEL ══ --}}
            <div id="tab-list">
                <div class="table-container">

                    {{-- Panel Header --}}
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 py-4 px-4 bg-white border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold">{{ $selectedJenis }}</span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border" id="rowCount">
                                {{ $agendaData->count() }} mesin
                            </span>
                        </div>
                        <div class="search-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-secondary">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <input class="search-input form-control border" id="searchInput" type="text" placeholder="Cari kode / nama mesin…" oninput="filterTable(this.value)">
                        </div>
                    </div>

                    {{-- Table --}}
                    @if ($agendaData->isEmpty())
                    <div class="text-center py-5">
                        <div class="fs-1 mb-3 opacity-50">🔧</div>
                        <p class="text-secondary small">Tidak ada data mesin untuk jenis <strong>{{ $selectedJenis }}</strong>.</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table agenda-table mb-0" id="agendaTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Mesin</th>
                                    <th>Maintenance Terakhir</th>
                                    <th>Frekuensi &amp; Jadwal Berikut</th>
                                    <th>Status</th>
                                    <th>Sisa Hari</th>
                                </tr>
                            </thead>
                            <tbody id="agendaBody">
                                @foreach ($agendaData as $i => $item)
                                @php $mesin = $item['mesin']; @endphp
                                @foreach ($item['schedules'] as $si => $sch)
                                <tr class="agenda-row" data-search="{{ strtolower($mesin->kode_mesin . ' ' . $mesin->nama_mesin) }}">

                                    {{-- No. --}}
                                    <td>
                                        @if ($si === 0)
                                        <span class="small">{{ $i + 1 }}</span>
                                        @endif
                                    </td>

                                    {{-- Mesin info --}}
                                    <td>
                                        @if ($si === 0)
                                        <div class="machine-code">{{ $mesin->kode_mesin }}</div>
                                        <div class="machine-name">{{ $mesin->nama_mesin }}</div>
                                        @if ($mesin->lokasi)
                                        <div class="machine-meta">📍 {{ $mesin->lokasi }}</div>
                                        @endif
                                        @endif
                                    </td>

                                    {{-- Last date --}}
                                    <td>
                                        @if ($si === 0)
                                        @if ($item['last_date'])
                                        <div class="last-date">{{ $item['last_date']->format('d M Y') }}</div>
                                        <div class="mt-1" style="font-size:11px">
                                            {{ $item['last_date']->diffForHumans() }}
                                        </div>
                                        @else
                                        <span class="last-date none">— Belum ada</span>
                                        @endif
                                        @endif
                                    </td>

                                    {{-- Frekuensi & next due --}}
                                    <td>
                                        <div class="sched-item">
                                            <span class="sched-freq">{{ $sch['label'] }}</span>
                                            @if ($sch['next_due'])
                                            <span class="sched-due">→ {{ $sch['next_due']->format('d M Y') }}</span>
                                            @else
                                            <span class="fst-italic" style="font-size:11px">Tidak ada jadwal</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        <span class="status-badge {{ $sch['status'] }}">
                                            @switch($sch['status'])
                                            @case('overdue') Overdue @break
                                            @case('critical') Kritis @break
                                            @case('upcoming') Mendekati @break
                                            @case('scheduled') Terjadwal @break
                                            @case('no_record') Belum Ada @break
                                            @case('no_schedule')Tanpa Jadwal @break
                                            @endswitch
                                        </span>
                                    </td>

                                    {{-- Days left --}}
                                    <td>
                                        @if ($sch['days_left'] !== null)
                                        @php
                                        $dl = $sch['days_left'];
                                        $chipCls = $dl < 0 ? 'neg' : ($dl <=3 ? 'warn' : ($dl <=14 ? 'mid' : 'ok' )); $label=$dl < 0 ? abs($dl).' hr lewat' : $dl.' hari lagi'; @endphp <span class="days-chip {{ $chipCls }}">{{ $label }}</span>
                                            @else
                                            <span class="text-secondary" style="font-size:11px">—</span>
                                            @endif
                                    </td>

                                </tr>
                                @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 px-4 py-3 border-top">
                        <span class="text-secondary" style="font-size:11.5px" id="paginationInfo"></span>
                        <div class="d-flex gap-1" id="paginationBtns"></div>
                    </div>
                    @endif
                </div>
            </div>{{-- /tab-list --}}

            {{-- ════════════════════════════════ CALENDAR PANEL ══ --}}
            <div id="tab-calendar" style="display:none">
                <div class="table-container">
                    <div class="p-4">

                        {{-- Nav --}}
                        @php
                        $cm = \Carbon\Carbon::parse($calendarMonth . '-01');
                        $prev = $cm->copy()->subMonth()->format('Y-m');
                        $next = $cm->copy()->addMonth()->format('Y-m');
                        @endphp
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <a href="{{ route('agenda.index', ['jenis_mtc' => $selectedJenis, 'month' => $prev]) }}" class="cal-nav border text-secondary rounded">
                                ← {{ $cm->copy()->subMonth()->translatedFormat('M Y') }}
                            </a>
                            <h2 class="fs-5 fw-bold mb-0">{{ $cm->translatedFormat('F Y') }}</h2>
                            <a href="{{ route('agenda.index', ['jenis_mtc' => $selectedJenis, 'month' => $next]) }}" class="cal-nav border text-secondary rounded">
                                {{ $cm->copy()->addMonth()->translatedFormat('M Y') }} →
                            </a>
                        </div>

                        {{-- Grid --}}
                        <div class="cal-grid border bg-secondary bg-opacity-10">
                            @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $d)
                            <div class="cal-day-header bg-light text-secondary">{{ $d }}</div>
                            @endforeach

                            @php
                            $startDow = (int)$cm->copy()->startOfMonth()->dayOfWeek;
                            $startDow = ($startDow === 0) ? 6 : $startDow - 1;
                            $daysInMonth = $cm->daysInMonth;
                            $today = \Carbon\Carbon::today()->day;
                            $isCurrentMonth = $cm->isSameMonth(\Carbon\Carbon::today());
                            @endphp

                            @for ($e = 0; $e < $startDow; $e++) <div class="cal-cell bg-light opacity-50">
                        </div>
                        @endfor

                        @for ($d = 1; $d <= $daysInMonth; $d++) @php $isToday=$isCurrentMonth && $d===$today; @endphp <div class="cal-cell bg-white {{ $isToday ? 'today' : '' }}">
                            <div class="cal-day-num text-secondary">{{ $d }}</div>
                            @if (isset($calendarEvents[$d]))
                            @foreach ($calendarEvents[$d] as $ev)
                            <div class="cal-event {{ $ev['status'] }}" title="{{ $ev['nama'] }} — {{ $ev['frek'] }}">
                                {{ $ev['kode'] }} {{ $ev['frek'] }}
                            </div>
                            @endforeach
                            @endif
                    </div>
                    @endfor

                    @php
                    $total = $startDow + $daysInMonth;
                    $remainder = $total % 7;
                    $trailingCells = $remainder === 0 ? 0 : 7 - $remainder;
                    @endphp
                    @for ($t = 0; $t < $trailingCells; $t++) <div class="cal-cell bg-light opacity-50">
                </div>
                @endfor
            </div>

            {{-- Legend --}}
            <div class="d-flex flex-wrap gap-3 mt-3">
                @foreach ([
                ['overdue', 'Overdue', 'text-danger'],
                ['critical', 'Kritis', 'text-warning'],
                ['upcoming', 'Mendekati', 'text-warning'],
                ['scheduled', 'Terjadwal', 'text-success'],
                ] as $lg)
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-1 d-block {{ $lg[2] }}" style="width:10px;height:10px;background:currentColor;opacity:.8"></span>
                    <span class="text-secondary" style="font-size:11px">{{ $lg[1] }}</span>
                </div>
                @endforeach
            </div>

        </div>
    </div>
</div>{{-- /tab-calendar --}}

</div>{{-- /px-3 --}}
</div>{{-- /container-fluid --}}
</div>{{-- /page-content --}}
@endsection

@section('scripts')
<script>
    /* ── Tab switching ── */
    function switchTab(name, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('active');
        });
        btn.classList.add('active');
        ['list', 'calendar'].forEach(t => {
            document.getElementById('tab-' + t).style.display = (t === name) ? '' : 'none';
        });
    }

    /* ── Client-side search + pagination ── */
    const PER_PAGE = 15;
    let currentPage = 1;
    let visibleRows = [];

    function filterTable(query) {
        const q = query.trim().toLowerCase();
        const allRows = Array.from(document.querySelectorAll('#agendaBody .agenda-row'));
        const matchedKeys = new Set();
        allRows.forEach(row => {
            if (!q || row.dataset.search.includes(q)) matchedKeys.add(row.dataset.search);
        });
        visibleRows = allRows.filter(r => matchedKeys.has(r.dataset.search));
        currentPage = 1;
        renderPage();
    }

    function renderPage() {
        const allRows = Array.from(document.querySelectorAll('#agendaBody .agenda-row'));
        allRows.forEach(r => r.style.display = 'none');

        const start = (currentPage - 1) * PER_PAGE;
        const end = start + PER_PAGE;
        const pageRows = visibleRows.slice(start, end);
        pageRows.forEach(r => r.style.display = '');

        const total = visibleRows.length;
        document.getElementById('paginationInfo').textContent =
            total === 0 ? 'Tidak ada hasil' :
            `Menampilkan ${start + 1}–${Math.min(end, total)} dari ${total} baris`;

        const totalPages = Math.ceil(total / PER_PAGE);
        renderPaginationBtns(totalPages);

        const machineCount = new Set(visibleRows.map(r => r.dataset.search)).size;
        document.getElementById('rowCount').textContent = machineCount + ' mesin';
    }

    function renderPaginationBtns(totalPages) {
        const wrap = document.getElementById('paginationBtns');
        wrap.innerHTML = '';

        const make = (label, page, extra = '') => {
            const el = document.createElement('button');
            el.className = `page-btn btn btn-sm border ${extra}`;
            el.textContent = label;
            if (!extra.includes('disabled')) {
                el.onclick = () => {
                    currentPage = page;
                    renderPage();
                };
            }
            wrap.appendChild(el);
        };

        make('←', currentPage - 1, currentPage === 1 ? 'disabled text-secondary opacity-50' : 'text-secondary');

        const ws = 5;
        let pStart = Math.max(1, currentPage - Math.floor(ws / 2));
        let pEnd = Math.min(totalPages, pStart + ws - 1);
        if (pEnd - pStart < ws - 1) pStart = Math.max(1, pEnd - ws + 1);

        if (pStart > 1) {
            make('1', 1, 'text-secondary');
            if (pStart > 2) make('…', currentPage, 'disabled text-secondary opacity-50');
        }
        for (let p = pStart; p <= pEnd; p++) {
            make(p, p, p === currentPage ? 'btn-primary text-white' : 'text-secondary');
        }
        if (pEnd < totalPages) {
            if (pEnd < totalPages - 1) make('…', currentPage, 'disabled text-secondary opacity-50');
            make(totalPages, totalPages, 'text-secondary');
        }

        make('→', currentPage + 1, currentPage === totalPages || totalPages === 0 ? 'disabled text-secondary opacity-50' : 'text-secondary');
    }

    /* ── Init ── */
    document.addEventListener('DOMContentLoaded', () => {
        // set initial active tab style
        const firstTab = document.querySelector('.tab-btn');
        if (firstTab) {
            firstTab.classList.add('active');
        }
        visibleRows = Array.from(document.querySelectorAll('#agendaBody .agenda-row'));
        renderPage();
    });
</script>
@endsection