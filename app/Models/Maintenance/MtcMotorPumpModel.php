<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MtcMotorPumpModel extends Model
{
    // use SoftDeletes; // Uncomment jika pakai soft deletes

    protected $table = 'mtc_motor_pump';

    protected $fillable = [
        'nama_mesin',
        'tanggal',
        'waktu',
        'paket',

        // Motor
        'electrical_motor',
        'putaran_motor',
        'fibrasi_suara_motor',
        'bearing_motor',
        'pelumasan_motor',
        'kebersihan_unit_body_motor',

        // Pompa
        'putaran_pompa',
        'shaft_karet_coupling_pompa',
        'fan_belt_pompa',
        'pressure_pompa',
        'mechanical_seal_pompa',
        'gasket_pompa',
        'impeler',
        'kebersihan_unit_body_pompa',

        // Aksesoris
        'valve_aksesoris',
        'cek_valve_aksesoris',
        'flow_meter_aksesoris',
        'strainer_aksesoris',
        'alat_ukur_aksesoris',
        'kelengkapan_baut_mur_aksesoris',

        // Gearbox
        'tambah_ganti_oli_gearbox',
        'unit_area_gearbox',
        'oil_seal_gearbox',
        'filter_udara_gearbox',
        'bearing_gearbox',

        'keterangan',
        'korektif',
        'created_by',
    ];

    protected $casts = [
        'tanggal'                         => 'date',
        'waktu'                           => 'datetime:H:i:s',

        'electrical_motor'                => 'boolean',
        'putaran_motor'                   => 'boolean',
        'fibrasi_suara_motor'             => 'boolean',
        'bearing_motor'                   => 'boolean',
        'pelumasan_motor'                 => 'boolean',
        'kebersihan_unit_body_motor'      => 'boolean',

        'putaran_pompa'                   => 'boolean',
        'shaft_karet_coupling_pompa'      => 'boolean',
        'fan_belt_pompa'                  => 'boolean',
        'pressure_pompa'                  => 'boolean',
        'mechanical_seal_pompa'           => 'boolean',
        'gasket_pompa'                    => 'boolean',
        'impeler'                         => 'boolean',
        'kebersihan_unit_body_pompa'      => 'boolean',

        'valve_aksesoris'                 => 'boolean',
        'cek_valve_aksesoris'             => 'boolean',
        'flow_meter_aksesoris'            => 'boolean',
        'strainer_aksesoris'              => 'boolean',
        'alat_ukur_aksesoris'             => 'boolean',
        'kelengkapan_baut_mur_aksesoris'  => 'boolean',

        'tambah_ganti_oli_gearbox'        => 'boolean',
        'unit_area_gearbox'               => 'boolean',
        'oil_seal_gearbox'                => 'boolean',
        'filter_udara_gearbox'            => 'boolean',
        'bearing_gearbox'                 => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
