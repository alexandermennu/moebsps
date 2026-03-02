<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counselor_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('document_type');       // certificate, credential, training_cert, other
            $table->string('title');                // user-provided title/label
            $table->string('file_name');            // original file name
            $table->string('file_path');            // storage path
            $table->string('file_type')->nullable(); // mime type
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counselor_documents');
    }
};
