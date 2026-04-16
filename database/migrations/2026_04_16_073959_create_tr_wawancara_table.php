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
        Schema::create('tr_wawancara', function (Blueprint $table) {
            $table->integer('id_wawancara')->primary();
            $table->integer('id_pendaftaran')->nullable()->index('relation_252_fk');
            $table->string('nip_karyawan', 20)->nullable()->index('relation_265_fk');
            $table->dateTime('tgl_wawancara')->nullable();
            $table->char('waktu_wawancara', 10)->nullable();
            $table->string('tempat_wawancara', 100)->nullable();
            $table->string('hasil_wawancara', 500)->nullable();

            $table->unique(['id_wawancara'], 'tr_wawancara_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_wawancara');
    }
};
