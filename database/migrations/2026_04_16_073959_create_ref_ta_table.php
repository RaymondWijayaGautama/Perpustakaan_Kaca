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
            $table->integer('KODE_TA')->primary();
            $table->string('TA', 10)->nullable();
            $table->string('SEMESTER', 10)->nullable();
            $table->boolean('IS_CURRENT')->nullable();

            $table->unique(['KODE_TA'], 'ref_ta_pk');
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
