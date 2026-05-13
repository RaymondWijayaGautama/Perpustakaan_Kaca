<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE access_log MODIFY ID_ACCESS_LOG INT(11) NOT NULL AUTO_INCREMENT');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE access_log MODIFY ID_ACCESS_LOG INT(11) NOT NULL');
    }
};
