<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('information', function (Blueprint $table) {
            $table->dropUnique('information_name_unique');
            $table->date('bildirishnoma_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('information', function (Blueprint $table) {
            $table->date('bildirishnoma_date')->nullable(false)->change();
            $table->unique('name');
        });
    }
};
