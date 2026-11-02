@extends('layouts.app')

@section('title', 'Form Input Biaya Chemical WWTP')

@section('styles')
    <style>
        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.15rem rgba(41, 156, 219, 0.25);
            border-color: #299cdb;
        }

        .preview-card {
            background-color: #f8f9fa;
            border: 1px dashed #ced4da;
            border-radius: 8px;
        }

        .value-display {
            font-family: monospace;
            font-size: 1.1rem;
            font-weight: 600;
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
                            <h4 class="mb-1">Input Biaya Chemical WWTP</h4>
                            <p class="text-muted mb-0">Input data volume limbah diolah dan pemakaian chemical per bulan.</p>
                        </div>
                        <div>
                            <a href="{{ url('/wwtp/data_biaya_chemical') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i> Lihat Data Riwayat
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Layout -->
            <div class="row mb-4">
                <!-- Form Input -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-bottom py-3">
                            <h5 class="card-title mb-0">Form Pencatatan Bulanan</h5>
                        </div>
                        <div class="card-body p-4">
                            <form id="biayaForm">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="tahun" class="form-label fw-semibold">Tahun <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="tahun" name="tahun" required>
                                            @php
                                                $currentYear = date('Y');
                                            @endphp
                                            @for ($y = $currentYear - 2; $y <= $currentYear + 2; $y++)
                                                <option value="{{ $y }}"
                                                    {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="bulan" class="form-label fw-semibold">Bulan <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="bulan" name="bulan" required>
                                            <option value="">-- Pilih Bulan --</option>
                                            <option value="1">Januari</option>
                                            <option value="2">Februari</option>
                                            <option value="3">Maret</option>
                                            <option value="4">April</option>
                                            <option value="5">Mei</option>
                                            <option value="6">Juni</option>
                                            <option value="7">Juli</option>
                                            <option value="8">Agustus</option>
                                            <option value="9">September</option>
                                            <option value="10">Oktober</option>
                                            <option value="11">November</option>
                                            <option value="12">Desember</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="limbah_di_olah" class="form-label fw-semibold">Limbah Diolah (m³) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="limbah_di_olah"
                                        name="limbah_di_olah" required placeholder="Contoh: 1568" min="0">
                                    <div class="form-text">Volume hasil olah air limbah dalam satuan meter kubik.</div>
                                </div>

                                <div class="border-top my-4 pt-3">
                                    <h6 class="text-primary fw-bold mb-3">Input Kuantitas Chemical (kg/bulan)</h6>

                                    <div class="row" id="chemicalInputsContainer">
                                        @if ($standards->isEmpty())
                                            <div class="col-12 text-center text-muted py-3">
                                                Tidak ada data chemical di master. Silakan tambahkan dahulu di halaman <a
                                                    href="{{ url('wwtp/master_biaya_chemical') }}">Master Harga</a>.
                                            </div>
                                        @else
                                            @foreach ($standards as $std)
                                                <div class="col-md-6 mb-3">
                                                    <label for="qty_{{ $std->id }}"
                                                        class="form-label fw-semibold">{{ $std->chemical_name }}</label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" class="form-control calc-input"
                                                            id="qty_{{ $std->id }}" name="qty[{{ $std->id }}]"
                                                            data-price="{{ $std->harga_standar }}"
                                                            data-name="{{ $std->chemical_name }}" required value="0"
                                                            min="0">
                                                        <span class="input-group-text">kg</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <!-- Validation Alert -->
                                <div id="validationAlert" class="alert alert-warning border-0 d-none" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="mdi mdi-alert-circle fs-3 me-2"></i>
                                        <span id="validationMsg">Data bulan ini sudah diinput!</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="reset" class="btn btn-light px-4">
                                        <i class="mdi mdi-refresh me-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4" id="btnSubmit"
                                        {{ $standards->isEmpty() ? 'disabled' : '' }}>
                                        <i class="mdi mdi-content-save me-1"></i> Simpan Data
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Live Preview calculations -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0 text-primary fw-bold">
                                <i class="mdi mdi-calculator me-1"></i> Estimasi Real-Time & Biaya
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Standard Price Reference Banner -->
                            <div class="alert alert-soft-secondary border-0 mb-4 py-2">
                                <span class="fw-semibold small text-muted">Standar Harga Aktif:</span>
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    @foreach ($standards as $std)
                                        <span class="badge bg-light text-dark">{{ $std->chemical_name }}: Rp
                                            {{ number_format($std->harga_standar, 0, ',', '.') }}</span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Cost Monthly Preview -->
                            <div class="preview-card p-3 mb-4">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Estimasi Biaya per Bulan</h6>
                                <div class="row g-2" id="costPreviewList">
                                    <div class="col-12 text-center text-muted small py-2">Masukkan kuantitas untuk melihat
                                        estimasi</div>
                                </div>
                            </div>

                            <!-- Cost per m3 Preview -->
                            <div class="preview-card p-3">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Estimasi Biaya per m³ Limbah</h6>
                                <div class="row g-2" id="m3PreviewList">
                                    <div class="col-12 text-center text-muted small py-2">Masukkan volume limbah dan
                                        kuantitas chemical</div>
                                </div>
                            </div>
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
            // Format to Indonesian Rupiah representation
            function formatRupiah(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'decimal',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                }).format(value);
            }

            // Calculation Logic
            function calculatePreviews() {
                const limbah = parseFloat($('#limbah_di_olah').val()) || 0;

                let totalCost = 0;
                let costListHtml = '';
                let m3ListHtml = '';

                const inputs = $('.calc-input');

                if (inputs.length === 0) return;

                inputs.each(function() {
                    const qty = parseFloat($(this).val()) || 0;
                    const price = parseFloat($(this).data('price')) || 0;
                    const name = $(this).data('name');

                    const cost = qty * price;
                    const costM3 = limbah > 0 ? cost / limbah : 0;

                    totalCost += cost;

                    costListHtml += `
                        <div class="col-6 text-muted">Biaya ${name}/bulan:</div>
                        <div class="col-6 text-end value-display text-dark">Rp ${formatRupiah(cost)}</div>
                    `;

                    m3ListHtml += `
                        <div class="col-6 text-muted">Biaya ${name}/m³:</div>
                        <div class="col-6 text-end value-display text-dark">Rp ${formatRupiah(costM3)}</div>
                    `;
                });

                const totalM3 = limbah > 0 ? totalCost / limbah : 0;

                costListHtml += `
                    <div class="col-6 fw-bold text-primary border-top pt-2">Total Biaya Chemical:</div>
                    <div class="col-6 text-end value-display text-primary fw-bold border-top pt-2">Rp ${formatRupiah(totalCost)}</div>
                `;

                m3ListHtml += `
                    <div class="col-6 fw-bold text-success border-top pt-2">Total Biaya / m³:</div>
                    <div class="col-6 text-end value-display text-success fw-bold border-top pt-2">Rp ${formatRupiah(totalM3)}</div>
                `;

                $('#costPreviewList').html(costListHtml);
                $('#m3PreviewList').html(m3ListHtml);
            }

            // Event Listeners for Live preview calculations
            $(document).on('input change', '.calc-input, #limbah_di_olah', function() {
                calculatePreviews();
            });

            // Init calculation
            calculatePreviews();

            // Set default select month
            const currentMonth = new Date().getMonth() + 1;
            $('#bulan').val(currentMonth);

            // Check if selected Month/Year is already filled
            function checkFilled() {
                const tahun = $('#tahun').val();
                const bulan = $('#bulan').val();

                if (!tahun || !bulan) {
                    $('#validationAlert').addClass('d-none');
                    $('#btnSubmit').prop('disabled', false);
                    return;
                }

                $.ajax({
                    url: "{{ url('api/wwtp-biaya-chemical/check-filled') }}",
                    method: 'GET',
                    data: {
                        tahun: tahun,
                        bulan: bulan
                    },
                    success: function(response) {
                        if (response.success && response.is_filled) {
                            const monthText = $('#bulan option:selected').text();
                            $('#validationMsg').html(
                                `Data biaya chemical untuk bulan <strong>${monthText} ${tahun}</strong> sudah diinput. Silakan pilih bulan lain atau edit data di riwayat.`
                            );
                            $('#validationAlert').removeClass('d-none');
                            $('#btnSubmit').prop('disabled', true);
                        } else {
                            $('#validationAlert').addClass('d-none');
                            $('#btnSubmit').prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error checking filled records', xhr);
                    }
                });
            }

            $('#tahun, #bulan').on('change', function() {
                checkFilled();
            });

            // Initial check
            checkFilled();

            // Submit Form
            $('#biayaForm').on('submit', function(e) {
                e.preventDefault();
                const btn = $('#btnSubmit');
                const originalText = btn.html();

                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: "{{ url('wwtp/biaya-chemical') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        // Reset form but keep Year
                        const yr = $('#tahun').val();
                        $('#biayaForm')[0].reset();
                        $('#tahun').val(yr);
                        $('#bulan').val(currentMonth);

                        calculatePreviews();
                        checkFilled();
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON;
                        let msg = 'Gagal menyimpan data biaya chemical!';
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
            $('#biayaForm').on('reset', function() {
                setTimeout(function() {
                    $('#tahun').val(new Date().getFullYear());
                    $('#bulan').val(currentMonth);
                    calculatePreviews();
                    checkFilled();
                }, 10);
            });
        });
    </script>
@endsection
