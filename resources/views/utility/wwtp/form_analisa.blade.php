@extends('layouts.app')

@section('title', 'Tambah Data Analisa WWTP')

@section('styles')
    <style>
        .analisa-shell {
            max-width: 1180px;
            margin: 0 auto;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.15rem rgba(41, 156, 219, 0.25);
            border-color: #299cdb;
        }

        .avatar-lg {
            width: 5rem;
            height: 5rem;
        }

        .avatar-title {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .entry-panel {
            border: 1px solid #eef2f7;
            border-radius: 8px;
            /* background: #fff; */
        }

        .entry-panel-header {
            border-bottom: 1px solid #eef2f7;
            /* background: #f8fafc; */
            border-radius: 8px 8px 0 0;
        }

        .section-label {
            color: #667085;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .parameter-summary {
            border: 1px solid #e6f2fb;
            border-radius: 8px;
        }

        .metric-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border-radius: 999px;
            padding: .35rem .7rem;
            font-size: .78rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .metric-chip.info {
            background: rgba(41, 156, 219, .12);
            color: #2078aa;
        }

        .metric-chip.neutral {
            background: #f2f4f7;
            color: #475467;
        }

        .metric-chip.success {
            background: rgba(10, 179, 156, .12);
            color: #087d6f;
        }

        .table-input {
            margin-bottom: 0;
        }

        .table-input thead th {
            background: #f8fafc;
            color: #475467;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
            border-bottom: 1px solid #e9ecef;
            white-space: nowrap;
        }

        .table-input td {
            vertical-align: middle;
            border-color: #eef2f7;
        }

        .table-input input[type="number"] {
            min-width: 170px;
            max-width: 220px;
            margin-left: auto;
        }

        .point-index {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #f2f4f7;
            color: #475467;
            font-weight: 700;
        }

        .empty-state {
            min-height: 220px;
            border: 1px dashed #cfe4f4;
            border-radius: 8px;
            background: #f8fbfd;
        }

        .empty-state-icon {
            width: 56px;
            height: 56px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(41, 156, 219, .12);
            color: #299cdb;
        }

        .form-actions {
            position: sticky;
            bottom: 0;
            z-index: 3;
            backdrop-filter: blur(6px);
            border-top: 1px solid #eef2f7;
        }

        @media (max-width: 767.98px) {
            .page-title-box {
                gap: 1rem;
            }

            .table-input input[type="number"] {
                min-width: 150px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="analisa-shell">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-1">Tambah Data Analisa WWTP</h4>
                                <p class="text-muted mb-0">Input hasil analisa air limbah berdasarkan parameter dan point
                                    pengukuran.</p>
                            </div>
                            <div>
                                <a href="{{ url('/wwtp/data_analisa') }}" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Data
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <form id="analisaForm">
                                    @csrf

                                    <div class="p-4 pb-3">
                                        <div class="entry-panel">
                                            <div class="entry-panel-header px-4 py-3">
                                                <div
                                                    class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                    <div>
                                                        <div class="section-label mb-1">Header Analisa</div>
                                                        <h5 class="mb-0">Tanggal dan Parameter</h5>
                                                    </div>
                                                    <span class="metric-chip neutral" id="pointCountChip">
                                                        <i class="mdi mdi-map-marker-radius-outline"></i>
                                                        {{ $points->count() }} point
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="p-4">
                                                <div class="row g-3 align-items-end">
                                                    <div class="col-lg-6">
                                                        <label for="analisa_date" class="form-label fw-semibold">
                                                            Tanggal Analisa <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <i class="mdi mdi-calendar-month-outline"></i>
                                                            </span>
                                                            <input type="date" class="form-control" id="analisa_date"
                                                                name="analisa_date" required>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label for="parameter_id" class="form-label fw-semibold">
                                                            Parameter <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <i class="mdi mdi-flask-empty-outline"></i>
                                                            </span>
                                                            <select class="form-select" id="parameter_id"
                                                                name="parameter_id" required>
                                                                <option value="">-- Pilih Parameter --</option>
                                                                @foreach ($parameters as $param)
                                                                    <option value="{{ $param->id }}">
                                                                        {{ $param->parameter_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="parameter-summary mt-4 px-3 py-3" id="parameterSummary"
                                                    style="display: none;">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                        <div>
                                                            <div class="section-label mb-1">Parameter Dipilih</div>
                                                            <div class="fw-bold fs-5" id="selectedParameterName">-</div>
                                                        </div>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <span class="metric-chip info" id="selectedParameterUnit">
                                                                <i class="mdi mdi-ruler-square"></i> -
                                                            </span>
                                                            <span class="metric-chip success" id="filledPointChip">
                                                                <i class="mdi mdi-table-check"></i> Siap input
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Shift Selection -->
                                            {{-- <div class="col-md-4 mb-3">
                                            <label for="shift" class="form-label fw-semibold">
                                                Shift <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="shift" name="shift" required>
                                                <option value="">-- Pilih Shift --</option>
                                                <option value="1">Shift 1</option>
                                                <option value="2">Shift 2</option>
                                                <option value="3">Shift 3</option>
                                            </select>
                                        </div> --}}

                                            <!-- Area -->
                                            {{-- <div class="col-md-4 mb-3">
                                            <label for="area" class="form-label fw-semibold">
                                                Area
                                            </label>
                                            <input type="text" class="form-control" id="area" name="area" placeholder="Masukkan Area (Opsional, e.g. WWTP 1)">
                                        </div> --}}
                                        </div>
                                    </div>

                                    <!-- Points & Standard Values section -->
                                    <div class="px-4 pb-4">
                                        <div class="entry-panel">
                                            <div class="entry-panel-header px-4 py-3">
                                                <div
                                                    class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                    <div>
                                                        <div class="section-label mb-1">Hasil Analisa</div>
                                                        <h5 class="mb-0">Point Pengukuran</h5>
                                                    </div>
                                                    <a href="{{ url('/wwtp/manage_standar_analisa') }}"
                                                        class="btn btn-soft-info btn-sm">
                                                        <i class="mdi mdi-database-cog-outline me-1"></i> Master Data
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- Dynamic points input table -->
                                            <div id="points-container" class="table-responsive" style="display: none;">
                                                <table class="table table-hover table-input align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">No</th>
                                                            <th>Point Pengukuran</th>
                                                            <th class="text-center">Standar</th>
                                                            <th class="text-center">Satuan</th>
                                                            <th class="text-end">Hasil Analisa</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="points-tbody">
                                                        <!-- Populated via Javascript -->
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Placeholder if no parameter is selected -->
                                            <div id="no-param-alert"
                                                class="empty-state d-flex align-items-center justify-content-center text-center m-4"
                                                role="alert">
                                                <div>
                                                    <div class="empty-state-icon mb-3">
                                                        <i class="mdi mdi-flask-plus-outline fs-2"></i>
                                                    </div>
                                                    <h5 class="mb-1">Pilih parameter</h5>
                                                    <p class="text-muted mb-0">Daftar point pengukuran akan tampil setelah
                                                        parameter dipilih.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="form-actions d-flex justify-content-end gap-2 p-4">
                                        <button type="reset" class="btn btn-outline-danger">
                                            <i class="mdi mdi-refresh me-1"></i> Reset Form
                                        </button>
                                        <button type="submit" class="btn btn-info text-white" id="btnSubmitAnalisa">
                                            <i class="mdi mdi-content-save me-1"></i> Simpan Data Analisa
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <div class="avatar-lg mx-auto mb-4">
                        <div class="avatar-title bg-soft-success text-success rounded-circle">
                            <i class="mdi mdi-check-circle fs-1"></i>
                        </div>
                    </div>
                    <h4 class="mb-3">Data Berhasil Disimpan!</h4>
                    <p class="text-muted mb-4" id="successMessage">Data analisa WWTP telah berhasil disimpan ke sistem.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-info" data-bs-dismiss="modal">
                            Tambah Data Lagi
                        </button>
                        <a href="{{ url('/wwtp/data_analisa') }}" class="btn btn-info text-white">
                            Lihat Semua Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const today = new Date().toISOString().split('T')[0];
            $('#analisa_date').val(today);

            // Fetch points, standards and parameter definitions from backend
            const points = @json($points);
            const standards = @json($standards);
            const parameters = @json($parameters);

            // Construct maps for parameters for easy lookup
            const parameterMap = {};
            parameters.forEach(function(p) {
                parameterMap[p.id] = p;
            });

            // Store original option text in data attribute for all options in parameter_id select
            $('#parameter_id option').each(function() {
                const opt = $(this);
                if (opt.val()) {
                    opt.data('original-text', opt.text());
                }
            });

            // Function to rebuild the points input table based on the selected parameter
            function rebuildPointsTable() {
                const parameterId = $('#parameter_id').val();
                const tbody = $('#points-tbody');

                console.log(parameterId);

                tbody.empty();

                if (parameterId === '' || parameterId === null) {
                    $('#points-container').hide();
                    $('#no-param-alert').removeClass('d-none');
                    $('#parameterSummary').hide();
                    return;
                }

                $('#no-param-alert').addClass('d-none');
                $('#points-container').show();
                $('#parameterSummary').show();

                const param = parameterMap[parameterId];
                const unit = param ? param.unit : '';
                const unitDisplay = unit ? '' + unit : '-';
                const parameterName = param ? param.parameter_name : '-';

                $('#selectedParameterName').text(parameterName);
                $('#selectedParameterUnit').html(`<i class="mdi mdi-ruler-square"></i> ${escapeHtml(unitDisplay)}`);
                $('#filledPointChip').html(`<i class="mdi mdi-table-check"></i> ${points.length} point siap input`);

                points.forEach(function(point, index) {
                    const key = point.id + '_' + parameterId;
                    const stdVal = standards[key];
                    let stdDisplay = '';

                    if (stdVal !== undefined && stdVal !== null) {
                        const parsedStd = parseFloat(stdVal);
                        const displayStd = parsedStd % 1 === 0 ? parsedStd.toFixed(0) : parsedStd
                            .toString();
                        stdDisplay =
                            `<span class="metric-chip info">${displayStd} ${escapeHtml(unitDisplay)}</span>`;
                    } else {
                        stdDisplay = `<span class="metric-chip neutral">Belum diset</span>`;
                    }

                    const row = `
                        <tr>
                            <td class="text-center"><span class="point-index">${index + 1}</span></td>
                            <td>
                                <div class="fw-semibold text-dark">${escapeHtml(point.point_name)}</div>
                            </td>
                            <td class="text-center">${stdDisplay}</td>
                            <td class="text-center">
                                <span class="metric-chip neutral">${escapeHtml(unitDisplay)}</span>
                            </td>
                            <td class="text-end">
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control"
                                        name="hasil_analisa[${point.id}][${parameterId}]"
                                        placeholder="0.00"
                                        min="0">
                                    <span class="input-group-text">${escapeHtml(unitDisplay)}</span>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }

            // Function to fetch and disable already filled parameters
            function checkFilledParameters() {
                const date = $('#analisa_date').val();
                // const shift = $('#shift').val();

                // If any of the required check parameters is missing, enable all parameters
                if (!date) {
                    $('#parameter_id option').each(function() {
                        const opt = $(this);
                        if (opt.val()) {
                            opt.prop('disabled', false);
                            opt.text(opt.data('original-text') || opt.text());
                        }
                    });
                    return;
                }

                // Call API to check filled parameters
                $.ajax({
                    url: "{{ url('api/wwtp-analisa/check-filled') }}",
                    method: 'GET',
                    data: {
                        analisa_date: date,
                        //shift: shift
                    },
                    success: function(filledIds) {
                        const selectedVal = $('#parameter_id').val();
                        let currentSelectedIsDisabled = false;

                        $('#parameter_id option').each(function() {
                            const opt = $(this);
                            const val = parseInt(opt.val());

                            if (val) {
                                const originalText = opt.data('original-text') || opt.text();
                                if (filledIds.includes(val)) {
                                    opt.prop('disabled', true);
                                    opt.text(originalText + ' - (Sudah terisi)');
                                    if (selectedVal == val) {
                                        currentSelectedIsDisabled = true;
                                    }
                                } else {
                                    opt.prop('disabled', false);
                                    opt.text(originalText);
                                }
                            }
                        });

                        // If the currently selected parameter is disabled, reset choice
                        if (currentSelectedIsDisabled) {
                            $('#parameter_id').val('');
                            rebuildPointsTable();
                        }
                    },
                    error: function() {
                        console.error('Gagal mengambil data parameter terisi.');
                    }
                });
            }

            // Change event listeners to check filled parameters
            $('#analisa_date').on('change', function() {
                checkFilledParameters();
            });

            checkFilledParameters();

            // Change event listener to rebuild points table
            $('#parameter_id').on('change', function() {
                rebuildPointsTable();
            });

            // Submit Form
            $('#analisaForm').on('submit', function(e) {
                e.preventDefault();

                const btn = $('#btnSubmitAnalisa');
                const originalText = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                const formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('wwtp-analisa.store') }}",
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#successMessage').html(response.message ||
                            'Data analisa WWTP telah berhasil disimpan ke sistem.');
                        $('#successModal').modal('show');

                        // Clear the selected parameter and point inputs
                        $('#parameter_id').val('');
                        rebuildPointsTable();

                        // Recheck filled parameters for today
                        checkFilledParameters();
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON;
                        let message = 'Terjadi kesalahan saat menyimpan data!';

                        if (error && error.message) {
                            message = error.message;
                        } else if (error && error.errors) {
                            message = Object.values(error.errors).flat().join('<br>');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: message,
                            confirmButtonColor: '#299cdb'
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            $('#analisaForm').on('reset', function() {
                setTimeout(function() {
                    $('#analisa_date').val(today);
                    checkFilledParameters();
                    rebuildPointsTable();
                }, 10);
            });

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }
        });
    </script>
@endsection
