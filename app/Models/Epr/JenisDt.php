<?php

namespace App\Models\Epr;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisDt extends Model
{
    use HasFactory;

    protected $table = 'epr_jenis_dts';

    protected $fillable = [
        'name',
        'aktif',
        'created_by'
    ];

    protected $casts = [
        'aktif' => 'boolean'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
