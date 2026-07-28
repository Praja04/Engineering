@extends('layouts.app')

@section('title', 'Approval Boiler Logs')

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
                <div class="card-header-custom">
                    <h4 class="mb-1 text-white fw-bold">
                        <i class="ri-checkbox-circle-line me-2 text-warning"></i>
                        Approval Boiler Logs Harian
                    </h4>
                    <p class="mb-0 text-white-50 small">Engineering Utility · Halaman Persetujuan Laporan Harian (06:00 -
                        06:00)</p>
                </div>
                <div class="card-body p-4">
                    {{-- Mass Action Bar --}}
                    <div id="massActionArea" class="d-none mb-3 p-3 bg-light rounded d-flex justify-content-between align-items-center border-start border-primary border-3">
                        <div>
                            <span class="fw-bold text-primary" id="checkedCount">0</span> laporan terpilih
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-danger btn-sm px-3" id="btnMassReject">
                                <i class="ri-close-circle-line me-1"></i> Reject Terpilih
                            </button>
                            <button type="button" class="btn btn-success btn-sm px-3" id="btnMassApprove">
                                <i class="ri-checkbox-circle-line me-1"></i> Approve Terpilih
                            </button>
                        </div>
                    </div>

                    <div class="mb-3 d-flex justify-content-start">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="btnSelectAll">
                            <i class="ri-checkbox-multiple-line me-1"></i> Pilih Semua
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="40"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Foreman Pengaju</th>
                                    <th>Supervisor Penyetuju</th>
                                    <th class="text-center">Total Jam Data</th>
                                    <th class="text-center">Avg PV Steam (Bar)</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <div class="spinner-border text-primary spinner-border-sm me-2" role="status">
                                        </div>
                                        Memuat data...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Detail & Action -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom p-3">
                    <h5 class="modal-title fw-bold"><i class="ri-checkbox-circle-fill text-warning me-2"></i>Periksa &
                        Setujui Log Boiler</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border-start border-primary border-3">
                                <h6 class="fw-bold mb-2">Informasi Umum</h6>
                                <input type="hidden" id="detail_id">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted" width="40%">Tanggal Operasional</td>
                                        <td>: <span id="detail_tanggal" class="fw-bold"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Total Jam Tercatat</td>
                                        <td>: <span id="detail_total_jam"></span> jam</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Status Approval</td>
                                        <td>: <span id="detail_status"></span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border-start border-warning border-3">
                                <h6 class="fw-bold mb-2">Pihak Berwenang & Tanda Tangan</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted" width="40%">Diajukan (Foreman)</td>
                                        <td>: <span id="detail_foreman"></span> <small class="text-muted"
                                                id="detail_submitted_at"></small></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Disetujui (Supervisor)</td>
                                        <td>: <span id="detail_supervisor"></span> <small class="text-muted"
                                                id="detail_approved_at"></small></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3"><i class="ri-table-line text-info me-2"></i>Tabel Log Data Sensor Per Jam</h6>

                    <div class="scrollable-detail-table mb-4">
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
                                    @if (in_array(Auth::user()->jabatan, ['foreman', 'supervisor', 'admin']))
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
                <div class="modal-footer border-top p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                    <div class="d-flex gap-2" id="detailActionButtons">
                        <button type="button" class="btn btn-danger px-4" id="btnDetailReject">
                            <i class="ri-close-circle-line me-1"></i> Reject
                        </button>
                        <button type="button" class="btn btn-success px-4" id="btnDetailApprove">
                            <i class="ri-checkbox-circle-line me-1"></i> Approve
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Log Boiler -->
    <div class="modal fade" id="modalEditLog" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white"><i class="ri-edit-box-line me-1"></i> Edit Log Boiler - <span
                            id="edit_log_waktu"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formEditLog">
                        <input type="hidden" id="edit_log_id" name="id">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">PV Steam (Bar)</label>
                                <input type="number" step="any" name="PVSteam" id="edit_PVSteam"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Feed Press (Bar)</label>
                                <input type="number" step="any" name="FeedPressure" id="edit_FeedPressure"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Press Past. (Bar)</label>
                                <input type="number" step="any" name="Press_Pasteur" id="edit_Press_Pasteur"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Level Feed W. (%)</label>
                                <input type="number" step="any" name="LevelFeedWater" id="edit_LevelFeedWater"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Inlet Flow (m3/h)</label>
                                <input type="number" step="any" name="InletWaterFlow" id="edit_InletWaterFlow"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Outlet Flow (ton/h)</label>
                                <input type="number" step="any" name="OutletSteamFlow" id="edit_OutletSteamFlow"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Suhu Feed Tank (°C)</label>
                                <input type="number" step="any" name="SuhuFeedTank" id="edit_SuhuFeedTank"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">ID Fan (rpm)</label>
                                <input type="number" step="any" name="IDFan" id="edit_IDFan"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">LH FD Fan (rpm)</label>
                                <input type="number" step="any" name="LHFDFan" id="edit_LHFDFan"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">RH FD Fan (rpm)</label>
                                <input type="number" step="any" name="RHFDFan" id="edit_RHFDFan"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">LH Stoker (rpm)</label>
                                <input type="number" step="any" name="LHStoker" id="edit_LHStoker"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">RH Stoker (rpm)</label>
                                <input type="number" step="any" name="RHStoker" id="edit_RHStoker"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">LH Temp (°C)</label>
                                <input type="number" step="any" name="LHTemp" id="edit_LHTemp"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">RH Temp (°C)</label>
                                <input type="number" step="any" name="RHTemp" id="edit_RHTemp"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">O2 (%)</label>
                                <input type="number" step="any" name="O2" id="edit_O2"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">CO2 (%)</label>
                                <input type="number" step="any" name="CO2" id="edit_CO2"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">LH Guiloutine (mm)</label>
                                <input type="number" step="any" name="LHGuiloutine" id="edit_LHGuiloutine"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">RH Guiloutine (mm)</label>
                                <input type="number" step="any" name="RHGuiloutine" id="edit_RHGuiloutine"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Water Pump 1 (Hz)</label>
                                <input type="number" step="any" name="WaterPump1" id="edit_WaterPump1"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Water Pump 2 (Hz)</label>
                                <input type="number" step="any" name="WaterPump2" id="edit_WaterPump2"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Batubara FK</label>
                                <input type="number" step="any" name="Batubara_FK" id="edit_Batubara_FK"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Steam FK</label>
                                <input type="number" step="any" name="Steam_FK" id="edit_Steam_FK"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Water Flow - Total Count (m3/h)</label>
                                <input type="number" step="any" name="water_flow_total" id="edit_water_flow_total"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Water HMI - Flow Rate (m3/h)</label>
                                <input type="number" step="any" name="water_hmi_flow_rate"
                                    id="edit_water_hmi_flow_rate" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Water HMI - Total Count (m3)</label>
                                <input type="number" step="any" name="water_hmi_total" id="edit_water_hmi_total"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Temp Flue Gass (°C)</label>
                                <input type="number" step="any" name="flue_gass_temp" id="edit_flue_gass_temp"
                                    class="form-control form-control-sm">
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
            loadData();

            function loadData() {
                // Reset mass action UI
                $('#checkAll').prop('checked', false);
                $('#massActionArea').addClass('d-none');
                $('#checkedCount').text('0');

                $('#tableBody').html(`
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                            Memuat data...
                        </td>
                    </tr>
                `);

                $.get("{{ route('boiler-logs.approval.json') }}", function(res) {
                    if (res.status === 200) {
                        renderTable(res.data);
                    }
                }).fail(function(xhr) {
                    console.error('Gagal memuat data approval:', xhr);
                    $('#tableBody').html(`
                        <tr>
                            <td colspan="9" class="text-center py-5 text-danger">
                                <i class="ri-error-warning-line fs-2 d-block mb-2"></i>
                                Gagal mengambil data dari server (${xhr.status}: ${xhr.statusText})
                            </td>
                        </tr>
                    `);
                });
            }

            function renderTable(data) {
                const items = data ? (Array.isArray(data) ? data : Object.values(data)) : [];

                let html = '';
                if (items.length === 0) {
                    html =
                        '<tr><td colspan="9" class="text-center py-5 text-muted"><i class="ri-checkbox-circle-line text-success fs-2 d-block mb-2"></i>Semua laporan boiler log sudah diproses.</td></tr>';
                } else {
                    items.forEach((item, index) => {
                        console.log(item);

                        let actionButtons = `
                            <button class="btn btn-sm btn-primary rounded-pill px-3 btn-periksa" data-id="${item.id}">
                                <i class="ri-eye-line me-1"></i> Periksa
                            </button>
                        `;

                        html += `
                            <tr>
                                <td><input type="checkbox" class="form-check-input row-checkbox" value="${item.id}"></td>
                                <td>${index + 1}</td>
                                <td>${formatDate(item.tanggal)}</td>
                                <td>${item.foreman?.username || '-'}</td>
                                <td>${item.supervisor?.username || '-'}</td>
                                <td class="text-center fw-bold">${item.total_logs} / 25</td>
                                <td class="text-center">${formatNum(item.avg_steam)}</td>
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

            // ── Checkbox Change Actions ──
            $(document).on('change', '#checkAll', function() {
                const checked = $(this).is(':checked');
                $('.row-checkbox').prop('checked', checked);
                toggleMassActionArea();
            });

            $(document).on('change', '.row-checkbox', function() {
                const totalChecked = $('.row-checkbox:checked').length;
                const totalCheckboxes = $('.row-checkbox').length;
                $('#checkAll').prop('checked', totalChecked === totalCheckboxes);
                toggleMassActionArea();
            });

            // ── Button Pilih Semua Action ──
            $(document).on('click', '#btnSelectAll', function() {
                const totalCheckboxes = $('.row-checkbox').length;
                if (totalCheckboxes === 0) return;

                const totalChecked = $('.row-checkbox:checked').length;
                const checkAllState = (totalChecked < totalCheckboxes);

                $('.row-checkbox').prop('checked', checkAllState);
                $('#checkAll').prop('checked', checkAllState);
                toggleMassActionArea();
            });

            function toggleMassActionArea() {
                const checkedIds = getCheckedIds();
                const totalChecked = checkedIds.length;
                const totalCheckboxes = $('.row-checkbox').length;

                if (totalChecked > 0) {
                    $('#checkedCount').text(totalChecked);
                    $('#massActionArea').removeClass('d-none');
                } else {
                    $('#massActionArea').addClass('d-none');
                }

                // Update tombol Pilih Semua text/style
                if (totalChecked === totalCheckboxes && totalCheckboxes > 0) {
                    $('#btnSelectAll').html('<i class="ri-checkbox-multiple-blank-line me-1"></i> Batal Pilih Semua')
                                      .removeClass('btn-outline-primary')
                                      .addClass('btn-outline-danger');
                } else {
                    $('#btnSelectAll').html('<i class="ri-checkbox-multiple-line me-1"></i> Pilih Semua')
                                      .removeClass('btn-outline-danger')
                                      .addClass('btn-outline-primary');
                }
            }

            function getCheckedIds() {
                const ids = [];
                $('.row-checkbox:checked').each(function() {
                    ids.push($(this).val());
                });
                return ids;
            }

            // ── Mass Approve Action ──
            $('#btnMassApprove').click(function() {
                const ids = getCheckedIds();
                if (ids.length === 0) return;

                Swal.fire({
                    title: 'Approve Massal?',
                    text: `Anda akan menyetujui ${ids.length} laporan boiler secara bersamaan.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Setujui Semua!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('boiler-logs.mass-approve') }}",
                            method: 'POST',
                            data: { ids: ids },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                Swal.fire('Berhasil', res.message, 'success');
                                loadData();
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Gagal menyetujui laporan massal.', 'error');
                            }
                        });
                    }
                });
            });

            // ── Mass Reject Action ──
            $('#btnMassReject').click(function() {
                const ids = getCheckedIds();
                if (ids.length === 0) return;

                Swal.fire({
                    title: 'Reject Massal?',
                    text: `Anda akan menolak ${ids.length} laporan boiler secara bersamaan dan mengembalikan ke status Draft.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Tolak Semua!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('boiler-logs.mass-reject') }}",
                            method: 'POST',
                            data: { ids: ids },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                Swal.fire('Ditolak', res.message, 'info');
                                loadData();
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Gagal menolak laporan massal.', 'error');
                            }
                        });
                    }
                });
            });

            // ── View Detail (Periksa) ──
            $(document).on('click', '.btn-periksa', function() {
                const id = $(this).data('id');
                showDetail(id);
            });

            let currentDetailApprovalId = null;

            function showDetail(id) {
                currentDetailApprovalId = id;
                $.get("{{ url('utility/boiler-logs/json') }}/" + id, function(res) {
                    if (res.status === 200) {
                        const app = res.approval;
                        const logs = res.logs;

                        $('#detail_id').val(app.id);
                        $('#detail_tanggal').text(formatDate(app.tanggal));
                        $('#detail_total_jam').text(logs.length);
                        $('#detail_status').html(getStatusBadge(app.status));

                        $('#detail_foreman').text(app.foreman?.username || '-');
                        $('#detail_submitted_at').text(app.submitted_at ?
                            `(${formatDateTime(app.submitted_at)})` : '');
                        $('#detail_supervisor').text(app.supervisor?.username || '-');
                        $('#detail_approved_at').text(app.approved_at ?
                            `(${formatDateTime(app.approved_at)})` : '');

                        // Hide/show action buttons depending on status and role
                        const userJabatan = ("{{ Auth::user()->jabatan ?? '' }}").toLowerCase().trim();
                        const statusLaporan = (app.status ?? '').toLowerCase().trim();
                        console.log('Debug Approval Modal:', {
                            status_laporan: statusLaporan,
                            jabatan_user: userJabatan,
                            apakah_supervisor: userJabatan === 'supervisor',
                            apakah_admin: userJabatan === 'admin',
                            kondisi_lulus: statusLaporan === 'waiting_supervisor' && (
                                userJabatan === 'supervisor' || userJabatan === 'admin')
                        });
                        if (statusLaporan === 'waiting_supervisor' && (userJabatan === 'supervisor' ||
                                userJabatan === 'admin')) {
                            $('#detailActionButtons').removeClass('d-none');
                        } else {
                            $('#detailActionButtons').addClass('d-none');
                        }

                        // Form selalu bisa diedit oleh foreman, supervisor, dan admin
                        // tanpa memandang status approval (termasuk setelah approved)
                        let canEdit = ['foreman', 'supervisor', 'admin'].includes(userJabatan);

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

            // ── Action: Approve ──
            $('#btnDetailApprove').click(function() {
                const id = $('#detail_id').val();

                Swal.fire({
                    title: 'Setujui Laporan Boiler Log?',
                    text: "Laporan harian ini akan disetujui.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Setujui!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('utility/boiler-logs/approve') }}/" + id,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                $('#modalDetail').modal('hide');
                                Swal.fire('Disetujui', res.message, 'success');
                                loadData();
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message ||
                                    'Gagal menyetujui laporan.', 'error');
                            }
                        });
                    }
                });
            });

            // ── Action: Reject ──
            $('#btnDetailReject').click(function() {
                const id = $('#detail_id').val();

                Swal.fire({
                    title: 'Tolak Laporan?',
                    text: "Laporan harian ini akan ditolak dan dikembalikan ke status Draft.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Tolak!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('utility/boiler-logs/reject') }}/" + id,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                $('#modalDetail').modal('hide');
                                Swal.fire('Ditolak', res.message, 'info');
                                loadData();
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message ||
                                    'Gagal menolak laporan.', 'error');
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
                return Number(v).toLocaleString('id-ID', {
                    maximumFractionDigits: 3
                });
            }

            function formatDate(dateString) {
                const options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                return new Date(dateString).toLocaleDateString('id-ID', options);
            }

            function formatDateTime(dateTimeString) {
                const options = {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
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
                        loadData();

                        // Fix scroll bug
                        setTimeout(() => {
                            if ($('#modalDetail').hasClass('show')) {
                                $('body').addClass('modal-open');
                            }
                        }, 400);
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message ||
                            'Gagal memperbarui data.', 'error');
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
