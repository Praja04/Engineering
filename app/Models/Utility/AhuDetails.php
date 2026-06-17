<?php

namespace App\Models\Utility;

use App\Models\User;
use App\Models\Utility\Ahu;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhuDetails extends Model
{
    use HasFactory;

    protected $table = 'ahu_details';

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected $fillable = [
        'ahu_id',
        'tanggal',
        'jam',

        // AHU 1
        'ampere_1',
        'set_temp_1',
        'pressure_in_1',
        'pressure_out_1',
        'ct_in_1',
        'ct_out_1',

        // AHU 2
        'ampere_2',
        'set_temp_2',
        'pressure_in_2',
        'pressure_out_2',
        'ct_in_2',
        'ct_out_2',

        // AHU 3
        'ampere_3',
        'set_temp_3',
        'pressure_in_3',
        'pressure_out_3',
        'ct_in_3',
        'ct_out_3',

        // AHU 4
        'ampere_4',
        'set_temp_4',
        'pressure_in_4',
        'pressure_out_4',
        'ct_in_4',
        'ct_out_4',

        // Temp Out
        'temp_out_1',
        'temp_out_2',
        'temp_out_3',
        'temp_out_4',
        'created_by',
    ];

    public function ahu()
    {
        return $this->belongsTo(Ahu::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
