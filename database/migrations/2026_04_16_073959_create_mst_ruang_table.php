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
            $table->integer('ID_RUANG')->unique('mst_ruang_pk');
            $table->string('NAMA_RUANG', 20)->nullable();
            $table->integer('LUAS')->nullable();
            $table->integer('KAPASITAS')->nullable();
            $table->string('KONDISI_RUANGAN')->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_RUANG']);
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
