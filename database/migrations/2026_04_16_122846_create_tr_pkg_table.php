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
        Schema::create('tr_pkg', function (Blueprint $table) {
            $table->integer('id_tr_pkg')->primary();
            $table->string('nip_karyawan', 20)->nullable()->index('relation_456_fk');
            $table->integer('kode_ta')->nullable()->index('relation_797_fk');
            $table->dateTime('tgl_tr_pkg')->nullable();
            $table->string('nip_evaluator_pkg', 20)->nullable();
            $table->char('catatan_evaluator', 10)->nullable();
            $table->string('nip_validator_pkg', 20)->nullable();
            $table->double('nilai_akhir_pkg')->nullable();
            $table->string('status_tr_pkg')->nullable();

            $table->unique(['id_tr_pkg'], 'tr_pkg_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_pkg');
    }
};
