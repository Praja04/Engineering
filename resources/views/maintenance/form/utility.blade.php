@extends('layouts.app')

@section('title', ' Form Check Mtc Utility')

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
                    Form Check Maintenance Utility
                </div>

                <div class="card-body">

                    <form id="form-mtc-utility">
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
                            $coolingTower = [
                                'cleaning_saringan_cooling_tower' => 'Cleaning Saringan Bak Cooling Tower',
                                'cleaning_unit_cooling_tower' => 'Cleaning Unit Cooling Tower',
                                'cleaning_bak_cooling_tower' => 'Cleaning Bak Cooling Tower',
                            ];

                            $ro = [
                                'check_sensor_tank_farm_ro_produk' => 'Check Sensor Tank Farm RO Produk',
                                'cleaning_flow_rate_mmf_1' => 'Cleaning Flow Rate MMF #1',
                                'cleaning_flow_rate_mmf_2' => 'Cleaning Flow Rate MMF #2',
                                'cleaning_flow_rate_ro_produk' => 'Cleaning Flow Rate RO Produk',
                                'cleaning_flow_rate_ro_reject' => 'Cleaning Flow Rate RO Reject',
                                'penggantian_micron_filter_cip' => 'Penggantian Micron Filter CIP',
                                'penggantian_micron_filter_makeup_water' => 'Penggantian Micron Filter Make Up Water',
                                'cleaning_cip_tank' => 'Cleaning CIP Tank',
                                'cip_membrane_reverse_osmosis' => 'CIP Membrane Reverse Osmosis',
                                'check_fungsi_valve' => 'Check Fungsi Valve',
                                'cleaning_unit_ro_mesin' => 'Cleaning Unit RO Mesin',
                            ];

                            $compressor = [
                                'sirkulasi_phe_aq55vsd' => 'Sirkulasi PHE AQ55VSD',
                                'penggantian_air_ro_aq55vsd' => 'Penggantian Air RO AQ55VSD',
                                'cleaning_compressor_aq55vsd' => 'Cleaning Compressor AQ55VSD',
                                'cleaning_jalur_cooling_aq55vsd' => 'Cleaning Jalur Cooling AQ55VSD',
                                'cleaning_dryer_fd185' => 'Cleaning Dryer FD185',
                                'cleaning_compressor_ga37' => 'Cleaning Compressor GA37',
                                'cleaning_dryer_fd120' => 'Cleaning Dryer FD120',
                                'lubrikasi_motor_compressor_aq55vsd' => 'Lubrikasi Motor Compressor AQ55VSD',
                                'cleaning_compressor_sm55' => 'Cleaning Compressor SM55',
                            ];

                            $tankFarm = [
                                'cleaning_sensor_level_tank_farm' => 'Cleaning Sensor Level Tank Farm',
                                'cleaning_sensor_level_fresh_water_menara' =>
                                    'Cleaning Sensor Level Fresh Water Menara',
                                'cleaning_sensor_level_ro_reject_menara' => 'Cleaning Sensor Level RO Reject Menara',
                                'cleaning_sensor_level_intermediate' => 'Cleaning Sensor Level Intermediate',
                            ];

                            $boiler = [
                                'check_safety_valve' => 'Check Safety Valve',
                                'cleaning_level_gauge' => 'Cleaning Level Gauge',
                                'cleaning_level_transmitter' => 'Cleaning Level Transmitter',
                                'check_pressure_transmitter' => 'Check Pressure Transmitter',
                                'check_temperature_transmitter' => 'Check Temperature Transmitter',
                                'cleaning_sensor_o2_co2' => 'Cleaning Sensor O2 & CO2',
                                'check_chaingrate' => 'Check Chaingrate',
                                'check_ruang_bakar' => 'Check Ruang Bakar',
                                'check_back_chamber' => 'Check Back Chamber',
                                'check_guillotine' => 'Check Guillotine',
                                'check_wet_ash_conveyor' => 'Check Wet Ash Conveyor',
                                'check_bottom_ash_conveyor' => 'Check Bottom Ash Conveyor',
                                'check_conveyor_batu_bara' => 'Check Conveyor Batu Bara',
                                'check_feeder' => 'Check Feeder',
                                'cleaning_bak_wet_ash_conveyor' => 'Cleaning Bak Wet Ash Conveyor',
                                'check_feed_tank' => 'Check Feed Tank',
                            ];

                            $wwtp = [
                                'check_line_limbah' => 'Check Line Limbah',
                                'check_line_chemical' => 'Check Line Chemical',
                                'check_tangki_kotak' => 'Check Tangki Kotak',
                                'check_tangki_bulat' => 'Check Tangki Bulat',
                            ];
                        @endphp

                        <h6 class="fw-bold text-primary mb-3">Cooling Tower</h6>

                        @foreach (array_chunk($coolingTower, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">RO</h6>

                        @foreach (array_chunk($ro, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">Compressor</h6>

                        @foreach (array_chunk($compressor, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">Tank Farm</h6>

                        @foreach (array_chunk($tankFarm, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">Boiler</h6>

                        @foreach (array_chunk($boiler, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">WWTP</h6>

                        @foreach (array_chunk($wwtp, 2, true) as $row)
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
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <button type="button" id="btn-reset" class="btn btn-outline-danger">
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
            const STORAGE_KEY = 'form_mtc_utility_data';

            // Fungsi untuk menyimpan form ke localStorage
            function saveFormToLocalStorage() {
                const formData = {};
                $('#form-mtc-utility').serializeArray().forEach(item => {
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
            $('#form-mtc-utility').on('change input', 'input, select, textarea', function() {
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

            $('#form-mtc-utility').on('submit', function(e) {
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
                    url: "{{ route('mtc.utility.store') }}",
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
                        $('#form-mtc-utility')[0].reset();
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
