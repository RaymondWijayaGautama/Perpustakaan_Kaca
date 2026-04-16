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
        Schema::create('tr_ijin_cuti', function (Blueprint $table) {
            $table->integer('id_tr_ic')->primary();
            $table->string('nip_karyawan', 20)->nullable()->index('relation_470_fk');
            $table->dateTime('tgl_awal')->nullable();
            $table->dateTime('tgl_selesai')->nullable();
            $table->string('keterangan_ijin_cuti')->nullable();
            $table->string('link_bukti_ijin_cuti')->nullable();
            $table->string('status_ijin_cuti')->nullable();

            $table->unique(['id_tr_ic'], 'tr_ijin_cuti_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_ijin_cuti');
    }
};
