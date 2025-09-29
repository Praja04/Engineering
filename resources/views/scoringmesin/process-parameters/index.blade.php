@extends('layouts.app')

@section('title', 'Master Data Parameter Proses')

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
                    <h4 class="mb-sm-0">Master Data Parameter Proses</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Scoring Mesin</a></li>
                            <li class="breadcrumb-item active">Parameter Proses</li>
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
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Total Parameter</p>
                                <h4 class="mb-0" id="totalParameters">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-primary rounded fs-3">
                                        <i class="bx bx-slider text-primary"></i>
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
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Total Mesin</p>
                                <h4 class="mb-0 text-info" id="totalMachines">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-info rounded fs-3">
                                        <i class="bx bx-cog text-info"></i>
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
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Parameter Terbaru</p>
                                <h6 class="mb-0" id="latestParameter">-</h6>
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
                                <i class="ri-settings-3-line align-middle me-1"></i>
                                Daftar Parameter Proses
                            </h5>
                            <div class="flex-shrink-0">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addParameterModal">
                                    <i class="ri-add-line align-bottom me-1"></i> Tambah Parameter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter dan Search -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="search-box">
                                    <input type="text" id="searchParameter" class="form-control search" placeholder="Cari parameter...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select" id="machineFilter">
                                    <option value="">Semua Mesin</option>
                                    <!-- Options will be loaded dynamically -->
                                </select>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Nama Parameter</th>
                                        <th scope="col">Mesin</th>
                                        <th scope="col">Kode Mesin</th>
                                        <th scope="col">Tanggal Dibuat</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="parameterTableBody">
                                    <tr id="loadingRow">
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <div class="spinner-border text-primary me-2" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <span>Memuat data parameter...</span>
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

<!-- Modal Tambah Parameter -->
<div class="modal fade" id="addParameterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-add-line me-2"></i>Tambah Parameter Proses
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addParameterForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="parameterMachine" class="form-label">Mesin <span class="text-danger">*</span></label>
                        <select class="form-select" id="parameterMachine" name="machine_id" required>
                            <option value="">Pilih Mesin</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="parameterName" class="form-label">Nama Parameter <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="parameterName" name="name" placeholder="Contoh: Suhu" required>
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

<!-- Modal Edit Parameter -->
<div class="modal fade" id="editParameterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-pencil-line me-2"></i>Edit Parameter Proses
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editParameterForm">
                <input type="hidden" id="editParameterId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editParameterMachine" class="form-label">Mesin <span class="text-danger">*</span></label>
                        <select class="form-select" id="editParameterMachine" name="machine_id" required>
                            <option value="">Pilih Mesin</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editParameterName" class="form-label">Nama Parameter <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editParameterName" name="name" required>
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

<!-- Modal View Parameter -->
<div class="modal fade" id="viewParameterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-eye-line me-2"></i>Detail Parameter
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="parameterDetailContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    let parametersData = [];
    let machinesData = [];
    let filteredData = [];
    let currentPage = 1;
    let itemsPerPage = 10;

    $(document).ready(function() {
        loadMachines();
        loadParameters();

        $('#searchParameter').on('keyup', filterAndRenderTable);
        $('#machineFilter').on('change', filterAndRenderTable);

        setupFormHandlers();
    });

    function loadMachines() {
        $.ajax({
            url: "/scoring-mesin/machines",
            type: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            success: function(response) {
                machinesData = response;
                populateMachineSelects();
            }
        });
    }

    function populateMachineSelects() {
        let options = '<option value="">Pilih Mesin</option>';
        let filterOptions = '<option value="">Semua Mesin</option>';

        machinesData.forEach(machine => {
            options += `<option value="${machine.id}">${machine.name} (${machine.code})</option>`;
            filterOptions += `<option value="${machine.id}">${machine.name} (${machine.code})</option>`;
        });

        $('#parameterMachine, #editParameterMachine').html(options);
        $('#machineFilter').html(filterOptions);
    }

    function loadParameters() {
        $.ajax({
            url: "/scoring-mesin/process-parameters",
            type: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            success: function(response) {
                parametersData = response;
                filteredData = [...parametersData];
                renderTable();
                updateStatistics();
            },
            error: function(xhr) {
                showError();
            }
        });
    }

    function filterAndRenderTable() {
        const searchTerm = $('#searchParameter').val().toLowerCase();
        const machineFilter = $('#machineFilter').val();

        filteredData = parametersData.filter(param => {
            const matchesSearch = param.name.toLowerCase().includes(searchTerm) ||
                (param.machine && param.machine.name.toLowerCase().includes(searchTerm));
            const matchesMachine = !machineFilter || param.machine_id == machineFilter;

            return matchesSearch && matchesMachine;
        });

        currentPage = 1;
        renderTable();
    }

    function renderTable() {
        const tbody = $('#parameterTableBody');

        if (filteredData.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="text-muted">
                            <i class="ri-database-2-line fs-24 mb-2 d-block"></i>
                            Belum ada data parameter proses
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
        paginatedData.forEach((param, index) => {
            const globalIndex = startIndex + index + 1;
            html += `
                <tr>
                    <td>${globalIndex}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs me-3">
                                <span class="avatar-title rounded-circle bg-soft-primary text-primary fs-16">
                                    <i class="bx bx-slider"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">${param.name}</h6>
                            </div>
                        </div>
                    </td>
                    <td>${param.machine ? param.machine.name : '-'}</td>
                    <td>
                        <span class="badge bg-soft-info text-info">${param.machine ? param.machine.code : '-'}</span>
                    </td>
                    <td>${formatDate(param.created_at)}</td>
                    <td>
                        <div class="hstack gap-2">
                            <button type="button" class="btn btn-soft-primary btn-sm btn-action" 
                                    onclick="viewParameter(${param.id})" 
                                    data-bs-toggle="modal" data-bs-target="#viewParameterModal"
                                    title="Lihat Detail">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button type="button" class="btn btn-soft-success btn-sm btn-action" 
                                    onclick="editParameter(${param.id})" 
                                    data-bs-toggle="modal" data-bs-target="#editParameterModal"
                                    title="Edit">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button type="button" class="btn btn-soft-danger btn-sm btn-action" 
                                    onclick="deleteParameter(${param.id})" 
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
        const total = parametersData.length;
        const uniqueMachines = [...new Set(parametersData.map(p => p.machine_id))].length;
        const latest = parametersData.length > 0 ? parametersData[parametersData.length - 1].name : '-';

        $('#totalParameters').text(total);
        $('#totalMachines').text(uniqueMachines);
        $('#latestParameter').text(latest);
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
        $('#parameterTableBody').html(`
            <tr>
                <td colspan="6" class="text-center py-4 text-danger">
                    <i class="ri-error-warning-line fs-24 mb-2 d-block"></i>
                    Gagal memuat data parameter
                    <br>
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadParameters()">
                        <i class="ri-refresh-line me-1"></i>Coba Lagi
                    </button>
                </td>
            </tr>
        `);
    }

    function setupFormHandlers() {
        $('#addParameterForm').on('submit', function(e) {
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
                url: "/scoring-mesin/process-parameters",
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
                            text: 'Parameter berhasil ditambahkan',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                    $('#addParameterModal').modal('hide');
                    $('#addParameterForm')[0].reset();
                    loadParameters();
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

        $('#editParameterForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            const id = $('#editParameterId').val();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            }

            $.ajax({
                url: `/scoring-mesin/process-parameters/${id}`,
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
                            text: 'Parameter berhasil diupdate',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                    $('#editParameterModal').modal('hide');
                    loadParameters();
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

    function viewParameter(id) {
        $('#parameterDetailContent').html(`
            <div class="text-center py-4">
                <div class="spinner-border" role="status"></div>
                <p class="mt-2">Memuat detail parameter...</p>
            </div>
        `);

        $.ajax({
            url: `/scoring-mesin/process-parameters/${id}`,
            type: 'GET',
            success: function(response) {
                $('#parameterDetailContent').html(`
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Parameter:</label>
                        <p class="mb-0">${response.name}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mesin:</label>
                        <p class="mb-0">${response.machine ? response.machine.name : '-'}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Mesin:</label>
                        <p class="mb-0">${response.machine ? response.machine.code : '-'}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Dibuat:</label>
                        <p class="mb-0">${formatDate(response.created_at)}</p>
                    </div>
                `);
            }
        });
    }

    function editParameter(id) {
        const param = parametersData.find(p => p.id === id);
        if (param) {
            $('#editParameterId').val(param.id);
            $('#editParameterName').val(param.name);
            $('#editParameterMachine').val(param.machine_id);
        }
    }

    function deleteParameter(id) {
        const confirmDelete = () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            }

            $.ajax({
                url: `/scoring-mesin/process-parameters/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Parameter berhasil dihapus',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                    loadParameters();
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data parameter akan dihapus permanen!",
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
            if (confirm('Apakah Anda yakin ingin menghapus parameter ini?')) confirmDelete();
        }
    }
</script>
@endsection