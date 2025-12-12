@extends('layouts.app')

@section('title', 'Dashboard WWTP Performance')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dashboard WWTP Performance Monitoring</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Utility</a></li>
                            <li class="breadcrumb-item active">WWTP Performance</li>
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
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">This Week Records</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value" id="weeklyRecords">0</span>
                                </h4>
                                <p class="text-muted mb-0">
                                    <span id="weeklyStatus" class="badge bg-success-subtle text-success"></span>
                                </p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info-subtle rounded fs-3">
                                    <i class="bx bx-calendar-week text-info"></i>
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
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Avg Weekly TSS</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span id="avgWeeklyTSS">0</span> <small class="fs-14 text-muted">mg/L</small>
                                </h4>
                                <p class="text-muted mb-0 text-truncate">Current week average</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning-subtle rounded fs-3">
                                    <i class="bx bx-test-tube text-warning"></i>
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
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Avg Weekly COD</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span id="avgWeeklyCOD">0</span> <small class="fs-14 text-muted">mg/L</small>
                                </h4>
                                <p class="text-muted mb-0 text-truncate">Current week average</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success-subtle rounded fs-3">
                                    <i class="bx bx-droplet text-success"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Process Type Selection -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Select Process Type</h4>
                    </div>
                    <div class="card-body">
                        <div class="btn-group w-100" role="group" aria-label="Process Type">
                            <input type="radio" class="btn-check" name="processType" id="equal" value="equal" checked>
                            <label class="btn btn-outline-primary" for="equal">
                                <i class="bx bx-equalizer"></i> Equal
                            </label>

                            <input type="radio" class="btn-check" name="processType" id="outlet_anaerob" value="outlet_anaerob">
                            <label class="btn btn-outline-info" for="outlet_anaerob">
                                <i class="bx bx-sync"></i> Outlet Anaerob
                            </label>

                            <input type="radio" class="btn-check" name="processType" id="aerob" value="aerob">
                            <label class="btn btn-outline-success" for="aerob">
                                <i class="bx bx-wind"></i> Aerob
                            </label>

                            <input type="radio" class="btn-check" name="processType" id="daf" value="daf">
                            <label class="btn btn-outline-warning" for="daf">
                                <i class="bx bx-filter"></i> DAF
                            </label>

                            <input type="radio" class="btn-check" name="processType" id="outlet" value="outlet">
                            <label class="btn btn-outline-danger" for="outlet">
                                <i class="bx bx-exit"></i> Outlet
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- TSS Trend Chart -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">TSS Trend - <span id="tssTrendTitle">Equal</span></h4>
                        <div class="flex-shrink-0">
                            <select class="form-select form-select-sm" id="tssPeriod" style="width: 150px;">
                                <option value="7">Last 7 Days</option>
                                <option value="14">Last 14 Days</option>
                                <option value="30" selected>Last 30 Days</option>
                                <option value="60">Last 60 Days</option>
                                <option value="90">Last 90 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="tssChart"></div>
                    </div>
                </div>
            </div>

            <!-- COD Trend Chart -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">COD Trend - <span id="codTrendTitle">Equal</span></h4>
                        <div class="flex-shrink-0">
                            <select class="form-select form-select-sm" id="codPeriod" style="width: 150px;">
                                <option value="7">Last 7 Days</option>
                                <option value="14">Last 14 Days</option>
                                <option value="30" selected>Last 30 Days</option>
                                <option value="60">Last 60 Days</option>
                                <option value="90">Last 90 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="codChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Combined TSS & COD Chart -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">TSS & COD Comparison - <span id="combinedTitle">Equal</span></h4>
                    </div>
                    <div class="card-body">
                        <div id="combinedChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Comparison Chart -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">6-Month Performance Comparison (All Process Types)</h4>
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
                        <h4 class="card-title mb-0 flex-grow-1">Recent Performance Records</h4>
                        <div class="flex-shrink-0">
                            <button class="btn btn-sm btn-primary" onclick="loadRecentRecords()">
                                <i class="bx bx-refresh"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Week Period</th>
                                        <th scope="col">Process Type</th>
                                        <th scope="col">TSS</th>
                                        <th scope="col">COD</th>
                                        <th scope="col">Photo</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="recentRecordsTable">
                                    <tr>
                                        <td colspan="6" class="text-center">
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

<!-- Photo Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Performance Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Performance Photo" class="img-fluid">
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

    .btn-group .btn-check:checked+label {
        font-weight: 600;
    }

    .apexcharts-tooltip {
        background: #fff !important;
        border: 1px solid #e3e6ef !important;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .badge-process {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let tssChart, codChart, combinedChart, monthlyComparisonChart;
    let currentProcessType = 'equal';

    // Load all data on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadStatistics();
        initCharts();
        loadMonthlyComparison();
        loadRecentRecords();

        // Event listeners for process type selection
        document.querySelectorAll('input[name="processType"]').forEach(radio => {
            radio.addEventListener('change', function() {
                currentProcessType = this.value;
                updateProcessTypeTitle(this.value);
                updateAllCharts();
            });
        });

        // Event listeners for period selection
        document.getElementById('tssPeriod').addEventListener('change', updateTSSChart);
        document.getElementById('codPeriod').addEventListener('change', updateCODChart);
    });

    // Update process type title
    function updateProcessTypeTitle(type) {
        const titles = {
            'equal': 'Equal',
            'outlet_anaerob': 'Outlet Anaerob',
            'aerob': 'Aerob',
            'daf': 'DAF',
            'outlet': 'Outlet'
        };
        const title = titles[type] || type;
        document.getElementById('tssTrendTitle').textContent = title;
        document.getElementById('codTrendTitle').textContent = title;
        document.getElementById('combinedTitle').textContent = title;
    }

    // Update all charts when process type changes
    function updateAllCharts() {
        updateTSSChart();
        updateCODChart();
        updateCombinedChart();
    }

    // Load Statistics
    async function loadStatistics() {
        try {
            const response = await fetch('/api/wwtp-performance/dashboard/statistics');
            const data = await response.json();

            // Animate counter
            animateValue('totalRecords', 0, data.total_records, 1000);

            // Weekly summary
            let totalWeeklyRecords = 0;
            let totalTSS = 0;
            let totalCOD = 0;
            let recordCount = 0;

            if (data.weekly_summary) {
                Object.values(data.weekly_summary).forEach(item => {
                    totalWeeklyRecords += item.count || 0;
                    if (item.avg_tss) {
                        totalTSS += item.avg_tss;
                        recordCount++;
                    }
                    if (item.avg_cod) {
                        totalCOD += item.avg_cod;
                    }
                });
            }

            animateValue('weeklyRecords', 0, totalWeeklyRecords, 1000);

            const avgTSS = recordCount > 0 ? (totalTSS / recordCount).toFixed(2) : 0;
            const avgCOD = recordCount > 0 ? (totalCOD / recordCount).toFixed(2) : 0;

            document.getElementById('avgWeeklyTSS').textContent = avgTSS;
            document.getElementById('avgWeeklyCOD').textContent = avgCOD;

            // Weekly status
            if (totalWeeklyRecords > 0) {
                document.getElementById('weeklyStatus').textContent = `${totalWeeklyRecords} records this week`;
                document.getElementById('weeklyStatus').className = 'badge bg-success-subtle text-success';
            } else {
                document.getElementById('weeklyStatus').textContent = 'No data this week';
                document.getElementById('weeklyStatus').className = 'badge bg-warning-subtle text-warning';
            }

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
        // TSS Chart
        const tssOptions = {
            series: [{
                name: 'TSS',
                data: []
            }],
            chart: {
                type: 'line',
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
            colors: ['#f59e0b'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: [],
                labels: {
                    rotate: -45
                }
            },
            yaxis: {
                title: {
                    text: 'TSS (mg/L)'
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value.toFixed(2) + ' mg/L';
                    }
                }
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        };
        tssChart = new ApexCharts(document.querySelector("#tssChart"), tssOptions);
        tssChart.render();

        // COD Chart
        const codOptions = {
            series: [{
                name: 'COD',
                data: []
            }],
            chart: {
                type: 'line',
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
            colors: ['#10b981'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: [],
                labels: {
                    rotate: -45
                }
            },
            yaxis: {
                title: {
                    text: 'COD (mg/L)'
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value.toFixed(2) + ' mg/L';
                    }
                }
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        };
        codChart = new ApexCharts(document.querySelector("#codChart"), codOptions);
        codChart.render();

        // Combined Chart
        const combinedOptions = {
            series: [{
                name: 'TSS',
                data: []
            }, {
                name: 'COD',
                data: []
            }],
            chart: {
                type: 'area',
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
            colors: ['#f59e0b', '#10b981'],
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
                    opacityTo: 0.1
                }
            },
            xaxis: {
                categories: [],
                labels: {
                    rotate: -45
                }
            },
            yaxis: {
                title: {
                    text: 'Concentration (mg/L)'
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(value) {
                        return value.toFixed(2) + ' mg/L';
                    }
                }
            },
            legend: {
                position: 'bottom'
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        };
        combinedChart = new ApexCharts(document.querySelector("#combinedChart"), combinedOptions);
        combinedChart.render();

        // Load initial data
        updateAllCharts();
    }

    // Update TSS Chart
    async function updateTSSChart() {
        try {
            const period = document.getElementById('tssPeriod').value;
            const response = await fetch(`/api/wwtp-performance/dashboard/chart/${currentProcessType}/${period}`);
            const data = await response.json();

            const categories = data.map(d => {
                // Gunakan created_at atau tanggal yang tersedia
                const date = new Date(d.tanggal || d.created_at);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });

            const tssData = data.map(d => parseFloat(d.tss) || 0);

            tssChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });

            tssChart.updateSeries([{
                name: 'TSS',
                data: tssData
            }]);

        } catch (error) {
            console.error('Error updating TSS chart:', error);
        }
    }

    // Update COD Chart
    async function updateCODChart() {
        try {
            const period = document.getElementById('codPeriod').value;
            const response = await fetch(`/api/wwtp-performance/dashboard/chart/${currentProcessType}/${period}`);
            const data = await response.json();

            const categories = data.map(d => {
                // Gunakan created_at atau tanggal yang tersedia
                const date = new Date(d.tanggal || d.created_at);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });

            const codData = data.map(d => parseFloat(d.cod) || 0);

            codChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });

            codChart.updateSeries([{
                name: 'COD',
                data: codData
            }]);

        } catch (error) {
            console.error('Error updating COD chart:', error);
        }
    }

    // Update Combined Chart
    async function updateCombinedChart() {
        try {
            const response = await fetch(`/api/wwtp-performance/dashboard/chart/${currentProcessType}/30`);
            const data = await response.json();

            const categories = data.map(d => {
                const date = new Date(d.tanggal);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });

            const tssData = data.map(d => parseFloat(d.tss) || 0);
            const codData = data.map(d => parseFloat(d.cod) || 0);

            combinedChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });

            combinedChart.updateSeries([{
                name: 'TSS',
                data: tssData
            }, {
                name: 'COD',
                data: codData
            }]);

        } catch (error) {
            console.error('Error updating combined chart:', error);
        }
    }

    // Load Monthly Comparison
    async function loadMonthlyComparison() {
        try {
            const response = await fetch('/api/wwtp-performance/dashboard/monthly-comparison');
            const data = await response.json();

            // Prepare data for chart
            const categories = data.map(d => d.month);
            const processTypes = ['equal', 'outlet_anaerob', 'aerob', 'daf', 'outlet'];
            const processNames = {
                'equal': 'Equal',
                'outlet_anaerob': 'Outlet Anaerob',
                'aerob': 'Aerob',
                'daf': 'DAF',
                'outlet': 'Outlet'
            };
            const colors = ['#3b82f6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'];

            const series = processTypes.map((type, index) => ({
                name: processNames[type],
                data: data.map(month => {
                    const typeData = month.data[type];
                    return typeData ? ((typeData.avg_tss + typeData.avg_cod) / 2).toFixed(2) : 0;
                })
            }));

            const options = {
                series: series,
                chart: {
                    type: 'bar',
                    height: 400,
                    stacked: false,
                    toolbar: {
                        show: true
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                colors: colors,
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
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
                    categories: categories
                },
                yaxis: {
                    title: {
                        text: 'Average Concentration (mg/L)'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value + ' mg/L';
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
            const response = await fetch('/api/wwtp-performance/dashboard/recent/10');
            const data = await response.json();

            const tbody = document.getElementById('recentRecordsTable');

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No data available</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(record => {
                const processTypes = {
                    'equal': {
                        name: 'Equal',
                        class: 'primary'
                    },
                    'outlet_anaerob': {
                        name: 'Outlet Anaerob',
                        class: 'info'
                    },
                    'aerob': {
                        name: 'Aerob',
                        class: 'success'
                    },
                    'daf': {
                        name: 'DAF',
                        class: 'warning'
                    },
                    'outlet': {
                        name: 'Outlet',
                        class: 'danger'
                    }
                };

                const processInfo = processTypes[record.jenis] || {
                    name: record.jenis,
                    class: 'secondary'
                };
                const badge = `<span class="badge badge-process bg-${processInfo.class}-subtle text-${processInfo.class}">${processInfo.name}</span>`;

                const photoBtn = record.foto ?
                    `<button class="btn btn-sm btn-light" onclick="showPhoto('${record.foto}')">
                         <i class="bx bx-image"></i> View
                       </button>` :
                    '<span class="text-muted">No photo</span>';

                // Week period
                const weekPeriod = record.week ?
                    `${new Date(record.week.week_start).toLocaleDateString('id-ID')} - ${new Date(record.week.week_end).toLocaleDateString('id-ID')}` :
                    'N/A';

                return `
                <tr>
                    <td><small>${weekPeriod}</small></td>
                    <td>${badge}</td>
                    <td><strong>${parseFloat(record.tss).toFixed(2)}</strong> mg/L</td>
                    <td><strong>${parseFloat(record.cod).toFixed(2)}</strong> mg/L</td>
                    <td>${photoBtn}</td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-info" onclick="viewDetail(${record.id})" title="View Detail">
                                <i class="bx bx-show"></i>
                            </button>
                            <button class="btn btn-danger" onclick="deleteRecord(${record.id})" title="Delete">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            }).join('');

        } catch (error) {
            console.error('Error loading recent records:', error);
            document.getElementById('recentRecordsTable').innerHTML =
                '<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>';
        }
    }

    // Show photo in modal
    function showPhoto(photoPath) {
        const fullPath = `/storage/${photoPath}`;
        document.getElementById('modalImage').src = fullPath;
        const modal = new bootstrap.Modal(document.getElementById('photoModal'));
        modal.show();
    }

    // View detail (placeholder - implement as needed)
    async function viewDetail(id) {
        try {
            const response = await fetch(`/api/wwtp-performance/${id}`);
            const result = await response.json();

            if (result.status === 'success') {
                const data = result.data;
                alert(`
Performance Detail:
-------------------
Week: ${new Date(data.week.week_start).toLocaleDateString('id-ID')} - ${new Date(data.week.week_end).toLocaleDateString('id-ID')}
Process Type: ${data.jenis}
TSS: ${data.tss} mg/L
COD: ${data.cod} mg/L
                `);
            }
        } catch (error) {
            console.error('Error viewing detail:', error);
            alert('Failed to load detail');
        }
    }

    // Delete record
    async function deleteRecord(id) {
        if (!confirm('Are you sure you want to delete this record?')) {
            return;
        }

        try {
            const response = await fetch(`/api/wwtp-performance/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (result.status === 'success') {
                alert('Record deleted successfully');
                loadStatistics();
                updateAllCharts();
                loadMonthlyComparison();
                loadRecentRecords();
            } else {
                alert('Failed to delete record');
            }
        } catch (error) {
            console.error('Error deleting record:', error);
            alert('Failed to delete record');
        }
    }
</script>
@endsection