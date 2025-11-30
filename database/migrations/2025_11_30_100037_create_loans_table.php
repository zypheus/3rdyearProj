<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates loans table for loan applications.
     * Status workflow: pending → under_review → approved/rejected → active → completed/defaulted
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Applicant (member)
            $table->string('loan_type', 100); // personal, business, emergency, education
            $table->decimal('amount', 15, 2); // Requested amount
            $table->integer('term_months'); // Loan duration in months
            $table->decimal('interest_rate', 5, 2); // Annual interest rate %
            $table->text('purpose')->nullable(); // Purpose of loan
            $table->enum('status', [
                'pending',
                'under_review', 
                'approved',
                'rejected',
                'active',
                'completed',
                'defaulted'
            ])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null'); // Officer/Admin
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->decimal('approved_amount', 15, 2)->nullable(); // May differ from requested
            $table->date('disbursement_date')->nullable();
            $table->timestamps();
            
            // Indexes for common queries
            $table->index('status');
            $table->index('loan_type');
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
