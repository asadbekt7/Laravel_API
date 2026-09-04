<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            ALTER TABLE warehouse
            ALTER COLUMN akt_number TYPE integer
            USING akt_number::integer
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE warehouse
            ALTER COLUMN akt_number TYPE varchar(255)
            USING akt_number::varchar
        ');
    }
};
