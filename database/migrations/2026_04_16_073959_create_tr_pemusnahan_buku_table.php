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
            $table->integer('id_pemusnahan_buku')->primary();
            $table->integer('id_cp_koleksi')->nullable()->index('relation_2598_fk');
            $table->string('ket_pemusnahan_buku')->nullable();
            $table->dateTime('tgl_pemusnahan_buku')->nullable();
            $table->boolean('is_delete')->nullable();

            $table->unique(['id_pemusnahan_buku'], 'tr_pemusnahan_buku_pk');
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
