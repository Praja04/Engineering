@extends('layouts.app')

@section('title', 'Agenda Cooling Tower Form')

@section('styles')
    <style>
        .category-card {
            border-left: 4px solid #10b981;
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
                        style="background: linear-gradient(135deg, #064e3b 0%, #10b981 100%); border-radius: 12px;">
                        <div class="card-body">
                            <h4 class="text-white fw-bold mb-1">
                                <i class="ri-checkbox-circle-line text-warning me-2"></i>
                                Agenda Cooling Tower - Daily Checklist Form
                            </h4>
                            <p class="text-white-50 mb-0">
                                Checklist harian pemantauan, cleaning, greasing cooling tower & pompa
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form id="formAgendaCT">
                        @csrf

                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-success">
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
                            if (!function_exists('renderCTChecklistItem')) {
                                function renderCTChecklistItem($fieldName, $labelText)
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

                        <!-- Group 1: Kelistrikan -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #3b82f6;">
                            <div class="card-header bg-soft-info border-0">
                                <h6 class="card-title text-info fw-bold mb-0">
                                    <i class="ri-flashlight-line me-2"></i> Cek Kelistrikan (A,V)
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderCTChecklistItem('kelistrikan_pompa_10000p2', 'Cek kelistrikan (A,V) pompa 10000P2') !!}
                                {!! renderCTChecklistItem('kelistrikan_pompa_10000p2a', 'Cek kelistrikan (A,V) pompa 10000P2a') !!}
                                {!! renderCTChecklistItem('kelistrikan_pompa_10000p2b', 'Cek kelistrikan (A,V) pompa 10000P2b') !!}
                                {!! renderCTChecklistItem('kelistrikan_fan_1', 'Cek kelistrikan (A,V) Motor Fan 1') !!}
                                {!! renderCTChecklistItem('kelistrikan_fan_2', 'Cek kelistrikan (A,V) Motor Fan 2') !!}
                                {!! renderCTChecklistItem('kelistrikan_fan_3', 'Cek kelistrikan (A,V) Motor Fan 3') !!}
                                {!! renderCTChecklistItem('kelistrikan_fan_4', 'Cek kelistrikan (A,V) Motor Fan 4') !!}
                            </div>
                        </div>

                        <!-- Group 2: Suhu & Pressure -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #f59e0b;">
                            <div class="card-header bg-soft-warning border-0">
                                <h6 class="card-title text-warning fw-bold mb-0">
                                    <i class="ri-temp-hot-line me-2"></i> Suhu, Pressure & Chemical
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderCTChecklistItem('suhu_out_ct', 'Cek suhu out cooling tower') !!}
                                {!! renderCTChecklistItem('suhu_in_ct', 'Cek suhu in cooling tower') !!}
                                {!! renderCTChecklistItem('pressure_out_ct', 'Cek pressure out cooling tower') !!}
                                {!! renderCTChecklistItem('pressure_in_ct', 'Cek pressure in cooling tower') !!}
                                {!! renderCTChecklistItem('ph_air_ct', 'Cek pH air cooling tower') !!}
                                {!! renderCTChecklistItem('stok_chemical', 'Cek stok chemical') !!}
                            </div>
                        </div>

                        <!-- Group 3: Cleaning -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #ef4444;">
                            <div class="card-header bg-soft-danger border-0">
                                <h6 class="card-title text-danger fw-bold mb-0">
                                    <i class="ri-brush-line me-2"></i> Cleaning (Saringan & Valve)
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderCTChecklistItem('cleaning_saringan_bak', 'Cleaning saringan Bak Cooling Tower') !!}
                                {!! renderCTChecklistItem('cleaning_strainer_10000p2', 'Cleaning strainer pompa 10000 P2') !!}
                                {!! renderCTChecklistItem('cleaning_strainer_10000p2a', 'Cleaning strainer pompa 10000 P2 a') !!}
                                {!! renderCTChecklistItem('cleaning_strainer_10000p2b', 'Cleaning strainer pompa 10000 P2 b') !!}
                                {!! renderCTChecklistItem('cleaning_valve_10000p2', 'Cleaning check valve Pompa 10000 P2') !!}
                                {!! renderCTChecklistItem('cleaning_valve_10000p2a', 'Cleaning check valve Pompa 10000 P2a') !!}
                                {!! renderCTChecklistItem('cleaning_valve_10000p2b', 'Cleaning check valve Pompa 10000 P2b') !!}
                            </div>
                        </div>

                        <!-- Group 4: Greasing & Fan Cleaning -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #a855f7;">
                            <div class="card-header bg-soft-purple border-0" style="background-color: #f3e8ff;">
                                <h6 class="card-title text-purple fw-bold mb-0" style="color: #7e22ce;">
                                    <i class="ri-oil-line me-2"></i> Greasing & Fan Cleaning
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderCTChecklistItem('greasing_pompa_10000p2', 'Greasing Pompa 10000 P2') !!}
                                {!! renderCTChecklistItem('greasing_pompa_10000p2a', 'Greasing Pompa 10000 P2 a') !!}
                                {!! renderCTChecklistItem('greasing_pompa_10000p2b', 'Greasing Pompa 10000 P2 b') !!}
                                {!! renderCTChecklistItem('greasing_cleaning_fan_1', 'Greasing dan Cleaning Fan 1') !!}
                                {!! renderCTChecklistItem('greasing_cleaning_fan_2', 'Greasing dan Cleaning Fan 2') !!}
                                {!! renderCTChecklistItem('greasing_cleaning_fan_3', 'Greasing dan Cleaning Fan 3') !!}
                                {!! renderCTChecklistItem('greasing_cleaning_fan_4', 'Greasing dan Cleaning Fan 4') !!}
                            </div>
                        </div>

                        <!-- Group 5: Pengecekan Rubber Coupling & Sling -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #6366f1;">
                            <div class="card-header bg-soft-indigo border-0" style="background-color: #e0e7ff;">
                                <h6 class="card-title text-indigo fw-bold mb-0" style="color: #4338ca;">
                                    <i class="ri-repeat-line me-2"></i> Rubber Coupling & Sling Fan
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderCTChecklistItem('rubber_coupling_10000p2', 'Pengecekan rubber coupling Pompa 10000P2') !!}
                                {!! renderCTChecklistItem('rubber_coupling_10000p2a', 'Pengecekan rubber coupling Pompa 10000P2 a') !!}
                                {!! renderCTChecklistItem('rubber_coupling_10000p2b', 'Pengecekan rubber coupling Pompa 10000P2 b') !!}
                                {!! renderCTChecklistItem('sling_fan_ct_1', 'Pengecekan sling fan CT 1') !!}
                                {!! renderCTChecklistItem('sling_fan_ct_2', 'Pengecekan sling fan CT 2') !!}
                                {!! renderCTChecklistItem('sling_fan_ct_3', 'Pengecekan sling fan CT 3') !!}
                                {!! renderCTChecklistItem('sling_fan_ct_4', 'Pengecekan sling fan CT 4') !!}
                            </div>
                        </div>

                        <!-- Group 6: Lain-lain -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #6b7280;">
                            <div class="card-header bg-soft-secondary border-0">
                                <h6 class="card-title text-secondary fw-bold mb-0">
                                    <i class="ri-tools-line me-2"></i> Kalibrasi & Inspeksi
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderCTChecklistItem('kalibrasi_dosis_chemical', 'Kalibrasi dosis chemical') !!}
                                {!! renderCTChecklistItem('inspeksi_baut_mur', 'Inspeksi baut dan mur (all CT dan Pompa)') !!}
                            </div>
                        </div>

                        <div class="text-end mb-5">
                            <button type="submit" class="btn btn-success px-5 btn-lg rounded-pill shadow-sm">
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
            $('#formAgendaCT').submit(function(e) {
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
                    url: "{{ route('agenda-cooling-tower.store') }}",
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
                        $('#formAgendaCT').find('input[type="radio"]').each(function() {
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
