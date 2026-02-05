@extends('layouts.app')

@section('title', ' Form Check Mtc Electric P2H')

@section('styles')
    <style>
        .item-card.not-ok {
            background-color: rgba(220, 53, 69, 0.05);
            border-color: #dc3545;
        }

        .kondisi-btn {
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }

        .kondisi-btn.active {
            color: #fff !important;
        }

        .kondisi-btn.active.btn-outline-success {
            background: #198754;
            border-color: #198754;
        }

        .kondisi-btn.active.btn-outline-danger {
            background: #dc3545;
            border-color: #dc3545;
        }

        /* Mobile friendly */
        @media (max-width: 576px) {
            .kondisi-btn {
                padding: 0.75rem 0;
                font-size: 1.1rem;
            }

            .card-header small {
                font-size: 0.85rem;
            }
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

        .keterangan-wrapper {
            height: 0;
            overflow: hidden;
            transition: height 0.3s ease;
            margin: 0 !important;
            padding: 0 !important;
        }

        .item-card.not-ok .keterangan-wrapper {
            height: auto;
            margin: 0.5rem 0 !important;
            padding: 0 !important;
        }

        .kondisi-radio:checked+.kondisi-btn {
            color: #fff;
        }

        .kondisi-radio[value="1"]:checked+.kondisi-btn {
            background-color: #198754;
            border-color: #198754;
        }

        .kondisi-radio[value="0"]:checked+.kondisi-btn {
            background-color: #dc3545;
            border-color: #dc3545;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    Form Check Maintenance Electric P2H
                </div>

                <div class="card-body">

                    <form id="form-mtc-electric-p2h" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal
                                    <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control"
                                    value="{{ old('tanggal', now()->toDateString()) }}">
                                @error('tanggal')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Waktu <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="waktu"
                                    value="{{ old('waktu', now()->format('H:i')) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Departemen <span class="text-danger">*</span></label>
                                <input type="text" name="departemen" class="form-control" value="{{ old('departemen') }}"
                                    placeholder="Warehouse">
                                @error('departemen')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">No Unit <span class="text-danger">*</span></label>
                                <input type="text" name="no_unit" class="form-control" value="{{ old('no_unit') }}"
                                    placeholder="F01">
                                @error('no_unit')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Shift <span class="text-danger">*</span></label>
                                <select name="shift" class="form-select">
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ old('shift') == 1 ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ old('shift') == 2 ? 'selected' : '' }}>Shift 2</option>
                                    <option value="3" {{ old('shift') == 3 ? 'selected' : '' }}>Shift 3</option>
                                </select>
                                @error('shift')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Hours Meter (Jam Operasional)<span
                                        class="text-danger">*</span></label>
                                <input type="numeric" name="hours_meter" class="form-control"
                                    value="{{ old('hours_meter') }}" placeholder="12345">
                                <small class="form-label fst-italic">Catat sesuai kondisi aktual di unit</small>
                                @error('hours_meter')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- error global dari withValidator (checklist) --}}
                        @error('checklist')
                            <div class="alert alert-danger py-2">{{ $message }}</div>
                        @enderror

                        @php
                            $electricP2h = [
                                'level_minyak_rem' => [
                                    'label' => 'Check Level Minyak Rem',
                                    'standar' => 'Berada di level max',
                                ],
                                'level_oli_hydraulic' => [
                                    'label' => 'Check Level Oli Hydraulic',
                                    'standar' => 'Berada di level max',
                                ],
                                'isi_air_aki' => [
                                    'label' => 'Check Isi Air Aki',
                                    'standar' => 'Berada di level standar',
                                ],
                                'baterai' => [
                                    'label' => 'Check Baterai',
                                    'standar' => 'Tidak kurang dari 30%',
                                ],
                                'hydraulic_system' => [
                                    'label' => 'Hydraulic System',
                                    'standar' => 'Berfungsi dengan baik dan terlubrikasi',
                                ],
                                'selang_hydraulic' => [
                                    'label' => 'Selang Hydraulic',
                                    'standar' => 'Tidak ada kebocoran oli',
                                ],
                                'lift_chains' => [
                                    'label' => 'Lift Chains',
                                    'standar' => 'Kekencangan kanan dan kiri sama serta terlubrikasi',
                                ],
                                'fork' => [
                                    'label' => 'Pengecekan Fork',
                                    'standar' => 'Tidak bengkok dan tidak patah',
                                ],
                                'body_unit' => [
                                    'label' => 'Check Body Unit',
                                    'standar' => 'Tidak lecet dan tidak penyok',
                                ],
                                'lampu_kombinasi_kiri' => [
                                    'label' => 'Check Lampu Kombinasi Kiri',
                                    'standar' => 'Menyala normal dan tidak pecah',
                                ],
                                'lampu_kombinasi_kanan' => [
                                    'label' => 'Check Lampu Kombinasi Kanan',
                                    'standar' => 'Menyala normal dan tidak pecah',
                                ],
                                'lampu_sorot' => [
                                    'label' => 'Check Lampu Sorot / Head Lamp',
                                    'standar' => 'Menyala normal dan tidak pecah',
                                ],
                                'lampu_sign_depan_kanan' => [
                                    'label' => 'Check Lampu Sign Depan Kanan',
                                    'standar' => 'Menyala normal dan tidak pecah',
                                ],
                                'lampu_sign_depan_kiri' => [
                                    'label' => 'Check Lampu Sign Depan Kiri',
                                    'standar' => 'Menyala normal dan tidak pecah',
                                ],
                                'klakson' => [
                                    'label' => 'Check Klakson / Horn',
                                    'standar' => 'Bunyi saat tombol ditekan',
                                ],
                                'buzzer_back' => [
                                    'label' => 'Check Buzzer Back',
                                    'standar' => 'Berbunyi normal saat maju dan mundur',
                                ],
                                'kaca_spion' => [
                                    'label' => 'Check Kaca Spion',
                                    'standar' => 'Terpasang dengan baik dan tidak pecah',
                                ],
                                'baut_roda' => [
                                    'label' => 'Check Kekencangan Baut Roda',
                                    'standar' => 'Kencang dan tidak patah',
                                ],
                                'ban' => [
                                    'label' => 'Check Ban',
                                    'standar' => 'Masih bagus dan layak pakai',
                                ],
                                'kebersihan_unit' => [
                                    'label' => 'Check Kebersihan Unit',
                                    'standar' => 'Bersih dari kotoran dan debu',
                                ],
                                'panel_display' => [
                                    'label' => 'Check Panel Display',
                                    'standar' => 'Berfungsi normal, tidak pecah, dan tidak ada alarm',
                                ],
                                'sistem_kemudi' => [
                                    'label' => 'Sistem Kemudi',
                                    'standar' => 'Tidak berat dan bergerak lancar',
                                ],
                            ];
                        @endphp

                        <h6 class="fw-bold text-primary mb-3 mt-4">Checklist Mtc Electric P2h</h6>

                        @foreach (array_chunk($electricP2h, 2, true) as $row)
                            <div class="row g-3 mb-3">
                                @foreach ($row as $field => $item)
                                    <div class="col-md-6 col-12">
                                        <div class="card shadow-sm item-card p-3 item-row"
                                            data-field="{{ $field }}">

                                            {{-- Judul --}}
                                            <label class="form-label fw-semibold mb-1">
                                                {{ $item['label'] }}
                                            </label>

                                            {{-- Standar pemeliharaan --}}
                                            <div class="text-muted small mb-2">
                                                {{ $item['standar'] }}
                                            </div>

                                            {{-- Radio --}}
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

                                            {{-- Keterangan --}}
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


                        <div class="row mt-4 mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Catatan</label>
                                <textarea class="form-control" name="catatan" rows="3"></textarea>
                            </div>
                        </div>

                        <div id="clientError" class="alert alert-danger d-none py-2"></div>

                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" id="btnResetKondisi" class="btn btn-outline-danger">Reset</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const STORAGE_KEY = 'form_mtc_electric_p2h_data';
            let isLoading = false;
            let signaturePad = null;
            let pendingFormData = null;

            function saveFormLocalStorage() {
                const data = {};

                $('#form-mtc-electric-p2h')
                    .find('input, select, textarea')
                    .not('[name^="materials"]')
                    .each(function() {
                        const name = $(this).attr('name');
                        if (!name) return;

                        if ($(this).is(':radio')) {
                            if ($(this).is(':checked')) {
                                data[name] = $(this).val();
                            }
                        } else if ($(this).is(':checkbox')) {
                            data[name] = $(this).is(':checked');
                        } else {
                            data[name] = $(this).val();
                        }
                    });

                localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
            }

            function loadFormLocalStorage() {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (!saved) return;

                isLoading = true;

                const data = JSON.parse(saved);

                for (const [name, value] of Object.entries(data)) {
                    const $inputs = $(`[name="${name}"]`);

                    if (!$inputs.length) continue;

                    if ($inputs.first().is(':radio')) {
                        $inputs.filter(`[value="${value}"]`)
                            .prop('checked', true)
                            .trigger('change');

                    } else if ($inputs.first().is(':checkbox')) {
                        $inputs.prop('checked', value);

                    } else {
                        $inputs.val(value).trigger('input');
                    }
                }

                $('.item-row').each(function() {
                    updateStatusRow($(this));
                });

                isLoading = false;
            }

            loadFormLocalStorage();

            $('#form-mtc-electric-p2h').on('change input', 'input, select, textarea', function() {
                saveFormLocalStorage();
            });

            $(document).on('change', '.status-radio', function() {
                if (isLoading) return;

                const $row = $(this).closest('.item-row');
                const isNg = $row.find('input[value="0"]').is(':checked');
                const $ket = $row.find('.keterangan-wrapper input');

                if (isNg) {
                    $row.addClass('not-ok');
                    $row.find('.keterangan-wrapper').removeClass('d-none');
                    $ket.attr('required', true);
                } else {
                    $row.removeClass('not-ok');
                    $row.find('.keterangan-wrapper').addClass('d-none');
                    $ket.val('').removeAttr('required');
                }

                updateStatusRow($row);
                saveFormLocalStorage();
            });

            function updateStatusRow($row) {
                const isNg = $row.find('input[value="0"]').is(':checked');
                const $wrapper = $row.find('.keterangan-wrapper');
                const $input = $wrapper.find('input');

                if (isNg) {
                    $row.addClass('not-ok');
                    $wrapper.removeClass('d-none');
                    $input.attr('required', true);
                } else {
                    $row.removeClass('not-ok');
                    $wrapper.addClass('d-none');
                    $input.val('').removeAttr('required');
                }
            }

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

            $('#form-mtc-electric-p2h').on('submit', function(e) {
                e.preventDefault();

                pendingFormData = new FormData(this);

                $('#modalTtd').modal('show');
            });

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
                    url: "{{ route('mtc.electric-p2h.store') }}",
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
                            resetElectricP2hForm();
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

            $('#btnResetKondisi').on('click', function() {
                Swal.fire({
                    title: 'Reset kondisi?',
                    text: 'Semua checklist dan keterangan akan dikosongkan',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Reset',
                    cancelButtonText: 'Batal'
                }).then(r => {
                    if (!r.isConfirmed) return;

                    resetElectricP2hForm();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Checklist telah direset',
                        timer: 1500,
                        showConfirmButton: false
                    });
                });
            });

            function resetElectricP2hForm() {
                // reset form native
                const $form = $('#form-mtc-electric-p2h');
                $form[0].reset();

                $('.kondisi-radio').prop('checked', false);
                $('.kondisi-btn').removeClass('active');

                $('.item-row').each(function() {
                    const $row = $(this);

                    $row.removeClass('not-ok');

                    const $wrapper = $row.find('.keterangan-wrapper');
                    const $input = $wrapper.find('input, textarea');

                    $wrapper.addClass('d-none');
                    $input
                        .val('')
                        .removeClass('is-invalid')
                        .removeAttr('required');
                });

                $('#clientError').addClass('d-none').text('');

                localStorage.removeItem(STORAGE_KEY);
            }

        });
    </script>
@endsection
