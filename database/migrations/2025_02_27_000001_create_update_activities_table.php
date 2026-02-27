<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('update_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_update_id')->constrained('weekly_updates')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->text('activity');
            $table->string('responsible_persons')->nullable();
            $table->string('status_flag')->default('not_started'); // not_started, ongoing, completed, na
            $table->text('status_comment')->nullable();
            $table->text('challenges')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('update_activities');
    }
};
