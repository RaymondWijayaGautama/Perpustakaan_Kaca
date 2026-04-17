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
        Schema::create('mst_kelas', function (Blueprint $table) {
            $table->integer('id_kelas')->unique('mst_kelas_pk');
            $table->integer('id_tingkat')->nullable()->index('tingkat_kelas_fk');
            $table->char('kode_kelas', 3)->nullable();
            $table->boolean('is_delete')->nullable();
            $table->integer('kuota_kelas')->nullable();

            $table->primary(['id_kelas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_kelas');
    }
};
