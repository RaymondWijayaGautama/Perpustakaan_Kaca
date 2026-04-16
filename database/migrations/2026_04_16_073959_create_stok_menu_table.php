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
            $table->integer('ID_STOK_MENU')->primary();
            $table->integer('ID_MENU_COFFEESHOP')->nullable()->index('relation_93_fk');
            $table->dateTime('TGL_STOK_MENU')->nullable();
            $table->integer('JML_STOK_MENU')->nullable();

            $table->unique(['ID_STOK_MENU'], 'stok_menu_pk');
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
