<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

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
    ];

    // Relationships

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
}
