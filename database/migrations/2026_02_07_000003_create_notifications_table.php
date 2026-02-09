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
    Schema::create('notifications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
        $table->enum('type', ['success', 'error', 'info']);
        $table->string('recipient_email', 255);
        $table->string('subject', 255);
        $table->text('body');
        $table->timestamp('sent_at')->nullable();
        $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
        $table->text('error_message')->nullable();
        $table->timestamp('created_at')->useCurrent();

        $table->index('status');
        $table->index('sent_at');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
