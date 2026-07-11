@extends('layouts.app')

@section('title', 'Reverse Osmosis Log Form')

@section('styles')
<style>
    .flatpickr-input {
        background-color: inherit !important;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm"
                    style="background: linear-gradient(135deg, #1b4965 0%, #0f3057 100%); border-radius: 12px;">
                    <div class="card-body">
                        <h4 class="text-white fw-bold mb-1">
                            <i class="ri-water-flash-line text-info me-2"></i>
                            Reverse Osmosis - Log Form
                        </h4>
                        <p class="text-white-50 mb-0">
                            Input log harian operasional Reverse Osmosis
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form id="formReverseOsmosis">
                            @csrf
                            <div class="mb-3">
                                <h6 class="fw-bold text-primary mb-2">
                                    <i class="ri-file-list-3-line me-1"></i> Informasi Dasar
                                </h6>
                                <hr class="mt-0">
                            </div>

                            <div class="row mb-4 g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <!-- MMF Section -->
                            <div class="mb-3">
                                <h6 class="fw-bold text-success mb-2">
                                    <i class="ri-dashboard-3-line me-1"></i> Data MMF (Multi Media Filter)
                                </h6>
                                <hr class="mt-0">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Pressure Feed MMF 1 (Bar)</label>
                                    <input type="number" step="0.01" name="mmf_pressure_feed_1" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pressure Feed MMF 2 (Bar)</label>
                                    <input type="number" step="0.01" name="mmf_pressure_feed_2" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pressure Produk MMF 1 (Bar)</label>
                                    <input type="number" step="0.01" name="mmf_pressure_produk_1" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pressure Produk MMF 2 (Bar)</label>
                                    <input type="number" step="0.01" name="mmf_pressure_produk_2" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Output Flow MMF 1 (Bar)</label>
                                    <input type="number" step="0.01" name="mmf_output_flow_1" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Output Flow MMF 2 (Bar)</label>
                                    <input type="number" step="0.01" name="mmf_output_flow_2" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6 d-flex align-items-center mt-4">
                                    <div class="form-check form-switch form-switch-md">
                                        <input type="checkbox" name="mmf_status_backwash_1" class="form-check-input" id="backwash1" value="1">
                                        <label class="form-check-label fw-bold text-dark" for="backwash1">Status Backwash MMF 1</label>
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex align-items-center mt-4">
                                    <div class="form-check form-switch form-switch-md">
                                        <input type="checkbox" name="mmf_status_backwash_2" class="form-check-input" id="backwash2" value="1">
                                        <label class="form-check-label fw-bold text-dark" for="backwash2">Status Backwash MMF 2</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Micron Filter Section -->
                            <div class="mb-3">
                                <h6 class="fw-bold text-warning mb-2">
                                    <i class="ri-filter-2-line me-1"></i> Data Micron Filter
                                </h6>
                                <hr class="mt-0">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Pressure Inlet Micron Filter (Bar)</label>
                                    <input type="number" step="0.01" name="micron_filter_pressure_inlet" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pressure Outlet Micron Filter (Bar)</label>
                                    <input type="number" step="0.01" name="micron_filter_pressure_outlet" class="form-control" placeholder="0.00">
                                </div>
                            </div>

                            <!-- RO Section -->
                            <div class="mb-3">
                                <h6 class="fw-bold text-info mb-2">
                                    <i class="ri-bubble-chart-line me-1"></i> Data RO (Reverse Osmosis)
                                </h6>
                                <hr class="mt-0">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Permeate Flowrate (m3/jam)</label>
                                    <input type="number" step="0.01" name="ro_permeate_flowrate" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">RO Reject Flowrate (m3/jam)</label>
                                    <input type="number" step="0.01" name="ro_reject_flowrate" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Flowmeter Accumulation (m3)</label>
                                    <input type="number" step="0.01" name="ro_flowmeter_accumulation" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pressure Inlet 1st Stage (Bar)</label>
                                    <input type="number" step="0.01" name="ro_pressure_inlet_1st_stage" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pressure Inlet 2nd Stage (Bar)</label>
                                    <input type="number" step="0.01" name="ro_pressure_inlet_2nd_stage" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pressure Concentrate (Bar)</label>
                                    <input type="number" step="0.01" name="ro_pressure_concentrate" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pressure RO Produk (Bar)</label>
                                    <input type="number" step="0.01" name="ro_pressure_produk" class="form-control" placeholder="0.00">
                                </div>
                            </div>

                            <!-- CIP Section -->
                            <div class="mb-3">
                                <h6 class="fw-bold text-danger mb-2">
                                    <i class="ri-refresh-line me-1"></i> Data CIP (Clean In Place)
                                </h6>
                                <hr class="mt-0">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Keterangan CIP</label>
                                    <input type="text" name="cip_keterangan" class="form-control" placeholder="Keterangan CIP...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Chemical CIP</label>
                                    <input type="text" name="cip_jenis_chemical" class="form-control" placeholder="Jenis Chemical...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Qty Chemical CIP</label>
                                    <input type="text" name="cip_qty_chemical" class="form-control" placeholder="Qty Chemical...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Hasil CIP</label>
                                    <input type="text" name="cip_hasil" class="form-control" placeholder="Hasil CIP...">
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="ri-send-plane-2-line me-1"></i> Submit Log
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
        $('#formReverseOsmosis').submit(function(e) {
            e.preventDefault();

            let hasValue = false;
            $(this).find('input[type="number"], input[type="text"]').each(function() {
                if ($(this).val().trim() !== '') {
                    hasValue = true;
                    return false;
                }
            });

            if (!hasValue) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Minimal harus ada 1 nilai data teknis yang diisi sebelum submit.'
                });
                return;
            }

            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Loading...');

            $.ajax({
                url: "{{ route('reverse-osmosis.store') }}",
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
                    $('#formReverseOsmosis')[0].reset();
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
                        '<i class="ri-send-plane-2-line me-1"></i> Submit Log');
                }
            });
        });
    });
</script>
@endsection
