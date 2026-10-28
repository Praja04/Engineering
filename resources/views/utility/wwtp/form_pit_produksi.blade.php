@extends('layouts.app')

@section('title', 'Pit Produksi Harian WWTP')

@section('styles')
    <style>
        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.15rem rgba(41, 156, 219, 0.25);
            border-color: #299cdb;
        }

        .stat-card {
            border-radius: 10px;
            transition: all 0.2s ease-in-out;
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .number-badge {
            font-family: monospace;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .table> :not(caption)>*>* {
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Header Page -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between pb-3">
                        <div>
                            <h4 class="mb-1 text-primary"><i class="mdi mdi-cube-outline me-2"></i>Pit Produksi Harian WWTP
                            </h4>
                            <p class="text-muted mb-0">Input dan kelola volume pit produksi harian (m³) sebagai basis
                                perhitungan KPI Biaya Per Kubik di Dashboard WCO.</p>
                        </div>
                        <div class="d-flex gap-2 mt-3 mt-sm-0">
                            <a href="{{ url('/dashboard/wwtp/wco') }}" class="btn btn-outline-info">
                                <i class="mdi mdi-view-dashboard-outline me-1"></i> Dashboard WCO
                            </a>
                            <a href="{{ url('/wwtp/data_biaya_chemical') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-cash-multiple me-1"></i> Data Biaya Chemical
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Stat Cards -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="stat-icon bg-info-subtle text-info me-3">
                                <i class="mdi mdi-cup-water"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted small fw-semibold text-uppercase">Total Pit Produksi (Filter)</div>
                                <div class="fs-4 fw-bold text-info" id="statTotalM3">0 m³</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="stat-icon bg-success-subtle text-success me-3">
                                <i class="mdi mdi-chart-timeline-variant"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted small fw-semibold text-uppercase">Rata-Rata Harian</div>
                                <div class="fs-4 fw-bold text-success" id="statAvgM3">0 m³/hari</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="stat-icon bg-warning-subtle text-warning me-3">
                                <i class="mdi mdi-calendar-check"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted small fw-semibold text-uppercase">Jumlah Hari Terinput</div>
                                <div class="fs-4 fw-bold text-warning" id="statDaysCount">0 Hari</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area: Form & Table -->
            <div class="row">
                <!-- Form Input -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div
                            class="card-header bg-light border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="mdi mdi-plus-box-outline me-1 text-primary"></i> Input Pit
                                Produksi</h5>
                            <span class="badge bg-primary-subtle text-primary">Harian</span>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <form id="formPitProduksi">
                                @csrf
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label fw-semibold">Tanggal <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal"
                                        value="{{ date('Y-m-d') }}" required>
                                    <div class="form-text">Pilih tanggal pencatatan.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="nilai_pit_produksi" class="form-label fw-semibold">Volume Pit Produksi <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" class="form-control"
                                            id="nilai_pit_produksi" name="nilai_pit_produksi" placeholder="Contoh: 45.50"
                                            required>
                                        <span class="input-group-text fw-semibold bg-light">m³</span>
                                    </div>
                                    <div class="form-text">Input volume pit produksi dalam meter kubik (m³).</div>
                                </div>

                                <div class="mb-4">
                                    <label for="catatan" class="form-label fw-semibold">Catatan / Keterangan</label>
                                    <textarea class="form-control" id="catatan" name="catatan" rows="3"
                                        placeholder="Opsional (contoh: Shift 1-3 normal, running pompa A)"></textarea>
                                </div>

                                <div class="alert alert-info border-0 d-flex align-items-center p-2 mb-3"
                                    style="font-size: 12px;">
                                    <i class="mdi mdi-information-outline fs-5 me-2"></i>
                                    <div>Nilai harian ini langsung diakumulasikan di KPI Biaya Per Kubik Dashboard WCO SHE.
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" id="btnSubmit" class="btn btn-primary flex-grow-1">
                                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Data
                                    </button>
                                    <button type="button" id="btnResetForm" class="btn btn-outline-secondary">
                                        Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Table Riwayat -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-bottom py-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <h5 class="card-title mb-0"><i class="mdi mdi-history me-1 text-info"></i> Riwayat Pit
                                    Produksi Harian</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnReloadTable">
                                    <i class="mdi mdi-refresh me-1"></i> Refresh
                                </button>
                            </div>
                        </div>

                        <!-- Filter Bar -->
                        <div class="card-body border-bottom bg-light-subtle py-2 px-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-3 col-6">
                                    <label class="form-label small text-muted mb-1">Tahun</label>
                                    <select id="filterTahun" class="form-select form-select-sm">
                                        <option value="">Semua Tahun</option>
                                        @php $currentYear = date('Y'); @endphp
                                        @for ($y = $currentYear - 2; $y <= $currentYear + 2; $y++)
                                            <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>
                                                {{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small text-muted mb-1">Bulan</label>
                                    <select id="filterBulan" class="form-select form-select-sm">
                                        <option value="">Semua Bulan</option>
                                        @php
                                            $currMonth = date('n');
                                            $months = [
                                                1 => 'Januari',
                                                2 => 'Februari',
                                                3 => 'Maret',
                                                4 => 'April',
                                                5 => 'Mei',
                                                6 => 'Juni',
                                                7 => 'Juli',
                                                8 => 'Agustus',
                                                9 => 'September',
                                                10 => 'Oktober',
                                                11 => 'November',
                                                12 => 'Desember',
                                            ];
                                        @endphp
                                        @foreach ($months as $num => $name)
                                            <option value="{{ $num }}" {{ $num == $currMonth ? 'selected' : '' }}>
                                                {{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-12">
                                    <label class="form-label small text-muted mb-1">Cari</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                        <input type="text" id="filterSearch" class="form-control"
                                            placeholder="Cari tanggal/catatan...">
                                    </div>
                                </div>
                                <div class="col-md-2 col-12 text-end pt-md-3">
                                    <button type="button" id="btnResetFilter"
                                        class="btn btn-sm btn-outline-secondary w-100">
                                        Reset Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0" id="tablePitProduksi">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 50px;">No</th>
                                            <th style="min-width: 140px;">Tanggal</th>
                                            <th class="text-end" style="min-width: 130px;">Pit Produksi (m³)</th>
                                            <th>Catatan</th>
                                            <th style="min-width: 120px;">Diinput Oleh</th>
                                            <th class="text-center" style="width: 100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <div class="spinner-border spinner-border-sm text-primary me-2"
                                                    role="status"></div>
                                                Memuat data pit produksi...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                                <div class="text-muted small" id="paginationInfo">Menampilkan 0 data</div>
                                <ul class="pagination pagination-sm mb-0" id="paginationNav"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pit Produksi -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEditPitProduksi">
                    @csrf
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="modalEditLabel"><i
                                class="mdi mdi-pencil-outline me-1 text-primary"></i> Edit Pit Produksi Harian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_tanggal" class="form-label fw-semibold">Tanggal <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nilai_pit_produksi" class="form-label fw-semibold">Volume Pit Produksi <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control"
                                    id="edit_nilai_pit_produksi" name="nilai_pit_produksi" required>
                                <span class="input-group-text fw-semibold bg-light">m³</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_catatan" class="form-label fw-semibold">Catatan / Keterangan</label>
                            <textarea class="form-control" id="edit_catatan" name="catatan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnUpdate" class="btn btn-primary">
                            <i class="mdi mdi-content-save-outline me-1"></i> Simpan Perubahan
                        </button>
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
            const editModal = new bootstrap.Modal(document.getElementById('modalEdit'));

            const indoMonths = [
                '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            const indoDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

            function formatIndoDate(dateStr) {
                if (!dateStr) return '-';
                const cleanDate = String(dateStr).split('T')[0];
                const parts = cleanDate.split('-');
                if (parts.length !== 3) return dateStr;
                const year = parseInt(parts[0], 10);
                const month = parseInt(parts[1], 10);
                const dayNum = parseInt(parts[2], 10);
                const d = new Date(year, month - 1, dayNum);
                const dayName = indoDays[d.getDay()] || '';
                const monthName = indoMonths[month] || '';
                return `<div class="fw-semibold text-dark">${dayNum} ${monthName} ${year}</div><div class="text-muted small">${dayName}</div>`;
            }

            function loadData(page = 1) {
                currentPage = page;
                const tahun = $('#filterTahun').val();
                const bulan = $('#filterBulan').val();
                const search = $('#filterSearch').val();

                $('#tableBody').html(`
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            Memuat data pit produksi...
                        </td>
                    </tr>
                `);

                $.ajax({
                    url: '{{ url('/api/wwtp-pit-produksi') }}',
                    type: 'GET',
                    data: {
                        page: page,
                        tahun: tahun,
                        bulan: bulan,
                        search: search
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            renderTable(res.data);
                            renderStats(res.stats);
                            renderPagination(res.data);
                        } else {
                            showErrorTable('Gagal memuat data.');
                        }
                    },
                    error: function(err) {
                        showErrorTable('Terjadi kesalahan saat memuat data dari server.');
                    }
                });
            }

            function renderStats(stats) {
                if (!stats) return;
                const totalM3 = parseFloat(stats.total_m3 || 0).toLocaleString('id-ID', {
                    minimumFractionDigits: 1,
                    maximumFractionDigits: 2
                });
                const avgM3 = parseFloat(stats.avg_m3 || 0).toLocaleString('id-ID', {
                    minimumFractionDigits: 1,
                    maximumFractionDigits: 2
                });
                const count = stats.days_count || 0;

                $('#statTotalM3').text(`${totalM3} m³`);
                $('#statAvgM3').text(`${avgM3} m³/hari`);
                $('#statDaysCount').text(`${count} Hari`);
            }

            function renderTable(paginated) {
                const rows = paginated.data;
                if (!rows || rows.length === 0) {
                    $('#tableBody').html(`
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="mdi mdi-alert-circle-outline fs-3 d-block mb-1 text-warning"></i>
                                Belum ada data pit produksi untuk periode ini.
                            </td>
                        </tr>
                    `);
                    $('#paginationInfo').text('Menampilkan 0 data');
                    $('#paginationNav').empty();
                    return;
                }

                let html = '';
                const fromIndex = paginated.from || 1;

                rows.forEach((item, idx) => {
                    const no = fromIndex + idx;
                    const dateFormatted = formatIndoDate(item.tanggal);
                    const nilaiM3 = parseFloat(item.nilai_pit_produksi || 0).toLocaleString('id-ID', {
                        minimumFractionDigits: 1,
                        maximumFractionDigits: 2
                    });
                    const catatan = item.catatan ?
                        `<span class="text-secondary">${escapeHtml(item.catatan)}</span>` :
                        '<span class="text-muted">-</span>';
                    const creator = item.created_by ? item.created_by.username : (item.created_by || '-');

                    html += `
                        <tr>
                            <td class="text-center text-muted fw-semibold">${no}</td>
                            <td>${dateFormatted}</td>
                            <td class="text-end">
                                <span class="badge bg-info-subtle text-info number-badge px-2 py-1">${nilaiM3} m³</span>
                            </td>
                            <td>${catatan}</td>
                            <td><span class="badge bg-light text-secondary border">${escapeHtml(creator)}</span></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-edit" data-id="${item.id}" title="Edit Data">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-delete" data-id="${item.id}" data-date="${item.tanggal}" title="Hapus Data">
                                        <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                $('#tableBody').html(html);
                $('#paginationInfo').text(
                    `Menampilkan ${paginated.from || 0} - ${paginated.to || 0} dari ${paginated.total || 0} data`
                    );
            }

            function renderPagination(paginated) {
                const nav = $('#paginationNav');
                nav.empty();

                if (!paginated || paginated.last_page <= 1) return;

                // Previous
                const prevDisabled = paginated.current_page === 1 ? 'disabled' : '';
                nav.append(`
                    <li class="page-item ${prevDisabled}">
                        <a class="page-link" href="javascript:void(0)" data-page="${paginated.current_page - 1}">&laquo;</a>
                    </li>
                `);

                // Pages
                for (let p = 1; p <= paginated.last_page; p++) {
                    if (p === 1 || p === paginated.last_page || (p >= paginated.current_page - 2 && p <= paginated
                            .current_page + 2)) {
                        const active = p === paginated.current_page ? 'active' : '';
                        nav.append(`
                            <li class="page-item ${active}">
                                <a class="page-link" href="javascript:void(0)" data-page="${p}">${p}</a>
                            </li>
                        `);
                    } else if (p === paginated.current_page - 3 || p === paginated.current_page + 3) {
                        nav.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                    }
                }

                // Next
                const nextDisabled = paginated.current_page === paginated.last_page ? 'disabled' : '';
                nav.append(`
                    <li class="page-item ${nextDisabled}">
                        <a class="page-link" href="javascript:void(0)" data-page="${paginated.current_page + 1}">&raquo;</a>
                    </li>
                `);
            }

            function showErrorTable(msg) {
                $('#tableBody').html(`
                    <tr>
                        <td colspan="6" class="text-center py-4 text-danger">
                            <i class="mdi mdi-alert-circle fs-3 d-block mb-1"></i>
                            ${escapeHtml(msg)}
                        </td>
                    </tr>
                `);
            }

            function escapeHtml(text) {
                if (!text) return '';
                return String(text)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // Pagination Click
            $(document).on('click', '#paginationNav .page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page && page !== currentPage) {
                    loadData(page);
                }
            });

            // Filter Actions
            $('#filterTahun, #filterBulan').on('change', function() {
                loadData(1);
            });

            let searchTimeout = null;
            $('#filterSearch').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadData(1);
                }, 400);
            });

            $('#btnResetFilter').on('click', function() {
                const currentYear = new Date().getFullYear();
                const currentMonth = new Date().getMonth() + 1;
                $('#filterTahun').val(currentYear);
                $('#filterBulan').val(currentMonth);
                $('#filterSearch').val('');
                loadData(1);
            });

            $('#btnReloadTable').on('click', function() {
                loadData(currentPage);
            });

            // Reset Form Input
            $('#btnResetForm').on('click', function() {
                $('#formPitProduksi')[0].reset();
                $('#tanggal').val(new Date().toISOString().split('T')[0]);
            });

            // Submit Form Input Harian
            $('#formPitProduksi').on('submit', function(e) {
                e.preventDefault();
                const btn = $('#btnSubmit');
                const origHtml = btn.html();

                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                const formData = {
                    _token: '{{ csrf_token() }}',
                    tanggal: $('#tanggal').val(),
                    nilai_pit_produksi: $('#nilai_pit_produksi').val(),
                    catatan: $('#catatan').val()
                };

                $.ajax({
                    url: '{{ url('/api/wwtp-pit-produksi') }}',
                    type: 'POST',
                    data: formData,
                    success: function(res) {
                        btn.prop('disabled', false).html(origHtml);
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message ||
                                    'Data pit produksi harian berhasil disimpan.',
                                timer: 1800,
                                showConfirmButton: false
                            });
                            $('#nilai_pit_produksi').val('');
                            $('#catatan').val('');
                            loadData(1);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Gagal menyimpan data.'
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(origHtml);
                        let msg = 'Terjadi kesalahan sistem.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errs = Object.values(xhr.responseJSON.errors).flat();
                            msg = errs.join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            html: msg
                        });
                    }
                });
            });

            // Open Edit Modal
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: `{{ url('/api/wwtp-pit-produksi') }}/${id}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.status === 'success' && res.data) {
                            const d = res.data;
                            $('#edit_id').val(d.id);
                            $('#edit_tanggal').val(String(d.tanggal).split('T')[0]);
                            $('#edit_nilai_pit_produksi').val(d.nilai_pit_produksi);
                            $('#edit_catatan').val(d.catatan || '');
                            editModal.show();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Data tidak ditemukan.'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal mengambil data.'
                        });
                    }
                });
            });

            // Submit Edit Form
            $('#formEditPitProduksi').on('submit', function(e) {
                e.preventDefault();
                const id = $('#edit_id').val();
                const btn = $('#btnUpdate');
                const origHtml = btn.html();

                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Memperbarui...');

                const formData = {
                    _token: '{{ csrf_token() }}',
                    tanggal: $('#edit_tanggal').val(),
                    nilai_pit_produksi: $('#edit_nilai_pit_produksi').val(),
                    catatan: $('#edit_catatan').val()
                };

                $.ajax({
                    url: `{{ url('/api/wwtp-pit-produksi') }}/${id}`,
                    type: 'PUT',
                    data: formData,
                    success: function(res) {
                        btn.prop('disabled', false).html(origHtml);
                        if (res.status === 'success') {
                            editModal.hide();
                            Swal.fire({
                                icon: 'success',
                                title: 'Diperbarui!',
                                text: res.message ||
                                    'Data pit produksi berhasil diperbarui.',
                                timer: 1800,
                                showConfirmButton: false
                            });
                            loadData(currentPage);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Gagal memperbarui.'
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(origHtml);
                        let msg = 'Terjadi kesalahan sistem.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errs = Object.values(xhr.responseJSON.errors).flat();
                            msg = errs.join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            html: msg
                        });
                    }
                });
            });

            // Delete Record
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const date = $(this).data('date');

                Swal.fire({
                    title: 'Hapus Data Pit Produksi?',
                    text: `Apakah Anda yakin ingin menghapus data tanggal ${date}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '<i class="mdi mdi-trash-can-outline me-1"></i> Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('/api/wwtp-pit-produksi') }}/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                if (res.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Dihapus!',
                                        text: res.message ||
                                            'Data berhasil dihapus.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    loadData(currentPage);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: res.message || 'Gagal menghapus.'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat menghapus data.'
                                });
                            }
                        });
                    }
                });
            });

            // Initial Load
            loadData(1);
        });
    </script>
@endsection
