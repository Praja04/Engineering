 @php
 $checklist = [
 'Forklift Electrical' => [
 'check_buzzer_back' => 'Check Buzzer Back',
 'check_klakson' => 'Check Klakson',
 'check_pilot_lamp' => 'Check Pilot Lamp',
 'check_lampu_sorot' => 'Check Lampu Sorot',
 'check_lampu_kombinasi_kanan_belakang' => 'Check Lampu Kombinasi Kanan Belakang',
 'check_lampu_kombinasi_kiri_belakang' => 'Check Lampu Kombinasi Kiri Belakang',
 'check_kaca_sepion' => 'Check Kaca Spion',
 ],

 'Battery Charger Electrical' => [
 'check_battery' => 'Check Battery',
 'check_skun_battery' => 'Check Skun Battery',
 'check_terminal_charger_battery' => 'Check Terminal Charger Battery',
 'check_kunci_kontak' => 'Check Kunci Kontak',
 'check_main_contactor' => 'Check Main Contactor',
 'check_microswitch' => 'Check Microswitch',
 'check_eps_controller' => 'Check EPS Controller',
 'check_steering_motor' => 'Check Steering Motor (Brush & Commutator)',
 'check_fan' => 'Check Fan',
 'check_fuse' => 'Check Fuse',
 'check_display_control' => 'Check Display Control',
 'check_wiring_terminal' => 'Check Wiring & Terminal',
 'check_carbon_brush' => 'Check Carbon Brush',
 ],

 'Drive Steering Hydraulic Braking' => [
 'check_steering_wheel' => 'Check Steering Wheel',
 'check_baut_roda' => 'Check Baut Roda',
 'check_drive_caster_load_wheel' => 'Check Drive, Caster & Load Wheel',
 'check_lift_chain' => 'Check Lift Chain',
 'check_lift_bracket' => 'Check Lift Bracket',
 'check_hydraulic_hose' => 'Check Hydraulic Hose',
 'check_motor_hydraulic_pump' => 'Check Motor Hydraulic Pump',
 'check_fork' => 'Check Fork',
 'check_lift_rollers' => 'Check Lift Rollers',
 'check_mast_rollers' => 'Check Mast Rollers',
 'check_lift_cylinders' => 'Check Lift Cylinders',
 'check_tilt_cylinders' => 'Check Tilt Cylinders',
 'check_control_valve' => 'Check Control Valve',
 'check_hydraulic_tank' => 'Check Hydraulic Tank',
 'check_overhead_guard' => 'Check Overhead Guard (Pelindung Kemudi)',
 'check_all_bolt_nut' => 'Check All Bolt & Nut',
 'check_power_steering' => 'Check Power Steering',
 'check_brake_cam_adjust_bolt' => 'Check Brake Cam & Adjust Bolt',
 'check_axle' => 'Check Axle',
 'check_greasing_point' => 'Check Greasing Point',
 'check_air_spring' => 'Check Air Spring',
 ],
 'Oil' => [
 'ganti_gear_oil' => 'Ganti Gear Oil',
 'ganti_hydraulic_oil' => 'Ganti Hydraulic Oil',
 'ganti_return_filter' => 'Ganti Return Filter',
 'ganti_brake_oil' => 'Ganti Brake Oil',
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

 <h5 class="mb-3">Mtc Electric Engine Inspection</h5>

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
         <td>: {{ $main->tanggal ?? '-' }}</td>
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
         <td>: {{ $main->paket ?? '-' }}</td>
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