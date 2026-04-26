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
            $table->integer('ID_DT_RESEP', true)->unique('dt_resep_pk');
            $table->integer('ID_BAHAN_BAKU')->nullable()->index('relation_86_fk');
            $table->integer('ID_MENU_COFFEESHOP')->nullable()->index('relation_85_fk');
            $table->float('KUANTITI_BAHAN_RESEP')->nullable();
            $table->string('SATUAN_BAHAN_RESEP', 10)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_DT_RESEP']);
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
