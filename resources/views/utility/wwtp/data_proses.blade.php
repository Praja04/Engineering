@extends('layouts.app')

@section('title', 'WWTP Data Monitoring')

@section('content')

    <div class="page-content">
        <div class="container-fluid">
            <div class="container-fluid px-4 py-5">
                <!-- Header Section -->
                <div class="mb-5">
                    <h1 class="display-5 fw-bold text-primary mb-2">
                        <i class="fas fa-water me-3"></i>WWTP Data Monitoring
                    </h1>
                    <p class="text-muted fs-5">Wastewater Treatment Plant - Real-time Data Dashboard</p>
                </div>

                <!-- Statistics Cards -->
                <div class="row g-4 mb-5">
                    <div class="col-lg-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100 stat-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                                        <i class="fas fa-database fs-3"></i>
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
                                        <i class="fas fa-arrow-down fs-3"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1 small">Influent Records</p>
                                        <h3 class="fw-bold mb-0" id="influentRecords">0</h3>
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
                                        <i class="fas fa-arrow-up fs-3"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1 small">Effluent Records</p>
                                        <h3 class="fw-bold mb-0" id="effluentRecords">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100 stat-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3 p-3 me-3">
                                        <i class="fas fa-calendar-week fs-3"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1 small">This Week</p>
                                        <h3 class="fw-bold mb-0" id="weekRecords">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mb-4">
                    <div class="col-12 d-flex gap-2">
                        <a href="{{ url('/wwtp/form_proses') }}" class="btn btn-primary btn-lg px-4 shadow-sm">
                            <i class="fas fa-plus-circle me-2"></i>Tambah Data Proses
                        </a>
                        <button type="button" class="btn btn-success btn-lg px-4 shadow-sm" id="btnExport">
                            <i class="fas fa-file-excel me-2"></i>Export Excel
                        </button>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small text-muted fw-semibold">Filter Kategori</label>
                                <select class="form-select form-select-lg" id="filterKategori">
                                    <option value="">Semua Kategori</option>
                                    <option value="influent">Influent</option>
                                    <option value="effluent">Effluent</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted fw-semibold">Filter Bulan</label>
                                <input type="month" class="form-control form-control-lg" id="filterBulan">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted fw-semibold">Cari Data</label>
                                <input type="text" class="form-control form-control-lg" id="searchData"
                                    placeholder="Cari berdasarkan tanggal atau kategori...">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-secondary btn-lg w-100" id="btnReset">
                                    <i class="fas fa-redo me-2"></i>Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Tabs -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 p-4">
                        <ul class="nav nav-pills nav-fill gap-2" id="dataTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="weekly-tab" data-bs-toggle="tab"
                                    data-bs-target="#weekly" type="button">
                                    <i class="fas fa-calendar-alt me-2"></i>Data Mingguan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily"
                                    type="button">
                                    <i class="fas fa-calendar-day me-2"></i>Data Harian
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content" id="dataTabsContent">

                            <!-- Weekly Data Tab -->
                            <div class="tab-pane fade show active" id="weekly" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="fw-semibold">Tanggal</th>
                                                <th class="fw-semibold">Kategori</th>
                                                <th class="fw-semibold">Detail Data</th>
                                                <th class="fw-semibold text-end">Total Volume</th>
                                                <th class="fw-semibold text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="weeklyTableBody">
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Weekly Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                    <div class="text-muted small" id="weeklyPaginationInfo">
                                        <!-- e.g. Menampilkan 1 - 10 dari 45 data -->
                                    </div>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0" id="weeklyPagination">
                                        </ul>
                                    </nav>
                                </div>
                            </div>

                            <!-- Daily Data Tab -->
                            <div class="tab-pane fade" id="daily" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="fw-semibold">Tanggal</th>
                                                <th class="fw-semibold">Shift</th>
                                                <th class="fw-semibold">Detail Data</th>
                                                <th class="fw-semibold text-end">Total Volume</th>
                                                <th class="fw-semibold text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dailyTableBody">
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    <i class="fas fa-info-circle me-2"></i>Data harian akan ditampilkan di
                                                    sini
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Daily Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                    <div class="text-muted small" id="dailyPaginationInfo">
                                    </div>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0" id="dailyPagination">
                                        </ul>
                                    </nav>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Modal -->
            <div class="modal fade" id="detailModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-info-circle me-2"></i>Detail Data WWTP
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4" id="modalDetailContent">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-danger" id="btnDelete">
                                <i class="fas fa-trash me-2"></i>Hapus Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal (Harian) -->
            <div class="modal fade" id="editModal" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-edit me-2"></i>Edit Data WWTP Harian
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form id="editHarianForm">
                                <input type="hidden" id="edit_id" name="id">
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_tanggal" class="form-label fw-semibold">Tanggal <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit_tanggal" name="tanggal"
                                            required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_shift" class="form-label fw-semibold">Shift <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_shift" name="shift" required>
                                            <option value="">-- Pilih Shift --</option>
                                            <option value="shift1">Shift 1 (06:00 - 14:00)</option>
                                            <option value="shift2">Shift 2 (14:00 - 22:00)</option>
                                            <option value="shift3">Shift 3 (22:00 - 06:00)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3 text-info"><i class="mdi mdi-gauge me-2"></i>Data Debit &
                                        Running WWTP</h6>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label for="edit_debit1" class="form-label">Debit WWTP 1</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control"
                                                    id="edit_debit1" name="debit1" min="0" placeholder="0.00">
                                                <span class="input-group-text">m³/h</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="edit_running_wwtp1" class="form-label">Running WWTP 1</label>
                                            <select name="running_wwtp1" id="edit_running_wwtp1" class="form-select">
                                                <option value="">-- Pilih Status --</option>
                                                <option value="ON">ON</option>
                                                <option value="OFF">OFF</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="edit_debit2" class="form-label">Debit WWTP 2</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control"
                                                    id="edit_debit2" name="debit2" min="0" placeholder="0.00">
                                                <span class="input-group-text">m³/h</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="edit_running_wwtp2" class="form-label">Running WWTP 2</label>
                                            <select name="running_wwtp2" id="edit_running_wwtp2" class="form-select">
                                                <option value="">-- Pilih Status --</option>
                                                <option value="ON">ON</option>
                                                <option value="OFF">OFF</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="mdi mdi-water-pump me-2"></i>Data
                                        Influent</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="card border border-primary-subtle p-2">
                                                <label class="form-label fw-semibold text-primary mb-1">Pit Sparta <span
                                                        class="text-danger">*</span></label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Awal</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm" id="edit_pit_sparta_awal"
                                                            name="pit_sparta_awal">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Sekarang</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm" id="edit_pit_sparta"
                                                            name="pit_sparta" min="0" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border border-primary-subtle p-2">
                                                <label class="form-label fw-semibold text-primary mb-1">Pit Garam <span
                                                        class="text-danger">*</span></label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Awal</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm" id="edit_pit_garam_awal"
                                                            name="pit_garam_awal">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Sekarang</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm" id="edit_pit_garam"
                                                            name="pit_garam" min="0" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border border-primary-subtle p-2">
                                                <label class="form-label fw-semibold text-primary mb-1">Pit Domestik <span
                                                        class="text-danger">*</span></label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Awal</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm"
                                                            id="edit_pit_domestik_awal" name="pit_domestik_awal">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Sekarang</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm" id="edit_pit_domestik"
                                                            name="pit_domestik" min="0" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border border-secondary-subtle p-2">
                                                <label class="form-label fw-semibold text-dark mb-1">Pit Produksi Step
                                                    3</label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Awal</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm"
                                                            id="edit_pit_produksi_step3_awal"
                                                            name="pit_produksi_step3_awal">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Sekarang</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm"
                                                            id="edit_pit_produksi_step3" name="pit_produksi_step3"
                                                            min="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border border-secondary-subtle p-2">
                                                <label class="form-label fw-semibold text-dark mb-1">Pit Storage</label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Awal</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm"
                                                            id="edit_pit_storage_awal" name="pit_storage_awal">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Sekarang</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm" id="edit_pit_storage"
                                                            name="pit_storage" min="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border border-secondary-subtle p-2">
                                                <label class="form-label fw-semibold text-dark mb-1">Pit Proses WWTP
                                                    2</label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Awal</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm"
                                                            id="edit_pit_proses_wwtp2_awal" name="pit_proses_wwtp2_awal">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Sekarang</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm"
                                                            id="edit_pit_proses_wwtp2" name="pit_proses_wwtp2"
                                                            min="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border border-secondary-subtle p-2">
                                                <label class="form-label fw-semibold text-dark mb-1">Pit Outlet</label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Awal</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm" id="edit_pit_outlet_awal"
                                                            name="pit_outlet_awal">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Sekarang</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm" id="edit_pit_outlet"
                                                            name="pit_outlet" min="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border border-secondary-subtle p-2">
                                                <label class="form-label fw-semibold text-dark mb-1">Pit Boiler</label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Awal</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm" id="edit_pit_boiler_awal"
                                                            name="pit_boiler_awal">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small text-muted mb-0">Sekarang</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm" id="edit_pit_boiler"
                                                            name="pit_boiler" min="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-warning" id="btnSaveEdit">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Weekly Modal -->
            <div class="modal fade" id="editWeeklyModal" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-edit me-2"></i>Edit Data WWTP Mingguan
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form id="editWeeklyForm">
                                <input type="hidden" id="edit_weekly_id" name="id">
                                <input type="hidden" id="edit_weekly_kategori" name="kategori">
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_weekly_tanggal" class="form-label fw-semibold">Tanggal <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit_weekly_tanggal"
                                            name="tanggal" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Kategori</label>
                                        <input type="text" class="form-control" id="edit_weekly_kategori_display"
                                            readonly>
                                    </div>
                                </div>
                                <div id="edit_weekly_influent_form" style="display: none;">
                                    <div class="mb-4">
                                        <h6 class="fw-bold mb-3 text-primary"><i class="mdi mdi-water-pump me-2"></i>Data
                                            Influent Mingguan</h6>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label for="edit_weekly_pit_sparta" class="form-label fw-semibold">Pit
                                                    Sparta <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        id="edit_weekly_pit_sparta" name="pit_sparta" min="0">
                                                    <span class="input-group-text">m³</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edit_weekly_pit_garam" class="form-label fw-semibold">Pit
                                                    Garam <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        id="edit_weekly_pit_garam" name="pit_garam" min="0">
                                                    <span class="input-group-text">m³</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edit_weekly_pit_domestik" class="form-label fw-semibold">Pit
                                                    Domestik <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        id="edit_weekly_pit_domestik" name="pit_domestik" min="0">
                                                    <span class="input-group-text">m³</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="edit_weekly_pit_produksi_step3" class="form-label">Pit
                                                    Produksi Step 3</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        id="edit_weekly_pit_produksi_step3" name="pit_produksi_step3"
                                                        min="0">
                                                    <span class="input-group-text">m³</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="edit_weekly_pit_storage" class="form-label">Pit
                                                    Storage</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        id="edit_weekly_pit_storage" name="pit_storage" min="0">
                                                    <span class="input-group-text">m³</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="edit_weekly_pit_proses_wwtp2" class="form-label">Pit Proses
                                                    WWTP 2</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        id="edit_weekly_pit_proses_wwtp2" name="pit_proses_wwtp2"
                                                        min="0">
                                                    <span class="input-group-text">m³</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="edit_weekly_pit_outlet" class="form-label">Pit Outlet</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        id="edit_weekly_pit_outlet" name="pit_outlet" min="0">
                                                    <span class="input-group-text">m³</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="edit_weekly_pit_boiler" class="form-label">Pit Boiler</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        id="edit_weekly_pit_boiler" name="pit_boiler" min="0">
                                                    <span class="input-group-text">m³</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="edit_weekly_effluent_form" style="display: none;">
                                    <div class="mb-4">
                                        <h6 class="fw-bold mb-3 text-success"><i class="mdi mdi-water-check me-2"></i>Data
                                            Effluent Mingguan</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="edit_weekly_full_proses" class="form-label fw-semibold">Full
                                                    Proses <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        id="edit_weekly_full_proses" name="full_proses" min="0">
                                                    <span class="input-group-text">m³</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_weekly_daf_pre" class="form-label fw-semibold">DAF Pre
                                                    <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        id="edit_weekly_daf_pre" name="daf_pre" min="0">
                                                    <span class="input-group-text">m³</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-warning" id="btnSaveEditWeekly">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
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
                                <i class="fas fa-file-excel me-2"></i>Export Report WWTP
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="alert alert-info border-0 shadow-sm mb-4">
                                <i class="fas fa-info-circle me-2"></i>Laporan mencakup data <strong>Proses, Performance,
                                    Sludge,</strong> dan <strong>Chemical</strong> pada tanggal yang dipilih.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Pilih Tanggal
                                    Laporan</label>
                                <input type="date" class="form-control form-control-lg" id="export_tanggal"
                                    value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-success px-4" id="btnProcessExport">
                                <i class="fas fa-download me-2"></i>Download Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .data-row {
            transition: background-color 0.2s ease;
        }

        .data-row:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .nav-pills .nav-link {
            border-radius: 10px;
            font-weight: 500;
            padding: 12px 24px;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .detail-item {
            transition: all 0.2s ease;
        }

        .detail-item:hover {
            border-color: #0d6efd !important;
            background-color: rgba(13, 110, 253, 0.05);
        }

        .pagination .page-link {
            border-radius: 6px !important;
            margin: 0 2px;
            color: #667eea;
            border-color: #dee2e6;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
        }

        .pagination .page-link:hover {
            background-color: #f0f0ff;
            border-color: #667eea;
            color: #667eea;
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
        }
    </style>

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
               STATE
            ───────────────────────────────────────────── */
            let weeklyState = {
                currentPage: 1,
                lastPage: 1,
                total: 0,
                kategori: '',
                bulan: '',
                search: '',
                // keep full current-page rows for editWeekly lookup (no need to cache all)
                rows: []
            };

            let dailyState = {
                currentPage: 1,
                lastPage: 1,
                total: 0,
                bulan: '',
            };

            /* ─────────────────────────────────────────────
               STATS (separate lightweight call)
            ───────────────────────────────────────────── */
            function loadStatistics() {
                $.ajax({
                    url: '/api/wwtp/dashboard/statistics',
                    method: 'GET',
                    success: function(res) {
                        $('#totalRecords').text(res.total_records ?? 0);
                        $('#influentRecords').text(res.total_influent ?? 0);
                        $('#effluentRecords').text(res.total_effluent ?? 0);
                        $('#weekRecords').text(res.this_week ?? 0);
                    },
                    error: function() {
                        /* silent – stats are non-critical */
                    }
                });
            }

            /* ─────────────────────────────────────────────
               WEEKLY — LOAD
            ───────────────────────────────────────────── */
            function loadWeekly(page) {
                page = page || weeklyState.currentPage;

                const params = {
                    page: page,
                    per_page: PER_PAGE,
                };
                if (weeklyState.kategori) params.kategori = weeklyState.kategori;
                if (weeklyState.bulan) params.bulan = weeklyState.bulan;
                if (weeklyState.search) params.search = weeklyState.search;

                $('#weeklyTableBody').html(`
            <tr>
                <td colspan="5" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `);
                $('#weeklyPaginationInfo').text('');
                $('#weeklyPagination').empty();

                $.ajax({
                    url: '/api/wwtp',
                    method: 'GET',
                    data: params,
                    success: function(response) {
                        /* Laravel paginate() returns:
                           { data:[], current_page, last_page, total, from, to } */
                        weeklyState.currentPage = response.current_page;
                        weeklyState.lastPage = response.last_page;
                        weeklyState.total = response.total;
                        weeklyState.rows = response.data;

                        renderWeeklyTable(response.data);
                        renderPaginationInfo('#weeklyPaginationInfo', response);
                        renderPagination('#weeklyPagination', response, loadWeekly);
                    },
                    error: function(xhr) {
                        console.error('Error loading weekly data:', xhr);
                        showError('Gagal memuat data mingguan');
                    }
                });
            }

            /* ─────────────────────────────────────────────
               WEEKLY — RENDER TABLE
            ───────────────────────────────────────────── */
            function renderWeeklyTable(data) {
                const tbody = $('#weeklyTableBody');
                tbody.empty();

                if (!data || data.length === 0) {
                    tbody.append(`
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="fas fa-inbox me-2"></i>Tidak ada data yang ditemukan
                    </td>
                </tr>
            `);
                    return;
                }

                data.forEach(function(item) {
                    const badge = item.kategori === 'influent' ?
                        '<span class="badge bg-info">Influent</span>' :
                        '<span class="badge bg-success">Effluent</span>';

                    let detailData = '';
                    let totalVolume = 0;

                    if (item.kategori === 'influent' && item.influent) {
                        const inf = item.influent;
                        totalVolume = parseFloat(inf.pit_sparta || 0) +
                            parseFloat(inf.pit_garam || 0) +
                            parseFloat(inf.pit_domestik || 0) +
                            parseFloat(inf.pit_produksi_step3 || 0) +
                            parseFloat(inf.pit_storage || 0) +
                            parseFloat(inf.pit_proses_wwtp2 || 0) +
                            parseFloat(inf.pit_outlet || 0) +
                            parseFloat(inf.pit_boiler || 0);
                        detailData =
                            `Sparta: ${inf.pit_sparta}m³, Garam: ${inf.pit_garam}m³, Domestik: ${inf.pit_domestik}m³`;
                    } else if (item.kategori === 'effluent' && item.effluent) {
                        const eff = item.effluent;
                        totalVolume = parseFloat(eff.full_proses || 0) + parseFloat(eff.daf_pre || 0);
                        detailData = `Full Proses: ${eff.full_proses}m³, DAF Pre: ${eff.daf_pre}m³`;
                    }

                    tbody.append(`
                        <tr class="data-row">
                            <td>${formatDate(item.tanggal)}</td>
                            <td>${badge}</td>
                            <td><small class="text-muted">${detailData}</small></td>
                            <td class="text-end fw-bold">${totalVolume.toFixed(2)} m³</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary me-1"
                                        onclick="showDetail(${item.id})"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Detail">
                                    <i class="mdi mdi-eye"></i>
                                </button>
                                ${canEditDelete ? `
                                                    <button class="btn btn-sm btn-outline-warning me-1"
                                                            onclick="editWeekly(${item.id})"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Data">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                            onclick="confirmDelete(${item.id})"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Data">
                                                        <i class="mdi mdi-trash-can"></i>
                                                    </button>
                                                    ` : ''}
                            </td>
                        </tr>
                    `);
                });
            }

            /* ─────────────────────────────────────────────
               DAILY — LOAD
            ───────────────────────────────────────────── */
            function loadDataHarian(page) {
                page = page || dailyState.currentPage;

                const params = {
                    page: page,
                    per_page: PER_PAGE,
                };
                if (dailyState.bulan) params.bulan = dailyState.bulan;

                $('#dailyTableBody').html(`
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </td>
                    </tr>
                `);
                $('#dailyPaginationInfo').text('');
                $('#dailyPagination').empty();

                $.ajax({
                    url: '/api/wwtp/influent-harian',
                    method: 'GET',
                    data: params,
                    success: function(response) {
                        dailyState.currentPage = response.current_page;
                        dailyState.lastPage = response.last_page;
                        dailyState.total = response.total;

                        renderDailyTable(response.data);
                        renderPaginationInfo('#dailyPaginationInfo', response);
                        renderPagination('#dailyPagination', response, loadDataHarian);
                    },
                    error: function(xhr) {
                        console.error('Error loading daily data:', xhr);
                        showError('Gagal memuat data harian');
                    }
                });
            }

            /* ─────────────────────────────────────────────
               DAILY — RENDER TABLE
            ───────────────────────────────────────────── */
            function renderDailyTable(data) {
                const tbody = $('#dailyTableBody');
                tbody.empty();

                if (!data || data.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox me-2"></i>Tidak ada data harian
                            </td>
                        </tr>
                    `);
                    return;
                }

                data.forEach(function(item) {
                    const shiftMap = {
                        shift1: 'Shift 1',
                        shift2: 'Shift 2',
                        shift3: 'Shift 3'
                    };
                    const shiftLabel = shiftMap[item.shift] || item.shift;

                    const diffSparta = Math.max(0, parseFloat(item.pit_sparta || 0) - parseFloat(item
                        .pit_sparta_awal || 0));
                    const diffGaram = Math.max(0, parseFloat(item.pit_garam || 0) - parseFloat(item
                        .pit_garam_awal || 0));
                    const diffDomestik = Math.max(0, parseFloat(item.pit_domestik || 0) - parseFloat(item
                        .pit_domestik_awal || 0));
                    const diffProduksi = Math.max(0, parseFloat(item.pit_produksi_step3 || 0) - parseFloat(
                        item.pit_produksi_step3_awal || 0));
                    const diffStorage = Math.max(0, parseFloat(item.pit_storage || 0) - parseFloat(item
                        .pit_storage_awal || 0));
                    const diffProses = Math.max(0, parseFloat(item.pit_proses_wwtp2 || 0) - parseFloat(item
                        .pit_proses_wwtp2_awal || 0));
                    const diffOutlet = Math.max(0, parseFloat(item.pit_outlet || 0) - parseFloat(item
                        .pit_outlet_awal || 0));
                    const diffBoiler = Math.max(0, parseFloat(item.pit_boiler || 0) - parseFloat(item
                        .pit_boiler_awal || 0));

                    console.log(diffSparta);
                    console.log(diffGaram);
                    console.log(diffDomestik);
                    console.log(diffProduksi);
                    console.log(diffStorage);
                    console.log(diffProses);
                    console.log(diffOutlet);
                    console.log(diffBoiler);
                    const totalVolume = diffSparta + diffGaram + diffDomestik + diffProduksi + diffStorage +
                        diffProses + diffOutlet + diffBoiler;

                    const detailData =
                        `Sparta: ${diffSparta.toFixed(2)}m³, Debit WWTP1: ${item.debit1 || '-'}m³/h, Debit WWTP2: ${item.debit2 || '-'}m³/h`;

                    tbody.append(`
                        <tr class="data-row">
                            <td>${formatDate(item.tanggal)}</td>
                            <td><span class="badge bg-secondary">${shiftLabel}</span></td>
                            <td><small class="text-muted">${detailData}</small></td>
                            <td class="text-end fw-bold">${totalVolume.toFixed(2)} m³</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary me-1"
                                        onclick="showDetailHarian(${item.id})"
                                        data-bs-toggle="tooltip" title="Lihat Detail">
                                    <i class="mdi mdi-eye"></i>
                                </button>
                                ${canEditDeleteDaily(item.approval_status) ? `
                                                    <button class="btn btn-sm btn-outline-warning me-1"
                                                            onclick="editHarian(${item.id})"
                                                            data-bs-toggle="tooltip" title="Edit Data">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                            onclick="confirmDeleteHarian(${item.id})"
                                                            data-bs-toggle="tooltip" title="Hapus Data">
                                                        <i class="mdi mdi-trash-can"></i>
                                                    </button>
                                                    ` : ''}
                            </td>
                        </tr>
                    `);
                });
            }

            /* ─────────────────────────────────────────────
               PAGINATION HELPERS
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

                // Prev
                ul.append(`
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                `);

                // Page numbers — show up to 5 pages around current
                const range = pageRange(currentPage, lastPage);
                range.forEach(function(p) {
                    if (p === '...') {
                        ul.append(`<li class="page-item disabled"><span class="page-link">…</span></li>`);
                    } else {
                        ul.append(`
                    <li class="page-item ${p === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${p}">${p}</a>
                    </li>
                `);
                    }
                });

                // Next
                ul.append(`
                    <li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                `);

                // Bind clicks
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
                const delta = 2;
                const range = [];
                const result = [];
                let l;

                for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
                    range.push(i);
                }
                if (current - delta > 2) range.unshift('...');
                if (current + delta < last - 1) range.push('...');
                range.unshift(1);
                if (last > 1) range.push(last);

                range.forEach(function(i) {
                    if (l) {
                        if (i === '...' && l !== '...') result.push('...');
                        else if (i !== '...') result.push(i);
                    } else {
                        result.push(i);
                    }
                    l = i;
                });
                return result;
            }

            /* ─────────────────────────────────────────────
               FILTER EVENTS
            ───────────────────────────────────────────── */
            // Debounce search to avoid hammering the server on every keystroke
            let searchTimer;
            $('#searchData').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    weeklyState.search = $('#searchData').val();
                    weeklyState.currentPage = 1;
                    loadWeekly(1);
                }, 400);
            });

            $('#filterKategori').on('change', function() {
                weeklyState.kategori = $(this).val();
                weeklyState.currentPage = 1;
                loadWeekly(1);
            });

            $('#filterBulan').on('change', function() {
                const bulan = $(this).val();
                weeklyState.bulan = bulan;
                weeklyState.currentPage = 1;
                dailyState.bulan = bulan;
                dailyState.currentPage = 1;
                loadWeekly(1);
                // reload daily too if tab is visible
                if ($('#daily-tab').hasClass('active')) loadDataHarian(1);
            });

            $('#btnReset').on('click', function() {
                $('#filterKategori').val('');
                $('#filterBulan').val('');
                $('#searchData').val('');
                weeklyState.kategori = '';
                weeklyState.bulan = '';
                weeklyState.search = '';
                weeklyState.currentPage = 1;
                dailyState.bulan = '';
                dailyState.currentPage = 1;
                loadWeekly(1);
                if ($('#daily-tab').hasClass('active')) loadDataHarian(1);
            });

            /* ─────────────────────────────────────────────
               TAB SWITCH
            ───────────────────────────────────────────── */
            $('#daily-tab').on('click', function() {
                if (dailyState.total === 0 && dailyState.currentPage === 1) {
                    loadDataHarian(1);
                }
            });

            /* ─────────────────────────────────────────────
               DETAIL — WEEKLY
            ───────────────────────────────────────────── */
            let currentRecordId = null;
            let currentRecordType = 'weekly';

            window.showDetail = function(id) {
                const record = weeklyState.rows.find(r => r.id === id);
                if (!record) return;

                currentRecordId = id;
                currentRecordType = 'weekly';

                if (canEditDelete) {
                    $('#btnDelete').show();
                } else {
                    $('#btnDelete').hide();
                }

                let content = `
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="info-box p-3 bg-light rounded">
                                <p class="text-muted small mb-1">Tanggal</p>
                                <p class="fw-bold mb-0">${formatDate(record.tanggal)}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box p-3 bg-light rounded">
                                <p class="text-muted small mb-1">Kategori</p>
                                ${record.kategori === 'influent'
                                    ? '<span class="badge bg-info">Influent</span>'
                                    : '<span class="badge bg-success">Effluent</span>'}
                            </div>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-3 text-primary">Volume Detail (m³)</h6>
                    <div class="row g-3">
                `;

                let total = 0;

                if (record.kategori === 'influent' && record.influent) {
                    const inf = record.influent;
                    const items = [{
                            label: 'Pit Sparta',
                            value: inf.pit_sparta
                        },
                        {
                            label: 'Pit Garam',
                            value: inf.pit_garam
                        },
                        {
                            label: 'Pit Domestik',
                            value: inf.pit_domestik
                        },
                        {
                            label: 'Pit Produksi Step 3',
                            value: inf.pit_produksi_step3
                        },
                        {
                            label: 'Pit Storage',
                            value: inf.pit_storage
                        },
                        {
                            label: 'Pit Proses WWTP 2',
                            value: inf.pit_proses_wwtp2
                        },
                        {
                            label: 'Pit Outlet',
                            value: inf.pit_outlet
                        },
                        {
                            label: 'Pit Boiler',
                            value: inf.pit_boiler
                        },
                    ];
                    items.forEach(function(item) {
                        if (item.value !== null && item.value !== undefined) {
                            total += parseFloat(item.value);
                            content += `
                        <div class="col-md-6">
                            <div class="detail-item p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">${item.label}</span>
                                    <span class="fw-bold fs-5">${item.value}</span>
                                </div>
                            </div>
                        </div>
                    `;
                        }
                    });
                } else if (record.kategori === 'effluent' && record.effluent) {
                    const eff = record.effluent;
                    total = parseFloat(eff.full_proses || 0) + parseFloat(eff.daf_pre || 0);
                    content += `
                        <div class="col-md-6">
                            <div class="detail-item p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Full Proses</span>
                                    <span class="fw-bold fs-5">${eff.full_proses}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">DAF Pre</span>
                                    <span class="fw-bold fs-5">${eff.daf_pre}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }

                content += `
                    </div>
                    <div class="mt-4 p-3 bg-primary bg-opacity-10 rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold text-primary">Total Volume</span>
                            <span class="fw-bold fs-4 text-primary">${total.toFixed(2)} m³</span>
                        </div>
                    </div>
                `;

                $('#modalDetailContent').html(content);
                new bootstrap.Modal(document.getElementById('detailModal')).show();
            };

            /* ─────────────────────────────────────────────
               DETAIL — DAILY
            ───────────────────────────────────────────── */
            window.showDetailHarian = function(id) {
                $.ajax({
                    url: `/api/wwtp/influent-harian/${id}`,
                    method: 'GET',
                    success: function(record) {
                        currentRecordId = id;
                        currentRecordType = 'daily';

                        if (canEditDeleteDaily(record.approval_status)) {
                            $('#btnDelete').show();
                        } else {
                            $('#btnDelete').hide();
                        }

                        const shiftMap = {
                            shift1: 'Shift 1',
                            shift2: 'Shift 2',
                            shift3: 'Shift 3'
                        };
                        const shiftLabel = shiftMap[record.shift] || record.shift;

                        let content = `
                            <h6 class="fw-bold mb-3 text-info mt-4">
                                <i class="fas fa-gauge me-2"></i>Data Debit & Running WWTP
                            </h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="info-box p-3 bg-light rounded">
                                        <p class="text-muted small mb-1">Debit WWTP 1</p>
                                        <p class="fw-bold mb-0">${record.debit1 || '-'} m³/h</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box p-3 bg-light rounded">
                                        <p class="text-muted small mb-1">Running WWTP 1</p>
                                        <p class="fw-bold mb-0">${record.running_wwtp1 || '-'}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box p-3 bg-light rounded">
                                        <p class="text-muted small mb-1">Debit WWTP 2</p>
                                        <p class="fw-bold mb-0">${record.debit2 || '-'} m³/h</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box p-3 bg-light rounded">
                                        <p class="text-muted small mb-1">Running WWTP 2</p>
                                        <p class="fw-bold mb-0">${record.running_wwtp2 || '-'}</p>
                                    </div>
                                </div>
                            </div>
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
                                        <span class="badge bg-secondary">${shiftLabel}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box p-3 bg-light rounded">
                                        <p class="text-muted small mb-1">Tipe</p>
                                        <span class="badge bg-info">Data Harian</span>
                                    </div>
                                </div>
                            </div>
                            <h6 class="fw-bold mb-3 text-primary">Volume Detail (m³)</h6>
                            <div class="row g-3">
                        `;

                        const items = [{
                                label: 'Pit Sparta',
                                value: record.pit_sparta !== null && record.pit_sparta !==
                                    undefined ? Math.max(0, parseFloat(record.pit_sparta ||
                                            0) -
                                        parseFloat(record.pit_sparta_awal)).toFixed(2) : null
                            },
                            {
                                label: 'Pit Garam',
                                value: record.pit_garam !== null && record.pit_garam !==
                                    undefined ? Math.max(0, parseFloat(record.pit_garam ||
                                            0) -
                                        parseFloat(record.pit_garam_awal)).toFixed(2) : null
                            },
                            {
                                label: 'Pit Domestik',
                                value: record.pit_domestik !== null && record.pit_domestik !==
                                    undefined ? Math.max(0, parseFloat(record
                                            .pit_domestik || 0) -
                                        parseFloat(record.pit_domestik_awal)).toFixed(2) : null
                            },
                            {
                                label: 'Pit Produksi Step 3',
                                value: record.pit_produksi_step3 !== null && record
                                    .pit_produksi_step3 !== undefined ? Math.max(0, parseFloat(
                                        record.pit_produksi_step3 || 0) - parseFloat(
                                        record
                                        .pit_produksi_step3_awal)).toFixed(2) : null
                            },
                            {
                                label: 'Pit Storage',
                                value: record.pit_storage !== null && record.pit_storage !==
                                    undefined ? Math.max(0, parseFloat(record
                                            .pit_storage || 0) -
                                        parseFloat(record.pit_storage_awal)).toFixed(2) : null
                            },
                            {
                                label: 'Pit Proses WWTP 2',
                                value: record.pit_proses_wwtp2 !== null && record
                                    .pit_proses_wwtp2 !== undefined ? Math.max(0, parseFloat(
                                        record.pit_proses_wwtp2 || 0) - parseFloat(
                                        record
                                        .pit_proses_wwtp2_awal)).toFixed(2) : null
                            },
                            {
                                label: 'Pit Outlet',
                                value: record.pit_outlet !== null && record.pit_outlet !==
                                    undefined ? Math.max(0, parseFloat(record.pit_outlet ||
                                            0) -
                                        parseFloat(record.pit_outlet_awal)).toFixed(2) : null
                            },
                            {
                                label: 'Pit Boiler',
                                value: record.pit_boiler !== null && record.pit_boiler !==
                                    undefined ? Math.max(0, parseFloat(record.pit_boiler ||
                                            0) -
                                        parseFloat(record.pit_boiler_awal)).toFixed(2) : null
                            },
                        ];

                        let total = 0;
                        items.forEach(function(item) {
                            if (item.value !== null && item.value !== undefined) {
                                total += parseFloat(item.value);
                                content += `
                            <div class="col-md-6">
                                <div class="detail-item p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">${item.label}</span>
                                        <span class="fw-bold fs-5">${item.value}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                            }
                        });

                        content += `
                    </div>
                    <div class="mt-4 p-3 bg-primary bg-opacity-10 rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold text-primary">Total Volume</span>
                            <span class="fw-bold fs-4 text-primary">${total.toFixed(2)} m³</span>
                        </div>
                    </div>
                `;

                        $('#modalDetailContent').html(content);
                        new bootstrap.Modal(document.getElementById('detailModal')).show();
                    },
                    error: function() {
                        showError('Gagal memuat detail data');
                    }
                });
            };

            /* ─────────────────────────────────────────────
               DELETE
            ───────────────────────────────────────────── */
            $('#btnDelete').on('click', function() {
                if (currentRecordId && confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                    if (currentRecordType === 'daily') deleteHarian(currentRecordId);
                    else deleteRecord(currentRecordId);
                }
            });

            window.confirmDelete = function(id) {
                if (confirm('Apakah Anda yakin ingin menghapus data ini?')) deleteRecord(id);
            };

            window.confirmDeleteHarian = function(id) {
                if (confirm('Apakah Anda yakin ingin menghapus data harian ini?')) deleteHarian(id);
            };

            function deleteRecord(id) {
                $.ajax({
                    url: `/api/wwtp/${id}`,
                    method: 'DELETE',
                    success: function() {
                        $('#detailModal').modal('hide');
                        showSuccess('Data berhasil dihapus');
                        loadWeekly(weeklyState.currentPage);
                        loadStatistics();
                    },
                    error: function() {
                        showError('Gagal menghapus data');
                    }
                });
            }

            function deleteHarian(id) {
                $.ajax({
                    url: `/api/wwtp/influent-harian/${id}`,
                    method: 'DELETE',
                    success: function() {
                        $('#detailModal').modal('hide');
                        showSuccess('Data harian berhasil dihapus');
                        loadDataHarian(dailyState.currentPage);
                    },
                    error: function() {
                        showError('Gagal menghapus data harian');
                    }
                });
            }

            /* ─────────────────────────────────────────────
               EDIT — HARIAN
            ───────────────────────────────────────────── */
            window.editHarian = function(id) {
                $.ajax({
                    url: `/api/wwtp/influent-harian/${id}`,
                    method: 'GET',
                    success: function(record) {
                        $('#edit_id').val(record.id);
                        $('#edit_tanggal').val(record.tanggal);
                        $('#edit_shift').val(record.shift);
                        $('#edit_debit1').val(record.debit1);
                        $('#edit_running_wwtp1').val(record.running_wwtp1);
                        $('#edit_debit2').val(record.debit2);
                        $('#edit_running_wwtp2').val(record.running_wwtp2);
                        $('#edit_pit_sparta').val(record.pit_sparta);
                        $('#edit_pit_sparta_awal').val(record.pit_sparta_awal);
                        $('#edit_pit_garam').val(record.pit_garam);
                        $('#edit_pit_garam_awal').val(record.pit_garam_awal);
                        $('#edit_pit_domestik').val(record.pit_domestik);
                        $('#edit_pit_domestik_awal').val(record.pit_domestik_awal);
                        $('#edit_pit_produksi_step3').val(record.pit_produksi_step3);
                        $('#edit_pit_produksi_step3_awal').val(record.pit_produksi_step3_awal);
                        $('#edit_pit_storage').val(record.pit_storage);
                        $('#edit_pit_storage_awal').val(record.pit_storage_awal);
                        $('#edit_pit_proses_wwtp2').val(record.pit_proses_wwtp2);
                        $('#edit_pit_proses_wwtp2_awal').val(record.pit_proses_wwtp2_awal);
                        $('#edit_pit_outlet').val(record.pit_outlet);
                        $('#edit_pit_outlet_awal').val(record.pit_outlet_awal);
                        $('#edit_pit_boiler').val(record.pit_boiler);
                        $('#edit_pit_boiler_awal').val(record.pit_boiler_awal);
                        new bootstrap.Modal(document.getElementById('editModal')).show();
                    },
                    error: function() {
                        showError('Gagal memuat data untuk edit');
                    }
                });
            };

            $('#btnSaveEdit').on('click', function() {
                const id = $('#edit_id').val();
                const formData = {
                    tanggal: $('#edit_tanggal').val(),
                    shift: $('#edit_shift').val(),
                    debit1: $('#edit_debit1').val() || null,
                    running_wwtp1: $('#edit_running_wwtp1').val() || null,
                    debit2: $('#edit_debit2').val() || null,
                    running_wwtp2: $('#edit_running_wwtp2').val() || null,
                    pit_sparta: $('#edit_pit_sparta').val(),
                    pit_sparta_awal: $('#edit_pit_sparta_awal').val() !== "" ? $(
                        '#edit_pit_sparta_awal').val() : null,
                    pit_garam: $('#edit_pit_garam').val(),
                    pit_garam_awal: $('#edit_pit_garam_awal').val() !== "" ? $('#edit_pit_garam_awal')
                        .val() : null,
                    pit_domestik: $('#edit_pit_domestik').val(),
                    pit_domestik_awal: $('#edit_pit_domestik_awal').val() !== "" ? $(
                        '#edit_pit_domestik_awal').val() : null,
                    pit_produksi_step3: $('#edit_pit_produksi_step3').val() || null,
                    pit_produksi_step3_awal: $('#edit_pit_produksi_step3_awal').val() !== "" ? $(
                        '#edit_pit_produksi_step3_awal').val() : null,
                    pit_storage: $('#edit_pit_storage').val() || null,
                    pit_storage_awal: $('#edit_pit_storage_awal').val() !== "" ? $(
                        '#edit_pit_storage_awal').val() : null,
                    pit_proses_wwtp2: $('#edit_pit_proses_wwtp2').val() || null,
                    pit_proses_wwtp2_awal: $('#edit_pit_proses_wwtp2_awal').val() !== "" ? $(
                        '#edit_pit_proses_wwtp2_awal').val() : null,
                    pit_outlet: $('#edit_pit_outlet').val() || null,
                    pit_outlet_awal: $('#edit_pit_outlet_awal').val() !== "" ? $(
                        '#edit_pit_outlet_awal').val() : null,
                    pit_boiler: $('#edit_pit_boiler').val() || null,
                    pit_boiler_awal: $('#edit_pit_boiler_awal').val() !== "" ? $(
                        '#edit_pit_boiler_awal').val() : null,
                };

                $.ajax({
                    url: `/api/wwtp/influent-harian/${id}`,
                    method: 'PUT',
                    data: formData,
                    success: function() {
                        $('#editModal').modal('hide');
                        showSuccess('Data berhasil diperbarui');
                        loadDataHarian(dailyState.currentPage);
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON;
                        let message = 'Terjadi kesalahan saat memperbarui data!';
                        if (error && error.message) message = error.message;
                        else if (error && error.errors) message = Object.values(error.errors)
                            .flat().join('\n');
                        alert(message);
                    }
                });
            });

            /* ─────────────────────────────────────────────
               EDIT — WEEKLY
            ───────────────────────────────────────────── */
            window.editWeekly = function(id) {
                const record = weeklyState.rows.find(r => r.id === id);
                if (!record) return;

                $('#edit_weekly_id').val(record.id);
                $('#edit_weekly_tanggal').val(record.tanggal);
                $('#edit_weekly_kategori').val(record.kategori);
                $('#edit_weekly_kategori_display').val(
                    record.kategori === 'influent' ? 'Influent (Air Masuk)' : 'Effluent (Air Keluar)'
                );

                if (record.kategori === 'influent') {
                    $('#edit_weekly_influent_form').show();
                    $('#edit_weekly_effluent_form').hide();
                    if (record.influent) {
                        $('#edit_weekly_pit_sparta').val(record.influent.pit_sparta);
                        $('#edit_weekly_pit_garam').val(record.influent.pit_garam);
                        $('#edit_weekly_pit_domestik').val(record.influent.pit_domestik);
                        $('#edit_weekly_pit_produksi_step3').val(record.influent.pit_produksi_step3);
                        $('#edit_weekly_pit_storage').val(record.influent.pit_storage);
                        $('#edit_weekly_pit_proses_wwtp2').val(record.influent.pit_proses_wwtp2);
                        $('#edit_weekly_pit_outlet').val(record.influent.pit_outlet);
                        $('#edit_weekly_pit_boiler').val(record.influent.pit_boiler);
                    }
                } else {
                    $('#edit_weekly_influent_form').hide();
                    $('#edit_weekly_effluent_form').show();
                    if (record.effluent) {
                        $('#edit_weekly_full_proses').val(record.effluent.full_proses);
                        $('#edit_weekly_daf_pre').val(record.effluent.daf_pre);
                    }
                }

                new bootstrap.Modal(document.getElementById('editWeeklyModal')).show();
            };

            $('#btnSaveEditWeekly').on('click', function() {
                const id = $('#edit_weekly_id').val();
                const kategori = $('#edit_weekly_kategori').val();
                const formData = {
                    tanggal: $('#edit_weekly_tanggal').val()
                };

                if (kategori === 'influent') {
                    formData.pit_sparta = $('#edit_weekly_pit_sparta').val();
                    formData.pit_garam = $('#edit_weekly_pit_garam').val();
                    formData.pit_domestik = $('#edit_weekly_pit_domestik').val();
                    formData.pit_produksi_step3 = $('#edit_weekly_pit_produksi_step3').val() || null;
                    formData.pit_storage = $('#edit_weekly_pit_storage').val() || null;
                    formData.pit_proses_wwtp2 = $('#edit_weekly_pit_proses_wwtp2').val() || null;
                    formData.pit_outlet = $('#edit_weekly_pit_outlet').val() || null;
                    formData.pit_boiler = $('#edit_weekly_pit_boiler').val() || null;
                } else {
                    formData.full_proses = $('#edit_weekly_full_proses').val();
                    formData.daf_pre = $('#edit_weekly_daf_pre').val();
                }

                $.ajax({
                    url: `/api/wwtp/${id}`,
                    method: 'PUT',
                    data: formData,
                    success: function() {
                        $('#editWeeklyModal').modal('hide');
                        showSuccess('Data mingguan berhasil diperbarui');
                        loadWeekly(weeklyState.currentPage);
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON;
                        let message = 'Terjadi kesalahan saat memperbarui data!';
                        if (error && error.message) message = error.message;
                        else if (error && error.errors) message = Object.values(error.errors)
                            .flat().join('\n');
                        alert(message);
                    }
                });
            });

            /* ─────────────────────────────────────────────
               HELPERS
            ───────────────────────────────────────────── */
            function formatDate(dateString) {
                return new Date(dateString).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            function showSuccess(message) {
                alert(message);
            }

            function showError(message) {
                alert(message);
            }

            /* ─────────────────────────────────────────────
               INIT
            ───────────────────────────────────────────── */
            loadStatistics();
            loadWeekly(1);
        });
    </script>

@endsection
