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
        Schema::create('tr_peminjaman', function (Blueprint $table) {
            $table->integer('id_peminjaman')->primary();
            $table->integer('id_siswa_tetap')->nullable()->index('relation_1110_fk');
            $table->integer('id_cp_koleksi')->nullable()->index('relation_1111_fk');
            $table->string('nip_karyawan', 20)->nullable()->index('relation_2603_fk');
            $table->dateTime('tgl_pinjam')->nullable();
            $table->dateTime('tgl_harus_kembali')->nullable();
            $table->dateTime('tgl_kembali')->nullable();
            $table->string('status_peminjaman', 100)->nullable();
            $table->string('kondisi_buku', 25)->nullable();
            $table->string('keterangan_peminjaman')->nullable();
            $table->double('denda_peminjaman')->nullable();

            $table->unique(['id_peminjaman'], 'tr_peminjaman_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_peminjaman');
    }
};
