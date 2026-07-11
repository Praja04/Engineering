@extends('layouts.app')

@section('title', 'Agenda Tank Farm & Hydrant Checklist Form')

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
    .btn-check + .btn {
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
                    style="background: linear-gradient(135deg, #102a43 0%, #243e56 100%); border-radius: 12px;">
                    <div class="card-body">
                        <h4 class="text-white fw-bold mb-1">
                            <i class="ri-checkbox-line text-warning me-2"></i>
                            Agenda Tank Farm & Hydrant - Daily Checklist Form
                        </h4>
                        <p class="text-white-50 mb-0">
                            Checklist harian agenda Tank Farm dan Hydrant
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <form id="formAgendaTankFarm">
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
                            function renderChecklistItem($fieldName, $labelText) {
                                return '
                                <div class="checklist-item d-flex justify-content-between align-items-center flex-wrap py-3 px-2 border-bottom">
                                    <div class="fw-medium text-dark flex-grow-1 pe-2 mb-2 mb-sm-0">
                                        ' . $labelText . '
                                    </div>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check" name="' . $fieldName . '" id="' . $fieldName . '_empty" value="" checked>
                                        <label class="btn btn-outline-secondary px-3" for="' . $fieldName . '_empty">Kosong</label>

                                        <input type="radio" class="btn-check" name="' . $fieldName . '" id="' . $fieldName . '_ok" value="OK">
                                        <label class="btn btn-outline-success px-3" for="' . $fieldName . '_ok">OK</label>

                                        <input type="radio" class="btn-check" name="' . $fieldName . '" id="' . $fieldName . '_nok" value="NOK">
                                        <label class="btn btn-outline-danger px-3" for="' . $fieldName . '_nok">NOK</label>
                                    </div>
                                </div>
                                ';
                            }
                        }
                    @endphp

                    <!-- Unified Checklist Section -->
                    <div class="card shadow-sm category-card mb-4">
                        <div class="card-header bg-soft-primary border-0">
                            <h6 class="card-title text-primary fw-bold mb-0">
                                <i class="ri-list-check-2 me-2"></i> Parameter Checklist Tank Farm & Hydrant
                            </h6>
                        </div>
                        <div class="card-body py-1">
                            {!! renderChecklistItem('kelistrikan_pompa_sumur_1', 'Cek Kelistrikan (A, V) Pompa Sumur 1') !!}
                            {!! renderChecklistItem('kelistrikan_pompa_sumur_2', 'Cek Kelistrikan (A, V) Pompa Sumur 2') !!}
                            {!! renderChecklistItem('kelistrikan_pompa_sumur_4', 'Cek Kelistrikan (A, V) Pompa Sumur 4') !!}
                            {!! renderChecklistItem('kelistrikan_pompa_sumur_5', 'Cek Kelistrikan (A, V) Pompa Sumur 5') !!}
                            
                            {!! renderChecklistItem('pressure_pompa_sumur_1', 'Cek Pressure Pompa Sumur 1') !!}
                            {!! renderChecklistItem('pressure_pompa_sumur_2', 'Cek Pressure Pompa Sumur 2') !!}
                            {!! renderChecklistItem('pressure_pompa_sumur_4', 'Cek Pressure Pompa Sumur 4') !!}
                            {!! renderChecklistItem('pressure_pompa_sumur_5', 'Cek Pressure Pompa Sumur 5') !!}
                            
                            {!! renderChecklistItem('flow_meter_pompa_sumur_1', 'Cek Flow Meter Pompa Sumur 1') !!}
                            {!! renderChecklistItem('flow_meter_pompa_sumur_2', 'Cek Flow Meter Pompa Sumur 2') !!}
                            {!! renderChecklistItem('flow_meter_pompa_sumur_4', 'Cek Flow Meter Pompa Sumur 4') !!}
                            {!! renderChecklistItem('flow_meter_pompa_sumur_5', 'Cek Flow Meter Pompa Sumur 5') !!}
                            
                            {!! renderChecklistItem('drain_lumpur_settling_tank', 'Drain Lumpur Settling Tank') !!}
                            
                            {!! renderChecklistItem('kelistrikan_pompa_10p3', 'Cek Kelistrikan (A, V) Pompa 10P3') !!}
                            {!! renderChecklistItem('kelistrikan_pompa_10p3a', 'Cek Kelistrikan (A, V) Pompa 10P3a') !!}
                            {!! renderChecklistItem('pressure_gauge_intermediate', 'Cek Pressure Gauge Intermediate') !!}
                            {!! renderChecklistItem('level_bandul_tank_farm', 'Cek Level Bandul Tank Farm') !!}
                            {!! renderChecklistItem('flow_meter_fresh_water_tank', 'Cek Flow Meter Fresh Water Tank') !!}
                            {!! renderChecklistItem('flow_meter_fwt_to_ro', 'Cek Flow Meter Fresh Water Tank to Mesin RO') !!}
                            
                            {!! renderChecklistItem('kelistrikan_pompa_10p4', 'Cek Kelistrikan (A, V) Pompa 10P4') !!}
                            {!! renderChecklistItem('kelistrikan_pompa_10p4a', 'Cek Kelistrikan (A, V) Pompa 10P4a') !!}
                            {!! renderChecklistItem('pressure_gauge_pompa_10p4_p4a', 'Cek Pressure Gauge Pompa 10P4 & P4a') !!}
                            
                            {!! renderChecklistItem('kelistrikan_pompa_10p5', 'Cek Kelistrikan (A, V) Pompa 10P5') !!}
                            {!! renderChecklistItem('kelistrikan_pompa_10p5a', 'Cek Kelistrikan (A, V) Pompa 10P5a') !!}
                            {!! renderChecklistItem('kelistrikan_pompa_10p5b', 'Cek Kelistrikan (A, V) Pompa 10P5b') !!}
                            {!! renderChecklistItem('flow_meter_ro_reject_tank', 'Cek Flow Meter RO Reject Tank') !!}
                            {!! renderChecklistItem('pressure_gauge_pompa_10p5_10p5a', 'Cek Pressure Gauge Pompa 10P5 & 10P5a') !!}
                            
                            {!! renderChecklistItem('drain_lumpur_tangki_intermediate', 'Drain Lumpur Tangki Intermediate') !!}
                            {!! renderChecklistItem('inspeksi_all_pompa_tf_intermediate', 'Inspeksi All Pompa Tank Farm dan Intermediet (HLT)') !!}
                            
                            {!! renderChecklistItem('inspeksi_pompa_20p1', 'Inspeksi (HLTE) Pompa 20P1') !!}
                            {!! renderChecklistItem('inspeksi_pompa_20p1a', 'Inspeksi (HLTE) Pompa 20P1a') !!}
                            
                            {!! renderChecklistItem('kelistrikan_pompa_20p2', 'Cek Kelistrikan (A, V) Pompa 20P2') !!}
                            {!! renderChecklistItem('kelistrikan_pompa_20p2a', 'Cek Kelistrikan (A, V) Pompa 20P2a') !!}
                            
                            {!! renderChecklistItem('kelistrikan_pompa_60p1', 'Cek Kelistrikan (A, V) Pompa 60P1') !!}
                            {!! renderChecklistItem('kelistrikan_pompa_60p2', 'Cek Kelistrikan (A, V) Pompa 60P2') !!}
                            {!! renderChecklistItem('kelistrikan_pompa_60p3', 'Cek Kelistrikan (A, V) Pompa 60P3') !!}
                            
                            {!! renderChecklistItem('pressure_gauge_pompa_60p1', 'Cek Pressure Gauge Pompa 60P1') !!}
                            {!! renderChecklistItem('pressure_gauge_pompa_60p2', 'Cek Pressure Gauge Pompa 60P2') !!}
                            {!! renderChecklistItem('pressure_gauge_pompa_60p3', 'Cek Pressure Gauge Pompa 60P3') !!}
                            {!! renderChecklistItem('baterai_pompa_60p3', 'Cek Batterai Pompa 60P3') !!}
                            {!! renderChecklistItem('bahan_bakar_pompa_60p3', 'Cek Bahan Bakar Pompa 60P3') !!}
                            {!! renderChecklistItem('pressure_gauge_water_tank_hydrant', 'Cek Pressure Gauge Water Tank Hydrant') !!}
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
        $('#formAgendaTankFarm').submit(function(e) {
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
                url: "{{ route('agenda-tank-farm.store') }}",
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
                    $('#formAgendaTankFarm').find('input[type="radio"]').each(function() {
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
