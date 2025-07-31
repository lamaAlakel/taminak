<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Company extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'address',
        'license_number',
        'image',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $appends = ['average_rate', 'user_rate'];
    // Relationships
    public function getAverageRateAttribute()
    {
        return $this->rates()->avg('rate')
            ? round($this->rates()->avg('rate'), 2)
            : null;
    }

    public function getUserRateAttribute()
    {
        $user = auth()->user();
        if (! $user instanceof User) {
            return null;
        }
        $rate = $this->rates()
            ->where('user_id', $user->id)
            ->first(['id', 'rate']);
        return $rate
            ? ['id' => $rate->id, 'rate' => $rate->rate]
            : null;
    }

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }

    public function serviceProviders()
    {
        return $this->belongsToMany(
            ServiceProvider::class,  // related model
            'contracts',                         // pivot table
            'company_id',                        // this model’s FK on pivot
            'service_provider_id'                // related model’s FK on pivot
        )->withTimestamps();
    }
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
