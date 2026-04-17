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
        Schema::create('tr_jurnal_mengajar', function (Blueprint $table) {
            $table->integer('id_jurnal_mengajar')->primary();
            $table->string('nip_karyawan', 20)->nullable()->index('relation_8764_fk');
            $table->string('ketugasan_guru')->nullable();
            $table->string('minggu_ke')->nullable();
            $table->dateTime('tgl_penyerahan')->nullable();
            $table->string('nip_validator_pengajaran', 20)->nullable();
            $table->string('status_jurnal_mengajar')->nullable();

            $table->unique(['id_jurnal_mengajar'], 'tr_jurnal_mengajar_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_jurnal_mengajar');
    }
};
