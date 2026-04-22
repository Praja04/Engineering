@extends('layouts.app')

@section('title', 'ESP Report - Approval')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Header --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0" style="background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%);">
                    <div class="card-body py-3 px-4">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="text-white fw-bold mb-0">
                                    <i class="ri-shield-check-line me-2" style="color:#f6c90e;"></i>
                                    ESP Shift Report — Approval
                                </h4>
                                <p class="text-white-50 mb-0 fs-13 mt-1">
                                    @if(auth()->user()->jabatan === 'foreman')
                                    <span class="badge bg-warning text-dark me-1">Foreman</span>
                                    Approve laporan yang telah disubmit operator dan ditujukan kepada Anda
                                    @elseif(auth()->user()->jabatan === 'supervisor')
                                    <span class="badge bg-info text-dark me-1">Supervisor</span>
                                    Final approval laporan yang telah disetujui foreman dan ditujukan kepada Anda
                                    @endif
                                </p>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('esp-operational-report.data') }}" class="btn btn-sm btn-light">
                                    <i class="ri-database-2-line me-1"></i>Lihat Data
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Card --}}
        <div class="row g-3 mt-1">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="background:#fff3cd;width:52px;height:52px;min-width:52px;">
                            <i class="ri-time-line text-warning fs-22"></i>
                        </div>
                        <div>
                            <div class="fs-22 fw-bold" id="count-pending">—</div>
                            <div class="text-muted fs-13">Menunggu Approval Anda</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="background:#d1e7dd;width:52px;height:52px;min-width:52px;">
                            <i class="ri-check-double-line text-success fs-22"></i>
                        </div>
                        <div>
                            <div class="fs-22 fw-bold" id="count-approved">—</div>
                            <div class="text-muted fs-13">Sudah Diapprove</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="background:#cfe2ff;width:52px;height:52px;min-width:52px;">
                            <i class="ri-calendar-line text-primary fs-22"></i>
                        </div>
                        <div>
                            <div class="fs-22 fw-bold" id="count-total">—</div>
                            <div class="text-muted fs-13">Total Laporan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="row mt-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="row align-items-center g-2">
                            <div class="col-auto">
                                <label class="form-label mb-0 fs-13 text-muted">Filter Status</label>
                            </div>
                            <div class="col-auto">
                                <select class="form-select form-select-sm" id="filter-status" style="width:auto;">
                                    <option value="pending">Menunggu Approval</option>
                                    <option value="approved">Sudah Diapprove</option>
                                    <option value="">Semua</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <input type="date" class="form-control form-control-sm" id="filter-tanggal" placeholder="Filter tanggal">
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-outline-secondary" id="btn-refresh">
                                    <i class="ri-refresh-line me-1"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="row mt-2">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold">
                            <i class="ri-list-check me-2 text-warning"></i>
                            Daftar Laporan Shift
                        </h6>
                        <span class="badge bg-warning-subtle text-warning fs-12" id="badge-jabatan">
                            @if(auth()->user()->jabatan === 'foreman') Level: Foreman
                            @elseif(auth()->user()->jabatan === 'supervisor') Level: Supervisor
                            @endif
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Operator</th>
                                        <th>Air (m³)</th>
                                        <th>Steam (ton)</th>
                                        <th>Batubara (ton)</th>
                                        <th>RH Awal</th>
                                        <th>RH Akhir</th>
                                        <th>Disubmit</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-approval">
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-muted">
                                            <i class="ri-loader-4-line"></i> Memuat data...
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

{{-- ══ MODAL DETAIL + APPROVE ═══════════════════════════════════════ --}}
<div class="modal fade" id="modal-approval" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom" style="background:linear-gradient(135deg,#f6c90e,#f7971e);">
                <h5 class="modal-title text-dark fw-bold">
                    <i class="ri-shield-check-line me-2"></i>
                    Review Laporan — <span id="modal-tgl"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Info Operator --}}
                <div class="alert alert-light border mb-3 py-2">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <span class="text-muted fs-12">Operator:</span>
                            <span class="fw-semibold ms-1" id="m-operator">—</span>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted fs-12">Foreman:</span>
                            <span class="fw-semibold ms-1" id="m-foreman">—</span>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted fs-12">Supervisor:</span>
                            <span class="fw-semibold ms-1" id="m-supervisor">—</span>
                        </div>
                    </div>
                </div>

                {{-- Detail data --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <h6 class="fw-semibold text-muted border-bottom pb-2">Pemakaian</h6>
                        <table class="table table-sm table-bordered mb-0">
                            <tr>
                                <th class="text-muted w-50">Air</th>
                                <td id="m-air">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Steam</th>
                                <td id="m-steam">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Batubara</th>
                                <td id="m-batubara">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Efisiensi</th>
                                <td id="m-efisiensi">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Pengisian</th>
                                <td id="m-pengisian">—</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-semibold text-muted border-bottom pb-2">Running Hour & Feed Tank</h6>
                        <table class="table table-sm table-bordered mb-0">
                            <tr>
                                <th class="text-muted w-50">RH Awal</th>
                                <td id="m-rh-awal">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">RH Akhir</th>
                                <td id="m-rh-akhir">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">FT Awal</th>
                                <td id="m-ft-awal">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">FT Akhir</th>
                                <td id="m-ft-akhir">—</td>
                            </tr>
                        </table>
                        <h6 class="fw-semibold text-muted border-bottom pb-2 mt-3">Chemical</h6>
                        <table class="table table-sm table-bordered mb-0">
                            <tr>
                                <th class="text-muted w-50">SCF</th>
                                <td id="m-scf">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">SRTF</th>
                                <td id="m-srtf">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Dosis</th>
                                <td id="m-dosis">—</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Approval chain --}}
                <div class="border rounded-3 p-3 bg-light">
                    <h6 class="fw-semibold mb-3">Status Approval</h6>
                    <div class="d-flex align-items-center gap-2 flex-wrap" id="modal-approval-chain"></div>
                </div>

            </div>
            <div class="modal-footer border-top" id="modal-footer-approve">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btn-do-approve">
                    <i class="ri-check-double-line me-1"></i>
                    <span id="btn-approve-label">Approve</span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(function() {

        const jabatan = '{{ auth()->user()->jabatan }}';

        // Status yang bisa di-approve oleh jabatan ini
        const pendingStatus = jabatan === 'foreman' ?
            'approved_operator' :
            jabatan === 'supervisor' ?
            'approved_foreman' :
            null;

        const statusMap = {
            'approved_operator': {
                label: 'Menunggu Foreman',
                cls: 'bg-warning text-dark'
            },
            'approved_foreman': {
                label: 'Menunggu Supervisor',
                cls: 'bg-info text-dark'
            },
            'approved_supervisor': {
                label: 'Final Approved',
                cls: 'bg-success text-white'
            },
        };

        let currentId = null;

        // ── LOAD DATA ────────────────────────────────────────────────────
        function loadData() {
            const status = $('#filter-status').val();
            const tanggal = $('#filter-tanggal').val();

            $('#tbody-approval').html(
                '<tr><td colspan="10" class="text-center py-4 text-muted"><i class="ri-loader-4-line"></i> Memuat...</td></tr>'
            );

            $.get('{{ route("esp-shift-report.json") }}', {
                status,
                tanggal
            }, function(data) {
                // Hitung summary
                const pending = data.filter(r => r.status === pendingStatus).length;
                const approved = data.filter(r => r.status !== pendingStatus).length;
                $('#count-pending').text(pending);
                $('#count-approved').text(approved);
                $('#count-total').text(data.length);

                if (!data.length) {
                    $('#tbody-approval').html(
                        '<tr><td colspan="10" class="text-center py-4 text-muted">Tidak ada laporan yang ditujukan kepada Anda</td></tr>'
                    );
                    return;
                }

                let html = '';
                data.forEach(function(r) {
                    const s = statusMap[r.status] ?? {
                        label: r.status,
                        cls: 'bg-secondary text-white'
                    };
                    const canApprove = r.status === pendingStatus;
                    const operatorName = r.operator?.username ?? '—';

                    html += `<tr data-row='${JSON.stringify(r)}'>
                    <td class="fw-medium">${r.tanggal_laporan}</td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary">
                            <i class="ri-user-line me-1"></i>${operatorName}
                        </span>
                    </td>
                    <td>${r.pemakaian_air ?? '—'}</td>
                    <td>${r.pemakaian_steam ?? '—'}</td>
                    <td>${r.pemakaian_batubara ?? '—'}</td>
                    <td>${r.running_hour_awal ?? '—'}</td>
                    <td>${r.running_hour_akhir ?? '—'}</td>
                    <td class="fs-12 text-muted">${r.created_at ?? '—'}</td>
                    <td><span class="badge ${s.cls}">${s.label}</span></td>
                    <td>
                        <button class="btn btn-xs btn-outline-warning btn-review py-0 px-2 me-1">
                            <i class="ri-eye-line"></i> Review
                        </button>
                        ${canApprove
                            ? `<button class="btn btn-xs btn-success btn-quick-approve py-0 px-2"
                                data-id="${r.id}">
                                <i class="ri-check-line"></i> Approve
                               </button>`
                            : ''
                        }
                    </td>
                </tr>`;
                });
                $('#tbody-approval').html(html);
            });
        }

        // ── FILTER & REFRESH ─────────────────────────────────────────────
        $('#filter-status, #filter-tanggal').on('change', loadData);
        $('#btn-refresh').on('click', loadData);

        // ── BUKA MODAL REVIEW ────────────────────────────────────────────
        $(document).on('click', '.btn-review', function() {
            const r = $(this).closest('tr').data('row');
            currentId = r.id;

            $('#modal-tgl').text(r.tanggal_laporan);

            // Info penanggungjawab
            $('#m-operator').text(r.operator?.username ?? '—');
            $('#m-foreman').text(r.foreman?.username ?? '—');
            $('#m-supervisor').text(r.supervisor?.username ?? '—');

            $('#m-air').text(r.pemakaian_air !== null ? r.pemakaian_air + ' m³' : '—');
            $('#m-steam').text(r.pemakaian_steam !== null ? r.pemakaian_steam + ' ton' : '—');
            $('#m-batubara').text(r.pemakaian_batubara !== null ? r.pemakaian_batubara + ' ton' : '—');
            $('#m-efisiensi').text(r.efisiensi_batubara !== null ? r.efisiensi_batubara + ' %' : '—');
            $('#m-pengisian').text(r.pengisian_batubara !== null ? r.pengisian_batubara + ' ton' : '—');
            $('#m-rh-awal').text(r.running_hour_awal !== null ? r.running_hour_awal + ' jam' : '—');
            $('#m-rh-akhir').text(r.running_hour_akhir !== null ? r.running_hour_akhir + ' jam' : '—');
            $('#m-ft-awal').text(r.feed_tank_awal !== null ? r.feed_tank_awal + ' m³' : '—');
            $('#m-ft-akhir').text(r.feed_tank_akhir !== null ? r.feed_tank_akhir + ' m³' : '—');
            $('#m-scf').text(r.chemical_scf !== null ? r.chemical_scf + ' L' : '—');
            $('#m-srtf').text(r.chemical_srtf !== null ? r.chemical_srtf + ' L' : '—');
            $('#m-dosis').text(r.dosis !== null ? r.dosis + ' ppm' : '—');

            // Approval chain visual
            const steps = [{
                    key: 'approved_operator',
                    label: 'Operator',
                    icon: 'ri-user-line',
                    name: r.operator?.username
                },
                {
                    key: 'approved_foreman',
                    label: 'Foreman',
                    icon: 'ri-user-star-line',
                    name: r.foreman?.username
                },
                {
                    key: 'approved_supervisor',
                    label: 'Supervisor',
                    icon: 'ri-shield-check-line',
                    name: r.supervisor?.username
                },
            ];
            const order = steps.map(s => s.key);
            const currIdx = order.indexOf(r.status);
            let chain = '';
            steps.forEach(function(s, i) {
                const done = i <= currIdx;
                chain += `
                <div class="d-flex flex-column align-items-center gap-1 text-center" style="min-width:90px;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                        style="width:44px;height:44px;font-size:20px;
                            background:${done ? '#198754' : '#e9ecef'};
                            color:${done ? '#fff' : '#6c757d'};">
                        <i class="${s.icon}"></i>
                    </div>
                    <small class="text-muted fw-medium">${s.label}</small>
                    <small class="text-dark fw-semibold fs-11">${s.name ?? '—'}</small>
                    <span class="badge ${done ? 'bg-success' : 'bg-secondary'} fs-10">
                        ${done ? '✓ Done' : 'Pending'}
                    </span>
                </div>
                ${i < steps.length - 1
                    ? `<div class="flex-grow-1 border-top mt-3" style="min-width:30px;border-width:2px!important;
                        border-color:${done && i < currIdx ? '#198754' : '#dee2e6'}!important;"></div>`
                    : ''
                }`;
            });
            $('#modal-approval-chain').html(chain);

            // Tampilkan/sembunyikan tombol approve
            const canApprove = r.status === pendingStatus;
            if (canApprove) {
                $('#btn-do-approve').show();
                $('#modal-footer-approve').show();
                const labelMap = {
                    foreman: 'Approve sebagai Foreman',
                    supervisor: 'Final Approve sebagai Supervisor',
                };
                $('#btn-approve-label').text(labelMap[jabatan] ?? 'Approve');
            } else {
                $('#btn-do-approve').hide();
            }

            new bootstrap.Modal('#modal-approval').show();
        });

        // ── QUICK APPROVE (dari baris tabel) ─────────────────────────────
        $(document).on('click', '.btn-quick-approve', function(e) {
            e.stopPropagation();
            const id = $(this).data('id');
            Swal.fire({
                title: 'Konfirmasi Approve',
                text: 'Apakah Anda yakin ingin menyetujui laporan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Approve',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#198754',
            }).then(function(result) {
                if (result.isConfirmed) doApprove(id);
            });
        });

        // ── APPROVE DARI MODAL ────────────────────────────────────────────
        $('#btn-do-approve').on('click', function() {
            Swal.fire({
                title: 'Konfirmasi Approval',
                text: 'Anda akan menyetujui laporan ini. Lanjutkan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Approve',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#198754',
            }).then(function(result) {
                if (result.isConfirmed) {
                    bootstrap.Modal.getInstance('#modal-approval').hide();
                    doApprove(currentId);
                }
            });
        });

        // ── FUNGSI APPROVE ────────────────────────────────────────────────
        function doApprove(id) {
            const url = jabatan === 'foreman' ?
                `/utility/esp-shift-report/${id}/approve-foreman` :
                `/utility/esp-shift-report/${id}/approve-supervisor`;

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message ?? 'Laporan berhasil diapprove.',
                        timer: 1800,
                        showConfirmButton: false,
                    });
                    loadData();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message ?? 'Terjadi kesalahan.',
                    });
                }
            });
        }

        // ── INIT ──────────────────────────────────────────────────────────
        loadData();
    });
</script>
@endsection