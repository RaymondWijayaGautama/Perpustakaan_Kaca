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
            $table->integer('ID_JURNAL_MENGAJAR', true);
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_8764_fk');
            $table->string('KETUGASAN_GURU')->nullable();
            $table->string('MINGGU_KE')->nullable();
            $table->dateTime('TGL_PENYERAHAN')->nullable();
            $table->string('NIP_VALIDATOR_PENGAJARAN', 20)->nullable();
            $table->string('STATUS_JURNAL_MENGAJAR')->nullable();

            $table->unique(['ID_JURNAL_MENGAJAR'], 'tr_jurnal_mengajar_pk');
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
