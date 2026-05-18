<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receiving_id')->constrained('receivings')->restrictOnDelete();
            $table->string('name');
            $table->foreignId('type_id')->constrained('types')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('model_id')->constrained('models')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->string('condition')->default('new');
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->unsignedInteger('staff_id');
            $table->decimal('price_per_unit', 12, 2)->default(0.00);
            $table->decimal('product_price',12,2);
            $table->text('description');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('warehouse');
    }
};
