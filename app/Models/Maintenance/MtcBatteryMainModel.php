<?php

namespace App\Models\Maintenance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcBatteryMainModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_battery_main';

    protected $fillable = [
        'mtc_main_id',
        'battery_type',
        'no_seri',
        'no_unit',
        'kondisi_plug_battery',
        'total_voltase',
        'grounding',
        'catatan',
        'intercell',
        'kondisi_skun',
        'kondisi_unit',
    ];

    protected $casts = [
        'intercell' => 'boolean',
        'kondisi_skun' => 'boolean',
        'kondisi_unit' => 'boolean',
    ];

    public function main()
    {
        return $this->belongsTo(MtcMainModel::class, 'mtc_main_id');
    }

    public function details()
    {
        return $this->hasMany(MtcBatteryModel::class, 'mtc_battery_id');
    }
}
