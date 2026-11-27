<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mtc_mesin_backup')) {
            Schema::create('mtc_mesin_backup', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mesin_id')->constrained('mtc_master_mesin')->cascadeOnDelete();
                $table->foreignId('backup_mesin_id')->constrained('mtc_master_mesin')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['mesin_id', 'backup_mesin_id']);
            });
        }

        // Migrate existing single backup_mesin_id data into mtc_mesin_backup
        if (Schema::hasColumn('mtc_master_mesin', 'backup_mesin_id')) {
            $existing = DB::table('mtc_master_mesin')
                ->whereNotNull('backup_mesin_id')
                ->select('id as mesin_id', 'backup_mesin_id')
                ->get();

            foreach ($existing as $row) {
                if ($row->mesin_id && $row->backup_mesin_id) {
                    DB::table('mtc_mesin_backup')->updateOrInsert(
                        ['mesin_id' => $row->mesin_id, 'backup_mesin_id' => $row->backup_mesin_id],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mtc_mesin_backup');
    }
};
