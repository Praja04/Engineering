@extends('layouts.app')

@section('title', 'Master Data Section')

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
                    <h4 class="mb-sm-0">Master Data Section</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Scoring Mesin</a></li>
                            <li class="breadcrumb-item active">Section</li>
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
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Total Section</p>
                                <h4 class="mb-0" id="totalSections">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-primary rounded fs-3">
                                        <i class="bx bx-grid-alt text-primary"></i>
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
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Total Parameter</p>
                                <h4 class="mb-0 text-info" id="totalParameters">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-info rounded fs-3">
                                        <i class="bx bx-slider text-info"></i>
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
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Section Terbaru</p>
                                <h6 class="mb-0" id="latestSection">-</h6>
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
                                <i class="ri-layout-grid-line align-middle me-1"></i>
                                Daftar Section
                            </h5>
                            <div class="flex-shrink-0">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                                    <i class="ri-add-line align-bottom me-1"></i> Tambah Section
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter dan Search -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="search-box">
                                    <input type="text" id="searchSection" class="form-control search" placeholder="Cari section...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="parameterFilter">
                                    <option value="">Semua Parameter</option>
                                    <!-- Options will be loaded dynamically -->
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="machineFilter">
                                    <option value="">Semua Mesin</option>
                                </select>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Nama Section</th>
                                        <th scope="col">Parameter Proses</th>
                                        <th scope="col">Mesin</th>
                                        <th scope="col">Tanggal Dibuat</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="sectionTableBody">
                                    <tr id="loadingRow">
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <div class="spinner-border text-primary me-2" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <span>Memuat data section...</span>
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

<!-- Modal Tambah Section -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-add-line me-2"></i>Tambah Section
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addSectionForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="sectionParameter" class="form-label">Parameter Proses <span class="text-danger">*</span></label>
                        <select class="form-select" id="sectionParameter" name="process_parameter_id" required>
                            <option value="">Pilih Parameter</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="sectionName" class="form-label">Nama Section <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sectionName" name="name" placeholder="Contoh: Section A" required>
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

<!-- Modal Edit Section -->
<div class="modal fade" id="editSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-pencil-line me-2"></i>Edit Section
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSectionForm">
                <input type="hidden" id="editSectionId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editSectionParameter" class="form-label">Parameter Proses <span class="text-danger">*</span></label>
                        <select class="form-select" id="editSectionParameter" name="process_parameter_id" required>
                            <option value="">Pilih Parameter</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editSectionName" class="form-label">Nama Section <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editSectionName" name="name" required>
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

<!-- Modal View Section -->
<div class="modal fade" id="viewSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-eye-line me-2"></i>Detail Section
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="sectionDetailContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    let sectionsData = [];
    let parametersData = [];
    let filteredData = [];
    let currentPage = 1;
    let itemsPerPage = 10;

    $(document).ready(function() {
        loadParameters();
        loadSections();

        $('#searchSection').on('keyup', filterAndRenderTable);
        $('#parameterFilter').on('change', filterAndRenderTable);
        $('#machineFilter').on('change', filterAndRenderTable);

        setupFormHandlers();
    });

    /* =========================
       🔹 LOAD DATA
    ==========================*/
    function loadParameters() {
        $.ajax({
            url: "/scoring-mesin/process-parameters",
            type: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            success: function(response) {
                parametersData = response;
                populateParameterSelects();
            }
        });
    }

    function populateParameterSelects() {
        let options = '<option value="">Pilih Parameter</option>';
        let filterOptions = '<option value="">Semua Parameter</option>';

        parametersData.forEach(param => {
            const machineInfo = param.machine ? ` - ${param.machine.name}` : '';
            options += `<option value="${param.id}">${param.name}${machineInfo}</option>`;
            filterOptions += `<option value="${param.id}">${param.name}${machineInfo}</option>`;
        });

        $('#sectionParameter, #editSectionParameter').html(options);
        $('#parameterFilter').html(filterOptions);
    }

    function loadSections() {
        $.ajax({
            url: "/scoring-mesin/sections",
            type: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            success: function(response) {
                sectionsData = response;
                filteredData = [...sectionsData];
                renderTable();
                updateStatistics();
                populateMachineFilter(); // ✅ isi filter mesin setelah data masuk
            },
            error: function() {
                showError();
            }
        });
    }

    /* =========================
       🔹 FILTER MESIN
    ==========================*/
    function populateMachineFilter() {
        const uniqueMachines = [];

        sectionsData.forEach(section => {
            const machine = section.process_parameter?.machine;
            if (machine && !uniqueMachines.find(m => m.id === machine.id)) {
                uniqueMachines.push(machine);
            }
        });

        let options = '<option value="">Semua Mesin</option>';
        uniqueMachines.forEach(machine => {
            options += `<option value="${machine.id}">${machine.name}</option>`;
        });

        $('#machineFilter').html(options);
    }

    /* =========================
       🔹 FILTER + RENDER TABLE
    ==========================*/
    function filterAndRenderTable() {
        const searchTerm = $('#searchSection').val().toLowerCase();
        const parameterFilter = $('#parameterFilter').val();
        const machineFilter = $('#machineFilter').val();

        filteredData = sectionsData.filter(section => {
            const processParam = section.process_parameter;
            const machine = processParam?.machine;

            const matchesSearch =
                section.name.toLowerCase().includes(searchTerm) ||
                (processParam && processParam.name.toLowerCase().includes(searchTerm)) ||
                (machine && machine.name.toLowerCase().includes(searchTerm));

            const matchesParameter = !parameterFilter || section.process_parameter_id == parameterFilter;
            const matchesMachine = !machineFilter || (machine && machine.id == machineFilter);

            return matchesSearch && matchesParameter && matchesMachine;
        });

        currentPage = 1;
        renderTable();
    }

    /* =========================
       🔹 RENDER TABLE + PAGINATION
    ==========================*/
    function renderTable() {
        const tbody = $('#sectionTableBody');

        if (filteredData.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="text-muted">
                            <i class="ri-database-2-line fs-24 mb-2 d-block"></i>
                            Belum ada data section
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
        paginatedData.forEach((section, index) => {
            const globalIndex = startIndex + index + 1;
            const parameterName = section.process_parameter ? section.process_parameter.name : '-';
            const machineName = section.process_parameter?.machine ? section.process_parameter.machine.name : '-';

            html += `
                <tr>
                    <td>${globalIndex}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs me-3">
                                <span class="avatar-title rounded-circle bg-soft-primary text-primary fs-16">
                                    <i class="bx bx-grid-alt"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">${section.name}</h6>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-soft-info text-info">${parameterName}</span></td>
                    <td>${machineName}</td>
                    <td>${formatDate(section.created_at)}</td>
                    <td>
                        <div class="hstack gap-2">
                            <button type="button" class="btn btn-soft-primary btn-sm btn-action" 
                                onclick="viewSection(${section.id})" data-bs-toggle="modal" data-bs-target="#viewSectionModal"
                                title="Lihat Detail">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button type="button" class="btn btn-soft-success btn-sm btn-action" 
                                onclick="editSection(${section.id})" data-bs-toggle="modal" data-bs-target="#editSectionModal"
                                title="Edit">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button type="button" class="btn btn-soft-danger btn-sm btn-action" 
                                onclick="deleteSection(${section.id})" title="Hapus">
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

        let html = '<ul class="pagination pagination-separated justify-content-center justify-content-sm-end mb-sm-0">';
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a href="#" class="page-link" onclick="changePage(${currentPage - 1}); return false;">Previous</a>
                 </li>`;

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a href="#" class="page-link" onclick="changePage(${i}); return false;">${i}</a>
                         </li>`;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a href="#" class="page-link" onclick="changePage(${currentPage + 1}); return false;">Next</a>
                 </li></ul>`;

        $('#paginationLinks').html(html);
        $('#paginationContainer').show();
    }

    function changePage(page) {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        if (page >= 1 && page <= totalPages && page !== currentPage) {
            currentPage = page;
            renderTable();
        }
    }

    /* =========================
       🔹 UTILITIES
    ==========================*/
    function updateStatistics() {
        const total = sectionsData.length;
        const uniqueParameters = [...new Set(sectionsData.map(s => s.process_parameter_id))].length;
        const latest = total > 0 ? sectionsData[sectionsData.length - 1].name : '-';
        $('#totalSections').text(total);
        $('#totalParameters').text(uniqueParameters);
        $('#latestSection').text(latest);
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        return typeof moment !== 'undefined' ?
            moment(dateString).format('DD/MM/YYYY HH:mm') :
            new Date(dateString).toLocaleString('id-ID');
    }

    function showError() {
        $('#sectionTableBody').html(`
            <tr>
                <td colspan="6" class="text-center py-4 text-danger">
                    <i class="ri-error-warning-line fs-24 mb-2 d-block"></i>
                    Gagal memuat data section
                    <br>
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadSections()">
                        <i class="ri-refresh-line me-1"></i>Coba Lagi
                    </button>
                </td>
            </tr>
        `);
    }

    /* =========================
       🔹 VIEW / EDIT / DELETE
    ==========================*/
    function viewSection(id) {
        $('#sectionDetailContent').html(`
            <div class="text-center py-4">
                <div class="spinner-border" role="status"></div>
                <p class="mt-2">Memuat detail section...</p>
            </div>
        `);

        $.get(`/scoring-mesin/sections/${id}`, function(response) {
            const paramName = response.process_parameter?.name || '-';
            const machineName = response.process_parameter?.machine?.name || '-';

            $('#sectionDetailContent').html(`
                <div class="mb-3"><label class="fw-semibold">Nama Section:</label><p>${response.name}</p></div>
                <div class="mb-3"><label class="fw-semibold">Parameter Proses:</label><p>${paramName}</p></div>
                <div class="mb-3"><label class="fw-semibold">Mesin:</label><p>${machineName}</p></div>
                <div class="mb-3"><label class="fw-semibold">Tanggal Dibuat:</label><p>${formatDate(response.created_at)}</p></div>
                <div class="mb-3"><label class="fw-semibold">Terakhir Update:</label><p>${formatDate(response.updated_at)}</p></div>
            `);
        });
    }

    function editSection(id) {
        const section = sectionsData.find(s => s.id === id);
        if (section) {
            $('#editSectionId').val(section.id);
            $('#editSectionName').val(section.name);
            $('#editSectionParameter').val(section.process_parameter_id);
        }
    }

    function deleteSection(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data section akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                $.ajax({
                    url: `/scoring-mesin/sections/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Section berhasil dihapus',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        loadSections();
                    }
                });
            }
        });
    }
</script>

@endsection