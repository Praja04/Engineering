@extends('layouts.app')

@section('title', 'Tambah Data Analisa WWTP')

@section('styles')
    <style>
        .card.border-info {
            border-color: #299cdb !important;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.15rem rgba(41, 156, 219, 0.25);
            border-color: #299cdb;
        }

        .input-group-text {
            background-color: #f8f9fa;
            color: #495057;
        }

        .btn-info {
            background-color: #299cdb;
            border-color: #299cdb;
        }

        .btn-info:hover {
            background-color: #2284ba;
            border-color: #2284ba;
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

        .bg-soft-success {
            background-color: rgba(10, 179, 156, 0.1) !important;
        }

        .alert-info {
            background-color: rgba(41, 156, 219, 0.1);
            border-left: 4px solid #299cdb;
        }

        .table-input td {
            vertical-align: middle;
        }

        .table-input input[type="number"] {
            min-width: 120px;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">Tambah Data Analisa WWTP</h4>
                            <p class="text-muted mb-0">Input hasil analisa parameter air limbah per parameter</p>
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
                        <div class="card-body p-4">
                            <form id="analisaForm">
                                @csrf

                                <!-- Header Data section -->
                                <div class="mb-4">
                                    <div class="row g-3">
                                        <!-- Date Selection -->
                                        <div class="col-md-6 mb-3">
                                            <label for="analisa_date" class="form-label fw-semibold">
                                                Tanggal Analisa <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control" id="analisa_date" name="analisa_date"
                                                required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="parameter_id" class="form-label fw-semibold">
                                                Parameter <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="parameter_id" name="parameter_id" required>
                                                <option value="">-- Pilih Parameter --</option>
                                                @foreach ($parameters as $param)
                                                    <option value="{{ $param->id }}">{{ $param->parameter_name }}
                                                    </option>
                                                @endforeach
                                            </select>

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
                                <div class="mb-4">
                                    <h5 class="mb-3 text-info border-bottom pb-2">
                                        <i class="mdi mdi-table-large me-2"></i>Hasil Analisa per Point Pengukuran
                                    </h5>

                                    <!-- Dynamic points input table -->
                                    <div id="points-container" class="table-responsive" style="display: none;">
                                        <table class="table table-bordered table-hover table-input align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 30%;">Point Pengukuran</th>
                                                    <th style="width: 25%;" class="text-center">Standard</th>
                                                    <th style="width: 20%;" class="text-center">Satuan</th>
                                                    <th style="width: 25%;" class="text-center">Hasil Analisa</th>
                                                </tr>
                                            </thead>
                                            <tbody id="points-tbody">
                                                <!-- Populated via Javascript -->
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Placeholder if no parameter is selected -->
                                    <div id="no-param-alert" class="alert alert-info border-0" role="alert">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-information-outline fs-4 me-2 text-info"></i>
                                            <span>Silakan pilih parameter terlebih dahulu untuk mengisi hasil
                                                analisa.</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
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
                    <p class="text-muted mb-4" id="successMessage">Data analisa WWTP telah berhasil disimpan ke sistem.</p>
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

                tbody.empty();

                if (!parameterId) {
                    $('#points-container').hide();
                    $('#no-param-alert').show();
                    return;
                }

                $('#no-param-alert').hide();
                $('#points-container').show();

                const param = parameterMap[parameterId];
                const unit = param ? param.unit : '';
                const unitDisplay = unit ? '' + unit : '-';

                points.forEach(function(point) {
                    const key = point.id + '_' + parameterId;
                    const stdVal = standards[key];
                    let stdDisplay = '';

                    if (stdVal !== undefined && stdVal !== null) {
                        const parsedStd = parseFloat(stdVal);
                        const displayStd = parsedStd % 1 === 0 ? parsedStd.toFixed(0) : parsedStd
                            .toString();
                        stdDisplay =
                            `<span class="badge bg-soft-info text-info fs-6 px-3 py-2">${displayStd}${unitDisplay}</span>`;
                    } else {
                        stdDisplay = `<span class="text-muted small">-</span>`;
                    }

                    const row = `
                        <tr>
                            <td class="fw-semibold text-dark">${point.point_name}</td>
                            <td class="text-center">${stdDisplay}</td>
                            <td class="text-center">${unitDisplay}</td>
                            <td>
                                <input type="number" step="0.01" class="form-control" 
                                    name="hasil_analisa[${point.id}][${parameterId}]" 
                                    placeholder="Masukkan hasil" 
                                    min="0" style="width: 250px;margin:0 auto">
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
                    url: "{{ url('api/wwtp-analisa') }}",
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
        });
    </script>
@endsection
