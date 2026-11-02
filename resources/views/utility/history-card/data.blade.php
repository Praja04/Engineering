@extends('layouts.app')

@section('title', 'Data History Card - Operasional')

@section('styles')
    <style>
        .sheet-card-preview {
            background-color: #ffffff;
            border: 2px solid #333333;
            border-radius: 4px;
            padding: 16px;
        }

        .sheet-header-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin-bottom: 0;
        }

        .sheet-header-table td {
            border: 1px solid #000;
            padding: 6px 12px;
            vertical-align: middle;
        }

        .sheet-main-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            border-top: none;
        }

        .sheet-main-table th,
        .sheet-main-table td {
            border: 1px solid #000;
            padding: 6px 10px;
            font-size: 0.88rem;
        }

        .sheet-main-table th {
            background-color: #f8f9fa;
            text-align: center;
            font-weight: 700;
        }

        .sheet-footer-code {
            font-size: 0.78rem;
            font-weight: 700;
            text-align: right;
            margin-top: 6px;
            color: #111;
            letter-spacing: 0.5px;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Header Banner --}}
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 12px;">
                        <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <h4 class="text-white fw-bold mb-1">
                                    <i class="ri-database-2-line text-warning me-2"></i>
                                    Data History Card - Operasional
                                </h4>
                                <p class="text-white-50 mb-0">
                                    Rekap riwayat pelaksanaan aktivitas utility & operasional (FRM/EUT/01/009/001-00)
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('history-card.index') }}"
                                    class="btn btn-warning btn-sm fw-semibold shadow-sm">
                                    <i class="ri-add-line me-1"></i> Form Input Baru
                                </a>
                                <button type="button" class="btn btn-outline-light btn-sm fw-semibold shadow-sm"
                                    data-bs-toggle="modal" data-bs-target="#modalSelectAreaPrint">
                                    <i class="ri-printer-line me-1"></i> Cetak Format Sheet
                                </button>
                                <a href="{{ route('history-card.export') }}" id="btnExportCsv"
                                    class="btn btn-success btn-sm fw-semibold shadow-sm">
                                    <i class="ri-file-excel-line me-1"></i> Export CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Section --}}
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3">
                            <form id="filterForm">
                                <div class="row g-2 align-items-end">
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <label class="form-label small fw-semibold text-muted mb-1">FILTER AREA</label>
                                        <select name="area" id="filterArea" class="form-select form-select-sm">
                                            <option value="">Semua Area</option>
                                            @foreach ($areas as $area)
                                                <option value="{{ $area }}"
                                                    {{ $selectedArea === $area ? 'selected' : '' }}>
                                                    {{ $area }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-3 col-6">
                                        <label class="form-label small fw-semibold text-muted mb-1">BULAN</label>
                                        <select name="bulan" id="filterBulan" class="form-select form-select-sm">
                                            <option value="">Semua Bulan</option>
                                            @for ($m = 1; $m <= 12; $m++)
                                                @php $val = sprintf('%02d', $m); @endphp
                                                <option value="{{ $val }}"
                                                    {{ $selectedMonth == $val ? 'selected' : '' }}>
                                                    {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-3 col-6">
                                        <label class="form-label small fw-semibold text-muted mb-1">TAHUN</label>
                                        <select name="tahun" id="filterTahun" class="form-select form-select-sm">
                                            <option value="">Semua Tahun</option>
                                            @php $currentYear = (int)date('Y'); @endphp
                                            @for ($y = $currentYear - 2; $y <= $currentYear + 2; $y++)
                                                <option value="{{ $y }}"
                                                    {{ $selectedYear == $y ? 'selected' : '' }}>
                                                    {{ $y }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-6 col-6">
                                        <label class="form-label small fw-semibold text-muted mb-1">TGL MULAI
                                            (Opsional)</label>
                                        <input type="date" name="start_date" id="filterStartDate"
                                            class="form-control form-control-sm" value="{{ $startDate }}">
                                    </div>

                                    <div class="col-lg-2 col-md-6 col-6">
                                        <label class="form-label small fw-semibold text-muted mb-1">TGL SELESAI
                                            (Opsional)</label>
                                        <input type="date" name="end_date" id="filterEndDate"
                                            class="form-control form-control-sm" value="{{ $endDate }}">
                                    </div>

                                    <div class="col-lg-1 col-12 d-flex gap-1">
                                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold"
                                            title="Terapkan Filter">
                                            <i class="ri-filter-line"></i>
                                        </button>
                                        <button type="button" id="btnResetFilter" class="btn btn-light btn-sm border w-100"
                                            title="Reset Filter">
                                            <i class="ri-refresh-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Table Card --}}
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div
                            class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs me-2">
                                    <span class="avatar-title bg-primary-subtle rounded-circle">
                                        <i class="ri-list-check-2 fs-14"></i>
                                    </span>
                                </div>
                                <h6 class="card-title mb-0 fw-bold text-dark">
                                    Daftar Riwayat History Card
                                    <span id="badgeSelectedArea"
                                        class="badge bg-info-subtle text-info border border-info-subtle ms-2"
                                        style="display: none;"></span>
                                </h6>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <div class="input-group input-group-sm" style="max-width: 250px;">
                                    <input type="text" id="filterSearch" class="form-control"
                                        placeholder="Cari deskripsi / area...">
                                    <button class="btn btn-outline-secondary" type="button" id="btnSearch">
                                        <i class="ri-search-line"></i>
                                    </button>
                                </div>
                                <span class="badge bg-light text-muted border" id="badgeTotalRecords">Memuat...</span>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 5%;">No</th>
                                            <th class="text-center" style="width: 12%;">Tanggal</th>
                                            <th class="text-center" style="width: 11%;">Jam (WIB)</th>
                                            <th style="width: 20%;">Area</th>
                                            <th>Deskripsi</th>
                                            <th class="text-center" style="width: 13%;">Teknisi</th>
                                            @if (auth()->user()->jabatan != 'operator')
                                                <th class="text-center" style="width: 10%;">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyHistoryCard">
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="spinner-border text-primary spinner-border-sm me-1"
                                                    role="status"></div>
                                                <span class="text-muted">Memuat data riwayat history card...</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top py-3" id="paginationContainer"
                            style="display: none;">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="small text-muted" id="paginationInfo"></div>
                                <div>
                                    <ul class="pagination pagination-sm mb-0" id="paginationLinks"></ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="modalEditHistoryCard" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white py-3">
                    <h6 class="modal-title fw-bold text-white mb-0">
                        <i class="ri-pencil-line me-1"></i> Edit Data History Card
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEditHistoryCard">
                    @csrf
                    <input type="hidden" id="editRecordId">
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" id="editTanggal" name="tanggal" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold mb-0">Jam (WIB) <span
                                            class="text-danger">*</span></label>
                                    <button type="button"
                                        class="btn btn-link btn-sm p-0 text-decoration-none text-primary fw-semibold"
                                        id="btnNowJamEdit" title="Gunakan waktu sekarang">
                                        <i class="ri-time-line me-1"></i>Sekarang
                                    </button>
                                </div>
                                <div class="input-group">
                                    <input type="text" id="editJam" name="jam" class="form-control" required
                                        placeholder="HH:MM (Custom WIB)">
                                    <span class="input-group-text bg-light fw-bold text-primary">WIB</span>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">Format 24 Jam (WIB). Bisa diketik
                                    manual atau dipilih.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Area <span class="text-danger">*</span></label>
                            <select id="editArea" name="area" class="form-select" required>
                                @foreach ($areas as $area)
                                    <option value="{{ $area }}">{{ $area }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi Pelaksanaan <span
                                    class="text-danger">*</span></label>
                            <textarea id="editDeskripsi" name="deskripsi" rows="4" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSaveEdit" class="btn btn-primary btn-sm px-3 fw-semibold">
                            <i class="ri-save-line me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Pilih Area Cetak --}}
    <div class="modal fade" id="modalSelectAreaPrint" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white py-3">
                    <h6 class="modal-title fw-bold text-white mb-0">
                        <i class="ri-printer-line me-1"></i> Pilih Area Cetak History Card
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Area yang Mau Dicetak <span
                                class="text-danger">*</span></label>
                        <select id="modalPrintAreaSelect" class="form-select fw-medium">
                            @foreach ($areas as $area)
                                <option value="{{ $area }}">{{ $area }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Judul lembar sheet akan tercetak otomatis dengan nama area
                            terpilih.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnConfirmPrint" class="btn btn-primary btn-sm px-3 fw-semibold">
                        <i class="ri-printer-line me-1"></i> Buka Cetak Sheet
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        const API_URL = "{{ route('history-card.get-data') }}";
        const EXPORT_BASE_URL = "{{ route('history-card.export') }}";
        const UPDATE_URL = "{{ url('utility/history-card/update') }}";
        const DESTROY_URL = "{{ url('utility/history-card/destroy') }}";

        let currentPage = 1;
        let searchTimeout = null;

        // Escape HTML utility to prevent XSS
        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Load data via AJAX
        function loadData(page = 1) {
            currentPage = page;

            const area = $('#filterArea').val();
            const bulan = $('#filterBulan').val();
            const tahun = $('#filterTahun').val();
            const startDate = $('#filterStartDate').val();
            const endDate = $('#filterEndDate').val();
            const search = $('#filterSearch').val();

            // Update CSV Export link with current filters
            const exportParams = new URLSearchParams();
            if (area) exportParams.set('area', area);
            if (bulan) exportParams.set('bulan', bulan);
            if (tahun) exportParams.set('tahun', tahun);
            if (startDate) exportParams.set('start_date', startDate);
            if (endDate) exportParams.set('end_date', endDate);
            $('#btnExportCsv').attr('href', `${EXPORT_BASE_URL}?${exportParams.toString()}`);

            // Loading state in tbody
            $('#tbodyHistoryCard').html(`
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="spinner-border text-primary spinner-border-sm me-1" role="status"></div>
                        <span class="text-muted">Memuat data riwayat history card...</span>
                    </td>
                </tr>
            `);

            $.ajax({
                url: API_URL,
                type: "GET",
                data: {
                    page: page,
                    area: area,
                    bulan: bulan,
                    tahun: tahun,
                    start_date: startDate,
                    end_date: endDate,
                    search: search,
                    per_page: 25
                },
                success: function(res) {
                    if (!res.status) {
                        $('#tbodyHistoryCard').html(`
                            <tr>
                                <td colspan="7" class="text-center py-4 text-danger">
                                    <i class="ri-error-warning-line me-1"></i> Gagal memuat data.
                                </td>
                            </tr>
                        `);
                        return;
                    }

                    // Update area header badge
                    if (area) {
                        $('#badgeSelectedArea').text(area).show();
                    } else {
                        $('#badgeSelectedArea').hide();
                    }

                    // Update total count badge
                    const total = res.pagination ? res.pagination.total : res.data.length;
                    $('#badgeTotalRecords').text(`Total: ${total} Catatan`);

                    // Check empty data
                    if (!res.data || res.data.length === 0) {
                        $('#tbodyHistoryCard').html(`
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="ri-inbox-line fs-32 text-secondary d-block mb-2"></i>
                                    Tidak ada data history card untuk filter yang dipilih.
                                </td>
                            </tr>
                        `);
                        $('#paginationContainer').hide();
                        return;
                    }

                    // Render rows
                    let html = '';
                    const fromIndex = res.pagination ? res.pagination.from : 1;

                    res.data.forEach((item, idx) => {
                        const rowNo = fromIndex + idx;
                        html += `
                            <tr>
                                <td class="text-center fw-medium">${rowNo}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">
                                        ${escapeHtml(item.tanggal_formatted)}
                                    </span>
                                </td>
                                <td class="text-center fw-medium">
                                    <span class="badge bg-light text-dark border">
                                        ${escapeHtml(item.jam_formatted)}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-primary">
                                        ${escapeHtml(item.area)}
                                    </span>
                                </td>
                                <td style="white-space: pre-wrap; word-break: break-word;">${escapeHtml(item.deskripsi)}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        <i class="ri-user-3-line me-1"></i>${escapeHtml(item.teknisi)}
                                    </span>
                                </td>
                                @if (auth()->user()->jabatan != 'operator')
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btnEditRecord"
                                                data-id="${item.id}" data-tanggal="${escapeHtml(item.tanggal)}"
                                                data-jam="${escapeHtml(item.jam)}" data-area="${escapeHtml(item.area)}"
                                                data-deskripsi="${escapeHtml(item.deskripsi)}" title="Edit Catatan">
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-outline-danger btnDeleteRecord"
                                                data-id="${item.id}"
                                                title="Hapus Catatan">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        `;
                    });

                    $('#tbodyHistoryCard').html(html);

                    // Render pagination
                    renderPagination(res.pagination);
                },
                error: function(xhr) {
                    $('#tbodyHistoryCard').html(`
                        <tr>
                            <td colspan="7" class="text-center py-4 text-danger">
                                <i class="ri-error-warning-line me-1"></i> Terjadi kesalahan koneksi saat memuat data.
                                <br><button type="button" class="btn btn-outline-danger btn-sm mt-2" onclick="loadData(${page})">
                                    <i class="ri-refresh-line me-1"></i> Coba Lagi
                                </button>
                            </td>
                        </tr>
                    `);
                    $('#badgeTotalRecords').text('Error');
                    $('#paginationContainer').hide();
                }
            });
        }

        // Render Pagination UI
        function renderPagination(p) {
            if (!p || p.total <= p.per_page) {
                $('#paginationContainer').hide();
                return;
            }

            $('#paginationInfo').text(`Menampilkan ${p.from} s/d ${p.to} dari ${p.total} catatan`);

            let linksHtml = '';

            // Previous Button
            if (p.current_page > 1) {
                linksHtml += `
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="loadData(${p.current_page - 1})">&laquo;</a>
                    </li>
                `;
            } else {
                linksHtml += `
                    <li class="page-item disabled">
                        <span class="page-link">&laquo;</span>
                    </li>
                `;
            }

            // Page numbers
            const startPage = Math.max(1, p.current_page - 2);
            const endPage = Math.min(p.last_page, p.current_page + 2);

            if (startPage > 1) {
                linksHtml +=
                    `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadData(1)">1</a></li>`;
                if (startPage > 2) {
                    linksHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                if (i === p.current_page) {
                    linksHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                } else {
                    linksHtml +=
                        `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadData(${i})">${i}</a></li>`;
                }
            }

            if (endPage < p.last_page) {
                if (endPage < p.last_page - 1) {
                    linksHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
                linksHtml +=
                    `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadData(${p.last_page})">${p.last_page}</a></li>`;
            }

            // Next Button
            if (p.current_page < p.last_page) {
                linksHtml += `
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="loadData(${p.current_page + 1})">&raquo;</a>
                    </li>
                `;
            } else {
                linksHtml += `
                    <li class="page-item disabled">
                        <span class="page-link">&raquo;</span>
                    </li>
                `;
            }

            $('#paginationLinks').html(linksHtml);
            $('#paginationContainer').show();
        }

        $(document).ready(function() {
            // Flatpickr for Modal Edit
            const editJamFp = flatpickr("#editJam", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 1,
                allowInput: true,
            });

            $('#btnNowJamEdit').on('click', function() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const timeStr = `${hours}:${minutes}`;
                $('#editJam').val(timeStr);
                editJamFp.setDate(timeStr);
            });

            // Initial AJAX load
            loadData(1);

            // Filter submit
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                loadData(1);
            });

            // Reset filter
            $('#btnResetFilter').on('click', function() {
                $('#filterArea').val('');
                $('#filterBulan').val('');
                $('#filterTahun').val('');
                $('#filterStartDate').val('');
                $('#filterEndDate').val('');
                $('#filterSearch').val('');
                loadData(1);
            });

            // Search input click & enter
            $('#btnSearch').on('click', function() {
                loadData(1);
            });

            $('#filterSearch').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    loadData(1);
                } else {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        loadData(1);
                    }, 400);
                }
            });

            // Confirm Print Modal
            $('#btnConfirmPrint').on('click', function() {
                const area = $('#modalPrintAreaSelect').val();
                const url = "{{ route('history-card.print') }}?area=" + encodeURIComponent(area);
                window.open(url, '_blank');
                $('#modalSelectAreaPrint').modal('hide');
            });

            // Open Edit Modal
            $(document).on('click', '.btnEditRecord', function() {
                const id = $(this).data('id');
                const tanggal = $(this).data('tanggal');
                const jam = $(this).data('jam');
                const area = $(this).data('area');
                const deskripsi = $(this).data('deskripsi');

                $('#editRecordId').val(id);
                $('#editTanggal').val(tanggal);
                editJamFp.setDate(jam);
                $('#editArea').val(area);
                $('#editDeskripsi').val(deskripsi);

                $('#modalEditHistoryCard').modal('show');
            });

            // Save Edit via AJAX
            $('#formEditHistoryCard').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editRecordId').val();
                const $btn = $('#btnSaveEdit');
                const origText = $btn.html();

                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: `${UPDATE_URL}/${id}`,
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#modalEditHistoryCard').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message || 'Data berhasil diperbarui.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadData(currentPage);
                    },
                    error: function(xhr) {
                        let msg = 'Terjadi kesalahan saat memperbarui data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: msg
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(origText);
                    }
                });
            });

            // Delete Record via AJAX
            $(document).on('click', '.btnDeleteRecord', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Hapus Catatan Ini?',
                    text: 'Data riwayat history card yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${DESTROY_URL}/${id}`,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message ||
                                        'Data berhasil dihapus.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadData(currentPage);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: xhr.responseJSON?.message ||
                                        'Gagal menghapus data.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
