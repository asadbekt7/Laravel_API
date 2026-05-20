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
            $table->foreignId('receiving_id')->nullable()->constrained('receivings');
            $table->text('name');
            $table->string('document_number')->nullable();
            $table->text('supplier_name')->nullable();
            $table->string('batch_number')->nullable();
            $table->foreignId('type_id')->constrained('types')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('model_id')->constrained('models')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->string('inventory_number')->unique();
            $table->string('room_name')->nullable();
            $table->string('building')->nullable();
            $table->string('room_number')->nullable();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('full_name');
            $table->string('department')->nullable();
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
