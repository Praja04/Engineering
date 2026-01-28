<?php

namespace App\Models\Maintenance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcElectricP2hInspectionDetailModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_electric_p2h_inspection_details';

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
        return $this->belongsTo(MtcElectricP2hInspectionModel::class, 'inspection_id');
    }

    public function item()
    {
        return $this->belongsTo(MtcElectricP2hItemModel::class, 'item_id');
    }
}
