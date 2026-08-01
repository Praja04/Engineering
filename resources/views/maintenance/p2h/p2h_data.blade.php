@extends('layouts.app')

@section('title', 'Data Check Mtc P2H')

@section('styles')
    <style>
        .card-soft {
            border: 1px solid #eee;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .small-muted {
            color: #6c757d;
            font-size: .85rem;
        }

        .group-title {
            font-weight: 700;
            color: #0d6efd;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
            margin: 14px 0 10px;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 20px;
        }

        @media (max-width: 992px) {
            .items-grid {
                grid-template-columns: 1fr;
            }
        }

        .item-cell {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 6px 0;
            border-bottom: 1px dashed #eee;
        }

        .item-cell:last-child {
            border-bottom: none;
        }

        .item-cell .item-label {
            font-size: .95rem;
            line-height: 1.4;
        }

        .status-3 .btn {
            white-space: nowrap;
            font-size: .85rem;
            padding: .4rem .6rem;
        }

        .item-edit {
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .item-edit .item-label {
            font-weight: 600;
            font-size: .95rem;
        }

        .checklist-edit-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="card card-soft shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="fw-bold">Data Maintenance P2H (Electric & Diesel)</h4>
                        <div class="small-muted">List inspeksi + detail hasil pengecekan P2H</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('p2h.form.index') }}" class="btn btn-primary">
                            + Input Baru
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- FILTER --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="filterDate">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nama Unit / Mesin</label>
                            <input type="text" class="form-control" id="filterNoUnit" placeholder="Contoh: FORKLIFT">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Departemen</label>
                            <input type="text" class="form-control" id="filterDepartemen"
                                placeholder="Contoh: Warehouse">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label d-block">&nbsp;</label>
                            <div class="d-flex gap-2 text-nowrap">
                                <button type="button" class="btn btn-outline-primary w-100" id="btnApply">
                                    <i class="mdi mdi-filter me-2"></i> Terapkan</button>
                                <button type="button" class="btn btn-outline-secondary w-100" id="btnReset">
                                    <i class="mdi mdi-restart"></i> Reset</button>
                            </div>
                        </div>
                    </div>

                    {{-- TABLE --}}
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tabelP2h" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis P2H</th>
                                    <th>Unit/Mesin</th>
                                    <th>No Unit (Code)</th>
                                    <th>Departemen</th>
                                    <th>Shift</th>
                                    <th>Hours Meter</th>
                                    <th>Score (%)</th>
                                    <th>Status</th>
                                    <th style="width:140px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyP2h">
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <div id="paginationInfo" class="small text-muted"></div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0" id="paginationList">
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <div class="fw-bold" id="detailTitle">Detail Inspeksi P2H</div>
                        <div class="small-muted" id="detailSub"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailBody">
                    {{-- Injected dynamically --}}
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="formEditP2h">
                    @csrf
                    <input type="hidden" id="editId" name="id">

                    <div class="modal-header">
                        <div>
                            <div class="fw-bold">Edit Inspeksi Mtc P2H</div>
                            <div class="text-muted small" id="editSub"></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal *</label>
                                <input type="date" class="form-control" name="tanggal" id="editTanggal" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Departemen</label>
                                <input type="text" class="form-control" name="departemen" id="editDepartemen" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Unit/Mesin</label>
                                <input type="text" class="form-control" id="editMesinName" readonly>
                            </div>
                            <div class="col-md-3" id="editNoUnitWrapper">
                                <label class="form-label">No Unit / Code</label>
                                <input type="text" class="form-control" name="no_unit" id="editNoUnit">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="waktu_mulai" id="editWaktuMulai"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="waktu_selesai" id="editWaktuSelesai"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-select" id="editShift" required>
                                    <option value="1">Shift 1</option>
                                    <option value="2">Shift 2</option>
                                    <option value="3">Shift 3</option>
                                </select>
                            </div>
                            <div class="col-md-3" id="editHourMeterWrapper">
                                <label class="form-label">Hours Meter (Jam Operasional) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="hours_meter" id="editHourMeter"
                                    required>
                            </div>
                        </div>

                        <div class="card shadow-sm category-card mt-4 mb-3">
                            <div class="card-header bg-soft-primary border-0">
                                <h6 class="card-title text-primary fw-bold mb-0">Checklist Items</h6>
                            </div>
                            <div class="card-body py-2" id="editChecklistContainer">
                                {{-- Checklist rows will be dynamically injected --}}
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-12">
                                <label class="form-label">Catatan</label>
                                <textarea class="form-control" name="catatan" id="editCatatan" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveEdit">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="spinEdit"></span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL TRACKING --}}
    <div class="modal fade" id="modalTracking" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="trackingTitle">Tracking Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="trackingBody">
                    {{-- Injected dynamically --}}
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const API_URL = "{{ url('api/mtc/p2h/get-data') }}";
            const DELETE_URL = "{{ url('mtc/main/delete') }}";
            const UPDATE_URL = "{{ url('mtc/p2h/data/update') }}";

            let currentRows = [];

            const electricP2h = {
                level_minyak_rem: {
                    label: 'Check Level Minyak Rem',
                    standar: 'Berada di level max',
                    type: 'forklift'
                },
                level_oli_hydraulic: {
                    label: 'Check Level Oli Hydraulic',
                    standar: 'Berada di level max',
                    type: 'forklift'
                },
                isi_air_aki: {
                    label: 'Check Isi Air Aki',
                    standar: 'Berada di level standar',
                    type: 'all'
                },
                baterai: {
                    label: 'Check Baterai',
                    standar: 'Tidak kurang dari 30%',
                    type: 'all'
                },
                hydraulic_system: {
                    label: 'Hydraulic System',
                    standar: 'Berfungsi dengan baik and terlubrikasi',
                    type: 'all'
                },
                selang_hydraulic: {
                    label: 'Selang Hydraulic',
                    standar: 'Tidak ada kebocoran oli',
                    type: 'forklift'
                },
                lift_chains: {
                    label: 'Lift Chains',
                    standar: 'Kekencangan kanan dan kiri sama serta terlubrikasi',
                    type: 'forklift_es'
                },
                fork: {
                    label: 'Pengecekan Fork',
                    standar: 'Tidak bengkok dan tidak patah',
                    type: 'all'
                },
                body_unit: {
                    label: 'Check Body Unit',
                    standar: 'Tidak lecet dan tidak penyok',
                    type: 'all'
                },
                lampu_kombinasi_kiri: {
                    label: 'Check Lampu Kombinasi Kiri',
                    standar: 'Menyala normal dan tidak pecah',
                    type: 'forklift'
                },
                lampu_kombinasi_kanan: {
                    label: 'Check Lampu Kombinasi Kanan',
                    standar: 'Menyala normal dan tidak pecah',
                    type: 'forklift'
                },
                lampu_sorot: {
                    label: 'Check Lampu Sorot / Head Lamp',
                    standar: 'Menyala normal dan tidak pecah',
                    type: 'forklift'
                },
                lampu_sign_depan_kanan: {
                    label: 'Check Lampu Sign Depan Kanan',
                    standar: 'Menyala normal dan tidak pecah',
                    type: 'forklift'
                },
                lampu_sign_depan_kiri: {
                    label: 'Check Lampu Sign Depan Kiri',
                    standar: 'Menyala normal dan tidak pecah',
                    type: 'forklift'
                },
                klakson: {
                    label: 'Check Klakson / Horn',
                    standar: 'Bunyi saat tombol ditekan',
                    type: 'all'
                },
                buzzer_back: {
                    label: 'Check Buzzer Back',
                    standar: 'Berbunyi normal saat maju dan mundur',
                    type: 'forklift'
                },
                kaca_spion: {
                    label: 'Check Kaca Spion',
                    standar: 'Terpasang dengan baik dan tidak pecah',
                    type: 'forklift'
                },
                baut_roda: {
                    label: 'Check Kekencangan Baut Roda',
                    standar: 'Kencang dan tidak patah',
                    type: 'all'
                },
                ban: {
                    label: 'Check Ban',
                    standar: 'Masih bagus dan layak pakai',
                    type: 'all'
                },
                kebersihan_unit: {
                    label: 'Check Kebersihan Unit',
                    standar: 'Bersih dari kotoran dan debu',
                    type: 'all'
                },
                panel_display: {
                    label: 'Check Panel Display',
                    standar: 'Berfungsi normal, tidak pecah, dan tidak ada alarm',
                    type: 'all'
                },
                sistem_kemudi: {
                    label: 'Sistem Kemudi',
                    standar: 'Tidak berat dan bergerak lancar',
                    type: 'all'
                }
            };

            const dieselP2h = {
                klakson: {
                    label: 'Check Klakson',
                    standar: 'Bunyi ketika tombol ditekan'
                },
                buzzer_back: {
                    label: 'Check Buzzer Back',
                    standar: 'Berbunyi normal saat maju dan mundur'
                },
                oli_mesin: {
                    label: 'Check Kondisi & Level Oli Mesin',
                    standar: 'Berada di level max dan tidak ada kebocoran'
                },
                radiator_hose: {
                    label: 'Check Kondisi Level Radiator & Hose',
                    standar: 'Berada di level max dan tidak ada kebocoran'
                },
                water_pump: {
                    label: 'Check Water Pump',
                    standar: 'Tidak ada kebocoran'
                },
                injection_system: {
                    label: 'Check Injection Pump, Injector & Piping',
                    standar: 'Tidak ada kebocoran'
                },
                fan_vbelt: {
                    label: 'Check Fan & V-Belt',
                    standar: 'Berfungsi baik dan V-belt tidak retak atau putus'
                },
                turbocharger_manifold: {
                    label: 'Check Turbocharger & Manifold',
                    standar: 'Berfungsi baik dan terlubrikasi'
                },
                tensioner_belt: {
                    label: 'Check Automatic Tensioner Belt',
                    standar: 'Berfungsi dengan baik'
                },
                starting_motor: {
                    label: 'Check Fungsi Starting Motor',
                    standar: 'Berfungsi dengan baik'
                },
                alternator: {
                    label: 'Check Fungsi Alternator',
                    standar: 'Berfungsi dengan baik'
                },
                control_display: {
                    label: 'Check Control Display',
                    standar: 'Berfungsi normal, tidak pecah, dan tidak ada alarm'
                },
                oli_transmisi: {
                    label: 'Check Kondisi & Level Oli Transmisi',
                    standar: 'Berada di level max dan tidak ada kebocoran'
                },
                aki: {
                    label: 'Check Kondisi Aki & Level Air Aki',
                    standar: 'Level max, aki tidak drop, dan bersih'
                },
                engine_mounting: {
                    label: 'Check Engine Mounting',
                    standar: 'Berfungsi dengan baik'
                },
                filter_oli_transmisi: {
                    label: 'Check Filter Oli Transmisi',
                    standar: 'Tidak ada kebocoran oli'
                },
                fungsi_rem: {
                    label: 'Check Fungsi Rem',
                    standar: 'Berfungsi dengan baik dan tidak blong'
                },
                fungsi_kopling: {
                    label: 'Check Fungsi Kopling',
                    standar: 'Berfungsi dengan baik dan tidak macet'
                },
                oli_hydraulic: {
                    label: 'Check Kondisi & Level Oli Hydraulic',
                    standar: 'Berada di level max dan tidak ada kebocoran'
                },
                hydraulic_system: {
                    label: 'Check Fungsi Hydraulic System',
                    standar: 'Berfungsi dengan baik dan terlubrikasi'
                },
                steering_system: {
                    label: 'Check Fungsi Steering System',
                    standar: 'Tidak berat dan bergerak lancar'
                },
                body_back_rest: {
                    label: 'Check Kondisi Back Rest & Body',
                    standar: 'Tidak ada cacat atau penyok'
                },
                kaca_spion: {
                    label: 'Check Kaca Spion',
                    standar: 'Terpasang lengkap dan tidak pecah'
                },
                bucket_pin: {
                    label: 'Check Kondisi Bucket & Pin Bucket',
                    standar: 'Berfungsi baik dan tidak retak atau hilang'
                },
                dump_pin_bushing: {
                    label: 'Check Kondisi Dump, Pin & Bushing',
                    standar: 'Berfungsi dan tidak retak atau hilang'
                },
                seal_hydraulic: {
                    label: 'Check Kondisi Seal Hydraulic',
                    standar: 'Tidak ada kebocoran oli'
                },
                roda_ban_baut: {
                    label: 'Check Kondisi Roda, Ban & Baut',
                    standar: 'Ban layak pakai dan baut terpasang kencang'
                },
                lampu_unit: {
                    label: 'Check Lampu Depan & Belakang (Kanan & Kiri)',
                    standar: 'Menyala normal dan tidak pecah'
                },
                baut_bearing_molen: {
                    label: 'Check Baut Bearing Molen & Gandengan',
                    standar: 'Baut terpasang utuh dan kencang'
                },
                baut_hanger_as: {
                    label: 'Check Baut Hanger As Roda',
                    standar: 'Baut terpasang utuh dan kencang'
                },
                baut_grease: {
                    label: 'Check Kondisi Baut Grease',
                    standar: 'Baut tidak aus dan terlumasi grease'
                },
                katup_pembuangan_angin: {
                    label: 'Check Katup Pembuangan Angin',
                    standar: 'Berfungsi dengan baik'
                }
            };

            const gensetP2h = {
                level_oli_mesin: {
                    label: 'Level Oli Mesin',
                    standar: 'Berada dilevel Max'
                },
                kebocoran_oli_mesin: {
                    label: 'Kebocoran Oli Mesin',
                    standar: 'Tidak ada kebocoran'
                },
                level_coolant_radiator: {
                    label: 'Level Coolant/Radiator',
                    standar: 'Berada dilevel Max'
                },
                kebocoran_coolant: {
                    label: 'Kebocoran Coolant',
                    standar: 'Tidak ada kebocoran'
                },
                level_bahan_bakar: {
                    label: 'Level Bahan Bakar',
                    standar: 'Berada dilevel Max'
                },
                kebocoran_bahan_bakar: {
                    label: 'Kebocoran Bahan Bakar',
                    standar: 'Tidak ada kebocoran'
                },
                kondisi_aki_baterai: {
                    label: 'Kondisi Aki/Baterai',
                    standar: 'Terminal bersih , tidak korosi'
                },
                tegangan_baterai: {
                    label: 'Tegangan Baterai',
                    standar: 'Normal'
                },
                filter_udara: {
                    label: 'Filter Udara',
                    standar: 'Bersih'
                },
                kondisi_panel_genset: {
                    label: 'Kondisi Panel Genset',
                    standar: 'Bersih, Indicator Normal'
                },
                emergency_stop: {
                    label: 'Emergency Stop',
                    standar: 'Tidak ada alarm'
                },
                suara_mesin_running: {
                    label: 'Suara Mesin Saat Running',
                    standar: 'Halus , Tidak Kasar'
                },
                kebersihan_area_genset: {
                    label: 'Kebersihan Area Genset',
                    standar: 'Bersih'
                },
                kondisi_knalpot_exhaust: {
                    label: 'Kondisi Knalpot/Exhaust',
                    standar: 'Tidak bocor'
                }
            };

            function fmtDate(iso) {
                if (!iso) return '-';
                const d = new Date(iso);
                if (isNaN(d.getTime())) return iso;
                return d.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            function statusBadge(val) {
                if (val === true || val === 1 || val === "1") return `<span class="badge bg-success">OK</span>`;
                if (val === false || val === 0 || val === "0") return `<span class="badge bg-danger">NOK</span>`;
                return `<span class="badge bg-secondary">Kosong</span>`;
            }

            function approvalBadge(status, id) {
                let badgeClass = 'bg-secondary';
                let label = status;
                if (status === 'pending') {
                    badgeClass = 'bg-warning text-dark';
                    label = 'Pending';
                } else if (status === 'waiting') {
                    badgeClass = 'bg-info text-white';
                    label = 'Waiting';
                } else if (status === 'approved') {
                    badgeClass = 'bg-success text-white';
                    label = 'Approved';
                } else if (status === 'rejected') {
                    badgeClass = 'bg-danger text-white';
                    label = 'Rejected';
                }
                return `<span class="badge ${badgeClass} btn-tracking" data-id="${id}" style="cursor: pointer;" title="Klik untuk melacak approval">${label}</span>`;
            }

            function buildDetailHTML(row) {
                const isElectric = row.jenis_mtc === 'Electric P2H' || row.jenis_mtc === 'Electrical P2H';
                const isDiesel = row.jenis_mtc === 'Diesel P2H';
                const isGenset = row.jenis_mtc === 'Genset P2H';

                let detailsData = null;
                let metadata = null;

                if (isElectric) {
                    detailsData = row.electric_p2h;
                    metadata = electricP2h;
                } else if (isDiesel) {
                    detailsData = row.diesel_p2h;
                    metadata = dieselP2h;
                } else if (isGenset) {
                    detailsData = row.genset_p2h;
                    metadata = gensetP2h;
                }

                const namaMesin = (detailsData?.mesin?.nama_mesin || '').toUpperCase();

                let activeType = '';
                if (isElectric) {
                    if (namaMesin.includes('FORKLIFT')) activeType = 'forklift';
                    else if (namaMesin.includes('PALLET MOVER') || namaMesin.includes('PM')) activeType = 'pm';
                    else if (namaMesin.includes('STACKER') || namaMesin.includes('STEKER') || namaMesin.includes(
                            'ES')) activeType = 'es';
                }

                // Filter visible electric fields
                const visibleMetadata = {};
                Object.entries(metadata).forEach(([key, item]) => {
                    let visible = true;
                    if (isElectric && activeType) {
                        if (item.type !== 'all' && activeType !== 'forklift') {
                            if (activeType === 'es' && item.type !== 'forklift_es') {
                                visible = false;
                            } else if (activeType === 'pm') {
                                visible = false;
                            }
                        }
                    }
                    if (visible) {
                        visibleMetadata[key] = item;
                    }
                });

                const cells = Object.entries(visibleMetadata).map(([key, item]) => `
                    <div class="item-cell">
                        <div class="item-info">
                            <div class="item-label fw-semibold">${item.label}</div>
                            <div class="text-muted small">${item.standar ?? ''}</div>
                        </div>
                        <div class="item-status">${statusBadge(detailsData?.[key])}</div>
                    </div>
                `).join('');

                return `
                    <div class="detail-meta row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="text-muted small">Tanggal</div>
                            <div class="fw-bold">${fmtDate(row.tanggal)}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Waktu</div>
                            <div class="fw-bold">${row.waktu_mulai ?? '-'} s/d ${row.waktu_selesai ?? '-'}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Departemen</div>
                            <div class="fw-bold">${row.departemen ?? '-'}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Unit/Mesin</div>
                            <div class="fw-bold">${namaMesin || detailsData?.no_unit || '-'}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">No Unit (Code)</div>
                            <div class="fw-bold">${isElectric ? '-' : (detailsData?.no_unit || '-')}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Shift</div>
                            <div class="fw-bold">Shift ${detailsData?.shift ?? '-'}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Hours Meter</div>
                            <div class="fw-bold">${detailsData?.hours_meter ?? '-'}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Dibuat Oleh</div>
                            <div class="fw-bold">${row.created_by?.username ?? '-'}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Score Persentase</div>
                            <div class="fw-bold text-primary">${isElectric && detailsData?.persentase ? detailsData.persentase + '%' : '-'}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="group-title mb-3">Hasil Checklist</div>
                        <div class="items-grid">${cells}</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="group-title">Keterangan Kerusakan (NOK)</div>
                            <div class="border p-2 rounded bg-light">${row.keterangan || '-'}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="group-title">Catatan</div>
                            <div class="border p-2 rounded bg-light">${detailsData?.catatan || '-'}</div>
                        </div>
                    </div>
                `;
            }

            let currentPage = 1;
            const pageSize = 10;
            let totalRecords = 0;

            function loadTableData(page = 1) {
                currentPage = page;
                const start = (currentPage - 1) * pageSize;

                $('#tbodyP2h').html(`
                    <tr>
                        <td colspan="11" class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="ms-2 text-muted">Memuat data...</span>
                        </td>
                    </tr>
                `);

                const filters = {
                    date: $('#filterDate').val() || null,
                    no_unit: $('#filterNoUnit').val() || null,
                    departemen: $('#filterDepartemen').val() || null,
                    start: start,
                    length: pageSize
                };

                $.ajax({
                    url: API_URL,
                    type: 'GET',
                    data: filters,
                    dataType: 'json',
                    success: function(res) {
                        currentRows = res.data || [];
                        totalRecords = res.recordsFiltered || 0;

                        if (currentRows.length === 0) {
                            $('#tbodyP2h').html(`
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">Tidak ada data ditemukan</td>
                                </tr>
                            `);
                            $('#paginationInfo').text('Menampilkan 0 sampai 0 dari 0 data');
                            $('#paginationList').empty();
                            return;
                        }

                        let html = '';
                        currentRows.forEach((row, index) => {
                            const rowNum = start + index + 1;
                            const isElectric = row.jenis_mtc === 'Electric P2H' || row.jenis_mtc === 'Electrical P2H';
                            const isDiesel = row.jenis_mtc === 'Diesel P2H';
                            const isGenset = row.jenis_mtc === 'Genset P2H';

                            let typeBadge = '';
                            if (isElectric) {
                                typeBadge = `<span class="badge bg-soft-primary text-primary">Electric</span>`;
                            } else if (isDiesel) {
                                typeBadge = `<span class="badge bg-soft-danger text-danger">Diesel</span>`;
                            } else if (isGenset) {
                                typeBadge = `<span class="badge bg-soft-warning text-warning">Genset</span>`;
                            } else {
                                typeBadge = `<span class="badge bg-soft-secondary text-secondary">${row.jenis_mtc}</span>`;
                            }

                            let machineName = '-';
                            let unitCode = '-';
                            let shiftVal = '-';
                            let hoursVal = '-';
                            let percentageVal = '-';

                            if (isElectric) {
                                machineName = row.electric_p2h?.mesin?.nama_mesin || '-';
                                shiftVal = row.electric_p2h?.shift || '-';
                                hoursVal = row.electric_p2h?.hours_meter || '-';
                                percentageVal = row.electric_p2h?.persentase ? `<b>${row.electric_p2h.persentase}%</b>` : '-';
                            } else if (isDiesel) {
                                machineName = row.diesel_p2h?.mesin?.nama_mesin || '-';
                                unitCode = row.diesel_p2h?.no_unit || '-';
                                shiftVal = row.diesel_p2h?.shift || '-';
                                hoursVal = row.diesel_p2h?.hours_meter || '-';
                            } else if (isGenset) {
                                machineName = row.genset_p2h?.mesin?.nama_mesin || '-';
                                unitCode = row.genset_p2h?.no_unit || '-';
                                shiftVal = row.genset_p2h?.shift || '-';
                                hoursVal = row.genset_p2h?.hours_meter || '-';
                            }

                            const showBtn = `<button class="btn btn-sm btn-info btn-detail me-1" data-id="${row.id}" title="Lihat Detail"><i class="mdi mdi-eye"></i></button>`;
                            let editBtn = '';
                            let delBtn = '';

                            if (row.status !== 'approved') {
                                editBtn = `<button class="btn btn-sm btn-primary btn-edit me-1" data-id="${row.id}" title="Edit"><i class="mdi mdi-pencil"></i></button>`;
                                delBtn = `<button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" title="Hapus"><i class="mdi mdi-trash-can"></i></button>`;
                            }

                            html += `
                                <tr>
                                    <td class="text-center">${rowNum}</td>
                                    <td>${fmtDate(row.tanggal)}</td>
                                    <td>${typeBadge}</td>
                                    <td>${machineName}</td>
                                    <td>${unitCode}</td>
                                    <td>${row.departemen ?? '-'}</td>
                                    <td class="text-center">${shiftVal}</td>
                                    <td>${hoursVal}</td>
                                    <td>${percentageVal}</td>
                                    <td>${approvalBadge(row.status, row.id)}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            ${showBtn}
                                            ${editBtn}
                                            ${delBtn}
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });

                        $('#tbodyP2h').html(html);

                        // Render Pagination
                        const totalPages = Math.ceil(totalRecords / pageSize);
                        const endRow = Math.min(start + pageSize, totalRecords);
                        $('#paginationInfo').text(`Menampilkan ${start + 1} sampai ${endRow} dari ${totalRecords} data`);

                        let pagHtml = '';
                        // Previous button
                        pagHtml += `
                            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                <a class="page-link" href="#" data-page="${currentPage - 1}">Sebelumnya</a>
                            </li>
                        `;

                        for (let p = 1; p <= totalPages; p++) {
                            pagHtml += `
                                <li class="page-item ${currentPage === p ? 'active' : ''}">
                                    <a class="page-link" href="#" data-page="${p}">${p}</a>
                                </li>
                            `;
                        }

                        // Next button
                        pagHtml += `
                            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                <a class="page-link" href="#" data-page="${currentPage + 1}">Berikutnya</a>
                            </li>
                        `;

                        $('#paginationList').html(pagHtml);
                    },
                    error: function() {
                        $('#tbodyP2h').html(`
                            <tr>
                                <td colspan="11" class="text-center text-danger py-4">Gagal memuat data</td>
                            </tr>
                        `);
                    }
                });
            }

            // Init load
            loadTableData(1);

            // Handle Pagination click
            $(document).on('click', '#paginationList .page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page && page !== currentPage) {
                    loadTableData(page);
                }
            });

            $('#btnApply').on('click', function() {
                loadTableData(1);
            });

            $('#btnReset').on('click', function() {
                $('#filterDate').val('');
                $('#filterNoUnit').val('');
                $('#filterDepartemen').val('');
                loadTableData(1);
            });

            // DETAIL MODAL
            $(document).on('click', '.btn-detail', function() {
                const id = $(this).data('id');
                const row = currentRows.find(r => r.id == id);
                if (!row) return;

                $('#detailTitle').text(`Detail P2H - ${row.jenis_mtc}`);
                $('#detailSub').text(
                    `Tanggal: ${fmtDate(row.tanggal)} | Status: ${row.status.toUpperCase()}`);

                const html = buildDetailHTML(row);
                $('#detailBody').html(html);
                $('#modalDetail').modal('show');
            });

            // TRACKING MODAL
            $(document).on('click', '.btn-tracking', function() {
                const id = $(this).data('id');
                const row = currentRows.find(r => r.id == id);
                if (!row) return;

                $('#trackingTitle').text(`Tracking Approval - ${row.jenis_mtc}`);

                $.get(`/mtc/main/tracking/${id}`, function(res) {
                    if (res.status && res.data.length) {
                        let html = '<div class="vertical-timeline">';
                        res.data.forEach(item => {
                            let badge = '';
                            if (item.status === 'approved') badge = 'text-success';
                            else if (item.status === 'rejected') badge = 'text-danger';
                            else badge = 'text-warning';

                            html += `
                                    <div class="timeline-item pb-3 border-start ps-3 position-relative">
                                        <div class="fw-bold">${item.role} (${item.approver})</div>
                                        <div class="${badge} fw-semibold small">${item.status.toUpperCase()}</div>
                                        <div class="text-muted small">${item.action_at ? fmtDate(item.action_at) : 'Belum diproses'}</div>
                                        ${item.catatan ? `<div class="fst-italic text-muted small">Catatan: "${item.catatan}"</div>` : ''}
                                    </div>
                                `;
                        });
                        html += '</div>';
                        $('#trackingBody').html(html);
                    } else {
                        $('#trackingBody').html(
                            '<div class="text-center text-muted">Belum ada data tracking approval.</div>'
                        );
                    }
                    $('#modalTracking').modal('show');
                });
            });

            // DELETE ACTION
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33'
                }).then(res => {
                    if (!res.isConfirmed) return;
                    $.ajax({
                        url: `${DELETE_URL}/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(resp) {
                            Swal.fire('Berhasil', resp.message, 'success');
                            loadTableData(currentPage);
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal', xhr.responseJSON?.message ||
                                'Terjadi kesalahan', 'error');
                        }
                    });
                });
            });

            // EDIT MODAL & FORM INJECTION
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const row = currentRows.find(r => r.id == id);
                if (!row) return;

                const isElectric = row.jenis_mtc === 'Electric P2H' || row.jenis_mtc === 'Electrical P2H';
                const isDiesel = row.jenis_mtc === 'Diesel P2H';
                const isGenset = row.jenis_mtc === 'Genset P2H';

                let details = null;
                let metadata = null;
                if (isElectric) {
                    details = row.electric_p2h;
                    metadata = electricP2h;
                } else if (isDiesel) {
                    details = row.diesel_p2h;
                    metadata = dieselP2h;
                } else if (isGenset) {
                    details = row.genset_p2h;
                    metadata = gensetP2h;
                }

                // Parse existing keterangan into a key-value map
                const nokMap = {};
                if (row.keterangan) {
                    row.keterangan.split(' | ').forEach(part => {
                        const idx = part.indexOf(':');
                        if (idx !== -1) {
                            const label = part.substring(0, idx).trim();
                            const val = part.substring(idx + 1).trim();
                            nokMap[label] = val;
                        }
                    });
                }

                $('#editId').val(row.id);
                $('#editTanggal').val(row.tanggal.split('T')[0]);
                $('#editDepartemen').val(row.departemen);
                $('#editMesinName').val(details?.mesin?.nama_mesin || '-');
                $('#editWaktuMulai').val(row.waktu_mulai ? row.waktu_mulai.substring(0, 5) : '');
                $('#editWaktuSelesai').val(row.waktu_selesai ? row.waktu_selesai.substring(0, 5) : '');
                $('#editShift').val(details?.shift);
                $('#editHourMeter').val(details?.hours_meter);
                $('#editCatatan').val(details?.catatan);

                // Sub-filtering logic for Electric P2H checklist rows
                const namaMesin = (details?.mesin?.nama_mesin || '').toUpperCase();
                let activeType = '';
                if (isElectric) {
                    if (namaMesin.includes('FORKLIFT')) activeType = 'forklift';
                    else if (namaMesin.includes('PALLET MOVER') || namaMesin.includes('PM')) activeType =
                        'pm';
                    else if (namaMesin.includes('STACKER') || namaMesin.includes('STEKER') || namaMesin
                        .includes('ES')) activeType = 'es';
                }

                // no_unit is always shown and required for all types
                $('#editNoUnitWrapper').show();
                $('#editNoUnit').attr('required', true).val(details?.no_unit || '');

                if (isElectric) {
                    if (activeType === 'forklift' || activeType === 'pm' || activeType === 'es') {
                        $('#editHourMeterWrapper').show();
                        $('#editHourMeter').attr('required', true);
                    } else {
                        $('#editHourMeterWrapper').hide();
                        $('#editHourMeter').removeAttr('required').val('');
                    }
                } else if (isDiesel) {
                    $('#editHourMeterWrapper').show();
                    $('#editHourMeter').attr('required', true);
                } else if (isGenset) {
                    // Genset does not have hours meter
                    $('#editHourMeterWrapper').hide();
                    $('#editHourMeter').removeAttr('required').val('');
                }

                // Inject checklist items dynamically
                let html = '';
                Object.entries(metadata).forEach(([key, item]) => {
                    let visible = true;
                    if (isElectric && activeType) {
                        if (item.type !== 'all' && activeType !== 'forklift') {
                            if (activeType === 'es' && item.type !== 'forklift_es') {
                                visible = false;
                            } else if (activeType === 'pm') {
                                visible = false;
                            }
                        }
                    }

                    if (!visible) return;

                    const curVal = details?.[key];
                    const isOk = curVal == 1 || curVal === true;
                    const isNok = curVal == 0 || curVal === false;
                    const isEmpty = curVal === null || curVal === undefined || curVal === '';
                    
                    const nokValue = isNok ? (nokMap[item.label] || 'NOK') : '';

                    html += `
                            <div class="checklist-edit-row item-row pb-2 flex-column align-items-start" data-field="${key}">
                                <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
                                    <div class="flex-grow-1 pe-2">
                                        <div class="fw-semibold text-dark" style="font-size: 0.9rem;">${item.label}</div>
                                        <div class="text-muted small" style="font-size: 0.8rem;">${item.standar ?? ''}</div>
                                    </div>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check edit-radio" name="${key}" id="edit_${key}_ok" value="1" ${isOk ? 'checked' : ''}>
                                        <label class="btn btn-outline-success px-3 rounded-start" for="edit_${key}_ok">OK</label>

                                        <input type="radio" class="btn-check edit-radio" name="${key}" id="edit_${key}_nok" value="0" ${isNok ? 'checked' : ''}>
                                        <label class="btn btn-outline-danger px-3" for="edit_${key}_nok">NOK</label>

                                        <input type="radio" class="btn-check edit-radio" name="${key}" id="edit_${key}_empty" value="" ${isEmpty ? 'checked' : ''}>
                                        <label class="btn btn-outline-secondary px-3 rounded-end" for="edit_${key}_empty">Kosong</label>
                                    </div>
                                </div>
                                <div class="keterangan-wrapper mt-2 w-100 ${isNok ? '' : 'd-none'}">
                                    <input type="text" class="form-control form-control-sm bg-soft-danger text-danger border-danger edit-keterangan-input"
                                        name="keterangan_${key}" id="edit_keterangan_${key}" placeholder="Keterangan wajib diisi jika NOK..."
                                        value="${nokValue}" ${isNok ? 'required' : ''}>
                                </div>
                            </div>
                        `;
                });

                $('#editChecklistContainer').html(html);
                $('#modalEdit').modal('show');
            });

            // Handle edit radio change
            $(document).on('change', '.edit-radio', function() {
                const $row = $(this).closest('.item-row');
                const val = $row.find('input[type="radio"]:checked').val();
                const $wrapper = $row.find('.keterangan-wrapper');
                const $input = $wrapper.find('input');

                if (val === '0') { // NOK
                    $wrapper.removeClass('d-none');
                    $input.attr('required', true);
                } else {
                    $wrapper.addClass('d-none');
                    $input.val('').removeAttr('required');
                }
            });

            // Handle edit form submit
            $('#formEditP2h').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editId').val();

                // Collect NOK details for keterangan update
                const row = currentRows.find(r => r.id == id);
                const isElectric = row.jenis_mtc === 'Electric P2H' || row.jenis_mtc === 'Electrical P2H';
                const isDiesel = row.jenis_mtc === 'Diesel P2H';
                const isGenset = row.jenis_mtc === 'Genset P2H';

                let metadata = null;
                if (isElectric) {
                    metadata = electricP2h;
                } else if (isDiesel) {
                    metadata = dieselP2h;
                } else if (isGenset) {
                    metadata = gensetP2h;
                }
                const details = [];

                $('#editChecklistContainer .item-row').each(function() {
                    const $el = $(this);
                    const key = $el.data('field');
                    const isNg = $el.find('input[type="radio"]:checked').val() === '0';
                    if (isNg) {
                        const itemMeta = metadata[key];
                        const val = $el.find('input[name="keterangan_' + key + '"]').val().trim() || 'NOK';
                        details.push(`${itemMeta.label}: ${val}`);
                    }
                });

                const fd = new FormData(this);
                if (details.length) {
                    fd.set('keterangan', details.join(" | "));
                } else {
                    fd.set('keterangan', '');
                }

                $('#spinEdit').removeClass('d-none');
                $('#btnSaveEdit').prop('disabled', true);

                $.ajax({
                    url: `${UPDATE_URL}/${id}`,
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(resp) {
                        Swal.fire('Berhasil', resp.message, 'success');
                        $('#modalEdit').modal('hide');
                        loadTableData(currentPage);
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan',
                            'error');
                    },
                    complete: function() {
                        $('#spinEdit').addClass('d-none');
                        $('#btnSaveEdit').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
