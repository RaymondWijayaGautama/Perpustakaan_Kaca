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
            $table->integer('id_tagihan_siswa')->primary();
            $table->integer('id_siswa_tetap')->nullable()->index('relation_6902_fk');
            $table->integer('id_jenis_pembayaran')->nullable()->index('relation_6905_fk');
            $table->string('bulan_tagihan_siswa', 25)->nullable();
            $table->string('tahun_tagihan_siswa', 4)->nullable();
            $table->double('jumlah_tagihan_siswa')->nullable();
            $table->string('status_tagihan_siswa', 100)->nullable();
            $table->dateTime('duedatetime_tagihan_siswa')->nullable();

            $table->unique(['id_tagihan_siswa'], 'tagihan_siswa_pk');
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
