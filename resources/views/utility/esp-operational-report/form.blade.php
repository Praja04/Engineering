@extends('layouts.app')

@section('title', 'ESP Report - Form Input')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="row">
                <div class="col-12">
                    <div class="card border-0" style="background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%);">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="text-white fw-bold mb-1">
                                        <i class="ri-flashlight-line me-2" style="color:#f6c90e;"></i>
                                        ESP Report — Input Data
                                    </h4>
                                    <p class="text-white-50 mb-0 fs-14">
                                        Pilih jenis laporan yang ingin diisi
                                    </p>
                                </div>
                                <div class="col-auto">
                                    <span class="badge bg-light text-dark fs-12 me-2" id="clock-now"></span>
                                    <span class="badge fs-12" id="badge-tanggal-shift"
                                        style="background:rgba(255,255,255,.15); color:#fff;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════
             CARD PILIHAN (tampil default)
        ══════════════════════════════════════ --}}
            <div id="section-pilihan" class="row g-4 mt-1">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 esp-pick-card" id="btn-pick-operational"
                        style="cursor:pointer; transition:.2s;">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center me-3"
                                    style="background:linear-gradient(135deg,#4361ee,#3a0ca3);
                                       width:56px;height:56px;min-width:56px;">
                                    <i class="ri-bar-chart-2-line text-white fs-24"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-semibold">Operational Report</h5>
                                    <small class="text-muted">Input data per jam</small>
                                </div>
                            </div>
                            <p class="text-muted fs-14 mb-3 flex-grow-1">
                                Pencatatan data operasional ESP setiap jam: arus primer/sekunder,
                                tegangan primer/sekunder, dan suhu thermal.
                            </p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-primary-subtle text-primary">Arus Primer</span>
                                <span class="badge bg-primary-subtle text-primary">Arus Sekunder</span>
                                <span class="badge bg-primary-subtle text-primary">Tegangan</span>
                                <span class="badge bg-primary-subtle text-primary">Suhu Thermal</span>
                            </div>
                            <div class="border-top pt-2 d-flex justify-content-between align-items-center">
                                <span class="text-muted fs-13"><i class="ri-time-line me-1"></i>Setiap jam
                                    (06:00–05:00)</span>
                                <span class="text-primary fw-semibold fs-13">Pilih <i
                                        class="ri-arrow-right-line"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 esp-pick-card" id="btn-pick-shift"
                        style="cursor:pointer; transition:.2s;">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center me-3"
                                    style="background:linear-gradient(135deg,#f72585,#7209b7);
                                       width:56px;height:56px;min-width:56px;">
                                    <i class="ri-file-list-3-line text-white fs-24"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-semibold">Shift Report</h5>
                                    <small class="text-muted">Laporan ringkasan akhir shift</small>
                                </div>
                            </div>
                            <p class="text-muted fs-14 mb-3 flex-grow-1">
                                Laporan rekapitulasi akhir shift: pemakaian air, steam, batubara,
                                running hour, feed tank, dan chemical treatment.
                            </p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-danger-subtle text-danger">Air & Steam</span>
                                <span class="badge bg-danger-subtle text-danger">Batubara</span>
                                <span class="badge bg-danger-subtle text-danger">Running Hour</span>
                                <span class="badge bg-danger-subtle text-danger">Chemical</span>
                            </div>
                            <div class="border-top pt-2 d-flex justify-content-between align-items-center">
                                <span class="text-muted fs-13"><i class="ri-time-line me-1"></i>Akhir shift (sebelum
                                    07:00)</span>
                                <span class="text-danger fw-semibold fs-13">Pilih <i class="ri-arrow-right-line"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════
             FORM OPERATIONAL REPORT
        ══════════════════════════════════════ --}}
            <div id="section-operational" style="display:none;">
                <div class="row mb-3">
                    <div class="col-12">
                        <button class="btn btn-sm btn-outline-secondary btn-back">
                            <i class="ri-arrow-left-line me-1"></i>Kembali
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-bottom">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="ri-bar-chart-2-line me-2 text-primary"></i>
                                    Input Operational Report
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="form-operational">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="form-label fw-medium">Jam Laporan <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" name="jam_laporan" id="jam_laporan" required>
                                                <option value="">-- Pilih Jam --</option>
                                                @php
                                                    $hours = [];
                                                    for ($h = 6; $h <= 23; $h++) {
                                                        $hours[] = sprintf('%02d:00', $h);
                                                    }
                                                    for ($h = 0; $h <= 5; $h++) {
                                                        $hours[] = sprintf('%02d:00', $h);
                                                } @endphp @foreach ($hours as $h)
                                                    <option value="{{ $h }}">{{ $h }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-medium">Grup <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" name="grup" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="A">Grup A</option>
                                                <option value="B">Grup B</option>
                                                <option value="C">Grup C</option>
                                                <option value="D">Grup D</option>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <hr class="my-1">
                                            <p class="text-muted fs-12 mb-0">Data Arus</p>
                                        </div>

                                        <div class="col-6">
                                            <label class="form-label fw-medium">Arus Primer (A)</label>
                                            <input type="number" step="0.01" class="form-control" name="arus_primer"
                                                placeholder="0.00">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-medium">Arus Sekunder (mA)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="arus_sekunder" placeholder="0.00">
                                        </div>

                                        <div class="col-12">
                                            <hr class="my-1">
                                            <p class="text-muted fs-12 mb-0">Data Tegangan</p>
                                        </div>

                                        <div class="col-6">
                                            <label class="form-label fw-medium">Tegangan Primer (V)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="tegangan_primer" placeholder="0.00">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-medium">Tegangan Sekunder (kV)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="tegangan_sekunder" placeholder="0.00">
                                        </div>

                                        <div class="col-12">
                                            <hr class="my-1">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-medium">Suhu Thermal (°C)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="suhu_thermal" placeholder="0.00">
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary w-100"
                                                id="btn-submit-operational">
                                                <i class="ri-save-line me-1"></i>Simpan Data
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Preview tabel data hari ini --}}
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm">
                            <div
                                class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="ri-table-line me-2 text-info"></i>
                                    Data Hari Ini
                                </h5>
                                <div class="d-flex gap-2">
                                    <select class="form-select form-select-sm" id="filter-grup-operational"
                                        style="width:auto;">
                                        <option value="">Semua Grup</option>
                                        <option value="A">Grup A</option>
                                        <option value="B">Grup B</option>
                                        <option value="C">Grup C</option>
                                        <option value="D">Grup D</option>
                                    </select>
                                    <button class="btn btn-sm btn-outline-info" id="btn-refresh-operational">
                                        <i class="ri-refresh-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height:420px; overflow-y:auto;">
                                    <table class="table table-sm table-hover table-bordered mb-0 text-center">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>Jam</th>
                                                <th>Grup</th>
                                                <th>A.Primer</th>
                                                <th>A.Sekunder</th>
                                                <th>T.Primer</th>
                                                <th>T.Sekunder</th>
                                                <th>Suhu</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-operational-preview">
                                            <tr>
                                                <td colspan="7" class="text-center py-3 text-muted">
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

            {{-- ══════════════════════════════════════
             FORM SHIFT REPORT
        ══════════════════════════════════════ --}}
            <div id="section-shift" style="display:none;">
                <div class="row mb-3">
                    <div class="col-12">
                        <button class="btn btn-sm btn-outline-secondary btn-back">
                            <i class="ri-arrow-left-line me-1"></i>Kembali
                        </button>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-bottom">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0 fw-semibold">
                                        <i class="ri-file-list-3-line me-2 text-danger"></i>
                                        Input Shift Report
                                    </h5>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fs-13 text-muted">Tanggal Laporan:</span>
                                        @if (auth()->user()->jabatan === 'operator')
                                            <span class="badge bg-danger fs-13 px-3 py-2"
                                                id="display-tanggal-shift">—</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="form-shift">
                                    @csrf

                                    {{-- Tanggal laporan: readonly untuk operator, date picker untuk non-operator --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Tanggal Laporan</label>
                                        @if (auth()->user()->jabatan === 'operator')
                                            <input type="date" class="form-control" name="tanggal_laporan"
                                                id="input-tanggal-shift" readonly
                                                style="background:#f8f9fa; cursor:not-allowed;">
                                        @else
                                            <input type="date" class="form-control" name="tanggal_laporan"
                                                id="input-tanggal-shift">
                                        @endif
                                    </div>

                                    {{-- Alert jika operator & di luar waktu input --}}
                                    @if (auth()->user()->jabatan === 'operator')
                                        <div id="alert-waktu-shift" class="alert alert-warning d-none" role="alert">
                                            <i class="ri-error-warning-line me-2"></i>
                                            Laporan hanya bisa diinput sebelum jam <strong>08:00</strong>.
                                            Waktu sekarang sudah melewati batas.
                                        </div>
                                    @endif

                                    {{-- ══ PILIH FOREMAN & SUPERVISOR ══ --}}
                                    <div class="row g-3 mb-3">
                                        <div class="col-12">
                                            <h6 class="fw-semibold text-muted border-bottom pb-2">
                                                <i class="ri-user-star-line me-1"></i>Penanggungjawab Approval
                                            </h6>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">
                                                Foreman <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" name="foreman_id" id="select-foreman" required>
                                                <option value="">— Memuat data... —</option>
                                            </select>
                                            <div class="form-text text-muted fs-12">
                                                <i class="ri-info-line me-1"></i>Approval tahap pertama
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">
                                                Supervisor <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" name="supervisor_id" id="select-supervisor"
                                                required>
                                                <option value="">— Memuat data... —</option>
                                            </select>
                                            <div class="form-text text-muted fs-12">
                                                <i class="ri-info-line me-1"></i>Final approval
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        {{-- PEMAKAIAN --}}
                                        <div class="col-12">
                                            <h6 class="fw-semibold text-muted border-bottom pb-2">
                                                <i class="ri-drop-line me-1"></i>Pemakaian
                                            </h6>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Pemakaian Air (m³)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="pemakaian_air" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Pemakaian Steam (ton)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="pemakaian_steam" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Pemakaian Batubara (ton)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="pemakaian_batubara" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Efisiensi Batubara (%)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="efisiensi_batubara" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Pengisian Batubara (ton)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="pengisian_batubara" placeholder="0.00">
                                        </div>

                                        {{-- RUNNING HOUR --}}
                                        <div class="col-12 mt-2">
                                            <h6 class="fw-semibold text-muted border-bottom pb-2">
                                                <i class="ri-timer-line me-1"></i>Running Hour & Feed Tank
                                            </h6>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">RH Awal (jam)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="running_hour_awal" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">RH Akhir (jam)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="running_hour_akhir" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Feed Tank Awal (m³)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="feed_tank_awal" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Feed Tank Akhir (m³)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="feed_tank_akhir" placeholder="0.00">
                                        </div>

                                        {{-- CHEMICAL --}}
                                        <div class="col-12 mt-2">
                                            <h6 class="fw-semibold text-muted border-bottom pb-2">
                                                <i class="ri-flask-line me-1"></i>Chemical
                                            </h6>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Chemical SCF (L)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="chemical_scf" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Chemical SRTF (L)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                name="chemical_srtf" placeholder="0.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Dosis (ppm)</label>
                                            <input type="number" step="0.01" class="form-control" name="dosis"
                                                placeholder="0.00">
                                        </div>

                                        <div class="col-12 mt-2">
                                            <button type="submit" class="btn btn-danger w-100" id="btn-submit-shift">
                                                <i class="ri-send-plane-line me-1"></i>Submit Laporan
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {

            // ── CLOCK & info tanggal laporan shift ──────────────────────────
            function updateClock() {
                const now = new Date();
                const hh = String(now.getHours()).padStart(2, '0');
                const mm = String(now.getMinutes()).padStart(2, '0');
                const ss = String(now.getSeconds()).padStart(2, '0');
                $('#clock-now').text(hh + ':' + mm + ':' + ss);
            }
            updateClock();
            setInterval(updateClock, 1000);

            // Resolusi tanggal laporan shift (operator):
            // jam 00:00–05:59 → pakai tanggal kemarin
            function resolveShiftDate() {
                const now = new Date();
                const hh = now.getHours();
                const d = (hh < 6) ? new Date(now - 86400000) : now;
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${day}`;
            }

            const isOperator = {{ auth()->user()->jabatan === 'operator' ? 'true' : 'false' }};

            if (isOperator) {
                const tgl = resolveShiftDate();
                $('#display-tanggal-shift').text(tgl);
                $('#input-tanggal-shift').val(tgl);

                // Update badge header
                $('#badge-tanggal-shift').text('Laporan: ' + tgl);

                // Cek apakah jam sudah >= 06:00 (lewat batas input)
                const hh = new Date().getHours();
                if (hh >= 8) {
                    // $('#alert-waktu-shift').removeClass('d-none');
                    // $('#btn-submit-shift').prop('disabled', true)
                    //     .html('<i class="ri-lock-line me-1"></i>Waktu Input Sudah Tutup');
                }
            } else {
                // Non-operator: default ke hari ini
                const now = new Date();
                const iso = now.toISOString().slice(0, 10);
                $('#input-tanggal-shift').val(iso);
            }

            // ── LOAD APPROVERS (Foreman & Supervisor) ───────────────────────
            function loadApprovers() {
                $.get('/api/utility/users/approvers', function(data) {
                    // Isi dropdown Foreman — dari data.staff (jabatan bukan operator, dept engineering)
                    const foremanList = data.staff ?? [];
                    let foremanOpts = '<option value="">— Pilih Foreman —</option>';
                    foremanList.forEach(function(u) {
                        foremanOpts += `<option value="${u.id}">${u.username}</option>`;
                    });
                    $('#select-foreman').html(foremanOpts);

                    // Isi dropdown Supervisor — dari data.user (jabatan supervisor)
                    const supervisorList = data.user ?? [];
                    let supervisorOpts = '<option value="">— Pilih Supervisor —</option>';
                    supervisorList.forEach(function(u) {
                        supervisorOpts += `<option value="${u.id}">${u.username}</option>`;
                    });
                    $('#select-supervisor').html(supervisorOpts);
                }).fail(function() {
                    $('#select-foreman').html('<option value="">Gagal memuat data</option>');
                    $('#select-supervisor').html('<option value="">Gagal memuat data</option>');
                    toastr.error('Gagal memuat daftar approver');
                });
            }

            // ── TOGGLE SECTION ───────────────────────────────────────────────
            function showSection(target) {
                $('#section-pilihan').hide();
                $('#section-operational').hide();
                $('#section-shift').hide();
                $(target).show();
            }

            $('#btn-pick-operational').on('click', function() {
                showSection('#section-operational');
                loadOperationalPreview();
            });

            $('#btn-pick-shift').on('click', function() {
                showSection('#section-shift');
                loadApprovers(); // Load approvers saat form shift dibuka
            });

            $('.btn-back').on('click', function() {
                showSection('#section-pilihan');
            });

            // Hover efek card pilihan
            $('.esp-pick-card').on('mouseenter', function() {
                $(this).css('transform', 'translateY(-4px)').css('box-shadow',
                    '0 8px 24px rgba(0,0,0,.12)');
            }).on('mouseleave', function() {
                $(this).css('transform', '').css('box-shadow', '');
            });

            // ── LOAD PREVIEW OPERATIONAL ─────────────────────────────────────
            function loadOperationalPreview() {
                const tanggal = new Date().toISOString().slice(0, 10);
                const grup = $('#filter-grup-operational').val();
                $('#tbody-operational-preview').html(
                    '<tr><td colspan="7" class="text-center py-3 text-muted"><i class="ri-loader-4-line"></i> Memuat...</td></tr>'
                );
                $.get('{{ route('esp-operational-report.json') }}', {
                    tanggal,
                    grup
                }, function(data) {
                    let html = '';
                    data.forEach(function(r) {
                        const hasData = r.arus_primer !== null || r.arus_sekunder !== null;
                        html += `<tr class="${hasData ? '' : 'text-muted'}">
                    <td class="fw-medium">${r.jam}</td>
                    <td>${r.grup ?? '—'}</td>
                    <td>${r.arus_primer ?? '—'}</td>
                    <td>${r.arus_sekunder ?? '—'}</td>
                    <td>${r.tegangan_primer ?? '—'}</td>
                    <td>${r.tegangan_sekunder ?? '—'}</td>
                    <td>${r.suhu_thermal ?? '—'}</td>
                </tr>`;
                    });
                    $('#tbody-operational-preview').html(html ||
                        '<tr><td colspan="7" class="text-center text-muted py-3">Belum ada data</td></tr>'
                    );
                });
            }

            $('#filter-grup-operational, #btn-refresh-operational').on('change click', function() {
                loadOperationalPreview();
            });

            // ── SUBMIT OPERATIONAL ───────────────────────────────────────────
            $('#form-operational').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btn-submit-operational');
                $btn.prop('disabled', true).html('<i class="ri-loader-4-line me-1"></i>Menyimpan...');

                $.ajax({
                    url: '{{ route('esp-operational-report.store') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        toastr.success(res.message ?? 'Data berhasil disimpan');
                        loadOperationalPreview();
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors;
                        if (errors) {
                            const msg = Object.values(errors).flat().join('<br>');
                            toastr.error(msg, 'Validasi gagal', {
                                escapeHtml: false
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message ?? 'Terjadi kesalahan');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(
                            '<i class="ri-save-line me-1"></i>Simpan Data');
                    }
                });
            });

            // ── SUBMIT SHIFT REPORT ──────────────────────────────────────────
            $('#form-shift').on('submit', function(e) {
                e.preventDefault();

                // Validasi foreman & supervisor wajib dipilih
                if (!$('#select-foreman').val()) {
                    toastr.error('Silakan pilih Foreman terlebih dahulu');
                    return;
                }
                if (!$('#select-supervisor').val()) {
                    toastr.error('Silakan pilih Supervisor terlebih dahulu');
                    return;
                }

                const $btn = $('#btn-submit-shift');
                $btn.prop('disabled', true).html('<i class="ri-loader-4-line me-1"></i>Menyimpan...');

                $.ajax({
                    url: '{{ route('esp-shift-report.store') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Laporan Terkirim',
                            text: res.message ??
                                'Laporan shift berhasil disubmit dan menunggu approval.',
                            confirmButtonText: 'OK'
                        });
                        $('#form-shift')[0].reset();

                        // Reset tanggal operator
                        if (isOperator) {
                            const tgl = resolveShiftDate();
                            $('#display-tanggal-shift').text(tgl);
                            $('#input-tanggal-shift').val(tgl);
                        }

                        // Reset & reload approvers
                        loadApprovers();
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message ?? 'Terjadi kesalahan';
                        const errors = xhr.responseJSON?.errors;
                        if (errors) {
                            const errMsg = Object.values(errors).flat().join('<br>');
                            toastr.error(errMsg, 'Validasi gagal', {
                                escapeHtml: false
                            });
                        } else {
                            toastr.error(msg);
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(
                            '<i class="ri-send-plane-line me-1"></i>Submit Laporan');
                    }
                });
            });

        });
    </script>
@endsection
