<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcRefrigerasiModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_refrigerasi';

    protected $fillable = [
        'nama_mesin',
        'tanggal',
        'waktu',
        'paket',

        // Unit Indoor
        'check_filter_udara',
        'check_cover_filter_udara',
        'check_electrical_indoor',
        'check_suhu_evaporator',
        'check_indikator_display',
        'check_motor_blower',
        'check_fan_belt_blower',
        'check_pergerakan_motor_swing',
        'check_kontroler_indoor',
        'check_saluran_drain_kondensasi',
        'sirkulasi_evaporator',

        // Unit Outdoor
        'check_kondisi_kondensor',
        'check_electrical_outdoor',
        'check_motor_fan',
        'check_tekanan_freon',
        'pelumasan_motor_fan',
        'kebersihan_unit_body_outdoor',

        // Jalur Distribusi
        'check_jalur_freon',
        'check_jalur_distribusi_udara',
        'check_jalur_return_udara',

        // Catatan
        'keterangan',
        'korektif',

        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'waktu'   => 'datetime:H:i:s',

        // Unit Indoor
        'check_filter_udara' => 'boolean',
        'check_cover_filter_udara' => 'boolean',
        'check_electrical_indoor' => 'boolean',
        'check_suhu_evaporator' => 'boolean',
        'check_indikator_display' => 'boolean',
        'check_motor_blower' => 'boolean',
        'check_fan_belt_blower' => 'boolean',
        'check_pergerakan_motor_swing' => 'boolean',
        'check_kontroler_indoor' => 'boolean',
        'check_saluran_drain_kondensasi' => 'boolean',
        'sirkulasi_evaporator' => 'boolean',

        // Unit Outdoor
        'check_kondisi_kondensor' => 'boolean',
        'check_electrical_outdoor' => 'boolean',
        'check_motor_fan' => 'boolean',
        'check_tekanan_freon' => 'boolean',
        'pelumasan_motor_fan' => 'boolean',
        'kebersihan_unit_body_outdoor' => 'boolean',

        // Jalur Distribusi
        'check_jalur_freon' => 'boolean',
        'check_jalur_distribusi_udara' => 'boolean',
        'check_jalur_return_udara' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
