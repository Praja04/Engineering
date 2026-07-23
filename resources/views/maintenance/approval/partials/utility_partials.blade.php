@php
    $checklist = [
        'cooling_tower' => [
            'cleaning_saringan_cooling_tower' => 'Cleaning Saringan Bak Cooling Tower',
            'cleaning_unit_cooling_tower' => 'Cleaning Unit Cooling Tower',
            'cleaning_bak_cooling_tower' => 'Cleaning Bak Cooling Tower',
        ],

        'ro' => [
            'check_sensor_tank_farm_ro_produk' => 'Check Sensor Tank Farm RO Produk',
            'cleaning_flow_rate_mmf_1' => 'Cleaning Flow Rate MMF #1',
            'cleaning_flow_rate_mmf_2' => 'Cleaning Flow Rate MMF #2',
            'cleaning_flow_rate_ro_produk' => 'Cleaning Flow Rate RO Produk',
            'cleaning_flow_rate_ro_reject' => 'Cleaning Flow Rate RO Reject',
            'penggantian_micron_filter_cip' => 'Penggantian Micron Filter CIP',
            'penggantian_micron_filter_makeup_water' => 'Penggantian Micron Filter Make Up Water',
            'cleaning_cip_tank' => 'Cleaning CIP Tank',
            'cip_membrane_reverse_osmosis' => 'CIP Membrane Reverse Osmosis',
            'check_fungsi_valve' => 'Check Fungsi Valve',
            'cleaning_unit_ro_mesin' => 'Cleaning Unit RO Mesin',
        ],

        'compressor' => [
            'sirkulasi_phe_aq55vsd' => 'Sirkulasi PHE AQ55VSD',
            'penggantian_air_ro_aq55vsd' => 'Penggantian Air RO AQ55VSD',
            'cleaning_compressor_aq55vsd' => 'Cleaning Compressor AQ55VSD',
            'cleaning_jalur_cooling_aq55vsd' => 'Cleaning Jalur Cooling AQ55VSD',
            'cleaning_dryer_fd185' => 'Cleaning Dryer FD185',
            'cleaning_compressor_ga37' => 'Cleaning Compressor GA37',
            'cleaning_dryer_fd120' => 'Cleaning Dryer FD120',
            'lubrikasi_motor_compressor_aq55vsd' => 'Lubrikasi Motor Compressor AQ55VSD',
            'cleaning_compressor_sm55' => 'Cleaning Compressor SM55',
        ],

        'tank_farm' => [
            'cleaning_sensor_level_tank_farm' => 'Cleaning Sensor Level Tank Farm',
            'cleaning_sensor_level_fresh_water_menara' => 'Cleaning Sensor Level Fresh Water Menara',
            'cleaning_sensor_level_ro_reject_menara' => 'Cleaning Sensor Level RO Reject Menara',
            'cleaning_sensor_level_intermediate' => 'Cleaning Sensor Level Intermediate',
        ],

        'boiler' => [
            'check_safety_valve' => 'Check Safety Valve',
            'cleaning_level_gauge' => 'Cleaning Level Gauge',
            'cleaning_level_transmitter' => 'Cleaning Level Transmitter',
            'check_pressure_transmitter' => 'Check Pressure Transmitter',
            'check_temperature_transmitter' => 'Check Temperature Transmitter',
            'cleaning_sensor_o2_co2' => 'Cleaning Sensor O2 & CO2',
            'check_chaingrate' => 'Check Chaingrate',
            'check_ruang_bakar' => 'Check Ruang Bakar',
            'check_back_chamber' => 'Check Back Chamber',
            'check_guillotine' => 'Check Guillotine',
            'check_wet_ash_conveyor' => 'Check Wet Ash Conveyor',
            'check_bottom_ash_conveyor' => 'Check Bottom Ash Conveyor',
            'check_conveyor_batu_bara' => 'Check Conveyor Batu Bara',
            'check_feeder' => 'Check Feeder',
            'cleaning_bak_wet_ash_conveyor' => 'Cleaning Bak Wet Ash Conveyor',
            'check_feed_tank' => 'Check Feed Tank',
        ],

        'wwtp' => [
            'check_line_limbah' => 'Check Line Limbah',
            'check_line_chemical' => 'Check Line Chemical',
            'check_tangki_kotak' => 'Check Tangki Kotak',
            'check_tangki_bulat' => 'Check Tangki Bulat',
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

<h5 class="mb-3">Mtc Utility Inspection</h5>

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
        <th>Tanggal</th>
        <td>: {{ $main->tanggal }}</td>
    </tr>
    <tr>
        <th>Waktu Mulai</th>
        <td>: {{ $main->waktu_mulai }}</td>
    </tr>
    <tr>
        <th>Waktu Selesai</th>
        <td>: {{ $main->waktu_selesai }}</td>
    </tr>
    <tr>
        <th>Paket</th>
        <td>: {{ $main->paket }}</td>
    </tr>
    <tr>
        <th>Departmene</th>
        <td>: {{ $main->departemen }}</td>
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
