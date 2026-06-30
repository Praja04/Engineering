@extends('layouts.app')

@section('title', 'Tambah Data Sludge WWTP')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">Tambah Data Sludge WWTP</h4>
                            <p class="text-muted mb-0">Input data pengelolaan lumpur per shift</p>
                        </div>
                        <div>
                            <a href="{{ url('/wwtp/data_sludge') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODE SELECTOR -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card mode-card active-mode" id="mode-sludge" style="cursor:pointer;">
                        <div class="card-body text-center">
                            <i class="mdi mdi-delete-variant fs-2 text-warning"></i>
                            <h5 class="mt-2">Sludge Harian</h5>
                            <p class="text-muted mb-0">Input drain, hasil & running hour per shift</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mode-card" id="mode-pengangkutan" style="cursor:pointer;">
                        <div class="card-body text-center">
                            <i class="mdi mdi-truck-fast fs-2 text-primary"></i>
                            <h5 class="mt-2">Pengangkutan Sludge</h5>
                            <p class="text-muted mb-0">Input total pengangkutan per minggu</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================================ -->
            <!-- FORM 1: SLUDGE HARIAN            -->
            <!-- ================================ -->
            <div id="section-sludge">
                <div class="card">
                    <div class="card-body">
                        <form id="sludgeForm">

                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal" class="form-label fw-semibold">
                                        Tanggal <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                    <div class="form-text">Tanggal pencatatan data</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="shift" class="form-label fw-semibold">
                                        Shift <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="shift" name="shift" required>
                                        <option value="">-- Pilih Shift --</option>
                                        <option value="shift1">Shift 1 (06:00 - 14:00)</option>
                                        <option value="shift2">Shift 2 (14:00 - 22:00)</option>
                                        <option value="shift3">Shift 3 (22:00 - 06:00)</option>
                                    </select>
                                    <div class="form-text">Shift kerja yang bertugas</div>
                                </div>
                                <div class="row mb-4" id="daily_approval_row" style="display: none;">
                                    <div class="col-md-6 mb-3">
                                        <label for="daily_foreman_id" class="form-label fw-semibold">
                                            Pilih Foreman <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="daily_foreman_id" name="foreman_id">
                                            <option value="">-- Pilih Foreman --</option>
                                        </select>
                                        <div class="form-text">Foreman yang akan memverifikasi laporan ini</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="daily_supervisor_id" class="form-label fw-semibold">
                                            Pilih Supervisor <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="daily_supervisor_id" name="supervisor_id">
                                            <option value="">-- Pilih Supervisor --</option>
                                        </select>
                                        <div class="form-text">Supervisor yang akan menyetujui laporan ini</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sludge Data -->
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1 text-warning">
                                            <i class="mdi mdi-delete-variant me-2"></i>Data Sludge Management
                                        </h5>
                                        <p class="text-muted mb-0">Input data pengelolaan lumpur hasil proses WWTP</p>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <!-- Drain Lumpur -->
                                    <div class="col-md-6">
                                        <div class="card border border-warning">
                                            <div class="card-body">
                                                <label for="drain_lumpur" class="form-label fw-semibold">
                                                    <i class="mdi mdi-hydraulic-oil-level text-warning me-1"></i>
                                                    Drain Lumpur <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        id="drain_lumpur" name="drain_lumpur" min="0"
                                                        placeholder="0.00" required>
                                                    <span class="input-group-text bg-light">m³</span>
                                                </div>
                                                <small class="form-text text-muted">Volume lumpur yang dikeluarkan dari
                                                    sistem</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Running Hour SCP -->
                                    <div class="col-md-6">
                                        <div class="card border border-warning">
                                            <div class="card-body">
                                                <label for="running_hour_scp" class="form-label fw-semibold">
                                                    <i class="mdi mdi-clock-outline text-warning me-1"></i>
                                                    Running Hour SCP <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        id="running_hour_scp" name="running_hour_scp" min="0"
                                                        placeholder="0.00" required>
                                                    <span class="input-group-text bg-light">jam</span>
                                                </div>
                                                <small class="form-text text-muted">Waktu operasional Sludge Collection
                                                    Pump</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hasil Lumpur -->
                                    <div class="col-md-6">
                                        <div class="card border border-warning">
                                            <div class="card-body">
                                                <label for="hasil_lumpur" class="form-label fw-semibold">
                                                    <i class="mdi mdi-water-percent text-warning me-1"></i>
                                                    Hasil Lumpur <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        id="hasil_lumpur" name="hasil_lumpur" min="0"
                                                        placeholder="0.00" required>
                                                    <span class="input-group-text bg-light">ton</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sludge Content -->
                                    <div class="col-md-6">
                                        <div class="card border border-warning">
                                            <div class="card-body">
                                                <label for="sludge_content" class="form-label fw-semibold">
                                                    <i class="mdi mdi-water-percent text-warning me-1"></i>
                                                    Content Sludge (%) <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control"
                                                        id="sludge_content" name="sludge_content" min="0"
                                                        placeholder="0.00" required>
                                                    <span class="input-group-text bg-light">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Information Alert -->
                            <div class="alert alert-warning border-0 mb-4" role="alert">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="mdi mdi-information fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <strong>Catatan Penting:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Data sludge dapat diinput maksimal <strong>3x per hari</strong> (sesuai
                                                shift 1, 2, dan 3)</li>
                                            <li>Setiap shift hanya dapat diinput <strong>1x per tanggal</strong></li>
                                            <li>Pastikan data yang diinput sudah benar sebelum menyimpan</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-light">
                                    <i class="mdi mdi-refresh me-1"></i> Reset Form
                                </button>
                                <button type="submit" class="btn btn-warning" id="btnSubmitSludge">
                                    <i class="mdi mdi-content-save me-1"></i> Simpan Data Sludge
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <!-- ================================ -->
            <!-- FORM 2: PENGANGKUTAN SLUDGE      -->
            <!-- ================================ -->
            <div id="section-pengangkutan" style="display:none;">
                <div class="card">
                    <div class="card-body">
                        <form id="pengangkutanForm">

                            <h5 class="mb-4 text-primary">
                                <i class="mdi mdi-truck-fast me-1"></i> Input Pengangkutan Sludge
                            </h5>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_pengangkutan" class="form-label fw-semibold">
                                        Tanggal <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="tanggal_pengangkutan"
                                        name="tanggal_pengangkutan" required>
                                    <div class="form-text">Tanggal pengangkutan dilakukan</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="jumlah_pengangkutan" class="form-label fw-semibold">
                                        Jumlah Pengangkutan <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control"
                                            id="jumlah_pengangkutan" name="jumlah_pengangkutan" min="0"
                                            placeholder="0.00" required>
                                        <span class="input-group-text bg-light">ton/mobil</span>
                                    </div>
                                    <div class="form-text">Total volume sludge yang diangkut</div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-light">
                                    <i class="mdi mdi-refresh me-1"></i> Reset Form
                                </button>
                                <button type="submit" class="btn btn-primary" id="btnSubmitPengangkutan">
                                    <i class="mdi mdi-content-save me-1"></i> Simpan Pengangkutan
                                </button>
                            </div>

                        </form>
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
                    <p class="text-muted mb-4" id="successMessage">Data telah berhasil disimpan ke sistem.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Tambah Data Lagi
                        </button>
                        <a href="{{ url('/wwtp/data_sludge') }}" class="btn btn-primary">
                            Lihat Semua Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card.border-warning {
            border-color: #f1b44c !important;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.15rem rgba(241, 180, 76, 0.25);
            border-color: #f1b44c;
        }

        .input-group-text {
            background-color: #f8f9fa;
            color: #495057;
        }

        .btn-warning {
            background-color: #f1b44c;
            border-color: #f1b44c;
            color: #fff;
        }

        .btn-warning:hover {
            background-color: #e6a935;
            border-color: #e6a935;
            color: #fff;
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

        .alert-warning {
            background-color: rgba(241, 180, 76, 0.1);
            border-left: 4px solid #f1b44c;
        }

        .mode-card {
            border: 2px solid transparent;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .mode-card:hover {
            border-color: #ddd;
        }

        .active-mode {
            border-color: #f1b44c !important;
            box-shadow: 0 0 10px rgba(241, 180, 76, 0.3);
        }

        #mode-pengangkutan.active-mode {
            border-color: #556ee6 !important;
            box-shadow: 0 0 10px rgba(85, 110, 230, 0.3);
        }
    </style>

    <script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            const today = new Date().toISOString().split('T')[0];
            $('#tanggal').val(today);
            $('#tanggal_pengangkutan').val(today);

            function checkDailyApproval() {
                const tanggal = $('#tanggal').val();
                if (!tanggal) {
                    $('#daily_approval_row').hide();
                    $('#daily_foreman_id, #daily_supervisor_id').val('').prop('required', false);
                    $('#btnSubmitSludge').prop('disabled', false);
                    return;
                }

                $.ajax({
                    url: "{{ url('wwtp-approval/check') }}",
                    method: 'GET',
                    data: {
                        tanggal: tanggal
                    },
                    success: function(response) {
                        if (response.approval_exists) {
                            $('#daily_approval_row').hide();
                            $('#daily_foreman_id, #daily_supervisor_id').val('').prop('required',
                                false);

                            // if (response.approval.status === 'approved_foreman' || response.approval
                            //     .status === 'approved_supervisor') {
                            //     Swal.fire({
                            //         icon: 'warning',
                            //         title: 'Peringatan',
                            //         text: 'Laporan harian untuk tanggal ini sudah disetujui dan terkunci.',
                            //         confirmButtonColor: '#3085d6'
                            //     });
                            //     $('#btnSubmitSludge').prop('disabled', true);
                            // } else {
                            //     $('#btnSubmitSludge').prop('disabled', false);
                            // }
                        } else {
                            $('#btnSubmitSludge').prop('disabled', false);
                            $('#daily_approval_row').show();
                            $('#daily_foreman_id, #daily_supervisor_id').prop('required', true);

                            const foremanSelect = $('#daily_foreman_id');
                            foremanSelect.html('<option value="">-- Pilih Foreman --</option>');
                            response.foremen.forEach(function(u) {
                                foremanSelect.append(
                                    `<option value="${u.id}">${u.username}</option>`);
                            });

                            const supervisorSelect = $('#daily_supervisor_id');
                            supervisorSelect.html('<option value="">-- Pilih Supervisor --</option>');
                            response.supervisors.forEach(function(u) {
                                supervisorSelect.append(
                                    `<option value="${u.id}">${u.username}</option>`);
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Gagal mengecek status approval harian:', xhr);
                    }
                });
            }

            function checkFilledShifts() {
                const tanggal = $('#tanggal').val();
                if (!tanggal) {
                    $('#shift option').prop('disabled', false);
                    return;
                }

                $.ajax({
                    url: "/api/wwtp-sludge/filled-shifts",
                    method: 'GET',
                    data: {
                        tanggal: tanggal
                    },
                    success: function(response) {
                        if (response.success && response.filled_shifts) {
                            const currentSelected = $('#shift').val();

                            // Reset semua option
                            $('#shift option').each(function() {
                                const originalText = $(this).data('original-text') || $(this)
                                    .text();

                                $(this)
                                    .data('original-text', originalText)
                                    .text(originalText)
                                    .prop('disabled', false);
                            });

                            // Disable options present in response.filled_shifts
                            response.filled_shifts.forEach(function(shift) {
                                const option = $(`#shift option[value="${shift}"]`);

                                option
                                    .prop('disabled', true)
                                    .text(`${option.data('original-text')} (Sudah Terisi)`);
                            });

                            // If currently selected shift is now disabled, reset it
                            if (response.filled_shifts.includes(currentSelected)) {
                                $('#shift').val('');
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Gagal mengambil data shift terisi:', xhr);
                    }
                });
            }

            $('#tanggal').on('change', checkDailyApproval);
            $('#tanggal').on('change', checkFilledShifts);
            checkDailyApproval();
            checkFilledShifts();

            // ==============================
            // MODE SWITCHER
            // ==============================
            $('#mode-sludge').on('click', function() {
                $('#mode-sludge').addClass('active-mode');
                $('#mode-pengangkutan').removeClass('active-mode');
                $('#section-sludge').show();
                $('#section-pengangkutan').hide();
            });

            $('#mode-pengangkutan').on('click', function() {
                $('#mode-pengangkutan').addClass('active-mode');
                $('#mode-sludge').removeClass('active-mode');
                $('#section-sludge').hide();
                $('#section-pengangkutan').show();
            });

            // ==============================
            // SUBMIT FORM 1: SLUDGE HARIAN
            // ==============================
            $('#sludgeForm').on('submit', function(e) {
                e.preventDefault();

                const btn = $('#btnSubmitSludge');
                const originalText = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                const formData = {
                    _token: "{{ csrf_token() }}",
                    tanggal: $('#tanggal').val(),
                    shift: $('#shift').val(),
                    drain_lumpur: $('#drain_lumpur').val(),
                    running_hour_scp: $('#running_hour_scp').val(),
                    hasil_lumpur: $('#hasil_lumpur').val(),
                    sludge_content: $('#sludge_content').val(),
                    foreman_id: $('#daily_foreman_id').val() || null,
                    supervisor_id: $('#daily_supervisor_id').val() || null,
                };

                $.ajax({
                    url: "{{ url('wwtp-sludge') }}",
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#successMessage').html(response.message ||
                            'Data sludge WWTP telah berhasil disimpan ke sistem.');
                        $('#successModal').modal('show');

                        $('#sludgeForm')[0].reset();
                        $('#tanggal').val(today);
                        checkDailyApproval();
                        checkFilledShifts();
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
                            confirmButtonColor: '#3085d6'
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Reset handler sludge form
            $('#sludgeForm').on('reset', function() {
                setTimeout(function() {
                    $('#tanggal').val(today);
                    checkFilledShifts();
                }, 10);
            });

            // ==============================
            // SUBMIT FORM 2: PENGANGKUTAN
            // ==============================
            $('#pengangkutanForm').on('submit', function(e) {
                e.preventDefault();

                const btn = $('#btnSubmitPengangkutan');
                const originalText = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                const formData = {
                    tanggal: $('#tanggal_pengangkutan').val(),
                    jumlah_pengangkutan: $('#jumlah_pengangkutan').val()
                };

                $.ajax({
                    url: "{{ url('api/wwtp-sludge/pengangkutan') }}",
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#successMessage').html(response.message ||
                            'Data pengangkutan sludge telah berhasil disimpan ke sistem.');
                        $('#successModal').modal('show');

                        $('#pengangkutanForm')[0].reset();
                        $('#tanggal_pengangkutan').val(today);
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
                            confirmButtonColor: '#3085d6'
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Reset handler pengangkutan form
            $('#pengangkutanForm').on('reset', function() {
                setTimeout(function() {
                    $('#tanggal_pengangkutan').val(today);
                }, 10);
            });

        });
    </script>
@endsection
