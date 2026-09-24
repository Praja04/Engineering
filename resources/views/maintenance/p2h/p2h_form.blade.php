@extends('layouts.app')

@section('title', 'Form Check Genset P2H')

@section('styles')
    <style>
        .category-card {
            border-left: 4px solid #ffc107;
            border-radius: 8px;
        }

        .checklist-item {
            transition: background-color 0.2s ease;
        }

        .checklist-item:hover {
            background-color: #f8f9fa;
        }

        .btn-check+.btn {
            min-width: 65px;
            font-weight: 600;
        }

        .item-card.not-ok {
            background-color: rgba(220, 53, 69, 0.05);
            border-color: #dc3545;
        }

        .select2-selection__placeholder {
            font-size: 13px;
        }

        .select2-container--bootstrap-5 .select2-results__option {
            font-size: 11px !important;
            padding: 3px 8px !important;
            line-height: 1.3 !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            font-size: 13px !important;
            line-height: 1.3 !important;
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted,
        .select2-container--bootstrap-5 .select2-results__option--selected {
            font-size: 11px !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <span class="fw-bold fs-6"><i class="ri-plug-line me-2"></i>Form Pemeriksaan Genset P2H</span>
                    <a href="{{ route('p2h.data.index') }}" class="btn btn-sm btn-dark">
                        <i class="ri-arrow-left-line me-1"></i> Data P2H
                    </a>
                </div>

                <div class="card-body">
                    <form id="form-mtc-p2h" method="POST">
                        @csrf

                        {{-- INFORMASI UMUM --}}
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Unit Genset <span class="text-danger">*</span></label>
                                <select name="mesin_id" id="mesin_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih unit genset</option>
                                    @foreach ($mesin as $item)
                                        <option value="{{ $item->id }}" 
                                            data-kode="{{ $item->kode_mesin }}"
                                            data-departemen="{{ $item->dept }}"
                                            data-nama-mesin="{{ $item->nama_mesin }}">
                                            {{ $item->nama_mesin }} {{ $item->kode_mesin ? "({$item->kode_mesin})" : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">No Unit / Code</label>
                                <input type="text" name="no_unit" id="no_unit" class="form-control"
                                    placeholder="Contoh: GENSET-01">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Departemen <span class="text-danger">*</span></label>
                                <input type="text" name="departemen" id="departemen" class="form-control"
                                    placeholder="Engineering" value="ENG" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Shift <span class="text-danger">*</span></label>
                                <select name="shift" class="form-select" required>
                                    <option value="1" selected>Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="waktu_mulai" id="waktu_mulai" value="{{ date('H:i') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Waktu Selesai</label>
                                <input type="time" class="form-control" name="waktu_selesai" id="waktu_selesai">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Hours Meter (Jam Operasional)</label>
                                <input type="number" step="any" name="hours_meter" id="hours_meter" class="form-control"
                                    placeholder="Contoh: 450">
                            </div>
                        </div>

                        {{-- Helper function to generate checklist item row --}}
                        @php
                            if (!function_exists('renderP2hChecklistItemGenset')) {
                                function renderP2hChecklistItemGenset($fieldName, $labelText, $standarText)
                                {
                                    $uniqId = $fieldName . '_' . bin2hex(random_bytes(3));
                                    return '
                                <div class="col-md-6 col-12 checklist-col">
                                    <div class="checklist-item p-3 border rounded h-100 item-row" data-field="' . $fieldName . '">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                                            <div class="flex-grow-1 pe-2 mb-2 mb-sm-0">
                                                <div class="fw-semibold text-dark" style="font-size: 0.9rem;">' . $labelText . '</div>
                                                <div class="text-muted small" style="font-size: 0.78rem;">Standar: ' . $standarText . '</div>
                                            </div>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <input type="radio" class="btn-check radio-checklist status-radio" name="' . $fieldName . '" id="' . $uniqId . '_ok" value="1">
                                                <label class="btn btn-outline-success px-3 rounded-start" for="' . $uniqId . '_ok">OK</label>

                                                <input type="radio" class="btn-check radio-checklist status-radio" name="' . $fieldName . '" id="' . $uniqId . '_ng" value="0">
                                                <label class="btn btn-outline-danger px-3" for="' . $uniqId . '_ng">NOK</label>

                                                <input type="radio" class="btn-check radio-checklist status-radio" name="' . $fieldName . '" id="' . $uniqId . '_empty" value="" checked>
                                                <label class="btn btn-outline-secondary px-3 rounded-end" for="' . $uniqId . '_empty">-</label>
                                            </div>
                                        </div>
                                        <div class="keterangan-wrapper d-none mt-2">
                                            <input type="text" class="form-control form-control-sm border-danger text-danger bg-soft-danger" name="keterangan_' . $fieldName . '" placeholder="Keterangan wajib jika NOK...">
                                        </div>
                                    </div>
                                </div>
                                ';
                                }
                            }
                        @endphp

                        {{-- CHECKLIST GENSET --}}
                        <div class="card shadow-sm category-card mt-4 mb-4">
                            <div class="card-header bg-soft-warning border-0">
                                <h6 class="card-title text-warning fw-bold mb-0">
                                    <i class="ri-plug-line me-2"></i> Checklist Pemeriksaan Harian Genset P2H
                                </h6>
                            </div>
                            <div class="card-body py-3">
                                <div class="row g-3">
                                    {!! renderP2hChecklistItemGenset('level_oli_mesin', 'Level Oli Mesin', 'Berada di level Max') !!}
                                    {!! renderP2hChecklistItemGenset('kebocoran_oli_mesin', 'Kebocoran Oli Mesin', 'Tidak ada kebocoran') !!}
                                    {!! renderP2hChecklistItemGenset('level_coolant_radiator', 'Level Coolant/Radiator', 'Berada di level Max') !!}
                                    {!! renderP2hChecklistItemGenset('kebocoran_coolant', 'Kebocoran Coolant', 'Tidak ada kebocoran') !!}
                                    {!! renderP2hChecklistItemGenset('level_bahan_bakar', 'Level Bahan Bakar', 'Berada di level Max') !!}
                                    {!! renderP2hChecklistItemGenset('kebocoran_bahan_bakar', 'Kebocoran Bahan Bakar', 'Tidak ada kebocoran') !!}
                                    {!! renderP2hChecklistItemGenset('kondisi_aki_baterai', 'Kondisi Aki/Baterai', 'Terminal bersih, tidak korosi') !!}
                                    {!! renderP2hChecklistItemGenset('tegangan_baterai', 'Tegangan Baterai', 'Normal') !!}
                                    {!! renderP2hChecklistItemGenset('filter_udara', 'Filter Udara', 'Bersih') !!}
                                    {!! renderP2hChecklistItemGenset('kondisi_panel_genset', 'Kondisi Panel Genset', 'Bersih, Indikator Normal') !!}
                                    {!! renderP2hChecklistItemGenset('emergency_stop', 'Emergency Stop', 'Tidak ada alarm error') !!}
                                    {!! renderP2hChecklistItemGenset('suara_mesin_running', 'Suara Mesin Saat Running', 'Halus, tidak kasar / abnormal') !!}
                                    {!! renderP2hChecklistItemGenset('kebersihan_area_genset', 'Kebersihan Area Genset', 'Bersih dari kotoran & genangan') !!}
                                    {!! renderP2hChecklistItemGenset('kondisi_knalpot_exhaust', 'Kondisi Knalpot / Exhaust', 'Tidak bocor') !!}
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Catatan Tambahan</label>
                                <textarea class="form-control" name="catatan" rows="3" placeholder="Masukkan catatan kondisi aktual jika ada..."></textarea>
                            </div>
                        </div>

                        {{-- TOMBOL AKSI --}}
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="button" class="btn btn-outline-secondary" id="btnResetForm">
                                <i class="ri-refresh-line me-1"></i> Reset Form
                            </button>
                            <button type="submit" class="btn btn-warning px-4 py-2 fw-semibold" id="btn-submit">
                                <i class="ri-save-line me-1"></i> Simpan Genset P2H
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL APPROVER --}}
    <div class="modal fade" id="modalApprover" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Approver Genset P2H</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Staff Maintenance <span class="text-danger">*</span></label>
                        <select id="staffDropdown" class="form-select">
                            <option value="">Memuat staff...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User / Department Head <span class="text-danger">*</span></label>
                        <select id="userDropdown" class="form-select">
                            <option value="">Memuat user...</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSelectApprover">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let pendingFormData = null;
            let selectedStaff = null;
            let selectedUser = null;

            $('#mesin_id').on('change', function() {
                const $opt = $(this).find('option:selected');
                const kode = $opt.data('kode');
                const dept = $opt.data('departemen');

                if (kode) {
                    $('#no_unit').val(kode);
                }
                if (dept) {
                    $('#departemen').val(dept);
                }
            });

            $('input.radio-checklist:checked').each(function() {
                $(this).data('waschecked', true);
            });

            $(document).on('click', '.radio-checklist', function() {
                var $this = $(this);
                if ($this.val() === '') {
                    $('input[name="' + this.name + '"].radio-checklist').data('waschecked', false);
                    $this.data('waschecked', true);
                    return;
                }

                if ($this.data('waschecked') === true) {
                    this.checked = false;
                    $this.data('waschecked', false);
                    const $emptyRadio = $('input[name="' + this.name + '"][value=""]');
                    $emptyRadio.prop('checked', true).data('waschecked', true);
                    $emptyRadio.trigger('change');
                } else {
                    $('input[name="' + this.name + '"].radio-checklist').data('waschecked', false);
                    $this.data('waschecked', true);
                }
            });

            // Toggle keterangan input when NOK is checked
            $(document).on('change', '.radio-checklist', function() {
                const val = $(this).val();
                const $row = $(this).closest('.item-row');
                const $wrapper = $row.find('.keterangan-wrapper');
                const $input = $wrapper.find('input');

                if (val === '0') {
                    $row.addClass('not-ok');
                    $wrapper.removeClass('d-none');
                    $input.attr('required', true);
                } else {
                    $row.removeClass('not-ok');
                    $wrapper.addClass('d-none');
                    $input.val('').removeAttr('required');
                }
            });

            $('#form-mtc-p2h').on('submit', function(e) {
                e.preventDefault();

                let hasChecked = false;
                $('.radio-checklist:checked').each(function() {
                    if ($(this).val() !== '') {
                        hasChecked = true;
                        return false;
                    }
                });

                if (!hasChecked) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Checklist Belum Diisi',
                        text: 'Silakan periksa minimal salah satu item checklist sebelum menyimpan.'
                    });
                    return;
                }

                pendingFormData = new FormData(this);

                // Open Approver Modal
                $('#modalApprover').modal('show');

                $.get('/api/mtc/users/approvers', function(res) {
                    const $staff = $('#staffDropdown').empty().append('<option value="">Pilih staff</option>');
                    (res.staff || []).forEach(u => {
                        $staff.append(`<option value="${u.id}">${u.username}</option>`);
                    });

                    const $user = $('#userDropdown').empty().append('<option value="">Pilih user</option>');
                    (res.user || []).forEach(u => {
                        $user.append(`<option value="${u.id}">${u.username}</option>`);
                    });
                });
            });

            $('#btnSelectApprover').on('click', function() {
                selectedStaff = $('#staffDropdown').val();
                selectedUser = $('#userDropdown').val();

                if (!selectedStaff || !selectedUser) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Approver',
                        text: 'Pilih staff dan user terlebih dahulu.'
                    });
                    return;
                }

                pendingFormData.append('staff_id', selectedStaff);
                pendingFormData.append('user_id', selectedUser);

                // Collect NOK details
                let nokList = [];
                $('.item-row.not-ok').each(function() {
                    const name = $(this).find('.fw-semibold').text().trim();
                    const ket = $(this).find('.keterangan-wrapper input').val().trim();
                    nokList.push(`${name}: ${ket}`);
                });
                if (nokList.length > 0) {
                    pendingFormData.append('keterangan', nokList.join(' | '));
                }

                $('#modalApprover').modal('hide');
                submitForm(pendingFormData);
            });

            function submitForm(formData) {
                const $btn = $('#btn-submit');
                $btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ route('p2h.form.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Data Genset P2H berhasil disimpan!',
                            confirmButtonText: 'Lihat Data'
                        }).then(() => {
                            window.location.href = "{{ route('p2h.data.index') }}";
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.'
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> Simpan Genset P2H');
                    }
                });
            }

            $('#btnResetForm').on('click', function() {
                Swal.fire({
                    title: 'Reset form?',
                    text: 'Semua checklist akan dikosongkan',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Reset',
                    cancelButtonText: 'Batal'
                }).then(r => {
                    if (r.isConfirmed) {
                        $('#form-mtc-p2h')[0].reset();
                        $('.radio-checklist').data('waschecked', false);
                        $('.radio-checklist[value=""]').prop('checked', true).data('waschecked', true);
                        $('.item-row').removeClass('not-ok');
                        $('.keterangan-wrapper').addClass('d-none').find('input').val('');
                    }
                });
            });
        });
    </script>
@endsection
