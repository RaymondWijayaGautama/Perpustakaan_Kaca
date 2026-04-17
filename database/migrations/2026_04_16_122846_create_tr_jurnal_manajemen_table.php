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
        Schema::create('tr_jurnal_manajemen', function (Blueprint $table) {
            $table->integer('id_jurnal_manajemen')->primary();
            $table->string('nip_karyawan', 20)->nullable()->index('relation_409_fk');
            $table->string('ketugasan_manajemen')->nullable();
            $table->string('minggu_manajemen')->nullable();
            $table->string('tgl_penyerahan_manajemen')->nullable();
            $table->string('nip_validator_manajemen', 20)->nullable();
            $table->string('status_jurnal_manajemen')->nullable();

            $table->unique(['id_jurnal_manajemen'], 'tr_jurnal_manajemen_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_jurnal_manajemen');
    }
};
