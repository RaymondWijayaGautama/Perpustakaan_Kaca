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
        Schema::create('ref_pelanggaran', function (Blueprint $table) {
            $table->integer('ID_REF_PELANGGARAN', true);
            $table->string('NAMA_PELANGGARAN')->nullable();
            $table->string('JENIS_PELANGGARAN')->nullable();
            $table->integer('POIN_PELANGGARAN')->nullable();

            $table->unique(['ID_REF_PELANGGARAN'], 'ref_pelanggaran_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_pelanggaran');
    }
};
