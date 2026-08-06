<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'assigned_officer_id',
        'bvn', 'nin', 'address', 'date_of_birth', 'kyc_status',
        'staff_code', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token', 'current_session_token'];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    // ---- Role helpers ----
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isOfficer(): bool
    {
        return $this->role === 'officer';
    }

    public function isBorrower(): bool
    {
        return $this->role === 'borrower';
    }

    // ---- Relationships ----
    public function assignedOfficer()
    {
        return $this->belongsTo(User::class, 'assigned_officer_id');
    }

    public function borrowers() // officer -> their borrowers
    {
        return $this->hasMany(User::class, 'assigned_officer_id')->where('role', 'borrower');
    }

    public function loans() // as a borrower
    {
        return $this->hasMany(Loan::class, 'borrower_id');
    }

    public function managedLoans() // as an officer
    {
        return $this->hasMany(Loan::class, 'officer_id');
    }

    public function collectionsMade() // as an officer
    {
        return $this->hasMany(Collection::class, 'officer_id');
    }
}
