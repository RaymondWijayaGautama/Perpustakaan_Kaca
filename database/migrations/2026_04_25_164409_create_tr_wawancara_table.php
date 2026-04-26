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
            $table->integer('ID_WAWANCARA', true);
            $table->integer('ID_PENDAFTARAN')->nullable()->index('relation_252_fk');
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_265_fk');
            $table->dateTime('TGL_WAWANCARA')->nullable();
            $table->char('WAKTU_WAWANCARA', 10)->nullable();
            $table->string('TEMPAT_WAWANCARA', 100)->nullable();
            $table->string('HASIL_WAWANCARA', 500)->nullable();

            $table->unique(['ID_WAWANCARA'], 'tr_wawancara_pk');
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
