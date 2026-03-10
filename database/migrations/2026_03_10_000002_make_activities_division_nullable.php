<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Drop existing foreign key and make column nullable
            $table->dropForeign(['division_id']);
            $table->foreignId('division_id')->nullable()->change();
            $table->foreign('division_id')->references('id')->on('divisions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->foreignId('division_id')->nullable(false)->change();
            $table->foreign('division_id')->references('id')->on('divisions')->cascadeOnDelete();
        });
    }
};
