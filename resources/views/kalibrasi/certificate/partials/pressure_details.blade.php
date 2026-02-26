<div class="modal-body p-0">
    <!-- Tabs -->
    <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3 px-3 pt-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#info-pane">Informasi</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#measurement-pane">Pengukuran</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#result-pane">Hitung U
                Gabungan</button>
        </li>
    </ul>

    <div class="tab-content p-3">
        <!-- Tab 1: Informasi -->
        <div class="tab-pane fade show active" id="info-pane">
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="card card-animate border-start border-primary border-3">
                        <div class="card-body">
                            <small class="text-muted d-block">
                                <i class="bi bi-calendar-event"></i> Kode Alat
                            </small>
                            <strong>{{ $main->alat->kode_alat }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-animate border-start border-primary border-3">
                        <div class="card-body">
                            <small class="text-muted d-block">
                                <i class="bi bi-calendar-event"></i> Nama Alat
                            </small>
                            <strong id="detail_nama_alat">{{ $main->alat->nama_alat }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-animate border-start border-primary border-3">
                        <div class="card-body">
                            <small class="text-muted d-block">
                                <i class="bi bi-calendar-event"></i> Tanggal Kalibrasi
                            </small>
                            <strong id="detail_tgl_kalibrasi">{{ $main->tgl_kalibrasi }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-animate border-start border-success border-3">
                        <div class="card-body">
                            <small class="text-muted d-block">
                                <i class="bi bi-calendar-check"></i> Tgl Kalibrasi Ulang
                            </small>
                            <strong id="detail_tgl_ulang">{{ $main->tgl_kalibrasi_ulang }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-animate border-start border-info border-3">
                        <div class="card-body">
                            <small class="text-muted d-block">
                                <i class="bi bi-geo-alt"></i> Lokasi
                            </small>
                            <strong id="detail_lokasi">{{ $main->lokasi_kalibrasi }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-animate border-start border-warning border-3">
                        <div class="card-body">
                            <small class="text-muted d-block">
                                <i class="bi bi-thermometer-half"></i> Suhu Ruangan
                            </small>
                            <strong id="detail_suhu">{{ $main->suhu_ruangan }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-animate border-start border-info border-3">
                        <div class="card-body">
                            <small class="text-muted d-block">
                                <i class="bi bi-droplet"></i> Kelembaban
                            </small>
                            <strong id="detail_kelembaban">{{ $main->kelembaban }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-animate border-start border-primary border-3">
                        <div class="card-body">
                            <small class="text-muted d-block">
                                <i class="bi bi-gear"></i> Jenis Kalibrasi
                            </small>
                            <strong id="detail_jenis">{{ $main->jenis_kalibrasi }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-primary">
                <strong><i class="bi bi-book me-2"></i>Metode Kalibrasi</strong>
                <p class="mb-0 mt-2 small" id="detail_metode">{{ $main->alat->metode_kalibrasi }}</p>
            </div>
        </div>

        <!-- Tab 2: Pengukuran -->
        <div class="tab-pane fade" id="measurement-pane">
            <!-- Tekanan Naik -->
            <div class="card mb-3 border-success">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-arrow-up-circle-fill me-2"></i>
                    <strong>Tekanan Naik</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm text-center mb-0">
                            <thead class="table-light align-middle">
                                <tr>
                                    <th>Titik</th>
                                    <th>Penunjuk Standar</th>
                                    <th>Penunjuk Alat</th>
                                    <th>Koreksi Standar</th>
                                    <th>Tekanan Standar</th>
                                    <th>Koreksi Alat</th>
                                    <th>Avg. Penunjuk Alat</th>
                                    <th>Avg. Tekanan Standar</th>
                                    <th>Avg. Kor Alat</th>
                                    <th>Standar Deviasi</th>
                                    <th>Ketidakpastian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($main->pressure as $pressure)
                                    @php
                                        $detailsNaik = $pressure->details->where('arah', 'naik');
                                        $detailCount = $detailsNaik->count();
                                    @endphp

                                    @foreach ($detailsNaik as $index => $detail)
                                        <tr>
                                            @if ($index == 0)
                                                <td rowspan="{{ $detailCount }}" class="align-middle fw-bold">
                                                    {{ formNum($pressure->titik_kalibrasi) }}
                                                </td>
                                            @endif

                                            <td>{{ formNum($detail->penunjuk_standar) }}</td>
                                            <td>{{ formNum($detail->penunjuk_alat) }}</td>
                                            <td>{{ formNum($detail->koreksi_standar) }}</td>
                                            <td>{{ formNum($detail->tekanan_standar) }}</td>
                                            <td>{{ formNum($detail->koreksi_alat) }}</td>

                                            @if ($index === 0)
                                                <td rowspan="{{ $detailCount }}">
                                                    {{ formNum($pressure->avg_penunjuk_alat_naik) }}
                                                </td>
                                                <td rowspan="{{ $detailCount }}">
                                                    {{ formNum($pressure->avg_tekanan_standar_naik) }}
                                                </td>
                                                <td rowspan="{{ $detailCount }}">
                                                    {{ formNum($pressure->avg_koreksi_alat_naik) }}
                                                </td>
                                                <td rowspan="{{ $detailCount }}">
                                                    {{ formNum($pressure->std_deviasi_naik) }}
                                                </td>
                                                <td rowspan="{{ $detailCount }}">
                                                    {{ formNum($pressure->ketidakpastian_naik) }}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>


                        </table>
                    </div>
                </div>
            </div>

            <!-- Tekanan Turun -->
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-arrow-down-circle-fill me-2"></i>
                    <strong>Tekanan Turun</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm text-center mb-0">
                            <thead class="table-light align-middle">
                                <tr>
                                    <th>Titik</th>
                                    <th>Penunjuk Standar</th>
                                    <th>Penunjuk Alat</th>
                                    <th>Koreksi Standar</th>
                                    <th>Tekanan Standar</th>
                                    <th>Koreksi Alat</th>
                                    <th>Avg. Penunjuk Alat</th>
                                    <th>Avg. Tekanan Standar</th>
                                    <th>Avg. Kor Alat</th>
                                    <th>Standar Deviasi</th>
                                    <th>Ketidakpastian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($main->pressure as $pressure)
                                    @php
                                        $detailsTurun = $pressure->details
                                            ->filter(fn($item) => strtolower(trim($item->arah)) === 'turun')
                                            ->values(); // reset index biar rapi

                                        $detailCount = $detailsTurun->count();
                                    @endphp

                                    @if ($detailCount > 0)
                                        @foreach ($detailsTurun as $index => $detail)
                                            <tr>
                                                @if ($index === 0)
                                                    <td rowspan="{{ $detailCount }}" class="align-middle fw-bold">
                                                        {{ formNum($pressure->titik_kalibrasi) }}
                                                    </td>
                                                @endif
                                                {{-- <td>{{ formNum($pressure->titik_kalibrasi) }}</td> --}}
                                                <td>{{ formNum($detail->penunjuk_standar) }}</td>
                                                <td>{{ formNum($detail->penunjuk_alat) }}</td>
                                                <td>{{ formNum($detail->koreksi_standar) }}</td>
                                                <td>{{ formNum($detail->tekanan_standar) }}</td>
                                                <td>{{ formNum($detail->koreksi_alat) }}</td>
                                                @if ($index === 0)
                                                    <td rowspan="{{ $detailCount }}">
                                                        {{ formNum($pressure->avg_penunjuk_alat_turun) }}
                                                    </td>
                                                    <td rowspan="{{ $detailCount }}">
                                                        {{ formNum($pressure->avg_tekanan_standar_turun) }}
                                                    </td>
                                                    <td rowspan="{{ $detailCount }}">
                                                        {{ formNum($pressure->avg_koreksi_alat_turun) }}
                                                    </td>
                                                    <td rowspan="{{ $detailCount }}">
                                                        {{ formNum($pressure->std_deviasi_turun) }}
                                                    </td>
                                                    <td rowspan="{{ $detailCount }}">
                                                        {{ formNum($pressure->ketidakpastian_turun) }}
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Hasil -->
        <div class="tab-pane fade" id="result-pane">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-calculator-fill me-2"></i>
                    <strong>Hasil Perhitungan Ketidakpastian</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Titik</th>
                                    <th>U Naik</th>
                                    <th>U Turun</th>
                                    <th>U Naik²</th>
                                    <th>U Turun²</th>
                                    <th class="bg-success-subtle">U Gabungan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($main->pressure as $pressure)
                                    <tr>
                                        <td>{{ formNum($pressure->titik_kalibrasi, 6) }}</td>
                                        <td>{{ formNum($pressure->u_naik, 6) }}</td>
                                        <td>{{ formNum($pressure->u_turun, 6) }}</td>
                                        <td>{{ formNum($pressure->u_naik_kuadrat, 6) }}</td>
                                        <td>{{ formNum($pressure->u_turun_kuadrat, 6) }}</td>
                                        <td class="fw-bold text-success">
                                            {{ formNum($pressure->u_gabungan, 6) }}
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer bg-light">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
        <i class="bi bi-x-circle me-1"></i> Tutup
    </button>
</div>

@php
    function formNum($value, $precision = 3)
    {
        if ($value === null) {
            return '-';
        }

        $num = (float) $value;

        // kalau 0, langsung return 0
        if ($num == 0) {
            return '0';
        }

        // format lalu buang nol di belakang
        return rtrim(rtrim(number_format($num, $precision, '.', ''), '0'), '.');
    }
@endphp
