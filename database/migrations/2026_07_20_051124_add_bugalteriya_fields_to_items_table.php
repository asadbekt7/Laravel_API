<?php

use App\Enums\ItemType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Bugalteriyadan keladigan maydonlar items da ham saqlanadi
            $table->enum('item_type', ItemType::values())
                ->default(ItemType::RASXOD->value)
                ->after('name');

            $table->date('expiry_date')->nullable()->after('item_type');
            $table->string('statya')->nullable()->after('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['item_type', 'expiry_date', 'statya']);
        });
    }
};
