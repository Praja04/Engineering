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

        .detail-item {
            transition: all .2s ease;
        }

        .detail-item:hover {
            border-color: #299cdb !important;
            background-color: rgba(41, 156, 219, .05);
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
                                        <p class="text-muted mb-1 small">Parameter Diuji</p>
                                        <h5 class="fw-bold mb-0">5 Parameter</h5>
                                        <small class="text-muted">COD, TSS, pH, EC, DO</small>
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
                                        <th class="fw-semibold text-center">COD (mg/L)</th>
                                        <th class="fw-semibold text-center">TSS (mg/L)</th>
                                        <th class="fw-semibold text-center">pH</th>
                                        <th class="fw-semibold text-center">EC (µS/cm)</th>
                                        <th class="fw-semibold text-center">DO (mg/L)</th>
                                        <th class="fw-semibold text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="analisaTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
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

            {{-- =========================================================== --}}
            {{-- MODAL: Detail Analisa --}}
            {{-- =========================================================== --}}
            <div class="modal fade" id="detailAnalisaModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="mdi mdi-flask-outline me-2"></i>Detail Data Analisa WWTP
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4" id="modalAnalisaContent"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-danger" id="btnDelete" style="display:none;">
                                <i class="mdi mdi-trash-can me-2"></i>Hapus Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================================================== --}}
            {{-- MODAL: Edit Analisa --}}
            {{-- =========================================================== --}}
            <div class="modal fade" id="editAnalisaModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="mdi mdi-pencil me-2"></i>Edit Data Analisa WWTP
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form id="editAnalisaForm">
                                <input type="hidden" id="edit_id">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Tanggal <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit_tanggal" required>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <!-- COD -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">COD (mg/L)</label>
                                        <input type="number" step="0.01" class="form-control" id="edit_cod">
                                    </div>
                                    <!-- TSS -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">TSS (mg/L)</label>
                                        <input type="number" step="0.01" class="form-control" id="edit_tss">
                                    </div>
                                    <!-- pH -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">pH</label>
                                        <input type="number" step="0.01" class="form-control" id="edit_ph"
                                            min="0" max="14">
                                    </div>
                                    <!-- EC -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">EC (µS/cm)</label>
                                        <input type="number" step="0.01" class="form-control" id="edit_ec">
                                    </div>
                                    <!-- DO -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">DO (mg/L)</label>
                                        <input type="number" step="0.01" class="form-control" id="edit_do">
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
@endsection

@section('scripts')
    <script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const PER_PAGE = 10;
            const userJabatan = "{{ Auth::user()->jabatan }}";
            const canEditDelete = userJabatan !== 'operator';

            const state = {
                currentPage: 1,
                lastPage: 1,
                total: 0,
                bulan: '',
                search: '',
            };

            let currentId = null;

            // Init Load
            loadData(1);

            // Load data function
            function loadData(page) {
                page = page || state.currentPage;

                const params = {
                    page,
                    per_page: PER_PAGE
                };
                if (state.bulan) params.bulan = state.bulan;
                if (state.search) params.search = state.search;

                showLoading('#analisaTableBody', 8);
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

            // Render Table Body
            function renderTable(data, from) {
                const tbody = $('#analisaTableBody');
                tbody.empty();

                if (!data || !data.length) {
                    tbody.append(`<tr><td colspan="8" class="text-center py-4 text-muted">
                    <i class="mdi mdi-inbox me-2"></i>Tidak ada data analisa</td></tr>`);
                    return;
                }

                data.forEach(function(item, idx) {
                    const no = (from || 1) + idx;
                    let btns = `<button class="btn btn-sm btn-outline-info me-1"
                                onclick="showDetail(${item.id})" title="Lihat Detail">
                                <i class="mdi mdi-eye"></i></button>`;
                    if (canEditDelete) {
                        btns += `
                            <button class="btn btn-sm btn-outline-success me-1"
                                onclick="showEdit(${item.id})" title="Edit">
                            <i class="mdi mdi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger"
                                onclick="confirmDelete(${item.id})" title="Hapus">
                            <i class="mdi mdi-trash-can"></i></button>`;
                    }

                    const num = (number) => {
                        if (number == null) return '-';

                        const parsed = parseFloat(number);

                        // Jika desimal semuanya 0, hilangkan
                        return parsed % 1 === 0 ?
                            parsed.toFixed(0) :
                            parsed.toString();
                    };

                    tbody.append(`
                        <tr class="data-row">
                            <td>${no}</td>
                            <td>${formatDate(item.tanggal)}</td>
                            <td class="text-center fw-bold">${num(item.cod)}</td>
                            <td class="text-center fw-bold">${num(item.tss)}</td>
                            <td class="text-center fw-bold text-info">${num(item.ph)}</td>
                            <td class="text-center fw-bold">${num(item.ec)}</td>
                            <td class="text-center fw-bold">${num(item.do)}</td>
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

            // Detail Modal
            window.showDetail = function(id) {
                $.ajax({
                    url: `/api/wwtp-analisa/${id}`,
                    method: 'GET',
                    success: function(record) {

                        const num = (number) => {
                            if (number == null) return '-';

                            const parsed = parseFloat(number);

                            // Jika desimal semuanya 0, hilangkan
                            return parsed % 1 === 0 ?
                                parsed.toFixed(0) :
                                parsed.toString();
                        };

                        currentId = id;
                        $('#modalAnalisaContent').html(`
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="info-box p-3 bg-light rounded">
                                        <p class="text-muted small mb-1">Tanggal Analisa</p>
                                        <p class="fw-bold mb-0 fs-5">${formatDate(record.tanggal)}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box p-3 bg-light rounded">
                                        <p class="text-muted small mb-1">Waktu Input</p>
                                        <p class="fw-bold mb-0">${formatDateTime(record.created_at)}</p>
                                    </div>
                                </div>
                            </div>
                            <h6 class="fw-bold mb-3 text-info">Hasil Pengukuran Parameter</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="detail-item p-4 border rounded bg-light text-center">
                                        <span class="text-muted small">COD</span>
                                        <p class="fw-bold fs-3 mb-0 text-info">${num(record.cod)} <small class="text-muted fs-6">mg/L</small></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="detail-item p-4 border rounded bg-light text-center">
                                        <span class="text-muted small">TSS</span>
                                        <p class="fw-bold fs-3 mb-0 text-info">${num(record.tss)} <small class="text-muted fs-6">mg/L</small></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="detail-item p-4 border rounded bg-light text-center">
                                        <span class="text-muted small">pH</span>
                                        <p class="fw-bold fs-3 mb-0 text-info">${num(record.ph)}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item p-4 border rounded bg-light text-center">
                                        <span class="text-muted small">EC</span>
                                        <p class="fw-bold fs-3 mb-0 text-info">${num(record.ec)} <small class="text-muted fs-6">µS/cm</small></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item p-4 border rounded bg-light text-center">
                                        <span class="text-muted small">DO</span>
                                        <p class="fw-bold fs-3 mb-0 text-info">${num(record.do)} <small class="text-muted fs-6">mg/L</small></p>
                                    </div>
                                </div>
                            </div>`);
                        canEditDelete ? $('#btnDelete').show() : $('#btnDelete').hide();
                        new bootstrap.Modal(document.getElementById('detailAnalisaModal')).show();
                    },
                    error: function() {
                        showError('Gagal memuat detail data analisa');
                    }
                });
            };

            // Edit Modal
            window.showEdit = function(id) {
                $.ajax({
                    url: `/api/wwtp-analisa/${id}`,
                    method: 'GET',
                    success: function(record) {
                        currentId = id;
                        $('#edit_id').val(record.id);
                        $('#edit_tanggal').val(record.tanggal);
                        $('#edit_cod').val(record.cod);
                        $('#edit_tss').val(record.tss);
                        $('#edit_ph').val(record.ph);
                        $('#edit_ec').val(record.ec);
                        $('#edit_do').val(record.do);
                        new bootstrap.Modal(document.getElementById('editAnalisaModal')).show();
                    },
                    error: function() {
                        showError('Gagal memuat data untuk diedit');
                    }
                });
            };

            // Submit Edit
            $('#btnSaveEdit').on('click', function() {
                const id = $('#edit_id').val();
                const btn = $(this);
                const orig = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: `/api/wwtp-analisa/${id}`,
                    method: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        tanggal: $('#edit_tanggal').val(),
                        cod: $('#edit_cod').val(),
                        tss: $('#edit_tss').val(),
                        ph: $('#edit_ph').val(),
                        ec: $('#edit_ec').val(),
                        do: $('#edit_do').val(),
                    },
                    success: function() {
                        $('#editAnalisaModal').modal('hide');
                        showSuccess('Data analisa berhasil diperbarui');
                        loadData(state.currentPage);
                    },
                    error: function(xhr) {
                        showErrorFromXhr(xhr);
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(orig);
                    }
                });
            });

            // Delete from Detail
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

            // Delete directly
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

            // Shared pagination utilities
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

            // Helpers
            function formatDate(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            function formatDateTime(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
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

            function showErrorFromXhr(xhr) {
                const err = xhr.responseJSON;
                let msg = 'Terjadi kesalahan saat menyimpan data!';
                if (err?.message) msg = err.message;
                else if (err?.errors) msg = Object.values(err.errors).flat().join('\n');
                showError(msg);
            }
        });
    </script>
@endsection
