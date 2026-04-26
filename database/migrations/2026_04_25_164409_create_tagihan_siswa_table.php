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
        Schema::create('tagihan_siswa', function (Blueprint $table) {
            $table->integer('ID_TAGIHAN_SISWA', true);
            $table->integer('ID_SISWA_TETAP')->nullable()->index('relation_6902_fk');
            $table->integer('ID_JENIS_PEMBAYARAN')->nullable()->index('relation_6905_fk');
            $table->string('BULAN_TAGIHAN_SISWA', 25)->nullable();
            $table->string('TAHUN_TAGIHAN_SISWA', 4)->nullable();
            $table->double('JUMLAH_TAGIHAN_SISWA')->nullable();
            $table->string('STATUS_TAGIHAN_SISWA', 100)->nullable();
            $table->dateTime('DUEDATETIME_TAGIHAN_SISWA')->nullable();

            $table->unique(['ID_TAGIHAN_SISWA'], 'tagihan_siswa_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_siswa');
    }
};
