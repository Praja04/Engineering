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
                                    <p class="text-muted mb-1 small">Avg Drain Today</p>
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
                <div class="col-12">
                    <a href="{{ url('/wwtp/form_sludge') }}" class="btn btn-warning btn-lg">
                        <i class="mdi mdi-plus-circle me-2"></i>Tambah Data Sludge
                    </a>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-semibold">Filter Shift</label>
                            <select class="form-select form-select-lg" id="filterShift">
                                <option value="">Semua Shift</option>
                                <option value="1">Shift 1 (06:00 - 14:00)</option>
                                <option value="2">Shift 2 (14:00 - 22:00)</option>
                                <option value="3">Shift 3 (22:00 - 06:00)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-semibold">Filter Bulan</label>
                            <input type="month" class="form-control form-control-lg" id="filterBulan">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-semibold">Cari Data</label>
                            <input type="text" class="form-control form-control-lg" id="searchData" placeholder="Cari berdasarkan tanggal...">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary btn-lg w-100" id="btnReset">
                                <i class="mdi mdi-refresh me-2"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning bg-opacity-10 border-0 p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <div class="avatar-title bg-warning rounded-circle">
                                    <i class="mdi mdi-delete-variant"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0 text-warning fw-bold">
                                <i class="mdi mdi-table me-2"></i>Data Sludge Harian
                            </h5>
                            <p class="text-muted mb-0 small">Monitoring pengelolaan lumpur per shift kerja</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="sludgeTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">No</th>
                                    <th class="fw-semibold">Tanggal</th>
                                    <th class="fw-semibold">Shift</th>
                                    <th class="fw-semibold text-center">Drain Lumpur (m³)</th>
                                    <th class="fw-semibold text-center">Running Hour SCP (jam)</th>
                                    <th class="fw-semibold text-center">Hasil Lumpur (ton)</th>
                                    <th class="fw-semibold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="sludgeTableBody">
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="spinner-border text-warning" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div class="modal fade" id="detailSludgeModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title">
                            <i class="mdi mdi-delete-variant me-2"></i>Detail Data Sludge
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4" id="modalSludgeContent">
                        <!-- Content will be loaded dynamically -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-danger" id="btnDelete">
                            <i class="mdi mdi-trash-can me-2"></i>Hapus Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
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
                            <input type="hidden" id="edit_id" name="id">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_tanggal" class="form-label fw-semibold">
                                        Tanggal <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                                </div>
                                <div class="col-md-6">
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

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_drain_lumpur" class="form-label fw-semibold">
                                        Drain Lumpur (m³) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" class="form-control" id="edit_drain_lumpur" name="drain_lumpur" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_running_hour_scp" class="form-label fw-semibold">
                                        Running Hour SCP (jam) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" class="form-control" id="edit_running_hour_scp" name="running_hour_scp" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_hasil_lumpur" class="form-label fw-semibold">
                                        Hasil Lumpur (ton) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" class="form-control" id="edit_hasil_lumpur" name="hasil_lumpur" required>
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
        background-color: rgba(241, 180, 76, 0.1);
    }

    .detail-item {
        transition: all 0.2s ease;
    }

    .detail-item:hover {
        border-color: #f1b44c !important;
        background-color: rgba(241, 180, 76, 0.05);
    }

    .avatar-sm {
        width: 3rem;
        height: 3rem;
    }

    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .badge-shift {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }
</style>

<script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        let allData = [];
        let currentRecordId = null;
        const userJabatan = "{{ Auth::user()->jabatan }}";
        const canEditDelete = userJabatan !== 'operator';
        // Load data saat halaman pertama kali dimuat
        loadData();

        // Event listeners untuk filter
        $('#filterShift, #filterBulan, #searchData').on('change keyup', function() {
            filterData();
        });

        $('#btnReset').on('click', function() {
            $('#filterShift').val('');
            $('#filterBulan').val('');
            $('#searchData').val('');
            filterData();
        });

        // Delete button handler
        $('#btnDelete').on('click', function() {
            if (currentRecordId && confirm('Apakah Anda yakin ingin menghapus data sludge ini?')) {
                deleteRecord(currentRecordId);
            }
        });

        // Edit button handler
        $('#btnSaveEdit').on('click', function() {
            saveEdit();
        });

        // Load Data
        function loadData() {
            $.ajax({
                url: '/api/wwtp-sludge',
                method: 'GET',
                success: function(response) {
                    allData = response.data || [];
                    updateStatistics();
                    filterData();
                },
                error: function(xhr) {
                    console.error('Error loading data:', xhr);
                    showError('Gagal memuat data sludge');
                }
            });
        }

        // Update Statistics
        function updateStatistics() {
            // Total records
            $('#totalRecords').text(allData.length);

            // This week's data
            const now = new Date();
            const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay() + 1));
            startOfWeek.setHours(0, 0, 0, 0);

            const weekData = allData.filter(item => {
                const itemDate = new Date(item.tanggal);
                return itemDate >= startOfWeek;
            });
            $('#weekRecords').text(weekData.length);

            // Today's data
            const today = new Date().toISOString().split('T')[0];
            const todayData = allData.filter(item => item.tanggal === today);
            $('#todayRecords').text(todayData.length);

            // Average drain today
            if (todayData.length > 0) {
                const avgDrain = todayData.reduce((sum, item) => sum + parseFloat(item.drain_lumpur || 0), 0) / todayData.length;
                $('#avgDrain').text(avgDrain.toFixed(2));
            } else {
                $('#avgDrain').text('0');
            }
        }

        // Filter Data
        function filterData() {
            const shift = $('#filterShift').val();
            const bulan = $('#filterBulan').val();
            const search = $('#searchData').val().toLowerCase();

            let filtered = allData;

            // Filter by shift
            if (shift) {
                filtered = filtered.filter(item => item.shift == shift);
            }

            // Filter by bulan
            if (bulan) {
                filtered = filtered.filter(item => {
                    const itemMonth = item.tanggal.substring(0, 7);
                    return itemMonth === bulan;
                });
            }

            // Filter by search
            if (search) {
                filtered = filtered.filter(item => {
                    const tanggal = formatDate(item.tanggal).toLowerCase();
                    return tanggal.includes(search);
                });
            }

            renderTable(filtered);
        }

        // Render Table
        function renderTable(data) {
            const tbody = $('#sludgeTableBody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="mdi mdi-inbox me-2"></i>Tidak ada data sludge
                        </td>
                    </tr>
                `);
                return;
            }

            data.forEach((item, index) => {
                const shiftBadge = getShiftBadge(item.shift);

                // Conditional action buttons
                let actionButtons = `
            <button class="btn btn-sm btn-outline-primary me-1" 
                    onclick="showDetail(${item.id})"
                    data-bs-toggle="tooltip" 
                    title="Lihat Detail">
                <i class="mdi mdi-eye"></i>
            </button>
        `;

                if (canEditDelete) {
                    actionButtons += `
                <button class="btn btn-sm btn-outline-success me-1" 
                        onclick="showEdit(${item.id})"
                        data-bs-toggle="tooltip" 
                        title="Edit Data">
                    <i class="mdi mdi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" 
                        onclick="confirmDelete(${item.id})"
                        data-bs-toggle="tooltip" 
                        title="Hapus Data">
                    <i class="mdi mdi-trash-can"></i>
                </button>
            `;
                }

                tbody.append(`
            <tr class="data-row">
                <td>${index + 1}</td>
                <td>${formatDate(item.tanggal)}</td>
                <td>${shiftBadge}</td>
                <td class="text-center fw-bold">${parseFloat(item.drain_lumpur).toFixed(2)}</td>
                <td class="text-center fw-bold">${parseFloat(item.running_hour_scp).toFixed(2)}</td>
                <td class="text-center fw-bold">${parseFloat(item.hasil_lumpur).toFixed(2)}</td>
                <td class="text-center">${actionButtons}</td>
            </tr>
        `);
            });

            $('[data-bs-toggle="tooltip"]').tooltip();
        }

        // Show Detail
        window.showDetail = function(id) {
            $.ajax({
                url: `/api/wwtp-sludge/${id}`,
                method: 'GET',
                success: function(record) {
                    currentRecordId = id;

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
                            <p class="fw-bold mb-0">${formatDateTime(record.created_at)}</p>
                        </div>
                    </div>
                </div>
                
                <h6 class="fw-bold mb-3 text-warning">Data Pengelolaan Sludge</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-item p-4 border rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted">Drain Lumpur</span>
                                    <p class="fw-bold fs-3 mb-0 text-warning">${parseFloat(record.drain_lumpur).toFixed(2)} <small class="text-muted fs-6">m³</small></p>
                                </div>
                                <i class="mdi mdi-hydraulic-oil-level fs-1 text-warning opacity-25"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item p-4 border rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted">Running Hour SCP</span>
                                    <p class="fw-bold fs-3 mb-0 text-warning">${parseFloat(record.running_hour_scp).toFixed(2)} <small class="text-muted fs-6">jam</small></p>
                                </div>
                                <i class="mdi mdi-clock-outline fs-1 text-warning opacity-25"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item p-4 border rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted">Hasil Lumpur</span>
                                    <p class="fw-bold fs-3 mb-0 text-warning">${parseFloat(record.hasil_lumpur).toFixed(2)} <small class="text-muted fs-6">ton</small></p>
                                </div>
                                <i class="mdi mdi-clock-outline fs-1 text-warning opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                    $('#modalSludgeContent').html(content);

                    // Show/hide delete button based on role
                    if (canEditDelete) {
                        $('#btnDelete').show();
                    } else {
                        $('#btnDelete').hide();
                    }

                    new bootstrap.Modal(document.getElementById('detailSludgeModal')).show();
                },
                error: function(xhr) {
                    console.error('Error loading detail:', xhr);
                    showError('Gagal memuat detail data');
                }
            });
        }

        // Show Edit
        window.showEdit = function(id) {
            $.ajax({
                url: `/api/wwtp-sludge/${id}`,
                method: 'GET',
                success: function(record) {
                    currentRecordId = id;

                    $('#edit_id').val(record.id);
                    $('#edit_tanggal').val(record.tanggal);
                    $('#edit_shift').val(record.shift);
                    $('#edit_drain_lumpur').val(record.drain_lumpur);
                    $('#edit_running_hour_scp').val(record.running_hour_scp);
                    $('#edit_hasil_lumpur').val(record.hasil_lumpur); // ← tambah ini

                    new bootstrap.Modal(document.getElementById('editSludgeModal')).show();
                },
                error: function(xhr) {
                    showError('Gagal memuat data untuk diedit');
                }
            });
        }

        // Save Edit
        function saveEdit() {
            const id = $('#edit_id').val();
            const btnSave = $('#btnSaveEdit');
            const originalText = btnSave.html();
            btnSave.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

            const formData = {
                tanggal: $('#edit_tanggal').val(),
                shift: $('#edit_shift').val(),
                drain_lumpur: $('#edit_drain_lumpur').val(),
                running_hour_scp: $('#edit_running_hour_scp').val(),
                hasil_lumpur: $('#edit_hasil_lumpur').val()
            };

            $.ajax({
                url: `/api/wwtp-sludge/${id}`,
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#editSludgeModal').modal('hide');
                    showSuccess('Data sludge berhasil diperbarui');
                    loadData();
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    let message = 'Terjadi kesalahan saat menyimpan data!';

                    if (error && error.message) {
                        message = error.message;
                    } else if (error && error.errors) {
                        message = Object.values(error.errors).flat().join('<br>');
                    }

                    showError(message);
                },
                complete: function() {
                    btnSave.prop('disabled', false).html(originalText);
                }
            });
        }

        // Confirm Delete
        window.confirmDelete = function(id) {
            currentRecordId = id;

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data sludge ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteRecord(id);
                }
            });
        }

        // Delete Record
        function deleteRecord(id) {
            $.ajax({
                url: `/api/wwtp-sludge/${id}`,
                method: 'DELETE',
                success: function(response) {
                    $('#detailSludgeModal').modal('hide');
                    showSuccess('Data sludge berhasil dihapus');
                    loadData();
                },
                error: function(xhr) {
                    console.error('Error deleting record:', xhr);
                    showError('Gagal menghapus data sludge');
                }
            });
        }

        // Helper Functions
        function getShiftLabel(shift) {
            const labels = {
                '1': 'Shift 1 (06:00 - 14:00)',
                '2': 'Shift 2 (14:00 - 22:00)',
                '3': 'Shift 3 (22:00 - 06:00)'
            };
            return labels[shift] || shift;
        }

        function getShiftBadge(shift) {
            const badges = {
                '1': '<span class="badge bg-primary badge-shift">Shift 1</span>',
                '2': '<span class="badge bg-success badge-shift">Shift 2</span>',
                '3': '<span class="badge bg-info badge-shift">Shift 3</span>'
            };
            return badges[shift] || `<span class="badge bg-secondary badge-shift">${shift}</span>`;
        }

        function formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        function formatDateTime(dateString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
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
                html: message,
                confirmButtonColor: '#d33'
            });
        }
    });
</script>
@endsection