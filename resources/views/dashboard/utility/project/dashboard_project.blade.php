@extends('layouts.app')

@section('title', 'Dashboard Project Engineering Utility')

@section('styles')
    <style>
        /* Fix Sticky Header in Dark Theme & Light Theme */
        .table-sticky-header th {
            position: sticky;
            top: 0;
            z-index: 3;
            background-color: #f3f6f9 !important;
            color: #495057 !important;
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.08);
        }

        [data-layout-mode="dark"] .table-sticky-header th,
        [data-bs-theme="dark"] .table-sticky-header th {
            background-color: #262a2f !important;
            color: #ced4da !important;
            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, 0.1);
        }

        /* Apexcharts data labels high contrast */
        .apexcharts-datalabel,
        .apexcharts-datalabel text,
        .apexcharts-datalabels text {
            fill: #ffffff !important;
            font-weight: 700 !important;
        }

        /* Apexcharts tooltip text color */
        .apexcharts-tooltip,
        .apexcharts-tooltip * {
            color: #ffffff !important;
        }

        .apexcharts-tooltip span,
        .apexcharts-tooltip div,
        .apexcharts-tooltip-text,
        .apexcharts-tooltip-text-y-label,
        .apexcharts-tooltip-text-y-value,
        .apexcharts-tooltip-title {
            color: #ffffff !important;
            font-weight: 700 !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.45);
        }

        .apexcharts-tooltip.apexcharts-theme-light,
        .apexcharts-tooltip.apexcharts-theme-dark {
            border: none !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
            border-radius: 6px !important;
        }

        /* Multi-select Dropdown Custom Styles */
        .custom-filter-dropdown .filter-btn {
            height: 38px;
            font-size: 13px;
            border-color: #ced4da;
            transition: all 0.2s ease;
        }

        .custom-filter-dropdown .filter-btn:hover,
        .custom-filter-dropdown .filter-btn:focus {
            background-color: #f8f9fa;
            border-color: #b1b8be;
        }

        .custom-filter-dropdown .filter-btn.is-active {
            border-color: #0d6efd;
            background-color: #f0f7ff;
            color: #0d6efd !important;
            font-weight: 600;
        }

        .custom-filter-dropdown .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            z-index: 1050;
        }

        .custom-filter-dropdown .dropdown-item {
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .custom-filter-dropdown .option-item {
            display: flex !important;
            align-items: center;
        }

        .custom-filter-dropdown .option-item.d-none {
            display: none !important;
        }

        .custom-filter-dropdown .dropdown-item:hover {
            background-color: #f1f5f9;
        }

        .custom-filter-dropdown .dropdown-item:active {
            background-color: #e2e8f0;
            color: inherit;
        }

        .cursor-pointer {
            cursor: pointer !important;
        }

        .filter-options-list::-webkit-scrollbar {
            width: 5px;
        }

        .filter-options-list::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }

        [data-layout-mode="dark"] .custom-filter-dropdown .filter-btn,
        [data-bs-theme="dark"] .custom-filter-dropdown .filter-btn {
            background-color: #212529 !important;
            border-color: #32383e !important;
            color: #ced4da !important;
        }

        [data-layout-mode="dark"] .custom-filter-dropdown .filter-btn:hover,
        [data-bs-theme="dark"] .custom-filter-dropdown .filter-btn:hover {
            background-color: #2a3036 !important;
        }

        [data-layout-mode="dark"] .custom-filter-dropdown .filter-btn.is-active,
        [data-bs-theme="dark"] .custom-filter-dropdown .filter-btn.is-active {
            border-color: #0d6efd !important;
            background-color: rgba(13, 110, 253, 0.15) !important;
            color: #3b82f6 !important;
        }

        [data-layout-mode="dark"] .custom-filter-dropdown .dropdown-menu,
        [data-bs-theme="dark"] .custom-filter-dropdown .dropdown-menu {
            background-color: #212529 !important;
            border: 1px solid #32383e !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
        }

        [data-layout-mode="dark"] .custom-filter-dropdown .dropdown-item:hover,
        [data-bs-theme="dark"] .custom-filter-dropdown .dropdown-item:hover {
            background-color: #2a3036 !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- ── Header Banner ── --}}
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 12px;">
                        <div class="card-body p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <h4 class="text-white fw-bold mb-1">
                                    <i class="ri-dashboard-2-line text-warning me-2"></i>
                                    Dashboard Project Engineering Utility
                                </h4>
                                <p class="text-white-50 mb-0 fs-13">
                                    Monitoring visual performa dan status seluruh project utility (Boiler, Utility, WWTP,
                                    MTC)
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <button type="button" id="btnToggleFilterPanel"
                                    class="btn btn-outline-light btn-sm fw-semibold shadow-sm" title="Tampilkan / Sembunyikan Filter">
                                    <i class="ri-filter-3-line me-1"></i> <span id="textToggleFilter">Filter Data</span>
                                </button>
                                <button type="button" id="btnToggleAlertTerlambat"
                                    class="btn btn-danger btn-sm fw-semibold shadow-sm" title="Tampilkan / Sembunyikan Alert Project Terlambat"
                                    style="display: {{ ($kpis['terlambat'] ?? 0) > 0 ? 'inline-flex' : 'none' }}; align-items: center;">
                                    <i class="ri-error-warning-fill me-1"></i> <span id="headerTerlambatCount">{{ $kpis['terlambat'] ?? 0 }}</span> Terlambat
                                </button>
                                <button type="button" id="btnRefreshDashboard"
                                    class="btn btn-outline-light btn-sm fw-semibold shadow-sm">
                                    <i class="ri-refresh-line me-1"></i> Refresh Data
                                </button>
                                <a href="{{ route('utility.project.data') }}"
                                    class="btn btn-light btn-sm fw-semibold shadow-sm">
                                    <i class="ri-table-line me-1 text-primary"></i> Data Project & Tracking
                                </a>
                                <a href="{{ route('utility.project.form') }}"
                                    class="btn btn-warning btn-sm fw-semibold shadow-sm">
                                    <i class="ri-add-line me-1"></i> Buat Project Baru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Alert Terlambat (Default dihilangkan / disembunyikan dulu) ── --}}
            <div id="alertTerlambatBanner"
                class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center mb-3"
                role="alert" style="display: none;">
                <i class="ri-error-warning-fill fs-20 me-2 text-danger"></i>
                <div class="flex-grow-1">
                    <strong>Perhatian:</strong> Terdapat <strong id="alertTerlambatCount">{{ $kpis['terlambat'] ?? 0 }}
                        project</strong> yang berstatus terlambat (belum selesai melewati batas deadline).
                </div>
                <a href="{{ route('utility.project.data', ['status' => 'Terlambat']) }}" class="btn btn-sm btn-danger ms-2 text-nowrap">
                    Lihat Project Terlambat <i class="ri-arrow-right-s-line"></i>
                </a>
                <button type="button" class="btn-close ms-2" id="btnCloseAlertTerlambat" aria-label="Close" title="Tutup Alert"></button>
            </div>

            {{-- ── Filter Dashboard Panel (Default dihilangkan / disembunyikan dulu) ── --}}
            <div class="card border-0 shadow-sm mb-3" id="dashFilterCard" style="display: none;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                        <span class="fs-12 fw-bold text-uppercase text-muted">
                            <i class="ri-filter-3-line text-primary me-1"></i>Filter Dashboard Project
                        </span>
                        <button type="button" class="btn-close fs-11" id="btnHideFilterCard" aria-label="Close" title="Sembunyikan Panel Filter"></button>
                    </div>
                    <form id="dashFilterForm" class="row g-2 align-items-center">

                        {{-- Tahun Filter (Dropdown Multi-Select) --}}
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="dropdown custom-filter-dropdown" data-filter="year">
                                <button
                                    class="btn btn-outline-light text-dark bg-white border w-100 d-flex justify-content-between align-items-center text-truncate dropdown-toggle shadow-none py-2 px-2 filter-btn"
                                    type="button" id="dropdownYearBtn" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" aria-expanded="false">
                                    <span class="filter-btn-text text-truncate me-1">
                                        <i class="ri-calendar-line text-muted me-1"></i><span class="label-text">Semua Tahun</span>
                                    </span>
                                    <span class="badge bg-primary rounded-pill filter-count-badge d-none">0</span>
                                </button>
                                <div class="dropdown-menu p-2 shadow-lg border-0 rounded-3" style="min-width: 200px;" aria-labelledby="dropdownYearBtn">
                                    <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                        <small class="fw-bold text-muted text-uppercase fs-11">Filter Tahun</small>
                                        <div class="d-flex gap-2">
                                            <a href="javascript:void(0)" class="fs-11 text-primary text-decoration-none fw-medium btn-select-all" data-target="year">Pilih Semua</a>
                                            <span class="text-muted fs-11">|</span>
                                            <a href="javascript:void(0)" class="fs-11 text-danger text-decoration-none fw-medium btn-clear-filter" data-target="year">Reset</a>
                                        </div>
                                    </div>
                                    <div class="filter-options-list">
                                        @foreach ($yearsList as $yr)
                                            <label class="dropdown-item px-2 py-1 rounded-2 d-flex align-items-center gap-2 cursor-pointer mb-0 option-item">
                                                <input class="form-check-input mt-0 filter-checkbox filter-checkbox-year" type="checkbox" value="{{ $yr }}" {{ in_array($yr, $yearList ?? []) ? 'checked' : '' }}>
                                                <span class="fs-12 text-dark">Tahun {{ $yr }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Fase Filter (Dropdown Multi-Select) --}}
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="dropdown custom-filter-dropdown" data-filter="fase">
                                <button
                                    class="btn btn-outline-light text-dark bg-white border w-100 d-flex justify-content-between align-items-center text-truncate dropdown-toggle shadow-none py-2 px-2 filter-btn"
                                    type="button" id="dropdownFaseBtn" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" aria-expanded="false">
                                    <span class="filter-btn-text text-truncate me-1">
                                        <i class="ri-git-commit-line text-muted me-1"></i><span class="label-text">Semua Fase</span>
                                    </span>
                                    <span class="badge bg-primary rounded-pill filter-count-badge d-none">0</span>
                                </button>
                                <div class="dropdown-menu p-2 shadow-lg border-0 rounded-3" style="min-width: 250px;" aria-labelledby="dropdownFaseBtn">
                                    <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                        <small class="fw-bold text-muted text-uppercase fs-11">Filter Fase</small>
                                        <div class="d-flex gap-2">
                                            <a href="javascript:void(0)" class="fs-11 text-primary text-decoration-none fw-medium btn-select-all" data-target="fase">Pilih Semua</a>
                                            <span class="text-muted fs-11">|</span>
                                            <a href="javascript:void(0)" class="fs-11 text-danger text-decoration-none fw-medium btn-clear-filter" data-target="fase">Reset</a>
                                        </div>
                                    </div>
                                    <div class="filter-options-list">
                                        @php
                                            $faseListConfig = [
                                                'Fase 1' => [
                                                    'label' => 'Fase 1: Konsep Project',
                                                    'badge' => 'bg-info-subtle text-info border border-info-subtle',
                                                    'icon' => 'ri-lightbulb-line',
                                                ],
                                                'Fase 2' => [
                                                    'label' => 'Fase 2: Administrasi (IO/ PR/ PO)',
                                                    'badge' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                                    'icon' => 'ri-file-list-3-line',
                                                ],
                                                'Fase 3' => [
                                                    'label' => 'Fase 3: Proses Pekerjaan',
                                                    'badge' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                                    'icon' => 'ri-tools-line',
                                                ],
                                            ];
                                        @endphp
                                        @foreach ($faseListConfig as $fKey => $fCfg)
                                            <label class="dropdown-item px-2 py-1 rounded-2 d-flex align-items-center justify-content-between cursor-pointer mb-0 option-item">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input class="form-check-input mt-0 filter-checkbox filter-checkbox-fase" type="checkbox" value="{{ $fKey }}" {{ in_array($fKey, $faseList ?? []) ? 'checked' : '' }}>
                                                    <span class="fs-12 text-dark">{{ $fKey }}</span>
                                                </div>
                                                <span class="badge {{ $fCfg['badge'] }} fs-10 px-2 py-0"><i class="{{ $fCfg['icon'] }} me-1"></i>{{ $fKey }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Area Filter (Dropdown Multi-Select) --}}
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="dropdown custom-filter-dropdown" data-filter="area">
                                <button
                                    class="btn btn-outline-light text-dark bg-white border w-100 d-flex justify-content-between align-items-center text-truncate dropdown-toggle shadow-none py-2 px-2 filter-btn"
                                    type="button" id="dropdownAreaBtn" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" aria-expanded="false">
                                    <span class="filter-btn-text text-truncate me-1">
                                        <i class="ri-map-pin-line text-muted me-1"></i><span class="label-text">Semua Area</span>
                                    </span>
                                    <span class="badge bg-primary rounded-pill filter-count-badge d-none">0</span>
                                </button>
                                <div class="dropdown-menu p-2 shadow-lg border-0 rounded-3" style="min-width: 230px;" aria-labelledby="dropdownAreaBtn">
                                    <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                        <small class="fw-bold text-muted text-uppercase fs-11">Filter Area</small>
                                        <div class="d-flex gap-2">
                                            <a href="javascript:void(0)" class="fs-11 text-primary text-decoration-none fw-medium btn-select-all" data-target="area">Pilih Semua</a>
                                            <span class="text-muted fs-11">|</span>
                                            <a href="javascript:void(0)" class="fs-11 text-danger text-decoration-none fw-medium btn-clear-filter" data-target="area">Reset</a>
                                        </div>
                                    </div>
                                    <div class="filter-options-list" style="max-height: 220px; overflow-y: auto;">
                                        @foreach ($areasList as $ar)
                                            <label class="dropdown-item px-2 py-1 rounded-2 d-flex align-items-center gap-2 cursor-pointer mb-0 option-item">
                                                <input class="form-check-input mt-0 filter-checkbox filter-checkbox-area" type="checkbox" value="{{ $ar }}" {{ in_array($ar, $areaList ?? []) ? 'checked' : '' }}>
                                                <span class="fs-12 text-dark">{{ $ar }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kategori Filter (Dropdown Multi-Select) --}}
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="dropdown custom-filter-dropdown" data-filter="kategori">
                                <button
                                    class="btn btn-outline-light text-dark bg-white border w-100 d-flex justify-content-between align-items-center text-truncate dropdown-toggle shadow-none py-2 px-2 filter-btn"
                                    type="button" id="dropdownKategoriBtn" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" aria-expanded="false">
                                    <span class="filter-btn-text text-truncate me-1">
                                        <i class="ri-price-tag-3-line text-muted me-1"></i><span class="label-text">Semua Kategori</span>
                                    </span>
                                    <span class="badge bg-primary rounded-pill filter-count-badge d-none">0</span>
                                </button>
                                <div class="dropdown-menu p-2 shadow-lg border-0 rounded-3" style="min-width: 250px;" aria-labelledby="dropdownKategoriBtn">
                                    <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                        <small class="fw-bold text-muted text-uppercase fs-11">Filter Kategori</small>
                                        <div class="d-flex gap-2">
                                            <a href="javascript:void(0)" class="fs-11 text-primary text-decoration-none fw-medium btn-select-all" data-target="kategori">Pilih Semua</a>
                                            <span class="text-muted fs-11">|</span>
                                            <a href="javascript:void(0)" class="fs-11 text-danger text-decoration-none fw-medium btn-clear-filter" data-target="kategori">Reset</a>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light border-end-0 py-1"><i class="ri-search-line fs-11"></i></span>
                                            <input type="text" class="form-control form-control-sm border-start-0 filter-search-input" placeholder="Cari kategori..." data-filter="kategori" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="filter-options-list" style="max-height: 200px; overflow-y: auto;">
                                        @foreach ($categoriesList as $cat)
                                            <label class="dropdown-item px-2 py-1 rounded-2 gap-2 cursor-pointer mb-0 option-item">
                                                <input class="form-check-input mt-0 filter-checkbox filter-checkbox-kategori" type="checkbox" value="{{ $cat }}" {{ in_array($cat, $kategoriList ?? []) ? 'checked' : '' }}>
                                                <span class="fs-12 text-dark text-truncate">{{ $cat }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Prioritas Filter (Dropdown Multi-Select) --}}
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="dropdown custom-filter-dropdown" data-filter="priority">
                                <button
                                    class="btn btn-outline-light text-dark bg-white border w-100 d-flex justify-content-between align-items-center text-truncate dropdown-toggle shadow-none py-2 px-2 filter-btn"
                                    type="button" id="dropdownPriorityBtn" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" aria-expanded="false">
                                    <span class="filter-btn-text text-truncate me-1">
                                        <i class="ri-flag-line text-muted me-1"></i><span class="label-text">Semua Prioritas</span>
                                    </span>
                                    <span class="badge bg-primary rounded-pill filter-count-badge d-none">0</span>
                                </button>
                                <div class="dropdown-menu p-2 shadow-lg border-0 rounded-3" style="min-width: 220px;" aria-labelledby="dropdownPriorityBtn">
                                    <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                        <small class="fw-bold text-muted text-uppercase fs-11">Filter Prioritas</small>
                                        <div class="d-flex gap-2">
                                            <a href="javascript:void(0)" class="fs-11 text-primary text-decoration-none fw-medium btn-select-all" data-target="priority">Pilih Semua</a>
                                            <span class="text-muted fs-11">|</span>
                                            <a href="javascript:void(0)" class="fs-11 text-danger text-decoration-none fw-medium btn-clear-filter" data-target="priority">Reset</a>
                                        </div>
                                    </div>
                                    <div class="filter-options-list">
                                        @php
                                            $priorities = [
                                                'Critical' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                                'High' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                                'Medium' => 'bg-info-subtle text-info border border-info-subtle',
                                                'Low' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                            ];
                                        @endphp
                                        @foreach ($priorities as $prKey => $prClass)
                                            <label class="dropdown-item px-2 py-1 rounded-2 d-flex align-items-center justify-content-between cursor-pointer mb-0 option-item">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input class="form-check-input mt-0 filter-checkbox filter-checkbox-priority" type="checkbox" value="{{ $prKey }}" {{ in_array($prKey, $priorityList ?? []) ? 'checked' : '' }}>
                                                    <span class="fs-12 text-dark">{{ $prKey }}</span>
                                                </div>
                                                <span class="badge {{ $prClass }} fs-10 px-2 py-0">{{ $prKey }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Status Filter (Dropdown Multi-Select) --}}
                        <div class="col-xl-1 col-md-3 col-sm-6">
                            <div class="dropdown custom-filter-dropdown" data-filter="status">
                                <button
                                    class="btn btn-outline-light text-dark bg-white border w-100 d-flex justify-content-between align-items-center text-truncate dropdown-toggle shadow-none py-2 px-2 filter-btn"
                                    type="button" id="dropdownStatusBtn" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" aria-expanded="false">
                                    <span class="filter-btn-text text-truncate me-1">
                                        <i class="ri-checkbox-circle-line text-muted me-1"></i><span class="label-text">Status</span>
                                    </span>
                                    <span class="badge bg-primary rounded-pill filter-count-badge d-none">0</span>
                                </button>
                                <div class="dropdown-menu p-2 shadow-lg border-0 rounded-3" style="min-width: 220px;" aria-labelledby="dropdownStatusBtn">
                                    <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                        <small class="fw-bold text-muted text-uppercase fs-11">Filter Status</small>
                                        <div class="d-flex gap-2">
                                            <a href="javascript:void(0)" class="fs-11 text-primary text-decoration-none fw-medium btn-select-all" data-target="status">Pilih Semua</a>
                                            <span class="text-muted fs-11">|</span>
                                            <a href="javascript:void(0)" class="fs-11 text-danger text-decoration-none fw-medium btn-clear-filter" data-target="status">Reset</a>
                                        </div>
                                    </div>
                                    <div class="filter-options-list">
                                        @php
                                            $statuses = [
                                                'Selesai' => [
                                                    'class' => 'bg-success-subtle text-success border border-success-subtle',
                                                    'icon' => 'ri-checkbox-circle-line',
                                                ],
                                                'On Progress' => [
                                                    'class' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                                    'icon' => 'ri-time-line',
                                                ],
                                                'Terlambat' => [
                                                    'class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                                    'icon' => 'ri-alert-line',
                                                ],
                                            ];
                                        @endphp
                                        @foreach ($statuses as $stKey => $stMeta)
                                            <label class="dropdown-item px-2 py-1 rounded-2 d-flex align-items-center justify-content-between cursor-pointer mb-0 option-item">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input class="form-check-input mt-0 filter-checkbox filter-checkbox-status" type="checkbox" value="{{ $stKey }}" {{ in_array($stKey, $statusList ?? []) ? 'checked' : '' }}>
                                                    <span class="fs-12 text-dark">{{ $stKey }}</span>
                                                </div>
                                                <span class="badge {{ $stMeta['class'] }} fs-10 px-2 py-0"><i class="{{ $stMeta['icon'] }} me-1"></i>{{ $stKey }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Reset Button --}}
                        <div class="col-xl-1 col-md-3 col-sm-6 d-flex gap-1">
                            <button type="button" id="btnDashResetFilter" class="btn btn-outline-danger w-100 py-2 filter-btn" title="Reset Semua Filter">
                                <i class="ri-refresh-line"></i>
                            </button>
                        </div>
                    </form>

                    {{-- Active Filter Badges Row --}}
                    <div id="activeFiltersRow" class="d-flex flex-wrap align-items-center gap-1 mt-2 pt-2 border-top d-none">
                        <span class="fs-12 text-muted me-1"><i class="ri-filter-3-line me-1"></i>Filter Aktif:</span>
                        <div id="activeFilterBadges" class="d-flex flex-wrap gap-1 align-items-center"></div>
                        <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0 ms-2 fs-12 fw-semibold" id="btnClearAllActive">
                            <i class="ri-close-circle-line me-1"></i>Hapus Semua
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── 5 KPI Cards (Light Bootstrap) ── --}}
            <div class="row g-3 mb-3">
                {{-- Total Project --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card card-animate border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Total Project</p>
                                    <h3 class="mb-0 fw-bold text-dark" id="kpiTotal">{{ $kpis['total'] }}</h3>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle rounded-circle fs-3">
                                        <i class="ri-folders-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3 fs-12 text-muted">
                                <span class="badge bg-light text-secondary border me-1"
                                    id="kpiActiveCategories">{{ $kpis['active_categories'] }}</span>
                                <span>Kategori Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Selesai --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card card-animate border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Selesai</p>
                                    <h3 class="mb-0 fw-bold text-success" id="kpiSelesai">{{ $kpis['selesai'] }}</h3>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3 fs-12">
                                <span class="badge bg-success-subtle text-success me-1"
                                    id="kpiSelesaiPct">{{ $kpis['selesai_pct'] }}%</span>
                                <span class="text-muted">dari total project</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- On Progress --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card card-animate border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">On Progress</p>
                                    <h3 class="mb-0 fw-bold text-warning" id="kpiOnProgress">{{ $kpis['on_progress'] }}
                                    </h3>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                        <i class="ri-time-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3 fs-12">
                                <span class="badge bg-warning-subtle text-warning me-1"
                                    id="kpiOnProgressPct">{{ $kpis['on_progress_pct'] }}%</span>
                                <span class="text-muted">sedang berjalan</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Terlambat --}}
                <div class="col-xl-3 col-md-6 col-sm-6">
                    <div class="card card-animate border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Terlambat</p>
                                    <h3 class="mb-0 fw-bold text-danger" id="kpiTerlambat">{{ $kpis['terlambat'] }}</h3>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-3">
                                        <i class="ri-error-warning-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-3 fs-12">
                                <span class="badge bg-danger-subtle text-danger me-1"
                                    id="kpiTerlambatPct">{{ $kpis['terlambat_pct'] }}%</span>
                                <span class="text-muted">butuh penanganan</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Rata-Rata Progress --}}
                <div class="col-xl-3 col-md-6 col-sm-12">
                    <div class="card card-animate border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Rata-Rata Progress</p>
                                    <h3 class="mb-0 fw-bold text-primary" id="kpiAvgProgress">
                                        {{ $kpis['avg_progress'] }}%
                                    </h3>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                        <i class="ri-pie-chart-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="progress progress-sm mt-3" style="height: 6px;">
                                <div class="progress-bar bg-primary" id="kpiAvgProgressBar" role="progressbar"
                                    style="width: {{ $kpis['avg_progress'] }}%;"
                                    aria-valuenow="{{ $kpis['avg_progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Charts Row 1: Summary Status, Summary Fase & Status per Kategori ── --}}
            <div class="row g-3 mb-3">
                {{-- Donut Chart: Status Summary --}}
                <div class="col-xl-3 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="card-title mb-0 fw-bold text-dark">
                                    <i class="ri-pie-chart-2-line text-primary me-2"></i>Status Summary
                                </h6>
                                <span class="badge bg-light text-muted border" id="summaryTotalBadge">Total:
                                    {{ $kpis['total'] }}</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div id="chartStatusSummary" style="min-height: 280px;"></div>
                            <div class="row text-center mt-2 g-1 border-top pt-2">
                                <div class="col-4">
                                    <span class="d-block text-muted fs-11">Selesai</span>
                                    <span class="fw-bold text-success fs-13"
                                        id="summaryPctSelesai">{{ $chartStatusSummary['percentages']['selesai'] }}%</span>
                                    <small class="text-muted d-block fs-10"
                                        id="summaryCountSelesai">({{ $chartStatusSummary['counts']['selesai'] ?? $kpis['selesai'] }})</small>
                                </div>
                                <div class="col-4 border-start border-end">
                                    <span class="d-block text-muted fs-11">On Progress</span>
                                    <span class="fw-bold text-warning fs-13"
                                        id="summaryPctOnProgress">{{ $chartStatusSummary['percentages']['on_progress'] }}%</span>
                                    <small class="text-muted d-block fs-10"
                                        id="summaryCountOnProgress">({{ $chartStatusSummary['counts']['on_progress'] ?? $kpis['on_progress'] }})</small>
                                </div>
                                <div class="col-4">
                                    <span class="d-block text-muted fs-11">Terlambat</span>
                                    <span class="fw-bold text-danger fs-13"
                                        id="summaryPctTerlambat">{{ $chartStatusSummary['percentages']['terlambat'] }}%</span>
                                    <small class="text-muted d-block fs-10"
                                        id="summaryCountTerlambat">({{ $chartStatusSummary['counts']['terlambat'] ?? $kpis['terlambat'] }})</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Donut Chart: Summary Fase --}}
                <div class="col-xl-3 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="card-title mb-0 fw-bold text-dark">
                                    <i class="ri-git-commit-line text-info me-2"></i>Summary Fase
                                </h6>
                                <span class="badge bg-light text-muted border" id="faseTotalBadge">Total:
                                    {{ $kpis['total'] }}</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div id="chartFaseSummary" style="min-height: 280px;"></div>
                            <div class="row text-center mt-2 g-1 border-top pt-2">
                                <div class="col-4">
                                    <span class="d-block text-muted fs-11" title="Fase 1: Konsep Project">Fase 1</span>
                                    <span class="fw-bold text-info fs-13"
                                        id="fasePct1">{{ $chartFaseSummary['percentages']['fase1'] ?? 0 }}%</span>
                                    <small class="text-muted d-block fs-10"
                                        id="faseCount1">({{ $chartFaseSummary['counts']['fase1'] ?? 0 }})</small>
                                </div>
                                <div class="col-4 border-start border-end">
                                    <span class="d-block text-muted fs-11" title="Fase 2: Administrasi (IO/PR/PO)">Fase
                                        2</span>
                                    <span class="fw-bold text-warning fs-13"
                                        id="fasePct2">{{ $chartFaseSummary['percentages']['fase2'] ?? 0 }}%</span>
                                    <small class="text-muted d-block fs-10"
                                        id="faseCount2">({{ $chartFaseSummary['counts']['fase2'] ?? 0 }})</small>
                                </div>
                                <div class="col-4">
                                    <span class="d-block text-muted fs-11" title="Fase 3: Pekerjaan Project">Fase 3</span>
                                    <span class="fw-bold text-primary fs-13"
                                        id="fasePct3">{{ $chartFaseSummary['percentages']['fase3'] ?? 0 }}%</span>
                                    <small class="text-muted d-block fs-10"
                                        id="faseCount3">({{ $chartFaseSummary['counts']['fase3'] ?? 0 }})</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Horizontal Stacked Bar: Status by Kategori --}}
                <div class="col-xl-6 col-lg-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="card-title mb-0 fw-bold text-dark">
                                    <i class="ri-bar-chart-horizontal-line text-primary me-2"></i>Status Project per
                                    Kategori
                                </h6>
                                <small class="text-muted">Akumulasi per kategori pekerjaan</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chartStatusByKategori" style="min-height: 330px;"></div>
                        </div>
                    </div>
                </div>

                {{-- Stacked Bar: Status per Area (Boiler, Utility, WWTP, MTC) --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="card-title mb-0 fw-bold text-dark">
                                    <i class="ri-community-line text-primary me-2"></i>Distribusi Status per Area
                                </h6>
                                <small class="text-muted">Boiler, Utility, WWTP, MTC</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chartStatusByArea" style="min-height: 280px;"></div>
                        </div>
                    </div>
                </div>

                {{-- Top 5 Attention Projects --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="card-title mb-0 fw-bold text-dark">
                                    <i class="ri-alarm-warning-line text-warning me-2"></i>Project Prioritas (Perlu
                                    Perhatian)
                                </h6>
                                <a href="{{ route('utility.project.data') }}" class="btn btn-sm btn-soft-primary">
                                    Lihat Semua <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 312px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0 text-nowrap" style="min-width: 500px;">
                                    <thead class="fs-12 text-muted table-sticky-header">
                                        <tr>
                                            <th style="min-width: 170px;">Project</th>
                                            <th style="width: 90px;" class="text-center">Prioritas</th>
                                            <th style="width: 80px;" class="text-center">Area</th>
                                            <th style="min-width: 120px;">Progress</th>
                                            <th style="width: 100px;">Status</th>
                                            <th class="text-end" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attentionProjectsBody">
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <div class="spinner-border spinner-border-sm text-primary me-2"
                                                    role="status"></div>
                                                Memuat data project prioritas...
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

    {{-- ── Modal Quick Update Progress (Light Bootstrap) ── --}}
    <div class="modal fade" id="modalQuickProgress" tabindex="-1" aria-labelledby="modalQuickProgressLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="modalQuickProgressLabel">
                        <i class="ri-edit-2-line text-primary me-2"></i>Update Progress Project
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formQuickProgress">
                    @csrf
                    <input type="hidden" id="quick_project_id" name="project_id">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-muted fs-12 mb-1">Nama Project</label>
                            <div id="quick_project_nama" class="fw-semibold text-dark fs-14 p-2 bg-light rounded border">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold text-dark mb-0">Progress Persentase (%)</label>
                                <span id="quick_slider_val" class="badge bg-primary fs-13">0%</span>
                            </div>
                            <input type="range" class="form-range" id="quick_progress_range" min="0"
                                max="100" step="1" value="0">
                            <div class="input-group input-group-sm mt-2">
                                <span class="input-group-text bg-light">Input Angka</span>
                                <input type="number" class="form-control" id="quick_progress_input" name="progress"
                                    min="0" max="100" value="0" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Tanggal Update</label>
                                <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    Fase Project <small class="text-muted fw-normal fs-11">(Opsional)</small>
                                </label>
                                <select class="form-select" id="quick_fase" name="fase">
                                    <option value="">-- Tetap (Tidak Diubah) --</option>
                                    @foreach ($faseOptions ?? [] as $fKey => $fLabel)
                                        <option value="{{ $fKey }}">{{ $fLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Catatan Progress / Kendala <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" name="catatan" rows="3"
                                placeholder="Tuliskan perkembangan pekerjaan, kendala teknis, atau catatan lainnya..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitQuickProgress">
                            <i class="ri-save-line me-1"></i> Simpan Update Progress
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // ── Setup AJAX CSRF Token ──
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            let chartSummary = null;
            let chartFase = null;
            let chartKategori = null;
            let chartArea = null;

            function escapeHtml(str) {
                if (!str) return '';
                return $('<div>').text(str).html();
            }

            // ── 1. Chart Status Summary (Donut Chart) ──
            const summaryData = @json($chartStatusSummary);
            const summaryOptions = {
                series: (summaryData && summaryData.series) ? summaryData.series : [0, 0, 0],
                labels: (summaryData && summaryData.labels) ? summaryData.labels : ['Selesai', 'On Progress',
                    'Terlambat'
                ],
                chart: {
                    type: 'donut',
                    height: 280,
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#0ab39c', '#f7b84b', '#f06548'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '13px',
                                    fontWeight: 600,
                                    color: '#495057'
                                },
                                value: {
                                    show: true,
                                    fontSize: '20px',
                                    fontWeight: 700,
                                    color: '#212529',
                                    formatter: function(val) {
                                        return val + ' prj';
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total Project',
                                    fontSize: '12px',
                                    fontWeight: 600,
                                    color: '#495057',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                stroke: {
                    width: 2,
                    colors: ['#fff']
                },
                legend: {
                    position: 'bottom',
                    fontSize: '12px',
                    markers: {
                        radius: 12
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif',
                        fontWeight: 700,
                        colors: ['#ffffff']
                    },
                    formatter: function(val) {
                        return Math.round(val) + "%";
                    }
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function(val) {
                            return val + " Project";
                        }
                    }
                }
            };
            if (document.querySelector("#chartStatusSummary")) {
                chartSummary = new ApexCharts(document.querySelector("#chartStatusSummary"), summaryOptions);
                chartSummary.render();
            }

            // ── 1b. Chart Fase Summary (Donut Chart) ──
            const faseData = @json($chartFaseSummary ?? null);
            const faseOptions = {
                series: (faseData && faseData.series) ? faseData.series : [0, 0, 0],
                labels: (faseData && faseData.labels) ? faseData.labels : ['Fase 1: Konsep',
                    'Fase 2: Administrasi',
                    'Fase 3: Pekerjaan'
                ],
                chart: {
                    type: 'donut',
                    height: 280,
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#299cdb', '#f7b84b', '#405189'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '13px',
                                    fontWeight: 600,
                                    color: '#495057'
                                },
                                value: {
                                    show: true,
                                    fontSize: '20px',
                                    fontWeight: 700,
                                    color: '#212529',
                                    formatter: function(val) {
                                        return val + ' prj';
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total Project',
                                    fontSize: '12px',
                                    fontWeight: 600,
                                    color: '#495057',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                stroke: {
                    width: 2,
                    colors: ['#fff']
                },
                legend: {
                    position: 'bottom',
                    fontSize: '12px',
                    markers: {
                        radius: 12
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif',
                        fontWeight: 700,
                        colors: ['#ffffff']
                    },
                    dropShadow: {
                        enabled: true,
                        top: 1,
                        left: 1,
                        blur: 2,
                        color: '#000000',
                        opacity: 0.75
                    },
                    formatter: function(val) {
                        return Math.round(val) + "%";
                    }
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function(val) {
                            return val + " Project";
                        }
                    }
                }
            };
            if (document.querySelector("#chartFaseSummary")) {
                chartFase = new ApexCharts(document.querySelector("#chartFaseSummary"), faseOptions);
                chartFase.render();
            }

            // ── 2. Chart Status by Kategori (Horizontal Stacked Bar) ──
            const kategoriData = @json($chartStatusByKategori);
            const kategoriOptions = {
                series: (kategoriData && kategoriData.series) ? kategoriData.series : [],
                chart: {
                    type: 'bar',
                    height: 330,
                    stacked: true,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#0ab39c', '#f7b84b', '#f06548'],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '60%',
                        borderRadius: 2
                    }
                },
                stroke: {
                    width: 1,
                    colors: ['#fff']
                },
                xaxis: {
                    categories: (kategoriData && kategoriData.categories) ? kategoriData.categories : [],
                    labels: {
                        style: {
                            colors: '#74788d',
                            fontSize: '11px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#495057',
                            fontSize: '12px',
                            fontWeight: 500
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 3
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px'
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function(val) {
                            return val + " Project";
                        }
                    }
                }
            };
            if (document.querySelector("#chartStatusByKategori")) {
                chartKategori = new ApexCharts(document.querySelector("#chartStatusByKategori"), kategoriOptions);
                chartKategori.render();
            }

            // ── 3. Chart Status by Area (Vertical Stacked Bar) ──
            const areaData = @json($chartStatusByArea);
            const areaOptions = {
                series: (areaData && areaData.series) ? areaData.series : [],
                chart: {
                    type: 'bar',
                    height: 280,
                    stacked: true,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#0ab39c', '#f7b84b', '#f06548'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '45%',
                        borderRadius: 4
                    }
                },
                stroke: {
                    width: 1,
                    colors: ['#fff']
                },
                xaxis: {
                    categories: (areaData && areaData.categories) ? areaData.categories : [],
                    labels: {
                        style: {
                            colors: '#495057',
                            fontSize: '12px',
                            fontWeight: 600
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Project',
                        style: {
                            color: '#74788d',
                            fontSize: '11px'
                        }
                    },
                    labels: {
                        style: {
                            colors: '#74788d',
                            fontSize: '11px'
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 3
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px'
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function(val) {
                            return val + " Project";
                        }
                    }
                }
            };
            if (document.querySelector("#chartStatusByArea")) {
                chartArea = new ApexCharts(document.querySelector("#chartStatusByArea"), areaOptions);
                chartArea.render();
            }

            function getPriorityBadge(priority) {
                if (priority === 'Critical') {
                    return '<span class="badge bg-danger text-white px-2 py-1 fs-11"><i class="ri-alarm-warning-fill me-1"></i>Critical</span>';
                } else if (priority === 'High') {
                    return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-11 fw-semibold"><i class="ri-arrow-up-circle-fill me-1"></i>High</span>';
                } else if (priority === 'Low') {
                    return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 fs-11 fw-semibold"><i class="ri-arrow-down-circle-line me-1"></i>Low</span>';
                }
                return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-11 fw-semibold"><i class="ri-equal-circle-fill me-1"></i>Medium</span>';
            }

            // ── 4. Render Attention Projects Table Body ──
            function renderAttentionProjects(projects) {
                const $tbody = $('#attentionProjectsBody');
                $tbody.empty();

                if (!projects || projects.length === 0) {
                    $tbody.html(`
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="ri-checkbox-circle-line fs-24 text-success d-block mb-1"></i>
                        Semua project telah selesai atau tidak ada yang sesuai kriteria filter!
                    </td>
                </tr>
            `);
                    return;
                }

                projects.forEach(function(prj) {
                    let areaBadge = '';
                    if (prj.area === 'Boiler') {
                        areaBadge =
                            '<span class="badge bg-danger-subtle text-danger px-2 py-1 fs-12">Boiler</span>';
                    } else if (prj.area === 'Utility') {
                        areaBadge =
                            '<span class="badge bg-primary-subtle text-primary px-2 py-1 fs-12">Utility</span>';
                    } else if (prj.area === 'WWTP') {
                        areaBadge =
                            '<span class="badge bg-info-subtle text-info px-2 py-1 fs-12">WWTP</span>';
                    } else {
                        areaBadge =
                            `<span class="badge bg-secondary-subtle text-secondary px-2 py-1 fs-12">${escapeHtml(prj.area || 'MTC')}</span>`;
                    }

                    const isTerlambat = prj.status === 'Terlambat';
                    const barColor = isTerlambat ? 'bg-danger' : 'bg-primary';
                    const statusBadge = isTerlambat ?
                        '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-12"><i class="ri-alert-line me-1"></i>Terlambat</span>' :
                        '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-12"><i class="ri-time-line me-1"></i>On Progress</span>';

                    const row = `
                <tr>
                    <td>
                        <div class="fw-semibold text-dark fs-13 text-truncate" style="max-width: 200px;" title="${escapeHtml(prj.nama_project)}">${escapeHtml(prj.nama_project)}</div>
                        <div class="d-flex align-items-center gap-1 mt-1">
                            <span class="badge bg-light text-muted border fs-11">${escapeHtml(prj.nomor_project)}</span>
                            <span class="text-muted fs-11">&bull; Target: ${escapeHtml(prj.finish_date || '-')}</span>
                        </div>
                    </td>
                    <td class="text-center">${getPriorityBadge(prj.priority)}</td>
                    <td class="text-center">${areaBadge}</td>
                    <td style="min-width: 120px;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 6px;">
                                <div class="progress-bar ${barColor}" style="width: ${prj.progress}%;"></div>
                            </div>
                            <span class="fs-12 fw-semibold text-muted">${prj.progress}%</span>
                        </div>
                    </td>
                    <td>${statusBadge}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-update-progress"
                            data-id="${prj.id}"
                            data-nama="${escapeHtml(prj.nama_project)}"
                            data-progress="${prj.progress}"
                            data-fase="${escapeHtml(prj.fase || '')}">
                            <i class="ri-edit-line"></i> Update
                        </button>
                    </td>
                </tr>
            `;
                    $tbody.append(row);
                });
            }

            // Initial render of Attention Projects
            renderAttentionProjects(@json($attentionProjects));

            // ── Multi-Select Helpers ──
            const defaultFilterLabels = {
                year: 'Semua Tahun',
                fase: 'Semua Fase',
                area: 'Semua Area',
                kategori: 'Semua Kategori',
                priority: 'Semua Prioritas',
                status: 'Status'
            };

            function getSelectedFilterValues(filterName) {
                const vals = [];
                $(`.filter-checkbox-${filterName}:checked`).each(function() {
                    vals.push($(this).val());
                });
                return vals;
            }

            function updateDropdownLabels() {
                const filters = ['year', 'fase', 'area', 'kategori', 'priority', 'status'];
                filters.forEach(function(f) {
                    const $container = $(`.custom-filter-dropdown[data-filter="${f}"]`);
                    const $btn = $container.find('.filter-btn');
                    const $label = $container.find('.label-text');
                    const $badge = $container.find('.filter-count-badge');
                    const vals = getSelectedFilterValues(f);

                    if (vals.length === 0) {
                        $label.text(defaultFilterLabels[f]);
                        $badge.addClass('d-none').text('0');
                        $btn.removeClass('is-active');
                    } else if (vals.length === 1) {
                        $label.text(f === 'year' ? `Tahun ${vals[0]}` : vals[0]);
                        $badge.removeClass('d-none').text('1');
                        $btn.addClass('is-active');
                    } else {
                        const titleNames = {
                            year: 'Tahun',
                            fase: 'Fase',
                            area: 'Area',
                            kategori: 'Kategori',
                            priority: 'Prioritas',
                            status: 'Status'
                        };
                        $label.text(titleNames[f]);
                        $badge.removeClass('d-none').text(vals.length);
                        $btn.addClass('is-active');
                    }
                });
            }

            function updateActiveFilterBadges() {
                const $badgesContainer = $('#activeFilterBadges');
                $badgesContainer.empty();
                let totalActive = 0;

                const filters = ['year', 'fase', 'area', 'kategori', 'priority', 'status'];
                const titleNames = {
                    year: 'Tahun',
                    fase: 'Fase',
                    area: 'Area',
                    kategori: 'Kategori',
                    priority: 'Prioritas',
                    status: 'Status'
                };

                filters.forEach(function(f) {
                    const titleName = titleNames[f];
                    const vals = getSelectedFilterValues(f);
                    totalActive += vals.length;

                    vals.forEach(function(val) {
                        let colorClass = 'bg-primary-subtle text-primary border-primary-subtle';
                        if (f === 'status') {
                            if (val === 'Selesai') colorClass =
                                'bg-success-subtle text-success border-success-subtle';
                            else if (val === 'Terlambat') colorClass =
                                'bg-danger-subtle text-danger border-danger-subtle';
                            else colorClass =
                                'bg-warning-subtle text-warning border-warning-subtle';
                        } else if (f === 'priority') {
                            if (val === 'Critical') colorClass =
                                'bg-danger-subtle text-danger border-danger-subtle';
                            else if (val === 'High') colorClass =
                                'bg-warning-subtle text-warning border-warning-subtle';
                            else if (val === 'Medium') colorClass =
                                'bg-info-subtle text-info border-info-subtle';
                            else colorClass =
                                'bg-secondary-subtle text-secondary border-secondary-subtle';
                        } else if (f === 'fase') {
                            if (val === 'Fase 1') colorClass =
                                'bg-info-subtle text-info border-info-subtle';
                            else if (val === 'Fase 2') colorClass =
                                'bg-warning-subtle text-warning border-warning-subtle';
                            else colorClass =
                                'bg-primary-subtle text-primary border-primary-subtle';
                        } else if (f === 'year') {
                            colorClass = 'bg-dark-subtle text-dark border-dark-subtle';
                        }

                        $badgesContainer.append(`
                            <span class="badge ${colorClass} border d-inline-flex align-items-center gap-1 py-1 px-2 fs-11">
                                <span class="text-muted">${titleName}:</span> <strong>${escapeHtml(val)}</strong>
                                <i class="ri-close-line cursor-pointer text-danger ms-1 btn-remove-active-tag" data-filter="${f}" data-value="${escapeHtml(val)}" title="Hapus filter"></i>
                            </span>
                        `);
                    });
                });

                if (totalActive > 0) {
                    $('#activeFiltersRow').removeClass('d-none');
                } else {
                    $('#activeFiltersRow').addClass('d-none');
                }
            }

            // ── 5. Load Dashboard Data via jQuery AJAX ──
            window.loadDashboardData = function() {
                const $btnRefresh = $('#btnRefreshDashboard');
                const origRefreshText = $btnRefresh.html();
                $btnRefresh.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Loading...');

                const filterParams = {
                    format: 'json',
                    year: getSelectedFilterValues('year'),
                    fase: getSelectedFilterValues('fase'),
                    area: getSelectedFilterValues('area'),
                    kategori: getSelectedFilterValues('kategori'),
                    priority: getSelectedFilterValues('priority'),
                    status: getSelectedFilterValues('status')
                };

                $.ajax({
                    url: '{{ route('utility.project.dashboard') }}',
                    type: 'GET',
                    data: filterParams,
                    dataType: 'json',
                    success: function(res) {
                        if (!res.success) return;

                        // 1. Alert Banner
                        if (res.kpis && res.kpis.terlambat > 0) {
                            $('#alertTerlambatCount').text(res.kpis.terlambat + ' project');
                            $('#headerTerlambatCount').text(res.kpis.terlambat);
                            $('#btnToggleAlertTerlambat').css('display', 'inline-flex');
                            if ($('#alertTerlambatBanner').is(':visible')) {
                                $('#alertTerlambatBanner').show();
                            }
                        } else {
                            $('#btnToggleAlertTerlambat').hide();
                            $('#alertTerlambatBanner').hide();
                        }

                        // 2. KPI Cards
                        if (res.kpis) {
                            $('#kpiTotal').text(res.kpis.total);
                            $('#kpiActiveCategories').text(res.kpis.active_categories);
                            $('#kpiSelesai').text(res.kpis.selesai);
                            $('#kpiSelesaiPct').text(res.kpis.selesai_pct + '%');
                            $('#kpiOnProgress').text(res.kpis.on_progress);
                            $('#kpiOnProgressPct').text(res.kpis.on_progress_pct + '%');
                            $('#kpiTerlambat').text(res.kpis.terlambat);
                            $('#kpiTerlambatPct').text(res.kpis.terlambat_pct + '%');
                            $('#kpiAvgProgress').text(res.kpis.avg_progress + '%');
                            $('#kpiAvgProgressBar').css('width', res.kpis.avg_progress + '%').attr(
                                'aria-valuenow', res.kpis.avg_progress);
                            $('#summaryTotalBadge').text('Total: ' + res.kpis.total);
                        }

                        // 3. Status Summary Percentages, Counts & Chart
                        if (res.chartStatusSummary) {
                            if (res.chartStatusSummary.percentages) {
                                $('#summaryPctSelesai').text((res.chartStatusSummary.percentages
                                    .selesai || 0) + '%');
                                $('#summaryPctOnProgress').text((res.chartStatusSummary.percentages
                                    .on_progress || 0) + '%');
                                $('#summaryPctTerlambat').text((res.chartStatusSummary.percentages
                                    .terlambat || 0) + '%');
                            }
                            if (res.chartStatusSummary.counts) {
                                $('#summaryCountSelesai').text('(' + (res.chartStatusSummary.counts
                                    .selesai || 0) + ')');
                                $('#summaryCountOnProgress').text('(' + (res.chartStatusSummary
                                    .counts
                                    .on_progress || 0) + ')');
                                $('#summaryCountTerlambat').text('(' + (res.chartStatusSummary
                                    .counts
                                    .terlambat || 0) + ')');
                            }
                            if (chartSummary && res.chartStatusSummary.series) {
                                chartSummary.updateSeries(res.chartStatusSummary.series);
                            }
                        }

                        // 3b. Summary Fase Percentages, Counts & Chart
                        if (res.chartFaseSummary) {
                            if (res.chartFaseSummary.percentages) {
                                $('#fasePct1').text((res.chartFaseSummary.percentages.fase1 || 0) +
                                    '%');
                                $('#fasePct2').text((res.chartFaseSummary.percentages.fase2 || 0) +
                                    '%');
                                $('#fasePct3').text((res.chartFaseSummary.percentages.fase3 || 0) +
                                    '%');
                            }
                            if (res.chartFaseSummary.counts) {
                                $('#faseCount1').text('(' + (res.chartFaseSummary.counts.fase1 ||
                                    0) + ')');
                                $('#faseCount2').text('(' + (res.chartFaseSummary.counts.fase2 ||
                                    0) + ')');
                                $('#faseCount3').text('(' + (res.chartFaseSummary.counts.fase3 ||
                                    0) + ')');
                            }
                            $('#faseTotalBadge').text('Total: ' + (res.kpis ? res.kpis.total : 0));
                            if (chartFase && res.chartFaseSummary.series) {
                                chartFase.updateSeries(res.chartFaseSummary.series);
                            }
                        }

                        // 4. Chart Status by Kategori
                        if (res.chartStatusByKategori && chartKategori) {
                            chartKategori.updateOptions({
                                series: res.chartStatusByKategori.series,
                                xaxis: {
                                    categories: res.chartStatusByKategori.categories
                                }
                            });
                        }

                        // 5. Chart Status by Area
                        if (res.chartStatusByArea && chartArea) {
                            chartArea.updateOptions({
                                series: res.chartStatusByArea.series,
                                xaxis: {
                                    categories: res.chartStatusByArea.categories
                                }
                            });
                        }

                        // 6. Attention Projects Table
                        renderAttentionProjects(res.attentionProjects || []);
                    },
                    error: function(xhr) {
                        console.error('Gagal memuat data dashboard:', xhr);
                    },
                    complete: function() {
                        $btnRefresh.prop('disabled', false).html(origRefreshText);
                    }
                });
            };

            // Event listener Filter Multi-Select Checkboxes
            $(document).on('change', '.filter-checkbox', function() {
                updateDropdownLabels();
                updateActiveFilterBadges();
                window.loadDashboardData();
            });

            // Remove tag from active filters bar
            $(document).on('click', '.btn-remove-active-tag', function(e) {
                e.preventDefault();
                const filter = $(this).data('filter');
                const val = String($(this).data('value'));
                $(`.filter-checkbox-${filter}`).filter(function() {
                    return $(this).val() === val;
                }).prop('checked', false);
                updateDropdownLabels();
                updateActiveFilterBadges();
                window.loadDashboardData();
            });

            // Select all options in a dropdown
            $('.btn-select-all').on('click', function(e) {
                e.preventDefault();
                const target = $(this).data('target');
                $(`.filter-checkbox-${target}`).prop('checked', true);
                updateDropdownLabels();
                updateActiveFilterBadges();
                window.loadDashboardData();
            });

            // Clear options in a single dropdown
            $('.btn-clear-filter').on('click', function(e) {
                e.preventDefault();
                const target = $(this).data('target');
                $(`.filter-checkbox-${target}`).prop('checked', false);
                updateDropdownLabels();
                updateActiveFilterBadges();
                window.loadDashboardData();
            });

            // Prevent Bootstrap dropdown from intercepting typing/spacebar inside search inputs
            $(document).on('click keydown keypress', '.filter-search-input', function(e) {
                e.stopPropagation();
            });

            // Search filter inside dropdown (e.g. Kategori)
            $(document).on('input keyup', '.filter-search-input', function(e) {
                e.stopPropagation();
                const query = $(this).val().toLowerCase().trim();
                const filter = $(this).data('filter');
                const $container = $(`.custom-filter-dropdown[data-filter="${filter}"]`);
                let visibleCount = 0;

                $container.find('.option-item').each(function() {
                    const text = $(this).find('span').text().toLowerCase();
                    const isMatch = text.indexOf(query) !== -1;
                    if (isMatch) {
                        $(this).removeClass('d-none');
                        visibleCount++;
                    } else {
                        $(this).addClass('d-none');
                    }
                });

                let $emptyMsg = $container.find('.filter-no-results');
                if (visibleCount === 0) {
                    if ($emptyMsg.length === 0) {
                        $container.find('.filter-options-list').append(
                            '<div class="filter-no-results text-center py-2 text-muted fs-11">Tidak ditemukan</div>'
                        );
                    } else {
                        $emptyMsg.removeClass('d-none');
                    }
                } else {
                    $emptyMsg.addClass('d-none');
                }
            });

            // Reset all filters
            $('#btnDashResetFilter, #btnClearAllActive').on('click', function(e) {
                e.preventDefault();
                $('.filter-checkbox').prop('checked', false);
                $('.filter-search-input').val('').trigger('input');
                updateDropdownLabels();
                updateActiveFilterBadges();
                window.loadDashboardData();
            });

            // Refresh Button Click
            $('#btnRefreshDashboard').on('click', function() {
                window.loadDashboardData();
            });

            // Initial UI update for badges and labels
            updateDropdownLabels();
            updateActiveFilterBadges();

            // ── 5. Hide / Show Toggle: Alert Terlambat & Filter Dashboard ──
            // Toggle Alert Terlambat
            $('#btnToggleAlertTerlambat').on('click', function() {
                $('#alertTerlambatBanner').slideToggle(200);
            });

            $('#btnCloseAlertTerlambat').on('click', function() {
                $('#alertTerlambatBanner').slideUp(200);
            });

            // Toggle Filter Dashboard Card
            $('#btnToggleFilterPanel').on('click', function() {
                $('#dashFilterCard').slideToggle(200, function() {
                    const isVis = $(this).is(':visible');
                    $('#textToggleFilter').text(isVis ? 'Tutup Filter' : 'Filter Data');
                });
            });

            $('#btnHideFilterCard').on('click', function() {
                $('#dashFilterCard').slideUp(200, function() {
                    $('#textToggleFilter').text('Filter Data');
                });
            });

            // ── 6. Quick Progress Update Modal Interaction ──
            $('#quick_progress_range').on('input', function() {
                const val = parseInt($(this).val()) || 0;
                $('#quick_progress_input').val(val);
                $('#quick_slider_val').text(val + '%');
                if (val >= 100) {
                    $('#quick_fase').val('Fase 3');
                }
            });

            $('#quick_progress_input').on('input change', function() {
                let val = parseInt($(this).val()) || 0;
                if (val < 0) val = 0;
                if (val > 100) val = 100;
                $('#quick_progress_range').val(val);
                $('#quick_slider_val').text(val + '%');
                if (val >= 100) {
                    $('#quick_fase').val('Fase 3');
                }
            });

            $(document).on('click', '.btn-update-progress', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const prog = parseInt($(this).data('progress')) || 0;
                const fase = $(this).data('fase') || '';

                $('#quick_project_id').val(id);
                $('#quick_project_nama').text(nama);
                $('#quick_progress_range').val(prog);
                $('#quick_progress_input').val(prog);
                $('#quick_slider_val').text(prog + '%');
                $('#quick_fase').val(fase);
                if (prog >= 100) {
                    $('#quick_fase').val('Fase 3');
                }

                $('#modalQuickProgress').modal('show');
            });

            $('#formQuickProgress').on('submit', function(e) {
                e.preventDefault();
                const progVal = parseInt($('#quick_progress_input').val()) || 0;
                if (progVal >= 100) {
                    $('#quick_fase').val('Fase 3');
                }
                const id = $('#quick_project_id').val();
                const $btn = $('#btnSubmitQuickProgress');
                const origText = $btn.html();

                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: `{{ url('utility/project') }}/${id}/progress`,
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            $('#modalQuickProgress').modal('hide');
                            $('#formQuickProgress')[0].reset();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            // Refresh dashboard asynchronously via AJAX without page reload!
                            window.loadDashboardData();
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Gagal memproses request.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(origText);
                    }
                });
            });
        });
    </script>
@endsection
