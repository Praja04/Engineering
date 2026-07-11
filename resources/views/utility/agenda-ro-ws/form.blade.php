@extends('layouts.app')

@section('title', 'Agenda RO-WS Checklist Form')

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
                                Agenda RO-WS - Daily Checklist Form
                            </h4>
                            <p class="text-white-50 mb-0">
                                Checklist harian agenda Reverse Osmosis & Water Softener
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form id="formAgendaRoWs">
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

                        <!-- Group 1: Reverse Osmosis (RO) -->
                        <div class="card shadow-sm category-card mb-4">
                            <div class="card-header bg-soft-primary border-0">
                                <h6 class="card-title text-primary fw-bold mb-0">
                                    <i class="ri-bubble-chart-line me-2"></i> Agenda Reverse Osmosis (RO)
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderChecklistItem('inspeksi_hpt_pump', 'Inspeksi (HLT) High Pressure Pump') !!}
                                {!! renderChecklistItem('inspeksi_cip_pump', 'Inspeksi (HLT) CIP Pump') !!}
                                {!! renderChecklistItem('inspeksi_blower_ro', 'Inspeksi (HLT) Blower RO') !!}
                                {!! renderChecklistItem('cek_chemical', 'Cek Chemical') !!}
                                {!! renderChecklistItem('pencatatan_flow_meter_produksi', 'Pencatatan Flow Meter Produksi RO Produk') !!}
                                {!! renderChecklistItem('cek_nilai_conductivity', 'Cek Nilai Conductivity') !!}
                                {!! renderChecklistItem('cek_dp_1st_2st', 'Cek ΔP 1st & 2st') !!}
                                {!! renderChecklistItem('cek_dp_mmf_1_2', 'Cek ΔP MMF #1 & MMF #2') !!}
                                {!! renderChecklistItem('pencatatan_flow_meter_konsumsi', 'Pencatatan Flow Meter Konsumsi RO Produk') !!}
                                {!! renderChecklistItem('backwash_mmf_1', 'Backwash MMF #1') !!}
                                {!! renderChecklistItem('backwash_mmf_2', 'Backwash MMF #2') !!}
                                {!! renderChecklistItem('cek_kondisi_rotameter_mmf_1', 'Cek Kondisi Rota Meter MMF 1') !!}
                                {!! renderChecklistItem('cek_kondisi_rotameter_mmf_2', 'Cek Kondisi Rota Meter MMF 2') !!}
                                {!! renderChecklistItem('cek_kondisi_rotameter_ro_product', 'Cek Kondisi Rotameter RO Product') !!}
                                {!! renderChecklistItem('cek_kondisi_rotameter_ro_reject', 'Cek Kondisi Rotameter RO Reject') !!}
                                {!! renderChecklistItem('kalibrasi_dosis_kimia', 'Kalibrasi Dosis Penggunaan Kimia') !!}
                                {!! renderChecklistItem('cleaning_unit_ro', 'Cleaning Unit RO') !!}
                                {!! renderChecklistItem('cleaning_unit_mmf_1', 'Cleaning Unit MMF 1') !!}
                                {!! renderChecklistItem('cleaning_unit_mmf_2', 'Cleaning Unit MMF 2') !!}
                            </div>
                        </div>

                        <!-- Group 2: Water Softener (WS) -->
                        <div class="card shadow-sm category-card mb-4" style="border-left-color: #e71d36;">
                            <div class="card-header bg-soft-danger border-0">
                                <h6 class="card-title text-danger-emphasis fw-bold mb-0">
                                    <i class="ri-drop-line me-2"></i> Agenda Water Softener (WS)
                                </h6>
                            </div>
                            <div class="card-body py-1">
                                {!! renderChecklistItem('cek_output_hardness', 'Cek Output Hardness') !!}
                                {!! renderChecklistItem('cek_flow_produk', 'Cek Flow Produk') !!}
                                {!! renderChecklistItem('regenerasi_mesin_ws', 'Regenerasi Mesin Water Softener') !!}
                                {!! renderChecklistItem('cek_pompa_transfer', 'Cek Kondisi Pompa Transfer (H,L,T)') !!}
                                {!! renderChecklistItem('cek_pompa_suplai', 'Cek Kondisi Pompa Suplai (H,L,T)') !!}
                                {!! renderChecklistItem('cleaning_tanki_buffer_ws', 'Cleaning Tanki Buffer WS') !!}
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
            $('#formAgendaRoWs').submit(function(e) {
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
                    url: "{{ route('agenda-ro-ws.store') }}",
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
                        $('#formAgendaRoWs').find('input[type="radio"]').each(function() {
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
