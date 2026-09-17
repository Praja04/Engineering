<?php

namespace App\Models\Maintenance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcP2hModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_p2h';

    protected $fillable = [
        'warehouse_id',
        'production_id',
        'source',
        'mesin_id',
        'nomor_unit',
        'dept',
        'tanggal',
        'shift',
        'jenis_p2h',
        'operator_name',
        'jam_operasional',
        'persentase',
        'status_kelayakan',
        'foto_kondisi_accu',
        'catatan',

        // Checklist Warehouse API
        'cek_baterai',
        'cek_fork',
        'kondisi_body_kebersihan',
        'lampu_kiri',
        'lampu_kanan',
        'lampu_sorot',
        'lampu_sign_depan_kanan',
        'lampu_sign_depan_kiri',
        'kipas_belakang',
        'rantai_lift',
        'sistem_hidrolik',
        'kondisi_axle',
        'sistem_kemudi',
        'panel_display',
        'air_aki',
        'klakson',
        'buzzer_mundur',
        'kaca_spion',
        'kondisi_ban',
        'fungsi_rem',
        'check_kunci_pm',
        'check_kebersihan_unit',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'persentase' => 'float',
        'jam_operasional' => 'float',

        // Booleans
        'cek_baterai' => 'boolean',
        'cek_fork' => 'boolean',
        'kondisi_body_kebersihan' => 'boolean',
        'lampu_kiri' => 'boolean',
        'lampu_kanan' => 'boolean',
        'lampu_sorot' => 'boolean',
        'lampu_sign_depan_kanan' => 'boolean',
        'lampu_sign_depan_kiri' => 'boolean',
        'kipas_belakang' => 'boolean',
        'rantai_lift' => 'boolean',
        'sistem_hidrolik' => 'boolean',
        'kondisi_axle' => 'boolean',
        'sistem_kemudi' => 'boolean',
        'panel_display' => 'boolean',
        'air_aki' => 'boolean',
        'klakson' => 'boolean',
        'buzzer_mundur' => 'boolean',
        'kaca_spion' => 'boolean',
        'kondisi_ban' => 'boolean',
        'fungsi_rem' => 'boolean',
        'check_kunci_pm' => 'boolean',
        'check_kebersihan_unit' => 'boolean',
    ];

    /**
     * Relasi ke mtc_master_mesin
     */
    public function mesin()
    {
        return $this->belongsTo(MtcMasterMesinModel::class, 'mesin_id');
    }
}
