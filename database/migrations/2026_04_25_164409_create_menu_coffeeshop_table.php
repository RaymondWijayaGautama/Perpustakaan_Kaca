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
        Schema::create('menu_coffeeshop', function (Blueprint $table) {
            $table->integer('ID_MENU_COFFEESHOP', true)->unique('menu_coffeeshop_pk');
            $table->string('NAMA_MENU_COFFEESHOP', 100)->nullable();
            $table->double('HARGA_JUAL_MENU')->nullable();
            $table->double('HARGA_POKOK_MENU')->nullable();
            $table->string('KATEGORI_MENU', 100)->nullable();
            $table->string('LINK_FOTO_MENU')->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_MENU_COFFEESHOP']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_coffeeshop');
    }
};
