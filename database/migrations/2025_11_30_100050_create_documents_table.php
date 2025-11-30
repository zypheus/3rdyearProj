<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates documents table for loan application attachments.
     * Documents can be verified by officers/admins.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Uploader
            $table->string('document_type', 100); // id, income_proof, bank_statement, etc.
            $table->string('filename'); // Original filename
            $table->string('file_path'); // Storage path
            $table->unsignedBigInteger('file_size'); // Size in bytes
            $table->string('mime_type', 100);
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable(); // Verification notes
            $table->timestamps();
            
            // Indexes for common queries
            $table->index('document_type');
            $table->index('is_verified');
            $table->index(['loan_id', 'document_type']);
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
