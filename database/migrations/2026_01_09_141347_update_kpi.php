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
        Schema::table('kpi', function (Blueprint $table) {
            $table->decimal('invoice_listrik', 15, 2)->nullable()->after('kecap_matang');
            $table->decimal('steam', 15, 2)->nullable()->after('invoice_listrik');
            $table->decimal('batubara', 15, 2)->nullable()->after('steam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi', function (Blueprint $table) {
            $table->dropColumn(['invoice_listrik', 'steam', 'batubara']);
        });
    }
};
