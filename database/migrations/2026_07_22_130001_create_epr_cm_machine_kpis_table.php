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
        Schema::create('epr_cm_machine_kpis', function (Blueprint $table) {
            $table->id();
            $table->string('month'); // YYYY-MM
            $table->string('mesin');
            $table->decimal('availability_pct', 5, 2)->default(0);
            $table->decimal('performance_pct', 5, 2)->default(0);
            $table->decimal('quality_pct', 5, 2)->default(0);
            $table->decimal('oee_pct', 5, 2)->default(0);
            $table->decimal('pm_compliance_pct', 5, 2)->default(0);
            $table->decimal('repeat_failure_pct', 5, 2)->default(0);
            $table->decimal('minor_stop_freq', 8, 2)->default(0);
            $table->decimal('cost_per_hour', 10, 2)->default(0);
            $table->decimal('energy_per_pack', 10, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epr_cm_machine_kpis');
    }
};
