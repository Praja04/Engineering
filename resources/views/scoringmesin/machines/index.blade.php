@extends('layouts.app')

@section('title', 'Master Data Mesin')

@section('styles')
<style>
    .card-hover:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
    }

    .machine-icon {
        font-size: 3rem;
        color: #405189;
    }

    .status-badge {
        font-size: 0.875rem;
    }

    .btn-action {
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(64, 81, 137, 0.05);
    }

    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Master Data Mesin</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Scoring Mesin</a></li>
                            <li class="breadcrumb-item active">Master Data Mesin</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate card-hover" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Total Mesin</p>
                                <h4 class="mb-0" id="totalMachines">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-primary rounded fs-3">
                                        <i class="bx bx-cog text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate card-hover" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Mesin Aktif</p>
                                <h4 class="mb-0 text-success" id="activeMachines">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-success rounded fs-3">
                                        <i class="bx bx-check-circle text-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate card-hover" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Mesin Maintenance</p>
                                <h4 class="mb-0 text-warning" id="maintenanceMachines">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-warning rounded fs-3">
                                        <i class="bx bx-wrench text-warning"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate card-hover" data-aos="fade-up" data-aos-delay="400">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Mesin Rusak</p>
                                <h4 class="mb-0 text-danger" id="brokenMachines">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-danger rounded fs-3">
                                        <i class="bx bx-x-circle text-danger"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card" data-aos="fade-up" data-aos-delay="500">
                    <div class="card-header border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-settings-2-line align-middle me-1"></i>
                                Daftar Mesin
                            </h5>
                            <div class="flex-shrink-0">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMachineModal">
                                    <i class="ri-add-line align-bottom me-1"></i> Tambah Mesin
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter dan Search -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="search-box">
                                    <input type="text" id="searchMachine" class="form-control search" placeholder="Cari mesin...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select" id="statusFilter">
                                    <option value="">Semua Status</option>
                                    <option value="active">Aktif</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="broken">Rusak</option>
                                </select>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0" id="machineTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Kode Mesin</th>
                                        <th scope="col">Nama Mesin</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Tanggal Dibuat</th>
                                        <th scope="col">Terakhir Update</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="machineTableBody">
                                    <!-- Loading skeleton -->
                                    <tr id="loadingRow">
                                        <td colspan="7" class="text-center py-4">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <div class="spinner-border text-primary me-2" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <span>Memuat data mesin...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="row align-items-center mt-4" id="paginationContainer" style="display: none;">
                            <div class="col-sm-6">
                                <div class="text-muted" id="paginationInfo">
                                    <!-- Pagination info will be loaded here -->
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="pagination-wrap hstack justify-content-end" id="paginationLinks">
                                    <!-- Pagination links will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->

<!-- Modal Tambah Mesin -->
<div class="modal fade" id="addMachineModal" tabindex="-1" aria-labelledby="addMachineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMachineModalLabel">
                    <i class="ri-add-line me-2"></i>Tambah Mesin Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addMachineForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="machineCode" class="form-label">Kode Mesin <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="machineCode" name="code" placeholder="Contoh: MCH-001" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="machineName" class="form-label">Nama Mesin <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="machineName" name="name" placeholder="Contoh: Pasteurizer" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="machineStatus" class="form-label">Status</label>
                        <select class="form-select" id="machineStatus" name="status">
                            <option value="active" selected>Aktif</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="broken">Rusak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="machineDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="machineDescription" name="description" rows="3" placeholder="Deskripsi mesin (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Mesin -->
<div class="modal fade" id="editMachineModal" tabindex="-1" aria-labelledby="editMachineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMachineModalLabel">
                    <i class="ri-pencil-line me-2"></i>Edit Mesin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMachineForm">
                <input type="hidden" id="editMachineId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editMachineCode" class="form-label">Kode Mesin <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editMachineCode" name="code" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editMachineName" class="form-label">Nama Mesin <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editMachineName" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editMachineStatus" class="form-label">Status</label>
                        <select class="form-select" id="editMachineStatus" name="status">
                            <option value="active">Aktif</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="broken">Rusak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editMachineDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="editMachineDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-save-line me-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Mesin -->
<div class="modal fade" id="viewMachineModal" tabindex="-1" aria-labelledby="viewMachineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMachineModalLabel">
                    <i class="ri-eye-line me-2"></i>Detail Mesin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="machineDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Global variables
    let machinesData = [];
    let filteredData = [];
    let currentPage = 1;
    let itemsPerPage = 10;

    $(document).ready(function() {
        // Load initial data
        loadMachines();

        // Search functionality
        $('#searchMachine').on('keyup', function() {
            filterAndRenderTable();
        });

        // Status filter
        $('#statusFilter').on('change', function() {
            filterAndRenderTable();
        });

        // Form submissions
        setupFormHandlers();
    });

    // Load machines data from API
    function loadMachines() {
        $.ajax({
            url: "{{url('/scoring-mesin/machines')}}",
            type: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            success: function(response) {
                machinesData = response;
                filteredData = [...machinesData];
                renderTable();
                updateStatistics();
            },
            error: function(xhr) {
                console.error('Error loading machines:', xhr);
                showError();
            }
        });
    }

    // Filter and render table
    function filterAndRenderTable() {
        const searchTerm = $('#searchMachine').val().toLowerCase();
        const statusFilter = $('#statusFilter').val();

        filteredData = machinesData.filter(machine => {
            const matchesSearch = machine.name.toLowerCase().includes(searchTerm) ||
                machine.code.toLowerCase().includes(searchTerm);
            const matchesStatus = !statusFilter || machine.status === statusFilter;

            return matchesSearch && matchesStatus;
        });

        currentPage = 1; // Reset to first page when filtering
        renderTable();
    }

    // Render table with data
    function renderTable() {
        const tbody = $('#machineTableBody');

        if (filteredData.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">
                            <i class="ri-database-2-line fs-24 mb-2 d-block"></i>
                            Belum ada data mesin
                        </div>
                    </td>
                </tr>
            `);
            $('#paginationContainer').hide();
            return;
        }

        // Calculate pagination
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedData = filteredData.slice(startIndex, endIndex);

        // Render table rows
        let html = '';
        paginatedData.forEach((machine, index) => {
            const globalIndex = startIndex + index + 1;
            const statusClass = getStatusClass(machine.status);
            const statusText = getStatusText(machine.status);

            html += `
                <tr>
                    <td>${globalIndex}</td>
                    <td>
                        <span class="badge bg-soft-info text-info">${machine.code}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs me-3">
                                <span class="avatar-title rounded-circle bg-soft-primary text-primary fs-16">
                                    <i class="bx bx-cog"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">${machine.name}</h6>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge ${statusClass} status-badge">${statusText}</span>
                    </td>
                    <td>${formatDate(machine.created_at)}</td>
                    <td>${formatDate(machine.updated_at)}</td>
                    <td>
                        <div class="hstack gap-2">
                            <button type="button" class="btn btn-soft-primary btn-sm btn-action" 
                                    onclick="viewMachine(${machine.id})" 
                                    data-bs-toggle="modal" data-bs-target="#viewMachineModal"
                                    title="Lihat Detail">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button type="button" class="btn btn-soft-success btn-sm btn-action" 
                                    onclick="editMachine(${machine.id})" 
                                    data-bs-toggle="modal" data-bs-target="#editMachineModal"
                                    title="Edit">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button type="button" class="btn btn-soft-danger btn-sm btn-action" 
                                    onclick="deleteMachine(${machine.id})" 
                                    title="Hapus">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.html(html);
        renderPagination();
    }

    // Render pagination
    function renderPagination() {
        const totalItems = filteredData.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);

        if (totalPages <= 1) {
            $('#paginationContainer').hide();
            return;
        }

        const startItem = (currentPage - 1) * itemsPerPage + 1;
        const endItem = Math.min(currentPage * itemsPerPage, totalItems);

        // Update pagination info
        $('#paginationInfo').html(`Menampilkan ${startItem} sampai ${endItem} dari ${totalItems} data`);

        // Generate pagination links
        let paginationHtml = '<ul class="pagination pagination-separated justify-content-center justify-content-sm-end mb-sm-0">';

        // Previous button
        paginationHtml += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a href="#" class="page-link" onclick="changePage(${currentPage - 1}); return false;">Previous</a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHtml += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a href="#" class="page-link" onclick="changePage(${i}); return false;">${i}</a>
                    </li>
                `;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Next button
        paginationHtml += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a href="#" class="page-link" onclick="changePage(${currentPage + 1}); return false;">Next</a>
            </li>
        `;

        paginationHtml += '</ul>';

        $('#paginationLinks').html(paginationHtml);
        $('#paginationContainer').show();
    }

    // Change page
    function changePage(page) {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        if (page >= 1 && page <= totalPages && page !== currentPage) {
            currentPage = page;
            renderTable();
        }
    }

    // Update statistics
    function updateStatistics() {
        const total = machinesData.length;
        const active = machinesData.filter(m => m.status === 'active').length;
        const maintenance = machinesData.filter(m => m.status === 'maintenance').length;
        const broken = machinesData.filter(m => m.status === 'broken').length;

        $('#totalMachines').text(total);
        $('#activeMachines').text(active);
        $('#maintenanceMachines').text(maintenance);
        $('#brokenMachines').text(broken);
    }

    // Utility functions
    function getStatusClass(status) {
        const statusClasses = {
            'active': 'bg-success',
            'maintenance': 'bg-warning',
            'broken': 'bg-danger'
        };
        return statusClasses[status] || 'bg-secondary';
    }

    function getStatusText(status) {
        const statusTexts = {
            'active': 'Aktif',
            'maintenance': 'Maintenance',
            'broken': 'Rusak'
        };
        return statusTexts[status] || 'Unknown';
    }

    function formatDate(dateString) {
        if (!dateString) return '-';

        // Check if moment is available
        if (typeof moment !== 'undefined') {
            return moment(dateString).format('DD/MM/YYYY HH:mm');
        }

        // Fallback to native Date formatting
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');

        return `${day}/${month}/${year} ${hours}:${minutes}`;
    }

    function showError() {
        $('#machineTableBody').html(`
            <tr>
                <td colspan="7" class="text-center py-4 text-danger">
                    <i class="ri-error-warning-line fs-24 mb-2 d-block"></i>
                    Gagal memuat data mesin
                    <br>
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadMachines()">
                        <i class="ri-refresh-line me-1"></i>Coba Lagi
                    </button>
                </td>
            </tr>
        `);
    }

    // Setup form handlers
    function setupFormHandlers() {
        // Add Machine Form Submit
        $('#addMachineForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            // Show loading
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            $.ajax({
                url: "{{url('/scoring-mesin/machines')}}",
                type: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Mesin berhasil ditambahkan',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        alert('Mesin berhasil ditambahkan');
                    }

                    $('#addMachineModal').modal('hide');
                    $('#addMachineForm')[0].reset();
                    loadMachines(); // Reload data
                },
                error: function(xhr) {
                    let errorMsg = 'Terjadi kesalahan';

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMsg = Object.values(errors)[0][0];
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg
                        });
                    } else {
                        alert('Error: ' + errorMsg);
                    }
                }
            });
        });

        // Edit Machine Form Submit
        $('#editMachineForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            const id = $('#editMachineId').val();

            // Show loading
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            $.ajax({
                url: "{{url('/scoring-mesin/machines')}}/" + id,
                type: 'PUT',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Mesin berhasil diupdate',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        alert('Mesin berhasil diupdate');
                    }

                    $('#editMachineModal').modal('hide');
                    loadMachines(); // Reload data
                },
                error: function(xhr) {
                    let errorMsg = 'Terjadi kesalahan';

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMsg = Object.values(errors)[0][0];
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg
                        });
                    } else {
                        alert('Error: ' + errorMsg);
                    }
                }
            });
        });
    }

    // View Machine Detail
    function viewMachine(id) {
        // Show loading
        $('#machineDetailContent').html(`
            <div class="text-center py-4">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat detail mesin...</p>
            </div>
        `);

        $.ajax({
            url: "{{url('/scoring-mesin/machines')}}/" + id,
            type: 'GET',
            success: function(response) {
                const machine = response;
                const statusClass = getStatusClass(machine.status).replace('bg-', '');
                const statusText = getStatusText(machine.status);

                $('#machineDetailContent').html(`
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Kode Mesin:</label>
                                <p class="mb-0">${machine.code}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Mesin:</label>
                                <p class="mb-0">${machine.name}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status:</label>
                                <p class="mb-0">
                                    <span class="badge bg-${statusClass}">${statusText}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Dibuat:</label>
                                <p class="mb-0">${formatDate(machine.created_at)}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Terakhir Update:</label>
                                <p class="mb-0">${formatDate(machine.updated_at)}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Deskripsi:</label>
                                <p class="mb-0">${machine.description || '-'}</p>
                            </div>
                        </div>
                    </div>
                `);
            },
            error: function() {
                $('#machineDetailContent').html(`
                    <div class="text-center py-4 text-danger">
                        <i class="ri-error-warning-line fs-24 mb-2 d-block"></i>
                        Gagal memuat detail mesin
                    </div>
                `);
            }
        });
    }

    // Edit Machine
    function editMachine(id) {
        // Find machine in current data
        const machine = machinesData.find(m => m.id === id);
        if (machine) {
            $('#editMachineId').val(machine.id);
            $('#editMachineCode').val(machine.code);
            $('#editMachineName').val(machine.name);
            $('#editMachineStatus').val(machine.status || 'active');
            $('#editMachineDescription').val(machine.description || '');
        } else {
            // Fallback to API call if not found in current data
            $.ajax({
                url: "{{url('/scoring-mesin/machines')}}/" + id,
                type: 'GET',
                success: function(response) {
                    const machine = response;
                    $('#editMachineId').val(machine.id);
                    $('#editMachineCode').val(machine.code);
                    $('#editMachineName').val(machine.name);
                    $('#editMachineStatus').val(machine.status || 'active');
                    $('#editMachineDescription').val(machine.description || '');
                },
                error: function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal memuat data mesin'
                        });
                    } else {
                        alert('Gagal memuat data mesin');
                    }
                }
            });
        }
    }

    // Delete Machine
    function deleteMachine(id) {
        const confirmDelete = () => {
            // Show loading
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            $.ajax({
                url: "{{url('/scoring-mesin/machines/')}}/" +id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Mesin berhasil dihapus',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        alert('Mesin berhasil dihapus');
                    }

                    loadMachines(); // Reload data
                },
                error: function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal menghapus mesin'
                        });
                    } else {
                        alert('Gagal menghapus mesin');
                    }
                }
            });
        };

        // Show confirmation dialog
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data mesin akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    confirmDelete();
                }
            });
        } else {
            if (confirm('Apakah Anda yakin ingin menghapus mesin ini?')) {
                confirmDelete();
            }
        }
    }
</script>
@endsection