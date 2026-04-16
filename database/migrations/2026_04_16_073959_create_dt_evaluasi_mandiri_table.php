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
            $table->integer('ID_DT_EVALUASI_MANDIRI')->unique('dt_evaluasi_mandiri_pk');
            $table->integer('ID_MST_EVALUASI_MANDIRI')->nullable()->index('relation_2513_fk');
            $table->integer('ID_TR_EVALUASI_MANDIRI')->nullable()->index('relation_2517_fk');
            $table->char('NILAI_EVALUASI_MANDIRI', 2)->nullable();
            $table->string('CATATAN_KHUSUS_EVALUASI')->nullable();
            $table->string('REKOMENDASI_EVALUASI')->nullable();
            $table->string('TINDAKLANJUT_EVALUASI')->nullable();
            $table->string('KETERANGAN_EVALUASI')->nullable();

            $table->primary(['ID_DT_EVALUASI_MANDIRI']);
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
