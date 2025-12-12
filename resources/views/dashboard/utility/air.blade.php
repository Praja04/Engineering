@extends('layouts.app')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        <!-- Page Header with Gradient Background -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-gradient-primary p-4 rounded-3 mb-4 text-white shadow-lg">
                    <div>
                        <h4 class="mb-1 text-white fw-bold">Engineering Dashboard - Air</h4>
                        <p class="mb-0 opacity-75">Monitor konsumsi air secara real-time</p>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0 bg-transparent">
                            <li class="breadcrumb-item">
                                <a href="javascript: void(0);" class="text-white-50">Dashboards</a>
                            </li>
                            <li class="breadcrumb-item active text-white">
                                Air
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

       

        <!-- Charts Grid -->
        <div class="row g-4">
            <!-- Water Usage Process & Support -->
            <div class="col-xxl-12 col-lg-12">
                <div class="card chart-card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-bottom-0 pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1 fw-semibold">Air Proses & Support</h5>
                                <p class="text-muted mb-0 small">Pemakaian air untuk utilitas</p>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline btn-sm dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                                    <i class="ri-calendar-line me-1"></i>
                                    <span id="selectedBulanAir">Pilih Periode</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end p-3 border-0 shadow-lg" style="min-width: 300px;">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Dari Tanggal</label>
                                        <input type="date" id="startDateAir" class="form-control form-control-sm">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                                        <input type="date" id="endDateAir" class="form-control form-control-sm">
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" id="applyAirRange">
                                        <i class="ri-refresh-line me-1"></i>Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="chart-loading" id="loading-air">
                            <div class="text-center p-5">
                                <div class="spinner-grow text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted mb-0">Memuat data pemakaian air...</p>
                            </div>
                        </div>
                        <div id="pemakaian-air-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>

            <!-- Raw Water Usage -->
            <div class="col-xxl-12 col-lg-12">
                <div class="card chart-card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-bottom-0 pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1 fw-semibold">Air Raw</h5>
                                <p class="text-muted mb-0 small">Pemakaian air mentah</p>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline btn-sm dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                                    <i class="ri-calendar-line me-1"></i>
                                    <span id="selectedBulanAirRaw">Pilih Periode</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end p-3 border-0 shadow-lg" style="min-width: 300px;">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Dari Tanggal</label>
                                        <input type="date" id="startDateAirRaw" class="form-control form-control-sm">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                                        <input type="date" id="endDateAirRaw" class="form-control form-control-sm">
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" id="applyAirRawRange">
                                        <i class="ri-refresh-line me-1"></i>Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="chart-loading" id="loading-air-raw">
                            <div class="text-center p-5">
                                <div class="spinner-grow text-info" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted mb-0">Memuat data air raw...</p>
                            </div>
                        </div>
                        <div id="pemakaian-air-chart-raw" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Analysis Section -->
        <div class="row g-4 mt-2">
            <!-- Water Trend -->
            <div class="col-xl-12">
                <div class="card trend-card border-0 shadow-sm">
                    <div class="card-header bg-gradient-primary text-white border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1 text-white fw-semibold">Trend Pemakaian Air</h5>
                                <p class="mb-0 opacity-75 small">Analisis pola konsumsi air harian</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="month" id="filter_bulan" class="form-control form-control-sm bg-white" style="width: 140px;">
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="chart-loading" id="loading-trend-air">
                            <div class="text-center p-5">
                                <div class="spinner-grow text-primary" role="status"></div>
                                <p class="mt-3 text-muted mb-0">Memuat trend data air...</p>
                            </div>
                        </div>
                        <div id="pemakaian_air_chart" class="apex-charts"></div>
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
                        colors: '#8c9097'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: '12px',
                        colors: '#8c9097'
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
    function fetchAir(start, end) {
        showLoading('loading-air');
        $.getJSON(`{{ url('utility/top5/air') }}?start_date=${start}&end_date=${end}`, data => {
            renderBar(data.map(d => d.jenis_pemakaian), data.map(d => +d.total_pemakaian),
                "#pemakaian-air-chart", 'air', 'm³', '#3B82F6');
        }).always(() => hideLoading('loading-air'));
    }

    // Define water quotas per well
    const waterQuotas = {
        'Sumur 1': 59, // m³ per day
        'Sumur 2': 73, // m³ per day
        'Sumur 4': 28, // m³ per day
        'Sumur 5': 32, // m³ per day
        'PDAM': 14 // m³ per day
    };

    // Fetch Air Raw with quota comparison
    function fetchAirRaw(start, end) {
        showLoading('loading-air-raw');

        $.getJSON(`{{ url('utility/top5/air/raw') }}?start_date=${start}&end_date=${end}`, data => {
            // Calculate number of days
            const startDate = new Date(start);
            const endDate = new Date(end);
            const daysDiff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;

            // Calculate quota for each source
            const processedData = data.map(item => {
                const quota = waterQuotas[item.jenis_pemakaian] || 0;
                const expectedUsage = quota * daysDiff;
                const variance = item.total_pemakaian - expectedUsage;
                const percentageUsage = expectedUsage > 0 ? (item.total_pemakaian / expectedUsage * 100).toFixed(1) : 0;

                return {
                    ...item,
                    quota: quota,
                    expectedUsage: expectedUsage,
                    variance: variance,
                    percentageUsage: percentageUsage,
                    status: item.total_pemakaian > expectedUsage ? 'exceeded' : 'normal'
                };
            });

            // Prepare chart data - showing actual vs expected
            const labels = processedData.map(d => d.jenis_pemakaian);
            const actualUsage = processedData.map(d => d.total_pemakaian);
            const expectedUsage = processedData.map(d => d.expectedUsage);

            renderComparisonBar(labels, actualUsage, expectedUsage, "#pemakaian-air-chart-raw", 'airRaw', 'm³', processedData);

        }).always(() => hideLoading('loading-air-raw'));
    }

    // Render comparison bar chart (actual vs quota)
    function renderComparisonBar(labels, actualValues, expectedValues, selector, key, unit, dataDetails) {
        const options = baseOptions('bar');

        options.series = [{
                name: `Pemakaian Aktual (${unit})`,
                data: actualValues
            },
            {
                name: `Target Quota (${unit})`,
                data: expectedValues
            }
        ];

        options.xaxis.categories = labels;
        options.colors = ['#06B6D4', '#D1D5DB'];

        options.chart.height = 400;

        options.dataLabels = {
            enabled: true,
            position: 'top',
            formatter: function(val) {
                return val + ' ' + unit;
            },
            style: {
                fontSize: '11px',
                fontWeight: 600,
                colors: ['#0891B2', '#6B7280']
            },
            offsetY: -20
        };

        const maxValue = Math.max(...actualValues, ...expectedValues);
        options.yaxis = {
            max: maxValue * 1.2,
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
                formatter: (val, {
                    series,
                    seriesIndex,
                    dataPointIndex
                }) => {
                    const detail = dataDetails[dataPointIndex];
                    if (seriesIndex === 0) {
                        const status = detail.status === 'exceeded' ?
                            `<span style="color: #EF4444;">⚠️ Melebihi ${Math.abs(detail.variance).toFixed(0)} ${unit}</span>` :
                            `<span style="color: #10B981;">✓ Normal</span>`;
                        return `${val} ${unit} (${detail.percentageUsage}% dari target)<br/>${status}`;
                    } else {
                        return `${val} ${unit} (Target)`;
                    }
                }
            }
        };

        options.plotOptions = {
            bar: {
                columnWidth: '70%',
                borderRadius: 6,
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

        options.states = {
            hover: {
                filter: {
                    type: 'darken',
                    value: 0.15
                }
            }
        };

        if (chartInstances[key]) {
            chartInstances[key].updateOptions(options, false, false);
        } else {
            chartInstances[key] = new ApexCharts(document.querySelector(selector), options);
            chartInstances[key].render();
        }

        // Display quota summary table
        displayQuotaSummary(dataDetails, selector);
    }

    // Display quota analysis summary
    function displayQuotaSummary(dataDetails, chartSelector) {
        const container = $(chartSelector).parent();
        let summaryHtml = `
        <div class="quota-summary mt-4 p-3 bg-light rounded" style="border-left: 4px solid #06B6D4;">
            <h6 class="fw-semibold text-muted mb-3">📊 Analisis Quota</h6>
            <div class="row g-2">
    `;

        dataDetails.forEach(item => {
            const statusBadge = item.status === 'exceeded' ?
                '<span class="badge bg-danger-subtle text-danger">Melebihi</span>' :
                '<span class="badge bg-success-subtle text-success">Normal</span>';

            summaryHtml += `
            <div class="col-md-6 col-lg-4">
                <div class="d-flex justify-content-between align-items-center small p-2 border rounded" >
                    <div>
                        <strong>${item.jenis_pemakaian}</strong><br>
                        <span class="text-muted">Actual: ${item.total_pemakaian} m³</span><br>
                        <span class="text-muted">Target: ${item.expectedUsage.toFixed(0)} m³</span>
                    </div>
                    <div class="text-end">
                        ${statusBadge}<br>
                        <small class="text-muted">${item.percentageUsage}%</small>
                    </div>
                </div>
            </div>
        `;
        });

        summaryHtml += `
            </div>
        </div>
    `;

        // Insert summary below chart
        container.find('.quota-summary').remove();
        $(chartSelector).after(summaryHtml);
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
        fetchAir(start, end);
        fetchAirRaw(start, end);

        // Trend chart
        const fetchTrendAir = setupTrend("#pemakaian_air_chart", "{{ url('utility/trend-pemakaian-air') }}", "m³", "trendAir", "loading-trend-air");

        // Event listener filter bulan
        $('#filter_bulan').on('change', function() {
            fetchTrendAir({
                bulan: this.value
            });
        });

        // Event listener filter date-range Air Proses & Support
        $("#applyAirRange").on("click", function() {
            const start = $("#startDateAir").val();
            const end = $("#endDateAir").val();
            if (!start || !end) {
                alert("Pilih tanggal awal & akhir!");
                return;
            }
            $("#selectedBulanAir").text(`${start} s/d ${end}`);
            fetchAir(start, end);
        });

        // Event listener filter date-range Air Raw
        $("#applyAirRawRange").on("click", function() {
            const start = $("#startDateAirRaw").val();
            const end = $("#endDateAirRaw").val();
            if (!start || !end) {
                alert("Pilih tanggal awal & akhir!");
                return;
            }
            $("#selectedBulanAirRaw").text(`${start} s/d ${end}`);
            fetchAirRaw(start, end);
        });
    });
</script>

<!-- Enhanced CSS Styles -->
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
        --info-gradient: linear-gradient(135deg, #06B6D4 0%, #0891B2 100%);
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        /* background-color: #F8FAFC; */
    }

    .page-content {
        animation: fadeInUp 0.6s ease-out;
    }

    .bg-gradient-primary {
        background: var(--primary-gradient);
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
        background: linear-gradient(135deg, #FFFFFF 0%, #EFF6FF 100%);
        border-left: 4px solid #3B82F6;
    }

    .chart-card {
        /* background: #FFFFFF; */
    }

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
        border-color: #3B82F6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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

    .badge.bg-danger-subtle {
        background-color: #FEE2E2;
        color: #DC2626;
    }

    .badge.bg-success-subtle {
        background-color: #D1FAE5;
        color: #059669;
    }

    .badge.bg-info-subtle {
        background-color: #E0F2FE;
        color: #0369A1;
    }
</style>

@endsection