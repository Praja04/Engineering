@extends('layouts.app')

@section('title', 'Water Softener — Input Harian')

@section('styles')
<style>
    /* ── Page Header ── */
    .ws-header {
        background: linear-gradient(135deg, #1a3a5c 0%, #0f2540 60%, #162d4a 100%);
        border-radius: 16px;
        padding: 28px 32px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    .ws-header::before {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 180px;
        height: 180px;
        background: rgba(79, 172, 254, 0.08);
        border-radius: 50%;
    }

    .ws-header::after {
        content: '';
        position: absolute;
        bottom: -60px;
        left: 30%;
        width: 260px;
        height: 260px;
        background: rgba(79, 172, 254, 0.05);
        border-radius: 50%;
    }

    .ws-header h4 {
        color: #e8f4fd;
        font-weight: 700;
        margin: 0;
        font-size: 1.35rem;
    }

    .ws-header p {
        color: #7eb8e8;
        margin: 4px 0 0;
        font-size: 0.88rem;
    }

    .ws-header .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(79, 172, 254, 0.15);
        border: 1px solid rgba(79, 172, 254, 0.3);
        color: #7eb8e8;
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 0.78rem;
    }

    /* ── Date Picker Card ── */
    .date-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e4ecf3;
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
    }

    [data-layout-mode="dark"] .date-card {
        background: #1e2a38;
        border-color: #2d3f52;
    }

    /* ── Section Cards ── */
    .ws-section {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #e4ecf3;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
    }

    [data-layout-mode="dark"] .ws-section {
        border-color: #2d3f52;
    }

    .ws-section-header {
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        letter-spacing: .03em;
    }

    .ws-section-header .icon-wrap {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .ws-section-body {
        background: #fff;
        padding: 20px 24px;
    }

    [data-layout-mode="dark"] .ws-section-body {
        background: #1e2a38;
    }

    /* WS 1 — blue */
    .hdr-ws1 {
        background: linear-gradient(90deg, #e8f4fd, #dbeeff);
        color: #1a6aad;
    }

    .hdr-ws1 .icon-wrap {
        background: #1a6aad22;
        color: #1a6aad;
    }

    /* WS 2 — teal */
    .hdr-ws2 {
        background: linear-gradient(90deg, #e6f7f5, #d4f3ef);
        color: #0e8a7a;
    }

    .hdr-ws2 .icon-wrap {
        background: #0e8a7a22;
        color: #0e8a7a;
    }

    /* Regen 1 — amber */
    .hdr-rg1 {
        background: linear-gradient(90deg, #fff8e6, #fff0cc);
        color: #b07d00;
    }

    .hdr-rg1 .icon-wrap {
        background: #b07d0022;
        color: #b07d00;
    }

    /* Regen 2 — rose */
    .hdr-rg2 {
        background: linear-gradient(90deg, #ffeef0, #ffd9de);
        color: #c0384a;
    }

    .hdr-rg2 .icon-wrap {
        background: #c0384a22;
        color: #c0384a;
    }

    [data-layout-mode="dark"] .hdr-ws1 {
        background: linear-gradient(90deg, #13273b, #0f2236);
        color: #7eb8e8;
    }

    [data-layout-mode="dark"] .hdr-ws2 {
        background: linear-gradient(90deg, #0e2a28, #0b2320);
        color: #5fccc0;
    }

    [data-layout-mode="dark"] .hdr-rg1 {
        background: linear-gradient(90deg, #2a2210, #231c0a);
        color: #ffc947;
    }

    [data-layout-mode="dark"] .hdr-rg2 {
        background: linear-gradient(90deg, #2a1218, #220d12);
        color: #ff8096;
    }

    /* ── Form Controls ── */
    .form-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #556980;
        margin-bottom: 5px;
    }

    [data-layout-mode="dark"] .form-label {
        color: #8fafc7;
    }

    .unit-badge {
        background: #f0f6fc;
        border: 1px solid #cfe2f7;
        color: #556980;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0 12px;
        border-radius: 0 6px 6px 0;
        white-space: nowrap;
    }

    [data-layout-mode="dark"] .unit-badge {
        background: #253344;
        border-color: #2d4560;
        color: #7a9bb5;
    }

    .hardness-over {
        border-color: #dc3545 !important;
    }

    .hardness-warn {
        font-size: 0.75rem;
        color: #dc3545;
        margin-top: 3px;
        display: none;
    }

    /* ── Status Bar ── */
    #statusBar {
        border-radius: 10px;
        padding: 12px 18px;
        font-size: 0.85rem;
        font-weight: 600;
        display: none;
        margin-bottom: 16px;
        align-items: center;
        gap: 10px;
    }

    #statusBar.show {
        display: flex;
    }

    /* ── Submit area ── */
    .ws-submit-row {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e4ecf3;
        padding: 20px 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
    }

    [data-layout-mode="dark"] .ws-submit-row {
        background: #1e2a38;
        border-color: #2d3f52;
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

    .btn-ws-save:active {
        transform: none;
    }

    .btn-ws-save:disabled {
        opacity: .6;
        transform: none;
    }

    /* ── Blocked overlay ── */
    .ws-blocked-notice {
        display: none;
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 10px;
        padding: 14px 18px;
        color: #856404;
        font-size: 0.87rem;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }

    .ws-blocked-notice.show {
        display: flex;
    }

    /* ── Table Preview ── */
    #tblPreview thead th {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 700;
        background: #f7fafd;
        white-space: nowrap;
    }

    [data-layout-mode="dark"] #tblPreview thead th {
        background: #1a2b3c;
    }

    /* ── Flatpickr time input ── */
    .flatpickr-input {
        background-color: inherit !important;
        cursor: pointer;
    }

    .flatpickr-input[readonly] {
        cursor: pointer;
    }

    /* Pastikan numInput di flatpickr time tidak ada AM/PM */
    .flatpickr-time .flatpickr-am-pm {
        display: none !important;
    }

    .flatpickr-time input.flatpickr-hour,
    .flatpickr-time input.flatpickr-minute {
        font-size: 1.1rem;
    }

    /* Responsive tweak */
    @media(max-width: 576px) {
        .ws-section-body {
            padding: 16px;
        }

        .ws-header {
            padding: 20px 20px;
        }
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- ── Page Header ── --}}
        <div class="ws-header" data-aos="fade-down">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                <div>
                    <h4><i class="bx bx-droplet me-2"></i>Water Softener — Input Harian</h4>
                    <p>Engineering Utility · Catat data WS dan Regenerasi harian</p>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge-status"><i class="bx bx-calendar-check"></i> Monitoring Harian</span>
                    <a href="{{ route('water-softener.rekap') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                        <i class="bx bx-bar-chart-alt-2 me-1"></i>Rekap
                    </a>
                </div>
            </div>
        </div>




        {{-- ── Blocked Notice ── --}}
        <div class="ws-blocked-notice" id="blockedNotice">
            <i class="bx bx-lock-alt fs-4"></i>
            <span>Laporan bulan ini sudah <strong>disetujui Supervisor</strong>. Data tidak dapat diubah.</span>
        </div>

        {{-- ── Main Form ── --}}
        <form id="formWS" novalidate>
            @csrf
            <div class="row">
                <div class="col-sm-6" style="margin-bottom: 10px;">
                    <label class="form-label">
                        <i class="bx bx-calendar me-1"></i>
                        Tanggal Input <span class="text-danger">*</span>
                    </label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="row g-4">

                {{-- ── LEFT COLUMN ── --}}
                <div class="col-lg-6">

                    {{-- WS 1 --}}
                    <div class="ws-section">
                        <div class="ws-section-header hdr-ws1">
                            <div class="icon-wrap"><i class="bx bx-water"></i></div>
                            Water Softener 1
                        </div>

                        <div class="ws-section-body">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label">Jam Operasi</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control fp-time" id="ws1_jam" name="ws1_jam" placeholder="HH:MM" autocomplete="off" readonly>
                                        <span class="unit-badge"><i class="bx bx-time-five"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Flow <span class="text-muted fw-normal">(m³/h)</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" class="form-control" id="ws1_flow" name="ws1_flow" placeholder="0.00">
                                        <span class="unit-badge">m³/h</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Hardness In <span class="text-muted fw-normal">(ppm)</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" class="form-control" id="ws1_hardness_in" name="ws1_hardness_in" placeholder="0.00">
                                        <span class="unit-badge">ppm</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Hardness Out <span class="text-muted fw-normal">(ppm)</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" class="form-control hardness-out" id="ws1_hardness_out" name="ws1_hardness_out" placeholder="0.00">
                                        <span class="unit-badge">ppm</span>
                                    </div>
                                    <div class="hardness-warn" id="warn_ws1"><i class="bx bx-error-circle me-1"></i>Melebihi standar (max 10 ppm)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Regen 1 --}}
                    <div class="ws-section">
                        <div class="ws-section-header hdr-rg1">
                            <div class="icon-wrap"><i class="bx bx-refresh"></i></div>
                            Regenerasi 1
                        </div>
                        <div class="ws-section-body">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label">Jam Regenerasi</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control fp-time" id="regen1_jam" name="regen1_jam" placeholder="HH:MM" autocomplete="off" readonly>
                                        <span class="unit-badge"><i class="bx bx-time-five"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Nomer WS</label>
                                    <select class="form-select" id="regen1_nomer_ws" name="regen1_nomer_ws">
                                        <option value="">— Pilih —</option>
                                        <option value="1">WS 1</option>
                                        <option value="2">WS 2</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Air Pelarut <span class="text-muted fw-normal">(L)</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" class="form-control" id="regen1_air_pelarut" name="regen1_air_pelarut" placeholder="0.00">
                                        <span class="unit-badge">L</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Garam <span class="text-muted fw-normal">(kg)</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" class="form-control" id="regen1_garam" name="regen1_garam" placeholder="0.00">
                                        <span class="unit-badge">kg</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- end LEFT --}}

                {{-- ── RIGHT COLUMN ── --}}
                <div class="col-lg-6">

                    {{-- WS 2 --}}
                    <div class="ws-section">
                        <div class="ws-section-header hdr-ws2">
                            <div class="icon-wrap"><i class="bx bx-water"></i></div>
                            Water Softener 2
                        </div>
                        <div class="ws-section-body">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label">Jam Operasi</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control fp-time" id="ws2_jam" name="ws2_jam" placeholder="HH:MM" autocomplete="off" readonly>
                                        <span class="unit-badge"><i class="bx bx-time-five"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Flow <span class="text-muted fw-normal">(m³/h)</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" class="form-control" id="ws2_flow" name="ws2_flow" placeholder="0.00">
                                        <span class="unit-badge">m³/h</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Hardness In <span class="text-muted fw-normal">(ppm)</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" class="form-control" id="ws2_hardness_in" name="ws2_hardness_in" placeholder="0.00">
                                        <span class="unit-badge">ppm</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Hardness Out <span class="text-muted fw-normal">(ppm)</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" class="form-control hardness-out" id="ws2_hardness_out" name="ws2_hardness_out" placeholder="0.00">
                                        <span class="unit-badge">ppm</span>
                                    </div>
                                    <div class="hardness-warn" id="warn_ws2"><i class="bx bx-error-circle me-1"></i>Melebihi standar (max 10 ppm)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Regen 2 --}}
                    <div class="ws-section">
                        <div class="ws-section-header hdr-rg2">
                            <div class="icon-wrap"><i class="bx bx-refresh"></i></div>
                            Regenerasi 2
                        </div>
                        <div class="ws-section-body">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label">Jam Regenerasi</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control fp-time" id="regen2_jam" name="regen2_jam" placeholder="HH:MM" autocomplete="off" readonly>
                                        <span class="unit-badge"><i class="bx bx-time-five"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Nomer WS</label>
                                    <select class="form-select" id="regen2_nomer_ws" name="regen2_nomer_ws">
                                        <option value="">— Pilih —</option>
                                        <option value="1">WS 1</option>
                                        <option value="2">WS 2</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Air Pelarut <span class="text-muted fw-normal">(L)</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" class="form-control" id="regen2_air_pelarut" name="regen2_air_pelarut" placeholder="0.00">
                                        <span class="unit-badge">L</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Garam <span class="text-muted fw-normal">(kg)</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" class="form-control" id="regen2_garam" name="regen2_garam" placeholder="0.00">
                                        <span class="unit-badge">kg</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- end RIGHT --}}

            </div>{{-- end row --}}

            {{-- ── Submit Row ── --}}
            <div class="ws-submit-row d-flex align-items-center justify-content-between flex-wrap gap-3" data-aos="fade-up" data-aos-delay="200">
                <div>
                    <p class="mb-0 text-muted" style="font-size:.83rem;">
                        <i class="bx bx-info-circle me-1"></i>
                        Semua field boleh kosong kecuali tanggal. Hardness Out standar maks <strong>10 ppm</strong>.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" id="btnReset">
                        <i class="bx bx-eraser me-1"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-ws-save" id="btnSave">
                        <i class="bx bx-save me-1"></i>Simpan Data
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {

        const CSRF = $('meta[name="csrf-token"]').attr('content');
        const STORE = "{{ route('water-softener.store') }}";
        const DATA = "{{ route('water-softener.data') }}";
        let isBlocked = false;

        // ── Inisialisasi Flatpickr 24-jam untuk semua .fp-time ────
        const timeIds = ['ws1_jam', 'ws2_jam', 'regen1_jam', 'regen2_jam'];
        const fpInstances = {};

        timeIds.forEach(function(id) {
            fpInstances[id] = flatpickr('#' + id, {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i', // keluarkan format 24-jam HH:MM
                time_24hr: true, // paksa 24 jam, hilangkan AM/PM
                minuteIncrement: 1,
                allowInput: false,
            });
        });

        // ── Hardness Out live warning ──────────────────────────────
        function checkHardness(inputId, warnId) {
            $('#' + inputId).on('input', function() {
                const val = parseFloat($(this).val());
                const over = !isNaN(val) && val > 10;
                $(this).toggleClass('hardness-over', over);
                $('#' + warnId).toggle(over);
            });
        }
        checkHardness('ws1_hardness_out', 'warn_ws1');
        checkHardness('ws2_hardness_out', 'warn_ws2');


        // ── Submit Form ───────────────────────────────────────────
        $('#formWS').on('submit', function(e) {
            e.preventDefault();
            // if (isBlocked) return;

            const tgl = $('#tanggal').val();
            if (!tgl) {
                toastr.warning('Muat data tanggal terlebih dahulu.');
                return;
            }

            const $btn = $('#btnSave').prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i>Menyimpan…');

            $.ajax({
                url: STORE,
                method: 'POST',
                data: {
                    _token: CSRF,
                    tanggal: tgl,
                    ws1_jam: $('#ws1_jam').val() || null,
                    ws1_hardness_in: $('#ws1_hardness_in').val() || null,
                    ws1_hardness_out: $('#ws1_hardness_out').val() || null,
                    ws1_flow: $('#ws1_flow').val() || null,
                    ws2_jam: $('#ws2_jam').val() || null,
                    ws2_hardness_in: $('#ws2_hardness_in').val() || null,
                    ws2_hardness_out: $('#ws2_hardness_out').val() || null,
                    ws2_flow: $('#ws2_flow').val() || null,
                    regen1_jam: $('#regen1_jam').val() || null,
                    regen1_air_pelarut: $('#regen1_air_pelarut').val() || null,
                    regen1_garam: $('#regen1_garam').val() || null,
                    regen1_nomer_ws: $('#regen1_nomer_ws').val() || null,
                    regen2_jam: $('#regen2_jam').val() || null,
                    regen2_air_pelarut: $('#regen2_air_pelarut').val() || null,
                    regen2_garam: $('#regen2_garam').val() || null,
                    regen2_nomer_ws: $('#regen2_nomer_ws').val() || null,
                },
                success: function(res) {
                    toastr.success(res.message, 'Berhasil', {
                        timeOut: 3500
                    });
                    loadData();
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Terjadi kesalahan.';
                    toastr.error(msg, 'Gagal', {
                        timeOut: 4000
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i>Simpan Data');
                }
            });
        });

        // ── Reset ─────────────────────────────────────────────────
        $('#btnReset').on('click', function() {
            resetForm();
            toastr.info('Form dikosongkan.');
        });

        // ── Helpers ───────────────────────────────────────────────

        /**
         * Isi form dari row data DB.
         * Untuk field jam (fp-time): gunakan flatpickr.setDate() agar
         * widget sinkron, bukan .val() langsung.
         */
        function fillForm(row) {
            // Field non-jam
            const nonTimeFields = [
                'ws1_hardness_in', 'ws1_hardness_out', 'ws1_flow',
                'ws2_hardness_in', 'ws2_hardness_out', 'ws2_flow',
                'regen1_air_pelarut', 'regen1_garam', 'regen1_nomer_ws',
                'regen2_air_pelarut', 'regen2_garam', 'regen2_nomer_ws',
            ];
            nonTimeFields.forEach(f => $('#' + f).val(row[f] ?? ''));

            // Field jam — set via flatpickr instance agar 24-jam tampil benar
            timeIds.forEach(function(id) {
                const val = row[id] ?? null;
                if (val) {
                    fpInstances[id].setDate(val, false); // false = tidak trigger onChange
                } else {
                    fpInstances[id].clear();
                }
            });

            // Trigger hardness warnings
            $('#ws1_hardness_out, #ws2_hardness_out').trigger('input');
        }

        function resetForm() {
            const nonTimeFields = [
                'ws1_hardness_in', 'ws1_hardness_out', 'ws1_flow',
                'ws2_hardness_in', 'ws2_hardness_out', 'ws2_flow',
                'regen1_air_pelarut', 'regen1_garam', 'regen1_nomer_ws',
                'regen2_air_pelarut', 'regen2_garam', 'regen2_nomer_ws',
            ];
            nonTimeFields.forEach(f => $('#' + f).val(''));

            // Kosongkan flatpickr
            timeIds.forEach(id => fpInstances[id].clear());

            $('.hardness-out').removeClass('hardness-over');
            $('.hardness-warn').hide();
        }

        function setStatus(type, html) {
            const map = {
                loading: 'alert-secondary',
                success: 'alert-success',
                info: 'alert-info',
                danger: 'alert-danger',
            };
            $('#statusBar')
                .removeClass('alert-secondary alert-success alert-info alert-danger')
                .addClass(map[type] || 'alert-info')
                .html(html)
                .addClass('show');
        }

        function formatTgl(iso) {
            const [y, m, d] = iso.split('-');
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${d} ${months[parseInt(m)-1]} ${y}`;
        }

        function buildTable(rows) {
            const $body = $('#tblBody').empty();
            $('#badgeJumlah').text(rows.length + ' hari');
            if (!rows.length) {
                $body.html('<tr><td colspan="11" class="text-center text-muted py-4">Belum ada data bulan ini</td></tr>');
                return;
            }
            rows.forEach(r => {
                const ho1class = r.ws1_hardness_out > 10 ? 'text-danger fw-bold' : '';
                const ho2class = r.ws2_hardness_out > 10 ? 'text-danger fw-bold' : '';
                const tgl = r.tanggal ? formatTgl(r.tanggal.substring(0, 10)) : '-';
                $body.append(`
                <tr>
                    <td>${tgl}</td>
                    <td>${r.ws1_jam   ?? '-'}</td>
                    <td>${r.ws1_hardness_in  ?? '-'}</td>
                    <td class="${ho1class}">${r.ws1_hardness_out ?? '-'}</td>
                    <td>${r.ws1_flow  ?? '-'}</td>
                    <td>${r.ws2_jam   ?? '-'}</td>
                    <td>${r.ws2_hardness_in  ?? '-'}</td>
                    <td class="${ho2class}">${r.ws2_hardness_out ?? '-'}</td>
                    <td>${r.ws2_flow  ?? '-'}</td>
                    <td>${r.regen1_jam ? '✓ '+r.regen1_jam : '-'}</td>
                    <td>${r.regen2_jam ? '✓ '+r.regen2_jam : '-'}</td>
                </tr>
            `);
            });
        }
        // ── Muat Data otomatis ─────────────────────────────────────────────
        function loadData() {
            const tgl = $('#tanggal').val();
            if (!tgl) return;

            const d = new Date(tgl);
            const bulan = d.getMonth() + 1;
            const tahun = d.getFullYear();

            $.ajax({
                url: DATA,
                data: {
                    bulan,
                    tahun
                },
                success: function(res) {
                    const approval = res.approval;
                    isBlocked = approval && approval.status === 'approved_supervisor';
                    // if (isBlocked) {
                    //     $('#blockedNotice').toggleClass('show', isBlocked);
                    //     $('#btnSave').prop('disabled', isBlocked);
                    // }
                    const row = res.data.find(r => r.tanggal && r.tanggal.substring(0, 10) === tgl);
                    resetForm();
                    if (row) {
                        setStatus('success', `<i class="bx bx-check-circle me-1"></i>
                            Data tanggal <strong>${formatTgl(tgl)}</strong> sudah ada.`);
                    } else {
                        setStatus('info', `<i class="bx bx-plus-circle me-1"></i>
                            Belum ada data untuk tanggal <strong>${formatTgl(tgl)}</strong>.`);
                    }

                    buildTable(res.data);
                },
                error: function() {
                    setStatus('danger', '<i class="bx bx-error-circle me-1"></i>Gagal memuat data.');
                }
            });
        }

        // Trigger load saat tanggal berubah
        $('#tanggal').on('change', loadData);

        // Auto-load saat halaman dibuka
        loadData();

    });
</script>
@endsection