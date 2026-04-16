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
        Schema::create('mst_tujuan_pembelajaran', function (Blueprint $table) {
            $table->integer('id_tujuan_pemb')->unique('mst_tujuan_pembelajaran_pk');
            $table->integer('id_cap_pemb')->nullable()->index('relation_4314_fk');
            $table->char('deskripsi_tujuan_pemb', 254)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_tujuan_pemb']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_tujuan_pembelajaran');
    }
};
