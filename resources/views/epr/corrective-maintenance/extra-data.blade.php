@extends('layouts.app')

@section('title', 'Input Biaya, OEE & Action Plan CM')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Header --}}
        <div class="row mb-3 align-items-center">
            <div class="col">
                <h4 class="mb-1 text-slate-800 fw-bold">
                    <i class="ri-money-dollar-circle-line text-primary me-2"></i>
                    INPUT BIAYA, OEE & ACTION PLAN CM
                </h4>
                <p class="text-muted mb-0 fs-13">Kelola data pendukung Corrective Maintenance (Biaya sparepart, rating OEE mesin, & Rencana Perbaikan)</p>
            </div>
            <div class="col-auto">
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 fs-12 fw-semibold">Periode Bulan:</label>
                    <input type="month" class="form-control form-control-sm" value="{{ $month }}" style="width: 160px;" onchange="window.location.href='{{ route('epr.cm.extra-data') }}?month=' + this.value">
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        {{-- Nav Tabs --}}
        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-costs" role="tab">
                    <i class="ri-money-dollar-box-line me-1"></i> Biaya Perbaikan / Sparepart
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-oee" role="tab">
                    <i class="ri-speedometer-line me-1"></i> OEE & KPI Tambahan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-action-plans" role="tab">
                    <i class="ri-task-line me-1"></i> Action Plan & Timeline
                </a>
            </li>
        </ul>

        {{-- Tab Content --}}
        <div class="tab-content text-muted">

            {{-- TAB 1: BIAYA PERBAIKAN --}}
            <div class="tab-pane active" id="tab-costs" role="tabpanel">
                <div class="row g-3">
                    {{-- Form Add Cost --}}
                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-dark text-white py-2">
                                <h6 class="card-title text-white mb-0 fs-13"><i class="ri-add-circle-line me-1"></i> Tambah Biaya Perbaikan</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('epr.cm.cost.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label fs-12 fw-semibold mb-1">Pilih Mesin <span class="text-danger">*</span></label>
                                        <select name="mesin" class="form-select form-select-sm" required>
                                            <option value="">-- Pilih Mesin --</option>
                                            @foreach($machines as $m)
                                            <option value="{{ $m->name }}">{{ $m->name }} {{ $m->code ? ' (' . $m->code . ')' : '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fs-12 fw-semibold mb-1">Tanggal Perbaikan <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fs-12 fw-semibold mb-1">Kategori Biaya <span class="text-danger">*</span></label>
                                        <select name="kategori_biaya" class="form-select form-select-sm" required>
                                            <option value="Sparepart">Sparepart</option>
                                            <option value="Jasa">Jasa Perbaikan</option>
                                            <option value="Material">Material Support</option>
                                            <option value="Overhaul">Overhaul Unit</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fs-12 fw-semibold mb-1">Jumlah Biaya (Rp) <span class="text-danger">*</span></label>
                                        <input type="number" name="jumlah_biaya" class="form-control form-control-sm" placeholder="Contoh: 15000000" min="0" step="1000" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fs-12 fw-semibold mb-1">Deskripsi / Sparepart</label>
                                        <textarea name="deskripsi" class="form-control form-control-sm" rows="2" placeholder="Detail part yang diganti..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="ri-save-line me-1"></i> Simpan Data Biaya</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Data Table Costs --}}
                    <div class="col-xl-8 col-lg-7">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light py-2">
                                <h6 class="card-title mb-0 fs-13 fw-bold">Daftar Biaya Perbaikan ({{ date('F Y', strtotime($month . '-01')) }})</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover align-middle mb-0" style="font-size: 12px;">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>TANGGAL</th>
                                                <th>MESIN</th>
                                                <th>KATEGORI</th>
                                                <th>DESKRIPSI</th>
                                                <th class="text-end">BIAYA (RP)</th>
                                                <th class="text-center" width="50">AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($costs as $c)
                                            <tr>
                                                <td class="font-monospace">{{ date('d M Y', strtotime($c->tanggal)) }}</td>
                                                <td class="fw-bold">{{ $c->mesin }}</td>
                                                <td><span class="badge bg-info-subtle text-info border border-info-subtle">{{ $c->kategori_biaya }}</span></td>
                                                <td>{{ $c->deskripsi ?: '—' }}</td>
                                                <td class="text-end font-monospace fw-bold text-success">Rp {{ number_format($c->jumlah_biaya, 0, ',', '.') }}</td>
                                                <td class="text-center">
                                                    <form action="{{ route('epr.cm.cost.delete', $c->id) }}" method="POST" onsubmit="return confirm('Hapus data biaya ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-icon btn-xs"><i class="ri-delete-bin-line"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">Belum ada data biaya perbaikan untuk bulan ini.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        @if($costs->count() > 0)
                                        <tfoot class="table-light fw-bold">
                                            <tr>
                                                <td colspan="4" class="text-end">TOTAL BIAYA:</td>
                                                <td class="text-end text-success fs-13">Rp {{ number_format($costs->sum('jumlah_biaya'), 0, ',', '.') }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: OEE & KPI TAMBAHAN --}}
            <div class="tab-pane" id="tab-oee" role="tabpanel">
                <div class="row g-3">
                    {{-- Form Add OEE --}}
                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-dark text-white py-2">
                                <h6 class="card-title text-white mb-0 fs-13"><i class="ri-speedometer-line me-1"></i> Input OEE & KPI Mesin</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('epr.cm.kpi.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <div class="mb-2">
                                        <label class="form-label fs-12 fw-semibold mb-1">Pilih Mesin <span class="text-danger">*</span></label>
                                        <select name="mesin" class="form-select form-select-sm" required>
                                            <option value="">-- Pilih Mesin --</option>
                                            @foreach($machines as $m)
                                            <option value="{{ $m->name }}">{{ $m->name }} {{ $m->code ? ' (' . $m->code . ')' : '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-4">
                                            <label class="form-label fs-11 fw-semibold mb-1">Avail (%)</label>
                                            <input type="number" name="availability_pct" class="form-control form-control-sm" placeholder="85.0" step="0.1" min="0" max="100" required>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label fs-11 fw-semibold mb-1">Perf (%)</label>
                                            <input type="number" name="performance_pct" class="form-control form-control-sm" placeholder="90.0" step="0.1" min="0" max="100" required>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label fs-11 fw-semibold mb-1">Qual (%)</label>
                                            <input type="number" name="quality_pct" class="form-control form-control-sm" placeholder="98.0" step="0.1" min="0" max="100" required>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label fs-11 fw-semibold mb-1">PM Compliance (%)</label>
                                            <input type="number" name="pm_compliance_pct" class="form-control form-control-sm" value="92.0" step="0.1" min="0" max="100">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fs-11 fw-semibold mb-1">Repeat Failure (%)</label>
                                            <input type="number" name="repeat_failure_pct" class="form-control form-control-sm" value="12.0" step="0.1" min="0" max="100">
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-4">
                                            <label class="form-label fs-11 fw-semibold mb-1">Minor Stop</label>
                                            <input type="number" name="minor_stop_freq" class="form-control form-control-sm" value="10.0" step="0.1">
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label fs-11 fw-semibold mb-1">Cost / Hour</label>
                                            <input type="number" name="cost_per_hour" class="form-control form-control-sm" value="50.0" step="0.1">
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label fs-11 fw-semibold mb-1">Energy / Pack</label>
                                            <input type="number" name="energy_per_pack" class="form-control form-control-sm" value="0.35" step="0.01">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm w-100"><i class="ri-save-line me-1"></i> Simpan KPI Mesin</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Data Table OEE --}}
                    <div class="col-xl-8 col-lg-7">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light py-2">
                                <h6 class="card-title mb-0 fs-13 fw-bold">Daftar KPI & OEE Mesin ({{ date('F Y', strtotime($month . '-01')) }})</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover align-middle mb-0 text-center" style="font-size: 11px;">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>MESIN</th>
                                                <th>AVAIL (%)</th>
                                                <th>PERF (%)</th>
                                                <th>QUAL (%)</th>
                                                <th>OEE (%)</th>
                                                <th>PM COMP (%)</th>
                                                <th>REPEAT FAIL (%)</th>
                                                <th width="50">AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($kpis as $k)
                                            <tr>
                                                <td class="fw-bold text-start">{{ $k->mesin }}</td>
                                                <td>{{ number_format($k->availability_pct, 1) }}</td>
                                                <td>{{ number_format($k->performance_pct, 1) }}</td>
                                                <td>{{ number_format($k->quality_pct, 1) }}</td>
                                                <td class="fw-bold {{ $k->oee_pct >= 85 ? 'text-success' : ($k->oee_pct >= 70 ? 'text-warning' : 'text-danger') }}">{{ number_format($k->oee_pct, 1) }}%</td>
                                                <td>{{ number_format($k->pm_compliance_pct, 1) }}%</td>
                                                <td>{{ number_format($k->repeat_failure_pct, 1) }}%</td>
                                                <td>
                                                    <form action="{{ route('epr.cm.kpi.delete', $k->id) }}" method="POST" onsubmit="return confirm('Hapus data OEE ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-icon btn-xs"><i class="ri-delete-bin-line"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">Belum ada data OEE KPI untuk bulan ini.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 3: ACTION PLAN & TIMELINE --}}
            <div class="tab-pane" id="tab-action-plans" role="tabpanel">
                <div class="row g-3">
                    {{-- Form Add Action Plan --}}
                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-dark text-white py-2">
                                <h6 class="card-title text-white mb-0 fs-13"><i class="ri-task-line me-1"></i> Tambah Action Plan</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('epr.cm.action-plan.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <div class="mb-2">
                                        <label class="form-label fs-12 fw-semibold mb-1">Pilih Mesin <span class="text-danger">*</span></label>
                                        <select name="mesin" class="form-select form-select-sm" required>
                                            <option value="">-- Pilih Mesin --</option>
                                            @foreach($machines as $m)
                                            <option value="{{ $m->name }}">{{ $m->name }} {{ $m->code ? ' (' . $m->code . ')' : '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fs-12 fw-semibold mb-1">Isu Utama / Kendala <span class="text-danger">*</span></label>
                                        <input type="text" name="isu_utama" class="form-control form-control-sm" placeholder="Contoh: Breakdown Tertinggi 8.67%" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fs-12 fw-semibold mb-1">Akar Masalah</label>
                                        <input type="text" name="akar_masalah" class="form-control form-control-sm" placeholder="Contoh: Conveyor sering jammed">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fs-12 fw-semibold mb-1">Saran Perbaikan / Action</label>
                                        <input type="text" name="saran_perbaikan" class="form-control form-control-sm" placeholder="Contoh: Overhaul conveyor & guide">
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label fs-11 fw-semibold mb-1">PIC</label>
                                            <input type="text" name="pic" class="form-control form-control-sm" placeholder="MECH / ELEC">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fs-11 fw-semibold mb-1">Target Date</label>
                                            <input type="date" name="target_date" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fs-11 fw-semibold mb-1">Progress Mingguan (Gantt Colors)</label>
                                        <div class="row g-1">
                                            <div class="col-3 text-center">
                                                <span class="fs-10 d-block text-muted">W1</span>
                                                <select name="w1_status" class="form-select form-select-sm">
                                                    <option value="none">Kosong</option>
                                                    <option value="red" class="bg-danger text-white">Merah</option>
                                                    <option value="orange" class="bg-warning text-white">Orange</option>
                                                    <option value="yellow" class="bg-warning text-dark">Kuning</option>
                                                    <option value="green" class="bg-success text-white">Hijau</option>
                                                </select>
                                            </div>
                                            <div class="col-3 text-center">
                                                <span class="fs-10 d-block text-muted">W2</span>
                                                <select name="w2_status" class="form-select form-select-sm">
                                                    <option value="none">Kosong</option>
                                                    <option value="red" class="bg-danger text-white">Merah</option>
                                                    <option value="orange" class="bg-warning text-white">Orange</option>
                                                    <option value="yellow" class="bg-warning text-dark">Kuning</option>
                                                    <option value="green" class="bg-success text-white">Hijau</option>
                                                </select>
                                            </div>
                                            <div class="col-3 text-center">
                                                <span class="fs-10 d-block text-muted">W3</span>
                                                <select name="w3_status" class="form-select form-select-sm">
                                                    <option value="none">Kosong</option>
                                                    <option value="red" class="bg-danger text-white">Merah</option>
                                                    <option value="orange" class="bg-warning text-white">Orange</option>
                                                    <option value="yellow" class="bg-warning text-dark">Kuning</option>
                                                    <option value="green" class="bg-success text-white">Hijau</option>
                                                </select>
                                            </div>
                                            <div class="col-3 text-center">
                                                <span class="fs-10 d-block text-muted">W4</span>
                                                <select name="w4_status" class="form-select form-select-sm">
                                                    <option value="none">Kosong</option>
                                                    <option value="red" class="bg-danger text-white">Merah</option>
                                                    <option value="orange" class="bg-warning text-white">Orange</option>
                                                    <option value="yellow" class="bg-warning text-dark">Kuning</option>
                                                    <option value="green" class="bg-success text-white">Hijau</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-warning text-dark btn-sm w-100 fw-bold"><i class="ri-save-line me-1"></i> Simpan Action Plan</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Data Table Action Plan --}}
                    <div class="col-xl-8 col-lg-7">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light py-2">
                                <h6 class="card-title mb-0 fs-13 fw-bold">Daftar Action Plan & Progress Timeline ({{ date('F Y', strtotime($month . '-01')) }})</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover align-middle mb-0" style="font-size: 11px;">
                                        <thead class="table-dark text-center">
                                            <tr>
                                                <th>MESIN</th>
                                                <th>ISU UTAMA</th>
                                                <th>SARAN PERBAIKAN</th>
                                                <th>PIC</th>
                                                <th>TARGET</th>
                                                <th>W1</th>
                                                <th>W2</th>
                                                <th>W3</th>
                                                <th>W4</th>
                                                <th width="40">AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($actionPlans as $ap)
                                            <tr>
                                                <td class="fw-bold text-center">{{ $ap->mesin }}</td>
                                                <td>{{ $ap->isu_utama }}</td>
                                                <td>{{ $ap->saran_perbaikan }}</td>
                                                <td class="text-center">{{ $ap->pic }}</td>
                                                <td class="text-center font-monospace">{{ $ap->target_date ? date('d M Y', strtotime($ap->target_date)) : '—' }}</td>
                                                <td class="text-center">@if($ap->w1_status !== 'none') <span class="badge bg-{{ $ap->w1_status == 'orange' ? 'warning' : ($ap->w1_status == 'yellow' ? 'warning text-dark' : $ap->w1_status) }}">&nbsp;</span> @endif</td>
                                                <td class="text-center">@if($ap->w2_status !== 'none') <span class="badge bg-{{ $ap->w2_status == 'orange' ? 'warning' : ($ap->w2_status == 'yellow' ? 'warning text-dark' : $ap->w2_status) }}">&nbsp;</span> @endif</td>
                                                <td class="text-center">@if($ap->w3_status !== 'none') <span class="badge bg-{{ $ap->w3_status == 'orange' ? 'warning' : ($ap->w3_status == 'yellow' ? 'warning text-dark' : $ap->w3_status) }}">&nbsp;</span> @endif</td>
                                                <td class="text-center">@if($ap->w4_status !== 'none') <span class="badge bg-{{ $ap->w4_status == 'orange' ? 'warning' : ($ap->w4_status == 'yellow' ? 'warning text-dark' : $ap->w4_status) }}">&nbsp;</span> @endif</td>
                                                <td class="text-center">
                                                    <form action="{{ route('epr.cm.action-plan.delete', $ap->id) }}" method="POST" onsubmit="return confirm('Hapus Action Plan ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-icon btn-xs"><i class="ri-delete-bin-line"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-muted py-4">Belum ada action plan untuk bulan ini.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
