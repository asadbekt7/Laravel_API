<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `warehouse` jadvalini "akt" (hujjat) darajasiga qisqartiramiz:
     * item darajasidagi ustunlarni (name, type_id, category_id, model_id,
     * quantity, unit_id, condition, staff_id, product_price) olib tashlaymiz
     * va akt_number, akt_date ustunlarini qo'shamiz.
     */
    public function up(): void
    {
        Schema::table('warehouse', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['model_id']);
            $table->dropForeign(['unit_id']);

            // item darajasidagi ustunlarni butunlay olib tashlaymiz.
            $table->dropColumn([
                'name',
                'type_id',
                'category_id',
                'model_id',
                'quantity',
                'unit_id',
                'condition',
                'staff_id',
                'product_price',
            ]);

            // 3) Akt (hujjat) ma'lumotlari uchun yangi ustunlar.
            $table->string('akt_number', 255)->nullable();
            $table->date('akt_date')->nullable();
            $table->string('status', 255)->nullable();
        });

        Schema::table('warehouse', function (Blueprint $table) {
            $table->index('akt_number');
            $table->index('akt_date');
        });
    }

    /**
     * Rollback: akt_number/akt_date'ni olib tashlab, eski ustunlarni
     * asl tип va constraintlari bilan tiklaymiz.
     *
     * ESLATMA: down() faqat sxemani tiklaydi — bu ustunlarga tushgan
     * MA'LUMOTLARNI tiklay olmaydi (chunki up() bosqichida DROP COLUMN
     * bilan birga ular butunlay o'chib ketgan).
     */
    public function down(): void
    {
        Schema::table('warehouse', function (Blueprint $table) {
            $table->dropIndex(['warehouse_akt_number_index']);
            $table->dropIndex(['warehouse_akt_date_index']);
            $table->dropColumn(['akt_number', 'akt_date']);
        });

        Schema::table('warehouse', function (Blueprint $table) {
            $table->string('name', 255);
            $table->foreignId('type_id')->constrained('types')->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->restrictOnDelete();
            $table->foreignId('model_id')->nullable()->constrained('models')->restrictOnDelete();
            $table->integer('quantity');
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->string('condition', 255)->default('new');
            $table->integer('staff_id')->nullable();
            $table->decimal('product_price', 12, 2);
        });
    }
};
