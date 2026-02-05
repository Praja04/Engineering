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
        // Schema::create('mtc_sipil_items', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('jenis_perawatan');
        //     $table->text('standar_pemeliharaan');
        //     $table->boolean('aktif')->default(true);
        //     $table->unsignedInteger('urutan')->default(0);
        //     $table->timestamps();
        // });

        Schema::create('mtc_sipil_inspections', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('mesin_id')->constrained('mtc_master_mesin')->onDelete('restrict');
            $table->foreignId('mtc_main_id')->constrained('mtc_main')->onDelete('cascade');
            // $table->foreignId('item_id')->constrained('mtc_sipil_items')->onDelete('restrict');

            $table->boolean('plumbing')->nullable();
            $table->boolean('plafon')->nullable();
            $table->boolean('lantai')->nullable();
            $table->boolean('dinding')->nullable();
            $table->boolean('jendela')->nullable();
            $table->boolean('pintu')->nullable();
            $table->boolean('rooling_fast_door')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('mtc_sipil_items');
        Schema::dropIfExists('mtc_sipil_inspections');
    }
};
