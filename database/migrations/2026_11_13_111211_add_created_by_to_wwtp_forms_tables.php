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
        $tables = [
            'wwtp_records',
            'wwtp_influent_harian',
            'wwtp_performance_weeks',
            'wwtp_performance_records',
            'wwtp_performance_ph_harian',
            'wwtp_performance_sample',
            'wwtp_sludge',
            'pengangkutan_sludge'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'created_by')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'wwtp_records',
            'wwtp_influent_harian',
            'wwtp_performance_weeks',
            'wwtp_performance_records',
            'wwtp_performance_ph_harian',
            'wwtp_performance_sample',
            'wwtp_sludge',
            'pengangkutan_sludge'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'created_by')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['created_by']);
                    $table->dropColumn('created_by');
                });
            }
        }
    }
};
