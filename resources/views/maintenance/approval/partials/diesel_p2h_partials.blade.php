 @php
 $sipil = [
 'klakson' => [
 'label' => 'Check Klakson',
 'standar' => 'Bunyi ketika tombol ditekan',
 ],
 'buzzer_back' => [
 'label' => 'Check Buzzer Back',
 'standar' => 'Berbunyi normal saat maju dan mundur',
 ],
 'oli_mesin' => [
 'label' => 'Check Kondisi & Level Oli Mesin',
 'standar' => 'Berada di level max dan tidak ada kebocoran',
 ],
 'radiator_hose' => [
 'label' => 'Check Kondisi Level Radiator & Hose',
 'standar' => 'Berada di level max dan tidak ada kebocoran',
 ],
 'water_pump' => [
 'label' => 'Check Water Pump',
 'standar' => 'Tidak ada kebocoran',
 ],
 'injection_system' => [
 'label' => 'Check Injection Pump, Injector & Piping',
 'standar' => 'Tidak ada kebocoran',
 ],
 'fan_vbelt' => [
 'label' => 'Check Fan & V-Belt',
 'standar' => 'Berfungsi baik dan V-belt tidak retak atau putus',
 ],
 'turbocharger_manifold' => [
 'label' => 'Check Turbocharger & Manifold',
 'standar' => 'Berfungsi baik dan terlubrikasi',
 ],
 'tensioner_belt' => [
 'label' => 'Check Automatic Tensioner Belt',
 'standar' => 'Berfungsi dengan baik',
 ],
 'starting_motor' => [
 'label' => 'Check Fungsi Starting Motor',
 'standar' => 'Berfungsi dengan baik',
 ],
 'alternator' => [
 'label' => 'Check Fungsi Alternator',
 'standar' => 'Berfungsi dengan baik',
 ],
 'control_display' => [
 'label' => 'Check Control Display',
 'standar' => 'Berfungsi normal, tidak pecah, dan tidak ada alarm',
 ],
 'oli_transmisi' => [
 'label' => 'Check Kondisi & Level Oli Transmisi',
 'standar' => 'Berada di level max dan tidak ada kebocoran',
 ],
 'aki' => [
 'label' => 'Check Kondisi Aki & Level Air Aki',
 'standar' => 'Level max, aki tidak drop, dan bersih',
 ],
 'engine_mounting' => [
 'label' => 'Check Engine Mounting',
 'standar' => 'Berfungsi dengan baik',
 ],
 'filter_oli_transmisi' => [
 'label' => 'Check Filter Oli Transmisi',
 'standar' => 'Tidak ada kebocoran oli',
 ],
 'fungsi_rem' => [
 'label' => 'Check Fungsi Rem',
 'standar' => 'Berfungsi dengan baik dan tidak blong',
 ],
 'fungsi_kopling' => [
 'label' => 'Check Fungsi Kopling',
 'standar' => 'Berfungsi dengan baik dan tidak macet',
 ],
 'oli_hydraulic' => [
 'label' => 'Check Kondisi & Level Oli Hydraulic',
 'standar' => 'Berada di level max dan tidak ada kebocoran',
 ],
 'hydraulic_system' => [
 'label' => 'Check Fungsi Hydraulic System',
 'standar' => 'Berfungsi dengan baik dan terlubrikasi',
 ],
 'steering_system' => [
 'label' => 'Check Fungsi Steering System',
 'standar' => 'Tidak berat dan bergerak lancar',
 ],
 'body_back_rest' => [
 'label' => 'Check Kondisi Back Rest & Body',
 'standar' => 'Tidak ada cacat atau penyok',
 ],
 'kaca_spion' => [
 'label' => 'Check Kaca Spion',
 'standar' => 'Terpasang lengkap dan tidak pecah',
 ],
 'bucket_pin' => [
 'label' => 'Check Kondisi Bucket & Pin Bucket',
 'standar' => 'Berfungsi baik dan tidak retak atau hilang',
 ],
 'dump_pin_bushing' => [
 'label' => 'Check Kondisi Dump, Pin & Bushing',
 'standar' => 'Berfungsi dan tidak retak atau hilang',
 ],
 'seal_hydraulic' => [
 'label' => 'Check Kondisi Seal Hydraulic',
 'standar' => 'Tidak ada kebocoran oli',
 ],
 'roda_ban_baut' => [
 'label' => 'Check Kondisi Roda, Ban & Baut',
 'standar' => 'Ban layak pakai dan baut terpasang kencang',
 ],
 'lampu_unit' => [
 'label' => 'Check Lampu Depan & Belakang (Kanan & Kiri)',
 'standar' => 'Menyala normal dan tidak pecah',
 ],
 'baut_bearing_molen' => [
 'label' => 'Check Baut Bearing Molen & Gandengan',
 'standar' => 'Baut terpasang utuh dan kencang',
 ],
 'baut_hanger_as' => [
 'label' => 'Check Baut Hanger As Roda',
 'standar' => 'Baut terpasang utuh dan kencang',
 ],
 'baut_grease' => [
 'label' => 'Check Kondisi Baut Grease',
 'standar' => 'Baut tidak aus dan terlumasi grease',
 ],
 'katup_pembuangan_angin' => [
 'label' => 'Check Katup Pembuangan Angin',
 'standar' => 'Berfungsi dengan baik',
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

 <h5 class="mb-3">Mtc Diesel P2H Inspection</h5>

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

 <h6 class="mb-2">Checklist Diesel P2H</h6>

 <table class="table table-sm table-bordered align-middle">
     <thead>
         <tr class="table-light text-center">
             <th>Item</th>
             <th>Standar</th>
             <th>Status</th>
         </tr>
     </thead>
     <tbody>
         @foreach ($sipil as $field => $item)
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