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
        Schema::create('dt_promo', function (Blueprint $table) {
            $table->integer('id_dt_promo')->unique('dt_promo_pk');
            $table->integer('id_promo')->nullable()->index('relation_3386_fk');
            $table->integer('id_menu_coffeeshop')->nullable()->index('relation_3387_fk');
            $table->double('subtotal_promo')->nullable();

            $table->primary(['id_dt_promo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_promo');
    }
};
