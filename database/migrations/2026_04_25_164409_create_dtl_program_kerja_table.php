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
        Schema::create('dtl_program_kerja', function (Blueprint $table) {
            $table->integer('ID_DT_PROGKER', true)->unique('dtl_program_kerja_pk');
            $table->integer('ID_PROGRAM_KERJA')->nullable()->index('relation_1275_fk');
            $table->integer('ID_REF_DANA')->nullable()->index('relation_1797_fk');
            $table->double('NOMINAL')->nullable();
            $table->dateTime('TGL_AWAL')->nullable();
            $table->dateTime('TGL_AKHIR')->nullable();
            $table->integer('QTY')->nullable();
            $table->double('HARGA_SATUAN')->nullable();
            $table->integer('VOLUME')->nullable();
            $table->char('SATUAN', 10)->nullable();
            $table->double('TOTAL_PROGKER')->nullable();

            $table->primary(['ID_DT_PROGKER']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dtl_program_kerja');
    }
};
