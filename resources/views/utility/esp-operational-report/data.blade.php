@extends('layouts.app')

@section('title', 'ESP Report - Data')

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
                                        <i class="ri-database-2-line me-2" style="color:#f6c90e;"></i>
                                        ESP Report — Data & Rekap
                                    </h4>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('esp-operational-report.index') }}" class="btn btn-sm btn-light">
                                        <i class="ri-add-line me-1"></i>Input Data
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter & Tab --}}
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-3">
                            <div class="row align-items-center g-2">
                                {{-- Tab Jenis Laporan --}}
                                <div class="col-auto">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary active" id="tab-operational">
                                            <i class="ri-bar-chart-2-line me-1"></i>Operational
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" id="tab-shift">
                                            <i class="ri-file-list-3-line me-1"></i>Shift Report
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" id="tab-coal">
                                            <i class="ri-truck-line me-1"></i>Coal Handover
                                        </button>
                                    </div>
                                </div>

                                <div class="col-auto">
                                    <div class="vr mx-1" style="height:36px;"></div>
                                </div>

                                {{-- Filter Tanggal (Operational & Shift) --}}
                                <div class="col-auto filter-tanggal-single-wrapper">
                                    <label class="form-label mb-0 me-2 fs-13 text-muted">Tanggal</label>
                                </div>
                                <div class="col-auto filter-tanggal-single-wrapper">
                                    <input type="date" class="form-control form-control-sm" id="filter-tanggal"
                                        value="{{ date('Y-m-d') }}">
                                </div>

                                {{-- Filter Range Tanggal (Coal Handover) --}}
                                <div class="col-auto filter-tanggal-range-wrapper" style="display:none;">
                                    <label class="form-label mb-0 me-2 fs-13 text-muted">Range Tanggal</label>
                                </div>
                                <div class="col-auto filter-tanggal-range-wrapper" style="display:none;">
                                    <div class="d-flex align-items-center gap-1">
                                        <input type="date" class="form-control form-control-sm" id="filter-start-date"
                                            value="{{ date('Y-m-d', strtotime('-14 days')) }}">
                                        <span class="text-muted fs-12">s/d</span>
                                        <input type="date" class="form-control form-control-sm" id="filter-end-date"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                {{-- Filter Range Tanggal (Shift Report) --}}
                                <div class="col-auto filter-tanggal-range-shift-wrapper" style="display:none;">
                                    <label class="form-label mb-0 me-2 fs-13 text-muted">Range Tanggal</label>
                                </div>
                                <div class="col-auto filter-tanggal-range-shift-wrapper" style="display:none;">
                                    <div class="d-flex align-items-center gap-1">
                                        <input type="date" class="form-control form-control-sm" id="filter-start-date-shift"
                                            value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                                        <span class="text-muted fs-12">s/d</span>
                                        <input type="date" class="form-control form-control-sm" id="filter-end-date-shift"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                {{-- Filter Grup (hanya operational) --}}
                                <div class="col-auto" id="filter-grup-wrapper">
                                    <select class="form-select form-select-sm" id="filter-grup" style="width:auto;">
                                        <option value="">Semua Grup</option>
                                        <option value="A">Grup A</option>
                                        <option value="B">Grup B</option>
                                        <option value="C">Grup C</option>
                                        <option value="D">Grup D</option>
                                    </select>
                                </div>

                                <div class="col-auto ms-auto">
                                    <button class="btn btn-sm btn-outline-secondary me-1" id="btn-refresh">
                                        <i class="ri-refresh-line me-1"></i>Refresh
                                    </button>
                                    {{-- Export hanya untuk operational --}}
                                    <a href="#" class="btn btn-sm btn-success" id="btn-export">
                                        <i class="ri-file-excel-2-line me-1"></i>Export
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ TABEL OPERATIONAL ═══════════════════════════════════════ --}}
            <div id="section-tbl-operational">
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-bottom">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="ri-table-line me-2 text-primary"></i>
                                    Data Operational Report
                                    <span class="badge bg-primary-subtle text-primary ms-1"
                                        id="badge-count-operational">—</span>
                                </h6>
                            </div>
                            <div class="card-body p-2">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0 text-center align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Jam</th>
                                                <th>Grup</th>
                                                <th>Arus Primer (A)</th>
                                                <th>Arus Sekunder (mA)</th>
                                                <th>Teg. Primer (V)</th>
                                                <th>Teg. Sekunder (kV)</th>
                                                <th>Suhu Thermal (°C)</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-operational">
                                            <tr>
                                                <td colspan="8" class="py-4 text-muted">Memuat data...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ TABEL SHIFT REPORT ══════════════════════════════════════ --}}
            <div id="section-tbl-shift" style="display:none;">
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-bottom">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="ri-file-list-3-line me-2 text-danger"></i>
                                    Data Shift Report
                                </h6>
                            </div>
                            <div class="card-body p-2">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Air (m³)</th>
                                                <th>Steam (ton)</th>
                                                <th>Batubara (ton)</th>
                                                <th>Efisiensi (%)</th>
                                                <th>RH Awal</th>
                                                <th>RH Akhir</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-shift">
                                            <tr>
                                                <td colspan="9" class="py-4 text-muted text-center">Memuat data...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ TABEL COAL HANDOVER ══════════════════════════════════════ --}}
            <div id="section-tbl-coal" style="display:none;">
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-bottom">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="ri-truck-line me-2 text-warning"></i>
                                    Data Serah Terima Batu Bara Gudang
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th rowspan="2" class="text-center align-middle">Tanggal</th>
                                                <th colspan="2" class="text-center bg-light-subtle">Penyuplai
                                                    (Warehouse)</th>
                                                <th colspan="2" class="text-center bg-light-subtle">Penerima (ENG)</th>
                                                <th rowspan="2" class="text-center align-middle">Operator</th>
                                                <th rowspan="2" class="text-center align-middle">Aksi</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">Qty (Ton)</th>
                                                <th class="text-center">NIK / Nama</th>
                                                <th class="text-center">Qty (Ton)</th>
                                                <th class="text-center">NIK / Nama</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-coal">
                                            <tr>
                                                <td colspan="7" class="py-4 text-muted text-center">Memuat data...</td>
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
    </div>

    {{-- ══ MODAL DETAIL OPERATIONAL ════════════════════════════════════ --}}
    <div class="modal fade" id="modal-operational" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom" style="background:linear-gradient(135deg,#4361ee,#3a0ca3);">
                    <h5 class="modal-title text-white fw-semibold">
                        <i class="ri-bar-chart-2-line me-2"></i>Detail Operational — <span id="modal-op-jam"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-sm mb-0">
                        <tbody>
                            <tr>
                                <th class="w-50 text-muted">Jam</th>
                                <td id="d-op-jam">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Grup</th>
                                <td id="d-op-grup">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Arus Primer</th>
                                <td id="d-op-arus-primer">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Arus Sekunder</th>
                                <td id="d-op-arus-sekunder">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tegangan Primer</th>
                                <td id="d-op-teg-primer">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tegangan Sekunder</th>
                                <td id="d-op-teg-sekunder">—</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Suhu Thermal</th>
                                <td id="d-op-suhu">—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MODAL EDIT OPERATIONAL ══════════════════════════════════════ --}}
    @if (auth()->user()->jabatan !== 'operator')
        <div class="modal fade" id="modal-edit-operational" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-semibold">
                            <i class="ri-edit-line me-2 text-primary"></i>Edit Operational — <span
                                id="edit-op-jam-label"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="form-edit-operational">
                        @csrf
                        <input type="hidden" id="edit-op-jam">
                        <input type="hidden" id="edit-op-tanggal">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label fw-medium">Grup</label>
                                    <select class="form-select" id="edit-op-grup" required>
                                        <option value="A">Grup A</option>
                                        <option value="B">Grup B</option>
                                        <option value="C">Grup C</option>
                                        <option value="D">Grup D</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-medium">Arus Primer (A)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-op-arus-primer">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-medium">Arus Sekunder (mA)</label>
                                    <input type="number" step="0.01" class="form-control"
                                        id="edit-op-arus-sekunder">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-medium">Tegangan Primer (V)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-op-teg-primer">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-medium">Tegangan Sekunder (kV)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-op-teg-sekunder">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-medium">Suhu Thermal (°C)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-op-suhu">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="btn-save-edit-operational">
                                <i class="ri-save-line me-1"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ══ MODAL DETAIL SHIFT ═══════════════════════════════════════════ --}}
    <div class="modal fade" id="modal-shift" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom" style="background:linear-gradient(135deg,#f72585,#7209b7);">
                    <h5 class="modal-title text-white fw-semibold">
                        <i class="ri-file-list-3-line me-2"></i>Detail Shift Report — <span id="modal-shift-tgl"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted border-bottom pb-2">Pemakaian</h6>
                            <table class="table table-sm table-bordered mb-0">
                                <tr>
                                    <th class="text-muted">Air</th>
                                    <td id="d-sh-air">—</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Steam</th>
                                    <td id="d-sh-steam">—</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Batubara</th>
                                    <td id="d-sh-batubara">—</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Efisiensi Batubara</th>
                                    <td id="d-sh-efisiensi">—</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Pengisian Batubara</th>
                                    <td id="d-sh-pengisian">—</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted border-bottom pb-2">Running Hour & Feed Tank</h6>
                            <table class="table table-sm table-bordered mb-0">
                                <tr>
                                    <th class="text-muted">RH Awal</th>
                                    <td id="d-sh-rh-awal">—</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">RH Akhir</th>
                                    <td id="d-sh-rh-akhir">—</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Feed Tank Awal</th>
                                    <td id="d-sh-ft-awal">—</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Feed Tank Akhir</th>
                                    <td id="d-sh-ft-akhir">—</td>
                                </tr>
                            </table>
                            <h6 class="fw-semibold text-muted border-bottom pb-2 mt-3">Chemical</h6>
                            <table class="table table-sm table-bordered mb-0">
                                <tr>
                                    <th class="text-muted">Chemical SCF</th>
                                    <td id="d-sh-scf">—</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Chemical SRTF</th>
                                    <td id="d-sh-srtf">—</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Dosis</th>
                                    <td id="d-sh-dosis">—</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-12">
                            <h6 class="fw-semibold text-muted border-bottom pb-2">Status Approval</h6>
                            <div id="approval-timeline" class="d-flex align-items-center gap-3 flex-wrap"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MODAL DETAIL COAL HANDOVER ══════════════════════════════════ --}}
    <div class="modal fade" id="modal-coal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom" style="background:linear-gradient(135deg,#f6d365,#fda085);">
                    <h5 class="modal-title text-dark fw-bold">
                        <i class="ri-truck-line me-2"></i>Detail Serah Terima Batu Bara — <span
                            id="modal-coal-tgl"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-primary border-bottom pb-2">1. Penyuplai (Warehouse)</h6>
                            <table class="table table-sm table-bordered mb-0">
                                <tr>
                                    <th class="text-muted w-50">Qty (Ton)</th>
                                    <td id="d-coal-penyuplai-qty">—</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">NIK / Nama</th>
                                    <td id="d-coal-penyuplai-name">—</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-success border-bottom pb-2">2. Penerima (ENG)</h6>
                            <table class="table table-sm table-bordered mb-0">
                                <tr>
                                    <th class="text-muted w-50">Qty (Ton)</th>
                                    <td id="d-coal-penerima-qty">—</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">NIK / Nama</th>
                                    <td id="d-coal-penerima-name">—</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-12 mt-2">
                            <span class="text-muted fs-12">Disubmit oleh: </span><span class="fw-semibold fs-12"
                                id="d-coal-operator">—</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ══ MODAL EDIT SHIFT ════════════════════════════════════════════ --}}
    @if (auth()->user()->jabatan !== 'operator')
        <div class="modal fade" id="modal-edit-shift" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-semibold">
                            <i class="ri-edit-line me-2 text-danger"></i>Edit Shift Report — <span
                                id="edit-sh-tgl-label"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="form-edit-shift">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit-sh-id">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Pemakaian Air (m³)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-sh-air">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Pemakaian Steam (ton)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-sh-steam">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Pemakaian Batubara (ton)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-sh-batubara">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Efisiensi Batubara (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-sh-efisiensi">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Pengisian Batubara (ton)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-sh-pengisian">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">RH Awal (jam)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-sh-rh-awal">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">RH Akhir (jam)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-sh-rh-akhir">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Feed Tank Awal (m³)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-sh-ft-awal">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Feed Tank Akhir (m³)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-sh-ft-akhir">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Chemical SCF (L)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-sh-scf">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Chemical SRTF (L)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-sh-srtf">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Dosis (ppm)</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-sh-dosis">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="ri-save-line me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ══ MODAL EDIT COAL HANDOVER ════════════════════════════════════ --}}
        <div class="modal fade" id="modal-edit-coal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-semibold">
                            <i class="ri-edit-line me-2 text-warning"></i>Edit Serah Terima Batu Bara — <span
                                id="edit-coal-tgl-label"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="form-edit-coal">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit-coal-id">
                        <div class="modal-body">
                            <div class="row g-4">
                                {{-- PENYUPLAI --}}
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 bg-light">
                                        <h6 class="fw-semibold text-primary mb-3">1. Penyuplai (Warehouse)</h6>
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Qty (Ton)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                id="edit-coal-penyuplai-qty" required>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label fw-medium">NIK / Nama</label>
                                            <input type="text" class="form-control" id="edit-coal-penyuplai-name"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                {{-- PENERIMA --}}
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 bg-light">
                                        <h6 class="fw-semibold text-success mb-3">2. Penerima (ENG)</h6>
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Qty (Ton)</label>
                                            <input type="number" step="0.01" class="form-control"
                                                id="edit-coal-penerima-qty" required>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label fw-medium">NIK / Nama</label>
                                            <input type="text" class="form-control" id="edit-coal-penerima-name"
                                                required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning btn-sm text-dark fw-medium">
                                <i class="ri-save-line me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script>
        $(function() {

            let activeTab = 'operational';
            let currentOpRow = null; // simpan row yang sedang dibuka modal
            let currentCoalRow = null;

            // Helper to format decimals: removes trailing zeros (e.g. 12.00 -> 12, 12.50 -> 12.5)
            function formatDecimalVal(val) {
                if (val === null || val === undefined || val === '') return '';
                const num = parseFloat(val);
                return isNaN(num) ? val : num;
            }

            function formatDecimalDisplay(val, unit = '') {
                if (val === null || val === undefined || val === '') return '—';
                const num = parseFloat(val);
                if (isNaN(num)) return val + (unit ? ' ' + unit : '');
                return num + (unit ? ' ' + unit : '');
            }

            // ── TAB TOGGLE ───────────────────────────────────────────────────
            $('#tab-operational').on('click', function() {
                activeTab = 'operational';
                $(this).addClass('active btn-primary').removeClass('btn-outline-primary');
                $('#tab-shift').addClass('btn-outline-primary').removeClass('active btn-primary');
                $('#tab-coal').addClass('btn-outline-primary').removeClass('active btn-primary');
                $('#section-tbl-operational').show();
                $('#section-tbl-shift').hide();
                $('#section-tbl-coal').hide();
                $('#filter-grup-wrapper').show();
                $('.filter-tanggal-single-wrapper').show();
                $('.filter-tanggal-range-wrapper').hide();
                $('.filter-tanggal-range-shift-wrapper').hide();
                $('#btn-export').show();
                loadOperational();
            });

            $('#tab-shift').on('click', function() {
                activeTab = 'shift';
                $(this).addClass('active btn-primary').removeClass('btn-outline-primary');
                $('#tab-operational').addClass('btn-outline-primary').removeClass('active btn-primary');
                $('#tab-coal').addClass('btn-outline-primary').removeClass('active btn-primary');
                $('#section-tbl-operational').hide();
                $('#section-tbl-shift').show();
                $('#section-tbl-coal').hide();
                $('#filter-grup-wrapper').hide();
                $('.filter-tanggal-single-wrapper').hide();
                $('.filter-tanggal-range-wrapper').hide();
                $('.filter-tanggal-range-shift-wrapper').show();
                $('#btn-export').hide();
                loadShift();
            });

            $('#tab-coal').on('click', function() {
                activeTab = 'coal';
                $(this).addClass('active btn-primary').removeClass('btn-outline-primary');
                $('#tab-operational').addClass('btn-outline-primary').removeClass('active btn-primary');
                $('#tab-shift').addClass('btn-outline-primary').removeClass('active btn-primary');
                $('#section-tbl-operational').hide();
                $('#section-tbl-shift').hide();
                $('#section-tbl-coal').show();
                $('#filter-grup-wrapper').hide();
                $('.filter-tanggal-single-wrapper').hide();
                $('.filter-tanggal-range-wrapper').show();
                $('.filter-tanggal-range-shift-wrapper').hide();
                $('#btn-export').hide();
                loadCoal();
            });

            // ── FILTER & REFRESH ─────────────────────────────────────────────
            $('#filter-tanggal, #filter-grup').on('change', function() {
                if (activeTab === 'operational') loadOperational();
            });
            $('#filter-start-date-shift, #filter-end-date-shift').on('change', function() {
                if (activeTab === 'shift') loadShift();
            });
            $('#filter-start-date, #filter-end-date').on('change', function() {
                if (activeTab === 'coal') loadCoal();
            });
            $('#btn-refresh').on('click', function() {
                if (activeTab === 'operational') loadOperational();
                else if (activeTab === 'shift') loadShift();
                else if (activeTab === 'coal') loadCoal();
            });

            // ── EXPORT ───────────────────────────────────────────────────────
            $('#btn-export').on('click', function(e) {
                e.preventDefault();
                const tanggal = $('#filter-tanggal').val();
                const grup = $('#filter-grup').val();
                let url = '{{ route('esp-operational-report.export') }}?tanggal=' + tanggal;
                if (grup) url += '&grup=' + grup;
                window.location.href = url;
            });

            // ── LOAD OPERATIONAL ─────────────────────────────────────────────
            function statusBadgeOp(r) {
                return r.arus_primer !== null ?
                    '<span class="badge bg-success-subtle text-success">Ada</span>' :
                    '<span class="badge bg-light text-muted">Kosong</span>';
            }

            function loadOperational() {
                const tanggal = $('#filter-tanggal').val();
                const grup = $('#filter-grup').val();
                $('#tbody-operational').html(
                    '<tr><td colspan="8" class="text-center py-4 text-muted"><i class="ri-loader-4-line"></i> Memuat...</td></tr>'
                );
                $.get('{{ route('esp-operational-report.json') }}', {
                    tanggal,
                    grup
                }, function(data) {
                    const filled = data.filter(r => r.arus_primer !== null);
                    $('#badge-count-operational').text(filled.length + ' data');
                    let html = '';
                    data.forEach(function(r) {
                        const hasData = r.arus_primer !== null;
                        html += `
                            <tr class="${hasData ? '' : 'text-muted opacity-50'}" style="cursor:${hasData?'pointer':'default'};"
                                data-row='${JSON.stringify(r)}'>
                                <td class="fw-medium">${r.jam}</td>
                                <td>${r.grup ? '<span class="badge bg-primary-subtle text-primary">'+r.grup+'</span>' : '—'}</td>
                                <td>${formatDecimalVal(r.arus_primer) || '—'}</td>
                                <td>${formatDecimalVal(r.arus_sekunder) || '—'}</td>
                                <td>${formatDecimalVal(r.tegangan_primer) || '—'}</td>
                                <td>${formatDecimalVal(r.tegangan_sekunder) || '—'}</td>
                                <td>${formatDecimalVal(r.suhu_thermal) || '—'}</td>
                                <td>
                                    ${hasData
                                        ? `<button class="btn btn-xs btn-outline-primary btn-detail-op py-0 px-2" title="Detail">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            @if (auth()->user()->jabatan !== 'operator')
                                                <button type="button" class="btn btn-xs btn-outline-primary btn-edit-op-row py-0 px-2" title="Edit">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                            @endif`
                                        : '—'
                                    }
                                </td>
                            </tr>`;
                    });
                    $('#tbody-operational').html(html ||
                        '<tr><td colspan="8" class="text-center py-4 text-muted">Belum ada data</td></tr>'
                    );
                });
            }

            // ── LOAD SHIFT ───────────────────────────────────────────────────
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

            function loadShift() {
                const startDate = $('#filter-start-date-shift').val();
                const endDate = $('#filter-end-date-shift').val();
                $('#tbody-shift').html(
                    '<tr><td colspan="9" class="text-center py-4 text-muted"><i class="ri-loader-4-line"></i> Memuat...</td></tr>'
                );
                $.get('{{ route('esp-shift-report.json') }}', {
                    start_date: startDate,
                    end_date: endDate
                }, function(data) {
                    if (!data.length) {
                        $('#tbody-shift').html(
                            '<tr><td colspan="9" class="text-center py-4 text-muted">Belum ada data shift report</td></tr>'
                        );
                        return;
                    }
                    let html = '';
                    data.forEach(function(r) {
                        const s = statusMap[r.status] ?? {
                            label: r.status,
                            cls: 'bg-secondary text-white'
                        };
                        html += `<tr style="cursor:pointer;" data-row='${JSON.stringify(r)}'>
                    <td class="fw-medium">${r.tanggal_laporan}</td>
                    <td>${formatDecimalVal(r.pemakaian_air) || '—'}</td>
                    <td>${formatDecimalVal(r.pemakaian_steam) || '—'}</td>
                    <td>${formatDecimalVal(r.pemakaian_batubara) || '—'}</td>
                    <td>${formatDecimalVal(r.efisiensi_batubara) || '—'}</td>
                    <td>${formatDecimalVal(r.running_hour_awal) || '—'}</td>
                    <td>${formatDecimalVal(r.running_hour_akhir) || '—'}</td>
                    <td><span class="badge ${s.cls}">${s.label}</span></td>
                    <td>
                        <button class="btn btn-xs btn-outline-danger btn-detail-shift py-0 px-2" title="Detail">
                            <i class="ri-eye-line"></i>
                        </button>
                        @if (auth()->user()->jabatan !== 'operator')
                            <button type="button" class="btn btn-xs btn-outline-primary btn-edit-shift-row py-0 px-2" title="Edit">
                                <i class="ri-edit-line"></i>
                            </button>
                        @endif
                    </td>
                </tr>`;
                    });
                    $('#tbody-shift').html(html);
                });
            }

            // ── LOAD COAL ───────────────────────────────────────────────────
            function loadCoal() {
                const startDate = $('#filter-start-date').val();
                const endDate = $('#filter-end-date').val();
                $('#tbody-coal').html(
                    '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="ri-loader-4-line"></i> Memuat...</td></tr>'
                );
                $.get('{{ route('esp-coal-handover.json') }}', {
                    start_date: startDate,
                    end_date: endDate
                }, function(data) {
                    if (!data.length) {
                        $('#tbody-coal').html(
                            '<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data serah terima batubara</td></tr>'
                        );
                        return;
                    }
                    let html = '';
                    data.forEach(function(r) {
                        const operatorName = r.operator?.username ?? '—';
                        html += `<tr style="cursor:pointer;" data-row='${JSON.stringify(r)}'>
                    <td class="fw-medium">${r.tanggal_laporan}</td>
                    <td>${formatDecimalVal(r.penyuplai_qty) || '—'}</td>
                    <td>${r.penyuplai_nik_nama ?? '—'}</td>
                    <td>${formatDecimalVal(r.penerima_qty) || '—'}</td>
                    <td>${r.penerima_nik_nama ?? '—'}</td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary">
                            <i class="ri-user-line me-1"></i>${operatorName}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-xs btn-outline-warning btn-detail-coal py-0 px-2 text-dark" title="Detail">
                            <i class="ri-eye-line"></i>
                        </button>
                        @if (auth()->user()->jabatan !== 'operator')
                            <button type="button" class="btn btn-xs btn-outline-primary btn-edit-coal-row py-0 px-2" title="Edit">
                                <i class="ri-edit-line"></i>
                            </button>
                        @endif
                    </td>
                </tr>`;
                    });
                    $('#tbody-coal').html(html);
                });
            }

            // ── MODAL DETAIL OPERATIONAL ─────────────────────────────────────
            $(document).on('click', '.btn-detail-op', function(e) {
                e.stopPropagation();
                const r = $(this).closest('tr').data('row');
                currentOpRow = r;
                $('#modal-op-jam').text(r.jam);
                $('#d-op-jam').text(r.jam);
                $('#d-op-grup').text(r.grup ?? '—');
                $('#d-op-arus-primer').text(formatDecimalDisplay(r.arus_primer, 'A'));
                $('#d-op-arus-sekunder').text(formatDecimalDisplay(r.arus_sekunder, 'mA'));
                $('#d-op-teg-primer').text(formatDecimalDisplay(r.tegangan_primer, 'V'));
                $('#d-op-teg-sekunder').text(formatDecimalDisplay(r.tegangan_sekunder, 'kV'));
                $('#d-op-suhu').text(formatDecimalDisplay(r.suhu_thermal, '°C'));
                new bootstrap.Modal('#modal-operational').show();
            });

            // Buka edit operational langsung dari baris tabel
            $(document).on('click', '.btn-edit-op-row', function(e) {
                e.stopPropagation();
                const r = $(this).closest('tr').data('row');
                currentOpRow = r;
                $('#edit-op-jam-label').text(r.jam);
                $('#edit-op-jam').val(r.jam);
                $('#edit-op-tanggal').val($('#filter-tanggal').val());
                $('#edit-op-grup').val(r.grup);
                $('#edit-op-arus-primer').val(formatDecimalVal(r.arus_primer));
                $('#edit-op-arus-sekunder').val(formatDecimalVal(r.arus_sekunder));
                $('#edit-op-teg-primer').val(formatDecimalVal(r.tegangan_primer));
                $('#edit-op-teg-sekunder').val(formatDecimalVal(r.tegangan_sekunder));
                $('#edit-op-suhu').val(formatDecimalVal(r.suhu_thermal));
                new bootstrap.Modal('#modal-edit-operational').show();
            });

            // Submit edit operational (reuse route store — updateOrCreate)
            $('#form-edit-operational').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btn-save-edit-operational');
                $btn.prop('disabled', true).html('<i class="ri-loader-4-line me-1"></i>Menyimpan...');
                $.ajax({
                    url: '{{ route('esp-operational-report.store') }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        jam_laporan: $('#edit-op-jam').val(),
                        grup: $('#edit-op-grup').val(),
                        arus_primer: $('#edit-op-arus-primer').val(),
                        arus_sekunder: $('#edit-op-arus-sekunder').val(),
                        tegangan_primer: $('#edit-op-teg-primer').val(),
                        tegangan_sekunder: $('#edit-op-teg-sekunder').val(),
                        suhu_thermal: $('#edit-op-suhu').val(),
                    },
                    success: function() {
                        toastr.success('Data berhasil diperbarui');
                        bootstrap.Modal.getInstance('#modal-edit-operational').hide();
                        loadOperational();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message ?? 'Gagal menyimpan');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(
                            '<i class="ri-save-line me-1"></i>Simpan');
                    }
                });
            });

            // ── MODAL DETAIL SHIFT ────────────────────────────────────────────
            let currentShiftRow = null;

            $(document).on('click', '.btn-detail-shift', function(e) {
                e.stopPropagation();
                const r = $(this).closest('tr').data('row');
                currentShiftRow = r;
                $('#modal-shift-tgl').text(r.tanggal_laporan);
                $('#d-sh-air').text(formatDecimalDisplay(r.pemakaian_air, 'm³'));
                $('#d-sh-steam').text(formatDecimalDisplay(r.pemakaian_steam, 'ton'));
                $('#d-sh-batubara').text(formatDecimalDisplay(r.pemakaian_batubara, 'ton'));
                $('#d-sh-efisiensi').text(formatDecimalDisplay(r.efisiensi_batubara, '%'));
                $('#d-sh-pengisian').text(formatDecimalDisplay(r.pengisian_batubara, 'ton'));
                $('#d-sh-rh-awal').text(formatDecimalDisplay(r.running_hour_awal, 'jam'));
                $('#d-sh-rh-akhir').text(formatDecimalDisplay(r.running_hour_akhir, 'jam'));
                $('#d-sh-ft-awal').text(formatDecimalDisplay(r.feed_tank_awal, 'm³'));
                $('#d-sh-ft-akhir').text(formatDecimalDisplay(r.feed_tank_akhir, 'm³'));
                $('#d-sh-scf').text(formatDecimalDisplay(r.chemical_scf, 'L'));
                $('#d-sh-srtf').text(formatDecimalDisplay(r.chemical_srtf, 'L'));
                $('#d-sh-dosis').text(formatDecimalDisplay(r.dosis, 'ppm'));

                // Timeline approval
                const steps = [{
                        key: 'approved_operator',
                        label: 'Operator',
                        icon: 'ri-user-line'
                    },
                    {
                        key: 'approved_foreman',
                        label: 'Foreman',
                        icon: 'ri-user-star-line'
                    },
                    {
                        key: 'approved_supervisor',
                        label: 'Supervisor',
                        icon: 'ri-shield-check-line'
                    },
                ];
                const order = ['approved_operator', 'approved_foreman', 'approved_supervisor'];
                const currentIdx = order.indexOf(r.status);
                let tl = '';
                steps.forEach(function(s, i) {
                    const done = i <= currentIdx;
                    tl +=
                        `<div class="d-flex flex-column align-items-center gap-1">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                    style="width:40px;height:40px;
                        background:${done ? 'var(--vz-success)' : 'var(--vz-light)'};
                        color:${done ? '#fff' : 'var(--vz-secondary)'};">
                    <i class="${s.icon} fs-18"></i>
                </div>
                <small class="text-muted">${s.label}</small>
                <span class="badge ${done ? 'bg-success' : 'bg-light text-muted'} fs-10">
                    ${done ? 'Approved' : 'Pending'}
                </span>
            </div>
            ${i < steps.length-1 ? '<div class="border-top flex-grow-1 mt-3" style="width:40px;border-width:2px!important;'+(done && i<currentIdx?'border-color:var(--vz-success)!important':'')+'"></div>' : ''}`;
                });
                $('#approval-timeline').html(tl);
                new bootstrap.Modal('#modal-shift').show();
            });

            // Buka edit shift langsung dari baris tabel
            $(document).on('click', '.btn-edit-shift-row', function(e) {
                e.stopPropagation();
                const r = $(this).closest('tr').data('row');
                currentShiftRow = r;
                $('#edit-sh-tgl-label').text(r.tanggal_laporan);
                $('#edit-sh-id').val(r.id);
                $('#edit-sh-air').val(formatDecimalVal(r.pemakaian_air));
                $('#edit-sh-steam').val(formatDecimalVal(r.pemakaian_steam));
                $('#edit-sh-batubara').val(formatDecimalVal(r.pemakaian_batubara));
                $('#edit-sh-efisiensi').val(formatDecimalVal(r.efisiensi_batubara));
                $('#edit-sh-pengisian').val(formatDecimalVal(r.pengisian_batubara));
                $('#edit-sh-rh-awal').val(formatDecimalVal(r.running_hour_awal));
                $('#edit-sh-rh-akhir').val(formatDecimalVal(r.running_hour_akhir));
                $('#edit-sh-ft-awal').val(formatDecimalVal(r.feed_tank_awal));
                $('#edit-sh-ft-akhir').val(formatDecimalVal(r.feed_tank_akhir));
                $('#edit-sh-scf').val(formatDecimalVal(r.chemical_scf));
                $('#edit-sh-srtf').val(formatDecimalVal(r.chemical_srtf));
                $('#edit-sh-dosis').val(formatDecimalVal(r.dosis));
                new bootstrap.Modal('#modal-edit-shift').show();
            });

            // Submit edit shift
            $('#form-edit-shift').on('submit', function(e) {
                e.preventDefault();
                const id = $('#edit-sh-id').val();
                $.ajax({
                    url: `/utility/esp-shift-report/${id}`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'PUT',
                        pemakaian_air: $('#edit-sh-air').val(),
                        pemakaian_steam: $('#edit-sh-steam').val(),
                        pemakaian_batubara: $('#edit-sh-batubara').val(),
                        efisiensi_batubara: $('#edit-sh-efisiensi').val(),
                        pengisian_batubara: $('#edit-sh-pengisian').val(),
                        running_hour_awal: $('#edit-sh-rh-awal').val(),
                        running_hour_akhir: $('#edit-sh-rh-akhir').val(),
                        feed_tank_awal: $('#edit-sh-ft-awal').val(),
                        feed_tank_akhir: $('#edit-sh-ft-akhir').val(),
                        chemical_scf: $('#edit-sh-scf').val(),
                        chemical_srtf: $('#edit-sh-srtf').val(),
                        dosis: $('#edit-sh-dosis').val(),
                    },
                    success: function() {
                        toastr.success('Data shift berhasil diperbarui');
                        bootstrap.Modal.getInstance('#modal-edit-shift').hide();
                        loadShift();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message ?? 'Gagal menyimpan');
                    }
                });
            });

            // ── MODAL DETAIL COAL HANDOVER ─────────────────────────────────────
            $(document).on('click', '.btn-detail-coal', function(e) {
                e.stopPropagation();
                const r = $(this).closest('tr').data('row');
                currentCoalRow = r;
                $('#modal-coal-tgl').text(r.tanggal_laporan);
                $('#d-coal-penyuplai-qty').text(formatDecimalDisplay(r.penyuplai_qty, 'Ton'));
                $('#d-coal-penyuplai-name').text(r.penyuplai_nik_nama ?? '—');
                $('#d-coal-penerima-qty').text(formatDecimalDisplay(r.penerima_qty, 'Ton'));
                $('#d-coal-penerima-name').text(r.penerima_nik_nama ?? '—');
                $('#d-coal-operator').text(r.operator?.username ?? '—');
                new bootstrap.Modal('#modal-coal').show();
            });

            // Buka edit coal langsung dari baris tabel
            $(document).on('click', '.btn-edit-coal-row', function(e) {
                e.stopPropagation();
                const r = $(this).closest('tr').data('row');
                currentCoalRow = r;
                $('#edit-coal-tgl-label').text(r.tanggal_laporan);
                $('#edit-coal-id').val(r.id);
                $('#edit-coal-penyuplai-qty').val(formatDecimalVal(r.penyuplai_qty));
                $('#edit-coal-penyuplai-name').val(r.penyuplai_nik_nama);
                $('#edit-coal-penerima-qty').val(formatDecimalVal(r.penerima_qty));
                $('#edit-coal-penerima-name').val(r.penerima_nik_nama);
                new bootstrap.Modal('#modal-edit-coal').show();
            });

            // Submit edit coal
            $('#form-edit-coal').on('submit', function(e) {
                e.preventDefault();
                const id = $('#edit-coal-id').val();
                $.ajax({
                    url: `/utility/esp-coal-handover/${id}`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'PUT',
                        penyuplai_qty: $('#edit-coal-penyuplai-qty').val(),
                        penyuplai_nik_nama: $('#edit-coal-penyuplai-name').val(),
                        penerima_qty: $('#edit-coal-penerima-qty').val(),
                        penerima_nik_nama: $('#edit-coal-penerima-name').val(),
                    },
                    success: function() {
                        toastr.success('Data serah terima batubara berhasil diperbarui');
                        bootstrap.Modal.getInstance('#modal-edit-coal').hide();
                        loadCoal();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message ?? 'Gagal menyimpan');
                    }
                });
            });

            // ── INIT ─────────────────────────────────────────────────────────
            loadOperational();
        });
    </script>
@endsection
