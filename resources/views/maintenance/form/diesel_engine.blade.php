@extends('layouts.app')

@section('title', ' Form Check Mtc Diesel Engine')

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
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    Form Check Maintenance Diesel Engine
                </div>

                <div class="card-body">

                    <form id="form-mtc-diesel-engine">
                        @csrf

                        {{-- INFORMASI UMUM --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Nama Mesin <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_mesin" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Paket</label>
                                <select class="form-select" name="paket">
                                    <option value="">-- Pilih --</option>
                                    <option>Z</option>
                                    <option>A</option>
                                    <option>B</option>
                                    <option>C</option>
                                    <option>D</option>
                                </select>
                            </div>
                        </div>

                        @php
                            $engine = [
                                'check_kondisi_level_oli_mesin' => 'Check Kondisi & Level Oli Mesin',
                                'check_kondisi_radiator_hose' => 'Check Kondisi Radiator & Hose',
                                'check_kondisi_level_air_radiator' => 'Check Kondisi & Level Air Radiator',
                                'check_water_pump' => 'Check Water Pump',
                                'check_injection_pump_injector_piping' => 'Check Injection Pump, Injector, Piping',
                                'check_turbocharger_manifold' => 'Check Turbocharger & Manifold',
                                'check_fan_v_belt' => 'Check Fan & V-Belt',
                                'check_automatic_tensioner_belt' => 'Check Automatic Tensioner Belt',
                                'check_engine_mounting' => 'Check Engine Mounting',
                                'check_air_filter_condition' => 'Check Air Filter Condition',
                                'check_clearence_valve_drain_valve' => 'Check Clearence Valve (Drain Valve)',
                                'check_engine_oil_filter' => 'Check Engine Oil Filter',
                                'check_air_radiator' => 'Check Air Radiator',
                                'check_minyak_kopling' => 'Check Minyak Kopling',
                                'check_fuel_filter' => 'Check Fuel Filter',
                            ];

                            $electric = [
                                'check_kondisi_aki_level_air_aki' => 'Check Kondisi Aki & Level Air Aki',
                                'check_fungsi_starting_motor' => 'Check Fungsi Starting Motor',
                                'check_fungsi_alternator' => 'Check Fungsi Alternator',
                                'check_sensor_sensor_gauge' => 'Check Sensor-sensor & Gauge',
                                'check_fuse_control_switch' => 'Check Fuse & Control Switch',
                                'check_control_display' => 'Check Control Display',
                                'check_indicator_wiring' => 'Check Indicator & Wiring',
                            ];

                            $transmisiBrakeDriveShaft = [
                                'check_kondisi_level_oli_transmisi' => 'Check Kondisi & Level Oli Transmisi',
                                'check_fungsi_transmisi' => 'Check Fungsi Transmisi',
                                'check_filter_oli_transmisi' => 'Check Filter Oli Transmisi',
                                'check_fungsi_rem' => 'Check Fungsi Rem',
                                'check_oli_tidak_ada_yang_bocor' => 'Check Oli Tidak Ada yang Bocor',
                                'check_kondisi_drive_shaft' => 'Check Kondisi Drive Shaft',
                            ];

                            $hydraulic = [
                                'check_kondisi_level_hydraulic_oil' => 'Check Kondisi & Level Hydraulic Oil',
                                'check_kondisi_hydraulic_oil_filter' => 'Check Kondisi Hydraulic Oil Filter',
                                'check_fungsi_hydraulic_system' => 'Check Fungsi Hydraulic System',
                                'check_fungsi_steering_system' => 'Check Fungsi Steering System',
                                'check_kondisi_hydraulic_cylinder' => 'Check Kondisi Hydraulic Cylinder',
                                'check_kondisi_steering_cylinder' => 'Check Kondisi Steering Cylinder',
                                'check_kondisi_axle_oil' => 'Check Kondisi Axle Oil',
                                'check_kondisi_baut_roda_hydraulic' => 'Check Kondisi Baut Roda',
                                'check_kondisi_bucket_pin_bucket' => 'Check Kondisi Bucket, Pin Bucket',
                                'check_kondisi_dump_pin_bushing' => 'Check Kondisi Dump, Pin & Bushing',
                            ];

                            $general = [
                                'check_klakson' => 'Check Klakson',
                                'check_buzzer_back' => 'Check Buzzer Back',
                                'check_kondisi_basket_fresh_body' => 'Check Kondisi Basket Fresh & Body',
                                'check_kaca_sepion' => 'Check Kaca Spion',
                                'check_kondisi_roda_ban' => 'Check Kondisi Roda/Ban',
                                'check_baut_roda_general' => 'Check Baut Roda',
                                'check_lampu_depan_kanan' => 'Check Lampu Depan Kanan',
                                'check_lampu_depan_kiri' => 'Check Lampu Depan Kiri',
                                'check_baut_bearing_molen' => 'Check Baut Bearing Molen',
                                'check_baut_hanger_as_roda' => 'Check Baut Hanger As Roda',
                            ];
                        @endphp


                        <h6 class="fw-bold text-primary mb-3">Engine</h6>

                        @foreach (array_chunk($engine, 2, true) as $row)
                            <div class="row g-3 mb-3">
                                @foreach ($row as $field => $label)
                                    <div class="col-md-6 col-12">
                                        <div class="card shadow-sm item-card p-3 item-row" data-field="{{ $field }}">

                                            <label class="form-label fw-semibold">
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

                                            <small class="status-label-default">Belum dicek</small>

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

                        <h6 class="fw-bold text-primary mb-3">Electric</h6>

                        @foreach (array_chunk($electric, 2, true) as $row)
                            <div class="row g-3 mb-3">
                                @foreach ($row as $field => $label)
                                    <div class="col-md-6 col-12">
                                        <div class="card shadow-sm item-card p-3 item-row" data-field="{{ $field }}">

                                            <label class="form-label fw-semibold">
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

                                            <small class="status-label-default">Belum dicek</small>

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

                        <h6 class="fw-bold text-primary mb-3">Transmisi Brake Drive shaft</h6>

                        @foreach (array_chunk($transmisiBrakeDriveShaft, 2, true) as $row)
                            <div class="row g-3 mb-3">
                                @foreach ($row as $field => $label)
                                    <div class="col-md-6 col-12">
                                        <div class="card shadow-sm item-card p-3 item-row"
                                            data-field="{{ $field }}">

                                            <label class="form-label fw-semibold">
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

                                            <small class="status-label-default">Belum dicek</small>

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


                        <h6 class="fw-bold text-primary mb-3">Hydraulic</h6>

                        @foreach (array_chunk($hydraulic, 2, true) as $row)
                            <div class="row g-3 mb-3">
                                @foreach ($row as $field => $label)
                                    <div class="col-md-6 col-12">
                                        <div class="card shadow-sm item-card p-3 item-row"
                                            data-field="{{ $field }}">

                                            <label class="form-label fw-semibold">
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

                                            <small class="status-label-default">Belum dicek</small>

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

                        <h6 class="fw-bold text-primary mb-3">General</h6>

                        @foreach (array_chunk($general, 2, true) as $row)
                            <div class="row g-3 mb-3">
                                @foreach ($row as $field => $label)
                                    <div class="col-md-6 col-12">
                                        <div class="card shadow-sm item-card p-3 item-row"
                                            data-field="{{ $field }}">

                                            <label class="form-label fw-semibold">
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

                                            <small class="status-label-default">Belum dicek</small>

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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const STORAGE_KEY = 'form_mtc_diesel_engine_data';

            // Fungsi untuk menyimpan form ke localStorage
            function saveFormToLocalStorage() {
                const formData = {};
                $('#form-mtc-diesel-engine').serializeArray().forEach(item => {
                    formData[item.name] = item.value;
                });

                // Simpan juga status radio yang tidak ter-serialize dengan serializeArray
                $('.status-radio:checked').each(function() {
                    formData[$(this).attr('name')] = $(this).val();
                });

                localStorage.setItem(STORAGE_KEY, JSON.stringify(formData));
            }

            // Fungsi untuk memuat data dari localStorage ke form
            function loadFormFromLocalStorage() {
                const savedData = localStorage.getItem(STORAGE_KEY);
                if (!savedData) return;

                const data = JSON.parse(savedData);

                // Isi semua input, textarea, select
                for (const [name, value] of Object.entries(data)) {
                    const $input = $(`[name="${name}"]`);

                    if ($input.is(':radio')) {
                        $(`input[name="${name}"][value="${value}"]`).prop('checked', true).trigger('change');
                    } else if ($input.is(':checkbox')) {
                        $input.prop('checked', value === 'on');
                    } else if ($input.is('select') || $input.is('input') || $input.is('textarea')) {
                        $input.val(value);
                    }
                }

                // Trigger change untuk radio agar UI (keterangan, warna, dll) ikut ter-update
                $('.status-radio:checked').trigger('change');
            }

            // Load data saat halaman dibuka
            loadFormFromLocalStorage();

            // Simpan setiap kali ada perubahan
            $('#form-mtc-diesel-engine').on('change input', 'input, select, textarea', function() {
                saveFormToLocalStorage();
            });

            $('.status-radio').on('change', function() {
                const $row = $(this).closest('.item-row');
                const isOk = $row.find('input[value="1"]').is(':checked');
                const isNg = $row.find('input[value="0"]').is(':checked');
                const $ket = $row.find('.keterangan-wrapper input');

                if (isOk || isNg) {
                    $row.find('.status-label-default').addClass('d-none');
                }

                if (isNg) {
                    $row.addClass('not-ok');
                    $row.find('.keterangan-wrapper').removeClass('d-none');
                    $ket.attr('required', true);
                } else {
                    $row.removeClass('not-ok');
                    $row.find('.keterangan-wrapper').addClass('d-none');
                    $ket.val('').removeClass('is-invalid').removeAttr('required');
                }

                saveFormToLocalStorage();
            });

            function collectNotOkDetails() {
                const details = [];

                $('.item-row').each(function() {
                    const $row = $(this);
                    const isNg = $row.find('input[value="0"]').is(':checked');
                    if (!isNg) return;

                    const label = $row.find('label.form-label').text().trim();
                    const keterangan = $row.find('input[name^="keterangan_"]').val().trim();

                    if (keterangan) {
                        details.push(`${label}: ${keterangan}`);
                    }
                });

                if (details.length === 0) return '';

                return details.join(", ");
            }

            $('#form-mtc-diesel-engine').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $btn = $('#btn-submit');
                $btn.prop('disabled', true);

                let valid = true;

                $('[data-required-when-not-ok]').each(function() {
                    const $input = $(this);
                    const $row = $input.closest('.item-row');
                    const isNg = $row.find('input[value="0"]').is(':checked');

                    if (isNg && !$input.val().trim()) {
                        $input.addClass('is-invalid');
                        valid = false;
                    }
                });

                if (!valid) {
                    $('html, body').animate({
                        scrollTop: $('.is-invalid').first().offset().top - 120
                    }, 300);
                    return;
                }

                const catatanUmum = $form.find('textarea[name="keterangan"]').val()?.trim() || '';

                const detailString = collectNotOkDetails();

                let finalKeterangan = catatanUmum;
                if (detailString) {
                    if (catatanUmum) {
                        finalKeterangan += "\n\n" + detailString;
                    } else {
                        finalKeterangan = detailString;
                    }
                }

                const formData = new FormData($form[0]);

                formData.set('keterangan', finalKeterangan);

                $.ajax({
                    url: "{{ route('mtc.diesel-engine.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false, // ← wajib! jangan proses data sebagai string
                    contentType: false,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message || 'Data berhasil disimpan',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                localStorage.removeItem(STORAGE_KEY);
                                $form[0].reset();
                                $('.keterangan-wrapper').addClass('d-none');
                                $('.status-label-default').removeClass('d-none');
                                $('.item-row').removeClass('not-ok');
                                $('.is-invalid').removeClass('is-invalid');
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message ||
                                'Terjadi kesalahan saat menyimpan data'
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
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
                        $('#form-mtc-diesel-engine')[0].reset();
                        $('.keterangan-wrapper').addClass('d-none');
                        $('.status-label-default').removeClass('d-none');
                        $('.item-row').removeClass('not-ok');
                        $('.is-invalid').removeClass('is-invalid');
                        localStorage.removeItem(STORAGE_KEY);
                    }
                });
            });
        });
    </script>
@endsection
