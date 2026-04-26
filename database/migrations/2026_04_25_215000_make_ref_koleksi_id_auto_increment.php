<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE ref_koleksi MODIFY ID_REF_KOLEKSI INT(11) NOT NULL AUTO_INCREMENT');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE ref_koleksi MODIFY ID_REF_KOLEKSI INT(11) NOT NULL');
    }
};
