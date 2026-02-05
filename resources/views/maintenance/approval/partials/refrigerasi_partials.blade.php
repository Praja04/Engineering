 @php
     $checklist = [
         'Unit Indoor' => [
             'check_filter_udara' => 'Check Filter Udara',
             'check_cover_filter_udara' => 'Check Cover Filter Udara',
             'check_electrical_indoor' => 'Check Electrical (Indoor)',
             'check_suhu_evaporator' => 'Check Suhu Evaporator',
             'check_indikator_display' => 'Check Indikator Display',
             'check_motor_blower' => 'Check Motor Blower',
             'check_fan_belt_blower' => 'Check Fan Belt Blower',
             'check_pergerakan_motor_swing' => 'Check Pergerakan Motor Swing',
             'check_kontroler_indoor' => 'Check Kontroler Indoor',
             'check_saluran_drain_kondensasi' => 'Check Saluran Drain Kondensasi',
             'sirkulasi_evaporator' => 'Sirkulasi Evaporator',
         ],

         'Unit Outdoor' => [
             'check_kondisi_kondensor' => 'Check Kondisi Kondensor',
             'check_electrical_outdoor' => 'Check Electrical (Outdoor)',
             'check_motor_fan' => 'Check Motor Fan',
             'check_tekanan_freon' => 'Check Tekanan Freon',
             'pelumasan_motor_fan' => 'Pelumasan Motor Fan',
             'kebersihan_unit_body_outdoor' => 'Kebersihan Unit & Body Outdoor',
         ],

         'Jalur Distribusi' => [
             'check_jalur_freon' => 'Check Jalur Freon',
             'check_jalur_distribusi_udara' => 'Check Jalur Distribusi Udara',
             'check_jalur_return_udara' => 'Check Jalur Return Udara',
         ],
     ];

     function badge($v)
     {
         return $v === null
             ? '<span class="badge bg-secondary">No Check</span>'
             : ($v
                 ? '<span class="badge bg-success">OK</span>'
                 : '<span class="badge bg-danger">NG</span>');
     }
 @endphp

 <h5 class="mb-3">Mtc Refrigerasi Inspection</h5>

 <table class="table table-sm table-borderless mb-4">
     <tr>
         <th width="160">Mesin</th>
         <td>: {{ $data->mesin->nama_mesin ?? '-' }}</td>
     </tr>
     <tr>
         <th>Lokasi</th>
         <td>: {{ $data->mesin->lokasi ?? '-' }}</td>
     </tr>
     <tr>
         <th>Departemen</th>
         <td>: {{ $main->departemen ?? '-' }}</td>
     </tr>
     <tr>
         <th>Tanggal</th>
         <td>: {{ $main->tanggal }}</td>
     </tr>
     <tr>
         <th>Waktu</th>
         <td>: {{ $main->waktu }}</td>
     </tr>
     <tr>
         <th>Paket</th>
         <td>: {{ $main->paket }}</td>
     </tr>
     <tr>
         <th>Korektif</th>
         <td>: {{ $main->korektif ?? '-' }}</td>
     </tr>
     <tr>
         <th>Keterangan</th>
         <td>: {{ $main->keterangan ?? '-' }}</td>
     </tr>
 </table>

 <h6 class="mb-2">Checklist</h6>

 <table class="table table-sm table-bordered align-middle">
     <thead>
         <tr class="table-light text-center">
             <th width="120">Bagian</th>
             <th>Item</th>
             <th width="80">Status</th>
         </tr>
     </thead>
     <tbody>
         @foreach ($checklist as $section => $items)
             @php $rowspan = count($items); @endphp
             @foreach ($items as $field => $label)
                 <tr>
                     @if ($loop->first)
                         <td rowspan="{{ $rowspan }}" class="fw-bold align-middle text-center">
                             {{ $section }}
                         </td>
                     @endif
                     <td>{{ $label }}</td>
                     <td class="text-center">{!! badge($data->$field) !!}</td>
                 </tr>
             @endforeach
         @endforeach
     </tbody>
 </table>

 <h6 class="mt-4 mb-2">Kebutuhan Material</h6>

 @if ($main->kebutuhanMaterial->isEmpty())
     <div class="text-muted fst-italic">
         Tidak ada kebutuhan material
     </div>
 @else
     <table class="table table-sm table-bordered align-middle">
         <thead class="table-light text-center">
             <tr>
                 <th width="120">MID</th>
                 <th>Deskripsi</th>
                 <th width="80">Qty</th>
             </tr>
         </thead>
         <tbody>
             @foreach ($main->kebutuhanMaterial as $material)
                 <tr>
                     <td class="text-center">{{ $material->mid }}</td>
                     <td>{{ $material->deskripsi }}</td>
                     <td class="text-center">{{ $material->qty }}</td>
                 </tr>
             @endforeach
         </tbody>
     </table>
 @endif
