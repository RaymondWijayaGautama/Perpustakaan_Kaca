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
        Schema::create('tr_evaluasi_mandiri', function (Blueprint $table) {
            $table->integer('ID_TR_EVALUASI_MANDIRI')->primary();
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_2518_fk');
            $table->dateTime('TGL_EVALUASI_MANDIRI')->nullable();
            $table->string('NIP_VALIDATOR_EVALUASI_MANDIRI', 20)->nullable();
            $table->string('STATUS_EVAL_MANDIRI', 100)->nullable();

            $table->unique(['ID_TR_EVALUASI_MANDIRI'], 'tr_evaluasi_mandiri_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_evaluasi_mandiri');
    }
};
