<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE bugalteriya ALTER COLUMN item_type DROP DEFAULT');
        DB::statement('ALTER TABLE bugalteriya ALTER COLUMN item_type DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE bugalteriya ALTER COLUMN item_type SET DEFAULT 'rasxod'");
    }
};
