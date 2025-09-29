@extends('layouts.app')

@section('title', 'Master Data Part')

@section('styles')
<style>
    .card-hover:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
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
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Master Data Part</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Scoring Mesin</a></li>
                            <li class="breadcrumb-item active">Part</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-4 col-md-6">
                <div class="card card-animate card-hover" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Total Part</p>
                                <h4 class="mb-0" id="totalParts">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-primary rounded fs-3">
                                        <i class="bx bx-cube text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="card card-animate card-hover" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Total Section</p>
                                <h4 class="mb-0 text-info" id="totalSections">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-info rounded fs-3">
                                        <i class="bx bx-grid-alt text-info"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="card card-animate card-hover" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Part Terbaru</p>
                                <h6 class="mb-0" id="latestPart">-</h6>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-success rounded fs-3">
                                        <i class="bx bx-time text-success"></i>
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
                <div class="card" data-aos="fade-up" data-aos-delay="400">
                    <div class="card-header border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-stack-line align-middle me-1"></i>
                                Daftar Part
                            </h5>
                            <div class="flex-shrink-0">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPartModal">
                                    <i class="ri-add-line align-bottom me-1"></i> Tambah Part
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter dan Search -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="search-box">
                                    <input type="text" id="searchPart" class="form-control search" placeholder="Cari part...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="machineFilter">
                                    <option value="">Semua Mesin</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="processFilter">
                                    <option value="">Semua Proses</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="sectionFilter">
                                    <option value="">Semua Section</option>
                                </select>
                            </div>
                        </div>


                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Nama Part</th>
                                        <th scope="col">Section</th>
                                        <th scope="col">Parameter</th>
                                        <th scope="col">Mesin</th>
                                        <th scope="col">Tanggal Dibuat</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="partTableBody">
                                    <tr id="loadingRow">
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <div class="spinner-border text-primary me-2" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <span>Memuat data part...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="row align-items-center mt-4" id="paginationContainer" style="display: none;">
                            <div class="col-sm-6">
                                <div class="text-muted" id="paginationInfo"></div>
                            </div>
                            <div class="col-sm-6">
                                <div class="pagination-wrap hstack justify-content-end" id="paginationLinks"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Tambah Part -->
<div class="modal fade" id="addPartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-add-line me-2"></i>Tambah Part
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addPartForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="partSection" class="form-label">Section <span class="text-danger">*</span></label>
                        <select class="form-select" id="partSection" name="section_id" required>
                            <option value="">Pilih Section</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="partName" class="form-label">Nama Part <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="partName" name="name" placeholder="Contoh: Part A1" required>
                        <div class="invalid-feedback"></div>
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

<!-- Modal Edit Part -->
<div class="modal fade" id="editPartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-pencil-line me-2"></i>Edit Part
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPartForm">
                <input type="hidden" id="editPartId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editPartSection" class="form-label">Section <span class="text-danger">*</span></label>
                        <select class="form-select" id="editPartSection" name="section_id" required>
                            <option value="">Pilih Section</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editPartName" class="form-label">Nama Part <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editPartName" name="name" required>
                        <div class="invalid-feedback"></div>
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

<!-- Modal View Part -->
<div class="modal fade" id="viewPartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-eye-line me-2"></i>Detail Part
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="partDetailContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    let partsData = [];
    let sectionsData = [];
    let filteredData = [];
    let currentPage = 1;
    let itemsPerPage = 10;

    $(document).ready(function() {
        loadSections();
        loadParts();

        $('#searchPart').on('keyup', filterAndRenderTable);
        $('#sectionFilter').on('change', filterAndRenderTable);

        setupFormHandlers();
    });

    function loadSections() {
        $.ajax({
            url: "/scoring-mesin/sections",
            type: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            success: function(response) {
                sectionsData = response;
                populateSectionSelects();
                populateMachineAndProcessFilters();
            }
        });
    }

    function populateSectionSelects() {
        let options = '<option value="">Pilih Section</option>';
        let filterOptions = '<option value="">Semua Section</option>';

        console.log(sectionsData);

        sectionsData.forEach(section => {
            // pastikan relasi tersedia agar tidak error
            const paramName = section.process_parameter ? section.process_parameter.name : '';
            const machineName = section.process_parameter && section.process_parameter.machine ? section.process_parameter.machine.name : '';

            // gabungkan jadi satu teks (skip yang kosong)
            let displayText = section.name;
            if (paramName) displayText += ` - ${paramName}`;
            if (machineName) displayText += ` - ${machineName}`;

            options += `<option value="${section.id}">${displayText}</option>`;
            filterOptions += `<option value="${section.id}">${displayText}</option>`;
        });

        $('#partSection, #editPartSection').html(options);
        $('#sectionFilter').html(filterOptions);
    }

    function populateMachineAndProcessFilters() {
        const machines = [];
        const processes = [];

        sectionsData.forEach(section => {
            if (section.process_parameter) {
                processes.push({
                    id: section.process_parameter.id,
                    name: section.process_parameter.name
                });
                if (section.process_parameter.machine) {
                    machines.push({
                        id: section.process_parameter.machine.id,
                        name: section.process_parameter.machine.name
                    });
                }
            }
        });

        // unikkan
        const uniqueMachines = machines.filter((v, i, a) => a.findIndex(t => t.id === v.id) === i);
        const uniqueProcesses = processes.filter((v, i, a) => a.findIndex(t => t.id === v.id) === i);

        let machineOptions = '<option value="">Semua Mesin</option>';
        uniqueMachines.forEach(m => machineOptions += `<option value="${m.id}">${m.name}</option>`);

        let processOptions = '<option value="">Semua Proses</option>';
        uniqueProcesses.forEach(p => processOptions += `<option value="${p.id}">${p.name}</option>`);

        $('#machineFilter').html(machineOptions);
        $('#processFilter').html(processOptions);
    }


    function loadParts() {
        $.ajax({
            url: "/scoring-mesin/parts",
            type: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            success: function(response) {
                partsData = response;
                filteredData = [...partsData];
                renderTable();
                updateStatistics();
            },
            error: function(xhr) {
                showError();
            }
        });
    }

    function filterAndRenderTable() {
        const searchTerm = $('#searchPart').val().toLowerCase();
        const sectionFilter = $('#sectionFilter').val();

        filteredData = partsData.filter(part => {
            const matchesSearch = part.name.toLowerCase().includes(searchTerm) ||
                (part.section && part.section.name.toLowerCase().includes(searchTerm));
            const matchesSection = !sectionFilter || part.section_id == sectionFilter;

            return matchesSearch && matchesSection;
        });

        currentPage = 1;
        renderTable();
    }

    function renderTable() {
        const tbody = $('#partTableBody');

        if (filteredData.length === 0) {
            tbody.html(`
            <tr>
                <td colspan="7" class="text-center py-4"> <!-- Ubah dari 6 ke 7 -->
                    <div class="text-muted">
                        <i class="ri-database-2-line fs-24 mb-2 d-block"></i>
                        Belum ada data part
                    </div>
                </td>
            </tr>
        `);
            $('#paginationContainer').hide();
            return;
        }

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedData = filteredData.slice(startIndex, endIndex);

        let html = '';
        paginatedData.forEach((part, index) => {
            const globalIndex = startIndex + index + 1;
            const sectionName = part.section ? part.section.name : '-';
            const parameterName = part.section && part.section.process_parameter ?
                part.section.process_parameter.name : '-';
            const machineName = part.section && part.section.process_parameter &&
                part.section.process_parameter.machine ?
                part.section.process_parameter.machine.name : '-'; // Tambahkan ini

            html += `
            <tr>
                <td>${globalIndex}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs me-3">
                            <span class="avatar-title rounded-circle bg-soft-primary text-primary fs-16">
                                <i class="bx bx-cube"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">${part.name}</h6>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-soft-info text-info">${sectionName}</span>
                </td>
                <td>${parameterName}</td>
                <td>${machineName}</td> <!-- Tambahkan ini -->
                <td>${formatDate(part.created_at)}</td>
                <td>
                    <div class="hstack gap-2">
                        <button type="button" class="btn btn-soft-primary btn-sm btn-action" 
                                onclick="viewPart(${part.id})" 
                                data-bs-toggle="modal" data-bs-target="#viewPartModal"
                                title="Lihat Detail">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button type="button" class="btn btn-soft-success btn-sm btn-action" 
                                onclick="editPart(${part.id})" 
                                data-bs-toggle="modal" data-bs-target="#editPartModal"
                                title="Edit">
                            <i class="ri-pencil-line"></i>
                        </button>
                        <button type="button" class="btn btn-soft-danger btn-sm btn-action" 
                                onclick="deletePart(${part.id})" 
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

    function renderPagination() {
        const totalItems = filteredData.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);

        if (totalPages <= 1) {
            $('#paginationContainer').hide();
            return;
        }

        const startItem = (currentPage - 1) * itemsPerPage + 1;
        const endItem = Math.min(currentPage * itemsPerPage, totalItems);

        $('#paginationInfo').html(`Menampilkan ${startItem} sampai ${endItem} dari ${totalItems} data`);

        let paginationHtml = '<ul class="pagination pagination-separated justify-content-center justify-content-sm-end mb-sm-0">';

        paginationHtml += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a href="#" class="page-link" onclick="changePage(${currentPage - 1}); return false;">Previous</a>
            </li>
        `;

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

        paginationHtml += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a href="#" class="page-link" onclick="changePage(${currentPage + 1}); return false;">Next</a>
            </li>
        `;

        paginationHtml += '</ul>';
        $('#paginationLinks').html(paginationHtml);
        $('#paginationContainer').show();
    }

    function changePage(page) {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        if (page >= 1 && page <= totalPages && page !== currentPage) {
            currentPage = page;
            renderTable();
        }
    }

    function updateStatistics() {
        const total = partsData.length;
        const uniqueSections = [...new Set(partsData.map(p => p.section_id))].length;
        const latest = partsData.length > 0 ? partsData[partsData.length - 1].name : '-';

        $('#totalParts').text(total);
        $('#totalSections').text(uniqueSections);
        $('#latestPart').text(latest);
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        if (typeof moment !== 'undefined') {
            return moment(dateString).format('DD/MM/YYYY HH:mm');
        }
        const date = new Date(dateString);
        return date.toLocaleString('id-ID');
    }

    function showError() {
        $('#partTableBody').html(`
            <tr>
                <td colspan="6" class="text-center py-4 text-danger">
                    <i class="ri-error-warning-line fs-24 mb-2 d-block"></i>
                    Gagal memuat data part
                    <br>
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadParts()">
                        <i class="ri-refresh-line me-1"></i>Coba Lagi
                    </button>
                </td>
            </tr>
        `);
    }

    function setupFormHandlers() {
        $('#addPartForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            }

            $.ajax({
                url: "/scoring-mesin/parts",
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
                            text: 'Part berhasil ditambahkan',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                    $('#addPartModal').modal('hide');
                    $('#addPartForm')[0].reset();
                    loadParts();
                },
                error: function(xhr) {
                    const errorMsg = xhr.status === 422 && xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors)[0][0] : 'Terjadi kesalahan';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg
                        });
                    }
                }
            });
        });

        $('#editPartForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            const id = $('#editPartId').val();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            }

            $.ajax({
                url: `/scoring-mesin/parts/${id}`,
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
                            text: 'Part berhasil diupdate',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                    $('#editPartModal').modal('hide');
                    loadParts();
                },
                error: function(xhr) {
                    const errorMsg = xhr.status === 422 && xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors)[0][0] : 'Terjadi kesalahan';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg
                        });
                    }
                }
            });
        });
    }

    function viewPart(id) {
        $('#partDetailContent').html(`
            <div class="text-center py-4">
                <div class="spinner-border" role="status"></div>
                <p class="mt-2">Memuat detail part...</p>
            </div>
        `);

        $.ajax({
            url: `/scoring-mesin/parts/${id}`,
            type: 'GET',
            success: function(response) {
                const sectionName = response.section ? response.section.name : '-';
                const parameterName = response.section && response.section.process_parameter ?
                    response.section.process_parameter.name : '-';

                $('#partDetailContent').html(`
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Part:</label>
                        <p class="mb-0">${response.name}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Section:</label>
                        <p class="mb-0">${sectionName}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Parameter Proses:</label>
                        <p class="mb-0">${parameterName}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Dibuat:</label>
                        <p class="mb-0">${formatDate(response.created_at)}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Terakhir Update:</label>
                        <p class="mb-0">${formatDate(response.updated_at)}</p>
                    </div>
                `);
            }
        });
    }

    function editPart(id) {
        const part = partsData.find(p => p.id === id);
        if (part) {
            $('#editPartId').val(part.id);
            $('#editPartName').val(part.name);
            $('#editPartSection').val(part.section_id);
        }
    }

    function deletePart(id) {
        const confirmDelete = () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            }

            $.ajax({
                url: `/scoring-mesin/parts/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Part berhasil dihapus',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                    loadParts();
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data part akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) confirmDelete();
            });
        } else {
            if (confirm('Apakah Anda yakin ingin menghapus part ini?')) confirmDelete();
        }
    }
</script>
@endsection