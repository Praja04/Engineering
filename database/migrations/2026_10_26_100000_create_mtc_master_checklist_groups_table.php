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
        Schema::create('mtc_master_checklist_groups', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_mtc'); // Motor Pompa, Utility, Electrical, Refrigerasi, Electric Engine, Diesel Engine, Battery, Sipil
            $table->string('nama_group'); // e.g. Motor, Pompa, Aksesoris, Gearbox, Panel, dsb.
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['jenis_mtc', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_master_checklist_groups');
    }
};
