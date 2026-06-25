@extends('layouts.app')

@section('title', 'Approval Harian WWTP')

@section('styles')
    <style>
        .page-header-card {
            background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%);
            border-radius: 12px;
        }

        .data-row {
            transition: background-color .2s ease;
        }

        .data-row:hover {
            background-color: rgba(41, 156, 219, .08);
        }

        .detail-table td,
        .detail-table th {
            vertical-align: middle;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- <!-- Loading Overlay -->
            <div id="loadingOverlay" class="loading-overlay d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div> --}}

            <!-- Page Heading -->
            <div class="card border-0 shadow-sm mb-4 page-header-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h4 class="text-white fw-bold mb-1">
                                <i class="ri-check-double-line text-warning me-2"></i>
                                Approval Harian WWTP
                            </h4>
                            <p class="text-white-50 mb-0 small">
                                Periksa dan berikan persetujuan untuk data harian WWTP (Proses, Performance, Sludge)
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="row mb-3">
                <div class="col-12">
                    <ul class="nav nav-pills nav-customs nav-danger" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#pending-tab" role="tab"
                                id="btnTabPending">
                                <i class="ri-timer-2-line me-1 align-bottom"></i> Menunggu Approval
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#history-tab" role="tab" id="btnTabHistory">
                                <i class="ri-history-line me-1 align-bottom"></i> Riwayat Approval
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Approval Table Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Tanggal</th>
                                    <th>Operator Pengaju</th>
                                    <th>Foreman Verifikator</th>
                                    <th>Supervisor Approval</th>
                                    <th>Status Approval</th>
                                    <th class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="approvalTableBody">
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="spinner-border text-info" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- MODAL: Detail & Action --}}
            <div class="modal fade" id="detailApprovalModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title text-info">
                                <i class="ri-eye-line me-2"></i>Detail & Persetujuan Laporan Harian WWTP
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <!-- Header Info -->
                            <div id="modalHeaderContent"></div>

                            <!-- Tabs for Daily Forms inside Modal -->
                            <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#modal-proses-tab" role="tab">
                                        <i class="ri-water-flash-line me-1"></i> Proses (Influent Harian)
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#modal-performance-tab" role="tab">
                                        <i class="ri-pulse-line me-1"></i> Performance (pH Harian)
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#modal-sludge-tab" role="tab">
                                        <i class="ri-delete-bin-line me-1"></i> Sludge Harian
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content pt-2">
                                <div class="tab-pane active" id="modal-proses-tab" role="tabpanel">
                                    <div class="table-responsive" id="prosesTableContainer">
                                        <p class="text-muted text-center py-3">Loading data Proses...</p>
                                    </div>
                                </div>
                                <div class="tab-pane" id="modal-performance-tab" role="tabpanel">
                                    <div class="table-responsive" id="performanceTableContainer">
                                        <p class="text-muted text-center py-3">Loading data Performance...</p>
                                    </div>
                                </div>
                                <div class="tab-pane" id="modal-sludge-tab" role="tabpanel">
                                    <div class="table-responsive" id="sludgeTableContainer">
                                        <p class="text-muted text-center py-3">Loading data Sludge...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light" id="modalFooterActions">
                            <!-- Actions loaded via JS based on status -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const userJabatan = "{{ Auth::user()->jabatan ?? '' }}";
            const csrfToken = "{{ csrf_token() }}";
            let activeTab = 'pending';
            let cachedRecords = [];

            // Load Pending on mount
            loadApprovals();

            $('#btnTabPending').on('click', function() {
                activeTab = 'pending';
                loadApprovals();
            });

            $('#btnTabHistory').on('click', function() {
                activeTab = 'history';
                loadApprovals();
            });

            function loadApprovals() {
                showLoading(true);
                $.ajax({
                    url: "{{ url('wwtp-approval/list') }}",
                    method: 'GET',
                    data: {
                        tab: activeTab
                    },
                    success: function(response) {
                        cachedRecords = response || [];
                        renderTable(cachedRecords);
                        showLoading(false);
                    },
                    error: function() {
                        showLoading(false);
                        Swal.fire('Error', 'Gagal memuat data approval.', 'error');
                    }
                });
            }

            function renderTable(data) {
                const tbody = $('#approvalTableBody');
                tbody.empty();

                if (!data || !data.length) {
                    tbody.append(`<tr><td colspan="7" class="text-center py-5 text-muted">
                        <i class="ri-checkbox-circle-line fs-3 text-success d-block mb-2"></i>
                        Tidak ada data approval harian untuk ditampilkan.</td></tr>`);
                    return;
                }

                data.forEach(function(item, idx) {
                    const no = idx + 1;

                    // Status Badge
                    let statusBadge = '';
                    const status = item.status || 'submitted';
                    if (status === 'submitted') {
                        statusBadge =
                            '<span class="badge bg-warning text-dark"><i class="mdi mdi-clock-outline me-1"></i>Menunggu Foreman</span>';
                    } else if (status === 'approved_foreman') {
                        statusBadge =
                            '<span class="badge bg-info"><i class="mdi mdi-account-clock-outline me-1"></i>Menunggu Spv</span>';
                    } else if (status === 'approved_supervisor') {
                        statusBadge =
                            '<span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Selesai</span>';
                    } else if (status === 'rejected') {
                        const reason = item.reject_reason ?
                            ` title="Alasan: ${escapeHtml(item.reject_reason)}"` : '';
                        statusBadge =
                            `<span class="badge bg-danger"${reason}><i class="mdi mdi-close-circle-outline me-1"></i>Ditolak</span>`;
                    }

                    // Action buttons
                    let actions = `<button class="btn btn-sm btn-info" onclick="showApprovalDetail(${item.id})">
                        <i class="ri-eye-line align-bottom me-1"></i> Periksa</button>`;

                    tbody.append(`
                        <tr class="data-row">
                            <td class="ps-4">${no}</td>
                            <td><span class="badge bg-light text-dark border"><i class="mdi mdi-calendar me-1"></i>${formatDate(item.tanggal)}</span></td>
                            <td>${item.operator ? item.operator.username : '-'}</td>
                            <td>${item.foreman ? item.foreman.username : '-'}</td>
                            <td>${item.supervisor ? item.supervisor.username : '-'}</td>
                            <td>${statusBadge}</td>
                            <td class="text-center pe-4">${actions}</td>
                        </tr>
                    `);
                });
            }

            window.showApprovalDetail = function(id) {
                showLoading(true);
                $.ajax({
                    url: `{{ url('wwtp-approval') }}/${id}`,
                    method: 'GET',
                    success: function(response) {
                        showLoading(false);
                        const record = response.approval;
                        if (!record) return;

                        // Load Header Info
                        let statusBadge = '';
                        const status = record.status || 'submitted';
                        if (status === 'submitted') {
                            statusBadge =
                                '<span class="badge bg-warning text-dark"><i class="mdi mdi-clock-outline me-1"></i>Menunggu Foreman</span>';
                        } else if (status === 'approved_foreman') {
                            statusBadge =
                                '<span class="badge bg-info"><i class="mdi mdi-account-clock-outline me-1"></i>Menunggu Spv</span>';
                        } else if (status === 'approved_supervisor') {
                            statusBadge =
                                '<span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Selesai</span>';
                        } else if (status === 'rejected') {
                            statusBadge =
                                '<span class="badge bg-danger"><i class="mdi mdi-close-circle-outline me-1"></i>Ditolak</span>';
                        }

                        let rejectReasonHtml = '';
                        if (status === 'rejected' && record.reject_reason) {
                            rejectReasonHtml = `
                                <div class="col-12 mt-2">
                                    <div class="alert alert-danger mb-0 py-2">
                                        <i class="mdi mdi-alert-circle-outline me-1"></i><strong>Alasan Penolakan:</strong> ${escapeHtml(record.reject_reason)}
                                    </div>
                                </div>
                            `;
                        }

                        let headerHtml = `
                             <div class="row g-3 mb-4">
                                 <div class="col-md-3">
                                     <div class="info-box p-3 bg-light rounded border border-info h-100">
                                         <p class="text-muted small mb-1">Tanggal Laporan</p>
                                         <p class="fw-bold mb-0 fs-6">${formatDate(record.tanggal)}</p>
                                     </div>
                                 </div>
                                 <div class="col-md-2">
                                     <div class="info-box p-3 bg-light rounded border border-info h-100">
                                         <p class="text-muted small mb-1">Operator Pengaju</p>
                                         <p class="fw-bold mb-0 fs-6">${record.operator ? record.operator.username : '-'}</p>
                                     </div>
                                 </div>
                                 <div class="col-md-2">
                                     <div class="info-box p-3 bg-light rounded border border-info h-100">
                                         <p class="text-muted small mb-1">Foreman</p>
                                         <p class="fw-bold mb-0 fs-6">${record.foreman ? record.foreman.username : '-'}</p>
                                     </div>
                                 </div>
                                 <div class="col-md-2">
                                     <div class="info-box p-3 bg-light rounded border border-info h-100">
                                         <p class="text-muted small mb-1">Supervisor</p>
                                         <p class="fw-bold mb-0 fs-6">${record.supervisor ? record.supervisor.username : '-'}</p>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="info-box p-3 bg-light rounded border border-info h-100">
                                         <p class="text-muted small mb-1">Status Approval</p>
                                         <p class="mb-0 fs-6">${statusBadge}</p>
                                     </div>
                                 </div>
                                 ${rejectReasonHtml}
                             </div>
                             <h6 class="fw-bold mb-3 text-info">Detail Laporan Harian WWTP</h6>
                         `;
                        $('#modalHeaderContent').html(headerHtml);

                        // 1. PROSES TABLE
                        let shiftDataProses = {
                            'shift1': {
                                debit1: '-',
                                running_wwtp1: '-',
                                debit2: '-',
                                running_wwtp2: '-',
                                pit_sparta: '-',
                                pit_garam: '-',
                                pit_domestik: '-',
                                pit_produksi_step3: '-',
                                pit_storage: '-',
                                pit_proses_wwtp2: '-',
                                pit_outlet: '-',
                                pit_boiler: '-'
                            },
                            'shift2': {
                                debit1: '-',
                                running_wwtp1: '-',
                                debit2: '-',
                                running_wwtp2: '-',
                                pit_sparta: '-',
                                pit_garam: '-',
                                pit_domestik: '-',
                                pit_produksi_step3: '-',
                                pit_storage: '-',
                                pit_proses_wwtp2: '-',
                                pit_outlet: '-',
                                pit_boiler: '-'
                            },
                            'shift3': {
                                debit1: '-',
                                running_wwtp1: '-',
                                debit2: '-',
                                running_wwtp2: '-',
                                pit_sparta: '-',
                                pit_garam: '-',
                                pit_domestik: '-',
                                pit_produksi_step3: '-',
                                pit_storage: '-',
                                pit_proses_wwtp2: '-',
                                pit_outlet: '-',
                                pit_boiler: '-'
                            }
                        };
                        if (response.influent && response.influent.length > 0) {
                            response.influent.forEach(function(item) {
                                let s = item.shift;
                                if (shiftDataProses[s]) {
                                    shiftDataProses[s] = {
                                        debit1: num(item.debit1),
                                        running_wwtp1: item.running_wwtp1 || '-',
                                        debit2: num(item.debit2),
                                        running_wwtp2: item.running_wwtp2 || '-',
                                        pit_sparta: num(item.pit_sparta),
                                        pit_garam: num(item.pit_garam),
                                        pit_domestik: num(item.pit_domestik),
                                        pit_produksi_step3: num(item
                                            .pit_produksi_step3),
                                        pit_storage: num(item.pit_storage),
                                        pit_proses_wwtp2: num(item.pit_proses_wwtp2),
                                        pit_outlet: num(item.pit_outlet),
                                        pit_boiler: num(item.pit_boiler)
                                    };
                                }
                            });
                        }

                        let prosesHtml = `
                            <table class="table table-bordered table-hover align-middle mb-0 text-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>Parameter</th>
                                        <th class="text-center">Shift 1 (06:00 - 14:00)</th>
                                        <th class="text-center">Shift 2 (14:00 - 22:00)</th>
                                        <th class="text-center">Shift 3 (22:00 - 06:00)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td><strong>Debit 1 (m³/h)</strong></td><td class="text-center">${shiftDataProses.shift1.debit1}</td><td class="text-center">${shiftDataProses.shift2.debit1}</td><td class="text-center">${shiftDataProses.shift3.debit1}</td></tr>
                                    <tr><td><strong>Running WWTP 1</strong></td><td class="text-center">${statusBadgeText(shiftDataProses.shift1.running_wwtp1)}</td><td class="text-center">${statusBadgeText(shiftDataProses.shift2.running_wwtp1)}</td><td class="text-center">${statusBadgeText(shiftDataProses.shift3.running_wwtp1)}</td></tr>
                                    <tr><td><strong>Debit 2 (m³/h)</strong></td><td class="text-center">${shiftDataProses.shift1.debit2}</td><td class="text-center">${shiftDataProses.shift2.debit2}</td><td class="text-center">${shiftDataProses.shift3.debit2}</td></tr>
                                    <tr><td><strong>Running WWTP 2</strong></td><td class="text-center">${statusBadgeText(shiftDataProses.shift1.running_wwtp2)}</td><td class="text-center">${statusBadgeText(shiftDataProses.shift2.running_wwtp2)}</td><td class="text-center">${statusBadgeText(shiftDataProses.shift3.running_wwtp2)}</td></tr>
                                    <tr><td><strong>Pit Sparta (m³)</strong></td><td class="text-center">${shiftDataProses.shift1.pit_sparta}</td><td class="text-center">${shiftDataProses.shift2.pit_sparta}</td><td class="text-center">${shiftDataProses.shift3.pit_sparta}</td></tr>
                                    <tr><td><strong>Pit Garam (m³)</strong></td><td class="text-center">${shiftDataProses.shift1.pit_garam}</td><td class="text-center">${shiftDataProses.shift2.pit_garam}</td><td class="text-center">${shiftDataProses.shift3.pit_garam}</td></tr>
                                    <tr><td><strong>Pit Domestik (m³)</strong></td><td class="text-center">${shiftDataProses.shift1.pit_domestik}</td><td class="text-center">${shiftDataProses.shift2.pit_domestik}</td><td class="text-center">${shiftDataProses.shift3.pit_domestik}</td></tr>
                                    <tr><td><strong>Pit Produksi Step 3 (m³)</strong></td><td class="text-center">${shiftDataProses.shift1.pit_produksi_step3}</td><td class="text-center">${shiftDataProses.shift2.pit_produksi_step3}</td><td class="text-center">${shiftDataProses.shift3.pit_produksi_step3}</td></tr>
                                    <tr><td><strong>Pit Storage (m³)</strong></td><td class="text-center">${shiftDataProses.shift1.pit_storage}</td><td class="text-center">${shiftDataProses.shift2.pit_storage}</td><td class="text-center">${shiftDataProses.shift3.pit_storage}</td></tr>
                                    <tr><td><strong>Pit Proses WWTP 2 (m³)</strong></td><td class="text-center">${shiftDataProses.shift1.pit_proses_wwtp2}</td><td class="text-center">${shiftDataProses.shift2.pit_proses_wwtp2}</td><td class="text-center">${shiftDataProses.shift3.pit_proses_wwtp2}</td></tr>
                                    <tr><td><strong>Pit Outlet (m³)</strong></td><td class="text-center">${shiftDataProses.shift1.pit_outlet}</td><td class="text-center">${shiftDataProses.shift2.pit_outlet}</td><td class="text-center">${shiftDataProses.shift3.pit_outlet}</td></tr>
                                    <tr><td><strong>Pit Boiler (m³)</strong></td><td class="text-center">${shiftDataProses.shift1.pit_boiler}</td><td class="text-center">${shiftDataProses.shift2.pit_boiler}</td><td class="text-center">${shiftDataProses.shift3.pit_boiler}</td></tr>
                                </tbody>
                            </table>
                        `;
                        $('#prosesTableContainer').html(prosesHtml);

                        // 2. PERFORMANCE TABLE
                        let shiftDataPerformance = {
                            'shift1': {
                                equalisasi_1: '-',
                                equalisasi_2: '-',
                                netralisasi: '-',
                                sedimentasi_1: '-',
                                sedimentasi_2: '-',
                                outlet_anaerob: '-',
                                aerob: '-',
                                lumpur_aktif: '-',
                                clarifier_2: '-',
                                outlet: '-'
                            },
                            'shift2': {
                                equalisasi_1: '-',
                                equalisasi_2: '-',
                                netralisasi: '-',
                                sedimentasi_1: '-',
                                sedimentasi_2: '-',
                                outlet_anaerob: '-',
                                aerob: '-',
                                lumpur_aktif: '-',
                                clarifier_2: '-',
                                outlet: '-'
                            },
                            'shift3': {
                                equalisasi_1: '-',
                                equalisasi_2: '-',
                                netralisasi: '-',
                                sedimentasi_1: '-',
                                sedimentasi_2: '-',
                                outlet_anaerob: '-',
                                aerob: '-',
                                lumpur_aktif: '-',
                                clarifier_2: '-',
                                outlet: '-'
                            }
                        };
                        if (response.ph && response.ph.length > 0) {
                            response.ph.forEach(function(item) {
                                let s = item.shift;
                                if (shiftDataPerformance[s]) {
                                    shiftDataPerformance[s] = {
                                        equalisasi_1: num(item.equalisasi_1),
                                        equalisasi_2: num(item.equalisasi_2),
                                        netralisasi: num(item.netralisasi),
                                        sedimentasi_1: num(item.sedimentasi_1),
                                        sedimentasi_2: num(item.sedimentasi_2),
                                        outlet_anaerob: num(item.outlet_anaerob),
                                        aerob: num(item.aerob),
                                        lumpur_aktif: num(item.lumpur_aktif),
                                        clarifier_2: num(item.clarifier_2),
                                        outlet: num(item.outlet)
                                    };
                                }
                            });
                        }

                        let performanceHtml = `
                            <table class="table table-bordered table-hover align-middle mb-0 text-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>Parameter pH</th>
                                        <th class="text-center">Shift 1 (06:00 - 14:00)</th>
                                        <th class="text-center">Shift 2 (14:00 - 22:00)</th>
                                        <th class="text-center">Shift 3 (22:00 - 06:00)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td><strong>Equalisasi 1</strong></td><td class="text-center">${shiftDataPerformance.shift1.equalisasi_1}</td><td class="text-center">${shiftDataPerformance.shift2.equalisasi_1}</td><td class="text-center">${shiftDataPerformance.shift3.equalisasi_1}</td></tr>
                                    <tr><td><strong>Equalisasi 2</strong></td><td class="text-center">${shiftDataPerformance.shift1.equalisasi_2}</td><td class="text-center">${shiftDataPerformance.shift2.equalisasi_2}</td><td class="text-center">${shiftDataPerformance.shift3.equalisasi_2}</td></tr>
                                    <tr><td><strong>Netralisasi</strong></td><td class="text-center">${shiftDataPerformance.shift1.netralisasi}</td><td class="text-center">${shiftDataPerformance.shift2.netralisasi}</td><td class="text-center">${shiftDataPerformance.shift3.netralisasi}</td></tr>
                                    <tr><td><strong>Sedimentasi 1</strong></td><td class="text-center">${shiftDataPerformance.shift1.sedimentasi_1}</td><td class="text-center">${shiftDataPerformance.shift2.sedimentasi_1}</td><td class="text-center">${shiftDataPerformance.shift3.sedimentasi_1}</td></tr>
                                    <tr><td><strong>Sedimentasi 2</strong></td><td class="text-center">${shiftDataPerformance.shift1.sedimentasi_2}</td><td class="text-center">${shiftDataPerformance.shift2.sedimentasi_2}</td><td class="text-center">${shiftDataPerformance.shift3.sedimentasi_2}</td></tr>
                                    <tr><td><strong>Outlet Anaerob</strong></td><td class="text-center">${shiftDataPerformance.shift1.outlet_anaerob}</td><td class="text-center">${shiftDataPerformance.shift2.outlet_anaerob}</td><td class="text-center">${shiftDataPerformance.shift3.outlet_anaerob}</td></tr>
                                    <tr><td><strong>Aerob</strong></td><td class="text-center">${shiftDataPerformance.shift1.aerob}</td><td class="text-center">${shiftDataPerformance.shift2.aerob}</td><td class="text-center">${shiftDataPerformance.shift3.aerob}</td></tr>
                                    <tr><td><strong>Lumpur Aktif</strong></td><td class="text-center">${shiftDataPerformance.shift1.lumpur_aktif}</td><td class="text-center">${shiftDataPerformance.shift2.lumpur_aktif}</td><td class="text-center">${shiftDataPerformance.shift3.lumpur_aktif}</td></tr>
                                    <tr><td><strong>Clarifier 2</strong></td><td class="text-center">${shiftDataPerformance.shift1.clarifier_2}</td><td class="text-center">${shiftDataPerformance.shift2.clarifier_2}</td><td class="text-center">${shiftDataPerformance.shift3.clarifier_2}</td></tr>
                                    <tr><td><strong>Outlet</strong></td><td class="text-center">${shiftDataPerformance.shift1.outlet}</td><td class="text-center">${shiftDataPerformance.shift2.outlet}</td><td class="text-center">${shiftDataPerformance.shift3.outlet}</td></tr>
                                </tbody>
                            </table>
                        `;
                        $('#performanceTableContainer').html(performanceHtml);

                        // 3. SLUDGE TABLE
                        let shiftDataSludge = {
                            'shift1': {
                                drain_lumpur: '-',
                                running_hour_scp: '-',
                                hasil_lumpur: '-',
                                sludge_content: '-'
                            },
                            'shift2': {
                                drain_lumpur: '-',
                                running_hour_scp: '-',
                                hasil_lumpur: '-',
                                sludge_content: '-'
                            },
                            'shift3': {
                                drain_lumpur: '-',
                                running_hour_scp: '-',
                                hasil_lumpur: '-',
                                sludge_content: '-'
                            }
                        };
                        if (response.sludge && response.sludge.length > 0) {
                            response.sludge.forEach(function(item) {
                                let s = String(item.shift);
                                if (shiftDataSludge[s]) {
                                    shiftDataSludge[s] = {
                                        drain_lumpur: num(item.drain_lumpur),
                                        running_hour_scp: num(item.running_hour_scp),
                                        hasil_lumpur: num(item.hasil_lumpur),
                                        sludge_content: num(item.sludge_content)
                                    };
                                }
                            });
                        }

                        let sludgeHtml = `
                            <table class="table table-bordered table-hover align-middle mb-0 text-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>Parameter Sludge</th>
                                        <th class="text-center">Shift 1 (06:00 - 14:00)</th>
                                        <th class="text-center">Shift 2 (14:00 - 22:00)</th>
                                        <th class="text-center">Shift 3 (22:00 - 06:00)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td><strong>Drain Lumpur (m³)</strong></td><td class="text-center">${shiftDataSludge['shift1'].drain_lumpur}</td><td class="text-center">${shiftDataSludge['shift2'].drain_lumpur}</td><td class="text-center">${shiftDataSludge['shift3'].drain_lumpur}</td></tr>
                                    <tr><td><strong>Running Hour SCP (jam)</strong></td><td class="text-center">${shiftDataSludge['shift1'].running_hour_scp}</td><td class="text-center">${shiftDataSludge['shift2'].running_hour_scp}</td><td class="text-center">${shiftDataSludge['shift3'].running_hour_scp}</td></tr>
                                    <tr><td><strong>Hasil Lumpur (ton)</strong></td><td class="text-center">${shiftDataSludge['shift1'].hasil_lumpur}</td><td class="text-center">${shiftDataSludge['shift2'].hasil_lumpur}</td><td class="text-center">${shiftDataSludge['shift3'].hasil_lumpur}</td></tr>
                                    <tr><td><strong>Content Sludge (%)</strong></td><td class="text-center">${shiftDataSludge['shift1'].sludge_content}</td><td class="text-center">${shiftDataSludge['shift2'].sludge_content}</td><td class="text-center">${shiftDataSludge['shift3'].sludge_content}</td></tr>
                                </tbody>
                            </table>
                        `;
                        $('#sludgeTableContainer').html(sludgeHtml);

                        // Load Action Buttons in Footer
                        let footerActions =
                            `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>`;

                        const isPending = activeTab === 'pending';
                        const isMyTurn = (userJabatan === 'foreman' && status === 'submitted') ||
                            (userJabatan === 'supervisor' && status === 'approved_foreman');

                        if (isPending && isMyTurn) {
                            footerActions = `
                                <button type="button" class="btn btn-danger" onclick="rejectRecord(${record.id})">
                                    <i class="ri-close-circle-line me-1"></i> Tolak
                                </button>
                                <button type="button" class="btn btn-success" onclick="approveRecord(${record.id})">
                                    <i class="ri-checkbox-circle-line me-1"></i> Setujui
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            `;
                        }
                        $('#modalFooterActions').html(footerActions);

                        $('#detailApprovalModal').modal('show');
                    },
                    error: function(xhr) {
                        showLoading(false);
                        Swal.fire('Error', 'Gagal memuat detail laporan.', 'error');
                    }
                });
            };

            window.approveRecord = function(id) {
                Swal.fire({
                    title: 'Konfirmasi Setuju',
                    text: 'Apakah Anda yakin ingin menyetujui data harian WWTP ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Ya, Setujui',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        showLoading(true);
                        $.ajax({
                            url: `{{ url('wwtp-approval') }}/${id}/approve`,
                            method: 'POST',
                            data: {
                                _token: csrfToken
                            },
                            success: function(res) {
                                showLoading(false);
                                $('#detailApprovalModal').modal('hide');
                                Swal.fire('Berhasil!', res.message, 'success');
                                loadApprovals();
                            },
                            error: function(xhr) {
                                showLoading(false);
                                Swal.fire('Gagal!', xhr.responseJSON?.message ||
                                    'Gagal memproses approval.', 'error');
                            }
                        });
                    }
                });
            };

            window.rejectRecord = function(id) {
                Swal.fire({
                    title: 'Tolak Laporan',
                    text: 'Masukkan alasan penolakan:',
                    input: 'textarea',
                    inputPlaceholder: 'Alasan penolakan...',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Tolak',
                    cancelButtonText: 'Batal',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Alasan penolakan wajib diisi!'
                        }
                    }
                }).then(result => {
                    if (result.isConfirmed && result.value) {
                        showLoading(true);
                        $.ajax({
                            url: `{{ url('wwtp-approval') }}/${id}/reject`,
                            method: 'POST',
                            data: {
                                _token: csrfToken,
                                reason: result.value
                            },
                            success: function(res) {
                                showLoading(false);
                                $('#detailApprovalModal').modal('hide');
                                Swal.fire('Ditolak!', res.message, 'info');
                                loadApprovals();
                            },
                            error: function(xhr) {
                                showLoading(false);
                                Swal.fire('Gagal!', xhr.responseJSON?.message ||
                                    'Gagal menolak laporan.', 'error');
                            }
                        });
                    }
                });
            };

            function showLoading(show) {
                if (show) $('#loadingOverlay').removeClass('d-none');
                else $('#loadingOverlay').addClass('d-none');
            }

            function statusBadgeText(val) {
                if (val === 'ON') return '<span class="badge bg-success">ON</span>';
                if (val === 'OFF') return '<span class="badge bg-danger">OFF</span>';
                return val;
            }

            const num = (number) => {
                if (number == null || number === '') return '-';
                const parsed = parseFloat(number);
                return parsed % 1 === 0 ? parsed.toFixed(0) : parsed.toString();
            };

            function formatDate(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }
        });
    </script>
@endsection
