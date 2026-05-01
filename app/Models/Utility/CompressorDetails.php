<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompressorDetails extends Model
{
    use HasFactory;

    protected $table = 'compressor_details';

    protected $fillable = [
        'compressor_id',
        'tanggal',
        'jam',

        // pressure
        'pressure_outlet_1',
        'pressure_outlet_2',
        'pressure_outlet_3',
        'pressure_outlet_4',

        // element
        'element_outlet_1',
        'element_outlet_2',
        'element_outlet_4',

        'load_percent',

        // running hour
        'running_hour_1',
        'running_hour_2',
        'running_hour_3',
        'running_hour_4',

        // laoded hour
        'loaded_hour_1',
        'loaded_hour_2',
        'loaded_hour_3',
        'loaded_hour_4',

        // motor start
        'motor_start_1',
        'motor_start_2',
        'motor_start_3',
        'motor_start_4',

        'accumulated_volume',
        'temperature_comp_ir',
        'pressure_in',
        'pressure_out',

        'suhu_dryer_tr15',
        'suhu_dryer_fx250',
        'suhu_dryer_ir',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function compressor()
    {
        return $this->belongsTo(Compressor::class, 'compressor_id');
    }
}
