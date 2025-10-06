@extends('layouts.app')

@section('title', 'Machine Process Management')

@section('styles')
<style>
    .card-hover:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
    }

    .status-badge {
        font-size: 0.875rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 500;
    }

    .btn-action {
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(64, 81, 137, 0.05);
    }

    .tree-view {
        border-left: 2px solid #dee2e6;
        padding-left: 1rem;
        margin-left: 0.5rem;
    }

    .tree-item {
        padding: 0.5rem;
        margin: 0.25rem 0;
        border-radius: 0.375rem;
        background: #f8f9fa;
    }

    .critical-badge {
        background: #dc3545;
        color: white;
        padding: 0.125rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
    }

    .process-card {
        border-left: 4px solid #405189;
    }

    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Machine Process Management</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Scoring Mesin</a></li>
                            <li class="breadcrumb-item active">Machine Process</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate card-hover" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Total Process</p>
                                <h4 class="mb-0" id="totalProcesses">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-primary rounded fs-3">
                                        <i class="bx bx-slider text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate card-hover" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Active Machines</p>
                                <h4 class="mb-0 text-success" id="activeMachines">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-success rounded fs-3">
                                        <i class="bx bx-check-circle text-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate card-hover" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Maintenance</p>
                                <h4 class="mb-0 text-warning" id="maintenanceMachines">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-warning rounded fs-3">
                                        <i class="bx bx-wrench text-warning"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate card-hover" data-aos="fade-up" data-aos-delay="400">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-semibold text-muted fs-12 mb-1">Broken</p>
                                <h4 class="mb-0 text-danger" id="brokenMachines">0</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-soft-danger rounded fs-3">
                                        <i class="bx bx-x-circle text-danger"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card" data-aos="fade-up" data-aos-delay="500">
                    <div class="card-header border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-settings-2-line align-middle me-1"></i>
                                Daftar Machine Process
                            </h5>
                            <div class="flex-shrink-0">
                                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
                                    <i class="ri-stack-line align-bottom me-1"></i> Bulk Assign
                                </button>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="ri-add-line align-bottom me-1"></i> Tambah Process
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter dan Search -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="search-box">
                                    <input type="text" id="searchProcess" class="form-control search" placeholder="Cari process...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterMachine">
                                    <option value="">Semua Mesin</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="active">Aktif</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="broken">Rusak</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-outline-primary active" id="viewList">
                                        <i class="ri-list-check"></i> List
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="viewTree">
                                        <i class="ri-node-tree"></i> Tree
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- List View -->
                        <div id="listView">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle mb-0" id="processTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">No</th>
                                            <th scope="col">Mesin</th>
                                            <th scope="col">Kode</th>
                                            <th scope="col">Parameter Proses</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Catatan</th>
                                            <th scope="col">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="processTableBody">
                                        <tr id="loadingRow">
                                            <td colspan="7" class="text-center py-4">
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <div class="spinner-border text-primary me-2" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <span>Memuat data machine process...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tree View -->
                        <div id="treeView" style="display: none;">
                            <div id="treeContainer" class="p-3">
                                <div class="alert alert-info text-center">
                                    <i class="ri-information-line"></i> Pilih mesin dari filter untuk melihat tree view
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Machine Process</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mesin <span class="text-danger">*</span></label>
                        <select class="form-select" name="machine_id" required>
                            <option value="">Pilih Mesin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parameter Proses <span class="text-danger">*</span></label>
                        <select class="form-select" name="process_parameter_id" required>
                            <option value="">Pilih Parameter</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="catatan" rows="3" placeholder="Masukkan catatan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line align-bottom me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Machine Process</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm">
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mesin <span class="text-danger">*</span></label>
                        <select class="form-select" name="machine_id" required>
                            <option value="">Pilih Mesin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parameter Proses <span class="text-danger">*</span></label>
                        <select class="form-select" name="process_parameter_id" required>
                            <option value="">Pilih Parameter</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="catatan" rows="3" placeholder="Masukkan catatan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-refresh-line align-bottom me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Assign Modal -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Assign Process Parameters</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkAssignForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mesin <span class="text-danger">*</span></label>
                        <select class="form-select" name="machine_id" required>
                            <option value="">Pilih Mesin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parameter Proses <span class="text-danger">*</span></label>
                        <div id="processParameterCheckboxes" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <!-- Checkboxes akan dimuat via JS -->
                        </div>
                        <small class="text-muted">Pilih satu atau lebih parameter proses</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (untuk semua)</label>
                        <textarea class="form-control" name="catatan" rows="3" placeholder="Masukkan catatan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-check-double-line align-bottom me-1"></i> Assign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Machine Process</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content will be loaded via JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // CSRF Token Setup
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // State
    let machineProcesses = [];
    let machines = [];
    let processParameters = [];
    let filteredData = [];

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadMachines();
        loadProcessParameters();
        loadMachineProcesses();
        loadStatistics();
        setupEventListeners();
    });

    // Load Data
    async function loadMachines() {
        try {
            const response = await fetch('/scoring-mesin/machines', {
                headers: {
                    'Accept': 'application/json'
                }
            });
            machines = await response.json();
            populateMachineSelects();
        } catch (error) {
            console.error('Error loading machines:', error);
        }
    }

    async function loadProcessParameters() {
        try {
            const response = await fetch('/scoring-mesin/process-parameters', {
                headers: {
                    'Accept': 'application/json'
                }
            });
            processParameters = await response.json();
            populateProcessParameterSelects();
            populateProcessParameterCheckboxes();
        } catch (error) {
            console.error('Error loading process parameters:', error);
        }
    }

    async function loadMachineProcesses() {
        try {
            const response = await fetch('/machine-processes', {
                headers: {
                    'Accept': 'application/json'
                }
            });
            machineProcesses = await response.json();
            filteredData = machineProcesses;
            renderListView();
        } catch (error) {
            console.error('Error loading machine processes:', error);
            showError('Gagal memuat data machine processes');
        }
    }

    async function loadStatistics() {
        try {
            // Count statistics from machineProcesses
            const total = machineProcesses.length;
            const active = machineProcesses.filter(mp => mp.machine.status === 'active').length;
            const maintenance = machineProcesses.filter(mp => mp.machine.status === 'maintenance').length;
            const broken = machineProcesses.filter(mp => mp.machine.status === 'broken').length;

            document.getElementById('totalProcesses').textContent = total;
            document.getElementById('activeMachines').textContent = active;
            document.getElementById('maintenanceMachines').textContent = maintenance;
            document.getElementById('brokenMachines').textContent = broken;
        } catch (error) {
            console.error('Error loading statistics:', error);
        }
    }

    // Populate Selects
    function populateMachineSelects() {
        const selects = document.querySelectorAll('select[name="machine_id"]');
        const filterSelect = document.getElementById('filterMachine');

        const options = machines.map(m =>
            `<option value="${m.id}">${m.name} (${m.code})</option>`
        ).join('');

        selects.forEach(select => {
            select.innerHTML = '<option value="">Pilih Mesin</option>' + options;
        });

        filterSelect.innerHTML = '<option value="">Semua Mesin</option>' + options;
    }

    function populateProcessParameterSelects() {
        const selects = document.querySelectorAll('select[name="process_parameter_id"]');
        const options = processParameters.map(p =>
            `<option value="${p.id}">${p.name}</option>`
        ).join('');

        selects.forEach(select => {
            select.innerHTML = '<option value="">Pilih Parameter</option>' + options;
        });
    }

    function populateProcessParameterCheckboxes() {
        const container = document.getElementById('processParameterCheckboxes');
        container.innerHTML = processParameters.map(p => `
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" value="${p.id}" id="param${p.id}">
                <label class="form-check-label" for="param${p.id}">
                    ${p.name}
                </label>
            </div>
        `).join('');
    }

    // Render List View
    function renderListView() {
        const tbody = document.getElementById('processTableBody');
        const searchTerm = document.getElementById('searchProcess').value.toLowerCase();
        const filterMachine = document.getElementById('filterMachine').value;
        const filterStatus = document.getElementById('filterStatus').value;

        let filtered = machineProcesses;

        if (searchTerm) {
            filtered = filtered.filter(mp =>
                mp.machine.name.toLowerCase().includes(searchTerm) ||
                mp.machine.code.toLowerCase().includes(searchTerm) ||
                mp.process_parameter.name.toLowerCase().includes(searchTerm)
            );
        }

        if (filterMachine) {
            filtered = filtered.filter(mp => mp.machine_id == filterMachine);
        }

        if (filterStatus) {
            filtered = filtered.filter(mp => mp.machine.status === filterStatus);
        }

        filteredData = filtered;

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">
                            <i class="ri-information-line fs-3"></i>
                            <p class="mt-2">Tidak ada data machine process</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = filtered.map((mp, index) => {
            const statusClass = mp.machine.status === 'active' ? 'success' :
                mp.machine.status === 'maintenance' ? 'warning' : 'danger';
            const statusText = mp.machine.status === 'active' ? 'Aktif' :
                mp.machine.status === 'maintenance' ? 'Maintenance' : 'Rusak';

            return `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-soft-primary rounded me-2">
                                <div class="avatar-title bg-soft-primary text-primary">
                                    <i class="bx bx-cog"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0">${mp.machine.name}</h6>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-soft-secondary">${mp.machine.code}</span></td>
                    <td>
                        <i class="ri-slider-line text-primary me-1"></i>
                        ${mp.process_parameter.name}
                    </td>
                    <td>
                        <span class="badge badge-soft-${statusClass}">${statusText}</span>
                    </td>
                    <td>
                        ${mp.catatan ? `<small class="text-muted">${mp.catatan.substring(0, 50)}${mp.catatan.length > 50 ? '...' : ''}</small>` : '<span class="text-muted">-</span>'}
                    </td>
                    <td>
                        <div class="hstack gap-1">
                            <button class="btn btn-sm btn-soft-info btn-action" onclick="viewDetail(${mp.id})" title="Detail">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button class="btn btn-sm btn-soft-warning btn-action" onclick="editMachineProcess(${mp.id})" title="Edit">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button class="btn btn-sm btn-soft-danger btn-action" onclick="deleteMachineProcess(${mp.id})" title="Hapus">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Load Tree View
    async function loadTreeView(machineId) {
        try {
            const response = await fetch(`/machine-processes/tree/${machineId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            renderTreeView(data);
        } catch (error) {
            console.error('Error loading tree view:', error);
            showError('Gagal memuat tree view');
        }
    }

    function renderTreeView(data) {
        const container = document.getElementById('treeContainer');

        const statusClass = data.machine.status === 'active' ? 'success' :
            data.machine.status === 'maintenance' ? 'warning' : 'danger';

        let html = `
            <div class="card border mb-4">
                <div class="card-body bg-soft-primary">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary rounded me-3">
                            <div class="avatar-title bg-primary text-white">
                                <i class="bx bx-cog fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="mb-1">${data.machine.name}</h4>
                            <p class="text-muted mb-0">
                                <strong>Kode:</strong> ${data.machine.code} | 
                                <span class="badge badge-soft-${statusClass}">${data.machine.status_text}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        if (data.processes.length === 0) {
            html += `
                <div class="alert alert-warning">
                    <i class="ri-alert-line"></i> Belum ada process parameter untuk mesin ini
                </div>
            `;
        } else {
            data.processes.forEach(process => {
                html += `
                    <div class="card process-card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <i class="ri-slider-line text-primary fs-4 me-2"></i>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">${process.process_parameter_name}</h5>
                                    ${process.catatan ? `<p class="text-muted mb-0"><i class="ri-sticky-note-line"></i> ${process.catatan}</p>` : ''}
                                </div>
                            </div>
                            
                            ${process.sections.length > 0 ? `
                                <div class="tree-view">
                                    ${process.sections.map(section => `
                                        <div class="mb-3">
                                            <div class="tree-item">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="ri-folder-line text-info me-2"></i>
                                                    <strong>${section.name}</strong>
                                                </div>
                                                ${section.parts.length > 0 ? `
                                                    <div class="ms-4">
                                                        ${section.parts.map(part => `
                                                            <div class="d-flex align-items-center mb-2">
                                                                <i class="ri-box-3-line text-secondary me-2"></i>
                                                                <span>${part.name}</span>
                                                                ${part.critical === 'Y' ? '<span class="critical-badge ms-2">CRITICAL</span>' : ''}
                                                                ${part.standar ? `<span class="text-muted ms-2 small">(${part.standar})</span>` : ''}
                                                            </div>
                                                        `).join('')}
                                                    </div>
                                                ` : '<p class="text-muted small ms-4">Belum ada part</p>'}
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            ` : '<p class="text-muted">Belum ada section</p>'}
                        </div>
                    </div>
                `;
            });
        }

        container.innerHTML = html;
    }

    // Event Listeners
    function setupEventListeners() {
        // View Toggle
        document.getElementById('viewList').addEventListener('click', function() {
            document.getElementById('listView').style.display = 'block';
            document.getElementById('treeView').style.display = 'none';
            this.classList.add('active');
            document.getElementById('viewTree').classList.remove('active');
        });

        document.getElementById('viewTree').addEventListener('click', function() {
            document.getElementById('listView').style.display = 'none';
            document.getElementById('treeView').style.display = 'block';
            this.classList.add('active');
            document.getElementById('viewList').classList.remove('active');

            const machineId = document.getElementById('filterMachine').value;
            if (machineId) {
                loadTreeView(machineId);
            } else {
                document.getElementById('treeContainer').innerHTML = `
                    <div class="alert alert-info text-center">
                        <i class="ri-information-line"></i> Pilih mesin dari filter untuk melihat tree view
                    </div>
                `;
            }
        });

        // Filters
        document.getElementById('filterMachine').addEventListener('change', function() {
            renderListView();
            if (document.getElementById('viewTree').classList.contains('active') && this.value) {
                loadTreeView(this.value);
            }
        });

        document.getElementById('filterStatus').addEventListener('change', renderListView);
        document.getElementById('searchProcess').addEventListener('input', renderListView);

        // Forms
        document.getElementById('addForm').addEventListener('submit', handleAdd);
        document.getElementById('editForm').addEventListener('submit', handleEdit);
        document.getElementById('bulkAssignForm').addEventListener('submit', handleBulkAssign);
    }

    // CRUD Operations
    async function handleAdd(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        try {
            const response = await fetch('/machine-processes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                showSuccess(result.message);
                bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();
                e.target.reset();
                loadMachineProcesses();
                loadStatistics();
            } else {
                showError(result.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            showError('Terjadi kesalahan: ' + error.message);
        }
    }

    async function editMachineProcess(id) {
        const mp = machineProcesses.find(m => m.id === id);
        const form = document.getElementById('editForm');

        form.querySelector('[name="id"]').value = mp.id;
        form.querySelector('[name="machine_id"]').value = mp.machine_id;
        form.querySelector('[name="process_parameter_id"]').value = mp.process_parameter_id;
        form.querySelector('[name="catatan"]').value = mp.catatan || '';

        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    async function handleEdit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        const id = data.id;
        delete data.id;

        try {
            const response = await fetch(`/machine-processes/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                showSuccess(result.message);
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                loadMachineProcesses();
            } else {
                showError(result.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            showError('Terjadi kesalahan: ' + error.message);
        }
    }

    async function deleteMachineProcess(id) {
        if (!confirm('Yakin ingin menghapus machine process ini?')) return;

        try {
            const response = await fetch(`/machine-processes/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const result = await response.json();
            showSuccess(result.message);
            loadMachineProcesses();
            loadStatistics();
        } catch (error) {
            showError('Terjadi kesalahan: ' + error.message);
        }
    }

    async function handleBulkAssign(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const machineId = formData.get('machine_id');
        const catatan = formData.get('catatan');

        const checkedBoxes = document.querySelectorAll('#processParameterCheckboxes input[type="checkbox"]:checked');
        const processParameterIds = Array.from(checkedBoxes).map(cb => parseInt(cb.value));

        if (processParameterIds.length === 0) {
            showError('Pilih minimal 1 parameter proses');
            return;
        }

        try {
            const response = await fetch(`/machine-processes/bulk-assign/${machineId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    process_parameter_ids: processParameterIds,
                    catatan: catatan
                })
            });

            const result = await response.json();
            showSuccess(`${result.message}\nBerhasil: ${result.created}, Dilewati: ${result.skipped}`);
            bootstrap.Modal.getInstance(document.getElementById('bulkAssignModal')).hide();
            e.target.reset();
            document.querySelectorAll('#processParameterCheckboxes input[type="checkbox"]').forEach(cb => cb.checked = false);
            loadMachineProcesses();
            loadStatistics();
        } catch (error) {
            showError('Terjadi kesalahan: ' + error.message);
        }
    }

    async function viewDetail(id) {
        try {
            const response = await fetch(`/machine-processes/${id}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const mp = await response.json();

            const statusClass = mp.machine.status === 'active' ? 'success' :
                mp.machine.status === 'maintenance' ? 'warning' : 'danger';

            let detailHtml = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card border">
                            <div class="card-header bg-soft-primary">
                                <h6 class="mb-0"><i class="ri-settings-3-line"></i> Informasi Mesin</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td class="fw-semibold" width="40%">Nama Mesin</td>
                                        <td>${mp.machine.name}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Kode Mesin</td>
                                        <td><span class="badge badge-soft-secondary">${mp.machine.code}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Status</td>
                                        <td><span class="badge badge-soft-${statusClass}">${mp.machine.status === 'active' ? 'Aktif' : mp.machine.status === 'maintenance' ? 'Maintenance' : 'Rusak'}</span></td>
                                    </tr>
                                    ${mp.machine.description ? `
                                    <tr>
                                        <td class="fw-semibold">Deskripsi</td>
                                        <td>${mp.machine.description}</td>
                                    </tr>
                                    ` : ''}
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border">
                            <div class="card-header bg-soft-info">
                                <h6 class="mb-0"><i class="ri-slider-line"></i> Informasi Parameter</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td class="fw-semibold" width="40%">Parameter Proses</td>
                                        <td>${mp.process_parameter.name}</td>
                                    </tr>
                                    ${mp.catatan ? `
                                    <tr>
                                        <td class="fw-semibold">Catatan</td>
                                        <td>${mp.catatan}</td>
                                    </tr>
                                    ` : ''}
                                    <tr>
                                        <td class="fw-semibold">Jumlah Section</td>
                                        <td><span class="badge badge-soft-primary">${mp.process_parameter.sections.length}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                ${mp.process_parameter.sections.length > 0 ? `
                <div class="card border">
                    <div class="card-header bg-soft-success">
                        <h6 class="mb-0"><i class="ri-folder-line"></i> Sections & Parts</h6>
                    </div>
                    <div class="card-body">
                        ${mp.process_parameter.sections.map(section => `
                            <div class="mb-3 p-3 bg-light rounded">
                                <h6 class="mb-2">
                                    <i class="ri-folder-3-line text-info"></i> ${section.name}
                                </h6>
                                ${section.parts.length > 0 ? `
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Part</th>
                                                    <th>Critical</th>
                                                    <th>Standar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${section.parts.map((part, idx) => `
                                                    <tr>
                                                        <td>${idx + 1}</td>
                                                        <td>
                                                            <i class="ri-box-3-line text-secondary"></i> ${part.name}
                                                        </td>
                                                        <td>
                                                            ${part.critical === 'Y' ? '<span class="critical-badge">YES</span>' : '<span class="badge badge-soft-secondary">NO</span>'}
                                                        </td>
                                                        <td>${part.standar || '-'}</td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                ` : '<p class="text-muted mb-0 ms-3">Belum ada part</p>'}
                            </div>
                        `).join('')}
                    </div>
                </div>
                ` : '<div class="alert alert-warning"><i class="ri-alert-line"></i> Belum ada section untuk parameter proses ini</div>'}
            `;

            document.getElementById('detailContent').innerHTML = detailHtml;
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        } catch (error) {
            showError('Gagal memuat detail: ' + error.message);
        }
    }

    // Helper Functions
    function showSuccess(message) {
        // You can integrate with your existing notification system
        alert(message);
    }

    function showError(message) {
        // You can integrate with your existing notification system
        alert(message);
    }
</script>
@endsection