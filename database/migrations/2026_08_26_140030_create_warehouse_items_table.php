<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `warehouse` endi "akt" (hujjat) darajasida bo'lgani uchun,
     * har bir akt ichidagi item qatorlarini shu jadvalda saqlaymiz.
     */
    public function up(): void
    {
        Schema::create('warehouse_items', function (Blueprint $table) {
            $table->id();

            // Qaysi aktga (warehouse yozuviga) tegishli item ekanligi.
            // Akt o'chirilsa, unga tegishli itemlar ham o'chib ketadi.
            $table->foreignId('warehouse_id')
                ->constrained('warehouse')
                ->cascadeOnDelete();

            // Ushbu item qaysi "information item"dan olinganini bildiradi.
            // Manba ma'lumot (masalan tavsif) o'chirilishi item tarixini
            // buzmasligi uchun RESTRICT qilingan — jadval nomini tekshiring.
            $table->foreignId('information_item_id')
                ->constrained('information_items')
                ->restrictOnDelete();

            $table->foreignId('type_id')
                ->constrained('types')
                ->restrictOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->restrictOnDelete();

            $table->foreignId('model_id')
                ->nullable()
                ->constrained('models')
                ->restrictOnDelete();

            $table->timestamps();

            // Bitta akt ichida turdagi itemlarni tez filtrlash uchun.
            $table->index(['warehouse_id', 'type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_items');
    }
};
