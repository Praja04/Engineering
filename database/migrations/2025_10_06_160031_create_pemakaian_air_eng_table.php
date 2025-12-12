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
        Schema::create('pemakaian_air_eng', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->float('pemakaian_awal');
            $table->float('pemakaian_akhir');
            $table->string('jenis_pemakaian', 150)->default('');
            $table->text('notes')->nullable();
            $table->string('created_by', 100);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemakaian_air_eng');
    }
};
