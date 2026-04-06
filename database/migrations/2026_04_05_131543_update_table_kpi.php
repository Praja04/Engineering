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
        Schema::table('kpi', function (Blueprint $table) {
            $table->renameColumn('invoice_listrik', 'listrik_prd');
            $table->float('listrik_bas')->nullable()->after('steam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('kpi', function (Blueprint $table) {
            $table->renameColumn('listrik_prd', 'invoice_listrik');
            $table->dropColumn('listrik_bas');
        });
    }
};
