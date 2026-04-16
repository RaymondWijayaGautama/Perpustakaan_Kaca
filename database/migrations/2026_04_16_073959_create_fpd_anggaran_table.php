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
        Schema::create('fpd_anggaran', function (Blueprint $table) {
            $table->integer('ID_FPD')->unique('fpd_anggaran_pk');
            $table->integer('ID_PROGRAM_KERJA')->nullable()->index('relation_1284_fk');
            $table->dateTime('TGL_FPD')->nullable();
            $table->double('NOMINAL_ANGGARAN')->nullable();
            $table->double('NOMINAL_FPD')->nullable();
            $table->double('NOMINAL_SISA')->nullable();
            $table->string('NIP_VALIDATOR_FPD', 20)->nullable();

            $table->primary(['ID_FPD']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fpd_anggaran');
    }
};
