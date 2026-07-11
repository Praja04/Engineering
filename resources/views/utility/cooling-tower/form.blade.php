@extends('layouts.app')

@section('title', 'Cooling Tower Log Form')

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
                    style="background: linear-gradient(135deg, #0d3b66 0%, #001f3f 100%); border-radius: 12px;">
                    <div class="card-body">
                        <h4 class="text-white fw-bold mb-1">
                            <i class="ri-temp-cold-line text-info me-2"></i>
                            Cooling Tower - Log Form
                        </h4>
                        <p class="text-white-50 mb-0">
                            Input log harian cooling tower per 4 jam
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form id="formCoolingTower">
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

                                <div class="col-md-4">
                                    <label class="form-label">Jam Pencatatan</label>
                                    <select name="jam" class="form-select" required>
                                        <option value="">-- Pilih Jam --</option>
                                        <option value="08:00">08:00</option>
                                        <option value="12:00">12:00</option>
                                        <option value="16:00">16:00</option>
                                        <option value="20:00">20:00</option>
                                        <option value="00:00">00:00 (24:00)</option>
                                        <option value="04:00">04:00</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold text-success mb-2">
                                    <i class="ri-dashboard-3-line me-1"></i> Data Teknis
                                </h6>
                                <hr class="mt-0">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-12"><span class="badge bg-soft-primary text-primary">Pressure (Bar)</span></div>
                                <div class="col-md-6">
                                    <label class="form-label">Pressure CT IN</label>
                                    <input type="number" step="0.01" name="pressure_ct_in" class="form-control"
                                        placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pressure CT OUT</label>
                                    <input type="number" step="0.01" name="pressure_ct_out" class="form-control"
                                        placeholder="0.00">
                                </div>

                                <div class="col-12 mt-4"><span class="badge bg-soft-success text-success">Temperatur (°C)</span></div>
                                <div class="col-md-6">
                                    <label class="form-label">Temperatur CT IN</label>
                                    <input type="number" step="0.01" name="temp_ct_in" class="form-control"
                                        placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Temperatur CT OUT</label>
                                    <input type="number" step="0.01" name="temp_ct_out" class="form-control"
                                        placeholder="0.00">
                                </div>

                                <div class="col-12 mt-4"><span class="badge bg-soft-info text-info">Flowrate RO to CT</span></div>
                                <div class="col-md-6">
                                    <label class="form-label">Flowrate RO Awal</label>
                                    <input type="number" step="0.01" name="flowrate_ro_awal" class="form-control"
                                        placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Flowrate RO Akhir</label>
                                    <input type="number" step="0.01" name="flowrate_ro_akhir" class="form-control"
                                        placeholder="0.00">
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
        $('#formCoolingTower').submit(function(e) {
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
                    text: 'Minimal harus ada 1 nilai data teknis yang diisi sebelum submit.'
                });
                return;
            }

            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Loading...');

            $.ajax({
                url: "{{ route('cooling-tower.store') }}",
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
                    $('#formCoolingTower')[0].reset();
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
