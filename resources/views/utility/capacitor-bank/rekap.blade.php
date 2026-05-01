@extends('layouts.app')

@section('title', 'Capacitor Bank — Rekap Data')

@section('styles')
<style>
    /* ── Header ── */
    .rekap-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%);
        border-radius: 16px;
        padding: 28px 32px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    .rekap-header::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(147, 197, 253, 0.07);
        border-radius: 50%;
    }

    .rekap-header h4 {
        color: #dbeafe;
        font-weight: 700;
        margin: 0;
        font-size: 1.35rem;
    }

    .rekap-header p {
        color: #93c5fd;
        margin: 4px 0 0;
        font-size: 0.88rem;
    }

    /* ── Filter Card ── */
    .filter-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e0e7ff;
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
    }

    [data-layout-mode="dark"] .filter-card {
        background: #1e293b;
        border-color: #334155;
    }

    /* ── Summary Cards ── */
    .sum-row {
        margin-bottom: 20px;
    }

    .sum-card {
        border-radius: 14px;
        padding: 18px 20px;
        border: 1px solid transparent;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .sum-card .sum-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        margin-bottom: 10px;
    }

    .sum-card .sum-val {
        font-size: 1.8rem;
        font-weight: 800;
        line-height: 1;
    }

    .sum-card .sum-lbl {
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: .04em;
        margin-top: 4px;
        opacity: .75;
    }

    .sum-card .sum-sub {
        font-size: 0.75rem;
        margin-top: 6px;
        opacity: .6;
    }

    .sum-total {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1e3a8a;
    }

    .sum-total .sum-icon {
        background: #1e3a8a15;
        color: #1e3a8a;
    }

    .sum-high {
        background: #fef3c7;
        border-color: #fcd34d;
        color: #92400e;
    }

    .sum-high .sum-icon {
        background: #92400e15;
        color: #92400e;
    }

    .sum-hot {
        background: #fee2e2;
        border-color: #fca5a5;
        color: #b91c1c;
    }

    .sum-hot .sum-icon {
        background: #b91c1c15;
        color: #b91c1c;
    }

    .sum-status {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #166534;
    }

    .sum-status .sum-icon {
        background: #16653415;
        color: #166534;
    }

    [data-layout-mode="dark"] .sum-total {
        background: #1e2a44;
        border-color: #1e40af;
        color: #93c5fd;
    }

    [data-layout-mode="dark"] .sum-high {
        background: #422006;
        border-color: #78350f;
        color: #fcd34d;
    }

    [data-layout-mode="dark"] .sum-hot {
        background: #451a1a;
        border-color: #7f1d1d;
        color: #fca5a5;
    }

    [data-layout-mode="dark"] .sum-status {
        background: #14532d;
        border-color: #15803d;
        color: #86efac;
    }

    /* ── Table Card ── */
    .table-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e0e7ff;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
        margin-bottom: 20px;
    }

    [data-layout-mode="dark"] .table-card {
        background: #1e293b;
        border-color: #334155;
    }

    .table-card-header {
        padding: 14px 20px;
        background: linear-gradient(90deg, #eff6ff, #dbeafe);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    [data-layout-mode="dark"] .table-card-header {
        background: linear-gradient(90deg, #1e2a44, #1e40af);
    }

    .table-card-header h6 {
        margin: 0;
        font-weight: 700;
        color: #1e3a8a;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    [data-layout-mode="dark"] .table-card-header h6 {
        color: #93c5fd;
    }

    #tblRekap thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
        background: #f8fafc;
        white-space: nowrap;
        padding: 10px 12px;
        border-bottom: 2px solid #e2e8f0;
    }

    [data-layout-mode="dark"] #tblRekap thead th {
        background: #1e293b;
        border-color: #475569;
    }

    #tblRekap tbody td {
        padding: 9px 12px;
        font-size: 0.84rem;
        vertical-align: middle;
    }

    #tblRekap tbody tr:hover {
        background: #eff6ff40;
    }

    .badge-high {
        background: #fef3c7;
        color: #92400e;
        font-size: 0.72rem;
        padding: 3px 8px;
        border-radius: 10px;
        font-weight: 700;
    }

    .badge-hot {
        background: #fee2e2;
        color: #b91c1c;
        font-size: 0.72rem;
        padding: 3px 8px;
        border-radius: 10px;
        font-weight: 700;
    }

    .badge-normal {
        background: #d1fae5;
        color: #065f46;
        font-size: 0.72rem;
        padding: 3px 8px;
        border-radius: 10px;
        font-weight: 700;
    }

    .badge-na {
        background: #f3f4f6;
        color: #9ca3af;
        font-size: 0.72rem;
        padding: 3px 8px;
        border-radius: 10px;
    }

    /* ── Approval Banner ── */
    .approval-banner {
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        font-size: 0.86rem;
    }

    .approval-banner.draft {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #475569;
    }

    .approval-banner.waiting_supervisor {
        background: #fef9c3;
        border: 1px solid #fde047;
        color: #713f12;
    }

    .approval-banner.approved_supervisor {
        background: #d1fae5;
        border: 1px solid #6ee7b7;
        color: #065f46;
    }

    /* ── Buttons ── */
    .btn-export {
        background: #1e3a8a;
        color: #dbeafe;
        border: none;
        border-radius: 10px;
        padding: 8px 20px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all .2s;
    }

    .btn-export:hover {
        background: #1d4ed8;
        color: #dbeafe;
        transform: translateY(-1px);
    }

    .btn-submit-month {
        background: linear-gradient(135deg, #059669, #047857);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 9px 22px;
        font-weight: 700;
        font-size: 0.88rem;
        transition: all .2s;
    }

    .btn-submit-month:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(5, 150, 105, .35);
        color: #fff;
    }

    .btn-submit-month:disabled {
        opacity: .5;
        transform: none;
    }

    .btn-cb-save {
        background: linear-gradient(135deg, #dc2626, #ef4444);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 10px 28px;
        font-weight: 700;
        font-size: 0.9rem;
        transition: all .2s;
    }

    .btn-cb-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(239, 68, 68, .35);
        color: #fff;
    }

    /* ── Skeleton ── */
    .skeleton-row td {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        height: 36px;
    }

    @keyframes shimmer {
        0% {
            background-position: 200% 0
        }

        100% {
            background-position: -200% 0
        }
    }

    @media(max-width:576px) {
        .rekap-header {
            padding: 20px;
        }

        .sum-val {
            font-size: 1.4rem;
        }
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- ── Header ── --}}
        <div class="rekap-header" data-aos="fade-down">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                <div>
                    <h4><i class="bx bx-zap me-2"></i>Capacitor Bank — Rekap Data</h4>
                    <p>Monitoring arus & suhu harian</p>
                </div>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <a href="{{ route('capacitor-bank.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                        <i class="bx bx-edit me-1"></i>Input Harian
                    </a>
                    <a href="{{ route('capacitor-bank.approval') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                        <i class="bx bx-check-shield me-1"></i>Approval
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Filter ── --}}
        <div class="filter-card" data-aos="fade-up" data-aos-delay="50">
            <div class="row align-items-end g-3">
                <div class="col-sm-4 col-md-3">
                    <label class="form-label fw-semibold" style="font-size:.82rem;color:#556980;">Pilih Bulan</label>
                    <input type="month" id="inputBulan" class="form-control" value="{{ date('Y-m') }}">
                </div>
                <div class="col-auto">
                    <button class="btn btn-submit-month px-4" id="btnLoad" style="background:linear-gradient(135deg,#1e3a8a,#1d4ed8);">
                        <i class="bx bx-search me-1"></i>Tampilkan
                    </button>
                </div>
                <div class="col-auto ms-auto">
                    <button id="btnExport" class="btn btn-export">
                        <i class="bx bx-file me-1"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Approval Banner ── --}}
        <div id="approvalBanner" class="approval-banner" style="display:none;" data-aos="fade-up" data-aos-delay="80"></div>

        {{-- ── Submit Bulan Button (Foreman) ── --}}
        <div id="submitArea" class="mb-3" style="display:none;" data-aos="fade-up">
            <button class="btn btn-submit-month" id="btnOpenSubmit">
                <i class="bx bx-send me-1"></i>Ajukan ke Supervisor
            </button>
            <span class="text-muted ms-3" style="font-size:.82rem;" id="submitHint"></span>
        </div>

        {{-- ── Summary Cards ── --}}
        <div class="row g-3 sum-row" data-aos="fade-up" data-aos-delay="100">
            <div class="col-6 col-md-3">
                <div class="sum-card sum-total">
                    <div class="sum-icon"><i class="bx bx-calendar"></i></div>
                    <div class="sum-val" id="sumTotal">—</div>
                    <div class="sum-lbl">Total Hari</div>
                    <div class="sum-sub">Data terekam bulan ini</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="sum-card sum-high">
                    <div class="sum-icon"><i class="bx bx-trending-up"></i></div>
                    <div class="sum-val" id="sumHigh">—</div>
                    <div class="sum-lbl">Arus Tinggi</div>
                    <div class="sum-sub">I &gt; 50A</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="sum-card sum-hot">
                    <div class="sum-icon"><i class="bx bx-thermometer"></i></div>
                    <div class="sum-val" id="sumHot">—</div>
                    <div class="sum-lbl">Suhu Panas</div>
                    <div class="sum-sub">T &gt; 60°C</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="sum-card sum-status">
                    <div class="sum-icon"><i class="bx bx-shield-check"></i></div>
                    <div class="sum-val" id="sumStatus" style="font-size:1rem;margin-top:4px;">—</div>
                    <div class="sum-lbl">Status Laporan</div>
                    <div class="sum-sub" id="sumStatusSub">—</div>
                </div>
            </div>
        </div>

        {{-- ── Table ── --}}
        <div class="table-card" data-aos="fade-up" data-aos-delay="150">
            <div class="table-card-header">
                <h6><i class="bx bx-table"></i>Detail Data Harian</h6>
                <span class="badge bg-success rounded-pill" id="badgeTgl">0 tanggal</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tblRekap">
                    <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align:middle;">Tgl</th>
                            <th rowspan="2" style="vertical-align:middle;">Jam</th>
                            <th rowspan="2" style="vertical-align:middle;">Arus Total</th>
                            <th colspan="3" class="text-center" style="background:#dbeafe;">Capacitor A</th>
                            <th colspan="3" class="text-center" style="background:#e0f2fe;">Capacitor B</th>
                            <th colspan="3" class="text-center" style="background:#fef3c7;">Capacitor C</th>
                            <th rowspan="2" style="vertical-align:middle;">Suhu</th>
                            <th rowspan="2" style="vertical-align:middle;text-align:center;">Aksi</th>
                        </tr>
                        <tr>
                            <th style="background:#dbeafe;">I1</th>
                            <th style="background:#dbeafe;">I2</th>
                            <th style="background:#dbeafe;">I3</th>
                            <th style="background:#e0f2fe;">I1</th>
                            <th style="background:#e0f2fe;">I2</th>
                            <th style="background:#e0f2fe;">I3</th>
                            <th style="background:#fef3c7;">I1</th>
                            <th style="background:#fef3c7;">I2</th>
                            <th style="background:#fef3c7;">I3</th>
                        </tr>
                    </thead>
                    <tbody id="tblBody">
                        <tr>
                            <td colspan="14" class="text-center text-muted py-4">
                                Pilih bulan lalu klik <strong>Tampilkan</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
{{-- ── Modal Submit Bulan ── --}}
<div class="modal fade" id="modalSubmit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1e3a8a,#1d4ed8);color:#dbeafe;border:none;padding:20px 24px;">
                <h5 class="modal-title"><i class="bx bx-send me-2"></i>Ajukan Laporan ke Supervisor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-3" style="font-size:.87rem;">
                    Laporan akan dikunci dan dikirim ke Supervisor. Pastikan semua data sudah benar.
                </p>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.83rem;">
                        Pilih Supervisor <span class="text-danger">*</span>
                    </label>
                    <select id="selectSupervisor" class="form-select">
                        <option value="">— Pilih Supervisor —</option>
                    </select>
                </div>
                <div class="alert alert-warning py-2 px-3" style="font-size:.82rem;">
                    <i class="bx bx-info-circle me-1"></i>
                    Setelah diajukan, data bulan ini tidak bisa diubah.
                </div>
            </div>
            <div class="modal-footer" style="padding:16px 24px;">
                <button class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-submit-month" id="btnSubmitConfirm">
                    <i class="bx bx-send me-1"></i>Ajukan ke Supervisor
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detail Capacitor Bank</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="detailBody">
                {{-- diisi via JS --}}
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUpdate" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formUpdate" class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Update Data Capacitor Bank</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="upd_tanggal">

                <!-- INFO UTAMA -->
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label>Jam</label>
                        <input type="time" id="upd_jam" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Arus Total</label>
                        <input type="number" id="upd_arus_total" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Suhu</label>
                        <input type="number" id="upd_suhu" class="form-control">
                    </div>
                </div>

                <hr>

                <!-- CAPACITOR A -->
                <h6 class="fw-bold text-primary">Capacitor A</h6>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label>I1</label>
                        <input type="number" id="upd_cap_a_i1" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>I2</label>
                        <input type="number" id="upd_cap_a_i2" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>I3</label>
                        <input type="number" id="upd_cap_a_i3" class="form-control">
                    </div>
                </div>

                <!-- CAPACITOR B -->
                <h6 class="fw-bold text-info">Capacitor B</h6>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label>I1</label>
                        <input type="number" id="upd_cap_b_i1" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>I2</label>
                        <input type="number" id="upd_cap_b_i2" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>I3</label>
                        <input type="number" id="upd_cap_b_i3" class="form-control">
                    </div>
                </div>

                <!-- CAPACITOR C -->
                <h6 class="fw-bold text-warning">Capacitor C</h6>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label>I1</label>
                        <input type="number" id="upd_cap_c_i1" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>I2</label>
                        <input type="number" id="upd_cap_c_i2" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>I3</label>
                        <input type="number" id="upd_cap_c_i3" class="form-control">
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">
                    <i class="bx bx-save me-1"></i>Update Data
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {

        const CSRF = $('meta[name="csrf-token"]').attr('content');
        const DATA = "{{ route('capacitor-bank.data') }}";
        const SUBMIT = "{{ route('capacitor-bank.submit') }}";
        const AUTH_JABATAN = "{{ auth()->user()->jabatan ?? '' }}";
        const AUTH_ID = '{{auth()-> id()}}';

        let currentBulan, currentTahun, approvalData;

        const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // ── Helper: parse tanggal string "YYYY-MM-DD" ──
        function parseTanggal(str) {
            if (!str) return null;
            const ymd = str.substring(0, 10).split('-');
            return new Date(parseInt(ymd[0]), parseInt(ymd[1]) - 1, parseInt(ymd[2]));
        }

        function formatTglLabel(str) {
            const d = parseTanggal(str);
            if (!d) return '-';
            return `${d.getDate()} ${MONTHS[d.getMonth()]} ${d.getFullYear()}`;
        }

        // ── Load Data ─────────────────────────────────────────────────
        $('#btnLoad').on('click', loadData);

        function loadData() {
            const val = $('#inputBulan').val();
            if (!val) {
                toastr.warning('Pilih bulan terlebihdahulu.');
                return;
            }

            const [y, m] = val.split('-');
            currentBulan = parseInt(m);
            currentTahun = parseInt(y);

            skeletonTable();

            $.ajax({
                url: DATA,
                data: {
                    bulan: currentBulan,
                    tahun: currentTahun
                },
                success: function(res) {
                    approvalData = res.approval;
                    buildSummary(res.data, res.approval);
                    buildTable(res.data, res.approval);
                    renderBanner(res.approval);
                    renderSubmitArea(res.data, res.approval);
                    $('#badgeTgl').text(res.data.length + ' tanggal');
                },
                error: function() {
                    toastr.error('Gagal memuat data.');
                    $('#tblBody').html('<tr><td colspan="14" class="text-center text-muted py-4">Gagal memuat data</td></tr>');
                }
            });
        }

        // ── Summary ───────────────────────────────────────────────────
        function buildSummary(rows, approval) {
            const total = rows.length;
            const high = rows.filter(r => {
                return (r.cap_a_i1 || 0) > 50 || (r.cap_a_i2 || 0) > 50 || (r.cap_a_i3 || 0) > 50 ||
                    (r.cap_b_i1 || 0) > 50 || (r.cap_b_i2 || 0) > 50 || (r.cap_b_i3 || 0) > 50 ||
                    (r.cap_c_i1 || 0) > 50 || (r.cap_c_i2 || 0) > 50 || (r.cap_c_i3 || 0) > 50;
            }).length;
            const hot = rows.filter(r => (r.suhu_ruang || 0) > 60).length;

            const statusMap = {
                'draft': {
                    icon: 'bx-edit',
                    text: 'Draft'
                },
                'waiting_supervisor': {
                    icon: 'bx-time-five',
                    text: 'Menunggu Supervisor'
                },
                'approved_supervisor': {
                    icon: 'bx-check-double',
                    text: 'Final Disetujui'
                },
            };
            const st = approval ? approval.status : 'draft';
            const stInfo = statusMap[st] || statusMap['draft'];

            $('#sumTotal').text(total);
            $('#sumHigh').text(high);
            $('#sumHot').text(hot);
            $('#sumStatus').html(`<i class="bx ${stInfo.icon} me-1"></i>${stInfo.text}`);
            $('#sumStatusSub').text(approval ? `${currentBulan}/${currentTahun}` : 'Belum ada laporan');
            $('#badgeTgl').text(total + ' tanggal');
        }

        // ── Approval Banner ───────────────────────────────────────
        function renderBanner(approval) {
            const $b = $('#approvalBanner');
            if (!approval) {
                $b.hide();
                return;
            }

            const map = {
                'draft': {
                    icon: 'bx-edit',
                    msg: 'Draft — belum diajukan ke Supervisor.'
                },
                'waiting_supervisor': {
                    icon: 'bx-time-five',
                    msg: `Menunggu persetujuan Supervisor <strong>${approval.supervisor?.username ?? '-'}</strong>.`
                },
                'approved_supervisor': {
                    icon: 'bx-check-double',
                    msg: `<strong>Final disetujui</strong> oleh Supervisor <strong>${approval.supervisor?.username ?? '-'}</strong>. Laporan terkunci.`
                },
            };
            const info = map[approval.status] || map['draft'];

            $b.attr('class', 'approval-banner ' + approval.status)
                .html(`<i class="bx ${info.icon} fs-4 flex-shrink-0"></i><span>${info.msg}</span>`)
                .show();
        }

        // ── Submit Area (Foreman) ─────────────────────────────────
        function renderSubmitArea(rows, approval) {
            const $submitArea = $('#submitArea');
            $submitArea.hide();

            if (!approval) return;

            // hanya foreman & status draft
            if (approval.status === 'draft' && AUTH_JABATAN === 'foreman') {

                const ready = rows.length > 0; // ✅ cukup ada data saja

                $('#btnOpenSubmit').prop('disabled', !ready);

                $('#submitHint').text(ready ?
                    `${rows.length} data tersedia — siap diajukan.` :
                    `Belum ada data untuk bulan ini.`
                );

                $submitArea.show();
            }
        }

        // ── Table ─────────────────────────────────────────────────────
        function buildTable(rows, approval) {
            const $body = $('#tblBody').empty();
            const canEdit = !approval || approval.status === 'draft';

            if (!rows.length) {
                $body.html('<tr><td colspan="14" class="text-center text-muted py-4">Tidak ada data bulan ini</td></tr>');
                return;
            }

            rows.forEach(function(r) {
                const tgl = r.tanggal ? (() => {
                    const d = parseTanggal(r.tanggal);
                    return `${d.getDate()} ${MONTHS[d.getMonth()]}`;
                })() : '-';

                const i1a = currentBadge(r.cap_a_i1);
                const i2a = currentBadge(r.cap_a_i2);
                const i3a = currentBadge(r.cap_a_i3);
                const i1b = currentBadge(r.cap_b_i1);
                const i2b = currentBadge(r.cap_b_i2);
                const i3b = currentBadge(r.cap_b_i3);
                const i1c = currentBadge(r.cap_c_i1);
                const i2c = currentBadge(r.cap_c_i2);
                const i3c = currentBadge(r.cap_c_i3);
                const suhuBadge = tempBadge(r.suhu_ruang);

                const updateBtn = canEdit ? `
                    <button class="btn btn-sm btn-outline-warning rounded-pill px-2 py-0 btn-update-row"
                        data-row='${JSON.stringify(r).replace(/'/g, "&#39;")}'>
                        <i class="bx bx-edit"></i>
                    </button>` : '';

                const detailBtn = `
                    <button class="btn btn-sm btn-outline-info rounded-pill px-2 py-0 btn-detail-row"
                        data-row='${JSON.stringify(r).replace(/'/g, "&#39;")}'>
                        <i class="bx bx-info-circle"></i>
                    </button>`;

                $body.append(`
                    <tr>
                        <td class="fw-semibold">${tgl}</td>
                        <td>${r.jam ?? '-'}</td>
                        <td>${r.arus_total ? r.arus_total + ' A' : '-'}</td>
                        <td>${i1a}</td><td>${i2a}</td><td>${i3a}</td>
                        <td>${i1b}</td><td>${i2b}</td><td>${i3b}</td>
                        <td>${i1c}</td><td>${i2c}</td><td>${i3c}</td>
                        <td>${suhuBadge}</td>
                        <td class="text-center" style="white-space:nowrap;">${detailBtn} ${updateBtn}</td>
                    </tr>
                `);
            });
        }

        function currentBadge(val) {
            if (val === null || val === undefined || val === '') return '<span class="badge-na">N/A</span>';
            const n = parseFloat(val);
            return n > 50 ?
                `<span class="badge-high">⚡ ${n} A</span>` :
                `<span class="badge-normal">${n} A</span>`;
        }

        function tempBadge(val) {
            if (val === null || val === undefined || val === '') return '<span class="badge-na">N/A</span>';
            const n = parseFloat(val);
            if (n > 60) {
                return `<span class="badge-hot">🔥 ${n}°C</span>`;
            } else {
                return `<span class="badge-normal">${n}°C</span>`;
            }
        }

        function skeletonTable() {
            let rows = '';
            for (let i = 0; i < 5; i++) {
                rows += '<tr class="skeleton-row">' + '<td></td>'.repeat(14) + '</tr>';
            }
            $('#tblBody').html(rows);
        }

        // ── Export PDF ────────────────────────────────────────────────
        $('#btnExport').on('click', function() {
            const val = $('#inputBulan').val();
            if (!val) {
                toastr.warning('Pilih bulan terlebih dahulu.');
                return;
            }
            window.location.href = "{{ route('capacitor-bank.export') }}?bulan=" + val;
        });

        $(document).on('click', '.btn-detail-row', function() {
            const row = $(this).data('row');

            let html = `
                <table class="table table-sm">
                    <tr><th>Tanggal</th><td>${row.tanggal}</td></tr>
                    <tr><th>Jam</th><td>${row.jam ?? '-'}</td></tr>
                    <tr><th>Arus Total</th><td>${row.arus_total ?? '-'}</td></tr>
                    <tr><th>Suhu</th><td>${row.suhu_ruang ?? '-'}</td></tr>
                </table>
            `;

            $('#detailBody').html(html);
            $('#modalDetail').modal('show');
        });

        $(document).on('click', '.btn-update-row', function() {
            const row = $(this).data('row');

            $('#upd_tanggal').val(row.tanggal);
            $('#upd_jam').val(row.jam);
            $('#upd_arus_total').val(row.arus_total);
            $('#upd_suhu').val(row.suhu_ruang);

            // CAP A
            $('#upd_cap_a_i1').val(row.cap_a_i1);
            $('#upd_cap_a_i2').val(row.cap_a_i2);
            $('#upd_cap_a_i3').val(row.cap_a_i3);

            // CAP B
            $('#upd_cap_b_i1').val(row.cap_b_i1);
            $('#upd_cap_b_i2').val(row.cap_b_i2);
            $('#upd_cap_b_i3').val(row.cap_b_i3);

            // CAP C
            $('#upd_cap_c_i1').val(row.cap_c_i1);
            $('#upd_cap_c_i2').val(row.cap_c_i2);
            $('#upd_cap_c_i3').val(row.cap_c_i3);

            $('#modalUpdate').modal('show');
        });


        $('#formUpdate').on('submit', function(e) {
            e.preventDefault();

            const tanggal = $('#upd_tanggal').val();

            $.ajax({
                url: `/utility/capacitor-bank/${tanggal}`,
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': CSRF
                },
                data: {
                    jam: $('#upd_jam').val(),
                    arus_total: $('#upd_arus_total').val(),
                    suhu_ruang: $('#upd_suhu').val(), // ✅ HARUS ini

                    // CAP A
                    cap_a_i1: $('#upd_cap_a_i1').val(),
                    cap_a_i2: $('#upd_cap_a_i2').val(),
                    cap_a_i3: $('#upd_cap_a_i3').val(),

                    // CAP B
                    cap_b_i1: $('#upd_cap_b_i1').val(),
                    cap_b_i2: $('#upd_cap_b_i2').val(),
                    cap_b_i3: $('#upd_cap_b_i3').val(),

                    // CAP C
                    cap_c_i1: $('#upd_cap_c_i1').val(),
                    cap_c_i2: $('#upd_cap_c_i2').val(),
                    cap_c_i3: $('#upd_cap_c_i3').val()
                },
                success: function() {
                    toastr.success('Data berhasil diupdate');
                    $('#modalUpdate').modal('hide');
                    loadData();
                },
                error: function() {
                    toastr.error('Gagal update data');
                }
            });
        });

        // ── Modal Submit ──────────────────────────────────────────────
        $('#btnOpenSubmit').on('click', function() {
            loadSupervisorList();
            new bootstrap.Modal($('#modalSubmit')[0]).show();
        });

        $('#btnSubmitConfirm').on('click', function() {
            const supervisorId = $('#selectSupervisor').val();
            if (!supervisorId) {
                toastr.warning('Pilih supervisor terlebih dahulu.');
                return;
            }

            const $btn = $(this).prop('disabled', true)
                .html('<i class="bx bx-loader-alt bx-spin me-1"></i>Mengirim…');

            $.ajax({
                url: SUBMIT,
                method: 'POST',
                data: {
                    _token: CSRF,
                    bulan: currentBulan,
                    tahun: currentTahun,
                    supervisor_id: supervisorId
                },
                success: function(res) {
                    toastr.success(res.message, 'Berhasil', {
                        timeOut: 4000
                    });
                    bootstrap.Modal.getInstance($('#modalSubmit')[0]).hide();
                    loadData();
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message ?? 'Gagal mengajukan.', 'Error', {
                        timeOut: 4000
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false)
                        .html('<i class="bx bx-send me-1"></i>Ajukan ke Supervisor');
                }
            });
        });

        function loadSupervisorList() {
            $.ajax({
                url: '/api/utility/users/approvers',
                method: 'GET',
                success: function(res) {
                    let opt = '<option value="">— Pilih Supervisor —</option>';
                    const list = res.user ?? res.supervisor ?? [];
                    list.forEach(function(u) {
                        opt += `<option value="${u.id}">${u.username ?? u.name}</option>`;
                    });
                    $('#selectSupervisor').html(opt);
                },
                error: function() {
                    toastr.error('Gagal memuat daftar supervisor.');
                }
            });
        }

        // ── Auto load saat halaman dimuat ────────────────────────────
        loadData();
    });
</script>
@endsection