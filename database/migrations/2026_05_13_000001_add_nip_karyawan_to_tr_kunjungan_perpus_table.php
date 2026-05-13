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
        Schema::table('tr_kunjungan_perpus', function (Blueprint $table) {
            $table->string('NIP_KARYAWAN', 20)->nullable()->after('ID_SISWA_TETAP')->index('kunjungan_nip_karyawan_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_kunjungan_perpus', function (Blueprint $table) {
            $table->dropColumn('NIP_KARYAWAN');
        });
    }
};
