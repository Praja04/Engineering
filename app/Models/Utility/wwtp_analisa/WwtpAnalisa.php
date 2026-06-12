<?php

namespace App\Models\Utility\wwtp_analisa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\User;

class WwtpAnalisa extends Model
{
    use HasFactory;

    protected $table = 'wwtp_analisa';

    protected $fillable = [
        'analisa_date',
        'shift',
        'area',
        'created_by',
        'pelaksana_id',
        'foreman_id',
        'supervisor_id',
        'status',
        'approved_foreman_at',
        'approved_supervisor_at',
        'reject_reason'
    ];

    protected $casts = [
        'analisa_date'           => 'date',
        'approved_foreman_at'    => 'datetime',
        'approved_supervisor_at' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(WwtpAnalisaDetail::class, 'analisa_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pelaksana()
    {
        return $this->belongsTo(User::class, 'pelaksana_id');
    }

    public function foreman()
    {
        return $this->belongsTo(User::class, 'foreman_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
