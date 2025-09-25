<?php

namespace App\Models\Kalibrasi;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatKalibrasiModel extends Model
{
    use HasFactory;

    protected $table = 'alat_kalibrasi';

    protected $fillable = [
        'user_id',
        'kode_alat',
        'nama_alat',
        'jenis_kalibrasi',
        'jumlah',
        'departemen_pemilik',
        'lokasi_alat',
        'no_kalibrasi',
        'merk',
        'tipe',
        'kapasitas',
        'resolusi',
        'range_penggunaan',
        'limits_permissible_error'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kalibrasi()
    {
        return $this->hasMany(KalibrasiModel::class, 'alat_id');
    }
}
