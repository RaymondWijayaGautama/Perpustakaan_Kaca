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
        Schema::create('dt_resep', function (Blueprint $table) {
            $table->integer('id_dt_resep')->unique('dt_resep_pk');
            $table->integer('id_bahan_baku')->nullable()->index('relation_86_fk');
            $table->integer('id_menu_coffeeshop')->nullable()->index('relation_85_fk');
            $table->float('kuantiti_bahan_resep')->nullable();
            $table->string('satuan_bahan_resep', 10)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_dt_resep']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_resep');
    }
};
