@extends('layouts.app')

@section('title', 'Rekap Data Analisis Utility')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); border-radius: 12px;">
                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="text-white fw-bold mb-1">
                                    <i class="ri-database-2-line text-warning me-2"></i>
                                    Analisis Utility - Rekap Data
                                </h4>
                                <p class="text-white-50 mb-0">
                                    Daftar log harian checklist Analisis Utility
                                </p>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('analisis-utility.index') }}"
                                    class="btn btn-warning btn-sm rounded-pill px-3">
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

            <!-- Container for Collected Months (Drafts) -->
            @if (Auth::user()->jabatan === 'foreman')
                <div class="card shadow-sm mt-3" id="collectedCard" style="display: none;">
                    <div class="card-header bg-soft-warning border-0">
                        <h5 class="card-title mb-0 text-warning-emphasis fw-bold">
                            <i class="ri-history-line me-2"></i> Draft Data Laporan Terkumpul
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Bulan</th>
                                        <th>Tahun</th>
                                        <th class="text-center pe-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="collectedTbody">
                                    <!-- Dynamic rows -->
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
                            <div id="monthlyStatusContainer" class="d-inline-block">
                                <!-- Status Approval Bulanan Terpilih -->
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-nowrap" id="tableData">
                            <thead class="table-light align-middle text-center">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Checklist OK</th>
                                    <th>Checklist NOK</th>
                                    <th>Belum Diisi / Kosong</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
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
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">Detail Analisis Utility</h5>
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
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Edit Analisis Utility</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEdit">
                        @csrf
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                        </div>

                        <!-- Segmented control template wrapper function -->
                        @php
                            if (!function_exists('renderAnalisisEditChecklistItem')) {
                                function renderAnalisisEditChecklistItem($fieldName, $labelText)
                                {
                                    return '
                                    <div class="edit-item d-flex justify-content-between align-items-center flex-wrap py-2 border-bottom">
                                        <div class="fw-medium text-dark flex-grow-1 pe-2 mb-2 mb-sm-0 small">
                                            ' .
                                        $labelText .
                                        '
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <input type="radio" class="btn-check" name="' .
                                        $fieldName .
                                        '" id="edit_' .
                                        $fieldName .
                                        '_empty" value="">
                                            <label class="btn btn-outline-secondary px-3" for="edit_' .
                                        $fieldName .
                                        '_empty">Kosong</label>

                                            <input type="radio" class="btn-check" name="' .
                                        $fieldName .
                                        '" id="edit_' .
                                        $fieldName .
                                        '_ok" value="OK">
                                            <label class="btn btn-outline-success px-3" for="edit_' .
                                        $fieldName .
                                        '_ok">OK</label>

                                            <input type="radio" class="btn-check" name="' .
                                        $fieldName .
                                        '" id="edit_' .
                                        $fieldName .
                                        '_nok" value="NOK">
                                            <label class="btn btn-outline-danger px-3" for="edit_' .
                                        $fieldName .
                                        '_nok">NOK</label>
                                        </div>
                                    </div>
                                    ';
                                }
                            }
                        @endphp

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light p-3 mb-3">
                                    <h6 class="fw-bold text-primary mb-2">pH Air</h6>
                                    {!! renderAnalisisEditChecklistItem('ph_fw_storage', 'Masukkan pH FW Storage') !!}
                                    {!! renderAnalisisEditChecklistItem('ph_ws_storage', 'Masukkan pH WS Storage') !!}
                                    {!! renderAnalisisEditChecklistItem('ph_ro_storage', 'Masukkan pH RO Storage') !!}
                                    {!! renderAnalisisEditChecklistItem('ph_in_mmf', 'Masukkan pH In MMF') !!}
                                    {!! renderAnalisisEditChecklistItem('ph_buffer_tank_ws', 'Masukkan pH Buffer Tank WS') !!}
                                    {!! renderAnalisisEditChecklistItem('ph_outlet_ws', 'Masukkan pH Outlet WS') !!}
                                    {!! renderAnalisisEditChecklistItem('ph_menara_ws', 'Masukkan pH Menara WS') !!}
                                    {!! renderAnalisisEditChecklistItem('ph_depo_lt1', 'Masukkan pH Depo Lt.1') !!}
                                    {!! renderAnalisisEditChecklistItem('ph_depo_lt2', 'Masukkan pH Depo Lt.2') !!}
                                    {!! renderAnalisisEditChecklistItem('ph_cooling_tower', 'Masukkan pH Cooling Tower') !!}
                                    {!! renderAnalisisEditChecklistItem('ph_boiler', 'Masukkan pH Boiler') !!}
                                    {!! renderAnalisisEditChecklistItem('ph_outlet_ws_2', 'Masukkan pH Outlet WS (2)') !!}
                                </div>

                                <div class="card border-0 bg-light p-3 mb-3">
                                    <h6 class="fw-bold text-danger mb-2">Turbidity & Chlorine</h6>
                                    {!! renderAnalisisEditChecklistItem('turbidity_in_mmf', 'Masukkan Turbidity IN MMF') !!}
                                    {!! renderAnalisisEditChecklistItem('turbidity_out_mmf', 'Masukkan Turbidity Out MMF') !!}
                                    {!! renderAnalisisEditChecklistItem('turbidity_cooling_tower', 'Masukkan Turbidity Cooling Tower') !!}
                                    {!! renderAnalisisEditChecklistItem('chlorine_mmf', 'Masukkan Chlorine MMF') !!}
                                    {!! renderAnalisisEditChecklistItem('chlorine_menara', 'Masukkan Chlorine Menara') !!}
                                    {!! renderAnalisisEditChecklistItem('chlorine_depo_lt1', 'Masukkan Chlorine Depo LT.1') !!}
                                    {!! renderAnalisisEditChecklistItem('chlorine_depo_lt2', 'Masukkan Chlorine Depo LT.2') !!}
                                    {!! renderAnalisisEditChecklistItem('chlorine_daily_tank_dissolver', 'Masukkan Chlorine Daily Tank Dissolver') !!}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 bg-light p-3 mb-3">
                                    <h6 class="fw-bold text-success mb-2">TDS Air</h6>
                                    {!! renderAnalisisEditChecklistItem('tds_fw_storage', 'Masukkan TDS FW Storage') !!}
                                    {!! renderAnalisisEditChecklistItem('tds_ws_storage', 'Masukkan TDS WS Storage') !!}
                                    {!! renderAnalisisEditChecklistItem('tds_ro_storage', 'Masukkan TDS RO Storage') !!}
                                    {!! renderAnalisisEditChecklistItem('tds_in_mmf', 'Masukkan TDS In MMF') !!}
                                    {!! renderAnalisisEditChecklistItem('tds_out_ro', 'Masukkan TDS Out RO') !!}
                                    {!! renderAnalisisEditChecklistItem('tds_menara_ws', 'Masukkan TDS menara WS') !!}
                                    {!! renderAnalisisEditChecklistItem('tds_daily_tank_dissolver', 'Masukkan TDS daily Tank dissolver') !!}
                                    {!! renderAnalisisEditChecklistItem('tds_depo_lt1', 'Masukkan TDS Depo Lt.1') !!}
                                    {!! renderAnalisisEditChecklistItem('tds_depo_lt2', 'Masukkan TDS Depo Lt.2') !!}
                                    {!! renderAnalisisEditChecklistItem('tds_cooling_tower', 'Masukkan TDS Cooling Tower') !!}
                                    {!! renderAnalisisEditChecklistItem('tds_boiler', 'Masukkan TDS Boiler') !!}
                                </div>

                                <div class="card border-0 bg-light p-3 mb-3">
                                    <h6 class="fw-bold text-secondary mb-2">Hardness Air</h6>
                                    {!! renderAnalisisEditChecklistItem('hardness_inlet_ws', 'Masukkan hardness Inlet WS') !!}
                                    {!! renderAnalisisEditChecklistItem('hardness_outlet_ws', 'Masukkan hardness Outlet WS') !!}
                                    {!! renderAnalisisEditChecklistItem('hardness_ws_storage', 'Masukkan hardness WS Storage') !!}
                                    {!! renderAnalisisEditChecklistItem('hardness_ct', 'Masukkan hardness CT') !!}
                                    {!! renderAnalisisEditChecklistItem('hardness_ro', 'Masukkan hardness RO') !!}
                                    {!! renderAnalisisEditChecklistItem('hardness_boiler', 'Masukkan hardness Boiler') !!}
                                </div>
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
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white" id="modalCollectedDetailTitle">Detail Data Bulanan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalCollectedDetailContent">
                    <!-- Tables loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Submit Monthly -->
    <div class="modal fade" id="modalSubmitMonthly" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Kirim Approval Bulanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formSubmitMonthly">
                        @csrf
                        <input type="hidden" name="bulan" id="sm_bulan">
                        <input type="hidden" name="tahun" id="sm_tahun">

                        <div class="mb-3">
                            <label class="form-label">Pilih Supervisor</label>
                            <select name="supervisor_id" id="select_supervisor" class="form-select" required>
                                <option value="">-- Pilih Supervisor --</option>
                            </select>
                        </div>

                        <p class="text-muted small">
                            * Dengan menekan kirim, data checklist pada bulan ini akan diajukan ke Supervisor.
                        </p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning" onclick="processSubmitMonthly()">Kirim
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
                    <h5 class="modal-title text-white"><i class="ri-file-excel-2-line me-1"></i> Export Excel Analisis Utility
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formExport">
                        <div class="row g-2">
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tahun</label>
                                <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}">
                            </div>
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
        const API_URL = "{{ route('analisis-utility.get-data') }}";
        let currentPage = 1;

        const FIELDS_PH = [
            { field: 'ph_fw_storage', label: 'Masukkan pH FW Storage' },
            { field: 'ph_ws_storage', label: 'Masukkan pH WS Storage' },
            { field: 'ph_ro_storage', label: 'Masukkan pH RO Storage' },
            { field: 'ph_in_mmf', label: 'Masukkan pH In MMF' },
            { field: 'ph_buffer_tank_ws', label: 'Masukkan pH Buffer Tank WS' },
            { field: 'ph_outlet_ws', label: 'Masukkan pH Outlet WS' },
            { field: 'ph_menara_ws', label: 'Masukkan pH Menara WS' },
            { field: 'ph_depo_lt1', label: 'Masukkan pH Depo Lt.1' },
            { field: 'ph_depo_lt2', label: 'Masukkan pH Depo Lt.2' },
            { field: 'ph_cooling_tower', label: 'Masukkan pH Cooling Tower' },
            { field: 'ph_boiler', label: 'Masukkan pH Boiler' },
            { field: 'ph_outlet_ws_2', label: 'Masukkan pH Outlet WS (2)' }
        ];

        const FIELDS_TDS = [
            { field: 'tds_fw_storage', label: 'Masukkan TDS FW Storage' },
            { field: 'tds_ws_storage', label: 'Masukkan TDS WS Storage' },
            { field: 'tds_ro_storage', label: 'Masukkan TDS RO Storage' },
            { field: 'tds_in_mmf', label: 'Masukkan TDS In MMF' },
            { field: 'tds_out_ro', label: 'Masukkan TDS Out RO' },
            { field: 'tds_menara_ws', label: 'Masukkan TDS menara WS' },
            { field: 'tds_daily_tank_dissolver', label: 'Masukkan TDS daily Tank dissolver' },
            { field: 'tds_depo_lt1', label: 'Masukkan TDS Depo Lt.1' },
            { field: 'tds_depo_lt2', label: 'Masukkan TDS Depo Lt.2' },
            { field: 'tds_cooling_tower', label: 'Masukkan TDS Cooling Tower' },
            { field: 'tds_boiler', label: 'Masukkan TDS Boiler' }
        ];

        const FIELDS_TURB_CHLOR = [
            { field: 'turbidity_in_mmf', label: 'Masukkan Turbidity IN MMF' },
            { field: 'turbidity_out_mmf', label: 'Masukkan Turbidity Out MMF' },
            { field: 'turbidity_cooling_tower', label: 'Masukkan Turbidity Cooling Tower' },
            { field: 'chlorine_mmf', label: 'Masukkan Chlorine MMF' },
            { field: 'chlorine_menara', label: 'Masukkan Chlorine Menara' },
            { field: 'chlorine_depo_lt1', label: 'Masukkan Chlorine Depo LT.1' },
            { field: 'chlorine_depo_lt2', label: 'Masukkan Chlorine Depo LT.2' },
            { field: 'chlorine_daily_tank_dissolver', label: 'Masukkan Chlorine Daily Tank Dissolver' }
        ];

        const FIELDS_HARDNESS = [
            { field: 'hardness_inlet_ws', label: 'Masukkan hardness Inlet WS' },
            { field: 'hardness_outlet_ws', label: 'Masukkan hardness Outlet WS' },
            { field: 'hardness_ws_storage', label: 'Masukkan hardness WS Storage' },
            { field: 'hardness_ct', label: 'Masukkan hardness CT' },
            { field: 'hardness_ro', label: 'Masukkan hardness RO' },
            { field: 'hardness_boiler', label: 'Masukkan hardness Boiler' }
        ];

        const ALL_FIELDS = [...FIELDS_PH, ...FIELDS_TDS, ...FIELDS_TURB_CHLOR, ...FIELDS_HARDNESS];

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

                        let countOk = 0;
                        let countNok = 0;
                        let countEmpty = 0;

                        ALL_FIELDS.forEach(f => {
                            if (item[f.field] === 'OK') countOk++;
                            else if (item[f.field] === 'NOK') countNok++;
                            else countEmpty++;
                        });

                        html += `
                            <tr>
                                <td class="text-center fw-medium">${item.tanggal}</td>
                                <td class="text-center text-success fw-bold">${countOk} Items</td>
                                <td class="text-center text-danger fw-bold">${countNok} Items</td>
                                <td class="text-center text-secondary">${countEmpty} Items</td>
                                <td class="text-center">${statusBadge}</td>
                                <td class="text-center">
                                    <div>
                                        <button class="btn btn-icon btn-sm btn-info" onclick="showDetail(${item.id})" title="Detail">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        @if (auth()->user()->jabatan != 'operator')
                                            <button class="btn btn-icon btn-sm btn-primary" onclick="editData(${item.id})" title="Edit">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                            <button class="btn btn-icon btn-sm btn-danger" onclick="deleteData(${item.id})" title="Hapus">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    if (res.data.length == 0) {
                        html =
                            '<tr><td colspan="6" class="text-center py-4 text-muted">Data checklist tidak ditemukan</td></tr>';
                    }

                    $('#tbodyData').html(html);
                    renderPagination(res.pagination);

                    if (res.data.length > 0) {
                        let first = res.data[0];
                        let statusText = '';
                        if (first.approval_status === 'draft') statusText =
                            '<span class="badge bg-warning p-2"><i class="ri-time-line me-1"></i> Status Bulan Ini: Draft</span>';
                        else if (first.approval_status === 'submitted') statusText =
                            '<span class="badge bg-info p-2"><i class="ri-loader-4-line me-1"></i> Status Bulan Ini: Menunggu Approval</span>';
                        else if (first.approval_status === 'approved_foreman') statusText =
                            '<span class="badge bg-primary p-2"><i class="ri-loader-4-line me-1"></i> Status Bulan Ini: Approved Foreman</span>';
                        else if (first.approval_status === 'approved_supervisor') statusText =
                            '<span class="badge bg-success p-2"><i class="ri-checkbox-circle-line me-1"></i> Status Bulan Ini: Approved</span>';
                        else if (first.approval_status === 'rejected') statusText =
                            '<span class="badge bg-danger p-2"><i class="ri-close-circle-line me-1"></i> Status Bulan Ini: Rejected</span>';

                        $('#monthlyStatusContainer').html(statusText);
                    } else {
                        $('#monthlyStatusContainer').empty();
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
                url: "{{ route('analisis-utility.get-collected') }}",
                type: "GET",
                success: function(res) {
                    tbody.empty();
                    if (res.results && res.results.length > 0) {
                        card.show();
                        window.collectedData = res.results;
                        res.results.forEach((monthData, index) => {
                            let app = monthData.approval;
                            let monthName = moment().month(app.bulan - 1).format('MMMM');
                            let trHtml = `
                            <tr>
                                <td class="ps-3 fw-medium">${monthName}</td>
                                <td>${app.tahun}</td>
                                <td class="text-center pe-3">
                                    <button class="btn btn-sm btn-info" onclick="showCollectedDetail(${index})">
                                        <i class="ri-eye-line me-1"></i> Detail
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="openSubmitMonthly(${app.bulan}, ${app.tahun})">
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

        function buildChecklistStatusHtml(item, fieldList) {
            let listHtml = '<ul class="list-group list-group-flush">';
            fieldList.forEach(f => {
                let badge = '<span class="badge bg-secondary">-</span>';
                if (item[f.field] === 'OK') badge =
                    '<span class="badge bg-success"><i class="ri-check-line"></i> OK</span>';
                else if (item[f.field] === 'NOK') badge =
                    '<span class="badge bg-danger"><i class="ri-close-line"></i> NOK</span>';

                listHtml += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="small fw-medium">${f.label}</span>
                    ${badge}
                </li>
                `;
            });
            listHtml += '</ul>';
            return listHtml;
        }

        function showCollectedDetail(index) {
            let item = window.collectedData[index];
            $('#modalCollectedDetailTitle').text(`Detail Checklist Bulan ${moment().month(item.approval.bulan-1).format('MMMM')} - ${item.approval.tahun}`);
            
            let html = '';
            item.data.forEach(d => {
                html += `
                    <div class="card mb-3 border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold">${d.tanggal}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="fw-bold text-primary mb-2">pH Air</div>
                                    ${buildChecklistStatusHtml(d, FIELDS_PH)}
                                    <div class="fw-bold text-danger mb-2 mt-3">Turbidity & Chlorine</div>
                                    ${buildChecklistStatusHtml(d, FIELDS_TURB_CHLOR)}
                                </div>
                                <div class="col-md-6">
                                    <div class="fw-bold text-success mb-2">TDS Air</div>
                                    ${buildChecklistStatusHtml(d, FIELDS_TDS)}
                                    <div class="fw-bold text-secondary mb-2 mt-3">Hardness Air</div>
                                    ${buildChecklistStatusHtml(d, FIELDS_HARDNESS)}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#modalCollectedDetailContent').html(html);
            $('#modalCollectedDetail').modal('show');
        }

        function openSubmitMonthly(bulan, tahun) {
            $('#sm_bulan').val(bulan);
            $('#sm_tahun').val(tahun);

            $.get('/api/utility/users/approvers', function(data) {
                const supervisorList = data.user ?? [];
                let supervisorOpts = '<option value="">— Pilih Supervisor —</option>';
                supervisorList.forEach(function(u) {
                    supervisorOpts += `<option value="${u.id}">${u.username}</option>`;
                });
                $('#select_supervisor').html(supervisorOpts);
            }).fail(function() {
                $('#select_supervisor').html('<option value="">Gagal memuat data</option>');
            });

            $('#modalSubmitMonthly').modal('show');
        }

        function processSubmitMonthly() {
            let form = $('#formSubmitMonthly');
            if (!form[0].checkValidity()) {
                form[0].reportValidity();
                return;
            }

            let btn = $('#modalSubmitMonthly').find('.btn-warning');
            btn.prop('disabled', true).text('Mengirim...');

            $.ajax({
                url: "{{ route('analisis-utility.submit-monthly') }}",
                type: "POST",
                data: form.serialize(),
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message
                    });
                    $('#modalSubmitMonthly').modal('hide');
                    loadData();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).text('Kirim Sekarang');
                }
            });
        }

        function showDetail(id) {
            $.ajax({
                url: `{{ url('utility/analisis-utility/show') }}/${id}`,
                type: "GET",
                success: function(res) {
                    if (res.status === 200) {
                        let item = res.data;
                        let html = `
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Tanggal Log:</strong> ${item.tanggal}
                                </div>
                                <div class="col-md-6 text-end">
                                    <strong>Dibuat oleh:</strong> ${item.creator_name}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card shadow-none border mb-3">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="card-title text-primary fw-bold mb-0">pH Air</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            ${buildChecklistStatusHtml(item, FIELDS_PH)}
                                        </div>
                                    </div>
                                    <div class="card shadow-none border mb-3">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="card-title text-danger fw-bold mb-0">Turbidity & Chlorine</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            ${buildChecklistStatusHtml(item, FIELDS_TURB_CHLOR)}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card shadow-none border mb-3">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="card-title text-success fw-bold mb-0">TDS Air</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            ${buildChecklistStatusHtml(item, FIELDS_TDS)}
                                        </div>
                                    </div>
                                    <div class="card shadow-none border mb-3">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="card-title text-secondary fw-bold mb-0">Hardness Air</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            ${buildChecklistStatusHtml(item, FIELDS_HARDNESS)}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#detailContent').html(html);
                        $('#modalDetail').modal('show');
                    }
                }
            });
        }

        function editData(id) {
            $.ajax({
                url: `{{ url('utility/analisis-utility/show') }}/${id}`,
                type: "GET",
                success: function(res) {
                    if (res.status === 200) {
                        let item = res.data;
                        $('#edit_id').val(item.id);
                        $('#edit_tanggal').val(item.tanggal);

                        ALL_FIELDS.forEach(f => {
                            let val = item[f.field] || '';
                            $(`input[name="${f.field}"][value="${val}"]`).prop('checked', true);
                        });

                        $('#modalEdit').modal('show');
                    }
                }
            });
        }

        function submitUpdate() {
            let id = $('#edit_id').val();
            let data = $('#formEdit').serialize();

            $.ajax({
                url: `{{ url('utility/analisis-utility/update') }}/${id}`,
                type: "POST",
                data: data,
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#modalEdit').modal('hide');
                    loadData();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                    });
                }
            });
        }

        function deleteData(id) {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data checklist harian ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('utility/analisis-utility/destroy') }}/${id}`,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadData();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                            });
                        }
                    });
                }
            });
        }

        function renderPagination(p) {
            let links = '';
            let info = `Showing page <b>${p.current_page}</b> of <b>${p.last_page}</b> (Total: <b>${p.total}</b> items)`;
            $('#paginationInfo').html(info);

            for (let i = 1; i <= p.last_page; i++) {
                links += `
                    <li class="page-item ${p.current_page === i ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="loadData(${i})">${i}</a>
                    </li>
                `;
            }
            $('#paginationLinks').html(links);
        }

        // Export Actions
        $('#btnExport').click(function() {
            $('#modalExport').modal('show');
        });

        $('#btnExportConfirm').on('click', function() {
            const bulan = $('select[name="bulan"]', '#formExport').val();
            const tahun = $('input[name="tahun"]', '#formExport').val();

            let btn = $(this);
            btn.prop('disabled', true).html(
                '<i class="ri-loader-4-line align-middle me-1"></i> Downloading...');

            fetch(`{{ route('analisis-utility.export') }}?bulan=${bulan}&tahun=${tahun}`)
                .then(async response => {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.indexOf('application/json') !== -1) {
                        const json = await response.json();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data Tidak Ditemukan',
                            text: json.message ||
                                'Tidak ada data ditemukan untuk periode tersebut.'
                        });
                    } else if (response.ok) {
                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;

                        const disposition = response.headers.get('content-disposition');
                        let filename = 'Analisis_Utility_Report.xlsx';
                        if (disposition && disposition.indexOf('filename=') !== -1) {
                            filename = disposition.split('filename=')[1].replace(/["']/g, '');
                        }
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);
                        $('#modalExport').modal('hide');
                    } else {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat mengunduh laporan.',
                            'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Gagal!', 'Koneksi ke server gagal.', 'error');
                })
                .finally(() => {
                    btn.prop('disabled', false).html(
                        '<i class="ri-download-cloud-2-line me-1"></i> Download Excel');
                });
        });

        $(document).ready(function() {
            loadData();
        });
    </script>
@endsection
