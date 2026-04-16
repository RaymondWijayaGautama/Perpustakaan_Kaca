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
            $table->integer('ID_TR_PEMINJAMAN_INV')->primary();
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_5145_fk');
            $table->dateTime('TGL_MULAI_PINJAM')->nullable();
            $table->dateTime('TGL_SELESAI_PINJAM')->nullable();
            $table->string('STATUS_PEMINJAMAN_INV', 100)->nullable();
            $table->string('KETERANGAN_PEMINJAMAN_INV')->nullable();
            $table->string('NIP_VALIDATOR_PEMINJAMAN', 20)->nullable();

            $table->unique(['ID_TR_PEMINJAMAN_INV'], 'tr_peminjaman_inventaris_pk');
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
