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
        //
        Schema::table('wwtp_influent_harian', function (Blueprint $table) {
            $table->decimal('debit1', 15, 2)->nullable()->after('pit_boiler');
            $table->string('running_wwtp1')->nullable()->after('debit1');
            $table->decimal('debit2', 15, 2)->nullable()->after('running_wwtp1');
            $table->string('running_wwtp2')->nullable()->after('debit2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('wwtp_influent_harian', function (Blueprint $table) {
            $table->dropColumn(['debit1', 'running_wwtp1', 'debit2', 'running_wwtp2']);
        });
    }
};
