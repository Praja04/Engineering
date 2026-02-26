<?php

namespace App\Models\Kalibrasi\Volumetrik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalibrasiVolumetrikDetailModel extends Model
{
    use HasFactory;

    protected $table = 'cal_volumetrik_detail';

    protected $fillable = [
        'volumetrik_id',
        'penunjuk_standar',
        'penunjuk_alat',
        'koreksi',
    ];

    /**
     * Relasi ke tabel kalibrasi
     */
    public function volumetrik()
    {
        return $this->belongsTo(KalibrasiVolumetrikModel::class, 'volumetrik_id');
    }
}
