@extends('layouts.app')

@section('title', 'Rekap Data Agenda Tank Farm & Hydrant')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #102a43 0%, #243e56 100%); border-radius: 12px;">
                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="text-white fw-bold mb-1">
                                    <i class="ri-database-2-line text-success me-2"></i>
                                    Agenda Tank Farm & Hydrant - Rekap Data
                                </h4>
                                <p class="text-white-50 mb-0">
                                    Daftar log harian checklist Agenda Tank Farm & Hydrant
                                </p>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('agenda-tank-farm.index') }}"
                                    class="btn btn-warning btn-sm rounded-pill px-3">
                                    <i class="ri-add-line me-1"></i> Input
                                </a>

                                <button class="btn btn-outline-light btn-sm rounded-pill px-3" id="btnExport">
                                    <i class="ri-download-2-line me-1"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Container for Collected Months (Drafts) -->
            @if (Auth::user()->jabatan === 'foreman')
                <div class="card shadow-sm mt-3" id="collectedCard" style="display: none;">
                    <div class="card-header bg-soft-warning border-0">
                        <h5 class="card-title mb-0 text-warning-emphasis fw-bold">
                            <i class="ri-history-line me-2"></i> Draft Data Bulanan Terkumpul
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Bulan</th>
                                        <th>Tahun</th>
                                        <th class="text-center pe-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="collectedTbody">
                                    <!-- Dynamic rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <div class="row g-3 mb-4 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Filter Bulan</label>
                            <input type="month" id="filter_bulan" class="form-control" value="{{ date('Y-m') }}">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" onclick="loadData()">
                                <i class="ri-filter-3-line me-1"></i> Filter
                            </button>
                        </div>
                        <div class="col-md-7 text-end">
                            <div id="monthlyStatusContainer" class="d-inline-block">
                                <!-- Status Approval Bulanan Terpilih -->
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-nowrap" id="tableData">
                            <thead class="table-light align-middle text-center">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Checklist OK</th>
                                    <th>Checklist NOK</th>
                                    <th>Belum Diisi / Kosong</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyData">
                                <!-- Data AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div id="paginationInfo"></div>
                        <nav>
                            <ul class="pagination mb-0" id="paginationLinks"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">Detail Checklist Tank Farm & Hydrant</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Content via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Edit Checklist Tank Farm & Hydrant</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEdit">
                        @csrf
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                        </div>

                        @php
                            if (!function_exists('renderEditChecklistItem')) {
                                function renderEditChecklistItem($fieldName, $labelText)
                                {
                                    return '
                                    <div class="edit-item d-flex justify-content-between align-items-center flex-wrap py-2 border-bottom">
                                        <div class="fw-medium text-dark flex-grow-1 pe-2 mb-2 mb-sm-0 small">
                                            ' .
                                        $labelText .
                                        '
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <input type="radio" class="btn-check" name="' .
                                        $fieldName .
                                        '" id="edit_' .
                                        $fieldName .
                                        '_empty" value="">
                                            <label class="btn btn-outline-secondary px-3" for="edit_' .
                                        $fieldName .
                                        '_empty">Kosong</label>

                                            <input type="radio" class="btn-check" name="' .
                                        $fieldName .
                                        '" id="edit_' .
                                        $fieldName .
                                        '_ok" value="OK">
                                            <label class="btn btn-outline-success px-3" for="edit_' .
                                        $fieldName .
                                        '_ok">OK</label>

                                            <input type="radio" class="btn-check" name="' .
                                        $fieldName .
                                        '" id="edit_' .
                                        $fieldName .
                                        '_nok" value="NOK">
                                            <label class="btn btn-outline-danger px-3" for="edit_' .
                                        $fieldName .
                                        '_nok">NOK</label>
                                        </div>
                                    </div>
                                    ';
                                }
                            }
                        @endphp

                        <div class="card border-0 bg-light p-3">
                            <h6 class="fw-bold text-primary mb-2">Checklist Parameter</h6>
                            {!! renderEditChecklistItem('kelistrikan_pompa_sumur_1', 'Cek Kelistrikan (A, V) Pompa Sumur 1') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_sumur_2', 'Cek Kelistrikan (A, V) Pompa Sumur 2') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_sumur_4', 'Cek Kelistrikan (A, V) Pompa Sumur 4') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_sumur_5', 'Cek Kelistrikan (A, V) Pompa Sumur 5') !!}
                            {!! renderEditChecklistItem('pressure_pompa_sumur_1', 'Cek Pressure Pompa Sumur 1') !!}
                            {!! renderEditChecklistItem('pressure_pompa_sumur_2', 'Cek Pressure Pompa Sumur 2') !!}
                            {!! renderEditChecklistItem('pressure_pompa_sumur_4', 'Cek Pressure Pompa Sumur 4') !!}
                            {!! renderEditChecklistItem('pressure_pompa_sumur_5', 'Cek Pressure Pompa Sumur 5') !!}
                            {!! renderEditChecklistItem('flow_meter_pompa_sumur_1', 'Cek Flow Meter Pompa Sumur 1') !!}
                            {!! renderEditChecklistItem('flow_meter_pompa_sumur_2', 'Cek Flow Meter Pompa Sumur 2') !!}
                            {!! renderEditChecklistItem('flow_meter_pompa_sumur_4', 'Cek Flow Meter Pompa Sumur 4') !!}
                            {!! renderEditChecklistItem('flow_meter_pompa_sumur_5', 'Cek Flow Meter Pompa Sumur 5') !!}
                            {!! renderEditChecklistItem('drain_lumpur_settling_tank', 'Drain Lumpur Settling Tank') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_10p3', 'Cek Kelistrikan (A, V) Pompa 10P3') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_10p3a', 'Cek Kelistrikan (A, V) Pompa 10P3a') !!}
                            {!! renderEditChecklistItem('pressure_gauge_intermediate', 'Cek Pressure Gauge Intermediate') !!}
                            {!! renderEditChecklistItem('level_bandul_tank_farm', 'Cek Level Bandul Tank Farm') !!}
                            {!! renderEditChecklistItem('flow_meter_fresh_water_tank', 'Cek Flow Meter Fresh Water Tank') !!}
                            {!! renderEditChecklistItem('flow_meter_fwt_to_ro', 'Cek Flow Meter Fresh Water Tank to Mesin RO') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_10p4', 'Cek Kelistrikan (A, V) Pompa 10P4') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_10p4a', 'Cek Kelistrikan (A, V) Pompa 10P4a') !!}
                            {!! renderEditChecklistItem('pressure_gauge_pompa_10p4_p4a', 'Cek Pressure Gauge Pompa 10P4 & P4a') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_10p5', 'Cek Kelistrikan (A, V) Pompa 10P5') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_10p5a', 'Cek Kelistrikan (A, V) Pompa 10P5a') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_10p5b', 'Cek Kelistrikan (A, V) Pompa 10P5b') !!}
                            {!! renderEditChecklistItem('flow_meter_ro_reject_tank', 'Cek Flow Meter RO Reject Tank') !!}
                            {!! renderEditChecklistItem('pressure_gauge_pompa_10p5_10p5a', 'Cek Pressure Gauge Pompa 10P5 & 10P5a') !!}
                            {!! renderEditChecklistItem('drain_lumpur_tangki_intermediate', 'Drain Lumpur Tangki Intermediate') !!}
                            {!! renderEditChecklistItem(
                                'inspeksi_all_pompa_tf_intermediate',
                                'Inspeksi All Pompa Tank Farm dan Intermediet (HLT)',
                            ) !!}
                            {!! renderEditChecklistItem('inspeksi_pompa_20p1', 'Inspeksi (HLTE) Pompa 20P1') !!}
                            {!! renderEditChecklistItem('inspeksi_pompa_20p1a', 'Inspeksi (HLTE) Pompa 20P1a') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_20p2', 'Cek Kelistrikan (A, V) Pompa 20P2') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_20p2a', 'Cek Kelistrikan (A, V) Pompa 20P2a') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_60p1', 'Cek Kelistrikan (A, V) Pompa 60P1') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_60p2', 'Cek Kelistrikan (A, V) Pompa 60P2') !!}
                            {!! renderEditChecklistItem('kelistrikan_pompa_60p3', 'Cek Kelistrikan (A, V) Pompa 60P3') !!}
                            {!! renderEditChecklistItem('pressure_gauge_pompa_60p1', 'Cek Pressure Gauge Pompa 60P1') !!}
                            {!! renderEditChecklistItem('pressure_gauge_pompa_60p2', 'Cek Pressure Gauge Pompa 60P2') !!}
                            {!! renderEditChecklistItem('pressure_gauge_pompa_60p3', 'Cek Pressure Gauge Pompa 60P3') !!}
                            {!! renderEditChecklistItem('baterai_pompa_60p3', 'Cek Batterai Pompa 60P3') !!}
                            {!! renderEditChecklistItem('bahan_bakar_pompa_60p3', 'Cek Bahan Bakar Pompa 60P3') !!}
                            {!! renderEditChecklistItem('pressure_gauge_water_tank_hydrant', 'Cek Pressure Gauge Water Tank Hydrant') !!}
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="submitUpdate()">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Collected -->
    <div class="modal fade" id="modalCollectedDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white" id="modalCollectedDetailTitle">Detail Data Bulanan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalCollectedDetailContent">
                    <!-- Tables loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Submit Monthly -->
    <div class="modal fade" id="modalSubmitMonthly" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Kirim Approval Bulanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formSubmitMonthly">
                        @csrf
                        <input type="hidden" name="bulan" id="sm_bulan">
                        <input type="hidden" name="tahun" id="sm_tahun">

                        <div class="mb-3">
                            <label class="form-label">Pilih Supervisor</label>
                            <select name="supervisor_id" id="select_supervisor" class="form-select" required>
                                <option value="">-- Pilih Supervisor --</option>
                            </select>
                        </div>

                        <p class="text-muted small">
                            * Dengan menekan kirim, data checklist pada bulan ini akan diajukan ke Supervisor.
                        </p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning" onclick="processSubmitMonthly()">Kirim
                        Sekarang</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Export Excel -->
    <div class="modal fade" id="modalExport" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white"><i class="ri-file-excel-2-line me-1"></i> Export Excel Agenda TF-HY
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formExport">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bulan</label>
                                <select name="bulan" class="form-select">
                                    <option value="">-- Semua --</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tahun</label>
                                <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success px-4" id="btnExportConfirm">
                        <i class="ri-download-cloud-2-line me-1"></i> Download Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const API_URL = "{{ route('agenda-tank-farm.get-data') }}";
        let currentPage = 1;

        const ALL_FIELDS = [{
                field: 'kelistrikan_pompa_sumur_1',
                label: 'Cek Kelistrikan (A, V) Pompa Sumur 1'
            },
            {
                field: 'kelistrikan_pompa_sumur_2',
                label: 'Cek Kelistrikan (A, V) Pompa Sumur 2'
            },
            {
                field: 'kelistrikan_pompa_sumur_4',
                label: 'Cek Kelistrikan (A, V) Pompa Sumur 4'
            },
            {
                field: 'kelistrikan_pompa_sumur_5',
                label: 'Cek Kelistrikan (A, V) Pompa Sumur 5'
            },
            {
                field: 'pressure_pompa_sumur_1',
                label: 'Cek Pressure Pompa Sumur 1'
            },
            {
                field: 'pressure_pompa_sumur_2',
                label: 'Cek Pressure Pompa Sumur 2'
            },
            {
                field: 'pressure_pompa_sumur_4',
                label: 'Cek Pressure Pompa Sumur 4'
            },
            {
                field: 'pressure_pompa_sumur_5',
                label: 'Cek Pressure Pompa Sumur 5'
            },
            {
                field: 'flow_meter_pompa_sumur_1',
                label: 'Cek Flow Meter Pompa Sumur 1'
            },
            {
                field: 'flow_meter_pompa_sumur_2',
                label: 'Cek Flow Meter Pompa Sumur 2'
            },
            {
                field: 'flow_meter_pompa_sumur_4',
                label: 'Cek Flow Meter Pompa Sumur 4'
            },
            {
                field: 'flow_meter_pompa_sumur_5',
                label: 'Cek Flow Meter Pompa Sumur 5'
            },
            {
                field: 'drain_lumpur_settling_tank',
                label: 'Drain Lumpur Settling Tank'
            },
            {
                field: 'kelistrikan_pompa_10p3',
                label: 'Cek Kelistrikan (A, V) Pompa 10P3'
            },
            {
                field: 'kelistrikan_pompa_10p3a',
                label: 'Cek Kelistrikan (A, V) Pompa 10P3a'
            },
            {
                field: 'pressure_gauge_intermediate',
                label: 'Cek Pressure Gauge Intermediate'
            },
            {
                field: 'level_bandul_tank_farm',
                label: 'Cek Level Bandul Tank Farm'
            },
            {
                field: 'flow_meter_fresh_water_tank',
                label: 'Cek Flow Meter Fresh Water Tank'
            },
            {
                field: 'flow_meter_fwt_to_ro',
                label: 'Cek Flow Meter Fresh Water Tank to Mesin RO'
            },
            {
                field: 'kelistrikan_pompa_10p4',
                label: 'Cek Kelistrikan (A, V) Pompa 10P4'
            },
            {
                field: 'kelistrikan_pompa_10p4a',
                label: 'Cek Kelistrikan (A, V) Pompa 10P4a'
            },
            {
                field: 'pressure_gauge_pompa_10p4_p4a',
                label: 'Cek Pressure Gauge Pompa 10P4 & P4a'
            },
            {
                field: 'kelistrikan_pompa_10p5',
                label: 'Cek Kelistrikan (A, V) Pompa 10P5'
            },
            {
                field: 'kelistrikan_pompa_10p5a',
                label: 'Cek Kelistrikan (A, V) Pompa 10P5a'
            },
            {
                field: 'kelistrikan_pompa_10p5b',
                label: 'Cek Kelistrikan (A, V) Pompa 10P5b'
            },
            {
                field: 'flow_meter_ro_reject_tank',
                label: 'Cek Flow Meter RO Reject Tank'
            },
            {
                field: 'pressure_gauge_pompa_10p5_10p5a',
                label: 'Cek Pressure Gauge Pompa 10P5 & 10P5a'
            },
            {
                field: 'drain_lumpur_tangki_intermediate',
                label: 'Drain Lumpur Tangki Intermediate'
            },
            {
                field: 'inspeksi_all_pompa_tf_intermediate',
                label: 'Inspeksi All Pompa Tank Farm dan Intermediet (HLT)'
            },
            {
                field: 'inspeksi_pompa_20p1',
                label: 'Inspeksi (HLTE) Pompa 20P1'
            },
            {
                field: 'inspeksi_pompa_20p1a',
                label: 'Inspeksi (HLTE) Pompa 20P1a'
            },
            {
                field: 'kelistrikan_pompa_20p2',
                label: 'Cek Kelistrikan (A, V) Pompa 20P2'
            },
            {
                field: 'kelistrikan_pompa_20p2a',
                label: 'Cek Kelistrikan (A, V) Pompa 20P2a'
            },
            {
                field: 'kelistrikan_pompa_60p1',
                label: 'Cek Kelistrikan (A, V) Pompa 60P1'
            },
            {
                field: 'kelistrikan_pompa_60p2',
                label: 'Cek Kelistrikan (A, V) Pompa 60P2'
            },
            {
                field: 'kelistrikan_pompa_60p3',
                label: 'Cek Kelistrikan (A, V) Pompa 60P3'
            },
            {
                field: 'pressure_gauge_pompa_60p1',
                label: 'Cek Pressure Gauge Pompa 60P1'
            },
            {
                field: 'pressure_gauge_pompa_60p2',
                label: 'Cek Pressure Gauge Pompa 60P2'
            },
            {
                field: 'pressure_gauge_pompa_60p3',
                label: 'Cek Pressure Gauge Pompa 60P3'
            },
            {
                field: 'baterai_pompa_60p3',
                label: 'Cek Batterai Pompa 60P3'
            },
            {
                field: 'bahan_bakar_pompa_60p3',
                label: 'Cek Bahan Bakar Pompa 60P3'
            },
            {
                field: 'pressure_gauge_water_tank_hydrant',
                label: 'Cek Pressure Gauge Water Tank Hydrant'
            }
        ];

        function loadData(page = 1) {
            currentPage = page;
            let bulan = $('#filter_bulan').val();

            $.ajax({
                url: API_URL,
                type: "GET",
                data: {
                    bulan: bulan,
                    page: page
                },
                success: function(res) {
                    let html = '';
                    res.data.forEach(item => {
                        let statusBadge = '';
                        if (item.approval_status == 'draft') statusBadge =
                            '<span class="badge bg-warning">Draft</span>';
                        else if (item.approval_status == 'submitted') statusBadge =
                            '<span class="badge bg-info">Submitted</span>';
                        else if (item.approval_status == 'approved_foreman') statusBadge =
                            '<span class="badge bg-primary">Approve Foreman</span>';
                        else if (item.approval_status == 'approved_supervisor') statusBadge =
                            '<span class="badge bg-success">Approved</span>';
                        else if (item.approval_status == 'rejected') statusBadge =
                            '<span class="badge bg-danger">Rejected</span>';
                        else statusBadge = '<span class="badge bg-secondary">-</span>';

                        let countOk = 0;
                        let countNok = 0;
                        let countEmpty = 0;

                        ALL_FIELDS.forEach(f => {
                            if (item[f.field] === 'OK') countOk++;
                            else if (item[f.field] === 'NOK') countNok++;
                            else countEmpty++;
                        });

                        html += `
                            <tr>
                                <td class="text-center fw-medium">${item.tanggal}</td>
                                <td class="text-center text-success fw-bold">${countOk} Items</td>
                                <td class="text-center text-danger fw-bold">${countNok} Items</td>
                                <td class="text-center text-secondary">${countEmpty} Items</td>
                                <td class="text-center">${statusBadge}</td>
                                <td class="text-center">
                                    <div>
                                        <button class="btn btn-sm btn-info" onclick="showDetail(${item.id})" title="Detail">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary" onclick="editData(${item.id})" title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteData(${item.id})" title="Hapus">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    if (res.data.length == 0) {
                        html =
                            '<tr><td colspan="6" class="text-center py-4 text-muted">Data checklist tidak ditemukan</td></tr>';
                    }

                    $('#tbodyData').html(html);
                    renderPagination(res.pagination);

                    if (res.data.length > 0) {
                        let first = res.data[0];
                        let statusText = '';
                        if (first.approval_status === 'draft') statusText =
                            '<span class="badge bg-warning p-2"><i class="ri-time-line me-1"></i> Status Bulan Ini: Draft</span>';
                        else if (first.approval_status === 'submitted') statusText =
                            '<span class="badge bg-info p-2"><i class="ri-loader-4-line me-1"></i> Status Bulan Ini: Menunggu Approval</span>';
                        else if (first.approval_status === 'approved_foreman') statusText =
                            '<span class="badge bg-primary p-2"><i class="ri-loader-4-line me-1"></i> Status Bulan Ini: Approved Foreman</span>';
                        else if (first.approval_status === 'approved_supervisor') statusText =
                            '<span class="badge bg-success p-2"><i class="ri-checkbox-circle-line me-1"></i> Status Bulan Ini: Approved</span>';
                        else if (first.approval_status === 'rejected') statusText =
                            '<span class="badge bg-danger p-2"><i class="ri-close-circle-line me-1"></i> Status Bulan Ini: Rejected</span>';

                        $('#monthlyStatusContainer').html(statusText);
                    } else {
                        $('#monthlyStatusContainer').empty();
                    }
                }
            });

            loadCollected();
        }

        function loadCollected() {
            let tbody = $('#collectedTbody');
            let card = $('#collectedCard');
            if (tbody.length == 0) return;

            $.ajax({
                url: "{{ route('agenda-tank-farm.get-collected') }}",
                type: "GET",
                success: function(res) {
                    tbody.empty();
                    if (res.results && res.results.length > 0) {
                        card.show();
                        window.collectedData = res.results;
                        res.results.forEach((monthData, index) => {
                            let app = monthData.approval;
                            let monthName = moment().month(app.bulan - 1).format('MMMM');
                            let trHtml = `
                            <tr>
                                <td class="ps-3 fw-medium">${monthName}</td>
                                <td>${app.tahun}</td>
                                <td class="text-center pe-3">
                                    <button class="btn btn-sm btn-info" onclick="showCollectedDetail(${index})">
                                        <i class="ri-eye-line me-1"></i> Detail
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="openSubmitMonthly(${app.bulan}, ${app.tahun})">
                                        <i class="ri-send-plane-fill me-1"></i> Kirim Approval
                                    </button>
                                </td>
                            </tr>
                        `;
                            tbody.append(trHtml);
                        });
                    } else {
                        card.hide();
                    }
                }
            });
        }

        function buildChecklistStatusHtml(item, fieldList) {
            let listHtml = '<ul class="list-group list-group-flush">';
            fieldList.forEach(f => {
                let badge = '<span class="badge bg-secondary">-</span>';
                if (item[f.field] === 'OK') badge =
                    '<span class="badge bg-success"><i class="ri-check-line"></i> OK</span>';
                else if (item[f.field] === 'NOK') badge =
                    '<span class="badge bg-danger"><i class="ri-close-line"></i> NOK</span>';

                listHtml += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="small fw-medium">${f.label}</span>
                    ${badge}
                </li>
                `;
            });
            listHtml += '</ul>';
            return listHtml;
        }

        function showCollectedDetail(index) {
            let monthData = window.collectedData[index];
            let app = monthData.approval;
            let monthName = moment().month(app.bulan - 1).format('MMMM');

            let dataHtml = '';
            monthData.data.forEach(item => {
                let countOk = 0;
                let countNok = 0;
                ALL_FIELDS.forEach(f => {
                    if (item[f.field] === 'OK') countOk++;
                    else if (item[f.field] === 'NOK') countNok++;
                });

                dataHtml += `
                <div class="card border mb-2">
                    <div class="card-header bg-light d-flex justify-content-between">
                        <strong>Tanggal: ${item.tanggal}</strong>
                        <span>
                            <span class="badge bg-success me-1">${countOk} OK</span>
                            <span class="badge bg-danger">${countNok} NOK</span>
                        </span>
                    </div>
                </div>
                `;
            });

            $('#modalCollectedDetailTitle').text(`Detail Data Checklist Bulan ${monthName} ${app.tahun}`);
            $('#modalCollectedDetailContent').html(dataHtml);
            $('#modalCollectedDetail').modal('show');
        }

        function openSubmitMonthly(bulan, tahun) {
            $('#sm_bulan').val(bulan);
            $('#sm_tahun').val(tahun);

            // Load approvers
            $.get('/api/utility/users/approvers', function(data) {
                const supervisorList = data.user ?? [];
                let supervisorOpts = '<option value="">— Pilih Supervisor —</option>';
                supervisorList.forEach(function(u) {
                    supervisorOpts += `<option value="${u.id}">${u.username}</option>`;
                });
                $('#select_supervisor').html(supervisorOpts);
            }).fail(function() {
                $('#select_supervisor').html('<option value="">Gagal memuat data</option>');
            });

            $('#modalSubmitMonthly').modal('show');
        }

        function processSubmitMonthly() {
            let data = $('#formSubmitMonthly').serialize();

            if (!$('#select_supervisor').val()) {
                Swal.fire('Peringatan', 'Harap pilih Supervisor', 'warning');
                return;
            }

            $.post("{{ route('agenda-tank-farm.submit-monthly') }}", data, function(res) {
                Swal.fire('Berhasil!', res.message, 'success');
                $('#modalSubmitMonthly').modal('hide');
                loadData(currentPage);
            }).fail(function(xhr) {
                let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                Swal.fire('Gagal!', msg, 'error');
            });
        }

        function showDetail(id) {
            $.get("{{ url('utility/agenda-tank-farm/show') }}/" + id, function(res) {
                let data = res.data;
                let html = `
                    <div class="row g-3">
                        <div class="col-6"><strong>Tanggal:</strong> ${data.tanggal}</div>
                        <div class="col-6"><strong>Created By:</strong> ${data.created_by?.username || '-'}</div>
                        <div class="col-12"><hr class="my-1"></div>
                        
                        <div class="col-12">
                            <div class="card shadow-none border">
                                <div class="card-header bg-soft-primary py-2"><h6 class="mb-0 text-primary fw-bold">Checklist Tank Farm & Hydrant</h6></div>
                                ${buildChecklistStatusHtml(data, ALL_FIELDS)}
                            </div>
                        </div>
                    </div>
                `;
                $('#detailContent').html(html);
                $('#modalDetail').modal('show');
            });
        }

        function editData(id) {
            $.get("{{ url('utility/agenda-tank-farm/show') }}/" + id, function(res) {
                let data = res.data;

                $('#edit_id').val(data.id);
                $('#edit_tanggal').val(data.tanggal.substring(0, 10));

                ALL_FIELDS.forEach(f => {
                    let val = data[f.field];
                    if (val === 'OK') {
                        $(`#edit_${f.field}_ok`).prop('checked', true);
                    } else if (val === 'NOK') {
                        $(`#edit_${f.field}_nok`).prop('checked', true);
                    } else {
                        $(`#edit_${f.field}_empty`).prop('checked', true);
                    }
                });

                $('#modalEdit').modal('show');
            });
        }

        function submitUpdate() {
            let id = $('#edit_id').val();

            let hasValue = false;
            $('#formEdit').find('input[type="radio"]:checked').each(function() {
                if ($(this).val() !== '') {
                    hasValue = true;
                    return false;
                }
            });

            if (!hasValue) {
                Swal.fire('Peringatan', 'Minimal harus ada 1 checklist (OK / NOK) yang diisi sebelum update.', 'warning');
                return;
            }

            let data = $('#formEdit').serialize();

            $.post("{{ url('utility/agenda-tank-farm/update') }}/" + id, data, function(res) {
                Swal.fire('Berhasil!', res.message, 'success');
                $('#modalEdit').modal('hide');
                loadData(currentPage);
            }).fail(function(xhr) {
                let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                Swal.fire('Gagal!', msg, 'error');
            });
        }

        function renderPagination(pagination) {
            let html = '';
            if (pagination && pagination.last_page > 1) {
                html += `<li class="page-item ${pagination.current_page == 1 ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="loadData(${pagination.current_page - 1})">Prev</a>
            </li>`;

                for (let i = 1; i <= pagination.last_page; i++) {
                    if (i == 1 || i == pagination.last_page || (i >= pagination.current_page - 2 && i <= pagination
                            .current_page + 2)) {
                        html += `<li class="page-item ${pagination.current_page == i ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="loadData(${i})">${i}</a>
                    </li>`;
                    } else if (i == pagination.current_page - 3 || i == pagination.current_page + 3) {
                        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                }

                html += `<li class="page-item ${pagination.current_page == pagination.last_page ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="loadData(${pagination.current_page + 1})">Next</a>
            </li>`;
            }
            $('#paginationLinks').html(html);
            if (pagination) {
                $('#paginationInfo').html(
                    `Showing <b>${pagination.total > 0 ? (pagination.current_page - 1) * pagination.per_page + 1 : 0}</b> to <b>${Math.min(pagination.current_page * pagination.per_page, pagination.total)}</b> of <b>${pagination.total}</b> entries`
                );
            }
        }

        function deleteData(id) {
            Swal.fire({
                title: 'Hapus data?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('utility/agenda-tank-farm/destroy') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            Swal.fire({
                                title: 'Success!',
                                text: res.message,
                                icon: 'success',
                                timer: 1000,
                                showConfirmButton: false
                            });
                            loadData(currentPage);
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                            Swal.fire('Gagal!', msg, 'error');
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            loadData();

            $('#btnExport').on('click', function() {
                $('#modalExport').modal('show');
            });

            $('#btnExportConfirm').on('click', function() {
                const bulan = $('select[name="bulan"]', '#formExport').val();
                const tahun = $('input[name="tahun"]', '#formExport').val();

                let btn = $(this);
                btn.prop('disabled', true).html(
                    '<i class="ri-loader-4-line align-middle me-1"></i> Downloading...');

                fetch(`{{ route('agenda-tank-farm.export') }}?bulan=${bulan}&tahun=${tahun}`)
                    .then(async response => {
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.indexOf('application/json') !== -1) {
                            const json = await response.json();
                            Swal.fire({
                                icon: 'warning',
                                title: 'Data Tidak Ditemukan',
                                text: json.message ||
                                    'Tidak ada data ditemukan untuk periode tersebut.'
                            });
                        } else if (response.ok) {
                            const blob = await response.blob();
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;

                            const disposition = response.headers.get('content-disposition');
                            let filename = 'Agenda_Tank_Farm_Report.xlsx';
                            if (disposition && disposition.indexOf('filename=') !== -1) {
                                filename = disposition.split('filename=')[1].replace(/["']/g, '');
                            }
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                            window.URL.revokeObjectURL(url);
                            $('#modalExport').modal('hide');
                        } else {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat mengunduh laporan.',
                                'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Gagal!', 'Koneksi ke server gagal.', 'error');
                    })
                    .finally(() => {
                        btn.prop('disabled', false).html(
                            '<i class="ri-download-cloud-2-line me-1"></i> Download Excel');
                    });
            });
        });
    </script>
@endsection
