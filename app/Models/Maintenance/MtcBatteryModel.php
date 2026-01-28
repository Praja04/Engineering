<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcBatteryModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_battery';

    protected $fillable = [
        'tanggal',
        'waktu',
        'battery_type',
        'no_seri',
        'no_unit',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'waktu'   => 'datetime:H:i',
    ];

    // Detail battery (1 battery punya banyak detail)
    public function details()
    {
        return $this->hasMany(MtcBatteryDetailModel::class, 'battery_id');
    }

    // User pembuat
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // User pengupdate
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
