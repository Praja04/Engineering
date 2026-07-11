@extends('layouts.app')

@section('title', 'Rekap Data Agenda RO-WS')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #0f3057 0%, #00587a 100%); border-radius: 12px;">
                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="text-white fw-bold mb-1">
                                    <i class="ri-database-2-line text-success me-2"></i>
                                    Agenda RO-WS - Rekap Data
                                </h4>
                                <p class="text-white-50 mb-0">
                                    Daftar log harian checklist Agenda RO-WS
                                </p>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('agenda-ro-ws.index') }}" class="btn btn-warning btn-sm rounded-pill px-3">
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
                            <i class="ri-history-line me-2"></i> Draft Data Bulanan Terkumpul
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
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">Detail Checklist Agenda RO-WS</h5>
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
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Edit Checklist Agenda RO-WS</h5>
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
                            if (!function_exists('renderEditChecklistItem')) {
                                function renderEditChecklistItem($fieldName, $labelText) {
                                    return '
                                    <div class="edit-item d-flex justify-content-between align-items-center flex-wrap py-2 border-bottom">
                                        <div class="fw-medium text-dark flex-grow-1 pe-2 mb-2 mb-sm-0 small">
                                            ' . $labelText . '
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <input type="radio" class="btn-check" name="' . $fieldName . '" id="edit_' . $fieldName . '_empty" value="">
                                            <label class="btn btn-outline-secondary px-3" for="edit_' . $fieldName . '_empty">Kosong</label>

                                            <input type="radio" class="btn-check" name="' . $fieldName . '" id="edit_' . $fieldName . '_ok" value="OK">
                                            <label class="btn btn-outline-success px-3" for="edit_' . $fieldName . '_ok">OK</label>

                                            <input type="radio" class="btn-check" name="' . $fieldName . '" id="edit_' . $fieldName . '_nok" value="NOK">
                                            <label class="btn btn-outline-danger px-3" for="edit_' . $fieldName . '_nok">NOK</label>
                                        </div>
                                    </div>
                                    ';
                                }
                            }
                        @endphp

                        <div class="card border-0 bg-light p-3 mb-3">
                            <h6 class="fw-bold text-primary mb-2">Reverse Osmosis Checklist</h6>
                            {!! renderEditChecklistItem('inspeksi_hpt_pump', 'Inspeksi (HLT) High Pressure Pump') !!}
                            {!! renderEditChecklistItem('inspeksi_cip_pump', 'Inspeksi (HLT) CIP Pump') !!}
                            {!! renderEditChecklistItem('inspeksi_blower_ro', 'Inspeksi (HLT) Blower RO') !!}
                            {!! renderEditChecklistItem('cek_chemical', 'Cek Chemical') !!}
                            {!! renderEditChecklistItem('pencatatan_flow_meter_produksi', 'Pencatatan Flow Meter Produksi') !!}
                            {!! renderEditChecklistItem('cek_nilai_conductivity', 'Cek Nilai Conductivity') !!}
                            {!! renderEditChecklistItem('cek_dp_1st_2st', 'Cek ΔP 1st & 2st') !!}
                            {!! renderEditChecklistItem('cek_dp_mmf_1_2', 'Cek ΔP MMF #1 & MMF #2') !!}
                            {!! renderEditChecklistItem('pencatatan_flow_meter_konsumsi', 'Pencatatan Flow Meter Konsumsi') !!}
                            {!! renderEditChecklistItem('backwash_mmf_1', 'Backwash MMF #1') !!}
                            {!! renderEditChecklistItem('backwash_mmf_2', 'Backwash MMF #2') !!}
                            {!! renderEditChecklistItem('cek_kondisi_rotameter_mmf_1', 'Cek Kondisi Rota Meter MMF 1') !!}
                            {!! renderEditChecklistItem('cek_kondisi_rotameter_mmf_2', 'Cek Kondisi Rota Meter MMF 2') !!}
                            {!! renderEditChecklistItem('cek_kondisi_rotameter_ro_product', 'Cek Kondisi Rotameter RO Product') !!}
                            {!! renderEditChecklistItem('cek_kondisi_rotameter_ro_reject', 'Cek Kondisi Rotameter RO Reject') !!}
                            {!! renderEditChecklistItem('kalibrasi_dosis_kimia', 'Kalibrasi Dosis Penggunaan Kimia') !!}
                            {!! renderEditChecklistItem('cleaning_unit_ro', 'Cleaning Unit RO') !!}
                            {!! renderEditChecklistItem('cleaning_unit_mmf_1', 'Cleaning Unit MMF 1') !!}
                            {!! renderEditChecklistItem('cleaning_unit_mmf_2', 'Cleaning Unit MMF 2') !!}
                        </div>

                        <div class="card border-0 bg-light p-3">
                            <h6 class="fw-bold text-danger mb-2">Water Softener Checklist</h6>
                            {!! renderEditChecklistItem('cek_output_hardness', 'Cek Output Hardness') !!}
                            {!! renderEditChecklistItem('cek_flow_produk', 'Cek Flow Produk') !!}
                            {!! renderEditChecklistItem('regenerasi_mesin_ws', 'Regenerasi Mesin Water Softener') !!}
                            {!! renderEditChecklistItem('cek_pompa_transfer', 'Cek Kondisi Pompa Transfer (H,L,T)') !!}
                            {!! renderEditChecklistItem('cek_pompa_suplai', 'Cek Kondisi Pompa Suplai (H,L,T)') !!}
                            {!! renderEditChecklistItem('cleaning_tanki_buffer_ws', 'Cleaning Tanki Buffer WS') !!}
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
                    <h5 class="modal-title text-white"><i class="ri-file-excel-2-line me-1"></i> Export Excel Agenda RO-WS
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
        const API_URL = "{{ route('agenda-ro-ws.get-data') }}";
        let currentPage = 1;

        const FIELDS_RO = [
            { field: 'inspeksi_hpt_pump', label: 'Inspeksi (HLT) High Pressure Pump' },
            { field: 'inspeksi_cip_pump', label: 'Inspeksi (HLT) CIP Pump' },
            { field: 'inspeksi_blower_ro', label: 'Inspeksi (HLT) Blower RO' },
            { field: 'cek_chemical', label: 'Cek Chemical' },
            { field: 'pencatatan_flow_meter_produksi', label: 'Pencatatan Flow Meter Produksi RO Produk' },
            { field: 'cek_nilai_conductivity', label: 'Cek Nilai Conductivity' },
            { field: 'cek_dp_1st_2st', label: 'Cek ΔP 1st & 2st' },
            { field: 'cek_dp_mmf_1_2', label: 'Cek ΔP MMF #1 & MMF #2' },
            { field: 'pencatatan_flow_meter_konsumsi', label: 'Pencatatan Flow Meter Konsumsi RO Produk' },
            { field: 'backwash_mmf_1', label: 'Backwash MMF #1' },
            { field: 'backwash_mmf_2', label: 'Backwash MMF #2' },
            { field: 'cek_kondisi_rotameter_mmf_1', label: 'Cek Kondisi Rota Meter MMF 1' },
            { field: 'cek_kondisi_rotameter_mmf_2', label: 'Cek Kondisi Rota Meter MMF 2' },
            { field: 'cek_kondisi_rotameter_ro_product', label: 'Cek Kondisi Rotameter RO Product' },
            { field: 'cek_kondisi_rotameter_ro_reject', label: 'Cek Kondisi Rotameter RO Reject' },
            { field: 'kalibrasi_dosis_kimia', label: 'Kalibrasi Dosis Penggunaan Kimia' },
            { field: 'cleaning_unit_ro', label: 'Cleaning Unit RO' },
            { field: 'cleaning_unit_mmf_1', label: 'Cleaning Unit MMF 1' },
            { field: 'cleaning_unit_mmf_2', label: 'Cleaning Unit MMF 2' }
        ];

        const FIELDS_WS = [
            { field: 'cek_output_hardness', label: 'Cek Output Hardness' },
            { field: 'cek_flow_produk', label: 'Cek Flow Produk' },
            { field: 'regenerasi_mesin_ws', label: 'Regenerasi Mesin Water Softener' },
            { field: 'cek_pompa_transfer', label: 'Cek Kondisi Pompa Transfer (H,L,T)' },
            { field: 'cek_pompa_suplai', label: 'Cek Kondisi Pompa Suplai (H,L,T)' },
            { field: 'cleaning_tanki_buffer_ws', label: 'Cleaning Tanki Buffer WS' }
        ];

        const ALL_FIELDS = [...FIELDS_RO, ...FIELDS_WS];

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

                        // Count OK / NOK / Empty values
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
                        html = '<tr><td colspan="6" class="text-center py-4 text-muted">Data checklist tidak ditemukan</td></tr>';
                    }

                    $('#tbodyData').html(html);
                    renderPagination(res.pagination);

                    // Update Status Approval Bulanan Terpilih
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
                url: "{{ route('agenda-ro-ws.get-collected') }}",
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
                if (item[f.field] === 'OK') badge = '<span class="badge bg-success"><i class="ri-check-line"></i> OK</span>';
                else if (item[f.field] === 'NOK') badge = '<span class="badge bg-danger"><i class="ri-close-line"></i> NOK</span>';

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
            let monthData = window.collectedData[index];
            let app = monthData.approval;
            let monthName = moment().month(app.bulan - 1).format('MMMM');
            
            let dataHtml = '';
            monthData.data.forEach(item => {
                let countOk = 0;
                let countNok = 0;
                let countEmpty = 0;
                ALL_FIELDS.forEach(f => {
                    if (item[f.field] === 'OK') countOk++;
                    else if (item[f.field] === 'NOK') countNok++;
                    else countEmpty++;
                });

                dataHtml += `
                <div class="card border mb-2">
                    <div class="card-header bg-light d-flex justify-content-between">
                        <strong>Tanggal: ${item.tanggal}</strong>
                        <span>
                            <span class="badge bg-success me-1">${countOk} OK</span>
                            <span class="badge bg-danger">${countNok} NOK</span>
                        </span>
                    </div>
                </div>
                `;
            });

            $('#modalCollectedDetailTitle').text(`Detail Data Checklist Bulan ${monthName} ${app.tahun}`);
            $('#modalCollectedDetailContent').html(dataHtml);
            $('#modalCollectedDetail').modal('show');
        }

        function openSubmitMonthly(bulan, tahun) {
            $('#sm_bulan').val(bulan);
            $('#sm_tahun').val(tahun);

            // Load approvers
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
            let data = $('#formSubmitMonthly').serialize();

            if (!$('#select_supervisor').val()) {
                Swal.fire('Peringatan', 'Harap pilih Supervisor', 'warning');
                return;
            }

            $.post("{{ route('agenda-ro-ws.submit-monthly') }}", data, function(res) {
                Swal.fire('Berhasil!', res.message, 'success');
                $('#modalSubmitMonthly').modal('hide');
                loadData(currentPage);
            }).fail(function(xhr) {
                let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                Swal.fire('Gagal!', msg, 'error');
            });
        }

        function showDetail(id) {
            $.get("{{ url('utility/agenda-ro-ws/show') }}/" + id, function(res) {
                let data = res.data;
                let html = `
                    <div class="row g-3">
                        <div class="col-6"><strong>Tanggal:</strong> ${data.tanggal}</div>
                        <div class="col-6"><strong>Created By:</strong> ${data.created_by?.username || '-'}</div>
                        <div class="col-12"><hr class="my-1"></div>
                        
                        <div class="col-md-6">
                            <div class="card shadow-none border">
                                <div class="card-header bg-soft-primary py-2"><h6 class="mb-0 text-primary fw-bold">Reverse Osmosis</h6></div>
                                ${buildChecklistStatusHtml(data, FIELDS_RO)}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-none border">
                                <div class="card-header bg-soft-danger py-2"><h6 class="mb-0 text-danger fw-bold">Water Softener</h6></div>
                                ${buildChecklistStatusHtml(data, FIELDS_WS)}
                            </div>
                        </div>
                    </div>
                `;
                $('#detailContent').html(html);
                $('#modalDetail').modal('show');
            });
        }

        function editData(id) {
            $.get("{{ url('utility/agenda-ro-ws/show') }}/" + id, function(res) {
                let data = res.data;

                $('#edit_id').val(data.id);
                $('#edit_tanggal').val(data.tanggal.substring(0, 10));

                ALL_FIELDS.forEach(f => {
                    let val = data[f.field];
                    if (val === 'OK') {
                        $(`#edit_${f.field}_ok`).prop('checked', true);
                    } else if (val === 'NOK') {
                        $(`#edit_${f.field}_nok`).prop('checked', true);
                    } else {
                        $(`#edit_${f.field}_empty`).prop('checked', true);
                    }
                });

                $('#modalEdit').modal('show');
            });
        }

        function submitUpdate() {
            let id = $('#edit_id').val();

            let hasValue = false;
            $('#formEdit').find('input[type="radio"]:checked').each(function() {
                if ($(this).val() !== '') {
                    hasValue = true;
                    return false;
                }
            });

            if (!hasValue) {
                Swal.fire('Peringatan', 'Minimal harus ada 1 checklist (OK / NOK) yang diisi sebelum update.', 'warning');
                return;
            }

            let data = $('#formEdit').serialize();

            $.post("{{ url('utility/agenda-ro-ws/update') }}/" + id, data, function(res) {
                Swal.fire('Berhasil!', res.message, 'success');
                $('#modalEdit').modal('hide');
                loadData(currentPage);
            }).fail(function(xhr) {
                let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                Swal.fire('Gagal!', msg, 'error');
            });
        }

        function renderPagination(pagination) {
            let html = '';
            if (pagination && pagination.last_page > 1) {
                html += `<li class="page-item ${pagination.current_page == 1 ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="loadData(${pagination.current_page - 1})">Prev</a>
            </li>`;

                for (let i = 1; i <= pagination.last_page; i++) {
                    if (i == 1 || i == pagination.last_page || (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
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
                        url: "{{ url('utility/agenda-ro-ws/destroy') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            Swal.fire({
                                title: 'Success!',
                                text: res.message,
                                icon: 'success',
                                timer: 1000,
                                showConfirmButton: false
                            });
                            loadData(currentPage);
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                            Swal.fire('Gagal!', msg, 'error');
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            loadData();

            $('#btnExport').on('click', function() {
                $('#modalExport').modal('show');
            });

            $('#btnExportConfirm').on('click', function() {
                const bulan = $('select[name="bulan"]', '#formExport').val();
                const tahun = $('input[name="tahun"]', '#formExport').val();
                
                let btn = $(this);
                btn.prop('disabled', true).html('<i class="ri-loader-4-line align-middle me-1"></i> Downloading...');

                fetch(`{{ route('agenda-ro-ws.export') }}?bulan=${bulan}&tahun=${tahun}`)
                    .then(async response => {
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.indexOf('application/json') !== -1) {
                            const json = await response.json();
                            Swal.fire({
                                icon: 'warning',
                                title: 'Data Tidak Ditemukan',
                                text: json.message || 'Tidak ada data ditemukan untuk periode tersebut.'
                            });
                        } else if (response.ok) {
                            const blob = await response.blob();
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            
                            const disposition = response.headers.get('content-disposition');
                            let filename = 'Agenda_RO_WS_Report.xlsx';
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
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat mengunduh laporan.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Gagal!', 'Koneksi ke server gagal.', 'error');
                    })
                    .finally(() => {
                        btn.prop('disabled', false).html('<i class="ri-download-cloud-2-line me-1"></i> Download Excel');
                    });
            });
        });
    </script>
@endsection
