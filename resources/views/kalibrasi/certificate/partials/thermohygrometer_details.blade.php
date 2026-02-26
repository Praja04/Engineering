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
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#result-pane">Perhitungan
                Summary</button>
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
            <!-- Suhu -->
            <div class="card mb-3 border-danger">
                <div class="card-header bg-danger text-white">
                    <i class="bi bi-thermometer-half me-2"></i><strong>Pengukuran Suhu (°C)</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm text-center mb-0">
                            <thead class="table-light align-middle">
                                <tr>
                                    <th>Titik / Posisi Bagian</th>
                                    <th>Penunjuk Standar</th>
                                    <th>Penunjuk Alat</th>
                                    <th>Koreksi Standar</th>
                                    <th>Tekanan Standar</th>
                                    <th>Koreksi Alat</th>
                                    <th>Ketidakpastian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($main->thermohygrometer as $item)
                                    @foreach ($item->details as $detail)
                                        <tr>
                                            <td>{{ $item->titik_kalibrasi }}</td>
                                            <td>{{ number_format($detail->penunjuk_standar_suhu, 0, ',', '.') }}</td>
                                            <td>{{ number_format($detail->penunjuk_alat_suhu, 0, ',', '.') }}</td>
                                            <td>{{ number_format($detail->koreksi_standar_suhu, 0, ',', '.') }}</td>
                                            <td>{{ number_format($detail->tekanan_standar_suhu, 0, ',', '.') }}</td>
                                            <td>{{ number_format($detail->koreksi_alat_suhu, 0, ',', '.') }}</td>
                                            <td>{{ number_format($item->ketidak_pastian_suhu, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RH -->
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-droplet-half me-2"></i><strong>Pengukuran Kelembaban (RH %)</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm text-center mb-0">
                            <thead class="table-light align-middle">
                                <tr>
                                    <th>Titik / Posisi Bagian</th>
                                    <th>Penunjuk Standar</th>
                                    <th>Penunjuk Alat</th>
                                    <th>Koreksi Standar</th>
                                    <th>Tekanan Standar</th>
                                    <th>Koreksi Alat</th>
                                    <th>Ketidakpastian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($main->thermohygrometer as $item)
                                    @foreach ($item->details as $detail)
                                        <tr>
                                            <td>{{ $item->titik_kalibrasi }}</td>
                                            <td>{{ number_format($detail->penunjuk_standar_rh, 0, ',', '.') }}</td>
                                            <td>{{ number_format($detail->penunjuk_alat_rh, 0, ',', '.') }}</td>
                                            <td>{{ number_format($detail->koreksi_standar_rh, 0, ',', '.') }}</td>
                                            <td>{{ number_format($detail->tekanan_standar_rh, 0, ',', '.') }}</td>
                                            <td>{{ number_format($detail->koreksi_alat_rh, 0, ',', '.') }}</td>
                                            <td>{{ number_format($item->ketidak_pastian_rh, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Hasil Gabungan -->
        <div class="tab-pane fade" id="result-pane">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-bar-chart-fill me-2"></i><strong>Hasil Perhitungan Gabungan</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Posisi Bagian</th>
                                    <th>Avg. Penunjuk Alat (°C)</th>
                                    <th>Avg. Penunjuk Alat (RH %)</th>
                                    <th>Standar Deviasi (°C)</th>
                                    <th>Standar Deviasi (RH %)</th>
                                    <th>Ketidakpastian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($main->thermohygrometer as $item)
                                    <tr>
                                        <td>{{ $item->titik_kalibrasi }}</td>
                                        <td>{{ number_format($item->avg_penunjuk_alat_suhu, 0, ',', '.') }}</td>
                                        <td>{{ number_format($item->avg_penunjuk_alat_rh, 0, ',', '.') }}</td>
                                        <td>{{ number_format($item->std_deviasi_suhu, 0, ',', '.') }}</td>
                                        <td>{{ number_format($item->std_deviasi_rh, 0, ',', '.') }}</td>
                                        <td>{{ number_format($item->ketidakpastian_suhu, 0, ',', '.') }}
                                            <br>{{ number_format($item->ketidakpastian_rh, 0, ',', '.') }}
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
