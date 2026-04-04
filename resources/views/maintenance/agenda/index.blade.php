@extends('layouts.app')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap');

    body {
        font-family: 'DM Sans', sans-serif;
    }

    /* ── Summary Cards ── */
    .sum-card {
        border-radius: 10px;
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        position: relative;
        overflow: hidden;
        transition: transform .2s;
    }

    .sum-card:hover {
        transform: translateY(-1px);
    }

    .sum-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        border-radius: 10px 10px 0 0;
    }

    .sum-card.overdue::before {
        background: #dc3545;
    }

    .sum-card.critical::before {
        background: #fd7e14;
    }

    .sum-card.upcoming::before {
        background: #ffc107;
    }

    .sum-card.scheduled::before {
        background: #198754;
    }

    .sum-card.no-record::before {
        background: #6c757d;
    }

    .sum-card.no-sched::before {
        background: #343a40;
    }

    .sum-card__count {
        font-size: 32px;
        font-weight: 800;
        line-height: 1;
    }

    .sum-card__label {
        font-size: 10.5px;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    /* ── Tab bar ── */
    .tab-bar {
        display: flex;
        gap: 4px;
        padding: 4px;
        width: fit-content;
        margin-bottom: 24px;
        border-radius: 10px;
    }

    .tab-btn {
        font-size: 12px;
        letter-spacing: .05em;
        text-transform: uppercase;
        padding: 8px 20px;
        border-radius: 7px;
        border: none;
        background: transparent;
        cursor: pointer;
        transition: background .15s, color .15s;
    }

    /* ── Filter chips ── */
    .filter-chip {
        font-size: 11.5px;
        padding: 6px 14px;
        border-radius: 20px;
        text-decoration: none;
        display: inline-block;
        transition: all .15s;
    }

    /* ── Agenda Table ── */
    .agenda-table thead th {
        font-size: 10.5px;
        text-transform: uppercase;
        letter-spacing: .08em;
        padding: 12px 20px;
        text-align: left;
        white-space: nowrap;
    }

    .agenda-table tbody td {
        padding: 14px 20px;
        font-size: 13.5px;
        vertical-align: middle;
    }

    .agenda-row {
        transition: background .12s;
    }

    /* ── Machine info ── */
    .machine-code {
        font-size: 12px;
        margin-bottom: 3px;
    }

    .machine-name {
        font-size: 14px;
        font-weight: 500;
    }

    .machine-meta {
        font-size: 11.5px;
        margin-top: 2px;
    }

    /* ── Last date ── */
    .last-date {
        font-size: 12px;
    }

    .last-date.none {
        font-style: italic;
    }

    /* ── Schedule item ── */
    .sched-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sched-freq {
        font-size: 11px;
        min-width: 72px;
    }

    .sched-due {
        font-size: 12px;
    }

    /* ── Status badge ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 10.5px;
        text-transform: uppercase;
        letter-spacing: .06em;
        padding: 3px 10px;
        border-radius: 20px;
        white-space: nowrap;
        font-weight: 500;
    }

    .status-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-badge.overdue {
        background: rgba(220, 53, 69, .12);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, .25);
    }

    .status-badge.overdue::before {
        background: #dc3545;
        box-shadow: 0 0 6px #dc3545;
    }

    .status-badge.critical {
        background: rgba(253, 126, 20, .12);
        color: #fd7e14;
        border: 1px solid rgba(253, 126, 20, .25);
    }

    .status-badge.critical::before {
        background: #fd7e14;
        box-shadow: 0 0 6px #fd7e14;
    }

    .status-badge.upcoming {
        background: rgba(255, 193, 7, .10);
        color: #e6a800;
        border: 1px solid rgba(255, 193, 7, .25);
    }

    .status-badge.upcoming::before {
        background: #ffc107;
    }

    .status-badge.scheduled {
        background: rgba(25, 135, 84, .10);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, .2);
    }

    .status-badge.scheduled::before {
        background: #198754;
    }

    .status-badge.no_record {
        background: rgba(108, 117, 125, .10);
        color: #6c757d;
        border: 1px solid rgba(108, 117, 125, .2);
    }

    .status-badge.no_record::before {
        background: #6c757d;
    }

    .status-badge.no_schedule {
        background: rgba(52, 58, 64, .10);
        color: #6c757d;
        border: 1px solid rgba(52, 58, 64, .2);
    }

    .status-badge.no_schedule::before {
        background: #343a40;
    }

    /* ── Days chip ── */
    .days-chip {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 4px;
    }

    .days-chip.neg {
        background: rgba(220, 53, 69, .15);
        color: #dc3545;
    }

    .days-chip.warn {
        background: rgba(253, 126, 20, .15);
        color: #fd7e14;
    }

    .days-chip.mid {
        background: rgba(255, 193, 7, .12);
        color: #e6a800;
    }

    .days-chip.ok {
        background: rgba(25, 135, 84, .10);
        color: #198754;
    }

    /* ── Calendar ── */
    .cal-nav a {
        font-size: 13px;
        text-decoration: none;
        padding: 6px 14px;
        border-radius: 6px;
        transition: all .15s;
    }

    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        border-radius: 10px;
        overflow: hidden;
    }

    .cal-day-header {
        padding: 10px;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .08em;
        text-align: center;
    }

    .cal-cell {
        min-height: 100px;
        padding: 10px;
        transition: background .12s;
    }

    .cal-day-num {
        font-size: 12px;
        margin-bottom: 6px;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cal-cell.today .cal-day-num {
        background: #0d6efd;
        color: #fff;
        border-radius: 50%;
    }

    .cal-event {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 3px;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .cal-event.overdue {
        background: rgba(220, 53, 69, .2);
        color: #dc3545;
    }

    .cal-event.critical {
        background: rgba(253, 126, 20, .2);
        color: #fd7e14;
    }

    .cal-event.upcoming {
        background: rgba(255, 193, 7, .15);
        color: #e6a800;
    }

    .cal-event.scheduled {
        background: rgba(25, 135, 84, .10);
        color: #198754;
    }

    /* ── Pagination ── */
    .page-btn {
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 6px;
        background: transparent;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all .15s;
    }

    /* ── Search ── */
    .search-wrap {
        position: relative;
    }

    .search-wrap svg {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .search-input {
        font-size: 13px;
        padding: 7px 12px 7px 34px;
        border-radius: 6px;
        width: 220px;
        outline: none;
        transition: border-color .15s;
    }

    /* ── Misc ── */
    .agenda-header__badge {
        font-size: 11px;
        padding: 3px 10px;
        border-radius: 20px;
        letter-spacing: .06em;
        text-transform: uppercase;
        display: inline-block;
        margin-bottom: 8px;
    }

    .section-divider {
        margin: 28px 0;
    }

    @keyframes rowIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .agenda-table tbody tr {
        animation: rowIn .25s ease both;
    }

    @media (max-width: 900px) {

        .agenda-table thead th:nth-child(4),
        .agenda-table tbody td:nth-child(4) {
            display: none;
        }
    }

    @media (max-width: 640px) {
        .cal-cell {
            min-height: 60px;
            padding: 6px 4px;
        }

        .cal-event {
            display: none;
        }
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
                    <a href="{{ route('agenda.index', ['jenis_mtc' => $jenis, 'month' => $calendarMonth]) }}" class="filter-chip border {{ $selectedJenis === $jenis ? 'btn btn-primary border-primary' : 'text-secondary border-secondary border-opacity-25' }}">
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
                <div class="card border rounded-3 overflow-hidden">

                    {{-- Panel Header --}}
                    <div class="card-header  border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3 py-3 px-4">
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
                                        <span class="text-secondary small">{{ $i + 1 }}</span>
                                        @endif
                                    </td>

                                    {{-- Mesin info --}}
                                    <td>
                                        @if ($si === 0)
                                        <div class="machine-code text-primary">{{ $mesin->kode_mesin }}</div>
                                        <div class="machine-name">{{ $mesin->nama_mesin }}</div>
                                        @if ($mesin->lokasi)
                                        <div class="machine-meta text-secondary">📍 {{ $mesin->lokasi }}</div>
                                        @endif
                                        @endif
                                    </td>

                                    {{-- Last date --}}
                                    <td>
                                        @if ($si === 0)
                                        @if ($item['last_date'])
                                        <div class="last-date text-secondary">{{ $item['last_date']->format('d M Y') }}</div>
                                        <div class="text-secondary mt-1" style="font-size:11px">
                                            {{ $item['last_date']->diffForHumans() }}
                                        </div>
                                        @else
                                        <span class="last-date none text-secondary">— Belum ada</span>
                                        @endif
                                        @endif
                                    </td>

                                    {{-- Frekuensi & next due --}}
                                    <td>
                                        <div class="sched-item">
                                            <span class="sched-freq text-secondary">{{ $sch['label'] }}</span>
                                            @if ($sch['next_due'])
                                            <span class="sched-due text-secondary">→ {{ $sch['next_due']->format('d M Y') }}</span>
                                            @else
                                            <span class="text-secondary fst-italic" style="font-size:11px">Tidak ada jadwal</span>
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
                <div class="card border rounded-3">
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
            b.classList.remove('active', 'btn-primary', 'text-white');
            b.classList.add('text-secondary');
        });
        btn.classList.add('active', 'btn-primary', 'text-white');
        btn.classList.remove('text-secondary');
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
            firstTab.classList.add('btn-primary', 'text-white');
            firstTab.classList.remove('text-secondary');
        }
        visibleRows = Array.from(document.querySelectorAll('#agendaBody .agenda-row'));
        renderPage();
    });
</script>
@endsection