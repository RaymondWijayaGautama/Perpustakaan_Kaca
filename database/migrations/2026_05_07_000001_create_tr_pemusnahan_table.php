<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tr_pemusnahan')) {
            return;
        }

        Schema::create('tr_pemusnahan', function (Blueprint $table) {
            $table->id();
            $table->string('isbn', 25);
            $table->integer('id_cp_koleksi')->nullable();
            $table->text('alasan');
            $table->string('nip_karyawan', 25);
            $table->dateTime('tanggal_pemusnahan')->nullable();
            $table->string('status', 50)->default('menunggu_konfirmasi');
            $table->timestamps();

            $table->index('isbn');
            $table->index('id_cp_koleksi');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tr_pemusnahan');
    }
};
