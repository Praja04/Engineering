<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mtc_master_mesin', function (Blueprint $table) {
            $table->foreignId('backup_mesin_id')
                ->nullable()
                ->after('has_backup')
                ->constrained('mtc_master_mesin')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mtc_master_mesin', function (Blueprint $table) {
            $table->dropForeign(['backup_mesin_id']);
            $table->dropColumn('backup_mesin_id');
        });
    }
};
