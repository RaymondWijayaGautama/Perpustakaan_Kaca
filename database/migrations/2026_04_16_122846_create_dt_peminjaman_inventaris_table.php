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
            $table->integer('id_dt_pinjam_inv')->unique('dt_peminjaman_inventaris_pk');
            $table->integer('id_tr_peminjaman_inv')->nullable()->index('relation_5155_fk');
            $table->integer('id_inventaris')->nullable()->index('relation_5156_fk');
            $table->integer('id_ruang')->nullable()->index('relation_6920_fk');
            $table->string('kondisi_sebelum')->nullable();
            $table->string('kondisi_sesudah')->nullable();

            $table->primary(['id_dt_pinjam_inv']);
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
