@extends('layouts.app')

@section('title', 'Agenda AHU Checklist Form')

@section('styles')
    <style>
        .category-card {
            border-left: 4px solid #1b4965;
            border-radius: 8px;
        }

        .checklist-item {
            transition: background-color 0.2s ease;
        }

        .checklist-item:hover {
            background-color: #f8f9fa;
        }

        /* Touch optimization */
        .btn-check+.btn {
            min-width: 65px;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #0f3057 0%, #00587a 100%); border-radius: 12px;">
                        <div class="card-body">
                            <h4 class="text-white fw-bold mb-1">
                                <i class="ri-checkbox-line text-warning me-2"></i>
                                Agenda AHU - Daily Checklist Form
                            </h4>
                            <p class="text-white-50 mb-0">
                                Checklist harian agenda Air Handling Unit (AHU)
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form id="formAgendaAhu">
                        @csrf

                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-primary">
                                            <i class="ri-calendar-line me-1"></i> Pilih Tanggal Checklist
                                        </label>
                                        <input type="date" name="tanggal" class="form-control"
                                            value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Helper function to generate checklist item row -->
                        @php
                            if (!function_exists('renderChecklistItem')) {
                                function renderChecklistItem($fieldName, $labelText)
                                {
                                    return '
                                <div class="checklist-item py-3 px-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <div class="fw-medium text-dark flex-grow-1 pe-2 mb-2 mb-sm-0">
                                            ' . $labelText . '
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <input type="radio" class="btn-check radio-checklist" name="' . $fieldName . '" id="' . $fieldName . '_empty" value="" checked style="display: none;">

                                            <input type="radio" class="btn-check radio-checklist" name="' . $fieldName . '" id="' . $fieldName . '_ok" value="OK">
                                            <label class="btn btn-outline-success px-3 rounded-start" for="' . $fieldName . '_ok">OK</label>

                                            <input type="radio" class="btn-check radio-checklist" name="' . $fieldName . '" id="' . $fieldName . '_nok" value="NOK">
                                            <label class="btn btn-outline-danger px-3 rounded-end" for="' . $fieldName . '_nok">NOK</label>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control form-control-sm input-description bg-soft-danger text-danger border-danger mt-2" name="keterangan_' . $fieldName . '" id="keterangan_' . $fieldName . '" placeholder="Isi keterangan kerusakan / ketidaksesuaian..." style="display: none;">
                                </div>
                                ';
                                }
                            }
                        @endphp

                        <!-- Group 1: Kelistrikan & Pressure -->
                        <div class="card shadow-sm category-card mb-4">
                            <div class="card-header bg-soft-primary border-0">
                                <h6 class="card-title text-primary fw-bold mb-0">
                                    <i class="ri-bubble-chart-line me-2"></i> Cek Kelistrikan & Pressure
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderChecklistItem('kelistrikan_ahu_1', 'Cek kelistrikan (A,V) AHU 1') !!}
                                {!! renderChecklistItem('kelistrikan_ahu_2', 'Cek kelistrikan (A,V) AHU 2') !!}
                                {!! renderChecklistItem('kelistrikan_ahu_3', 'Cek kelistrikan (A,V) AHU 3') !!}
                                {!! renderChecklistItem('kelistrikan_ahu_4', 'Cek kelistrikan (A,V) AHU 4') !!}
                                {!! renderChecklistItem('pressur_gauge_in_ahu_1', 'Cek pressur gauge in AHU 1') !!}
                                {!! renderChecklistItem('pressur_gauge_in_ahu_2', 'Cek pressur gauge in AHU 2') !!}
                                {!! renderChecklistItem('pressur_gauge_in_ahu_3', 'Cek pressur gauge in AHU 3') !!}
                                {!! renderChecklistItem('pressur_gauge_in_ahu_4', 'Cek pressur gauge in AHU 4') !!}
                                {!! renderChecklistItem('pressur_gauge_out_ahu_1', 'Cek pressur gauge out AHU 1') !!}
                                {!! renderChecklistItem('pressur_gauge_out_ahu_2', 'Cek pressur gauge out AHU 2') !!}
                                {!! renderChecklistItem('pressur_gauge_out_ahu_3', 'Cek pressur gauge out AHU 3') !!}
                                {!! renderChecklistItem('pressur_gauge_out_ahu_4', 'Cek pressur gauge out AHU 4') !!}
                            </div>
                        </div>

                        <!-- Group 2: Temperature -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #f77f00;">
                            <div class="card-header bg-soft-warning border-0">
                                <h6 class="card-title text-warning-emphasis fw-bold mb-0">
                                    <i class="ri-temp-hot-line me-2"></i> Cek Temperature Gauge
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderChecklistItem('temp_gauge_in_ahu_1', 'Cek temperature gauge in AHU 1') !!}
                                {!! renderChecklistItem('temp_gauge_in_ahu_2', 'Cek temperature gauge in AHU 2') !!}
                                {!! renderChecklistItem('temp_gauge_in_ahu_3', 'Cek temperature gauge in AHU 3') !!}
                                {!! renderChecklistItem('temp_gauge_in_ahu_4', 'Cek temperature gauge in AHU 4') !!}
                                {!! renderChecklistItem('temp_gauge_out_ahu_1', 'Cek temperature gauge out AHU 1') !!}
                                {!! renderChecklistItem('temp_gauge_out_ahu_2', 'Cek temperature gauge out AHU 2') !!}
                                {!! renderChecklistItem('temp_gauge_out_ahu_3', 'Cek temperature gauge out AHU 3') !!}
                                {!! renderChecklistItem('temp_gauge_out_ahu_4', 'Cek temperature gauge out AHU 4') !!}
                            </div>
                        </div>

                        <!-- Group 3: Maintenance & Inspeksi -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #e71d36;">
                            <div class="card-header bg-soft-danger border-0">
                                <h6 class="card-title text-danger-emphasis fw-bold mb-0">
                                    <i class="ri-settings-5-line me-2"></i> Cleaning & Inspeksi
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderChecklistItem('clean_filter_strainer_1', 'Cleaning filter udara & strainer 1') !!}
                                {!! renderChecklistItem('clean_filter_strainer_2', 'Cleaning filter udara & strainer 2') !!}
                                {!! renderChecklistItem('clean_filter_strainer_3', 'Cleaning filter udara & strainer 3') !!}
                                {!! renderChecklistItem('clean_filter_strainer_4', 'Cleaning filter udara & strainer 4') !!}
                                {!! renderChecklistItem('clean_filter_bebas_ahu', 'Cleaning filter udara bebas ke AHU') !!}
                                {!! renderChecklistItem('inspeksi_h_ahu_1_4', 'Inspeksi (H) AHU 1 s/d 4') !!}
                            </div>
                        </div>

                        <div class="text-end mb-5">
                            <button type="submit" class="btn btn-primary px-5 btn-lg rounded-pill shadow-sm">
                                <i class="ri-send-plane-2-line me-1"></i> Submit Checklist
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#formAgendaAhu').submit(function(e) {
                e.preventDefault();

                let hasValue = false;
                $(this).find('input[type="radio"]:checked').each(function() {
                    if ($(this).val() !== '') {
                        hasValue = true;
                        return false;
                    }
                });

                if (!hasValue) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Minimal harus ada 1 checklist (OK / NOK) yang diisi sebelum submit.'
                    });
                    return;
                }

                let btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).text('Loading...');

                $.ajax({
                    url: "{{ route('agenda-ahu.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Reset radio buttons to "Kosong"
                        $('#formAgendaAhu').find('input[type="radio"]').each(function() {
                            if ($(this).val() === '') {
                                $(this).prop('checked', true);
                            } else {
                                $(this).prop('checked', false);
                            }
                        });

                        // Clear and hide all description inputs
                        $('.input-description').hide().prop('required', false).val('');
                    },
                    error: function(xhr) {
                        let err = xhr.responseJSON;
                        let message = err?.message || 'Terjadi kesalahan';
                        if (err?.errors) {
                            message = Object.values(err.errors).flat().join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: message
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(
                            '<i class="ri-send-plane-2-line me-1"></i> Submit Checklist');
                    }
                });
            });

            // Listen for radio button changes to toggle description fields
            $(document).on('change', '.radio-checklist', function() {
                let name = $(this).attr('name');
                let val = $(this).val();
                let descInput = $(`#keterangan_${name}`);
                
                if (val === 'NOK') {
                    descInput.slideDown().prop('required', true);
                } else {
                    descInput.slideUp().prop('required', false).val('');
                }
            });
        });
    </script>
@endsection
