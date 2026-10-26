@extends('layouts.app')

@php
    $isEdit = isset($project);
    $pageTitle = $isEdit ? 'Edit Project Utility' : 'Form Input Project Utility';
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- ── Header Banner ── --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm"
                        style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 12px;">
                        <div class="card-body p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <h4 class="text-white fw-bold mb-1">
                                    <i class="ri-file-add-line text-warning me-2"></i>
                                    {{ $pageTitle }}
                                </h4>
                                <p class="text-white-50 mb-0 fs-13">
                                    {{ $isEdit ? 'Perbarui informasi dan target project utility' : 'Tambahkan project baru untuk tracking pekerjaan engineering utility (Boiler, Utility, WWTP, MTC)' }}
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('utility.project.data') }}"
                                    class="btn btn-light btn-sm fw-semibold shadow-sm">
                                    <i class="ri-arrow-left-line me-1"></i> Data Project
                                </a>
                                {{-- <a href="{{ route('utility.project.dashboard') }}"
                                    class="btn btn-outline-light btn-sm fw-semibold shadow-sm">
                                    <i class="ri-dashboard-2-line me-1"></i> Dashboard
                                </a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Form Card (Centered Clean Light Bootstrap) ── --}}
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10 col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs me-2">
                                    <span class="avatar-title bg-primary-subtle rounded-circle">
                                        <i class="ri-edit-2-line fs-14"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="card-title mb-0 fw-bold text-dark">
                                        {{ $isEdit ? 'Formulir Pembaruan Project' : 'Formulir Pendaftaran Project Baru' }}
                                    </h6>
                                    <small class="text-muted">Lengkapi data project di bawah ini dengan benar</small>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                    <strong>Terdapat kesalahan pengisian data:</strong>
                                    <ul class="mb-0 mt-1 ps-3">
                                        @foreach ($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <form method="POST"
                                action="{{ $isEdit ? route('utility.project.update', $project->id) : route('utility.project.store') }}">
                                @csrf
                                @if ($isEdit)
                                    @method('PUT')
                                @endif

                                {{-- Area Project Selection --}}
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-bold text-dark mb-0">
                                            Area Project <span class="text-danger">*</span>
                                        </label>
                                    </div>
                                    <div class="row g-2">
                                        @php
                                            $currentArea = old(
                                                'area',
                                                $isEdit ? $project->area : $areas[0] ?? 'Utility',
                                            );
                                        @endphp
                                        @foreach ($areas as $area)
                                            <div class="col-6 col-sm-3">
                                                <input type="radio" class="btn-check" name="area"
                                                    id="area_{{ \Illuminate\Support\Str::slug($area) }}"
                                                    value="{{ $area }}"
                                                    {{ $currentArea === $area ? 'checked' : '' }} required>
                                                <label
                                                    class="btn btn-outline-primary w-100 py-2 d-flex flex-column align-items-center justify-content-center"
                                                    for="area_{{ \Illuminate\Support\Str::slug($area) }}">
                                                    <i
                                                        class="fs-20 mb-1 {{ $area === 'Boiler' ? 'ri-fire-line' : ($area === 'Utility' ? 'ri-flashlight-line' : ($area === 'WWTP' ? 'ri-water-flash-line' : ($area === 'MTC' ? 'ri-tools-line' : 'ri-community-line'))) }}"></i>
                                                    <span class="fw-semibold fs-13">{{ $area }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Kategori & Nomor Project --}}
                                <div class="row g-3 mb-3">
                                    <div class="col-md-7">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label fw-bold text-dark mb-0">
                                                Kategori Project <span class="text-danger">*</span>
                                            </label>
                                        </div>
                                        <select name="kategori" class="form-select" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat }}"
                                                    {{ old('kategori', $isEdit ? $project->kategori : '') === $cat ? 'selected' : '' }}>
                                                    {{ $cat }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold text-muted mb-1">
                                            Nomor Project (Opsional)
                                        </label>
                                        <input type="text" class="form-control" name="nomor_project"
                                            placeholder="Auto-generate jika kosong"
                                            value="{{ old('nomor_project', $isEdit ? $project->nomor_project : '') }}">
                                    </div>
                                </div>

                                {{-- Priority Project Selection --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark mb-1">
                                        Tingkat Prioritas (Priority) <span class="text-danger">*</span>
                                    </label>
                                    @php
                                        $currPriority = old('priority', $isEdit ? ($project->priority ?? 'Medium') : 'Medium');
                                        $priorities = [
                                            ['val' => 'Critical', 'label' => 'Critical', 'color' => 'danger', 'icon' => 'ri-alarm-warning-fill', 'desc' => 'Mendesak & Urgent'],
                                            ['val' => 'High', 'label' => 'High', 'color' => 'warning', 'icon' => 'ri-arrow-up-circle-fill', 'desc' => 'Prioritas Tinggi'],
                                            ['val' => 'Medium', 'label' => 'Medium', 'color' => 'primary', 'icon' => 'ri-equal-circle-fill', 'desc' => 'Prioritas Reguler'],
                                            ['val' => 'Low', 'label' => 'Low', 'color' => 'secondary', 'icon' => 'ri-arrow-down-circle-line', 'desc' => 'Prioritas Rendah']
                                        ];
                                    @endphp
                                    <div class="row g-2">
                                        @foreach ($priorities as $p)
                                            <div class="col-6 col-md-3">
                                                <input type="radio" class="btn-check" name="priority"
                                                    id="priority_{{ strtolower($p['val']) }}"
                                                    value="{{ $p['val'] }}"
                                                    {{ $currPriority === $p['val'] ? 'checked' : '' }} required>
                                                <label class="btn btn-outline-{{ $p['color'] }} w-100 py-2 text-start d-flex flex-column justify-content-between h-100"
                                                    for="priority_{{ strtolower($p['val']) }}">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="{{ $p['icon'] }} me-1 fs-15"></i>
                                                        <span class="fw-bold fs-13">{{ $p['val'] }}</span>
                                                    </div>
                                                    <small class="fs-11 opacity-75 d-block">{{ $p['desc'] }}</small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Nama Project --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark">
                                        Nama Project <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="nama_project"
                                        placeholder="Contoh: Pembuatan Pipa Bypass Feed Water Pompa 2"
                                        value="{{ old('nama_project', $isEdit ? $project->nama_project : '') }}" required>
                                </div>

                                {{-- Target Result --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark">
                                        Target Result / Sasaran Hasil
                                    </label>
                                    <textarea class="form-control" name="target_result" rows="2"
                                        placeholder="Contoh: Menghindari downtime saat maintenance pompa utama, flow air tetap stabil 25 m3/h">{{ old('target_result', $isEdit ? $project->target_result : '') }}</textarea>
                                </div>

                                {{-- ── Timeline Target (Rapi, Terstruktur 2 Kolom: Mulai & Target Selesai) ── --}}
                                <div class="card border mb-3 bg-light-subtle shadow-none">
                                    <div
                                        class="card-header py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-calendar-event-line text-primary me-2 fs-16"></i>
                                            <span class="fw-bold text-dark fs-13">Timeline & Jadwal Pengerjaan</span>
                                        </div>
                                        <small class="text-muted">Target Mulai & Selesai</small>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row g-3">
                                            {{-- Mulai (Start) --}}
                                            <div class="col-md-6">
                                                <div class="p-3 rounded border h-100 shadow-none">
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <span
                                                            class="badge bg-primary-subtle border text-primary border-primary-subtle fs-11">
                                                            <i class="ri-play-circle-line me-1"></i> Mulai (Start)
                                                        </span>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label text-muted fs-12 mb-1">Tanggal
                                                            Mulai</label>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text bg-light text-muted"><i
                                                                    class="ri-calendar-line"></i></span>
                                                            <input type="date" class="form-control" name="start_date"
                                                                value="{{ old('start_date', $isEdit && $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : date('Y-m-d')) }}">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="form-label text-muted fs-12 mb-1">Minggu Mulai
                                                            (Week)</label>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text bg-light text-muted"><i
                                                                    class="ri-time-line"></i></span>
                                                            <input type="text" class="form-control" name="start_week"
                                                                placeholder="Contoh: W1 Jan / W4 Okt"
                                                                value="{{ old('start_week', $isEdit ? $project->start_week : '') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Target Selesai (Deadline) --}}
                                            <div class="col-md-6">
                                                <div class="p-3 rounded border h-100 shadow-none">
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <span
                                                            class="badge bg-danger-subtle text-danger border border-danger-subtle fs-11">
                                                            <i class="ri-flag-2-line me-1"></i> Target Selesai (Deadline)
                                                        </span>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label text-muted fs-12 mb-1">Tanggal
                                                            Selesai</label>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text bg-light text-muted"><i
                                                                    class="ri-calendar-check-line"></i></span>
                                                            <input type="date" class="form-control" name="finish_date"
                                                                value="{{ old('finish_date', $isEdit && $project->finish_date ? \Carbon\Carbon::parse($project->finish_date)->format('Y-m-d') : '') }}">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="form-label text-muted fs-12 mb-1">Minggu Selesai
                                                            (Week)</label>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text bg-light text-muted"><i
                                                                    class="ri-time-line"></i></span>
                                                            <input type="text" class="form-control" name="finish_week"
                                                                placeholder="Contoh: W4 Nov / W2 Des"
                                                                value="{{ old('finish_week', $isEdit ? $project->finish_week : '') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── Progress Persentase & Status ── --}}
                                @php
                                    $initialProg = (int) old('progress', $isEdit ? $project->progress : 0);
                                    $currStatus = old('status', $isEdit ? $project->status : 'On Progress');
                                @endphp
                                <div class="card border mb-3 bg-light-subtle shadow-none">
                                    <div
                                        class="card-header py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-pie-chart-line text-primary me-2 fs-16"></i>
                                            <span class="fw-bold text-dark fs-13">Progress & Status Pekerjaan</span>
                                        </div>
                                        <span id="sliderBadge"
                                            class="badge {{ $initialProg == 100 ? 'bg-success' : 'bg-primary' }} fs-12">{{ $initialProg }}%</span>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-md-7">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <label class="form-label fw-bold text-dark mb-0">Progress Persentase
                                                        (%)</label>
                                                    <span class="fs-12 text-muted">Geser atau ketik angka</span>
                                                </div>

                                                {{-- Visual Live Progress Bar --}}
                                                <div class="progress mb-2" style="height: 10px; border-radius: 6px;">
                                                    <div id="visualProgressBar"
                                                        class="progress-bar progress-bar-striped progress-bar-animated {{ $initialProg == 100 ? 'bg-success' : 'bg-primary' }}"
                                                        role="progressbar" style="width: {{ $initialProg }}%;"
                                                        aria-valuenow="{{ $initialProg }}" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>

                                                {{-- Range Slider --}}
                                                <input type="range" class="form-range mb-2" id="progressRange"
                                                    min="0" max="100" step="1"
                                                    value="{{ $initialProg }}" oninput="syncProgress(this.value)">

                                                {{-- Number Input --}}
                                                <div class="input-group input-group-sm" style="max-width: 180px;">
                                                    <span class="input-group-text bg-light text-muted">Input</span>
                                                    <input type="number" class="form-control text-center fw-bold"
                                                        id="progressInput" name="progress" min="0" max="100"
                                                        value="{{ $initialProg }}" oninput="syncProgress(this.value)">
                                                    <span class="input-group-text fw-semibold">%</span>
                                                </div>
                                            </div>

                                            <div class="col-md-5">
                                                <label class="form-label fw-bold text-dark mb-1">Status Project</label>
                                                <select name="status" id="statusSelect" class="form-select">
                                                    <option value="On Progress"
                                                        {{ $currStatus === 'On Progress' ? 'selected' : '' }}>On Progress
                                                    </option>
                                                    <option value="Selesai"
                                                        {{ $currStatus === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                                    <option value="Terlambat"
                                                        {{ $currStatus === 'Terlambat' ? 'selected' : '' }}>Terlambat
                                                    </option>
                                                </select>
                                                <small class="text-muted fs-11 mt-1 d-block">
                                                    <i class="ri-information-line me-1"></i>Otomatis
                                                    <strong>Selesai</strong> jika progress mencapai 100%.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Catatan / Progress Note --}}
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-dark">
                                        {{ $isEdit ? 'Catatan Tambahan untuk Pembaruan' : 'Catatan Awal / Perkembangan Pekerjaan' }}
                                    </label>
                                    <textarea class="form-control" name="{{ $isEdit ? 'catatan_update' : 'latest_note' }}" rows="3"
                                        placeholder="Tuliskan catatan kemajuan pekerjaan, vendor terkait, atau kendala lapangan...">{{ old($isEdit ? 'catatan_update' : 'latest_note', $isEdit ? '' : '') }}</textarea>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                    <a href="{{ route('utility.project.data') }}" class="btn btn-light px-4">
                                        Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                        <i class="ri-save-line me-1"></i>
                                        {{ $isEdit ? 'Perbarui Project' : 'Simpan Project Baru' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function syncProgress(val) {
            val = parseInt(val) || 0;
            if (val < 0) val = 0;
            if (val > 100) val = 100;

            const $range = $('#progressRange');
            const $input = $('#progressInput');
            const $badge = $('#sliderBadge');
            const $bar = $('#visualProgressBar');
            const $status = $('#statusSelect');

            if ($range.val() != val) $range.val(val);
            if ($input.val() != val) $input.val(val);

            $badge.text(val + '%');
            if (val === 100) {
                $badge.attr('class', 'badge bg-success fs-12');
                $bar.attr('class', 'progress-bar progress-bar-striped progress-bar-animated bg-success');
                if ($status.val() !== 'Terlambat') {
                    $status.val('Selesai');
                }
            } else {
                $badge.attr('class', 'badge bg-primary fs-12');
                $bar.attr('class', 'progress-bar progress-bar-striped progress-bar-animated bg-primary');
                if ($status.val() === 'Selesai') {
                    $status.val('On Progress');
                }
            }

            $bar.css('width', val + '%').attr('aria-valuenow', val);
        }

        $(document).ready(function() {
            $('#progressRange').on('input', function() {
                syncProgress($(this).val());
            });

            $('#progressInput').on('input', function() {
                syncProgress($(this).val());
            });
        });
    </script>
@endsection
