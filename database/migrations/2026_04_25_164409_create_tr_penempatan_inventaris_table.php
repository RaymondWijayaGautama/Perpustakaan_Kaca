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
            $table->integer('ID_PENEMPATAN', true);
            $table->integer('ID_INVENTARIS')->nullable()->index('relation_1159_fk');
            $table->integer('ID_RUANG')->nullable()->index('relation_1166_fk');
            $table->dateTime('TGL_MULAI_PENEMPATAN')->nullable();
            $table->dateTime('TGL_SELESAI_PENEMPATAN')->nullable();

            $table->unique(['ID_PENEMPATAN'], 'tr_penempatan_inventaris_pk');
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
