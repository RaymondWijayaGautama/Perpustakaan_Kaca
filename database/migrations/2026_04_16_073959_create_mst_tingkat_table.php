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
            $table->integer('ID_TINGKAT')->unique('mst_tingkat_pk');
            $table->string('NAMA_TINGKATAN', 100)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_TINGKAT']);
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
