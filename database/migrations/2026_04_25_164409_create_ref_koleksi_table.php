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
        Schema::create('ref_koleksi', function (Blueprint $table) {
            $table->integer('ID_REF_KOLEKSI', true);
            $table->string('NO_KATEGORI_BUKU', 10)->nullable();
            $table->string('DESKRIPSI_KATEGORI')->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->unique(['ID_REF_KOLEKSI'], 'ref_koleksi_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_koleksi');
    }
};
