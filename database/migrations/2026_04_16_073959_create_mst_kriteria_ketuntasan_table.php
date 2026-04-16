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
        Schema::create('mst_kriteria_ketuntasan', function (Blueprint $table) {
            $table->integer('id_kktp')->unique('mst_kriteria_ketuntasan_pk');
            $table->integer('id_atp')->nullable()->index('relation_5979_fk');
            $table->integer('id_modul_ajar')->index('relation_6028_fk2');
            $table->string('interval_nilai', 100)->nullable();
            $table->string('deskripsi_interval')->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_kktp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_kriteria_ketuntasan');
    }
};
