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
        Schema::create('activity_log', function (Blueprint $table) {
            $table->integer('id_activity_log')->unique('activity_log_pk');
            $table->integer('id_access_log')->nullable()->index('relation_6979_fk');
            $table->dateTime('event_time')->nullable();
            $table->string('actor_username', 25)->nullable();
            $table->string('actor_role', 100)->nullable();
            $table->string('activity_name', 100)->nullable();
            $table->string('related_data', 100)->nullable();
            $table->string('activity_description')->nullable();

            $table->primary(['id_activity_log']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
