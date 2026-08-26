<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Yangi `information_items` jadvalini yaratadi.
     *
     * Munosabat: information (1) -> information_items (N)
     */
    public function up(): void
    {
        Schema::create('information_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('information_id')
                ->constrained('information')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('product_name');
            $table->foreignId('unit_id')
                ->constrained('units')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->decimal('quantity', 20,3 );
            $table->decimal('item_price', 20, 2);
            $table->decimal('total_price', 20, 2);

            $table->timestamps();
            $table->index('information_id', 'information_items_information_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('information_items');
    }
};
