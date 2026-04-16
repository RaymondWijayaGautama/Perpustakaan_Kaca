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
        Schema::create('dt_evaluasi_mandiri', function (Blueprint $table) {
            $table->integer('id_dt_evaluasi_mandiri')->unique('dt_evaluasi_mandiri_pk');
            $table->integer('id_mst_evaluasi_mandiri')->nullable()->index('relation_2513_fk');
            $table->integer('id_tr_evaluasi_mandiri')->nullable()->index('relation_2517_fk');
            $table->char('nilai_evaluasi_mandiri', 2)->nullable();
            $table->string('catatan_khusus_evaluasi')->nullable();
            $table->string('rekomendasi_evaluasi')->nullable();
            $table->string('tindaklanjut_evaluasi')->nullable();
            $table->string('keterangan_evaluasi')->nullable();

            $table->primary(['id_dt_evaluasi_mandiri']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_evaluasi_mandiri');
    }
};
