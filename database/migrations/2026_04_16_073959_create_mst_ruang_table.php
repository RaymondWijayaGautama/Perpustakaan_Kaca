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
        Schema::create('mst_ruang', function (Blueprint $table) {
            $table->integer('id_ruang')->unique('mst_ruang_pk');
            $table->string('nama_ruang', 20)->nullable();
            $table->integer('luas')->nullable();
            $table->integer('kapasitas')->nullable();
            $table->string('kondisi_ruangan')->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_ruang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_ruang');
    }
};
