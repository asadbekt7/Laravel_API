<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informations', function (Blueprint $table) {
            $table->id();
            $table->string('akt_raqam', 15)->nullable();
            $table->date('akt_sana')->nullable();
            $table->string('schet_faktura_raqam', 15)->nullable();
            $table->date('schet_faktura_sana')->nullable();
            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();  ;
            $table->string('123', 30)->nullable();
            $table->date('shartnoma_sana')->nullable();
            $table->string('shartnoma_fayl')->nullable(); // fayl yo'li saqlanadi
            $table->string('xaridor_full_name', 50)->nullable();
            $table->string('ishonchnoma_raqam', 20)->nullable();
            $table->date('ishonchnoma_sana')->nullable();
            $table->string('ishonchnoma_fayl')->nullable();
            $table->foreignId('location_id')
                ->constrained('locations')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->date('ombor_qabul_sana')->nullable();
            $table->string('ombor_qabul_xodim_full_name')->nullable();
            $table->text('jami_summa')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('informations');
    }
};
