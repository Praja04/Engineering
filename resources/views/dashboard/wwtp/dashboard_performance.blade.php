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
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="processType" id="equal" value="equal" checked>
                            <label class="btn btn-outline-primary" for="equal"><i class="bx bx-equalizer"></i> Equal</label>
                            <input type="radio" class="btn-check" name="processType" id="outlet_anaerob" value="outlet_anaerob">
                            <label class="btn btn-outline-info" for="outlet_anaerob"><i class="bx bx-sync"></i> Outlet Anaerob</label>
                            <input type="radio" class="btn-check" name="processType" id="aerob" value="aerob">
                            <label class="btn btn-outline-success" for="aerob"><i class="bx bx-wind"></i> Aerob</label>
                            <input type="radio" class="btn-check" name="processType" id="daf" value="daf">
                            <label class="btn btn-outline-warning" for="daf"><i class="bx bx-filter"></i> DAF</label>
                            <input type="radio" class="btn-check" name="processType" id="outlet" value="outlet">
                            <label class="btn btn-outline-danger" for="outlet"><i class="bx bx-exit"></i> Outlet</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row - Weekly -->
        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">TSS Trend - <span id="weeklyTssTrendTitle">Equal</span></h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="weeklyTssStartDate" style="width:150px;">
                                <span class="align-self-center">to</span>
                                <input type="date" class="form-control form-control-sm" id="weeklyTssEndDate" style="width:150px;">
                                <button class="btn btn-sm btn-primary" onclick="updateWeeklyTssChart()"><i class="bx bx-search-alt"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="weeklyTssChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">COD Trend - <span id="weeklyCodTrendTitle">Equal</span></h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="weeklyCodStartDate" style="width:150px;">
                                <span class="align-self-center">to</span>
                                <input type="date" class="form-control form-control-sm" id="weeklyCodEndDate" style="width:150px;">
                                <button class="btn btn-sm btn-primary" onclick="updateWeeklyCodChart()"><i class="bx bx-search-alt"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="weeklyCodChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">TSS & COD Comparison - <span id="weeklyCombinedTitle">Equal</span></h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="weeklyCombinedStartDate" style="width:150px;">
                                <span class="align-self-center">to</span>
                                <input type="date" class="form-control form-control-sm" id="weeklyCombinedEndDate" style="width:150px;">
                                <button class="btn btn-sm btn-primary" onclick="updateWeeklyCombinedChart()"><i class="bx bx-search-alt"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="weeklyCombinedChart"></div>
                    </div>
                </div>
            </div>
        </div>

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
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" id="dailyTotalShifts">0</span></h4>
                                <p class="text-muted mb-0 text-truncate">All time shifts</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle rounded fs-3"><i class="bx bx-data text-primary"></i></span>
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
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" id="dailyTotalDays">0</span></h4>
                                <p class="text-muted mb-0"><span id="dailyTodayStatus" class="badge bg-success-subtle text-success"></span></p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info-subtle rounded fs-3"><i class="bx bx-calendar-alt text-info"></i></span>
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
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" id="dailyWeekShifts">0</span></h4>
                                <p class="text-muted mb-0 text-truncate">Shifts this week</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success-subtle rounded fs-3"><i class="bx bx-time text-success"></i></span>
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
                                <h6 class="fs-16 fw-semibold mb-4"><span id="dailyLastUpdate">-</span></h6>
                                <p class="text-muted mb-0 text-truncate">Latest shift</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning-subtle rounded fs-3"><i class="bx bx-calendar text-warning"></i></span>
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
                                <label class="btn btn-outline-primary w-100" for="equalisasi_1">Equalisasi 1</label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="equalisasi_2" value="equalisasi_2">
                                <label class="btn btn-outline-primary w-100" for="equalisasi_2">Equalisasi 2</label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="netralisasi" value="netralisasi">
                                <label class="btn btn-outline-info w-100" for="netralisasi">Netralisasi</label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="sedimentasi_1" value="sedimentasi_1">
                                <label class="btn btn-outline-info w-100" for="sedimentasi_1">Sedimentasi 1</label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="sedimentasi_2" value="sedimentasi_2">
                                <label class="btn btn-outline-info w-100" for="sedimentasi_2">Sedimentasi 2</label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="ph_outlet_anaerob" value="outlet_anaerob">
                                <label class="btn btn-outline-success w-100" for="ph_outlet_anaerob">Outlet Anaerob</label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="ph_aerob" value="aerob">
                                <label class="btn btn-outline-success w-100" for="ph_aerob">Aerob</label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="lumpur_aktif" value="lumpur_aktif">
                                <label class="btn btn-outline-warning w-100" for="lumpur_aktif">Lumpur Aktif</label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="clarifier_2" value="clarifier_2">
                                <label class="btn btn-outline-warning w-100" for="clarifier_2">Clarifier 2</label>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <input type="radio" class="btn-check" name="phPoint" id="ph_outlet" value="outlet">
                                <label class="btn btn-outline-danger w-100" for="ph_outlet">Outlet</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">PH Trend - <span id="dailyPhTrendTitle">Equalisasi 1</span></h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="dailyStartDate" style="width:150px;">
                                <span class="align-self-center">to</span>
                                <input type="date" class="form-control form-control-sm" id="dailyEndDate" style="width:150px;">
                                <button class="btn btn-sm btn-primary" onclick="updateDailyPhChart()"><i class="bx bx-search-alt"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="dailyPhChart"></div>
                    </div>
                </div>
            </div>
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

        <!-- ========================================= -->
        <!-- SECTION: DATA SAMPLE -->
        <!-- ========================================= -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-warning-subtle">
                    <div class="card-body">
                        <h4 class="card-title text-warning mb-0">
                            <i class="bx bx-test-tube"></i> Data Sample (Sample Performance Monitoring)
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards - Sample -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Sample</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" id="sampleTotal">0</span></h4>
                                <p class="text-muted mb-0 text-truncate">All time data</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning-subtle rounded fs-3"><i class="bx bx-test-tube text-warning"></i></span>
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
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">This Week</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" id="sampleWeek">0</span></h4>
                                <p class="text-muted mb-0"><span id="sampleWeekStatus" class="badge bg-warning-subtle text-warning"></span></p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info-subtle rounded fs-3"><i class="bx bx-calendar-week text-info"></i></span>
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
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">This Month</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" id="sampleMonth">0</span></h4>
                                <p class="text-muted mb-0 text-truncate">Sample this month</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success-subtle rounded fs-3"><i class="bx bx-calendar text-success"></i></span>
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
                                <h6 class="fs-16 fw-semibold mb-4"><span id="sampleLastUpdate">-</span></h6>
                                <p class="text-muted mb-0 text-truncate">Latest sample</p>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle rounded fs-3"><i class="bx bx-time text-primary"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sample Parameter & Jenis Sampel Selection -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Select Parameter</h4>
                    </div>
                    <div class="card-body">
                        <div class="btn-group w-100 flex-wrap" role="group">
                            <input type="radio" class="btn-check" name="sampleParam" id="sp_tss" value="avg_tss" checked>
                            <label class="btn btn-outline-warning" for="sp_tss">TSS</label>
                            <input type="radio" class="btn-check" name="sampleParam" id="sp_sv30" value="avg_sv30">
                            <label class="btn btn-outline-warning" for="sp_sv30">SV30</label>
                            <input type="radio" class="btn-check" name="sampleParam" id="sp_ph" value="avg_ph">
                            <label class="btn btn-outline-warning" for="sp_ph">pH</label>
                            <input type="radio" class="btn-check" name="sampleParam" id="sp_mlss" value="avg_mlss">
                            <label class="btn btn-outline-warning" for="sp_mlss">MLSS</label>
                            <input type="radio" class="btn-check" name="sampleParam" id="sp_svl" value="avg_svl">
                            <label class="btn btn-outline-warning" for="sp_svl">SVL</label>
                            <input type="radio" class="btn-check" name="sampleParam" id="sp_do" value="avg_do">
                            <label class="btn btn-outline-warning" for="sp_do">DO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Select Jenis Sampel</h4>
                    </div>
                    <div class="card-body">
                        <div id="sampleJenisButtons" class="d-flex flex-wrap gap-2">
                            <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                            <span class="text-muted small ms-2">Memuat jenis sampel...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sample Trend Chart + Pie -->
        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">
                            Trend <span id="sampleParamTitle">TSS</span> - <span id="sampleJenisTitle">All</span>
                        </h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="sampleStartDate" style="width:150px;">
                                <span class="align-self-center">to</span>
                                <input type="date" class="form-control form-control-sm" id="sampleEndDate" style="width:150px;">
                                <button class="btn btn-sm btn-warning text-dark" onclick="updateSampleTrendChart()"><i class="bx bx-search-alt"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="sampleTrendChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Parameter Distribution (Bulan Ini)</h4>
                    </div>
                    <div class="card-body">
                        <div id="samplePieChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Comparison - Sample -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">6-Month Sample Comparison (Per Jenis Sampel)</h4>
                    </div>
                    <div class="card-body">
                        <div id="sampleMonthlyComparisonChart"></div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<!-- Photo Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Performance Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15);
    }

    .counter-value {
        animation: countUp 1s ease-out;
    }

    @keyframes countUp {
        from {
            opacity: 0;
            transform: scale(.8);
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
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }

    #sampleJenisButtons .btn {
        font-size: 0.8rem;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Chart instances
    let weeklyTssChart, weeklyCodChart, weeklyCombinedChart, weeklyMonthlyComparisonChart;
    let dailyPhChart, dailyShiftPieChart, dailyMonthlyComparisonChart;
    let sampleTrendChart, samplePieChart, sampleMonthlyComparisonChart;

    // Current selections
    let currentProcessType = 'equal';
    let currentPhPoint = 'equalisasi_1';
    let currentSampleParam = 'avg_tss';
    let currentSampleJenis = null; // null = semua
    let allJenisSampelData = [];

    const paramLabels = {
        avg_tss: 'TSS (mg/L)',
        avg_sv30: 'SV30 (mL/L)',
        avg_ph: 'pH',
        avg_mlss: 'MLSS (mg/L)',
        avg_svl: 'SVL (mL/g)',
        avg_do: 'DO (mg/L)'
    };
    const paramUnits = {
        avg_tss: 'mg/L',
        avg_sv30: 'mL/L',
        avg_ph: '',
        avg_mlss: 'mg/L',
        avg_svl: 'mL/g',
        avg_do: 'mg/L'
    };

    document.addEventListener('DOMContentLoaded', function() {
        setDefaultDates();
        initCharts();
        loadWeeklyData();
        loadDailyData();
        loadSampleData();

        document.querySelectorAll('input[name="processType"]').forEach(r => {
            r.addEventListener('change', function() {
                currentProcessType = this.value;
                updateWeeklyProcessTypeTitle(this.value);
                updateWeeklyCharts();
            });
        });

        document.querySelectorAll('input[name="phPoint"]').forEach(r => {
            r.addEventListener('change', function() {
                currentPhPoint = this.value;
                updateDailyPhPointTitle(this.value);
                updateDailyPhChart();
            });
        });

        document.querySelectorAll('input[name="sampleParam"]').forEach(r => {
            r.addEventListener('change', function() {
                currentSampleParam = this.value;
                const labelMap = {
                    avg_tss: 'TSS',
                    avg_sv30: 'SV30',
                    avg_ph: 'pH',
                    avg_mlss: 'MLSS',
                    avg_svl: 'SVL',
                    avg_do: 'DO'
                };
                document.getElementById('sampleParamTitle').textContent = labelMap[this.value] || this.value;
                updateSampleTrendChart();
            });
        });
    });

    function setDefaultDates() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        const fmt = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;

        ['weeklyTssStartDate', 'weeklyCodStartDate', 'weeklyCombinedStartDate', 'dailyStartDate', 'sampleStartDate']
        .forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = fmt(firstDay);
        });
        ['weeklyTssEndDate', 'weeklyCodEndDate', 'weeklyCombinedEndDate', 'dailyEndDate', 'sampleEndDate']
        .forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = fmt(lastDay);
        });
    }

    // ==============================
    // WEEKLY
    // ==============================
    async function loadWeeklyData() {
        await loadWeeklyStatistics();
        await updateWeeklyCharts();
        await loadWeeklyMonthlyComparison();
    }

    async function loadWeeklyStatistics() {
        try {
            const data = await fetchJSON('/api/wwtp-performance/dashboard/statistics');
            animateValue('weeklyTotalRecords', 0, data.total_records || 0, 1000);
            animateValue('weeklyRecords', 0, data.total_records_this_week || 0, 1000);
            let totalTSS = 0,
                totalCOD = 0,
                recordCount = 0;
            if (data.weekly_summary && Object.keys(data.weekly_summary).length > 0) {
                Object.values(data.weekly_summary).forEach(item => {
                    if (item.avg_tss != null) {
                        totalTSS += parseFloat(item.avg_tss) || 0;
                        recordCount++;
                    }
                    if (item.avg_cod != null) totalCOD += parseFloat(item.avg_cod) || 0;
                });
            }
            document.getElementById('weeklyAvgTSS').textContent = recordCount > 0 ? (totalTSS / recordCount).toFixed(2) : '0.00';
            document.getElementById('weeklyAvgCOD').textContent = recordCount > 0 ? (totalCOD / recordCount).toFixed(2) : '0.00';
            const el = document.getElementById('weeklyStatus');
            const n = data.total_records_this_week || 0;
            el.textContent = n > 0 ? `${n} records this week` : 'No data this week';
            el.className = n > 0 ? 'badge bg-success-subtle text-success' : 'badge bg-warning-subtle text-warning';
        } catch (e) {
            console.error(e);
        }
    }

    function updateWeeklyProcessTypeTitle(type) {
        const t = {
            equal: 'Equal',
            outlet_anaerob: 'Outlet Anaerob',
            aerob: 'Aerob',
            daf: 'DAF',
            outlet: 'Outlet'
        } [type] || type;
        ['weeklyTssTrendTitle', 'weeklyCodTrendTitle', 'weeklyCombinedTitle'].forEach(id => {
            document.getElementById(id).textContent = t;
        });
    }

    async function updateWeeklyCharts() {
        await updateWeeklyTssChart();
        await updateWeeklyCodChart();
        await updateWeeklyCombinedChart();
    }

    async function updateWeeklyTssChart() {
        const s = document.getElementById('weeklyTssStartDate').value;
        const e = document.getElementById('weeklyTssEndDate').value;
        if (!s || !e) return;
        try {
            const data = await fetchJSON(`/api/wwtp-performance/dashboard/chart/${currentProcessType}?start_date=${s}&end_date=${e}`);
            const cats = data.map(d => fmtDateShort(d.tanggal || d.created_at));
            weeklyTssChart.updateOptions({
                xaxis: {
                    categories: cats
                }
            });
            weeklyTssChart.updateSeries([{
                name: 'TSS',
                data: data.map(d => parseFloat(d.tss) || 0)
            }]);
        } catch (e) {
            console.error(e);
        }
    }

    async function updateWeeklyCodChart() {
        const s = document.getElementById('weeklyCodStartDate').value;
        const e = document.getElementById('weeklyCodEndDate').value;
        if (!s || !e) return;
        try {
            const data = await fetchJSON(`/api/wwtp-performance/dashboard/chart/${currentProcessType}?start_date=${s}&end_date=${e}`);
            const cats = data.map(d => fmtDateShort(d.tanggal || d.created_at));
            weeklyCodChart.updateOptions({
                xaxis: {
                    categories: cats
                }
            });
            weeklyCodChart.updateSeries([{
                name: 'COD',
                data: data.map(d => parseFloat(d.cod) || 0)
            }]);
        } catch (e) {
            console.error(e);
        }
    }

    async function updateWeeklyCombinedChart() {
        const s = document.getElementById('weeklyCombinedStartDate').value;
        const e = document.getElementById('weeklyCombinedEndDate').value;
        if (!s || !e) return;
        try {
            const data = await fetchJSON(`/api/wwtp-performance/dashboard/chart/${currentProcessType}?start_date=${s}&end_date=${e}`);
            const cats = data.map(d => fmtDateShort(d.tanggal || d.created_at));
            weeklyCombinedChart.updateOptions({
                xaxis: {
                    categories: cats
                }
            });
            weeklyCombinedChart.updateSeries([{
                    name: 'TSS',
                    data: data.map(d => parseFloat(d.tss) || 0)
                },
                {
                    name: 'COD',
                    data: data.map(d => parseFloat(d.cod) || 0)
                }
            ]);
        } catch (e) {
            console.error(e);
        }
    }

    async function loadWeeklyMonthlyComparison() {
        try {
            const data = await fetchJSON('/api/wwtp-performance/dashboard/monthly-comparison');
            const cats = data.map(d => d.month);
            const types = ['equal', 'outlet_anaerob', 'aerob', 'daf', 'outlet'];
            const names = {
                equal: 'Equal',
                outlet_anaerob: 'Outlet Anaerob',
                aerob: 'Aerob',
                daf: 'DAF',
                outlet: 'Outlet'
            };
            const series = types.map(type => ({
                name: names[type],
                data: data.map(m => {
                    const td = m.data[type];
                    return td ? ((parseFloat(td.avg_tss) || 0) + (parseFloat(td.avg_cod) || 0)) / 2 : 0;
                })
            }));
            weeklyMonthlyComparisonChart.updateOptions({
                xaxis: {
                    categories: cats
                }
            });
            weeklyMonthlyComparisonChart.updateSeries(series);
        } catch (e) {
            console.error(e);
        }
    }

    // ==============================
    // DAILY
    // ==============================
    async function loadDailyData() {
        await loadDailyStatistics();
        await updateDailyPhChart();
        await updateDailyShiftBreakdownChart();
        await loadDailyMonthlyComparison();
    }

    async function loadDailyStatistics() {
        try {
            const data = await fetchJSON('/api/wwtp-performance/dashboard/statistics-harian');
            animateValue('dailyTotalShifts', 0, data.total_records || 0, 1000);
            animateValue('dailyTotalDays', 0, data.total_days || 0, 1000);
            animateValue('dailyWeekShifts', 0, data.total_shifts_this_week || 0, 1000);
            if (data.last_update) {
                document.getElementById('dailyLastUpdate').textContent =
                    new Date(data.last_update).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    }) +
                    (data.last_shift ? ` (${data.last_shift})` : '');
            }
            const todayStatus = document.getElementById('dailyTodayStatus');
            todayStatus.textContent = data.total_shifts_today === 0 ? 'Belum ada data hari ini' : `${data.total_shifts_today} shift hari ini`;
            todayStatus.className = data.total_shifts_today === 0 ? 'badge bg-warning-subtle text-warning' : 'badge bg-success-subtle text-success';
        } catch (e) {
            console.error(e);
        }
    }

    function updateDailyPhPointTitle(p) {
        const t = {
            equalisasi_1: 'Equalisasi 1',
            equalisasi_2: 'Equalisasi 2',
            netralisasi: 'Netralisasi',
            sedimentasi_1: 'Sedimentasi 1',
            sedimentasi_2: 'Sedimentasi 2',
            outlet_anaerob: 'Outlet Anaerob',
            aerob: 'Aerob',
            lumpur_aktif: 'Lumpur Aktif',
            clarifier_2: 'Clarifier 2',
            outlet: 'Outlet'
        } [p] || p;
        document.getElementById('dailyPhTrendTitle').textContent = t;
    }

    async function updateDailyPhChart() {
        const s = document.getElementById('dailyStartDate').value;
        const e = document.getElementById('dailyEndDate').value;
        if (!s || !e) return;
        try {
            const data = await fetchJSON(`/api/wwtp-performance/dashboard/chart-harian?start_date=${s}&end_date=${e}`);
            const cats = data.map(d => fmtDateShort(d.tanggal));
            dailyPhChart.updateOptions({
                xaxis: {
                    categories: cats
                }
            });
            dailyPhChart.updateSeries([{
                name: 'PH Level',
                data: data.map(d => parseFloat(d[currentPhPoint]) || 0)
            }]);
            await updateDailyShiftBreakdownChart();
        } catch (e) {
            console.error(e);
        }
    }

    async function updateDailyShiftBreakdownChart() {
        const s = document.getElementById('dailyStartDate').value;
        const e = document.getElementById('dailyEndDate').value;
        if (!s || !e) return;
        try {
            const data = await fetchJSON(`/api/wwtp-performance/dashboard/shift-breakdown?start_date=${s}&end_date=${e}`);
            const labelMap = {
                total_equalisasi_1: 'Equalisasi 1',
                total_equalisasi_2: 'Equalisasi 2',
                total_netralisasi: 'Netralisasi',
                total_sedimentasi_1: 'Sedimentasi 1',
                total_sedimentasi_2: 'Sedimentasi 2',
                total_outlet_anaerob: 'Outlet Anaerob',
                total_aerob: 'Aerob',
                total_lumpur_aktif: 'Lumpur Aktif',
                total_clarifier_2: 'Clarifier 2',
                total_outlet: 'Outlet'
            };
            dailyShiftPieChart.updateOptions({
                labels: Object.values(labelMap)
            });
            dailyShiftPieChart.updateSeries(
                Object.keys(labelMap).map(k => {
                    const val = parseFloat(data[k]) || 0;
                    return parseFloat(val.toFixed(2));
                })
            );
        } catch (e) {
            console.error(e);
        }
    }

    async function loadDailyMonthlyComparison() {
        try {
            const data = await fetchJSON('/api/wwtp-performance/dashboard/monthly-comparison-harian');
            const cats = data.map(d => d.month);
            const points = ['equalisasi_1', 'equalisasi_2', 'netralisasi', 'sedimentasi_1', 'sedimentasi_2',
                'outlet_anaerob', 'aerob', 'lumpur_aktif', 'clarifier_2', 'outlet'
            ];
            const names = {
                equalisasi_1: 'Equalisasi 1',
                equalisasi_2: 'Equalisasi 2',
                netralisasi: 'Netralisasi',
                sedimentasi_1: 'Sedimentasi 1',
                sedimentasi_2: 'Sedimentasi 2',
                outlet_anaerob: 'Outlet Anaerob',
                aerob: 'Aerob',
                lumpur_aktif: 'Lumpur Aktif',
                clarifier_2: 'Clarifier 2',
                outlet: 'Outlet'
            };
            const series = points.map(p => ({
                name: names[p],
                data: data.map(m => parseFloat(m.data[p]) || 0)
            }));
            dailyMonthlyComparisonChart.updateOptions({
                xaxis: {
                    categories: cats
                }
            });
            dailyMonthlyComparisonChart.updateSeries(series);
        } catch (e) {
            console.error(e);
        }
    }

    // ==============================
    // SAMPLE
    // ==============================
    async function loadSampleData() {
        await loadJenisSampelButtons();
        await updateSampleTrendChart();
        await updateSamplePieChart();
        await loadSampleMonthlyComparison();
    }



    async function loadJenisSampelButtons() {
        try {
            const res = await fetchJSON('/api/wwtp-performance/jenis-sampel');
            allJenisSampelData = res.data || [];
            const container = document.getElementById('sampleJenisButtons');

            let html = `
                <input type="radio" class="btn-check" name="sampleJenis" id="sj_all" value="" checked>
                <label class="btn btn-warning text-dark" for="sj_all">Semua</label>`;

            allJenisSampelData.forEach(j => {
                html += `
                    <input type="radio" class="btn-check" name="sampleJenis" id="sj_${j.id}" value="${j.id}">
                    <label class="btn btn-outline-warning" for="sj_${j.id}">${j.nama_sampel}</label>`;
            });

            container.innerHTML = html;

            // Bind event
            document.querySelectorAll('input[name="sampleJenis"]').forEach(r => {
                r.addEventListener('change', function() {
                    currentSampleJenis = this.value || null;
                    const selected = allJenisSampelData.find(j => String(j.id) === this.value);
                    document.getElementById('sampleJenisTitle').textContent = selected ? selected.nama_sampel : 'All';
                    updateSampleTrendChart();
                });
            });
        } catch (e) {
            console.error(e);
        }
    }

    async function updateSampleTrendChart() {
        const s = document.getElementById('sampleStartDate').value;
        const e = document.getElementById('sampleEndDate').value;
        if (!s || !e) return;
        try {
            const data = await fetchJSON(`/api/wwtp-performance/dashboard/sample/chart?start_date=${s}&end_date=${e}`);

            // Filter per jenis jika dipilih
            const cats = data.map(d => fmtDateShort(d.tanggal));
            const unit = paramUnits[currentSampleParam] || '';
            const lbl = paramLabels[currentSampleParam] || currentSampleParam;

            let series = [];
            if (currentSampleJenis) {
                // Satu jenis sampel saja
                const selected = allJenisSampelData.find(j => String(j.id) === String(currentSampleJenis));
                const seriesData = data.map(day => {
                    const found = (day.per_jenis || []).find(j => String(j.id_sampel) === String(currentSampleJenis));
                    return found ? parseFloat(found[currentSampleParam]) || 0 : 0;
                });
                series = [{
                    name: selected?.nama_sampel || 'Sampel',
                    data: seriesData
                }];
            } else {
                // Semua jenis sampel — tampilkan per jenis
                const jenisMap = {};
                data.forEach(day => {
                    (day.per_jenis || []).forEach(j => {
                        if (!jenisMap[j.id_sampel]) jenisMap[j.id_sampel] = {
                            name: j.jenis_sampel,
                            data: Array(data.length).fill(0)
                        };
                        const idx = data.indexOf(day);
                        jenisMap[j.id_sampel].data[idx] = parseFloat(j[currentSampleParam]) || 0;
                    });
                });
                series = Object.values(jenisMap);
            }

            sampleTrendChart.updateOptions({
                xaxis: {
                    categories: cats
                },
                yaxis: {
                    title: {
                        text: lbl
                    },
                    labels: {
                        formatter: v => v ? v.toFixed(2) + (unit ? ' ' + unit : '') : '0'
                    }
                },
                tooltip: {
                    y: {
                        formatter: v => v ? v.toFixed(2) + (unit ? ' ' + unit : '') : '0'
                    }
                }
            });
            sampleTrendChart.updateSeries(series);
        } catch (e) {
            console.error(e);
        }
    }

    async function updateSamplePieChart() {
        try {
            const data = await fetchJSON('/api/wwtp-performance/dashboard/sample/statistics');
            const summary = data.monthly_summary || [];
            const labels = summary.map(s => s.jenis_sampel);
            const values = summary.map(s => parseFloat(s[currentSampleParam]) || 0);

            samplePieChart.updateOptions({
                labels
            });
            samplePieChart.updateSeries(values);
        } catch (e) {
            console.error(e);
        }
    }

    async function loadSampleMonthlyComparison() {
        try {
            const data = await fetchJSON('/api/wwtp-performance/dashboard/sample/monthly-comparison');
            const cats = data.map(d => d.month);

            // Build series per jenis sampel dari semua bulan
            const jenisMap = {};
            data.forEach(m => {
                (m.per_jenis || []).forEach(j => {
                    if (!jenisMap[j.id_sampel]) jenisMap[j.id_sampel] = {
                        name: j.jenis_sampel,
                        data: Array(data.length).fill(0)
                    };
                    const idx = data.indexOf(m);
                    jenisMap[j.id_sampel].data[idx] = parseFloat(j[currentSampleParam]) || 0;
                });
            });
            const series = Object.values(jenisMap);

            sampleMonthlyComparisonChart.updateOptions({
                xaxis: {
                    categories: cats
                }
            });
            sampleMonthlyComparisonChart.updateSeries(series);
        } catch (e) {
            console.error(e);
        }
    }

    // ==============================
    // UTILITY
    // ==============================
    async function fetchJSON(url) {
        const res = await fetch(url);
        if (!res.ok) throw new Error(`HTTP ${res.status}: ${url}`);
        return res.json();
    }

    function fmtDateShort(dateStr) {
        return new Date(dateStr).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short'
        });
    }

    function animateValue(id, start, end, duration) {
        const obj = document.getElementById(id);
        if (!obj) return;
        start = parseInt(start) || 0;
        end = parseInt(end) || 0;
        if (start === end) {
            obj.textContent = end;
            return;
        }
        const startTime = Date.now(),
            endTime = startTime + duration;

        function update() {
            const progress = Math.min((Date.now() - startTime) / duration, 1);
            obj.textContent = Math.floor(start + (end - start) * progress);
            if (progress < 1) requestAnimationFrame(update);
            else obj.textContent = end;
        }
        requestAnimationFrame(update);
    }

    // ==============================
    // INIT CHARTS
    // ==============================
    function initCharts() {
        const lineOpts = {
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
                enabled: true,
                formatter: v => v ? v.toFixed(1) : '0',
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
                    opacity: .9,
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
                    formatter: v => v ? v.toFixed(2) : '0'
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

        const areaOpts = {
            ...lineOpts,
            chart: {
                ...lineOpts.chart,
                type: 'area'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: .4,
                    opacityTo: .1
                }
            }
        };

        const pieOpts = {
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
                formatter: (val, opts) => {
                    const v = opts.w.globals.series[opts.seriesIndex];
                    return (!v || v === 0) ? '0' : v.toFixed(1) + '\n(' + val.toFixed(1) + '%)';
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
                    opacity: .45
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
                                formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0).toFixed(2)
                            }
                        }
                    }
                }
            }
        };

        const barOpts = {
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
                enabled: true,
                formatter: v => (!v || v === 0) ? '' : v.toFixed(1),
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

        // Weekly
        weeklyTssChart = new ApexCharts(document.querySelector('#weeklyTssChart'), {
            ...lineOpts,
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
                    formatter: v => v ? v.toFixed(1) + ' mg/L' : '0 mg/L'
                }
            }
        });
        weeklyTssChart.render();

        weeklyCodChart = new ApexCharts(document.querySelector('#weeklyCodChart'), {
            ...lineOpts,
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
                    formatter: v => v ? v.toFixed(1) + ' mg/L' : '0 mg/L'
                }
            }
        });
        weeklyCodChart.render();

        weeklyCombinedChart = new ApexCharts(document.querySelector('#weeklyCombinedChart'), {
            ...areaOpts,
            series: [{
                name: 'TSS',
                data: []
            }, {
                name: 'COD',
                data: []
            }],
            colors: ['#f59e0b', '#10b981'],
            yaxis: {
                title: {
                    text: 'Concentration (mg/L)'
                },
                labels: {
                    formatter: v => v ? v.toFixed(1) + ' mg/L' : '0 mg/L'
                }
            }
        });
        weeklyCombinedChart.render();

        weeklyMonthlyComparisonChart = new ApexCharts(document.querySelector('#weeklyMonthlyComparisonChart'), {
            ...barOpts,
            series: [],
            colors: ['#3b82f6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'],
            yaxis: {
                title: {
                    text: 'Average Concentration (mg/L)'
                },
                labels: {
                    formatter: v => v ? v.toFixed(1) + ' mg/L' : '0 mg/L'
                }
            }
        });
        weeklyMonthlyComparisonChart.render();

        // Daily
        dailyPhChart = new ApexCharts(document.querySelector('#dailyPhChart'), {
            ...lineOpts,
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
                    formatter: v => v ? v.toFixed(2) : '0'
                }
            }
        });
        dailyPhChart.render();

        dailyShiftPieChart = new ApexCharts(document.querySelector('#dailyShiftPieChart'), {
            ...pieOpts,
            series: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            labels: ['Equalisasi 1', 'Equalisasi 2', 'Netralisasi', 'Sedimentasi 1', 'Sedimentasi 2',
                'Outlet Anaerob', 'Aerob', 'Lumpur Aktif', 'Clarifier 2', 'Outlet'
            ],
            colors: ['#3b82f6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#6366f1']
        });
        dailyShiftPieChart.render();

        dailyMonthlyComparisonChart = new ApexCharts(document.querySelector('#dailyMonthlyComparisonChart'), {
            ...barOpts,
            series: [],
            yaxis: {
                title: {
                    text: 'Average PH Level'
                },
                labels: {
                    formatter: v => v ? v.toFixed(2) : '0'
                }
            }
        });
        dailyMonthlyComparisonChart.render();

        // Sample Trend
        sampleTrendChart = new ApexCharts(document.querySelector('#sampleTrendChart'), {
            ...lineOpts,
            series: [],
            colors: ['#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'],
            yaxis: {
                title: {
                    text: 'Value'
                },
                labels: {
                    formatter: v => v ? v.toFixed(2) : '0'
                }
            }
        });
        sampleTrendChart.render();

        // Sample Pie
        samplePieChart = new ApexCharts(document.querySelector('#samplePieChart'), {
            ...pieOpts,
            series: [],
            labels: [],
            colors: ['#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316']
        });
        samplePieChart.render();

        // Sample Monthly
        sampleMonthlyComparisonChart = new ApexCharts(document.querySelector('#sampleMonthlyComparisonChart'), {
            ...barOpts,
            series: [],
            colors: ['#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'],
            yaxis: {
                title: {
                    text: 'Value'
                },
                labels: {
                    formatter: v => v ? v.toFixed(2) : '0'
                }
            }
        });
        sampleMonthlyComparisonChart.render();
    }
</script>
@endsection