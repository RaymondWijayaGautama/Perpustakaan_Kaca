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
            $table->integer('id_jabatan')->primary();
            $table->string('deskripsi_jabatan', 100)->nullable();
            $table->boolean('is_valid_jabatan')->nullable();

            $table->unique(['id_jabatan'], 'ref_jabatan_str_pk');
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
