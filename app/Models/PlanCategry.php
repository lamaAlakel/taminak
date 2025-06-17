<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanCategry extends Model
{
    use HasFactory;
    protected $fillable=[
        'plan_id',
        'category_id'
    ];
}
