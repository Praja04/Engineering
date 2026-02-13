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

        <!-- ========================================= -->
        <!-- SECTION: DATA MINGGUAN (WEEKLY) -->
        <!-- ========================================= -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary-subtle">
                    <div class="card-body">
                        <h4 class="card-title text-primary mb-0">
                            <i class="bx bx-calendar-week"></i> Data Mingguan (Weekly Performance)
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
                                    <span id="weeklyAvgTSS">0</span> <small class="fs-14 text-muted">mg/L</small>
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
                                    <span id="weeklyAvgCOD">0</span> <small class="fs-14 text-muted">mg/L</small>
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

        <!-- Charts Row - Weekly -->
        <div class="row">
            <!-- TSS Trend Chart -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">TSS Trend - <span id="weeklyTssTrendTitle">Equal</span></h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="weeklyTssStartDate" style="width: 150px;">
                                <span class="align-self-center">to</span>
                                <input type="date" class="form-control form-control-sm" id="weeklyTssEndDate" style="width: 150px;">
                                <button class="btn btn-sm btn-primary" onclick="updateWeeklyTssChart()">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="weeklyTssChart"></div>
                    </div>
                </div>
            </div>

            <!-- COD Trend Chart -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">COD Trend - <span id="weeklyCodTrendTitle">Equal</span></h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="weeklyCodStartDate" style="width: 150px;">
                                <span class="align-self-center">to</span>
                                <input type="date" class="form-control form-control-sm" id="weeklyCodEndDate" style="width: 150px;">
                                <button class="btn btn-sm btn-primary" onclick="updateWeeklyCodChart()">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="weeklyCodChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Combined TSS & COD Chart -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">TSS & COD Comparison - <span id="weeklyCombinedTitle">Equal</span></h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="weeklyCombinedStartDate" style="width: 150px;">
                                <span class="align-self-center">to</span>
                                <input type="date" class="form-control form-control-sm" id="weeklyCombinedEndDate" style="width: 150px;">
                                <button class="btn btn-sm btn-primary" onclick="updateWeeklyCombinedChart()">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="weeklyCombinedChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Comparison Chart - Weekly -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">6-Month Performance Comparison (All Process Types)</h4>
                    </div>
                    <div class="card-body">
                        <div id="weeklyMonthlyComparisonChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Records Table - Weekly -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Recent Weekly Performance Records</h4>
                        <div class="flex-shrink-0">
                            <button class="btn btn-sm btn-primary" onclick="loadWeeklyRecentRecords()">
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
                                    </tr>
                                </thead>
                                <tbody id="weeklyRecentRecordsTable">
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

        <!-- ========================================= -->
        <!-- SECTION: DATA HARIAN (DAILY) -->
        <!-- ========================================= -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-success-subtle">
                    <div class="card-body">
                        <h4 class="card-title text-success mb-0">
                            <i class="bx bx-calendar"></i> Data Harian (Daily PH Monitoring)
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

        <!-- PH Monitoring Point Selection -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Select PH Monitoring Point</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="equalisasi_1" value="equalisasi_1" checked>
                                <label class="btn btn-outline-primary w-100" for="equalisasi_1">
                                    Equalisasi 1
                                </label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="equalisasi_2" value="equalisasi_2">
                                <label class="btn btn-outline-primary w-100" for="equalisasi_2">
                                    Equalisasi 2
                                </label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="netralisasi" value="netralisasi">
                                <label class="btn btn-outline-info w-100" for="netralisasi">
                                    Netralisasi
                                </label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="sedimentasi_1" value="sedimentasi_1">
                                <label class="btn btn-outline-info w-100" for="sedimentasi_1">
                                    Sedimentasi 1
                                </label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="sedimentasi_2" value="sedimentasi_2">
                                <label class="btn btn-outline-info w-100" for="sedimentasi_2">
                                    Sedimentasi 2
                                </label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="ph_outlet_anaerob" value="outlet_anaerob">
                                <label class="btn btn-outline-success w-100" for="ph_outlet_anaerob">
                                    Outlet Anaerob
                                </label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="ph_aerob" value="aerob">
                                <label class="btn btn-outline-success w-100" for="ph_aerob">
                                    Aerob
                                </label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="lumpur_aktif" value="lumpur_aktif">
                                <label class="btn btn-outline-warning w-100" for="lumpur_aktif">
                                    Lumpur Aktif
                                </label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="clarifier_2" value="clarifier_2">
                                <label class="btn btn-outline-warning w-100" for="clarifier_2">
                                    Clarifier 2
                                </label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="ph_outlet" value="outlet">
                                <label class="btn btn-outline-danger w-100" for="ph_outlet">
                                    Outlet
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row - Daily -->
        <div class="row">
            <!-- PH Trend Chart -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">PH Trend - <span id="dailyPhTrendTitle">Equalisasi 1</span></h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="dailyStartDate" style="width: 150px;">
                                <span class="align-self-center">to</span>
                                <input type="date" class="form-control form-control-sm" id="dailyEndDate" style="width: 150px;">
                                <button class="btn btn-sm btn-primary" onclick="updateDailyPhChart()">
                                    <i class="bx bx-search-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="dailyPhChart"></div>
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
                        <h4 class="card-title mb-0">6-Month PH Comparison (All Monitoring Points)</h4>
                    </div>
                    <div class="card-body">
                        <div id="dailyMonthlyComparisonChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Records Table - Daily -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Recent Daily PH Records</h4>
                        <div class="flex-shrink-0">
                            <button class="btn btn-sm btn-primary" onclick="loadDailyRecentRecords()">
                                <i class="bx bx-refresh"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Shifts</th>
                                        <th scope="col">Details</th>
                                    </tr>
                                </thead>
                                <tbody id="dailyRecentRecordsTable">
                                    <tr>
                                        <td colspan="3" class="text-center">
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
    // Chart instances
    let weeklyTssChart, weeklyCodChart, weeklyCombinedChart, weeklyMonthlyComparisonChart;
    let dailyPhChart, dailyShiftPieChart, dailyMonthlyComparisonChart;

    // Current selections
    let currentProcessType = 'equal';
    let currentPhPoint = 'equalisasi_1';

    // Load all data on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Set default dates
        setDefaultDates();

        // Initialize charts
        initCharts();

        // Load data
        loadWeeklyData();
        loadDailyData();

        // Event listeners for weekly process type selection
        document.querySelectorAll('input[name="processType"]').forEach(radio => {
            radio.addEventListener('change', function() {
                currentProcessType = this.value;
                updateWeeklyProcessTypeTitle(this.value);
                updateWeeklyCharts();
            });
        });

        // Event listeners for daily PH point selection
        document.querySelectorAll('input[name="phPoint"]').forEach(radio => {
            radio.addEventListener('change', function() {
                currentPhPoint = this.value;
                updateDailyPhPointTitle(this.value);
                updateDailyPhChart();
            });
        });
    });

    // Set default dates
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

        // Weekly TSS dates
        document.getElementById('weeklyTssStartDate').value = formatDate(firstDay);
        document.getElementById('weeklyTssEndDate').value = formatDate(lastDay);

        // Weekly COD dates
        document.getElementById('weeklyCodStartDate').value = formatDate(firstDay);
        document.getElementById('weeklyCodEndDate').value = formatDate(lastDay);

        // Weekly Combined dates
        document.getElementById('weeklyCombinedStartDate').value = formatDate(firstDay);
        document.getElementById('weeklyCombinedEndDate').value = formatDate(lastDay);

        // Daily PH dates
        document.getElementById('dailyStartDate').value = formatDate(firstDay);
        document.getElementById('dailyEndDate').value = formatDate(lastDay);
    }

    // =============================
    // WEEKLY DATA FUNCTIONS
    // =============================

    async function loadWeeklyData() {
        await loadWeeklyStatistics();
        await updateWeeklyCharts();
        await loadWeeklyMonthlyComparison();
        await loadWeeklyRecentRecords();
    }

    async function loadWeeklyStatistics() {
        try {
            const response = await fetch('/api/wwtp-performance/dashboard/statistics');
            const data = await response.json();

            animateValue('weeklyTotalRecords', 0, data.total_records || 0, 1000);
            animateValue('weeklyRecords', 0, data.total_records_this_week || 0, 1000);

            let totalTSS = 0;
            let totalCOD = 0;
            let recordCount = 0;

            if (data.weekly_summary && typeof data.weekly_summary === 'object' && Object.keys(data.weekly_summary).length > 0) {
                Object.values(data.weekly_summary).forEach(item => {
                    if (item.avg_tss !== undefined && item.avg_tss !== null) {
                        totalTSS += parseFloat(item.avg_tss) || 0;
                        recordCount++;
                    }
                    if (item.avg_cod !== undefined && item.avg_cod !== null) {
                        totalCOD += parseFloat(item.avg_cod) || 0;
                    }
                });
            }

            const avgTSS = recordCount > 0 ? (totalTSS / recordCount).toFixed(2) : '0.00';
            const avgCOD = recordCount > 0 ? (totalCOD / recordCount).toFixed(2) : '0.00';

            document.getElementById('weeklyAvgTSS').textContent = avgTSS;
            document.getElementById('weeklyAvgCOD').textContent = avgCOD;

            const weeklyStatusEl = document.getElementById('weeklyStatus');
            const totalWeekly = data.total_records_this_week || 0;
            if (totalWeekly > 0) {
                weeklyStatusEl.textContent = `${totalWeekly} records this week`;
                weeklyStatusEl.className = 'badge bg-success-subtle text-success';
            } else {
                weeklyStatusEl.textContent = 'No data this week';
                weeklyStatusEl.className = 'badge bg-warning-subtle text-warning';
            }

        } catch (error) {
            console.error('Error loading weekly statistics:', error);
        }
    }

    function updateWeeklyProcessTypeTitle(type) {
        const titles = {
            'equal': 'Equal',
            'outlet_anaerob': 'Outlet Anaerob',
            'aerob': 'Aerob',
            'daf': 'DAF',
            'outlet': 'Outlet'
        };
        const title = titles[type] || type;
        document.getElementById('weeklyTssTrendTitle').textContent = title;
        document.getElementById('weeklyCodTrendTitle').textContent = title;
        document.getElementById('weeklyCombinedTitle').textContent = title;
    }

    async function updateWeeklyCharts() {
        await updateWeeklyTssChart();
        await updateWeeklyCodChart();
        await updateWeeklyCombinedChart();
    }

    async function updateWeeklyTssChart() {
        try {
            const startDate = document.getElementById('weeklyTssStartDate').value;
            const endDate = document.getElementById('weeklyTssEndDate').value;

            if (!startDate || !endDate) {
                alert('Please select both start and end dates');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date must be before end date');
                return;
            }

            const response = await fetch(`/api/wwtp-performance/dashboard/chart/${currentProcessType}?start_date=${startDate}&end_date=${endDate}`);
            const data = await response.json();

            const categories = data.map(d => {
                const date = new Date(d.tanggal || d.created_at);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });

            const tssData = data.map(d => parseFloat(d.tss) || 0);

            weeklyTssChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });

            weeklyTssChart.updateSeries([{
                name: 'TSS',
                data: tssData
            }]);

        } catch (error) {
            console.error('Error updating weekly TSS chart:', error);
            alert('Error loading data. Please try again.');
        }
    }

    async function updateWeeklyCodChart() {
        try {
            const startDate = document.getElementById('weeklyCodStartDate').value;
            const endDate = document.getElementById('weeklyCodEndDate').value;

            if (!startDate || !endDate) {
                alert('Please select both start and end dates');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date must be before end date');
                return;
            }

            const response = await fetch(`/api/wwtp-performance/dashboard/chart/${currentProcessType}?start_date=${startDate}&end_date=${endDate}`);
            const data = await response.json();

            const categories = data.map(d => {
                const date = new Date(d.tanggal || d.created_at);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });

            const codData = data.map(d => parseFloat(d.cod) || 0);

            weeklyCodChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });

            weeklyCodChart.updateSeries([{
                name: 'COD',
                data: codData
            }]);

        } catch (error) {
            console.error('Error updating weekly COD chart:', error);
            alert('Error loading data. Please try again.');
        }
    }

    async function updateWeeklyCombinedChart() {
        try {
            const startDate = document.getElementById('weeklyCombinedStartDate').value;
            const endDate = document.getElementById('weeklyCombinedEndDate').value;

            if (!startDate || !endDate) {
                alert('Please select both start and end dates');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date must be before end date');
                return;
            }

            const response = await fetch(`/api/wwtp-performance/dashboard/chart/${currentProcessType}?start_date=${startDate}&end_date=${endDate}`);
            const data = await response.json();

            const categories = data.map(d => {
                const date = new Date(d.tanggal || d.created_at);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });

            const tssData = data.map(d => parseFloat(d.tss) || 0);
            const codData = data.map(d => parseFloat(d.cod) || 0);

            weeklyCombinedChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });

            weeklyCombinedChart.updateSeries([{
                    name: 'TSS',
                    data: tssData
                },
                {
                    name: 'COD',
                    data: codData
                }
            ]);

        } catch (error) {
            console.error('Error updating weekly combined chart:', error);
            alert('Error loading data. Please try again.');
        }
    }

    async function loadWeeklyMonthlyComparison() {
        try {
            const response = await fetch('/api/wwtp-performance/dashboard/monthly-comparison');
            const data = await response.json();

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

            weeklyMonthlyComparisonChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });
            weeklyMonthlyComparisonChart.updateSeries(series);

        } catch (error) {
            console.error('Error loading weekly monthly comparison:', error);
        }
    }

    async function loadWeeklyRecentRecords() {
        try {
            const response = await fetch('/api/wwtp-performance/dashboard/recent/10');
            const data = await response.json();

            const tbody = document.getElementById('weeklyRecentRecordsTable');

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
                   
                </tr>
            `;
            }).join('');

        } catch (error) {
            console.error('Error loading weekly recent records:', error);
            document.getElementById('weeklyRecentRecordsTable').innerHTML =
                '<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>';
        }
    }

    // =============================
    // DAILY DATA FUNCTIONS
    // =============================

    async function loadDailyData() {
        await loadDailyStatistics();
        await updateDailyPhChart();
        await updateDailyShiftBreakdownChart();
        await loadDailyMonthlyComparison();
        await loadDailyRecentRecords();
    }

    async function loadDailyStatistics() {
        try {
            const response = await fetch('/api/wwtp-performance/dashboard/statistics-harian');
            const data = await response.json();

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

            const statusText = data.total_shifts_today === 0 ?
                'Belum ada data hari ini' :
                `${data.total_shifts_today} shift hari ini`;
            const statusClass = data.total_shifts_today === 0 ?
                'badge bg-warning-subtle text-warning' :
                'badge bg-success-subtle text-success';

            document.getElementById('dailyTodayStatus').textContent = statusText;
            document.getElementById('dailyTodayStatus').className = statusClass;

        } catch (error) {
            console.error('Error loading daily statistics:', error);
        }
    }

    function updateDailyPhPointTitle(point) {
        const titles = {
            'equalisasi_1': 'Equalisasi 1',
            'equalisasi_2': 'Equalisasi 2',
            'netralisasi': 'Netralisasi',
            'sedimentasi_1': 'Sedimentasi 1',
            'sedimentasi_2': 'Sedimentasi 2',
            'outlet_anaerob': 'Outlet Anaerob',
            'aerob': 'Aerob',
            'lumpur_aktif': 'Lumpur Aktif',
            'clarifier_2': 'Clarifier 2',
            'outlet': 'Outlet'
        };
        const title = titles[point] || point;
        document.getElementById('dailyPhTrendTitle').textContent = title;
    }

    async function updateDailyPhChart() {
        try {
            const startDate = document.getElementById('dailyStartDate').value;
            const endDate = document.getElementById('dailyEndDate').value;

            if (!startDate || !endDate) {
                alert('Please select both start and end dates');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date must be before end date');
                return;
            }

            const response = await fetch(`/api/wwtp-performance/dashboard/chart-harian?start_date=${startDate}&end_date=${endDate}`);
            const data = await response.json();

            const categories = data.map(d => {
                const date = new Date(d.tanggal);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            });

            const phData = data.map(d => parseFloat(d[currentPhPoint]) || 0);

            dailyPhChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });

            dailyPhChart.updateSeries([{
                name: 'PH Level',
                data: phData
            }]);

            // Also update shift breakdown chart with same date range
            await updateDailyShiftBreakdownChart();

        } catch (error) {
            console.error('Error updating daily PH chart:', error);
            alert('Error loading data. Please try again.');
        }
    }

    async function updateDailyShiftBreakdownChart() {
        try {
            const startDate = document.getElementById('dailyStartDate').value;
            const endDate = document.getElementById('dailyEndDate').value;

            if (!startDate || !endDate) {
                return;
            }

            const response = await fetch(`/api/wwtp-performance/dashboard/shift-breakdown?start_date=${startDate}&end_date=${endDate}`);
            const data = await response.json();

            const labels = data.map(d => d.shift.replace('shift', 'Shift '));
            const values = data.map(d => parseInt(d.count) || 0);

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
            const response = await fetch('/api/wwtp-performance/dashboard/monthly-comparison-harian');
            const data = await response.json();

            const categories = data.map(d => d.month);
            const phPoints = ['equalisasi_1', 'equalisasi_2', 'netralisasi', 'sedimentasi_1', 'sedimentasi_2',
                'outlet_anaerob', 'aerob', 'lumpur_aktif', 'clarifier_2', 'outlet'
            ];
            const phNames = {
                'equalisasi_1': 'Equalisasi 1',
                'equalisasi_2': 'Equalisasi 2',
                'netralisasi': 'Netralisasi',
                'sedimentasi_1': 'Sedimentasi 1',
                'sedimentasi_2': 'Sedimentasi 2',
                'outlet_anaerob': 'Outlet Anaerob',
                'aerob': 'Aerob',
                'lumpur_aktif': 'Lumpur Aktif',
                'clarifier_2': 'Clarifier 2',
                'outlet': 'Outlet'
            };

            const series = phPoints.map(point => ({
                name: phNames[point],
                data: data.map(month => parseFloat(month.data[point]) || 0)
            }));

            dailyMonthlyComparisonChart.updateOptions({
                xaxis: {
                    categories: categories
                }
            });
            dailyMonthlyComparisonChart.updateSeries(series);

        } catch (error) {
            console.error('Error loading daily monthly comparison:', error);
        }
    }

    async function loadDailyRecentRecords() {
        try {
            const response = await fetch('/api/wwtp-performance/dashboard/recent-harian/10');
            const data = await response.json();

            const tbody = document.getElementById('dailyRecentRecordsTable');

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center">No data available</td></tr>';
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
                        <div class="mb-2 p-2 border rounded">
                            <span class="badge bg-primary-subtle text-primary mb-1">${shift.shift}</span>
                            <div class="row g-1 small">
                                <div class="col-6">Eq1: ${shift.equalisasi_1 || '-'}</div>
                                <div class="col-6">Eq2: ${shift.equalisasi_2 || '-'}</div>
                                <div class="col-6">Netra: ${shift.netralisasi || '-'}</div>
                                <div class="col-6">Sed1: ${shift.sedimentasi_1 || '-'}</div>
                                <div class="col-6">Sed2: ${shift.sedimentasi_2 || '-'}</div>
                                <div class="col-6">O.Anaerob: ${shift.outlet_anaerob || '-'}</div>
                                <div class="col-6">Aerob: ${shift.aerob || '-'}</div>
                                <div class="col-6">L.Aktif: ${shift.lumpur_aktif || '-'}</div>
                                <div class="col-6">Clar2: ${shift.clarifier_2 || '-'}</div>
                                <div class="col-6">Outlet: ${shift.outlet || '-'}</div>
                            </div>
                        </div>
                    `;
                }).join('');

                return `
                <tr>
                    <td><strong>${formattedDate}</strong></td>
                    <td><span class="badge bg-info-subtle text-info">${record.shift_count} shifts</span></td>
                    <td>${shiftDetails}</td>
                </tr>
            `;
            }).join('');

        } catch (error) {
            console.error('Error loading daily recent records:', error);
            document.getElementById('dailyRecentRecordsTable').innerHTML =
                '<tr><td colspan="3" class="text-center text-danger">Error loading data</td></tr>';
        }
    }

    // =============================
    // UTILITY FUNCTIONS
    // =============================

    function animateValue(id, start, end, duration) {
        const obj = document.getElementById(id);
        if (!obj) return;

        start = parseInt(start) || 0;
        end = parseInt(end) || 0;

        if (start === end) {
            obj.textContent = end;
            return;
        }

        const startTime = Date.now();
        const endTime = startTime + duration;

        function update() {
            const now = Date.now();
            const progress = Math.min((now - startTime) / duration, 1);
            const current = Math.floor(start + (end - start) * progress);
            obj.textContent = current;

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                obj.textContent = end;
            }
        }

        requestAnimationFrame(update);
    }

    function showPhoto(photoPath) {
        const fullPath = `/storage/${photoPath}`;
        document.getElementById('modalImage').src = fullPath;
        const modal = new bootstrap.Modal(document.getElementById('photoModal'));
        modal.show();
    }

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
                loadWeeklyData();
            } else {
                alert('Failed to delete record');
            }
        } catch (error) {
            console.error('Error deleting record:', error);
            alert('Failed to delete record');
        }
    }

    // =============================
    // INITIALIZE CHARTS
    // =============================

    function initCharts() {
        // Common chart options
        const lineChartOptions = {
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
            dataLabels: {
                enabled: true, // AKTIFKAN dataLabels
                formatter: function(value) {
                    if (!value || value === 0) return '0';
                    return value.toFixed(1);
                },
                style: {
                    fontSize: '10px',
                    colors: ['#304758'],
                    fontWeight: 'bold'
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
                width: 3
            },
            xaxis: {
                categories: [],
                labels: {
                    rotate: -45
                }
            },
            tooltip: {
                y: {
                    formatter: (value) => value ? value.toFixed(2) : '0'
                }
            },
            grid: {
                borderColor: '#f1f1f1'
            },
            markers: {
                size: 4,
                hover: {
                    size: 6
                }
            }
        };

        const areaChartOptions = {
            ...lineChartOptions,
            chart: {
                ...lineChartOptions.chart,
                type: 'area'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1
                }
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
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    const value = opts.w.globals.series[opts.seriesIndex];
                    // Tampilkan nilai aktual dan persentase
                    if (!value || value === 0) return '0';
                    return value.toFixed(1) + '\n(' + val.toFixed(1) + '%)';
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
                                    return total.toFixed(2);
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
                height: 400,
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
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true, // AKTIFKAN dataLabels
                formatter: function(value) {
                    if (!value || value === 0) return '';
                    return value.toFixed(1);
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
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        };

        // Weekly Charts
        weeklyTssChart = new ApexCharts(document.querySelector("#weeklyTssChart"), {
            ...lineChartOptions,
            series: [{
                name: 'TSS',
                data: []
            }],
            colors: ['#f59e0b'],
            yaxis: {
                title: {
                    text: 'TSS (mg/L)'
                },
                labels: {
                    formatter: (value) => value ? value.toFixed(1) + ' mg/L' : '0 mg/L'
                }
            },
            tooltip: {
                y: {
                    formatter: (value) => value ? value.toFixed(2) + ' mg/L' : '0 mg/L'
                }
            }
        });
        weeklyTssChart.render();

        weeklyCodChart = new ApexCharts(document.querySelector("#weeklyCodChart"), {
            ...lineChartOptions,
            series: [{
                name: 'COD',
                data: []
            }],
            colors: ['#10b981'],
            yaxis: {
                title: {
                    text: 'COD (mg/L)'
                },
                labels: {
                    formatter: (value) => value ? value.toFixed(1) + ' mg/L' : '0 mg/L'
                }
            },
            tooltip: {
                y: {
                    formatter: (value) => value ? value.toFixed(2) + ' mg/L' : '0 mg/L'
                }
            }
        });
        weeklyCodChart.render();

        weeklyCombinedChart = new ApexCharts(document.querySelector("#weeklyCombinedChart"), {
            ...areaChartOptions,
            series: [{
                    name: 'TSS',
                    data: []
                },
                {
                    name: 'COD',
                    data: []
                }
            ],
            colors: ['#f59e0b', '#10b981'],
            yaxis: {
                title: {
                    text: 'Concentration (mg/L)'
                },
                labels: {
                    formatter: (value) => value ? value.toFixed(1) + ' mg/L' : '0 mg/L'
                }
            },
            tooltip: {
                y: {
                    formatter: (value) => value ? value.toFixed(2) + ' mg/L' : '0 mg/L'
                }
            }
        });
        weeklyCombinedChart.render();

        weeklyMonthlyComparisonChart = new ApexCharts(document.querySelector("#weeklyMonthlyComparisonChart"), {
            ...barChartOptions,
            series: [],
            colors: ['#3b82f6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'],
            yaxis: {
                title: {
                    text: 'Average Concentration (mg/L)'
                },
                labels: {
                    formatter: (value) => value ? value.toFixed(1) + ' mg/L' : '0 mg/L'
                }
            },
            tooltip: {
                y: {
                    formatter: (value) => value ? value.toFixed(2) + ' mg/L' : '0 mg/L'
                }
            }
        });
        weeklyMonthlyComparisonChart.render();

        // Daily Charts
        dailyPhChart = new ApexCharts(document.querySelector("#dailyPhChart"), {
            ...lineChartOptions,
            series: [{
                name: 'PH Level',
                data: []
            }],
            colors: ['#3b82f6'],
            yaxis: {
                title: {
                    text: 'PH Level'
                },
                labels: {
                    formatter: (value) => value ? value.toFixed(2) : '0'
                },
                min: 0,
                max: 14
            },
            tooltip: {
                y: {
                    formatter: (value) => value ? 'PH: ' + value.toFixed(2) : 'PH: 0'
                }
            }
        });
        dailyPhChart.render();

        dailyShiftPieChart = new ApexCharts(document.querySelector("#dailyShiftPieChart"), {
            ...pieChartOptions,
            series: [0, 0, 0],
            labels: ['Shift 1', 'Shift 2', 'Shift 3'],
            colors: ['#4bc0c0', '#ff6384', '#36a2eb'],
            tooltip: {
                y: {
                    formatter: (value) => value ? value.toFixed(2) : '0'
                }
            }
        });
        dailyShiftPieChart.render();

        dailyMonthlyComparisonChart = new ApexCharts(document.querySelector("#dailyMonthlyComparisonChart"), {
            ...barChartOptions,
            series: [],
            colors: ['#3b82f6'],
            yaxis: {
                title: {
                    text: 'Average PH Level'
                },
                labels: {
                    formatter: (value) => value ? value.toFixed(2) : '0'
                },
                min: 0,
                max: 14
            },
            tooltip: {
                y: {
                    formatter: (value) => value ? 'PH: ' + value.toFixed(2) : 'PH: 0'
                }
            }
        });
        dailyMonthlyComparisonChart.render();
    }
</script>
@endsection