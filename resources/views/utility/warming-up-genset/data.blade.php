@extends('layouts.app')

@section('title', 'Warming Up Genset — Data')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="card border-0 shadow-sm mb-3"
            style="background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%); border-radius: 12px;">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>
                    <h4 class="text-white fw-bold mb-1">
                        <i class="ri-battery-2-charge-fill text-warning me-2"></i>
                        Warming Up Genset
                    </h4>
                    <p class="text-white-50 mb-0 small">
                        Engineering Utility · Monitoring laporan genset
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('warming-up-genset.index') }}" class="btn btn-warning btn-sm rounded-pill px-3">
                        <i class="ri-add-line me-1"></i> Input
                    </a>

                    <button class="btn btn-outline-light btn-sm rounded-pill px-3" id="btnExport">
                        <i class="ri-download-2-line me-1"></i> Export
                    </button>
                </div>

            </div>
        </div>

        {{-- 🔍 FILTER --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Bulan & Tahun</label>
                        <input type="month" id="filterBulan" class="form-control" value="{{ date('Y-m') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select id="filterStatus" class="form-select">
                            <option value="">-- Semua Status --</option>
                            <option value="submitted">Submitted</option>
                            <option value="approved_foreman">Approved Foreman</option>
                            <option value="approved_supervisor">Approved Supervisor</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-warning w-100" id="btnFilter">
                            <i class="ri-filter-3-line me-1"></i> Terapkan Filter
                        </button>
                    </div>

                </div>

            </div>
        </div>

        {{-- 📊 TABLE --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="ri-table-2 me-1 text-info"></i> Data Laporan
                </h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="tblData" class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Operator</th>
                                <th>Engine Speed</th>
                                <th>Temp</th>
                                <th>Oil</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="tableBody">
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="ri-inbox-line fs-3 d-block mb-2"></i>
                                    Belum ada data. Silakan filter terlebih dahulu.
                                </td>
                            </tr>
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

{{-- 📄 MODAL DETAIL --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Detail Laporan Warming Up Genset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailContent">
                <!-- Data akan dimuat di sini -->
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- 📝 MODAL EDIT --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit Laporan Warming Up Genset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Laporan</label>
                            <input type="date" name="tanggal_laporan" id="edit_tanggal_laporan" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jam Pencatatan</label>
                            <input type="text" name="jam_pencatatan" id="edit_jam_pencatatan" class="form-control" placeholder="HH:MM" readonly required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Foreman</label>
                            <select name="foreman_id" id="edit_foreman_id" class="form-select" required>
                                <option value="">-- Pilih Foreman --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Supervisor</label>
                            <select name="supervisor_id" id="edit_supervisor_id" class="form-select" required>
                                <option value="">-- Pilih Supervisor --</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <hr class="my-2 opacity-50">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Engine Speed (RPM)</label>
                            <input type="number" step="0.01" name="engine_speed" id="edit_engine_speed" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Engine Temp (°C)</label>
                            <input type="number" step="0.01" name="engine_temperature" id="edit_engine_temperature" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Oil Press (Bar)</label>
                            <input type="number" step="0.01" name="engine_oil_pressure" id="edit_engine_oil_pressure" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Battery (V)</label>
                            <input type="number" step="0.01" name="battery_voltage" id="edit_battery_voltage" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Charge Alt (V)</label>
                            <input type="number" step="0.01" name="charge_alt_voltage" id="edit_charge_alt_voltage" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Freq (Hz)</label>
                            <input type="number" step="0.01" name="frequency" id="edit_frequency" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">RH (Hours)</label>
                            <input type="number" step="0.01" name="running_hour" id="edit_running_hour" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Oil Status 1</label>
                            <input type="number" step="0.01" name="status_oil_1" id="edit_status_oil_1" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Oil Status 2</label>
                            <input type="number" step="0.01" name="status_oil_2" id="edit_status_oil_2" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 🔄 LOADING --}}
<div class="loading-overlay d-none" id="loadingOverlay">
    <div class="spinner-border text-warning" role="status"></div>
</div>
@endsection

@section('scripts')
<script>
    const API_URL = "{{ route('warming-up-genset.json') }}";
    const SHOW_URL = "{{ url('utility/warming-up-genset/json') }}";
    let cachedData = [];

    $(document).ready(function() {
        loadData(1);

        // Initialize flatpickr for edit modal jam
        flatpickr('#edit_jam_pencatatan', {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true,
            minuteIncrement: 1,
            allowInput: false,
        });

        // ── Load data on filter ──
        $('#btnFilter').on('click', function() {
            loadData(1);
        });

        // ── Export ──
        $('#btnExport').on('click', function() {
            const params = new URLSearchParams({
                bulan: $('#filterBulan').val(),
            });

            $.ajax({
                url: API_URL.replace('/json', '/export') + '?' + params.toString(),
                method: 'GET',
                success: function(res) {
                    if (res.data.length === 0) {
                        Swal.fire('Tidak ada data', 'Tidak ada data untuk diekspor',
                            'info');
                        return;
                    }

                    // Buat CSV
                    let csv =
                        'Tanggal,Jam Pencatatan,Operator,Engine Speed,Engine Temp,Oil Pressure,Battery Voltage,Charge Alt Voltage,Running Hour,Frequency,Status Oil 1,Status Oil 2,Status\n';

                    res.data.forEach(item => {
                        csv +=
                            `"${item.tanggal_laporan}","${item.jam_pencatatan}","${item.operator?.username || '-'}","${item.engine_speed || '-'}","${item.engine_temperature || '-'}","${item.engine_oil_pressure || '-'}","${item.battery_voltage || '-'}","${item.charge_alt_voltage || '-'}","${item.running_hour || '-'}","${item.frequency || '-'}","${item.status_oil_1 || '-'}","${item.status_oil_2 || '-'}","${item.status}"\n`;
                    });

                    // Download CSV
                    const element = document.createElement('a');
                    element.setAttribute('href', 'data:text/csv;charset=utf-8,' +
                        encodeURIComponent(csv));
                    element.setAttribute('download',
                        `warming-up-genset-${new Date().toISOString().split('T')[0]}.csv`
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

        // ── Load Data Function ──
        function loadData(page = 1) {
            showLoading(true);

            const params = new URLSearchParams({
                bulan: $('#filterBulan').val(),
                status: $('#filterStatus').val(),
                page: page,
                per_page: 15
            });

            $.ajax({
                url: API_URL + '?' + params.toString(),
                method: 'GET',
                success: function(res) {
                    cachedData = res.data;
                    renderTable(res.data, res.pagination);
                    renderPagination(res.pagination);
                    showLoading(false);
                },
                error: function(xhr) {
                    showLoading(false);
                    let msg = 'Gagal memuat data';
                    if (xhr.responseJSON?.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        }

        function renderTable(data, pagination) {
            let html = '';

            if (data.length === 0) {
                html =
                    '<tr><td colspan="9" class="text-center py-5 text-muted"><i class="ri-inbox-line fs-3 d-block mb-2"></i>Tidak ada data</td></tr>';
                $('#tableBody').html(html);
                return;
            }

            const formatNum = (v) => v ? Number(v) : '-';
            data.forEach((item, idx) => {
                const statusBadge = getStatusBadge(item.status);
                const no = ((pagination.current_page - 1) * pagination.per_page) + (idx + 1);

                html += `
                    <tr>
                        <td class="text-center">${no}</td>
                        <td>${formatDate(item.tanggal_laporan)}</td>
                        <td>${item.jam_pencatatan || '-'}</td>
                        <td>${item.operator?.username || '-'}</td>
                        <td>${formatNum(item.engine_speed)}</td>
                        <td>${formatNum(item.engine_temperature)}</td>
                        <td>${formatNum(item.engine_oil_pressure)}</td>
                        <td>${statusBadge}</td>
                        <td class="text-center">
                            <div>
                                <button class="btn btn-sm btn-outline-primary btn-detail" data-id="${item.id}" title="Detail">
                                    <i class="ri-eye-line"></i>
                                </button>
                                ${['submitted', 'rejected'].includes(item.status) ? `
                                <button class="btn btn-sm btn-outline-warning btn-edit" data-id="${item.id}" title="Edit">
                                    <i class="ri-edit-line"></i>
                                </button>` : ''}
                            </div>
                        </td>
                    </tr>
                    `;
            });

            $('#tableBody').html(html);
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

        function getStatusBadge(status) {
            const badges = {
                'submitted': '<span class="badge bg-info">Submitted</span>',
                'approved_foreman': '<span class="badge bg-warning text-dark">Approved Foreman</span>',
                'approved_supervisor': '<span class="badge bg-success">Approved Supervisor</span>',
                'rejected': '<span class="badge bg-danger">Rejected</span>'
            };
            return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
        }

        function formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        function showLoading(show) {
            if (show) {
                $('#loadingOverlay').removeClass('d-none');
            } else {
                $('#loadingOverlay').addClass('d-none');
            }
        }

        $(document).on('click', '.btn-detail', function() {
            const id = $(this).data('id');
            // Cari di cache dulu
            const data = cachedData.find(item => item.id == id);

            if (data) {
                renderDetail(data);
                $('#modalDetail').modal('show');
            } else {
                // Jika tidak ada di cache, get dari server
                showLoading(true);
                $.ajax({
                    url: `${SHOW_URL}/${id}`,
                    method: 'GET',
                    success: function(res) {
                        showLoading(false);
                        renderDetail(res.data);
                        $('#modalDetail').modal('show');
                    },
                    error: function(xhr) {
                        showLoading(false);
                        Swal.fire('Error', 'Gagal memuat detail data', 'error');
                    }
                });
            }
        });

        // ── Handle Edit Button ──
        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            const data = cachedData.find(item => item.id == id);

            if (data) {
                openEditModal(data);
            } else {
                showLoading(true);
                $.ajax({
                    url: `${SHOW_URL}/${id}`,
                    method: 'GET',
                    success: function(res) {
                        showLoading(false);
                        openEditModal(res.data);
                    },
                    error: function() {
                        showLoading(false);
                        Swal.fire('Error', 'Gagal memuat data', 'error');
                    }
                });
            }
        });

        function openEditModal(data) {
            const formatNum = (v) => v ? Number(v) : '';

            $('#edit_id').val(data.id);
            $('#edit_tanggal_laporan').val(data.tanggal_laporan.substring(0, 10));
            $('#edit_jam_pencatatan').val(data.jam_pencatatan);

            // Set fields with formatNum
            $('#edit_engine_speed').val(formatNum(data.engine_speed));
            $('#edit_engine_temperature').val(formatNum(data.engine_temperature));
            $('#edit_engine_oil_pressure').val(formatNum(data.engine_oil_pressure));
            $('#edit_battery_voltage').val(formatNum(data.battery_voltage));
            $('#edit_charge_alt_voltage').val(formatNum(data.charge_alt_voltage));
            $('#edit_frequency').val(formatNum(data.frequency));
            $('#edit_running_hour').val(formatNum(data.running_hour));
            $('#edit_status_oil_1').val(formatNum(data.status_oil_1));
            $('#edit_status_oil_2').val(formatNum(data.status_oil_2));

            loadApprovers(data.foreman_id, data.supervisor_id);
            $('#modalEdit').modal('show');
        }

        let approversLoaded = false;

        function loadApprovers(selectedForeman = null, selectedSupervisor = null) {
            // Kita load setiap kali buka untuk memastikan data terbaru, 
            // tapi bisa juga di-cache jika data jarang berubah.
            $.ajax({
                url: '/api/utility/users/approvers',
                method: 'GET',
                success: function(res) {
                    const staff = res.staff || [];
                    let foremanOpts = '<option value="">-- Pilih Foreman --</option>';
                    let supervisorOpts = '<option value="">-- Pilih Supervisor --</option>';

                    staff.forEach(item => {
                        const isForeman = item.username.toLowerCase().includes('foreman');
                        const isSupervisor = item.username.toLowerCase().includes('supervisor');

                        if (isForeman) {
                            foremanOpts += `<option value="${item.id}" ${item.id == selectedForeman ? 'selected' : ''}>${item.username}</option>`;
                        }
                        if (isSupervisor) {
                            supervisorOpts += `<option value="${item.id}" ${item.id == selectedSupervisor ? 'selected' : ''}>${item.username}</option>`;
                        }
                    });

                    $('#edit_foreman_id').html(foremanOpts);
                    $('#edit_supervisor_id').html(supervisorOpts);
                }
            });
        }

        $('#formEdit').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_id').val();
            const formData = $(this).serialize();

            showLoading(true);
            $.ajax({
                url: `${SHOW_URL}/${id}`,
                method: 'POST',
                data: formData,
                success: function(res) {
                    showLoading(false);
                    Swal.fire('Berhasil', res.message, 'success');
                    $('#modalEdit').modal('hide');
                    loadData(1); // Refresh table
                },
                error: function(xhr) {
                    showLoading(false);
                    let msg = 'Gagal menyimpan perubahan';
                    if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                    if (xhr.responseJSON?.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    Swal.fire('Gagal', msg, 'error');
                }
            });
        });

        function renderDetail(data) {
            console.log(data);
            const statusBadge = getStatusBadge(data.status);

            const html = `
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Informasi Dasar</label>
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted w-40">Tanggal</td><td class="fw-semibold">: ${formatDate(data.tanggal_laporan)}</td></tr>
                            <tr><td class="text-muted">Jam</td><td>: ${data.jam_pencatatan || '-'}</td></tr>
                            <tr><td class="text-muted">Operator</td><td>: ${data.operator?.username || '-'}</td></tr>
                            <tr><td class="text-muted">Status</td><td>: ${statusBadge}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Approval</label>
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted w-40">Foreman</td><td>: ${data.foreman?.username || '-'}</td></tr>
                            <tr><td class="text-muted">Approved at</td><td>: ${data.approved_foreman_at ?? '-'}</td></tr>
                            <tr><td class="text-muted">Supervisor</td><td>: ${data.supervisor?.username || '-'}</td></tr>
                            <tr><td class="text-muted">Approved at</td><td>: ${data.approved_supervisor_at ?? '-'}</td></tr>
                        </table>
                    </div>
                    <div class="col-12">
                        <hr class="my-2 opacity-50">
                        <label class="text-muted small mb-3">Monitoring Teknis</label>
                        <div class="row g-3">
                            ${renderDetailItem('Speed', data.engine_speed, 'RPM')}
                            ${renderDetailItem('Temp', data.engine_temperature, '°C')}
                            ${renderDetailItem('Oil Press', data.engine_oil_pressure, 'Bar')}
                            ${renderDetailItem('Battery', data.battery_voltage, 'V')}
                            ${renderDetailItem('Charge Alt', data.charge_alt_voltage, 'V')}
                            ${renderDetailItem('Freq', data.frequency, 'Hz')}
                            ${renderDetailItem('RH', data.running_hour, 'Hours')}
                            ${renderDetailItem('Oil Status 1', data.status_oil_1, '')}
                            ${renderDetailItem('Oil Status 2', data.status_oil_2, '')}
                        </div>
                    </div>
                </div>
            `;
            $('#detailContent').html(html);
        }

        function renderDetailItem(label, value, unit) {
            const formatNum = (v) => v ? Number(v) : '-';
            return `
            <div class="col-6 col-md-3">
                <div class="p-2 border rounded bg-light">
                    <div class="text-muted small">${label}</div>
                    <div class="fw-bold">${formatNum(value)} <small class="text-muted fw-normal">${unit}</small></div>
                </div>
            </div>
        `;
        }
    });
</script>
@endsection