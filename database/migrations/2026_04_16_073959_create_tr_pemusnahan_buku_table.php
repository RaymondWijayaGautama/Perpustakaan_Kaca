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
        Schema::create('tr_pemusnahan_buku', function (Blueprint $table) {
            $table->integer('ID_PEMUSNAHAN_BUKU')->primary();
            $table->integer('ID_CP_KOLEKSI')->nullable()->index('relation_2598_fk');
            $table->string('KET_PEMUSNAHAN_BUKU')->nullable();
            $table->dateTime('TGL_PEMUSNAHAN_BUKU')->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->unique(['ID_PEMUSNAHAN_BUKU'], 'tr_pemusnahan_buku_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_pemusnahan_buku');
    }
};
