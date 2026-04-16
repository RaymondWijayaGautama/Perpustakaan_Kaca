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
            $table->integer('ID_JURNAL_MANAJEMEN')->primary();
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_409_fk');
            $table->string('KETUGASAN_MANAJEMEN')->nullable();
            $table->string('MINGGU_MANAJEMEN')->nullable();
            $table->string('TGL_PENYERAHAN_MANAJEMEN')->nullable();
            $table->string('NIP_VALIDATOR_MANAJEMEN', 20)->nullable();
            $table->string('STATUS_JURNAL_MANAJEMEN')->nullable();

            $table->unique(['ID_JURNAL_MANAJEMEN'], 'tr_jurnal_manajemen_pk');
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
