@extends('layouts.app')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        <!-- Page Header with Gradient Background -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-gradient-success p-4 rounded-3 mb-4 text-white shadow-lg">
                    <div>
                        <h4 class="mb-1 text-white fw-bold">Engineering Dashboard - Chemical</h4>
                        <p class="mb-0 opacity-75">Monitor konsumsi bahan kimia secara real-time</p>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0 bg-transparent">
                            <li class="breadcrumb-item">
                                <a href="javascript: void(0);" class="text-white-50">Dashboards</a>
                            </li>
                            <li class="breadcrumb-item active text-white">
                                Chemical
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

      
        <!-- Charts Grid -->
        <div class="row g-4">
            <!-- Chemical Usage -->
            <div class="col-xxl-12 col-lg-12">
                <div class="card chart-card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-bottom-0 pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1 fw-semibold">Pemakaian Chemical</h5>
                                <p class="text-muted mb-0 small">Konsumsi bahan kimia</p>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline btn-sm dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                                    <i class="ri-calendar-line me-1"></i>
                                    <span id="selectedBulanChemical">Pilih Periode</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end p-3 border-0 shadow-lg" style="min-width: 300px;">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Dari Tanggal</label>
                                        <input type="date" id="startDateChemical" class="form-control form-control-sm">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                                        <input type="date" id="endDateChemical" class="form-control form-control-sm">
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" id="applyChemicalRange">
                                        <i class="ri-refresh-line me-1"></i>Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="chart-loading" id="loading-chemical">
                            <div class="text-center p-5">
                                <div class="spinner-grow text-success" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted mb-0">Memuat data chemical...</p>
                            </div>
                        </div>
                        <div id="pemakaian-chemical-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Analysis Section -->
        <div class="row g-4 mt-2">
            <!-- Chemical Trend -->
            <div class="col-xl-12">
                <div class="card trend-card border-0 shadow-sm">
                    <div class="card-header bg-gradient-success text-white border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1 text-white fw-semibold">Trend Pemakaian Chemical</h5>
                                <p class="mb-0 opacity-75 small">Analisis pola konsumsi chemical harian</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="month" id="filter_bulan_chemical" class="form-control form-control-sm bg-white" style="width: 140px;">
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="chart-loading" id="loading-trend-chemical">
                            <div class="text-center p-5">
                                <div class="spinner-grow text-success" role="status"></div>
                                <p class="mt-3 text-muted mb-0">Memuat trend data chemical...</p>
                            </div>
                        </div>
                        <div id="pemakaian_chemical_chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Simpan instance chart
    const chartInstances = {};

    function showLoading(id) {
        $(`#${id}`).fadeIn(200);
    }

    function hideLoading(id) {
        $(`#${id}`).fadeOut(300);
    }

    // Base options
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
                        // colors: '#8c9097'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: '12px',
                        // colors: '#8c9097'
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

    // Render bar chart
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

    // Fetch data untuk bar chart
    function fetchChemical(start, end) {
        showLoading('loading-chemical');
        $.getJSON(`{{ url('utility/top5/chemical') }}?start_date=${start}&end_date=${end}`, data => {
            renderBar(data.map(d => d.jenis_pemakaian), data.map(d => +d.total_pemakaian),
                "#pemakaian-chemical-chart", 'chemical', 'kg', '#10B981');
        }).always(() => hideLoading('loading-chemical'));
    }

    // Trend chart setup
    function setupTrend(selector, url, ySuffix, key, loadingId) {
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

        const chart = new ApexCharts(document.querySelector(selector), options);
        chart.render();
        chartInstances[key] = chart;

        function fetchTrend(params = {}) {
            showLoading(loadingId);
            $.getJSON(url, params, data => {
                chart.updateSeries(data);
            }).always(() => hideLoading(loadingId));
        }

        fetchTrend(); // initial load
        return fetchTrend;
    }

    // Init
    $(function() {
        const today = new Date();
        const start = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().slice(0, 10);
        const end = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().slice(0, 10);

        // Load bar chart
        fetchChemical(start, end);

        // Trend chart
        const fetchTrendChemical = setupTrend("#pemakaian_chemical_chart", "{{ url('utility/trend-pemakaian-chemical') }}", "kg", "trendChemical", "loading-trend-chemical");

        // Event listener filter bulan
        $('#filter_bulan_chemical').on('change', function() {
            fetchTrendChemical({
                bulan: this.value
            });
        });

        // Event listener filter date-range
        $("#applyChemicalRange").on("click", function() {
            const start = $("#startDateChemical").val();
            const end = $("#endDateChemical").val();
            if (!start || !end) {
                alert("Pilih tanggal awal & akhir!");
                return;
            }
            $("#selectedBulanChemical").text(`${start} s/d ${end}`);
            fetchChemical(start, end);
        });
    });
</script>

<!-- Enhanced CSS Styles -->
<style>
    :root {
        --success-gradient: linear-gradient(135deg, #10B981 0%, #059669 100%);
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        /* background-color: #F8FAFC; */
    }

    .page-content {
        animation: fadeInUp 0.6s ease-out;
    }

    .bg-gradient-success {
        background: var(--success-gradient);
    }

    /* Enhanced Card Styles */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        /* background: #FFFFFF; */
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .stat-card {
        background: linear-gradient(135deg, #FFFFFF 0%, #D1FAE5 100%);
        border-left: 4px solid #10B981;
    }

    /* .chart-card {
        background: #FFFFFF;
    } */

    .trend-card .card-header {
        border-radius: 16px 16px 0 0;
        padding: 1.5rem;
    }

    /* Enhanced Loading States */
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

    /* Enhanced Buttons */
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

    /* Enhanced Dropdowns */
    .dropdown-menu {
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border-radius: 12px;
        padding: 1rem;
        backdrop-filter: blur(16px);
        background: rgba(255, 255, 255, 0.95);
    }

    .dropdown-toggle::after {
        transition: transform 0.2s ease;
    }

    .dropdown[aria-expanded="true"] .dropdown-toggle::after {
        transform: rotate(180deg);
    }

    /* Enhanced Form Controls */
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

    /* Enhanced Breadcrumbs */
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

    /* Enhanced Avatar */
    .avatar-sm {
        width: 40px;
        height: 40px;
        font-size: 14px;
    }

    /* Enhanced Scrollbar */
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

    /* Enhanced Animations */
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

    /* Responsive Enhancements */
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

    /* Utility Classes */
    .tracking-wide {
        letter-spacing: 0.05em;
    }

    /* Badge Styles */
    .badge {
        padding: 0.35rem 0.65rem;
        font-weight: 500;
        border-radius: 6px;
    }

    .badge.bg-success-subtle {
        background-color: #D1FAE5;
        color: #059669;
    }
</style>

@endsection