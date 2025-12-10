<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disbursement extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'amount',
        'method',
        'reference_number',
        'disbursed_by',
        'disbursed_at',
        'notes',
    ];

    protected $casts = [
        'disbursed_at' => 'datetime',
        'amount' => 'float',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
