@extends('layouts.app')

@section('title', 'Data Analisa WWTP')

@section('styles')
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
            background-color: rgba(41, 156, 219, .08);
        }

        .pagination .page-link {
            border-radius: 6px !important;
            margin: 0 2px;
            color: #299cdb;
            border-color: #dee2e6;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #299cdb 0%, #2284ba 100%);
            border-color: transparent;
            color: white;
        }

        .pagination .page-link:hover {
            background-color: #f0f9ff;
            border-color: #299cdb;
            color: #2284ba;
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
        }

        .detail-table td,
        .detail-table th {
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="container-fluid">

                <!-- Header Section -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fw-bold text-info mb-2">
                                <i class="mdi mdi-flask-outline me-3"></i>Data Analisa WWTP
                            </h2>
                            <p class="text-muted fs-5">Wastewater Treatment Plant - Parameter Monitoring & Analysis</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <a href="{{ url('/wwtp/form_analisa') }}" class="btn btn-info text-white btn-lg">
                                    <i class="mdi mdi-plus-circle me-2"></i>Tambah Data Analisa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row g-4 mb-3">
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100 stat-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-info bg-opacity-10 text-info rounded-3 p-3 me-3">
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

                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100 stat-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                                        <i class="mdi mdi-check-circle-outline fs-3"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1 small">Status Pemantauan</p>
                                        <h5 class="fw-bold mb-0 text-success">Aktif</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100 stat-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                                        <i class="mdi mdi-flask-empty-outline fs-3"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-1 small">Parameter Tersedia</p>
                                        <h5 class="fw-bold mb-0">{{ $parameters->count() }} Parameter</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <!-- Filters -->
                        <div class="row g-3 mb-4 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small text-muted fw-semibold">Filter Bulan</label>
                                <input type="month" class="form-control" id="filterBulan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-semibold">Cari Tanggal</label>
                                <input type="text" class="form-control" id="searchData"
                                    placeholder="Cari berdasarkan tanggal (YYYY-MM-DD)...">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-secondary w-100" id="btnReset">
                                    <i class="mdi mdi-refresh me-1"></i>Reset
                                </button>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold">No</th>
                                        <th class="fw-semibold">Tanggal</th>
                                        <th class="fw-semibold">Shift</th>
                                        <th class="fw-semibold">Area</th>
                                        <th class="fw-semibold">Parameter Uji</th>
                                        <th class="fw-semibold">Dibuat Oleh</th>
                                        <th class="fw-semibold text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="analisaTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="spinner-border text-info" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                            <div class="text-muted small" id="analisaPaginationInfo"></div>
                            <nav>
                                <ul class="pagination pagination-sm mb-0" id="analisaPagination"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL: Detail Analisa --}}
            <div class="modal fade" id="detailAnalisaModal" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title text-info">
                                <i class="mdi mdi-flask-outline me-2"></i>Detail Data Analisa WWTP
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4" id="modalAnalisaContent">
                            <!-- Detail content loaded via AJAX -->
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-danger" id="btnDelete" style="display:none;">
                                <i class="mdi mdi-trash-can me-2"></i>Hapus Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const PER_PAGE = 10;
            const userJabatan = "{{ Auth::user()->jabatan ?? '' }}";
            const canEditDelete = userJabatan !== 'operator';

            const state = {
                currentPage: 1,
                lastPage: 1,
                total: 0,
                bulan: '',
                search: '',
            };

            let currentId = null;

            // Use the parameter definitions from PHP
            const parametersList = @json($parameters);
            const standards = @json($standards ?? []);

            // Init Load
            loadData(1);

            function loadData(page) {
                page = page || state.currentPage;

                const params = {
                    page,
                    per_page: PER_PAGE
                };
                if (state.bulan) params.bulan = state.bulan;
                if (state.search) params.search = state.search;

                showLoading('#analisaTableBody', 7);
                clearPagination('#analisaPaginationInfo', '#analisaPagination');

                $.ajax({
                    url: '/api/wwtp-analisa',
                    method: 'GET',
                    data: params,
                    success: function(response) {
                        state.currentPage = response.current_page;
                        state.lastPage = response.last_page;
                        state.total = response.total;

                        $('#totalRecords').text(response.total);
                        renderTable(response.data, response.from);
                        renderPaginationInfo('#analisaPaginationInfo', response);
                        renderPagination('#analisaPagination', response, loadData);
                    },
                    error: function() {
                        showError('Gagal memuat data analisa WWTP');
                    }
                });
            }

            function renderTable(data, from) {
                const tbody = $('#analisaTableBody');
                tbody.empty();

                if (!data || !data.length) {
                    tbody.append(`<tr><td colspan="7" class="text-center py-4 text-muted">
                    <i class="mdi mdi-inbox me-2"></i>Tidak ada data analisa</td></tr>`);
                    return;
                }

                data.forEach(function(item, idx) {
                    const no = (from || 1) + idx;
                    let btns = `<button class="btn btn-sm btn-outline-info me-1"
                                onclick="showDetail(${item.id})" title="Lihat Detail">
                                <i class="mdi mdi-eye"></i> Detail</button>`;
                    if (canEditDelete) {
                        btns += `
                            <button class="btn btn-sm btn-outline-danger"
                                onclick="confirmDelete(${item.id})" title="Hapus">
                            <i class="mdi mdi-trash-can"></i> Hapus</button>`;
                    }

                    // Get list of filled parameter names
                    let paramNames = new Set();
                    if (item.details && item.details.length > 0) {
                        item.details.forEach(d => {
                            if (d.parameter) paramNames.add(d.parameter.parameter_name);
                        });
                    }

                    let paramBadges = '';
                    if (paramNames.size > 0) {
                        paramNames.forEach(name => {
                            paramBadges +=
                                `<span class="badge bg-soft-info text-info me-1">${name}</span>`;
                        });
                    } else {
                        paramBadges = `<span class="text-muted small">-</span>`;
                    }

                    tbody.append(`
                        <tr class="data-row">
                            <td>${no}</td>
                            <td><span class="badge bg-light text-dark border"><i class="mdi mdi-calendar me-1"></i>${formatDate(item.analisa_date)}</span></td>
                            <td>${item.shift ? 'Shift ' + item.shift : '-'}</td>
                            <td>${item.area || '-'}</td>
                            <td>${paramBadges}</td>
                            <td>${item.creator ? item.creator.username : '-'}</td>
                            <td class="text-center">${btns}</td>
                        </tr>`);
                });
            }

            // Filters Search / Reset
            let searchTimer;
            $('#searchData').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    state.search = $('#searchData').val();
                    state.currentPage = 1;
                    loadData(1);
                }, 400);
            });

            $('#filterBulan').on('change', function() {
                state.bulan = $(this).val();
                state.currentPage = 1;
                loadData(1);
            });

            $('#btnReset').on('click', function() {
                $('#filterBulan').val('');
                $('#searchData').val('');
                state.bulan = '';
                state.search = '';
                state.currentPage = 1;
                loadData(1);
            });

            window.showDetail = function(id) {
                $('#modalAnalisaContent').html(`
                     <div class="text-center py-5">
                         <div class="spinner-border text-info" role="status">
                             <span class="visually-hidden">Loading...</span>
                         </div>
                     </div>
                 `);
                new bootstrap.Modal(document.getElementById('detailAnalisaModal')).show();

                $.ajax({
                    url: `/api/wwtp-analisa/${id}`,
                    method: 'GET',
                    success: function(record) {
                        currentId = id;

                        let headerHtml = `
                             <div class="row g-3 mb-4">
                                 <div class="col-md-3">
                                     <div class="info-box p-3 bg-light rounded border border-info h-100">
                                         <p class="text-muted small mb-1">Tanggal Analisa</p>
                                         <p class="fw-bold mb-0 fs-6">${formatDate(record.analisa_date)}</p>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="info-box p-3 bg-light rounded border border-info h-100">
                                         <p class="text-muted small mb-1">Shift</p>
                                         <p class="fw-bold mb-0 fs-6">${record.shift ? 'Shift ' + record.shift : '-'}</p>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="info-box p-3 bg-light rounded border border-info h-100">
                                         <p class="text-muted small mb-1">Area</p>
                                         <p class="fw-bold mb-0 fs-6">${record.area || '-'}</p>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="info-box p-3 bg-light rounded border border-info h-100">
                                         <p class="text-muted small mb-1">Dibuat Oleh</p>
                                         <p class="fw-bold mb-0 fs-6">${record.creator ? record.creator.username : '-'}</p>
                                     </div>
                                 </div>
                             </div>
                             <h6 class="fw-bold mb-3 text-info border-bottom pb-2">Matrix Pengukuran Point</h6>
                         `;

                        // Reorganize data into matrix: pointName -> { pointId, params: { paramId -> value } }
                        // Also keep track of active parameter IDs that have data in this record
                        let matrixData = {};
                        let activeParamIds = new Set();
                        if (record.details && record.details.length > 0) {
                            record.details.forEach(d => {
                                let pointName = d.point ? d.point.point_name :
                                    'Unknown Point';
                                let pointId = d.point_id;
                                let paramId = d.parameter_id;

                                if (!matrixData[pointName]) {
                                    matrixData[pointName] = {
                                        pointId: pointId,
                                        params: {}
                                    };
                                }
                                matrixData[pointName].params[paramId] = d.hasil_analisa;
                                activeParamIds.add(paramId);
                            });
                        }

                        // Only display columns for parameters that actually have data
                        const activeParamsList = parametersList.filter(p => activeParamIds.has(p
                            .id));

                        // Generate Table Headers based on active parameters only
                        let thHtml = '';
                        activeParamsList.forEach(p => {
                            thHtml +=
                                `<th class="text-center">${p.parameter_name} <br><small class="text-muted">(${p.unit})</small></th>`;
                        });

                        let trHtml = '';
                        // Loop each point that has data
                        for (const [pointName, pointInfo] of Object.entries(matrixData)) {
                            let tdHtml = '';
                            const pointId = pointInfo.pointId;

                            activeParamsList.forEach(p => {
                                const val = pointInfo.params[p.id];
                                if (val !== undefined && val !== null) {
                                    const key = pointId + '_' + p.id;
                                    const stdVal = standards[key];
                                    let stdDisplay = '';
                                    if (stdVal !== undefined && stdVal !== null) {
                                        const parsedStd = parseFloat(stdVal);
                                        const displayStd = parsedStd % 1 === 0 ? parsedStd
                                            .toFixed(0) : parsedStd.toString();
                                        stdDisplay =
                                            `<div class="text-muted mt-1 small" style="font-size: 0.72rem; font-weight: normal;">(Std: ${displayStd})</div>`;
                                    }
                                    tdHtml +=
                                        `<td class="text-center align-middle"><div class="fw-semibold text-info fs-6">${num(val)}</div>${stdDisplay}</td>`;
                                } else {
                                    tdHtml +=
                                        `<td class="text-center align-middle text-muted">-</td>`;
                                }
                            });

                            trHtml += `
                                 <tr>
                                     <td class="fw-bold bg-light align-middle">${pointName}</td>
                                     ${tdHtml}
                                 </tr>
                             `;
                        }

                        if (Object.keys(matrixData).length === 0) {
                            trHtml =
                                `<tr><td colspan="${activeParamsList.length + 1}" class="text-center py-4 text-muted">Tidak ada data hasil pengukuran</td></tr>`;
                        }

                        let pointsHtml = `
                             <div class="table-responsive mt-3">
                                 <table class="table table-bordered table-hover detail-table">
                                     <thead class="table-light">
                                        <tr>
                                            <th>Point Pengukuran</th>
                                            ${thHtml}
                                        </tr>
                                     </thead>
                                     <tbody>
                                         ${trHtml}
                                     </tbody>
                                 </table>
                             </div>
                         `;

                        $('#modalAnalisaContent').html(headerHtml + pointsHtml);
                        canEditDelete ? $('#btnDelete').show() : $('#btnDelete').hide();
                    },
                    error: function() {
                        $('#modalAnalisaContent').html(
                            '<div class="alert alert-danger">Gagal memuat detail data analisa</div>'
                        );
                    }
                });
            };

            $('#btnDelete').on('click', function() {
                if (!currentId) return;
                confirmSwal('Hapus data analisa ini?', function() {
                    $.ajax({
                        url: `/api/wwtp-analisa/${currentId}`,
                        method: 'DELETE',
                        data: {
                            _token: $('input[name="_token"]').val()
                        },
                        success: function() {
                            $('#detailAnalisaModal').modal('hide');
                            showSuccess('Data analisa berhasil dihapus');
                            loadData(state.currentPage);
                        },
                        error: function() {
                            showError('Gagal menghapus data analisa');
                        }
                    });
                });
            });

            window.confirmDelete = function(id) {
                confirmSwal('Hapus data analisa ini?', function() {
                    $.ajax({
                        url: `/api/wwtp-analisa/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: $('input[name="_token"]').val()
                        },
                        success: function() {
                            showSuccess('Data analisa berhasil dihapus');
                            loadData(state.currentPage);
                        },
                        error: function() {
                            showError('Gagal menghapus data analisa');
                        }
                    });
                });
            };

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
                <div class="spinner-border text-info" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div></td></tr>`);
            }

            function clearPagination(infoSelector, paginationSelector) {
                $(infoSelector).text('');
                $(paginationSelector).empty();
            }

            const num = (number) => {
                if (number == null || number === '') return '-';
                const parsed = parseFloat(number);
                return parsed % 1 === 0 ? parsed.toFixed(0) : parsed.toString();
            };

            function formatDate(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
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
        });
    </script>
@endsection
