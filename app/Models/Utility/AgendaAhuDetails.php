<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaAhuDetails extends Model
{
    use HasFactory;

    protected $table = 'agenda_ahu_details';

    protected $fillable = [
        'agenda_ahu_id',
        'tanggal',
        'kelistrikan_ahu_1',
        'kelistrikan_ahu_2',
        'kelistrikan_ahu_3',
        'kelistrikan_ahu_4',
        'pressur_gauge_in_ahu_1',
        'pressur_gauge_in_ahu_2',
        'pressur_gauge_in_ahu_3',
        'pressur_gauge_in_ahu_4',
        'pressur_gauge_out_ahu_1',
        'pressur_gauge_out_ahu_2',
        'pressur_gauge_out_ahu_3',
        'pressur_gauge_out_ahu_4',
        'temp_gauge_in_ahu_1',
        'temp_gauge_in_ahu_2',
        'temp_gauge_in_ahu_3',
        'temp_gauge_in_ahu_4',
        'temp_gauge_out_ahu_1',
        'temp_gauge_out_ahu_2',
        'temp_gauge_out_ahu_3',
        'temp_gauge_out_ahu_4',
        'clean_filter_strainer_1',
        'clean_filter_strainer_2',
        'clean_filter_strainer_3',
        'clean_filter_strainer_4',
        'clean_filter_bebas_ahu',
        'inspeksi_h_ahu_1_4',
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

    public function agendaAhu()
    {
        return $this->belongsTo(AgendaAhu::class, 'agenda_ahu_id');
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
