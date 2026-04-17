 <div class="modal-body p-0">
     <!-- Tabs -->
     <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3 px-3 pt-3" role="tablist">
         <li class="nav-item">
             <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#info-pane">Informasi</button>
         </li>
         <li class="nav-item">
             <button class="nav-link" data-bs-toggle="tab" data-bs-target="#measurement-pane">Data
                 Pengukuran</button>
         </li>
         <li class="nav-item">
             <button class="nav-link" data-bs-toggle="tab" data-bs-target="#summary-pane">Summary</button>
         </li>
         <li class="nav-item">
             <button class="nav-link" data-bs-toggle="tab" data-bs-target="#final-pane">Final
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

         <!-- Tab 2: Data Pengukuran -->
         <div class="tab-pane fade" id="measurement-pane">
             <div class="card border-success mb-3">
                 <div class="card-header bg-soft-primary text-primary">
                     <i class="bi bi-rulers me-2"></i> Data Pengukuran
                 </div>
                 <div class="card-body p-0">
                     <div class="table-responsive">
                         <table class="table table-sm text-center mb-0">
                             <thead class="table-light align-middle">
                                 <tr>
                                     <th>N</th>
                                     <th>Nilai Master (mm)</th>
                                     <th>Nilai Pembacaan (mm)</th>
                                 </tr>
                             </thead>
                             <tbody id="detail_pengukuran">
                                 @foreach ($main->jangkaSorong as $js)
                                     <tr class="table-secondary">
                                         <td colspan="3" class="fw-bold text-start">
                                             Master: {{ formNum($js->master->nilai_master) }} mm
                                         </td>
                                     </tr>

                                     @foreach ($js->details as $detail)
                                         <tr>
                                             <td>{{ $detail->no_pengulangan }}</td>
                                             <td>{{ formNum($detail->nilai_master) }}</td>
                                             <td>{{ formNum($detail->nilai_pembacaan) }}</td>
                                         </tr>
                                     @endforeach
                                 @endforeach
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>
         </div>

         <!-- Tab 3: Summary -->
         <div class="tab-pane fade" id="summary-pane">
             <div class="card border-success mb-3">
                 <div class="card-header bg-soft-primary text-primary">
                     <i class="bi bi-bar-chart-line me-2"></i> Hasil Perhitungan Summary
                 </div>
                 <div class="card-body p-0">
                     <div class="table-responsive">
                         <table class="table table-hover table-sm text-center mb-0">
                             <thead class="table-light">
                                 <tr>
                                     <th>No Master</th>
                                     <th>Nilai Master (mm)</th>
                                     <th>Avg Pembacaan (mm)</th>
                                     <th>Std Deviasi</th>
                                     <th>Koreksi</th>
                                 </tr>
                             </thead>
                             <tbody id="detail_summary">
                                 @foreach ($main->jangkaSorong as $js)
                                     <tr>
                                         <td>{{ $js->master->no }}</td>
                                         <td>{{ formNum($js->master->nilai_master) }}</td>
                                         <td>{{ formNum($js->avg_pembacaan) }}</td>
                                         <td>{{ formNum($js->std_dev) }}</td>
                                         <td>{{ formNum($js->koreksi) }}</td>
                                     </tr>
                                 @endforeach
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>
         </div>

         <!-- Tab 4: Final Summary -->
         <div class="tab-pane fade" id="final-pane">
             <div class="card border-success">
                 <div class="card-header bg-soft-primary text-primary">
                     <i class="bi bi-clipboard-check me-2"></i> Final Summary
                 </div>
                 <div class="card-body p-0">
                     <div class="table-responsive">
                         <table class="table table-hover table-sm text-center mb-0">
                             <thead class="table-light">
                                 <tr>
                                     <th>Std Dev Total</th>
                                     <th>Ketidakpastian</th>
                                     <th>K (K=2)</th>
                                 </tr>
                             </thead>
                             <tbody id="detail_final_summary">
                                 @foreach ($main->jangkaSorongSummary as $smry)
                                     <tr>
                                         <td>{{ formNum($smry->std_dev_total) }}</td>
                                         <td>{{ formNum($smry->ketidakpastian) }}</td>
                                         <td>{{ formNum($smry->k_2) }}</td>
                                     </tr>
                                 @endforeach
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>
         </div>
     </div> <!-- /tab-content -->
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
