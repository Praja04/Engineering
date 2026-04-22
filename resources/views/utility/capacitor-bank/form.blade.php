@extends('layouts.app')

@section('title', 'Capacitor Bank — Input Harian')

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

    /* CAP A — blue */
    .hdr-cap-a {
        background: linear-gradient(90deg, #e8f4fd, #dbeeff);
        color: #1a6aad;
    }

    .hdr-cap-a .icon-wrap {
        background: #1a6aad22;
        color: #1a6aad;
    }

    /* CAP B — teal */
    .hdr-cap-b {
        background: linear-gradient(90deg, #e6f7f5, #d4f3ef);
        color: #0e8a7a;
    }

    .hdr-cap-b .icon-wrap {
        background: #0e8a7a22;
        color: #0e8a7a;
    }

    /* CAP C — amber */
    .hdr-cap-c {
        background: linear-gradient(90deg, #fff8e6, #fff0cc);
        color: #b07d00;
    }

    .hdr-cap-c .icon-wrap {
        background: #b07d0022;
        color: #b07d00;
    }

    /* SUHU — rose */
    .hdr-suhu {
        background: linear-gradient(90deg, #ffeef0, #ffd9de);
        color: #c0384a;
    }

    .hdr-suhu .icon-wrap {
        background: #c0384a22;
        color: #c0384a;
    }

    [data-layout-mode="dark"] .hdr-cap-a {
        background: linear-gradient(90deg, #13273b, #0f2236);
        color: #7eb8e8;
    }

    [data-layout-mode="dark"] .hdr-cap-b {
        background: linear-gradient(90deg, #0e2a28, #0b2320);
        color: #5fccc0;
    }

    [data-layout-mode="dark"] .hdr-cap-c {
        background: linear-gradient(90deg, #2a2210, #231c0a);
        color: #ffc947;
    }

    [data-layout-mode="dark"] .hdr-suhu {
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

    /* ── Flatpickr time input ── */
    .flatpickr-input {
        background-color: inherit !important;
        cursor: pointer;
    }

    .flatpickr-input[readonly] {
        cursor: pointer;
    }

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
                    <h4><i class="bx bx-chip me-2"></i>Capacitor Bank — Input Harian</h4>
                    <p>Engineering Utility · Monitoring arus dan kondisi capacitor</p>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge-status"><i class="bx bx-calendar-check"></i> Monitoring Harian</span>
                    <a href="{{ route('capacitor-bank.rekap') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
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

        {{-- ── Status Bar ── --}}
        <div class="alert" id="statusBar"></div>

        {{-- ── Main Form ── --}}
        <form id="formCap" novalidate>
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

            <div class="date-card">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Jam Operasi</label>
                        <div class="input-group">
                            <input type="text" class="form-control fp-time" id="jam" name="jam" placeholder="HH:MM" autocomplete="off" readonly>
                            <span class="unit-badge"><i class="bx bx-time-five"></i></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Arus Total</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" class="form-control" id="arus_total" name="arus_total" placeholder="0.00">
                            <span class="unit-badge">A</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                {{-- ── CAP A ── --}}
                <div class="col-lg-4">
                    <div class="ws-section">
                        <div class="ws-section-header hdr-cap-a">
                            <div class="icon-wrap"><i class="bx bx-chip"></i></div>
                            Capacitor A
                        </div>
                        <div class="ws-section-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Nomor</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cap_a_nomor" name="cap_a_nomor" placeholder="0.00">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">I1</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cap_a_i1" name="cap_a_i1" placeholder="0.00">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">I2</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cap_a_i2" name="cap_a_i2" placeholder="0.00">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">I3</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cap_a_i3" name="cap_a_i3" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── CAP B ── --}}
                <div class="col-lg-4">
                    <div class="ws-section">
                        <div class="ws-section-header hdr-cap-b">
                            <div class="icon-wrap"><i class="bx bx-chip"></i></div>
                            Capacitor B
                        </div>
                        <div class="ws-section-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Nomor</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cap_b_nomor" name="cap_b_nomor" placeholder="0.00">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">I1</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cap_b_i1" name="cap_b_i1" placeholder="0.00">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">I2</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cap_b_i2" name="cap_b_i2" placeholder="0.00">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">I3</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cap_b_i3" name="cap_b_i3" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── CAP C ── --}}
                <div class="col-lg-4">
                    <div class="ws-section">
                        <div class="ws-section-header hdr-cap-c">
                            <div class="icon-wrap"><i class="bx bx-chip"></i></div>
                            Capacitor C
                        </div>
                        <div class="ws-section-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Nomor</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cap_c_nomor" name="cap_c_nomor" placeholder="0.00">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">I1</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cap_c_i1" name="cap_c_i1" placeholder="0.00">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">I2</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cap_c_i2" name="cap_c_i2" placeholder="0.00">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">I3</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cap_c_i3" name="cap_c_i3" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Suhu Ruang ── --}}
            <div class="ws-section">
                <div class="ws-section-header hdr-suhu">
                    <div class="icon-wrap"><i class="bx bx-thermometer"></i></div Suhu Ruang </div>
                    <div class="ws-section-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">Suhu Ruang <span class="text-muted fw-normal">(°C)</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control" id="suhu_ruang" name="suhu_ruang" placeholder="0.00">
                                    <span class="unit-badge">°C</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Submit Row ── --}}
                <div class="ws-submit-row d-flex align-items-center justify-content-between flex-wrap gap-3" data-aos="fade-up" data-aos-delay="300">
                    <div>
                        <p class="mb-0 text-muted" style="font-size:.83rem;">
                            <i class="bx bx-info-circle me-1"></i>
                            Semua field boleh kosong kecuali tanggal. Isi data sesuai pengukuran aktual.
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
        const STORE = "{{ route('capacitor-bank.store') }}";
        const DATA = "{{ route('capacitor-bank.data') }}";
        let isBlocked = false;
        let fpInstance = null;

        // ── Inisialisasi Flatpickr 24-jam ────
        fpInstance = flatpickr("#jam", {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true,
            minuteIncrement: 1,
            allowInput: false,
        });

        // ── Submit Form ───────────────────────────────────────────
        $('#formCap').on('submit', function(e) {
            e.preventDefault();
            if (isBlocked) return;

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
                    jam: $('#jam').val() || null,
                    arus_total: $('#arus_total').val() || null,

                    cap_a_nomor: $('#cap_a_nomor').val() || null,
                    cap_a_i1: $('#cap_a_i1').val() || null,
                    cap_a_i2: $('#cap_a_i2').val() || null,
                    cap_a_i3: $('#cap_a_i3').val() || null,

                    cap_b_nomor: $('#cap_b_nomor').val() || null,
                    cap_b_i1: $('#cap_b_i1').val() || null,
                    cap_b_i2: $('#cap_b_i2').val() || null,
                    cap_b_i3: $('#cap_b_i3').val() || null,

                    cap_c_nomor: $('#cap_c_nomor').val() || null,
                    cap_c_i1: $('#cap_c_i1').val() || null,
                    cap_c_i2: $('#cap_c_i2').val() || null,
                    cap_c_i3: $('#cap_c_i3').val() || null,

                    suhu_ruang: $('#suhu_ruang').val() || null,
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
        function fillForm(row) {
            // Semua field
            const fields = [
                'jam', 'arus_total', 'cap_a_nomor', 'cap_a_i1', 'cap_a_i2', 'cap_a_i3',
                'cap_b_nomor', 'cap_b_i1', 'cap_b_i2', 'cap_b_i3',
                'cap_c_nomor', 'cap_c_i1', 'cap_c_i2', 'cap_c_i3', 'suhu_ruang'
            ];

            fields.forEach(f => {
                const val = row[f] ?? '';
                if (f === 'jam' && val) {
                    fpInstance.setDate(val, false);
                } else {
                    $('#' + f).val(val);
                }
            });
        }

        function resetForm() {
            $('#jam').val('');
            fpInstance.clear();

            const fields = [
                'arus_total', 'cap_a_nomor', 'cap_a_i1', 'cap_a_i2', 'cap_a_i3',
                'cap_b_nomor', 'cap_b_i1', 'cap_b_i2', 'cap_b_i3',
                'cap_c_nomor', 'cap_c_i1', 'cap_c_i2', 'cap_c_i3', 'suhu_ruang'
            ];
            fields.forEach(f => $('#' + f).val(''));
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
                    $('#blockedNotice').toggleClass('show', isBlocked);
                    $('#btnSave').prop('disabled', isBlocked);

                    const row = res.data.find(r => r.tanggal && r.tanggal.substring(0, 10) === tgl);
                    resetForm();
                    if (row) {
                        setStatus('success', `
                    <i class="bx bx-check-circle me-1"></i>
                    Data tanggal <strong>${formatTgl(tgl)}</strong> sudah ada! Silahkan pilih tanggal lain!.
                    
                `);
                    } else {
                        setStatus('info', `
                    <i class="bx bx-plus-circle me-1"></i>
                    Belum ada data untuk tanggal <strong>${formatTgl(tgl)}</strong>.
                `);
                    }
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