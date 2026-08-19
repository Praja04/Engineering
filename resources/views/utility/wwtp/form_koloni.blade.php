@extends('layouts.app')

@section('title', 'Form Input Koloni WWTP')

@section('styles')
    <style>
        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.15rem rgba(41, 156, 219, 0.25);
            border-color: #299cdb;
        }

        .sci-input-group {
            border: 1px dashed #ced4da;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .sci-input-symbol {
            font-size: 1.5rem;
            font-weight: 700;
            color: #495057;
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
                            <h4 class="mb-1">Input Data Koloni WWTP</h4>
                            <p class="text-muted mb-0">Input data hasil laboratorium koloni per minggu.</p>
                        </div>
                        <div>
                            <a href="{{ url('/wwtp/data_koloni') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i> Lihat Data Riwayat
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-bottom py-3">
                            <h5 class="card-title mb-0">Form Pencatatan Koloni Mingguan</h5>
                        </div>
                        <div class="card-body p-4">
                            <form id="koloniForm">
                                @csrf
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal" class="form-label fw-semibold">
                                            Tanggal Pengukuran <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                        <div class="form-text text-primary" id="weekHelperText">
                                            Pilih tanggal untuk menghitung periode minggu.
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="master_koloni_id" class="form-label fw-semibold">
                                            Sampel Koloni <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="master_koloni_id" name="master_koloni_id" required>
                                            <option value="">-- Pilih Sampel --</option>
                                            @foreach ($samples as $sample)
                                                <option value="{{ $sample->id }}">{{ $sample->nama_sample }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Pilih titik sampel pengukuran.</div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold d-block">
                                        Nilai Koloni (Scientific Notation) <span class="text-danger">*</span>
                                    </label>

                                    <div class="sci-input-group">
                                        <div class="row align-items-center g-3 text-center">
                                            <div class="col-md-5">
                                                <label for="nilai_base" class="form-label small text-muted">Angka
                                                    Utama</label>
                                                <input type="number" step="0.001"
                                                    class="form-control text-center font-monospace fs-4 fw-bold"
                                                    id="nilai_base" name="nilai_base" placeholder="Contoh: 1.92" required
                                                    min="0">
                                                <span class="form-text small text-muted">Bisa menggunakan desimal</span>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="sci-input-symbol">&times; 10 <sup>^</sup></div>
                                            </div>
                                            <div class="col-md-5">
                                                <label for="nilai_pangkat"
                                                    class="form-label small text-muted">Eksponen</label>
                                                <input type="number"
                                                    class="form-control text-center font-monospace fs-4 fw-bold"
                                                    id="nilai_pangkat" name="nilai_pangkat" placeholder="Contoh: 5"
                                                    required>
                                                <span class="form-text small text-muted">Harus berupa angka bulat</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Validation Alert Area -->
                                <div id="validationAlert" class="alert alert-warning border-0 d-none" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="mdi mdi-alert-circle fs-3 me-2"></i>
                                        <span id="validationMsg">Sampel sudah terisi untuk minggu ini!</span>
                                    </div>
                                </div>

                                <div class="alert alert-info border-0" role="alert">
                                    <div class="d-flex">
                                        <i class="mdi mdi-information fs-4 me-2"></i>
                                        <div>
                                            <strong>Validasi Mingguan:</strong> Satu sampel hanya boleh diinput <strong>1
                                                kali</strong> per periode minggu (Senin - Minggu).
                                            Eksponen pangkat yang disimpan adalah murni rumusan inputan dan tidak dikonversi
                                            ke desimal penuh di basis data.
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="reset" class="btn btn-light px-4">
                                        <i class="mdi mdi-refresh me-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4" id="btnSubmit">
                                        <i class="mdi mdi-content-save me-1"></i> Simpan Data
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Set default date to today
            const today = new Date().toISOString().split('T')[0];
            $('#tanggal').val(today);
            updateWeekHelper();

            function getWeekRange(dateString) {
                const date = new Date(dateString);
                const day = date.getDay();
                // Adjust to Monday-Sunday
                const diffToMonday = date.getDate() - day + (day === 0 ? -6 : 1);

                const monday = new Date(date.setDate(diffToMonday));
                const sunday = new Date(monday);
                sunday.setDate(monday.getDate() + 6);

                const formatDate = (d) => {
                    const options = {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    };
                    return d.toLocaleDateString('id-ID', options);
                };

                return {
                    start: monday.toISOString().split('T')[0],
                    end: sunday.toISOString().split('T')[0],
                    formatted: `${formatDate(monday)} s/d ${formatDate(sunday)}`
                };
            }

            function updateWeekHelper() {
                const dateVal = $('#tanggal').val();
                if (dateVal) {
                    const range = getWeekRange(dateVal);
                    $('#weekHelperText').html(
                        `<i class="mdi mdi-calendar-range me-1"></i>Periode Minggu: <strong>${range.formatted}</strong>`
                    );
                } else {
                    $('#weekHelperText').html('Pilih tanggal untuk menghitung periode minggu.');
                }
            }

            // Listen date change
            $('#tanggal').on('change', function() {
                updateWeekHelper();
                checkIfFilled();
            });

            // Listen sample change
            $('#master_koloni_id').on('change', function() {
                checkIfFilled();
            });

            // Check if already filled
            function checkIfFilled() {
                const tanggal = $('#tanggal').val();
                const sampleId = $('#master_koloni_id').val();

                if (!tanggal || !sampleId) {
                    $('#validationAlert').addClass('d-none');
                    $('#btnSubmit').prop('disabled', false);
                    return;
                }

                $.ajax({
                    url: "{{ url('api/wwtp-koloni/check-filled') }}",
                    method: 'GET',
                    data: {
                        tanggal: tanggal,
                        master_koloni_id: sampleId
                    },
                    success: function(response) {
                        if (response.success && response.is_filled) {
                            $('#validationMsg').html(
                                `Titik sampel ini sudah diinput untuk minggu <strong>${response.week_range}</strong>. Silakan pilih titik sampel atau minggu lain.`
                            );
                            $('#validationAlert').removeClass('d-none');
                            $('#btnSubmit').prop('disabled', true);
                        } else {
                            $('#validationAlert').addClass('d-none');
                            $('#btnSubmit').prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error validation week check', xhr);
                    }
                });
            }

            // Submit Form
            $('#koloniForm').on('submit', function(e) {
                e.preventDefault();
                const btn = $('#btnSubmit');
                const originalText = btn.html();

                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: "{{ url('wwtp/koloni') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tanggal: $('#tanggal').val(),
                        master_koloni_id: $('#master_koloni_id').val(),
                        nilai_base: $('#nilai_base').val(),
                        nilai_pangkat: $('#nilai_pangkat').val(),
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#koloniForm')[0].reset();
                        $('#tanggal').val(today);
                        updateWeekHelper();
                        $('#validationAlert').addClass('d-none');
                        $('#btnSubmit').prop('disabled', false);
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON;
                        let msg = 'Gagal menyimpan data koloni!';
                        if (error && error.errors) {
                            msg = Object.values(error.errors).flat().join('<br>');
                        } else if (error && error.message) {
                            msg = error.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: msg
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Reset handler
            $('#koloniForm').on('reset', function() {
                setTimeout(function() {
                    $('#tanggal').val(today);
                    updateWeekHelper();
                    $('#validationAlert').addClass('d-none');
                    $('#btnSubmit').prop('disabled', false);
                }, 10);
            });
        });
    </script>
@endsection
