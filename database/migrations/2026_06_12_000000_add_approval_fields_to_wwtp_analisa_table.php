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
        Schema::table('wwtp_analisa', function (Blueprint $table) {
            $table->foreignId('pelaksana_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('foreman_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('submitted');
            $table->timestamp('approved_foreman_at')->nullable();
            $table->timestamp('approved_supervisor_at')->nullable();
            $table->text('reject_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wwtp_analisa', function (Blueprint $table) {
            $table->dropForeign(['pelaksana_id']);
            $table->dropForeign(['foreman_id']);
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn([
                'pelaksana_id',
                'foreman_id',
                'supervisor_id',
                'status',
                'approved_foreman_at',
                'approved_supervisor_at',
                'reject_reason'
            ]);
        });
    }
};
