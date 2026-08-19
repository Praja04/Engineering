<?php

namespace App\Models\Utility;

use App\Models\User;
use App\Models\Utility\WwtpKoloniDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WwtpMasterKoloni extends Model
{
    use HasFactory;

    protected $table = 'wwtp_master_koloni';

    protected $fillable = [
        'nama_sample',
        'created_by',
        'updated_by',
    ];

    public function records()
    {
        return $this->hasMany(WwtpKoloniDetail::class, 'master_koloni_id');
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
