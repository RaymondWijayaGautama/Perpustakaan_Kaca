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
        Schema::create('tr_prestasi_pelatihan_karyawan', function (Blueprint $table) {
            $table->integer('id_prestasi_pelatihan')->primary();
            $table->string('nip_karyawan', 20)->nullable()->index('relation_7011_fk');
            $table->string('jenis_prestasi_pelatihan', 100)->nullable();
            $table->string('nama_pretasi_pelatihan', 100)->nullable();
            $table->string('tempat_prestasi_pelatihan', 100)->nullable();
            $table->dateTime('tgl_prestasi_pelatihan')->nullable();
            $table->string('ket_prestasi_pelatihan')->nullable();
            $table->dateTime('tgl_lapor')->nullable();
            $table->string('status_prestasi_pelatihan')->nullable();

            $table->unique(['id_prestasi_pelatihan'], 'tr_prestasi_pelatihan_karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_prestasi_pelatihan_karyawan');
    }
};
