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
        //
        Schema::table('wwtp_influent', function (Blueprint $table) {
            $table->decimal('pit_produksi_step3', 15, 2)->nullable()->after('pit_domestik');
            $table->decimal('pit_storage', 15, 2)->nullable()->after('pit_produksi_step3');
            $table->decimal('pit_proses_wwtp2', 15, 2)->nullable()->after('pit_storage');
            $table->decimal('pit_outlet', 15, 2)->nullable()->after('pit_proses_wwtp2');
            $table->decimal('pit_boiler', 15, 2)->nullable()->after('pit_outlet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('wwtp_influent', function (Blueprint $table) {
            $table->dropColumn(['pit_produksi_step3', 'pit_storage', 'pit_proses_wwtp2', 'pit_outlet', 'pit_boiler']);
        });
    }
};
