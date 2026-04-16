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
        Schema::create('ref_jabatan_str', function (Blueprint $table) {
            $table->integer('ID_JABATAN')->primary();
            $table->string('DESKRIPSI_JABATAN', 100)->nullable();
            $table->boolean('IS_VALID_JABATAN')->nullable();

            $table->unique(['ID_JABATAN'], 'ref_jabatan_str_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_jabatan_str');
    }
};
