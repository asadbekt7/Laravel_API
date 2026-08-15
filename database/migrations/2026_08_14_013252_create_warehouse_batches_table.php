<?php
// database/migrations/2026_08_16_000001_create_warehouse_batches_table.php

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
            $table->string('batch_number')->unique(); // qo'lda kiritiladi
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('status')->default(BatchStatus::InProgress->value);
            $table->string('file_path')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_batches');
    }
};
