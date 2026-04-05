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

        <!-- Charts Row - Weekly TSS & COD -->
        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">TSS Trend - <span id="weeklyTssTrendTitle">Equal</span></h4>
                        <div class="flex-shrink-0 d-flex gap-2">
                            <input type="date" class="form-control form-control-sm" id="weeklyTssStartDate" style="width:150px;">
                            <span class="align-self-center">to</span>
                            <input type="date" class="form-control form-control-sm" id="weeklyTssEndDate" style="width:150px;">
                            <button class="btn btn-sm btn-primary" onclick="updateWeeklyTssChart()"><i class="bx bx-search-alt"></i></button>
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
                        <div class="flex-shrink-0 d-flex gap-2">
                            <input type="date" class="form-control form-control-sm" id="weeklyCodStartDate" style="width:150px;">
                            <span class="align-self-center">to</span>
                            <input type="date" class="form-control form-control-sm" id="weeklyCodEndDate" style="width:150px;">
                            <button class="btn btn-sm btn-primary" onclick="updateWeeklyCodChart()"><i class="bx bx-search-alt"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="weeklyCodChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TSS & COD Combined -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">TSS & COD Comparison - <span id="weeklyCombinedTitle">Equal</span></h4>
                        <div class="flex-shrink-0 d-flex gap-2">
                            <input type="date" class="form-control form-control-sm" id="weeklyCombinedStartDate" style="width:150px;">
                            <span class="align-self-center">to</span>
                            <input type="date" class="form-control form-control-sm" id="weeklyCombinedEndDate" style="width:150px;">
                            <button class="btn btn-sm btn-primary" onclick="updateWeeklyCombinedChart()"><i class="bx bx-search-alt"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="weeklyCombinedChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6-Month Weekly Comparison -->
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

        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-info-subtle">
                    <div class="card-body">
                        <h4 class="card-title text-info mb-0">
                            <i class="bx bx-images"></i> Galeri Foto Performance
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex flex-wrap gap-2">
                        <h4 class="card-title mb-0 flex-grow-1">
                            Foto Proses &ndash; <span id="galleryProcessLabel">Equal</span>
                        </h4>
                        {{-- Filter: Process Type --}}
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="galleryJenis" id="gal_equal" value="equal" checked>
                                <label class="btn btn-outline-primary" for="gal_equal">Equal</label>

                                <input type="radio" class="btn-check" name="galleryJenis" id="gal_outlet_anaerob" value="outlet_anaerob">
                                <label class="btn btn-outline-info" for="gal_outlet_anaerob">Outlet Anaerob</label>

                                <input type="radio" class="btn-check" name="galleryJenis" id="gal_aerob" value="aerob">
                                <label class="btn btn-outline-success" for="gal_aerob">Aerob</label>

                                <input type="radio" class="btn-check" name="galleryJenis" id="gal_daf" value="daf">
                                <label class="btn btn-outline-warning" for="gal_daf">DAF</label>

                                <input type="radio" class="btn-check" name="galleryJenis" id="gal_outlet" value="outlet">
                                <label class="btn btn-outline-danger" for="gal_outlet">Outlet</label>
                            </div>
                            {{-- Filter: Date Range --}}
                            <input type="date" class="form-control form-control-sm" id="galleryStartDate" style="width:140px;">
                            <span class="text-muted small">s/d</span>
                            <input type="date" class="form-control form-control-sm" id="galleryEndDate" style="width:140px;">
                            <button class="btn btn-sm btn-primary" onclick="loadPhotoGallery()">
                                <i class="bx bx-search-alt"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Info count --}}
                        <div class="d-flex align-items-center justify-content-between mb-3" id="galleryInfo" style="display:none!important">
                            <small class="text-muted">
                                Menampilkan <strong id="galleryCount">0</strong> foto
                            </small>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-light border" id="galleryPrevBtn" onclick="galleryScroll(-1)">
                                    <i class="bx bx-chevron-left"></i>
                                </button>
                                <button class="btn btn-sm btn-light border" id="galleryNextBtn" onclick="galleryScroll(1)">
                                    <i class="bx bx-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Loading state --}}
                        <div id="galleryLoading" class="text-center py-5" style="display:none">
                            <div class="spinner-border text-info" role="status"></div>
                            <p class="mt-2 text-muted">Memuat foto...</p>
                        </div>

                        {{-- Empty state --}}
                        <div id="galleryEmpty" class="text-center py-5">
                            <i class="bx bx-image-alt fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Tidak ada foto tersedia untuk filter ini.</p>
                        </div>

                        {{-- Scrollable photo strip --}}
                        <div id="galleryWrapper" class="position-relative" style="display:none">
                            <div id="galleryStrip" class="d-flex gap-3 overflow-auto pb-2" style="scroll-behavior:smooth; scrollbar-width:thin;">
                                {{-- Cards injected via JS --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== LIGHTBOX MODAL ===== --}}
        <div class="modal fade" id="photoLightboxModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-dark text-white border-0">
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <h5 class="modal-title mb-0" id="lightboxTitle">Foto Proses</h5>
                            <small class="text-muted" id="lightboxWeek"></small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center pt-2">
                        <img id="lightboxImg" src="" alt="Foto Proses" class="img-fluid rounded" style="max-height:65vh; object-fit:contain;">
                        <div class="d-flex justify-content-center gap-4 mt-3">
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="bx bx-test-tube me-1"></i> TSS: <strong id="lightboxTSS">-</strong> mg/L
                            </span>
                            <span class="badge bg-success fs-6">
                                <i class="bx bx-droplet me-1"></i> COD: <strong id="lightboxCOD">-</strong> mg/L
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 justify-content-between">
                        <button class="btn btn-outline-light btn-sm" id="lightboxPrev" onclick="lightboxNav(-1)">
                            <i class="bx bx-chevron-left"></i> Sebelumnya
                        </button>
                        <small class="text-muted" id="lightboxCounter"></small>
                        <button class="btn btn-outline-light btn-sm" id="lightboxNext" onclick="lightboxNav(1)">
                            Berikutnya <i class="bx bx-chevron-right"></i>
                        </button>
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

        <!-- PH Trend + Donut Distribution -->
        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">PH Trend - <span id="dailyPhTrendTitle">Equalisasi 1</span></h4>
                        <div class="flex-shrink-0 d-flex gap-2">
                            <input type="date" class="form-control form-control-sm" id="dailyStartDate" style="width:150px;">
                            <span class="align-self-center">to</span>
                            <input type="date" class="form-control form-control-sm" id="dailyEndDate" style="width:150px;">
                            <button class="btn btn-sm btn-primary" onclick="updateDailyPhChart()"><i class="bx bx-search-alt"></i></button>
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
                        <div id="dailyShiftPieFilter" class="mb-2 d-flex flex-wrap gap-1"></div>
                        <div id="dailyShiftPieChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6-Month Daily Comparison -->
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

        <!-- Select Jenis Sampel -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4 class="card-title mb-0 flex-grow-1">Select Jenis Sampel</h4>

                        <!-- PINDAHAN FILTER -->
                        <div class="flex-shrink-0 d-flex gap-2">
                            <input type="date" class="form-control form-control-sm" id="sampleStartDate" style="width:150px;">
                            <span class="align-self-center">to</span>
                            <input type="date" class="form-control form-control-sm" id="sampleEndDate" style="width:150px;">
                            <button class="btn btn-sm btn-warning text-dark" onclick="updateAllSampleCharts()">
                                <i class="bx bx-search-alt"></i>
                            </button>
                        </div>
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

        <!-- 6 Parameter Line Charts -->
        <div class="row">

            <!-- ROW 1 -->
            <div class="col-xl-6 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            Trend TSS (mg/L) - <span id="sampleJenisTitleTss">All</span>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div id="sampleTssTrendChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            Trend SV30 (mL/L) - <span id="sampleJenisTitleSv30">All</span>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div id="sampleSv30TrendChart"></div>
                    </div>
                </div>
            </div>

            <!-- ROW 2 -->
            <div class="col-xl-6 col-md-6 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            Trend pH - <span id="sampleJenisTitlePh">All</span>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div id="samplePhTrendChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            Trend MLSS (mg/L) - <span id="sampleJenisTitleMlss">All</span>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div id="sampleMlssTrendChart"></div>
                    </div>
                </div>
            </div>

            <!-- ROW 3 -->
            <div class="col-xl-6 col-md-6 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            Trend SVI (mL/g) - <span id="sampleJenisTitleSvl">All</span>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div id="sampleSvlTrendChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            Trend DO (mg/L) - <span id="sampleJenisTitleDo">All</span>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div id="sampleDoTrendChart"></div>
                    </div>
                </div>
            </div>

        </div>
        <!-- 6-Month Sample Comparison -->
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
    #galleryStrip::-webkit-scrollbar {
        height: 5px;
    }

    #galleryStrip::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #galleryStrip::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .gallery-card {
        min-width: 200px;
        max-width: 200px;
        flex-shrink: 0;
        cursor: pointer;
        transition: transform .18s ease, box-shadow .18s ease;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, .08);
    }

    .gallery-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, .13);
    }

    .gallery-card .gallery-img-wrap {
        height: 140px;
        overflow: hidden;
        background: #f3f4f6;
        position: relative;
    }

    .gallery-card .gallery-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .25s ease;
    }

    .gallery-card:hover .gallery-img-wrap img {
        transform: scale(1.06);
    }

    .gallery-card .gallery-body {
        padding: 10px 12px 12px;
    }

    .gallery-card .gallery-week {
        font-size: 11px;
        color: #6c757d;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .gallery-card .gallery-badges {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .gallery-card .gallery-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 20px;
    }

    .gallery-no-photo {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 140px;
        background: #f3f4f6;
        color: #adb5bd;
        font-size: 2rem;
    }

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
    // ── Gallery State (module-pattern tanpa IIFE agar fungsi global) ──────────────
    var _gallery = {
        data: [],
        lightboxIndex: 0,
        modalInst: null,
        labels: {
            equal: 'Equal',
            outlet_anaerob: 'Outlet Anaerob',
            aerob: 'Aerob',
            daf: 'DAF',
            outlet: 'Outlet',
        }
    };

    // ── Definisikan dulu semua fungsi sebelum dipanggil ──────────────────────────

    function galleryRenderStrip(items) {
        var strip = document.getElementById('galleryStrip');
        strip.innerHTML = '';
        items.forEach(function(item, idx) {
            var card = document.createElement('div');
            card.className = 'gallery-card shadow-sm';
            card.setAttribute('onclick', 'galleryOpenLightbox(' + idx + ')');

            var imgHtml = item.foto_url ?
                '<img src="' + item.foto_url + '" alt="Foto ' + item.jenis + '" loading="lazy">' :
                '<div class="gallery-no-photo"><i class="bx bx-image-alt"></i></div>';

            card.innerHTML =
                '<div class="gallery-img-wrap">' + imgHtml + '</div>' +
                '<div class="gallery-body">' +
                '<div class="gallery-week"><i class="bx bx-calendar me-1"></i>' + (item.week_label || '-') + '</div>' +
                '<div class="gallery-badges">' +
                '<span class="gallery-badge bg-warning-subtle text-warning">TSS ' + (item.tss !== null ? item.tss : '-') + '</span>' +
                '<span class="gallery-badge bg-success-subtle text-success">COD ' + (item.cod !== null ? item.cod : '-') + '</span>' +
                '</div>' +
                '</div>';

            strip.appendChild(card);
        });
    }

    function galleryUpdateLightbox() {
        var item = _gallery.data[_gallery.lightboxIndex];
        if (!item) return;
        document.getElementById('lightboxImg').src = item.foto_url || '';
        document.getElementById('lightboxTitle').textContent = 'Foto \u2013 ' + (_gallery.labels[item.jenis] || item.jenis);
        document.getElementById('lightboxWeek').textContent = item.week_label || '-';
        document.getElementById('lightboxTSS').textContent = item.tss !== null ? item.tss : '-';
        document.getElementById('lightboxCOD').textContent = item.cod !== null ? item.cod : '-';
        document.getElementById('lightboxCounter').textContent = (_gallery.lightboxIndex + 1) + ' / ' + _gallery.data.length;
        document.getElementById('lightboxPrev').disabled = _gallery.lightboxIndex === 0;
        document.getElementById('lightboxNext').disabled = _gallery.lightboxIndex === _gallery.data.length - 1;
    }

    function galleryOpenLightbox(idx) {
        _gallery.lightboxIndex = idx;
        galleryUpdateLightbox();
        if (!_gallery.modalInst) {
            _gallery.modalInst = new bootstrap.Modal(document.getElementById('photoLightboxModal'));
        }
        _gallery.modalInst.show();
    }

    function galleryScroll(dir) {
        document.getElementById('galleryStrip').scrollLeft += dir * 650;
    }

    function lightboxNav(dir) {
        var next = _gallery.lightboxIndex + dir;
        if (next < 0 || next >= _gallery.data.length) return;
        _gallery.lightboxIndex = next;
        galleryUpdateLightbox();
    }

    function loadPhotoGallery() {
        var jenis = (document.querySelector('input[name="galleryJenis"]:checked') || {}).value || 'equal';
        var start = document.getElementById('galleryStartDate').value;
        var end = document.getElementById('galleryEndDate').value;

        document.getElementById('galleryProcessLabel').textContent = _gallery.labels[jenis] || jenis;
        document.getElementById('galleryLoading').style.display = 'block';
        document.getElementById('galleryEmpty').style.display = 'none';
        document.getElementById('galleryWrapper').style.display = 'none';
        document.getElementById('galleryInfo').style.display = 'none';

        var url = '{{ route("wwtp.performance.photo-gallery") }}?jenis=' + jenis + '&start_date=' + start + '&end_date=' + end;

        fetch(url)
            .then(function(r) {
                return r.json();
            })
            .then(function(res) {
                document.getElementById('galleryLoading').style.display = 'none';
                _gallery.data = (res.data || []).filter(function(d) {
                    return d.foto_url;
                });

                if (_gallery.data.length === 0) {
                    document.getElementById('galleryEmpty').style.display = 'block';
                    return;
                }
                galleryRenderStrip(_gallery.data);
                document.getElementById('galleryCount').textContent = _gallery.data.length;
                document.getElementById('galleryInfo').style.display = 'flex';
                document.getElementById('galleryWrapper').style.display = 'block';
            })
            .catch(function() {
                document.getElementById('galleryLoading').style.display = 'none';
                document.getElementById('galleryEmpty').style.display = 'block';
            });
    }

    // ── Inisialisasi setelah DOM siap ─────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        // Set default date range: awal – akhir bulan ini
        var now = new Date();
        var y = now.getFullYear();
        var m = String(now.getMonth() + 1).padStart(2, '0');
        var first = y + '-' + m + '-01';
        var last = new Date(y, now.getMonth() + 1, 0).toISOString().split('T')[0];
        document.getElementById('galleryStartDate').value = first;
        document.getElementById('galleryEndDate').value = last;

        // Radio change listener
        document.querySelectorAll('input[name="galleryJenis"]').forEach(function(radio) {
            radio.addEventListener('change', loadPhotoGallery);
        });

        // Keyboard navigation di dalam lightbox
        document.addEventListener('keydown', function(e) {
            var modal = document.getElementById('photoLightboxModal');
            if (!modal || !modal.classList.contains('show')) return;
            if (e.key === 'ArrowLeft') lightboxNav(-1);
            if (e.key === 'ArrowRight') lightboxNav(1);
        });

        // Load pertama kali
        loadPhotoGallery();
    });
</script>
<script>
    // ==============================
    // CHART INSTANCES
    // ==============================
    let weeklyTssChart, weeklyCodChart, weeklyCombinedChart, weeklyMonthlyComparisonChart;
    let dailyPhChart, dailyShiftPieChart, dailyMonthlyComparisonChart;
    let sampleMonthlyComparisonChart;
    const sampleChartInstances = {}; // keyed by param key e.g. 'avg_tss'

    // ==============================
    // STATE
    // ==============================
    let currentProcessType = 'equal';
    let currentPhPoint = 'equalisasi_1';
    let currentSampleJenis = null;
    let allJenisSampelData = [];

    // ==============================
    // SAMPLE PARAMETER CONFIG
    // ==============================
    const sampleParams = [{
            key: 'avg_tss',
            label: 'TSS',
            unit: 'mg/L',
            color: '#f59e0b',
            trendId: 'sampleTssTrendChart',
            titleId: 'sampleJenisTitleTss'
        },
        {
            key: 'avg_sv30',
            label: 'SV30',
            unit: 'mL/L',
            color: '#10b981',
            trendId: 'sampleSv30TrendChart',
            titleId: 'sampleJenisTitleSv30'
        },
        {
            key: 'avg_ph',
            label: 'pH',
            unit: '',
            color: '#3b82f6',
            trendId: 'samplePhTrendChart',
            titleId: 'sampleJenisTitlePh'
        },
        {
            key: 'avg_mlss',
            label: 'MLSS',
            unit: 'mg/L',
            color: '#ef4444',
            trendId: 'sampleMlssTrendChart',
            titleId: 'sampleJenisTitleMlss'
        },
        {
            key: 'avg_svl',
            label: 'SVI',
            unit: 'mL/g',
            color: '#8b5cf6',
            trendId: 'sampleSvlTrendChart',
            titleId: 'sampleJenisTitleSvl'
        },
        {
            key: 'avg_do',
            label: 'DO',
            unit: 'mg/L',
            color: '#14b8a6',
            trendId: 'sampleDoTrendChart',
            titleId: 'sampleJenisTitleDo'
        },
    ];

    // ==============================
    // PIE CHART FILTER STATE
    // ==============================
    const pieChartFullData = {
        dailyShift: {
            labels: [],
            series: [],
            colors: []
        },
    };

    // ==============================
    // INIT
    // ==============================
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
    });

    // ==============================
    // DATE HELPERS
    // ==============================
    function setDefaultDates() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        const fmt = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;

        [
            'weeklyTssStartDate', 'weeklyCodStartDate', 'weeklyCombinedStartDate',
            'dailyStartDate', 'sampleStartDate'
        ].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = fmt(firstDay);
        });

        [
            'weeklyTssEndDate', 'weeklyCodEndDate', 'weeklyCombinedEndDate',
            'dailyEndDate', 'sampleEndDate'
        ].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = fmt(lastDay);
        });
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
        const startTime = Date.now();

        function update() {
            const progress = Math.min((Date.now() - startTime) / duration, 1);
            obj.textContent = Math.floor(start + (end - start) * progress);
            if (progress < 1) requestAnimationFrame(update);
            else obj.textContent = end;
        }
        requestAnimationFrame(update);
    }

    // ==============================
    // PIE FILTER HELPERS
    // ==============================
    function storePieData(dataKey, labels, series, colors) {
        pieChartFullData[dataKey] = {
            labels,
            series,
            colors
        };
    }

    function buildPieFilter(containerId, labels, colors, callbackName) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';
        labels.forEach((label, i) => {
            const color = colors[i % colors.length];
            const checkId = `${containerId}_cb_${i}`;
            const wrapper = document.createElement('div');
            wrapper.className = 'd-inline-flex align-items-center me-2 mb-1';
            wrapper.innerHTML = `
            <input type="checkbox" id="${checkId}" checked
                class="form-check-input me-1"
                style="cursor:pointer; accent-color:${color}; width:14px; height:14px;"
                onchange="${callbackName}()">
            <label for="${checkId}" class="form-check-label small mb-0"
                style="cursor:pointer; color:${color}; font-weight:600;">${label}</label>`;
            container.appendChild(wrapper);
        });
    }

    function applyPieFilter(chartInstance, dataKey) {
        const idMap = {
            dailyShift: 'dailyShiftPieFilter',
        };
        const containerId = idMap[dataKey];
        const {
            labels,
            series,
            colors
        } = pieChartFullData[dataKey];
        if (!labels.length) return;

        const checkboxes = document.querySelectorAll(`#${containerId} input[type=checkbox]`);
        const selected = Array.from(checkboxes).map((cb, i) => cb.checked ? i : -1).filter(i => i !== -1);

        if (!selected.length) {
            chartInstance.updateOptions({
                labels: ['No Data'],
                colors: ['#ccc']
            });
            chartInstance.updateSeries([0]);
            return;
        }
        chartInstance.updateOptions({
            labels: selected.map(i => labels[i]),
            colors: selected.map(i => colors[i % colors.length])
        });
        chartInstance.updateSeries(selected.map(i => series[i]));
    }

    function filterDailyShiftPie() {
        applyPieFilter(dailyShiftPieChart, 'dailyShift');
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
            weeklyTssChart.updateOptions({
                xaxis: {
                    categories: data.map(d => fmtDateShort(d.tanggal || d.created_at))
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
            weeklyCodChart.updateOptions({
                xaxis: {
                    categories: data.map(d => fmtDateShort(d.tanggal || d.created_at))
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
            weeklyCombinedChart.updateOptions({
                xaxis: {
                    categories: data.map(d => fmtDateShort(d.tanggal || d.created_at))
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
                    categories: data.map(d => d.month)
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
            const data = await fetchJSON('/api/wwtp-performance/dashboard/statistics');
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
            const el = document.getElementById('dailyTodayStatus');
            el.textContent = data.total_shifts_today === 0 ? 'Belum ada data hari ini' : `${data.total_shifts_today} shift hari ini`;
            el.className = data.total_shifts_today === 0 ? 'badge bg-warning-subtle text-warning' : 'badge bg-success-subtle text-success';
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
            dailyPhChart.updateOptions({
                xaxis: {
                    categories: data.map(d => fmtDateShort(d.tanggal))
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
            const colors = ['#3b82f6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#6366f1'];
            const dailyLabels = Object.values(labelMap);
            const dailySeries = Object.keys(labelMap).map(k => parseFloat((parseFloat(data[k]) || 0).toFixed(2)));

            storePieData('dailyShift', dailyLabels, dailySeries, colors);
            buildPieFilter('dailyShiftPieFilter', dailyLabels, colors, 'filterDailyShiftPie');
            dailyShiftPieChart.updateOptions({
                labels: dailyLabels,
                colors
            });
            dailyShiftPieChart.updateSeries(dailySeries);
        } catch (e) {
            console.error(e);
        }
    }

    async function loadDailyMonthlyComparison() {
        try {
            const data = await fetchJSON('/api/wwtp-performance/dashboard/monthly-comparison-harian');
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
                    categories: data.map(d => d.month)
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
        await updateAllSampleCharts();
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

            document.querySelectorAll('input[name="sampleJenis"]').forEach(r => {
                r.addEventListener('change', function() {
                    currentSampleJenis = this.value || null;
                    updateAllSampleCharts();
                });
            });
        } catch (e) {
            console.error(e);
        }
    }

    async function updateAllSampleCharts() {
        const s = document.getElementById('sampleStartDate').value;
        const e = document.getElementById('sampleEndDate').value;
        if (!s || !e) return;

        let trendData = [];
        try {
            trendData = await fetchJSON(`/api/wwtp-performance/dashboard/sample/chart?start_date=${s}&end_date=${e}`);
        } catch (err) {
            console.error(err);
            return;
        }

        const cats = trendData.map(d => fmtDateShort(d.tanggal));
        const multiColors = ['#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];

        sampleParams.forEach(p => {
            const chartInst = sampleChartInstances[p.key];
            if (!chartInst) return;

            // Build series
            let series = [];
            if (currentSampleJenis) {
                const selected = allJenisSampelData.find(j => String(j.id) === String(currentSampleJenis));
                series = [{
                    name: selected?.nama_sampel || 'Sampel',
                    data: trendData.map(day => {
                        const found = (day.per_jenis || []).find(j => String(j.id_sampel) === String(currentSampleJenis));
                        return found ? parseFloat(found[p.key]) || 0 : 0;
                    })
                }];
            } else {
                const jenisMap = {};
                trendData.forEach(day => {
                    (day.per_jenis || []).forEach(j => {
                        if (!jenisMap[j.id_sampel]) jenisMap[j.id_sampel] = {
                            name: j.jenis_sampel,
                            data: Array(trendData.length).fill(0)
                        };
                        jenisMap[j.id_sampel].data[trendData.indexOf(day)] = parseFloat(j[p.key]) || 0;
                    });
                });
                series = Object.values(jenisMap);
            }

            chartInst.updateOptions({
                colors: currentSampleJenis ? [p.color] : multiColors,
                xaxis: {
                    categories: cats
                },
                yaxis: {
                    title: {
                        text: p.unit ? `${p.label} (${p.unit})` : p.label
                    },
                    labels: {
                        formatter: v => v ? v.toFixed(2) + (p.unit ? ' ' + p.unit : '') : '0'
                    }
                },
                tooltip: {
                    y: {
                        formatter: v => v ? v.toFixed(2) + (p.unit ? ' ' + p.unit : '') : '0'
                    }
                }
            });
            chartInst.updateSeries(series);

            // Update title
            const titleEl = document.getElementById(p.titleId);
            if (titleEl) {
                const sel = allJenisSampelData.find(j => String(j.id) === String(currentSampleJenis));
                titleEl.textContent = sel ? sel.nama_sampel : 'All';
            }
        });
    }

    async function loadSampleMonthlyComparison() {
        try {
            const data = await fetchJSON('/api/wwtp-performance/dashboard/sample/monthly-comparison');
            const jenisMap = {};
            data.forEach(m => {
                (m.per_jenis || []).forEach(j => {
                    if (!jenisMap[j.id_sampel]) jenisMap[j.id_sampel] = {
                        name: j.jenis_sampel,
                        data: Array(data.length).fill(0)
                    };
                    jenisMap[j.id_sampel].data[data.indexOf(m)] = parseFloat(j['avg_tss']) || 0;
                });
            });
            sampleMonthlyComparisonChart.updateOptions({
                xaxis: {
                    categories: data.map(d => d.month)
                }
            });
            sampleMonthlyComparisonChart.updateSeries(Object.values(jenisMap));
        } catch (e) {
            console.error(e);
        }
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
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
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
                show: false
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
                background: {
                    enabled: true,
                    foreColor: '#fff',
                    borderRadius: 4,
                    padding: 4,
                    opacity: .9,
                    borderWidth: 0,
                    borderColor: 'transparent'
                },
                dropShadow: {
                    enabled: true,
                    top: 1,
                    left: 1,
                    blur: 2,
                    opacity: .6
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

        // --- WEEKLY ---
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

        // --- DAILY ---
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

        // --- SAMPLE: 6 LINE CHARTS ---
        const multiColors = ['#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];

        sampleParams.forEach(p => {
            const chart = new ApexCharts(document.querySelector(`#${p.trendId}`), {
                ...lineOpts,
                series: [],
                colors: multiColors,
                yaxis: {
                    title: {
                        text: p.unit ? `${p.label} (${p.unit})` : p.label
                    },
                    labels: {
                        formatter: v => v ? v.toFixed(2) + (p.unit ? ' ' + p.unit : '') : '0'
                    }
                },
                tooltip: {
                    y: {
                        formatter: v => v ? v.toFixed(2) + (p.unit ? ' ' + p.unit : '') : '0'
                    }
                }
            });
            chart.render();
            sampleChartInstances[p.key] = chart;
        });

        // --- SAMPLE MONTHLY COMPARISON ---
        sampleMonthlyComparisonChart = new ApexCharts(document.querySelector('#sampleMonthlyComparisonChart'), {
            ...barOpts,
            series: [],
            colors: multiColors,
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