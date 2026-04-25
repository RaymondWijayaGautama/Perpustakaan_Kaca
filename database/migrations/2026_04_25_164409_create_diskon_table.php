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
            $table->integer('ID_DISKON', true)->unique('diskon_pk');
            $table->string('NAMA_DISKON', 100)->nullable();
            $table->float('PERSEN_DISKON')->nullable();
            $table->string('STATUS_DISKON', 100)->nullable();
            $table->dateTime('TGL_MULAI_DISKON')->nullable();
            $table->dateTime('TGL_SELESAI_DISKON')->nullable();

            $table->primary(['ID_DISKON']);
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
