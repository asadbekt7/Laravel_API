<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('information', function (Blueprint $table) {
            $table->foreignId('creator_id')->constrained('users');
            $table->foreignId('assignee_id')->nullable()->constrained('users');
            $table->string('status')->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('information', function (Blueprint $table) {
            //
        });
    }
};
