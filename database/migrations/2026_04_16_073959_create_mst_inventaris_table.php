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
        Schema::create('mst_inventaris', function (Blueprint $table) {
            $table->integer('ID_INVENTARIS')->unique('mst_inventaris_pk');
            $table->integer('ID_KAT_BARANG')->nullable()->index('relation_1156_fk');
            $table->integer('ID_PEMBELIAN')->nullable()->index('relation_1199_fk');
            $table->integer('ID_TR_LAPORAN')->nullable()->index('relation_5167_fk');
            $table->char('KODE_INVENTARIS', 15)->nullable();
            $table->string('NAMA_INVENTARIS', 25)->nullable();
            $table->double('NILAI_INVENTARIS')->nullable();
            $table->dateTime('TGL_HABIS_GARANSI')->nullable();
            $table->string('LINK_FOTO_BARANG')->nullable();
            $table->string('MEREK_INV', 100)->nullable();
            $table->string('NO_SERI_INV', 100)->nullable();
            $table->string('DIMENSI_INV', 100)->nullable();
            $table->string('KETERANGAN_INV')->nullable();
            $table->dateTime('TGL_BELI_INV')->nullable();
            $table->string('KONDISI_TERAKHIR_INV')->nullable();
            $table->string('STATUS_INV', 100)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_INVENTARIS']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_inventaris');
    }
};
