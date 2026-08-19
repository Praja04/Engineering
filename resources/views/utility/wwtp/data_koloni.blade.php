@extends('layouts.app')

@section('title', 'Data Riwayat Koloni WWTP')

@section('styles')
    <style>
        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.15rem rgba(41, 156, 219, 0.25);
            border-color: #299cdb;
        }

        .table> :not(caption)>*>* {
            vertical-align: middle;
        }

        .scientific-badge {
            font-size: 1rem;
            letter-spacing: 0.5px;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">Riwayat Data Koloni WWTP</h4>
                            <p class="text-muted mb-0">Daftar rekaman penginputan laboratorium koloni mingguan.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ url('/wwtp/form_koloni') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Data Koloni
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="filterSample" class="form-label small text-muted fw-semibold">Filter Titik
                                        Sample</label>
                                    <select id="filterSample" class="form-select">
                                        <option value="">Semua Sample</option>
                                        @foreach ($samples as $sample)
                                            <option value="{{ $sample->id }}">{{ $sample->nama_sample }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterBulan" class="form-label small text-muted fw-semibold">Filter
                                        Bulan</label>
                                    <input type="month" id="filterBulan" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="searchData" class="form-label small text-muted fw-semibold">Cari
                                        Data</label>
                                    <input type="text" id="searchData" class="form-control"
                                        placeholder="Cari tanggal atau nama sample...">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" id="btnReset" class="btn btn-soft-secondary w-100">
                                        <i class="mdi mdi-refresh me-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 80px;" class="text-center">No</th>
                                            <th>Periode Minggu</th>
                                            <th style="width: 250px;" class="text-center">Sampel Terisi</th>
                                            <th style="width: 180px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dataTableBody">
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="mdi mdi-loading mdi-spin fs-4 mb-2 d-block"></i>
                                                Memuat data riwayat...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination Container -->
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted small" id="paginationInfo">
                                    Menampilkan 0 sampai 0 dari 0 data
                                </div>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-rounded mb-0" id="paginationList">
                                        <!-- Will be populated dynamically -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Week Detail -->
    <div class="modal fade" id="modalWeekDetail" tabindex="-1" aria-labelledby="modalWeekDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="modalWeekDetailLabel">
                        <i class="mdi mdi-format-list-bulleted me-2 text-primary"></i>Rincian Sampel Koloni Mingguan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Week Range Info Header -->
                    <div class="alert alert-info border-0 mb-4" role="alert">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <i class="mdi mdi-calendar-range fs-4 me-2 align-middle"></i>
                                Periode: <strong id="detailPeriodText">-</strong>
                            </div>
                            <span class="badge bg-primary text-white fs-6 px-3 py-2" id="detailWText">W-</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0 text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th style="width: 150px;" class="text-center">Tanggal Uji</th>
                                    <th>Titik Sampel</th>
                                    <th class="text-center" style="width: 220px;">Nilai Koloni</th>
                                    <th>Dibuat Oleh</th>
                                    <th style="width: 150px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="weekDetailTableBody">
                                <!-- Loaded via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="modalEditLabel">Edit Data Koloni</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEdit">
                    @csrf
                    <input type="hidden" id="recordId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Informasi Periode & Sample</label>
                            <div class="bg-light p-3 rounded">
                                <div class="mb-1 small text-muted">Titik Sample:</div>
                                <div class="fw-bold mb-2 text-dark" id="editSampleName">-</div>
                                <div class="mb-1 small text-muted">Tanggal Uji:</div>
                                <div class="fw-bold mb-2 text-primary" id="editTanggalUji">-</div>
                                <div class="small text-muted">Periode Minggu:</div>
                                <div class="fw-semibold text-dark" id="editWeekRange">-</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nilai Koloni (Scientific Notation) <span
                                    class="text-danger">*</span></label>
                            <div class="bg-light p-3 rounded border">
                                <div class="row align-items-center g-2 text-center">
                                    <div class="col-md-5">
                                        <input type="number" step="0.001"
                                            class="form-control text-center font-monospace fs-5 fw-bold"
                                            id="edit_nilai_base" name="nilai_base" required min="0">
                                    </div>
                                    <div class="col-md-2 fw-bold text-muted fs-4">
                                        &times; 10 <sup>^</sup>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="number" class="form-control text-center font-monospace fs-5 fw-bold"
                                            id="edit_nilai_pangkat" name="nilai_pangkat" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" id="btnCancelEdit">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnUpdateRecord">Perbarui Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let currentDataList = [];
            let activeWeekHeaderId = null;

            // Load Data function
            function loadData(page = 1, callback = null) {
                currentPage = page;
                const sampleId = $('#filterSample').val();
                const bulan = $('#filterBulan').val();
                const search = $('#searchData').val();

                $.ajax({
                    url: "{{ url('api/wwtp-koloni') }}",
                    method: 'GET',
                    data: {
                        page: page,
                        master_koloni_id: sampleId,
                        bulan: bulan,
                        search: search,
                        per_page: 10
                    },
                    success: function(response) {
                        currentDataList = response.data;
                        renderTable(response.data, response.from);
                        renderPagination(response);
                        if (callback) callback();
                    },
                    error: function() {
                        $('#dataTableBody').html(
                            `<tr><td colspan="4" class="text-center text-danger py-4">Gagal memuat data. Silakan coba kembali.</td></tr>`
                        );
                    }
                });
            }

            // Init Load
            loadData(1);

            // Table rendering
            function renderTable(data, fromIndex) {
                let rows = '';
                if (!data || data.length === 0) {
                    rows =
                        `<tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada data riwayat koloni ditemukan</td></tr>`;
                    $('#dataTableBody').html(rows);
                    return;
                }

                data.forEach((item, index) => {
                    const startFormatted = formatDateString(item.week_start);
                    const endFormatted = formatDateString(item.week_end);
                    const wNum = getWeekOfMonth(item.week_start);
                    const monthName = getIndonesianMonth(item.week_start);
                    const year = new Date(item.week_start).getFullYear();

                    rows += `
                        <tr>
                            <td class="text-center fw-semibold">${(fromIndex || 1) + index}</td>
                            <td class="fw-semibold text-dark">
                                W${wNum} - ${monthName} ${year} <span class="text-muted fw-normal small">(${startFormatted} s/d ${endFormatted})</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-soft-success text-success px-2 py-1">${item.details.length} Titik Sampel Terisi</span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-soft-primary btn-sm btn-detail" data-id="${item.id}">
                                    <i class="mdi mdi-eye-outline me-1"></i> Lihat Detail
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#dataTableBody').html(rows);
            }

            // Render Rincian Detail di Modal
            function showWeekDetails(weekId) {
                activeWeekHeaderId = weekId;
                const weekRecord = currentDataList.find(x => x.id == weekId);
                if (!weekRecord) return;

                const startFormatted = formatDateString(weekRecord.week_start);
                const endFormatted = formatDateString(weekRecord.week_end);
                const wNum = getWeekOfMonth(weekRecord.week_start);
                const monthName = getIndonesianMonth(weekRecord.week_start);
                const year = new Date(weekRecord.week_start).getFullYear();

                $('#detailPeriodText').text(`${startFormatted} s/d ${endFormatted}`);
                $('#detailWText').text(`W${wNum} - ${monthName} ${year}`);

                let rows = '';
                if (weekRecord.details.length === 0) {
                    rows =
                        `<tr><td colspan="5" class="text-center py-3 text-muted">Tidak ada rincian sampel untuk minggu ini.</td></tr>`;
                } else {
                    weekRecord.details.forEach((det, idx) => {
                        const tanggalFormatted = formatDateString(det.tanggal);
                        const mathematicalStr =
                            `${det.nilai_base} &times; 10<sup>${det.nilai_pangkat}</sup>`;
                        const literalStr = `${det.nilai_base}*10^${det.nilai_pangkat}`;

                        rows += `
                            <tr>
                                <td class="text-center fw-semibold">${idx + 1}</td>
                                <td class="text-center text-dark">${tanggalFormatted}</td>
                                <td class="fw-semibold">${det.master_koloni.nama_sample}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-primary border scientific-badge px-2 py-1">${mathematicalStr}</span>
                                    <small class="text-muted font-monospace d-block">(${literalStr})</small>
                                </td>
                                <td class="fw-semibold">${det.created_by?.username ?? '-'}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-soft-warning btn-sm btn-edit-detail" data-id="${det.id}">
                                            <i class="mdi mdi-pencil"></i> Edit
                                        </button>
                                        <button class="btn btn-soft-danger btn-sm btn-delete-detail" data-id="${det.id}">
                                            <i class="mdi mdi-trash-can"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#weekDetailTableBody').html(rows);
                $('#modalWeekDetail').modal('show');
            }

            // Klik Lihat Detail
            $(document).on('click', '.btn-detail', function() {
                const id = $(this).data('id');
                showWeekDetails(id);
            });

            // Klik Edit di dalam Modal Detail
            $(document).on('click', '.btn-edit-detail', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: `{{ url('api/wwtp-koloni') }}/${id}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            const data = response.data;
                            $('#recordId').val(data.id);
                            $('#editSampleName').text(data.master_koloni.nama_sample);
                            const start = formatDateString(data.koloni.week_start);
                            const end = formatDateString(data.koloni.week_end);
                            $('#editTanggalUji').text(formatDateString(data.tanggal));
                            $('#editWeekRange').text(`${start} s/d ${end}`);

                            $('#edit_nilai_base').val(data.nilai_base);
                            $('#edit_nilai_pangkat').val(data.nilai_pangkat);

                            $('#modalWeekDetail').modal('hide');
                            $('#modalEdit').modal('show');
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal mengambil detail data.'
                        });
                    }
                });
            });

            // Klik Batal Edit -> Kembalikan ke Modal Detail
            $('#btnCancelEdit').on('click', function() {
                $('#modalEdit').modal('hide');
                if (activeWeekHeaderId) {
                    showWeekDetails(activeWeekHeaderId);
                }
            });

            // Submit edit form
            $('#formEdit').on('submit', function(e) {
                e.preventDefault();
                const id = $('#recordId').val();
                const btn = $('#btnUpdateRecord');
                const originalText = btn.html();

                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Memperbarui...');

                $.ajax({
                    url: `{{ url('wwtp/koloni') }}/${id}`,
                    method: 'PUT',
                    data: {
                        _token: "{{ csrf_token() }}",
                        nilai_base: $('#edit_nilai_base').val(),
                        nilai_pangkat: $('#edit_nilai_pangkat').val(),
                    },
                    success: function(response) {
                        $('#modalEdit').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadData(currentPage, function() {
                            if (activeWeekHeaderId) {
                                showWeekDetails(activeWeekHeaderId);
                            }
                        });
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON;
                        let msg = 'Gagal memperbarui data koloni!';
                        if (error && error.errors) {
                            msg = Object.values(error.errors).flat().join('<br>');
                        } else if (error && error.message) {
                            msg = error.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: msg
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Klik Hapus di dalam Modal Detail
            $(document).on('click', '.btn-delete-detail', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Data Koloni?',
                    text: "Tindakan ini tidak dapat dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('api/wwtp-koloni') }}/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadData(currentPage, function() {
                                    // Cek apakah data header minggu ini masih ada di list
                                    const stillExists = currentDataList.some(
                                        x => x.id == activeWeekHeaderId);
                                    if (stillExists) {
                                        showWeekDetails(activeWeekHeaderId);
                                    } else {
                                        $('#modalWeekDetail').modal('hide');
                                    }
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Gagal menghapus data koloni.'
                                });
                            }
                        });
                    }
                });
            });

            // Pagination rendering
            function renderPagination(response) {
                const total = response.total;
                const from = response.from || 0;
                const to = response.to || 0;
                $('#paginationInfo').text(`Menampilkan ${from} sampai ${to} dari ${total} data`);

                let listHtml = '';
                const lastPage = response.last_page;
                const current = response.current_page;

                listHtml += `
                    <li class="page-item ${current === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0)" data-page="${current - 1}"><i class="mdi mdi-chevron-left"></i></a>
                    </li>
                `;

                let startPage = Math.max(1, current - 2);
                let endPage = Math.min(lastPage, current + 2);

                for (let i = startPage; i <= endPage; i++) {
                    listHtml += `
                        <li class="page-item ${current === i ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0)" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                listHtml += `
                    <li class="page-item ${current === lastPage ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0)" data-page="${current + 1}"><i class="mdi mdi-chevron-right"></i></a>
                    </li>
                `;

                $('#paginationList').html(listHtml);
            }

            // Click pagination link
            $(document).on('click', '.pagination .page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                const parent = $(this).parent();
                if (page && !parent.hasClass('disabled') && !parent.hasClass('active')) {
                    loadData(page);
                }
            });

            // Filters event listeners
            $('#filterSample, #filterBulan').on('change', function() {
                loadData(1);
            });

            let searchTimer;
            $('#searchData').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => loadData(1), 350);
            });

            // Reset filters
            $('#btnReset').on('click', function() {
                $('#filterSample').val('');
                $('#filterBulan').val('');
                $('#searchData').val('');
                loadData(1);
            });

            // Helper: hitung nomor minggu relative terhadap bulan (1 - 5)
            function getWeekOfMonth(dateStr) {
                const date = new Date(dateStr);
                const day = date.getDate();
                return Math.ceil(day / 7);
            }

            // Helper: nama bulan Indonesia
            function getIndonesianMonth(dateStr) {
                const date = new Date(dateStr);
                const months = [
                    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                ];
                return months[date.getMonth()];
            }

            // Helper: format YYYY-MM-DD to Indonesian Format
            function formatDateString(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });
            }
        });
    </script>
@endsection
