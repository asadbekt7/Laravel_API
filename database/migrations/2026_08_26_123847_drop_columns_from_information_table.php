<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `information` jadvalidan quyidagi ustunlarni olib tashlaydi:
     * product_name, unit_id, quantity, price, akt_number, akt_date, assignee_id
     *
     * OGOHLANTIRISH (foydalanuvchi tomonidan tasdiqlangan qaror):
     * bu 7 ta ustundagi production'dagi joriy qiymatlar hech qayerga
     * ko'chirilmaydi/zaxiralanmaydi - ular QAYTARIB BO'LMAYDIGAN tarzda yo'qoladi
     * (shu jumladan product_name/unit_id/quantity/price - yangi information_items
     * jadvali bo'sh holda ishga tushadi, eski ma'lumot bilan to'ldirilmaydi).
     * Bu migrationni ishga tushirishdan oldin to'liq DB backup (pg_dump) olish shart.
     */
    public function up(): void
    {
        // 1-qadam: ustunlarga bog'liq index va FK constraintlarni avval tushiramiz.
        // PostgreSQL'da ustun FK/index bilan bog'liq bo'lsa, to'g'ridan-to'g'ri
        // dropColumn qilib bo'lmaydi - avval constraintni yechish kerak.
        Schema::table('information', function (Blueprint $table) {
            $table->dropIndex('information_akt_number_status_index');
            $table->dropForeign('information_unit_id_foreign');
            $table->dropForeign('information_assignee_id_foreign');
        });

        // 2-qadam: endi ustunlarni xavfsiz o'chirish mumkin
        Schema::table('information', function (Blueprint $table) {
            $table->dropColumn([
                'product_name',
                'unit_id',
                'quantity',
                'price',
                'akt_number',
                'akt_date',
                'assignee_id',
            ]);
        });
    }

    /**
     * Rollback faqat SXEMANI (schema) tiklaydi, MA'LUMOTNI emas.
     * Barcha 7 ta ustunning qiymatlari down() da ham qayta tiklanmaydi,
     * chunki ular hech qayerga zaxiralanmagan edi.
     */
    public function down(): void
    {
        Schema::table('information', function (Blueprint $table) {
            $table->string('product_name')->nullable();
            $table->foreignId('unit_id')->nullable()
                ->constrained('units')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('quantity')->nullable();
            $table->decimal('price', 20, 2)->nullable();
            $table->string('akt_number')->nullable();
            $table->date('akt_date')->nullable();
            $table->foreignId('assignee_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->index(['akt_number', 'status'], 'information_akt_number_status_index');
        });
    }
};
