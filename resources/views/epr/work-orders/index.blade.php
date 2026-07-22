@extends('layouts.app')

@section('title', 'Management Work Order')

@section('styles')
<style>
    .card-header-custom {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        color: white;
        padding: 1.25rem;
        border-radius: 12px 12px 0 0;
    }

    .badge-status { font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; letter-spacing: .4px; text-transform: uppercase; }
    .badge-open     { background: rgba(59,130,246,.15);  color: #3b82f6; }
    .badge-assigned { background: rgba(139,92,246,.15);  color: #8b5cf6; }
    .badge-progress { background: rgba(245,158,11,.15);  color: #f59e0b; }
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

    #tableWo thead th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--vz-text-muted); white-space: nowrap; }
    #tableWo tbody tr { cursor: pointer; transition: background .15s; }
    #tableWo tbody tr:hover { background: rgba(139,92,246,.04) !important; }

    .empty-state { text-align: center; padding: 60px 20px; color: var(--vz-text-muted); }
    .empty-state i { font-size: 48px; display: block; margin-bottom: 12px; opacity: .4; }

    .assignee-tag { display: inline-flex; align-items: center; gap: 4px; background: rgba(99,102,241,.1); color: #6366f1; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .assignee-tag .remove-assignee { cursor: pointer; font-size: 14px; margin-left: 2px; }
</style>
@endsection

@section('content')
<div class="page-content">
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="ri-task-line me-2 text-primary"></i>
                Management Work Order
            </h4>
            <p class="text-muted mb-0 fs-13">Buat, kelola, dan assign Work Order ke operator</p>
        </div>
        <button class="btn btn-primary btn-sm d-flex align-items-center gap-2" onclick="openCreateModal()">
            <i class="ri-add-line"></i> Buat WO Baru
        </button>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        @php
            $stats = [
                ['label' => 'Total WO',  'id' => 'st-total',    'icon' => 'ri-file-list-3-line',    'color' => '#6366f1', 'bg' => 'rgba(99,102,241,.12)'],
                ['label' => 'Assigned',  'id' => 'st-assigned', 'icon' => 'ri-user-follow-line',    'color' => '#8b5cf6', 'bg' => 'rgba(139,92,246,.12)'],
                ['label' => 'Progress',  'id' => 'st-progress', 'icon' => 'ri-loader-4-line',       'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,.12)'],
                ['label' => 'Done',      'id' => 'st-done',     'icon' => 'ri-checkbox-circle-line','color' => '#22c55e', 'bg' => 'rgba(34,197,94,.12)'],
                ['label' => 'Approved',  'id' => 'st-approved', 'icon' => 'ri-shield-check-line',   'color' => '#10b981', 'bg' => 'rgba(16,185,129,.12)'],
            ];
        @endphp
        @foreach($stats as $s)
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

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 text-white fw-bold"><i class="ri-task-line me-2 text-warning"></i>Daftar Work Order</h5>
                <p class="mb-0 text-white-50 small">Kelola seluruh Work Order predictive maintenance</p>
            </div>
        </div>
        <div class="card-body p-4">
            {{-- Filter --}}
            <div class="filter-bar">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fs-12 fw-semibold mb-1">Cari</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                            <input type="text" id="filterSearch" class="form-control" placeholder="No WO, judul, mesin...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fs-12 fw-semibold mb-1">Status</label>
                        <select id="filterStatus" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            <option value="assigned">Assigned</option>
                            <option value="progress">Progress</option>
                            <option value="done">Done</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fs-12 fw-semibold mb-1">Prioritas</label>
                        <select id="filterPriority" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            <option value="critical">Critical</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fs-12 fw-semibold mb-1">Bulan</label>
                        <input type="month" id="filterMonth" class="form-control form-control-sm" value="{{ date('Y-m') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-1">
                        <button class="btn btn-primary btn-sm flex-fill" onclick="loadWorkOrders()">
                            <i class="ri-filter-3-line me-1"></i> Filter
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">
                            <i class="ri-refresh-line"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tableWo" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>No WO</th>
                            <th>Judul</th>
                            <th>Area / Mesin</th>
                            <th>Prioritas</th>
                            <th>Target</th>
                            <th>Operator</th>
                            <th>Laporan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="9" class="text-center py-5 text-muted">
                            <div class="spinner-border text-primary spinner-border-sm me-2"></div> Memuat...
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</div>

{{-- ═══ Modal Create/Edit WO ═══ --}}
<div class="modal fade" id="modalWo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom p-3">
                <h5 class="modal-title fw-bold" id="modalWoTitle">
                    <i class="ri-task-line text-primary me-2"></i> Buat Work Order Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formWo">
                    @csrf
                    <input type="hidden" id="wo-id">

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Judul Pekerjaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="wo-title" placeholder="cth: Ganti Bearing Motor Conveyor B3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Area / Unit <span class="text-danger">*</span></label>
                            <select class="form-select" id="wo-area" required>
                                <option value="">Pilih area...</option>
                                <option>Filling Retail</option>
                                <option>Packing Retail</option>
                                <option>Gravity Roller</option>
                                <option>Workshop</option>
                                <option>Pasteur</option>
                                <option>Storage</option>
                                <option>Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Mesin</label>
                            <input type="text" class="form-control" id="wo-machine" placeholder="cth: Conveyor B3, Pompa P-201...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Prioritas <span class="text-danger">*</span></label>
                            <select class="form-select" id="wo-priority" required>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Target Tanggal Selesai</label>
                            <input type="date" class="form-control" id="wo-target">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Deskripsi Detail</label>
                            <textarea class="form-control" id="wo-desc" rows="3" placeholder="Penjelasan detail pekerjaan yang harus dilakukan..."></textarea>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Assign Operator --}}
                    <h6 class="fw-bold mb-3"><i class="ri-team-line text-info me-2"></i>Assign Operator <span class="text-danger">*</span></h6>
                    <div class="row g-2 mb-3" id="assignRow">
                        <div class="col-md-5">
                            <select class="form-select form-select-sm" id="assignUser">
                                <option value="">Pilih operator...</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control form-control-sm" id="assignDuration" placeholder="Durasi (menit)" min="0">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control form-control-sm" id="assignNote" placeholder="Catatan...">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="addAssignee()">
                                <i class="ri-add-line"></i>
                            </button>
                        </div>
                    </div>
                    <div id="assigneeList" class="d-flex flex-wrap gap-2 mb-3">
                        {{-- Filled by JS --}}
                    </div>
                    <div class="text-danger small d-none" id="assignError">Minimal satu operator harus di-assign</div>
                </form>
            </div>
            <div class="modal-footer border-top p-3">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary px-4" id="btnSaveWo" onclick="saveWo()">
                    <i class="ri-save-line me-1"></i> Simpan WO
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ Modal Detail WO ═══ --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom p-3">
                <h5 class="modal-title fw-bold" id="detail-title">
                    <i class="ri-task-line text-primary me-2"></i> Detail WO
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detail-body"></div>
            <div class="modal-footer border-top p-3" id="detail-footer">
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
    let assignableUsers = { operators: [], foremen: [] };
    let assignees = []; // { user_id, username, duration, note }

    loadWorkOrders();
    loadUsers();

    // ═══ LOAD DATA ═══
    function loadWorkOrders() {
        const params = {
            search: $('#filterSearch').val(),
            status: $('#filterStatus').val(),
            priority: $('#filterPriority').val(),
            month: $('#filterMonth').val(),
        };
        $.get("{{ route('epr.wo.json') }}", params, function(data) {
            workOrders = data;
            renderTable();
            renderStats();
        });
    }

    function loadUsers() {
        $.get("{{ route('epr.wo.users') }}", function(data) {
            assignableUsers = data;
            renderUserDropdown();
        });
    }

    window.resetFilters = function() {
        $('#filterSearch').val('');
        $('#filterStatus').val('');
        $('#filterPriority').val('');
        $('#filterMonth').val("{{ date('Y-m') }}");
        loadWorkOrders();
    };

    // ═══ STATS ═══
    function renderStats() {
        $('#st-total').text(workOrders.length);
        $('#st-assigned').text(workOrders.filter(w => w.status === 'assigned').length);
        $('#st-progress').text(workOrders.filter(w => w.status === 'progress').length);
        $('#st-done').text(workOrders.filter(w => w.status === 'done').length);
        $('#st-approved').text(workOrders.filter(w => w.status === 'approved').length);
    }

    // ═══ TABLE ═══
    function renderTable() {
        const tbody = $('#tableBody');
        if (!workOrders.length) {
            tbody.html(`<tr><td colspan="9"><div class="empty-state"><i class="ri-task-line"></i>Belum ada Work Order</div></td></tr>`);
            return;
        }

        const statusBadge = s => ({
            open:     '<span class="badge-status badge-open">Open</span>',
            assigned: '<span class="badge-status badge-assigned">Assigned</span>',
            progress: '<span class="badge-status badge-progress">Progress</span>',
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
        workOrders.forEach(function(wo) {
            const ops = wo.assignees.map(a => a.username).join(', ') || '—';
            const targetStr = wo.target_date ? formatDate(wo.target_date) : '—';
            const isOverdue = wo.target_date && new Date(wo.target_date) < new Date() && !['done','approved'].includes(wo.status);

            html += `<tr data-id="${wo.id}">
                <td><span class="fw-bold text-primary" style="font-family:monospace; font-size:12px;">${wo.wo_number}</span></td>
                <td style="max-width:200px;" title="${esc(wo.title)}">
                    <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${esc(wo.title)}</div>
                </td>
                <td>
                    <span class="badge bg-light text-dark">${wo.area}</span>
                    ${wo.machine ? `<br><small class="text-muted">${esc(wo.machine)}</small>` : ''}
                </td>
                <td>${prioBadge(wo.priority)}</td>
                <td class="${isOverdue ? 'text-danger fw-bold' : ''}">${targetStr}${isOverdue ? ' ⚠️' : ''}</td>
                <td><small>${esc(ops)}</small></td>
                <td><span class="badge bg-info-subtle text-info">${wo.report_count}</span></td>
                <td>${statusBadge(wo.status)}</td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        <button class="btn btn-sm btn-outline-info btn-detail" data-id="${wo.id}" title="Detail">
                            <i class="ri-eye-line"></i>
                        </button>
                        ${['open','assigned','rejected'].includes(wo.status) ?
                            `<button class="btn btn-sm btn-outline-warning btn-edit" data-id="${wo.id}" title="Edit">
                                <i class="ri-edit-line"></i>
                            </button>` : ''
                        }
                        ${['open','assigned'].includes(wo.status) ?
                            `<button class="btn btn-sm btn-outline-danger btn-delete" data-id="${wo.id}" title="Hapus">
                                <i class="ri-delete-bin-line"></i>
                            </button>` : ''
                        }
                    </div>
                </td>
            </tr>`;
        });
        tbody.html(html);
    }

    // ═══ CREATE / EDIT MODAL ═══
    window.openCreateModal = function() {
        $('#wo-id').val('');
        $('#wo-title').val('');
        $('#wo-area').val('');
        $('#wo-machine').val('');
        $('#wo-priority').val('medium');
        $('#wo-target').val('');
        $('#wo-desc').val('');
        assignees = [];
        renderAssigneeList();
        $('#modalWoTitle').html('<i class="ri-task-line text-primary me-2"></i> Buat Work Order Baru');
        $('#modalWo').modal('show');
    };

    $(document).on('click', '.btn-edit', function(e) {
        e.stopPropagation();
        const wo = workOrders.find(w => w.id == $(this).data('id'));
        if (!wo) return;

        $('#wo-id').val(wo.id);
        $('#wo-title').val(wo.title);
        $('#wo-area').val(wo.area);
        $('#wo-machine').val(wo.machine || '');
        $('#wo-priority').val(wo.priority);
        $('#wo-target').val(wo.target_date || '');
        $('#wo-desc').val(wo.description || '');
        assignees = wo.assignees.map(a => ({
            user_id: a.user_id,
            username: a.username,
            duration: a.duration,
            note: a.note,
        }));
        renderAssigneeList();
        $('#modalWoTitle').html('<i class="ri-edit-line text-warning me-2"></i> Edit Work Order');
        $('#modalWo').modal('show');
    });

    // ═══ ASSIGNEES ═══
    function renderUserDropdown() {
        const sel = $('#assignUser');
        sel.empty().append('<option value="">Pilih operator...</option>');
        (assignableUsers.operators || []).forEach(u => {
            sel.append(`<option value="${u.id}">${u.username} (Operator)</option>`);
        });
        (assignableUsers.foremen || []).forEach(u => {
            sel.append(`<option value="${u.id}">${u.username} (Foreman)</option>`);
        });
    }

    window.addAssignee = function() {
        const userId = $('#assignUser').val();
        const username = $('#assignUser option:selected').text();
        if (!userId) return;
        if (assignees.find(a => a.user_id == userId)) {
            Swal.fire('Info', 'User sudah di-assign', 'info');
            return;
        }
        assignees.push({
            user_id: parseInt(userId),
            username: username,
            duration: parseInt($('#assignDuration').val()) || null,
            note: $('#assignNote').val() || null,
        });
        $('#assignUser').val('');
        $('#assignDuration').val('');
        $('#assignNote').val('');
        $('#assignError').addClass('d-none');
        renderAssigneeList();
    };

    function renderAssigneeList() {
        const list = $('#assigneeList');
        list.empty();
        assignees.forEach((a, i) => {
            let label = a.username;
            if (a.duration) label += ` · ${a.duration}m`;
            if (a.note) label += ` · ${a.note}`;
            list.append(`<div class="assignee-tag">${esc(label)} <span class="remove-assignee" onclick="removeAssignee(${i})">✕</span></div>`);
        });
    }

    window.removeAssignee = function(idx) {
        assignees.splice(idx, 1);
        renderAssigneeList();
    };

    // ═══ SAVE WO ═══
    window.saveWo = function() {
        // Auto-add assignee if user selected one but forgot to click "+" button
        const userId = $('#assignUser').val();
        if (userId) {
            if (!assignees.find(a => a.user_id == userId)) {
                assignees.push({
                    user_id: parseInt(userId),
                    username: $('#assignUser option:selected').text(),
                    duration: parseInt($('#assignDuration').val()) || null,
                    note: $('#assignNote').val() || null,
                });
                $('#assignUser').val('');
                $('#assignDuration').val('');
                $('#assignNote').val('');
                $('#assignError').addClass('d-none');
                renderAssigneeList();
            }
        }

        if (!$('#wo-title').val().trim() || !$('#wo-area').val()) {
            Swal.fire('Validasi', 'Judul dan Area wajib diisi', 'warning');
            return;
        }
        if (!assignees.length) {
            $('#assignError').removeClass('d-none');
            return;
        }

        const btn = $('#btnSaveWo');
        btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm me-2"></div> Menyimpan...');

        $.ajax({
            url: "{{ route('epr.wo.store') }}",
            method: 'POST',
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: JSON.stringify({
                id: $('#wo-id').val() || null,
                title: $('#wo-title').val().trim(),
                description: $('#wo-desc').val(),
                area: $('#wo-area').val(),
                machine: $('#wo-machine').val(),
                priority: $('#wo-priority').val(),
                target_date: $('#wo-target').val() || null,
                assignees: assignees.map(a => ({
                    user_id: a.user_id,
                    duration: a.duration,
                    note: a.note,
                })),
            }),
            success: function(res) {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Work Order berhasil disimpan', timer: 2000, showConfirmButton: false });
                    $('#modalWo').modal('hide');
                    loadWorkOrders();
                } else {
                    Swal.fire('Error', res.message || 'Gagal menyimpan', 'error');
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Terjadi kesalahan server';
                Swal.fire('Error', msg, 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> Simpan WO');
            }
        });
    };

    // ═══ DELETE ═══
    $(document).on('click', '.btn-delete', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Work Order?',
            text: 'WO yang dihapus tidak bisa dikembalikan',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/epr/work-orders/${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function() {
                        Swal.fire({ icon: 'success', title: 'Dihapus!', timer: 1500, showConfirmButton: false });
                        loadWorkOrders();
                    }
                });
            }
        });
    });

    // ═══ DETAIL ═══
    $(document).on('click', '.btn-detail', function(e) {
        e.stopPropagation();
        openDetail($(this).data('id'));
    });
    $(document).on('click', '#tableWo tbody tr', function(e) {
        if ($(e.target).closest('button').length) return;
        const id = $(this).data('id');
        if (id) openDetail(id);
    });

    function openDetail(id) {
        const wo = workOrders.find(w => w.id == id);
        if (!wo) return;

        const SB = s => ({
            open: '<span class="badge-status badge-open">Open</span>',
            assigned: '<span class="badge-status badge-assigned">Assigned</span>',
            progress: '<span class="badge-status badge-progress">Progress</span>',
            done: '<span class="badge-status badge-done">Done</span>',
            approved: '<span class="badge-status badge-approved">Approved</span>',
            rejected: '<span class="badge-status badge-rejected">Rejected</span>',
        })[s] || s;

        const PB = p => ({
            critical: '<span class="badge-prio prio-critical">Critical</span>',
            high: '<span class="badge-prio prio-high">High</span>',
            medium: '<span class="badge-prio prio-medium">Medium</span>',
            low: '<span class="badge-prio prio-low">Low</span>',
        })[p] || p;

        $('#detail-title').html(`<i class="ri-task-line text-primary me-2"></i>${wo.wo_number}`);

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
                ${wo.approved_by ? `<tr><td class="text-muted">${wo.status === 'rejected' ? 'Ditolak' : 'Diapprove'} oleh</td><td>: ${wo.approved_by} · ${wo.approved_at || ''}</td></tr>` : ''}
                ${wo.reject_reason ? `<tr><td class="text-muted">Alasan Reject</td><td>: <span class="text-danger">${esc(wo.reject_reason)}</span></td></tr>` : ''}
            </table>
        </div>`;

        if (wo.description) {
            html += `<div class="mb-4"><h6 class="fw-bold mb-2">Deskripsi</h6><p class="mb-0">${esc(wo.description)}</p></div>`;
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
            html += `<a href="{{ route('epr.pm.data') }}" class="btn btn-outline-primary btn-sm"><i class="ri-external-link-line me-1"></i> Lihat di Predictive Data</a>`;
        } else {
            html += `<p class="text-muted small">Belum ada laporan dari operator.</p>`;
        }

        $('#detail-body').html(html);
        $('#detail-footer').html('<button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>');
        $('#modalDetail').modal('show');
    }

    // ═══ HELPERS ═══
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
