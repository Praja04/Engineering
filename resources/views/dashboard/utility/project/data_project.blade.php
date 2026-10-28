@extends('layouts.app')

@section('title', 'Data Project Engineering Utility')

@section('styles')
    <style>
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

        .sortable-th {
            cursor: pointer;
            user-select: none;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .sortable-th:hover {
            background-color: #f1f5f9 !important;
            color: #0d6efd !important;
        }

        .sortable-th .sort-icon {
            font-size: 13px;
            opacity: 0.35;
            transition: opacity 0.15s ease, color 0.15s ease;
        }

        .sortable-th:hover .sort-icon {
            opacity: 0.8;
            color: #0d6efd !important;
        }

        .sortable-th.is-sorted {
            color: #0d6efd !important;
            background-color: #f0f7ff !important;
        }

        .sortable-th.is-sorted .sort-icon {
            opacity: 1;
            color: #0d6efd !important;
            font-weight: bold;
        }

        .filter-options-list::-webkit-scrollbar {
            width: 5px;
        }

        .filter-options-list::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
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
                                    <i class="ri-folder-shared-line text-warning me-2"></i>
                                    Data Project Engineering Utility
                                </h4>
                                <p class="text-white-50 mb-0 fs-13">
                                    Kelola data, monitoring target pekerjaan, dan perbarui progres project utility (Boiler,
                                    Utility, WWTP, MTC)
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('utility.project.dashboard') }}"
                                    class="btn btn-info fw-semibold shadow-sm">
                                    <i class="ri-dashboard-2-line me-1"></i> Dashboard
                                </a>
                                <button type="button" class="btn btn-success fw-semibold shadow-sm btn-export-excel"
                                    title="Download seluruh data project ke format Excel (.xlsx)">
                                    <i class="ri-file-excel-2-line me-1"></i> Download Excel
                                </button>
                                @if (Auth::user()->jabatan === 'admin')
                                    <button type="button" class="btn btn-outline-light fw-semibold shadow-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                                        <i class="ri-upload-2-line me-1"></i> Upload Excel
                                    </button>
                                @endif
                                <a href="{{ route('utility.project.form') }}" class="btn btn-warning fw-semibold shadow-sm">
                                    <i class="ri-add-line me-1"></i> Tambah Project
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Filter Card ── --}}
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <form id="filterForm" method="GET" action="{{ route('utility.project.data') }}"
                                class="row g-2 align-items-center">
                                {{-- Search --}}
                                <div class="col-lg-2 col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="ri-search-line"></i></span>
                                        <input type="text" class="form-control" id="filterSearch" name="search"
                                            placeholder="Cari nama project, nomor, catatan..." value="{{ $search }}">
                                    </div>
                                </div>

                                {{-- Fase Filter (Dropdown Multi-Select) --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <div class="dropdown custom-filter-dropdown" data-filter="fase">
                                        <button
                                            class="btn btn-outline-light text-dark border w-100 d-flex justify-content-between align-items-center text-truncate dropdown-toggle shadow-none py-2 px-2 filter-btn"
                                            type="button" id="dropdownFaseBtn" data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside" aria-expanded="false">
                                            <span class="filter-btn-text text-truncate me-1">
                                                <i class="ri-git-commit-line text-muted me-1"></i><span
                                                    class="label-text">Semua Fase</span>
                                            </span>
                                            <span class="badge bg-primary rounded-pill filter-count-badge d-none">0</span>
                                        </button>
                                        <div class="dropdown-menu p-2 shadow-lg border-0 rounded-3"
                                            style="min-width: 250px;" aria-labelledby="dropdownFaseBtn">
                                            <div
                                                class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                                <small class="fw-bold text-muted text-uppercase fs-11">Filter Fase</small>
                                                <div class="d-flex gap-2">
                                                    <a href="javascript:void(0)"
                                                        class="fs-11 text-primary text-decoration-none fw-medium btn-select-all"
                                                        data-target="fase">Pilih Semua</a>
                                                    <span class="text-muted fs-11">|</span>
                                                    <a href="javascript:void(0)"
                                                        class="fs-11 text-danger text-decoration-none fw-medium btn-clear-filter"
                                                        data-target="fase">Reset</a>
                                                </div>
                                            </div>
                                            <div class="filter-options-list">
                                                @php
                                                    $faseListConfig = [
                                                        'Fase 1' => [
                                                            'label' => 'Fase 1: Konsep Project',
                                                            'badge' =>
                                                                'bg-info-subtle text-info border border-info-subtle',
                                                            'icon' => 'ri-lightbulb-line',
                                                        ],
                                                        'Fase 2' => [
                                                            'label' => 'Fase 2: Administrasi Project (IO/ PR/ PO)',
                                                            'badge' =>
                                                                'bg-warning-subtle text-warning border border-warning-subtle',
                                                            'icon' => 'ri-file-list-3-line',
                                                        ],
                                                        'Fase 3' => [
                                                            'label' => 'Fase 3: Proses Pekerjaan Project',
                                                            'badge' =>
                                                                'bg-primary-subtle text-primary border border-primary-subtle',
                                                            'icon' => 'ri-tools-line',
                                                        ],
                                                    ];
                                                @endphp
                                                @foreach ($faseListConfig as $fKey => $fCfg)
                                                    <label
                                                        class="dropdown-item px-2 py-1 rounded-2 d-flex align-items-center justify-content-between cursor-pointer mb-0 option-item">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <input
                                                                class="form-check-input mt-0 filter-checkbox filter-checkbox-fase"
                                                                type="checkbox" value="{{ $fKey }}"
                                                                {{ in_array($fKey, $faseList ?? []) ? 'checked' : '' }}>
                                                            <span class="fs-12 text-dark">{{ $fKey }}</span>
                                                        </div>
                                                        <span class="badge {{ $fCfg['badge'] }} fs-10 px-2 py-0"><i
                                                                class="{{ $fCfg['icon'] }} me-1"></i>{{ $fKey }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Area Filter (Dropdown Multi-Select) --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <div class="dropdown custom-filter-dropdown" data-filter="area">
                                        <button
                                            class="btn btn-outline-light text-dark border w-100 d-flex justify-content-between align-items-center text-truncate dropdown-toggle shadow-none py-2 px-2 filter-btn"
                                            type="button" id="dropdownAreaBtn" data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside" aria-expanded="false">
                                            <span class="filter-btn-text text-truncate me-1">
                                                <i class="ri-map-pin-line text-muted me-1"></i><span
                                                    class="label-text">Area</span>
                                            </span>
                                            <span class="badge bg-primary rounded-pill filter-count-badge d-none">0</span>
                                        </button>
                                        <div class="dropdown-menu p-2 shadow-lg border-0 rounded-3"
                                            style="min-width: 230px;" aria-labelledby="dropdownAreaBtn">
                                            <div
                                                class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                                <small class="fw-bold text-muted text-uppercase fs-11">Filter Area</small>
                                                <div class="d-flex gap-2">
                                                    <a href="javascript:void(0)"
                                                        class="fs-11 text-primary text-decoration-none fw-medium btn-select-all"
                                                        data-target="area">Pilih Semua</a>
                                                    <span class="text-muted fs-11">|</span>
                                                    <a href="javascript:void(0)"
                                                        class="fs-11 text-danger text-decoration-none fw-medium btn-clear-filter"
                                                        data-target="area">Reset</a>
                                                </div>
                                            </div>
                                            <div class="filter-options-list" style="max-height: 220px; overflow-y: auto;">
                                                @foreach ($areasList as $ar)
                                                    <label
                                                        class="dropdown-item px-2 py-1 rounded-2 d-flex align-items-center gap-2 cursor-pointer mb-0 option-item">
                                                        <input
                                                            class="form-check-input mt-0 filter-checkbox filter-checkbox-area"
                                                            type="checkbox" value="{{ $ar }}"
                                                            {{ in_array($ar, $areaList ?? []) ? 'checked' : '' }}>
                                                        <span class="fs-12 text-dark">{{ $ar }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Kategori Filter (Dropdown Multi-Select) --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <div class="dropdown custom-filter-dropdown" data-filter="kategori">
                                        <button
                                            class="btn btn-outline-light text-dark border w-100 d-flex justify-content-between align-items-center text-truncate dropdown-toggle shadow-none py-2 px-2 filter-btn"
                                            type="button" id="dropdownKategoriBtn" data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside" aria-expanded="false">
                                            <span class="filter-btn-text text-truncate me-1">
                                                <i class="ri-price-tag-3-line text-muted me-1"></i><span
                                                    class="label-text">Kategori</span>
                                            </span>
                                            <span class="badge bg-primary rounded-pill filter-count-badge d-none">0</span>
                                        </button>
                                        <div class="dropdown-menu p-2 shadow-lg border-0 rounded-3"
                                            style="min-width: 250px;" aria-labelledby="dropdownKategoriBtn">
                                            <div
                                                class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                                <small class="fw-bold text-muted text-uppercase fs-11">Filter
                                                    Kategori</small>
                                                <div class="d-flex gap-2">
                                                    <a href="javascript:void(0)"
                                                        class="fs-11 text-primary text-decoration-none fw-medium btn-select-all"
                                                        data-target="kategori">Pilih Semua</a>
                                                    <span class="text-muted fs-11">|</span>
                                                    <a href="javascript:void(0)"
                                                        class="fs-11 text-danger text-decoration-none fw-medium btn-clear-filter"
                                                        data-target="kategori">Reset</a>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light border-end-0 py-1"><i
                                                            class="ri-search-line fs-11"></i></span>
                                                    <input type="text"
                                                        class="form-control form-control-sm border-start-0 filter-search-input"
                                                        placeholder="Cari kategori..." data-filter="kategori"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="filter-options-list" style="max-height: 200px; overflow-y: auto;">
                                                @foreach ($categoriesList as $cat)
                                                    <label
                                                        class="dropdown-item px-2 py-1 rounded-2 gap-2 cursor-pointer mb-0 option-item">
                                                        <input
                                                            class="form-check-input mt-0 filter-checkbox filter-checkbox-kategori"
                                                            type="checkbox" value="{{ $cat }}"
                                                            {{ in_array($cat, $kategoriList ?? []) ? 'checked' : '' }}>
                                                        <span
                                                            class="fs-12 text-dark text-truncate">{{ $cat }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Priority Filter (Dropdown Multi-Select) --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <div class="dropdown custom-filter-dropdown" data-filter="priority">
                                        <button
                                            class="btn btn-outline-light text-dark border w-100 d-flex justify-content-between align-items-center text-truncate dropdown-toggle shadow-none py-2 px-2 filter-btn"
                                            type="button" id="dropdownPriorityBtn" data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside" aria-expanded="false">
                                            <span class="filter-btn-text text-truncate me-1">
                                                <i class="ri-flag-line text-muted me-1"></i><span
                                                    class="label-text">Prioritas</span>
                                            </span>
                                            <span class="badge bg-primary rounded-pill filter-count-badge d-none">0</span>
                                        </button>
                                        <div class="dropdown-menu p-2 shadow-lg border-0 rounded-3"
                                            style="min-width: 220px;" aria-labelledby="dropdownPriorityBtn">
                                            <div
                                                class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                                <small class="fw-bold text-muted text-uppercase fs-11">Filter
                                                    Prioritas</small>
                                                <div class="d-flex gap-2">
                                                    <a href="javascript:void(0)"
                                                        class="fs-11 text-primary text-decoration-none fw-medium btn-select-all"
                                                        data-target="priority">Pilih Semua</a>
                                                    <span class="text-muted fs-11">|</span>
                                                    <a href="javascript:void(0)"
                                                        class="fs-11 text-danger text-decoration-none fw-medium btn-clear-filter"
                                                        data-target="priority">Reset</a>
                                                </div>
                                            </div>
                                            <div class="filter-options-list">
                                                @php
                                                    $priorities = [
                                                        'Critical' =>
                                                            'bg-danger-subtle text-danger border border-danger-subtle',
                                                        'High' =>
                                                            'bg-warning-subtle text-warning border border-warning-subtle',
                                                        'Medium' =>
                                                            'bg-info-subtle text-info border border-info-subtle',
                                                        'Low' =>
                                                            'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                                    ];
                                                @endphp
                                                @foreach ($priorities as $prKey => $prClass)
                                                    <label
                                                        class="dropdown-item px-2 py-1 rounded-2 d-flex align-items-center justify-content-between cursor-pointer mb-0 option-item">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <input
                                                                class="form-check-input mt-0 filter-checkbox filter-checkbox-priority"
                                                                type="checkbox" value="{{ $prKey }}"
                                                                {{ in_array($prKey, $priorityList ?? []) ? 'checked' : '' }}>
                                                            <span class="fs-12 text-dark">{{ $prKey }}</span>
                                                        </div>
                                                        <span
                                                            class="badge {{ $prClass }} fs-10 px-2 py-0">{{ $prKey }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Status Filter (Dropdown Multi-Select) --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <div class="dropdown custom-filter-dropdown" data-filter="status">
                                        <button
                                            class="btn btn-outline-light text-dark border w-100 d-flex justify-content-between align-items-center text-truncate dropdown-toggle shadow-none py-2 px-2 filter-btn"
                                            type="button" id="dropdownStatusBtn" data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside" aria-expanded="false">
                                            <span class="filter-btn-text text-truncate me-1">
                                                <i class="ri-checkbox-circle-line text-muted me-1"></i><span
                                                    class="label-text">Status</span>
                                            </span>
                                            <span class="badge bg-primary rounded-pill filter-count-badge d-none">0</span>
                                        </button>
                                        <div class="dropdown-menu p-2 shadow-lg border-0 rounded-3"
                                            style="min-width: 220px;" aria-labelledby="dropdownStatusBtn">
                                            <div
                                                class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                                <small class="fw-bold text-muted text-uppercase fs-11">Filter
                                                    Status</small>
                                                <div class="d-flex gap-2">
                                                    <a href="javascript:void(0)"
                                                        class="fs-11 text-primary text-decoration-none fw-medium btn-select-all"
                                                        data-target="status">Pilih Semua</a>
                                                    <span class="text-muted fs-11">|</span>
                                                    <a href="javascript:void(0)"
                                                        class="fs-11 text-danger text-decoration-none fw-medium btn-clear-filter"
                                                        data-target="status">Reset</a>
                                                </div>
                                            </div>
                                            <div class="filter-options-list">
                                                @php
                                                    $statuses = [
                                                        'Selesai' => [
                                                            'class' =>
                                                                'bg-success-subtle text-success border border-success-subtle',
                                                            'icon' => 'ri-checkbox-circle-line',
                                                        ],
                                                        'On Progress' => [
                                                            'class' =>
                                                                'bg-warning-subtle text-warning border border-warning-subtle',
                                                            'icon' => 'ri-time-line',
                                                        ],
                                                        'Terlambat' => [
                                                            'class' =>
                                                                'bg-danger-subtle text-danger border border-danger-subtle',
                                                            'icon' => 'ri-alert-line',
                                                        ],
                                                    ];
                                                @endphp
                                                @foreach ($statuses as $stKey => $stMeta)
                                                    <label
                                                        class="dropdown-item px-2 py-1 rounded-2 d-flex align-items-center justify-content-between cursor-pointer mb-0 option-item">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <input
                                                                class="form-check-input mt-0 filter-checkbox filter-checkbox-status"
                                                                type="checkbox" value="{{ $stKey }}"
                                                                {{ in_array($stKey, $statusList ?? []) ? 'checked' : '' }}>
                                                            <span class="fs-12 text-dark">{{ $stKey }}</span>
                                                        </div>
                                                        <span class="badge {{ $stMeta['class'] }} fs-10 px-2 py-0"><i
                                                                class="{{ $stMeta['icon'] }} me-1"></i>{{ $stKey }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Buttons --}}
                                <div class="col-lg-1 col-md-3 col-sm-6 d-flex gap-1">
                                    <button type="button" id="btnResetFilter"
                                        class="btn btn-outline-danger w-100 py-2 filter-btn"
                                        title="Reset semua filter">
                                        <i class="ri-refresh-line"></i>
                                    </button>
                                </div>
                            </form>

                            {{-- Active Filter Badges Row --}}
                            <div id="activeFiltersRow"
                                class="d-flex flex-wrap align-items-center gap-1 mt-2 pt-2 border-top d-none">
                                <span class="fs-12 text-muted me-1"><i class="ri-filter-3-line me-1"></i>Filter
                                    Aktif:</span>
                                <div id="activeFilterBadges" class="d-flex flex-wrap gap-1 align-items-center"></div>
                                <button type="button"
                                    class="btn btn-link btn-sm text-danger text-decoration-none p-0 ms-2 fs-12 fw-semibold"
                                    id="btnClearAllActive">
                                    <i class="ri-close-circle-line me-1"></i>Hapus Semua
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Data Table Card ── --}}
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h6 class="card-title mb-0 fw-bold text-dark">
                                        <i class="ri-table-line text-primary me-2"></i>Daftar Project
                                    </h6>
                                    <small class="text-muted">Menampilkan <strong id="displayCount">-</strong> dari
                                        total {{ $totalProjectsCount }} project</small>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="d-flex gap-1 flex-wrap">
                                        <span id="badgeCountSelesai"
                                            class="badge bg-success-subtle text-success border border-success-subtle">
                                            Selesai: -
                                        </span>
                                        <span id="badgeCountOnProgress"
                                            class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                            On Progress: -
                                        </span>
                                        <span id="badgeCountTerlambat"
                                            class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            Terlambat: -
                                        </span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-success fw-semibold btn-export-excel shadow-sm"
                                        title="Download data project ke Excel">
                                        <i class="ri-file-excel-line me-1"></i> Export Excel
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle table-nowrap mb-0" id="projectTable">
                                    <thead class="table-light fs-12 text-muted">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">No</th>
                                            <th style="min-width: 230px;" class="sortable-th" data-sort="nama_project"
                                                title="Klik untuk mengurutkan Nama Project">
                                                <div class="d-flex align-items-center justify-content-between gap-1">
                                                    <span>Nama Project & Target</span>
                                                    <i class="ri-arrow-up-down-line sort-icon"></i>
                                                </div>
                                            </th>
                                            <th style="width: 85px;" class="text-center sortable-th" data-sort="area"
                                                title="Klik untuk mengurutkan Area">
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <span>Area</span>
                                                    <i class="ri-arrow-up-down-line sort-icon"></i>
                                                </div>
                                            </th>
                                            <th style="width: 95px;" class="text-center sortable-th" data-sort="priority"
                                                title="Klik untuk mengurutkan Prioritas">
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <span>Prioritas</span>
                                                    <i class="ri-arrow-up-down-line sort-icon"></i>
                                                </div>
                                            </th>
                                            <th style="min-width: 120px;" class="sortable-th" data-sort="kategori"
                                                title="Klik untuk mengurutkan Kategori">
                                                <div class="d-flex align-items-center justify-content-between gap-1">
                                                    <span>Kategori</span>
                                                    <i class="ri-arrow-up-down-line sort-icon"></i>
                                                </div>
                                            </th>
                                            <th style="width: 100px;" class="text-center sortable-th" data-sort="fase"
                                                title="Klik untuk mengurutkan Fase">
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <span>Fase</span>
                                                    <i class="ri-arrow-up-down-line sort-icon"></i>
                                                </div>
                                            </th>
                                            <th style="min-width: 130px;" class="sortable-th" data-sort="finish_date"
                                                title="Klik untuk mengurutkan Timeline Selesai">
                                                <div class="d-flex align-items-center justify-content-between gap-1">
                                                    <span>Timeline</span>
                                                    <i class="ri-arrow-up-down-line sort-icon"></i>
                                                </div>
                                            </th>
                                            <th style="min-width: 130px;" class="sortable-th" data-sort="progress"
                                                title="Klik untuk mengurutkan Progress">
                                                <div class="d-flex align-items-center justify-content-between gap-1">
                                                    <span>Progress</span>
                                                    <i class="ri-arrow-up-down-line sort-icon"></i>
                                                </div>
                                            </th>
                                            <th style="width: 100px;" class="text-center sortable-th" data-sort="status"
                                                title="Klik untuk mengurutkan Status">
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <span>Status</span>
                                                    <i class="ri-arrow-up-down-line sort-icon"></i>
                                                </div>
                                            </th>
                                            <th style="min-width: 200px;">Catatan Terakhir</th>
                                            <th style="width: 130px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="projectTableBody">
                                        <tr>
                                            <td colspan="11" class="text-center py-5 text-muted">
                                                <div class="spinner-border text-primary spinner-border-sm me-2"
                                                    role="status"></div>
                                                Memuat data project...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer border-top py-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="fs-12 text-muted mb-0">Tampilkan:</label>
                                    <select class="form-select form-select-sm w-auto shadow-none" id="perPageSelect">
                                        <option value="10">10</option>
                                        <option value="25" selected>25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <span class="fs-12 text-muted" id="paginationInfo">Menampilkan 0 - 0 dari 0
                                        project</span>
                                </div>
                                <nav aria-label="Navigasi Halaman Project">
                                    <ul class="pagination pagination-sm mb-0 justify-content-end" id="paginationControls">
                                        <!-- Diisi via JavaScript -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Modal 1: Update Progress ── --}}
    <div class="modal fade" id="modalUpdateProgress" tabindex="-1" aria-labelledby="modalUpdateProgressLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="modalUpdateProgressLabel">
                        <i class="ri-edit-2-line text-primary me-2"></i>Update Progress Project
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formUpdateProgress">
                    @csrf
                    <input type="hidden" id="modal_project_id" name="project_id">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-muted fs-12 mb-1">Project</label>
                            <div id="modal_project_nama" class="fw-semibold text-dark fs-14 p-2 bg-light rounded border">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold text-dark mb-0">Progress Persentase (%)</label>
                                <span id="modal_slider_val" class="badge bg-primary fs-13">0%</span>
                            </div>
                            <input type="range" class="form-range" id="modal_progress_range" min="0"
                                max="100" step="1" value="0">
                            <div class="d-flex align-items-center justify-content-between mt-2 gap-2">
                                <div class="input-group input-group-sm" style="max-width: 180px;">
                                    <span class="input-group-text bg-light">Input Angka</span>
                                    <input type="number" class="form-control text-center fw-bold"
                                        id="modal_progress_input" name="progress" min="0" max="100"
                                        value="0" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small id="modal_progress_min_text" class="text-muted fs-11">
                                    <i class="ri-lock-line me-1 text-warning"></i>Minimal: <strong
                                        id="modal_min_val">0%</strong> (tidak boleh mundur)
                                </small>
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
                                <select class="form-select" id="modal_fase" name="fase">
                                    <option value="">-- Tetap (Tidak Diubah) --</option>
                                    @foreach ($faseOptions ?? [] as $fKey => $fLabel)
                                        <option value="{{ $fKey }}">{{ $fLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3" id="modal_prev_note_box" style="display: none;">
                            <label class="form-label text-muted fs-12 mb-1">
                                <i class="ri-chat-history-line text-info me-1"></i>Catatan Terakhir Sebelumnya
                            </label>
                            <div id="modal_prev_note_text"
                                class="p-2 bg-light rounded border text-muted fs-12 fst-italic"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Catatan Progress / Hasil <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="modal_catatan" name="catatan" rows="3"
                                placeholder="Tuliskan perkembangan pekerjaan terbaru, kendala, atau hasil yang sudah dicapai..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitProgress">
                            <i class="ri-save-line me-1"></i> Simpan Progress
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Modal 2: Riwayat History Progress ── --}}
    <div class="modal fade" id="modalHistory" tabindex="-1" aria-labelledby="modalHistoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="modalHistoryLabel">
                            <i class="ri-history-line text-info me-2"></i>Riwayat Progress Project
                        </h5>
                        <small id="history_project_title" class="text-muted"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 500px; overflow-y: auto;">
                    <div id="history_loading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted mt-2 fs-13">Memuat riwayat...</p>
                    </div>
                    <div id="history_container" class="d-none">
                        {{-- Dinamis diisi via JavaScript --}}
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Modal 3: Import & Migrasi Data Excel ── --}}
    <div class="modal fade" id="modalImportExcel" tabindex="-1" aria-labelledby="modalImportExcelLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalImportExcelLabel">
                            <i class="ri-file-excel-2-line text-success me-2"></i>Upload & Migrasi Data Project (Excel)
                        </h5>
                        <small class="text-muted">Import langsung daftar project yang sudah berjalan ke dalam
                            sistem</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formImportExcel" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        {{-- ── Template Download Banner ── --}}
                        <div class="card border border-primary-subtle bg-primary-subtle bg-opacity-10 mb-3 rounded-3">
                            <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fs-20 flex-shrink-0"
                                        style="width: 42px; height: 42px;">
                                        <i class="ri-file-download-line"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">Download Template Standar Migrasi</h6>
                                        <p class="text-muted mb-0 fs-12">Gunakan format Excel standar dengan contoh data
                                            dan sheet referensi master.</p>
                                    </div>
                                </div>
                                <a href="{{ route('utility.project.template') }}"
                                    class="btn btn-primary btn-sm fw-semibold shadow-sm text-nowrap">
                                    <i class="ri-download-2-line me-1"></i> Download Template (.xlsx)
                                </a>
                            </div>
                        </div>

                        {{-- ── Petunjuk Format Kolom ── --}}
                        <div class="card border border-light-subtle bg-light mb-3 rounded-3">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-dark fs-13 mb-2 d-flex align-items-center">
                                    <i class="ri-information-line text-info me-1"></i> Ketentuan Pengisian Kolom Excel:
                                </h6>
                                <ul class="text-muted fs-12 mb-0 ps-3">
                                    <li class="mb-1"><strong>Nama Project:</strong> Wajib diisi. Jika nama project sudah
                                        ada di sistem, data akan diperbarui (update).</li>
                                    <li class="mb-1"><strong>Area:</strong> Wajib diisi sesuai salah satu dari:
                                        <code>Boiler</code>, <code>Utility</code>, <code>WWTP</code>, <code>MTC</code>.
                                    </li>
                                    <li class="mb-1"><strong>Kategori:</strong> Maintenance, Baku Mutu, Compliance, 5R,
                                        KPI BB dan Steam, Digitalisasi, Improvement, dll.</li>
                                    <li class="mb-1"><strong>Prioritas:</strong> <code>Critical</code>,
                                        <code>High</code>, <code>Medium</code>, <code>Low</code>. Jika kosong, otomatis
                                        diatur ke <code>Medium</code>.
                                    </li>
                                    <li class="mb-1"><strong>Nomor Project:</strong> Opsional. Jika kosong, sistem
                                        otomatis membuat nomor urut (contoh: <code>PRJ-BOI-001</code>).</li>
                                    <li class="mb-1"><strong>Tanggal & Minggu:</strong> Jika tanggal mulai/selesai kosong
                                        tetapi kolom Minggu terisi (contoh: <code>W1 Okt 26</code>, <code>W3 Nov 26</code>),
                                        sistem otomatis mengambil tanggal awal/akhir minggu tersebut.</li>
                                    <li><strong>Status & Progress:</strong> Progress angka <code>0 - 100</code>. Jika status
                                        kosong, otomatis dihitung dari persentase (100% = <code>Selesai</code>) dan batas
                                        minggu selesai (<code>Terlambat</code> jika sudah lewat waktu saat ini, atau
                                        <code>On Progress</code> jika masih berjalan).
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- ── File Upload Input ── --}}
                        <div class="mb-3">
                            <label for="import_excel_file" class="form-label fw-bold text-dark">
                                Pilih File Excel (.xlsx / .xls / .csv) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="import_excel_file" name="file"
                                    accept=".xlsx, .xls, .csv" required>
                                <button class="btn btn-outline-secondary" type="button" id="btnClearFile"
                                    title="Reset file">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                            <div class="form-text fs-12 text-muted">
                                Maksimal ukuran file 10 MB. Mendukung file Excel (.xlsx, .xls) maupun CSV.
                            </div>
                        </div>

                        {{-- ── Upload Progress State ── --}}
                        <div id="importLoadingState" class="d-none text-center py-3">
                            <div class="spinner-border text-primary spinner-border-sm mb-2" role="status"></div>
                            <div class="fs-13 fw-semibold text-dark">Sedang memproses dan menyimpan data migrasi...</div>
                            <small class="text-muted">Mohon tunggu, proses parsing spreadsheet sedang berjalan.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success fw-semibold" id="btnSubmitImport">
                            <i class="ri-upload-cloud-2-line me-1"></i> Mulai Upload & Migrasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // ── Setup AJAX CSRF Token ──
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            let currentPage = 1;
            let currentPerPage = 25;
            let currentSortBy = 'created_at';
            let currentSortDir = 'desc';

            function escapeHtml(str) {
                if (!str) return '';
                return $('<div>').text(str).html();
            }

            function getFaseBadge(fase) {
                if (fase === 'Fase 2') {
                    return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle fw-semibold" title="Fase 2: Administrasi Project (IO/ PR/ PO)"><i class="ri-file-list-3-line me-1"></i>Fase 2</span>';
                } else if (fase === 'Fase 3') {
                    return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold" title="Fase 3: Proses Pekerjaan Project"><i class="ri-tools-line me-1"></i>Fase 3</span>';
                }
                return '<span class="badge bg-info-subtle text-info border border-info-subtle fw-semibold" title="Fase 1: Konsep Project"><i class="ri-lightbulb-line me-1"></i>Fase 1</span>';
            }

            function getAreaBadge(area) {
                if (area === 'Boiler')
                    return '<span class="badge bg-danger-subtle text-danger fw-semibold">Boiler</span>';
                if (area === 'Utility')
                    return '<span class="badge bg-primary-subtle text-primary fw-semibold">Utility</span>';
                if (area === 'WWTP') return '<span class="badge bg-info-subtle text-info fw-semibold">WWTP</span>';
                return `<span class="badge bg-secondary-subtle text-secondary fw-semibold">${escapeHtml(area || 'MTC')}</span>`;
            }

            function getPriorityBadge(priority) {
                if (priority === 'Critical') {
                    return '<span class="badge bg-danger text-white"><i class="ri-alarm-warning-fill me-1"></i>Critical</span>';
                } else if (priority === 'High') {
                    return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle fw-semibold"><i class="ri-arrow-up-circle-fill me-1"></i>High</span>';
                } else if (priority === 'Low') {
                    return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-semibold"><i class="ri-arrow-down-circle-line me-1"></i>Low</span>';
                }
                return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold"><i class="ri-equal-circle-fill me-1"></i>Medium</span>';
            }

            function getStatusBadge(status) {
                if (status === 'Selesai') {
                    return '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="ri-checkbox-circle-line me-1"></i>Selesai</span>';
                }
                if (status === 'Terlambat') {
                    return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="ri-alert-line me-1"></i>Terlambat</span>';
                }
                return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="ri-time-line me-1"></i>On Progress</span>';
            }

            function updateStatsBadges(stats) {
                if (!stats) return;
                $('#displayCount').text(stats.filtered);
                $('#badgeCountSelesai').text(`Selesai: ${stats.selesai}`);
                $('#badgeCountOnProgress').text(`On Progress: ${stats.on_progress}`);
                $('#badgeCountTerlambat').text(`Terlambat: ${stats.terlambat}`);
            }

            function renderPagination(meta) {
                const $controls = $('#paginationControls');
                $controls.empty();

                if (!meta || meta.total === 0) {
                    $('#paginationInfo').text('Menampilkan 0 project');
                    return;
                }

                $('#paginationInfo').text(`Menampilkan ${meta.from} - ${meta.to} dari ${meta.total} project`);

                if (meta.last_page <= 1) {
                    return;
                }

                const current = meta.current_page;
                const last = meta.last_page;

                // Tombol Previous
                const prevDisabled = current === 1 ? 'disabled' : '';
                $controls.append(`
                    <li class="page-item ${prevDisabled}">
                        <a class="page-link py-1 px-2 cursor-pointer btn-page-nav" data-page="${current - 1}" aria-label="Previous">
                            <i class="ri-arrow-left-s-line"></i>
                        </a>
                    </li>
                `);

                // Windowing nomor halaman
                let startPage = Math.max(1, current - 2);
                let endPage = Math.min(last, current + 2);

                if (startPage > 1) {
                    $controls.append(`
                        <li class="page-item ${current === 1 ? 'active' : ''}">
                            <a class="page-link py-1 px-2 cursor-pointer btn-page-nav" data-page="1">1</a>
                        </li>
                    `);
                    if (startPage > 2) {
                        $controls.append(
                            '<li class="page-item disabled"><span class="page-link py-1 px-2">&hellip;</span></li>'
                        );
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    const activeClass = i === current ? 'active' : '';
                    $controls.append(`
                        <li class="page-item ${activeClass}">
                            <a class="page-link py-1 px-2 cursor-pointer btn-page-nav" data-page="${i}">${i}</a>
                        </li>
                    `);
                }

                if (endPage < last) {
                    if (endPage < last - 1) {
                        $controls.append(
                            '<li class="page-item disabled"><span class="page-link py-1 px-2">&hellip;</span></li>'
                        );
                    }
                    $controls.append(`
                        <li class="page-item ${current === last ? 'active' : ''}">
                            <a class="page-link py-1 px-2 cursor-pointer btn-page-nav" data-page="${last}">${last}</a>
                        </li>
                    `);
                }

                // Tombol Next
                const nextDisabled = current === last ? 'disabled' : '';
                $controls.append(`
                    <li class="page-item ${nextDisabled}">
                        <a class="page-link py-1 px-2 cursor-pointer btn-page-nav" data-page="${current + 1}" aria-label="Next">
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </li>
                `);
            }

            // ── Multi-Select Filter Helpers ──
            const defaultFilterLabels = {
                fase: 'Semua Fase',
                area: 'Semua Area',
                kategori: 'Semua Kategori',
                status: 'Semua Status',
                priority: 'Semua Prioritas'
            };

            // ── LocalStorage Filter Persistence Helpers ──
            const FILTER_STORAGE_KEY = 'utility_project_filters';

            function saveFiltersToStorage() {
                try {
                    const state = {
                        search: $('#filterSearch').val() || '',
                        fase: getSelectedFilterValues('fase'),
                        area: getSelectedFilterValues('area'),
                        kategori: getSelectedFilterValues('kategori'),
                        status: getSelectedFilterValues('status'),
                        priority: getSelectedFilterValues('priority'),
                        sort_by: currentSortBy,
                        sort_dir: currentSortDir
                    };
                    localStorage.setItem(FILTER_STORAGE_KEY, JSON.stringify(state));
                } catch (e) {
                    console.warn('Gagal menyimpan filter ke localStorage:', e);
                }
            }

            function restoreFiltersFromStorage() {
                try {
                    const raw = localStorage.getItem(FILTER_STORAGE_KEY);
                    if (!raw) return false;
                    const state = JSON.parse(raw);
                    if (!state) return false;

                    if (typeof state.search === 'string' && state.search.length > 0) {
                        $('#filterSearch').val(state.search);
                    }

                    if (state.sort_by) {
                        currentSortBy = state.sort_by;
                        currentSortDir = state.sort_dir || 'asc';
                    }

                    ['fase', 'area', 'kategori', 'status', 'priority'].forEach(function(f) {
                        const vals = state[f];
                        if (Array.isArray(vals) && vals.length > 0) {
                            $(`.filter-checkbox-${f}`).each(function() {
                                const isChecked = vals.includes($(this).val());
                                $(this).prop('checked', isChecked);
                            });
                        }
                    });
                    return true;
                } catch (e) {
                    console.warn('Gagal membaca filter dari localStorage:', e);
                    return false;
                }
            }

            function clearFiltersStorage() {
                try {
                    localStorage.removeItem(FILTER_STORAGE_KEY);
                } catch (e) {}
            }

            function getSelectedFilterValues(filterName) {
                const vals = [];
                $(`.filter-checkbox-${filterName}:checked`).each(function() {
                    vals.push($(this).val());
                });
                return vals;
            }

            function updateDropdownLabels() {
                const filters = ['fase', 'area', 'kategori', 'status', 'priority'];
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
                        $label.text(vals[0]);
                        $badge.removeClass('d-none').text('1');
                        $btn.addClass('is-active');
                    } else {
                        const titleName = f === 'priority' ? 'Prioritas' : (f === 'fase' ? 'Fase' : f
                            .charAt(0).toUpperCase() + f.slice(1));
                        $label.text(`${titleName}`);
                        $badge.removeClass('d-none').text(vals.length);
                        $btn.addClass('is-active');
                    }
                });
            }

            function updateActiveFilterBadges() {
                const $badgesContainer = $('#activeFilterBadges');
                $badgesContainer.empty();
                let totalActive = 0;

                const filters = ['fase', 'area', 'kategori', 'status', 'priority'];
                filters.forEach(function(f) {
                    const titleName = f === 'priority' ? 'Prioritas' : (f === 'fase' ? 'Fase' : f.charAt(0)
                        .toUpperCase() + f.slice(1));
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

            // ── Load Data Project via Async jQuery AJAX with Custom Pagination ──
            function loadProjectsData(page = 1) {
                currentPage = page;
                const filters = {
                    format: 'json',
                    page: currentPage,
                    per_page: currentPerPage,
                    sort_by: currentSortBy,
                    sort_dir: currentSortDir,
                    search: $('#filterSearch').val(),
                    fase: getSelectedFilterValues('fase'),
                    area: getSelectedFilterValues('area'),
                    kategori: getSelectedFilterValues('kategori'),
                    priority: getSelectedFilterValues('priority'),
                    status: getSelectedFilterValues('status')
                };

                $('#projectTableBody').html(`
                    <tr>
                        <td colspan="11" class="text-center py-5 text-muted">
                            <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                            Memuat data project...
                        </td>
                    </tr>
                `);

                $.ajax({
                    url: '{{ route('utility.project.data') }}',
                    type: 'GET',
                    data: filters,
                    dataType: 'json',
                    success: function(res) {
                        if (!res.success || !res.data || res.data.length === 0) {
                            $('#projectTableBody').html(`
                                <tr>
                                    <td colspan="11" class="text-center py-5 text-muted">
                                        <i class="ri-folder-open-line fs-36 text-secondary d-block mb-2"></i>
                                        Tidak ada data project yang sesuai dengan filter pencarian.
                                    </td>
                                </tr>
                            `);
                            updateStatsBadges(res.stats);
                            renderPagination(res.pagination || {
                                total: 0
                            });
                            return;
                        }

                        let html = '';
                        $.each(res.data, function(idx, item) {
                            const targetHtml = item.target_result ? `
                                <div class="text-muted fs-11 text-truncate mt-1" style="max-width: 320px;" title="${escapeHtml(item.target_result)}">
                                    <i class="ri-focus-2-line text-primary me-1"></i>${escapeHtml(item.target_result)}
                                </div>` : '';

                            const startStr = item.start_date || (item.start_week || '-');
                            const finishStr = item.finish_date || (item.finish_week || '-');
                            const finishClass = item.status === 'Terlambat' ? 'text-danger' :
                                'text-dark';
                            const progColor = (item.progress == 100) ? 'bg-success' : (item
                                .status === 'Terlambat' ? 'bg-danger' : 'bg-primary');

                            html += `
                                <tr id="row-project-${item.id}">
                                    <td class="text-center text-muted fw-semibold">${item.no || (idx + 1)}</td>
                                    <td>
                                        <div class="fw-bold text-dark fs-13">${escapeHtml(item.nama_project)}</div>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <span class="badge bg-light text-muted border fs-11">${escapeHtml(item.nomor_project)}</span>
                                        </div>
                                        ${targetHtml}
                                    </td>
                                    <td class="text-center">
                                        ${getAreaBadge(item.area)}
                                    </td>
                                    <td class="text-center">
                                        ${getPriorityBadge(item.priority)}
                                    </td>
                                    <td>
                                        <span class="text-dark fs-12 fw-medium">${escapeHtml(item.kategori)}</span>
                                    </td>
                                    <td class="text-center fase-col-${item.id}">
                                        ${getFaseBadge(item.fase)}
                                    </td>
                                    <td class="fs-11">
                                        <div>
                                            <span class="text-muted">Start:</span>
                                            <strong>${escapeHtml(startStr)}</strong>
                                        </div>
                                        <div class="mt-1">
                                            <span class="text-muted">Finish:</span>
                                            <strong class="${finishClass}">
                                                ${escapeHtml(finishStr)}
                                            </strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 7px;">
                                                <div class="progress-bar progress-bar-${item.id} ${progColor}" role="progressbar" style="width: ${item.progress}%;"></div>
                                            </div>
                                            <span class="fs-12 fw-bold text-dark progress-text-${item.id}">${item.progress}%</span>
                                        </div>
                                    </td>
                                    <td class="text-center status-col-${item.id}">
                                        ${getStatusBadge(item.status)}
                                    </td>
                                    <td>
                                        ${item.latest_note ? `
                                                                                                    <div class="note-text-${item.id} fs-12 text-dark text-truncate" style="max-width: 230px;" title="${escapeHtml(item.latest_note)}">
                                                                                                        <i class="ri-chat-1-line text-primary me-1"></i>${escapeHtml(item.latest_note)}
                                                                                                    </div>
                                                                                                ` : `
                                                                                                    <div class="note-text-${item.id} fs-12 text-muted fst-italic" style="max-width: 230px;">
                                                                                                        -
                                                                                                    </div>
                                                                                                `}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-update-progress"
                                            data-id="${item.id}"
                                            data-nama="${escapeHtml(item.nama_project)}"
                                            data-progress="${item.progress}"
                                            data-fase="${escapeHtml(item.fase || '')}"
                                            data-note="${escapeHtml(item.latest_note || '')}" title="Update Progress">
                                            <i class="ri-edit-circle-line"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info btn-view-history"
                                            data-id="${item.id}"
                                            data-nama="${escapeHtml(item.nama_project)}"
                                            title="Lihat Riwayat Update">
                                            <i class="ri-history-line"></i>
                                        </button>
                                        <a href="${item.edit_url}" class="btn btn-sm btn-outline-secondary" title="Edit Data">
                                            <i class="ri-pencil-line"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-project"
                                            data-id="${item.id}"
                                            data-nama="${escapeHtml(item.nama_project)}" title="Hapus Project">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });

                        $('#projectTableBody').html(html);
                        updateStatsBadges(res.stats);
                        renderPagination(res.pagination);
                    },
                    error: function(xhr) {
                        $('#projectTableBody').html(`
                            <tr>
                                <td colspan="11" class="text-center py-5 text-danger">
                                    <i class="ri-error-warning-line fs-36 d-block mb-2"></i>
                                    Gagal memuat data project dari server.
                                </td>
                            </tr>
                        `);
                    }
                });
            }

            // ── Pagination Click Handlers ──
            $(document).on('click', '.btn-page-nav', function(e) {
                e.preventDefault();
                const page = parseInt($(this).data('page'));
                if (page && page !== currentPage) {
                    loadProjectsData(page);
                    $('html, body').animate({
                        scrollTop: $('#projectTable').offset().top - 120
                    }, 200);
                }
            });

            // ── Per Page Select Change ──
            $('#perPageSelect').on('change', function() {
                currentPerPage = parseInt($(this).val()) || 25;
                loadProjectsData(1);
            });

            // ── Sort Header UI Helper ──
            function updateSortHeaderUI() {
                $('.sortable-th').each(function() {
                    const col = $(this).data('sort');
                    const $icon = $(this).find('.sort-icon');
                    if (col === currentSortBy) {
                        $(this).addClass('is-sorted');
                        if (currentSortDir === 'asc') {
                            $icon.attr('class', 'ri-arrow-up-line sort-icon');
                        } else {
                            $icon.attr('class', 'ri-arrow-down-line sort-icon');
                        }
                    } else {
                        $(this).removeClass('is-sorted');
                        $icon.attr('class', 'ri-arrow-up-down-line sort-icon');
                    }
                });
            }

            // ── Sort Column Click Handler ──
            $(document).on('click', '.sortable-th', function() {
                const col = $(this).data('sort');
                if (!col) return;

                if (currentSortBy === col) {
                    currentSortDir = currentSortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSortBy = col;
                    // Numerical / dates / progress default to desc, names/strings to asc
                    if (['finish_date', 'progress'].includes(col)) {
                        currentSortDir = 'desc';
                    } else {
                        currentSortDir = 'asc';
                    }
                }

                saveFiltersToStorage();
                updateSortHeaderUI();
                loadProjectsData(1);
            });

            // ── Event Handlers for Filter (Async jQuery) ──
            let searchDebounceTimer = null;
            $('#filterSearch').on('input keyup', function() {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function() {
                    saveFiltersToStorage();
                    loadProjectsData(1);
                }, 400);
            });

            // Checkbox multi-select change
            $(document).on('change', '.filter-checkbox', function() {
                saveFiltersToStorage();
                updateDropdownLabels();
                updateActiveFilterBadges();
                loadProjectsData(1);
            });

            // Remove tag from active filters bar
            $(document).on('click', '.btn-remove-active-tag', function(e) {
                e.preventDefault();
                const filter = $(this).data('filter');
                const val = String($(this).data('value'));
                $(`.filter-checkbox-${filter}`).filter(function() {
                    return $(this).val() === val;
                }).prop('checked', false);
                saveFiltersToStorage();
                updateDropdownLabels();
                updateActiveFilterBadges();
                loadProjectsData(1);
            });

            // Select all options in a dropdown
            $('.btn-select-all').on('click', function(e) {
                e.preventDefault();
                const target = $(this).data('target');
                $(`.filter-checkbox-${target}`).prop('checked', true);
                saveFiltersToStorage();
                updateDropdownLabels();
                updateActiveFilterBadges();
                loadProjectsData(1);
            });

            // Clear options in a single dropdown
            $('.btn-clear-filter').on('click', function(e) {
                e.preventDefault();
                const target = $(this).data('target');
                $(`.filter-checkbox-${target}`).prop('checked', false);
                saveFiltersToStorage();
                updateDropdownLabels();
                updateActiveFilterBadges();
                loadProjectsData(1);
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

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                loadProjectsData(1);
            });

            // ── Reset All Filters Async (Clears LocalStorage) ──
            $('#btnResetFilter, #btnClearAllActive').on('click', function(e) {
                e.preventDefault();
                clearFiltersStorage();
                $('#filterSearch').val('');
                $('.filter-checkbox').prop('checked', false);
                $('.filter-search-input').val('').trigger('input');
                currentSortBy = 'created_at';
                currentSortDir = 'desc';
                updateSortHeaderUI();
                updateDropdownLabels();
                updateActiveFilterBadges();
                loadProjectsData(1);
            });

            // ── Download Data to Excel (Filtered / Sorted) ──
            $(document).on('click', '.btn-export-excel', function(e) {
                e.preventDefault();
                const params = new URLSearchParams();

                const search = $('#filterSearch').val();
                if (search) params.append('search', search);

                const fase = getSelectedFilterValues('fase');
                if (fase.length > 0) params.append('fase', fase.join(','));

                const area = getSelectedFilterValues('area');
                if (area.length > 0) params.append('area', area.join(','));

                const kategori = getSelectedFilterValues('kategori');
                if (kategori.length > 0) params.append('kategori', kategori.join(','));

                const priority = getSelectedFilterValues('priority');
                if (priority.length > 0) params.append('priority', priority.join(','));

                const status = getSelectedFilterValues('status');
                if (status.length > 0) params.append('status', status.join(','));

                if (currentSortBy) params.append('sort_by', currentSortBy);
                if (currentSortDir) params.append('sort_dir', currentSortDir);

                const exportUrl = '{{ route('utility.project.export') }}?' + params.toString();
                window.location.href = exportUrl;
            });

            // ── Initial Load on Page Ready with LocalStorage ──
            const urlParams = new URLSearchParams(window.location.search);
            const hasUrlFilters = urlParams.has('status') || urlParams.has('area') ||
                urlParams.has('kategori') || urlParams.has('priority') ||
                urlParams.has('fase') || urlParams.has('search');

            if (!hasUrlFilters) {
                restoreFiltersFromStorage();
            } else {
                saveFiltersToStorage();
            }

            updateSortHeaderUI();
            updateDropdownLabels();
            updateActiveFilterBadges();
            loadProjectsData();

            // ── Progress Range & Input Sync (Tidak boleh mundur) ──
            let activeMinProgress = 0;

            $('#modal_progress_range').on('input', function() {
                let val = parseInt($(this).val()) || 0;
                if (val < activeMinProgress) {
                    val = activeMinProgress;
                    $(this).val(val);
                }
                $('#modal_progress_input').val(val);
                $('#modal_slider_val').text(val + '%');
            });

            $('#modal_progress_input').on('input change', function() {
                let val = parseInt($(this).val()) || 0;
                if (val < activeMinProgress) {
                    val = activeMinProgress;
                    $(this).val(val);
                }
                if (val > 100) val = 100;
                $('#modal_progress_range').val(val);
                $('#modal_slider_val').text(val + '%');
            });

            // ── Open Update Progress Modal ──
            $(document).on('click', '.btn-update-progress', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const prog = parseInt($(this).data('progress')) || 0;
                const fase = $(this).attr('data-fase') || $(this).data('fase') || '';
                const note = $(this).attr('data-note') || $(this).data('note') || '';

                activeMinProgress = prog;
                $('#modal_project_id').val(id);
                $('#modal_project_nama').text(nama);
                $('#modal_progress_range').attr('min', prog).val(prog);
                $('#modal_progress_input').attr('min', prog).val(prog);
                $('#modal_min_val').text(prog + '%');
                $('#modal_slider_val').text(prog + '%');
                $('#modal_fase').val(fase);
                $('#modal_catatan').val('');

                if (note && note.trim().length > 0) {
                    $('#modal_prev_note_text').text(note);
                    $('#modal_prev_note_box').show();
                } else {
                    $('#modal_prev_note_text').text('');
                    $('#modal_prev_note_box').hide();
                }

                $('#modalUpdateProgress').modal('show');
            });

            // ── Submit Progress via Async jQuery AJAX ──
            $('#formUpdateProgress').on('submit', function(e) {
                e.preventDefault();

                const newProg = parseInt($('#modal_progress_input').val()) || 0;
                if (newProg < activeMinProgress) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Progress Tidak Valid',
                        text: `Progress tidak boleh mundur! Minimal progress adalah ${activeMinProgress}%.`
                    });
                    return;
                }

                const id = $('#modal_project_id').val();
                const $btn = $('#btnSubmitProgress');
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
                            $('#modalUpdateProgress').modal('hide');

                            // Realtime DOM updates
                            const $row = $('#row-project-' + id);
                            const prog = res.data.progress;
                            const status = res.data.status;
                            const note = res.data.latest_note;

                            // Progress bar & text
                            const barColor = (prog == 100) ? 'bg-success' : (status ===
                                'Terlambat' ? 'bg-danger' : 'bg-primary');
                            $row.find('.progress-bar-' + id)
                                .css('width', prog + '%')
                                .attr('class', 'progress-bar progress-bar-' + id + ' ' +
                                    barColor);
                            $row.find('.progress-text-' + id).text(prog + '%');

                            // Note text with icon
                            if (note && note.trim().length > 0) {
                                $row.find('.note-text-' + id)
                                    .attr('class', 'note-text-' + id +
                                        ' fs-12 text-dark text-truncate')
                                    .html('<i class="ri-chat-1-line text-primary me-1"></i>' +
                                        escapeHtml(note))
                                    .attr('title', note);
                            } else {
                                $row.find('.note-text-' + id)
                                    .attr('class', 'note-text-' + id +
                                        ' fs-12 text-muted fst-italic')
                                    .html('-')
                                    .attr('title', '');
                            }

                            // Data attributes
                            $row.find('.btn-update-progress')
                                .data('progress', prog).attr('data-progress', prog)
                                .data('fase', res.data.fase || '').attr('data-fase', res.data.fase || '')
                                .data('note', note || '').attr('data-note', note || '');

                            // Fase Badge
                            if (res.data.fase) {
                                $row.find('.fase-col-' + id).html(getFaseBadge(res.data.fase));
                            }

                            // Status Badge
                            $row.find('.status-col-' + id).html(getStatusBadge(status));

                            // Sinkronisasi data ke DataTables
                            if (projectTable) {
                                projectTable.row($row).invalidate().draw(false);
                                updateStatsBadges();
                            }

                            // Refresh data project
                            loadProjectsData();

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message ||
                                    'Terjadi kesalahan saat memperbarui progress.'
                            });
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Gagal memproses request ke server.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(origText);
                    }
                });
            });

            // ── View History via Async jQuery AJAX ──
            $(document).on('click', '.btn-view-history', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                $('#history_project_title').text(nama);
                $('#history_loading').removeClass('d-none');
                $('#history_container').addClass('d-none').empty();

                $('#modalHistory').modal('show');

                $.ajax({
                    url: `{{ url('utility/project') }}/${id}/history`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $('#history_loading').addClass('d-none');
                        const $container = $('#history_container').removeClass('d-none');

                        if (!res.logs || res.logs.length === 0) {
                            $container.html(`
                        <div class="text-center py-4 text-muted">
                            <i class="ri-time-line fs-28 d-block mb-1"></i>
                            Belum ada riwayat update progress untuk project ini.
                        </div>
                    `);
                            return;
                        }

                        let html = '<div class="d-flex flex-column gap-2">';
                        $.each(res.logs, function(i, log) {
                            const diff = log.progress_baru - log.progress_sebelumnya;
                            const diffBadge = diff > 0 ?
                                `<span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold">+${diff}%</span>` :
                                (diff < 0 ?
                                    `<span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold">${diff}%</span>` :
                                    `<span class="badge bg-secondary-subtle text-muted border">0%</span>`
                                );

                            html += `
                                <div class="card border border-light-subtle shadow-none bg-light bg-opacity-50 mb-0 rounded-3">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-primary fs-12 px-2 py-1">${log.progress_sebelumnya}% &rarr; ${log.progress_baru}%</span>
                                                ${diffBadge}
                                            </div>
                                            <span class="text-muted fs-12"><i class="ri-calendar-line me-1"></i>${log.tanggal} ${log.jam}</span>
                                        </div>
                                        <div class="text-dark fs-13 mb-2 text-break">${escapeHtml(log.catatan)}</div>
                                        <div class="text-muted fs-12 border-top pt-2 mt-1">
                                            <i class="ri-user-line me-1"></i>Diupdate oleh: <strong class="text-dark">${escapeHtml(log.user_nama)}</strong>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        $container.html(html);
                    },
                    error: function() {
                        $('#history_loading').addClass('d-none');
                        $('#history_container').removeClass('d-none').html(
                            '<div class="alert alert-danger mb-0">Gagal memuat data riwayat project. Silakan coba kembali.</div>'
                        );
                    }
                });
            });

            // ── Delete Project via Async jQuery AJAX ──
            $(document).on('click', '.btn-delete-project', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus Project?',
                    text: `Anda yakin ingin menghapus project "${nama}"? Data riwayat progress juga akan terhapus.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('utility/project') }}/${id}`,
                            type: 'DELETE',
                            dataType: 'json',
                            success: function(res) {
                                if (res.success) {
                                    if (projectTable) {
                                        projectTable.row($('#row-project-' + id))
                                            .remove().draw(false);
                                        updateStatsBadges();
                                    } else {
                                        $('#row-project-' + id).fadeOut(300,
                                            function() {
                                                $(this).remove();
                                            });
                                    }
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: res.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire('Gagal!', res.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                let msg = 'Gagal menghapus project.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });

            // ── Clear Import File Input ──
            $('#btnClearFile').on('click', function() {
                $('#import_excel_file').val('');
            });

            // ── Submit Import Form via AJAX ──
            $('#formImportExcel').on('submit', function(e) {
                e.preventDefault();

                const fileInput = document.getElementById('import_excel_file');
                if (!fileInput.files || fileInput.files.length === 0) {
                    Swal.fire('Perhatian', 'Silakan pilih file Excel terlebih dahulu.', 'warning');
                    return;
                }

                const formData = new FormData(this);
                const $btn = $('#btnSubmitImport');
                const origText = $btn.html();

                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span> Mengupload...');
                $('#importLoadingState').removeClass('d-none');

                $.ajax({
                    url: '{{ route('utility.project.import') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(res) {
                        $('#importLoadingState').addClass('d-none');
                        $btn.prop('disabled', false).html(origText);

                        if (res.success) {
                            $('#modalImportExcel').modal('hide');
                            $('#formImportExcel')[0].reset();

                            Swal.fire({
                                icon: 'success',
                                title: 'Migrasi Berhasil!',
                                text: res.message,
                                confirmButtonColor: '#198754',
                                confirmButtonText: 'OK, Lihat Data'
                            }).then(() => {
                                loadProjectsData();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Import Data',
                                text: res.message ||
                                    'Terjadi kesalahan saat memproses data Excel.'
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#importLoadingState').addClass('d-none');
                        $btn.prop('disabled', false).html(origText);

                        let msg = 'Gagal memproses file Excel di server.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errs = Object.values(xhr.responseJSON.errors).flat();
                            if (errs.length > 0) msg = errs.join('<br>');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error Import',
                            html: msg
                        });
                    }
                });
            });
        });
    </script>
@endsection
