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
        Schema::create('mst_arah_tujuan_pembelajaran', function (Blueprint $table) {
            $table->integer('id_atp')->unique('mst_arah_tujuan_pembelajaran_p');
            $table->integer('id_tujuan_pemb')->nullable()->index('relation_4315_fk');
            $table->integer('id_guru_mapel')->nullable()->index('relation_6000_fk');
            $table->char('deskripsi_atp', 254)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_atp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_arah_tujuan_pembelajaran');
    }
};
