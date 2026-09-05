<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_items', function (Blueprint $table) {
            $table->enum('asset_type', ['asosiy', 'tmz'])->nullable();
            $table->string('responsible_person_id')->nullable();
            $table->string('responsible_person_name')->nullable();
            $table->foreignId('tmz_id')
                ->nullable()
                ->constrained('tmz')
                ->restrictOnDelete();
            $table->index('asset_type');
            $table->index('responsible_person_id');
        });

        // type_id endi faqat asset_type = 'asosiy' bo'lganda majburiy
        // ('tmz' turida umuman kiritilmaydi), shuning uchun DB darajasidagi
        // NOT NULL cheklovini olib tashlaymiz. Doctrine/DBAL kerak
        // bo'lmasligi uchun PostgreSQL'ga xos raw SQL ishlatildi.
        DB::statement('ALTER TABLE warehouse_items ALTER COLUMN type_id DROP NOT NULL');
    }

    public function down(): void
    {
        Schema::table('warehouse_items', function (Blueprint $table) {
            $table->dropIndex(['asset_type']);
            $table->dropIndex(['responsible_person_id']);
            $table->dropConstrainedForeignId('tmz_id');
            $table->dropColumn(['responsible_person_id', 'responsible_person_name']);
            $table->dropColumn('asset_type');
        });
    }
};
