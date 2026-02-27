<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_plan_id')->constrained('weekly_plans')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->text('activity');
            $table->string('responsible_persons')->nullable();
            $table->text('status_comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_activities');
    }
};
