<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds fields to support Calamity Loan type based on Pag-IBIG guidelines:
     * - Grace period tracking
     * - Penalty rate configuration
     * - Eligible amount for 80% loanable calculation
     * - Penalty tracking in payment schedules and payments
     */
    public function up(): void
    {
        // Add calamity loan fields to loans table
        Schema::table('loans', function (Blueprint $table) {
            // Grace period in months (default 0 for regular loans, 2 for calamity)
            $table->unsignedTinyInteger('grace_period_months')->default(0)->after('term_months');
            
            // Penalty rate per day as percentage (e.g., 0.05 = 0.05% = 1/20 of 1%)
            $table->decimal('penalty_rate', 8, 4)->default(0)->after('interest_rate');
            
            // Eligible amount (base amount for calculating loanable amount)
            $table->decimal('eligible_amount', 15, 2)->nullable()->after('amount');
            
            // Loanable percentage (e.g., 80 = 80% of eligible amount)
            $table->decimal('loanable_percentage', 5, 2)->nullable()->after('eligible_amount');
            
            // Total penalties accumulated on this loan
            $table->decimal('total_penalties', 15, 2)->default(0)->after('total_paid');
        });

        // Add penalty fields to payment_schedules table
        Schema::table('payment_schedules', function (Blueprint $table) {
            // Penalty amount for late payment
            $table->decimal('penalty_amount', 12, 2)->default(0)->after('interest_component');
            
            // Days delayed past due date
            $table->unsignedInteger('days_delayed')->default(0)->after('penalty_amount');
        });

        // Add penalty fields to payments table
        Schema::table('payments', function (Blueprint $table) {
            // Penalty amount included in this payment
            $table->decimal('penalty_amount', 12, 2)->default(0)->after('interest_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'grace_period_months',
                'penalty_rate',
                'eligible_amount',
                'loanable_percentage',
                'total_penalties',
            ]);
        });

        Schema::table('payment_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'penalty_amount',
                'days_delayed',
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('penalty_amount');
        });
    }
};
