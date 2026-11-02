@extends('layouts.app')

@section('title', 'Master Checklist Maintenance')

@section('styles')
    <style>
        .table-custom tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
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
                    <p class="text-muted mb-0 small">Kelola parameter inspeksi & formulir checklist pemeliharaan secara
                        dinamis</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#modalAddGroup">
                        <i class="mdi mdi-folder-plus-outline me-1"></i> Tambah Group
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalChecklist"
                        id="btnOpenAddModal">
                        <i class="mdi mdi-plus me-1"></i> Tambah Item Checklist
                    </button>
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 mb-0">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-3 p-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="mdi mdi-checkbox-marked-circle-outline fs-3"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold text-dark" id="stat-total">0</h4>
                                <span class="text-muted small text-uppercase fw-semibold">Total Checklist</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 mb-0">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-3 p-3 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="mdi mdi-check-circle fs-3"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold text-dark" id="stat-active">0</h4>
                                <span class="text-muted small text-uppercase fw-semibold">Checklist Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 mb-0">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-3 p-3 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="mdi mdi-folder-multiple-outline fs-3"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold text-dark" id="stat-groups">0</h4>
                                <span class="text-muted small text-uppercase fw-semibold">Total Group</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100 mb-0">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-3 p-3 bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="mdi mdi-pause-circle fs-3"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold text-dark" id="stat-inactive">0</h4>
                                <span class="text-muted small text-uppercase fw-semibold">Checklist Nonaktif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Panel --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold text-muted mb-1">Cari Checklist</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" id="filter-search" class="form-control border-start-0" placeholder="Ketik kata kunci...">
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
                                    <option value="{{ $grp->id }}" data-jenis="{{ $grp->jenis_mtc }}">
                                        {{ $grp->nama_group }} ({{ $grp->jenis_mtc }})
                                    </option>
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
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                        </div>
                                        Memuat data checklist...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- Table Footer / Pagination --}}
                    <div class="d-flex align-items-center justify-content-between p-3 border-top bg-light flex-wrap gap-2">
                        <div id="paginationInfo" class="small text-muted">Menampilkan 0 sampai 0 dari 0 data</div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex align-items-center gap-1">
                                <label for="selectPageSize" class="small text-muted mb-0 me-1">Baris:</label>
                                <select id="selectPageSize" class="form-select form-select-sm" style="width: 75px;">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <ul class="pagination pagination-sm mb-0" id="paginationList"></ul>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="btn-refresh-table"
                                title="Segarkan Data">
                                <i class="mdi mdi-refresh"></i>
                            </button>
                        </div>
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
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Maintenance <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="jenis_mtc" id="modal_jenis_mtc" required>
                                <option value="" disabled selected>-- Pilih Jenis Maintenance --</option>
                                @foreach ($jenisList as $jenis)
                                    <option value="{{ $jenis }}">{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Group / Bagian <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="group_id" id="modal_group_id" required>
                                <option value="" disabled selected>-- Pilih Group --</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Item Checklist <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="label" id="modal_label"
                                placeholder="Contoh: Kebocoran Oli, Putaran Motor, dsb." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Standar / Panduan Pengecekan</label>
                            <textarea class="form-control" name="standar" id="modal_standar" rows="2"
                                placeholder="Contoh: Tidak ada getaran abnormal atau suara berdecit"></textarea>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">Tipe Input <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="tipe_input" id="modal_tipe_input" required>
                                    <option value="boolean_ok_ng">OK / Tidak OK (Radio)</option>
                                    <option value="number">Angka / Pengukuran (Number)</option>
                                    <option value="text">Teks Deskripsi (Text)</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Urutan Tampil</label>
                                <input type="number" class="form-control" name="urutan" id="modal_urutan"
                                    value="1" min="1">
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
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Maintenance <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="jenis_mtc" id="group_jenis_mtc" required>
                                <option value="" disabled selected>-- Pilih Jenis Maintenance --</option>
                                @foreach ($jenisList as $jenis)
                                    <option value="{{ $jenis }}">{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Group <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_group"
                                placeholder="Contoh: Chiller, Conveyor, Sistem Hidrolik" required>
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
            let currentPage = 1;
            let pageSize = 25;

            // Load Data Checklist via jQuery AJAX & render into regular HTML table with pagination
            function loadChecklistData(page = 1) {
                currentPage = page;
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
                    page: currentPage,
                    per_page: pageSize,
                    jenis_mtc: $('#filter-jenis').val(),
                    group_id: $('#filter-group').val(),
                    status: $('#filter-status').val(),
                    search: $('#filter-search').val(),
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
                        const totalRecords = res.recordsFiltered || 0;
                        const totalPages = res.total_pages || Math.ceil(totalRecords / pageSize);
                        const startIndex = (res.current_page - 1) * res.per_page;
                        const startRow = totalRecords > 0 ? startIndex + 1 : 0;
                        const endRow = Math.min(startIndex + pageSize, totalRecords);

                        $('#paginationInfo').text(
                            `Menampilkan ${startRow} sampai ${endRow} dari ${totalRecords} data`);

                        if (data.length === 0) {
                            $tbody.html(`
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="mdi mdi-clipboard-text-search-outline fs-1 d-block mb-2 text-secondary"></i>
                                Tidak ada data checklist ditemukan.
                            </td>
                        </tr>
                    `);
                            $('#paginationList').empty();
                            return;
                        }

                        let rowsHtml = '';
                        data.forEach(function(item, index) {
                            const groupName = item.group ? item.group.nama_group : '-';
                            const standarText = item.standar ?
                                `<small class="text-muted">${escapeHtml(item.standar)}</small>` :
                                '<span class="text-muted fst-italic">-</span>';

                            let badgeTipe =
                                '<span class="badge bg-secondary-subtle text-secondary">Text</span>';
                            if (item.tipe_input === 'boolean_ok_ng') {
                                badgeTipe =
                                    '<span class="badge bg-primary-subtle text-primary">OK / NG</span>';
                            } else if (item.tipe_input === 'number') {
                                badgeTipe =
                                    '<span class="badge bg-info-subtle text-info">Number</span>';
                            }

                            const badgeStatus = item.is_active ?
                                '<span class="badge bg-success-subtle text-success">Aktif</span>' :
                                '<span class="badge bg-danger-subtle text-danger">Nonaktif</span>';

                            const toggleText = item.is_active ? 'Nonaktifkan' : 'Aktifkan';
                            const toggleIcon = item.is_active ? 'mdi-close-circle-outline' :
                                'mdi-check-circle-outline';
                            const toggleColor = item.is_active ? 'text-warning' :
                                'text-success';

                            rowsHtml += `
                        <tr>
                            <td class="text-center text-muted fw-semibold">${startIndex + index + 1}</td>
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

                        // Render Pagination
                        let pagHtml = '';
                        pagHtml += `
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage - 1}">Sebelumnya</a>
                    </li>
                `;

                        const pages = [];
                        if (totalPages <= 7) {
                            for (let i = 1; i <= totalPages; i++) pages.push(i);
                        } else {
                            pages.push(1);
                            let start = Math.max(2, currentPage - 2);
                            let end = Math.min(totalPages - 1, currentPage + 2);
                            if (currentPage <= 4) {
                                start = 2;
                                end = 5;
                            } else if (currentPage >= totalPages - 3) {
                                start = totalPages - 4;
                                end = totalPages - 1;
                            }
                            if (start > 2) pages.push('...');
                            for (let i = start; i <= end; i++) pages.push(i);
                            if (end < totalPages - 1) pages.push('...');
                            pages.push(totalPages);
                        }

                        pages.forEach(p => {
                            if (p === '...') {
                                pagHtml += `
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        `;
                            } else {
                                pagHtml += `
                            <li class="page-item ${currentPage === p ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="${p}">${p}</a>
                            </li>
                        `;
                            }
                        });

                        pagHtml += `
                    <li class="page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage + 1}">Berikutnya</a>
                    </li>
                `;

                        $('#paginationList').html(pagHtml);
                    },
                    error: function() {
                        $tbody.html(`
                    <tr>
                        <td colspan="9" class="text-center py-4 text-danger">
                            <i class="mdi mdi-alert-circle-outline me-1"></i> Gagal memuat data checklist. Silakan coba kembali.
                        </td>
                    </tr>
                `);
                        $('#paginationInfo').text('Gagal memuat data');
                        $('#paginationList').empty();
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
            loadChecklistData(1);

            // Refresh button
            $('#btn-refresh-table').on('click', function() {
                loadChecklistData(currentPage);
            });

            // Pagination click handler
            $(document).on('click', '#paginationList .page-link', function(e) {
                e.preventDefault();
                const page = parseInt($(this).data('page'));
                if (!isNaN(page) && page > 0 && page !== currentPage) {
                    loadChecklistData(page);
                }
            });

            // Page size change handler
            $('#selectPageSize').on('change', function() {
                pageSize = parseInt($(this).val()) || 25;
                loadChecklistData(1);
            });

            // Filter events
            $('#filter-jenis, #filter-group, #filter-status').on('change', function() {
                loadChecklistData(1);
            });

            // Search with debounce
            $('#filter-search').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    loadChecklistData(1);
                }, 300);
            });

            // Reset filter
            $('#btn-reset-filter').on('click', function() {
                $('#filter-search').val('');
                $('#filter-jenis').val('');
                $('#filter-group').val('');
                $('#filter-status').val('');
                loadChecklistData(1);
            });

            // Dynamic Group Loader for Modals
            function loadGroupsForJenis(jenis, targetSelectId, selectedGroupId = null) {
                const $select = $(targetSelectId);
                $select.empty().append('<option value="" disabled selected>Memuat group...</option>');

                if (!jenis) {
                    $select.empty().append(
                        '<option value="" disabled selected>-- Pilih Jenis Maintenance Dulu --</option>');
                    return;
                }

                $.get("{{ route('master.mtc.checklist.groups') }}", {
                    jenis_mtc: jenis
                }, function(res) {
                    $select.empty().append('<option value="" disabled selected>-- Pilih Group --</option>');
                    if (res.status && res.groups.length > 0) {
                        res.groups.forEach(g => {
                            const isSelected = selectedGroupId && selectedGroupId == g.id ?
                                'selected' : '';
                            $select.append(
                                `<option value="${g.id}" ${isSelected}>${g.nama_group}</option>`
                            );
                        });
                    } else {
                        $select.append(
                            '<option value="" disabled>Belum ada group untuk jenis ini</option>');
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
                $('#modal_group_id').empty().append(
                    '<option value="" disabled selected>-- Pilih Jenis Maintenance Dulu --</option>');
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
                const url = id ? `/mtc/master/checklist/update/${id}` :
                    "{{ route('master.mtc.checklist.store') }}";
                const btn = $('#btnSaveChecklist');

                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

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
                        loadChecklistData(id ? currentPage : 1);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(
                            '<i class="mdi mdi-content-save me-1"></i> Simpan');
                    }
                });
            });

            // Save Group
            $('#formAddGroup').on('submit', function(e) {
                e.preventDefault();
                const btn = $('#btnSaveGroup');
                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

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
                        btn.prop('disabled', false).html(
                            '<i class="mdi mdi-folder-plus me-1"></i> Simpan Group');
                    }
                });
            });

            // Toggle Active Status
            $(document).on('click', '.btn-toggle', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: `/mtc/master/checklist/toggle-status/${id}`,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Diperbarui',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        });
                        loadChecklistData(currentPage);
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Gagal mengubah status',
                            'error');
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
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                Swal.fire('Sukses', res.message, 'success');
                                loadChecklistData(currentPage);
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal', xhr.responseJSON?.message ||
                                    'Gagal menghapus checklist', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
