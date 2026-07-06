@extends('layouts.app')

@section('title', 'Form Boiler Log')

@section('styles')
    <style>
        .card-header-custom {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            padding: 1.25rem;
            border-radius: 12px 12px 0 0;
        }
        .form-label {
            color: #374151;
        }
        .section-divider {
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 text-white fw-bold">
                            <i class="ri-add-circle-line me-2 text-warning"></i>
                            Form Input Log Boiler
                        </h4>
                        <p class="mb-0 text-white-50 small">Engineering Utility · Input Manual Parameter Log Boiler</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form id="formCreate">
                        <div class="row g-3 mb-4 section-divider">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal</label>
                                <input type="date" name="tanggal" id="create_tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jam</label>
                                <select name="jam" id="create_jam" class="form-select" required>
                                    @for($i = 0; $i < 24; $i++)
                                        @php $time = sprintf('%02d:00', $i); @endphp
                                        <option value="{{ $time }}" {{ $i === 7 ? 'selected' : '' }}>{{ $time }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <h5 class="fw-bold text-primary mb-3"><i class="ri-sensor-line me-1"></i> Data Manual (Kolom Khusus Excel)</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Water Flow - Total Count (m3/h)</label>
                                <input type="number" step="any" name="water_flow_total" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Water HMI - Flow Rate (m3/h)</label>
                                <input type="number" step="any" name="water_hmi_flow_rate" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Water HMI - Total Count (m3)</label>
                                <input type="number" step="any" name="water_hmi_total" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Temp Flue Gass (°C)</label>
                                <input type="number" step="any" name="flue_gass_temp" class="form-control">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ url('utility/boiler-logs/data') }}" class="btn btn-secondary px-4">Kembali</a>
                            <button type="button" id="btnSaveCreate" class="btn btn-primary px-5">
                                <i class="ri-save-line me-1"></i> Simpan Data
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
            // ── Save Input Manual ──
            $('#btnSaveCreate').click(function() {
                const form = $('#formCreate');
                
                // Show loading
                Swal.fire({
                    title: 'Menyimpan Data...',
                    html: 'Harap tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ route('boiler-logs.store') }}",
                    method: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: res.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Redirect to data view
                            window.location.href = "{{ url('utility/boiler-logs/data') }}";
                        });
                    },
                    error: function(xhr) {
                        let msg = 'Gagal menyimpan data.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            } else if (xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                        }
                        Swal.fire({
                            title: 'Gagal!',
                            html: msg,
                            icon: 'error'
                        });
                    }
                });
            });
        });
    </script>
@endsection
