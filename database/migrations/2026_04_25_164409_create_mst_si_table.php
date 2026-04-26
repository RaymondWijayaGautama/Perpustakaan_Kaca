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
        Schema::create('mst_si', function (Blueprint $table) {
            $table->integer('ID_SI', true)->unique('mst_si_pk');
            $table->string('NAMA_SI', 25)->nullable();
            $table->string('DESKRIPSI_SI', 100)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_SI']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_si');
    }
};
