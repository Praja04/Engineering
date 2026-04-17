<div class="modal-body p-0">
    <!-- Tabs -->
    <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3 px-3 pt-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#info-pane">Informasi</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#keypad-pane"> Keypad
                Data</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#measurement-pane">Pengukuran</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pengukuran-detail-pane">Pengukuran
                Detail</button>
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

        <!-- Tab Keypad -->
        <div class="tab-pane fade" id="keypad-pane">
            <div class="card border-success">
                <div class="card-header bg-soft-primary text-primary">
                    <i class="bi bi-calculator-fill me-2"></i>
                    <strong>Keypad Data</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tested / Adjusted</th>
                                    <th>Measured Value</th>
                                    <th>Criterion / Tolerance</th>
                                    <th>Passed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($main->keypad as $kp)
                                    <tr>
                                        <td>
                                            @if ($kp->tested == 1)
                                                <span class="badge bg-success">
                                                    <i class="mdi mdi-check"></i>
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="mdi mdi-close"></i>
                                                </span>
                                            @endif
                                        </td>

                                        <td>{{ $kp->measured ?? '-' }}</td>
                                        <td>{{ $kp->criterion ?? '-' }}</td>

                                        <td>
                                            @if ($kp->passed)
                                                <span class="badge bg-success">YES</span>
                                            @else
                                                <span class="badge bg-danger">NO</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">Tidak ada data keypad</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Pengukuran -->
        <div class="tab-pane fade" id="measurement-pane">

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
                                    <th>Indikator</th>
                                    <th>Jenis Alat Ukur</th>
                                    <th>Jenis Standar</th>
                                    <th>Nilai Master</th>
                                    <th>Avg Pembacaan</th>
                                    <th>Std Dev</th>
                                    <th>Koreksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($main->instrumen->sortBy('titik_kalibrasi') as $ins)
                                    <tr>
                                        <td>{{ $ins->titik_kalibrasi ?? '-' }}</td>
                                        <td>{{ $ins->indikator ?? '-' }}</td>
                                        <td>{{ $ins->jenis_alat_ukur ?? '-' }}</td>
                                        <td>{{ $ins->jenis_standar ?? '-' }}</td>
                                        <td>{{ number_format($ins->nilai_master, 0, ',', '.') ?? '-' }}</td>
                                        <td>{{ number_format($ins->avg_pembacaan, 0, ',', '.') ?? '-' }}</td>
                                        <td>{{ number_format($ins->std_dev, 0, ',', '.') ?? '-' }}</td>
                                        <td>{{ number_format($ins->koreksi, 0, ',', '.') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-muted">Tidak ada data pengukuran</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="pengukuran-detail-pane">

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
                                    <th>Alat</th>
                                    <th>Standar</th>
                                    <th>Pembacaan Alat</th>
                                    <th>Pembacaan Standar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($main->instrumen as $ins)

                                    @php
                                        $detailCount = $ins->details->count();
                                    @endphp

                                    @forelse ($ins->details as $index => $detail)
                                        <tr>

                                            {{-- Titik Kalibrasi hanya tampil di baris pertama --}}
                                            @if ($index == 0)
                                                <td rowspan="{{ $detailCount }}">
                                                    {{ $ins->titik_kalibrasi ?? '-' }}
                                                </td>
                                            @endif

                                            <td>{{ number_format($detail->alat, 0, ',', '.') ?? '-' }}</td>
                                            <td>{{ number_format($detail->standar, 0, ',', '.') ?? '-' }}</td>
                                            <td>{{ number_format($detail->pembacaan_alat, 0, ',', '.') ?? '-' }}</td>
                                            <td>{{ number_format($detail->pembacaan_standar, 0, ',', '.') ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td>{{ $ins->titik_kalibrasi ?? '-' }}</td>
                                            <td colspan="4" class="text-muted">Tidak ada detail</td>
                                        </tr>
                                    @endforelse

                                @empty
                                    <tr>
                                        <td colspan="5" class="text-muted">Tidak ada data instrumen</td>
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
