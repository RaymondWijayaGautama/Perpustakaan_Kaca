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
        Schema::create('mst_capaian_pembelajaran', function (Blueprint $table) {
            $table->integer('id_cap_pemb')->unique('mst_capaian_pembelajaran_pk');
            $table->char('deskripsi_cap_pemb', 254)->nullable();
            $table->char('elemen_cap_pemb', 254)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_cap_pemb']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_capaian_pembelajaran');
    }
};
