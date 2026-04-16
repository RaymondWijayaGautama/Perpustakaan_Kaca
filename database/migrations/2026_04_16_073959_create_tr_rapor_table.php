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
            $table->integer('id_tr_rapor')->primary();
            $table->integer('id_siswa_kelas')->nullable()->index('relation_5967_fk');
            $table->char('kode_mapel', 10)->nullable()->index('relation_5971_fk');
            $table->float('nilai_akhir_mapel')->nullable();

            $table->unique(['id_tr_rapor'], 'tr_rapor_pk');
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
