<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'percent',
        'image',
        'description',
    ];

    // Relationships
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
