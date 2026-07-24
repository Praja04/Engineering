@extends('layouts.app')

@section('title', 'Visualisasi WWTP')

@section('styles')
    <style>
        .visualisasi-wrapper {
            width: 100%;
            overflow-x: auto;
            background-color: #1a1e29;
            border-radius: 12px;
            box-shadow: inset 0 2px 20px rgba(0, 0, 0, 0.5);
            border: 1px solid #2d3748;
        }

        .visualisasi-container {
            position: relative;
            width: 1550px;
            height: 600px;
            color: #e2e8f0;
        }

        /* Section Title absolute positions */
        .section-header {
            position: absolute;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 11px;
            letter-spacing: 1px;
            pointer-events: none;
        }

        .header-salt {
            top: 20px;
            left: 20px;
            color: #f59e0b;
        }

        .header-main {
            top: 150px;
            left: 20px;
            color: #0dcaf0;
        }

        .header-sludge {
            top: 420px;
            left: 20px;
            color: #8b5cf6;
        }

        /* Machine Nodes absolute positioning */
        .machine-node {
            position: absolute;
            background: rgba(30, 41, 59, 0.75);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 2px solid #475569;
            border-radius: 10px;
            width: 150px;
            height: 70px;
            padding: 8px 10px;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            z-index: 10;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .machine-node:hover {
            transform: translateY(-3px);
            border-color: #3b82f6;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
        }

        .machine-node.active-node {
            border-color: #10b981;
            box-shadow: 0 0 12px rgba(16, 185, 129, 0.3);
        }

        .machine-node.selected-inspect {
            border-color: #f59e0b;
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.6);
            background: rgba(245, 158, 11, 0.15);
        }

        .node-title {
            font-weight: 700;
            font-size: 11px;
            color: #fff;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .node-value {
            font-size: 9px;
            color: #cbd5e1;
            text-align: left;
            line-height: 1.3;
        }

        .node-value div {
            display: flex;
            justify-content: space-between;
        }

        /* Flow connector arrows using SVG */
        .connector-svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        /* Animate flowing dashes along lines */
        .flow-line {
            stroke: #475569;
            stroke-width: 3;
            fill: none;
            stroke-dasharray: 6, 6;
            animation: dash 35s linear infinite;
        }

        .flow-line.active-flow {
            stroke: #3b82f6;
            filter: drop-shadow(0 0 4px rgba(59, 130, 246, 0.6));
        }

        .flow-line.sludge-flow {
            stroke: #8b5cf6;
            filter: drop-shadow(0 0 4px rgba(139, 92, 246, 0.6));
        }

        .flow-line.salt-flow {
            stroke: #f59e0b;
            filter: drop-shadow(0 0 4px rgba(245, 158, 11, 0.6));
        }

        @keyframes dash {
            to {
                stroke-dashoffset: -1000;
            }
        }

        /* Inspection Pane on the right */
        .inspect-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            color: #f1f5f9;
            height: 100%;
        }

        .inspect-header {
            border-bottom: 1px solid #334155;
            padding: 16px 20px;
            background-color: #0f172a;
            border-top-left-radius: 11px;
            border-top-right-radius: 11px;
        }

        .badge-removal {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid #10b981;
            color: #10b981;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        .custom-date-input {
            background-color: #3d4a60 !important;
            border: 1px solid #475569 !important;
            color: #fff !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Visualisasi & Flow Proses WWTP</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#">Utility</a></li>
                                <li class="breadcrumb-item"><a href="#">WWTP</a></li>
                                <li class="breadcrumb-item active">Visualisasi</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Info Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-dark border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="row align-items-center g-3">
                                <div class="col-auto">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-primary-subtle rounded-3 fs-4">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col">
                                    <h5 class="text-white mb-1">Monitoring Diagram Alir & Parameter WWTP</h5>
                                    <p class="text-muted mb-0">Klik pada salah satu unit/mesin untuk melihat analisis detail
                                        dan formula perhitungan removal.</p>
                                </div>
                                <div class="col-12 col-md-auto d-flex align-items-center gap-2">
                                    <button id="btn-play-pause" class="btn btn-warning gap-1 d-flex align-items-center me-2">
                                        <i class="mdi mdi-play"></i> <span>Auto Play</span>
                                    </button>
                                    <label class="text-white small text-nowrap mb-0" for="filter_tanggal">Pilih
                                        Tanggal:</label>
                                    <input type="date" id="filter_tanggal" class="form-control custom-date-input"
                                        style="width: 170px;">
                                    <button id="btn-refresh" class="btn btn-outline-info">
                                        <i class="mdi mdi-refresh"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Side: Flowchart Visualizer -->
                <div class="col-xl-9 col-lg-8 mb-4">
                    <div class="visualisasi-wrapper">
                        <div class="visualisasi-container" id="visual-container">

                            <!-- SVG Overlay for drawing connections -->
                            <svg class="connector-svg" id="svg-connectors">
                                <!-- Salt Line Connections -->
                                <path id="line-garam-buffer" class="flow-line salt-flow" d="" />

                                <!-- Main Line Connections -->
                                <path id="line-sparta-step3" class="flow-line active-flow" d="" />
                                <path id="line-step3-equal" class="flow-line active-flow" d="" />
                                <path id="line-equal-anaerob" class="flow-line active-flow" d="" />

                                <!-- Splitting Anaerob to Aerob & Lumpur Aktif -->
                                <path id="line-anaerob-aerob" class="flow-line active-flow" d="" />
                                <path id="line-anaerob-lumpur" class="flow-line active-flow" d="" />

                                <!-- Domestik directly to Lumpur Aktif -->
                                <path id="line-domestik-lumpur" class="flow-line active-flow" d="" />

                                <!-- Merging to DAF -->
                                <path id="line-aerob-daf" class="flow-line active-flow" d="" />
                                <path id="line-lumpur-daf" class="flow-line active-flow" d="" />

                                <!-- Post DAF -->
                                <path id="line-daf-sand" class="flow-line active-flow" d="" />
                                <path id="line-sand-outlet" class="flow-line active-flow" d="" />

                                <!-- Sludge Line Connections -->
                                <path id="line-slurry-screw" class="flow-line sludge-flow" d="" />
                                <path id="line-screw-ibc" class="flow-line sludge-flow" d="" />
                                <path id="line-ibc-angkut" class="flow-line sludge-flow" d="" />
                            </svg>

                            <!-- Section Headers -->
                            <div class="section-header header-salt"><i class="mdi mdi-cube-outline"></i> Salt Line (Garam)
                            </div>
                            <div class="section-header header-main"><i class="mdi mdi-cog-sync-outline"></i> Main Biological
                                & Chemical Treatment Line</div>
                            <div class="section-header header-sludge"><i class="mdi mdi-opacity"></i> Sludge Processing Line
                                (Lumpur)</div>

                            <!-- === SALT LINE NODES === -->
                            <div class="machine-node" id="node-pit_garam" data-unit="pit_garam"
                                style="left: 20px; top: 50px;">
                                <div class="node-title">Pit Garam</div>
                                <div class="node-value">
                                    <div><span>Vol:</span> <strong id="val-pg-vol">-</strong></div>
                                    <div><span>pH:</span> <strong id="val-pg-ph">-</strong></div>
                                    <div><span>TSS:</span> <strong id="val-pg-tss">-</strong></div>
                                </div>
                            </div>

                            <div class="machine-node" id="node-buffer_pit_garam" data-unit="buffer_pit_garam"
                                style="left: 210px; top: 50px;">
                                <div class="node-title">Buffer Pit Garam</div>
                                <div class="node-value"
                                    style="display: flex; flex-direction: column; justify-content: center; height: calc(100% - 15px);">
                                    <div class="text-success text-center fw-bold" style="font-size: 10px;">Active / Ready
                                    </div>
                                </div>
                            </div>


                            <!-- === MAIN BIOLOGICAL TREATMENT LINE NODES === -->
                            <div class="machine-node" id="node-pit_sparta" data-unit="pit_sparta"
                                style="left: 20px; top: 190px;">
                                <div class="node-title">Pit Sparta</div>
                                <div class="node-value">
                                    <div><span>Vol:</span> <strong id="val-ps-vol">-</strong></div>
                                </div>
                            </div>

                            <div class="machine-node" id="node-step3" data-unit="step3"
                                style="left: 210px; top: 190px;">
                                <div class="node-title">Step 3</div>
                                <div class="node-value">
                                    <div><span>Vol:</span> <strong id="val-s3-vol">-</strong></div>
                                </div>
                            </div>

                            <div class="machine-node" id="node-equalisasi" data-unit="equalisasi"
                                style="left: 400px; top: 190px;">
                                <div class="node-title">Equalisasi</div>
                                <div class="node-value">
                                    <div><span>pH:</span> <strong id="val-eq-ph">-</strong></div>
                                    <div><span>TSS:</span> <strong id="val-eq-tss">-</strong></div>
                                    <div><span>COD:</span> <strong id="val-eq-cod">-</strong></div>
                                </div>
                            </div>

                            <div class="machine-node" id="node-anaerob" data-unit="anaerob"
                                style="left: 590px; top: 190px;">
                                <div class="node-title">Anaerob</div>
                                <div class="node-value">
                                    <div><span>pH:</span> <strong id="val-an-ph">-</strong></div>
                                    <div><span>TSS:</span> <strong id="val-an-tss">-</strong></div>
                                    <div><span>COD:</span> <strong id="val-an-cod">-</strong></div>
                                </div>
                            </div>

                            <!-- Aerob (Upper Column 5) -->
                            <div class="machine-node" id="node-aerob" data-unit="aerob" style="left: 780px; top: 70px;">
                                <div class="node-title">Aerob</div>
                                <div class="node-value">
                                    <div><span>pH:</span> <strong id="val-ae-ph">-</strong></div>
                                    <div><span>TSS:</span> <strong id="val-ae-tss">-</strong></div>
                                    <div><span>COD:</span> <strong id="val-ae-cod">-</strong></div>
                                </div>
                            </div>

                            <!-- Pit Domestik (Row 3, Col 1) -->
                            <div class="machine-node" id="node-pit_domestik" data-unit="pit_domestik"
                                style="left: 20px; top: 310px;">
                                <div class="node-title">Pit Domestik</div>
                                <div class="node-value">
                                    <div><span>Vol:</span> <strong id="val-pd-vol">-</strong></div>
                                </div>
                            </div>

                            <!-- Lumpur Aktif (Lower Column 5) -->
                            <div class="machine-node" id="node-lumpur_aktif" data-unit="lumpur_aktif"
                                style="left: 780px; top: 310px;">
                                <div class="node-title">Lumpur Aktif</div>
                                <div class="node-value">
                                    <div><span>pH:</span> <strong id="val-la-ph">-</strong></div>
                                    <div><span>TSS:</span> <strong id="val-la-tss">-</strong></div>
                                    <div><span>COD:</span> <strong id="val-la-cod">-</strong></div>
                                </div>
                            </div>

                            <!-- DAF (Col 6, Row 2) -->
                            <div class="machine-node" id="node-daf" data-unit="daf" style="left: 970px; top: 190px;">
                                <div class="node-title">DAF</div>
                                <div class="node-value">
                                    <div><span>pH:</span> <strong id="val-df-ph">-</strong></div>
                                    <div><span>TSS:</span> <strong id="val-df-tss">-</strong></div>
                                    <div><span>COD:</span> <strong id="val-df-cod">-</strong></div>
                                </div>
                            </div>

                            <!-- Sand Filter (Col 7, Row 2) -->
                            <div class="machine-node" id="node-sandfilter" data-unit="sandfilter"
                                style="left: 1160px; top: 190px;">
                                <div class="node-title">Sand Filter</div>
                                <div class="node-value">
                                    <div><span>pH:</span> <strong id="val-sf-ph">-</strong></div>
                                    <div><span>TSS:</span> <strong id="val-sf-tss">-</strong></div>
                                    <div><span>COD:</span> <strong id="val-sf-cod">-</strong></div>
                                </div>
                            </div>

                            <!-- Outlet (Col 8, Row 2) -->
                            <div class="machine-node" id="node-outlet" data-unit="outlet"
                                style="left: 1350px; top: 190px;">
                                <div class="node-title">Outlet</div>
                                <div class="node-value">
                                    <div><span>pH:</span> <strong id="val-ot-ph">-</strong></div>
                                    <div><span>TSS:</span> <strong id="val-ot-tss">-</strong></div>
                                    <div><span>COD:</span> <strong id="val-ot-cod">-</strong></div>
                                </div>
                            </div>


                            <!-- === SLUDGE LINE NODES === -->
                            <div class="machine-node" id="node-drain_lumpur" data-unit="drain_lumpur"
                                style="left: 210px; top: 460px;">
                                <div class="node-title">Drain Lumpur</div>
                                <div class="node-value">
                                    <div><span>Vol:</span> <strong id="val-dl-vol">-</strong></div>
                                </div>
                            </div>

                            <div class="machine-node" id="node-screwpress" data-unit="screwpress"
                                style="left: 400px; top: 460px;">
                                <div class="node-title">Screwpress</div>
                                <div class="node-value">
                                    <div><span>RH:</span> <strong id="val-sp-rh">-</strong></div>
                                    <div><span>Content:</span> <strong id="val-sp-cnt">-</strong></div>
                                </div>
                            </div>

                            <div class="machine-node" id="node-ibc_tank" data-unit="ibc_tank"
                                style="left: 590px; top: 460px;">
                                <div class="node-title">IBC Tank</div>
                                <div class="node-value">
                                    <div><span>Hasil:</span> <strong id="val-it-vol">-</strong></div>
                                </div>
                            </div>

                            <div class="machine-node" id="node-pengangkutan_sludge" data-unit="pengangkutan_sludge"
                                style="left: 780px; top: 460px;">
                                <div class="node-title">Pengangkutan Sludge</div>
                                <div class="node-value">
                                    <div><span>Jumlah:</span> <strong id="val-ps-qty">-</strong></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Right Side: Inspector Details Panel -->
                <div class="col-xl-3 col-lg-4 mb-4">
                    <div class="card inspect-card shadow">
                        <div class="inspect-header d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small text-uppercase">Selected Unit</span>
                                <h5 class="text-white mb-0" id="inspect-title">Pilih Mesin/Unit</h5>
                            </div>
                            <i class="mdi mdi-information-outline fs-3 text-info"></i>
                        </div>
                        <div class="card-body p-4 d-flex flex-column justify-content-between" style="min-height: 480px;">

                            <div id="inspect-content">
                                <div class="text-center py-5 text-muted">
                                    <i class="mdi mdi-gesture-tap fs-1 d-block mb-3 text-secondary"></i>
                                    <p class="mb-0">Pilih salah satu unit mesin pada alur diagram proses untuk memuat
                                        data analisis harian & persentase removal secara terperinci.</p>
                                </div>
                            </div>

                            <!-- Footer indicator -->
                            <div class="mt-4 pt-3 border-top border-secondary text-muted small">
                                <strong>Standard Parameter:</strong><br>
                                pH: 6.0 - 9.0 | TSS: max 100 ppm | COD: max 300 ppm
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
        $(document).ready(function() {
            let loadedDate = "";

            // Function to load all visual data from API
            function loadVisualData(date = "") {
                $.ajax({
                    url: "{{ route('wwtp.dashboard_visualisasi_data') }}",
                    type: "GET",
                    data: {
                        tanggal: date
                    },
                    beforeSend: function() {
                        $('#btn-refresh').html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                        );
                    },
                    success: function(response) {
                        $('#btn-refresh').html('<i class="mdi mdi-refresh"></i>');
                        if (response.status === 'success') {
                            loadedDate = response.tanggal;
                            $('#filter_tanggal').val(loadedDate);

                            // Update UI Node values
                            updateUIValues(response);

                            // Store complete response globally for inspect panel lookup
                            window.wwtpData = response;

                            // Auto-select first inspect node
                            if (!$('.machine-node.selected-inspect').length) {
                                $('#node-equalisasi').trigger('click');
                            } else {
                                let currentSelected = $('.machine-node.selected-inspect').data('unit');
                                showInspectionDetails(currentSelected);
                            }
                        }
                    },
                    error: function(xhr) {
                        $('#btn-refresh').html('<i class="mdi mdi-refresh"></i>');
                        alert("Gagal memuat data visualisasi WWTP.");
                    }
                });
            }

            // Helper function to format numbers
            function fmt(val, dec = 2, unit = "") {
                if (val === null || val === undefined || val === 0 || val === "0") return "-";
                return Number(val).toFixed(dec) + (unit ? " " + unit : "");
            }

            // Update all metrics on the flowchart nodes
            function updateUIValues(res) {
                // Processes
                $('#val-pg-vol').text(fmt(res.proses.pit_garam, 1, 'm³'));
                $('#val-ps-vol').text(fmt(res.proses.pit_sparta, 1, 'm³'));
                $('#val-s3-vol').text(fmt(res.proses.pit_produksi_step3, 1, 'm³'));
                $('#val-pd-vol').text(fmt(res.proses.pit_domestik, 1, 'm³'));

                // Analisa values
                $('#val-pg-ph').text(fmt(res.analisa.Influent.ph, 1));
                $('#val-pg-tss').text(fmt(res.analisa.Influent.tss, 0, 'ppm'));

                $('#val-eq-ph').text(fmt(res.analisa['Equalisasi 2'].ph, 1));
                $('#val-eq-tss').text(fmt(res.analisa['Equalisasi 2'].tss, 0, 'ppm'));
                $('#val-eq-cod').text(fmt(res.analisa['Equalisasi 2'].cod, 0, 'ppm'));

                $('#val-an-ph').text(fmt(res.analisa['Outlet Anaerob'].ph, 1));
                $('#val-an-tss').text(fmt(res.analisa['Outlet Anaerob'].tss, 0, 'ppm'));
                $('#val-an-cod').text(fmt(res.analisa['Outlet Anaerob'].cod, 0, 'ppm'));

                // Top/Bottom paths (Aerob / Lumpur Aktif)
                $('#val-ae-ph').text(fmt(res.analisa['Aerasi-5'].ph, 1));
                $('#val-ae-tss').text(fmt(res.analisa['Aerasi-5'].tss, 0, 'ppm'));
                $('#val-ae-cod').text(fmt(res.analisa['Aerasi-5'].cod, 0, 'ppm'));

                $('#val-la-ph').text(fmt(res.analisa['Lumpur Aktif'].ph, 1));
                $('#val-la-tss').text(fmt(res.analisa['Lumpur Aktif'].tss, 0, 'ppm'));
                $('#val-la-cod').text(fmt(res.analisa['Lumpur Aktif'].cod, 0, 'ppm'));

                // Post-merge DAF, Sandfilter, and Outlet
                $('#val-df-ph').text(fmt(res.analisa['Outlet DAF'].ph, 1));
                $('#val-df-tss').text(fmt(res.analisa['Outlet DAF'].tss, 0, 'ppm'));
                $('#val-df-cod').text(fmt(res.analisa['Outlet DAF'].cod, 0, 'ppm'));

                $('#val-sf-ph').text(fmt(res.analisa['Outlet Sand Filter'].ph, 1));
                $('#val-sf-tss').text(fmt(res.analisa['Outlet Sand Filter'].tss, 0, 'ppm'));
                $('#val-sf-cod').text(fmt(res.analisa['Outlet Sand Filter'].cod, 0, 'ppm'));

                $('#val-ot-ph').text(fmt(res.analisa.Effluent.ph, 1));
                $('#val-ot-tss').text(fmt(res.analisa.Effluent.tss, 0, 'ppm'));
                $('#val-ot-cod').text(fmt(res.analisa.Effluent.cod, 0, 'ppm'));

                // Sludge Line
                $('#val-dl-vol').text(fmt(res.sludge.drain_lumpur, 1, 'm³'));
                $('#val-sp-rh').text(fmt(res.sludge.running_hour_scp, 1, 'hrs'));
                $('#val-sp-cnt').text(fmt(res.sludge.sludge_content, 1, '%'));
                $('#val-it-vol').text(fmt(res.sludge.hasil_lumpur, 1, 'm³'));
                $('#val-ps-qty').text(fmt(res.sludge.pengangkutan, 0, 'm³'));

                // Toggle active classes on nodes with values
                $('.machine-node').each(function() {
                    let unit = $(this).data('unit');
                    let hasData = false;
                    if (res.proses[unit] > 0 || res.sludge[unit] > 0) hasData = true;
                    if (res.analisa[unit] && (res.analisa[unit].ph > 0 || res.analisa[unit].tss > 0))
                        hasData = true;
                    if (unit === 'buffer_pit_garam' && res.proses.pit_garam > 0) hasData = true;

                    if (hasData) {
                        $(this).addClass('active-node');
                    } else {
                        $(this).removeClass('active-node');
                    }
                });
            }

            // Draw and update SVG connection lines dynamically based on node coordinates
            function drawConnectors() {
                let container = $('#visual-container');

                function getRightPos(elId) {
                    let el = $('#' + elId);
                    if (!el.length) return {
                        x: 0,
                        y: 0
                    };
                    let pos = el.position();
                    return {
                        x: pos.left + el.outerWidth(),
                        y: pos.top + el.outerHeight() / 2
                    };
                }

                function getLeftPos(elId) {
                    let el = $('#' + elId);
                    if (!el.length) return {
                        x: 0,
                        y: 0
                    };
                    let pos = el.position();
                    return {
                        x: pos.left,
                        y: pos.top + el.outerHeight() / 2
                    };
                }

                // Salt Line
                let p_pg = getRightPos('node-pit_garam');
                let p_bpg = getLeftPos('node-buffer_pit_garam');
                $('#line-garam-buffer').attr('d', `M ${p_pg.x} ${p_pg.y} L ${p_bpg.x} ${p_bpg.y}`);

                // Main Line
                let p_sp = getRightPos('node-pit_sparta');
                let p_s3 = getLeftPos('node-step3');
                $('#line-sparta-step3').attr('d', `M ${p_sp.x} ${p_sp.y} L ${p_s3.x} ${p_s3.y}`);

                let p_s3_r = getRightPos('node-step3');
                let p_eq = getLeftPos('node-equalisasi');
                $('#line-step3-equal').attr('d', `M ${p_s3_r.x} ${p_s3_r.y} L ${p_eq.x} ${p_eq.y}`);

                let p_eq_r = getRightPos('node-equalisasi');
                let p_an = getLeftPos('node-anaerob');
                $('#line-equal-anaerob').attr('d', `M ${p_eq_r.x} ${p_eq_r.y} L ${p_an.x} ${p_an.y}`);

                // Split Anaerob to Aerob & Lumpur Aktif
                let p_an_r = getRightPos('node-anaerob');
                let p_ae = getLeftPos('node-aerob');
                let p_la = getLeftPos('node-lumpur_aktif');
                let midX = p_an_r.x + (p_ae.x - p_an_r.x) / 2;
                $('#line-anaerob-aerob').attr('d',
                    `M ${p_an_r.x} ${p_an_r.y} L ${midX} ${p_an_r.y} L ${midX} ${p_ae.y} L ${p_ae.x} ${p_ae.y}`);
                $('#line-anaerob-lumpur').attr('d',
                    `M ${p_an_r.x} ${p_an_r.y} L ${midX} ${p_an_r.y} L ${midX} ${p_la.y} L ${p_la.x} ${p_la.y}`);

                // Domestik to Lumpur Aktif
                let p_dom = getRightPos('node-pit_domestik');
                $('#line-domestik-lumpur').attr('d', `M ${p_dom.x} ${p_dom.y} L ${p_la.x} ${p_la.y}`);

                // Aerob & Lumpur Aktif merging to DAF
                let p_ae_r = getRightPos('node-aerob');
                let p_la_r = getRightPos('node-lumpur_aktif');
                let p_daf = getLeftPos('node-daf');
                let midX2 = p_ae_r.x + (p_daf.x - p_ae_r.x) / 2;
                $('#line-aerob-daf').attr('d',
                    `M ${p_ae_r.x} ${p_ae_r.y} L ${midX2} ${p_ae_r.y} L ${midX2} ${p_daf.y} L ${p_daf.x} ${p_daf.y}`
                );
                $('#line-lumpur-daf').attr('d',
                    `M ${p_la_r.x} ${p_la_r.y} L ${midX2} ${p_la_r.y} L ${midX2} ${p_daf.y} L ${p_daf.x} ${p_daf.y}`
                );

                // DAF to Sand Filter
                let p_daf_r = getRightPos('node-daf');
                let p_sf = getLeftPos('node-sandfilter');
                $('#line-daf-sand').attr('d', `M ${p_daf_r.x} ${p_daf_r.y} L ${p_sf.x} ${p_sf.y}`);

                // Sand Filter to Outlet
                let p_sf_r = getRightPos('node-sandfilter');
                let p_ot = getLeftPos('node-outlet');
                $('#line-sand-outlet').attr('d', `M ${p_sf_r.x} ${p_sf_r.y} L ${p_ot.x} ${p_ot.y}`);

                // Sludge Line
                let p_dl = getRightPos('node-drain_lumpur');
                let p_spress = getLeftPos('node-screwpress');
                $('#line-slurry-screw').attr('d', `M ${p_dl.x} ${p_dl.y} L ${p_spress.x} ${p_spress.y}`);

                let p_spress_r = getRightPos('node-screwpress');
                let p_it = getLeftPos('node-ibc_tank');
                $('#line-screw-ibc').attr('d', `M ${p_spress_r.x} ${p_spress_r.y} L ${p_it.x} ${p_it.y}`);

                let p_it_r = getRightPos('node-ibc_tank');
                let p_psludge = getLeftPos('node-pengangkutan_sludge');
                $('#line-ibc-angkut').attr('d', `M ${p_it_r.x} ${p_it_r.y} L ${p_psludge.x} ${p_psludge.y}`);
            }

            // Click handler to inspect a unit
            $(document).on('click', '.machine-node', function() {
                $('.machine-node').removeClass('selected-inspect');
                $(this).addClass('selected-inspect');

                let unit = $(this).data('unit');
                showInspectionDetails(unit);

                // Sync index with manual click
                if (typeof unitSequence !== 'undefined') {
                    let idx = unitSequence.indexOf(unit);
                    if (idx !== -1) {
                        currentRotationIndex = idx;
                    }
                }
            });

            // Show details in the right-side inspect panel
            function showInspectionDetails(unit) {
                let res = window.wwtpData;
                if (!res) return;

                let title = "";
                let html = "";
                let badge = "";

                function rawFmt(val, dec = 2, suffix = "") {
                    if (val === null || val === undefined || val === 0 || val === "0") return "-";
                    return Number(val).toFixed(dec) + " " + suffix;
                }

                switch (unit) {
                    case 'pit_garam':
                        title = "Pit Garam";
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Salt Processing & Inlet</h6>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                                <span class="text-muted small d-block">PROSES HARIAN</span>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="text-white fw-semibold">Volume Aliran:</span>
                                    <span class="fs-16 fw-bold text-info">${rawFmt(res.proses.pit_garam, 1, 'm³')}</span>
                                </div>
                            </div>
                            <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                                <span class="text-muted small d-block">ANALISA PARAMETER</span>
                                <div class="d-flex justify-content-between mt-2">
                                    <span>pH:</span>
                                    <strong class="text-light">${rawFmt(res.analisa.Influent.ph, 1)}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>TSS:</span>
                                    <strong class="text-light">${rawFmt(res.analisa.Influent.tss, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>COD:</span>
                                    <strong class="text-light">${rawFmt(res.analisa.Influent.cod, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>EC:</span>
                                    <strong class="text-light">${rawFmt(res.analisa.Influent.ec, 0, 'µS/cm')}</strong>
                                </div>
                            </div>
                        </div>
                    `;
                        break;

                    case 'buffer_pit_garam':
                        title = "Buffer Pit Garam";
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Penyangga Aliran</h6>
                        </div>
                        <p class="text-muted small">Fungsi utama adalah menampung limpahan air garam dari Pit Garam sebelum dialirkan ke tahap pengolahan berikutnya guna menstabilkan laju alir (buffer).</p>
                        <div class="p-3 bg-dark-subtle rounded-3 border border-secondary text-center">
                            <span class="text-muted small">STATUS OPERASIONAL</span>
                            <h5 class="text-success fw-bold mt-2"><i class="mdi mdi-check-circle"></i> Ready / Active</h5>
                        </div>
                    `;
                        break;

                    case 'pit_sparta':
                        title = "Pit Sparta";
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Inlet Produksi</h6>
                        </div>
                        <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                            <span class="text-muted small d-block">PROSES HARIAN</span>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-white fw-semibold">Volume Aliran:</span>
                                <span class="fs-16 fw-bold text-info">${rawFmt(res.proses.pit_sparta, 1, 'm³')}</span>
                            </div>
                        </div>
                    `;
                        break;

                    case 'step3':
                        title = "Step 3";
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Inlet Produksi Step 3</h6>
                        </div>
                        <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                            <span class="text-muted small d-block">PROSES HARIAN</span>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-white fw-semibold">Volume Aliran:</span>
                                <span class="fs-16 fw-bold text-info">${rawFmt(res.proses.pit_produksi_step3, 1, 'm³')}</span>
                            </div>
                        </div>
                    `;
                        break;

                    case 'equalisasi':
                        title = "Equalisasi";
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Bak Ekualisasi (Influent)</h6>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                                <span class="text-muted small d-block">DEBIT ALIRAN MASUK</span>
                                <div class="d-flex justify-content-between mt-2">
                                    <span>Debit 1 (Avg):</span>
                                    <strong class="text-info">${rawFmt(res.proses.debit1, 1, 'm³')}</strong>
                                </div>
                            </div>
                            <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                                <span class="text-muted small d-block">ANALISA PARAMETER INFLUENT</span>
                                <div class="d-flex justify-content-between mt-2">
                                    <span>pH:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Equalisasi 2'].ph, 1)}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>TSS:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Equalisasi 2'].tss, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>COD:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Equalisasi 2'].cod, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>EC:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Equalisasi 2'].ec, 0, 'µS/cm')}</strong>
                                </div>
                            </div>
                        </div>
                    `;
                        break;

                    case 'anaerob':
                        title = "Anaerob";
                        badge =
                            `<span class="badge-removal">Removal: TSS ${rawFmt(res.removals.anaerob.tss, 1, '%')} | COD ${rawFmt(res.removals.anaerob.cod, 1, '%')}</span>`;
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Pengolahan Anaerobik</h6>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                                <span class="text-muted small d-block">ANALISA PARAMETER OUTLET</span>
                                <div class="d-flex justify-content-between mt-2">
                                    <span>pH:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Outlet Anaerob'].ph, 1)}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>TSS:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Outlet Anaerob'].tss, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>COD:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Outlet Anaerob'].cod, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>EC:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Outlet Anaerob'].ec, 0, 'µS/cm')}</strong>
                                </div>
                            </div>
                            <div class="p-3 bg-info-subtle text-dark rounded-3 border border-info">
                                <span class="fw-bold d-block text-uppercase small">Formula Efisiensi (Removal)</span>
                                <code class="d-block mt-1 font-monospace text-dark" style="font-size: 10px;">((Influent - Outlet Anaerob) / Influent) * 100</code>
                            </div>
                        </div>
                    `;
                        break;

                    case 'pit_domestik':
                        title = "Pit Domestik";
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Limbah Domestik</h6>
                        </div>
                        <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                            <span class="text-muted small d-block">PROSES HARIAN</span>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-white fw-semibold">Volume Aliran:</span>
                                <span class="fs-16 fw-bold text-info">${rawFmt(res.proses.pit_domestik, 1, 'm³')}</span>
                            </div>
                        </div>
                    `;
                        break;

                    case 'aerob':
                        title = "Aerob";
                        badge =
                            `<span class="badge-removal">Removal: TSS ${rawFmt(res.removals.aerob.tss, 1, '%')} | COD ${rawFmt(res.removals.aerob.cod, 1, '%')}</span>`;
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Bak Aerasi (Aerobik)</h6>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                                <span class="text-muted small d-block">ANALISA PARAMETER AERASI</span>
                                <div class="d-flex justify-content-between mt-2">
                                    <span>pH:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Aerasi-5'].ph, 1)}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>TSS:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Aerasi-5'].tss, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>COD:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Aerasi-5'].cod, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>EC:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Aerasi-5'].ec, 0, 'µS/cm')}</strong>
                                </div>
                            </div>
                            <div class="p-3 bg-info-subtle text-dark rounded-3 border border-info">
                                <span class="fw-bold d-block text-uppercase small">Formula Efisiensi (Removal)</span>
                                <code class="d-block mt-1 font-monospace text-dark" style="font-size: 10px;">((Outlet Anaerob - Aerasi 6) / Outlet Anaerob) * 100</code>
                            </div>
                        </div>
                    `;
                        break;

                    case 'lumpur_aktif':
                        title = "Lumpur Aktif";
                        badge =
                            `<span class="badge-removal">Removal: TSS ${rawFmt(res.removals.lumpur_aktif.tss, 1, '%')} | COD ${rawFmt(res.removals.lumpur_aktif.cod, 1, '%')}</span>`;
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Activated Sludge System</h6>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                                <span class="text-muted small d-block">ANALISA PARAMETER LUMPUR AKTIF</span>
                                <div class="d-flex justify-content-between mt-2">
                                    <span>pH:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Lumpur Aktif'].ph, 1)}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>TSS:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Lumpur Aktif'].tss, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>COD:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Lumpur Aktif'].cod, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>EC:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Lumpur Aktif'].ec, 0, 'µS/cm')}</strong>
                                </div>
                            </div>
                            <div class="p-3 bg-info-subtle text-dark rounded-3 border border-info">
                                <span class="fw-bold d-block text-uppercase small">Formula Efisiensi (Removal)</span>
                                <code class="d-block mt-1 font-monospace text-dark" style="font-size: 10px;">((Outlet Anaerob - Lumpur Aktif) / Outlet Anaerob) * 100</code>
                            </div>
                        </div>
                    `;
                        break;

                    case 'daf':
                        title = "DAF";
                        badge =
                            `<span class="badge-removal">Removal: TSS ${rawFmt(res.removals.daf.tss, 1, '%')} | COD ${rawFmt(res.removals.daf.cod, 1, '%')}</span>`;
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Dissolved Air Flotation (DAF)</h6>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                                <span class="text-muted small d-block">ANALISA PARAMETER DAF POST</span>
                                <div class="d-flex justify-content-between mt-2">
                                    <span>pH:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Outlet DAF'].ph, 1)}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>TSS:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Outlet DAF'].tss, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>COD:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Outlet DAF'].cod, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>EC:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Outlet DAF'].ec, 0, 'µS/cm')}</strong>
                                </div>
                            </div>
                            <div class="p-3 bg-info-subtle text-dark rounded-3 border border-info">
                                <span class="fw-bold d-block text-uppercase small">Formula Efisiensi (Removal)</span>
                                <div class="small mt-1 text-secondary" style="font-size: 9px; line-height: 1.2;">
                                    Clarifier Avg = (Clarifier 1 + Clarifier 2) / 2 <br>
                                    ((Clarifier Avg - DAF) / Clarifier Avg) * 100
                                </div>
                            </div>
                        </div>
                    `;
                        break;

                    case 'sandfilter':
                        title = "Sand Filter";
                        badge =
                            `<span class="badge-removal">Removal: TSS ${rawFmt(res.removals.sandfilter.tss, 1, '%')} | COD ${rawFmt(res.removals.sandfilter.cod, 1, '%')}</span>`;
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Penyaringan Pasir Silika</h6>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                                <span class="text-muted small d-block">ANALISA PARAMETER SANDFILTER</span>
                                <div class="d-flex justify-content-between mt-2">
                                    <span>pH:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Outlet Sand Filter'].ph, 1)}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>TSS:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Outlet Sand Filter'].tss, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>COD:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Outlet Sand Filter'].cod, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>EC:</span>
                                    <strong class="text-light">${rawFmt(res.analisa['Outlet Sand Filter'].ec, 0, 'µS/cm')}</strong>
                                </div>
                            </div>
                            <div class="p-3 bg-info-subtle text-dark rounded-3 border border-info">
                                <span class="fw-bold d-block text-uppercase small">Formula Efisiensi (Removal)</span>
                                <code class="d-block mt-1 font-monospace text-dark" style="font-size: 10px;">((DAF - Sandfilter) / DAF) * 100</code>
                            </div>
                        </div>
                    `;
                        break;

                    case 'outlet':
                        title = "Outlet (Effluent)";
                        badge =
                            `<span class="badge-removal">Total Removal: TSS ${rawFmt(res.removals.total.tss, 1, '%')} | COD ${rawFmt(res.removals.total.cod, 1, '%')}</span>`;
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Effluent Final discharge</h6>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                                <span class="text-muted small d-block">PROSES HARIAN</span>
                                <div class="d-flex justify-content-between mt-2">
                                    <span>Volume Debit 2:</span>
                                    <strong class="text-info">${rawFmt(res.proses.debit2, 1, 'm³')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>Pit Outlet Sum:</span>
                                    <strong class="text-info">${rawFmt(res.proses.pit_outlet, 1, 'm³')}</strong>
                                </div>
                            </div>
                            <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                                <span class="text-muted small d-block">ANALISA PARAMETER OUTLET</span>
                                <div class="d-flex justify-content-between mt-2">
                                    <span>pH:</span>
                                    <strong class="text-light">${rawFmt(res.analisa.Effluent.ph, 1)}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>TSS:</span>
                                    <strong class="text-light">${rawFmt(res.analisa.Effluent.tss, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>COD:</span>
                                    <strong class="text-light">${rawFmt(res.analisa.Effluent.cod, 0, 'ppm')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>EC:</span>
                                    <strong class="text-light">${rawFmt(res.analisa.Effluent.ec, 0, 'µS/cm')}</strong>
                                </div>
                            </div>
                            <div class="p-3 bg-info-subtle text-dark rounded-3 border border-info">
                                <span class="fw-bold d-block text-uppercase small text-dark">Formula Total Removal</span>
                                <code class="d-block mt-1 font-monospace text-dark" style="font-size: 10px;">((Influent - Outlet) / Influent) * 100</code>
                            </div>
                        </div>
                    `;
                        break;

                    case 'drain_lumpur':
                        title = "Drain Lumpur";
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Sludge Discharge</h6>
                        </div>
                        <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                            <span class="text-muted small d-block">PROSES SLUDGE</span>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-white fw-semibold">Volume Lumpur Terbuang:</span>
                                <span class="fs-16 fw-bold text-info">${rawFmt(res.sludge.drain_lumpur, 1, 'm³')}</span>
                            </div>
                        </div>
                    `;
                        break;

                    case 'screwpress':
                        title = "Screwpress";
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Dewatering Unit (SCP)</h6>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                                <span class="text-muted small d-block">KINERJA HARIAN</span>
                                <div class="d-flex justify-content-between mt-2">
                                    <span>Running Hour (RH):</span>
                                    <strong class="text-info">${rawFmt(res.sludge.running_hour_scp, 1, 'jam')}</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>Sludge Content:</span>
                                    <strong class="text-info">${rawFmt(res.sludge.sludge_content, 1, '%')}</strong>
                                </div>
                            </div>
                        </div>
                    `;
                        break;

                    case 'ibc_tank':
                        title = "IBC Tank";
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Sludge Storage</h6>
                        </div>
                        <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                            <span class="text-muted small d-block">PROSES SLUDGE</span>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-white fw-semibold">Volume Hasil Lumpur:</span>
                                <span class="fs-16 fw-bold text-info">${rawFmt(res.sludge.hasil_lumpur, 1, 'm³')}</span>
                            </div>
                        </div>
                    `;
                        break;

                    case 'pengangkutan_sludge':
                        title = "Pengangkutan Sludge";
                        html = `
                        <div class="mb-4">
                            <span class="text-muted small">KATEGORI</span>
                            <h6 class="text-white fw-bold">Sludge Disposal (Weekly)</h6>
                        </div>
                        <div class="p-3 bg-dark-subtle rounded-3 border border-secondary">
                            <span class="text-muted small d-block">DISPOSAL REKAP MINGGUAN</span>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-white fw-semibold">Jumlah Pengangkutan:</span>
                                <span class="fs-16 fw-bold text-info">${rawFmt(res.sludge.pengangkutan, 0, 'm³')}</span>
                            </div>
                        </div>
                    `;
                        break;
                }

                $('#inspect-title').html(title + " " + (badge ? "<br>" + badge : ""));
                $('#inspect-content').html(html);
            }

            // Auto Rotation Logic
            const unitSequence = [
                'pit_garam',
                'buffer_pit_garam',
                'pit_sparta',
                'step3',
                'equalisasi',
                'anaerob',
                'aerob',
                'pit_domestik',
                'lumpur_aktif',
                'daf',
                'sandfilter',
                'outlet',
                'drain_lumpur',
                'screwpress',
                'ibc_tank',
                'pengangkutan_sludge'
            ];

            let rotationInterval = null;
            let currentRotationIndex = 4; // Start at equalisasi

            function startRotation() {
                if (rotationInterval) clearInterval(rotationInterval);
                $('#btn-play-pause').html('<i class="mdi mdi-pause"></i> <span>Pause Auto</span>');
                $('#btn-play-pause').removeClass('btn-warning').addClass('btn-outline-warning');

                rotationInterval = setInterval(function() {
                    currentRotationIndex = (currentRotationIndex + 1) % unitSequence.length;
                    let targetUnit = unitSequence[currentRotationIndex];
                    let nodeEl = $('#node-' + targetUnit);
                    if (nodeEl.length) {
                        nodeEl.trigger('click');
                    }
                }, 10000); // 10 seconds
            }

            function stopRotation() {
                if (rotationInterval) {
                    clearInterval(rotationInterval);
                    rotationInterval = null;
                }
                $('#btn-play-pause').html('<i class="mdi mdi-play"></i> <span>Auto Play</span>');
                $('#btn-play-pause').removeClass('btn-outline-warning').addClass('btn-warning');
            }

            // Play / Pause button click
            $('#btn-play-pause').click(function() {
                if (rotationInterval) {
                    stopRotation();
                } else {
                    startRotation();
                }
            });

            // Initially load data
            loadVisualData();

            // Listen for filter date changes
            $('#filter_tanggal').change(function() {
                loadVisualData($(this).val());
            });

            // Manual refresh button click
            $('#btn-refresh').click(function() {
                loadVisualData($('#filter_tanggal').val());
            });

            // Trigger redrawing connection lines on page load and window resizing
            setTimeout(drawConnectors, 500);
            $(window).resize(drawConnectors);
            $('.visualisasi-container').scroll(drawConnectors);
        });
    </script>
@endsection
