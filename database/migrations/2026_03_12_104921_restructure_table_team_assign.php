<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah ejo_id ke teams
        Schema::table('teams', function (Blueprint $table) {
            $table->foreignId('ejo_id')
                ->nullable()
                ->after('department')
                ->constrained('ejo_tickets')
                ->nullOnDelete();
        });

        // Bersihkan ejo_team_assign — hanya butuh ejo_id + user_id
        // Hapus kolom team_id jika sudah ada dari migration sebelumnya
        if (Schema::hasColumn('ejo_team_assign', 'team_id')) {
            Schema::table('ejo_team_assign', function (Blueprint $table) {
                $table->dropForeign(['team_id']);
                $table->dropColumn('team_id');
            });
        }

        // Tambah user_id ke ejo_team_assign jika belum ada
        if (!Schema::hasColumn('ejo_team_assign', 'user_id')) {
            Schema::table('ejo_team_assign', function (Blueprint $table) {
                $table->foreignId('user_id')
                    ->after('ejo_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                // 1 user tidak bisa assign 2x ke EJO yang sama
                $table->unique(['ejo_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['ejo_id']);
            $table->dropColumn('ejo_id');
        });

        Schema::table('ejo_team_assign', function (Blueprint $table) {
            $table->dropUnique(['ejo_id', 'user_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            $table->foreignId('team_id')
                ->constrained('teams')
                ->cascadeOnDelete();
        });
    }
};
