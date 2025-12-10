<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Simulation Mode
    |--------------------------------------------------------------------------
    | When enabled members can create simulated payments for their own ACTIVE
    | loans. Payments are first created with a pending status and can be
    | manually marked as confirmed or rejected (failed) by the member.
    | No real money movement occurs – this is strictly for demonstration.
    */
    'simulation_mode' => env('PAYMENTS_SIMULATION_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | Simulation Processing Delay (milliseconds)
    |--------------------------------------------------------------------------
    | Optional small delay used in the UI to mimic processing time before
    | allowing a member to confirm or reject a simulated payment.
    */
    'simulation_delay_ms' => env('PAYMENTS_SIMULATION_DELAY_MS', 1200),
];
