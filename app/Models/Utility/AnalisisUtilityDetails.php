<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalisisUtilityDetails extends Model
{
    use HasFactory;

    protected $table = 'analisis_utility_details';

    protected $fillable = [
        'analisis_utility_id',
        'tanggal',
        'ph_fw_storage',
        'ph_ws_storage',
        'ph_ro_storage',
        'ph_in_mmf',
        'ph_buffer_tank_ws',
        'ph_outlet_ws',
        'ph_menara_ws',
        'ph_depo_lt1',
        'ph_depo_lt2',
        'ph_cooling_tower',
        'ph_boiler',
        'ph_outlet_ws_2',
        'tds_fw_storage',
        'tds_ws_storage',
        'tds_ro_storage',
        'tds_in_mmf',
        'tds_out_ro',
        'tds_menara_ws',
        'tds_daily_tank_dissolver',
        'tds_depo_lt1',
        'tds_depo_lt2',
        'tds_cooling_tower',
        'tds_boiler',
        'turbidity_in_mmf',
        'turbidity_out_mmf',
        'turbidity_cooling_tower',
        'chlorine_mmf',
        'chlorine_menara',
        'chlorine_depo_lt1',
        'chlorine_depo_lt2',
        'chlorine_daily_tank_dissolver',
        'hardness_inlet_ws',
        'hardness_outlet_ws',
        'hardness_ws_storage',
        'hardness_ct',
        'hardness_ro',
        'hardness_boiler',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function analisisUtility()
    {
        return $this->belongsTo(AnalisisUtility::class, 'analisis_utility_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
