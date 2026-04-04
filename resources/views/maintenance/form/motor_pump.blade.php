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
                Form Check Maintenance Motor Pump
            </div>

            <div class="card-body">

                <form id="form-mtc-motorpump">
                    @csrf

                    {{-- INFORMASI UMUM --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Nama Mesin <span class="text-danger">*</span></label>
                            {{-- <input type="text" class="form-control" name="mesin_id" required> --}}
                            <select name="mesin_id" id="mesin_id" class="form-control" required>
                                @foreach ($mesin as $item)
                                <option value="{{ $item->id }}" data-lokasi="{{ $item->lokasi }}" data-departemen="{{ $item->dept }}">
                                    {{ $item->nama_mesin }} - {{ $item->lokasi }}
                                </option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Waktu <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="waktu" value="{{ old('waktu', now()->format('H:i')) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Lokasi </label>
                            <input type="text" class="form-control" name="lokasi"  required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Departemen </label>
                            <input type="text" class="form-control" name="departemen" required>
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
                    'kebersihan_unit_body_motor' => 'Kebersihan Unit Body Motor',
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
                                    <input type="radio" class="btn-check status-radio" name="{{ $field }}" value="1" id="{{ $field }}_ok">
                                    <label class="btn btn-outline-success" for="{{ $field }}_ok">OK</label>

                                    <input type="radio" class="btn-check status-radio" name="{{ $field }}" value="0" id="{{ $field }}_ng">
                                    <label class="btn btn-outline-danger" for="{{ $field }}_ng">Tidak
                                        OK</label>
                                </div>

                                <div class="keterangan-wrapper d-none mt-2">
                                    <input type="text" class="form-control form-control-sm" name="keterangan_{{ $field }}" placeholder="Wajib diisi jika Tidak OK" data-required-when-not-ok>
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
                                    <input type="radio" class="btn-check status-radio" name="{{ $field }}" value="1" id="{{ $field }}_ok">
                                    <label class="btn btn-outline-success" for="{{ $field }}_ok">OK</label>

                                    <input type="radio" class="btn-check status-radio" name="{{ $field }}" value="0" id="{{ $field }}_ng">
                                    <label class="btn btn-outline-danger" for="{{ $field }}_ng">Tidak
                                        OK</label>
                                </div>

                                <div class="keterangan-wrapper d-none mt-2">
                                    <input type="text" class="form-control form-control-sm" name="keterangan_{{ $field }}" placeholder="Wajib diisi jika Tidak OK" data-required-when-not-ok>
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
                            <div class="card shadow-sm item-card p-3 item-row" data-field="{{ $field }}">

                                <label class="form-label fw-semibold">
                                    {{ $label }}
                                </label>

                                <div class="btn-group btn-group-sm w-100">
                                    <input type="radio" class="btn-check status-radio" name="{{ $field }}" value="1" id="{{ $field }}_ok">
                                    <label class="btn btn-outline-success" for="{{ $field }}_ok">OK</label>

                                    <input type="radio" class="btn-check status-radio" name="{{ $field }}" value="0" id="{{ $field }}_ng">
                                    <label class="btn btn-outline-danger" for="{{ $field }}_ng">Tidak
                                        OK</label>
                                </div>

                                <div class="keterangan-wrapper d-none mt-2">
                                    <input type="text" class="form-control form-control-sm" name="keterangan_{{ $field }}" placeholder="Wajib diisi jika Tidak OK" data-required-when-not-ok>
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
                            <div class="card shadow-sm item-card p-3 item-row" data-field="{{ $field }}">

                                <label class="form-label fw-semibold">
                                    {{ $label }}
                                </label>

                                <div class="btn-group btn-group-sm w-100">
                                    <input type="radio" class="btn-check status-radio" name="{{ $field }}" value="1" id="{{ $field }}_ok">
                                    <label class="btn btn-outline-success" for="{{ $field }}_ok">OK</label>

                                    <input type="radio" class="btn-check status-radio" name="{{ $field }}" value="0" id="{{ $field }}_ng">
                                    <label class="btn btn-outline-danger" for="{{ $field }}_ng">Tidak
                                        OK</label>
                                </div>

                                <div class="keterangan-wrapper d-none mt-2">
                                    <input type="text" class="form-control form-control-sm" name="keterangan_{{ $field }}" placeholder="Wajib diisi jika Tidak OK" data-required-when-not-ok>
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
                                            <input type="number" name="materials[0][mid]" class="form-control form-control-sm" required>
                                        </td>
                                        <td>
                                            <input type="text" name="materials[0][desc]" class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <input type="number" name="materials[0][qty]" class="form-control form-control-sm" min="1" required>
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

<div class="modal fade" id="modalTtd" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tanda Tangan Teknisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <canvas id="signature-pad" style="border:1px solid #ccc; width:100%; height:200px;"></canvas>

                <div class="mt-2">
                    <button type="button" class="btn btn-outline-danger btn-sm" id="btnClearTtd">
                        Reset TTD
                    </button>
                </div>
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
                    <label for="staffDropdown" class="form-label">Staff</label>
                    <select class="form-select" id="staffDropdown">
                        <option value="">Pilih staff</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="userDropdown" class="form-label">User MT/MTC</label>
                    <select class="form-select" id="userDropdown">
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
        const STORAGE_KEY = 'form_mtc_motorpump_data';
        let index = 0;
        let isLoading = false;


        $('#mesin_id').on('change', function() {
            const selected = $(this).find(':selected');

            const lokasi = selected.data('lokasi') || '';
            const departemen = selected.data('departemen') || '';

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

        // Fungsi untuk menyimpan form ke localStorage
        function saveFormToLocalStorage() {
            let formData = {};
            let materials = [];

            // === FORM NORMAL ===
            $('#form-mtc-motorpump')
                .find('input, select, textarea')
                .not('[name^="materials"]')
                .each(function() {
                    const name = $(this).attr('name');
                    if (!name) return;

                    if ($(this).is(':radio')) {
                        if ($(this).is(':checked')) {
                            formData[name] = $(this).val();
                        }
                    } else if ($(this).is(':checkbox')) {
                        formData[name] = $(this).is(':checked');
                    } else {
                        formData[name] = $(this).val();
                    }
                });

            // === KEBUTUHAN MATERIAL ===
            $('#materialTable tbody tr').each(function() {
                const row = {
                    mid: $(this).find('input[name*="[mid]"]').val(),
                    desc: $(this).find('input[name*="[desc]"]').val(),
                    qty: $(this).find('input[name*="[qty]"]').val()
                };

                if (row.mid || row.desc || row.qty) {
                    materials.push(row);
                }
            });

            localStorage.setItem(STORAGE_KEY, JSON.stringify({
                form: formData,
                materials: materials
            }));
        }

        // Fungsi untuk memuat data dari localStorage ke form
        function loadFormFromLocalStorage() {
            const savedData = localStorage.getItem(STORAGE_KEY);
            if (!savedData) return;

            isLoading = true;

            const data = JSON.parse(savedData);

            // === FORM ===
            if (data.form) {
                for (const [name, value] of Object.entries(data.form)) {
                    const $input = $(`[name="${name}"]`);

                    if ($input.is(':radio')) {
                        $(`input[name="${name}"][value="${value}"]`)
                            .prop('checked', true)
                            .trigger('change');

                    } else if ($input.is(':checkbox')) {
                        $input.prop('checked', value);

                    } else {
                        $input.val(value).trigger('change');
                    }
                }
            }

            // === MATERIALS ===
            $('#materialTable tbody').empty();
            index = 1;

            if (data.materials && data.materials.length) {
                data.materials.forEach(item => {
                    let row = `
                            <tr>
                                <td>
                                    <input type="number" name="materials[${index}][mid]"
                                        class="form-control form-control-sm"
                                        value="${item.mid || ''}">
                                </td>
                                <td>
                                    <input type="text" name="materials[${index}][desc]"
                                        class="form-control form-control-sm"
                                        value="${item.desc || ''}">
                                </td>
                                <td>
                                    <input type="number" name="materials[${index}][qty]"
                                        class="form-control form-control-sm"
                                        value="${item.qty || ''}">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger removeRow">×</button>
                                </td>
                            </tr>
                        `;
                    $('#materialTable tbody').append(row);
                    index++;
                });
            }

            isLoading = false;
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

            return details.join(" | ");
        }

        // Kebutuhan Material
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

            saveFormToLocalStorage();
        });

        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            saveFormToLocalStorage();
        });
        // End Kebutuhan Material

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
            const $form = $('#form-mtc-motorpump');

            $form[0].reset();

            $form.find('select').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).val(null).trigger('change');
                }
            });

            // Reset UI checklist
            $('.keterangan-wrapper').addClass('d-none');
            $('.status-label-default').removeClass('d-none');
            $('.item-row').removeClass('not-ok');
            $('.is-invalid').removeClass('is-invalid');

            $('#materialTable tbody').empty();
            index = 1;

            localStorage.removeItem(STORAGE_KEY);
        }

        // Tanda Tangan
        let selectedStaff = null;
        let selectedUser = null;
        let signaturePad = null;
        let pendingFormData = null;

        $('#form-mtc-motorpump').on('submit', function(e) {
            e.preventDefault();
            pendingFormData = new FormData(this);

            $('#modalApprover').modal('show');

            // Load staff & user maintenance dari API
            $.get('/api/mtc/users/approvers', function(res) {
                const $staffDropdown = $('#staffDropdown');
                const $userDropdown = $('#userDropdown');

                $staffDropdown.empty().append(`<option value="">Pilih staff</option>`);
                res.staff.forEach(user => {
                    $staffDropdown.append(
                        `<option value="${user.id}">${user.username}</option>`);
                });

                $userDropdown.empty().append(`<option value="">Pilih user</option>`);
                res.user.forEach(user => {
                    $userDropdown.append(
                        `<option value="${user.id}">${user.username}</option>`);
                });
            });
        });

        // Pilih staff
        $('#staffDropdown').on('change', function() {
            selectedStaff = $(this).val();
        });

        // Pilih user maintenance
        $('#userDropdown').on('change', function() {
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

        // Inisialisasi signature pad
        $('#modalTtd').on('shown.bs.modal', function() {
            if (!signaturePad) {
                const canvas = document.getElementById('signature-pad');
                canvas.width = canvas.offsetWidth;
                canvas.height = 200;
                signaturePad = new SignaturePad(canvas);
            }
        });

        $('#btnClearTtd').on('click', function() {
            signaturePad.clear();
        });

        $('#modalTtd').on('hidden.bs.modal', function() {
            if (signaturePad) signaturePad.clear();
        });

        $('#btnSaveTtd').on('click', function() {

            if (!signaturePad || signaturePad.isEmpty()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'TTD belum diisi',
                    text: 'Silakan tanda tangan terlebih dahulu'
                });
                return;
            }

            const ttdBase64 = signaturePad.toDataURL('image/png');
            const keterangan = collectNotOkDetails();

            pendingFormData.append('ttd_base64', ttdBase64);
            if (keterangan) {
                pendingFormData.append('keterangan', keterangan);
            }
            pendingFormData.delete('_token');
            pendingFormData.append(
                '_token',
                $('meta[name="csrf-token"]').attr('content')
            );

            $('#modalTtd').modal('hide');

            submitFinalForm(pendingFormData);
        });

        function submitFinalForm(formData) {
            const $btn = $('#btn-submit');
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('mtc.motor-pump.store') }}",
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