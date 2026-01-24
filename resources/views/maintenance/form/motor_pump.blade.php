@extends('layouts.app')

@section('title', ' Form Check Mtc Motor Pump')

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
                    Form Check Maintenance Motor Pump
                </div>

                <div class="card-body">

                    <form id="form-mtc-motorpump">
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

                        <h6 class="fw-bold text-primary mb-3">Motor</h6>

                        @php
                            $motors = [
                                'electrical_motor' => 'Kondisi Electrical',
                                'putaran_motor' => 'Putaran Motor',
                                'fibrasi_suara_motor' => 'Fibrasi dan Suara Motor',
                                'bearing_motor' => 'Bearing Motor',
                                'pelumasan_motor' => 'Pelumasan Motor',
                                'kebersihan_body_motor' => 'Kebersihan Body Motor',
                            ];

                            $pump = [
                                'putaran_pompa' => 'Putaran Pompa',
                                'shaft_karet_coupling_pompa' => 'Shaft & Karet Coupling',
                                'fan_belt_pompa' => 'Fan Belt',
                                'pressure_pompa' => 'Pressure Pompa',
                                'mechanical_seal_pompa' => 'Mechanical Seal',
                                'gasket_pompa' => 'Gasket Pompa',
                                'impeler' => 'Kondisi Impeller',
                                'kebersihan_unit_body_pompa' => 'Kebersihan Unit & Body Pompa',
                            ];

                            $aksesoris = [
                                'valve_aksesoris' => 'Valve',
                                'cek_valve_aksesoris' => 'Cek Valve',
                                'flow_meter_aksesoris' => 'Flow Meter',
                                'strainer_aksesoris' => 'Strainer / Saringan',
                                'alat_ukur_aksesoris' => 'Alat Ukur',
                                'kelengkapan_baut_mur_aksesoris' => 'Kelengkapan Baut & Mur',
                            ];

                            $gearbox = [
                                'tambah_ganti_oli_gearbox' => 'Penambahan / Penggantian Oli Gearbox',
                                'unit_area_gearbox' => 'Unit & Area Gearbox',
                                'oil_seal_gearbox' => 'Oil Seal Gearbox',
                                'filter_udara_gearbox' => 'Filter Udara Gearbox',
                                'bearing_gearbox' => 'Bearing Gearbox',
                            ];
                        @endphp

                        @foreach (array_chunk($motors, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">Pompa</h6>

                        @foreach (array_chunk($pump, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">Aksesoris</h6>

                        @foreach (array_chunk($aksesoris, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">Gearbox</h6>

                        @foreach (array_chunk($gearbox, 2, true) as $row)
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

                        {{-- CATATAN --}}
                        {{-- <div class="row mt-4">
                            <div class="col-md-6">
                                <label class="form-label">Catatan Umum</label>
                                <textarea class="form-control" name="keterangan" rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tindakan Korektif</label>
                                <textarea class="form-control" name="korektif" rows="3"></textarea>
                            </div>
                        </div> --}}

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
            const STORAGE_KEY = 'form_mtc_motorpump_data';

            // Fungsi untuk menyimpan form ke localStorage
            function saveFormToLocalStorage() {
                const formData = {};
                $('#form-mtc-motorpump').serializeArray().forEach(item => {
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
            $('#form-mtc-motorpump').on('change input', 'input, select, textarea', function() {
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

            $('#form-mtc-motorpump').on('submit', function(e) {
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
                    url: '/mtc/form/motor-pump/store',
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
                        $('#form-mtc-motorpump')[0].reset();
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
