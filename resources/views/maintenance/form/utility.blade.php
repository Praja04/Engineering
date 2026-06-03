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
            font-size: 11px !important;
        }

        /* Hilangkan background kotak besar */
        .flatpickr-time .numInputWrapper {
            background: transparent !important;
            border: none !important;
        }

        /* Hilangkan background saat aktif (yang biru itu) */
        .flatpickr-time .numInputWrapper input {
            background: transparent !important;
        }

        /* Override highlight biru */
        .flatpickr-time input.flatpickr-hour,
        .flatpickr-time input.flatpickr-minute {
            background: transparent !important;
            box-shadow: none !important;
        }

        /* Saat focus jangan jadi biru blok */
        .flatpickr-time input:focus {
            background: transparent !important;
            box-shadow: none !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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
                                {{-- <input type="text" class="form-control" name="nama_mesin" required> --}}
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" id="label_tanggal">
                                    Tanggal <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" name="tanggal" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"> Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="text" id="waktu_mulai" name="waktu_mulai" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"> Waktu Selesai </label>
                                <input type="text" id="waktu_selesai" name="waktu_selesai" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kode Mesin </label>
                                <input type="text" class="form-control" name="kode_mesin" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Lokasi </label>
                                <input type="text" class="form-control" name="lokasi" readonly>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Departemen </label>
                                <input type="text" class="form-control" name="departemen" readonly>
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

                        <h6 class="fw-bold text-primary mb-3">RO</h6>

                        @foreach (array_chunk($ro, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">Compressor</h6>

                        @foreach (array_chunk($compressor, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">Tank Farm</h6>

                        @foreach (array_chunk($tankFarm, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">Boiler</h6>

                        @foreach (array_chunk($boiler, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">WWTP</h6>

                        @foreach (array_chunk($wwtp, 2, true) as $row)
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
                                                <select name="materials[0][mid]"
                                                    class="form-control form-control-sm mid-select2"></select>
                                            </td>
                                            <td>
                                                <input type="text" name="materials[0][desc]"
                                                    class="form-control form-control-sm">
                                            </td>
                                            <td>
                                                <input type="number" name="materials[0][qty]"
                                                    class="form-control form-control-sm" min="1">
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
                            <option value="">-- username --</option>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            let index = 1;
            flatpickr("#waktu_mulai", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i", // 24 jam
                time_24hr: true // ⬅️ ini yang bikin 00–23
            });

            flatpickr("#waktu_selesai", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i", // 24 jam
                time_24hr: true // ⬅️ ini yang bikin 00–23
            });

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

            function initMidSelect2(element) {
                $(element).select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Cari MID / Nama Barang...',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: 'http://10.11.10.130:8087/api/wsp/barang',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function(response) {
                            return {
                                results: response.data.map(function(item) {
                                    return {
                                        id: item.mid_barang,
                                        text: item.mid_barang + ' - ' + item.nama_barang,
                                        nama_barang: item.nama_barang
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    templateResult: function(data) {
                        if (!data.id) return data.text;
                        return $(`
                            <div class="d-flex flex-column">
                                <span class="fw-bold" style="font-size: 12.5px;">${data.id}</span>
                                <small class="text-muted" style="font-size: 11px;">${data.nama_barang}</small>
                            </div>
                        `);
                    },
                    templateSelection: function(data) {
                        return data.id || data.text;
                    }
                }).on('select2:select', function(e) {
                    const data = e.params.data;
                    $(this).closest('tr').find('input[name*="[desc]"]').val(data.nama_barang);
                }).on('select2:clear select2:unselect', function(e) {
                    $(this).closest('tr').find('input[name*="[desc]"]').val('');
                    $(this).closest('tr').find('input[name*="[qty]"]').val('');
                });
            }

            // Initialize existing rows
            initMidSelect2('.mid-select2');

            // Kebutuhan Material
            $('#addRow').on('click', function() {
                let row = `
                    <tr>
                        <td>
                            <select name="materials[${index}][mid]" class="form-control form-control-sm mid-select2"></select>
                        </td>
                        <td>
                            <input type="text" name="materials[${index}][desc]" class="form-control form-control-sm">
                        </td>
                        <td>
                            <input type="number" name="materials[${index}][qty]" class="form-control form-control-sm" min="1">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger removeRow">×</button>
                        </td>
                    </tr>
                `;

                const $row = $(row);
                $('#materialTable tbody').append($row);
                initMidSelect2($row.find('.mid-select2'));
                index++;
            });

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
            });
            // End Kebutuhan Material

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

                return details.join(" | ");
            }

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
                        resetFormUtility();
                    }
                });
            });

            function resetFormUtility() {
                const $form = $('#form-mtc-utility');

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

            let pendingFormData = null;
            let selectedStaff = null;
            let selectedUser = null;

            $('#form-mtc-utility').on('submit', function(e) {
                e.preventDefault();
                pendingFormData = new FormData(this);

                $('#modalApprover').modal('show');

                // Load staff & user maintenance dari API
                $.get('/api/mtc/users/approvers', function(res) {
                    // Staff → langsung tampil semua username
                    const $staffDropdown = $('#staffDropdown');
                    $staffDropdown.empty().append('<option value="">Pilih staff</option>');
                    res.staff.forEach(u => {
                        $staffDropdown.append(
                            `<option value="${u.id}">${u.username}</option>`);
                    });

                    // User → pilih departemen dulu
                    const depts = [...new Set(res.user.map(u => u.departemen))];
                    const $userDept = $('#userDept');
                    $userDept.empty().append('<option value="">Pilih Departemen</option>');
                    depts.forEach(d => $userDept.append(`<option value="${d}">${d}</option>`));

                    // Saat dept dipilih → tampil & auto-set user
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

                        // Reset selectedUser saat ganti dept
                        selectedUser = null;
                    });
                });


            });

            $(document).on('change', '#staffDropdown', function() {
                selectedStaff = $(this).val();
            });

            $(document).on('change', '#userDropdown', function() {
                selectedUser = $(this).val();
            });
            // Klik tombol pilih
            $('#btnSelectApprover').on('click', function() {
                if (!selectedStaff || !selectedUser) {
                    Swal.fire('Pilih staff dan user maintenance terlebih dahulu');
                    return;
                }

                pendingFormData.append('staff_id', selectedStaff);
                pendingFormData.append('user_id', selectedUser);

                $('#modalApprover').modal('hide');
                $('#modalTtd').modal('show'); // lanjut modal TTD
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
                    url: "{{ route('mtc.utility.store') }}",
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
                            resetFormUtility();
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
