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
            $table->integer('ID_DT_PROMO')->unique('dt_promo_pk');
            $table->integer('ID_PROMO')->nullable()->index('relation_3386_fk');
            $table->integer('ID_MENU_COFFEESHOP')->nullable()->index('relation_3387_fk');
            $table->double('SUBTOTAL_PROMO')->nullable();

            $table->primary(['ID_DT_PROMO']);
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
