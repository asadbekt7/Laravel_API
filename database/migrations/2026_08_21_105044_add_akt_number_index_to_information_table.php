<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * /receiving endpoint'i har doim WHERE akt_number = ? AND status = ?
     * (yoki GROUP BY akt_number ... WHERE status = ?) ko'rinishida so'rov beradi.
     * Jadval kattalashgani sari bu compound indeks bo'lmasa PostgreSQL Seq Scan'ga
     * o'tib qoladi — shuning uchun oldindan qo'shib qo'yamiz.
     */
    public function up(): void
    {
        Schema::table('information', function (Blueprint $table) {
            $table->index(['akt_number', 'status'], 'information_akt_number_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('information', function (Blueprint $table) {
            $table->dropIndex('information_akt_number_status_index');
        });
    }
};

