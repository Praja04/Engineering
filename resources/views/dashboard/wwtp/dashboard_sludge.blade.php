@extends('layouts.app')

@section('title', 'Dashboard WWTP Sludge')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dashboard WWTP Sludge</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Utility</a></li>
                            <li class="breadcrumb-item active">WWTP Sludge Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================= -->
        <!-- SECTION: DATA SLUDGE -->
        <!-- ========================================= -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary-subtle">
                    <div class="card-body">
                        <h4 class="card-title text-primary mb-0">
                            <i class="bx bx-droplet"></i> Data Sludge Management
                        </h4>
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
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Shifts</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value" id="totalShifts">0</span>
                                </h4>
                                <p class="text-muted mb-0 text-truncate">All time shifts</p>
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
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Days</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value" id="totalDays">0</span>
                                </h4>
                                <p class="text-muted mb-0">
                                    <span id="todayStatus" class="badge bg-success-subtle text-success"></span>
                                </p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info-subtle rounded fs-3">
                                    <i class="bx bx-calendar-alt text-info"></i>
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
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">This Week Shifts</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value" id="weekShifts">0</span>
                                </h4>
                                <p class="text-muted mb-0 text-truncate">Shifts this week</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success-subtle rounded fs-3">
                                    <i class="bx bx-time text-success"></i>
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
                                <p class="text-muted mb-0 text-truncate">Latest shift</p>
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
                                <p class="text-uppercase fw-medium text-muted mb-1">Avg Drain Lumpur (This Month)</p>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                    <span id="monthlyDrainAvg">0</span> <small class="fs-14 text-muted">m³</small>
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
                                    <i class="bx bx-time-five"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-1">Avg Running Hour SCP (This Month)</p>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                    <span id="monthlyRunningHourAvg">0</span> <small class="fs-14 text-muted">hours</small>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Drain Lumpur Trend Chart -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Drain Lumpur Trend</h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="drainStartDate" style="width: 150px;">
                                <span class="align-self-center">to</span>
                                <input type="date" class="form-control form-control-sm" id="drainEndDate" style="width: 150px;">
                                <button class="btn btn-sm btn-primary" onclick="updateDrainChart()">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="drainLumpurChart"></div>
                    </div>
                </div>
            </div>

            <!-- Shift Breakdown Pie Chart -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Distribution by Shift</h4>
                    </div>
                    <div class="card-body">
                        <div id="shiftPieChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Running Hour Chart -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Running Hour SCP Trend</h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="runningStartDate" style="width: 150px;">
                                <span class="align-self-center">to</span>
                                <input type="date" class="form-control form-control-sm" id="runningEndDate" style="width: 150px;">
                                <button class="btn btn-sm btn-primary" onclick="updateRunningHourChart()">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="runningHourChart"></div>
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
                        <h4 class="card-title mb-0 flex-grow-1">Recent Sludge Records</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Shift</th>
                                        <th scope="col">Drain Lumpur</th>
                                        <th scope="col">Running Hour SCP</th>
                                        <th scope="col">Total Daily</th>
                                    </tr>
                                </thead>
                                <tbody id="recentRecordsTable">
                                    <tr>
                                        <td colspan="5" class="text-center">
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
    // Chart instances
    let drainLumpurChart, runningHourChart, shiftPieChart, monthlyComparisonChart;

    // Load all data on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard loading started...');

        // Set default dates
        setDefaultDates();

        // Initialize charts first
        initCharts();

        // Load data after charts are initialized
        setTimeout(function() {
            loadStatistics();
            updateDrainChart();
            updateRunningHourChart();
            updateShiftBreakdown();
            loadMonthlyComparison();
            loadRecentRecords();
        }, 100);
    });

    // Set default dates for filters
    function setDefaultDates() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        document.getElementById('drainStartDate').value = formatDate(firstDay);
        document.getElementById('drainEndDate').value = formatDate(lastDay);
        document.getElementById('runningStartDate').value = formatDate(firstDay);
        document.getElementById('runningEndDate').value = formatDate(lastDay);
    }

    // =============================
    // LOAD STATISTICS
    // =============================

    async function loadStatistics() {
        try {
            const response = await fetch('/api/wwtp-sludge/dashboard/statistics');

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Statistics:', data);

            animateValue('totalShifts', 0, data.total_shifts || 0, 1000);
            animateValue('totalDays', 0, data.total_days || 0, 1000);
            animateValue('weekShifts', 0, data.shifts_this_week || 0, 1000);

            if (data.last_update) {
                const date = new Date(data.last_update);
                const dateStr = date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
                const shiftLabel = data.last_shift ? ` (Shift ${data.last_shift})` : '';
                document.getElementById('lastUpdate').textContent = dateStr + shiftLabel;
            }

            const statusText = (data.shifts_today || 0) === 0 ?
                'Belum ada data hari ini' :
                `${data.shifts_today} shift hari ini`;
            const statusClass = (data.shifts_today || 0) === 0 ?
                'badge bg-warning-subtle text-warning' :
                'badge bg-success-subtle text-success';

            document.getElementById('todayStatus').textContent = statusText;
            document.getElementById('todayStatus').className = statusClass;

            document.getElementById('monthlyDrainAvg').textContent = data.monthly_drain_avg || '0';
            document.getElementById('monthlyRunningHourAvg').textContent = data.monthly_running_hour_avg || '0';

        } catch (error) {
            console.error('Error loading statistics:', error);
            alert('Error loading statistics. Please check console for details.');
        }
    }

    // =============================
    // UPDATE DRAIN LUMPUR CHART
    // =============================

    async function updateDrainChart() {
        try {
            const startDate = document.getElementById('drainStartDate').value;
            const endDate = document.getElementById('drainEndDate').value;

            if (!startDate || !endDate) {
                console.warn('Start or end date not set');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date must be before end date');
                return;
            }

            const response = await fetch(`/api/wwtp-sludge/dashboard/drain-chart?start_date=${startDate}&end_date=${endDate}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Drain chart data:', data);

            if (!data || data.length === 0) {
                console.warn('No drain data available');
                drainLumpurChart.updateSeries([{
                    name: 'Drain Lumpur',
                    data: []
                }]);
                return;
            }

            const categories = data.map(d => {
                const date = new Date(d.tanggal);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });

            const drainData = data.map(d => parseFloat(d.total_drain) || 0);

            drainLumpurChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });

            drainLumpurChart.updateSeries([{
                name: 'Drain Lumpur',
                data: drainData
            }]);

        } catch (error) {
            console.error('Error updating drain chart:', error);
            alert('Error loading drain chart data. Please try again.');
        }
    }

    // =============================
    // UPDATE RUNNING HOUR CHART
    // =============================

    async function updateRunningHourChart() {
        try {
            const startDate = document.getElementById('runningStartDate').value;
            const endDate = document.getElementById('runningEndDate').value;

            if (!startDate || !endDate) {
                console.warn('Start or end date not set');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date must be before end date');
                return;
            }

            const response = await fetch(`/api/wwtp-sludge/dashboard/running-hour-chart?start_date=${startDate}&end_date=${endDate}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Running hour chart data:', data);

            if (!data || data.length === 0) {
                console.warn('No running hour data available');
                runningHourChart.updateSeries([{
                    name: 'Running Hour SCP',
                    data: []
                }]);
                return;
            }

            const categories = data.map(d => {
                const date = new Date(d.tanggal);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });

            const runningData = data.map(d => parseFloat(d.total_running_hour) || 0);

            runningHourChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });

            runningHourChart.updateSeries([{
                name: 'Running Hour SCP',
                data: runningData
            }]);

        } catch (error) {
            console.error('Error updating running hour chart:', error);
            alert('Error loading running hour chart data. Please try again.');
        }
    }

    // =============================
    // UPDATE SHIFT BREAKDOWN
    // =============================

    async function updateShiftBreakdown() {
        try {
            const startDate = document.getElementById('drainStartDate').value;
            const endDate = document.getElementById('drainEndDate').value;

            if (!startDate || !endDate) {
                console.warn('Dates not set for shift breakdown');
                return;
            }

            const response = await fetch(`/api/wwtp-sludge/dashboard/shift-breakdown?start_date=${startDate}&end_date=${endDate}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Shift breakdown data:', data);

            if (!data || data.length === 0) {
                console.warn('No shift breakdown data available');
                shiftPieChart.updateSeries([0, 0, 0]);
                return;
            }

            const labels = data.map(d => `Shift ${d.shift}`);
            const values = data.map(d => parseFloat(d.total) || 0);

            shiftPieChart.updateOptions({
                labels: labels
            });
            shiftPieChart.updateSeries(values);

        } catch (error) {
            console.error('Error updating shift breakdown:', error);
        }
    }

    // =============================
    // LOAD MONTHLY COMPARISON
    // =============================

    async function loadMonthlyComparison() {
        try {
            const response = await fetch('/api/wwtp-sludge/dashboard/monthly-comparison');

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Monthly comparison:', data);

            if (!data || data.length === 0) {
                console.warn('No monthly comparison data available');
                return;
            }

            monthlyComparisonChart.updateOptions({
                xaxis: {
                    categories: data.map(d => d.month)
                }
            });

            monthlyComparisonChart.updateSeries([{
                    name: 'Drain Lumpur',
                    data: data.map(d => parseFloat(d.drain_lumpur) || 0)
                },
                {
                    name: 'Running Hour SCP',
                    data: data.map(d => parseFloat(d.running_hour_scp) || 0)
                }
            ]);

        } catch (error) {
            console.error('Error loading monthly comparison:', error);
        }
    }

    // =============================
    // LOAD RECENT RECORDS
    // =============================

    async function loadRecentRecords() {
        try {
            const response = await fetch('/api/wwtp-sludge/dashboard/recent-records/10');

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Recent records:', data);

            const tbody = document.getElementById('recentRecordsTable');

            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No data available</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(record => {
                const date = new Date(record.tanggal);
                const formattedDate = date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });

                const shiftDetails = record.shifts.map(shift => {
                    return `
                        <div class="mb-1">
                            <span class="badge bg-primary-subtle text-primary">Shift ${shift.shift}</span>
                            <small class="text-muted ms-2">
                                Drain: ${shift.drain_lumpur || 0} m³ | 
                                Running Hour: ${shift.running_hour_scp || 0} hrs
                            </small>
                        </div>
                    `;
                }).join('');

                return `
                    <tr>
                        <td><strong>${formattedDate}</strong></td>
                        <td><span class="badge bg-info-subtle text-info">${record.shift_count} shifts</span></td>
                        <td><strong>${(record.total_drain || 0).toFixed(2)} m³</strong></td>
                        <td><strong>${(record.total_running_hour || 0).toFixed(2)} hrs</strong></td>
                        <td>${shiftDetails}</td>
                    </tr>
                `;
            }).join('');

        } catch (error) {
            console.error('Error loading recent records:', error);
            document.getElementById('recentRecordsTable').innerHTML =
                '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>';
        }
    }

    // =============================
    // UTILITY FUNCTIONS
    // =============================

    function animateValue(id, start, end, duration) {
        const obj = document.getElementById(id);
        if (!obj) {
            console.warn(`Element with id '${id}' not found`);
            return;
        }

        const range = end - start;
        if (range === 0) {
            obj.textContent = end;
            return;
        }

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

    // =============================
    // INITIALIZE CHARTS
    // =============================

    function initCharts() {
        console.log('Initializing charts...');

        // Common chart options
        const areaChartOptions = {
            chart: {
                type: 'area',
                height: 320,
                toolbar: {
                    show: true
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
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
            tooltip: {
                shared: true,
                intersect: false
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        };

        // Drain Lumpur Chart
        try {
            drainLumpurChart = new ApexCharts(document.querySelector("#drainLumpurChart"), {
                ...areaChartOptions,
                colors: ['#4bc0c0'],
                series: [{
                    name: 'Drain Lumpur',
                    data: []
                }],
                yaxis: {
                    title: {
                        text: 'Volume (m³)'
                    },
                    labels: {
                        formatter: (value) => value ? value.toFixed(0) + ' m³' : '0 m³'
                    }
                },
                tooltip: {
                    y: {
                        formatter: (value) => value ? value.toFixed(2) + ' m³' : '0 m³'
                    }
                }
            });
            drainLumpurChart.render();
            console.log('Drain lumpur chart initialized');
        } catch (error) {
            console.error('Error initializing drain lumpur chart:', error);
        }

        // Running Hour Chart
        try {
            runningHourChart = new ApexCharts(document.querySelector("#runningHourChart"), {
                ...areaChartOptions,
                colors: ['#ff6384'],
                series: [{
                    name: 'Running Hour SCP',
                    data: []
                }],
                yaxis: {
                    title: {
                        text: 'Hours'
                    },
                    labels: {
                        formatter: (value) => value ? value.toFixed(0) + ' hrs' : '0 hrs'
                    }
                },
                tooltip: {
                    y: {
                        formatter: (value) => value ? value.toFixed(2) + ' hrs' : '0 hrs'
                    }
                }
            });
            runningHourChart.render();
            console.log('Running hour chart initialized');
        } catch (error) {
            console.error('Error initializing running hour chart:', error);
        }

        // Shift Pie Chart
        try {
            shiftPieChart = new ApexCharts(document.querySelector("#shiftPieChart"), {
                chart: {
                    type: 'donut',
                    height: 320,
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                colors: ['#4bc0c0', '#ff6384', '#36a2eb'],
                series: [0, 0, 0],
                labels: ['Shift 1', 'Shift 2', 'Shift 3'],
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center'
                },
                dataLabels: {
                    enabled: true,
                    formatter: (val, opts) => {
                        return val.toFixed(1) + '%';
                    }
                },
                tooltip: {
                    y: {
                        formatter: (value) => value ? value.toFixed(2) + ' m³' : '0 m³'
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
                                    formatter: (w) => {
                                        const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        return total.toFixed(2) + ' m³';
                                    }
                                }
                            }
                        }
                    }
                }
            });
            shiftPieChart.render();
            console.log('Shift pie chart initialized');
        } catch (error) {
            console.error('Error initializing shift pie chart:', error);
        }

        // Monthly Comparison Chart
        try {
            monthlyComparisonChart = new ApexCharts(document.querySelector("#monthlyComparisonChart"), {
                series: [{
                        name: 'Drain Lumpur',
                        data: []
                    },
                    {
                        name: 'Running Hour SCP',
                        data: []
                    }
                ],
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
                colors: ['#4bc0c0', '#ff6384'],
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
                    categories: []
                },
                yaxis: [{
                        title: {
                            text: 'Drain Lumpur (m³)'
                        },
                        labels: {
                            formatter: (value) => value ? value.toFixed(0) + ' m³' : '0 m³'
                        }
                    },
                    {
                        opposite: true,
                        title: {
                            text: 'Running Hour (hrs)'
                        },
                        labels: {
                            formatter: (value) => value ? value.toFixed(0) + ' hrs' : '0 hrs'
                        }
                    }
                ],
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: [{
                            formatter: (value) => value ? value.toFixed(2) + ' m³' : '0 m³'
                        },
                        {
                            formatter: (value) => value ? value.toFixed(2) + ' hrs' : '0 hrs'
                        }
                    ]
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center'
                },
                grid: {
                    borderColor: '#f1f1f1'
                }
            });
            monthlyComparisonChart.render();
            console.log('Monthly comparison chart initialized');
        } catch (error) {
            console.error('Error initializing monthly comparison chart:', error);
        }

        console.log('All charts initialized');
    }
</script>
@endsection