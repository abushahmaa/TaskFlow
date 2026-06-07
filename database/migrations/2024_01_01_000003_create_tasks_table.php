<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->string('status')->default('to_do');    // to_do, in_progress, in_review, completed, blocked
            $table->dateTime('deadline')->nullable();
            $table->decimal('estimated_hours', 5, 2)->nullable();
            $table->boolean('overdue_notified')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['deadline', 'overdue_notified']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
