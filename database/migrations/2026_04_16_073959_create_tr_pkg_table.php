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
            $table->integer('ID_TR_PKG')->primary();
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_456_fk');
            $table->integer('KODE_TA')->nullable()->index('relation_797_fk');
            $table->dateTime('TGL_TR_PKG')->nullable();
            $table->string('NIP_EVALUATOR_PKG', 20)->nullable();
            $table->char('CATATAN_EVALUATOR', 10)->nullable();
            $table->string('NIP_VALIDATOR_PKG', 20)->nullable();
            $table->float('NILAI_AKHIR_PKG')->nullable();
            $table->string('STATUS_TR_PKG')->nullable();

            $table->unique(['ID_TR_PKG'], 'tr_pkg_pk');
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
