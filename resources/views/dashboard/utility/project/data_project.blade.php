@extends('layouts.app')

@section('title', 'Data Project Engineering Utility')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- ── Header Banner ── --}}
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 12px;">
                        <div class="card-body p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <h4 class="text-white fw-bold mb-1">
                                    <i class="ri-folder-shared-line text-warning me-2"></i>
                                    Data Project Engineering Utility
                                </h4>
                                <p class="text-white-50 mb-0 fs-13">
                                    Kelola data, monitoring target pekerjaan, dan perbarui progres project utility (Boiler,
                                    Utility, WWTP, MTC)
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('utility.project.dashboard') }}"
                                    class="btn btn-info fw-semibold shadow-sm">
                                    <i class="ri-dashboard-2-line me-1"></i> Dashboard
                                </a>
                                @if (Auth::user()->jabatan === 'admin')
                                    <button type="button" class="btn btn-success fw-semibold shadow-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                                        <i class="ri-file-excel-2-line me-1"></i> Upload Excel
                                    </button>
                                @endif
                                <a href="{{ route('utility.project.form') }}" class="btn btn-warning fw-semibold shadow-sm">
                                    <i class="ri-add-line me-1"></i> Tambah Project
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Filter Card ── --}}
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <form id="filterForm" method="GET" action="{{ route('utility.project.data') }}"
                                class="row g-2 align-items-center">
                                {{-- Search --}}
                                <div class="col-lg-3 col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="ri-search-line"></i></span>
                                        <input type="text" class="form-control" id="filterSearch" name="search"
                                            placeholder="Cari nama project, nomor, catatan..." value="{{ $search }}">
                                    </div>
                                </div>

                                {{-- Area Filter --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <select id="filterArea" name="area" class="form-select">
                                        <option value="all">Semua Area</option>
                                        @foreach ($areasList as $ar)
                                            <option value="{{ $ar }}" {{ $areaFilter === $ar ? 'selected' : '' }}>
                                                {{ $ar }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Kategori Filter --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <select id="filterKategori" name="kategori" class="form-select">
                                        <option value="all">Semua Kategori</option>
                                        @foreach ($categoriesList as $cat)
                                            <option value="{{ $cat }}"
                                                {{ $kategoriFilter === $cat ? 'selected' : '' }}>{{ $cat }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Status Filter --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <select id="filterStatus" name="status" class="form-select">
                                        <option value="all">Semua Status</option>
                                        <option value="Selesai" {{ $statusFilter === 'Selesai' ? 'selected' : '' }}>Selesai
                                        </option>
                                        <option value="On Progress"
                                            {{ $statusFilter === 'On Progress' ? 'selected' : '' }}>On Progress</option>
                                        <option value="Terlambat" {{ $statusFilter === 'Terlambat' ? 'selected' : '' }}>
                                            Terlambat</option>
                                    </select>
                                </div>

                                {{-- Priority Filter --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <select id="filterPriority" name="priority" class="form-select">
                                        <option value="all">Semua Prioritas</option>
                                        <option value="Critical"
                                            {{ isset($priorityFilter) && $priorityFilter === 'Critical' ? 'selected' : '' }}>
                                            Critical</option>
                                        <option value="High"
                                            {{ isset($priorityFilter) && $priorityFilter === 'High' ? 'selected' : '' }}>
                                            High</option>
                                        <option value="Medium"
                                            {{ isset($priorityFilter) && $priorityFilter === 'Medium' ? 'selected' : '' }}>
                                            Medium</option>
                                        <option value="Low"
                                            {{ isset($priorityFilter) && $priorityFilter === 'Low' ? 'selected' : '' }}>
                                            Low</option>
                                    </select>
                                </div>

                                {{-- Buttons --}}
                                <div class="col-lg-1 col-md-3 col-sm-6 d-flex gap-1">
                                    <button type="button" id="btnResetFilter" class="btn btn-outline-danger w-100"
                                        title="Reset filter secara async">
                                        <i class="ri-refresh-line"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Data Table Card ── --}}
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h6 class="card-title mb-0 fw-bold text-dark">
                                        <i class="ri-table-line text-primary me-2"></i>Daftar Project
                                    </h6>
                                    <small class="text-muted">Menampilkan <strong id="displayCount">-</strong> dari
                                        total {{ $totalProjectsCount }} project</small>
                                </div>
                                <div class="d-flex gap-1 flex-wrap">
                                    <span id="badgeCountSelesai"
                                        class="badge bg-success-subtle text-success border border-success-subtle">
                                        Selesai: -
                                    </span>
                                    <span id="badgeCountOnProgress"
                                        class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                        On Progress: -
                                    </span>
                                    <span id="badgeCountTerlambat"
                                        class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                        Terlambat: -
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle table-nowrap mb-0" id="projectTable">
                                    <thead class="table-light fs-12 text-muted">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">No</th>
                                            <th style="min-width: 250px;">Nama Project & Target</th>
                                            <th style="width: 90px;" class="text-center">Area</th>
                                            <th style="width: 100px;" class="text-center">Prioritas</th>
                                            <th style="min-width: 130px;">Kategori</th>
                                            <th style="min-width: 140px;">Timeline</th>
                                            <th style="min-width: 140px;">Progress</th>
                                            <th style="width: 110px;" class="text-center">Status</th>
                                            <th style="min-width: 180px;">Catatan Terakhir</th>
                                            <th style="width: 130px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="projectTableBody">
                                        <tr>
                                            <td colspan="10" class="text-center py-5 text-muted">
                                                <div class="spinner-border text-primary spinner-border-sm me-2"
                                                    role="status"></div>
                                                Memuat data project...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Modal 1: Update Progress ── --}}
    <div class="modal fade" id="modalUpdateProgress" tabindex="-1" aria-labelledby="modalUpdateProgressLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="modalUpdateProgressLabel">
                        <i class="ri-edit-2-line text-primary me-2"></i>Update Progress Project
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formUpdateProgress">
                    @csrf
                    <input type="hidden" id="modal_project_id" name="project_id">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-muted fs-12 mb-1">Project</label>
                            <div id="modal_project_nama" class="fw-semibold text-dark fs-14 p-2 bg-light rounded border">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold text-dark mb-0">Progress Persentase (%)</label>
                                <span id="modal_slider_val" class="badge bg-primary fs-13">0%</span>
                            </div>
                            <input type="range" class="form-range" id="modal_progress_range" min="0"
                                max="100" step="1" value="0">
                            <div class="input-grouinput-group-sm mt-2">
                                <span class="input-group-text bg-light">Input Angka</span>
                                <input type="number" class="form-control" id="modal_progress_input" name="progress"
                                    min="0" max="100" value="0" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Tanggal Update</label>
                            <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Catatan Progress / Hasil <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="modal_catatan" name="catatan" rows="3"
                                placeholder="Tuliskan perkembangan pekerjaan terbaru, kendala, atau hasil yang sudah dicapai..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitProgress">
                            <i class="ri-save-line me-1"></i> Simpan Progress
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Modal 2: Riwayat History Progress ── --}}
    <div class="modal fade" id="modalHistory" tabindex="-1" aria-labelledby="modalHistoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="modalHistoryLabel">
                            <i class="ri-history-line text-info me-2"></i>Riwayat Progress Project
                        </h5>
                        <small id="history_project_title" class="text-muted"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 500px; overflow-y: auto;">
                    <div id="history_loading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted mt-2 fs-13">Memuat riwayat...</p>
                    </div>
                    <div id="history_container" class="timeline-2 d-none">
                        {{-- Dinamis diisi via JavaScript --}}
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Modal 3: Import & Migrasi Data Excel ── --}}
    <div class="modal fade" id="modalImportExcel" tabindex="-1" aria-labelledby="modalImportExcelLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalImportExcelLabel">
                            <i class="ri-file-excel-2-line text-success me-2"></i>Upload & Migrasi Data Project (Excel)
                        </h5>
                        <small class="text-muted">Import langsung daftar project yang sudah berjalan ke dalam
                            sistem</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formImportExcel" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        {{-- ── Template Download Banner ── --}}
                        <div class="card border border-primary-subtle bg-primary-subtle bg-opacity-10 mb-3 rounded-3">
                            <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fs-20 flex-shrink-0"
                                        style="width: 42px; height: 42px;">
                                        <i class="ri-file-download-line"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">Download Template Standar Migrasi</h6>
                                        <p class="text-muted mb-0 fs-12">Gunakan format Excel standar dengan contoh data
                                            dan sheet referensi master.</p>
                                    </div>
                                </div>
                                <a href="{{ route('utility.project.template') }}"
                                    class="btn btn-primary btn-sm fw-semibold shadow-sm text-nowrap">
                                    <i class="ri-download-2-line me-1"></i> Download Template (.xlsx)
                                </a>
                            </div>
                        </div>

                        {{-- ── Petunjuk Format Kolom ── --}}
                        <div class="card border border-light-subtle bg-light mb-3 rounded-3">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-dark fs-13 mb-2 d-flex align-items-center">
                                    <i class="ri-information-line text-info me-1"></i> Ketentuan Pengisian Kolom Excel:
                                </h6>
                                <ul class="text-muted fs-12 mb-0 ps-3">
                                    <li class="mb-1"><strong>Nama Project:</strong> Wajib diisi. Jika nama project sudah
                                        ada di sistem, data akan diperbarui (update).</li>
                                    <li class="mb-1"><strong>Area:</strong> Wajib diisi sesuai salah satu dari:
                                        <code>Boiler</code>, <code>Utility</code>, <code>WWTP</code>, <code>MTC</code>.</li>
                                    <li class="mb-1"><strong>Kategori:</strong> Maintenance, Baku Mutu, Compliance, 5R,
                                        KPI BB dan Steam, Digitalisasi, Improvement, dll.</li>
                                    <li class="mb-1"><strong>Prioritas:</strong> <code>Critical</code>, <code>High</code>, <code>Medium</code>, <code>Low</code>. Jika kosong, otomatis diatur ke <code>Medium</code>.</li>
                                    <li class="mb-1"><strong>Nomor Project:</strong> Opsional. Jika kosong, sistem otomatis membuat nomor urut (contoh: <code>PRJ-BOI-001</code>).</li>
                                    <li class="mb-1"><strong>Tanggal & Minggu:</strong> Jika tanggal mulai/selesai kosong tetapi kolom Minggu terisi (contoh: <code>W1 Okt 26</code>, <code>W3 Nov 26</code>), sistem otomatis mengambil tanggal awal/akhir minggu tersebut.</li>
                                    <li><strong>Status & Progress:</strong> Progress angka <code>0 - 100</code>. Jika status kosong, otomatis dihitung dari persentase (100% = <code>Selesai</code>) dan batas minggu selesai (<code>Terlambat</code> jika sudah lewat waktu saat ini, atau <code>On Progress</code> jika masih berjalan).</li>
                                </ul>
                            </div>
                        </div>

                        {{-- ── File Upload Input ── --}}
                        <div class="mb-3">
                            <label for="import_excel_file" class="form-label fw-bold text-dark">
                                Pilih File Excel (.xlsx / .xls / .csv) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="import_excel_file" name="file"
                                    accept=".xlsx, .xls, .csv" required>
                                <button class="btn btn-outline-secondary" type="button" id="btnClearFile"
                                    title="Reset file">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                            <div class="form-text fs-12 text-muted">
                                Maksimal ukuran file 10 MB. Mendukung file Excel (.xlsx, .xls) maupun CSV.
                            </div>
                        </div>

                        {{-- ── Upload Progress State ── --}}
                        <div id="importLoadingState" class="d-none text-center py-3">
                            <div class="spinner-border text-primary spinner-border-sm mb-2" role="status"></div>
                            <div class="fs-13 fw-semibold text-dark">Sedang memproses dan menyimpan data migrasi...</div>
                            <small class="text-muted">Mohon tunggu, proses parsing spreadsheet sedang berjalan.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success fw-semibold" id="btnSubmitImport">
                            <i class="ri-upload-cloud-2-line me-1"></i> Mulai Upload & Migrasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // ── Setup AJAX CSRF Token ──
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            let projectTable = null;

            function escapeHtml(str) {
                if (!str) return '';
                return $('<div>').text(str).html();
            }

            function getAreaBadge(area) {
                if (area === 'Boiler')
                    return '<span class="badge bg-danger-subtle text-danger fw-semibold">Boiler</span>';
                if (area === 'Utility')
                    return '<span class="badge bg-primary-subtle text-primary fw-semibold">Utility</span>';
                if (area === 'WWTP') return '<span class="badge bg-info-subtle text-info fw-semibold">WWTP</span>';
                return `<span class="badge bg-secondary-subtle text-secondary fw-semibold">${escapeHtml(area || 'MTC')}</span>`;
            }

            function getPriorityBadge(priority) {
                if (priority === 'Critical') {
                    return '<span class="badge bg-danger text-white"><i class="ri-alarm-warning-fill me-1"></i>Critical</span>';
                } else if (priority === 'High') {
                    return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle fw-semibold"><i class="ri-arrow-up-circle-fill me-1"></i>High</span>';
                } else if (priority === 'Low') {
                    return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-semibold"><i class="ri-arrow-down-circle-line me-1"></i>Low</span>';
                }
                return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold"><i class="ri-equal-circle-fill me-1"></i>Medium</span>';
            }

            function getStatusBadge(status) {
                if (status === 'Selesai') {
                    return '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="ri-checkbox-circle-line me-1"></i>Selesai</span>';
                }
                if (status === 'Terlambat') {
                    return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="ri-alert-line me-1"></i>Terlambat</span>';
                }
                return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="ri-time-line me-1"></i>On Progress</span>';
            }

            function updateStatsBadges(stats) {
                if (stats) {
                    $('#displayCount').text(stats.filtered);
                    $('#badgeCountSelesai').text(`Selesai: ${stats.selesai}`);
                    $('#badgeCountOnProgress').text(`On Progress: ${stats.on_progress}`);
                    $('#badgeCountTerlambat').text(`Terlambat: ${stats.terlambat}`);
                    return;
                }
                if (!projectTable) return;
                const visibleRows = projectTable.rows({
                    search: 'applied'
                }).nodes();
                let countSelesai = 0,
                    countOnProgress = 0,
                    countTerlambat = 0;
                $(visibleRows).each(function() {
                    const statusText = $(this).find('td:nth-child(8)').text().trim();
                    if (statusText.includes('Selesai')) countSelesai++;
                    else if (statusText.includes('Terlambat')) countTerlambat++;
                    else if (statusText.includes('On Progress')) countOnProgress++;
                });
                $('#displayCount').text(visibleRows.length);
                $('#badgeCountSelesai').text(`Selesai: ${countSelesai}`);
                $('#badgeCountOnProgress').text(`On Progress: ${countOnProgress}`);
                $('#badgeCountTerlambat').text(`Terlambat: ${countTerlambat}`);
            }

            function initDataTable() {
                if ($.fn.DataTable.isDataTable('#projectTable')) {
                    $('#projectTable').DataTable().destroy();
                }
                projectTable = $('#projectTable').DataTable({
                    pageLength: 25,
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "Semua"]
                    ],
                    responsive: true,
                    order: [
                        [0, 'asc']
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [9]
                    }],
                    language: {
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        zeroRecords: "Tidak ada project yang sesuai kriteria",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ project",
                        infoEmpty: "Menampilkan 0 project",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        paginate: {
                            first: "Awal",
                            last: "Akhir",
                            next: '<i class="ri-arrow-right-s-line"></i>',
                            previous: '<i class="ri-arrow-left-s-line"></i>'
                        }
                    },
                    dom: '<"d-flex flex-wrap justify-content-between align-items-center p-3 border-bottom"l>rt<"d-flex flex-wrap justify-content-between align-items-center p-3 border-top"ip>'
                });

                projectTable.on('draw', function() {
                    updateStatsBadges();
                });
            }

            // ── Load Data Project via Async jQuery AJAX ──
            function loadProjectsData() {
                const filters = {
                    format: 'json',
                    search: $('#filterSearch').val(),
                    area: $('#filterArea').val(),
                    kategori: $('#filterKategori').val(),
                    priority: $('#filterPriority').val(),
                    status: $('#filterStatus').val()
                };

                if ($.fn.DataTable.isDataTable('#projectTable')) {
                    $('#projectTable').DataTable().destroy();
                }

                $('#projectTableBody').html(`
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                            Memuat data project...
                        </td>
                    </tr>
                `);

                $.ajax({
                    url: '{{ route('utility.project.data') }}',
                    type: 'GET',
                    data: filters,
                    dataType: 'json',
                    success: function(res) {
                        if (!res.success || !res.data || res.data.length === 0) {
                            $('#projectTableBody').html(`
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="ri-folder-open-line fs-36 text-secondary d-block mb-2"></i>
                                        Tidak ada data project yang sesuai dengan filter pencarian.
                                    </td>
                                </tr>
                            `);
                            updateStatsBadges(res.stats);
                            return;
                        }

                        let html = '';
                        $.each(res.data, function(idx, item) {
                            const targetHtml = item.target_result ? `
                                <div class="text-muted fs-11 text-truncate mt-1" style="max-width: 320px;" title="${escapeHtml(item.target_result)}">
                                    <i class="ri-focus-2-line text-primary me-1"></i>${escapeHtml(item.target_result)}
                                </div>` : '';

                            const startStr = item.start_date || (item.start_week || '-');
                            const finishStr = item.finish_date || (item.finish_week || '-');
                            const finishClass = item.status === 'Terlambat' ? 'text-danger' :
                                'text-dark';
                            const progColor = (item.progress == 100) ? 'bg-success' : (item
                                .status === 'Terlambat' ? 'bg-danger' : 'bg-primary');

                            html += `
                                <tr id="row-project-${item.id}">
                                    <td class="text-center text-muted fw-semibold">${idx + 1}</td>
                                    <td>
                                        <div class="fw-bold text-dark fs-13">${escapeHtml(item.nama_project)}</div>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <span class="badge bg-light text-muted border fs-11">${escapeHtml(item.nomor_project)}</span>
                                        </div>
                                        ${targetHtml}
                                    </td>
                                    <td class="text-center">
                                        ${getAreaBadge(item.area)}
                                    </td>
                                    <td class="text-center">
                                        ${getPriorityBadge(item.priority)}
                                    </td>
                                    <td>
                                        <span class="text-dark fs-12 fw-medium">${escapeHtml(item.kategori)}</span>
                                    </td>
                                    <td class="fs-11">
                                        <div>
                                            <span class="text-muted">Start:</span>
                                            <strong>${escapeHtml(startStr)}</strong>
                                        </div>
                                        <div class="mt-1">
                                            <span class="text-muted">Finish:</span>
                                            <strong class="${finishClass}">
                                                ${escapeHtml(finishStr)}
                                            </strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 7px;">
                                                <div class="progress-bar progress-bar-${item.id} ${progColor}" role="progressbar" style="width: ${item.progress}%;"></div>
                                            </div>
                                            <span class="fs-12 fw-bold text-dark progress-text-${item.id}">${item.progress}%</span>
                                        </div>
                                    </td>
                                    <td class="text-center status-col-${item.id}">
                                        ${getStatusBadge(item.status)}
                                    </td>
                                    <td>
                                        <div class="note-text-${item.id} fs-12 text-muted text-truncate" style="max-width: 220px;" title="${escapeHtml(item.latest_note || '')}">
                                            ${escapeHtml(item.latest_note || '-')}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-update-progress"
                                            data-id="${item.id}"
                                            data-nama="${escapeHtml(item.nama_project)}"
                                            data-progress="${item.progress}"
                                            data-note="${escapeHtml(item.latest_note || '')}" title="Update Progress">
                                            <i class="ri-edit-circle-line"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info btn-view-history"
                                            data-id="${item.id}"
                                            data-nama="${escapeHtml(item.nama_project)}"
                                            title="Lihat Riwayat Update">
                                            <i class="ri-history-line"></i>
                                        </button>
                                        <a href="${item.edit_url}" class="btn btn-sm btn-outline-secondary" title="Edit Data">
                                            <i class="ri-pencil-line"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-project"
                                            data-id="${item.id}"
                                            data-nama="${escapeHtml(item.nama_project)}" title="Hapus Project">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });

                        $('#projectTableBody').html(html);
                        updateStatsBadges(res.stats);
                        initDataTable();
                    },
                    error: function(xhr) {
                        $('#projectTableBody').html(`
                            <tr>
                                <td colspan="10" class="text-center py-5 text-danger">
                                    <i class="ri-error-warning-line fs-36 d-block mb-2"></i>
                                    Gagal memuat data project dari server.
                                </td>
                            </tr>
                        `);
                    }
                });
            }

            // ── Event Handlers for Filter (Async jQuery) ──
            let searchDebounceTimer = null;
            $('#filterSearch').on('input keyup', function() {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function() {
                    loadProjectsData();
                }, 400);
            });

            $('#filterArea, #filterKategori, #filterPriority, #filterStatus').on('change', function() {
                loadProjectsData();
            });

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                loadProjectsData();
            });

            // ── Reset Filter Async ──
            $('#btnResetFilter').on('click', function(e) {
                e.preventDefault();
                $('#filterSearch').val('');
                $('#filterArea').val('all');
                $('#filterKategori').val('all');
                $('#filterPriority').val('all');
                $('#filterStatus').val('all');
                loadProjectsData();
            });

            // ── Initial Load on Page Ready ──
            loadProjectsData();

            // ── Progress Range & Input Sync ──
            $('#modal_progress_range').on('input', function() {
                const val = $(this).val();
                $('#modal_progress_input').val(val);
                $('#modal_slider_val').text(val + '%');
            });

            $('#modal_progress_input').on('input', function() {
                let val = parseInt($(this).val()) || 0;
                if (val < 0) val = 0;
                if (val > 100) val = 100;
                $('#modal_progress_range').val(val);
                $('#modal_slider_val').text(val + '%');
            });

            // ── Open Update Progress Modal ──
            $(document).on('click', '.btn-update-progress', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const prog = parseInt($(this).data('progress')) || 0;

                $('#modal_project_id').val(id);
                $('#modal_project_nama').text(nama);
                $('#modal_progress_range').val(prog);
                $('#modal_progress_input').val(prog);
                $('#modal_slider_val').text(prog + '%');
                $('#modal_catatan').val('');

                $('#modalUpdateProgress').modal('show');
            });

            // ── Submit Progress via Async jQuery AJAX ──
            $('#formUpdateProgress').on('submit', function(e) {
                e.preventDefault();
                const id = $('#modal_project_id').val();
                const $btn = $('#btnSubmitProgress');
                const origText = $btn.html();

                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: `{{ url('utility/project') }}/${id}/progress`,
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            $('#modalUpdateProgress').modal('hide');

                            // Realtime DOM updates
                            const $row = $('#row-project-' + id);
                            const prog = res.data.progress;
                            const status = res.data.status;
                            const note = res.data.latest_note;

                            // Progress bar & text
                            const barColor = (prog == 100) ? 'bg-success' : (status ===
                                'Terlambat' ? 'bg-danger' : 'bg-primary');
                            $row.find('.progress-bar-' + id)
                                .css('width', prog + '%')
                                .attr('class', 'progress-bar progress-bar-' + id + ' ' +
                                    barColor);
                            $row.find('.progress-text-' + id).text(prog + '%');

                            // Note text
                            $row.find('.note-text-' + id).text(note || '-').attr('title',
                                note || '');

                            // Data attributes
                            $row.find('.btn-update-progress').data('progress', prog).attr(
                                'data-progress', prog);

                            // Status Badge
                            let statusBadge = '';
                            if (status === 'Selesai') {
                                statusBadge =
                                    '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="ri-checkbox-circle-line me-1"></i>Selesai</span>';
                            } else if (status === 'Terlambat') {
                                statusBadge =
                                    '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="ri-alert-line me-1"></i>Terlambat</span>';
                            } else {
                                statusBadge =
                                    '<span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="ri-time-line me-1"></i>On Progress</span>';
                            }
                            $row.find('.status-col-' + id).html(statusBadge);

                            // Sinkronisasi data ke DataTables
                            if (projectTable) {
                                projectTable.row($row).invalidate().draw(false);
                                updateStatsBadges();
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message ||
                                    'Terjadi kesalahan saat memperbarui progress.'
                            });
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Gagal memproses request ke server.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(origText);
                    }
                });
            });

            // ── View History via Async jQuery AJAX ──
            $(document).on('click', '.btn-view-history', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                $('#history_project_title').text(nama);
                $('#history_loading').removeClass('d-none');
                $('#history_container').addClass('d-none').empty();

                $('#modalHistory').modal('show');

                $.ajax({
                    url: `{{ url('utility/project') }}/${id}/history`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $('#history_loading').addClass('d-none');
                        const $container = $('#history_container').removeClass('d-none');

                        if (!res.logs || res.logs.length === 0) {
                            $container.html(`
                        <div class="text-center py-4 text-muted">
                            <i class="ri-time-line fs-28 d-block mb-1"></i>
                            Belum ada riwayat update progress untuk project ini.
                        </div>
                    `);
                            return;
                        }

                        let html = '<div class="list-group list-group-flush">';
                        $.each(res.logs, function(i, log) {
                            const diff = log.progress_baru - log.progress_sebelumnya;
                            const diffBadge = diff > 0 ?
                                `<span class="badge bg-success-subtle text-success">+${diff}%</span>` :
                                (diff < 0 ?
                                    `<span class="badge bg-danger-subtle text-danger">${diff}%</span>` :
                                    `<span class="badge bg-light text-muted">0%</span>`
                                );

                            html += `
                        <div class="list-group-item px-0 py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary-subtle text-primary">${log.progress_sebelumnya}% &rarr; ${log.progress_baru}%</span>
                                    ${diffBadge}
                                </div>
                                <span class="text-muted fs-12"><i class="ri-calendar-line me-1"></i>${log.tanggal} ${log.jam}</span>
                            </div>
                            <div class="text-dark fs-13 mb-1 mt-2">${log.catatan}</div>
                            <small class="text-muted"><i class="ri-user-line me-1"></i>Diupdate oleh: <strong>${log.user_nama}</strong></small>
                        </div>
                    `;
                        });
                        html += '</div>';
                        $container.html(html);
                    },
                    error: function() {
                        $('#history_loading').addClass('d-none');
                        $('#history_container').removeClass('d-none').html(
                            '<div class="alert alert-danger mb-0">Gagal memuat data riwayat project. Silakan coba kembali.</div>'
                        );
                    }
                });
            });

            // ── Delete Project via Async jQuery AJAX ──
            $(document).on('click', '.btn-delete-project', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus Project?',
                    text: `Anda yakin ingin menghapus project "${nama}"? Data riwayat progress juga akan terhapus.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('utility/project') }}/${id}`,
                            type: 'DELETE',
                            dataType: 'json',
                            success: function(res) {
                                if (res.success) {
                                    if (projectTable) {
                                        projectTable.row($('#row-project-' + id))
                                            .remove().draw(false);
                                        updateStatsBadges();
                                    } else {
                                        $('#row-project-' + id).fadeOut(300,
                                            function() {
                                                $(this).remove();
                                            });
                                    }
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: res.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire('Gagal!', res.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                let msg = 'Gagal menghapus project.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });

            // ── Clear Import File Input ──
            $('#btnClearFile').on('click', function() {
                $('#import_excel_file').val('');
            });

            // ── Submit Import Form via AJAX ──
            $('#formImportExcel').on('submit', function(e) {
                e.preventDefault();

                const fileInput = document.getElementById('import_excel_file');
                if (!fileInput.files || fileInput.files.length === 0) {
                    Swal.fire('Perhatian', 'Silakan pilih file Excel terlebih dahulu.', 'warning');
                    return;
                }

                const formData = new FormData(this);
                const $btn = $('#btnSubmitImport');
                const origText = $btn.html();

                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Mengupload...');
                $('#importLoadingState').removeClass('d-none');

                $.ajax({
                    url: '{{ route('utility.project.import') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(res) {
                        $('#importLoadingState').addClass('d-none');
                        $btn.prop('disabled', false).html(origText);

                        if (res.success) {
                            $('#modalImportExcel').modal('hide');
                            $('#formImportExcel')[0].reset();

                            Swal.fire({
                                icon: 'success',
                                title: 'Migrasi Berhasil!',
                                text: res.message,
                                confirmButtonColor: '#198754',
                                confirmButtonText: 'OK, Lihat Data'
                            }).then(() => {
                                loadProjectsData();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Import Data',
                                text: res.message ||
                                    'Terjadi kesalahan saat memproses data Excel.'
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#importLoadingState').addClass('d-none');
                        $btn.prop('disabled', false).html(origText);

                        let msg = 'Gagal memproses file Excel di server.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errs = Object.values(xhr.responseJSON.errors).flat();
                            if (errs.length > 0) msg = errs.join('<br>');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error Import',
                            html: msg
                        });
                    }
                });
            });
        });
    </script>
@endsection
