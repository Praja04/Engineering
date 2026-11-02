<?php

namespace App\Models\Utility;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityHistoryCard extends Model
{
    use HasFactory;

    protected $table = 'history_cards';

    public const AREAS = [
        'Compressor IR, Dryer IR dan Dryer TR-15',
        'Compressor GA37, GA55, Dryer FX250',
        'RO',
        'Water Softener',
        'Power House',
        'Boiler',
        'Tank Farm',
        'Hydrant',
    ];

    protected $fillable = [
        'tanggal',
        'jam',
        'area',
        'deskripsi',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
