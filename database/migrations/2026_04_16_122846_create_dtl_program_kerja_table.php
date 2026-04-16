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
            $table->integer('id_dt_progker')->unique('dtl_program_kerja_pk');
            $table->integer('id_program_kerja')->nullable()->index('relation_1275_fk');
            $table->integer('id_ref_dana')->nullable()->index('relation_1797_fk');
            $table->double('nominal')->nullable();
            $table->dateTime('tgl_awal')->nullable();
            $table->dateTime('tgl_akhir')->nullable();
            $table->integer('qty')->nullable();
            $table->double('harga_satuan')->nullable();
            $table->integer('volume')->nullable();
            $table->char('satuan', 10)->nullable();
            $table->double('total_progker')->nullable();

            $table->primary(['id_dt_progker']);
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
