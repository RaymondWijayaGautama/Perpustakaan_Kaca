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
        Schema::create('mst_tingkat', function (Blueprint $table) {
            $table->integer('id_tingkat')->unique('mst_tingkat_pk');
            $table->string('nama_tingkatan', 100)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_tingkat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_tingkat');
    }
};
