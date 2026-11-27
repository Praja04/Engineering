<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mtc_master_mesin', function (Blueprint $table) {
            $table->boolean('has_backup')->default(false)->after('aktif')->comment('0=Tanpa Backup (Dihitung Downtime Lost), 1=Ada Backup (Bukan Downtime Lost)');
            $table->string('backup_notes')->nullable()->after('has_backup')->comment('Nama atau kode mesin backup di area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mtc_master_mesin', function (Blueprint $table) {
            $table->dropColumn(['has_backup', 'backup_notes']);
        });
    }
};
