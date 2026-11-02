<?php

namespace App\Models\Utility;

use App\Models\User;
use App\Models\Utility\WwtpKoloni;
use App\Models\Utility\WwtpMasterKoloni;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WwtpKoloniDetail extends Model
{
    use HasFactory;

    protected $table = 'wwtp_koloni_details';

    protected $fillable = [
        'wwtp_koloni_id',
        'master_koloni_id',
        'tanggal',
        'nilai_base',
        'nilai_pangkat',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal'       => 'date',
        'nilai_base'    => 'double',
        'nilai_pangkat' => 'integer',
    ];

    public function koloni()
    {
        return $this->belongsTo(WwtpKoloni::class, 'wwtp_koloni_id');
    }

    public function masterKoloni()
    {
        return $this->belongsTo(WwtpMasterKoloni::class, 'master_koloni_id');
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
