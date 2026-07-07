@extends('layouts.app')

@section('title', 'WWTP Performance Monitoring')

@section('content')

    <div class="page-content">
        <div class="container-fluid">
            <div class="container-fluid px-4 py-5">

                <!-- Header -->
                <div class="mb-5">
                    <h1 class="display-5 fw-bold text-primary mb-2">
                        <i class="fas fa-chart-line me-3"></i>WWTP Performance Monitoring
                    </h1>
                    <p class="text-muted fs-5">Wastewater Treatment Plant - Performance Dashboard</p>
                </div>

                <!-- Statistics Cards -->
                <div class="row g-4 mb-5">
                    <div class="col-lg-3 col-md-6">
                        <div class="card border-0 shadow-sm h-100 stat-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                                        <i class="fas fa-flask fs-3"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1 small">Total Performance</p>
                                        <h3 class="fw-bold mb-0" id="totalPerformance">0</h3>
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
                                        <i class="fas fa-water fs-3"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1 small">Total PH Records</p>
                                        <h3 class="fw-bold mb-0" id="totalPH">0</h3>
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
                                        <i class="fas fa-vial fs-3"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1 small">Total Sample</p>
                                        <h3 class="fw-bold mb-0" id="totalSample">0</h3>
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
                                        <i class="fas fa-calendar-week fs-3"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1 small">This Week</p>
                                        <h3 class="fw-bold mb-0" id="weekPerformance">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="row mb-4">
                    <div class="col-12 d-flex gap-2">
                        <a href="{{ url('/wwtp/form_performance') }}" class="btn btn-primary btn-lg px-4 shadow-sm">
                            <i class="fas fa-plus-circle me-2"></i>Tambah Data Performance
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
                                <label class="form-label small text-muted fw-semibold">Filter Jenis</label>
                                <select class="form-select form-select-lg" id="filterJenis">
                                    <option value="">Semua Jenis</option>
                                    <option value="equal">Equalisasi</option>
                                    <option value="outlet_anaerob">Outlet Anaerob</option>
                                    <option value="aerob">Aerob</option>
                                    <option value="daf">DAF</option>
                                    <option value="outlet">Outlet</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted fw-semibold">Filter Bulan</label>
                                <input type="month" class="form-control form-control-lg" id="filterBulan">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted fw-semibold">Cari Data</label>
                                <input type="text" class="form-control form-control-lg" id="searchData"
                                    placeholder="Cari berdasarkan minggu atau jenis...">
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
                                <button class="nav-link active" id="performance-tab" data-bs-toggle="tab"
                                    data-bs-target="#performance" type="button">
                                    <i class="fas fa-flask me-2"></i>Performance Mingguan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="ph-tab" data-bs-toggle="tab" data-bs-target="#ph"
                                    type="button">
                                    <i class="fas fa-water me-2"></i>PH Harian
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="sample-tab" data-bs-toggle="tab" data-bs-target="#sample"
                                    type="button">
                                    <i class="fas fa-vial me-2"></i>Data Sample
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content" id="dataTabsContent">

                            <!-- ===== Tab: Performance Mingguan ===== -->
                            <div class="tab-pane fade show active" id="performance" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="fw-semibold">Minggu</th>
                                                <th class="fw-semibold">Jenis</th>
                                                <th class="fw-semibold text-center">TSS (mg/L)</th>
                                                <th class="fw-semibold text-center">COD (mg/L)</th>
                                                <th class="fw-semibold text-center">Foto</th>
                                                <th class="fw-semibold text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="performanceTableBody">
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Performance Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                    <div class="text-muted small" id="performancePaginationInfo"></div>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0" id="performancePagination"></ul>
                                    </nav>
                                </div>
                            </div>

                            <!-- ===== Tab: PH Harian ===== -->
                            <div class="tab-pane fade" id="ph" role="tabpanel">
                                <!-- Filter PH -->
                                <div class="row g-3 mb-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted fw-semibold">Filter Bulan</label>
                                        <input type="month" class="form-control" id="filterBulanPH">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-outline-secondary w-100" id="btnResetPH">
                                            <i class="fas fa-redo me-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="fw-semibold">Tanggal</th>
                                                <th class="fw-semibold">Shift</th>
                                                <th class="fw-semibold text-center">Equalisasi 1</th>
                                                <th class="fw-semibold text-center">Equalisasi 2</th>
                                                <th class="fw-semibold text-center">Outlet</th>
                                                <th class="fw-semibold text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="phTableBody">
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    <i class="fas fa-info-circle me-2"></i>Data PH harian akan ditampilkan
                                                    di sini
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- PH Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                    <div class="text-muted small" id="phPaginationInfo"></div>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0" id="phPagination"></ul>
                                    </nav>
                                </div>
                            </div>

                            <!-- ===== Tab: Data Sample ===== -->
                            <div class="tab-pane fade" id="sample" role="tabpanel">
                                <!-- Filter Sample -->
                                <div class="row g-3 mb-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted fw-semibold">Filter Jenis Sampel</label>
                                        <select class="form-select" id="filterJenisSample">
                                            <option value="">Semua Jenis Sampel</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted fw-semibold">Filter Bulan</label>
                                        <input type="month" class="form-control" id="filterBulanSample">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted fw-semibold">Cari</label>
                                        <input type="text" class="form-control" id="searchSample"
                                            placeholder="Cari jenis sampel...">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-outline-secondary w-100" id="btnResetSample">
                                            <i class="fas fa-redo me-1"></i> Reset
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="fw-semibold">Tanggal</th>
                                                <th class="fw-semibold">Jenis Sampel</th>
                                                <th class="fw-semibold text-center">TSS <small
                                                        class="text-muted">(mg/L)</small></th>
                                                <th class="fw-semibold text-center">SV30 <small
                                                        class="text-muted">(mL/L)</small></th>
                                                <th class="fw-semibold text-center">pH</th>
                                                <th class="fw-semibold text-center">MLSS <small
                                                        class="text-muted">(mg/L)</small></th>
                                                <th class="fw-semibold text-center">SVI <small
                                                        class="text-muted">(mL/g)</small></th>
                                                <th class="fw-semibold text-center">DO <small
                                                        class="text-muted">(mg/L)</small></th>
                                                <th class="fw-semibold text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sampleTableBody">
                                            <tr>
                                                <td colspan="9" class="text-center py-5">
                                                    <div class="spinner-border text-warning" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Sample Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                    <div class="text-muted small" id="samplePaginationInfo"></div>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0" id="samplePagination"></ul>
                                    </nav>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================================================== --}}
            {{-- MODALS: Performance --}}
            {{-- =========================================================== --}}
            <div class="modal fade" id="detailPerformanceModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="fas fa-flask me-2"></i>Detail Performance WWTP</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4" id="modalPerformanceContent"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-danger" id="btnDeletePerformance"
                                style="display:none;">
                                <i class="fas fa-trash me-2"></i>Hapus Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editPerformanceModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Data Performance</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form id="editPerformanceForm" enctype="multipart/form-data">
                                <input type="hidden" id="edit_perf_id">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Jenis <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_perf_jenis" required>
                                            <option value="">-- Pilih Jenis --</option>
                                            <option value="equal">Equalisasi</option>
                                            <option value="outlet_anaerob">Outlet Anaerob</option>
                                            <option value="aerob">Aerob</option>
                                            <option value="daf">DAF</option>
                                            <option value="outlet">Outlet</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Minggu</label>
                                        <input type="week" class="form-control" id="edit_perf_week" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">TSS (mg/L) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" id="edit_perf_tss"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">COD (mg/L) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" id="edit_perf_cod"
                                            required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Foto Dokumentasi</label>
                                    <input type="file" class="form-control" id="edit_perf_foto" accept="image/*">
                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
                                    <div id="edit_perf_current_foto" class="mt-2"></div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-success" id="btnSavePerformance">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================================================== --}}
            {{-- MODALS: PH Harian --}}
            {{-- =========================================================== --}}
            <div class="modal fade" id="detailPHModal" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title"><i class="fas fa-water me-2"></i>Detail PH Harian</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4" id="modalPHContent"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-danger" id="btnDeletePH" style="display:none;">
                                <i class="fas fa-trash me-2"></i>Hapus Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editPHModal" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Data PH Harian</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form id="editPHForm">
                                <input type="hidden" id="edit_ph_id">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tanggal <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit_ph_tanggal" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Shift <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_ph_shift" required>
                                            <option value="">-- Pilih Shift --</option>
                                            <option value="shift1">Shift 1 (06:00 - 14:00)</option>
                                            <option value="shift2">Shift 2 (14:00 - 22:00)</option>
                                            <option value="shift3">Shift 3 (22:00 - 06:00)</option>
                                        </select>
                                    </div>
                                </div>
                                <h6 class="fw-bold mb-3 text-info">Nilai PH pada Berbagai Lokasi</h6>
                                <div class="row g-3">
                                    <div class="col-md-4"><label class="form-label">Equalisasi 1</label><input
                                            type="number" step="0.01" class="form-control"
                                            id="edit_ph_equalisasi_1"></div>
                                    <div class="col-md-4"><label class="form-label">Equalisasi 2</label><input
                                            type="number" step="0.01" class="form-control"
                                            id="edit_ph_equalisasi_2"></div>
                                    <div class="col-md-4"><label class="form-label">Netralisasi</label><input
                                            type="number" step="0.01" class="form-control" id="edit_ph_netralisasi">
                                    </div>
                                    <div class="col-md-4"><label class="form-label">Sedimentasi 1</label><input
                                            type="number" step="0.01" class="form-control"
                                            id="edit_ph_sedimentasi_1"></div>
                                    <div class="col-md-4"><label class="form-label">Sedimentasi 2</label><input
                                            type="number" step="0.01" class="form-control"
                                            id="edit_ph_sedimentasi_2"></div>
                                    <div class="col-md-4"><label class="form-label">Outlet Anaerob</label><input
                                            type="number" step="0.01" class="form-control"
                                            id="edit_ph_outlet_anaerob"></div>
                                    <div class="col-md-4"><label class="form-label">Aerob</label><input type="number"
                                            step="0.01" class="form-control" id="edit_ph_aerob"></div>
                                    <div class="col-md-4"><label class="form-label">Lumpur Aktif</label><input
                                            type="number" step="0.01" class="form-control"
                                            id="edit_ph_lumpur_aktif"></div>
                                    <div class="col-md-4"><label class="form-label">Clarifier 2</label><input
                                            type="number" step="0.01" class="form-control" id="edit_ph_clarifier_2">
                                    </div>
                                    <div class="col-md-4"><label class="form-label">Outlet</label><input type="number"
                                            step="0.01" class="form-control" id="edit_ph_outlet"></div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-success" id="btnSavePH">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================================================== --}}
            {{-- MODALS: Sample --}}
            {{-- =========================================================== --}}
            <div class="modal fade" id="detailSampleModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title"><i class="fas fa-vial me-2"></i>Detail Data Sample</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4" id="modalSampleContent"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-danger" id="btnDeleteSample" style="display:none;">
                                <i class="fas fa-trash me-2"></i>Hapus Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editSampleModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Data Sample</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form id="editSampleForm">
                                <input type="hidden" id="edit_sample_id">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tanggal <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit_sample_tanggal" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Jenis Sampel <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_sample_id_sampel" required>
                                            <option value="">-- Pilih Jenis Sampel --</option>
                                        </select>
                                    </div>
                                </div>
                                <h6 class="fw-bold mb-3 text-warning">Parameter Pengujian</h6>
                                <div class="row g-3">
                                    <div class="col-md-4"><label class="form-label">TSS (mg/L) <span
                                                class="text-danger">*</span></label><input type="number" step="0.01"
                                            min="0" class="form-control" id="edit_sample_tss" required></div>
                                    <div class="col-md-4"><label class="form-label">SV30 (mL/L) <span
                                                class="text-danger">*</span></label><input type="number" step="0.01"
                                            min="0" class="form-control" id="edit_sample_sv30" required></div>
                                    <div class="col-md-4"><label class="form-label">pH <span
                                                class="text-danger">*</span></label><input type="number" step="0.01"
                                            min="0" max="14" class="form-control" id="edit_sample_ph"
                                            required></div>
                                    <div class="col-md-4"><label class="form-label">MLSS (mg/L) <span
                                                class="text-danger">*</span></label><input type="number" step="0.01"
                                            min="0" class="form-control" id="edit_sample_mlss" required></div>
                                    <div class="col-md-4"><label class="form-label">SVI (mL/g) <span
                                                class="text-danger">*</span></label><input type="number" step="0.01"
                                            min="0" class="form-control" id="edit_sample_svl" required></div>
                                    <div class="col-md-4"><label class="form-label">DO (mg/L) <span
                                                class="text-danger">*</span></label><input type="number" step="0.01"
                                            min="0" class="form-control" id="edit_sample_do" required></div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-warning text-dark" id="btnSaveSample">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
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
                                Sludge,</strong> dan <strong>Chemical</strong> pada tanggal atau bulan yang dipilih.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Tipe Export</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="export_type" id="export_type_daily" value="daily" checked>
                                    <label class="form-check-label" for="export_type_daily">Harian</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="export_type" id="export_type_monthly" value="monthly">
                                    <label class="form-check-label" for="export_type_monthly">Bulanan</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3" id="input_tanggal_container">
                            <label class="form-label fw-bold text-muted small text-uppercase">Pilih Tanggal Laporan</label>
                            <input type="date" class="form-control form-control-lg" id="export_tanggal"
                                value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3 d-none" id="input_bulan_container">
                            <label class="form-label fw-bold text-muted small text-uppercase">Pilih Bulan Laporan</label>
                            <input type="month" class="form-control form-control-lg" id="export_bulan"
                                value="{{ date('Y-m') }}" max="{{ date('Y-m') }}">
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
            background-color: rgba(13, 110, 253, .05);
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
            transition: all .2s ease;
        }

        .detail-item:hover {
            border-color: #0d6efd !important;
            background-color: rgba(13, 110, 253, .05);
        }

        .foto-preview {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
            border-radius: 8px;
        }

        .badge-jenis {
            font-size: .85rem;
            padding: .5rem 1rem;
        }

        .param-badge {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 14px;
            min-width: 90px;
        }

        .param-badge .param-val {
            font-size: 1.1rem;
            font-weight: 700;
            color: #212529;
        }

        .param-badge .param-lbl {
            font-size: .72rem;
            color: #6c757d;
            margin-top: 2px;
        }

        .param-badge .param-unit {
            font-size: .7rem;
            color: #adb5bd;
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
               EXPORT EXCEL
            ───────────────────────────────────────────── */
            $('input[name="export_type"]').on('change', function() {
                if ($(this).val() === 'daily') {
                    $('#input_tanggal_container').removeClass('d-none');
                    $('#input_bulan_container').addClass('d-none');
                } else {
                    $('#input_tanggal_container').addClass('d-none');
                    $('#input_bulan_container').removeClass('d-none');
                }
            });

            $('#btnExport').on('click', function() {
                $('#exportExcelModal').modal('show');
            });

            $('#btnProcessExport').on('click', function() {
                const type = $('input[name="export_type"]:checked').val();
                if (type === 'daily') {
                    const tanggal = $('#export_tanggal').val();
                    if (!tanggal) {
                        Swal.fire('Peringatan', 'Silakan pilih tanggal terlebih dahulu', 'warning');
                        return;
                    }
                    window.open(`{{ route('wwtp.export') }}?tanggal=${tanggal}`, '_blank');
                } else {
                    const bulanVal = $('#export_bulan').val();
                    if (!bulanVal) {
                        Swal.fire('Peringatan', 'Silakan pilih bulan terlebih dahulu', 'warning');
                        return;
                    }
                    const parts = bulanVal.split('-');
                    const year = parts[0];
                    const month = parseInt(parts[1], 10);
                    window.open(`{{ route('wwtp.export-monthly') }}?month=${month}&year=${year}`, '_blank');
                }
                $('#exportExcelModal').modal('hide');
            });


            /* ─────────────────────────────────────────────
               STATE — one object per tab
            ───────────────────────────────────────────── */
            const perfState = {
                currentPage: 1,
                lastPage: 1,
                total: 0,
                jenis: '',
                bulan: '',
                search: '',
                rows: [] // current page rows (for edit lookup)
            };

            const phState = {
                currentPage: 1,
                lastPage: 1,
                total: 0,
                bulan: '',
                rows: []
            };

            const sampleState = {
                currentPage: 1,
                lastPage: 1,
                total: 0,
                bulan: '',
                idSampel: '',
                search: '',
                rows: []
            };

            let currentRecordId = null;
            let allJenisSampel = [];

            /* ─────────────────────────────────────────────
               STATISTICS (lightweight separate call)
            ───────────────────────────────────────────── */
            function loadStatistics() {
                $.ajax({
                    url: '/api/wwtp-performance/dashboard/statistics',
                    method: 'GET',
                    success: function(res) {
                        $('#totalPerformance').text(res.total_records ?? 0);
                        $('#weekPerformance').text(res.total_records_this_week ?? 0);
                    }
                });
            }

            /* ─────────────────────────────────────────────
               PERFORMANCE MINGGUAN
            ───────────────────────────────────────────── */
            function loadPerformance(page) {
                page = page || perfState.currentPage;

                const params = {
                    page,
                    per_page: PER_PAGE
                };
                if (perfState.jenis) params.jenis = perfState.jenis;
                if (perfState.bulan) params.bulan = perfState.bulan;
                if (perfState.search) params.search = perfState.search;

                showLoading('#performanceTableBody', 6);
                clearPagination('#performancePaginationInfo', '#performancePagination');

                $.ajax({
                    url: '/api/wwtp-performance',
                    method: 'GET',
                    data: params,
                    success: function(response) {
                        perfState.currentPage = response.current_page;
                        perfState.lastPage = response.last_page;
                        perfState.total = response.total;
                        perfState.rows = response.data;

                        renderPerformanceTable(response.data);
                        renderPaginationInfo('#performancePaginationInfo', response);
                        renderPagination('#performancePagination', response, loadPerformance);

                        // update stat card
                        $('#totalPerformance').text(response.total);
                    },
                    error: function() {
                        showError('Gagal memuat data performance');
                    }
                });
            }

            function renderPerformanceTable(data) {
                const tbody = $('#performanceTableBody');
                tbody.empty();
                if (!data || !data.length) {
                    tbody.append(
                        `<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-inbox me-2"></i>Tidak ada data performance</td></tr>`
                    );
                    return;
                }
                data.forEach(function(record) {
                    /* record now has: id, jenis, tss, cod, foto, week: { week_start, week_end } */
                    const hasFoto = record.foto ?
                        '<i class="fas fa-check-circle text-success"></i>' :
                        '<i class="fas fa-times-circle text-muted"></i>';
                    let btns =
                        `<button class="btn btn-sm btn-outline-primary me-1" onclick="showPerformanceDetail(${record.id})" title="Lihat Detail"><i class="mdi mdi-eye"></i></button>`;
                    if (canEditDelete) {
                        btns +=
                            `
                    <button class="btn btn-sm btn-outline-success me-1" onclick="showPerformanceEdit(${record.id})" title="Edit"><i class="mdi mdi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDeletePerformance(${record.id})" title="Hapus"><i class="mdi mdi-trash-can"></i></button>`;
                    }
                    tbody.append(`
                <tr class="data-row">
                    <td>${record.week ? formatWeekRange(record.week.week_start, record.week.week_end) : '-'}</td>
                    <td>${getJenisBadge(record.jenis)}</td>
                    <td class="text-center fw-bold">${record.tss}</td>
                    <td class="text-center fw-bold">${record.cod}</td>
                    <td class="text-center">${hasFoto}</td>
                    <td class="text-center">${btns}</td>
                </tr>`);
                });
            }

            // Filters
            let perfSearchTimer;
            $('#searchData').on('keyup', function() {
                clearTimeout(perfSearchTimer);
                perfSearchTimer = setTimeout(function() {
                    perfState.search = $('#searchData').val();
                    perfState.currentPage = 1;
                    loadPerformance(1);
                }, 400);
            });

            $('#filterJenis').on('change', function() {
                perfState.jenis = $(this).val();
                perfState.currentPage = 1;
                loadPerformance(1);
            });

            $('#filterBulan').on('change', function() {
                perfState.bulan = $(this).val();
                perfState.currentPage = 1;
                loadPerformance(1);
            });

            $('#btnReset').on('click', function() {
                $('#filterJenis').val('');
                $('#filterBulan').val('');
                $('#searchData').val('');
                perfState.jenis = '';
                perfState.bulan = '';
                perfState.search = '';
                perfState.currentPage = 1;
                loadPerformance(1);
            });

            // Detail
            window.showPerformanceDetail = function(id) {
                $.ajax({
                    url: `/api/wwtp-performance/${id}`,
                    method: 'GET',
                    success: function(response) {
                        const r = response.data;
                        currentRecordId = id;
                        let content = `
                    <div class="row g-3 mb-4">
                        <div class="col-md-4"><div class="info-box p-3 bg-light rounded"><p class="text-muted small mb-1">Periode</p><p class="fw-bold mb-0">${formatWeekRange(r.week.week_start, r.week.week_end)}</p></div></div>
                        <div class="col-md-4"><div class="info-box p-3 bg-light rounded"><p class="text-muted small mb-1">Jenis</p>${getJenisBadge(r.jenis)}</div></div>
                        <div class="col-md-4"><div class="info-box p-3 bg-light rounded"><p class="text-muted small mb-1">Tanggal Input</p><p class="fw-bold mb-0">${formatDate(r.created_at)}</p></div></div>
                    </div>
                    <h6 class="fw-bold mb-3 text-primary">Parameter</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6"><div class="detail-item p-4 border rounded bg-light"><span class="text-muted">TSS</span><p class="fw-bold fs-3 mb-0 text-primary">${r.tss} <small class="text-muted fs-6">mg/L</small></p></div></div>
                        <div class="col-md-6"><div class="detail-item p-4 border rounded bg-light"><span class="text-muted">COD</span><p class="fw-bold fs-3 mb-0 text-primary">${r.cod} <small class="text-muted fs-6">mg/L</small></p></div></div>
                    </div>`;
                        if (r.foto) content +=
                            `<h6 class="fw-bold mb-3 text-primary">Dokumentasi</h6><div class="text-center"><img src="/storage/${r.foto}" class="foto-preview img-thumbnail"></div>`;
                        $('#modalPerformanceContent').html(content);
                        canEditDelete ? $('#btnDeletePerformance').show() : $(
                            '#btnDeletePerformance').hide();
                        new bootstrap.Modal(document.getElementById('detailPerformanceModal'))
                            .show();
                    },
                    error: function() {
                        showError('Gagal memuat detail data');
                    }
                });
            };

            // Edit
            window.showPerformanceEdit = function(id) {
                $.ajax({
                    url: `/api/wwtp-performance/${id}`,
                    method: 'GET',
                    success: function(response) {
                        const r = response.data;
                        currentRecordId = id;
                        $('#edit_perf_id').val(r.id);
                        $('#edit_perf_jenis').val(r.jenis);
                        $('#edit_perf_tss').val(r.tss);
                        $('#edit_perf_cod').val(r.cod);
                        const ws = new Date(r.week.week_start);
                        $('#edit_perf_week').val(
                            `${ws.getFullYear()}-W${getWeekNumber(ws).toString().padStart(2,'0')}`
                        );
                        $('#edit_perf_current_foto').html(r.foto ?
                            `<img src="/storage/${r.foto}" class="img-thumbnail" style="max-height:150px;">` :
                            '');
                        new bootstrap.Modal(document.getElementById('editPerformanceModal')).show();
                    },
                    error: function() {
                        showError('Gagal memuat data untuk diedit');
                    }
                });
            };

            $('#btnSavePerformance').on('click', function() {
                const id = $('#edit_perf_id').val(),
                    btn = $(this),
                    orig = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');
                const fd = new FormData();
                fd.append('jenis', $('#edit_perf_jenis').val());
                fd.append('tss', $('#edit_perf_tss').val());
                fd.append('cod', $('#edit_perf_cod').val());
                if ($('#edit_perf_foto')[0].files[0]) fd.append('foto', $('#edit_perf_foto')[0].files[0]);
                $.ajax({
                    url: `/api/wwtp-performance/${id}`,
                    method: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $('#editPerformanceModal').modal('hide');
                        showSuccess('Data berhasil diperbarui');
                        loadPerformance(perfState.currentPage);
                    },
                    error: function(xhr) {
                        showErrorFromXhr(xhr);
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(orig);
                    }
                });
            });

            $('#btnDeletePerformance').on('click', function() {
                if (!currentRecordId) return;
                confirmSwal('Hapus data performance ini?', function() {
                    $.ajax({
                        url: `/api/wwtp-performance/${currentRecordId}`,
                        method: 'DELETE',
                        success: function() {
                            $('#detailPerformanceModal').modal('hide');
                            showSuccess('Data dihapus');
                            loadPerformance(perfState.currentPage);
                        },
                        error: function() {
                            showError('Gagal menghapus');
                        }
                    });
                });
            });

            window.confirmDeletePerformance = function(id) {
                confirmSwal('Hapus data performance ini?', function() {
                    $.ajax({
                        url: `/api/wwtp-performance/${id}`,
                        method: 'DELETE',
                        success: function() {
                            showSuccess('Data dihapus');
                            loadPerformance(perfState.currentPage);
                        },
                        error: function() {
                            showError('Gagal menghapus');
                        }
                    });
                });
            };

            /* ─────────────────────────────────────────────
               PH HARIAN
            ───────────────────────────────────────────── */
            function loadPH(page) {
                page = page || phState.currentPage;

                const params = {
                    page,
                    per_page: PER_PAGE
                };
                if (phState.bulan) params.bulan = phState.bulan;

                showLoading('#phTableBody', 6);
                clearPagination('#phPaginationInfo', '#phPagination');

                $.ajax({
                    url: '/api/wwtp-performance/ph-harian',
                    method: 'GET',
                    data: params,
                    success: function(response) {
                        phState.currentPage = response.current_page;
                        phState.lastPage = response.last_page;
                        phState.total = response.total;
                        phState.rows = response.data;

                        $('#totalPH').text(response.total);
                        renderPHTable(response.data);
                        renderPaginationInfo('#phPaginationInfo', response);
                        renderPagination('#phPagination', response, loadPH);
                    },
                    error: function() {
                        showError('Gagal memuat data PH');
                    }
                });
            }

            function renderPHTable(data) {
                const tbody = $('#phTableBody');
                tbody.empty();
                if (!data || !data.length) {
                    tbody.append(
                        `<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-inbox me-2"></i>Tidak ada data PH harian</td></tr>`
                    );
                    return;
                }
                data.forEach(function(item) {
                    let btns =
                        `<button class="btn btn-sm btn-outline-primary me-1" onclick="showPHDetail(${item.id})" title="Lihat Detail"><i class="mdi mdi-eye"></i></button>`;
                    if (canEditDeleteDaily(item.approval_status)) {
                        btns +=
                            `
                    <button class="btn btn-sm btn-outline-success me-1" onclick="showPHEdit(${item.id})" title="Edit"><i class="mdi mdi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDeletePH(${item.id})" title="Hapus"><i class="mdi mdi-trash-can"></i></button>`;
                    }
                    tbody.append(`
                <tr class="data-row">
                    <td>${formatDate(item.tanggal)}</td>
                    <td>${getShiftBadge(item.shift)}</td>
                    <td class="text-center fw-bold">${item.equalisasi_1 ?? '-'}</td>
                    <td class="text-center fw-bold">${item.equalisasi_2 ?? '-'}</td>
                    <td class="text-center fw-bold">${item.outlet ?? '-'}</td>
                    <td class="text-center">${btns}</td>
                </tr>`);
                });
            }

            // PH filters
            $('#filterBulanPH').on('change', function() {
                phState.bulan = $(this).val();
                phState.currentPage = 1;
                loadPH(1);
            });

            $('#btnResetPH').on('click', function() {
                $('#filterBulanPH').val('');
                phState.bulan = '';
                phState.currentPage = 1;
                loadPH(1);
            });

            // PH tab click
            $('#ph-tab').on('click', function() {
                if (phState.total === 0 && phState.currentPage === 1) loadPH(1);
            });

            // PH Detail
            window.showPHDetail = function(id) {
                $.ajax({
                    url: `/api/wwtp-performance/ph-harian/${id}`,
                    method: 'GET',
                    success: function(r) {
                        currentRecordId = id;
                        const phItems = [{
                                label: 'Equalisasi 1',
                                value: r.equalisasi_1
                            },
                            {
                                label: 'Equalisasi 2',
                                value: r.equalisasi_2
                            },
                            {
                                label: 'Netralisasi',
                                value: r.netralisasi
                            },
                            {
                                label: 'Sedimentasi 1',
                                value: r.sedimentasi_1
                            },
                            {
                                label: 'Sedimentasi 2',
                                value: r.sedimentasi_2
                            },
                            {
                                label: 'Outlet Anaerob',
                                value: r.outlet_anaerob
                            },
                            {
                                label: 'Aerob',
                                value: r.aerob
                            },
                            {
                                label: 'Lumpur Aktif',
                                value: r.lumpur_aktif
                            },
                            {
                                label: 'Clarifier 2',
                                value: r.clarifier_2
                            },
                            {
                                label: 'Outlet',
                                value: r.outlet
                            }
                        ];
                        const phGrid = phItems.filter(i => i.value !== null && i.value !==
                            undefined).map(i =>
                            `<div class="col-md-6 col-lg-4"><div class="detail-item p-3 border rounded">
                        <small class="text-muted">${i.label}</small>
                        <p class="fw-bold fs-5 mb-0 text-info">${i.value} <small class="text-muted">pH</small></p>
                    </div></div>`).join('');
                        $('#modalPHContent').html(`
                    <div class="row g-3 mb-4">
                        <div class="col-md-6"><div class="info-box p-3 bg-light rounded"><p class="text-muted small mb-1">Tanggal</p><p class="fw-bold mb-0">${formatDate(r.tanggal)}</p></div></div>
                        <div class="col-md-6"><div class="info-box p-3 bg-light rounded"><p class="text-muted small mb-1">Shift</p>${getShiftBadge(r.shift)}</div></div>
                    </div>
                    <h6 class="fw-bold mb-3 text-info">Nilai PH per Lokasi</h6>
                    <div class="row g-3">${phGrid}</div>`);
                        if (canEditDeleteDaily(r.approval_status)) {
                            $('#btnDeletePH').show();
                        } else {
                            $('#btnDeletePH').hide();
                        }
                        new bootstrap.Modal(document.getElementById('detailPHModal')).show();
                    },
                    error: function() {
                        showError('Gagal memuat detail data PH');
                    }
                });
            };

            // PH Edit
            window.showPHEdit = function(id) {
                $.ajax({
                    url: `/api/wwtp-performance/ph-harian/${id}`,
                    method: 'GET',
                    success: function(r) {
                        currentRecordId = id;
                        $('#edit_ph_id').val(r.id);
                        $('#edit_ph_tanggal').val(r.tanggal);
                        $('#edit_ph_shift').val(r.shift);
                        $('#edit_ph_equalisasi_1').val(r.equalisasi_1 || '');
                        $('#edit_ph_equalisasi_2').val(r.equalisasi_2 || '');
                        $('#edit_ph_netralisasi').val(r.netralisasi || '');
                        $('#edit_ph_sedimentasi_1').val(r.sedimentasi_1 || '');
                        $('#edit_ph_sedimentasi_2').val(r.sedimentasi_2 || '');
                        $('#edit_ph_outlet_anaerob').val(r.outlet_anaerob || '');
                        $('#edit_ph_aerob').val(r.aerob || '');
                        $('#edit_ph_lumpur_aktif').val(r.lumpur_aktif || '');
                        $('#edit_ph_clarifier_2').val(r.clarifier_2 || '');
                        $('#edit_ph_outlet').val(r.outlet || '');
                        new bootstrap.Modal(document.getElementById('editPHModal')).show();
                    },
                    error: function() {
                        showError('Gagal memuat data untuk diedit');
                    }
                });
            };

            $('#btnSavePH').on('click', function() {
                const id = $('#edit_ph_id').val(),
                    btn = $(this),
                    orig = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');
                $.ajax({
                    url: `/api/wwtp-performance/ph-harian/${id}`,
                    method: 'PUT',
                    data: {
                        tanggal: $('#edit_ph_tanggal').val(),
                        shift: $('#edit_ph_shift').val(),
                        equalisasi_1: $('#edit_ph_equalisasi_1').val() || null,
                        equalisasi_2: $('#edit_ph_equalisasi_2').val() || null,
                        netralisasi: $('#edit_ph_netralisasi').val() || null,
                        sedimentasi_1: $('#edit_ph_sedimentasi_1').val() || null,
                        sedimentasi_2: $('#edit_ph_sedimentasi_2').val() || null,
                        outlet_anaerob: $('#edit_ph_outlet_anaerob').val() || null,
                        aerob: $('#edit_ph_aerob').val() || null,
                        lumpur_aktif: $('#edit_ph_lumpur_aktif').val() || null,
                        clarifier_2: $('#edit_ph_clarifier_2').val() || null,
                        outlet: $('#edit_ph_outlet').val() || null
                    },
                    success: function() {
                        $('#editPHModal').modal('hide');
                        showSuccess('Data PH berhasil diperbarui');
                        loadPH(phState.currentPage);
                    },
                    error: function(xhr) {
                        showErrorFromXhr(xhr);
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(orig);
                    }
                });
            });

            $('#btnDeletePH').on('click', function() {
                if (!currentRecordId) return;
                confirmSwal('Hapus data PH ini?', function() {
                    $.ajax({
                        url: `/api/wwtp-performance/ph-harian/${currentRecordId}`,
                        method: 'DELETE',
                        success: function() {
                            $('#detailPHModal').modal('hide');
                            showSuccess('Data PH dihapus');
                            loadPH(phState.currentPage);
                        },
                        error: function() {
                            showError('Gagal menghapus data PH');
                        }
                    });
                });
            });

            window.confirmDeletePH = function(id) {
                confirmSwal('Hapus data PH ini?', function() {
                    $.ajax({
                        url: `/api/wwtp-performance/ph-harian/${id}`,
                        method: 'DELETE',
                        success: function() {
                            showSuccess('Data PH dihapus');
                            loadPH(phState.currentPage);
                        },
                        error: function() {
                            showError('Gagal menghapus data PH');
                        }
                    });
                });
            };

            /* ─────────────────────────────────────────────
               DATA SAMPLE
            ───────────────────────────────────────────── */
            function loadJenisSampelOptions() {
                $.ajax({
                    url: '/api/wwtp-performance/jenis-sampel',
                    method: 'GET',
                    success: function(res) {
                        allJenisSampel = res.data || [];
                        const filterSel = $('#filterJenisSample');
                        const editSel = $('#edit_sample_id_sampel');
                        filterSel.find('option:not(:first)').remove();
                        editSel.find('option:not(:first)').remove();
                        allJenisSampel.forEach(function(j) {
                            filterSel.append(
                                `<option value="${j.id}">${j.nama_sampel}</option>`);
                            editSel.append(`<option value="${j.id}">${j.nama_sampel}</option>`);
                        });
                    }
                });
            }

            function loadSample(page) {
                page = page || sampleState.currentPage;

                const params = {
                    page,
                    per_page: PER_PAGE
                };
                if (sampleState.bulan) params.bulan = sampleState.bulan;
                if (sampleState.idSampel) params.id_sampel = sampleState.idSampel;
                if (sampleState.search) params.search = sampleState.search;

                showLoading('#sampleTableBody', 9);
                clearPagination('#samplePaginationInfo', '#samplePagination');

                $.ajax({
                    url: '/api/wwtp-performance/sample',
                    method: 'GET',
                    data: params,
                    success: function(response) {
                        sampleState.currentPage = response.current_page;
                        sampleState.lastPage = response.last_page;
                        sampleState.total = response.total;
                        sampleState.rows = response.data;

                        $('#totalSample').text(response.total);
                        renderSampleTable(response.data);
                        renderPaginationInfo('#samplePaginationInfo', response);
                        renderPagination('#samplePagination', response, loadSample);
                    },
                    error: function() {
                        showError('Gagal memuat data sample');
                    }
                });
            }

            function renderSampleTable(data) {
                const tbody = $('#sampleTableBody');
                tbody.empty();
                if (!data || !data.length) {
                    tbody.append(
                        `<tr><td colspan="9" class="text-center py-4 text-muted"><i class="fas fa-inbox me-2"></i>Tidak ada data sample</td></tr>`
                    );
                    return;
                }
                data.forEach(function(s) {
                    let btns =
                        `<button class="btn btn-sm btn-outline-warning me-1" onclick="showSampleDetail(${s.id})" title="Lihat Detail"><i class="mdi mdi-eye"></i></button>`;
                    if (canEditDelete) {
                        btns +=
                            `
                    <button class="btn btn-sm btn-outline-success me-1" onclick="showSampleEdit(${s.id})" title="Edit"><i class="mdi mdi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteSample(${s.id})" title="Hapus"><i class="mdi mdi-trash-can"></i></button>`;
                    }
                    tbody.append(`
                <tr class="data-row">
                    <td>${formatDate(s.tanggal)}</td>
                    <td><span class="badge bg-warning text-dark">${s.jenis_sampel.nama_sampel || '-'}</span></td>
                    <td class="text-center fw-bold">${s.tss  ?? '-'}</td>
                    <td class="text-center fw-bold">${s.sv30 ?? '-'}</td>
                    <td class="text-center fw-bold">${s.ph   ?? '-'}</td>
                    <td class="text-center fw-bold">${s.mlss ?? '-'}</td>
                    <td class="text-center fw-bold">${s.svl  ?? '-'}</td>
                    <td class="text-center fw-bold">${s.do   ?? '-'}</td>
                    <td class="text-center">${btns}</td>
                </tr>`);
                });
            }

            // Sample filters
            let sampleSearchTimer;
            $('#searchSample').on('keyup', function() {
                clearTimeout(sampleSearchTimer);
                sampleSearchTimer = setTimeout(function() {
                    sampleState.search = $('#searchSample').val();
                    sampleState.currentPage = 1;
                    loadSample(1);
                }, 400);
            });

            $('#filterJenisSample').on('change', function() {
                sampleState.idSampel = $(this).val();
                sampleState.currentPage = 1;
                loadSample(1);
            });

            $('#filterBulanSample').on('change', function() {
                sampleState.bulan = $(this).val();
                sampleState.currentPage = 1;
                loadSample(1);
            });

            $('#btnResetSample').on('click', function() {
                $('#filterJenisSample').val('');
                $('#filterBulanSample').val('');
                $('#searchSample').val('');
                sampleState.idSampel = '';
                sampleState.bulan = '';
                sampleState.search = '';
                sampleState.currentPage = 1;
                loadSample(1);
            });

            // Sample tab click
            $('#sample-tab').on('click', function() {
                if (allJenisSampel.length === 0) loadJenisSampelOptions();
                if (sampleState.total === 0 && sampleState.currentPage === 1) loadSample(1);
            });

            // Sample Detail
            window.showSampleDetail = function(id) {
                $.ajax({
                    url: `/api/wwtp-performance/sample/${id}`,
                    method: 'GET',
                    success: function(response) {
                        const s = response.data;
                        currentRecordId = id;
                        const namaSampel = s.jenis_sampel?.nama_sampel ?? s.jenis_sampel ?? '-';
                        const params = [{
                                lbl: 'TSS',
                                val: s.tss,
                                unit: 'mg/L'
                            },
                            {
                                lbl: 'SV30',
                                val: s.sv30,
                                unit: 'mL/L'
                            },
                            {
                                lbl: 'pH',
                                val: s.ph,
                                unit: 'pH'
                            },
                            {
                                lbl: 'MLSS',
                                val: s.mlss,
                                unit: 'mg/L'
                            },
                            {
                                lbl: 'SVI',
                                val: s.svl,
                                unit: 'mL/g'
                            },
                            {
                                lbl: 'DO',
                                val: s.do,
                                unit: 'mg/L'
                            }
                        ];
                        const paramGrid = params.map(function(p) {
                            return `<div class="col-md-4 col-6">
                        <div class="param-badge w-100 text-center">
                            <span class="param-val">${p.val ?? '-'}</span>
                            <span class="param-unit">${p.unit}</span>
                            <span class="param-lbl">${p.lbl}</span>
                        </div>
                    </div>`;
                        }).join('');
                        $('#modalSampleContent').html(`
                    <div class="row g-3 mb-4">
                        <div class="col-md-6"><div class="info-box p-3 bg-light rounded"><p class="text-muted small mb-1">Tanggal</p><p class="fw-bold mb-0">${formatDate(s.tanggal ? s.tanggal.substring(0,10) : '')}</p></div></div>
                        <div class="col-md-6"><div class="info-box p-3 bg-light rounded"><p class="text-muted small mb-1">Jenis Sampel</p><span class="badge bg-warning text-dark fs-6">${namaSampel}</span></div></div>
                    </div>
                    <h6 class="fw-bold mb-3 text-warning">Parameter Pengujian</h6>
                    <div class="row g-3">${paramGrid}</div>`);
                        canEditDelete ? $('#btnDeleteSample').show() : $('#btnDeleteSample').hide();
                        new bootstrap.Modal(document.getElementById('detailSampleModal')).show();
                    },
                    error: function() {
                        showError('Gagal memuat detail sample');
                    }
                });
            };

            // Sample Edit
            window.showSampleEdit = function(id) {
                if (allJenisSampel.length === 0) loadJenisSampelOptions();
                $.ajax({
                    url: `/api/wwtp-performance/sample/${id}`,
                    method: 'GET',
                    success: function(response) {
                        const s = response.data;
                        currentRecordId = id;
                        $('#edit_sample_id').val(s.id);
                        $('#edit_sample_tanggal').val(s.tanggal ? s.tanggal.substring(0, 10) : '');
                        $('#edit_sample_id_sampel').val(s.id_sampel);
                        $('#edit_sample_tss').val(s.tss);
                        $('#edit_sample_sv30').val(s.sv30);
                        $('#edit_sample_ph').val(s.ph);
                        $('#edit_sample_mlss').val(s.mlss);
                        $('#edit_sample_svl').val(s.svl);
                        $('#edit_sample_do').val(s.do);
                        new bootstrap.Modal(document.getElementById('editSampleModal')).show();
                    },
                    error: function() {
                        showError('Gagal memuat data untuk diedit');
                    }
                });
            };

            $('#btnSaveSample').on('click', function() {
                const id = $('#edit_sample_id').val(),
                    btn = $(this),
                    orig = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');
                $.ajax({
                    url: `/api/wwtp-performance/sample/${id}`,
                    method: 'PUT',
                    data: {
                        tanggal: $('#edit_sample_tanggal').val(),
                        id_sampel: $('#edit_sample_id_sampel').val(),
                        tss: $('#edit_sample_tss').val(),
                        sv30: $('#edit_sample_sv30').val(),
                        ph: $('#edit_sample_ph').val(),
                        mlss: $('#edit_sample_mlss').val(),
                        svl: $('#edit_sample_svl').val(),
                        do: $('#edit_sample_do').val()
                    },
                    success: function() {
                        $('#editSampleModal').modal('hide');
                        showSuccess('Data sample berhasil diperbarui');
                        loadSample(sampleState.currentPage);
                    },
                    error: function(xhr) {
                        showErrorFromXhr(xhr);
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(orig);
                    }
                });
            });

            $('#btnDeleteSample').on('click', function() {
                if (!currentRecordId) return;
                confirmSwal('Hapus data sample ini?', function() {
                    $.ajax({
                        url: `/api/wwtp-performance/sample/${currentRecordId}`,
                        method: 'DELETE',
                        success: function() {
                            $('#detailSampleModal').modal('hide');
                            showSuccess('Data sample dihapus');
                            loadSample(sampleState.currentPage);
                        },
                        error: function() {
                            showError('Gagal menghapus data sample');
                        }
                    });
                });
            });

            window.confirmDeleteSample = function(id) {
                confirmSwal('Hapus data sample ini?', function() {
                    $.ajax({
                        url: `/api/wwtp-performance/sample/${id}`,
                        method: 'DELETE',
                        success: function() {
                            showSuccess('Data sample dihapus');
                            loadSample(sampleState.currentPage);
                        },
                        error: function() {
                            showError('Gagal menghapus data sample');
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
                    `Menampilkan ${response.from ?? 0}–${response.to ?? 0} dari ${response.total} data`);
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
                for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) range
                    .push(i);
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
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
        </td></tr>`);
            }

            function clearPagination(infoSelector, paginationSelector) {
                $(infoSelector).text('');
                $(paginationSelector).empty();
            }

            /* ─────────────────────────────────────────────
               HELPERS
            ───────────────────────────────────────────── */
            function getJenisBadge(jenis) {
                console.log(jenis);
                return ({
                        equal: '<span class="badge bg-primary badge-jenis">Equalisasi</span>',
                        outlet_anaerob: '<span class="badge bg-info badge-jenis">Outlet Anaerob</span>',
                        aerob: '<span class="badge bg-success badge-jenis">Aerob</span>',
                        daf: '<span class="badge bg-warning badge-jenis">DAF</span>',
                        outlet: '<span class="badge bg-secondary badge-jenis">Outlet</span>'
                    } [jenis] ||
                    `<span class="badge bg-secondary badge-jenis">${jenis}</span>`);
            }

            function getShiftBadge(shift) {
                return ({
                        shift1: '<span class="badge bg-primary">Shift 1</span>',
                        shift2: '<span class="badge bg-success">Shift 2</span>',
                        shift3: '<span class="badge bg-info">Shift 3</span>'
                    } [shift] ||
                    `<span class="badge bg-secondary">${shift}</span>`);
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

            function getWeekNumber(date) {
                const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
                const day = d.getUTCDay() || 7;
                d.setUTCDate(d.getUTCDate() + 4 - day);
                const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
                return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
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

            /* ─────────────────────────────────────────────
               INIT
            ───────────────────────────────────────────── */
            loadStatistics();
            loadPerformance(1);
        });
    </script>
@endsection
