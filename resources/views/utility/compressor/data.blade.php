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
        @if(Auth::user()->jabatan === 'operator')
        <div id="collectedWeeksContainer">
            <!-- Dynamic cards will be injected here -->
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <option value="00:00">00:00 (24:00)</option>
                                <option value="04:00">04:00</option>
                            </select>
                        </div>

                        <div class="col-12 mt-3"><span class="badge bg-soft-primary text-primary">Pressure Outlet (Bar)</span></div>
                        <div class="col-md-3">
                            <label class="form-label">Outlet 1</label>
                            <input type="number" step="0.01" name="pressure_outlet_1" id="edit_pressure_1" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Outlet 2</label>
                            <input type="number" step="0.01" name="pressure_outlet_2" id="edit_pressure_2" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Outlet 3</label>
                            <input type="number" step="0.01" name="pressure_outlet_3" id="edit_pressure_3" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Outlet 4</label>
                            <input type="number" step="0.01" name="pressure_outlet_4" id="edit_pressure_4" class="form-control">
                        </div>

                        <div class="col-12 mt-3"><span class="badge bg-soft-success text-success">Element Outlet (°C)</span></div>
                        <div class="col-md-4">
                            <label class="form-label">Element 1</label>
                            <input type="number" step="0.01" name="element_outlet_1" id="edit_element_1" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Element 2</label>
                            <input type="number" step="0.01" name="element_outlet_2" id="edit_element_2" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Element 4</label>
                            <input type="number" step="0.01" name="element_outlet_4" id="edit_element_4" class="form-control">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label">Load Percent (%)</label>
                            <input type="number" step="0.01" name="load_percent" id="edit_load" class="form-control">
                        </div>

                        <div class="col-12 mt-3"><span class="badge bg-soft-info text-info">Running Hour</span></div>
                        <div class="col-md-3">
                            <label class="form-label">RH 1</label>
                            <input type="number" step="0.01" name="running_hour_1" id="edit_rh_1" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">RH 2</label>
                            <input type="number" step="0.01" name="running_hour_2" id="edit_rh_2" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">RH 3</label>
                            <input type="number" step="0.01" name="running_hour_3" id="edit_rh_3" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">RH 4</label>
                            <input type="number" step="0.01" name="running_hour_4" id="edit_rh_4" class="form-control">
                        </div>

                        <div class="col-12 mt-3"><span class="badge bg-soft-dark text-dark">Motor Start</span></div>
                        <div class="col-md-3">
                            <label class="form-label">Start 1</label>
                            <input type="number" step="0.01" name="motor_start_1" id="edit_start_1" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start 2</label>
                            <input type="number" step="0.01" name="motor_start_2" id="edit_start_2" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start 3</label>
                            <input type="number" step="0.01" name="motor_start_3" id="edit_start_3" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start 4</label>
                            <input type="number" step="0.01" name="motor_start_4" id="edit_start_4" class="form-control">
                        </div>

                        <div class="col-12 mt-3"><span class="badge bg-soft-secondary text-secondary">Lain-lain</span></div>
                        <div class="col-md-3">
                            <label class="form-label">Accumulated Vol.</label>
                            <input type="number" step="0.01" name="accumulated_volume" id="edit_acc_vol" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Temp Comp IR</label>
                            <input type="number" step="0.01" name="temperature_comp_ir" id="edit_temp_ir" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pressure In</label>
                            <input type="number" step="0.01" name="pressure_in" id="edit_p_in" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pressure Out</label>
                            <input type="number" step="0.01" name="pressure_out" id="edit_p_out" class="form-control">
                        </div>

                        <div class="col-12 mt-3"><span class="badge bg-soft-warning text-warning">Suhu Dryer (°C)</span></div>
                        <div class="col-md-4">
                            <label class="form-label">Dryer TR15</label>
                            <input type="number" step="0.01" name="suhu_dryer_tr15" id="edit_dryer_tr" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Dryer FX250</label>
                            <input type="number" step="0.01" name="suhu_dryer_fx250" id="edit_dryer_fx" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Dryer IR</label>
                            <input type="number" step="0.01" name="suhu_dryer_ir" id="edit_dryer_ir" class="form-control">
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
                        <label class="form-label">Pilih Foreman</label>
                        <select name="foreman_id" id="sw_foreman_id" class="form-select" required>
                            <option value="">-- Pilih Foreman --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Supervisor</label>
                        <select name="supervisor_id" id="sw_supervisor_id" class="form-select" required>
                            <option value="">-- Pilih Supervisor --</option>
                        </select>
                    </div>

                    <p class="text-muted small">
                        * Dengan menekan kirim, data pada minggu ini akan dikunci dan dikirim ke Foreman & Supervisor.
                    </p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" onclick="processSubmitWeekly()">Kirim Sekarang</button>
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
                    if (item.approval_status == 'draft') statusBadge = '<span class="badge bg-warning">Draft</span>';
                    else if (item.approval_status == 'submitted') statusBadge = '<span class="badge bg-info">Submitted</span>';
                    else if (item.approval_status == 'approved_foreman') statusBadge = '<span class="badge bg-primary">Approve FM</span>';
                    else if (item.approval_status == 'approved_supervisor') statusBadge = '<span class="badge bg-success">Approved</span>';
                    else if (item.approval_status == 'rejected') statusBadge = '<span class="badge bg-danger">Rejected</span>';
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
                    if (first.approval_status === 'draft') statusText = '<span class="badge bg-warning p-2"><i class="ri-time-line me-1"></i> Minggu ini: Draft</span>';
                    else if (first.approval_status === 'submitted') statusText = '<span class="badge bg-info p-2"><i class="ri-loader-4-line me-1"></i> Minggu ini: Menunggu Approval</span>';
                    else if (first.approval_status === 'approved_supervisor') statusText = '<span class="badge bg-success p-2"><i class="ri-checkbox-circle-line me-1"></i> Minggu ini: Approved</span>';
                    else if (first.approval_status === 'rejected') statusText = '<span class="badge bg-danger p-2"><i class="ri-close-circle-line me-1"></i> Minggu ini: Rejected</span>';

                    $('#weeklyStatusContainer').html(statusText);
                } else {
                    $('#weeklyStatusContainer').empty();
                }
            }
        });

        loadCollected();
    }

    function loadCollected() {
        let container = $('#collectedWeeksContainer');
        if (container.length == 0) return;

        $.ajax({
            url: "{{ route('compressor.get-collected') }}",
            type: "GET",
            success: function(res) {
                container.empty();
                if (res.results && res.results.length > 0) {
                    res.results.forEach(weekData => {
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
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info" onclick="showDetail(${item.id})"><i class="ri-eye-line"></i></button>
                                        <button class="btn btn-sm btn-primary" onclick="editData(${item.id})"><i class="ri-edit-line"></i></button>
                                    </td>
                                </tr>
                            `;
                        });

                        let cardHtml = `
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                                        <div class="card-header bg-soft-warning border-0 d-flex justify-content-between align-items-center py-3">
                                            <div>
                                                <h5 class="card-title mb-0 text-warning-emphasis fw-bold">
                                                    <i class="ri-history-line me-2"></i> Data Terkumpul Minggu ke-${app.week} (${moment(app.tgl_awal).format('DD MMM')} - ${moment(app.tgl_akhir).format('DD MMM YYYY')})
                                                </h5>
                                                <p class="mb-0 text-muted small">Silakan kirim approval jika data minggu ini sudah lengkap.</p>
                                            </div>
                                            <div>
                                                <button class="btn btn-warning fw-bold px-4 shadow-sm" onclick="openSubmitWeekly(${app.week}, ${app.bulan}, ${app.tahun})">
                                                    <i class="ri-send-plane-fill me-1"></i> Kirim Approval W${app.week}
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover align-middle mb-0">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Jam</th>
                                                            <th class="text-center">Pr. Outlet (1/2/3/4)</th>
                                                            <th class="text-center">El. Outlet (1/2/4)</th>
                                                            <th class="text-center">Load (%)</th>
                                                            <th class="text-center">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ${dataRows}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.append(cardHtml);
                    });
                }
            }
        });
    }

    function openSubmitWeekly(week, bulan, tahun) {
        $('#sw_week').val(week);
        $('#sw_bulan').val(bulan);
        $('#sw_tahun').val(tahun);

        // Load approvers
        $.ajax({
            url: "{{ route('warming-up-genset.get-approver') }}",
            type: "GET",
            success: function(res) {
                let staff = res.staff;
                $('#sw_foreman_id').empty().append('<option value="">-- Pilih Foreman --</option>');
                $('#sw_supervisor_id').empty().append('<option value="">-- Pilih Supervisor --</option>');

                staff.forEach(item => {
                    if (item.username.toLowerCase().includes('foreman')) {
                        $('#sw_foreman_id').append(`<option value="${item.id}">${item.username}</option>`);
                    }
                    if (item.username.toLowerCase().includes('supervisor')) {
                        $('#sw_supervisor_id').append(`<option value="${item.id}">${item.username}</option>`);
                    }
                });
            }
        });

        $('#modalSubmitWeekly').modal('show');
    }

    function processSubmitWeekly() {
        let data = $('#formSubmitWeekly').serialize();

        if (!$('#sw_foreman_id').val() || !$('#sw_supervisor_id').val()) {
            Swal.fire('Peringatan', 'Harap pilih Foreman dan Supervisor', 'warning');
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
                        <div class="col-6"><strong>Tanggal:</strong> ${data.tanggal}</div>
                        <div class="col-6"><strong>Jam:</strong> ${data.jam}</div>
                        <div class="col-12"><hr></div>
                        <div class="col-6"><strong>Pressure Outlet 1:</strong> ${formatNum(data.pressure_outlet_1)}</div>
                        <div class="col-6"><strong>Pressure Outlet 2:</strong> ${formatNum(data.pressure_outlet_2)}</div>
                        <div class="col-6"><strong>Pressure Outlet 3:</strong> ${formatNum(data.pressure_outlet_3)}</div>
                        <div class="col-6"><strong>Pressure Outlet 4:</strong> ${formatNum(data.pressure_outlet_4)}</div>
                        <div class="col-12 mt-2"><span class="badge bg-soft-success text-success">Element Outlet (°C)</span></div>
                        <div class="col-6 mt-1"><strong>Element Outlet 1:</strong> ${formatNum(data.element_outlet_1)}</div>
                        <div class="col-6 mt-1"><strong>Element Outlet 2:</strong> ${formatNum(data.element_outlet_2)}</div>
                        <div class="col-6 mt-1"><strong>Element Outlet 4:</strong> ${formatNum(data.element_outlet_4)}</div>
                        <div class="col-6 mt-1"><strong>Load %:</strong> ${formatNum(data.load_percent)}</div>
                        
                        <div class="col-12 mt-2"><span class="badge bg-soft-info text-info">Running Hour</span></div>
                        <div class="col-6 mt-1"><strong>RH 1:</strong> ${formatNum(data.running_hour_1)}</div>
                        <div class="col-6 mt-1"><strong>RH 2:</strong> ${formatNum(data.running_hour_2)}</div>
                        <div class="col-6 mt-1"><strong>RH 3:</strong> ${formatNum(data.running_hour_3)}</div>
                        <div class="col-6 mt-1"><strong>RH 4:</strong> ${formatNum(data.running_hour_4)}</div>

                        <div class="col-12 mt-2"><span class="badge bg-soft-dark text-dark">Motor Start</span></div>
                        <div class="col-6 mt-1"><strong>Start 1:</strong> ${formatNum(data.motor_start_1)}</div>
                        <div class="col-6 mt-1"><strong>Start 2:</strong> ${formatNum(data.motor_start_2)}</div>
                        <div class="col-6 mt-1"><strong>Start 3:</strong> ${formatNum(data.motor_start_3)}</div>
                        <div class="col-6 mt-1"><strong>Start 4:</strong> ${formatNum(data.motor_start_4)}</div>

                        <div class="col-12 mt-2"><span class="badge bg-soft-secondary text-secondary">Lain-lain</span></div>
                        <div class="col-6 mt-1"><strong>Accumulated Volume:</strong> ${formatNum(data.accumulated_volume)}</div>
                        <div class="col-6 mt-1"><strong>Temp Comp IR:</strong> ${formatNum(data.temperature_comp_ir)}</div>
                        <div class="col-6 mt-1"><strong>Pressure In:</strong> ${formatNum(data.pressure_in)}</div>
                        <div class="col-6 mt-1"><strong>Pressure Out:</strong> ${formatNum(data.pressure_out)}</div>

                        <div class="col-12 mt-2"><span class="badge bg-soft-warning text-warning">Suhu Dryer (°C)</span></div>
                        <div class="col-4 mt-1"><strong>TR15:</strong> ${formatNum(data.suhu_dryer_tr15)}</div>
                        <div class="col-4 mt-1"><strong>FX250:</strong> ${formatNum(data.suhu_dryer_fx250)}</div>
                        <div class="col-4 mt-1"><strong>IR:</strong> ${formatNum(data.suhu_dryer_ir)}</div>
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
            $('#paginationInfo').html(`Showing <b>${pagination.total > 0 ? (pagination.current_page - 1) * pagination.per_page + 1 : 0}</b> to <b>${Math.min(pagination.current_page * pagination.per_page, pagination.total)}</b> of <b>${pagination.total}</b> entries`);
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
            const params = new URLSearchParams({
                bulan: $('#filter_bulan').val(),
            });

            $.ajax({
                url: EXPORT_URL + '?' + params.toString(),
                method: 'GET',
                success: function(res) {
                    if (res.data.length === 0) {
                        Swal.fire('Tidak ada data', 'Tidak ada data untuk diekspor',
                            'info');
                        return;
                    }

                    // Buat CSV
                    let headers = [
                        'Tanggal', 'Jam', 'Operator',
                        'P.Outlet 1', 'P.Outlet 2', 'P.Outlet 3', 'P.Outlet 4',
                        'E.Outlet 1', 'E.Outlet 2', 'E.Outlet 4',
                        'Load %',
                        'RH 1', 'RH 2', 'RH 3', 'RH 4',
                        'Start 1', 'Start 2', 'Start 3', 'Start 4',
                        'Acc Vol', 'Temp Comp IR', 'P.In', 'P.Out',
                        'TR15', 'FX250', 'IR', 'Status Approval'
                    ];

                    let csv = headers.join(',') + '\n';

                    const formatNum = (v) => v ? Number(v) : '-';

                    res.data.forEach(item => {
                        let row = [
                            item.tanggal,
                            item.jam,
                            item.compressor?.operator?.username || '-',
                            formatNum(item.pressure_outlet_1),
                            formatNum(item.pressure_outlet_2),
                            formatNum(item.pressure_outlet_3),
                            formatNum(item.pressure_outlet_4),
                            formatNum(item.element_outlet_1),
                            formatNum(item.element_outlet_2),
                            formatNum(item.element_outlet_4),
                            formatNum(item.load_percent),
                            formatNum(item.running_hour_1),
                            formatNum(item.running_hour_2),
                            formatNum(item.running_hour_3),
                            formatNum(item.running_hour_4),
                            formatNum(item.motor_start_1),
                            formatNum(item.motor_start_2),
                            formatNum(item.motor_start_3),
                            formatNum(item.motor_start_4),
                            formatNum(item.accumulated_volume),
                            formatNum(item.temperature_comp_ir),
                            formatNum(item.pressure_in),
                            formatNum(item.pressure_out),
                            formatNum(item.suhu_dryer_tr15),
                            formatNum(item.suhu_dryer_fx250),
                            formatNum(item.suhu_dryer_ir),
                            item.compressor?.status || '-'
                        ];
                        csv += row.map(v => `"${v}"`).join(',') + '\n';
                    });

                    // Download CSV
                    const element = document.createElement('a');
                    element.setAttribute('href', 'data:text/csv;charset=utf-8,' +
                        encodeURIComponent(csv));
                    element.setAttribute('download',
                        `compressor-rekap-${new Date().toISOString().split('T')[0]}.csv`
                    );
                    element.style.display = 'none';
                    document.body.appendChild(element);
                    element.click();
                    document.body.removeChild(element);

                    Swal.fire('Berhasil', 'Data berhasil diekspor', 'success');
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Gagal mengekspor data', 'error');
                }
            });
        });
    });
</script>
@endsection