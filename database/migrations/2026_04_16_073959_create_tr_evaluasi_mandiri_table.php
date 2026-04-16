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
            $table->integer('id_tr_evaluasi_mandiri')->primary();
            $table->string('nip_karyawan', 20)->nullable()->index('relation_2518_fk');
            $table->dateTime('tgl_evaluasi_mandiri')->nullable();
            $table->string('nip_validator_evaluasi_mandiri', 20)->nullable();
            $table->string('status_eval_mandiri', 100)->nullable();

            $table->unique(['id_tr_evaluasi_mandiri'], 'tr_evaluasi_mandiri_pk');
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
