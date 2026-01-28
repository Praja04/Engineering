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
        Schema::create('mtc_electric_p2h_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_pengecekan');
            $table->text('kondisi_normal');
            $table->boolean('aktif')->default(true);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('mtc_electric_p2h_inspections', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('no_unit');
            $table->string('departemen')->nullable();
            $table->integer('shift')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });

        Schema::create('mtc_electric_p2h_inspection_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')
                ->constrained('mtc_electric_p2h_inspections')
                ->onDelete('cascade');

            $table->foreignId('item_id')
                ->constrained('mtc_electric_p2h_items')
                ->onDelete('restrict');

            $table->boolean('kondisi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['inspection_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_electric_p2h_inspection_details');
        Schema::dropIfExists('mtc_electric_p2h_inspections');
        Schema::dropIfExists('mtc_electric_p2h_items');
    }
};
