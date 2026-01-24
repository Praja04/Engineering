<?php

namespace App\Models\Maintenance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcSipilInspectionDetailModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_sipil_inspection_details';

    protected $fillable = [
        'inspection_id',
        'item_id',
        'kondisi',
        'keterangan',
    ];

    protected $casts = [
        'kondisi' => 'boolean',
    ];

    public function inspection()
    {
        return $this->belongsTo(MtcSipilInspectionModel::class, 'inspection_id');
    }

    public function item()
    {
        return $this->belongsTo(MtcSipilItemModel::class, 'item_id');
    }
}
