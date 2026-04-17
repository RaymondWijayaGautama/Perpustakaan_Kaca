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
        Schema::create('stok_menu', function (Blueprint $table) {
            $table->integer('id_stok_menu')->primary();
            $table->integer('id_menu_coffeeshop')->nullable()->index('relation_93_fk');
            $table->dateTime('tgl_stok_menu')->nullable();
            $table->integer('jml_stok_menu')->nullable();

            $table->unique(['id_stok_menu'], 'stok_menu_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_menu');
    }
};
