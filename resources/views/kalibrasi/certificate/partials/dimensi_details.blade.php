 <div class="modal-body p-0">
     <!-- Tabs -->
     <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3 px-3 pt-3" role="tablist">
         <li class="nav-item">
             <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#info-pane">Informasi</button>
         </li>
         <li class="nav-item">
             <button class="nav-link" data-bs-toggle="tab" data-bs-target="#measurement-pane">Pengukuran Detail</button>
         </li>
         <li class="nav-item">
             <button class="nav-link" data-bs-toggle="tab" data-bs-target="#result-pane">
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
                                 </tr>
                             </thead>
                             <tbody id="hitung_data">
                                 @foreach ($main->dimensi as $dimensi)
                                     @php
                                         $detailCount = $dimensi->details->count();
                                     @endphp
                                     @foreach ($dimensi->details as $index => $detail)
                                         <tr>
                                             @if ($index == 0)
                                                 <td rowspan="{{ $detailCount }}" class="align-middle fw-bold">
                                                     {{ number_format($dimensi->titik_kalibrasi, 0, ',', '.') }}</td>
                                             @endif
                                             <td>{{ number_format($detail->penunjuk_standar, 0, ',', '.') }}</td>
                                             <td>{{ number_format($detail->penunjuk_alat, 0, ',', '.') }}</td>
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
                                     <th>Titik Kalibrasi</th>
                                     <th>Nilai Master</th>
                                     <th>Avg Pembacaan</th>
                                     <th>Koreksi</th>
                                     <th>Std Dev</th>
                                     <th>Ketidakpastian</th>
                                 </tr>
                             </thead>
                             <tbody id="detail_gabungan">
                                 @foreach ($main->dimensi as $dimensi)
                                     <tr>
                                         <td>{{ number_format($dimensi->titik_kalibrasi, 0, ',', '.') }}</td>
                                         <td>{{ number_format($dimensi->nilai_master, 0, ',', '.') }}</td>
                                         <td>{{ number_format($dimensi->avg_pembacaan, 0, ',', '.') }}</td>
                                         <td>{{ number_format($dimensi->koreksi, 0, ',', '.') }}</td>
                                         <td>{{ number_format($dimensi->std_dev, 0, ',', '.') }}</td>
                                         <td>{{ number_format($dimensi->ketidakpastian, 0, ',', '.') }}</td>
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
