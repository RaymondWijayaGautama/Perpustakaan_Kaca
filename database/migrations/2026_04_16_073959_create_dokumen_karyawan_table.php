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
        Schema::create('dokumen_karyawan', function (Blueprint $table) {
            $table->integer('id_dok_karyawan')->unique('dokumen_karyawan_pk');
            $table->string('nip_karyawan', 20)->nullable()->index('relation_2418_fk');
            $table->char('nama_dok_karyawan', 10)->nullable();
            $table->string('status_dok_karyawan', 100)->nullable();
            $table->string('link_dok_karyawan')->nullable();

            $table->primary(['id_dok_karyawan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_karyawan');
    }
};
