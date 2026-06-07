<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_log_id')->constrained('work_logs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->timestamps();

            $table->index('work_log_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_replies');
    }
};
