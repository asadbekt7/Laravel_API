<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bugalteriya', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn('warehouse_id');

            $table->foreignId('warehouse_item_id')
                ->nullable()
                ->after('id')
                ->constrained('warehouse_items')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bugalteriya', function (Blueprint $table) {
            $table->dropForeign(['warehouse_item_id']);
            $table->dropColumn('warehouse_item_id');

            $table->foreignId('warehouse_id')
                ->nullable()
                ->constrained('warehouse');
        });
    }
};
