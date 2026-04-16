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
            $table->integer('id_tr_jabatan')->primary();
            $table->integer('id_jabatan')->nullable()->index('relation_354_fk');
            $table->string('nip_karyawan', 20)->nullable()->index('relation_355_fk');
            $table->dateTime('tgl_mulai_jabatan')->nullable();
            $table->dateTime('tgl_selesai_jabatan')->nullable();
            $table->string('no_sk_jabatan', 100)->nullable();

            $table->unique(['id_tr_jabatan'], 'tr_jabatan_pk');
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
