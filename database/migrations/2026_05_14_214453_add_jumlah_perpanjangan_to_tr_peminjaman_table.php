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
        Schema::table('tr_peminjaman', function (Blueprint $table) {
            $table->integer('JUMLAH_PERPANJANGAN')->default(0)->after('DENDA_PEMINJAMAN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_peminjaman', function (Blueprint $table) {
            $table->dropColumn('JUMLAH_PERPANJANGAN');
        });
    }
};
