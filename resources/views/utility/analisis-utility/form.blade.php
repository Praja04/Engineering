@extends('layouts.app')

@section('title', 'Analisis Utility Form')

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
                        style="background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); border-radius: 12px;">
                        <div class="card-body">
                            <h4 class="text-white fw-bold mb-1">
                                <i class="ri-checkbox-circle-line text-warning me-2"></i>
                                Analisis Utility - Daily Checklist Form
                            </h4>
                            <p class="text-white-50 mb-0">
                                Checklist harian analisis pH, TDS, Turbidity, Chlorine, dan Hardness air
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form id="formAnalisisUtility">
                        @csrf

                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-teal">
                                            <i class="ri-calendar-line me-1"></i> Pilih Tanggal Analisis
                                        </label>
                                        <input type="date" name="tanggal" class="form-control"
                                            value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Helper function to generate checklist item row -->
                        @php
                            if (!function_exists('renderAnalisisItem')) {
                                function renderAnalisisItem($fieldName, $labelText, $unit = '')
                                {
                                    $unitAddon = $unit ? '<span class="input-group-text">' . $unit . '</span>' : '';
                                    return '
                                    <div class="checklist-item d-flex justify-content-between align-items-center flex-wrap py-3 px-2 border-bottom">
                                        <div class="fw-medium text-dark flex-grow-1 pe-2 mb-2 mb-sm-0">
                                            ' .
                                        $labelText .
                                        '
                                        </div>
                                        <div style="width: 200px;">
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" name="' .
                                        $fieldName .
                                        '" placeholder="0.00">
                                                ' .
                                        $unitAddon .
                                        '
                                            </div>
                                        </div>
                                    </div>
                                ';
                                }
                            }
                        @endphp

                        <!-- Group 1: pH -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #3b82f6;">
                            <div class="card-header bg-soft-info border-0">
                                <h6 class="card-title text-info fw-bold mb-0">
                                    <i class="ri-contrast-drop-line me-2"></i> Pengecekan pH Air
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderAnalisisItem('ph_fw_storage', 'Masukkan pH FW Storage', 'pH') !!}
                                {!! renderAnalisisItem('ph_ws_storage', 'Masukkan pH WS Storage', 'pH') !!}
                                {!! renderAnalisisItem('ph_ro_storage', 'Masukkan pH RO Storage', 'pH') !!}
                                {!! renderAnalisisItem('ph_in_mmf', 'Masukkan pH In MMF', 'pH') !!}
                                {!! renderAnalisisItem('ph_buffer_tank_ws', 'Masukkan pH Buffer Tank WS', 'pH') !!}
                                {!! renderAnalisisItem('ph_outlet_ws', 'Masukkan pH Outlet WS', 'pH') !!}
                                {!! renderAnalisisItem('ph_menara_ws', 'Masukkan pH Menara WS', 'pH') !!}
                                {!! renderAnalisisItem('ph_depo_lt1', 'Masukkan pH Depo Lt.1', 'pH') !!}
                                {!! renderAnalisisItem('ph_depo_lt2', 'Masukkan pH Depo Lt.2', 'pH') !!}
                                {!! renderAnalisisItem('ph_cooling_tower', 'Masukkan pH Cooling Tower', 'pH') !!}
                                {!! renderAnalisisItem('ph_boiler', 'Masukkan pH Boiler', 'pH') !!}
                            </div>
                        </div>

                        <!-- Group 2: TDS -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #f59e0b;">
                            <div class="card-header bg-soft-warning border-0">
                                <h6 class="card-title text-warning fw-bold mb-0">
                                    <i class="ri-water-flash-line me-2"></i> Pengecekan TDS Air
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderAnalisisItem('tds_fw_storage', 'Masukkan TDS FW Storage', 'ppm') !!}
                                {!! renderAnalisisItem('tds_ws_storage', 'Masukkan TDS WS Storage', 'ppm') !!}
                                {!! renderAnalisisItem('tds_ro_storage', 'Masukkan TDS RO Storage', 'ppm') !!}
                                {!! renderAnalisisItem('tds_in_mmf', 'Masukkan TDS In MMF', 'ppm') !!}
                                {!! renderAnalisisItem('tds_out_ro', 'Masukkan TDS Out RO', 'ppm') !!}
                                {!! renderAnalisisItem('tds_menara_ws', 'Masukkan TDS menara WS', 'ppm') !!}
                                {!! renderAnalisisItem('tds_daily_tank_dissolver', 'Masukkan TDS daily Tank dissolver', 'ppm') !!}
                                {!! renderAnalisisItem('tds_depo_lt1', 'Masukkan TDS Depo Lt.1', 'ppm') !!}
                                {!! renderAnalisisItem('tds_depo_lt2', 'Masukkan TDS Depo Lt.2', 'ppm') !!}
                                {!! renderAnalisisItem('tds_cooling_tower', 'Masukkan TDS Cooling Tower', 'ppm') !!}
                                {!! renderAnalisisItem('tds_boiler', 'Masukkan TDS Boiler', 'ppm') !!}
                            </div>
                        </div>

                        <!-- Group 3: Turbidity & Chlorine -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #ef4444;">
                            <div class="card-header bg-soft-danger border-0">
                                <h6 class="card-title text-danger fw-bold mb-0">
                                    <i class="ri-bubble-chart-line me-2"></i> Turbidity & Chlorine
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderAnalisisItem('turbidity_in_mmf', 'Masukkan Turbidity IN MMF', 'NTU') !!}
                                {!! renderAnalisisItem('turbidity_out_mmf', 'Masukkan Turbidity Out MMF', 'NTU') !!}
                                {!! renderAnalisisItem('turbidity_cooling_tower', 'Masukkan Turbidity Cooling Tower', 'NTU') !!}
                                {!! renderAnalisisItem('chlorine_mmf', 'Masukkan Chlorine MMF', 'ppm') !!}
                                {!! renderAnalisisItem('chlorine_menara', 'Masukkan Chlorine Menara', 'ppm') !!}
                                {!! renderAnalisisItem('chlorine_depo_lt1', 'Masukkan Chlorine Depo LT.1', 'ppm') !!}
                                {!! renderAnalisisItem('chlorine_depo_lt2', 'Masukkan Chlorine Depo LT.2', 'ppm') !!}
                                {!! renderAnalisisItem('chlorine_daily_tank_dissolver', 'Masukkan Chlorine Daily Tank Dissolver', 'ppm') !!}
                            </div>
                        </div>

                        <!-- Group 4: Hardness -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #8b5cf6;">
                            <div class="card-header bg-soft-purple border-0" style="background-color: #f5f3ff;">
                                <h6 class="card-title text-purple fw-bold mb-0" style="color: #6d28d9;">
                                    <i class="ri-flask-line me-2"></i> Hardness Air
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderAnalisisItem('hardness_inlet_ws', 'Masukkan hardness Inlet WS', 'ppm') !!}
                                {!! renderAnalisisItem('hardness_outlet_ws', 'Masukkan hardness Outlet WS', 'ppm') !!}
                                {!! renderAnalisisItem('hardness_ws_storage', 'Masukkan hardness WS Storage', 'ppm') !!}
                                {!! renderAnalisisItem('hardness_ct', 'Masukkan hardness CT', 'ppm') !!}
                                {!! renderAnalisisItem('hardness_ro', 'Masukkan hardness RO', 'ppm') !!}
                                {!! renderAnalisisItem('hardness_boiler', 'Masukkan hardness Boiler', 'ppm') !!}
                            </div>
                        </div>

                        <div class="text-end mb-5">
                            <button type="submit" class="btn btn-teal px-5 btn-lg rounded-pill shadow-sm text-white"
                                style="background-color: #0f766e;">
                                <i class="ri-send-plane-2-line me-1"></i> Submit
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
            $('#formAnalisisUtility').submit(function(e) {
                e.preventDefault();

                let hasValue = false;
                $(this).find('input[type="number"]').each(function() {
                    if ($(this).val().trim() !== '') {
                        hasValue = true;
                        return false;
                    }
                });

                if (!hasValue) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Minimal harus ada 1 parameter yang diisi sebelum submit.'
                    });
                    return;
                }

                let btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).text('Loading...');

                $.ajax({
                    url: "{{ route('analisis-utility.store') }}",
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

                        // Reset inputs
                        $('#formAnalisisUtility').find('input[type="number"]').val('');
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
                            '<i class="ri-send-plane-2-line me-1"></i> Submit Analisis');
                    }
                });
            });
        });
    </script>
@endsection
