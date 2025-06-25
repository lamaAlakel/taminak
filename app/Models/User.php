<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationships

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }

    public function planRequest()
    {
        return $this->hasMany(PlanRequest::class);
    }
}
