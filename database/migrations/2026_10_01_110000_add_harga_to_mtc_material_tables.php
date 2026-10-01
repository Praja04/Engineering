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
        Schema::table('mtc_kebutuhan_material', function (Blueprint $table) {
            if (!Schema::hasColumn('mtc_kebutuhan_material', 'harga_satuan')) {
                $table->decimal('harga_satuan', 15, 2)->default(0)->nullable()->after('uom');
            }
            if (!Schema::hasColumn('mtc_kebutuhan_material', 'total_harga')) {
                $table->decimal('total_harga', 15, 2)->default(0)->nullable()->after('harga_satuan');
            }
        });

        Schema::table('mtc_penggantian_material', function (Blueprint $table) {
            if (!Schema::hasColumn('mtc_penggantian_material', 'harga_satuan')) {
                $table->decimal('harga_satuan', 15, 2)->default(0)->nullable()->after('uom');
            }
            if (!Schema::hasColumn('mtc_penggantian_material', 'total_harga')) {
                $table->decimal('total_harga', 15, 2)->default(0)->nullable()->after('harga_satuan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mtc_kebutuhan_material', function (Blueprint $table) {
            $table->dropColumn(['harga_satuan', 'total_harga']);
        });

        Schema::table('mtc_penggantian_material', function (Blueprint $table) {
            $table->dropColumn(['harga_satuan', 'total_harga']);
        });
    }
};
