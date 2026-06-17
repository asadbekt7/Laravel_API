<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('malumots', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number');
            $table->date('contract_date');
            $table->string('contract_file_path');
            $table->foreignId('yetkazuvchi_id')
                ->constrained('yetkazuvchi')
                ->cascadeOnUpdate()
                ->cascadeOnDelete(); ;
            $table->string('bildirishnoma_number');
            $table->date('bildirishnoma_date');
            $table->string('bildirishnoma_file_path');
            $table->string('name');
            $table->foreignId('unit_id')
                ->constrained('units')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('price' , 20, 2);
            $table->string('ishonchnoma_number');
            $table->date('ishonchnoma_date');
            $table->string('ishonchnoma_file_path');
            $table->string('hisob_faktura');
            $table->date('hisob_faktura_date');
            $table->string('hisob_faktura_file_path');
            $table->string('akt_number');
            $table->date('akt_date');
            $table->text('description');

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('malumots');
    }
};
