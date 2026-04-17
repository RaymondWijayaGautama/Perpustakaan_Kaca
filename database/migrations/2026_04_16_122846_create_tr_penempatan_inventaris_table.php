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
        Schema::create('tr_penempatan_inventaris', function (Blueprint $table) {
            $table->integer('id_penempatan')->primary();
            $table->integer('id_inventaris')->nullable()->index('relation_1159_fk');
            $table->integer('id_ruang')->nullable()->index('relation_1166_fk');
            $table->dateTime('tgl_mulai_penempatan')->nullable();
            $table->dateTime('tgl_selesai_penempatan')->nullable();

            $table->unique(['id_penempatan'], 'tr_penempatan_inventaris_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_penempatan_inventaris');
    }
};
