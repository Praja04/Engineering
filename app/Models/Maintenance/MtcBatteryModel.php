<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcBatteryModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_battery_inspections';

    protected $fillable = [
        'mtc_battery_id',
        'voltase',
        'level_air_aki',
        'cell',
    ];

    protected $casts = [
        'level_air_aki' => 'boolean',
    ];

    public function mainBattery()
    {
        return $this->belongsTo(MtcBatteryMainModel::class, 'mtc_battery_id');
    }

    // public function mesin()
    // {
    //     return $this->belongsTo(MtcMasterMesinModel::class, 'mesin_id');
    // }
}
