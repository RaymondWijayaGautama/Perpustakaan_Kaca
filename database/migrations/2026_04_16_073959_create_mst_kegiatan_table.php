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
        Schema::create('mst_kegiatan', function (Blueprint $table) {
            $table->integer('ID_KEGIATAN')->unique('mst_kegiatan_pk');
            $table->integer('MST_ID_KEGIATAN')->nullable()->index('relation_1751_fk');
            $table->string('DESKRIPSI_KEGIATAN', 100)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_KEGIATAN']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_kegiatan');
    }
};
