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
            $table->integer('id_role_coffeeshop')->primary();
            $table->string('nama_role_coffeeshop', 100)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->unique(['id_role_coffeeshop'], 'role_coffeeshop_pk');
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
