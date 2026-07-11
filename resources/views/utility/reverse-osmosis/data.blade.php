@extends('layouts.app')

@section('title', 'Rekap Data Reverse Osmosis')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #1b4965 0%, #0f3057 100%); border-radius: 12px;">
                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="text-white fw-bold mb-1">
                                    <i class="ri-database-2-line text-success me-2"></i>
                                    Reverse Osmosis - Rekap Data
                                </h4>
                                <p class="text-white-50 mb-0">
                                    Daftar log harian Reverse Osmosis
                                </p>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('reverse-osmosis.index') }}"
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
                            <div id="monthlyStatusContainer" class="d-inline-block">
                                <!-- Status Approval Bulanan Terpilih akan muncul di sini -->
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-nowrap" id="tableData">
                            <thead class="table-light align-middle">
                                <tr class="text-center">
                                    <th>Tanggal</th>
                                    <th>MMF Feed 1/2</th>
                                    <th>Micron Inlet/Outlet</th>
                                    <th>RO Permeate</th>
                                    <th>RO Reject</th>
                                    <th>RO Accumulation</th>
                                    <th>CIP Keterangan</th>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">Detail Log Reverse Osmosis</h5>
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
                    <h5 class="modal-title text-white">Edit Log Reverse Osmosis</h5>
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

                            <div class="col-12 mt-3"><span class="badge bg-soft-primary text-primary">MMF (Multi Media
                                    Filter)</span></div>
                            <div class="col-md-3">
                                <label class="form-label">Pressure Feed MMF 1</label>
                                <input type="number" step="0.01" name="mmf_pressure_feed_1"
                                    id="edit_mmf_pressure_feed_1" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pressure Feed MMF 2</label>
                                <input type="number" step="0.01" name="mmf_pressure_feed_2"
                                    id="edit_mmf_pressure_feed_2" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pressure Produk MMF 1</label>
                                <input type="number" step="0.01" name="mmf_pressure_produk_1"
                                    id="edit_mmf_pressure_produk_1" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pressure Produk MMF 2</label>
                                <input type="number" step="0.01" name="mmf_pressure_produk_2"
                                    id="edit_mmf_pressure_produk_2" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Output Flow MMF 1</label>
                                <input type="number" step="0.01" name="mmf_output_flow_1"
                                    id="edit_mmf_output_flow_1" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Output Flow MMF 2</label>
                                <input type="number" step="0.01" name="mmf_output_flow_2"
                                    id="edit_mmf_output_flow_2" class="form-control">
                            </div>
                            <div class="col-md-3 d-flex align-items-end mb-2">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="mmf_status_backwash_1" class="form-check-input"
                                        id="edit_backwash1" value="1">
                                    <label class="form-check-label fw-bold" for="edit_backwash1">Backwash MMF 1</label>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end mb-2">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="mmf_status_backwash_2" class="form-check-input"
                                        id="edit_backwash2" value="1">
                                    <label class="form-check-label fw-bold" for="edit_backwash2">Backwash MMF 2</label>
                                </div>
                            </div>

                            <div class="col-12 mt-3"><span class="badge bg-soft-warning text-warning">Micron Filter</span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pressure Inlet Micron Filter</label>
                                <input type="number" step="0.01" name="micron_filter_pressure_inlet"
                                    id="edit_micron_filter_pressure_inlet" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pressure Outlet Micron Filter</label>
                                <input type="number" step="0.01" name="micron_filter_pressure_outlet"
                                    id="edit_micron_filter_pressure_outlet" class="form-control">
                            </div>

                            <div class="col-12 mt-3"><span class="badge bg-soft-info text-info">RO (Reverse
                                    Osmosis)</span></div>
                            <div class="col-md-4">
                                <label class="form-label">Permeate Flowrate</label>
                                <input type="number" step="0.01" name="ro_permeate_flowrate"
                                    id="edit_ro_permeate_flowrate" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">RO Reject Flowrate</label>
                                <input type="number" step="0.01" name="ro_reject_flowrate"
                                    id="edit_ro_reject_flowrate" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Flowmeter Accumulation</label>
                                <input type="number" step="0.01" name="ro_flowmeter_accumulation"
                                    id="edit_ro_flowmeter_accumulation" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pressure Inlet 1st Stage</label>
                                <input type="number" step="0.01" name="ro_pressure_inlet_1st_stage"
                                    id="edit_ro_pressure_inlet_1st_stage" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pressure Inlet 2nd Stage</label>
                                <input type="number" step="0.01" name="ro_pressure_inlet_2nd_stage"
                                    id="edit_ro_pressure_inlet_2nd_stage" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pressure Concentrate</label>
                                <input type="number" step="0.01" name="ro_pressure_concentrate"
                                    id="edit_ro_pressure_concentrate" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pressure RO Produk</label>
                                <input type="number" step="0.01" name="ro_pressure_produk"
                                    id="edit_ro_pressure_produk" class="form-control">
                            </div>

                            <div class="col-12 mt-3"><span class="badge bg-soft-danger text-danger">CIP (Clean In
                                    Place)</span></div>
                            <div class="col-md-6">
                                <label class="form-label">Keterangan CIP</label>
                                <input type="text" name="cip_keterangan" id="edit_cip_keterangan"
                                    class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Chemical CIP</label>
                                <input type="text" name="cip_jenis_chemical" id="edit_cip_jenis_chemical"
                                    class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Qty Chemical CIP</label>
                                <input type="text" name="cip_qty_chemical" id="edit_cip_qty_chemical"
                                    class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hasil CIP</label>
                                <input type="text" name="cip_hasil" id="edit_cip_hasil" class="form-control">
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
                    <h5 class="modal-title text-white" id="modalCollectedDetailTitle">Detail Data Bulanan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>MMF Feed 1/2</th>
                                    <th>Micron Inlet/Outlet</th>
                                    <th>RO Permeate</th>
                                    <th>RO Reject</th>
                                    <th>RO Accumulation</th>
                                    <th>CIP Keterangan</th>
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
                            * Dengan menekan kirim, data pada bulan ini akan diajukan ke Supervisor.
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
                    <h5 class="modal-title text-white"><i class="ri-file-excel-2-line me-1"></i> Export Excel Reverse
                        Osmosis
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
        const API_URL = "{{ route('reverse-osmosis.get-data') }}";
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
                                <td class="text-center">${formatNum(item.mmf_pressure_feed_1)} / ${formatNum(item.mmf_pressure_feed_2)}</td>
                                <td class="text-center">${formatNum(item.micron_filter_pressure_inlet)} / ${formatNum(item.micron_filter_pressure_outlet)}</td>
                                <td class="text-center">${formatNum(item.ro_permeate_flowrate)}</td>
                                <td class="text-center">${formatNum(item.ro_reject_flowrate)}</td>
                                <td class="text-center">${formatNum(item.ro_flowmeter_accumulation)}</td>
                                <td>${item.cip_keterangan || '-'}</td>
                                <td>${statusBadge}</td>
                                <td class="text-center">
                                    <div>
                                        <button class="btn btn-sm btn-info" onclick="showDetail(${item.id})" title="Detail">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        @if (auth()->user()->jabatan != 'operator')
                                            <button class="btn btn-sm btn-primary" onclick="editData(${item.id})" title="Edit">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteData(${item.id})" title="Hapus">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    if (res.data.length == 0) {
                        html = '<tr><td colspan="9" class="text-center py-4">Data tidak ditemukan</td></tr>';
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
                url: "{{ route('reverse-osmosis.get-collected') }}",
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

        function showCollectedDetail(index) {
            let monthData = window.collectedData[index];
            let app = monthData.approval;
            let monthName = moment().month(app.bulan - 1).format('MMMM');
            let dataRows = '';
            const formatNum = (v) => v ? Number(v) : '-';

            monthData.data.forEach(item => {
                dataRows += `
                <tr>
                    <td>${item.tanggal}</td>
                    <td class="text-center">${formatNum(item.mmf_pressure_feed_1)} / ${formatNum(item.mmf_pressure_feed_2)}</td>
                    <td class="text-center">${formatNum(item.micron_filter_pressure_inlet)} / ${formatNum(item.micron_filter_pressure_outlet)}</td>
                    <td class="text-center">${formatNum(item.ro_permeate_flowrate)}</td>
                    <td class="text-center">${formatNum(item.ro_reject_flowrate)}</td>
                    <td class="text-center">${formatNum(item.ro_flowmeter_accumulation)}</td>
                    <td>${item.cip_keterangan || '-'}</td>
                </tr>
            `;
            });

            $('#modalCollectedDetailTitle').text(
                `Detail Data Bulan ${monthName} ${app.tahun}`
            );
            $('#modalCollectedDetailTbody').html(dataRows);
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

            $.post("{{ route('reverse-osmosis.submit-monthly') }}", data, function(res) {
                Swal.fire('Berhasil!', res.message, 'success');
                $('#modalSubmitMonthly').modal('hide');
                loadData(currentPage);
            }).fail(function(xhr) {
                let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                Swal.fire('Gagal!', msg, 'error');
            });
        }

        function showDetail(id) {
            $.get("{{ url('utility/reverse-osmosis/show') }}/" + id, function(res) {
                let data = res.data;
                const formatNum = (v) => v ? Number(v) : '-';
                let html = `
                    <div class="row g-3">
                        <div class="col-4"><strong>Tanggal:</strong> ${data.tanggal}</div>
                        <div class="col-4"><strong>Created By:</strong> ${data.created_by?.username || '-'}</div>
                        <div class="col-12"><hr></div>
                        <div class="col-12"><span class="badge bg-soft-primary text-primary">MMF</span></div>
                        <div class="col-6"><strong>Pressure Feed 1/2:</strong> ${formatNum(data.mmf_pressure_feed_1)} / ${formatNum(data.mmf_pressure_feed_2)} Bar</div>
                        <div class="col-6"><strong>Pressure Produk 1/2:</strong> ${formatNum(data.mmf_pressure_produk_1)} / ${formatNum(data.mmf_pressure_produk_2)} Bar</div>
                        <div class="col-6"><strong>Output Flow MMF 1/2:</strong> ${formatNum(data.mmf_output_flow_1)} / ${formatNum(data.mmf_output_flow_2)} Bar</div>
                        <div class="col-6"><strong>Backwash 1/2:</strong> ${data.mmf_status_backwash_1 ? 'Ya' : 'Tidak'} / ${data.mmf_status_backwash_2 ? 'Ya' : 'Tidak'}</div>
                        
                        <div class="col-12 mt-2"><span class="badge bg-soft-warning text-warning">Micron Filter</span></div>
                        <div class="col-6"><strong>Pressure Inlet/Outlet:</strong> ${formatNum(data.micron_filter_pressure_inlet)} / ${formatNum(data.micron_filter_pressure_outlet)} Bar</div>

                        <div class="col-12 mt-2"><span class="badge bg-soft-info text-info">Reverse Osmosis</span></div>
                        <div class="col-6"><strong>Permeate Flowrate:</strong> ${formatNum(data.ro_permeate_flowrate)} m3/jam</div>
                        <div class="col-6"><strong>Reject Flowrate:</strong> ${formatNum(data.ro_reject_flowrate)} m3/jam</div>
                        <div class="col-6"><strong>Accumulation Flowmeter:</strong> ${formatNum(data.ro_flowmeter_accumulation)} m3</div>
                        <div class="col-6"><strong>Inlet Stage 1 / 2:</strong> ${formatNum(data.ro_pressure_inlet_1st_stage)} / ${formatNum(data.ro_pressure_inlet_2nd_stage)} Bar</div>
                        <div class="col-6"><strong>Concentrate Pressure:</strong> ${formatNum(data.ro_pressure_concentrate)} Bar</div>
                        <div class="col-6"><strong>RO Produk Pressure:</strong> ${formatNum(data.ro_pressure_produk)} Bar</div>

                        <div class="col-12 mt-2"><span class="badge bg-soft-danger text-danger">CIP</span></div>
                        <div class="col-6"><strong>Keterangan:</strong> ${data.cip_keterangan || '-'}</div>
                        <div class="col-6"><strong>Jenis Chemical:</strong> ${data.cip_jenis_chemical || '-'}</div>
                        <div class="col-6"><strong>Qty Chemical:</strong> ${data.cip_qty_chemical || '-'}</div>
                        <div class="col-6"><strong>Hasil:</strong> ${data.cip_hasil || '-'}</div>
                    </div>
                `;
                $('#detailContent').html(html);
                $('#modalDetail').modal('show');
            });
        }

        function editData(id) {
            $.get("{{ url('utility/reverse-osmosis/show') }}/" + id, function(res) {
                let data = res.data;
                const formatNum = (v) => v ? Number(v) : '';

                $('#edit_id').val(data.id);
                $('#edit_tanggal').val(data.tanggal.substring(0, 10));

                $('#edit_mmf_pressure_feed_1').val(formatNum(data.mmf_pressure_feed_1));
                $('#edit_mmf_pressure_feed_2').val(formatNum(data.mmf_pressure_feed_2));
                $('#edit_mmf_pressure_produk_1').val(formatNum(data.mmf_pressure_produk_1));
                $('#edit_mmf_pressure_produk_2').val(formatNum(data.mmf_pressure_produk_2));
                $('#edit_mmf_output_flow_1').val(formatNum(data.mmf_output_flow_1));
                $('#edit_mmf_output_flow_2').val(formatNum(data.mmf_output_flow_2));
                $('#edit_backwash1').prop('checked', data.mmf_status_backwash_1);
                $('#edit_backwash2').prop('checked', data.mmf_status_backwash_2);

                $('#edit_micron_filter_pressure_inlet').val(formatNum(data.micron_filter_pressure_inlet));
                $('#edit_micron_filter_pressure_outlet').val(formatNum(data.micron_filter_pressure_outlet));

                $('#edit_ro_permeate_flowrate').val(formatNum(data.ro_permeate_flowrate));
                $('#edit_ro_reject_flowrate').val(formatNum(data.ro_reject_flowrate));
                $('#edit_ro_flowmeter_accumulation').val(formatNum(data.ro_flowmeter_accumulation));
                $('#edit_ro_pressure_inlet_1st_stage').val(formatNum(data.ro_pressure_inlet_1st_stage));
                $('#edit_ro_pressure_inlet_2nd_stage').val(formatNum(data.ro_pressure_inlet_2nd_stage));
                $('#edit_ro_pressure_concentrate').val(formatNum(data.ro_pressure_concentrate));
                $('#edit_ro_pressure_produk').val(formatNum(data.ro_pressure_produk));

                $('#edit_cip_keterangan').val(data.cip_keterangan || '');
                $('#edit_cip_jenis_chemical').val(data.cip_jenis_chemical || '');
                $('#edit_cip_qty_chemical').val(data.cip_qty_chemical || '');
                $('#edit_cip_hasil').val(data.cip_hasil || '');

                $('#modalEdit').modal('show');
            });
        }

        function submitUpdate() {
            let id = $('#edit_id').val();

            let hasValue = false;
            $('#formEdit').find('input[type="number"], input[type="text"]').each(function() {
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

            $.post("{{ url('utility/reverse-osmosis/update') }}/" + id, data, function(res) {
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
                        url: "{{ url('utility/reverse-osmosis/destroy') }}/" + id,
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
                btn.prop('disabled', true).html(
                    '<i class="ri-loader-4-line align-middle me-1"></i> Downloading...');

                fetch(`{{ route('reverse-osmosis.export') }}?bulan=${bulan}&tahun=${tahun}`)
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
                            let filename = 'Reverse_Osmosis_Report.xlsx';
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
        });
    </script>
@endsection
