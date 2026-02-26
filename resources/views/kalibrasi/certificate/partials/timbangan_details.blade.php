<div class="modal-body p-0">
    <!-- Tabs -->
    <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3 px-3 pt-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#info-pane">Informasi</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pembacaan-pane">Pembacaan</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#keseragaman-pane">Keseragaman
                Skala</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pinggan-pane">Pinggan</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tare-pane">Tare</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#histerisis-pane">Histerisis</button>
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

        {{-- TAB PEMBACAAN --}}
        <div class="tab-pane fade" id="pembacaan-pane">
            @php
                $grouped = $main->kemampuanUlang->groupBy('jenis');
            @endphp

            @forelse($grouped as $jenis => $rows)

                <h6 class="mt-3">
                    <strong>{{ str_replace('_', ' ', $jenis) }}</strong>
                </h6>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Ulangan</th>
                                <th>Nilai Z</th>
                                <th>Nilai M</th>
                                <th>Selisih</th>
                                <th>Maks. Perbedaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr>
                                    <td>{{ $row->ulangan }}</td>
                                    <td>{{ number_format($row->nilai_z, 0, ',', '.') }}</td>
                                    <td>{{ number_format($row->nilai_m, 0, ',', '.') }}</td>
                                    <td>{{ number_format($row->std_dev, 0, ',', '.') }}</td>
                                    <td>{{ number_format($row->maks_perbedaan, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @empty
                <p class="text-muted">Tidak ada data pembacaan.</p>
            @endforelse


            {{-- SUMMARY --}}
            <hr>

            @if ($main->kemampuanUlangSummary->count())
                <h6 class="mt-3"><strong>Summary Pembacaan</strong></h6>

                <table class="table table-bordered table-sm text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Jenis</th>
                            <th>Std Dev</th>
                            <th>Maks Perbedaan Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($main->kemampuanUlangSummary as $summary)
                            <tr>
                                <td>{{ str_replace('_', ' ', $summary->jenis) }}</td>
                                <td>{{ number_format($summary->std_dev, 0, ',', '.') }}</td>
                                <td>{{ number_format($summary->maks_perbedaan_akhir, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>

        {{-- Keseragaman Skala --}}
        <div class="tab-pane fade" id="keseragaman-pane">

            @php
                $grouped = $main->keseragamanSkala->groupBy('massa_ke');
            @endphp

            @forelse($grouped as $massaKe => $rows)

                <h6 class="mt-3"><strong>Massa ke-{{ $massaKe }}</strong></h6>

                <table class="table table-bordered table-sm text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Jenis</th>
                            <th>Beban</th>
                            <th>Pembacaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td>{{ $row->jenis }}</td>
                                <td>{{ number_format($row->beban, 0, ',', '.') }}</td>
                                <td>{{ number_format($row->pemacaan, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @empty
                <p class="text-muted">Tidak ada data keseragaman.</p>
            @endforelse


            {{-- SUMMARY --}}
            @if ($main->keseragamanSkalaSummary->count())
                <hr>
                <h6><strong>Summary</strong></h6>
                <table class="table table-bordered table-sm text-center">
                    <tr>
                        <th>Avg Z</th>
                        <th>Avg M</th>
                        <th>Selisih</th>
                        <th>Koreksi</th>
                    </tr>

                    @foreach ($main->keseragamanSkalaSummary as $summary)
                        <tr>
                            <td>{{ number_format($summary->avg_z, 0, ',', '.') }}</td>
                            <td>{{ number_format($summary->avg_m, 0, ',', '.') }}</td>
                            <td>{{ number_format($summary->selisih_zm, 0, ',', '.') }}</td>
                            <td>{{ number_format($summary->koreksi_skala, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

        </div>

        <!-- Tab Pinggan -->
        <div class="tab-pane fade" id="pinggan-pane">

            @forelse($main->pinggan as $pinggan)

                <h6 class="mt-3"><strong>Pinggan</strong></h6>

                <table class="table table-bordered table-sm text-center">
                    <tr>
                        <th>Diameter</th>
                        <th>Massa</th>
                    </tr>
                    <tr>
                        <td>{{ number_format($pinggan->diameter, 0, ',', '.') }}</td>
                        <td>{{ number_format($pinggan->massa, 0, ',', '.') }}</td>
                    </tr>
                </table>

                {{-- Detail Posisi --}}
                <table class="table table-bordered table-sm text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Percobaan</th>
                            <th>Posisi</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pinggan->details as $detail)
                            <tr>
                                <td>{{ $detail->percobaan }}</td>
                                <td>{{ $detail->posisi }}</td>
                                <td>{{ number_format($detail->nilai, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @empty
                <p class="text-muted">Tidak ada data pinggan.</p>
            @endforelse

        </div>

        <!-- Tab Tare -->
        <div class="tab-pane fade" id="tare-pane">

            @php
                $grouped = $main->tare->groupBy('kondisi');
            @endphp

            @forelse($grouped as $kondisi => $rows)

                <h6 class="mt-3">
                    <strong>{{ ucfirst($kondisi) }}</strong>
                </h6>

                <table class="table table-bordered table-sm text-center">
                    <thead>
                        <tr>
                            <th>Label</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td>{{ $row->label }}</td>
                                <td>{{ number_format($row->nilai, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @empty
                <p class="text-muted">Tidak ada data tare.</p>
            @endforelse

        </div>

        <!-- Tab Histerisis -->
        <div class="tab-pane fade" id="histerisis-pane">

            @php
                $grouped = $main->histerisis->groupBy('pengulangan');
            @endphp

            @forelse($grouped as $ulangan => $rows)

                <h6 class="mt-3"><strong>Percobaan {{ $ulangan }}</strong></h6>

                <table class="table table-bordered table-sm text-center">
                    <thead>
                        <tr>
                            <th>Label</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td>{{ strtoupper($row->label) }}</td>
                                <td>{{ number_format($row->nilai, 0, ',', '.') }}</td>
                                <td>{{ $row->nilai }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @empty
                <p class="text-muted">Tidak ada data histerisis.</p>
            @endforelse


            {{-- SUMMARY --}}
            @if ($main->histerisisSummary)
                <hr>
                <table class="table table-bordered table-sm text-center">
                    <tr>
                        <th>Rata M1-M2</th>
                        <th>Rata Z1-Z2</th>
                        <th>Nilai MZ</th>
                        <th>Histerisis</th>
                    </tr>
                    <tr>
                        <td>{{ number_format($main->histerisisSummary->avg_m1m2, 0, ',', '.') }}</td>
                        <td>{{ number_format($main->histerisisSummary->avg_z1z2, 0, ',', '.') }}</td>
                        <td>{{ number_format($main->histerisisSummary->nilai_mz, 0, ',', '.') }}</td>
                        <td>{{ number_format($main->histerisisSummary->histerisis, 0, ',', '.') }}</td>
                    </tr>
                </table>
            @endif

        </div>
    </div> <!-- /tab-content -->
</div>
