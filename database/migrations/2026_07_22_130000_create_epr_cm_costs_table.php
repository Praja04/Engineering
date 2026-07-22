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
        Schema::create('epr_cm_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corrective_maintenance_id')->nullable()->constrained('epr_corrective_maintenances')->onDelete('cascade');
            $table->string('mesin');
            $table->date('tanggal');
            $table->string('kategori_biaya')->default('Sparepart'); // Sparepart, Jasa, Material, Overhaul
            $table->text('deskripsi')->nullable();
            $table->decimal('jumlah_biaya', 15, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epr_cm_costs');
    }
};
