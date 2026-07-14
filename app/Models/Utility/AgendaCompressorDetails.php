<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaCompressorDetails extends Model
{
    use HasFactory;

    protected $table = 'agenda_compressor_details';

    protected $fillable = [
        'agenda_compressor_id',
        'tanggal',
        'pressure_aq55vsd',
        'running_hour_aq55vsd',
        'element_outlet_aq55vsd',
        'kelistrikan_aq55vsd',
        'rpm_aq55vsd',
        'pressure_ga37',
        'running_hour_ga37',
        'kelistrikan_ga37',
        'element_outlet_ga37',
        'pressure_ir55',
        'running_hour_ir55',
        'kelistrikan_ir55',
        'temperature_ir55',
        'cleaning_strainer_aq55vsd',
        'cleaning_valve_ga37',
        'replace_filter_ir55',
        'inspeksi_motor_aq55vsd',
        'inspeksi_motor_ga37',
        'inspeksi_motor_ir55',
        'inspeksi_dryer_120',
        'inspeksi_dryer_tr15',
        'inspeksi_dryer_ir',
        'pressure_in_out_ct',
        'pressure_bejana_receiver',
        'pressure_in_out_dryer',
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

    public function agendaCompressor()
    {
        return $this->belongsTo(AgendaCompressor::class, 'agenda_compressor_id');
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
