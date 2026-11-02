<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'section')) {
                $table->string('section')->nullable()->after('nik');
            }
            if (! Schema::hasColumn('users', 'totp_secret')) {
                $table->string('totp_secret')->nullable();
            }
            if (! Schema::hasColumn('users', 'access_permissions')) {
                $table->text('access_permissions')->nullable();
            }
            if (! Schema::hasColumn('users', 'layout_settings')) {
                $table->text('layout_settings')->nullable();
            }
            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'section')) {
                $table->dropColumn('section');
            }
            if (Schema::hasColumn('users', 'totp_secret')) {
                $table->dropColumn('totp_secret');
            }
            if (Schema::hasColumn('users', 'access_permissions')) {
                $table->dropColumn('access_permissions');
            }
            if (Schema::hasColumn('users', 'layout_settings')) {
                $table->dropColumn('layout_settings');
            }
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
