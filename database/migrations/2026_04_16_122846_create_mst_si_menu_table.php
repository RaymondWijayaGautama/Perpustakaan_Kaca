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
        Schema::create('mst_si_menu', function (Blueprint $table) {
            $table->integer('id_si_role_menu')->unique('mst_si_menu_pk');
            $table->integer('id_si')->nullable()->index('relation_15_fk');
            $table->string('nama_menu', 25)->nullable();
            $table->string('deskripsi_menu', 100)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_si_role_menu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_si_menu');
    }
};
