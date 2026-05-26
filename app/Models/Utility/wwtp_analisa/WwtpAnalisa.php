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
        'created_by'
    ];

    public function details()
    {
        return $this->hasMany(WwtpAnalisaDetail::class, 'analisa_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
