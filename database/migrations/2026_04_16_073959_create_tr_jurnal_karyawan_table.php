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
        Schema::create('tr_jurnal_karyawan', function (Blueprint $table) {
            $table->integer('id_tr_jurnal_karyawan')->primary();
            $table->string('nip_karyawan', 20)->nullable()->index('relation_8826_fk');
            $table->string('ketugasan_karyawan')->nullable();
            $table->string('minggu_karyawan')->nullable();
            $table->dateTime('tgl_penyerahan_karyawan')->nullable();
            $table->string('nip_validator_karyawan', 20)->nullable();
            $table->string('status_j_karyawan')->nullable();

            $table->unique(['id_tr_jurnal_karyawan'], 'tr_jurnal_karyawan_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_jurnal_karyawan');
    }
};
