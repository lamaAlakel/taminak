<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bio',
        'image',
        'contact_number',
        'location',
        'lat' ,
        'lng'
    ];

    // Relationships

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
    public function companies()
    {
        return $this->belongsToMany(
            Company::class,          // related model
            'contracts',                         // pivot table
            'service_provider_id',               // this model’s FK on pivot
            'company_id'                         // related model’s FK on pivot
        )->withTimestamps();
    }
}
