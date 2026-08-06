<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'minimum_amount',
        'maximum_amount',
        'interest_rate',
        'tenure_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'minimum_amount' => 'decimal:2',
            'maximum_amount' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
