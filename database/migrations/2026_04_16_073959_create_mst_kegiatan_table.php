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
            $table->integer('id_kegiatan')->unique('mst_kegiatan_pk');
            $table->integer('mst_id_kegiatan')->nullable()->index('relation_1751_fk');
            $table->string('deskripsi_kegiatan', 100)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_kegiatan']);
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
