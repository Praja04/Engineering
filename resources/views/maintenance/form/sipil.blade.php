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
                                <label class="form-label">Waktu Mulai
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="waktu_mulai" id="waktu_mulai"
                                    placeholder="Pilih waktu" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Waktu Selesai</label>
                                <input type="text" class="form-control" name="waktu_selesai" id="waktu_selesai"
                                    placeholder="Pilih waktu">
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

                                <select name="area" id="area" class="form-control">
                                    <option value="" disabled selected>-- Pilih Area --</option>
                                    @foreach ($area as $item)
                                        <option value="{{ $item->nama_mesin }}" data-lokasi="{{ $item->lokasi }}"
                                            data-departemen="{{ $item->dept }}">
                                            {{ $item->nama_mesin }} - {{ $item->lokasi }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('area')
                                    <div class=" text-danger small">{{ $message }}
                                    </div>
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
                                        <div class="card shadow-sm item-card p-3 item-row"
                                            data-field="{{ $field }}">

                                            {{-- Judul --}}
                                            <label class="form-label fw-semibold mb-1" data-label="{{ $field }}">
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
                                                <select name="materials[0][mid]" class="form-control form-control-sm mid-select2" required></select>
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
                            <button type="submit" id="btn-submit" class="btn btn-primary">Simpan</button>
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
            let index = 0;
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
            $('#area').on('change', function() {
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
                });
            }

            // Initialize existing rows
            initMidSelect2('.mid-select2');

            $('#addRow').on('click', function() {
                let row = `
                    <tr>
                        <td>
                            <select name="materials[${index}][mid]" class="form-control form-control-sm mid-select2" required></select>
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

                const $row = $(row);
                $('#materialTable tbody').append($row);
                initMidSelect2($row.find('.mid-select2'));
                index++;
            });

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
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


            }

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

            let pendingFormData = null;
            let selectedStaff = null;
            let selectedUser = null;

            $('#form-mtc-sipil').on('submit', function(e) {
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
