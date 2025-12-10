<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'sequence',
        'due_date',
        'amount',
        'principal_component',
        'interest_component',
        'status',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'confirmed_at' => 'datetime',
        'amount' => 'float',
        'principal_component' => 'float',
        'interest_component' => 'float',
    ];

    public const STATUS_PLANNED = 'planned';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PAID = 'paid';
    public const STATUS_SKIPPED = 'skipped';

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
