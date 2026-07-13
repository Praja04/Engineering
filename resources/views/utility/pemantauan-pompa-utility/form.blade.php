@extends('layouts.app')

@section('title', 'Pemantauan Pompa Utility Form')

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
                                Pemantauan Pompa Utility - Daily Checklist Form
                            </h4>
                            <p class="text-white-50 mb-0">
                                Checklist harian pemantauan pompa utility & fans
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form id="formPemantauanPompa">
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

                        <!-- Helper function to generate  item row -->
                        @php
                            if (!function_exists('renderItem')) {
                                function renderItem($fieldName, $labelText)
                                {
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
                                            <span class="input-group-text">A</span>
                                        </div>
                                    </div>
                                </div>
                                ';
                                }
                            }
                        @endphp

                        <!-- Group 1: Pompa Ampere 1 -->
                        <div class="card shadow-sm category-card mb-4">
                            <div class="card-header bg-soft-primary border-0">
                                <h6 class="card-title text-primary fw-bold mb-0">
                                    <i class="ri-bubble-chart-line me-2"></i> Cek Ampere Pompa Utility
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderItem('ampere_pompa_10p3', 'Cek Ampere Pompa 10P3') !!}
                                {!! renderItem('ampere_pompa_10p3a', 'Cek Ampere Pompa 10P3A') !!}
                                {!! renderItem('ampere_pompa_10p4', 'Cek Ampere Pompa 10P4') !!}
                                {!! renderItem('ampere_pompa_10p4a', 'Cek Ampere Pompa 10P4A') !!}
                                {!! renderItem('ampere_pompa_10p5b', 'Cek Ampere Pompa 10P5B') !!}
                                {!! renderItem('ampere_pompa_20p1', 'Cek Ampere Pompa 20P1') !!}
                                {!! renderItem('ampere_pompa_20p1a', 'Cek Ampere Pompa 20P1A') !!}
                                {!! renderItem('ampere_pompa_20p2', 'Cek Ampere Pompa 20P2') !!}
                                {!! renderItem('ampere_pompa_20p2a', 'Cek Ampere Pompa 20P2A') !!}
                                {!! renderItem('ampere_pompa_60p1', 'Cek Ampere Pompa 60P1') !!}
                                {!! renderItem('ampere_pompa_60p2', 'Cek Ampere Pompa 60P2') !!}
                                {!! renderItem('ampere_pompa_60p3', 'Cek Ampere Pompa 60P3') !!}
                            </div>
                        </div>

                        <!-- Group 2: Pompa TF-WS-CIP -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #f77f00;">
                            <div class="card-header bg-soft-warning border-0">
                                <h6 class="card-title text-warning-emphasis fw-bold mb-0">
                                    <i class="ri-settings-line me-2"></i> Cek Ampere Pompa TF, WS, CIP & CT
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderItem('ampere_pompa_hp_pump', 'Cek Ampere Pompa HP PUMP') !!}
                                {!! renderItem('ampere_pompa_cip_pump', 'Cek Ampere Pompa CIP PUMP') !!}
                                {!! renderItem('ampere_pompa_tf_ws', 'Cek Ampere Pompa TF WS') !!}
                                {!! renderItem('ampere_pompa_ct_10000p1', 'Cek Ampere pompa CT 10000P1') !!}
                                {!! renderItem('ampere_pompa_ct_10000p2', 'Cek Ampere pompa CT 10000P2') !!}
                                {!! renderItem('ampere_pompa_ct_10000p3', 'Cek Ampere pompa CT 10000P3') !!}
                            </div>
                        </div>

                        <!-- Group 3: Fans -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #e71d36;">
                            <div class="card-header bg-soft-danger border-0">
                                <h6 class="card-title text-danger-emphasis fw-bold mb-0">
                                    <i class="ri-windy-line me-2"></i> Cek Ampere Fan
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderItem('ampere_fan_1', 'Cek Ampere Fan 1') !!}
                                {!! renderItem('ampere_fan_2', 'Cek Ampere Fan 2') !!}
                                {!! renderItem('ampere_fan_3', 'Cek Ampere Fan 3') !!}
                                {!! renderItem('ampere_fan_4', 'Cek Ampere Fan 4') !!}
                            </div>
                        </div>

                        <div class="text-end mb-5">
                            <button type="submit" class="btn btn-primary px-5 btn-lg rounded-pill shadow-sm">
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
            $('#formPemantauanPompa').submit(function(e) {
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
                    url: "{{ route('pemantauan-pompa-utility.store') }}",
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
                        $('#formPemantauanPompa').find('input[type="number"]').val('');
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
