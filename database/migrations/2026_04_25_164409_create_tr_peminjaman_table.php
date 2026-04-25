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
            $table->integer('ID_PEMINJAMAN', true);
            $table->integer('ID_SISWA_TETAP')->nullable()->index('relation_1110_fk');
            $table->integer('ID_CP_KOLEKSI')->nullable()->index('relation_1111_fk');
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_2603_fk');
            $table->dateTime('TGL_PINJAM')->nullable();
            $table->dateTime('TGL_HARUS_KEMBALI')->nullable();
            $table->dateTime('TGL_KEMBALI')->nullable();
            $table->string('STATUS_PEMINJAMAN', 100)->nullable();
            $table->string('KONDISI_BUKU', 25)->nullable();
            $table->string('KETERANGAN_PEMINJAMAN')->nullable();
            $table->double('DENDA_PEMINJAMAN')->nullable();

            $table->unique(['ID_PEMINJAMAN'], 'tr_peminjaman_pk');
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
