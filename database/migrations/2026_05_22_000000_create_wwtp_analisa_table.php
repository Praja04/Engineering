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
        Schema::create('wwtp_point', function (Blueprint $table) {
            $table->id();
            $table->string('point_name')->unique();
            $table->timestamps();
        });

        Schema::create('wwtp_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('parameter_name')->unique();
            $table->string('unit')->nullable();
            $table->timestamps();
        });

        Schema::create('wwtp_standards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('point_id')->constrained('wwtp_point')->onDelete('cascade');
            $table->foreignId('parameter_id')->constrained('wwtp_parameters')->onDelete('cascade');
            $table->decimal('standard_value', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('wwtp_analisa', function (Blueprint $table) {
            $table->id();
            $table->date('analisa_date');
            $table->integer('shift');
            $table->string('area')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('wwtp_analisa_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analisa_id')->constrained('wwtp_analisa')->onDelete('cascade');
            $table->foreignId('point_id')->constrained('wwtp_point')->onDelete('cascade');
            $table->foreignId('parameter_id')->constrained('wwtp_parameters')->onDelete('cascade');
            $table->decimal('hasil_analisa', 10, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwtp_analisa_details');
        Schema::dropIfExists('wwtp_analisa');
        Schema::dropIfExists('wwtp_standards');
        Schema::dropIfExists('wwtp_parameters');
        Schema::dropIfExists('wwtp_point');
    }
};
