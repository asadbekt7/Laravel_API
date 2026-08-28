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
            $table->decimal('quantity', 20, 3)->default(0)->after('information_item_id');
        });

        DB::statement('
            UPDATE warehouse_items
               SET quantity = information_items.quantity
              FROM information_items
             WHERE information_items.id = warehouse_items.information_item_id
        ');
    }

    public function down(): void
    {
        Schema::table('warehouse_items', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
