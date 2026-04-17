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
        Schema::create('ref_ta', function (Blueprint $table) {
            $table->integer('kode_ta')->primary();
            $table->string('ta', 10)->nullable();
            $table->string('semester', 10)->nullable();
            $table->boolean('is_current')->nullable();

            $table->unique(['kode_ta'], 'ref_ta_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_ta');
    }
};
