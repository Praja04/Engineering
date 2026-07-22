@extends('layouts.app')

@section('title', 'Approval Work Order')

@section('styles')
<style>
    .card-header-custom {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        color: white;
        padding: 1.25rem;
        border-radius: 12px 12px 0 0;
    }

    .badge-status { font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; letter-spacing: .4px; text-transform: uppercase; }
    .badge-done     { background: rgba(34,197,94,.15);   color: #22c55e; }
    .badge-approved { background: rgba(16,185,129,.15);  color: #10b981; }
    .badge-rejected { background: rgba(239,68,68,.15);   color: #ef4444; }

    .badge-prio { font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: .5px; }
    .prio-critical { background: #ef4444; color: #fff; }
    .prio-high     { background: #f59e0b; color: #fff; }
    .prio-medium   { background: #3b82f6; color: #fff; }
    .prio-low      { background: #94a3b8; color: #fff; }

    .stat-card { border-radius: 12px; transition: transform .2s; }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }

    .filter-bar { background: var(--vz-card-bg); border: 1px solid var(--vz-border-color); border-radius: 10px; padding: 16px 20px; margin-bottom: 20px; }

    #tableApproval thead th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--vz-text-muted); white-space: nowrap; }
    #tableApproval tbody tr { cursor: pointer; transition: background .15s; }
    #tableApproval tbody tr:hover { background: rgba(16,185,129,.04) !important; }

    .empty-state { text-align: center; padding: 60px 20px; color: var(--vz-text-muted); }
    .empty-state i { font-size: 48px; display: block; margin-bottom: 12px; opacity: .4; }

    .assignee-tag { display: inline-flex; align-items: center; gap: 4px; background: rgba(99,102,241,.1); color: #6366f1; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
</style>
@endsection

@section('content')
<div class="page-content">
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="ri-checkbox-circle-line me-2 text-success"></i>
                Approval Work Order
            </h4>
            <p class="text-muted mb-0 fs-13">Tinjau dan setujui laporan penyelesaian Work Order dari operator</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        @php
            $stats = [
                ['label' => 'Menunggu Approval',  'id' => 'st-pending',   'icon' => 'ri-time-line',            'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,.12)'],
                ['label' => 'Approved',          'id' => 'st-approved',  'icon' => 'ri-checkbox-circle-line', 'color' => '#22c55e', 'bg' => 'rgba(34,197,94,.12)'],
                ['label' => 'Rejected',          'id' => 'st-rejected',  'icon' => 'ri-close-circle-line',    'color' => '#ef4444', 'bg' => 'rgba(239,68,68,.12)'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="col-md-4">
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

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header-custom">
            <h5 class="mb-1 text-white fw-bold"><i class="ri-checkbox-circle-line me-2 text-warning"></i>Persetujuan Work Order</h5>
            <p class="mb-0 text-white-50 small">Daftar laporan penyelesaian pekerjaan</p>
        </div>
        <div class="card-body p-4">
            {{-- Filter --}}
            <div class="filter-bar">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fs-12 fw-semibold mb-1">Cari</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                            <input type="text" id="filterSearch" class="form-control" placeholder="No WO, judul, area, mesin...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-12 fw-semibold mb-1">Status Approval</label>
                        <select id="filterStatus" class="form-select form-select-sm">
                            <option value="done">Menunggu Approval</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="">Semua Riwayat</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-12 fw-semibold mb-1">Prioritas</label>
                        <select id="filterPriority" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            <option value="critical">Critical</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-1">
                        <button class="btn btn-primary btn-sm flex-fill" onclick="loadApprovalList()">
                            <i class="ri-filter-3-line me-1"></i> Filter
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">
                            <i class="ri-refresh-line"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tableApproval" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>No WO</th>
                            <th>Judul</th>
                            <th>Area / Mesin</th>
                            <th>Prioritas</th>
                            <th>Target</th>
                            <th>Operator</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="8" class="text-center py-5 text-muted">
                            <div class="spinner-border text-primary spinner-border-sm me-2"></div> Memuat...
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</div>

{{-- ═══ Modal Detail & Action Approval ═══ --}}
<div class="modal fade" id="modalApproval" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom p-3">
                <h5 class="modal-title fw-bold" id="detail-title">
                    <i class="ri-checkbox-circle-line text-success me-2"></i> Tinjau Work Order
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="detail-info"></div>

                <div id="approvalActionBlock" class="d-none mt-4 p-3 bg-light rounded border">
                    <h6 class="fw-bold mb-3"><i class="ri-shield-user-line text-warning me-2"></i>Keputusan Supervisor</h6>
                    <div class="row g-2">
                        <div class="col-12 mb-2">
                            <label class="form-label small fw-bold">Alasan Ditolak / Catatan Revisi <span class="text-muted fw-normal">(Hanya wajib jika ditolak)</span></label>
                            <textarea class="form-control form-control-sm" id="rejectReason" rows="2" placeholder="Tuliskan feedback perbaikan jika WO ditolak..."></textarea>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-danger w-100 py-2" id="btnReject" onclick="processReject()">
                                <i class="ri-close-circle-line me-1"></i> Tolak / Butuh Revisi
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-success w-100 py-2" id="btnApprove" onclick="processApprove()">
                                <i class="ri-checkbox-circle-line me-1"></i> Approve Selesai
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top p-3">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    let workOrders = [];
    let currentWoId = null;

    loadApprovalList();

    function loadApprovalList() {
        const params = {
            status: $('#filterStatus').val(),
            priority: $('#filterPriority').val(),
        };
        $.get("{{ route('epr.wo.json') }}", params, function(data) {
            workOrders = data;
            renderTable();
            renderStats();
        });
    }

    window.resetFilters = function() {
        $('#filterSearch').val('');
        $('#filterStatus').val('done');
        $('#filterPriority').val('');
        loadApprovalList();
    };

    function renderStats() {
        // Stats using all loaded records
        $('#st-pending').text(workOrders.filter(w => w.status === 'done').length);
        $('#st-approved').text(workOrders.filter(w => w.status === 'approved').length);
        $('#st-rejected').text(workOrders.filter(w => w.status === 'rejected').length);
    }

    function renderTable() {
        const search = ($('#filterSearch').val() || '').toLowerCase();
        let list = workOrders;

        if (search) {
            list = list.filter(w =>
                w.wo_number.toLowerCase().includes(search) ||
                w.title.toLowerCase().includes(search) ||
                (w.machine || '').toLowerCase().includes(search) ||
                w.area.toLowerCase().includes(search)
            );
        }

        const tbody = $('#tableBody');
        if (!list.length) {
            tbody.html(`<tr><td colspan="8"><div class="empty-state"><i class="ri-checkbox-circle-line"></i>Tidak ada Work Order untuk diapprove</div></td></tr>`);
            return;
        }

        const statusBadge = s => ({
            done:     '<span class="badge-status badge-done">Done</span>',
            approved: '<span class="badge-status badge-approved">Approved</span>',
            rejected: '<span class="badge-status badge-rejected">Rejected</span>',
        })[s] || s;

        const prioBadge = p => ({
            critical: '<span class="badge-prio prio-critical">Critical</span>',
            high:     '<span class="badge-prio prio-high">High</span>',
            medium:   '<span class="badge-prio prio-medium">Medium</span>',
            low:      '<span class="badge-prio prio-low">Low</span>',
        })[p] || p;

        let html = '';
        list.forEach(function(wo) {
            const ops = wo.assignees.map(a => a.username).join(', ') || '—';
            html += `<tr data-id="${wo.id}">
                <td><span class="fw-bold text-primary" style="font-family:monospace; font-size:12px;">${wo.wo_number}</span></td>
                <td><div style="max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${esc(wo.title)}">${esc(wo.title)}</div></td>
                <td><span class="badge bg-light text-dark">${wo.area}</span>${wo.machine ? `<br><small class="text-muted">${esc(wo.machine)}</small>` : ''}</td>
                <td>${prioBadge(wo.priority)}</td>
                <td>${wo.target_date ? formatDate(wo.target_date) : '—'}</td>
                <td><small>${esc(ops)}</small></td>
                <td>${statusBadge(wo.status)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-primary px-3 btn-review" data-id="${wo.id}">
                        <i class="ri-eye-line me-1"></i> Tinjau
                    </button>
                </td>
            </tr>`;
        });
        tbody.html(html);
    }

    $(document).on('click', '.btn-review, #tableApproval tbody tr', function(e) {
        if ($(e.target).closest('button').length && !$(e.target).closest('.btn-review').length) return;
        const id = $(this).data('id') || $(this).closest('tr').data('id');
        if (id) openApprovalModal(id);
    });

    function openApprovalModal(id) {
        currentWoId = id;
        const wo = workOrders.find(w => w.id == id);
        if (!wo) return;

        const SB = s => ({
            done:     '<span class="badge-status badge-done">Done</span>',
            approved: '<span class="badge-status badge-approved">Approved</span>',
            rejected: '<span class="badge-status badge-rejected">Rejected</span>',
        })[s] || s;

        const PB = p => ({
            critical: '<span class="badge-prio prio-critical">Critical</span>',
            high:     '<span class="badge-prio prio-high">High</span>',
            medium:   '<span class="badge-prio prio-medium">Medium</span>',
            low:      '<span class="badge-prio prio-low">Low</span>',
        })[p] || p;

        let html = '';
        html += `<div class="p-3 bg-light rounded border-start border-primary border-3 mb-4">
            <table class="table table-sm table-borderless mb-0">
                <tr><td class="text-muted" width="30%">No WO</td><td>: <strong style="font-family:monospace;">${wo.wo_number}</strong></td></tr>
                <tr><td class="text-muted">Judul</td><td>: <strong>${esc(wo.title)}</strong></td></tr>
                <tr><td class="text-muted">Area / Mesin</td><td>: ${wo.area}${wo.machine ? ' / ' + esc(wo.machine) : ''}</td></tr>
                <tr><td class="text-muted">Prioritas</td><td>: ${PB(wo.priority)}</td></tr>
                <tr><td class="text-muted">Status</td><td>: ${SB(wo.status)}</td></tr>
                <tr><td class="text-muted">Target</td><td>: ${wo.target_date ? formatDate(wo.target_date) : '—'}</td></tr>
                <tr><td class="text-muted">Dibuat oleh</td><td>: ${wo.created_by} · ${formatDate(wo.created_at)}</td></tr>
            </table>
        </div>`;

        if (wo.description) {
            html += `<div class="mb-4"><h6 class="fw-bold mb-2">Deskripsi WO</h6><p class="mb-0">${esc(wo.description)}</p></div>`;
        }

        html += `<h6 class="fw-bold mb-3"><i class="ri-team-line text-info me-2"></i>Operator Ditugaskan</h6>`;
        html += `<div class="d-flex flex-wrap gap-2 mb-4">`;
        wo.assignees.forEach(a => {
            let label = a.username;
            if (a.duration) label += ` · ${a.duration}m`;
            if (a.note) label += ` · ${a.note}`;
            html += `<div class="assignee-tag">${esc(label)}</div>`;
        });
        html += `</div>`;

        html += `<h6 class="fw-bold mb-2"><i class="ri-article-line text-success me-2"></i>Laporan Terkait (${wo.report_count})</h6>`;
        if (wo.report_count > 0) {
            html += `<div class="mb-3"><a href="{{ route('epr.pm.data') }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="ri-external-link-line me-1"></i> Lihat Laporan Detail & Foto Dokumentasi</a></div>`;
        } else {
            html += `<p class="text-muted small">Belum ada laporan dari operator.</p>`;
        }

        $('#detail-info').html(html);

        if (wo.status === 'done') {
            $('#rejectReason').val('');
            $('#approvalActionBlock').removeClass('d-none');
        } else {
            $('#approvalActionBlock').addClass('d-none');
        }

        $('#modalApproval').modal('show');
    }

    window.processApprove = function() {
        Swal.fire({
            title: 'Approve Work Order?',
            text: 'Ini menyatakan pekerjaan telah selesai diverifikasi',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            confirmButtonText: 'Ya, Approve',
            cancelButtonText: 'Batal',
        }).then(res => {
            if (res.isConfirmed) {
                $.post(`/epr/work-orders/${currentWoId}/approve`, { _token: '{{ csrf_token() }}' }, function(ret) {
                    if (ret.success) {
                        Swal.fire({ icon: 'success', title: 'Approved!', timer: 1500, showConfirmButton: false });
                        $('#modalApproval').modal('hide');
                        loadApprovalList();
                    } else {
                        Swal.fire('Error', ret.message, 'error');
                    }
                });
            }
        });
    };

    window.processReject = function() {
        const reason = $('#rejectReason').val().trim();
        if (!reason) {
            Swal.fire('Validasi', 'Tuliskan alasan penolakan/catatan revisi', 'warning');
            return;
        }

        $.post(`/epr/work-orders/${currentWoId}/reject`, { _token: '{{ csrf_token() }}', reason: reason }, function(ret) {
            if (ret.success) {
                Swal.fire({ icon: 'error', title: 'WO Ditolak / Direvisi', timer: 1500, showConfirmButton: false });
                $('#modalApproval').modal('hide');
                loadApprovalList();
            } else {
                Swal.fire('Error', ret.message, 'error');
            }
        });
    };

    function formatDate(d) {
        if (!d) return '—';
        try { return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }); }
        catch(e) { return d; }
    }

    function esc(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
</script>
@endsection
