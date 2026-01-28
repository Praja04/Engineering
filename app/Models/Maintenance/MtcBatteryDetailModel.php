<?php

namespace App\Models\Maintenance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcBatteryDetailModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_battery_detail';

    protected $fillable = [
        'battery_id',
        'voltase',
        'level_air_aki',
        'intercell',
        'kondisi_skun',
        'kondisi_unit',
        'grounding',
        'cell',
    ];

    protected $casts = [
        'voltase' => 'boolean',
        'level_air_aki' => 'boolean',
        'intercell' => 'boolean',
        'kondisi_skun' => 'boolean',
        'kondisi_unit' => 'boolean',
        'grounding' => 'boolean',
    ];

    public function battery()
    {
        return $this->belongsTo(MtcBatteryModel::class, 'battery_id');
    }
}
