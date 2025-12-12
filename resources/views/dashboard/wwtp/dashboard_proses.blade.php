@extends('layouts.app')

@section('title', 'Dashboard WWTP')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dashboard WWTP Proses</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Utility</a></li>
                            <li class="breadcrumb-item active">WWTP Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Records</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value" id="totalRecords">0</span>
                                </h4>
                                <p class="text-muted mb-0 text-truncate">All time data</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle rounded fs-3">
                                    <i class="bx bx-data text-primary"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Influent Records</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value" id="totalInfluent">0</span>
                                </h4>
                                <p class="text-muted mb-0">
                                    <span id="weeklyInfluentStatus" class="badge bg-success-subtle text-success"></span>
                                </p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info-subtle rounded fs-3">
                                    <i class="bx bx-import text-info"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Effluent Records</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value" id="totalEffluent">0</span>
                                </h4>
                                <p class="text-muted mb-0">
                                    <span id="weeklyEffluentCount" class="badge bg-success-subtle text-success"></span>
                                </p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success-subtle rounded fs-3">
                                    <i class="bx bx-export text-success"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Last Update</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h6 class="fs-16 fw-semibold mb-4">
                                    <span id="lastUpdate">-</span>
                                </h6>
                                <p class="text-muted mb-0 text-truncate">Latest record date</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning-subtle rounded fs-3">
                                    <i class="bx bx-calendar text-warning"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Average Cards -->
        <div class="row">
            <div class="col-xl-6">
                <div class="card card-height-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info-subtle text-info rounded fs-3">
                                    <i class="bx bxs-droplet"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-1">Avg Influent (This Month)</p>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                    <span id="monthlyInfluentAvg">0</span> <small class="fs-14 text-muted">m³</small>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card card-height-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success-subtle text-success rounded fs-3">
                                    <i class="bx bxs-droplet-half"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-1">Avg Effluent (This Month)</p>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                    <span id="monthlyEffluentAvg">0</span> <small class="fs-14 text-muted">m³</small>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Influent Trend Chart -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Influent Data Trend</h4>
                        <div class="flex-shrink-0">
                            <select class="form-select form-select-sm" id="influentPeriod" style="width: 150px;">
                                <option value="7">Last 7 Days</option>
                                <option value="14">Last 14 Days</option>
                                <option value="30" selected>Last 30 Days</option>
                                <option value="60">Last 60 Days</option>
                                <option value="90">Last 90 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="influentChart"></div>
                    </div>
                </div>
            </div>

            <!-- Influent Breakdown Pie Chart -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Influent Source Distribution</h4>
                    </div>
                    <div class="card-body">
                        <div id="influentPieChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Effluent Trend Chart -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Effluent Data Trend</h4>
                        <div class="flex-shrink-0">
                            <select class="form-select form-select-sm" id="effluentPeriod" style="width: 150px;">
                                <option value="7">Last 7 Days</option>
                                <option value="14">Last 14 Days</option>
                                <option value="30" selected>Last 30 Days</option>
                                <option value="60">Last 60 Days</option>
                                <option value="90">Last 90 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="effluentChart"></div>
                    </div>
                </div>
            </div>

            <!-- Effluent Breakdown Pie Chart -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Effluent Process Distribution</h4>
                    </div>
                    <div class="card-body">
                        <div id="effluentPieChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Comparison Chart -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">6-Month Comparison</h4>
                    </div>
                    <div class="card-body">
                        <div id="monthlyComparisonChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Records Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Recent Records</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Kategori</th>
                                        <th scope="col">Details</th>
                                        <th scope="col">Total Volume</th>
                                    </tr>
                                </thead>
                                <tbody id="recentRecordsTable">
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            Loading data...
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
</div>

<style>
    .card-animate {
        transition: all 0.3s ease;
    }

    .card-animate:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .counter-value {
        animation: countUp 1s ease-out;
    }

    @keyframes countUp {
        from {
            opacity: 0;
            transform: scale(0.8);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .apexcharts-tooltip {
        background: #fff !important;
        border: 1px solid #e3e6ef !important;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let influentChart, effluentChart, influentPieChart, effluentPieChart, monthlyComparisonChart;
    let allInfluentData = [];
    let allEffluentData = [];

    // Load all data on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadStatistics();
        initCharts();
        loadMonthlyComparison();
        loadRecentRecords();

        // Event listeners
        document.getElementById('influentPeriod').addEventListener('change', updateInfluentChart);
        document.getElementById('effluentPeriod').addEventListener('change', updateEffluentChart);
    });

    // Load Statistics
    async function loadStatistics() {
        try {
            const response = await fetch('/api/wwtp/dashboard/statistics');
            const data = await response.json();

            // Animate counter
            animateValue('totalRecords', 0, data.total_records, 1000);
            animateValue('totalInfluent', 0, data.total_influent, 1000);
            animateValue('totalEffluent', 0, data.total_effluent, 1000);

            // Last update
            if (data.last_update) {
                const date = new Date(data.last_update);
                document.getElementById('lastUpdate').textContent = date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            // Weekly status
            if (data.weekly_influent) {
                document.getElementById('weeklyInfluentStatus').textContent = 'Data minggu ini tersedia';
            } else {
                document.getElementById('weeklyInfluentStatus').textContent = 'Belum ada data minggu ini';
                document.getElementById('weeklyInfluentStatus').className = 'badge bg-warning-subtle text-warning';
            }

            document.getElementById('weeklyEffluentCount').textContent =
                data.weekly_effluent_count + ' record minggu ini';

            // Monthly averages
            document.getElementById('monthlyInfluentAvg').textContent =
                data.monthly_influent_avg || '0';
            document.getElementById('monthlyEffluentAvg').textContent =
                data.monthly_effluent_avg || '0';

        } catch (error) {
            console.error('Error loading statistics:', error);
        }
    }

    // Animate counter
    function animateValue(id, start, end, duration) {
        const obj = document.getElementById(id);
        const range = end - start;
        const increment = end > start ? 1 : -1;
        const stepTime = Math.abs(Math.floor(duration / range));
        let current = start;

        const timer = setInterval(function() {
            current += increment;
            obj.textContent = current;
            if (current == end) {
                clearInterval(timer);
            }
        }, stepTime);
    }

    // Initialize Charts
    function initCharts() {
        // Influent Line Chart
        const influentOptions = {
            series: [{
                name: 'Pit Sparta',
                data: []
            }, {
                name: 'Pit Garam',
                data: []
            }, {
                name: 'Pit Domestik',
                data: []
            }],
            chart: {
                type: 'area',
                height: 320,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#4bc0c0', '#ff6384', '#36a2eb'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                }
            },
            xaxis: {
                categories: [],
                labels: {
                    rotate: -45,
                    rotateAlways: false
                }
            },
            yaxis: {
                title: {
                    text: 'Volume (m³)'
                },
                labels: {
                    formatter: function(value) {
                        return value.toFixed(0) + ' m³';
                    }
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(value) {
                        return value.toFixed(2) + ' m³';
                    }
                }
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        };
        influentChart = new ApexCharts(document.querySelector("#influentChart"), influentOptions);
        influentChart.render();

        // Effluent Line Chart
        const effluentOptions = {
            series: [{
                name: 'Full Proses',
                data: []
            }, {
                name: 'DAF Pre',
                data: []
            }],
            chart: {
                type: 'area',
                height: 320,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#9966ff', '#ff9f40'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                }
            },
            xaxis: {
                categories: [],
                labels: {
                    rotate: -45,
                    rotateAlways: false
                }
            },
            yaxis: {
                title: {
                    text: 'Volume (m³)'
                },
                labels: {
                    formatter: function(value) {
                        return value.toFixed(0) + ' m³';
                    }
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(value) {
                        return value.toFixed(2) + ' m³';
                    }
                }
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        };
        effluentChart = new ApexCharts(document.querySelector("#effluentChart"), effluentOptions);
        effluentChart.render();

        // Influent Pie Chart
        const influentPieOptions = {
            series: [0, 0, 0],
            chart: {
                type: 'donut',
                height: 320,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            labels: ['Pit Sparta', 'Pit Garam', 'Pit Domestik'],
            colors: ['#4bc0c0', '#ff6384', '#36a2eb'],
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toFixed(1) + '%';
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value.toFixed(2) + ' m³';
                    }
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function(w) {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return total.toFixed(2) + ' m³';
                                }
                            }
                        }
                    }
                }
            }
        };
        influentPieChart = new ApexCharts(document.querySelector("#influentPieChart"), influentPieOptions);
        influentPieChart.render();

        // Effluent Pie Chart
        const effluentPieOptions = {
            series: [0, 0],
            chart: {
                type: 'donut',
                height: 320,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            labels: ['Full Proses', 'DAF Pre'],
            colors: ['#9966ff', '#ff9f40'],
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toFixed(1) + '%';
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value.toFixed(2) + ' m³';
                    }
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function(w) {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return total.toFixed(2) + ' m³';
                                }
                            }
                        }
                    }
                }
            }
        };
        effluentPieChart = new ApexCharts(document.querySelector("#effluentPieChart"), effluentPieOptions);
        effluentPieChart.render();

        // Load initial data
        updateInfluentChart();
        updateEffluentChart();
    }

    // Update Influent Chart
    async function updateInfluentChart() {
        try {
            const period = document.getElementById('influentPeriod').value;
            const response = await fetch(`/api/wwtp/dashboard/influent-chart/${period}`);
            const data = await response.json();

            allInfluentData = data;

            const categories = data.map(d => {
                const date = new Date(d.tanggal);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });

            const spartaData = data.map(d => parseFloat(d.pit_sparta) || 0);
            const garamData = data.map(d => parseFloat(d.pit_garam) || 0);
            const domestikData = data.map(d => parseFloat(d.pit_domestik) || 0);

            influentChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });

            influentChart.updateSeries([{
                name: 'Pit Sparta',
                data: spartaData
            }, {
                name: 'Pit Garam',
                data: garamData
            }, {
                name: 'Pit Domestik',
                data: domestikData
            }]);

            // Update pie chart
            const totalSparta = spartaData.reduce((sum, val) => sum + val, 0);
            const totalGaram = garamData.reduce((sum, val) => sum + val, 0);
            const totalDomestik = domestikData.reduce((sum, val) => sum + val, 0);

            influentPieChart.updateSeries([totalSparta, totalGaram, totalDomestik]);

        } catch (error) {
            console.error('Error updating influent chart:', error);
        }
    }

    // Update Effluent Chart
    async function updateEffluentChart() {
        try {
            const period = document.getElementById('effluentPeriod').value;
            const response = await fetch(`/api/wwtp/dashboard/effluent-chart/${period}`);
            const data = await response.json();

            allEffluentData = data;

            const categories = data.map(d => {
                const date = new Date(d.tanggal);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });

            const fullProsesData = data.map(d => parseFloat(d.full_proses) || 0);
            const dafPreData = data.map(d => parseFloat(d.daf_pre) || 0);

            effluentChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });

            effluentChart.updateSeries([{
                name: 'Full Proses',
                data: fullProsesData
            }, {
                name: 'DAF Pre',
                data: dafPreData
            }]);

            // Update pie chart
            const totalFullProses = fullProsesData.reduce((sum, val) => sum + val, 0);
            const totalDafPre = dafPreData.reduce((sum, val) => sum + val, 0);

            effluentPieChart.updateSeries([totalFullProses, totalDafPre]);

        } catch (error) {
            console.error('Error updating effluent chart:', error);
        }
    }

    // Load Monthly Comparison
    async function loadMonthlyComparison() {
        try {
            const response = await fetch('/api/wwtp/dashboard/monthly-comparison');
            const data = await response.json();

            const options = {
                series: [{
                    name: 'Influent',
                    data: data.map(d => parseFloat(d.influent) || 0)
                }, {
                    name: 'Effluent',
                    data: data.map(d => parseFloat(d.effluent) || 0)
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                colors: ['#36a2eb', '#9966ff'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded',
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: data.map(d => d.month)
                },
                yaxis: {
                    title: {
                        text: 'Volume (m³)'
                    },
                    labels: {
                        formatter: function(value) {
                            return value.toFixed(0) + ' m³';
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value.toFixed(2) + ' m³';
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center'
                },
                grid: {
                    borderColor: '#f1f1f1'
                }
            };

            monthlyComparisonChart = new ApexCharts(document.querySelector("#monthlyComparisonChart"), options);
            monthlyComparisonChart.render();

        } catch (error) {
            console.error('Error loading monthly comparison:', error);
        }
    }

    // Load Recent Records
    async function loadRecentRecords() {
        try {
            const response = await fetch('/api/wwtp/dashboard/recent-records/10');
            const data = await response.json();

            const tbody = document.getElementById('recentRecordsTable');

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No data available</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(record => {
                const date = new Date(record.tanggal);
                const formattedDate = date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });

                let details = '';
                let total = 0;
                let badge = '';

                if (record.kategori === 'influent' && record.influent) {
                    badge = '<span class="badge bg-info-subtle text-info">Influent</span>';
                    details = `
                    <small class="text-muted">
                        Sparta: ${record.influent.pit_sparta} m³<br>
                        Garam: ${record.influent.pit_garam} m³<br>
                        Domestik: ${record.influent.pit_domestik} m³
                    </small>
                `;
                    total = parseFloat(record.influent.pit_sparta) +
                        parseFloat(record.influent.pit_garam) +
                        parseFloat(record.influent.pit_domestik);
                } else if (record.kategori === 'effluent' && record.effluent) {
                    badge = '<span class="badge bg-success-subtle text-success">Effluent</span>';
                    details = `
                    <small class="text-muted">
                        Full Proses: ${record.effluent.full_proses} m³<br>
                        DAF Pre: ${record.effluent.daf_pre} m³
                    </small>
                `;
                    total = parseFloat(record.effluent.full_proses) +
                        parseFloat(record.effluent.daf_pre);
                }

                return `
                <tr>
                    <td><strong>${formattedDate}</strong></td>
                    <td>${badge}</td>
                    <td>${details}</td>
                    <td><strong>${total.toFixed(2)} m³</strong></td>
                </tr>
            `;
            }).join('');

        } catch (error) {
            console.error('Error loading recent records:', error);
            document.getElementById('recentRecordsTable').innerHTML =
                '<tr><td colspan="4" class="text-center text-danger">Error loading data</td></tr>';
        }
    }
</script>
@endsection