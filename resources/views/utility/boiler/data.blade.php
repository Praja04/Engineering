@extends('layouts.app')

@section('title', 'Rekap Boiler Logs')

@section('styles')
    <style>
        .card-header-custom {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            padding: 1.25rem;
            border-radius: 12px 12px 0 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #6c757d;
            border-top: none;
        }

        .scrollable-detail-table {
            max-height: 450px;
            overflow-y: auto;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
        }
        
        .scrollable-detail-table table {
            margin-bottom: 0;
        }

        .scrollable-detail-table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: #343a40;
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 text-white fw-bold">
                            <i class="ri-dashboard-3-line me-2 text-warning"></i>
                            Rekap Boiler Logs Harian
                        </h4>
                        <p class="mb-0 text-white-50 small">Engineering Utility · Riwayat Log Sensor Harian (06:00 - 06:00)</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-warning btn-sm rounded-pill px-3" id="btnSync">
                            <i class="ri-refresh-line me-1"></i> Sinkron Sensor
                        </button>
                        <button class="btn btn-outline-light btn-sm rounded-pill px-3" id="btnExport">
                            <i class="ri-download-2-line me-1"></i> Export Excel
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    {{-- Filter --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Bulan & Tahun</label>
                            <input type="month" id="filterBulan" class="form-control" value="{{ date('Y-m') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Status Approval</label>
                            <select id="filterStatus" class="form-select">
                                <option value="">-- Semua Status --</option>
                                <option value="draft">Draft</option>
                                <option value="waiting_supervisor">Waiting Supervisor</option>
                                <option value="approved_supervisor">Approved</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="btnFilter">
                                <i class="ri-filter-3-line me-1"></i> Terapkan Filter
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Foreman Pengaju</th>
                                    <th>Supervisor Penyetuju</th>
                                    <th class="text-center">Total Jam Data</th>
                                    <th class="text-center">Avg PV Steam (Bar)</th>
                                    <th class="text-center">Avg Suhu Feed Tank (°C)</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                                        Memuat data...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div id="paginationInfo" class="small text-muted"></div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0" id="paginationLinks"></ul>
                        </nav>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Export Excel -->
    <div class="modal fade" id="modalExport" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white"><i class="ri-file-excel-2-line me-1"></i> Export Excel Boiler Log</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formExport" action="{{ route('boiler-logs.export') }}" method="GET" target="_blank">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Tanggal Operasional</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="alert alert-info mb-0" style="font-size: 0.85rem;">
                            <i class="ri-information-line me-1"></i> File Excel yang diunduh mencakup log per jam (06:00 s.d 06:00 hari berikutnya) beserta tanda tangan digital jika laporan sudah disetujui.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="formExport" class="btn btn-success px-4">
                        <i class="ri-download-cloud-2-line me-1"></i> Download Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Submit Approval -->
    <div class="modal fade" id="modalSubmit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white"><i class="ri-send-plane-line me-1"></i> Ajukan Approval Harian</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formSubmit">
                        <input type="hidden" id="submit_id">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Foreman Pengaju</label>
                            <select id="submit_foreman" name="foreman_id" class="form-select" required>
                                <option value="">-- Pilih Foreman --</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Supervisor Penyetuju</label>
                            <select id="submit_supervisor" name="supervisor_id" class="form-select" required>
                                <option value="">-- Pilih Supervisor --</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnConfirmSubmit" class="btn btn-primary px-4">
                        <i class="ri-check-line me-1"></i> Kirim Pengajuan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Boiler Log 24 Jam -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom p-3">
                    <h5 class="modal-title fw-bold"><i class="ri-article-line text-primary me-2"></i>Detail Log Boiler & Status Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border-start border-primary border-3">
                                <h6 class="fw-bold mb-2">Informasi Umum</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" width="40%">Tanggal Operasional</td><td>: <span id="detail_tanggal" class="fw-bold"></span></td></tr>
                                    <tr><td class="text-muted">Total Jam Tercatat</td><td>: <span id="detail_total_jam"></span> jam</td></tr>
                                    <tr><td class="text-muted">Status Approval</td><td>: <span id="detail_status"></span></td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border-start border-warning border-3">
                                <h6 class="fw-bold mb-2">Pihak Berwenang & Tanda Tangan</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" width="40%">Diajukan (Foreman)</td><td>: <span id="detail_foreman"></span> <small class="text-muted" id="detail_submitted_at"></small></td></tr>
                                    <tr><td class="text-muted">Disetujui (Supervisor)</td><td>: <span id="detail_supervisor"></span> <small class="text-muted" id="detail_approved_at"></small></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3"><i class="ri-table-line text-info me-2"></i>Tabel Log Data Sensor Per Jam</h6>
                    
                    <div class="scrollable-detail-table">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Waktu</th>
                                    <th>PV Steam</th>
                                    <th>Feed Press</th>
                                    <th>Press Past.</th>
                                    <th>Level Feed W.</th>
                                    <th>WH Flow Rate</th>
                                    <th>WH Total Count</th>
                                    <th>Inlet Flow</th>
                                    <th>Outlet Flow</th>
                                    <th>Suhu Feed T.</th>
                                    <th>ID Fan</th>
                                    <th>LH FD Fan</th>
                                    <th>RH FD Fan</th>
                                    <th>LH Stoker</th>
                                    <th>RH Stoker</th>
                                    <th>WF Total Count</th>
                                    <th>LH Temp</th>
                                    <th>RH Temp</th>
                                    <th>O2</th>
                                    <th>CO2</th>
                                    <th>Flue Gass Temp</th>
                                    <th>LH Guil.</th>
                                    <th>RH Guil.</th>
                                    <th>Pump 1</th>
                                    <th>Pump 2</th>
                                    <th>BatuBara FK</th>
                                    <th>Steam FK</th>
                                    @if(in_array(Auth::user()->jabatan, ['foreman', 'supervisor', 'admin']))
                                    <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody id="detailTableBody">
                                {{-- Data sensor via ajax --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal Edit Log Boiler -->
    <div class="modal fade" id="modalEditLog" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white"><i class="ri-edit-box-line me-1"></i> Edit Log Boiler - <span id="edit_log_waktu"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formEditLog">
                        <input type="hidden" id="edit_log_id" name="id">
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">PV Steam (Bar)</label>
                                <input type="number" step="any" name="PVSteam" id="edit_PVSteam" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Feed Press (Bar)</label>
                                <input type="number" step="any" name="FeedPressure" id="edit_FeedPressure" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Press Past. (Bar)</label>
                                <input type="number" step="any" name="Press_Pasteur" id="edit_Press_Pasteur" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Level Feed W. (%)</label>
                                <input type="number" step="any" name="LevelFeedWater" id="edit_LevelFeedWater" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Inlet Flow (m3/h)</label>
                                <input type="number" step="any" name="InletWaterFlow" id="edit_InletWaterFlow" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Outlet Flow (ton/h)</label>
                                <input type="number" step="any" name="OutletSteamFlow" id="edit_OutletSteamFlow" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Suhu Feed Tank (°C)</label>
                                <input type="number" step="any" name="SuhuFeedTank" id="edit_SuhuFeedTank" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">ID Fan (rpm)</label>
                                <input type="number" step="any" name="IDFan" id="edit_IDFan" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">LH FD Fan (rpm)</label>
                                <input type="number" step="any" name="LHFDFan" id="edit_LHFDFan" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">RH FD Fan (rpm)</label>
                                <input type="number" step="any" name="RHFDFan" id="edit_RHFDFan" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">LH Stoker (rpm)</label>
                                <input type="number" step="any" name="LHStoker" id="edit_LHStoker" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">RH Stoker (rpm)</label>
                                <input type="number" step="any" name="RHStoker" id="edit_RHStoker" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">LH Temp (°C)</label>
                                <input type="number" step="any" name="LHTemp" id="edit_LHTemp" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">RH Temp (°C)</label>
                                <input type="number" step="any" name="RHTemp" id="edit_RHTemp" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">O2 (%)</label>
                                <input type="number" step="any" name="O2" id="edit_O2" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">CO2 (%)</label>
                                <input type="number" step="any" name="CO2" id="edit_CO2" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">LH Guiloutine (mm)</label>
                                <input type="number" step="any" name="LHGuiloutine" id="edit_LHGuiloutine" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">RH Guiloutine (mm)</label>
                                <input type="number" step="any" name="RHGuiloutine" id="edit_RHGuiloutine" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Water Pump 1 (Hz)</label>
                                <input type="number" step="any" name="WaterPump1" id="edit_WaterPump1" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Water Pump 2 (Hz)</label>
                                <input type="number" step="any" name="WaterPump2" id="edit_WaterPump2" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Batubara FK</label>
                                <input type="number" step="any" name="Batubara_FK" id="edit_Batubara_FK" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Steam FK</label>
                                <input type="number" step="any" name="Steam_FK" id="edit_Steam_FK" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Water Flow - Total Count (m3/h)</label>
                                <input type="number" step="any" name="water_flow_total" id="edit_water_flow_total" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Water HMI - Flow Rate (m3/h)</label>
                                <input type="number" step="any" name="water_hmi_flow_rate" id="edit_water_hmi_flow_rate" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Water HMI - Total Count (m3)</label>
                                <input type="number" step="any" name="water_hmi_total" id="edit_water_hmi_total" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Temp Flue Gass (°C)</label>
                                <input type="number" step="any" name="flue_gass_temp" id="edit_flue_gass_temp" class="form-control form-control-sm">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnCancelEditLog">Batal</button>
                    <button type="button" id="btnSaveEditLog" class="btn btn-info px-4 text-white">
                        <i class="ri-save-line me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Load list data
            loadData(1);

            // Fetch users list for dropdown submit
            let usersLoaded = false;
            function loadUsers() {
                if (usersLoaded) return;
                $.get("{{ route('boiler-logs.users') }}", function(res) {
                    if (res.status === 200) {
                        let opForemen = '<option value="">-- Pilih Foreman --</option>';
                        let opSupervisors = '<option value="">-- Pilih Supervisor --</option>';
                        
                        res.foremen.forEach(user => {
                            opForemen += `<option value="${user.id}">${user.username}</option>`;
                        });
                        
                        res.supervisors.forEach(user => {
                            opSupervisors += `<option value="${user.id}">${user.username}</option>`;
                        });

                        $('#submit_foreman').html(opForemen);
                        $('#submit_supervisor').html(opSupervisors);
                        
                        // Set default selection to logged-in user if foreman
                        const currentUserId = "{{ Auth::user()->id }}";
                        const currentUserJabatan = "{{ Auth::user()->jabatan }}";
                        if (currentUserJabatan === 'foreman') {
                            $('#submit_foreman').val(currentUserId);
                        }

                        usersLoaded = true;
                    }
                });
            }

            // Click Terapkan Filter
            $('#btnFilter').click(function() {
                loadData(1);
            });

            function loadData(page) {
                const perPage = 15;
                const status = $('#filterStatus').val();
                const bulan = $('#filterBulan').val();

                $('#tableBody').html(`
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                            Memuat data...
                        </td>
                    </tr>
                `);

                $.get("{{ route('boiler-logs.json') }}", {
                    page: page,
                    per_page: perPage,
                    status: status,
                    bulan: bulan
                }, function(res) {
                    if (res.status === 200) {
                        renderTable(res.data, (page - 1) * perPage);
                        renderPagination(res.pagination);
                    }
                });
            }

            function renderTable(data, startNo) {
                let html = '';
                if (data.length === 0) {
                    html = '<tr><td colspan="9" class="text-center py-5 text-muted">Tidak ada data ditemukan.</td></tr>';
                } else {
                    data.forEach((item, index) => {
                        let actionButtons = `
                            <button class="btn btn-sm btn-outline-info btn-detail"
                                data-id="${item.id}" title="Detail">
                                <i class="ri-eye-line"></i>
                            </button>
                        `;

                        // Show submit button only for draft
                        if (item.status === 'draft') {
                            actionButtons += `
                                <button class="btn btn-sm btn-outline-primary btn-submit-approval"
                                    data-id="${item.id}" title="Ajukan Approval">
                                    <i class="ri-send-plane-line"></i>
                                </button>
                            `;
                        } else {
                            actionButtons += `
                                <button class="btn btn-sm btn-outline-secondary" disabled title="Terkunci">
                                    <i class="ri-lock-line"></i>
                                </button>
                            `;
                        }

                        html += `
                            <tr>
                                <td>${startNo + index + 1}</td>
                                <td>${formatDate(item.tanggal)}</td>
                                <td>${item.foreman?.username || '-'}</td>
                                <td>${item.supervisor?.username || '-'}</td>
                                <td class="text-center fw-bold">${item.total_logs} / 25</td>
                                <td class="text-center">${formatNum(item.avg_steam)}</td>
                                <td class="text-center">${formatNum(item.avg_temp)}</td>
                                <td>${getStatusBadge(item.status)}</td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        ${actionButtons}
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#tableBody').html(html);
            }

            function renderPagination(pagination) {
                let html = '';
                if (pagination.last_page > 1) {
                    for (let i = 1; i <= pagination.last_page; i++) {
                        html += `
                        <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                    }
                }
                $('#paginationLinks').html(html);
                $('#paginationInfo').text(`Showing data ${dataRange(pagination)}`);
            }

            function dataRange(p) {
                if (p.total === 0) return '0';
                const start = (p.current_page - 1) * p.per_page + 1;
                const end = Math.min(p.current_page * p.per_page, p.total);
                return `${start} - ${end} of ${p.total}`;
            }

            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                loadData($(this).data('page'));
            });

            // ── View Detail ──
            let currentDetailApprovalId = null;
            $(document).on('click', '.btn-detail', function() {
                const id = $(this).data('id');
                showDetail(id);
            });

            function showDetail(id) {
                currentDetailApprovalId = id;
                $.get("{{ url('utility/boiler-logs/json') }}/" + id, function(res) {
                    if (res.status === 200) {
                        const app = res.approval;
                        const logs = res.logs;

                        $('#detail_tanggal').text(formatDate(app.tanggal));
                        $('#detail_total_jam').text(logs.length);
                        $('#detail_status').html(getStatusBadge(app.status));
                        
                        $('#detail_foreman').text(app.foreman?.username || '-');
                        $('#detail_submitted_at').text(app.submitted_at ? `(${formatDateTime(app.submitted_at)})` : '');
                        $('#detail_supervisor').text(app.supervisor?.username || '-');
                        $('#detail_approved_at').text(app.approved_at ? `(${formatDateTime(app.approved_at)})` : '');

                        const userJabatan = "{{ Auth::user()->jabatan }}";
                        let canEdit = false;
                        if (app.status === 'draft') {
                            canEdit = ['foreman', 'supervisor', 'admin'].includes(userJabatan);
                        } else {
                            canEdit = ['foreman', 'admin'].includes(userJabatan);
                        }

                        let html = '';
                        logs.forEach((log, index) => {
                            let actionBtn = '';
                            if (canEdit) {
                                actionBtn = `<td>
                                    <button class="btn btn-sm btn-info text-white btn-edit-log" data-id="${log.id}" data-waktu="${log.waktu}" data-log='${JSON.stringify(log)}'>
                                        <i class="ri-edit-line"></i>
                                    </button>
                                </td>`;
                            } else {
                                actionBtn = `<td>
                                    <button class="btn btn-sm btn-secondary" disabled title="Laporan dikunci">
                                        <i class="ri-lock-line"></i>
                                    </button>
                                </td>`;
                            }

                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td class="fw-bold">${formatTime(log.waktu)}</td>
                                    <td>${formatNum(log.PVSteam)}</td>
                                    <td>${formatNum(log.FeedPressure)}</td>
                                    <td>${formatNum(log.Press_Pasteur)}</td>
                                    <td>${formatNum(log.LevelFeedWater)}</td>
                                    <td>${formatNum(log.water_hmi_flow_rate)}</td>
                                    <td>${formatNum(log.water_hmi_total)}</td>
                                    <td>${formatNum(log.InletWaterFlow)}</td>
                                    <td>${formatNum(log.OutletSteamFlow)}</td>
                                    <td>${formatNum(log.SuhuFeedTank)}</td>
                                    <td>${formatNum(log.IDFan)}</td>
                                    <td>${formatNum(log.LHFDFan)}</td>
                                    <td>${formatNum(log.RHFDFan)}</td>
                                    <td>${formatNum(log.LHStoker)}</td>
                                    <td>${formatNum(log.RHStoker)}</td>
                                    <td>${formatNum(log.water_flow_total)}</td>
                                    <td>${formatNum(log.LHTemp)}</td>
                                    <td>${formatNum(log.RHTemp)}</td>
                                    <td>${formatNum(log.O2)}</td>
                                    <td>${formatNum(log.CO2)}</td>
                                    <td>${formatNum(log.flue_gass_temp)}</td>
                                    <td>${formatNum(log.LHGuiloutine)}</td>
                                    <td>${formatNum(log.RHGuiloutine)}</td>
                                    <td>${formatNum(log.WaterPump1)}</td>
                                    <td>${formatNum(log.WaterPump2)}</td>
                                    <td>${formatNum(log.Batubara_FK)}</td>
                                    <td>${formatNum(log.Steam_FK)}</td>
                                    ${actionBtn}
                                </tr>
                            `;
                        });
                        
                        $('#detailTableBody').html(html);
                        $('#modalDetail').modal('show');
                    }
                });
            }

            // ── Trigger Submit Modal ──
            $(document).on('click', '.btn-submit-approval', function() {
                const id = $(this).data('id');
                $('#submit_id').val(id);
                loadUsers();
                $('#modalSubmit').modal('show');
            });

            // ── Confirm Submit ──
            $('#btnConfirmSubmit').click(function() {
                const id = $('#submit_id').val();
                const foreman_id = $('#submit_foreman').val();
                const supervisor_id = $('#submit_supervisor').val();

                if (!foreman_id || !supervisor_id) {
                    Swal.fire('Validasi', 'Mohon pilih Foreman dan Supervisor.', 'warning');
                    return;
                }

                $.ajax({
                    url: "{{ url('utility/boiler-logs/submit') }}/" + id,
                    method: 'POST',
                    data: {
                        foreman_id: foreman_id,
                        supervisor_id: supervisor_id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $('#modalSubmit').modal('hide');
                        Swal.fire('Berhasil', res.message, 'success');
                        loadData(1);
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message || 'Gagal mengajukan approval.', 'error');
                    }
                });
            });

            // ── Trigger Sync Sensor via Swal ──
            $('#btnSync').click(function() {
                Swal.fire({
                    title: 'Sinkronisasi Data Sensor Boiler',
                    text: 'Silakan pilih tanggal untuk ditarik datanya dari API sensor:',
                    html: `
                        <input type="date" id="syncDateInput" class="form-control" value="${new Date().toISOString().split('T')[0]}">
                    `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Mulai Sinkron',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        const date = document.getElementById('syncDateInput').value;
                        if (!date) {
                            Swal.showValidationMessage('Mohon pilih tanggal!');
                        }
                        return date;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const date = result.value;
                        
                        Swal.fire({
                            title: 'Sedang Menghubungi Sensor...',
                            html: 'Proses sinkronisasi data sedang berlangsung. Harap tunggu.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('api.boiler-logs.sync') }}",
                            method: 'POST',
                            data: { tanggal: date },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                Swal.fire('Berhasil', res.message, 'success');
                                loadData(1);
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal', xhr.responseJSON?.message || 'Gagal menyinkronkan data sensor.', 'error');
                            }
                        });
                    }
                });
            });

            // Helper formatters
            function getStatusBadge(status) {
                const badges = {
                    'draft': '<span class="badge bg-secondary">Draft</span>',
                    'waiting_supervisor': '<span class="badge bg-warning text-dark">Waiting Supervisor</span>',
                    'approved_supervisor': '<span class="badge bg-success">Approved</span>'
                };
                return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
            }

            function formatNum(v) {
                if (v === null || v === undefined || v === '') return '-';
                return Number(v).toLocaleString('id-ID', { maximumFractionDigits: 3 });
            }

            function formatDate(dateString) {
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                return new Date(dateString).toLocaleDateString('id-ID', options);
            }

            function formatDateTime(dateTimeString) {
                const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                return new Date(dateTimeString).toLocaleDateString('id-ID', options);
            }

            function formatTime(waktuString) {
                if (!waktuString) return '-';
                const date = new Date(waktuString);
                if (isNaN(date.getTime())) return waktuString;

                const pad = (n) => n.toString().padStart(2, '0');
                const yyyy = date.getFullYear();
                const mm = pad(date.getMonth() + 1);
                const dd = pad(date.getDate());
                const hh = pad(date.getHours());
                const min = pad(date.getMinutes());

                return `${yyyy}-${mm}-${dd} ${hh}:${min}`;
            }

            // Export Excel Modal Trigger
            $('#btnExport').on('click', function() {
                $('#modalExport').modal('show');
            });


            // ── Trigger Edit Log Modal ──
            $(document).on('click', '.btn-edit-log', function() {
                const id = $(this).data('id');
                const waktu = $(this).data('waktu');
                const log = $(this).data('log');

                $('#edit_log_id').val(id);
                $('#edit_log_waktu').text(formatTime(waktu));

                $('#edit_PVSteam').val(log.PVSteam);
                $('#edit_FeedPressure').val(log.FeedPressure);
                $('#edit_Press_Pasteur').val(log.Press_Pasteur);
                $('#edit_LevelFeedWater').val(log.LevelFeedWater);
                $('#edit_InletWaterFlow').val(log.InletWaterFlow);
                $('#edit_OutletSteamFlow').val(log.OutletSteamFlow);
                $('#edit_SuhuFeedTank').val(log.SuhuFeedTank);
                $('#edit_IDFan').val(log.IDFan);
                $('#edit_LHFDFan').val(log.LHFDFan);
                $('#edit_RHFDFan').val(log.RHFDFan);
                $('#edit_LHStoker').val(log.LHStoker);
                $('#edit_RHStoker').val(log.RHStoker);
                $('#edit_LHTemp').val(log.LHTemp);
                $('#edit_RHTemp').val(log.RHTemp);
                $('#edit_O2').val(log.O2);
                $('#edit_CO2').val(log.CO2);
                $('#edit_LHGuiloutine').val(log.LHGuiloutine);
                $('#edit_RHGuiloutine').val(log.RHGuiloutine);
                $('#edit_WaterPump1').val(log.WaterPump1);
                $('#edit_WaterPump2').val(log.WaterPump2);
                $('#edit_Batubara_FK').val(log.Batubara_FK);
                $('#edit_Steam_FK').val(log.Steam_FK);
                $('#edit_water_flow_total').val(log.water_flow_total);
                $('#edit_water_hmi_flow_rate').val(log.water_hmi_flow_rate);
                $('#edit_water_hmi_total').val(log.water_hmi_total);
                $('#edit_flue_gass_temp').val(log.flue_gass_temp);

                $('#modalEditLog').modal('show');
            });

            // ── Save Edit Log ──
            $('#btnSaveEditLog').click(function() {
                const id = $('#edit_log_id').val();
                const form = $('#formEditLog');
                
                $.ajax({
                    url: "{{ url('utility/boiler-logs/update') }}/" + id,
                    method: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $('#modalEditLog').modal('hide');
                        Swal.fire('Berhasil', res.message, 'success');
                        
                        if (currentDetailApprovalId) {
                            showDetail(currentDetailApprovalId);
                        }
                        loadData(1);

                        // Fix scroll bug
                        setTimeout(() => {
                            if ($('#modalDetail').hasClass('show')) {
                                $('body').addClass('modal-open');
                            }
                        }, 400);
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Gagal memperbarui data.', 'error');
                    }
                });
            });

            // ── Cancel/Close Edit Log Modal ──
            $('#btnCancelEditLog, #modalEditLog .btn-close').click(function() {
                $('#modalEditLog').modal('hide');
                setTimeout(() => {
                    if ($('#modalDetail').hasClass('show')) {
                        $('body').addClass('modal-open');
                    }
                }, 400);
            });
        });
    </script>
@endsection
