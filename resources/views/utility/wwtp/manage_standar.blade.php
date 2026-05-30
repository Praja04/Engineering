@extends('layouts.app')

@section('title', 'Master Data Analisa WWTP')

@section('styles')
    <style>
        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.15rem rgba(41, 156, 219, 0.25);
            border-color: #299cdb;
        }

        .btn-info {
            background-color: #299cdb;
            border-color: #299cdb;
        }

        .btn-info:hover {
            background-color: #2284ba;
            border-color: #2284ba;
        }

        .master-tabs .nav-link {
            color: #495057;
            font-weight: 600;
        }

        .master-tabs .nav-link.active {
            color: #299cdb;
        }

        .standard-group {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .standard-group+.standard-group {
            margin-top: 1rem;
        }

        .standard-group-header {
            border-bottom: 1px solid #e9ecef;
        }

        .table> :not(caption)>*>* {
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    @php
        $canManageMaster = (Auth::user()->jabatan ?? '') !== 'operator';
    @endphp

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">Master Data Analisa WWTP</h4>
                            <p class="text-muted mb-0">Kelola parameter, point pengukuran, dan nilai standar analisa.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ url('/wwtp/form_analisa') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-file-document-edit-outline me-1"></i> Form Analisa
                            </a>
                            @if ($canManageMaster)
                                <button type="button" class="btn btn-info text-white" id="btnContextAdd">
                                    <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Parameter
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-3">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                                    <i class="mdi mdi-flask-empty-outline fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-1">Parameter</p>
                                    <h4 class="mb-0 fw-bold" id="totalParameters">{{ $parameters->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-info bg-opacity-10 text-info rounded-3 p-3 me-3">
                                    <i class="mdi mdi-map-marker-radius-outline fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-1">Point Pengukuran</p>
                                    <h4 class="mb-0 fw-bold" id="totalPoints">{{ $points->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                                    <i class="mdi mdi-check-decagram-outline fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-1">Standar Value</p>
                                    <h4 class="mb-0 fw-bold" id="totalStandards">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <ul class="nav nav-tabs nav-justified master-tabs" id="masterDataTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="parameter-tab" data-bs-toggle="tab"
                                data-bs-target="#parameterPane" type="button" role="tab">
                                <i class="mdi mdi-flask-empty-outline me-1"></i> Manage Parameter
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="point-tab" data-bs-toggle="tab" data-bs-target="#pointPane"
                                type="button" role="tab">
                                <i class="mdi mdi-map-marker-radius-outline me-1"></i> Manage Point Pengukuran
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="standard-tab" data-bs-toggle="tab" data-bs-target="#standardPane"
                                type="button" role="tab">
                                <i class="mdi mdi-check-decagram-outline me-1"></i> Manage Standar Value
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-4">
                        <div class="tab-pane fade show active" id="parameterPane" role="tabpanel">
                            <div class="row g-3 mb-3 align-items-end">
                                <div class="col-md-10">
                                    <label for="searchParameter" class="form-label small text-muted fw-semibold">Cari
                                        Parameter</label>
                                    <input type="text" class="form-control" id="searchParameter"
                                        placeholder="Cari nama parameter atau satuan...">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-secondary w-100" data-reset="parameter">
                                        <i class="mdi mdi-refresh me-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 8%;">No</th>
                                            <th>Parameter</th>
                                            <th>Satuan</th>
                                            <th class="text-center">Standar Terkait</th>
                                            <th class="text-center" style="width: 18%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="parameterTableBody"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pointPane" role="tabpanel">
                            <div class="row g-3 mb-3 align-items-end">
                                <div class="col-md-10">
                                    <label for="searchPoint" class="form-label small text-muted fw-semibold">Cari Point
                                        Pengukuran</label>
                                    <input type="text" class="form-control" id="searchPoint"
                                        placeholder="Cari nama point pengukuran...">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-secondary w-100" data-reset="point">
                                        <i class="mdi mdi-refresh me-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 8%;">No</th>
                                            <th>Point Pengukuran</th>
                                            <th class="text-center">Standar Terkait</th>
                                            <th class="text-center" style="width: 18%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pointTableBody"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="standardPane" role="tabpanel">
                            <div class="row g-3 mb-3 align-items-end">
                                <div class="col-md-10">
                                    <label for="searchStandard" class="form-label small text-muted fw-semibold">Cari
                                        Standar Value</label>
                                    <input type="text" class="form-control" id="searchStandard"
                                        placeholder="Cari parameter, point, satuan, atau nilai standar...">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-secondary w-100" data-reset="standard">
                                        <i class="mdi mdi-refresh me-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                            <div id="standardGroups"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="parameterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-info" id="parameterModalTitle">
                        <i class="mdi mdi-flask-empty-outline me-2"></i>Tambah Parameter
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="parameterForm">
                    @csrf
                    <input type="hidden" id="parameter_master_id" name="parameter_master_id">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="parameter_name" class="form-label fw-semibold">Nama Parameter <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="parameter_name" name="parameter_name"
                                placeholder="Contoh: pH" required>
                        </div>
                        <div class="mb-0">
                            <label for="unit" class="form-label fw-semibold">Satuan</label>
                            <input type="text" class="form-control" id="unit" name="unit"
                                placeholder="Contoh: mg/L">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-white" id="btnSaveParameter">
                            <i class="mdi mdi-content-save-outline me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pointModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-info" id="pointModalTitle">
                        <i class="mdi mdi-map-marker-radius-outline me-2"></i>Tambah Point Pengukuran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="pointForm">
                    @csrf
                    <input type="hidden" id="point_master_id" name="point_master_id">
                    <div class="modal-body p-4">
                        <label for="point_name" class="form-label fw-semibold">Nama Point Pengukuran <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="point_name" name="point_name"
                            placeholder="Contoh: Inlet Equalisasi" required>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-white" id="btnSavePoint">
                            <i class="mdi mdi-content-save-outline me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="standardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-info" id="standardModalTitle">
                        <i class="mdi mdi-check-decagram-outline me-2"></i>Tambah Standar Value
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="standardForm">
                    @csrf
                    <input type="hidden" id="standard_id" name="standard_id">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="point_id" class="form-label fw-semibold">Point Pengukuran <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="point_id" name="point_id" required></select>
                        </div>
                        <div class="mb-3">
                            <label for="parameter_id" class="form-label fw-semibold">Parameter <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="parameter_id" name="parameter_id" required></select>
                        </div>
                        <div class="mb-0">
                            <label for="standard_value" class="form-label fw-semibold">Nilai Standar</label>
                            <input type="number" step="0.01" min="0" class="form-control"
                                id="standard_value" name="standard_value" placeholder="Contoh: 6.50">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-white" id="btnSaveStandard">
                            <i class="mdi mdi-content-save-outline me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const canEditDelete = @json($canManageMaster);
            const modals = {
                parameter: new bootstrap.Modal(document.getElementById('parameterModal')),
                point: new bootstrap.Modal(document.getElementById('pointModal')),
                standard: new bootstrap.Modal(document.getElementById('standardModal')),
            };
            const endpoints = {
                parameters: "{{ url('/api/wwtp-analisa/parameters') }}",
                points: "{{ url('/api/wwtp-analisa/points') }}",
                standards: "{{ url('/api/wwtp-analisa/standards') }}",
            };
            const state = {
                activeTab: 'parameter',
                parameters: [],
                points: [],
            };
            let searchTimer;

            loadParameters();
            loadPoints();
            loadStandards();

            $('#masterDataTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                state.activeTab = e.target.id.replace('-tab', '');
                updateContextButton();
            });

            $('#btnContextAdd').on('click', function() {
                if (state.activeTab === 'point') return openPointModal();
                if (state.activeTab === 'standard') return openStandardModal();
                openParameterModal();
            });

            $('#searchParameter, #searchPoint, #searchStandard').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    const id = $(document.activeElement).attr('id');
                    if (id === 'searchPoint') return loadPoints();
                    if (id === 'searchStandard') return loadStandards();
                    loadParameters();
                }, 350);
            });

            $('[data-reset]').on('click', function() {
                const target = $(this).data('reset');
                $(`#search${capitalize(target)}`).val('');
                if (target === 'point') return loadPoints();
                if (target === 'standard') return loadStandards();
                loadParameters();
            });

            $('#parameterForm').on('submit', function(e) {
                e.preventDefault();
                saveMaster({
                    form: $(this),
                    id: $('#parameter_master_id').val(),
                    url: endpoints.parameters,
                    button: $('#btnSaveParameter'),
                    modal: modals.parameter,
                    reload: function() {
                        loadParameters();
                        loadStandards();
                    },
                    fallback: 'Gagal menyimpan parameter.'
                });
            });

            $('#pointForm').on('submit', function(e) {
                e.preventDefault();
                saveMaster({
                    form: $(this),
                    id: $('#point_master_id').val(),
                    url: endpoints.points,
                    button: $('#btnSavePoint'),
                    modal: modals.point,
                    reload: function() {
                        loadPoints();
                        loadStandards();
                    },
                    fallback: 'Gagal menyimpan point pengukuran.'
                });
            });

            $('#standardForm').on('submit', function(e) {
                e.preventDefault();
                saveMaster({
                    form: $(this),
                    id: $('#standard_id').val(),
                    url: endpoints.standards,
                    button: $('#btnSaveStandard'),
                    modal: modals.standard,
                    reload: loadStandards,
                    fallback: 'Gagal menyimpan standar value.'
                });
            });

            $(document).on('click', '.btnEditParameter', function() {
                $.get(`${endpoints.parameters}/${$(this).data('id')}`, function(data) {
                    openParameterModal(data);
                }).fail(function() {
                    showError('Gagal memuat detail parameter.');
                });
            });

            $(document).on('click', '.btnEditPoint', function() {
                $.get(`${endpoints.points}/${$(this).data('id')}`, function(data) {
                    openPointModal(data);
                }).fail(function() {
                    showError('Gagal memuat detail point pengukuran.');
                });
            });

            $(document).on('click', '.btnEditStandard', function() {
                $.get(`${endpoints.standards}/${$(this).data('id')}`, function(data) {
                    openStandardModal(data);
                }).fail(function() {
                    showError('Gagal memuat detail standar value.');
                });
            });

            $(document).on('click', '.btnDeleteParameter', function() {
                confirmDelete(`${endpoints.parameters}/${$(this).data('id')}`, 'Hapus parameter ini?',
                    'Data standar dan detail analisa yang terkait parameter ini ikut terhapus.',
                    function() {
                        loadParameters();
                        loadStandards();
                    });
            });

            $(document).on('click', '.btnDeletePoint', function() {
                confirmDelete(`${endpoints.points}/${$(this).data('id')}`, 'Hapus point pengukuran ini?',
                    'Data standar dan detail analisa yang terkait point ini ikut terhapus.',
                    function() {
                        loadPoints();
                        loadStandards();
                    });
            });

            $(document).on('click', '.btnDeleteStandard', function() {
                confirmDelete(`${endpoints.standards}/${$(this).data('id')}`, 'Hapus standar value ini?',
                    'Data standar akan dihapus dari master analisa WWTP.', loadStandards);
            });

            function openParameterModal(data = null) {
                $('#parameterForm')[0].reset();
                $('#parameter_master_id').val(data?.id || '');
                $('#parameter_name').val(data?.parameter_name || '');
                $('#unit').val(data?.unit || '');
                $('#parameterModalTitle').html(
                    `<i class="mdi ${data ? 'mdi-pencil-outline' : 'mdi-flask-empty-outline'} me-2"></i>${data ? 'Edit' : 'Tambah'} Parameter`
                );
                modals.parameter.show();
            }

            function openPointModal(data = null) {
                $('#pointForm')[0].reset();
                $('#point_master_id').val(data?.id || '');
                $('#point_name').val(data?.point_name || '');
                $('#pointModalTitle').html(
                    `<i class="mdi ${data ? 'mdi-pencil-outline' : 'mdi-map-marker-radius-outline'} me-2"></i>${data ? 'Edit' : 'Tambah'} Point Pengukuran`
                );
                modals.point.show();
            }

            function openStandardModal(data = null) {
                $('#standardForm')[0].reset();
                $('#standard_id').val(data?.id || '');
                refreshStandardOptions(data?.point_id, data?.parameter_id);
                $('#standard_value').val(data?.standard_value || '');
                $('#standardModalTitle').html(
                    `<i class="mdi ${data ? 'mdi-pencil-outline' : 'mdi-check-decagram-outline'} me-2"></i>${data ? 'Edit' : 'Tambah'} Standar Value`
                );
                modals.standard.show();
            }

            function saveMaster(config) {
                const originalText = config.button.html();
                config.button.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: config.id ? `${config.url}/${config.id}` : config.url,
                    method: config.id ? 'PUT' : 'POST',
                    data: config.form.serialize(),
                    success: function(response) {
                        config.modal.hide();
                        showSuccess(response.message || 'Data berhasil disimpan.');
                        config.reload();
                    },
                    error: function(xhr) {
                        showValidationError(xhr, config.fallback);
                    },
                    complete: function() {
                        config.button.prop('disabled', false).html(originalText);
                    }
                });
            }

            function loadParameters() {
                setLoading('#parameterTableBody', 5);
                $.get(endpoints.parameters, {
                    search: $('#searchParameter').val()
                }, function(data) {
                    state.parameters = data;
                    $('#totalParameters').text(data.length);
                    renderParameters(data);
                    refreshStandardOptions();
                }).fail(function() {
                    showError('Gagal memuat data parameter.');
                    renderParameters([]);
                });
            }

            function loadPoints() {
                setLoading('#pointTableBody', 4);
                $.get(endpoints.points, {
                    search: $('#searchPoint').val()
                }, function(data) {
                    state.points = data;
                    $('#totalPoints').text(data.length);
                    renderPoints(data);
                    refreshStandardOptions();
                }).fail(function() {
                    showError('Gagal memuat data point pengukuran.');
                    renderPoints([]);
                });
            }

            function loadStandards() {
                $('#standardGroups').html(loadingBlock());
                $.get(endpoints.standards, {
                    search: $('#searchStandard').val()
                }, function(data) {
                    $('#totalStandards').text(data.length);
                    renderStandards(data);
                }).fail(function() {
                    showError('Gagal memuat data standar value.');
                    renderStandards([]);
                });
            }

            function renderParameters(data) {
                const tbody = $('#parameterTableBody').empty();
                if (!data.length) return emptyTable(tbody, 5, 'Belum ada parameter.');

                data.forEach(function(item, index) {
                    tbody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td class="fw-semibold">${escapeHtml(item.parameter_name)}</td>
                            <td>${escapeHtml(item.unit || '-')}</td>
                            <td class="text-center"><span class="badge bg-light text-dark border">${item.standards_count || 0}</span></td>
                            <td class="text-center">${actionButtons('Parameter', item.id)}</td>
                        </tr>
                    `);
                });
            }

            function renderPoints(data) {
                const tbody = $('#pointTableBody').empty();
                if (!data.length) return emptyTable(tbody, 4, 'Belum ada point pengukuran.');

                data.forEach(function(item, index) {
                    tbody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td class="fw-semibold">${escapeHtml(item.point_name)}</td>
                            <td class="text-center"><span class="badge bg-light text-dark border">${item.standards_count || 0}</span></td>
                            <td class="text-center">${actionButtons('Point', item.id)}</td>
                        </tr>
                    `);
                });
            }

            function renderStandards(data) {
                const wrapper = $('#standardGroups').empty();
                if (!data.length) {
                    wrapper.html(`
                        <div class="text-center py-4 text-muted">
                            <i class="mdi mdi-inbox me-2"></i>Belum ada standar value.
                        </div>
                    `);
                    return;
                }

                const grouped = data.reduce(function(carry, item) {
                    const parameter = item.parameter || {};
                    const key = parameter.id || item.parameter_id;
                    if (!carry[key]) {
                        carry[key] = {
                            parameter,
                            rows: []
                        };
                    }
                    carry[key].rows.push(item);
                    return carry;
                }, {});

                Object.values(grouped).forEach(function(group) {
                    const parameter = group.parameter || {};
                    const rows = group.rows.map(function(item, index) {
                        const point = item.point || {};
                        return `
                            <tr>
                                <td style="width: 8%;">${index + 1}</td>
                                <td class="fw-semibold">${escapeHtml(point.point_name || '-')}</td>
                                <td class="text-center">
                                    <span class="badge bg-soft-info text-info fs-6 px-3 py-2">${formatNumber(item.standard_value)}</span>
                                </td>
                                <td class="text-center" style="width: 18%;">${actionButtons('Standard', item.id)}</td>
                            </tr>
                        `;
                    }).join('');

                    wrapper.append(`
                        <div class="standard-group">
                            <div class="standard-group-header px-3 py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">${escapeHtml(parameter.parameter_name || '-')}</div>
                                    <div class="small text-muted">Satuan: ${escapeHtml(parameter.unit || '-')}</div>
                                </div>
                                <span class="badge bg-light text-dark border">${group.rows.length} point</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 8%;">No</th>
                                            <th>Point Pengukuran</th>
                                            <th class="text-center">Nilai Standar</th>
                                            <th class="text-center" style="width: 18%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>${rows}</tbody>
                                </table>
                            </div>
                        </div>
                    `);
                });
            }

            function refreshStandardOptions(selectedPoint = null, selectedParameter = null) {
                const pointSelect = $('#point_id');
                const parameterSelect = $('#parameter_id');

                pointSelect.html('<option value="">-- Pilih Point --</option>');
                parameterSelect.html('<option value="">-- Pilih Parameter --</option>');

                state.points.forEach(function(point) {
                    pointSelect.append(
                        `<option value="${point.id}">${escapeHtml(point.point_name)}</option>`);
                });

                state.parameters.forEach(function(parameter) {
                    const unit = parameter.unit ? ` (${parameter.unit})` : '';
                    parameterSelect.append(
                        `<option value="${parameter.id}">${parameter.id} - ${escapeHtml(parameter.parameter_name)}${escapeHtml(unit)}</option>`
                    );
                });

                if (selectedPoint) pointSelect.val(selectedPoint);
                if (selectedParameter) parameterSelect.val(selectedParameter);
            }

            function confirmDelete(url, title, text, callback) {
                Swal.fire({
                    title,
                    text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url,
                        method: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            showSuccess(response.message || 'Data berhasil dihapus.');
                            callback();
                        },
                        error: function(xhr) {
                            showValidationError(xhr, 'Gagal menghapus data.');
                        }
                    });
                });
            }

            function updateContextButton() {
                const labels = {
                    parameter: '<i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Parameter',
                    point: '<i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Point',
                    standard: '<i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Standar',
                };
                $('#btnContextAdd').html(labels[state.activeTab] || labels.parameter);
            }

            function actionButtons(type, id) {
                if (!canEditDelete) return '<span class="text-muted small">Read only</span>';

                return `
                    <button type="button" class="btn btn-sm btn-outline-warning me-1 btnEdit${type}" data-id="${id}" title="Edit">
                        <i class="mdi mdi-pencil-outline"></i> Edit
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger btnDelete${type}" data-id="${id}" title="Hapus">
                        <i class="mdi mdi-trash-can-outline"></i> Hapus
                    </button>
                `;
            }

            function setLoading(selector, colspan) {
                $(selector).html(`
                    <tr>
                        <td colspan="${colspan}" class="text-center py-5">${loadingSpinner()}</td>
                    </tr>
                `);
            }

            function loadingBlock() {
                return `<div class="text-center py-5">${loadingSpinner()}</div>`;
            }

            function loadingSpinner() {
                return `
                    <div class="spinner-border text-info" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                `;
            }

            function emptyTable(tbody, colspan, message) {
                tbody.html(`
                    <tr>
                        <td colspan="${colspan}" class="text-center py-4 text-muted">
                            <i class="mdi mdi-inbox me-2"></i>${message}
                        </td>
                    </tr>
                `);
            }

            function formatNumber(value) {
                if (value === null || value === undefined || value === '') return '-';
                const parsed = parseFloat(value);
                return parsed % 1 === 0 ? parsed.toFixed(0) : parsed.toString();
            }

            function capitalize(value) {
                return value.charAt(0).toUpperCase() + value.slice(1);
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function showSuccess(message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: message,
                    timer: 1800,
                    showConfirmButton: false
                });
            }

            function showError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: message,
                    confirmButtonColor: '#299cdb'
                });
            }

            function showValidationError(xhr, fallback) {
                const response = xhr.responseJSON;
                let message = fallback;

                if (response && response.errors) {
                    message = Object.values(response.errors).flat().join('<br>');
                } else if (response && response.message) {
                    message = response.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: message,
                    confirmButtonColor: '#299cdb'
                });
            }
        });
    </script>
@endsection
