@extends('layouts.app')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        <!-- Page Header with Gradient Background -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between p-4 rounded-3 mb-4 text-white shadow-lg" style="background: linear-gradient(135deg, #28a745, #0d6efd);">
                    <div>

                        <h4 class="mb-1 text-white fw-bold">Engineering Dashboard - Chemical</h4>
                        <p class="mb-0 opacity-75">Monitor konsumsi bahan kimia secara real-time</p>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0 bg-transparent">
                            <li class="breadcrumb-item">
                                <a href="javascript: void(0);" class="text-white-50">Dashboards</a>
                            </li>
                            <li class="breadcrumb-item active text-white">Chemical</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid - Chemical Utility -->
        <div class="row g-4">
            <div class="col-xxl-12 col-lg-12">
                <div class="card chart-card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-bottom-0 pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1 fw-semibold">
                                    <i class="ri-drop-line text-primary me-2"></i>Pemakaian Chemical Utility
                                </h5>
                                <p class="text-muted mb-0 small">SCF, SRTF, PT-100, SMBS, B4, SRF, Chlorin</p>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline btn-sm dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                                    <i class="ri-calendar-line me-1"></i>
                                    <span id="selectedBulanUtility">Pilih Periode</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end p-3 border-0 shadow-lg" style="min-width: 300px;">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Dari Tanggal</label>
                                        <input type="date" id="startDateUtility" class="form-control form-control-sm">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                                        <input type="date" id="endDateUtility" class="form-control form-control-sm">
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" id="applyUtilityRange">
                                        <i class="ri-refresh-line me-1"></i>Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="chart-loading" id="loading-utility">
                            <div class="text-center p-5">
                                <div class="spinner-grow text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted mb-0">Memuat data chemical utility...</p>
                            </div>
                        </div>
                        <div id="pemakaian-utility-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid - Chemical WWTP -->
        <div class="row g-4 mt-2">
            <div class="col-xxl-12 col-lg-12">
                <div class="card chart-card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-bottom-0 pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1 fw-semibold">
                                    <i class="ri-water-flash-line text-success me-2"></i>Pemakaian Chemical WWTP
                                </h5>
                                <p class="text-muted mb-0 small">PAC, BE-100, C-204, C-9040, Denfloc, NaOH, dll</p>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline btn-sm dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                                    <i class="ri-calendar-line me-1"></i>
                                    <span id="selectedBulanWWTP">Pilih Periode</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end p-3 border-0 shadow-lg" style="min-width: 300px;">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Dari Tanggal</label>
                                        <input type="date" id="startDateWWTP" class="form-control form-control-sm">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                                        <input type="date" id="endDateWWTP" class="form-control form-control-sm">
                                    </div>
                                    <button class="btn btn-success btn-sm w-100" id="applyWWTPRange">
                                        <i class="ri-refresh-line me-1"></i>Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="chart-loading" id="loading-wwtp">
                            <div class="text-center p-5">
                                <div class="spinner-grow text-success" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted mb-0">Memuat data chemical WWTP...</p>
                            </div>
                        </div>
                        <div id="pemakaian-wwtp-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Analysis Section - Chemical Utility -->
        <div class="row g-4 mt-2">
            <div class="col-xl-12">
                <div class="card trend-card border-0 shadow-sm">
                    <div class="card-header bg-gradient-primary text-white border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1 text-white fw-semibold">Trend Pemakaian Chemical Utility</h5>
                                <p class="mb-0 opacity-75 small">Analisis pola konsumsi chemical utility harian</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="month" id="filter_bulan_utility" class="form-control form-control-sm bg-white" style="width: 140px;">
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="chart-loading" id="loading-trend-utility">
                            <div class="text-center p-5">
                                <div class="spinner-grow text-primary" role="status"></div>
                                <p class="mt-3 text-muted mb-0">Memuat trend data chemical utility...</p>
                            </div>
                        </div>
                        <div id="pemakaian_utility_chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Analysis Section - Chemical WWTP -->
        <div class="row g-4 mt-2">
            <div class="col-xl-12">
                <div class="card trend-card border-0 shadow-sm">
                    <div class="card-header bg-gradient-success text-white border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1 text-white fw-semibold">Trend Pemakaian Chemical WWTP</h5>
                                <p class="mb-0 opacity-75 small">Analisis pola konsumsi chemical WWTP harian</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="month" id="filter_bulan_wwtp" class="form-control form-control-sm bg-white" style="width: 140px;">
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="chart-loading" id="loading-trend-wwtp">
                            <div class="text-center p-5">
                                <div class="spinner-grow text-success" role="status"></div>
                                <p class="mt-3 text-muted mb-0">Memuat trend data chemical WWTP...</p>
                            </div>
                        </div>
                        <div id="pemakaian_wwtp_chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const chartInstances = {};

    function showLoading(id) {
        $(`#${id}`).fadeIn(200);
    }

    function hideLoading(id) {
        $(`#${id}`).fadeOut(300);
    }

    function baseOptions(type = 'bar') {
        return {
            chart: {
                type,
                height: 300,
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: type === 'line' ? 'smooth' : 'straight',
                width: 2
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 3
            },
            xaxis: {
                labels: {
                    style: {
                        fontSize: '12px',
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: '12px',
                    }
                }
            },
            legend: {
                show: true,
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: 12,
                markers: {
                    radius: 4,
                    width: 12,
                    height: 12
                }
            },
            noData: {
                text: 'Memuat data...'
            }
        };
    }

    function renderBar(labels, values, selector, key, unit, color) {
        const options = baseOptions('bar');
        options.series = [{
            name: `Total (${unit})`,
            data: values
        }];
        options.xaxis.categories = labels;
        options.colors = [color];

        options.dataLabels = {
            enabled: true,
            position: 'top',
            formatter: function(val) {
                return val + ' ' + unit;
            },
            style: {
                fontSize: '12px',
                fontWeight: 600,
                colors: ['#1F2937']
            },
            offsetY: -20
        };

        options.chart.height = 350;

        const maxValue = Math.max(...values);
        options.yaxis = {
            max: maxValue * 1.15,
            title: {
                text: `Pemakaian (${unit})`
            },
            labels: {
                style: {
                    fontSize: '12px',
                    colors: '#8c9097'
                }
            }
        };

        options.tooltip = {
            y: {
                formatter: val => `${val} ${unit}`
            }
        };

        options.plotOptions = {
            bar: {
                columnWidth: '60%',
                borderRadius: 8,
                dataLabels: {
                    position: 'top'
                }
            }
        };

        options.legend = {
            position: 'bottom',
            horizontalAlign: 'center',
            fontSize: 12,
            markers: {
                radius: 4,
                width: 12,
                height: 12
            }
        };

        if (chartInstances[key]) {
            chartInstances[key].updateOptions(options, false, false);
        } else {
            chartInstances[key] = new ApexCharts(document.querySelector(selector), options);
            chartInstances[key].render();
        }
    }

    function fetchChemicalByArea(start, end, area, selector, loadingId, key, color) {
        showLoading(loadingId);
        $.getJSON(`{{ url('utility/top5/chemical') }}?start_date=${start}&end_date=${end}&area=${area}`, data => {
            if (!data || data.length === 0) {
                showEmptyState(selector, key, `Tidak ada data ${area.toUpperCase()} di rentang tanggal ini`);
            } else {
                renderBar(
                    data.map(d => d.jenis_pemakaian),
                    data.map(d => +d.total_pemakaian),
                    selector,
                    key,
                    'kg',
                    color
                );
            }
        }).fail(() => {
            showEmptyState(selector, key, 'Gagal memuat data. Silakan coba lagi.');
        }).always(() => hideLoading(loadingId));
    }

    function showEmptyState(selector, key, message) {
        const options = baseOptions('bar');
        options.series = [{
            name: 'Data',
            data: []
        }];
        options.xaxis.categories = [];
        options.chart.height = 350;
        options.noData = {
            text: message,
            align: 'center',
            verticalAlign: 'middle',
            offsetX: 0,
            offsetY: 0,
            style: {
                color: '#9CA3AF',
                fontSize: '16px',
                fontFamily: 'Inter, sans-serif'
            }
        };

        if (chartInstances[key]) {
            chartInstances[key].updateOptions(options, true, true);
        } else {
            chartInstances[key] = new ApexCharts(document.querySelector(selector), options);
            chartInstances[key].render();
        }
    }

    function setupTrend(selector, url, ySuffix, key, loadingId, area) {
        const options = baseOptions('line');
        options.chart.height = 350;
        options.series = [];
        options.xaxis.type = 'datetime';
        options.yaxis.title = {
            text: `Pemakaian (${ySuffix})`
        };
        options.tooltip = {
            x: {
                format: 'dd MMM yyyy'
            },
            y: {
                formatter: v => `${v} ${ySuffix}`
            }
        };
        options.noData = {
            text: 'Tidak ada data untuk periode ini',
            align: 'center',
            verticalAlign: 'middle',
            style: {
                color: '#9CA3AF',
                fontSize: '16px',
                fontFamily: 'Inter, sans-serif'
            }
        };

        const chart = new ApexCharts(document.querySelector(selector), options);
        chart.render();
        chartInstances[key] = chart;

        function fetchTrend(params = {}) {
            // Selalu tambahkan area ke params
            const finalParams = {
                ...params,
                area
            };

            showLoading(loadingId);
            $.getJSON(url, finalParams, data => {
                if (!data || data.length === 0) {
                    chart.updateSeries([]);
                } else {
                    chart.updateSeries(data);
                }
            }).fail(() => {
                chart.updateSeries([]);
            }).always(() => hideLoading(loadingId));
        }

        fetchTrend(); // initial load dengan area
        return fetchTrend;
    }

    $(function() {
        const today = new Date();
        const start = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().slice(0, 10);
        const end = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().slice(0, 10);

        // Load bar charts
        fetchChemicalByArea(start, end, 'utility', "#pemakaian-utility-chart", "loading-utility", 'utility', '#0EA5E9');
        fetchChemicalByArea(start, end, 'wwtp', "#pemakaian-wwtp-chart", "loading-wwtp", 'wwtp', '#10B981');

        // Trend charts - dengan area parameter
        const fetchTrendUtility = setupTrend("#pemakaian_utility_chart", "{{ url('utility/trend-pemakaian-chemical') }}", "kg", "trendUtility", "loading-trend-utility", "utility");
        const fetchTrendWWTP = setupTrend("#pemakaian_wwtp_chart", "{{ url('utility/trend-pemakaian-chemical') }}", "kg", "trendWWTP", "loading-trend-wwtp", "wwtp");

        // Event listeners - Utility
        $('#filter_bulan_utility').on('change', function() {
            fetchTrendUtility({
                bulan: this.value
            });
        });

        $("#applyUtilityRange").on("click", function() {
            const start = $("#startDateUtility").val();
            const end = $("#endDateUtility").val();
            if (!start || !end) {
                alert("Pilih tanggal awal & akhir!");
                return;
            }
            $("#selectedBulanUtility").text(`${start} s/d ${end}`);
            fetchChemicalByArea(start, end, 'utility', "#pemakaian-utility-chart", "loading-utility", 'utility', '#0EA5E9');
        });

        // Event listeners - WWTP
        $('#filter_bulan_wwtp').on('change', function() {
            fetchTrendWWTP({
                bulan: this.value
            });
        });

        $("#applyWWTPRange").on("click", function() {
            const start = $("#startDateWWTP").val();
            const end = $("#endDateWWTP").val();
            if (!start || !end) {
                alert("Pilih tanggal awal & akhir!");
                return;
            }
            $("#selectedBulanWWTP").text(`${start} s/d ${end}`);
            fetchChemicalByArea(start, end, 'wwtp', "#pemakaian-wwtp-chart", "loading-wwtp", 'wwtp', '#10B981');
        });
    });
</script>

<!-- Enhanced CSS Styles -->
<style>
    :root {
        --success-gradient: linear-gradient(135deg, #10B981 0%, #059669 100%);
        --primary-gradient: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%);
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .page-content {
        animation: fadeInUp 0.6s ease-out;
    }

    .bg-gradient-success {
        background: var(--success-gradient);
    }

    .bg-gradient-primary {
        background: var(--primary-gradient);
    }

    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .trend-card .card-header {
        border-radius: 16px 16px 0 0;
        padding: 1.5rem;
    }

    .chart-loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(4px);
        z-index: 10;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .spinner-grow {
        animation-duration: 1.5s;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border-radius: 12px;
        padding: 1rem;
        backdrop-filter: blur(16px);
        background: rgba(255, 255, 255, 0.95);
    }

    .form-control {
        border-radius: 8px;
        border: 2px solid #E2E8F0;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .form-control:focus {
        border-color: #10B981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        transform: scale(1.02);
    }

    .breadcrumb {
        background: transparent;
        margin: 0;
    }

    .breadcrumb-item a {
        text-decoration: none;
        transition: opacity 0.2s ease;
    }

    .breadcrumb-item a:hover {
        opacity: 0.8;
    }

    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #F1F5F9;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 4px;
        transition: background 0.2s ease;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .card {
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        .page-title-box {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }

        .dropdown-menu {
            min-width: 280px !important;
        }

        .apex-charts {
            height: 300px !important;
        }
    }
</style>

@endsection