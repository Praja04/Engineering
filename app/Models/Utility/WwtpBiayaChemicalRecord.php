<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class WwtpBiayaChemicalRecord extends Model
{
    use HasFactory;

    protected $table = 'wwtp_biaya_chemical_records';

    protected $fillable = [
        'tanggal',
        'limbah_di_olah',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'limbah_di_olah' => 'double',
    ];

    public function details()
    {
        return $this->hasMany(WwtpBiayaChemicalDetail::class, 'wwtp_biaya_chemical_record_id');
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
