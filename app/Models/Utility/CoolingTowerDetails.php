<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoolingTowerDetails extends Model
{
    use HasFactory;

    protected $table = 'cooling_tower_details';

    protected $fillable = [
        'cooling_tower_id',
        'tanggal',
        'jam',
        'pressure_ct_in',
        'pressure_ct_out',
        'temp_ct_in',
        'temp_ct_out',
        'flowrate_ro_awal',
        'flowrate_ro_akhir',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'jam' => 'datetime:H:i',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function coolingTower()
    {
        return $this->belongsTo(CoolingTower::class, 'cooling_tower_id');
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
