<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_batches', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_id')->nullable()->change();
        });

        if (Schema::hasColumn('warehouse_batches', 'pdf_path')
            && ! Schema::hasColumn('warehouse_batches', 'file_path')) {
            Schema::table('warehouse_batches', function (Blueprint $table) {
                $table->renameColumn('pdf_path', 'file_path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('warehouse_batches', 'file_path')
            && ! Schema::hasColumn('warehouse_batches', 'pdf_path')) {
            Schema::table('warehouse_batches', function (Blueprint $table) {
                $table->renameColumn('file_path', 'pdf_path');
            });
        }

        Schema::table('warehouse_batches', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_id')->nullable(false)->change();
        });
    }
};
