<?php
// database/migrations/2026_08_16_000003_create_warehouse_batch_signers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\SignerLevelStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_batch_signers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('warehouse_batches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->unsignedTinyInteger('level');
            $table->string('role_label')->nullable();
            $table->string('status')->default(SignerLevelStatus::Pending->value);
            $table->text('comment')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['batch_id', 'level']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_batch_signers');
    }
};
