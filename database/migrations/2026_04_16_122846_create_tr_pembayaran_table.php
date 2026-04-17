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
            $table->integer('id_pembayaran')->primary();
            $table->integer('id_siswa_tetap')->nullable()->index('relation_277_fk');
            $table->integer('kode_ta')->nullable()->index('relation_278_fk');
            $table->integer('id_jenis_pembayaran')->nullable()->index('relation_284_fk');
            $table->integer('id_tagihan_siswa')->nullable()->index('relation_6901_fk');
            $table->integer('ref_id_jenis_pembayaran')->nullable()->index('relation_6906_fk');
            $table->dateTime('tgl_bayar')->nullable();
            $table->double('jumlah_bayar')->nullable();
            $table->string('link_bukti_bayar')->nullable();
            $table->string('nip_validator_pembayaran', 20)->nullable();

            $table->unique(['id_pembayaran'], 'tr_pembayaran_pk');
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
