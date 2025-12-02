<?php

namespace App\Models\Kalibrasi\Timbangan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmryTareModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_tmb_tare_smry';

    protected $fillable = [
        'kalibrasi_id',
        'massa',
        'selisih_mz_tanpa_nol',
        'selisih_mz_dengan_nol',
        'pengaruh',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
