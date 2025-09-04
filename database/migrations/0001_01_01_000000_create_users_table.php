<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->string('username')->unique();
            $table->string('password');
            $table->timestamps(); // created_at & updated_at

            $table->enum('jabatan', ['dept_head', 'foreman', 'supervisor', 'operator'])->nullable();
            $table->string('image')->nullable();
            $table->string('email')->nullable();
            $table->string('departemen', 100)->nullable();
            $table->string('bagian', 100)->nullable();
            $table->integer('nik')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};