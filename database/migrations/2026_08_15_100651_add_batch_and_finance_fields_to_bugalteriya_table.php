<?php
// database/migrations/2026_08_16_000002_add_batch_and_finance_fields_to_bugalteriya_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bugalteriya', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->after('id')
                ->constrained('warehouse_batches')->nullOnDelete();

            $table->string('debit')->nullable()->after('status');
            $table->string('kredit')->nullable()->after('debit');
            $table->unsignedInteger('talab_qilingan')->nullable()->after('kredit');

            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::table('bugalteriya', function (Blueprint $table) {
            $table->dropConstrainedForeignId('batch_id');
            $table->dropColumn(['debit', 'kredit', 'talab_qilingan']);
        });
    }
};
