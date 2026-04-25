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
        Schema::create('role_coffeeshop', function (Blueprint $table) {
            $table->integer('ID_ROLE_COFFEESHOP', true);
            $table->string('NAMA_ROLE_COFFEESHOP', 100)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->unique(['ID_ROLE_COFFEESHOP'], 'role_coffeeshop_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_coffeeshop');
    }
};
