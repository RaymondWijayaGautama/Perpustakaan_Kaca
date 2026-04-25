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
            $table->integer('ID_MITRA_PKL', true)->unique('mitra_pkl_pk');
            $table->string('NAMA_MITRA_PKL')->nullable();
            $table->string('STATUS_MITRA_PKL', 100)->nullable();
            $table->string('ALAMAT_MITRA_PKL')->nullable();
            $table->string('NO_TELP_MITRA_PKL')->nullable();
            $table->string('JARAK_TEMPAT_PKL', 100)->nullable();
            $table->string('NO_MOU_PKL', 100)->nullable();

            $table->primary(['ID_MITRA_PKL']);
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
