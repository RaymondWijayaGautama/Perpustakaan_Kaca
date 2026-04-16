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
            $table->integer('id_menu_coffeeshop')->unique('menu_coffeeshop_pk');
            $table->string('nama_menu_coffeeshop', 100)->nullable();
            $table->double('harga_jual_menu')->nullable();
            $table->double('harga_pokok_menu')->nullable();
            $table->string('kategori_menu', 100)->nullable();
            $table->string('link_foto_menu')->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_menu_coffeeshop']);
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
