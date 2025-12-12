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
        Schema::create('wwtp_performance_records', function (Blueprint $table) {
            $table->id();

            // Relasi ke minggu
            $table->foreignId('performance_week_id')
            ->constrained('wwtp_performance_weeks')
            ->onDelete('cascade');

            // jenis wwpt
            $table->enum('jenis', [
                'equal',
                'outlet_anaerob',
                'aerob',
                'daf',
                'outlet',
            ]);

            // Parameter
            $table->float('tss')->nullable(); // mg/L
            $table->float('cod')->nullable(); // mg/L

            // Foto (path file)
            $table->string('foto')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wwtp_performance_records');
    }
};
