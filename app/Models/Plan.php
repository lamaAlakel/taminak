<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'price',
        'form',
        'image',
    ];

    protected $casts = [
        'form' => 'array',
    ];

    // Relationships

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'plan_categories',
            'plan_id',
            'category_id'
        )->withTimestamps();
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function planRequests()
    {
        return $this->hasMany(PlanRequest::class);
    }
}
