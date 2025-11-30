<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Creates test users for each of the 3 roles:
     * - Admin: Full system access
     * - Officer: Loan processing and verification
     * - Member: Loan application and tracking
     * 
     * CRITICAL: Only these 3 roles exist in the system
     */
    public function run(): void
    {
        // Create Admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@loanease.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        // Create Officer user
        User::factory()->create([
            'name' => 'Officer User',
            'email' => 'officer@loanease.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_OFFICER,
        ]);

        // Create Member user
        User::factory()->create([
            'name' => 'Member User',
            'email' => 'member@loanease.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_MEMBER,
        ]);

        // Create additional test members
        User::factory(5)->create([
            'role' => User::ROLE_MEMBER,
        ]);

        $this->command->info('✅ Created test users:');
        $this->command->info('   Admin:   admin@loanease.test / password');
        $this->command->info('   Officer: officer@loanease.test / password');
        $this->command->info('   Member:  member@loanease.test / password');
    }
}