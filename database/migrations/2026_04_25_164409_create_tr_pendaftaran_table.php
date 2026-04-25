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
        Schema::create('tr_pendaftaran', function (Blueprint $table) {
            $table->integer('ID_PENDAFTARAN', true);
            $table->char('KODE_CALON', 20)->nullable()->index('relation_55_fk');
            $table->integer('KODE_TA')->nullable()->index('relation_279_fk');
            $table->string('NO_PENDAFTARAN', 20)->nullable();
            $table->dateTime('TGL_DAFTAR')->nullable();
            $table->string('BUKTI_BAYAR')->nullable();
            $table->double('BAYAR_SPS')->nullable();
            $table->float('NILAI_BHS_INDO')->nullable();
            $table->float('NILAI_BHS_ING')->nullable();
            $table->float('NILAI_MTK')->nullable();
            $table->float('NILAI_IPA')->nullable();
            $table->char('UKURAN_BAJU', 3)->nullable();
            $table->string('PILIHAN_PRODI', 100)->nullable();
            $table->string('SUMBER_INFO', 100)->nullable();
            $table->string('ALASAN_DAFTAR')->nullable();
            $table->string('ALASAN_PRODI')->nullable();
            $table->string('HOBI')->nullable();
            $table->string('CITA_CITA')->nullable();
            $table->string('ORANG_DIHORMATI', 100)->nullable();
            $table->string('ORGANISASI_DIIKUTI')->nullable();
            $table->integer('JALUR_PENDAF')->nullable();
            $table->string('STATUS_PENDAF')->nullable();
            $table->string('STATUS_VERIF_BAYAR')->nullable();

            $table->unique(['ID_PENDAFTARAN'], 'tr_pendaftaran_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_pendaftaran');
    }
};
