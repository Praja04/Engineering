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
        Schema::create('wwtp_master_koloni', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sample');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('wwtp_koloni', function (Blueprint $table) {
            $table->id();
            $table->date('week_start')->unique();
            $table->date('week_end');
            $table->timestamps();
        });

        Schema::create('wwtp_koloni_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wwtp_koloni_id')->constrained('wwtp_koloni')->onDelete('cascade');
            $table->foreignId('master_koloni_id')->constrained('wwtp_master_koloni')->onDelete('cascade');
            $table->date('tanggal');
            $table->double('nilai_base');
            $table->integer('nilai_pangkat');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['wwtp_koloni_id', 'master_koloni_id'], 'wwtp_koloni_detail_sample_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwtp_koloni_details');
        Schema::dropIfExists('wwtp_koloni');
        Schema::dropIfExists('wwtp_master_koloni');
    }
};
