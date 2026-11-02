<?php

namespace App\Models\Epr;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CmLoadingTime extends Model
{
    use HasFactory;

    protected $table = 'epr_cm_loading_times';

    protected $fillable = [
        'tanggal',
        'mesin',
        'kategori',
        'loading_time_a',
        'loading_time_b',
        'loading_time_c',
        'loading_time_all',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

