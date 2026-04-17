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
        Schema::create('jabatan_menu', function (Blueprint $table) {
            $table->integer('id_hak_akses')->unique('jabatan_menu_pk');
            $table->integer('id_si_role_menu')->nullable()->index('relation_8842_fk');
            $table->integer('id_jabatan')->nullable()->index('relation_8843_fk');

            $table->primary(['id_hak_akses']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan_menu');
    }
};
