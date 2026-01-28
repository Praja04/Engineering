<?php

namespace App\Models\Maintenance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcDieselP2hItemModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_diesel_p2h_items';

    protected $fillable = [
        'item_pengecekan',
        'kondisi_normal',
        'aktif',
        'urutan',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Relasi ke detail inspection
     */
    public function inspectionDetails()
    {
        return $this->hasMany(MtcDieselP2hInspectionDetailModel::class, 'item_id');
    }
}
