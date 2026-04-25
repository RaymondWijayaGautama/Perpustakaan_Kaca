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
        Schema::create('mst_tujuan_pembelajaran', function (Blueprint $table) {
            $table->integer('ID_TUJUAN_PEMB', true)->unique('mst_tujuan_pembelajaran_pk');
            $table->integer('ID_CAP_PEMB')->nullable()->index('relation_4314_fk');
            $table->char('DESKRIPSI_TUJUAN_PEMB', 254)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_TUJUAN_PEMB']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_tujuan_pembelajaran');
    }
};
