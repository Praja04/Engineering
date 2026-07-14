<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReverseOsmosisDetails extends Model
{
    use HasFactory;

    protected $table = 'reverse_osmosis_details';

    protected $fillable = [
        'reverse_osmosis_id',
        'tanggal',
        'mmf_pressure_feed_1',
        'mmf_pressure_feed_2',
        'mmf_pressure_produk_1',
        'mmf_pressure_produk_2',
        'mmf_output_flow_1',
        'mmf_output_flow_2',
        'mmf_status_backwash_1',
        'mmf_status_backwash_2',
        'micron_filter_pressure_inlet',
        'micron_filter_pressure_outlet',
        'ro_permeate_flowrate',
        'ro_reject_flowrate',
        'ro_flowmeter_accumulation',
        'ro_pressure_inlet_1st_stage',
        'ro_pressure_inlet_2nd_stage',
        'ro_pressure_concentrate',
        'ro_pressure_produk',
        'cip_keterangan',
        'cip_jenis_chemical',
        'cip_qty_chemical',
        'cip_hasil',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'mmf_status_backwash_1' => 'boolean',
        'mmf_status_backwash_2' => 'boolean',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function reverseOsmosis()
    {
        return $this->belongsTo(ReverseOsmosis::class, 'reverse_osmosis_id');
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
