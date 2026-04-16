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
            $table->integer('id_inventaris')->unique('mst_inventaris_pk');
            $table->integer('id_kat_barang')->nullable()->index('relation_1156_fk');
            $table->integer('id_pembelian')->nullable()->index('relation_1199_fk');
            $table->integer('id_tr_laporan')->nullable()->index('relation_5167_fk');
            $table->char('kode_inventaris', 15)->nullable();
            $table->string('nama_inventaris', 25)->nullable();
            $table->double('nilai_inventaris')->nullable();
            $table->dateTime('tgl_habis_garansi')->nullable();
            $table->string('link_foto_barang')->nullable();
            $table->string('merek_inv', 100)->nullable();
            $table->string('no_seri_inv', 100)->nullable();
            $table->string('dimensi_inv', 100)->nullable();
            $table->string('keterangan_inv')->nullable();
            $table->dateTime('tgl_beli_inv')->nullable();
            $table->string('kondisi_terakhir_inv')->nullable();
            $table->string('status_inv', 100)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_inventaris']);
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
