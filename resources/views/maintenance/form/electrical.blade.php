@extends('layouts.app')

@section('title', ' Form Check Mtc Electrical')

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
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    Form Check Maintenance Electrical
                </div>

                <div class="card-body">

                    <form id="form-mtc-electrical">
                        @csrf

                        {{-- INFORMASI UMUM --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Nama Mesin <span class="text-danger">*</span></label>
                                <select name="mesin_id" id="mesin_id" class="form-control" required>
                                    <option value="" disabled>Pilih mesin - lokasi</option>
                                    @foreach ($mesin as $item)
                                        <option value="{{ $item->id }}" data-lokasi="{{ $item->lokasi }}"
                                            data-departemen="{{ $item->dept }}">
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
                                <input type="text" class="form-control" name="lokasi" value="{{ old('lokasi') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Departemen </label>
                                <input type="text" class="form-control" name="departemen"
                                    value="{{ old('departemen') }}">
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
                            $panel = [
                                'check_kunci' => 'Check Kunci',
                                'check_koneksi_kabel' => 'Check Koneksi Kabel',
                                'check_wiring_panel' => 'Check Wiring Panel',
                                'check_lampu_indikator' => 'Check Lampu Indikator',
                                'check_name_plate' => 'Check Name Plate',
                                'check_unit_electrical' => 'Check Unit Electrical',
                                'check_grounding' => 'Check Grounding',
                                'check_kebersihan' => 'Check Kebersihan',
                                'check_bus_bar' => 'Check Bus Bar',
                                'check_nilai_grounding' => 'Check Nilai Grounding',
                            ];

                            $penerangan = [
                                'check_kondisi_lampu' => 'Check Kondisi Lampu',
                                'check_cover_lampu' => 'Check Cover Lampu',
                                'check_wiring_penerangan' => 'Check Wiring Penerangan',
                                'check_saklar' => 'Check Saklar',
                                'check_penyangga_penerangan' => 'Check Penyangga Penerangan',
                            ];

                            $sistemDistribusi = [
                                'check_stecker' => 'Check Stecker',
                                'check_stop_kontak' => 'Check Stop Kontak',
                                'check_terminal_listrik' => 'Check Terminal Listrik',
                                'check_pengabelan_distribusi' => 'Check Pengkabelan Distribusi',
                                'check_support_pelindung_distribusi' => 'Check Support & Pelindung Distribusi',
                            ];

                            $capacitorBank = [
                                'check_kondisi_fisik_capacitor' => 'Check Kondisi Fisik Capacitor',
                                'check_nilai_farad' => 'Check Nilai Farad',
                                'check_nilai_ampere' => 'Check Nilai Ampere',
                                'check_kebersihan_capacitor' => 'Check Kebersihan (Capacitor)',
                            ];

                            $trafo = [
                                'check_kebocoran_oli_sisi_bawah' => 'Check Kebocoran Oli Sisi Bawah',
                                'check_kebocoran_oli_sisi_atas' => 'Check Kebocoran Oli Sisi Atas',
                                'check_level_oli' => 'Check Level Oli',
                            ];
                        @endphp


                        <h6 class="fw-bold text-primary mb-3">Panel</h6>

                        @foreach (array_chunk($panel, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">Penerangan</h6>

                        @foreach (array_chunk($penerangan, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">Sistem Distribusi</h6>

                        @foreach (array_chunk($sistemDistribusi, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">Capacitor Bank</h6>

                        @foreach (array_chunk($capacitorBank, 2, true) as $row)
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

                        <h6 class="fw-bold text-primary mb-3">Trafo</h6>

                        @foreach (array_chunk($trafo, 2, true) as $row)
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

            let index = 0;
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
                        resetFormElectrical();
                    }
                });
            });

            function resetFormElectrical() {
                const $form = $('#form-mtc-electrical');

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

            let pendingFormData = null;
            let selectedStaff = null;
            let selectedUser = null;

            $('#form-mtc-electrical').on('submit', function(e) {
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

                    const $userDropdown = $('#userDropdown');
                    $userDropdown.empty().append('<option value="">Pilih user</option>');
                    res.user.forEach(u => {
                        $userDropdown.append(
                            `<option value="${u.id}">${u.username}</option>`
                        );
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
                    url: "{{ route('mtc.electrical.store') }}",
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
                            resetFormElectrical();
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
