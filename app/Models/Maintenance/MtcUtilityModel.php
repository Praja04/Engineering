<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcUtilityModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_utility';

    protected $fillable = [
        'nama_mesin',
        'tanggal',
        'waktu',
        'paket',

        // Cooling Tower
        'cleaning_saringan_cooling_tower',
        'cleaning_unit_cooling_tower',
        'cleaning_bak_cooling_tower',

        // RO
        'check_sensor_tank_farm_ro_produk',
        'cleaning_flow_rate_mmf_1',
        'cleaning_flow_rate_mmf_2',
        'cleaning_flow_rate_ro_produk',
        'cleaning_flow_rate_ro_reject',
        'penggantian_micron_filter_cip',
        'penggantian_micron_filter_makeup_water',
        'cleaning_cip_tank',
        'cip_membrane_reverse_osmosis',
        'check_fungsi_valve',
        'cleaning_unit_ro_mesin',

        // Compressor
        'sirkulasi_phe_aq55vsd',
        'penggantian_air_ro_aq55vsd',
        'cleaning_compressor_aq55vsd',
        'cleaning_jalur_cooling_aq55vsd',
        'cleaning_dryer_fd185',
        'cleaning_compressor_ga37',
        'cleaning_dryer_fd120',
        'lubrikasi_motor_compressor_aq55vsd',
        'cleaning_compressor_sm55',

        // Tank Farm
        'cleaning_sensor_level_tank_farm',
        'cleaning_sensor_level_fresh_water_menara',
        'cleaning_sensor_level_ro_reject_menara',
        'cleaning_sensor_level_intermediate',

        // Boiler
        'check_safety_valve',
        'cleaning_level_gauge',
        'cleaning_level_transmitter',
        'check_pressure_transmitter',
        'check_temperature_transmitter',
        'cleaning_sensor_o2_co2',
        'check_chaingrate',
        'check_ruang_bakar',
        'check_back_chamber',
        'check_guillotine',
        'check_wet_ash_conveyor',
        'check_bottom_ash_conveyor',
        'check_conveyor_batu_bara',
        'check_feeder',
        'cleaning_bak_wet_ash_conveyor',
        'check_feed_tank',

        // WWTP
        'check_line_limbah',
        'check_line_chemical',
        'check_tangki_kotak',
        'check_tangki_bulat',

        // Keterangan
        'keterangan',
        'korektif',

        'created_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu'   => 'datetime:H:i:s',

        // semua checklist boolean
        'cleaning_saringan_cooling_tower' => 'boolean',
        'cleaning_unit_cooling_tower' => 'boolean',
        'cleaning_bak_cooling_tower' => 'boolean',

        'check_sensor_tank_farm_ro_produk' => 'boolean',
        'cleaning_flow_rate_mmf_1' => 'boolean',
        'cleaning_flow_rate_mmf_2' => 'boolean',
        'cleaning_flow_rate_ro_produk' => 'boolean',
        'cleaning_flow_rate_ro_reject' => 'boolean',
        'penggantian_micron_filter_cip' => 'boolean',
        'penggantian_micron_filter_makeup_water' => 'boolean',
        'cleaning_cip_tank' => 'boolean',
        'cip_membrane_reverse_osmosis' => 'boolean',
        'check_fungsi_valve' => 'boolean',
        'cleaning_unit_ro_mesin' => 'boolean',

        'sirkulasi_phe_aq55vsd' => 'boolean',
        'penggantian_air_ro_aq55vsd' => 'boolean',
        'cleaning_compressor_aq55vsd' => 'boolean',
        'cleaning_jalur_cooling_aq55vsd' => 'boolean',
        'cleaning_dryer_fd185' => 'boolean',
        'cleaning_compressor_ga37' => 'boolean',
        'cleaning_dryer_fd120' => 'boolean',
        'lubrikasi_motor_compressor_aq55vsd' => 'boolean',
        'cleaning_compressor_sm55' => 'boolean',

        'cleaning_sensor_level_tank_farm' => 'boolean',
        'cleaning_sensor_level_fresh_water_menara' => 'boolean',
        'cleaning_sensor_level_ro_reject_menara' => 'boolean',
        'cleaning_sensor_level_intermediate' => 'boolean',

        'check_safety_valve' => 'boolean',
        'cleaning_level_gauge' => 'boolean',
        'cleaning_level_transmitter' => 'boolean',
        'check_pressure_transmitter' => 'boolean',
        'check_temperature_transmitter' => 'boolean',
        'cleaning_sensor_o2_co2' => 'boolean',

        'check_chaingrate' => 'boolean',
        'check_ruang_bakar' => 'boolean',
        'check_back_chamber' => 'boolean',
        'check_guillotine' => 'boolean',
        'check_wet_ash_conveyor' => 'boolean',
        'check_bottom_ash_conveyor' => 'boolean',
        'check_conveyor_batu_bara' => 'boolean',
        'check_feeder' => 'boolean',
        'cleaning_bak_wet_ash_conveyor' => 'boolean',
        'check_feed_tank' => 'boolean',

        'check_line_limbah' => 'boolean',
        'check_line_chemical' => 'boolean',
        'check_tangki_kotak' => 'boolean',
        'check_tangki_bulat' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
