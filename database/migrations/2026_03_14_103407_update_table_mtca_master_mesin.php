<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mtc_master_mesin', function (Blueprint $table) {
            $table->dropColumn('frekuensi');
            $table->string('dept')->nullable()->after('lokasi');
            $table->string('kode_mesin')->nullable()->unique()->after('dept');
        });

        Schema::create('mtc_mesin_frekuensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id')->constrained('mtc_master_mesin')->cascadeOnDelete();
            $table->unsignedSmallInteger('interval')->default(1); // contoh: 1, 2, 6
            $table->enum('satuan', ['hari', 'minggu', 'bulan', 'tahun']);
            $table->timestamps();

            $table->unique(['mesin_id', 'interval', 'satuan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mtc_mesin_frekuensi');

        Schema::table('mtc_master_mesin', function (Blueprint $table) {
            $table->dropUnique(['kode_mesin']);
            $table->dropColumn(['dept', 'kode_mesin']);
            $table->string('frekuensi')->nullable()->after('lokasi');
        });
    }
};
