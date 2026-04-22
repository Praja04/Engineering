@extends('layouts.app')

@section('title', 'Water Softener — Approval')

{{-- Halaman ini hanya untuk Supervisor --}}
@php $jabatan = auth()->user()->jabatan ?? 'operator'; @endphp

@section('styles')
<style>
    /* ── Header ── */
    .apv-header {
        background: linear-gradient(135deg, #1a1240 0%, #251a5c 60%, #1e1650 100%);
        border-radius: 16px;
        padding: 28px 32px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    .apv-header::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -40px;
        width: 220px;
        height: 220px;
        background: rgba(139, 92, 246, .08);
        border-radius: 50%;
    }

    .apv-header h4 {
        color: #ede9fe;
        font-weight: 700;
        margin: 0;
        font-size: 1.35rem;
    }

    .apv-header p {
        color: #a78bfa;
        margin: 4px 0 0;
        font-size: 0.88rem;
    }

    /* ── Role Badge ── */
    .role-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: .03em;
    }

    .role-pill.supervisor {
        background: rgba(139, 92, 246, .15);
        border: 1px solid rgba(139, 92, 246, .4);
        color: #a78bfa;
    }

    /* ── Approval Cards ── */
    .apv-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #ece9fa;
        overflow: hidden;
        margin-bottom: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
        transition: transform .18s, box-shadow .18s;
    }

    .apv-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, .08);
    }

    [data-layout-mode="dark"] .apv-card {
        background: #1c1838;
        border-color: #2e2a4a;
    }

    .apv-card-header {
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        border-bottom: 1px solid #f0ecfd;
    }

    [data-layout-mode="dark"] .apv-card-header {
        border-color: #2e2a4a;
    }

    .apv-card-header .month-badge {
        font-size: 1.1rem;
        font-weight: 800;
        color: #4c1d95;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    [data-layout-mode="dark"] .apv-card-header .month-badge {
        color: #c4b5fd;
    }

    .apv-card-body {
        padding: 16px 20px;
    }

    .apv-meta {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
        font-size: 0.82rem;
        color: #6b7280;
        margin-bottom: 14px;
    }

    .apv-meta strong {
        color: #374151;
    }

    [data-layout-mode="dark"] .apv-meta {
        color: #9ca3af;
    }

    [data-layout-mode="dark"] .apv-meta strong {
        color: #d1d5db;
    }

    .apv-timeline {
        display: flex;
        align-items: center;
        gap: 0;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .apv-step {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 20px;
    }

    .apv-step.done {
        background: #d1fae5;
        color: #065f46;
    }

    .apv-step.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .apv-step.waiting {
        background: #f3f4f6;
        color: #9ca3af;
    }

    .apv-step.final {
        background: #ede9fe;
        color: #4c1d95;
    }

    .apv-arrow {
        color: #d1d5db;
        margin: 0 4px;
        font-size: 1rem;
    }

    [data-layout-mode="dark"] .apv-step.done {
        background: #064e3b;
        color: #6ee7b7;
    }

    [data-layout-mode="dark"] .apv-step.pending {
        background: #451a03;
        color: #fcd34d;
    }

    [data-layout-mode="dark"] .apv-step.waiting {
        background: #1f2937;
        color: #6b7280;
    }

    [data-layout-mode="dark"] .apv-step.final {
        background: #2e1065;
        color: #c4b5fd;
    }

    /* ── Status Badges ── */
    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .status-chip.waiting_supervisor {
        background: #fef9c3;
        color: #713f12;
        border: 1px solid #fde047;
    }

    .status-chip.approved_supervisor {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }

    /* ── Action Buttons ── */
    .btn-approve {
        background: linear-gradient(135deg, #059669, #047857);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 9px 22px;
        font-weight: 700;
        font-size: 0.88rem;
        transition: all .2s;
    }

    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(5, 150, 105, .35);
        color: #fff;
    }

    .btn-approve:disabled {
        opacity: .5;
        transform: none;
    }

    /* ── Empty State ── */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 12px;
        display: block;
    }

    .empty-state p {
        font-size: 0.9rem;
        margin: 0;
    }

    /* ── Modal ── */
    .modal-apv .modal-content {
        border-radius: 16px;
        border: none;
        overflow: hidden;
    }

    .modal-apv .modal-header {
        background: linear-gradient(135deg, #1a1240, #251a5c);
        color: #ede9fe;
        border: none;
        padding: 20px 24px;
    }

    .modal-apv .modal-footer {
        border-top: 1px solid #f0ecfd;
        padding: 16px 24px;
    }

    /* ── Detail Table ── */
    .detail-tbl thead th {
        font-size: 0.73rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
        background: #f5f3ff;
        white-space: nowrap;
        border-bottom: 2px solid #ddd6fe;
    }

    .detail-tbl tbody td {
        font-size: 0.82rem;
        padding: 8px 10px;
        vertical-align: middle;
    }

    .badge-over {
        background: #fee2e2;
        color: #b91c1c;
        font-size: 0.72rem;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 700;
    }

    .badge-normal {
        background: #d1fae5;
        color: #065f46;
        font-size: 0.72rem;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 700;
    }

    /* ── Loading ── */
    #loadingState {
        text-align: center;
        padding: 40px;
        color: #9ca3af;
    }

    /* ── Access Denied ── */
    .access-denied {
        text-align: center;
        padding: 80px 20px;
        color: #6b7280;
    }

    .access-denied i {
        font-size: 4rem;
        color: #e5e7eb;
        display: block;
        margin-bottom: 16px;
    }

    .access-denied h5 {
        font-weight: 700;
        color: #374151;
        margin-bottom: 8px;
    }

    @media(max-width:576px) {
        .apv-header {
            padding: 20px;
        }

        .apv-step {
            font-size: 0.72rem;
            padding: 5px 10px;
        }
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- ── Header ── --}}
        <div class="apv-header" data-aos="fade-down">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                <div>
                    <h4><i class="bx bx-check-shield me-2"></i>Water Softener — Approval</h4>
                    <p>Persetujuan laporan bulanan Water Softener</p>
                </div>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <span class="role-pill supervisor">
                        <i class="bx bx-shield-quarter"></i>Supervisor
                    </span>
                    <a href="{{ route('water-softener.rekap') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                        <i class="bx bx-bar-chart-alt-2 me-1"></i>Rekap
                    </a>
                </div>
            </div>
        </div>

        @if ($jabatan !== 'supervisor')
        {{-- ── Bukan Supervisor: tampilkan pesan akses ditolak ── --}}
        <div class="apv-card" data-aos="fade-up">
            <div class="apv-card-body">
                <div class="access-denied">
                    <i class="bx bx-lock-alt"></i>
                    <h5>Akses Terbatas</h5>
                    <p>Halaman ini hanya dapat diakses oleh <strong>Supervisor</strong>.</p>
                    <a href="{{ route('water-softener.rekap') }}" class="btn btn-outline-primary rounded-pill mt-3 px-4">
                        <i class="bx bx-arrow-back me-1"></i>Kembali ke Rekap
                    </a>
                </div>
            </div>
        </div>
        @else
        {{-- ── Info Banner ── --}}
        <div class="alert alert-info d-flex align-items-center gap-2 py-2 px-3 rounded-3 mb-4" style="font-size:.84rem; border-radius:12px!important;" data-aos="fade-up" data-aos-delay="50" id="infoBanner">
            <i class="bx bx-info-circle fs-5 flex-shrink-0"></i>
            <span id="infoBannerText">Memuat…</span>
        </div>

        {{-- ── Tab Pending / History ── --}}
        <div class="mb-3 d-flex gap-2" data-aos="fade-up" data-aos-delay="70">
            <button class="btn btn-sm btn-primary rounded-pill px-3 tab-btn active" data-tab="pending">
                ⏳ Menunggu Persetujuan
            </button>
            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 tab-btn" data-tab="history">
                ✅ Sudah Disetujui
            </button>
        </div>

        {{-- ── List Approvals ── --}}
        <div id="loadingState" data-aos="fade-up" data-aos-delay="80">
            <i class="bx bx-loader-alt bx-spin fs-2 d-block mb-2"></i>Memuat daftar laporan…
        </div>

        <div id="approvalList"></div>
        @endif

    </div>
</div>

@if ($jabatan === 'supervisor')
{{-- ── Modal Review & Approve ── --}}
<div class="modal fade modal-apv" id="modalSupervisor" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-check-double me-2"></i>Review & Setujui Laporan
                    <small class="ms-2 fw-normal opacity-75" id="modalMonthLabel"></small>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning py-2 px-3 mb-3" style="font-size:.83rem;">
                    <i class="bx bx-error me-1"></i>
                    Ini adalah <strong>persetujuan final</strong>. Setelah disetujui, laporan terkunci permanen dan tidak bisa diubah.
                </div>

                {{-- Data Preview --}}
                <div class="table-responsive" style="max-height:340px;overflow-y:auto;">
                    <table class="table table-sm detail-tbl">
                        <thead>
                            <tr>
                                <th>Tgl</th>
                                <th>WS1 H-In</th>
                                <th>WS1 H-Out</th>
                                <th>WS1 Flow</th>
                                <th>WS2 H-In</th>
                                <th>WS2 H-Out</th>
                                <th>WS2 Flow</th>
                                <th>Rg1</th>
                                <th>Rg2</th>
                            </tr>
                        </thead>
                        <tbody id="detailTblBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-approve" id="btnApproveSvConfirm">
                    <i class="bx bx-check-double me-1"></i>Setujui Final
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
@if ($jabatan === 'supervisor')
<script>
    $(function() {
        const CSRF = $('meta[name="csrf-token"]').attr('content');
        const LIST_URL = "{{ route('water-softener.approval.list') }}";
        const DATA_URL = "{{ route('water-softener.data') }}";

        let activeApprovalId = null;
        let currentTab = 'pending';

        const months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // ── Info Banner ───────────────────────────────────────────────
        const bannerText = {
            pending: 'Menampilkan laporan yang telah diajukan Foreman dan <strong>menunggu persetujuan Anda</strong>.',
            history: 'Menampilkan laporan yang <strong>sudah Anda setujui final</strong>.',
        };

        function updateBanner() {
            $('#infoBannerText').html(bannerText[currentTab] ?? bannerText.pending);
        }

        // ── Load List ─────────────────────────────────────────────────
        function loadList() {
            $('#loadingState').show();
            $('#approvalList').empty();
            updateBanner();

            $.ajax({
                url: LIST_URL,
                data: {
                    tab: currentTab
                },
                success: function(rows) {
                    $('#loadingState').hide();
                    if (!rows.length) {
                        const msg = currentTab === 'pending' ?
                            'Tidak ada laporan yang perlu diproses saat ini.' :
                            'Belum ada riwayat laporan yang sudah disetujui.';
                        $('#approvalList').html(`
                            <div class="empty-state" data-aos="fade-up">
                                <i class="bx bx-inbox"></i>
                                <p>${msg}</p>
                            </div>`);
                        return;
                    }
                    rows.forEach((apv, i) => renderCard(apv, i));
                },
                error: function() {
                    $('#loadingState').hide();
                    $('#approvalList').html('<div class="alert alert-danger">Gagal memuat daftar laporan.</div>');
                }
            });
        }

        // ── Render Card ───────────────────────────────────────────────
        function renderCard(apv, idx) {
            const st = apv.status;
            const bulanNm = months[(apv.bulan - 1)] || apv.bulan;

            // Tombol approve hanya di tab pending
            let actionBtn = '';
            if (currentTab === 'pending' && st === 'waiting_supervisor') {
                actionBtn = `
                    <button class="btn btn-approve btn-sv-action"
                            data-id="${apv.id}" data-bulan="${apv.bulan}" data-tahun="${apv.tahun}">
                        <i class="bx bx-check-double me-1"></i>Review & Setujui
                    </button>`;
            }

            const subAt = apv.submitted_at ?
                `Diajukan: <strong>${formatDate(apv.submitted_at)}</strong>` :
                'Belum diajukan';

            const svAt = apv.supervisor_approved_at ?
                ` &nbsp;<small class="text-muted">(${formatDate(apv.supervisor_approved_at)})</small>` :
                '';

            const html = `
            <div class="apv-card" data-aos="fade-up" data-aos-delay="${idx * 60}">
                <div class="apv-card-header">
                    <div class="month-badge">
                        <i class="bx bx-calendar-check" style="font-size:1.4rem;"></i>
                        ${bulanNm} ${apv.tahun}
                    </div>
                    <span class="status-chip ${st}">${statusText(st)}</span>
                </div>
                <div class="apv-card-body">
                    <div class="apv-meta">
                        <span><i class="bx bx-hard-hat me-1"></i>Foreman: <strong>${apv.foreman?.username ?? '-'}</strong></span>
                        <span><i class="bx bx-time me-1"></i>${subAt}</span>
                        ${st === 'approved_supervisor' ? `<span><i class="bx bx-check-double me-1"></i>Disetujui${svAt}</span>` : ''}
                    </div>
                    <div class="apv-timeline mb-3">${buildTimeline(apv)}</div>
                    <div class="d-flex justify-content-end">${actionBtn}</div>
                </div>
            </div>`;

            $('#approvalList').append(html);
        }

        // ── Timeline ──────────────────────────────────────────────────
        function buildTimeline(apv) {
            const st = apv.status;
            const s1 = ['waiting_supervisor', 'approved_supervisor'].includes(st) ? 'done' : 'pending';
            const s2 = st === 'approved_supervisor' ? 'final' : (st === 'waiting_supervisor' ? 'pending' : 'waiting');

            return `
            <span class="apv-step ${s1}">
                <i class="bx bx-${s1 === 'done' ? 'check' : 'time'}"></i>Diajukan Foreman
            </span>
            <span class="apv-arrow">›</span>
            <span class="apv-step ${s2}">
                <i class="bx bx-${s2 === 'final' ? 'check-double' : 'shield-quarter'}"></i>Supervisor
            </span>`;
        }

        function statusText(st) {
            const map = {
                'waiting_supervisor': '⏳ Menunggu Persetujuan',
                'approved_supervisor': '🔒 Final Disetujui',
            };
            return map[st] || st;
        }

        // ── Supervisor: klik Review ───────────────────────────────────
        $(document).on('click', '.btn-sv-action', function() {
            const id = $(this).data('id');
            const bulan = $(this).data('bulan');
            const tahun = $(this).data('tahun');
            activeApprovalId = id;

            const bulanNm = months[(bulan - 1)] || bulan;
            $('#modalMonthLabel').text(`— ${bulanNm} ${tahun}`);

            loadDetailTable(bulan, tahun, '#detailTblBody', function() {
                new bootstrap.Modal($('#modalSupervisor')[0]).show();
            });
        });

        // ── Konfirmasi Approve ────────────────────────────────────────
        $('#btnApproveSvConfirm').on('click', function() {
            const $btn = $(this).prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i>Menyetujui…');

            $.ajax({
                url: `/utility/water-softener/approve/supervisor/${activeApprovalId}`,
                method: 'POST',
                data: {
                    _token: CSRF
                },
                success: function(res) {
                    toastr.success(res.message, 'Berhasil!', {
                        timeOut: 4000
                    });
                    bootstrap.Modal.getInstance($('#modalSupervisor')[0]).hide();
                    loadList();
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message ?? 'Gagal menyetujui.', 'Error', {
                        timeOut: 4000
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="bx bx-check-double me-1"></i>Setujui Final');
                }
            });
        });

        // ── Detail Table ──────────────────────────────────────────────
        function loadDetailTable(bulan, tahun, selector, cb) {
            const $body = $(selector);
            const months2 = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            $body.html('<tr><td colspan="9" class="text-center py-3"><i class="bx bx-loader-alt bx-spin me-1"></i>Memuat…</td></tr>');

            $.ajax({
                url: DATA_URL,
                data: {
                    bulan,
                    tahun
                },
                success: function(res) {
                    $body.empty();
                    if (!res.data.length) {
                        $body.html('<tr><td colspan="9" class="text-center text-muted">Tidak ada data</td></tr>');
                    } else {
                        res.data.forEach(r => {
                            const tgl = r.tanggal ? (() => {
                                const d = new Date(r.tanggal);
                                return `${d.getDate()} ${months2[d.getMonth()]}`;
                            })() : '-';
                            const ho1 = hardnessBadge(r.ws1_hardness_out);
                            const ho2 = hardnessBadge(r.ws2_hardness_out);
                            $body.append(`
                            <tr>
                                <td class="fw-semibold">${tgl}</td>
                                <td>${r.ws1_hardness_in ?? '-'}</td>
                                <td>${ho1}</td>
                                <td>${r.ws1_flow ?? '-'}</td>
                                <td>${r.ws2_hardness_in ?? '-'}</td>
                                <td>${ho2}</td>
                                <td>${r.ws2_flow ?? '-'}</td>
                                <td>${r.regen1_jam ? '✓' : '-'}</td>
                                <td>${r.regen2_jam ? '✓' : '-'}</td>
                            </tr>`);
                        });
                    }
                    if (cb) cb();
                },
                error: function() {
                    $body.html('<tr><td colspan="9" class="text-center text-danger">Gagal memuat data</td></tr>');
                    if (cb) cb();
                }
            });
        }

        function hardnessBadge(val) {
            if (val === null || val === undefined || val === '') return '-';
            const n = parseFloat(val);
            return n > 10 ?
                `<span class="badge-over">⚠ ${n}</span>` :
                `<span class="badge-normal">${n}</span>`;
        }

        function formatDate(str) {
            if (!str) return '-';
            const d = new Date(str);
            const m = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${d.getDate()} ${m[d.getMonth()]} ${d.getFullYear()}`;
        }

        // ── Tab Switch ────────────────────────────────────────────────
        $(document).on('click', '.tab-btn', function() {
            $('.tab-btn').removeClass('btn-primary active').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
            currentTab = $(this).data('tab');
            loadList();
        });

        // ── Init ──────────────────────────────────────────────────────
        loadList();
    });
</script>
@endif
@endsection