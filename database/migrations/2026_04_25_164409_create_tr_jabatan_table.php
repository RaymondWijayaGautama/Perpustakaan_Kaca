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
        Schema::create('tr_jabatan', function (Blueprint $table) {
            $table->integer('ID_TR_JABATAN', true);
            $table->integer('ID_JABATAN')->nullable()->index('relation_354_fk');
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_355_fk');
            $table->dateTime('TGL_MULAI_JABATAN')->nullable();
            $table->dateTime('TGL_SELESAI_JABATAN')->nullable();
            $table->string('NO_SK_JABATAN', 100)->nullable();

            $table->unique(['ID_TR_JABATAN'], 'tr_jabatan_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_jabatan');
    }
};
