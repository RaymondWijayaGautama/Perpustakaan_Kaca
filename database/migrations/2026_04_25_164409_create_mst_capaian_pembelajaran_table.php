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
            $table->integer('ID_CAP_PEMB', true)->unique('mst_capaian_pembelajaran_pk');
            $table->char('DESKRIPSI_CAP_PEMB', 254)->nullable();
            $table->char('ELEMEN_CAP_PEMB', 254)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_CAP_PEMB']);
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
