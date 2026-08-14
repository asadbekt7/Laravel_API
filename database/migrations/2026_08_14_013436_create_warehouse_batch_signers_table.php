<?php
// database/migrations/2026_08_14_100003_create_warehouse_batch_signers_table.php
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
            $table->unsignedTinyInteger('level');   // 1 = buxgalter, 2,3... = tasdiqlovchilar
            $table->string('role_label')->nullable(); // UI uchun: "Buxgalter", "Bo'lim boshlig'i" va h.k.
            $table->string('status')->default(SignerLevelStatus::Pending->value);
            $table->text('comment')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['batch_id', 'level']);
            $table->index(['user_id', 'status']); // "menga tegishli active batchlar"
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_batch_signers');
    }
};
