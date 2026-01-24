<?php

namespace App\Models\Maintenance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcSipilItemModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_sipil_items';

    protected $fillable = [
        'jenis_perawatan',
        'standar_pemeliharaan',
        'aktif',
        'urutan',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'urutan' => 'integer',
    ];

    public function details()
    {
        return $this->hasMany(MtcSipilInspectionDetailModel::class, 'item_id');
    }
}
