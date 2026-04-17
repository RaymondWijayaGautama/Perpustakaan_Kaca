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
        Schema::create('diskon', function (Blueprint $table) {
            $table->integer('id_diskon')->unique('diskon_pk');
            $table->string('nama_diskon', 100)->nullable();
            $table->double('persen_diskon')->nullable();
            $table->string('status_diskon', 100)->nullable();
            $table->dateTime('tgl_mulai_diskon')->nullable();
            $table->dateTime('tgl_selesai_diskon')->nullable();

            $table->primary(['id_diskon']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diskon');
    }
};
