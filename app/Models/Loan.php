<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_product_id',
        'borrower_id',
        'officer_id',
        'amount',
        'interest_amount',
        'total_payable',
        'amount_repaid',
        'purpose',
        'status',
        'approved_by',
        'approved_at',
        'disbursed_at',
        'due_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'interest_amount' => 'decimal:2',
            'total_payable' => 'decimal:2',
            'amount_repaid' => 'decimal:2',
            'approved_at' => 'datetime',
            'disbursed_at' => 'datetime',
            'due_at' => 'datetime',
        ];
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isOwner()) {
            return $query;
        }

        if ($user->isOfficer()) {
            return $query->where('officer_id', $user->id);
        }

        return $query->where('borrower_id', $user->id);
    }

    public function product()
    {
        return $this->belongsTo(LoanProduct::class, 'loan_product_id');
    }

    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }
}
