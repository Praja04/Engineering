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
                                <label class="form-label">Area
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="area" class="form-control" value="{{ old('area') }}"
                                    placeholder="Office Lt.2 / Gudang A">
                                @error('area')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- error global dari withValidator (checklist) --}}
                        @error('checklist')
                            <div class="alert alert-danger py-2">{{ $message }}</div>
                        @enderror

                        <div class="mt-4">
                            <div class="row g-3">
                                @foreach ($items as $i => $item)
                                    @php
                                        $oldKondisi = old("details.$i.kondisi");
                                        $oldKet = old("details.$i.keterangan", '');
                                        $idYa = "sipil_{$i}_ya";
                                        $idTidak = "sipil_{$i}_tidak";
                                    @endphp

                                    <div class="col-12 col-md-6">
                                        <div class="card border shadow-sm item-card {{ (string) $oldKondisi === '0' ? 'not-ok' : '' }}"
                                            data-index="{{ $i }}">
                                            <div class="card-header bg-light">
                                                <strong class="d-block">{{ $item->jenis_perawatan }}</strong>
                                                <small
                                                    class="text-muted d-block mt-1">{{ $item->standar_pemeliharaan }}</small>
                                            </div>

                                            <div class="card-body pb-2 pt-3">
                                                <!-- Radio Ya/Tidak -->
                                                <div class="d-flex gap-2 mb-1">
                                                    <div class="flex-fill">
                                                        <input type="hidden" name="details[{{ $i }}][item_id]"
                                                            value="{{ $item->id }}">

                                                        <input id="{{ $idYa }}" type="radio"
                                                            name="details[{{ $i }}][kondisi]" value="1"
                                                            class="visually-hidden kondisi-radio"
                                                            {{ (string) $oldKondisi === '1' ? 'checked' : '' }}>

                                                        <label for="{{ $idYa }}"
                                                            class="btn btn-outline-success w-100 py-2 kondisi-btn {{ (string) $oldKondisi === '1' ? 'active' : '' }}">
                                                            YA
                                                        </label>
                                                    </div>

                                                    <div class="flex-fill">
                                                        <input id="{{ $idTidak }}" type="radio"
                                                            name="details[{{ $i }}][kondisi]" value="0"
                                                            class="visually-hidden kondisi-radio"
                                                            {{ (string) $oldKondisi === '0' ? 'checked' : '' }}>

                                                        <label for="{{ $idTidak }}"
                                                            class="btn btn-outline-danger w-100 py-2 kondisi-btn {{ (string) $oldKondisi === '0' ? 'active' : '' }}">
                                                            TIDAK
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Placeholder kosong untuk keterangan nanti (akan ditambahkan via JS) -->
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Rekomendasi</label>
                                <input type="text" name="rekomendasi" class="form-control"
                                    value="{{ old('rekomendasi') }}">
                                @error('rekomendasi')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Korektif</label>
                                <input type="text" name="korektif" class="form-control" value="{{ old('korektif') }}">
                                @error('korektif')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const STORAGE_KEY = 'form_mtc_sipil_data';

            function saveFormLocalStorage() {
                const data = {};

                $('#form-mtc-sipil').find('input, textarea').each(function() {
                    const name = this.name;
                    if (!name) return;

                    let value;

                    if (this.type === 'radio') {
                        if (this.checked) {
                            value = this.value;
                        }
                    } else if (this.type === 'checkbox') {
                        value = this.checked ? '1' : '0';
                    } else {
                        value = $(this).val() || ''; // pakai || '' biar tidak undefined
                    }

                    if (value !== undefined && value !== null) {
                        if (this.tagName.toLowerCase() === 'textarea' || value !== '') {
                            data[name] = value;
                        }
                    }
                });

                console.log('Saved data:', data);
                localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
            }

            function loadFormLocalStorage() {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (!saved) return;

                const data = JSON.parse(saved);

                // Langkah 1: Set semua radio & input lain dulu (tanpa trigger change)
                $.each(data, function(name, value) {
                    const escapedName = name.replace(/([[\]])/g, '\\$1');
                    const $el = $('[name="' + escapedName + '"]');

                    if ($el.length) {
                        if ($el.is(':radio')) {
                            const $targetRadio = $el.filter('[value="' + value + '"]');
                            if ($targetRadio.length) {
                                $targetRadio.prop('checked', true);
                            }
                        } else if ($el.is('textarea, input[type="text"], input[type="date"]')) {
                            $el.val(value);
                        }
                    }
                });

                // Langkah 2: Trigger change pada semua radio yang checked → ini akan membuat wrapper keterangan kalau TIDAK
                $('.kondisi-radio:checked').each(function() {
                    $(this).trigger('change');
                });

                // Force create wrapper untuk card yang kondisi TIDAK (jaga-jaga kalau trigger change gagal)
                setTimeout(() => {
                    $('.item-card').each(function() {
                        const $card = $(this);
                        const isTidak = $card.find('.kondisi-radio[value="0"]').is(':checked');
                        if (isTidak && $card.find('.keterangan-wrapper').length === 0) {
                            const index = $card.data('index');
                            const $body = $card.find('.card-body');

                            const $wrapper = $(`
                                <div class="keterangan-wrapper mt-3">
                                    <textarea name="details[${index}][keterangan]" class="form-control" rows="2"
                                            placeholder="Keterangan wajib diisi karena kondisi TIDAK"></textarea>
                                    <div class="mt-2"></div>
                                </div>
                            `);

                            $body.append($wrapper);
                            $wrapper.show(); // langsung tampilkan tanpa animasi
                        }
                    });

                    // Isi value keterangan
                    $.each(data, function(name, value) {
                        if (name.includes('[keterangan]')) {
                            const escapedName = name.replace(/([[\]])/g, '\\$1');
                            const $textarea = $('[name="' + escapedName + '"]');
                            if ($textarea.length) {
                                $textarea.val(value);
                            }
                        }
                    });

                    saveFormLocalStorage();
                }, 600);

                updateRowState();
            }

            loadFormLocalStorage();

            $('#form-mtc-sipil').on('change', 'input[type="radio"], textarea', function() {
                saveFormLocalStorage();
            });

            function updateRowState() {
                $('.item-card').each(function() {
                    const $card = $(this);
                    const isTidak = $card.find('.kondisi-radio[value="0"]').is(':checked');
                    $card.toggleClass('not-ok', isTidak);
                });
            }

            $('#form-mtc-sipil').on('change', '.kondisi-radio', function() {
                const $radio = $(this);
                const $card = $radio.closest('.item-card');
                const $body = $card.find('.card-body');
                const index = $card.data('index'); // ambil dari data-index

                // Reset active tombol
                $card.find('.kondisi-btn').removeClass('active');
                const $checkedRadio = $card.find('.kondisi-radio:checked');
                if ($checkedRadio.length) {
                    $checkedRadio.next('label.kondisi-btn').addClass('active');
                }

                // Update warna card
                const isTidak = $card.find('.kondisi-radio[value="0"]').prop('checked');
                $card.toggleClass('not-ok', isTidak);

                // Cari wrapper keterangan
                let $wrapper = $card.find('.keterangan-wrapper');

                if (isTidak) {
                    if ($wrapper.length === 0) {
                        $wrapper = $(`
                            <div class="keterangan-wrapper mt-3">
                                <textarea name="details[${index}][keterangan]" class="form-control" rows="2"
                                        placeholder="Keterangan wajib diisi karena kondisi TIDAK"></textarea>
                                <div class="mt-2"></div>
                            </div>
                        `);

                        $body.append($wrapper);

                        saveFormLocalStorage();
                    }

                    $wrapper.slideDown(200, () => {
                        $wrapper.find('textarea').focus();
                        saveFormLocalStorage();
                    });
                    $wrapper.find('textarea').prop('required', true);
                } else {
                    if ($wrapper.length > 0) {
                        $wrapper.slideUp(200, function() {
                            $wrapper.remove();
                            saveFormLocalStorage(); // hapus dari localStorage kalau perlu
                        });
                    }
                }

                saveFormLocalStorage();
            });

            $('#form-mtc-sipil').on('submit', function(e) {
                e.preventDefault();

                $('#clientError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');

                let checkedCount = 0;
                let valid = true;

                $('.item-card').each(function() {
                    const $card = $(this);

                    const $tidak = $card.find('.kondisi-radio[value="0"]');
                    const $ya = $card.find('.kondisi-radio[value="1"]');
                    const $ket = $card.find('textarea');

                    // Hitung berapa item yang sudah dipilih (Ya atau Tidak)
                    if ($tidak.is(':checked') || $ya.is(':checked')) {
                        checkedCount++;
                    }

                    // Kalau TIDAK → keterangan wajib diisi
                    if ($tidak.is(':checked') && !$ket.val().trim()) {
                        $ket.addClass('is-invalid');
                        valid = false;
                    }
                });

                if (!valid) {
                    $('#clientError')
                        .removeClass('d-none')
                        .text('Keterangan wajib diisi untuk item dengan kondisi TIDAK.');
                    return;
                }

                const formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('mtc.sipil.store') }}",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message ?? 'Data berhasil disimpan',
                            timer: 1800,
                            showConfirmButton: false
                        });

                        // Reset semua form
                        $('.kondisi-radio').prop('checked', false);
                        $('.kondisi-btn').removeClass('active');
                        $('.keterangan-wrapper').slideUp(200); // sembunyikan keterangan
                        $('textarea').val(''); // kosongkan isi
                        localStorage.removeItem(STORAGE_KEY);
                        $('#form-mtc-sipil')[0].reset();
                        updateRowState();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                        });
                    }
                });
            });

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
                    $('#form-mtc-sipil')[0].reset();
                    // Reset radio & tombol
                    $('.kondisi-radio').prop('checked', false);
                    $('.kondisi-btn').removeClass('active');
                    $('.keterangan-wrapper').slideUp(200, function() {
                        $(this).remove();
                    });
                    $('textarea').val('');
                    updateRowState();

                    localStorage.removeItem(STORAGE_KEY);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Checklist telah direset',
                        timer: 1500,
                        showConfirmButton: false
                    });
                });
            });
        });
    </script>
@endsection
