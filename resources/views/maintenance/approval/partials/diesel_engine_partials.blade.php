 @php
 $checklist = [
 'Engine' => [
 'check_kondisi_level_oli_mesin' => 'Check Kondisi & Level Oli Mesin',
 'check_kondisi_radiator_hose' => 'Check Kondisi Radiator & Hose',
 'check_kondisi_level_air_radiator' => 'Check Kondisi & Level Air Radiator',
 'check_water_pump' => 'Check Water Pump',
 'check_injection_pump_injector_piping' => 'Check Injection Pump, Injector, Piping',
 'check_turbocharger_manifold' => 'Check Turbocharger & Manifold',
 'check_fan_v_belt' => 'Check Fan & V-Belt',
 'check_automatic_tensioner_belt' => 'Check Automatic Tensioner Belt',
 'check_engine_mounting' => 'Check Engine Mounting',
 'check_air_filter_condition' => 'Check Air Filter Condition',
 'check_clearence_valve_drain_valve' => 'Check Clearence Valve (Drain Valve)',
 'check_engine_oil_filter' => 'Check Engine Oil Filter',
 'check_air_radiator' => 'Check Air Radiator',
 'check_minyak_kopling' => 'Check Minyak Kopling',
 'check_fuel_filter' => 'Check Fuel Filter',
 ],

 'Electric' => [
 'check_kondisi_aki_level_air_aki' => 'Check Kondisi Aki & Level Air Aki',
 'check_fungsi_starting_motor' => 'Check Fungsi Starting Motor',
 'check_fungsi_alternator' => 'Check Fungsi Alternator',
 'check_sensor_sensor_gauge' => 'Check Sensor-sensor & Gauge',
 'check_fuse_control_switch' => 'Check Fuse & Control Switch',
 'check_control_display' => 'Check Control Display',
 'check_indicator_wiring' => 'Check Indicator & Wiring',
 ],

 'Transmisi Brake Drive Shaft' => [
 'check_kondisi_level_oli_transmisi' => 'Check Kondisi & Level Oli Transmisi',
 'check_fungsi_transmisi' => 'Check Fungsi Transmisi',
 'check_filter_oli_transmisi' => 'Check Filter Oli Transmisi',
 'check_fungsi_rem' => 'Check Fungsi Rem',
 'check_oli_tidak_ada_yang_bocor' => 'Check Oli Tidak Ada yang Bocor',
 'check_kondisi_drive_shaft' => 'Check Kondisi Drive Shaft',
 ],
 'Hydraulic' => [
 'check_kondisi_level_hydraulic_oil' => 'Check Kondisi & Level Hydraulic Oil',
 'check_kondisi_hydraulic_oil_filter' => 'Check Kondisi Hydraulic Oil Filter',
 'check_fungsi_hydraulic_system' => 'Check Fungsi Hydraulic System',
 'check_fungsi_steering_system' => 'Check Fungsi Steering System',
 'check_kondisi_hydraulic_cylinder' => 'Check Kondisi Hydraulic Cylinder',
 'check_kondisi_steering_cylinder' => 'Check Kondisi Steering Cylinder',
 'check_kondisi_axle_oil' => 'Check Kondisi Axle Oil',
 'check_kondisi_baut_roda_hydraulic' => 'Check Kondisi Baut Roda',
 'check_kondisi_bucket_pin_bucket' => 'Check Kondisi Bucket, Pin Bucket',
 'check_kondisi_dump_pin_bushing' => 'Check Kondisi Dump, Pin & Bushing',
 ],
 'General' => [
 'check_klakson' => 'Check Klakson',
 'check_buzzer_back' => 'Check Buzzer Back',
 'check_kondisi_basket_fresh_body' => 'Check Kondisi Basket Fresh & Body',
 'check_kaca_sepion' => 'Check Kaca Spion',
 'check_kondisi_roda_ban' => 'Check Kondisi Roda/Ban',
 'check_baut_roda_general' => 'Check Baut Roda',
 'check_lampu_depan_kanan' => 'Check Lampu Depan Kanan',
 'check_lampu_depan_kiri' => 'Check Lampu Depan Kiri',
 'check_baut_bearing_molen' => 'Check Baut Bearing Molen',
 'check_baut_hanger_as_roda' => 'Check Baut Hanger As Roda',
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

 <h5 class="mb-3">Mtc Diesel Engine Inspection</h5>

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
         <th>Waktu Mulai</th>
         <td>: {{ $main->waktu_mulai ?? '-' }}</td>
     </tr>
     <tr>
         <th>Waktu Selesai</th>
         <td>: {{ $main->waktu_selesai ?? '-' }}</td>
     </tr>
     <tr>
         <th>Paket</th>
         <td>: {{ $main->paket }}</td>
     </tr>
     <tr>
         <th>Running Hour</th>
         <td>: {{ $main->running_hour ?? '-' }}</td>
     </tr>
     <tr>
         <th>Korektif</th>
         <td>: {{ $main->korektif ?? '-' }}</td>
     </tr>
     <tr>
         <th>Keterangan NOK</th>
         <td>: {{ $main->keterangan ?? '-' }}</td>
     </tr>
 </table>

 <h6 class="mb-2">Checklist</h6>

 <table class="table table-sm table-bordered align-middle">
     <thead>
         <tr class="table-light text-center">
             <th>Bagian</th>
             <th>Item</th>
             <th>Status</th>
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

 <h6 class="mt-4 mb-2">Penggantian Material</h6>

 @if ($main->penggantianMaterial->isEmpty())
 <div class="text-muted fst-italic">
     Tidak ada penggantian material
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
         @foreach ($main->penggantianMaterial as $replacement)
         <tr>
             <td class="text-center">{{ $replacement->mid }}</td>
             <td>{{ $replacement->deskripsi }}</td>
             <td class="text-center">{{ $replacement->qty }}</td>
         </tr>
         @endforeach
     </tbody>
 </table>
 @endif