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
        Schema::create('tr_peminjaman_inventaris', function (Blueprint $table) {
            $table->integer('id_tr_peminjaman_inv')->primary();
            $table->string('nip_karyawan', 20)->nullable()->index('relation_5145_fk');
            $table->dateTime('tgl_mulai_pinjam')->nullable();
            $table->dateTime('tgl_selesai_pinjam')->nullable();
            $table->string('status_peminjaman_inv', 100)->nullable();
            $table->string('keterangan_peminjaman_inv')->nullable();
            $table->string('nip_validator_peminjaman', 20)->nullable();

            $table->unique(['id_tr_peminjaman_inv'], 'tr_peminjaman_inventaris_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_peminjaman_inventaris');
    }
};
