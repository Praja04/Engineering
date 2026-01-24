<?php

namespace App\Models\Maintenance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MtcElectricalModel extends Model
{
    use HasFactory;

    protected $table = 'mtc_electrical';

    protected $fillable = [
        'nama_mesin',
        'tanggal',
        'waktu',
        'paket',

        // Panel
        'check_kunci',
        'check_koneksi_kabel',
        'check_wiring_panel',
        'check_lampu_indikator',
        'check_name_plate',
        'check_unit_electrical',
        'check_grounding',
        'check_kebersihan',
        'check_bus_bar',
        'check_nilai_grounding',

        // Penerangan
        'check_kondisi_lampu',
        'check_cover_lampu',
        'check_wiring_penerangan',
        'check_saklar',
        'check_penyangga_penerangan',

        // Sistem Distribusi
        'check_stecker',
        'check_stop_kontak',
        'check_terminal_listrik',
        'check_pengabelan_distribusi',
        'check_support_pelindung_distribusi',

        // Capacitor Bank
        'check_kondisi_fisik_capacitor',
        'check_nilai_farad',
        'check_nilai_ampere',
        'check_kebersihan_capacitor',

        // Trafo
        'check_kebocoran_oli_sisi_bawah',
        'check_kebocoran_oli_sisi_atas',
        'check_level_oli',

        // Catatan
        'keterangan',
        'korektif',

        'created_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu'   => 'datetime:H:i:s',

        // Boolean checklist
        'check_kunci' => 'boolean',
        'check_koneksi_kabel' => 'boolean',
        'check_wiring_panel' => 'boolean',
        'check_lampu_indikator' => 'boolean',
        'check_name_plate' => 'boolean',
        'check_unit_electrical' => 'boolean',
        'check_grounding' => 'boolean',
        'check_kebersihan' => 'boolean',
        'check_bus_bar' => 'boolean',
        'check_nilai_grounding' => 'boolean',

        'check_kondisi_lampu' => 'boolean',
        'check_cover_lampu' => 'boolean',
        'check_wiring_penerangan' => 'boolean',
        'check_saklar' => 'boolean',
        'check_penyangga_penerangan' => 'boolean',

        'check_stecker' => 'boolean',
        'check_stop_kontak' => 'boolean',
        'check_terminal_listrik' => 'boolean',
        'check_pengabelan_distribusi' => 'boolean',
        'check_support_pelindung_distribusi' => 'boolean',

        'check_kondisi_fisik_capacitor' => 'boolean',
        'check_nilai_farad' => 'boolean',
        'check_nilai_ampere' => 'boolean',
        'check_kebersihan_capacitor' => 'boolean',

        'check_kebocoran_oli_sisi_bawah' => 'boolean',
        'check_kebocoran_oli_sisi_atas' => 'boolean',
        'check_level_oli' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
