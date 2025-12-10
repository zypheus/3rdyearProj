<?php

/**
 * Loan Configuration
 * 
 * Configuration for different loan types and their parameters.
 * Calamity loan settings are based on Pag-IBIG guidelines (adapted without membership requirements).
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Loan Settings
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'min_amount' => 1000,
        'max_amount' => 1000000,
        'min_term_months' => 1,
        'max_term_months' => 60,
        'max_interest_rate' => 50,
        'regular_loan_interest_rate' => 10.5,  // Fixed interest rate for non-calamity loans (10.5% p.a.)
        'interest_calculation_method' => 'flat',  // 'flat' or 'diminishing' - Use flat rate for all loans
    ],

    /*
    |--------------------------------------------------------------------------
    | Calamity Loan Settings
    |--------------------------------------------------------------------------
    |
    | Based on Pag-IBIG Calamity Loan guidelines:
    | - Fixed interest rate: 5.95% per annum (FLAT RATE)
    | - Term options: 2 years (24 months) or 3 years (36 months)
    | - Grace period: 2 months before first payment
    | - Penalty: 1/20 of 1% per day of delay (0.05% per day)
    | - Loanable amount: 80% of eligible amount
    | - Payment priority: Penalties → Interest → Principal
    | - Interest calculation: Flat rate (interest computed on original principal for entire term)
    |
    */
    'calamity' => [
        // Fixed interest rate for calamity loans (5.95% per annum)
        'interest_rate' => 5.95,
        
        // Interest calculation method: 'flat' or 'diminishing'
        'interest_calculation_method' => 'flat',

        // Available term options in months (2 or 3 years only)
        'term_options' => [24, 36],

        // Grace period before first payment is due (in months)
        'grace_period_months' => 2,

        // Daily penalty rate as percentage (1/20 of 1% = 0.05%)
        'penalty_rate_per_day' => 0.05,

        // Loanable percentage of eligible amount
        'loanable_percentage' => 80,

        // Minimum eligible amount
        'min_eligible_amount' => 5000,

        // Maximum eligible amount
        'max_eligible_amount' => 500000,

        // Minimum loanable amount (after 80% calculation)
        'min_loanable_amount' => 4000,

        // Payment allocation priority
        'payment_priority' => [
            'penalties',
            'interest',
            'principal',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Loan Type Labels
    |--------------------------------------------------------------------------
    */
    'type_labels' => [
        'personal' => 'Personal Loan',
        'business' => 'Business Loan',
        'emergency' => 'Emergency Loan',
        'education' => 'Education Loan',
        'calamity' => 'Calamity Loan',
    ],

    /*
    |--------------------------------------------------------------------------
    | Loan Type Descriptions
    |--------------------------------------------------------------------------
    */
    'type_descriptions' => [
        'personal' => 'General purpose personal loans for individual needs.',
        'business' => 'Loans for business capital and expansion.',
        'emergency' => 'Quick loans for urgent financial needs.',
        'education' => 'Loans for educational expenses and tuition.',
        'calamity' => 'Special assistance loan for members affected by natural disasters or calamities. Features a 2-month grace period and fixed 5.95% annual interest rate.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Labels
    |--------------------------------------------------------------------------
    */
    'status_labels' => [
        'pending' => 'Pending',
        'under_review' => 'Under Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'active' => 'Active',
        'completed' => 'Completed',
        'defaulted' => 'Defaulted',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Colors (Tailwind CSS classes)
    |--------------------------------------------------------------------------
    */
    'status_colors' => [
        'pending' => 'yellow',
        'under_review' => 'blue',
        'approved' => 'green',
        'rejected' => 'red',
        'active' => 'emerald',
        'completed' => 'gray',
        'defaulted' => 'red',
    ],
];
