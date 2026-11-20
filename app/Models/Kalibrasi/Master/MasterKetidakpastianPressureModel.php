<?php

namespace App\Models\Kalibrasi\Master;

use App\Models\Kalibrasi\AlatKalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKetidakpastianPressureModel extends Model
{
    use HasFactory;

    protected $table = 'cal_pressure_master_ketidakpastian';

    protected $fillable = [
        'alat_id',
        'kode_alat',
        'nama_alat',
        'titik_kalibrasi',
        'nilai_ketidakpastian',
        'u_naik',
        'u_turun',
        'keterangan',
    ];

    protected $casts = [
        'titik_kalibrasi' => 'float',
        'nilai_ketidakpastian' => 'float',
        'u_naik' => 'float',
        'u_turun' => 'float',
    ];

    public function alat()
    {
        return $this->belongsTo(AlatKalibrasiModel::class, 'alat_id');
    }
}
