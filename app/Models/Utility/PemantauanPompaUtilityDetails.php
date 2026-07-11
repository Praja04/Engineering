<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemantauanPompaUtilityDetails extends Model
{
    use HasFactory;

    protected $table = 'pemantauan_pompa_utility_details';

    protected $fillable = [
        'pemantauan_pompa_utility_id',
        'tanggal',
        'ampere_pompa_10p3',
        'ampere_pompa_10p3a',
        'ampere_pompa_10p4',
        'ampere_pompa_10p4a',
        'ampere_pompa_10p5b',
        'ampere_pompa_20p1',
        'ampere_pompa_20p1a',
        'ampere_pompa_20p2',
        'ampere_pompa_20p2a',
        'ampere_pompa_60p1',
        'ampere_pompa_60p2',
        'ampere_pompa_60p3',
        'ampere_pompa_hp_pump',
        'ampere_pompa_cip_pump',
        'ampere_pompa_tf_ws',
        'ampere_fan_1',
        'ampere_fan_2',
        'ampere_fan_3',
        'ampere_fan_4',
        'ampere_pompa_ct_10000p1',
        'ampere_pompa_ct_10000p2',
        'ampere_pompa_ct_10000p3',
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

    public function pemantauanPompaUtility()
    {
        return $this->belongsTo(PemantauanPompaUtility::class, 'pemantauan_pompa_utility_id');
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
