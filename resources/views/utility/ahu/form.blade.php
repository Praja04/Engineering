@extends('layouts.app')

@section('title', 'AHU Form')

@section('styles')
<style>
    .card-ahu {
        border-radius: 12px;
        transition: transform 0.2s;
    }
    .card-ahu:hover {
        transform: translateY(-5px);
    }
    .bg-ahu-1 { background-color: #f0f7ff; border-left: 5px solid #0d6efd; }
    .bg-ahu-2 { background-color: #f0fff4; border-left: 5px solid #198754; }
    .bg-ahu-3 { background-color: #fffaf0; border-left: 5px solid #ffc107; }
    .bg-ahu-4 { background-color: #fff5f5; border-left: 5px solid #dc3545; }

    [data-layout-mode="dark"] .bg-ahu-1 { background-color: rgba(13, 110, 253, 0.05); }
    [data-layout-mode="dark"] .bg-ahu-2 { background-color: rgba(25, 135, 84, 0.05); }
    [data-layout-mode="dark"] .bg-ahu-3 { background-color: rgba(255, 193, 7, 0.05); }
    [data-layout-mode="dark"] .bg-ahu-4 { background-color: rgba(220, 53, 69, 0.05); }
    [data-layout-mode="dark"] .card-ahu h6 { color: #f3f3f3; }
    [data-layout-mode="dark"] .card-ahu label { color: #adb5bd; }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="card border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%); border-radius: 12px;">
            <div class="card-body">
                <h4 class="text-white fw-bold mb-1">
                    <i class="ri-windy-line text-warning me-2"></i> AHU - Monitoring Form
                </h4>
                <p class="text-white-50 mb-0">Input laporan harian Air Handling Unit</p>
            </div>
        </div>

        <form id="formAhu">
            @csrf
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jam</label>
                            <input type="text" name="jam" id="jam" class="form-control" placeholder="HH:MM" readonly required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                @foreach([1,2,3,4] as $i)
                <div class="col-xl-6">
                    <div class="card shadow-sm card-ahu bg-ahu-{{$i}}">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">AHU {{$i}}</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="small">Ampere</label>
                                    <input type="number" step="0.01" name="ampere_{{$i}}" class="form-control form-control-sm" placeholder="0.00">
                                </div>
                                <div class="col-6">
                                    <label class="small">Set Temp (°C)</label>
                                    <input type="number" step="0.01" name="set_temp_{{$i}}" class="form-control form-control-sm" placeholder="0.00">
                                </div>
                                <div class="col-6">
                                    <label class="small">Press In (Bar)</label>
                                    <input type="number" step="0.01" name="pressure_in_{{$i}}" class="form-control form-control-sm" placeholder="0.00">
                                </div>
                                <div class="col-6">
                                    <label class="small">Press Out (Bar)</label>
                                    <input type="number" step="0.01" name="pressure_out_{{$i}}" class="form-control form-control-sm" placeholder="0.00">
                                </div>
                                <div class="col-6">
                                    <label class="small">CT In (°C)</label>
                                    <input type="number" step="0.01" name="ct_in_{{$i}}" class="form-control form-control-sm" placeholder="0.00">
                                </div>
                                <div class="col-6">
                                    <label class="small">CT Out (°C)</label>
                                    <input type="number" step="0.01" name="ct_out_{{$i}}" class="form-control form-control-sm" placeholder="0.00">
                                </div>
                                <div class="col-12">
                                    <label class="small">Temp Out (°C)</label>
                                    <input type="number" step="0.01" name="temp_out_{{$i}}" class="form-control form-control-sm" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-end mt-4 mb-5">
                <button type="submit" class="btn btn-primary px-5 shadow">
                    <i class="ri-save-line me-1"></i> Simpan Data AHU
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        flatpickr('#jam', {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true,
            minuteIncrement: 1,
            allowInput: false,
        });
    });

    $('#formAhu').submit(function(e) {
        e.preventDefault();
        let btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="ri-loader-4-line ri-spin me-1"></i> Menyimpan...');

        $.ajax({
            url: "{{ route('ahu.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                Swal.fire('Berhasil!', res.message, 'success');
                $('#formAhu')[0].reset();
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                Swal.fire('Gagal!', msg, 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> Simpan Data AHU');
            }
        });
    });
</script>
@endsection
