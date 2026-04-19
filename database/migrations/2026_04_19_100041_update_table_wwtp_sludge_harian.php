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
        Schema::table('wwtp_sludge', function (Blueprint $table) {
            $table->float('sludge_content')->nullable()->after('hasil_lumpur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('wwtp_sludge', function (Blueprint $table) {
            $table->dropColumn('sludge_content');
        });
    }
};
