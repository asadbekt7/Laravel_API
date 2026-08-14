<?php

// database/migrations/2026_08_14_100001_create_warehouse_batches_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\BatchStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique()->nullable(); // create() dan keyin ID asosida to'ldiriladi
            $table->foreignId('staff_id')->constrained('users')->restrictOnDelete();   // mahsulot biriktirilgan xodim
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete(); // batchni yaratgan (auth user)
            $table->string('status')->default(BatchStatus::InProgress->value);
            $table->string('pdf_path')->nullable(); // PDF generatsiya qilingach shu yerga yoziladi
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['staff_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_batches');
    }
};
