<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Qaysi bildirgi asosida yuk xati yaratilgan
            $table->foreignId('notice_id')
                ->constrained('notices')
                ->cascadeOnDelete();

            // Yuk xati raqami
            $table->string('invoice_number');

            // Yuk xati sanasi
            $table->date('invoice_date');

            // Yuk xatini yaratgan ombor mudiri
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            // Snapshot — foydalanuvchi nomi
            $table->string('created_by_name');

            // Hujjatning hozirgi holati
            $table->string('status');

            $table->timestamps();

            $table->index('notice_id');
            $table->index('status');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
