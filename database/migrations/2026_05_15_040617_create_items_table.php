<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uzasbo_import_id')->nullable();
            $table->text('name');
            $table->foreignId('type_id')->constrained('tapes')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('model_id')->constrained('models')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->string('inventory_number')->unique();
            $table->string('serial_number')->nullable()->unique();
            $table->string('room_name')->nullable();
            $table->string('building')->nullable();
            $table->string('room_number')->nullable();
            $table->unsignedInteger('staff_id');
            $table->enum('condition', ['new', 'good', 'fair', 'poor', 'damaged'])->default('new');
            $table->enum('status', ['active', 'inactive', 'disposed', 'lost'])->default('active');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
