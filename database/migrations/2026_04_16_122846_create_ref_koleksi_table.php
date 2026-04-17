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
            $table->integer('id_ref_koleksi')->primary();
            $table->string('no_kategori_buku', 10)->nullable();
            $table->string('deskripsi')->nullable();
            $table->boolean('is_delete')->nullable();

            $table->unique(['id_ref_koleksi'], 'ref_koleksi_pk');
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
