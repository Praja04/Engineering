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
        Schema::create('utility_monthly_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('bulan'); // Format YYYY-MM
            $table->string('tipe'); // listrik, air, chemical
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('foreman_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('draft');
            $table->text('reject_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('foreman_approved_at')->nullable();
            $table->timestamp('supervisor_approved_at')->nullable();
            $table->timestamps();

            $table->unique(['bulan', 'tipe']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utility_monthly_approvals');
    }
};
