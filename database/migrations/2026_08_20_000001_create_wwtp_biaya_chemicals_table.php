<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wwtp_chemical_standards', function (Blueprint $table) {
            $table->id();
            $table->string('chemical_name')->unique();
            $table->double('harga_standar');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Seed default values matching the screenshots
        DB::table('wwtp_chemical_standards')->insert([
            ['chemical_name' => 'PAC', 'harga_standar' => 8400, 'created_at' => now(), 'updated_at' => now()],
            ['chemical_name' => 'BE', 'harga_standar' => 7000, 'created_at' => now(), 'updated_at' => now()],
            ['chemical_name' => 'C204', 'harga_standar' => 35000, 'created_at' => now(), 'updated_at' => now()],
            ['chemical_name' => 'C9040', 'harga_standar' => 62000, 'created_at' => now(), 'updated_at' => now()],
            ['chemical_name' => 'NAOH', 'harga_standar' => 4200, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::create('wwtp_biaya_chemical_records', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique(); // Stores first day of month (e.g. 2024-01-01)
            $table->double('limbah_di_olah');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('wwtp_biaya_chemical_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wwtp_biaya_chemical_record_id')
                  ->constrained('wwtp_biaya_chemical_records')
                  ->onDelete('cascade')
                  ->name('fk_wwtp_chem_record_detail');
            $table->foreignId('chemical_standard_id')
                  ->constrained('wwtp_chemical_standards')
                  ->onDelete('cascade')
                  ->name('fk_wwtp_chem_standard_detail');
            $table->double('qty');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['wwtp_biaya_chemical_record_id', 'chemical_standard_id'], 'wwtp_biaya_chem_record_standard_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wwtp_biaya_chemical_details');
        Schema::dropIfExists('wwtp_biaya_chemical_records');
        Schema::dropIfExists('wwtp_chemical_standards');
    }
};
