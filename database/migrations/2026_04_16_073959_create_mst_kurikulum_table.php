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
        Schema::create('mst_kurikulum', function (Blueprint $table) {
            $table->integer('id_kurikulum')->unique('mst_kurikulum_pk');
            $table->string('nama_kurikulum', 100)->nullable();
            $table->string('no_sk_penetapan', 100)->nullable();
            $table->string('status_kurikulum', 100)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_kurikulum']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_kurikulum');
    }
};
