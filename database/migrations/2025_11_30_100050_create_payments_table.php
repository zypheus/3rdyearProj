<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates payments table for loan repayments.
     * Payments are recorded by officers/admins.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Payer (member)
            $table->decimal('amount', 15, 2); // Total payment amount
            $table->decimal('principal_amount', 15, 2)->default(0); // Applied to principal
            $table->decimal('interest_amount', 15, 2)->default(0); // Applied to interest
            $table->date('payment_date');
            $table->string('payment_method', 50); // cash, bank_transfer, check, online
            $table->string('reference_number')->nullable(); // Transaction reference
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null'); // Officer/Admin
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for common queries
            $table->index('status');
            $table->index('payment_date');
            $table->index('payment_method');
            $table->index(['loan_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
