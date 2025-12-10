<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wwtp_records', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('kategori', ['influent', 'effluent']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wwtp_records');
    }
};
