@extends('layouts.app')

@section('title', ' Form Check Mtc Diesel P2H')

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
                Form Check Maintenance Diesel P2H
            </div>

            <div class="card-body">

                <form id="form-mtc-diesel-p2h" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Nama Mesin
                                <span class="text-danger">*</span></label>
                            <select name="mesin_id" id="mesin_id" class="form-control" required>
                                <option value="" selected disabled>
                                    Pilih mesin - lokasi
                                </option>
                                @foreach ($mesin as $item)
                                <option value="{{ $item->id }}" data-departemen="{{ $item->dept }}">
                                    {{ $item->nama_mesin }} - {{ $item->departemen }}
                                </option>
                                @endforeach
                            </select>
                            @error('mesin_id')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal
                                <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', now()->toDateString()) }}">
                            @error('tanggal')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Waktu Mulai<span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="waktu_mulai" required>
                            @error('waktu_mulai')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Waktu Selesai</label>
                            <input type="time" class="form-control" name="waktu_selesai" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Departemen <span class="text-danger">*</span></label>
                            <input type="text" name="departemen" class="form-control" value="{{ old('departemen') }}" placeholder="Warehouse">
                            @error('departemen')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">No Unit <span class="text-danger">*</span></label>
                            <input type="text" name="no_unit" class="form-control" value="{{ old('no_unit') }}" placeholder="F01">
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
                            <label class="form-label">Hours Meter (Jam Operasional)<span class="text-danger">*</span></label>
                            <input type="numeric" name="hours_meter" class="form-control" value="{{ old('hours_meter') }}" placeholder="12345">
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
                    $dieselP2h = [
                    'klakson' => [
                    'label' => 'Check Klakson',
                    'standar' => 'Bunyi ketika tombol ditekan',
                    ],
                    'buzzer_back' => [
                    'label' => 'Check Buzzer Back',
                    'standar' => 'Berbunyi normal saat maju dan mundur',
                    ],
                    'oli_mesin' => [
                    'label' => 'Check Kondisi & Level Oli Mesin',
                    'standar' => 'Berada di level max dan tidak ada kebocoran',
                    ],
                    'radiator_hose' => [
                    'label' => 'Check Kondisi Level Radiator & Hose',
                    'standar' => 'Berada di level max dan tidak ada kebocoran',
                    ],
                    'water_pump' => [
                    'label' => 'Check Water Pump',
                    'standar' => 'Tidak ada kebocoran',
                    ],
                    'injection_system' => [
                    'label' => 'Check Injection Pump, Injector & Piping',
                    'standar' => 'Tidak ada kebocoran',
                    ],
                    'fan_vbelt' => [
                    'label' => 'Check Fan & V-Belt',
                    'standar' => 'Berfungsi baik dan V-belt tidak retak atau putus',
                    ],
                    'turbocharger_manifold' => [
                    'label' => 'Check Turbocharger & Manifold',
                    'standar' => 'Berfungsi baik dan terlubrikasi',
                    ],
                    'tensioner_belt' => [
                    'label' => 'Check Automatic Tensioner Belt',
                    'standar' => 'Berfungsi dengan baik',
                    ],
                    'starting_motor' => [
                    'label' => 'Check Fungsi Starting Motor',
                    'standar' => 'Berfungsi dengan baik',
                    ],
                    'alternator' => [
                    'label' => 'Check Fungsi Alternator',
                    'standar' => 'Berfungsi dengan baik',
                    ],
                    'control_display' => [
                    'label' => 'Check Control Display',
                    'standar' => 'Berfungsi normal, tidak pecah, dan tidak ada alarm',
                    ],
                    'oli_transmisi' => [
                    'label' => 'Check Kondisi & Level Oli Transmisi',
                    'standar' => 'Berada di level max dan tidak ada kebocoran',
                    ],
                    'aki' => [
                    'label' => 'Check Kondisi Aki & Level Air Aki',
                    'standar' => 'Level max, aki tidak drop, dan bersih',
                    ],
                    'engine_mounting' => [
                    'label' => 'Check Engine Mounting',
                    'standar' => 'Berfungsi dengan baik',
                    ],
                    'filter_oli_transmisi' => [
                    'label' => 'Check Filter Oli Transmisi',
                    'standar' => 'Tidak ada kebocoran oli',
                    ],
                    'fungsi_rem' => [
                    'label' => 'Check Fungsi Rem',
                    'standar' => 'Berfungsi dengan baik dan tidak blong',
                    ],
                    'fungsi_kopling' => [
                    'label' => 'Check Fungsi Kopling',
                    'standar' => 'Berfungsi dengan baik dan tidak macet',
                    ],
                    'oli_hydraulic' => [
                    'label' => 'Check Kondisi & Level Oli Hydraulic',
                    'standar' => 'Berada di level max dan tidak ada kebocoran',
                    ],
                    'hydraulic_system' => [
                    'label' => 'Check Fungsi Hydraulic System',
                    'standar' => 'Berfungsi dengan baik dan terlubrikasi',
                    ],
                    'steering_system' => [
                    'label' => 'Check Fungsi Steering System',
                    'standar' => 'Tidak berat dan bergerak lancar',
                    ],
                    'body_back_rest' => [
                    'label' => 'Check Kondisi Back Rest & Body',
                    'standar' => 'Tidak ada cacat atau penyok',
                    ],
                    'kaca_spion' => [
                    'label' => 'Check Kaca Spion',
                    'standar' => 'Terpasang lengkap dan tidak pecah',
                    ],
                    'bucket_pin' => [
                    'label' => 'Check Kondisi Bucket & Pin Bucket',
                    'standar' => 'Berfungsi baik dan tidak retak atau hilang',
                    ],
                    'dump_pin_bushing' => [
                    'label' => 'Check Kondisi Dump, Pin & Bushing',
                    'standar' => 'Berfungsi dan tidak retak atau hilang',
                    ],
                    'seal_hydraulic' => [
                    'label' => 'Check Kondisi Seal Hydraulic',
                    'standar' => 'Tidak ada kebocoran oli',
                    ],
                    'roda_ban_baut' => [
                    'label' => 'Check Kondisi Roda, Ban & Baut',
                    'standar' => 'Ban layak pakai dan baut terpasang kencang',
                    ],
                    'lampu_unit' => [
                    'label' => 'Check Lampu Depan & Belakang (Kanan & Kiri)',
                    'standar' => 'Menyala normal dan tidak pecah',
                    ],
                    'baut_bearing_molen' => [
                    'label' => 'Check Baut Bearing Molen & Gandengan',
                    'standar' => 'Baut terpasang utuh dan kencang',
                    ],
                    'baut_hanger_as' => [
                    'label' => 'Check Baut Hanger As Roda',
                    'standar' => 'Baut terpasang utuh dan kencang',
                    ],
                    'baut_grease' => [
                    'label' => 'Check Kondisi Baut Grease',
                    'standar' => 'Baut tidak aus dan terlumasi grease',
                    ],
                    'katup_pembuangan_angin' => [
                    'label' => 'Check Katup Pembuangan Angin',
                    'standar' => 'Berfungsi dengan baik',
                    ],
                    ];
                    @endphp

                    <h6 class="fw-bold text-primary mb-3 mt-4">Checklist Mtc Diesel P2h</h6>

                    @foreach (array_chunk($dieselP2h, 2, true) as $row)
                    <div class="row g-3 mb-3">
                        @foreach ($row as $field => $item)
                        <div class="col-md-6 col-12">
                            <div class="card shadow-sm item-card p-3 item-row" data-field="{{ $field }}">

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
                                    <input type="radio" class="btn-check status-radio" name="{{ $field }}" value="1" id="{{ $field }}_ok">
                                    <label class="btn btn-outline-success" for="{{ $field }}_ok">OK</label>

                                    <input type="radio" class="btn-check status-radio" name="{{ $field }}" value="0" id="{{ $field }}_ng">
                                    <label class="btn btn-outline-danger" for="{{ $field }}_ng">Tidak
                                        OK</label>
                                </div>

                                {{-- Keterangan --}}
                                <div class="keterangan-wrapper d-none mt-2">
                                    <input type="text" class="form-control form-control-sm" name="keterangan_{{ $field }}" placeholder="Wajib diisi jika Tidak OK" data-required-when-not-ok>
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
        let signaturePad = null;
        let pendingFormData = null;
        $('#mesin_id').on('change', function() {
            const selected = $(this).find(':selected');

            const departemen = selected.data('departemen') || '';
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

        let selectedStaff = null;
        let selectedUser = null;

        $('#form-mtc-diesel-p2h').on('submit', function(e) {
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
                url: "{{ route('mtc.diesel-p2h.store') }}",
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
                        resetDieselP2hForm();
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

                resetDieselP2hForm();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Checklist telah direset',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        });

        function resetDieselP2hForm() {
            // reset form native
            const $form = $('#form-mtc-diesel-p2h');
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

        }
    });
</script>
@endsection