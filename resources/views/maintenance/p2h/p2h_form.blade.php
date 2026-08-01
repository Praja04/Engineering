@extends('layouts.app')

@section('title', 'Form Check Mtc P2H')

@section('styles')
    <style>
        .category-card {
            border-left: 4px solid #1b4965;
            border-radius: 8px;
        }

        .card-type {
            cursor: pointer;
            border: 2px solid #e9ecef !important;
            border-radius: 12px;
            transition: all 0.25s ease-in-out;
            background-color: #ffffff;
        }

        .card-type:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
            border-color: #ced4da !important;
        }

        .card-type.active[data-value="Electric P2H"] {
            border-color: #0d6efd !important;
            background-color: rgba(13, 110, 253, 0.05) !important;
            box-shadow: 0 6px 12px rgba(13, 110, 253, 0.1) !important;
        }

        .card-type.active[data-value="Diesel P2H"] {
            border-color: #dc3545 !important;
            background-color: rgba(220, 53, 69, 0.05) !important;
            box-shadow: 0 6px 12px rgba(220, 53, 69, 0.1) !important;
        }

        .card-type.active[data-value="Genset P2H"] {
            border-color: #ffc107 !important;
            background-color: rgba(255, 193, 7, 0.05) !important;
            box-shadow: 0 6px 12px rgba(255, 193, 7, 0.1) !important;
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
                <div class="card-header bg-primary text-white fw-bold">
                    Form Check Maintenance P2H (Electric & Diesel)
                </div>

                <div class="card-body">
                    <form id="form-mtc-p2h" method="POST">
                        @csrf

                        {{-- SELEKTOR JENIS P2H (CARDS) --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="card card-type p-3 h-100 text-center text-dark" data-value="Electric P2H">
                                    <div class="fs-1 text-primary mb-2">
                                        <i class="ri-flashlight-line"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1">Electric P2H</h5>
                                    <small class="text-muted">Inspeksi unit bertenaga listrik / battery</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-type p-3 h-100 text-center text-dark" data-value="Diesel P2H">
                                    <div class="fs-1 text-danger mb-2">
                                        <i class="ri-water-flash-line"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1">Diesel P2H</h5>
                                    <small class="text-muted">Inspeksi unit bertenaga diesel / BBM</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-type p-3 h-100 text-center text-dark" data-value="Genset P2H">
                                    <div class="fs-1 text-warning mb-2">
                                        <i class="ri-plug-line"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1">Genset P2H</h5>
                                    <small class="text-muted">Inspeksi untuk unit Generator Set (Genset)</small>
                                </div>
                            </div>
                        </div>

                        <div id="form-fields-wrapper" style="display: none;">
                            {{-- INFORMASI UMUM --}}
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Unit/Mesin <span class="text-danger">*</span></label>
                                    <select name="mesin_id" id="mesin_id" class="form-control" required>
                                        <option value="" disabled selected>Pilih unit - departemen</option>
                                        @foreach ($mesin as $item)
                                            <option value="{{ $item->id }}" data-jenis="{{ $item->jenis_mtc }}"
                                                data-departemen="{{ $item->dept }}"
                                                data-nama-mesin="{{ $item->nama_mesin }}">
                                                {{ $item->nama_mesin }} - {{ $item->dept }} ({{ $item->jenis_mtc }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3" id="no_unit_text_wrapper">
                                    <label class="form-label">No Unit / Code <span class="text-danger">*</span></label>
                                    <input type="text" name="no_unit" id="no_unit" class="form-control"
                                        placeholder="Contoh: F01" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}"
                                        required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="waktu_mulai" id="waktu_mulai"
                                        placeholder="Pilih waktu" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Waktu Selesai</label>
                                    <input type="text" class="form-control" name="waktu_selesai" id="waktu_selesai"
                                        placeholder="Pilih waktu">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Departemen <span class="text-danger">*</span></label>
                                    <input type="text" name="departemen" id="departemen" class="form-control"
                                        placeholder="Warehouse" required readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Shift <span class="text-danger">*</span></label>
                                    <select name="shift" class="form-select" required>
                                        <option value="" disabled selected>-- Pilih Shift --</option>
                                        <option value="1">Shift 1</option>
                                        <option value="2">Shift 2</option>
                                        <option value="3">Shift 3</option>
                                    </select>
                                </div>
                                <div class="col-md-3" id="hours_meter_wrapper">
                                    <label class="form-label">Hours Meter (Jam Operasional) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="hours_meter" id="hours_meter" class="form-control"
                                        placeholder="12345">
                                    <small class="form-label fst-italic text-muted">Catat sesuai kondisi aktual di
                                        unit</small>
                                </div>
                            </div>

                            {{-- Helper function to generate checklist item row --}}
                            @php
                                if (!function_exists('renderP2hChecklistItem')) {
                                    function renderP2hChecklistItem($fieldName, $labelText, $standarText, $type = null)
                                    {
                                        $uniqId = $fieldName . '_' . bin2hex(random_bytes(4));
                                        $dataAttr = $type ? 'data-type="' . $type . '"' : '';
                                        return '
                                    <div class="col-md-6 col-12 checklist-col">
                                        <div class="checklist-item p-3 border rounded h-100 item-row" data-field="' .
                                            $fieldName .
                                            '" ' .
                                            $dataAttr .
                                            '>
                                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                                <div class="flex-grow-1 pe-2 mb-2 mb-sm-0">
                                                    <div class="fw-semibold text-dark" style="font-size: 0.9rem;">' .
                                            $labelText .
                                            '</div>
                                                    <div class="text-muted small" style="font-size: 0.8rem;">Normal: ' .
                                            $standarText .
                                            '</div>
                                                </div>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <input type="radio" class="btn-check radio-checklist status-radio" name="' .
                                            $fieldName .
                                            '" id="' .
                                            $uniqId .
                                            '_ok" value="1">
                                                    <label class="btn btn-outline-success px-3 rounded-start" for="' .
                                            $uniqId .
                                            '_ok">OK</label>
 
                                                    <input type="radio" class="btn-check radio-checklist status-radio" name="' .
                                            $fieldName .
                                            '" id="' .
                                            $uniqId .
                                            '_ng" value="0">
                                                    <label class="btn btn-outline-danger px-3" for="' .
                                            $uniqId .
                                            '_ng">NOK</label>
 
                                                    <input type="radio" class="btn-check radio-checklist status-radio" name="' .
                                            $fieldName .
                                            '" id="' .
                                            $uniqId .
                                            '_empty" value="" checked>
                                                    <label class="btn btn-outline-secondary px-3 rounded-end" for="' .
                                            $uniqId .
                                            '_empty">No Check</label>
                                                </div>
                                            </div>
                                            <div class="keterangan-wrapper d-none mt-2">
                                                <input type="text" class="form-control form-control-sm input-description bg-soft-danger text-danger border-danger" name="keterangan_' .
                                            $fieldName .
                                            '" id="keterangan_' .
                                            $uniqId .
                                            '" placeholder="Keterangan wajib diisi jika NOK...">
                                            </div>
                                        </div>
                                    </div>
                                    ';
                                    }
                                }
                            @endphp

                            {{-- CHECKLIST ELECTRIC --}}
                            <div class="card shadow-sm category-card mt-4 mb-4" id="cardElectricChecklist"
                                style="display: none;">
                                <div class="card-header bg-soft-primary border-0">
                                    <h6 class="card-title text-primary fw-bold mb-0">
                                        <i class="ri-flashlight-line me-2"></i> Checklist P2H Electric
                                    </h6>
                                </div>
                                <div class="card-body py-3">
                                    <div class="row g-3">
                                        {!! renderP2hChecklistItem('level_minyak_rem', 'Check Level Minyak Rem', 'Berada di level max', 'forklift') !!}
                                        {!! renderP2hChecklistItem(
                                            'level_oli_hydraulic',
                                            'Check Level Oli Hydraulic',
                                            'Berada di level max',
                                            'forklift',
                                        ) !!}
                                        {!! renderP2hChecklistItem('isi_air_aki', 'Check Isi Air Aki', 'Berada di level standar', 'all') !!}
                                        {!! renderP2hChecklistItem('baterai', 'Check Baterai', 'Tidak kurang dari 30%', 'all') !!}
                                        {!! renderP2hChecklistItem(
                                            'hydraulic_system',
                                            'Hydraulic System',
                                            'Berfungsi dengan baik dan terlubrikasi',
                                            'all',
                                        ) !!}
                                        {!! renderP2hChecklistItem('selang_hydraulic', 'Selang Hydraulic', 'Tidak ada kebocoran oli', 'forklift_es') !!}
                                        {!! renderP2hChecklistItem(
                                            'lift_chains',
                                            'Lift Chains',
                                            'Kekencangan kanan dan kiri sama serta terlubrikasi',
                                            'forklift_es',
                                        ) !!}
                                        {!! renderP2hChecklistItem('fork', 'Pengecekan Fork', 'Tidak bengkok dan tidak patah', 'all') !!}
                                        {!! renderP2hChecklistItem('body_unit', 'Check Body Unit', 'Tidak lecet dan tidak penyok', 'all') !!}
                                        {!! renderP2hChecklistItem(
                                            'lampu_kombinasi_kiri',
                                            'Check Lampu Kombinasi Kiri',
                                            'Menyala normal dan tidak pecah',
                                            'forklift',
                                        ) !!}
                                        {!! renderP2hChecklistItem(
                                            'lampu_kombinasi_kanan',
                                            'Check Lampu Kombinasi Kanan',
                                            'Menyala normal dan tidak pecah',
                                            'forklift',
                                        ) !!}
                                        {!! renderP2hChecklistItem(
                                            'lampu_sorot',
                                            'Check Lampu Sorot / Head Lamp',
                                            'Menyala normal dan tidak pecah',
                                            'forklift',
                                        ) !!}
                                        {!! renderP2hChecklistItem(
                                            'lampu_sign_depan_kanan',
                                            'Check Lampu Sign Depan Kanan',
                                            'Menyala normal dan tidak pecah',
                                            'forklift',
                                        ) !!}
                                        {!! renderP2hChecklistItem(
                                            'lampu_sign_depan_kiri',
                                            'Check Lampu Sign Depan Kiri',
                                            'Menyala normal dan tidak pecah',
                                            'forklift',
                                        ) !!}
                                        {!! renderP2hChecklistItem('klakson', 'Check Klakson / Horn', 'Bunyi saat tombol ditekan', 'all') !!}
                                        {!! renderP2hChecklistItem(
                                            'buzzer_back',
                                            'Check Buzzer Back',
                                            'Berbunyi normal saat maju dan mundur',
                                            'forklift',
                                        ) !!}
                                        {!! renderP2hChecklistItem(
                                            'kaca_spion',
                                            'Check Kaca Spion',
                                            'Terpasang dengan baik dan tidak pecah',
                                            'forklift',
                                        ) !!}
                                        {!! renderP2hChecklistItem('baut_roda', 'Check Kekencangan Baut Roda', 'Kencang dan tidak patah', 'all') !!}
                                        {!! renderP2hChecklistItem('ban', 'Check Ban', 'Masih bagus dan layak pakai', 'all') !!}
                                        {!! renderP2hChecklistItem('kebersihan_unit', 'Check Kebersihan Unit', 'Bersih dari kotoran dan debu', 'all') !!}
                                        {!! renderP2hChecklistItem(
                                            'panel_display',
                                            'Check Panel Display',
                                            'Berfungsi normal, tidak pecah, dan tidak ada alarm',
                                            'all',
                                        ) !!}
                                        {!! renderP2hChecklistItem('sistem_kemudi', 'Sistem Kemudi', 'Tidak berat dan bergerak lancar', 'all') !!}
                                    </div>
                                </div>
                            </div>

                            {{-- CHECKLIST DIESEL --}}
                            <div class="card shadow-sm category-card mt-4 mb-4" id="cardDieselChecklist"
                                style="border-left-color: #e71d36; display: none;">
                                <div class="card-header bg-soft-danger border-0">
                                    <h6 class="card-title text-danger fw-bold mb-0">
                                        <i class="ri-water-flash-line me-2"></i> Checklist P2H Diesel
                                    </h6>
                                </div>
                                <div class="card-body py-3">
                                    <div class="row g-3">
                                        {!! renderP2hChecklistItem('klakson', 'Check Klakson', 'Bunyi ketika tombol ditekan') !!}
                                        {!! renderP2hChecklistItem('buzzer_back', 'Check Buzzer Back', 'Berbunyi normal saat maju dan mundur') !!}
                                        {!! renderP2hChecklistItem(
                                            'oli_mesin',
                                            'Check Kondisi & Level Oli Mesin',
                                            'Berada di level max dan tidak ada kebocoran',
                                        ) !!}
                                        {!! renderP2hChecklistItem(
                                            'radiator_hose',
                                            'Check Kondisi Level Radiator & Hose',
                                            'Berada di level max dan tidak ada kebocoran',
                                        ) !!}
                                        {!! renderP2hChecklistItem('water_pump', 'Check Water Pump', 'Tidak ada kebocoran') !!}
                                        {!! renderP2hChecklistItem('injection_system', 'Check Injection Pump, Injector & Piping', 'Tidak ada kebocoran') !!}
                                        {!! renderP2hChecklistItem(
                                            'fan_vbelt',
                                            'Check Fan & V-Belt',
                                            'Berfungsi baik dan V-belt tidak retak atau putus',
                                        ) !!}
                                        {!! renderP2hChecklistItem(
                                            'turbocharger_manifold',
                                            'Check Turbocharger & Manifold',
                                            'Berfungsi baik dan terlubrikasi',
                                        ) !!}
                                        {!! renderP2hChecklistItem('tensioner_belt', 'Check Automatic Tensioner Belt', 'Berfungsi dengan baik') !!}
                                        {!! renderP2hChecklistItem('starting_motor', 'Check Fungsi Starting Motor', 'Berfungsi dengan baik') !!}
                                        {!! renderP2hChecklistItem('alternator', 'Check Fungsi Alternator', 'Berfungsi dengan baik') !!}
                                        {!! renderP2hChecklistItem(
                                            'control_display',
                                            'Check Control Display',
                                            'Berfungsi normal, tidak pecah, dan tidak ada alarm',
                                        ) !!}
                                        {!! renderP2hChecklistItem(
                                            'oli_transmisi',
                                            'Check Kondisi & Level Oli Transmisi',
                                            'Berada di level max dan tidak ada kebocoran',
                                        ) !!}
                                        {!! renderP2hChecklistItem('aki', 'Check Kondisi Aki & Level Air Aki', 'Level max, aki tidak drop, dan bersih') !!}
                                        {!! renderP2hChecklistItem('engine_mounting', 'Check Engine Mounting', 'Berfungsi dengan baik') !!}
                                        {!! renderP2hChecklistItem('filter_oli_transmisi', 'Check Filter Oli Transmisi', 'Tidak ada kebocoran oli') !!}
                                        {!! renderP2hChecklistItem('fungsi_rem', 'Check Fungsi Rem', 'Berfungsi dengan baik dan tidak blong') !!}
                                        {!! renderP2hChecklistItem('fungsi_kopling', 'Check Fungsi Kopling', 'Berfungsi dengan baik dan tidak macet') !!}
                                        {!! renderP2hChecklistItem(
                                            'oli_hydraulic',
                                            'Check Kondisi & Level Oli Hydraulic',
                                            'Berada di level max dan tidak ada kebocoran',
                                        ) !!}
                                        {!! renderP2hChecklistItem(
                                            'hydraulic_system',
                                            'Check Fungsi Hydraulic System',
                                            'Berfungsi dengan baik dan terlubrikasi',
                                        ) !!}
                                        {!! renderP2hChecklistItem('steering_system', 'Check Fungsi Steering System', 'Tidak berat dan bergerak lancar') !!}
                                        {!! renderP2hChecklistItem('body_back_rest', 'Check Kondisi Back Rest & Body', 'Tidak ada cacat atau penyok') !!}
                                        {!! renderP2hChecklistItem('kaca_spion', 'Check Kaca Spion', 'Terpasang lengkap dan tidak pecah') !!}
                                        {!! renderP2hChecklistItem(
                                            'bucket_pin',
                                            'Check Kondisi Bucket & Pin Bucket',
                                            'Berfungsi baik dan tidak retak atau hilang',
                                        ) !!}
                                        {!! renderP2hChecklistItem(
                                            'dump_pin_bushing',
                                            'Check Kondisi Dump, Pin & Bushing',
                                            'Berfungsi dan tidak retak atau hilang',
                                        ) !!}
                                        {!! renderP2hChecklistItem('seal_hydraulic', 'Check Kondisi Seal Hydraulic', 'Tidak ada kebocoran oli') !!}
                                        {!! renderP2hChecklistItem(
                                            'roda_ban_baut',
                                            'Check Kondisi Roda, Ban & Baut',
                                            'Ban layak pakai dan baut terpasang kencang',
                                        ) !!}
                                        {!! renderP2hChecklistItem(
                                            'lampu_unit',
                                            'Check Lampu Depan & Belakang (Kanan & Kiri)',
                                            'Menyala normal dan tidak pecah',
                                        ) !!}
                                        {!! renderP2hChecklistItem(
                                            'baut_bearing_molen',
                                            'Check Baut Bearing Molen & Gandengan',
                                            'Baut terpasang utuh dan kencang',
                                        ) !!}
                                        {!! renderP2hChecklistItem('baut_hanger_as', 'Check Baut Hanger As Roda', 'Baut terpasang utuh dan kencang') !!}
                                        {!! renderP2hChecklistItem('baut_grease', 'Check Kondisi Baut Grease', 'Baut tidak aus dan terlumasi grease') !!}
                                        {!! renderP2hChecklistItem('katup_pembuangan_angin', 'Check Katup Pembuangan Angin', 'Berfungsi dengan baik') !!}
                                    </div>
                                </div>
                            </div>

                            {{-- CHECKLIST GENSET --}}
                            <div class="card shadow-sm category-card mt-4 mb-4" id="cardGensetChecklist"
                                style="border-left-color: #ffc107; display: none;">
                                <div class="card-header bg-soft-warning border-0">
                                    <h6 class="card-title text-warning fw-bold mb-0">
                                        <i class="ri-plug-line me-2"></i> Checklist P2H Genset
                                    </h6>
                                </div>
                                <div class="card-body py-3">
                                    <div class="row g-3">
                                        {!! renderP2hChecklistItem('level_oli_mesin', 'Level Oli Mesin', 'Berada dilevel Max') !!}
                                        {!! renderP2hChecklistItem('kebocoran_oli_mesin', 'Kebocoran Oli Mesin', 'Tidak ada kebocoran') !!}
                                        {!! renderP2hChecklistItem('level_coolant_radiator', 'Level Coolant/Radiator', 'Berada dilevel Max') !!}
                                        {!! renderP2hChecklistItem('kebocoran_coolant', 'Kebocoran Coolant', 'Tidak ada kebocoran') !!}
                                        {!! renderP2hChecklistItem('level_bahan_bakar', 'Level Bahan Bakar', 'Berada dilevel Max') !!}
                                        {!! renderP2hChecklistItem('kebocoran_bahan_bakar', 'Kebocoran Bahan Bakar', 'Tidak ada kebocoran') !!}
                                        {!! renderP2hChecklistItem('kondisi_aki_baterai', 'Kondisi Aki/Baterai', 'Terminal bersih , tidak korosi') !!}
                                        {!! renderP2hChecklistItem('tegangan_baterai', 'Tegangan Baterai', 'Normal') !!}
                                        {!! renderP2hChecklistItem('filter_udara', 'Filter Udara', 'Bersih') !!}
                                        {!! renderP2hChecklistItem('kondisi_panel_genset', 'Kondisi Panel Genset', 'Bersih, Indicator Normal') !!}
                                        {!! renderP2hChecklistItem('emergency_stop', 'Emergency Stop', 'Tidak ada alarm') !!}
                                        {!! renderP2hChecklistItem('suara_mesin_running', 'Suara Mesin Saat Running', 'Halus , Tidak Kasar') !!}
                                        {!! renderP2hChecklistItem('kebersihan_area_genset', 'Kebersihan Area Genset', 'Bersih') !!}
                                        {!! renderP2hChecklistItem('kondisi_knalpot_exhaust', 'Kondisi Knalpot/Exhaust', 'Tidak bocor') !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4 mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Catatan</label>
                                    <textarea class="form-control" name="catatan" rows="3" placeholder="Masukkan catatan jika diperlukan..."></textarea>
                                </div>
                            </div>

                            <div id="clientError" class="alert alert-danger d-none py-2"></div>

                            <div class="d-flex gap-2 justify-content-end mb-4">
                                <button type="button" id="btnResetKondisi" class="btn btn-outline-danger">Reset</button>
                                <button type="submit" id="btn-submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div> <!-- End of form-fields-wrapper -->
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CHOOSE APPROVER --}}
    <div class="modal fade" id="modalApprover" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Approver</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Staff Engineering</label>
                        <select class="form-select" id="staffDropdown" required>
                            <option value="">Pilih staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User MT/MTC</label>
                        <select class="form-select" id="userDropdown" required>
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

    {{-- MODAL SIGNATURE --}}
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
                    <button type="button" class="btn btn-primary" id="btnSaveTtd">Simpan & Kirim</button>
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

            let pendingFormData = null;

            // Keep a copy of all original options in mesin_id
            const allMesinOptions = [];
            $('#mesin_id option').each(function() {
                const $opt = $(this);
                allMesinOptions.push({
                    value: $opt.val(),
                    text: $opt.text(),
                    jenis: $opt.data('jenis') || '',
                    departemen: $opt.data('departemen') || '',
                    namaMesin: $opt.data('nama-mesin') || '',
                    disabled: $opt.prop('disabled') || false,
                    selected: $opt.prop('selected') || false
                });
            });

            // Handle card selection
            $('.card-type').on('click', function() {
                const selectedType = $(this).data('value');

                $('.card-type').removeClass('active');
                $(this).addClass('active');

                // Show the form wrapper
                $('#form-fields-wrapper').slideDown();

                // Re-populate and filter the select
                const $select = $('#mesin_id');
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.empty();

                // Add default placeholder option
                const placeholderOpt = allMesinOptions.find(o => o.value === '');
                if (placeholderOpt) {
                    $select.append(
                        $('<option></option>')
                        .val('')
                        .text(placeholderOpt.text)
                        .prop('disabled', true)
                        .prop('selected', true)
                    );
                }

                // Add filtered options
                allMesinOptions.forEach(opt => {
                    if (opt.value !== '' && opt.jenis === selectedType) {
                        const $newOpt = $('<option></option>')
                            .val(opt.value)
                            .text(opt.text)
                            .attr('data-jenis', opt.jenis)
                            .attr('data-departemen', opt.departemen)
                            .attr('data-nama-mesin', opt.namaMesin);
                        $select.append($newOpt);
                    }
                });

                // Re-initialize select2
                $select.select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Cari nama mesin / lokasi...',
                    allowClear: true,
                    width: '100%',
                    templateResult: function(data) {
                        if (!data.id) return data.text;
                        const parts = data.text.split(' - ');
                        return $('<span><b>' + parts[0] + '</b><br><small>' + (parts[1] || '') +
                            '</small></span>');
                    }
                });

                // Trigger change to reset checklist
                $select.val('').trigger('change');
            });

            $('#mesin_id').on('change', function() {
                const selected = $(this).find(':selected');
                const departemen = selected.data('departemen') || '';
                const jenis = selected.data('jenis') || '';
                const namaMesin = (selected.data('nama-mesin') || '').toUpperCase();

                $('#departemen').val(departemen);

                // Reset checklists
                $('#cardElectricChecklist, #cardDieselChecklist, #cardGensetChecklist').hide();
                $('.radio-checklist[value=""]').prop('checked', true).trigger('change');
                
                // Hide hours meter by default
                $('#hours_meter_wrapper').hide();
                $('#hours_meter').val('').removeAttr('required');

                if (jenis === 'Electric P2H') {
                    $('#cardElectricChecklist').show();
                    filterElectricChecklist(namaMesin);
                } else if (jenis === 'Diesel P2H') {
                    $('#cardDieselChecklist').show();
                    $('#hours_meter_wrapper').show();
                    // Hours meter is always required for Diesel P2H
                    $('#hours_meter').attr('required', true);
                } else if (jenis === 'Genset P2H') {
                    $('#cardGensetChecklist').show();
                    // Genset does not have hours meter
                }
            });

            // Toggle description input on radio selection
            $(document).on('change', '.radio-checklist', function() {
                const name = $(this).attr('name');
                const val = $(this).val();
                const $row = $(this).closest('.item-row');
                const $wrapper = $row.find('.keterangan-wrapper');
                const $input = $wrapper.find('input');

                if (val === '0') { // NOK
                    $row.addClass('not-ok');
                    $wrapper.removeClass('d-none');
                    $input.attr('required', true);
                } else {
                    $row.removeClass('not-ok');
                    $wrapper.addClass('d-none');
                    $input.val('').removeAttr('required');
                }
            });

            function filterElectricChecklist(namaMesin) {
                const isForklift = namaMesin.includes('FORKLIFT');
                const isPM = namaMesin.includes('PALLET MOVER') || namaMesin.includes('PM');
                const isES = namaMesin.includes('STACKER') || namaMesin.includes('STEKER') || namaMesin.includes(
                    'ES');

                let activeType = '';
                if (isForklift) activeType = 'forklift';
                else if (isPM) activeType = 'pm';
                else if (isES) activeType = 'es';

                // Toggle Hours meter requirement based on active machine type
                if (activeType === 'forklift' || activeType === 'pm' || activeType === 'es') {
                    $('#hours_meter_wrapper').show();
                    $('#hours_meter').attr('required', true);
                } else {
                    $('#hours_meter_wrapper').hide();
                    $('#hours_meter').val('').removeAttr('required');
                }

                $('#cardElectricChecklist .item-row').each(function() {
                    const $row = $(this);
                    const type = $row.data('type'); // forklift, forklift_es, all
                    const $container = $row.closest('.checklist-col');

                    let visible = false;
                    if (!activeType) {
                        visible = true;
                    } else if (type === 'all') {
                        visible = true;
                    } else if (activeType === 'forklift') {
                        visible = true;
                    } else if (activeType === 'es' && type === 'forklift_es') {
                        visible = true;
                    }

                    if (visible) {
                        $container.removeClass('d-none');
                    } else {
                        $container.addClass('d-none');
                        // Reset items that are hidden
                        $row.find('input[type="radio"]').prop('checked', false);
                        $row.find('.keterangan-wrapper').addClass('d-none');
                        $row.find('input[type="text"]').val('').removeAttr('required');
                        $row.removeClass('not-ok');
                    }
                });
            }

            function collectNotOkDetails() {
                const details = [];
                const selectedJenis = $('#mesin_id').find(':selected').data('jenis');
                let targetCard = '';
                if (selectedJenis === 'Electric P2H') {
                    targetCard = '#cardElectricChecklist';
                } else if (selectedJenis === 'Diesel P2H') {
                    targetCard = '#cardDieselChecklist';
                } else if (selectedJenis === 'Genset P2H') {
                    targetCard = '#cardGensetChecklist';
                }

                if (targetCard) {
                    $(targetCard + ' .item-row:visible').each(function() {
                        const $row = $(this);
                        const isNg = $row.find('input[value="0"]').is(':checked');
                        if (!isNg) return;

                        const label = $row.find('.fw-semibold').text().trim();
                        const keterangan = $row.find('.keterangan-wrapper input').val().trim();

                        if (keterangan) {
                            details.push(`${label}: ${keterangan}`);
                        }
                    });
                }

                return details.join(" | ");
            }

            let selectedStaff = null;
            let selectedUser = null;

            $('#form-mtc-p2h').on('submit', function(e) {
                e.preventDefault();

                // Client side check: ensure at least one checklist option is chosen
                const selectedJenis = $('#mesin_id').find(':selected').data('jenis');
                let targetCard = '';
                if (selectedJenis === 'Electric P2H') {
                    targetCard = '#cardElectricChecklist';
                } else if (selectedJenis === 'Diesel P2H') {
                    targetCard = '#cardDieselChecklist';
                } else if (selectedJenis === 'Genset P2H') {
                    targetCard = '#cardGensetChecklist';
                }

                let hasValue = false;
                if (targetCard) {
                    $(targetCard + ' .item-row:visible').each(function() {
                        const checkedVal = $(this).find('input[type="radio"]:checked').val();
                        if (checkedVal !== '') {
                            hasValue = true;
                            return false; // Break loop
                        }
                    });
                }

                if (!hasValue) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Minimal harus ada 1 checklist (OK / NOK) yang diisi sebelum submit.'
                    });
                    return;
                }

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
                            `<option value="${u.id}">${u.username}</option>`);
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
                    url: "{{ route('p2h.form.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        let successHtml = response.message;
                        if (response.persentase > 0) {
                            successHtml +=
                                `<br><strong style="font-size: 1.2rem; color: #198754;">Nilai Persentase: ${response.persentase}%</strong>`;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            html: successHtml,
                            timer: 4000,
                            showConfirmButton: true
                        }).then(() => {
                            resetForm();
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
                    resetForm();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Checklist telah direset',
                        timer: 1500,
                        showConfirmButton: false
                    });
                });
            });

            function resetForm() {
                const $form = $('#form-mtc-p2h');
                $form[0].reset();

                $form.find('select').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).val(null).trigger('change');
                    }
                });

                $('.radio-checklist[value=""]').prop('checked', true).trigger('change');
                $('.item-row').removeClass('not-ok');
                $('.keterangan-wrapper').addClass('d-none').find('input').val('').removeAttr('required');

                $('#cardElectricChecklist, #cardDieselChecklist, #cardGensetChecklist').hide();
                $('#no_unit_text_wrapper').hide();
                $('#no_unit').val('').removeAttr('required');
                $('#hours_meter_wrapper').show();
                $('#hours_meter').val('').removeAttr('required');

                // Reset cards
                $('.card-type').removeClass('active');
                $('#form-fields-wrapper').hide();

                $('#clientError').addClass('d-none').text('');
            }
        });
    </script>
@endsection
