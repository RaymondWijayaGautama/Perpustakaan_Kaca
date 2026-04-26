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
        Schema::create('tr_rapor', function (Blueprint $table) {
            $table->integer('ID_TR_RAPOR', true);
            $table->integer('ID_SISWA_KELAS')->nullable()->index('relation_5967_fk');
            $table->char('KODE_MAPEL', 10)->nullable()->index('relation_5971_fk');
            $table->float('NILAI_AKHIR_MAPEL')->nullable();

            $table->unique(['ID_TR_RAPOR'], 'tr_rapor_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_rapor');
    }
};
