@extends('layouts.app')

@section('title', 'Agenda Compressor Form')

@section('styles')
    <style>
        .category-card {
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
        }

        .checklist-item {
            transition: background-color 0.2s ease;
        }

        .checklist-item:hover {
            background-color: #f8f9fa;
        }

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
                        style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); border-radius: 12px;">
                        <div class="card-body">
                            <h4 class="text-white fw-bold mb-1">
                                <i class="ri-checkbox-circle-line text-warning me-2"></i>
                                Agenda Compressor - Daily Checklist Form
                            </h4>
                            <p class="text-white-50 mb-0">
                                Checklist harian pemantauan, cleaning, greasing compressor & dryer
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form id="formAgendaComp">
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
                            if (!function_exists('renderCompChecklistItem')) {
                                function renderCompChecklistItem($fieldName, $labelText)
                                {
                                    return '
                                <div class="checklist-item d-flex justify-content-between align-items-center flex-wrap py-3 px-2 border-bottom">
                                    <div class="fw-medium text-dark flex-grow-1 pe-2 mb-2 mb-sm-0">
                                        ' .
                                        $labelText .
                                        '
                                    </div>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check" name="' .
                                        $fieldName .
                                        '" id="' .
                                        $fieldName .
                                        '_empty" value="" checked>
                                        <label class="btn btn-outline-secondary px-3" for="' .
                                        $fieldName .
                                        '_empty">Kosong</label>

                                        <input type="radio" class="btn-check" name="' .
                                        $fieldName .
                                        '" id="' .
                                        $fieldName .
                                        '_ok" value="OK">
                                        <label class="btn btn-outline-success px-3" for="' .
                                        $fieldName .
                                        '_ok">OK</label>

                                        <input type="radio" class="btn-check" name="' .
                                        $fieldName .
                                        '" id="' .
                                        $fieldName .
                                        '_nok" value="NOK">
                                        <label class="btn btn-outline-danger px-3" for="' .
                                        $fieldName .
                                        '_nok">NOK</label>
                                    </div>
                                </div>
                                ';
                                }
                            }
                        @endphp

                        <!-- Group 1: AQ55VSD -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #2563eb;">
                            <div class="card-header bg-soft-primary border-0" style="background-color: #dbeafe;">
                                <h6 class="card-title text-primary fw-bold mb-0">
                                    <i class="ri-settings-2-line me-2"></i> Compressor AQ55VSD
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderCompChecklistItem('pressure_aq55vsd', 'Cek pressure AQ55VSD') !!}
                                {!! renderCompChecklistItem('running_hour_aq55vsd', 'Cek running hour AQ55VSD') !!}
                                {!! renderCompChecklistItem('element_outlet_aq55vsd', 'Cek elemen outlet AQ55VSD') !!}
                                {!! renderCompChecklistItem('kelistrikan_aq55vsd', 'Cek kelistrikan (A,V) AQ55VSD') !!}
                                {!! renderCompChecklistItem('rpm_aq55vsd', 'Cek RPM AQ55VSD') !!}
                                {!! renderCompChecklistItem('cleaning_strainer_aq55vsd', 'Cleaning Strainer AQ55VSD') !!}
                                {!! renderCompChecklistItem('inspeksi_motor_aq55vsd', 'Inspeksi motor AQ 55 VSD (HLT)') !!}
                            </div>
                        </div>

                        <!-- Group 2: GA37 -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #0d9488;">
                            <div class="card-header bg-soft-info border-0" style="background-color: #ccfbf1;">
                                <h6 class="card-title text-teal fw-bold mb-0" style="color: #0f766e;">
                                    <i class="ri-settings-3-line me-2"></i> Compressor GA37
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderCompChecklistItem('pressure_ga37', 'Cek pressure GA37') !!}
                                {!! renderCompChecklistItem('running_hour_ga37', 'Cek running hour GA37') !!}
                                {!! renderCompChecklistItem('kelistrikan_ga37', 'Cek kelistrikan (A,V) GA37') !!}
                                {!! renderCompChecklistItem('element_outlet_ga37', 'Cek elemen outlet GA37') !!}
                                {!! renderCompChecklistItem('cleaning_valve_ga37', 'Cleaning Blowoff valve GA37') !!}
                                {!! renderCompChecklistItem('inspeksi_motor_ga37', 'Inspeksi motor GA37 (HLT)') !!}
                            </div>
                        </div>

                        <!-- Group 3: IR55 -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #ea580c;">
                            <div class="card-header bg-soft-warning border-0" style="background-color: #ffedd5;">
                                <h6 class="card-title text-warning-emphasis fw-bold mb-0" style="color: #c2410c;">
                                    <i class="ri-settings-4-line me-2"></i> Compressor IR55
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderCompChecklistItem('pressure_ir55', 'Cek pressure IR55') !!}
                                {!! renderCompChecklistItem('running_hour_ir55', 'Cek running hour IR55') !!}
                                {!! renderCompChecklistItem('kelistrikan_ir55', 'Cek kelistrikan (A,V) IR55') !!}
                                {!! renderCompChecklistItem('temperature_ir55', 'Cek Temperature IR55') !!}
                                {!! renderCompChecklistItem('replace_filter_ir55', 'Penggantain filter udara masuk IR55') !!}
                                {!! renderCompChecklistItem('inspeksi_motor_ir55', 'Inspeksi motor IR55 (HLT)') !!}
                            </div>
                        </div>

                        <!-- Group 4: Dryers & CT -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #6b7280;">
                            <div class="card-header bg-soft-secondary border-0" style="background-color: #f3f4f6;">
                                <h6 class="card-title text-secondary fw-bold mb-0">
                                    <i class="ri-dashboard-line me-2"></i> Dryers, CT & Receivers
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderCompChecklistItem('inspeksi_dryer_120', 'Inspeksi Dryer 120 (HLT)') !!}
                                {!! renderCompChecklistItem('inspeksi_dryer_tr15', 'Inspeksi Dryer TR-15 (HLT)') !!}
                                {!! renderCompChecklistItem('inspeksi_dryer_ir', 'Inspeksi Dryer IR (HLT)') !!}
                                {!! renderCompChecklistItem('pressure_in_out_ct', 'Cek Pressure in dan out Cooling Tower') !!}
                                {!! renderCompChecklistItem('pressure_bejana_receiver', 'Cek Pressure Bejana Air Receiver Tank (1 & 2)') !!}
                                {!! renderCompChecklistItem('pressure_in_out_dryer', 'Cek Pressure In dan out dryer 120, TR-15, & IR') !!}
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
            $('#formAgendaComp').submit(function(e) {
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
                    url: "{{ route('agenda-compressor.store') }}",
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
                        $('#formAgendaComp').find('input[type="radio"]').each(function() {
                            if ($(this).val() === '') {
                                $(this).prop('checked', true);
                            } else {
                                $(this).prop('checked', false);
                            }
                        });
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
        });
    </script>
@endsection
