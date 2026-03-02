<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counselor_education', function (Blueprint $table) {
            // Document upload fields (same pattern as counselor_certificates)
            $table->string('document_path')->nullable()->after('notes');
            $table->string('document_name')->nullable()->after('document_path');
            $table->string('document_type')->nullable()->after('document_name');
            $table->unsignedBigInteger('document_size')->nullable()->after('document_type');

            // Year the qualification was obtained (replaces year_graduated as the primary "year" field)
            $table->year('year_obtained')->nullable()->after('year_graduated');
        });
    }

    public function down(): void
    {
        Schema::table('counselor_education', function (Blueprint $table) {
            $table->dropColumn(['document_path', 'document_name', 'document_type', 'document_size', 'year_obtained']);
        });
    }
};
