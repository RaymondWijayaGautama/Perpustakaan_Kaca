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
            $table->integer('id_pendaftaran')->primary();
            $table->char('kode_calon', 20)->nullable()->index('relation_55_fk');
            $table->integer('kode_ta')->nullable()->index('relation_279_fk');
            $table->string('no_pendaftaran', 20)->nullable();
            $table->dateTime('tgl_daftar')->nullable();
            $table->string('bukti_bayar')->nullable();
            $table->double('bayar_sps')->nullable();
            $table->double('nilai_bhs_indo')->nullable();
            $table->double('nilai_bhs_ing')->nullable();
            $table->double('nilai_mtk')->nullable();
            $table->double('nilai_ipa')->nullable();
            $table->char('ukuran_baju', 3)->nullable();
            $table->string('pilihan_prodi', 100)->nullable();
            $table->string('sumber_info', 100)->nullable();
            $table->string('alasan_daftar')->nullable();
            $table->string('alasan_prodi')->nullable();
            $table->string('hobi')->nullable();
            $table->string('cita_cita')->nullable();
            $table->string('orang_dihormati', 100)->nullable();
            $table->string('organisasi_diikuti')->nullable();
            $table->integer('jalur_pendaf')->nullable();
            $table->string('status_pendaf')->nullable();
            $table->string('status_verif_bayar')->nullable();

            $table->unique(['id_pendaftaran'], 'tr_pendaftaran_pk');
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
