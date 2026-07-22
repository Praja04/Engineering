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
        Schema::create('epr_jenis_dts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('aktif')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('epr_corrective_maintenances', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('shift');
            $table->string('grup');
            $table->string('mesin');
            $table->string('pouch_sachet');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('total_menit');
            $table->text('keterangan')->nullable();
            $table->text('downtime')->nullable();
            $table->foreignId('jenis_dt_id')->nullable()->constrained('epr_jenis_dts')->onDelete('set null');
            $table->string('am_pm');
            $table->string('electrical_mechanical');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epr_corrective_maintenances');
        Schema::dropIfExists('epr_jenis_dts');
    }
};
