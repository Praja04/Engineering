@extends('layouts.app')

@section('title', ' Form Check Mtc Sipil')

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
                    Form Check Maintenance Sipil
                </div>

                <div class="card-body">

                    <form id="form-mtc-sipil" method="POST">
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
                                <label class="form-label">Departemen
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="departemen" class="form-control" value="{{ old('departemen') }}"
                                    placeholder="Produksi">
                                @error('departemen')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Lokasi </label>
                                <input type="text" class="form-control" name="lokasi" value="{{ old('lokasi') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Area
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="area" class="form-control" value="{{ old('area') }}"
                                    placeholder="Produksi Process">
                                @error('area')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- error global dari withValidator (checklist) --}}
                        @error('checklist')
                            <div class="alert alert-danger py-2">{{ $message }}</div>
                        @enderror

                        @php
                            $sipil = [
                                'plumbing' => [
                                    'label' => 'Plumbing',
                                    'standar' => 'Tidak ada kebocoran dan mampet saluran air pada pipa',
                                ],
                                'plafon' => [
                                    'label' => 'Plafon',
                                    'standar' => 'Tidak berlubang, berjamur dan retakan pada plafon',
                                ],
                                'lantai' => [
                                    'label' => 'Lantai',
                                    'standar' => 'Tidak berlubang, retak, gompal dan jamur pada lantai',
                                ],
                                'dinding' => [
                                    'label' => 'Dinding',
                                    'standar' =>
                                        'Tidak ada dinding retak, gompal dan cat atau wallpaper (mengelupas, berjamur, kusam)',
                                ],
                                'jendela' => [
                                    'label' => 'Jendela',
                                    'standar' =>
                                        'Engsel berfungsi dengan baik, tidak ada retakan kaca atau cover, cat tidak kusam dan tulangan tidak cacat',
                                ],
                                'pintu' => [
                                    'label' => 'Pintu',
                                    'standar' =>
                                        'Engsel berfungsi dengan baik, tidak ada retakan kaca atau cover, cat tidak kusam dan tulangan tidak cacat',
                                ],
                                'rooling_fast_door' => [
                                    'label' => 'Rooling / Fast Door',
                                    'standar' => 'Suara halus, rel terlubrikasi, naik dan turun normal',
                                ],
                            ];
                        @endphp

                        <h6 class="fw-bold text-primary mb-3 mt-4">Checklist Mtc Sipil</h6>

                        @foreach (array_chunk($sipil, 2, true) as $row)
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

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Rekomendasi</label>
                                <textarea class="form-control" name="rekomendasi" rows="3" value="{{ old('rekomendasi') }}"></textarea>
                                @error('rekomendasi')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Korektif</label>
                                <textarea class="form-control" name="korektif" rows="3" value="{{ old('korektif') }}"></textarea>
                                @error('korektif')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

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
            const STORAGE_KEY = 'form_mtc_sipil_data';
            let index = 0;
            let isLoading = false;

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

            function saveFormToLocalStorage() {
                let formData = {};
                let materials = [];

                // === FORM NORMAL ===
                $('#form-mtc-sipil')
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

            loadFormFromLocalStorage();

            $('#form-mtc-sipil').on('change input', 'input, select, textarea', function() {
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

            $('#btnResetKondisi').on('click', function() {
                Swal.fire({
                    title: 'Reset Form?',
                    text: 'Semua isian akan dikosongkan',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, reset',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        resetFormSipil();
                    }
                });
            });

            function resetFormSipil() {
                const $form = $('#form-mtc-sipil');

                $form[0].reset();

                $form.find('select').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).val(null).trigger('change');
                    }
                });

                $form.find('.kondisi-radio').prop('checked', false);
                $('.kondisi-btn').removeClass('active');
                $('.keterangan-wrapper').remove();
                $('.item-card').removeClass('not-ok');
                $('.is-invalid').removeClass('is-invalid');
                $('#materialTable tbody').empty();
                // updateRowState();

                localStorage.removeItem(STORAGE_KEY);
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

            let signaturePad = null;
            let pendingFormData = null;
            let selectedStaff = null;
            let selectedUser = null;

            $('#form-mtc-sipil').on('submit', function(e) {
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
                    url: "{{ route('mtc.sipil.store') }}",
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
                            resetFormSipil();
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
