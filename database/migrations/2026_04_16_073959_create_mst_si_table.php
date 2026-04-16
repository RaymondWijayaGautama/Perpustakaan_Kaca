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
            $table->integer('id_si')->unique('mst_si_pk');
            $table->string('nama_si', 25)->nullable();
            $table->string('deskripsi_si', 100)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_si']);
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
