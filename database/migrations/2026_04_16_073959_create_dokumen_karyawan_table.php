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
        Schema::create('dokumen_karyawan', function (Blueprint $table) {
            $table->integer('ID_DOK_KARYAWAN')->unique('dokumen_karyawan_pk');
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_2418_fk');
            $table->char('NAMA_DOK_KARYAWAN', 10)->nullable();
            $table->string('STATUS_DOK_KARYAWAN', 100)->nullable();
            $table->string('LINK_DOK_KARYAWAN')->nullable();

            $table->primary(['ID_DOK_KARYAWAN']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_karyawan');
    }
};
