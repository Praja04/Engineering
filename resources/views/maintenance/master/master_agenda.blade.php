@extends('layouts.app')

@section('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        .page-content {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            color: #1e293b;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* ── Back Button ── */
        .btn-back {
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .btn-back:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        .filter-chip.active-chip {
            background: #0f172a !important;
            border-color: #0f172a !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }

        /* ── Upload Zone ── */
        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.2s ease;
            position: relative;
        }

        .upload-zone:hover {
            border-color: #2563eb;
            background: rgba(37, 99, 235, 0.02);
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .file-input-hidden {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        /* ── Planner Pill Badges ── */
        .planner-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            line-height: 1.2;
        }

        .planner-badge.pkg-z,
        .planner-badge.pkg-Z {
            background: rgba(37, 99, 235, 0.07);
            color: #2563eb;
            border-color: rgba(37, 99, 235, 0.15);
        }

        .planner-badge.pkg-a,
        .planner-badge.pkg-A {
            background: rgba(34, 197, 94, 0.07);
            color: #16a34a;
            border-color: rgba(34, 197, 94, 0.15);
        }

        .planner-badge.pkg-b,
        .planner-badge.pkg-B {
            background: rgba(249, 115, 22, 0.07);
            color: #ea580c;
            border-color: rgba(249, 115, 22, 0.15);
        }

        .planner-badge.pkg-c,
        .planner-badge.pkg-C {
            background: rgba(6, 182, 212, 0.07);
            color: #0891b2;
            border-color: rgba(6, 182, 212, 0.15);
        }

        .planner-badge.pkg-d,
        .planner-badge.pkg-D {
            background: rgba(239, 68, 68, 0.07);
            color: #dc2626;
            border-color: rgba(239, 68, 68, 0.15);
        }

        .planner-badge.pkg-other {
            background: rgba(100, 116, 139, 0.07);
            color: #475569;
            border-color: rgba(100, 116, 139, 0.15);
        }

        /* ── Table Design ── */
        .planner-table thead th {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            padding: 12px 16px;
            background: #f8fafc;
            text-align: center;
            white-space: nowrap;
        }

        .planner-table tbody td {
            padding: 12px 16px;
            font-size: 13px;
            vertical-align: middle;
            color: #334155;
        }

        .planner-cell {
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .planner-cell:hover {
            background-color: rgba(37, 99, 235, 0.05) !important;
        }

        .spinner-border {
            width: 2rem;
            height: 2rem;
        }

        /* ── Tabs Styling ── */
        .nav-tabs .nav-link {
            border: none;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 16px;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
            background: transparent;
        }

        .nav-tabs .nav-link:hover {
            color: #0f172a;
            border-bottom: 2px solid #cbd5e1;
        }

        .nav-tabs .nav-link.active {
            color: #0f172a !important;
            border-bottom: 2px solid #0f172a !important;
            background: transparent !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- ══════════════════════════════════════════ HEADER ══ --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 animate-fade-in-up">
                <div>
                    <h1 class="fw-bold fs-3 mb-1 mt-2" style="letter-spacing: -0.5px;">Master Agenda Plan</h1>
                    <p class="text-secondary small mb-0 fw-medium">
                        Upload dan kelola rencana agenda perawatan tahunan per jenis MTC
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-dark shadow-sm d-flex align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#manualAgendaModal" style="border-radius: 8px;">
                        <i class="mdi mdi-plus-circle-outline"></i>
                        Tambah Agenda Manual
                    </button>
                    <button type="button" class="btn btn-outline-dark shadow-sm d-flex align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#uploadExcelModal" style="border-radius: 8px;">
                        <i class="mdi mdi-upload"></i>
                        Upload Excel
                    </button>
                </div>
            </div>



            {{-- ═══════════════════════════════════ JENIS FILTER ══ --}}
            <div class="d-flex align-items-center flex-wrap gap-3 mb-4 animate-fade-in-up" style="animation-delay: 0.15s;">
                <span class="text-secondary text-uppercase fw-bold" style="font-size:11px; letter-spacing:.05em;">Jenis
                    Mesin:</span>
                <div class="d-flex flex-wrap gap-2" id="filterChipsContainer">
                    @foreach ($jenisMtcList as $jenis)
                        <button type="button"
                            class="filter-chip btn btn-outline-dark shadow-sm {{ $selectedJenis === $jenis ? 'active-chip' : '' }}"
                            data-jenis="{{ $jenis }}">
                            {{ $jenis }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- ════════════════════════════════ MATRIX VIEW CARD ══ --}}
            <div class="card shadow-sm animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark fs-6">Preview Matrix Agenda</span>
                            <span class="badge badge-soft-secondary border border-secondary-subtle px-2 py-1"
                                style="font-size: 11px;">
                                <strong id="badgeJenis">{{ $selectedJenis }}</strong> &nbsp;|&nbsp; Tahun: <strong
                                    id="badgeTahun">{{ $selectedYear }}</strong>
                            </span>
                            <span class="badge badge-soft-success border border-success-subtle px-2 py-1"
                                style="font-size: 11px;">
                                Total Agenda: <strong id="badgeTotalAgenda">0</strong>
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <label for="tahunSelect" class="text-secondary small fw-bold mb-0">Pilih Tahun:</label>
                            <select id="tahunSelect" class="form-select form-select-sm"
                                style="width: 100px; border-radius: 8px;">
                                @for ($y = date('Y') - 2; $y <= date('Y') + 4; $y++)
                                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Table Container driven by jQuery AJAX -->
                    <div id="matrixTableWrapper">
                        <!-- Loaded dynamically -->
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════ EDIT CELL MODAL ══ -- border-radius -->
            <div class="modal fade" id="editAgendaModal" tabindex="-1" aria-labelledby="editAgendaModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
                        <form id="editAgendaForm">
                            @csrf
                            <input type="hidden" name="mesin_id" id="editMesinId">
                            <input type="hidden" name="tahun" id="editTahun">
                            <input type="hidden" name="bulan" id="editBulan">

                            <div class="modal-header bg-light">
                                <h5 class="modal-title fw-bold text-dark fs-6" id="editAgendaModalLabel">Kelola Agenda
                                    Bulanan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-bold text-secondary mb-1">Nama Mesin</label>
                                        <div class="fw-bold text-dark" id="editMesinNamaText">-</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-bold text-secondary mb-1">Periode
                                            Perawatan</label>
                                        <div class="fw-bold text-dark" id="editPeriodeText">-</div>
                                    </div>
                                </div>

                                <div id="weeklyEditContainer">
                                    <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3 small text-uppercase"
                                        style="letter-spacing: .05em;">Rencana Mingguan</h6>

                                    @for ($w = 1; $w <= 5; $w++)
                                        <div class="row align-items-center mb-3">
                                            <div class="col-4">
                                                <label for="editWeek{{ $w }}"
                                                    class="small fw-semibold text-secondary mb-0">Minggu
                                                    Ke-{{ $w }}</label>
                                            </div>
                                            <div class="col-8">
                                                <select name="weeks[{{ $w }}]" id="editWeek{{ $w }}"
                                                    class="form-select form-select-sm week-select"
                                                    style="border-radius: 8px; padding: 6px 12px;">
                                                    <option value="none">(Kosong / Hapus)</option>
                                                    <option value="Z">Paket Z</option>
                                                    <option value="A">Paket A</option>
                                                    <option value="B">Paket B</option>
                                                    <option value="C">Paket C</option>
                                                    <option value="D">Paket D</option>
                                                    <option value="Checkpoint">Checkpoint</option>
                                                    <option value="Korektif">Korektif</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endfor
                                </div>

                                <div id="dateEditContainer" class="d-none">
                                    <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3 small text-uppercase"
                                        style="letter-spacing: .05em;">Rencana Harian (Tanggal)</h6>
                                    <div id="dynamicDateRows" style="max-height: 250px; overflow-y: auto; padding-right: 4px;" class="mb-3">
                                        <!-- dynamic rows -->
                                    </div>
                                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold mt-2" id="btnAddDateRow" style="border-radius: 8px;">
                                        <i class="mdi mdi-plus"></i> Tambah Tanggal
                                    </button>
                                </div>

                                <!-- Alert feedback inside modal -->
                                <div id="editFeedback" class="alert d-none py-2 small mb-0 mt-3"
                                    style="border-radius: 8px;"></div>
                            </div>
                            <div class="modal-footer bg-light justify-content-between p-3">
                                <button type="button" class="btn btn-outline-danger btn-sm fw-bold" id="btnHapusSemua"
                                    style="border-radius: 8px; padding: 6px 16px;">
                                    Hapus Semua
                                </button>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-light btn-sm fw-semibold"
                                        data-bs-dismiss="modal"
                                        style="border-radius: 8px; padding: 6px 16px;">Batal</button>
                                    <button type="submit" class="btn btn-dark btn-sm fw-bold" id="btnSaveSingle"
                                        style="border-radius: 8px; padding: 6px 16px;">Simpan Perubahan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════ UPLOAD EXCEL MODAL ══ -->
            <div class="modal fade" id="uploadExcelModal" tabindex="-1" aria-labelledby="uploadExcelModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
                        <form id="uploadAgendaForm" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header bg-light">
                                <h5 class="modal-title fw-bold text-dark fs-6" id="uploadExcelModalLabel">Upload File
                                    Agenda</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label for="uploadJenisMtc" class="form-label small fw-bold text-secondary">Jenis
                                        Maintenance (MTC) <span class="text-danger">*</span></label>
                                    <select name="jenis_mtc" id="uploadJenisMtc" class="form-select form-select-sm"
                                        required style="border-radius: 8px; padding: 8px 12px;">
                                        @foreach ($jenisMtcList as $jenis)
                                            <option value="{{ $jenis }}"
                                                {{ $selectedJenis === $jenis ? 'selected' : '' }}>{{ $jenis }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="uploadTahun" class="form-label small fw-bold text-secondary">Tahun Agenda
                                        <span class="text-danger">*</span></label>
                                    <select name="tahun" id="uploadTahun" class="form-select form-select-sm" required
                                        style="border-radius: 8px; padding: 8px 12px;">
                                        @for ($y = date('Y') - 1; $y <= date('Y') + 4; $y++)
                                            <option value="{{ $y }}"
                                                {{ $selectedYear == $y ? 'selected' : '' }}>
                                                {{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-secondary">File Excel <span
                                            class="text-danger">*</span></label>
                                    <div class="upload-zone">
                                        <div class="file-input-wrapper">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="text-secondary mb-2"
                                                width="32" height="32" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                            </svg>
                                            <div class="small fw-bold text-primary" id="fileNamePlaceholder">Pilih file
                                                excel (.xlsx, .xls)</div>
                                            <input type="file" name="file_excel" id="fileExcel"
                                                class="file-input-hidden" accept=".xlsx, .xls" required
                                                onchange="updateFileName(this)">
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="progress mb-3 d-none" id="uploadProgressContainer"
                                    style="height: 6px; border-radius: 10px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-dark"
                                        id="uploadProgressBar" role="progressbar" style="width: 0%;"></div>
                                </div>

                                <!-- Feedback Alerts -->
                                <div id="uploadFeedback" class="d-none"></div>

                                <div class="alert alert-warning py-2 px-3 small mb-0"
                                    style="border-radius: 8px; font-size: 11px;">
                                    <strong>Peringatan:</strong> Proses upload akan menimpa (delete &amp; replace) seluruh
                                    Master Agenda untuk jenis yang dipilih pada tahun tersebut.
                                </div>
                            </div>
                            <div class="modal-footer bg-light justify-content-end p-3">
                                <button type="button" class="btn btn-light btn-sm fw-semibold" data-bs-dismiss="modal"
                                    style="border-radius: 8px; padding: 6px 16px;">Batal</button>
                                <button type="submit" class="btn btn-dark btn-sm fw-bold" id="btnSubmitUpload"
                                    style="border-radius: 8px; padding: 6px 16px;">Upload &amp; Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════ MANUAL AGENDA MODAL ══ -->
            <div class="modal fade" id="manualAgendaModal" tabindex="-1" aria-labelledby="manualAgendaModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
                        <form id="manualAgendaForm">
                            @csrf
                            <div class="modal-header bg-light">
                                <h5 class="modal-title fw-bold text-dark fs-6" id="manualAgendaModalLabel">Tambah / Edit
                                    Agenda Manual</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label for="manualJenisMtc" class="form-label small fw-bold text-secondary">Jenis
                                            MTC <span class="text-danger">*</span></label>
                                        <select name="jenis_mtc" id="manualJenisMtc" class="form-select form-select-sm"
                                            required style="border-radius: 8px; padding: 8px 12px;">
                                            @foreach ($jenisMtcList as $jenis)
                                                <option value="{{ $jenis }}"
                                                    {{ $selectedJenis === $jenis ? 'selected' : '' }}>
                                                    {{ $jenis }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="manualMesinId" class="form-label small fw-bold text-secondary">Mesin
                                            <span class="text-danger">*</span></label>
                                        <select name="mesin_id" id="manualMesinId" class="form-select form-select-sm"
                                            required style="border-radius: 8px; padding: 8px 12px;">
                                            <option value="">-- Pilih Mesin --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label for="manualTahun" class="form-label small fw-bold text-secondary">Tahun
                                            <span class="text-danger">*</span></label>
                                        <select name="tahun" id="manualTahun" class="form-select form-select-sm"
                                            required style="border-radius: 8px; padding: 8px 12px;">
                                            @for ($y = date('Y') - 1; $y <= date('Y') + 4; $y++)
                                                <option value="{{ $y }}"
                                                    {{ $selectedYear == $y ? 'selected' : '' }}>
                                                    {{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="manualBulan" class="form-label small fw-bold text-secondary">Bulan
                                            <span class="text-danger">*</span></label>
                                        <select name="bulan" id="manualBulan" class="form-select form-select-sm"
                                            required style="border-radius: 8px; padding: 8px 12px;">
                                            <option value="1">Januari</option>
                                            <option value="2">Februari</option>
                                            <option value="3">Maret</option>
                                            <option value="4">April</option>
                                            <option value="5">Mei</option>
                                            <option value="6">Juni</option>
                                            <option value="7">Juli</option>
                                            <option value="8">Agustus</option>
                                            <option value="9">September</option>
                                            <option value="10">Oktober</option>
                                            <option value="11">November</option>
                                            <option value="12">Desember</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="manualWeeklyContainer">
                                    <h6 class="fw-bold text-secondary border-bottom pb-1 mb-2 small text-uppercase"
                                        style="letter-spacing: .05em; font-size: 11px;">Rencana Mingguan</h6>

                                    <div style="max-height: 220px; overflow-y: auto; padding-right: 4px;" class="mb-3">
                                        @for ($w = 1; $w <= 5; $w++)
                                            <div class="row align-items-center mb-2">
                                                <div class="col-4">
                                                    <label for="manualWeek{{ $w }}"
                                                        class="small fw-semibold text-secondary mb-0"
                                                        style="font-size: 12px;">Minggu {{ $w }}</label>
                                                </div>
                                                <div class="col-8">
                                                    <select name="weeks[{{ $w }}]"
                                                        id="manualWeek{{ $w }}"
                                                        class="form-select form-select-sm manual-week-select"
                                                        style="border-radius: 8px; padding: 5px 10px; font-size: 12px;">
                                                        <option value="none">(Kosong / Hapus)</option>
                                                        <option value="Z">Paket Z</option>
                                                        <option value="A">Paket A</option>
                                                        <option value="B">Paket B</option>
                                                        <option value="C">Paket C</option>
                                                        <option value="D">Paket D</option>
                                                        <option value="Checkpoint">Checkpoint</option>
                                                        <option value="Korektif">Korektif</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div id="manualDateContainer" class="d-none">
                                    <h6 class="fw-bold text-secondary border-bottom pb-1 mb-2 small text-uppercase"
                                        style="letter-spacing: .05em; font-size: 11px;">Rencana Harian (Tanggal)</h6>

                                    <div id="manualDynamicDateRows" style="max-height: 220px; overflow-y: auto; padding-right: 4px;" class="mb-3">
                                        <!-- dynamic rows -->
                                    </div>
                                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold mb-3" id="btnManualAddDateRow" style="border-radius: 8px;">
                                        <i class="mdi mdi-plus"></i> Tambah Tanggal
                                    </button>
                                </div>

                                <div id="manualFeedback" class="alert d-none py-2 small mb-0"
                                    style="border-radius: 8px;"></div>
                            </div>
                            <div class="modal-footer bg-light justify-content-end p-3">
                                <button type="button" class="btn btn-light btn-sm fw-semibold" data-bs-dismiss="modal"
                                    style="border-radius: 8px; padding: 6px 16px;">Batal</button>
                                <button type="submit" class="btn btn-dark btn-sm fw-bold" id="btnSubmitManual"
                                    style="border-radius: 8px; padding: 6px 16px;">Simpan Agenda</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>{{-- /container-fluid --}}
    </div>{{-- /page-content --}}
@endsection

@section('scripts')
    <script>
        let currentJenis = "{{ $selectedJenis }}";
        let currentTahun = "{{ $selectedYear }}";
        let loadedPlans = {};
        let loadedMachines = [];

        /* Indonesian Month Names Helper */
        function getMonthName(num) {
            const months = [
                "", "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];
            return months[num] || "";
        }

        /* Update File Name Label */
        function updateFileName(input) {
            const placeholder = document.getElementById('fileNamePlaceholder');
            if (input.files && input.files[0]) {
                placeholder.textContent = input.files[0].name;
                placeholder.className = "small fw-bold text-success";
            } else {
                placeholder.textContent = "Pilih file excel (.xlsx, .xls)";
                placeholder.className = "small fw-bold text-primary";
            }
        }

        function addManualDateRow(dateVal = '', packageVal = 'A') {
            const yearStr = $('#manualTahun').val() || currentTahun;
            const monthStr = String($('#manualBulan').val() || 1).padStart(2, '0');
            
            const lastDay = new Date(yearStr, monthStr, 0).getDate();
            const minDate = `${yearStr}-${monthStr}-01`;
            const maxDate = `${yearStr}-${monthStr}-${String(lastDay).padStart(2, '0')}`;
            
            const rowHtml = `
                <div class="row align-items-center mb-2 manual-date-row">
                    <div class="col-6">
                        <input type="date" name="dates[]" class="form-control form-control-sm" 
                               value="${dateVal}" min="${minDate}" max="${maxDate}" required 
                               style="border-radius: 8px;">
                    </div>
                    <div class="col-4">
                        <select name="date_packages[]" class="form-select form-select-sm" style="border-radius: 8px;">
                            <option value="Z" ${packageVal === 'Z' ? 'selected' : ''}>Paket Z</option>
                            <option value="A" ${packageVal === 'A' ? 'selected' : ''}>Paket A</option>
                            <option value="B" ${packageVal === 'B' ? 'selected' : ''}>Paket B</option>
                            <option value="C" ${packageVal === 'C' ? 'selected' : ''}>Paket C</option>
                            <option value="D" ${packageVal === 'D' ? 'selected' : ''}>Paket D</option>
                            <option value="Checkpoint" ${packageVal === 'Checkpoint' ? 'selected' : ''}>Checkpoint</option>
                            <option value="Korektif" ${packageVal === 'Korektif' ? 'selected' : ''}>Korektif</option>
                        </select>
                    </div>
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-manual-date" style="border-radius: 8px; padding: 4px 8px;">
                            <i class="mdi mdi-delete-outline"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#manualDynamicDateRows').append(rowHtml);
        }

        function addDateRow(dateVal = '', packageVal = 'A') {
            const yearStr = currentTahun;
            const monthStr = String($('#editBulan').val() || 1).padStart(2, '0');
            
            const lastDay = new Date(yearStr, monthStr, 0).getDate();
            const minDate = `${yearStr}-${monthStr}-01`;
            const maxDate = `${yearStr}-${monthStr}-${String(lastDay).padStart(2, '0')}`;
            
            const rowHtml = `
                <div class="row align-items-center mb-2 date-row">
                    <div class="col-6">
                        <input type="date" name="dates[]" class="form-control form-control-sm" 
                               value="${dateVal}" min="${minDate}" max="${maxDate}" required 
                               style="border-radius: 8px;">
                    </div>
                    <div class="col-4">
                        <select name="date_packages[]" class="form-select form-select-sm" style="border-radius: 8px;">
                            <option value="Z" ${packageVal === 'Z' ? 'selected' : ''}>Paket Z</option>
                            <option value="A" ${packageVal === 'A' ? 'selected' : ''}>Paket A</option>
                            <option value="B" ${packageVal === 'B' ? 'selected' : ''}>Paket B</option>
                            <option value="C" ${packageVal === 'C' ? 'selected' : ''}>Paket C</option>
                            <option value="D" ${packageVal === 'D' ? 'selected' : ''}>Paket D</option>
                            <option value="Checkpoint" ${packageVal === 'Checkpoint' ? 'selected' : ''}>Checkpoint</option>
                            <option value="Korektif" ${packageVal === 'Korektif' ? 'selected' : ''}>Korektif</option>
                        </select>
                    </div>
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-date" style="border-radius: 8px; padding: 4px 8px;">
                            <i class="mdi mdi-delete-outline"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#dynamicDateRows').append(rowHtml);
        }

        /* Update manual form plans based on cached plans */
        function updateManualFormPlans() {
            const mesinId = $('#manualMesinId').val();
            const bulanNum = $('#manualBulan').val();
            const isDateBased = (currentJenis === 'Electric Engine' || currentJenis === 'Diesel Engine');

            if (isDateBased) {
                $('#manualWeeklyContainer').addClass('d-none');
                $('#manualDateContainer').removeClass('d-none');
                $('#manualDynamicDateRows').empty();

                if (mesinId && bulanNum && loadedPlans[mesinId] && loadedPlans[mesinId][bulanNum]) {
                    const plans = loadedPlans[mesinId][bulanNum];
                    let count = 0;
                    plans.forEach(plan => {
                        if (plan.tanggal) {
                            addManualDateRow(plan.tanggal, plan.paket);
                            count++;
                        }
                    });
                    if (count === 0) {
                        addManualDateRow('', 'A');
                    }
                } else {
                    addManualDateRow('', 'A');
                }
            } else {
                $('#manualWeeklyContainer').removeClass('d-none');
                $('#manualDateContainer').addClass('d-none');
                
                // Reset all to none
                $('.manual-week-select').val('none');

                if (mesinId && bulanNum && loadedPlans[mesinId] && loadedPlans[mesinId][bulanNum]) {
                    const plans = loadedPlans[mesinId][bulanNum];
                    plans.forEach(plan => {
                        $(`#manualWeek${plan.minggu_ke}`).val(plan.paket.toUpperCase().trim());
                    });
                }
            }
        }

        /* Load matrix data via jQuery AJAX */
        function loadMatrixData() {
            const wrapper = $('#matrixTableWrapper');

            // Show loading state
            wrapper.html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-dark mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-secondary small fw-semibold">Memuat preview agenda...</div>
                </div>
            `);

            // Update year and jenis badges
            $('#badgeJenis').text(currentJenis);
            $('#badgeTahun').text(currentTahun);

            // Sync current year and jenis MTC to manual input form
            $('#manualTahun').val(currentTahun);
            $('#manualJenisMtc').val(currentJenis);

            $.ajax({
                url: "{{ route('agenda.master.data') }}",
                type: "GET",
                data: {
                    jenis_mtc: currentJenis,
                    tahun: currentTahun
                },
                success: function(res) {
                    // Cache the loaded data
                    loadedPlans = res.plans || {};
                    loadedMachines = res.machines || [];

                    // Populate manual machine dropdown
                    const manualMesinSelect = $('#manualMesinId');
                    manualMesinSelect.empty();
                    manualMesinSelect.append('<option value="">-- Pilih Mesin --</option>');
                    if (loadedMachines && loadedMachines.length > 0) {
                        loadedMachines.forEach(m => {
                            manualMesinSelect.append(
                                `<option value="${m.id}">${m.nama_mesin} (${m.kode_mesin || '—'})</option>`
                            );
                        });
                    }

                    // Reset manual plans
                    updateManualFormPlans();

                    // Calculate total agenda count from loaded plans
                    let totalAgendaCount = 0;
                    if (res.plans) {
                        Object.keys(res.plans).forEach(machineId => {
                            Object.keys(res.plans[machineId]).forEach(monthNum => {
                                const monthPlans = res.plans[machineId][monthNum];
                                if (monthPlans) {
                                    totalAgendaCount += monthPlans.length;
                                }
                            });
                        });
                    }
                    $('#badgeTotalAgenda').text(totalAgendaCount);

                    if (!res.status || !res.machines || res.machines.length === 0) {
                        $('#badgeTotalAgenda').text('0');
                        wrapper.html(`
                            <div class="text-center py-5">
                                <div class="fs-1 mb-3 opacity-40">📅</div>
                                <h5 class="fw-bold text-secondary">Belum Ada Master Agenda Plan</h5>
                                <p class="text-muted small mx-auto mb-0" style="max-width: 400px;">
                                    Master agenda perawatan untuk jenis <strong>${currentJenis}</strong> tahun <strong>${currentTahun}</strong> belum tersedia. Silakan gunakan form upload di atas.
                                </p>
                            </div>
                        `);
                        return;
                    }

                    // Construct Table
                    let html = `
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle planner-table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th style="min-width: 150px; text-align: left;">Nama Mesin</th>
                                        <th style="width: 100px;">Kode Mesin</th>
                                        <th>JAN</th><th>FEB</th><th>MAR</th><th>APR</th><th>MEI</th><th>JUN</th>
                                        <th>JUL</th><th>AGT</th><th>SEP</th><th>OKT</th><th>NOV</th><th>DES</th>
                                        <th style="width: 80px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    res.machines.forEach((m, idx) => {
                        html += `
                            <tr>
                                <td class="text-center text-secondary fw-semibold">${idx + 1}</td>
                                <td>
                                    <div class="fw-bold text-dark">${m.nama_mesin}</div>
                                    ${m.lokasi ? `<div class="text-muted small mt-1" style="font-size: 11px;">📍 ${m.lokasi}</div>` : ''}
                                </td>
                                <td class="text-start font-monospace">
                                    <span class="badge badge-soft-primary border border-secondary-subtle px-2 py-1" style="font-size: 11px;">
                                        ${m.kode_mesin || '—'}
                                    </span>
                                </td>
                        `;

                        // Generate month cells
                        for (let monthNum = 1; monthNum <= 12; monthNum++) {
                            const monthPlans = (res.plans[m.id] && res.plans[m.id][monthNum]) ? res
                                .plans[m.id][monthNum] : [];
                            const serializedPlans = JSON.stringify(monthPlans).replace(/'/g, "&apos;");

                            html += `
                                <td class="planner-cell" 
                                    data-mesin-id="${m.id}" 
                                    data-mesin-nama="${m.nama_mesin}" 
                                    data-bulan-num="${monthNum}" 
                                    data-bulan-nama="${getMonthName(monthNum)}" 
                                    data-plans='${serializedPlans}'>
                            `;

                            if (monthPlans && monthPlans.length > 0) {
                                html += '<div class="d-flex flex-column gap-1">';
                                monthPlans.forEach(plan => {
                                    let pkgClass = 'pkg-other';
                                    const cleanPkg = plan.paket.toUpperCase().trim();
                                    if (cleanPkg === 'Z') pkgClass = 'pkg-z';
                                    else if (cleanPkg === 'A') pkgClass = 'pkg-a';
                                    else if (cleanPkg === 'B') pkgClass = 'pkg-b';
                                    else if (cleanPkg === 'C') pkgClass = 'pkg-c';
                                    else if (cleanPkg === 'D') pkgClass = 'pkg-d';

                                    let labelText = '';
                                    if (plan.tanggal) {
                                        const dayNum = plan.tanggal.substring(8, 10);
                                        labelText = `Tgl ${dayNum}`;
                                    } else {
                                        labelText = `M${plan.minggu_ke}`;
                                    }

                                    html += `
                                        <span class="planner-badge ${pkgClass} px-2 py-1">
                                            <span class="me-3">${labelText}</span>
                                            <span class="fw-bold fs-7">${plan.paket}</span>
                                        </span>
                                    `;
                                });
                                html += '</div>';
                            } else {
                                html += '<div class="text-center text-muted">—</div>';
                            }
                            html += '</td>';
                        }

                        // Add action buttons column (Clear Machine Agenda)
                        html += `
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-xs btn-clear-machine-agenda" 
                                        data-mesin-id="${m.id}" 
                                        data-mesin-nama="${m.nama_mesin}" 
                                        style="border-radius: 6px; padding: 2px 8px; font-size: 11px; font-weight: 600; white-space: nowrap;"
                                        title="Hapus seluruh agenda untuk mesin ini di tahun ${currentTahun}">
                                        <i class="mdi mdi-delete-sweep-outline"></i> Clear
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    wrapper.html(html);
                },
                error: function() {
                    wrapper.html(`
                        <div class="text-center py-5 text-danger small">
                            <div class="fs-2 mb-2">⚠️</div>
                            <strong>Error!</strong> Gagal mengambil data preview agenda dari server. Silakan coba muat ulang halaman.
                        </div>
                    `);
                }
            });
        }

        $(document).ready(function() {
            // Load initial matrix preview
            loadMatrixData();

            // Clear Machine Agenda click handler
            $(document).on('click', '.btn-clear-machine-agenda', function() {
                const mesinId = $(this).data('mesin-id');
                const mesinNama = $(this).data('mesin-nama');

                Swal.fire({
                    title: 'Hapus Agenda?',
                    html: `Apakah Anda yakin ingin menghapus seluruh agenda perawatan untuk mesin <strong>${mesinNama}</strong> pada tahun <strong>${currentTahun}</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('agenda.master.clear-machine') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                mesin_id: mesinId,
                                tahun: currentTahun
                            },
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: res.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    loadMatrixData(); // Reload table
                                } else {
                                    Swal.fire('Gagal!', res.message, 'error');
                                }
                            },
                            error: function(err) {
                                let msg = 'Gagal menghapus agenda.';
                                if (err.responseJSON && err.responseJSON.message) {
                                    msg = err.responseJSON.message;
                                }
                                Swal.fire('Error!', msg, 'error');
                            }
                        });
                    }
                });
            });

            // MTC Chips click handler (Table preview filter)
            $('#filterChipsContainer').on('click', '.filter-chip', function() {
                $('.filter-chip').removeClass('active-chip');
                $(this).addClass('active-chip');

                currentJenis = $(this).data('jenis');
                loadMatrixData();
            });

            // Year select handler (Table preview filter)
            $('#tahunSelect').on('change', function() {
                currentTahun = $(this).val();
                loadMatrixData();
            });

            // Manual form listeners to auto-fill existing weeks/dates
            $('#manualMesinId, #manualBulan, #manualTahun, #manualJenisMtc').on('change', function() {
                updateManualFormPlans();
            });

            // Sync manual jenis MTC change back to global filters and matrix
            $('#manualJenisMtc').on('change', function() {
                const selectedJenis = $(this).val();
                if (selectedJenis !== currentJenis) {
                    currentJenis = selectedJenis;
                    // Update main chips visual state
                    $('.filter-chip').removeClass('active-chip');
                    $(`.filter-chip[data-jenis="${currentJenis}"]`).addClass('active-chip');
                    loadMatrixData();
                }
            });

            // Sync manual tahun change back to global filters and matrix
            $('#manualTahun').on('change', function() {
                const selectedYr = $(this).val();
                if (selectedYr !== currentTahun) {
                    currentTahun = selectedYr;
                    $('#tahunSelect').val(currentTahun);
                    loadMatrixData();
                }
            });

            // Submit Manual Agenda Form via AJAX
            $('#manualAgendaForm').on('submit', function(e) {
                e.preventDefault();

                const btnSubmit = $('#btnSubmitManual');
                const feedback = $('#manualFeedback');
                const selectedMesin = $('#manualMesinId').val();

                if (!selectedMesin) {
                    feedback.removeClass('d-none').addClass('alert-danger').html(
                        '<strong>Peringatan:</strong> Pilih mesin terlebih dahulu.');
                    return;
                }

                btnSubmit.prop('disabled', true);
                feedback.addClass('d-none').html('');

                $.ajax({
                    url: "{{ route('agenda.master.save-single') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        btnSubmit.prop('disabled', false);
                        if (res.status) {
                            // Hide the manual input modal
                            $('#manualAgendaModal').modal('hide');

                            // Show Sweetalert success toast
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses!',
                                text: 'Agenda berhasil diperbarui.',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            loadMatrixData(); // reload matrix preview
                        } else {
                            feedback.removeClass('d-none').addClass('alert-danger').html(
                                `<strong>Error!</strong> ${res.message}`);
                        }
                    },
                    error: function(err) {
                        btnSubmit.prop('disabled', false);
                        let msg = 'Gagal menyimpan agenda.';
                        if (err.responseJSON && err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                        feedback.removeClass('d-none').addClass('alert-danger').html(
                            `<strong>Error!</strong> ${msg}`);
                    }
                });
            });

            // Actionable cell click handler (Open Edit Modal)
            $(document).on('click', '.planner-cell', function() {
                const mesinId = $(this).data('mesin-id');
                const mesinNama = $(this).data('mesin-nama');
                const bulanNum = $(this).data('bulan-num');
                const bulanNama = $(this).data('bulan-nama');
                const plans = $(this).data('plans') || [];

                // Populate hidden values
                $('#editMesinId').val(mesinId);
                $('#editTahun').val(currentTahun);
                $('#editBulan').val(bulanNum);

                // Populate details text
                $('#editMesinNamaText').text(mesinNama);
                $('#editPeriodeText').text(bulanNama + ' ' + currentTahun);

                const isDateBased = (currentJenis === 'Electric Engine' || currentJenis === 'Diesel Engine');
                if (isDateBased) {
                    $('#weeklyEditContainer').addClass('d-none');
                    $('#dateEditContainer').removeClass('d-none');
                    $('#dynamicDateRows').empty();

                    if (plans && plans.length > 0) {
                        plans.forEach(plan => {
                            if (plan.tanggal) {
                                addDateRow(plan.tanggal, plan.paket);
                            }
                        });
                    } else {
                        addDateRow('', 'A');
                    }
                } else {
                    $('#weeklyEditContainer').removeClass('d-none');
                    $('#dateEditContainer').addClass('d-none');

                    // Reset selectors to none
                    $('.week-select').val('none');

                    // Map database values back to selectors
                    plans.forEach(plan => {
                        $(`#editWeek${plan.minggu_ke}`).val(plan.paket.toUpperCase().trim());
                    });
                }

                // Clear alerts
                $('#editFeedback').addClass('d-none').html('');

                // Display modal
                $('#editAgendaModal').modal('show');
            });

            // Hapus Semua weekly/date plans handler
            $('#btnHapusSemua').on('click', function() {
                if (confirm(
                        'Apakah Anda yakin ingin menghapus semua agenda perawatan untuk mesin ini di bulan tersebut?'
                    )) {
                    $('.week-select').val('none');
                    $('#dynamicDateRows').empty();
                    $('#editAgendaForm').trigger('submit');
                }
            });

            // Dynamic date row events
            $(document).on('click', '#btnAddDateRow', function() {
                addDateRow('', 'A');
            });

            $(document).on('click', '.btn-remove-date', function() {
                $(this).closest('.date-row').remove();
            });

            $(document).on('click', '#btnManualAddDateRow', function() {
                addManualDateRow('', 'A');
            });

            $(document).on('click', '.btn-remove-manual-date', function() {
                $(this).closest('.manual-date-row').remove();
            });

            // Submit Edit Form via AJAX
            $('#editAgendaForm').on('submit', function(e) {
                e.preventDefault();

                const btnSave = $('#btnSaveSingle');
                const feedback = $('#editFeedback');

                btnSave.prop('disabled', true);
                feedback.addClass('d-none').html('');

                $.ajax({
                    url: "{{ route('agenda.master.save-single') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        btnSave.prop('disabled', false);
                        if (res.status) {
                            $('#editAgendaModal').modal('hide');
                            loadMatrixData(); // dynamic reload matrix preview
                        } else {
                            feedback.removeClass('d-none').addClass('alert-danger').html(
                                `<strong>Error!</strong> ${res.message}`);
                        }
                    },
                    error: function(err) {
                        btnSave.prop('disabled', false);
                        let msg = 'Gagal menyimpan perubahan.';
                        if (err.responseJSON && err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                        feedback.removeClass('d-none').addClass('alert-danger').html(
                            `<strong>Error!</strong> ${msg}`);
                    }
                });
            });

            // AJAX Upload handler
            const uploadForm = document.getElementById('uploadAgendaForm');
            if (uploadForm) {
                uploadForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const btnSubmit = document.getElementById('btnSubmitUpload');
                    const progressContainer = document.getElementById('uploadProgressContainer');
                    const progressBar = document.getElementById('uploadProgressBar');
                    const feedback = document.getElementById('uploadFeedback');

                    const uploadedJenis = $('#uploadJenisMtc').val();
                    const uploadedTahun = $('#uploadTahun').val();

                    // Reset UI
                    feedback.className = 'd-none';
                    feedback.innerHTML = '';
                    progressContainer.classList.remove('d-none');
                    progressBar.style.width = '0%';
                    btnSubmit.disabled = true;

                    const formData = new FormData(this);

                    $.ajax({
                        url: "{{ route('agenda.upload') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        xhr: function() {
                            const xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener("progress", function(evt) {
                                if (evt.lengthComputable) {
                                    const percentComplete = Math.round((evt.loaded / evt
                                        .total) * 100);
                                    progressBar.style.width = percentComplete + '%';
                                }
                            }, false);
                            return xhr;
                        },
                        success: function(res) {
                            progressBar.style.width = '100%';
                            setTimeout(() => {
                                progressContainer.classList.add('d-none');
                                btnSubmit.disabled = false;

                                // Hide the upload modal
                                $('#uploadExcelModal').modal('hide');

                                // Show sweetalert notification
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses Mengunggah!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Reset file input
                                $('#fileExcel').val('');
                                $('#fileNamePlaceholder').text(
                                    "Pilih file excel (.xlsx, .xls)").attr('class',
                                    'small fw-bold text-primary');

                                // Synchronize the table preview filters to display the uploaded plan
                                currentJenis = uploadedJenis;
                                currentTahun = uploadedTahun;

                                // Update chips visual state
                                $('.filter-chip').removeClass('active-chip');
                                $(`.filter-chip[data-jenis="${currentJenis}"]`)
                                    .addClass('active-chip');

                                // Update year select dropdown visual state
                                $('#tahunSelect').val(currentTahun);

                                // Refresh matrix table preview dynamically
                                loadMatrixData();
                            }, 500);
                        },
                        error: function(err) {
                            progressContainer.classList.add('d-none');
                            btnSubmit.disabled = false;

                            let msg = 'Gagal mengupload file.';
                            if (err.responseJSON && err.responseJSON.message) {
                                msg = err.responseJSON.message;
                            }

                            if (err.responseJSON && err.responseJSON.errors) {
                                msg = Object.values(err.responseJSON.errors).join('<br>');
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Mengunggah Agenda',
                                html: `<div style="text-align: left; max-height: 250px; overflow-y: auto; font-size: 13px; line-height: 1.6; padding-right: 5px;">${msg}</div>`,
                                confirmButtonText: 'Tutup',
                                confirmButtonColor: '#0f172a'
                            });
                        }
                    });
                });
            }
        });
    </script>
@endsection
