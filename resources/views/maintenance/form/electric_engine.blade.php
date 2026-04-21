@extends('layouts.app')

@section('title', ' Form Check Mtc Electric Engine')

@section('styles')
    <style>
        .item-card.not-ok {
            background-color: rgba(220, 53, 69, 0.05);
        }

        .status-label-default {
            font-size: 0.8rem;
            color: #6c757d;
            font-style: italic;
        }

        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.15rem rgba(220, 53, 69, .25) !important;
        }

        .select2-selection__placeholder {
            font-size: 13px;
        }

        /* === DROPDOWN OPTION === */
        .select2-container--bootstrap-5 .select2-results__option {
            font-size: 11px !important;
            padding: 3px 8px !important;
            line-height: 1.3 !important;
        }

        /* TEXT YANG TAMPIL SAAT SUDAH DIPILIH */
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            font-size: 13px !important;
            line-height: 1.3 !important;
        }

        /* OPTION YANG AKTIF / HOVER */
        .select2-container--bootstrap-5 .select2-results__option--highlighted,
        .select2-container--bootstrap-5 .select2-results__option--selected {
            font-size: 11px !important;
        }

        /* TEXT UTAMA */
        .select2-container--bootstrap-5 .select2-results__option span {
            font-size: 12px !important;
        }

        /* TEXT <small> */
        .select2-container--bootstrap-5 .select2-results__option small {
            font-size: 10px !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    Form Check Maintenance Electric Engine
                </div>

                <div class="card-body">

                    <form id="form-mtc-electric-engine">
                        @csrf

                        {{-- INFORMASI UMUM --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Nama Mesin <span class="text-danger">*</span></label>
                                <select name="mesin_id" id="mesin_id" class="form-control" required>
                                    <option value="" disabled selected>
                                        Pilih mesin - lokasi
                                    </option>
                                    @foreach ($mesin as $item)
                                        <option value="{{ $item->id }}" data-lokasi="{{ $item->lokasi }}"
                                            data-departemen="{{ $item->dept }}" data-kode-mesin="{{ $item->kode_mesin }}">
                                            {{ $item->nama_mesin }} - {{ $item->lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" id="label_tanggal">
                                    Tanggal <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" name="tanggal" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Waktu Mulai<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="waktu_mulai" id="waktu_mulai"
                                    placeholder="Pilih waktu" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Waktu Selesai</label>
                                <input type="text" class="form-control" name="waktu_selesai" id="waktu_selesai"
                                    placeholder="Pilih waktu">

                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Lokasi </label>
                                <input type="text" class="form-control" name="lokasi" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kode Mesin </label>
                                <input type="text" class="form-control" name="kode_mesin" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Departemen </label>
                                <input type="text" class="form-control" name="departemen" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Runnning Hour </label>
                                <input type="numeric" class="form-control" name="running_hour"
                                    value="{{ old('running_hour') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Paket</label>
                                <select class="form-select" name="paket">
                                    <option value="">-- Pilih --</option>
                                    <option>Z</option>
                                    <option>A</option>
                                    <option>B</option>
                                    <option>C</option>
                                    <option>D</option>
                                    <option>Korektif</option>
                                    <option>Checkpoint</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-none" id="tanggal_selesai_wrapper">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="tanggal_selesai">
                            </div>
                        </div>

                        @php
                            $forkliftElectrical = [
                                'check_buzzer_back' => 'Check Buzzer Back',
                                'check_klakson' => 'Check Klakson',
                                'check_pilot_lamp' => 'Check Pilot Lamp',
                                'check_lampu_sorot' => 'Check Lampu Sorot',
                                'check_lampu_kombinasi_kanan_belakang' => 'Check Lampu Kombinasi Kanan Belakang',
                                'check_lampu_kombinasi_kiri_belakang' => 'Check Lampu Kombinasi Kiri Belakang',
                                'check_kaca_sepion' => 'Check Kaca Spion',
                            ];

                            $batteryChargerElectrical = [
                                'check_battery' => 'Check Battery',
                                'check_skun_battery' => 'Check Skun Battery',
                                'check_terminal_charger_battery' => 'Check Terminal Charger Battery',
                                'check_kunci_kontak' => 'Check Kunci Kontak',
                                'check_main_contactor' => 'Check Main Contactor',
                                'check_microswitch' => 'Check Microswitch',
                                'check_eps_controller' => 'Check EPS Controller',
                                'check_steering_motor' => 'Check Steering Motor (Brush & Commutator)',
                                'check_fan' => 'Check Fan',
                                'check_fuse' => 'Check Fuse',
                                'check_display_control' => 'Check Display Control',
                                'check_wiring_terminal' => 'Check Wiring & Terminal',
                                'check_carbon_brush' => 'Check Carbon Brush',
                            ];

                            $driveSteeringHydraulicBraking = [
                                'check_steering_wheel' => 'Check Steering Wheel',
                                'check_baut_roda' => 'Check Baut Roda',
                                'check_drive_caster_load_wheel' => 'Check Drive, Caster & Load Wheel',
                                'check_lift_chain' => 'Check Lift Chain',
                                'check_lift_bracket' => 'Check Lift Bracket',
                                'check_hydraulic_hose' => 'Check Hydraulic Hose',
                                'check_motor_hydraulic_pump' => 'Check Motor Hydraulic Pump',
                                'check_fork' => 'Check Fork',
                                'check_lift_rollers' => 'Check Lift Rollers',
                                'check_mast_rollers' => 'Check Mast Rollers',
                                'check_lift_cylinders' => 'Check Lift Cylinders',
                                'check_tilt_cylinders' => 'Check Tilt Cylinders',
                                'check_control_valve' => 'Check Control Valve',
                                'check_hydraulic_tank' => 'Check Hydraulic Tank',
                                'check_overhead_guard' => 'Check Overhead Guard (Pelindung Kemudi)',
                                'check_all_bolt_nut' => 'Check All Bolt & Nut',
                                'check_power_steering' => 'Check Power Steering',
                                'check_brake_cam_adjust_bolt' => 'Check Brake Cam & Adjust Bolt',
                                'check_axle' => 'Check Axle',
                                'check_greasing_point' => 'Check Greasing Point',
                                'check_air_spring' => 'Check Air Spring',
                            ];

                            $oil = [
                                'ganti_gear_oil' => 'Ganti Gear Oil',
                                'ganti_hydraulic_oil' => 'Ganti Hydraulic Oil',
                                'ganti_return_filter' => 'Ganti Return Filter',
                                'ganti_brake_oil' => 'Ganti Brake Oil',
                            ];
                        @endphp

                        <h6 class="fw-bold text-primary mb-3">Forklift Electrical</h6>

                        @foreach (array_chunk($forkliftElectrical, 2, true) as $row)
                            <div class="row g-3 mb-3">
                                @foreach ($row as $field => $label)
                                    <div class="col-md-6 col-12">
                                        <div class="card shadow-sm item-card p-3 item-row" data-field="{{ $field }}">

                                            <label class="form-label fw-semibold" data-label="{{ $field }}">
                                                {{ $label }}
                                            </label>

                                            <div class="btn-group btn-group-sm w-100">
                                                <input type="radio" class="btn-check status-radio"
                                                    name="{{ $field }}" value="1" id="{{ $field }}_ok">
                                                <label class="btn btn-outline-success"
                                                    for="{{ $field }}_ok">OK</label>

                                                <input type="radio" class="btn-check status-radio"
                                                    name="{{ $field }}" value="0" id="{{ $field }}_ng">
                                                <label class="btn btn-outline-danger" for="{{ $field }}_ng">Tidak
                                                    OK</label>
                                            </div>

                                            <div class="keterangan-wrapper d-none mt-2">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="keterangan_{{ $field }}"
                                                    placeholder="Wajib diisi jika Tidak OK" data-required-when-not-ok>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <h6 class="fw-bold text-primary mb-3">Battery, Charger, & Electrical System</h6>

                        @foreach (array_chunk($batteryChargerElectrical, 2, true) as $row)
                            <div class="row g-3 mb-3">
                                @foreach ($row as $field => $label)
                                    <div class="col-md-6 col-12">
                                        <div class="card shadow-sm item-card p-3 item-row"
                                            data-field="{{ $field }}">

                                            <label class="form-label fw-semibold" data-label="{{ $field }}">
                                                {{ $label }}
                                            </label>

                                            <div class="btn-group btn-group-sm w-100">
                                                <input type="radio" class="btn-check status-radio"
                                                    name="{{ $field }}" value="1"
                                                    id="{{ $field }}_ok">
                                                <label class="btn btn-outline-success"
                                                    for="{{ $field }}_ok">OK</label>

                                                <input type="radio" class="btn-check status-radio"
                                                    name="{{ $field }}" value="0"
                                                    id="{{ $field }}_ng">
                                                <label class="btn btn-outline-danger" for="{{ $field }}_ng">Tidak
                                                    OK</label>
                                            </div>

                                            <div class="keterangan-wrapper d-none mt-2">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="keterangan_{{ $field }}"
                                                    placeholder="Wajib diisi jika Tidak OK" data-required-when-not-ok>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <h6 class="fw-bold text-primary mb-3">Drive, Steering, Mast, Hydraulic & Braking System</h6>

                        @foreach (array_chunk($driveSteeringHydraulicBraking, 2, true) as $row)
                            <div class="row g-3 mb-3">
                                @foreach ($row as $field => $label)
                                    <div class="col-md-6 col-12">
                                        <div class="card shadow-sm item-card p-3 item-row"
                                            data-field="{{ $field }}">

                                            <label class="form-label fw-semibold" data-label="{{ $field }}">
                                                {{ $label }}
                                            </label>

                                            <div class="btn-group btn-group-sm w-100">
                                                <input type="radio" class="btn-check status-radio"
                                                    name="{{ $field }}" value="1"
                                                    id="{{ $field }}_ok">
                                                <label class="btn btn-outline-success"
                                                    for="{{ $field }}_ok">OK</label>

                                                <input type="radio" class="btn-check status-radio"
                                                    name="{{ $field }}" value="0"
                                                    id="{{ $field }}_ng">
                                                <label class="btn btn-outline-danger" for="{{ $field }}_ng">Tidak
                                                    OK</label>
                                            </div>

                                            <div class="keterangan-wrapper d-none mt-2">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="keterangan_{{ $field }}"
                                                    placeholder="Wajib diisi jika Tidak OK" data-required-when-not-ok>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach


                        <h6 class="fw-bold text-primary mb-3">Oil</h6>

                        @foreach (array_chunk($oil, 2, true) as $row)
                            <div class="row g-3 mb-3">
                                @foreach ($row as $field => $label)
                                    <div class="col-md-6 col-12">
                                        <div class="card shadow-sm item-card p-3 item-row"
                                            data-field="{{ $field }}">

                                            <label class="form-label fw-semibold" data-label="{{ $field }}">
                                                {{ $label }}
                                            </label>

                                            <div class="btn-group btn-group-sm w-100">
                                                <input type="radio" class="btn-check status-radio"
                                                    name="{{ $field }}" value="1"
                                                    id="{{ $field }}_ok">
                                                <label class="btn btn-outline-success"
                                                    for="{{ $field }}_ok">OK</label>

                                                <input type="radio" class="btn-check status-radio"
                                                    name="{{ $field }}" value="0"
                                                    id="{{ $field }}_ng">
                                                <label class="btn btn-outline-danger" for="{{ $field }}_ng">Tidak
                                                    OK</label>
                                            </div>

                                            <div class="keterangan-wrapper d-none mt-2">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="keterangan_{{ $field }}"
                                                    placeholder="Wajib diisi jika Tidak OK" data-required-when-not-ok>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        {{-- Korektif --}}
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <label class="form-label">Tindakan Korektif</label>
                                <textarea class="form-control" name="korektif" rows="3"></textarea>
                            </div>
                        </div>

                        {{-- Kebutuhan Material --}}
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <label class="form-label">Kebutuhan Material</label>
                                <table class="table table-bordered" id="materialTable">
                                    <thead class="table-light text-no-wrap">
                                        <tr>
                                            <th style="width: 20%">MID</th>
                                            <th>Deskripsi</th>
                                            <th style="width: 15%">Jumlah</th>
                                            <th class="text-center" style="width: 10%">
                                                <button type="button" class="btn btn-sm btn-primary" id="addRow">
                                                    +
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="number" name="materials[0][mid]"
                                                    class="form-control form-control-sm" required>
                                            </td>
                                            <td>
                                                <input type="text" name="materials[0][desc]"
                                                    class="form-control form-control-sm">
                                            </td>
                                            <td>
                                                <input type="number" name="materials[0][qty]"
                                                    class="form-control form-control-sm" min="1" required>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger removeRow">
                                                    ×
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>

                        {{-- BUTTON --}}
                        <div class="text-end mt-4">
                            <button type="button" id="btn-reset" class="btn btn-outline-danger me-2">
                                Reset
                            </button>
                            <button type="submit" id="btn-submit" class="btn btn-primary">
                                Simpan
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal TTD --}}
    <div class="modal fade" id="modalTtd" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tanda Tangan Teknisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ asset('storage/mtc/ttd/ttd_teknisi.jpeg') }}"
                        style="max-width: 100%; border: 1px solid #ccc;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnSaveTtd">
                        Simpan & Kirim
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Pilih Approver --}}
    <div class="modal fade" id="modalApprover" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Approver</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Staff Engineering</label>
                        <select class="form-select" id="staffDropdown">
                            <option value="">Pilih staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User MT/MTC</label>
                        <select class="form-select" id="userDept">
                            <option value="">Pilih Departemen</option>
                        </select>
                        <select class="form-select mt-2 d-none" id="userDropdown">
                            <option value="">Pilih user</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSelectApprover">Lanjut</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            flatpickr("#waktu_mulai", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 1,
            });

            flatpickr("#waktu_selesai", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 1,
            });
            let index = 0;
            $('select[name="paket"]').on('change', function() {
                const val = $(this).val();

                if (val === 'Korektif') {
                    $('#tanggal_selesai_wrapper').removeClass('d-none');
                    $('input[name="tanggal_selesai"]').attr('required', true);

                    // 🔥 Ubah label
                    $('#label_tanggal').html('Tanggal Mulai <span class="text-danger">*</span>');

                } else {
                    $('#tanggal_selesai_wrapper').addClass('d-none');
                    $('input[name="tanggal_selesai"]')
                        .val('')
                        .removeAttr('required');

                    // 🔥 Balikin ke default
                    $('#label_tanggal').html('Tanggal <span class="text-danger">*</span>');
                }
            });

            $('#mesin_id').on('change', function() {
                const selected = $(this).find(':selected');

                const lokasi = selected.data('lokasi') || '';
                const departemen = selected.data('departemen') || '';
                const kodeMesin = selected.data('kode-mesin') || '';
                $('input[name="kode_mesin"]').val(kodeMesin);
                $('input[name="lokasi"]').val(lokasi);
                $('input[name="departemen"]').val(departemen);

            });

            $('#mesin_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Cari nama mesin / lokasi...',
                allowClear: true,
                width: '100%',
                templateResult: function(data) {
                    if (!data.id) return data.text;
                    return $('<span><b>' + data.text.split(' - ')[0] + '</b><br><small>' + data.text
                        .split(' - ')[1] + '</small></span>');
                }
            });


            $('.status-radio').on('change', function() {
                const $row = $(this).closest('.item-row');
                // const isOk = $row.find('input[value="1"]').is(':checked');
                const isNg = $row.find('input[value="0"]').is(':checked');
                const $ket = $row.find('.keterangan-wrapper input');

                // if (isOk || isNg) {
                //     $row.find('.status-label-default').addClass('d-none');
                // }

                if (isNg) {
                    $row.addClass('not-ok');
                    $row.find('.keterangan-wrapper').removeClass('d-none');
                    $ket.attr('required', true);
                } else {
                    $row.removeClass('not-ok');
                    $row.find('.keterangan-wrapper').addClass('d-none');
                    $ket.val('').removeClass('is-invalid').removeAttr('required');
                }


            });

            function collectNotOkDetails() {
                const details = [];

                $('.item-row').each(function() {
                    const $row = $(this);
                    const isNg = $row.find('input[value="0"]').is(':checked');
                    if (!isNg) return;

                    const label = $row.find('label.form-label').data('label');
                    const keterangan = $row.find('input[name^="keterangan_"]').val().trim();

                    if (keterangan) {
                        details.push(`${label}: ${keterangan}`);
                    }
                });

                if (details.length === 0) return '';

                return details.join(", ");
            }

            $('#addRow').on('click', function() {
                let row = `
                    <tr>
                        <td>
                            <input type="number" name="materials[${index}][mid]" class="form-control form-control-sm" required>
                        </td>
                        <td>
                            <input type="text" name="materials[${index}][desc]" class="form-control form-control-sm">
                        </td>
                        <td>
                            <input type="number" name="materials[${index}][qty]" class="form-control form-control-sm" min="1" required>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger removeRow">×</button>
                        </td>
                    </tr>
                `;

                $('#materialTable tbody').append(row);
                index++;


            });

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();

            });

            $('#btn-reset').on('click', function() {
                Swal.fire({
                    title: 'Reset Form?',
                    text: 'Semua isian akan dikosongkan',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, reset',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        resetFormMotorPump();
                    }
                });
            });

            function resetFormMotorPump() {
                const $form = $('#form-mtc-electric-engine');

                $form[0].reset();

                $form.find('select').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).val(null).trigger('change');
                    }
                });
                $('#tanggal_selesai_wrapper').addClass('d-none');
                $('input[name="tanggal_selesai"]').val('').removeAttr('required');
                // Reset UI checklist
                $('.keterangan-wrapper').addClass('d-none');
                $('.status-label-default').removeClass('d-none');
                $('.item-row').removeClass('not-ok');
                $('.is-invalid').removeClass('is-invalid');

                $('#materialTable tbody').empty();
                index = 1;


            }

            // Tanda Tangan

            let pendingFormData = null;
            let selectedStaff = null;
            let selectedUser = null;

            $('#form-mtc-electric-engine').on('submit', function(e) {
                e.preventDefault();
                pendingFormData = new FormData(this);

                $('#modalApprover').modal('show');

                $.get('/api/mtc/users/approvers', function(res) {
                    const $staffDropdown = $('#staffDropdown');
                    $staffDropdown.empty().append('<option value="">Pilih staff</option>');
                    res.staff.forEach(u => {
                        $staffDropdown.append(
                            `<option value="${u.id}">${u.username}</option>`);
                    });

                    const depts = [...new Set(res.user.map(u => u.departemen))];
                    const $userDept = $('#userDept');
                    $userDept.empty().append('<option value="">Pilih Departemen</option>');
                    depts.forEach(d => $userDept.append(`<option value="${d}">${d}</option>`));

                    $('#userDept').off('change').on('change', function() {
                        const dept = $(this).val();
                        const filtered = res.user.filter(u => u.departemen === dept);
                        const $userDropdown = $('#userDropdown');
                        $userDropdown.empty().append(
                            '<option value="">Pilih user</option>');
                        filtered.forEach(u => {
                            $userDropdown.append(
                                `<option value="${u.id}">${u.username}</option>`
                            );
                        });
                        $userDropdown.removeClass('d-none');
                        selectedUser = null;
                    });
                });
            });

            $(document).on('change', '#staffDropdown', function() {
                selectedStaff = $(this).val() || null;
            });

            $(document).on('change', '#userDropdown', function() {
                selectedUser = $(this).val() || null;
            });

            $('#btnSelectApprover').on('click', function() {
                if (!selectedStaff || !selectedUser) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih approver',
                        text: 'Pilih staff dan user MT/MTC terlebih dahulu'
                    });
                    return;
                }

                pendingFormData.append('staff_id', selectedStaff);
                pendingFormData.append('user_id', selectedUser);

                $('#modalApprover').modal('hide');
                $('#modalTtd').modal('show');
            });

            $('#btnSaveTtd').on('click', function() {
                const keterangan = collectNotOkDetails();

                pendingFormData.append('ttd_path', 'mtc/ttd/ttd_teknisi.jpeg');
                if (keterangan) {
                    pendingFormData.append('keterangan', keterangan);
                }
                pendingFormData.delete('_token');
                pendingFormData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                $('#modalTtd').modal('hide');
                submitFinalForm(pendingFormData);
            });

            function submitFinalForm(formData) {
                const $btn = $('#btn-submit');
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('mtc.electric-engine.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            resetFormMotorPump();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            }
        });
    </script>
@endsection
