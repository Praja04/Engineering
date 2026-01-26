@extends('layouts.app')

@section('title', 'WWTP Performance Monitoring')

@section('content')

<div class="page-content">
    <div class="container-fluid">
        <div class="container-fluid px-4 py-5">
            <!-- Header Section -->
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

                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-3 p-3 me-3">
                                    <i class="fas fa-calendar-day fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">Today PH</p>
                                    <h3 class="fw-bold mb-0" id="todayPH">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="row mb-4">
                <div class="col-12">
                    <a href="{{ url('/wwtp/form_performance') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Data Performance
                    </a>
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
                            <input type="text" class="form-control form-control-lg" id="searchData" placeholder="Cari berdasarkan minggu atau jenis...">
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
                            <button class="nav-link active" id="performance-tab" data-bs-toggle="tab" data-bs-target="#performance" type="button">
                                <i class="fas fa-flask me-2"></i>Performance Mingguan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ph-tab" data-bs-toggle="tab" data-bs-target="#ph" type="button">
                                <i class="fas fa-water me-2"></i>PH Harian
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="dataTabsContent">
                        <!-- Performance Mingguan Tab -->
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
                        </div>

                        <!-- PH Harian Tab -->
                        <div class="tab-pane fade" id="ph" role="tabpanel">
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
                                                <i class="fas fa-info-circle me-2"></i>Data PH harian akan ditampilkan di sini
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

        <!-- Detail Performance Modal -->
        <div class="modal fade" id="detailPerformanceModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-flask me-2"></i>Detail Performance WWTP
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4" id="modalPerformanceContent">
                        <!-- Content will be loaded dynamically -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-danger" id="btnDeletePerformance" style="display: none;">
                            <i class="fas fa-trash me-2"></i>Hapus Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Performance Modal -->
        <div class="modal fade" id="editPerformanceModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-edit me-2"></i>Edit Data Performance
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="editPerformanceForm" enctype="multipart/form-data">
                            <input type="hidden" id="edit_perf_id">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
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
                                    <label class="form-label fw-semibold">Minggu <span class="text-danger">*</span></label>
                                    <input type="week" class="form-control" id="edit_perf_week" readonly>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">TSS (mg/L) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="edit_perf_tss" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">COD (mg/L) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="edit_perf_cod" required>
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

        <!-- Detail PH Modal -->
        <div class="modal fade" id="detailPHModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-water me-2"></i>Detail PH Harian
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4" id="modalPHContent">
                        <!-- Content will be loaded dynamically -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-danger" id="btnDeletePH" style="display: none;">
                            <i class="fas fa-trash me-2"></i>Hapus Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit PH Modal -->
        <div class="modal fade" id="editPHModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-edit me-2"></i>Edit Data PH Harian
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="editPHForm">
                            <input type="hidden" id="edit_ph_id">

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit_ph_tanggal" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Shift <span class="text-danger">*</span></label>
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
                                <div class="col-md-4">
                                    <label class="form-label">Equalisasi 1</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_ph_equalisasi_1">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Equalisasi 2</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_ph_equalisasi_2">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Netralisasi</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_ph_netralisasi">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Sedimentasi 1</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_ph_sedimentasi_1">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Sedimentasi 2</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_ph_sedimentasi_2">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Outlet Anaerob</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_ph_outlet_anaerob">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Aerob</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_ph_aerob">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Lumpur Aktif</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_ph_lumpur_aktif">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Clarifier 2</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_ph_clarifier_2">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Outlet</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_ph_outlet">
                                </div>
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

    .foto-preview {
        max-width: 100%;
        max-height: 300px;
        object-fit: contain;
        border-radius: 8px;
    }

    .badge-jenis {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }
</style>

<script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        let allPerformanceData = [];
        let allPHData = [];
        let currentRecordId = null;
        let currentRecordType = 'performance';

        // Get user jabatan from Laravel
        const userJabatan = "{{ Auth::user()->jabatan }}";
        const canEditDelete = userJabatan !== 'operator';

        // Load data saat halaman pertama kali dimuat
        loadPerformanceData();

        // Event listeners untuk filter
        $('#filterJenis, #filterBulan, #searchData').on('change keyup', function() {
            filterPerformanceData();
        });

        $('#btnReset').on('click', function() {
            $('#filterJenis').val('');
            $('#filterBulan').val('');
            $('#searchData').val('');
            filterPerformanceData();
        });

        // Delete button handlers
        $('#btnDeletePerformance').on('click', function() {
            if (currentRecordId) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Apakah Anda yakin ingin menghapus data performance ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deletePerformance(currentRecordId);
                    }
                });
            }
        });

        $('#btnDeletePH').on('click', function() {
            if (currentRecordId) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Apakah Anda yakin ingin menghapus data PH ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deletePH(currentRecordId);
                    }
                });
            }
        });

        // Save Performance Edit
        $('#btnSavePerformance').on('click', function() {
            savePerformanceEdit();
        });

        // Save PH Edit
        $('#btnSavePH').on('click', function() {
            savePHEdit();
        });

        // Load Performance Data
        function loadPerformanceData() {
            $.ajax({
                url: '/api/wwtp-performance',
                method: 'GET',
                success: function(response) {
                    allPerformanceData = response.data || [];
                    updateStatistics();
                    filterPerformanceData();
                },
                error: function(xhr) {
                    console.error('Error loading performance data:', xhr);
                    showError('Gagal memuat data performance');
                }
            });
        }

        // Update Statistics
        function updateStatistics() {
            const totalPerf = allPerformanceData.reduce((sum, week) => sum + (week.records?.length || 0), 0);
            $('#totalPerformance').text(totalPerf);

            // Count this week's data
            const now = new Date();
            const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay() + 1));
            startOfWeek.setHours(0, 0, 0, 0);

            const weekData = allPerformanceData.filter(week => {
                const weekStart = new Date(week.week_start);
                return weekStart >= startOfWeek;
            }).reduce((sum, week) => sum + (week.records?.length || 0), 0);

            $('#weekPerformance').text(weekData);
        }

        // Filter Performance Data
        function filterPerformanceData() {
            const jenis = $('#filterJenis').val();
            const bulan = $('#filterBulan').val();
            const search = $('#searchData').val().toLowerCase();

            let filtered = allPerformanceData.map(week => {
                let filteredRecords = week.records || [];

                if (jenis) {
                    filteredRecords = filteredRecords.filter(r => r.jenis === jenis);
                }

                if (bulan) {
                    const weekMonth = week.week_start.substring(0, 7);
                    if (weekMonth !== bulan) {
                        filteredRecords = [];
                    }
                }

                if (search) {
                    filteredRecords = filteredRecords.filter(r => {
                        const weekStr = `${week.week_start} - ${week.week_end}`.toLowerCase();
                        const jenisStr = getJenisLabel(r.jenis).toLowerCase();
                        return weekStr.includes(search) || jenisStr.includes(search);
                    });
                }

                return {
                    ...week,
                    records: filteredRecords
                };
            }).filter(week => week.records.length > 0);

            renderPerformanceTable(filtered);
        }

        // Render Performance Table
        function renderPerformanceTable(data) {
            const tbody = $('#performanceTableBody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox me-2"></i>Tidak ada data performance
                        </td>
                    </tr>
                `);
                return;
            }

            data.forEach(week => {
                week.records.forEach(record => {
                    const jenisBadge = getJenisBadge(record.jenis);
                    const weekRange = formatWeekRange(week.week_start, week.week_end);
                    const hasFoto = record.foto ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-muted"></i>';

                    let actionButtons = `
                        <button class="btn btn-sm btn-outline-primary me-1" 
                                onclick="showPerformanceDetail(${record.id})"
                                data-bs-toggle="tooltip" 
                                title="Lihat Detail">
                            <i class="mdi mdi-eye"></i>
                        </button>
                    `;

                    if (canEditDelete) {
                        actionButtons += `
                            <button class="btn btn-sm btn-outline-success me-1" 
                                    onclick="showPerformanceEdit(${record.id})"
                                    data-bs-toggle="tooltip" 
                                    title="Edit Data">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="confirmDeletePerformance(${record.id})"
                                    data-bs-toggle="tooltip" 
                                    title="Hapus Data">
                                <i class="mdi mdi-trash-can"></i>
                            </button>
                        `;
                    }

                    tbody.append(`
                        <tr class="data-row">
                            <td>${weekRange}</td>
                            <td>${jenisBadge}</td>
                            <td class="text-center fw-bold">${record.tss}</td>
                            <td class="text-center fw-bold">${record.cod}</td>
                            <td class="text-center">${hasFoto}</td>
                            <td class="text-center">${actionButtons}</td>
                        </tr>
                    `);
                });
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        }

        // Show Performance Edit
        window.showPerformanceEdit = function(id) {
            $.ajax({
                url: `/api/wwtp-performance/${id}`,
                method: 'GET',
                success: function(response) {
                    const record = response.data;
                    currentRecordId = id;

                    $('#edit_perf_id').val(record.id);
                    $('#edit_perf_jenis').val(record.jenis);
                    $('#edit_perf_tss').val(record.tss);
                    $('#edit_perf_cod').val(record.cod);

                    // Set week value
                    const weekStart = new Date(record.week.week_start);
                    const year = weekStart.getFullYear();
                    const weekNum = getWeekNumber(weekStart);
                    $('#edit_perf_week').val(`${year}-W${weekNum.toString().padStart(2, '0')}`);

                    // Show current foto if exists
                    if (record.foto) {
                        $('#edit_perf_current_foto').html(`
                            <img src="/storage/${record.foto}" class="img-thumbnail" style="max-height: 150px;" alt="Current Photo">
                            <p class="small text-muted mt-1">Foto saat ini</p>
                        `);
                    } else {
                        $('#edit_perf_current_foto').html('');
                    }

                    new bootstrap.Modal(document.getElementById('editPerformanceModal')).show();
                },
                error: function(xhr) {
                    showError('Gagal memuat data untuk diedit');
                }
            });
        }

        // Save Performance Edit
        function savePerformanceEdit() {
            const id = $('#edit_perf_id').val();
            const btnSave = $('#btnSavePerformance');
            const originalText = btnSave.html();
            btnSave.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

            const formData = new FormData();
            formData.append('jenis', $('#edit_perf_jenis').val());
            formData.append('tss', $('#edit_perf_tss').val());
            formData.append('cod', $('#edit_perf_cod').val());

            const fotoFile = $('#edit_perf_foto')[0].files[0];
            if (fotoFile) {
                formData.append('foto', fotoFile);
            }

            $.ajax({
                url: `/api/wwtp-performance/${id}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#editPerformanceModal').modal('hide');
                    showSuccess('Data performance berhasil diperbarui');
                    loadPerformanceData();
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    let message = 'Terjadi kesalahan saat menyimpan data!';
                    if (error && error.message) {
                        message = error.message;
                    }
                    showError(message);
                },
                complete: function() {
                    btnSave.prop('disabled', false).html(originalText);
                }
            });
        }

        function getWeekNumber(date) {
            const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
            const dayNum = d.getUTCDay() || 7;
            d.setUTCDate(d.getUTCDate() + 4 - dayNum);
            const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
            return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
        }

        // Show Performance Detail
        window.showPerformanceDetail = function(id) {
            $.ajax({
                url: `/api/wwtp-performance/${id}`,
                method: 'GET',
                success: function(response) {
                    const record = response.data;
                    currentRecordId = id;
                    currentRecordType = 'performance';

                    const jenisLabel = getJenisLabel(record.jenis);
                    const weekRange = formatWeekRange(record.week.week_start, record.week.week_end);

                    let content = `
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="info-box p-3 bg-light rounded">
                                    <p class="text-muted small mb-1">Periode Minggu</p>
                                    <p class="fw-bold mb-0">${weekRange}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box p-3 bg-light rounded">
                                    <p class="text-muted small mb-1">Jenis Pengukuran</p>
                                    ${getJenisBadge(record.jenis)}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box p-3 bg-light rounded">
                                    <p class="text-muted small mb-1">Tanggal Input</p>
                                    <p class="fw-bold mb-0">${formatDate(record.created_at)}</p>
                                </div>
                            </div>
                        </div>
                        
                        <h6 class="fw-bold mb-3 text-primary">Parameter Pengukuran</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="detail-item p-4 border rounded bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-muted">TSS (Total Suspended Solids)</span>
                                            <p class="fw-bold fs-3 mb-0 text-primary">${record.tss} <small class="text-muted fs-6">mg/L</small></p>
                                        </div>
                                        <i class="fas fa-molecule fs-1 text-primary opacity-25"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item p-4 border rounded bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-muted">COD (Chemical Oxygen Demand)</span>
                                            <p class="fw-bold fs-3 mb-0 text-primary">${record.cod} <small class="text-muted fs-6">mg/L</small></p>
                                        </div>
                                        <i class="fas fa-water fs-1 text-primary opacity-25"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    if (record.foto) {
                        content += `
                            <h6 class="fw-bold mb-3 text-primary">Dokumentasi</h6>
                            <div class="text-center">
                                <img src="/storage/${record.foto}" class="foto-preview img-thumbnail" alt="Dokumentasi">
                            </div>
                        `;
                    }

                    $('#modalPerformanceContent').html(content);

                    // Show/hide delete button based on role
                    if (canEditDelete) {
                        $('#btnDeletePerformance').show();
                    } else {
                        $('#btnDeletePerformance').hide();
                    }

                    new bootstrap.Modal(document.getElementById('detailPerformanceModal')).show();
                },
                error: function(xhr) {
                    console.error('Error loading detail:', xhr);
                    showError('Gagal memuat detail data');
                }
            });
        }

        // Confirm Delete Performance
        window.confirmDeletePerformance = function(id) {
            currentRecordId = id;
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data performance ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    deletePerformance(id);
                }
            });
        }

        // Delete Performance
        function deletePerformance(id) {
            $.ajax({
                url: `/api/wwtp-performance/${id}`,
                method: 'DELETE',
                success: function(response) {
                    $('#detailPerformanceModal').modal('hide');
                    showSuccess('Data performance berhasil dihapus');
                    loadPerformanceData();
                },
                error: function(xhr) {
                    console.error('Error deleting performance:', xhr);
                    showError('Gagal menghapus data performance');
                }
            });
        }

        // Load PH Data
        function loadPHData() {
            $.ajax({
                url: '/api/wwtp-performance/ph-harian',
                method: 'GET',
                success: function(response) {
                    allPHData = response || [];
                    updatePHStatistics();
                    renderPHTable(allPHData);
                },
                error: function(xhr) {
                    console.error('Error loading PH data:', xhr);
                    showError('Gagal memuat data PH');
                }
            });
        }

        // Update PH Statistics
        function updatePHStatistics() {
            $('#totalPH').text(allPHData.length);

            // Count today's data
            const today = new Date().toISOString().split('T')[0];
            const todayCount = allPHData.filter(ph => ph.tanggal === today).length;
            $('#todayPH').text(todayCount);
        }

        // Render PH Table
        function renderPHTable(data) {
            const tbody = $('#phTableBody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox me-2"></i>Tidak ada data PH harian
                        </td>
                    </tr>
                `);
                return;
            }

            data.forEach(item => {
                const shiftBadge = getShiftBadge(item.shift);

                let actionButtons = `
                    <button class="btn btn-sm btn-outline-primary me-1" 
                            onclick="showPHDetail(${item.id})"
                            data-bs-toggle="tooltip" 
                            title="Lihat Detail">
                        <i class="mdi mdi-eye"></i>
                    </button>
                `;

                if (canEditDelete) {
                    actionButtons += `
                        <button class="btn btn-sm btn-outline-success me-1" 
                                onclick="showPHEdit(${item.id})"
                                data-bs-toggle="tooltip" 
                                title="Edit Data">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" 
                                onclick="confirmDeletePH(${item.id})"
                                data-bs-toggle="tooltip" 
                                title="Hapus Data">
                            <i class="mdi mdi-trash-can"></i>
                        </button>
                    `;
                }

                tbody.append(`
                    <tr class="data-row">
                        <td>${formatDate(item.tanggal)}</td>
                        <td>${shiftBadge}</td>
                        <td class="text-center fw-bold">${item.equalisasi_1 || '-'}</td>
                        <td class="text-center fw-bold">${item.equalisasi_2 || '-'}</td>
                        <td class="text-center fw-bold">${item.outlet || '-'}</td>
                        <td class="text-center">${actionButtons}</td>
                    </tr>
                `);
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        }

        // Show PH Detail
        window.showPHDetail = function(id) {
            $.ajax({
                url: `/api/wwtp-performance/ph-harian/${id}`,
                method: 'GET',
                success: function(record) {
                    currentRecordId = id;
                    currentRecordType = 'ph';

                    const shiftLabel = getShiftLabel(record.shift);

                    let content = `
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
                                    <p class="fw-bold mb-0">${formatDate(record.created_at)}</p>
                                </div>
                            </div>
                        </div>
                        
                        <h6 class="fw-bold mb-3 text-info">Nilai PH pada Berbagai Lokasi</h6>
                        <div class="row g-3">
                    `;

                    const phItems = [{
                            label: 'Equalisasi 1',
                            value: record.equalisasi_1,
                            icon: 'water'
                        },
                        {
                            label: 'Equalisasi 2',
                            value: record.equalisasi_2,
                            icon: 'water'
                        },
                        {
                            label: 'Netralisasi',
                            value: record.netralisasi,
                            icon: 'water-check'
                        },
                        {
                            label: 'Sedimentasi 1',
                            value: record.sedimentasi_1,
                            icon: 'water-outline'
                        },
                        {
                            label: 'Sedimentasi 2',
                            value: record.sedimentasi_2,
                            icon: 'water-outline'
                        },
                        {
                            label: 'Outlet Anaerob',
                            value: record.outlet_anaerob,
                            icon: 'water-outline'
                        },
                        {
                            label: 'Aerob',
                            value: record.aerob,
                            icon: 'water-outline'
                        },
                        {
                            label: 'Lumpur Aktif',
                            value: record.lumpur_aktif,
                            icon: 'water-outline'
                        },
                        {
                            label: 'Clarifier 2',
                            value: record.clarifier_2,
                            icon: 'water-outline'
                        },
                        {
                            label: 'Outlet',
                            value: record.outlet,
                            icon: 'water-check'
                        }
                    ];

                    phItems.forEach(item => {
                        if (item.value !== null && item.value !== undefined) {
                            content += `
                                <div class="col-md-6 col-lg-4">
                                    <div class="detail-item p-3 border rounded">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted">${item.label}</small>
                                                <p class="fw-bold fs-5 mb-0 text-info">${item.value} <small class="text-muted">pH</small></p>
                                            </div>
                                            <i class="mdi mdi-${item.icon} fs-3 text-info opacity-25"></i>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    });

                    content += `</div>`;

                    $('#modalPHContent').html(content);

                    // Show/hide delete button based on role
                    if (canEditDelete) {
                        $('#btnDeletePH').show();
                    } else {
                        $('#btnDeletePH').hide();
                    }

                    new bootstrap.Modal(document.getElementById('detailPHModal')).show();
                },
                error: function(xhr) {
                    console.error('Error loading PH detail:', xhr);
                    showError('Gagal memuat detail data PH');
                }
            });
        }

        // Show PH Edit
        window.showPHEdit = function(id) {
            $.ajax({
                url: `/api/wwtp-performance/ph-harian/${id}`,
                method: 'GET',
                success: function(record) {
                    currentRecordId = id;

                    $('#edit_ph_id').val(record.id);
                    $('#edit_ph_tanggal').val(record.tanggal);
                    $('#edit_ph_shift').val(record.shift);
                    $('#edit_ph_equalisasi_1').val(record.equalisasi_1 || '');
                    $('#edit_ph_equalisasi_2').val(record.equalisasi_2 || '');
                    $('#edit_ph_netralisasi').val(record.netralisasi || '');
                    $('#edit_ph_sedimentasi_1').val(record.sedimentasi_1 || '');
                    $('#edit_ph_sedimentasi_2').val(record.sedimentasi_2 || '');
                    $('#edit_ph_outlet_anaerob').val(record.outlet_anaerob || '');
                    $('#edit_ph_aerob').val(record.aerob || '');
                    $('#edit_ph_lumpur_aktif').val(record.lumpur_aktif || '');
                    $('#edit_ph_clarifier_2').val(record.clarifier_2 || '');
                    $('#edit_ph_outlet').val(record.outlet || '');

                    new bootstrap.Modal(document.getElementById('editPHModal')).show();
                },
                error: function(xhr) {
                    showError('Gagal memuat data untuk diedit');
                }
            });
        }

        // Save PH Edit
        function savePHEdit() {
            const id = $('#edit_ph_id').val();
            const btnSave = $('#btnSavePH');
            const originalText = btnSave.html();
            btnSave.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

            const formData = {
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
            };

            $.ajax({
                url: `/api/wwtp-performance/ph-harian/${id}`,
                method: 'PUT',
                data: formData,
                success: function(response) {
                    $('#editPHModal').modal('hide');
                    showSuccess('Data PH berhasil diperbarui');
                    loadPHData();
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    let message = 'Terjadi kesalahan saat menyimpan data!';
                    if (error && error.message) {
                        message = error.message;
                    }
                    showError(message);
                },
                complete: function() {
                    btnSave.prop('disabled', false).html(originalText);
                }
            });
        }

        // Confirm Delete PH
        window.confirmDeletePH = function(id) {
            currentRecordId = id;
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data PH ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    deletePH(id);
                }
            });
        }

        // Delete PH
        function deletePH(id) {
            $.ajax({
                url: `/api/wwtp-performance/ph-harian/${id}`,
                method: 'DELETE',
                success: function(response) {
                    $('#detailPHModal').modal('hide');
                    showSuccess('Data PH berhasil dihapus');
                    loadPHData();
                },
                error: function(xhr) {
                    console.error('Error deleting PH:', xhr);
                    showError('Gagal menghapus data PH');
                }
            });
        }

        // Helper Functions
        function getJenisLabel(jenis) {
            const labels = {
                'equal': 'Equalisasi',
                'outlet_anaerob': 'Outlet Anaerob',
                'aerob': 'Aerob',
                'daf': 'DAF',
                'outlet': 'Outlet'
            };
            return labels[jenis] || jenis;
        }

        function getJenisBadge(jenis) {
            const badges = {
                'equal': '<span class="badge bg-primary badge-jenis">Equalisasi</span>',
                'outlet_anaerob': '<span class="badge bg-info badge-jenis">Outlet Anaerob</span>',
                'aerob': '<span class="badge bg-success badge-jenis">Aerob</span>',
                'daf': '<span class="badge bg-warning badge-jenis">DAF</span>',
                'outlet': '<span class="badge bg-secondary badge-jenis">Outlet</span>'
            };
            return badges[jenis] || `<span class="badge bg-secondary badge-jenis">${jenis}</span>`;
        }

        function getShiftLabel(shift) {
            const labels = {
                'shift1': 'Shift 1 (06:00 - 14:00)',
                'shift2': 'Shift 2 (14:00 - 22:00)',
                'shift3': 'Shift 3 (22:00 - 06:00)'
            };
            return labels[shift] || shift;
        }

        function getShiftBadge(shift) {
            const badges = {
                'shift1': '<span class="badge bg-primary">Shift 1</span>',
                'shift2': '<span class="badge bg-success">Shift 2</span>',
                'shift3': '<span class="badge bg-info">Shift 3</span>'
            };
            return badges[shift] || `<span class="badge bg-secondary">${shift}</span>`;
        }

        function formatWeekRange(start, end) {
            const startDate = new Date(start);
            const endDate = new Date(end);
            const options = {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            };
            return `${startDate.toLocaleDateString('id-ID', options)} - ${endDate.toLocaleDateString('id-ID', options)}`;
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
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                confirmButtonColor: '#3085d6',
                timer: 2000
            });
        }

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonColor: '#d33'
            });
        }

        // Event listener untuk tab PH
        $('#ph-tab').on('click', function() {
            loadPHData();
        });

        // Load PH data jika tab PH sudah aktif saat page load
        if ($('#ph-tab').hasClass('active')) {
            loadPHData();
        }
    });
</script>
@endsection