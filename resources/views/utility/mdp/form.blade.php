@extends('layouts.app')

@section('title', 'Form Pemantauan MDP')

@section('styles')
<style>
    .flatpickr-input {
        background-color: inherit !important;
        cursor: pointer;
    }

    .flatpickr-time .flatpickr-am-pm {
        display: none !important;
    }

    .card-header-custom {
        background: linear-gradient(135deg, #1a1f36 0%, #2d3561 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 12px 12px 0 0;
    }

    .form-section-title {
        color: #3f51b5;
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        border-bottom: 2px solid #e0e0e0;
        padding-bottom: 0.5rem;
    }

    .form-section-title i {
        margin-right: 0.5rem;
        font-size: 1.2rem;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .btn-submit {
        background: linear-gradient(135deg, #3f51b5 0%, #5c6bc0 100%);
        border: none;
        padding: 0.8rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(63, 81, 181, 0.3);
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header-custom">
                        <h4 class="mb-1 text-white fw-bold">
                            <i class="ri-dashboard-3-line me-2"></i>
                            Form Pemantauan MDP
                        </h4>
                        <p class="mb-0 text-white-50">
                            Silakan lengkapi data pemantauan MDP harian di bawah ini.
                        </p>
                    </div>
                    <div class="card-body p-4">
                        <form id="formMdp">
                            @csrf
                            
                            {{-- Seksi Informasi Dasar --}}
                            <div class="form-section-title">
                                <i class="ri-information-line"></i> Informasi Laporan
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Laporan</label>
                                    <input type="date" name="tanggal_laporan" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jam Pencatatan</label>
                                    <input type="text" name="jam_pencatatan" id="jam_pencatatan" class="form-control" placeholder="HH:MM" readonly required>
                                </div>
                            </div>

                            {{-- Seksi Approval --}}
                            <div class="form-section-title">
                                <i class="ri-user-follow-line"></i> Penunjukan Approval
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Foreman</label>
                                    <select name="foreman_id" id="select_foreman" class="form-select" required>
                                        <option value="">-- Pilih Foreman --</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Supervisor</label>
                                    <select name="supervisor_id" id="select_supervisor" class="form-select" required>
                                        <option value="">-- Pilih Supervisor --</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Seksi Data Arus & Tegangan --}}
                            <div class="form-section-title">
                                <i class="ri-flashlight-line"></i> Data Arus & Tegangan
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">E-Del (kWh)</label>
                                    <input type="number" step="0.01" name="e_del" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Arus Rata-rata (A)</label>
                                    <input type="number" step="0.01" name="arus_rata_rata" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Tegangan Rata-rata (V)</label>
                                    <input type="number" step="0.01" name="tegangan_rata_rata" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Temperatur Trafo (°C)</label>
                                    <input type="number" step="0.01" name="temperatur_transformator" class="form-control" placeholder="0.00">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card bg-light shadow-none border">
                                        <div class="card-body">
                                            <h6 class="fw-bold mb-3 text-primary">Arus Per Fasa (A)</h6>
                                            <div class="mb-2">
                                                <label class="form-label small">Arus I1</label>
                                                <input type="number" step="0.01" name="arus_i1" class="form-control" placeholder="0.00">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small">Arus I2</label>
                                                <input type="number" step="0.01" name="arus_i2" class="form-control" placeholder="0.00">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label small">Arus I3</label>
                                                <input type="number" step="0.01" name="arus_i3" class="form-control" placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light shadow-none border">
                                        <div class="card-body">
                                            <h6 class="fw-bold mb-3 text-success">Tegangan Per Fasa (V)</h6>
                                            <div class="mb-2">
                                                <label class="form-label small">Tegangan V1</label>
                                                <input type="number" step="0.01" name="tegangan_v1" class="form-control" placeholder="0.00">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small">Tegangan V2</label>
                                                <input type="number" step="0.01" name="tegangan_v2" class="form-control" placeholder="0.00">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label small">Tegangan V3</label>
                                                <input type="number" step="0.01" name="tegangan_v3" class="form-control" placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light shadow-none border">
                                        <div class="card-body">
                                            <h6 class="fw-bold mb-3 text-warning">Daya Per Fasa (kW)</h6>
                                            <div class="mb-2">
                                                <label class="form-label small">Daya P1</label>
                                                <input type="number" step="0.01" name="daya_p1" class="form-control" placeholder="0.00">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small">Daya P2</label>
                                                <input type="number" step="0.01" name="daya_p2" class="form-control" placeholder="0.00">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label small">Daya P3</label>
                                                <input type="number" step="0.01" name="daya_p3" class="form-control" placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Seksi Tambahan --}}
                            <div class="form-section-title">
                                <i class="ri-more-2-line"></i> Informasi Tambahan
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Daya Total (kW)</label>
                                    <input type="number" step="0.01" name="daya_total" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Level Oil</label>
                                    <select name="level_oil" class="form-select">
                                        <option value="">-- Pilih Level Oil --</option>
                                        <option value="ok">OK</option>
                                        <option value="nok">NOT OK</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-end border-top pt-4">
                                <button type="reset" class="btn btn-light px-4 me-2">Reset</button>
                                <button type="submit" class="btn btn-primary btn-submit px-5" id="btnSubmit">
                                    <i class="ri-send-plane-fill me-1"></i> Submit Laporan
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
        // Initialize Flatpickr for Time
        flatpickr('#jam_pencatatan', {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true,
            minuteIncrement: 1,
            defaultDate: "{{ date('H:i') }}"
        });

        // Load Approvers
        loadApprovers();

        function loadApprovers() {
            $.get('/api/utility/users/approvers', function(res) {
                // Populate Foreman
                const foremanList = res.staff || [];
                let foremanHtml = '<option value="">-- Pilih Foreman --</option>';
                foremanList.forEach(user => {
                    foremanHtml += `<option value="${user.id}">${user.username}</option>`;
                });
                $('#select_foreman').html(foremanHtml);

                // Populate Supervisor
                const supervisorList = res.user || [];
                let supervisorHtml = '<option value="">-- Pilih Supervisor --</option>';
                supervisorList.forEach(user => {
                    supervisorHtml += `<option value="${user.id}">${user.username}</option>`;
                });
                $('#select_supervisor').html(supervisorHtml);
            }).fail(function() {
                toastr.error('Gagal memuat data approver.');
            });
        }

        // Form Submission
        $('#formMdp').on('submit', function(e) {
            e.preventDefault();
            
            const $btn = $('#btnSubmit');
            $btn.prop('disabled', true).html('<i class="ri-loader-4-line ri-spin me-1"></i> Memproses...');

            $.ajax({
                url: "{{ route('mdp-monitoring.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        showConfirmButton: true
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    const err = xhr.responseJSON;
                    let msg = 'Terjadi kesalahan saat menyimpan data.';
                    if (err && err.errors) {
                        msg = Object.values(err.errors).flat().join('<br>');
                    } else if (err && err.message) {
                        msg = err.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        html: msg
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="ri-send-plane-fill me-1"></i> Submit Laporan');
                }
            });
        });
    });
</script>
@endsection
