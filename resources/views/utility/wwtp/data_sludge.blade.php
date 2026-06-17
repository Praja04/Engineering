@extends('layouts.app')

@section('title', 'Data Sludge WWTP')

@section('content')

<div class="page-content">
    <div class="container-fluid">
        <div class="container-fluid px-4 py-5">

            <!-- Header Section -->
            <div class="mb-5">
                <h1 class="display-5 fw-bold text-warning mb-2">
                    <i class="mdi mdi-delete-variant me-3"></i>Data Sludge WWTP
                </h1>
                <p class="text-muted fs-5">Wastewater Treatment Plant - Sludge Management Monitoring</p>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-5">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3 p-3 me-3">
                                    <i class="mdi mdi-database fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">Total Records</p>
                                    <h3 class="fw-bold mb-0" id="totalRecords">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-info bg-opacity-10 text-info rounded-3 p-3 me-3">
                                    <i class="mdi mdi-calendar-week fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">This Week</p>
                                    <h3 class="fw-bold mb-0" id="weekRecords">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                                    <i class="mdi mdi-calendar-today fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">Today</p>
                                    <h3 class="fw-bold mb-0" id="todayRecords">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-danger bg-opacity-10 text-danger rounded-3 p-3 me-3">
                                    <i class="mdi mdi-hydraulic-oil-level fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">Avg Drain Bulan Ini</p>
                                    <h3 class="fw-bold mb-0" id="avgDrain">0</h3>
                                    <small class="text-muted">m³</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="row mb-4">
                <div class="col-12 d-flex gap-2">
                    <a href="{{ url('/wwtp/form_sludge') }}" class="btn btn-warning btn-lg">
                        <i class="mdi mdi-plus-circle me-2"></i>Tambah Data Sludge
                    </a>
                    <button type="button" class="btn btn-success btn-lg" id="btnExport">
                        <i class="mdi mdi-file-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>

            <!-- Data Tabs -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 p-4">
                    <ul class="nav nav-pills nav-fill gap-2" id="sludgeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="sludge-tab" data-bs-toggle="tab" data-bs-target="#tab-sludge" type="button">
                                <i class="mdi mdi-delete-variant me-2"></i>Data Sludge Harian
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pengangkutan-tab" data-bs-toggle="tab" data-bs-target="#tab-pengangkutan" type="button">
                                <i class="mdi mdi-truck me-2"></i>Pengangkutan Sludge
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="sludgeTabsContent">

                        {{-- ===== Tab: Sludge Harian ===== --}}
                        <div class="tab-pane fade show active" id="tab-sludge" role="tabpanel">

                            <!-- Filter Sludge -->
                            <div class="row g-3 mb-4 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label small text-muted fw-semibold">Filter Shift</label>
                                    <select class="form-select" id="filterShift">
                                        <option value="">Semua Shift</option>
                                        <option value="shift1">Shift 1 (06:00 - 14:00)</option>
                                        <option value="shift2">Shift 2 (14:00 - 22:00)</option>
                                        <option value="shift3">Shift 3 (22:00 - 06:00)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted fw-semibold">Filter Bulan</label>
                                    <input type="month" class="form-control" id="filterBulan">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted fw-semibold">Cari Tanggal</label>
                                    <input type="text" class="form-control" id="searchData" placeholder="Cari berdasarkan tanggal...">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-secondary w-100" id="btnReset">
                                        <i class="mdi mdi-refresh me-1"></i>Reset
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="fw-semibold">No</th>
                                            <th class="fw-semibold">Tanggal</th>
                                            <th class="fw-semibold">Shift</th>
                                            <th class="fw-semibold text-center">Drain Lumpur (m³)</th>
                                            <th class="fw-semibold text-center">Running Hour SCP (jam)</th>
                                            <th class="fw-semibold text-center">Hasil Lumpur (ton)</th>
                                            <th class="fw-semibold text-center">Sludge Content (%)</th>
                                            <th class="fw-semibold text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sludgeTableBody">
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="spinner-border text-warning" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Sludge Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                <div class="text-muted small" id="sludgePaginationInfo"></div>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0" id="sludgePagination"></ul>
                                </nav>
                            </div>
                        </div>

                        {{-- ===== Tab: Pengangkutan Sludge ===== --}}
                        <div class="tab-pane fade" id="tab-pengangkutan" role="tabpanel">

                            <!-- Filter Pengangkutan -->
                            <div class="row g-3 mb-4 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted fw-semibold">Filter Bulan</label>
                                    <input type="month" class="form-control" id="filterBulanPengangkutan">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted fw-semibold">Cari Minggu</label>
                                    <input type="text" class="form-control" id="searchPengangkutan" placeholder="Cari berdasarkan tanggal minggu...">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-secondary w-100" id="btnResetPengangkutan">
                                        <i class="mdi mdi-refresh me-1"></i>Reset
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="fw-semibold">No</th>
                                            <th class="fw-semibold">Periode Minggu</th>
                                            <th class="fw-semibold text-center">Jumlah Pengangkutan (ton)</th>
                                            <th class="fw-semibold text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pengangkutanTableBody">
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="mdi mdi-information me-2"></i>Data pengangkutan akan
                                                ditampilkan di sini
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pengangkutan Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                <div class="text-muted small" id="pengangkutanPaginationInfo"></div>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0" id="pengangkutanPagination"></ul>
                                </nav>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================================== --}}
        {{-- MODAL: Detail Sludge --}}
        {{-- =========================================================== --}}
        <div class="modal fade" id="detailSludgeModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title">
                            <i class="mdi mdi-delete-variant me-2"></i>Detail Data Sludge
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4" id="modalSludgeContent"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-danger" id="btnDelete" style="display:none;">
                            <i class="mdi mdi-trash-can me-2"></i>Hapus Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL: Edit Sludge --}}
        <div class="modal fade" id="editSludgeModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="mdi mdi-pencil me-2"></i>Edit Data Sludge
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="editSludgeForm">
                            <input type="hidden" id="edit_id">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit_tanggal" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Shift <span class="text-danger">*</span></label>
                                    <select class="form-select" id="edit_shift" required>
                                        <option value="">-- Pilih Shift --</option>
                                        <option value="shift1">Shift 1 (06:00 - 14:00)</option>
                                        <option value="shift2">Shift 2 (14:00 - 22:00)</option>
                                        <option value="shift3">Shift 3 (22:00 - 06:00)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Drain Lumpur (m³)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_drain_lumpur">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Running Hour SCP (jam)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_running_hour_scp">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Hasil Lumpur (ton)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_hasil_lumpur">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Sludge Content (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_sludge_content">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btnSaveEdit">
                            <i class="mdi mdi-content-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================================== --}}
        {{-- MODAL: Detail Pengangkutan --}}
        {{-- =========================================================== --}}
        <div class="modal fade" id="detailPengangkutanModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="mdi mdi-truck me-2"></i>Detail Pengangkutan Sludge
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4" id="modalPengangkutanContent"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-danger" id="btnDeletePengangkutan" style="display:none;">
                            <i class="mdi mdi-trash-can me-2"></i>Hapus Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL: Edit Pengangkutan --}}
        <div class="modal fade" id="editPengangkutanModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="mdi mdi-pencil me-2"></i>Edit Pengangkutan Sludge
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="editPengangkutanForm">
                            <input type="hidden" id="edit_pengangkutan_id">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal (dalam minggu tersebut) <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_pengangkutan_tanggal" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jumlah Pengangkutan (ton) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="edit_pengangkutan_jumlah" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-success" id="btnSavePengangkutan">
                            <i class="mdi mdi-content-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL: Export Excel --}}
        <div class="modal fade" id="exportExcelModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="mdi mdi-file-excel me-2"></i>Export Report WWTP
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 shadow-sm mb-4">
                            <i class="mdi mdi-information me-2"></i>Laporan mencakup data <strong>Proses, Performance, Sludge,</strong> dan <strong>Chemical</strong> pada tanggal yang dipilih.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Pilih Tanggal Laporan</label>
                            <input type="date" class="form-control form-control-lg" id="export_tanggal" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-success px-4" id="btnProcessExport">
                            <i class="mdi mdi-download me-2"></i>Download Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .stat-card {
        transition: transform .3s ease, box-shadow .3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }

    .data-row {
        transition: background-color .2s ease;
    }

    .data-row:hover {
        background-color: rgba(241, 180, 76, .08);
    }

    .detail-item {
        transition: all .2s ease;
    }

    .detail-item:hover {
        border-color: #f1b44c !important;
        background-color: rgba(241, 180, 76, .05);
    }

    .nav-pills .nav-link {
        border-radius: 10px;
        font-weight: 500;
        padding: 12px 24px;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #f1b44c 0%, #e09b23 100%);
    }

    .badge-shift {
        font-size: .85rem;
        padding: .5rem 1rem;
    }

    .pagination .page-link {
        border-radius: 6px !important;
        margin: 0 2px;
        color: #f1b44c;
        border-color: #dee2e6;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #f1b44c 0%, #e09b23 100%);
        border-color: transparent;
    }

    .pagination .page-link:hover {
        background-color: #fff8ec;
        border-color: #f1b44c;
        color: #e09b23;
    }

    .pagination .page-item.disabled .page-link {
        color: #adb5bd;
    }
</style>

<script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    $(document).ready(function() {

        const PER_PAGE = 10;
        const userJabatan = "{{ Auth::user()->jabatan }}";
        const canEditDelete = userJabatan !== 'operator';

        function canEditDeleteDaily(approvalStatus) {
            if (approvalStatus === 'approved_supervisor') {
                return ['supervisor', 'admin', 'dept_head'].includes(userJabatan);
            }
            if (approvalStatus === 'approved_foreman') {
                return ['foreman', 'supervisor', 'admin', 'dept_head'].includes(userJabatan);
            }
            return true;
        }

        /* ─────────────────────────────────────────────
           STATE
        ───────────────────────────────────────────── */
        const sludgeState = {
            currentPage: 1,
            lastPage: 1,
            total: 0,
            shift: '',
            bulan: '',
            search: '',
        };

        const pengangkutanState = {
            currentPage: 1,
            lastPage: 1,
            total: 0,
            bulan: '',
            search: '',
        };

        let currentSludgeId = null;
        let currentPengangkutanId = null;

        /* ─────────────────────────────────────────────
           INIT
        ───────────────────────────────────────────── */
        loadStatistics();
        loadSludge(1);

        /* ─────────────────────────────────────────────
           EXPORT EXCEL
        ───────────────────────────────────────────── */
        $('#btnExport').on('click', function() {
            $('#exportExcelModal').modal('show');
        });

        $('#btnProcessExport').on('click', function() {
            const tanggal = $('#export_tanggal').val();
            if (!tanggal) {
                Swal.fire('Peringatan', 'Silakan pilih tanggal terlebih dahulu', 'warning');
                return;
            }
            window.open(`{{ route('wwtp.export') }}?tanggal=${tanggal}`, '_blank');
            $('#exportExcelModal').modal('hide');
        });

        /* ─────────────────────────────────────────────
           STATISTICS
        ───────────────────────────────────────────── */
        function loadStatistics() {
            $.ajax({
                url: '/api/wwtp-sludge/dashboard/statistics',
                method: 'GET',
                success: function(res) {
                    $('#totalRecords').text(res.total_shifts ?? 0);
                    $('#weekRecords').text(res.shifts_this_week ?? 0);
                    $('#todayRecords').text(res.shifts_today ?? 0);
                    $('#avgDrain').text(res.monthly_drain_avg ?? 0);
                },
            });
        }

        /* ─────────────────────────────────────────────
           SLUDGE HARIAN
        ───────────────────────────────────────────── */
        function loadSludge(page) {
            page = page || sludgeState.currentPage;

            const params = {
                page,
                per_page: PER_PAGE
            };
            if (sludgeState.shift) params.shift = sludgeState.shift;
            if (sludgeState.bulan) params.bulan = sludgeState.bulan;
            if (sludgeState.search) params.search = sludgeState.search;

            showLoading('#sludgeTableBody', 8);
            clearPagination('#sludgePaginationInfo', '#sludgePagination');

            $.ajax({
                url: '/api/wwtp-sludge',
                method: 'GET',
                data: params,
                success: function(response) {
                    sludgeState.currentPage = response.current_page;
                    sludgeState.lastPage = response.last_page;
                    sludgeState.total = response.total;

                    $('#totalRecords').text(response.total);
                    renderSludgeTable(response.data, response.from);
                    renderPaginationInfo('#sludgePaginationInfo', response);
                    renderPagination('#sludgePagination', response, loadSludge);
                },
                error: function() {
                    showError('Gagal memuat data sludge');
                }
            });
        }

        function renderSludgeTable(data, from) {
            const tbody = $('#sludgeTableBody');
            tbody.empty();

            if (!data || !data.length) {
                tbody.append(`<tr><td colspan="8" class="text-center py-4 text-muted">
                    <i class="mdi mdi-inbox me-2"></i>Tidak ada data sludge</td></tr>`);
                return;
            }

            data.forEach(function(item, idx) {
                const no = (from || 1) + idx;
                let btns = `<button class="btn btn-sm btn-outline-primary me-1"
                                onclick="showSludgeDetail(${item.id})" title="Lihat Detail">
                                <i class="mdi mdi-eye"></i></button>`;
                if (canEditDeleteDaily(item.approval_status)) {
                    btns += `
                    <button class="btn btn-sm btn-outline-success me-1"
                            onclick="showSludgeEdit(${item.id})" title="Edit">
                        <i class="mdi mdi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger"
                            onclick="confirmDeleteSludge(${item.id})" title="Hapus">
                        <i class="mdi mdi-trash-can"></i></button>`;
                }

                tbody.append(`
                <tr class="data-row">
                    <td>${no}</td>
                    <td>${formatDate(item.tanggal)}</td>
                    <td>${getShiftBadge(item.shift)}</td>
                    <td class="text-center fw-bold">${parseFloat(item.drain_lumpur   || 0).toFixed(2)}</td>
                    <td class="text-center fw-bold">${parseFloat(item.running_hour_scp || 0).toFixed(2)}</td>
                    <td class="text-center fw-bold">${parseFloat(item.hasil_lumpur   || 0).toFixed(2)}</td>
                    <td class="text-center fw-bold">${item.sludge_content != null ? parseFloat(item.sludge_content).toFixed(2) : '-'}</td>
                    <td class="text-center">${btns}</td>
                </tr>`);
            });
        }

        // Filters – sludge
        let sludgeSearchTimer;
        $('#searchData').on('keyup', function() {
            clearTimeout(sludgeSearchTimer);
            sludgeSearchTimer = setTimeout(function() {
                sludgeState.search = $('#searchData').val();
                sludgeState.currentPage = 1;
                loadSludge(1);
            }, 400);
        });

        $('#filterShift').on('change', function() {
            sludgeState.shift = $(this).val();
            sludgeState.currentPage = 1;
            loadSludge(1);
        });

        $('#filterBulan').on('change', function() {
            sludgeState.bulan = $(this).val();
            sludgeState.currentPage = 1;
            loadSludge(1);
        });

        $('#btnReset').on('click', function() {
            $('#filterShift').val('');
            $('#filterBulan').val('');
            $('#searchData').val('');
            sludgeState.shift = '';
            sludgeState.bulan = '';
            sludgeState.search = '';
            sludgeState.currentPage = 1;
            loadSludge(1);
        });

        // Detail – sludge
        window.showSludgeDetail = function(id) {
            $.ajax({
                url: `/api/wwtp-sludge/${id}`,
                method: 'GET',
                success: function(record) {
                    currentSludgeId = id;
                    $('#modalSludgeContent').html(`
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="info-box p-3 bg-light rounded">
                                <p class="text-muted small mb-1">Tanggal</p>
                                <p class="fw-bold mb-0">${formatDate(record.tanggal)}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box p-3 bg-light rounded">
                                <p class="text-muted small mb-1">Shift</p>
                                ${getShiftBadge(record.shift)}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box p-3 bg-light rounded">
                                <p class="text-muted small mb-1">Waktu Input</p>
                                <p class="fw-bold mb-0">${formatDateTime(record.created_at)}</p>
                            </div>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-3 text-warning">Data Pengelolaan Sludge</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-item p-4 border rounded bg-light">
                                <span class="text-muted">Drain Lumpur</span>
                                <p class="fw-bold fs-3 mb-0 text-warning">${parseFloat(record.drain_lumpur || 0).toFixed(2)} <small class="text-muted fs-6">m³</small></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item p-4 border rounded bg-light">
                                <span class="text-muted">Running Hour SCP</span>
                                <p class="fw-bold fs-3 mb-0 text-warning">${parseFloat(record.running_hour_scp || 0).toFixed(2)} <small class="text-muted fs-6">jam</small></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item p-4 border rounded bg-light">
                                <span class="text-muted">Hasil Lumpur</span>
                                <p class="fw-bold fs-3 mb-0 text-warning">${parseFloat(record.hasil_lumpur || 0).toFixed(2)} <small class="text-muted fs-6">ton</small></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item p-4 border rounded bg-light">
                                <span class="text-muted">Sludge Content</span>
                                <p class="fw-bold fs-3 mb-0 text-warning">${record.sludge_content != null ? parseFloat(record.sludge_content).toFixed(2) : '-'} <small class="text-muted fs-6">%</small></p>
                            </div>
                        </div>
                    </div>`);
                    if (canEditDeleteDaily(record.approval_status)) {
                        $('#btnDelete').show();
                    } else {
                        $('#btnDelete').hide();
                    }
                    new bootstrap.Modal(document.getElementById('detailSludgeModal')).show();
                },
                error: function() {
                    showError('Gagal memuat detail data');
                }
            });
        };

        // Edit – sludge
        window.showSludgeEdit = function(id) {
            $.ajax({
                url: `/api/wwtp-sludge/${id}`,
                method: 'GET',
                success: function(record) {
                    currentSludgeId = id;
                    $('#edit_id').val(record.id);
                    $('#edit_tanggal').val(record.tanggal);
                    $('#edit_shift').val(record.shift);
                    $('#edit_drain_lumpur').val(record.drain_lumpur);
                    $('#edit_running_hour_scp').val(record.running_hour_scp);
                    $('#edit_hasil_lumpur').val(record.hasil_lumpur);
                    $('#edit_sludge_content').val(record.sludge_content);
                    new bootstrap.Modal(document.getElementById('editSludgeModal')).show();
                },
                error: function() {
                    showError('Gagal memuat data untuk diedit');
                }
            });
        };

        $('#btnSaveEdit').on('click', function() {
            const id = $('#edit_id').val();
            const btn = $(this),
                orig = btn.html();
            btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

            $.ajax({
                url: `/api/wwtp-sludge/${id}`,
                method: 'POST',
                data: {
                    tanggal: $('#edit_tanggal').val(),
                    shift: $('#edit_shift').val(),
                    drain_lumpur: $('#edit_drain_lumpur').val(),
                    running_hour_scp: $('#edit_running_hour_scp').val(),
                    hasil_lumpur: $('#edit_hasil_lumpur').val(),
                    sludge_content: $('#edit_sludge_content').val(),
                },
                success: function() {
                    $('#editSludgeModal').modal('hide');
                    showSuccess('Data sludge berhasil diperbarui');
                    loadSludge(sludgeState.currentPage);
                },
                error: function(xhr) {
                    showErrorFromXhr(xhr);
                },
                complete: function() {
                    btn.prop('disabled', false).html(orig);
                }
            });
        });

        $('#btnDelete').on('click', function() {
            if (!currentSludgeId) return;
            confirmSwal('Hapus data sludge ini?', function() {
                $.ajax({
                    url: `/api/wwtp-sludge/${currentSludgeId}`,
                    method: 'DELETE',
                    success: function() {
                        $('#detailSludgeModal').modal('hide');
                        showSuccess('Data sludge berhasil dihapus');
                        loadSludge(sludgeState.currentPage);
                    },
                    error: function() {
                        showError('Gagal menghapus data sludge');
                    }
                });
            });
        });

        window.confirmDeleteSludge = function(id) {
            confirmSwal('Hapus data sludge ini?', function() {
                $.ajax({
                    url: `/api/wwtp-sludge/${id}`,
                    method: 'DELETE',
                    success: function() {
                        showSuccess('Data sludge berhasil dihapus');
                        loadSludge(sludgeState.currentPage);
                    },
                    error: function() {
                        showError('Gagal menghapus data sludge');
                    }
                });
            });
        };

        /* ─────────────────────────────────────────────
           PENGANGKUTAN SLUDGE
        ───────────────────────────────────────────── */
        function loadPengangkutan(page) {
            page = page || pengangkutanState.currentPage;

            const params = {
                page,
                per_page: PER_PAGE
            };
            if (pengangkutanState.bulan) params.bulan = pengangkutanState.bulan;
            if (pengangkutanState.search) params.search = pengangkutanState.search;

            showLoading('#pengangkutanTableBody', 4);
            clearPagination('#pengangkutanPaginationInfo', '#pengangkutanPagination');

            $.ajax({
                url: '/api/wwtp-sludge/pengangkutan',
                method: 'GET',
                data: params,
                success: function(response) {
                    pengangkutanState.currentPage = response.current_page;
                    pengangkutanState.lastPage = response.last_page;
                    pengangkutanState.total = response.total;

                    renderPengangkutanTable(response.data, response.from);
                    renderPaginationInfo('#pengangkutanPaginationInfo', response);
                    renderPagination('#pengangkutanPagination', response, loadPengangkutan);
                },
                error: function() {
                    showError('Gagal memuat data pengangkutan');
                }
            });
        }

        function renderPengangkutanTable(data, from) {
            const tbody = $('#pengangkutanTableBody');
            tbody.empty();

            if (!data || !data.length) {
                tbody.append(`<tr><td colspan="4" class="text-center py-4 text-muted">
                    <i class="mdi mdi-inbox me-2"></i>Tidak ada data pengangkutan</td></tr>`);
                return;
            }

            data.forEach(function(item, idx) {
                const no = (from || 1) + idx;
                let btns = `<button class="btn btn-sm btn-outline-info me-1"
                                onclick="showPengangkutanDetail(${item.id})" title="Lihat Detail">
                                <i class="mdi mdi-eye"></i></button>`;
                if (canEditDelete) {
                    btns += `
                    <button class="btn btn-sm btn-outline-success me-1"
                            onclick="showPengangkutanEdit(${item.id})" title="Edit">
                        <i class="mdi mdi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger"
                            onclick="confirmDeletePengangkutan(${item.id})" title="Hapus">
                        <i class="mdi mdi-trash-can"></i></button>`;
                }

                tbody.append(`
                <tr class="data-row">
                    <td>${no}</td>
                    <td>${formatWeekRange(item.week_start, item.week_end)}</td>
                    <td class="text-center fw-bold">${parseFloat(item.jumlah_pengangkutan || 0).toFixed(2)}</td>
                    <td class="text-center">${btns}</td>
                </tr>`);
            });
        }

        // Tab click – lazy load pengangkutan
        $('#pengangkutan-tab').on('click', function() {
            if (pengangkutanState.total === 0 && pengangkutanState.currentPage === 1) {
                loadPengangkutan(1);
            }
        });

        // Filters – pengangkutan
        let pengangkutanSearchTimer;
        $('#searchPengangkutan').on('keyup', function() {
            clearTimeout(pengangkutanSearchTimer);
            pengangkutanSearchTimer = setTimeout(function() {
                pengangkutanState.search = $('#searchPengangkutan').val();
                pengangkutanState.currentPage = 1;
                loadPengangkutan(1);
            }, 400);
        });

        $('#filterBulanPengangkutan').on('change', function() {
            pengangkutanState.bulan = $(this).val();
            pengangkutanState.currentPage = 1;
            loadPengangkutan(1);
        });

        $('#btnResetPengangkutan').on('click', function() {
            $('#filterBulanPengangkutan').val('');
            $('#searchPengangkutan').val('');
            pengangkutanState.bulan = '';
            pengangkutanState.search = '';
            pengangkutanState.currentPage = 1;
            loadPengangkutan(1);
        });

        // Detail – pengangkutan
        window.showPengangkutanDetail = function(id) {
            $.ajax({
                url: `/api/wwtp-sludge/pengangkutan/${id}`,
                method: 'GET',
                success: function(response) {
                    const r = response.data;
                    currentPengangkutanId = id;
                    $('#modalPengangkutanContent').html(`
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="info-box p-3 bg-light rounded">
                                <p class="text-muted small mb-1">Periode Minggu</p>
                                <p class="fw-bold mb-0">${formatWeekRange(r.week_start, r.week_end)}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="detail-item p-4 border rounded bg-light text-center">
                                <span class="text-muted">Jumlah Pengangkutan</span>
                                <p class="fw-bold display-6 mb-0 text-info">${parseFloat(r.jumlah_pengangkutan || 0).toFixed(2)} <small class="text-muted fs-6">ton</small></p>
                            </div>
                        </div>
                    </div>`);
                    canEditDelete ? $('#btnDeletePengangkutan').show() : $('#btnDeletePengangkutan').hide();
                    new bootstrap.Modal(document.getElementById('detailPengangkutanModal')).show();
                },
                error: function() {
                    showError('Gagal memuat detail pengangkutan');
                }
            });
        };

        // Edit – pengangkutan
        window.showPengangkutanEdit = function(id) {
            $.ajax({
                url: `/api/wwtp-sludge/pengangkutan/${id}`,
                method: 'GET',
                success: function(response) {
                    const r = response.data;
                    currentPengangkutanId = id;
                    $('#edit_pengangkutan_id').val(r.id);
                    // Gunakan week_start sebagai nilai tanggal awal
                    $('#edit_pengangkutan_tanggal').val(r.week_start);
                    $('#edit_pengangkutan_jumlah').val(r.jumlah_pengangkutan);
                    new bootstrap.Modal(document.getElementById('editPengangkutanModal')).show();
                },
                error: function() {
                    showError('Gagal memuat data untuk diedit');
                }
            });
        };

        $('#btnSavePengangkutan').on('click', function() {
            const id = $('#edit_pengangkutan_id').val();
            const btn = $(this),
                orig = btn.html();
            btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

            $.ajax({
                url: `/api/wwtp-sludge/pengangkutan/${id}`,
                method: 'POST',
                data: {
                    tanggal: $('#edit_pengangkutan_tanggal').val(),
                    jumlah_pengangkutan: $('#edit_pengangkutan_jumlah').val(),
                },
                success: function() {
                    $('#editPengangkutanModal').modal('hide');
                    showSuccess('Data pengangkutan berhasil diperbarui');
                    loadPengangkutan(pengangkutanState.currentPage);
                },
                error: function(xhr) {
                    showErrorFromXhr(xhr);
                },
                complete: function() {
                    btn.prop('disabled', false).html(orig);
                }
            });
        });

        $('#btnDeletePengangkutan').on('click', function() {
            if (!currentPengangkutanId) return;
            confirmSwal('Hapus data pengangkutan ini?', function() {
                $.ajax({
                    url: `/api/wwtp-sludge/pengangkutan/${currentPengangkutanId}`,
                    method: 'DELETE',
                    success: function() {
                        $('#detailPengangkutanModal').modal('hide');
                        showSuccess('Data pengangkutan berhasil dihapus');
                        loadPengangkutan(pengangkutanState.currentPage);
                    },
                    error: function() {
                        showError('Gagal menghapus data pengangkutan');
                    }
                });
            });
        });

        window.confirmDeletePengangkutan = function(id) {
            confirmSwal('Hapus data pengangkutan ini?', function() {
                $.ajax({
                    url: `/api/wwtp-sludge/pengangkutan/${id}`,
                    method: 'DELETE',
                    success: function() {
                        showSuccess('Data pengangkutan berhasil dihapus');
                        loadPengangkutan(pengangkutanState.currentPage);
                    },
                    error: function() {
                        showError('Gagal menghapus data pengangkutan');
                    }
                });
            });
        };

        /* ─────────────────────────────────────────────
           SHARED PAGINATION UTILITIES
        ───────────────────────────────────────────── */
        function renderPaginationInfo(selector, response) {
            if (!response.total) {
                $(selector).text('');
                return;
            }
            $(selector).text(
                `Menampilkan ${response.from ?? 0}–${response.to ?? 0} dari ${response.total} data`
            );
        }

        function renderPagination(selector, response, loadFn) {
            const ul = $(selector);
            const currentPage = response.current_page;
            const lastPage = response.last_page;
            ul.empty();
            if (lastPage <= 1) return;

            ul.append(`<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">&laquo;</a></li>`);

            pageRange(currentPage, lastPage).forEach(function(p) {
                if (p === '...') {
                    ul.append(`<li class="page-item disabled"><span class="page-link">…</span></li>`);
                } else {
                    ul.append(`<li class="page-item ${p === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${p}">${p}</a></li>`);
                }
            });

            ul.append(`<li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">&raquo;</a></li>`);

            ul.find('a.page-link').on('click', function(e) {
                e.preventDefault();
                const p = parseInt($(this).data('page'));
                if (!isNaN(p) && p >= 1 && p <= lastPage) {
                    loadFn(p);
                    $('html, body').animate({
                        scrollTop: 0
                    }, 200);
                }
            });
        }

        function pageRange(current, last) {
            const delta = 2,
                range = [],
                result = [];
            let l;
            for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) range.push(i);
            if (current - delta > 2) range.unshift('...');
            if (current + delta < last - 1) range.push('...');
            range.unshift(1);
            if (last > 1) range.push(last);
            range.forEach(function(i) {
                if (l) {
                    if (i === '...' && l !== '...') result.push('...');
                    else if (i !== '...') result.push(i);
                } else result.push(i);
                l = i;
            });
            return result;
        }

        function showLoading(tbodySelector, colspan) {
            $(tbodySelector).html(`<tr><td colspan="${colspan}" class="text-center py-5">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div></td></tr>`);
        }

        function clearPagination(infoSelector, paginationSelector) {
            $(infoSelector).text('');
            $(paginationSelector).empty();
        }

        /* ─────────────────────────────────────────────
           HELPERS
        ───────────────────────────────────────────── */
        function getShiftBadge(shift) {
            const map = {
                'shift1': '<span class="badge bg-primary badge-shift">Shift 1</span>',
                'shift2': '<span class="badge bg-success badge-shift">Shift 2</span>',
                'shift3': '<span class="badge bg-info badge-shift">Shift 3</span>',
                '1': '<span class="badge bg-primary badge-shift">Shift 1</span>',
                '2': '<span class="badge bg-success badge-shift">Shift 2</span>',
                '3': '<span class="badge bg-info badge-shift">Shift 3</span>',
            };
            return map[String(shift)] || `<span class="badge bg-secondary badge-shift">${shift}</span>`;
        }

        function formatWeekRange(start, end) {
            const opt = {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            };
            return `${new Date(start).toLocaleDateString('id-ID', opt)} – ${new Date(end).toLocaleDateString('id-ID', opt)}`;
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        function formatDateTime(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function confirmSwal(text, onConfirm) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(function(r) {
                if (r.isConfirmed) onConfirm();
            });
        }

        function showSuccess(msg) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: msg,
                confirmButtonColor: '#3085d6',
                timer: 2000
            });
        }

        function showError(msg) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: msg,
                confirmButtonColor: '#d33'
            });
        }

        function showErrorFromXhr(xhr) {
            const err = xhr.responseJSON;
            let msg = 'Terjadi kesalahan saat menyimpan data!';
            if (err?.message) msg = err.message;
            else if (err?.errors) msg = Object.values(err.errors).flat().join('\n');
            showError(msg);
        }
    });
</script>
@endsection