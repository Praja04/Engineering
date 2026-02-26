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

        <!-- ========================================= -->
        <!-- SECTION: DATA MINGGUAN -->
        <!-- ========================================= -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary-subtle">
                    <div class="card-body">
                        <h4 class="card-title text-primary mb-0">
                            <i class="bx bx-calendar-week"></i> Data Mingguan
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards - Weekly -->
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
                                    <span class="counter-value" id="weeklyTotalRecords">0</span>
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
                                    <span class="counter-value" id="weeklyTotalInfluent">0</span>
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
                                    <span class="counter-value" id="weeklyTotalEffluent">0</span>
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
                                    <span id="weeklyLastUpdate">-</span>
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

        <!-- Monthly Average Cards - Weekly -->
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
                                    <span id="weeklyMonthlyInfluentAvg">0</span> <small class="fs-14 text-muted">m³</small>
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
                                    <span id="weeklyMonthlyEffluentAvg">0</span> <small class="fs-14 text-muted">m³</small>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row - Weekly -->
        <div class="row">
            <!-- Influent Trend Chart -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Influent Data Trend (Weekly)</h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="weeklyInfluentStartDate" style="width: 150px;">
                                <span class="align-self-center">to</span>
                                <input type="date" class="form-control form-control-sm" id="weeklyInfluentEndDate" style="width: 150px;">
                                <button class="btn btn-sm btn-primary" onclick="updateWeeklyInfluentChart()">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="weeklyInfluentChart"></div>
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
                        <div id="weeklyInfluentPieChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Effluent Charts - Weekly -->
        <div class="row">
            <!-- Effluent Trend Chart -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Effluent Data Trend (Weekly)</h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="weeklyEffluentStartDate" style="width: 150px;">
                                <span class="align-self-center">to</span>
                                <input type="date" class="form-control form-control-sm" id="weeklyEffluentEndDate" style="width: 150px;">
                                <button class="btn btn-sm btn-primary" onclick="updateWeeklyEffluentChart()">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="weeklyEffluentChart"></div>
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
                        <div id="weeklyEffluentPieChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Comparison Chart - Weekly -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">6-Month Comparison (Weekly Data)</h4>
                    </div>
                    <div class="card-body">
                        <div id="weeklyMonthlyComparisonChart"></div>
                    </div>
                </div>
            </div>
        </div>

    

        <!-- ========================================= -->
        <!-- SECTION: DATA HARIAN -->
        <!-- ========================================= -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-success-subtle">
                    <div class="card-body">
                        <h4 class="card-title text-success mb-0">
                            <i class="bx bx-calendar"></i> Data Harian (Per Shift)
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards - Daily -->
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
                                    <span class="counter-value" id="dailyTotalShifts">0</span>
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
                                    <span class="counter-value" id="dailyTotalDays">0</span>
                                </h4>
                                <p class="text-muted mb-0">
                                    <span id="dailyTodayStatus" class="badge bg-success-subtle text-success"></span>
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
                                    <span class="counter-value" id="dailyWeekShifts">0</span>
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
                                    <span id="dailyLastUpdate">-</span>
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

        <!-- Monthly Average Card - Daily -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card card-height-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info-subtle text-info rounded fs-3">
                                    <i class="bx bxs-droplet"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-1">Avg Influent Per Day (This Month)</p>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-0">
                                    <span id="dailyMonthlyAvg">0</span> <small class="fs-14 text-muted">m³</small>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row - Daily -->
        <div class="row">
            <!-- Influent Trend Chart -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Influent Data Trend (Daily Aggregated)</h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="dailyInfluentStartDate" style="width: 150px;">
                                <span class="align-self-center">to</span>
                                <input type="date" class="form-control form-control-sm" id="dailyInfluentEndDate" style="width: 150px;">
                                <button class="btn btn-sm btn-primary" onclick="updateDailyInfluentChart()">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="dailyInfluentChart"></div>
                    </div>
                </div>
            </div>

            <!-- Shift Breakdown Pie Chart -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Daily Distribution</h4>
                    </div>
                    <div class="card-body">
                        <div id="dailyShiftPieChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Comparison Chart - Daily -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">6-Month Comparison (Daily Data)</h4>
                    </div>
                    <div class="card-body">
                        <div id="dailyMonthlyComparisonChart"></div>
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
    let weeklyInfluentChart, weeklyEffluentChart, weeklyInfluentPieChart, weeklyEffluentPieChart, weeklyMonthlyComparisonChart;
    let dailyInfluentChart, dailyShiftPieChart, dailyMonthlyComparisonChart;

    // Load all data on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard loading started...');

        // Set default dates (start of month to end of month)
        setDefaultDates();

        // Initialize charts first
        initCharts();

        // Load data after charts are initialized
        setTimeout(function() {
            loadWeeklyData();
            loadDailyData();
        }, 100);
    });

    // Set default dates for filters
    function setDefaultDates() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        // Format to YYYY-MM-DD
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        // Set influent dates
        document.getElementById('weeklyInfluentStartDate').value = formatDate(firstDay);
        document.getElementById('weeklyInfluentEndDate').value = formatDate(lastDay);

        // Set effluent dates
        document.getElementById('weeklyEffluentStartDate').value = formatDate(firstDay);
        document.getElementById('weeklyEffluentEndDate').value = formatDate(lastDay);

        // Set daily influent dates
        document.getElementById('dailyInfluentStartDate').value = formatDate(firstDay);
        document.getElementById('dailyInfluentEndDate').value = formatDate(lastDay);
    }

    // =============================
    // WEEKLY DATA FUNCTIONS
    // =============================

    async function loadWeeklyData() {
        console.log('Loading weekly data...');
        try {
            await loadWeeklyStatistics();
            await updateWeeklyInfluentChart();
            await updateWeeklyEffluentChart();
            await loadWeeklyMonthlyComparison();
            console.log('Weekly data loaded successfully');
        } catch (error) {
            console.error('Error loading weekly data:', error);
        }
    }

    async function loadWeeklyStatistics() {
        try {
            const response = await fetch('/api/wwtp/dashboard/statistics');

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Weekly statistics:', data);

            animateValue('weeklyTotalRecords', 0, data.total_records || 0, 1000);
            animateValue('weeklyTotalInfluent', 0, data.total_influent || 0, 1000);
            animateValue('weeklyTotalEffluent', 0, data.total_effluent || 0, 1000);

            if (data.last_update) {
                const date = new Date(data.last_update);
                document.getElementById('weeklyLastUpdate').textContent = date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            if (data.weekly_influent) {
                document.getElementById('weeklyInfluentStatus').textContent = 'Data minggu ini tersedia';
                document.getElementById('weeklyInfluentStatus').className = 'badge bg-success-subtle text-success';
            } else {
                document.getElementById('weeklyInfluentStatus').textContent = 'Belum ada data minggu ini';
                document.getElementById('weeklyInfluentStatus').className = 'badge bg-warning-subtle text-warning';
            }

            document.getElementById('weeklyEffluentCount').textContent = (data.weekly_effluent_count || 0) + ' record minggu ini';
            document.getElementById('weeklyMonthlyInfluentAvg').textContent = data.monthly_influent_avg || '0';
            document.getElementById('weeklyMonthlyEffluentAvg').textContent = data.monthly_effluent_avg || '0';

        } catch (error) {
            console.error('Error loading weekly statistics:', error);
            alert('Error loading weekly statistics. Please check console for details.');
        }
    }

    async function updateWeeklyInfluentChart() {
        try {
            const startDate = document.getElementById('weeklyInfluentStartDate').value;
            const endDate = document.getElementById('weeklyInfluentEndDate').value;

            if (!startDate || !endDate) {
                console.warn('Start or end date not set for weekly influent');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date must be before end date');
                return;
            }

            const response = await fetch(`/api/wwtp/dashboard/influent-chart?start_date=${startDate}&end_date=${endDate}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Weekly influent chart data:', data);

            if (!data || data.length === 0) {
                console.warn('No weekly influent data available');
                weeklyInfluentChart.updateSeries([{
                        name: 'Pit Sparta',
                        data: []
                    },
                    {
                        name: 'Pit Garam',
                        data: []
                    },
                    {
                        name: 'Pit Domestik',
                        data: []
                    },
                    {
                        name: 'Pit Produksi Step 3',
                        data: []
                    },
                    {
                        name: 'Pit Storage',
                        data: []
                    }, {
                        name: 'Pit Proses WWTP 2',
                        data: []
                    },
                    {
                        name: 'Pit Outlet',
                        data: []
                    },
                    {
                        name: 'Pit Domestik',
                        data: []
                    },
                    {
                        name: 'Pit Boiler',
                        data: []
                    }
                ]);
                weeklyInfluentPieChart.updateSeries([0, 0, 0]);
                return;
            }

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
            const produksiStep3Data = data.map(d => parseFloat(d.pit_produksi_step3) || 0);
            const storageData = data.map(d => parseFloat(d.pit_storage) || 0);
            const procesWWTP2Data = data.map(d => parseFloat(d.pit_proses_wwtp2) || 0);
            const outletData = data.map(d => parseFloat(d.pit_outlet) || 0);
            const boilerData = data.map(d => parseFloat(d.pit_boiler) || 0);

            weeklyInfluentChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });

            weeklyInfluentChart.updateSeries([{
                    name: 'Pit Sparta',
                    data: spartaData
                },
                {
                    name: 'Pit Garam',
                    data: garamData
                },
                {
                    name: 'Pit Domestik',
                    data: domestikData
                },
                {
                    name: 'Pit Produksi Step 3',
                    data: produksiStep3Data
                },
                {
                    name: 'Pit Storage',
                    data: storageData
                },
                {
                    name: 'Pit Proses WWTP 2',
                    data: procesWWTP2Data
                },
                {
                    name: 'Pit Outlet',
                    data: outletData
                },
                {
                    name: 'Pit Boiler',
                    data: boilerData
                }
            ]);

            const totalSparta = spartaData.reduce((sum, val) => sum + val, 0);
            const totalGaram = garamData.reduce((sum, val) => sum + val, 0);
            const totalDomestik = domestikData.reduce((sum, val) => sum + val, 0);
            const totalProduksiStep3 = produksiStep3Data.reduce((sum, val) => sum + val, 0);
            const totalStorage = storageData.reduce((sum, val) => sum + val, 0);
            const totalProcesWWTP2 = procesWWTP2Data.reduce((sum, val) => sum + val, 0);
            const totalOutlet = outletData.reduce((sum, val) => sum + val, 0);
            const totalBoiler = boilerData.reduce((sum, val) => sum + val, 0);

            weeklyInfluentPieChart.updateSeries([totalSparta, totalGaram, totalDomestik, totalProduksiStep3, totalStorage, totalProcesWWTP2, totalOutlet, totalBoiler]);

        } catch (error) {
            console.error('Error updating weekly influent chart:', error);
            alert('Error loading influent chart data. Please try again.');
        }
    }

    async function updateWeeklyEffluentChart() {
        try {
            const startDate = document.getElementById('weeklyEffluentStartDate').value;
            const endDate = document.getElementById('weeklyEffluentEndDate').value;

            if (!startDate || !endDate) {
                console.warn('Start or end date not set for weekly effluent');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date must be before end date');
                return;
            }

            const response = await fetch(`/api/wwtp/dashboard/effluent-chart?start_date=${startDate}&end_date=${endDate}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Weekly effluent chart data:', data);

            if (!data || data.length === 0) {
                console.warn('No weekly effluent data available');
                weeklyEffluentChart.updateSeries([{
                        name: 'Full Proses',
                        data: []
                    },
                    {
                        name: 'DAF Pre',
                        data: []
                    }
                ]);
                weeklyEffluentPieChart.updateSeries([0, 0]);
                return;
            }

            const categories = data.map(d => {
                const date = new Date(d.tanggal);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });

            const fullProsesData = data.map(d => parseFloat(d.full_proses) || 0);
            const dafPreData = data.map(d => parseFloat(d.daf_pre) || 0);

            weeklyEffluentChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });

            weeklyEffluentChart.updateSeries([{
                    name: 'Full Proses',
                    data: fullProsesData
                },
                {
                    name: 'DAF Pre',
                    data: dafPreData
                }
            ]);

            const totalFullProses = fullProsesData.reduce((sum, val) => sum + val, 0);
            const totalDafPre = dafPreData.reduce((sum, val) => sum + val, 0);

            weeklyEffluentPieChart.updateSeries([totalFullProses, totalDafPre]);

        } catch (error) {
            console.error('Error updating weekly effluent chart:', error);
            alert('Error loading effluent chart data. Please try again.');
        }
    }

    async function loadWeeklyMonthlyComparison() {
        try {
            const response = await fetch('/api/wwtp/dashboard/monthly-comparison');

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Weekly monthly comparison:', data);

            if (!data || data.length === 0) {
                console.warn('No monthly comparison data available');
                return;
            }

            weeklyMonthlyComparisonChart.updateOptions({
                xaxis: {
                    categories: data.map(d => d.month)
                }
            });

            weeklyMonthlyComparisonChart.updateSeries([{
                    name: 'Influent',
                    data: data.map(d => parseFloat(d.influent) || 0)
                },
                {
                    name: 'Effluent',
                    data: data.map(d => parseFloat(d.effluent) || 0)
                }
            ]);

        } catch (error) {
            console.error('Error loading weekly monthly comparison:', error);
        }
    }

    

    // =============================
    // DAILY DATA FUNCTIONS
    // =============================

    async function loadDailyData() {
        console.log('Loading daily data...');
        try {
            await loadDailyStatistics();
            await updateDailyInfluentChart();
            await updateDailyShiftBreakdownChart();
            await loadDailyMonthlyComparison();
            console.log('Daily data loaded successfully');
        } catch (error) {
            console.error('Error loading daily data:', error);
        }
    }

    async function loadDailyStatistics() {
        try {
            const response = await fetch('/api/wwtp/dashboard/statistics-harian');

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Daily statistics:', data);

            animateValue('dailyTotalShifts', 0, data.total_records || 0, 1000);
            animateValue('dailyTotalDays', 0, data.total_days || 0, 1000);
            animateValue('dailyWeekShifts', 0, data.total_shifts_this_week || 0, 1000);

            if (data.last_update) {
                const date = new Date(data.last_update);
                const dateStr = date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
                const shiftLabel = data.last_shift ? ` (${data.last_shift})` : '';
                document.getElementById('dailyLastUpdate').textContent = dateStr + shiftLabel;
            }

            const statusText = (data.total_shifts_today || 0) === 0 ?
                'Belum ada data hari ini' :
                `${data.total_shifts_today} shift hari ini`;
            const statusClass = (data.total_shifts_today || 0) === 0 ?
                'badge bg-warning-subtle text-warning' :
                'badge bg-success-subtle text-success';

            document.getElementById('dailyTodayStatus').textContent = statusText;
            document.getElementById('dailyTodayStatus').className = statusClass;

            document.getElementById('dailyMonthlyAvg').textContent = data.monthly_avg_per_day || '0';

        } catch (error) {
            console.error('Error loading daily statistics:', error);
            alert('Error loading daily statistics. Please check console for details.');
        }
    }

    async function updateDailyInfluentChart() {
        try {
            const startDate = document.getElementById('dailyInfluentStartDate').value;
            const endDate = document.getElementById('dailyInfluentEndDate').value;

            if (!startDate || !endDate) {
                console.warn('Start or end date not set for daily influent');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date must be before end date');
                return;
            }

            const response = await fetch(`/api/wwtp/dashboard/influent-harian-chart?start_date=${startDate}&end_date=${endDate}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Daily influent chart data:', data);

            if (!data || data.length === 0) {
                console.warn('No daily influent data available');
                dailyInfluentChart.updateSeries([{
                        name: 'Pit Sparta',
                        data: []
                    },
                    {
                        name: 'Pit Garam',
                        data: []
                    },
                    {
                        name: 'Pit Domestik',
                        data: []
                    },
                    {
                        name: 'Pit Produksi Step 3',
                        data: []
                    },
                    {
                        name: 'Pit Storage',
                        data: []
                    },
                    {
                        name: 'Pit Proses WWTP2',
                        data: []
                    },
                    {
                        name: 'Pit Outlet',
                        data: []
                    },
                    {
                        name: 'Pit Boiler',
                        data: []
                    }
                ]);
                await updateDailyShiftBreakdownChart(); // ← tambah ini
                return;
            }

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
            const produksiStep3Data = data.map(d => parseFloat(d.pit_produksi_step3) || 0);
            const storageData = data.map(d => parseFloat(d.pit_storage) || 0);
            const prosesWwtp2Data = data.map(d => parseFloat(d.pit_proses_wwtp2) || 0);
            const outletData = data.map(d => parseFloat(d.pit_outlet) || 0);
            const boilerData = data.map(d => parseFloat(d.pit_boiler) || 0);

            dailyInfluentChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });

            dailyInfluentChart.updateSeries([{
                    name: 'Pit Sparta',
                    data: spartaData
                },
                {
                    name: 'Pit Garam',
                    data: garamData
                },
                {
                    name: 'Pit Domestik',
                    data: domestikData
                },
                {
                    name: 'Pit Produksi Step 3',
                    data: produksiStep3Data
                },
                {
                    name: 'Pit Storage',
                    data: storageData
                },
                {
                    name: 'Pit Proses WWTP2',
                    data: prosesWwtp2Data
                },
                {
                    name: 'Pit Outlet',
                    data: outletData
                },
                {
                    name: 'Pit Boiler',
                    data: boilerData
                }
            ]);

            await updateDailyShiftBreakdownChart(); // ← tambah ini

        } catch (error) {
            console.error('Error updating daily influent chart:', error);
            alert('Error loading daily influent chart data. Please try again.');
        }
    }

    async function updateDailyShiftBreakdownChart() {
        try {
            const startDate = document.getElementById('dailyInfluentStartDate').value;
            const endDate = document.getElementById('dailyInfluentEndDate').value;

            if (!startDate || !endDate) {
                console.warn('Dates not set for shift breakdown');
                return;
            }

            const response = await fetch(`/api/wwtp/dashboard/shift-breakdown?start_date=${startDate}&end_date=${endDate}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Shift breakdown data:', data);

            const labels = [
                'Pit Sparta', 'Pit Garam', 'Pit Domestik', 'Pit Produksi Step 3',
                'Pit Storage', 'Pit Proses WWTP2', 'Pit Outlet', 'Pit Boiler'
            ];

            const values = [
                data.total_sparta || 0,
                data.total_garam || 0,
                data.total_domestik || 0,
                data.total_produksi_step3 || 0,
                data.total_storage || 0,
                data.total_proses_wwtp2 || 0,
                data.total_outlet || 0,
                data.total_boiler || 0,
            ];

            const hasData = values.some(v => v > 0);
            if (!hasData) {
                console.warn('No shift breakdown data available');
                dailyShiftPieChart.updateSeries([0, 0, 0, 0, 0, 0, 0, 0]);
                return;
            }

            dailyShiftPieChart.updateOptions({
                labels: labels
            });
            dailyShiftPieChart.updateSeries(values);

        } catch (error) {
            console.error('Error updating daily shift breakdown chart:', error);
        }
    }

    async function loadDailyMonthlyComparison() {
        try {
            const response = await fetch('/api/wwtp/dashboard/monthly-comparison-harian');

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Daily monthly comparison:', data);

            if (!data || data.length === 0) {
                console.warn('No daily monthly comparison data available');
                return;
            }

            dailyMonthlyComparisonChart.updateOptions({
                xaxis: {
                    categories: data.map(d => d.month)
                }
            });

            dailyMonthlyComparisonChart.updateSeries([{
                name: 'Influent',
                data: data.map(d => parseFloat(d.influent) || 0)
            }]);

        } catch (error) {
            console.error('Error loading daily monthly comparison:', error);
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
            colors: ['#4bc0c0', '#ff6384', '#36a2eb', '#ffce56', '#9966ff', '#ff9f40', '#4dc9f6', '#f67019'],
            dataLabels: {
                enabled: true, // AKTIFKAN dataLabels
                formatter: function(value) {
                    if (!value || value === 0) return '0';
                    return value.toFixed(0);
                },
                style: {
                    fontSize: '10px',
                    colors: ['#304758']
                },
                background: {
                    enabled: true,
                    foreColor: '#fff',
                    borderRadius: 2,
                    padding: 4,
                    opacity: 0.9,
                    borderWidth: 1,
                    borderColor: '#fff'
                }
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
            yaxis: {
                title: {
                    text: 'Volume (m³)'
                },
                labels: {
                    formatter: (value) => value ? value.toFixed(0) + ' m³' : '0 m³'
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: (value) => value ? value.toFixed(2) + ' m³' : '0 m³'
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

        const pieChartOptions = {
            chart: {
                type: 'donut',
                height: 320,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#4bc0c0', '#ff6384', '#36a2eb', '#ffce56', '#9966ff', '#ff9f40', '#4dc9f6', '#f67019'],
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    const value = opts.w.globals.series[opts.seriesIndex];
                    if (!value || value === 0) return '0 m³';
                    return value.toFixed(1) + ' m³\n(' + val.toFixed(1) + '%)';
                },
                style: {
                    fontSize: '11px',
                    fontWeight: 'bold',
                    colors: ['#fff']
                },
                dropShadow: {
                    enabled: true,
                    top: 1,
                    left: 1,
                    blur: 1,
                    opacity: 0.45
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
                                fontSize: '14px',
                                fontWeight: 'bold',
                                formatter: (w) => {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return total.toFixed(2) + ' m³';
                                }
                            }
                        }
                    }
                }
            }
        };

        const barChartOptions = {
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
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded',
                    dataLabels: {
                        position: 'top' // top, center, bottom
                    }
                }
            },
            dataLabels: {
                enabled: true, // AKTIFKAN dataLabels untuk bar chart
                formatter: function(value) {
                    if (!value || value === 0) return '';
                    return value.toFixed(0);
                },
                offsetY: -20,
                style: {
                    fontSize: '10px',
                    colors: ['#304758'],
                    fontWeight: 'bold'
                }
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: []
            },
            yaxis: {
                title: {
                    text: 'Volume (m³)'
                },
                labels: {
                    formatter: (value) => value ? value.toFixed(0) + ' m³' : '0 m³'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: (value) => value ? value.toFixed(2) + ' m³' : '0 m³'
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

        // Weekly Charts
        try {
            weeklyInfluentChart = new ApexCharts(document.querySelector("#weeklyInfluentChart"), {
                ...areaChartOptions,
                colors: ['#4bc0c0', '#ff6384', '#36a2eb'],
                series: [{
                        name: 'Pit Sparta',
                        data: []
                    },
                    {
                        name: 'Pit Garam',
                        data: []
                    },
                    {
                        name: 'Pit Domestik',
                        data: []
                    }
                ]
            });
            weeklyInfluentChart.render();
            console.log('Weekly influent chart initialized');
        } catch (error) {
            console.error('Error initializing weekly influent chart:', error);
        }

        try {
            weeklyEffluentChart = new ApexCharts(document.querySelector("#weeklyEffluentChart"), {
                ...areaChartOptions,
                colors: ['#9966ff', '#ff9f40'],
                series: [{
                        name: 'Full Proses',
                        data: []
                    },
                    {
                        name: 'DAF Pre',
                        data: []
                    }
                ]
            });
            weeklyEffluentChart.render();
            console.log('Weekly effluent chart initialized');
        } catch (error) {
            console.error('Error initializing weekly effluent chart:', error);
        }

        try {
            weeklyInfluentPieChart = new ApexCharts(document.querySelector("#weeklyInfluentPieChart"), {
                ...pieChartOptions,
                series: [0, 0, 0, 0, 0, 0, 0, 0],
                labels: ['Pit Sparta', 'Pit Garam', 'Pit Domestik', 'Pit Produksi Step 3', 'Pit Storage', 'Pit Proses WWTP2', 'Pit Outlet', 'Pit Boiler']
            });
            weeklyInfluentPieChart.render();
            console.log('Weekly influent pie chart initialized');
        } catch (error) {
            console.error('Error initializing weekly influent pie chart:', error);
        }

        try {
            weeklyEffluentPieChart = new ApexCharts(document.querySelector("#weeklyEffluentPieChart"), {
                ...pieChartOptions,
                colors: ['#9966ff', '#ff9f40'],
                series: [0, 0],
                labels: ['Full Proses', 'DAF Pre']
            });
            weeklyEffluentPieChart.render();
            console.log('Weekly effluent pie chart initialized');
        } catch (error) {
            console.error('Error initializing weekly effluent pie chart:', error);
        }

        try {
            weeklyMonthlyComparisonChart = new ApexCharts(document.querySelector("#weeklyMonthlyComparisonChart"), {
                ...barChartOptions,
                colors: ['#36a2eb', '#9966ff'],
                series: [{
                        name: 'Influent',
                        data: []
                    },
                    {
                        name: 'Effluent',
                        data: []
                    }
                ]
            });
            weeklyMonthlyComparisonChart.render();
            console.log('Weekly monthly comparison chart initialized');
        } catch (error) {
            console.error('Error initializing weekly monthly comparison chart:', error);
        }

        // Daily Charts
        try {
            dailyInfluentChart = new ApexCharts(document.querySelector("#dailyInfluentChart"), {
                ...areaChartOptions,
                series: [{
                        name: 'Pit Sparta',
                        data: []
                    },
                    {
                        name: 'Pit Garam',
                        data: []
                    },
                    {
                        name: 'Pit Domestik',
                        data: []
                    },
                    {
                        name: 'Pit Produksi Step 3',
                        data: []
                    },
                    {
                        name: 'Pit Storage',
                        data: []
                    },
                    {
                        name: 'Pit Proses WWTP2',
                        data: []
                    },
                    {
                        name: 'Pit Outlet',
                        data: []
                    },
                    {
                        name: 'Pit Boiler',
                        data: []
                    }
                ]
            });
            dailyInfluentChart.render();
            console.log('Daily influent chart initialized');
        } catch (error) {
            console.error('Error initializing daily influent chart:', error);
        }

        try {
            dailyShiftPieChart = new ApexCharts(document.querySelector("#dailyShiftPieChart"), {
                ...pieChartOptions,
                series: [0, 0, 0, 0, 0, 0, 0, 0],
                labels: ['Pit Sparta', 'Pit Garam', 'Pit Domestik', 'Pit Produksi Step 3', 'Pit Storage', 'Pit Proses WWTP2', 'Pit Outlet', 'Pit Boiler']
            });
            dailyShiftPieChart.render();
            console.log('Daily shift pie chart initialized');
        } catch (error) {
            console.error('Error initializing daily shift pie chart:', error);
        }

        try {
            dailyMonthlyComparisonChart = new ApexCharts(document.querySelector("#dailyMonthlyComparisonChart"), {
                ...barChartOptions,
                colors: ['#36a2eb'],
                series: [{
                    name: 'Influent',
                    data: []
                }]
            });
            dailyMonthlyComparisonChart.render();
            console.log('Daily monthly comparison chart initialized');
        } catch (error) {
            console.error('Error initializing daily monthly comparison chart:', error);
        }

        console.log('All charts initialized');
    }
</script>
@endsection