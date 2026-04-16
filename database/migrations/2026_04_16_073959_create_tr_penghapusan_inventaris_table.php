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
        Schema::create('tr_penghapusan_inventaris', function (Blueprint $table) {
            $table->integer('id_penghapusan_inv')->primary();
            $table->integer('id_inventaris')->nullable()->index('relation_1223_fk');
            $table->dateTime('tgl_penghapusan_inv')->nullable();
            $table->string('ket_penghapusan_inv')->nullable();
            $table->double('nominal_penjualan')->nullable();
            $table->dateTime('tgl_penjualan_inv')->nullable();
            $table->string('nip_validator_penghapusan', 20)->nullable();

            $table->unique(['id_penghapusan_inv'], 'tr_penghapusan_inventaris_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_penghapusan_inventaris');
    }
};
