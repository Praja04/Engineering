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
        Schema::table('mtc_p2h', function (Blueprint $table) {
            if (!Schema::hasColumn('mtc_p2h', 'production_id')) {
                $table->unsignedBigInteger('production_id')->nullable()->index()->after('warehouse_id');
            }
            if (!Schema::hasColumn('mtc_p2h', 'source')) {
                $table->string('source', 50)->nullable()->after('production_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mtc_p2h', function (Blueprint $table) {
            if (Schema::hasColumn('mtc_p2h', 'production_id')) {
                $table->dropColumn('production_id');
            }
            if (Schema::hasColumn('mtc_p2h', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};
