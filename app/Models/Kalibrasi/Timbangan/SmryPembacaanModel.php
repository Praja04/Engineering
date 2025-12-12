<?php

namespace App\Models\Kalibrasi\Timbangan;

use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmryPembacaanModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_tmb_pembacaan_smry';

    protected $fillable = [
        'kalibrasi_id',
        'kemampuan',
        'beban',
        'std_dev',
        'maks_perbedaan_akhir',
    ];

    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
