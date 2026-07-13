<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ItemType;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->enum('item_type', ItemType::values())
                ->nullable()
                ->default(ItemType::ASOSIY_VOSITA->value)
                ->index()
                ->after('name');

            $table->date('expiry_date')->nullable()->after('item_type');

            $table->string('expense_item', 100)->nullable()->after('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['item_type', 'expiry_date', 'expense_item']);
        });
    }
};
