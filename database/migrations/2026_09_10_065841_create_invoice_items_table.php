<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();

            // Qaysi yuk xatiga tegishli
            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->cascadeOnDelete();

            // Qaysi ombor mahsulotidan chiqarilgan
            $table->foreignId('warehouse_item_id')
                ->constrained('warehouse_items')
                ->restrictOnDelete();

            // Chiqarilgan miqdor
            $table->decimal('quantity', 20, 3);

            // Chiqarish vaqtida berilgan responsible person
            $table->string('responsible_person_id')->nullable();
            $table->string('responsible_person_name')->nullable();

            // Mahsulot turi: asosiy / tmz
            $table->string('asset_type')->nullable();

            $table->timestamps();

            $table->index('invoice_id');
            $table->index('warehouse_item_id');
            $table->index('responsible_person_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};




