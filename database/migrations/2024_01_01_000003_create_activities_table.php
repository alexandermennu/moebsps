<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('not_started'); // not_started, in_progress, completed, overdue
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->date('start_date')->nullable();
            $table->date('due_date');
            $table->date('completed_date')->nullable();
            $table->integer('progress_percentage')->default(0);
            $table->boolean('is_overdue')->default(false);
            $table->boolean('is_escalated')->default(false);
            $table->string('escalated_to')->nullable(); // bureau_head, minister
            $table->timestamp('escalated_at')->nullable();
            $table->boolean('is_repeated')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
