@extends('layouts.app')

@section('title', 'Predictive Maintenance Data')

@section('styles')
<style>
    .card-header-custom {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        color: white;
        padding: 1.25rem;
        border-radius: 12px 12px 0 0;
    }

    .badge-status {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        letter-spacing: .4px;
        text-transform: uppercase;
    }
    .badge-open     { background: rgba(59,130,246,.15);  color: #3b82f6; }
    .badge-progress { background: rgba(245,158,11,.15); color: #f59e0b; }
    .badge-done     { background: rgba(34,197,94,.15);  color: #22c55e; }
    .badge-onhold   { background: rgba(239,68,68,.15);  color: #ef4444; }

    .stat-card {
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,.06);
        transition: transform .2s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 20px;
    }

    .filter-bar {
        background: var(--vz-card-bg);
        border: 1px solid var(--vz-border-color);
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }

    #tableReports thead th {
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .6px; color: var(--vz-text-muted); white-space: nowrap;
    }
    #tableReports tbody tr { cursor: pointer; transition: background .15s; }
    #tableReports tbody tr:hover { background: rgba(59,130,246,.05) !important; }

    .empty-state { text-align: center; padding: 60px 20px; color: var(--vz-text-muted); }
    .empty-state i { font-size: 48px; display: block; margin-bottom: 12px; opacity: .4; }

    /* Timeline */
    .timeline-item { position: relative; padding-left: 24px; margin-bottom: 18px; }
    .timeline-item::before {
        content: ''; position: absolute; left: 4px; top: 8px; width: 10px; height: 10px;
        border-radius: 50%; background: #3b82f6; border: 2px solid var(--vz-card-bg);
    }
    .timeline-item.done::before   { background: #22c55e; }
    .timeline-item.onhold::before { background: #ef4444; }
    .timeline-line { border-left: 2px solid var(--vz-border-color); margin-left: 8px; }
</style>
@endsection

@section('content')
<div class="page-content">
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="ri-database-2-line me-2 text-primary"></i>
                EPR — Data Predictive Maintenance
            </h4>
            <p class="text-muted mb-0 fs-13">Riwayat hasil monitoring dan log predictive maintenance harian</p>
        </div>
        @if(in_array(Auth::user()->jabatan, ['operator', 'foreman', 'admin']))
        <a href="{{ route('epr.pm.form') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
            <i class="ri-add-line"></i> Buat Laporan Baru
        </a>
        @endif
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4" id="statCards">
        @php
            $statItems = [
                ['label' => 'Total',    'id' => 'st-total',  'icon' => 'ri-file-list-3-line',      'color' => '#6366f1', 'bg' => 'rgba(99,102,241,.12)'],
                ['label' => 'Open',     'id' => 'st-open',   'icon' => 'ri-record-circle-line',     'color' => '#3b82f6', 'bg' => 'rgba(59,130,246,.12)'],
                ['label' => 'Progress', 'id' => 'st-prog',   'icon' => 'ri-loader-4-line',          'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,.12)'],
                ['label' => 'Done',     'id' => 'st-done',   'icon' => 'ri-checkbox-circle-line',   'color' => '#22c55e', 'bg' => 'rgba(34,197,94,.12)'],
                ['label' => 'On Hold',  'id' => 'st-hold',   'icon' => 'ri-pause-circle-line',      'color' => '#ef4444', 'bg' => 'rgba(239,68,68,.12)'],
            ];
        @endphp
        @foreach($statItems as $s)
        <div class="col-6 col-md">
            <div class="card stat-card mb-0 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon" style="background:{{ $s['bg'] }}; color:{{ $s['color'] }}">
                        <i class="{{ $s['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-20 lh-1 mb-1" id="{{ $s['id'] }}">0</div>
                        <div class="text-muted fs-12">{{ $s['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 text-white fw-bold">
                    <i class="ri-file-list-3-line me-2 text-info"></i>
                    Riwayat Laporan Predictive Maintenance
                </h5>
                <p class="mb-0 text-white-50 small">Kelola, tinjau, dan perbarui log predictive maintenance</p>
            </div>
        </div>
        <div class="card-body p-4">
            {{-- Filter --}}
            <div class="filter-bar">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fs-12 fw-semibold mb-1">Cari</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                            <input type="text" id="filterSearch" class="form-control" placeholder="Cari teknisi, area, pekerjaan...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fs-12 fw-semibold mb-1">Status</label>
                        <select id="filterStatus" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            <option value="open">Open</option>
                            <option value="progress">Progress</option>
                            <option value="done">Done</option>
                            <option value="onhold">On Hold</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fs-12 fw-semibold mb-1">Area</label>
                        <select id="filterArea" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            <option>Filling Retail</option>
                            <option>Packing Retail</option>
                            <option>Gravity Roller</option>
                            <option>Workshop</option>
                            <option>Pasteur</option>
                            <option>Storage</option>
                            <option>Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fs-12 fw-semibold mb-1">Bulan</label>
                        <input type="month" id="filterMonth" class="form-control form-control-sm" value="{{ date('Y-m') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-1">
                        <button class="btn btn-primary btn-sm flex-fill" id="btnFilter">
                            <i class="ri-filter-3-line me-1"></i> Filter
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" id="btnResetFilter">
                            <i class="ri-refresh-line"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table id="tableReports" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Teknisi</th>
                            <th>Area</th>
                            <th>Pekerjaan</th>
                            <th>Waktu Kerja</th>
                            <th>Status</th>
                            <th>Foto</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</div>

{{-- Modal Detail Laporan --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom p-3">
                <h5 class="modal-title fw-bold">
                    <i class="ri-article-line text-primary me-2"></i>
                    <span id="detail-title">Detail Laporan</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detail-body">
                {{-- Filled by JS --}}
            </div>
            <div class="modal-footer border-top p-3" id="detail-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Lightbox --}}
<div id="lightbox" style="display:none; position:fixed; inset:0; z-index:2000; background:rgba(0,0,0,.92); align-items:center; justify-content:center;" onclick="closeLightbox()">
    <button style="position:absolute; top:16px; right:20px; background:rgba(255,255,255,.15); border:none; color:#fff; font-size:20px; cursor:pointer; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center;" onclick="closeLightbox()">✕</button>
    <button class="lb-nav lb-prev" onclick="lbNav(-1,event)" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,.15); border:none; color:#fff; font-size:20px; cursor:pointer; width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center;">‹</button>
    <img id="lbImg" src="" style="max-width:92vw; max-height:86vh; object-fit:contain; border-radius:8px;">
    <button class="lb-nav lb-next" onclick="lbNav(1,event)" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:rgba(255,255,255,.15); border:none; color:#fff; font-size:20px; cursor:pointer; width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center;">›</button>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    let reports = [];
    let lbImages = [];
    let lbIdx = 0;

    loadReports();

    function loadReports() {
        $.get("{{ route('epr.pm.get-reports') }}", function(data) {
            reports = data;
            renderTable();
            renderStats();
        }).fail(function() {
            Swal.fire('Error', 'Gagal memuat data dari server', 'error');
        });
    }

    function renderStats() {
        const root = reports.filter(r => !r.parentId);
        $('#st-total').text(root.length);
        $('#st-open').text(root.filter(r => r.status === 'open').length);
        $('#st-prog').text(root.filter(r => r.status === 'progress').length);
        $('#st-done').text(root.filter(r => r.status === 'done').length);
        $('#st-hold').text(root.filter(r => r.status === 'onhold').length);
    }

    function renderTable() {
        const search = ($('#filterSearch').val() || '').toLowerCase();
        const status = $('#filterStatus').val();
        const area   = $('#filterArea').val();
        const month  = $('#filterMonth').val();

        // Only show root reports
        let list = reports.filter(r => !r.parentId);

        if (search) {
            list = list.filter(r =>
                (r.tech || '').toLowerCase().includes(search) ||
                (r.area || '').toLowerCase().includes(search) ||
                (r.work || '').toLowerCase().includes(search) ||
                (r.adhocTitle || '').toLowerCase().includes(search) ||
                (r.notes || '').toLowerCase().includes(search) ||
                (r.workOrderNo || '').toLowerCase().includes(search)
            );
        }
        if (status) list = list.filter(r => r.status === status);
        if (area) list = list.filter(r => r.area === area);
        if (month) list = list.filter(r => (r.date || '').substring(0, 7) === month);

        const tbody = $('#tableBody');
        if (!list.length) {
            tbody.html(`<tr><td colspan="9"><div class="empty-state"><i class="ri-file-list-3-line"></i>Tidak ada data laporan</div></td></tr>`);
            return;
        }

        const statusBadge = s => ({
            open:     '<span class="badge-status badge-open">Open</span>',
            progress: '<span class="badge-status badge-progress">Progress</span>',
            done:     '<span class="badge-status badge-done">Done</span>',
            onhold:   '<span class="badge-status badge-onhold">On Hold</span>',
        })[s] || `<span class="badge-status badge-open">${s}</span>`;

        let html = '';
        list.forEach(function(r, i) {
            const dur = calcDuration(r.timeStart, r.timeEnd);
            const photoCount = (r.photos || []).length;
            const thumbHtml = photoCount > 0
                ? `<img src="${r.photos[0].thumb}" style="width:32px;height:32px;border-radius:6px;object-fit:cover;border:1px solid var(--vz-border-color);" title="${photoCount} foto">`
                : '<span class="text-muted">—</span>';

            const children = reports.filter(x => x.parentId == r.id);
            const updateBadge = children.length > 0
                ? `<span class="badge bg-info-subtle text-info ms-1" title="${children.length} update">${children.length} upd</span>`
                : '';

            const woBadge = r.workOrderNo
                ? `<br><span class="badge bg-primary-subtle text-primary mt-1" style="font-size:10px; font-family:monospace;">WO: ${r.workOrderNo}</span>`
                : '';

            html += `<tr data-id="${r.id}">
                <td>${i + 1}</td>
                <td class="fw-semibold">${formatDate(r.date)}</td>
                <td>${r.tech || '—'}</td>
                <td><span class="badge bg-light text-dark">${r.area || '—'}</span></td>
                <td style="max-width:240px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${escHtml(r.adhocTitle || r.work || '')}">
                    ${r.isAdhoc ? '<i class="ri-flashlight-line text-warning me-1" title="Ad-hoc"></i>' : ''}
                    ${escHtml(r.adhocTitle || r.work || '')}
                    ${updateBadge}
                    ${woBadge}
                </td>
                <td class="text-nowrap">
                    <small>${r.timeStart || '—'} – ${r.timeEnd || '—'}</small>
                    ${dur ? `<br><small class="text-muted">⏱ ${dur}</small>` : ''}
                </td>
                <td>${statusBadge(r.status)}</td>
                <td>${thumbHtml}</td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        <button class="btn btn-sm btn-outline-info btn-detail" data-id="${r.id}" title="Detail / Timeline">
                            <i class="ri-eye-line"></i>
                        </button>
                        @if(in_array(Auth::user()->jabatan, ['operator', 'foreman', 'admin']))
                        <button class="btn btn-sm btn-outline-warning btn-edit" data-id="${r.id}" title="Edit Laporan">
                            <i class="ri-edit-line"></i>
                        </button>
                        ${r.status !== 'done' ?
                            `<button class="btn btn-sm btn-outline-primary btn-update" data-id="${r.id}" title="Update Lanjutan">
                                <i class="ri-arrow-go-forward-line"></i>
                            </button>` : ''
                        }
                        @endif
                    </div>
                </td>
            </tr>`;
        });
        tbody.html(html);
    }

    // Filter triggers
    $('#btnFilter').on('click', renderTable);
    $('#filterStatus, #filterArea, #filterMonth').on('change', renderTable);
    let searchTimer;
    $('#filterSearch').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(renderTable, 300);
    });
    $('#btnResetFilter').on('click', function() {
        $('#filterSearch').val('');
        $('#filterStatus').val('');
        $('#filterArea').val('');
        $('#filterMonth').val("{{ date('Y-m') }}");
        renderTable();
    });

    // Detail Action
    $(document).on('click', '.btn-detail', function(e) {
        e.stopPropagation();
        openDetail($(this).data('id'));
    });
    $(document).on('click', '#tableReports tbody tr', function(e) {
        if ($(e.target).closest('button').length) return;
        const id = $(this).data('id');
        if (id) openDetail(id);
    });

    // Edit Action (Redirects to form page with edit parameter)
    $(document).on('click', '.btn-edit', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        window.location.href = "{{ route('epr.pm.form') }}?edit=" + id;
    });

    // Update Action (Redirects to form page with parent parameter)
    $(document).on('click', '.btn-update', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        window.location.href = "{{ route('epr.pm.form') }}?parent=" + id;
    });

    function openDetail(id) {
        const r = reports.find(x => x.id == id);
        if (!r) return;

        const SL = { open: 'Open', progress: 'In Progress', done: 'Done', onhold: 'On Hold' };
        const SC = { open: 'badge-open', progress: 'badge-progress', done: 'badge-done', onhold: 'badge-onhold' };

        // Collect updates
        const updates = reports.filter(u => u.parentId == id).sort((a, b) => a.createdAt.localeCompare(b.createdAt));
        const allReps = [r, ...updates];

        // Total duration
        let totalMin = 0;
        allReps.forEach(rep => {
            if (rep.timeStart && rep.timeEnd) {
                const [sh, sm] = rep.timeStart.split(':').map(Number);
                const [eh, em] = rep.timeEnd.split(':').map(Number);
                totalMin += Math.max(0, (eh * 60 + em) - (sh * 60 + sm));
            }
        });
        const totalDur = totalMin > 0 ? fmtMin(totalMin) : '—';
        const totalPhotos = allReps.reduce((a, rep) => a + (rep.photos || []).length, 0);

        $('#detail-title').text((r.adhocTitle || r.work || 'Laporan').substring(0, 50));

        let html = '';

        // Info cards
        html += `<div class="row g-3 mb-4">
            <div class="col-4"><div class="text-center p-3 bg-light rounded"><div class="fw-bold fs-18">${allReps.length}</div><div class="text-muted small">Update</div></div></div>
            <div class="col-4"><div class="text-center p-3 bg-light rounded"><div class="fw-bold fs-18">${totalPhotos}</div><div class="text-muted small">Foto</div></div></div>
            <div class="col-4"><div class="text-center p-3 bg-light rounded"><div class="fw-bold fs-18">${totalDur}</div><div class="text-muted small">Total Waktu</div></div></div>
        </div>`;

        // Info table
        html += `<div class="p-3 bg-light rounded border-start border-primary border-3 mb-4">
            <table class="table table-sm table-borderless mb-0">
                <tr><td class="text-muted" width="35%">Teknisi</td><td>: <strong>${r.tech || '—'}</strong></td></tr>
                <tr><td class="text-muted">Area</td><td>: ${r.area || '—'}</td></tr>
                <tr><td class="text-muted">Work Order</td><td>: ${r.workOrderNo ? '<strong style="font-family:monospace;">' + r.workOrderNo + '</strong>' : '<span class="text-muted">Pekerjaan Mandiri</span>'}</td></tr>
                <tr><td class="text-muted">Status</td><td>: <span class="badge-status ${SC[r.status] || 'badge-open'}">${SL[r.status] || r.status}</span>
                    ${r.isAdhoc ? ' <span class="badge-status badge-adhoc">⚡ Ad-hoc</span>' : ''}</td></tr>
            </table>
        </div>`;

        // Timeline
        html += `<h6 class="fw-bold mb-3"><i class="ri-time-line text-info me-2"></i>Timeline Laporan</h6>`;
        html += `<div class="timeline-line">`;
        allReps.forEach((rep, idx) => {
            const label = idx === 0 ? '📋 Laporan Awal' : `🔄 Update ${idx}`;
            const dur = calcDuration(rep.timeStart, rep.timeEnd);
            const dotClass = rep.status === 'done' ? 'done' : rep.status === 'onhold' ? 'onhold' : '';

            html += `<div class="timeline-item ${dotClass}">
                <div class="text-muted small mb-1">${label} · ${formatDate(rep.date)} · ⏰ ${rep.timeStart || '—'} – ${rep.timeEnd || '—'} ${dur ? '(' + dur + ')' : ''}</div>
                <div class="mb-1"><span class="badge-status ${SC[rep.status] || 'badge-open'}">${SL[rep.status] || rep.status}</span></div>
                <div>${rep.work || '—'}</div>
                ${rep.notes ? `<div class="mt-1 small text-muted bg-white p-2 rounded">💬 ${rep.notes}</div>` : ''}
                ${(rep.photos && rep.photos.length) ? `<div class="d-flex gap-2 mt-2 flex-wrap">${rep.photos.map((p, pi) =>
                    `<img src="${p.thumb || p.url}" style="width:56px;height:56px;border-radius:8px;object-fit:cover;border:1px solid var(--vz-border-color);cursor:pointer;" onclick="openLightboxUrls([${rep.photos.map(x => `'${x.url}'`).join(',')}],${pi})">`
                ).join('')}</div>` : ''}
            </div>`;
        });
        html += `</div>`;

        $('#detail-body').html(html);

        // Footer buttons
        let footerHtml = '<button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>';
        @if(in_array(Auth::user()->jabatan, ['operator', 'foreman', 'admin']))
            if (r.status !== 'done') {
                footerHtml = `<a href="{{ route('epr.pm.form') }}?parent=${r.id}" class="btn btn-primary">
                    <i class="ri-arrow-go-forward-line me-1"></i> Update Lanjutan
                </a>` + footerHtml;
            }
            footerHtml = `<a href="{{ route('epr.pm.form') }}?edit=${r.id}" class="btn btn-outline-warning">
                <i class="ri-edit-line me-1"></i> Edit
            </a>` + footerHtml;
        @endif
        $('#detail-footer').html(footerHtml);

        $('#modalDetail').modal('show');
    }

    // Lightbox handlers
    window.openLightboxUrls = function(urls, idx) {
        lbImages = urls;
        lbIdx = idx;
        $('#lbImg').attr('src', urls[idx]);
        $('#lightbox').css('display', 'flex');
    };

    window.closeLightbox = function() {
        $('#lightbox').css('display', 'none');
    };

    window.lbNav = function(dir, e) {
        e.stopPropagation();
        lbIdx = (lbIdx + dir + lbImages.length) % lbImages.length;
        $('#lbImg').attr('src', lbImages[lbIdx]);
    };

    function calcDuration(start, end) {
        if (!start || !end) return '';
        const [sh, sm] = start.split(':').map(Number);
        const [eh, em] = end.split(':').map(Number);
        const mnt = Math.max(0, (eh * 60 + em) - (sh * 60 + sm));
        return mnt > 0 ? fmtMin(mnt) : '';
    }

    function fmtMin(mnt) {
        const h = Math.floor(mnt / 60), m = mnt % 60;
        return (h > 0 ? h + 'j ' : '') + (m > 0 ? m + 'm' : '');
    }

    function formatDate(d) {
        if (!d) return '—';
        try { return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }); }
        catch(e) { return d; }
    }

    function escHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
</script>
@endsection
