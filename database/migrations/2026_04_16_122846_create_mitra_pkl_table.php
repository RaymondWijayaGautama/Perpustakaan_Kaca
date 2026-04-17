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
        Schema::create('mitra_pkl', function (Blueprint $table) {
            $table->integer('id_mitra_pkl')->unique('mitra_pkl_pk');
            $table->string('nama_mitra_pkl')->nullable();
            $table->string('status_mitra_pkl', 100)->nullable();
            $table->string('alamat_mitra_pkl')->nullable();
            $table->string('no_telp_mitra_pkl')->nullable();
            $table->string('jarak_tempat_pkl', 100)->nullable();
            $table->string('no_mou_pkl', 100)->nullable();

            $table->primary(['id_mitra_pkl']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra_pkl');
    }
};
