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
        Schema::table('compressor_details', function (Blueprint $table) {
            $table->decimal('suhu_ruang_compressor', 10, 2)->nullable()->after('suhu_dryer_ir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compressor_details', function (Blueprint $table) {
            $table->dropColumn('suhu_ruang_compressor');
        });
    }
};
