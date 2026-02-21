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

        <!-- Form Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-xs">
                                    <div class="avatar-title bg-warning rounded-circle">
                                        <i class="mdi mdi-delete-variant"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0">Data Sludge WWTP Harian</h5>
                                <p class="text-muted mb-0">Input data drain lumpur dan running hour per shift</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge bg-warning">Harian</span>
                            </div>
                        </div>
                    </div>
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
                                        <option value="1">Shift 1 (06:00 - 14:00)</option>
                                        <option value="2">Shift 2 (14:00 - 22:00)</option>
                                        <option value="3">Shift 3 (22:00 - 06:00)</option>
                                    </select>
                                    <div class="form-text">Shift kerja yang bertugas</div>
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
                                                    <i class="mdi mdi-hydraulic-oil-level text-warning me-1"></i>Drain Lumpur <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control" id="drain_lumpur" name="drain_lumpur" min="0" placeholder="0.00" required>
                                                    <span class="input-group-text bg-light">m³</span>
                                                </div>
                                                <small class="form-text text-muted">Volume lumpur yang dikeluarkan dari sistem</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Running Hour SCP -->
                                    <div class="col-md-6">
                                        <div class="card border border-warning">
                                            <div class="card-body">
                                                <label for="running_hour_scp" class="form-label fw-semibold">
                                                    <i class="mdi mdi-clock-outline text-warning me-1"></i>Running Hour SCP <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control" id="running_hour_scp" name="running_hour_scp" min="0" placeholder="0.00" required>
                                                    <span class="input-group-text bg-light">jam</span>
                                                </div>
                                                <small class="form-text text-muted">Waktu operasional Sludge Collection Pump</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hasil lumpur  -->
                                    <div class="col-md-6">
                                        <div class="card border border-warning">
                                            <div class="card-body">
                                                <label for="hasil_lumpur" class="form-label fw-semibold">
                                                    <i class="mdi mdi-water-percent text-warning me-1"></i>Hasil Lumpur <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control" id="hasil_lumpur" name="hasil_lumpur" min="0" placeholder="0.00" required>
                                                    <span class="input-group-text bg-light">ton</span>
                                                </div>
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
                                    <li>Data sludge dapat diinput maksimal <strong>3x per hari</strong> (sesuai shift 1, 2, dan 3)</li>
                                    <li>Setiap shift hanya dapat diinput <strong>1x per tanggal</strong></li>
                                    <li>Pastikan data yang diinput sudah benar sebelum menyimpan</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-light">
                                    <i class="mdi mdi-refresh me-1"></i> Reset Form
                                </button>
                                <button type="submit" class="btn btn-warning" id="submitSludgeForm">
                                    <i class="mdi mdi-content-save me-1"></i> Simpan Data Sludge
                                </button>
                            </div>
                        </div>
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
                <p class="text-muted mb-4" id="successMessage">Data sludge WWTP telah berhasil disimpan ke sistem.</p>
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
    /* Card Styles */
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

    .btn-outline-warning {
        color: #f1b44c;
        border-color: #f1b44c;
    }

    .btn-outline-warning:hover {
        background-color: #f1b44c;
        color: white;
    }

    .avatar-xs {
        width: 2rem;
        height: 2rem;
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

    .form-text {
        font-size: 0.8125rem;
    }

    .alert-warning {
        background-color: rgba(241, 180, 76, 0.1);
        border-left: 4px solid #f1b44c;
    }
</style>

<script src="{{ asset('material/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Set default date to today
        const today = new Date().toISOString().split('T')[0];
        $('#tanggal').val(today);

        // Submit sludge form
        $('#sludgeForm').on('submit', function(e) {
            e.preventDefault();

            const btnSubmit = $('#submitSludgeForm');
            const originalText = btnSubmit.html();
            btnSubmit.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

            const formData = {
                tanggal: $('#tanggal').val(),
                shift: $('#shift').val(),
                drain_lumpur: $('#drain_lumpur').val(),
                running_hour_scp: $('#running_hour_scp').val(),
                hasil_lumpur: $('#hasil_lumpur').val()
            };

            $.ajax({
                url: "{{ url('api/wwtp-sludge') }}",
                method: 'POST',
                data: formData,
                success: function(response) {
                    // Show success modal
                    $('#successMessage').html(response.message || 'Data sludge WWTP telah berhasil disimpan ke sistem.');
                    $('#successModal').modal('show');

                    // Reset form
                    $('#sludgeForm')[0].reset();
                    $('#tanggal').val(today);

                    // Reload recent data if function exists
                    if (typeof loadRecentData === 'function') {
                        loadRecentData();
                    }
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
                    btnSubmit.prop('disabled', false).html(originalText);
                }
            });
        });

        // Reset form handler
        $('#sludgeForm').on('reset', function() {
            setTimeout(function() {
                $('#tanggal').val(today);
            }, 10);
        });
    });
</script>
@endsection