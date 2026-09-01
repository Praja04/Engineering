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
        // Add full EJO Engineer fields to users table
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable();
            }
            if (! Schema::hasColumn('users', 'fullname')) {
                $table->string('fullname')->nullable();
            }
            if (! Schema::hasColumn('users', 'dept')) {
                $table->string('dept')->nullable();
            }
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            if (! Schema::hasColumn('users', 'signature')) {
                $table->string('signature')->nullable();
            }
            if (! Schema::hasColumn('users', 'show_status_prop')) {
                $table->boolean('show_status_prop')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'fullname')) {
                $table->dropColumn('fullname');
            }
            if (Schema::hasColumn('users', 'dept')) {
                $table->dropColumn('dept');
            }
            if (Schema::hasColumn('users', 'avatar')) {
                $table->dropColumn('avatar');
            }
            if (Schema::hasColumn('users', 'signature')) {
                $table->dropColumn('signature');
            }
            if (Schema::hasColumn('users', 'show_status_prop')) {
                $table->dropColumn('show_status_prop');
            }
        });
    }
};
