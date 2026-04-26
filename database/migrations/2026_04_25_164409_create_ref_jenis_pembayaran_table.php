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
        Schema::create('ref_jenis_pembayaran', function (Blueprint $table) {
            $table->integer('ID_JENIS_PEMBAYARAN', true);
            $table->string('DESKRIPSI_JENIS_PEMBAYARAN', 100)->nullable();

            $table->unique(['ID_JENIS_PEMBAYARAN'], 'ref_jenis_pembayaran_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_jenis_pembayaran');
    }
};
