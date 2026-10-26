@extends('layouts.app')

@section('title', 'Master Checklist Maintenance')

@section('styles')
    <style>
        :root {
            --ink: #0f172a;
            --ink-soft: #475569;
            --ink-muted: #94a3b8;
            --surface: #ffffff;
            --surface-2: #f8fafc;
            --surface-3: #f1f5f9;
            --border: #e2e8f0;
            --accent: #2563eb;
            --accent-hov: #1d4ed8;
            --accent-bg: #eff6ff;
            --success: #16a34a;
            --success-bg: #f0fdf4;
            --danger: #dc2626;
            --danger-bg: #fef2f2;
            --warning: #d97706;
            --warning-bg: #fffbeb;
            --radius: 10px;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, .07), 0 1px 2px rgba(15, 23, 42, .04);
            --shadow: 0 4px 12px rgba(15, 23, 42, .08), 0 2px 4px rgba(15, 23, 42, .05);
            --transition: .18s cubic-bezier(.4, 0, .2, 1);
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px 20px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 14px;
            transition: box-shadow var(--transition), transform var(--transition);
        }

        .stat-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-1px);
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-icon.blue { background: var(--accent-bg); color: var(--accent); }
        .stat-icon.green { background: var(--success-bg); color: var(--success); }
        .stat-icon.amber { background: var(--warning-bg); color: var(--warning); }
        .stat-icon.purple { background: #f3e8ff; color: #7e22ce; }

        .stat-value {
            font-size: 22px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--ink-muted);
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-top: 4px;
        }

        .filter-panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }

        .table-custom tbody tr:hover {
            background-color: var(--surface-2);
        }

        .search-box-wrapper {
            position: relative;
        }

        .search-box-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ink-muted);
        }

        .search-box-wrapper input {
            padding-left: 36px;
        }
    </style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h4 class="mb-1 fw-bold text-dark">Master Checklist Maintenance</h4>
                <p class="text-muted mb-0 small">Kelola parameter inspeksi & formulir checklist pemeliharaan secara dinamis</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAddGroup">
                    <i class="mdi mdi-folder-plus-outline me-1"></i> Tambah Group
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalChecklist" id="btnOpenAddModal">
                    <i class="mdi mdi-plus me-1"></i> Tambah Item Checklist
                </button>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-total">0</div>
                    <div class="stat-label">Total Checklist</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="mdi mdi-check-circle"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-active">0</div>
                    <div class="stat-label">Checklist Aktif</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber">
                    <i class="mdi mdi-folder-multiple-outline"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-groups">0</div>
                    <div class="stat-label">Total Group / Kategori</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="mdi mdi-pause-circle"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-inactive">0</div>
                    <div class="stat-label">Checklist Nonaktif</div>
                </div>
            </div>
        </div>

        {{-- Filter Panel --}}
        <div class="filter-panel">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Cari Checklist</label>
                    <div class="search-box-wrapper">
                        <i class="mdi mdi-magnify"></i>
                        <input type="text" id="filter-search" class="form-control form-control-sm" placeholder="Ketik kata kunci...">
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Jenis Maintenance</label>
                    <select id="filter-jenis" class="form-select form-select-sm">
                        <option value="">-- Semua Jenis Maintenance --</option>
                        @foreach ($jenisList as $jenis)
                            <option value="{{ $jenis }}">{{ $jenis }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Group / Kategori</label>
                    <select id="filter-group" class="form-select form-select-sm">
                        <option value="">-- Semua Group --</option>
                        @foreach ($groups as $grp)
                            <option value="{{ $grp->id }}" data-jenis="{{ $grp->jenis_mtc }}">{{ $grp->nama_group }} ({{ $grp->jenis_mtc }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                    <select id="filter-status" class="form-select form-select-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-1 col-sm-6">
                    <button type="button" class="btn btn-sm btn-light border w-100" id="btn-reset-filter" title="Reset Filter">
                        <i class="mdi mdi-refresh"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom table-hover align-middle table-sm w-100 mb-0" id="tableChecklist">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center py-3">No</th>
                                <th class="py-3">Jenis Maintenance</th>
                                <th class="py-3">Group / Bagian</th>
                                <th class="py-3">Item Checklist</th>
                                <th class="py-3">Standar / Panduan</th>
                                <th style="width: 110px;" class="text-center py-3">Tipe Input</th>
                                <th style="width: 70px;" class="text-center py-3">Urutan</th>
                                <th style="width: 90px;" class="text-center py-3">Status</th>
                                <th style="width: 120px;" class="text-center py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyChecklist">
                            {{-- Loaded dynamically via jQuery AJAX --}}
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    Memuat data checklist...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- Table Footer / Counter --}}
                <div class="d-flex align-items-center justify-content-between p-3 border-top bg-light">
                    <small class="text-muted" id="table-info">Menampilkan 0 item checklist</small>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-refresh-table">
                        <i class="mdi mdi-refresh me-1"></i> Segarkan Data
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- MODAL ADD / EDIT CHECKLIST --}}
<div class="modal fade" id="modalChecklist" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formChecklist">
                @csrf
                <input type="hidden" name="checklist_id" id="checklist_id">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalChecklistTitle">Tambah Item Checklist</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Maintenance <span class="text-danger">*</span></label>
                        <select class="form-select" name="jenis_mtc" id="modal_jenis_mtc" required>
                            <option value="" disabled selected>-- Pilih Jenis Maintenance --</option>
                            @foreach ($jenisList as $jenis)
                                <option value="{{ $jenis }}">{{ $jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Group / Bagian <span class="text-danger">*</span></label>
                        <select class="form-select" name="group_id" id="modal_group_id" required>
                            <option value="" disabled selected>-- Pilih Group --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Item Checklist <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="label" id="modal_label" placeholder="Contoh: Kebocoran Oli, Putaran Motor, dsb." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Standar / Panduan Pengecekan</label>
                        <textarea class="form-control" name="standar" id="modal_standar" rows="2" placeholder="Contoh: Tidak ada getaran abnormal atau suara berdecit"></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Tipe Input <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipe_input" id="modal_tipe_input" required>
                                <option value="boolean_ok_ng">OK / Tidak OK (Radio)</option>
                                <option value="number">Angka / Pengukuran (Number)</option>
                                <option value="text">Teks Deskripsi (Text)</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Urutan Tampil</label>
                            <input type="number" class="form-control" name="urutan" id="modal_urutan" value="1" min="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveChecklist">
                        <i class="mdi mdi-content-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL ADD GROUP --}}
<div class="modal fade" id="modalAddGroup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formAddGroup">
                @csrf
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold">Tambah Group Checklist Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Maintenance <span class="text-danger">*</span></label>
                        <select class="form-select" name="jenis_mtc" id="group_jenis_mtc" required>
                            <option value="" disabled selected>-- Pilih Jenis Maintenance --</option>
                            @foreach ($jenisList as $jenis)
                                <option value="{{ $jenis }}">{{ $jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Group <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_group" placeholder="Contoh: Chiller, Conveyor, Sistem Hidrolik" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Urutan</label>
                        <input type="number" class="form-control" name="urutan" value="1" min="1">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark" id="btnSaveGroup">
                        <i class="mdi mdi-folder-plus me-1"></i> Simpan Group
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let searchTimeout = null;

    // Load Data Checklist via jQuery AJAX & render into regular HTML table
    function loadChecklistData() {
        const $tbody = $('#tbodyChecklist');
        $tbody.html(`
            <tr>
                <td colspan="9" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Memuat data checklist...
                </td>
            </tr>
        `);

        const params = {
            jenis_mtc: $('#filter-jenis').val(),
            group_id:  $('#filter-group').val(),
            status:    $('#filter-status').val(),
            search:    $('#filter-search').val(),
        };

        $.ajax({
            url: "{{ route('master.mtc.checklist.data') }}",
            type: "GET",
            data: params,
            dataType: "json",
            success: function(res) {
                if (res.summary) {
                    $('#stat-total').text(res.summary.total || 0);
                    $('#stat-active').text(res.summary.active || 0);
                    $('#stat-inactive').text(res.summary.inactive || 0);
                    $('#stat-groups').text(res.summary.groups || 0);
                }

                const data = res.data || [];
                $('#table-info').text(`Menampilkan ${data.length} item checklist`);

                if (data.length === 0) {
                    $tbody.html(`
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="mdi mdi-clipboard-text-search-outline fs-1 d-block mb-2 text-secondary"></i>
                                Tidak ada data checklist ditemukan.
                            </td>
                        </tr>
                    `);
                    return;
                }

                let rowsHtml = '';
                data.forEach(function(item, index) {
                    const groupName = item.group ? item.group.nama_group : '-';
                    const standarText = item.standar ? `<small class="text-muted">${escapeHtml(item.standar)}</small>` : '<span class="text-muted fst-italic">-</span>';
                    
                    let badgeTipe = '<span class="badge bg-secondary-subtle text-secondary">Text</span>';
                    if (item.tipe_input === 'boolean_ok_ng') {
                        badgeTipe = '<span class="badge bg-primary-subtle text-primary">OK / NG</span>';
                    } else if (item.tipe_input === 'number') {
                        badgeTipe = '<span class="badge bg-info-subtle text-info">Number</span>';
                    }

                    const badgeStatus = item.is_active 
                        ? '<span class="badge bg-success-subtle text-success">Aktif</span>'
                        : '<span class="badge bg-danger-subtle text-danger">Nonaktif</span>';

                    const toggleText = item.is_active ? 'Nonaktifkan' : 'Aktifkan';
                    const toggleIcon = item.is_active ? 'mdi-close-circle-outline' : 'mdi-check-circle-outline';
                    const toggleColor = item.is_active ? 'text-warning' : 'text-success';

                    rowsHtml += `
                        <tr>
                            <td class="text-center text-muted fw-semibold">${index + 1}</td>
                            <td><span class="fw-semibold text-dark">${escapeHtml(item.jenis_mtc)}</span></td>
                            <td><span class="badge bg-light text-dark border">${escapeHtml(groupName)}</span></td>
                            <td class="fw-semibold">${escapeHtml(item.label)}</td>
                            <td>${standarText}</td>
                            <td class="text-center">${badgeTipe}</td>
                            <td class="text-center text-muted">${item.urutan}</td>
                            <td class="text-center">${badgeStatus}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit" 
                                        data-id="${item.id}" 
                                        data-jenis="${escapeHtml(item.jenis_mtc)}"
                                        data-group="${item.group_id}"
                                        data-label="${escapeHtml(item.label)}"
                                        data-standar="${escapeHtml(item.standar || '')}"
                                        data-tipe="${item.tipe_input}"
                                        data-urutan="${item.urutan}"
                                        title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-toggle ${toggleColor}" 
                                        data-id="${item.id}" 
                                        title="${toggleText}">
                                        <i class="mdi ${toggleIcon}"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete" 
                                        data-id="${item.id}" 
                                        data-label="${escapeHtml(item.label)}"
                                        title="Hapus">
                                        <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                $tbody.html(rowsHtml);
            },
            error: function() {
                $tbody.html(`
                    <tr>
                        <td colspan="9" class="text-center py-4 text-danger">
                            <i class="mdi mdi-alert-circle-outline me-1"></i> Gagal memuat data checklist. Silakan coba kembali.
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Escape helper for XSS protection
    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Initial load
    loadChecklistData();

    // Refresh button
    $('#btn-refresh-table').on('click', function() {
        loadChecklistData();
    });

    // Filter events
    $('#filter-jenis, #filter-group, #filter-status').on('change', function() {
        loadChecklistData();
    });

    // Search with debounce
    $('#filter-search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadChecklistData();
        }, 300);
    });

    // Reset filter
    $('#btn-reset-filter').on('click', function() {
        $('#filter-search').val('');
        $('#filter-jenis').val('');
        $('#filter-group').val('');
        $('#filter-status').val('');
        loadChecklistData();
    });

    // Dynamic Group Loader for Modals
    function loadGroupsForJenis(jenis, targetSelectId, selectedGroupId = null) {
        const $select = $(targetSelectId);
        $select.empty().append('<option value="" disabled selected>Memuat group...</option>');

        if (!jenis) {
            $select.empty().append('<option value="" disabled selected>-- Pilih Jenis Maintenance Dulu --</option>');
            return;
        }

        $.get("{{ route('master.mtc.checklist.groups') }}", { jenis_mtc: jenis }, function(res) {
            $select.empty().append('<option value="" disabled selected>-- Pilih Group --</option>');
            if (res.status && res.groups.length > 0) {
                res.groups.forEach(g => {
                    const isSelected = selectedGroupId && selectedGroupId == g.id ? 'selected' : '';
                    $select.append(`<option value="${g.id}" ${isSelected}>${g.nama_group}</option>`);
                });
            } else {
                $select.append('<option value="" disabled>Belum ada group untuk jenis ini</option>');
            }
        });
    }

    $('#modal_jenis_mtc').on('change', function() {
        loadGroupsForJenis($(this).val(), '#modal_group_id');
    });

    // Open Add Modal
    $('#btnOpenAddModal').on('click', function() {
        $('#formChecklist')[0].reset();
        $('#checklist_id').val('');
        $('#modalChecklistTitle').text('Tambah Item Checklist');
        $('#modal_group_id').empty().append('<option value="" disabled selected>-- Pilih Jenis Maintenance Dulu --</option>');
    });

    // Edit Checklist Item
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const jenis = $(this).data('jenis');
        const group = $(this).data('group');
        const label = $(this).data('label');
        const standar = $(this).data('standar');
        const tipe = $(this).data('tipe');
        const urutan = $(this).data('urutan');

        $('#checklist_id').val(id);
        $('#modal_jenis_mtc').val(jenis);
        $('#modal_label').val(label);
        $('#modal_standar').val(standar);
        $('#modal_tipe_input').val(tipe);
        $('#modal_urutan').val(urutan);
        $('#modalChecklistTitle').text('Edit Item Checklist');

        loadGroupsForJenis(jenis, '#modal_group_id', group);
        $('#modalChecklist').modal('show');
    });

    // Save Checklist Item (Create / Update)
    $('#formChecklist').on('submit', function(e) {
        e.preventDefault();
        const id = $('#checklist_id').val();
        const url = id ? `/mtc/master/checklist/update/${id}` : "{{ route('master.mtc.checklist.store') }}";
        const btn = $('#btnSaveChecklist');

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                $('#modalChecklist').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                loadChecklistData();
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                });
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="mdi mdi-content-save me-1"></i> Simpan');
            }
        });
    });

    // Save Group
    $('#formAddGroup').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btnSaveGroup');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: "{{ route('master.mtc.checklist.group.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                $('#modalAddGroup').modal('hide');
                $('#formAddGroup')[0].reset();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                location.reload();
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                });
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="mdi mdi-folder-plus me-1"></i> Simpan Group');
            }
        });
    });

    // Toggle Active Status
    $(document).on('click', '.btn-toggle', function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/mtc/master/checklist/toggle-status/${id}`,
            type: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status Diperbarui',
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false
                });
                loadChecklistData();
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Gagal mengubah status', 'error');
            }
        });
    });

    // Delete Checklist Item
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const label = $(this).data('label');

        Swal.fire({
            title: 'Hapus Item Checklist?',
            text: `Apakah Anda yakin ingin menghapus "${label}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/mtc/master/checklist/delete/${id}`,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        Swal.fire('Sukses', res.message, 'success');
                        loadChecklistData();
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Gagal menghapus checklist', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
