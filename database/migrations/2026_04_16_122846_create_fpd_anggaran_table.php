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
            $table->integer('id_fpd')->unique('fpd_anggaran_pk');
            $table->integer('id_program_kerja')->nullable()->index('relation_1284_fk');
            $table->dateTime('tgl_fpd')->nullable();
            $table->double('nominal_anggaran')->nullable();
            $table->double('nominal_fpd')->nullable();
            $table->double('nominal_sisa')->nullable();
            $table->string('nip_validator_fpd', 20)->nullable();

            $table->primary(['id_fpd']);
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
