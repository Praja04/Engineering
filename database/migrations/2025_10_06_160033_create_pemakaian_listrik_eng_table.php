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
        Schema::create('pemakaian_listrik_eng', function (Blueprint $table) {
            $table->id();
            $table->date('waktu')->nullable();
            $table->string('operator', 150)->default('0');
            $table->enum('panel_type', [
                'MDP', 'COS', 'SDP1', 'SDP2', 'SDP3', 'SDP4', 'SDP5', 'SDP6',
                'SDP7', 'SDP8', 'SDP9', 'SDP10', 'SDP11', 'SDP12', 'SDP13', 'SDP14'
            ]);
            $table->float('volt')->default(0);
            $table->float('a')->default(0);
            $table->float('kw')->default(0);
            $table->float('mwh')->default(0);
            $table->float('cos')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemakaian_listrik_eng');
    }
};
