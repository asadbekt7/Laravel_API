<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uzasbo_imports', function (Blueprint $table) {
            $table->id();
            $table->string('account_number', 27)->nullable()->comment('Казначейский лицевой счет организации');
            $table->string('full_name')->nullable()->comment('М.О.Л.');
            $table->string('department')->nullable()->comment('Отдел');
            $table->string('bank_schot')->nullable()->comment('Счет');
            $table->string('inventory_number', 14)->unique()->comment('Инвентарный номер');
            $table->string('old_inventory_number')->nullable()->comment('Старый инвентар рақами');
            $table->string('expense_article')->nullable()->comment('Статья расходов');
            $table->text('name')->comment('Наименование основных средств');
            $table->string('unit')->nullable()->comment('Единица измерения');
            $table->decimal('opening_quantity', 15, 4)->nullable()->comment('Кол-во на начало периода');
            $table->decimal('opening_summ', 20, 2)->nullable()->comment('Сумма на начало периода');
            $table->decimal('debit_quantity', 15, 4)->nullable()->comment('Дебет кол-во');
            $table->decimal('debit_summ', 20, 2)->nullable()->comment('Дебет сумма');
            $table->decimal('credit_quantity', 15, 4)->nullable()->comment('Кредит кол-во');
            $table->decimal('credit_summ', 20, 2)->nullable()->comment('Кредит сумма');
            $table->decimal('closing_quantity', 15, 4)->nullable()->comment('Кол-во на конец периода');
            $table->decimal('closing_summ', 20, 2)->nullable()->comment('Сумма на конец периода — основная сумма');
            $table->string('import_type')->nullable();
            $table->string('row_number')->nullable()->comment('Original row number in file');
            $table->timestamps();
            $table->index('account_number');
            $table->index('department');
            $table->index('bank_schot');
            $table->index('full_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uzasbo_imports');
    }
};
