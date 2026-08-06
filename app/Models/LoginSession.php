<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginSession extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'token',
        'ip_address',
        'user_agent',
        'logged_in_at',
    ];

    protected function casts(): array
    {
        return [
            'logged_in_at' => 'datetime',
        ];
    }
}
