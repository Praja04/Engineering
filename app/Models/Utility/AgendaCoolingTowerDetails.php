<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaCoolingTowerDetails extends Model
{
    use HasFactory;

    protected $table = 'agenda_cooling_tower_details';

    protected $fillable = [
        'agenda_cooling_tower_id',
        'tanggal',
        'kelistrikan_pompa_10000p2',
        'kelistrikan_pompa_10000p2a',
        'kelistrikan_pompa_10000p2b',
        'kelistrikan_fan_1',
        'kelistrikan_fan_2',
        'kelistrikan_fan_3',
        'kelistrikan_fan_4',
        'suhu_out_ct',
        'suhu_in_ct',
        'pressure_out_ct',
        'pressure_in_ct',
        'ph_air_ct',
        'stok_chemical',
        'cleaning_saringan_bak',
        'cleaning_strainer_10000p2',
        'cleaning_strainer_10000p2a',
        'cleaning_strainer_10000p2b',
        'greasing_pompa_10000p2',
        'greasing_pompa_10000p2a',
        'greasing_pompa_10000p2b',
        'rubber_coupling_10000p2',
        'rubber_coupling_10000p2a',
        'rubber_coupling_10000p2b',
        'cleaning_valve_10000p2',
        'cleaning_valve_10000p2a',
        'cleaning_valve_10000p2b',
        'kalibrasi_dosis_chemical',
        'greasing_cleaning_fan_1',
        'greasing_cleaning_fan_2',
        'greasing_cleaning_fan_3',
        'greasing_cleaning_fan_4',
        'sling_fan_ct_1',
        'sling_fan_ct_2',
        'sling_fan_ct_3',
        'sling_fan_ct_4',
        'inspeksi_baut_mur',
        'created_by',
        'updated_by',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'keterangan' => 'array',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function agendaCoolingTower()
    {
        return $this->belongsTo(AgendaCoolingTower::class, 'agenda_cooling_tower_id');
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
