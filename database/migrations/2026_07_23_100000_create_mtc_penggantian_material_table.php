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
        Schema::create('mtc_penggantian_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mtc_main_id')->constrained('mtc_main')->onDelete('cascade');
            $table->string('mid')->nullable();
            $table->string('deskripsi')->nullable();
            $table->integer('qty')->nullable();
            $table->string('uom')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_penggantian_material');
    }
};
