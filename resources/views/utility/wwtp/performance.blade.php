@extends('layouts.app')

@section('title', 'WWTP Performance')

@section('content')
{{-- Note: API endpoint is /api/wwtp-performance --}}
<div class="page-content">
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1">WWTP Performance</h4>
                        <p class="text-muted mb-0">Wastewater Treatment Plant Performance Monitoring</p>
                    </div>
                    <div class="page-title-right">
                        <button type="button" class="btn btn-primary btn-label" data-bs-toggle="modal" data-bs-target="#modalForm">
                            <i class="mdi mdi-plus label-icon"></i> Tambah Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-2">Total Weeks</p>
                                <h4 class="mb-0" id="totalWeeks">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-soft-primary text-primary rounded fs-3">
                                        <i class="mdi mdi-calendar-range"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-2">Total Records</p>
                                <h4 class="mb-0" id="totalRecords">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-soft-info text-info rounded fs-3">
                                        <i class="mdi mdi-file-chart"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-2">Current Week</p>
                                <h4 class="mb-0" id="currentWeekRecords">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-soft-success text-success rounded fs-3">
                                        <i class="mdi mdi-calendar-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-2">Avg TSS</p>
                                <h4 class="mb-0" id="avgTSS">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-soft-warning text-warning rounded fs-3">
                                        <i class="mdi mdi-water-percent"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Filter Jenis</label>
                                <select id="filterJenis" class="form-select">
                                    <option value="">Semua Jenis</option>
                                    <option value="equal">Equal</option>
                                    <option value="outlet_anaerob">Outlet Anaerob</option>
                                    <option value="aerob">Aerob</option>
                                    <option value="daf">DAF</option>
                                    <option value="outlet">Outlet</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Filter Bulan</label>
                                <input type="month" id="filterMonth" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Cari Data</label>
                                <input type="text" id="searchInput" class="form-control" placeholder="Cari...">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" id="btnReset" class="btn btn-soft-secondary w-100">
                                    <i class="mdi mdi-refresh me-1"></i> Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Data Performance WWTP</h5>
                            <div class="flex-shrink-0">
                                <button type="button" class="btn btn-soft-info btn-sm" id="btnExport">
                                    <i class="mdi mdi-file-export me-1"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="performanceTable" class="table table-hover table-striped align-middle table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">No</th>
                                        <th>Week Period</th>
                                        <th class="text-center" style="width: 150px;">Jenis</th>
                                        <th class="text-center">TSS (mg/L)</th>
                                        <th class="text-center">COD (mg/L)</th>
                                        <th class="text-center" style="width: 100px;">Foto</th>
                                        <th class="text-center" style="width: 150px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalFormLabel">
                    <i class="mdi mdi-file-document-edit me-2"></i>Tambah Data Performance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="performanceForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="recordId" name="recordId">

                    <div class="alert alert-info border-0 mb-4" role="alert">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-information fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <strong>Catatan:</strong> Setiap <strong>jenis</strong> hanya dapat diinput <strong>1x per minggu</strong>.
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label fw-semibold">
                                Tanggal <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            <div class="form-text">Minggu akan ditentukan otomatis</div>
                        </div>
                        <div class="col-md-6">
                            <label for="jenis" class="form-label fw-semibold">
                                Jenis <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="jenis" name="jenis" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="equal">Equal</option>
                                <option value="outlet_anaerob">Outlet Anaerob</option>
                                <option value="aerob">Aerob</option>
                                <option value="daf">DAF</option>
                                <option value="outlet">Outlet</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tss" class="form-label fw-semibold">
                                <i class="mdi mdi-water-percent text-primary"></i> TSS <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="tss" name="tss" min="0" placeholder="0.00" required>
                                <span class="input-group-text">mg/L</span>
                            </div>
                            <div class="form-text">Total Suspended Solids</div>
                        </div>
                        <div class="col-md-6">
                            <label for="cod" class="form-label fw-semibold">
                                <i class="mdi mdi-flask text-info"></i> COD <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="cod" name="cod" min="0" placeholder="0.00" required>
                                <span class="input-group-text">mg/L</span>
                            </div>
                            <div class="form-text">Chemical Oxygen Demand</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="foto" class="form-label fw-semibold">
                                <i class="mdi mdi-camera text-success"></i> Foto
                            </label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            <div class="form-text">Format: JPG, PNG, JPEG. Max 2MB</div>
                            <div id="previewContainer" class="mt-2" style="display: none;">
                                <img id="imagePreview" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                                <button type="button" class="btn btn-sm btn-danger mt-2" id="removeImage">
                                    <i class="mdi mdi-delete"></i> Hapus Foto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="mdi mdi-content-save me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalDetailLabel">
                    <i class="mdi mdi-eye me-2"></i>Detail Performance WWTP
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Image -->
<div class="modal fade" id="modalImage" tabindex="-1" aria-labelledby="modalImageLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalImageLabel">
                    <i class="mdi mdi-image me-2"></i>Foto Performance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="fullImage" src="" alt="Performance Photo" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<link href="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    .card-animate {
        transition: all 0.3s ease;
    }

    .card-animate:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
    }

    .bg-soft-primary {
        background-color: rgba(64, 81, 137, 0.1);
    }

    .bg-soft-info {
        background-color: rgba(41, 156, 219, 0.1);
    }

    .bg-soft-success {
        background-color: rgba(10, 179, 156, 0.1);
    }

    .bg-soft-warning {
        background-color: rgba(243, 156, 18, 0.1);
    }

    .bg-soft-secondary {
        background-color: rgba(108, 117, 125, 0.1);
    }

    .table> :not(caption)>*>* {
        padding: 0.75rem 0.75rem;
    }

    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.75rem;
    }

    .btn-label {
        position: relative;
        padding-left: 44px;
    }

    .btn-label .label-icon {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 0, 0, 0.1);
        border-radius: 0.25rem 0 0 0.25rem;
    }

    .jenis-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        font-weight: 600;
        text-transform: uppercase;
    }
</style>

<script src="{{ asset('material/assets/libs/datatables.net/datatables.min.js') }}"></script>
<script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
    $(document).ready(function() {
        let table;
        let isEdit = false;
        let allData = [];
        let currentImagePath = null;

        // Initialize DataTable
        function initDataTable() {
            table = $('#performanceTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '/api/wwtp-performance',
                    dataSrc: function(json) {
                        allData = json.data;
                        updateStatistics(json.data);
                        return processDataForTable(json.data);
                    }
                },
                columns: [{
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return `<span class="badge badge-soft-secondary">${meta.row + 1}</span>`;
                        }
                    },
                    {
                        data: 'week_start',
                        render: function(data, type, row) {
                            const start = new Date(row.week_start);
                            const end = new Date(row.week_end);
                            return `
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">${formatDate(start)} - ${formatDate(end)}</span>
                                    <small class="text-muted">Minggu ${getWeekNumber(start)}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'jenis',
                        className: 'text-center',
                        render: function(data) {
                            const badges = {
                                'equal': '<span class="badge bg-primary jenis-badge">Equal</span>',
                                'outlet_anaerob': '<span class="badge bg-info jenis-badge">Outlet Anaerob</span>',
                                'aerob': '<span class="badge bg-success jenis-badge">Aerob</span>',
                                'daf': '<span class="badge bg-warning jenis-badge">DAF</span>',
                                'outlet': '<span class="badge bg-danger jenis-badge">Outlet</span>'
                            };
                            return badges[data] || data;
                        }
                    },
                    {
                        data: 'tss',
                        className: 'text-center',
                        render: function(data) {
                            return `<span class="fw-semibold text-primary">${parseFloat(data).toFixed(2)}</span>`;
                        }
                    },
                    {
                        data: 'cod',
                        className: 'text-center',
                        render: function(data) {
                            return `<span class="fw-semibold text-info">${parseFloat(data).toFixed(2)}</span>`;
                        }
                    },
                    {
                        data: 'foto',
                        className: 'text-center',
                        render: function(data) {
                            if (data) {
                                return `
                                    <button type="button" class="btn btn-sm btn-soft-success btn-view-image" data-foto="${data}">
                                        <i class="mdi mdi-image"></i> Lihat
                                    </button>
                                `;
                            }
                            return '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data) {
                            return `
                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button" class="btn btn-sm btn-soft-info btn-detail" data-id="${data.id}" title="Detail">
                                        <i class="mdi mdi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-soft-warning btn-edit" data-id="${data.id}" title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-soft-danger btn-delete" data-id="${data.id}" title="Hapus">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
                    search: "_INPUT_",
                    searchPlaceholder: "Cari data..."
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                responsive: true
            });
        }

        // Process nested data for DataTable
        function processDataForTable(data) {
            const flatData = [];
            data.forEach(week => {
                if (week.records && week.records.length > 0) {
                    week.records.forEach(record => {
                        flatData.push({
                            ...record,
                            week_start: week.week_start,
                            week_end: week.week_end
                        });
                    });
                }
            });
            return flatData;
        }

        // Helper functions
        function formatDate(date) {
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function getWeekNumber(date) {
            const firstDayOfYear = new Date(date.getFullYear(), 0, 1);
            const pastDaysOfYear = (date - firstDayOfYear) / 86400000;
            return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
        }

        // Update statistics
        function updateStatistics(data) {
            const totalWeeks = data.length;
            let totalRecords = 0;
            let totalTSS = 0;
            let countTSS = 0;

            // Get current week
            const now = new Date();
            const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay() + 1));
            const endOfWeek = new Date(now.setDate(now.getDate() - now.getDay() + 7));
            let currentWeekRecords = 0;

            data.forEach(week => {
                if (week.records && week.records.length > 0) {
                    totalRecords += week.records.length;

                    week.records.forEach(record => {
                        totalTSS += parseFloat(record.tss);
                        countTSS++;
                    });

                    // Check if week is current week
                    const weekStart = new Date(week.week_start);
                    const weekEnd = new Date(week.week_end);
                    if (weekStart <= endOfWeek && weekEnd >= startOfWeek) {
                        currentWeekRecords = week.records.length;
                    }
                }
            });

            const avgTSS = countTSS > 0 ? (totalTSS / countTSS).toFixed(2) : 0;

            $('#totalWeeks').text(totalWeeks);
            $('#totalRecords').text(totalRecords);
            $('#currentWeekRecords').text(currentWeekRecords);
            $('#avgTSS').text(avgTSS);
        }

        initDataTable();

        // Image preview
        $('#foto').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size
                if (file.size > 2048000) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal 2MB'
                    });
                    $(this).val('');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result);
                    $('#previewContainer').show();
                }
                reader.readAsDataURL(file);
            }
        });

        // Remove image preview
        $('#removeImage').on('click', function() {
            $('#foto').val('');
            $('#imagePreview').attr('src', '');
            $('#previewContainer').hide();
        });

        // View full image
        $(document).on('click', '.btn-view-image', function() {
            const foto = $(this).data('foto');
            $('#fullImage').attr('src', `/storage/${foto}`);
            $('#modalImage').modal('show');
        });

        // Filter by jenis
        $('#filterJenis').on('change', function() {
            const value = $(this).val();
            table.column(2).search(value ? value.toUpperCase() : '').draw();
        });

        // Filter by month
        $('#filterMonth').on('change', function() {
            const value = $(this).val();

            $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn) {
                return fn.name !== 'monthFilter';
            });

            if (value) {
                const monthFilter = function(settings, data, dataIndex) {
                    const rowData = table.row(dataIndex).data();
                    const date = new Date(rowData.week_start);
                    const [selectedYear, selectedMonth] = value.split('-');

                    return date.getMonth() === (parseInt(selectedMonth) - 1) &&
                        date.getFullYear() === parseInt(selectedYear);
                };
                monthFilter.name = 'monthFilter';
                $.fn.dataTable.ext.search.push(monthFilter);
            }

            table.draw();
        });

        // Custom search
        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Reset filters
        $('#btnReset').on('click', function() {
            $('#filterJenis').val('');
            $('#filterMonth').val('');
            $('#searchInput').val('');
            $.fn.dataTable.ext.search = [];
            table.search('').columns().search('').draw();
        });

        // Reset modal when closed
        $('#modalForm').on('hidden.bs.modal', function() {
            resetForm();
        });

        // Submit form
        $('#performanceForm').on('submit', function(e) {
            e.preventDefault();

            const btnSubmit = $('#btnSubmit');
            const originalText = btnSubmit.html();
            btnSubmit.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

            const formData = new FormData(this);

            // Remove recordId from formData if not editing
            if (!isEdit) {
                formData.delete('recordId');
            }

            const url = isEdit ? `/api/wwtp-performance/${$('#recordId').val()}` : '/api/wwtp-performance';

            // For PUT request, we need to use POST with _method
            if (isEdit) {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#modalForm').modal('hide');
                    table.ajax.reload(null, false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    const error = xhr.responseJSON;
                    let message = 'Terjadi kesalahan!';

                    if (error && error.message) {
                        message = error.message;
                    } else if (error && error.errors) {
                        message = Object.values(error.errors).flat().join('<br>');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: message,
                        confirmButtonColor: '#3085d6'
                    });
                },
                complete: function() {
                    btnSubmit.prop('disabled', false).html(originalText);
                }
            });
        });

        // Detail button
        $(document).on('click', '.btn-detail', function() {
            const id = $(this).data('id');

            $.ajax({
                url: `/api/wwtp-performance/${id}`,
                method: 'GET',
                beforeSend: function() {
                    $('#detailContent').html(`
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    `);
                    $('#modalDetail').modal('show');
                },
                success: function(response) {
                    const data = response.data;
                    const week = data.week;

                    let content = `
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-soft-primary text-primary rounded">
                                                        <i class="mdi mdi-calendar-range fs-4"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <p class="text-muted mb-1">Week Period</p>
                                                <h6 class="mb-0">${formatDate(new Date(week.week_start))} - ${formatDate(new Date(week.week_end))}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-soft-info text-info rounded">
                                                        <i class="mdi mdi-tag fs-4"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <p class="text-muted mb-1">Jenis</p>
                                                <h6 class="mb-0">
                                                    ${getJenisBadge(data.jenis)}
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="mb-3 text-primary">
                                    <i class="mdi mdi-chart-line me-2"></i>Parameter Performance
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted" width="200">
                                                    <i class="mdi mdi-water-percent text-primary me-2"></i>TSS
                                                </td>
                                                <td class="text-end fw-semibold">${parseFloat(data.tss).toFixed(2)} mg/L</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">
                                                    <i class="mdi mdi-flask text-info me-2"></i>COD
                                                </td>
                                                <td class="text-end fw-semibold">${parseFloat(data.cod).toFixed(2)} mg/L</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                    `;

                    if (data.foto) {
                        content += `
                                <hr class="my-4">
                                <h6 class="mb-3 text-success">
                                    <i class="mdi mdi-camera me-2"></i>Dokumentasi
                                </h6>
                                <div class="text-center">
                                    <img src="/storage/${data.foto}" alt="Performance Photo" class="img-fluid rounded" style="max-height: 300px; cursor: pointer;" onclick="$('#fullImage').attr('src', '/storage/${data.foto}'); $('#modalImage').modal('show');">
                                </div>
                        `;
                    }

                    content += `
                            </div>
                        </div>
                    `;

                    $('#detailContent').html(content);
                },
                error: function() {
                    $('#detailContent').html(`
                        <div class="alert alert-danger" role="alert">
                            <i class="mdi mdi-alert-circle me-2"></i>
                            Gagal memuat data. Silakan coba lagi.
                        </div>
                    `);
                }
            });
        });

        // Edit button
        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            isEdit = true;

            $.ajax({
                url: `/api/wwtp-performance/${id}`,
                method: 'GET',
                beforeSend: function() {
                    Swal.fire({
                        title: 'Memuat data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    const data = response.data;

                    $('#recordId').val(data.id);

                    // Set tanggal from week_start
                    $('#tanggal').val(data.week.week_start);
                    $('#jenis').val(data.jenis);
                    $('#tss').val(data.tss);
                    $('#cod').val(data.cod);

                    currentImagePath = data.foto;

                    // Show existing image if available
                    if (data.foto) {
                        $('#imagePreview').attr('src', `/storage/${data.foto}`);
                        $('#previewContainer').show();
                    }

                    $('#modalFormLabel').html('<i class="mdi mdi-pencil me-2"></i>Edit Data Performance');
                    $('#jenis').prop('disabled', true);
                    $('#modalForm').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal memuat data untuk diedit'
                    });
                }
            });
        });

        // Delete button
        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: '<p class="mb-0">Apakah Anda yakin ingin menghapus data ini?</p><small class="text-muted">Data yang dihapus tidak dapat dikembalikan</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="mdi mdi-delete me-1"></i> Ya, Hapus!',
                cancelButtonText: '<i class="mdi mdi-close me-1"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/wwtp-performance/${id}`,
                        method: 'DELETE',
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Menghapus...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            table.ajax.reload(null, false);

                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Gagal menghapus data'
                            });
                        }
                    });
                }
            });
        });

        // Export button
        $('#btnExport').on('click', function() {
            Swal.fire({
                title: 'Export Data',
                text: 'Fitur export akan segera tersedia',
                icon: 'info'
            });
        });

        // Helper function for jenis badge
        function getJenisBadge(jenis) {
            const badges = {
                'equal': '<span class="badge bg-primary">Equal</span>',
                'outlet_anaerob': '<span class="badge bg-info">Outlet Anaerob</span>',
                'aerob': '<span class="badge bg-success">Aerob</span>',
                'daf': '<span class="badge bg-warning">DAF</span>',
                'outlet': '<span class="badge bg-danger">Outlet</span>'
            };
            return badges[jenis] || jenis;
        }

        function resetForm() {
            isEdit = false;
            $('#performanceForm')[0].reset();
            $('#recordId').val('');
            $('#jenis').prop('disabled', false);
            $('#imagePreview').attr('src', '');
            $('#previewContainer').hide();
            $('#modalFormLabel').html('<i class="mdi mdi-file-document-edit me-2"></i>Tambah Data Performance');
            currentImagePath = null;
        }
    });
</script>

@endsection