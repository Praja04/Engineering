 @php
 $checklistItems = [
     'level_oli_mesin' => [
         'label' => 'Level Oli Mesin',
         'standar' => 'Berada dilevel Max',
     ],
     'kebocoran_oli_mesin' => [
         'label' => 'Kebocoran Oli Mesin',
         'standar' => 'Tidak ada kebocoran',
     ],
     'level_coolant_radiator' => [
         'label' => 'Level Coolant/Radiator',
         'standar' => 'Berada dilevel Max',
     ],
     'kebocoran_coolant' => [
         'label' => 'Kebocoran Coolant',
         'standar' => 'Tidak ada kebocoran',
     ],
     'level_bahan_bakar' => [
         'label' => 'Level Bahan Bakar',
         'standar' => 'Berada dilevel Max',
     ],
     'kebocoran_bahan_bakar' => [
         'label' => 'Kebocoran Bahan Bakar',
         'standar' => 'Tidak ada kebocoran',
     ],
     'kondisi_aki_baterai' => [
         'label' => 'Kondisi Aki/Baterai',
         'standar' => 'Terminal bersih , tidak korosi',
     ],
     'tegangan_baterai' => [
         'label' => 'Tegangan Baterai',
         'standar' => 'Normal',
     ],
     'filter_udara' => [
         'label' => 'Filter Udara',
         'standar' => 'Bersih',
     ],
     'kondisi_panel_genset' => [
         'label' => 'Kondisi Panel Genset',
         'standar' => 'Bersih, Indicator Normal',
     ],
     'emergency_stop' => [
         'label' => 'Emergency Stop',
         'standar' => 'Tidak ada alarm',
     ],
     'suara_mesin_running' => [
         'label' => 'Suara Mesin Saat Running',
         'standar' => 'Halus , Tidak Kasar',
     ],
     'kebersihan_area_genset' => [
         'label' => 'Kebersihan Area Genset',
         'standar' => 'Bersih',
     ],
     'kondisi_knalpot_exhaust' => [
         'label' => 'Kondisi Knalpot/Exhaust',
         'standar' => 'Tidak bocor',
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

 <h5 class="mb-3">Mtc Genset P2H Inspection</h5>

 <table class="table table-sm table-borderless mb-4">
     <tr>
         <th>Tanggal</th>
         <td>: {{ $main->tanggal }}</td>
     </tr>
     <tr>
         <th>Waktu Mulai</th>
         <td>: {{ $main->waktu_mulai ?? '-' }}</td>
     </tr>
     <tr>
         <th>Waktu Selesai</th>
         <td>: {{ $main->waktu_selesai ?? '-' }}</td>
     </tr>
     <tr>
         <th>Departemen</th>
         <td>: {{ $main->departemen ?? '-' }}</td>
     </tr>
     <tr>
         <th>No Unit</th>
         <td>: {{ $data->no_unit }}</td>
     </tr>
     <tr>
         <th>Shift</th>
         <td>: {{ $data->shift }}</td>
     </tr>
     <tr>
         <th>Hours Meter</th>
         <td>: {{ $data->hours_meter ?? '-' }}</td>
     </tr>
     <tr>
         <th>Catatan</th>
         <td>: {{ $data->catatan ?? '-' }}</td>
     </tr>
     <tr>
         <th>Keterangan NOK</th>
         <td>: {{ $main->keterangan ?? '-' }}</td>
     </tr>
 </table>

 <h6 class="mb-2">Checklist Genset P2H</h6>

 <table class="table table-sm table-bordered align-middle">
     <thead>
         <tr class="table-light text-center">
             <th>Item</th>
             <th>Standar</th>
             <th>Status</th>
         </tr>
     </thead>
     <tbody>
         @foreach ($checklistItems as $field => $item)
         <tr>
             <td class="fw-semibold">{{ $item['label'] }}</td>
             <td>{{ $item['standar'] }}</td>
             <td class="text-center">
                 {!! badge($data->$field ?? null) !!}
             </td>
         </tr>
         @endforeach
     </tbody>
 </table>
