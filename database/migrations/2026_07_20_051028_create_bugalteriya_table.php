<?php

use App\Enums\ItemType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bugalteriya', function (Blueprint $table) {
            $table->id();

            // Qaysi ombor yozuvidan kelgani (audit uchun)
            $table->foreignId('warehouse_id')->nullable()
                ->constrained('warehouse')->nullOnDelete();

            // ===== Ombor snapshot =====
            $table->string('name');
            $table->string('document_number')->nullable();
            $table->string('supplier_name')->nullable();

            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();

            $table->unsignedInteger('quantity');
            $table->string('inventory_number')->nullable();

            // ===== Turi va buxgalter to'ldiradigan maydonlar =====
            $table->enum('item_type', ItemType::values())
                ->default(ItemType::RASXOD->value);
            $table->date('expiry_date')->nullable(); // asosiy vosita uchun
            $table->string('statya')->nullable();    // rasxod uchun

            // ===== Joylashuv =====
            $table->string('room_name')->nullable();
            $table->string('building')->nullable();
            $table->string('room_number')->nullable();

            // ===== Xodim snapshot =====
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('full_name');
            $table->string('department')->nullable();

            $table->string('condition')->default('new');
            $table->text('notes')->nullable();

            // ===== Jarayon holati =====
            $table->enum('status', ['pending', 'completed', 'cancelled'])
                ->default('pending');
            $table->foreignId('items_id')->nullable(); // yakunlangach items.id
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bugalteriya');
    }
};
