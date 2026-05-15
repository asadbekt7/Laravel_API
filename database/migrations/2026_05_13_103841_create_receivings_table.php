<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receivings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_type_id')->constrained('document_types')->restrictOnDelete();
            $table->string('document_number')->unique();
            $table->date('document_date');
            $table->text('supplier_name');
            $table->date('delivery_date');
            $table->string('batch_number');
            $table->decimal('batch_cost', 12, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('receivings');
    }
};
