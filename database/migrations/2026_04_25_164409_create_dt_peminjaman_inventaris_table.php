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
        Schema::create('dt_peminjaman_inventaris', function (Blueprint $table) {
            $table->integer('ID_DT_PINJAM_INV', true)->unique('dt_peminjaman_inventaris_pk');
            $table->integer('ID_TR_PEMINJAMAN_INV')->nullable()->index('relation_5155_fk');
            $table->integer('ID_INVENTARIS')->nullable()->index('relation_5156_fk');
            $table->integer('ID_RUANG')->nullable()->index('relation_6920_fk');
            $table->string('KONDISI_SEBELUM')->nullable();
            $table->string('KONDISI_SESUDAH')->nullable();

            $table->primary(['ID_DT_PINJAM_INV']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_peminjaman_inventaris');
    }
};
