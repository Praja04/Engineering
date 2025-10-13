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
        Schema::create('pemakaian_chemical', function (Blueprint $table) {
            $table->id();
            $table->string('operator', 100);
            $table->date('tanggal');
            $table->string('chemical_area', 200);
            $table->string('jenis_pemakaian', 200);
            $table->string('shift', 8);
            $table->float('nilai_pemakaian')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->integer('running_hour')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemakaian_chemical');
    }
};
