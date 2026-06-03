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

        .parameter-detail-card {
            border: 1px solid #e6f2fb;
            border-radius: 8px;
            height: 100%;
        }

        .parameter-detail-card .card-header {
            border-bottom: 1px solid #e6f2fb;
        }

        .parameter-actions {
            border-top: 1px solid #eef2f7;
        }

        .parameter-result-input {
            min-width: 110px;
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
                                        <th class="fw-semibold">Parameter Uji</th>
                                        <th class="fw-semibold">Dibuat Oleh</th>
                                        <th class="fw-semibold text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="analisaTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
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
            const csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();

            const state = {
                currentPage: 1,
                lastPage: 1,
                total: 0,
                bulan: '',
                search: '',
            };

            let currentId = null;
            const detailModalEl = document.getElementById('detailAnalisaModal');
            const detailModal = bootstrap.Modal.getOrCreateInstance(detailModalEl);

            detailModalEl.addEventListener('hidden.bs.modal', function() {
                cleanupModalOverlay();
            });

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

                showLoading('#analisaTableBody', 5);
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
                    tbody.append(`<tr><td colspan="5" class="text-center py-4 text-muted">
                    <i class="mdi mdi-inbox me-2"></i>Tidak ada data analisa</td></tr>`);
                    return;
                }

                data.forEach(function(item, idx) {
                    const no = (from || 1) + idx;
                    let btns = `<button class="btn btn-sm btn-outline-info me-1"
                                onclick="showDetail(${item.id})" title="Lihat Detail">
                                <i class="mdi mdi-eye"></i> Detail</button>`;
                    btns += `
                        <button class="btn btn-sm btn-outline-warning me-1"
                            onclick="downloadPdf(${item.id})" title="Download PDF">
                        <i class="mdi mdi-file-pdf-box"></i> PDF</button>`;

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
                            <td>${paramBadges}</td>
                            <td>${item.creator ? item.creator.username : '-'}</td>
                            <td class="text-center text-nowrap">${btns}</td>
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

            window.showDetail = function(id, shouldShowModal = true) {
                $('#modalAnalisaContent').html(`
                     <div class="text-center py-5">
                         <div class="spinner-border text-info" role="status">
                             <span class="visually-hidden">Loading...</span>
                         </div>
                     </div>
                 `);
                if (shouldShowModal) {
                    detailModal.show();
                }

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
                                         <p class="text-muted small mb-1">Dibuat Oleh</p>
                                         <p class="fw-bold mb-0 fs-6">${record.creator ? record.creator.username : '-'}</p>
                                     </div>
                                 </div>
                             </div>
                             <h6 class="fw-bold mb-3 text-info border-bottom pb-2">Detail Parameter Pengukuran</h6>
                         `;

                        // Reorganize data into parameter cards.
                        let parameterData = {};
                        if (record.details && record.details.length > 0) {
                            record.details.forEach(d => {
                                const paramId = d.parameter_id;
                                const param = d.parameter || parametersList.find(p => p
                                    .id ===
                                    paramId) || {};

                                if (!parameterData[paramId]) {
                                    parameterData[paramId] = {
                                        id: paramId,
                                        name: param.parameter_name ||
                                            'Unknown Parameter',
                                        unit: param.unit || '',
                                        points: []
                                    };
                                }

                                parameterData[paramId].points.push({
                                    pointId: d.point_id,
                                    pointName: d.point ? d.point.point_name :
                                        'Unknown Point',
                                    value: d.hasil_analisa
                                });
                            });
                        }

                        const parameterOrder = parametersList.map(p => p.id);
                        const activeParamsList = Object.values(parameterData).sort((a, b) => {
                            return parameterOrder.indexOf(a.id) - parameterOrder.indexOf(b
                                .id);
                        });

                        let pointsHtml = '<div class="row g-3 mt-1">';
                        activeParamsList.forEach(param => {
                            let rowsHtml = '';
                            param.points.forEach(point => {
                                const unit = param.unit ?
                                    ` ${escapeHtml(param.unit)}` : '';
                                const key = point.pointId + '_' + param.id;
                                rowsHtml += `
                                    <tr>
                                        <td class="fw-semibold">${escapeHtml(point.pointName)}</td>
                                        <td class="text-center">${standardDisplay(standards[key], param.unit)}</td>
                                        <td class="text-center">
                                            <span class="fw-semibold text-info result-display">${num(point.value)}${unit}</span>
                                            <div class="input-group input-group-sm result-input d-none">
                                                <input type="number" step="0.01" class="form-control parameter-result-input"
                                                    data-point-id="${point.pointId}" value="${escapeHtml(point.value)}">
                                                ${param.unit ? `<span class="input-group-text">${escapeHtml(param.unit)}</span>` : ''}
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });

                            const actionHtml = canEditDelete ? `
                                <div class="parameter-actions p-3 d-flex justify-content-end gap-2">
                                    <div class="view-actions">
                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                            onclick="editParameter(${record.id}, ${param.id})">
                                            <i class="mdi mdi-pencil me-1"></i>Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDeleteParameter(${record.id}, ${param.id})">
                                            <i class="mdi mdi-trash-can me-1"></i>Hapus
                                        </button>
                                    </div>
                                    <div class="edit-actions d-none">
                                        <button type="button" class="btn btn-sm btn-info text-white"
                                            onclick="saveParameter(${record.id}, ${param.id})">
                                            <i class="mdi mdi-content-save me-1"></i>Simpan
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="cancelEditParameter(${param.id})">Batal</button>
                                    </div>
                                </div>
                            ` : '';

                            pointsHtml += `
                                <div class="col-lg-6">
                                    <div class="parameter-detail-card" id="parameter-card-${param.id}">
                                        <div class="card-header bg-light p-3">
                                            <div class="d-flex justify-content-between align-items-start gap-2">
                                                <div>
                                                    <h6 class="fw-bold text-info mb-1">${escapeHtml(param.name)}</h6>
                                                    <small class="text-muted">Point pengukuran dan hasil analisa</small>
                                                </div>
                                                <span class="badge bg-soft-info text-info">${escapeHtml(param.unit || '-')}</span>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover detail-table mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Point Pengukuran</th>
                                                        <th class="text-center">Standar Value</th>
                                                        <th class="text-center">Hasil Analisa</th>
                                                    </tr>
                                                </thead>
                                                <tbody>${rowsHtml}</tbody>
                                            </table>
                                        </div>
                                        ${actionHtml}
                                    </div>
                                </div>
                            `;
                        });

                        if (!activeParamsList.length) {
                            pointsHtml +=
                                '<div class="col-12 text-center py-4 text-muted">Tidak ada data hasil pengukuran</div>';
                        }
                        pointsHtml += '</div>';

                        $('#modalAnalisaContent').html(headerHtml + pointsHtml);
                        $('#btnDelete').hide();
                    },
                    error: function() {
                        $('#modalAnalisaContent').html(
                            '<div class="alert alert-danger">Gagal memuat detail data analisa</div>'
                        );
                    }
                });
            };

            window.editParameter = function(analisaId, parameterId) {
                const card = $(`#parameter-card-${parameterId}`);
                card.find('.result-display').addClass('d-none');
                card.find('.result-input').removeClass('d-none');
                card.find('.view-actions').addClass('d-none');
                card.find('.edit-actions').removeClass('d-none');
            };

            window.cancelEditParameter = function(parameterId) {
                const card = $(`#parameter-card-${parameterId}`);
                card.find('.result-display').removeClass('d-none');
                card.find('.result-input').addClass('d-none');
                card.find('.view-actions').removeClass('d-none');
                card.find('.edit-actions').addClass('d-none');
            };

            window.saveParameter = function(analisaId, parameterId) {
                const card = $(`#parameter-card-${parameterId}`);
                const data = {
                    _token: csrfToken
                };

                card.find('.parameter-result-input').each(function() {
                    const pointId = $(this).data('point-id');
                    data[`hasil_analisa[${pointId}]`] = $(this).val();
                });

                $.ajax({
                    url: `/api/wwtp-analisa/${analisaId}/parameters/${parameterId}`,
                    method: 'POST',
                    data,
                    success: function() {
                        showSuccess('Data parameter berhasil diperbarui');
                        showDetail(analisaId, false);
                        loadData(state.currentPage);
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON;
                        showError(error && error.message ? error.message :
                            'Gagal memperbarui data parameter');
                    }
                });
            };

            window.confirmDeleteParameter = function(analisaId, parameterId) {
                const parameterName = $(`#parameter-card-${parameterId} h6`).text() || 'parameter ini';
                confirmSwal(`Hapus data ${parameterName}?`, function() {
                    $.ajax({
                        url: `/api/wwtp-analisa/${analisaId}/parameters/${parameterId}`,
                        method: 'DELETE',
                        data: {
                            _token: csrfToken
                        },
                        success: function() {
                            showSuccess('Data parameter berhasil dihapus');
                            if ($('.parameter-detail-card').length <= 1) {
                                detailModal.hide();
                            } else {
                                showDetail(analisaId, false);
                            }
                            loadData(state.currentPage);
                        },
                        error: function() {
                            showError('Gagal menghapus data parameter');
                        }
                    });
                });
            };

            $('#btnDelete').on('click', function() {
                if (!currentId) return;
                confirmSwal('Hapus data analisa ini?', function() {
                    $.ajax({
                        url: `/api/wwtp-analisa/${currentId}`,
                        method: 'DELETE',
                        data: {
                            _token: csrfToken
                        },
                        success: function() {
                            detailModal.hide();
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
                            _token: csrfToken
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

            window.downloadPdf = function(id) {
                window.open(`/wwtp/data_analisa/${id}/pdf`, '_blank');
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

            function standardDisplay(value, unit) {
                if (value === undefined || value === null || value === '') {
                    return '<span class="text-muted">-</span>';
                }

                const unitText = unit ? ` ${escapeHtml(unit)}` : '';
                return `<span class="fw-semibold">${num(value)}${unitText}</span>`;
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function cleanupModalOverlay() {
                if ($('.modal.show').length) return;

                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css({
                    overflow: '',
                    paddingRight: ''
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
