@extends('layouts.app')

@section('title', 'Rekap Data Compressor')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%); border-radius: 12px;">
                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="text-white fw-bold mb-1">
                                    <i class="ri-database-2-line text-success me-2"></i>
                                    Compressor - Rekap Data
                                </h4>
                                <p class="text-white-50 mb-0">
                                    Daftar log harian compressor
                                </p>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('compressor.index') }}" class="btn btn-warning btn-sm rounded-pill px-3">
                                    <i class="ri-add-line me-1"></i> Input
                                </a>

                                <button class="btn btn-outline-light btn-sm rounded-pill px-3" id="btnExport">
                                    <i class="ri-download-2-line me-1"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Container for Collected Weeks (Drafts) -->
            @if (Auth::user()->jabatan === 'foreman')
                <div class="card shadow-sm mt-3" id="collectedCard" style="display: none;">
                    <div class="card-header bg-soft-warning border-0">
                        <h5 class="card-title mb-0 text-warning-emphasis fw-bold">
                            <i class="ri-history-line me-2"></i> Draft Data Mingguan Terkumpul
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Minggu Ke-</th>
                                        <th>Periode</th>
                                        <th class="text-center pe-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="collectedTbody">
                                    <!-- Dynamic rows will be injected here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <div class="row g-3 mb-4 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Filter Bulan</label>
                            <input type="month" id="filter_bulan" class="form-control" value="{{ date('Y-m') }}">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" onclick="loadData()">
                                <i class="ri-filter-3-line me-1"></i> Filter
                            </button>
                        </div>
                        <div class="col-md-7 text-end">
                            <div id="weeklyStatusContainer" class="d-inline-block">
                                <!-- Status Approval Mingguan Terpilih akan muncul di sini -->
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle text-nowrap" id="tableData">
                            <thead class="table-light align-middle">
                                <tr>
                                    <th rowspan="2">Tanggal</th>
                                    <th rowspan="2">Jam</th>
                                    <th colspan="4" class="text-center text-primary">Pressure Outlet (Bar)</th>
                                    <th colspan="3" class="text-center text-success">Element Outlet (°C)</th>
                                    <th rowspan="2">Load (%)</th>
                                    <th rowspan="2">Status Approval</th>
                                    <th rowspan="2" class="text-center">Aksi</th>
                                </tr>
                                <tr class="text-center">
                                    <th>1</th>
                                    <th>2</th>
                                    <th>3</th>
                                    <th>4</th>
                                    <th>1</th>
                                    <th>2</th>
                                    <th>4</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyData">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div id="paginationInfo"></div>
                        <nav>
                            <ul class="pagination mb-0" id="paginationLinks"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">Detail Log Compressor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Content via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Edit Log Compressor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEdit">
                        @csrf
                        <input type="hidden" name="id" id="edit_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jam</label>
                                <select name="jam" id="edit_jam" class="form-select" required>
                                    <option value="08:00">08:00</option>
                                    <option value="12:00">12:00</option>
                                    <option value="16:00">16:00</option>
                                    <option value="20:00">20:00</option>
                                    <option value="00:00">00:00 (24:00)</option>
                                    <option value="04:00">04:00</option>
                                </select>
                            </div>

                            <div class="col-12 mt-3"><span class="badge bg-soft-primary text-primary">Pressure Outlet
                                    (Bar)</span></div>
                            <div class="col-md-3">
                                <label class="form-label">Outlet 1</label>
                                <input type="number" step="0.01" name="pressure_outlet_1" id="edit_pressure_1"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Outlet 2</label>
                                <input type="number" step="0.01" name="pressure_outlet_2" id="edit_pressure_2"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Outlet 3</label>
                                <input type="number" step="0.01" name="pressure_outlet_3" id="edit_pressure_3"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Outlet 4</label>
                                <input type="number" step="0.01" name="pressure_outlet_4" id="edit_pressure_4"
                                    class="form-control">
                            </div>

                            <div class="col-12 mt-3"><span class="badge bg-soft-success text-success">Element Outlet
                                    (°C)</span></div>
                            <div class="col-md-4">
                                <label class="form-label">Element 1</label>
                                <input type="number" step="0.01" name="element_outlet_1" id="edit_element_1"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Element 2</label>
                                <input type="number" step="0.01" name="element_outlet_2" id="edit_element_2"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Element 4</label>
                                <input type="number" step="0.01" name="element_outlet_4" id="edit_element_4"
                                    class="form-control">
                            </div>

                            <div class="col-md-4 mt-3">
                                <label class="form-label">Load Percent (%)</label>
                                <input type="number" step="0.01" name="load_percent" id="edit_load"
                                    class="form-control">
                            </div>

                            <div class="col-12 mt-3"><span class="badge bg-soft-info text-info">Running Hour</span></div>
                            <div class="col-md-3">
                                <label class="form-label">RH 1</label>
                                <input type="number" step="0.01" name="running_hour_1" id="edit_rh_1"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">RH 2</label>
                                <input type="number" step="0.01" name="running_hour_2" id="edit_rh_2"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">RH 3</label>
                                <input type="number" step="0.01" name="running_hour_3" id="edit_rh_3"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">RH 4</label>
                                <input type="number" step="0.01" name="running_hour_4" id="edit_rh_4"
                                    class="form-control">
                            </div>

                            <div class="col-12 mt-3"><span class="badge bg-soft-primary text-primary">Loaded Hour</span>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Loaded 1</label>
                                <input type="number" step="0.01" name="loaded_hour_1" id="edit_loaded_1"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Loaded 2</label>
                                <input type="number" step="0.01" name="loaded_hour_2" id="edit_loaded_2"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Loaded 3</label>
                                <input type="number" step="0.01" name="loaded_hour_3" id="edit_loaded_3"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Loaded 4</label>
                                <input type="number" step="0.01" name="loaded_hour_4" id="edit_loaded_4"
                                    class="form-control">
                            </div>

                            <div class="col-12 mt-3"><span class="badge bg-soft-dark text-dark">Motor Start</span></div>
                            <div class="col-md-3">
                                <label class="form-label">Start 1</label>
                                <input type="number" step="0.01" name="motor_start_1" id="edit_start_1"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start 2</label>
                                <input type="number" step="0.01" name="motor_start_2" id="edit_start_2"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start 3</label>
                                <input type="number" step="0.01" name="motor_start_3" id="edit_start_3"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start 4</label>
                                <input type="number" step="0.01" name="motor_start_4" id="edit_start_4"
                                    class="form-control">
                            </div>

                            <div class="col-12 mt-3"><span class="badge bg-soft-secondary text-secondary">Lain-lain</span>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Accumulated Vol.</label>
                                <input type="number" step="0.01" name="accumulated_volume" id="edit_acc_vol"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Temp Comp IR</label>
                                <input type="number" step="0.01" name="temperature_comp_ir" id="edit_temp_ir"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pressure In</label>
                                <input type="number" step="0.01" name="pressure_in" id="edit_p_in"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pressure Out</label>
                                <input type="number" step="0.01" name="pressure_out" id="edit_p_out"
                                    class="form-control">
                            </div>

                            <div class="col-12 mt-3"><span class="badge bg-soft-warning text-warning">Suhu Dryer
                                    (°C)</span></div>
                            <div class="col-md-4">
                                <label class="form-label">Dryer TR15</label>
                                <input type="number" step="0.01" name="suhu_dryer_tr15" id="edit_dryer_tr"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Dryer FX250</label>
                                <input type="number" step="0.01" name="suhu_dryer_fx250" id="edit_dryer_fx"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Dryer IR</label>
                                <input type="number" step="0.01" name="suhu_dryer_ir" id="edit_dryer_ir"
                                    class="form-control">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="submitUpdate()">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Collected -->
    <div class="modal fade" id="modalCollectedDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white" id="modalCollectedDetailTitle">Detail Data Mingguan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th class="text-center">Pr. Outlet (1/2/3/4)</th>
                                    <th class="text-center">El. Outlet (1/2/4)</th>
                                    <th class="text-center">Load (%)</th>
                                </tr>
                            </thead>
                            <tbody id="modalCollectedDetailTbody">
                                <!-- Data will be injected via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Submit Weekly -->
    <div class="modal fade" id="modalSubmitWeekly" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Kirim Approval Mingguan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formSubmitWeekly">
                        @csrf
                        <input type="hidden" name="week" id="sw_week">
                        <input type="hidden" name="bulan" id="sw_bulan">
                        <input type="hidden" name="tahun" id="sw_tahun">

                        <div class="mb-3">
                            <label class="form-label">Pilih Supervisor</label>
                            <select name="supervisor_id" id="select_supervisor" class="form-select" required>
                                <option value="">-- Pilih Supervisor --</option>
                            </select>
                        </div>

                        <p class="text-muted small">
                            * Dengan menekan kirim, data pada minggu ini akan dikunci dan dikirim ke Supervisor.
                        </p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning" onclick="processSubmitWeekly()">Kirim
                        Sekarang</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Export Excel -->
    <div class="modal fade" id="modalExport" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white"><i class="ri-file-excel-2-line me-1"></i> Export Excel Compressor
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formExport">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Minggu</label>
                                <select name="week" class="form-select">
                                    <option value="">-- Semua --</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Bulan</label>
                                <select name="bulan" class="form-select">
                                    <option value="">-- Semua --</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tahun</label>
                                <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}">
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 mb-0" style="font-size: 0.85rem;">
                            <i class="ri-information-line me-1"></i> Data akan diekspor menggunakan template Excel yang
                            tersedia di sistem.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success px-4" id="btnExportConfirm">
                        <i class="ri-download-cloud-2-line me-1"></i> Download Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const API_URL = "{{ route('compressor.get-data') }}";
        const EXPORT_URL = "{{ route('compressor.export') }}";
        let currentPage = 1;

        function loadData(page = 1) {
            currentPage = page;
            let bulan = $('#filter_bulan').val();

            $.ajax({
                url: API_URL,
                type: "GET",
                data: {
                    bulan: bulan,
                    page: page
                },
                success: function(res) {
                    let html = '';
                    const formatNum = (v) => v ? Number(v) : '-';
                    res.data.forEach(item => {
                        let statusBadge = '';
                        if (item.approval_status == 'draft') statusBadge =
                            '<span class="badge bg-warning">Draft</span>';
                        else if (item.approval_status == 'submitted') statusBadge =
                            '<span class="badge bg-info">Submitted</span>';
                        else if (item.approval_status == 'approved_foreman') statusBadge =
                            '<span class="badge bg-primary">Approve Foreman</span>';
                        else if (item.approval_status == 'approved_supervisor') statusBadge =
                            '<span class="badge bg-success">Approved</span>';
                        else if (item.approval_status == 'rejected') statusBadge =
                            '<span class="badge bg-danger">Rejected</span>';
                        else statusBadge = '<span class="badge bg-secondary">-</span>';

                        html += `
                            <tr>
                                <td>${item.tanggal}</td>
                                <td>${item.jam}</td>
                                <td class="text-center">${formatNum(item.pressure_outlet_1)}</td>
                                <td class="text-center">${formatNum(item.pressure_outlet_2)}</td>
                                <td class="text-center">${formatNum(item.pressure_outlet_3)}</td>
                                <td class="text-center">${formatNum(item.pressure_outlet_4)}</td>
                                <td class="text-center">${formatNum(item.element_outlet_1)}</td>
                                <td class="text-center">${formatNum(item.element_outlet_2)}</td>
                                <td class="text-center">${formatNum(item.element_outlet_4)}</td>
                                <td class="text-center">${formatNum(item.load_percent)}</td>
                                <td>${statusBadge}</td>
                                <td class="text-center">
                                    <div>
                                        <button class="btn btn-sm btn-info" onclick="showDetail(${item.id})" title="Detail">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary" onclick="editData(${item.id})" title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteData(${item.id})" title="Hapus">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    if (res.data.length == 0) {
                        html = '<tr><td colspan="12" class="text-center py-4">Data tidak ditemukan</td></tr>';
                    }

                    $('#tbodyData').html(html);
                    renderPagination(res.pagination);

                    // Update Status Approval Mingguan Terpilih (berdasarkan data pertama)
                    if (res.data.length > 0) {
                        let first = res.data[0];
                        let d = moment(first.tanggal);
                        let week = Math.ceil(d.date() / 7);
                        let statusText = '';
                        if (first.approval_status === 'draft') statusText =
                            '<span class="badge bg-warning p-2"><i class="ri-time-line me-1"></i> Minggu ini: Draft</span>';
                        else if (first.approval_status === 'submitted') statusText =
                            '<span class="badge bg-info p-2"><i class="ri-loader-4-line me-1"></i> Minggu ini: Menunggu Approval</span>';
                        else if (first.approval_status === 'approved_supervisor') statusText =
                            '<span class="badge bg-success p-2"><i class="ri-checkbox-circle-line me-1"></i> Minggu ini: Approved</span>';
                        else if (first.approval_status === 'rejected') statusText =
                            '<span class="badge bg-danger p-2"><i class="ri-close-circle-line me-1"></i> Minggu ini: Rejected</span>';

                        $('#weeklyStatusContainer').html(statusText);
                    } else {
                        $('#weeklyStatusContainer').empty();
                    }
                }
            });

            loadCollected();
        }

        function loadCollected() {
            let tbody = $('#collectedTbody');
            let card = $('#collectedCard');
            if (tbody.length == 0) return;

            $.ajax({
                url: "{{ route('compressor.get-collected') }}",
                type: "GET",
                success: function(res) {
                    tbody.empty();
                    if (res.results && res.results.length > 0) {
                        card.show();
                        window.collectedData = res.results;
                        res.results.forEach((weekData, index) => {
                            let app = weekData.approval;
                            let trHtml = `
                            <tr>
                                <td class="ps-3 fw-medium">Minggu ke-${app.week}</td>
                                <td>${moment(app.tgl_awal).format('DD MMM')} - ${moment(app.tgl_akhir).format('DD MMM YYYY')}</td>
                                <td class="text-center pe-3">
                                    <button class="btn btn-sm btn-info" onclick="showCollectedDetail(${index})">
                                        <i class="ri-eye-line me-1"></i> Detail
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="openSubmitWeekly(${app.week}, ${app.bulan}, ${app.tahun})">
                                        <i class="ri-send-plane-fill me-1"></i> Kirim Approval
                                    </button>
                                </td>
                            </tr>
                        `;
                            tbody.append(trHtml);
                        });
                    } else {
                        card.hide();
                    }
                }
            });
        }

        function showCollectedDetail(index) {
            let weekData = window.collectedData[index];
            let app = weekData.approval;
            let dataRows = '';
            const formatNum = (v) => v ? Number(v) : '-';

            weekData.data.forEach(item => {
                dataRows += `
                <tr>
                    <td>${item.tanggal}</td>
                    <td>${item.jam}</td>
                    <td class="text-center">${formatNum(item.pressure_outlet_1)}/${formatNum(item.pressure_outlet_2)}/${formatNum(item.pressure_outlet_3)}/${formatNum(item.pressure_outlet_4)}</td>
                    <td class="text-center">${formatNum(item.element_outlet_1)}/${formatNum(item.element_outlet_2)}/${formatNum(item.element_outlet_4)}</td>
                    <td class="text-center">${formatNum(item.load_percent)}</td>
                </tr>
            `;
            });

            $('#modalCollectedDetailTitle').text(
                `Detail Data Minggu ke-${app.week} (${moment(app.tgl_awal).format('DD MMM')} - ${moment(app.tgl_akhir).format('DD MMM YYYY')})`
            );
            $('#modalCollectedDetailTbody').html(dataRows);
            $('#modalCollectedDetail').modal('show');
        }

        function openSubmitWeekly(week, bulan, tahun) {
            $('#sw_week').val(week);
            $('#sw_bulan').val(bulan);
            $('#sw_tahun').val(tahun);

            // Load approvers
            $.get('/api/utility/users/approvers', function(data) {
                // Isi dropdown Supervisor — dari data.user (jabatan supervisor)
                const supervisorList = data.user ?? [];
                let supervisorOpts = '<option value="">— Pilih Supervisor —</option>';
                supervisorList.forEach(function(u) {
                    supervisorOpts += `<option value="${u.id}">${u.username}</option>`;
                });
                $('#select_supervisor').html(supervisorOpts);
            }).fail(function() {
                $('#select_supervisor').html('<option value="">Gagal memuat data</option>');
                toastr.error('Gagal memuat daftar approver');
            });

            $('#modalSubmitWeekly').modal('show');
        }

        function processSubmitWeekly() {
            let data = $('#formSubmitWeekly').serialize();

            if (!$('#select_supervisor').val()) {
                Swal.fire('Peringatan', 'Harap pilih Supervisor', 'warning');
                return;
            }

            $.post("{{ route('compressor.submit-weekly') }}", data, function(res) {
                Swal.fire('Berhasil!', res.message, 'success');
                $('#modalSubmitWeekly').modal('hide');
                loadData(currentPage);
            }).fail(function(xhr) {
                let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                Swal.fire('Gagal!', msg, 'error');
            });
        }

        function showDetail(id) {
            $.get("{{ url('utility/compressor/show') }}/" + id, function(res) {
                let data = res.data;
                const formatNum = (v) => v ? Number(v) : '-';
                let html = `
                    <div class="row g-3">
                        <div class="col-4"><strong>Tanggal:</strong> ${data.tanggal}</div>
                        <div class="col-4"><strong>Jam:</strong> ${data.jam}</div>
                        <div class="col-4"><strong>Created By:</strong> ${data.created_by?.username || '-'}</div>
                        <div class="col-12"><hr></div>
                        <div class="col-6"><strong>Pressure Outlet 1:</strong> ${formatNum(data.pressure_outlet_1)} Bar</div>
                        <div class="col-6"><strong>Pressure Outlet 2:</strong> ${formatNum(data.pressure_outlet_2)} Bar</div>
                        <div class="col-6"><strong>Pressure Outlet 3:</strong> ${formatNum(data.pressure_outlet_3)} Bar</div>
                        <div class="col-6"><strong>Pressure Outlet 4:</strong> ${formatNum(data.pressure_outlet_4)} Bar</div>
                        <div class="col-12 mt-2"><span class="badge bg-soft-success text-success">Element Outlet (°C)</span></div>
                        <div class="col-6 mt-1"><strong>Element Outlet 1:</strong> ${formatNum(data.element_outlet_1)} °C</div>
                        <div class="col-6 mt-1"><strong>Element Outlet 2:</strong> ${formatNum(data.element_outlet_2)} °C</div>
                        <div class="col-6 mt-1"><strong>Element Outlet 4:</strong> ${formatNum(data.element_outlet_4)} °C</div>
                        <div class="col-6 mt-1"><strong>Load %:</strong> ${formatNum(data.load_percent)} %</div>
                        
                        <div class="col-12 mt-2"><span class="badge bg-soft-info text-info">Running Hour</span></div>
                        <div class="col-6 mt-1"><strong>RH 1:</strong> ${formatNum(data.running_hour_1)}</div>
                        <div class="col-6 mt-1"><strong>RH 2:</strong> ${formatNum(data.running_hour_2)}</div>
                        <div class="col-6 mt-1"><strong>RH 3:</strong> ${formatNum(data.running_hour_3)}</div>
                        <div class="col-6 mt-1"><strong>RH 4:</strong> ${formatNum(data.running_hour_4)}</div>

                        <div class="col-12 mt-2"><span class="badge bg-soft-primary text-primary">Loaded Hour</span></div>
                        <div class="col-6 mt-1"><strong>Loaded 1:</strong> ${formatNum(data.loaded_hour_1)}</div>
                        <div class="col-6 mt-1"><strong>Loaded 2:</strong> ${formatNum(data.loaded_hour_2)}</div>
                        <div class="col-6 mt-1"><strong>Loaded 3:</strong> ${formatNum(data.loaded_hour_3)}</div>
                        <div class="col-6 mt-1"><strong>Loaded 4:</strong> ${formatNum(data.loaded_hour_4)}</div>

                        <div class="col-12 mt-2"><span class="badge bg-soft-dark text-dark">Motor Start</span></div>
                        <div class="col-6 mt-1"><strong>Start 1:</strong> ${formatNum(data.motor_start_1)}</div>
                        <div class="col-6 mt-1"><strong>Start 2:</strong> ${formatNum(data.motor_start_2)}</div>
                        <div class="col-6 mt-1"><strong>Start 3:</strong> ${formatNum(data.motor_start_3)}</div>
                        <div class="col-6 mt-1"><strong>Start 4:</strong> ${formatNum(data.motor_start_4)}</div>

                        <div class="col-12 mt-2"><span class="badge bg-soft-secondary text-secondary">Lain-lain</span></div>
                        <div class="col-6 mt-1"><strong>Accumulated Volume:</strong> ${formatNum(data.accumulated_volume)}</div>
                        <div class="col-6 mt-1"><strong>Temp Comp IR:</strong> ${formatNum(data.temperature_comp_ir)} °C</div>
                        <div class="col-6 mt-1"><strong>Pressure In:</strong> ${formatNum(data.pressure_in)} Bar</div>
                        <div class="col-6 mt-1"><strong>Pressure Out:</strong> ${formatNum(data.pressure_out)} Bar</div>

                        <div class="col-12 mt-2"><span class="badge bg-soft-warning text-warning">Suhu Dryer (°C)</span></div>
                        <div class="col-4 mt-1"><strong>TR15:</strong> ${formatNum(data.suhu_dryer_tr15)} °C</div>
                        <div class="col-4 mt-1"><strong>FX250:</strong> ${formatNum(data.suhu_dryer_fx250)} °C</div>
                        <div class="col-4 mt-1"><strong>IR:</strong> ${formatNum(data.suhu_dryer_ir)} °C</div>
                    </div>
                `;
                $('#detailContent').html(html);
                $('#modalDetail').modal('show');
            });
        }

        function editData(id) {
            $.get("{{ url('utility/compressor/show') }}/" + id, function(res) {
                let data = res.data;
                const formatNum = (v) => v ? Number(v) : '';

                $('#edit_id').val(data.id);
                $('#edit_tanggal').val(data.tanggal.substring(0, 10));
                $('#edit_jam').val(data.jam.substring(0, 5));

                // Set fields with formatNum
                $('#edit_pressure_1').val(formatNum(data.pressure_outlet_1));
                $('#edit_pressure_2').val(formatNum(data.pressure_outlet_2));
                $('#edit_pressure_3').val(formatNum(data.pressure_outlet_3));
                $('#edit_pressure_4').val(formatNum(data.pressure_outlet_4));
                $('#edit_element_1').val(formatNum(data.element_outlet_1));
                $('#edit_element_2').val(formatNum(data.element_outlet_2));
                $('#edit_element_4').val(formatNum(data.element_outlet_4));
                $('#edit_load').val(formatNum(data.load_percent));
                $('#edit_rh_1').val(formatNum(data.running_hour_1));
                $('#edit_rh_2').val(formatNum(data.running_hour_2));
                $('#edit_rh_3').val(formatNum(data.running_hour_3));
                $('#edit_rh_4').val(formatNum(data.running_hour_4));
                $('#edit_loaded_1').val(formatNum(data.loaded_hour_1));
                $('#edit_loaded_2').val(formatNum(data.loaded_hour_2));
                $('#edit_loaded_3').val(formatNum(data.loaded_hour_3));
                $('#edit_loaded_4').val(formatNum(data.loaded_hour_4));
                $('#edit_start_1').val(formatNum(data.motor_start_1));
                $('#edit_start_2').val(formatNum(data.motor_start_2));
                $('#edit_start_3').val(formatNum(data.motor_start_3));
                $('#edit_start_4').val(formatNum(data.motor_start_4));
                $('#edit_acc_vol').val(formatNum(data.accumulated_volume));
                $('#edit_temp_ir').val(formatNum(data.temperature_comp_ir));
                $('#edit_p_in').val(formatNum(data.pressure_in));
                $('#edit_p_out').val(formatNum(data.pressure_out));
                $('#edit_dryer_tr').val(formatNum(data.suhu_dryer_tr15));
                $('#edit_dryer_fx').val(formatNum(data.suhu_dryer_fx250));
                $('#edit_dryer_ir').val(formatNum(data.suhu_dryer_ir));

                $('#modalEdit').modal('show');
            });
        }

        function submitUpdate() {
            let id = $('#edit_id').val();

            let hasValue = false;
            $('#formEdit').find('input[type="number"]').each(function() {
                if ($(this).val().trim() !== '') {
                    hasValue = true;
                    return false;
                }
            });

            if (!hasValue) {
                Swal.fire('Peringatan', 'Minimal harus ada 1 nilai data teknis yang diisi sebelum update.', 'warning');
                return;
            }

            let data = $('#formEdit').serialize();

            $.post("{{ url('utility/compressor/update') }}/" + id, data, function(res) {
                Swal.fire('Berhasil!', res.message, 'success');
                $('#modalEdit').modal('hide');
                loadData(currentPage);
            });
        }

        function renderPagination(pagination, callback) {
            let html = '';
            if (pagination && pagination.last_page > 1) {
                html += `<li class="page-item ${pagination.current_page == 1 ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="loadData(${pagination.current_page - 1})">Prev</a>
            </li>`;

                for (let i = 1; i <= pagination.last_page; i++) {
                    if (i == 1 || i == pagination.last_page || (i >= pagination.current_page - 2 && i <= pagination
                            .current_page + 2)) {
                        html += `<li class="page-item ${pagination.current_page == i ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="loadData(${i})">${i}</a>
                    </li>`;
                    } else if (i == pagination.current_page - 3 || i == pagination.current_page + 3) {
                        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                }

                html += `<li class="page-item ${pagination.current_page == pagination.last_page ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="loadData(${pagination.current_page + 1})">Next</a>
            </li>`;
            }
            $('#paginationLinks').html(html);
            if (pagination) {
                $('#paginationInfo').html(
                    `Showing <b>${pagination.total > 0 ? (pagination.current_page - 1) * pagination.per_page + 1 : 0}</b> to <b>${Math.min(pagination.current_page * pagination.per_page, pagination.total)}</b> of <b>${pagination.total}</b> entries`
                );
            }
        }

        function deleteData(id) {
            Swal.fire({
                title: 'Hapus data?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('utility/compressor/destroy') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            if (res.status == 200) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: res.message,
                                    icon: 'success',
                                    timer: 1000,
                                    showConfirmButton: false
                                });
                            }
                            loadData(currentPage);
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            loadData();

            // ── Export ──
            $('#btnExport').on('click', function() {
                $('#modalExport').modal('show');
            });

            $('#btnExportConfirm').on('click', function() {
                const week = $('select[name="week"]', '#formExport').val();
                const bulan = $('select[name="bulan"]', '#formExport').val();
                const tahun = $('input[name="tahun"]', '#formExport').val();
                window.location.href = `{{ route('compressor.export') }}?week=${week}&bulan=${bulan}&tahun=${tahun}`;
                $('#modalExport').modal('hide');
            });
        });
    </script>
@endsection
