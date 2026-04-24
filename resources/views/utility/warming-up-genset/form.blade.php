@extends('layouts.app')

@section('title', ' Warming Up Genset Form')

@section('styles')
<style>
    .flatpickr-input {
        background-color: inherit !important;
        cursor: pointer;
    }

    .flatpickr-input[readonly] {
        cursor: pointer;
    }

    /* Pastikan numInput di flatpickr time tidak ada AM/PM */
    .flatpickr-time .flatpickr-am-pm {
        display: none !important;
    }

    .flatpickr-time input.flatpickr-hour,
    .flatpickr-time input.flatpickr-minute {
        font-size: 1.1rem;
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
                            <i class="ri-battery-2-charge-fill text-warning me-2"></i>
                            Warming Up Genset - Form
                        </h4>
                        <p class="text-white-50 mb-0">
                            Input laporan harian genset
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form id="formGenset">
                            @csrf
                            <div class="mb-3">
                                <h6 class="fw-bold text-primary mb-2">
                                    <i class="ri-file-list-3-line me-1"></i> Informasi Laporan
                                </h6>
                                <hr class="mt-0">
                            </div>

                            <div class="row mb-4 g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Laporan</label>
                                    <input type="date" name="tanggal_laporan" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Jam Pencatatan</label>
                                    <input type="text" name="jam_pencatatan" id="jam_pencatatan" class="form-control"
                                        placeholder="HH:MM" autocomplete="off" readonly required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold text-warning mb-2">
                                    <i class="ri-user-star-line me-1"></i> Approval
                                </h6>
                                <hr class="mt-0">
                            </div>

                            <div class="row mb-4 g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Foreman</label>
                                    <select name="foreman_id" id="select_foreman" class="form-control" required>
                                        <option value="">-- Pilih Foreman --</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Supervisor</label>
                                    <select name="supervisor_id" id="select_supervisor" class="form-control" required>
                                        <option value="">-- Pilih Supervisor --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 g-3">
                                <h6 class="fw-bold text-success mb-2">
                                    <i class="ri-settings-3-line me-1"></i> Data Operasional Genset
                                </h6>
                                <hr class="mt-0">
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Engine Speed (RPM)</label>
                                    <input type="number" name="engine_speed" class="form-control" placeholder="0.00"
                                        step="0.01" min="0">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Engine Temperature</label>
                                    <input type="number" step="0.01" name="engine_temperature" class="form-control"
                                        placeholder="0.00" step="0.01" min="0">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Engine Oil Pressure</label>
                                    <input type="number" step="0.01" name="engine_oil_pressure" class="form-control"
                                        placeholder="0.00" step="0.01" min="0">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Battery Voltage</label>
                                    <input type="number" step="0.01" name="battery_voltage" class="form-control"
                                        placeholder="0.00" step="0.01" min="0">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Charge Alt Voltage</label>
                                    <input type="number" step="0.01" name="charge_alt_voltage" class="form-control"
                                        placeholder="0.00" step="0.01" min="0">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Running Hour</label>
                                    <input type="number" step="0.01" name="running_hour" class="form-control"
                                        placeholder="0.00" step="0.01" min="0">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Frequency</label>
                                    <input type="number" step="0.01" name="frequency" class="form-control"
                                        placeholder="0.00" step="0.01" min="0">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Status Oil 1</label>
                                    <input type="number" step="0.01" name="status_oil_1" class="form-control"
                                        placeholder="0.00" step="0.01" min="0">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Status Oil 2</label>
                                    <input type="number" step="0.01" name="status_oil_2" class="form-control"
                                        placeholder="0.00" step="0.01" min="0">
                                </div>

                            </div>

                            {{-- 🔘 SUBMIT --}}
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ri-send-plane-2-line me-1"></i> Submit
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
        flatpickr('#jam_pencatatan', {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true,
            minuteIncrement: 1,
            allowInput: false,
        });

        loadApprover();

        function loadApprover() {
            $.get('/api/utility/users/approvers', function(data) {
                // Isi dropdown Foreman — dari data.staff (jabatan bukan operator, dept engineering)
                const foremanList = data.staff ?? [];
                let foremanOpts = '<option value="">— Pilih Foreman —</option>';
                foremanList.forEach(function(u) {
                    foremanOpts += `<option value="${u.id}">${u.username}</option>`;
                });
                $('#select_foreman').html(foremanOpts);

                // Isi dropdown Supervisor — dari data.user (jabatan supervisor)
                const supervisorList = data.user ?? [];
                let supervisorOpts = '<option value="">— Pilih Supervisor —</option>';
                supervisorList.forEach(function(u) {
                    supervisorOpts += `<option value="${u.id}">${u.username}</option>`;
                });
                $('#select_supervisor').html(supervisorOpts);
            }).fail(function() {
                $('#select_foreman').html('<option value="">Gagal memuat data</option>');
                $('#select_supervisor').html('<option value="">Gagal memuat data</option>');
                toastr.error('Gagal memuat daftar approver');
            });
        }

        $('#formGenset').submit(function(e) {
            e.preventDefault();

            let btn = $(this).find('button[type="submit"]');

            // 🔄 loading state
            btn.prop('disabled', true).text('Loading...');

            $.ajax({
                url: "{{ route('warming-up-genset.store') }}",
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

                    $('#formGenset')[0].reset();
                },

                error: function(xhr) {
                    let err = xhr.responseJSON;

                    let message = 'Terjadi kesalahan';

                    if (err?.errors) {
                        message = Object.values(err.errors).flat().join('<br>');
                    } else if (err?.message) {
                        message = err.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: message
                    });
                },

                complete: function() {
                    // 🔁 reset button
                    btn.prop('disabled', false).html(
                        '<i class="ri-send-plane-2-line me-1"></i> Submit');
                }
            });
        });
    })
</script>
@endsection