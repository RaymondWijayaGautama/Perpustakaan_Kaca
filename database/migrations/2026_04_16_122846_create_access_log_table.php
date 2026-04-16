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
        Schema::create('access_log', function (Blueprint $table) {
            $table->integer('id_access_log')->unique('access_log_pk');
            $table->dateTime('start_login')->nullable();
            $table->dateTime('end_login')->nullable();
            $table->string('username', 25)->nullable();
            $table->string('role', 10)->nullable();

            $table->primary(['id_access_log']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_log');
    }
};
