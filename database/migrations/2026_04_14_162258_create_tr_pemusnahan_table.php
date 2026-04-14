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
        Schema::create('tr_pemusnahan', function (Blueprint $table) {
            $table->id();
            $table->string('isbn', 20);
            $table->text('alasan');
            $table->string('nip_karyawan', 20);
            $table->dateTime('tanggal_pemusnahan');
            $table->string('status', 20)->default('dimusnahkan');
            $table->timestamps();

            // Opsional: Hubungkan dengan tabel buku untuk integritas data
            // $table->foreign('isbn')->references('ISBN')->on('mst_koleksi_buku')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_pemusnahan');
    }
};