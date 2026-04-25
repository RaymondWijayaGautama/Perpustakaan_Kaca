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
        Schema::create('tr_pembayaran', function (Blueprint $table) {
            $table->integer('ID_PEMBAYARAN', true);
            $table->integer('ID_SISWA_TETAP')->nullable()->index('relation_277_fk');
            $table->integer('KODE_TA')->nullable()->index('relation_278_fk');
            $table->integer('ID_JENIS_PEMBAYARAN')->nullable()->index('relation_284_fk');
            $table->integer('ID_TAGIHAN_SISWA')->nullable()->index('relation_6901_fk');
            $table->integer('REF_ID_JENIS_PEMBAYARAN')->nullable()->index('relation_6906_fk');
            $table->dateTime('TGL_BAYAR')->nullable();
            $table->double('JUMLAH_BAYAR')->nullable();
            $table->string('LINK_BUKTI_BAYAR')->nullable();
            $table->string('NIP_VALIDATOR_PEMBAYARAN', 20)->nullable();

            $table->unique(['ID_PEMBAYARAN'], 'tr_pembayaran_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_pembayaran');
    }
};
