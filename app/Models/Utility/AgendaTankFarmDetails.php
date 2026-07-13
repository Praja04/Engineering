<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaTankFarmDetails extends Model
{
    use HasFactory;

    protected $table = 'agenda_tank_farm_details';

    protected $fillable = [
        'agenda_tank_farm_id',
        'tanggal',
        'kelistrikan_pompa_sumur_1',
        'kelistrikan_pompa_sumur_2',
        'kelistrikan_pompa_sumur_4',
        'kelistrikan_pompa_sumur_5',
        'pressure_pompa_sumur_1',
        'pressure_pompa_sumur_2',
        'pressure_pompa_sumur_4',
        'pressure_pompa_sumur_5',
        'flow_meter_pompa_sumur_1',
        'flow_meter_pompa_sumur_2',
        'flow_meter_pompa_sumur_4',
        'flow_meter_pompa_sumur_5',
        'drain_lumpur_settling_tank',
        'kelistrikan_pompa_10p3',
        'kelistrikan_pompa_10p3a',
        'pressure_gauge_intermediate',
        'level_bandul_tank_farm',
        'flow_meter_fresh_water_tank',
        'flow_meter_fwt_to_ro',
        'kelistrikan_pompa_10p4',
        'kelistrikan_pompa_10p4a',
        'pressure_gauge_pompa_10p4_p4a',
        'kelistrikan_pompa_10p5',
        'kelistrikan_pompa_10p5a',
        'kelistrikan_pompa_10p5b',
        'flow_meter_ro_reject_tank',
        'pressure_gauge_pompa_10p5_10p5a',
        'drain_lumpur_tangki_intermediate',
        'inspeksi_all_pompa_tf_intermediate',
        'inspeksi_pompa_20p1',
        'inspeksi_pompa_20p1a',
        'kelistrikan_pompa_20p2',
        'kelistrikan_pompa_20p2a',
        'kelistrikan_pompa_60p1',
        'kelistrikan_pompa_60p2',
        'kelistrikan_pompa_60p3',
        'pressure_gauge_pompa_60p1',
        'pressure_gauge_pompa_60p2',
        'pressure_gauge_pompa_60p3',
        'baterai_pompa_60p3',
        'bahan_bakar_pompa_60p3',
        'pressure_gauge_water_tank_hydrant',
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

    public function agendaTankFarm()
    {
        return $this->belongsTo(AgendaTankFarm::class, 'agenda_tank_farm_id');
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
