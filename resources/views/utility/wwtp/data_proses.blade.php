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
                            <input type="text" class="form-control form-control-lg" id="searchData" placeholder="Cari berdasarkan tanggal atau kategori...">
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
                            <button class="nav-link active" id="weekly-tab" data-bs-toggle="tab" data-bs-target="#weekly" type="button">
                                <i class="fas fa-calendar-alt me-2"></i>Data Mingguan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily" type="button">
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
                                                <i class="fas fa-info-circle me-2"></i>Data harian akan ditampilkan di sini
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
                        <!-- Content will be loaded dynamically -->
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

        <!-- update modal -->
        <!-- Edit Modal -->
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

                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_tanggal" class="form-label fw-semibold">
                                        Tanggal <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_shift" class="form-label fw-semibold">
                                        Shift <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="edit_shift" name="shift" required>
                                        <option value="">-- Pilih Shift --</option>
                                        <option value="shift1">Shift 1 (06:00 - 14:00)</option>
                                        <option value="shift2">Shift 2 (14:00 - 22:00)</option>
                                        <option value="shift3">Shift 3 (22:00 - 06:00)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Debit & Running WWTP -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 text-info">
                                    <i class="mdi mdi-gauge me-2"></i>Data Debit & Running WWTP
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="edit_debit1" class="form-label">Debit WWTP 1</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="edit_debit1" name="debit1" min="0" placeholder="0.00">
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
                                            <input type="number" step="0.01" class="form-control" id="edit_debit2" name="debit2" min="0" placeholder="0.00">
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

                            <!-- Influent Data -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 text-primary">
                                    <i class="mdi mdi-water-pump me-2"></i>Data Influent
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="edit_pit_sparta" class="form-label fw-semibold">
                                            Pit Sparta <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="edit_pit_sparta" name="pit_sparta" min="0" required>
                                            <span class="input-group-text">m³</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_pit_garam" class="form-label fw-semibold">
                                            Pit Garam <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="edit_pit_garam" name="pit_garam" min="0" required>
                                            <span class="input-group-text">m³</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_pit_domestik" class="form-label fw-semibold">
                                            Pit Domestik <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="edit_pit_domestik" name="pit_domestik" min="0" required>
                                            <span class="input-group-text">m³</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="edit_pit_produksi_step3" class="form-label">Pit Produksi Step 3</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="edit_pit_produksi_step3" name="pit_produksi_step3" min="0">
                                            <span class="input-group-text">m³</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="edit_pit_storage" class="form-label">Pit Storage</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="edit_pit_storage" name="pit_storage" min="0">
                                            <span class="input-group-text">m³</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="edit_pit_proses_wwtp2" class="form-label">Pit Proses WWTP 2</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="edit_pit_proses_wwtp2" name="pit_proses_wwtp2" min="0">
                                            <span class="input-group-text">m³</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="edit_pit_outlet" class="form-label">Pit Outlet</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="edit_pit_outlet" name="pit_outlet" min="0">
                                            <span class="input-group-text">m³</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="edit_pit_boiler" class="form-label">Pit Boiler</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="edit_pit_boiler" name="pit_boiler" min="0">
                                            <span class="input-group-text">m³</span>
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


        <!-- //modal upadate weekly -->
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

                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_weekly_tanggal" class="form-label fw-semibold">
                                        Tanggal <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="edit_weekly_tanggal" name="tanggal" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Kategori</label>
                                    <input type="text" class="form-control" id="edit_weekly_kategori_display" readonly>
                                </div>
                            </div>

                            <!-- Influent Form -->
                            <div id="edit_weekly_influent_form" style="display: none;">
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3 text-primary">
                                        <i class="mdi mdi-water-pump me-2"></i>Data Influent Mingguan
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="edit_weekly_pit_sparta" class="form-label fw-semibold">
                                                Pit Sparta <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="edit_weekly_pit_sparta" name="pit_sparta" min="0">
                                                <span class="input-group-text">m³</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="edit_weekly_pit_garam" class="form-label fw-semibold">
                                                Pit Garam <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="edit_weekly_pit_garam" name="pit_garam" min="0">
                                                <span class="input-group-text">m³</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="edit_weekly_pit_domestik" class="form-label fw-semibold">
                                                Pit Domestik <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="edit_weekly_pit_domestik" name="pit_domestik" min="0">
                                                <span class="input-group-text">m³</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="edit_weekly_pit_produksi_step3" class="form-label">Pit Produksi Step 3</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="edit_weekly_pit_produksi_step3" name="pit_produksi_step3" min="0">
                                                <span class="input-group-text">m³</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="edit_weekly_pit_storage" class="form-label">Pit Storage</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="edit_weekly_pit_storage" name="pit_storage" min="0">
                                                <span class="input-group-text">m³</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="edit_weekly_pit_proses_wwtp2" class="form-label">Pit Proses WWTP 2</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="edit_weekly_pit_proses_wwtp2" name="pit_proses_wwtp2" min="0">
                                                <span class="input-group-text">m³</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="edit_weekly_pit_outlet" class="form-label">Pit Outlet</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="edit_weekly_pit_outlet" name="pit_outlet" min="0">
                                                <span class="input-group-text">m³</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="edit_weekly_pit_boiler" class="form-label">Pit Boiler</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="edit_weekly_pit_boiler" name="pit_boiler" min="0">
                                                <span class="input-group-text">m³</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Effluent Form -->
                            <div id="edit_weekly_effluent_form" style="display: none;">
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3 text-success">
                                        <i class="mdi mdi-water-check me-2"></i>Data Effluent Mingguan
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="edit_weekly_full_proses" class="form-label fw-semibold">
                                                Full Proses <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="edit_weekly_full_proses" name="full_proses" min="0">
                                                <span class="input-group-text">m³</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="edit_weekly_daf_pre" class="form-label fw-semibold">
                                                DAF Pre <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="edit_weekly_daf_pre" name="daf_pre" min="0">
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
</style>

<script>
    $(document).ready(function() {
        let allData = [];
        let currentRecordId = null;
        let currentRecordType = 'weekly'; // Track whether we're deleting weekly or daily data
        const userJabatan = "{{ Auth::user()->jabatan }}";
        const canEditDelete = userJabatan !== 'operator';
        // Load data saat halaman pertama kali dimuat
        loadData();

        // Event listeners untuk filter
        $('#filterKategori, #filterBulan, #searchData').on('change keyup', function() {
            filterData();
        });

        $('#btnReset').on('click', function() {
            $('#filterKategori').val('');
            $('#filterBulan').val('');
            $('#searchData').val('');
            filterData();
        });

        // Delete button handler - Updated untuk handle kedua tipe data
        $('#btnDelete').on('click', function() {
            if (currentRecordId && confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                if (currentRecordType === 'daily') {
                    deleteHarian(currentRecordId);
                } else {
                    deleteRecord(currentRecordId);
                }
            }
        });

        function loadData() {
            $.ajax({
                url: '/api/wwtp',
                method: 'GET',
                success: function(response) {
                    allData = response;
                    updateStatistics();
                    filterData();
                },
                error: function(xhr) {
                    console.error('Error loading data:', xhr);
                    showError('Gagal memuat data');
                }
            });
        }

        function updateStatistics() {
            const total = allData.length;
            const influent = allData.filter(d => d.kategori === 'influent').length;
            const effluent = allData.filter(d => d.kategori === 'effluent').length;

            // Hitung data minggu ini
            const now = new Date();
            const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
            const weekData = allData.filter(d => {
                const date = new Date(d.tanggal);
                return date >= startOfWeek;
            }).length;

            $('#totalRecords').text(total);
            $('#influentRecords').text(influent);
            $('#effluentRecords').text(effluent);
            $('#weekRecords').text(weekData);
        }

        function filterData() {
            const kategori = $('#filterKategori').val();
            const bulan = $('#filterBulan').val();
            const search = $('#searchData').val().toLowerCase();

            let filtered = allData.filter(item => {
                // Filter kategori
                if (kategori && item.kategori !== kategori) return false;

                // Filter bulan
                if (bulan) {
                    const itemMonth = item.tanggal.substring(0, 7);
                    if (itemMonth !== bulan) return false;
                }

                // Filter search
                if (search) {
                    const tanggal = item.tanggal.toLowerCase();
                    const kat = item.kategori.toLowerCase();
                    if (!tanggal.includes(search) && !kat.includes(search)) return false;
                }

                return true;
            });

            renderTable(filtered);
        }

        function renderTable(data) {
            const tbody = $('#weeklyTableBody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append(`
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="fas fa-inbox me-2"></i>Tidak ada data yang ditemukan
                    </td>
                </tr>
            `);
                return;
            }

            data.forEach(item => {
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

                    detailData = `Sparta: ${inf.pit_sparta}m³, Garam: ${inf.pit_garam}m³, Domestik: ${inf.pit_domestik}m³`;
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
                        <!-- Tombol Lihat Detail -->
                        <button class="btn btn-sm btn-outline-primary me-1" 
                                onclick="showDetail(${item.id})"
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="Lihat Detail">
                            <i class="mdi mdi-eye"></i>
                        </button>
                        ${canEditDelete ? `
                        <!-- Tombol Edit - Tidak untuk Operator -->
                        <button class="btn btn-sm btn-outline-warning me-1" 
                                onclick="editWeekly(${item.id})"
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="Edit Data">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <!-- Tombol Hapus - Tidak untuk Operator -->
                        <button class="btn btn-sm btn-outline-danger" 
                                onclick="confirmDelete(${item.id})"
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="Hapus Data">
                            <i class="mdi mdi-trash-can"></i>
                        </button>
                        ` : ''}
                    </td>

                </tr>
            `);
            });
        }

        window.showDetail = function(id) {
            const record = allData.find(r => r.id === id);
            if (!record) return;

            currentRecordId = id;
            currentRecordType = 'weekly';
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));

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
                    }
                ];

                items.forEach(item => {
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
            modal.show();
        }

        window.confirmDelete = function(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                deleteRecord(id);
            }
        }

        function deleteRecord(id) {
            $.ajax({
                url: `/api/wwtp/${id}`,
                method: 'DELETE',
                success: function(response) {
                    $('#detailModal').modal('hide');
                    showSuccess('Data berhasil dihapus');
                    loadData();
                },
                error: function(xhr) {
                    console.error('Error deleting record:', xhr);
                    showError('Gagal menghapus data');
                }
            });
        }

        function formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        function showSuccess(message) {
            alert(message);
        }

        function showError(message) {
            alert(message);
        }

        // Load data harian
        function loadDataHarian() {
            $.ajax({
                url: '/api/wwtp/influent-harian',
                method: 'GET',
                success: function(response) {
                    renderDailyTable(response);
                },
                error: function(xhr) {
                    console.error('Error loading daily data:', xhr);
                    showError('Gagal memuat data harian');
                }
            });
        }

        // Render tabel harian
        function renderDailyTable(data) {
            const tbody = $('#dailyTableBody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append(`
            <tr>
                <td colspan="5" class="text-center py-4 text-muted">
                    <i class="fas fa-inbox me-2"></i>Tidak ada data harian
                </td>
            </tr>
        `);
                return;
            }

            data.forEach(item => {
                let shiftLabel = '';
                switch (item.shift) {
                    case 'shift1':
                        shiftLabel = 'Shift 1';
                        break;
                    case 'shift2':
                        shiftLabel = 'Shift 2';
                        break;
                    case 'shift3':
                        shiftLabel = 'Shift 3';
                        break;
                }

                const totalVolume = parseFloat(item.pit_sparta || 0) +
                    parseFloat(item.pit_garam || 0) +
                    parseFloat(item.pit_domestik || 0) +
                    parseFloat(item.pit_produksi_step3 || 0) +
                    parseFloat(item.pit_storage || 0) +
                    parseFloat(item.pit_proses_wwtp2 || 0) +
                    parseFloat(item.pit_outlet || 0) +
                    parseFloat(item.pit_boiler || 0);

                const detailData = `Sparta: ${item.pit_sparta}m³, Debit WWTP1: ${item.debit1 || '-'}m³/h, Debit WWTP2: ${item.debit2 || '-'}m³/h`;
                tbody.append(`
            <tr class="data-row">
                <td>${formatDate(item.tanggal)}</td>
                <td><span class="badge bg-secondary">${shiftLabel}</span></td>
                <td><small class="text-muted">${detailData}</small></td>
                <td class="text-end fw-bold">${totalVolume.toFixed(2)} m³</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="showDetailHarian(${item.id})" data-bs-toggle="tooltip" title="Lihat Detail">
                    <i class="mdi mdi-eye"></i>
                    </button>
                   ${canEditDelete ? `
                        <button class="btn btn-sm btn-outline-warning me-1" onclick="editHarian(${item.id})" data-bs-toggle="tooltip" title="Edit Data">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteHarian(${item.id})" data-bs-toggle="tooltip" title="Hapus Data">
                            <i class="mdi mdi-trash-can"></i>
                        </button>
                        ` : ''}
                </td>
            </tr>
        `);
            });
        }

        // Show detail harian
        window.showDetailHarian = function(id) {
            $.ajax({
                url: `/api/wwtp/influent-harian/${id}`,
                method: 'GET',
                success: function(record) {
                    currentRecordId = id;
                    currentRecordType = 'daily';
                    const modal = new bootstrap.Modal(document.getElementById('detailModal'));

                    let shiftLabel = '';
                    switch (record.shift) {
                        case 'shift1':
                            shiftLabel = 'Shift 1';
                            break;
                        case 'shift2':
                            shiftLabel = 'Shift 2';
                            break;
                        case 'shift3':
                            shiftLabel = 'Shift 3';
                            break;
                    }

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
                            value: record.pit_sparta
                        },
                        {
                            label: 'Pit Garam',
                            value: record.pit_garam
                        },
                        {
                            label: 'Pit Domestik',
                            value: record.pit_domestik
                        },
                        {
                            label: 'Pit Produksi Step 3',
                            value: record.pit_produksi_step3
                        },
                        {
                            label: 'Pit Storage',
                            value: record.pit_storage
                        },
                        {
                            label: 'Pit Proses WWTP 2',
                            value: record.pit_proses_wwtp2
                        },
                        {
                            label: 'Pit Outlet',
                            value: record.pit_outlet
                        },
                        {
                            label: 'Pit Boiler',
                            value: record.pit_boiler
                        }
                    ];

                    let total = 0;
                    items.forEach(item => {
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
                    modal.show();
                },
                error: function(xhr) {
                    console.error('Error loading detail:', xhr);
                    showError('Gagal memuat detail data');
                }
            });
        }

        //// Confirm delete harian
        window.confirmDeleteHarian = function(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data harian ini?')) {
                deleteHarian(id);
            }
        }

        // Delete harian
        function deleteHarian(id) {
            $.ajax({
                url: `/api/wwtp/influent-harian/${id}`,
                method: 'DELETE',
                success: function(response) {
                    $('#detailModal').modal('hide');
                    showSuccess('Data harian berhasil dihapus');
                    loadDataHarian();
                },
                error: function(xhr) {
                    console.error('Error deleting daily data:', xhr);
                    showError('Gagal menghapus data harian');
                }
            });
        }

        //Updatedata harian
        // Edit harian
        window.editHarian = function(id) {
            $.ajax({
                url: `/api/wwtp/influent-harian/${id}`,
                method: 'GET',
                success: function(record) {
                    // Fill form dengan data
                    $('#edit_id').val(record.id);
                    $('#edit_tanggal').val(record.tanggal);
                    $('#edit_shift').val(record.shift);
                    $('#edit_debit1').val(record.debit1);
                    $('#edit_running_wwtp1').val(record.running_wwtp1);
                    $('#edit_debit2').val(record.debit2);
                    $('#edit_running_wwtp2').val(record.running_wwtp2);
                    $('#edit_pit_sparta').val(record.pit_sparta);
                    $('#edit_pit_garam').val(record.pit_garam);
                    $('#edit_pit_domestik').val(record.pit_domestik);
                    $('#edit_pit_produksi_step3').val(record.pit_produksi_step3);
                    $('#edit_pit_storage').val(record.pit_storage);
                    $('#edit_pit_proses_wwtp2').val(record.pit_proses_wwtp2);
                    $('#edit_pit_outlet').val(record.pit_outlet);
                    $('#edit_pit_boiler').val(record.pit_boiler);

                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('editModal'));
                    modal.show();
                },
                error: function(xhr) {
                    console.error('Error loading data:', xhr);
                    showError('Gagal memuat data untuk edit');
                }
            });
        }

        // Save edit
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
                pit_garam: $('#edit_pit_garam').val(),
                pit_domestik: $('#edit_pit_domestik').val(),
                pit_produksi_step3: $('#edit_pit_produksi_step3').val() || null,
                pit_storage: $('#edit_pit_storage').val() || null,
                pit_proses_wwtp2: $('#edit_pit_proses_wwtp2').val() || null,
                pit_outlet: $('#edit_pit_outlet').val() || null,
                pit_boiler: $('#edit_pit_boiler').val() || null
            };

            $.ajax({
                url: `/api/wwtp/influent-harian/${id}`,
                method: 'PUT',
                data: formData,
                success: function(response) {
                    $('#editModal').modal('hide');
                    showSuccess('Data berhasil diperbarui');
                    loadDataHarian();
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    let message = 'Terjadi kesalahan saat memperbarui data!';

                    if (error && error.message) {
                        message = error.message;
                    } else if (error && error.errors) {
                        message = Object.values(error.errors).flat().join('<br>');
                    }

                    alert(message);
                }
            });
        });



        //edit mingguan
        // Edit Weekly
        window.editWeekly = function(id) {
            const record = allData.find(r => r.id === id);
            if (!record) return;

            // Set basic data
            $('#edit_weekly_id').val(record.id);
            $('#edit_weekly_tanggal').val(record.tanggal);
            $('#edit_weekly_kategori').val(record.kategori);

            // Display kategori
            const kategoriDisplay = record.kategori === 'influent' ? 'Influent (Air Masuk)' : 'Effluent (Air Keluar)';
            $('#edit_weekly_kategori_display').val(kategoriDisplay);

            // Show/hide forms based on kategori
            if (record.kategori === 'influent') {
                $('#edit_weekly_influent_form').show();
                $('#edit_weekly_effluent_form').hide();

                // Fill influent data
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

                // Fill effluent data
                if (record.effluent) {
                    $('#edit_weekly_full_proses').val(record.effluent.full_proses);
                    $('#edit_weekly_daf_pre').val(record.effluent.daf_pre);
                }
            }

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editWeeklyModal'));
            modal.show();
        }

        // Save edit weekly
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
                success: function(response) {
                    $('#editWeeklyModal').modal('hide');
                    showSuccess('Data mingguan berhasil diperbarui');
                    loadData(); // Reload data mingguan
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    let message = 'Terjadi kesalahan saat memperbarui data!';

                    if (error && error.message) {
                        message = error.message;
                    } else if (error && error.errors) {
                        message = Object.values(error.errors).flat().join('<br>');
                    }

                    alert(message);
                }
            });
        });

        // Event listener untuk tab daily
        $('#daily-tab').on('click', function() {
            loadDataHarian();
        });

        // Load data harian jika tab daily sudah aktif saat page load
        if ($('#daily-tab').hasClass('active')) {
            loadDataHarian();
        }
    });
</script>
@endsection