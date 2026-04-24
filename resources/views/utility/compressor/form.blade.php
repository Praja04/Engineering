@extends('layouts.app')

@section('title', 'Compressor Log Form')

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
                    style="background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%); border-radius: 12px;">
                    <div class="card-body">
                        <h4 class="text-white fw-bold mb-1">
                            <i class="ri-rhythm-line text-info me-2"></i>
                            Compressor - Log Form
                        </h4>
                        <p class="text-white-50 mb-0">
                            Input log harian compressor per 4 jam
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form id="formCompressor">
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
                                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Jam Pencatatan</label>
                                    <select name="jam" class="form-select" required>
                                        <option value="">-- Pilih Jam --</option>
                                        <option value="08:00">08:00</option>
                                        <option value="12:00">12:00</option>
                                        <option value="16:00">16:00</option>
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
                                <div class="col-12"><span class="badge bg-soft-primary text-primary">Pressure Outlet (Bar)</span></div>
                                <div class="col-md-3">
                                    <label class="form-label">Outlet 1</label>
                                    <input type="number" step="0.01" name="pressure_outlet_1" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Outlet 2</label>
                                    <input type="number" step="0.01" name="pressure_outlet_2" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Outlet 3</label>
                                    <input type="number" step="0.01" name="pressure_outlet_3" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Outlet 4</label>
                                    <input type="number" step="0.01" name="pressure_outlet_4" class="form-control" placeholder="0.00">
                                </div>

                                <div class="col-12 mt-4"><span class="badge bg-soft-success text-success">Element Outlet (°C)</span></div>
                                <div class="col-md-4">
                                    <label class="form-label">Element 1</label>
                                    <input type="number" step="0.01" name="element_outlet_1" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Element 2</label>
                                    <input type="number" step="0.01" name="element_outlet_2" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Element 4</label>
                                    <input type="number" step="0.01" name="element_outlet_4" class="form-control" placeholder="0.00">
                                </div>

                                <div class="col-md-4 mt-4">
                                    <label class="form-label fw-bold text-danger">Load Percent (%)</label>
                                    <input type="number" step="0.01" name="load_percent" class="form-control" placeholder="0.00">
                                </div>

                                <div class="col-12 mt-4"><span class="badge bg-soft-info text-info">Running Hour</span></div>
                                <div class="col-md-3">
                                    <label class="form-label">RH 1</label>
                                    <input type="number" step="0.01" name="running_hour_1" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">RH 2</label>
                                    <input type="number" step="0.01" name="running_hour_2" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">RH 3</label>
                                    <input type="number" step="0.01" name="running_hour_3" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">RH 4</label>
                                    <input type="number" step="0.01" name="running_hour_4" class="form-control" placeholder="0.00">
                                </div>

                                <div class="col-12 mt-4"><span class="badge bg-soft-dark text-dark">Motor Start</span></div>
                                <div class="col-md-3">
                                    <label class="form-label">Start 1</label>
                                    <input type="number" step="0.01" name="motor_start_1" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Start 2</label>
                                    <input type="number" step="0.01" name="motor_start_2" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Start 3</label>
                                    <input type="number" step="0.01" name="motor_start_3" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Start 4</label>
                                    <input type="number" step="0.01" name="motor_start_4" class="form-control" placeholder="0.00">
                                </div>

                                <div class="col-12 mt-4"><span class="badge bg-soft-secondary text-secondary">Lain-lain</span></div>
                                <div class="col-md-4">
                                    <label class="form-label">Accumulated Volume</label>
                                    <input type="number" step="0.01" name="accumulated_volume" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Temp Comp IR</label>
                                    <input type="number" step="0.01" name="temperature_comp_ir" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pressure In</label>
                                    <input type="number" step="0.01" name="pressure_in" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pressure Out</label>
                                    <input type="number" step="0.01" name="pressure_out" class="form-control" placeholder="0.00">
                                </div>

                                <div class="col-12 mt-4"><span class="badge bg-soft-warning text-warning">Suhu Dryer (°C)</span></div>
                                <div class="col-md-4">
                                    <label class="form-label">Dryer TR15</label>
                                    <input type="number" step="0.01" name="suhu_dryer_tr15" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Dryer FX250</label>
                                    <input type="number" step="0.01" name="suhu_dryer_fx250" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Dryer IR</label>
                                    <input type="number" step="0.01" name="suhu_dryer_ir" class="form-control" placeholder="0.00">
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
        $('#formCompressor').submit(function(e) {
            e.preventDefault();
            let btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Loading...');

            $.ajax({
                url: "{{ route('compressor.store') }}",
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
                    $('#formCompressor')[0].reset();
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
                    btn.prop('disabled', false).html('<i class="ri-send-plane-2-line me-1"></i> Submit Log');
                }
            });
        });
    })
</script>
@endsection