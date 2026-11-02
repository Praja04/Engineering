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
        Schema::table('mtc_agenda', function (Blueprint $table) {
            // Make minggu_ke nullable
            $table->unsignedTinyInteger('minggu_ke')->nullable()->change();

            // Add tanggal column
            $table->date('tanggal')->nullable()->after('bulan');

            // Create new unique indexes first
            $table->unique(['mesin_id', 'tanggal'], 'mtc_agenda_tanggal_unique');
            $table->unique(['mesin_id', 'tahun', 'bulan', 'minggu_ke'], 'mtc_agenda_week_unique');

            // Now drop old unique constraint safely
            $table->dropUnique('mtc_agenda_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mtc_agenda', function (Blueprint $table) {
            $table->unique(['mesin_id', 'tahun', 'bulan', 'minggu_ke'], 'mtc_agenda_unique');

            $table->dropUnique('mtc_agenda_tanggal_unique');
            $table->dropUnique('mtc_agenda_week_unique');

            $table->dropColumn('tanggal');

            $table->unsignedTinyInteger('minggu_ke')->nullable(false)->change();
        });
    }
};
