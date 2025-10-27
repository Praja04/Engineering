<?php

namespace App\Models\Kalibrasi\Volumetrik;

use App\Models\Kalibrasi\KalibrasiModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiVolumetrikModel extends Model
{
    use HasFactory;

    protected $table = 'kalibrasi_volumetrik';

    protected $fillable = [
        'kalibrasi_id',
        'titik_kalibrasi',
        'penunjuk_standar',
        'penunjuk_alat',
        'koreksi',
    ];

    /**
     * Relasi ke tabel kalibrasi
     */
    public function kalibrasi()
    {
        return $this->belongsTo(KalibrasiModel::class, 'kalibrasi_id');
    }
}
