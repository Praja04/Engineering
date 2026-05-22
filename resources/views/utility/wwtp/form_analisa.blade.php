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
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">Tambah Data Analisa WWTP</h4>
                            <p class="text-muted mb-0">Input data parameter analisa WWTP harian</p>
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
                    <div class="card">
                        <div class="card-body">
                            <form id="analisaForm">
                                @csrf

                                <!-- Date Selection -->
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal" class="form-label fw-semibold">
                                            Tanggal <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                        <div class="form-text">Tanggal analisa laboratorium dilakukan</div>
                                    </div>
                                </div>

                                <!-- Parameters Grid -->
                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1 text-info">
                                                <i class="mdi mdi-flask-outline me-2"></i>Parameter Analisa
                                            </h5>
                                            <p class="text-muted mb-0">Masukkan hasil pengukuran parameter air limbah</p>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <!-- COD -->
                                        <div class="col-md-4">
                                            <div class="card border border-info">
                                                <div class="card-body">
                                                    <label for="cod" class="form-label fw-semibold text-dark">
                                                        COD (Chemical Oxygen Demand)
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" class="form-control"
                                                            id="cod" name="cod" min="0" placeholder="0.00">
                                                        <span class="input-group-text bg-light">mg/L</span>
                                                    </div>
                                                    <small class="form-text text-muted">Kebutuhan oksigen kimiawi</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- TSS -->
                                        <div class="col-md-4">
                                            <div class="card border border-info">
                                                <div class="card-body">
                                                    <label for="tss" class="form-label fw-semibold text-dark">
                                                        TSS (Total Suspended Solids)
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" class="form-control"
                                                            id="tss" name="tss" min="0" placeholder="0.00">
                                                        <span class="input-group-text bg-light">mg/L</span>
                                                    </div>
                                                    <small class="form-text text-muted">Total padatan tersuspensi</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- pH -->
                                        <div class="col-md-4">
                                            <div class="card border border-info">
                                                <div class="card-body">
                                                    <label for="ph" class="form-label fw-semibold text-dark">
                                                        pH (Derajat Keasaman)
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" class="form-control"
                                                            id="ph" name="ph" min="0" max="14"
                                                            placeholder="7.00">
                                                        <span class="input-group-text bg-light">pH</span>
                                                    </div>
                                                    <small class="form-text text-muted">Skala keasaman (0 - 14)</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- EC -->
                                        <div class="col-md-6">
                                            <div class="card border border-info">
                                                <div class="card-body">
                                                    <label for="ec" class="form-label fw-semibold text-dark">
                                                        EC (Emulsifiable Concentrate)
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" class="form-control"
                                                            id="ec" name="ec" min="0" placeholder="0.00">
                                                        <span class="input-group-text bg-light">%</span>
                                                    </div>
                                                    <small class="form-text text-muted">Konsentrat cair berbasis
                                                        minyak</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- DO -->
                                        <div class="col-md-6">
                                            <div class="card border border-info">
                                                <div class="card-body">
                                                    <label for="do" class="form-label fw-semibold text-dark">
                                                        DO (Dissolved Oxygen)
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" class="form-control"
                                                            id="do" name="do" min="0"
                                                            placeholder="0.00">
                                                        <span class="input-group-text bg-light">mg/L</span>
                                                    </div>
                                                    <small class="form-text text-muted">Kandungan oksigen terlarut</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Information Alert -->
                                <div class="alert alert-info border-0 mb-4" role="alert">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="mdi mdi-information fs-4 text-info"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2 text-dark">
                                            <strong>Catatan Penting:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Setiap tanggal hanya boleh memiliki <strong>satu data analisa</strong>.
                                                </li>
                                                <li>Jika data pada tanggal terpilih sudah ada, sistem akan menolak dan
                                                    memberikan notifikasi error.</li>
                                                <li>Formulir ini mendukung angka desimal (gunakan titik sebagai pemisah
                                                    desimal, contoh: 7.35).</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="reset" class="btn btn-light">
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
                    <p class="text-muted mb-4" id="successMessage">Data analisa WWTP telah berhasil disimpan ke sistem.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
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
            $('#tanggal').val(today);

            // Submit Form
            $('#analisaForm').on('submit', function(e) {
                e.preventDefault();

                const btn = $('#btnSubmitAnalisa');
                const originalText = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                const formData = {
                    _token: $('input[name="_token"]').val(),
                    tanggal: $('#tanggal').val(),
                    cod: $('#cod').val(),
                    tss: $('#tss').val(),
                    ph: $('#ph').val(),
                    ec: $('#ec').val(),
                    do: $('#do').val()
                };

                $.ajax({
                    url: "{{ url('api/wwtp-analisa') }}",
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#successMessage').html(response.message ||
                            'Data analisa WWTP telah berhasil disimpan ke sistem.');
                        $('#successModal').modal('show');

                        $('#analisaForm')[0].reset();
                        $('#tanggal').val(today);
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

            // Reset Handler
            $('#analisaForm').on('reset', function() {
                setTimeout(function() {
                    $('#tanggal').val(today);
                }, 10);
            });
        });
    </script>
@endsection
