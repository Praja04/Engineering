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
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#result-pane">Hitung
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
                <div class="card-header bg-soft-primary text-primary">
                    <i class="bi bi-arrow-up-circle-fill me-2"></i>
                    <strong>Pengukuran</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm text-center mb-0">
                            <thead class="table-light align-middle">
                                <tr>
                                    <th>Titik Kalibrasi</th>
                                    <th>Penunjuk Standar</th>
                                    <th>Penunjuk Alat</th>
                                    <th>Koreksi Standar</th>
                                    <th>Suhu Standar</th>
                                    <th>Koreksi Alat</th>
                                </tr>
                            </thead>
                            <tbody id="hitung_data">
                                @foreach ($main->temperature as $temperature)
                                    @php
                                        $detailCount = $temperature->details->count();
                                    @endphp

                                    @foreach ($temperature->details as $index => $detail)
                                        <tr>

                                            {{-- TAMPILKAN TITIK HANYA DI BARIS PERTAMA --}}
                                            @if ($index == 0)
                                                <td rowspan="{{ $detailCount }}" class="align-middle fw-bold">
                                                    {{ formNum($temperature->titik_kalibrasi) }}
                                                </td>
                                            @endif

                                            <td>{{ formNum($detail->penunjuk_standar) }}</td>
                                            <td>{{ formNum($detail->penunjuk_alat) }}</td>
                                            <td>{{ formNum($detail->koreksi_standar) }}</td>
                                            <td>{{ formNum($detail->suhu_standar) }}</td>
                                            <td>{{ formNum($detail->koreksi_alat) }}</td>
                                        </tr>
                                    @endforeach
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
                <div class="card-header bg-soft-primary text-primary">
                    <i class="bi bi-calculator-fill me-2"></i>
                    <strong>Hasil Perhitungan Summary</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Titik Kalibrasi (°C)</th>
                                    <th>Avg Penunjuk Alat</th>
                                    <th>Avg Suhu Standar</th>
                                    <th>Avg Kor Alat</th>
                                    <th>Std Deviasi</th>
                                    <th>Ketidakpastian</th>
                                </tr>
                            </thead>
                            <tbody id="detail_gabungan">
                                @foreach ($main->temperature as $temperature)
                                    <tr>
                                        <td>{{ formNum($temperature->titik_kalibrasi) }}</td>
                                        <td>{{ formNum($temperature->avg_penunjuk_alat) }}</td>
                                        <td>{{ formNum($temperature->avg_suhu_standar) }}</td>
                                        <td>{{ formNum($temperature->avg_kor_alat) }}</td>
                                        <td>{{ formNum($temperature->stdev) }}</td>
                                        <td>{{ formNum($temperature->ketidakpastian) }}</td>
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
