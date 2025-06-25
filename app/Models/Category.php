<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
    ];

    // Relationships

    public function plans()
    {
        return $this->belongsToMany(
            Plan::class,
            'plan_categories',
            'category_id',
            'plan_id'
        )->withTimestamps();
    }
}
