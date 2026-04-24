@extends('layouts.app')

@section('title', 'Water Softener — Rekap Data')

@section('styles')
    <style>
        /* ── Header ── */
        .rekap-header {
            background: linear-gradient(135deg, #0d3b2e 0%, #0a4a35 60%, #115c40 100%);
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
            background: rgba(52, 211, 153, 0.07);
            border-radius: 50%;
        }

        .rekap-header h4 {
            color: #d1fae5;
            font-weight: 700;
            margin: 0;
            font-size: 1.35rem;
        }

        .rekap-header p {
            color: #6ee7b7;
            margin: 4px 0 0;
            font-size: 0.88rem;
        }

        /* ── Filter Card ── */
        .filter-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e0ede9;
            padding: 20px 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
        }

        [data-layout-mode="dark"] .filter-card {
            background: #1a2b25;
            border-color: #2a3f36;
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
            background: #eaf7f0;
            border-color: #a7e3c4;
            color: #0a5c38;
        }

        .sum-total .sum-icon {
            background: #0a5c3815;
            color: #0a5c38;
        }

        .sum-over {
            background: #fff0f0;
            border-color: #ffbdbd;
            color: #b91c1c;
        }

        .sum-over .sum-icon {
            background: #b91c1c15;
            color: #b91c1c;
        }

        .sum-regen {
            background: #fef9e7;
            border-color: #fde68a;
            color: #92400e;
        }

        .sum-regen .sum-icon {
            background: #92400e15;
            color: #92400e;
        }

        .sum-status {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1e3a8a;
        }

        .sum-status .sum-icon {
            background: #1e3a8a15;
            color: #1e3a8a;
        }

        [data-layout-mode="dark"] .sum-total {
            background: #0a2a1e;
            border-color: #174d35;
            color: #6ee7b7;
        }

        [data-layout-mode="dark"] .sum-over {
            background: #2a1212;
            border-color: #5c2020;
            color: #fca5a5;
        }

        [data-layout-mode="dark"] .sum-regen {
            background: #2a2010;
            border-color: #5c4010;
            color: #fcd34d;
        }

        [data-layout-mode="dark"] .sum-status {
            background: #101e3a;
            border-color: #1e3a6a;
            color: #93c5fd;
        }

        /* ── Table Card ── */
        .table-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e0ede9;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
            margin-bottom: 20px;
        }

        [data-layout-mode="dark"] .table-card {
            background: #1a2b25;
            border-color: #2a3f36;
        }

        .table-card-header {
            padding: 14px 20px;
            background: linear-gradient(90deg, #eaf7f0, #d4f0e3);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        [data-layout-mode="dark"] .table-card-header {
            background: linear-gradient(90deg, #0f2a1f, #0a2218);
        }

        .table-card-header h6 {
            margin: 0;
            font-weight: 700;
            color: #0a5c38;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        [data-layout-mode="dark"] .table-card-header h6 {
            color: #6ee7b7;
        }

        #tblRekap thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-weight: 700;
            background: #f0faf5;
            white-space: nowrap;
            padding: 10px 12px;
            border-bottom: 2px solid #c8ecd9;
        }

        [data-layout-mode="dark"] #tblRekap thead th {
            background: #122018;
            border-color: #1e3d2a;
        }

        #tblRekap tbody td {
            padding: 9px 12px;
            font-size: 0.84rem;
            vertical-align: middle;
        }

        #tblRekap tbody tr:hover {
            background: #f0faf540;
        }

        .badge-over {
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

        .regen-tick {
            color: #059669;
            font-weight: 700;
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

        /* ── Modals ── */
        .modal-submit .modal-content,
        .modal-edit .modal-content,
        .modal-detail .modal-content {
            border-radius: 16px;
            border: none;
            overflow: hidden;
        }

        .modal-submit .modal-header {
            background: linear-gradient(135deg, #0d3b2e, #115c40);
            color: #d1fae5;
            border: none;
            padding: 20px 24px;
        }

        .modal-edit .modal-header {
            background: linear-gradient(135deg, #1a3a5c, #0f2540);
            color: #e8f4fd;
            border: none;
            padding: 20px 24px;
        }

        .modal-detail .modal-header {
            background: linear-gradient(135deg, #1e3a2f, #2d5a45);
            color: #d1fae5;
            border: none;
            padding: 20px 24px;
        }

        .modal-submit .modal-footer,
        .modal-edit .modal-footer,
        .modal-detail .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 16px 24px;
        }

        .edit-section-title {
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: .03em;
            padding: 6px 10px;
            border-radius: 8px;
            margin-bottom: 4px;
        }

        .edit-lbl {
            font-size: 0.8rem;
            font-weight: 600;
            color: #556980;
            margin-bottom: 4px;
        }

        [data-layout-mode="dark"] .edit-lbl {
            color: #8fafc7;
        }

        /* ── Detail Modal specific ── */
        .detail-section {
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 14px;
        }

        .detail-section-title {
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: .03em;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .detail-item .d-lbl {
            font-size: 0.73rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            opacity: .6;
        }

        .detail-item .d-val {
            font-size: 0.92rem;
            font-weight: 700;
        }

        .detail-divider {
            border: none;
            border-top: 1px dashed #dee2e6;
            margin: 4px 0 12px;
        }

        /* ── Buttons ── */
        .btn-export {
            background: #0d3b2e;
            color: #d1fae5;
            border: none;
            border-radius: 10px;
            padding: 8px 20px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all .2s;
        }

        .btn-export:hover {
            background: #115c40;
            color: #d1fae5;
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

        .btn-ws-save {
            background: linear-gradient(135deg, #1a6aad, #2c8fe0);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 28px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all .2s;
        }

        .btn-ws-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 143, 224, .35);
            color: #fff;
        }

        .btn-ws-save:disabled {
            opacity: .6;
            transform: none;
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
                        <h4><i class="bx bx-bar-chart-alt-2 me-2"></i>Water Softener — Rekap Data</h4>
                        <p>Laporan bulanan hardness, flow &amp; regenerasi</p>
                    </div>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <a href="{{ route('water-softener.index') }}"
                            class="btn btn-sm btn-outline-light rounded-pill px-3">
                            <i class="bx bx-edit me-1"></i>Input Harian
                        </a>
                        <a href="{{ route('water-softener.approval') }}"
                            class="btn btn-sm btn-outline-light rounded-pill px-3">
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
                        <button class="btn btn-submit-month px-4" id="btnLoad"
                            style="background:linear-gradient(135deg,#1a6aad,#2c8fe0);">
                            <i class="bx bx-search me-1"></i>Tampilkan
                        </button>
                    </div>
                    <div class="col-auto ms-auto">
                        <button class="btn btn-export" id="btnExport">
                            <i class="bx bx-download me-1"></i>Export PDF
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── Approval Banner ── --}}
            <div id="approvalBanner" class="approval-banner" style="display:none;" data-aos="fade-up" data-aos-delay="80">
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
                    <div class="sum-card sum-over">
                        <div class="sum-icon"><i class="bx bx-error-circle"></i></div>
                        <div class="sum-val" id="sumOver">—</div>
                        <div class="sum-lbl">Over Standar</div>
                        <div class="sum-sub">Hardness Out &gt; 10 ppm</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="sum-card sum-regen">
                        <div class="sum-icon"><i class="bx bx-refresh"></i></div>
                        <div class="sum-val" id="sumRegen">—</div>
                        <div class="sum-lbl">Regenerasi</div>
                        <div class="sum-sub">Hari ada regen aktif</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="sum-card sum-status">
                        <div class="sum-icon"><i class="bx bx-shield-quarter"></i></div>
                        <div class="sum-val" id="sumStatus" style="font-size:1rem;margin-top:4px;">—</div>
                        <div class="sum-lbl">Status Laporan</div>
                        <div class="sum-sub" id="sumStatusSub">—</div>
                    </div>
                </div>
            </div>

            {{-- ── Submit Bulan Button (Foreman) ── --}}
            <div id="submitArea" class="mb-3" style="display:none;" data-aos="fade-up">
                <button class="btn btn-submit-month" id="btnOpenSubmit">
                    <i class="bx bx-send me-1"></i>Ajukan ke Supervisor
                </button>
                <span class="text-muted ms-3" style="font-size:.82rem;" id="submitHint"></span>
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
                                <th colspan="3" class="text-center" style="background:#e8f4fd;">WS 1</th>
                                <th colspan="3" class="text-center" style="background:#e6f7f5;">WS 2</th>
                                <th colspan="2" class="text-center" style="background:#fff8e6;">Regen 1</th>
                                <th colspan="2" class="text-center" style="background:#ffeef0;">Regen 2</th>
                                <th rowspan="2" style="vertical-align:middle;text-align:center;">Aksi</th>
                            </tr>
                            <tr>
                                <th style="background:#e8f4fd;">Hard-In</th>
                                <th style="background:#e8f4fd;">Hard-Out</th>
                                <th style="background:#e8f4fd;">Flow</th>
                                <th style="background:#e6f7f5;">Hard-In</th>
                                <th style="background:#e6f7f5;">Hard-Out</th>
                                <th style="background:#e6f7f5;">Flow</th>
                                <th style="background:#fff8e6;">Jam</th>
                                <th style="background:#fff8e6;">Garam</th>
                                <th style="background:#ffeef0;">Jam</th>
                                <th style="background:#ffeef0;">Garam</th>
                            </tr>
                        </thead>
                        <tbody id="tblBody">
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">
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
    <div class="modal fade modal-submit" id="modalSubmit" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-send me-2"></i>Ajukan Laporan ke Supervisor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-3" style="font-size:.87rem;">
                        Laporan akan dikunci dan dikirim ke Supervisor untuk disetujui. Pastikan semua data sudah benar.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.83rem;">
                            Pilih Supervisor <span class="text-danger">*</span>
                        </label>
                        <select id="selectSupervisor" name="supervisor" class="form-control">
                            <option value="">— Pilih Supervisor —</option>
                        </select>
                    </div>
                    <div class="alert alert-warning py-2 px-3" style="font-size:.82rem;">
                        <i class="bx bx-info-circle me-1"></i>
                        Setelah diajukan, data bulan ini tidak bisa diubah.
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-submit-month" id="btnSubmitConfirm">
                        <i class="bx bx-send me-1"></i>Ajukan ke Supervisor
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Modal Detail Row ── --}}
    <div class="modal fade modal-detail" id="modalDetailRow" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-info-circle me-2"></i>Detail Data —
                        <span id="detailTglLabel" class="ms-1"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">

                    {{-- WS 1 --}}
                    <div class="detail-section" style="background:#eef6fd;">
                        <div class="detail-section-title" style="color:#1a6aad;">
                            <i class="bx bx-water"></i> Water Softener 1
                        </div>
                        <hr class="detail-divider">
                        <div class="row g-3">
                            <div class="col-6 col-sm-3">
                                <div class="detail-item">
                                    <span class="d-lbl" style="color:#1a6aad;">Jam Operasi</span>
                                    <span class="d-val" id="dWs1Jam">—</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="detail-item">
                                    <span class="d-lbl" style="color:#1a6aad;">Flow (m³/h)</span>
                                    <span class="d-val" id="dWs1Flow">—</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="detail-item">
                                    <span class="d-lbl" style="color:#1a6aad;">Hardness In</span>
                                    <span class="d-val" id="dWs1HardIn">—</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="detail-item">
                                    <span class="d-lbl" style="color:#1a6aad;">Hardness Out</span>
                                    <span class="d-val" id="dWs1HardOut">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- WS 2 --}}
                    <div class="detail-section" style="background:#eaf7f5;">
                        <div class="detail-section-title" style="color:#0e8a7a;">
                            <i class="bx bx-water"></i> Water Softener 2
                        </div>
                        <hr class="detail-divider">
                        <div class="row g-3">
                            <div class="col-6 col-sm-3">
                                <div class="detail-item">
                                    <span class="d-lbl" style="color:#0e8a7a;">Jam Operasi</span>
                                    <span class="d-val" id="dWs2Jam">—</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="detail-item">
                                    <span class="d-lbl" style="color:#0e8a7a;">Flow (m³/h)</span>
                                    <span class="d-val" id="dWs2Flow">—</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="detail-item">
                                    <span class="d-lbl" style="color:#0e8a7a;">Hardness In</span>
                                    <span class="d-val" id="dWs2HardIn">—</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="detail-item">
                                    <span class="d-lbl" style="color:#0e8a7a;">Hardness Out</span>
                                    <span class="d-val" id="dWs2HardOut">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Regen 1 & 2 side by side --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-section h-100" style="background:#fefbe8;">
                                <div class="detail-section-title" style="color:#b07d00;">
                                    <i class="bx bx-refresh"></i> Regenerasi 1
                                </div>
                                <hr class="detail-divider">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <span class="d-lbl" style="color:#b07d00;">Jam Regen</span>
                                            <span class="d-val" id="dRegen1Jam">—</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <span class="d-lbl" style="color:#b07d00;">Nomer WS</span>
                                            <span class="d-val" id="dRegen1NomerWS">—</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <span class="d-lbl" style="color:#b07d00;">Air Pelarut</span>
                                            <span class="d-val" id="dRegen1Air">—</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <span class="d-lbl" style="color:#b07d00;">Garam</span>
                                            <span class="d-val" id="dRegen1Garam">—</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-section h-100" style="background:#fff0f2;">
                                <div class="detail-section-title" style="color:#c0384a;">
                                    <i class="bx bx-refresh"></i> Regenerasi 2
                                </div>
                                <hr class="detail-divider">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <span class="d-lbl" style="color:#c0384a;">Jam Regen</span>
                                            <span class="d-val" id="dRegen2Jam">—</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <span class="d-lbl" style="color:#c0384a;">Nomer WS</span>
                                            <span class="d-val" id="dRegen2NomerWS">—</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <span class="d-lbl" style="color:#c0384a;">Air Pelarut</span>
                                            <span class="d-val" id="dRegen2Air">—</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <span class="d-lbl" style="color:#c0384a;">Garam</span>
                                            <span class="d-val" id="dRegen2Garam">—</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Modal Edit Row ── --}}
    <div class="modal fade modal-edit" id="modalEditRow" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-edit me-2"></i>Edit Data —
                        <span id="editTglLabel" class="ms-1"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="editTanggal">

                    {{-- WS 1 --}}
                    <div class="edit-section-title" style="background:#e8f4fd;color:#1a6aad;">
                        <i class="bx bx-water me-1"></i>Water Softener 1
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-3">
                            <label class="edit-lbl">Jam Operasi</label>
                            <input type="text" class="form-control fp-time-edit" id="edit_ws1_jam"
                                placeholder="HH:MM" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="edit-lbl">Flow (m³/h)</label>
                            <input type="number" step="0.01" min="0" class="form-control"
                                id="edit_ws1_flow">
                        </div>
                        <div class="col-sm-3">
                            <label class="edit-lbl">Hardness In (ppm)</label>
                            <input type="number" step="0.01" min="0" class="form-control"
                                id="edit_ws1_hardness_in">
                        </div>
                        <div class="col-sm-3">
                            <label class="edit-lbl">Hardness Out (ppm)</label>
                            <input type="number" step="0.01" min="0" class="form-control"
                                id="edit_ws1_hardness_out">
                        </div>
                    </div>

                    {{-- WS 2 --}}
                    <div class="edit-section-title" style="background:#e6f7f5;color:#0e8a7a;">
                        <i class="bx bx-water me-1"></i>Water Softener 2
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-3">
                            <label class="edit-lbl">Jam Operasi</label>
                            <input type="text" class="form-control fp-time-edit" id="edit_ws2_jam"
                                placeholder="HH:MM" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="edit-lbl">Flow (m³/h)</label>
                            <input type="number" step="0.01" min="0" class="form-control"
                                id="edit_ws2_flow">
                        </div>
                        <div class="col-sm-3">
                            <label class="edit-lbl">Hardness In (ppm)</label>
                            <input type="number" step="0.01" min="0" class="form-control"
                                id="edit_ws2_hardness_in">
                        </div>
                        <div class="col-sm-3">
                            <label class="edit-lbl">Hardness Out (ppm)</label>
                            <input type="number" step="0.01" min="0" class="form-control"
                                id="edit_ws2_hardness_out">
                        </div>
                    </div>

                    {{-- Regen 1 --}}
                    <div class="edit-section-title" style="background:#fff8e6;color:#b07d00;">
                        <i class="bx bx-refresh me-1"></i>Regenerasi 1
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-3">
                            <label class="edit-lbl">Jam Regen</label>
                            <input type="text" class="form-control fp-time-edit" id="edit_regen1_jam"
                                placeholder="HH:MM" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="edit-lbl">Nomer WS</label>
                            <select class="form-select" id="edit_regen1_nomer_ws">
                                <option value="">— Pilih —</option>
                                <option value="1">WS 1</option>
                                <option value="2">WS 2</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="edit-lbl">Air Pelarut (L)</label>
                            <input type="number" step="0.01" min="0" class="form-control"
                                id="edit_regen1_air_pelarut">
                        </div>
                        <div class="col-sm-3">
                            <label class="edit-lbl">Garam (kg)</label>
                            <input type="number" step="0.01" min="0" class="form-control"
                                id="edit_regen1_garam">
                        </div>
                    </div>

                    {{-- Regen 2 --}}
                    <div class="edit-section-title" style="background:#ffeef0;color:#c0384a;">
                        <i class="bx bx-refresh me-1"></i>Regenerasi 2
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="edit-lbl">Jam Regen</label>
                            <input type="text" class="form-control fp-time-edit" id="edit_regen2_jam"
                                placeholder="HH:MM" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="edit-lbl">Nomer WS</label>
                            <select class="form-select" id="edit_regen2_nomer_ws">
                                <option value="">— Pilih —</option>
                                <option value="1">WS 1</option>
                                <option value="2">WS 2</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="edit-lbl">Air Pelarut (L)</label>
                            <input type="number" step="0.01" min="0" class="form-control"
                                id="edit_regen2_air_pelarut">
                        </div>
                        <div class="col-sm-3">
                            <label class="edit-lbl">Garam (kg)</label>
                            <input type="number" step="0.01" min="0" class="form-control"
                                id="edit_regen2_garam">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-ws-save" id="btnSaveEdit">
                        <i class="bx bx-save me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(function() {

            const CSRF = $('meta[name="csrf-token"]').attr('content');
            const SUBMIT = "{{ route('water-softener.submit') }}";
            const DATA = "{{ route('water-softener.data') }}";
            const UPDATE = "{{ route('water-softener.update', '__TGL__') }}";

            // Role & ID dari session auth
            const AUTH_JABATAN = "{{ auth()->user()->jabatan ?? '' }}";
            const AUTH_ID = {{ auth()->id() }};

            let currentBulan, currentTahun, approvalData;

            const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            const statusLabel = {
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

            // ── Helper: parse tanggal string "YYYY-MM-DD" tanpa timezone shift ──
            function parseTanggal(str) {
                if (!str) return null;
                // Ambil 10 karakter pertama "YYYY-MM-DD", split manual
                const ymd = str.substring(0, 10).split('-');
                return new Date(parseInt(ymd[0]), parseInt(ymd[1]) - 1, parseInt(ymd[2]));
            }

            function formatTglLabel(str) {
                const d = parseTanggal(str);
                if (!d) return '-';
                return `${d.getDate()} ${MONTHS[d.getMonth()]} ${d.getFullYear()}`;
            }

            // ── Flatpickr untuk modal edit ────────────────────────────────
            const editTimeIds = ['edit_ws1_jam', 'edit_ws2_jam', 'edit_regen1_jam', 'edit_regen2_jam'];
            const fpEdit = {};
            editTimeIds.forEach(function(id) {
                fpEdit[id] = flatpickr('#' + id, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: 'H:i',
                    time_24hr: true,
                    minuteIncrement: 1,
                    allowInput: false,
                });
            });

            // ── Load Data ─────────────────────────────────────────────────
            $('#btnLoad').on('click', loadData);
            loadData();

            function loadData() {
                const val = $('#inputBulan').val();
                if (!val) {
                    toastr.warning('Pilih bulan terlebih dahulu.');
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
                        console.log(res);
                        approvalData = res.approval;
                        buildSummary(res.data, res.approval);
                        buildTable(res.data, res.approval);
                        renderBanner(res.approval);
                        renderSubmitArea(res.data, res.approval);
                    },
                    error: function() {
                        toastr.error('Gagal memuat data.');
                        $('#tblBody').html(
                            '<tr><td colspan="12" class="text-center text-muted py-4">Gagal memuat data</td></tr>'
                            );
                    }
                });
            }

            // ── Summary ───────────────────────────────────────────────────
            function buildSummary(rows, approval) {
                const total = rows.length;
                const over = rows.filter(r => parseFloat(r.ws1_hardness_out) > 10 || parseFloat(r
                    .ws2_hardness_out) > 10).length;
                const regen = rows.filter(r => r.regen1_jam || r.regen2_jam).length;
                const st = approval ? approval.status : 'draft';
                const stLbl = statusLabel[st] || statusLabel['draft'];

                $('#sumTotal').text(total);
                $('#sumOver').text(over);
                $('#sumRegen').text(regen);
                $('#sumStatus').html(`<i class="bx ${stLbl.icon} me-1"></i>${stLbl.text}`);
                $('#sumStatusSub').text(approval ? `${currentBulan}/${currentTahun}` : 'Belum ada laporan');
                $('#badgeTgl').text(total + ' tanggal');
            }

            // ── Approval Banner ───────────────────────────────────────────
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

            // ── Submit Area (Foreman ajukan ke Supervisor) ────────────────
            function renderSubmitArea(rows, approval) {
                const $submitArea = $('#submitArea');

                $submitArea.hide();

                if (!approval) return;

                // Tombol "Ajukan ke Supervisor" hanya untuk FOREMAN saat status draft
                // Syarat: minimal ada 1 data yang sudah diinput di bulan ini
                if (approval.status === 'draft' && AUTH_JABATAN === 'foreman') {
                    const hasData = rows.length > 0;

                    $('#btnOpenSubmit').prop('disabled', !hasData);
                    $('#submitHint').text(hasData ?
                        `${rows.length} data terinput — siap diajukan ke Supervisor.` :
                        'Belum ada data untuk bulan ini.'
                    );
                    $submitArea.show();
                }
            }

            // ── Table ─────────────────────────────────────────────────────
            function buildTable(rows, approval) {
                const $body = $('#tblBody').empty();

                // Edit hanya saat status draft. Semua role boleh edit (cek di server).
                const canEdit = !approval || approval.status === 'draft';

                if (!rows.length) {
                    $body.html(
                        '<tr><td colspan="12" class="text-center text-muted py-4">Tidak ada data bulan ini</td></tr>'
                        );
                    return;
                }

                rows.forEach(function(r) {
                    // ── Tanggal: parse manual hindari timezone shift ──
                    const tgl = r.tanggal ? (() => {
                        const d = parseTanggal(r.tanggal);
                        return `${d.getDate()} ${MONTHS[d.getMonth()]}`;
                    })() : '-';

                    const ho1 = hardnessBadge(r.ws1_hardness_out);
                    const ho2 = hardnessBadge(r.ws2_hardness_out);
                    const rg1 = r.regen1_jam ? `<span class="regen-tick">✓</span> ${r.regen1_jam}` : '-';
                    const rg2 = r.regen2_jam ? `<span class="regen-tick">✓</span> ${r.regen2_jam}` : '-';

                    // Tombol Detail — semua role bisa lihat
                    const detailBtn = `
                    <button class="btn btn-sm btn-outline-info rounded-pill px-2 py-0 btn-detail-row"
                        title="Lihat detail"
                        data-row='${JSON.stringify(r).replace(/'/g, "&#39;")}'>
                        <i class="bx bx-info-circle"></i>
                    </button>`;

                    // Tombol Edit — hanya muncul jika status draft
                    const editBtn = canEdit ?
                        `<button class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 btn-edit-row ms-1"
                            title="Edit data ini"
                            data-row='${JSON.stringify(r).replace(/'/g, "&#39;")}'>
                            <i class="bx bx-edit-alt"></i>
                       </button>` :
                        '';

                    $body.append(`
                    <tr>
                        <td class="fw-semibold">${tgl}</td>
                        <td>${r.ws1_hardness_in  ?? '-'}</td>
                        <td>${ho1}</td>
                        <td>${r.ws1_flow         ?? '-'}</td>
                        <td>${r.ws2_hardness_in  ?? '-'}</td>
                        <td>${ho2}</td>
                        <td>${r.ws2_flow         ?? '-'}</td>
                        <td>${rg1}</td>
                        <td>${r.regen1_garam ? r.regen1_garam + ' kg' : '-'}</td>
                        <td>${rg2}</td>
                        <td>${r.regen2_garam ? r.regen2_garam + ' kg' : '-'}</td>
                        <td class="text-center" style="white-space:nowrap;">${detailBtn}${editBtn}</td>
                    </tr>
                `);
                });
            }

            function hardnessBadge(val) {
                if (val === null || val === undefined || val === '') return '<span class="badge-na">N/A</span>';
                const n = parseFloat(val);
                return n > 10 ?
                    `<span class="badge-over">⚠ ${n} ppm</span>` :
                    `<span class="badge-normal">${n} ppm</span>`;
            }

            function skeletonTable() {
                let rows = '';
                for (let i = 0; i < 5; i++) {
                    rows += '<tr class="skeleton-row">' + '<td></td>'.repeat(12) + '</tr>';
                }
                $('#tblBody').html(rows);
            }

            // ── Klik tombol Detail ────────────────────────────────────────
            $(document).on('click', '.btn-detail-row', function() {
                const r = $(this).data('row');

                // Header label tanggal
                $('#detailTglLabel').text(formatTglLabel(r.tanggal));

                // WS 1
                $('#dWs1Jam').text(r.ws1_jam ?? '—');
                $('#dWs1Flow').text(r.ws1_flow ? r.ws1_flow + ' m³/h' : '—');
                $('#dWs1HardIn').text(r.ws1_hardness_in ? r.ws1_hardness_in + ' ppm' : '—');

                const ho1 = r.ws1_hardness_out !== null && r.ws1_hardness_out !== undefined ?
                    parseFloat(r.ws1_hardness_out) :
                    null;
                $('#dWs1HardOut').html(ho1 !== null ?
                    (ho1 > 10 ?
                        `<span class="badge-over">⚠ ${ho1} ppm</span>` :
                        `<span class="badge-normal">${ho1} ppm</span>`) :
                    '—');

                // WS 2
                $('#dWs2Jam').text(r.ws2_jam ?? '—');
                $('#dWs2Flow').text(r.ws2_flow ? r.ws2_flow + ' m³/h' : '—');
                $('#dWs2HardIn').text(r.ws2_hardness_in ? r.ws2_hardness_in + ' ppm' : '—');

                const ho2 = r.ws2_hardness_out !== null && r.ws2_hardness_out !== undefined ?
                    parseFloat(r.ws2_hardness_out) :
                    null;
                $('#dWs2HardOut').html(ho2 !== null ?
                    (ho2 > 10 ?
                        `<span class="badge-over">⚠ ${ho2} ppm</span>` :
                        `<span class="badge-normal">${ho2} ppm</span>`) :
                    '—');

                // Regen 1
                $('#dRegen1Jam').text(r.regen1_jam ?? '—');
                $('#dRegen1NomerWS').text(r.regen1_nomer_ws ? 'WS ' + r.regen1_nomer_ws : '—');
                $('#dRegen1Air').text(r.regen1_air_pelarut ? r.regen1_air_pelarut + ' L' : '—');
                $('#dRegen1Garam').text(r.regen1_garam ? r.regen1_garam + ' kg' : '—');

                // Regen 2
                $('#dRegen2Jam').text(r.regen2_jam ?? '—');
                $('#dRegen2NomerWS').text(r.regen2_nomer_ws ? 'WS ' + r.regen2_nomer_ws : '—');
                $('#dRegen2Air').text(r.regen2_air_pelarut ? r.regen2_air_pelarut + ' L' : '—');
                $('#dRegen2Garam').text(r.regen2_garam ? r.regen2_garam + ' kg' : '—');

                new bootstrap.Modal($('#modalDetailRow')[0]).show();
            });

            // ── Klik tombol Edit di baris ─────────────────────────────────
            $(document).on('click', '.btn-edit-row', function() {
                const r = $(this).data('row');
                const tgl = r.tanggal ? r.tanggal.substring(0, 10) : '';

                $('#editTanggal').val(tgl);
                $('#editTglLabel').text(formatTglLabel(r.tanggal));

                // Isi field non-jam
                const nonTime = [
                    'ws1_hardness_in', 'ws1_hardness_out', 'ws1_flow',
                    'ws2_hardness_in', 'ws2_hardness_out', 'ws2_flow',
                    'regen1_air_pelarut', 'regen1_garam', 'regen1_nomer_ws',
                    'regen2_air_pelarut', 'regen2_garam', 'regen2_nomer_ws',
                ];
                nonTime.forEach(function(f) {
                    $('#edit_' + f).val(r[f] ?? '');
                });

                // Isi field jam via flatpickr
                editTimeIds.forEach(function(id) {
                    const key = id.replace('edit_', '');
                    r[key] ? fpEdit[id].setDate(r[key], false) : fpEdit[id].clear();
                });

                new bootstrap.Modal($('#modalEditRow')[0]).show();
            });

            // ── Simpan Edit ───────────────────────────────────────────────
            $('#btnSaveEdit').on('click', function() {
                const tgl = $('#editTanggal').val();
                const $btn = $(this).prop('disabled', true).html(
                    '<i class="bx bx-loader-alt bx-spin me-1"></i>Menyimpan…');

                $.ajax({
                    url: UPDATE.replace('__TGL__', tgl),
                    method: 'PUT',
                    data: {
                        _token: CSRF,
                        ws1_jam: $('#edit_ws1_jam').val() || null,
                        ws1_hardness_in: $('#edit_ws1_hardness_in').val() || null,
                        ws1_hardness_out: $('#edit_ws1_hardness_out').val() || null,
                        ws1_flow: $('#edit_ws1_flow').val() || null,
                        ws2_jam: $('#edit_ws2_jam').val() || null,
                        ws2_hardness_in: $('#edit_ws2_hardness_in').val() || null,
                        ws2_hardness_out: $('#edit_ws2_hardness_out').val() || null,
                        ws2_flow: $('#edit_ws2_flow').val() || null,
                        regen1_jam: $('#edit_regen1_jam').val() || null,
                        regen1_air_pelarut: $('#edit_regen1_air_pelarut').val() || null,
                        regen1_garam: $('#edit_regen1_garam').val() || null,
                        regen1_nomer_ws: $('#edit_regen1_nomer_ws').val() || null,
                        regen2_jam: $('#edit_regen2_jam').val() || null,
                        regen2_air_pelarut: $('#edit_regen2_air_pelarut').val() || null,
                        regen2_garam: $('#edit_regen2_garam').val() || null,
                        regen2_nomer_ws: $('#edit_regen2_nomer_ws').val() || null,
                    },
                    success: function(res) {
                        toastr.success(res.message, 'Berhasil', {
                            timeOut: 3500
                        });
                        bootstrap.Modal.getInstance($('#modalEditRow')[0]).hide();
                        loadData();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message ?? 'Gagal menyimpan.', 'Error', {
                            timeOut: 4000
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(
                            '<i class="bx bx-save me-1"></i>Simpan Perubahan');
                    }
                });
            });

            // ── Modal Submit Bulan ────────────────────────────────────────
            $('#btnOpenSubmit').on('click', function() {
                new bootstrap.Modal($('#modalSubmit')[0]).show();
                loadSupervisorList();
            });

            $('#btnSubmitConfirm').on('click', function() {
                const supervisorId = $('#selectSupervisor').val();
                if (!supervisorId) {
                    toastr.warning('Pilih supervisor terlebih dahulu.');
                    return;
                }

                const $btn = $(this).prop('disabled', true).html(
                    '<i class="bx bx-loader-alt bx-spin me-1"></i>Mengirim…');

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
                        toastr.error(xhr.responseJSON?.message ?? 'Gagal mengajukan.',
                        'Error', {
                            timeOut: 4000
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(
                            '<i class="bx bx-send me-1"></i>Ajukan ke Supervisor');
                    }
                });
            });

            // ── Load Supervisor list untuk modal submit ───────────────────
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


            $('#btnExport').on('click', function() {
                const el = document.querySelector('.table-card');
                const bulanStr = $('#inputBulan').val().replace('-', '/');
                html2pdf().set({
                    margin: 8,
                    filename: `WaterSoftener_${bulanStr}.pdf`,
                    image: {
                        type: 'jpeg',
                        quality: 0.98
                    },
                    html2canvas: {
                        scale: 2
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'landscape'
                    }
                }).from(el).save();
            });

        });
    </script>
@endsection
