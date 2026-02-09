<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('documents', function (Blueprint $table) {
        $table->id();
        $table->string('filing_number', 50)->unique();
        $table->enum('document_type', ['contractor_invoice', 'supplier_invoice', 'general_invoice']);
        $table->enum('status', ['pending', 'processing', 'validated', 'rejected', 'approved', 'paid'])->default('pending');
        
        $table->string('original_filename', 255);
        $table->string('file_path', 500);
        $table->unsignedInteger('file_size')->nullable();
        $table->string('mime_type', 100)->nullable();
        
        $table->json('extracted_data')->nullable();
        $table->json('validation_errors')->nullable();
        $table->json('metadata')->nullable();
        $table->string('email_recipient', 255)->nullable();
        
        $table->timestamp('filed_at')->nullable();
        $table->timestamp('processed_at')->nullable();
        $table->timestamp('validated_at')->nullable();
        $table->timestamps();
        $table->softDeletes();

        $table->index('filing_number');
        $table->index('status');
        $table->index('document_type');
        $table->index('filed_at');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
