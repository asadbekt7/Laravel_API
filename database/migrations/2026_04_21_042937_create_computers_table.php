<?php
// database/migrations/xxxx_create_computers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('computers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('model_id')->constrained('models')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->text('description')->nullable();
            $table->string('room_name')->nullable();
            $table->string('building')->nullable();
            $table->string('room_number')->nullable();
            // TODO: Employee API tayyor bo'lganda quyidagilarni qo'shing:
            // $table->unsignedBigInteger('employee_id')->nullable();
            // $table->string('employee_name')->nullable();
            // $table->string('employee_position')->nullable();
            $table->foreignId('inventory_number_id')
                ->nullable()
                ->constrained('inventorynumbers')
                ->nullOnDelete();
            $table->string('inventory_number')->nullable()->comment('inventorynumbers dan denormalized');
            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('computers');
    }
};
