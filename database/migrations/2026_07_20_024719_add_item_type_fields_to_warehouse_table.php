<?php

use App\Enums\ItemType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse', function (Blueprint $table) {
            $table->enum('item_type', ItemType::values())
                ->default(ItemType::RASXOD->value)
                ->after('name');

            // Asosiy vosita uchun — yaroqlilik muddati
            $table->date('expiry_date')->nullable()->after('item_type');

            // Rasxod uchun — statya
            $table->string('statya')->nullable()->after('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::table('warehouse', function (Blueprint $table) {
            $table->dropColumn(['item_type', 'expiry_date', 'statya']);
        });
    }
};
