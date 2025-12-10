<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Loan;
use App\Models\Disbursement;
use App\Models\PaymentSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ActiveLoanUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a member user (or use existing)
        $email = 'juan.delacruz@example.com';
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'Juan Dela Cruz',
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'member',
            ]);
            $this->command->info('✓ Created new user: ' . $user->email);
        } else {
            $this->command->info('✓ Using existing user: ' . $user->email);
        }

        // Create an approved loan (6 months ago)
        $loanCreatedAt = now()->subMonths(6)->subDays(15);
        $loan = Loan::create([
            'user_id' => $user->id,
            'loan_type' => 'personal',
            'amount' => 10000.00,
            'approved_amount' => 10000.00,
            'term_months' => 12,
            'interest_rate' => 12.0, // 12% annual
            'purpose' => 'Personal expenses and home improvement',
            'status' => 'active',
            'reviewed_by' => 1, // Assuming admin user with ID 1 exists
            'reviewed_at' => $loanCreatedAt->copy()->addDays(2),
            'disbursement_date' => $loanCreatedAt->copy()->addDays(3),
            'outstanding_balance' => 10000.00,
            'total_paid' => 0,
            'created_at' => $loanCreatedAt,
            'updated_at' => now(),
        ]);

        // Create disbursement record
        Disbursement::create([
            'loan_id' => $loan->id,
            'amount' => 10000.00,
            'method' => 'bank_transfer',
            'reference_number' => 'BT-' . strtoupper(substr(md5(uniqid()), 0, 10)),
            'disbursed_by' => 1,
            'disbursed_at' => $loanCreatedAt->copy()->addDays(3),
            'notes' => 'Initial loan disbursement via bank transfer',
            'created_at' => $loanCreatedAt->copy()->addDays(3),
        ]);

        // Generate payment schedule (12 monthly payments)
        $principal = 10000.00;
        $termMonths = 12;
        $annualRate = 12.0;
        $monthlyRate = $annualRate / 100 / 12; // 0.01 (1% per month)

        // Calculate monthly payment using amortization formula
        $monthlyPayment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / 
                          (pow(1 + $monthlyRate, $termMonths) - 1);
        
        $balance = $principal;
        $disbursementDate = $loanCreatedAt->copy()->addDays(3);

        for ($seq = 1; $seq <= $termMonths; $seq++) {
            $interestPayment = $balance * $monthlyRate;
            $principalPayment = $monthlyPayment - $interestPayment;
            $balance -= $principalPayment;

            $dueDate = $disbursementDate->copy()->addMonths($seq);
            
            // First 6 months are due (some might be overdue)
            $status = $seq <= 6 ? 'confirmed' : 'confirmed';

            PaymentSchedule::create([
                'loan_id' => $loan->id,
                'sequence' => $seq,
                'due_date' => $dueDate->toDateString(),
                'amount' => round($monthlyPayment, 2),
                'principal_component' => round($principalPayment, 2),
                'interest_component' => round($interestPayment, 2),
                'status' => $status,
                'confirmed_by' => 1,
                'confirmed_at' => $disbursementDate->copy()->addDays(1),
                'created_at' => $disbursementDate->copy()->addDays(1),
            ]);
        }

        $this->command->info('✓ Created user: ' . $user->email);
        $this->command->info('✓ Created active loan: ₱10,000 (Loan #' . $loan->id . ')');
        $this->command->info('✓ Loan started: ' . $loanCreatedAt->format('F d, Y'));
        $this->command->info('✓ Monthly payment: ₱' . number_format($monthlyPayment, 2));
        $this->command->info('✓ Created 12 payment schedule entries');
        $this->command->info('✓ Login credentials: juan.delacruz@example.com / password');
    }
}
